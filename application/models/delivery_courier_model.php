<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Delivery_courier_model extends MY_Model {
	function __construct() {
		parent::__construct();
	}

	function get_courier_lists($where = false) {
		$query = "SELECT delivery_courier.id, 1 as is_percentage, commission as delivery_cost, concat(delivery_company.company_name, ' - ', courier_name) as delivery_cost_name FROM delivery_courier
					JOIN delivery_company ON delivery_company.id = delivery_courier.delivery_company_id
					WHERE delivery_courier.is_active = 1";

		if ($where) {
			$query .= " AND delivery_courier.id = ".$where;
		}
		$result = $this->db->query($query);
		return $result;
	}
}