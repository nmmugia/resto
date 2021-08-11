<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_data_convertion_process_to_enum_stock_history_status extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
    public function up(){
    $check=$this->db->query("select*from enum_stock_history_status where id=6")->result();
    if(sizeof($check)==0){
      $this->db->insert("enum_stock_history_status",array(
        "value"=>"Proses Konversi Inventory"
      ));      
    }
  }

  public function down(){
    $this->db->where("id","6");
    $this->db->delete("enum_stock_history_status");
  }
}
