<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_data_enum_stock_history_status_refund extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
  
  public function up(){
    $check = $this->db->query("select * from enum_stock_history_status where `value` = 'Bertambah karena refund'")->result();
    if (sizeof($check) == 0){
      $this->db->insert("enum_stock_history_status",array(
        "value" => "Bertambah karena refund"
      ));      
    }
  }

  public function down(){
    $this->db->where("value","Bertambah karena refund");
    $this->db->delete("enum_stock_history_status");
  }
}
