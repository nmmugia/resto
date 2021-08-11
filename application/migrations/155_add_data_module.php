<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_data_module extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
  
  public function up(){
    $check=$this->db->query("select * from module where `name`='CRM'")->result();
    if(sizeof($check)==0){
      $this->db->insert("module",array(
        "name"=>"CRM"
      ));      
    } 
  }

  public function down(){
    $this->db->where("name","CRM");
    $this->db->delete("module"); 
  }
}
