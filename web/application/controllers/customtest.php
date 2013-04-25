<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Customtest extends CI_Controller {

	private function _redirect_page($method, $params = array()){
		if (method_exists($this, $method))
			return call_user_func_array(array($this, $method), $params);
		else
			show_404();
	}

	public function _remap($method, $params = array()){
		$this->load->model('user');
		
		if ($method == 'home') $method = 'run';
		
		if ($this->user->is_logged_in())
			$this->_redirect_page($method, $params);
		else
			$this->login();
	}
	
	public function run() {
		$this->load->library('form_validation');
		$this->load->helper('cookie');
		
		$this->form_validation->set_error_delimiters('<div class="alert alert-error">', '</div>');

		$this->form_validation->set_rules('language', 'Language', 'required');
		
		if ($this->form_validation->run() == FALSE){
			$data = array(
				'language'	=>	$this->input->cookie('language'),
				'code'		=>	'',
				'input'		=>	'',
				'output'	=>	''
			);

			$this->load->view('customtest/run', $data);
			
		} else {
			$language = $this->input->post('language');
			$this->input->set_cookie(array('name' => 'language', 'value' => $language, 'expire' => '10000000'));
			$uid = $this->session->userdata('uid');
			$this->user->save_language($uid, $language);

			$code = html_entity_decode($this->input->post('texteditor'));	
			$input = $this->input->post('input');
			
			$cmd = 'mkdir /tmp/foj/customtest > /dev/null';
			system($cmd);
			
			$path = '/tmp/foj/customtest/' . rand();
			$cmd = 'mkdir ' . $path . ' > /dev/null';
			system($cmd);
			
			$cmd = 'cp /usr/bin/judge_core ' . $path . ' > /dev/null';
			system($cmd);
			
			switch ($language) {
				case 'C':		$cmd = 'gcc Main.c -o Main';
								$source = 'Main.c';
								break;
				case 'C++':		$cmd = 'g++ Main.cpp -o Main';
								$source = 'Main.cpp';
								break;
				case 'C++11':	$cmd = 'g++ Main.code --std=c++11 -o Main';
								$source = 'Main.cpp';
								break;
				case 'Pascal':	$cmd = 'fpc Main.pas -oMain';
								$source = 'Main.pas';
								break;
			}
			
			$current_path = getcwd();
			chdir($path);
			
			$file = fopen('data.in', 'w');
			fwrite($file, $input);
			fclose($file);
			
			$file = fopen($source, 'w');
			fwrite($file, $code);
			fclose($file);
			
			$compile = system($cmd, $status);
			
			if ($status == 0) {
				$cmd = './judge_core 0 Main data.in /dev/null data.out 10000 524288 0 > /dev/null';
				system($cmd);
				
				$output = file_get_contents('data.out');
				
				$file = fopen('test.log', 'r');
				fscanf($file, "%d %f %d %d", $time, $time, $time, $memory);
				fclose($file);
			} else {
				$output = $compile;
				$time = $memory = false;
			}
			
			$data = array(
				'language' => $language,
				'code'	=>	$code,
				'input'	=>	$input,
				'output'	=>	$output,
				'time'	=>	$time,
				'memory'	=>	$memory,
				'status'	=>	$status
			);
			
			system('rm -R ' . $path);
			
			chdir($current_path);
			$this->load->view('customtest/run', $data);
		}
	}
}