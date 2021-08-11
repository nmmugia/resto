<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_data_enum_printer_type extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
  
  public function up(){
    $check=$this->db->query("select * from enum_printer_type where `name_type`='printer_cashier'")->result();
    if(sizeof($check)==0){
      $this->db->insert("enum_printer_type",array(
        "name_type"=>"printer_cashier",
        "value"=>"Printer Kasir"
      ));      
    }

    $check=$this->db->query("select * from enum_printer_type where `name_type`='printer_kitchen'")->result();
    if(sizeof($check)==0){
      $this->db->insert("enum_printer_type",array(
        "name_type"=>"printer_kitchen",
        "value"=>"Printer Dapur"
      ));      
    }

    $check=$this->db->query("select * from enum_printer_type where `name_type`='printer_checker_service'")->result();
    if(sizeof($check)==0){
      $this->db->insert("enum_printer_type",array(
        "name_type"=>"printer_checker_service",
        "value"=>"Printer Checker/Service"
      ));      
    }

    $check=$this->db->query("select * from enum_printer_type where `name_type`='printer_checker_kitchen'")->result();
    if(sizeof($check)==0){
      $this->db->insert("enum_printer_type",array(
        "name_type"=>"printer_checker_kitchen",
        "value"=>"Printer Checker Dapur"
      ));      
    }

    $check=$this->db->query("select * from enum_printer_type where `name_type`='printer_reservation'")->result();
    if(sizeof($check)==0){
      $this->db->insert("enum_printer_type",array(
        "name_type"=>"printer_reservation",
        "value"=>"Printer Reservasi"
      ));      
    }

    $check=$this->db->query("select * from enum_printer_type where `name_type`='printer_matrix_po'")->result();
    if(sizeof($check)==0){
      $this->db->insert("enum_printer_type",array(
        "name_type"=>"printer_matrix_po",
        "value"=>"Printer Dot Matrix PO"
      ));      
    }

    $check=$this->db->query("select * from enum_printer_type where `name_type`='printer_matrix_reservation'")->result();
    if(sizeof($check)==0){
      $this->db->insert("enum_printer_type",array(
        "name_type"=>"printer_matrix_reservation",
        "value"=>"Printer Dot Matrix Reservasi"
      ));      
    }

    $check=$this->db->query("select * from enum_printer_type where `name_type`='printer_matrix_hrd'")->result();
    if(sizeof($check)==0){
      $this->db->insert("enum_printer_type",array(
        "name_type"=>"printer_matrix_hrd",
        "value"=>"Printer Dot Matrix HRD"
      ));      
    }
  }

  public function down(){
    $this->db->where("name_type","printer_cashier");
    $this->db->delete("enum_printer_type");
    $this->db->where("name_type","printer_kitchen");
    $this->db->delete("enum_printer_type");
    $this->db->where("name_type","printer_checker_service");
    $this->db->delete("enum_printer_type");
    $this->db->where("name_type","printer_checker_kitchen");
    $this->db->delete("enum_printer_type");
    $this->db->where("name_type","printer_reservation");
    $this->db->delete("enum_printer_type");
    $this->db->where("name_type","printer_matrix_po");
    $this->db->delete("enum_printer_type");
    $this->db->where("name_type","printer_matrix_reservation");
    $this->db->delete("enum_printer_type");
    $this->db->where("name_type","printer_matrix_hrd");
    $this->db->delete("enum_printer_type");
  }
}
