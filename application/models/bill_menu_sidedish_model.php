<?php

class Bill_menu_sidedish_model extends MY_Model
{
  function __construct()
  {
      parent::__construct();
  }
  function get_all_by_bill_menu_id($bill_menu_id=null)
  {
    return $this->db->select("*")
      ->from("bill_menu_side_dish")
      ->where("bill_menu_id",$bill_menu_id)
      ->get()->result();
  }
}