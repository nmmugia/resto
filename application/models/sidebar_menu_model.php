<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @Author: Fitria Kartika
 * @Date:   2015-10-12 14:28:19
 * @Last Modified by:   Fitria Kartika
 * @Last Modified time: 2015-10-22 15:50:36
 */
class Sidebar_menu_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    function get_sidebar_menu($cond=FALSE){

        $where = "";
        if($cond){
            $first_elem = false;
            foreach ($cond as $field => $value) {
                if(!$first_elem) {
                    $where .= " WHERE $field=$value ";
                }
                else {
                    $where .= " AND $field=$value ";
                }
                $first_elem = true;
            }
        }

        $qry = 
        "SELECT a.id, a.name, a.parent_id, a.url, a.sequence,
        GROUP_CONCAT(g.name separator ', ') as groups_access
        from sidebar_menu a 
        LEFT JOIN sidebar_menu_access b on b.sidebar_menu_id = a.id
        LEFT JOIN groups g on g.id = b.groups_id
        $where
        GROUP BY a.id
        ORDER BY a.sequence ASC
        ";

        return $this->db->query($qry)->result();

    }

    function get_sidebar_menu_dropdown($parent_id=0){
        $order_by = array('name', 'asc');
        $data  = $this->get_all_where('sidebar_menu', array('parent_id'=> $parent_id), $limit= FALSE, $order_by);
        $result      = array();
        $result['0'] = "Root";
        foreach ($data as $row) {
            $result[$row->id] = $row->name;
        }

        return $result;
    }


    function get_non_selected_access($sidebar_menu_id, $selected = FALSE){
        if(!$selected){
            $selected = $this->get_selected_access($sidebar_menu_id);
        }
        $selected_temp =array(0);
        foreach ($selected as $key => $row) {
            $selected_temp[] = $row->group_id;
        }
        $qry = $this->db->select('a.*');
        $qry->distinct();
        $qry->from('groups a');
        $qry->join('sidebar_menu_access b', 'b.groups_id = a.id' ,'left');
        $qry->where_not_in('a.id', $selected_temp);
        return $qry->get()->result();
    }

    function get_selected_access($sidebar_menu_id){

        $qry = $this->db->select('a.*, a.id as group_id, b.sidebar_menu_id, b.id as sidebar_menu_access_id');
        $qry->from('groups a');
        $qry->join('sidebar_menu_access b', 'b.groups_id = a.id' );
        $qry->where('b.sidebar_menu_id', $sidebar_menu_id);

        return $qry->get()->result();
    }

    function get_sequence($parent_id = 0){
        $this->db->select('max(sequence) as sequence')
        ->from('sidebar_menu')
        ->where('parent_id', $parent_id);
        $result = $this->db->get()->row();

        if($result){
            $result = $result->sequence+1;
        }else{
            $result = 1;
        }
        return $result;

    }


    function get_previous_record($sequence, $parent_id){
        $qry = 
        "SELECT  * 
        from sidebar_menu
        where parent_id = $parent_id
        and sequence = 
        (select
          max(sequence) from 
          sidebar_menu
          where parent_id = $parent_id
          and
          sequence < $sequence
          ) 
        ";
        return $this->db->query($qry)->row();

    }

    function get_next_record($sequence, $parent_id){

        $qry = 
        "SELECT  * 
        from sidebar_menu
        where parent_id = $parent_id
        and sequence = 
        (select
          min(sequence) from 
          sidebar_menu
          where parent_id = $parent_id
          and
          sequence > $sequence
          ) 
    ";
    return $this->db->query($qry)->row();

    }

    function get_groups_menu($group_id){
        $qry = $this->db->select('
            a.id as sidebar_menu_access_id,
            b.id as sidebar_menu_id,
            b.*

            ');
        $qry->from('sidebar_menu_access a');
		
        $qry->join('sidebar_menu b', 'a.sidebar_menu_id = b.id' );
		$qry->join('module m', 'm.id = b.module_id' );
        $qry->where('a.groups_id', $group_id);
		$qry->where('m.is_installed',"1");
        $qry->order_by('b.sequence');

        return $qry->get()->result();
    }

    public function set_session($menu_access)
    {
        $session_data['menu_access'] = $menu_access;
        $this->session->set_userdata($session_data);
        return TRUE;
    }

    function test(){
        $qry = 'select * 
        from sidebar_menu a
        where a.id  not in (select sidebar_menu_id from sidebar_menu_access  )';
        $result= $this->db->query($qry)->result();
        foreach ($result as $key => $row) {
            $data_save = array(
                'sidebar_menu_id' => $row->id,
                'groups_id' => 1,
                );
            $this->sidebar_menu_model->save('sidebar_menu_access', $data_save);
        }

    }
 }