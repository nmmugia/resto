<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * datatables_helper.php
 * Author: FAZHAL
 * Date: 09/11/2014
 * Time: 7:16 PM
 */
 
function set_enum_enhancer($is_enhancer){
     $html = "";
    if($is_enhancer == 1){
        $html = "Penambah";
    }else{
        $html = "Pengurang";
    }
    return $html;
}


function set_status_attendance($enum_status_attendance,$user_id){
    $html = "";
    //checking kalo jam pulang masih kosong tombol absen masih ada.
    $cond = array(
        "user_id" => $user_id,
        "created_at" => date("Y-m-d"),
        "checkout_time " =>NULL,
        "over_checkout_time " =>NULL
    );
    $CI =& get_instance();
    $CI->load->model('hrd_model');
    $data = $CI->hrd_model->get_where("hr_attendances",$cond);
    if($enum_status_attendance != "" && !$data){
        $html = "-";
    }else{
      $html="<div class='btn-group'>
            <a href='" . base_url(SITE_ADMIN . '/hrd_attendance/add_attendance/'.$user_id)."' class='btn btn-default' rel='Absen'>Absen</a>
              </div>
      ";
    }
    return $html;
}

function set_status_tindakan($enum_status_attendance,$user_id,$date=""){
    $html = "";
     
    
    // if($enum_status_attendance != ""){
        // $html = "";
    // }else{ 
        $html="<div class='btn-group'>
              <a href='" . base_url(SITE_ADMIN . '/hrd_attendance/set_perfomance/'.$user_id."/".$date)."' class='btn btn-default' rel='Reimburse'>Tindakan</a>
                </div>
        ";
    // }
    return $html;
}

function set_status_repayment_method($repayment_method){
     
    $CI =& get_instance();
    $CI->load->model('hrd_model');
    $data = $CI->hrd_model->get_enum_repayment_method($repayment_method);
    if($data){
        return $data[0]->value;

    } 
    return "-";
}

function set_attendance_text($enum_status_attendance){
   
    $CI =& get_instance();
    $CI->load->model('hrd_model');
    $data = $CI->hrd_model->get_enum_attendance_byid($enum_status_attendance);
    if($data){
        return $data[0]->name; 
    } 
    return "-";
}

function set_percentage_appraisal($total_nilai){
    
    $percentage = 1/2;
    return $total_nilai;
}