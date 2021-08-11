<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/16/2014
 * Time: 10:53 AM
 */
class Tax_model extends MY_Model
{
	function __construct()
	{
		parent::__construct();
	}

	public function get_taxes($order_type, $tax_method, $is_active = FALSE)
	{
		$this->db->select('ot.id, ot.tax_id, t.tax_percentage, t.account_id, t.tax_name, t.is_service, ot.is_active')
				 ->from('order_taxes ot')
				 ->join('taxes t', 't.id = ot.tax_id')
				 ->where('ot.order_type', $order_type);
		if ($is_active) {
			$this->db->where('ot.is_active', $is_active);
		}
		if ($tax_method == 2) {
			$this->db->order_by('t.is_service', 'desc');
		}
		$query = $this->db->get();
		$result = $query->result();

		return $result;
	}
}