<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_data_enum_option_flazz extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
    public function up(){
    $check=$this->db->query("select*from enum_payment_option where id=11")->result();
    if(sizeof($check)==0){
      $this->db->insert("enum_payment_option",array("value"=>"Flazz"));      
    }
  }

  public function down(){
    $this->db->where("id",11);
    $this->db->delete("enum_payment_option");
  }
}
