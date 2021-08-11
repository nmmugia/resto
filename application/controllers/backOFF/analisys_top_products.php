<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Analisys_Top_Products extends Admin_Controller
{
  public $path="admin/analisys_top_products/";
  public function __construct()
  {
    parent::__construct();
    $this->load->model("report_model","report");
    $this->load->helper(array('dompdf', 'file'));
  }
  public function hourly_sales_report()
  {
    $this->data['title']    = "Top Product & Analisis";
    $this->data['subtitle'] = "Hourly Sales Report"; 
    $search=array(
      "store_id"=>$this->data['setting']['store_id'],
      "date"=>date("Y-m-d")
    );
    $button="filter";
    if($this->input->server('REQUEST_METHOD') == 'POST'){
      $search=$this->input->post("search");
      $button=$this->input->post("button");
    }
    $this->data['lists']=$this->report->hourly_sales_report($search);
    $total_revenue=0;
    foreach($this->data['lists'] as $l){
      $total_revenue+=$l->revenue;
    }
    foreach($this->data['lists'] as $l){
      $l->percentage=$l->revenue/$total_revenue*100;
    }
    $this->data['search']=$search;
    $this->data['store']=$this->store_model->get_one("store",$search['store_id']);
    $this->data['detail_view']=$this->load->view($this->path."hourly_sales_report_pdf",$this->data,true);
    if($button=="export_pdf"){
      $data     = pdf_create($this->data['detail_view'], '', false, 'portrait');
      $filename = 'hourly_sales_report_'.date("Y-m-d").'.pdf';
      header("Content-type:application/pdf");
      header('Content-Disposition: attachment;filename="'.$filename.'"');
      echo $data;
    }
    $this->data['store_lists']=$this->store_model->get_all_store();
    $this->data['content'] .= $this->load->view($this->path.'hourly_sales_report', $this->data, true);
    $this->render('report');
  }
  public function sales_summary_report()
  {
    $this->data['title']    = "Top Product & Analisis";
    $this->data['subtitle'] = "Sales Summary Report"; 
    $search=array(
      "store_id"=>$this->data['setting']['store_id'],
      "date"=>date("Y-m-d")
    );
    $button="filter";
    if($this->input->server('REQUEST_METHOD') == 'POST'){
      $search=$this->input->post("search");
      $button=$this->input->post("button");
    }
    $this->data['lists']=$this->report->sales_summary_report($search);
    $total_gross_profit=0;
    foreach($this->data['lists'] as $l){
      $l->gross_profit=$l->sales-$l->cogs;
      $total_gross_profit+=$l->gross_profit;
    }
    foreach($this->data['lists'] as $l){
      $l->profit_margin=($l->gross_profit!=0 && $total_gross_profit!=0 ? $l->gross_profit/$total_gross_profit*100 : 0);
    }
    $this->data['search']=$search;
    $this->data['store']=$this->store_model->get_one("store",$search['store_id']);
    $this->data['detail_view']=$this->load->view($this->path."sales_summary_report_pdf",$this->data,true);
    if($button=="export_pdf"){
      $data     = pdf_create($this->data['detail_view'], '', false, 'portrait');
      $filename = 'sales_summary_report_'.date("Y-m-d").'.pdf';
      header("Content-type:application/pdf");
      header('Content-Disposition: attachment;filename="'.$filename.'"');
      echo $data;
    }
    $this->data['store_lists']=$this->store_model->get_all_store();
    $this->data['content'] .= $this->load->view($this->path.'sales_summary_report', $this->data, true);
    $this->render('report');
  } 
  public function sales_by_day_report()
  {
    $this->data['title']    = "Top Product & Analisis";
    $this->data['subtitle'] = "Sales By Day Report"; 
    $search=array(
      "store_id"=>$this->data['setting']['store_id'],
      "date"=>date("Y-m")
    );
    $button="filter";
    $image_data="";
    if($this->input->server('REQUEST_METHOD') == 'POST'){
      $search=$this->input->post("search");
      $button=$this->input->post("button");
      $image_data=$this->input->post("image_data");
    }
    $this->data['button']=$button;
    $this->data['image_data']=$image_data;
    $this->data['lists']=$this->report->sales_by_day_report($search);
    $this->data['search']=$search;
    $this->data['store']=$this->store_model->get_one("store",$search['store_id']);
    $this->data['detail_view']=$this->load->view($this->path."sales_by_day_report_pdf",$this->data,true);
    if($button=="export_pdf"){
      $data     = pdf_create($this->data['detail_view'], '', false, 'portrait');
      $filename = 'sales_by_day_report_'.date("Y-m-d").'.pdf';
      header("Content-type:application/pdf");
      header('Content-Disposition: attachment;filename="'.$filename.'"');
      echo $data;
    }
    $this->data['store_lists']=$this->store_model->get_all_store();
    $this->data['content'] .= $this->load->view($this->path.'sales_by_day_report', $this->data, true);
    $this->render('report');
  }
  public function sales_by_department_category_report()
  {
    $this->data['title']    = "Top Product & Analisis";
    $this->data['subtitle'] = "Sales By Department / Category Report"; 
    $search=array(
      "store_id"=>$this->data['setting']['store_id'],
      "from"=>date("Y-m-d"),
      "to"=>date("Y-m-d"),
      "type"=>"department"
    );
    $button="filter";
    $image_data="";
    if($this->input->server('REQUEST_METHOD') == 'POST'){
      $search=$this->input->post("search");
      $button=$this->input->post("button");
      $image_data=$this->input->post("image_data");
    }
    $this->data['button']=$button;
    $this->data['image_data']=$image_data;
    $this->data['lists']=$this->report->sales_by_department_category_report($search);
    $this->data['search']=$search;
    $this->data['store']=$this->store_model->get_one("store",$search['store_id']);
    $this->data['detail_view']=$this->load->view($this->path."sales_by_department_category_report_pdf",$this->data,true);
    if($button=="export_pdf"){
      $data     = pdf_create($this->data['detail_view'], '', false, 'portrait');
      $filename = 'sales_by_department_category_report_'.date("Y-m-d").'.pdf';
      header("Content-type:application/pdf");
      header('Content-Disposition: attachment;filename="'.$filename.'"');
      echo $data;
    }
    $this->data['store_lists']=$this->store_model->get_all_store();
    $this->data['content'] .= $this->load->view($this->path.'sales_by_department_category_report', $this->data, true);
    $this->render('report');
  }
  public function top_worst_product_report()
  {
    $this->data['title']    = "Top Product & Analisis";
    $this->data['subtitle'] = "Top / Worst Product Report"; 
    $search=array(
      "store_id"=>$this->data['setting']['store_id'],
      "from"=>date("Y-m-d"),
      "to"=>date("Y-m-d")
    );
    $limit=3;
    $button="filter";
    if($this->input->server('REQUEST_METHOD') == 'POST'){
      $search=$this->input->post("search");
      $button=$this->input->post("button");
    }
    $lists=$this->report->top_worst_product_report($search);
    $results=array(
      "bshp"=>array(),
      "bslp"=>array(),
      "lslp"=>array()
    );
    $counter=1;
    $temp=array();
    foreach($lists as $l){
      if($counter<=$limit){
        array_push($results['bshp'],$l);
      }else{
        array_push($temp,$l);
      }
      $counter++;
    }
    $temp2=array();
    $counter=1;
    foreach($temp as $l){
      if($counter<=$limit){
        array_push($results['bslp'],$l);
      }else{
        array_push($temp2,$l);
      }
      $counter++;
    }
    $temp2=array_reverse($temp2);
    $counter=1;
    foreach($temp2 as $l){
      if($counter<=$limit){
        array_push($results['lslp'],$l);
      }
      $counter++;
    }
    $this->data['results']=$results;
    $this->data['search']=$search;
    $this->data['store']=$this->store_model->get_one("store",$search['store_id']);
    $this->data['detail_view']=$this->load->view($this->path."top_worst_product_report_pdf",$this->data,true);
    if($button=="export_pdf"){
      $data     = pdf_create($this->data['detail_view'], '', false, 'portrait');
      $filename = 'top_worst_product_report_report_'.date("Y-m-d").'.pdf';
      header("Content-type:application/pdf");
      header('Content-Disposition: attachment;filename="'.$filename.'"');
      echo $data;
    }
    $this->data['store_lists']=$this->store_model->get_all_store();
    $this->data['content'] .= $this->load->view($this->path.'top_worst_product_report', $this->data, true);
    $this->render('report');
  }
  public function sales_by_waiter_report()
  {
    $this->data['title']    = "Top Product & Analisis";
    $this->data['subtitle'] = "Sales By Waiter Report"; 
    $search=array(
      "store_id"=>$this->data['setting']['store_id'],
      "from"=>date("Y-m-d"),
      "to"=>date("Y-m-d")
    );
    $limit=3;
    $button="filter";
    $image_data="";
    if($this->input->server('REQUEST_METHOD') == 'POST'){
      $search=$this->input->post("search");
      $button=$this->input->post("button");
      $image_data=$this->input->post("image_data");
    }
    $this->data['button']=$button;
    $this->data['image_data']=$image_data;
    $this->data['lists']=$this->report->sales_by_waiter_report($search);
    $this->data['search']=$search;
    $this->data['store']=$this->store_model->get_one("store",$search['store_id']);
    $this->data['detail_view']=$this->load->view($this->path."sales_by_waiter_report_pdf",$this->data,true);
    if($button=="export_pdf"){
      $data     = pdf_create($this->data['detail_view'], '', false, 'portrait');
      $filename = 'sales_by_waiter_report_'.date("Y-m-d").'.pdf';
      header("Content-type:application/pdf");
      header('Content-Disposition: attachment;filename="'.$filename.'"');
      echo $data;
    }
    $this->data['store_lists']=$this->store_model->get_all_store();
    $this->data['content'] .= $this->load->view($this->path.'sales_by_waiter_report', $this->data, true);
    $this->render('report');
  }
  public function inventory_used_report()
  {
    $search=array(
      "store_id"=>$this->data['setting']['store_id'],
      "from"=>date("Y-m-d"),
      "to"=>date("Y-m-d"),
      "inventory_id"=>""
    );
    $limit=3;
    $button="filter";
    if($this->input->server('REQUEST_METHOD') == 'POST'){
      $search=$this->input->post("search");
      $button=$this->input->post("button");
    }
    $search['is_print']=false;
    if($button=="export_pdf")$search['is_print']=true;
    $this->data['title']    = "Top Product & Analisis";
    $inventory=array();
    $this->data['search']=$search;
    $this->data['store']=$this->store_model->get_one("store",$search['store_id']);
    if($search['inventory_id']!=""){
      $this->load->model("inventory_model");
      $inventory=$this->inventory_model->get_one("inventory",$search['inventory_id']);
      $this->data['inventory']=$inventory;
      $this->data['subtitle'] = "Detail Inventory Used Report : ".ucfirst($inventory->name); 
      $this->data['lists']=$this->report->inventory_used_detail_report($search);
      $total_used=0;
      foreach($this->data['lists'] as $l){
        $total_used+=$l->total_used;
      }
      foreach($this->data['lists'] as $l){
        $l->percentage=$l->total_used/$total_used*100;
      }
      $this->data['detail_view']=$this->load->view($this->path."inventory_used_detail_report_pdf",$this->data,true);
      $filename = 'inventory_used_detail_report_'.date("Y-m-d").'.pdf';
    }else{
      $this->data['subtitle'] = "Inventory Used Report"; 
      $this->data['inventory']=$inventory;
      $this->data['lists']=$this->report->inventory_used_report($search);
      $this->data['detail_view']=$this->load->view($this->path."inventory_used_report_pdf",$this->data,true);
      $filename = 'inventory_used_report_'.date("Y-m-d").'.pdf';
    }
    if($button=="export_pdf"){
      $data     = pdf_create($this->data['detail_view'], '', false, 'portrait');
      header("Content-type:application/pdf");
      header('Content-Disposition: attachment;filename="'.$filename.'"');
      echo $data;
    }
    $this->data['store_lists']=$this->store_model->get_all_store();
    if($search['inventory_id']!=""){
      $this->data['content'] .= $this->load->view($this->path.'inventory_used_detail_report', $this->data, true);
    }else{
      $this->data['content'] .= $this->load->view($this->path.'inventory_used_report', $this->data, true);
    }
    $this->render('report');
  }
}