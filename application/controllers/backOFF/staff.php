<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/17/2014
 * Time: 8:27 PM
 */
class Staff extends Admin_Controller
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
        redirect(SITE_ADMIN . '/staff/admin');
    }

    public function admin()
    {
        $this->data['title']    = $this->lang->line('admin_staff_title');
        $this->data['subtitle'] = $this->lang->line('admin_staff_title');

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['add_url']  = base_url(SITE_ADMIN . '/staff/add_admin');
        $this->data['data_url'] = base_url(SITE_ADMIN . '/staff/getdataadmin');;
        //load content
        $this->data['use_username'] = TRUE;
        $this->data['content'] .= $this->load->view('admin/staff-list', $this->data, true);
        $this->render('admin');
    }

    public function backoffice()
    {
        $this->data['title']    = $this->lang->line('backoffice_staff_title');
        $this->data['subtitle'] = "Staf Back Office";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['add_url']  = base_url(SITE_ADMIN . '/staff/add_backoffice');
        $this->data['data_url'] = base_url(SITE_ADMIN . '/staff/getdatabackoffice');;
        //load content
        $this->data['use_username'] = TRUE;
        $this->data['content'] .= $this->load->view('admin/staff-list', $this->data, true);
        $this->render('admin');
    }

    public function kitchen()
    {
        $this->data['title']    = $this->lang->line('kitchen_staff_title');
        $this->data['subtitle'] = $this->lang->line('kitchen_staff_title');

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['add_url']  = base_url(SITE_ADMIN . '/staff/add_kitchen');
        $this->data['data_url'] = base_url(SITE_ADMIN . '/staff/getdatakitchen');;
        //load content
        $this->data['content'] .= $this->load->view('admin/staff-list', $this->data, true);
        $this->render('admin');
    }

    public function waiter()
    {
        $this->data['title']    = $this->lang->line('waiter_staff_title');
        $this->data['subtitle'] = $this->lang->line('waiter_staff_title');

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['add_url']  = base_url(SITE_ADMIN . '/staff/add_waiter');
        $this->data['data_url'] = base_url(SITE_ADMIN . '/staff/getdatawaiter');;
        //load content
        $this->data['content'] .= $this->load->view('admin/staff-list', $this->data, true);
        $this->render('admin');
    }

    public function cashier()
    {
        $this->data['title']    = $this->lang->line('cashier_staff_title');
        $this->data['subtitle'] = $this->lang->line('cashier_staff_title');

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['add_url']  = base_url(SITE_ADMIN . '/staff/add_cashier');
        $this->data['data_url'] = base_url(SITE_ADMIN . '/staff/getdatacashier');;
        //load content
        $this->data['content'] .= $this->load->view('admin/staff-list', $this->data, true);
        $this->render('admin');
    }

    public function add_waiter()
    {
        $this->data['title']    = "Tambah Waiter";
        $this->data['subtitle'] = "Tambah Waiter";

        //validate form input
        $this->form_validation->set_rules('username', 'NIP', 'required|numeric|xss_clean|min_length[1]|max_length[50]|callback__checkusernameusers');
        $this->form_validation->set_rules('store_id', 'Resto', 'required|callback__dropdown_check');
        $this->form_validation->set_rules('name', 'Nama', 'required|xss_clean|min_length[1]|max_length[100]');
        $this->form_validation->set_rules('email', 'Email', 'valid_email|callback__checkemailusers');
        $this->form_validation->set_rules('address', 'address', 'xss_clean');
        $this->form_validation->set_rules('identity_num', 'Nomor identitas', 'required|xss_clean|min_length[1]|max_length[100]');

        $phone_input = $this->input->post('phone');
        if (!empty($phone_input)) {
            $this->form_validation->set_rules('phone', 'Telepon', 'numeric|min_length[6]|max_length[16]');
        }


        if ($this->form_validation->run() == true) {

            $username = $this->input->post('username');
            $email    = $this->input->post('email');
            $password = $username;


            $additional_data = array('name' => $this->input->post('name'),
                                     'address' => $this->input->post('address'),
                                     'phone' => $this->input->post('phone'),
                                     'identity_type' => $this->input->post('identity_type'),
                                     'identity_num' => $this->input->post('identity_num'),
                                     'gender' => $this->input->post('gender'),
                                     'store_id' => $this->input->post('store_id'),
                                     'outlet_id' => $this->input->post('outlet_id'));
            $group           = array('5');

            $save = $this->ion_auth->register($username, $password, $email, $additional_data, $group);

            if ($save === false) {
                $this->session->set_flashdata('message', $this->lang->line('error_add'));
            }
            else {
                $this->session->set_flashdata('message_success', $this->lang->line('success_add'));
            }


            $btnaction = $this->input->post('btnAction');
            if ($btnaction == 'save_exit') {
                redirect(SITE_ADMIN . '/staff/waiter', 'refresh');
            }
            else {
                redirect(SITE_ADMIN . '/staff/add_waiter/', 'refresh');
            }
        }
        else {
            //display the create user form
            //set the flash data error message if there is one
            $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            $this->data['message_success'] = $this->session->flashdata('message_success');

            $this->data['store_id']      = $this->categories_model->get_store();
            $this->data['outlet_id']     = $this->categories_model->get_outlet_dropdown();
            $this->data['identity_type'] = array('KTP' => 'KTP', 'SIM' => 'SIM', 'Kartu pelajar' => 'Kartu Pelajar');
            $this->data['gender']        = array('0' => 'Wanita', '1' => 'Pria');

            $this->data['username']     = array('name'        => 'username',
                                                'id'          => 'username',
                                                'type'        => 'text',
                                                'class'       => 'form-control requiredTextField NumericWithZero',
                                                'field-name'  => 'NIP',
                                                'placeholder' => 'Masukan NIP',
                                                'value'       => $this->form_validation->set_value('username'));
            $this->data['name']         = array('name'        => 'name',
                                                'id'          => 'name',
                                                'type'        => 'text',
                                                'class'       => 'form-control requiredTextField',
                                                'field-name'  => 'Nama',
                                                'placeholder' => 'Masukan nama',
                                                'value'       => $this->form_validation->set_value('name'));
            $this->data['email']        = array('name'        => 'email',
                                                'id'          => 'email',
                                                'type'        => 'text',
                                                'class'       => 'form-control',
                                                'field-name'  => 'Email',
                                                'placeholder' => 'Masukan email',
                                                'value'       => $this->form_validation->set_value('email'));
            $this->data['identity_num'] = array('name'        => 'identity_num',
                                                'id'          => 'identity_num',
                                                'type'        => 'text',
                                                'class'       => 'form-control requiredTextField',
                                                'field-name'  => 'No Identitas',
                                                'placeholder' => 'Masukan no identitas',
                                                'value'       => $this->form_validation->set_value('identity_num'));
            $this->data['address']      = array('name'        => 'address',
                                                'id'          => 'address',
                                                'type'        => 'text',
                                                'class'       => 'form-control',
                                                'rows'        => '5',
                                                'field-name'  => 'address',
                                                'placeholder' => 'Masukan alamat',
                                                'value'       => $this->form_validation->set_value('address'));
            $this->data['phone']        = array('name'        => 'phone',
                                                'id'          => 'phone',
                                                'type'        => 'text',
                                                'class'       => 'form-control NumericWithZero',
                                                'field-name'  => 'Telepon',
                                                'placeholder' => 'Masukan telepon',
                                                'value'       => $this->form_validation->set_value('phone'));

            $this->data['cancel_url'] = base_url(SITE_ADMIN . '/staff/waiter');
            //load content
            $this->data['content'] .= $this->load->view('admin/staff-add', $this->data, true);
            $this->render('admin');
        }

    }

    public function add_kitchen()
    {
        $this->data['title']           = "Add Kitchen";
        $this->data['subtitle']        = "Add Kitchen";
        $this->data['required_outlet'] = TRUE;

        //validate form input
        $this->form_validation->set_rules('username', 'NIP', 'required|numeric|xss_clean|min_length[1]|max_length[50]|callback__checkusernameusers');
        $this->form_validation->set_rules('store_id', 'Store', 'required|callback__dropdown_check');
        $this->form_validation->set_rules('outlet_id', 'Outlet', 'required|callback__dropdown_check');
        $this->form_validation->set_rules('name', 'Name', 'required|xss_clean|min_length[1]|max_length[100]');
        $this->form_validation->set_rules('email', 'Email', 'valid_email|callback__checkemailusers');
        $this->form_validation->set_rules('address', 'Address', 'xss_clean');
        $this->form_validation->set_rules('identity_num', 'Identity Number', 'required|xss_clean|min_length[1]|max_length[100]');

        $phone_input = $this->input->post('phone');
        if (! empty($phone_input)) {
            $this->form_validation->set_rules('phone', 'Phone', 'numeric|min_length[6]|max_length[16]');
        }


        if ($this->form_validation->run() == true) {

            $username = $this->input->post('username');
            $email    = $this->input->post('email');
            $password = $username;


            $additional_data = array('name' => $this->input->post('name'),
                                     'address' => $this->input->post('address'),
                                     'phone' => $this->input->post('phone'),
                                     'identity_type' => $this->input->post('identity_type'),
                                     'identity_num' => $this->input->post('identity_num'),
                                     'gender' => $this->input->post('gender'),
                                     'store_id' => $this->input->post('store_id'),
                                     'outlet_id' => $this->input->post('outlet_id'));
            $group           = array('4');

            $save = $this->ion_auth->register($username, $password, $email, $additional_data, $group);

            if ($save === false) {
                $this->session->set_flashdata('message', 'Failed save staff');
            }
            else {
                $this->session->set_flashdata('message_success', 'Success save staff');
            }


            $btnaction = $this->input->post('btnAction');
            if ($btnaction == 'save_exit') {
                redirect(SITE_ADMIN . '/staff/kitchen', 'refresh');
            }
            else {
                redirect(SITE_ADMIN . '/staff/add_kitchen/', 'refresh');
            }
        }
        else {
            //display the create user form
            //set the flash data error message if there is one
            $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            $this->data['message_success'] = $this->session->flashdata('message_success');

            $this->data['store_id']      = $this->categories_model->get_store();
            $this->data['outlet_id']     = $this->categories_model->get_outlet_dropdown();
            $this->data['identity_type'] = array('KTP' => 'KTP', 'SIM' => 'SIM', 'Kartu pelajar' => 'Kartu Pelajar');
            $this->data['gender']        = array('0' => 'Woman', '1' => 'Man');

            $this->data['username']     = array('name' => 'username',
                                                'id' => 'username',
                                                'type' => 'text',
                                                'class' => 'form-control requiredTextField NumericWithZero',
                                                'field-name' => 'NIP',
                                                'placeholder' => 'Enter NIP',
                                                'value' => $this->form_validation->set_value('username'));
            $this->data['name']         = array('name' => 'name',
                                                'id' => 'name',
                                                'type' => 'text',
                                                'class' => 'form-control requiredTextField',
                                                'field-name' => 'Name',
                                                'placeholder' => 'Enter Name',
                                                'value' => $this->form_validation->set_value('name'));
            $this->data['email']        = array('name' => 'email',
                                                'id' => 'email',
                                                'type' => 'text',
                                                'class' => 'form-control',
                                                'field-name' => 'Email',
                                                'placeholder' => 'Enter Email',
                                                'value' => $this->form_validation->set_value('email'));
            $this->data['identity_num'] = array('name' => 'identity_num',
                                                'id' => 'identity_num',
                                                'type' => 'text',
                                                'class' => 'form-control requiredTextField',
                                                'field-name' => 'Identity Number',
                                                'placeholder' => 'Enter Identity Number',
                                                'value' => $this->form_validation->set_value('identity_num'));
            $this->data['address']      = array('name' => 'address',
                                                'id' => 'address',
                                                'type' => 'text',
                                                'class' => 'form-control',
                                                'rows' => '5',
                                                'field-name' => 'Address',
                                                'placeholder' => 'Enter Address',
                                                'value' => $this->form_validation->set_value('address'));
            $this->data['phone']        = array('name' => 'phone',
                                                'id' => 'phone',
                                                'type' => 'text',
                                                'class' => 'form-control NumericWithZero',
                                                'field-name' => 'Phone',
                                                'placeholder' => 'Enter Phone',
                                                'value' => $this->form_validation->set_value('phone'));

            $this->data['cancel_url'] = base_url(SITE_ADMIN . '/staff/kitchen');
            //load content
            $this->data['content'] .= $this->load->view('admin/staff-add', $this->data, true);
            $this->render('admin');
        }

    }

    public function add_cashier()
    {
        $this->data['title']    = "Add Cashier";
        $this->data['subtitle'] = "Add Cashier";

        //validate form input
        $this->form_validation->set_rules('username', 'NIP', 'required|numeric|xss_clean|min_length[1]|max_length[50]|callback__checkusernameusers');
        $this->form_validation->set_rules('store_id', 'Store', 'required|callback__dropdown_check');
        $this->form_validation->set_rules('name', 'Name', 'required|xss_clean|min_length[1]|max_length[100]');
        $this->form_validation->set_rules('email', 'Email', 'valid_email|callback__checkemailusers');
        $this->form_validation->set_rules('address', 'Address', 'xss_clean');
        $this->form_validation->set_rules('identity_num', 'Identity Number', 'required|xss_clean|min_length[1]|max_length[100]');

        $phone_input = $this->input->post('phone');
        if (! empty($phone_input)) {
            $this->form_validation->set_rules('phone', 'Phone', 'numeric|min_length[6]|max_length[16]');
        }


        if ($this->form_validation->run() == true) {

            $username = $this->input->post('username');
            $email    = $this->input->post('email');
            $password = $username;


            $additional_data = array('name' => $this->input->post('name'),
                                     'address' => $this->input->post('address'),
                                     'phone' => $this->input->post('phone'),
                                     'identity_type' => $this->input->post('identity_type'),
                                     'identity_num' => $this->input->post('identity_num'),
                                     'gender' => $this->input->post('gender'),
                                     'store_id' => $this->input->post('store_id'),
                                     'outlet_id' => $this->input->post('outlet_id'));
            $group           = array('3');

            $save = $this->ion_auth->register($username, $password, $email, $additional_data, $group);

            if ($save === false) {
                $this->session->set_flashdata('message', 'Failed save staff');
            }
            else {
                $this->session->set_flashdata('message_success', 'Success save staff');
            }


            $btnaction = $this->input->post('btnAction');
            if ($btnaction == 'save_exit') {
                redirect(SITE_ADMIN . '/staff/cashier', 'refresh');
            }
            else {
                redirect(SITE_ADMIN . '/staff/add_cashier/', 'refresh');
            }
        }
        else {
            //display the create user form
            //set the flash data error message if there is one
            $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            $this->data['message_success'] = $this->session->flashdata('message_success');

            $this->data['store_id']      = $this->categories_model->get_store();
            $this->data['outlet_id']     = $this->categories_model->get_outlet_dropdown();
            $this->data['identity_type'] = array('KTP' => 'KTP', 'SIM' => 'SIM', 'Kartu pelajar' => 'Kartu Pelajar');
            $this->data['gender']        = array('0' => 'Woman', '1' => 'Man');

            $this->data['username']     = array('name' => 'username',
                                                'id' => 'username',
                                                'type' => 'text',
                                                'class' => 'form-control requiredTextField NumericWithZero',
                                                'field-name' => 'NIP',
                                                'placeholder' => 'Enter NIP',
                                                'value' => $this->form_validation->set_value('username'));
            $this->data['name']         = array('name' => 'name',
                                                'id' => 'name',
                                                'type' => 'text',
                                                'class' => 'form-control requiredTextField',
                                                'field-name' => 'Name',
                                                'placeholder' => 'Enter Name',
                                                'value' => $this->form_validation->set_value('name'));
            $this->data['email']        = array('name' => 'email',
                                                'id' => 'email',
                                                'type' => 'text',
                                                'class' => 'form-control',
                                                'field-name' => 'Email',
                                                'placeholder' => 'Enter Email',
                                                'value' => $this->form_validation->set_value('email'));
            $this->data['identity_num'] = array('name' => 'identity_num',
                                                'id' => 'identity_num',
                                                'type' => 'text',
                                                'class' => 'form-control requiredTextField',
                                                'field-name' => 'Identity Number',
                                                'placeholder' => 'Enter Identity Number',
                                                'value' => $this->form_validation->set_value('identity_num'));
            $this->data['address']      = array('name' => 'address',
                                                'id' => 'address',
                                                'type' => 'text',
                                                'class' => 'form-control',
                                                'rows' => '5',
                                                'field-name' => 'Address',
                                                'placeholder' => 'Enter Address',
                                                'value' => $this->form_validation->set_value('address'));
            $this->data['phone']        = array('name' => 'phone',
                                                'id' => 'phone',
                                                'type' => 'text',
                                                'class' => 'form-control NumericWithZero',
                                                'field-name' => 'Phone',
                                                'placeholder' => 'Enter Phone',
                                                'value' => $this->form_validation->set_value('phone'));

            $this->data['cancel_url'] = base_url(SITE_ADMIN . '/staff/cashier');
            //load content
            $this->data['content'] .= $this->load->view('admin/staff-add', $this->data, true);
            $this->render('admin');
        }

    }

    public function add_backoffice()
    {
        $this->data['title']        = "Add Backoffice";
        $this->data['subtitle']     = "Add Backoffice";
        $this->data['use_username'] = TRUE;

        //validate form input
        $this->form_validation->set_rules('username', 'Username', 'required|xss_clean|min_length[1]|max_length[50]|callback__checkusernameusers');
        $this->form_validation->set_rules('password', 'Password', 'required|xss_clean|min_length[6]|max_length[50]');
        $this->form_validation->set_rules('name', 'Name', 'required|xss_clean|min_length[1]|max_length[100]');
        $this->form_validation->set_rules('email', 'Email', 'valid_email|callback__checkemailusers');
        $this->form_validation->set_rules('address', 'Address', 'xss_clean');
        $this->form_validation->set_rules('identity_num', 'Identity Number', 'required|xss_clean|min_length[1]|max_length[100]');

        $phone_input = $this->input->post('phone');
        if (! empty($phone_input)) {
            $this->form_validation->set_rules('phone', 'Phone', 'numeric|min_length[6]|max_length[16]');
        }


        if ($this->form_validation->run() == true) {

            $username = $this->input->post('username');
            $email    = $this->input->post('email');
            $password = $this->input->post('password');


            $additional_data = array('name' => $this->input->post('name'),
                                     'address' => $this->input->post('address'),
                                     'phone' => $this->input->post('phone'),
                                     'identity_type' => $this->input->post('identity_type'),
                                     'identity_num' => $this->input->post('identity_num'),
                                     'gender' => $this->input->post('gender'),
                                     'store_id' => $this->input->post('store_id'),
                                     'outlet_id' => $this->input->post('outlet_id'));
            $group           = array('2');

            $save = $this->ion_auth->register($username, $password, $email, $additional_data, $group);

            if ($save === false) {
                $this->session->set_flashdata('message', 'Failed save staff');
            }
            else {
                $this->session->set_flashdata('message_success', 'Success save staff');
            }


            $btnaction = $this->input->post('btnAction');
            if ($btnaction == 'save_exit') {
                redirect(SITE_ADMIN . '/staff/backoffice', 'refresh');
            }
            else {
                redirect(SITE_ADMIN . '/staff/add_backoffice/', 'refresh');
            }
        }
        else {
            //display the create user form
            //set the flash data error message if there is one
            $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            $this->data['message_success'] = $this->session->flashdata('message_success');

            $this->data['store_id']      = $this->categories_model->get_store();
            $this->data['outlet_id']     = $this->categories_model->get_outlet_dropdown();
            $this->data['identity_type'] = array('KTP' => 'KTP', 'SIM' => 'SIM', 'Kartu pelajar' => 'Kartu Pelajar');
            $this->data['gender']        = array('0' => 'Woman', '1' => 'Man');

            $this->data['username']     = array('name' => 'username',
                                                'id' => 'username',
                                                'type' => 'text',
                                                'class' => 'form-control requiredTextField',
                                                'field-name' => 'Username',
                                                'placeholder' => 'Enter Username',
                                                'value' => $this->form_validation->set_value('username'));
            $this->data['password']     = array('name' => 'password',
                                                'id' => 'password',
                                                'type' => 'password',
                                                'class' => 'form-control requiredTextField',
                                                'field-name' => 'Password',
                                                'placeholder' => 'Enter Password',
                                                'value' => '');
            $this->data['name']         = array('name' => 'name',
                                                'id' => 'name',
                                                'type' => 'text',
                                                'class' => 'form-control requiredTextField',
                                                'field-name' => 'Name',
                                                'placeholder' => 'Enter Name',
                                                'value' => $this->form_validation->set_value('name'));
            $this->data['email']        = array('name' => 'email',
                                                'id' => 'email',
                                                'type' => 'text',
                                                'class' => 'form-control',
                                                'field-name' => 'Email',
                                                'placeholder' => 'Enter Email',
                                                'value' => $this->form_validation->set_value('email'));
            $this->data['identity_num'] = array('name' => 'identity_num',
                                                'id' => 'identity_num',
                                                'type' => 'text',
                                                'class' => 'form-control requiredTextField',
                                                'field-name' => 'Identity Number',
                                                'placeholder' => 'Enter Identity Number',
                                                'value' => $this->form_validation->set_value('identity_num'));
            $this->data['address']      = array('name' => 'address',
                                                'id' => 'address',
                                                'type' => 'text',
                                                'class' => 'form-control',
                                                'rows' => '5',
                                                'field-name' => 'Address',
                                                'placeholder' => 'Enter Address',
                                                'value' => $this->form_validation->set_value('address'));
            $this->data['phone']        = array('name' => 'phone',
                                                'id' => 'phone',
                                                'type' => 'text',
                                                'class' => 'form-control NumericWithZero',
                                                'field-name' => 'Phone',
                                                'placeholder' => 'Enter Phone',
                                                'value' => $this->form_validation->set_value('phone'));

            $this->data['cancel_url'] = base_url(SITE_ADMIN . '/staff/backoffice');
            //load content
            $this->data['content'] .= $this->load->view('admin/staff-add', $this->data, true);
            $this->render('admin');
        }

    }

    public function add_admin()
    {
        $this->data['title']        = "Add Administrator";
        $this->data['subtitle']     = "Add Administrator";
        $this->data['use_username'] = TRUE;

        //validate form input
        $this->form_validation->set_rules('username', 'Username', 'required|xss_clean|min_length[1]|max_length[50]|callback__checkusernameusers');
        $this->form_validation->set_rules('password', 'Password', 'required|xss_clean|min_length[6]|max_length[50]');
        $this->form_validation->set_rules('name', 'Name', 'required|xss_clean|min_length[1]|max_length[100]');
        $this->form_validation->set_rules('email', 'Email', 'valid_email|callback__checkemailusers');
        $this->form_validation->set_rules('address', 'Address', 'xss_clean');
        $this->form_validation->set_rules('identity_num', 'Identity Number', 'required|xss_clean|min_length[1]|max_length[100]');

        $phone_input = $this->input->post('phone');
        if (! empty($phone_input)) {
            $this->form_validation->set_rules('phone', 'Phone', 'numeric|min_length[6]|max_length[16]');
        }


        if ($this->form_validation->run() == true) {

            $username = $this->input->post('username');
            $email    = $this->input->post('email');
            $password = $this->input->post('password');


            $additional_data = array('name' => $this->input->post('name'),
                                     'address' => $this->input->post('address'),
                                     'phone' => $this->input->post('phone'),
                                     'identity_type' => $this->input->post('identity_type'),
                                     'identity_num' => $this->input->post('identity_num'),
                                     'gender' => $this->input->post('gender'),
                                     'store_id' => $this->input->post('store_id'),
                                     'outlet_id' => $this->input->post('outlet_id'));
            $group           = array('1');

            $save = $this->ion_auth->register($username, $password, $email, $additional_data, $group);

            if ($save === false) {
                $this->session->set_flashdata('message', 'Failed save staff');
            }
            else {
                $this->session->set_flashdata('message_success', 'Success save staff');
            }


            $btnaction = $this->input->post('btnAction');
            if ($btnaction == 'save_exit') {
                redirect(SITE_ADMIN . '/staff/admin', 'refresh');
            }
            else {
                redirect(SITE_ADMIN . '/staff/add_admin/', 'refresh');
            }
        }
        else {
            //display the create user form
            //set the flash data error message if there is one
            $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            $this->data['message_success'] = $this->session->flashdata('message_success');

            $this->data['store_id']      = $this->categories_model->get_store();
            $this->data['outlet_id']     = $this->categories_model->get_outlet_dropdown();
            $this->data['identity_type'] = array('KTP' => 'KTP', 'SIM' => 'SIM', 'Kartu pelajar' => 'Kartu Pelajar');
            $this->data['gender']        = array('0' => 'Woman', '1' => 'Man');

            $this->data['username']     = array('name' => 'username',
                                                'id' => 'username',
                                                'type' => 'text',
                                                'class' => 'form-control requiredTextField',
                                                'field-name' => 'Username',
                                                'placeholder' => 'Enter Username',
                                                'value' => $this->form_validation->set_value('username'));
            $this->data['password']     = array('name' => 'password',
                                                'id' => 'password',
                                                'type' => 'password',
                                                'class' => 'form-control requiredTextField',
                                                'field-name' => 'Password',
                                                'placeholder' => 'Enter Password',
                                                'value' => '');
            $this->data['name']         = array('name' => 'name',
                                                'id' => 'name',
                                                'type' => 'text',
                                                'class' => 'form-control requiredTextField',
                                                'field-name' => 'Name',
                                                'placeholder' => 'Enter Name',
                                                'value' => $this->form_validation->set_value('name'));
            $this->data['email']        = array('name' => 'email',
                                                'id' => 'email',
                                                'type' => 'text',
                                                'class' => 'form-control',
                                                'field-name' => 'Email',
                                                'placeholder' => 'Enter Email',
                                                'value' => $this->form_validation->set_value('email'));
            $this->data['identity_num'] = array('name' => 'identity_num',
                                                'id' => 'identity_num',
                                                'type' => 'text',
                                                'class' => 'form-control requiredTextField',
                                                'field-name' => 'Identity Number',
                                                'placeholder' => 'Enter Identity Number',
                                                'value' => $this->form_validation->set_value('identity_num'));
            $this->data['address']      = array('name' => 'address',
                                                'id' => 'address',
                                                'type' => 'text',
                                                'class' => 'form-control',
                                                'rows' => '5',
                                                'field-name' => 'Address',
                                                'placeholder' => 'Enter Address',
                                                'value' => $this->form_validation->set_value('address'));
            $this->data['phone']        = array('name' => 'phone',
                                                'id' => 'phone',
                                                'type' => 'text',
                                                'class' => 'form-control NumericWithZero',
                                                'field-name' => 'Phone',
                                                'placeholder' => 'Enter Phone',
                                                'value' => $this->form_validation->set_value('phone'));

            $this->data['cancel_url'] = base_url(SITE_ADMIN . '/staff/admin');
            //load content
            $this->data['content'] .= $this->load->view('admin/staff-add', $this->data, true);
            $this->render('admin');
        }

    }

    public function edit()
    {
        $id       = $this->uri->segment(4);
        $group_id = $this->uri->segment(5);

        if (empty($id)) {
            redirect(SITE_ADMIN . '/staff');
        }

        if (empty($group_id)) {
            redirect(SITE_ADMIN . '/staff');
        }

        $group_data = $this->categories_model->get_one('groups', $group_id);
        if (empty($group_data)) {
            redirect(SITE_ADMIN . '/staff');
        }

        $form_data = $this->categories_model->get_one('users', $id);
        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/staff');
        }

        $group_users = $this->categories_model->get_by('users_groups', $id, 'user_id');
        if (empty($group_users)) {
            redirect(SITE_ADMIN . '/staff');
        }
        if ($group_users->group_id != $group_id) {
            redirect(SITE_ADMIN . '/staff');
        }

        $this->data['form_data'] = $form_data;
        $this->data['group_id']  = $group_data->id;
        $this->data['subtitle']  = "Edit " . $group_data->description;

        //validate form input
        if ($this->input->post('email') != $form_data->email) {
            $is_unique = '|is_unique[users.email]';
        }
        else {
            $is_unique = '';
        }

        if ($group_id == 1 OR $group_id == 2) {
            $this->data['use_username'] = TRUE;
            $this->form_validation->set_rules('username', 'Username', 'required|xss_clean|min_length[1]|max_length[50]');
        }
        else {
            if ($group_id == 4) {
                $this->form_validation->set_rules('outlet_id', 'Outlet', 'required|callback__dropdown_check');
            }
            $this->form_validation->set_rules('store_id', 'Store', 'required|callback__dropdown_check');

            $this->form_validation->set_rules('username', 'NIP', 'required|numeric|xss_clean|min_length[1]|max_length[50]');
        }


        $this->form_validation->set_rules('name', 'Name', 'required|xss_clean|min_length[1]|max_length[100]');
        $this->form_validation->set_rules('email', 'Email', 'valid_email' . $is_unique);
        $this->form_validation->set_rules('address', 'Address', 'xss_clean');
        $this->form_validation->set_rules('identity_num', 'Identity Number', 'required|xss_clean|min_length[1]|max_length[100]');

        $phone_input = $this->input->post('phone');
        if (! empty($phone_input)) {
            $this->form_validation->set_rules('phone', 'Phone', 'numeric|min_length[6]|max_length[16]');
        }

        $password = $this->input->post('password');
        if (! empty($password)) {
            $this->form_validation->set_rules('password', 'Password', 'required|xss_clean|min_length[6]|max_length[50]');
        }


        if (isset($_POST) && ! empty($_POST)) {

            if ($this->form_validation->run() === TRUE) {

                $email         = $this->input->post('email');
                $name          = $this->input->post('name');
                $address       = $this->input->post('address');
                $identity_num  = $this->input->post('identity_num');
                $identity_type = $this->input->post('identity_type');
                $phone         = $this->input->post('phone');
                $gender        = $this->input->post('gender');
                $store_id      = $this->input->post('store_id');
                $outlet_id     = $this->input->post('outlet_id');

                if (! empty($password)) {
                    $additional_data = array('password' => $password,
                                             'name' => $name,
                                             'address' => $address,
                                             'phone' => $phone,
                                             'identity_type' => $identity_type,
                                             'identity_num' => $identity_num,
                                             'gender' => $gender,
                                             'store_id' => $store_id,
                                             'outlet_id' => $outlet_id,
                                             'email' => $email);
                }
                else {
                    $additional_data = array('name' => $name,
                                             'address' => $address,
                                             'phone' => $phone,
                                             'identity_type' => $identity_type,
                                             'identity_num' => $identity_num,
                                             'gender' => $gender,
                                             'store_id' => $store_id,
                                             'outlet_id' => $outlet_id,
                                             'email' => $email);
                }

                $save = $this->ion_auth->update($id, $additional_data);

                if ($save === false) {
                    $this->session->set_flashdata('message', 'Failed save staff');
                }
                else {
                    $this->session->set_flashdata('message_success', 'Success save staff');
                }

                $btnaction = $this->input->post('btnAction');
                if ($btnaction == 'save_exit') {
                    redirect(SITE_ADMIN . '/staff/' . $group_data->name, 'refresh');
                }
                else {
                    redirect(SITE_ADMIN . '/staff/edit/' . $id . '/' . $group_data->id, 'refresh');
                }


            }
        }

        //display the edit user form
        $this->data['csrf'] = $this->_get_csrf_nonce();

        //set the flash data error message if there is one
        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['store_id']      = $this->categories_model->get_store();
        $this->data['outlet_id']     = $this->categories_model->get_outlet_dropdown($form_data->outlet_id);
        $this->data['identity_type'] = array('KTP' => 'KTP', 'SIM' => 'SIM', 'Kartu pelajar' => 'Kartu Pelajar');
        $this->data['gender']        = array('0' => 'Woman', '1' => 'Man');

        if ($group_id == 1 OR $group_id == 2) {
            $this->data['username'] = array('name' => 'username',
                                            'id' => 'username',
                                            'type' => 'text',
                                            'class' => 'form-control requiredTextField',
                                            'field-name' => 'Username',
                                            'placeholder' => 'Enter Username',
                                            'value' => $this->form_validation->set_value('username', $form_data->username),
                                            'readonly' => 'readonly');
            $this->data['password'] = array('name' => 'password',
                                            'id' => 'password',
                                            'type' => 'password',
                                            'class' => 'form-control',
                                            'field-name' => 'Password',
                                            'placeholder' => 'Enter Password',
                                            'value' => '');
        }
        else {
            $this->data['username'] = array('name' => 'username',
                                            'id' => 'username',
                                            'type' => 'text',
                                            'class' => 'form-control requiredTextField NumericWithZero',
                                            'field-name' => 'NIP',
                                            'placeholder' => 'Enter NIP',
                                            'value' => $this->form_validation->set_value('username', $form_data->username),
                                            'readonly' => 'readonly');
        }

        $this->data['name']         = array('name' => 'name',
                                            'id' => 'name',
                                            'type' => 'text',
                                            'class' => 'form-control requiredTextField',
                                            'field-name' => 'Name',
                                            'placeholder' => 'Enter Name',
                                            'value' => $this->form_validation->set_value('name', $form_data->name));
        $this->data['email']        = array('name' => 'email',
                                            'id' => 'email',
                                            'type' => 'text',
                                            'class' => 'form-control',
                                            'field-name' => 'Email',
                                            'placeholder' => 'Enter Email',
                                            'value' => $this->form_validation->set_value('email', $form_data->email));
        $this->data['identity_num'] = array('name' => 'identity_num',
                                            'id' => 'identity_num',
                                            'type' => 'text',
                                            'class' => 'form-control requiredTextField',
                                            'field-name' => 'Identity Number',
                                            'placeholder' => 'Enter Identity Number',
                                            'value' => $this->form_validation->set_value('identity_num', $form_data->identity_num));
        $this->data['address']      = array('name' => 'address',
                                            'id' => 'address',
                                            'type' => 'text',
                                            'class' => 'form-control',
                                            'rows' => '5',
                                            'field-name' => 'Address',
                                            'placeholder' => 'Enter Address',
                                            'value' => $this->form_validation->set_value('address', $form_data->address));
        $this->data['phone']        = array('name' => 'phone',
                                            'id' => 'phone',
                                            'type' => 'text',
                                            'class' => 'form-control NumericWithZero',
                                            'field-name' => 'Phone',
                                            'placeholder' => 'Enter Phone',
                                            'value' => $this->form_validation->set_value('phone', $form_data->phone));

        $this->data['cancel_url'] = base_url(SITE_ADMIN . '/staff/' . $group_data->name);

        $this->data['content'] .= $this->load->view('admin/staff-edit.php', $this->data, true);

        $this->render('admin');
    }

    public function delete()
    {
        $id       = $this->uri->segment(4);
        $group_id = $this->uri->segment(5);

        if (empty($id)) {
            redirect(SITE_ADMIN . '/staff');
        }

        if (empty($group_id)) {
            redirect(SITE_ADMIN . '/staff');
        }

        $group_data = $this->categories_model->get_one('groups', $group_id);
        if (empty($group_data)) {
            redirect(SITE_ADMIN . '/staff');
        }

        $form_data = $this->categories_model->get_one('users', $id);

        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/staff');
        }

        $group_users = $this->categories_model->get_by('users_groups', $id, 'user_id');
        if (empty($group_users)) {
            redirect(SITE_ADMIN . '/staff');
        }
        if ($group_users->group_id != $group_id) {
            redirect(SITE_ADMIN . '/staff');
        }

        $user = $this->ion_auth->user()->row();
        if ($user->id == $id) {
            $this->session->set_flashdata('message', 'Cannot delete user');
        }
        else {
            $result = $this->ion_auth->delete_user($id);
            if ($result) {
                $this->session->set_flashdata('message_success', 'Staff successfully deleted');
            }
            else {
                $this->session->set_flashdata('message', 'Error. Failed to delete');
            }
        }

        redirect(SITE_ADMIN . '/staff/' . $group_data->name, 'refresh');
    }

    public function getdataadmin()
    {
        $group_id = 1;
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $this->datatables->select('users.id, username, name, email, phone, gender')->from('users')->join('store', 'store.id = users.store_id', 'left')->join('outlet', 'outlet.id = users.outlet_id', 'left')->join('users_groups', 'users_groups.user_id = users.id')->where('users_groups.group_id', $group_id)->unset_column('users.id')->unset_column('gender')->add_column('gender', '$1', 'check_gender(gender)')->add_column('actions', "<div class='btn-group'>
                                    <a href='" . base_url(SITE_ADMIN . '/staff/edit/$1/1') . "'  class='btn btn-default'><i class='fa fa-pencil'></i> Edit</a>
                                    <a href='" . base_url(SITE_ADMIN . '/staff/delete/$1/1') . "' class='btn btn-danger deleteNow' rel='Staff'><i class='fa fa-trash-o'></i> Delete</a>
                                </div>", 'id');
        echo $this->datatables->generate();
    }

    public function getdatabackoffice()
    {
        $group_id = 2;
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $this->datatables->select('users.id, username, name, email, phone, gender')->from('users')->join('store', 'store.id = users.store_id', 'left')->join('outlet', 'outlet.id = users.outlet_id', 'left')->join('users_groups', 'users_groups.user_id = users.id')->where('users_groups.group_id', $group_id)->unset_column('users.id')->unset_column('gender')->add_column('gender', '$1', 'check_gender(gender)')->add_column('actions', "<div class='btn-group'>
                                    <a href='" . base_url(SITE_ADMIN . '/staff/edit/$1/2') . "'  class='btn btn-default'><i class='fa fa-pencil'></i> Edit</a>
                                    <a href='" . base_url(SITE_ADMIN . '/staff/delete/$1/2') . "' class='btn btn-danger deleteNow' rel='Staff'><i class='fa fa-trash-o'></i> Delete</a>
                                </div>", 'id');
        echo $this->datatables->generate();
    }

    public function getdatacashier()
    {
        $group_id = 3;
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $this->datatables->select('users.id, store_name, outlet_name, username, name, email, phone, gender')->from('users')->join('store', 'store.id = users.store_id', 'left')->join('outlet', 'outlet.id = users.outlet_id', 'left')->join('users_groups', 'users_groups.user_id = users.id')->where('users_groups.group_id', $group_id)->unset_column('users.id')->unset_column('gender')->add_column('gender', '$1', 'check_gender(gender)')->add_column('actions', "<div class='btn-group'>
                                    <a href='" . base_url(SITE_ADMIN . '/staff/edit/$1/3') . "'  class='btn btn-default'><i class='fa fa-pencil'></i> Edit</a>
                                    <a href='" . base_url(SITE_ADMIN . '/staff/delete/$1/3') . "' class='btn btn-danger deleteNow' rel='Staff'><i class='fa fa-trash-o'></i> Delete</a>
                                </div>", 'id');
        echo $this->datatables->generate();
    }

    public function getdatakitchen()
    {
        $group_id = 4;
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $this->datatables->select('users.id, store_name, outlet_name, username, name, email, phone, gender')->from('users')->join('store', 'store.id = users.store_id', 'left')->join('outlet', 'outlet.id = users.outlet_id', 'left')->join('users_groups', 'users_groups.user_id = users.id')->where('users_groups.group_id', $group_id)->unset_column('users.id')->unset_column('gender')->add_column('gender', '$1', 'check_gender(gender)')->add_column('actions', "<div class='btn-group'>
                                    <a href='" . base_url(SITE_ADMIN . '/staff/edit/$1/4') . "'  class='btn btn-default'><i class='fa fa-pencil'></i> Edit</a>
                                    <a href='" . base_url(SITE_ADMIN . '/staff/delete/$1/4') . "' class='btn btn-danger deleteNow' rel='Staff'><i class='fa fa-trash-o'></i> Delete</a>
                                </div>", 'id');
        echo $this->datatables->generate();
    }

    public function getdatawaiter()
    {
        $group_id = 5;
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $this->datatables->select('users.id, store_name, outlet_name, username, name, email, phone, gender')->from('users')->join('store', 'store.id = users.store_id', 'left')->join('outlet', 'outlet.id = users.outlet_id', 'left')->join('users_groups', 'users_groups.user_id = users.id')->where('users_groups.group_id', $group_id)->unset_column('users.id')->unset_column('gender')->add_column('gender', '$1', 'check_gender(gender)')->add_column('actions', "<div class='btn-group'>
                                    <a href='" . base_url(SITE_ADMIN . '/staff/edit/$1/5') . "'  class='btn btn-default'><i class='fa fa-pencil'></i> Edit</a>
                                    <a href='" . base_url(SITE_ADMIN . '/staff/delete/$1/5') . "' class='btn btn-danger deleteNow' rel='Staff'><i class='fa fa-trash-o'></i> Delete</a>
                                </div>", 'id');
        echo $this->datatables->generate();
    }
}