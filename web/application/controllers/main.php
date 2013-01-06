<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {

	private function _redirect_page($method, $params = array()){
		if (method_exists($this, $method))
			return call_user_func_array(array($this, $method), $params);
		else
			show_404();
	}

	public function _remap($method, $params = array()){
		$this->load->model('user');
		
		if ($this->user->is_logged_in() || $method == 'index' || $method == 'register' || $method == 'userinfo' || $method == 'logout')
			$this->_redirect_page($method, $params);
		else
			$this->login();
	}
	
	public function logout(){
		$this->user->logout();
		$this->load->view('main/home');
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
	
	public function userinfo(){
		$name = $this->session->userdata('username');
		$this->load->view('userinfo', array('name' => $name));
	}
	
	public function register(){
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span class="alert add-on alert-error">', '</span>');
		
		$this->form_validation->set_rules('username', 'Username', 'required|is_unique[User.name]');
		$this->form_validation->set_rules('password', 'Password', 'required|matches[confirm_password]');
		$this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[User.email]');

		$this->form_validation->set_message('is_unique', '%s not available!');
		$this->form_validation->set_message('required', '%s is required!');
		
		if ($this->form_validation->run() == FALSE)
			$this->load->view('register');
		else {
			$this->user->registion_success($this->input->post(NULL, TRUE));
			
			$this->load->view('success');
		}
	}

	public function index(){
		$theme = $this->input->cookie('theme');
		if ( ! $theme) $theme = 'default';
		$this->load->view("$theme/framework", array('logged_in' => $this->session->userdata('uid')));
	}
	
	public function home(){
		$this->load->view('main/home');
	}
	
	function submit_check($pid){
		$this->load->model('problems');
		//$code_size_limit = $this->problems->load_code_size_limit($pid);
		//if ($code_size_limit == FALSE) return FALSE;
		//if ($code_size_limit > 0 && $code_size_limit < strlen($this->input->post('texteditor'))) return FALSE;
		return TRUE;
	}
	
	static function _convert_status($status){
		switch ($status){
			case -1: return '<span class="label label-info">waiting</span>';
			case 0: return '<span class="label label-success">AC</span>';
			case 1: return '<span class="label label-important">PE</span>';
			case 2: return '<span class="label label-important">WA</span>';
			case 3: return '<span class="label">Chk Err</span>';
			case 4: return '<span class="label label-warning">OLE</span>';
			case 5: return '<span class="label label-warning">MLE</span>';
			case 6: return '<span class="label label-warning">TLE</span>';
			case 7: return '<span class="label label-important">RE</span>';
			case 8: return '<span class="label">CE</span>';
			case 9: return '<span class="label">IE</span>';
			default: return '';
		}
	}

	public function problemset($page = 0){
		$problems_per_page = 20;
		
		$uid = $this->session->userdata('uid');
		$this->load->model('user');
		$this->load->model('problems');
		$this->load->model('misc');
		if (! ($keyword = $this->input->get('search', TRUE))){
			if ($page == 0){
				$page = $this->user->load_last_page($uid);
			}else $this->user->save_last_page($uid, $page);
		} else if ($page == 0) $page = 1;

		if ($keyword){
			$keyword = "%" . $keyword . "%";
			$count = $this->problems->search_count($keyword);
			if ($count > 0 && ($count + $problems_per_page - 1) / $problems_per_page < $page)
				$page = ($count + $problems_per_page - 1) / $problems_per_page;
			$row_begin = ($page - 1) * $problems_per_page;
			$data = $this->problems->load_search_problemset($keyword, $row_begin, $problems_per_page);
	
			$result = $this->problems->load_search_problemset_status($uid, $keyword);
		}else{
			$count = $this->problems->count();
			if ($count > 0 && ($count + $problems_per_page - 1) / $problems_per_page < $page)
				$page = ($count + $problems_per_page - 1) / $problems_per_page;
			$row_begin = ($page - 1) * $problems_per_page;
			$data = $this->problems->load_problemset($row_begin, $problems_per_page);
			$start = $row_begin + 1000;
			$end = $start + $problems_per_page;
			$result = $this->problems->load_problemset_status($uid, $start, $end);
		}
		
		foreach ($result as $row) $status["$row->pid"] = $row->status;

		$categorization = $this->misc->load_categorization();
		foreach ($data as $row){
			if ($row->submitCount > 0) $row->average = $row->average / $row->submitCount;
			$row->category = $this->misc->load_problem_category($row->pid, $categorization);
			$row->average = number_format($row->average, 2);
			$row->status = '';
			$row->ac = FALSE;
			if (isset($status["$row->pid"])){
				$row->status = self::_convert_status($status["$row->pid"]);
				if ($status["$row->pid"] == 0) $row->ac = TRUE;
			}
		}

		$this->load->library('pagination');
		$config['base_url'] = '#main/problemset/';
		$config['total_rows'] = $count;
		$config['per_page'] = $problems_per_page;
		$config['cur_page'] = $page;
		if ($keyword) $config['suffix'] = '?search=' . $this->input->get('search');
		$this->pagination->initialize($config);

		$this->load->view('main/problemset', array('data' => $data));
	}

	public function show($pid){
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
			
			$categorization = $this->misc->load_categorization();
			$data->category = $this->misc->load_problem_category($pid, $categorization);
		}
	
		if ($data == FALSE)
			$this->load->view('error', array('message' => 'Problem not available!'));
		else
			$this->load->view('main/show', array('data' => $data, 'category' => $categorization));
	}
	
	public function limits($pid){
		$this->load->model('problems');
		$data = $this->problems->load_limits($pid);
		if ($data != FALSE){
			$data->data = json_decode($data->dataConfiguration);
			
			$data->timeLimit = $data->memoryLimit = 0;
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
			if ($data->timeLimit < 0){
				unset($data->timeLimit);
				unset($data->memoryLimit);
			}
		}
	
		if ($data == FALSE)
			$this->load->view('error', array('message' => 'Problem not available!'));
		else
			$this->load->view('main/limits', array('data' => $data));
	}
	
	public function addtag($pid){
		$id = $this->input->get('tag', TRUE);
		if (!$id) return;
		
		$this->load->model('misc');
		if ($this->misc->is_accepted($this->session->userdata('uid'), $pid))
			$this->misc->add_categorization($pid, $id);
	}
	
	public function deltag($pid, $id){
		$this->load->model('misc');
		if ($this->misc->is_accepted($this->session->userdata('uid'), $pid))
			$this->misc->delete_categorization($pid, $id);		
	}

	public function submit($pid = 0, $cid = 0, $gid = 0, $tid = 0){
		$this->load->library('form_validation');
		$this->load->helper('cookie');
		$this->form_validation->set_error_delimiters('<div class="alert alert-error">', '</div>');

		$this->form_validation->set_rules('pid', 'Problem ID', 'required|callback_submit_check');
		
		if ($this->form_validation->run() == FALSE){
			$data = array(
				'pid' => $pid,
				'language' => $this->input->cookie('language'),
				'code' => ''
			);
			if ($cid > 0) $data['cid'] = $cid;
			if ($tid > 0) $data['tid'] = $tid;
			if ($gid > 0) $data['gid'] = $gid;
			$this->load->view('main/submit', $data);
			
		} else {
			$language = $this->input->post('language');
			$this->input->set_cookie(array('name' => 'language', 'value' => $language, 'expire' => '10000000'));
			$uid = $this->session->userdata('uid');
			$this->user->save_language($uid, $language);

			$data = array(
				'uid'	=>	$uid,
				'name'	=>	$this->session->userdata('username'),
				'pid'	=>	$this->input->post('pid', TRUE),
				'code'	=>	$this->input->post('texteditor', TRUE),
				'codeLength'	=>	strlen($this->input->post('texteditor', TRUE)),
				'language'	=>	$language,
				'submitTime'	=>	date("Y-m-d H:i:s")
			);
			$data['code'] = html_entity_decode($data['code']);
			if ($this->input->post('cid') != '') $data['cid'] = $this->input->post('cid');
			if ($this->input->post('gid') != '') $data['gid'] = $this->input->post('gid');
			if ($this->input->post('tid') != '') $data['tid'] = $this->input->post('tid');			
			
			if (isset($data['tid'])){
				$this->load->model('misc');
				$info = $this->misc->load_task_info($data['gid'], $data['tid']);
				if (strtotime($info->startTime) > time() || strtotime($info->endTime) < time()) return;
				$languages = explode(',', $info->language);
				if ( ! in_array($language, $languages)) return;
			}
			
			if (isset($data['cid'])){
				$this->load->model('contests');
				$info = $this->contests->load_contest_status($data['cid']);
				if (strtotime($info->startTime) > time() || strtotime($info->endTime) < time()) return;		
				$languages = explode(',', $info->language);
				if ( ! in_array($language, $languages)) return;
			}			
			
			$this->load->model('problems');
			$showed = $this->problems->is_showed($data['pid']);
			if ($showed == 0){
				if ($this->user->is_admin()) $data['isShowed'] = 0;
				else return;
			}

			$this->load->model('submission');
			$this->submission->save_submission($data);
			$this->user->submit();
			
			$this->load->view('success');
		}
	}

	public function statistic($pid, $page = 1){
		$users_per_page = 20;
		
		$this->load->model('submission');
		$row_begin = ($page - 1) * $users_per_page;
		$count = $this->submission->statistic_count($pid);
		$data = $this->submission->load_statistic($pid, $row_begin, $users_per_page);
		$this->submission->format_data($data);
		
		$this->load->library('pagination');
		$config['base_url'] = "#main/statistic/$pid/";
		$config['total_rows'] = $count;
		$config['per_page'] = $users_per_page;
		$config['cur_page'] = $page;
		$config['uri_segment'] = 4;
		$this->pagination->initialize($config);
		
		$this->load->view('main/statistic', array('data' => $data, 'pid' => $pid));
	}

	public function status($page = 1){
		$submissions_per_page = 20;
		
		$filter = (array)$this->input->post(NULL, TRUE);
		
		$this->load->model('submission');
		$row_begin = ($page - 1) * $submissions_per_page;
		$count = $this->submission->count($filter);
		$data = $this->submission->load_status($row_begin, $submissions_per_page, $filter);
		$this->submission->format_data($data);
		
		$this->load->library('pagination');
		$config['base_url'] = '#main/status/';
		$config['total_rows'] = $count;
		$config['per_page'] = $submissions_per_page;
		$config['first_link'] = 'Top';
		$config['last_link'] = FALSE;
		$this->pagination->initialize($config);

		$filter = array_merge(array('status' => array(), 'languages' =>array()), $filter);
		$this->load->view('main/status', array('data'	=>	$data, 'filter' => $filter));
	}
	
	public function submission_change_access($sid){
		$this->load->model('submission');
		if ($this->session->userdata('priviledge') == 'admin' || $this->session->userdata('uid') == $this->submission->load_uid($sid))
			$this->submission->change_access($sid);
	}

	public function code($sid){
		$this->load->model('submission');
		$data = $this->submission->load_code($sid);
		
		if ($data == FALSE)
			$this->load->view('error', array('message' => 'You have NO priviledge to see this code.'));
		else
			$this->load->view('main/code', $data);
	}
	
	public function result($sid){
		$this->load->model('submission');
		$data = $this->submission->load_result($sid);

		if ($data == FALSE){
			$this->load->view('error', array('message' => 'You have NO priviledge to see this result.'));
		} else {
			$result = json_decode($data->result);
			if ($result->compileStatus)
				foreach ($result->cases as $row => $value)
					$this->submission->format_data($value->tests);
			$this->load->view('main/result', array('result' => $result));
		}
	}
	
	public function ranklist($page = 1){
		$users_per_page = 20;
		
		$this->load->model('user');
		$this->load->model('misc');
		$count = $this->user->count();
		if ($count > 0 && ($count + $users_per_page - 1) / $users_per_page < $page)
			$page = ($count + $users_per_page - 1) / $users_per_page;
		$row_begin = ($page - 1) * $users_per_page;
		$data = $this->misc->load_ranklist($row_begin, $users_per_page);
		$rank = $row_begin;
		foreach ($data as $row){
			$row->rank = ++$rank;
			$row->rate = 0.00;
			if ($row->submitCount > 0) $row->rate = $row->solvedCount / $row->submitCount * 100;
			$row->rate = number_format($row->rate, 2);
		}
		
		$this->load->library('pagination');
		$config['base_url'] = '#main/ranklist/';
		$config['total_rows'] = $count;
		$config['per_page'] = $users_per_page;
		$this->pagination->initialize($config);

		$this->load->view('main/ranklist', array('data' => $data));
	}

}

// End of file main.php
