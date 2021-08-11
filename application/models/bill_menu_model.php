<?php

class Bill_menu_model extends MY_Model
{
  function __construct()
  {
      parent::__construct();
  }
  function get_all_by_bill_id($bill_id=null)
  {
    return $this->db->select("*")
      ->from("bill_menu")
      ->where("bill_id",$bill_id)
      ->get()->result();
  }
}