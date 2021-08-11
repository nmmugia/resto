<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Created by DIOS.
* User: Alta Falconeri
* Date: 2/9/2015
* Time: 8:59 AM
*/
class Hrd_scheduler extends CI_Controller
{
    private $_url;
    function __construct()
    {
        parent::__construct();
        $this->load->model('hrd_model');
        

        $data_settings = $this->hrd_model->get_where('hr_setting', array());
        
        $this->ip_fingerprint = "192.168.2.10";
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
    public function fingerprint_attendances(){ 
      $this->load->helper(array('attendances'));
      $parameter = array(  "IP" =>$this->ip_fingerprint); 
      $return_data = array();
      $return_data['status'] = true;
      $return_data['data'] = array();
      $return_data['message'] = "";
      $this->save_all_activity_fp();
      $data = fingerprint_attendances($parameter);


      $delete = array(
                    'created_at'      => date("Y-m-d")
                );

      $db_delete_data = $this->hrd_model->delete_by_limit('hr_attendances', $delete ,0);


      foreach ($data as $key => $value) { 

        if($value['date'] == date("Y-m-d")){

          $check_data_user =  $this->hrd_model->get_one('users', $value['user_id']);
          $cond = array(
            "user_id"=>$value['user_id'],
            "date"=>$value['date']
          );
          $holiday=$this->hrd_model->get_all_where("hr_employee_holidays",array("user_id"=>$value['user_id'],"day"=>date("D",strtotime($value['date']))));
          $schedules = $this->hrd_model->get_schedules_where($cond); 
          $checking=FALSE;
          if(sizeof($holiday)>0){
              $save_data = array(
                  'user_id'      => $value['user_id'],
                  'enum_status_attendance' => 1
              );
              $checking=true;
          }else{
              foreach ($schedules as $schedule) {

                  $save_data = array(
                      'user_id'      => $value['user_id']
                  );
                  $checkin_from = date("H:i:s", strtotime($schedule->start_time." -".$this->range_attendance." minutes"));
                  $checkin_max_late = date("H:i:s", strtotime($schedule->start_time." +".$this->max_late." minutes"));
                  $checkout_from = date("H:i:s", strtotime($schedule->end_time));
                  $checkout_max_late = date("H:i:s", strtotime($schedule->end_time." +".$this->range_attendance." minutes"));
                  if(strtotime($value['time_in'])<strtotime($checkin_from)){
                      //MASUK SEBELUM JADWAL
                      $save_data['over_checkin_time'] = $value['time_in'];
                      $save_data['enum_status_attendance'] = 0;
                  }elseif(strtotime($value['time_in']) >= strtotime($checkin_from) && strtotime($value['time_in'])<=strtotime($checkin_max_late)) {
                      //MASUK TEPAT WAKTU
                      $save_data['enum_status_attendance'] = 1;
                      $save_data['checkin_time'] = $value['time_in'];
                  }elseif($value['time_in'] > $checkin_max_late ){
                      //MASUK TERLAMBAT
                      $save_data['enum_status_attendance'] = 2;
                      $save_data['checkin_time'] = $value['time_in'];
                  }
                  if($value['time_out'] >= $checkout_from && $value['time_out'] <= $checkout_max_late){
                      $save_data['checkout_time'] = $value['time_out'];
                  }elseif($value['time_out'] > $checkout_max_late ){
                      $save_data['over_checkout_time'] = $value['time_out'];
                  }

                  $checking=true;
              }
          }
          $db_save_data=false;
          if($checking==true){
            $save_data['created_at'] = $value['date'];

            //$db_delete_data = $this->hrd_model->delete_by_where('hr_attendances', $save_data );
            $db_save_data = $this->hrd_model->save('hr_attendances', $save_data);                     
          }
        }
      }
      // clear_fingerprint($parameter);
      echo json_encode($return_data);
    }

    public function save_all_activity_fp(){
        $this->load->helper(array('attendances'));
        $parameter = array(  "IP" =>$this->ip_fingerprint); 
        $data_all = get_all_activities_fp($parameter); 
        // $total = 0;
        // $total_success = 0;
        // $total_error = 0;

        $file = fopen('uploads/history_attendance.csv', 'w');
        foreach ($data_all as $value) {

            //if($value['date'] == date("Y-m-d")){
            $user_name =  $this->hrd_model->get_one('users', $value['user_id']);

            $save_data = array(
                "user_id"=>$value['user_id'],
                "user_name" =>$user_name->name,
                "date"=>$value['date'],
                "time"=>$value['time']

            );
            
            fputcsv($file, $save_data);

            //$db_delete_data = $this->hrd_model->delete_by_where('hr_history_fingerprint', $save_data );
            //$save_data = $this->hrd_model->save('hr_history_fingerprint', $save_data);

            // if($save_data){
            //   $total_success++;
            // }else{
            //   $total_error++;
            // }
            // $total++;
          } 
       // }

        fclose($file);

        // if($total == ($total_success + $total_error) && $total_error == 0){
        //   return true;   
        // }else{
        //   return false; 
        // } 
        return true;
    }

    public function save_all_fingerprint(){
      $this->load->helper(array('attendances'));
      $parameter = array(  "IP" =>$this->ip_fingerprint); 

      $data_all = fingerprint_attendances($parameter); 
      $total = 0;
      $total_success = 0;
      $total_error = 0;
      $return_data = array();
      $return_data['status'] = true;
      $return_data['data'] = array();
      $return_data['message'] = "";

     

      $start_date = $this->input->post('start_date');
      $end_date = $this->input->post('end_date');
      // $file = fopen('uploads/history_attendance.csv', 'w');
      foreach ($data_all as $value) { 
        if($value['date'] >= $start_date && $value['date'] <= $end_date ){
          //SAVE TO HR_HISTORY_FINGERPRINT
          $parameter_delete_fingerprint = array(
              "user_id"=>$value['user_id'],
              "date"=>$value['date'],
              "time"=>$value['time_in'] ,
              "status"=>$value['status'] 
          );
          // fputcsv($file, $parameter_delete_fingerprint);

          $db_delete_data = $this->hrd_model->delete_by_where('hr_history_fingerprint', $parameter_delete_fingerprint );

          $save_data_fingerprint = $this->hrd_model->save('hr_history_fingerprint', $parameter_delete_fingerprint); 

          //SAVE TO HR_ATTENDANCES
          $cond = array(
            "user_id"=>$value['user_id'],
            "date"=>$value['date']
          );

          $delete = array(
              "user_id"=>$value['user_id'],
              "created_at"=>$value['date']
          );

          $db_delete_data_att = $this->hrd_model->delete_by_where('hr_attendances', $delete);

          $holiday=$this->hrd_model->get_all_where("hr_employee_holidays",array("user_id"=>$value['user_id'],"day"=>date("D",strtotime($value['date']))));
          $schedules = $this->hrd_model->get_schedules_where($cond); 
          $checking=FALSE;
          if(sizeof($holiday)>0){
              $save_data = array(
                  'user_id'      => $value['user_id'],
                  'enum_status_attendance' => 1
              );
              $checking=true;
          }else{
              foreach ($schedules as $schedule) {

                  $save_data = array(
                      'user_id'      => $value['user_id']
                  );
                  $checkin_from = date("H:i:s", strtotime($schedule->start_time." -".$this->range_attendance." minutes"));
                  $checkin_max_late = date("H:i:s", strtotime($schedule->start_time." +".$this->max_late." minutes"));
                  $checkout_from = date("H:i:s", strtotime($schedule->end_time));
                  $checkout_max_late = date("H:i:s", strtotime($schedule->end_time." +".$this->range_attendance." minutes"));
                  if(strtotime($value['time_in'])<strtotime($checkin_from)){
                      //MASUK SEBELUM JADWAL
                      $save_data['over_checkin_time'] = $value['time_in'];
                      $save_data['enum_status_attendance'] = 0;
                  }elseif(strtotime($value['time_in']) >= strtotime($checkin_from) && strtotime($value['time_in'])<=strtotime($checkin_max_late)) {
                      //MASUK TEPAT WAKTU
                      $save_data['enum_status_attendance'] = 1;
                      $save_data['checkin_time'] = $value['time_in'];
                  }elseif($value['time_in'] > $checkin_max_late ){
                      //MASUK TERLAMBAT
                      $save_data['enum_status_attendance'] = 2;
                      $save_data['checkin_time'] = $value['time_in'];
                  }
                  if($value['time_out'] >= $checkout_from && $value['time_out'] <= $checkout_max_late){
                      $save_data['checkout_time'] = $value['time_out'];
                  }elseif($value['time_out'] > $checkout_max_late ){
                      $save_data['over_checkout_time'] = $value['time_out'];
                  }

                  $checking=true;
              }
          }
          $db_save_data=false;
          if($checking==true){
            $save_data['created_at'] = $value['date']; 
            $db_save_data = $this->hrd_model->save('hr_attendances', $save_data);                     
          }
        } 

      }
      // fclose($file);
      echo json_encode($return_data); 
    }
 }