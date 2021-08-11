<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/16/2014
 * Time: 9:58 AM
 */
class Categories extends Admin_Controller
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
        $this->data['title']    = "Categories";
        $this->data['subtitle'] = "Categories";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        //load content
        $this->data['content'] .= $this->load->view('admin/categories-list', $this->data, true);
        $this->render('admin');
    }

    public function add()
    {
        $this->data['title']    = "Add Category";
        $this->data['subtitle'] = "Add Category";

        //validate form input
        $this->form_validation->set_rules('category_name', 'Category Name', 'required|xss_clean|min_length[1]|max_length[50]');
        $this->form_validation->set_rules('outlet_id', 'Outlet', 'required|xss_clean|callback__dropdown_check');

        if ($this->form_validation->run() == true) {

            $image_name = '';
            $isUpload   = TRUE;
            if (! empty($_FILES['icon_url']['name'])) {
                //upload config
                $newname                 = $this->generate_random_name();
                $config['upload_path']   = './uploads/category/';
                $config['allowed_types'] = 'jpeg|jpg|jpe|png';
                $config['max_size']      = '1000';//Kilo Byte
                $config['overwrite']     = FALSE;
                $config['file_name']     = $newname;
                $this->load->library('upload', $config);

                if (! $this->upload->do_upload('icon_url')) {
                    $this->session->set_flashdata('message', $this->upload->display_errors());
                    $isUpload = FALSE;
                }
                else {
                    $this->load->library('image_moo');
                    $this->image_moo->load($this->upload->data()['full_path'])->set_background_colour("#FFFFFF")->resize(100, 100, TRUE)->save_pa('thumb_', '', FALSE);

                    $image_name = 'uploads/category/thumb_' . $this->upload->data()['file_name'];
                    $isUpload   = TRUE;
                    if (file_exists($this->upload->data()['full_path'])) {
                        unlink($this->upload->data()['full_path']);
                    }
                }
            }

            if ($isUpload === TRUE) {
                $array = array('category_name' => $this->input->post('category_name'),
                               'outlet_id' => $this->input->post('outlet_id'),
                               'icon_url' => $image_name);

                $save = $this->categories_model->save('category', $array);

                if ($save === false) {
                    $this->session->set_flashdata('message', 'Failed save category');
                }
                else {
                    $this->session->set_flashdata('message_success', 'Success save category');
                }
            }

            $btnaction = $this->input->post('btnAction');
            if ($btnaction == 'save_exit') {
                redirect(SITE_ADMIN . '/categories', 'refresh');
            }
            else {
                redirect(SITE_ADMIN . '/categories/add/', 'refresh');
            }


        }
        else {
            //display the create user form
            //set the flash data error message if there is one
            $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            $this->data['message_success'] = $this->session->flashdata('message_success');

            $this->data['category_name'] = array('name' => 'category_name',
                                                 'id' => 'category_name',
                                                 'type' => 'text',
                                                 'class' => 'form-control requiredTextField',
                                                 'field-name' => 'Category name',
                                                 'placeholder' => 'Enter Category Name',
                                                 'value' => $this->form_validation->set_value('category_name'));
            $outlet                      = $this->categories_model->get_outlet();
            $this->data['outlet_id']     = $outlet;
            $this->data['icon_url']      = array('name' => 'icon_url',
                                                 'id' => 'icon_url',
                                                 'type' => 'file',
                                                 'class' => 'form-control maxUploadSize',
                                                 'placeholder' => '',
                                                 'data-maxsize' => '1000000', // byte
                                                 'value' => $this->form_validation->set_value('icon_url'));
            //load content
            $this->data['content'] .= $this->load->view('admin/categories-add', $this->data, true);
            $this->render('admin');
        }

    }

    public function edit()
    {
        $id = $this->uri->segment(4);

        if (empty($id)) {
            redirect(SITE_ADMIN . '/categories');
        }

        $form_data = $this->categories_model->get_one('category', $id);

        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/categories');
        }

        $this->data['form_data'] = $form_data;
        $this->data['subtitle']  = "Edit Category";

        //validate form input
        $this->form_validation->set_rules('category_name', 'Category Name', 'required|xss_clean|min_length[1]|max_length[50]');
        $this->form_validation->set_rules('outlet_id', 'Outlet', 'required|xss_clean|callback__dropdown_check');

        if (isset($_POST) && ! empty($_POST)) {

            if ($this->form_validation->run() === TRUE) {

                $image_name = $form_data->icon_url;
                $isUpload   = TRUE;
                if (! empty($_FILES['icon_url']['name'])) {
                    //upload config
                    $newname                 = $this->generate_random_name();
                    $config['upload_path']   = './uploads/category/';
                    $config['allowed_types'] = 'jpeg|jpg|jpe|png';
                    $config['max_size']      = '1000';
                    $config['overwrite']     = FALSE;
                    $config['file_name']     = $newname;
                    $this->load->library('upload', $config);

                    if (! $this->upload->do_upload('icon_url')) {
                        $this->session->set_flashdata('message', $this->upload->display_errors());
                        $isUpload = FALSE;
                    }
                    else {
                        $this->load->library('image_moo');
                        $this->image_moo->load($this->upload->data()['full_path'])->set_background_colour("#FFFFFF")->resize(100, 100, TRUE)->save_pa('thumb_', '', FALSE);

                        $image_name = 'uploads/category/thumb_' . $this->upload->data()['file_name'];
                        $isUpload   = TRUE;
                        if (file_exists($this->upload->data()['full_path'])) {
                            unlink($this->upload->data()['full_path']);
                        }

                        if (! empty($form_data->icon_url)) {
                            $url = './' . $form_data->icon_url;

                            if (file_exists($url)) {
                                unlink($url);
                            }
                        }
                    }
                }

                if ($isUpload === TRUE) {
                    $array = array('category_name' => $this->input->post('category_name'),
                                   'outlet_id' => $this->input->post('outlet_id'),
                                   'icon_url' => $image_name);

                    $save = $this->categories_model->save('category', $array, $id);

                    if ($save === false) {
                        $this->session->set_flashdata('message', 'Failed save category');
                    }
                    else {
                        $this->session->set_flashdata('message_success', 'Success save category');
                    }

                }
                $btnaction = $this->input->post('btnAction');
                if ($btnaction == 'save_exit') {
                    redirect(SITE_ADMIN . '/categories', 'refresh');
                }
                else {
                    redirect(SITE_ADMIN . '/categories/edit/' . $id, 'refresh');
                }


            }
        }

        //display the edit user form
        $this->data['csrf'] = $this->_get_csrf_nonce();

        //set the flash data error message if there is one
        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['category_name'] = array('name' => 'category_name',
                                             'id' => 'category_name',
                                             'type' => 'text',
                                             'class' => 'form-control requiredTextField',
                                             'field-name' => 'Category name',
                                             'placeholder' => 'Enter Category Name',
                                             'value' => $this->form_validation->set_value('category_name', $form_data->category_name));
        $outlet                      = $this->categories_model->get_outlet();
        $this->data['outlet_id']     = $outlet;
        $this->data['icon_url']      = array('name' => 'icon_url',
                                             'id' => 'icon_url',
                                             'type' => 'file',
                                             'class' => 'form-control maxUploadSize',
                                             'placeholder' => '',
                                             'data-maxsize' => '1000000', // byte
                                             'value' => $this->form_validation->set_value('icon_url'));

        $this->data['content'] .= $this->load->view('admin/categories-edit.php', $this->data, true);

        $this->render('admin');
    }

    public function delete()
    {
        $id = $this->uri->segment(4);

        if (empty($id)) {
            redirect(SITE_ADMIN . '/categories');
        }

        $form_data = $this->categories_model->get_one('category', $id);

        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/categories');
        }

        if (! empty($form_data->icon_url)) {
            $url = './' . $form_data->icon_url;

            if (file_exists($url)) {
                unlink($url);
            }
        }

        $result = $this->categories_model->delete('category', $id);
        if ($result) {
            $this->session->set_flashdata('message_success', 'Category successfully deleted');
        }
        else {
            $this->session->set_flashdata('message', 'Error. Failed to delete');
        }

        redirect(SITE_ADMIN . '/categories', 'refresh');
    }

    public function remove_image()
    {
        $id = $this->input->post('id');
        if (! empty($id)) {
            $form_data = $this->categories_model->get_one('category', $id);

            if (empty($form_data)) {
                $status  = FALSE;
                $message = 'Failed remove image';
            }
            else {

                if (! empty($form_data->icon_url)) {
                    $url = './' . $form_data->icon_url;

                    if (file_exists($url)) {
                        unlink($url);
                    }
                }

                $array = array('icon_url' => '');

                $save    = $this->categories_model->save('category', $array, $id);
                $status  = TRUE;
                $message = 'Success remove image';
            }
        }
        else {
            $status  = FALSE;
            $message = 'Failed remove image';
        }
        echo json_encode(array('status' => $status, 'message' => $message));
    }

    public function getdatatables()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $this->datatables->select('category.id, category_name, outlet_name,store_name')->from('category')->join('outlet', 'outlet.id = category.outlet_id')->join('store', 'store.id = outlet.store_id')->where('category.is_active', 1)->add_column('actions', "<div class='btn-group'>
                                    <a href='" . base_url(SITE_ADMIN . '/categories/edit/$1') . "'  class='btn btn-default'><i class='fa fa-pencil'></i> edit</a>
                                    <a href='" . base_url(SITE_ADMIN . '/categories/delete/$1') . "' class='btn btn-danger deleteNow' rel='Category'><i class='fa fa-trash-o'></i> Delete</a>
                                </div>", 'id');
        echo $this->datatables->generate();
    }
}