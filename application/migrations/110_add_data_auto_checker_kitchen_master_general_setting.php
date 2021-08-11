<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_data_auto_checker_kitchen_master_general_setting extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
  public function up(){
    $check=$this->db->query("select*from master_general_setting where `name`='auto_checker_kitchen'")->result();
    if(sizeof($check)==0){
      $this->db->insert("master_general_setting",array(
        "name"=>"auto_checker_kitchen",
        "value"=>1
      ));      
    }
  }

  public function down(){
    $this->db->where("name","auto_checker_kitchen");
    $this->db->delete("master_general_setting");
  }
}
