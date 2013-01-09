<?php

class Problems extends CI_Model{

	function __construct(){
		parent::__construct();
	}
	
	function change_status($pid){
		$this->db->query("UPDATE ProblemSet SET isShowed=1-isShowed WHERE pid=?", array($pid));
	}
	
	function uid($pid){
		return $this->db->query("SELECT uid FROM ProblemSet WHERE pid=?", array($pid))->row()->uid;
	}
	
	function count($uid = FALSE){
		if ( ! $uid)
			return $this->db->query("SELECT COUNT(*) AS count FROM ProblemSet")->row()->count;
		else
			return $this->db->query("SELECT COUNT(*) AS count FROM ProblemSet WHERE uid=?", array($uid))->row()->count;
	}
	
	function load_problemset($row_begin, $count, $rev = FALSE, $uid = FALSE){
		if ( ! $uid){
			if ($rev)
				return $this->db->query("SELECT pid, title, source, solvedCount, submitCount, scoreSum AS average, isShowed
										FROM ProblemSet ORDER BY pid DESC LIMIT ?, ?", array($row_begin, $count))->result();
			else
				return $this->db->query("SELECT pid, title, source, solvedCount, submitCount, scoreSum AS average, isShowed
										FROM ProblemSet LIMIT ?, ?", array($row_begin, $count))->result();
		} else {
			if ($rev)
				return $this->db->query("SELECT pid, title, source, solvedCount, submitCount, scoreSum AS average, isShowed
										FROM ProblemSet WHERE uid=? ORDER BY pid DESC LIMIT ?, ?", array($uid, $row_begin, $count))->result();
			else
				return $this->db->query("SELECT pid, title, source, solvedCount, submitCount, scoreSum AS average, isShowed
										FROM ProblemSet WHERE uid=? LIMIT ?, ?", array($uid, $row_begin, $count))->result();
		}
	}
	
	function load_problemset_status($uid, $start, $end){
		return $this->db->query("SELECT min(status) AS status, pid FROM Submission 
									WHERE uid=? AND pid>=? AND pid<? GROUP BY pid", array($uid, $start, $end))->result();
	}
	
	function load_dataconf($pid){
		return $this->db->query("SELECT title, dataConfiguration FROM ProblemSet WHERE pid=?", array($pid))->row();
	}
	
	function save_dataconf($pid, $data){
		$sql = $this->db->update_string('ProblemSet', array('dataConfiguration' => $data), "pid=$pid");
		$this->db->query($sql);
	}
	
	function load_code_size_limit($pid){
		$result = $this->db->query("SELECT codeSizeLimit FROM ProblemSet WHERE pid=?", array($pid));
		if ($result->num_rows() == 0) return FALSE;
		return $result->row()->codeSizeLimit;
	}
	
	function search_count($keyword){
		return $this->db->query("SELECT COUNT(*) AS count FROM ProblemSet
								WHERE title LIKE ? OR source LIKE ?", array($keyword, $keyword))->row()->count;
	}
	
	function load_search_problemset($keyword, $row_begin, $count){
		return $this->db->query("SELECT pid, title, source, solvedCount, submitCount, scoreSum AS average, isShowed FROM ProblemSet
								WHERE (title LIKE ? OR source LIKE ?) AND isShowed=1 LIMIT ?, ?", 
								array($keyword, $keyword, $row_begin, $count))->result();
	}
	
	function load_search_problemset_status($uid, $keyword){
		return $this->db->query("SELECT min(status) AS status, pid FROM Submission WHERE uid=? AND
								pid in (SELECT pid FROM ProblemSet WHERE (title LIKE ? OR source LIKE ?) AND isShowed=1)
								GROUP BY pid", array($uid, $keyword, $keyword))->result();
	}
	
	function load_problem($pid){
		$result = $this->db->query("SELECT * from ProblemSet WHERE pid=?", array($pid));
		if ($result->num_rows() == 0) return FALSE;
		return $result->row();
	}
	
	function load_limits($pid){
		$result = $this->db->query("SELECT pid, title, dataConfiguration from ProblemSet WHERE pid=? AND isShowed=1", array($pid));
		if ($result->num_rows() == 0) return FALSE;
		return $result->row();		
	}
	
	function add($data, $pid = 0){
		$cnt = $this->db->query('SELECT MAX(pid) AS cnt FROM ProblemSet')->row()->cnt + 1;
		if ($cnt == 1) $cnt = 1000;
		$this->db->query('ALTER TABLE ProblemSet AUTO_INCREMENT=?', array($cnt));

		if ($pid == 0){
			$data['uid'] = $this->user->uid();
			$sql = $this->db->insert_string('ProblemSet', $data);
		}else $sql = $this->db->update_string('ProblemSet', $data, "pid=$pid");
		$this->db->query($sql);
		
		return $pid == 0 ? $this->db->insert_id() : $pid;
	}
	
	function delete($pid){
		$this->db->query("DELETE FROM ProblemSet WHERE pid=?", array($pid));
	}
	
	function is_showed($pid){
		return $this->db->query("SELECT isShowed FROM ProblemSet WHERE pid=?", array($pid))->row()->isShowed;
	}
	
	function load_problem_submission($pid){
		return $this->db->query("SELECT sid FROM Submission WHERE pid=?", array($pid))->result();
	}
}
