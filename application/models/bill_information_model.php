<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      Diky Pratansyah <pratansyah@gmail.com>
 * @copyright   2015 Digital Oasis
 * @since       2.0.0
 */

class Bill_Information_Model extends MY_Model{

    /**
     * Table's name to be used throughout the model
     * @var string
     */
    private $_table = 'bill_information';

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

    public function get_all($cond)
    {
        $this->db->select('*')
        ->from($this->_table)
        ->where($cond);
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