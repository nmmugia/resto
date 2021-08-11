<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/17/2014
 * Time: 8:27 PM
 */
class Hrd_staff extends Hrd_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) { 
            redirect(SITE_ADMIN . '/login', 'refresh');
        }else{
            $this->load->model('hrd_model');
            $this->load->model('categories_model');
            $this->load->library('encrypt');
            $data_settings = $this->hrd_model->get_where('hr_setting', array("name"=>"fingerprint_ip"));
            $this->ip_fingerprint = "192.168.1.201";
            if(!empty($data_settings)){
                $this->ip_fingerprint = $data_settings[0]->value;
            }
        } 
    }

    public function index()
    {
        
    } 
    public function _check_pin($pin)
    {
        $user_id = $this->uri->segment(4);

        if (!empty($user_id)) {
            $user_old = $this->db->where("id", $user_id)->get('users')->row();            
            $this->db->where("id !=", $user_old->id);
        }

        $data = $this->db->get('users')->result();
        foreach ($data as $key => $row) {
            if($this->encrypt->decode($row->pin) == $pin){
                $this->form_validation->set_message('_check_pin', 'pin sudah terdaftar');
                return FALSE;
            }
        }      
            return TRUE;

    }

    public function staff_list(){
        $this->data['title']    = $this->lang->line('admin_staff_title');
        $this->data['subtitle'] = $this->lang->line('admin_staff_title');

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['add_url']  = base_url(SITE_ADMIN . '/hrd_staff/add_staff');
        $this->data['data_url'] = base_url(SITE_ADMIN . '/hrd_staff/get_data_staff');;
        //load content
        $this->data['use_username'] = TRUE;
         
 
        $this->data['content'] .= $this->load->view('admin/hrd/staff-list', $this->data, true);
        $this->render('hrd');
    }

    public function add_staff(){ 
        $this->data['title']        = "Tambah Staff";
        $this->data['subtitle']     = "Tambah Staff";
        $this->data['use_username'] = TRUE;
        // $this->data['group_id'] = 1;

        //validate form input

        $this->form_validation->set_rules('store_id', 'Resto', 'required|callback__dropdown_check');
        $this->form_validation->set_rules('username', 'Username', 'required|xss_clean|min_length[1]|max_length[50]|callback__checkusernameusers');
        $this->form_validation->set_rules('password', 'Password', 'required|xss_clean|min_length[6]|max_length[50]');
        $this->form_validation->set_rules('name', 'Nama', 'required|xss_clean|min_length[1]|max_length[100]');
        $this->form_validation->set_rules('email', 'Email', 'valid_email|callback__checkemailusers');
        $this->form_validation->set_rules('address', 'address', 'xss_clean');
        $this->form_validation->set_rules('identity_num', 'No Identitas', 'required|xss_clean|min_length[1]|max_length[100]');
        
        $is_compliment = $this->input->post('is_compliment');
        $pin = 1;
       
        if($is_compliment == 1){
            $this->form_validation->set_rules('pin', 'PIN', 'required|xss_clean|min_length[1]|callback__check_pin');  
            $pin = $this->encrypt->encode($this->input->post('pin'));  
        }
        

        $phone_input = $this->input->post('phone');
        if (!empty($phone_input)) {
            $this->form_validation->set_rules('phone', 'Telepon', 'numeric|min_length[6]|max_length[16]');
        }


        if ($this->form_validation->run() == true) {

            $username = $this->input->post('username');
            $email    = $this->input->post('email');
            $password = $this->input->post('password');


            $additional_data = array('name'          => $this->input->post('name'),
                                     'address'       => $this->input->post('address'),
                                     'phone'         => $this->input->post('phone'),
                                     'identity_type' => $this->input->post('identity_type'),
                                     'identity_num'  => $this->input->post('identity_num'),
                                     'gender'        => $this->input->post('gender'),
                                     'store_id'      => $this->input->post('store_id'),
                                     'pin'          =>  $pin,
                                     'nip'      => $this->input->post('nip'),
                                     'account_no'      => $this->input->post('account_no'),
                                     'account_name'      => $this->input->post('account_name'),
                                     'account_branch'      => $this->input->post('account_branch'),
                                     'account_bank_id'      => $this->input->post('banks'),
                                     'outlet_id'     => $this->input->post('outlet_id'));
            $group           = array($this->input->post('user_level'));

            $save = $this->ion_auth->register($username, $password, $email, $additional_data, $group);

            if ($save === false) {
                $this->session->set_flashdata('message', $this->lang->line('error_add'));
            }
            else {
                $this->session->set_flashdata('message_success', $this->lang->line('success_add'));
            }


            $btnaction = $this->input->post('btnAction');
            if ($btnaction == 'save_exit') {
                redirect(SITE_ADMIN . '/hrd_staff/staff_list', 'refresh');
            }
            else {
                redirect(SITE_ADMIN . '/hrd_staff/add_staff/', 'refresh');
            }
        }
        else {
            //display the create user form
            //set the flash data error message if there is one
            $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            $this->data['message_success'] = $this->session->flashdata('message_success');

            $this->data['store_id']      = $this->hrd_model->get_store();
            $this->data['outlet_id']     = $this->hrd_model->get_outlet_dropdown();
            $this->data['user_levels']     = $this->hrd_model->get_user_level_dropdown();
            $this->data['banks']     = $this->hrd_model->get_bank_dropdown();
            $this->data['identity_type'] = array('KTP' => 'KTP', 'SIM' => 'SIM', 'Kartu pelajar' => 'Kartu Pelajar');
            // $this->data['gender']        = array('0' => 'Wanita', '1' => 'Pria');

            $this->data['men'] = array('name' => 'gender',
                'type' => 'radio',
                'class' => 'requiredTextField',
                'field-name' => 'Pria',
                'placeholder' => '',
                'checked'=> ($this->form_validation->set_value('gender') == 1) ? 'true': '',
                'value' => "1");
            
            $this->data['women'] = array('name' => 'gender',
                'type' => 'radio',
                'class' => 'requiredTextField',
                'field-name' => 'Wanita',
                'placeholder' => '',
                'checked'=> ($this->form_validation->set_value('gender') == 0) ? 'true': '',
                'value' => "0");


            $this->data['y_compliment'] = array('name' => 'is_compliment',
                'type' => 'radio',
                'class' => 'requiredTextField y_compliment', 
                'placeholder' => '',
                'checked'=> ($this->form_validation->set_value('is_compliment') == 1) ? 'true': '',
                'value' => "1");
            
            $this->data['n_compliment'] = array('name' => 'is_compliment',
                'type' => 'radio',
                'class' => 'requiredTextField n_compliment', 
                'placeholder' => '',
                'checked'=> ($this->form_validation->set_value('is_compliment') == 0) ? 'true': '',
                'value' => "0");

            $this->data['username']     = array('name'        => 'username',
                                                'id'          => 'username',
                                                'type'        => 'text',
                                                'class'       => 'form-control requiredTextField no-special-char',
                                                'field-name'  => 'Username',
                                                'placeholder' => 'Masukan username',
                                                'value'       => $this->form_validation->set_value('username'));
            $this->data['nip']     = array('name'        => 'nip',
                                                'id'          => 'nip',
                                                'type'        => 'text',
                                                'class'       => 'form-control requiredTextField NumericOnly',
                                                'field-name'  => 'NIP',
                                                'placeholder' => 'Masukan NIP',
                                                'value'       => $this->form_validation->set_value('nip'));

            $this->data['password']     = array('name'        => 'password',
                                                'id'          => 'password',
                                                'type'        => 'password',
                                                'class'       => 'form-control requiredTextField',
                                                'field-name'  => 'Password',
                                                'placeholder' => 'Masukan password',
                                                'value'       => '');

            $this->data['pin']     = array('name'        => 'pin',
                                                'id'          => 'pin',
                                                'type'        => 'text',
                                                'class'       => 'form-control',
                                                'field-name'  => 'PIN',
                                                'placeholder' => 'Masukan PIN',
                                                'value'       => '');            

            $this->data['name']         = array('name'        => 'name',
                                                'id'          => 'name',
                                                'type'        => 'text',
                                                'class'       => 'form-control requiredTextField char-only',
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
                                                'class'       => 'form-control requiredTextField NumericOnly',
                                                'maxlength'   => '50',
                                                'field-name'  => 'No Identitas',
                                                'placeholder' => 'Masukan no identitas', 
                                                'value'       => $this->form_validation->set_value('identity_num'));
            $this->data['address']      = array('name'        => 'address',
                                                'id'          => 'address',
                                                'type'        => 'text',
                                                'class'       => 'form-control no-special-char',
                                                'rows'        => '5',
                                                'field-name'  => 'address',
                                                'placeholder' => 'Masukan alamat',
                                                'value'       => $this->form_validation->set_value('address'));
            $this->data['phone']        = array('name'        => 'phone',
                                                'id'          => 'phone',
                                                'type'        => 'text',
                                                'class'       => 'form-control NumericOnly',
                                                'field-name'  => 'Telepon',
                                                'placeholder' => 'Masukan telepon',
                                                'value'       => $this->form_validation->set_value('phone'));
            $this->data['account_no']      = array('name'        => 'account_no',
                                                'id'          => 'account_no',
                                                'type'        => 'text',
                                                'class'       => 'form-control NumericOnly',
                                                'field-name'  => 'No Rekening',
                                                'placeholder' => 'Masukan No Rekening',
                                                'value'       => $this->form_validation->set_value('account_no'));

            $this->data['account_name']        = array('name'        => 'account_name',
                                                'id'          => 'account_name',
                                                'type'        => 'text',
                                                'class'       => 'form-control char-only',
                                                'field-name'  => 'A.n Rekening',
                                                'placeholder' => 'Masukan Atas Nama',
                                                'value'       => $this->form_validation->set_value('account_name'));

            $this->data['account_branch']        = array('name'        => 'account_branch',
                                                'id'          => 'account_branch',
                                                'type'        => 'text',
                                                'class'       => 'form-control char-only',
                                                'field-name'  => 'Kantor Cabang Bank',
                                                'placeholder' => 'Masukan Kantor Cabang Bank',
                                                'value'       => $this->form_validation->set_value('account_branch'));

            $this->data['cancel_url'] = base_url(SITE_ADMIN . '/hrd_staff/staff_list');
            //load content
            $this->data['content'] .= $this->load->view('admin/hrd/staff-add', $this->data, true);
            $this->render('hrd');
        }
    }
     public function get_data_staff()
    {
        $store = $this->data['setting']['store_id'];
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables')); 

        
        $this->datatables->select('store_name,outlet_name,users.id,users.active, username, name, email, phone, gender,users_groups.group_id as group_id')
            ->from('users')
            ->join('users_groups', 'users_groups.user_id = users.id')
            ->join('store', 'store.id = users.store_id', 'left')
            ->join('outlet', 'outlet.id = users.outlet_id', 'left')
            ->group_by('users.id')
            ->unset_column('users.id')->unset_column('gender')
            ->add_column('gender', '$1', 'check_gender(gender)')
            ->add_column('detail', "<div class='btn-group'>
                <a href='" . base_url(SITE_ADMIN . '/hrd_staff/detail_staff/$1') . "'  class='btn btn-default'><i class='fa fa-pencil'></i>Detail</a>
            </div>", 'id')
            ->add_column('actions',"$1",'check_action_staff(active,id,group_id)','active,id,group_id');


        if ($store > 0){
        $this->datatables->where('users.store_id', $store);
        }
        echo $this->datatables->generate();
    }


    public function delete_staff()
    {
        $id       = $this->uri->segment(4);
        $group_id = $this->uri->segment(5);

        if (empty($id)) {
            redirect(SITE_ADMIN . '/hrd_staff');
        }

        if (empty($group_id)) {
            redirect(SITE_ADMIN . '/hrd_staff');
        }

        $group_data = $this->hrd_model->get_one('groups', $group_id);
        if (empty($group_data)) {
            redirect(SITE_ADMIN . '/hrd_staff');
        }

        $form_data = $this->hrd_model->get_one('users', $id);

        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/hrd_staff');
        }

        if ( $form_data->id == 1 || $form_data->username == 'administrator'){
            $this->session->set_flashdata('message', $this->lang->line('error_permission'));
            redirect(SITE_ADMIN . '/hrd_staff/staff_list', 'refresh');
        } 
        
        $group_users = $this->hrd_model->get_by('users_groups', $id, 'user_id');
        if (empty($group_users)) {
            redirect(SITE_ADMIN . '/hrd_staff');
        }
        if ($group_users->group_id != $group_id) {
            redirect(SITE_ADMIN . '/hrd_staff');
        }

      
        $cashier_order = $this->hrd_model->get_by('bill', $id, 'cashier_id');
        if (!empty($cashier_order)) {
            $this->session->set_flashdata('message', 'User mempunyai order, tidak dapat dihapus!');
            redirect(SITE_ADMIN . '/hrd_staff/staff_list', 'refresh');
        }

        

        $user = $this->ion_auth->user()->row();
        if ($user->id == $id ) {
            $this->session->set_flashdata('message', $this->lang->line('error_delete'));
        }
        else {
            $result = $this->ion_auth->delete_user($id);
            if ($result) {
                $this->session->set_flashdata('message_success', $this->lang->line('success_delete'));
            }
            else {
                $this->session->set_flashdata('message', $this->lang->line('error_delete'));
            }
        }

        redirect(SITE_ADMIN . '/hrd_staff/staff_list', 'refresh');
    }


    public function edit_staff()
    {
        $id       = $this->uri->segment(4);
        $group_id = $this->uri->segment(5);

        if (empty($id)) {
            redirect(SITE_ADMIN . '/hrd_staff');
        }

        if (empty($group_id)) {
            redirect(SITE_ADMIN . '/hrd_staff');
        }

        $group_data = $this->categories_model->get_one('groups', $group_id);
        if (empty($group_data)) {
            redirect(SITE_ADMIN . '/hrd_staff');
        }

        $form_data = $this->categories_model->get_one('users', $id);
        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/hrd_staff');
        }

        $group_users = $this->categories_model->get_by('users_groups', $id, 'user_id');
        if (empty($group_users)) {
            redirect(SITE_ADMIN . '/hrd_staff');
        }
        if ($group_users->group_id != $group_id) {
            redirect(SITE_ADMIN . '/hrd_staff');
        }

         

        $this->data['form_data'] = $form_data;
        $this->data['group_id']  = $group_users->group_id;
        
        $this->data['subtitle']  = "Edit Staff";

        //validate form input
        if ($this->input->post('email') != $form_data->email) {
            $is_unique = '|is_unique[users.email]';
        }
        else {
            $is_unique = '';
        }

         
        $this->form_validation->set_rules('username', 'Username', 'required|xss_clean|min_length[1]|max_length[50]');
        $this->form_validation->set_rules('nip', 'NIP', 'required|numeric|xss_clean|min_length[1]|max_length[50]');

        $this->form_validation->set_rules('name', 'Nama', 'required|xss_clean|min_length[1]|max_length[100]');
        $this->form_validation->set_rules('email', 'Email', 'valid_email' . $is_unique);
        $this->form_validation->set_rules('address', 'address', 'xss_clean');
        $this->form_validation->set_rules('identity_num', 'No Identitas', 'required|xss_clean|min_length[1]|max_length[100]');

        $phone_input = $this->input->post('phone');
        if (!empty($phone_input)) {
            $this->form_validation->set_rules('phone', 'Telepon', 'numeric|min_length[6]|max_length[16]');
        }

        $password = $this->input->post('password');
        if (!empty($password)) {
            $this->form_validation->set_rules('password', 'Password', 'required|xss_clean|min_length[6]|max_length[50]');
        }
        $is_compliment = $this->input->post('is_compliment');
        $pin = 1;
       
        if($is_compliment == 1){
            $this->form_validation->set_rules('pin', 'PIN', 'required|xss_clean|min_length[1]|callback__check_pin');  
            $pin = $this->encrypt->encode($this->input->post('pin'));  
        }

        if (isset($_POST) && !empty($_POST)) {

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
                $nip     = $this->input->post('nip');
                $account_name     = $this->input->post('account_name');
                $account_branch     = $this->input->post('account_branch');
                $account_no     = $this->input->post('account_no');
                $account_bank_id     = $this->input->post('banks');
                $user_levels     = $this->input->post('user_level');

                if (!empty($password)) {
                    $additional_data = array('password'      => $password,
                                             'name'          => $name,
                                             'address'       => $address,
                                             'phone'         => $phone,
                                             'identity_type' => $identity_type,
                                             'identity_num'  => $identity_num,
                                             'gender'        => $gender,
                                             'store_id'      => $store_id,
                                             'outlet_id'     => $outlet_id,
                                             'nip'     => $nip,
                                             'account_name'     => $account_name,
                                             'account_branch'     => $account_branch,
                                             'account_no'     => $account_no,
                                             'account_bank_id'     => $account_bank_id,
                                             'pin'      => $pin,
                                             'email'         => $email);
                }
                else {
                    $additional_data = array('name'          => $name,
                                             'address'       => $address,
                                             'phone'         => $phone,
                                             'identity_type' => $identity_type,
                                             'identity_num'  => $identity_num,
                                             'gender'        => $gender,
                                             'store_id'      => $store_id,
                                             'outlet_id'     => $outlet_id,
                                            'nip'     => $nip,
                                            'account_name'     => $account_name,
                                             'account_branch'     => $account_branch,
                                             'account_no'     => $account_no,
                                             'account_bank_id'     => $account_bank_id,
                                             'pin'      => $pin,
                                             'email'         => $email);
                }
                $remove_user_group = $this->categories_model->delete_by_where("users_groups",array("user_id"=>$id));

                if($remove_user_group){
                    $save_data = array(
                        'user_id'      => $id,
                        'group_id'      => $user_levels
                    );
                    $bill_payment_id = $this->categories_model->save('users_groups', $save_data); 
                }

                $save = $this->ion_auth->update($id, $additional_data);

                if ($save === false) {
                    $this->session->set_flashdata('message', $this->lang->line('error_add'));
                }
                else {
                    $this->session->set_flashdata('message_success', $this->lang->line('success_add'));
                }

                $btnaction = $this->input->post('btnAction');
                if ($btnaction == 'save_exit') {
                    redirect(SITE_ADMIN . '/hrd_staff/staff_list', 'refresh');
                }
                else {
                    redirect(SITE_ADMIN . '/hrd_staff/edit_staff/' . $id . '/' . $group_data->id, 'refresh');
                }


            }
        }

        //display the edit user form
        $this->data['csrf'] = $this->_get_csrf_nonce();

        //set the flash data error message if there is one
        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['store_id'] = $this->hrd_model->get_store();  
        $this->data['outlet_id'] = $this->hrd_model->get_outlet_dropdown();
        $this->data['user_levels']     = $this->hrd_model->get_user_level_dropdown();
          $this->data['banks']     = $this->hrd_model->get_bank_dropdown();

        $this->data['identity_type'] = array('KTP' => 'KTP', 'SIM' => 'SIM', 'Kartu pelajar' => 'Kartu Pelajar');
        $this->data['gender']        = array('0' => 'Wanita', '1' => 'Pria');

        $this->data['group_id'] = $group_users->group_id;
        $this->data['data_outlet_id'] = $form_data->outlet_id;

        $this->data['men'] = array('name' => 'gender',
                'type' => 'radio',
                'class' => 'requiredTextField',
                'field-name' => 'Pria',
                'placeholder' => '',
                'checked'=> ($form_data->gender == 1) ? 'true': '',
                'value' => "1");
            
        $this->data['women'] = array('name' => 'gender',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Wanita',
            'placeholder' => '',
            'checked'=> ($form_data->gender == 0) ? 'true': '',
            'value' => "0");
        $this->data['y_compliment'] = array('name' => 'is_compliment',
            'type' => 'radio',
            'class' => 'requiredTextField y_compliment', 
            'placeholder' => '',
            'checked'=> ($form_data->pin!=1) ? 'true': '',
            'value' => "1");
        
        $this->data['n_compliment'] = array('name' => 'is_compliment',
            'type' => 'radio',
            'class' => 'requiredTextField n_compliment', 
            'placeholder' => '',
            'checked'=> ($form_data->pin==1) ? 'true': '',
            'value' => "0");
        $this->data['username'] = array(
            'name'        => 'username',
            'id'          => 'username',
            'type'        => 'text',
            'class'       => 'form-control requiredTextField',
            'field-name'  => 'Username',
            'placeholder' => 'Masukan username',
            'value'       => $this->form_validation->set_value('username', $form_data->username)
            );
        $this->data['password'] = array(
            'name'        => 'password',
            'id'          => 'password',
            'type'        => 'password',
            'class'       => 'form-control',
            'field-name'  => 'Password',
            'placeholder' => 'Masukan password',
            'value'       => '');
           
 
        $this->data['nip'] = array(
            'name'        => 'nip',
            'id'          => 'nip',
            'type'        => 'text',
            'class'       => 'form-control requiredTextField NumericWithZero',
            'field-name'  => 'NIP',
            'placeholder' => 'Masukan NIP',
            'value'       => $this->form_validation->set_value('nip', $form_data->nip));
        
        $this->data['pin']     = array(
            'name'        => 'pin',
            'id'          => 'pin',
            'type'        => 'text',
            'class'       => 'form-control requiredTextField NumericOnly',
            'field-name'  => 'PIN',
            'placeholder' => 'Masukan PIN',
            'value'       => $this->form_validation->set_value('pin', $this->encrypt->decode($form_data->pin)));
        

        $this->data['name']         = array(
            'name'        => 'name',
            'id'          => 'name',
            'type'        => 'text',
            'class'       => 'form-control requiredTextField',
            'field-name'  => 'Nama',
            'placeholder' => 'Masukan nama',
            'value'       => $this->form_validation->set_value('name', $form_data->name));

        $this->data['email']        = array(
            'name'        => 'email',
            'id'          => 'email',
            'type'        => 'text',
            'class'       => 'form-control',
            'field-name'  => 'Email',
            'placeholder' => 'Masukan email',
            'value'       => $this->form_validation->set_value('email', $form_data->email));

        $this->data['identity_num'] = array(
            'name'        => 'identity_num',
            'id'          => 'identity_num',
            'type'        => 'text',
            'class'       => 'form-control',
            'maxlength'   => '50',
            'field-name'  => 'No Identitas',
            'maxlength'   => '50',
            'placeholder' => 'Masukan no identitas',
            'value'       => $this->form_validation->set_value('identity_num', $form_data->identity_num));

        $this->data['address']      = array(
            'name'        => 'address',
            'id'          => 'address',
            'type'        => 'text',
            'class'       => 'form-control',
            'rows'        => '5',
            'field-name'  => 'Alamat',
            'placeholder' => 'Masukan alamat',
            'value'       => $this->form_validation->set_value('address', $form_data->address));

        $this->data['phone']        = array(
            'name'        => 'phone',
            'id'          => 'phone',
            'type'        => 'text',
            'class'       => 'form-control NumericWithZero',
            'field-name'  => 'Telepon',
            'placeholder' => 'Masukan telepon',
            'value'       => $this->form_validation->set_value('phone', $form_data->phone));

        $this->data['account_no']      = array('name'        => 'account_no',
                                            'id'          => 'account_no',
                                            'type'        => 'text',
                                            'class'       => 'form-control NumericOnly',
                                            'field-name'  => 'No Rekening',
                                            'placeholder' => 'Masukan No Rekening',
                                            'value'       => $this->form_validation->set_value('account_no', $form_data->account_no));

        $this->data['account_name']        = array('name'        => 'account_name',
                                            'id'          => 'account_name',
                                            'type'        => 'text',
                                            'class'       => 'form-control char-only',
                                            'field-name'  => 'A.n Rekening',
                                            'placeholder' => 'Masukan Atas Nama',
                                            'value'       =>$this->form_validation->set_value('phone', $form_data->account_name));

        $this->data['account_branch']        = array('name'        => 'account_branch',
                                            'id'          => 'account_branch',
                                            'type'        => 'text',
                                            'class'       => 'form-control char-only',
                                            'field-name'  => 'Kantor Cabang Bank',
                                            'placeholder' => 'Masukan Kantor Cabang Bank',
                                            'value'       => $this->form_validation->set_value('phone', $form_data->account_branch));
        $this->data['cancel_url'] = base_url(SITE_ADMIN . '/hrd_staff/staff_list');

        $this->data['content'] .= $this->load->view('admin/hrd/staff-edit.php', $this->data, true);

        $this->render('hrd');
    }
    public function detail_staff(){
        $this->data['title']    = "Detail Staff";
        $this->data['subtitle'] = "Detail Staff";
        $id       = $this->uri->segment(4);
        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['users'] = $this->hrd_model->get_one('users',$id); 
        $this->data['employee_id'] = $id;
        $this->data['jobs']     = $this->hrd_model->get_jobs_dropdown();
        $this->data['employee_affairs']     = $this->hrd_model->get_employee_affair_dropdown(); 
        $this->data['outlets']     = $this->hrd_model->get_outlet_dropdown();
        $this->data['stores']      = $this->hrd_model->get_store();
        $this->data['check_job_histories']=$this->hrd_model->get_all_where("hr_jobs_history",array("employee_id"=>$id));
        $this->data['resign_number']=$this->hrd_model->generate_resign_number();
        $this->data['date']=date("Y-m-d");
        $this->data['data_url'] = base_url(SITE_ADMIN . '/hrd_staff/get_detail_staff/'.$id);
        $this->data['content'] .= $this->load->view('admin/hrd/staff_detail_view', $this->data, true);
        $this->render('hrd');
    }
    public function exports($user_id="")
    {
      $this->load->helper(array('dompdf', 'file'));
      
      $this->data['employee']=$this->hrd_model->get_one("users",$user_id);
      $this->data['job_histories']=$this->hrd_model->get_jobs_histories($user_id);
      $this->data['payroll_histories']=$this->hrd_model->get_payroll_histories($user_id);
      // $this->data['content'] .= $this->load->view('admin/hrd/export_employee_view', $this->data, true);
      // $this->render('hrd');
      $html=$this->load->view("admin/hrd/export_employee_view",$this->data,true);
      $data     = pdf_create($html, '', false, 'portrait');
      $filename = 'detail_staff_'.date("Y-m-d").'.pdf';
      header("Content-type:application/pdf");
      header('Content-Disposition: attachment;filename="'.$filename.'"');
      echo $data;
    }
    public function resign(){
      if($this->input->server('REQUEST_METHOD') == 'POST'){
        $user_id=$this->input->post("user_id");
        $description=$this->input->post("description");
        $this->hrd_model->save("hr_resign",array(
          "user_id"=>$user_id,
          "date" => date("Y-m-d H:i:s"),
          "description"=>$description,
          "resign_number" => $this->hrd_model->generate_resign_number()
        ));
        $this->hrd_model->save("users",array("active"=>0),$user_id);
        $this->session->set_flashdata('message_success', "Data resign berhasil disimpan.");
      }
      redirect(SITE_ADMIN."/hrd_staff/staff_list");
    }
    public function paklaring($id=NULL){
        $this->data['title']    = "Detail Staff";
        $this->data['subtitle'] = "Paklaring";
        $this->data['resign']=$this->hrd_model->get_data_resign($id);
        $this->data['content'] .= $this->load->view('admin/hrd/staff_paklaring_view', $this->data, true);
        $this->render('hrd');
    }
     public function get_jobs_history()
    {
     
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));  
         $user_id = $this->uri->segment(4);
         $user=$this->hrd_model->get_one("users",$user_id);
        $this->datatables->select('a.id as id,b.name as status_name,a.start_date,a.end_date, d.jobs_name 
                ,c.store_name,reimburse,vacation')
            ->from('hr_jobs_history a') 
            ->join('hr_jobs d ', 'd.id = a.jobs_id') 
            ->join('users',"users.id = a.employee_id")
            ->join('hr_enum_employee_affair b', 'a.e_affair_id = b.id') 
            ->join('store c ', 'c.id = a.store_id')
            ->where("users.id",$user_id)
           ->add_column('actions', ($user->active==1 ? "<div class='btn-group'>
            <a   class='btn btn-default edit-jobs-history'  jobs-history-id='$1'><i class='fa fa-pencil'></i> Edit</a>
            <a  class='btn btn-danger delete-jobs-history' rel='Status Kepegawaian'  jobs-history-id='$1'><i class='fa fa-trash-o'></i> Delete</a>
        </div>" : ""), 'id'); 
       
        echo $this->datatables->generate();
    }

    public function save_jobs_history(){
        $emp_affair_id =  $this->input->post('emp_affair_id');
        $start_date =  $this->input->post('start_date');
        $end_date =  $this->input->post('end_date');
        $store_id =  $this->input->post('store_id');
        $jobs_id =  $this->input->post('jobs_id');
        $employee_id =  $this->input->post('employee_id');
        $reimburse =  $this->input->post('reimburse');
        $vacation =  $this->input->post('vacation');

        $return_data = array();
        $return_data['status'] = true;
        $return_data['data'] = array();
        $return_data['message'] = ""; 


         if(empty($employee_id)){
            $return_data['status'] = false;
            $return_data['message'] = "Parameter Pegawai  Kosong";  
        }

        if(empty($start_date)){
            $return_data['status'] = false;
            $return_data['message'] = "Parameter Tanggal Masuk Kosong";  
        }

        if(empty($store_id)){
            $return_data['status'] = false;
            $return_data['message'] = "Parameter Kantor Cabang Kosong";  
        }

        // if(empty($reimburse)){
            // $return_data['status'] = false;
            // $return_data['message'] = "Parameter Reimburse Kosong";  
        // }

        // if(empty($vacation)){
            // $return_data['status'] = false;
            // $return_data['message'] = "Parameter Vacation Kosong";  
        // }


        $outlet_id = 0;
        if($return_data['status']){
            $data = array(
                'employee_id' =>$employee_id,
                'e_affair_id' =>$emp_affair_id,
                'store_id' =>$store_id,
                'outlet_id' =>$outlet_id, 
                'jobs_id' =>$jobs_id,
                'start_date' =>$start_date,
                // 'end_date' =>$end_date,
                'reimburse' =>$reimburse, 
                'vacation' =>$vacation,
            );
            $save = $this->hrd_model->save_jobs_history($data);
            if($save){
                $return_data['status'] = true;
            }else{
                $return_data['message'] = "Maaf, Data Gagal Di simpan. Silahkan Hubungi Administrator";  
            } 
        }

        echo json_encode($return_data);
       
    }

    public function delete_jobs_history(){
        $id =  $this->input->post('id');
        $return_data['status'] = false;
        $return_data['data'] = array();
        $return_data['message'] = ""; 

        if(empty($id)){
            $return_data['message'] = "Parameter Kosong"; 
             echo json_encode($return_data); 
        }else{
            $status = $this->hrd_model->delete_jobs_history(array('id' => $id));
            if($status){
                $return_data['status'] = true;
            }else{
                $return_data['message'] = "Maaf, Data Gagal Di Hapus. Silahkan Hubungi Administrator";  
            }

            echo json_encode($return_data);
        } 
        
    }
    public function update_jobs_history(){
        $id =  $this->input->post('id');
        $emp_affair_id =  $this->input->post('emp_affair_id');
        $start_date =  $this->input->post('start_date');
        $end_date =  $this->input->post('end_date');
        $store_id =  $this->input->post('store_id');
        $jobs_id =  $this->input->post('jobs_id');
        $employee_id =  $this->input->post('employee_id');
        $reimburse =  $this->input->post('reimburse');
        $vacation =  $this->input->post('vacation');

        $return_data = array();
        $return_data['status'] = true;
        $return_data['data'] = array();
        $return_data['message'] = ""; 

         if(empty($store_id)){
            $return_data['status'] = false;
            $return_data['message'] = "Parameter Store Masuk Kosong";  
        }

         
        if(empty($start_date)){
            $return_data['status'] = false;
            $return_data['message'] = "Parameter Tanggal Masuk Kosong";  
        }

        // if(empty($end_date)){
            // $return_data['status'] = false;
            // $return_data['message'] = "Parameter Tanggal Keluar Kosong";  
        // }
        // if(empty($reimburse)){
            // $return_data['status'] = false;
            // $return_data['message'] = "Parameter Reimburse Kosong";  
        // }

        // if(empty($vacation)){
            // $return_data['status'] = false;
            // $return_data['message'] = "Parameter Vacation Kosong";  
        // }


        $outlet_id = 0;
        if($return_data['status']){
            $data = array(
              
                'e_affair_id' =>$emp_affair_id,
                'store_id' =>$store_id,
                'outlet_id' =>$outlet_id, 
                'jobs_id' =>$jobs_id,
                'start_date' =>$start_date,
                // 'end_date' =>$end_date,
                'reimburse' =>$reimburse, 
                'vacation' =>$vacation
            );
            $save = $this->hrd_model->update_jobs_history($id,$data);
            if($save){
                $return_data['status'] = true;
            }else{
                $return_data['status'] = false;
                $return_data['message'] = $this->db->last_query();  
            } 
        }

        echo json_encode($return_data);
        
    }
    public function get_one_jobs_history(){
        $id =  $this->input->post('id');
        $return_data = array();
        $return_data['status'] = false;
        $return_data['data'] = array();
        $return_data['message'] = ""; 

        if(empty($employee_affair_name)){
            $return_data['message'] = "Parameter Kosong";  
        }
        
       $data_employee = $this->hrd_model->get_one('hr_jobs_history', $id);
        if($data_employee){
            $return_data['status'] = true;
            $return_data['data'] = $data_employee;
        }else{
            $return_data['message'] = "Maaf, Gagal Mengambil data. Silahkan Hubungi Administrator";  
        }

        echo json_encode($return_data);
    } 

    public function upload_staff_to_machine(){ 
        $this->load->helper(array('attendances'));
        $data_staff = $this->hrd_model->get_where("users",array("id !=" =>"1"));
        $return_data = array();
        $return_data['status'] = true;
        $return_data['data'] = array();
        $return_data['message'] = ""; 
        $return_data['total_error'] = 0; 
        $return_data['total_success'] = 0; 
        $return_data['total_staff'] = 0; 

        foreach ($data_staff as $staff) { 
            $finger_templates=$this->hrd_model->get_where("hr_finger_templates",array("user_id"=>$staff->id));
            $data = array(
                "IP" => $this->ip_fingerprint,
                "ID" =>$staff->id,
                "NAME" =>$staff->name
            );  
            if(upload_to_fingerprint($data)){
              foreach($finger_templates as $f){
                upload_template_user_fingerprint(array(
                  "IP" => $this->ip_fingerprint,
                  "PIN" => $staff->id,
                  "FINGER_ID" => $f->finger_id,
                  "SIZE"=>$f->size,
                  "VALID"=>$f->valid,
                  "TEMPLATE"=>$f->template,
                ));
              }  
              $return_data['total_success']++;
            }else{
              $return_data['total_error']++;
            }
            $return_data['total_staff']++;
        }
        $total_staff  = $return_data['total_staff'];
        $total_error = $return_data['total_error'];
        $total_success = $return_data['total_success'];
        if($total_staff != ($total_error+$total_success) ){
            $return_data['status'] = false;
        }
        echo json_encode($return_data);

    }

    public function download_finger_template(){ 
        $this->load->helper(array('attendances')); 
        $return_data = array();
        $return_data['status'] = true;
        $return_data['data'] = array();
        $return_data['message'] = "";   

        $delete_all_template = $this->db->empty_table('hr_finger_templates'); 
        if($delete_all_template){
             $data = array(  "IP" =>$this->ip_fingerprint); 
            $template_datas = get_template_user_fingerprint($data); 
            foreach ($template_datas as $template_data) { 
                $save_data = array(
                    'user_id'      => $template_data["PIN"],
                    'finger_id'      => $template_data["FINGER_ID"],
                    'size'      => $template_data["SIZE"],
                    'valid'      => $template_data["VALID"],
                    'template'      => $template_data["TEMPLATE"],
                );
                $bill_payment_id = $this->categories_model->save('hr_finger_templates', $save_data); 
            }
        } 
        echo json_encode($return_data);

    }

     public function get_finger_template_byid()
    {
         $user_id = $this->uri->segment(4);
  
        if (empty($user_id)) {
           $user_id = 0;
        }


        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables')); 
         $this->datatables->select('finger_id,size,template')
            ->from('users')
            ->join("hr_finger_templates","users.id = hr_finger_templates.user_id")
            ->where("users.id",$user_id);
             
        echo $this->datatables->generate();
    }
    function clear_all_data(){
      $this->load->helper("attendances_helper");
      delete_all_user_fingerprint(array("IP"=>$this->ip_fingerprint));
    }
}