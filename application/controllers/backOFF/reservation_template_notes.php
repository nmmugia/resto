<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Reservation_Template_Notes extends Admin_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model("reservation_model");
  }
  public function index(){
    $this->data['title']           = "Reservasi Template Note";
    $this->data['subtitle']        = "Reservasi Template Note";
    $this->data['theme']           = 'floor-theme';
    $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
    $this->data['message_success'] = $this->session->flashdata('message_success');
    $this->data['add_url']  = base_url(SITE_ADMIN . '/reservation_template_note/add');
    $this->data['data_url'] = base_url(SITE_ADMIN . '/reservation_template_note/get_data');;
    $this->data['content'] .= $this->load->view('admin/reservation-template-note-list', $this->data, true);
    $this->render('admin');
  }

  public function get_data()
  {
    $this->load->library(array('datatables'));
    $this->load->helper(array('datatables'));
    $this->datatables->select('id,template_name,note')->from('template_reservation_note')
    ->add_column('actions',"
      <a href='" . base_url(SITE_ADMIN . '/reservation_template_notes/edit/$1') . "'  class='btn btn-default'><i class='fa fa-pencil'></i></a>
      <a href='" . base_url(SITE_ADMIN . '/reservation_template_notes/delete/$1') . "' class='btn btn-danger deleteNow' rel='Reservasi Template Note'><i class='fa fa-trash-o'></i></a>
    </div>", 'id');
    echo $this->datatables->generate();
  }
  public function add()
  {
    $this->data['title']    = "Reservasi Template Note";
    $this->data['subtitle'] = "Tambah Reservasi Template Note";
    $this->form_validation->set_rules('template_name', 'Nama Template', 'required|xss_clean|max_length[60]');
    $this->form_validation->set_rules('note', 'Note', 'required|xss_clean');
    if ($this->form_validation->run() == true) {
      $array = array(
        'template_name' => $this->input->post('template_name'),
        'note' => $this->input->post('note'),
        'created_at'=>date("Y-m-d H:i:s"),
        'created_by'=>$this->data['user_profile_data']->id
      );
      $save = $this->reservation_model->save('template_reservation_note', $array);
      if ($save === false) {
        $this->session->set_flashdata('message', 'Gagal menyimpan data');
      }else {
        $this->session->set_flashdata('message_success', 'Berhasil menyimpan data');
      }
      $btnaction = $this->input->post('btnAction');
      if ($btnaction == 'save_exit') {
        redirect(SITE_ADMIN . '/reservation_template_notes', 'refresh');
      }
      else {
        redirect(SITE_ADMIN . '/reservation_template_notes/add/', 'refresh');
      }
    }else {
      $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
      $this->data['message_success'] = $this->session->flashdata('message_success');
      $this->data['template_name'] = array(
        'name' => 'template_name',
        'id' => 'template_name',
        'type' => 'text',
        'class' => 'form-control requiredTextField only_alpha_numeric',
        'field-name' => 'Nama Template',
        'placeholder' => 'Masukan nama template',
        'value' => $this->form_validation->set_value('template_name')
      );
      $this->data['note'] = array(
        'name' => 'note',
        'id' => 'note',
        'type' => 'text',
        'class' => 'form-control requiredTextField',
        'field-name' => 'Note',
        'placeholder' => 'Masukan note',
        'value' => $this->form_validation->set_value('note')
      );
      $this->data['content'] .= $this->load->view('admin/reservation-template-note-add', $this->data, true);
      $this->render('admin');
    }
  }
  public function edit()
  {
    $id=$this->uri->segment(4);
    $form_data=$this->reservation_model->get_one("template_reservation_note",$id);
    if(sizeof($form_data)==0)redirect(base_url("reservation_template_notes"));
    $this->data['form_data']=$form_data;
    $this->data['title']    = "Reservasi Template Note";
    $this->data['subtitle'] = "Edit Reservasi Template Note";
    $this->form_validation->set_rules('template_name', 'Nama Template', 'required|xss_clean|max_length[60]');
    $this->form_validation->set_rules('note', 'Note', 'required|xss_clean');
    if ($this->form_validation->run() == true) {
      $array = array(
        'template_name' => $this->input->post('template_name'),
        'note' => $this->input->post('note'),
        'modified_at'=>date("Y-m-d H:i:s"),
        'modified_by'=>$this->data['user_profile_data']->id
      );
      $save = $this->reservation_model->save('template_reservation_note', $array,$id);
      if ($save === false) {
        $this->session->set_flashdata('message', 'Gagal menyimpan data');
      }else {
        $this->session->set_flashdata('message_success', 'Berhasil menyimpan data');
      }
      $btnaction = $this->input->post('btnAction');
      if ($btnaction == 'save_exit') {
        redirect(SITE_ADMIN . '/reservation_template_notes', 'refresh');
      }
      else {
        redirect(SITE_ADMIN . '/reservation_template_notes/edit/'.$id, 'refresh');
      }
    }else {
      $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
      $this->data['message_success'] = $this->session->flashdata('message_success');
      $this->data['template_name'] = array(
        'name' => 'template_name',
        'id' => 'template_name',
        'type' => 'text',
        'class' => 'form-control requiredTextField only_alpha_numeric',
        'field-name' => 'Nama Template',
        'placeholder' => 'Masukan nama template',
        'value' => $this->form_validation->set_value('template_name',$form_data->template_name)
      );
      $this->data['note'] = array(
        'name' => 'note',
        'id' => 'note',
        'type' => 'text',
        'class' => 'form-control requiredTextField',
        'field-name' => 'Note',
        'placeholder' => 'Masukan note',
        'value' => $this->form_validation->set_value('note',$form_data->note)
      );
      $this->data['content'] .= $this->load->view('admin/reservation-template-note-edit', $this->data, true);
      $this->render('admin');
    }
  }
  public function delete()
  {
    $id = $this->uri->segment(4);
    if (empty($id))redirect(SITE_ADMIN.'/reservation_template_notes');
    $form_data = $this->reservation_model->get_one('template_reservation_note', $id);
    if (empty($form_data))redirect(SITE_ADMIN.'/reservation_template_notes');
    $result = $this->reservation_model->delete('template_reservation_note', $id);
    if ($result) {
      $this->session->set_flashdata('message_success', 'Berhasil menghapus data');
    }else {
      $this->session->set_flashdata('message', 'Error. Gagal menghapus data');
    }
    redirect(SITE_ADMIN.'/reservation_template_notes', 'refresh');
  }    
}