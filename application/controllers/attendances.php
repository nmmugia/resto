<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Attendances extends CI_Controller {
  public $data=array();
  public $ip_fingerprint="192.168.1.201";
  public $max_late="";
  public $range_attendance="";
  public function __construct(){
    parent::__construct();
    $this->load->model("hrd_model");
    $data_settings = $this->hrd_model->get_where('hr_setting', array());
    if(!empty($data_settings)){
      foreach ($data_settings as $key => $value) { 
        if($value->name =="fingerprint_ip"){
         $this->ip_fingerprint = $value->value; 
        }else if($value->name =="max_late"){ 
          $this->max_late = $value->value; 
        }else if($value->name =="range_attendance"){ 
          $this->range_attendance = $value->value; 
        }
      }
    }
  }
  public function index()
  {
    $this->load->view("attendances",$this->data);
  }
  public function get_attendances()
  {
    $this->load->helper("attendances_helper");
    $results=fingerprint_attendances(array(
      "IP"=>$this->ip_fingerprint
    ));
    $lists=array();
    foreach($results as $r){
      if(strtotime(date("Y-m-d"))==strtotime($r['date']))
      {
        $employee=$this->hrd_model->get_one("users",$r['user_id']);
        if(sizeof($employee)>0 && $employee->active==1){
          $employee->date=$r['date'];
          $employee->time_in=$r['time_in'];
          $employee->time_out=$r['time_out'];
          array_push($lists,$employee);
        }        
      }
    }
    $this->data['lists']=$lists;
    $this->load->view("attendances_view",$this->data);
  }
}