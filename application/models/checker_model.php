<?php

class Checker_model extends MY_Model
{
  function __construct()
  {
    parent::__construct();
  }
  public function get_order_menu_checker($outlet_id)
    {
        $list_order  = array();
        $list_orders = array();
        $ordersThing = new stdClass();
        $table_data  = array();

        $this->db->select('table.id as table_id, table_name ')
        ->from('table')
                 ->join('floor', 'floor.id = table.floor_id')
                 ->join('outlet', 'outlet.store_id = floor.store_id');
        if($outlet_id!=0){
          $this->db->where('outlet.id', $outlet_id);
         }
        $list_table = $this->db->get()->result();
        foreach ($list_table as $table) {

            $this->db->select('order_menu.*, menu.menu_name,enum_cooking_status.status_name,
              (select group_concat(side_dish_id) from order_menu_side_dish where order_menu_id=order_menu.id) as side_dishes,
              (select group_concat(menu_option_value_id) from order_menu_option where order_menu_id=order_menu.id) as options,
              menu.color
            ')
            ->from('order_menu')
                     ->join('order', 'order.id = order_menu.order_id')->join('menu', 'menu.id = order_menu.menu_id')
                     ->join('category', 'category.id = menu.category_id')
                     ->join('enum_cooking_status', 'enum_cooking_status.id = order_menu.cooking_status')
                     ->where('order.table_id', $table->table_id);
                     if($outlet_id!=0){
                      $this->db->where('category.outlet_id', $outlet_id);
                     }
                     $this->db->where('order_menu.process_status', 1)->where_in('order_menu.cooking_status', array(7))->order_by("order_menu.created_at","asc");
            $orders = $this->db->get()->result();
            // echo $this->db->last_query();

            $tableThing = new stdClass();
            if (sizeof($orders) != 0) {
                $order_data = array();
                foreach ($orders as $order) {
                    $orderThing     = new stdClass();
                    $sidedish_order = array();
                    $options_order  = array();

                    if (! empty($order->side_dishes)) {
                        $sidedish = explode(',', $order->side_dishes);
                        foreach ($sidedish as $value) {
                            $side = $this->get_one('side_dish', $value);
                            if ($side) {
                                $sideThing        = new stdClass();
                                $sideThing->name  = $side->name;
                                $sidedish_order[] = $sideThing;
                            }
                        }
                    }

                    if (! empty($order->options)) {
                        $opts = explode(',', $order->options);
                        foreach ($opts as $opt) {
                            $optx = $this->get_one('menu_option_value', $opt);
                            if ($optx) {
                                $option_name                    = $this->get_one('menu_option', $optx->option_id);
                                $optionThing                    = new stdClass();
                                $optionThing->option_name       = $option_name->option_name;
                                $optionThing->option_value_name = $optx->option_value_name;
                                $options_order[]                = $optionThing;
                            }
                        }
                    }
                    $orderThing->order          = $order;
                    $orderThing->side_dish_list = $sidedish_order;
                    $orderThing->option_list    = $options_order;
                    $order_data[]               = $orderThing;


                }
                $tableThing->table_id   = $table->table_id;
                $tableThing->table_name = $table->table_name;
                $tableThing->order      = $order_data;

                $list_order[] = $tableThing;
            }

        }

        $this->db->select('order_menu.*, menu.menu_name,enum_cooking_status.status_name, order.customer_name,
              (select group_concat(side_dish_id) from order_menu_side_dish where order_menu_id=order_menu.id) as side_dishes,
              (select group_concat(menu_option_value_id) from order_menu_option where order_menu_id=order_menu.id) as options')
                 ->from('order_menu')
                 ->join('order', 'order.id = order_menu.order_id')
                 ->join('menu', 'menu.id = order_menu.menu_id')
                 ->join('category', 'category.id = menu.category_id')
                 ->join('enum_cooking_status', 'enum_cooking_status.id = order_menu.cooking_status')
                 ->where('order.table_id', 0);
                 if($outlet_id!=0){
                  $this->db->where('category.outlet_id',$outlet_id);
                 }
                 $this->db->where('order_menu.process_status', 1)->where_in('order_menu.cooking_status', array(7));
        $orders     = $this->db->get()->result();
        $tableThing = new stdClass();
        if (sizeof($orders) != 0) {
            $order_data = array();
            foreach ($orders as $order) {
                $orderThing     = new stdClass();
                $sidedish_order = array();
                $options_order  = array();

                if (! empty($order->side_dishes)) {
                    $sidedish = explode(',', $order->side_dishes);
                    foreach ($sidedish as $value) {
                        $side = $this->get_one('menu_side_dish', $value);
                        if ($side) {
                            $sideThing        = new stdClass();
                            $sideThing->name  = $side->name;
                            $sidedish_order[] = $sideThing;
                        }
                    }
                }

                if (! empty($order->options)) {
                    $opts = explode(',', $order->options);
                    foreach ($opts as $opt) {
                        $optx = $this->get_one('menu_option_value', $opt);
                        if ($optx) {
                            $option_name                    = $this->get_one('menu_option', $optx->option_id);
                            $optionThing                    = new stdClass();
                            $optionThing->option_name       = $option_name->option_name;
                            $optionThing->option_value_name = $optx->option_value_name;
                            $options_order[]                = $optionThing;
                        }
                    }
                }
                $orderThing->order          = $order;
                $orderThing->side_dish_list = $sidedish_order;
                $orderThing->option_list    = $options_order;
                // $order_data[] = $orderThing;

                $id = $order->order_id;
                if (isset($order_data[$id])) {
                    $order_data[$id][] = $orderThing;
                }
                else {
                    $order_data[$id] = array($orderThing);
                }
            }
            $tableThing->table_id   = 0;
            $tableThing->table_name = 'Takeaway';
            $tableThing->order      = $order_data;

            $list_order[] = $tableThing;
            // array_push($list_order,$tableThing);
        }

        $return['order'] = $list_order;

        return $list_order;

        // $this->db->select('order_menu.id as order_menu_id, order_menu.count, order_menu.options, order_menu.side_dishes, order_menu.notes, order_menu.order_id, order_menu.cooking_status, orders.table_id, enum_cooking_status.status_name, menu.menu_name,table.table_name ')
        // ->from('order_menu')
        // ->join('enum_cooking_status','enum_cooking_status.id = order_menu.cooking_status')
        // ->join('order','order.id = order_menu.order_id')
        // ->join('table','table.id = orders.table_id')
        // ->join('floor','floor.id = table.floor_id')
        // ->join('menu','menu.id = order_menu.menu_id')
        // ->where('floor.store_id',$store_id)
        // ->where_in('order_menu.cooking_status',array(1,2));

        // $result = $this->db->get()->result();
    }
    public function get_order_menu_checker2($outlet_id,$checker_setting=array(),$post_to=0)
    {
      $setting=array("reservation");
      foreach($checker_setting as $key=>$d){
        if($d==1){
          array_push($setting,$key);
        }
      }
      // where date(o2.start_order)=CURRENT_DATE() and o2.id<=o.id and (o2.id in (select order_id from order_menu where dinein_takeaway=1 and date(created_at)=CURRENT_DATE()) or o2.is_take_away=1)
      $this->db->select("
        om.id, om.order_id, om.menu_id, om.quantity,
		om.waiter_id, om.process_status, om.cooking_status, om.kitchen_status,
		om.note, om.created_at, om.created_by, om.dinein_takeaway,
		om.is_check, om.finished_at, om.post_to, o.table_id,o.start_order,o.customer_name,t.table_name,ecs.status_name,
        (select group_concat(side_dish_id) from order_menu_side_dish where order_menu_id=om.id) as side_dishes,
        (select group_concat(menu_option_value_id) from order_menu_option where order_menu_id=om.id) as options,
        IF((select count(id) from order_menu where order_id=om.order_id and created_at < om.created_at) > 0,1,0) as is_additional,
        IF(t.id!='',IF(om.dinein_takeaway=1,'takeaway_delivery','dinein'),IF(o.is_take_away=1 or om.dinein_takeaway=1,'takeaway_delivery',IF(o.is_delivery=1,'takeaway_delivery','reservation'))) as type,
        IF(t.id!='',IF(om.dinein_takeaway=1,'takeaway','dinein'),IF(o.is_take_away=1 or om.dinein_takeaway=1,'takeaway',IF(o.is_delivery=1,'delivery','reservation'))) as type_origin,
        u.name as waiter_name,u2.name as waiter_name_current,
				IF(c.is_package=1,m2.menu_name,m.menu_name) as menu_name,
        IF(c.is_package=1,m2.menu_short_name,m.menu_short_name) as menu_short_name,
        IF(c.is_package=1,opm.quantity,om.quantity) as quantity,
        IF(c.is_package=1,m2.process_checker,m.process_checker) as process_checker,
        IF(c.is_package=1,m2.color,m.color) as color,
        IF(c.is_package=1,m2.background_color,m.background_color) as background_color,
        IF(c.is_package=1,opm.cooking_status,om.cooking_status) as cooking_status,
        IF(c.is_package=1,opm.process_status,om.process_status) as process_status,
        IF(c.is_package=1,opm.is_check,om.is_check) as is_check,opm.id as order_package_menu_id,c.is_package,
        (
          if(om.dinein_takeaway=1 or o.is_take_away=1,
            (
              (
                select 900+count(*)
                from `order` o2
                where date(o2.start_order)=date(o.start_order) and o2.id<=o.id and o2.is_take_away=1
              )
            )
          ,if(o.is_delivery=1,
            (
              select 800+count(*)
              from `order` o2
              where date(o2.start_order)=date(o.start_order) and o2.id<=o.id and o2.is_delivery=1
            )
          ,0))
        ) as counter
      ",false)->from("order_menu om")
			->join('order_package_menu opm','om.id=opm.order_menu_id and opm.cooking_status=7','left')
			->join('menu m2', 'm2.id = opm.menu_id','left')
			->join('category c2','c2.id=m2.category_id','left')
      ->join('order o', 'o.id = om.order_id')
      ->join('table t','t.id=o.table_id','left')
      ->join('users u','o.created_by=u.id','left')
      ->join('users u2','om.created_by=u2.id','left')
      ->join('menu m', 'm.id = om.menu_id')
      ->join('category c', 'c.id = m.category_id')
      ->join('enum_cooking_status ecs', 'ecs.id = om.cooking_status');
      if($outlet_id!=0){
        $this->db->where('IF(c.is_package=1,c2.outlet_id,c.outlet_id)=', $outlet_id);
      }
      $this->db->where('om.process_status', 1);
      $this->db->where_in('IF(c.is_package=1,opm.cooking_status,om.cooking_status)', array(7));
			if($post_to>0){				
				$this->db->where('om.post_to', $post_to);
			}
      $this->db->group_by("o.id,om.id,opm.id")
      ->order_by("om.created_at","asc")
      ->order_by("m.duration","desc")
      ->order_by("m2.duration","desc")
      ->order_by("m.menu_name","asc")
      ->order_by("m2.menu_name","asc");
      $results=array();
      foreach($this->db->get()->result() as $r){
        if(in_array($r->type,$setting)){
          $sidedish_order = array();
          $options_order  = array();
          if (! empty($r->side_dishes)) {
            $sidedish = explode(',', $r->side_dishes);
            foreach ($sidedish as $value) {
              $side = $this->get_one('side_dish', $value);
              if ($side) {
                $sideThing        = new stdClass();
                $sideThing->name  = $side->name;
                $sidedish_order[] = $sideThing;
              }
            }
          }
          if (! empty($r->options)) {
              $opts = explode(',', $r->options);
              foreach ($opts as $opt) {
                  $optx = $this->get_one('menu_option_value', $opt);
                  if ($optx) {
                      $option_name                    = $this->get_one('menu_option', $optx->option_id);
                      $optionThing                    = new stdClass();
                      $optionThing->option_name       = $option_name->option_name;
                      $optionThing->option_value_name = $optx->option_value_name;
                      $options_order[]                = $optionThing;
                  }
              }
          }
          $r->side_dish_list=$sidedish_order;
          $r->option_list=$options_order;
          $results[$r->is_additional][$r->order_id."_".$r->type][]=$r;          
        }
      }
      return $results;
    }
    public function get_reservation_without_order_menu($params=array()){
      // where date(o2.created_at)=date(r.book_date) and o2.created_at<=r.book_date and (o2.id in (select order_id from order_menu where dinein_takeaway=1 and date(created_at)=date(r.book_date)) or o2.is_take_away=1)
      if($params['start_date']=="")$params['start_date']=date("Y-m-d");
      $this->db->select("r.*,r.id as reservation_id,t.table_name,u.name as operator_name,
        (
          if(r.order_type=2,
            (
              select 900+count(*)
              from `order` o2
              where date(o2.created_at)=date(r.book_date) and o2.created_at<=r.book_date and o2.is_take_away=1
            )
          ,if(r.order_type=3,
            (
              select 800+count(*)
              from `order` o2
              where date(o2.created_at)=date(r.book_date) and o2.created_at<=r.book_date and o2.is_delivery=1
            )
          ,0))
        ) as counter
      ",false)->from("reservation r")
      ->join('table t','t.id=r.table_id','left')
      ->join('users u','r.created_by=u.id','left')
      ->where('r.id not in (select reservation_id from `order` where reservation_id=r.id)')
      ->where('if(r.order_type=1,r.table_id!=0,true)')
      ->where('date(r.book_date) >=',$params['start_date'])
      ->where_in('r.status',array(1,2));
      if($params['end_date']!=""){
        $this->db->where('date(r.book_date) <=',$params['end_date']);
      }
      $this->db->order_by("r.book_date","asc");
      $results=$this->db->get()->result();
      return $results;
    }
    public function get_order_menu_reservation($params=array())
    {
      // where date(o2.created_at)=date(r.book_date) and o2.id<=o.id and (o2.id in (select order_id from order_menu where dinein_takeaway=1 and date(created_at)=date(r.book_date)) or o2.is_take_away=1)
      if($params['start_date']=="")$params['start_date']=date("Y-m-d");
      $this->db->select("
        r.order_type,r.status_posting,om.*,o.reservation_id,o.start_order,o.table_id,o.start_order,o.customer_name,t.table_name,m.menu_name,ecs.status_name,
        (select group_concat(side_dish_id) from order_menu_side_dish where order_menu_id=om.id) as side_dishes,
        (select group_concat(menu_option_value_id) from order_menu_option where order_menu_id=om.id) as options,
        IF(t.id!='',IF(om.dinein_takeaway=1,'takeaway','dinein'),IF(o.is_take_away=1 or om.dinein_takeaway=1,'takeaway',IF(o.is_delivery=1,'delivery','reservation'))) as type_origin,
        m.process_checker,m.color,m.background_color,m.menu_short_name,u.name as waiter_name,r.book_date,
        (
          if(om.dinein_takeaway=1 or o.is_take_away=1,
            (
              (
                select 900+count(*)
                from `order` o2
                where date(o2.created_at)=date(r.book_date) and o2.id<=o.id and o2.is_take_away=1
              )
            )
          ,if(o.is_delivery=1,
            (
              select 800+count(*)
              from `order` o2
              where date(o2.created_at)=date(r.book_date) and o2.id<=o.id and o2.is_delivery=1
            )
          ,0))
        ) as counter
      ",false)->from("order_menu om")
      ->join('order o', 'o.id = om.order_id')
      ->join('reservation r','o.reservation_id=r.id','left')
      ->join('table t','t.id=o.table_id','left')
      ->join('users u','o.created_by=u.id','left')
      ->join('menu m', 'm.id = om.menu_id')
      ->join('category c', 'c.id = m.category_id')
      ->join('enum_cooking_status ecs', 'ecs.id = om.cooking_status')
      ->where('o.reservation_id !=',0)
      ->where_in('r.status',array(1,2))
	  ->where('if(r.order_type=1,r.table_id!=0,true)')
      ->where('date(r.book_date) >=',$params['start_date']);
      if($params['end_date']!=""){
        $this->db->where('date(r.book_date) <=',$params['end_date']);
      }
      $this->db->order_by("o.start_order","asc")
      ->order_by("m.duration","desc")
      ->order_by("m.menu_name","asc");
      $results=array();
      foreach($this->db->get()->result() as $r){
        // if($r->reservation_id!=0){
          $sidedish_order = array();
          $options_order  = array();
          if (! empty($r->side_dishes)) {
            $sidedish = explode(',', $r->side_dishes);
            foreach ($sidedish as $value) {
              $side = $this->get_one('side_dish', $value);
              if ($side) {
                $sideThing        = new stdClass();
                $sideThing->name  = $side->name;
                $sidedish_order[] = $sideThing;
              }
            }
          }
          if (! empty($r->options)) {
              $opts = explode(',', $r->options);
              foreach ($opts as $opt) {
                  $optx = $this->get_one('menu_option_value', $opt);
                  if ($optx) {
                      $option_name                    = $this->get_one('menu_option', $optx->option_id);
                      $optionThing                    = new stdClass();
                      $optionThing->option_name       = $option_name->option_name;
                      $optionThing->option_value_name = $optx->option_value_name;
                      $options_order[]                = $optionThing;
                  }
              }
          }
          $r->side_dish_list=$sidedish_order;
          $r->option_list=$options_order;
          $results[$r->order_id][]=$r;                    
        // }
      }
      return $results;
    }
    public function get_order_menu_reservation_by_id($reservation_id){
      $this->db->select("
        r.order_type,r.status_posting,om.*,o.reservation_id,o.start_order,o.table_id,o.start_order,o.customer_name,t.table_name,m.menu_name,ecs.status_name,
        (select group_concat(side_dish_id) from order_menu_side_dish where order_menu_id=om.id) as side_dishes,
        (select group_concat(menu_option_value_id) from order_menu_option where order_menu_id=om.id) as options,
        IF(t.id!='',IF(om.dinein_takeaway=1,'takeaway','dinein'),IF(o.is_take_away=1 or om.dinein_takeaway=1,'takeaway',IF(o.is_delivery=1,'delivery','reservation'))) as type_origin,
        m.process_checker,m.color,m.background_color,m.menu_short_name,u.name as waiter_name,r.book_date,
        (
          if(om.dinein_takeaway=1 or o.is_take_away=1,
            (
              (
                select 900+count(*)
                from `order` o2
                where date(o2.created_at)=date(r.book_date) and o2.id<=o.id and o2.is_take_away=1
              )
            )
          ,if(o.is_delivery=1,
            (
              select 800+count(*)
              from `order` o2
              where date(o2.created_at)=date(r.book_date) and o2.id<=o.id and o2.is_delivery=1
            )
          ,0))
        ) as counter
      ",false)->from("order_menu om")
      ->join('order o', 'o.id = om.order_id')
      ->join('reservation r','o.reservation_id=r.id','left')
      ->join('table t','t.id=o.table_id','left')
      ->join('users u','o.created_by=u.id','left')
      ->join('menu m', 'm.id = om.menu_id')
      ->join('category c', 'c.id = m.category_id')
      ->join('enum_cooking_status ecs', 'ecs.id = om.cooking_status')
      ->where('o.reservation_id =',$reservation_id)
      ->where_in('r.status',array(1,2));
      $this->db->order_by("o.start_order","asc")
      ->order_by("m.duration","desc")
      ->order_by("m.menu_name","asc");
      $results=array();
      foreach($this->db->get()->result() as $r){
        
        // if($r->reservation_id!=0){
          $sidedish_order = array();
          $options_order  = array();
          if (! empty($r->side_dishes)) {
            $sidedish = explode(',', $r->side_dishes);
            foreach ($sidedish as $value) {
              $side = $this->get_one('side_dish', $value);
              if ($side) {
                $sideThing        = new stdClass();
                $sideThing->name  = $side->name;
                $sidedish_order[] = $sideThing;
              }
            }
          }
          if (! empty($r->options)) {
              $opts = explode(',', $r->options);
              foreach ($opts as $opt) {
                  $optx = $this->get_one('menu_option_value', $opt);
                  if ($optx) {
                      $option_name                    = $this->get_one('menu_option', $optx->option_id);
                      $optionThing                    = new stdClass();
                      $optionThing->option_name       = $option_name->option_name;
                      $optionThing->option_value_name = $optx->option_value_name;
                      $options_order[]                = $optionThing;
                  }
              }
          }
          $r->side_dish_list=$sidedish_order;
          $r->option_list=$options_order;
          $results[0][]=$r;                    
      }
      return $results;
    }
}