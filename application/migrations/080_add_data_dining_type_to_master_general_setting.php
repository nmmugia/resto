<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_data_dining_type_to_master_general_setting extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
	public function up(){
    $check=$this->db->query("select*from master_general_setting where `name`='dining_type'")->result();
    if(sizeof($check)==0){
      $this->db->insert("master_general_setting",array(
        "name"=>"dining_type",
        "value"=>"1"
      ));      
    }
  }

  public function down(){
    $this->db->where("name","dining_type");
    $this->db->delete("master_general_setting");
  }
}
