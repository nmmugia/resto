<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/16/2014
 * Time: 10:53 AM
 */
class Categories_model extends MY_Model
{

    /**
     *
     */
    function __construct()
    {
        parent::__construct();
    }

    public function get_store()
    {
        $this->db->select('*');
        $this->db->from('store');
        $this->db->order_by('store_name', 'ASC');
        $query = $this->db->get();
        $data  = $query->result();

        $results    = array();
        $results[0] = $this->lang->line('ds_choose_store');
        foreach ($data as $store) {
            $results[$store->id] = $store->store_name;
        }

        return $results;
    }

    public function get_ge_by_store($store_id)
    {
        $this->db->select('*');
        $this->db->from('general_expenses');
        $this->db->join('store', 'store.id = general_expenses.store_id');
        $this->db->where('store.id', $this->data['setting']['store_id']);
        $this->db->order_by('general_expenses.name', 'ASC');

        return $this->db->get()->result();
    }

    public function get_outlet($store_id="",$outlet_and_warehouse=1)
    {
        $this->db->select('outlet.*');
        $this->db->from('outlet');
        if($store_id!=""){
          $this->db->where("store_id",$store_id);
        }
		if($outlet_and_warehouse==0){
          $this->db->where("is_warehouse",0);
		}
        $this->db->order_by('outlet_name', 'ASC');
        $query = $this->db->get();
        $data  = $query->result();

        $results    = array();
        $results[0] = $this->lang->line('ds_choose_outlet');
        foreach ($data as $outlet) {
            $results[$outlet->id] = $outlet->outlet_name;
        }

        return $results;
    }
	public function get_menu_dropdown($id = 0)
    {
        $this->db->select('m.*,c.outlet_id');
        $this->db->from('menu m');
		$this->db->join("category c","m.category_id=c.id");
        $this->db->order_by('menu_name', 'ASC');
        $query = $this->db->get();
        $data  = $query->result();

        $results                         = array();
        $results['0" class="chain_cl_0'] = "Semua Menu";
        foreach ($data as $menu) {
			$results[$menu->id . '" class="chain_cl_0'] = $menu->menu_name;
            if ($id != 0 && $id == $menu->id) {
                $results[$menu->id . '" class="select_me chain_cl_' . $menu->outlet_id] = $menu->menu_name;
            }
            else {
                $results['0" class="chain_cl_' . $menu->outlet_id] = "All";
                $results[$menu->id . '" class="chain_cl_' . $menu->outlet_id] = $menu->menu_name;
            }
        }

        return $results;
    }
    public function get_menu_sd_dropdown($id = 0)
    {
        $this->db->select('m.*');
        $this->db->from('menu m');
        $this->db->order_by('menu_name', 'ASC');
        $query = $this->db->get();
        $data  = $query->result();

        $results                         = array();
        $results[''] = "Semua Menu";
        foreach ($data as $menu) {
            
            $results[$menu->id] = $menu->menu_name;
            
        }

        return $results;
    }
    public function get_floor()
    {
        $this->db->select('floor.*, store_name');
        $this->db->from('floor');
        $this->db->from('outlet');
        $this->db->join('store', 'store.id = floor.store_id');
        $this->db->order_by('store_name', 'ASC');
        $this->db->order_by('floor_name', 'ASC');
        $query = $this->db->get();
        $data  = $query->result();

        $results    = array();
        $results[0] = $this->lang->line('ds_choose_floor');
        foreach ($data as $outlet) {
            $results[$outlet->id] = $outlet->store_name . ' - ' . $outlet->floor_name;
        }

        return $results;
    }

    public function get_category()
    {
        $this->db->select('category.*,outlet_name, store_name');
        $this->db->from('category');
        $this->db->join('outlet', 'outlet.id = category.outlet_id');
        $this->db->join('store', 'store.id = outlet.store_id');
        $this->db->order_by('store_name', 'ASC');
        $this->db->order_by('outlet_name', 'ASC');
        $this->db->order_by('category_name', 'ASC');
        $query = $this->db->get();
        $data  = $query->result();

        $results    = array();
        $results[0] = $this->lang->line('ds_choose_category');
        foreach ($data as $outlet) {
            $results[$outlet->id] = $outlet->store_name . ' - ' . $outlet->outlet_name . ' - ' . $outlet->category_name;
        }

        return $results;
    }

    public function save_menu($columns, $sidedishval = false, $optionsval = false, $id = 0)
    {
        $table = 'menu';

        if ($id > 0) {
            $this->db->where('id', $id);
            $result1 = $this->db->update($table, $columns);
        }
        else {
            $result1 = $this->db->insert($table, $columns);
        }

        if ($result1) {
            $menu_id = ((int)$id == 0) ? $this->db->insert_id() : $id;

            //loop through the side dish and add them to the db
            if ($sidedishval !== false) {
                $obj =& get_instance();
                $obj->load->model('options_model');

                // wipe the slate
                $obj->options_model->clear_options('menu_side_dish', $id);

                // save edited values
                $count = 1;
                foreach ($sidedishval as $side) {
                    $side['menu_id']         = $menu_id;
                    $side['sequence']        = $count;
                    $side['side_dish_hpp']   = floatval($side['side_dish_hpp']);
                    $side['side_dish_price'] = floatval($side['side_dish_price']);

                    $obj->options_model->save_side_dish($side);
                    $count++;
                }
            }

            if ($optionsval !== false) {
                $obj =& get_instance();
                $obj->load->model('options_model');

                // wipe the slate
                $obj->options_model->clear_options('menu_option', $id);

                // save edited values
                $count2 = 1;
                foreach ($optionsval as $option) {
                    $values = $option['values'];
                    unset($option['values']);
                    $option['menu_id']  = $menu_id;
                    $option['sequence'] = $count2;

                    $obj->options_model->save_options($option, $values);
                    $count2++;
                }
            }

            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    public function get_category_by_outlet($outlet_id)
    {
        $this->db->select('*')->from('category')->where('outlet_id', $outlet_id);

        return $this->db->get()->result();
    }

    public function get_menu_by_category($category_id, $filter)
    {
        if ($filter == 'all') {
            $this->db->select('*')->from('menu')->where('category_id', $category_id);
        }
        else {
            $this->db->select('*')->from('menu')->where('category_id', $category_id)->where('available', 1);
        }

        return $this->db->get()->result();
    }

    public function get_outlet_dropdown($id = 0)
    {
        $this->db->select('*');
        $this->db->from('outlet');
        $this->db->order_by('outlet_name', 'ASC');
        $query = $this->db->get();
        $data  = $query->result();

        $results                         = array();
        $results['0" class="chain_cl_0'] = $this->lang->line('ds_choose_outlet');
        foreach ($data as $outlet) {
            if ($id != 0 && $id == $outlet->id) {
                $results[$outlet->id . '" class="select_me chain_cl_' . $outlet->store_id] = $outlet->outlet_name;
            }
            else {
                $results[$outlet->id . '" class="chain_cl_' . $outlet->store_id] = $outlet->outlet_name;
            }
        }

        return $results;
    }
	
    public function get_outlet_dropdown_by_store_id($store_id = 0)
    {
        $this->db->select('*');
        $this->db->from('outlet');
		if($store_id){
			$this->db->where('outlet.store_id', $store_id);
		}
        $this->db->order_by('outlet_name', 'ASC');
        $query = $this->db->get();
        $data  = $query->result();

        $results                         = array();
        $results['0" class="chain_cl_0'] = $this->lang->line('ds_choose_outlet');
        foreach ($data as $outlet) {
            $results[$outlet->id . '" class="chain_cl_' . $outlet->store_id] = $outlet->outlet_name;
        }

        return $results;
    }
	
    public function get_outlet_dropdown2($id = 0)
    {
        $this->db->select('*');
        $this->db->from('outlet');
        $this->db->order_by('outlet_name', 'ASC');
        $query = $this->db->get();
        $data  = $query->result();

        $results                         = array();
        $results['0" class="chain_cl_0'] = $this->lang->line('ds_choose_outlet');
        foreach ($data as $outlet) {
            if ($id != 0 && $id == $outlet->id) {
                $results[$outlet->id . '" class="select_me chain_cl_' . $outlet->store_id] = $outlet->outlet_name;
            }
            else {
                $results[$outlet->id . '" class="chain_cl_' . $outlet->store_id] = $outlet->outlet_name;
            }
        }

        return $results;
    }
    public function get_outlet_dropdown_report($id = 0)
    {
        $this->db->select('*');
        $this->db->from('outlet');
        $this->db->order_by('outlet_name', 'ASC');
        $query = $this->db->get();
        $data  = $query->result();

        $results                         = array();
        $results['0" class="chain_cl_0'] = "All";
        foreach ($data as $outlet) {
            if ($id != 0 && $id == $outlet->id) {
                $results[$outlet->id . '" class="select_me chain_cl_' . $outlet->store_id] = $outlet->outlet_name;
            }
            else {
                $results['0" class="chain_cl_' . $outlet->store_id] = "All";
                $results[$outlet->id . '" class="chain_cl_' . $outlet->store_id] = $outlet->outlet_name;
            }
        }

        return $results;
    }
	
    public function get_status_table($table_id)
    {
        $this->db->select('table_status')->from('table')->where('id', $table_id)->limit(1);

        return $this->db->get()->result();
    }
	
    public function get_status_table_by_floor($floor_id)
    {
        $this->db->select('table_status')->from('table')->where('floor_id', $floor_id);

        return $this->db->get()->result();
    }

    
    public function get_one_menu($id)
    {
        $this->db->select('menu.*,outlet_id,outlet_name, 
            store_name,store.id as store_id,
            category_name,
            outlet_name, 
            menu_ingredient.quantity');
        $this->db->from('menu');
        $this->db->join('category', 'category.id = menu.category_id');
        $this->db->join('outlet', 'outlet.id = category.outlet_id');
        $this->db->join('store', 'store.id = outlet.store_id');
        $this->db->join('menu_ingredient', 'menu_ingredient.menu_id = menu.id', 'left');
        $this->db->order_by('menu_name', 'ASC');
        $this->db->where(array('menu.id' => $id))->limit(1);

        return $this->get()->row();
    }


    function get_category_dropdown($condition = false){
		if(!$condition) $condition = array();
        $data = $this->db->select("*")
        ->where($condition)
        ->order_by('category_name', 'ASC')
        ->get('category')->result();

        $results    = array();
        $results[0] = "Pilih Kategori";
        foreach ($data as $row) {
            $results[$row->id] = $row->category_name;
        }

        return $results;

    }

    function get_chained_category_dropdown($id = 0)
    {
        $this->db->select('category.*,outlet_name, outlet_id, store_name');
        $this->db->from('category');
        $this->db->join('outlet', 'outlet.id = category.outlet_id');
        $this->db->join('store', 'store.id = outlet.store_id');
                $this->db->where("category.is_package",0);
                $this->db->where("category.is_active", 1);
        $this->db->order_by('store_name', 'ASC');
        $this->db->order_by('outlet_name', 'ASC');
        $this->db->order_by('category_name', 'ASC');
        $query = $this->db->get();
        $data  = $query->result();

        $results                         = array();
        $results['0" class="chain_cl_0'] = $this->lang->line('ds_choose_category');
        foreach ($data as $outlet) {
            if ($id != 0 && $id == $outlet->id) {
                $results[$outlet->id . '" class="select_me chain_cl_' . $outlet->outlet_id] = $outlet->category_name;
            }
            else {
                $results[$outlet->id . '" class="chain_cl_' . $outlet->outlet_id] = $outlet->category_name;
            }
        }

        return $results;
    }

    function get_outlet_dropdown_from_warehouse(){
        $data = $this->db->select("*")
        ->where('is_warehouse',1)
        ->order_by('outlet_name', 'ASC')
        ->get('outlet')->result();

        $results    = array();
        $results[0] = "Pilih ".$this->lang->line('outlet_title');
        foreach ($data as $row) {
            $results[$row->id] = $row->outlet_name;
        }

        return $results;

    }

    function get_outlet_dropdown_from_warehouse2($store){
        $data = $this->db->select("*")
        ->where('is_warehouse',1)
        ->where_not_in('store_id', $store)
        ->order_by('outlet_name', 'ASC')
        ->get('outlet')->result();

        $results    = array();
        $results[0] = "Pilih ".$this->lang->line('outlet_title');
        foreach ($data as $row) {
            $results[$row->id] = $row->outlet_name;
        }

        return $results;

    }


    public function get_sidedishes_dropdown($id = 0)
    {
        $this->db->select('id,name');
        $this->db->from('side_dish');
         
        
        $query = $this->db->get();
        $data  = $query->result();

        $results      = array();
        $results['0'] ="Pilih Side Dish";
        foreach ($data as $side) {
            $results[$side->id] = $side->name;
        }

        return $results;
    }

}