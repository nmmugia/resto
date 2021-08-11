<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Purchase_Order_Retur_Detail_Model extends MY_Model{

    /**
     * Table's name to be used throughout the model
     * @var string
     */
    private $_table = 'purchase_order_retur_detail';

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
        $this->db->select($this->_table.'.*, i.name, i.unit,pod.inventory_id,pod.uom_id,u.code,i.uom_id as origin_uom_id,u2.code as origin_code')
        ->from($this->_table)
        ->join('purchase_order_receive_detail pord', $this->_table.'.purchase_order_receive_detail_id = pord.id')
        ->join('purchase_order_detail pod', 'pord.purchase_order_detail_id = pod.id')
        ->join('uoms u',"pod.uom_id=u.id","left")
        ->join('inventory i', 'pod.inventory_id = i.id')
        ->join('uoms u2',"i.uom_id=u2.id","left")
        ->where($cond)->order_by("pod.id","asc");
        return $this->db->get()->result();
    }

    public function get_previous($cond=array())
    {
        // $cond = array_merge($cond, array('purchase_order_receive_detail.purchase_order_detail_id' => 'purchase_order_detail.id'));
        $this->db->select('sum(received_quantity) as previous')
        ->from('purchase_order_receive_detail')
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

    public function get_received($cond=array()) {
        $this->db->select('pord.id, pod.inventory_id, i.name, SUM(pord.received_quantity) AS quantity, (SELECT SUM(pored.retur_quantity)
            FROM purchase_order_retur_detail pored
            WHERE pored.purchase_order_receive_detail_id = pord.id) AS retur_quantity, u.code, pord.price')
        ->from('purchase_order_receive_detail pord')
        ->join('purchase_order_detail pod', 'pod.id = pord.purchase_order_detail_id')
        ->join('inventory i', 'i.id = pod.inventory_id')
        ->join('uoms u', 'u.id = pod.uom_id')
        ->where($cond)
        ->group_by(array('pod.inventory_id', 'u.code'))
        ->order_by('pord.id', 'asc');
        return $this->db->get()->result();
    }

}