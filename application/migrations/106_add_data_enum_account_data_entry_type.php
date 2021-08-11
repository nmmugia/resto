<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_data_enum_account_data_entry_type extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
  
  public function up(){
    $check = $this->db->query("select * from enum_account_data_entry_type where `value` = 'purchase_order_retur'")->result();
    if (sizeof($check) == 0){
      $this->db->insert("enum_account_data_entry_type",array(
        "value" => "purchase_order_retur"
      ));      
    }
  }

  public function down(){
    $this->db->where("value","purchase_order_retur");
    $this->db->delete("enum_account_data_entry_type");
  }
}
