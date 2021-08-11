<?php

class Table_model extends MY_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function get_enum_table_status(){
    	$data = array();
    	$enum = $this->get('enum_table_status')->result();

    	foreach ($enum as $key => $row) {
    		$data[$row->id] = $row->status_name;
    	}

    	return $data;
    }


    public function get_data_all_table($where = '')
    {
        $this->db->select(
            'table.id,
            table.id as table_id,
            table.table_name,
            table.table_status,
            table.customer_count,
            table.table_shape,
            enum_table_status.status_name,
            floor.floor_name'
            )
        ->from('table')
        ->join('enum_table_status','table.table_status = enum_table_status.id')
        ->join('floor','floor.id = table.floor_id')
        ->where('table.is_active', 1);
        if($where!=""){
          $this->db->where($where);
        }
        $this->db->order_by('floor_name', 'asc')
        ->order_by('table_name * 1', 'asc',false);
        return $this->db->get()->result();
    }

    public function get_table_merge($table_id)
    {
        $this->db->select('*')
        ->from('table_merge')
        ->where("parent_id = '$table_id' OR table_id='$table_id'");
        return $this->db->get()->result();
    }

    public function delete_table_merge($table_id)
    {
        $this->db->where('parent_id', $table_id);
        return $this->db->delete('table_merge');
    }

    public function get_printer_checker_dropdown()
    {
        $this->db->select('*');
        $this->db->from('master_general_setting');
        $this->db->where('name','printer_checker');
        $query = $this->db->get();
        $data  = $query->result();

        $results = array();
        if(sizeof($data) > 0){
            $results = json_decode($data[0]->value);
            // foreach ($temp->value as $row) {
            //     $results[$row] = $row;
            // }
        }

        return $results;
    }

    public function get_table($table_id){
        $this->db->select(
            'table.*'
            )
        ->from('table')
        ->where('table.id', $table_id);
        return $this->db->get()->row();
    }

}

?>