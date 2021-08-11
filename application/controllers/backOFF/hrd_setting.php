<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/17/2014
 * Time: 8:27 PM
 */
class Hrd_setting extends Hrd_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) { 
            redirect(SITE_ADMIN . '/login', 'refresh');
        }else{
            $this->load->model('hrd_model');
            $this->load->model('categories_model');
            $this->load->library('encrypt');
            $this->_store_data = $this->ion_auth->user()->row();
        } 
    }

    public function index()
    {
        $this->data['title']    = "Setting Umum";
        $this->data['subtitle'] ="Setting Umum";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success'); 
        $payroll_periode=$this->hrd_model->get("hr_payroll_periode")->row();
        $salary_component=$this->hrd_model->get("hr_salary_component")->result();
        $components=array();
        foreach($salary_component as $s){
          if(in_array($s->key,array("late_1","late_2","permission_go_home","permission_alpha","alpha"))){
            $components[$s->key]=$s->formula_default;
          }
        }
        $max_late =  $this->input->post('max_late');
        $fingerprint_ip =  $this->input->post('fingerprint_ip');
        $cron_job_time =  $this->input->post('cron_job_time');
        $range_attendance =  $this->input->post('range_attendance');
        $bonus_point =  $this->input->post('bonus_point');
        $from =  $this->input->post('from');
        $to =  $this->input->post('to');
        $data_settings = $this->hrd_model->get_where('hr_setting', false);

        if (isset($_POST) && !empty($_POST)) {   
            foreach($components as $key=>$value){
              $val=$this->input->post($key);
              $this->hrd_model->update_where("hr_salary_component",array("formula_default"=>$val),array("key"=>$key));
            }
            if(!empty($max_late)){
                $this->hrd_model->update_where('hr_setting', array("value"=>$max_late), array('name' => "max_late"));     
            }

            if(!empty($fingerprint_ip)){
                $this->hrd_model->update_where('hr_setting', array("value"=>$fingerprint_ip), array('name' => "fingerprint_ip"));     
            }

            if(!empty($cron_job_time)){
                $this->hrd_model->update_where('hr_setting', array("value"=>$cron_job_time), array('name' => "cron_job_time"));     
            }
            
            if(!empty($range_attendance)){
                $this->hrd_model->update_where('hr_setting', array("value"=>$range_attendance), array('name' => "range_attendance"));     
            }
						$this->hrd_model->update_where('hr_setting', array("value"=>$bonus_point), array('name' => "bonus_point"));
						$component_bonus_point=$this->hrd_model->get_all_where("hr_salary_component",array("key"=>"bonus_point"));
						if(sizeof($component_bonus_point)>0){
							$component_bonus_point=$component_bonus_point[0];
							$this->hrd_model->update_where('hr_jobs_components',array("value"=>$bonus_point),array("component_id"=>$component_bonus_point->id));
						}
            if(sizeof($payroll_periode)>0){
              $this->hrd_model->save("hr_payroll_periode",array(
                "modified_by"=>$this->_store_data->id,
                "modified_at"=>date("Y-m-d"),
                "from"=>$from,
                "to"=>$to,
              ),$payroll_periode->id);
            }else{
              $this->hrd_model->save("hr_payroll_periode",array(
                "created_by"=>$this->_store_data->id,
                "created_at"=>date("Y-m-d"),
                "from"=>$from,
                "to"=>$to,
              ));
            }

            $advices =  $this->input->post('advices');
            $advice_day =  $this->input->post('advice_day');
            $advice_percentage =  $this->input->post('advice_percentage');
            $i = 0;
            if($advices!=""){
              foreach ($advices as $key => $value) { 
                  $data_update = array(
                      "total_days"=> $advice_day[$i],
                      "bonus"=>$advice_percentage[$i]
                  );
                  $this->hrd_model->update_where('hr_payroll_advice', $data_update, array('id' => $advices[$i]));
               $i++;  
              }               
            }
            redirect(SITE_ADMIN . '/hrd_setting/', 'refresh');
        }  
      
        foreach ($data_settings as $key => $value) {  
            $this->data[$value->name] = $value->value;
             
        } 
        $this->data['payroll_periode']=$payroll_periode;
        $this->data['payroll_advice'] = $this->hrd_model->get_where('hr_payroll_advice',false);
        $this->data['components']=$components;
        $this->data['content'] .= $this->load->view('admin/hrd/setting_view', $this->data, true);
        $this->render('hrd');
    } 
   
}