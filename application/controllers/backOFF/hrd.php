<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Hrd extends Hrd_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) { 
            redirect(SITE_ADMIN . '/login', 'refresh');
        }else{ 
            $this->load->model('categories_model');
        } 
    }

    public function index()
    {
        redirect(SITE_ADMIN . '/');
    } 
    public function setting_office_hour_rolling()
		{
			if($_POST){
				$office_hour_id=$this->input->post("office_hour_id");
				$target_office_hour_id=$this->input->post("target_office_hour_id");
				$this->hrd_model->delete_by_limit("hr_office_hour_rolling",array("office_hour_id"=>$office_hour_id),0);
				foreach($target_office_hour_id as $a){
					$this->hrd_model->save("hr_office_hour_rolling",array(
						"office_hour_id"=>$office_hour_id,
						"office_hour_target_id"=>$a,
					));
				}
				$this->session->set_flashdata('message_success', 'Berhasil Menyimpan Pengaturan Pergantian Jam Kerja');
				$btnaction = $this->input->post('btnAction');
				redirect(base_url(SITE_ADMIN . '/hrd/setting_office_hour_rolling'), 'refresh');
			}
			$this->data['title']    = "Atur Pergantian Jam Kerja";
			$this->data['subtitle'] = "Atur Pergantian Jam Kerja";
			$this->data['message']         = $this->session->flashdata('message');
			$this->data['message_success'] = $this->session->flashdata('message_success');			
			$this->data['office_hour_lists']=$this->hrd_model->get("hr_office_hours")->result();
			$this->data['content'] .= $this->load->view('admin/hrd/setting_office_hour_rolling', $this->data, true);
			$this->render('hrd'); 
		}
		public function get_data_office_hour_rolling()
		{
			$office_hour_id=$this->input->post("office_hour_id");
			$office_hour_from=$this->hrd_model->get_all_where("hr_office_hours",array("id !="=>$office_hour_id));
			$office_hour_target=$this->hrd_model->get_office_hour_target(array("office_hour_id"=>$office_hour_id));
			$content_to="";
			$target_ids=array();
      foreach($office_hour_target as $r){
        $content_to.="<option value='".$r->id."'>".$r->name."</option>";
				array_push($target_ids,$r->id);
      }
			$content_from="";
      foreach($office_hour_from as $r){
				if(!in_array($r->id,$target_ids)){					
					$content_from.="<option value='".$r->id."'>".$r->name."</option>";
				}
      }
      echo json_encode(array(
        "content_from"=>$content_from,
        "content_to"=>$content_to,
      ));
		}
    public function setting_employee_affair_list()
    {  
        $this->data['title']    = "Status Kepegawaian";
        $this->data['subtitle'] = "Status Kepegawaian";  
        $this->data['content'] .= $this->load->view('admin/hrd/setting_employee_affair_view', $this->data, true);
        $this->render('hrd'); 
    }

    public function get_data_empl_affair()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $this->datatables->select('id, name,during')->from('hr_enum_employee_affair')
                            ->add_column('actions', "<div class='btn-group'>
                                    <button   class='btn btn-default edit-employee-affair'   employee-affair-id='$1'><i class='fa fa-pencil'></i> Edit</button>
                                    <button  class='btn btn-danger delete-employee-affair' rel='Status Kepegawaian'  employee-affair-id='$1'><i class='fa fa-trash-o'></i> Delete</button>
                                </div>", 'id'); 
        echo $this->datatables->generate();
    }

    public function save_employee_affair(){
        $employee_affair_name =  $this->input->post('emp_affair_name');
        $during =  $this->input->post('during');
        $next_job =  $this->input->post('next_job');
        $return_data = array();
        $return_data['status'] = false;
        $return_data['data'] = array();
        $return_data['message'] = ""; 

        if(empty($employee_affair_name)){
            $return_data['message'] = "Parameter Kosong";  
        }else{
            $data = array('name' =>$employee_affair_name,'during' =>$during,'next_job' =>$next_job);
            $save = $this->hrd_model->save_employee_affair($data);
            if($save){
                $return_data['status'] = true;
            }else{
                $return_data['message'] = "Maaf, Status Kepegawaian Gagal Di simpan. Silahkan Hubungi Administrator";  
            }

            echo json_encode($return_data);
        }
       
    }
    public function update_employee_affair(){
        $employee_affair_name =  $this->input->post('emp_affair_name');
        $during =  $this->input->post('during');
        $employee_affair_id =  $this->input->post('emp_affair_id');
         $next_job =  $this->input->post('next_job');
        $return_data = array();
        $return_data['status'] = false;
        $return_data['data'] = array();
        $return_data['message'] = ""; 

        if(empty($employee_affair_name)){
            $return_data['message'] = "Parameter Kosong"; 
             echo json_encode($return_data); 
        }else{
            $data = array('name' =>$employee_affair_name,'during' =>$during,'next_job' =>$next_job);
            $save = $this->hrd_model->update_employee_affair($employee_affair_id,$data);
            if($save){
                $return_data['status'] = true;
            }else{
                $return_data['message'] = "Maaf, Status Kepegawaian Gagal Di simpan. Silahkan Hubungi Administrator";  
            }

            echo json_encode($return_data);
        }
        
    }
    public function delete_employee_affair(){
        $employee_affair_id =  $this->input->post('emp_affair_id');
        $return_data['status'] = false;
        $return_data['data'] = array();
        $return_data['message'] = ""; 

        if(empty($employee_affair_id)){
            $return_data['message'] = "Parameter Kosong"; 
             echo json_encode($return_data); 
        }else{
            $data_employee_affair = $this->hrd_model->check_enum_employee_affair(array('e_affair_id' => $employee_affair_id));
            if($data_employee_affair){
                $return_data['message'] = "Maaf,data master tidak dapat  dihapus karena berkaitan dengan data lain"; 
            }else{
                $status = $this->hrd_model->delete_employee_affair(array('id' => $employee_affair_id));
                if($status){
                    $return_data['status'] = true;
                }else{
                    $return_data['message'] = "Maaf, Status Kepegawaian Gagal Di Hapus. Silahkan Hubungi Administrator";  
                }
            } 
            echo json_encode($return_data);
        } 
        
    }


    public function get_one_employee_affair(){
        $emp_affair_id =  $this->input->post('emp_affair_id');
        $return_data = array();
        $return_data['status'] = false;
        $return_data['data'] = array();
        $return_data['message'] = ""; 

        if(empty($employee_affair_name)){
            $return_data['message'] = "Parameter Kosong";  
        }
        
        $data_employee = $this->hrd_model->get_one('hr_enum_employee_affair', $emp_affair_id);
        $next_jobs = $this->hrd_model->get_all_where('hr_enum_employee_affair', array("id !="=>$emp_affair_id));
        if($data_employee){
            $return_data['status'] = true;
            $return_data['data'] = $data_employee;
            $return_data['next_job'] = $next_jobs;
        }else{
            $return_data['message'] = "Maaf, Gagal Mengambil data status Kepegawaian. Silahkan Hubungi Administrator";  
        }

        echo json_encode($return_data);
    }

     public function get_all_employee_affair(){
         
        $return_data = array();
        $return_data['status'] = false;
        $return_data['data'] = array();
        $return_data['message'] = "";  

        $next_jobs = $this->hrd_model->get('hr_enum_employee_affair')->result();
        if($next_jobs){
            $return_data['status'] = true; 
            $return_data['next_job'] = $next_jobs;
        }else{
            $return_data['message'] = "Maaf, Gagal Mengambil data status Kepegawaian. Silahkan Hubungi Administrator";  
        }

        echo json_encode($return_data);
    }

    public function save_memorandum(){
        $memorandum_name =  $this->input->post('memorandum_name');
        $memorandum_period =  $this->input->post('memorandum_period');
        $return_data = array();
        $return_data['status'] = true;
        $return_data['data'] = array();
        $return_data['message'] = ""; 

        if(empty($memorandum_name)){
            $return_data['status'] = false;
            $return_data['message'] = "Parameter Name Kosong";  
        }

        if(empty($memorandum_period)){
            $return_data['status'] = false;
            $return_data['message'] = "Parameter Period Kosong";  
        }


        if($return_data['status']){
            $data = array(
                'name' =>$memorandum_name,
                'period' =>$memorandum_period
            );
            $save = $this->hrd_model->save_memorandum($data);
            if($save){
                $return_data['status'] = true;
            }else{
                $return_data['message'] = "Maaf, Status Kepegawaian Gagal Di simpan. Silahkan Hubungi Administrator";  
            } 
        }

        echo json_encode($return_data);
       
    }

    public function delete_memorandum(){
        $memorandum_id =  $this->input->post('memorandum_id');
        $return_data['status'] = false;
        $return_data['data'] = array();
        $return_data['message'] = ""; 

        if(empty($memorandum_id)){
            $return_data['message'] = "Parameter Kosong"; 
             echo json_encode($return_data); 
        }else{
            $status = $this->hrd_model->delete_memorandum(array('id' => $memorandum_id));
            if($status){
                $return_data['status'] = true;
            }else{
                $return_data['message'] = "Maaf, Surat Peringatan Gagal Di Hapus. Silahkan Hubungi Administrator";  
            }

            echo json_encode($return_data);
        } 
        
    }

    public function get_one_memorandum(){
        $memorandum_id =  $this->input->post('memorandum_id');
        $return_data = array();
        $return_data['status'] = false;
        $return_data['data'] = array();
        $return_data['message'] = ""; 

        if(empty($employee_affair_name)){
            $return_data['message'] = "Parameter Kosong";  
        }
        
       $data_employee = $this->hrd_model->get_one('hr_memorandum', $memorandum_id);
        if($data_employee){
            $return_data['status'] = true;
            $return_data['data'] = $data_employee;
        }else{
            $return_data['message'] = "Maaf, Gagal Mengambil data Surat Peringatan. Silahkan Hubungi Administrator";  
        }

        echo json_encode($return_data);
    }
    public function update_memorandum(){
        $memorandum_name =  $this->input->post('memorandum_name');
        $memorandum_period =  $this->input->post('memorandum_period');
        $memorandum_id =  $this->input->post('memorandum_id');
        $return_data = array();
        $return_data['status'] = true;
        $return_data['data'] = array();
        $return_data['message'] = ""; 

        if(empty($memorandum_name)){
            $return_data['status'] = false;
            $return_data['message'] = "Parameter Name Kosong"; 
             
        }

        if(empty($memorandum_period)){
            $return_data['status'] = false;
            $return_data['message'] = "Parameter Period Kosong"; 
             
        }

        if($return_data['status']){
            $data = array(
                'name' =>$memorandum_name,
                'period' =>$memorandum_period
            );
            $save = $this->hrd_model->update_memorandum($memorandum_id,$data); 
            if($save){
                $return_data['status'] = true;
            }else{
                $return_data['status'] = false;
                $return_data['message'] = "Maaf, Surat Peringatan Gagal Di simpan. Silahkan Hubungi Administrator";  
            }  
        }
        
        echo json_encode($return_data);
    }
    public function setting_salary_component_list(){
        $this->data['title']    = "Komponen Gaji";
        $this->data['subtitle'] = "Komponen Gaji";
        $this->data['content'] .= $this->load->view('admin/hrd/setting_salary_component_view', $this->data, true);
        $this->render('hrd'); 
    }

    public function get_data_salary_component(){
        $this->load->library(array('datatables')); 
        $this->load->helper(array('hrd'));

        $this->datatables->select('id,name,is_enhancer,key,formula_default')->from('hr_salary_component')

                            ->unset_column('is_enhancer')
                            ->add_column('is_enhancer', '$1', 'set_enum_enhancer(is_enhancer)') 
                            ->add_column('actions', "<div class='btn-group'>
                                    <a href='" . base_url(SITE_ADMIN . '/hrd/edit_salary_component/$1')."' class='btn btn-default edit-salary-component'  salary-component-id='$1'><i class='fa fa-pencil'></i> Edit</a>
                                    <button class='btn btn-danger delete-salary-component'  salary-component-id='$1'><i class='fa fa-trash-o'></i> Delete</button>
                                </div>", 'id')
                             ->where('is_static',0); 
        echo $this->datatables->generate();
    }
    public function delete_salary_component(){
        $salary_component_id =  $this->input->post('salary_component_id');
        $return_data['status'] = false;
        $return_data['data'] = array();
        $return_data['message'] = ""; 

        if(empty($salary_component_id)){
            $return_data['message'] = "Parameter Kosong"; 
             echo json_encode($return_data); 
        }else{
            $status = $this->hrd_model->delete_salary_component(array('id' => $salary_component_id));
            if($status){
                $return_data['status'] = true;
            }else{
                $return_data['message'] = "Maaf, Komponen Gaji Gagal Di Hapus. Silahkan Hubungi Administrator";  
            }

            echo json_encode($return_data);
        } 
        
    }

    public function add_salary_component(){
        $this->data['title']    = "Tambah komponen Gaji";
        $this->data['subtitle'] = "Tambah komponen Gaji";

        //validate form input
        $this->form_validation->set_rules('name', 'Nama Komponen Gaji', 'required|xss_clean|min_length[1]|max_length[50]');
        // $this->form_validation->set_rules('key', 'key Komponen Gaji', 'required|xss_clean|min_length[1]|max_length[50]');
        // $this->form_validation->set_rules('formula_default', 'Rumus Default Komponen Gaji', 'required|xss_clean|min_length[1]|max_length[50]');
        

        if ($this->form_validation->run() == true) {
            $is_enhancer = $this->input->post('is_enhancer');
            if($is_enhancer == "0"){
                $is_enhancer = -1;
            }
            $array = array('name' => $this->input->post('name'),
                           'key' => $this->input->post('key'),
                           'formula_default' => $this->input->post('formula_default'), 
                           'is_enhancer' => $is_enhancer
                    );

            $save = $this->categories_model->save('hr_salary_component', $array);

            if ($save === false) {
                $this->session->set_flashdata('message', 'Gagal Menyimpan Komponen Gaji');
            }
            else {
                $this->session->set_flashdata('message_success', 'Berhasil Menyimpan Komponen Gaji');
            }


            $btnaction = $this->input->post('btnAction');
            if ($btnaction == 'save_exit') {
                redirect(SITE_ADMIN . '/hrd/setting_salary_component_list', 'refresh');
            }
            else {
                redirect(SITE_ADMIN . '/hrd/add_salary_component/', 'refresh');
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
                                              'class' => 'form-control only_alpha_numeric',
                                              'field-name' => 'name',
                                              'placeholder' => 'Nama Komponen Gaji',
                                              'value' => $this->form_validation->set_value('name')); 


             $this->data['key'] = array('name' => 'key',
                                              'id' => 'key', 
                                              'field-name' => 'key', 
                                               'class' => 'form-control',
                                              'value' => $this->form_validation->set_value('key')); 

            $this->data['formula_default'] = array('name' => 'formula_default',
                                              'id' => 'formula_default', 
                                              'field-name' => 'formula_default', 
                                               'class' => 'form-control',
                                              'value' => $this->form_validation->set_value('formula_default')); 
            $this->data['is_enhancer_on'] = array('name' => 'is_enhancer',
                'type' => 'radio',
                'class' => 'requiredTextField',
                'field-name' => 'Penambah',
                'placeholder' => '',
                'checked'=> ($this->form_validation->set_value('is_enhancer') == 1) ? 'true': '',
                'value' => "1");
            
            $this->data['is_enhancer_off'] = array('name' => 'is_enhancer',
                'type' => 'radio',
                'class' => 'requiredTextField',
                'field-name' => 'Pengurang',
                'placeholder' => '',
                'checked'=> ($this->form_validation->set_value('is_enhancer') == 0) ? 'true': '',
                'value' => "0");

            //load content
            $this->data['content'] .= $this->load->view('admin/hrd/setting_salary_component_add', $this->data, true);
            $this->render('hrd');
        }
    }

    public function edit_salary_component(){
        
        $id = $this->uri->segment(4);

        if (empty($id)) {
            redirect(SITE_ADMIN . '/hrd/setting_salary_component_list');
        }

        $form_data = $this->hrd_model->get_one('hr_salary_component', $id);

        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/hrd/setting_salary_component_list');
        }

        $this->data['form_data'] = $form_data;
        $this->data['subtitle']  = "Edit Komponen Gaji";

        //validate form input
        $this->form_validation->set_rules('name', 'Nama Komponen Gaji', 'required|xss_clean|min_length[1]|max_length[50]');
        $this->form_validation->set_rules('key', 'key Komponen Gaji', 'required|xss_clean|min_length[1]|max_length[50]');
        $this->form_validation->set_rules('formula_default', 'Rumus Default Komponen Gaji', 'required|xss_clean|min_length[1]|max_length[50]');
        
        if (isset($_POST) && ! empty($_POST)) {

            if ($this->form_validation->run() === TRUE) {
                $is_enhancer = $this->input->post('is_enhancer');
                if($is_enhancer == "0"){
                    $is_enhancer = -1;
                }

                 $array = array('name' => $this->input->post('name'),
                           'key' => $this->input->post('key'),
                           'formula_default' => $this->input->post('formula_default'), 
                           'is_enhancer' => $is_enhancer
                    );

                $save = $this->categories_model->save('hr_salary_component', $array, $id);

                if ($save === false) {
                    $this->session->set_flashdata('message', 'Gagal Menyimpan Komponen Gaji');
                }
                else {
                    $this->session->set_flashdata('message_success', 'Berhasil Menyimpan Komponen Gaji');
                }


                $btnaction = $this->input->post('btnAction');
                if ($btnaction == 'save_exit') {
                    redirect(SITE_ADMIN . '/hrd/setting_salary_component_list', 'refresh');
                }
                else {
                    redirect(SITE_ADMIN . '/hrd/edit_salary_component/' . $id, 'refresh');
                }


            }
        }

        //display the edit user form
        $this->data['csrf'] = $this->_get_csrf_nonce();

        //set the flash data error message if there is one
        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success'); 
        
        $this->data['name'] = array('name' => 'name',
                                              'id' => 'name',
                                              'type' => 'text',
                                              'class' => 'form-control char-only',
                                              'field-name' => 'name',
                                              'placeholder' => 'Nama Komponen Gaji', 
                                              'value' => $this->form_validation->set_value('name', $form_data->name)); 


        $this->data['key'] = array('name' => 'key',
                                      'id' => 'key', 
                                      'field-name' => 'key', 
                                      'class' => 'form-control',
                                      'value' => $this->form_validation->set_value('key', $form_data->key)); 

        $this->data['formula_default'] = array('name' => 'formula_default',
                                      'id' => 'formula_default', 
                                      'field-name' => 'formula_default',
                                      'class' => 'form-control', 
                                      'value' => $this->form_validation->set_value('formula_default', $form_data->formula_default)); 
        $this->data['is_enhancer_on'] = array('name' => 'is_enhancer',
                'type' => 'radio',
                'class' => 'requiredTextField',
                'field-name' => 'Penambah',
                'placeholder' => '',
                'checked'=> ($form_data->is_enhancer == 1) ? 'true': '',
                'value' => "1");
            
        $this->data['is_enhancer_off'] = array('name' => 'is_enhancer',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Pengurang',
            'placeholder' => '',
            'checked'=> ($form_data->is_enhancer == -1) ? 'true': '',
            'value' => "0");



        $this->data['content'] .= $this->load->view('admin/hrd/setting_salary_component_edit.php', $this->data, true);

        $this->render('hrd');
    }

    public function set_salary_component(){
        $id = $this->uri->segment(4);

        $this->data['subtitle']  = "Setting Komponen Gaji";

        if (empty($id)) {
            redirect(SITE_ADMIN . '/hrd/setting_jobs_list');
        } 
        $form_data = $this->hrd_model->get_one('hr_jobs', $id);
          if (empty($form_data)) {
            redirect(SITE_ADMIN . '/hrd/setting_jobs_list');
        }
        if (isset($_POST) && ! empty($_POST)) { 
            $status_delete = $this->hrd_model->delete_jobs_components(array('job_id' => $id));
            $data_enhancer = (!empty($_POST['enhancer']))?$_POST['enhancer']:array();
            $data_subtrahend =  (!empty($_POST['subtrahend']))?$_POST['subtrahend']:array();
            
            $result = array_merge($data_enhancer, $data_subtrahend);
            $save = false;
          
            if(!empty($result)){ 
                $array = array();
                foreach ($result as $data) {
                    if(isset($data['quantity']) && $data['quantity'] >=0){
                         $array = array('job_id' => $id,
                               'component_id' => (isset($data['salary_component_id']) ? $data['salary_component_id'] : $data['component_id']),
                               'value' => $data['quantity']
                        ); 
                        $save = $this->categories_model->save('hr_jobs_components', $array); 
                    }
                   
                } 
            } 
            if ($save === false) {
                $this->session->set_flashdata('message', 'Gagal Menyimpan Komponen Gaji');
            }
            else {
                $this->session->set_flashdata('message_success', 'Berhasil Menyimpan Komponen Gaji');
            }


            $btnaction = $this->input->post('btnAction');
            if ($btnaction == 'save_exit') {
                redirect(SITE_ADMIN . '/hrd/setting_jobs_list', 'refresh');
            }
            else {
                redirect(SITE_ADMIN . '/hrd/set_salary_component/' . $id, 'refresh');
            }

        }else{
            
            
            $this->data['form_data'] = $form_data;
            
            $this->data['jabatan']  = $form_data->jobs_name;
            
            $this->data['enhancer_sal_component_static'] = $this->hrd_model->get_all_where("hr_salary_component",array('is_enhancer'=>1,'is_static'=>1)); 
            $this->data['substrahend_sal_component_static'] = $this->hrd_model->get_all_where("hr_salary_component",array('is_enhancer'=>-1,'is_static'=>1)); 
            
            $this->data['enhancer_sal_component_dropdwn'] = $this->hrd_model->get_salary_component_dropdown(array('is_enhancer'=>1)); 
            $this->data['substrahend_sal_component_dropdwn'] = $this->hrd_model->get_salary_component_dropdown(array('is_enhancer'=>-1)); 
            

            $this->data['data_enhancer_jobs_component'] = $this->hrd_model->get_jobs_component_by(array("job_id"=>$id,"is_enhancer"=>1));
            $this->data['data_subtrahend_jobs_component'] = $this->hrd_model->get_jobs_component_by(array("job_id"=>$id,"is_enhancer"=>-1)); 

            $this->data['data_enhancer_salary_component']  = $this->hrd_model->get_salary_component(array('is_enhancer'=>1,'is_static'=>0));
            $this->data['data_substrahend_salary_component']  = $this->hrd_model->get_salary_component(array('is_enhancer'=>-1,'is_static'=>0));

            $this->data['content'] .= $this->load->view('admin/hrd/set_salary_component_view.php', $this->data, true);

            $this->render('hrd');
        }
        
        
    }

    public function setting_office_hours(){
        $this->data['title']    = "Template Jam Kerja";
        $this->data['subtitle'] = "Template Jam Kerja";
        $this->data['content'] .= $this->load->view('admin/hrd/setting_office_hours_view', $this->data, true);
        $this->render('hrd'); 
    }

    public function get_data_office_hours(){
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $this->datatables->select('id,name,checkin_time,checkout_time')->from('hr_office_hours')
                            ->add_column('actions', "<div class='btn-group'>
                                    <a href='" . base_url(SITE_ADMIN . '/hrd/edit_office_hours/$1')."' class='btn btn-default edit-office-hours'  office-hours-id='$1'><i class='fa fa-pencil'></i> Edit</a>
                                    <button class='btn btn-danger delete-office-hours'  office-hours-id='$1'><i class='fa fa-trash-o'></i> Delete</button>
                                </div>", 'id'); 
        echo $this->datatables->generate();
    }

    public function delete_office_hours(){
        $id =  $this->input->post('id');
        $return_data['status'] = false;
        $return_data['data'] = array();
        $return_data['message'] = ""; 

        if(empty($id)){
            $return_data['message'] = "Parameter Kosong"; 
             echo json_encode($return_data); 
        }else{
            $status = $this->hrd_model->delete_office_hours(array('id' => $id));
            if($status){
                $return_data['status'] = true;
            }else{
                $return_data['message'] = "Maaf, Template Jam Kerja Gagal Di Hapus. Silahkan Hubungi Administrator";  
            }

            echo json_encode($return_data);
        } 
        
    }

    public function add_office_hours(){
        $this->data['title']    = "Tambah Template Jam Kerja";
        $this->data['subtitle'] = "Tambah Template Jam Kerja";

        //validate form input
        $this->form_validation->set_rules('name', 'Nama Template Kerja', 'required|xss_clean|min_length[1]|max_length[50]');
        $this->form_validation->set_rules('checkin_time', 'Jam Masuk', 'required|xss_clean|min_length[1]|max_length[50]');
        $this->form_validation->set_rules('checkout_time', 'Jam Keluar', 'required|xss_clean|min_length[1]|max_length[50]');
        

        if ($this->form_validation->run() == true) {

            $array = array('name' => $this->input->post('name'),
                           'checkin_time' => $this->input->post('checkin_time'),
                           'checkout_time' => $this->input->post('checkout_time')
                    );

            $save = $this->categories_model->save('hr_office_hours', $array);

            if ($save === false) {
                $this->session->set_flashdata('message', 'Gagal Menyimpan Template Kerja');
            }
            else {
                $this->session->set_flashdata('message_success', 'Berhasil Menyimpan Template Kerja');
            }


            $btnaction = $this->input->post('btnAction');
            if ($btnaction == 'save_exit') {
                redirect(SITE_ADMIN . '/hrd/setting_office_hours', 'refresh');
            }
            else {
                redirect(SITE_ADMIN . '/hrd/add_office_hours/', 'refresh');
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
                                              'placeholder' => 'Nama Jam Kerja',
                                              'value' => $this->form_validation->set_value('name')); 


             $this->data['checkin_time'] = array('name' => 'checkin_time',  
                                               'type' => 'text',
                                               'class' => 'form-control',
                                              'field-name' => 'checkin_time', 
                                                'onkeydown'=>'return false',
                                              'value' => $this->form_validation->set_value('checkin_time')); 

              $this->data['checkout_time'] = array('name' => 'checkout_time',
                                              'id' => 'checkout_time', 
                                               'type' => 'text',
                                                 'onkeydown'=>'return false',
                                               'class' => 'form-control checkout_time',
                                              'field-name' => 'checkout_time', 
                                              'value' => $this->form_validation->set_value('checkout_time')); 

            //load content
            $this->data['content'] .= $this->load->view('admin/hrd/setting_office_hours_add', $this->data, true);
            $this->render('hrd');
        }
    }

    public function edit_office_hours(){
        
        $id = $this->uri->segment(4);

        if (empty($id)) {
            redirect(SITE_ADMIN . '/hrd/setting_office_hours');
        }

        $form_data = $this->hrd_model->get_one('hr_office_hours', $id);

        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/hrd/setting_office_hours');
        }

        $this->data['form_data'] = $form_data;
        $this->data['subtitle']  = "Edit Template Jam Kerja";

        //validate form input
        $this->form_validation->set_rules('name', 'Nama Template Kerja', 'required|xss_clean|min_length[1]|max_length[50]');
        $this->form_validation->set_rules('checkin_time', 'Jam Masuk', 'required|xss_clean|min_length[1]|max_length[50]');
        $this->form_validation->set_rules('checkout_time', 'Jam Keluar', 'required|xss_clean|min_length[1]|max_length[50]');

        if (isset($_POST) && ! empty($_POST)) {

            if ($this->form_validation->run() === TRUE) {


                $array = array('name' => $this->input->post('name'),
                           'checkin_time' => $this->input->post('checkin_time'),
                           'checkout_time' => $this->input->post('checkout_time')
                    );

                $save = $this->categories_model->save('hr_office_hours', $array, $id);

                if ($save === false) {
                    $this->session->set_flashdata('message', 'Gagal Menyimpan Jam Kerja');
                }
                else {
                    $this->session->set_flashdata('message_success', 'Berhasil Menyimpan Jam Kerja');
                }


                $btnaction = $this->input->post('btnAction');
                if ($btnaction == 'save_exit') {
                    redirect(SITE_ADMIN . '/hrd/setting_office_hours', 'refresh');
                }
                else {
                    redirect(SITE_ADMIN . '/hrd/edit_office_hours/' . $id, 'refresh');
                }


            }
        }

        //display the edit user form
        $this->data['csrf'] = $this->_get_csrf_nonce();

        //set the flash data error message if there is one
        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success'); 
        
         $this->data['name'] = array('name' => 'name',
                                              'id' => 'name',
                                              'type' => 'text',
                                              'class' => 'form-control no-special-char',
                                              'field-name' => 'name',
                                              'placeholder' => 'Nama Jam Kerja', 
                                              'value' => $this->form_validation->set_value('name', $form_data->name)); 


             $this->data['checkin_time'] = array('name' => 'checkin_time',
                                              'id' => 'checkin_time', 
                                                'type' => 'text',
                                              'class' => 'form-control',
                                              'field-name' => 'checkin_time', 
                                              'value' => $this->form_validation->set_value('checkin_time', $form_data->checkin_time)); 

              $this->data['checkout_time'] = array('name' => 'checkout_time',
                                              'id' => 'checkout_time', 
                                                'type' => 'text',
                                              'class' => 'form-control',
                                              'field-name' => 'checkout_time', 
                                              'value' => $this->form_validation->set_value('checkout_time', $form_data->checkout_time)); 


        $this->data['content'] .= $this->load->view('admin/hrd/setting_office_hours_edit.php', $this->data, true);

        $this->render('hrd');
    }



    public function setting_jobs_list(){
        $this->data['title']    = "Master Jabatan ";
        $this->data['subtitle'] = "Master Jabatan";
        $this->data['content'] .= $this->load->view('admin/hrd/setting_jobs_view', $this->data, true);
        $this->render('hrd'); 
    }

    public function get_data_jobs(){
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $this->datatables->select('id,jobs_name,note')->from('hr_jobs')
                            ->add_column('salary_component', "<div class='btn-group'>
                                    <a href='" . base_url(SITE_ADMIN . '/hrd/set_salary_component/$1')."' class='btn btn-default'><i class='fa fa-pencil'></i> Komponen Gaji</a> 
                                </div>", 'id')
                            ->add_column('actions', "<div class='btn-group'>
                                    <a href='" . base_url(SITE_ADMIN . '/hrd/edit_jobs/$1')."' class='btn btn-default edit-jobs'  jobs-id='$1'><i class='fa fa-pencil'></i> Edit</a>
                                    <button class='btn btn-danger delete-jobs'  jobs-id='$1'><i class='fa fa-trash-o'></i> Delete</button>
                                </div>", 'id'); 
        echo $this->datatables->generate();
    }

    public function delete_jobs(){
        $id =  $this->input->post('id');
        $return_data['status'] = false;
        $return_data['data'] = array();
        $return_data['message'] = ""; 

        if(empty($id)){
            $return_data['message'] = "Parameter Kosong"; 
             echo json_encode($return_data); 
        }else{ 
            $status = $this->hrd_model->delete_jobs(array('id' => $id));
            if($status){
                $return_data['status'] = true;
            }else{
                $return_data['message'] = "Maaf, Master Jabatan Gagal Di Hapus. Silahkan Hubungi Administrator";  
            }

            echo json_encode($return_data);
        } 
        
    }

    public function add_jobs(){
        $this->data['title']    = "Tambah Master Jabatan";
        $this->data['subtitle'] = "Tambah Master Jabatan";

        //validate form input
        $this->form_validation->set_rules('jobs_name', 'Nama Master Jabatan', 'required|xss_clean|min_length[1]|max_length[50]');
        $this->form_validation->set_rules('note', 'Keterangan', 'xss_clean|min_length[1]');
        

        if ($this->form_validation->run() == true) {

            $array = array('jobs_name' => $this->input->post('jobs_name'),
                           'note' => $this->input->post('note'),
                           'store_id' => $this->data['setting']['store_id'],
                    );

            $save = $this->categories_model->save('hr_jobs', $array);

            if ($save === false) {
                $this->session->set_flashdata('message', 'Gagal Menyimpan Master Jabatan');
            }
            else {
                $this->session->set_flashdata('message_success', 'Berhasil Menyimpan Master Jabatan');
            }


            $btnaction = $this->input->post('btnAction');
            if ($btnaction == 'save_exit') {
                redirect(SITE_ADMIN . '/hrd/setting_jobs_list', 'refresh');
            }
            else {
                redirect(SITE_ADMIN . '/hrd/add_jobs/', 'refresh');
            }


        }
        else {
            //display the create user form
            //set the flash data error message if there is one
            $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            $this->data['message_success'] = $this->session->flashdata('message_success');

            $this->data['jobs_name'] = array('name' => 'jobs_name',
                                              'id' => 'jobs_name',
                                              'type' => 'text',
                                              'class' => 'form-control char-only',
                                              'field-name' => 'jobs_name',
                                              'placeholder' => 'Nama Jabatan',
                                              'value' => $this->form_validation->set_value('jobs_name')); 


             $this->data['note'] = array('name' => 'note',
                                              'id' => 'note',  
                                               'class' => 'form-control no-special-char',
                                              'field-name' => 'note', 
                                              'value' => $this->form_validation->set_value('note'));  

            //load content
            $this->data['content'] .= $this->load->view('admin/hrd/setting_jobs_add', $this->data, true);
            $this->render('hrd');
        }
    }

    public function edit_jobs(){
        
        $id = $this->uri->segment(4);

        if (empty($id)) {
            redirect(SITE_ADMIN . '/hrd/setting_jobs_list');
        }

        $form_data = $this->hrd_model->get_one('hr_jobs', $id);

        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/hrd/setting_jobs_list');
        }

        $this->data['form_data'] = $form_data;
        $this->data['subtitle']  = "Edit Master Jabatan";

        //validate form input
       $this->form_validation->set_rules('jobs_name', 'Nama Master Jabatan', 'required|xss_clean|min_length[1]|max_length[50]');
        $this->form_validation->set_rules('note', 'Keterangan ', 'xss_clean|min_length[1]');  

        if (isset($_POST) && ! empty($_POST)) {

            if ($this->form_validation->run() === TRUE) {


                $array = array(
                        'jobs_name' => $this->input->post('jobs_name'),
                        'note' => $this->input->post('note')
                );

                $save = $this->categories_model->save('hr_jobs', $array, $id);

                if ($save === false) {
                    $this->session->set_flashdata('message', 'Gagal Menyimpan Master Jabatan');
                }
                else {
                    $this->session->set_flashdata('message_success', 'Berhasil Menyimpan Master Jabatan');
                }


                $btnaction = $this->input->post('btnAction');
                if ($btnaction == 'save_exit') {
                    redirect(SITE_ADMIN . '/hrd/setting_jobs_list', 'refresh');
                }
                else {
                    redirect(SITE_ADMIN . '/hrd/edit_jobs/' . $id, 'refresh');
                }


            }
        }

        //display the edit user form
        $this->data['csrf'] = $this->_get_csrf_nonce();

        //set the flash data error message if there is one
        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success'); 
        
         $this->data['jobs_name'] = array('name' => 'jobs_name',
                                              'id' => 'jobs_name',
                                              'type' => 'text',
                                              'class' => 'form-control char-only',
                                              'field-name' => 'jobs_name',
                                              'placeholder' => 'Nama Jabatan', 
                                              'value' => $this->form_validation->set_value('jobs_name', $form_data->jobs_name)); 


            
              $this->data['note'] = array('name' => 'note',
                                              'id' => 'note',  
                                              'field-name' => 'note', 
                                               'class' => 'form-control no-special-char',
                                              'value' => $this->form_validation->set_value('note', $form_data->note)); 


        $this->data['content'] .= $this->load->view('admin/hrd/setting_jobs_edit.php', $this->data, true);

        $this->render('hrd');
    }

    public function setting_memorandum()
    {
        $this->data['title']    = "Surat Peringatan";
        $this->data['subtitle'] = "Surat Peringatan";
        $this->data['content'] .= $this->load->view('admin/hrd/setting_memorandum_view', $this->data, true);
        $this->render('hrd'); 
    }

    public function get_data_memorandum()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $this->datatables->select('id, name,period')->from('hr_memorandum')
                            ->add_column('actions', "<div class='btn-group'>
                                    <button   class='btn btn-default edit-memorandum'  memorandum-id='$1'><i class='fa fa-pencil'></i> Edit</button>
                                    <button  class='btn btn-danger delete-memorandum' rel='Status Kepegawaian'  memorandum-id='$1'><i class='fa fa-trash-o'></i> Delete</button>
                                </div>", 'id'); 
        echo $this->datatables->generate();
    }

    public function setting_umum()
    {
        $this->render('hrd'); 
    }
}