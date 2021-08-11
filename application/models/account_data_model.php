<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      Diky Pratansyah <pratansyah@gmail.com>
 * @copyright   2015 Digital Oasis
 * @since       2.0.0
 */

class Account_Data_Model extends MY_Model{

    /**
     * Table's name to be used throughout the model
     * @var string
     */
    private $_table = 'account_data';

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Add new data
     * @param   mix     $data    Detail transfer request to be added
     * @return  boolean          Status whether success of failed
     */
    public function add($data) {
        if (count($data) == count($data, COUNT_RECURSIVE)){
            $this->db->insert($this->_table, $data);
            $insert_id = $this->db->insert_id();

            return  $insert_id;
        }else{
            return $this->db->insert_batch($this->_table, $data);
        }
    }

    public function get_all($cond)
    {
        $this->db->select('*')
        ->from($this->_table)
        ->where($cond);
        return $this->db->get()->result();
    }

    public function get_all_no_id($cond)
    {
        $this->db->select('store_id, entry_type, foreign_id, account_id, debit, credit, info, status_ar, created_at, created_by, modified_at, modified_by')
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
	
    public function add_detail($data) {
        if (count($data) == count($data, COUNT_RECURSIVE))
            return $this->db->insert('account_data_detail', $data);
        else
            return $this->db->insert_batch('account_data_detail', $data);
    }

    public function get_all_detail($cond){
        $this->db->select('*')
        ->from('account_data_detail')
        ->where($cond);
        return $this->db->get()->result();        
    }

}
