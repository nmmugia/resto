<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/17/2014
 * Time: 8:27 PM
 */
class Outlet extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('categories_model');

        // disable for client
        redirect(SITE_ADMIN);
    }

    public function index()
    {
        $this->data['title']    = "Outlet";
        $this->data['subtitle'] = "Outlet";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        //load content
        $this->data['content'] .= $this->load->view('admin/outlet-list', $this->data, true);
        $this->render('admin');
    }

    public function add()
    {
        $this->data['title']    = "Add Outlet";
        $this->data['subtitle'] = "Add Outlet";

        //validate form input
        $this->form_validation->set_rules('outlet_name', 'Outlet Name', 'required|xss_clean|min_length[1]|max_length[100]');
        $this->form_validation->set_rules('store_id', 'Store', 'required|xss_clean|callback__dropdown_check');

        if ($this->form_validation->run() == true) {

            $array = array('outlet_name' => $this->input->post('outlet_name'), 'store_id' => $this->input->post('store_id'));

            $save = $this->categories_model->save('outlet', $array);

            if ($save === false) {
                $this->session->set_flashdata('message', 'Failed save outlet');
            }
            else {
                $this->session->set_flashdata('message_success', 'Success save outlet');
            }


            $btnaction = $this->input->post('btnAction');
            if ($btnaction == 'save_exit') {
                redirect(SITE_ADMIN . '/outlet', 'refresh');
            }
            else {
                redirect(SITE_ADMIN . '/outlet/add/', 'refresh');
            }


        }
        else {
            //display the create user form
            //set the flash data error message if there is one
            $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            $this->data['message_success'] = $this->session->flashdata('message_success');

            $this->data['outlet_name'] = array('name' => 'outlet_name', 'id' => 'outlet_name', 'type' => 'text', 'class' => 'form-control requiredTextField', 'field-name' => 'Outlet name', 'placeholder' => 'Enter Outlet Name', 'value' => $this->form_validation->set_value('outlet_name'));
            $store                     = $this->categories_model->get_store();
            $this->data['store_id']    = $store;

            //load content
            $this->data['content'] .= $this->load->view('admin/outlet-add', $this->data, true);
            $this->render('admin');
        }

    }

    public function edit()
    {
        $id = $this->uri->segment(4);

        if (empty($id)) {
            redirect(SITE_ADMIN . '/outlet');
        }

        $form_data = $this->categories_model->get_one('outlet', $id);

        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/outlet');
        }

        $this->data['form_data'] = $form_data;
        $this->data['subtitle']  = "Edit Outlet";

        //validate form input
        $this->form_validation->set_rules('outlet_name', 'Outlet Name', 'required|xss_clean|min_length[1]|max_length[100]');
        $this->form_validation->set_rules('store_id', 'Store', 'required|xss_clean|callback__dropdown_check');

        if (isset($_POST) && ! empty($_POST)) {

            if ($this->form_validation->run() === TRUE) {


                $array = array('outlet_name' => $this->input->post('outlet_name'), 'store_id' => $this->input->post('store_id'));

                $save = $this->categories_model->save('outlet', $array, $id);

                if ($save === false) {
                    $this->session->set_flashdata('message', 'Failed save outlet');
                }
                else {
                    $this->session->set_flashdata('message_success', 'Success save outlet');
                }


                $btnaction = $this->input->post('btnAction');
                if ($btnaction == 'save_exit') {
                    redirect(SITE_ADMIN . '/outlet', 'refresh');
                }
                else {
                    redirect(SITE_ADMIN . '/outlet/edit/' . $id, 'refresh');
                }


            }
        }

        //display the edit user form
        $this->data['csrf'] = $this->_get_csrf_nonce();

        //set the flash data error message if there is one
        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['outlet_name'] = array('name' => 'outlet_name', 'id' => 'outlet_name', 'type' => 'text', 'class' => 'form-control requiredTextField', 'field-name' => 'Outlet name', 'placeholder' => 'Enter Outlet Name', 'value' => $this->form_validation->set_value('outlet_name', $form_data->outlet_name));
        $store                     = $this->categories_model->get_store();
        $this->data['store_id']    = $store;

        $this->data['content'] .= $this->load->view('admin/outlet-edit.php', $this->data, true);

        $this->render('admin');
    }

    public function delete()
    {
        $id = $this->uri->segment(4);

        if (empty($id)) {
            redirect(SITE_ADMIN . '/outlet');
        }

        $form_data = $this->categories_model->get_one('outlet', $id);

        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/outlet');
        }

        $result = $this->categories_model->delete('outlet', $id);
        if ($result) {
            $this->session->set_flashdata('message_success', 'Outlet successfully deleted');
        }
        else {
            $this->session->set_flashdata('message', 'Error. Failed to delete');
        }

        redirect(SITE_ADMIN . '/outlet', 'refresh');
    }

    public function getdatatables()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $this->datatables->select('outlet.id, outlet_name, store_name')->from('outlet')->join('store', 'store.id = outlet.store_id')->add_column('actions', "<div class='btn-group'>
                                    <a href='" . base_url(SITE_ADMIN . '/outlet/edit/$1') . "'  class='btn btn-default'><i class='fa fa-pencil'></i> Edit</a>
                                    <a href='" . base_url(SITE_ADMIN . '/outlet/delete/$1') . "' class='btn btn-danger deleteNow' rel='Outlet'><i class='fa fa-trash-o'></i> Delete</a>
                                </div>", 'id');
        echo $this->datatables->generate();
    }
}