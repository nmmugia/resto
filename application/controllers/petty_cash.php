<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Petty_Cash extends Cashier_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('petty_cash_model');
        $this->load->model('account_data_model');
        $this->groups_access->check_feature_access('petty_cash');
        $all_cooking_status = array();
        foreach ($this->petty_cash_model->get("enum_cooking_status")->result() as $a) {
            $all_cooking_status[$a->id] = $a->status_name;
        }
        $this->data['all_cooking_status'] = json_encode($all_cooking_status);
    }

    public function index()
    {
        if ($this->data['data_open_close']->status != 1) redirect(base_url());
        $this->groups_access->check_feature_access('petty_cash');
        $this->data['title'] = "Kas Kecil";
        $this->data['theme'] = 'floor-theme';
        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');
        $this->data['add_petty_cash'] = base_url('petty_cash/add');
        $this->data['add_balance_cash'] = base_url('balance_cash/add');
        $this->data['data_url'] = base_url('petty_cash/get_data');;
        $this->data['data_balance_url'] = base_url('petty_cash/get_balance_data');;
        $this->data['content'] .= $this->load->view('petty_cash_v', $this->data, true);
        $this->render('cashier');
    }

    public function get_data()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));
		
		$open_close = $this->petty_cash_model->get_by("open_close_cashier", 1, "status");
		
        $this->datatables->select('pc.id,pc.user_id,pc.description,pc.amount,u.name')
            ->from('petty_cash pc')
            ->join('users u', 'u.id = pc.user_id', 'left')
            ->where('date(date)', date('Y-m-d'))
            ->where('date >=', ($open_close && !empty($open_close->open_at) ? $open_close->open_at : date("Y-m-d H:i:s")))
            ->unset_column('amount')
            ->add_column("amount", "$1", "convert_rupiah(amount)")
            ->add_column('actions', "
      <a rel='tooltip' title='Edit' href='" . base_url('petty_cash/edit/$1') . "'  class='btn btn-default btn-xs' feature_confirmation='" . ($this->data['feature_confirmation']['petty_cash']) . "'><i class='fa fa-pencil'></i></a>
      <a href='" . base_url('petty_cash/delete/$1') . "' title='Delete' data-id='$1' class='btn btn-danger btn-petty-cash-delete btn-xs' feature_confirmation='" . ($this->data['feature_confirmation']['petty_cash']) . "'><i class='fa fa-trash-o'></i></a>
    ", 'id');
        echo $this->datatables->generate();
    }

    public function get_balance_data()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));
        $this->datatables->select('bch.id,bch.user_id,bch.description,bch.amount,u.name')
            ->from('balance_cash_history bch')
            ->join('users u', 'u.id = bch.user_id', 'left')
            ->where('date(date)', date('Y-m-d'))
            ->unset_column('amount')
            ->add_column("amount", "$1", "convert_rupiah(amount)")
            ->add_column('actions', "
      <a rel='tooltip' title='Edit' href='" . base_url('balance_cash/edit/$1') . "'  class='btn btn-default btn-xs'><i class='fa fa-pencil'></i></a>
      <a href='" . base_url('balance_cash/delete/$1') . "' title='Delete' data-id='$1' class='btn btn-danger btn-petty-cash-delete btn-xs' feature_confirmation='" . ($this->data['feature_confirmation']['petty_cash']) . "'><i class='fa fa-trash-o'></i></a>
    ", 'id');
        echo $this->datatables->generate();
    }

    public function add()
    {
        $this->data['title'] = "Kas Kecil";
        $this->data['subtitle'] = "Tambah Data";
        $this->form_validation->set_rules('description', 'Deskripsi Pengeluaran', 'required');
        $this->form_validation->set_rules('amount', 'Jumlah Pengeluaran', 'required|numeric');
        if ($this->form_validation->run() == true) {
            $ge_id = $this->input->post('ge_id');
            $data_array = array(
                "user_id" => $this->data['user_profile_data']->id,
                "petty_cash_id" => $this->data['setting']['store_id'] . time(),
                "date" => date("Y-m-d H:i:s"),
                "description" => $this->input->post("description"),
                "amount" => $this->input->post("amount"),
                'ge_id' => $ge_id,
            );
            $petty_cash = $this->petty_cash_model->save('petty_cash', $data_array);
            if ($petty_cash === false) {
                $this->session->set_flashdata('message', 'Gagal menyimpan data');
            } else {
                if ($this->data['module']['ACCOUNTING'] == 1) {
                    $get_enum_petty_cash = array_pop($this->account_data_model->get_all_where('enum_account_data_entry_type', array('value' => 'petty_cash'), 1));
                    $array = array(
                        'store_id'      => $this->data['setting']['store_id'],
                        'entry_type'    => $get_enum_petty_cash->id,
                        'foreign_id'    => $petty_cash,
                        'account_id'    => $this->data['setting']['petty_cash_account_id'],
                        'debit'         => 0,
                        'credit'        => $this->input->post("amount"),
                        'info'          => 'Pengeluaran Kas Kecil',
                        'created_at'    => date('Y-m-d H:i:s'),
                        'created_by'    => $this->data['user_profile_data']->id,
                        'modified_at'   => date('Y-m-d H:i:s'),
                        'modified_by'   => $this->data['user_profile_data']->id
                    );

                    $account_data = $this->account_data_model->add($array);

                    $general_expenses = $this->account_data_model->get_one('general_expenses', $ge_id);

                    $array = array(
                        'store_id'      => $this->data['setting']['store_id'],
                        'entry_type'    => $get_enum_petty_cash->id,
                        'foreign_id'    => $petty_cash,
                        'account_id'    => $general_expenses->account_id,
                        'debit'         => $this->input->post("amount"),
                        'credit'        => 0,
                        'info'          => $this->input->post("description"),
                        'created_at'    => date('Y-m-d H:i:s'),
                        'created_by'    => $this->data['user_profile_data']->id,
                        'modified_at'   => date('Y-m-d H:i:s'),
                        'modified_by'   => $this->data['user_profile_data']->id
                    );

                    $account_data = $this->account_data_model->add($array);

                    if ($account_data) {
                        $account_data_detail = array(
                            'account_data_id' => $account_data,
                            'info' => $this->data['setting']['store_id'] . time(),
                            'store_id' => $this->data['setting']['store_id'],
                            'description' => $this->input->post("description"),
                            'created_at' => date("Y-m-d H:i:s")
                        );
                        $this->account_data_model->add_detail($account_data_detail);
                    }
                }
                $this->session->set_flashdata('message_success', 'Berhasil menyimpan data');
            }
            $btnAction = $this->input->post('btnAction');
            if ($btnAction == 'save_exit') {
                redirect('petty_cash', 'refresh');
            } else {
                redirect('petty_cash/add', 'refresh');
            }
        } else {
            $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            $this->data['message_success'] = $this->session->flashdata('message_success');

            $this->data['description'] = array(
                'name' => 'description',
                'id' => 'description',
                'type' => 'text',
                'class' => 'form-control requiredTextField only_alpha_numeric',
                'field-name' => 'deskripsi pengeluaran',
                'placeholder' => 'deskripsi pengeluaran',
                'value' => $this->form_validation->set_value('description')
            );
            $this->data['amount'] = array(
                'name' => 'amount',
                'id' => 'amount',
                'type' => 'text',
                'class' => 'form-control requiredTextField only_numeric',
                'field-name' => 'jumlah pengeluaran',
                'placeholder' => 'jumlah pengeluaran',
                'value' => $this->form_validation->set_value('amount')
            );

            $general_expenses = $this->petty_cash_model->get_general_expenses();
            $this->data['ge_id'] = $general_expenses;

            $this->data['content'] .= $this->load->view('petty_cash_add_v', $this->data, true);
            $this->render('cashier');
        }
    }

    public function edit()
    {
        $id = $this->uri->segment(3);
        if (empty($id)) redirect('petty_cash');
        $this->load->model('petty_cash_model');
        $form_data = $this->petty_cash_model->get_one('petty_cash', $id);
        if (empty($form_data)) redirect('petty_cash');
        $this->data['form_data'] = $form_data;
        $this->data['title'] = "Kas Kecil";
        $this->data['subtitle'] = "Edit Data";
        $this->form_validation->set_rules('description', 'Deskripsi Pengeluaran', 'required');
        $this->form_validation->set_rules('amount', 'Jumlah Pengeluaran', 'required|numeric');
        if (isset($_POST) && !empty($_POST)) {
            if ($this->form_validation->run() === TRUE) {
                $data_array = array(
                    "user_id" => $this->data['user_profile_data']->id,
                    "date" => date("Y-m-d H:i:s"),
                    "description" => $this->input->post("description"),
                    "amount" => $this->input->post("amount"),
                    "has_sync" => 0,
                    'ge_id' => $this->input->post('ge_id'),
                );

                $petty_cash = $this->petty_cash_model->save('petty_cash', $data_array, $id);

                if ($petty_cash === false) {
                    $this->session->set_flashdata('message', 'Gagal menyimpan data');
                } else {
                    if ($this->data['module']['ACCOUNTING'] == 1) {
                        $account_data_olds = $this->account_data_model->get_all_where("account_data", array("entry_type" => 6, "foreign_id" => $id));
                        foreach ($account_data_olds as $a) {
                            $this->account_data_model->delete_by_limit("account_data_detail", array("account_data_id" => $a->id), 0);
                        }
                        $this->account_data_model->delete_by_limit("account_data", array("entry_type" => 6, "foreign_id" => $id), 0);
                        $array = array(
                            'has_synchronized' => 0,
                            'account_id' => $this->data['setting']['other_cost_account_id'],
                            'store_id' => $this->data['setting']['store_id'],
                            'entry_type' => 6,
                            'foreign_id' => $petty_cash,
                            'credit' => 0,
                            'debit' => $this->input->post("amount"),
                            'created_at' => date("Y-m-d H:i:s"),
                        );
                        $account_data = $this->account_data_model->add($array);
                        if ($account_data) {
                            $account_data_detail = array(
                                'account_data_id' => $account_data,
                                'info' => $this->data['setting']['store_id'] . time(),
                                'store_id' => $this->data['setting']['store_id'],
                                'description' => $this->input->post("description"),
                                'created_at' => date("Y-m-d H:i:s")
                            );
                            $this->account_data_model->add_detail($account_data_detail);
                        }                        
                    }
                    $this->session->set_flashdata('message_success', 'Berhasil menyimpan data');
                }
                $btnaction = $this->input->post('btnAction');
                if ($btnaction == 'save_exit') {
                    redirect('petty_cash', 'refresh');
                } else {
                    redirect('petty_cash/edit/' . $id, 'refresh');
                }
            }
        }
        $this->data['description'] = array(
            'name' => 'description',
            'id' => 'description',
            'type' => 'text',
            'class' => 'form-control requiredTextField only_alpha_numeric',
            'field-name' => "deskripsi pengeluaran",
            'placeholder' => "deskripsi pengeluaran",
            'value' => $this->form_validation->set_value('description', $form_data->description)
        );
        $this->data['amount'] = array(
            'name' => 'amount',
            'id' => 'amount',
            'type' => 'text',
            'class' => 'form-control requiredTextField only_numeric',
            'field-name' => "jumlah pengeluaran",
            'placeholder' => "jumlah pengeluaran",
            'value' => $this->form_validation->set_value('amount', $form_data->amount)
        );
        $general_expenses = $this->petty_cash_model->get_general_expenses();
        $this->data['ge_id'] = $general_expenses;
        $this->data['selected'] = $form_data->ge_id;

        $this->data['content'] .= $this->load->view('petty_cash_edit_v.php', $this->data, true);
        $this->render('cashier');
    }

    public function delete()
    {
        $id = $this->uri->segment(3);
        $type_id = $this->uri->segment(4);


        if (empty($id)) redirect('petty_cash');
        $form_data = $this->petty_cash_model->get_one('petty_cash', $id);        
        if (empty($form_data)) redirect('petty_cash');

        $result = $this->petty_cash_model->delete('petty_cash', $id);

        if ($result) {
            $this->session->set_flashdata('message_success', 'Berhasil menghapus data');
        } else {
            $this->session->set_flashdata('message', 'Error. Gagal menghapus data');
        }

        redirect('petty_cash', 'refresh');
    }

}