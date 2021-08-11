<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_data_enum_printer_type_printer_matrix_transfer extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
  
  public function up(){
    $check=$this->db->query("select * from enum_printer_type where `name_type`='printer_matrix_transfer'")->result();
    if(sizeof($check)==0){
      $this->db->insert("enum_printer_type",array(
        "name_type"=>"printer_matrix_transfer",
        "value"=>"Printer Dot Matrix Transfer Outlet"
      ));      
    }
  }

  public function down(){
    $this->db->where("name_type","printer_matrix_transfer");
    $this->db->delete("enum_printer_type");
  }
}
