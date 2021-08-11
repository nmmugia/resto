<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/16/2014
 * Time: 10:53 AM
 */
class Cashier_model extends MY_Model
{

    /**
     *
     */
    function __construct()
    {
        parent::__construct();
    }
    function get_order_delivery($status)
    {
        $this->db->select('o.*,r.status_posting,
          (
            select 800+count(*)
            from `order` o2
            where date(o2.start_order)=date(o.start_order) and o2.id<=o.id and o2.is_delivery=1
          ) as counter
        ',false)->from("order o")
        ->join("reservation r","r.id=o.reservation_id","left");
        $this->db->where('o.order_status', $status);
        $this->db->where('o.is_delivery', 1);
        $this->db->where('date(now())>=', 'date(o.start_order)',false);
        $this->db->order_by('counter', 'ASC');
        $result=$this->db->get();
        $return = array();
        foreach ($result->result() as $option) {
          if((int)$option->reservation_id==0 || $option->status_posting==1){
            $option->values = $this->get_order_menu($option->id);
            $return[]       = $option;            
          }
        }
        return $return;
    }
    function get_order_takeaway($status=0)
    {
        // where date(o2.start_order)=CURRENT_DATE() and o2.id<=o.id and (o2.id in (select order_id from order_menu where dinein_takeaway=1 and date(created_at)=CURRENT_DATE()) or o2.is_take_away=1)
        $this->db->select('o.*,r.status_posting,
          (
            select 900+count(*)
            from `order` o2
            where date(o2.start_order)=date(o.start_order) and o2.id<=o.id and o2.is_take_away=1
          ) as counter
        ',false)->from("order o")
        ->join("reservation r","r.id=o.reservation_id","left");
        $this->db->where('o.order_status', $status);
        $this->db->where('o.is_take_away', 1);
        $this->db->where('date(now())>=', 'date(o.start_order)',false);
        $this->db->order_by('counter', 'ASC');
        $result=$this->db->get();
        $return = array();
        foreach ($result->result() as $option) {
          if((int)$option->reservation_id==0 || $option->status_posting==1){
            $option->values = $this->get_order_menu($option->id);
            $return[]       = $option;
          }
        }

        return $return;
    }

    function get_order_menu($order_id)
    {
        $this->db->select('order_menu.id as order_menu_id,
            menu.menu_name,menu.menu_price,SUM(order_menu.quantity) - IFNULL((SELECT SUM(quantity) from bill_menu where order_menu_id = order_menu.id),0) as quantity,
            order_menu.cooking_status,
            order_menu.process_status,
            enum_cooking_status.status_name,
            IF((select count(om.id) from order_menu om where om.order_id=order_menu.order_id and om.created_at < order_menu.created_at) > 0,1,0) as is_additional
        ',false)
        ->from('order_menu');
        $this->db->join('menu', 'menu.id = order_menu.menu_id');
        $this->db->join('enum_cooking_status', 'enum_cooking_status.id = order_menu.cooking_status');
        $this->db->where('order_menu.order_id', $order_id);
        $this->db->where('order_menu.quantity - IFNULL((SELECT SUM(quantity) from bill_menu where order_menu_id = order_menu.id),0) >',"0");
        $this->db->order_by('order_menu.created_at', 'ASC');
        $this->db->group_by('order_menu.menu_id');
        return $this->db->get()->result();
    }

    function get_category_by_store($store_id,$contains_menu_only=false)
    {
        $this->db->select('category.*');
        $this->db->from('category');
        $this->db->join('outlet', 'outlet.id = category.outlet_id');
        $this->db->join('store', 'store.id = outlet.store_id');
        if($contains_menu_only){
            $this->db->join('menu', 'category.id = menu.category_id');
            $this->db->where('menu.is_active', 1);
            $this->db->group_by('category.id');
        }
        $this->db->where('store.id', $store_id);       
        $this->db->where('category.is_active', 1);
        $this->db->order_by('category.category_name', 'ASC');

        return $this->db->get()->result();
    }

    function get_category_by_store_menu($store_id)
    {
        $this->db->select('category.*');
        $this->db->from('category');
        $this->db->join('menu', 'menu.category_id = category.id');
        $this->db->join('outlet', 'outlet.id = category.outlet_id');
        $this->db->join('store', 'store.id = outlet.store_id');
        $this->db->where('store.id', $store_id);
        $this->db->where('category.is_active', 1);
        $this->db->order_by('category.category_name', 'ASC');
        $this->db->group_by('category.category_name');

        return $this->db->get()->result();
    }

    function get_menus_by_store($store_id=0)
    {
        $this->db->select('menu.*, category.outlet_id,count(menu_option.id) as menu_option_count,count(menu_side_dish.id) as menu_side_dish_count');
        $this->db->from('menu');
        $this->db->join('category', 'category.id = menu.category_id');
        $this->db->join('outlet', 'outlet.id = category.outlet_id');
        $this->db->join('store', 'store.id = outlet.store_id');
        $this->db->join('menu_option','menu.id=menu_option.menu_id','left');
        $this->db->join('menu_side_dish','menu.id=menu_side_dish.menu_id','left');
        $this->db->where('store.id', $store_id);
        $this->db->where('menu.available', 1);
        $this->db->where('menu.is_active', 1);
        $this->db->group_by('menu.id');
        $this->db->order_by('menu.menu_name', 'ASC');
        $this->db->order_by('menu.position', 'ASC');
        $this->db->order_by('menu.created_at', 'DESC');

        return $this->db->get()->result();
    }
    function get_menus_by_category($params=array())
    {
        $this->db->select('menu.*, category.outlet_id,count(menu_option.id) as menu_option_count, if(menu.is_promo = 1, count(menu_promo_side_dish.id), count(menu_side_dish.id)) as menu_side_dish_count', false);
        $this->db->from('menu');
        $this->db->join('category', 'category.id = menu.category_id');
        $this->db->join('menu_option','menu.id=menu_option.menu_id','left');
        $this->db->join('menu_side_dish','menu.id=menu_side_dish.menu_id','left');
        $this->db->join('menu_promo_side_dish', 'menu.id = menu_promo_side_dish.parent_menu_id', 'left');
        if($params['category_id']!=""){
          $this->db->where('menu.category_id', $params['category_id']);
        }
        $this->db->where('menu.available', $params['available']);
        $this->db->where('menu.is_active', 1);
        $this->db->group_by('menu.id');
        $this->db->order_by('menu.menu_name', 'ASC');
        return $this->db->get()->result();
    }

    function get_max_receipt_id()
    {
        $this->db->select_max('receipt_number');
        $this->db->from('bill');
        $this->db->limit(1);

        return $this->db->get()->row()->receipt_number;
    }
    function get_four_digit_receipt_id()
    {
        $this->db->select('IFNULL(GROUP_CONCAT(SUBSTR(receipt_number,9,LENGTH(receipt_number))),"") as exist_numbers',false);
        $this->db->from('bill');
        // $this->db->where("date(payment_date)",date("Y-m-d"));
        $this->db->where("date(payment_date)","2016-03-06");
        return $this->db->get()->row()->exist_numbers;
    }

    public function get_promo_discount_dropdown($store_id)
    {
        $query = $this->db->query("
            select
                a.id, a.name, 'discount' AS promo_type
            FROM
                promo_discount a
            JOIN promo_schedule b ON (a.promo_schedule_id = b.id)
            WHERE
                b.start_date <= CURDATE()
            AND b.end_date >= CURDATE()
            AND CASE
                WHEN has_timeframe = 1 THEN
                    b.start_time <= CURTIME()
                AND b.end_time >= CURTIME()
                ELSE
                    has_timeframe = 0
            END
            AND CASE
                WHEN dayname(curdate()) = 'Monday' THEN is_monday = 1
                WHEN dayname(curdate()) = 'Tuesday' THEN is_tuesday = 1
                WHEN dayname(curdate()) = 'Wednesday' THEN is_wednesday = 1
                WHEN dayname(curdate()) = 'Thursday' THEN is_thursday = 1
                WHEN dayname(curdate()) = 'Friday' THEN is_friday = 1
                WHEN dayname(curdate()) = 'Saturday' THEN is_saturday = 1
                WHEN dayname(curdate()) = 'Sunday' THEN is_sunday = 1
            END
        ");  
      
        $data  = $query->result();

        $results    = array();
        $results[0] = "Pilih Promo";
        foreach ($data as $store) {
            $results[$store->id."-".$store->promo_type] = $store->name;
        }

        return $results;
    }
		public function get_promo_cc_dropdown($store_id)
    {
        $query = $this->db->query("
					select a.id,a.name,'cc' as promo_type 
					from promo_cc a 
					join promo_schedule b on(a.promo_schedule_id = b.id)
					where  b.end_date > curdate()
					AND
					CASE
						WHEN dayname(curdate()) = 'Monday' and has_timeframe=1 THEN is_monday=1
						WHEN dayname(curdate()) = 'Tuesday' and has_timeframe=1 THEN is_tuesday=1
						WHEN dayname(curdate()) = 'Wednesday' and has_timeframe= 1 THEN is_wednesday=1
						WHEN dayname(curdate()) = 'Thursday' and has_timeframe= 1 THEN is_thursday=1
						WHEN dayname(curdate()) = 'Friday' and has_timeframe= 1 THEN is_friday=1
						WHEN dayname(curdate()) = 'Saturday' and has_timeframe= 1 THEN is_saturday=1
						WHEN dayname(curdate()) = 'Sunday' and has_timeframe= 1 THEN is_sunday=1
						ELSE has_timeframe = 0
					END
        ");  
      
        $data  = $query->result();

        $results    = array();
        $results[0] = "Pilih Promo";
        foreach ($data as $store) {
            $results[$store->id."-".$store->promo_type] = $store->name;
        }

        return $results;
    }
    public function get_detail_promo($promo_type,$promo_id){
        $this->db->select('b.category_id,a.*');
        $this->db->from('promo_'.$promo_type.' a');
        $this->db->join('promo_'.$promo_type.'_category b', 'a.id= b.promo_'.$promo_type.'_id');
        
        $this->db->where('a.id', $promo_id);
       

        return $this->db->get()->result();
    }

    public function get_detail_promo_menu($promo_type,$promo_id){
        $this->db->select('b.menu_id,a.*');
        $this->db->from('promo_'.$promo_type.' a');
        $this->db->join('promo_'.$promo_type.'_menu b', 'a.id= b.promo_'.$promo_type.'_id');
        
        $this->db->where('a.id', $promo_id);
       

        return $this->db->get()->result();
    }
}