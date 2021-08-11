<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_data_voucher_method_and_printer_format_and_hrd_and_warehouse_printer extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
  public function up(){
    $check=$this->db->query("select*from master_general_setting where name='printer_warehouse'")->result();
    if(sizeof($check)==0){
      $this->db->insert("master_general_setting",array(
        "name"=>"printer_warehouse",
        "value"=>""
      ));      
    }
		$check=$this->db->query("select*from master_general_setting where name='printer_hrd'")->result();
    if(sizeof($check)==0){
      $this->db->insert("master_general_setting",array(
        "name"=>"printer_hrd",
        "value"=>""
      ));      
    }
		$check=$this->db->query("select*from master_general_setting where name='printer_format'")->result();
    if(sizeof($check)==0){
      $this->db->insert("master_general_setting",array(
        "name"=>"printer_format",
        "value"=>1
      ));      
    }
		$check=$this->db->query("select*from master_general_setting where name='voucher_method'")->result();
    if(sizeof($check)==0){
      $this->db->insert("master_general_setting",array(
        "name"=>"voucher_method",
        "value"=>1
      ));      
    }
  }

  public function down(){
    $this->db->where("name","printer_warehouse");
    $this->db->delete("master_general_setting");
		$this->db->where("name","printer_hrd");
    $this->db->delete("master_general_setting");
		$this->db->where("name","printer_format");
    $this->db->delete("master_general_setting");
		$this->db->where("name","voucher_method");
    $this->db->delete("master_general_setting");
  }
}
