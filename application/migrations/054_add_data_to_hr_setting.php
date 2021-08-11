<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_data_to_hr_setting extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
    public function up(){
    $check=$this->db->query("select*from hr_setting where `name`='bonus_point'")->result();
    if(sizeof($check)==0){
      $this->db->insert("hr_setting",array(
        "name"=>"bonus_point",
        "value"=>0
      ));      
    }
  }

  public function down(){
    $this->db->where("name","bonus_point");
    $this->db->delete("hr_setting");
  }
}
