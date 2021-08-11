<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      Diky Pratansyah <pratansyah@gmail.com>
 * @copyright   2015 Digital Oasis
 * @since       2.0.0
 */

class Sync_Model extends MY_Model{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Add data from client
     * @param mixed $data  Row(s) of data
     * @param string $table Table's name
     */
    public function add($data, $table)
    {
        if (count($data) == count($data, COUNT_RECURSIVE)) return (bool)$this->save($table, array_pop($data));
        else return $this->db->insert_batch($table, $data);
    }


    /**
     * Delete data that has been previously inserted but some of them failed
     * @param  string   $table Table's name
     * @param  mixed    $ids   Ids that will be deleted
     * @return boolean
     */
    public function delete($table, $ids)
    {
        $this->where_in('id', $ids);
        return $this->db->delete($table);
    }

    public function update_sync($table, $data, $ids)
    {
        $this->db->where_in('id', $ids);
        return $this->db->update($table, $data);
    }

}