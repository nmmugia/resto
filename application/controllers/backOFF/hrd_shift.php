<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by BOSRESTO.
 * User: AZIS
 * Date: 09/06/2016
 * Time: 14:00 PM
 */
class Hrd_shift extends Hrd_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) { 
            redirect(SITE_ADMIN . '/login', 'refresh');
        }else{
            $this->load->model('hrd_model');
            $this->load->model('user_model');
            $this->load->model('categories_model');
            $this->load->library('encrypt');
        } 
    }

    public function index()
    {
        $this->data['title']    = "Kelola Jadwal / Shift";
        $this->data['subtitle'] ="Kelola Jadwal / Shift";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');


        $this->data['pegawai']= $this->user_model->get_user_by_schedule();
        $this->data['url_change'] = SITE_ADMIN.'/hrd_shift/change_shift';
        $this->data['url_move_shift'] = SITE_ADMIN.'/hrd_shift/move_shift_process';
        $this->data['url_rolling'] = SITE_ADMIN.'/hrd_shift/rolling_shifts';

        $this->data['office_hour']= $this->hrd_model->get('hr_office_hours')->result();

        $this->data['office_hours']= $this->hrd_model->get("hr_office_hours")->result();
        $this->data['office_hour_targets']= $this->hrd_model->get("hr_office_hours")->result();

        $this->data['description'] = array(
        'name' => 'description',
        'id' => 'description',
        'type' => 'text',
        'class' => 'form-control',
        'field-name' => 'Deskripsi',
        'placeholder' => 'Masukan Alasan',
        'value' => ''

        );

        $this->data['data_url'] = base_url(SITE_ADMIN . '/hrd_shift/get_data_user_move');;
        $this->data['data_url'] = base_url(SITE_ADMIN . '/hrd_shift/get_data_user');;
        $this->data['content'] .= $this->load->view('admin/hrd/shift_view', $this->data, true);
        $this->render('hrd');
    }

    public function ch_shift()
    {
        $this->data['title']    = "Tukar Jadwal / Shift";
        $this->data['subtitle'] ="Tukar Jadwal / Shift";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['description'] = array(
        'name' => 'description',
        'id' => 'description',
        'type' => 'text',
        'class' => 'form-control',
        'field-name' => 'Deskripsi',
        'placeholder' => 'Masukan Alasan',
        'value' => ''

        );

        $this->data['data_url'] = base_url(SITE_ADMIN . '/hrd_shift/get_data_user');;
        $this->data['content'] .= $this->load->view('admin/hrd/shift_view_list', $this->data, true);
        $this->render('hrd');
    }

    public function move_shift()
    {
        $this->data['title']    = "Pindah Jadwal / Shift";
        $this->data['subtitle'] ="Pindah Jadwal / Shift";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');


        $this->data['pegawai']= $this->user_model->get_user_by_schedule();
        $this->data['office_hour']= $this->hrd_model->get('hr_office_hours')->result();

        $this->data['data_url'] = base_url(SITE_ADMIN . '/hrd_shift/get_data_user_move');;
        $this->data['content'] .= $this->load->view('admin/hrd/move_shift_view_list', $this->data, true);
        $this->render('hrd');
    }

     public function get_data_user()
    {
     
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));  
        
        $this->datatables->select('u.name, hs.start_date, IF(hs.end_date = "0000-00-00", "Selamanya", hs.end_date) as end_date, hsd.start_time, hsd.end_time, ho.name as ofname', false)
            ->from('users u')
            ->join('hr_schedules hs','hs.user_id=u.id')
            ->join('hr_schedule_detail hsd', 'hsd.schedule_id=hs.id')
            ->join('hr_office_hours ho','ho.id=hsd.office_hour_id' )


            ->where('u.active',1);
        echo $this->datatables->generate();
    }
    
    public function get_data_user_move()
    {
     
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));  
        
        $this->datatables->select('u.name, hs.start_date, IF(hs.end_date = "0000-00-00", "Selamanya", hs.end_date) as end_date, hsd.start_time, hsd.end_time, ho.name as ofname', false)
            ->from('users u')
            ->join('hr_schedules hs','hs.user_id=u.id')
            ->join('hr_schedule_detail hsd', 'hsd.schedule_id=hs.id')
            ->join('hr_office_hours ho','ho.id=hsd.office_hour_id' )


            ->where('u.active',1);
        echo $this->datatables->generate();
    }

     function get_data_employee_by_office_hour_1()
    {
      $office_hour_id=$this->input->post("office_hour_id");
      $office_hour=$this->hrd_model->get_one("hr_office_hours",$office_hour_id);
      $results=$this->hrd_model->get_office_hour_active(array("office_hour_id"=>$office_hour_id));
      $results=$this->hrd_model->get_detail_employee_office_hour_1(array("office_hour_id"=>$office_hour_id));

      $content="";
      foreach($results as $r){
        $content.="<option value='".$r->user_id."'>".$r->name."</option>";
      }
      echo json_encode(array(
        "content"=>$content
      ));
    }

    public function move_shift_process()
    {

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');


        $employee       = $this->input->post('employee');
        $office_hour    = $this->input->post('office_hour');
        $start_date     = $this->input->post('start_date');
        $end_date       = $this->input->post('end_date');
        $repeat         = $this->input->post('repeat_exchange');

        if($repeat == 1){

            $end_date = "0000-00-00";
        }

        $office_hour    = $this->hrd_model->get_one("hr_office_hours",$office_hour);
        if($office_hour){

        }
        for($x=0;$x<sizeof($employee);$x++){
            $user_id=$employee[$x]; 
            $get_user = $this->hrd_model->get_schedule_start_date($user_id); 
            $save_data = array(
              'start_date'          => $start_date,
              'end_date'            => $end_date,
              'enum_repeat'         => $repeat,
              'free_day'            => 0,
              'is_special_schedule' => 0,
              'has_sync'            => 0
            );

            $save_data = $this->hrd_model->update_where('hr_schedules', $save_data, array('id' => $get_user[0]->id ));
            $save_detail = array(

                'office_hour_id'    => $office_hour->id,
                'start_time'        => $office_hour->checkin_time,
                'end_time'          => $office_hour->checkout_time
            );
            $this->hrd_model->update_where('hr_schedule_detail', $save_detail, array('schedule_id' => $get_user[0]->id ));
            $this->session->set_flashdata('message_success', "Berhasil Menukar Jadwal / Shift");     
            redirect(base_url(SITE_ADMIN.'/hrd_shift','refresh'));
      }    
    }

    public function change_shift()
    {

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');


        $user_1         = $this->input->post('user_1');
        $user_2         = $this->input->post('user_2');
        $start_date     = $this->input->post('start_date');
        $end_date       = $this->input->post('end_date');
        $repeat         = $this->input->post('repeat_exchange');
        $description    = $this->input->post('description');

        if($repeat == 1){

            $end_date = "0000-00-00";
        }

        $data_user_1 = $this->hrd_model->get_user_shift($user_1);
        $data_user_2 = $this->hrd_model->get_user_shift($user_2);


        if (isset($_POST) && !empty($_POST)) {

            if($start_date == $data_user_1[0]->start_date){

                $update_data_hs = array(
                'start_date'            => $data_user_1[0]->start_date,
                'end_date'              => $end_date,
                'enum_repeat'           => $repeat
                );

                $update_data_hsd = array(
                'start_time'            => $data_user_2[0]->start_time,
                'end_time'              => $data_user_2[0]->end_time,
                'office_hour_id'        => $data_user_2[0]->office_hour_id
                );


                $update_data_hs = $this->hrd_model->update_where('hr_schedules', $update_data_hs, array('id' => $data_user_1[0]->id )); 
                $update_data_hsd = $this->hrd_model->update_where('hr_schedule_detail', $update_data_hsd, array('schedule_id' => $data_user_1[0]->schedule_id )); 
                
              
            } else {

                $save_data_hs = array(
                'user_id'               => $user_1,
                'start_date'            => $start_date,
                'end_date'              => $end_date,
                'enum_repeat'           => $repeat
                );

                $save_data_hsd = array(
                'schedule_id'           => $data_user_1[0]->schedule_id,
                'start_time'            => $data_user_2[0]->start_time,
                'end_time'              => $data_user_2[0]->end_time,
                'office_hour_id'        => $data_user_2[0]->office_hour_id
                );

                $save_data_hs = $this->hrd_model->save('hr_schedules', $save_data_hs);
                $save_data_hsd = $this->hrd_model->save('hr_schedule_detail', $save_data_hsd); 

             
            }

            if($start_date == $data_user_2[0]->start_date){

                $update_data_hs2 = array(
                'start_date'            => $data_user_2[0]->start_date,
                'end_date'              => $end_date,
                'enum_repeat'           => $repeat
                );

                $update_data_hsd2 = array(
                'start_time'            => $data_user_1[0]->start_time,
                'end_time'              => $data_user_1[0]->end_time,
                'office_hour_id'        => $data_user_1[0]->office_hour_id
                );


                $update_data_hs2 = $this->hrd_model->update_where('hr_schedules', $update_data_hs2, array('id' => $data_user_2[0]->id )); 
                $update_data_hsd2 = $this->hrd_model->update_where('hr_schedule_detail', $update_data_hsd2, array('schedule_id' => $data_user_2[0]->schedule_id )); 
                
                
            } else {

                $save_data_hs2 = array(
                'user_id'               => $user_2,
                'start_date'            => $start_date,
                'end_date'              => $end_date,
                'enum_repeat'           => $repeat
                );

                $save_data_hsd2 = array(
                'schedule_id'           => $data_user_2[0]->schedule_id,
                'start_time'            => $data_user_1[0]->start_time,
                'end_time'              => $data_user_1[0]->end_time,
                'office_hour_id'        => $data_user_1[0]->office_hour_id
                );

                $save_data_hs2 = $this->hrd_model->save('hr_schedules', $save_data_hs2);
                $save_data_hsd2 = $this->hrd_model->save('hr_schedule_detail', $save_data_hsd2); 

             
            }
            

            $save_excuse = array(
                'user_id'           => $user_1,
                'ex_user_id'        => $user_2,
                'created_at'        => date("Y-m-d"),
                'note'              => $description
                );

            $save_excuse = $this->hrd_model->save('hr_schedule_change', $save_excuse);
                 
            $this->session->set_flashdata('message_success', "Berhasil Menukar Shift");     
            redirect(base_url(SITE_ADMIN.'/hrd_shift/index#people-tab','refresh'));
        } 
          
    } 

    public function exchange_shift()
    {
        
      $this->form_validation->set_rules('start_date', 'Tanggal Mulai', 'required');
      $this->form_validation->set_rules('from_office_hour', 'Dari Jam Kerja', 'required');
      $this->form_validation->set_rules('to_office_hour', 'Sampai Jam Kerja', 'required');

      if ($this->form_validation->run()==TRUE) {
        $from_office_hour_id=$this->input->post("from_office_hour");
        $to_office_hour_id=$this->input->post("to_office_hour");
        $employees_from=$this->input->post("employees_from");
        $employees_to=$this->input->post("employees_to");
        $start_date=$this->input->post("start_date");
        $end_date=$this->input->post("end_date");
        $repeat=$this->input->post("repeat");
        $from_office_hour=$this->hrd_model->get_one("hr_office_hours",$from_office_hour_id);
        $to_office_hour=$this->hrd_model->get_one("hr_office_hours",$to_office_hour_id);
        $from_results=$this->hrd_model->get_office_hour_active(array("office_hour_id"=>$from_office_hour->id));
        $to_results=$this->hrd_model->get_office_hour_active(array("office_hour_id"=>$to_office_hour->id));
        $temp_from_results=array();
        $temp_to_results=array();

        foreach ($from_results as $r) {
            array_push($temp_from_results,$r->user_id);  
        }
        foreach ($to_results as $r) {
            array_push($temp_to_results,$r->user_id);
        }
       


        for($x=0;$x<sizeof($employees_from);$x++){
          $user_id=$employees_from[$x];
          if(!in_array($user_id,$temp_from_results)){
            $save_data = array(
              'user_id'             => $user_id,
              'start_date'          => $start_date,
              'end_date'            => $end_date,
              'enum_repeat'         => $repeat,
              'free_day'            => 0,
              'is_special_schedule' => 0
            );
            $save_data = $this->hrd_model->save('hr_schedules', $save_data); 
            if($save_data){
              $save_detail = array(
                'schedule_id'       => $save_data,
                'office_hour_id'    => $from_office_hour->id,
                'start_time'        => $from_office_hour->checkin_time,
                'end_time'          => $from_office_hour->checkout_time
              );
              $this->hrd_model->save('hr_schedule_detail', $save_detail);
            }            
          }
        }
        for($x=0;$x<sizeof($employees_to);$x++){
          $user_id=$employees_to[$x];
          if(!in_array($user_id,$temp_to_results)){
            $save_data = array(
              'user_id'             => $user_id,
              'start_date'          => $start_date,
              'end_date'            => $end_date,
              'enum_repeat'         => $repeat,
              'free_day'            => 0,
              'is_special_schedule' => 0
            );
            $save_data = $this->hrd_model->save('hr_schedules', $save_data); 
            if($save_data){
              $save_detail = array(
                'schedule_id'       => $save_data,
                'office_hour_id'        => $to_office_hour->id,
                'start_time'        => $to_office_hour->checkin_time,
                'end_time'          => $to_office_hour->checkout_time
              );
              $this->hrd_model->save('hr_schedule_detail', $save_detail);
            }            
          }
        }
        $this->session->set_flashdata('message_success', "Berhasil Menukar Shift");
        
      }else{
        redirect(base_url(SITE_ADMIN."/hrd_shift/index#move-tab"));
      }
       
    }
    function days_diff($d1, $d2) {
            $d1 = strtotime($d1);
            $d2 = strtotime($d2);
            return ($d2 - $d1)/60/60/24;
        
    }

    public function rolling_shifts()
    {  

        if (isset($_POST) && !empty($_POST)) {

            $start_date=$this->input->post("start_date_rolling");
            $end_date=$this->input->post("end_date_rolling");
            $repeat=$this->input->post("repeat-rolling");
            $detail=$this->input->post("detail"); 
            
            $employees = $this->hrd_model->get_users_schedules($start_date,$end_date);
            $office_hours = $this->hrd_model->get("hr_office_hours")->result();
            $office_hour_targets=array();
            foreach($office_hours as $o){
                $office_hour_targets[$o->id]= $o;
            }
           
            foreach ($employees as $employee) { 
                //DELETE SCHEDULES AND SCHEDULE_DETAIL WHERE START_DATE AND END_DATE
                $this->store_model->delete_by_where("hr_schedules",array("id"=>$employee->hr_schedule_id));
                $this->store_model->delete_by_where("hr_schedule_detail",array("id"=>$employee->hr_schedule_detail_id));

                $new_office_hours_id    = $detail[$employee->office_hour_id]; 
                $new_checkout_time          = $office_hour_targets[$new_office_hours_id]->checkout_time;
                $new_checkin_time           = $office_hour_targets[$new_office_hours_id]->checkin_time;
                $save_detail = array(
                    'office_hour_id'    => $new_office_hours_id 
                 );
               
                //INSERT FIRST RANGE DATE
                 $total_difference_day =  $this->days_diff($employee->start_date,$start_date);
                if($total_difference_day > 0){
                    $new_end_date = date('Y-m-d', strtotime('-'.$total_difference_day.' day', strtotime($start_date)));
                }else{
                    $new_end_date = $end_date;
                } 


                $save_data_1 = array( 
                  'user_id'             => $employee->user_id,
                  'start_date'          => $employee->start_date,
                  'end_date'            => $new_end_date,
                  'enum_repeat'         => $repeat
                );
                $saved1 = $this->hrd_model->save('hr_schedules', $save_data_1);
                if($saved1){
                    $save_detail['schedule_id'] = $saved1;
                    if(strtotime($employee->start_date) < strtotime($start_date) ){
                        $save_detail['start_time'] = $employee->start_time;
                        $save_detail['end_time'] = $employee->end_time;
                    }else{
                        $save_detail['start_time'] = $new_checkin_time;
                        $save_detail['end_time'] = $new_checkout_time;
                    }
                    $saved_detail1 = $this->hrd_model->save('hr_schedule_detail', $save_detail);
                }
                //INSERT MIDDLE RANGE DATE
                if(strtotime($employee->start_date) < strtotime($start_date) ){ 
                    $save_data_2 = array( 
                      'user_id'           => $employee->user_id,
                      'start_date'          => $start_date,
                      'end_date'            => $end_date,
                      'enum_repeat'         => $repeat
                    );
                    $saved2 = $this->hrd_model->save('hr_schedules', $save_data_2);
                    if($saved2){ 
                        $save_detail['start_time'] = $new_checkin_time;
                        $save_detail['end_time'] = $new_checkout_time;
                        $save_detail['schedule_id'] = $saved2;
                        $saved_detail2 = $this->hrd_model->save('hr_schedule_detail', $save_detail);
                    }
                }
                //INSERT LAST RANGE DATE
                if(strtotime($employee->end_date) > strtotime($end_date) ){ 
                    $save_data_3 = array( 
                      'user_id'             => $employee->user_id,
                      'start_date'          => date('Y-m-d', strtotime('+1 day', strtotime($end_date))),
                      'end_date'            => $employee->end_date,
                      'enum_repeat'         => $repeat
                    );    
                    $saved3 = $this->hrd_model->save('hr_schedules', $save_data_3);
                    if($saved3){

                        $save_detail['schedule_id'] = $saved3;
                        $save_detail['start_time'] = $employee->start_time;
                        $save_detail['end_time'] = $employee->end_time;
                        $saved_detail3 = $this->hrd_model->save('hr_schedule_detail', $save_detail);
                    }
                } 
            } 
            $this->session->set_flashdata('message_success', "Berhasil Menukar Shift");
            redirect(base_url(SITE_ADMIN."/hrd_shift/index#group-tab"));
        }else{
            redirect(base_url(SITE_ADMIN."/hrd_shift/index"));
        }
     
    }

    public function manage_shift() {
        $this->data['title'] = "Kelola Shift Group";
        $this->data['subtitle'] = "Kelola Shift Group"; 

        $this->data['url_rolling'] = SITE_ADMIN.'/hrd_shift/rolling_shifts';
        $this->data['office_hours']= $this->hrd_model->get("hr_office_hours")->result();
        $this->data['office_hour_targets']= $this->hrd_model->get("hr_office_hours")->result();
        $this->data['content'] .= $this->load->view('admin/hrd/shift_management', $this->data, true);
        $this->render('hrd');
    }
}