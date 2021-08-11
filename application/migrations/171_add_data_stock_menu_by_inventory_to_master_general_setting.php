<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_data_stock_menu_by_inventory_to_master_general_setting extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
  public function up(){
    $check=$this->db->query("select * from master_general_setting where `name`='stock_menu_by_inventory'")->result();
    if(sizeof($check)==0){
      $this->db->insert("master_general_setting",array(
        "name"=>"stock_menu_by_inventory",
        "value"=>1
      ));      
    }
  }

  public function down(){
    $this->db->where("name","stock_menu_by_inventory");
    $this->db->delete("master_general_setting");
  }
}
