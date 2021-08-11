<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      Diky Pratansyah <pratansyah@gmail.com>
 * @copyright   2015 Digital Oasis
 * @since       2.0.0
 */

class Stock_Transfer_model extends MY_Model{
    
    /**
     * Table's name to be used throughout the model
     * @var string
     */
    private $_table = 'stock_request';

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get list of transfer request(s)
     * @param  mixed $cond 	Condition(s) to be fulfilled
     * @return mixed List of requests
     */

    public function get_transfer_request($cond=array())
    {
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

    	$query = "	SELECT *,
						(SELECT s.store_name
						 FROM store s
						 WHERE sr.supplier_store_id=s.id) as supplier_name,
                        (SELECT s.store_name
                         FROM store s
                         WHERE sr.requester_store_id=s.id) as requester_name,
                        (SELECT outlet_name
                         FROM outlet
                         WHERE sr.requester_outlet_id=outlet.id) as requester_outlet_name,
                         (SELECT outlet_name
                         FROM outlet
                         WHERE sr.supplier_outlet_id=outlet.id) as supplier_outlet_name
					FROM stock_request sr
					$where
					ORDER BY sr.created_at DESC";
    	return $this->db->query($query)->result();
    }

    /**
     * Add new transfer request
     * @param   mix     $obj    Transfer request to be added
     * @return  integer         Id of the newly inserted request
     */
    public function add($obj)
    {

        $this->db->insert($this->_table, $obj);
        $id = $this->db->insert_id();
       
        return $id;
    }

    public function add_batch($obj)
    {
        $result = array();
        foreach ($obj as $row) {
            $result[] = $this->add($row);
        }

        return $result;
    }

    /**
     * Get stock transfer data with spesific condition
     * This method is a wrapper for parent's method get_all_where so that table's name doesn't need to appear in controller
     * @param  mixed $cond Condition for query in a form of array where each key is the field's name and the value is the condition
     * @return mixed       Array of stock transfer(s) which satisfy the condition
     */
    public function get($cond)
    {
        // Check every condition in array $cond
        // If there's an array while checking then use it for condition in where_in clause of sql and delete it
        $new_cond = array();
        foreach ($cond as $key => $value) {
            if(is_array($value)){
                $this->db->where_in($key, $value);
            }
            else $new_cond = array_merge($new_cond, array($key => $value));
        }
        $this->db->where($new_cond);

        return $this->db->get($this->_table)->result();
    }

    public function update($data=array(), $cond=array())
    {
        return $this->update_where($this->_table, $data, $cond);
    }

}