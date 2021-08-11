<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/17/2014
 * Time: 8:27 PM
 */
class Hrd_schedule extends Hrd_Controller
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
        } 
    }

    public function index()
    {
        $this->data['title']    = "Kelola Jadwal";
        $this->data['subtitle'] ="Kelola Jadwal";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

       
        $this->data['data_url'] = base_url(SITE_ADMIN . '/hrd_schedule/get_data_user');;
        //load content
        $this->data['use_username'] = TRUE;
        $this->data['content'] .= $this->load->view('admin/hrd/schedule_staff_list', $this->data, true);
        $this->render('hrd');
    }
    
    public function get_data_user()
    {
     
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));  
        
        $this->datatables->select('users.id,users.nip,users.name')
            ->from('users')
            ->where('users.active',1)
            ->add_column('view', "<div class='btn-group'>
                <a   class='btn btn-default view-payroll-history'  payroll-history-id='$1'><i class='fa fa-pencil'></i> View</a>
                </div>", 'id')
            // ->add_column('standard_schedule', "<div class='btn-group'>
                // <a href='" . base_url(SITE_ADMIN . '/hrd_schedule/standard_schedule/$1')."' class='btn btn-default edit-payroll-history'  payroll-history-id='$1'> SET</a>
                // </div>", 'id')
            // ->add_column('special_schedule', "<div class='btn-group'>
                // <a href='" . base_url(SITE_ADMIN . '/hrd_schedule/special_schedule/$1')."' class='btn btn-default edit-payroll-history'  payroll-history-id='$1'> SET</a>
                // </div>", 'id')
            ->add_column('history_holiday', "<div class='btn-group'>
                <a href='" . base_url(SITE_ADMIN . '/hrd_schedule/history_holiday/$1')."' class='btn btn-default edit-payroll-history'  payroll-history-id='$1'>SET</a>
                </div>", 'id')
            ->add_column('set_change_schedule', "<div class='btn-group'>
                <a href='" . base_url(SITE_ADMIN . '/hrd_schedule/set_change_schedule/$1')."' class='btn btn-default edit-payroll-history'  payroll-history-id='$1'> SET</a>
                </div>", 'id');  
        echo $this->datatables->generate();
    }

    // fungsi untuk mengelola jadwal pegawai
    public function set_standard_schedule() {
        $this->load->model('hrd_model');
        $this->data['title']    = "Kelola Jadwal";
        $this->data['subtitle'] ="Kelola Jadwal";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');


        if (isset($_POST) && !empty($_POST)) {
            $office_hour_id=$this->input->post("office_hour");
            $employees=$this->input->post("employees");
            $start_date=$this->input->post("start_date");
            $end_date=$this->input->post("end_date");
            $repeat=$this->input->post("repeat");
            $start_time=$this->input->post("start_time");
            $end_time=$this->input->post("end_time");

            $office_hour=$this->hrd_model->get_one("hr_office_hours", $office_hour_id);
            $return=array();
            $return['status']=true;
            $return['message']="";


            if ($office_hour_id == "" || $office_hour_id = 0) {
                if (($start_time == "") || ($end_time == "")) {
                    $return['status']=false;
                    $return['message']="Jam Mulai dan Jam Akhir harus diisi";
                } else {
                    $office_hour_id = 0;
                    $checkin_time = $start_time;
                    $checkout_time = $end_time;
                }                    
            } else {
                $office_hour_id = $this->input->post("office_hour");
                $checkin_time = $office_hour->checkin_time;
                $checkout_time = $office_hour->checkout_time;
            }

            for($x=0;$x<sizeof($employees);$x++){
                $user_id = $employees[$x];
                $checking_schedule = $this->hrd_model->checking_schedule(array("start_date"=>$start_date,"end_date"=>$end_date,"repeat"=>$repeat,"user_id"=>$user_id));
                if(sizeof($checking_schedule)>0){
                    $return['status']=false;
                    foreach($checking_schedule as $c){
                        $return['message'].=$c->name." sudah mempunyai jadwal di tanggal ".date("d/m/Y",strtotime($c->start_date)).($c->enum_repeat==1 ? " dan berlaku seterusnya" : " s/d ".date("d/m/Y",strtotime($c->end_date)))."<br>";                     
                    }
                }
            }

            if (!empty($employees)){

            if($return['status']==true){
                

                for($x=0;$x<sizeof($employees);$x++){
                    $user_id=$employees[$x];

                    if($repeat == 1){

                    $save_data = array(
                        'user_id'             => $user_id,
                        'start_date'          => $start_date,
                        'end_date'            => '0000-00-00',
                        'enum_repeat'         => $repeat,
                        'free_day'            => 0,
                        'is_special_schedule' => 0
                    );

                    } else {

                    $save_data = array(
                        'user_id'             => $user_id,
                        'start_date'          => $start_date,
                        'end_date'            => $end_date,
                        'enum_repeat'         => $repeat,
                        'free_day'            => 0,
                        'is_special_schedule' => 0
                    );

                    }

                    $save_data = $this->hrd_model->save('hr_schedules', $save_data); 

                    if($save_data){
                        $save_detail = array(
                            'schedule_id'       => $save_data,
                            'office_hour_id'    => $office_hour_id,
                            'start_time'        => $checkin_time,
                            'end_time'          => $checkout_time
                        );
                     $this->hrd_model->save('hr_schedule_detail', $save_detail);
                    }

                }  
            
            }
        }

          
            redirect(base_url(SITE_ADMIN.'/hrd_schedule/set_standard_schedule','refresh'));

             
        } else {
            $store = $this->data['setting']['store_id'];
            $this->data['office_hours']= $this->hrd_model->get("hr_office_hours")->result();

            $usersch = array();
            $get_user_id = $this->hrd_model->get("hr_schedules")->result();
            foreach ($get_user_id as $key) {
                array_push($usersch, $key->user_id);
            }

            $this->data['data_url'] = base_url(SITE_ADMIN . '/hrd_schedule/get_employee_schedule_data');
            $this->data['employees']=$this->hrd_model->get_employee_schedule(array("active"=>1, "store_id" => $store),false);

           
            $this->data['content'] .= $this->load->view('admin/hrd/set_schedule_standard_view', $this->data, true);
            $this->render('hrd');
        }

    }

     public function get_employee_schedule_data()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $this->datatables->select('s.name as nm,hs.start_date as sd,
                hs.end_date as ed,
                hsd.start_time as st,
                hsd.end_time as et')
            ->from('users s')
            ->join("hr_schedules hs", "hs.user_id=s.id")
            ->join("hr_schedule_detail hsd", "hsd.schedule_id=hs.id")
            ->unset_column("ed");

        $this->datatables->add_column('ed', '$1', 'endtime(ed)');
        // $this->datatables->add_column('actions', "<div class='btn-group'>
        // <a href='" . base_url(SITE_ADMIN . '/hrd/detail_refund/$1') . "' target='_blank' class='btn btn-default' rel='tooltip' data-tooltip='tooltip' target='_blank' title='Detail'>
        // <i class='fa fa-search'></i></a>", 'id');

        echo $this->datatables->generate();
    }

    // fungsi untuk mengelola jadwal cuti pegawai
    public function holiday() {
        $this->data['title']    = "Kelola Cuti Pegawai";
        $this->data['subtitle'] = "Kelola Cuti Pegawai";

        $this->data['users'] = $this->hrd_model->get_user_dropdown(array("active"=>1));

        $this->data['data_url'] = base_url(SITE_ADMIN . '/hrd_schedule/get_leave_data/');
        $this->data['content'] .= $this->load->view('admin/hrd/schedule_holiday_view', $this->data, true);
        $this->render('hrd');
    }

    public function standard_schedule()
    {
        $id       = $this->uri->segment(4);
        $this->data['title']    = "Set Jadwal Standar";
        $this->data['subtitle'] ="Set Jadwal Standar";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        if (isset($_POST) && !empty($_POST)) {

            // $data_schedule = $this->hrd_model->get_standard_schedules_byuser($this->input->post('user_id'));
            // if($data_schedule){
                 // $remove_schedule = $this->hrd_model->delete_by_where("hr_schedules",array("user_id"=>$this->input->post('user_id')));
                 // if($remove_schedule){
                     // $remove_schedule_detail = $this->hrd_model->delete_by_where(
                            // "hr_schedule_detail",
                            // array("schedule_id"=>$data_schedule->schedule_id)
                    // );
                 // }
            // }

            // $free_day = json_encode($this->input->post('free_day'));
            $free_day = 0;
            $save_data = array(
                'user_id'      => $this->input->post('user_id'),
                'start_date'      => $this->input->post('start_date'),
                'end_date'      => $this->input->post('end_date'),
                'enum_repeat'      => $this->input->post('repeat'),
                'free_day'      => $free_day,
                'is_special_schedule'=>0
            );
            $save_data = $this->hrd_model->save('hr_schedules', $save_data); 

            $status = true;
            if($save_data){
                 $save_data2 = array(
                    'schedule_id'      => $save_data,
                    'office_hour_id'      => $this->input->post("template_id"),
                    'start_time'      => $this->input->post('start_time'),
                    'end_time'      => $this->input->post('end_time')
                );
                $save_data2 = $this->hrd_model->save('hr_schedule_detail', $save_data2);
                if($save_data2){
                    $this->session->set_flashdata('message_success', "Berhasil Menyimpan Jadwal");
                } else{
                    $status = false;
                    $this->session->set_flashdata('message', "Gagal Menyimpan Detail Jadwal");
                }
            }else{
                $status = false;
                $this->session->set_flashdata('message', "Gagal Menyimpan Jadwal");
            }

            if($status){
                 redirect(SITE_ADMIN . '/hrd_schedule', 'refresh');
            }

        }

        $this->data['users']  = $this->hrd_model->get_one('users', $id);

        $this->data['schedules']  = $this->hrd_model->get_standard_schedules_byuser($id);
       
        $this->data['office_hours']     = $this->hrd_model->get_office_hours_dropdown();
       
        $this->data['data_url'] = base_url(SITE_ADMIN . '/hrd_schedule/get_data_user');;
        //load content
        $this->data['use_username'] = TRUE;
        $this->data['content'] .= $this->load->view('admin/hrd/schedule_standard_view', $this->data, true);
        $this->render('hrd');
    } 

      public function special_schedule()
    {
        $id       = $this->uri->segment(4);
        $this->data['title']    = "Set Jadwal Khusus";
        $this->data['subtitle'] ="Set Jadwal Khusus";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        if (isset($_POST) && !empty($_POST)) {  
            $save_data = array(
                'user_id'      => $this->input->post('user_id'),
                'start_date'      => $this->input->post('start_date'),
                'end_date'      => $this->input->post('end_date'),
                'enum_repeat'      => $this->input->post('repeat'),
                'is_special_schedule'=>1
            );
            $save_data = $this->hrd_model->save('hr_schedules', $save_data); 

            $status = true;
            if($save_data){
                 $save_data2 = array(
                    'schedule_id'      => $save_data,
                    'start_time'      => $this->input->post('start_time'),
                    'end_time'      => $this->input->post('end_time')
                );
                $save_data2 = $this->hrd_model->save('hr_schedule_detail', $save_data2);
                if($save_data2){
                    $this->session->set_flashdata('message_success', "Berhasil Menyimpan Jadwal");
                } else{
                    $status = false;
                    $this->session->set_flashdata('message', "Gagal Menyimpan Detail Jadwal");
                }
            }else{
                $status = false;
                $this->session->set_flashdata('message', "Gagal Menyimpan Jadwal");
            }

            if($status){
                 redirect(SITE_ADMIN . '/hrd_schedule', 'refresh');
            }

        }

        $this->data['users']  = $this->hrd_model->get_one('users', $id);


        $this->data['schedules']  = $this->hrd_model->get_standard_schedules_byuser($id);
       
        $this->data['office_hours']     = $this->hrd_model->get_office_hours_dropdown();
       
        $this->data['data_url'] = base_url(SITE_ADMIN . '/hrd_schedule/get_data_user');;
        //load content
        $this->data['use_username'] = TRUE;
        $this->data['content'] .= $this->load->view('admin/hrd/schedule_special_view', $this->data, true);
        $this->render('hrd');
    } 


    public function history_holiday()
    {
        $id = $this->uri->segment(4);
        $this->data['title']    = "History Cuti";
        $this->data['subtitle'] ="History Cuti";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['history_jobs'] =  $this->hrd_model->get_last_jobs_user($id);
        $this->data['add_url']  = base_url(SITE_ADMIN . '/hrd_schedule/add_holiday/'.$id);
        $this->data['data_url'] = base_url(SITE_ADMIN . '/hrd_schedule/get_leave_data/'.$id);
        //load content
        $this->data['use_username'] = TRUE;
        $this->data['content'] .= $this->load->view('admin/hrd/schedule_holiday_view', $this->data, true);
        $this->render('hrd');
    }

    public function add_holiday()
    {
        $this->data['title']    = "Input Cuti";
        $this->data['subtitle'] ="Input Cuti";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        if (isset($_POST) && !empty($_POST)) {
            $user_id = $this->input->post('user_id');
            $total_days = 0;
            $start_date = strtotime($this->input->post('start_date'));
            $end_date = strtotime($this->input->post('end_date'));
            $datediff = $start_date - $end_date;
            $total_days =  abs(floor($datediff/(60*60*24))) + 1;

            $check_users_holidays = $this->hrd_model->check_job_history(array('hjh.employee_id' => $user_id));
            $get_user = array_pop($this->hrd_model->get_all_where('users', array('id' => $user_id)));
            if (!empty($check_users_holidays) && $check_users_holidays->vacation > 0) {
                $save_data = array(
                    'user_id'      => $this->input->post('user_id'),
                    'start_date'      => $this->input->post('start_date'),
                    'end_date'      => $this->input->post('end_date'), 
                    'enum_holiday_status'=>$this->input->post('enum_holiday_status'),
                    'created_at'      => date('Y-m-d H:i:s'),
                    'days'=> $total_days
                );
               
                $save_data = $this->hrd_model->save('hr_holidays', $save_data);

                if ($save_data) {
                    $this->session->set_flashdata('message_success', "Berhasil menambah data.");
                }
            } else {
                $this->session->set_flashdata('message', "Gagal menyimpan data. Karyawan atas nama ".$get_user->name." belum mempunyai jatah cuti.");
            }   

            redirect(SITE_ADMIN . '/hrd_schedule/add_holiday/', 'refresh');
        }

        $this->data['users'] = $this->hrd_model->get_user_dropdown(array("active"=>1));

        //load content
        $this->data['use_username'] = TRUE;
        $this->data['content'] .= $this->load->view('admin/hrd/schedule_add_holiday_view', $this->data, true);
        $this->render('hrd');
    }

    public function get_leave_data()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));  
        
        $this->datatables->select('hr_holidays.id,users.id as user_id, users.nip,users.name,hr_holidays.start_date,hr_holidays.end_date,hr_holidays.days')
            ->from('hr_holidays')
            ->join('users', 'users.id = hr_holidays.user_id', 'left')
              ->add_column('actions', "<div class='btn-group'>
                 <a href='" . base_url(SITE_ADMIN . '/hrd_schedule/edit_holidays/$1/$2') . "'  class='btn btn-default' rel='Cuti' ><i class='fa fa-pencil'></i> Edit</a>
                <a href='" . base_url(SITE_ADMIN . '/hrd_schedule/delete_holidays/$1/$2') . "'  class='btn btn-danger deleteNow' rel='Cuti' ><i class='fa fa-trash-o'></i> Delete</a>
                </div>", 'id,user_id');
        echo $this->datatables->generate();
    }


    public function get_holidays_data()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));  
        
        $this->datatables->select('users.id,users.username, users.name,hr_employee_holidays.created_at,hr_employee_holidays.day')
            ->from('hr_employee_holidays')
            ->join('users', 'users.id = hr_employee_holidays.user_id') ;
        echo $this->datatables->generate();
    }

    public function edit_holidays()
    {
        $user_id       = $this->uri->segment(5);
        $id       = $this->uri->segment(4);
        
        $this->data['title']    = "Edit Cuti";
        $this->data['subtitle'] ="Edit Cuti";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        if (isset($_POST) && !empty($_POST)) { 
            $total_days = 0;
             $start_date = strtotime($this->input->post('start_date'));
             $end_date = strtotime($this->input->post('end_date'));
             $datediff = $start_date - $end_date;
             $total_days =  abs(floor($datediff/(60*60*24)));

            $save_data = array(
                'user_id'      => $this->input->post('user_id'),
                'start_date'      => $this->input->post('start_date'),
                'end_date'      => $this->input->post('end_date'), 
                'enum_holiday_status'=>$this->input->post('enum_holiday_status'),
               'updated_at'      => date('Y-m-d H:i:s'),
               'days'=> $total_days
            );

           
            $save_data = $this->hrd_model->update_holidays($this->input->post('holiday_id'),$save_data); 
          
            //
            redirect(SITE_ADMIN . '/hrd_schedule/history_holiday/'.$this->input->post('user_id'), 'refresh');
        }

        $this->data['users']  = $this->hrd_model->get_one('users', $user_id);
       
        $this->data['history_jobs'] =  $this->hrd_model->get_last_jobs_user($user_id); 

        $this->data['data_holidays'] = $this->hrd_model->get_holidays_by_id(array("id"=>$id)); 

        $this->data['taken_total'] = $this->hrd_model->get_taken_total_holidays_byuser(array("user_id"=>$user_id));

        $this->data['data_url'] = base_url(SITE_ADMIN . '/hrd_schedule/get_data_user');
        //load content
        $this->data['use_username'] = TRUE;
        $this->data['content'] .= $this->load->view('admin/hrd/schedule_edit_holiday_view', $this->data, true);
        $this->render('hrd');
    }

    public function delete_holidays(){
        $id       = $this->uri->segment(4);
        $user_id     = $this->uri->segment(5);
        $return_data['status'] = false;
        $return_data['data'] = array();
        $return_data['message'] = ""; 

        if(empty($id)){
            
              redirect(SITE_ADMIN . '/hrd_schedule', 'refresh');
        }else{
            $status = $this->hrd_model->delete_by_where("hr_holidays",array("id"=>$id));
            if($status){
                 $this->session->set_flashdata('message_success', "Data Berhasil Di Hapus");
            }else{
                $this->session->set_flashdata('message', "Maaf, Data Gagal Di Hapus. Silahkan Hubungi Administrator");
            }

             redirect(SITE_ADMIN . '/hrd_schedule/holiday', 'refresh');
        } 
        
    }

    public function get_data_office_hours(){
        $id = $this->input->post('template_id');
        $data  = $this->hrd_model->get_one('hr_office_hours', $id);
        $return_data = array();
        $return_data['data'] = array();
        $return_data['status'] = false;
        $return_data['message'] = "";
        if($data){
            $return_data['data'] = $data;
            $return_data['status'] = true;
        }else{
            $return_data['message'] = "Data Template Kerja tidak ditemukan";
        }

        echo json_encode($return_data);
    }

    public function set_change_schedule()
    {
        $id       = $this->uri->segment(4);
        $this->data['title']    = "Set Pindah Jadwal";
        $this->data['subtitle'] ="Set Pindah Jadwal";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        if (isset($_POST) && !empty($_POST)) {
             $save_data = array(
                'user_id'      => $this->input->post('user_id'),
                'holiday_date'      => $this->input->post('holiday_date'),
                'work_date'      => $this->input->post('work_date'),
                'created_at'      => date('Y-m-d H:i:s')
            );
            $save_data = $this->hrd_model->save('hr_schedule_exchange', $save_data); 
            if($save_data){
                $this->session->set_flashdata('message_success', "Berhasil Menukar Jadwal");
            }else{
                $this->session->set_flashdata('message', "Gagal Menukar Jadwal");
            }
            redirect(SITE_ADMIN . '/hrd_schedule', 'refresh');
        }

        $this->data['users']  = $this->hrd_model->get_one('users', $id);
       
        $this->data['data_url'] = base_url(SITE_ADMIN . '/hrd_schedule/get_data_user');;
        //load content
        $this->data['use_username'] = TRUE;
        $this->data['content'] .= $this->load->view('admin/hrd/schedule_change_view', $this->data, true);
        $this->render('hrd');
    }

   

    function view_detail_office_hours($office_hour_id=0)
    {
      $this->data['office_hour']=$this->hrd_model->get_one("hr_office_hours",$office_hour_id);
      $this->data['detail']=$this->hrd_model->get_detail_employee_office_hour(array("office_hour_id"=>$office_hour_id));
      $content=$this->load->view("admin/hrd/view_detail_office_hour",$this->data,true);
      echo json_encode(array(
        "content"=>$content
      ));
    }

    /*
    *   Update by: M. Tri Ramdhani
    *   Date: 22/08/2016
    */
    function employee_holidays() {
        $this->data['title']    = "Kelola Hari Libur";
        $this->data['subtitle'] ="Kelola Hari Libur";
        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->form_validation->set_rules('holiday-date', 'Tanggal Libur', 'required');
        $this->form_validation->set_rules('employees', 'Karyawan', 'required');

        if ($this->form_validation->run()==TRUE) {
            
            $employees=$this->input->post("employees");
            $date = $this->input->post('holiday-date');

            if (!empty($employees) || !empty($date)) {


                // $this->hrd_model->delete_by_limit("hr_employee_holidays",array("day"=>$day),0);
                foreach($employees as $e){
                    $this->hrd_model->save("hr_employee_holidays",array(
                        "created_at"=>date("Y-m-d H:i:s"),
                        "created_by"=>$this->data['user_profile_data']->id,
                        "user_id"=>$e,
                        "day"=>$date
                    ));
                }
                $this->session->set_flashdata('message_success', "Hari libur berhasil disimpan.");
            } else {
                $this->session->set_flashdata('message', "Gagal menyimpan. ");
            }

        redirect(base_url(SITE_ADMIN."/hrd_schedule/employee_holidays"));

        } else {
            $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            $this->data['message_success'] = $this->session->flashdata('message_success');
            $this->data['employees']=$this->hrd_model->get_all_where("users",array("active"=>1,"name !="=>$this->config->config['sync_user_username']));

            $this->data['data_url'] = base_url(SITE_ADMIN . '/hrd_schedule/get_holidays_data/');
            $this->data['content'] .= $this->load->view('admin/hrd/set_employee_holiday_view', $this->data, true);
            $this->render('hrd');
        }  



    }
    /* end edit by tri */

    function get_data_employee_holidays()
    {
      $day=$this->input->post("day");
      $this->data['employee_holidays']=$this->hrd_model->get_employee_holidays(array("day"=>$day));
      $this->data['employees']=$this->hrd_model->get_all_where("users",array("active"=>1));
      $current_holiday_employees="";
      $exceptions=array();
      foreach($this->data['employee_holidays'] as $a){
        array_push($exceptions,$a->id);
        $current_holiday_employees.="<option value='".$a->id."'>".$a->name."</option>";
      }
      $employee_list_content="";
      foreach($this->data['employees'] as $a){
        if(!in_array($a->id,$exceptions)){
          $employee_list_content.="<option value='".$a->id."'>".$a->name."</option>";
        }
      }
      echo json_encode(array(
        "employee_list"=>$employee_list_content,
        "current_holiday_employees"=>$current_holiday_employees
      ));
    } 
    
    function get_data_employee_by_office_hour()
    {
      $office_hour_id=$this->input->post("office_hour_id");
      $office_hour=$this->hrd_model->get_one("hr_office_hours",$office_hour_id);
 
      $results=$this->hrd_model->get_detail_employee_office_hour(array("office_hour_id"=>$office_hour_id));
      $content="";
      foreach($results as $r){
        $content.="<option value='".$r->user_id."'>".$r->name."</option>";
      }
      echo json_encode(array(
        "content"=>$content
      ));
    } 
    
}