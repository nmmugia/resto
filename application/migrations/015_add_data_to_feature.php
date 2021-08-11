<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_data_to_feature extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
    public function up(){
    $check=$this->db->query("select*from feature where `key`='post_to_ready'")->result();
    if(sizeof($check)==0){
      $this->db->insert("feature",array(
        "name"=>"Post To Ready",
        "key"=>"post_to_ready"
      ));      
    }
  }

  public function down(){
    $this->db->where("key","post_to_ready");
    $this->db->delete("feature");
  }
}
