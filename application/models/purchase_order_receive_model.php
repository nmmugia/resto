<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      Diky Pratansyah <pratansyah@gmail.com>
 * @copyright   2015 Digital Oasis
 * @since       2.0.0
 */

class Purchase_Order_Receive_Model extends MY_Model{

    /**
     * Table's name to be used throughout the model
     * @var string
     */
    private $_table = 'purchase_order_receive';

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


    /**
     * Get stock transfer data with spesific condition
     * @param  mixed $cond Condition for query in a form of array where each key is the field's name and the value is the condition
     * @return mixed       Array of stock transfer(s) which satisfy the condition
     */
    public function get($cond=array())
    {
        $this->db->select('*')
        ->from($this->_table)
        ->where($cond);
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

    public function get_history($id) {
        $query = $this->db->query('select COALESCE(SUM(amount), 0) AS total FROM purchase_order_payment_history WHERE purchase_order_receive_id = '.$id['purchase_order_receive_id']);

        return $query->result();
    }

}