<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      Diky Pratansyah <pratansyah@gmail.com>
 * @copyright   2015 Digital Oasis
 * @since       2.0.0
 */

class Account_Model extends MY_Model{

    /**
     * Table's name to be used throughout the model
     * @var string
     */
    private $_table = 'account';

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
        if (count($data) == count($data, COUNT_RECURSIVE)) return $this->db->insert($this->_table, $data);
        else return $this->db->insert_batch($this->_table, $data);
    }


    /**
     * Get data with spesific condition
     * @param  mixed $cond Condition for query in a form of array where each key is the field's name and the value is the condition
     * @return mixed       Array of stock transfer(s) which satisfy the condition
     */
    public function get($cond=array())
    {
        $where = '';
        // If no condition supplied then leave where string empty;
        if(count($cond) > 0){
            // Crafting the condition string
            $where = ' AND ';
            foreach ($cond as $field => $value) {
                // If the value is an array then make WHERE IN condition string
                if(is_array($value)){
                    $where .= "$field IN (";
                    foreach ($value as $val) {
                        $where .= $val.", ";
                    }
                    $where = substr($where, 0, -2);
                    $where .= ") ";
                }
                else{
                    $where .= "$field=$value ";
                }
            }
        }

        $query = "  SELECT a.id, a.code, a.name, a.account_type_id, at.name as type_name, a.is_active,
                        IFNULL((SELECT sum(s.id)
                         FROM   supplier s
                         WHERE  s.account_receivable_id=a.id OR 
                                s.account_payable_id=a.id), 0) +
                        IFNULL((SELECT sum(ad.id)
                         FROM   account_data ad
                         WHERE  ad.account_id=a.id), 0) as used
                    FROM account a, account_type at
                    WHERE a.account_type_id=at.id
                    $where";
        return $this->db->query($query)->result();
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
    public function get_data($account_id=0)
    {
      $this->db->select("a.*,at.name as account_type_name,IF(at.debit_multiplier=1,1,0) as default_balance",false)
      ->from("account a")
      ->join("account_type at","a.account_type_id=at.id","inner")
      ->where("a.id",$account_id);
      return $this->db->get()->row();
    }
    public function get_for_beginning_balances()
    {
      $this->db->select("a.*,at.name as account_type_name,IF(count(ad.id)>0,1,0) as is_exists",false)
      ->from("account a")
      ->join("account_type at","a.account_type_id=at.id","inner")
      ->join("account_data ad","a.id=ad.account_id","left")
      ->where("a.is_active",1)
      ->group_by("a.id")
      ->having("count(ad.id) =",0)
      ->order_by("a.account_type_id,a.code","ASC");
      return $this->db->get()->result();
    }

    public function get_account_dropdown(){
        $data = $this->get_all();
    
        $results    = array();
        $results[0]="Pilih Akun";
        foreach ($data as $row) {
            $results[$row->id] = $row->name;
        }

        return $results;

    }
}
