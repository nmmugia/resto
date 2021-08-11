<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
* 	Created by : Moh Tri Ramdhani
*	Created at : 2016-10-14
*/
class Balance_Cash extends Cashier_Controller
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('petty_cash_model');
		$this->load->model('account_data_model');
		$this->groups_access->check_feature_access('petty_cash');
	}

	/* function for adding data to balance_cash_history table */
	public function add()
	{
		$this->data['title'] = "Saldo";
		$this->data['subtitle'] = "Tambah Data";
		
		/* validation rules for description and amount */
		$this->form_validation->set_rules('description', 'Deskripsi Penambah', 'required');
		$this->form_validation->set_rules('amount', 'Jumlah Penambah', 'required|numeric');

		if ($this->form_validation->run() == TRUE) {
			$amount 		= $this->input->post('amount');
			$description	= $this->input->post('description');

			// prepare data add
			$data_array = array(
				'user_id'		=> $this->data['user_profile_data']->id,
				'date'			=> date('Y-m-d H:i:s'),
				'description'	=> $description,
				'amount'		=> $amount
			);

			// process insert data to table
			$balance_cash = $this->petty_cash_model->save('balance_cash_history', $data_array);

			if ($balance_cash === false) {
				$this->session->set_flashdata('message', 'Gagal menyimpan data');
			} else {
				if ($this->data['module']['ACCOUNTING'] == 1) {
					// prepare data accounting
					$get_enum_petty_cash = array_pop($this->account_data_model->get_all_where('enum_account_data_entry_type', array('value' => 'petty_cash'), 1));
					$array = array(
						'store_id'		=> $this->data['setting']['store_id'],
						'entry_type'	=> $get_enum_petty_cash->id,
						'foreign_id'	=> $balance_cash,
						'account_id'	=> $this->data['setting']['petty_cash_account_id'],
						'debit'			=> 0,
						'credit'		=> $amount,
						'info'			=> 'Penambahan Saldo Kas Kecil',
						'created_at'	=> date('Y-m-d H:i:s'),
						'created_by'	=> $this->data['user_profile_data']->id,
						'modified_at'	=> date('Y-m-d H:i:s'),
						'modified_by'	=> $this->data['user_profile_data']->id
					);

					// insert into table account data and detail
					$account_data = $this->account_data_model->add($array);

					if ($account_data) {
						$account_data_detail = array(
							'account_data_id'	=> $account_data,
							'store_id'			=> $this->data['setting']['store_id'],
							'info'				=> $this->data['setting']['store_id'] . time(),
							'description'		=> $description,
							'created_at'		=> date('Y-m-d H:i:s')
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
				redirect('balance_cash/add', 'refresh');
			}
		} else {
			$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
			$this->data['message_success'] = $this->session->flashdata('message_success');

			$this->data['description'] = array(
				'name'			=> 'description',
				'id'			=> 'description',
				'type'			=> 'text',
				'class'			=> 'form-control requiredTextField only_alpha_numeric',
				'field-name'	=> 'deskrpisi penambah',
				'placeholder'	=> 'deskripsi penambah',
				'value'			=> $this->form_validation->set_value('description')
			);

			$this->data['amount'] = array(
				'name' => 'amount',
                'id' => 'amount',
                'type' => 'text',
                'class' => 'form-control requiredTextField only_numeric',
                'field-name' => 'jumlah penambah',
                'placeholder' => 'jumlah penambah',
                'value' => $this->form_validation->set_value('amount')
			);

			$this->data['content'] .= $this->load->view('balance_cash_add_v', $this->data, true);
			$this->render('cashier');
		}
	}

	/* function for editing data at balance_cash_history table */
	public function edit($id = 0)
	{
		if (empty($id)) redirect('petty_cash');

		// get balance cash data
		$form_data = $this->petty_cash_model->get_one('balance_cash_history', $id);
		if (empty($form_data)) {
			redirect('petty_cash');
			$this->session->set_flashdata('message', 'Error. Data tidak ditemukan');
		}

		$this->data['form_data'] = $form_data;
		$this->data['title'] = "Saldo";
		$this->data['subtitle'] = "Edit Data";

		// set validation rules
		$this->form_validation->set_rules('description', 'Deskripsi Penambah', 'required');
		$this->form_validation->set_rules('amount', 'Jumlah Penambah', 'required|numeric');

		if ($this->form_validation->run() === TRUE) {
			$amount 		= $this->input->post('amount');
			$description	= $this->input->post('description');

			$data_array = array(
				'user_id'		=> $this->data['user_profile_data']->id,
				'date'			=> date('Y-m-d H:i:s'),
				'description'	=> $description,
				'amount'		=> $amount
			);

			// process update data to table
			$balance_cash = $this->petty_cash_model->save('balance_cash_history', $data_array, $id);

			if ($balance_cash === false) {
				$this->session->set_flashdata('message', 'Gagal menyimpan data');
			} else {
				if ($this->data['module']['ACCOUNTING'] == 1) {						
					// prepare data accounting
					$get_enum_petty_cash = array_pop($this->account_data_model->get_all_where('enum_account_data_entry_type', array('value' => 'petty_cash'), 1));

					// delete old data
					$account_data_olds = $this->account_data_model->get_all_where('account_data', array('entry_type' => $get_enum_petty_cash->id, 'foreign_id' => $id));
					foreach ($account_data_olds as $key) {
						$this->account_data_model->delete_by_limit('account_data_detail', array('account_data_id' => $key->id), 0);
					}
					$this->account_data_model->delete_by_limit('account_data', array('entry_type' => $get_enum_petty_cash->id, 'foreign_id' => $id), 0);

					$array = array(
						'store_id'		=> $this->data['setting']['store_id'],
						'entry_type'	=> $get_enum_petty_cash->id,
						'foreign_id'	=> $balance_cash,
						'account_id'	=> $this->data['setting']['petty_cash_account_id'],
						'debit'			=> 0,
						'credit'		=> $amount,
						'info'			=> 'Penambahan Saldo Kas Kecil',
						'created_at'	=> date('Y-m-d H:i:s'),
						'created_by'	=> $this->data['user_profile_data']->id,
						'modified_at'	=> date('Y-m-d H:i:s'),
						'modified_by'	=> $this->data['user_profile_data']->id
					);

					// insert into table account data and detail
					$account_data = $this->account_data_model->add($array);

					if ($account_data) {
						$account_data_detail = array(
							'account_data_id'	=> $account_data,
							'store_id'			=> $this->data['setting']['store_id'],
							'info'				=> $this->data['setting']['store_id'] . time(),
							'description'		=> $description,
							'created_at'		=> date('Y-m-d H:i:s')
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
				redirect('balance_cash/edit/' . $id, 'refresh');
			}
		} else {
			$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
			$this->data['message_success'] = $this->session->flashdata('message_success');

			$this->data['description'] = array(
				'name'			=> 'description',
				'id'			=> 'description',
				'type'			=> 'text',
				'class'			=> 'form-control requiredTextField only_alpha_numeric',
				'field-name'	=> 'deskrpisi penambah',
				'placeholder'	=> 'deskripsi penambah',
				'value'			=> $this->form_validation->set_value('description', $form_data->description)
			);

			$this->data['amount'] = array(
				'name' => 'amount',
                'id' => 'amount',
                'type' => 'text',
                'class' => 'form-control requiredTextField only_numeric',
                'field-name' => 'jumlah penambah',
                'placeholder' => 'jumlah penambah',
                'value' => $this->form_validation->set_value('amount', $form_data->amount)
			);

			$this->data['content'] .= $this->load->view('balance_cash_edit_v', $this->data, true);
			$this->render('cashier');			
		}
	}

	/* function for deleting data from balance_cash_history table */
	public function delete($id = 0)
	{
		if (empty($id)) redirect('petty_cash');

		// get balance cash data
		$form_data = $this->petty_cash_model->get_one('balance_cash_history', $id);
		if (empty($form_data)) {
			redirect('petty_cash');
			$this->session->set_flashdata('message', 'Error. Data tidak ditemukan');
		} else {
			// delete from table
			$result = $this->petty_cash_model->delete('balance_cash_history', $id);

			if ($result) {
				$this->session->set_flashdata('message_success', 'Berhasil menghapus data');
			} else {
				$this->session->set_flashdata('message', 'Error. Gagal menghapus data');
			}
		}
		redirect('petty_cash', 'refresh');
	}
}