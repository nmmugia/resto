<?php if (! defined('BASEPATH')) exit('No direct script access allowed');


class Feature_Settings extends Admin_Controller
{
  public function __construct() {
    parent::__construct();
    $this->load->model("feature_model");
    $this->load->model("user_model");
  }
  public function index(){
    if(empty($this->data['setting']['store_id']))redirect('');
    $this->data['title']    = "Fitur";
    $this->data['subtitle'] = "Pengaturan Fitur";
    $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
    $this->data['message_success'] = $this->session->flashdata('message_success');
    $this->data['lists']=$this->feature_model->get_feature_lock_securities();
    $this->data['content'] .= $this->load->view('admin/feature-setting-list', $this->data, true);
    $this->render('admin');      
  }
  public function set($id=NULL){
    if($this->input->server('REQUEST_METHOD') == 'POST'){
      $feature_id=$this->input->post("feature_id");
      $users=$this->input->post("users");
      $users_unlock=($users!="" ? implode(",",$users) : "");
      $this->db->trans_begin();
      $this->feature_model->save("feature",array("users_unlock"=>$users_unlock),$feature_id);
      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        $this->session->set_flashdata('message', 'Pengaturan fitur gagal disimpan');
      } else {
        $this->db->trans_commit();        
        $this->session->set_flashdata('message_success', 'Pengaturan fitur berhasil disimpan');
      }
      redirect(SITE_ADMIN . '/feature_settings', 'refresh');
    }else{
      $this->data['lists']=$this->user_model->get_dashboard_access();
      $this->data['feature']=$this->feature_model->get_one("feature",$id);
      $content=$this->load->view('admin/feature-setting-set', $this->data, true);
      echo json_encode(array(
        "content" => $content,
        "feature" => $this->data['feature']
      ));
    }
  }
}