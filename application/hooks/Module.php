<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Module extends CI_Controller {
	public function __construct() {
		parent::__construct();
        $this->load->library('session');
		$this->load->model("module_model");
	}
	
	/*public function check_module_allowed(){
		$this->CI->load->model("module_model");
		
		$condition = "due_date is not NULL ";
		$condition .= " AND ((due_date < '".date("Y-m-d")."' and is_installed = 1)";
		$condition .= " OR (due_date >= '".date("Y-m-d")."' and is_installed = 0))";
		
		$modules = $this->CI->module_model->get_all_where("module", $condition);
		
		foreach($modules as $module){
			if(date("Y-m-d") > $module->due_date && $module->is_installed == 1){
				$this->CI->module_model->save("module", array("is_installed" => 0), $module->id);
			} else if (date("Y-m-d") <= $module->due_date && $module->is_installed == 0){
				$this->CI->module_model->save("module", array("is_installed" => 1), $module->id);
			}
		}
	}*/
	
	public function get_module_expired(){
		$condition = array(
			"due_date <" => date("Y-m-d"),
		);
		
		$modules = $this->module_model->get_all_where("module", $condition);
		if($modules){
			$this->session->set_userdata('module_expired', $modules);
			echo "<script>console.log(".json_encode($this->session->userdata('module_expired')).");</script>";	
		} else {
			$this->session->set_userdata('module_expired', array());
		}
	}
	
	public function get_module_grace_period(){
		$condition = array(
			"due_date >=" => date("Y-m-d"),
			"DATE_SUB(due_date, INTERVAL reminder DAY) <" => date("Y-m-d"),
		);
		
		$modules = $this->module_model->get_all_where("module", $condition);
		if($modules){
			echo "<script>console.log(".json_encode($modules).");</script>";
			$this->session->set_userdata('grace_period', $modules);
		} else {
			$this->session->set_userdata('grace_period', array());
		}
	}
}