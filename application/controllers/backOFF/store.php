<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/17/2014
 * Time: 8:27 PM
 */
class Store extends Admin_Controller
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
        $this->data['title']    = "Store";
        $this->data['subtitle'] = "Store";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        //load content
        $this->data['content'] .= $this->load->view('admin/store-list', $this->data, true);
        $this->render('admin');
    }

    public function add()
    {
        $this->data['title']    = "Add Store";
        $this->data['subtitle'] = "Add Store";

        //validate form input
        $this->form_validation->set_rules('store_name', 'Store Name', 'required|xss_clean|min_length[1]|max_length[100]');
        $this->form_validation->set_rules('store_address', 'Store Address', 'xss_clean');
        $this->form_validation->set_rules('store_phone', 'Store Phone', 'xss_clean|min_length[6]|max_length[16]');

        if ($this->form_validation->run() == true) {

            $image_name = '';
            $isUpload   = TRUE;
            if (! empty($_FILES['store_logo']['name'])) {
                //upload config
                $newname                 = $this->generate_random_name();
                $config['upload_path']   = './uploads/store/';
                $config['allowed_types'] = 'jpeg|jpg|jpe|png';
                $config['max_size']      = '1000';
                $config['overwrite']     = FALSE;
                $config['file_name']     = $newname;
                $this->load->library('upload', $config);

                if (! $this->upload->do_upload('store_logo')) {
                    $this->session->set_flashdata('message', $this->upload->display_errors());
                    $isUpload = FALSE;
                }
                else {
                    $this->load->library('image_moo');
                    $this->image_moo->load($this->upload->data()['full_path'])->set_background_colour("#FFFFFF")->resize(100, 100, TRUE)->save_pa('thumb_', '', FALSE);

                    $image_name = 'uploads/store/thumb_' . $this->upload->data()['file_name'];
                    $isUpload   = TRUE;
                    if (file_exists($this->upload->data()['full_path'])) {
                        unlink($this->upload->data()['full_path']);
                    }
                }
            }

            if ($isUpload === TRUE) {
                $array = array('store_name' => $this->input->post('store_name'),
                               'store_address' => $this->input->post('store_address'),
                               'store_phone' => $this->input->post('store_phone'),
                               'store_logo' => $image_name);

                $save = $this->categories_model->save('store', $array);

                if ($save === false) {
                    $this->session->set_flashdata('message', 'Failed save store');
                }
                else {
                    $this->session->set_flashdata('message_success', 'Success save store');
                }
            }

            $btnaction = $this->input->post('btnAction');
            if ($btnaction == 'save_exit') {
                redirect(SITE_ADMIN . '/store', 'refresh');
            }
            else {
                redirect(SITE_ADMIN . '/store/add/', 'refresh');
            }


        }
        else {
            //display the create user form
            //set the flash data error message if there is one
            $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            $this->data['message_success'] = $this->session->flashdata('message_success');

            $this->data['store_name']    = array('name' => 'store_name',
                                                 'id' => 'store_name',
                                                 'type' => 'text',
                                                 'class' => 'form-control requiredTextField',
                                                 'field-name' => 'Store name',
                                                 'placeholder' => 'Enter Store Name',
                                                 'value' => $this->form_validation->set_value('store_name'));
            $this->data['store_address'] = array('name' => 'store_address',
                                                 'id' => 'store_address',
                                                 'type' => 'text',
                                                 'class' => 'form-control',
                                                 'rows' => '5',
                                                 'field-name' => 'Store Address',
                                                 'placeholder' => 'Enter Store Address',
                                                 'value' => $this->form_validation->set_value('store_address'));
            $this->data['store_phone']   = array('name' => 'store_phone',
                                                 'id' => 'store_phone',
                                                 'type' => 'text',
                                                 'class' => 'form-control NumericWithZero',
                                                 'field-name' => 'Store phone',
                                                 'placeholder' => 'Enter Phone Name',
                                                 'value' => $this->form_validation->set_value('store_phone'));
            $this->data['store_logo']    = array('name' => 'store_logo',
                                                 'id' => 'store_logo',
                                                 'type' => 'file',
                                                 'class' => 'form-control maxUploadSize',
                                                 'placeholder' => '',
                                                 'data-maxsize' => '1000000', // byte
                                                 'value' => $this->form_validation->set_value('store_logo'));
            //load content
            $this->data['content'] .= $this->load->view('admin/store/store-add', $this->data, true);
            $this->render('admin');
        }

    }

    public function edit()
    {
        $id = $this->uri->segment(4);

        if (empty($id)) {
            redirect(SITE_ADMIN . '/store');
        }

        $form_data = $this->categories_model->get_one('store', $id);

        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/store');
        }

        $this->data['form_data'] = $form_data;
          $this->data['title']    = "edit Store";
        $this->data['subtitle']  = "Edit Store";

        //validate form input
        $this->form_validation->set_rules('store_name', 'Store Name', 'required|xss_clean|min_length[1]|max_length[100]');
        $this->form_validation->set_rules('store_address', 'Store Address', 'xss_clean');
        $this->form_validation->set_rules('store_phone', 'Store Phone', 'xss_clean|min_length[6]|max_length[16]');

        if (isset($_POST) && ! empty($_POST)) {

            if ($this->form_validation->run() === TRUE) {

                $image_name = $form_data->store_logo;
                $isUpload   = TRUE;
                if (! empty($_FILES['store_logo']['name'])) {
                    //upload config
                    $newname                 = $this->generate_random_name();
                    $config['upload_path']   = './uploads/store/';
                    $config['allowed_types'] = 'jpeg|jpg|jpe|png';
                    $config['max_size']      = '1000';
                    $config['overwrite']     = FALSE;
                    $config['file_name']     = $newname;
                    $this->load->library('upload', $config);

                    if (! $this->upload->do_upload('store_logo')) {
                        $this->session->set_flashdata('message', $this->upload->display_errors());
                        $isUpload = FALSE;
                    }
                    else {
                        $this->load->library('image_moo');
                        $this->image_moo->load($this->upload->data()['full_path'])->set_background_colour("#FFFFFF")->resize(100, 100, TRUE)->save_pa('thumb_', '', FALSE);

                        $image_name = 'uploads/store/thumb_' . $this->upload->data()['file_name'];
                        $isUpload   = TRUE;
                        if (file_exists($this->upload->data()['full_path'])) {
                            unlink($this->upload->data()['full_path']);
                        }

                        if (! empty($form_data->store_logo)) {
                            $url = './' . $form_data->store_logo;

                            if (file_exists($url)) {
                                unlink($url);
                            }
                        }
                    }
                }

                if ($isUpload === TRUE) {
                    $array = array('store_name' => $this->input->post('store_name'),
                                   'store_address' => $this->input->post('store_address'),
                                   'store_phone' => $this->input->post('store_phone'),
                                   'store_logo' => $image_name);

                    $save = $this->categories_model->save('store', $array, $id);

                    if ($save === false) {
                        $this->session->set_flashdata('message', 'Failed save store');
                    }
                    else {
                        $this->session->set_flashdata('message_success', 'Success save store');
                    }

                }
                $btnaction = $this->input->post('btnAction');
                if ($btnaction == 'save_exit') {
                    redirect(SITE_ADMIN . '/store', 'refresh');
                }
                else {
                    redirect(SITE_ADMIN . '/store/edit/' . $id, 'refresh');
                }


            }
        }

        //display the edit user form
        $this->data['csrf'] = $this->_get_csrf_nonce();

        //set the flash data error message if there is one
        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['store_name']    = array('name' => 'store_name',
                                             'id' => 'store_name',
                                             'type' => 'text',
                                             'class' => 'form-control requiredTextField',
                                             'field-name' => 'Store name',
                                             'placeholder' => 'Enter Store Name',
                                             'value' => $this->form_validation->set_value('store_name', $form_data->store_name));
        $this->data['store_address'] = array('name' => 'store_address',
                                             'id' => 'store_address',
                                             'type' => 'text',
                                             'class' => 'form-control',
                                             'rows' => '5',
                                             'field-name' => 'Store Address',
                                             'placeholder' => 'Enter Store Address',
                                             'value' => $this->form_validation->set_value('store_address', $form_data->store_address));
        $this->data['store_phone']   = array('name' => 'store_phone',
                                             'id' => 'store_phone',
                                             'type' => 'text',
                                             'class' => 'form-control NumericWithZero',
                                             'field-name' => 'Store phone',
                                             'placeholder' => 'Enter Phone Name',
                                             'value' => $this->form_validation->set_value('store_phone', $form_data->store_phone));
        $this->data['store_logo']    = array('name' => 'store_logo',
                                             'id' => 'store_logo',
                                             'type' => 'file',
                                             'class' => 'form-control maxUploadSize',
                                             'placeholder' => '',
                                             'data-maxsize' => '1000000', // byte
                                             'value' => $this->form_validation->set_value('store_logo'));


        $this->data['content'] .= $this->load->view('admin/store/store-edit.php', $this->data, true);

        $this->render('admin');
    }

    public function delete()
    {
        $id = $this->uri->segment(4);

        if (empty($id)) {
            redirect(SITE_ADMIN . '/store');
        }

        $form_data = $this->categories_model->get_one('store', $id);

        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/store');
        }

        if (! empty($form_data->store_logo)) {
            $url = './' . $form_data->store_logo;

            if (file_exists($url)) {
                unlink($url);
            }
        }

        $result = $this->categories_model->delete('store', $id);
        if ($result) {
            $this->session->set_flashdata('message_success', 'Store successfully deleted');
        }
        else {
            $this->session->set_flashdata('message', 'Error. Failed to delete');
        }

        redirect(SITE_ADMIN . '/store', 'refresh');
    }

    public function remove_image()
    {
        $id = $this->input->post('id');
        if (! empty($id)) {
            $form_data = $this->categories_model->get_one('store', $id);

            if (empty($form_data)) {
                $status  = FALSE;
                $message = 'Failed remove image';
            }
            else {

                if (! empty($form_data->store_logo)) {
                    $url = './' . $form_data->store_logo;

                    if (file_exists($url)) {
                        unlink($url);
                    }
                }

                $array = array('store_logo' => '');

                $save    = $this->categories_model->save('store', $array, $id);
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

        $this->datatables->select('id, store_name, store_address, store_phone')->from('store')->add_column('actions', "<div class='btn-group'>
                                    <a href='" . base_url(SITE_ADMIN . '/store/edit/$1') . "'  class='btn btn-default'><i class='fa fa-pencil'></i> Edit</a>
                                    <a href='" . base_url(SITE_ADMIN . '/store/delete/$1') . "' class='btn btn-danger deleteNow' rel='Store'><i class='fa fa-trash-o'></i> Delete</a>
                                </div>", 'id');
        echo $this->datatables->generate();
    }
}