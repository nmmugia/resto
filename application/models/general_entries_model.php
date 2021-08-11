<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      Diky Pratansyah <pratansyah@gmail.com>
 * @copyright   2015 Digital Oasis
 * @since       2.0.0
 */

class General_Entries_Model extends MY_Model{

    /**
     * Table's name to be used throughout the model
     * @var string
     */
    private $_table = 'general_entries';

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Add new data
     * @param   mix     $data    Detail transfer request to be added
     * @return  boolean          Status whether success of failed
     */
    public function add($data)
    {
        if (count($data) == count($data, COUNT_RECURSIVE)) return $this->save($this->_table, $data);
        else return $this->db->insert_batch($this->_table, $data);
    }


    /**
     * Get data with spesific condition
     * @param  mixed $cond Condition for query in a form of array where each key is the field's name and the value is the condition
     * @return mixed       Array of stock transfer(s) which satisfy the condition
     */
    public function get($cond=array())
    {
        
    }

    public function get_all()
    {
        $this->db->select('*')
        ->from($this->_table);
        return $this->db->get()->result();
    }

    /**
     * Update table
     * @param  mixed  $data Data to be set
     * @param  mixed  $cond Condition of searching
     * @return boolean
     */
    public function update($data=array(), $cond=array())
    {
        return $this->update_where($this->_table, $data, $cond);
    }

    public function delete($cond)
    {
        return $this->delete_by_where($this->_table, $cond);
    }

}
