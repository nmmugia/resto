<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

 
class Hrd_audit extends Hrd_Controller
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
         redirect(SITE_ADMIN . '/hrd_audit/setting_audit', 'refresh');
    }

    public function setting_audit()
    {
        $this->data['title']    = "Daftar Template Audit";
        $this->data['subtitle'] ="Daftar Template Audit";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

       
        $this->data['data_url'] = base_url(SITE_ADMIN . '/hrd_audit/get_data_template_audit');
       
        //load content
        $this->data['use_username'] = TRUE;
        $this->data['content'] .= $this->load->view('admin/hrd/audit_template_list', $this->data, true);
        $this->render('hrd');
    }
    
      public function process_audit()
    {
        $this->data['title']    = "Daftar Proses Audit";
        $this->data['subtitle'] ="Daftar Proses Audit";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

       
        $this->data['data_url'] = base_url(SITE_ADMIN . '/hrd_audit/get_data_process_list');
       
        //load content
        $this->data['use_username'] = TRUE;
        $this->data['content'] .= $this->load->view('admin/hrd/audit_process_list', $this->data, true);
        $this->render('hrd');
    }
    

    public function get_data_template_audit(){
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));  
        $this->load->helper(array('hrd'));
        $this->datatables->select('id,name,description')
            ->from('hr_audit_template')  
            ->add_column('actions', 
                "<div class='btn-group'>
                     <a href='" . base_url(SITE_ADMIN . '/hrd_audit/view_template_audit/$1')."' class='btn btn-default'  >View</a>
                </div>", 'id');
           
            
        echo $this->datatables->generate();
    } 

    public function get_data_process_list(){
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));  
        $this->load->helper(array('hrd'));
        $this->datatables->select('hap.id as audit_process_id,
                store.store_name,hap.created_at, hat.name as template_name,hap.description,hap.period')
            ->from('hr_audit_process hap') 
            ->join('store','hap.store_id = store.id')
            ->join('hr_audit_template hat','hap.template_id = hat.id')  
            ->add_column('actions', 
                "<div class='btn-group'>
                    <a href='" . base_url(SITE_ADMIN . '/hrd_audit/view_process_audit/$1')."' class='btn btn-default'  >View</a>
                    <a href='" . base_url(SITE_ADMIN . '/hrd_audit/delete_process_audit/$1')."' class='btn btn-danger deleteNow' rel='Hasil Audit'>Delete</a>
                </div>", 'audit_process_id');
           
            
        echo $this->datatables->generate();
    } 
    public function add_template_audit(){
        $this->data['title']    = "Tambah Template Audit";
        $this->data['subtitle'] = "Tambah Template Audit";

        //validate form input
        $this->form_validation->set_rules('name', 'Nama Template Audit', 'required|xss_clean|min_length[1]|max_length[50]');
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

            $save_master = $this->hrd_model->save('hr_audit_template', $data_master);
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
                        'audit_template_id' => $save_master,
                        'name_category' => $category
                    );

                    $save_category = $this->hrd_model->save('hr_audit_template_category', $data_categories);
                    if($save_category){
                        $return_save_detail_category = true;
                        foreach ($detail_categories[$i] as $detail_category) { 

                            // echo $detail_category['name']."---".$detail_category['point'];
                            // echo "<br>";

                            $data_detail_category = array(
                                'audit_category_id' => $save_category,
                                'name' => $detail_category['name'],
                                'point' => $detail_category['point'],
                            );

                            $save_detail_category = $this->hrd_model->save('hr_audit_template_detail_category', $data_detail_category);
                            
                            if($save_detail_category){
                                 $this->session->set_flashdata('message_success', 'Berhasil Menyimpan Template Audit');
                            }else{
                                $return_save_detail_category = false;
                                $this->session->set_flashdata('message', 'Gagal Menyimpan Master Detail Category Audit');
                            }
                        }  

                    }else{
                        $return_save_category = false;
                        $this->session->set_flashdata('message', 'Gagal Menyimpan Master Category Audit');
                    } 
                  $i++;
                } 
            }else{
                $this->session->set_flashdata('message', 'Gagal Menyimpan Master Template Audit');
            } 

            $btnaction = $this->input->post('btnAction');
            if ($btnaction == 'save_exit') {
                redirect(SITE_ADMIN . '/hrd_audit/setting_audit/', 'refresh');
            }
            else {
                redirect(SITE_ADMIN . '/hrd_audit/add_template_audit/', 'refresh');
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
                                              'placeholder' => 'Nama Template Audit',
                                              'value' => $this->form_validation->set_value('name')); 


             $this->data['description'] = array('name' => 'description',
                                              'id' => 'description', 
                                              'field-name' => 'description', 
                                               'class' => 'form-control',
                                              'value' => $this->form_validation->set_value('description'));  
            
            //load content
            $this->data['content'] .= $this->load->view('admin/hrd/audit_add_template', $this->data, true);
            $this->render('hrd');
        }
    }


    public function add_process_audit(){
        $this->data['title']    = "Proses Audit";
        $this->data['subtitle'] = "Proses Audit";

        //validate form input
        // $this->form_validation->set_rules('store_id', 'Resto', 'required|xss_clean|min_length[1]|max_length[50]');
        $this->form_validation->set_rules('template_id', 'Template', 'required|xss_clean|min_length[1]|max_length[50]');
        $this->form_validation->set_rules('period', 'Period', 'required|xss_clean');
        
        if ($this->form_validation->run() == true) { 
           
            $store_id = $this->data['setting']['store_id'];
            $template_id = $this->input->post('template_id');
            $description = $this->input->post('description');
            $categories = $this->input->post('amount');
            $period = $this->input->post('period');
            
            $check_audit = $this->hrd_model->get_where('hr_audit_process', array(
                                                        "store_id"=>$store_id,
                                                        "template_id"=>$template_id,
                                                        "period"=>$period
                                                        )
                                                    );
            if(!empty($check_audit)){
                foreach ($check_audit as $audit) {
                    $check_audit_detail = $this->hrd_model->get_where('hr_audit_process_detail', array("audit_process_id"=>$audit->id));
                    foreach ($check_audit_detail as $detail_audit) {
                        $delete_detail_category =  $this->hrd_model->delete_by('hr_audit_process_detail_category', 
                                                                            array("audit_process_detail_id"=>$detail_audit->id));
                        
                    } 
                    $this->hrd_model->delete_by('hr_audit_process_detail', array("audit_process_id"=>$audit->id));
                }

                $this->hrd_model->delete_by('hr_audit_process', array("store_id"=>$store_id,"template_id"=>$template_id));

            }
            $data_master = array(
                    'store_id' => $store_id,
                    'template_id' => $template_id, 
                    'description' => $description,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $this->data['user_profile_data']->id,
                      "period"=>$period
            );

            $audit_process = $this->hrd_model->save('hr_audit_process', $data_master);

            foreach ($categories as $key =>$value) {
                $data_master = array(
                    'store_id' => $store_id,
                    'audit_process_id' => $audit_process,
                    'category_id' => $key 
                );

                $save_master = $this->hrd_model->save('hr_audit_process_detail', $data_master);
                foreach ($value as $detail_key => $detail_value) {
                    $data_detail = array( 
                        'audit_process_detail_id' => $save_master,
                        'detail_category_id' => $detail_key,
                        'value'=>$detail_value
                    );

                    $save_detail = $this->hrd_model->save('hr_audit_process_detail_category', $data_detail);
                }
            } 
            
            $btnaction = $this->input->post('btnAction');
            if ($btnaction == 'save_exit') {
                redirect(SITE_ADMIN . '/hrd_audit/process_audit/', 'refresh');
            }
            else {
                redirect(SITE_ADMIN . '/hrd_audit/add_process_audit/', 'refresh');
            }


        }
        else {
            //display the create user form
            //set the flash data error message if there is one
            $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            $this->data['message_success'] = $this->session->flashdata('message_success');
    
            $this->data['template_audits']      = $this->hrd_model->get_template_dropdown();

            //load content
            $this->data['content'] .= $this->load->view('admin/hrd/audit_add_process', $this->data, true);
            $this->render('hrd');
        }
    }

    public function edit_template_audit(){
        $this->data['title']    = "Edit Template Audit";
        $this->data['subtitle'] = "Edit Template Audit"; 
        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success'); 

        $template_id = $this->uri->segment(4);

        if (isset($_POST) && !empty($_POST)) { 
            $categories =  $this->hrd_model->get_where('hr_audit_template_category', array('audit_template_id'=>$template_id));
        
            //delete detail category
            $return_delete_detail_category = true;
            foreach ($categories as $category) { 
                $data_detail_categories = $this->hrd_model->delete_by('hr_audit_template_detail_category', array('audit_category_id'=>$category->id)); 
                if(!$data_detail_categories){
                    $return_delete_detail_category = false;
                }
            }


            if($return_delete_detail_category){
                $delete_categories = $this->hrd_model->delete_by('hr_audit_template_category', array('audit_template_id'=>$template_id)); 
                if($delete_categories){
                    $master_name = $this->input->post('name');
                    $master_description = $this->input->post('description');
                    $data_master = array(
                        'name' => $master_name,
                        'description' => $master_description
                    );

                    $save_master = $this->hrd_model->save('hr_audit_template', $data_master,$template_id);
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
                                'audit_template_id' => $save_master,
                                'name_category' => $category
                            );

                            $save_category = $this->hrd_model->save('hr_audit_template_category', $data_categories);
                            if($save_category){
                                $return_save_detail_category = true;
                                foreach ($detail_categories[$i] as $detail_category) { 

                                    // echo $detail_category['name']."---".$detail_category['point'];
                                    // echo "<br>";

                                    $data_detail_category = array(
                                        'audit_category_id' => $save_category,
                                        'name' => $detail_category['name'],
                                        'point' => $detail_category['point'],
                                    );

                                    $save_detail_category = $this->hrd_model->save('hr_audit_template_detail_category', $data_detail_category);
                                    
                                    if($save_detail_category){
                                         $this->session->set_flashdata('message_success', 'Berhasil Menyimpan Template Audit');
                                    }else{
                                        $return_save_detail_category = false;
                                        $this->session->set_flashdata('message', 'Gagal Menyimpan Master Detail Category Audit');
                                    }
                                }  

                            }else{
                                $return_save_category = false;
                                $this->session->set_flashdata('message', 'Gagal Menyimpan Master Category Audit');
                            } 
                          $i++;
                        } 
                    }else{
                        $this->session->set_flashdata('message', 'Gagal Menyimpan Master Template Audit');
                    } 
                  
                    $btnaction = $this->input->post('btnAction');
                    if ($btnaction == 'save_exit') {
                        redirect(SITE_ADMIN . '/hrd_audit/setting_audit/', 'refresh');
                    }
                    else {
                        redirect(SITE_ADMIN . '/hrd_audit/edit_template_audit/'.$template_id, 'refresh');
                    }
                }
            }


           
        }
        $this->data['template_id'] = $template_id;
        $this->data['data_templates'] = $this->hrd_model->get_one('hr_audit_template', $template_id);
        
        $categories =  $this->hrd_model->get_where('hr_audit_template_category', array('audit_template_id'=>$template_id));
       
       
        if(!empty( $categories)){  
            foreach ($categories as $category) { 
                $category->detail = array();
                $data_detail_categories = $this->hrd_model->get_where('hr_audit_template_detail_category', array('audit_category_id'=>$category->id)); 
                foreach ($data_detail_categories as $detail_category) {
                    array_push( $category->detail,$detail_category); 
                } 
            } 
        } 
        $this->data['data_categories'] = $categories; 
        //load content
        $this->data['content'] .= $this->load->view('admin/hrd/audit_edit_template', $this->data, true);
        $this->render('hrd');
        
    }


    public function view_template_audit(){
        $this->data['title']    = "View Template Audit";
        $this->data['subtitle'] = "View Template Audit"; 
        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success'); 

        $template_id = $this->uri->segment(4);

        $this->data['data_templates'] = $this->hrd_model->get_one('hr_audit_template', $template_id);
        
        $categories =  $this->hrd_model->get_where('hr_audit_template_category', array('audit_template_id'=>$template_id));
       
       
        if(!empty( $categories)){  
            foreach ($categories as $category) { 
                $category->detail = array();
                $data_detail_categories = $this->hrd_model->get_where('hr_audit_template_detail_category', array('audit_category_id'=>$category->id)); 
                foreach ($data_detail_categories as $detail_category) {
                    array_push( $category->detail,$detail_category); 
                } 
            } 
        } 
        $this->data['data_categories'] = $categories; 
        //load content
        $this->data['content'] .= $this->load->view('admin/hrd/audit_view_template', $this->data, true);
        $this->render('hrd');
        
    }

   
    public function view_process_audit(){
        $this->data['title']    = "Hasil Audit";
        $this->data['subtitle'] = "Hasil Audit"; 
        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success'); 

        
        $process_id = $this->uri->segment(4);

        $data_process = $this->hrd_model->get_audit_process_byid($process_id);
        if(!empty($data_process)){
            $template_id = $data_process->template_id;
        }
        
        $this->data['data_process'] = $data_process;
        $this->data['data_grade'] = $this->hrd_model->get_audit_percentage_process_byid($process_id);
        
        $this->data['data_templates'] = $this->hrd_model->get_one('hr_audit_template', $template_id);
        
        $categories =  $this->hrd_model->get_where('hr_audit_template_category', array('audit_template_id'=>$template_id));
       
       
        if(!empty( $categories)){  
            foreach ($categories as $category) { 
                $category->detail = array();
                $data_detail_categories = $this->hrd_model->get_grade_audit($category->id); 
                foreach ($data_detail_categories as $detail_category) {
                    array_push( $category->detail,$detail_category); 
                } 
            } 
        } 
        $this->data['data_categories'] = $categories; 
        //load content
        $this->data['content'] .= $this->load->view('admin/hrd/audit_view_result', $this->data, true);
        $this->render('hrd');
        
    }

    public function download_template(){
        $template_id = $this->input->post('template_id');

        $this->data['data_templates'] = $this->hrd_model->get_one('hr_audit_template', $template_id);
        
        $categories =  $this->hrd_model->get_where('hr_audit_template_category', array('audit_template_id'=>$template_id));
       
       
        if(!empty( $categories)){  
            $total = 0;
            foreach ($categories as $category) { 
                $category->detail = array();
                $data_detail_categories = $this->hrd_model->get_where('hr_audit_template_detail_category', array('audit_category_id'=>$category->id)); 
                foreach ($data_detail_categories as $detail_category) {
                    array_push( $category->detail,$detail_category); 
                    $total += $detail_category->point;
                } 
            } 
        } 
        $this->data['data_categories'] = $categories; 
        $this->data['max_grade'] = $total;
      
        $this->data['content'] .= $this->load->view('admin/hrd/audit_format_template', $this->data, true); 
        echo $this->data['content'];
    }

    public function delete_template_audit(){
        $template_id = $this->uri->segment(4);
        if(!empty($template_id)){

        }

        $categories =  $this->hrd_model->get_where('hr_audit_template_category', array('audit_template_id'=>$template_id));
        
        //delete detail category
        $return_delete_detail_category = true;
        foreach ($categories as $category) { 
            $data_detail_categories = $this->hrd_model->delete_by('hr_audit_template_detail_category', array('audit_category_id'=>$category->id)); 
            if(!$data_detail_categories){
                $return_delete_detail_category = false;
            }
        }


        if($return_delete_detail_category){
            $delete_categories = $this->hrd_model->delete_by('hr_audit_template_category', array('audit_template_id'=>$template_id)); 
            if($delete_categories){
                $delete_template = $this->hrd_model->delete_by('hr_audit_template', array('id'=>$template_id)); 
                if($delete_template){
                     $this->session->set_flashdata('message_success', 'Berhasil Menghapus Template Audit');
                }else{
                     $this->session->set_flashdata('message', 'Gagal Menghapus Template Audit');
                }
            }
        }


          redirect(SITE_ADMIN . '/hrd_audit/setting_audit/', 'refresh');
    }


    public function delete_process_audit(){
        $process_id = $this->uri->segment(4);
        if(!empty($template_id)){

        }

        $categories =  $this->hrd_model->get_where('hr_audit_process_detail', array('audit_process_id'=>$process_id));
        
        //delete detail category
        $return_delete_detail_category = true;
        foreach ($categories as $category) { 
            $data_detail_categories = $this->hrd_model->delete_by('hr_audit_process_detail_category', 
                                                                array('audit_process_detail_id'=>$category->id)); 
            if(!$data_detail_categories){
                $return_delete_detail_category = false;
            }
        }


        if($return_delete_detail_category){
            $delete_categories = $this->hrd_model->delete_by('hr_audit_process_detail', array('audit_process_id'=>$process_id)); 
            if($delete_categories){
                $delete_template = $this->hrd_model->delete_by('hr_audit_process', array('id'=>$process_id)); 
                if($delete_template){
                     $this->session->set_flashdata('message_success', 'Berhasil Menghapus Hasil Audit');
                }else{
                     $this->session->set_flashdata('message', 'Gagal Menghapus Hasil Audit');
                }
            }
        }


          redirect(SITE_ADMIN . '/hrd_audit/process_audit/', 'refresh');
    }
}