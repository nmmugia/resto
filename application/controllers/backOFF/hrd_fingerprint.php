<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by BOSRESTO.
 * User: Azis
 * Date: 09/05/2016
 * Time: 11:30 AM
 */
class Hrd_fingerprint extends Hrd_Controller
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

            $data_settings = $this->hrd_model->get_where('hr_setting', array());
            
            $this->ip_fingerprint = "192.168.1.201";

            if(!empty($data_settings)){
               foreach ($data_settings as $key => $value) { 
                  if($value->name =="fingerprint_ip"){
                       $this->ip_fingerprint = $value->value; 
                  }else if($value->name =="max_late"){ 
                      $this->max_late = $value->value; 
                  }
                  else if($value->name =="range_attendance"){ 
                      $this->range_attendance = $value->value; 
                  }

               }
            }
        } 
    }

    public function index()
    {
           redirect(SITE_ADMIN . '/hrd_fingerprint/fingerprint_list', 'refresh');
    }
    public function fingerprint_list()
    {
        $this->data['title']    = "History Finger Print";
        $this->data['subtitle'] = "History Finger Print";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

       
        $this->data['data_url'] = base_url(SITE_ADMIN . '/hrd_fingerprint/get_data_fingerprint');
        $this->data['use_username'] = TRUE;
        $this->data['content'] .= $this->load->view('admin/hrd/fingerprint_list_view', $this->data, true);
        $this->render('hrd');
    }
    public function get_data_fingerprint()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));  
        $this->load->helper(array('hrd'));

        $post_array = array();
        parse_str($this->input->post('param'), $post_array);
        
        $start_date = $post_array['start_date'];
        $end_date = $post_array['end_date'];



        $date=date("Y-m-d");

            $this->datatables->select('u.name as name, hf.date as curdate, hf.time as time',false)
            ->from('hr_history_fingerprint hf') 
            ->join('users u','hf.user_id = u.id');


        if($start_date){
            $this->datatables->where('hf.date >= ', $start_date);
        }

        if($end_date){
            $this->datatables->where('hf.date <= ', $end_date);
        }   
            
        echo $this->datatables->generate();
    }
  }