<?php if (! defined('BASEPATH')) exit('No direct script access allowed');


class Journal extends Admin_Controller
{
    /**
     * Global setting for store
     * @var mixed
     */
    private $_setting;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('store_model');
        $this->load->model('account_model');
        $this->load->model('general_entries_model');
        $this->load->model('account_data_model');

        $this->_setting = $this->data['setting'];
        $this->_store_data = $this->ion_auth->user()->row();
    }


    public function index()
    {
        $this->general();
    }

    public function general()
    {
        if($this->input->post()){
            $this->form_validation->set_rules('supplier', 'Nama Supplier', 'required|xss_clean');
            $this->form_validation->set_rules('order_date', 'Tanggal', 'required|xss_clean');
            $this->form_validation->set_rules('po_number', 'Nomor Dokumen', 'numeric|required|xss_clean');
            $this->form_validation->set_rules('debit[]', 'Debit', 'numeric|is_natural_no_zero|required|xss_clean');
            $this->form_validation->set_rules('credit[]', 'Credit', 'numeric|is_natural_no_zero|required|xss_clean');
            if($this->form_validation->run() == true) {
                $data = array(
                    'store_id'          => $this->input->post('supplier'),
                    'document_number'   => $this->input->post('po_number'),
                    'notes'             => $this->input->post('description'),
                    'entry_date'        => $this->input->post('order_date'),
                    'created_by'        => $this->_store_data->id
                );

                $entry_id   = $this->general_entries_model->add($data);
                $debits     = $this->input->post('debit');
                $credits    = $this->input->post('credit');
                
                $info       = $this->input->post('info');
                $data       = array();
                $account_data = array(
                    'store_id'      => $this->input->post('supplier'),
                    'account_id'    => 0,
                    'credit'        => 0,
                    'debit'         => 0,
                    'created_by'    => $this->_store_data->id,
                    'entry_type'    => 1,
                    'foreign_id'    => $entry_id,
                    'info'          => ''
                );
                foreach ($credits as $account_id => $credit) {
                    $account_data['credit']     = $credit;
                    $account_data['account_id'] = $account_id;
                    $account_data['info']       = $info[$account_id];
                    array_push($data, $account_data);
                }


                $account_data = array(
                    'store_id'      => $this->input->post('supplier'),
                    'account_id'    => 0,
                    'credit'        => 0,
                    'debit'         => 0,
                    'created_by'    => $this->_store_data->id,
                    'entry_type'    => 1,
                    'foreign_id'    => $entry_id,
                    'info'          => ''
                );

                foreach ($debits as $account_id => $debit) {
                    $account_data['debit']      = $debit;
                    $account_data['account_id'] = $account_id;
                    $account_data['info']       = $info[$account_id];
                    array_push($data, $account_data);
                }
               
                $status = $this->account_data_model->add($data);
                if($status) $this->session->set_flashdata('message_success', 'Jurnal berhasil ditambahkan');
                else $this->session->set_flashdata('message', 'Jurnal gagal ditambahkan, silahkan cobal lagi');
                redirect(SITE_ADMIN.'/journal/general', 'refresh');
            }
            else{
                $this->data['debits']     = $this->input->post('debit');
                $this->data['credits']    = $this->input->post('credit');
                $this->data['info']       = $this->input->post('info');
                $this->data['code']       = $this->input->post('code');
                $this->data['name']       = $this->input->post('name');
            }

        }
        $this->data['title']           = "Jurnal";
        $this->data['subtitle']        = "Jurnal Umum";
        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['accounts']     = $this->account_model->get_all();
        $this->data['stores']       = $this->store_model->get_all_store();
        $this->data['store_id']     = $this->_setting['store_id'];
        $this->data['content']     .= $this->load->view('admin/journal-general', $this->data, true);

        $this->render('admin');
    }

    public function edit($supplier_id=-1)
    {
        $supplier = $this->supplier_model->get(array('id' => $supplier_id));
        if(count($supplier) <= 0) {
            $this->session->flashdata('message', 'Tidak ada detail untuk transfer tersebut');
            redirect(SITE_ADMIN.'/supplier/supplier_list', 'refresh');
        }
        if($this->input->post()){
            $this->form_validation->set_rules('supplier_name', 'Nama supplier', 'required|xss_clean|min_length[1]|max_length[100]');
            $this->form_validation->set_rules('address', 'Alamat', 'required|xss_clean');
            $this->form_validation->set_rules('phone', 'Telepon', 'required|xss_clean|min_length[8]|max_length[13]|callback_validate_phone');
            $this->form_validation->set_rules('email', 'Email', 'required|xss_clean|callback_validate_email');
            $this->form_validation->set_rules('contact', 'Kontak Person', 'required|xss_clean');
            if($this->form_validation->run() == true) {
                $data = array(
                    'name' => $this->input->post('supplier_name'),
                    'address' => $this->input->post('address'),
                    'contact_name' => $this->input->post('contact'),
                    'phone' => $this->input->post('phone'),
                    'email' => $this->input->post('email'),
                    'account_receivable_id' => $this->input->post('receivable_id'),
                    'account_payable_id' => $this->input->post('payable_id'),
                );
                $status = $this->supplier_model->update($data, array('id' => $this->input->post('supplier_id')));
                if($status){
                    $this->session->set_flashdata('message_success', 'Supplier berhasil diubah');
                    redirect(SITE_ADMIN . '/supplier/supplier_list', 'refresh');
                }
                else{
                    $this->session->set_flashdata('message', 'Terjadi Kesalahan, silahkan diulangi');
                }
            }

        }

        $this->data['title']    = "Supplier";
        $this->data['subtitle'] = "Ubah Supplier";
        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['supplier']     = array_pop($supplier);
        $this->data['accounts']     = $this->account_model->get();
        $this->data['content']     .= $this->load->view('admin/supplier-edit', $this->data, true);

        $this->render('admin');

    }

    public function delete($supplier_id=-1)
    {
        $status = $this->supplier_model->delete(array('id' => $supplier_id));
        if($status) $this->session->flashdata('message_success', 'Supplier berhasil dihapus');
        else $this->session->flashdata('message', 'Tidak bisa menghapus supplier');

        redirect(SITE_ADMIN.'/supplier/supplier_list', 'refresh');
    }

    public function get_accounts()
    {
        $accounts = $this->account_model->get_all();
        $this->_response(array('status' => true, 'accounts' => $accounts));
    }
    
    /**
     * Display array in json format then immediately stop execution
     * @param  mixed $result array to be encoded
     * @return void
     */
    private function _response($result)
    {
        echo json_encode($result);
        die();
    }
}