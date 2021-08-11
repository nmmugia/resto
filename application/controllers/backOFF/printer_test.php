<?php if (! defined('BASEPATH')) exit('No direct script access allowed');


class Printer_Test extends Admin_Controller
{
  public function index()
  {
        if(empty($this->data['setting']['store_id']))redirect('');

        $this->data['title']    = "Printer Test";
        $this->data['subtitle'] = "Printer Test";

        $this->form_validation->set_rules('name', 'Nama Printer', 'required');

        if ($this->form_validation->run() == true) {

            $nama_printer = $this->input->post('name');
              

            $this->load->model("order_model");
            $this->load->helper(array('printer'));

            $printer_setting = $this->order_model->get_by("master_general_setting", "printer_cashier", "name");
             
            @printer_test_cashier($nama_printer, FALSE, TRUE, $printer_setting);

            redirect(SITE_ADMIN . '/printer_test', 'refresh');
        }
        else {
            //display the create user form
            //set the flash data error message if there is one
            $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            $this->data['message_success'] = $this->session->flashdata('message_success');

            $this->data['name'] = array('name' => 'name',
                                                'id' => 'name',
                                                'type' => 'text',
                                                'class' => 'form-control requiredTextField',
                                                'field-name' => 'Nama Printer',
                                                'placeholder' => 'Masukan Nama Printer',
                                                'value' => $this->form_validation->set_value('name'));


            $this->data['content'] .= $this->load->view('admin/printer-test', $this->data, true);
            $this->render('admin');
        }

    }
}