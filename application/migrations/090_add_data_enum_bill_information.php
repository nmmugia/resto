<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_data_enum_bill_information extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
  
  public function up(){
    $check=$this->db->query("select * from enum_bill_information where `value`='promo'")->result();
    if(sizeof($check)==0){
      $this->db->insert("enum_bill_information",array(
        "value"=>"promo"
      ));      
    }

    $check_promo_cc=$this->db->query("select * from enum_bill_information where `value`='promo cc'")->result();
    if(sizeof($check_promo_cc)==0){
      $this->db->insert("enum_bill_information",array(
        "value"=>"promo cc"
      ));      
    }
  }

  public function down(){
    $this->db->where("value","promo");
    $this->db->delete("enum_bill_information");
    $this->db->where("value","promo cc");
    $this->db->delete("enum_bill_information");
  }
}
