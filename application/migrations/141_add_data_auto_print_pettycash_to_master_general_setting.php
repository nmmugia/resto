<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_data_auto_print_pettycash_to_master_general_setting extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
	public function up(){
    $check=$this->db->query("select * from master_general_setting where `name`='auto_print_report_pettycash'")->result();
    if(sizeof($check)==0){
      $this->db->insert("master_general_setting",array(
        "name"=>"auto_print_report_pettycash",
        "value"=>0
      ));      
    }
  }

  public function down(){
    $this->db->where("name","auto_print_report_pettycash");
    $this->db->delete("master_general_setting");
  }
}
