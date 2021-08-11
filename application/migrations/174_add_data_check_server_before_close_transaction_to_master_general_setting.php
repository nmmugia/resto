<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_data_check_server_before_close_transaction_to_master_general_setting extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
  public function up(){
    $check=$this->db->query("select * from master_general_setting where `name`='check_server_before_close_transaction'")->result();
    if(sizeof($check)==0){
      $this->db->insert("master_general_setting",array(
        "name"=>"check_server_before_close_transaction",
        "value"=>"0",
        "default"=>"0",
        "description"=>"check transaction data on server before closing cashier",
      ));      
    }
  }

  public function down(){
    $this->db->where("name","check_server_before_close_transaction");
    $this->db->delete("master_general_setting");
  }
}
