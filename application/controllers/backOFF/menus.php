<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/16/2014
 * Time: 2:17 PM
 */
class Menus extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('categories_model');
        $this->load->model('options_model');
        $this->load->model('inventory_model');
        if (!$this->groups_access->have_access('admincms')) {
          redirect(SITE_ADMIN);

        }
        $this->data['color_lists']=array(
          "red" => "Merah",
          "purple" => "Ungu",
          "blue" => "Biru",
          "green" => "Hijau",
          "yellow" => "Kuning"
        );
    }

    public function index()
    {
        $this->data['title']    ="Menu";
        $this->data['subtitle'] = "Menu";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        //load content
        $this->data['content'] .= $this->load->view('admin/menus-list', $this->data, true);
        $this->render('admin');
    }


    public function detail()
    {
        $id = $this->uri->segment(4);

        if (empty($id)) {
            redirect(SITE_ADMIN . '/menus');
        }

        $form_data = $this->categories_model->get_one_menu($id);

        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/menus');
        }


        $this->data['form_data'] = $form_data;

        $this->data['subtitle']  = "Detail Menu";

        //validate form input
        
        //display the create user form
        //set the flash data error message if there is one
        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['menu_name']   = array('name' => 'menu_name',
                                           'id' => 'menu_name',
                                           'type' => 'text',
                                           'class' => 'form-control',
                                           'placeholder' => 'Masukan nama menu',
                                           'readonly' => 'true',
                                           'value' => $this->form_validation->set_value('menu_name', $form_data->menu_name));
        $this->data['menu_hpp']    = array('name' => 'menu_hpp',
                                           'id' => 'menu_hpp',
                                           'type' => 'text',
                                           'class' => 'form-control',
                                           'readonly' => 'true',
                                           'placeholder' => 'Masukan harga awal',
                                           'value' => $this->form_validation->set_value('menu_hpp', $form_data->menu_hpp));
        $this->data['menu_price']  = array('name' => 'menu_price',
                                           'id' => 'menu_price',
                                           'type' => 'text',
                                           'class' => 'form-control',
                                           'placeholder' => 'Masukan harga',
                                           'readonly' => 'true',
                                           'value' => $this->form_validation->set_value('menu_price', $form_data->menu_price));
       
        $this->data['menu_category']  = array('name' => 'menu_category',
                                           'id' => 'menu_category',
                                           'type' => 'text',
                                           'class' => 'form-control',
                                           'placeholder' => '',
                                           'readonly' => 'true',
                                           'value' => $this->form_validation->set_value('menu_category', $form_data->category_name));

        $this->data['menu_outlet']  = array('name' => 'menu_outlet',
                                           'id' => 'menu_outlet',
                                           'type' => 'text',
                                           'class' => 'form-control',
                                           'placeholder' => '',
                                           'readonly' => 'true',
                                           'value' => $this->form_validation->set_value('menu_outlet', $form_data->outlet_name));
        $this->data['is_instant_yes'] = array('name' => 'is_instant',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Menu diproses ?',
            'placeholder' => '',
            'disabled' => '',
            'checked'=> ($form_data->is_instant == 0) ? 'true': '',
            'value' => "0");
        
        $this->data['is_instant_no'] = array('name' => 'is_instant',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Menu diproses ?',
            'placeholder' => '',
            'disabled' => '',
            'checked'=> ($form_data->is_instant == 1) ? 'true': '',
            'value' => "1");
        $this->data['process_checker_yes'] = array('name' => 'process_checker',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Masuk ke checker ?',
            'placeholder' => '',
            'disabled' => '',
            'checked'=> ($form_data->process_checker == 1) ? 'true': '',
            'value' => "1");
        
        $this->data['process_checker_no'] = array('name' => 'process_checker',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Masuk ke checker ?',
            'placeholder' => '',
            'disabled' => '',
            'checked'=> ($form_data->process_checker == 0) ? 'true': '',
            'value' => "0");
        $this->data['use_taxes_yes'] = array('name' => 'use_taxes',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Masuk ke checker ?',
            'placeholder' => '',
            'disabled' => '',
            'checked'=> ($form_data->use_taxes == 1) ? 'true': '',
            'value' => "1");
        
        $this->data['use_taxes_no'] = array('name' => 'use_taxes',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Kena pajak ?',
            'placeholder' => '',
            'disabled' => '',
            'checked'=> ($form_data->use_taxes == 0) ? 'true': '',
            'value' => "0");

        $this->data['data_inventory'] = $this->inventory_model->get_inventory();        
        $this->data['sidedish'] = $this->categories_model->get_sidedishes_dropdown();

        $this->data['menu_ingredient'] = $this->options_model->get_menu_ingredient($id);
        //get sidedish
        $this->data['side_dish_value'] = $this->options_model->get_side_dish($id, $form_data->is_promo);
        //get options
        $this->data['options_value'] = $this->options_model->get_options($id);
        $count_option_value          = $this->options_model->count_option_value($id);

        $this->data['sidedishCount']     = array('id' => 'sidedishCount',
                                                 'name' => 'sidedishCount',
                                                 'type' => 'hidden',
                                                 'value' => count($this->data['side_dish_value']));
        $this->data['optionsCount']      = array('id' => 'optionsCount',
                                                 'name' => 'optionsCount',
                                                 'type' => 'hidden',
                                                 'value' => count($this->data['options_value']));
        $this->data['optionsValueCount'] = array('id' => 'optionsValueCount',
                                                 'name' => 'optionsValueCount',
                                                 'type' => 'hidden',
                                                 'value' => $count_option_value);
        $this->data['ingredientCount']     = array('id' => 'ingredientCount',
                                                 'name' => 'ingredientCount',
                                                 'type' => 'hidden',
                                                 'value' => count($this->data['menu_ingredient']));
        //load content
        $this->data['content'] .= $this->load->view('admin/menus-edit', $this->data, true);
        $this->render('admin');
    }

    public function delete()
    {
        $id = $this->uri->segment(4);

        if (empty($id)) {
            redirect(SITE_ADMIN . '/menus');
        }

        $form_data = $this->categories_model->get_one_menu($id);

        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/menus');
        }

        if ($this->data['user_store_id'] != '0') {
            if ($this->data['user_store_id'] != $form_data->store_id) {
                $this->session->set_flashdata('message', $this->lang->line('error_permission'));
                redirect(SITE_ADMIN . '/menus', 'refresh');
            }
        }

        $check_constraint = $this->categories_model->total_rows('order_menu', array('menu_id' => $id));

        if ($check_constraint > 0) {
            $this->session->set_flashdata('message', 'Menu tidak kosong. Gagal menghapus data!');
            redirect(SITE_ADMIN . '/menus');
        }

        if (! empty($form_data->icon_url)) {
            $url = './' . $form_data->icon_url;

            if (file_exists($url)) {
                unlink($url);
            }
        }

        // wipe the slate
        $this->options_model->clear_options('menu_option', $id);
        $this->options_model->clear_options('menu_side_dish', $id);
        $result = $this->categories_model->delete('menu', $id);

        if ($result) {
            $this->session->set_flashdata('message_success', $this->lang->line('success_delete'));
        }
        else {
            $this->session->set_flashdata('message', $this->lang->line('error_delete'));
        }

        redirect(SITE_ADMIN . '/menus', 'refresh');
    }

    public function remove_image()
    {
        $id = $this->input->post('id');
        if (! empty($id)) {
            $form_data = $this->categories_model->get_one('menu', $id);

            if (empty($form_data)) {
                $status  = FALSE;
                $message = 'Gagal menghapus gambar';
            }
            else {

                if (! empty($form_data->icon_url)) {
                    $url = './' . $form_data->icon_url;

                    if (file_exists($url)) {
                        unlink($url);
                    }
                }

                $array = array('icon_url' => '');

                $save    = $this->categories_model->save('menu', $array, $id);
                $status  = TRUE;
                $message = 'Berhasil menghapus gambar';
            }
        }
        else {
            $status  = FALSE;
            $message = 'Gagal menghapus gambar';
        }
        echo json_encode(array('status' => $status, 'message' => $message));
    }

    public function getdatatables()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));


        $this->datatables->select('menu.id, menu_name, menu_price, category_name, outlet_name')
        ->from('menu')
        ->join('category', 'category.id = menu.category_id')
        ->join('outlet', 'outlet.id = category.outlet_id')
        ->where('menu.is_active', 1)
        ->add_column('actions', "<div class='btn-group'>
                                    <a href='" . base_url(SITE_ADMIN . '/menus/detail/$1') . "'  class='btn btn-default'><i class='fa fa-search'></i> Detail</a>
                                    
                                </div>", 'id');
        
        echo $this->datatables->generate();
    }

    public function get_inventory_unit()
    {
        $id = $this->input->post('id');
        $status = FALSE;
        $data = '';

        if (!empty($id)) {

            $form_data = $this->inventory_model->get_by('inventory', $id, 'id');
            $message = 'Berhasil mengambil data';
            $data = $form_data->unit; 

        }
        else {
            $message = 'Gagal mengambil data';
        }
        echo json_encode(array(
            'status' => $status, 
            'message' => $message,
            'data'  => $data
            ));

    }

}