<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_data_use_checker_group_to_master_general_setting extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
	public function up(){
    $check=$this->db->query("select*from master_general_setting where `name`='checker_group'")->result();
    if(sizeof($check)==0){
      $this->db->insert("master_general_setting",array(
        "name"=>"checker_group",
        "value"=>0
      ));      
    }
  }

  public function down(){
    $this->db->where("name","checker_group");
    $this->db->delete("master_general_setting");
  }
}
