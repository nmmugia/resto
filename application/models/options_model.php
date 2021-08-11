<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/16/2014
 * Time: 3:55 PM
 */
class Options_model extends MY_Model
{

    /**
     *
     */
    function __construct()
    {
        parent::__construct();
    }

    function save_side_dish($option)
    {
        if (isset($option['id'])) {
            $this->db->where('id', $option['id']);
            $this->db->update('menu_side_dish', $option);
            $id = $option['id'];
        }
        else {
            $this->db->insert('menu_side_dish', $option);
            $id = $this->db->insert_id();
        }

        return $id;
    }

    function save_options($option, $values)
    {
        if (isset($option['id'])) {
            $this->db->where('id', $option['id']);
            $this->db->update('menu_option', $option);
            $id = $option['id'];

            //eliminate existing options
            $this->delete_option_values($id);
        }
        else {
            $this->db->insert('menu_option', $option);
            $id = $this->db->insert_id();
        }

        //add options to the database
        $sequence = 1;
        foreach ($values as $value) {
            $value['option_id'] = $id;
            $value['sequence']  = $sequence;
            $sequence++;

            $this->db->insert('menu_option_value', $value);
        }

        return $id;
    }

    function clear_options($table = 'menu_side_dish', $opt_id)
    {
        // get the list of side dish
        $list = $this->db->where('menu_id', $opt_id)->get($table)->result();

        foreach ($list as $opt) {
            $this->delete_option($table, $opt->id);
        }
    }

    function delete_option($table = 'menu_side_dish', $id)
    {
        $this->db->where('id', $id);
        $this->db->delete($table);

        if ($table == 'menu_option') {
            $this->delete_option_values($id);
        }
    }

    function delete_option_values($id)
    {
        $this->db->where('option_id', $id);
        $this->db->delete('menu_option_value');
    }

    function get_option_values($option_id)
    {
        $this->db->where('option_id', $option_id);
        $this->db->order_by('sequence', 'ASC');

        return $this->db->get('menu_option_value')->result();
    }

    function get_options($id)
    {
        $this->db->where('menu_id', $id);
        $this->db->order_by('sequence', 'ASC');

        $result = $this->db->get('menu_option');

        $return = array();
        foreach ($result->result() as $option) {
            $option->values = $this->get_option_values($option->id);
            $return[]       = $option;
        }

        return $return;
    }

    function get_side_dish($id, $is_promo)
    {
        if ($is_promo == 1) {
            $this->db->where('parent_menu_id', $id);
            $this->db->order_by('sequence', 'ASC');
            $result = $this->db->get('menu_promo_side_dish');
        } else {
            $this->db->where('menu_id', $id);
            $this->db->order_by('sequence', 'ASC');
            $result = $this->db->get('menu_side_dish');
        }

        $return = array();
        foreach ($result->result() as $option) {
            $return[] = $option;
        }

        return $return;
    }

    function count_option_value($id)
    {
        $this->db->where('menu_id', $id);
        $this->db->order_by('sequence', 'ASC');

        $result = $this->db->get('menu_option');

        $return = 0;
        foreach ($result->result() as $option) {
            $return += (count($this->get_option_values($option->id)));
        }

        return $return;
    }

    function get_all_menus_by_outlet_category($store_id, $outlet_id = 0, $category_id = 0)
    {
        $this->db->select('menu.*,category.outlet_id');
        $this->db->from('menu');
        $this->db->join('category', 'category.id = menu.category_id');
        $this->db->join('outlet', 'outlet.id = category.outlet_id');
        $this->db->join('store', 'store.id = outlet.store_id');
        $this->db->where('store.id', $store_id);
        if ($outlet_id > 0) {
            $this->db->where('outlet.id', $outlet_id);
        }

        if ($category_id > 0) {
            $this->db->where('category.id', $category_id);
        }
        $this->db->order_by('category.id', 'DESC');

        $result = $this->db->get()->result();
        $return = array();
        foreach ($result as $menu) {
            $menu->menu_option    = $this->get_options($menu->id);
            $menu->menu_side_dish = $this->get_side_dish($menu->id, $menu->is_promo);
            $return[]             = $menu;
        }

        return $return;
    }
    
    function get_menu_ingredient($menu_id){
       $this->db->select('inventory.name,uoms.name as unit, menu_ingredient.quantity')
        ->from('menu_ingredient')
        ->join('inventory','inventory.id = menu_ingredient.inventory_id')
        ->join('uoms','uoms.id = menu_ingredient.uom_id')
        ->where('menu_ingredient.menu_id', $menu_id);
        $this->db->order_by('sequence', 'ASC');
        
        
        return $this->db->get()->result();
    }

}