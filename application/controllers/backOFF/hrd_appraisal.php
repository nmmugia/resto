<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

 
class Hrd_appraisal extends Hrd_Controller
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
        } 
    }

    public function index()
    {
         redirect(SITE_ADMIN . '/hrd_appraisal/setting_template_appraisal', 'refresh');
    }

    public function setting_template_appraisal()
    {
        $this->data['title']    = "Daftar Template Appraisal";
        $this->data['subtitle'] ="Daftar Template Appraisal";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

       
        $this->data['data_url'] = base_url(SITE_ADMIN . '/hrd_appraisal/get_data_template_appraisal');
       
        //load content
        $this->data['use_username'] = TRUE;
        $this->data['content'] .= $this->load->view('admin/hrd/appraisal_template_list', $this->data, true);
        $this->render('hrd');
    }


    public function due_appraisal()
    {
        $this->data['title']    = "Daftar Hak Appraisal";
        $this->data['subtitle'] ="Daftar Hak Appraisal";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

       
        $this->data['data_url'] = base_url(SITE_ADMIN . '/hrd_appraisal/get_due_appraisal');
       
        //load content
        $this->data['use_username'] = TRUE;
        $this->data['content'] .= $this->load->view('admin/hrd/appraisal_due_list', $this->data, true);
        $this->render('hrd');
    }
    
    public function process_appraisal()
    {
        $this->data['title']    = "Daftar Proses Appraisal";
        $this->data['subtitle'] ="Daftar Proses Appraisal";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

       
        $this->data['data_url'] = base_url(SITE_ADMIN . '/hrd_appraisal/get_data_process_appraisal_list');
       
        //load content
        $this->data['use_username'] = TRUE;
        $this->data['content'] .= $this->load->view('admin/hrd/appraisal_process_list', $this->data, true);
        $this->render('hrd');
    }
    
    public function get_due_appraisal(){
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));  
        $this->load->helper(array('hrd'));
        $this->datatables->select('id,name,description')
            ->from('hr_appraisal_template')  
            ->add_column('actions', 
                "<div class='btn-group'>
                     <a href='" . base_url(SITE_ADMIN . '/hrd_appraisal/view_due_appraisal/$1')."' class='btn btn-default'  >View</a>
                      <a href='" . base_url(SITE_ADMIN . '/hrd_appraisal/edit_due_appraisal/$1')."' class='btn btn-default'  >Edit</a>
                      <a href='" . base_url(SITE_ADMIN . '/hrd_appraisal/delete_due_appraisal/$1')."' class='btn btn-danger deleteNow' rel='Hak Appraisal'>Delete</a>
                </div>", 'id');
           
            
        echo $this->datatables->generate();
    } 

    public function get_data_template_appraisal(){
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));  
        $this->load->helper(array('hrd'));
        $this->datatables->select('id,name,description,')
            ->from('hr_appraisal_template')  
            ->add_column('actions', 
                "<div class='btn-group'>
                     <a href='" . base_url(SITE_ADMIN . '/hrd_appraisal/view_template_appraisal/$1')."' class='btn btn-default'  >View</a>
                </div>", 'id');
           
            
        echo $this->datatables->generate();
    } 

    public function get_data_process_appraisal_list(){
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));  
        $this->load->helper(array('hrd'));
        $this->datatables->select('hap.id as appraisal_process_id,
                                users.name,hap.created_at, hat.name as template_name,hap.description,hap.period')
            ->from('hr_appraisal_process hap') 
            ->join('users','hap.user_id = users.id')
            ->join('hr_appraisal_template hat','hap.template_id = hat.id')  
            ->add_column('actions', 
                "<div class='btn-group'>
                    <a href='" . base_url(SITE_ADMIN . '/hrd_appraisal/view_process_appraisal/$1')."' class='btn btn-default'  >View</a>
                    <a href='" . base_url(SITE_ADMIN . '/hrd_appraisal/delete_process_appraisal/$1')."' class='btn btn-danger deleteNow' rel='Hasil Appraisal'>Delete</a>
                </div>", 'appraisal_process_id');
           
            
        echo $this->datatables->generate();
    } 
    public function add_due_appraisal(){
        $this->data['title']    = "Tambah Hak Appraisal";
        $this->data['subtitle'] = "Tambah Hak Appraisal";

        //validate form input
        $this->form_validation->set_rules('grantor_appraisal', 'Nama Pemberi Appraisal', 'required|xss_clean|min_length[1]|max_length[50]');
        $this->form_validation->set_rules('approval', 'Nama approval Appraisal', 'required|xss_clean');
      
        if ($this->form_validation->run() == true) {  
            $grantor_id = $this->input->post('grantor_appraisal');
            $receivers = $this->input->post('receiver');
            $approvals = $this->input->post('approval');
            $data_grantor = array(
                'is_grantor' => 1 
            );

            $grantor = $this->hrd_model->save('hr_jobs', $data_grantor,$grantor_id);
            if($grantor){
                foreach ($receivers as $receiver) {
                   
                    $data_receiver = array(
                        'job_id' => $receiver['job'],
                        'template_id' => $receiver['template'],
                        'grantor_id' => $grantor_id 
                    );

                    $receiver = $this->hrd_model->save('hr_appraisal_receiver', $data_receiver);
                }
                
                for ($i=0; $i < count($approvals); $i++) {   

                    $data_approval = array(
                        'job_id' => $approvals[$i], 
                        'grantor_id' => $grantor_id 
                    ); 
                    $save_approval = $this->hrd_model->save('hr_appraisal_approval', $data_approval);
                   
                }
                $this->session->set_flashdata('message_success', 'Berhasil Menyimpan Hak Appraisal');
            }else{
                  $this->session->set_flashdata('message', 'Gagal Menyimpan Hak Appraisal');
            }
            
            $btnaction = $this->input->post('btnAction');
            if ($btnaction == 'save_exit') {
                redirect(SITE_ADMIN . '/hrd_appraisal/due_appraisal', 'refresh');
            }
            else {
                redirect(SITE_ADMIN . '/hrd_appraisal/add_due_appraisal/', 'refresh');
            }
        }
        else {
            //display the create user form
            //set the flash data error message if there is one
            $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            $this->data['message_success'] = $this->session->flashdata('message_success');

            //get jabatan 
            $this->data['jobs']  = $this->hrd_model->get_all_jobs(false);
            $this->data['jobs_no_grantors']  = $this->hrd_model->get_all_jobs(array("is_grantor"=>0));
            //get template appraisal
            $this->data['template_appraisals']  = $this->hrd_model->get_all_template_appraisals(false);
            
            //load content
            $this->data['content'] .= $this->load->view('admin/hrd/appraisal_add_due', $this->data, true);
            $this->render('hrd');
        }
    }
    public function add_template_appraisal(){
        $this->data['title']    = "Tambah Template Appraisal";
        $this->data['subtitle'] = "Tambah Template Appraisal";

        //validate form input
        $this->form_validation->set_rules('name', 'Nama Template Appraisal', 'required|xss_clean|min_length[1]|max_length[50]');
        $this->form_validation->set_rules('description', 'Deskripsi', 'required|xss_clean|min_length[1]|max_length[50]');
      
        if ($this->form_validation->run() == true) { 
            // echo "<pre>";
            // print_r($_POST);
            $master_name = $this->input->post('name');
            $master_description = $this->input->post('description');
            $data_master = array(
                'name' => $master_name,
                'description' => $master_description
            );

            $save_master = $this->hrd_model->save('hr_appraisal_template', $data_master);
            //insert master 
            if($save_master){
                $categories = $this->input->post('category');
                $detail_categories = $this->input->post('detail_category');
                
                $return_save_category = true;
                $i = 0;
                foreach ($categories as $category) {
                    //insert master category
                    // echo $category."<br>"; 
                    $data_categories = array(
                        'appraisal_template_id' => $save_master,
                        'name_category' => $category
                    );

                    $save_category = $this->hrd_model->save('hr_appraisal_category', $data_categories);
                    if($save_category){
                        $return_save_detail_category = true;
                        foreach ($detail_categories[$i] as $detail_category) {  

                            $data_detail_category = array(
                                'appraisal_category_id' => $save_category,
                                'name' => $detail_category['name'],
                                'point' => $detail_category['point']
                            );

                            $save_detail_category = $this->hrd_model->save('hr_appraisal_detail_category', $data_detail_category);
                            
                            if($save_detail_category){
                                 $this->session->set_flashdata('message_success', 'Berhasil Menyimpan Template Appraisal');
                            }else{
                                $return_save_detail_category = false;
                                $this->session->set_flashdata('message', 'Gagal Menyimpan Master Detail Category Appraisal');
                            }
                        }  

                    }else{
                        $return_save_category = false;
                        $this->session->set_flashdata('message', 'Gagal Menyimpan Master Category Appraisal');
                    } 
                  $i++;
                } 
            }else{
                $this->session->set_flashdata('message', 'Gagal Menyimpan Master Template Appraisal');
            } 

            $btnaction = $this->input->post('btnAction');
            if ($btnaction == 'save_exit') {
                redirect(SITE_ADMIN . '/hrd_appraisal/setting_template_appraisal/', 'refresh');
            }
            else {
                redirect(SITE_ADMIN . '/hrd_appraisal/add_template_appraisal/', 'refresh');
            }


        }
        else {
            //display the create user form
            //set the flash data error message if there is one
            $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            $this->data['message_success'] = $this->session->flashdata('message_success');

            $this->data['name'] = array('name' => 'name',
                                              'id' => 'name',
                                              'type' => 'text',
                                              'class' => 'form-control no-special-char',
                                              'field-name' => 'name',
                                              'placeholder' => 'Nama Template Appraisal',
                                              'value' => $this->form_validation->set_value('name')); 


             $this->data['description'] = array('name' => 'description',
                                              'id' => 'description', 
                                              'field-name' => 'description', 
                                               'class' => 'form-control',
                                              'value' => $this->form_validation->set_value('description'));  
            
            //load content
            $this->data['content'] .= $this->load->view('admin/hrd/appraisal_add_template', $this->data, true);
            $this->render('hrd');
        }
    }


    public function add_process_appraisal(){
        $this->data['title']    = "Proses Appraisal";
        $this->data['subtitle'] = "Proses Appraisal";

        //validate form input
        $this->form_validation->set_rules('user_id', 'Pegawai', 'required|xss_clean|min_length[1]|max_length[50]');
        $this->form_validation->set_rules('template_id', 'Template', 'required|xss_clean|min_length[1]|max_length[50]');
        $this->form_validation->set_rules('period', 'Period', 'required|xss_clean');
        
        if ($this->form_validation->run() == true) { 
           
            $user_id = $this->input->post('user_id');
            $template_id = $this->input->post('template_id');
            $description = $this->input->post('description');
            $categories = $this->input->post('amount');
            $period = $this->input->post('period');
            
            $check_appraisal = $this->hrd_model->get_where('hr_appraisal_process', array(
                                                        "user_id"=>$user_id,
                                                        "template_id"=>$template_id,
                                                        "period"=>$period
                                                        )
                                                    );
            if(!empty($check_appraisal)){
                foreach ($check_appraisal as $appraisal) {
                    $check_audit_detail = $this->hrd_model->get_where('hr_appraisal_process_detail', array("appraisal_process_id"=>$appraisal->id));
                    foreach ($check_audit_detail as $detail_audit) {
                        $delete_detail_category =  $this->hrd_model->delete_by('hr_appraisal_process_detail_category', 
                                                                            array("appraisal_process_detail_id"=>$detail_audit->id));
                        
                    } 
                    $this->hrd_model->delete_by('hr_appraisal_process_detail', array("appraisal_process_id"=>$appraisal->id));
                }

                $this->hrd_model->delete_by('hr_appraisal_process', array("user_id"=>$user_id,"template_id"=>$template_id));

            }
            $data_master = array(
                    'user_id' => $user_id,
                    'template_id' => $template_id, 
                    'description' => $description,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $this->data['user_profile_data']->id,
                    "period"=>$period
            );

            $audit_process = $this->hrd_model->save('hr_appraisal_process', $data_master);

            foreach ($categories as $key =>$value) {
                $data_master = array(
                    'user_id' => $user_id,
                    'appraisal_process_id' => $audit_process,
                    'category_id' => $key 
                );

                $save_master = $this->hrd_model->save('hr_appraisal_process_detail', $data_master);
                foreach ($value as $detail_key => $detail_value) {
                    $data_detail = array( 
                        'appraisal_process_detail_id' => $save_master,
                        'detail_category_id' => $detail_key,
                        'value'=>$detail_value
                    );

                    $save_detail = $this->hrd_model->save('hr_appraisal_process_detail_category', $data_detail);
                }
            } 
            
            $btnaction = $this->input->post('btnAction');
            if ($btnaction == 'save_exit') {
                redirect(SITE_ADMIN . '/hrd_appraisal/process_appraisal/', 'refresh');
            }
            else {
                redirect(SITE_ADMIN . '/hrd_appraisal/add_process_appraisal/', 'refresh');
            }

        }
        else {
            //display the create user form
            //set the flash data error message if there is one
            $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            $this->data['message_success'] = $this->session->flashdata('message_success');
    
            $this->data['users']      = $this->hrd_model->get_user_dropdown();
            $this->data['template_appraisals']      = $this->hrd_model->get_template_appraisal_dropdown();

            //load content
            $this->data['content'] .= $this->load->view('admin/hrd/appraisal_add_process', $this->data, true);
            $this->render('hrd');
        }
    }
    public function view_template_appraisal(){
        $this->data['title']    = "View Template Appraisal";
        $this->data['subtitle'] = "View Template Appraisal"; 
        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success'); 

        $template_id = $this->uri->segment(4);

        $this->data['data_templates'] = $this->hrd_model->get_one('hr_appraisal_template', $template_id);
        
        $categories =  $this->hrd_model->get_where('hr_appraisal_category', array('appraisal_template_id'=>$template_id));
       
       
        if(!empty( $categories)){  
            foreach ($categories as $category) { 
                $category->detail = array();
                $data_detail_categories = $this->hrd_model->get_where('hr_appraisal_detail_category', array('appraisal_category_id'=>$category->id)); 
                foreach ($data_detail_categories as $detail_category) {
                    array_push( $category->detail,$detail_category); 
                } 
            } 
        } 
        $this->data['data_categories'] = $categories; 
        //load content
        $this->data['content'] .= $this->load->view('admin/hrd/appraisal_view_template', $this->data, true);
        $this->render('hrd');
        
    }
    public function edit_template_appraisal(){
        $this->data['title']    = "View Template Appraisal";
        $this->data['subtitle'] = "View Template Appraisal"; 
        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success'); 

        $template_id = $this->uri->segment(4);

        if (isset($_POST) && !empty($_POST)) { 

            $categories =  $this->hrd_model->get_where('hr_appraisal_category', array('appraisal_template_id'=>$template_id));
            
            //delete detail category
            $return_delete_detail_category = true;
            foreach ($categories as $category) { 
                $data_detail_categories = $this->hrd_model->delete_by('hr_appraisal_detail_category', array('appraisal_category_id'=>$category->id)); 
                if(!$data_detail_categories){
                    $return_delete_detail_category = false;
                }
            }


            if($return_delete_detail_category){
                $delete_categories = $this->hrd_model->delete_by('hr_appraisal_category', array('appraisal_template_id'=>$template_id)); 
                if($delete_categories){
                    $master_name = $this->input->post('name');
                    $master_description = $this->input->post('description');
                    $data_master = array(
                        'name' => $master_name,
                        'description' => $master_description
                    );

                    $save_master = $this->hrd_model->save('hr_appraisal_template', $data_master,$template_id);
                    //insert master 
                    if($save_master){
                        $categories = $this->input->post('category');
                        $detail_categories = $this->input->post('detail_category');
                        
                        $return_save_category = true;
                        $i = 0;
                        foreach ($categories as $category) {
                            //insert master category
                            // echo $category."<br>"; 
                            $data_categories = array(
                                'appraisal_template_id' => $save_master,
                                'name_category' => $category
                            );

                            $save_category = $this->hrd_model->save('hr_appraisal_category', $data_categories);
                            if($save_category){
                                $return_save_detail_category = true;
                                foreach ($detail_categories[$i] as $detail_category) {  

                                    $data_detail_category = array(
                                        'appraisal_category_id' => $save_category,
                                        'name' => $detail_category['name'],
                                        'point' => $detail_category['point']
                                    );

                                    $save_detail_category = $this->hrd_model->save('hr_appraisal_detail_category', $data_detail_category);
                                    
                                    if($save_detail_category){
                                         $this->session->set_flashdata('message_success', 'Berhasil Menyimpan Template Appraisal');
                                    }else{
                                        $return_save_detail_category = false;
                                        $this->session->set_flashdata('message', 'Gagal Menyimpan Master Detail Category Appraisal');
                                    }
                                }  

                            }else{
                                $return_save_category = false;
                                $this->session->set_flashdata('message', 'Gagal Menyimpan Master Category Appraisal');
                            } 
                          $i++;
                        } 
                    }else{
                        $this->session->set_flashdata('message', 'Gagal Menyimpan Master Template Appraisal');
                    } 

                    $btnaction = $this->input->post('btnAction');
                    if ($btnaction == 'save_exit') {
                        redirect(SITE_ADMIN . '/hrd_appraisal/setting_template_appraisal/', 'refresh');
                    }
                    else {
                        redirect(SITE_ADMIN . '/hrd_appraisal/add_template_appraisal/', 'refresh');
                    }
                }
            }
        }


        $this->data['data_templates'] = $this->hrd_model->get_one('hr_appraisal_template', $template_id);
        
        $categories =  $this->hrd_model->get_where('hr_appraisal_category', array('appraisal_template_id'=>$template_id));
       
       
        if(!empty( $categories)){  
            foreach ($categories as $category) { 
                $category->detail = array();
                $data_detail_categories = $this->hrd_model->get_where('hr_appraisal_detail_category', array('appraisal_category_id'=>$category->id)); 
                foreach ($data_detail_categories as $detail_category) {
                    array_push( $category->detail,$detail_category); 
                } 
            } 
        } 
        $this->data['data_categories'] = $categories; 
        //load content
        $this->data['content'] .= $this->load->view('admin/hrd/appraisal_edit_template', $this->data, true);
        $this->render('hrd');
        
    } 

    public function download_template_appraisal(){
        $template_id = $this->input->post('template_id');

        $this->data['data_templates'] = $this->hrd_model->get_one('hr_appraisal_template', $template_id);
        
        $categories =  $this->hrd_model->get_where('hr_appraisal_category', array('appraisal_template_id'=>$template_id));
       
       
        if(!empty( $categories)){  
            $total = 0;
            foreach ($categories as $category) { 
                $category->detail = array();
                $data_detail_categories = $this->hrd_model->get_where('hr_appraisal_detail_category', array('appraisal_category_id'=>$category->id)); 
                foreach ($data_detail_categories as $detail_category) {
                    array_push( $category->detail,$detail_category); 
                    $total += $detail_category->point;
                } 
            } 
        } 
        $this->data['data_categories'] = $categories; 
        $this->data['max_grade'] = $total;
      
        $this->data['content'] .= $this->load->view('admin/hrd/appraisal_format_template', $this->data, true); 
        echo $this->data['content'];
    }

    public function delete_template_appraisal(){
        $template_id = $this->uri->segment(4);
        if(!empty($template_id)){

        }

        $categories =  $this->hrd_model->get_where('hr_appraisal_category', array('appraisal_template_id'=>$template_id));
        
        //delete detail category
        $return_delete_detail_category = true;
        foreach ($categories as $category) { 
            $data_detail_categories = $this->hrd_model->delete_by('hr_appraisal_detail_category', array('appraisal_category_id'=>$category->id)); 
            if(!$data_detail_categories){
                $return_delete_detail_category = false;
            }
        }


        if($return_delete_detail_category){
            $delete_categories = $this->hrd_model->delete_by('hr_appraisal_category', array('appraisal_template_id'=>$template_id)); 
            if($delete_categories){
                $delete_template = $this->hrd_model->delete_by('hr_appraisal_template', array('id'=>$template_id)); 
                if($delete_template){
                     $this->session->set_flashdata('message_success', 'Berhasil Menghapus Template Appraisal');
                }else{
                     $this->session->set_flashdata('message', 'Gagal Menghapus Template Appraisal');
                }
            }
        }


          redirect(SITE_ADMIN . '/hrd_appraisal/setting_template_appraisal/', 'refresh');
    } 

    public function view_process_appraisal(){
        $this->data['title']    = "Hasil Appraisal";
        $this->data['subtitle'] = "Hasil Appraisal"; 
        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success'); 

        $process_id = $this->uri->segment(4);

        $data_process = $this->hrd_model->get_appraisal_process_byid($process_id);
        if(!empty($data_process)){
            $template_id = $data_process->template_id;
        }
        
        $this->data['data_process'] = $data_process;
        $this->data['data_grade'] = $this->hrd_model->get_appraisal_percentage_process_byid($process_id);
        
        $this->data['data_templates'] = $this->hrd_model->get_one('hr_appraisal_template', $template_id);
        
        $categories =  $this->hrd_model->get_where('hr_appraisal_category', array('appraisal_template_id'=>$template_id));
       
       
        if(!empty( $categories)){  
            foreach ($categories as $category) { 
                $category->detail = array();
                $data_detail_categories = $this->hrd_model->get_grade_appraisal($category->id); 
                foreach ($data_detail_categories as $detail_category) {
                    array_push( $category->detail,$detail_category); 
                } 
            } 
        } 
        $this->data['data_categories'] = $categories; 
        //load content
        $this->data['content'] .= $this->load->view('admin/hrd/appraisal_view_result', $this->data, true);
        $this->render('hrd');
        
    }

    public function delete_process_appraisal(){
        $process_id = $this->uri->segment(4);
        if(!empty($template_id)){

        }

        $categories =  $this->hrd_model->get_where('hr_appraisal_process_detail', array('appraisal_process_id'=>$process_id));
        
        //delete detail category
        $return_delete_detail_category = true;
        foreach ($categories as $category) { 
            $data_detail_categories = $this->hrd_model->delete_by('hr_appraisal_process_detail_category', 
                                                                array('appraisal_process_detail_id'=>$category->id)); 
            if(!$data_detail_categories){
                $return_delete_detail_category = false;
            }
        }


        if($return_delete_detail_category){
            $delete_categories = $this->hrd_model->delete_by('hr_appraisal_process_detail', array('appraisal_process_id'=>$process_id)); 
            if($delete_categories){
                $delete_template = $this->hrd_model->delete_by('hr_appraisal_process', array('id'=>$process_id)); 
                if($delete_template){
                     $this->session->set_flashdata('message_success', 'Berhasil Menghapus Hasil Appraisal');
                }else{
                     $this->session->set_flashdata('message', 'Gagal Menghapus Hasil Appraisal');
                }
            }
        }


          redirect(SITE_ADMIN . '/hrd_appraisal/process_appraisal/', 'refresh');
    }
}