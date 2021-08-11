<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @Author: Fitria Kartika
 * @Date:   2015-10-12 11:33:20
 * @Last Modified by:   Fitria Kartika
 * @Last Modified time: 2015-11-02 12:07:26
 */

class Sidebar_menu extends Admin_Controller
{

    private $created_at ;
    private $created_by ;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('sidebar_menu_model');

        $this->created_at = date("Y-m-d H:i:s") ;
        $this->created_by =  $this->data['user_profile_data']->id;

    }


     public function index(){

        $this->data['title']    = "Sidebar Menu";
        $this->data['subtitle'] = "Sidebar Menu";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');
        
        $this->data['ddl_menu']  = $this->sidebar_menu_model->get_sidebar_menu_dropdown();

        $this->data['add_url']  = base_url(SITE_ADMIN . '/sidebar_menu/add_menu');
        $this->data['data_url'] = base_url(SITE_ADMIN . '/sidebar_menu/get_sidemenu_data');;
        //load content
        $this->data['content'] .= $this->load->view('admin/sidebar-menu-list', $this->data, true);
        $this->render('admin');
    }


    public function get_sidemenu_data()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $parent_id =0;
        if($this->input->post('parent_id')!==0){
            $parent_id =$this->input->post('parent_id');
            }

        $this->datatables->select('
            a.id, a.name, a.parent_id, a.url, a.sequence,
            GROUP_CONCAT(g.name separator ", ") as groups_access', FALSE)
        ->from("sidebar_menu a ")
        ->join('sidebar_menu_access b', 'b.sidebar_menu_id = a.id','left')
        ->join('groups g', 'g.id = b.groups_id','left')
        ->group_by('a.id')
        ->where('a.parent_id', $parent_id)
        ->add_column('actions_sequence', "<div class='btn-group'>
            <button data-url='" . base_url(SITE_ADMIN . '/sidebar_menu/update_sequence/previous/$1') . "' class='btn btn-default update-sequence' rel=''><i class='fa fa-sort-up'></i> </button>
            <button data-url='" . base_url(SITE_ADMIN . '/sidebar_menu/update_sequence/next/$1') . "' class='btn btn-default update-sequence' rel='menu'><i class='fa fa-sort-down'></i></button>
            </div>", 'id')

        ->add_column('actions', "<div class='btn-group'>
                                    <a href='" . base_url(SITE_ADMIN . '/sidebar_menu/edit_menu/$1') . "' class='btn btn-default' rel=''><i class='fa fa-pencil'></i> Edit</a>
                                    <a href='" . base_url(SITE_ADMIN . '/sidebar_menu/delete_menu/$1') . "' class='btn btn-danger deleteNow' rel='menu'><i class='fa fa-trash-o'></i> Hapus</a>
                                </div>", 'id');
     
        echo $this->datatables->generate();
    }


    public function add_menu()
    {

        $this->data['title']    = "Tambah Menu Admin";
        $this->data['subtitle'] = "Tambah Menu Admin";

        $this->form_validation->set_rules('name', 'nama', 'required|xss_clean|max_length[100]');
        $this->form_validation->set_rules('url','url' , 'xss_clean|callback__validate_url_format');
        $this->form_validation->set_rules('parent_id','url');
        $this->form_validation->set_rules('module','module','required');
        if ($this->form_validation->run() == true) {    

            $sequence = $this->sidebar_menu_model->get_sequence($this->input->post('parent_id'));
 
            $data_save = array('parent_id' => $this->input->post('parent_id'),
                                'name' => htmlentities($this->input->post('name')),
                                'url' =>  htmlentities($this->input->post('url')),
                                'sequence' => $sequence,
                                'created_at' => $this->created_at,
                                'created_by' => $this->created_by,
                                'module_id' => $this->input->post("module"),
                                );

            $sidebar_menu_id = $this->sidebar_menu_model->save('sidebar_menu', $data_save);
            if ($sidebar_menu_id === false) {
                $this->session->set_flashdata('message',$this->lang->line('error_add'));
            }
            else {
                if($this->input->post('menu_access')) {

                    $access = explode(",",$this->input->post('menu_access'));                    
                    foreach ($access as $key => $row) {
                        $data_save = array(
                            'sidebar_menu_id' => $sidebar_menu_id,
                            'groups_id' => $row,
                            'created_at' => $this->created_at,
                            'created_by' => $this->created_by,
                            );
                        $this->sidebar_menu_model->save('sidebar_menu_access', $data_save);
                    }

                }

                $this->session->set_flashdata('message_success', $this->lang->line('success_add'));
            }
            $btnaction = $this->input->post('btnAction');
            if ($btnaction == 'save_exit') {
                redirect(SITE_ADMIN . '/sidebar_menu', 'refresh');
            }
            else {
                redirect(SITE_ADMIN . '/sidebar_menu/add_menu/', 'refresh');
            }


        }
        else {
            //display the create user form
            //set the flash data error message if there is one
            $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            $this->data['message_success'] = $this->session->flashdata('message_success');

            $this->data['name']  = array('name' => 'name',
                                                'id' => 'name',
                                                'type' => 'text',
                                                'class' => 'form-control requiredTextField',
                                                'field-name' => 'nama',
                                                'placeholder' => 'Masukan nama menu',
                                                'value' => $this->form_validation->set_value('name'));


            $this->data['url']  = array('name' => 'url',
                                                'id' => 'url',
                                                'type' => 'text',
                                                'class' => 'form-control ',
                                                'field-name' => 'url',
                                                'placeholder' => 'Masukan url',
                                                'value' => $this->form_validation->set_value('url'));

            $this->data['parent_id']  = $this->form_validation->set_value('parent_id');
            $this->data['module_lists']=$this->sidebar_menu_model->get("module")->result();
            $this->data['ddl_menu']  = $this->sidebar_menu_model->get_sidebar_menu_dropdown();
            $this->data['selected_groups'] = array();
            $this->data['groups']  = $this->sidebar_menu_model->get('groups')->result();
            $this->data['cancel_url']  = base_url(SITE_ADMIN . '/sidebar_menu');
            $this->data['action']  = 'add';
            $this->data['content'] .= $this->load->view('admin/sidebar-menu-add', $this->data, true);
            $this->render('admin');
        }

    }


    public function edit_menu()
    {

        $id = $this->uri->segment(4);
        
        $form_data = $this->sidebar_menu_model->get_one('sidebar_menu', $id);
        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/sidebar_menu');
        }

        $this->data['form_data'] = $form_data;
        $this->data['title']    = "Edit Menu";
        $this->data['subtitle'] = "Edit Menu";

        $this->form_validation->set_rules('name', 'nama', 'required|xss_clean|max_length[100]');
        $this->form_validation->set_rules('url','url' , 'xss_clean|callback__validate_url_format');
        $this->form_validation->set_rules('module','module' , 'required');
        if ($this->form_validation->run() == true) {    
            $sequence = $this->sidebar_menu_model->get_sequence($this->input->post('parent_id'));

            $data_save = array(
                'parent_id' => $this->input->post('parent_id'),
                'name' => htmlentities($this->input->post('name')),
                'sequence' => $sequence,
                'url' =>  htmlentities($this->input->post('url')),
                'modified_at' => $this->created_at,
                'modified_by' => $this->created_by,
                'module_id' => $this->input->post("module"),
                );

            $sidebar_menu_id = $this->sidebar_menu_model->save('sidebar_menu', $data_save, $id);
            if ($sidebar_menu_id === false) {
                $this->session->set_flashdata('message',$this->lang->line('error_add'));
            }
            else {
                $this->sidebar_menu_model->delete_by_limit('sidebar_menu_access', array('sidebar_menu_id'=> $id),0);
                if($this->input->post('menu_access')) {
                    $access = explode(",",$this->input->post('menu_access')); 
                    foreach ($access as $key => $row) {
                        $data_save = array(
                            'sidebar_menu_id' => $id,
                            'groups_id' => $row,
                            'created_at' => $this->created_at,
                            'created_by' => $this->created_by,
                            );
                        $this->sidebar_menu_model->save('sidebar_menu_access', $data_save);
                    }

                }

                $this->session->set_flashdata('message_success', $this->lang->line('success_add'));
            }
            $btnaction = $this->input->post('btnAction');
            if ($btnaction == 'save_exit') {
                redirect(SITE_ADMIN . '/sidebar_menu', 'refresh');
            }
            else {
                redirect(SITE_ADMIN . '/sidebar_menu/edit_menu/' . $id, 'refresh');
            }


        }
        else {
            //display the create user form
            //set the flash data error message if there is one
            $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            $this->data['message_success'] = $this->session->flashdata('message_success');

            $this->data['name']  = array('name' => 'name',
                                                'id' => 'name',
                                                'type' => 'text',
                                                'class' => 'form-control requiredTextField',
                                                'field-name' => 'nama',
                                                'placeholder' => 'Masukan nama menu',
                                                'value' => $this->form_validation->set_value('name',$form_data->name));


            $this->data['url']  = array('name' => 'url',
                                                'id' => 'url',
                                                'type' => 'text',
                                                'class' => 'form-control ',
                                                'field-name' => 'url',
                                                'placeholder' => 'Masukan url',
                                                'value' => $this->form_validation->set_value('url',$form_data->url));

            $this->data['parent_id']  = $this->form_validation->set_value('parent_id', $form_data->parent_id);

            $this->data['ddl_menu']  = $this->sidebar_menu_model->get_sidebar_menu_dropdown();

            $this->data['selected_groups'] = $this->sidebar_menu_model->get_selected_access($id); 
            $this->data['groups']  = $this->sidebar_menu_model->get_non_selected_access($id,$this->data['selected_groups']);
            $this->data['module_lists']=$this->sidebar_menu_model->get("module")->result();
            $this->data['action']  = 'edit';
            $this->data['cancel_url']  = base_url(SITE_ADMIN . '/sidebar_menu');
            $this->data['content'] .= $this->load->view('admin/sidebar-menu-add', $this->data, true);
            $this->render('admin');
        }
    }


    public function update_sequence(){
        $direction = $this->uri->segment(4);
        $id = $this->uri->segment(5);
        $parent_id = $this->input->post('parent_id');
        $sequence = $this->input->post('sequence');
        $function = 'get_'.$direction.'_record';

        $data = $this->sidebar_menu_model->$function($sequence, $parent_id);
        if(!is_null($data->id)){
            $temp = $data->sequence;

            $data_save['sequence'] = $data->sequence;
            $this->sidebar_menu_model->save('sidebar_menu', $data_save, $id);

            $data_save['sequence'] = $sequence;
            $this->sidebar_menu_model->save('sidebar_menu', $data_save, $data->id);

        }
    }

    public function delete_menu()
    {

        $id = $this->uri->segment(4);

        if (empty($id)) {
            redirect(SITE_ADMIN . '/sidebar_menu');
        }

        $this->load->model('sidebar_menu_model');
        $form_data = $this->sidebar_menu_model->get_one('sidebar_menu', $id);

        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/sidebar_menu');
        }

        $this->sidebar_menu_model->delete_by_limit('sidebar_menu_access', array('sidebar_menu_id'=> $id),0);
        $result = $this->sidebar_menu_model->delete('sidebar_menu', $id);
        if($result){
            $this->session->set_flashdata('message_success', $this->lang->line('success_delete'));
        }else{
            $this->session->set_flashdata('message', $this->lang->line('error_delete'));

        }            
  

        redirect(SITE_ADMIN . '/sidebar_menu', 'refresh');
    }

    /**
     * Validate URL format WITHOUt prefix https:// or http://
     * format url : /example/example
     * @param  [string] $str 
     *
     * @return [bool]      
     *
     * @author fkartika
     */
    public  function _validate_url_format($str){
        $pattern = "|^[a-z0-9#-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i";
        if (!preg_match($pattern, $str)){
            $this->form_validation->set_message('_validate_url_format', 'format url salah.');
            return FALSE;
        }
        
        return TRUE;
    } 
}