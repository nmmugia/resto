<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Petty_Cash_model extends MY_Model
{
  function __construct()
  {
    parent::__construct();
  }
  public function get_general_expenses()
    {
        $this->db->select('*');
        $this->db->from('general_expenses');
        $this->db->where('store_id', $this->data['setting']['store_id']);
        $this->db->order_by('name', 'ASC');
        $query = $this->db->get();
        $data  = $query->result();

        $results    = array();
        $results[0] = "- Pilih Pengeluaran Umum -";
        foreach ($data as $ge) {
            $results[$ge->id] = $ge->name;
        }

        return $results;
    }
}