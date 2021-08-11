<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
* 	Created by : Moh Tri R
*	Created at : Wednesday, December 07, 2016
*/
class Migrate_refund_data extends Admin_Controller
{
	
	function __construct() {
		parent::__construct();
		if (!$this->ion_auth->logged_in()) { 
            redirect(SITE_ADMIN . '/login', 'refresh');
        } else {
        	$this->load->model('bill_model');
        	$this->_setting = $this->data['setting'];
        }		
	}

	function index() {
		$old_bill 				  		= array();
		$old_bill_courier_service 		= array();
		$old_bill_information 	  		= array();
		$old_bill_menu 			  		= array();
		$old_bill_menu_inventory_cogs	= array();
		$old_bill_menu_side_dish		= array();
		$old_bill_payment				= array();

		$get_refund = $this->bill_model->get_all_where('refund', array('data <>' => ''));
		foreach ($get_refund as $key => $value) {
			$data = json_decode($value->data);

			$old_bill_courier_service 	  = $data->bill_courier_service;
			if (!empty($old_bill_courier_service)) {
				unset($old_bill_courier_service->id);
				unset($old_bill_courier_service->bill_id);
			}	

			$old_bill_information 		  = $data->bill_information;
			if (!empty($old_bill_information)) {
				unset($old_bill_information->id);
				unset($old_bill_information->bill_id);
			}

			$old_bill_payment			  = $data->bill_payment;
			if (!empty($old_bill_payment)) {
				unset($old_bill_payment->id);
				unset($old_bill_payment->bill_id);
			}

			$old_bill_menu 				  = $data->bill_menu;
			if (!empty($old_bill_menu)) {
				unset($old_bill_menu->id);
				unset($old_bill_menu->bill_id);
			}

			$old_bill_menu_inventory_cogs = $data->bill_menu_inventory_cogs;
			if (!empty($old_bill_menu_inventory_cogs)) {
				unset($old_bill_menu_inventory_cogs->id);
				unset($old_bill_menu_inventory_cogs->bill_menu_id);
			}

			$old_bill_menu_side_dish = $data->bill_menu_side_dish;
			if (!empty($old_bill_menu_side_dish)) {
				unset($old_bill_menu_side_dish->id);
				unset($old_bill_menu_side_dish->bill_menu_id);
			}

			$old_bill = $data->bill;
			unset($old_bill->id);

			$refund_key = md5($this->data['setting']['store_id'] . $old_bill->order_id . $old_bill->receipt_number . $old_bill->created_at);
			$old_bill->has_synchronized = 0;
			$old_bill->is_refund = 1;
			$old_bill->refund_key = $refund_key;

			$id = $this->bill_model->save('refund', array('refund_key' => $refund_key, 'data' => NULL), $value->id);

			if ($id) {
				$params['refund_key'] = $refund_key;
				$params['refund_date'] = $value->created_at;
				$params['data_bill'] = $old_bill;
				$params['data_bill_courier_service'] = $old_bill_courier_service;
				$params['data_bill_information'] = $old_bill_information;
				$params['data_bill_menu'] = $old_bill_menu;
				$params['data_bill_payment'] = $old_bill_payment;
				$this->saveBill($params);
			}
		}
		redirect(SITE_ADMIN . '/');
	}

	function saveBill($params = array()) {
		$id = $this->bill_model->save('bill', $params['data_bill']);

		if ($id > 0) {
			$get_new_bill = $this->bill_model->get_all_where('bill', array('order_id' => $params['data_bill']->order_id, 'payment_date' => $params['refund_date']));
			$new_bill = $get_new_bill[0];
			$update_bill = $this->bill_model->save('bill', array('refund_key' => $params['refund_key'], 'has_synchronized' => 0), $new_bill->id);

			if (!empty($params['data_bill_courier_service'])) {
				foreach ($params['data_bill_courier_service'] as $key => $value) {
					unset($value->id);
					$value->bill_id = $id;
					$bill_courier_service = $this->bill_model->save('bill_courier_service', $value);
				}
			}

			foreach ($params['data_bill_information'] as $key => $value) {
				unset($value->id);
				$value->bill_id = $id;
				$bill_information = $this->bill_model->save('bill_information', $value);
			}

			foreach ($params['data_bill_payment'] as $key => $value) {
				unset($value->id);
				$value->bill_id = $id;
				$bill_payment = $this->bill_model->save('bill_payment', $value);
			}

			foreach ($params['data_bill_menu'] as $key => $value) {
				unset($value->id);
				$value->bill_id = $id;
				$bill_menu_id = $this->bill_model->save('bill_menu', $value);
			}
		}
	}
}