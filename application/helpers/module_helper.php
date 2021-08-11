<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

function check_module_allowed(){
	$CI = get_instance();
	$CI->load->model("module_model");
	
	$condition = "due_date is not NULL ";
	$condition .= " AND ((due_date < '".date("Y-m-d")."' and is_installed = 1)";
	$condition .= " OR (due_date >= '".date("Y-m-d")."' and is_installed = 0))";
	
	$modules = $CI->module_model->get_all_where("module", $condition);
	
	foreach($modules as $module){
		if(date("Y-m-d") > $module->due_date && $module->is_installed == 1){
			$CI->module_model->save("module", array("is_installed" => 0), $module->id);
		} else if (date("Y-m-d") <= $module->due_date && $module->is_installed == 0){
			$CI->module_model->save("module", array("is_installed" => 1), $module->id);
		}
	}
}

function get_module_expired(){
	$CI = get_instance();
	$CI->load->model("module_model");
	$condition = array(
		"due_date <" => date("Y-m-d"),
		"due_date is not NULL" => NULL,
	);
	
	$modules = $CI->module_model->get_all_where("module", $condition);
	if($modules){
		$CI->session->set_userdata('module_expired', $modules);
	} else {
		$CI->session->set_userdata('module_expired', array());
	}
}

function get_module_grace_period(){
	$CI = get_instance();
	$CI->load->model("module_model");
	$condition = array(
		"due_date >=" => date("Y-m-d"),
		"DATE_SUB(due_date, INTERVAL reminder DAY) <" => date("Y-m-d"),
	);
	
	$modules = $CI->module_model->get_all_where("module", $condition);
	if($modules){
		$CI->session->set_userdata('grace_period', $modules);
	} else {
		$CI->session->set_userdata('grace_period', array());
	}
}