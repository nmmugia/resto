<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/17/2014
 * Time: 8:27 PM
 */
class System extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->data['color_lists'] = array(
            "white" => "Putih",
            "pink" => "Pink",
            "lightgreen" => "Hijau Muda"
        );
    }

    public function index()
    {
        redirect(SITE_ADMIN . '/system/server_sync');
    }
     
    public function server_sync()
    {
        $this->data['title'] = "Sinkronisasi Server";
        $this->data['subtitle'] = "Sinkronisasi Server";

        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['add_url'] = base_url(SITE_ADMIN . '/system/add_server');
        $this->data['data_url'] = base_url(SITE_ADMIN . '/system/get_server_data');
        //load content
        $this->data['use_username'] = TRUE;
        $this->data['content'] .= $this->load->view('admin/sync-list', $this->data, true);
        $this->render('admin');
    }

    public function tax()
    {
        // disable for client
        redirect(SITE_ADMIN);

        $this->data['title'] = "Pengaturan Pajak";
        $this->data['subtitle'] = "Pengaturan Pajak";

        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['add_url'] = base_url(SITE_ADMIN . '/system/add_tax');
        $this->data['data_url'] = base_url(SITE_ADMIN . '/system/get_tax_data');
        //load content
        $this->data['use_username'] = TRUE;
        $this->data['content'] .= $this->load->view('admin/tax-list', $this->data, true);
        $this->render('admin');
    }

    public function get_server_data()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $this->datatables->select('id,controller,url,start_time,end_time,interval')->from('server_sync')->add_column('actions', "<div class='btn-group'>
                                    <a href='" . base_url(SITE_ADMIN . '/system/edit_server/$1') . "'  class='btn btn-default'><i class='fa fa-pencil'></i> Edit</a>
                                    <a href='" . base_url(SITE_ADMIN . '/system/delete_server/$1') . "' class='btn btn-danger deleteNow' rel='Server'><i class='fa fa-trash-o'></i> Hapus</a>
                                    <button class='btn btn-primary' onclick=App.syncDatabaseToServer('$2/$1')  class='sync-to-server'> Sync To Server</button>
                                </div>", 'id,controller');
        echo $this->datatables->generate();
    }

    public function add_server()
    {
        $this->data['title'] = "Tambah Server";
        $this->data['subtitle'] = "Tambah Server";

        //validate form input
        $this->form_validation->set_rules('server_sync_url', 'URL', 'required|prep_url');
        $this->form_validation->set_rules('server_sync_controller', 'Controller', 'required');
        $this->form_validation->set_rules('server_start_time', 'Waktu mulai', 'required');
        $this->form_validation->set_rules('server_end_time', 'Waktu akhir', 'required');
        $this->form_validation->set_rules('server_interval', 'Interval', 'required|greater_than[0]|less_than[301]');

        if ($this->form_validation->run() == true) {

            $start_time = $this->input->post('server_start_time');
            $end_time = $this->input->post('server_end_time');
            $interval = $this->input->post('server_interval');
            $to_time = strtotime($start_time);
            $from_time = strtotime($end_time);
            $diff_minute = round(abs($to_time - $from_time) / 60, 2);
            if ($interval > $diff_minute) {

                $this->session->set_flashdata('message', 'Interval harus kurang dari selisih waktu');
            } else {
                $this->load->model('tax_model');
                $random_name = $this->generate_random_name(15);
                $name = 'Posresto_task_' . $random_name;

                $data_array = array('url' => $this->input->post('server_sync_url'),
                    'controller' => $this->input->post('server_sync_controller'),
                    'name' => $name,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'interval' => $interval);

                $save = $this->tax_model->save('server_sync', $data_array);

                if ($save === false) {
                    $this->session->set_flashdata('message', 'Failed save server');
                } else {
                    // add task scheduler
                    $this->load->library('MY_scheduler');
                    $program = $this->config->item('php_exe_path') . ' -f ' . FCPATH . 'index.php scheduler index ' . $save . '';
                    $start_date = '01/01/2015';
                    $end_date = '12/12/3015';

                    $result = $this->my_scheduler->create_task($name, $interval, $program, $start_time, $end_time, $start_date, $end_date);

                    if ($result) {
                        $this->session->set_flashdata('message_success', 'Berhasil menyimpan data');
                    } else {
                        $this->tax_model->delete('server_sync', $save);
                        $this->session->set_flashdata('message', 'Gagal menyimpan server. Cek waktu mulai, waktu akhir & interval');
                    }
                }
            }

            $btnaction = $this->input->post('btnAction');
            if ($btnaction == 'save_exit') {
                redirect(SITE_ADMIN . '/system/server_sync', 'refresh');
            } else {
                redirect(SITE_ADMIN . '/system/add_server/', 'refresh');
            }


        } else {
            //display the create user form
            //set the flash data error message if there is one
            $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            $this->data['message_success'] = $this->session->flashdata('message_success');

            $this->data['server_sync_url'] = array('name' => 'server_sync_url',
                'id' => 'server_sync_url',
                'type' => 'text',
                'class' => 'form-control requiredTextField',
                'field-name' => 'URL',
                'placeholder' => 'Masukan URL server',
                'value' => $this->form_validation->set_value('server_sync_url'));
            $this->data['server_sync_controller'] = array('name' => 'server_sync_controller',
                'id' => 'server_sync_controller',
                'type' => 'text',
                'class' => 'form-control requiredTextField',
                'field-name' => 'URL',
                'placeholder' => 'Masukan controller',
                'value' => $this->form_validation->set_value('server_sync_controller'));
            $this->data['server_start_time'] = array('name' => 'server_start_time',
                'id' => 'server_start_time',
                'type' => 'text',
                'class' => 'form-control requiredTextField time start',
                'field-name' => 'Waktu mulai',
                'placeholder' => 'Masukan waktu mulai',
                'value' => $this->form_validation->set_value('server_start_time'));
            $this->data['server_end_time'] = array('name' => 'server_end_time',
                'id' => 'server_end_time',
                'type' => 'text',
                'class' => 'form-control requiredTextField time end',
                'field-name' => 'Waktu akhir',
                'placeholder' => 'Masukan waktu akhir',
                'value' => $this->form_validation->set_value('server_end_time'));
            $this->data['server_interval'] = array('name' => 'server_interval',
                'id' => 'server_interval',
                'type' => 'text',
                'class' => 'form-control requiredTextField',
                'field-name' => 'Interval',
                'placeholder' => 'Interval',
                'value' => $this->form_validation->set_value('server_interval'));

            //load content
            $this->data['content'] .= $this->load->view('admin/sync-add', $this->data, true);
            $this->render('admin');
        }

    }

    public function edit_server()
    {
        $id = $this->uri->segment(4);

        if (empty($id)) {
            redirect(SITE_ADMIN . '/system/server_sync');
        }
        $this->load->model('tax_model');
        $form_data = $this->tax_model->get_one('server_sync', $id);

        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/system/server_sync');
        }

        $this->data['form_data'] = $form_data;
        $this->data['subtitle'] = "Edit Server";

        //validate form input
        $this->form_validation->set_rules('server_sync_url', 'URL', 'required|prep_url');
        $this->form_validation->set_rules('server_sync_controller', 'Controller', 'required');
        $this->form_validation->set_rules('server_start_time', 'Waktu mulai', 'required');
        $this->form_validation->set_rules('server_end_time', 'Waktu akhir', 'required');
        $this->form_validation->set_rules('server_interval', 'Interval', 'required|greater_than[0]|less_than[301]');

        if (isset($_POST) && !empty($_POST)) {

            if ($this->form_validation->run() === TRUE) {
                $start_time = $this->input->post('server_start_time');
                $end_time = $this->input->post('server_end_time');
                $interval = $this->input->post('server_interval');
                $to_time = strtotime($start_time);
                $from_time = strtotime($end_time);
                $diff_minute = round(abs($to_time - $from_time) / 60, 2);
                if ($interval > $diff_minute) {

                    $this->session->set_flashdata('message', 'Interval harus kurang dari perbedaan waktu');
                } else {
                    // add task scheduler
                    $this->load->library('MY_scheduler');
                    $name = $form_data->name;
                    $program = $this->config->item('php_exe_path') . ' -f ' . FCPATH . 'index.php scheduler index ' . $id . '';
                    $start_date = '01/01/2015';
                    $end_date = '12/12/3015';

                    // $start_date = '2015/01/01';
                    // $end_date   = '3015/12/12';

                    $result = $this->my_scheduler->modify_task($name, $interval, $program, $start_time, $end_time, $start_date, $end_date);

                    if ($result) {
                        $this->load->model('tax_model');

                        $data_array = array('url' => $this->input->post('server_sync_url'),
                            'controller' => $this->input->post('server_sync_controller'),
                            'start_time' => $start_time,
                            'end_time' => $end_time,
                            'interval' => $interval);

                        $save = $this->tax_model->save('server_sync', $data_array, $id);

                        if ($save === false) {
                            $this->session->set_flashdata('message', 'Gagal menyimpan data');
                        } else {
                            $this->session->set_flashdata('message_success', 'Berhasil menyimpan data');
                        }
                    } else {
                        $this->session->set_flashdata('message', 'Gagal menyimpan server. Cek waktu mulai, waktu akhir & interval');
                    }
                }

                $btnaction = $this->input->post('btnAction');
                if ($btnaction == 'save_exit') {
                    redirect(SITE_ADMIN . '/system/server_sync', 'refresh');
                } else {
                    redirect(SITE_ADMIN . '/system/edit_server/' . $id, 'refresh');
                }


            }
        }

        //display the edit user form
        $this->data['csrf'] = $this->_get_csrf_nonce();

        //set the flash data error message if there is one
        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');
        $this->data['cancel_url'] = base_url(SITE_ADMIN . '/system/server_sync');

        $this->data['server_sync_url'] = array('name' => 'server_sync_url',
            'id' => 'server_sync_url',
            'type' => 'text',
            'class' => 'form-control requiredTextField',
            'field-name' => 'URL',
            'placeholder' => 'Masukan URL server',
            'value' => $this->form_validation->set_value('server_sync_url', $form_data->url));
        $this->data['server_sync_controller'] = array('name' => 'server_sync_controller',
            'id' => 'server_sync_controller',
            'type' => 'text',
            'class' => 'form-control requiredTextField',
            'field-name' => 'Controller',
            'placeholder' => 'Masukan Controller',
            'value' => $this->form_validation->set_value('server_sync_controller', $form_data->controller));
        $this->data['server_start_time'] = array('name' => 'server_start_time',
            'id' => 'server_start_time',
            'type' => 'text',
            'class' => 'form-control requiredTextField time start',
            'field-name' => 'Waktu mulai',
            'placeholder' => 'Masukan waktu mulai',
            'value' => $this->form_validation->set_value('server_start_time', $form_data->start_time));
        $this->data['server_end_time'] = array('name' => 'server_end_time',
            'id' => 'server_end_time',
            'type' => 'text',
            'class' => 'form-control requiredTextField time end',
            'field-name' => 'Waktu akhir',
            'placeholder' => 'Masukan waktu akhir',
            'value' => $this->form_validation->set_value('server_end_time', $form_data->end_time));
        $this->data['server_interval'] = array('name' => 'server_interval',
            'id' => 'server_interval',
            'type' => 'text',
            'class' => 'form-control requiredTextField',
            'field-name' => 'Interval',
            'placeholder' => 'Interval',
            'value' => $this->form_validation->set_value('server_interval', $form_data->interval));
        $this->data['content'] .= $this->load->view('admin/sync-edit.php', $this->data, true);

        $this->render('admin');
    }

    public function delete_server()
    {
        $id = $this->uri->segment(4);

        if (empty($id)) {
            redirect(SITE_ADMIN . '/system/server_sync');
        }

        $this->load->model('tax_model');
        $form_data = $this->tax_model->get_one('server_sync', $id);

        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/system/server_sync');
        }

        $this->load->library('MY_scheduler');

        $result = $this->my_scheduler->delete_task($form_data->name);
        if ($result) {
            $result = $this->tax_model->delete('server_sync', $id);
            if ($result) {
                $this->session->set_flashdata('message_success', 'Berhasil menghapus data');
            } else {
                $this->session->set_flashdata('message', 'Error(1). Gagal menghapus data');
            }
        } else {
            $this->session->set_flashdata('message', 'Error(2). Gagal menghapus data scheduler');
            $result = $this->tax_model->delete('server_sync', $id);
            if ($result) {
                $this->session->set_flashdata('message_success', 'Berhasil menghapus data  DB');
            } else {
                $this->session->set_flashdata('message', 'Error(1). Gagal menghapus data DB');
            }
        }

        redirect(SITE_ADMIN . '/system/server_sync', 'refresh');
    }


    public function get_tax_data()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $this->datatables->select('id,tax_name,tax_percentage')->from('taxes')->add_column('actions', "<div class='btn-group'>
                                    <a href='" . base_url(SITE_ADMIN . '/system/edit_tax/$1') . "'  class='btn btn-default'><i class='fa fa-pencil'></i> Edit</a>
                                    <a href='" . base_url(SITE_ADMIN . '/system/delete_tax/$1') . "' class='btn btn-danger deleteNow' rel='Tax'><i class='fa fa-trash-o'></i> Hapus</a>
                                </div>", 'id');
        echo $this->datatables->generate();
    }

    public function add_tax()
    {
        // disable for client
        redirect(SITE_ADMIN);

        $this->data['title'] = "Tambah Tax";
        $this->data['subtitle'] = "Tambah Tax";

        //validate form input
        $this->form_validation->set_rules('tax_name', 'Tax Name', 'required|xss_clean|min_length[1]|max_length[50]');
        $this->form_validation->set_rules('tax_percentage', 'Tax Percentage', 'required|xss_clean|numeric');

        if ($this->form_validation->run() == true) {

            $this->load->model('tax_model');
            $data_array = array('tax_name' => $this->input->post('tax_name'),
                'tax_percentage' => $this->input->post('tax_percentage'));

            $save = $this->tax_model->save('taxes', $data_array);

            if ($save === false) {
                $this->session->set_flashdata('message', 'Gagal menyimpan data');
            } else {
                $this->session->set_flashdata('message_success', 'Berhasil menyimpan data');
            }
            $btnaction = $this->input->post('btnAction');
            if ($btnaction == 'save_exit') {
                redirect(SITE_ADMIN . '/system/tax', 'refresh');
            } else {
                redirect(SITE_ADMIN . '/system/add_tax/', 'refresh');
            }


        } else {
            //display the create user form
            //set the flash data error message if there is one
            $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            $this->data['message_success'] = $this->session->flashdata('message_success');

            $this->data['tax_name'] = array('name' => 'tax_name',
                'id' => 'tax_name',
                'type' => 'text',
                'class' => 'form-control requiredTextField',
                'field-name' => 'Tax name',
                'placeholder' => 'Masukan Tax Name',
                'value' => $this->form_validation->set_value('tax_name'));
            $this->data['tax_percentage'] = array('name' => 'tax_percentage',
                'id' => 'tax_percentage',
                'type' => 'text',
                'class' => 'form-control requiredTextField',
                'field-name' => 'Tax Percentage',
                'placeholder' => 'Masukan Tax Percentage',
                'value' => $this->form_validation->set_value('tax_percentage'));

            //load content
            $this->data['content'] .= $this->load->view('admin/tax-add', $this->data, true);
            $this->render('admin');
        }

    }

    public function edit_tax()
    {
        // disable for client
        redirect(SITE_ADMIN);

        $id = $this->uri->segment(4);

        if (empty($id)) {
            redirect(SITE_ADMIN . '/system/tax');
        }
        $this->load->model('tax_model');
        $form_data = $this->tax_model->get_one('taxes', $id);

        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/system/tax');
        }

        $this->data['form_data'] = $form_data;
        $this->data['subtitle'] = "Edit Tax";

        //validate form input
        $this->form_validation->set_rules('tax_name', 'Tax Name', 'required|xss_clean|min_length[1]|max_length[50]');
        $this->form_validation->set_rules('tax_percentage', 'Tax Percentage', 'required|xss_clean|numeric');

        if (isset($_POST) && !empty($_POST)) {

            if ($this->form_validation->run() === TRUE) {
                $data_array = array('tax_name' => $this->input->post('tax_name'),
                    'tax_percentage' => $this->input->post('tax_percentage'));

                $save = $this->tax_model->save('taxes', $data_array, $id);

                if ($save === false) {
                    $this->session->set_flashdata('message', 'Gagal menyimpan data');
                } else {
                    $this->session->set_flashdata('message_success', 'Berhasil menyimpan data');
                }
                $btnaction = $this->input->post('btnAction');
                if ($btnaction == 'save_exit') {
                    redirect(SITE_ADMIN . '/system/tax', 'refresh');
                } else {
                    redirect(SITE_ADMIN . '/system/tax/edit_tax/' . $id, 'refresh');
                }


            }
        }

        //display the edit user form
        $this->data['csrf'] = $this->_get_csrf_nonce();

        //set the flash data error message if there is one
        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');
        $this->data['cancel_url'] = base_url(SITE_ADMIN . '/system/tax');

        $this->data['tax_name'] = array('name' => 'tax_name',
            'id' => 'tax_name',
            'type' => 'text',
            'class' => 'form-control requiredTextField',
            'field-name' => 'Tax name',
            'placeholder' => 'Masukan Tax Name',
            'value' => $this->form_validation->set_value('tax_name', $form_data->tax_name));
        $this->data['tax_percentage'] = array('name' => 'tax_percentage',
            'id' => 'tax_percentage',
            'type' => 'text',
            'class' => 'form-control requiredTextField',
            'field-name' => 'Tax Percentage',
            'placeholder' => 'Masukan Tax Percentage',
            'value' => $this->form_validation->set_value('tax_percentage', $form_data->tax_percentage));
        $this->data['content'] .= $this->load->view('admin/tax-edit.php', $this->data, true);

        $this->render('admin');
    }

    public function delete_tax()
    {
        $id = $this->uri->segment(4);

        if (empty($id)) {
            redirect(SITE_ADMIN . '/system/tax');
        }

        $this->load->model('tax_model');
        $form_data = $this->tax_model->get_one('taxes', $id);

        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/system/tax');
        }

        $result = $this->tax_model->delete('taxes', $id);
        if ($result) {
            $this->session->set_flashdata('message_success', 'Berhasil menghapus data');
        } else {
            $this->session->set_flashdata('message', 'Error. Gagal menghapus data');
        }

        redirect(SITE_ADMIN . '/system/tax', 'refresh');
    }

    public function additional_charges()
    {
        // alta - disable extra charge, redirect to tax
        redirect(SITE_ADMIN . '/system/tax');

        $this->data['title'] = "Biaya Tambahan";
        $this->data['subtitle'] = "Biaya Tambahan";

        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['add_url'] = base_url(SITE_ADMIN . '/system/add_additional_charges');
        $this->data['data_url'] = base_url(SITE_ADMIN . '/system/get_additional_charges_data');;
        //load content
        $this->data['content'] .= $this->load->view('admin/additional-charges-list', $this->data, true);
        $this->render('admin');
    }

    public function get_additional_charges_data()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $this->datatables->select('id,charge_name,charge_value')->from('extra_charge')->add_column('actions', "<div class='btn-group'>
                                    <a href='" . base_url(SITE_ADMIN . '/system/edit_additional_charges/$1') . "'  class='btn btn-default'><i class='fa fa-pencil'></i> Edit</a>
                                    <a href='" . base_url(SITE_ADMIN . '/system/delete_additional_charges/$1') . "' class='btn btn-danger deleteNow' rel='Biaya Tambahan'><i class='fa fa-trash-o'></i> Hapus</a>
                                </div>", 'id');
        echo $this->datatables->generate();
    }

    public function add_additional_charges()
    {
        // alta - disable extra charge, redirect to tax
        redirect(SITE_ADMIN . '/system/tax');

        $this->data['title'] = "Tambah Biaya Tambahan";
        $this->data['subtitle'] = "Tambah Biaya Tambahan";

        //validate form input
        $this->form_validation->set_rules('charge_name', 'Nama biaya', 'required|xss_clean|min_length[1]|max_length[50]');
        $this->form_validation->set_rules('charge_value', 'Value', 'required|xss_clean|numeric');

        if ($this->form_validation->run() == true) {

            $this->load->model('tax_model');
            $data_array = array('charge_name' => $this->input->post('charge_name'),
                'charge_value' => $this->input->post('charge_value'));

            $save = $this->tax_model->save('extra_charge', $data_array);

            if ($save === false) {
                $this->session->set_flashdata('message', 'Gagal menyimpan data');
            } else {
                $this->session->set_flashdata('message_success', 'Berhasil menyimpan data');
            }
            $btnaction = $this->input->post('btnAction');
            if ($btnaction == 'save_exit') {
                redirect(SITE_ADMIN . '/system/additional_charges', 'refresh');
            } else {
                redirect(SITE_ADMIN . '/system/add_additional_charges/', 'refresh');
            }


        } else {
            //display the create user form
            //set the flash data error message if there is one
            $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            $this->data['message_success'] = $this->session->flashdata('message_success');

            $this->data['charge_name'] = array('name' => 'charge_name',
                'id' => 'charge_name',
                'type' => 'text',
                'class' => 'form-control requiredTextField',
                'field-name' => 'Charge name',
                'placeholder' => 'Masukan nama biaya',
                'value' => $this->form_validation->set_value('charge_name'));
            $this->data['charge_value'] = array('name' => 'charge_value',
                'id' => 'charge_value',
                'type' => 'text',
                'class' => 'form-control requiredTextField',
                'field-name' => 'Charge value',
                'placeholder' => 'Masukan value',
                'value' => $this->form_validation->set_value('charge_value'));

            //load content
            $this->data['content'] .= $this->load->view('admin/additional-charges-add', $this->data, true);
            $this->render('admin');
        }

    }

    public function edit_additional_charges()
    {
        // alta - disable extra charge, redirect to tax
        redirect(SITE_ADMIN . '/system/tax');

        $id = $this->uri->segment(4);

        if (empty($id)) {
            redirect(SITE_ADMIN . '/system/additional_charges');
        }
        $this->load->model('tax_model');
        $form_data = $this->tax_model->get_one('extra_charge', $id);

        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/system/additional_charges');
        }

        $this->data['form_data'] = $form_data;
        $this->data['subtitle'] = "Edit Biaya Tambahan";

        //validate form input
        $this->form_validation->set_rules('charge_name', 'Nama biaya', 'required|xss_clean|min_length[1]|max_length[50]');
        $this->form_validation->set_rules('charge_value', 'Value', 'required|xss_clean|numeric');


        if (isset($_POST) && !empty($_POST)) {

            if ($this->form_validation->run() === TRUE) {
                $data_array = array('charge_name' => $this->input->post('charge_name'),
                    'charge_value' => $this->input->post('charge_value'));

                $save = $this->tax_model->save('extra_charge', $data_array, $id);

                if ($save === false) {
                    $this->session->set_flashdata('message', 'Gagal menyimpan data');
                } else {
                    $this->session->set_flashdata('message_success', 'Berhasil menyimpan data');
                }
                $btnaction = $this->input->post('btnAction');
                if ($btnaction == 'save_exit') {
                    redirect(SITE_ADMIN . '/system/additional_charges', 'refresh');
                } else {
                    redirect(SITE_ADMIN . '/system/edit_additional_charges/' . $id, 'refresh');
                }


            }
        }

        //display the edit user form
        $this->data['csrf'] = $this->_get_csrf_nonce();

        //set the flash data error message if there is one
        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');
        $this->data['cancel_url'] = base_url(SITE_ADMIN . '/system/additional_charges');

        $this->data['charge_name'] = array('name' => 'charge_name',
            'id' => 'charge_name',
            'type' => 'text',
            'class' => 'form-control requiredTextField',
            'field-name' => 'Charge name',
            'placeholder' => 'Masukan nama biaya',
            'value' => $this->form_validation->set_value('charge_name', $form_data->charge_name));
        $this->data['charge_value'] = array('name' => 'charge_value',
            'id' => 'charge_value',
            'type' => 'text',
            'class' => 'form-control requiredTextField',
            'field-name' => 'Value',
            'placeholder' => 'Masukan value',
            'value' => $this->form_validation->set_value('charge_value', $form_data->charge_value));
        $this->data['content'] .= $this->load->view('admin/additional-charges-edit.php', $this->data, true);

        $this->render('admin');
    }

    public function delete_additional_charges()
    {
        // alta - disable extra charge, redirect to tax
        redirect(SITE_ADMIN . '/system/tax');

        $id = $this->uri->segment(4);

        if (empty($id)) {
            redirect(SITE_ADMIN . '/system/additional_charges');
        }

        $this->load->model('tax_model');
        $form_data = $this->tax_model->get_one('extra_charge', $id);

        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/system/additional_charges');
        }

        $result = $this->tax_model->delete('extra_charge', $id);
        if ($result) {
            $this->session->set_flashdata('message_success', 'Berhasil menghapus data');
        } else {
            $this->session->set_flashdata('message', 'Error. Gagal menghapus data');
        }

        redirect(SITE_ADMIN . '/system/additional_charges', 'refresh');
    }

    public function remove_printer_logo()
    {
        $this->load->model('categories_model');
        if (!empty($this->data['setting']['printer_logo'])) {
            $printer_logo = $this->data['setting']['printer_logo'];
            if (empty($printer_logo)) {
                $status = FALSE;
                $message = 'Gagal menghapus gambar';
            } else {
                if (!empty($printer_logo)) {
                    $url = './' . $printer_logo;
                    if (file_exists($url)) {
                        unlink($url);
                    }
                }
                $this->categories_model->save_by('master_general_setting', array('value' => ''), "printer_logo", 'name');
                $status = TRUE;
                $message = 'Berhasil menghapus gambar';
            }
        } else {
            $status = FALSE;
            $message = 'Gagal menghapus gambar';
        }
        echo json_encode(array('status' => $status, 'message' => $message));
    }

    public function setting()
    {
        $this->load->model('categories_model');
        $this->load->model("member_category_model");
        $this->load->model("store_model");
        $this->load->model("order_model");

        $setting = $this->categories_model->get('master_general_setting')->result();

        $form_data = array();
        foreach ($setting as $name => $row) {
            $form_data[$row->name] = $row->value;
            $form_data["default"] = $row->default;
        }
        $this->data['form_data'] = $form_data;
        $this->data['subtitle'] = "Pengaturan";

        //validate form input
        $this->form_validation->set_rules('default_table_width', 'Nama biaya', 'required|numeric');
        $this->form_validation->set_rules('default_table_height', 'Value', 'required|numeric');
        // $this->form_validation->set_rules('store_id', 'ID Toko', 'required');
        $this->form_validation->set_rules('server_base_url', 'Value', 'required');
        $this->form_validation->set_rules('booking_remove_lock', 'waktu selesai lock reservasi', 'required|numeric');
        $this->form_validation->set_rules('booking_start_lock', 'waktu lock reservasi', 'required|numeric');
        // $this->form_validation->set_rules('void_manager_confirmation', 'konfirmasi void', 'required');
        $this->form_validation->set_rules('courier_service', 'jasa kurir', 'required|greater_than[-1]|less_than[101]');
        $this->form_validation->set_rules('revenue_sharing', 'Besaran Revenue Sharing', 'required|greater_than[-1]|less_than[101]');

        if (isset($_POST) && !empty($_POST)) {

            if ($this->form_validation->run() === TRUE) {
                $image_name = $form_data['printer_logo'];
                $isUpload = TRUE;
                if (!empty($_FILES['printer_logo']['name'])) {
                    //upload config
                    $newname = $this->generate_random_name();
                    $config['upload_path'] = './uploads/printer_logo/';
                    $config['allowed_types'] = '*';
                    $config['max_size'] = '1000';
                    $config['overwrite'] = FALSE;
                    $config['file_name'] = $newname;
                    $this->load->library('upload', $config);

                    if (!$this->upload->do_upload('printer_logo')) {
                        $this->session->set_flashdata('message', $this->upload->display_errors());
                        $isUpload = FALSE;
                    } else {
                        $this->load->library('image_moo');
                        $this->image_moo->load($this->upload->data()['full_path'])->set_background_colour("#FFFFFF")->stretch(150, 81);
                        $image_name = 'uploads/printer_logo/' . $this->upload->data()['file_name'];
                        $isUpload = TRUE;
                        if (!empty($form_data['printer_logo'])) {
                            $url = './' . $form_data['printer_logo'];
                            if (file_exists($url)) {
                                unlink($url);
                            }
                        }
                        $data_array = array('value' => $image_name);
                        $this->categories_model->save_by('master_general_setting', $data_array, "printer_logo", 'name');
                    }
                }
                
                foreach ($this->input->post() as $name => $row) {
                    if (!is_array($row)) {
                        $value = $row;
                        $data_array = array('value' => $value);
                    } else {
                        $tmp = array();
                        $default = 0;
                    
                        if ($name == 'printer_checker') {
                            $count=0;
                            foreach ($row as $key2 => $row2) {
                                $tmp[$count] = $row2['printer_name'];
                                $count++;
                            }
                            if ($this->input->post('is_checker_use_logo')) {
                                $default = 1;
                            }
                            $description = $this->input->post('checker_printer_width');
                            $value = json_encode($tmp);
                        } else if ($name == 'printer_checker_kitchen') {
                            foreach ($row as $key2 => $row2) {
                                $tmp[$row2['outlet_checker_kitchen_id']] = $row2['printer_name'];
                            }
                            if ($this->input->post('is_checker_kitchen_use_logo')) {
                                $default = 1;
                            }
                            $description = $this->input->post('checker_kitchen_printer_width');
                            $value = json_encode($tmp);
                        } else {
                            $tmp = array();
                            foreach ($row as $key2 => $row2) {
                                $tmp[$row2['outlet_id']] = $row2['printer_name'];

                            }
                            $value = json_encode($tmp);
                            if ($this->input->post('is_kitchen_use_logo')) {
                                $default = 1;
                            }
                            $description = $this->input->post('kitchen_printer_width');
                        }
                        $data_array = array('value' => $value, 'default' => $default, 'description' => $description);
                    }

                    if ($name == 'printer_cashier') {
                        if ($this->input->post('is_cashier_use_logo')) {
                            $default = 1;
                        } else {
                            $default = 0;
                        }
                        $description = $this->input->post('cashier_printer_width');
                        $data_array = array('value' => $value, 'default' => $default, 'description' => $description);
                    }


                    $save = $this->categories_model->save_by('master_general_setting', $data_array, $name, 'name');
                    if ($name == "use_kitchen") {
                        if ($row == 0) {
                            $this->order_model->update_order_menu_suspend_kitchen();
                            $this->order_model->update_order_menu_suspend_checker();
                        }
                    }
                    if ($name == "use_role_checker") {
                        if ($row == 0) {
                            $this->order_model->update_order_menu_suspend_checker();
                        }
                    }
                    if ($name == "is_checker_auto") {
                        $data_array = array('value' => $this->input->post('is_checker_auto'));
                        $save = $this->categories_model->save_by('master_general_setting', $data_array, 'auto_checker', 'name');
                    }
                    if ($name == "is_checker_kitchen_auto") {
                        $data_array = array('value' => $this->input->post('is_checker_kitchen_auto'));
                        $save = $this->categories_model->save_by('master_general_setting', $data_array, 'auto_checker_kitchen', 'name');
                    }
                    
                }

                if (!$this->input->post('printer_checker')) {
                    $data_array = array('value' => "");
                    $save = $this->categories_model->save_by('master_general_setting', $data_array, "printer_checker", 'name');

                }
                if (!$this->input->post('printer_kitchen')) {
                    $data_array = array('value' => "");
                    $save = $this->categories_model->save_by('master_general_setting', $data_array, "printer_kitchen", 'name');

                }
                if (!$this->input->post('printer_checker_kitchen')) {
                    $data_array = array('value' => "");
                    $save = $this->categories_model->save_by('master_general_setting', $data_array, "printer_checker_kitchen", 'name');

                }


                if ($save === false) {
                    $this->session->set_flashdata('message', 'Gagal menyimpan data');
                } else {
                    $this->session->set_flashdata('message_success', 'Berhasil menyimpan data');
                }

                redirect(SITE_ADMIN . '/system/setting', 'refresh');


            }
        }

        //display the edit user form
        $this->data['csrf'] = $this->_get_csrf_nonce();

        //set the flash data error message if there is one
        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');
        $this->data['cancel_url'] = base_url(SITE_ADMIN . '/system/setting');

        $this->data['all_outlet'] = $this->categories_model->get_outlet($this->data['setting']['store_id']);
        $data_printer_kitchen = json_decode($form_data['printer_kitchen']);
        $this->data['printer_kitchen'] = array();
        if (!empty($data_printer_kitchen)) {
            foreach ($data_printer_kitchen as $key => $row) {
                $printer_obj = new stdClass();
                $printer_obj->outlet_id = $key;
                $printer_obj->printer_name = $row;
                $this->data['printer_kitchen'][] = $printer_obj;
            }
        }

        $this->data['printer_checker'] = array();
        if (!empty($form_data['printer_checker'])) {
            $data_printer_checker = json_decode($form_data['printer_checker']);
            if($data_printer_checker){
                foreach ($data_printer_checker as $key => $row) {
                    $printer_obj = new stdClass();
                    // $printer_obj->outlet_id = $key;
                    $printer_obj->printer_name = $row;
                    $this->data['printer_checker'][] = $printer_obj;
                }    
            }
            
        }

        $this->data['printer_checker_kitchen'] = array();
        if (!empty($form_data['printer_checker_kitchen'])) {
            $printer_checker_kitchen = json_decode($form_data['printer_checker_kitchen']);
            if($printer_checker_kitchen){
                foreach ($printer_checker_kitchen as $key => $row) {
                    $printer_obj = new stdClass();
                    $printer_obj->outlet_id = $key;
                    $printer_obj->printer_name = $row;
                    $this->data['printer_checker_kitchen'][] = $printer_obj;
                }
            }
        }


        $this->data['printer_checker_kitchen_count'] = array('id' => 'printer_checker_kitchen_count',
            'name' => 'printer_checker_kitchen_count',
            'type' => 'hidden',
            'value' => count($this->data['printer_checker_kitchen']));


        $this->data['printer_checker_count'] = array('id' => 'printer_checker_count',
            'name' => 'printer_checker_count',
            'type' => 'hidden',
            'value' => count($this->data['printer_checker']));


        $this->data['printer_kitchen_count'] = array('id' => 'printer_kitchen_count',
            'name' => 'printer_kitchen_count',
            'type' => 'hidden',
            'value' => count($this->data['printer_kitchen']));


        $this->data['default_table_width'] = array('name' => 'default_table_width',
            'id' => 'default_table_width',
            'type' => 'text',
            'class' => 'form-control requiredTextField NumericOnly',
            'field-name' => 'Lebar Penempatan Meja',
            'placeholder' => '',
            'value' => $this->form_validation->set_value('default_table_width', $form_data['default_table_width']));
        $this->data['default_table_height'] = array('name' => 'default_table_height',
            'id' => 'default_table_height',
            'type' => 'text',
            'class' => 'form-control requiredTextField NumericOnly',
            'field-name' => 'Tinggi Penempatan Meja',
            'placeholder' => '',
            'value' => $this->form_validation->set_value('default_table_height', $form_data['default_table_height']));


        $this->data['zero_stock_order'] = $form_data['zero_stock_order'];

        $this->data['quantity_by_menu'] = array('name' => 'stock_menu_by_inventory',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Set Stock Menu',
            'placeholder' => '',
            'checked' => ($form_data['stock_menu_by_inventory'] == 0) ? 'true' : '',
            'value' => "0");
        $this->data['quantity_by_inventory'] = array('name' => 'stock_menu_by_inventory',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Set Stock Menu',
            'placeholder' => '',
            'checked' => ($form_data['stock_menu_by_inventory'] == 1) ? 'true' : '',
            'value' => "1");

        $this->data['server_base_url'] = array('name' => 'server_base_url',
            'id' => 'server_base_url',
            'type' => 'text',
            'class' => 'form-control requiredTextField',
            'field-name' => 'Alamat Server',
            'placeholder' => '',
            'value' => $this->form_validation->set_value('server_base_url', $form_data['server_base_url']));
        $this->data['private_key_resto'] = array('name' => 'private_key',
            'id' => 'private_key_resto',
            'type' => 'text',
            'class' => 'form-control requiredTextField',
            'field-name' => 'Private Key Resto',
            'placeholder' => '',
            'value' => $this->form_validation->set_value('private_key_resto', $form_data['private_key']));
        $this->data['site_title'] = array('name' => 'site_title',
            'id' => 'site_title',
            'type' => 'text',
            'class' => 'form-control requiredTextField',
            'field-name' => 'Judul Site',
            'placeholder' => '',
            'value' => $this->form_validation->set_value('site_title', $form_data['site_title']));
        $this->data['site_title_delimiter'] = array('name' => 'site_title_delimiter',
            'id' => 'site_title_delimiter',
            'type' => 'text',
            'class' => 'form-control requiredTextField',
            'field-name' => 'Pemisah Judul',
            'placeholder' => '',
            'value' => $this->form_validation->set_value('site_title_delimiter', $form_data['site_title_delimiter']));

        $this->data['notification_yes'] = array('name' => 'notification',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Notifikasi',
            'placeholder' => '',
            'checked' => ($form_data['notification'] == 1) ? 'true' : '',
            'value' => "1");
        $this->data['notification_no'] = array('name' => 'notification',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Notifikasi',
            'placeholder' => '',
            'checked' => ($form_data['notification'] == 0) ? 'true' : '',
            'value' => "0");

        $this->data['notification_kontra_bon_yes'] = array('name' => 'notification_kontra_bon',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Notifikasi Kontra Bon',
            'placeholder' => '',
            'checked' => ($form_data['notification_kontra_bon'] == 1) ? 'true' : '',
            'value' => "1");
        $this->data['notification_kontra_bon_no'] = array('name' => 'notification_kontra_bon',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Notifikasi Kontra Bon',
            'placeholder' => '',
            'checked' => ($form_data['notification_kontra_bon'] == 0) ? 'true' : '',
            'value' => "0");

        $this->data['font_size_bill_1'] = array('name' => 'font_size_bill',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Font Size Bill',
            'placeholder' => '',
            'checked' => ($form_data['font_size_bill'] == 1) ? 'true' : '',
            'value' => "1");

        $this->data['font_size_bill_2'] = array('name' => 'font_size_bill',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Font Size Bill',
            'placeholder' => '',
            'checked' => ($form_data['font_size_bill'] == 2) ? 'true' : '',
            'value' => "2");

        $this->data['dining_type_1'] = array('name' => 'dining_type',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Tipe Dinein',
            'placeholder' => '',
            'checked' => ($form_data['dining_type'] == 1) ? 'true' : '',
            'value' => "1");
        $this->data['dining_type_2'] = array('name' => 'dining_type',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Tipe Dinein',
            'placeholder' => '',
            'checked' => ($form_data['dining_type'] == 2) ? 'true' : '',
            'value' => "2");
        $this->data['dining_type_3'] = array('name' => 'dining_type',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Tipe Dinein',
            'placeholder' => '',
            'checked' => ($form_data['dining_type'] == 3) ? 'true' : '',
            'value' => "3");

        $this->data['target_print_list_menu_cashier'] = array('name' => 'target_print_list_menu',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Target printer list menu pada kasir',
            'placeholder' => '',
            'checked' => ($form_data['target_print_list_menu'] == 1) ? 'true' : '',
            'value' => "1");

        $this->data['target_print_list_menu_checker'] = array('name' => 'target_print_list_menu',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Target printer list menu pada kasir',
            'placeholder' => '',
            'checked' => ($form_data['target_print_list_menu'] == 2) ? 'true' : '',
            'value' => "2");

        $this->data['bill_auto_number_sequence'] = array('name' => 'bill_auto_number',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Format nomor bill',
            'placeholder' => '',
            'checked' => ($form_data['bill_auto_number'] == 1) ? 'true' : '',
            'value' => "1");

        $this->data['bill_auto_number_partial_random'] = array('name' => 'bill_auto_number',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Format nomor bill',
            'placeholder' => '',
            'checked' => ($form_data['bill_auto_number'] == 2) ? 'true' : '',
            'value' => "2");

        $this->data['bill_auto_number_random'] = array('name' => 'bill_auto_number',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Format nomor bill',
            'placeholder' => '',
            'checked' => ($form_data['bill_auto_number'] == 3) ? 'true' : '',
            'value' => "3");

        $this->data['tax_service_method_1'] = array('name' => 'tax_service_method',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Metode perhitungan tax',
            'placeholder' => '',
            'checked' => ($form_data['tax_service_method'] == 1) ? 'true' : '',
            'value' => "1");
        $this->data['tax_service_method_2'] = array('name' => 'tax_service_method',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Metode perhitungan tax',
            'placeholder' => '',
            'checked' => ($form_data['tax_service_method'] == 2) ? 'true' : '',
            'value' => "2");

        $this->data['stock_method_fifo'] = array('name' => 'stock_method',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'metode stok',
            'placeholder' => '',
            'checked' => ($form_data['stock_method'] == "FIFO") ? 'true' : '',
            'value' => "FIFO");
        $this->data['stock_method_average'] = array('name' => 'stock_method',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'metode stok',
            'placeholder' => '',
            'checked' => ($form_data['stock_method'] == "AVERAGE") ? 'true' : '',
            'value' => "AVERAGE");

        $this->data['opname_method_different'] = array('name' => 'opname_method',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'metode opname',
            'placeholder' => '',
            'checked' => ($form_data['opname_method'] == "DIFF") ? 'true' : '',
            'value' => "DIFF");
        $this->data['opname_method_existing'] = array('name' => 'opname_method',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'metode opname',
            'placeholder' => '',
            'checked' => ($form_data['opname_method'] == "EXIST") ? 'true' : '',
            'value' => "EXIST");

        $this->data['cogs_count_menu'] = array('name' => 'priority_cogs_count',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'metode perhitungan hpp',
            'placeholder' => '',
            'checked' => ($form_data['priority_cogs_count'] == 0) ? 'true' : '',
            'value' => "0");
        $this->data['cogs_count_inventory'] = array('name' => 'priority_cogs_count',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'metode perhitungan hpp',
            'placeholder' => '',
            'checked' => ($form_data['priority_cogs_count'] == 1) ? 'true' : '',
            'value' => "1");
        $this->data['use_primary_additional_color_yes'] = array('name' => 'use_primary_additional_color',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Warna background order',
            'placeholder' => '',
            'checked' => ($form_data['use_primary_additional_color'] == 1) ? 'true' : '',
            'value' => "1");

        $this->data['use_primary_additional_color_no'] = array('name' => 'use_primary_additional_color',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Warna background order',
            'placeholder' => '',
            'checked' => ($form_data['use_primary_additional_color'] == 0) ? 'true' : '',
            'value' => "0");
        $this->data['printer_cashier'] = array('name' => 'printer_cashier',
            'id' => 'printer_cashier',
            'type' => 'text',
            'class' => 'form-control',
            'field-name' => 'Printer Kasir',
            'placeholder' => '',
            'value' => $this->form_validation->set_value('printer_cashier', $form_data['printer_cashier']));
        $this->data['printer_warehouse'] = array('name' => 'printer_warehouse',
            'id' => 'printer_warehouse',
            'type' => 'text',
            'class' => 'form-control',
            'field-name' => 'Printer Gudang',
            'placeholder' => '',
            'value' => $this->form_validation->set_value('printer_warehouse', $form_data['printer_warehouse']));
        $this->data['printer_hrd'] = array('name' => 'printer_hrd',
            'id' => 'printer_hrd',
            'type' => 'text',
            'class' => 'form-control',
            'field-name' => 'Printer HRD Dot Matrix',
            'placeholder' => '',
            'value' => $this->form_validation->set_value('printer_hrd', $form_data['printer_hrd']));
        $this->data['printer_format_1'] = array('name' => 'printer_format',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Format Bill',
            'placeholder' => '',
            'checked' => ($form_data['printer_format'] == 1) ? 'true' : '',
            'value' => "1");

        $this->data['printer_format_2'] = array('name' => 'printer_format',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Format Bill',
            'placeholder' => '',
            'checked' => ($form_data['printer_format'] == 2) ? 'true' : '',
            'value' => "2");
        $this->data['voucher_method_1'] = array('name' => 'voucher_method',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Metode Input Voucher POS',
            'placeholder' => '',
            'checked' => ($form_data['voucher_method'] == 1) ? 'true' : '',
            'value' => "1");

        $this->data['voucher_method_2'] = array('name' => 'voucher_method',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Metode Input Voucher POS',
            'placeholder' => '',
            'checked' => ($form_data['voucher_method'] == 2) ? 'true' : '',
            'value' => "2");



        $this->data['open_close_format_1'] = array('name' => 'open_close_format',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Format Tutup Kasir',
            'placeholder' => '',
            'checked' => ($form_data['open_close_format'] == 1) ? 'true' : '',
            'value' => "1");
        $this->data['open_close_format_2'] = array('name' => 'open_close_format',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Format Tutup Kasir',
            'placeholder' => '',
            'checked' => ($form_data['open_close_format'] == 2) ? 'true' : '',
            'value' => "2");
        $this->data['open_close_format_3'] = array('name' => 'open_close_format',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Format Tutup Kasir',
            'placeholder' => '',
            'checked' => ($form_data['open_close_format'] == 3) ? 'true' : '',
            'value' => "3");
        $this->data['open_close_format_4'] = array('name' => 'open_close_format',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Format Tutup Kasir',
            'placeholder' => '',
            'checked' => ($form_data['open_close_format'] == 4) ? 'true' : '',
            'value' => "4");



        $this->data['open_close_metode_1'] = array('name' => 'open_close_system',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Metode Tutup Kasir',
            'placeholder' => '',
            'checked' => ($form_data['open_close_system'] == 1) ? 'true' : '',
            'value' => "1");
        $this->data['open_close_metode_2'] = array('name' => 'open_close_system',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Metode Tutup Kasir',
            'placeholder' => '',
            'checked' => ($form_data['open_close_system'] == 2) ? 'true' : '',
            'value' => "2");

        $this->data['coh_yes'] = array('name' => 'cash_on_hand',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Input Cash On Hand',
            'placeholder' => '',
            'checked' => ($form_data['cash_on_hand'] == 1) ? 'true' : '',
            'value' => "1");
        $this->data['coh_no'] = array('name' => 'cash_on_hand',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Input Cash On Hand',
            'placeholder' => '',
            'checked' => ($form_data['cash_on_hand'] == 0) ? 'true' : '',
            'value' => "0");

        $this->data['cash_admin_yes'] = array('name' => 'input_cash_admin',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Input Pengeluaran Admin',
            'placeholder' => '',
            'checked' => ($form_data['input_cash_admin'] == 1) ? 'true' : '',
            'value' => "1");
        $this->data['cash_admin_no'] = array('name' => 'input_cash_admin',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Input Pengeluaran Admin',
            'placeholder' => '',
            'checked' => ($form_data['input_cash_admin'] == 0) ? 'true' : '',
            'value' => "0");

        $this->data['checker_group_yes'] = array('name' => 'checker_group',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Memakai Grup Checker',
            'placeholder' => '',
            'checked' => ($form_data['checker_group'] == 1) ? 'true' : '',
            'value' => "1");
        $this->data['checker_group_no'] = array('name' => 'checker_group',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Memakai Grup Checker',
            'placeholder' => '',
            'checked' => ($form_data['checker_group'] == 0) ? 'true' : '',
            'value' => "0");

        $this->data['print_number_yes'] = array('name' => 'print_number',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Print Nomor',
            'placeholder' => '',
            'checked' => ($form_data['print_number'] == 1) ? 'true' : '',
            'value' => "1");

        $this->data['print_number_no'] = array('name' => 'print_number',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Print Nomor',
            'placeholder' => '',
            'checked' => ($form_data['print_number'] == 0) ? 'true' : '',
            'value' => "0");
        $this->data['printer_dot_matrix'] = array('name' => 'printer_dot_matrix',
            'id' => 'printer_dot_matrix',
            'type' => 'text',
            'class' => 'form-control',
            'field-name' => 'Printer Kasir',
            'placeholder' => '',
            'value' => $this->form_validation->set_value('printer_dot_matrix', $form_data['printer_dot_matrix']));
        $this->data['booking_remove_lock'] = array('name' => 'booking_remove_lock',
            'id' => 'booking_remove_lock',
            'type' => 'text',
            'class' => 'form-control requiredTextField NumericOnly',
            'field-name' => 'waktu selesai lock reservasi',
            'placeholder' => '',
            'value' => $this->form_validation->set_value('booking_remove_lock', $form_data['booking_remove_lock']));

        $this->data['booking_start_lock'] = array('name' => 'booking_start_lock',
            'id' => 'booking_start_lock',
            'type' => 'text',
            'class' => 'form-control requiredTextField NumericOnly',
            'field-name' => 'waktu lock reservasi',
            'placeholder' => '',
            'value' => $this->form_validation->set_value('booking_start_lock', $form_data['booking_start_lock']));


        $this->data['void_manager_confirmation_yes'] = array('name' => 'void_manager_confirmation',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'konfirmasi void',
            'placeholder' => '',
            'checked' => ($form_data['void_manager_confirmation'] == 1) ? 'true' : '',
            'value' => "1");

        $this->data['void_manager_confirmation_no'] = array('name' => 'void_manager_confirmation',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'konfirmasi void',
            'placeholder' => '',
            'checked' => ($form_data['void_manager_confirmation'] == 0) ? 'true' : '',
            'value' => "0");

        $this->data['nearest_round_0'] = array('name' => 'nearest_round',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'pembulatan',
            'placeholder' => '',
            'checked' => ($form_data['nearest_round'] == 0) ? 'true' : '',
            'value' => "0");

        $this->data['nearest_round_50'] = array('name' => 'nearest_round',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'pembulatan',
            'placeholder' => '',
            'checked' => ($form_data['nearest_round'] == 50) ? 'true' : '',
            'value' => "50");

        $this->data['nearest_round_100'] = array('name' => 'nearest_round',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'pembulatan',
            'placeholder' => '',
            'checked' => ($form_data['nearest_round'] == 100) ? 'true' : '',
            'value' => "100");

        $this->data['nearest_round_1000'] = array('name' => 'nearest_round',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'pembulatan',
            'placeholder' => '',
            'checked' => ($form_data['nearest_round'] == 1000) ? 'true' : '',
            'value' => "1000");

        $this->data['use_kitchen_yes'] = array('name' => 'use_kitchen',
            'type' => 'radio',
            'class' => 'requiredTextField use_kitchen',
            'field-name' => 'Proses Kitchen',
            'placeholder' => '',
            'checked' => ($form_data['use_kitchen'] == 1) ? 'true' : '',
            'value' => "1");
        $this->data['use_kitchen_no'] = array('name' => 'use_kitchen',
            'type' => 'radio',
            'class' => 'requiredTextField use_kitchen',
            'field-name' => 'Proses Kitchen',
            'placeholder' => '',
            'checked' => ($form_data['use_kitchen'] == 0) ? 'true' : '',
            'value' => "0");

        $this->data['count_kitchen_process_yes'] = array('name' => 'count_kitchen_process',
            'type' => 'radio',
            'class' => 'requiredTextField count_kitchen_process',
            'field-name' => 'Hitung Jumlah Proses Kitchen',
            'placeholder' => '',
            'checked' => ($form_data['count_kitchen_process'] == 1) ? 'true' : '',
            'value' => "1");
        $this->data['count_kitchen_process_no'] = array('name' => 'count_kitchen_process',
            'type' => 'radio',
            'class' => 'requiredTextField count_kitchen_process',
            'field-name' => 'Hitung Jumlah Proses Kitchen',
            'placeholder' => '',
            'checked' => ($form_data['count_kitchen_process'] == 0) ? 'true' : '',
            'value' => "0");

        $this->data['kitchen_timestamp_yes'] = array('name' => 'kitchen_timestamp',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Kitchen Timestamp',
            'placeholder' => '',
            'checked' => ($form_data['kitchen_timestamp'] == 1) ? 'true' : '',
            'value' => "1");
        $this->data['kitchen_timestamp_no'] = array('name' => 'kitchen_timestamp',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Kitchen Timestamp',
            'placeholder' => '',
            'checked' => ($form_data['kitchen_timestamp'] == 0) ? 'true' : '',
            'value' => "0");

        $this->data['cleaning_process_yes'] = array('name' => 'cleaning_process',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Proses Cleaning',
            'placeholder' => '',
            'checked' => ($form_data['cleaning_process'] == 1) ? 'true' : '',
            'value' => "1");

        $this->data['cleaning_process_no'] = array('name' => 'cleaning_process',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Proses Cleaning',
            'placeholder' => '',
            'checked' => ($form_data['cleaning_process'] == 0) ? 'true' : '',
            'value' => "0");
        $this->data['use_role_checker_yes'] = array('name' => 'use_role_checker',
            'type' => 'radio',
            'class' => 'requiredTextField use_role_checker',
            'field-name' => 'Role Checker',
            'placeholder' => '',
            'checked' => ($form_data['use_role_checker'] == 1) ? 'true' : '',
            'value' => "1");

        $this->data['use_role_checker_no'] = array('name' => 'use_role_checker',
            'type' => 'radio',
            'class' => 'requiredTextField use_role_checker',
            'field-name' => 'Role Checker',
            'placeholder' => '',
            'checked' => ($form_data['use_role_checker'] == 0) ? 'true' : '',
            'value' => "0");
        $this->data['auto_print_yes'] = array('name' => 'auto_print',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Otomatis Print',
            'placeholder' => '',
            'checked' => ($form_data['auto_print'] == 1) ? 'true' : '',
            'value' => "1"); 

        $this->data['auto_print_no'] = array('name' => 'auto_print',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Otomatis Print',
            'placeholder' => '',
            'checked' => ($form_data['auto_print'] == 0) ? 'true' : '',
            'value' => "0");

         $this->data['auto_print_report_product_yes'] = array('name' => 'auto_print_report_product',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Otomatis Print Report Product',
            'placeholder' => '',
            'checked' => ($form_data['auto_print_report_product'] == 1) ? 'true' : '',
            'value' => "1");

          $this->data['auto_print_report_product_no'] = array('name' => 'auto_print_report_product',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Otomatis Print Report Product',
            'placeholder' => '',
            'checked' => ($form_data['auto_print_report_product'] == 0) ? 'true' : '',
            'value' => "0");


        $this->data['auto_print_report_pettycash_yes'] = array('name' => 'auto_print_report_pettycash',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Otomatis Print Report Product',
            'placeholder' => '',
            'checked' => ($form_data['auto_print_report_pettycash'] == 1) ? 'true' : '',
            'value' => "1");

        $this->data['auto_print_report_pettycash_no'] = array('name' => 'auto_print_report_pettycash',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Otomatis Print Report Product',
            'placeholder' => '',
            'checked' => ($form_data['auto_print_report_pettycash'] == 0) ? 'true' : '',
            'value' => "0");


        $this->data['auto_print_report_stock_yes'] = array('name' => 'auto_print_report_stock',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Otomatis Print Report Product',
            'placeholder' => '',
            'checked' => ($form_data['auto_print_report_stock'] == 1) ? 'true' : '',
            'value' => "1");

        $this->data['auto_print_report_stock_no'] = array('name' => 'auto_print_report_stock',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Otomatis Print Report Product',
            'placeholder' => '',
            'checked' => ($form_data['auto_print_report_stock'] == 0) ? 'true' : '',
            'value' => "0");

        $this->data['delivery_company_yes'] = array('name' => 'delivery_company',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Jasa Kurir',
            'placeholder' => '',
            'checked' => ($form_data['delivery_company'] == 1) ? 'true' : '',
            'value' => "1");
        $this->data['delivery_company_no'] = array('name' => 'delivery_company',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Jasa Kurir',
            'placeholder' => '',
            'checked' => ($form_data['delivery_company'] == 0) ? 'true' : '',
            'value' => "0");

        $this->data['report_total_customer_yes'] = array('name' => 'report_total_customer',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'View Report Total Customer',
            'placeholder' => '',
            'checked' => ($form_data['report_total_customer'] == 1) ? 'true' : '',
            'value' => "1");
        $this->data['report_total_customer_no'] = array('name' => 'report_total_customer',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'View Report Total Customer',
            'placeholder' => '',
            'checked' => ($form_data['report_total_customer'] == 0) ? 'true' : '',
            'value' => "0");

        $this->data['summary_sales_on_cashier_yes'] = array('name' => 'summary_sales_on_cashier',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'View Report Total Customer',
            'placeholder' => '',
            'checked' => ($form_data['summary_sales_on_cashier'] == 1) ? 'true' : '',
            'value' => "1");
        $this->data['summary_sales_on_cashier_no'] = array('name' => 'summary_sales_on_cashier',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'View Report Total Customer',
            'placeholder' => '',
            'checked' => ($form_data['summary_sales_on_cashier'] == 0) ? 'true' : '',
            'value' => "0");

        $this->data['theme_default'] = array('name' => 'theme',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Tema',
            'placeholder' => '',
            'checked' => ($form_data['theme'] == 1) ? 'true' : '',
            'value' => "1");

        $this->data['theme_mini'] = array('name' => 'theme',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Tema',
            'placeholder' => '',
            'checked' => ($form_data['theme'] == 2) ? 'true' : '',
            'value' => "2");

        $this->data['mobile_default'] = array('name' => 'mobile_mode',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Support Mobile Mode',
            'placeholder' => '',
            'checked' => ($form_data['mobile_mode'] == 0) ? 'true' : '',
            'value' => "0"); 

        $this->data['mobile_support'] = array('name' => 'mobile_mode',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Support Mobile Mode',
            'placeholder' => '',
            'checked' => ($form_data['mobile_mode'] == 1) ? 'true' : '',
            'value' => "1");

        $this->data['booking_start_lock'] = array('name' => 'booking_start_lock',
            'id' => 'booking_start_lock',
            'type' => 'text',
            'class' => 'form-control requiredTextField NumericOnly',
            'field-name' => 'waktu lock reservasi',
            'placeholder' => '',
            'value' => $this->form_validation->set_value('booking_start_lock', $form_data['booking_start_lock']));

        $this->data['range_booking_time'] = array('name' => 'range_booking_time',
            'id' => 'range_booking_time',
            'type' => 'text',
            'class' => 'form-control requiredTextField qty-input',
            'field-name' => 'waktu lock reservasi',
            'placeholder' => '',
            'value' => $this->form_validation->set_value('range_booking_time', $form_data['range_booking_time']));
        $this->data['courier_service'] = array('name' => 'courier_service',
            'id' => 'courier_service',
            'type' => 'text',
            'class' => 'form-control requiredTextField only_numeric',
            'field-name' => 'Persentase biaya delivery order untuk kurir dari harga ongkos kirim.',
            'placeholder' => '',
            'value' => $this->form_validation->set_value('courier_service', $form_data['courier_service']));
        $this->data['printer_logo'] = array(
            'name' => 'printer_logo',
            'id' => 'printer_logo',
            'type' => 'file',
            'class' => 'form-control maxUploadSize',
            'placeholder' => '',
            'data-maxsize' => '1000000', // byte
            'value' => $this->form_validation->set_value('printer_logo', $form_data['printer_logo'])
        );

        $this->data['revenue_sharing'] = array('name' => 'revenue_sharing',
            'id' => 'revenue_sharing',
            'type' => 'text',
            'class' => 'form-control requiredTextField NumericOnly',
            'field-name' => 'Besaran Revenue Sharing',
            'placeholder' => '',
            'value' => $this->form_validation->set_value('revenue_sharing', $form_data['revenue_sharing']));

        $this->data['check_server_before_close_yes'] = array('name' => 'check_server_before_close_transaction',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Cek Data Transaksi Server Sebelum Closing',
            'placeholder' => '',
            'checked' => ($form_data['check_server_before_close_transaction'] == 1) ? 'true' : '',
            'value' => "1");
			
        $this->data['check_server_before_close_no'] = array('name' => 'check_server_before_close_transaction',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Cek Data Transaksi Server Sebelum Closing',
            'placeholder' => '',
            'checked' => ($form_data['check_server_before_close_transaction'] == 0) ? 'true' : '',
            'value' => "0");


        $printer_cashier = $this->store_model->get_by('master_general_setting', "printer_cashier", "name");

        $this->data['cashier_use_logo_yes'] = array('name' => 'is_cashier_use_logo',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Cashier Use Logo',
            'placeholder' => '',
            'checked' => ($printer_cashier->default == 1) ? 'true' : '',
            'value' => "1");

        $this->data['cashier_use_logo_no'] = array('name' => 'is_cashier_use_logo',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Cashier Use Logo',
            'placeholder' => '',
            'checked' => ($printer_cashier->default == 0) ? 'true' : '',
            'value' => "0");

        $printer_kitchen = $this->store_model->get_by('master_general_setting', "printer_kitchen", "name");

        $this->data['kitchen_use_logo_yes'] = array('name' => 'is_kitchen_use_logo',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Kitchen Use Logo',
            'placeholder' => '',
            'checked' => ($printer_kitchen->default == 1) ? 'true' : '',
            'value' => "1");

        $this->data['kitchen_use_logo_no'] = array('name' => 'is_kitchen_use_logo',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Kitchen Use Logo',
            'placeholder' => '',
            'checked' => ($printer_kitchen->default == 0) ? 'true' : '',
            'value' => "0");

        $printer_checker = $this->store_model->get_by('master_general_setting', "printer_checker", "name");

        $this->data['checker_use_logo_yes'] = array('name' => 'is_checker_use_logo',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Kitchen Use Logo',
            'placeholder' => '',
            'checked' => ($printer_checker->default == 1) ? 'true' : '',
            'value' => "1");

        $this->data['checker_use_logo_no'] = array('name' => 'is_checker_use_logo',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Kitchen Use Logo',
            'placeholder' => '',
            'checked' => ($printer_checker->default == 0) ? 'true' : '',
            'value' => "0");

        $this->data['checker_auto_yes'] = array('name' => 'is_checker_auto',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Checker Auto',
            'placeholder' => '',
            'checked' => ($form_data["auto_checker"] == 1) ? 'true' : '',
            'value' => "1");

        $this->data['checker_auto_no'] = array('name' => 'is_checker_auto',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Checker Auto',
            'placeholder' => '',
            'checked' => ($form_data["auto_checker"] == 0) ? 'true' : '',
            'value' => "0");

        $printer_checker_kitchen = $this->store_model->get_by('master_general_setting', "printer_checker_kitchen", "name");

        $this->data['checker_kitchen_use_logo_yes'] = array('name' => 'is_checker_kitchen_use_logo',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Kitchen Use Logo',
            'placeholder' => '',
            'checked' => ($printer_checker_kitchen->default == 1) ? 'true' : '',
            'value' => "1");

        $this->data['checker_kitchen_use_logo_no'] = array('name' => 'is_checker_kitchen_use_logo',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Kitchen Use Logo',
            'placeholder' => '',
            'checked' => ($printer_checker_kitchen->default == 0) ? 'true' : '',
            'value' => "0");

        $this->data['checker_kitchen_auto_yes'] = array('name' => 'is_checker_kitchen_auto',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Checker Auto',
            'placeholder' => '',
            'checked' => ($form_data["auto_checker_kitchen"] == 1) ? 'true' : '',
            'value' => "1");

        $this->data['checker_kitchen_auto_no'] = array('name' => 'is_checker_kitchen_auto',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Checker Auto',
            'placeholder' => '',
            'checked' => ($form_data["auto_checker_kitchen"] == 0) ? 'true' : '',
            'value' => "0");

        $this->data['cashier_printer_width_48'] = array('name' => 'cashier_printer_width',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Lebar Printer',
            'placeholder' => '',
            'checked' => ($printer_cashier->description == "48") ? 'true' : '',
            'value' => "48");

        $this->data['cashier_printer_width_72'] = array('name' => 'cashier_printer_width',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Lebar Printer',
            'placeholder' => '',
            'checked' => ($printer_cashier->description == "72") ? 'true' : '',
            'value' => "72");
        $this->data['cashier_printer_width_72_plus'] = array('name' => 'cashier_printer_width',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Lebar Printer',
            'placeholder' => '',
            'checked' => ($printer_cashier->description == "72+") ? 'true' : '',
            'value' => "72+");
        $this->data['kitchen_printer_width_48'] = array('name' => 'kitchen_printer_width',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Lebar Printer',
            'placeholder' => '',
            'checked' => ($printer_kitchen->description == "48") ? 'true' : '',
            'value' => "48");

        $this->data['kitchen_printer_width_72'] = array('name' => 'kitchen_printer_width',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Lebar Printer',
            'placeholder' => '',
            'checked' => ($printer_kitchen->description == "72") ? 'true' : '',
            'value' => "72");
        $this->data['kitchen_printer_width_72_plus'] = array('name' => 'kitchen_printer_width',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Lebar Printer',
            'placeholder' => '',
            'checked' => ($printer_kitchen->description == "72+") ? 'true' : '',
            'value' => "72+");
        $this->data['checker_printer_width_48'] = array('name' => 'checker_printer_width',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Lebar Printer',
            'placeholder' => '',
            'checked' => ($printer_checker->description == "48") ? 'true' : '',
            'value' => "48");

        $this->data['checker_printer_width_72'] = array('name' => 'checker_printer_width',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Lebar Printer',
            'placeholder' => '',
            'checked' => ($printer_checker->description == "72") ? 'true' : '',
            'value' => "72");
        $this->data['checker_printer_width_72_plus'] = array('name' => 'checker_printer_width',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Lebar Printer',
            'placeholder' => '',
            'checked' => ($printer_checker->description == "72+") ? 'true' : '',
            'value' => "72+");
        $this->data['checker_kitchen_printer_width_48'] = array('name' => 'checker_kitchen_printer_width',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Lebar Printer',
            'placeholder' => '',
            'checked' => ($printer_checker_kitchen->description == "48") ? 'true' : '',
            'value' => "48");

        $this->data['checker_kitchen_printer_width_72'] = array('name' => 'checker_kitchen_printer_width',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Lebar Printer',
            'placeholder' => '',
            'checked' => ($printer_checker_kitchen->description == "72") ? 'true' : '',
            'value' => "72");
        $this->data['checker_kitchen_printer_width_72_plus'] = array('name' => 'checker_kitchen_printer_width',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Lebar Printer',
            'placeholder' => '',
            'checked' => ($printer_checker_kitchen->description == "72+") ? 'true' : '',
            'value' => "72+");
        $this->data['inventory_account_id'] = $form_data["inventory_account_id"];
        $this->data['cash_account_id'] = $form_data["cash_account_id"];
        $this->data['temporary_cash_account_id'] = $form_data["temporary_cash_account_id"];
        $this->data['prive_account_id'] = $form_data["prive_account_id"];
        $this->data['credit_receivable_account_id'] = $form_data["credit_receivable_account_id"];
        $this->data['voucher_account_id'] = $form_data["voucher_account_id"];
        $this->data['debit_receivable_account_id'] = $form_data["debit_receivable_account_id"];
        $this->data['cogs_account_id'] = $form_data["cogs_account_id"];
        $this->data['income_account_id'] = $form_data["income_account_id"];
        $this->data['tax_account_id'] = $form_data["tax_account_id"];
        $this->data['pembulatan_account_id'] = $form_data["pembulatan_account_id"];
        $this->data['piutang_dagang_account_id'] = $form_data["piutang_dagang_account_id"];
        $this->data['piutang_karyawan_account_id'] = $form_data["piutang_karyawan_account_id"];
        $this->data['temporary_bank_account_id'] = $form_data["temporary_bank_account_id"];
        $this->data['discount_account_id'] = $form_data["discount_account_id"];
        $this->data['delivery_cost_account_id'] = $form_data["delivery_cost_account_id"];
        $this->data['hutang_dp_account_id'] = $form_data["hutang_dp_account_id"];
        $this->data['other_income_account_id'] = $form_data["other_income_account_id"];
        $this->data['bank_dp_account_id'] = $form_data["bank_dp_account_id"];

        $this->data['member_category_lists'] = $this->member_category_model->get_category_dropdown();
        $this->data['member_category_lists'][0] = "Tidak Ada Member Kategori Karyawan";
        $this->data['store_lists'] = $this->store_model->get_store_dropdown();

        $this->data['accounts'] = $this->store_model->get_account_dropdown();

        $this->data['content'] .= $this->load->view('admin/setting.php', $this->data, true);

        $this->render('admin');
    }

    public function update_master_general_setting()
    {
        $this->load->model('categories_model');
        $store_id = $this->input->post('store_id');
        $this->categories_model->save_by('master_general_setting', array('value' => $store_id), "store_id", 'name');
    }

}