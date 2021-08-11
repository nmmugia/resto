<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Header_Bill_Template extends Admin_Controller
{
  public function __construct()
  {
    parent::__construct();
    
  }
  public function index()
  {
    $this->data['title']    = "Template Header Bill";
    $this->data['subtitle'] = "Template Header Bill";
    $form_data=$this->store_model->get_all_where("template_global",array("reff"=>"HEADER_BILL"));
    if(sizeof($form_data)>0)$form_data=$form_data[0];
    $this->data['form_data']=$form_data;
    $this->form_validation->set_rules('description', 'Deskripsi');
    if ($this->form_validation->run() == true) {
      $array = array(
        'template_name' => "",
        'description' => $this->input->post('description'),
        'created_at'=>date("Y-m-d H:i:s"),
        'created_by'=>$this->data['user_profile_data']->id,
        'reff'=>"HEADER_BILL"
      );
      if(sizeof($form_data)>0){
        $save = $this->store_model->save('template_global', $array,$form_data->id);
      }else{
        $save = $this->store_model->save('template_global', $array);
      }
      if ($save === false) {
        $this->session->set_flashdata('message', 'Gagal menyimpan data');
      }else {
        $this->session->set_flashdata('message_success', 'Berhasil menyimpan data');
      }
      redirect(SITE_ADMIN . '/header_bill_template', 'refresh');
    }else {
      $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
      $this->data['message_success'] = $this->session->flashdata('message_success');
      $this->data['description'] = array(
        'name' => 'description',
        'id' => 'description',
        'type' => 'text',
        'class' => 'form-control',
        'field-name' => 'Deskripsi',
        'placeholder' => 'Masukan deskripsi',
        'value' => $this->form_validation->set_value('description',(sizeof($form_data)>0 ? $form_data->description : ""))
      );
      $this->data['content'] .= $this->load->view('admin/header-bill-template', $this->data, true);
      $this->render('admin');
    }
  }
}