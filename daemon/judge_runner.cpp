#include <cstdio>
#include <cstdlib>
#include <cstring>
#include <cmath>
#include <unistd.h>
#include <fstream>
#include <sstream>
#include <vector>
#include <map>
#include <sys/stat.h>
#include <errno.h>
#include <json/json.h>
#include <mysql/mysql.h>

using namespace std;

const int bufferSize = 256, SETTINGS_COUNT = 5;
char *sidStr, *runPath, dataPath[bufferSize];
int sid, pid, uid, JudgeType, Rtime, Rmemory, Rstatus;
MYSQL db;
MYSQL_RES *sqlResult;
string settings[SETTINGS_COUNT], language;
bool judgeStatus;

struct test{
        string input, output, userInput, userOutput;
        int timeLimit, memoryLimit;
};
struct testcase{
        double score;
        vector<test> tests;
};
struct problemset{
        string spjFile;
        map<string, string> compiler;
        vector<testcase> cases;
        int spjMode, IOMode;
} problem;
Json::Value result;
double Rscore;

void init(){
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
        
        mysql_init(&db);
        my_bool auto_reconnect = 1;
        mysql_options(&db, MYSQL_OPT_RECONNECT, &auto_reconnect);
        if (!mysql_real_connect(&db, settings[0].c_str(), settings[1].c_str(), settings[2].c_str(), settings[3].c_str(), 0, NULL, 0)){
                printf("Failed to connec to database!");
                exit(1);
        }
        
        string query("SELECT pid, code, language, score, status, uid FROM Submission WHERE sid=");
        query += sidStr;
        mysql_query(&db, query.c_str());
        sqlResult = mysql_store_result(&db);
    
        query = string("rm -f -R ") + runPath + "/* >/dev/null";
        system(query.c_str());
		query = string("mkdir -p ") + runPath + " >/dev/null";
		system(query.c_str());
        
        MYSQL_ROW row = mysql_fetch_row(sqlResult);
        
        char solved;
        if (row[4][0] == '0') solved = '1'; else solved = '0';
        query = string("UPDATE ProblemSet SET scoreSum=scoreSum-") + row[3] + ",solvedCount=solvedCount-" + solved + " WHERE pid=" + row[0];
        mysql_query(&db, query.c_str());
        query = string("UPDATE User SET solvedCount=solvedCount-") + solved + " WHERE uid=" + row[5];
        mysql_query(&db, query.c_str());
        query = string("UPDATE Submission SET score=0, status=-2 time=0, memory=0, judgeResult='' WHERE sid=") + sidStr;
        mysql_query(&db, query.c_str());
        
        chdir(runPath);
        ofstream fout;
        uid = atoi(row[5]);
        pid = atoi(row[0]);
        char pidStr[255];
        strcpy(pidStr, row[0]);
        if (strcmp(row[2], "C") == 0) fout.open("Main.c");
        else if (strcmp(row[2], "C++") == 0) fout.open("Main.cpp");
        else if (strcmp(row[2], "C++11") == 0) fout.open("Main.cpp");		
        else if (strcmp(row[2], "Pascal") == 0) fout.open("Main.pas");
        else if (strcmp(row[2], "Java") == 0) fout.open("Main.java");
        else if (strcmp(row[2], "Python") == 0) fout.open("Main.py");
		language = row[2];
        string program = row[1];
        if (sqlResult != NULL) mysql_free_result(sqlResult);
        
        strcpy(dataPath, (settings[4] + "/" + pidStr + "/").c_str());
        Json::Value root;
        Json::Reader reader;

		query = string("SELECT dataConfiguration FROM ProblemSet WHERE pid=") + pidStr;
        mysql_query(&db, query.c_str());
        sqlResult = mysql_store_result(&db);
		row = mysql_fetch_row(sqlResult);
		string config(row[0]);
        
        if (!reader.parse(config, root)){
                printf("Error parsing data configuration!\n");
                judgeStatus = true;
                return;
        }
        problem.IOMode = root.get("IOMode", 1).asInt();
        problem.spjMode = root.get("spjMode", -1).asInt();
        problem.compiler["C"] = root.get("compiler_C", "").asString();
        problem.compiler["C++"] = root.get("compiler_C++", "").asString();
		problem.compiler["C++11"] = root.get("compiler_C++11", "").asString();
        problem.compiler["Pascal"] = root.get("compiler_Pascal", "").asString();
		problem.compiler["Java"] = root.get("compiler_Java", "").asString();
		problem.compiler["Python"] = root.get("compiler_Python", "").asString();
        if (problem.spjMode >= 0) problem.spjFile = root.get("spjFile", "").asString();
        const Json::Value cases = root["cases"];
        for (int i = 0; i < (int)cases.size(); i++){
                testcase currentCase;
                currentCase.score = cases[i].get("score", 0).asDouble();
                
                const Json::Value tests = cases[i]["tests"];
                for (int j = 0; j < (int)tests.size(); j++){
                        test currentTest;
                        
                        currentTest.input = tests[j].get("input", "").asString();
                        currentTest.output = tests[j].get("output", "").asString();
                        currentTest.userInput = tests[j].get("userInput", "").asString();
                        currentTest.userOutput = tests[j].get("userOutput", "").asString();
                        currentTest.timeLimit = tests[j].get("timeLimit", 1000).asInt();
                        currentTest.memoryLimit = tests[j].get("memoryLimit", 262144).asInt();
                        
                        currentCase.tests.push_back(currentTest);
                }
                
                problem.cases.push_back(currentCase);
        }
        
        if (problem.IOMode == 3){
			program = root.get("framework", "").asString() + program;
			problem.IOMode = 0;
		}
        
        fout << program;
		fout.close();
}

void runTest(){
        //initialize
        printf("Testing %s\n", sidStr);
        char command[bufferSize];

        result["compileMessage"] = "";

        //compile
        char msg[bufferSize];
        chdir(runPath);
        if (language == "C")
                sprintf(command, "gcc Main.c %s -o %s/Main 2>&1", problem.compiler["C"].c_str(), runPath);
        
        else if (language == "C++")
                sprintf(command, "g++ Main.cpp %s -O2 -o %s/Main 2>&1", problem.compiler["C++"].c_str(), runPath);
		
        else if (language == "C++11")
                sprintf(command, "g++ Main.cpp %s -O2 -std=c++11 -o %s/Main 2>&1", problem.compiler["C++"].c_str(), runPath);
        
        else if (language == "Pascal")
                sprintf(command, "fpc Main.pas %s -O2 -Co -Ci -o%s/Main 2>&1", problem.compiler["Pascal"].c_str(), runPath);
        
        FILE *ret = popen(command, "r");
        while (fscanf(ret, "%[^\n]\n", msg) > 0) result["compileMessage"] = result["compileMessage"].asString() + msg + '\n';
        
        if (problem.spjMode >= 0){
                sprintf(command, "cp -f %s/%s %s/", dataPath, problem.spjFile.c_str(), runPath);
                system(command);
        }
                         
        if (pclose(ret) > 0){
                result["compileStatus"] = false;
                Rtime = Rmemory = 0;
                Rstatus = 8;
                return;
        }else result["compileStatus"] = true;

        //run
        for (int id = 0; id < (int)problem.cases.size(); id++){
                testcase &C = problem.cases[id];
                bool zero = false;
                Json::Value rcase;
                for (int i = 0; i < (int)C.tests.size(); i++){
                        Json::Value rtest;
                        #ifdef DEBUG
                                printf("Testing case %d, test %d\n", id, i);
                        #endif                  
                        sprintf(command, "cp -f %s/%s %s/%s", dataPath, C.tests[i].input.c_str(), runPath, C.tests[i].userInput.c_str());
                        system(command);
                        sprintf(command, "cp -f %s/%s %s/", dataPath, C.tests[i].output.c_str(), runPath);
                        system(command);
                        sprintf(command, "cp -f /usr/bin/judge_core %s", runPath);
                        system(command);
                        if (problem.spjMode < 0){
                                sprintf(command, "./judge_core %d ./Main %s %s %s %d %d %lf",
                                                problem.IOMode, C.tests[i].userInput.c_str(), C.tests[i].output.c_str(),
                                                C.tests[i].userOutput.c_str(), C.tests[i].timeLimit, C.tests[i].memoryLimit,
                                                C.score);
                        }else{
                                sprintf(command, "./judge_core %d ./Main %s %s %s %d %d %lf %s %d",
                                                problem.IOMode, C.tests[i].userInput.c_str(), C.tests[i].output.c_str(),
                                                C.tests[i].userOutput.c_str(), C.tests[i].timeLimit, C.tests[i].memoryLimit,
                                                C.score, problem.spjFile.c_str(), problem.spjMode);                             
                        }
                        #ifdef DEBUG
                                printf("%s\n", command);
                        #endif

                        system(command);
                        
                        ret = fopen("test.log", "r");
                        int status, time, memory; double score; char msg[4096];
                        fscanf(ret, "%d %lf %d %d\n%s", &status, &score, &time, &memory, msg);
                        if (fabs(score) < 1e-6) zero = true; else rcase["score"] = rcase["score"].asDouble() + score;
                        rtest["status"] = status;
                        rtest["time"] = time;
                        rtest["memory"] = memory;
                        rcase["tests"].append(rtest);
                        rcase["message"] = msg;
                        
                        Rtime += time; Rmemory = max(Rmemory, memory);
                        if (status > Rstatus) Rstatus = status;
                        fclose(ret);
                        
                        sprintf(command, "rm %s", C.tests[i].userOutput.c_str());
                        system(command);
                }
                if (zero) rcase["score"] = 0; else rcase["score"] = rcase["score"].asDouble() / C.tests.size();
                Rscore += rcase["score"].asDouble();
        #ifdef DEBUG
                printf("%.2lf\n", rcase["score"].asDouble());
        #endif
                result["cases"].append(rcase);
        }
        
        //clean
#ifndef DEBUG
        sprintf(command, "rm -R %s/* >/dev/null", runPath);
        system(command);
#endif
}

char cmsg[65536], rmsg[65536];

void writeResult(){
        stringstream sout;
        
        int solved = 0;
        if (Rstatus == 0) solved = 1;
        sout << "UPDATE ProblemSet SET scoreSum=scoreSum+" << Rscore << ", solvedCount=solvedCount+" << solved << " WHERE pid=" << pid;
        //cout << sout.str() << endl;
        mysql_ping(&db);
        mysql_query(&db, sout.str().c_str());
        
        sout.str(""); sout.clear();
        mysql_ping(&db);
        sout << "UPDATE User SET solvedCount=solvedCount+" << solved << " WHERE uid=" << uid;
        //cout << sout.str() << endl;
        mysql_query(&db, sout.str().c_str());
        
        sout.str(""); sout.clear();
        if (judgeStatus){
                sout << "UPDATE Submission SET status=9, score=0, time=0, memory=0, score=0" << " WHERE sid=" << sid;
        }else{
                std::string strtmp = result["compileMessage"].asString();
                int cnt = mysql_real_escape_string(&db, cmsg, strtmp.c_str(), strtmp.length());
                cmsg[cnt] = '\0';
                strtmp = result.toStyledString();
				while (strtmp.find("\342\200\231") != -1) strtmp.replace(strtmp.find("\342\200\231"), 3, "\'");
				while (strtmp.find("\342\200\230") != -1) strtmp.replace(strtmp.find("\342\200\230"), 3, "\'");
                cnt = mysql_real_escape_string(&db, rmsg, strtmp.c_str(), strtmp.length());
                rmsg[cnt] = '\0';
                sout << "UPDATE Submission SET status=" << Rstatus << ", score=" << Rscore << ", time=" << Rtime << ", memory=" << Rmemory
                         << ", judgeResult='" << rmsg << "' WHERE sid=" << sid << ";";
        }

        //cout << sout.str() << endl;
        mysql_ping(&db);
        mysql_query(&db, sout.str().c_str());
        
        if (solved){
                sout.str(""); sout.clear();
                sout << "SELECT COUNT(DISTINCT pid) FROM Submission WHERE status=0 AND uid=" << uid;
                //cout << sout.str() << endl;
                mysql_ping(&db);
                mysql_query(&db, sout.str().c_str());
                sqlResult = mysql_store_result(&db);    
                MYSQL_ROW row = mysql_fetch_row(sqlResult);     
                int cnt = atoi(row[0]);
                mysql_free_result(sqlResult);
                
                sout.str(""); sout.clear();
                sout << "UPDATE User SET acCount=" << cnt << " WHERE uid=" << uid;
                //cout << sout.str() << endl;
                mysql_ping(&db);
                mysql_query(&db, sout.str().c_str());
        }

        mysql_close(&db);
}

int main(int argc, char* argv[]){
        /*
                argv[1] is submission id.
                argv[2] is run path
                argv[3] (if exist) is judge type
        */
        if (argc < 3){
                printf("Usage: judge_runner submission_id run_path [special judge type]\n");
                return 1;
        }
        sidStr = argv[1]; runPath = argv[2];
        sid = atoi(argv[1]);
        if (argc >= 4) JudgeType = atoi(argv[3]);

        init();
        if (!judgeStatus) runTest();
        writeResult();

        return 0;
} 
