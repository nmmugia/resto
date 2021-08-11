<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_data_summary_sales_on_cashier_to_master_general_setting extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
  public function up(){
    $check=$this->db->query("select * from master_general_setting where `name`='summary_sales_on_cashier'")->result();
    if(sizeof($check)==0){
      $this->db->insert("master_general_setting",array(
        "name"=>"summary_sales_on_cashier",
        "value"=>0
      ));      
    }
  }

  public function down(){
    $this->db->where("name","summary_sales_on_cashier");
    $this->db->delete("master_general_setting");
  }
}
