<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class general_expenses extends Admin_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('bank_account_model');
    $this->load->model('categories_model');
    $this->load->model('account_model');
    $this->load->model('account_data_model');
    $this->load->model('petty_cash_model');
  }

  public function index()
  {
    $this->data['title']    = "General Expenses";
    $this->data['subtitle'] = "Pengeluaran Umum";
    $this->data['message']  = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
    $this->data['message_success'] = $this->session->flashdata('message_success');
    $this->data['content'] .= $this->load->view('admin/general-expenses-list', $this->data, true);
    $this->render('admin');
  }

  public function add()
  {
    $this->data['title']    = "General Expenses";
    $this->data['subtitle'] = "Tambah Pengeluaran";
    $this->form_validation->set_rules('ge_id', 'Jenis Pengeluaran', 'required|xss_clean');
    $this->form_validation->set_rules('amount', 'Jumlah Pengeluaran', 'required|xss_clean|number|greater_than[0]');
    
    if($this->data['module']["ACCOUNTING"] == 1){
      $this->form_validation->set_rules('account_id', 'Akun', 'required');
    }

    if ($this->form_validation->run() == true) {
      $ge_id = $this->input->post('ge_id');
      $description = $this->input->post('description');
      $amount = $this->input->post('amount');
      $date = date("Y-m-d H:i:s");
      $number = $this->data['setting']['store_id'] . time();

      $data_array = array(
        'expense_number' => $number,
        'ge_id' => $ge_id,
        'description' => $description,
        'amount' => $amount,
        'created_at' => $date,
        'created_by' => $this->data['user_profile_data']->id
      );

      if($this->data['module']["ACCOUNTING"] == 1){
        $data_array['account_id'] = $this->input->post('account_id');
      }

      $save = $this->categories_model->save('backoffice_expenses', $data_array);

      if ($save === false) {
        $this->session->set_flashdata('message', $this->lang->line('error_add'));
      } else {
        if ($this->data['module']["ACCOUNTING"] == 1) {
          $get_enum_entry_type = array_pop($this->account_data_model->get_all_where('enum_account_data_entry_type', array('value' => 'general_entries'), 1));

          $array = array(
            'store_id' => $this->data['setting']['store_id'],
            'entry_type' => $get_enum_entry_type->id,
            'foreign_id' => $save,
            'account_id' => $this->input->post('account_id'),
            'debit' => 0,
            'credit' => $amount,
            'info' => $description,
            'created_at' => $date,
            'created_by' => $this->data['user_profile_data']->id,
            'modified_at' => $date,
            'modified_by' => $this->data['user_profile_data']->id
          );
          $account_credit = $this->account_data_model->add($array);

          $general_expenses = $this->account_data_model->get_one('general_expenses', $ge_id);

          $array = array(
            'store_id' => $this->data['setting']['store_id'],
            'entry_type' => $get_enum_entry_type->id,
            'foreign_id' => $save,
            'account_id' => $general_expenses->account_id,
            'debit' => $amount,
            'credit' => 0,
            'info' => $description,
            'created_at' => $date,
            'created_by' => $this->data['user_profile_data']->id,
            'modified_at' => $date,
            'modified_by' => $this->data['user_profile_data']->id
          );

          $account_debit = $this->account_data_model->add($array);

          if ($account_debit) {
            $account_data_detail = array(
              'account_data_id' => $account_debit,
              'info' => 'general_entries',
              'store_id' => $this->data['setting']['store_id'],
              'description' => $description,
              'created_at' => $date
            );
            $this->account_data_model->add_detail($account_data_detail);
          }
        }
        $this->session->set_flashdata('message_success', $this->lang->line('success_add'));
      }

      $btnaction = $this->input->post('btnAction');

      if ($btnaction == 'save_exit') {
        redirect(SITE_ADMIN . '/general_expenses', 'refresh');
      }
      else {
        redirect(SITE_ADMIN . '/general_expenses/add/', 'refresh');
      }
    }else {
      $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
      $this->data['message_success'] = $this->session->flashdata('message_success');
      
      $this->data['description'] = array(
        'name' => 'description',
        'id' => 'description',
        'type' => 'text',
        'class' => 'form-control requiredTextField',
        'field-name' => 'Deskripsi',
        'placeholder' => 'Masukan deskripsi',
        'rows'=>4,
        'value' => $this->form_validation->set_value('description'));
      
      $this->data['amount'] = array(
        'name' => 'amount',
        'id' => 'amount',
        'type' => 'number',
        'class' => 'form-control requiredTextField',
        'field-name' => 'Jumlah Pengeluaran',
        'placeholder' => 'Masukan Jumlah Pengeluaran',
        'value' => $this->form_validation->set_value('amount'));

      $general_expenses = $this->petty_cash_model->get_general_expenses();
      $this->data['ge_id'] = $general_expenses;

      $this->data['is_accounting_module_active'] = $this->data['module']["ACCOUNTING"];

      if($this->data['module']["ACCOUNTING"] == 1){
        $this->data['accounts'] = $this->account_model->get_account_dropdown(); 
      }

      $this->data['content'] .= $this->load->view('admin/general-expenses-add', $this->data, true);
      $this->render('admin');
    }
  }

  public function edit()
  {
    $id = $this->uri->segment(4);

    if (empty($id)) {
      redirect(SITE_ADMIN . '/general_expenses');
    }

    $form_data = $this->categories_model->get_one('backoffice_expenses', $id);

    if (empty($form_data)) {
      redirect(SITE_ADMIN . '/general_expenses');
    }

    $this->data['form_data'] = $form_data;
    $this->data['subtitle']  = "Edit Pengeluaran";

    //validate form input
    $this->form_validation->set_rules('ge_id', 'Jenis Pengeluaran', 'required|xss_clean');
    $this->form_validation->set_rules('amount', 'Jumlah Pengeluaran', 'required|xss_clean|number|greater_than[0]');
    
    if($this->data['module']["ACCOUNTING"] == 1){
      $this->form_validation->set_rules('account_id', 'Akun', 'required');
    }

    if (isset($_POST) && ! empty($_POST)) {

      if ($this->form_validation->run() === TRUE) {
        $ge_id = $this->input->post('ge_id');
        $description = $this->input->post('description');
        $amount = $this->input->post('amount');
        $date = date("Y-m-d H:i:s");
        $number = $this->data['setting']['store_id'] . time();

        $data_array = array(
          'expense_number' => $number,
          'ge_id' => $ge_id,
          'description' => $description,
          'amount' => $amount,
          'created_at' => $date,
          'created_by' => $this->data['user_profile_data']->id
        );

        if($this->data['module']["ACCOUNTING"] == 1){
          $data_array['account_id'] = $this->input->post('account_id');
        }

        $save = $this->categories_model->save('backoffice_expenses', $data_array, $id);

        if ($save === false) {
          $this->session->set_flashdata('message', $this->lang->line('error_add'));
        } else {
          if ($this->data['module']["ACCOUNTING"] == 1) {
            $account_data_olds = $this->account_data_model->get_all_where("account_data", array("entry_type" => 1, "foreign_id" => $id));
            foreach ($account_data_olds as $a) {
                $this->account_data_model->delete_by_limit("account_data_detail", array("account_data_id" => $a->id), 0);
            }
            $this->account_data_model->delete_by_limit("account_data", array("entry_type" => 1, "foreign_id" => $id), 0);

            $get_enum_entry_type = array_pop($this->account_data_model->get_all_where('enum_account_data_entry_type', array('value' => 'general_entries'), 1));

            $array = array(
              'store_id' => $this->data['setting']['store_id'],
              'entry_type' => $get_enum_entry_type->id,
              'foreign_id' => $save,
              'account_id' => $this->input->post('account_id'),
              'debit' => 0,
              'credit' => $amount,
              'info' => $description,
              'created_at' => $date,
              'created_by' => $this->data['user_profile_data']->id,
              'modified_at' => $date,
              'modified_by' => $this->data['user_profile_data']->id
            );
            $account_credit = $this->account_data_model->add($array);

            $general_expenses = $this->account_data_model->get_one('general_expenses', $ge_id);

            $array = array(
              'store_id' => $this->data['setting']['store_id'],
              'entry_type' => $get_enum_entry_type->id,
              'foreign_id' => $save,
              'account_id' => $general_expenses->account_id,
              'debit' => $amount,
              'credit' => 0,
              'info' => $description,
              'created_at' => $date,
              'created_by' => $this->data['user_profile_data']->id,
              'modified_at' => $date,
              'modified_by' => $this->data['user_profile_data']->id
            );

            $account_debit = $this->account_data_model->add($array);

            if ($account_debit) {
              $account_data_detail = array(
                'account_data_id' => $account_debit,
                'info' => $number,
                'store_id' => $this->data['setting']['store_id'],
                'description' => $description,
                'created_at' => $date
              );
              $this->account_data_model->add_detail($account_data_detail);
            }
          }
          $this->session->set_flashdata('message_success', $this->lang->line('success_add'));
        }

        $btnaction = $this->input->post('btnAction');
        if ($btnaction == 'save_exit') {
          redirect(SITE_ADMIN . '/general_expenses', 'refresh');
        }
        else {
          redirect(SITE_ADMIN . '/general_expenses/edit/' . $id, 'refresh');
        }
      }
    }

        //display the edit user form
    $this->data['csrf'] = $this->_get_csrf_nonce();

        //set the flash data error message if there is one
    $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
    $this->data['message_success'] = $this->session->flashdata('message_success');

    $this->data['description'] = array(
      'name' => 'description',
      'id' => 'description',
      'type' => 'text',
      'class' => 'form-control requiredTextField',
      'field-name' => 'Deskripsi',
      'placeholder' => 'Masukan deskripsi',
      'rows'=>4,
      'value' => $this->form_validation->set_value('description', $form_data->description));
    
    $this->data['amount'] = array(
      'name' => 'amount',
      'id' => 'amount',
      'type' => 'number',
      'class' => 'form-control requiredTextField',
      'field-name' => 'Jumlah Pengeluaran',
      'placeholder' => 'Masukan Jumlah Pengeluaran',
      'value' => $this->form_validation->set_value('amount', $form_data->amount));

    $general_expenses = $this->petty_cash_model->get_general_expenses();
    $this->data['ge_id'] = $general_expenses;

    $this->data['is_accounting_module_active'] = $this->data['module']["ACCOUNTING"];

    if($this->data['module']["ACCOUNTING"] == 1){
      $this->data['accounts'] = $this->account_model->get_account_dropdown(); 
    }

    $this->data['content'] .= $this->load->view('admin/general-expenses-edit', $this->data, true);

    $this->render('admin');
  }

  public function delete(){
    $id = $this->uri->segment(4);

    if (empty($id)) {
      redirect(SITE_ADMIN . '/general_expenses');
    }

    $result = $this->categories_model->delete('backoffice_expenses', $id);

    if ($result) {
      if ($this->data['module']['ACCOUNTING'] == 1) {
        $account_data_olds = $this->account_data_model->get_all_where("account_data", array("entry_type" => 1, "foreign_id" => $id));
        foreach ($account_data_olds as $a) {
            $this->account_data_model->delete_by_limit("account_data_detail", array("account_data_id" => $a->id), 0);
        }
        $this->account_data_model->delete_by_limit("account_data", array("entry_type" => 1, "foreign_id" => $id), 0);
      }
      $this->session->set_flashdata('message_success', $this->lang->line('success_delete'));
    } else {
      $this->session->set_flashdata('message', $this->lang->line('error_delete'));
    }

    redirect(SITE_ADMIN . '/general_expenses', 'refresh');
  }

  public function getdatatables()
  {
    $this->load->library(array('datatables'));
    $this->load->helper(array('datatables'));
    $this->datatables->select('be.id, ge.name, be.description, be.amount')
      ->from('backoffice_expenses be')
      ->join('general_expenses ge', 'ge.id = be.ge_id')
      ->add_column('amount', '$1', 'convert_rupiah(amount)')
      ->add_column('actions', "<div class='btn-group'>
        <a href='" . base_url(SITE_ADMIN . '/general_expenses/edit/$1') . "'  class='btn btn-default'><i class='fa fa-pencil'></i> Edit</a>
        <a href='" . base_url(SITE_ADMIN . '/general_expenses/delete/$1') . "' class='btn btn-danger deleteNow' rel='Pengeluaran Umum'><i class='fa fa-trash-o'></i> Hapus</a>
      </div>", 'id');
    echo $this->datatables->generate();
  }
}