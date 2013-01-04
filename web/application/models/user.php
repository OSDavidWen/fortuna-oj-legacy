<?php

class User extends CI_Model{

	function __construct(){
		parent::__construct();
	}
	
	function is_logged_in(){
		if ( ! $this->session->userdata('uid')) return FALSE;
		return TRUE;
	}
	
	function uid(){
		return $this->session->userdata('uid');
	}
	
	function is_admin(){
		if ($this->session->userdata('priviledge') == 'admin') return TRUE;
		return FALSE;
	}
	
	function username_check($username){
		$result = $this->db->query("SELECT isEnabled FROM User WHERE name=?", array($username));
		if ($result->num_rows() == 0 || ! $result->row()->isEnabled) return FALSE;
		return TRUE;
	}
	
	function password_check($username, $password){
		$result = $this->db->query("SELECT priviledge, password FROM User WHERE name=?", array($username));
		if ($result->num_rows() == 0) return FALSE;
		if ($result->row()->password == $password) return $result; else return FALSE;
	}
	
	function login_check($username, $password, $admin = FALSE){
		$result = self::password_check($username, $password);
		if ($result == FALSE || ($admin && $query->row()->priviledge != 'admin')) return FALSE;
		return TRUE;
	}	
	
	function login_success($post = array()){
		$result = $this->db->query("SELECT priviledge, uid, showCategory FROM User WHERE name=?", array($post['username']));
		$this->session->set_userdata('username', $post['username']);
		$this->session->set_userdata('uid', $result->row()->uid);
		$this->session->set_userdata('priviledge', $result->row()->priviledge);
		$this->session->set_userdata('show_category', $result->row()->showCategory);
		$this->input->set_cookie(array('name' => 'priviledge', 'value' => $result->row()->priviledge, 'expire' => '86400'));
		
		$this->db->query("UPDATE User SET lastLogin=now(), lastIP=? WHERE uid=?", array($this->session->userdata('ip_address'), (int)$result->row()->uid));
	}
	
	function registion_success($post = array()){
		$data = array(
			'name' => $post['username'],
			'password' => md5(md5($post['password']) . $this->config->item('password_suffix')),
			'email' => $post['email'],
			'priviledge' => 'user',
		);
		$query = $this->db->query("SELECT * FROM School WHERE name=?", array($post['school']));
		if (isset($post['school']) && $query->num_rows() == 0){
			$query = $this->db->insert_string('School', array('name' => $post['school']));
			$this->db->query($query);
		}
		$query = $this->db->query("SELECT idSchool FROM School WHERE name=?", array($post['school']));
		$data['idSchool'] = $query->row()->idSchool;
		if (isset($post['description'])) $data['description'] = $post['description'];
		
		$sql = $this->db->insert_string('User', $data);
		$this->db->query($sql);
	}
	
	function logout(){
		$this->session->unset_userdata('username');
		$this->session->unset_userdata('uid');
		$this->session->unset_userdata('priviledge');
		$this->session->unset_userdata('show_category');
		$this->input->set_cookie(array('name' => 'priviledge', 'value' => '', 'expire' => ''));
	}
	
	function submit(){
		$this->db->query("UPDATE User SET submitCount=submitCount+1 WHERE uid=?", array($this->session->userdata('uid')));
	}

	function load_last_page($uid){
		return $this->db->query("SELECT lastPage FROM User WHERE uid=?", array($uid))->row()->lastPage;
	}
	
	function save_last_page($uid, $page){
		$this->db->query("UPDATE User SET lastPage=? WHERE uid=?", array($page, $uid));
	}
	
	function save_language($uid, $language){
		$this->db->query("UPDATE User SET language=? WHERE uid=?", array($language, $uid));
	}
	
	function count(){
		return $this->db->query("SELECT COUNT(*) AS count FROM User")->row()->count;
	}
	
	function load_user($uname){
		return $this->db->query("SELECT uid, email, description, idSchool, submitCount, solvedCount
								FROM User WHERE name=?", array($uname))->row();
	}
	
	function load_accepted($uid){
		return $this->db->query("SELECT DISTINCT pid FROM Submission WHERE uid=? AND status=0", array($uid))->result();
	}
	
	function load_school($idSchool){
		return $this->db->query("SELECT name FROM School WHERE idSchool=?", array($idSchool))->row()->name;
	}
	
	function load_rank($uid){
		$result = $this->db->query("SELECT solvedCount, submitCount FROM User WHERE uid=?", array($uid))->row();
		if ($result->submitCount == 0) $rate = 0;
		else $rate = $result->solvedCount / $result->submitCount;
		return $this->db->query("SELECT count(*) AS rank FROM User WHERE solvedCount > ? OR
			(solvedCount = ? AND solvedCount / submitCount > ?)", array($result->solvedCount, $result->solvedCount, $rate))->row()->rank;
	}
	
	function load_configuration($uid){
		return $this->db->query('SELECT showCategory, email FROM User WHERE uid=?', array($uid))->row();
	}
	
	function save_configuration($uid, $config){
		if (count($config) == 0) return;
		$sql = $this->db->update_string('User', $config, "uid=$uid");
		$this->session->set_userdata('show_category', $config['showCategory']);
		$this->db->query($sql);
	}
	
	function save_password($uid, $password){
		$sql = $this->db->update_string('User', array('password' => $password), "uid=$uid");
		$this->db->query($sql);
	}
	
	function load_users_list(){
		return $this->db->query("SELECT uid, name, idSchool, isEnabled, priviledge FROM User")->result();
	}
	
	function load_user_groups($uid, &$groups){
		$data = $this->db->query("SELECT gid FROM Group_has_User WHERE uid=?", array($uid))->result();
		foreach ($data as $row) $row->name = $groups[$row->gid]->name;
		return $data;
	}
	
	function change_status($uid){
		$this->db->query("UPDATE User SET isEnabled=1-isEnabled WHERE uid=?", array($uid));
	}
	
	function delete($uid){
		$this->db->query("DELETE FROM User WHERE uid=?", array($uid));
	}
}
