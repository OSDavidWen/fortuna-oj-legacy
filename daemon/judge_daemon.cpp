#include <unistd.h>
#include <sys/stat.h>
#include <sys/wait.h>
#include <stdlib.h>
#include <stdio.h>
#include <syslog.h>
#include <signal.h>
#include <mysql/mysql.h>
#include <algorithm>
#include <fstream>
#include <json/json.h>

using namespace std;

const int SETTINGS_COUNT = 6, MAX_RUNNING = 16;
MYSQL db;
int rows_count, running, max_running, id;
string settings[SETTINGS_COUNT];
struct judgement{
	int no;
	string runPath;
} info[MAX_RUNNING];

int daemon_init(void){ 
	pid_t pid; 
	if ((pid = fork()) < 0) return -1; 
	else if (pid != 0) exit(0);
        
	setsid();
	chdir("/");
	umask(0);
	//close(0);
	//close(1);
	//close(2);
	return 0; 
}

void sigterm_handler(int signo){ 
	if (signo == SIGTERM){
		mysql_close(&db);
		syslog(LOG_INFO, "Judge daemon terminated."); 
		closelog(); 
		exit(0); 
	} 
}

void sigchld_handler(int signo){
	if (signo == SIGCHLD){
		while (waitpid(-1, NULL, WNOHANG) > 0) running--;
	}
}

void init(){
        if(daemon_init() == -1){ 
                printf("can't fork self.\n"); 
                exit(0); 
        } 
        openlog("judge_daemon", LOG_PID, LOG_USER); 
        syslog(LOG_INFO, "Judge daemon started."); 
        signal(SIGTERM, sigterm_handler);
        signal(SIGCHLD, sigchld_handler);
        
        ifstream fin("/etc/foj_judged.conf");
        int cnt = 0;
        while (cnt < SETTINGS_COUNT){
                getline(fin, settings[cnt]);
                if (!settings[cnt].empty()){
                        settings[cnt].erase(0, settings[cnt].find_first_not_of(" "));
                        settings[cnt].erase(settings[cnt].find_last_not_of(" ") + 1);
                }
                if (settings[cnt][0] != '#') cnt++;
        }
        fin.close();
		sscanf(settings[5].c_str(), "%d", &max_running);
		if (max_running > MAX_RUNNING) max_running = MAX_RUNNING;
        
        mysql_init(&db);
        if (!mysql_real_connect(&db, settings[0].c_str(), settings[1].c_str(), settings[2].c_str(), settings[3].c_str(), 0, NULL, 0)){
			syslog(LOG_ERR, "Failed to connec to database!");
			exit(1);
        }
        
        srand(time(0));
        running = 0;
}

bool run(){
        string query = "SELECT sid FROM Submission WHERE status=-1 LIMIT 0,1;";
        if (mysql_query(&db, query.c_str())){
                syslog(LOG_ERR, "SQL Query Failed!");
                return false;
        }
        MYSQL_RES *result = mysql_store_result(&db);
        rows_count = mysql_num_rows(result);
        if (rows_count == 0){
			mysql_free_result(result);
			return false;
		}
        
		if (running < max_running){
			MYSQL_ROW row = mysql_fetch_row(result);
			int sid = atoi(row[0]), run = rand();
			query = string("UPDATE Submission SET status=-2 WHERE sid=") + row[0];
			mysql_free_result(result);
			if (mysql_query(&db, query.c_str())) return false;	
			
			int pid = fork();
			if (pid < 0){
				syslog(LOG_ERR, "Unable to fork!");
				return false;
			}
			running++; id++;
			if (pid == 0){
				
				char command[255];
				sprintf(command, "mkdir /tmp/foj/%d/%d -p > /dev/null", id, run);
				system(command);
				sprintf(command, "judge_runner %d /tmp/foj/%d/%d/ > /dev/null", sid, id, run);
				system(command);
				sprintf(command, "rm -f -R /tmp/foj/%d > /dev/null", id, run);
				system(command);
				exit(0);
			} else syslog(LOG_INFO, "Run judge_runner once.");
		}else{
			mysql_free_result(result);
			return false;
		}

        return true;
}

int main(void){
        init();
        
        while (true){
                if (!run()) sleep(1);
        }

        return 0;
}  
