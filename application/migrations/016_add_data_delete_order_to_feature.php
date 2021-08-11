<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_data_delete_order_to_feature extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
    public function up(){
    $check=$this->db->query("select*from feature where `key`='delete_order'")->result();
    if(sizeof($check)==0){
      $this->db->insert("feature",array(
        "name"=>"Delete Order",
        "key"=>"delete_order"
      ));      
    }
  }

  public function down(){
    $this->db->where("key","delete_order");
    $this->db->delete("feature");
  }
}
