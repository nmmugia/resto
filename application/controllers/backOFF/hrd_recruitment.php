<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/17/2014
 * Time: 8:27 PM
 */
class Hrd_recruitment extends Hrd_Controller
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
              $this->_store_data = $this->ion_auth->user()->row();
        } 
    }

    public function index()
    {
          redirect(SITE_ADMIN . '/hrd_recruitment/recruitment_list', 'refresh');
    } 

    public function recruitment_list(){
        $this->data['title']    = "Data Lamaran";
        $this->data['subtitle'] ="Data Lamaran";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

       
        $this->data['data_url'] = base_url(SITE_ADMIN . '/hrd_recruitment/get_data_recruitments');
        $this->data['add_url'] = base_url(SITE_ADMIN . '/hrd_recruitment/add_recruitment');
       
        $this->data['content'] .= $this->load->view('admin/hrd/recruitment_list_view', $this->data, true);
        $this->render('hrd');
    }  
    public function get_data_recruitments(){
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));  
      
        $this->datatables->select('id,name,phone_no,created_at,job_apply')
            ->from('hr_recruitment')  
            ->add_column('actions', 
                "<div class='btn-group'> 
                      <a href='" . base_url(SITE_ADMIN . '/hrd_recruitment/edit_recruitment/$1')."' class='btn btn-default'  >Edit</a>
                      <a href='" . base_url(SITE_ADMIN . '/hrd_recruitment/delete_recruitment/$1')."' class='btn btn-danger deleteNow' rel='Recruitment'>Delete</a>
                </div>", 'id');
           
            
        echo $this->datatables->generate();
    }

    public function delete_recruitment(){
        $id = $this->uri->segment(4);
        $this->hrd_model->delete_by('hr_rec_childrens', array("user_id"=>$id));
        $this->hrd_model->delete_by('hr_rec_courses', array("user_id"=>$id));
        $this->hrd_model->delete_by('hr_rec_edu', array("user_id"=>$id));
        $this->hrd_model->delete_by('hr_rec_experience', array("user_id"=>$id));
        $this->hrd_model->delete_by('hr_rec_mates', array("user_id"=>$id));
        $this->hrd_model->delete_by('hr_rec_org', array("user_id"=>$id));
        $this->hrd_model->delete_by('hr_rec_parents', array("user_id"=>$id));
        $this->hrd_model->delete_by('hr_rec_siblings', array("user_id"=>$id));

        $this->hrd_model->delete_by('hr_recruitment', array("id"=>$id));
        redirect(SITE_ADMIN . '/hrd_recruitment', 'refresh');
       
    }

    public function add_recruitment(){
        $this->data['title']    = "Tambah Calon Pegawai";
        $this->data['subtitle'] ="Tambah Calon Pegawai";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success'); 
        if (isset($_POST) && !empty($_POST)) {  
            $data_rec = array(
                'name'      => $this->input->post('full_name'),
                'birth_place'      => $this->input->post('placedate'),
                'birth_date'      => $this->input->post('birthdate'), 
                 'gender'      =>  $this->input->post('jk'), 
                'nationality'      =>  $this->input->post('nationality'), 
                'religion'      =>  $this->input->post('religion'), 
                'address'      =>  $this->input->post('address'), 
                'phone_no'      =>  $this->input->post('phone'),  
                'identity_no'      =>  $this->input->post('no_ktp'),  
                'driving_license_type'      =>  $this->input->post('no_sim'),  
                'driving_license_no'      =>  $this->input->post('sim_number'),  
                'married_status'      =>  $this->input->post('status'),
                'created_at'      => date('Y-m-d H:i:s'),
                'job_apply'      => $this->input->post('job_apply')
            );
            $save = $this->categories_model->save('hr_recruitment', $data_rec); 
            if($save){ 
                $date_mate = array(
                    'mate_name'      => $this->input->post('mate_name'),
                    'mate_birth_date'      => $this->input->post('mate_birth_date'),
                    'mate_birth_place'      => $this->input->post('mate_birth_place'), 
                    'mate_job'      =>  $this->input->post('mate_job'), 
                    'child_total'      => $this->input->post('child_total'), 
                    'user_id'      => $save
                );
                $save_mate = $this->categories_model->save('hr_rec_mates', $date_mate); 

                $data_parent = array(
                    'parent_name'      => $this->input->post('mate_name'),
                    'parent_job'      => $this->input->post('mate_birth_date'),
                    'parent_address'      => $this->input->post('mate_birth_place'),
                    'user_id'      => $save
                );
                $save_parents = $this->categories_model->save('hr_rec_parents', $data_parent); 
                $siblings = $this->input->post('siblings');
                if(!empty($siblings)){
                    foreach ($siblings as $data) {
                        $save_exp = array(
                            'sibling_name'          => $data['name'],
                            'sibling_age'        => $data['age'],
                            'sibling_status'        => $data['status'],
                            'sibling_edu_level'           => $data['education'],
                            'user_id'               => $save
                        );
                        $save_exps = $this->categories_model->save('hr_rec_siblings', $save_exp); 
                    }
                }


                $family = $this->input->post('family');
                if(!empty($family)){
                    foreach ($family as $data) {
                        $save_exp = array(
                            'child_name'          => $data['name'],
                            'child_age'        => $data['age'],
                            'child_edu_level'           => $data['education'],
                            'user_id'               => $save
                        );
                        $save_exps = $this->categories_model->save('hr_rec_childrens', $save_exp); 
                    }
                }


                $edu = $this->input->post('edu');
                if(!empty($edu)){
                    foreach ($edu as $data) {
                        $save_exp = array(
                            'school_period'          => $data['period'],
                            'school_name'        => $data['school_name'],
                            'school_city'           => $data['city'],
                            'user_id'               => $save,
                            'school_ijazah'        => $data['legacy'],
                        );
                        $save_exps = $this->categories_model->save('hr_rec_edu', $save_exp); 
                    }
                }

                $courses = $this->input->post('courses');
                if(!empty($courses)){
                    foreach ($courses as $data) {
                        $save_exp = array(
                            'course_name'          => $data['course_name'],
                            'course_period'        => $data['course_time'],
                            'course_place'           => $data['course_place'],
                            'user_id'               => $save,
                            'course_description'        => $data['course_description']
                        );
                        $save_exps = $this->categories_model->save('hr_rec_courses', $save_exp); 
                    }
                }


                $experiences = $this->input->post('experience');
                if(!empty($experiences)){
                    foreach ($experiences as $data) {
                        $save_exp = array(
                            'company_name'      => $data['experience_company'],
                            'company_period'      => $data['experience_period'],
                            'company_job'      => $data['experience_job'],
                            'user_id'      => $save,
                            'company_reason'      => $data['experience_reason']
                        );
                        $save_exps = $this->categories_model->save('hr_rec_experience', $save_exp); 
                    }
                }


                $orgs = $this->input->post('org');
                if(!empty($orgs)){
                    foreach ($orgs as $data) {
                        $save_exp = array(
                            'org_name'      => $data['experience_company'],
                            'org_period'      => $data['experience_period'],
                            'org_job'      => $data['experience_job'],
                            'user_id'      => $save,
                            'org_description'      => $data['experience_reason']
                        );
                        $save_exps = $this->categories_model->save('hr_rec_org', $save_exp); 
                    }
                }

            }
            $btnaction = $this->input->post('btnAction');
            if ($btnaction == 'save_exit') {
                redirect(SITE_ADMIN . '/hrd_recruitment', 'refresh');
            }
            else {
                redirect(SITE_ADMIN . '/hrd_recruitment/add_recruitment/', 'refresh');
            }
        }
        $this->data['content'] .= $this->load->view('admin/hrd/recruitment_add_view', $this->data, true);
        $this->render('hrd');
    } 

    public function edit_recruitment(){
        $this->data['title']    = "Ubah Calon Pegawai";
        $this->data['subtitle'] ="Ubah Calon Pegawai"; 
         $id = $this->uri->segment(4);

        if (isset($_POST) && !empty($_POST)) { 
        //       echo "<pre>";
        // print_r($_POST);
            $this->remove_recruitment($id);

            $data_rec = array(
                'name'      => $this->input->post('full_name'),
                'birth_place'      => $this->input->post('placedate'),
                'birth_date'      => $this->input->post('birthdate'), 
                 'gender'      =>  $this->input->post('jk'), 
                'nationality'      =>  $this->input->post('nationality'), 
                'religion'      =>  $this->input->post('religion'), 
                'address'      =>  $this->input->post('address'), 
                'phone_no'      =>  $this->input->post('phone'),  
                'identity_no'      =>  $this->input->post('no_ktp'),  
                'driving_license_type'      =>  $this->input->post('no_sim'),  
                'driving_license_no'      =>  $this->input->post('sim_number'),  
                'married_status'      =>  $this->input->post('status'),
                'created_at'      => date('Y-m-d H:i:s'),
                'job_apply'      => $this->input->post('job_apply')
            );
            $save = $this->categories_model->save('hr_recruitment', $data_rec,$id); 
            if($save){
                  $date_mate = array(
                    'mate_name'      => $this->input->post('mate_name'),
                    'mate_birth_date'      => $this->input->post('mate_birth_date'),
                    'mate_birth_place'      => $this->input->post('mate_birth_place'), 
                    'mate_job'      =>  $this->input->post('mate_job'), 
                    'child_total'      => $this->input->post('child_total'), 
                    'user_id'      => $save
                );
                $save_mate = $this->categories_model->save('hr_rec_mates', $date_mate); 

                $data_parent = array(
                    'parent_name'      => $this->input->post('mate_name'),
                    'parent_job'      => $this->input->post('mate_birth_date'),
                    'parent_address'      => $this->input->post('mate_birth_place'),
                    'user_id'      => $save
                );
                $save_parents = $this->categories_model->save('hr_rec_parents', $data_parent); 
                $siblings = $this->input->post('siblings');
                if(!empty($siblings)){
                    foreach ($siblings as $data) {
                        $save_exp = array(
                            'sibling_name'          => $data['name'],
                            'sibling_age'        => $data['age'],
                            'sibling_status'        => $data['status'],
                            'sibling_edu_level'           => $data['education'],
                            'user_id'               => $save
                        );
                        $save_exps = $this->categories_model->save('hr_rec_siblings', $save_exp); 
                    }
                }


                $family = $this->input->post('family');
                if(!empty($family)){
                    foreach ($family as $data) {
                        $save_exp = array(
                            'child_name'          => $data['name'],
                            'child_age'        => $data['age'],
                            'child_edu_level'           => $data['education'],
                            'user_id'               => $save
                        );
                        $save_exps = $this->categories_model->save('hr_rec_childrens', $save_exp); 
                    }
                }


                $edu = $this->input->post('edu');
                if(!empty($edu)){
                    foreach ($edu as $data) {
                        $save_exp = array(
                            'school_period'          => $data['period'],
                            'school_name'        => $data['school_name'],
                            'school_city'           => $data['city'],
                            'user_id'               => $save,
                            'school_ijazah'        => $data['legacy'],
                        );
                        $save_exps = $this->categories_model->save('hr_rec_edu', $save_exp); 
                    }
                }

                $courses = $this->input->post('courses');
                if(!empty($courses)){
                    foreach ($courses as $data) {
                        $save_exp = array(
                            'course_name'          => $data['course_name'],
                            'course_period'        => $data['course_time'],
                            'course_place'           => $data['course_place'],
                            'user_id'               => $save,
                            'course_description'        => $data['course_description']
                        );
                        $save_exps = $this->categories_model->save('hr_rec_courses', $save_exp); 
                    }
                }


                $experiences = $this->input->post('experience');
                if(!empty($experiences)){
                    foreach ($experiences as $data) {
                        $save_exp = array(
                            'company_name'      => $data['experience_company'],
                            'company_period'      => $data['experience_period'],
                            'company_job'      => $data['experience_job'],
                            'user_id'      => $save,
                            'company_reason'      => $data['experience_reason']
                        );
                        $save_exps = $this->categories_model->save('hr_rec_experience', $save_exp); 
                    }
                }


                $orgs = $this->input->post('org');
                if(!empty($orgs)){
                    foreach ($orgs as $data) {
                        $save_exp = array(
                            'org_name'      => $data['experience_company'],
                            'org_period'      => $data['experience_period'],
                            'org_job'      => $data['experience_job'],
                            'user_id'      => $save,
                            'org_description'      => $data['experience_reason']
                        );
                        $save_exps = $this->categories_model->save('hr_rec_org', $save_exp); 
                    }
                }

            }

            // die();
            $btnaction = $this->input->post('btnAction');
            if ($btnaction == 'save_exit') {
                redirect(SITE_ADMIN . '/hrd_recruitment', 'refresh');
            }
            else {
                redirect(SITE_ADMIN . '/hrd_recruitment/edit_recruitment/', 'refresh');
            }

        }
       
        $this->data['detail_recruits']   = $this->hrd_model->get_one('hr_recruitment', $id);

       $this->data['childrens'] = $this->hrd_model->get_where('hr_rec_childrens', array("user_id"=>$id)); 
       $this->data['courses']  = $this->hrd_model->get_where('hr_rec_courses', array("user_id"=>$id));
       $this->data['educations'] = $this->hrd_model->get_where('hr_rec_edu', array("user_id"=>$id));
       $this->data['experiences'] = $this->hrd_model->get_where('hr_rec_experience', array("user_id"=>$id));
       $this->data['orgs'] = $this->hrd_model->get_where('hr_rec_org', array("user_id"=>$id));
       $this->data['mates'] = $this->hrd_model->get_by('hr_rec_mates', $id,'user_id');  
       $this->data['parents'] = $this->hrd_model->get_by('hr_rec_parents', $id,'user_id');
       $this->data['siblings'] = $this->hrd_model->get_where('hr_rec_siblings', array("user_id"=>$id));
       


        $this->data['content'] .= $this->load->view('admin/hrd/recruitment_edit_view', $this->data, true);
        $this->render('hrd');
    } 
     
    public function remove_recruitment($id){
        $this->hrd_model->delete_by('hr_rec_childrens', array("user_id"=>$id));
            $this->hrd_model->delete_by('hr_rec_courses', array("user_id"=>$id));
            $this->hrd_model->delete_by('hr_rec_edu', array("user_id"=>$id));
            $this->hrd_model->delete_by('hr_rec_experience', array("user_id"=>$id));
            $this->hrd_model->delete_by('hr_rec_mates', array("user_id"=>$id));
            $this->hrd_model->delete_by('hr_rec_org', array("user_id"=>$id));
            $this->hrd_model->delete_by('hr_rec_parents', array("user_id"=>$id));
            $this->hrd_model->delete_by('hr_rec_siblings', array("user_id"=>$id));
    }
}