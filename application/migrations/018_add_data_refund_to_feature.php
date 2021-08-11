<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_data_refund_to_feature extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
    public function up(){
    $check=$this->db->query("select*from feature where `key`='refund'")->result();
    if(sizeof($check)==0){
      $this->db->insert("feature",array(
        "name"=>"Refund",
        "key"=>"refund"
      ));      
    }
  }

  public function down(){
    $this->db->where("key","refund");
    $this->db->delete("feature");
  }
}
