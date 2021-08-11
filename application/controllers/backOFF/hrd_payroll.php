<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/17/2014
 * Time: 8:27 PM
 */
class Hrd_payroll extends Hrd_Controller
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
						$this->data['data_store']=$this->hrd_model->get_one("store",$this->data['setting']['store_id']);
        } 
    }

    public function slip()
    {
        $this->data['title']    = "Slip Gaji";
        $this->data['subtitle'] ="Slip Gaji Bulan ".date('F Y');

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

       
        $this->data['data_url'] = base_url(SITE_ADMIN . '/hrd_payroll/get_payroll_history_this_month');
        

        $this->data['all_jobs'] = $this->hrd_model->get_all_jobs();
        $this->data['all_employees']=$this->hrd_model->get_all_where("users",array("active"=>1,"name !="=>$this->config->config['sync_user_username']));
        $this->data['all_employee_affairs'] = $this->hrd_model->get_all_employee_affair();
        //load content
        $this->data['use_username'] = TRUE;
        $this->data['content'] .= $this->load->view('admin/hrd/payroll_slip_list', $this->data, true);
        $this->render('hrd');
    }

    public function get_employees_for_payroll()
    {
      $periode=$this->input->post("periode");
      $job_id=$this->input->post("job_id");
      $status_id=$this->input->post("status_id");
      $data_jobs =  ($job_id!="" ? $job_id : "");
      $data_status = ($status_id!="" ? $status_id : "");
      $data_users_jobs = $this->hrd_model->get_user_job($data_status,$data_jobs);
      $content="";
      foreach($data_users_jobs as $d){
        $content.="<option value='".$d->employee_id."'>".$d->name."</option>";
      }
      echo json_encode(array(
        "content"=>$content
      ));
    }

    public function get_payroll_history_this_month()
    {
     
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));  
        $user_id = $this->uri->segment(4);
        $this->datatables->select('a.user_id,e.name,a.id,c.store_name, d.jobs_name,a.period,
          (sum(IF(hsc.key not in ("late_1","late_2","permission_go_home","permission_alpha","alpha") ,b.value * hsc.is_enhancer,0))-
          IF(
            sum(IF(hsc.key in ("late_1","late_2","permission_go_home","permission_alpha","alpha") ,b.value * hsc.is_enhancer*-1,0))>
            (select hr_detail_payroll_history.`value` from hr_detail_payroll_history inner join hr_salary_component on hr_salary_component.id=hr_detail_payroll_history.component_id where payroll_history_id=a.id and `key`="insentif"),
            (select hr_detail_payroll_history.`value` from hr_detail_payroll_history inner join hr_salary_component on hr_salary_component.id=hr_detail_payroll_history.component_id where payroll_history_id=a.id and `key`="insentif"),
            sum(IF(hsc.key in ("late_1","late_2","permission_go_home","permission_alpha","alpha") ,b.value * hsc.is_enhancer*-1,0))
          )) as payroll_total
        ',false)
          ->from('hr_payroll_history a')
          ->join('hr_detail_payroll_history b', 'a.id = b.payroll_history_id')  
          ->join('hr_salary_component hsc', 'hsc.id = b.component_id')  
          ->join('hr_jobs d ', 'd.id = a.jobs_id')  
          ->join('store c ', 'c.id = d.store_id')
          ->join('users e ', 'a.user_id = e.id') 
          ->group_by('a.user_id')
          ->where('a.period = concat(DATE_FORMAT(NOW(),"%m"),"-",DATE_FORMAT(NOW(),"%Y"))') 
          ->add_column('view', "<div class='btn-group'>
          <a href='" . base_url(SITE_ADMIN . '/hrd_payroll/detail_payroll_history/$1')."'  class='btn btn-default view-payroll-history'  payroll-history-id='$1'><i class='fa fa-pencil'></i> View</a>
         
        </div>", 'id')
           ->add_column('actions', "<div class='btn-group'>
          
        </div>", 'id');  
        echo $this->datatables->generate();
    }

    public function get_payroll_history()
    {
     
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));  
         $user_id = $this->uri->segment(4);
         $user=$this->hrd_model->get_one("users",$user_id);
        $this->datatables->select('e.name,a.id,c.store_name, d.jobs_name,a.period,
          (sum(IF(hsc.key not in ("late_1","late_2","permission_go_home","permission_alpha","alpha") ,b.value * hsc.is_enhancer,0))-
          IF(
            sum(IF(hsc.key in ("late_1","late_2","permission_go_home","permission_alpha","alpha") ,b.value * hsc.is_enhancer*-1,0))>
            (select hr_detail_payroll_history.`value` from hr_detail_payroll_history inner join hr_salary_component on hr_salary_component.id=hr_detail_payroll_history.component_id where payroll_history_id=a.id and `key`="insentif"),
            (select hr_detail_payroll_history.`value` from hr_detail_payroll_history inner join hr_salary_component on hr_salary_component.id=hr_detail_payroll_history.component_id where payroll_history_id=a.id and `key`="insentif"),
            sum(IF(hsc.key in ("late_1","late_2","permission_go_home","permission_alpha","alpha") ,b.value * hsc.is_enhancer*-1,0))
          )) as payroll_total
        ',false)
            ->from('hr_payroll_history a')
            ->join('hr_detail_payroll_history b', 'a.id = b.payroll_history_id')  
            ->join('hr_salary_component hsc', 'hsc.id = b.component_id')  
            ->join('hr_jobs d ', 'd.id = a.jobs_id')  
             ->join('store c ', 'c.id = d.store_id')
             ->join('users e ', 'a.user_id = e.id') 
            ->group_by('a.period')
            ->where('e.id ', $user_id) 
              ->add_column('view', "<div class='btn-group'>
            <a href='" . base_url(SITE_ADMIN . '/hrd_payroll/detail_payroll_history/$1')."'  class='btn btn-default view-payroll-history'  payroll-history-id='$1'><i class='fa fa-pencil'></i> View</a>
         
        </div>", 'id')
           ->add_column('actions', ($user->active==1 ? "<div class='btn-group'>
            <a href='" . base_url(SITE_ADMIN . '/hrd_payroll/edit_payroll_history/$1')."' class='btn btn-default edit-payroll-history'  payroll-history-id='$1'><i class='fa fa-pencil'></i> Edit</a>
        </div>" : ""), 'id');  
        // <a href='" . base_url(SITE_ADMIN . '/hrd_payroll/delete_payroll/$1')."' class='btn btn-danger deleteNow ' rel='History Payroll'  payroll-history-id='$1'><i class='fa fa-trash-o'></i> Delete</a>
        echo $this->datatables->generate();
    }

    public function add_payroll_history(){
        $id = $this->uri->segment(4);

        $this->data['subtitle']  = "Tambah History Gaji";

        if (empty($id)) {
            redirect(SITE_ADMIN . '/hrd_staff/detail_staff/'.$id.'#payroll');
        } 
        $form_data = $this->hrd_model->get_last_jobs_user($id); 


        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/hrd_staff/detail_staff/'.$id.'#payroll');
        }
        $save = false;
        if (isset($_POST) && ! empty($_POST)) { 
            $period =  $this->input->post('period');
            $employee_id =  $this->input->post('employee_id');
            $jobs_id =  $this->input->post('jobs_id');
            $job_history_id =  $this->input->post('job_history_id');

            $where_checking_payroll = array("user_id"=>$employee_id,"period"=>$period);
            $checking_period = $this->hrd_model->checking_payroll($where_checking_payroll);
            
            if(!$checking_period){
                //insert payroll per period
                $data_payroll = array(
                                    "user_id" => $employee_id,
                                    "jobs_id" => $jobs_id,
                                    "job_history_id" => $job_history_id,
                                    "period" => $period
                                );
 
                $insert_payroll  = $this->categories_model->save("hr_payroll_history",$data_payroll);

                if($insert_payroll){ 
                    //insert payroll detail
                    $data_enhancer = (!empty($_POST['enhancer']))?$_POST['enhancer']:array();
                    $data_subtrahend =  (!empty($_POST['subtrahend']))?$_POST['subtrahend']:array();
 
                    $result = array_merge($data_enhancer, $data_subtrahend); 
                    if(!empty($result)){ 
                        $array = array();
                        foreach ($result as $data) {
                            if(isset($data['quantity']) && ($data['quantity'] != 0 || $data['quantity'] != null) ){
                                 $array = array('payroll_history_id' => $insert_payroll,
                                       'component_id' => $data['component_id'],
                                       'value' => $data['quantity']
                                ); 
                                $save = $this->categories_model->save('hr_detail_payroll_history', $array); 
                            }
                           
                        } 
                    }  
                }

                if ($save === false) {
                    $this->session->set_flashdata('message', 'Gagal Menyimpan History Gaji');
                }
                else {
                    $insert_pinjaman = $this->insert_loan_auto($employee_id,$period);
                    if($insert_pinjaman){
                        $this->session->set_flashdata('message_success', 'Berhasil Menyimpan History Gaji');    
                    }else{
                        $this->session->set_flashdata('message', 'Gagal Menyimpan History Pinjaman');
                    }
                    
                }
                
            }else{ 
                $this->session->set_flashdata('message', 'Data Period Sudah Ada'.$employee_id);
            } 
          


            $btnaction = $this->input->post('btnAction');
            if ($btnaction == 'save_exit') {
                redirect(SITE_ADMIN . '/hrd_staff/detail_staff/'.$id.'#payroll');
            }
            else {
                redirect(SITE_ADMIN . '/hrd/set_salary_component/' . $id, 'refresh');
            }

        }else{  
            $this->data['form_data'] = $form_data; 
            $this->data['employee_id'] = $id;  

            $this->data['data_enhancer_salary_component']  = $this->hrd_model->get_salary_component(array('is_enhancer'=>1,'is_static'=>0));
            $this->data['data_substrahend_salary_component']  = $this->hrd_model->get_salary_component(array('is_enhancer'=>-1,'is_static'=>0));

            // $total_pinjaman = 0;
            // $total_pinjaman = $this->calculate_loan($id); 
 
            // $this->data['total_pinjaman'] = $total_pinjaman; 

            $this->data['content'] .= $this->load->view('admin/hrd/add_payroll_history_view.php', $this->data, true);

            $this->render('hrd');
        } 
    }

    public function edit_payroll_history(){
        $id = $this->uri->segment(4);

        $this->data['subtitle']  = "Tambah History Gaji";

        if (empty($id)) {
            redirect(SITE_ADMIN . '/hrd_staff/detail_staff/'.$id.'#payroll');
        } 
        $form_data = $this->hrd_model->get_detail_payroll($id); 


        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/hrd_staff/detail_staff/'.$id.'#payroll');
        }
        $save = false;
        if (isset($_POST) && ! empty($_POST)) { 
            $period =  $this->input->post('period');
            $employee_id =  $this->input->post('employee_id');
            $jobs_id =  $this->input->post('jobs_id');
            $payroll_id =  $this->input->post('payroll_id');

            $status_delete = $this->hrd_model->delete_detail_payroll(array('payroll_history_id' => $payroll_id));

            $data_enhancer = (!empty($_POST['enhancer']))?$_POST['enhancer']:array();
            $data_subtrahend =  (!empty($_POST['subtrahend']))?$_POST['subtrahend']:array(); 

            $result = array_merge($data_enhancer, $data_subtrahend); 
            if(!empty($result)){ 
                $array = array();
               
                foreach ($result as $data) {
                    if(isset($data['quantity']) && ($data['quantity'] != 0 || $data['quantity'] != null) ){
                         $array = array('payroll_history_id' => $payroll_id,
                               'component_id' => $data['component_id'],
                               'value' => $data['quantity']
                        ); 
                        $save = $this->categories_model->save('hr_detail_payroll_history', $array); 
                    } 
                }  
            }  

            if ($save === false) {
                $this->session->set_flashdata('message', 'Gagal Menyimpan History Gaji');
            }
            else {
                $this->session->set_flashdata('message_success', 'Berhasil Menyimpan History Gaji');
            }
                 
            $btnaction = $this->input->post('btnAction');
            if ($btnaction == 'save_exit') {
                redirect(SITE_ADMIN . '/hrd_staff/detail_staff/'.$form_data->user_id.'#payroll');
            }
            else {
                redirect(SITE_ADMIN . '/hrd/set_salary_component/' . $id, 'refresh');
            }

        }else{
            
            
            $this->data['form_data'] = $form_data;  
            $this->data['enhancer_sal_component_dropdwn'] = $this->hrd_model->get_salary_component_dropdown(array('is_enhancer'=>1)); 
            $this->data['substrahend_sal_component_dropdwn'] = $this->hrd_model->get_salary_component_dropdown(array('is_enhancer'=>-1)); 
                

            $this->data['data_enhancer_jobs_component'] = $this->hrd_model->get_detail_payroll_history(array("payroll_history_id"=>$id,"is_enhancer"=>1));
            $this->data['data_subtrahend_jobs_component'] = $this->hrd_model->get_detail_payroll_history(array("payroll_history_id"=>$id,"is_enhancer"=>-1)); 

            $this->data['data_enhancer_salary_component']  = $this->hrd_model->get_salary_component(array('is_enhancer'=>1));
            $this->data['data_substrahend_salary_component']  = $this->hrd_model->get_salary_component(array('is_enhancer'=>-1));

            $this->data['content'] .= $this->load->view('admin/hrd/edit_payroll_history_view.php', $this->data, true);

            $this->render('hrd');
        } 
    }

    public function detail_payroll_history(){
        $id = $this->uri->segment(4);

        $this->data['subtitle']  = "Detail Gaji";

        if (empty($id)) {
            redirect(SITE_ADMIN . '/hrd_staff/detail_staff/'.$id.'#payroll');
        } 
        $form_data = $this->hrd_model->get_detail_payroll($id); 


        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/hrd_staff/detail_staff/'.$id.'#payroll');
        }
        $save = false;
        if (isset($_POST) && ! empty($_POST)) { 
            $period =  $this->input->post('period');
            $employee_id =  $this->input->post('employee_id');
            $jobs_id =  $this->input->post('jobs_id');
            $payroll_id =  $this->input->post('payroll_id');
            

            $status_delete = $this->hrd_model->delete_detail_payroll(array('payroll_history_id' => $payroll_id));
           

            $data_enhancer = (!empty($_POST['enhancer']))?$_POST['enhancer']:array();
            $data_subtrahend =  (!empty($_POST['subtrahend']))?$_POST['subtrahend']:array(); 

            $result = array_merge($data_enhancer, $data_subtrahend); 
            if(!empty($result)){ 
                $array = array();
               
                foreach ($result as $data) {
                    if(isset($data['quantity']) && ($data['quantity'] != 0 || $data['quantity'] != null) ){
                         $array = array('payroll_history_id' => $payroll_id,
                               'component_id' => $data['component_id'],
                               'value' => $data['quantity']
                        ); 
                        $save = $this->categories_model->save('hr_detail_payroll_history', $array); 
                    }
                   
                } 

                
            }  

            if ($save === false) {
                $this->session->set_flashdata('message', 'Gagal Menyimpan History Gaji');
            }
            else {
                $this->session->set_flashdata('message_success', 'Berhasil Menyimpan History Gaji');
            }
                
             


            $btnaction = $this->input->post('btnAction');
            if ($btnaction == 'save_exit') {
                redirect(SITE_ADMIN . '/hrd_staff/detail_staff/'.$form_data->user_id.'#payroll');
            }
            else {
                redirect(SITE_ADMIN . '/hrd/set_salary_component/' . $id, 'refresh');
            }

        }else{  
            $this->data['form_data'] = $form_data;   
            $temp=explode("-",$form_data->period);
            $month=$temp[0];
            $year=$temp[1];
            $payroll_periode=$this->hrd_model->get("hr_payroll_periode")->row();
            
            if(sizeof($payroll_periode)>0){
              $temp2=date("Y-m-d",strtotime($year."-".$month."-01 -1 month"));
              $start_date=date("Y-m",strtotime($temp2))."-".$payroll_periode->from;
              $end_date=$year."-".$month."-".$payroll_periode->to;
            }else{
              $start_date=$year."-".$month."-01";
              $end_date=date("Y-m-t",strtotime($year."-".$month."-01"));
            } 

            $periode=$temp[1]."-".$temp[0]."-01";
          
            $params=array(
              "jobs"=>"",
              "status"=>"",
              "periode"=>$form_data->period,
              "id"=>$form_data->id
            );
            $lists=$this->hrd_model->get_print_payroll_history($params);
            $detail=array();
            foreach($lists as $l){
              $payroll_static_data=$this->hrd_model->get_payroll_static_data(array("user_id"=>$l->employee_id,"start_date"=>$start_date,"end_date"=>$end_date));
              if(!isset($detail[$l->employee_id])){
                $detail[$l->employee_id]=array(
                  "data"=>$l,
                  "payroll_static_data"=>$payroll_static_data,
                  "detail"=>array()
                );
              }
              array_push($detail[$l->employee_id]['detail'],$l);
            }
          
            $this->data['detail']=$detail;
            $this->data['periode']=$periode;
           
            $this->data['content'] .= $this->load->view('admin/hrd/detail_payroll_history_view.php', $this->data, true);
            $this->render('hrd');
        } 
    }

    public function delete_payroll()
    {
        $id       = $this->uri->segment(4);
        if(!isset($id) || empty($id)){
             redirect(SITE_ADMIN . '/hrd_staff/detail_staff/'.$id, 'refresh'.'#payroll');
        }else{
            $data_payroll = $this->hrd_model->get_one('hr_payroll_history',$id);
             $result = $this->hrd_model->delete_detail_payroll(array("payroll_history_id"=>$id));
             if($result){
                $data = $this->hrd_model->delete_payroll(array("id"=>$id));
                if ($data) {
                    $this->session->set_flashdata('message_success', $this->lang->line('success_delete'));
                }
                else {
                    $this->session->set_flashdata('message', $this->lang->line('error_delete'));
                }
             }else{
                $this->session->set_flashdata('message', $this->lang->line('error_delete'));
             }
        }
       
            
        redirect(SITE_ADMIN . '/hrd_staff/detail_staff/'.$data_payroll->user_id.'#payroll', 'refresh');
    }

    public function download_slip(){
        $period =  $this->input->post('periode');
        $jobs =  $this->input->post('jobs_id');
        $status =  $this->input->post('status_id');
        $employees =  $this->input->post('employees');
 
        $data_jobs =  "";
        $data_status = "";
        
        $data = $this->hrd_model->get_jobs_payroll_history($period,$data_status,$data_jobs);
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");

        // force download  
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream; charset=utf-8");
        header("Content-Type: application/download");

        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename=Slip Gaji $period .csv");
        header("Content-Transfer-Encoding: binary");
        ob_start();
        $df = fopen("php://output", 'w');
        echo "\xEF\xBB\xBF";// UTF-8 BOM. [trick]
        fputcsv($df, array("Nama","Kantor","Jabatan","Period","Jumlah"));
        foreach ($data as $row) { 
          if(in_array($row->id,$employees)){
            fputcsv($df, array($row->name,$row->store_name,$row->jobs_name,$row->period,$row->payroll_total));
          }
        }
        fclose($df);
        
        echo ob_get_clean(); 
    } 

    public function print_slip(){
      $period =  $this->input->post('periode');
      $jobs =  $this->input->post('jobs_id');
      $status =  $this->input->post('status_id');
      $employees =  $this->input->post('employees');
      $temp=explode("-",$period);
      $month=$temp[0];
      $year=$temp[1];
      $payroll_periode=$this->hrd_model->get("hr_payroll_periode")->row();
      if(sizeof($payroll_periode)>0){
        $temp2=date("Y-m-d",strtotime($year."-".$month."-01 -1 month"));
        $start_date=date("Y-m",strtotime($temp2))."-".$payroll_periode->from;
        $end_date=$year."-".$month."-".$payroll_periode->to;
      }else{
        $start_date=$year."-".$month."-01";
        $end_date=date("Y-m-t",strtotime($year."-".$month."-01"));
      }
      
      // $return_data = array();
      // $return_data['status'] = true;
      // $return_data['data'] = array();
      // $return_data['message'] = ""; 

      if(empty($period)){
        redirect(SITE_ADMIN."hrd_payroll/slip");
      }
      $temp=explode("-",$period);
      $periode=$temp[1]."-".$temp[0]."-01";
      $data_jobs =  "" ;
      $data_status = "";
      $params=array(
        "jobs"=>$data_jobs,
        "status"=>$data_status,
        "periode"=>$period
      );
      $lists=$this->hrd_model->get_print_payroll_history($params);
      $detail=array();
      foreach($lists as $l){
        if(in_array($l->employee_id,$employees)){
          $payroll_static_data=$this->hrd_model->get_payroll_static_data(array("user_id"=>$l->employee_id,"start_date"=>$start_date,"end_date"=>$end_date));
          if(!isset($detail[$l->employee_id])){
            $detail[$l->employee_id]=array(
              "data"=>$l,
              "payroll_static_data"=>$payroll_static_data,
              "detail"=>array()
            );
          }
          array_push($detail[$l->employee_id]['detail'],$l);          
        }
      }
      $this->data['detail']=$detail;
      $this->data['periode']=$periode;
      $this->data['subtitle']  = "Cetak Slip Gaji";
      $this->data['content'] .= $this->load->view('admin/hrd/print_payroll_view.php', $this->data, true);
      $this->render('hrd');
    }

    public function generate_slip(){
        $period =  $this->input->post('periode');
        $jobs =  $this->input->post('jobs_id');
        $status =  $this->input->post('status_id');
        $employees =  $this->input->post('employees');

        $temp=explode("-",$period);
        $month=$temp[0];
        $year=$temp[1];
        $payroll_periode=$this->hrd_model->get("hr_payroll_periode")->row();
        if(sizeof($payroll_periode)>0){
          $temp2=date("Y-m-d",strtotime($year."-".$month."-01 -1 month"));
          $start_date=date("Y-m",strtotime($temp2))."-".$payroll_periode->from;
          $end_date=$year."-".$month."-".$payroll_periode->to;
        }else{
          $start_date=$year."-".$month."-01";
          $end_date=date("Y-m-t",strtotime($year."-".$month."-01"));
        }
        if(empty($period)){
            $return_data['status'] = false;
            $return_data['message'] = "Parameter period Kosong";  
        }
        $data_jobs =  "";
        $data_status = "";
        $data_users_jobs = $this->hrd_model->get_user_job($data_status,$data_jobs);
        // echo "<pre>";
        foreach ($data_users_jobs as $data) {  
          if(in_array($data->employee_id,$employees)){
            $payroll_history =  $this->hrd_model->get_where('hr_payroll_history',array(
                "job_history_id"=>$data->job_history_id,
                 "period"=>$period
            ));
            if(!empty($payroll_history)){ 
                foreach ($payroll_history as $detail) {
                    $payroll_history_id = $detail->id;
                    $result = $this->hrd_model->delete_detail_payroll(array("payroll_history_id"=>$payroll_history_id));  
                    if($result) $remove_payroll_history = $this->hrd_model->delete_payroll(array("id"=>$payroll_history_id));  
                }  
            } 
             
             //insert payroll_history
            $data_payroll = array(
                "user_id" => $data->employee_id,
                "jobs_id" => $data->jobs_id,
                "job_history_id" => $data->job_history_id,
                "period" => $period
            );
            
             
            $insert_payroll  = $this->categories_model->save("hr_payroll_history",$data_payroll);
            if($insert_payroll){
                //insert detail payroll history
                $jobs_component = $this->hrd_model->get_jobs_component_by(array("job_id"=>$data->jobs_id));
                // echo "<pre>";
                // print_r($jobs_component);
                // exit;
                //STATIC PENGURANG INSENTIF
                $payroll_static_data=$this->hrd_model->get_payroll_static_data(array("user_id"=>$data->employee_id,"start_date"=>$start_date,"end_date"=>$end_date));
                // print_r($payroll_static_data);
                
                //HITUNG PINJAMAN CICILAN  
                foreach ($jobs_component as $component) {
                    if($component->key=='late_1'){
                      $component->value=$payroll_static_data['late_1']*$component->formula_default*$payroll_static_data['insentive'] /100;
                    }elseif($component->key=='late_2'){
                      $component->value=$payroll_static_data['late_2']*$component->formula_default*$payroll_static_data['insentive'] /100;
                    }elseif($component->key=='permission_go_home'){
                      $component->value=$payroll_static_data['permission_go_home']*$component->formula_default*$payroll_static_data['insentive'] /100;
                    }elseif($component->key=='permission_alpha'){
                      $component->value=$payroll_static_data['permission_alpha']*$component->formula_default*$payroll_static_data['insentive'] /100;
                    }elseif($component->key=='alpha'){
                      $component->value=$payroll_static_data['alpha']*$component->formula_default*$payroll_static_data['insentive'] /100;
										}elseif($component->key=='daily_allowance'){
											$component->value=$payroll_static_data['present']*$component->value;
										}elseif($component->key=='weekly_wage'){
											$component->value=$payroll_static_data['present_sunday']*$component->value;
                    }elseif($component->key=='reward'){
                      $component->value=$this->insert_auto_reward($data->employee_id,$month,$year);
                    }elseif($component->key == "pinjaman"){ //jika pinjaman
                      $component->value = $this->calculate_loan($data->employee_id);  
                    }
                    $array = array(
                      'payroll_history_id' => $insert_payroll,
                      'component_id' => $component->component_id,
                      'value' => $component->value
                    ); 
                    $save = $this->categories_model->save('hr_detail_payroll_history', $array); 
                } 
            } 
             
            $insert_pinjaman = $this->insert_loan_auto($data->employee_id,$period);
          }
        }
        redirect(SITE_ADMIN."/hrd_payroll/slip");
        // echo json_encode($return_data);
    }

    public function generate_single_slip(){
      $period =  $this->input->post('periode');
      $employee_id =  $this->input->post('employee_id');
      $job_id =  $this->input->post('job_id');
      $job_history_id =  $this->input->post('job_history_id');
      $temp=explode("-",$period);
      $month=$temp[0];
      $year=$temp[1];
      $payroll_periode=$this->hrd_model->get("hr_payroll_periode")->row();
      if(sizeof($payroll_periode)>0){
        $temp2=date("Y-m-d",strtotime($year."-".$month."-01 -1 month"));
        $start_date=date("Y-m",strtotime($temp2))."-".$payroll_periode->from;
        $end_date=$year."-".$month."-".$payroll_periode->to;
      }else{
        $start_date=$year."-".$month."-01";
        $end_date=date("Y-m-t",strtotime($year."-".$month."-01"));
      }
      $jobs_component = $this->hrd_model->get_jobs_component_by(array("job_id"=>$job_id));
      $payroll_static_data=$this->hrd_model->get_payroll_static_data(array("user_id"=>$employee_id,"start_date"=>$start_date,"end_date"=>$end_date));
      //HITUNG PINJAMAN CICILAN  
      $employee_component=array();
      $total_pinjaman = 0;
      foreach ($jobs_component as $component) {
        if($component->key=='late_1'){
          $component->value=$payroll_static_data['late_1']*$component->formula_default*$payroll_static_data['insentive'] /100;
        }elseif($component->key=='late_2'){
          $component->value=$payroll_static_data['late_2']*$component->formula_default*$payroll_static_data['insentive'] /100;
        }elseif($component->key=='permission_go_home'){
          $component->value=$payroll_static_data['permission_go_home']*$component->formula_default*$payroll_static_data['insentive'] /100;
        }elseif($component->key=='permission_alpha'){
          $component->value=$payroll_static_data['permission_alpha']*$component->formula_default*$payroll_static_data['insentive'] /100;
        }elseif($component->key=='alpha'){
          $component->value=$payroll_static_data['alpha']*$component->formula_default*$payroll_static_data['insentive'] /100;
				}elseif($component->key=='daily_allowance'){
					$component->value=$payroll_static_data['present']*$component->value;
				}elseif($component->key=='weekly_wage'){
					$component->value=$payroll_static_data['present_sunday']*$component->value;
        }elseif($component->key=='reward'){
          $component->value=$this->insert_auto_reward($employee_id,$month,$year);
        }elseif($component->key == "pinjaman"){ //jika pinjaman
          $component->value = $this->calculate_loan($employee_id);  
          $total_pinjaman=$component->value;
        }
        $employee_component[$component->component_id]=$component->value;
      }
      $this->data['employee_component'] = $employee_component; 
      $this->data['total_pinjaman'] = $total_pinjaman; 
      $this->data['enhancer_sal_component_dropdwn'] = $this->hrd_model->get_salary_component_dropdown(array('is_enhancer'=>1)); 
      $this->data['substrahend_sal_component_dropdwn'] = $this->hrd_model->get_salary_component_dropdown(array('is_enhancer'=>-1)); 
      $this->data['data_enhancer_jobs_component'] = $this->hrd_model->get_jobs_component_by(array("job_id"=>$job_id,"is_enhancer"=>1));
      $this->data['data_subtrahend_jobs_component'] = $this->hrd_model->get_jobs_component_by(array("job_id"=>$job_id,"is_enhancer"=>-1)); 
      $content=$this->load->view("admin/hrd/add_payroll_history_view_content",$this->data,true);
      echo json_encode(array(
        "content"=>$content
      ));
    }

    public function insert_auto_reward($user_id="",$month="",$year=""){
      $this->load->model("report_model");
      $achievements=$this->report_model->achievement_waiter(array("user_id"=>$user_id,"month"=>$month,"year"=>$year));
      $total_reward=0;
      foreach($achievements as $d){
        if($d->target_type==1 && $d->achievement_by_total>=$d->target_by_total){
          $total_reward+=($d->is_percentage==1 ? $d->reward*$d->achievement_by_total/100 : $d->reward );
        }
        if($d->target_type==2 && $d->achievement_by_item==0){
          $total_reward+=($d->is_percentage==1 ? $d->reward*$d->achievement_by_item_total/100 : $d->reward );
        }
      }
      $reward_kitchen=$this->report_model->get_all_where("reward_kitchen",array());
      if(sizeof($reward_kitchen)>0){
        $reward_kitchen=$reward_kitchen[0];
        $reward=$this->report_model->get_reward_kitchen(array("month"=>$month,"year"=>$year,"reward_kitchen"=>$reward_kitchen));
        if($reward>0){
          $users=$this->store_model->get_all_where("users",array("outlet_id"=>14));
          $total_reward+=($reward/sizeof($users));
        }
      }
      return $total_reward;
    }

    public function calculate_loan($user_id){
        $loan_data = $this->hrd_model->get_user_loans(array('user_id'=>$user_id,"payment_option !="=>"3"));  
        $total_pinjaman = 0;
        foreach ($loan_data as $loan) { 
            $loan_date = date("Y-m-d",strtotime($loan->loan_date));
            $now = date("Y-m-d");


            $date1 = $loan->loan_date;
            $date2 = date("Y-m-d");

            $ts1 = strtotime($date1);
            $ts2 = strtotime($date2);

            $year1 = date('Y', $ts1);
            $year2 = date('Y', $ts2);

            $month1 = date('m', $ts1);
            $month2 = date('m', $ts2);

            $diff = (($year2 - $year1) * 12) + ($month2 - $month1);
            
            $total_cicilan = 0;
            if($loan->instalment >= $diff){
                    //masih ada cicilan
                $total_cicilan = ($loan->loan_total-$loan->total_payment)/$loan->instalment;
                $total_pinjaman += $total_cicilan;
            } 
        } 

        return $total_pinjaman;
    }

    public function insert_loan_auto($user_id,$period){
        $loan_data = $this->hrd_model->get_user_loans(array('user_id'=>$user_id,"payment_option !="=>"3"));  
        $total_pinjaman = 0;
        $is_success = true;

        if($loan_data){
            foreach ($loan_data as $loan) { 
                $loan_date = date("Y-m-d",strtotime($loan->loan_date));
                $now = date("Y-m-d");


                $date1 = $loan->loan_date;
                $date2 = date("Y-m-d");

                $ts1 = strtotime($date1);
                $ts2 = strtotime($date2);

                $year1 = date('Y', $ts1);
                $year2 = date('Y', $ts2);

                $month1 = date('m', $ts1);
                $month2 = date('m', $ts2);

                $diff = (($year2 - $year1) * 12) + ($month2 - $month1);
                
                $total_cicilan = 0;
                if($loan->instalment >= $diff){
                        //masih ada cicilan
                    $total_cicilan = ($loan->loan_total-$loan->total_payment)/$loan->instalment;
                    $data = array(
                        'loan_id' =>$loan->id,
                        'repayment_date' =>$period,
                        'repayment_total' =>$total_cicilan,
                        'repayment_method'=>2,
                        'created_at'=> date('Y-m-d H:i:s')
                    );
                   
                    $save = $this->hrd_model->save('hr_repayments', $data);
                    if($save){
                        $is_success = true;
                    }else{
                        $is_success = false;
                    } 
                } 
            } 
        }
       

        return $is_success;
    }

    public function preview_slip(){
      $period =  $this->input->post('periode');
      $jobs =  $this->input->post('jobs_id');
      $status =  $this->input->post('status_id');
      $employees =  $this->input->post('employees');
			if($employees=="" || sizeof($employees)==0)redirect(base_url(SITE_ADMIN."/hrd_payroll/slip"));
      $temp=explode("-",$period);
      $month=$temp[0];
      $year=$temp[1];
      $payroll_periode=$this->hrd_model->get("hr_payroll_periode")->row();
      if(sizeof($payroll_periode)>0){
        $temp2=date("Y-m-d",strtotime($year."-".$month."-01 -1 month"));
        $start_date=date("Y-m",strtotime($temp2))."-".$payroll_periode->from;
        $end_date=$year."-".$month."-".$payroll_periode->to;
      }else{
        $start_date=$year."-".$month."-01";
        $end_date=date("Y-m-t",strtotime($year."-".$month."-01"));
      }
      $data_jobs =  "";
      $data_status = "";
      $data_users_jobs = $this->hrd_model->get_user_job($data_status,$data_jobs);
      $detail=array();
      foreach ($data_users_jobs as $data) {
        if(in_array($data->employee_id,$employees)){
          $jobs_component = $this->hrd_model->get_jobs_component_by(array("job_id"=>$data->jobs_id));
          //STATIC PENGURANG INSENTIF
          $payroll_static_data=$this->hrd_model->get_payroll_static_data(array("user_id"=>$data->employee_id,"start_date"=>$start_date,"end_date"=>$end_date));
          $detail[$data->employee_id]=array(
            "data"=>$data,
            "payroll_static_data"=>$payroll_static_data,
            "detail"=>array()
          );
          //HITUNG PINJAMAN CICILAN  
          foreach ($jobs_component as $component) {
            if($component->key=='late_1'){
              $component->value=$payroll_static_data['late_1']*$component->formula_default*$payroll_static_data['insentive'] /100;
            }elseif($component->key=='late_2'){
              $component->value=$payroll_static_data['late_2']*$component->formula_default*$payroll_static_data['insentive'] /100;
            }elseif($component->key=='permission_go_home'){
              $component->value=$payroll_static_data['permission_go_home']*$component->formula_default*$payroll_static_data['insentive'] /100;
            }elseif($component->key=='permission_alpha'){
              $component->value=$payroll_static_data['permission_alpha']*$component->formula_default*$payroll_static_data['insentive'] /100;
            }elseif($component->key=='alpha'){
              $component->value=$payroll_static_data['alpha']*$component->formula_default*$payroll_static_data['insentive'] /100;
			}elseif($component->key=='daily_allowance'){
              $component->value=$payroll_static_data['present']*$component->value;
			}elseif($component->key=='weekly_wage'){
              $component->value=$payroll_static_data['present_sunday']*$component->value;
            }elseif($component->key=='reward'){
              $component->value=$this->insert_auto_reward($data->employee_id,$month,$year);
            }elseif($component->key == "pinjaman"){ //jika pinjaman
              $component->value = $this->calculate_loan($data->employee_id);  
            }
            array_push($detail[$data->employee_id]['detail'],$component);                
          } 
        }
      }
      $this->data['periode']=$temp[1]."-".$temp[0]."-01";
      $this->data['jobs']=$jobs;
      $this->data['status']=$status;
      $this->data['employees']=$employees;
      $this->data['detail']=$detail;
      $this->data['subtitle']  = "Preview Slip Gaji";
      $this->data['content'] .= $this->load->view('admin/hrd/print_payroll_view.php', $this->data, true);
      $this->render('hrd');
    }
    
    public function print_dot_matrix($id=0){
        $form_data = $this->hrd_model->get_detail_payroll($id); 
        $temp=explode("-",$form_data->period);
        $month=$temp[0];
        $year=$temp[1];
        $payroll_periode=$this->hrd_model->get("hr_payroll_periode")->row();
        if(sizeof($payroll_periode)>0){
            $temp2=date("Y-m-d",strtotime($year."-".$month."-01 -1 month"));
            $start_date=date("Y-m",strtotime($temp2))."-".$payroll_periode->from;
            $end_date=$year."-".$month."-".$payroll_periode->to;
        }else{
            $start_date=$year."-".$month."-01";
            $end_date=date("Y-m-t",strtotime($year."-".$month."-01"));
        }
        $periode=$temp[1]."-".$temp[0]."-01";
        $params=array(
            "jobs"=>"",
            "status"=>"",
            "periode"=>$form_data->period,
            "id"=>$form_data->id
        );
        $lists=$this->hrd_model->get_print_payroll_history($params);
        $detail=array();
        foreach($lists as $l){
            $payroll_static_data=$this->hrd_model->get_payroll_static_data(array("user_id"=>$l->employee_id,"start_date"=>$start_date,"end_date"=>$end_date));
            if(!isset($detail[$l->employee_id])){
                $detail[$l->employee_id]=array(
                    "data"=>$l,
                    "payroll_static_data"=>$payroll_static_data,
                    "detail"=>array()
                );
            }
            array_push($detail[$l->employee_id]['detail'],$l);
        }
        $this->data['detail']=$detail;
        $this->data['periode']=$periode;
        $this->load->helper("printer_helper");
      
        //get printer HRD
        $this->load->model("setting_printer_model");
        $printer_arr_obj = $this->setting_printer_model->get_printer_by_enum_printer_type(array("name_type"=>"printer_matrix_hrd"));
        
        foreach ($printer_arr_obj as $printer_obj) {
            $printer_location = $printer_obj->name_printer;
            print_payroll($printer_location,$this->data);
        }
      
        redirect(base_url(SITE_ADMIN."/hrd_payroll/detail_payroll_history/".$id));
    }
}