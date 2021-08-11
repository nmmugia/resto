<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_data_reservation_to_enum_account_data_entry_type extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
	public function up(){
    $check=$this->db->query("select*from enum_account_data_entry_type where id=7")->result();
    if(sizeof($check)==0){
      $this->db->insert("enum_account_data_entry_type",array(
        "id"=>7,
        "value"=>"reservation",
				"created_at"=>date("Y-m-d H:i:s")
      ));      
    }
  }

  public function down(){
    $this->db->where("id",7);
    $this->db->delete("enum_account_data_entry_type");
  }
}
