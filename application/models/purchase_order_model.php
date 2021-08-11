<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      Diky Pratansyah <pratansyah@gmail.com>
 * @copyright   2015 Digital Oasis
 * @since       2.0.0
 */

class Purchase_Order_Model extends MY_Model{

    /**
     * Table's name to be used throughout the model
     * @var string
     */
    private $_table = 'purchase_order';

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Add new detail transfer request(s)
     * @param   mix     $data    Detail transfer request to be added
     * @return  boolean          Status whether success of failed
     */
    public function add($data)
    {
        if (count($data) == count($data, COUNT_RECURSIVE)) return $this->save($this->_table, $data);
        else return $this->db->insert_batch($this->_table, $data);
    }
    public  function get_max_po_number()
    {
        $this->db->select_max('number');
        $this->db->from('purchase_order');
        $this->db->limit(1);

        return $this->db->get()->row()->number;
    }

    public  function get_max_payment_number()
    {
        $this->db->select_max('payment_no');
        $this->db->from('purchase_order_receive');
        $this->db->limit(1);

        return $this->db->get()->row()->payment_no;
    }

    /**
     * Get stock transfer data with spesific condition
     * @param  mixed $cond Condition for query in a form of array where each key is the field's name and the value is the condition
     * @return mixed       Array of stock transfer(s) which satisfy the condition
     */
    public function get($cond=array())
    {
        $this->db->select($this->_table.'.*, supplier.name,sum(por.total-por.discount) as total_po, por.invoice_logo, por.discount',false)
        ->from($this->_table)
        ->join('supplier', 'supplier.id = '.$this->_table.'.supplier_id')
        ->join('purchase_order_receive por',$this->_table.".id=por.purchase_order_id",'left')
        ->where($cond)
        ->group_by($this->_table.".id");
        return $this->db->get()->result();
    }

    public function update($data=array(), $cond=array())
    {
        return $this->update_where($this->_table, $data, $cond);
    }
    
    public function delete($cond)
    {
        return $this->delete_by_where($this->_table, $cond);
    }

    public function get_all($cond)
    {
        $this->db->select('*')
        ->from($this->_table)
        ->where($cond);
        return $this->db->get()->result();
    }

}