<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
* 	Created by : Moh Tri R
*	Created at : Tuesday, October 25, 2016
*/
class Update_transfer_history extends Admin_Controller
{
	
	function __construct() {
		parent::__construct();
		$this->load->model('stock_model');
		$this->load->model('inventory_model');
	}

	function index() {
		$this->db->select("*")
				 ->from("stock_transfer_history")
				 ->group_by(array("origin_outlet_id", "destination_outlet_id", "date_format(created_at, '%Y-%m-%d %H:%i')"));
		$get_transfer_history = $this->db->get()->result();
		$this->insert_stock_transfer($get_transfer_history);
		
		redirect(base_url(), 'refresh');
	}

	function insert_stock_transfer($get_transfer_history) {
		foreach ($get_transfer_history as $key) {
			$data_stock_transfer = array(
				'origin_outlet_id' => $key->origin_outlet_id,
				'destination_outlet_id' => $key->destination_outlet_id,
				'created_at' => $key->created_at,
				'created_by' => $key->created_by
			);
			$id = $this->stock_model->save('stock_transfer', $data_stock_transfer);

			$datetime = strtotime($key->created_at);
			$date = date('Y-m-d H:i', $datetime);

			$this->update_stock_transfer_id($id, $date);			
		}
	}

	function update_stock_transfer_id($id, $date) {
		$this->db->select("*")
				 ->from("stock_transfer_history")
				 ->having("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i') = ", $date);
		$get_history = $this->db->get()->result();

		foreach ($get_history as $key) {
			$data_history = array(
				'stock_transfer_id' => $id
			);
			$this->stock_model->save('stock_transfer_history', $data_history, $key->id);
		}
	}
}