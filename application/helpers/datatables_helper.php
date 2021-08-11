<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * datatables_helper.php
 * Author: Alta
 * Date: 09/11/2014
 * Time: 7:16 PM
 */

function check_status($id)
{
    if ($id == '1') {
        return "Published";
    }
    else {
        return "Draft";
    }
}

function check_gender($id)
{
    if ($id == '1') {
        return "Pria";
    }
    else {
        return "Wanita";
    }
}

function check_discount_type($value, $type){
    if($type == '1'){
        return $value.'%';
    }
     else{
        return 'Rp. '.$value;
    }
}

function convert_member_benefit($value){
        return 'Diskon '.$value.'%';
}

function check_store($status){
    if($status){
        return "Semua Cabang";
    }else{
        return "Cabang Tertentu";
    }
}

function convert_date($date){
    $date_tmp = $date;
    $date_tmp = strtotime($date_tmp);
    if($date_tmp != 0){
        return date_format(date_create($date),'Y-m-d') ;
        }
}

function check_total($total){
    if(empty($total)){
        $total = 0;
    }

    return $total;
}

function margin($menu_price, $menu_hpp) {
    $margin = 0;
    $gross = $menu_price - $menu_hpp;
    if ($menu_price == 0) {
      $margin = 0;
    } else {
      $margin = round(($gross / $menu_price) * 100, 1);
    }
    return $margin.'%';
}

function markup($menu_price, $menu_hpp) {
    $markup = 0;
    $gross = $menu_price - $menu_hpp;
    if ($menu_hpp == 0) {
        $markup = 0;
    } else {
        $markup = round(($gross / $menu_hpp) * 100, 1);
    }    
    return $markup.'%';
}

function convert_status($status){
     $status_name = 'undefined';
    switch ($status) {
        case '0':
           $status_name = 'Dibuat';
        break;
        
        case '1':
           $status_name = 'Diberikan';
           
        break; 

        case '2':
           $status_name = 'Dipakai';
            # code...
        break;
    }

    return $status_name;
}

function convert_option_setting($table, $data_id=FALSE){
   $CI =& get_instance();
    $CI->load->model('option_setting_model');
    $data = $CI->option_setting_model->get_option_setting($table, $data_id);
    if($data){
        return $data[0]->data_value;

    }
    return "";
}

function convert_rupiah($value){
    if($value==0){
      return  $value = 'Rp '.$value; 
    }else{
      return  $value = 'Rp '. number_format($value, 0);
    }
}

function convert_compliment_limit($value){

    if($value){
       $value = 'Rp '. number_format($value, 0, "", ".");

   }else{
    $value = 'Tidak ada limit';
    }

    return $value;

}

function convert_date_with_time($date){
    $date_tmp = $date;
    $date_tmp = strtotime($date_tmp);
    if($date_tmp != 0){
      return date_format(date_create($date),'Y-m-d H:i') ;
    }
}

function transaction_periode($start_date, $end_date){

    return $start_date.' - '.$end_date;
}

function concat_name_payment($value, $info){
    if($value == "cash"){
        return "Cash";
    }else{
        return $value." (".$info.")";
    }

}

function convert_status_int($status){
    if($status == 1){
        return "Ya";
    }
    
    return "Tidak";        
}

function convert_local_time($date, $timezone= "in"){
    $formatted_time = strftime('%d %b %Y %H:%M', strtotime($date));
    return $formatted_time;
}

function convert_is_take_away($is_take_away){
    if($is_take_away == 0){
        return "Dine In";
    }
    
    return "Takeaway";  
}

function convert_order_type($is_take_away,$is_delivery){
    if($is_take_away == 1){
        return "Take Away";
    } else{
        if($is_delivery == 1){
            return "Delivery";
        }else{
            return "Dine In";
        }
    }
   
}
function generate_link_order($order_status,$id){
    if($order_status == 0){
        $html = "
         <div class='btn-group'>
                                    <a rel='tooltip' data-tooltip='tooltip'  title='Done'
                                    href='" . base_url(SITE_ADMIN . '/order_company/order_done/'.$id) . "'  class='btn btn-default'>Done</a>
                                    <a rel='tooltip' data-tooltip='tooltip'  title='Edit'
                                    href='" . base_url(SITE_ADMIN . '/order_company/edit/'.$id) . "'  class='btn btn-default'><i class='fa fa-pencil'></i></a>
                                    <a data-tooltip='tooltip' rel='Order' title='Delete'
                                    href='" . base_url(SITE_ADMIN . '/order_company/delete/'.$id) . "' class='btn btn-danger deleteNow' rel='Order '><i class='fa fa-trash-o'></i></a>
                                </div>";
                                return $html;
    }else{
         $html = "Order Selesai";
                                return $html;
    }
}
function set_banquet($is_use_banquet){
    $html = "";
    if($is_use_banquet == 1){
        $html = "Ya";
    }else{
        $html = "Tidak";
    }
    return $html;
}

function set_compliment_ifnull($compliment = 0){  
    if($compliment == null || empty($compliment) || !$compliment){
        $compliment = 0;
    }
    return  $compliment;
}
function check_action_staff($active=0,$id,$group_id){
  $html="";
  if($active==1){
     $html="<div class='btn-group'>
      <a href='" . base_url(SITE_ADMIN . '/hrd_staff/edit_staff/'.$id.'/'.$group_id) . "'  class='btn btn-default'><i class='fa fa-pencil'></i> Edit</a>
      <a href='" . base_url(SITE_ADMIN . '/hrd_staff/delete_staff/'.$id.'/'.$group_id) . "' class='btn btn-danger deleteNow' rel='Staf'><i class='fa fa-trash-o'></i> Hapus</a>
    </div>";
  }else{
     $html="<div class='btn-group'>
      <a href='" . base_url(SITE_ADMIN . '/hrd_staff/paklaring/'.$id) . "' target='_blank' class='btn btn-primary'><i class='fa fa-print'></i> Paklaring</a>
    </div>";
  }
  return $html;
}
function check_target_type($id)
{
    if ($id == '1') {
        return "Target By Total Penjualan";
    }
    else {
        return "Target By Penjualan Item";
    }
}
function check_reward($is_percentage=0,$reward=0)
{
    if ($is_percentage == 1) {
        return $reward." %";
    }
    else {
        return number_format($reward,0,",",".");
    }
}
function generate_action_for_po($id=0,$status=0){
  $html="";
  if($status==0){
    $html="<div class='btn-group'>
      <a href='" . base_url(SITE_ADMIN . '/purchase_order/history/'.$id) . "' class='btn btn-primary'>Detail</a>
    </div>";
  }else{
    $html="<div class='btn-group'>
      <a href='" . base_url(SITE_ADMIN . '/purchase_order/edit/'.$id) . "'  class='btn btn-default'><i class='fa fa-pencil'></i> Edit</a>
      <a href='" . base_url(SITE_ADMIN . '/purchase_order/delete/'.$id) . "' class='btn btn-danger deleteNow' rel='PO'><i class='fa fa-trash-o'></i> Hapus</a>
    </div>";
  }
  return $html;
}
function check_status_po($status=0)
{
  switch ($status) {
    case 1:
      return "Closed";
      break;
    
    case 2:
      return "Open";
      break;

    default:
      return "Open";
      break;
  }
}
function generate_action_for_receive_po($id=0,$status=0){
  $html="";
  if($status==0){
    $html="<div class='btn-group'>
      <a href='" . base_url(SITE_ADMIN . '/receive_stocks/receive/'.$id) . "' class='btn btn-danger'>Terima</a>
    </div>";
  }elseif($status==1){
    $html="<div class='btn-group'>
      <a href='" . base_url(SITE_ADMIN . '/receive_stocks/history/'.$id) . "' class='btn btn-primary'>History</a>
    </div>";
  }else{
    $html="<div class='btn-group'>
      <a href='" . base_url(SITE_ADMIN . '/receive_stocks/receive/'.$id) . "' class='btn btn-danger'>Terima</a>
      <a href='" . base_url(SITE_ADMIN . '/receive_stocks/history/'.$id) . "' class='btn btn-primary'>History</a>
    </div>";
  }
  return $html;
}
function convert_quantity($value){
    if($value==0){
      return 0; 
    }else{
      return round($value,3);
    }
}

/*
*   Added by  : M. Tri Ramdhani
*   Date      : 19/08/2016
*   Function  : generate action for retur order
*/

function generate_action_for_retur_po($id=0, $received=0, $purchase_order_id=0) {
  $CI =& get_instance();
  $CI->db->select('SUM(pored.retur_quantity) AS retur_quantity')
    ->from('purchase_order_receive_detail pord')
    ->join('purchase_order_receive por', 'por.id = pord.purchase_order_receive_id')
    ->join('purchase_order_retur_detail pored', 'pored.purchase_order_receive_detail_id = pord.id', 'left')
    ->join('purchase_order_detail pod', 'pod.id = pord.purchase_order_detail_id')
    ->join('purchase_order po', 'po.id = pod.purchase_order_id')
    ->where('por.id', $id);
  $retured = array_pop($CI->db->get()->result());

  $html = "";
  
  if ($retured->retur_quantity == null) {
    $html="<div class='btn-group'>
      <a href='" . base_url(SITE_ADMIN . '/retur_order/retur/'.$id) . "' class='btn btn-danger'>Retur</a>
    </div>";
  } elseif ($received > $retured->retur_quantity) {
    $html="<div class='btn-group'>
      <a href='" . base_url(SITE_ADMIN . '/retur_order/retur/'.$id) . "' class='btn btn-danger'>Retur</a>
      <a href='" . base_url(SITE_ADMIN . '/retur_order/history/'.$purchase_order_id) . "' class='btn btn-primary'>History</a>
    </div>";
  } elseif ($received == $retured->retur_quantity) {
    $html="<div class='btn-group'>
      <a href='" . base_url(SITE_ADMIN . '/retur_order/history/'.$purchase_order_id) . "' class='btn btn-primary'>History</a>
    </div>";
  }
  return $html;
}


function get_hadir($start_date,$end_date,$value_2){
$CI =& get_instance(); 
    $query = "select count(IF(enum_status_attendance = 1,1,NULL )) as hadir from hr_attendances where date(created_at) >= ".$start_date." and ".$end_date." <=  date(created_at)  and user_id ='".$value_2."'";
  
    $data =  $CI->db->query($query)->row();
 
    
    return $data->hadir;
}

function get_sakit($start_date,$end_date,$value_2){
$CI =& get_instance(); 
    $query = "select count(IF(enum_status_attendance = 4,1,NULL )) as sakit from hr_attendances where date(created_at) >= ".$start_date." and ".$end_date." <=  date(created_at) and user_id ='".$value_2."'";
  
    $data =  $CI->db->query($query)->row();
 
    
    return $data->sakit;
}

function get_cuti($start_date,$end_date,$value_2){
$CI =& get_instance(); 
    $query = "select count(IF(enum_status_attendance = 6,1,NULL )) as cuti from hr_attendances where date(created_at) >= ".$start_date." and ".$end_date." <=  date(created_at) and user_id ='".$value_2."'";
  
    $data =  $CI->db->query($query)->row();
 
    
    return $data->cuti;
}

function get_ijin($start_date,$end_date,$value_2){
$CI =& get_instance(); 
    $query = "select count(IF(enum_status_attendance = 3,1,NULL )) + count(IF(enum_status_attendance = 7,1,NULL )) as ijin from hr_attendances where date(created_at) >= ".$start_date." and ".$end_date." <=  date(created_at) and user_id ='".$value_2."'";
  
    $data =  $CI->db->query($query)->row();
 
    
    return $data->ijin;
}


function interval($value_1, $value_2,$value_3){

    if ($value_1 == 0)
    {
            $value_1 = $value_3;
    }

    $time_1 = strtotime($value_1);
    $time_2 = strtotime($value_2);

    $time = abs($time_2 - $time_1);
    $minutes = round($time / 60);
    $hour = round($minutes / 60);

      return  $hour;

}

function interval_menit($value_1, $value_2,$value_3){

    if ($value_1 == 0)
    {
            $value_1 = $value_3;
    }

    $time_1 = strtotime($value_1);
    $time_2 = strtotime($value_2);

    $time = abs($time_2 - $time_1);
    $minutes = round($time / 60);
    $hour = round($minutes / 60);

      return  $minutes;

}

function endtime($date) {
 if ($date == '0000-00-00'){
        $result = "Selama Bekerja";
 } else {
        $date_tmp = $date;
        $result = date($date_tmp);
 }
 
      return  $result;

}
// menghitung cost refund (selisih nominal sebelum dan sesudah refund)
function get_cost_refund($id) {
  $CI =& get_instance();
  $cost_refund = 0;
  $refund = $CI->store_model->get_one("refund", $id);

  // get bill before refund
  $old_bill = array_pop($CI->store_model->get_all_where('bill', array('is_refund' => 1, 'refund_key' => $refund->refund_key)));

  // get bill after refund
  $new_bill = array_pop($CI->store_model->get_all_where('bill', array('is_refund' => 0, 'refund_key' => $refund->refund_key)));
  if (empty($new_bill)) {
    $new_bill = array_pop($CI->store_model->get_all_where('bill', array('created_at' => $refund->created_at)));
  }

  // get total price  
  $before_refund = $old_bill->total_price;
  $after_refund = $new_bill->total_price;
  $cost_refund = $before_refund - $after_refund;

  return convert_rupiah($cost_refund);
}

// menghitung cost_spoiled inventory
function get_cost_spoiled($inventory_id, $is_warehouse) {
  $CI =& get_instance();
  $cost = 0;
  if ($is_warehouse != 1) {
    $get_stock = $CI->stock_model->get_all_where('stock_history', array('inventory_id' => $inventory_id, 'status' => 7));
    if (!empty($get_stock)) {
      foreach ($get_stock as $stock) {
        $cost += $stock->price * $stock->quantity * -1;            
      }
    }
  }
  return convert_rupiah($cost);
}

// mengecek format ongkos kirim (nominal rupiah atau persentasi)
function check_is_percentage($number,$is_percentage){
    if($is_percentage == 1){
       return $number." %";
    }else{
      return 'Rp '. number_format($number, 0, "", ".");
    }
}

function get_reservation_action_button($id, $reservation_status,$book_date, $feature_confirmation){
  $feature_confirmation = str_replace("-", ",", $feature_confirmation);
	$action_button = "<div style='text-align: center'>";
	if(date("Y-m-d H:i:s", strtotime($book_date)) >= date("Y-m-d H:i:s") && $reservation_status == "1"){
		$action_button .= "<a rel='tooltip' title='Edit' href='" . base_url('reservation/edit/'.$id) . "'  class='btn btn-default'><i class='fa fa-pencil'></i></a>";
	}
	
  if ($reservation_status != "3") {
    $action_button .=  "<a rel='reservasi' title='Delete' data-id='".$id."' class='btn btn-danger btn-reserv-delete' feature_confirmation='" . ($feature_confirmation) . "' rel='reservation'><i class='fa fa-trash-o'></i></a>";	
	   $action_button .=  "<a rel='tooltip' title='Cetak' href='javascript:void(0);' reservation_id='".$id."'  class='btn btn-primary reservation_print'><i class='fa fa-print'></i></a>";
  }
	$action_button .= "</div>";
	return $action_button;
}

function get_member_choice_button($id, $name, $member_code) {
  $action_button = "<div style='text-align: center'>";
  $action_button .=  "<a rel='tooltip' title='Pilih' href='javascript:void(0);' member_id='".$id."' member_name='".$name."' member_code='".$member_code."' class='btn btn-primary choose_member'>Pilih</a>";
  $action_button .= "</div>";
  return $action_button;
}

function set_enum_order_type($order_type){
     switch ($order_type) {
       case 1:
         return "Dine In";
         break;
        case 2:
         return "Takeaway";
         break;
         case 3:
         return "Delivery";
         break;
       
       default:
         return "Dine In";
         break;
     }
}