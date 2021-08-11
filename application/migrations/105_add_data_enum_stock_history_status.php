<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_data_enum_stock_history_status extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
  
  public function up(){
    $check = $this->db->query("select * from enum_stock_history_status where `value` = 'Retur Pembelian'")->result();
    if (sizeof($check) == 0){
      $this->db->insert("enum_stock_history_status",array(
        "value" => "Retur Pembelian"
      ));      
    }
  }

  public function down(){
    $this->db->where("value","Retur Pembelian");
    $this->db->delete("enum_stock_history_status");
  }
}
