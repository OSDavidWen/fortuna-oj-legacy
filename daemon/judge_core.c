#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/ptrace.h>
#include <sys/types.h>
#include <sys/wait.h>
#include <fcntl.h>
#include <unistd.h>
#include <sys/syscall.h>
#include <sys/reg.h>
#include <sys/time.h>
#include <time.h>
#include <sys/resource.h>
#include "judge_core.h"

//result:
// 0 for AC, 1 for PE, 2 for WA, 3 for Checker Error, 4 for OLE, 5 for MLE, 6 for TLE, 7 for RE

int type, forbidden[500], terminate;
double full_score;

void result(int status, double score, int time, int mem, char* msg){
	FILE *log = fopen("test.log", "w");
	fprintf(log, "%d %.2lf %d %d\n%s\n", status, score, time, mem, msg);
	fclose(log);
	printf("%d %.2lf %d %d\n%s\n\n", status, score, time, mem, msg);
	terminate = 1;
}

char buffer[2][65536];
int builtin_comp(char* file_std, char* file_user){
	FILE *std = fopen(file_std, "r"), *user = fopen(file_user, "r");
	int count = 0;
	while ( ! feof(std)){
		count++;
		int len0, len1;
		if (fgets(buffer[0], 65535, std) == NULL) len0 = 0; else len0 = strlen(buffer[0]);
		if (fgets(buffer[1], 65535, user) == NULL) len1 = 0; else len1 = strlen(buffer[1]);
		
		while (len0 && (buffer[0][len0 - 1] == ' ' || buffer[0][len0 - 1] == '\n' || buffer[0][len0 - 1] == '\r')) len0--;
		buffer[0][len0] = '\0';
		while (len1 && (buffer[1][len1 - 1] == ' ' || buffer[1][len1 - 1] == '\n' || buffer[1][len1 - 1] == '\r')) len1--;
		buffer[1][len1] = '\0';
		
		if (feof(std) && feof(user) && ! len0 && ! len1) break;
		
		if (strcmp(buffer[0], buffer[1]) != 0){
			printf("> %s\n< %s\n", buffer[0], buffer[1]);
			char exec_diff[256];
			sprintf(exec_diff, "diff --strip-trailing-cr -Z -B -w %s %s >/dev/null 2>&1\n", file_std, file_user);
			int status = system(exec_diff) >> 8;
			
			fclose(std); fclose(user);
			if (status > 0 || (count == 1 && !len1)) return 1;
			else return -1;
		}
	}
	
	fclose(std); fclose(user);
	return 0;
}

int main(int argc, char* argv[]){
	//argv[1] means test mode. 1 for file input/output, 0 for standard input/output, 2 for output submit task
	//argv[2] is program, argv[3] is input, argv[4] is standard output, argv[5] is user output
	//argv[6] is Time Limit, argv[7] is Memory Limit, argv[8] is full score
	//argv[9] is spj file(if exist) argv[10] is spj mode : 0 for default, 1 for cena, 2 for tsinsen, 3 for hustOJ
	type = atoi(argv[1]);
	sscanf(argv[8], "%lf", &full_score);
	int time_limit = atoi(argv[6]), mem_limit = atoi(argv[7]);
	int spj = (argc >= 10), spj_mode = (argc >= 11) ? atoi(argv[10]) : 0;
      
	int i;
	for (i = 0; i < syscall_forbidden_table_cnt; i++)
		forbidden[syscall_forbidden_table[i]] = 1;
	
	if (type <= 1){
		int time_usage = 0, mem_usage = 0;
		int pid = fork();
		
		if (pid < 0) return 1;
					
		if (pid == 0){
			ptrace(PTRACE_TRACEME, 0, NULL, NULL);
                      
			//set resources limit
			struct rlimit limit;
			limit.rlim_cur = (time_limit - 1) / 1000 + 2;
			limit.rlim_max = time_limit / 1000 + 2;
			alarm(limit.rlim_cur * 4);
			setrlimit(RLIMIT_CPU, &limit);
			limit.rlim_cur = limit.rlim_max = 104857600;
			setrlimit(RLIMIT_FSIZE, &limit);
                        
		#ifdef DEBUG
			printf("Start Judging!\n");
		#endif
			//redirect file describer to standard input/output
			if (type == 0){
				int fr = open(argv[3], O_RDONLY),
				fw = open(argv[5], (O_WRONLY | O_CREAT), 0666),
				fe = open("/dev/null", (O_WRONLY | O_CREAT), 0666);
				dup2(fr, 0); dup2(fw, 1); dup2(fe, 2);
				close(fr); close(fw);
			}
			execl(argv[2], argv[2], NULL);
		}else{
				
			//run program
			int runstat, first = 0;
			struct rusage rinfo;
			for (;;){
				wait4(pid, &runstat, WUNTRACED, &rinfo);
		
				//get memory usage
				if (!first){
					char cat_statm[255]; int tmp;
					sprintf(cat_statm, "cat /proc/%d/statm", pid);
					FILE *statm = popen(cat_statm, "r");
					fscanf(statm, "%d%d%d%d%d%d", &tmp, &tmp, &tmp, &tmp, &tmp, &tmp);
					if (tmp * 4 > mem_usage) mem_usage = tmp * 4;
					pclose(statm);
					first = 1;
				}
				if (rinfo.ru_maxrss > mem_usage) mem_usage = rinfo.ru_maxrss;
										
				if (mem_usage > mem_limit){
					result(5, 0, 0, mem_usage, "MLE");
					break;
				}
		
				//get time usage
				time_usage = (int)(rinfo.ru_utime.tv_sec * 1000 + rinfo.ru_utime.tv_usec / 1000);
				if (time_usage > time_limit){
					ptrace(PTRACE_KILL, pid, NULL, NULL);
					result(6, 0, time_usage, 0, "Time Limit Exceeded");
					break;                                  
				}
		
				if (WIFEXITED(runstat)){
					if (WEXITSTATUS(runstat) != 0) result(7, 0, time_usage, mem_usage, "Runtime Error!");
					break;
				}else if (WIFSIGNALED(runstat)){
					if (WTERMSIG(runstat) == SIGKILL) result(5, 0, time_usage, mem_usage, "SIGKILL");
					if (WTERMSIG(runstat) == SIGSEGV){
						if (mem_usage > mem_limit) result(5, 0, time_usage, mem_usage, "SIGSEGV");
						else result(7, 0, time_usage, 0, "SIGSEGV");
					}
					if (WTERMSIG(runstat) == SIGXFSZ) result(4, 0, 0, mem_usage, "SIGXFSZ");

					ptrace(PTRACE_KILL, pid, NULL, NULL);
					break;
				}else if (WIFSTOPPED(runstat)){
					//catch system call
					int signal = WSTOPSIG(runstat), syscall;
					if (signal == SIGTRAP){
						//get system call id
						syscall = ptrace(PTRACE_PEEKUSER, pid, 4 * ORIG_EAX, NULL);
						if (syscall == 252){
							char cat_statm[255]; int tmp;
							sprintf(cat_statm, "cat /proc/%d/statm", pid);
							FILE *statm = popen(cat_statm, "r");
							fscanf(statm, "%d%d%d%d%d%d", &tmp, &tmp, &tmp, &tmp, &tmp, &tmp);
							if (tmp * 4 > mem_usage) mem_usage = tmp * 4;
							pclose(statm);
							first = 1;
							break;
						}
						if (forbidden[syscall]){
							ptrace(PTRACE_KILL, pid, NULL, NULL);
							result(7, 0, time_usage, 0, "Runtime Error!");
							break;
						}
					} else {
						switch (WEXITSTATUS(runstat)){
							case SIGXCPU:
								result(6, 0, time_usage, 0, "SIGXCPU_EXIT");
								break;
							case SIGALRM:
								result(6, 0, time_usage, 0, "SIGALRM_EXIT");
								break;                                    
							case SIGSEGV:
								result(7, 0, time_usage, 0, "SIGSEGV_EXIT");
								break;
							case SIGXFSZ:
								result(4, 0, 0, mem_usage, "SIGXFSZ_EXIT");
								break;
							default:
								result(4, 0, 0, mem_usage, "RE");
						}
						ptrace(PTRACE_KILL, pid, NULL, NULL);
						break;
					}
					
					ptrace(PTRACE_SYSCALL, pid, NULL, NULL);
				}
			}
				
			ptrace(PTRACE_CONT, pid, NULL, NULL);
			wait4(pid, &runstat, WUNTRACED, &rinfo);
			time_usage = (int)(rinfo.ru_utime.tv_sec * 1000 + rinfo.ru_utime.tv_usec / 1000);
			if (time_usage > time_limit && !terminate) result(6, 0, time_usage, 0, "Time Limit Exceeded");
			if (rinfo.ru_maxrss > mem_usage) mem_usage = rinfo.ru_maxrss;
							
			if (terminate) return 0;
				//verify output
			if (spj){ //default
				char exec_spj[255], msg[4096], ac[]="Accepted";
				int status; double score;
				if (spj_mode == 0){
					sprintf(exec_spj, "./%s %s %s %s %s\n", argv[9], argv[3], argv[4], argv[5], argv[8]);
					FILE *res = popen(exec_spj, "r");
					if (res == NULL) result(3, 0, time_usage, mem_usage, "Checker error!");
					else{
						fscanf(res, "%d%lf%s", &status, &score, msg);
						result(0, score, time_usage, mem_usage, msg);
						pclose(res);
					}
											
				}else if (spj_mode == 1){ //cena
					sprintf(exec_spj, "./%s %s %s\n", argv[9], argv[8], argv[3]);
					if ((system(exec_spj) >> 8) > 0) result(3, 0, time_usage, mem_usage, "Checker error!");
					else{
						FILE *res = fopen("score.log", "r");
						fscanf(res, "%lf", &score);
						fclose(res);
						res = fopen("report.log", "r");
						if (res != NULL){
							fscanf(res, "%s", msg);
							fclose(res);
							result(0, score, time_usage, mem_usage, msg);
						}
						result(0, score, time_usage, mem_usage, ac);
					}
						
				}else if (spj_mode == 2){ //tsinsen
					sprintf(exec_spj, "./%s %s %s %s result.txt\n", argv[9], argv[3], argv[5], argv[4]);
					if ((system(exec_spj) >> 8) > 0) result(3, 0, time_usage, mem_usage, "Checker error!");
					else{
						FILE *res = fopen("result.txt", "r");
						fscanf(res, "%lf %s", &score, msg);
						score *= full_score;
						fclose(res);
						result(0, score, time_usage, mem_usage, msg);
					}
						
				}else if (spj_mode == 3){
					sprintf(exec_spj, "./%s %s %s %s\n", argv[9], argv[3], argv[4], argv[5]);
					if ((system(exec_spj) >> 8) > 0) result(2, 0, time_usage, mem_usage, "Wrong Answer");
					else result(0, full_score, time_usage, mem_usage, ac);
				}
			}else{
				int status = builtin_comp(argv[4], argv[5]);
				if (status > 0) result(2, 0, time_usage, mem_usage, "Wrong Answer!");
				else if (status < 0) result(1, 0, time_usage, mem_usage, "Presentation Error!");
				else result(0, full_score, time_usage, mem_usage, "Correct!");
			}
		}
	}else{
		
	}
	#ifdef DEBUG
		printf("Judge Ended!\n");
	#endif
	return 0;
}
