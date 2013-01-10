<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

	private function _redirect_page($method, $params = array()){
		if (method_exists($this, $method))
			return call_user_func_array(array($this, $method), $params);
		else
			show_404();
	}

	public function _remap($method, $params = array()){
		$this->load->model('user');
		
		$allowed_methods = array('addproblem', 'problemset');
		$restrcited_methods = array('delete_problem', 'dataconf', 'scan', 'upload', 'change_problem_status');
		if ($this->user->is_logged_in()){
			if ($this->user->is_admin() || in_array($method, $allowed_methods)) $this->_redirect_page($method, $params);
			else if (in_array($method, $restrcited_methods)){
				$this->load->model('problems');
				if (isset($params[0]) && $this->problems->uid($params[0]) == $this->user->uid())
					$this->_redirect_page($method, $params);
				else
					$this->load->view('information', array('data' => '<h5 class="alert">Operation not permitted!</h5>'));
			}else
				$this->load->view('information', array('data' => '<h5 class="alert">You are not administrators!</h5>'));
		}else
			$this->login();
	}
	
	function username_check($username){
		return $this->user->username_check($username);
	}

	function password_check($password){
		$password = md5(md5($password) . $this->config->item('password_suffix'));
		return $this->user->login_check($this->input->post('username', TRUE), $password);
	}
	
	function login(){
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('<span class="add-on alert alert-error">', '</span>');
			
		$this->form_validation->set_rules('username', 'Username', 'required|callback_username_check');
		$this->form_validation->set_rules('password', 'Password', 'required|callback_password_check');
			
		$this->form_validation->set_message('required', "%s is required");
		$this->form_validation->set_message('username_check', 'User NOT exist or DISABLED!');
		$this->form_validation->set_message('password_check', 'Password Error!');

		if ($this->form_validation->run() == FALSE){
			$this->load->view('login');
		}else{
			$this->user->login_success($this->input->post(NULL, TRUE));
			
			$this->load->view('success');
		}
	}
	
	public function index(){
		$this->load->view('admin/index');
	}
	
	public function addproblem($pid = 0){
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<div class="alert alert-error">', '</div>');
		
		$this->form_validation->set_rules('title', 'Title', 'required');
		$this->form_validation->set_rules('problemDescription', 'Problem Description', 'required');
		$this->form_validation->set_rules('inputDescription', 'Input Description', 'required');
		$this->form_validation->set_rules('outputDescription', 'Output Description', 'required');
		$this->form_validation->set_rules('inputSample', 'Sample Input', 'required');
		$this->form_validation->set_rules('outputSample', 'Sample Output', 'required');
		$this->form_validation->set_rules('dataConstraint', 'Data Constraint', 'required');
		
		if ($this->form_validation->run() == FALSE){
			if ($pid > 0){
				$data = (array)$this->db->query("SELECT * FROM ProblemSet WHERE pid=?", array($pid))->row();
				$data['pid'] = $pid;
			}else $data = NULL;

			$this->load->view("admin/addproblem", $data);
		}else{
			$data = $this->input->post(NULL);
			$data['codeSizeLimit'] = (int)$data['codeSizeLimit'];
			$data['isShowed'] = 0;
			$this->load->model('problems');
			if ($pid == 0){
				$new = TRUE;
				$pid = $this->problems->add($data);
				$this->problems->save_dataconf($pid, '{IOMode:0, cases:[]}');
			}else{
				$new = FALSE;
				$this->problems->add($data, $pid);
			}
			
			$target_path = $this->config->item('data_path') . '/' . $pid . '/';
			if (! is_dir($target_path)) mkdir($target_path);
			
			if ($new) $this->load->view('information', array('data' => 'success' . $pid));
			else $this->load->view('success');
		}
	}
	
	function delete_problem($pid){
		$this->load->model('problems');
		$this->problems->delete($pid);
	}
	
	public function change_problem_status($pid){
		$this->load->model('problems');
		$this->problems->change_status($pid);
	}
	
	public function problemset($page = 1){
		$problems_per_page = 20;
		$uid = FALSE;
		if ( ! $this->user->is_admin()) $uid = $this->user->uid();
	
		$this->load->model('problems');
		$count = $this->problems->count($uid);
		if ($count > 0 && ($count + $problems_per_page - 1) / $problems_per_page < $page)
			$page = ($count + $problems_per_page - 1) / $problems_per_page;
		$row_begin = ($page - 1) * $problems_per_page;
		$data = $this->problems->load_problemset($row_begin, $problems_per_page, TRUE, $uid);
		foreach ($data as $row)
			if ($row->isShowed == 1) $row->isShowed = '<span class="label label-success">Showed</span>';
			else $row->isShowed = '<span class="label label-important">Hidden</span>';

		$this->load->library('pagination');
		$config['base_url'] = base_url() . 'index.php#admin/problemset/';
		$config['total_rows'] = $count;
		$config['per_page'] = $problems_per_page;
		$config['cur_page'] = $page;
		$this->pagination->initialize($config);

		$this->load->view('admin/problemset', array('data' => $data, 'page' => $page));
	}

	public function dataconf($pid){
		$this->load->library('form_validation');
		$this->load->model('problems');
		$this->form_validation->set_error_delimiters('<div class="alert">', '</div>');
		
		$this->form_validation->set_rules('pid', 'pid', 'required');
		
		if ($this->form_validation->run() == FALSE){
			$data = $this->problems->load_dataconf($pid);
			$title = $data->title;
			$data = $data->dataConfiguration;

			$this->load->view('admin/dataconf', array('data' => $data, 'pid' => $pid, 'title' => $title));
		} else {
			$post = $this->input->post(NULL, TRUE);
			
			$data = NULL;
			$data['IOMode'] = (int)$post['IOMode'];
			$ccnt = 0;
			if (isset($post['score'])){
				foreach ($post['score'] as $case){
					if (isset($newcase)) unset($newcase);
					$newcase['score'] = (double)$case;
					$ccnt++;
					$data['cases'][] = $newcase;
				}
			}
			
			$tcnt = $post['tcnt'];
			for ($i = 1000000000; $i < 1000000000 + $tcnt; $i++){
				if (isset($post['infile'][$i])){
					if (isset($test)) unset($test);
					
					$test['input'] = $post['infile'][$i];
					$test['output'] = $post['outfile'][$i];
					$test['userInput'] = $post['user_input'];
					$test['userOutput'] = $post['user_output'];
					$test['timeLimit'] = (int)$post['time'][$i];
					$test['memoryLimit'] = (int)$post['memory'][$i];
					
					$data['cases'][(int)$post['case_no'][$i]]['tests'][] = $test;
				}
			}
			
			if (isset($post['spj'])){
				$data['spjMode'] = (int)$post['spjMode'];
				$data['spjFile'] = $post['spjFile'];
			}
			if ($data['IOMode'] == 3) $data['framework'] = $post['framework'];
			
			$data = json_encode($data);
			$this->problems->save_dataconf($this->input->post('pid'), $data);
			
			$this->load->view('success');
		}
	}
	
	public function upload($pid){
		$temp_file = $_FILES['Filedata']['tmp_name'];
		$target_path = $this->config->item('data_path') . $pid . '/';
		if (! is_dir($target_path)) mkdir($target_path);
		$target_file = $target_path . $_FILES['Filedata']['name'];

		$file_types = array('c', 'cpp', 'pas');
		$file_parts = pathinfo($_FILES['Filedata']['name']);
		$basename = $file_parts['basename'];
		$filename = $file_parts['filename'];
		$extension = $file_parts['extension'];
	
	//	if (in_array($file_parts['extension'],$file_types))
		if ( ! is_executable($temp_file))
			move_uploaded_file($temp_file, $target_file);
			
		if (in_array($extension, $file_types)){
			chdir($target_path);
			if ($extension == 'c')
				exec("gcc $basename -o $filename");
			if ($extension == 'cpp')
				exec("g++ $basename -o $filename");
			if ($extension == 'pas')
				exec("fpc $basename -o$filename");
		}
	}
	
	public function scan($pid){
		$target_path = $this->config->item('data_path') . '/' . $pid . '/';
		chdir($target_path);
		$dir = scandir('.');
		
		$cnts = 0;
		foreach ($dir as $file){
			if (is_file($file)){
				$info = pathinfo('./' . $file);
				$infile = $info['basename'];
				if (!strpos($infile, '.in')) continue;
				$outfile1 = str_ireplace('.in', '.out', $infile);
				$outfile2 = str_ireplace('.in', '.ans', $infile);
				$outfile3 = str_ireplace('.in', '.ou', $infile);
				if (is_file($outfile1) || is_file($outfile2) || is_file($outfile3)) $cnts++;
			}
		}

		foreach ($dir as $file){
			if (is_file($file)){
				$info = pathinfo('./' . $file);
				$infile = $info['basename'];
				if (!strpos($infile, '.in')) continue;
				$outfile1 = str_ireplace('.in', '.out', $infile);
				$outfile2 = str_ireplace('.in', '.ans', $infile);
				$outfile3 = str_ireplace('.in', '.ou', $infile);
				if (is_file($outfile1) || is_file($outfile2) || is_file($outfile3)){
					if (isset($test)) unset($test);
					if (isset($case)) unset($case);
					$test['input'] = $infile;
					if (is_file($outfile1)) $test['output'] = $outfile1;
					else if (is_file($outfile2)) $test['output'] = $outfile2;
					else if (is_file($outfile3)) $test['output'] = $outfile3;
					$case['tests'][] = $test;
					$data['cases'][] = $case;
				}
			}
		}
		
		echo json_encode($data);
	}
	
	public function contestlist($page = 1){
		$contests_per_page = 20;
	
		$this->load->model('contests');
		$count = $this->contests->count();
		if ($count > 0 && ($count + $contests_per_page - 1) / $contests_per_page < $page)
			$page = ($count + $contests_per_page - 1) / $contests_per_page;
		$row_begin = ($page - 1) * $contests_per_page;
		$data = $this->contests->load_contests_list($row_begin, $contests_per_page);
		foreach ($data as $row){
			$startTime = strtotime($row->startTime);
			$endTime = strtotime($row->endTime);
			$now = strtotime('now');
			if ($now > $endTime) $row->status = '<span class="label label-success">Ended</span>';
			else if ($now < $startTime) $row->status = '<span class="label label-info">Pending</span>';
			else{
				$row->status = '<span class="label label-important">Running</span>';
				$row->running = TRUE;
			}
			
			$row->count = $this->contests->load_contest_teams_count($row->cid);
		}

		$this->load->library('pagination');
		$config['base_url'] = base_url() . 'index.php#admin/contestlist/';
		$config['total_rows'] = $count;
		$config['per_page'] = $contests_per_page;
		$config['cur_page'] = $page;
		$this->pagination->initialize($config);

		$this->load->view('admin/contestlist', array('data' => $data));
	}

	public function newcontest($cid = 0){
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<div class="alert alert-error">', '</div>');
		
		$this->form_validation->set_rules('contest_title', 'Title', 'required');
		$this->form_validation->set_rules('start_date', 'Start Date', 'required');
		$this->form_validation->set_rules('start_time', 'Start Time', 'required');
		$this->form_validation->set_rules('end_date', 'End Date', 'required');
		$this->form_validation->set_rules('end_time', 'End Time', 'required');
		$this->form_validation->set_rules('teamMode', 'Team Mode', 'required');
		$this->form_validation->set_rules('contestMode', 'Contest Mode', 'required');
		$this->form_validation->set_rules('contestType', 'Contest Type', 'required');
		
		$this->load->model('contests');
		if ($this->form_validation->run() == FALSE){
			if ($cid > 0) $data = $this->contests->load_contest_configuration($cid);
			else $data = NULL;

			$this->load->view('admin/newcontest', $data);
		}else{
			$data = $this->input->post(NULL, TRUE);
			$data['isShowed'] = 1;
			if ($cid == 0) $this->contests->add($data);
			else $this->contests->add($data, $cid);
			
			$this->load->view('success');
		}
	}
	
	function delete_contest($cid){
		$this->load->model('contests');
		$this->contests->delete($cid);
	}
	
	function users(){
		$this->load->model('misc');
		$data = $this->user->load_users_list();
		$groups = $this->misc->load_groups($this->session->userdata('uid'));
		foreach ($data as $row){
			$row->groups = $this->user->load_user_groups($row->uid, $groups);
		}
		$this->load->view('admin/users', array('data' => $data));
	}
	
	function change_user_status($uid){
		$this->user->change_status($uid);
	}
	
	function delete_user($uid){
		$this->user->delete($uid);
	}
	
	function new_task($tid = 0){
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<div class="alert alert-error">', '</div>');
		
		$this->form_validation->set_rules('task_title', 'Title', 'required');
		$this->form_validation->set_rules('description', 'Description', '');
		
		$this->load->model('misc');
		if ($this->form_validation->run() == FALSE){
			if ($tid > 0) $data = $this->misc->load_task($tid);
			else $data = NULL;

			$this->load->view('task/new_task', $data);
		}else{
			$data = $this->input->post(NULL, TRUE);
			$this->misc->add_task($data, $tid);
			
			$this->load->view('success');
		}
	}
	
	function task_list($page = 1){
		$tasks_per_page = 20;
		
		$this->load->model('misc');
		
		$begin = ($page - 1) * $tasks_per_page;
		$count = $this->misc->count_tasks();
		$tasks = $this->misc->load_task_list($begin, $tasks_per_page);
		
		$this->load->library('pagination');
		$config['base_url'] = base_url() . 'index.php#admin/task_list/';
		$config['total_rows'] = $count;
		$config['per_page'] = $tasks_per_page;
		$config['cur_page'] = $page;
		$this->pagination->initialize($config);
		
		$this->load->view('admin/task_list', array('tasks' => $tasks));
	}

	function change_submission_status($sid){
		$this->load->model('submission');
		$this->submission->change_status($sid);
	}
	
	private function _set_pending($sid){
		$this->submission->rejudge($sid);

	}
	
	function rejudge(){
		$this->load->library('form_validation');
		$this->load->model('problems');
		$this->load->model('submission');
		$this->form_validation->set_error_delimiters('<div class="alert alert-error">', '</div>');
		
		$this->form_validation->set_rules('type', 'Type', 'required');
		$this->form_validation->set_rules('id', 'ID', 'required');
		if ($this->form_validation->run() == FALSE)
			$this->load->view('admin/rejudge');
		else{
			$data = $this->input->post(NULL, TRUE);
			if ($data['type'] == 'submission'){
				$this->submission->rejudge($data['id']);
			}else{
				$data = $this->problems->load_problem_submission($data['id']);
				foreach ($data as $row)
					$this->submission->rejudge($row->sid);
			}
			$this->load->view('success');
		}
	}
}

// End of file admin.php