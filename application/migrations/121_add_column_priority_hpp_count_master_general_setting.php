<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_priority_hpp_count_master_general_setting extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
  public function up(){
    $check=$this->db->query("select*from master_general_setting where `name`='priority_cogs_count'")->result();
    if(sizeof($check)==0){
      $this->db->insert("master_general_setting",array(
        "name"=>"priority_cogs_count",
        "value"=>'0'
      ));      
    }
  }

  public function down(){
    $this->db->where("name","priority_cogs_count");
    $this->db->delete("master_general_setting");
  }
}
