<?php

class Submission extends CI_Model{

	function __construct(){
		parent::__construct();
	}
	
	function change_status($sid){
		$this->db->query("UPDATE Submission SET isShowed=1-isShowed WHERE sid=?", array($sid));
	}
	
	function change_access($sid){
		$this->db->query("UPDATE Submission SET private=1-private WHERE sid=?", array($sid));
	}
	
	function save_submission($data){
		$sql = $this->db->insert_string('Submission', $data);
		$this->db->query($sql);
		$this->db->query("UPDATE ProblemSet SET submitCount=submitCount+1 WHERE pid=?", array($data['pid']));
	}
	
	function format_data(&$data){
		foreach ($data as $row){
			switch ($row->status){
				case -1: $row->result = '<span class="label">Pending</span>'; break;
				case 0: $row->result = '<span class="label label-success">Accepted</span>'; break;
				case 1: $row->result = '<span class="label label-important">Presentation Error</span>'; break;
				case 2: $row->result = '<span class="label label-important">Wrong Answer</span>'; break;
				case 3: $row->result = '<span class="label label-info">Checker Error</span>'; break;
				case 4: $row->result = '<span class="label label-warning">Output Limit Exceeded</span>'; break;
				case 5: $row->result = '<span class="label label-warning">Memory Limit Exceeded</span>'; break;
				case 6: $row->result = '<span class="label label-warning">Time Limit Exceeded</span>'; break;
				case 7: $row->result = '<span class="label label-important">Runtime Error</span>'; break;
				case 8: $row->result = '<span class="label">Compile Error</span>'; break;
				case 9: $row->result = '<span class="label">Internal Error</span>'; break;
				default: $row->result = 'Nothing Happened';
			}

			if (isset($row->codeLength)) $row->codeLength = $row->codeLength . ' bytes';

			if (isset($row->time)) $row->time = $row->time . ' ms';

			if (isset($row->memory)){
				if ($row->memory >= 1048576) $row->memory = number_format($row->memory / 1048576, 2) . ' GB';
				else if ($row->memory >= 1024) $row->memory = number_format($row->memory / 1024, 2) . ' MB';
				else $row->memory = $row->memory . ' KB';
			}
			
		}
	}
	
	function statistic_count($pid){
		return $this->db->query("SELECT COUNT(DISTINCT uid) AS count FROM Submission WHERE pid=?", array($pid))->row()->count;
	}
	
	function load_statistic($pid, $row_begin, $count){
		return $this->db->query("SELECT *, COUNT(DISTINCT A.uid) FROM
			(SELECT sid, uid, status, name, score, time, memory, codeLength, submitTime, language, 
			-score*1e15+time*1e10+memory*1e5+sid val FROM Submission WHERE pid=?) A
			RIGHT JOIN
			(SELECT uid, min(-score*1e15+time*1e10+memory*1e5+sid) eval, COUNT(*) count FROM Submission WHERE pid=? GROUP BY uid) B
			ON A.val=B.eval AND A.uid=B.uid GROUP BY A.uid ORDER BY A.val LIMIT ?,?;",
			array($pid, $pid, $row_begin, $count))->result();
	}
	
	private function filter_to_string($filter){
		$conditions = '';
		if (isset($filter['problems'])){
			$conditions .= ' AND pid IN (';
			foreach ($filter['problems'] as $pid) $conditions .= $pid . ',';
			$conditions[strlen($conditions) - 1] = ')';
		}
		if (isset($filter['users'])){
			$conditions .= ' AND name IN (';
			foreach ($filter['users'] as $name) $conditions .= "'$name',";
			$conditions[strlen($conditions) - 1] = ')';
		}
		if (isset($filter['status'])){
			$conditions .= ' AND status IN (';
			foreach ($filter['status'] as $status) $conditions .= $status . ',';
			$conditions[strlen($conditions) - 1] = ')';
		}
		if (isset($filter['languages'])){
			$conditions .= ' AND language IN (';
			foreach ($filter['languages'] as $language) $conditions .= "'$language',";
			$conditions[strlen($conditions) - 1] = ')';
		}
		return $conditions;
	}
	
	function count($filter = NULL){
		$conditions = self::filter_to_string($filter);
		return $this->db->query("SELECT COUNT(*) AS count FROM Submission WHERE cid IS NULL $conditions")->row()->count;
	}
	
	function load_status($row_begin, $count, $filter = NULL){
		$conditions = self::filter_to_string($filter);
		return $this->db->query("SELECT sid, uid, name, pid, status, score, time, memory, codeLength, submitTime, language, isShowed, private 
								FROM Submission WHERE cid IS NULL $conditions ORDER BY sid DESC LIMIT ?, ?", array($row_begin, $count))->result();
	}
	
	function load_code($sid){
		$result = $this->db->query("SELECT uid, pid, code, language, private FROM Submission WHERE sid=?", array($sid));
		if ($result->num_rows() == 0) return FALSE; else $result = $result->row();
		$uid = $this->session->userdata('uid');
		if ($result->uid == $uid || $this->session->userdata('priviledge') == 'admin' || $result->private == 0 || 
			$this->db->query("SELECT * FROM Submission WHERE pid=? AND uid=? AND status=0", array($result->pid, $uid))->num_rows() > 0)
			return $result;
		return FALSE;
	}
	
	function load_result($sid){
		$result = $this->db->query("SELECT uid, judgeResult AS result FROM Submission WHERE sid=?", array($sid));
		if ($result->num_rows() == 0) return FALSE;
		if ($result->row()->uid == $this->session->userdata('uid') || $this->session->userdata('priviledge') == 'admin') return $result->row();
		return FALSE;
	}
	
	function load_uid($sid){
		$result = $this->db->query("SELECT uid FROM Submission WHERE sid=?", array($sid));
		if ($result->num_rows() == 0) return FALSE;
		return $result->row()->uid;		
	}
}
