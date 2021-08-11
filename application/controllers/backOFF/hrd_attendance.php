<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

 
class Hrd_attendance extends Hrd_Controller
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
           redirect(SITE_ADMIN . '/hrd_attendance/attendance_list', 'refresh');
    }
    public function attendance_list()
    {
        $this->data['title']    = "Daftar Hadir";
        $this->data['subtitle'] ="Daftar Hadir";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

       
        $this->data['data_url'] = base_url(SITE_ADMIN . '/hrd_attendance/get_data_attendance');
       
        //load content
				$this->data['office_hour_lists']=$this->hrd_model->get("hr_office_hours")->result();
        $this->data['use_username'] = TRUE;
        $this->data['content'] .= $this->load->view('admin/hrd/attendance_list_view', $this->data, true);
        $this->render('hrd');
    }
    public function get_data_attendance(){
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));  
        $this->load->helper(array('hrd'));
        $date=date("Y-m-d");
        if($_POST){
          $data=$this->input->post();
          if($data['columns'][1]['search']['value']!=""){
            $date=$data['columns'][1]['search']['value'];
          }
        }
         $this->datatables->select('ha.id, users.id as user_id, users.nip, users.name,store.store_name, 
                                    hsd.start_time,hsd.end_time,ha.over_checkin_time,ha.over_checkout_time,
                                    ha.checkin_time,ha.checkout_time,IFNULL(hesa.name,"") as enum_status_attendance
                                    ,ha.attachment,CONCAT(hsd.start_time,"-",hsd.end_time) as schedule_time',false)
            ->from('users') 
            ->join('store','users.store_id = store.id')
            ->join('hr_schedules hs','hs.user_id = users.id AND (IF(hs.enum_repeat=1,"'.$date.'">=hs.start_date,"'.$date.'" BETWEEN hs.start_date AND hs.end_date)) AND hs.free_day NOT LIKE CONCAT("%",SUBSTRING(DAYNAME("'.$date.'"), 1, 3),"%")')
            ->join('hr_schedule_detail hsd','hsd.schedule_id = hs.id')
					  ->join('hr_office_hours hoh','hsd.office_hour_id=hoh.id')
            ->join('hr_attendances ha',' ha.user_id= users.id and ha.created_at = "'.$date.'"','LEFT')
            ->join('hr_enum_status_attendance hesa','ha.enum_status_attendance=hesa.id','left');
						if($data['office_hour_id']!=""){
							$this->datatables->where("hsd.office_hour_id",$data['office_hour_id']);
						}
            $this->datatables->group_by('users.id') 
               ->unset_column('enum_status_attendance') 
            // ->add_column('actions','$1', 'set_status_attendance(enum_status_attendance,user_id)')  
            ->add_column('curdate', date('Y-m-d',strtotime($date)))  
            ->add_column('performance','$1', 'set_status_tindakan(enum_status_attendance,user_id,'.date('Y-m-d',strtotime($date)).')')  ;
             
           
            
        echo $this->datatables->generate();
    }
    public function get_data_attendance_byuserid(){
        $user_id       = $this->uri->segment(4);
        $date       = $this->uri->segment(5);
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));  
        $this->load->helper(array('hrd'));
         $this->datatables->select('ha.id, users.id as user_id, users.nip, users.name,store.store_name, 
                                    hsd.start_time,hsd.end_time,
                                    ha.checkin_time,ha.checkout_time,ha.enum_status_attendance
                                    ,ha.attachment,CONCAT(hsd.start_time,"-",hsd.end_time) as schedule_time,date(IFNULL(ha.created_at,current_date())) as curdate
                                    ',false)
            ->from('users') 
            ->join('store','users.store_id = store.id')
            ->join('hr_schedules hs','hs.user_id = users.id ','left')
            ->join('hr_schedule_detail hsd','hsd.schedule_id = hs.id','left')
            ->join('hr_attendances ha',' ha.user_id= users.id'.($date!="" ? ' and ha.created_at = "'.$date.'"' : ""),'LEFT')
            ->group_by('date(ha.created_at)') 
            ->where('users.id',$user_id)
            ->unset_column('enum_status_attendance')
            ->add_column('enum_status_attendance','$1', 'set_attendance_text(enum_status_attendance)')  
            ->add_column('actions','$1', 'set_status_attendance(enum_status_attendance,user_id)');
						if($date!=""){
							$this->datatables->add_column('curdate', date('Y-m-d',strtotime($date)));							
						}
            $this->datatables->add_column('performance', "<div class='btn-group'>
              <a href='" . base_url(SITE_ADMIN . '/hrd_attendance/set_perfomance/$1')."' class='btn btn-default' rel='Reimburse'>Tindakan</a>
                </div>", 'user_id');
           
            
        echo $this->datatables->generate();
    }
    public function add_attendance(){
        $user_id       = $this->uri->segment(4);

         $cond = array(
            "user_id" => $user_id,
            "created_at" => date("Y-m-d"),
            "checkin_time IS NOT NULL" =>NULL
        );
        $data = $this->hrd_model->get_where("hr_attendances",$cond);
        if(!$data){
             $save_data = array(
                'user_id'      => $user_id,
                'checkin_time'      => date('H:i:s'),
                'enum_status_attendance'      => 1,
                'created_at' =>date('Y-m-d H:i:s')
            );
            $save_data = $this->hrd_model->save('hr_attendances', $save_data); 
        }else{
             $id_attendance = $data[0]->id;
             $save_data = array(
                
                'checkout_time'      => date('H:i:s')
            );
             $save_data = $this->hrd_model->save('hr_attendances', $save_data,$id_attendance); 
        }
       
       
         redirect(SITE_ADMIN . '/hrd_attendance', 'refresh');
    }
    public function set_perfomance(){
        $this->data['title']    = "Set performance";
        $this->data['subtitle'] ="Set performance";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');
        $user_id       = $this->uri->segment(4);
        $date       = $this->uri->segment(5);
        $this->data['employee_id'] =$user_id; 

        $this->data['status_attendances']  = $this->hrd_model->get_enum_attendance_dropdown();
        $data_note = array_pop($this->hrd_model->get_where('hr_attendances', array('user_id' => $user_id)));
        $this->data['note'] = array(
        'name' => 'note',
        'id' => 'note',
        'type' => 'text',
        'class' => 'form-control',
        'field-name' => 'Note',
        'placeholder' => 'Masukan Note',
        'value' => (!empty($data_note))?$data_note->note:""
          );

        $this->data['file_url']    = array('name' => 'file_url',
                                           'id' => 'file_url',
                                           'type' => 'file',
                                           'class' => 'form-control maxUploadSize',
                                           'placeholder' => '',
                                           'data-maxsize' => '10000000', // byte
                                          
                                          );
        $this->data['url_data'] =(!empty($data_note))?$data_note->attachment:"";

        $this->data['data_users'] = $this->hrd_model->get_one('users', $user_id);
        $this->data['history_jobs'] =  $this->hrd_model->get_last_jobs_user($user_id); 
        $this->data['an_life_taken_total'] = $this->hrd_model->get_taken_total_holidays_byuser(array("user_id"=>$user_id));
        $this->data['reimburse_taken_total'] = $this->hrd_model->get_taken_total_reimburse_byuser(array("user_id"=>$user_id));
        $this->data['data_attendance_today'] = $this->hrd_model->get_attendance_today_by_user($user_id);
        $this->data['date']=$date;
        $this->data['content'] .= $this->load->view('admin/hrd/attendance_set_performance_view', $this->data, true);
        $this->render('hrd');
    }

    public function update_status_attendance(){
      $user_id       = $this->input->post('user_id');
      $date       = $this->input->post('date');
      $file_url = $this->input->post('file_url');
      $note = $this->input->post('note');


      if(empty($user_id)) redirect(SITE_ADMIN . '/hrd_attendance', 'refresh'); 
      $status_attendances       = $this->input->post('status_attendances');
      if ($status_attendances == 0) {
        $this->session->set_flashdata('message', 'Silahkan Pilih Status');
      }
      else {


      $file_name = '';
      $isUpload   = TRUE;
            if (! empty($_FILES['file_url']['name'])) {
                //upload config
                $newname                 = $this->generate_random_name();
                $config['upload_path']   = './uploads/file_lampiran/';
                $config['allowed_types'] = 'pdf|doc|docx|xls|xlsx';
                $config['max_size']      = '1000';
                $config['overwrite']     = FALSE;
                $config['file_name']     = $newname;
                $val = $this->load->library('upload', $config);

                if (! $this->upload->do_upload('file_url')) {
                    $this->session->set_flashdata('message', $this->upload->display_errors());
                    $isUpload = FALSE;
                }
                else {
                   
                    $file_name = 'uploads/file_lampiran/'. $this->upload->data()['file_name'];
                    $isUpload   = TRUE;
                   
                }
            }







      
        $data = $this->hrd_model->get_all_where("hr_attendances",array("user_id"=>$user_id,"created_at"=>$date));
        $office_hour = $this->hrd_model->get_office_hour_active(array("user_id"=>$user_id,"date"=>$date,"office_hour_id"=>""));
        if(!empty($data)){
          $data=$data[0];
          // $del_attendance_today = $this->hrd_model->del_attendance_today($user_id);
        } 
        if(!empty($office_hour))$office_hour=$office_hour[0];
        $checkin_time = NULL;
        $checkout_time = NULL;
        if($status_attendances == 1){
          $checkin_time = date('H:i:s');
          $checkout_time = date('H:i:s'); 
        }
        $save_data = array(
          'user_id'                 => $user_id,
          // 'checkin_time'            => (sizeof($data)==0 || $data->checkin_time=="" ? (sizeof($office_hour)>0 ? $office_hour->start_time : $checkin_time) : $data->checkin_time),
          // 'checkout_time'           => (sizeof($data)==0 || $data->checkout_time=="" ? (sizeof($office_hour)>0 ? $office_hour->end_time : $checkout_time) : $data->checkout_time),
          'enum_status_attendance'  => $status_attendances,
          'attachment'              => $file_name,
          'note'                    => $note,
          'created_at'              => $date
        );
        $save_data = $this->hrd_model->save('hr_attendances', $save_data,(sizeof($data)>0 ? $data->id : 0)); 
      }
      redirect(SITE_ADMIN . '/hrd_attendance/set_perfomance/'.$user_id."/".$date, 'refresh');
    }

    public function get_attendance_statistic(){ 
      $user_id       = $this->uri->segment(4);
      $this->load->library(array('datatables'));
      $this->load->helper(array('datatables'));    
      $this->datatables->select(' b.name ,count(user_id) as total_days')
      ->from('hr_enum_status_attendance b')  
      ->join('(select*from hr_attendances where user_id="'.$user_id.'" group by date(created_at)) a','a.enum_status_attendance = b.id','LEFT')
      ->group_by('b.id,a.user_id') ;
      echo $this->datatables->generate();
    } 

    public function get_data_graphic_attendance(){
        $user_id       = $this->input->post('user_id');
        $return_data = array();
        $data_enum_attendance = $this->hrd_model->get_enum_attendance();
       
        foreach ($data_enum_attendance as $row) {
             $data = new stdclass();
             $data->name = $row->name;
             $data_enum_attendance = $this->hrd_model->get_attendance_statistic_byuser($user_id,$row->id);
             
             $total_days = array();
             foreach ($data_enum_attendance as $row2) {
                $total_days[] = (int)$row2->total_days;       
             }
             $data->data =  $total_days; 

             array_push( $return_data, $data);
        }
        echo json_encode($return_data);
    }
    public function get_attendance_statistic_bymonth(){
        $user_id       = $this->input->post('user_id');
        $return_data = array();
        $data_enum_attendance = $this->hrd_model->get_enum_attendance();
       
        foreach ($data_enum_attendance as $row) {
             $data = new stdclass();
             $data->name = $row->name;
             $data_enum_attendance = $this->hrd_model->get_attendance_statistic($row->id);
             
             $total_days = array();
             foreach ($data_enum_attendance as $row2) {
                $total_days[] = (int)$row2->total_days;       
             }
             $data->data =  $total_days; 

             array_push( $return_data, $data);
        }
        echo json_encode($return_data);
    }
    
     public function server_sync()
    {
        $this->data['title']    = "Sinkronisasi Server";
        $this->data['subtitle'] = "Sinkronisasi Server";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['add_url']  = base_url(SITE_ADMIN . '/hrd_attendance/add_server');
        $this->data['data_url'] = base_url(SITE_ADMIN . '/hrd_attendance/get_server_data');
        //load content
        $this->data['use_username'] = TRUE;
        $this->data['content'] .= $this->load->view('admin/hrd/sync-list', $this->data, true);
        $this->render('hrd');
    }

     public function get_server_data()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $this->datatables->select('id,controller,start_time,end_time,interval')
                                  ->from('hr_server_sync')
                                  ->add_column('actions', "<div class='btn-group'>
                                    <a href='" . base_url(SITE_ADMIN . '/hrd_attendance/edit_server/$1') . "'  class='btn btn-default'><i class='fa fa-pencil'></i> Edit</a>
                                    <a href='" . base_url(SITE_ADMIN . '/hrd_attendance/delete_server/$1') . "' class='btn btn-danger deleteNow' rel='Server'><i class='fa fa-trash-o'></i> Hapus</a>
                                    <br><br><br>
                                    <button  onclick=App.downloadLogData('$2')  class='sync-to-server'> Download Log Absensi</button>
                                </div>", 'id,controller') ;
        echo $this->datatables->generate();
    }

    public function add_server()
    {
        $this->data['title']    = "Tambah Server";
        $this->data['subtitle'] = "Tambah Server";
  
        $this->form_validation->set_rules('server_start_time', 'Waktu mulai', 'required');
        $this->form_validation->set_rules('server_end_time', 'Waktu akhir', 'required');
        $this->form_validation->set_rules('server_interval', 'Interval', 'required|greater_than[0]|less_than[301]');

        if ($this->form_validation->run() == true) {

            $start_time  = $this->input->post('server_start_time');
            $end_time    = $this->input->post('server_end_time');
            $interval    = $this->input->post('server_interval');
            $to_time     = strtotime($start_time);
            $from_time   = strtotime($end_time);
            $diff_minute = round(abs($to_time - $from_time) / 60, 2);
            if ($interval > $diff_minute) {

                $this->session->set_flashdata('message', 'Interval harus kurang dari selisih waktu');
            }
            else {
              
                $random_name = $this->generate_random_name(15);
                $name        = 'Posresto_HR_task_' . $random_name;

                $data_array = array( 
                                    'controller' => "fingerprint_attendances",
                                    'name' => $name,
                                    'start_time' => $start_time,
                                    'end_time' => $end_time,
                                    'interval' => $interval);

                $save = $this->hrd_model->save('hr_server_sync', $data_array);

                if ($save === false) {
                    $this->session->set_flashdata('message', 'Failed save server');
                }
                else {
                    // add task scheduler
                    $this->load->library('MY_scheduler');
                    $program    = $this->config->item('php_exe_path') . ' -f ' . FCPATH . 'index.php hrd_scheduler fingerprint_attendances';
                    $start_date = '01/01/2015';
                    $end_date   = '12/12/3015'; 
                    //$start_date = '2015/01/01';
                    //$end_date   = '3015/12/12';

                    $result = $this->my_scheduler->create_task_hr($name, $interval, $program, $start_time, $end_time, $start_date, $end_date);
                   
                 if ($result) {
                        $this->session->set_flashdata('message_success', 'Berhasil menyimpan data');
                    }
                    else {
                        $this->hrd_model->delete('hr_server_sync', $save);
                        $this->session->set_flashdata('message', 'Gagal menyimpan server. Cek waktu mulai, waktu akhir & interval');
                    }
                }
            }

            $btnaction = $this->input->post('btnAction');
            if ($btnaction == 'save_exit') {
                redirect(SITE_ADMIN . '/hrd_attendance/server_sync', 'refresh');
            }
            else {
                redirect(SITE_ADMIN . '/hrd_attendance/add_server/', 'refresh');
            }


        }
        else {
            //display the create user form
            //set the flash data error message if there is one
            $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            $this->data['message_success'] = $this->session->flashdata('message_success');

           
            $this->data['server_sync_url']   = array('name' => 'server_sync_url',
                                                 'id' => 'server_sync_url',
                                                 'type' => 'text',
                                                 'class' => 'form-control requiredTextField',
                                                 'field-name' => 'URL',
                                                 'placeholder' => 'Masukan URL server',
                                                 'disabled' => 'disabled',
                                                 'value' => "fingerprint_attendances"); 
            $this->data['server_start_time'] = array('name' => 'server_start_time',
                                                     'id' => 'server_start_time',
                                                     'type' => 'text',
                                                     'class' => 'form-control requiredTextField time start',
                                                     'field-name' => 'Waktu mulai',
                                                     'placeholder' => 'Masukan waktu mulai',
                                                     'value' => $this->form_validation->set_value('server_start_time'));
            $this->data['server_end_time']   = array('name' => 'server_end_time',
                                                     'id' => 'server_end_time',
                                                     'type' => 'text',
                                                     'class' => 'form-control requiredTextField time end',
                                                     'field-name' => 'Waktu akhir',
                                                     'placeholder' => 'Masukan waktu akhir',
                                                     'value' => $this->form_validation->set_value('server_end_time'));
            $this->data['server_interval']   = array('name' => 'server_interval',
                                                     'id' => 'server_interval',
                                                     'type' => 'text',
                                                     'class' => 'form-control requiredTextField',
                                                     'field-name' => 'Interval',
                                                     'placeholder' => 'Interval',
                                                     'value' => $this->form_validation->set_value('server_interval'));

            //load content
            $this->data['content'] .= $this->load->view('admin/hrd/sync-add', $this->data, true);
            $this->render('admin');
        }

    }

    public function edit_server()
    {
        $id = $this->uri->segment(4);

        if (empty($id)) {
            redirect(SITE_ADMIN . '/hrd_attendance/server_sync');
        } 
        $form_data = $this->hrd_model->get_one('hr_server_sync', $id);

        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/hrd_attendance/server_sync');
        }

        $this->data['form_data'] = $form_data;
        $this->data['subtitle']  = "Edit Server";

        //validate form input 
        $this->form_validation->set_rules('server_start_time', 'Waktu mulai', 'required');
        $this->form_validation->set_rules('server_end_time', 'Waktu akhir', 'required');
        $this->form_validation->set_rules('server_interval', 'Interval', 'required|greater_than[0]|less_than[301]');

        if (isset($_POST) && ! empty($_POST)) {

            if ($this->form_validation->run() === TRUE) {
                $start_time  = $this->input->post('server_start_time');
                $end_time    = $this->input->post('server_end_time');
                $interval    = $this->input->post('server_interval');
                $to_time     = strtotime($start_time);
                $from_time   = strtotime($end_time);
                $diff_minute = round(abs($to_time - $from_time) / 60, 2);
                if ($interval > $diff_minute) {

                    $this->session->set_flashdata('message', 'Interval harus kurang dari perbedaan waktu');
                }
                else {
                    // add task scheduler
                    $this->load->library('MY_scheduler');
                    $name       = $form_data->name;
                    $program    = $this->config->item('php_exe_path') . ' -f ' . FCPATH . 'index.php hrd_scheduler fingerprint_attendances';
                    $start_date = '01/01/2015';
                    $end_date   = '12/12/3015';

                    // $start_date = '2015/01/01';
                    // $end_date   = '3015/12/12';

                    $result = $this->my_scheduler->modify_task($name, $interval, $program, $start_time, $end_time, $start_date, $end_date);
                
                  if ($result) {
                       

                        $data_array = array( 
                                            'controller' => "fingerprint_attendances",
                                            'start_time' => $start_time,
                                            'end_time' => $end_time,
                                            'interval' => $interval);

                        $save = $this->hrd_model->save('hr_server_sync', $data_array, $id);

                        if ($save === false) {
                            $this->session->set_flashdata('message', 'Gagal menyimpan data');
                        }
                        else {
                            $this->session->set_flashdata('message_success', 'Berhasil menyimpan data');
                        }
                    }
                    else {
                        $this->session->set_flashdata('message', 'Gagal menyimpan server. Cek waktu mulai, waktu akhir & interval');
                    }
                }

                $btnaction = $this->input->post('btnAction');
                if ($btnaction == 'save_exit') {
                    redirect(SITE_ADMIN . '/hrd_attendance/server_sync', 'refresh');
                }
                else {
                    redirect(SITE_ADMIN . '/hrd_attendance/edit_server/' . $id, 'refresh');
                }


            }
        }

        //display the edit user form
        $this->data['csrf'] = $this->_get_csrf_nonce();

        //set the flash data error message if there is one
        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');
        $this->data['cancel_url']      = base_url(SITE_ADMIN . '/hrd_attendance/server_sync');

        $this->data['server_sync_url']   = array('name' => 'server_sync_url',
                                                 'id' => 'server_sync_url',
                                                 'type' => 'text',
                                                 'class' => 'form-control requiredTextField',
                                                 'field-name' => 'URL',
                                                 'placeholder' => 'Masukan URL server',
                                                 'disabled' => 'disabled',
                                                 'value' => "fingerprint_attendances"); 
        $this->data['server_start_time'] = array('name' => 'server_start_time',
                                                 'id' => 'server_start_time',
                                                 'type' => 'text',
                                                 'class' => 'form-control requiredTextField time start',
                                                 'field-name' => 'Waktu mulai',
                                                 'placeholder' => 'Masukan waktu mulai',
                                                 'value' => $this->form_validation->set_value('server_start_time', $form_data->start_time));
        $this->data['server_end_time']   = array('name' => 'server_end_time',
                                                 'id' => 'server_end_time',
                                                 'type' => 'text',
                                                 'class' => 'form-control requiredTextField time end',
                                                 'field-name' => 'Waktu akhir',
                                                 'placeholder' => 'Masukan waktu akhir',
                                                 'value' => $this->form_validation->set_value('server_end_time', $form_data->end_time));
        $this->data['server_interval']   = array('name' => 'server_interval',
                                                 'id' => 'server_interval',
                                                 'type' => 'text',
                                                 'class' => 'form-control requiredTextField',
                                                 'field-name' => 'Interval',
                                                 'placeholder' => 'Interval',
                                                 'value' => $this->form_validation->set_value('server_interval', $form_data->interval));
        $this->data['content'] .= $this->load->view('admin/hrd/sync-edit.php', $this->data, true);

        $this->render('admin');
    }

    public function delete_server()
    {
        $id = $this->uri->segment(4);

        if (empty($id)) {
            redirect(SITE_ADMIN . '/hrd_attendance/server_sync');
        } 
        $form_data = $this->hrd_model->get_one('hr_server_sync', $id);

        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/hrd_attendance/server_sync');
        }

        $this->load->library('MY_scheduler');

        $result = $this->my_scheduler->delete_task($form_data->name);
        if ($result) {
            $result = $this->hrd_model->delete('hr_server_sync', $id);
            if ($result) {
                $this->session->set_flashdata('message_success', 'Berhasil menghapus data');
            }
            else {
                $this->session->set_flashdata('message', 'Error(1). Gagal menghapus data');
            }
        }
        else {
            $this->session->set_flashdata('message', 'Error(2). Gagal menghapus data');
        }

        redirect(SITE_ADMIN . '/hrd_attendance/server_sync', 'refresh');
    }

}