<?php

class Notification_model extends My_Model
{
	private $table = 'notification';

	function __construct()
	{
		parent::__construct();
	}

	function newCount($user=NULL) {  
		$query = $this->db->query("SELECT count(*) FROM notification WHERE to_user = {$user} AND seen = 0");  
		return $query->row();  
	}

	function seen($id=0){
		$query = $this->db->query("UPDATE notification SET seen = 1 WHERE id ='".$id."'");  
		return $query->affected_rows();
	}

	function add($data){
		$this->save($table, $data);
	}

	function get_notification($to_user, $seen = FALSE){
		if ($seen) $seen = " AND seen = 0";
		$query = $this->db->query("SELECT * FROM notification WHERE to_user = {$to_user} ".$seen." order by date DESC ");  
		return $query->result();  
	}


}
?>