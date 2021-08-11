<?php if (! defined('BASEPATH')) exit('No direct script access allowed');


class Beginning_Balances extends Admin_Controller
{
  public function __construct() {
    parent::__construct();
    $this->load->model("account_model");
    $this->load->model("account_data_model");
  }
  public function index(){
    if(empty($this->data['setting']['store_id']))redirect('');
    $this->data['title']    = "Saldo Awal";
    $this->data['subtitle'] = "Pengaturan Saldo Awal";
    $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
    $this->data['message_success'] = $this->session->flashdata('message_success');
    if($this->input->server('REQUEST_METHOD') == 'POST'){
      $detail=$this->input->post("detail");
      $this->db->trans_begin();
      for($x=0;$x<sizeof($detail['account_id']);$x++){
        if($detail['amount'][$x]!=0){
          $account=$this->account_model->get_data($detail['account_id'][$x]);
          $balance_date=date("Y-m-d H:i:s");
          $this->account_data_model->save("account_data",array(
            "account_id" => $detail["account_id"][$x],
            "created_at" => $balance_date,
            "has_synchronized" => 0,
            "entry_type" => NULL,
            "store_id" => $this->data['setting']['store_id'],
            "debit" => ($account->default_balance==1 ? $detail['amount'][$x] : 0),
            "credit" => ($account->default_balance==0 ? $detail['amount'][$x] : 0),
            "foreign_id"=>NULL,
            "info" => "Saldo Awal"
          ));
        }
      }
      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        $this->session->set_flashdata('message', 'Failed save saldo awal');
      } else {
        $this->db->trans_commit();        
        $this->session->set_flashdata('message_success', 'Success save saldo awal');
      }
      redirect(SITE_ADMIN . '/beginning_balances', 'refresh');
    }else{
      $lists=$this->account_model->get_for_beginning_balances();
      $temp_coa_types=array();
      $coa_types=array();
      $data=array();
      foreach($lists as $l){
        if(!in_array($l->account_type_id,$temp_coa_types)){
          array_push($temp_coa_types,$l->account_type_id);
          array_push($coa_types,$l);
        }
        if(!isset($data[$l->account_type_id]))$data[$l->account_type_id]=array();
        array_push($data[$l->account_type_id],$l);
      }
      $this->data['coa_types']=$coa_types;
      $this->data['data']=$data;
      $this->data['content'] .= $this->load->view('admin/beginning-balance-list', $this->data, true);
      $this->render('admin');      
    }
  }
}