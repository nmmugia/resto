<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
ini_set('display_errors', 'off');
error_reporting(0);
/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/31/2014
 * Time: 8:10 AM
 */

/**
 * developed by 
 * User: Fitria Kartika
 */

/**
 * Printer Helpers
 *
 * Print receipt direct to printer
 *
 */
 
function get_printer_kitchen_setting()
{
  $printer_kitchen = array();
  $CI =& get_instance();
  $CI->load->model('store_model');
  $printer_kitchen = $CI->store_model->get_by('master_general_setting', "printer_kitchen","name"); 
  return $printer_kitchen;
}

function get_printer_checker_setting()
{
  $printer_checker = array();
  $CI =& get_instance();
  $CI->load->model('store_model');
  $printer_checker = $CI->store_model->get_by('master_general_setting', "printer_checker","name"); 
  return $printer_checker;
}

function get_printer_cashier_setting()
{ 
  $printer_cashier = array();
  $CI =& get_instance();
  $CI->load->model('store_model');
  $printer_cashier = $CI->store_model->get_by('master_general_setting', "printer_cashier","name"); 
  return $printer_cashier;
}
function get_printer_setting()
{ 
  $printer_cashier = array();
  $CI =& get_instance();
  $CI->load->model('store_model');
  $printer_cashier = $CI->store_model->get_by('master_general_setting', "printer_cashier","name"); 
  return $printer_cashier;
}
function convert_printer_name($printer_name='') {
  $temp=str_replace("\\\\","",$printer_name);
  $temp2=explode("\\",$temp);
  if(sizeof($temp2)>1){
    $return="\\\\".$temp2[0]."\\".$temp2[1];
  }else{
    $return=$temp2[0];
  }
  return $return;
}
function print_receive_po($printer_name='',$print_data=array())
{
  $printer_name=convert_printer_name($printer_name);
  if (function_exists('printer_open')) {
      // set up
      $yPos                = 20;

       $handle = printer_open($printer_name);
     
      // $handle=true;
   if($handle==false)return false;
      printer_set_option($handle, PRINTER_MODE, "RAW"); // cut mode
      printer_start_doc($handle,"Start Doc");
      printer_start_page($handle);
      $font = printer_create_font("draft", 10, 8, PRINTER_FW_BOLD, false, false, false, 0);
      printer_select_font($handle, $font);
      $max_row=25;
      $max_row_last=15;
      $split=ceil(sizeof($print_data['detail'])/$max_row);
      $paging=array();
      for($x=0;$x<$split;$x++)
      {
        if(isset($is_change)){
          $from=(isset($paging[$x-1]['to']) ? $paging[$x-1]['to'] : $paging[$x]['to']);
          $to=$from+$max_row_last;          
        }else{
          $from=$x*$max_row;
          $to=$from+$max_row;          
        }
        if($from<0)$from=0;
        if($to>sizeof($print_data['detail']))$to=sizeof($print_data['detail']);
        $paging[$x]=array(
          "from"=>$from,
          "to" =>$to
        );
        if(($x+1)==$split && !isset($is_change) && (sizeof($print_data['detail'])-(($split-1)*$max_row))>$max_row_last){
          $paging[$x]['to']=(($x+1)*$max_row);
          if($paging[$x]['to']>sizeof($print_data['detail']))$paging[$x]['to']=sizeof($print_data['detail']);
          $split++;
          $is_change=1;
        }        
      }
      // echo "<pre>";
      // print_r($paging);
      // exit;
      $total=0;
      for($x=0;$x<$split;$x++)
      {
        $counter=1;
        $yPos=($x*385)+20;
        printer_draw_text($handle, "FORM PENERIMAAN BARANG", 430, $yPos);
        $yPos+=15;
        if(isset($print_data['setting']['printer_logo'])){
          $path_logo=FCPATH.$print_data['setting']['printer_logo'];
          if(file_exists($path_logo)){
            printer_draw_bmp($handle, $path_logo,120,(($x==0 ? 0 : $yPos-40)+20),180,56);
          }          
        }
        $yPos = $yPos + 12;
        printer_draw_text($handle, "Purchase Ref", 350, $yPos);
        printer_draw_text($handle, "No Dokumen", 700, $yPos);
        printer_draw_text($handle, $print_data['receive']->payment_no, 800, $yPos);
        $yPos = $yPos + 12;
        printer_draw_text($handle, "Vendor", 350, $yPos);
        printer_draw_text($handle, $print_data['purchase_order']->supplier_id." - ".$print_data['purchase_order']->name, 450, $yPos);
        printer_draw_text($handle, "Rcpt Date", 700, $yPos);
        printer_draw_text($handle, date("d/m/Y",strtotime($print_data['receive']->incoming_date)), 800, $yPos);
        $yPos = $yPos + 15;

        printer_draw_line($handle, 100, $yPos, 950, $yPos);
        $yPos = $yPos + 5;
        
        printer_draw_line($handle, 100, $yPos-5, 100, $yPos+15);
        printer_draw_text($handle, "No.", 115, $yPos);
        printer_draw_line($handle, 145, $yPos-5, 145, $yPos+15);
        
        printer_draw_text($handle, "Kode Barang", 150, $yPos);
        printer_draw_line($handle, 250, $yPos-5, 250, $yPos+15);
        
        printer_draw_text($handle, "Nama Barang", 255, $yPos);
        printer_draw_line($handle, 500, $yPos-5, 500, $yPos+15);
        
        printer_draw_text($handle, "Harga", 505, $yPos);
        printer_draw_line($handle, 600, $yPos-5, 600, $yPos+15);
        
        printer_draw_text($handle, "Jumlah", 605, $yPos);
        printer_draw_line($handle, 680, $yPos-5, 680, $yPos+15);
        
        printer_draw_text($handle, "Satuan", 705, $yPos);
        printer_draw_line($handle, 780, $yPos-5, 780, $yPos+15);
        
        printer_draw_text($handle, "Sub Total", 805, $yPos);
        printer_draw_line($handle, 950, $yPos-5, 950, $yPos+15);
        $yPos+=15;
        printer_draw_line($handle, 100, $yPos, 950, $yPos);
        $yPos+=5;
        foreach($print_data['detail'] as $i){
          if($counter>$paging[$x]['from'] && $counter<=$paging[$x]['to']){
            $subtotal=$i->received_quantity*$i->price;
            $total+=$subtotal;
            printer_draw_text($handle, $counter, 115, $yPos);
            printer_draw_text($handle, $i->id, 150, $yPos);
            printer_draw_text($handle, $i->name, 255, $yPos);
            printer_draw_text($handle, number_format($i->price,2), 505, $yPos);
            printer_draw_text($handle, $i->received_quantity, 605, $yPos);
            printer_draw_text($handle, $i->code, 705, $yPos);
            printer_draw_text($handle, number_format($subtotal,2), 805, $yPos);
            $yPos+=10;            
          }
          $counter++;
        }
        printer_draw_line($handle, 100, $yPos, 950, $yPos);
        $yPos+=5;
        if($x+1==$split){
          printer_draw_text($handle, "TOTAL", 690, $yPos);
          printer_draw_text($handle, number_format($total,2), 805, $yPos);
          $yPos+=10;
          printer_draw_text($handle, "DISKON", 690, $yPos);
          printer_draw_text($handle, number_format($print_data['purchase_order']->discount,2), 805, $yPos);
          $yPos+=10;
          printer_draw_text($handle, "GRAND TOTAL", 690, $yPos);
          printer_draw_text($handle, number_format($print_data['purchase_order']->total_po,2), 805, $yPos);
          $yPos+=30;
          
          printer_draw_text($handle, "Pembuat", 170, $yPos);
          printer_draw_text($handle, "Pemeriksa", 315, $yPos);
          printer_draw_text($handle, "Disetujui", 470, $yPos);
          $yPos+=45;
          printer_draw_line($handle, 150, $yPos, 250, $yPos);
          printer_draw_line($handle, 300, $yPos, 400, $yPos);
          printer_draw_line($handle, 450, $yPos, 550, $yPos);
        }else{
          printer_draw_text($handle, "Bersambung...", 705, $yPos);
          $yPos+=80;
        }
      }
      // Header
      $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
      printer_select_pen($handle, $pen);

      printer_delete_font($font);
      printer_end_page($handle);
      printer_end_doc($handle);
      printer_close($handle);
  }
}
function print_reservation_dp($printer_name='',$print_data=array())
{
  $printer_name=convert_printer_name($printer_name);
  if (function_exists('printer_open')) {
      // set up
      $yPos                = 20;

      $handle = printer_open($printer_name);
      if($handle==false)return false;
      printer_set_option($handle, PRINTER_MODE, "RAW"); // cut mode
      printer_start_doc($handle,"Start Doc");
      printer_start_page($handle);
      $font = printer_create_font("draft", 12.5, 10, PRINTER_FW_BOLD, false, false, false, 0);
      printer_select_font($handle, $font);
      $max_row=25;
      $max_row_last=7;
      $split=ceil(sizeof($print_data['detail'])/$max_row);
      if($split==0)$split=1;
      $paging=array();
      for($x=0;$x<$split;$x++)
      {
        if(isset($is_change)){
          $from=(isset($paging[$x-1]['to']) ? $paging[$x-1]['to'] : $paging[$x]['to']);
          $to=$from+$max_row_last;          
        }else{
          $from=$x*$max_row;
          $to=$from+$max_row;
        }
        if($from<0)$from=0;
        if($to>sizeof($print_data['detail']))$to=sizeof($print_data['detail']);
        $paging[$x]=array(
          "from"=>$from,
          "to" =>$to
        );
        if(($x+1)==$split && !isset($is_change) && (sizeof($print_data['detail'])-(($split-1)*$max_row))>$max_row_last){
          $paging[$x]['to']=(($x+1)*$max_row);
          if($paging[$x]['to']>sizeof($print_data['detail']))$paging[$x]['to']=sizeof($print_data['detail']);
          $split++;
          $is_change=1;
        }        
      }
      $total=0;
      $total_non_tax=0;
      $taxes=array();
      foreach($print_data['taxes'] as $t){
        $taxes[]=array(
          "tax_name" => $t->tax_name,
          "tax_percentage" => $t->tax_percentage,
          "value" => 0,
          "service" => $t->is_service
        );
      }
      for($x=0;$x<$split;$x++)
      {
        $counter=1;
        $yPos=($x*385)+20;
        printer_draw_text($handle, "BON PESANAN", 450, $yPos);
        $yPos+=15;
        if(isset($print_data['setting']['printer_logo'])){
          $path_logo=FCPATH.$print_data['setting']['printer_logo'];
          if(file_exists($path_logo)){
            printer_draw_bmp($handle, $path_logo,120,(($x==0 ? 0 : $yPos-40)+20),180,56);
          }          
        }
        $yPos = $yPos + 12;
        printer_draw_text($handle, "Nama Pemesan", 200, $yPos);
        printer_draw_text($handle, $print_data['reservation']->customer_name, 355, $yPos);
        printer_draw_text($handle, "Waktu", 580, $yPos);
        printer_draw_text($handle, date("d/m/Y H:i:s",strtotime($print_data['reservation']->book_date))." ".($print_data['reservation']->order_type==1 ? "Ditempat" : ($print_data['reservation']->order_type==2 ? "Diambil" : "Diantar")), 680, $yPos);
        $yPos = $yPos + 12;
        printer_draw_text($handle, "Telp", 200, $yPos);
        printer_draw_text($handle, $print_data['reservation']->phone, 355, $yPos);
        printer_draw_text($handle, "Alamat", 580, $yPos);
        printer_draw_text($handle, $print_data['reservation']->customer_address, 680, $yPos);
        $yPos = $yPos + 15;

        printer_draw_line($handle, 80, $yPos, 950, $yPos);
        $yPos = $yPos + 5;
        
        printer_draw_line($handle, 80, $yPos-5, 80, $yPos+15);
        printer_draw_text($handle, "No.", 90, $yPos);
        printer_draw_line($handle, 120, $yPos-5, 120, $yPos+15);
        
        printer_draw_text($handle, "Nama Menu", 125, $yPos);
        printer_draw_line($handle, 350, $yPos-5, 350, $yPos+15);
        
        printer_draw_text($handle, "Catatan", 355, $yPos);
        printer_draw_line($handle, 600, $yPos-5, 600, $yPos+15);
        
        printer_draw_text($handle, "Harga", 605, $yPos);
        printer_draw_line($handle, 700, $yPos-5, 700, $yPos+15);
        
        printer_draw_text($handle, "Jumlah", 705, $yPos);
        printer_draw_line($handle, 780, $yPos-5, 780, $yPos+15);
        
        printer_draw_text($handle, "Sub Total", 785, $yPos);
        printer_draw_line($handle, 950, $yPos-5, 950, $yPos+15);
        $yPos+=15;
        printer_draw_line($handle, 80, $yPos, 950, $yPos);          
        $yPos+=5;
        $total_services = 0;
        $total_taxes = 0;
        $total_services2 = 0;
        $total_taxes2 = 0;
        foreach($print_data['detail'] as $i){
          if($counter>$paging[$x]['from'] && $counter<=$paging[$x]['to']){
            $total_sidedish=0;
            $notes=$i->note . " ";
            for($z=0;$z<sizeof($taxes);$z++){
              if ($print_data['tax_method'] == 1) {
                $taxes[$z]['value']+=($i->use_taxes==1 ? ($i->menu_price*$i->quantity)*$taxes[$z]['tax_percentage']/100 : 0);
              } else {
                if ($taxes[$z]['service'] == 1) {
                  $total_services += ($i->use_taxes==1 ? ($i->menu_price*$i->quantity)*$taxes[$z]['tax_percentage']/100 : 0);
                  $taxes[$z]['value'] = $total_services;
                } else {
                  $total_taxes += ($i->use_taxes==1 ? ($i->menu_price*$i->quantity)*$taxes[$z]['tax_percentage']/100 : 0);
                  $taxes[$z]['value'] = ($total_services * $taxes[$z]['tax_percentage'] / 100) + $total_taxes;
                }
              }
            }
            foreach($i->menu_options as $oo){              
              $notes .= $oo->option_name.':'. $oo->option_value_name;
            }
            foreach($i->menu_side_dish as $oss){
              $total_sidedish+=$oss->price*$i->quantity;
              $notes .= ', '.$oss->name . '('. number_format($oss->price,0).')';
              for($z=0;$z<sizeof($taxes);$z++){
                if ($print_data['tax_method'] == 1) {
                  $taxes['value']+=($i->use_taxes==1 ? ($oss->price*$i->quantity)*$taxes['tax_percentage']/100 : 0);
                } else {
                  if ($taxes['service'] == 1) {
                    $total_services2 += ($i->use_taxes==1 ? ($oss->price*$i->quantity)*$taxes['tax_percentage']/100 : 0);
                    $taxes['value'] = $total_services2;
                  } else {
                    $total_taxes2 += ($i->use_taxes==1 ? ($oss->price*$i->quantity)*$taxes['tax_percentage']/100 : 0);
                    $taxes['value'] = ($total_services2 * $taxes['tax_percentage'] / 100) + $total_taxes2;
                  }
                } 
              }
            }
            $subtotal=($i->menu_price*$i->quantity)+$total_sidedish;
            $total+=$subtotal;
            printer_draw_text($handle, $counter, 90, $yPos);
            printer_draw_text($handle, $i->menu_name, 125, $yPos);
            printer_draw_text($handle, $notes, 355, $yPos);
            printer_draw_text($handle, "Rp. ".number_format($i->menu_price), 605, $yPos);
            printer_draw_text($handle, $i->quantity, 705, $yPos);
            printer_draw_text($handle, "Rp. ".number_format($subtotal), 785, $yPos);
            $yPos+=15;
          }
          $counter++;
        }
        printer_draw_line($handle, 80, $yPos, 950, $yPos);
        $yPos+=5;
        if($x+1==$split){
          printer_draw_text($handle, "Total", 655, $yPos);
          printer_draw_text($handle, "Rp. ".number_format($total,2), 785, $yPos);
          $yPos+=15;
          foreach($taxes as $t){
            printer_draw_text($handle, $t['tax_name'], 655, $yPos);
            printer_draw_text($handle, "Rp. ".number_format($t['value'],2), 785, $yPos);
            $total+=$t['value'];
            $yPos+=15;
          }
          if(isset($print_data['order_reservation']->delivery_cost) && $print_data['order_reservation']->delivery_cost>0){
            printer_draw_text($handle, "Ongkir", 655, $yPos);
            printer_draw_text($handle, "Rp. ".number_format($print_data['order_reservation']->delivery_cost,2), 785, $yPos);
            $total+=$print_data['order_reservation']->delivery_cost;
            $yPos+=15;
          }
          $temp_grandtotal = $total;
          if ($print_data['round'] > 0) {
            $total += $print_data['round'] - ($total % $print_data['round']);
            $total -= ($total % $print_data['round']);
            $round = $total - $temp_grandtotal;
            if ($print_data['round'] == 1000) {
              if ($round > 500) {
                $round = $round - $print_data['round'];
                $total = $temp_grandtotal + $round;
              }
            }
          }
          $grandtotal=$total;
          printer_draw_text($handle, "Pembulatan", 655, $yPos);
          printer_draw_text($handle, "Rp. ".number_format($round,2), 785, $yPos);
          $yPos+=15;
          printer_draw_text($handle, "Grand Total", 655, $yPos);
          printer_draw_text($handle, "Rp. ".number_format($grandtotal,2), 785, $yPos);
          $yPos+=15;
          printer_draw_text($handle, "Uang Muka", 655, $yPos);
          printer_draw_text($handle, "Rp. ".number_format($print_data['reservation']->down_payment,2), 785, $yPos);
          $yPos+=15;
          $remain=$grandtotal-$print_data['reservation']->down_payment;
          if($remain<0)$remain=0;
          printer_draw_text($handle, "Sisa", 655, $yPos);
          printer_draw_text($handle, "Rp. ".number_format($remain,2), 785, $yPos);
          $yPos+=20;
          printer_draw_text($handle, "Bandung,".date("d/m/Y",strtotime($print_data['reservation']->created_at)), 355, $yPos);
          $yPos+=15;
          printer_draw_text($handle, "Kasir,", 175, $yPos);
          printer_draw_text($handle, "Yang menerima pesanan,", 355, $yPos);
          $yPos+=65;
          printer_draw_line($handle, 150, $yPos, 250, $yPos);
          printer_draw_line($handle, 330, $yPos, 630, $yPos);
          $yPos+=15;
          
          printer_draw_text($handle, "Catatan Pesanan :", 100, $yPos);
          $yPos+=10;
          printer_draw_text($handle, $print_data['reservation']->book_note, 100, $yPos);
          $yPos+=15;
          printer_draw_text($handle, "Aturan - Aturan Reservasi :", 100, $yPos);
          $yPos+=10;
          foreach($print_data['template'] as $p){
            printer_draw_text($handle, $p, 100, $yPos);
            $yPos+=10;            
          }
        }else{
          $yPos+=125;
        }
      }
      // Header
      $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
      printer_select_pen($handle, $pen);

      printer_delete_font($font);
      printer_end_page($handle);
      printer_end_doc($handle);
      printer_close($handle);
  }
}
function print_payroll($printer_name='',$print_data=array())
{
  $printer_name=convert_printer_name($printer_name);
  if (function_exists('printer_open')) {
      // set up
      $yPos                = 20;

      $handle = printer_open($printer_name);
      if($handle==false)return false;
      printer_set_option($handle, PRINTER_MODE, "RAW"); // cut mode
      printer_start_doc($handle,"Start Doc");
      printer_start_page($handle);
      $font = printer_create_font("draft", 11, 8, PRINTER_FW_BOLD, false, false, false, 0);
      printer_select_font($handle, $font);
      $max_row=25;
      $max_row_last=7;
      foreach($print_data['detail'] as $d){
        $yPos=20;
        printer_draw_text($handle, "SLIP GAJI KARYAWAN", 460, $yPos);
        $yPos+=15;
        // if(isset($print_data['data_store']->store_logo)){
          // $path_logo=FCPATH.$print_data['data_store']->store_logo;
          // if(file_exists($path_logo)){
            // printer_draw_bmp($handle, $path_logo,120,(($x==0 ? 0 : $yPos-40)+20),180,56);
          // }          
        // }
        $yPos = $yPos + 12;
        printer_draw_text($handle, "Nama Pegawai", 150, $yPos);
        printer_draw_text($handle, $d['data']->name, 450, $yPos);
        printer_draw_text($handle,date("F Y",strtotime($print_data['periode'])), 750, $yPos);
        $yPos = $yPos + 12;
        printer_draw_text($handle, "Jabatan", 150, $yPos);
        printer_draw_text($handle, $d['data']->jobs_name, 450, $yPos);
        $yPos = $yPos + 15;
        printer_draw_line($handle, 100, $yPos, 950, $yPos);
        $yPos = $yPos + 5;
        
        printer_draw_text($handle, "Penambahan", 115, $yPos);
        $yPos+=11;
        $total_plus=0;
        foreach($d['detail'] as $l){
          if($l->is_enhancer==1){
            $total_plus+=$l->value;
            printer_draw_text($handle, $l->component_name, 150, $yPos);
            printer_draw_text($handle, number_format($l->value,0), 700, $yPos);
            $yPos+=11;
          }
        } 
        printer_draw_text($handle, "Total Penambah", 150, $yPos);
        printer_draw_text($handle, number_format($total_plus,0), 700, $yPos);
        $yPos+=11;
        printer_draw_text($handle, "Pengurangan", 115, $yPos);
        $yPos+=11;
        $total_minus=0;
        $total_attendances=0;
        foreach($d['detail'] as $l){
          if($l->is_enhancer==-1 && !in_array($l->key,array("late_1","late_2","permission_go_home","permission_alpha","alpha"))){
            $total_minus+=$l->value;
            printer_draw_text($handle, $l->component_name, 150, $yPos);
            printer_draw_text($handle, number_format($l->value,0), 700, $yPos);
            $yPos+=11;
          }else{
            if(in_array($l->key,array("late_1","late_2","permission_go_home","permission_alpha","alpha")))$total_attendances+=$l->value;
          }
        }
        if($total_attendances>$d['payroll_static_data']['insentive']){
          $total_attendances=$d['payroll_static_data']['insentive'];
        }
        $total_minus+=$total_attendances;
        printer_draw_text($handle, "Pengurang Insentif", 150, $yPos);
        printer_draw_text($handle, number_format($total_attendances,0), 700, $yPos);
        $yPos+=11;
        foreach($d['detail'] as $l){
          if($l->is_enhancer==-1 && in_array($l->key,array("late_1","late_2","permission_go_home","permission_alpha","alpha"))){
            printer_draw_text($handle, $l->component_name, 200, $yPos);
            printer_draw_text($handle, $l->formula_default." % : ".$d['payroll_static_data'][$l->key], 450, $yPos);
            printer_draw_text($handle, number_format($l->value,0), 700, $yPos);
            $yPos+=11;
          }
        }
        printer_draw_text($handle, "Total Pengurang", 150, $yPos);
        printer_draw_text($handle, number_format($total_minus,0), 700, $yPos);
        $yPos+=11;
        printer_draw_text($handle, "Take Home Pay", 115, $yPos);
        printer_draw_text($handle, number_format($total_plus-$total_minus,0), 700, $yPos);
        $yPos+=11;
      }
      $yPos+=10;
      printer_draw_text($handle, "KEUANGAN", 300, $yPos);
      printer_draw_text($handle, "PENERIMA", 650, $yPos);
      $yPos+=45;
      printer_draw_line($handle, 250, $yPos, 450, $yPos);
      printer_draw_line($handle, 600, $yPos, 800, $yPos);
      // Header
      $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
      printer_select_pen($handle, $pen);

      printer_delete_font($font);
      printer_end_page($handle);
      printer_end_doc($handle);
      printer_close($handle);
  }
}
function print_open_close_bill($printer_name = '', $print_data = '', $user_data){
  $printer_name=convert_printer_name($printer_name); 
    if (function_exists('printer_open')) {
            // set up
            $var_max_char_length = 25;
            $yPos                = 0;

            $handle = printer_open($printer_name);
            if($handle==false)return false;
            printer_set_option($handle, PRINTER_MODE, "RAW"); // cut mode
            printer_start_doc($handle,"Start Doc");
            printer_start_page($handle);

            $font = printer_create_font("Courier New", 25, 15, PRINTER_FW_BOLD, false, false, false, 0);
            printer_select_font($handle, $font);
            if(isset($print_data['setting']['printer_logo'])){
              $path_logo=FCPATH.$print_data['setting']['printer_logo'];
              if(file_exists($path_logo)){
                printer_draw_bmp($handle, $path_logo, 35, 50,300,150);
                $yPos+=200;
              }          
            }
            $yPos = $yPos + 25;
            printer_draw_text($handle, "Waktu open", 0, $yPos);
            printer_draw_text($handle, ":", 170, $yPos);
            $yPos = $yPos + 25;
            printer_draw_text($handle, $print_data['open_at'], 50, $yPos);
            $yPos = $yPos + 40;

            printer_draw_text($handle, "Waktu close", 0, $yPos);
            printer_draw_text($handle, ":", 170, $yPos);
            $yPos = $yPos + 25;
            printer_draw_text($handle, $print_data['close_at'], 50, $yPos);
            $yPos = $yPos + 40;

            printer_draw_text($handle, "Open by", 0, $yPos);
            printer_draw_text($handle, ":", 170, $yPos);
            $yPos = $yPos + 25;
            printer_draw_text($handle, $print_data['open_by'], 50, $yPos);
            $yPos = $yPos + 40;

            printer_draw_text($handle, "Close by", 0, $yPos);
            printer_draw_text($handle, ":", 170, $yPos);
            $yPos = $yPos + 25;
            printer_draw_text($handle, $print_data['close_by'], 50, $yPos);
            $yPos = $yPos + 40;

            printer_draw_text($handle, "Total pendapatan", 0, $yPos);
            printer_draw_text($handle, ":", 250, $yPos);
            $yPos = $yPos + 25;
            printer_draw_text($handle, $print_data['total_cash'], 50, $yPos);
            $yPos = $yPos + 40;

            printer_draw_text($handle, "Total transaksi", 0, $yPos);
            printer_draw_text($handle, ":", 250, $yPos);
            $yPos = $yPos + 25;
            printer_draw_text($handle, $print_data['total_transaction'], 50, $yPos);
            $yPos = $yPos + 40;
           
            foreach ($print_data['bill_payment'] as $key => $row) {

                //tidak menampilkan payment tipe compliment, karna sudah ditampilkan di bill_information
                if($row->id != 5){
                    printer_draw_text($handle, $row->value, 0, $yPos);
                    printer_draw_text($handle, ":", 170, $yPos);
                    $yPos = $yPos + 25;
                    printer_draw_text($handle, convert_rupiah($row->total), 50, $yPos);
                    $yPos = $yPos + 40;
                }

            }

                
            foreach ($print_data['bill_information'] as $key => $row) {
                printer_draw_text($handle, $row->info, 0, $yPos);
                printer_draw_text($handle, ":", 170, $yPos);
                $yPos = $yPos + 25;
                printer_draw_text($handle, convert_rupiah($row->total), 50, $yPos);
                $yPos = $yPos + 40;
            }

            $yPos = $yPos + 5;
            printer_draw_text($handle, "OUTLET", 0, $yPos);
            $yPos = $yPos + 40;
            foreach ($print_data['outlet'] as $key => $row) {
                printer_draw_text($handle, "OUTLET - ".$row->outlet_name, 0, $yPos);
                printer_draw_text($handle, ":", 250, $yPos);
                $yPos = $yPos + 25;
                $temp = $yPos;
                $total = 0;
                 $yPos = $yPos + 40;
                if(!empty($row->data )){
                    foreach ($row->data  as $key => $row2) {

                        $yPos = $yPos + 10;
                        $yPos = _write_open_close_bill($handle, $row2, $yPos, $var_max_char_length);

                        $total += $row2->total_quantity;
                    }
                }
                printer_draw_text($handle, $total, 50, $temp);
                $yPos = $yPos + 40;
                
            }

            
            // Header
            $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
            printer_select_pen($handle, $pen);

            printer_delete_font($font);
            printer_end_page($handle);
            printer_end_doc($handle);
            printer_close($handle);
        }
}
function print_open_close_bill_mode($printer_name = '', $print_data = '', $user_data,$printer_setting=array()){
  $GLOBALS['printer_setting']=$printer_setting;
  $printer_name=convert_printer_name($printer_name);
  if (function_exists('printer_open')) {
      $ci=&get_instance(); 
        // $min_80=$ci->config->item("72_80");
        // set up
        $var_max_char_length = 25;
        $yPos                = 0;

        $handle = printer_open($printer_name);
        if($handle==false)return false;
        printer_set_option($handle, PRINTER_MODE, "RAW"); // cut mode
        printer_start_doc($handle,"Start Doc");
        printer_start_page($handle);

        $font = printer_create_font("Courier New", 25, 10, PRINTER_FW_BOLD, false, false, false, 0);        
        printer_select_font($handle, $font);

        //////// Header          
        printer_draw_text($handle, "TANGGAL OPEN", 0, $yPos);
        printer_draw_text($handle, ": ".date("d/m/Y",strtotime($print_data['oc_cashier']->open_at)), ($printer_setting->description == 48 ? 190 : 420), $yPos);

        $yPos = $yPos + 25;        
        printer_draw_text($handle, "JAM OPEN", 0, $yPos);
        printer_draw_text($handle, ": ".date("H:i:s",strtotime($print_data['oc_cashier']->open_at)), ($printer_setting->description == 48 ? 190 : 420), $yPos);
        
        $yPos = $yPos + 25;
        printer_draw_text($handle, "WAKTU CLOSE", 0, $yPos);
        printer_draw_text($handle, ": ".date("d/m/Y",strtotime($print_data['oc_cashier']->close_at)), ($printer_setting->description == 48 ? 190 : 420), $yPos);
        
        $yPos = $yPos + 25;
        printer_draw_text($handle, "JAM CLOSE", 0, $yPos);
        printer_draw_text($handle, ": ".date("H:i:s",strtotime($print_data['oc_cashier']->close_at)), ($printer_setting->description == 48 ? 190 : 420), $yPos);
        
        $yPos = $yPos + 25;
        printer_draw_text($handle, "OPEN BY", 0, $yPos);
        printer_draw_text($handle, ": ".$print_data['oc_cashier']->open_by_name, ($printer_setting->description == 48 ? 190 : 420), $yPos);
        
        $yPos = $yPos + 25;
        printer_draw_text($handle, "CLOSE BY", 0, $yPos);
        printer_draw_text($handle, ": ".$print_data['oc_cashier']->close_by_name, ($printer_setting->description == 48 ? 190 : 420), $yPos);
        
        $yPos = $yPos + 25;
        printer_draw_text($handle, "JML TRANSAKSI", 0, $yPos);
        printer_draw_text($handle, ": ".$print_data['transaction']->transaction_count, ($printer_setting->description == 48 ? 190 : 420), $yPos);       
        /////////// Item Sales
        $yPos = $yPos + 35;
        printer_draw_line($handle, 0, $yPos, ($printer_setting->description == 48 ? 370 : 600), $yPos);

        $yPos = $yPos + 15;
        printer_draw_text($handle, "Type", 0, $yPos);
        printer_draw_text($handle, "Qty", ($printer_setting->description == 48 ? 190 : 350), $yPos);
        printer_draw_text($handle, "Amount", ($printer_setting->description == 48 ? 250 : 420), $yPos);

        $yPos = $yPos + 25;
        printer_draw_line($handle, 0, $yPos, ($printer_setting->description == 48 ? 370 : 600), $yPos);

        $yPos = $yPos + 25;
        printer_draw_text($handle, "Item Sales", 0, $yPos);
        printer_draw_text($handle, "(+)", ($printer_setting->description == 48 ? 160 : 300), $yPos);
        printer_draw_text($handle, $print_data['oc_menu']->amount, ($printer_setting->description == 48 ? 190 : 220), $yPos);
        printer_draw_text($handle, number_format($print_data['net_sales']->amount), ($printer_setting->description == 48 ? 250 : 420), $yPos);
        $yPos = $yPos + 25;

        foreach($print_data['taxes_foreach'] as $d){          
          printer_draw_text($handle, $d->info, 0, $yPos);
          printer_draw_text($handle, number_format($d->amount), ($printer_setting->description == 48 ? 250 : 420), $yPos);
          $yPos = $yPos + 25;
        }

        if($print_data['round_up']->amount>0){
          printer_draw_text($handle, "Pembulatan", 0, $yPos);
          printer_draw_text($handle, "(+)", ($printer_setting->description == 48 ? 160 : 300), $yPos);
          printer_draw_text($handle, number_format($print_data['round_up']->amount), ($printer_setting->description == 48 ? 250 : 420), $yPos);
          $yPos = $yPos + 25;
        }

        if($print_data['delivery_charge']->amount>0){
          printer_draw_text($handle, "Ongkos Kirim", 0, $yPos);
          printer_draw_text($handle, "(+)", ($printer_setting->description == 48 ? 160 : 300), $yPos);
          printer_draw_text($handle, number_format($print_data['delivery_charge']->amount), ($printer_setting->description == 48 ? 250 : 420), $yPos);
          $yPos = $yPos + 25;
        }

        if($print_data['petty_cash']->amount>0){
          printer_draw_text($handle, "Kas Kecil", 0, $yPos);
          printer_draw_text($handle, "(-)", ($printer_setting->description == 48 ? 160 : 300), $yPos);
          printer_draw_text($handle, number_format($print_data['petty_cash']->amount), ($printer_setting->description == 48 ? 250 : 420), $yPos);
          $yPos = $yPos + 25;
        }

        if($print_data['dp_out']->amount>0){
          printer_draw_text($handle, "DP OUT", 0, $yPos);
          printer_draw_text($handle, "(-)", ($printer_setting->description == 48 ? 160 : 300), $yPos);
          printer_draw_text($handle, number_format($print_data['dp_out']->amount), ($printer_setting->description == 48 ? 250 : 420), $yPos);
          $yPos = $yPos + 25;
        }

        if($print_data['delivery']->amount>0){
          printer_draw_text($handle, "Komisi Delivery", 0, $yPos);
          printer_draw_text($handle, "(-)", ($printer_setting->description == 48 ? 160 : 300), $yPos);
          printer_draw_text($handle, number_format($print_data['delivery']->amount), ($printer_setting->description == 48 ? 250 : 420), $yPos);
          $yPos = $yPos + 25;
        }
        
        if($print_data['discount']->amount>0){
          printer_draw_text($handle, "Discount", 0, $yPos);
          printer_draw_text($handle, "(-)", ($printer_setting->description == 48 ? 160 : 300), $yPos);
          printer_draw_text($handle, number_format($print_data['discount']->amount), ($printer_setting->description == 48 ? 250 : 420), $yPos);
          $yPos = $yPos + 25;
        }
        
        if($print_data['bon']->amount>0){
          printer_draw_text($handle, "BON", 0, $yPos);
          printer_draw_text($handle, "(-)", ($printer_setting->description == 48 ? 160 : 300), $yPos);
          printer_draw_text($handle, number_format($print_data['bon']->amount), ($printer_setting->description == 48 ? 250 : 420), $yPos);
          $yPos = $yPos + 25;
        }

        if($print_data['voucher']->amount>0){
          printer_draw_text($handle, "Voucher", 0, $yPos);
          printer_draw_text($handle, "(-)", ($printer_setting->description == 48 ? 160 : 300), $yPos);
          printer_draw_text($handle, number_format($print_data['voucher']->amount), ($printer_setting->description == 48 ? 250 : 420), $yPos);
          $yPos = $yPos + 25;
        }
        
        if($print_data['compliment']->amount>0){
          printer_draw_text($handle, "Compliment", 0, $yPos);
          printer_draw_text($handle, "(-)", ($printer_setting->description == 48 ? 160 : 300), $yPos);
          printer_draw_text($handle, number_format($print_data['compliment']->amount), ($printer_setting->description == 48 ? 250 : 420), $yPos);
          $yPos = $yPos + 25;
        }

        if($print_data['cash_company']->amount>0){
          printer_draw_text($handle, "Cash Company", 0, $yPos);
          printer_draw_text($handle, "(-)", ($printer_setting->description == 48 ? 160 : 300), $yPos);
          printer_draw_text($handle, number_format($print_data['cash_company']->amount), ($printer_setting->description == 48 ? 250 : 420), $yPos);
          $yPos = $yPos + 25;
        }
        
        if($print_data['pending_bill_company']->amount>0){
          printer_draw_text($handle, "PB Company", 0, $yPos);
          printer_draw_text($handle, "(-)", ($printer_setting->description == 48 ? 160 : 300), $yPos);
          printer_draw_text($handle, $print_data['pending_bill_company']->countd, ($printer_setting->description == 48 ? 190 : 350), $yPos);
          printer_draw_text($handle, number_format($print_data['pending_bill_company']->amount), ($printer_setting->description == 48 ? 250 : 420), $yPos);
          $yPos = $yPos + 25;
        }
        
        if($print_data['pending_bill_employee']->amount>0){
          printer_draw_text($handle, "PB Employee ", 0, $yPos);
          printer_draw_text($handle, "(-)", ($printer_setting->description == 48 ? 160 : 300), $yPos);
          printer_draw_text($handle, $print_data['pending_bill_employee']->countd, ($printer_setting->description == 48 ? 190 : 350), $yPos);
          printer_draw_text($handle, number_format($print_data['pending_bill_employee']->amount), ($printer_setting->description == 48 ? 250 : 420), $yPos);
          $yPos = $yPos + 25;
        }

        $omzet=$print_data['net_sales']->amount+$print_data['taxes']->amount+$print_data['round_up']->amount+$print_data['delivery_charge']->amount;
        $total=$omzet-$print_data['voucher']->amount-$print_data['compliment']->amount-$print_data['cash_company']->amount-$print_data['pending_bill_company']->amount-$print_data['pending_bill_employee']->amount;
        $total-=($print_data['petty_cash']->amount+$print_data['dp_out']->amount+$print_data['delivery']->amount+$print_data['discount']->amount+$print_data['bon']->amount);

        $yPos = $yPos + 55;
        printer_draw_text($handle, "Total Sales", 0, $yPos);
        printer_draw_text($handle, "(=)", ($printer_setting->description == 48 ? 160 : 300), $yPos);
        printer_draw_text($handle, number_format($total), ($printer_setting->description == 48 ? 250 : 420), $yPos);
        $yPos = $yPos + 25;
        printer_draw_text($handle, "Estimated Sales", 0, $yPos);
        printer_draw_text($handle, number_format($total), ($printer_setting->description == 48 ? 250 : 420), $yPos);

        ///////////  Media
        $yPos = $yPos + 35;
        printer_draw_line($handle, 0, $yPos, ($printer_setting->description == 48 ? 370 : 600), $yPos);

        $yPos = $yPos + 15;
        _write_text_center_align($handle, "MEDIA", $yPos, ($printer_setting->description == 48 ? 150 : 240));

        $yPos = $yPos + 30;
        printer_draw_line($handle, 0, $yPos, ($printer_setting->description == 48 ? 370 : 600), $yPos);

        $yPos = $yPos + 25;
        printer_draw_text($handle, "Cash", 0, $yPos);
        printer_draw_text($handle, $print_data['cash']->countd, ($printer_setting->description == 48 ? 190 : 350), $yPos);
        printer_draw_text($handle, number_format($print_data['cash']->amount), ($printer_setting->description == 48 ? 250 : 420), $yPos);

        $yPos = $yPos + 25;
        $total_plus=$print_data['cash']->amount;
        $total_card=0;
        $total_card2=0;
        
        $jml=$print_data['cash']->countd;

        foreach($print_data['debit'] as $d){
          $total_plus+=$d->amount;
          $total_card+=$d->amount;
          $total_card2+=$d->countd;
          $jml+=$d->countd;
          printer_draw_text($handle, strtoupper($d->bank_name)."-DB", 0, $yPos);
          printer_draw_text($handle, $d->countd, ($printer_setting->description == 48 ? 190 : 350), $yPos);
          printer_draw_text($handle, number_format($d->amount), ($printer_setting->description == 48 ? 250 : 420), $yPos);
          $yPos = $yPos + 20;
        }

        foreach($print_data['credit'] as $d){              
          $total_plus+=$d->amount;
          $total_card+=$d->amount;
          $total_card2+=$d->countd;
          $jml+=$d->countd;
          printer_draw_text($handle, strtoupper($d->bank_name)."-CR", 0, $yPos);
          printer_draw_text($handle, $d->countd, ($printer_setting->description == 48 ? 190 : 350), $yPos);
          printer_draw_text($handle, number_format($d->amount), ($printer_setting->description == 48 ? 250 : 420), $yPos);
          $yPos = $yPos + 25;
        }

        foreach($print_data['flazz'] as $d){              
          $total_plus+=$d->amount;
          $total_card+=$d->amount;
          $total_card2+=$d->countd;
          $jml+=$d->countd;
          printer_draw_text($handle, strtoupper($d->bank_name)."-Flazz", 0, $yPos);
          printer_draw_text($handle, $d->countd, ($printer_setting->description == 48 ? 190 : 350), $yPos);
          printer_draw_text($handle, number_format($d->amount), ($printer_setting->description == 48 ? 250 : 420), $yPos);
          $yPos = $yPos + 25;
        }
        $total_cash = $print_data['cash']->amount;

        $yPos = $yPos + 35;
        printer_draw_text($handle, "TOTAL CARD", 0, $yPos);
        printer_draw_text($handle, $total_card2, ($printer_setting->description == 48 ? 190 : 350), $yPos);
        printer_draw_text($handle, number_format($total_card), ($printer_setting->description == 48 ? 250 : 420), $yPos);
        $yPos = $yPos + 25;
        printer_draw_text($handle, "TOTAL CASH", 0, $yPos);
        printer_draw_text($handle, $print_data['cash']->countd, ($printer_setting->description == 48 ? 190 : 350), $yPos);
        printer_draw_text($handle, number_format($total_cash), ($printer_setting->description == 48 ? 250 : 420), $yPos);

        ///////////// Total collection        
        $yPos = $yPos + 35;
        printer_draw_line($handle, 0, $yPos, ($printer_setting->description==48 ? 370 : 600), $yPos);

        $yPos = $yPos + 15;
        printer_draw_text($handle, "Total collection", 0, $yPos);
        printer_draw_text($handle, $jml, ($printer_setting->description == 48 ? 190 : 350), $yPos);
        printer_draw_text($handle, number_format($total_card+$total_cash), ($printer_setting->description == 48 ? 250 : 420), $yPos);

        $yPos = $yPos + 25;
        printer_draw_line($handle, 0, $yPos, ($printer_setting->description == 48 ? 370 : 600), $yPos);

        $font = printer_create_font("Courier New", 30, 10, PRINTER_FW_ULTRABOLD, false, false, false, 0);
        printer_select_font($handle, $font);

        $yPos = $yPos + 15;
        printer_draw_text($handle, "NET SALES", 0, $yPos);
        printer_draw_text($handle, number_format($print_data['net_sales']->amount), ($printer_setting->description == 48 ? 250 : 420), $yPos);

        $font = printer_create_font("Courier New", 25, 10, PRINTER_FW_BOLD, false, false, false, 0);
        printer_select_font($handle, $font);

        $yPos = $yPos + 30;
        printer_draw_line($handle, 0, $yPos, ($printer_setting->description == 48 ? 370 : 600), $yPos);

        //////// Bill Pending        
        $yPos = $yPos + 25;
        printer_draw_text($handle, "PB Company", 0, $yPos);
        printer_draw_text($handle, $print_data['pending_bill_company']->countd, ($printer_setting->description == 48 ? 190 : 350), $yPos);
        printer_draw_text($handle, number_format($print_data['pending_bill_company']->amount), ($printer_setting->description == 48 ? 250 : 420), $yPos);         

        $yPos = $yPos + 25;
        printer_draw_text($handle, "PB Employee ", 0, $yPos);
        printer_draw_text($handle, $print_data['pending_bill_employee']->countd, ($printer_setting->description == 48 ? 190 : 350), $yPos);
        printer_draw_text($handle, number_format($print_data['pending_bill_employee']->amount), ($printer_setting->description == 48 ? 250 : 420), $yPos);
                 
        $bill = $print_data['transaction']->transaction_count;         
        $yPos = $yPos + 55;
        printer_draw_text($handle, "Total # of Bills", 0, $yPos);
        printer_draw_text($handle, $bill, ($printer_setting->description == 48 ? 250 : 420), $yPos);
        $avg_bill = ($total_card+$total_cash) / $bill;
        $yPos = $yPos + 25;
        printer_draw_text($handle, "Avg Bills", 0, $yPos);
        printer_draw_text($handle, number_format(round($avg_bill)), ($printer_setting->description == 48 ? 250 : 420), $yPos);

        $menu = $print_data['oc_menu']->amount;
        $yPos = $yPos + 25;
        printer_draw_text($handle, "Total # of Menus", 0, $yPos);
        printer_draw_text($handle, number_format($menu), ($printer_setting->description == 48 ? 250 : 420), $yPos);
        $avg_menu = ($total_card+$total_cash) / $menu;
        $yPos = $yPos + 25;
        printer_draw_text($handle, "Avg Menus", 0, $yPos);
        printer_draw_text($handle, number_format(round($avg_menu)), ($printer_setting->description == 48 ? 250 : 420), $yPos);

        $covers = $print_data['transaction']->transaction_count;
        $yPos = $yPos + 25;
        printer_draw_text($handle, "Total # of Covers", 0, $yPos);
        printer_draw_text($handle, number_format($covers), ($printer_setting->description == 48 ? 250 : 420), $yPos);
        $avg_covers = ($total_card+$total_cash) / $covers;
        $yPos = $yPos + 25;
        printer_draw_text($handle, "Avg Covers", 0, $yPos);
        printer_draw_text($handle, number_format(round($avg_covers)), ($printer_setting->description == 48 ? 250 : 420), $yPos);

        $yPos = $yPos + 25;
        printer_draw_line($handle, 0, $yPos, ($printer_setting->description == 48 ? 370 : 600), $yPos);

        $yPos = $yPos + 25;
        printer_draw_text($handle, "Begin Receipt #", 0, $yPos);
        printer_draw_text($handle, $print_data['begin_end_receipt']->begin, ($printer_setting->description == 48 ? 250 : 420), $yPos);

        $yPos = $yPos + 25;
        printer_draw_text($handle, "End Receipt #", 0, $yPos);
        printer_draw_text($handle, $print_data['begin_end_receipt']->end, ($printer_setting->description == 48 ? 250 : 420), $yPos);


        $yPos = $yPos + 25;
        printer_draw_line($handle, 0, $yPos, ($printer_setting->description == 48 ? 370 : 600), $yPos);


        $yPos = $yPos + 15;
        _write_text_center_align($handle, "GROUP SALES", $yPos, ($printer_setting->description == 48 ? 150 : 200));

        $yPos = $yPos + 30;
        printer_draw_line($handle, 0, $yPos, ($printer_setting->description == 48 ? 370 : 600), $yPos);


        $yPos = $yPos + 25;
        $ttl_gr = 0;
        foreach($print_data['oc_category'] as $d){
          $ttl_gr += $d->mnpric;
          printer_draw_text($handle, strtoupper($d->ctgname), 0, $yPos);
          printer_draw_text($handle, $d->quantt, ($printer_setting->description == 48 ? 190 : 350), $yPos);
          printer_draw_text($handle, number_format($d->mnpric), ($printer_setting->description == 48 ? 250 : 420), $yPos);
          $yPos = $yPos + 25;
        }


        $yPos = $yPos + 55;
        printer_draw_text($handle, "Total Group", 0, $yPos);
        printer_draw_text($handle, $print_data['oc_menu']->amount, ($printer_setting->description == 48 ? 190 : 350), $yPos);
        printer_draw_text($handle, number_format($ttl_gr), ($printer_setting->description == 48 ? 250 : 420), $yPos);


        ////// sales category        
        $yPos = $yPos + 35;
        printer_draw_line($handle, 0, $yPos, ($printer_setting->description == 48 ? 370 : 600), $yPos);

        $yPos = $yPos + 15;
        _write_text_center_align($handle, "SALES CATEGORY", $yPos, ($printer_setting->description == 48 ? 150 : 200));

        $yPos = $yPos + 30;
        printer_draw_line($handle, 0, $yPos, ($printer_setting->description == 48 ? 370 : 600), $yPos);

        $yPos = $yPos + 25;
        printer_draw_text($handle, "Dine In", 0, $yPos);
        printer_draw_text($handle, number_format($print_data['oc_dinein']->dinein), ($printer_setting->description == 48 ? 190 : 350), $yPos);
        printer_draw_text($handle, number_format($print_data['oc_dinein']->ttldn), ($printer_setting->description == 48 ? 250 : 420), $yPos);
        
        $yPos = $yPos + 25;
        printer_draw_text($handle, "Take Away", 0, $yPos);
        printer_draw_text($handle, number_format($print_data['oc_takeaway']->takeaway), ($printer_setting->description == 48 ? 190 : 350), $yPos);
        printer_draw_text($handle, number_format($print_data['oc_takeaway']->ttltkw), ($printer_setting->description == 48 ? 250 : 420), $yPos);

        $yPos = $yPos + 25;
        printer_draw_text($handle, "Delivery", 0, $yPos);
        printer_draw_text($handle, number_format($print_data['oc_delivery']->delivery), ($printer_setting->description == 48 ? 190 : 350), $yPos);
        printer_draw_text($handle, number_format($print_data['oc_delivery']->ttldlv), ($printer_setting->description == 48 ? 250 : 420), $yPos);

        /////  diskon/promotion        
        $yPos = $yPos + 35;
        printer_draw_line($handle, 0, $yPos, ($printer_setting->description == 48 ? 370 : 600), $yPos);

        $yPos = $yPos + 15;
        _write_text_center_align($handle, "DISCOUNT / PROMOTION", $yPos, ($printer_setting->description == 48 ? 150 : 200));

        $yPos = $yPos + 30;
        printer_draw_line($handle, 0, $yPos, ($printer_setting->description == 48 ? 370 : 600), $yPos);

        $yPos = $yPos + 25;
        printer_draw_text($handle, "Discount", 0, $yPos);
        printer_draw_text($handle, number_format($print_data['discount']->amount), ($printer_setting->description == 48 ? 250 : 420), $yPos);
        

        $yPos = $yPos + 25;
        printer_draw_text($handle, "Voucher", 0, $yPos);
        printer_draw_text($handle, number_format($print_data['voucher']->amount), ($printer_setting->description == 48 ? 250 : 420), $yPos);
        
        /////// FIPO
        $yPos = $yPos + 35;
        printer_draw_line($handle, 0, $yPos, ($printer_setting->description==48 ? 370 : 600), $yPos);

        $yPos = $yPos + 25;
        printer_draw_line($handle, 0, $yPos, ($printer_setting->description==48 ? 370 : 600), $yPos);

        //// end 2
        $yPos = $yPos + 35;
        printer_draw_text($handle, "KASIR", ($printer_setting->description == 48 ? 20 : 130), $yPos);
        printer_draw_text($handle, "SUPERVISOR", ($printer_setting->description == 48 ? 175 : 300), $yPos);
        $yPos = $yPos + 145;
        printer_draw_text($handle, "(.......)", ($printer_setting->description == 48 ? 15 : 110), $yPos);
        printer_draw_text($handle, "(..........)", ($printer_setting->description == 48 ? 170 : 290), $yPos);
        $yPos = $yPos + 55;
        
        // Header
        $pen = printer_create_pen(PRINTER_PEN_SOLID, 1, "000000");
        printer_select_pen($handle, $pen);

        printer_delete_font($font);
        printer_end_page($handle);
        printer_end_doc($handle);
        printer_close($handle);
    }
}
function print_open_close_bill_mode2($printer_name = '', $print_data = '', $user_data,$printer_setting=array()){
  $GLOBALS['printer_setting']=$printer_setting;
  $printer_name=convert_printer_name($printer_name);
  if (function_exists('printer_open')) {
      $ci=&get_instance(); 
        // $min_80=$ci->config->item("72_80");
        // set up
        $var_max_char_length = 25;
        $yPos                = 0;

        $handle = printer_open($printer_name);
        if($handle==false)return false;
        printer_set_option($handle, PRINTER_MODE, "RAW"); // cut mode
        printer_start_doc($handle,"Start Doc");
        printer_start_page($handle);

        $font = printer_create_font("Courier New", 25, 15, PRINTER_FW_BOLD, false, false, false, 0);
        printer_select_font($handle, $font);
          
        printer_draw_text($handle, "TANGGAL OPEN", 0, $yPos);
        printer_draw_text($handle, ": ".date("d/m/Y",strtotime($print_data['oc_cashier']->open_at)), 190, $yPos);
        $yPos = $yPos + 40;
        printer_draw_text($handle, "JAM OPEN", 0, $yPos);
        printer_draw_text($handle, ": ".date("H:i:s",strtotime($print_data['oc_cashier']->open_at)), 190, $yPos);
        $yPos = $yPos + 40;

        printer_draw_text($handle, "WAKTU CLOSE", 0, $yPos);
        printer_draw_text($handle, ": ".date("d/m/Y",strtotime($print_data['oc_cashier']->close_at)), 190, $yPos);
        $yPos = $yPos + 40;
        printer_draw_text($handle, "JAM CLOSE", 0, $yPos);
        printer_draw_text($handle, ": ".date("H:i:s",strtotime($print_data['oc_cashier']->close_at)), 190, $yPos);
        $yPos = $yPos + 40;

        printer_draw_text($handle, "OPEN BY", 0, $yPos);
        printer_draw_text($handle, ": ".$print_data['oc_cashier']->open_by_name, 190, $yPos);
        $yPos = $yPos + 40;

        printer_draw_text($handle, "CLOSE BY", 0, $yPos);
        printer_draw_text($handle, ": ".$print_data['oc_cashier']->close_by_name, 190, $yPos);
        $yPos = $yPos + 40;
        
        printer_draw_text($handle, "JML TRANSAKSI", 0, $yPos);
        printer_draw_text($handle, ": ".$print_data['transaction']->transaction_count, 190, $yPos);
        
        $yPos = $yPos + 50;
        printer_draw_line($handle, 0, $yPos, ($printer_setting->description==48 ? 370 : 600), $yPos);
        $yPos = $yPos + 10;
        _write_text_center_align($handle, "CLOSE CASHIER", $yPos, ($printer_setting->description==48 ? 150 : 240));
        $yPos = $yPos + 30;
        printer_draw_line($handle, 0, $yPos, ($printer_setting->description==48 ? 370 : 600), $yPos);
        $yPos = $yPos + 30;
        printer_draw_text($handle, "CASH", 0, $yPos);
        printer_draw_text($handle, ": ".convert_rupiah($print_data['cash']->amount+$print_data['cash_dp']->amount), 175, $yPos);
        $yPos = $yPos + 40;

        printer_draw_text($handle, "DP IN", 0, $yPos);
        printer_draw_text($handle, ": (".convert_rupiah($print_data['cash_dp']->amount).")", 175, $yPos);
        $yPos = $yPos + 40;
        
        printer_draw_text($handle, "DP TRANSFER", 0, $yPos);
        printer_draw_text($handle, ": (".convert_rupiah($print_data['transfer_dp']->amount).")", 175, $yPos);
        $yPos = $yPos + 40;
        
        printer_draw_text($handle, "DP KARTU", 0, $yPos);
        printer_draw_text($handle, ": (".convert_rupiah($print_data['transfer_direct_dp']->amount).")", 175, $yPos);
        $yPos = $yPos + 40;
        // $total_plus=$print_data['cash']->amount+$print_data['cash_dp']->amount+$print_data['transfer_dp']->amount;
        $total_plus=$print_data['cash']->amount;
        foreach($print_data['debit'] as $d){
          $total_plus+=$d->amount;
          printer_draw_text($handle, strtoupper($d->bank_name)."-DB", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($d->amount), 175, $yPos);
          $yPos = $yPos + 40;
        }
        foreach($print_data['credit'] as $d){              
          $total_plus+=$d->amount;
          printer_draw_text($handle, strtoupper($d->bank_name)."-CR", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($d->amount), 175, $yPos);
          $yPos = $yPos + 40;
        }
        foreach($print_data['flazz'] as $d){              
          $total_plus+=$d->amount;
          printer_draw_text($handle, strtoupper($d->bank_name)."-Flazz", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($d->amount), 175, $yPos);
          $yPos = $yPos + 40;
        }
        printer_draw_text($handle, "+", ($printer_setting->description==48 ? 380 : 580), $yPos);
        printer_draw_line($handle, 0, $yPos, ($printer_setting->description==48 ? 370 : 600), $yPos);
        $yPos = $yPos + 10;
        printer_draw_text($handle, "JUMLAH", 0, $yPos);
        printer_draw_text($handle, ": ".convert_rupiah($total_plus), 175, $yPos);
        $yPos = $yPos + 60;
        
        $total_minus = 
          $print_data['petty_cash']->amount + 
          $print_data['dp_out']->amount+
          $print_data['bon']->amount + 
          $print_data['voucher']->amount +
          $print_data['compliment']->amount + 
          $print_data['discount']->amount +
          $print_data['cash_company']->amount + 
          $print_data['pending_bill_company']->amount + 
          $print_data['pending_bill_employee']->amount + 
          $print_data['delivery']->amount;
          
        printer_draw_text($handle, "KAS KECIL", 0, $yPos);
        printer_draw_text($handle, ": ".convert_rupiah($print_data['petty_cash']->amount), 175, $yPos);
        $yPos = $yPos + 40;
        
        printer_draw_text($handle, "DELIVERY", 0, $yPos);
        printer_draw_text($handle, ": ".convert_rupiah($print_data['delivery']->amount), 175, $yPos);
        $yPos = $yPos + 40;
        
        printer_draw_text($handle, "DP OUT", 0, $yPos);
        printer_draw_text($handle, ": ".convert_rupiah($print_data['dp_out']->amount), 175, $yPos);
        $yPos = $yPos + 40;
        
        printer_draw_text($handle, "BON", 0, $yPos);
        printer_draw_text($handle, ": ".convert_rupiah($print_data['bon']->amount), 175, $yPos);
        $yPos = $yPos + 40;
        if($print_data['voucher']->amount>0){
          printer_draw_text($handle, "VOUCHER", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['voucher']->amount), 175, $yPos);
          $yPos = $yPos + 40;          
        }
        if($print_data['compliment']->amount>0){
          printer_draw_text($handle, "COMPLIMENT", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['compliment']->amount), 175, $yPos);
          $yPos = $yPos + 40;          
        }
        if($print_data['discount']->amount>0){
          printer_draw_text($handle, "DISCOUNT", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['discount']->amount), 175, $yPos);
          $yPos = $yPos + 40;          
        }
        if($print_data['cash_company']->amount>0){
          printer_draw_text($handle, "CASH COMPANY", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['cash_company']->amount), 175, $yPos);
          $yPos = $yPos + 40;          
        }
        if($print_data['pending_bill_company']->amount>0){
          printer_draw_text($handle, "PB COMPANY", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['pending_bill_company']->amount), 175, $yPos);
          $yPos = $yPos + 40;          
        }
        if($print_data['pending_bill_employee']->amount>0){
          printer_draw_text($handle, "PB EMPLOYEE ", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['pending_bill_employee']->amount), 175, $yPos);
          $yPos = $yPos + 40;          
        }
        printer_draw_text($handle, "+", ($printer_setting->description==48 ? 380 : 580), $yPos);
        printer_draw_line($handle, 0, $yPos, ($printer_setting->description==48 ? 350 : 600), $yPos);
        $yPos = $yPos + 10;
        printer_draw_text($handle, "JUMLAH", 0, $yPos);
        printer_draw_text($handle, ": ".convert_rupiah($total_minus), 175, $yPos);
        $yPos = $yPos + 40;
        printer_draw_text($handle, "OMSET", 0, $yPos);
        $total_plus+=$print_data['petty_cash']->amount+$print_data['dp_out']->amount+$print_data['delivery']->amount+$print_data['bon']->amount;
        printer_draw_text($handle, ": ".convert_rupiah($total_plus), 175, $yPos);
        $yPos = $yPos + 40;
        printer_draw_text($handle, "SALDO AWAL", 0, $yPos);
        printer_draw_text($handle, ": ".convert_rupiah($print_data['oc_cashier']->begin_balance), 175, $yPos);
        $yPos = $yPos + 70;
        if($print_data['balance_cash_history']->amount>0){
          printer_draw_text($handle, "PENAMBAH SALDO", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['balance_cash_history']->amount), 175, $yPos);
          $yPos = $yPos + 70;
          printer_draw_text($handle, "SALDO AKHIR", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['oc_cashier']->begin_balance+$print_data['balance_cash_history']->amount), 175, $yPos);
          $yPos = $yPos + 70;
        }
        if($print_data['setting']['cash_on_hand']==1){
          printer_draw_line($handle, 0, $yPos, ($printer_setting->description==48 ? 350 : 600), $yPos);
          $yPos = $yPos + 10;
          printer_draw_text($handle, "CASH ON HAND", 0, $yPos);
          printer_draw_text($handle, ":".convert_rupiah($print_data['oc_cashier']->cash_on_hand), 175, $yPos);
          $yPos = $yPos + 40;
          printer_draw_text($handle, "SELISIH CASH", 0, $yPos);
          printer_draw_text($handle, ":".convert_rupiah($print_data['cash']->amount+$print_data['cash_dp']->amount - $print_data['oc_cashier']->cash_on_hand), 175, $yPos);
          $yPos = $yPos + 30;
        }
        $yPos = $yPos + 40;
        printer_draw_text($handle, "KASIR", ($printer_setting->description==48 ? 20 : 130), $yPos);
        printer_draw_text($handle, "SUPERVISOR", ($printer_setting->description==48 ? 175 : 300), $yPos);
        $yPos = $yPos + 140;
        printer_draw_text($handle, "(.......)", ($printer_setting->description==48 ? 15 : 110), $yPos);
        printer_draw_text($handle, "(..........)", ($printer_setting->description==48 ? 170 : 290), $yPos);
        $yPos = $yPos + 50;
        // Header
        $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
        printer_select_pen($handle, $pen);

        printer_delete_font($font);
        printer_end_page($handle);
        printer_end_doc($handle);
        printer_close($handle);
    }
}
function print_open_close_bill_mode3($printer_name = '', $print_data = '', $user_data,$printer_setting=array()){
  $GLOBALS['printer_setting']=$printer_setting;
  $printer_name=convert_printer_name($printer_name);
  if (function_exists('printer_open')) {
      $ci=&get_instance(); 
        // $min_80=$ci->config->item("72_80");
        // set up
        $var_max_char_length = 25;
        $yPos                = 0;

        $handle = printer_open($printer_name);
        if($handle==false)return false;
        printer_set_option($handle, PRINTER_MODE, "RAW"); // cut mode
        printer_start_doc($handle,"Start Doc");
        printer_start_page($handle);

        $font = printer_create_font("Courier New", 25, 15, PRINTER_FW_BOLD, false, false, false, 0);
        printer_select_font($handle, $font);
        if(isset($print_data['setting']['printer_logo']) && (!empty($printer_setting) && $printer_setting->default == 1) ){
          $path_logo=FCPATH.$print_data['setting']['printer_logo'];
          if(file_exists($path_logo)){ 
            printer_draw_bmp($handle, $path_logo, ($printer_setting->description==48 ? 35 : ($printer_setting->description==72 ? 75 : 130)), 50,300,150);
            $yPos+=200;
          }          
        }
        _write_text_center_align($handle, strtoupper($print_data['store_data']->store_name), $yPos, ($printer_setting->description==48 ? 150 : ($printer_setting->description==72 ? 185 : 240)), TRUE);
        $yPos = $yPos + 27;
        $string = trim($print_data['store_data']->store_address);

        if (strlen($string) > 25) {
            $string = wordwrap($string, 25);
            $string = explode("\n", $string, 3);
            $count  = 1;
            foreach ($string as $str) {
                 _write_text_center_align($handle, $str, $yPos, ($printer_setting->description==48 ? 150 : ($printer_setting->description==72 ? 160 : 215)));
                $count++;
                $yPos = $yPos + 27;
            }
        }else {
            _write_text_center_align($handle, $string, $yPos, ($printer_setting->description==48 ? 150 : ($printer_setting->description==72 ? 160 : 215)));
            $yPos = $yPos + 27;
        }
        _write_text_center_align($handle, $print_data['store_data']->store_phone, $yPos, ($printer_setting->description==48 ? 150 : ($printer_setting->description==72 ? 185 : 240)));
        

        $yPos = $yPos + 50;
        printer_draw_text($handle, "TANGGAL OPEN", 0, $yPos);
        printer_draw_text($handle, ":".date("d/m/Y",strtotime($print_data['oc_cashier']->open_at)), 240, $yPos);
        $yPos = $yPos + 40;
        printer_draw_text($handle, "JAM OPEN", 0, $yPos);
        printer_draw_text($handle, ":".date("H:i:s",strtotime($print_data['oc_cashier']->open_at)), 240, $yPos);
        $yPos = $yPos + 40;

        printer_draw_text($handle, "WAKTU CLOSE", 0, $yPos);
        printer_draw_text($handle, ":".date("d/m/Y",strtotime($print_data['oc_cashier']->close_at)), 240, $yPos);
        $yPos = $yPos + 40;
        printer_draw_text($handle, "JAM CLOSE", 0, $yPos);
        printer_draw_text($handle, ":".date("H:i:s",strtotime($print_data['oc_cashier']->close_at)), 240, $yPos);
        $yPos = $yPos + 40;

        printer_draw_text($handle, "OPEN BY", 0, $yPos);
        printer_draw_text($handle, ":".$print_data['oc_cashier']->open_by_name, 240, $yPos);
        $yPos = $yPos + 40;

        printer_draw_text($handle, "CLOSE BY", 0, $yPos);
        printer_draw_text($handle, ":".$print_data['oc_cashier']->close_by_name, 240, $yPos);
        $yPos = $yPos + 40;
        
        printer_draw_text($handle, "JML TRANSAKSI", 0, $yPos);
        printer_draw_text($handle, ":".$print_data['transaction']->transaction_count, 240, $yPos);
        
        $yPos = $yPos + 50;
        printer_draw_line($handle, 0, $yPos, ($printer_setting->description==48 ? 370 : 600), $yPos);
        $yPos = $yPos + 10;
        _write_text_center_align($handle, "CLOSE CASHIER", $yPos, ($printer_setting->description==48 ? 150 : 225));
        $yPos = $yPos + 30;
        printer_draw_line($handle, 0, $yPos, ($printer_setting->description==48 ? 370 : 600), $yPos);
        $yPos = $yPos + 30;
        
        
        
        printer_draw_text($handle, "NET SALES", 0, $yPos);
        printer_draw_text($handle, ":".convert_rupiah($print_data['net_sales']->amount), 240, $yPos);
        $yPos = $yPos + 40;

        printer_draw_text($handle, "Ppn", 0, $yPos);
        printer_draw_text($handle, ":".convert_rupiah($print_data['taxes']->amount), 240, $yPos);
        $yPos = $yPos + 40;
        
        printer_draw_text($handle, "PEMBULATAN", 0, $yPos);
        printer_draw_text($handle, ":".convert_rupiah($print_data['round_up']->amount), 240, $yPos);
        $yPos = $yPos + 40;
        
        printer_draw_text($handle, "ONGKOS KIRIM", 0, $yPos);
        printer_draw_text($handle, ":".convert_rupiah($print_data['delivery_charge']->amount), 240, $yPos);
        $yPos = $yPos + 40;
        printer_draw_text($handle, "+", ($printer_setting->description==48 ? 380 : 580), $yPos);
        printer_draw_line($handle, 0, $yPos, ($printer_setting->description==48 ? 370 : 600), $yPos);
        $yPos = $yPos + 10;
        $omzet=$print_data['net_sales']->amount+$print_data['taxes']->amount+$print_data['round_up']->amount+$print_data['delivery_charge']->amount;
        printer_draw_text($handle, "OMSET", 0, $yPos);
        printer_draw_text($handle, ":".convert_rupiah($omzet), 240, $yPos);
        $yPos = $yPos + 40;
        printer_draw_text($handle, "KAS KECIL", 0, $yPos);
        printer_draw_text($handle, ":".convert_rupiah($print_data['petty_cash']->amount), 240, $yPos);
        $yPos = $yPos + 40;
        printer_draw_text($handle, "DP OUT", 0, $yPos);
        printer_draw_text($handle, ":".convert_rupiah($print_data['dp_out']->amount), 240, $yPos);
        $yPos = $yPos + 40;
        printer_draw_text($handle, "KOMISI DELIVERY", 0, $yPos);
        printer_draw_text($handle, ":".convert_rupiah($print_data['delivery']->amount), 240, $yPos);
        $yPos = $yPos + 40;
        printer_draw_text($handle, "DISCOUNT", 0, $yPos);
        printer_draw_text($handle, ":".convert_rupiah($print_data['discount']->amount), 240, $yPos);
        $yPos = $yPos + 40;          
        printer_draw_text($handle, "BON", 0, $yPos);
        printer_draw_text($handle, ":".convert_rupiah($print_data['bon']->amount), 240, $yPos);
        $yPos = $yPos + 40;
        if($print_data['voucher']->amount>0){
          printer_draw_text($handle, "VOUCHER", 0, $yPos);
          printer_draw_text($handle, ":".convert_rupiah($print_data['voucher']->amount), 240, $yPos);
          $yPos = $yPos + 40;          
        }
        if($print_data['compliment']->amount>0){
          printer_draw_text($handle, "COMPLIMENT", 0, $yPos);
          printer_draw_text($handle, ":".convert_rupiah($print_data['compliment']->amount), 240, $yPos);
          $yPos = $yPos + 40;          
        }
        if($print_data['cash_company']->amount>0){
          printer_draw_text($handle, "CASH COMPANY", 0, $yPos);
          printer_draw_text($handle, ":".convert_rupiah($print_data['cash_company']->amount), 240, $yPos);
          $yPos = $yPos + 40;          
        }
        if($print_data['pending_bill_company']->amount>0){
          printer_draw_text($handle, "PB COMPANY", 0, $yPos);
          printer_draw_text($handle, ":".convert_rupiah($print_data['pending_bill_company']->amount), 240, $yPos);
          $yPos = $yPos + 40;          
        }
        if($print_data['pending_bill_employee']->amount>0){
          printer_draw_text($handle, "PB EMPLOYEE ", 0, $yPos);
          printer_draw_text($handle, ":".convert_rupiah($print_data['pending_bill_employee']->amount), 240, $yPos);
          $yPos = $yPos + 40;          
        }
        printer_draw_text($handle, "-", ($printer_setting->description==48 ? 380 : 580), $yPos);
        printer_draw_line($handle, 0, $yPos, ($printer_setting->description==48 ? 370 : 600), $yPos);
        $yPos = $yPos + 10;
        $total=$omzet-$print_data['voucher']->amount-$print_data['compliment']->amount-$print_data['cash_company']->amount-$print_data['pending_bill_company']->amount-$print_data['pending_bill_employee']->amount;
        $total-=($print_data['petty_cash']->amount+$print_data['dp_out']->amount+$print_data['delivery']->amount+$print_data['discount']->amount+$print_data['bon']->amount);
        printer_draw_text($handle, "JML SETORAN", 0, $yPos);
        printer_draw_text($handle, ":".convert_rupiah($total), 240, $yPos);
        $yPos = $yPos + 40;
        $total_card=0;
        foreach($print_data['debit'] as $d){
          $total_card+=$d->amount;
          printer_draw_text($handle, strtoupper($d->bank_name)."-DB", 0, $yPos);
          printer_draw_text($handle, ":".convert_rupiah($d->amount), 240, $yPos);
          $yPos = $yPos + 40;
        }
        foreach($print_data['credit'] as $d){              
          $total_card+=$d->amount;
          printer_draw_text($handle, strtoupper($d->bank_name)."-CR", 0, $yPos);
          printer_draw_text($handle, ":".convert_rupiah($d->amount), 240, $yPos);
          $yPos = $yPos + 40;
        }
        foreach($print_data['flazz'] as $d){              
          $total_card+=$d->amount;
          printer_draw_text($handle, strtoupper($d->bank_name)."-Flazz", 0, $yPos);
          printer_draw_text($handle, ":".convert_rupiah($d->amount), 240, $yPos);
          $yPos = $yPos + 40;
        }
        printer_draw_text($handle, "-", ($printer_setting->description==48 ? 380 : 580), $yPos);
        printer_draw_line($handle, 0, $yPos, ($printer_setting->description==48 ? 370 : 600), $yPos);
        $yPos = $yPos + 10;
        $total2=$total-$total_card;
        printer_draw_text($handle, "CASH", 0, $yPos);
        printer_draw_text($handle, ":".convert_rupiah($total2), 240, $yPos);
        $yPos = $yPos + 40;
        if($print_data['setting']['cash_on_hand']==1){
          printer_draw_text($handle, "CASH ON HAND", 0, $yPos);
          printer_draw_text($handle, ":".convert_rupiah($print_data['oc_cashier']->cash_on_hand), 240, $yPos);
          $yPos = $yPos + 40;
        }else{
          printer_draw_text($handle, "REAL CASH", 0, $yPos);
          printer_draw_text($handle, ":", 240, $yPos);
          $yPos = $yPos + 40;
        }
        printer_draw_line($handle, 0, $yPos, ($printer_setting->description==48 ? 370 : 600), $yPos);
        $yPos = $yPos + 10;
        printer_draw_text($handle, "SELISIH", 0, $yPos);
        if($print_data['setting']['cash_on_hand']==1){
          printer_draw_text($handle, ":".convert_rupiah($total2-$print_data['oc_cashier']->cash_on_hand), 240, $yPos);
        }else{
          printer_draw_text($handle, ":", 240, $yPos);
        }
        $yPos = $yPos + 150;
        
        printer_draw_text($handle, "DP CASH", 0, $yPos);
        printer_draw_text($handle, ":".convert_rupiah($print_data['cash_dp']->amount), 240, $yPos);
        $yPos = $yPos + 40;
        printer_draw_text($handle, "DP CARD", 0, $yPos);
        printer_draw_text($handle, ":".convert_rupiah($print_data['transfer_direct_dp']->amount), 240, $yPos);
        $yPos = $yPos + 40;
        printer_draw_text($handle, "DP TRANSFER", 0, $yPos);
        printer_draw_text($handle, ":".convert_rupiah($print_data['transfer_dp']->amount), 240, $yPos);
        $yPos = $yPos + 40;
        $yPos = $yPos + 40;
        printer_draw_text($handle, "KASIR", ($printer_setting->description==48 ? 50 : 130), $yPos);
        printer_draw_text($handle, "SUPERVISOR", ($printer_setting->description==48 ? 200 : 300), $yPos);
        $yPos = $yPos + 170;
        printer_draw_text($handle, "(........)", ($printer_setting->description==48 ? 15 : 90), $yPos);
        printer_draw_text($handle, "(..........)", ($printer_setting->description==48 ? 240 : 290), $yPos);
        $yPos = $yPos + 50;
        // Header
        $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
        printer_select_pen($handle, $pen);

        printer_delete_font($font);
        printer_end_page($handle);
        printer_end_doc($handle);
        printer_close($handle);
    }
}

function print_open_close_bill_mode4($printer_name = '', $print_data = '', $user_data,$printer_setting=array()){
  $GLOBALS['printer_setting']=$printer_setting;
  $printer_name=convert_printer_name($printer_name);
  if (function_exists('printer_open')) {
      $ci=&get_instance(); 
        // $min_80=$ci->config->item("72_80");
        // set up
        $var_max_char_length = 25;
        $yPos                = 0;

        $handle = printer_open($printer_name);
        if($handle==false)return false;
        printer_set_option($handle, PRINTER_MODE, "RAW"); // cut mode
        printer_start_doc($handle,"Start Doc");
        printer_start_page($handle);

        $font = printer_create_font("Courier New", 25, 15, PRINTER_FW_BOLD, false, false, false, 0);
        printer_select_font($handle, $font);
        
       
        printer_draw_text($handle, "TANGGAL OPEN", 0, $yPos);
        printer_draw_text($handle, ": ".date("d/m/Y",strtotime($print_data['oc_cashier']->open_at)), 190, $yPos);
        $yPos = $yPos + 40;
        printer_draw_text($handle, "JAM OPEN", 0, $yPos);
        printer_draw_text($handle, ": ".date("H:i:s",strtotime($print_data['oc_cashier']->open_at)), 190, $yPos);
        $yPos = $yPos + 40;

        printer_draw_text($handle, "WAKTU CLOSE", 0, $yPos);
        printer_draw_text($handle, ": ".date("d/m/Y",strtotime($print_data['oc_cashier']->close_at)), 190, $yPos);
        $yPos = $yPos + 40;
        printer_draw_text($handle, "JAM CLOSE", 0, $yPos);
        printer_draw_text($handle, ": ".date("H:i:s",strtotime($print_data['oc_cashier']->close_at)), 190, $yPos);
        $yPos = $yPos + 40;

        printer_draw_text($handle, "OPEN BY", 0, $yPos);
        printer_draw_text($handle, ": ".$print_data['oc_cashier']->open_by_name, 190, $yPos);
        $yPos = $yPos + 40;

        printer_draw_text($handle, "CLOSE BY", 0, $yPos);
        printer_draw_text($handle, ": ".$print_data['oc_cashier']->close_by_name, 190, $yPos);
        $yPos = $yPos + 40;
        
        printer_draw_text($handle, "JML TRANSAKSI", 0, $yPos);
        printer_draw_text($handle, ": ".$print_data['transaction']->transaction_count, 190, $yPos);
        
        $yPos = $yPos + 50;
        printer_draw_line($handle, 0, $yPos, ($printer_setting->description==48 ? 370 : 600), $yPos);
        $yPos = $yPos + 10;
        _write_text_center_align($handle, "CLOSE CASHIER", $yPos, ($printer_setting->description==48 ? 150 : 240));
        $yPos = $yPos + 30;
        printer_draw_line($handle, 0, $yPos, ($printer_setting->description==48 ? 370 : 600), $yPos);
        $yPos = $yPos + 30;

        printer_draw_text($handle, "SALDO AWAL", 0, $yPos);
        printer_draw_text($handle, ": ".convert_rupiah($print_data['oc_cashier']->begin_balance), 175, $yPos);
        $yPos = $yPos + 40;

        $pendapatan = 0;
        $total_plus=$print_data['cash']->amount;
        $total_card = 0;
        foreach($print_data['debit'] as $d){
          $total_plus+=$d->amount; 
          $total_card += $d->amount; 
        }
        foreach($print_data['credit'] as $d){              
          $total_plus+=$d->amount; 
          $total_card += $d->amount; 
        }

        $pendapatan +=  $total_plus + 
                        $print_data['cash_dp']->amount 
                        + $print_data['transfer_dp']->amount 
                        + $print_data['transfer_direct_dp']->amount;

        printer_draw_text($handle, "PENDAPATAN", 0, $yPos);
        printer_draw_text($handle, ": ".convert_rupiah($pendapatan), 175, $yPos);
        $yPos = $yPos + 40; 


        printer_draw_text($handle, "Cash", 0, $yPos);
        printer_draw_text($handle, ": ".convert_rupiah($print_data['cash']->amount), 175, $yPos);
        $yPos = $yPos + 40; 
        if($print_data['debit']){
          foreach($print_data['debit'] as $d){ 
            printer_draw_text($handle, strtoupper($d->bank_name)."-DB", 0, $yPos);
            printer_draw_text($handle, ": ".convert_rupiah($d->amount), 175, $yPos);
            $yPos = $yPos + 40;
          }  
        }else{
            printer_draw_text($handle, "Debit", 0, $yPos);
            printer_draw_text($handle, ": ".convert_rupiah(0), 175, $yPos);
            $yPos = $yPos + 40;
        }

        if($print_data['credit']){
          foreach($print_data['credit'] as $d){  
            printer_draw_text($handle, strtoupper($d->bank_name)."-CR", 0, $yPos);
            printer_draw_text($handle, ": ".convert_rupiah($d->amount), 175, $yPos);
            $yPos = $yPos + 40;
          }
        }else{
           printer_draw_text($handle, "Credit", 0, $yPos);
           printer_draw_text($handle, ": ".convert_rupiah(0), 175, $yPos);
           $yPos = $yPos + 40;
        } 

        if($print_data['flazz']){
          foreach($print_data['flazz'] as $d){  
            printer_draw_text($handle, strtoupper($d->bank_name)."-Flazz", 0, $yPos);
            printer_draw_text($handle, ": ".convert_rupiah($d->amount), 175, $yPos);
            $yPos = $yPos + 40;
          }
        }else{
           printer_draw_text($handle, "Flazz", 0, $yPos);
           printer_draw_text($handle, ": ".convert_rupiah(0), 175, $yPos);
           $yPos = $yPos + 40;
        }  

        
        $total_minus =  
          $print_data['dp_out']->amount+
          $print_data['bon']->amount + 
          $print_data['voucher']->amount +
          $print_data['compliment']->amount + 
          $print_data['discount']->amount +
          $print_data['cash_company']->amount + 
          $print_data['pending_bill_company']->amount + 
          $print_data['pending_bill_employee']->amount + 
          $print_data['delivery']->amount; 

        if($print_data['delivery']->amount >0){
          printer_draw_text($handle, "DELIVERY", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['delivery']->amount), 175, $yPos);
          $yPos = $yPos + 40;
        }
        
        if($print_data['dp_out']->amount>0){
          printer_draw_text($handle, "DP OUT", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['dp_out']->amount), 175, $yPos);
          $yPos = $yPos + 40;
        }
        
        if($print_data['bon']->amount>0){
          printer_draw_text($handle, "BON", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['bon']->amount), 175, $yPos);
          $yPos = $yPos + 40;
        }

        
        if($print_data['voucher']->amount>0){
          printer_draw_text($handle, "VOUCHER", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['voucher']->amount), 175, $yPos);
          $yPos = $yPos + 40;          
        }
        if($print_data['compliment']->amount>0){
          printer_draw_text($handle, "COMPLIMENT", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['compliment']->amount), 175, $yPos);
          $yPos = $yPos + 40;          
        }
        if($print_data['discount']->amount>0){
          printer_draw_text($handle, "DISCOUNT", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['discount']->amount), 175, $yPos);
          $yPos = $yPos + 40;          
        }
        if($print_data['cash_company']->amount>0){
          printer_draw_text($handle, "CASH COMPANY", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['cash_company']->amount), 175, $yPos);
          $yPos = $yPos + 40;          
        }
        if($print_data['pending_bill_company']->amount>0){
          printer_draw_text($handle, "PB COMPANY", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['pending_bill_company']->amount), 175, $yPos);
          $yPos = $yPos + 40;          
        }
        if($print_data['pending_bill_employee']->amount>0){
          printer_draw_text($handle, "PB EMPLOYEE ", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['pending_bill_employee']->amount), 175, $yPos);
          $yPos = $yPos + 40;          
        }
        printer_draw_text($handle, "+", ($printer_setting->description==48 ? 380 : 580), $yPos);
        printer_draw_line($handle, 0, $yPos, ($printer_setting->description==48 ? 350 : 600), $yPos);
        $yPos = $yPos + 10;
        printer_draw_text($handle, "JUMLAH", 0, $yPos);
        printer_draw_text($handle, ": ".convert_rupiah($print_data['cash']->amount+$print_data['oc_cashier']->begin_balance), 175, $yPos);
        $yPos = $yPos + 60;

         
        
          printer_draw_text($handle, "MODAL", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['balance_cash_history']->amount), 175, $yPos);
          $yPos = $yPos + 40; 
      
      
        printer_draw_text($handle, "KAS KECIL", 0, $yPos);
        printer_draw_text($handle, ": ".convert_rupiah($print_data['petty_cash']->amount), 175, $yPos);
        $yPos = $yPos + 40; 
        printer_draw_line($handle, 0, $yPos, ($printer_setting->description==48 ? 350 : 600), $yPos);
        $yPos = $yPos + 10;

        printer_draw_text($handle, "JUMLAH", 0, $yPos);
        printer_draw_text($handle, ": ".convert_rupiah($print_data['balance_cash_history']->amount - $print_data['petty_cash']->amount), 175, $yPos);
        $yPos = $yPos + 40;
        $total_penambah = $total_plus;
        $total_petty_cash = $print_data['balance_cash_history']->amount - $print_data['petty_cash']->amount;

        printer_draw_line($handle, 0, $yPos, ($printer_setting->description==48 ? 350 : 600), $yPos);
        $yPos = $yPos + 10;

        printer_draw_text($handle, "GRAND TOTAL", 0, $yPos);
        printer_draw_text($handle, ": ".convert_rupiah(($total_penambah - $total_card) + $total_petty_cash + $print_data['oc_cashier']->begin_balance), 175, $yPos);
        $yPos = $yPos + 70;
        $grand_total = ($total_penambah - $total_card) + $total_petty_cash + $print_data['oc_cashier']->begin_balance;
        if($print_data['setting']['cash_on_hand']==1){
          printer_draw_line($handle, 0, $yPos, ($printer_setting->description==48 ? 350 : 600), $yPos);
          $yPos = $yPos + 10;
          printer_draw_text($handle, "CASH ON HAND", 0, $yPos);
          printer_draw_text($handle, ":".convert_rupiah($print_data['oc_cashier']->cash_on_hand), 175, $yPos);
          $yPos = $yPos + 40;
          printer_draw_text($handle, "SELISIH CASH", 0, $yPos);
          // printer_draw_text($handle, ":".convert_rupiah($print_data['cash']->amount+$print_data['cash_dp']->amount - $print_data['oc_cashier']->cash_on_hand), 175, $yPos);
          printer_draw_text($handle, ":".convert_rupiah($grand_total - $print_data['oc_cashier']->cash_on_hand), 175, $yPos);
          $yPos = $yPos + 30;
        }
        $yPos = $yPos + 40;
        printer_draw_text($handle, "KASIR", ($printer_setting->description==48 ? 50 : 130), $yPos);
        printer_draw_text($handle, "SUPERVISOR", ($printer_setting->description==48 ? 200 : 300), $yPos);
        $yPos = $yPos + 140;
        printer_draw_text($handle, "(.......)", ($printer_setting->description==48 ? 15 : 110), $yPos);
        printer_draw_text($handle, "(..........)", ($printer_setting->description==48 ? 175 : 290), $yPos);
        $yPos = $yPos + 50;
        // Header
        $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
        printer_select_pen($handle, $pen);

        printer_delete_font($font);
        printer_end_page($handle);
        printer_end_doc($handle);
        printer_close($handle);
    }
}

if (! function_exists('_write_price')) {
    function _write_price($printer, $input, $yPos, $xPos, $printer_setting = array())
    {
        $s = strlen($input);
        $y = $xPos;
        for ($i = 1; $i < $s + 1; $i++) {
            $u = $i * -1;
            printer_draw_text($printer, substr($input, $u, 1), $y, $yPos);            
            $y = $y - 15;
        }
    }
}

if (! function_exists('_write_text_center_align')) {
    function _write_text_center_align($printer, $input, $yPos, $xPos, $upper = false)
    {
        $input = trim($input);
        if ($upper === true) {
            $textLength = imagefontwidth(10) * strlen($input) * 1.1;
        }
        else {
            $textLength = imagefontwidth(10) * strlen($input);
        }
        $x = ($xPos - ($textLength / 2));

        printer_draw_text($printer, $input, $x, $yPos);


    }
}


if (! function_exists('_write_text_center')) {
    function _write_text_center($printer, $input, $yPos, $xPos = 0, $upper = false, $size = 72)
    {
        $input = trim($input); 
        
        if($size == 72){
          $max_length = 38;  
        }else{
          $max_length = 26;
        }

        $input = trim($input);
        if ($upper === true) {
            $textLength = imagefontwidth(10) * strlen($input) * 1.1;
        }
        else {
            $textLength = imagefontwidth(10) * strlen($input);
        }
        $x = ($xPos - ($textLength / 2));

        $input = str_pad($input, $max_length," ", STR_PAD_BOTH);
        printer_draw_text($printer, $input, $x, $yPos);
    }
}


if (! function_exists('_write_open_close_bill')) {
    function _write_open_close_bill($handle, $order, $yPos, $length, $type="order")
    {
        if ($type=="order") {
            if (! empty($order->menu_name)) {
                $string     = $order->menu_name;
                $orderCount = $order->total_quantity;
            }
            else {
                $string     = $order->name;
                $orderCount = $order->total_quantity;
            }

        }
        else {
            $string     = $order->name;
            $orderCount = '';
        }

        $string = trim($string);

        if (strlen($string) > $length) {
            $string = wordwrap($string, $length);
            $string = explode("\n", $string, 3);
            $count  = 1;
            foreach ($string as $str) {
                printer_draw_text($handle, $str, 0, $yPos);
                $count++;
                $yPos = $yPos + 25;
            }
      printer_draw_text($handle, $orderCount . " Pcs ", 0, $yPos);
        }
        else {
            printer_draw_text($handle, $string, 0, $yPos);
            $yPos = $yPos + 25;
      printer_draw_text($handle, $orderCount . " Pcs ", 0, $yPos);
            $yPos = $yPos + 25;
        }



        return $yPos;
    }
}

if (! function_exists('print_checkout_bill')) {
    function print_checkout_bill($printer_name = '', $print_data = array(), $user_data, $pending = false,$is_temporary=TRUE,$printer_setting=array())
    {
        $ci=&get_instance(); 
        // $min_80=$ci->config->item("72_80");
        $GLOBALS['printer_setting']=$printer_setting;
        $printer_name=convert_printer_name($printer_name);
        if (function_exists('printer_open')) {
            // set up
            if($printer_setting->font_size==2){
              $var_max_char_length = ($printer_setting->description == 48 ? 20 : 35);
            }else{
              $var_max_char_length = ($printer_setting->description == 48 ? 25 : 40);  
            }
            
            $yPos                = 0;
            $handle = printer_open($printer_name);
            if($handle==false)return false;
            printer_set_option($handle, PRINTER_MODE, "RAW"); // cut mode
            printer_start_doc($handle,"Start Doc");
            printer_start_page($handle);
            $width=25;
            $height=15;
            
            if($printer_setting->font_size==2){
              $width=31;
              $height=19;
            }
            $font = printer_create_font("Courier New", $width, $height, PRINTER_FW_BOLD, false, false, false, 0);
            printer_select_font($handle, $font);
            if(isset($print_data['setting']['printer_logo']) && $print_data['setting']['printer_logo']!="" && (!empty($printer_setting) && $printer_setting->default == 1) ){
              $path_logo=FCPATH.$print_data['setting']['printer_logo'];
              if(file_exists($path_logo)){
                printer_draw_bmp($handle, $path_logo, ($printer_setting->description == 48 ? 35 : ($printer_setting->description == 72 ? 75 : 130)), 50,300,300);
                $yPos+=370;
              }
            }

            if(sizeof($print_data['header_bill'])>0){
              $string = trim($print_data['header_bill']->description);
              $length = ($printer_setting->description == 48 ? 25 : 37);

              $font = printer_create_font("Courier New", 40, 15, PRINTER_FW_BOLD, false, false, false, 0);
              printer_select_font($handle, $font);

              if (strlen($string) > $length) {
                $string = wordwrap($string, $length);
                $explode = explode("\n", $string);

                foreach($explode as $e){
                  if($printer_setting->description==48){
                    $e = substr($e, 0, $length);
                  }else{
                    $e = substr($e, 0, $length);
                  }
                  _write_text_center_align($handle, $e, $yPos, ($printer_setting->description == 48 ? 125 : ($printer_setting->description == 72 ? 160 : 215)), TRUE);
                  $yPos+=35;
                }

              } else {
                _write_text_center_align($handle, $e, $yPos, ($printer_setting->description == 48 ? 125 : ($printer_setting->description == 72 ? 160 : 215)), TRUE);
                $yPos+=35;
              }
            }

            if($is_temporary==TRUE){
              if(sizeof($print_data['bill_temporary'])>0){
                  $string = trim($print_data['bill_temporary']->description);
                  $max_length = 25;
                  if($printer_setting->font_size==2) {
                    $max_length = 20;
                  }
                  if (strlen($string) > $max_length) {
                      $string = wordwrap($string, $max_length);
                      $string = explode("\n", $string, 3); 
                      foreach ($string as $str) { 
                          _write_text_center_align($handle,$str, $yPos, ($printer_setting->description==48 ? 125 : ($printer_setting->description==72 ? 160 : 215)), TRUE,$printer_setting->description); 
                          $yPos = $yPos + 25;
                      }
                  }else { 
                      _write_text_center_align($handle,$string, $yPos, ($printer_setting->description==48 ? 125 : ($printer_setting->description==72 ? 160 : 215)), TRUE,$printer_setting->description); 
                  } 
              }else{
                $font = printer_create_font("Courier New", 35, 15, PRINTER_FW_BOLD, false, false, false, 0);
                printer_select_font($handle, $font); 
                _write_text_center_align($handle,"*BILL SEMENTARA*", $yPos,  ($printer_setting->description==48 ? 125 : ($printer_setting->description==72 ? 160 : 215)), TRUE,$printer_setting->description);
                $yPos = $yPos + 35;
              }
            }
            $yPos = $yPos + 70;
            $font = printer_create_font("Courier New", $width, $height, PRINTER_FW_BOLD, false, false, false, 0);
            printer_select_font($handle, $font); 
            _write_text_center_align($handle,strtoupper($print_data['store_data']->store_name), $yPos, ($printer_setting->description==48 ? 125 : ($printer_setting->description==72 ? 160 : 215)), TRUE,$printer_setting->description);
            $yPos = $yPos + 27;
            
            $string = trim($print_data['store_data']->store_address);
            $max_length = 25;
            if($printer_setting->font_size==2) {
              $max_length = 20;
            }
            if (strlen($string) > $max_length) {
                $string = wordwrap($string, $max_length);
                $string = explode("\n", $string, 3);
                $count  = 1;
                foreach ($string as $str) { 
                    _write_text_center_align($handle,$str, $yPos, ($printer_setting->description==48 ? 125 : ($printer_setting->description==72 ? 160 : 215)), TRUE,$printer_setting->description);
                    $count++;
                    $yPos = $yPos + 27;
                }
            }else { 
                _write_text_center_align($handle,$string, $yPos, ($printer_setting->description==48 ? 125 : ($printer_setting->description==72 ? 160 : 215)), TRUE,$printer_setting->description);
                $yPos = $yPos + 27;
            } 
            _write_text_center_align($handle,$print_data['store_data']->store_phone, $yPos, ($printer_setting->description==48 ? 125 : ($printer_setting->description==72 ? 160 : 215)), TRUE,$printer_setting->description);

            if(isset($print_data['bill']->receipt_number)){
                $yPos = $yPos + 54;
                printer_draw_text($handle, "Bill", 0, $yPos);
                printer_draw_text($handle, ":", 105, $yPos);
                printer_draw_text($handle, $print_data['bill']->receipt_number, 120, $yPos);

            }
            
            //WRITE WAITER NAME
            $yPos = $yPos + 27;
            printer_draw_text($handle, "Waiter", 0, $yPos);
            printer_draw_text($handle, ":", 105, $yPos);
            printer_draw_text($handle,  $print_data['data_order']->waiter_name, 120, $yPos);
            
            // WRITE KASIR
            $yPos = $yPos + 27;
            printer_draw_text($handle, "Kasir", 0, $yPos);
            printer_draw_text($handle, ":", 105, $yPos);
            printer_draw_text($handle,  $user_data->name, 120, $yPos);
            if(isset($print_data['data_order']->counter) && $print_data['data_order']->counter!=0){
              $yPos = $yPos + 27;
              printer_draw_text($handle, "No", 0, $yPos);
              printer_draw_text($handle, ":", 105, $yPos);
              printer_draw_text($handle, $print_data['data_order']->counter, 120, $yPos);
            }

            // WRITE TANGGAL
            $yPos = $yPos + 27;
            printer_draw_text($handle, "Tanggal", 0, $yPos);
            printer_draw_text($handle, ":", 105, $yPos);
            printer_draw_text($handle,  (isset($print_data['datetime']) ? date("d/m/Y",strtotime($print_data['datetime'])) : date("d/m/Y")), 120, $yPos);

            //_write_price($handle,"Kasir", $yPos, 350);


            // WRITE JAM
            $yPos = $yPos + 27;
            printer_draw_text($handle, "Jam", 0, $yPos);
            printer_draw_text($handle, ":", 105, $yPos);
            printer_draw_text($handle,  (isset($print_data['datetime']) ? date("H:i",strtotime($print_data['datetime'])) : date("H:i")), 120, $yPos);

            // WRITE MEJA & Customer Name
            $max_length = 30;
            if($printer_setting->font_size==2) {
              $max_length = 25;
            }
            $yPos = $yPos + 27;
            printer_draw_text($handle, $print_data['order_mode'], 0, $yPos);
            printer_draw_text($handle, ":", 105, $yPos);
            $string = $print_data['order_mode_name'];
            if (strlen($string) > $max_length) {
                $string = wordwrap($string,$max_length,"\n", true);
                $string = explode("\n", $string, 3);
                foreach ($string as $str) { 
                    printer_draw_text($handle, $str, 120, $yPos);
                    $yPos = $yPos + 27;
               }
            }else{
              printer_draw_text($handle, $string, 120, $yPos);
            }
           



            // WRITE CUSTOMER
            if($print_data['customer_data'] != ""){
              $yPos = $yPos + 27;
              printer_draw_text($handle, "Kary.", 0, $yPos);
              printer_draw_text($handle, ":", 105, $yPos);
              printer_draw_text($handle, $print_data['customer_data'], 120, $yPos);
            }
            if($print_data['data_order']->customer_phone!=""){
              $yPos = $yPos + 27;
              printer_draw_text($handle, "Telp", 0, $yPos);
              printer_draw_text($handle, ":", 105, $yPos);
              printer_draw_text($handle, $print_data['data_order']->customer_phone, 120, $yPos);
            }
            if((string)$print_data['data_order']->customer_address!=""){
              $yPos = $yPos + 27;
              printer_draw_text($handle, "Alamat", 0, $yPos);
              printer_draw_text($handle, ":", 105, $yPos);
              $address=$print_data['data_order']->customer_address;
              $count=strlen($address)/25;
              for($x=0;$x<$count;$x++){
                printer_draw_text($handle, substr($address,($x*25),25), 120, $yPos);
                if(($x+1)<$count){
                  $yPos = $yPos + 27;
                }
              }
            }

            // Header
            $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
            printer_select_pen($handle, $pen);

            $yPos = $yPos + 30;
            printer_draw_line($handle, 0, $yPos, ($printer_setting->description == 48 ? 400 : 600), $yPos);

            // body
            foreach ($print_data['order_list'] as $order) {
              $yPos = $yPos + 25;
              $yPos = _write_checkout_bill($handle, $order, $yPos, $var_max_char_length, $printer_setting);
            }

            $yPos = $yPos + 27;
            printer_draw_line($handle, 0, $yPos, ($printer_setting->description == 48 ? 400 : 600), $yPos);

            // footer
            if ($print_data['grand_total'] >= '0') {
                $yPos = $yPos + 27;
                printer_draw_text($handle, 'SUBTOTAL 1', 0, $yPos);
                _write_price($handle, convert_rupiah($print_data['subtotal']), $yPos, ($printer_setting->description == 48 ? 350 : ($printer_setting->description == 72 ? 495 : 550)), $printer_setting);

                if(!empty($print_data['bill_minus'])){
                    $yPos = $yPos + 27;
                    printer_draw_line($handle, 0, $yPos, ($printer_setting->description == 48 ? 400 : 600), $yPos);

                    foreach ($print_data['bill_minus'] as $minus) {
                        $string = trim($minus->info);
                        $max_length = 13;
                        if($printer_setting->font_size==2) {
                          $max_length = 10;
                        }
                        if (strlen($string) > $max_length) {
                            $string = wordwrap($string, $max_length);
                            $string = explode("\n", $string, 3);
                            $count  = 1;
                            foreach ($string as $str) {
                                $yPos = $yPos + 27;
                                printer_draw_text($handle, $str, 0, $yPos);
                                $count++;
                            }
                        }else {
                            $yPos = $yPos + 27;
                            printer_draw_text($handle, $string, 0, $yPos);
                        }
                        _write_price($handle, convert_rupiah($minus->amount), $yPos, ($printer_setting->description == 48 ? 350 : ($printer_setting->description == 72 ? 495 : 550)), $printer_setting);
                    }
                }
                
                if(!empty($print_data['bill_plus'])){

                    $yPos = $yPos + 27;
                    printer_draw_line($handle, 0, $yPos, ($printer_setting->description == 48 ? 400 : 600), $yPos);
                                    

                    if(isset($print_data['bill']->receipt_number)){
                      $temp = array();
                      foreach ($print_data['bill_plus'] as $k => $plus) {
                        if(strtolower($plus->info) == "pembulatan"){
                            $temp = $plus;
                            unset($print_data['bill_plus'][$k]);
                        }
                    }
                    $print_data['bill_plus'][] = $temp;

                    foreach ($print_data['bill_plus'] as $plus) {
                        $yPos = $yPos + 27;
                        printer_draw_text($handle, $plus->info, 0, $yPos);
                        _write_price($handle, convert_rupiah($plus->amount), $yPos, ($printer_setting->description==48 ? 350 : ($printer_setting->description==72 ? 495 : 550)), $printer_setting);
                    }

                    }else{

                      foreach ($print_data['bill_plus'] as $plus) {
                        $yPos = $yPos + 27;
                        printer_draw_text($handle, $plus->info, 0, $yPos);
                        _write_price($handle, convert_rupiah($plus->amount), $yPos, ($printer_setting->description==48 ? 350 : ($printer_setting->description==72 ? 495 : 550)), $printer_setting);
                    }

                        $yPos = $yPos + 27;
                        printer_draw_text($handle, "Pembulatan", 0, $yPos);
                        if($printer_setting->font_size==2) $yPos = $yPos + 27;
                        _write_price($handle, convert_rupiah($print_data['round_total']), $yPos, ($printer_setting->description==48 ? 350 : ($printer_setting->description==72 ? 495 : 550)), $printer_setting);
                    }
                }
                /*if(isset($print_data['reservation']) && sizeof($print_data['reservation'])>0 && $print_data['reservation']->down_payment>0){
                  if($print_data['grand_total']>0 && $print_data['reservation']->down_payment < $print_data['grand_total']){
                    $print_data['grand_total'] += $print_data['reservation']->down_payment;
                  }
                }*/

                $yPos = $yPos + 27;
                printer_draw_line($handle, 0, $yPos, ($printer_setting->description == 48 ? 400 : 600), $yPos);
                
                $yPos = $yPos + 27;
                printer_draw_text($handle, 'GRAND TOTAL', 0, $yPos);
                if($printer_setting->font_size==2) $yPos = $yPos + 27;
                _write_price($handle, convert_rupiah($print_data['grand_total']), $yPos, ($printer_setting->description==48 ? 350 : ($printer_setting->description==72 ? 495 : 550)), $printer_setting);

                if (!empty($print_data['bill_payment']) && $pending === FALSE) {
                    foreach ($print_data['bill_payment'] as $key => $payment) {
                        if($payment->value =="Cash"){
                            $yPos = $yPos + 27;
                            printer_draw_text($handle, $payment->value, 0, $yPos);
                            if($printer_setting->font_size==2) $yPos = $yPos + 27;
                            _write_price($handle, convert_rupiah($print_data['customer_cash_payment']), $yPos, ($printer_setting->description==48 ? 350 : ($printer_setting->description==72 ? 495 : 550)), $printer_setting);
                        }else{
                          $yPos = $yPos + 27;
                          printer_draw_text($handle, $payment->value, 0, $yPos);
                          if($printer_setting->font_size==2) $yPos = $yPos + 27;
                          _write_price($handle, convert_rupiah($payment->amount), $yPos, ($printer_setting->description==48 ? 350 : ($printer_setting->description==72 ? 495 : 380)), $printer_setting);      
                        }

                    }

                    if($print_data['change_due']<0){
                      $yPos = $yPos + 27;
                      printer_draw_text($handle, 'Harus Dibayar', 0, $yPos);
                      if($printer_setting->font_size==2) $yPos = $yPos + 27;
                      _write_price($handle, convert_rupiah((-1*$print_data['change_due'])), $yPos, ($printer_setting->description==48 ? 350 : ($printer_setting->description==72 ? 495 : 550)), $printer_setting);
                    }else{                      
                      $yPos = $yPos + 27;
                      printer_draw_text($handle, 'Kembalian', 0, $yPos);
                      if($printer_setting->font_size==2) $yPos = $yPos + 27;
                      _write_price($handle, convert_rupiah($print_data['change_due']), $yPos, ($printer_setting->description==48 ? 350 : ($printer_setting->description==72 ? 495 : 550)), $printer_setting);
                    }
                }
            }
            
            $yPos = $yPos + 54;
            printer_draw_line($handle, 0, $yPos, ($printer_setting->description==48 ? 400 : 600), $yPos);
            $yPos = $yPos + 27;

            if(sizeof($print_data['footer_bill'])>0){
              $string = trim($print_data['footer_bill']->description);
              $length = ($printer_setting->description == 48 ? 25 : 37);

              if (strlen($string) > $length) {
                $string = wordwrap($string, $length);
                $explode=explode("\n", $string);

                foreach($explode as $e){
                  if($printer_setting->description==48){
                    $e=substr($e, 0, $length);
                  }else{
                    $e=substr($e, 0, $length);
                  }
                  _write_text_center_align($handle, $e, $yPos, ($printer_setting->description==48 ? 125 : ($printer_setting->description==72 ? 160 : 215)), TRUE);
                  $yPos += 27;
                }
              } else {
                _write_text_center_align($handle, $e, $yPos, ($printer_setting->description==48 ? 125 : ($printer_setting->description==72 ? 160 : 215)), TRUE);
                $yPos += 27;
              }
            }else{
              _write_text_center_align($handle, "TERIMA KASIH", $yPos, ($printer_setting->description==48 ? 150 : ($printer_setting->description==72 ? 185 : 240)), TRUE);
            }

            printer_delete_font($font);
      if($is_temporary==FALSE){
        $font2 = printer_create_font("control", 10, 8, PRINTER_FW_BOLD, false, false, false, 0);
        printer_select_font($handle, $font2);
        printer_draw_text($handle,"A",0,0);
        printer_delete_font($font2);
      }
            printer_end_page($handle);
            printer_end_doc($handle);
            printer_close($handle);

        }
    }
}
if (! function_exists('_write_checkout_bill')) {
    function _write_checkout_bill($handle, $order, $yPos, $length,$printer_setting=array())
    {
      $ci=&get_instance(); 
        // $min_80=$ci->config->item("72_80");
        if (is_object($order)) {
            if (! empty($order->product_name)) {
                $string     = $order->product_name;
                $orderCount = $order->product_amount;
                $orderPrice = $order->product_price;
            }
            else {
                $string     = $order['product_name'];
                $orderCount = $order['product_amount'];
                $orderPrice = $order['product_price'] * $orderCount;
            }

        }
        else {
            $string     = $order['product_name'];
            $orderCount = $order['product_amount'];
            $orderPrice = number_format($order['product_price'] * $orderCount, 0, "", ".");
      
      // kalau mau pake RP, pake code dibawah
            //$orderPrice = convert_rupiah($order['product_price'] * $orderCount);
        }

        $string = trim($string);

        if (strlen($string) > $length) {
            $string = wordwrap($string, $length);
            $string = explode("\n", $string, 3);
            $count  = 1;
            foreach ($string as $str) {
                printer_draw_text($handle, $str, 0, $yPos);
                
                $count++;
                $yPos = $yPos + 20;
            }
            printer_draw_text($handle, $orderCount . " x " . number_format($order['product_price'], 0, "", "."), 0, $yPos);
            _write_price($handle, $orderPrice, $yPos, ($printer_setting->description==48 ? 350 : ($printer_setting->description==72 ? 495 : 550)), $printer_setting);
            $yPos = $yPos + 10;
        }else {
            printer_draw_text($handle, $string, 0, $yPos);
            $yPos = $yPos + 25;
            printer_draw_text($handle, $orderCount . " x " . number_format($order['product_price'], 0, "", "."), 0, $yPos);
            _write_price($handle, $orderPrice, $yPos, ($printer_setting->description==48 ? 350 : ($printer_setting->description==72 ? 495 : 550)), $printer_setting);
            $yPos = $yPos + 10;
        }

        if ( isset($order->side_dish_list)) {
            foreach ($order->side_dish_list as $sdh) {
                $yPos = _write_checkout_bill($handle, $sdh, $yPos, $length, $printer_setting);
            }
        }elseif ( isset($order['side_dish_list'])) {
            foreach ($order['side_dish_list'] as $sdh) {
                $yPos = _write_checkout_bill($handle, $sdh, $yPos, $length, $printer_setting);
            }
        }

        return $yPos;
    }
}



if (! function_exists('print_checkout_bill2')) {
    function print_checkout_bill2($printer_name = '', $print_data = array(), $user_data, $pending = false,$is_temporary=TRUE,$printer_setting=array())
    {
      // $min_80=55;
        $GLOBALS['printer_setting']=$printer_setting;
        $printer_name=convert_printer_name($printer_name);
        if (function_exists('printer_open')) {
            // set up
            $var_max_char_length = ($printer_setting->description == 48 ? 25 : 40);
            $yPos                = 0;
            $handle = printer_open($printer_name);
            if($handle==false)return false;
            printer_set_option($handle, PRINTER_MODE, "RAW"); // cut mode
            printer_start_doc($handle,"Start Doc");
            printer_start_page($handle);

            $width=25;
            $height=15;
            
            if($printer_setting->font_size==2){
                $width=29;
                $height=17;
            }
            $font = printer_create_font("Courier New", $width, $height, PRINTER_FW_BOLD, false, false, false, 0);
            printer_select_font($handle, $font);
            if(isset($print_data['setting']['printer_logo']) && $print_data['setting']['printer_logo']!="" && (!empty($printer_setting) && $printer_setting->default == 1) ){
              $path_logo=FCPATH.$print_data['setting']['printer_logo'];
              if(file_exists($path_logo)){
                printer_draw_bmp($handle, $path_logo, ($printer_setting->description == 48 ? 35 : ($printer_setting->description == 72 ? 75 : 130)), 50,300,150);
                $yPos+=200;
              }          
            } 
            _write_text_center($handle,strtoupper($print_data['store_data']->store_name), $yPos, 0, TRUE,$printer_setting->description);
            $yPos+=27;

            if($is_temporary==TRUE){
              if(sizeof($print_data['bill_temporary'])>0){
                  $string = trim($print_data['bill_temporary']->description);
                  $max_length = 25;
                  if($printer_setting->font_size==2) {
                    $max_length = 20;
                  }
                  if (strlen($string) > $max_length) {
                      $string = wordwrap($string, $max_length);
                      $string = explode("\n", $string, 3); 
                      foreach ($string as $str) { 
                          _write_text_center_align($handle,$str, $yPos, ($printer_setting->description==48 ? 125 : ($printer_setting->description==72 ? 160 : 215)), TRUE,$printer_setting->description); 
                          $yPos = $yPos + 25;
                      }
                  }else { 
                      _write_text_center_align($handle,$string, $yPos, ($printer_setting->description==48 ? 125 : ($printer_setting->description==72 ? 160 : 215)), TRUE,$printer_setting->description); 
                  } 
              }else{
                $font = printer_create_font("Courier New", 35, 15, PRINTER_FW_BOLD, false, false, false, 0);
                printer_select_font($handle, $font); 
                _write_text_center_align($handle,"*BILL SEMENTARA*", $yPos,  ($printer_setting->description==48 ? 125 : ($printer_setting->description==72 ? 160 : 215)), TRUE,$printer_setting->description);
                $yPos = $yPos + 35;
              }
            }
            $string = trim($print_data['store_data']->store_address);
    
            if (strlen($string) > 25) {
                $string = wordwrap($string, 25);
                $string = explode("\n", $string, 3);
                $count  = 1;
                foreach ($string as $str) { 
                    _write_text_center($handle,$str, $yPos, 0, TRUE,$printer_setting->description);
                    $count++;
                    $yPos+=27;
                }
            }else {
               
                _write_text_center($handle,$string, $yPos, 0, TRUE,$printer_setting->description);
                $yPos+=27;
            } 
            _write_text_center($handle, $print_data['store_data']->store_phone, $yPos, 0, TRUE,$printer_setting->description);


            if(isset($print_data['bill']->receipt_number)){
                $yPos+=54;
                printer_draw_text($handle, "Bill", 0, $yPos);
                printer_draw_text($handle, ":", 105, $yPos);
                printer_draw_text($handle, $print_data['bill']->receipt_number, 120, $yPos);
            }
            
            //WRITE WAITER NAME
            $yPos+=27;
            printer_draw_text($handle, "Waiter", 0, $yPos);
            printer_draw_text($handle, ":", 105, $yPos);
            printer_draw_text($handle,  $print_data['data_order']->waiter_name, 120, $yPos);

            // WRITE KASIR
            $yPos+=27;
            printer_draw_text($handle, "Kasir", 0, $yPos);
            printer_draw_text($handle, ":", 105, $yPos);
            printer_draw_text($handle,  $user_data->name, 120, $yPos);
            if(isset($print_data['data_order']->counter) && $print_data['data_order']->counter!=0){
              $yPos+=27;
              printer_draw_text($handle, "No", 0, $yPos);
              printer_draw_text($handle, ":", 105, $yPos);
              printer_draw_text($handle, $print_data['data_order']->counter, 120, $yPos);
            }
            // WRITE TANGGAL
            $yPos+=27;
            printer_draw_text($handle, "Tanggal", 0, $yPos);
            printer_draw_text($handle, ":", 105, $yPos);
            printer_draw_text($handle,  (isset($print_data['datetime']) ? date("d/m/Y",strtotime($print_data['datetime'])) : date("d/m/Y")), 120, $yPos);

            //_write_price($handle,"Kasir", $yPos, 350);


            // WRITE JAM
            $yPos+=27;
            printer_draw_text($handle, "Jam", 0, $yPos);
            printer_draw_text($handle, ":", 105, $yPos);
            printer_draw_text($handle,  (isset($print_data['datetime']) ? date("H:i",strtotime($print_data['datetime'])) : date("H:i")), 120, $yPos);

            // WRITE MEJA
            $yPos+=27;
            printer_draw_text($handle, $print_data['order_mode'], 0, $yPos);
            printer_draw_text($handle, ":", 105, $yPos);
            printer_draw_text($handle, $print_data['order_mode_name'], 120, $yPos);


            // WRITE CUSTOMER
            if($print_data['customer_data'] != ""){
              $yPos+=27;
              printer_draw_text($handle, "Kary.", 0, $yPos);
              printer_draw_text($handle, ":", 105, $yPos);
              printer_draw_text($handle, $print_data['customer_data'], 120, $yPos);
            }
            if($print_data['data_order']->customer_phone!=""){
              $yPos+=27;
              printer_draw_text($handle, "Telp", 0, $yPos);
              printer_draw_text($handle, ":", 105, $yPos);
              printer_draw_text($handle, $print_data['data_order']->customer_phone, 120, $yPos);
            }
            if((string)$print_data['data_order']->customer_address!=""){
              $yPos+=27;
              printer_draw_text($handle, "Alamat", 0, $yPos);
              printer_draw_text($handle, ":", 105, $yPos);
              $address=$print_data['data_order']->customer_address;
              $count=strlen($address)/25;
              for($x=0;$x<$count;$x++){
                printer_draw_text($handle, substr($address,($x*25),25), 120, $yPos);
                if(($x+1)<$count){
                  $yPos+=27;
                }
              }
            }

            // Header
            $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
            printer_select_pen($handle, $pen);

            $yPos+=30;
            printer_draw_line($handle, 0, $yPos, ($printer_setting->description == 48 ? 400 : 600), $yPos);
            $yPos+=27;
            printer_draw_text($handle, "QTY", 0, $yPos);
            printer_draw_text($handle, "MENU", ($printer_setting->description == 48 ? 80 : 100), $yPos);
            printer_draw_text($handle,  "HARGA", ($printer_setting->description == 48 ? 250 : 380), $yPos);
            // body
            $yPos+=27;
            foreach ($print_data['order_list'] as $order) {
                $yPos+=45;
                $yPos = _write_checkout_bill2($handle, $order, $yPos, $var_max_char_length, $printer_setting);
            }
            $yPos+=45;
            printer_draw_line($handle, 0, $yPos, ($printer_setting->description == 48 ? 400 : 600), $yPos);

            // footer
            if ($print_data['grand_total'] >= '0') {
                $yPos+=27;
                printer_draw_text($handle, 'SUBTOTAL 1', 0, $yPos);
                _write_price($handle, convert_rupiah($print_data['subtotal']), $yPos, ($printer_setting->description == 48 ? 350 : ($printer_setting->description == 72 ? 495 : 550)), $printer_setting);

                if(!empty($print_data['bill_minus'])){
                    $yPos+=27;
                    printer_draw_line($handle, 0, $yPos, ($printer_setting->description == 48 ? 400 : 600), $yPos);

                    foreach ($print_data['bill_minus'] as $minus) {
                        $string = trim($minus->info);
                        $max_length = 13;
                        if($printer_setting->font_size==2) {
                          $max_length = 10;
                        }
                        if (strlen($string) > $max_length) {
                            $string = wordwrap($string, $max_length);
                            $string = explode("\n", $string, 3);
                            $count  = 1;
                            foreach ($string as $str) {
                                $yPos+=27;
                                printer_draw_text($handle, $str, 0, $yPos);
                                $count++;
                            }
                        }else {
                            $yPos+=27;
                            printer_draw_text($handle, $string, 0, $yPos);
                        }
                        _write_price($handle, convert_rupiah($minus->amount), $yPos, ($printer_setting->description == 48 ? 350 : ($printer_setting->description == 72 ? 495 : 550)), $printer_setting);
                    }

                }
                
                if(!empty($print_data['bill_plus'])){

                    $yPos+=27;
                    printer_draw_line($handle, 0, $yPos, ($printer_setting->description==48 ? 400 : 600), $yPos);
                                    

                    if(isset($print_data['bill']->receipt_number)){
                      $temp = array();
                      foreach ($print_data['bill_plus'] as $k => $plus) {
                        if(strtolower($plus->info) == "pembulatan"){
                            $temp = $plus;
                            unset($print_data['bill_plus'][$k]);
                        }
                    }
                    $print_data['bill_plus'][] = $temp;

                    foreach ($print_data['bill_plus'] as $plus) {
                        $yPos+=27;
                        printer_draw_text($handle, $plus->info, 0, $yPos);
                        _write_price($handle, convert_rupiah($plus->amount), $yPos, ($printer_setting->description == 48 ? 350 : ($printer_setting->description == 72 ? 495 : 550)), $printer_setting);
                    }

                    }else{

                      foreach ($print_data['bill_plus'] as $plus) {
                        $yPos+=27;
                        printer_draw_text($handle, $plus->info, 0, $yPos);
                        _write_price($handle, convert_rupiah($plus->amount), $yPos, ($printer_setting->description == 48 ? 350 : ($printer_setting->description == 72 ? 495 : 550)), $printer_setting);
                    }

                        $yPos+=27;
                        printer_draw_text($handle, "Pembulatan", 0, $yPos);
                        _write_price($handle, convert_rupiah($print_data['round_total']), $yPos, ($printer_setting->description == 48 ? 350 : ($printer_setting->description == 72 ? 495 : 550)), $printer_setting);
                    }                   
                }
                if(isset($print_data['reservation']) && sizeof($print_data['reservation'])>0 && $print_data['reservation']->down_payment>0){
                  if($print_data['grand_total']>0){
                    $print_data['grand_total']+=$print_data['reservation']->down_payment;
                  }
                }

                $yPos+=27;
                printer_draw_line($handle, 0, $yPos, ($printer_setting->description==48 ? 400 : 600), $yPos);
                
                $yPos+=27;
                printer_draw_text($handle, 'GRAND TOTAL', 0, $yPos);
                _write_price($handle, convert_rupiah($print_data['grand_total']), $yPos, ($printer_setting->description == 48 ? 350 : ($printer_setting->description == 72 ? 495 : 550)), $printer_setting);


                if (!empty($print_data['bill_payment']) && $pending === FALSE) {
                    foreach ($print_data['bill_payment'] as $key => $payment) {
                        if($payment->value =="Cash"){
                            $yPos+=27;
                            printer_draw_text($handle, $payment->value, 0, $yPos);
                            _write_price($handle, convert_rupiah($print_data['customer_cash_payment']), $yPos, ($printer_setting->description == 48 ? 350 : ($printer_setting->description == 72 ? 495 : 550)), $printer_setting);
                        }else{
                          $yPos+=27;
                          printer_draw_text($handle, $payment->value, 0, $yPos);
                          _write_price($handle, convert_rupiah($payment->amount), $yPos, ($printer_setting->description == 48 ? 350 : ($printer_setting->description == 72 ? 495 : 550)), $printer_setting);      
                        }

                    }

                    if($print_data['change_due']<0){
                      $yPos+=27;
                      printer_draw_text($handle, 'Harus Dibayar', 0, $yPos);
                      _write_price($handle, convert_rupiah((-1*$print_data['change_due'])), $yPos, ($printer_setting->description == 48 ? 350 : ($printer_setting->description == 72 ? 495 : 380)), $printer_setting);
                    }else{                      
                      $yPos+=27;
                      printer_draw_text($handle, 'Kembalian', 0, $yPos);
                      _write_price($handle, convert_rupiah($print_data['change_due']), $yPos, ($printer_setting->description == 48 ? 350 : ($printer_setting->description == 72 ? 495 : 550)), $printer_setting);
                    }
                }
            }
            
            $yPos+=54;
            printer_draw_line($handle, 0, $yPos, ($printer_setting->description==48 ? 400 : 600), $yPos);
            $yPos+=27;
            _write_text_center_align($handle, "TERIMA KASIH", $yPos, ($printer_setting->description == 48 ? 150 : ($printer_setting->description == 72 ? 185 : 240)), TRUE);
            printer_delete_font($font);
      if($is_temporary==FALSE){
        $font2 = printer_create_font("control", 10, 8, PRINTER_FW_BOLD, false, false, false, 0);
        printer_select_font($handle, $font2);
        printer_draw_text($handle,"A",0,0);
        printer_delete_font($font2);
      }
            printer_end_page($handle);
            printer_end_doc($handle);
            printer_close($handle);

        }
    }
}
if (! function_exists('_write_checkout_bill2')) {
    function _write_checkout_bill2($handle, $order, $yPos, $length, $printer_setting = array())
    {
      // $min_80=55;
        if (is_object($order)) {
            if (! empty($order->product_name)) {
                $string     = $order->product_name;
                $orderCount = $order->product_amount;
                $orderPrice = $order->product_price;
            }
            else {
                $string     = $order['product_name'];
                $orderCount = $order['product_amount'];
                $orderPrice = $order['product_price'] * $orderCount;
            }

        }
        else {
            $string     = $order['product_name'];
            $orderCount = $order['product_amount'];
            $orderPrice = number_format($order['product_price'] * $orderCount, 0, "", "."); 
        }

        $length = 12;
          if (strlen($string) > $length) {
            $string = wordwrap($string, $length);
            $string = explode("\n", $string, 3);
            $count  = 1;
            foreach ($string as $str) {
              if($count == 1){
                printer_draw_text($handle, $orderCount, 0, $yPos);
                printer_draw_text($handle, $str, ($printer_setting->description == 48 ? 50 : 80), $yPos);
                _write_price($handle, $orderPrice, $yPos, ($printer_setting->description == 48 ? 350 : ($printer_setting->description == 72 ? 375 : 430)), $printer_setting);
                // printer_draw_text($handle,  convert_rupiah($orderCount * $order['product_price']), ($printer_setting->description == 48 ? 230 : ($printer_setting->description == 72 ? 375 : ($printer_setting->description == 'generic' ? 285 : 430))), $yPos);
               
              }else{
                $yPos = $yPos + 25;
                printer_draw_text($handle, $str, ($printer_setting->description == 48 ? 50 : 80), $yPos);

              } 
              $count++; 
            }  
        }else{
             printer_draw_text($handle, $orderCount, 0, $yPos);
              printer_draw_text($handle, $string, ($printer_setting->description == 48 ? 50 : 80), $yPos);
              _write_price($handle, $orderPrice, $yPos, ($printer_setting->description == 48 ? 350 : ($printer_setting->description == 72 ? 375 : 430)), $printer_setting);
              // printer_draw_text($handle,  convert_rupiah($orderCount * $order['product_price']), ($printer_setting->description == 48 ? 230 : ($printer_setting->description == 72 ? 375 : ($printer_setting->description == 'generic' ? 285 : 430))), $yPos);
        } 
        if ( isset($order->side_dish_list)) {
            foreach ($order->side_dish_list as $sdh) {
                $yPos = _write_checkout_bill2($handle, $sdh, $yPos, $length, $printer_setting);
            }
        }elseif ( isset($order['side_dish_list'])) {
            foreach ($order['side_dish_list'] as $sdh) {
                $yPos = _write_checkout_bill2($handle, $sdh, $yPos, $length, $printer_setting);
            }
        }
        $yPos += 20;

        return $yPos;
    }
}

if (!function_exists('convert_rupiah')) {
  function convert_rupiah($value){
    return  $value = 'Rp '. number_format($value, 0, "", ".");
  }
}


/******************** PRINT ORDER KITCHEN ***********************/
if (! function_exists('print_order_kitchen_helper')) {
    function print_order_kitchen_helper($printer_name = '', $print_data = '', $user_data, $void = false,$printer_setting=array())
    {
      $ci=&get_instance(); 
      $jml = 0;
        // $min_80=$ci->config->item("72_80");

      $GLOBALS['printer_setting']=$printer_setting;
      $printer_name=convert_printer_name($printer_name);
      if (function_exists('printer_open')) {
          // set up
          $var_max_char_length = ($printer_setting->description==48 ? 10 : 15);
          $yPos                = 0;

          $handle = printer_open($printer_name);
          if($handle==false)return false;
          printer_set_option($handle, PRINTER_MODE, "RAW"); // cut mode
          printer_start_doc($handle,"Start Doc");
          printer_start_page($handle);

          $font = printer_create_font("Courier New", 25, 15, PRINTER_FW_BOLD, false, false, false, 0);
          printer_select_font($handle, $font);
          if(isset($print_data['setting']['printer_logo']) && (!empty($printer_setting) && $printer_setting->default == 1) ){
            $path_logo=FCPATH.$print_data['setting']['printer_logo'];
            if(file_exists($path_logo)){
              printer_draw_bmp($handle, $path_logo, ($printer_setting->description == 48 ? 35 : ($printer_setting->description == 72 ? 75 : 130)), 50,300,150);
              $yPos+=200;
            }          
          }
          $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
          printer_select_pen($handle, $pen);
          $void_text = "";
          if($void){
              $font = printer_create_font("Courier New", 80, 70, PRINTER_FW_BOLD, false, false, false, 0);
              printer_select_font($handle, $font);
              
              $void_text = "VOID";
              _write_text_center_align($handle, $void_text, $yPos, ($printer_setting->description == 48 ? 90 : ($printer_setting->description == 72 ? 107 : 180)), TRUE);
              $yPos = $yPos + 80;
          }

          $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
          printer_select_pen($handle, $pen);
          
          if(!empty($print_data['DPT'])){
            $font = printer_create_font("Courier New", 85, 40, PRINTER_FW_ULTRABOLD, false, false, false, 0);
            printer_select_font($handle, $font);

            _write_text_center_align($handle, $print_data['DPT'], $yPos, ($printer_setting->description == 48 ? 80 : ($printer_setting->description == 72 ? 120 : 190)), TRUE);
            $yPos = $yPos + 80;
          }

          $font = printer_create_font("Courier New", 25, 15, PRINTER_FW_BOLD, false, false, false, 0);
          printer_select_font($handle, $font);

          _write_text_center_align($handle, $print_data['DP'], $yPos, ($printer_setting->description == 48 ? 170 : ($printer_setting->description == 72 ? 205 : 260)), TRUE);

          $font = printer_create_font("Courier New", 20, 10, PRINTER_FW_NORMAL, false, false, false, 0);
          printer_select_font($handle, $font);

          $yPos += 35;
          printer_draw_line($handle, 0, $yPos, ($printer_setting->description == 48 ? 400 : 600), $yPos);
          $yPos += 5;
          printer_draw_line($handle, 0, $yPos, ($printer_setting->description == 48 ? 400 : 600), $yPos);

          // WRITE MEJA
          if(isset($print_data['table_data'])){
            $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
            printer_select_pen($handle, $pen);

            $font2 = printer_create_font("Courier New", 40, 25, PRINTER_FW_ULTRABOLD, false, false, false, 0);
            printer_select_font($handle, $font2);
            $yPos += 30;
            printer_draw_text($handle, "NO Meja", 0, $yPos);
            printer_draw_text($handle,  ":", 180, $yPos);

            $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
            printer_select_pen($handle, $pen);
            
            $font2 = printer_create_font("Courier New", 50, 35, PRINTER_FW_ULTRABOLD, false, false, false, 0);
            printer_select_font($handle, $font2);
            printer_draw_text($handle,  $print_data['table_data']->table_name, 190, $yPos);
          }
          $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
          printer_select_pen($handle, $pen);
          
          $font = printer_create_font("Courier New", 30, 10, PRINTER_FW_NORMAL, false, false, false, 0);
          printer_select_font($handle, $font);
    
          // WRITE KASIR
          $yPos += 45;
          printer_draw_text($handle, "Jam Order", 0, $yPos);
          printer_draw_text($handle, ":", 110, $yPos);
          
          $font = printer_create_font("Courier New", 40, 12, PRINTER_FW_BOLD, false, false, false, 0);
          printer_select_font($handle, $font);
          printer_draw_text($handle,  date("Y/m/d H:i:s"), 125, $yPos);
          
          $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
          printer_select_pen($handle, $pen);
          
          $font = printer_create_font("Courier New", 20, 10, PRINTER_FW_NORMAL, false, false, false, 0);
          printer_select_font($handle, $font);

          $yPos += 30;
          printer_draw_text($handle, "Table Guard", 0, $yPos);
          printer_draw_text($handle, $user_data->name, 125, $yPos);
          
          if(isset($print_data['outlet_data'])){
              $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
              printer_select_pen($handle, $pen);
              
              $font2 = printer_create_font("Courier New", 25, 15, PRINTER_FW_ULTRABOLD, false, false, false, 0);
              printer_select_font($handle, $font2);
              $yPos += 27;
              printer_draw_text($handle, $print_data['outlet_data']." : ", 0,$yPos);

          }
          $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
          printer_select_pen($handle, $pen);
          
          $font = printer_create_font("Courier New", 20, 10, PRINTER_FW_NORMAL, false, false, false, 0);
          printer_select_font($handle, $font);
          if($void){
            $yPos += 20;
            printer_draw_text($handle, "Alasan", 0, $yPos);
            printer_draw_text($handle, ":", 105, $yPos);

            $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
            printer_select_pen($handle, $pen);
            @
            $font2 = printer_create_font("Courier New", 30, 15, PRINTER_FW_ULTRABOLD, false, false, false, 0);
            printer_select_font($handle, $font2);

            printer_draw_text($handle,  $print_data['note'], 120, $yPos);
          }

          // WRITE CUSTOMER
          if(isset($print_data['customer_data'])){

            $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
            printer_select_pen($handle, $pen);
            $font = printer_create_font("Courier New", 25, 10, PRINTER_FW_NORMAL, false, false, false, 0);
            printer_select_font($handle, $font);

            $yPos += 50;
            printer_draw_text($handle, "Nama", 0, $yPos);
            printer_draw_text($handle, ":", 105, $yPos);
            printer_select_pen($handle, $pen);
            
            $font = printer_create_font("Courier New", 25, 10, PRINTER_FW_BOLD, false, false, false, 0);
            printer_select_font($handle, $font);
            printer_draw_text($handle, $print_data['customer_data'], 120, $yPos);
          }

          // Header
          $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
          printer_select_pen($handle, $pen);
          
          $font2 = printer_create_font("Courier New", 40, 27, PRINTER_FW_ULTRABOLD, false, false, false, 0);
          printer_select_font($handle, $font2);

          // body
          foreach ($print_data['order_list'] as $order) {
              $yPos += 30;
              $yPos = _write_order_kitchen($handle, $order, $yPos, $var_max_char_length, 5, $printer_setting);
              $jml += $order->quantity;
              $yPos += 5;
          }

          $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
          printer_select_pen($handle, $pen);

          $font = printer_create_font("Courier New", 20, 10, PRINTER_FW_NORMAL, false, false, false, 0);
          printer_select_font($handle, $font);
          
          $yPos += 30;
          printer_draw_line($handle, 0, $yPos, ($printer_setting->description == 48 ? 400 : 600), $yPos);

          $yPos += 30;
          printer_draw_line($handle, 0, $yPos, ($printer_setting->description == 48 ? 400 : 600), $yPos);
          
          $yPos += 10;
          printer_draw_line($handle, 0, $yPos, ($printer_setting->description == 48 ? 400 : 600), $yPos);

          printer_delete_font($font);
          printer_end_page($handle);
          printer_end_doc($handle);
          printer_close($handle);
      }
    }
}


if (! function_exists('_write_order_kitchen')) {
    function _write_order_kitchen($handle, $order, $yPos, $length, $explode_limit= 4,$printer_setting=array())
    { 
        $ci=&get_instance(); 

        // $min_80=$ci->config->item("72_80");
        if (is_object($order)) {
            if (! empty($order->menu_name)) {
                $string     = $order->menu_name;
                $orderCount = $order->quantity;
            }
            else {
                $string     = $order;
                $orderCount = "";
            }

        }
        else {
            $string     = $order;
            $orderCount = '';
        }


        $string = trim($string);
        
        if (strlen($string) > $length) {
            $string = wordwrap($string, $length);
            $string = explode("\n", $string, $explode_limit);
            $count  = 1;
            foreach ($string as $str) {
                if ($count == 1) {
                    printer_draw_text($handle, $orderCount, 0, $yPos);
                    printer_draw_text($handle, $str, ($printer_setting->description == 48 ? 50 : 80), $yPos);
                }
                else {
                    printer_draw_text($handle, '', 0, $yPos);
                    printer_draw_text($handle, $str, ($printer_setting->description == 48 ? 50 : 80), $yPos);
                }
                $count++;

                $yPos += 30;
            }
        }
        else {
            printer_draw_text($handle, $orderCount, 0, $yPos);
            printer_draw_text($handle, $string,($printer_setting->description == 48 ? 50 : 80), $yPos);
            $yPos += 30;
        }

        if (isset($order->dinein_takeaway)) {
          $yPos = $yPos + 10;
          $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
          printer_select_pen($handle, $pen);

          $font2 = printer_create_font("Courier New", 25, 15, PRINTER_FW_NORMAL, false, false, false, 0);
          printer_select_font($handle, $font2);

          printer_draw_text($handle, "Type  : ", ($printer_setting->description == 48 ? 50 : 80), $yPos);

          $font2 = printer_create_font("Courier New", 30, 20, PRINTER_FW_ULTRABOLD, false, false, false, 0);
          printer_select_font($handle, $font2);

          printer_draw_text($handle, ($order->is_delivery==1 ? 'Delivery' : ($order->is_take_away==1 || $order->dinein_takeaway==1 ? "Takeaway" : 'Dine In' )), 160, $yPos);

          $yPos += 20;
        }
        if (! empty($order->options)) {
            foreach ($order->options as $odr) {
                $yPos += 10;

                $font2 = printer_create_font("Courier New", 25, 15, PRINTER_FW_ULTRABOLD, false, false, false, 0);
                printer_select_font($handle, $font2);

                printer_draw_text($handle, '(' . $odr->option_name .' - '. $odr->option_value_name . ') ', ($printer_setting->description == 48 ? 50 : 80), $yPos);

                $yPos += 30;
            }
        }

        if (! empty($order->side_dishes)) {
            foreach ($order->side_dishes as $sdh) {
                $yPos += 10;
                
                $font2 = printer_create_font("Courier New", 25, 15, PRINTER_FW_ULTRABOLD, false, false, false, 0);
                printer_select_font($handle, $font2);
                $yPos = _write_order_kitchen($handle, '- '.$sdh->name, $yPos, $length, 2, $printer_setting);
            }
        }

        if(!empty($order->note)){
            $yPos += 10;
            $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
            printer_select_pen($handle, $pen);

            $font2 = printer_create_font("Courier New", 35, 20, PRINTER_FW_BOLD, false, false, false, 0);
            printer_select_font($handle, $font2);

            $yPos = _write_order_kitchen($handle, 'Notes: '.$order->note, $yPos, $length, 15, $printer_setting);
        }

        $font2 = printer_create_font("Courier New", 40, 27, PRINTER_FW_ULTRABOLD, false, false, false, 0);
        printer_select_font($handle, $font2);

        return $yPos;
    }
}

if (! function_exists('printer_test_cashier')) {
    function printer_test_cashier($printer_name = "",$printer_setting=array())
    {
      $ci=&get_instance();

      $GLOBALS['printer_setting']=$printer_setting;
      $printer_name=convert_printer_name($printer_name);

      if (function_exists('printer_open')) {

        $var_max_char_length = ($printer_setting->description==48 ? 25 : 40);
        $yPos                = 0;
        $handle = printer_open($printer_name);

        if($handle==false)return false;

        printer_set_option($handle, PRINTER_MODE, "RAW"); 
        printer_start_doc($handle,"Start Doc");
        printer_start_page($handle);
        $font = printer_create_font("Courier New", 30, 20, PRINTER_FW_BOLD, false, false, false, 0);
        printer_select_font($handle, $font);
        
        printer_draw_text($handle, "Printer Test", 0, $yPos);
        $font = printer_create_font("Courier New", 20, 15, PRINTER_FW_BOLD, false, false, false, 0);
        printer_select_font($handle, $font);
        $yPos = $yPos + 50;
        printer_draw_text($handle, "Printer Name : ".$printer_name, 0, $yPos);
        $yPos = $yPos + 27;
        printer_draw_text($handle, date("Y/m/d H:i:s"), 0, $yPos);

        $yPos = $yPos + 27;
        
        $yPos = $yPos + 27;
        
        $yPos = $yPos + 27;
        printer_draw_text($handle, "You have", 0, $yPos);
        $yPos = $yPos + 27;
        printer_draw_text($handle, "correctly", 0, $yPos);
        $yPos = $yPos + 27;
        printer_draw_text($handle, "instaled your", 0, $yPos);
        $yPos = $yPos + 27;
        printer_draw_text($handle, "Printer", 0, $yPos);

        //PRINT TEST PAGE CENTER

         // _write_text_center($handle,"*BILL SEMENTARA*", $yPos, 0, TRUE,$printer_setting->description);
         // $yPos = $yPos + 27;
         // _write_text_center($handle,"Koffie Tijd", $yPos, 0, TRUE,$printer_setting->description);
         // $yPos = $yPos + 27;
         // _write_text_center($handle,"Jl. Citarum No. 30 Bandung", $yPos, 0, TRUE,$printer_setting->description);
         // $yPos = $yPos + 27;
         // _write_text_center($handle,"085100888812", $yPos, 0, TRUE,$printer_setting->description);
      
      // CASH DRAWER
      $font2 = printer_create_font("control", 10, 8, PRINTER_FW_BOLD, false, false, false, 0);
      printer_select_font($handle, $font2);
      printer_draw_text($handle, "A", 0, 0);
      printer_delete_font($font2);
      // END CASH DRAWER
      
      printer_delete_font($font);
      printer_end_page($handle);
      printer_end_doc($handle);
      printer_close($handle);
    }
  }
}

if (! function_exists('print_list_menu')) {
    function print_list_menu($printer_name = "", $print_data = '', $user_data=array(),$printer_setting=array())
    {
      $ci=&get_instance(); 
        // $min_80=$ci->config->item("72_80");
      $GLOBALS['printer_setting']=$printer_setting;
      $printer_name=convert_printer_name($printer_name);
      if (function_exists('printer_open')) {
        // SET UP
        $var_max_char_length = ($printer_setting->description==48 ? 10 : 15);
        $yPos                = 0;
        $handle = printer_open($printer_name);
        if($handle==false)return false;
        printer_set_option($handle, PRINTER_MODE, "RAW"); // cut mode
        printer_start_doc($handle,"Start Doc");
        printer_start_page($handle);
        $font = printer_create_font("Courier New", 25, 15, PRINTER_FW_BOLD, false, false, false, 0);
        printer_select_font($handle, $font);
        if(isset($print_data['setting']['printer_logo']) && (!empty($printer_setting) && $printer_setting->default == 1)){
          $path_logo=FCPATH.$print_data['setting']['printer_logo'];
          if(file_exists($path_logo)){
            printer_draw_bmp($handle, $path_logo, ($printer_setting->description==48 ? 35 : ($printer_setting->description==72 ? 75 : 130)), 50,300,130);
            $yPos+=200;
          }          
        }
        //STORE NAME
        _write_text_center_align($handle,"Checker", $yPos, ($printer_setting->description==48 ? 180 : ($printer_setting->description==72 ? 215 : 270)), TRUE);
        // WRITE MEJA
        $yPos = $yPos + 23;
       if($print_data['order']->table_name!=""){ 
         $yPos = $yPos + 27;
         printer_draw_text($handle, "Meja", 0, $yPos);
         printer_draw_text($handle, ":", 105, $yPos);


          $font2 = printer_create_font("Courier New", 40, 25, PRINTER_FW_ULTRABOLD, false, false, false, 0);
          printer_select_font($handle, $font2);
          printer_draw_text($handle, $print_data['order']->table_name, 120, $yPos);

          $font = printer_create_font("Courier New", 25, 15, PRINTER_FW_BOLD, false, false, false, 0);
          printer_select_font($handle, $font);
       }
      // WRITE WAITER
        $yPos = $yPos + 35;
        printer_draw_text($handle, "Waiter", 0, $yPos);
        printer_draw_text($handle, ":", 105, $yPos);
        printer_draw_text($handle,  $user_data->name, 120, $yPos);
      
      if(isset($print_data['order']->counter) && $print_data['order']->counter!=0){
        $yPos = $yPos + 27;
        printer_draw_text($handle, "No", 0, $yPos);
        printer_draw_text($handle, ":", 105, $yPos);
        printer_draw_text($handle, $print_data['order']->counter, 120, $yPos);
      }
      // WRITE CUSTOMER
      if($print_data['order']->customer_name!=""){
        $yPos = $yPos + 27;
        printer_draw_text($handle, "Nama", 0, $yPos);
        printer_draw_text($handle, ":", 105, $yPos);
        printer_draw_text($handle, $print_data['order']->customer_name, 120, $yPos);
        $yPos = $yPos + 27;
        printer_draw_text($handle, "Telp", 0, $yPos);
        printer_draw_text($handle, ":", 105, $yPos);
        printer_draw_text($handle, $print_data['order']->customer_phone, 120, $yPos);
        $yPos = $yPos + 27;
        printer_draw_text($handle, "Alamat", 0, $yPos);
        printer_draw_text($handle, ":", 105, $yPos);
        
        $address=$print_data['order']->customer_address;
        $count=strlen($address)/25;
        for($x=0;$x<$count;$x++){
          printer_draw_text($handle, substr($address,($x*25),25), 120, $yPos);
          if(($x+1)<$count){
            $yPos = $yPos + 27;
          }
        }
        // $address1=substr($print_data['order']->customer_address,0,28);
        // $address2=substr($print_data['order']->customer_address,28,strlen($print_data['order']->customer_address));
        // printer_draw_text($handle, $address1, 120, $yPos);
        // if($address2!=""){
          // $yPos = $yPos + 27;
          // printer_draw_text($handle, $address2, 120, $yPos); 
        // }
      }

      // Header
      $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
      printer_select_pen($handle, $pen);

      $yPos = $yPos + 50;
      printer_draw_line($handle, 0, $yPos, ($printer_setting->description==48 ? 400 : 600), $yPos);
      $yPos = $yPos + 25;
      printer_draw_text($handle, "QTY", 0, $yPos);
      printer_draw_text($handle, "PESANAN", ($printer_setting->description==48 ? 330 : ($printer_setting->description==72 ? 445 : 500)), $yPos);
      $yPos = $yPos + 30;
      printer_draw_line($handle, 0, $yPos, ($printer_setting->description==48 ? 400 : 600), $yPos);

      // body
      foreach ($print_data['order_lists'] as $order) {
          $yPos = $yPos + 25;
          $font2 = printer_create_font("Courier New", 40, 25, PRINTER_FW_ULTRABOLD, false, false, false, 0);
          printer_select_font($handle, $font2);
          $yPos = _write_order_kitchen($handle, $order, $yPos, $var_max_char_length,4,$printer_setting);
          $yPos = $yPos + 15;
          printer_draw_line($handle, 0, $yPos, ($printer_setting->description==48 ? 400 : 600), $yPos);
      }
      $font = printer_create_font("Courier New", 25, 15, PRINTER_FW_BOLD, false, false, false, 0);
      printer_select_font($handle, $font);
      $yPos = $yPos + 25;
      _write_price($handle, date("Y/m/d H:i:s"), $yPos, ($printer_setting->description==48 ? 370 : ($printer_setting->description==72 ? 365 : 420)), $printer_setting);
      printer_delete_font($font);
      printer_end_page($handle);
      printer_end_doc($handle);
      printer_close($handle);
      // $handle=fopen($printer_name,"w");
      // fwrite($handle,(chr(27).chr(112).chr(0).chr(50).chr(250)));
      // fclose($handle);
    }
  }
}
if (! function_exists('print_number')) {
  function print_number($printer_name = "", $string_number = '',$printer_setting=array())
  {
    $ci=&get_instance(); 
    $printer_name=convert_printer_name($printer_name);
    if (function_exists('printer_open')) {
      $handle = printer_open($printer_name);
      if($handle==false)return false;
      printer_set_option($handle, PRINTER_MODE, "RAW");
      printer_start_doc($handle,"Start Doc");
      printer_start_page($handle);
      $font = printer_create_font("Courier New", 120, 50, PRINTER_FW_BOLD, false, false, false, 0);
      printer_select_font($handle, $font);
      $left=280;
      if($printer_setting->description==72){
        $left-=strlen($string_number)*20;       
      }elseif($printer_setting->description=="72+"){
        $left=330;
        $left-=strlen($string_number)*20;       
      }else{
        $left=130;
        $left-=strlen($string_number)*10;       
      }
      printer_draw_text($handle, $string_number, $left, 0);
      printer_delete_font($font);
      printer_end_page($handle);
      printer_end_doc($handle);
      printer_close($handle);
    }
  }
}

/*
*   Updated by: tri
*   Updated at: 2016-10-06
*   Function: Print Transfer Stock antar Outlet
*/
if (! function_exists('print_transfer_outlet')) {
  function print_transfer_outlet($printer_name='',$print_data=array()){
    $ci=&get_instance();
    $printer_name = convert_printer_name($printer_name);    
    $datetime = strtotime($print_data['detail'][0]->created_at);
    $date = date('d-m-Y', $datetime);
    $time = date('H:i:s', $datetime);
    if (function_exists('printer_open')) {
      // set up
      $yPos = 20;
      $handle = printer_open($printer_name);
      if ($handle == false) return false;
      printer_set_option($handle, PRINTER_MODE, "RAW"); // cut mode
      printer_start_doc($handle,"Start Doc");
      printer_start_page($handle);
      $font = printer_create_font("draft", 12.5, 10, PRINTER_FW_BOLD, false, false, false, 0);
      printer_select_font($handle, $font);
      $max_row=25;
      $max_row_last=7;
      $split=ceil(sizeof($print_data['detail'])/$max_row);
      if($split==0)$split=1;
      $paging=array();
      for ($x=0; $x < $split; $x++) { 
        if (isset($is_change)) {
          $from = (isset($paging[$x-1]['to']) ? $paging[$x-1]['to'] : $paging[$x]['to']);
          $to = $from + $max_row_last;
        }
        if ($from < 0) $from = 0;
        if ($to > sizeof($print_data['detail'])) $to = sizeof($print_data['detail']);
        $paging[$x] = array(
          "from" => $from,
          "to" => $to
        );
        if (($x + 1) == $split && !isset($is_change) && (sizeof($print_data['detail']) - (($split - 1) * $max_row)) > $max_row_last) {
          $paging[$x]['to'] = (($x + 1) * $max_row);
          if ($paging[$x]['to'] > sizeof($print_data['detail']))$paging[$x]['to'] = sizeof($print_data['detail']);
          $split++;
          $is_change=1;
        }
      }

      for ($x=0; $x < $split; $x++) {
        $counter = 1;
        $yPos += 30;
        printer_draw_text($handle, "SURAT JALAN", 450, $yPos);
        $yPos += 20;
        printer_draw_text($handle, "Bagian: ", 200, $yPos);
        printer_draw_text($handle, $print_data['detail'][0]->origin_outlet, 300, $yPos);
        printer_draw_text($handle, "No: ", 600, $yPos);
        $yPos += 12;
        printer_draw_text($handle, "Waktu: ", 200, $yPos);
        printer_draw_text($handle, $time, 300, $yPos);
        printer_draw_text($handle, "Tanggal: ", 600, $yPos);
        printer_draw_text($handle, $date, 700, $yPos);
        $yPos += 20;

        printer_draw_line($handle, 190, $yPos, 840, $yPos);
        $yPos += 5;
        
        printer_draw_line($handle, 190, $yPos-5, 190, $yPos+15);
        printer_draw_text($handle, "No.", 200, $yPos);
        printer_draw_line($handle, 240, $yPos-5, 240, $yPos+15);

        printer_draw_text($handle, "Item", 250, $yPos);
        printer_draw_line($handle, 540, $yPos-5, 540, $yPos+15);

        printer_draw_text($handle, "Quantity", 550, $yPos);
        printer_draw_line($handle, 740, $yPos-5, 740, $yPos+15);

        printer_draw_text($handle, "Satuan", 750, $yPos);
        printer_draw_line($handle, 840, $yPos-5, 840, $yPos+15);
        $yPos+=15;

        printer_draw_line($handle, 190, $yPos, 840, $yPos);
        $yPos += 5;

        foreach ($print_data['detail'] as $data) {
          printer_draw_text($handle, $counter, 200, $yPos);
          printer_draw_text($handle, $data->item_name, 250, $yPos);
          printer_draw_text($handle, $data->quantity, 550, $yPos);
          printer_draw_text($handle, $data->code, 750, $yPos);
          $yPos += 12;
          $counter++;
        }
        $yPos += 5;
        printer_draw_line($handle, 190, $yPos, 840, $yPos);
        $yPos += 20;

        printer_draw_text($handle, "Inventory Staff,", 200, $yPos);
        printer_draw_text($handle, "Penerima,", 700, $yPos);
        $yPos+=60;
        // printer_draw_line($handle, 250, $yPos, 150, $yPos);
        // printer_draw_line($handle, 750, $yPos, 150, $yPos);
        printer_draw_line($handle, 180, $yPos, 360, $yPos);
        printer_draw_line($handle, 680, $yPos, 810, $yPos);
        $yPos+=15;
      }

      printer_delete_font($font);
      printer_end_page($handle);
      printer_end_doc($handle);
      printer_close($handle);
    }
  }
}

/*
*   Set up for generic printer
*/

if (! function_exists('generic_printer_test')) {
  function generic_printer_test($printer_name = "",$printer_setting=array())
  {
    $ci=&get_instance();

    $GLOBALS['printer_setting']=$printer_setting;
    $printer_name=convert_printer_name($printer_name);

    if (function_exists('printer_open')) {

      $var_max_char_length = 28;
      $yPos                = 0;
      $handle = printer_open($printer_name);

      if($handle==false)return false;

      printer_set_option($handle, PRINTER_MODE, "RAW"); 
      printer_start_doc($handle,"Start Doc");
      printer_start_page($handle);
      $font = printer_create_font("Courier New", 21, 14, PRINTER_FW_BOLD, false, false, false, 0);
      printer_select_font($handle, $font);

      printer_draw_text($handle, "Printer Test", 0, $yPos);
      $font = printer_create_font("Courier New", 14, 10, PRINTER_FW_BOLD, false, false, false, 0);
      printer_select_font($handle, $font);
      $yPos = $yPos + 25;
      printer_draw_text($handle, "Printer Name : ".$printer_name, 0, $yPos);
      $yPos = $yPos + 13;
      printer_draw_text($handle, date("Y/m/d H:i:s"), 0, $yPos);

      $yPos = $yPos + 35;
      printer_draw_text($handle, "You have", 0, $yPos);
      $yPos = $yPos + 13;
      printer_draw_text($handle, "correctly", 0, $yPos);
      $yPos = $yPos + 13;
      printer_draw_text($handle, "instaled your", 0, $yPos);
      $yPos = $yPos + 13;
      printer_draw_text($handle, "Printer", 0, $yPos);

      //PRINT TEST PAGE CENTER
      // _write_text_center($handle,"*BILL SEMENTARA*", $yPos, 0, TRUE,$printer_setting->description);
      //  $yPos = $yPos + 27;
      //  _write_text_center($handle,"Koffie Tijd", $yPos, 0, TRUE,$printer_setting->description);
      //  $yPos = $yPos + 27;
      //  _write_text_center($handle,"Jl. Citarum No. 30 Bandung", $yPos, 0, TRUE,$printer_setting->description);
      //  $yPos = $yPos + 27;
      //  _write_text_center($handle,"085100888812", $yPos, 0, TRUE,$printer_setting->description);
    
      printer_delete_font($font);
      printer_end_page($handle);
      printer_end_doc($handle);
      printer_close($handle);
    }
  }
}

if (! function_exists('print_order_kitchen_generic')) {
    function print_order_kitchen_generic($printer_name = '', $print_data = '', $user_data, $void = false,$printer_setting=array())
    {
      $ci=&get_instance(); 
      $jml = 0;

      $GLOBALS['printer_setting']=$printer_setting;
        $printer_name=convert_printer_name($printer_name);
        if (function_exists('printer_open')) {
            // set up
            $var_max_char_length = 20;
            $yPos                = 0;

            $handle = printer_open($printer_name);
            if($handle==false)return false;
            printer_set_option($handle, PRINTER_MODE, "RAW"); // cut mode
            printer_start_doc($handle,"Start Doc");
            printer_start_page($handle);

            $font = printer_create_font("Courier New", 25, 28, PRINTER_FW_BOLD, false, false, false, 0);
            printer_select_font($handle, $font);
            if(isset($print_data['setting']['printer_logo']) && (!empty($printer_setting) && $printer_setting->default == 1) ){
              $path_logo=FCPATH.$print_data['setting']['printer_logo'];
              if(file_exists($path_logo)){
                printer_draw_bmp($handle, $path_logo, 75, 50,300,150);
                $yPos+=200;
              }          
            }
            $pen = printer_create_pen(PRINTER_PEN_SOLID, 1, "000000");
            printer_select_pen($handle, $pen);
            $void_text = "";
            if($void){
                $font = printer_create_font("Courier New", 25, 28, PRINTER_FW_BOLD, false, false, false, 0);
                printer_select_font($handle, $font);
                $void_text = "VOID";

                _write_text_center_align($handle, $void_text, $yPos, 160, TRUE);
                $yPos = $yPos + 20;
            }

            $pen = printer_create_pen(PRINTER_PEN_SOLID, 1, "000000");
            printer_select_pen($handle, $pen);

            if(!empty($print_data['DPT'])){

                $font = printer_create_font("Courier New", 25, 28, PRINTER_FW_BOLD, false, false, false, 0);
                printer_select_font($handle, $font);

                _write_text_center_align($handle, $print_data['DPT'], $yPos, 130, TRUE);
                $yPos = $yPos + 20;
         
            }   

            $font = printer_create_font("Courier New", 10, 10, PRINTER_FW_BOLD, false, false, false, 0);
            printer_select_font($handle, $font);

            _write_text_center_align($handle, $print_data['DP'], $yPos, 205, TRUE);

            $font = printer_create_font("Courier New", 5, 10, PRINTER_FW_THIN, false, false, false, 0);
            printer_select_font($handle, $font);

            $yPos = $yPos + 15;
            printer_draw_line($handle, 0, $yPos, 600, $yPos);
            $yPos = $yPos + 5;
            printer_draw_line($handle, 0, $yPos, 600, $yPos);

            // WRITE MEJA
            if(isset($print_data['table_data'])){
              $pen = printer_create_pen(PRINTER_PEN_SOLID, 1, "000000");
              printer_select_pen($handle, $pen);

              $font2 = printer_create_font("Courier New", 15, 20, PRINTER_FW_BOLD, false, false, false, 0);
              printer_select_font($handle, $font2);
              $yPos = $yPos + 5;
              printer_draw_text($handle, "NO Meja", 0, $yPos);
              printer_draw_text($handle,  ":", 160, $yPos);

              $pen = printer_create_pen(PRINTER_PEN_SOLID, 1, "000000");
              printer_select_pen($handle, $pen);
              $font2 = printer_create_font("Courier New", 20, 20, PRINTER_FW_ULTRABOLD, false, false, false, 0);
              printer_select_font($handle, $font2);
              printer_draw_text($handle,  $print_data['table_data']->table_name, 190, $yPos);
            }

            $pen = printer_create_pen(PRINTER_PEN_SOLID, 1, "000000");
            printer_select_pen($handle, $pen);
            $font = printer_create_font("Courier New", 10, 7, PRINTER_FW_THIN, false, false, false, 0);
            printer_select_font($handle, $font);
      
            // WRITE KASIR
            $yPos = $yPos + 15;
            printer_draw_text($handle, "Jam Order", 0, $yPos);
            printer_draw_text($handle, ":", 80, $yPos);
            $font = printer_create_font("Courier New", 14, 10, PRINTER_FW_ULTRABOLD, false, false, false, 0);
            printer_select_font($handle, $font);
            printer_draw_text($handle,  date("Y/m/d H:i:s"), 100, $yPos);
            
            $pen = printer_create_pen(PRINTER_PEN_SOLID, 1, "000000");
            printer_select_pen($handle, $pen);
            $font = printer_create_font("Courier New", 10, 10, PRINTER_FW_THIN, false, false, false, 0);
            printer_select_font($handle, $font);

            $yPos = $yPos + 12;
            printer_draw_text($handle, "Table Guard", 0, $yPos);
            printer_draw_text($handle, $user_data->name, 120, $yPos);
            
            if(isset($print_data['outlet_data'])){
                $pen = printer_create_pen(PRINTER_PEN_SOLID, 1, "000000");
                printer_select_pen($handle, $pen);
                $font2 = printer_create_font("Courier New", 12, 10, PRINTER_FW_ULTRABOLD, false, false, false, 0);
                printer_select_font($handle, $font2);
                $yPos = $yPos + 10;
                printer_draw_text($handle, $print_data['outlet_data']." : ", 0,$yPos);

            }
            $pen = printer_create_pen(PRINTER_PEN_SOLID, 1, "000000");
            printer_select_pen($handle, $pen);
            $font = printer_create_font("Courier New", 10, 7, PRINTER_FW_THIN, false, false, false, 0);
            printer_select_font($handle, $font);
            if($void){
              $yPos = $yPos + 10;
              printer_draw_text($handle, "Alasan", 0, $yPos);
              printer_draw_text($handle, ":", 80, $yPos);

              $pen = printer_create_pen(PRINTER_PEN_SOLID, 1, "000000");
              printer_select_pen($handle, $pen);
              
              $font2 = printer_create_font("Courier New", 14, 10, PRINTER_FW_BOLD, false, false, false, 0);
              printer_select_font($handle, $font2);

              printer_draw_text($handle,  $print_data['note'], 100, $yPos);
            }
            
            // WRITE CUSTOMER
            if(isset($print_data['customer_data'])){

              $pen = printer_create_pen(PRINTER_PEN_SOLID, 1, "000000");
              printer_select_pen($handle, $pen);
              $font = printer_create_font("Courier New", 10, 7, PRINTER_FW_THIN, false, false, false, 0);
              printer_select_font($handle, $font);

              $yPos = $yPos + 10;
              printer_draw_text($handle, "Nama", 0, $yPos);
              printer_draw_text($handle, ":", 80, $yPos);
              $font = printer_create_font("Courier New", 14, 10, PRINTER_FW_BOLD, false, false, false, 0);
              printer_select_font($handle, $font);
              printer_draw_text($handle, $print_data['customer_data'], 100, $yPos);
            }

            // Header
            $pen = printer_create_pen(PRINTER_PEN_SOLID, 1, "000000");
            printer_select_pen($handle, $pen);
            
            $font2 = printer_create_font("Courier New", 14, 17, PRINTER_FW_BOLD, false, false, false, 0);
            printer_select_font($handle, $font2);

            // body
            foreach ($print_data['order_list'] as $order) {
                $yPos = $yPos + 13;
                $yPos = _write_order_kitchen_generic($handle, $order, $yPos, $var_max_char_length, 5, $printer_setting);
                $jml += $order->quantity;
                $yPos = $yPos + 5;
            }

            $pen = printer_create_pen(PRINTER_PEN_SOLID, 1, "000000");
            printer_select_pen($handle, $pen);
            $font = printer_create_font("Courier New", 5, 10, PRINTER_FW_NORMAL, false, false, false, 0);
            printer_select_font($handle, $font);
            $yPos = $yPos + 10;
            printer_draw_line($handle, 0, $yPos, 600, $yPos);
            $yPos = $yPos + 10;
            printer_draw_line($handle, 0, $yPos, 600, $yPos);
            $yPos = $yPos + 5;
            printer_draw_line($handle, 0, $yPos, 600, $yPos);

            printer_delete_font($font);
            printer_end_page($handle);
            printer_end_doc($handle);
            printer_close($handle);
        }
    }
}

if (! function_exists('_write_order_kitchen_generic')) {
    function _write_order_kitchen_generic($handle, $order, $yPos, $length, $explode_limit= 4,$printer_setting=array())
    { 
        $ci=&get_instance(); 

        // $min_80=$ci->config->item("72_80");
        if (is_object($order)) {
            if (! empty($order->menu_name)) {
                $string     = $order->menu_name;
                $orderCount = $order->quantity;
            }
            else {
                $string     = $order;
                $orderCount = "";
            }

        }
        else {
            $string     = $order;
            $orderCount = '';
        }


        $string = trim($string);
        
        if (strlen($string) > $length) {
            $string = wordwrap($string, $length);
            $string = explode("\n", $string, $explode_limit);
            $count  = 1;
            foreach ($string as $str) {
                if ($count == 1) {
                    printer_draw_text($handle, $orderCount, 0, $yPos);
                    printer_draw_text($handle, $str,50, $yPos);
                }
                else {
                    printer_draw_text($handle, '', 0, $yPos);
                    printer_draw_text($handle, $str,50, $yPos);
                }

                $count++;
                $yPos = $yPos + 12;
            }
        }
        else {
            printer_draw_text($handle, $orderCount, 0, $yPos);
            printer_draw_text($handle, $string,50, $yPos);
            $yPos = $yPos + 10;            
        }

        if (isset($order->dinein_takeaway)) {
          $yPos = $yPos + 2;
          $pen = printer_create_pen(PRINTER_PEN_SOLID, 1, "000000");
          printer_select_pen($handle, $pen);
          $font2 = printer_create_font("Courier New", 11, 14, PRINTER_FW_THIN, false, false, false, 0);
          printer_select_font($handle, $font2);

          printer_draw_text($handle, "Type : ", 50, $yPos);

          $font2 = printer_create_font("Courier New", 13, 16, PRINTER_FW_ULTRABOLD, false, false, false, 0);
          printer_select_font($handle, $font2);

          printer_draw_text($handle, ($order->is_delivery==1 ? 'Delivery' : ($order->is_take_away==1 || $order->dinein_takeaway==1 ? "Takeaway" : 'Dine In' )), 150, $yPos);

          $yPos = $yPos + 5;

         }
        if (! empty($order->options)) {
            foreach ($order->options as $odr) {
                $yPos = $yPos + 5;
                $font2 = printer_create_font("Courier New", 20, 10, PRINTER_FW_BOLD, false, false, false, 0);
                printer_select_font($handle, $font2);

                printer_draw_text($handle, '(' . $odr->option_name .' - '. $odr->option_value_name . ') ', 50, $yPos);

                $yPos = $yPos + 15;
            }
        }

        if (! empty($order->side_dishes)) {
            foreach ($order->side_dishes as $sdh) {
                $yPos = $yPos + 10;
              $font2 = printer_create_font("Courier New", 20, 10, PRINTER_FW_BOLD, false, false, false, 0);
                printer_select_font($handle, $font2);
                $yPos = _write_order_kitchen_generic($handle, '- '.$sdh->name, $yPos, $length,2,$printer_setting);
            }
        }

        if(!empty($order->note)){
            $yPos = $yPos + 10;
            $pen = printer_create_pen(PRINTER_PEN_SOLID, 1, "000000");
            printer_select_pen($handle, $pen);
            $font2 = printer_create_font("Courier New", 13, 16, PRINTER_FW_ULTRABOLD, false, false, false, 0);
            printer_select_font($handle, $font2);

            $yPos = _write_order_kitchen_generic($handle, 'Notes: '.$order->note, $yPos, $length, 20, $printer_setting);
        }
        $font2 = printer_create_font("Courier New", 14, 17, PRINTER_FW_BOLD, false, false, false, 0);
        printer_select_font($handle, $font2);

        return $yPos;
    }
}

if (! function_exists('print_checkout_bill_generic')) {
    function print_checkout_bill_generic($printer_name = '', $print_data = array(), $user_data, $pending = false,$is_temporary=TRUE,$printer_setting=array())
    {
        $ci=&get_instance();
        $GLOBALS['printer_setting']=$printer_setting;
        $printer_name=convert_printer_name($printer_name);
        if (function_exists('printer_open')) {
            // set up
            $var_max_char_length = 30;            
            $yPos                = 0;            
            $handle = printer_open($printer_name);

            if($handle==false)return false;
            printer_set_option($handle, PRINTER_MODE, "RAW"); // cut mode
            printer_start_doc($handle,"Start Doc");
            printer_start_page($handle);
            $width=10;
            $height=10;
            $font = printer_create_font("Courier New", $width, $height, PRINTER_FW_NORMAL, false, false, false, 0);
            printer_select_font($handle, $font);
            if(isset($print_data['setting']['printer_logo']) && $print_data['setting']['printer_logo']!="" && (!empty($printer_setting) && $printer_setting->default == 1) ){
              $path_logo=FCPATH.$print_data['setting']['printer_logo'];
              if(file_exists($path_logo)){
                printer_draw_bmp($handle, $path_logo, 130, 50,300,300);
                $yPos+=370;
              }
            }

            if(sizeof($print_data['header_bill'])>0){
              $font = printer_create_font("Courier New", 25, 12, PRINTER_FW_BOLD, false, false, false, 0);
              printer_select_font($handle, $font);
              $explode=explode("\n",$print_data['header_bill']->description);
              foreach($explode as $e){
                $e=substr($e,0,37);
                _write_text_center_align($handle, $e, $yPos, 200, TRUE);
                $yPos+=15;
              }
            }
            if($is_temporary==TRUE){
              $font = printer_create_font("Courier New", 18, 12, PRINTER_FW_BOLD, false, false, false, 0);
              printer_select_font($handle, $font);
              _write_text_center_align($handle, "*BILL SEMENTARA*", $yPos, 190, TRUE);
              $yPos = $yPos + 15;              
            }

            $font = printer_create_font("Courier New", $width, $height, PRINTER_FW_NORMAL, false, false, false, 0);
            printer_select_font($handle, $font);
            _write_text_center_align($handle, strtoupper($print_data['store_data']->store_name), $yPos, 205, TRUE);
            $yPos = $yPos + 10;
            
            $string = trim($print_data['store_data']->store_address);
            $max_length = 25;
            if (strlen($string) > $max_length) {
                $string = wordwrap($string, $max_length);
                $string = explode("\n", $string, 3);
                $count  = 1;
                foreach ($string as $str) {
                     _write_text_center_align($handle, $str, $yPos, 200);
                    $count++;
                    $yPos = $yPos + 9;
                }
            }else {
                _write_text_center_align($handle, $string, $yPos, 200);
                $yPos = $yPos + 10;
            }
            _write_text_center_align($handle, $print_data['store_data']->store_phone, $yPos, 200);


            if(isset($print_data['bill']->receipt_number)){
                $yPos = $yPos + 15;
                printer_draw_text($handle, "Bill", 0, $yPos);
                printer_draw_text($handle, ":", 85, $yPos);
                printer_draw_text($handle, $print_data['bill']->receipt_number, 100, $yPos);

            }
            
            //WRITE WAITER NAME
            $yPos = $yPos + 10;
            printer_draw_text($handle, "Waiter", 0, $yPos);
            printer_draw_text($handle, ":", 85, $yPos);
            printer_draw_text($handle,  $print_data['data_order']->waiter_name, 100, $yPos);
            // WRITE KASIR
            $yPos = $yPos + 10;
            printer_draw_text($handle, "Kasir", 0, $yPos);
            printer_draw_text($handle, ":", 85, $yPos);
            printer_draw_text($handle,  $user_data->name, 100, $yPos);
            if(isset($print_data['data_order']->counter) && $print_data['data_order']->counter!=0){
              $yPos = $yPos + 10;
              printer_draw_text($handle, "No", 0, $yPos);
              printer_draw_text($handle, ":", 85, $yPos);
              printer_draw_text($handle, $print_data['data_order']->counter, 100, $yPos);
            }
            // WRITE TANGGAL
            $yPos = $yPos + 10;
            printer_draw_text($handle, "Tanggal", 0, $yPos);
            printer_draw_text($handle, ":", 85, $yPos);
            printer_draw_text($handle,  (isset($print_data['datetime']) ? date("d/m/Y",strtotime($print_data['datetime'])) : date("d/m/Y")), 100, $yPos);

            // WRITE JAM
            $yPos = $yPos + 10;
            printer_draw_text($handle, "Jam", 0, $yPos);
            printer_draw_text($handle, ":", 85, $yPos);
            printer_draw_text($handle,  (isset($print_data['datetime']) ? date("H:i",strtotime($print_data['datetime'])) : date("H:i")), 100, $yPos);

            // WRITE MEJA
            $yPos = $yPos + 10;
            printer_draw_text($handle, $print_data['order_mode'], 0, $yPos);
            printer_draw_text($handle, ":", 85, $yPos);
            printer_draw_text($handle, $print_data['order_mode_name'], 100, $yPos);


            // WRITE CUSTOMER
            if($print_data['customer_data'] != ""){
              $yPos = $yPos + 10;
              printer_draw_text($handle, "Kary.", 0, $yPos);
              printer_draw_text($handle, ":", 85, $yPos);
              printer_draw_text($handle, $print_data['customer_data'], 100, $yPos);
            }
            if($print_data['data_order']->customer_phone!=""){
              $yPos = $yPos + 10;
              printer_draw_text($handle, "Telp", 0, $yPos);
              printer_draw_text($handle, ":", 85, $yPos);
              printer_draw_text($handle, $print_data['data_order']->customer_phone, 100, $yPos);
            }
            if((string)$print_data['data_order']->customer_address!=""){
              $yPos = $yPos + 10;
              printer_draw_text($handle, "Alamat", 0, $yPos);
              printer_draw_text($handle, ":", 85, $yPos);
              $address=$print_data['data_order']->customer_address;
              $count=strlen($address)/25;
              for($x=0;$x<$count;$x++){
                printer_draw_text($handle, substr($address,($x*25),25), 100, $yPos);
                if(($x+1)<$count){
                  $yPos = $yPos + 10;
                }
              }
            }

            // Header
            $pen = printer_create_pen(PRINTER_PEN_SOLID, 1, "000000");
            printer_select_pen($handle, $pen);

            $yPos = $yPos + 12;
            printer_draw_line($handle, 0, $yPos, 600, $yPos);

            // body
            foreach ($print_data['order_list'] as $order) {
                $yPos = $yPos + 10;
                $yPos = _write_checkout_bill_generic($handle, $order, $yPos, $var_max_char_length, $printer_setting);
            }
            $yPos = $yPos + 10;
            printer_draw_line($handle, 0, $yPos, 600, $yPos);

            // footer
            if ($print_data['grand_total'] >= '0') {
                $yPos = $yPos + 7;
                printer_draw_text($handle, 'SUBTOTAL 1', 0, $yPos);
                _write_price_generic($handle, convert_rupiah($print_data['subtotal']), $yPos, 380);

                if(!empty($print_data['bill_minus'])){
                    $yPos = $yPos + 10;
                    printer_draw_line($handle, 0, $yPos, 600, $yPos);

                    foreach ($print_data['bill_minus'] as $minus) {
                        $string = trim($minus->info);
                        $max_length = 13;
                        if($printer_setting->font_size==2) {
                          $max_length = 10;
                        }
                        if (strlen($string) > $max_length) {
                            $string = wordwrap($string, $max_length);
                            $string = explode("\n", $string, 3);
                            $count  = 1;
                            foreach ($string as $str) {
                                $yPos = $yPos + 10;
                                printer_draw_text($handle, $str, 0, $yPos);
                                $count++;
                            }
                        }else {
                            $yPos = $yPos + 10;
                            printer_draw_text($handle, $string, 0, $yPos);
                        }
                        _write_price_generic($handle, convert_rupiah($minus->amount), $yPos, 380);
                    }
                }
                
                if(!empty($print_data['bill_plus'])){

                    $yPos = $yPos + 10;
                    printer_draw_line($handle, 0, $yPos, 600, $yPos);                                    

                    if(isset($print_data['bill']->receipt_number)){
                      $temp = array();
                      foreach ($print_data['bill_plus'] as $k => $plus) {
                        if(strtolower($plus->info) == "pembulatan"){
                            $temp = $plus;
                            unset($print_data['bill_plus'][$k]);
                        }
                      }
                      $print_data['bill_plus'][] = $temp;

                      foreach ($print_data['bill_plus'] as $plus) {
                          $yPos = $yPos + 10;
                          printer_draw_text($handle, $plus->info, 0, $yPos);
                          _write_price_generic($handle, convert_rupiah($plus->amount), $yPos, 380);
                      }
                    }else{
                      foreach ($print_data['bill_plus'] as $plus) {
                        $yPos = $yPos + 10;
                        printer_draw_text($handle, $plus->info, 0, $yPos);
                        _write_price_generic($handle, convert_rupiah($plus->amount), $yPos, 380);
                    }
                       $yPos = $yPos + 10;
                       printer_draw_text($handle, "Pembulatan", 0, $yPos);                         
                       _write_price_generic($handle, convert_rupiah($print_data['round_total']), $yPos, 380);
                    }                   
                }
                if(isset($print_data['reservation']) && sizeof($print_data['reservation'])>0 && $print_data['reservation']->down_payment>0){
                  if($print_data['grand_total']>0){
                    $print_data['grand_total']+=$print_data['reservation']->down_payment;
                  }
                }

                $yPos = $yPos + 12;
                printer_draw_line($handle, 0, $yPos, 600, $yPos);
                
                $yPos = $yPos + 8;
                printer_draw_text($handle, 'GRAND TOTAL', 0, $yPos);
                _write_price_generic($handle, convert_rupiah($print_data['grand_total']), $yPos, 380);


                if (!empty($print_data['bill_payment']) && $pending === FALSE) {
                    foreach ($print_data['bill_payment'] as $key => $payment) {
                        if($payment->value =="Cash"){
                            $yPos = $yPos + 10;
                            printer_draw_text($handle, $payment->value, 0, $yPos);
                            _write_price_generic($handle, convert_rupiah($print_data['customer_cash_payment']), $yPos, 380);
                        }else{
                          $yPos = $yPos + 10;
                          printer_draw_text($handle, $payment->value, 0, $yPos);
                          _write_price_generic($handle, convert_rupiah($payment->amount), $yPos, 380);      
                        }
                    }

                    if($print_data['change_due']<0){
                      $yPos = $yPos + 10;
                      printer_draw_text($handle, 'Harus Dibayar', 0, $yPos);
                      _write_price_generic($handle, convert_rupiah((-1*$print_data['change_due'])), $yPos, 380);
                    }else{                      
                      $yPos = $yPos + 10;
                      printer_draw_text($handle, 'Kembalian', 0, $yPos);
                      _write_price_generic($handle, convert_rupiah($print_data['change_due']), $yPos, 380);
                    }
                }
            }
            
            $yPos = $yPos + 12;
            printer_draw_line($handle, 0, $yPos, 600, $yPos);
            $yPos = $yPos + 8;

            if(sizeof($print_data['footer_bill'])>0){
              $explode=explode("\n",$print_data['footer_bill']->description);
              foreach($explode as $e){
                $e=substr($e,0,37);
                _write_text_center_align($handle, $e, $yPos, 200, TRUE);
                $yPos+=27;
              }
            }else{
              _write_text_center_align($handle, "TERIMA KASIH", $yPos, 200, TRUE);
            }

            printer_delete_font($font);
      if($is_temporary==FALSE){
        $font2 = printer_create_font("control", 10, 8, PRINTER_FW_BOLD, false, false, false, 0);
        printer_select_font($handle, $font2);
        printer_draw_text($handle,"A",0,0);
        printer_delete_font($font2);
      }
            printer_end_page($handle);
            printer_end_doc($handle);
            printer_close($handle);

        }
    }
}

if (! function_exists('_write_checkout_bill_generic')) {
    function _write_checkout_bill_generic($handle, $order, $yPos, $length,$printer_setting=array())
    {
      $ci=&get_instance(); 
        // $min_80=$ci->config->item("72_80");
        if (is_object($order)) {
            if (! empty($order->product_name)) {
                $string     = $order->product_name;
                $orderCount = $order->product_amount;
                $orderPrice = $order->product_price;
            }
            else {
                $string     = $order['product_name'];
                $orderCount = $order['product_amount'];
                $orderPrice = $order['product_price'] * $orderCount;
            }

        }
        else {
            $string     = $order['product_name'];
            $orderCount = $order['product_amount'];
            $orderPrice = number_format($order['product_price'] * $orderCount, 0, "", ".");
      
            // kalau mau pake RP, pake code dibawah
            //$orderPrice = convert_rupiah($order['product_price'] * $orderCount);
        }

        $string = trim($string);

        if (strlen($string) > $length) {
            $string = wordwrap($string, $length);
            $string = explode("\n", $string, 3);
            $count  = 1;
            foreach ($string as $str) {
                printer_draw_text($handle, $str, 0, $yPos);                
                $count++;
                $yPos = $yPos + 10;
            }
            printer_draw_text($handle, $orderCount . " X " . number_format($order['product_price'], 0, "", "."), 0, $yPos);
            _write_price_generic($handle, $orderPrice, $yPos, 380);
            $yPos = $yPos + 7;
        }else {
            printer_draw_text($handle, $string, 0, $yPos);
            $yPos = $yPos + 10;
            printer_draw_text($handle, $orderCount . " X " . number_format($order['product_price'], 0, "", "."), 0, $yPos);
            _write_price_generic($handle, $orderPrice, $yPos, 380);
            $yPos = $yPos + 7;
        }

        if ( isset($order->side_dish_list)) {
            foreach ($order->side_dish_list as $sdh) {
                $yPos = _write_checkout_bill_generic($handle, $sdh, $yPos, $length, $printer_setting);
            }
        }elseif ( isset($order['side_dish_list'])) {
            foreach ($order['side_dish_list'] as $sdh) {
                $yPos = _write_checkout_bill_generic($handle, $sdh, $yPos, $length, $printer_setting);
            }
        }

        return $yPos;
    }
}

if (! function_exists('print_checkout_bill2_generic')) {
    function print_checkout_bill2_generic($printer_name = '', $print_data = array(), $user_data, $pending = false,$is_temporary=TRUE,$printer_setting=array())
    {
      // $min_80=55;
        $GLOBALS['printer_setting']=$printer_setting;
        $printer_name=convert_printer_name($printer_name);
        if (function_exists('printer_open')) {
            // set up
            $var_max_char_length = 30;
            $yPos                = 0;
            $handle = printer_open($printer_name);
            if($handle==false)return false;
            printer_set_option($handle, PRINTER_MODE, "RAW"); // cut mode
            printer_start_doc($handle,"Start Doc");
            printer_start_page($handle);

            $width=10;
            $height=10;
            
            if($printer_setting->font_size==2){
                $width=12;
                $height=12;
            }
            $font = printer_create_font("Courier New", $width, $height, PRINTER_FW_BOLD, false, false, false, 0);
            printer_select_font($handle, $font);
            if(isset($print_data['setting']['printer_logo']) && $print_data['setting']['printer_logo']!="" && (!empty($printer_setting) && $printer_setting->default == 1) ){
              $path_logo=FCPATH.$print_data['setting']['printer_logo'];
              if(file_exists($path_logo)){
                printer_draw_bmp($handle, $path_logo, 50, 50,300,150);
                $yPos+=50;
              }          
            }
            _write_text_center_align($handle, strtoupper($print_data['store_data']->store_name), $yPos, 200, TRUE);
            
            $yPos+=12;

            if($is_temporary==TRUE){
              _write_text_center_align($handle, "*BILL SEMENTARA*", $yPos, 200, TRUE);
              $yPos+=12;
            }
            $string = trim($print_data['store_data']->store_address);
    
            if (strlen($string) > 25) {
                $string = wordwrap($string, 25);
                $string = explode("\n", $string, 3);
                $count  = 1;
                foreach ($string as $str) {
                     _write_text_center_align($handle, $str, $yPos, 195);
                    $count++;
                    $yPos+=12;
                }
            }else {
                _write_text_center_align($handle, $string, $yPos, 195);
                $yPos+=12;
            }
            _write_text_center_align($handle, $print_data['store_data']->store_phone, $yPos, 200);


            if(isset($print_data['bill']->receipt_number)){
                $yPos+=20;
                printer_draw_text($handle, "Bill", 0, $yPos);
                printer_draw_text($handle, ":", 85, $yPos);
                printer_draw_text($handle, $print_data['bill']->receipt_number, 100, $yPos);
            }
            
            //WRITE WAITER NAME
            $yPos+=12;
            printer_draw_text($handle, "Waiter", 0, $yPos);
            printer_draw_text($handle, ":", 85, $yPos);
            printer_draw_text($handle,  $print_data['data_order']->waiter_name, 100, $yPos);

            // WRITE KASIR
            $yPos+=12;
            printer_draw_text($handle, "Kasir", 0, $yPos);
            printer_draw_text($handle, ":", 85, $yPos);
            printer_draw_text($handle,  $user_data->name, 100, $yPos);
            if(isset($print_data['data_order']->counter) && $print_data['data_order']->counter!=0){
              $yPos+=12;
              printer_draw_text($handle, "No", 0, $yPos);
              printer_draw_text($handle, ":", 85, $yPos);
              printer_draw_text($handle, $print_data['data_order']->counter, 100, $yPos);
            }
            // WRITE TANGGAL
            $yPos+=12;
            printer_draw_text($handle, "Tanggal", 0, $yPos);
            printer_draw_text($handle, ":", 85, $yPos);
            printer_draw_text($handle,  (isset($print_data['datetime']) ? date("d/m/Y",strtotime($print_data['datetime'])) : date("d/m/Y")), 100, $yPos);

            //_write_price($handle,"Kasir", $yPos, 350);


            // WRITE JAM
            $yPos+=12;
            printer_draw_text($handle, "Jam", 0, $yPos);
            printer_draw_text($handle, ":", 85, $yPos);
            printer_draw_text($handle,  (isset($print_data['datetime']) ? date("H:i",strtotime($print_data['datetime'])) : date("H:i")), 100, $yPos);

            // WRITE MEJA
            $yPos+=12;
            printer_draw_text($handle, $print_data['order_mode'], 0, $yPos);
            printer_draw_text($handle, ":", 85, $yPos);
            printer_draw_text($handle, $print_data['order_mode_name'], 100, $yPos);


            // WRITE CUSTOMER
            if($print_data['customer_data'] != ""){
              $yPos+=12;
              printer_draw_text($handle, "Kary.", 0, $yPos);
              printer_draw_text($handle, ":", 85, $yPos);
              printer_draw_text($handle, $print_data['customer_data'], 100, $yPos);
            }
            if($print_data['data_order']->customer_phone!=""){
              $yPos+=12;
              printer_draw_text($handle, "Telp", 0, $yPos);
              printer_draw_text($handle, ":", 85, $yPos);
              printer_draw_text($handle, $print_data['data_order']->customer_phone, 100, $yPos);
            }
            if((string)$print_data['data_order']->customer_address!=""){
              $yPos+=12;
              printer_draw_text($handle, "Alamat", 0, $yPos);
              printer_draw_text($handle, ":", 85, $yPos);
              $address=$print_data['data_order']->customer_address;
              $count=strlen($address)/25;
              for($x=0;$x<$count;$x++){
                printer_draw_text($handle, substr($address,($x*25),25), 100, $yPos);
                if(($x+1)<$count){
                  $yPos+=12;
                }
              }
            }

            // Header
            $pen = printer_create_pen(PRINTER_PEN_SOLID, 1, "000000");
            printer_select_pen($handle, $pen);

            $yPos+=15;
            printer_draw_line($handle, 0, $yPos, 600, $yPos);
            $yPos+=12;
            printer_draw_text($handle, "QTY", 0, $yPos);
            printer_draw_text($handle, "MENU", 65, $yPos);
            printer_draw_text($handle,  "HARGA", 285, $yPos);
            // body
            $yPos+=12;
            foreach ($print_data['order_list'] as $order) {
                $yPos+=15;
                $yPos = _write_checkout_bill2_generic($handle, $order, $yPos, $var_max_char_length, $printer_setting);
            }
            $yPos+=12;
            printer_draw_line($handle, 0, $yPos, 600, $yPos);

            // footer
            if ($print_data['grand_total'] >= '0') {
                $yPos+=10;
                printer_draw_text($handle, 'SUBTOTAL 1', 0, $yPos);
                _write_price_generic($handle, convert_rupiah($print_data['subtotal']), $yPos, 380, $printer_setting);

                if(!empty($print_data['bill_minus'])){
                    $yPos+=10;
                    printer_draw_line($handle, 0, $yPos, 600, $yPos);

                    foreach ($print_data['bill_minus'] as $minus) {
                        $string = trim($minus->info);
                        $max_length = 13;
                        if($printer_setting->font_size==2) {
                          $max_length = 10;
                        }
                        if (strlen($string) > $max_length) {
                            $string = wordwrap($string, $max_length);
                            $string = explode("\n", $string, 3);
                            $count  = 1;
                            foreach ($string as $str) {
                                $yPos+=10;
                                printer_draw_text($handle, $str, 0, $yPos);
                                $count++;
                            }
                        }else {
                            $yPos+=10;
                            printer_draw_text($handle, $string, 0, $yPos);
                        }
                        _write_price_generic($handle, convert_rupiah($minus->amount), $yPos, 380, $printer_setting);
                    }

                }
                
                if(!empty($print_data['bill_plus'])){

                    $yPos+=10;
                    printer_draw_line($handle, 0, $yPos, 600, $yPos);
                                    

                    if(isset($print_data['bill']->receipt_number)){
                      $temp = array();
                      foreach ($print_data['bill_plus'] as $k => $plus) {
                        if(strtolower($plus->info) == "pembulatan"){
                            $temp = $plus;
                            unset($print_data['bill_plus'][$k]);
                        }
                    }
                    $print_data['bill_plus'][] = $temp;

                    foreach ($print_data['bill_plus'] as $plus) {
                        $yPos+=10;
                        printer_draw_text($handle, $plus->info, 0, $yPos);
                        _write_price_generic($handle, convert_rupiah($plus->amount), $yPos, 380, $printer_setting);
                    }

                    }else{

                      foreach ($print_data['bill_plus'] as $plus) {
                        $yPos+=10;
                        printer_draw_text($handle, $plus->info, 0, $yPos);
                        _write_price_generic($handle, convert_rupiah($plus->amount), $yPos, 380, $printer_setting);
                    }

                        $yPos+=10;
                        printer_draw_text($handle, "Pembulatan", 0, $yPos);
                        _write_price_generic($handle, convert_rupiah($print_data['round_total']), $yPos, 380, $printer_setting);
                    }                   
                }
                if(isset($print_data['reservation']) && sizeof($print_data['reservation'])>0 && $print_data['reservation']->down_payment>0){
                  if($print_data['grand_total']>0){
                    $print_data['grand_total']+=$print_data['reservation']->down_payment;
                  }
                }

                $yPos+=12;
                printer_draw_line($handle, 0, $yPos, 600, $yPos);
                
                $yPos+=10;
                printer_draw_text($handle, 'GRAND TOTAL', 0, $yPos);
                _write_price_generic($handle, convert_rupiah($print_data['grand_total']), $yPos, 380, $printer_setting);


                if (!empty($print_data['bill_payment']) && $pending === FALSE) {
                    foreach ($print_data['bill_payment'] as $key => $payment) {
                        if($payment->value =="Cash"){
                            $yPos+=10;
                            printer_draw_text($handle, $payment->value, 0, $yPos);
                            _write_price_generic($handle, convert_rupiah($print_data['customer_cash_payment']), $yPos, 380, $printer_setting);
                        }else{
                          $yPos+=10;
                          printer_draw_text($handle, $payment->value, 0, $yPos);
                          _write_price_generic($handle, convert_rupiah($payment->amount), $yPos, 380, $printer_setting);      
                        }

                    }

                    if($print_data['change_due']<0){
                      $yPos+=10;
                      printer_draw_text($handle, 'Harus Dibayar', 0, $yPos);
                      _write_price_generic($handle, convert_rupiah((-1*$print_data['change_due'])), $yPos, 380, $printer_setting);
                    }else{                      
                      $yPos+=10;
                      printer_draw_text($handle, 'Kembalian', 0, $yPos);
                      _write_price($handle, convert_rupiah($print_data['change_due']), $yPos, 380, $printer_setting);
                    }
                }
            }
            
            $yPos+=15;
            printer_draw_line($handle, 0, $yPos, 600, $yPos);
            $yPos+=10;
            _write_text_center_align($handle, "TERIMA KASIH", $yPos, 200, TRUE);
            printer_delete_font($font);
      if($is_temporary==FALSE){
        $font2 = printer_create_font("control", 10, 8, PRINTER_FW_BOLD, false, false, false, 0);
        printer_select_font($handle, $font2);
        printer_draw_text($handle,"A",0,0);
        printer_delete_font($font2);
      }
            printer_end_page($handle);
            printer_end_doc($handle);
            printer_close($handle);

        }
    }
}

if (! function_exists('_write_checkout_bill2_generic')) {
    function _write_checkout_bill2_generic($handle, $order, $yPos, $length, $printer_setting = array())
    {
      // $min_80=55;
        if (is_object($order)) {
            if (! empty($order->product_name)) {
                $string     = $order->product_name;
                $orderCount = $order->product_amount;
                $orderPrice = $order->product_price;
            }
            else {
                $string     = $order['product_name'];
                $orderCount = $order['product_amount'];
                $orderPrice = $order['product_price'] * $orderCount;
            }

        }
        else {
            $string     = $order['product_name'];
            $orderCount = $order['product_amount'];
            $orderPrice = number_format($order['product_price'] * $orderCount, 0, "", "."); 
        }

        $length = 20;
          if (strlen($string) > $length) {
            $string = wordwrap($string, $length);
            $string = explode("\n", $string, 3);
            $count  = 1;
            foreach ($string as $str) {
              if($count == 1){
                printer_draw_text($handle, $orderCount, 0, $yPos);
                printer_draw_text($handle, $str, 65, $yPos);
                _write_price_generic($handle, $orderPrice, $yPos, 380, $printer_setting);
               
              }else{
                $yPos += 10;
                printer_draw_text($handle, $str, 65, $yPos);

              } 
              $count++; 
            }  
        }else{
             printer_draw_text($handle, $orderCount, 0, $yPos);
              printer_draw_text($handle, $string, 65, $yPos);
              _write_price_generic($handle, $orderPrice, $yPos, 380, $printer_setting);
        } 
        if ( isset($order->side_dish_list)) {
            foreach ($order->side_dish_list as $sdh) {
                $yPos = _write_checkout_bill2_generic($handle, $sdh, $yPos, $length, $printer_setting);
            }
        }elseif ( isset($order['side_dish_list'])) {
            foreach ($order['side_dish_list'] as $sdh) {
                $yPos = _write_checkout_bill2_generic($handle, $sdh, $yPos, $length, $printer_setting);
            }
        }
        $yPos += 8;

        return $yPos;
    }
}

if (! function_exists('_write_price_generic')) {
    function _write_price_generic($printer, $input, $yPos, $xPos)
    {
        $s = strlen($input);
        $y = $xPos;
        for ($i = 1; $i < $s + 1; $i++) {
            $u = $i * -1;
            printer_draw_text($printer, substr($input, $u, 1), $y, $yPos);
            $y = $y - 10;
        }
    }
}

if (! function_exists('print_list_menu_generic')) {
    function print_list_menu_generic($printer_name = "", $print_data = '', $user_data=array(),$printer_setting=array())
    {
      $ci=&get_instance(); 

      $GLOBALS['printer_setting']=$printer_setting;
      $printer_name=convert_printer_name($printer_name);

      if (function_exists('printer_open')) {
        // SET UP
        $var_max_char_length = 20;
        $yPos                = 0;
        $handle = printer_open($printer_name);
        if($handle==false)return false;
        printer_set_option($handle, PRINTER_MODE, "RAW"); // cut mode
        printer_start_doc($handle,"Start Doc");
        printer_start_page($handle);
        $font = printer_create_font("Courier New", 25, 28, PRINTER_FW_BOLD, false, false, false, 0);
        printer_select_font($handle, $font);
        if(isset($print_data['setting']['printer_logo']) && (!empty($printer_setting) && $printer_setting->default == 1)){
          $path_logo=FCPATH.$print_data['setting']['printer_logo'];
          if(file_exists($path_logo)){
            printer_draw_bmp($handle, $path_logo, 75, 50,300,130);
            $yPos+=200;
          }          
        }

        //STORE NAME
        $font = printer_create_font("Courier New", 16, 16, PRINTER_FW_BOLD, false, false, false, 0);
        printer_select_font($handle, $font);
        _write_text_center_align($handle,"Checker", $yPos, 200, TRUE);

        // WRITE MEJA
        if($print_data['order']->table_name!=""){ 
          $yPos = $yPos + 15;
          $font = printer_create_font("Courier New", 10, 10, PRINTER_FW_NORMAL, false, false, false, 0);
          printer_select_font($handle, $font);
          printer_draw_text($handle, "Meja", 0, $yPos);
          printer_draw_text($handle, ":", 80, $yPos);


          $font2 = printer_create_font("Courier New", 16, 16, PRINTER_FW_ULTRABOLD, false, false, false, 0);
          printer_select_font($handle, $font2);
          printer_draw_text($handle, $print_data['order']->table_name, 100, $yPos);

          $font = printer_create_font("Courier New", 10, 10, PRINTER_FW_NORMAL, false, false, false, 0);
          printer_select_font($handle, $font);
        }
        // WRITE WAITER
        $yPos = $yPos + 12;
        printer_draw_text($handle, "Waiter", 0, $yPos);
        printer_draw_text($handle, ":", 80, $yPos);
        printer_draw_text($handle,  $user_data->name, 100, $yPos);
      
      if(isset($print_data['order']->counter) && $print_data['order']->counter!=0){
        $yPos = $yPos + 10;
        printer_draw_text($handle, "No", 0, $yPos);
        printer_draw_text($handle, ":", 80, $yPos);
        printer_draw_text($handle, $print_data['order']->counter, 100, $yPos);
      }

      // WRITE CUSTOMER
      if($print_data['order']->customer_name!=""){
        $yPos = $yPos + 10;
        printer_draw_text($handle, "Nama", 0, $yPos);
        printer_draw_text($handle, ":", 80, $yPos);
        printer_draw_text($handle, $print_data['order']->customer_name, 100, $yPos);
        $yPos = $yPos + 10;
        printer_draw_text($handle, "Telp", 0, $yPos);
        printer_draw_text($handle, ":", 80, $yPos);
        printer_draw_text($handle, $print_data['order']->customer_phone, 100, $yPos);
        $yPos = $yPos + 10;
        printer_draw_text($handle, "Alamat", 0, $yPos);
        printer_draw_text($handle, ":", 80, $yPos);
        
        $address=$print_data['order']->customer_address;
        $count=strlen($address)/25;
        for($x=0;$x<$count;$x++){
          printer_draw_text($handle, substr($address,($x*25),25), 100, $yPos);
          if(($x+1)<$count){
            $yPos = $yPos + 10;
          }
        }
      }

      // Header
      $pen = printer_create_pen(PRINTER_PEN_SOLID, 1, "000000");
      printer_select_pen($handle, $pen);

      $yPos = $yPos + 18;
      printer_draw_line($handle, 0, $yPos, 600, $yPos);
      $yPos = $yPos + 10;
      printer_draw_text($handle, "QTY", 0, $yPos);
      printer_draw_text($handle, "PESANAN", 300, $yPos);
      $yPos = $yPos + 12;
      printer_draw_line($handle, 0, $yPos, 600, $yPos);

      // body
      foreach ($print_data['order_lists'] as $order) {
          $yPos = $yPos + 15;
          $font2 = printer_create_font("Courier New", 14, 17, PRINTER_FW_BOLD, false, false, false, 0);
          printer_select_font($handle, $font2);
          $yPos = _write_order_kitchen_generic($handle, $order, $yPos, $var_max_char_length, 5, $printer_setting);
          $yPos = $yPos + 5;
      }

      $yPos = $yPos + 12;
      printer_draw_line($handle, 0, $yPos, 600, $yPos);
      $font = printer_create_font("Courier New", 10, 10, PRINTER_FW_NORMAL, false, false, false, 0);
      printer_select_font($handle, $font);
      $yPos = $yPos + 10;
      _write_price_generic($handle, date("Y/m/d H:i:s"), $yPos, 350);
      printer_delete_font($font);
      printer_end_page($handle);
      printer_end_doc($handle);
      printer_close($handle);
    }
  }
}

function print_open_close_bill_mode_generic($printer_name = '', $print_data = '', $user_data,$printer_setting=array()){
  $GLOBALS['printer_setting']=$printer_setting;
  $printer_name=convert_printer_name($printer_name);
  if (function_exists('printer_open')) {
      $ci=&get_instance(); 
        // $min_80=$ci->config->item("72_80");
        // set up
        $var_max_char_length = 25;
        $yPos                = 0;

        $handle = printer_open($printer_name);
        if($handle==false)return false;
        printer_set_option($handle, PRINTER_MODE, "RAW"); // cut mode
        printer_start_doc($handle,"Start Doc");
        printer_start_page($handle);

        $font = printer_create_font("Courier New", 10, 8, PRINTER_FW_BOLD, false, false, false, 0);
        
        printer_select_font($handle, $font);

        //////// Header          
        printer_draw_text($handle, "TANGGAL OPEN", 0, $yPos);
        printer_draw_text($handle, ": ".date("d/m/Y",strtotime($print_data['oc_cashier']->open_at)), 120, $yPos);
        $yPos += 10;
        
        printer_draw_text($handle, "JAM OPEN", 0, $yPos);
        printer_draw_text($handle, ": ".date("H:i:s",strtotime($print_data['oc_cashier']->open_at)), 120, $yPos);        
        $yPos += 10;

        printer_draw_text($handle, "WAKTU CLOSE", 0, $yPos);
        printer_draw_text($handle, ": ".date("d/m/Y",strtotime($print_data['oc_cashier']->close_at)), 120, $yPos);        
        $yPos += 10;

        printer_draw_text($handle, "JAM CLOSE", 0, $yPos);
        printer_draw_text($handle, ": ".date("H:i:s",strtotime($print_data['oc_cashier']->close_at)), 120, $yPos);        
        $yPos += 10;

        printer_draw_text($handle, "OPEN BY", 0, $yPos);
        printer_draw_text($handle, ": ".$print_data['oc_cashier']->open_by_name, 120, $yPos);
        $yPos += 10;

        printer_draw_text($handle, "CLOSE BY", 0, $yPos);
        printer_draw_text($handle, ": ".$print_data['oc_cashier']->close_by_name, 120, $yPos);
        $yPos += 10;
        
        printer_draw_text($handle, "JML TRANSAKSI", 0, $yPos);
        printer_draw_text($handle, ": ".$print_data['transaction']->transaction_count, 120, $yPos);        

        /////////// Item Sales
        $yPos += 13;
        printer_draw_line($handle, 0, $yPos, 600, $yPos);

        $yPos += 8;
        printer_draw_text($handle, "Type", 0, $yPos);
        printer_draw_text($handle, "Qty", 220, $yPos);
        printer_draw_text($handle, "Amount", 275, $yPos);

        $yPos += 10;
        printer_draw_line($handle, 0, $yPos, 600, $yPos);

        $yPos += 10;
        printer_draw_text($handle, "Item Sales", 0, $yPos);
        printer_draw_text($handle, "(+)", 190, $yPos);
        printer_draw_text($handle, $print_data['oc_menu']->amount, 220, $yPos);
        printer_draw_text($handle, number_format($print_data['net_sales']->amount), 275, $yPos);
        $yPos += 10;

        foreach($print_data['taxes_foreach'] as $d){          
          printer_draw_text($handle, $d->info, 0, $yPos);
          printer_draw_text($handle, number_format($d->amount), 275, $yPos);
          $yPos += 10;
        }

        if($print_data['round_up']->amount>0){
          printer_draw_text($handle, "Pembulatan", 0, $yPos);
          printer_draw_text($handle, "(+)", 190, $yPos);
          printer_draw_text($handle, number_format($print_data['round_up']->amount), 275, $yPos);
          $yPos += 10;
        }

        if($print_data['delivery_charge']->amount>0){
          printer_draw_text($handle, "Ongkos Kirim", 0, $yPos);
          printer_draw_text($handle, "(+)", 190, $yPos);
          printer_draw_text($handle, number_format($print_data['delivery_charge']->amount), 275, $yPos);
          $yPos += 10;
        }

        if($print_data['petty_cash']->amount>0){
          printer_draw_text($handle, "Kas Kecil", 0, $yPos);
          printer_draw_text($handle, "(-)", 190, $yPos);
          printer_draw_text($handle, number_format($print_data['petty_cash']->amount), 275, $yPos);
          $yPos += 10;
        }

        if($print_data['dp_out']->amount>0){
          printer_draw_text($handle, "DP OUT", 0, $yPos);
          printer_draw_text($handle, "(-)", 190, $yPos);
          printer_draw_text($handle, number_format($print_data['dp_out']->amount), 275, $yPos);
          $yPos += 10;
        }

        if($print_data['delivery']->amount>0){
          printer_draw_text($handle, "Komisi Delivery", 0, $yPos);
          printer_draw_text($handle, "(-)", 190, $yPos);
          printer_draw_text($handle, number_format($print_data['delivery']->amount), 275, $yPos);
          $yPos += 10;
        }
        
        if($print_data['discount']->amount>0){
          printer_draw_text($handle, "Discount", 0, $yPos);
          printer_draw_text($handle, "(-)", 190, $yPos);
          printer_draw_text($handle, number_format($print_data['discount']->amount), 275, $yPos);
          $yPos += 10;
        }
        
        if($print_data['bon']->amount>0){
          printer_draw_text($handle, "BON", 0, $yPos);
          printer_draw_text($handle, "(-)", 190, $yPos);
          printer_draw_text($handle, number_format($print_data['bon']->amount), 275, $yPos);
          $yPos += 10;
        }

        if($print_data['voucher']->amount>0){
          printer_draw_text($handle, "Voucher", 0, $yPos);
          printer_draw_text($handle, "(-)", 190, $yPos);
          printer_draw_text($handle, number_format($print_data['voucher']->amount), 275, $yPos);
          $yPos += 10;
        }
        
        if($print_data['compliment']->amount>0){
          printer_draw_text($handle, "Compliment", 0, $yPos);
          printer_draw_text($handle, "(-)", 190, $yPos);
          printer_draw_text($handle, number_format($print_data['compliment']->amount), 275, $yPos);
          $yPos += 10;
        }

        if($print_data['cash_company']->amount>0){
          printer_draw_text($handle, "Cash Company", 0, $yPos);
          printer_draw_text($handle, "(-)", 190, $yPos);
          printer_draw_text($handle, number_format($print_data['cash_company']->amount), 275, $yPos);
          $yPos += 10;
        }
        
        if($print_data['pending_bill_company']->amount>0){
          printer_draw_text($handle, "PB Company", 0, $yPos);
          printer_draw_text($handle, "(-)", 190, $yPos);
          printer_draw_text($handle, $print_data['pending_bill_company']->countd, 220, $yPos);
          printer_draw_text($handle, number_format($print_data['pending_bill_company']->amount), 275, $yPos);
          $yPos += 10;
        }
        
        if($print_data['pending_bill_employee']->amount>0){
          printer_draw_text($handle, "PB Employee ", 0, $yPos);
          printer_draw_text($handle, "(-)", 190, $yPos);
          printer_draw_text($handle, $print_data['pending_bill_employee']->countd, 220, $yPos);
          printer_draw_text($handle, number_format($print_data['pending_bill_employee']->amount), 275, $yPos);
          $yPos += 10;
        }

        $omzet=$print_data['net_sales']->amount+$print_data['taxes']->amount+$print_data['round_up']->amount+$print_data['delivery_charge']->amount;
        $total=$omzet-$print_data['voucher']->amount-$print_data['compliment']->amount-$print_data['cash_company']->amount-$print_data['pending_bill_company']->amount-$print_data['pending_bill_employee']->amount;
        $total-=($print_data['petty_cash']->amount+$print_data['dp_out']->amount+$print_data['delivery']->amount+$print_data['discount']->amount+$print_data['bon']->amount);

        $yPos += 15;
        printer_draw_text($handle, "Total Sales", 0, $yPos);
        printer_draw_text($handle, "(=)", 190, $yPos);
        printer_draw_text($handle, number_format($total), 275, $yPos);
        $yPos += 10;
        printer_draw_text($handle, "Estimated Sales", 0, $yPos);
        printer_draw_text($handle, number_format($total), 275, $yPos);

        ///////////  Media
        $yPos += 13;
        printer_draw_line($handle, 0, $yPos, 600, $yPos);

        $yPos += 8;
        _write_text_center_align($handle, "MEDIA", $yPos, 200);

        $yPos += 13;
        printer_draw_line($handle, 0, $yPos, 600, $yPos);

        $yPos += 10;
        printer_draw_text($handle, "Cash", 0, $yPos);
        printer_draw_text($handle, $print_data['cash']->countd, 220, $yPos);
        printer_draw_text($handle, number_format($print_data['cash']->amount), 275, $yPos);

        $yPos += 10;
        $total_plus=$print_data['cash']->amount;
        $total_card=0;
        $total_card2=0;
        
        $jml=$print_data['cash']->countd;

        foreach($print_data['debit'] as $d){
          $total_plus+=$d->amount;
          $total_card+=$d->amount;
          $total_card2+=$d->countd;
          $jml+=$d->countd;
          printer_draw_text($handle, strtoupper($d->bank_name)."-DB", 0, $yPos);
          printer_draw_text($handle, $d->countd, 220, $yPos);
          printer_draw_text($handle, number_format($d->amount), 275, $yPos);
          $yPos += 10;
        }

        foreach($print_data['credit'] as $d){              
          $total_plus+=$d->amount;
          $total_card+=$d->amount;
          $total_card2+=$d->countd;
          $jml+=$d->countd;
          printer_draw_text($handle, strtoupper($d->bank_name)."-CR", 0, $yPos);
          printer_draw_text($handle, $d->countd, 220, $yPos);
          printer_draw_text($handle, number_format($d->amount), 275, $yPos);
          $yPos += 10;
        }

        foreach($print_data['flazz'] as $d){              
          $total_plus+=$d->amount;
          $total_card+=$d->amount;
          $total_card2+=$d->countd;
          $jml+=$d->countd;
          printer_draw_text($handle, strtoupper($d->bank_name)."-Flazz", 0, $yPos);
          printer_draw_text($handle, $d->countd, 220, $yPos);
          printer_draw_text($handle, number_format($d->amount), 275, $yPos);
          $yPos += 10;
        }
        $total_cash = $print_data['cash']->amount;

        $yPos += 13;
        printer_draw_text($handle, "TOTAL CARD", 0, $yPos);
        printer_draw_text($handle, $total_card2, 220, $yPos);
        printer_draw_text($handle, number_format($total_card), 275, $yPos);

        $yPos += 10;
        printer_draw_text($handle, "TOTAL CASH", 0, $yPos);
        printer_draw_text($handle, $print_data['cash']->countd, 220, $yPos);
        printer_draw_text($handle, number_format($total_cash), 275, $yPos);

        ///////////// Total collection        
        $yPos += 13;
        printer_draw_line($handle, 0, $yPos, 600, $yPos);

        $yPos += 8;
        printer_draw_text($handle, "Total collection", 0, $yPos);
        printer_draw_text($handle, $jml, 220, $yPos);
        printer_draw_text($handle, number_format($total_card+$total_cash), 275, $yPos);

        $yPos += 10;
        printer_draw_line($handle, 0, $yPos, 600, $yPos);

        $font = printer_create_font("Courier New", 10, 10, PRINTER_FW_BOLD, false, false, false, 0);
        printer_select_font($handle, $font);

        $yPos += 8;
        printer_draw_text($handle, "NET SALES", 0, $yPos);
        printer_draw_text($handle, number_format($print_data['net_sales']->amount), 275, $yPos);

        $font = printer_create_font("Courier New", 10, 8, PRINTER_FW_BOLD, false, false, false, 0);
        printer_select_font($handle, $font);

        $yPos += 12;
        printer_draw_line($handle, 0, $yPos, 600, $yPos);

        //////// Bill Pending        
        $yPos += 10;
        printer_draw_text($handle, "PB Company", 0, $yPos);
        printer_draw_text($handle, $print_data['pending_bill_company']->countd, 220, $yPos);
        printer_draw_text($handle, number_format($print_data['pending_bill_company']->amount), 275, $yPos);

        $yPos += 10;
        printer_draw_text($handle, "PB Employee ", 0, $yPos);
        printer_draw_text($handle, $print_data['pending_bill_employee']->countd, 220, $yPos);
        printer_draw_text($handle, number_format($print_data['pending_bill_employee']->amount), 275, $yPos);
                 
        $bill = $print_data['transaction']->transaction_count;         
        $yPos += 15;
        printer_draw_text($handle, "Total # of Bills", 0, $yPos);
        printer_draw_text($handle, $bill, 275, $yPos);
        $avg_bill = ($total_card+$total_cash) / $bill;
        $yPos += 10;
        printer_draw_text($handle, "Avg Bills", 0, $yPos);
        printer_draw_text($handle, number_format(round($avg_bill)), 275, $yPos);

        $menu = $print_data['oc_menu']->amount;
        $yPos += 10;
        printer_draw_text($handle, "Total # of Menus", 0, $yPos);
        printer_draw_text($handle, number_format($menu), 275, $yPos);
        $avg_menu = ($total_card+$total_cash) / $menu;
        $yPos += 10;
        printer_draw_text($handle, "Avg Menus", 0, $yPos);
        printer_draw_text($handle, number_format(round($avg_menu)), 275, $yPos);

        $covers = $print_data['transaction']->transaction_count;
        $yPos += 10;
        printer_draw_text($handle, "Total # of Covers", 0, $yPos);
        printer_draw_text($handle, number_format($covers), 275, $yPos);
        $avg_covers = ($total_card+$total_cash) / $covers;
        $yPos += 10;
        printer_draw_text($handle, "Avg Covers", 0, $yPos);
        printer_draw_text($handle, number_format(round($avg_covers)), 275, $yPos);

        $yPos += 10;
        printer_draw_line($handle, 0, $yPos, 600, $yPos);

        $yPos += 10;
        printer_draw_text($handle, "Begin Receipt #", 0, $yPos);
        printer_draw_text($handle, $print_data['begin_end_receipt']->begin, 275, $yPos);

        $yPos += 10;
        printer_draw_text($handle, "End Receipt #", 0, $yPos);
        printer_draw_text($handle, $print_data['begin_end_receipt']->end, 275, $yPos);

        $yPos += 10;
        printer_draw_line($handle, 0, $yPos, 600, $yPos);


        $yPos += 8;
        _write_text_center_align($handle, "GROUP SALES", $yPos, 200);

        $yPos += 12;
        printer_draw_line($handle, 0, $yPos, 600, $yPos);


        $yPos += 10;
        $ttl_gr = 0;
        foreach($print_data['oc_category'] as $d){
          $ttl_gr += $d->mnpric;
          printer_draw_text($handle, strtoupper($d->ctgname), 0, $yPos);
          printer_draw_text($handle, $d->quantt, 220, $yPos);
          printer_draw_text($handle, number_format($d->mnpric), 275, $yPos);
          $yPos += 10;
        }

        $yPos += 15;
        printer_draw_text($handle, "Total Group", 0, $yPos);
        printer_draw_text($handle, $print_data['oc_menu']->amount, 220, $yPos);
        printer_draw_text($handle, number_format($ttl_gr), 275, $yPos);

        ////// sales category        
        $yPos += 13;
        printer_draw_line($handle, 0, $yPos, 600, $yPos);

        $yPos += 8;
        _write_text_center_align($handle, "SALES CATEGORY", $yPos, 200);

        $yPos += 12;
        printer_draw_line($handle, 0, $yPos, 600, $yPos);

        $yPos += 10;
        printer_draw_text($handle, "Dine In", 0, $yPos);
        printer_draw_text($handle, number_format($print_data['oc_dinein']->dinein), 220, $yPos);
        printer_draw_text($handle, number_format($print_data['oc_dinein']->ttldn), 275, $yPos);
        
        $yPos += 10;
        printer_draw_text($handle, "Take Away", 0, $yPos);
        printer_draw_text($handle, number_format($print_data['oc_takeaway']->takeaway), 220, $yPos);
        printer_draw_text($handle, number_format($print_data['oc_takeaway']->ttltkw), 275, $yPos);

        $yPos += 10;
        printer_draw_text($handle, "Delivery", 0, $yPos);
        printer_draw_text($handle, number_format($print_data['oc_delivery']->delivery), 220, $yPos);
        printer_draw_text($handle, number_format($print_data['oc_delivery']->ttldlv), 275, $yPos);

        /////  diskon/promotion        
        $yPos += 13;
        printer_draw_line($handle, 0, $yPos, 600, $yPos);

        $yPos += 8;
        _write_text_center_align($handle, "DISCOUNT / PROMOTION", $yPos, 200);

        $yPos += 12;
        printer_draw_line($handle, 0, $yPos, 600, $yPos);

        $yPos += 10;        
        printer_draw_text($handle, "Discount", 0, $yPos);
        printer_draw_text($handle, number_format($print_data['discount']->amount), 275, $yPos);
        $yPos += 10;
        
        printer_draw_text($handle, "Voucher", 0, $yPos);
        printer_draw_text($handle, number_format($print_data['voucher']->amount), 275, $yPos);
        
        /////// FIPO
        $yPos += 13;
        printer_draw_line($handle, 0, $yPos, 600, $yPos);

        $yPos += 10;
        printer_draw_line($handle, 0, $yPos, 600, $yPos);

        //// end 2
        $yPos += 13;
        printer_draw_text($handle, "KASIR", 50, $yPos);
        printer_draw_text($handle, "SUPERVISOR", 200, $yPos);
        $yPos += 55;
        printer_draw_text($handle, "(.......)", 40, $yPos);
        printer_draw_text($handle, "(..........)", 190, $yPos);
        $yPos += 20;

        // Header
        $pen = printer_create_pen(PRINTER_PEN_SOLID, 1, "000000");
        printer_select_pen($handle, $pen);

        printer_delete_font($font);
        printer_end_page($handle);
        printer_end_doc($handle);
        printer_close($handle);
    }
}

function print_open_close_bill_mode2_generic($printer_name = '', $print_data = '', $user_data,$printer_setting=array()){
  $GLOBALS['printer_setting']=$printer_setting;
  $printer_name=convert_printer_name($printer_name);
  if (function_exists('printer_open')) {
      $ci=&get_instance(); 
        // $min_80=$ci->config->item("72_80");
        // set up
        $var_max_char_length = 25;
        $yPos                = 0;

        $handle = printer_open($printer_name);
        if($handle==false)return false;
        printer_set_option($handle, PRINTER_MODE, "RAW"); // cut mode
        printer_start_doc($handle,"Start Doc");
        printer_start_page($handle);

        $font = printer_create_font("Courier New", 10, 10, PRINTER_FW_BOLD, false, false, false, 0);
        printer_select_font($handle, $font);
          
        printer_draw_text($handle, "TANGGAL OPEN", 0, $yPos);
        printer_draw_text($handle, ": ".date("d/m/Y",strtotime($print_data['oc_cashier']->open_at)), 120, $yPos);
        $yPos = $yPos + 15;
        printer_draw_text($handle, "JAM OPEN", 0, $yPos);
        printer_draw_text($handle, ": ".date("H:i:s",strtotime($print_data['oc_cashier']->open_at)), 120, $yPos);
        $yPos = $yPos + 15;

        printer_draw_text($handle, "WAKTU CLOSE", 0, $yPos);
        printer_draw_text($handle, ": ".date("d/m/Y",strtotime($print_data['oc_cashier']->close_at)), 120, $yPos);
        $yPos = $yPos + 15;
        printer_draw_text($handle, "JAM CLOSE", 0, $yPos);
        printer_draw_text($handle, ": ".date("H:i:s",strtotime($print_data['oc_cashier']->close_at)), 120, $yPos);
        $yPos = $yPos + 15;

        printer_draw_text($handle, "OPEN BY", 0, $yPos);
        printer_draw_text($handle, ": ".$print_data['oc_cashier']->open_by_name, 120, $yPos);
        $yPos = $yPos + 15;

        printer_draw_text($handle, "CLOSE BY", 0, $yPos);
        printer_draw_text($handle, ": ".$print_data['oc_cashier']->close_by_name, 120, $yPos);
        $yPos = $yPos + 15;
        
        printer_draw_text($handle, "JML TRANSAKSI", 0, $yPos);
        printer_draw_text($handle, ": ".$print_data['transaction']->transaction_count, 120, $yPos);
        
        $yPos = $yPos + 17;
        printer_draw_line($handle, 0, $yPos, 600, $yPos);
        $yPos = $yPos + 7;
        _write_text_center_align($handle, "CLOSE CASHIER", $yPos, 200);
        $yPos = $yPos + 10;
        printer_draw_line($handle, 0, $yPos, 600, $yPos);
        $yPos = $yPos + 10;
        printer_draw_text($handle, "CASH", 0, $yPos);
        printer_draw_text($handle, ": ".convert_rupiah($print_data['cash']->amount+$print_data['cash_dp']->amount), 120, $yPos);
        $yPos = $yPos + 15;

        printer_draw_text($handle, "DP IN", 0, $yPos);
        printer_draw_text($handle, ": (".convert_rupiah($print_data['cash_dp']->amount).")", 120, $yPos);
        $yPos = $yPos + 15;
        
        printer_draw_text($handle, "DP TRANSFER", 0, $yPos);
        printer_draw_text($handle, ": (".convert_rupiah($print_data['transfer_dp']->amount).")", 120, $yPos);
        $yPos = $yPos + 15;
        
        printer_draw_text($handle, "DP KARTU", 0, $yPos);
        printer_draw_text($handle, ": (".convert_rupiah($print_data['transfer_direct_dp']->amount).")", 120, $yPos);
        $yPos = $yPos + 15;
        // $total_plus=$print_data['cash']->amount+$print_data['cash_dp']->amount+$print_data['transfer_dp']->amount;
        $total_plus=$print_data['cash']->amount;
        foreach($print_data['debit'] as $d){
          $total_plus+=$d->amount;
          printer_draw_text($handle, strtoupper($d->bank_name)."-DB", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($d->amount), 120, $yPos);
          $yPos = $yPos + 15;
        }
        foreach($print_data['credit'] as $d){              
          $total_plus+=$d->amount;
          printer_draw_text($handle, strtoupper($d->bank_name)."-CR", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($d->amount), 120, $yPos);
          $yPos = $yPos + 15;
        }
        foreach($print_data['flazz'] as $d){              
          $total_plus+=$d->amount;
          printer_draw_text($handle, strtoupper($d->bank_name)."-Flazz", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($d->amount), 120, $yPos);
          $yPos = $yPos + 15;
        }
        printer_draw_text($handle, "+", 400, $yPos);
        printer_draw_line($handle, 0, $yPos, 600, $yPos);
        $yPos = $yPos + 7;
        printer_draw_text($handle, "JUMLAH", 0, $yPos);
        printer_draw_text($handle, ": ".convert_rupiah($total_plus), 120, $yPos);
        $yPos = $yPos + 15;
        
        $total_minus = 
          $print_data['petty_cash']->amount + 
          $print_data['dp_out']->amount+
          $print_data['bon']->amount + 
          $print_data['voucher']->amount +
          $print_data['compliment']->amount + 
          $print_data['discount']->amount +
          $print_data['cash_company']->amount + 
          $print_data['pending_bill_company']->amount + 
          $print_data['pending_bill_employee']->amount + 
          $print_data['delivery']->amount;
          
        printer_draw_text($handle, "KAS KECIL", 0, $yPos);
        printer_draw_text($handle, ": ".convert_rupiah($print_data['petty_cash']->amount), 120, $yPos);
        $yPos = $yPos + 15;
        
        printer_draw_text($handle, "DELIVERY", 0, $yPos);
        printer_draw_text($handle, ": ".convert_rupiah($print_data['delivery']->amount), 120, $yPos);
        $yPos = $yPos + 15;
        
        printer_draw_text($handle, "DP OUT", 0, $yPos);
        printer_draw_text($handle, ": ".convert_rupiah($print_data['dp_out']->amount), 120, $yPos);
        $yPos = $yPos + 15;
        
        printer_draw_text($handle, "BON", 0, $yPos);
        printer_draw_text($handle, ": ".convert_rupiah($print_data['bon']->amount), 120, $yPos);
        $yPos = $yPos + 15;
        if($print_data['voucher']->amount>0){
          printer_draw_text($handle, "VOUCHER", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['voucher']->amount), 120, $yPos);
          $yPos = $yPos + 15;          
        }
        if($print_data['compliment']->amount>0){
          printer_draw_text($handle, "COMPLIMENT", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['compliment']->amount), 120, $yPos);
          $yPos = $yPos + 15;          
        }
        if($print_data['discount']->amount>0){
          printer_draw_text($handle, "DISCOUNT", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['discount']->amount), 120, $yPos);
          $yPos = $yPos + 15;          
        }
        if($print_data['cash_company']->amount>0){
          printer_draw_text($handle, "CASH COMPANY", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['cash_company']->amount), 120, $yPos);
          $yPos = $yPos + 15;          
        }
        if($print_data['pending_bill_company']->amount>0){
          printer_draw_text($handle, "PB COMPANY", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['pending_bill_company']->amount), 120, $yPos);
          $yPos = $yPos + 15;          
        }
        if($print_data['pending_bill_employee']->amount>0){
          printer_draw_text($handle, "PB EMPLOYEE ", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['pending_bill_employee']->amount), 120, $yPos);
          $yPos = $yPos + 15;          
        }
        printer_draw_text($handle, "+", 400, $yPos);
        printer_draw_line($handle, 0, $yPos, 600, $yPos);
        $yPos = $yPos + 7;
        printer_draw_text($handle, "JUMLAH", 0, $yPos);
        printer_draw_text($handle, ": ".convert_rupiah($total_minus), 120, $yPos);
        $yPos = $yPos + 15;
        printer_draw_text($handle, "OMSET", 0, $yPos);
        $total_plus+=$print_data['petty_cash']->amount+$print_data['dp_out']->amount+$print_data['delivery']->amount+$print_data['bon']->amount;
        printer_draw_text($handle, ": ".convert_rupiah($total_plus), 120, $yPos);
        $yPos = $yPos + 15;
        printer_draw_text($handle, "SALDO AWAL", 0, $yPos);
        printer_draw_text($handle, ": ".convert_rupiah($print_data['oc_cashier']->begin_balance), 120, $yPos);
        $yPos = $yPos + 25;
        if($print_data['balance_cash_history']->amount>0){
          printer_draw_text($handle, "PENAMBAH SALDO", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['balance_cash_history']->amount), 120, $yPos);
          $yPos = $yPos + 25;
          printer_draw_text($handle, "SALDO AKHIR", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['oc_cashier']->begin_balance+$print_data['balance_cash_history']->amount), 120, $yPos);
          $yPos = $yPos + 25;
        }
        if($print_data['setting']['cash_on_hand']==1){
          printer_draw_line($handle, 0, $yPos, 600, $yPos);
          $yPos = $yPos + 7;
          printer_draw_text($handle, "CASH ON HAND", 0, $yPos);
          printer_draw_text($handle, ":".convert_rupiah($print_data['oc_cashier']->cash_on_hand), 120, $yPos);
          $yPos = $yPos + 15;
          printer_draw_text($handle, "SELISIH CASH", 0, $yPos);
          printer_draw_text($handle, ":".convert_rupiah($print_data['cash']->amount+$print_data['cash_dp']->amount - $print_data['oc_cashier']->cash_on_hand), 120, $yPos);
          $yPos = $yPos + 10;
        }
        $yPos = $yPos + 15;
        printer_draw_text($handle, "KASIR", 30, $yPos);
        printer_draw_text($handle, "SUPERVISOR", 140, $yPos);
        $yPos = $yPos + 50;
        printer_draw_text($handle, "(.......)", 20, $yPos);
        printer_draw_text($handle, "(..........)", 130, $yPos);
        $yPos = $yPos + 20;
        // Header
        $pen = printer_create_pen(PRINTER_PEN_SOLID, 1, "000000");
        printer_select_pen($handle, $pen);

        printer_delete_font($font);
        printer_end_page($handle);
        printer_end_doc($handle);
        printer_close($handle);
    }
}

function print_open_close_bill_mode3_generic($printer_name = '', $print_data = '', $user_data,$printer_setting=array()){
  $GLOBALS['printer_setting']=$printer_setting;
  $printer_name=convert_printer_name($printer_name);
  if (function_exists('printer_open')) {
      $ci=&get_instance(); 
        // $min_80=$ci->config->item("72_80");
        // set up
        $var_max_char_length = 25;
        $yPos                = 0;

        $handle = printer_open($printer_name);
        if($handle==false)return false;
        printer_set_option($handle, PRINTER_MODE, "RAW"); // cut mode
        printer_start_doc($handle,"Start Doc");
        printer_start_page($handle);

        $font = printer_create_font("Courier New", 10, 10, PRINTER_FW_BOLD, false, false, false, 0);
        printer_select_font($handle, $font);
        if(isset($print_data['setting']['printer_logo']) && (!empty($printer_setting) && $printer_setting->default == 1) ){
          $path_logo=FCPATH.$print_data['setting']['printer_logo'];
          if(file_exists($path_logo)){ 
            printer_draw_bmp($handle, $path_logo, 50, 50,300,150);
            $yPos+=50;
          }          
        }
        _write_text_center_align($handle, strtoupper($print_data['store_data']->store_name), $yPos, 200, TRUE);
        $yPos = $yPos + 12;
        $string = trim($print_data['store_data']->store_address);

        if (strlen($string) > 25) {
            $string = wordwrap($string, 25);
            $string = explode("\n", $string, 3);
            $count  = 1;
            foreach ($string as $str) {
                 _write_text_center_align($handle, $str, $yPos, 200);
                $count++;
                $yPos = $yPos + 12;
            }
        }else {
            _write_text_center_align($handle, $string, $yPos, 200);
            $yPos = $yPos + 12;
        }
        _write_text_center_align($handle, $print_data['store_data']->store_phone, $yPos, 200);
        

        $yPos = $yPos + 20;
        printer_draw_text($handle, "TANGGAL OPEN", 0, $yPos);
        printer_draw_text($handle, ":".date("d/m/Y",strtotime($print_data['oc_cashier']->open_at)), 120, $yPos);
        $yPos = $yPos + 15;
        printer_draw_text($handle, "JAM OPEN", 0, $yPos);
        printer_draw_text($handle, ":".date("H:i:s",strtotime($print_data['oc_cashier']->open_at)), 120, $yPos);
        $yPos = $yPos + 15;

        printer_draw_text($handle, "WAKTU CLOSE", 0, $yPos);
        printer_draw_text($handle, ":".date("d/m/Y",strtotime($print_data['oc_cashier']->close_at)), 120, $yPos);
        $yPos = $yPos + 15;
        printer_draw_text($handle, "JAM CLOSE", 0, $yPos);
        printer_draw_text($handle, ":".date("H:i:s",strtotime($print_data['oc_cashier']->close_at)), 120, $yPos);
        $yPos = $yPos + 15;

        printer_draw_text($handle, "OPEN BY", 0, $yPos);
        printer_draw_text($handle, ":".$print_data['oc_cashier']->open_by_name, 120, $yPos);
        $yPos = $yPos + 15;

        printer_draw_text($handle, "CLOSE BY", 0, $yPos);
        printer_draw_text($handle, ":".$print_data['oc_cashier']->close_by_name, 120, $yPos);
        $yPos = $yPos + 15;
        
        printer_draw_text($handle, "JML TRANSAKSI", 0, $yPos);
        printer_draw_text($handle, ":".$print_data['transaction']->transaction_count, 120, $yPos);
        
        $yPos = $yPos + 20;
        printer_draw_line($handle, 0, $yPos, 600, $yPos);
        $yPos = $yPos + 7;
        _write_text_center_align($handle, "CLOSE CASHIER", $yPos, 200);
        $yPos = $yPos + 10;
        printer_draw_line($handle, 0, $yPos, 600, $yPos);
        $yPos = $yPos + 10;
        
        
        
        printer_draw_text($handle, "NET SALES", 0, $yPos);
        printer_draw_text($handle, ":".convert_rupiah($print_data['net_sales']->amount), 150, $yPos);
        $yPos = $yPos + 15;

        printer_draw_text($handle, "Ppn", 0, $yPos);
        printer_draw_text($handle, ":".convert_rupiah($print_data['taxes']->amount), 150, $yPos);
        $yPos = $yPos + 15;
        
        printer_draw_text($handle, "PEMBULATAN", 0, $yPos);
        printer_draw_text($handle, ":".convert_rupiah($print_data['round_up']->amount), 150, $yPos);
        $yPos = $yPos + 15;
        
        printer_draw_text($handle, "ONGKOS KIRIM", 0, $yPos);
        printer_draw_text($handle, ":".convert_rupiah($print_data['delivery_charge']->amount), 150, $yPos);
        $yPos = $yPos + 15;
        printer_draw_text($handle, "+", 400, $yPos);
        printer_draw_line($handle, 0, $yPos, 600, $yPos);
        $yPos = $yPos + 7;
        $omzet=$print_data['net_sales']->amount+$print_data['taxes']->amount+$print_data['round_up']->amount+$print_data['delivery_charge']->amount;
        printer_draw_text($handle, "OMSET", 0, $yPos);
        printer_draw_text($handle, ":".convert_rupiah($omzet), 150, $yPos);
        $yPos = $yPos + 15;
        printer_draw_text($handle, "KAS KECIL", 0, $yPos);
        printer_draw_text($handle, ":".convert_rupiah($print_data['petty_cash']->amount), 150, $yPos);
        $yPos = $yPos + 15;
        printer_draw_text($handle, "DP OUT", 0, $yPos);
        printer_draw_text($handle, ":".convert_rupiah($print_data['dp_out']->amount), 150, $yPos);
        $yPos = $yPos + 15;
        printer_draw_text($handle, "KOMISI DELIVERY", 0, $yPos);
        printer_draw_text($handle, ":".convert_rupiah($print_data['delivery']->amount), 150, $yPos);
        $yPos = $yPos + 15;
        printer_draw_text($handle, "DISCOUNT", 0, $yPos);
        printer_draw_text($handle, ":".convert_rupiah($print_data['discount']->amount), 150, $yPos);
        $yPos = $yPos + 15;          
        printer_draw_text($handle, "BON", 0, $yPos);
        printer_draw_text($handle, ":".convert_rupiah($print_data['bon']->amount), 150, $yPos);
        $yPos = $yPos + 15;
        if($print_data['voucher']->amount>0){
          printer_draw_text($handle, "VOUCHER", 0, $yPos);
          printer_draw_text($handle, ":".convert_rupiah($print_data['voucher']->amount), 150, $yPos);
          $yPos = $yPos + 15;          
        }
        if($print_data['compliment']->amount>0){
          printer_draw_text($handle, "COMPLIMENT", 0, $yPos);
          printer_draw_text($handle, ":".convert_rupiah($print_data['compliment']->amount), 150, $yPos);
          $yPos = $yPos + 15;          
        }
        if($print_data['cash_company']->amount>0){
          printer_draw_text($handle, "CASH COMPANY", 0, $yPos);
          printer_draw_text($handle, ":".convert_rupiah($print_data['cash_company']->amount), 150, $yPos);
          $yPos = $yPos + 15;          
        }
        if($print_data['pending_bill_company']->amount>0){
          printer_draw_text($handle, "PB COMPANY", 0, $yPos);
          printer_draw_text($handle, ":".convert_rupiah($print_data['pending_bill_company']->amount), 150, $yPos);
          $yPos = $yPos + 15;          
        }
        if($print_data['pending_bill_employee']->amount>0){
          printer_draw_text($handle, "PB EMPLOYEE ", 0, $yPos);
          printer_draw_text($handle, ":".convert_rupiah($print_data['pending_bill_employee']->amount), 150, $yPos);
          $yPos = $yPos + 15;          
        }
        printer_draw_text($handle, "-", 400, $yPos);
        printer_draw_line($handle, 0, $yPos, 600, $yPos);
        $yPos = $yPos + 7;
        $total=$omzet-$print_data['voucher']->amount-$print_data['compliment']->amount-$print_data['cash_company']->amount-$print_data['pending_bill_company']->amount-$print_data['pending_bill_employee']->amount;
        $total-=($print_data['petty_cash']->amount+$print_data['dp_out']->amount+$print_data['delivery']->amount+$print_data['discount']->amount+$print_data['bon']->amount);
        printer_draw_text($handle, "JML SETORAN", 0, $yPos);
        printer_draw_text($handle, ":".convert_rupiah($total), 150, $yPos);
        $yPos = $yPos + 15;
        $total_card=0;
        foreach($print_data['debit'] as $d){
          $total_card+=$d->amount;
          printer_draw_text($handle, strtoupper($d->bank_name)."-DB", 0, $yPos);
          printer_draw_text($handle, ":".convert_rupiah($d->amount), 150, $yPos);
          $yPos = $yPos + 15;
        }
        foreach($print_data['credit'] as $d){              
          $total_card+=$d->amount;
          printer_draw_text($handle, strtoupper($d->bank_name)."-CR", 0, $yPos);
          printer_draw_text($handle, ":".convert_rupiah($d->amount), 150, $yPos);
          $yPos = $yPos + 15;
        }
        foreach($print_data['flazz'] as $d){              
          $total_card+=$d->amount;
          printer_draw_text($handle, strtoupper($d->bank_name)."-Flazz", 0, $yPos);
          printer_draw_text($handle, ":".convert_rupiah($d->amount), 150, $yPos);
          $yPos = $yPos + 15;
        }
        printer_draw_text($handle, "-", 400, $yPos);
        printer_draw_line($handle, 0, $yPos, 600, $yPos);
        $yPos = $yPos + 7;
        $total2=$total-$total_card;
        printer_draw_text($handle, "CASH", 0, $yPos);
        printer_draw_text($handle, ":".convert_rupiah($total2), 150, $yPos);
        $yPos = $yPos + 15;
        if($print_data['setting']['cash_on_hand']==1){
          printer_draw_text($handle, "CASH ON HAND", 0, $yPos);
          printer_draw_text($handle, ":".convert_rupiah($print_data['oc_cashier']->cash_on_hand), 150, $yPos);
          $yPos = $yPos + 15;
        }else{
          printer_draw_text($handle, "REAL CASH", 0, $yPos);
          printer_draw_text($handle, ":", 150, $yPos);
          $yPos = $yPos + 15;
        }
        printer_draw_line($handle, 0, $yPos, 600, $yPos);
        $yPos = $yPos + 7;
        printer_draw_text($handle, "SELISIH", 0, $yPos);
        if($print_data['setting']['cash_on_hand']==1){
          printer_draw_text($handle, ":".convert_rupiah($total2-$print_data['oc_cashier']->cash_on_hand), 150, $yPos);
        }else{
          printer_draw_text($handle, ":", 150, $yPos);
        }
        $yPos = $yPos + 25;
        
        printer_draw_text($handle, "DP CASH", 0, $yPos);
        printer_draw_text($handle, ":".convert_rupiah($print_data['cash_dp']->amount), 150, $yPos);
        $yPos = $yPos + 15;
        printer_draw_text($handle, "DP CARD", 0, $yPos);
        printer_draw_text($handle, ":".convert_rupiah($print_data['transfer_direct_dp']->amount), 150, $yPos);
        $yPos = $yPos + 15;
        printer_draw_text($handle, "DP TRANSFER", 0, $yPos);
        printer_draw_text($handle, ":".convert_rupiah($print_data['transfer_dp']->amount), 150, $yPos);
        $yPos = $yPos + 15;
        $yPos = $yPos + 15;
        printer_draw_text($handle, "KASIR", 30, $yPos);
        printer_draw_text($handle, "SUPERVISOR", 140, $yPos);
        $yPos = $yPos + 50;
        printer_draw_text($handle, "(........)", 20, $yPos);
        printer_draw_text($handle, "(..........)", 130, $yPos);
        $yPos = $yPos + 20;
        // Header
        $pen = printer_create_pen(PRINTER_PEN_SOLID, 1, "000000");
        printer_select_pen($handle, $pen);

        printer_delete_font($font);
        printer_end_page($handle);
        printer_end_doc($handle);
        printer_close($handle);
    }
}

function print_open_close_bill_mode4_generic($printer_name = '', $print_data = '', $user_data, $printer_setting = array()){
  $GLOBALS['printer_setting']=$printer_setting;
  $printer_name=convert_printer_name($printer_name);
  if (function_exists('printer_open')) {
      $ci=&get_instance();
        // $min_80=$ci->config->item("72_80");
        // set up
        $var_max_char_length = 25;
        $yPos                = 0;

        $handle = printer_open($printer_name);
        if($handle==false)return false;
        printer_set_option($handle, PRINTER_MODE, "RAW"); // cut mode
        printer_start_doc($handle,"Start Doc");
        printer_start_page($handle);

        $font = printer_create_font("Courier New", 10, 10, PRINTER_FW_BOLD, false, false, false, 0);
        printer_select_font($handle, $font);
        
       
        printer_draw_text($handle, "TANGGAL OPEN", 0, $yPos);
        printer_draw_text($handle, ": ".date("d/m/Y",strtotime($print_data['oc_cashier']->open_at)), 120, $yPos);
        $yPos = $yPos + 15;
        printer_draw_text($handle, "JAM OPEN", 0, $yPos);
        printer_draw_text($handle, ": ".date("H:i:s",strtotime($print_data['oc_cashier']->open_at)), 120, $yPos);
        $yPos = $yPos + 15;

        printer_draw_text($handle, "WAKTU CLOSE", 0, $yPos);
        printer_draw_text($handle, ": ".date("d/m/Y",strtotime($print_data['oc_cashier']->close_at)), 120, $yPos);
        $yPos = $yPos + 15;
        printer_draw_text($handle, "JAM CLOSE", 0, $yPos);
        printer_draw_text($handle, ": ".date("H:i:s",strtotime($print_data['oc_cashier']->close_at)), 120, $yPos);
        $yPos = $yPos + 15;

        printer_draw_text($handle, "OPEN BY", 0, $yPos);
        printer_draw_text($handle, ": ".$print_data['oc_cashier']->open_by_name, 120, $yPos);
        $yPos = $yPos + 15;

        printer_draw_text($handle, "CLOSE BY", 0, $yPos);
        printer_draw_text($handle, ": ".$print_data['oc_cashier']->close_by_name, 120, $yPos);
        $yPos = $yPos + 15;
        
        printer_draw_text($handle, "JML TRANSAKSI", 0, $yPos);
        printer_draw_text($handle, ": ".$print_data['transaction']->transaction_count, 120, $yPos);
        
        $yPos = $yPos + 20;
        printer_draw_line($handle, 0, $yPos, 600, $yPos);
        $yPos = $yPos + 7;
        _write_text_center_align($handle, "CLOSE CASHIER", $yPos, 200);
        $yPos = $yPos + 10;
        printer_draw_line($handle, 0, $yPos, 600, $yPos);
        $yPos = $yPos + 10;

        printer_draw_text($handle, "SALDO AWAL", 0, $yPos);
        printer_draw_text($handle, ": ".convert_rupiah($print_data['oc_cashier']->begin_balance), 120, $yPos);
        $yPos = $yPos + 15;

        $pendapatan = 0;
        $total_plus=$print_data['cash']->amount;
        $total_card = 0;
        foreach($print_data['debit'] as $d){
          $total_plus+=$d->amount; 
          $total_card += $d->amount; 
        }
        foreach($print_data['credit'] as $d){              
          $total_plus+=$d->amount; 
          $total_card += $d->amount; 
        }

        $pendapatan +=  $total_plus + 
                        $print_data['cash_dp']->amount 
                        + $print_data['transfer_dp']->amount 
                        + $print_data['transfer_direct_dp']->amount;

        printer_draw_text($handle, "PENDAPATAN", 0, $yPos);
        printer_draw_text($handle, ": ".convert_rupiah($pendapatan), 120, $yPos);
        $yPos = $yPos + 15; 


        printer_draw_text($handle, "Cash", 0, $yPos);
        printer_draw_text($handle, ": ".convert_rupiah($print_data['cash']->amount), 120, $yPos);
        $yPos = $yPos + 15; 
        if($print_data['debit']){
          foreach($print_data['debit'] as $d){ 
            printer_draw_text($handle, strtoupper($d->bank_name)."-DB", 0, $yPos);
            printer_draw_text($handle, ": ".convert_rupiah($d->amount), 120, $yPos);
            $yPos = $yPos + 15;
          }  
        }else{
            printer_draw_text($handle, "Debit", 0, $yPos);
            printer_draw_text($handle, ": ".convert_rupiah(0), 120, $yPos);
            $yPos = $yPos + 15;
        }

        if($print_data['credit']){
          foreach($print_data['credit'] as $d){  
            printer_draw_text($handle, strtoupper($d->bank_name)."-CR", 0, $yPos);
            printer_draw_text($handle, ": ".convert_rupiah($d->amount), 120, $yPos);
            $yPos = $yPos + 15;
          }
        }else{
           printer_draw_text($handle, "Credit", 0, $yPos);
           printer_draw_text($handle, ": ".convert_rupiah(0), 120, $yPos);
           $yPos = $yPos + 15;
        } 

        if($print_data['flazz']){
          foreach($print_data['flazz'] as $d){  
            printer_draw_text($handle, strtoupper($d->bank_name)."-Flazz", 0, $yPos);
            printer_draw_text($handle, ": ".convert_rupiah($d->amount), 120, $yPos);
            $yPos = $yPos + 15;
          }
        }else{
           printer_draw_text($handle, "Flazz", 0, $yPos);
           printer_draw_text($handle, ": ".convert_rupiah(0), 120, $yPos);
           $yPos = $yPos + 15;
        }  

        
        $total_minus =  
          $print_data['dp_out']->amount+
          $print_data['bon']->amount + 
          $print_data['voucher']->amount +
          $print_data['compliment']->amount + 
          $print_data['discount']->amount +
          $print_data['cash_company']->amount + 
          $print_data['pending_bill_company']->amount + 
          $print_data['pending_bill_employee']->amount + 
          $print_data['delivery']->amount; 

        if($print_data['delivery']->amount >0){
          printer_draw_text($handle, "DELIVERY", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['delivery']->amount), 120, $yPos);
          $yPos = $yPos + 15;
        }
        
        if($print_data['dp_out']->amount>0){
          printer_draw_text($handle, "DP OUT", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['dp_out']->amount), 120, $yPos);
          $yPos = $yPos + 15;
        }
        
        if($print_data['bon']->amount>0){
          printer_draw_text($handle, "BON", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['bon']->amount), 120, $yPos);
          $yPos = $yPos + 15;
        }

        
        if($print_data['voucher']->amount>0){
          printer_draw_text($handle, "VOUCHER", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['voucher']->amount), 120, $yPos);
          $yPos = $yPos + 15;          
        }
        if($print_data['compliment']->amount>0){
          printer_draw_text($handle, "COMPLIMENT", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['compliment']->amount), 120, $yPos);
          $yPos = $yPos + 15;          
        }
        if($print_data['discount']->amount>0){
          printer_draw_text($handle, "DISCOUNT", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['discount']->amount), 120, $yPos);
          $yPos = $yPos + 15;          
        }
        if($print_data['cash_company']->amount>0){
          printer_draw_text($handle, "CASH COMPANY", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['cash_company']->amount), 120, $yPos);
          $yPos = $yPos + 15;          
        }
        if($print_data['pending_bill_company']->amount>0){
          printer_draw_text($handle, "PB COMPANY", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['pending_bill_company']->amount), 120, $yPos);
          $yPos = $yPos + 15;          
        }
        if($print_data['pending_bill_employee']->amount>0){
          printer_draw_text($handle, "PB EMPLOYEE ", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['pending_bill_employee']->amount), 120, $yPos);
          $yPos = $yPos + 15;          
        }
        printer_draw_text($handle, "+", 400, $yPos);
        printer_draw_line($handle, 0, $yPos, 600, $yPos);
        $yPos = $yPos + 7;
        printer_draw_text($handle, "JUMLAH", 0, $yPos);
        printer_draw_text($handle, ": ".convert_rupiah($print_data['cash']->amount+$print_data['oc_cashier']->begin_balance), 120, $yPos);
        $yPos = $yPos + 25;

         
        
          printer_draw_text($handle, "MODAL", 0, $yPos);
          printer_draw_text($handle, ": ".convert_rupiah($print_data['balance_cash_history']->amount), 120, $yPos);
          $yPos = $yPos + 15; 
      
      
        printer_draw_text($handle, "KAS KECIL", 0, $yPos);
        printer_draw_text($handle, ": ".convert_rupiah($print_data['petty_cash']->amount), 120, $yPos);
        $yPos = $yPos + 15; 
        printer_draw_line($handle, 0, $yPos, 600, $yPos);
        $yPos = $yPos + 7;

        printer_draw_text($handle, "JUMLAH", 0, $yPos);
        printer_draw_text($handle, ": ".convert_rupiah($print_data['balance_cash_history']->amount - $print_data['petty_cash']->amount), 120, $yPos);
        $yPos = $yPos + 15;
        $total_penambah = $total_plus;
        $total_petty_cash = $print_data['balance_cash_history']->amount - $print_data['petty_cash']->amount;

        printer_draw_line($handle, 0, $yPos, 600, $yPos);
        $yPos = $yPos + 7;

        printer_draw_text($handle, "GRAND TOTAL", 0, $yPos);
        printer_draw_text($handle, ": ".convert_rupiah(($total_penambah - $total_card) + $total_petty_cash + $print_data['oc_cashier']->begin_balance), 120, $yPos);
        $yPos = $yPos + 50;
        
        if($print_data['setting']['cash_on_hand']==1){
          printer_draw_line($handle, 0, $yPos, 600, $yPos);
          $yPos = $yPos + 7;
          printer_draw_text($handle, "CASH ON HAND", 0, $yPos);
          printer_draw_text($handle, ":".convert_rupiah($print_data['oc_cashier']->cash_on_hand), 120, $yPos);
          $yPos = $yPos + 15;
          printer_draw_text($handle, "SELISIH CASH", 0, $yPos);
          printer_draw_text($handle, ":".convert_rupiah($print_data['cash']->amount+$print_data['cash_dp']->amount - $print_data['oc_cashier']->cash_on_hand), 120, $yPos);
          $yPos = $yPos + 10;
        }
        $yPos = $yPos + 15;
        printer_draw_text($handle, "KASIR", 30, $yPos);
        printer_draw_text($handle, "SUPERVISOR", 140, $yPos);
        $yPos = $yPos + 50;
        printer_draw_text($handle, "(.......)", 20, $yPos);
        printer_draw_text($handle, "(..........)", 130, $yPos);
        $yPos = $yPos + 20;
        // Header
        $pen = printer_create_pen(PRINTER_PEN_SOLID, 1, "000000");
        printer_select_pen($handle, $pen);

        printer_delete_font($font);
        printer_end_page($handle);
        printer_end_doc($handle);
        printer_close($handle);
    }
}

if (! function_exists('print_order_kitchen_helper2')) {
    function print_order_kitchen_helper2($printer_name = '', $print_data = '', $user_data, $void = false,$printer_setting=array())
    {
      $ci=&get_instance(); 
      $jml = 0;
        // $min_80=$ci->config->item("72_80");
      $GLOBALS['printer_setting']=$printer_setting;
        $printer_name=convert_printer_name($printer_name);
        if (function_exists('printer_open')) {
            // set up
            $var_max_char_length = ($printer_setting->description==48 ? 20 : 30);
            $yPos                = 0;

            $handle = printer_open($printer_name);
            if($handle==false)return false;
            printer_set_option($handle, PRINTER_MODE, "RAW"); // cut mode
            printer_start_doc($handle,"Start Doc");
            printer_start_page($handle);

            $font = printer_create_font("Courier New", 25, 15, PRINTER_FW_BOLD, false, false, false, 0);
            printer_select_font($handle, $font);
            if(isset($print_data['setting']['printer_logo']) && (!empty($printer_setting) && $printer_setting->default == 1) ){
              $path_logo=FCPATH.$print_data['setting']['printer_logo'];
              if(file_exists($path_logo)){
                printer_draw_bmp($handle, $path_logo, ($printer_setting->description==48 ? 35 : ($printer_setting->description==72 ? 75 : 130)), 50,300,150);
                $yPos+=200;
              }          
            }
            $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
            printer_select_pen($handle, $pen);
            $void_text = "";
            if($void){
                $void_text = "VOID";
                $i= 0;
                while ( $i <= 180) {
                    $void_text.= "VOID"; 
                    $i += strlen($void_text); 
                }
                printer_draw_text($handle,  $void_text, 0, $yPos);
                $yPos = $yPos + 50;

            }      

            // if(isset($print_data['outlet_data'])){
            //     _write_text_center_align($handle, $print_data['outlet_data'], $yPos, ($printer_setting->description==48 ? 180 : ($printer_setting->description==72 ? 215 : 270)), TRUE);

            // }

            _write_text_center_align($handle, "DAFTAR PESANAN", $yPos, ($printer_setting->description==48 ? 180 : ($printer_setting->description==72 ? 215 : 270)), TRUE);


            $yPos = $yPos + 35;
            printer_draw_line($handle, 0, $yPos, ($printer_setting->description==48 ? 400 : 600), $yPos);
            $yPos = $yPos + 5;
            printer_draw_line($handle, 0, $yPos, ($printer_setting->description==48 ? 400 : 600), $yPos);

      // WRITE MEJA
            if(isset($print_data['table_data'])){
            $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
            printer_select_pen($handle, $pen);
            $font2 = printer_create_font("Courier New", 30, 15, PRINTER_FW_ULTRABOLD, false, false, false, 0);
            printer_select_font($handle, $font2);
               $yPos = $yPos + 30;
               printer_draw_text($handle, "NO Meja", 0, $yPos);
               printer_draw_text($handle,  ":", 110, $yPos);
               printer_draw_text($handle,  $print_data['table_data']->table_name, 125, $yPos);
               // printer_draw_text($handle, $print_data['table_data']->table_name, 120, $yPos);
            }
            $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
            printer_select_pen($handle, $pen);
            $font = printer_create_font("Courier New", 20, 10, PRINTER_FW_NORMAL, false, false, false, 0);
            printer_select_font($handle, $font);
      
      // WRITE KASIR
            $yPos = $yPos + 27;
            printer_draw_text($handle, "Jam Order", 0, $yPos);
            printer_draw_text($handle, ":", 110, $yPos);
            printer_draw_text($handle,  date("Y/m/d H:i:s"), 125, $yPos);
            //printer_draw_text($handle,  $user_data->name, 120, $yPos);
            $yPos = $yPos + 27;
            printer_draw_text($handle, "Table Guard", 0, $yPos);
            printer_draw_text($handle, $user_data->name, 125, $yPos);
            
            if(isset($print_data['outlet_data'])){
                $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
                printer_select_pen($handle, $pen);
                $font2 = printer_create_font("Courier New", 25, 15, PRINTER_FW_ULTRABOLD, false, false, false, 0);
                printer_select_font($handle, $font2);
                $yPos = $yPos + 27;
                printer_draw_text($handle, $print_data['outlet_data']." : ", 0,$yPos);

            }
            $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
            printer_select_pen($handle, $pen);
            $font = printer_create_font("Courier New", 20, 10, PRINTER_FW_NORMAL, false, false, false, 0);
            printer_select_font($handle, $font);
            if($void){
             $yPos = $yPos + 20;
            printer_draw_text($handle, "Alasan", 0, $yPos);
            printer_draw_text($handle, ":", 105, $yPos);
            printer_draw_text($handle,  $print_data['note'], 120, $yPos);
            }
      // WRITE CUSTOMER
           if(isset($print_data['customer_data'])){
               $yPos = $yPos + 50;
               printer_draw_text($handle, "Nama", 0, $yPos);
               printer_draw_text($handle, ":", 105, $yPos);
               printer_draw_text($handle, $print_data['customer_data'], 120, $yPos);
           }

            // Header
            $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
            printer_select_pen($handle, $pen);
            
            $font2 = printer_create_font("Courier New", 30, 15, PRINTER_FW_ULTRABOLD, false, false, false, 0);
            printer_select_font($handle, $font2);
            // $yPos = $yPos + 50;
            // printer_draw_line($handle, 0, $yPos, ($printer_setting->description==48 ? 400 : 600), $yPos);
            // $yPos = $yPos + 25;
            // printer_draw_text($handle, "PESANAN", 0, $yPos);
            // printer_draw_text($handle, "QTY", ($printer_setting->description==48 ? 330 : ($printer_setting->description==72 ? 445 : 500)), $yPos);
            // $yPos = $yPos + 30;
            // printer_draw_line($handle, 0, $yPos, ($printer_setting->description==48 ? 400 : 600), $yPos);

            // body
            foreach ($print_data['order_list'] as $order) {
                $yPos = $yPos + 35;
                $yPos = _write_order_kitchen2($handle, $order, $yPos, $var_max_char_length,2,$printer_setting);
                $jml += $order->quantity;
                // $yPos = $yPos + 15;
                // // printer_draw_line($handle, 0, $yPos, ($printer_setting->description==48 ? 400 : 600), $yPos);
            }

            $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
            printer_select_pen($handle, $pen);
            $font = printer_create_font("Courier New", 20, 10, PRINTER_FW_NORMAL, false, false, false, 0);
            printer_select_font($handle, $font);
            $yPos = $yPos + 30;
            printer_draw_line($handle, 0, $yPos, ($printer_setting->description==48 ? 400 : 600), $yPos);
            $yPos = $yPos + 10;
            printer_draw_text($handle, "Total Nem", 0, $yPos);
            printer_draw_text($handle, ":", 110, $yPos);
            printer_draw_text($handle, $jml, ($printer_setting->description==48 ? 350 : ($printer_setting->description==72 ? 455 : 510)), $yPos);

            $yPos = $yPos + 20;
            printer_draw_line($handle, 0, $yPos, ($printer_setting->description==48 ? 400 : 600), $yPos);
            $yPos = $yPos + 10;
            printer_draw_line($handle, 0, $yPos, ($printer_setting->description==48 ? 400 : 600), $yPos);

            // $yPos = $yPos + 25;
            // _write_price($handle, date("Y/m/d H:i:s"), $yPos, ($printer_setting->description==48 ? 370 : ($printer_setting->description==72 ? 365 : 420)));

            if($void){
                $yPos = $yPos + 25;
                printer_draw_text($handle,  $void_text, 0, $yPos);
            }  

            printer_delete_font($font);
            printer_end_page($handle);
            printer_end_doc($handle);
            printer_close($handle);
        }
    }
}

if (! function_exists('_write_order_kitchen2')) {
    function _write_order_kitchen2($handle, $order, $yPos, $length, $explode_limit= 2,$printer_setting=array())
    { 
        $ci=&get_instance(); 

        // $min_80=$ci->config->item("72_80");
        if (is_object($order)) {
            if (! empty($order->menu_name)) {
                $string     = $order->menu_name;
                $orderCount = $order->quantity;
            }
            else {
                $string     = $order;
                $orderCount = "";
            }

        }
        else {
            $string     = $order;
            $orderCount = '';
        }


        $string = trim($string);
        
        if (strlen($string) > $length) {
            $string = wordwrap($string, $length);
            $string = explode("\n", $string, $explode_limit);
            $count  = 1;
            foreach ($string as $str) {
                if ($count == 1) {
                    printer_draw_text($handle, $orderCount, 0, $yPos);
                    printer_draw_text($handle, $str, ($printer_setting->description == 48 ? 50 : 80), $yPos);

                    // printer_draw_text($handle, $str, 0, $yPos);
                    // printer_draw_text($handle, $orderCount, ($printer_setting->description==48 ? 350 : ($printer_setting->description==72 ? 455 : 510)), $yPos);
                }
                else {
                    printer_draw_text($handle, '', 0, $yPos);
                    printer_draw_text($handle, $str, ($printer_setting->description == 48 ? 50 : 80), $yPos);

                    // printer_draw_text($handle, $str, 0, $yPos);
                    // printer_draw_text($handle, '', ($printer_setting->description==48 ? 350 : ($printer_setting->description==72 ? 455 : 510)), $yPos);
                }

                
                $count++;
                $yPos = $yPos + 20;
            }
        }
        else {
            printer_draw_text($handle, $orderCount, 0, $yPos);
            printer_draw_text($handle, $string, ($printer_setting->description == 48 ? 50 : 80), $yPos);
            $yPos = $yPos + 20;
            
        }

        if (isset($order->dinein_takeaway)) {
          $yPos = $yPos + 10;
          $yPos = _write_order_kitchen2($handle, ($order->is_delivery==1 ? 'Tipe : Delivery' : ($order->is_take_away==1 || $order->dinein_takeaway==1 ? "Tipe : Takeaway" : 'Tipe : Dine In' )), $yPos, $length,2,$printer_setting);
        }
        if (! empty($order->options)) {
            foreach ($order->options as $odr) {
                $yPos = $yPos + 10;
                $yPos = _write_order_kitchen2($handle, ' (' . $odr->option_name .' - '. $odr->option_value_name . ') ', $yPos, $length,2,$printer_setting);
            }
        }

        if (! empty($order->side_dishes)) {
            foreach ($order->side_dishes as $sdh) {
                $yPos = $yPos + 10;
                $yPos = _write_order_kitchen2($handle, '- '.$sdh->name, $yPos, $length,2,$printer_setting);
            }
        }

        if(!empty($order->note)){
                $yPos = $yPos + 10;
                $yPos = _write_order_kitchen2($handle, 'notes: '.$order->note, $yPos, $length, 10,$printer_setting);            
        }


        return $yPos;
    }
}

if (! function_exists('print_kontra_bon')) {
  function print_kontra_bon($printer_name = '', $print_data = array()) {
    $printer_name=convert_printer_name($printer_name);
    if (function_exists('printer_open')) {
      // set up
      $yPos                = 20;

      $handle = printer_open($printer_name);
     
      // $handle=true;
      if($handle==false)return false;
      printer_set_option($handle, PRINTER_MODE, "RAW"); // cut mode
      printer_start_doc($handle,"Start Doc");
      printer_start_page($handle);
      $font = printer_create_font("draft", 11, 9, PRINTER_FW_BOLD, false, false, false, 0);
      printer_select_font($handle, $font);
      $max_row = 25;
      $max_row_last = 15;
      printer_draw_text($handle, "FAKTUR PEMBAYARAN KONTRA BON", 400, $yPos);      
      $yPos += 15;
      $yPos = $yPos + 12;
      printer_draw_text($handle, "Purchase Ref", 300, $yPos);
      printer_draw_text($handle, "No Pembayaran", 650, $yPos);
      printer_draw_text($handle, $print_data['po_receive']->payment_no, 800, $yPos);
      $yPos = $yPos + 12;
      printer_draw_text($handle, "Vendor", 300, $yPos);
      printer_draw_text($handle, $print_data['supplier']->supplier_id." - ".$print_data['supplier']->name, 450, $yPos);
      printer_draw_text($handle, "Rcpt Date", 650, $yPos);
      printer_draw_text($handle, date("d/m/Y",strtotime($print_data['payment_date'])), 800, $yPos);
      $yPos = $yPos + 15;

      printer_draw_line($handle, 100, $yPos, 950, $yPos);
      $yPos = $yPos + 5;
      
      printer_draw_line($handle, 100, $yPos-5, 100, $yPos+15);
      printer_draw_text($handle, "No.", 115, $yPos);
      printer_draw_line($handle, 145, $yPos-5, 145, $yPos+15);
      
      printer_draw_text($handle, "Uraian", 150, $yPos);
      printer_draw_line($handle, 780, $yPos-5, 780, $yPos+15);
      
      printer_draw_text($handle, "Sub Total", 805, $yPos);
      printer_draw_line($handle, 950, $yPos-5, 950, $yPos+15);
      $yPos+=15;
      printer_draw_line($handle, 100, $yPos, 950, $yPos);
      $yPos+=5;

      $total = 0;
      if ($print_data['is_history']) {
        $counter = 1;
        foreach ($print_data['detail'] as $key) {
          $total += $key->amount;
          printer_draw_text($handle, $counter, 115, $yPos);
          printer_draw_text($handle, 'Pembayaran Tanggal '.date("d/m/Y",strtotime($key->payment_date)), 150, $yPos);
          printer_draw_text($handle, number_format($key->amount, 2), 805, $yPos);
          $yPos+=10;
          $counter++;
        }
      } else {
        $total += $print_data['amount'];
        printer_draw_text($handle, 1, 115, $yPos);
        printer_draw_text($handle, 'Pembayaran Tanggal '.date("d/m/Y",strtotime($print_data['payment_date'])), 150, $yPos);
        printer_draw_text($handle, number_format($print_data['amount'], 2), 805, $yPos);
        $yPos+=10;
      }
      $yPos+=10;
      printer_draw_line($handle, 100, $yPos, 950, $yPos);
      $yPos+=5;

      printer_draw_text($handle, "GRAND TOTAL", 600, $yPos);
      printer_draw_text($handle, number_format($total, 2), 805, $yPos);
      $yPos+=10;

      $remaining = $print_data['po_receive']->total - ($print_data['has_paid']->total + $total);
      printer_draw_text($handle, "SISA HUTANG", 600, $yPos);
      printer_draw_text($handle, number_format(($remaining < 0) ? 0 : $remaining, 2), 805, $yPos);
      $yPos+=10;

      $status = "";
      if (($print_data['has_paid']->total + $total) < $print_data['po_receive']->total) {
        $status = "BELUM LUNAS";
      } else {
        $status = "LUNAS";
      }
      printer_draw_text($handle, "STATUS", 600, $yPos);
      printer_draw_text($handle, $status, 805, $yPos);
      $yPos+=30;

      printer_draw_text($handle, "Pembuat", 170, $yPos);
      printer_draw_text($handle, "Pemeriksa", 315, $yPos);
      printer_draw_text($handle, "Disetujui", 470, $yPos);
      $yPos+=45;
      printer_draw_line($handle, 150, $yPos, 250, $yPos);
      printer_draw_line($handle, 300, $yPos, 400, $yPos);
      printer_draw_line($handle, 450, $yPos, 550, $yPos);

    }

    // Header
    $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
    printer_select_pen($handle, $pen);

    printer_delete_font($font);
    printer_end_page($handle);
    printer_end_doc($handle);
    printer_close($handle);
  }
}

if (! function_exists('print_retur_po')) {
  function print_retur_po($printer_name='',$print_data=array()){
    $printer_name=convert_printer_name($printer_name);
    if (function_exists('printer_open')) {
      // set up
      $yPos                = 20;

      $handle = printer_open($printer_name);
     
      // $handle=true;
      if($handle==false)return false;
      printer_set_option($handle, PRINTER_MODE, "RAW"); // cut mode
      printer_start_doc($handle,"Start Doc");
      printer_start_page($handle);
      $font = printer_create_font("draft", 10, 8, PRINTER_FW_BOLD, false, false, false, 0);
      printer_select_font($handle, $font);
      $max_row=25;
      $max_row_last=15;
      $split=ceil(sizeof($print_data['detail'])/$max_row);
      $paging=array();
      for($x=0;$x<$split;$x++)
      {
        if(isset($is_change)){
          $from=(isset($paging[$x-1]['to']) ? $paging[$x-1]['to'] : $paging[$x]['to']);
          $to=$from+$max_row_last;          
        }else{
          $from=$x*$max_row;
          $to=$from+$max_row;          
        }
        if($from<0)$from=0;
        if($to>sizeof($print_data['detail']))$to=sizeof($print_data['detail']);
        $paging[$x]=array(
          "from"=>$from,
          "to" =>$to
        );
        if(($x+1)==$split && !isset($is_change) && (sizeof($print_data['detail'])-(($split-1)*$max_row))>$max_row_last){
          $paging[$x]['to']=(($x+1)*$max_row);
          if($paging[$x]['to']>sizeof($print_data['detail']))$paging[$x]['to']=sizeof($print_data['detail']);
          $split++;
          $is_change=1;
        }        
      }

      $total=0;
      for($x=0;$x<$split;$x++)
      {
        $counter=1;
        $yPos=($x*385)+20;
        printer_draw_text($handle, "FAKTUR PENGEMBALIAN BARANG", 430, $yPos);
        $yPos+=15;
        if(isset($print_data['setting']['printer_logo'])){
          $path_logo=FCPATH.$print_data['setting']['printer_logo'];
          if(file_exists($path_logo)){
            printer_draw_bmp($handle, $path_logo,120,(($x==0 ? 0 : $yPos-40)+20),180,56);
          }          
        }
        $yPos = $yPos + 12;
        printer_draw_text($handle, "Purchase Ref", 350, $yPos);
        printer_draw_text($handle, "No Dokumen", 700, $yPos);
        printer_draw_text($handle, $print_data['retur']->number, 800, $yPos);
        $yPos = $yPos + 12;
        printer_draw_text($handle, "Vendor", 350, $yPos);
        printer_draw_text($handle, $print_data['purchase_order']->supplier_id." - ".$print_data['purchase_order']->name, 450, $yPos);
        printer_draw_text($handle, "Rcpt Date", 700, $yPos);
        printer_draw_text($handle, date("d/m/Y",strtotime($print_data['retur']->retur_date)), 800, $yPos);
        $yPos = $yPos + 15;

        printer_draw_line($handle, 100, $yPos, 950, $yPos);
        $yPos = $yPos + 5;
        
        printer_draw_line($handle, 100, $yPos-5, 100, $yPos+15);
        printer_draw_text($handle, "No.", 115, $yPos);
        printer_draw_line($handle, 145, $yPos-5, 145, $yPos+15);
        
        printer_draw_text($handle, "Kode Barang", 150, $yPos);
        printer_draw_line($handle, 250, $yPos-5, 250, $yPos+15);
        
        printer_draw_text($handle, "Nama Barang", 255, $yPos);
        printer_draw_line($handle, 500, $yPos-5, 500, $yPos+15);
        
        printer_draw_text($handle, "Harga", 505, $yPos);
        printer_draw_line($handle, 600, $yPos-5, 600, $yPos+15);
        
        printer_draw_text($handle, "Jumlah", 605, $yPos);
        printer_draw_line($handle, 680, $yPos-5, 680, $yPos+15);
        
        printer_draw_text($handle, "Satuan", 705, $yPos);
        printer_draw_line($handle, 780, $yPos-5, 780, $yPos+15);
        
        printer_draw_text($handle, "Sub Total", 805, $yPos);
        printer_draw_line($handle, 950, $yPos-5, 950, $yPos+15);
        $yPos+=15;
        printer_draw_line($handle, 100, $yPos, 950, $yPos);
        $yPos+=5;
        foreach($print_data['detail'] as $i){
          if($counter>$paging[$x]['from'] && $counter<=$paging[$x]['to']){
            $subtotal=$i->retur_quantity*$i->price;
            $total+=$subtotal;
            printer_draw_text($handle, $counter, 115, $yPos);
            printer_draw_text($handle, $i->inventory_id, 150, $yPos);
            printer_draw_text($handle, $i->name, 255, $yPos);
            printer_draw_text($handle, number_format($i->price,2), 505, $yPos);
            printer_draw_text($handle, $i->retur_quantity, 605, $yPos);
            printer_draw_text($handle, $i->code, 705, $yPos);
            printer_draw_text($handle, number_format($subtotal,2), 805, $yPos);
            $yPos+=10;
          }
          $counter++;
        }
        printer_draw_line($handle, 100, $yPos, 950, $yPos);
        $yPos+=5;
        if($x+1==$split){
          printer_draw_text($handle, "GRAND TOTAL", 690, $yPos);
          printer_draw_text($handle, number_format($print_data['retur']->total,2), 805, $yPos);
          $yPos+=30;
          
          printer_draw_text($handle, "Pembuat", 170, $yPos);
          printer_draw_text($handle, "Pemeriksa", 315, $yPos);
          printer_draw_text($handle, "Disetujui", 470, $yPos);
          $yPos+=45;
          printer_draw_line($handle, 150, $yPos, 250, $yPos);
          printer_draw_line($handle, 300, $yPos, 400, $yPos);
          printer_draw_line($handle, 450, $yPos, 550, $yPos);
        }else{
          printer_draw_text($handle, "Bersambung...", 705, $yPos);
          $yPos+=80;
        }
      }
      // Header
      $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
      printer_select_pen($handle, $pen);

      printer_delete_font($font);
      printer_end_page($handle);
      printer_end_doc($handle);
      printer_close($handle);
    }
  }
}

if (! function_exists('print_report_product')) {
  function print_report_product($printer_name = '', $print_data = '', $user_data,$printer_setting){
    $printer_name=convert_printer_name($printer_name); 
      if (function_exists('printer_open')) {
              // set up
              $var_max_char_length = 25;
              $yPos                = 0;

              $handle = printer_open($printer_name);
              if($handle==false)return false;
              printer_set_option($handle, PRINTER_MODE, "RAW"); // cut mode
              printer_start_doc($handle,"Start Doc");
              printer_start_page($handle);

              $font = printer_create_font("Courier New", 30, 20, PRINTER_FW_BOLD, false, false, false, 0);
              printer_select_font($handle, $font); 

              if(isset($print_data['setting']['printer_logo'])){
                $path_logo=FCPATH.$print_data['setting']['printer_logo'];
                if(file_exists($path_logo)){
                  @printer_draw_bmp($handle, $path_logo, 35, 50,300,150);
                  $yPos+=200;
                }          
              }
              // echo "<pre>";
              // print_r($print_data);
              // die(); 
              _write_text_center($handle,"Laporan Produk", $yPos, 0, TRUE,$printer_setting->description);
              $yPos = $yPos + 120;

              $font = printer_create_font("Courier New", 25, 15, PRINTER_FW_BOLD, false, false, false, 0);
              printer_select_font($handle, $font); 

              printer_draw_text($handle, "Tanggal Open", 0, $yPos); 
              printer_draw_text($handle, " : ".date("d/m/Y",strtotime($print_data['oc_cashier']->open_at)),  ($printer_setting->description == 48 ? 190 : 250), $yPos);
              $yPos = $yPos + 40;

              printer_draw_text($handle, "Waktu Open", 0, $yPos); 
              printer_draw_text($handle, " : ".date("H:i",strtotime($print_data['oc_cashier']->open_at)),  ($printer_setting->description == 48 ? 190 : 250), $yPos);
              $yPos = $yPos + 40;

              printer_draw_text($handle, "Tanggal Close", 0, $yPos); 
              printer_draw_text($handle, " : ".date("d/m/Y",strtotime($print_data['oc_cashier']->open_at)),  ($printer_setting->description == 48 ? 190 : 250), $yPos);
              $yPos = $yPos + 40;

              printer_draw_text($handle, "Waktu Close", 0, $yPos); 
              printer_draw_text($handle, " : ".date("H:i",strtotime($print_data['oc_cashier']->open_at)),  ($printer_setting->description == 48 ? 190 : 250), $yPos);
              $yPos = $yPos + 40;

              printer_draw_text($handle, "Open by", 0, $yPos); 
              printer_draw_text($handle, " : ".$print_data['oc_cashier']->open_by_name, ($printer_setting->description == 48 ? 190 : 250), $yPos);
              $yPos = $yPos + 40;

              printer_draw_text($handle, "Close by", 0, $yPos); 
              printer_draw_text($handle, " : ".$print_data['oc_cashier']->close_by_name,($printer_setting->description == 48 ? 190 : 250), $yPos);
              $yPos = $yPos + 80;


              printer_draw_text($handle, "No", 6, $yPos,0);
              printer_draw_text($handle, "Nama Menu",  ($printer_setting->description == 48 ? 50 : 80), $yPos,0); 
              printer_draw_text($handle, "Jml", ($printer_setting->description == 48 ? 300 : 470), $yPos,0); 
               $yPos = $yPos + 25;
              printer_draw_line($handle, 0, $yPos, 950, $yPos);
              $yPos = $yPos + 50;

              $no = 1;
              foreach ($print_data['report_products'] as $key => $row) {  
                printer_draw_text($handle, $no, 6, $yPos,0); 
                $max_length = 13;
                
                if($printer_setting->font_size==2) {
                  $max_length = 10;
                }
                
                $string = trim( $row->menu_name);
                if (strlen($string) > $max_length) {
                  $string = wordwrap($string, $max_length);
                  $string = explode("\n", $string, 3); 
                  foreach ($string as $str) { 
                      printer_draw_text($handle, $str,  ($printer_setting->description == 48 ? 50 : 80), $yPos); 
                  }
                }else { 
                  printer_draw_text($handle, $string,  ($printer_setting->description == 48 ? 50 : 80), $yPos);
                }

                printer_draw_text($handle, $row->jumlah, ($printer_setting->description == 48 ? 315 : 475), $yPos);
                $yPos = $yPos + 25;
                $no++;   
              }   

              $yPos = $yPos + 90;
              // printer_draw_text($handle, "KASIR", ($printer_setting->description == 48 ? 20 : 130), $yPos);
              // printer_draw_text($handle, "SUPERVISOR", ($printer_setting->description == 48 ? 175 : 300), $yPos);
              // $yPos = $yPos + 145;
              // printer_draw_text($handle, "(.......)", ($printer_setting->description == 48 ? 15 : 110), $yPos);
              // printer_draw_text($handle, "(..........)", ($printer_setting->description == 48 ? 170 : 290), $yPos);
              // $yPos = $yPos + 55;
              
              // Header
              $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
              printer_select_pen($handle, $pen);

              printer_delete_font($font);
              printer_end_page($handle);
              printer_end_doc($handle);
              printer_close($handle); 
          }
  }
}



if (! function_exists('print_report_pettycash')) {
  function print_report_pettycash($printer_name = '', $print_data = '', $user_data,$printer_setting){
    $printer_name=convert_printer_name($printer_name); 
      if (function_exists('printer_open')) {
              // set up
              $var_max_char_length = 25;
              $yPos                = 0;

              $handle = printer_open($printer_name);
              if($handle==false)return false;
              printer_set_option($handle, PRINTER_MODE, "RAW"); // cut mode
              printer_start_doc($handle,"Start Doc");
              printer_start_page($handle);

              $font = printer_create_font("Courier New", 30, 20, PRINTER_FW_BOLD, false, false, false, 0);
              printer_select_font($handle, $font); 

              if(isset($print_data['setting']['printer_logo'])){
                $path_logo=FCPATH.$print_data['setting']['printer_logo'];
                if(file_exists($path_logo)){
                  @printer_draw_bmp($handle, $path_logo, 35, 50,300,150);
                  $yPos+=200;
                }          
              }
              // echo "<pre>";
              // print_r($print_data);
              // die(); 
              _write_text_center($handle,"Laporan Kas Kecil", $yPos, 0, TRUE,$printer_setting->description);
              $yPos = $yPos + 120;

              $font = printer_create_font("Courier New", 25, 15, PRINTER_FW_BOLD, false, false, false, 0);
              printer_select_font($handle, $font); 

              printer_draw_text($handle, "Tanggal Open", 0, $yPos); 
              printer_draw_text($handle, " : ".date("d/m/Y",strtotime($print_data['oc_cashier']->open_at)),  ($printer_setting->description == 48 ? 190 : 250), $yPos);
              $yPos = $yPos + 40;

              printer_draw_text($handle, "Waktu Open", 0, $yPos); 
              printer_draw_text($handle, " : ".date("H:i",strtotime($print_data['oc_cashier']->open_at)),  ($printer_setting->description == 48 ? 190 : 250), $yPos);
              $yPos = $yPos + 40;

              printer_draw_text($handle, "Tanggal Close", 0, $yPos); 
              printer_draw_text($handle, " : ".date("d/m/Y",strtotime($print_data['oc_cashier']->open_at)),  ($printer_setting->description == 48 ? 190 : 250), $yPos);
              $yPos = $yPos + 40;

              printer_draw_text($handle, "Waktu Close", 0, $yPos); 
              printer_draw_text($handle, " : ".date("H:i",strtotime($print_data['oc_cashier']->open_at)),  ($printer_setting->description == 48 ? 190 : 250), $yPos);
              $yPos = $yPos + 40;

              printer_draw_text($handle, "Open by", 0, $yPos); 
              printer_draw_text($handle, " : ".$print_data['oc_cashier']->open_by_name, ($printer_setting->description == 48 ? 190 : 250), $yPos);
              $yPos = $yPos + 40;

              printer_draw_text($handle, "Close by", 0, $yPos); 
              printer_draw_text($handle, " : ".$print_data['oc_cashier']->close_by_name,($printer_setting->description == 48 ? 190 : 250), $yPos);
              $yPos = $yPos + 120; 

              printer_draw_text($handle, "No", 6, $yPos,0);
              printer_draw_text($handle, "Keterangan",  ($printer_setting->description == 48 ? 50 : 60), $yPos,0); 
              printer_draw_text($handle, "Total", ($printer_setting->description == 48 ? 240 : 350), $yPos,0); 
               $yPos = $yPos + 25;
              printer_draw_line($handle, 0, $yPos, 950, $yPos);
              $yPos = $yPos + 50;

              $no = 1;
              foreach ($print_data['report_pettycashs'] as $key => $row) {  
                printer_draw_text($handle, $no, 6, $yPos,0); 
                $max_length = ($printer_setting->description == 48 ? 10 : 18);
                
                if($printer_setting->font_size==2) {
                  $max_length = 10;
                }
                
                $string = trim( $row->description);
                $yPosTotal = 0;
                if (strlen($string) > $max_length) {
                  $string = wordwrap($string, $max_length);
                  $string = explode("\n", $string, 20); 
                  foreach ($string as $str) { 
                      printer_draw_text($handle, $str,  ($printer_setting->description == 48 ? 50 : 60), $yPos);
                      $yPos = $yPos + 25; 
                      $yPosTotal+=25;
                  }
                }else { 
                  printer_draw_text($handle, $string,  ($printer_setting->description == 48 ? 50 : 60), $yPos); 
                }
                 $yPos = $yPos - $yPosTotal;
                printer_draw_text($handle, convert_rupiah($row->amount), ($printer_setting->description == 48 ? 240 : 350), $yPos); 
                $yPos = $yPos + 25;
                 $yPos = $yPos + $yPosTotal;
                $no++;   
              }   

              $yPos = $yPos + 120; 
              // Header
              $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
              printer_select_pen($handle, $pen);

              printer_delete_font($font);
              printer_end_page($handle);
              printer_end_doc($handle);
              printer_close($handle); 
          }
  }
}


if (! function_exists('print_report_stock')) {
  function print_report_stock($printer_name = '', $print_data = '', $user_data,$printer_setting){
    $printer_name=convert_printer_name($printer_name); 
      if (function_exists('printer_open')) {
              // set up
              $var_max_char_length = 25;
              $yPos                = 0;

              $handle = printer_open($printer_name);
              if($handle==false)return false;
              printer_set_option($handle, PRINTER_MODE, "RAW"); // cut mode
              printer_start_doc($handle,"Start Doc");
              printer_start_page($handle);

              $font = printer_create_font("Courier New", 30, 20, PRINTER_FW_BOLD, false, false, false, 0);
              printer_select_font($handle, $font); 

              if(isset($print_data['setting']['printer_logo'])){
                $path_logo=FCPATH.$print_data['setting']['printer_logo'];
                if(file_exists($path_logo)){
                  @printer_draw_bmp($handle, $path_logo, 35, 50,300,150);
                  $yPos+=200;
                }          
              }
              // echo "<pre>";
              // print_r($print_data);
              // die(); 
              _write_text_center($handle,"Laporan Stok", $yPos, 0, TRUE,$printer_setting->description);
              $yPos = $yPos + 120;

              $font = printer_create_font("Courier New", 25, 15, PRINTER_FW_BOLD, false, false, false, 0);
              printer_select_font($handle, $font); 

              printer_draw_text($handle, "Tanggal Open", 0, $yPos); 
              printer_draw_text($handle, " : ".date("d/m/Y",strtotime($print_data['oc_cashier']->open_at)),  ($printer_setting->description == 48 ? 190 : 250), $yPos);
              $yPos = $yPos + 40;

              printer_draw_text($handle, "Waktu Open", 0, $yPos); 
              printer_draw_text($handle, " : ".date("H:i",strtotime($print_data['oc_cashier']->open_at)),  ($printer_setting->description == 48 ? 190 : 250), $yPos);
              $yPos = $yPos + 40;

              printer_draw_text($handle, "Tanggal Close", 0, $yPos); 
              printer_draw_text($handle, " : ".date("d/m/Y",strtotime($print_data['oc_cashier']->open_at)),  ($printer_setting->description == 48 ? 190 : 250), $yPos);
              $yPos = $yPos + 40;

              printer_draw_text($handle, "Waktu Close", 0, $yPos); 
              printer_draw_text($handle, " : ".date("H:i",strtotime($print_data['oc_cashier']->open_at)),  ($printer_setting->description == 48 ? 190 : 250), $yPos);
              $yPos = $yPos + 40;

              printer_draw_text($handle, "Open by", 0, $yPos); 
              printer_draw_text($handle, " : ".$print_data['oc_cashier']->open_by_name, ($printer_setting->description == 48 ? 190 : 250), $yPos);
              $yPos = $yPos + 40;

              printer_draw_text($handle, "Close by", 0, $yPos); 
              printer_draw_text($handle, " : ".$print_data['oc_cashier']->close_by_name,($printer_setting->description == 48 ? 190 : 250), $yPos);
              $yPos = $yPos + 120; 

              printer_draw_text($handle, "No", 6, $yPos,0);
              printer_draw_text($handle, "Nama ",  ($printer_setting->description == 48 ? 50 : 50), $yPos,0);
              printer_draw_text($handle, "S",  ($printer_setting->description == 48 ? 220 : 320), $yPos,0); 
              printer_draw_text($handle, "In",  ($printer_setting->description == 48 ? 270 : 350), $yPos,0); 
              printer_draw_text($handle, "Out",  ($printer_setting->description == 48 ? 320 : 400), $yPos,0);  
              printer_draw_text($handle, "L", ($printer_setting->description == 48 ? 380 : 460), $yPos,0); 

               $yPos = $yPos + 25;
              printer_draw_line($handle, 0, $yPos, 950, $yPos);
              $yPos = $yPos + 50;

              $no = 1;
              foreach ($print_data['report_stocks'] as $key => $row) {  
                printer_draw_text($handle, $no, 6, $yPos,0); 
                $max_length = ($printer_setting->description == 48 ? 6 : 16);
                
                if($printer_setting->font_size==2) {
                  $max_length = 10;
                }
                
                $string = trim( $row->name);
                $yPosTotal = 0;
                if (strlen($string) > $max_length) {
                  $string = wordwrap($string, $max_length);
                  $string = explode("\n", $string, 20); 
                  foreach ($string as $str) { 
                      printer_draw_text($handle, $str,  ($printer_setting->description == 48 ? 50 : 60), $yPos);
                      $yPos = $yPos + 25; 
                      $yPosTotal+=25;
                  }
                }else { 
                  printer_draw_text($handle, $string,  ($printer_setting->description == 48 ? 50 : 60), $yPos); 
                }
                $yPos = $yPos - $yPosTotal;
                printer_draw_text($handle, $row->beginning_stock, ($printer_setting->description == 48 ? 220 : 320), $yPos);
                printer_draw_text($handle, $row->incoming_stock, ($printer_setting->description == 48 ? 270 : 350), $yPos);
                printer_draw_text($handle, $row->used_stock, ($printer_setting->description == 48 ? 320 : 400), $yPos);
                printer_draw_text($handle, $row->last_stock, ($printer_setting->description == 48 ? 380 : 460), $yPos);
                $yPos = $yPos + 25;
                $yPos = $yPos + $yPosTotal;
                $no++;   
              }   

              $yPos = $yPos + 120; 
              // Header
              $pen = printer_create_pen(PRINTER_PEN_SOLID, 3, "000000");
              printer_select_pen($handle, $pen);

              printer_delete_font($font);
              printer_end_page($handle);
              printer_end_doc($handle);
              printer_close($handle); 
          }
  }
}