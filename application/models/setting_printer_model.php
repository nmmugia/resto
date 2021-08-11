<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @Author: Fitria Kartika
 * @Date:   2015-10-12 14:28:19
 * @Last Modified by:   Fitria Kartika
 * @Last Modified time: 2015-10-22 15:50:36
 */
class Setting_printer_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    function get_printer_dropdown($is_add){
        $data  = $this->get_all_where('enum_printer_type');
        $result      = array();
        $is_add ? $result['0'] = "Pilih Tipe Printer" : $result['0'] = "All Printer";
        // $result['0'] = "All Printer";
        foreach ($data as $row) {
            $result[$row->id] = $row->value;
        }

        return $result;
    }

    function get_outlet_dropdown($store_id){
        $data  = $this->get_all_where('outlet', array('store_id' => $store_id) );
        $result      = array();
        $result['0'] = "Pilih ".$this->lang->line('outlet_title');
        foreach ($data as $row) {
            $result[$row->id] = $row->outlet_name;
        }

        return $result;
    }


    function get_non_selected_tables($printer_id, $selected = FALSE){
        if(!$selected){
            $selected = $this->get_selected_tables($printer_id);
        }
        $selected_temp =array(0);
        foreach ($selected as $key => $row) {
            $selected_temp[] = $row->id;
        }
        $qry = $this->db->select('a.id as id, a.table_name, b.id as setting_printer_table_id');
        $qry->distinct();
        $qry->from('table a');
        $qry->join('setting_printer_table b', 'b.table_id = a.id' ,'left');
        $qry->where_not_in('a.id', $selected_temp);
        return $qry->get()->result();
    }

    function get_selected_tables($printer_id){

        $qry = $this->db->select('a.id as id, a.table_name, b.id as setting_printer_table_id');
        $qry->from('table a');
        $qry->join('setting_printer_table b', 'b.table_id = a.id' );
        $qry->where('b.printer_id', $printer_id);

        return $qry->get()->result();
    }

    function get_printer_by_enum_printer_type($where = false){

        $qry = $this->db->select('a.*');
        $qry->from('setting_printer a');
        $qry->join('enum_printer_type b', 'b.id = a.type' );

        if ($where) {
            $qry->where($where);
        }
        

        return $qry->get()->result();
    }

 }