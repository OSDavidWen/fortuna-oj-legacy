<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Task extends CI_Controller {

	private function _redirect_page($method, $params = array()){
		if (method_exists($this, $method)){
			call_user_func_array(array($this, $method), $params);
		}else
			show_404();
	}

	public function _remap($method, $params = array()){
		$this->load->model('user');
		$this->load->model('misc');
		
		if ($this->user->is_logged_in())
			$this->_redirect_page($method, $params);
		else
			$this->login();
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

	public function task_list(){
		$uid = $this->user->uid();
		
		$groups = $this->misc->load_groups($uid);
		$data = $this->user->load_user_groups($uid, $groups);
		$submissions = $this->misc->load_tasks_submissions($uid);
		foreach ($data as $group){
			$group->tasks = $this->misc->load_group_tasks($group->gid);
			foreach ($group->tasks as $task)
				foreach ($task->problems as $problem)
					$problem->status = isset($submissions[$task->tid][$problem->pid]) ? $submissions[$task->tid][$problem->pid] : '';
		}
		
		$this->load->view('task/task_list', array('data' => $data));
	}
	
	public function show($pid, $gid, $tid){
		$this->load->model('problems');
		$this->load->model('misc');
		$data = $this->problems->load_problem($pid);
		if ($data != FALSE){
			$data->data = json_decode($data->dataConfiguration);
			
			$data->timeLimit = $data->memoryLimit = 0;
			if (isset($data->data)){
				foreach ($data->data->cases as $case){
					foreach ($case->tests as $test){
						if ($data->timeLimit == 0){
							$data->timeLimit = $test->timeLimit;
							$data->memoryLimit = $test->memoryLimit;
						} elseif ($data->timeLimit != $test->timeLimit || $data->memoryLimit != $test->memoryLimit)
							$data->timeLimit = -1;
							
						if ($data->timeLimit < 0) break;
					}
					if ($data->timeLimit < 0) break;
				}
			}
			if ($data->timeLimit < 0){
				unset($data->timeLimit);
				unset($data->memoryLimit);
			}
		}
		$data->tid = $tid;
		$data->gid = $gid;
		
		$endTime = $this->misc->load_task_info($gid, $tid)->endTime;
		$data->timeout = strtotime($endTime) < time();
		
		if ($data == FALSE)
			$this->load->view('error', array('message' => 'Problem not available!'));
		else
			$this->load->view('task/show', array('data' => $data));
	}

}
