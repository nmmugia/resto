<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_data_private_key_to_master_general_setting extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
  public function up(){
    $check=$this->db->query("select*from master_general_setting where name='private_key'")->result();
    if(sizeof($check)==0){
      $this->db->insert("master_general_setting",array(
        "name"=>"private_key",
        "value"=>""
      ));      
    }
  }

  public function down(){
    $this->db->where("name","private_key");
    $this->db->delete("master_general_setting");
  }
}
