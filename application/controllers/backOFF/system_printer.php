<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @Author: Fitria Kartika
 * @Date:   2015-10-12 11:33:20
 * @Last Modified by:   Fitria Kartika
 * @Last Modified time: 2015-11-02 12:07:26
 */

class System_printer extends Admin_Controller
{

    private $created_at ;
    private $created_by ;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('setting_printer_model');

        $this->created_at = date("Y-m-d H:i:s") ;
        $this->created_by =  $this->data['user_profile_data']->id;

    }


     public function index(){

        $this->data['title']    = "Setting Printer";
        $this->data['subtitle'] = "Setting Printer";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');
        
        $this->data['printer_type_id']  = $this->form_validation->set_value('printer_type_id');
        $this->data['ddl_printer_type']  = $this->setting_printer_model->get_printer_dropdown(false);

        $this->data['add_url']  = base_url(SITE_ADMIN . '/system_printer/add_printer');
        $this->data['data_url'] = base_url(SITE_ADMIN . '/system_printer/get_printer_list_data');;
        //load content
        $this->data['content'] .= $this->load->view('admin/setting-printer-list', $this->data, true);
        $this->render('admin');
    }


    public function get_printer_list_data()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $printer_type_id =0;
        if($this->input->post('printer_type_id')!==0){
            $printer_type_id =$this->input->post('printer_type_id');
        }

        $this->datatables->select('sp.id, sp.alias_name, sp.name_printer, pt.value as type, sp.printer_width, sp.font_size', FALSE)
        ->from("setting_printer sp ")
        ->join('enum_printer_type pt', 'pt.id = sp.type','left')
        ->group_by('sp.id')
        // ->where('sp.type', $printer_type_id)
        ->add_column('actions', "<div class='btn-group'>
                                    <a href='" . base_url(SITE_ADMIN . '/system_printer/edit_printer/$1') . "' class='btn btn-default' rel=''><i class='fa fa-pencil'></i> Edit</a>
                                    <a href='" . base_url(SITE_ADMIN . '/system_printer/delete_printer/$1') . "' class='btn btn-danger deleteNow' rel='printer'><i class='fa fa-trash-o'></i> Hapus</a>
                                    <a href='" . base_url(SITE_ADMIN . '/system_printer/test_printer/$1') . "' class='btn btn-info' rel='printer'><i class='fa fa-print'></i> Test</a>
                                </div>", 'id');
        
        if ($printer_type_id != 0) {
            $this->datatables->where('sp.type', $printer_type_id);
        }

        echo $this->datatables->generate();
    }


    public function add_printer()
    {

        $this->data['title']    = "Tambah Printer";
        $this->data['subtitle'] = "Tambah Printer";

        $this->form_validation->set_rules('type_id','tipe printer','required');
        $this->form_validation->set_rules('name_printer', 'nama printer', 'required');
        if ($this->input->post("type_id") == 6 || $this->input->post("type_id") == 7 || $this->input->post("type_id") == 8 || $this->input->post("type_id") == 9) {
            # code...
        } else {                        
            // $this->form_validation->set_rules('alias_name','nama alias printer' , 'required');
            $this->form_validation->set_rules('outlet_id','outlet', 'required');
            $this->form_validation->set_rules('printer_width','lebar','required');
            $this->form_validation->set_rules('font_size_bill','ukuran huruf','required');
            $this->form_validation->set_rules('is_use_logo','logo','required');
        }
        if ($this->form_validation->run() == true) {    
 
            $data_save = array('name_printer' => $this->input->post('name_printer'),
                                'alias_name' => $this->input->post('alias_name'),
                                'outlet_id' =>  $this->input->post('outlet_id'),
                                'printer_width' => $this->input->post('printer_width'),
                                'font_size' => $this->input->post("font_size_bill"),
                                'logo' => $this->input->post("is_use_logo"),
                                'type' => $this->input->post("type_id"),
                                'format_order' => $this->input->post("format_order")
                                );

            $setting_printer_id = $this->setting_printer_model->save('setting_printer', $data_save);
            if ($setting_printer_id === false) {
                $this->session->set_flashdata('message',$this->lang->line('error_add'));
            }
            else {
                if($this->input->post('table_list')) {

                    $access = explode(",",$this->input->post('table_list'));                    
                    foreach ($access as $key => $row) {
                        $data_save = array(
                            'printer_id' => $setting_printer_id,
                            'table_id' => $row,
                            'created_at' => $this->created_at,
                            'created_by' => $this->created_by,
                            );
                        $this->setting_printer_model->save('setting_printer_table', $data_save);
                    }

                }

                $this->session->set_flashdata('message_success', $this->lang->line('success_add'));
            }
            $btnaction = $this->input->post('btnAction');
            if ($btnaction == 'save_exit') {
                redirect(SITE_ADMIN . '/system_printer', 'refresh');
            }
            else {
                redirect(SITE_ADMIN . '/system_printer/add_printer/', 'refresh');
            }


        }
        else {
            //display the create user form
            //set the flash data error message if there is one
            $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            $this->data['message_success'] = $this->session->flashdata('message_success');

            $this->data['name_printer']  = array('name' => 'name_printer',
                                                'id' => 'name_printer',
                                                'type' => 'text',
                                                'class' => 'form-control requiredTextField',
                                                'field-name' => 'nama printer',
                                                'placeholder' => 'Masukan nama printer',
                                                'value' => $this->form_validation->set_value('name_printer'));


            $this->data['alias_name']  = array('name' => 'alias_name',
                                                'id' => 'alias_name',
                                                'type' => 'text',
                                                'class' => 'form-control ',
                                                'field-name' => 'nama alias printer',
                                                'placeholder' => 'Masukan nama alias printer',
                                                'value' => $this->form_validation->set_value('alias_name'));


            $this->data['printer_width_48'] = array('name' => 'printer_width',
                'type' => 'radio',
                'class' => 'requiredTextField',
                'field-name' => 'Lebar Printer',
                'placeholder' => '',
                'checked' => ($this->input->post('printer_width') == "48") ? 'true' : '',
                'value' => "48");

            $this->data['printer_width_72'] = array('name' => 'printer_width',
                'type' => 'radio',
                'class' => 'requiredTextField',
                'field-name' => 'Lebar Printer',
                'placeholder' => '',
                'checked' => ($this->input->post('printer_width') == "72") ? 'true' : '',
                'value' => "72");
            $this->data['printer_width_72_plus'] = array('name' => 'printer_width',
                'type' => 'radio',
                'class' => 'requiredTextField',
                'field-name' => 'Lebar Printer',
                'placeholder' => '',
                'checked' => ($this->input->post('printer_width') == "72+") ? 'true' : '',
                'value' => "72+");
            $this->data['printer_generic'] = array('name' => 'printer_width',
                'type' => 'radio',
                'class' => 'requiredTextField',
                'field-name' => 'Lebar Printer',
                'placeholder' => '',
                'checked' => ($this->input->post('printer_width') == "generic") ? 'true' : '',
                'value' => "generic");

            $this->data['font_size_bill_1'] = array('name' => 'font_size_bill',
                'type' => 'radio',
                'class' => 'requiredTextField',
                'field-name' => 'Font Size Bill',
                'placeholder' => '',
                // 'checked' => ($this->input->post['font_size_bill'] == 1) ? 'true' : '',
                'value' => "1");

            $this->data['font_size_bill_2'] = array('name' => 'font_size_bill',
                'type' => 'radio',
                'class' => 'requiredTextField',
                'field-name' => 'Font Size Bill',
                'placeholder' => '',
                // 'checked' => ($this->input->post['font_size_bill'] == 2) ? 'true' : '',
                'value' => "2");

            $this->data['use_logo_yes'] = array('name' => 'is_use_logo',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Use Logo',
            'placeholder' => '',
            'checked' => ($this->input->post('is_use_logo') === 1) ? 'true' : '',
            'value' => "1");

            $this->data['use_logo_no'] = array('name' => 'is_use_logo',
                'type' => 'radio',
                'class' => 'requiredTextField',
                'field-name' => 'Use Logo',
                'placeholder' => '',
                'checked' => ($this->input->post('is_use_logo') === 0) ? 'true' : '',
                'value' => "0");

            // add setting for font size in order
            $this->data['format_order_1'] = array(
                'name'          => 'format_order',
                'type'          => 'radio',
                'class'         => 'requiredTextField',
                'field-name'    => 'Format Order',
                'placeholder'   => '',
                'checked'       => ($this->input->post('format_order') === 1) ? 'true' : '',
                'value'         => '1');

            $this->data['format_order_2'] = array(
                'name'          => 'format_order',
                'type'          => 'radio',
                'class'         => 'requiredTextField',
                'field-name'    => 'Format Order',
                'placeholder'   => '',
                'checked'       => ($this->input->post('format_order') === 2) ? 'true' : '',
                'value'         => '2');

            $this->data['printer_type_id']  = $this->form_validation->set_value('printer_type_id', $this->input->post('type_id'));
            $this->data['outlet_id']  = $this->form_validation->set_value('outlet_id', $this->input->post('outlet_id'));
            $this->data['ddl_outlet']=$this->setting_printer_model->get_outlet_dropdown($this->data['setting']['store_id']);
            $this->data['ddl_printer_type']  = $this->setting_printer_model->get_printer_dropdown(true);
            $this->data['selected_table'] = array();
            $this->data['tables']  = $this->setting_printer_model->get('table')->result();
            $this->data['cancel_url']  = base_url(SITE_ADMIN . '/system_printer');
            $this->data['action']  = 'add';
            $this->data['content'] .= $this->load->view('admin/setting-printer-add', $this->data, true);
            $this->render('admin');
        }

    }


    public function edit_printer()
    {

        $id = $this->uri->segment(4);
        
        $form_data = $this->setting_printer_model->get_one('setting_printer', $id);
        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/system_printer');
        }

        $this->data['form_data'] = $form_data;
        $this->data['title']    = "Edit Printer";
        $this->data['subtitle'] = "Edit Printer";

        $this->form_validation->set_rules('type_id','tipe printer','required');
        $this->form_validation->set_rules('name_printer', 'nama printer', 'required');
        if ($this->input->post("type_id") == 6 || $this->input->post("type_id") == 7 || $this->input->post("type_id") == 8 || $this->input->post("type_id") == 9) {
            # code...
        } else {                        
            // $this->form_validation->set_rules('alias_name','nama alias printer' , 'required');
            $this->form_validation->set_rules('outlet_id','outlet', 'required');
            $this->form_validation->set_rules('printer_width','lebar','required');
            $this->form_validation->set_rules('font_size_bill','ukuran huruf','required');
            $this->form_validation->set_rules('is_use_logo','logo','required');
        }
        if ($this->form_validation->run() == true) {    

            $data_save = array('name_printer' => $this->input->post('name_printer'),
                                'alias_name' => $this->input->post('alias_name'),
                                'outlet_id' =>  $this->input->post('outlet_id'),
                                'printer_width' => $this->input->post('printer_width'),
                                'font_size' => $this->input->post("font_size_bill"),
                                'logo' => $this->input->post("is_use_logo"),
                                'type' => $this->input->post("type_id"),
                                'format_order' => $this->input->post("format_order")
                                );

            $setting_printer_id = $this->setting_printer_model->save('setting_printer', $data_save, $id);
            if ($setting_printer_id === false) {
                $this->session->set_flashdata('message',$this->lang->line('error_add'));
            }
            else {
                $this->setting_printer_model->delete_by_limit('setting_printer_table', array('printer_id'=> $id),0);
                if($this->input->post('table_list')) {
                    $access = explode(",",$this->input->post('table_list')); 
                    foreach ($access as $key => $row) {
                        $data_save = array(
                            'printer_id' => $setting_printer_id,
                            'table_id' => $row,
                            'created_at' => $this->created_at,
                            'created_by' => $this->created_by,
                            );
                        $this->setting_printer_model->save('setting_printer_table', $data_save);
                    }

                }

                $this->session->set_flashdata('message_success', $this->lang->line('success_add'));
            }
            $btnaction = $this->input->post('btnAction');
            if ($btnaction == 'save_exit') {
                redirect(SITE_ADMIN . '/system_printer', 'refresh');
            }
            else {
                redirect(SITE_ADMIN . '/system_printer/edit_printer/' . $id, 'refresh');
            }


        }
        else {
            //display the create user form
            //set the flash data error message if there is one
            $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            $this->data['message_success'] = $this->session->flashdata('message_success');

            $this->data['name_printer']  = array('name' => 'name_printer',
                                                'id' => 'name_printer',
                                                'type' => 'text',
                                                'class' => 'form-control requiredTextField',
                                                'field-name' => 'nama printer',
                                                'placeholder' => 'Masukan nama printer',
                                                'value' => $this->form_validation->set_value('name_printer',$form_data->name_printer));


            $this->data['alias_name']  = array('name' => 'alias_name',
                                                'id' => 'alias_name',
                                                'type' => 'text',
                                                'class' => 'form-control ',
                                                'field-name' => 'nama alias printer',
                                                'placeholder' => 'Masukan nama alias printer',
                                                'value' => $this->form_validation->set_value('alias_name',$form_data->alias_name));

            $this->data['printer_width_48'] = array('name' => 'printer_width',
                'type' => 'radio',
                'class' => 'requiredTextField',
                'field-name' => 'Lebar Printer',
                'placeholder' => '',
                'checked' => ($form_data->printer_width == "48") ? 'true' : '',
                'value' => "48");

            $this->data['printer_width_72'] = array('name' => 'printer_width',
                'type' => 'radio',
                'class' => 'requiredTextField',
                'field-name' => 'Lebar Printer',
                'placeholder' => '',
                'checked' => ($form_data->printer_width == "72") ? 'true' : '',
                'value' => "72");
            $this->data['printer_width_72_plus'] = array('name' => 'printer_width',
                'type' => 'radio',
                'class' => 'requiredTextField',
                'field-name' => 'Lebar Printer',
                'placeholder' => '',
                'checked' => ($form_data->printer_width == "72+") ? 'true' : '',
                'value' => "72+");
            $this->data['printer_generic'] = array('name' => 'printer_width',
                'type' => 'radio',
                'class' => 'requiredTextField',
                'field-name' => 'Lebar Printer',
                'placeholder' => '',
                'checked' => ($form_data->printer_width == "generic") ? 'true' : '',
                'value' => "generic");


            $this->data['font_size']  = array('name' => 'font_size',
                                                'id' => 'font_size',
                                                'type' => 'text',
                                                'class' => 'form-control ',
                                                'field-name' => 'ukuran huruf',
                                                'placeholder' => 'Masukan ukuran huruf',
                                                'value' => $this->form_validation->set_value('font_size',$form_data->font_size));

            $this->data['font_size_bill_1'] = array('name' => 'font_size_bill',
                'type' => 'radio',
                'class' => 'requiredTextField',
                'field-name' => 'Font Size Bill',
                'placeholder' => '',
                'checked' => ($form_data->font_size == 1) ? 'true' : '',
                'value' => "1");

            $this->data['font_size_bill_2'] = array('name' => 'font_size_bill',
                'type' => 'radio',
                'class' => 'requiredTextField',
                'field-name' => 'Font Size Bill',
                'placeholder' => '',
                'checked' => ($form_data->font_size == 2) ? 'true' : '',
                'value' => "2");

            $this->data['use_logo_yes'] = array('name' => 'is_use_logo',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Use Logo',
            'placeholder' => '',
            'checked' => ($form_data->logo === '1') ? 'true' : '',
            'value' => "1");

            $this->data['use_logo_no'] = array('name' => 'is_use_logo',
                'type' => 'radio',
                'class' => 'requiredTextField',
                'field-name' => 'Use Logo',
                'placeholder' => '',
                'checked' => ($form_data->logo === '0') ? 'true' : '',
                'value' => "0");

            // add setting for font size in order
            $this->data['format_order_1'] = array(
                'name'          => 'format_order',
                'type'          => 'radio',
                'class'         => 'requiredTextField',
                'field-name'    => 'Format Order',
                'placeholder'   => '',
                'checked'       => ($form_data->format_order == 1) ? 'true' : '',
                'value'         => '1');

            $this->data['format_order_2'] = array(
                'name'          => 'format_order',
                'type'          => 'radio',
                'class'         => 'requiredTextField',
                'field-name'    => 'Format Order',
                'placeholder'   => '',
                'checked'       => ($form_data->format_order == 2) ? 'true' : '',
                'value'         => '2');

            $this->data['printer_type_id']  = $this->form_validation->set_value('printer_type_id', $form_data->type);
            $this->data['outlet_id']  = $this->form_validation->set_value('outlet_id', $form_data->outlet_id);
            $this->data['ddl_outlet']=$this->setting_printer_model->get_outlet_dropdown($this->data['setting']['store_id']);
            $this->data['ddl_printer_type']  = $this->setting_printer_model->get_printer_dropdown(true);
            $this->data['selected_table'] = $this->setting_printer_model->get_selected_tables($id);
            $this->data['tables']  = $this->setting_printer_model->get_non_selected_tables($id,$this->data['selected_table']);
            
            $this->data['action']  = 'edit';
            $this->data['cancel_url']  = base_url(SITE_ADMIN . '/system_printer');
            $this->data['content'] .= $this->load->view('admin/setting-printer-add', $this->data, true);
            $this->render('admin');
        }
    }

    public function delete_printer()
    {

        $id = $this->uri->segment(4);

        if (empty($id)) {
            redirect(SITE_ADMIN . '/system_printer');
        }

        $form_data = $this->setting_printer_model->get_one('setting_printer', $id);

        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/system_printer');
        }

        $this->sidebar_menu_model->delete_by_limit('setting_printer_table', array('printer_id'=> $id),0);
        $result = $this->setting_printer_model->delete('setting_printer', $id);
        if($result){
            $this->session->set_flashdata('message_success', $this->lang->line('success_delete'));
        }else{
            $this->session->set_flashdata('message', $this->lang->line('error_delete'));

        }            
  

        redirect(SITE_ADMIN . '/system_printer', 'refresh');
    }

    public function test_printer()
    {

        $id = $this->uri->segment(4);

        if (empty($id)) {
            redirect(SITE_ADMIN . '/system_printer');
        }

        $form_data = $this->setting_printer_model->get_one('setting_printer', $id);
        
        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/system_printer');
        }

        $this->load->helper(array('printer'));

        $nama_printer = $form_data->name_printer;   

        //build object printer setting for printer helper
        $printer_setting = new stdClass();
        $printer_setting->id = $form_data->id;
        $printer_setting->name = $form_data->alias_name;
        $printer_setting->value = $form_data->name_printer;
        $printer_setting->default = $form_data->logo;
        $printer_setting->description = $form_data->printer_width;
        $printer_setting->font_size = $form_data->font_size;
        if ($form_data->printer_width == 'generic') {
            @generic_printer_test($nama_printer, FALSE, TRUE, $printer_setting);
        } else {
            @printer_test_cashier($nama_printer, FALSE, TRUE, $printer_setting);
        }
        

        redirect(SITE_ADMIN . '/system_printer', 'refresh');
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