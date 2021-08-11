<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Target_model extends MY_Model
{
  function __construct()
  {
    parent::__construct();
  }
  function get($id=""){
    return $this->db->select("t.*,u.name")
    ->from("target t")
    ->join("users u","t.user_id=u.id")
    ->where("t.id",$id)
    ->get()->row();
  }
  function get_detail($id=""){
    return $this->db->select("td.*,m.menu_name")
    ->from("target_detail td")
    ->join("menu m","td.menu_id=m.id")
    ->where("td.target_id",$id)
    ->get()->result();
  }
}