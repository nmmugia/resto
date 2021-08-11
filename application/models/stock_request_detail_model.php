<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      Diky Pratansyah <pratansyah@gmail.com>
 * @copyright   2015 Digital Oasis
 * @since       2.0.0
 */

class Stock_Request_Detail_Model extends MY_Model{

    /**
     * Table's name to be used throughout the model
     * @var string
     */
    private $_table = 'stock_request_detail';

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
        if (count($data) == count($data, COUNT_RECURSIVE)) return $this->db->insert($this->_table, $data);
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

    /**
     * Get inventor(y|ies) detail from stock transfer based on spesific condition
     * @param  mixed $cond The condition to be fulfilled
     * @return mixed       Array of inventor(y|ies) that satisfy the condition
     */
    public function get_items($cond=array())
    {
        // $this->db->select('*, sum(stock.quantity) as sum_quantity, '.$this->_table.'.id as stock_transfer_detail_id')
        // ->from($this->_table)
        // ->join('inventory', 'inventory.id='.$this->_table.'.inventory_id')
        // ->join('stock', 'stock.inventory_id='.$this->_table.'.inventory_id', 'left')
        // ->where($cond)
        // ->group_by('stock.quantity');
        // return $this->db->get()->result();

        $where = '';
        // If no condition supplied then leave where string empty;
        if(count($cond) > 0){
            // Crafting the condition string
            $first_elem = false;
            foreach ($cond as $field => $value) {
                if(!$first_elem) {
                    $where .= "WHERE $field=$value ";
                }
                else {
                    $where .= "AND $field=$value ";
                }
                $first_elem = true;
            }
        }
        $query = "SELECT stock_request_detail.*,inventory.name,u.code,
                (
                  select sum(stock_history.quantity) from stock_history
                  where stock_history.inventory_id=stock_request_detail.inventory_id and stock_request_detail.uom_id=stock_history.uom_id and stock_history.outlet_id=stock_request.supplier_outlet_id
                ) as sum_quantity,
                stock_request_detail.id as stock_transfer_detail_id
                FROM stock_request_detail
                inner join stock_request on stock_request_detail.stock_request_id=stock_request.id
                left join uoms u on stock_request_detail.uom_id=u.id
                JOIN inventory ON inventory.id=stock_request_detail.inventory_id
                $where";
                
        return $this->db->query($query)->result();
    }

    public function update($data=array(), $cond=array())
    {
        return $this->update_where($this->_table, $data, $cond);
    }


}