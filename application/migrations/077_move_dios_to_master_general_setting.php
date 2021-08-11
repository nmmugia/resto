<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_move_dios_to_master_general_setting extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
	public function up(){
    $check=$this->db->query("select*from master_general_setting where `name`='node_server_ip'")->result();
    if(sizeof($check)==0){
      $this->db->insert("master_general_setting",array(
        "name"=>"node_server_ip",
        "value"=>"http://localhost"
      ));      
    }
		$check=$this->db->query("select*from master_general_setting where `name`='node_server_port'")->result();
    if(sizeof($check)==0){
      $this->db->insert("master_general_setting",array(
        "name"=>"node_server_port",
        "value"=>"4312"
      ));      
    }
		$check=$this->db->query("select*from master_general_setting where `name`='php_exe_path'")->result();
    if(sizeof($check)==0){
      $this->db->insert("master_general_setting",array(
        "name"=>"php_exe_path",
        "value"=>"C:/xampp/php/php.exe"
      ));      
    }
		$check=$this->db->query("select*from master_general_setting where `name`='notification'")->result();
    if(sizeof($check)==0){
      $this->db->insert("master_general_setting",array(
        "name"=>"notification",
        "value"=>0
      ));      
    }
		$check=$this->db->query("select*from master_general_setting where `name`='use_primary_additional_color'")->result();
    if(sizeof($check)==0){
      $this->db->insert("master_general_setting",array(
        "name"=>"use_primary_additional_color",
        "value"=>0
      ));      
    }
		$check=$this->db->query("select*from master_general_setting where `name`='primary_bg_color'")->result();
    if(sizeof($check)==0){
      $this->db->insert("master_general_setting",array(
        "name"=>"primary_bg_color",
        "value"=>"white"
      ));      
    }
		$check=$this->db->query("select*from master_general_setting where `name`='additional_bg_color'")->result();
    if(sizeof($check)==0){
      $this->db->insert("master_general_setting",array(
        "name"=>"additional_bg_color",
        "value"=>"lightgreen"
      ));      
    }
		$check=$this->db->query("select*from master_general_setting where `name`='takeaway_bg_color'")->result();
    if(sizeof($check)==0){
      $this->db->insert("master_general_setting",array(
        "name"=>"takeaway_bg_color",
        "value"=>"pink"
      ));      
    }
		$check=$this->db->query("select*from master_general_setting where `name`='target_print_list_menu'")->result();
    if(sizeof($check)==0){
      $this->db->insert("master_general_setting",array(
        "name"=>"target_print_list_menu",
        "value"=>1
      ));      
    }
		$check=$this->db->query("select*from master_general_setting where `name`='bill_auto_number'")->result();
    if(sizeof($check)==0){
      $this->db->insert("master_general_setting",array(
        "name"=>"bill_auto_number",
        "value"=>1
      ));      
    }
		$check=$this->db->query("select*from master_general_setting where `name`='stock_method'")->result();
    if(sizeof($check)==0){
      $this->db->insert("master_general_setting",array(
        "name"=>"stock_method",
        "value"=>"FIFO"
      ));      
    }
		$check=$this->db->query("select*from master_general_setting where `name`='site_title'")->result();
    if(sizeof($check)==0){
      $this->db->insert("master_general_setting",array(
        "name"=>"site_title",
        "value"=>"BOSRESTO"
      ));      
    }
		$check=$this->db->query("select*from master_general_setting where `name`='site_title_delimiter'")->result();
    if(sizeof($check)==0){
      $this->db->insert("master_general_setting",array(
        "name"=>"site_title_delimiter",
        "value"=>"-"
      ));      
    }
  }

  public function down(){
    $this->db->or_where("name","node_server_ip");
    $this->db->or_where("name","node_server_port");
    $this->db->or_where("name","php_exe_path");
    $this->db->or_where("name","notification");
    $this->db->or_where("name","use_primary_additional_color");
    $this->db->or_where("name","primary_bg_color");
    $this->db->or_where("name","additional_bg_color");
    $this->db->or_where("name","takeaway_bg_color");
    $this->db->or_where("name","target_print_list_menu");
    $this->db->or_where("name","bill_auto_number");
    $this->db->or_where("name","stock_method");
    $this->db->or_where("name","site_title");
    $this->db->or_where("name","site_title_delimiter");
    $this->db->delete("master_general_setting");
  }
}
