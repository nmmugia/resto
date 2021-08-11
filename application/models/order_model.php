<?php

class Order_model extends MY_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function save_order($data)
    {
        $this->db->insert('order', $data);

        return $this->db->insert_id();
    }

    public function save_order_menu($data)
    {
        $this->db->insert('order_menu', $data);

        return $this->db->insert_id();
    }

    public function get_order_by_id($order_id)
    {
        $this->db->select('*')->from('order')->where('id', $order_id);

        return $this->db->get()->result();
    }

    public function get_order_by_date_range($start_date, $end_date, $where = FALSE)
    {
        $query = $this->db->select('order.*')->from('order')
            ->join("bill", "order.id=bill.order_id");
        if ($start_date) $query->where('bill.start_order >=', $start_date);
        if ($end_date) $query->where('bill.end_order <=', $end_date);

        if ($where !== FALSE) {
            $query->where($where);
        }

        return $this->db->get()->result();
    }

    public function get_order_menu_by_order($order_id, $cond = array(), $cond2 = array())
    {
        $this->db->select('*, menu.menu_name, menu.menu_price, order_menu.id as order_menu_id,
          u.name as waiter_name,o.is_delivery,o.is_take_away,menu.process_checker, menu.is_promo')
            ->from('order_menu')
            ->join('order o', 'order_menu.order_id=o.id')
            ->join('menu', 'menu.id = order_menu.menu_id')
            ->join('users u', 'order_menu.created_by = u.id', "left")
            ->where('order_id', $order_id);
        if (sizeof($cond) > 0) {
            $this->db->where_in("order_menu.cooking_status", $cond);
        }
        if (sizeof($cond2) > 0) {
            $this->db->where_in("order_menu.id", $cond2);
        }
        return $this->db->get()->result();
    }

    function get_price_analyst_data($store_id=FALSE,$outlet_id=FALSE,$category_menu_id=FALSE){
        $this->db->select('
                s.store_name as strname,
                c.category_name as ctgname,
                m.menu_name, 
                m.menu_hpp, 
                m.menu_price,
                (m.menu_price - m.menu_hpp) AS gross,
                ((m.menu_price - m.menu_hpp) / m.menu_price) * 100 AS margin,
                ((m.menu_price - m.menu_hpp) / m.menu_hpp) * 100 AS markup
            ')
        ->from('menu m')
        ->join('category c', 'c.id=m.category_id')
        ->join('outlet o', 'o.id=c.outlet_id')
        ->join('store s', 's.id=o.store_id');

        if($store_id){
            $this->db->where('s.id', $store_id);
        }
        if($outlet_id){
            $this->db->where('o.id', $outlet_id);
        }
        if($category_menu_id){
            $this->db->where('c.id', $category_menu_id);
        }

        $result = $this->db->get()->result();
        
        return $result;

    }

    public function get_order_menu_by_id($order_menu_id)
    {
        $this->db->select('*')
            ->from('order_menu')
            ->where('id', $order_menu_id);

        return $this->db->get()->result();
    }

    public function get_data_attendance_detail($start_date=FALSE, $end_date=FALSE,$user_id=FALSE)
    {


        $this->db->select('
                                    u.name as uname,
                                    a.created_at as cdate,
                                    hsd.start_time as masuk,
                                    hsd.end_time as keluar,
                                    a.checkin_time as amasuk,
                                    a.checkout_time as akeluar,
                                    a.over_checkout_time as abkeluar,
                                    esa.name as estatus,
                                    a.note as note
                                    ')
            ->from('hr_attendances a')
            ->join('users u',  'u.id = a.user_id')
            ->join('hr_enum_status_attendance esa', 'a.enum_status_attendance = esa.id')
            ->join('hr_schedules hs', 'hs.user_id = u.id')
            ->join('hr_schedule_detail hsd', 'hsd.schedule_id = hs.id');

        if($user_id){
            $this->db->where('u.id', $user_id);
        }
        if($start_date){
            $this->db->where('a.created_at >= ', $start_date);
        }

        if($end_date){
            $this->db->where('a.created_at <= ', $end_date);
        }

        $this->db->group_by('hs.id','hs.user_id');
        $this->db->order_by('hs.start_date', 'asc');
       

        return $this->db->get()->result();
       
    }

    public function get_order_history_by_order($order_id)
    {
        $this->db->select('*')->from('order_history')->where('order_id', $order_id);

        return $this->db->get()->row();
    }

    public function update_order_by_id($order_id, $data)
    {
        $this->db->where('id', $order_id);
        $this->db->update('order', $data);
        // $sql = $this->db->last_query();
        // return $sql;
        return ($this->db->affected_rows() > 0);
    }

    public function get_side_dish_by_menu($menu_id, $is_promo)
    {
        $this->db->select('side_dish.price, side_dish.name, side_dish.id');
        if ($is_promo == 1) {
            $this->db->from('menu_promo_side_dish');
            $this->db->join('side_dish', 'side_dish.id = menu_promo_side_dish.side_dish_id');
            $this->db->where('parent_menu_id', $menu_id);
        } else {
            $this->db->from('menu_side_dish');
            $this->db->join('side_dish', 'side_dish.id = menu_side_dish.side_dish_id');
            $this->db->where('menu_id', $menu_id);
        }
        
        $this->db->order_by("sequence", "asc");

        return $this->db->get()->result();
    }

    function get_side_dish_by_order_menu($order_menu_id, $is_promo)
    {
        $this->db->select('order_menu_side_dish.*, side_dish.price, side_dish.name, sequence')
            ->from('order_menu_side_dish')
            ->join('side_dish', 'side_dish.id = order_menu_side_dish.side_dish_id');
        if ($is_promo == 1) {
            $this->db->join('menu_promo_side_dish', 'side_dish.id = menu_promo_side_dish.side_dish_id');
        } else {
            $this->db->join('menu_side_dish', 'side_dish.id = menu_side_dish.side_dish_id');
        }
            
        $this->db->where('order_menu_id', $order_menu_id)
            ->group_by('side_dish.id')
            ->order_by("sequence", "asc");

        return $this->db->get()->result();
    }

    function get_option_by_order_menu($order_menu_id)
    {
        $this->db->select('order_menu_option.*,menu_option_value.option_value_name,menu_option.option_name')
            ->from('order_menu_option')
            ->join("menu_option_value", "order_menu_option.menu_option_value_id=menu_option_value.id", "left")
            ->join("menu_option", "menu_option.id=menu_option_value.option_id", "left")
            ->where('order_menu_id', $order_menu_id);

        return $this->db->get()->result();
    }

    function get_side_dish_bill_by_order_menu($bill_menu_id)
    {
        $this->db->select('bill_menu_side_dish.*, side_dish.price, side_dish.name,menu_side_dish.sequence')
            ->from('bill_menu_side_dish')
            ->join('side_dish', 'side_dish.id = bill_menu_side_dish.side_dish_id')
            ->join('menu_side_dish', 'side_dish.id = menu_side_dish.side_dish_id')
            ->where('bill_menu_id', $bill_menu_id)
            ->order_by("sequence", "asc");

        return $this->db->get()->result();
    }

    public function get_option_by_menu($menu_id)
    {
        $this->db->select('*,menu_option_value.id as option_value_id')
            ->from('menu_option_value')
            ->join('menu_option', 'menu_option.id = menu_option_value.option_id')
            ->where('menu_option.menu_id', $menu_id)
            ->order_by("menu_option_value.sequence", "asc")
            ->order_by("menu_option.sequence", "asc");

        return $this->db->get()->result();
    }

    public function get_option_value_by_id($id)
    {
        $this->db->select('menu_option_value.*,menu_option.option_name')
            ->from('menu_option_value')
            ->join('menu_option', 'menu_option.id = menu_option_value.option_id')
            ->where('menu_option_value.id', $id);
        return $this->db->get()->row();
    }

    public function update_order_by_table($table_id, $data)
    {
        $this->db->where('table_id', $table_id)->update('order', $data);

        return ($this->db->affected_rows() > 0);
    }

    public function update_status_cooking_menu_order($table_id, $data)
    {
        $this->db->where('order.table_id', $table_id)->where('order_menu.cooking_status', 1)->update('order_menu join `order` on `order`.id = order_menu.order_id', $data);

        // $sql = $this->db->last_query();
        // echo $sql;
        return ($this->db->affected_rows() > 0);
    }

    function delete_order_menu($menu_id)
    {
        $this->db->where('id', $menu_id);
        $this->db->delete('order_menu');
        // $sql = $this->db->last_query();
        // echo $sql;
        return ($this->db->affected_rows() > 0);
    }

    public function update_order_menu($order_menu_id, $data)
    {
        $this->db->where('id', $order_menu_id)
            ->update('order_menu', $data);

        return $order_menu_id;
    }

    public function clear_order_menu_sidedish($order_menu_id)
    {
        $this->db->where('order_menu_id', $order_menu_id);
        $this->db->delete('order_menu_side_dish');
    }

    public function clear_order_menu_option($order_menu_id)
    {
        $this->db->where('order_menu_id', $order_menu_id);
        $this->db->delete('order_menu_option');
    }

    public function save_side_dish_order_menu($sidedish, $order_menu_id)
    {
        $sidedish_array = explode(',', $sidedish);
        $this->clear_order_menu_sidedish($order_menu_id);
        $result = FALSE;
        foreach ($sidedish_array as $side) {
            $data = array(
                'order_menu_id' => $order_menu_id,
                'side_dish_id' => $side,
            );
            $result = $this->save('order_menu_side_dish', $data);
        }

        return $result;

    }

    public function save_option_order_menu($option, $order_menu_id)
    {
        if (!empty($option)) {
            $sidedish_array = explode(',', $option);
            $this->clear_order_menu_option($order_menu_id);
            $result = FALSE;
            foreach ($sidedish_array as $side) {
                $data = array(
                    'order_menu_id' => $order_menu_id,
                    'menu_option_value_id' => $side,
                );
                $result = $this->save('order_menu_option', $data);
            }

            return $result;
        }

    }


    public function calculate_total_order($order_id, $canceled = FALSE)
    {
        $this->db->select('order_menu.*,menu.menu_name, menu.menu_price, menu.category_id,
            order_menu.id as order_menu_id, 
            enum_cooking_status.status_name, menu.is_promo');
        $this->db->from('order_menu');
        $this->db->join('menu', 'menu.id = order_menu.menu_id');
        $this->db->join('enum_cooking_status', 'enum_cooking_status.id = order_menu.cooking_status');

        $this->db->where('order_id', $order_id);
        if ($canceled === TRUE) {
            $this->db->where('cooking_status !=', '4');
            $this->db->where('cooking_status !=', '0');
            $this->db->where('cooking_status !=', '6');
        }
        $result = $this->db->get();

        $list_order = array();
        $subtotal = 0;
        foreach ($result->result() as $option) {
            $sidedish_order = array();
            $options_order = array();
            $subtotal += ($option->quantity * $option->menu_price);
            $basic_price = $option->menu_price * $option->quantity;
            $order_menu_sidedish = $this->get_side_dish_by_order_menu($option->id, $option->is_promo);
            $order_menu_option = $this->get_option_by_order_menu($option->id);

            if (!empty($order_menu_sidedish)) {
                // $sidedish = explode(',', $option->side_dishes);
                foreach ($order_menu_sidedish as $side) {
                    // $side = $this->get_one('menu_side_dish', $value);
                    // if ($side) {
                    $side_price = ($option->quantity * $side->price);
                    $subtotal += $side_price;
                    //$basic_price += $side_price;
                    $sideThing = new stdClass();
                    $sideThing->name = $side->name;
                    $sideThing->id = $side->id;
                    $sideThing->quantity = $option->quantity;
                    $sideThing->origin_price = $side->price;
                    $sideThing->price = number_format($side_price, 0, "", ".");
                    $sidedish_order[] = $sideThing;
                    // }
                }
            }

            if (!empty($order_menu_option)) {

                // $opts = explode(',', $option->options);
                foreach ($order_menu_option as $opt) {
                    $optx = $this->get_one('menu_option_value', $opt->menu_option_value_id);
                    if ($optx) {
                        // $options_order[] = $optx->option_value_name;
                        $option_name = $this->get_one('menu_option', $optx->option_id);
                        $optionThing = new stdClass();
                        $optionThing->option_name = $option_name->option_name;
                        $optionThing->option_value_name = $optx->option_value_name;
                        $options_order[] = $optionThing;
                    }
                }
            }

            $order_menu = new stdClass();
            $order_menu->id = $option->id;
            $order_menu->order_id = $option->order_id;
            $order_menu->menu_id = $option->menu_id;
            $order_menu->quantity = $option->quantity;
            $order_menu->options = $options_order;
            $order_menu->side_dishes = $sidedish_order;
            $order_menu->note = $option->note;
            $order_menu->cooking_status = $option->cooking_status;
            $order_menu->process_status = $option->process_status;
            $order_menu->menu_name = $option->menu_name;
            $order_menu->category_id = $option->category_id;
            $order_menu->menu_price = number_format($basic_price, 0, "", ".");
            $order_menu->order_menu_id = $option->order_menu_id;
            $order_menu->side_dish_list = $sidedish_order;
            $order_menu->cooking_status_name = $option->status_name;
            $order_menu->option_list = $options_order;
            $order_menu->origin_price = $option->menu_price;
            $list_order[] = $order_menu;
        }

        $tax_price = 0;
        $count_tax = 0;
        $arr_tax = array();
        $taxes = $this->db->get('taxes');
        foreach ($taxes->result() as $tax) {
            $count_tax = (($subtotal * $tax->tax_percentage) / 100);
            $tax_price += $count_tax;
            $arr_tax[] = array('name' => $tax->tax_name . ' (' . $tax->tax_percentage . '%)',
                'value' => number_format($count_tax, 0, "", "."),
                'tax_percentage' => $tax->tax_percentage
            );
        }

        $total_price = $subtotal + $tax_price;


        $arr_charge = array();
        $extra_charge = $this->db->get('extra_charge');
        foreach ($extra_charge->result() as $xtra) {
            $total_price += $xtra->charge_value;
            $arr_charge[] = array('name' => $xtra->charge_name,
                'value' => number_format($xtra->charge_value, 0, "", "."));
        }

        //update order
        // $save_data = array('subtotal_price' => $subtotal, 'total_price' => $total_price, 'tax_price' => $tax_price);
        // $this->save('order', $save_data, $order_id);

        $return['order_list'] = $list_order;
        $return['subtotal_value'] = $subtotal;
        $return['subtotal'] = number_format($subtotal, 0, "", ".");
        $return['tax_price'] = $arr_tax;

        $return['extra_charge_price'] = $arr_charge;
        $return['total_price'] = number_format($total_price, 0, "", ".");

        return $return;
    }

    public function kitchen_prn($order_id){

    $sql = "SELECT 
                sum(process_status) as kitten
                FROM order_menu
                WHERE order_id ='" . $order_id . "' ";
    $query = $this->db->query($sql);
    return $query->result_array();

    }

    public function getattendances($params=array()){
     $parameter="";
    if($params['store_id']>0){
      $parameter.=" and s.id='".$params['store_id']."'";
    }
    if($params['user_id']>0){
      $parameter.=" and u.id='".$params['user_id']."'";
    }
    return $this->db->query("
      SELECT
  u.id AS userid,
  s.store_name AS sname,
  u. NAME AS uname,
  date(ha.created_at) AS curdate,
  hsd.start_time AS start_time,
  hsd.end_time AS end_time,
  ha.checkin_time,
  ha.checkout_time,
  ha.over_checkout_time,
  FLOOR(
    sum(
      TIME_TO_SEC(
        IFNULL(
          ha.checkout_time,
          hsd.end_time
        )
      ) - TIME_TO_SEC(ha.checkin_time)
    ) / 3600
  ) jam,
  FLOOR(
    sum(
      TIME_TO_SEC(IFNULL(ha.checkin_time, 0)) - TIME_TO_SEC(hsd.start_time)
    ) / 60
  ) telat,
  FLOOR(
    sum(
      TIME_TO_SEC(
        IFNULL(
          ha.checkout_time,
          ha.over_checkout_time
        )
      ) - TIME_TO_SEC(hsd.end_time)
    ) / 60
  ) over,
  (
    SELECT
      count(

        IF (
          enum_status_attendance = 1,
          1,
          NULL
        )
      )
    FROM
      hr_attendances
    WHERE
      user_id = u.id
  ) AS hadir,
  (
    SELECT
      count(

        IF (
          enum_status_attendance = 6,
          1,
          NULL
        )
      )
    FROM
      hr_attendances
    WHERE
      user_id = u.id
  ) AS cuti,
  (
    SELECT
      count(

        IF (
          enum_status_attendance = 4,
          1,
          NULL
        )
      )
    FROM
      hr_attendances
    WHERE
      user_id = u.id
  ) AS sakit,
  (
    SELECT
      count(

        IF (
          enum_status_attendance = 3,
          1,
          NULL
        )
      )
    FROM
      hr_attendances
    WHERE
      user_id = u.id
  ) + (
    SELECT
      count(

        IF (
          enum_status_attendance = 7,
          1,
          NULL
        )
      )
    FROM
      hr_attendances
    WHERE
      user_id = u.id
  ) AS ijin
FROM
  (`users` u)
JOIN `store` s ON `s`.`id` = `u`.`store_id`
LEFT JOIN `hr_schedules` hs ON `hs`.`user_id` = `u`.`id`
LEFT JOIN `hr_schedule_detail` hsd ON `hsd`.`schedule_id` = `hs`.`id`
JOIN `hr_attendances` ha ON `ha`.`user_id` = `u`.`id`
LEFT JOIN `hr_enum_status_attendance` hesa ON `hesa`.`id` = `ha`.`enum_status_attendance`
WHERE
  date(ha.created_at) >= '".$params['start_date']."'
AND date(ha.created_at) <= '".$params['end_date']."'
".$parameter."
GROUP BY
  `u`.`id`
ORDER BY
  `uname` ASC

    ")->result();
  }

  public function getpayroll($params=array()){
 $parameter="";

    if($params['user_id']>0){
      $parameter.=" and u.id='".$params['user_id']."'";
    }
    return $this->db->query("
SELECT
  u.`name` AS pname,
  hph.id,
  hj.jobs_name,
  RIGHT (hph.period, 4) AS years,
  LEFT (hph.period, 2) AS months,
  (
    SELECT
      sum(q. VALUE)
    FROM
      hr_detail_payroll_history q
    JOIN hr_salary_component w ON w.id = q.component_id
    JOIN hr_payroll_history r ON r.id = q.payroll_history_id
    WHERE
      w.is_enhancer = '1'
    AND r.user_id = u.id
  ) AS total_penerimaan,
  (
    SELECT
      sum(q. VALUE)
    FROM
      hr_detail_payroll_history q
    JOIN hr_salary_component w ON w.id = q.component_id
    JOIN hr_payroll_history r ON r.id = q.payroll_history_id
    WHERE
      w.is_enhancer = '-1'
    AND r.user_id = u.id
  ) AS total_potongan,
  (
    (
      SELECT
        sum(q. VALUE)
      FROM
        hr_detail_payroll_history q
      JOIN hr_salary_component w ON w.id = q.component_id
      JOIN hr_payroll_history r ON r.id = q.payroll_history_id
      WHERE
        w.is_enhancer = '1'
      AND r.user_id = u.id
    ) - (
      SELECT
        sum(q. VALUE)
      FROM
        hr_detail_payroll_history q
      JOIN hr_salary_component w ON w.id = q.component_id
      JOIN hr_payroll_history r ON r.id = q.payroll_history_id
      WHERE
        w.is_enhancer = '-1'
      AND r.user_id = u.id
    )
  ) AS total
FROM
  (`hr_payroll_history` hph)
JOIN `hr_jobs` hj ON `hj`.`id` = `hph`.`jobs_id`
JOIN `users` u ON `hph`.`user_id` = `u`.`id`
WHERE
  `hph`.`period` >= '".$params['start_date']."'
AND `hph`.`period` <= '".$params['end_date']."'
".$parameter."
GROUP BY
  `hph`.`user_id`
ORDER BY
  `pname` ASC
LIMIT 10
    ")->result();
  }
    public function kitchen_order($order_id, $canceled = FALSE, $outlet_id = FALSE, $order_menu_id = FALSE)
    {
        $this->db->select('
            order_menu.*,
            order_menu.quantity as base_quantity,
            IF(category.is_package=1,order_package_menu.quantity,order_menu.quantity) as quantity,
            IF(category.is_package=1,m2.menu_name,`menu`.`menu_name`) as menu_name,
            IF(category.is_package=1,m2.menu_price,`menu`.`menu_price`) as menu_price,
            order_menu.id as order_menu_id,
            `order`.is_delivery,`order`.is_take_away,category.is_package,menu_promo.quantity as package_quantity,
            IF(category.is_package=1,c2.outlet_id,category.outlet_id) as outlet_id,
            m2.is_promo
        ', false);
        $this->db->from('order_menu');
        $this->db->join('order_package_menu', 'order_menu.id = order_package_menu.order_menu_id', 'left');
        $this->db->join('menu_promo', 'order_package_menu.menu_id=menu_promo.package_menu_id and order_menu.menu_id=menu_promo.parent_menu_id', 'left');
        $this->db->join('menu as m2', 'm2.id = order_package_menu.menu_id', 'left');
        $this->db->join('order', 'order.id = order_menu.order_id');
        $this->db->join('menu', 'menu.id = order_menu.menu_id');
        $this->db->join('category', 'category.id = menu.category_id');
        $this->db->join('category as c2', 'c2.id = m2.category_id', 'left');
        $this->db->where('order_id', $order_id);
        if ($canceled === TRUE) {
            $this->db->where('IF(category.is_package = 1,order_package_menu.`cooking_status`,order_menu.`cooking_status`) !=4');
        }
        if ($outlet_id) {
            $this->db->where('IF(category.is_package = 1,c2.outlet_id,category.outlet_id)="' . $outlet_id . '"');
        }

        if ($order_menu_id) {
            $this->db->where('order_menu.id', $order_menu_id);
        } else {
            $this->db->where('kitchen_status', '0');
        }

        $result = $this->db->get();

        $list_order = array();
        $subtotal = 0;
        foreach ($result->result() as $option) {
            $sidedish_order = array();
            $options_order = array();
            if ($outlet_id && $option->is_package == 0) {
                $update_array = array('kitchen_status' => '1');
                $save = $this->order_model->save('order_menu', $update_array, $option->order_menu_id);
            }
            $order_menu_sidedish = $this->get_side_dish_by_order_menu($option->order_menu_id, $option->is_promo);
            $order_menu_option = $this->get_option_by_order_menu($option->order_menu_id);
            if (!empty($order_menu_sidedish)) {
                foreach ($order_menu_sidedish as $side) {
                    $side_price = ($option->quantity * $side->price);
                    $subtotal += $side_price;
                    $sideThing = new stdClass();
                    $sideThing->name = $side->name;
                    $sideThing->id = $side->id;
                    $sideThing->quantity = $option->quantity;
                    $sideThing->origin_price = $side->price;
                    $sideThing->price = number_format($side_price, 0, "", ".");
                    $sidedish_order[] = $sideThing;
                }
            }

            if (!empty($order_menu_option)) {

                foreach ($order_menu_option as $opt) {
                    $optx = $this->get_one('menu_option_value', $opt->menu_option_value_id);
                    if ($optx) {
                        $option_name = $this->get_one('menu_option', $optx->option_id);
                        $optionThing = new stdClass();
                        $optionThing->option_name = $option_name->option_name;
                        $optionThing->option_value_name = $optx->option_value_name;
                        $options_order[] = $optionThing;
                    }
                }
            }


            $order_menu = new stdClass();
            $order_menu->id = $option->id;
            $order_menu->order_id = $option->order_id;
            $order_menu->menu_id = $option->menu_id;
            $order_menu->is_package = $option->is_package;
            $order_menu->base_quantity = $option->base_quantity;
            $order_menu->package_quantity = $option->package_quantity;
            $order_menu->outlet_id = $option->outlet_id;
            $order_menu->quantity = $option->quantity;
            $order_menu->options = $options_order;
            $order_menu->side_dishes = $sidedish_order;
            $order_menu->note = $option->note;
            $order_menu->cooking_status = $option->cooking_status;
            $order_menu->menu_name = $option->menu_name;
            $order_menu->is_delivery = $option->is_delivery;
            $order_menu->is_take_away = $option->is_take_away;
            $order_menu->dinein_takeaway = $option->dinein_takeaway;
            $order_menu->order_menu_id = $option->order_menu_id;
            $order_menu->side_dish_list = $sidedish_order;
            $order_menu->option_list = $options_order;
            $order_menu->printer_name = "Outlet 1";
            $list_order[] = $order_menu;
        }

        return $list_order;
    }

    public function update_status_cooking_canceled($table_id, $data)
    {
        // $this->db->where('order.table_id', $table_id)->update('order_menu', $data);
        $this->db->where('order.table_id', $table_id)->update('order_menu join `order` on `order`.id = order_menu.order_id', $data);

        return ($this->db->affected_rows() > 0);
    }

    public function save_order_history($order_id)
    {
        $this->db->select('order_menu.*,
            store.id as id_store, store.store_name,store.store_address,store.store_phone,
            outlet.id as id_outlet, outlet.outlet_name,
            category.id as id_category, category.category_name,
            menu.menu_name, menu.menu_hpp, menu.menu_price, 
            order_menu.id as order_menu_id');
        $this->db->from('order_menu');
        $this->db->join('menu', 'menu.id = order_menu.menu_id');
        $this->db->join('category', 'category.id = menu.category_id');
        $this->db->join('outlet', 'outlet.id = category.outlet_id');
        $this->db->join('store', 'store.id = outlet.store_id');
        $this->db->where('order_id', $order_id);
        $this->db->where('cooking_status !=', '4');
        $this->db->where('cooking_status !=', '0');
        $this->db->where('cooking_status !=', '6');

        $result = $this->db->get();

        $list_order = array();
        $subtotal = 0;
        $global_store_id = 0;
        $global_store_name = '';
        foreach ($result->result() as $option) {

            $sidedish_order = array();
            $options_order = array();
            $subtotal += ($option->quantity * $option->menu_price);
            $basic_price = $option->menu_price * $option->quantity;

            if (!empty($option->side_dishes)) {
                $sidedish = explode(',', $option->side_dishes);
                foreach ($sidedish as $value) {
                    $side = $this->get_one('menu_side_dish', $value);
                    if ($side) {
                        $side_price = ($option->quantity * $side->side_dish_price);
                        $subtotal += $side_price;
                        $sideThing = new stdClass();
                        $sideThing->side_dish_name = $side->side_dish_name;
                        $sideThing->side_dish_hpp = $side->side_dish_hpp;
                        $sideThing->side_dish_price = $side->side_dish_price;
                        $sideThing->menu_id = $side->menu_id;
                        $sideThing->sequence = $side->sequence;
                        $sideThing->quantity = $option->quantity;
                        $sideThing->total_price = round($side_price, 0, PHP_ROUND_HALF_UP);
                        $sidedish_order[] = $sideThing;
                    }
                }
            }

            if (!empty($option->options)) {
                $opts = explode(',', $option->options);
                foreach ($opts as $opt) {
                    $optx = $this->get_one('menu_option_value', $opt);
                    if ($optx) {
                        $opParent = $this->get_one('menu_option', $optx->option_id);

                        $optThing = new stdClass();
                        $optThing->option_value_name = $optx->option_value_name;
                        $optThing->option_id = $optx->option_id;
                        $optThing->option_name = $opParent->option_name;
                        $optThing->menu_id = $opParent->menu_id;
                        $optThing->option_value_sequence = $optx->sequence;
                        $options_order[] = $optThing;
                    }
                }
            }

            $menu_ingredient = $this->get_menu_ingredient($option->menu_id, $option->id_outlet);
            foreach ($menu_ingredient as $key => $row) {
                $row->quantity *= $option->quantity;
                $data_stock_transaction = array(
                    'order_id' => $order_id,
                    'inventory_stock_id' => $row->inventory_stock_id,
                    'total' => $row->quantity,
                    // 'date' => date('Y-m-d')
                );
                $this->order_model->save('inventory_stock_transaction', $data_stock_transaction);
            }

            $order_menu = new stdClass();
            $order_menu->id = $option->id;
            $order_menu->order_id = $option->order_id;
            $order_menu->menu_id = $option->menu_id;
            $order_menu->quantity = $option->quantity;
            $order_menu->options = $option->options;
            $order_menu->side_dishes = $option->side_dishes;
            $order_menu->note = $option->note;
            $order_menu->cooking_status = $option->cooking_status;
            $order_menu->menu_name = $option->menu_name;
            $order_menu->menu_hpp = $option->menu_hpp;
            $order_menu->menu_price = $option->menu_price;
            $order_menu->category_id = $option->id_category;
            $order_menu->category_name = $option->category_name;
            $order_menu->outlet_id = $option->id_outlet;
            $order_menu->outlet_name = $option->outlet_name;
            $order_menu->store_id = $option->id_store;
            $order_menu->store_name = $option->store_name;
            $order_menu->store_address = $option->store_address;
            $order_menu->store_phone = $option->store_phone;
            $order_menu->order_menu_id = $option->order_menu_id;
            $order_menu->side_dish_list = $sidedish_order;
            $order_menu->option_list = $options_order;

            $this->load->model('inventory_model');
            $order_menu->stock_transaction_list = $this->inventory_model->get_inventory_stock_transaction_byorder($option->order_id);

            $list_order[] = $order_menu;

            $global_store_id = $option->id_store;
            $global_store_name = $option->store_name;
        }

        $tax_price = 0;
        $count_tax = 0;
        $arr_tax = array();
        $taxes = $this->db->get('taxes');
        foreach ($taxes->result() as $tax) {
            $count_tax = (($subtotal * $tax->tax_percentage) / 100);
            $tax_price += $count_tax;
            $arr_tax[] = array('tax_name' => $tax->tax_name,
                'tax_percentage' => $tax->tax_percentage,
                'total_tax' => round($count_tax, 0, PHP_ROUND_HALF_UP));
        }
        $total_price = $subtotal + $tax_price;


        $arr_charge = array();
        $extra_charge = $this->db->get('extra_charge');
        foreach ($extra_charge->result() as $xtra) {
            $total_price += $xtra->charge_value;
            $arr_charge[] = array('charge_name' => $xtra->charge_name,
                'charge_value' => $xtra->charge_value,
                'total_extra_charge' => round($xtra->charge_value, 0, PHP_ROUND_HALF_UP));
        }

        $arr_order_data = array();
        $order_table = $this->get_one('order', $order_id);
        if ($order_table) {
            if ($order_table->cashier_id == 0) {
                $cashier_id = $order_table->waiter_id;
            } else {
                $cashier_id = $order_table->cashier_id;

            }
            $cashier_data = $this->get_one('users', $cashier_id);
            $waiter_data = $this->get_one('users', $order_table->waiter_id);


            $secondThing = new stdClass();
            if ($order_table->table_id == '0') {
                $secondThing->order_type = 'Takeaway';
                $secondThing->table_id = 0;
                $secondThing->table_name = '';
                $secondThing->floor_id = 0;
                $secondThing->floor_name = '';
                $secondThing->store_id = $global_store_id;
                $secondThing->store_name = $global_store_name;
                $secondThing->customer_count = 0;
            } else {
                $table_data = $this->get_one('table', $order_table->table_id);
                $floor_data = $this->get_one('floor', $table_data->floor_id);
                $store_data = $this->get_one('store', $floor_data->store_id);
                $secondThing->order_type = 'Dine In';
                $secondThing->table_id = $table_data->id;
                $secondThing->table_name = $table_data->table_name;
                $secondThing->floor_id = $floor_data->id;
                $secondThing->floor_name = $floor_data->floor_name;
                $secondThing->store_id = $store_data->id;
                $secondThing->store_name = $store_data->store_name;
                $secondThing->customer_count = $table_data->customer_count;
            }


            $secondThing->receipt_id = $order_table->receipt_id;
            $secondThing->cashier_id = $order_table->cashier_id;
            $secondThing->cashier_name = $cashier_data->name;
            $secondThing->waiter_id = $order_table->waiter_id;
            $secondThing->waiter_name = ($waiter_data) ? $waiter_data->name : '';
            $secondThing->waiter_id = $order_table->waiter_id;
            $secondThing->waiter_id = $order_table->waiter_id;

            switch ($order_table->payment_method) {
                case 1:
                    $secondThing->payment_method = 'CASH';
                    break;
                case 2:
                    $secondThing->payment_method = 'DEBIT';
                    break;
                case 3:
                    $secondThing->payment_method = 'CREDIT';
                    break;
                default:
                    $secondThing->payment_method = 'CASH';
                    break;
            }

            $secondThing->payment_card_id = $order_table->payment_card_id;
            $secondThing->customer_name = $order_table->customer_name;
            $secondThing->order_date = $order_table->order_date;
            $secondThing->order_end = $order_table->order_end;

            $arr_order_data[] = $secondThing;
        }

        $return['order_data'] = $arr_order_data;
        $return['order_list'] = $list_order;
        $return['subtotal'] = round($subtotal, 0, PHP_ROUND_HALF_UP);
        $return['tax_price'] = $arr_tax;
        $return['extra_charge_price'] = $arr_charge;
        $return['total_price'] = round($total_price, 0, PHP_ROUND_HALF_UP);

        $save_history = array('order_id' => $order_id,
            'history' => json_encode($return));
        $this->save('order_history', $save_history);
    }

    public function get_data_table($order_id = 0, $table_id = FALSE)
    {
        $this->db->select(
            'table.id,
            table.id as table_id,
            table.table_name,
            table.table_status,
            table.customer_count,
            table.table_shape,
            order.id as order_id,
            enum_table_status.status_name,
            table.floor_id,
            floor.floor_name,
            order.customer_name,
						order.customer_phone,
						order.customer_address,
						order.reservation_id
						')
            ->from('table')
            ->join('enum_table_status', 'table.table_status = enum_table_status.id')
            ->join('floor', 'floor.id = table.floor_id');

        $join_type = 'left';
        if ($order_id > 0) {
            $join_type = 'right';
            $this->db->where('order.id', $order_id);
        }

        $this->db->join('order', 'table.id = order.table_id and order_status=0 and date(start_order)<=current_date()', $join_type);

        if ($table_id) {
            $this->db->where('table.id', $table_id);
        }
        $this->db->order_by('order.id', 'desc');
        return $this->db->get()->row();
    }

    public function get_role_staff($user_id)
    {
        $this->db->select('groups.id,users.name,group_concat(feature.key) as feature_accessed')
            ->from('groups')
            ->join('feature_access', 'groups.id=feature_access.groups_id')
            ->join('feature', 'feature.id=feature_access.feature_id')
            ->join('users_groups', 'users_groups.group_id = groups.id')
            ->join('users', 'users.id = users_groups.user_id')
            ->where('users_groups.user_id', $user_id)->group_by("users.id");

        return $this->db->get()->result();
    }

    public function get_outlet_id_by_order_id($order_id)
    {
        return $this->db->query("
            SELECT
                o.id AS outlet_id,
                o.outlet_name
            FROM order_menu om
            LEFT JOIN order_package_menu opm ON opm.order_menu_id = om.id
            INNER JOIN menu m ON IF(opm.id IS NOT NULL,opm.menu_id,om.menu_id)= m.id
            JOIN category c ON m.category_id = c.id
            JOIN outlet o ON c.outlet_id = o.id
            WHERE om.order_id = '" . $order_id . "'
            GROUP BY c.outlet_id
        ")->result();
    }

    public function get_outlet_by_menu_id($menu_id = 0)
    {
        $this->db->select('outlet_id, outlet_name,account_id')
            ->from('category')
            ->join('menu', 'menu.category_id = category.id')
            ->join('outlet', 'category.outlet_id = outlet.id')
            ->where('menu.id', $menu_id);

        return $this->db->get()->row();
    }

    public function get_count_cooking_status_order($order_id, $status)
    {
        $this->db->select('count(id) as quantity from order_menu where order_id = ' . $order_id . ' AND cooking_status = ' . $status . '');
        return $this->db->get()->row();
    }

    /**
     * return quantity of order menu by cooking status
     * @param  int $order_id
     * @param  boolean $status [get by status if defined]
     *
     * @return [array]
     *
     * @author fkartika
     */
    public function get_quantity_cooking_status_order_menu($order_id, $status = FALSE)
    {
        $return_data = array();
        $where = '';
        if ($status) {
            $where = ' AND cooking_status = ' . $status;
        }
        $this->db->select('count(o.id) as quantity, o.cooking_status  
            from order_menu o where o.order_id = ' . $order_id . ' ' . $where . '  
            group by o.cooking_status
            ');
        $data = $this->db->get()->result();
        foreach ($data as $key => $row) {
            $return_data[$row->cooking_status] = $row->quantity;
        }
        return $return_data;
    }

    public function get_table_by_order_id($order_id)
    {
        $this->db->select('`table`.* from `table`
            join `order` on `table`.id = `order`.table_id
            where `order`.id = ' . $order_id . '');
        return $this->db->get()->row();
    }

    public function get_parent_table_merge($table_id)
    {
        $sql = "SELECT 
                t.table_name as parent_name, 
                t.id as parent_id

                FROM `table` t
                JOIN table_merge tm ON t.id = tm.parent_id
                WHERE tm.table_id =" . $table_id . " ";
        $query = $this->db->query($sql);
        return $query->row();
    }

    public function get_merge_table_byparent($parent_id)
    {
        $sql = "SELECT 
                t.id, 
                t.table_name,
                t.table_shape
                FROM table_merge tm
                JOIN `table` t ON t.id = tm.table_id

                WHERE tm.parent_id ='" . $parent_id . "' ";
        $query = $this->db->query($sql);
        return $query->result();
    }

    function get_merge_tablename_byparent($parent_id)
    {
        $result = array();
        $sql = "SELECT 
                t.id, 
                t.table_name

                FROM table_merge tm
                JOIN `table` t ON t.id = tm.table_id

                WHERE tm.parent_id =" . $parent_id . " ";

        $query = $this->db->query($sql);
        foreach ($query->result() as $row) {
            $result[] = $row->table_name;
        }

        return $result;
    }

    function check_reset_cooking_status($order_id)
    {
        $sql = "SELECT id from order_menu m
        where
                (
                   m.cooking_status= 1
                   or m.cooking_status= 2
                   or m.cooking_status= 3
               )
        AND
        m.order_id = " . $order_id . " ";

        $query = $this->db->query($sql);
        return $query->num_rows();
    }


    function get_menu_ingredient($menu_id = FALSE, $outlet_id = FALSE)
    {

        $this->db
            ->select('id as stock_id, 
                    sum(quantity) as stock_quantity,
                    inventory_id, outlet_id, uom_id')
            ->from('stock')
            ->where('quantity >=', 0)
            ->group_by('inventory_id')
            ->group_by('uom_id')
            ->group_by('outlet_id');

        $subquery = $this->db->_compile_select();

        $this->db->_reset_select();


        $this->db->select('m.*, c.outlet_id, t1.*, min(floor(t1.stock_quantity/m.quantity)) as total_available')
            ->from('menu_ingredient as m')
            ->join('menu', 'menu.id = m.menu_id')
            ->join('category c', 'c.id = menu.category_id')
            ->join("($subquery) t1", "t1.outlet_id = c.outlet_id AND t1.inventory_id = m.inventory_id AND t1.uom_id = m.uom_id");
        if ($menu_id) {
            $this->db->where('m.menu_id', $menu_id);
        }
        if ($outlet_id) {
            $this->db->where('c.outlet_id', $outlet_id);

        }
        $this->db->order_by('sequence', 'ASC');

        $result = $this->db->get()->result();

        return $result;

    }


    function decrease_inventory_stock($menu_ingredient, $menu_count)
    {

    }

    function increase_inventory_stock($menu_ingredient, $menu_count)
    {

    }

    function get_menu_outlet($outlet_id)
    {
        $menu_outlet = $this->paged_join('menu', 'category', 'menu.id, category.outlet_id, menu.menu_quantity',
            'menu.category_id = category.id',
            0, 0, array('category.outlet_id' => $outlet_id,
                'available' => 1), 'menu_name ASC');
        return $menu_outlet->result();
    }

    public function all_menu_ingredient_with_stock($data_menu)
    {

        if (!empty($data_menu)) {
            foreach ($data_menu as $key => $row) {
                $this->one_menu_ingredient_with_stock($row);

            }

        }

    }

    public function one_menu_ingredient_with_stock($data)
    {
        if (is_object($data)) {
            $menu_id = $data->id;
            $new_obj = $data;
        } else {
            $new_obj = new stdClass();
            $menu_id = $data;

        }
        $new_obj->ingredient = array();
        $new_obj->ingredient = $this->get_menu_ingredient($menu_id);
        $new_obj->total_available = 0;
        if ($new_obj->ingredient[0]->total_available)
            $new_obj->total_available = $new_obj->ingredient[0]->total_available;
        return $new_obj;
    }

    public function get_available_stock($arr_ingredient)
    {
        $array_available = array();
        $today = date('Y-m-d');

        foreach ($arr_ingredient as $j => $row) {

            $where = array(
                'outlet_id' => $row->outlet_id,
                'inventory_id' => $row->inventory_id,
            );

            $stock = 0;
            $inventory_stock = $this->get_all_where('stock', $where);

            $stock = json_encode($inventory_stock);

            if (!empty($inventory_stock)) {
                $stock = $inventory_stock[0]->quantity;
            }


            $row->stock = $stock;

            $quantity = $row->quantity;
            if ($quantity != 0)
                $array_available[] = floor($stock / $quantity);
            else
                $array_available[] = $stock;

        }

        if (!empty($array_available)) {
            return min($array_available);
        } else {
            return 0;
        }
    }

    function get_all_order_in_table($table_id)
    {
        $this->db->select('*')
            ->from('order')
            ->where('table_id', $table_id)
            ->where('order_status', 0)
            ->where('end_order is null');

        return $this->db->get()->result();
    }

    function is_split_order($table_id)
    {
        $order = $this->get_all_order_in_table($table_id);
        if (sizeof($order > 1)) {
            return TRUE;
        }
        return FALSE;
    }

    function get_order_by_menu($menu_id)
    {

    }

    function get_order_combine($order_id)
    {
        $this->db->select('order.id as order_id, table.table_name,')
            ->from('order')
            ->join('table', 'order.table_id = table.id')
            ->where('table.table_status !=', '2')
            ->where('table.table_status !=', '5')
            ->where('order.id !=', $order_id)
            ->order_by('order.id', 'desc');
        return $this->db->get()->result();
    }

    function get_voucher_bycode($code)
    {

        $qry = $this->db->select('voucher.id, voucher_group.amount,voucher_group.minimum_order,
            voucher_group.start_valid_date, voucher.expire_date, 
            voucher.voucher_group_id,
            voucher_group.is_available_all_store')
            ->from('voucher')
            ->join('voucher_group', 'voucher_group.id = voucher.voucher_group_id')
            ->where('code', $code)
            ->where('voucher.status', 0)
            ->where('voucher.expire_date >= ', date('Y-m-d'));

        return $qry->get()->row();
    }

    function calculate_menu_hpp($order_id)
    {

        $query = $this->db->query('
        select sum(o.quantity * m.menu_hpp) as menu_hpp
        from order_menu o
        join menu m
        on o.menu_id = m.id
        where o.order_id = ' . $order_id . '
        ');

        $result = $query->row();
        if ($result) {
            return $result->menu_hpp;
        }
        return 0;
    }

    function update_voucher_with_limit($where, $limit)
    {
        $this->db->where($where);
        $this->db->limit($limit);
        $this->db->update('voucher', array("status" => 1));
        return ($this->db->affected_rows() > 0);
    }

    function get_compliment_usage($user_id, $start_date, $end_date = FALSE, $reset_period)
    {
        $this->db->select('sum(amount) as amount')
            ->from('compliment_usage')
            ->where('user_id', $user_id);
        if ($reset_period == 1) {
            $this->db->where('date(created_at)', $start_date);
        } else {
            $this->db->where('date(created_at) >=', $start_date);
            $this->db->where('date(created_at) <=', $end_date);
        }

        $res = $this->db->get();
        if ($res->num_rows() > 0) {
            $res = $res->row()->amount;
            if ($res) {
                return $res;
            } else {
                return 0;
            }
        }
        return 0;


    }

    function get_compliment_by_username($id)
    {

        $qry = $this->db->select(
            'users.id, c.is_available_all_store, is_cogs, cogs_limit, 
            is_discount, discount, discount_limit, reset_period, users.name
            ')
            ->from('compliment c')
            ->join('users', 'users.id = c.user_id')
            ->where('c.user_id', $id);
        return $qry->get()->row();


    }


    function is_table_merge($table_id)
    {

        $qry = $this->db->select('*')
            ->from('table_merge')
            ->where('parent_id', $table_id)
            ->or_where('table_id', $table_id);
        return $qry->get()->row();


    }

    public function get_bill_by_payment_date($open_at, $close_at)
    {
        $this->db->select('*  ');
        $this->db->from('bill')
            ->where('is_refund', 0)
            ->where('payment_date >= ', $open_at)
            ->where('payment_date <= ', $close_at);
        $result = $this->db->get()->result();
        return $result;
    }

    public function get_sum_bill_payment($open_at, $close_at)
    {
        $sql = 'SELECT count(id) as total_transaction, 
                sum(total_price) as total_cash
            from bill 
            where bill.is_refund = 0 and bill.payment_date >= "' . $open_at . '"
            AND bill.payment_date <= "' . $close_at . '"
            ';
        $query = $this->db->query($sql);
        return $query->row();
    }

    public function get_sum_bill_payment_byoption($open_at, $close_at, $payment_option = FALSE)
    {
        $where = "";
        if ($payment_option) {
            $where = "and p1.payment_option=" . $payment_option;
        }
        $sql = 'SELECT sum(p1.amount) as total
                
            from bill_payment p1
            join bill on bill.id = p1.bill_id
            where bill.is_refund = 0 and bill.payment_date >= "' . $open_at . '"
            AND bill.payment_date <= "' . $close_at . '"
            ' . $where . '
            GROUP BY p1.payment_option
            ';
        $query = $this->db->query($sql)->row();
        if ($query) {
            return $query->total;
        }
        return 0;
    }

    public function get_sum_bill_information($open_at, $close_at, $payment_option = FALSE)
    {
        $sql = 'SELECT sum(p1.amount) as total, p1.info
                
            from bill_information p1
            join bill on bill.id = p1.bill_id
            where bill.is_refund = 0 and bill.payment_date >= "' . $open_at . '"
            AND bill.payment_date <= "' . $close_at . '"
            GROUP BY p1.info
            ';
        $query = $this->db->query($sql);
        return $query->result();
    }


    public function calculate_open_close_bill($bill_id)
    {
        $this->db->select('bill_menu.*,menu.menu_name, 
            menu.menu_price, 
            bill_menu.id as bill_menu_id
             ');
        $this->db->from('bill_menu');
        $this->db->join('bill', 'bill.id = bill_menu.bill_id');
        $this->db->join('menu', 'menu.id = bill_menu.menu_id', 'left')
            ->where('bill.is_refund', 0)
            ->where('bill_menu.bill_id', $bill_id);

        $result = $this->db->get();
        $list_order = array();
        $subtotal = 0;
        foreach ($result->result() as $option) {
            $sidedish_order = array();
            $subtotal += ($option->quantity * $option->menu_price);
            $basic_price = $option->menu_price * $option->quantity;
            $order_menu_sidedish = $this->order_model->get_side_dish_bill_by_order_menu($option->id);

            if (!empty($order_menu_sidedish)) {
                // $sidedish = explode(',', $option->side_dishes);
                foreach ($order_menu_sidedish as $side) {
                    // $side = $this->get_one('menu_side_dish', $value);
                    // if ($side) {
                    $side_price = ($option->quantity * $side->price);
                    $subtotal += $side_price;
                    //$basic_price += $side_price;
                    $sideThing = new stdClass();
                    $sideThing->name = $side->name;
                    $sideThing->quantity = $option->quantity;
                    $sideThing->price = number_format($side_price, 0, "", ".");
                    $sidedish_order[] = $sideThing;
                    // }
                }
            }


            $order_menu = new stdClass();
            $order_menu->id = $option->id;
            $order_menu->menu_id = $option->menu_id;
            $order_menu->quantity = $option->quantity;
            $order_menu->menu_name = $option->menu_name;
            $order_menu->menu_price = number_format($basic_price, 0, "", ".");
            $order_menu->side_dish_list = $sidedish_order;
            $order_menu->origin_price = $option->menu_price;
            $list_order[] = $order_menu;
        }


        $return['order_list'] = $list_order;
        return $list_order;
    }

    public function calculate_total_order_bill($order_taxes, $order_id, $canceled = FALSE, $is_cogs_inventory = FALSE, $is_checkout = FALSE)
    {
        // if count cogs use menu
        $menu_cogs = "menu.menu_hpp";

        // if count cogs use inventory
        if ($is_cogs_inventory === TRUE) {
            $menu_cogs = "order_menu_inventory_cogs.cogs as menu_hpp";
        }
        $this->db->select('order_menu.id, order_menu.order_id, order_menu.menu_id, order_menu.quantity,
			order_menu.waiter_id, order_menu.process_status, order_menu.cooking_status, order_menu.kitchen_status,
			order_menu.note, order_menu.created_at, order_menu.created_by, order_menu.dinein_takeaway,
			order_menu.is_check, order_menu.finished_at, order_menu.post_to, 
			menu.menu_name, menu.menu_price, menu.category_id,'.$menu_cogs.',category.outlet_id,outlet.outlet_name,outlet.store_id,store.store_name,
            order_menu.id as order_menu_id, menu.use_taxes, menu.is_promo,
            enum_cooking_status.status_name,users.name as waiter_name,
            bill.receipt_number,bill.customer_count,
            (
                select id from order_menu om where om.order_id=order_menu.order_id and om.created_at < order_menu.created_at limit 0,1
            ) as is_additional,
            (
                select DATE_FORMAT(min(order_menu.created_at), "%H:%i") from order_menu where order_id = '.$order_id.'
            ) as start_cooking,
            (
                select DATE_FORMAT(max(order_menu.finished_at), "%H:%i") from order_menu where order_id = '.$order_id.'
            ) as end_cooking
        ', false);
        $this->db->from('order_menu');
        $this->db->join('bill', 'bill.order_id = order_menu.order_id', 'left');
        $this->db->join('users', 'users.id = order_menu.created_by', 'left');
        $this->db->join('menu', 'menu.id = order_menu.menu_id');
        $this->db->join('category', 'category.id = menu.category_id', 'left');
        $this->db->join('outlet', 'outlet.id = category.outlet_id', 'left');
        $this->db->join('store', 'store.id = outlet.store_id', 'left');
        $this->db->join('enum_cooking_status', 'enum_cooking_status.id = order_menu.cooking_status');

        // if count cogs use inventory
        if ($is_cogs_inventory === TRUE) {
            $this->db->join('order_menu_inventory_cogs', 'order_menu_inventory_cogs.order_menu_id = order_menu.id', 'left');
        }
        
        $this->db->where('order_menu.order_id', $order_id);
        
        if ($canceled === TRUE) {
            $this->db->where('cooking_status !=', '4');
            $this->db->where('cooking_status !=', '0');
            $this->db->where('cooking_status !=', '6');
        }
        $this->db->group_by("order_menu.id");
        $this->db->order_by("order_menu.created_at", "asc");
        $result = $this->db->get();

        $list_order = array();
        $subtotal = 0;
        $subtotal_non_taxes = 0;
        $total_hpp = 0;
        $temp = array();
        $temp_side_dish = array();
        foreach ($result->result() as $option) {
            $sidedish_order = array();
            $options_order = array();
            $total_hpp += $option->menu_hpp * $option->quantity;
            $basic_price = $option->menu_price * $option->quantity;
            $order_menu_option = $this->get_option_by_order_menu($option->id);

            if (!empty($order_menu_option)) {
                foreach ($order_menu_option as $opt) {
                    $optx = $this->get_one('menu_option_value', $opt->menu_option_value_id);
                    if ($optx) {
                        $option_name = $this->get_one('menu_option', $optx->option_id);
                        $optionThing = new stdClass();
                        $optionThing->option_name = $option_name->option_name;
                        $optionThing->option_value_name = $optx->option_value_name;
                        $options_order[] = $optionThing;
                    }
                }
            }

            if ($this->data['setting']['dining_type'] == 3 && $is_checkout == FALSE) {
                $billing_qty = new stdClass();
                $billing_qty->quantity_bill = 0;
            } else {
                $billing_qty = $this->db->query("select sum(bm.quantity) as quantity_bill from bill_menu bm join bill b on b.id = bm.bill_id where b.is_refund = 0 and bm.order_menu_id = '" . $option->id . "' group by bm.menu_id")->row();
            }
            
            $option->is_additional = ($option->is_additional > 0 ? 1 : 0);
            $order_menu = new stdClass();
            $order_menu->id = $option->id;
            $order_menu->order_id = $option->order_id;
            $order_menu->menu_id = $option->menu_id;
            $order_menu->quantity = $option->quantity - (sizeof($billing_qty) > 0 ? $billing_qty->quantity_bill : 0);
            $order_menu->note = $option->note;
            $order_menu->cooking_status = $option->cooking_status;
            $order_menu->process_status = $option->process_status;
            $order_menu->menu_name = $option->menu_name;
            $order_menu->category_id = $option->category_id;
            $order_menu->order_menu_id = $option->order_menu_id;
            $order_menu->side_dish_list = $sidedish_order;
            $order_menu->cooking_status_name = $option->status_name;
            $order_menu->option_list = $options_order;
            $order_menu->origin_price = $option->menu_price;
            $order_menu->menu_hpp = $option->menu_hpp;
            $order_menu->menu_price = 0;
            $order_menu->store_id = $option->store_id;
            $order_menu->store_name = $option->store_name;
            $order_menu->outlet_id = $option->outlet_id;
            $order_menu->outlet_name = $option->outlet_name;
            $order_menu->receipt_number = $option->receipt_number;
            $order_menu->customer_count = $option->customer_count;
            $order_menu->dinein_takeaway = (int)$option->dinein_takeaway;
            $order_menu->start_cooking = $option->start_cooking;
            $order_menu->end_cooking = $option->end_cooking;
            $order_menu->use_taxes = (int)$option->use_taxes;
            $order_menu->waiter_name = $option->waiter_name;
            $order_menu->is_additional = $option->is_additional;
            if ($order_menu->quantity > 0) {
                $done_payment = FALSE;
                $side_price = 0;
                $subtotal += ($order_menu->quantity * $option->menu_price);
                if ($order_menu->use_taxes == 0) {
                    $subtotal_non_taxes += ($order_menu->quantity * $option->menu_price);
                }
                $order_menu_sidedish = $this->get_side_dish_by_order_menu($option->id, $option->is_promo);

                $temp_side_dish[$option->id] = $option->menu_id;
                if (!empty($order_menu_sidedish)) {

                    foreach ($order_menu_sidedish as $side) {
                        $temp_side_dish[$option->id] .= $side->side_dish_id;
                        $side_price += $side->price;
                        $sideThing = new stdClass();
                        $sideThing->name = $side->name;
                        $sideThing->id = $side->id;
                        $sideThing->quantity = $option->quantity - (sizeof($billing_qty) > 0 ? $billing_qty->quantity_bill : 0);
                        $sideThing->origin_price = $side->price;
                        $sideThing->price = number_format($side_price, 0, "", ".");
                        $sidedish_order[] = $sideThing;
                    }
                    $subtotal += ($side_price * $order_menu->quantity);
                    if ($order_menu->use_taxes == 0) {
                        $subtotal_non_taxes += ($side_price * $order_menu->quantity);
                    }

                }

                $order_menu->side_dish_list = $sidedish_order;
                $option->menu_price += $side_price;
                $order_menu->origin_price = $option->menu_price;
                $order_menu->menu_price = ($order_menu->quantity * $option->menu_price);
                $list_order[$option->id] = $order_menu;
            }


        }
        $new_list_order = array();
        // grouping bill with the same menu id and sidedish
        // only group bill in checkout bill
        if ($canceled) {
            $had_same_sidedish = $this->get_keys_for_duplicate_values($temp_side_dish);
            $counter = 0;
            foreach ($had_same_sidedish as $key => $row) {
                foreach ($row as $key2 => $row2) {
                    if (isset($new_list_order[$counter])) {
                        $new_list_order[$counter]->quantity += $list_order[$row2]->quantity;
                        $new_list_order[$counter]->menu_price += $list_order[$row2]->menu_price;
                    } else {
                        $new_list_order[$counter] = $list_order[$row2];
                    }
                }
                $counter++;
            }
            unset($list_order);
        } else {
            $new_list_order = $list_order;
            unset($list_order);
        }

        $tax_price = 0;
        $non_tax_price = 0;
        $count_tax = 0;
        $count_non_tax = 0;
        $temp_subtotal = 0;
        $total_services = 0;
        $subtotal_taxes = $subtotal - $subtotal_non_taxes;
        $arr_tax = array();
        $order_type = 1;
        $order = $this->order_model->get_all_where('order', array('id' => $order_id));
        foreach ($order as $key) {
            if ($key->is_take_away == 1) {
                $order_type = 2;
            } elseif ($key->is_delivery == 1) {
                $order_type = 3;
            }
        }
        $tax_method = $this->data['setting']['tax_service_method'];
        $taxes = $this->tax_model->get_taxes($order_type, $tax_method, 1);
        foreach ($taxes as $key) {
            if ($key->is_service == 1) {
                $total_services += (($subtotal * $key->tax_percentage) / 100);
            }
        }
        foreach ($order_taxes as $tax) {
            if ($tax_method == 1) {
                $count_tax = (($subtotal * $tax->tax_percentage) / 100);
                $count_non_tax = (($subtotal_non_taxes * $tax->tax_percentage) / 100);
            } else {
                if ($tax->is_service == 1) {
                    $total_services = (($subtotal_taxes * $tax->tax_percentage) / 100);
                    $count_tax = $total_services;
                    $count_non_tax = 0;
                } else {
                    $temp_subtotal = $subtotal_taxes + $total_services;
                    $count_tax = (($temp_subtotal * $tax->tax_percentage) / 100);
                    $count_non_tax = 0;
                }
            }
            $tax_price += $count_tax;
            $non_tax_price += $count_non_tax;
                
            $arr_tax[] = array('name' => $tax->tax_name . ' (' . $tax->tax_percentage . '%)',
                'id' => $tax->tax_id,
                'origin_name' => $tax->tax_name,
                'value' => number_format($count_tax - $count_non_tax, 0, "", "."),
                'tax_percentage' => $tax->tax_percentage,
                'account_id' => $tax->account_id,
                'is_service'  => $tax->is_service
            );
        }
        $total_price = $subtotal + $tax_price - $non_tax_price;

        $arr_charge = array();
        $extra_charge = $this->db->get('extra_charge');
        foreach ($extra_charge->result() as $xtra) {
            $total_price += $xtra->charge_value;
            $arr_charge[] = array('name' => $xtra->charge_name,
                'value' => number_format($xtra->charge_value, 0, "", "."));
        }
        $order_data = $this->order_model->get_one('order', $order_id);
        $reservation = array();
        if (sizeof($order_data) > 0) {
            $reservation = $this->order_model->get_one("reservation", $order_data->reservation_id);
        }
        if ($order_data->is_delivery == 1) {
            $return['delivery_cost'] = ($this->data['setting']['delivery_company'] != 1) ? $order_data->delivery_cost : 0;
            $total_price += ($this->data['setting']['delivery_company'] != 1) ? $order_data->delivery_cost : 0;
        }
        if (sizeof($reservation) > 0 && $reservation->down_payment > 0) {
            $total_price -= $reservation->down_payment;
            if ($total_price < 0) $total_price = 0;
        }
        $return['reservation'] = $reservation;
        $return['order_list'] = $new_list_order;
        $return['subtotal_value'] = $subtotal;
        $return['subtotal'] = number_format($subtotal, 0, "", ".");
        $return['tax_price'] = $arr_tax;

        $return['extra_charge_price'] = $arr_charge;
        $return['total_price'] = number_format($total_price, 0, "", ".");
        $return['total_hpp'] = $total_hpp;

        return $return;
    }

    public function calculate_total_order_bill_for_refund($order_taxes, $receipt_number, $order_id, $canceled = FALSE)
    {
        $this->db->select('bm.order_menu_id,
                            bm.menu_id,
                            m.category_id,
                            m.use_taxes,
                            m.menu_name,
                            m.menu_price,
                            m.is_promo,
                            bm.quantity,
                            m.menu_hpp,
                            b.is_take_away,
                            b.is_delivery', false);
        $this->db->from('bill_menu bm');
        $this->db->join('bill b', 'b.id = bm.bill_id');
        $this->db->join('menu m', 'm.id = bm.menu_id');

        $this->db->where('b.receipt_number', $receipt_number);
        $this->db->where('b.is_refund', 0);
        $result = $this->db->get();
        
        $list_order = array();
        $subtotal = 0;
        $subtotal_non_taxes = 0;
        $order_type= 1;
        $total_services = 0;
        $temp = array();
        $temp_side_dish = array();
        
        foreach ($result->result() as $option) {
            if ($option->is_take_away == 1) {
                $order_type = 2;
            } else if ($option->is_delivery == 1) {
                $order_type = 3;
            }
            $sidedish_order = array();
            $options_order = array();
            $basic_price = $option->menu_price * $option->quantity;
            $order_menu_option = $this->get_option_by_order_menu($option->order_menu_id);

            if (!empty($order_menu_option)) {
                foreach ($order_menu_option as $opt) {
                    $optx = $this->get_one('menu_option_value', $opt->menu_option_value_id);
                    if ($optx) {
                        $option_name = $this->get_one('menu_option', $optx->option_id);
                        $optionThing = new stdClass();
                        $optionThing->option_name = $option_name->option_name;
                        $optionThing->option_value_name = $optx->option_value_name;
                        $options_order[] = $optionThing;
                    }
                }
            }

            $order_menu = new stdClass();
            $order_menu->menu_id = $option->menu_id;
            $order_menu->quantity = $option->quantity;
            $order_menu->menu_name = $option->menu_name;
            $order_menu->category_id = $option->category_id;
            $order_menu->order_menu_id = $option->order_menu_id;
            $order_menu->side_dish_list = $sidedish_order;
            $order_menu->option_list = $options_order;
            $order_menu->origin_price = $option->menu_price;
            $order_menu->menu_hpp = $option->menu_hpp;
            $order_menu->menu_price = 0;
            $order_menu->use_taxes = (int)$option->use_taxes;
            if ($order_menu->quantity) {
                $done_payment = FALSE;
                $side_price = 0;
                $subtotal += ($order_menu->quantity * $option->menu_price);
                if ($order_menu->use_taxes == 0) {
                    $subtotal_non_taxes += ($order_menu->quantity * $option->menu_price);
                }
                $order_menu_sidedish = $this->get_side_dish_by_order_menu($option->order_menu_id, $option->is_promo);

                $temp_side_dish[$option->order_menu_id] = $option->menu_id;
                if (!empty($order_menu_sidedish)) {

                    foreach ($order_menu_sidedish as $side) {
                        $temp_side_dish[$option->order_menu_id] .= $side->side_dish_id;
                        $side_price += $side->price;
                        $sideThing = new stdClass();
                        $sideThing->name = $side->name;
                        $sideThing->id = $side->id;
                        $sideThing->quantity = $option->quantity - $option->quantity;
                        $sideThing->origin_price = $side->price;
                        $sideThing->price = number_format($side_price, 0, "", ".");
                        $sidedish_order[] = $sideThing;
                    }
                    $subtotal += ($side_price * $order_menu->quantity);
                    if ($order_menu->use_taxes == 0) {
                        $subtotal_non_taxes += ($side_price * $order_menu->quantity);
                    }

                }

                $order_menu->side_dish_list = $sidedish_order;
                $option->menu_price += $side_price;
                $order_menu->origin_price = $option->menu_price;
                // $order_menu->menu_price     = number_format($order_menu->quantity * $option->menu_price , 0, "", ".");
                $order_menu->menu_price = ($order_menu->quantity * $option->menu_price);
                $list_order[$option->order_menu_id] = $order_menu;
            }


        }

        $new_list_order = array();
        // grouping bill with the same menu id and sidedish
        // only group bill in checkout bill
        if ($canceled) {
            $had_same_sidedish = $this->get_keys_for_duplicate_values($temp_side_dish);
            $counter = 0;
            foreach ($had_same_sidedish as $key => $row) {
                foreach ($row as $key2 => $row2) {
                    if (isset($new_list_order[$counter])) {
                        $new_list_order[$counter]->quantity += $list_order[$row2]->quantity;
                        $new_list_order[$counter]->menu_price += $list_order[$row2]->menu_price;
                    } else {
                        $new_list_order[$counter] = $list_order[$row2];
                    }
                }
                $counter++;
            }
            unset($list_order);
        } else {
            $new_list_order = $list_order;
            unset($list_order);
        }

        $tax_price = 0;
        $non_tax_price = 0;
        $count_tax = 0;
        $count_non_tax = 0;
        $arr_tax = array();
        // $taxes = $this->db->get('taxes');
        $tax_method = $this->data['setting']['tax_service_method'];
        $taxes = $this->tax_model->get_taxes($order_type, $tax_method, 1);
        foreach ($taxes as $key) {
            if ($key->is_service == 1) {
                $total_services += (($subtotal * $key->tax_percentage) / 100);
            }
        }
        foreach ($order_taxes as $tax) {
            if ($this->data['setting']['tax_service_method'] == 1) {
                $count_tax = (($subtotal * $tax->tax_percentage) / 100);
                $count_non_tax = (($subtotal_non_taxes * $tax->tax_percentage) / 100);
            } else {
                $temp_subtotal = $subtotal + $total_services;
                if ($tax->is_service == 0) {
                    $count_tax = (($temp_subtotal * $tax->tax_percentage) / 100);
                    $count_non_tax = (($subtotal_non_taxes * $tax->tax_percentage) / 100);                    
                } else {
                    $count_tax = (($subtotal * $tax->tax_percentage) / 100);
                    $count_non_tax = 0;
                }
            }
            $tax_price += $count_tax;
            $non_tax_price += $count_non_tax;
                
            $arr_tax[] = array('name' => $tax->tax_name . ' (' . $tax->tax_percentage . '%)',
                'id' => $tax->tax_id,
                'origin_name' => $tax->tax_name,
                'value' => number_format($count_tax - $count_non_tax, 0, "", "."),
                'tax_percentage' => $tax->tax_percentage,
                'account_id' => $tax->account_id,
                'is_service'  => $tax->is_service
            );
        }

        $total_price = $subtotal + $tax_price - $non_tax_price;


        $arr_charge = array();
        $extra_charge = $this->db->get('extra_charge');
        foreach ($extra_charge->result() as $xtra) {
            $total_price += $xtra->charge_value;
            $arr_charge[] = array('name' => $xtra->charge_name,
                'value' => number_format($xtra->charge_value, 0, "", "."));
        }
        $order_data = $this->order_model->get_one('order', $order_id);
        $reservation = $this->order_model->get_one("reservation", $order_data->reservation_id);
        if ($order_data->is_delivery == 1) {
            $return['delivery_cost'] = ($this->data['setting']['delivery_company'] != 1) ? $order_data->delivery_cost : 0;
            $total_price += $order_data->delivery_cost;
        }
        if (sizeof($reservation) > 0 && $reservation->down_payment > 0) {
            $total_price -= $reservation->down_payment;
            if ($total_price < 0) $total_price = 0;
        }
        $return['reservation'] = $reservation;
        $return['order_list'] = $new_list_order;
        $return['subtotal_value'] = $subtotal;
        $return['subtotal'] = number_format($subtotal, 0, "", ".");
        $return['tax_price'] = $arr_tax;

        $return['extra_charge_price'] = $arr_charge;
        $return['total_price'] = number_format($total_price, 0, "", ".");

        return $return;
    }

    public function calculate_total_order_bill_report($order_id, $from_date, $to_date, $canceled = FALSE)
    {
        $this->db->select('sum(bill.customer_count) as customer_count');
        $this->db->from('bill');
        $this->db->where('bill.is_refund', 0);
        $this->db->where('bill.table_id !=', 0);
        $this->db->where('bill.order_id', $order_id);
        $this->db->where('bill.start_order >= ', $from_date);
        $this->db->where('bill.end_order <= ', $to_date);
        $total_customer_count = (int)$this->db->get()->row()->customer_count;

        $this->db->select('order_menu.*,menu.menu_name, menu.menu_price, menu.category_id,menu.menu_hpp,category.outlet_id,outlet.outlet_name,outlet.store_id,store.store_name,menu.is_promo,
            order_menu.id as order_menu_id, 
            enum_cooking_status.status_name,bill.customer_count,bill.receipt_number
            ');
        $this->db->from('order_menu');
        $this->db->join('order', 'order.id = order_menu.order_id');
        $this->db->join('bill', 'bill.order_id = order.id');
        $this->db->join('menu', 'menu.id = order_menu.menu_id');
        $this->db->join('category', 'category.id = menu.category_id', 'left');
        $this->db->join('outlet', 'outlet.id = category.outlet_id', 'left');
        $this->db->join('store', 'store.id = outlet.store_id', 'left');
        $this->db->join('enum_cooking_status', 'enum_cooking_status.id = order_menu.cooking_status');

        $this->db->where('bill.order_id', $order_id);
        $this->db->where('bill.is_refund', 0);
        if ($canceled === TRUE) {
            $this->db->where('cooking_status !=', '4');
            $this->db->where('cooking_status !=', '0');
            $this->db->where('cooking_status !=', '6');
        }
        $result = $this->db->get();
        $list_order = array();
        $subtotal = 0;
        $temp = array();
        $temp_side_dish = array();
        foreach ($result->result() as $option) {
            $sidedish_order = array();
            $options_order = array();
            $basic_price = $option->menu_price * $option->quantity;
            $order_menu_option = $this->get_option_by_order_menu($option->id);

            if (!empty($order_menu_option)) {
                foreach ($order_menu_option as $opt) {
                    $optx = $this->get_one('menu_option_value', $opt->menu_option_value_id);
                    if ($optx) {
                        $option_name = $this->get_one('menu_option', $optx->option_id);
                        $optionThing = new stdClass();
                        $optionThing->option_name = $option_name->option_name;
                        $optionThing->option_value_name = $optx->option_value_name;
                        $options_order[] = $optionThing;
                    }
                }
            }

            $order_menu = new stdClass();
            $order_menu->id = $option->id;
            $order_menu->order_id = $option->order_id;
            $order_menu->menu_id = $option->menu_id;
            $order_menu->quantity = $option->quantity;
            $order_menu->note = $option->note;
            $order_menu->cooking_status = $option->cooking_status;
            $order_menu->process_status = $option->process_status;
            $order_menu->menu_name = $option->menu_name;
            $order_menu->category_id = $option->category_id;
            $order_menu->order_menu_id = $option->order_menu_id;
            $order_menu->side_dish_list = $sidedish_order;
            $order_menu->cooking_status_name = $option->status_name;
            $order_menu->option_list = $options_order;
            $order_menu->origin_price = $option->menu_price;
            $order_menu->menu_hpp = $option->menu_hpp;
            $order_menu->menu_price = 0;
            $order_menu->store_id = $option->store_id;
            $order_menu->store_name = $option->store_name;
            $order_menu->outlet_id = $option->outlet_id;
            $order_menu->outlet_name = $option->outlet_name;
            $order_menu->receipt_number = $option->receipt_number;
            $order_menu->customer_count = (int)$option->customer_count;
            if ($order_menu->quantity) {
                $done_payment = FALSE;
                $side_price = 0;
                $subtotal += ($order_menu->quantity * $option->menu_price);

                $order_menu_sidedish = $this->get_side_dish_by_order_menu($option->id, $option->is_promo);

                $temp_side_dish[$option->id] = $option->menu_id;
                if (!empty($order_menu_sidedish)) {

                    foreach ($order_menu_sidedish as $side) {
                        $temp_side_dish[$option->id] .= $side->side_dish_id;
                        $side_price += $side->price;
                        $sideThing = new stdClass();
                        $sideThing->name = $side->name;
                        $sideThing->id = $side->id;
                        $sideThing->quantity = $option->quantity;
                        $sideThing->origin_price = $side->price;
                        $sideThing->price = number_format($side_price, 0, "", ".");
                        $sidedish_order[] = $sideThing;
                    }
                    $subtotal += ($side_price * $order_menu->quantity);

                }

                $order_menu->side_dish_list = $sidedish_order;
                $option->menu_price += $side_price;
                $order_menu->origin_price = $option->menu_price;
                // $order_menu->menu_price     = number_format($order_menu->quantity * $option->menu_price , 0, "", ".");
                $order_menu->menu_price = ($order_menu->quantity * $option->menu_price);
                $list_order[$option->id] = $order_menu;
            }


        }

        $new_list_order = array();
        // grouping bill with the same menu id and sidedish
        // only group bill in checkout bill
        if ($canceled) {
            $had_same_sidedish = $this->get_keys_for_duplicate_values($temp_side_dish);
            $counter = 0;
            foreach ($had_same_sidedish as $key => $row) {
                foreach ($row as $key2 => $row2) {
                    if (isset($new_list_order[$counter])) {
                        $new_list_order[$counter]->quantity += $list_order[$row2]->quantity;
                        $new_list_order[$counter]->menu_price += $list_order[$row2]->menu_price;
                    } else {
                        $new_list_order[$counter] = $list_order[$row2];
                    }
                }
                $counter++;
            }
            unset($list_order);
        } else {
            $new_list_order = $list_order;
            unset($list_order);
        }

        $tax_price = 0;
        $count_tax = 0;
        $arr_tax = array();
        $taxes = $this->db->get('taxes');
        foreach ($taxes->result() as $tax) {
            $count_tax = (($subtotal * $tax->tax_percentage) / 100);
            $tax_price += $count_tax;
            $arr_tax[] = array('name' => $tax->tax_name . ' (' . $tax->tax_percentage . '%)',
                'value' => number_format($count_tax, 0, "", "."),
                'tax_percentage' => $tax->tax_percentage,
                'account_id' => $tax->account_id
            );
        }

        $total_price = $subtotal + $tax_price;


        $arr_charge = array();
        $extra_charge = $this->db->get('extra_charge');
        foreach ($extra_charge->result() as $xtra) {
            $total_price += $xtra->charge_value;
            $arr_charge[] = array('name' => $xtra->charge_name,
                'value' => number_format($xtra->charge_value, 0, "", "."));
        }
        $order_data = $this->order_model->get_one('order', $order_id);
        if ($order_data->is_delivery == 1) {
            $return['delivery_cost'] = $order_data->delivery_cost;
            $total_price += $order_data->delivery_cost;
        }

        $return['order_list'] = $new_list_order;
        $return['subtotal_value'] = $subtotal;
        $return['subtotal'] = number_format($subtotal, 0, "", ".");
        $return['tax_price'] = $arr_tax;
        $return['total_customer_count'] = $total_customer_count;

        $return['extra_charge_price'] = $arr_charge;
        $return['total_price'] = number_format($total_price, 0, "", ".");

        return $return;
    }

    /**
     * grouping array value by key
     * @param  [array] $my_arr
     *
     * @return [array]
     *
     * @author fkartika
     */
    function get_keys_for_duplicate_values($my_arr)
    {
        $dups = array();
        foreach ($my_arr as $key => $val) {
            $dups[$val][] = $key;
        }
        return $dups;
    }

    public function get_calculate_total_order_bill($order_id, $canceled = FALSE)
    {
        $this->db->select('order_menu.*,menu.menu_name, menu.menu_price, menu.category_id,
            order_menu.id as order_menu_id, 
            enum_cooking_status.status_name,
                    (
                      select sum( b.quantity) from `order` a join order_menu b on(a.id = b.order_id) 
                        where a.id = ' . $order_id . '
                     ) as sum_quantity,
            
                ( 
                    select sum( b.quantity) from bill a join bill_menu b on(a.id = b.bill_id)
                           where a.is_refund = 0 and a.order_id = ' . $order_id . '
                )  as quantity_bill
            
            ');
        $this->db->from('order_menu');
        $this->db->join('menu', 'menu.id = order_menu.menu_id');
        $this->db->join('enum_cooking_status', 'enum_cooking_status.id = order_menu.cooking_status');

        $this->db->where('order_id', $order_id);


        $result = $this->db->get()->row();
        return $result;
    }


    public function get_bill_menu($receipt_number, $canceled = FALSE)
    {
        $this->db->select('bill_menu.*,menu.menu_name, menu.menu_price, menu.category_id,
            bill_menu.id as bill_id, 
            enum_cooking_status.status_name
            ');
        $this->db->from('bill_menu');
        $this->db->join('bill', 'bill.id = bill_menu.bill_id');
        $this->db->join('menu', 'menu.id = bill_menu.menu_id');

        $this->db->where('receipt_number', $receipt_number);


        $result = $this->db->get()->row();
        return $result;
    }


    function get_bill_detail($receipt_number)
    {
        $this->db->select('bill.id as bill_id, bill_payment.amount as customer_payment,
            bill.*, table.table_name,  
            users.name as cashier, (total_price - total_cogs) as profit')
            ->from('bill')
            ->join('bill_payment', 'bill.id = bill_payment.bill_id')
            // ->join('order','bill.order_id = order.id')
            ->join('table', 'table.id = bill.table_id', 'left')
            ->join('users', 'users.id = bill.cashier_id', 'left')
            ->where('receipt_number', $receipt_number)
            ->where('is_refund', 0);

        $result = $this->db->get()->row();
        return $result;
    }

     function get_voucher_detail($bill_id)
    {
        $this->db->select('amount')
            ->from('bill_payment')
            ->join('bill', 'bill.id = bill_payment.bill_id')
            ->where('bill_id', $bill_id)
            ->where('is_refund', 0);
        $this->db->where('payment_option', 4);

        $result = $this->db->get()->result();
        return $result;
    }

    function get_sales_menu_detail($menu_id = FALSE, $start_date = FALSE, $end_date = FALSE)
    {

        $this->db->select('bill_menu.menu_id, 
            menu_id, menu.menu_name as menu_name, sum(quantity) as total_quantity, 
            sum(price*quantity) as total_price, sum(cogs*quantity) as total_cogs, 
            (sum(price*quantity)-sum(cogs*quantity)) as profit,
            bill_menu.created_at')
            ->from('bill_menu')
            ->join('bill', 'bill.id = bill_menu.bill_id')
            ->join('menu','menu.id=bill_menu.menu_id')
            ->where('menu_id', $menu_id)
            ->where('is_refund', 0);
        // ->group_by('bill_menu.menu_id')

        if ($start_date) {
            $this->db->where('bill_menu.created_at >= ', $start_date);
        }

        if ($end_date) {
            $this->db->where('bill_menu.created_at <= ', $end_date);
        }


        $result = $this->db->get()->row();
        return $result;

    }


    public function calculate_sales_menu_category($start_date = FALSE, $end_date = FALSE, $category_id = FALSE)
    {
        $this->db->select('menu.id as id, 
            menu_id, bill_menu.menu_name, sum(bill_menu.quantity) as total_quantity, 
            sum(price) as total_price,
            sum(bill_menu.cogs) as total_cogs, 
            (sum(price)-sum(cogs)) as profit, 
            category_name,
            outlet_name,
            bill_menu.created_at,
            ')
            ->join('menu', 'menu.id = bill_menu.menu_id')
            // ->join('bill_menu_inventory_cogs', 'bill_menu_inventory_cogs.bill_menu_id = bill_menu.id')
            ->join('category', 'category.id = menu.category_id')
            ->join('outlet', 'outlet.id = category.outlet_id')
            ->join('bill', 'bill.id = bill_menu.bill_id')
            ->from('bill_menu')
            ->where('bill.is_refund', 0)
            ->group_by('category.outlet_id');


        if ($start_date) {
            $this->db->where('bill_menu.created_at >= ', $start_date);
        }

        if ($end_date) {
            $this->db->where('bill_menu.created_at <= ', $end_date);
        }

        if ($category_id) {
            $this->db->where('menu.category_id ', $category_id);
        }


        $result = $this->db->get()->result();

        return $result;

    }


    public function get_calculate_payment_bill($type, $start_date = FALSE, $end_date = FALSE)
    {
        $this->db->select('bill_information.id, info, sum(amount) as amount')
            ->from('bill_information')
            ->join('bill', 'bill.id = bill_information.bill_id')
            ->where('type', $type)
            ->where('is_refund', 0);

        if ($start_date) {
            $this->db->where('bill.created_at >= ', $start_date);
        }

        if ($end_date) {
            $this->db->where('bill.created_at <= ', $end_date);
        }
        $this->db->group_by('info');
        return $this->db->get()->result();


    }

    public function get_summary_transaction($params = array())
    {
        $start_date = $params['start_date'];
        $end_date = $params['end_date'];
        $user_id = $params['user_id'];
        $order_type = $params['order_type'];
        $payment_option = $params['payment_option'];

        //TOTAL PENGELUARAN
        $this->db->select('sum(bm.price*bm.quantity) as total_price', false)
            ->from('bill_menu bm')
            ->join('bill b', 'b.id = bm.bill_id')
            ->join('menu m', 'm.id = bm.menu_id')
            ->where('b.is_refund', 0);
        if ($start_date) {
            $this->db->where('b.created_at >= ', $start_date);
        }
        if ($end_date) {
            $this->db->where('b.created_at <= ', $end_date);
        }
        if ($user_id) {
            $this->db->where('b.cashier_id', $user_id);
        }
        if ($order_type != "") {
            if ($order_type == 1) {
                $this->db->where('b.is_delivery', 0);
                $this->db->where('b.is_take_away', 0);
            } elseif ($order_type == 2) {
                $this->db->where('b.is_delivery', 0);
                $this->db->where('b.is_take_away', 1);
            } elseif ($order_type == 3) {
                $this->db->where('b.is_delivery', 1);
                $this->db->where('b.is_take_away', 0);
            }
        }
        if ($payment_option != "") {
            $this->db->where("b.id in (select bill_id from bill_payment where payment_option='" . $payment_option . "')");
        }
        $total_sell = $this->db->get()->row();
        
        //TOTAL TAX
        $this->db->select('SUM(bi.amount) as amount', false)
            ->from('bill_information bi')
            ->join('bill b', 'b.id = bi.bill_id')
            ->where('bi.type', 1)
            ->where('bi.info not like ', '%ongkos kirim%')
            ->where('bi.info not like ', '%pembulatan%')
            ->where('b.is_refund', 0);
        if ($start_date) {
            $this->db->where('b.created_at >= ', $start_date);
        }
        if ($end_date) {
            $this->db->where('b.created_at <= ', $end_date);
        }
        if ($user_id) {
            $this->db->where('b.cashier_id', $user_id);
        }
        if ($order_type != "") {
            if ($order_type == 1) {
                $this->db->where('b.is_delivery', 0);
                $this->db->where('b.is_take_away', 0);
            } elseif ($order_type == 2) {
                $this->db->where('b.is_delivery', 0);
                $this->db->where('b.is_take_away', 1);
            } elseif ($order_type == 3) {
                $this->db->where('b.is_delivery', 1);
                $this->db->where('b.is_take_away', 0);
            }
        }
        if ($payment_option != "") {
            $this->db->where("b.id in (select bill_id from bill_payment where payment_option='" . $payment_option . "')");
        }
        $total_sub_tax = $this->db->get()->row();

        // TOTAL PEMBULATAN
        $this->db->select('SUM(bi.amount) as amount', false)
            ->from('bill_information bi')
            ->join('bill b', 'b.id = bi.bill_id')
            ->where('bi.type', 1)
            ->where('bi.info like', '%pembulatan%')
            ->where('b.is_refund', 0);
        if ($start_date) {
            $this->db->where('b.created_at >= ', $start_date);
        }
        if ($end_date) {
            $this->db->where('b.created_at <= ', $end_date);
        }
        if ($user_id) {
            $this->db->where('b.cashier_id', $user_id);
        }
        if ($order_type != "") {
            if ($order_type == 1) {
                $this->db->where('b.is_delivery', 0);
                $this->db->where('b.is_take_away', 0);
            } elseif ($order_type == 2) {
                $this->db->where('b.is_delivery', 0);
                $this->db->where('b.is_take_away', 1);
            } elseif ($order_type == 3) {
                $this->db->where('b.is_delivery', 1);
                $this->db->where('b.is_take_away', 0);
            }
        }
        if ($payment_option != "") {
            $this->db->where("b.id in (select bill_id from bill_payment where payment_option='" . $payment_option . "')");
        }
        $total_pembulatan = $this->db->get()->row();

        //ONGKOS KIRIM
        $this->db->select('SUM(bi.amount) as amount', false)
            ->from('bill_information bi')
            ->join('bill b', 'b.id = bi.bill_id')
            ->where('bi.type', 1)
            ->where('bi.info like', '%ongkos kirim%')
            ->where('b.is_refund', 0);
        if ($start_date) {
            $this->db->where('b.created_at >= ', $start_date);
        }
        if ($end_date) {
            $this->db->where('b.created_at <= ', $end_date);
        }
        if ($user_id) {
            $this->db->where('b.cashier_id', $user_id);
        }
        if ($order_type != "") {
            if ($order_type == 1) {
                $this->db->where('b.is_delivery', 0);
                $this->db->where('b.is_take_away', 0);
            } elseif ($order_type == 2) {
                $this->db->where('b.is_delivery', 0);
                $this->db->where('b.is_take_away', 1);
            } elseif ($order_type == 3) {
                $this->db->where('b.is_delivery', 1);
                $this->db->where('b.is_take_away', 0);
            }
        }
        if ($payment_option != "") {
            $this->db->where("b.id in (select bill_id from bill_payment where payment_option='" . $payment_option . "')");
        }
        $total_ongkir = $this->db->get()->row();

        //DP IN 
        $total_dp_in = $this->db->query("select IFNULL(sum(down_payment),0) as dp_in from reservation where status != 3" . ($start_date != "" && $end_date != "" ? " and created_at >='" . $start_date . "' and created_at <='" . $end_date . "' " : ""))->row();

        //DP OUT 
        $total_dp_out = $this->db->query("select IFNULL(sum(amount), 0) AS dp_out from bill_payment where payment_option = 10 " . ($start_date != "" && $end_date != "" ? " and created_at >='" . $start_date . "' and created_at <='" . $end_date . "' " : ""))->row();

        //PENAMBAHAN MODAL
        $total_balance_cash = $this->db->query("select IFNULL(sum(amount), 0) AS balance_cash from balance_cash_history " . ($start_date != "" && $end_date != "" ? " where date >='" . $start_date . "' and date <='" . $end_date . "' " : ""))->row();

        //BON BILL
        $this->db->select('IFNULL(sum(amount),0) as amount', false)
            ->from('bill_payment bp')
            ->join('bill b', 'b.id = bp.bill_id')
            ->where('bp.payment_option', 9)
            ->where('b.is_refund', 0);
        if ($start_date) {
            $this->db->where('b.created_at >= ', $start_date);
        }
        if ($end_date) {
            $this->db->where('b.created_at <= ', $end_date);
        }
        if ($user_id) {
            $this->db->where('b.cashier_id', $user_id);
        }
        if ($order_type != "") {
            if ($order_type == 1) {
                $this->db->where('b.is_delivery', 0);
                $this->db->where('b.is_take_away', 0);
            } elseif ($order_type == 2) {
                $this->db->where('b.is_delivery', 0);
                $this->db->where('b.is_take_away', 1);
            } elseif ($order_type == 3) {
                $this->db->where('b.is_delivery', 1);
                $this->db->where('b.is_take_away', 0);
            }
        }
        if ($payment_option != "") {
            $this->db->where("b.id in (select bill_id from bill_payment where payment_option='" . $payment_option . "')");
        }
        $total_bon_bill = $this->db->get()->row();

        //PENDING BILL
        $this->db->select('IFNULL(sum(amount),0) as amount', false)
            ->from('bill_payment bp')
            ->join('bill b', 'b.id = bp.bill_id')
            ->where('b.is_refund', 0)
            ->where_in('bp.payment_option',array(6,7) );
        if ($start_date) {
            $this->db->where('b.created_at >= ', $start_date);
        }
        if ($end_date) {
            $this->db->where('b.created_at <= ', $end_date);
        }
        if ($user_id) {
            $this->db->where('b.cashier_id', $user_id);
        }
        if ($order_type != "") {
            if ($order_type == 1) {
                $this->db->where('b.is_delivery', 0);
                $this->db->where('b.is_take_away', 0);
            } elseif ($order_type == 2) {
                $this->db->where('b.is_delivery', 0);
                $this->db->where('b.is_take_away', 1);
            } elseif ($order_type == 3) {
                $this->db->where('b.is_delivery', 1);
                $this->db->where('b.is_take_away', 0);
            }
        }
        if ($payment_option != "") {
            $this->db->where("b.id in (select bill_id from bill_payment where payment_option='" . $payment_option . "')");
        }
        $total_pending_bill = $this->db->get()->row();

        //COMPLIMENT
        $this->db->select('IFNULL(sum(bp.amount),0) as amount', false)
            ->from('bill_payment bp')
            ->join('bill b', 'b.id = bp.bill_id')
            ->where('bp.payment_option', 5)
            ->where('b.is_refund', 0);
        if ($start_date) {
            $this->db->where('b.created_at >= ', $start_date);
        }
        if ($end_date) {
            $this->db->where('b.created_at <= ', $end_date);
        }
        if ($user_id) {
            $this->db->where('b.cashier_id', $user_id);
        }
        if ($order_type != "") {
            if ($order_type == 1) {
                $this->db->where('b.is_delivery', 0);
                $this->db->where('b.is_take_away', 0);
            } elseif ($order_type == 2) {
                $this->db->where('b.is_delivery', 0);
                $this->db->where('b.is_take_away', 1);
            } elseif ($order_type == 3) {
                $this->db->where('b.is_delivery', 1);
                $this->db->where('b.is_take_away', 0);
            }
        }
        if ($payment_option != "") {
            $this->db->where("b.id in (select bill_id from bill_payment where payment_option='" . $payment_option . "')");
        }
        $total_compliment = $this->db->get()->row();

        //KAS KECIL
        $total_petty_cash = $this->db->query("select IFNULL(sum(amount),0) as amount from petty_cash " . ($start_date != "" && $end_date != "" ? " where date >='" . $start_date . "' and date<='" . $end_date . "' " : ""))->row();

        //DOWN PAYMENT
        $total_dp = $this->db->query('select sum(r.down_payment) as amount from reservation r where r.status!=3 ' . ($start_date != "" && $end_date != "" ? " and r.created_at >='" . $start_date . "' and r.created_at<='" . $end_date . "' " : "") . ($order_type != "" ? " and r.order_type='" . $order_type . "'" : ""))->row();

        //PENDAPATAN,HPP,PROFIT,TOTAL TRANSAKSI,TOTAL JUMLAH CUSTOMER,JASA KURIR
        $this->db->select('
				sum(b.total_price)-sum(ifnull(bp.amount,0)) as sum_total_price,sum(b.total_cogs)-sum(ifnull(bp.amount,0)) as sum_total_cogs,
				count(b.id) as total_transaction,sum(b.customer_count) as total_customer_count,
				sum(bc.courier_service_value) as total_courier_service
			', false)
            ->from('bill b')
            ->join('(
                select bill_id,sum(amount) as amount from bill_payment where payment_option=9 group by bill_id
            ) bp', 'bp.bill_id=b.id', 'left')
            ->join('bill_courier_service bc', 'b.id=bc.bill_id', 'left')
            ->where('b.is_refund', 0);
        if ($start_date) {
            $this->db->where('b.created_at >= ', $start_date);
        }
        if ($end_date) {
            $this->db->where('b.created_at <= ', $end_date);
        }
        if ($user_id) {
            $this->db->where('cashier_id', $user_id);
        }
        if ($order_type != "") {
            if ($order_type == 1) {
                $this->db->where('is_delivery', 0);
                $this->db->where('is_take_away', 0);
            } elseif ($order_type == 2) {
                $this->db->where('is_delivery', 0);
                $this->db->where('is_take_away', 1);
            } elseif ($order_type == 3) {
                $this->db->where('is_delivery', 1);
                $this->db->where('is_take_away', 0);
            }
        }
        if ($payment_option != "") {
            $this->db->where("b.id in (select bill_id from bill_payment where payment_option='" . $payment_option . "')");
        }
        $total = $this->db->get()->row();

        //TOTAL QUANTITY ORDER
        $this->db->select('sum(bm.quantity) as amount', false)
            ->from('bill b')
            ->join('bill_menu bm', 'b.id=bm.bill_id')
            ->where('b.is_refund', 0);
        if ($start_date) {
            $this->db->where('b.created_at >= ', $start_date);
        }
        if ($end_date) {
            $this->db->where('b.created_at <= ', $end_date);
        }
        if ($user_id) {
            $this->db->where('cashier_id', $user_id);
        }
        if ($order_type != "") {
            if ($order_type == 1) {
                $this->db->where('is_delivery', 0);
                $this->db->where('is_take_away', 0);
            } elseif ($order_type == 2) {
                $this->db->where('is_delivery', 0);
                $this->db->where('is_take_away', 1);
            } elseif ($order_type == 3) {
                $this->db->where('is_delivery', 1);
                $this->db->where('is_take_away', 0);
            }
        }
        if ($payment_option != "") {
            $this->db->where("b.id in (select bill_id from bill_payment where payment_option='" . $payment_option . "')");
        }
        $total_quantity_order = $this->db->get()->row();

        //DISCOUNT
        $this->db->select('sum(bi.amount) as amount', false)
            ->from('bill b')
            ->join('bill_information bi', 'b.id=bi.bill_id')
            ->where('bi.info not like ', '%ongkos kirim%')
            ->where('bi.type', 2)
            ->where('b.is_refund', 0);
        if ($start_date) {
            $this->db->where('b.created_at >= ', $start_date);
        }
        if ($end_date) {
            $this->db->where('b.created_at <= ', $end_date);
        }
        if ($user_id) {
            $this->db->where('cashier_id', $user_id);
        }
        if ($order_type != "") {
            if ($order_type == 1) {
                $this->db->where('is_delivery', 0);
                $this->db->where('is_take_away', 0);
            } elseif ($order_type == 2) {
                $this->db->where('is_delivery', 0);
                $this->db->where('is_take_away', 1);
            } elseif ($order_type == 3) {
                $this->db->where('is_delivery', 1);
                $this->db->where('is_take_away', 0);
            }
        }
        if ($payment_option != "") {
            $this->db->where("b.id in (select bill_id from bill_payment where payment_option='" . $payment_option . "')");
        }
        $total_discount = $this->db->get()->row();

        //TOTAL DINEIN,TOTAL TAKEAWAY,TOTAL DELIVERY
        $this->db->select('
				sum(IF(b.is_delivery=0 and b.is_take_away=0,b.total_price,0)) as total_dinein,
				sum(IF(b.is_delivery=0 and b.is_take_away=0,1,0)) as total_count_dinein,
				
				sum(IF(b.is_delivery=0 and b.is_take_away=1,b.total_price,0)) as total_takeaway,
				sum(IF(b.is_delivery=0 and b.is_take_away=1,1,0)) as total_count_takeaway,
				
				sum(IF(b.is_delivery=1 and b.is_take_away=0,b.total_price,0)) as total_delivery,
				sum(IF(b.is_delivery=1 and b.is_take_away=0,1,0)) as total_count_delivery
			', false)
            ->from('bill b')
            ->where('b.is_refund', 0);
        if ($start_date) {
            $this->db->where('b.created_at >= ', $start_date);
        }
        if ($end_date) {
            $this->db->where('b.created_at <= ', $end_date);
        }
        if ($user_id) {
            $this->db->where('b.cashier_id', $user_id);
        }
        if ($payment_option != "") {
            $this->db->where("b.id in (select bill_id from bill_payment where payment_option='" . $payment_option . "')");
        }
        $total_other = $this->db->get()->row();

        //TOTAL PROMO
        $this->db->select('
				count(b.id) as total_count_promo,
				sum(((b.total_price-(select sum(amount) from bill_information where bill_id=b.id and type=1))*pd.discount/100)) as total_promo
			', false)
            ->from('bill b')
            ->join("promo_discount pd", "b.promo_id=pd.id")
            ->join("promo_schedule ps", "pd.promo_schedule_id=ps.id")
            ->where('b.is_refund', 0);
        if ($start_date) {
            $this->db->where('b.created_at >= ', $start_date);
        }
        if ($end_date) {
            $this->db->where('b.created_at <= ', $end_date);
        }
        if ($user_id) {
            $this->db->where('cashier_id', $user_id);
        }
        if ($order_type != "") {
            if ($order_type == 1) {
                $this->db->where('is_delivery', 0);
                $this->db->where('is_take_away', 0);
            } elseif ($order_type == 2) {
                $this->db->where('is_delivery', 0);
                $this->db->where('is_take_away', 1);
            } elseif ($order_type == 3) {
                $this->db->where('is_delivery', 1);
                $this->db->where('is_take_away', 0);
            }
        }
        if ($payment_option != "") {
            $this->db->where("b.id in (select bill_id from bill_payment where payment_option='" . $payment_option . "')");
        }
        $total_promo = $this->db->get()->row();
        
        //TOTAL VOUCHER
        $this->db->select('
				sum(bp.amount) as total_voucher,count(b.id) as total_count_voucher
			', false)
            ->from('bill b')
            ->join("bill_payment bp", "b.id=bp.bill_id")
            ->where("bp.payment_option", 4)
            ->where('b.is_refund', 0);
        if ($start_date) {
            $this->db->where('b.created_at >= ', $start_date);
        }
        if ($end_date) {
            $this->db->where('b.created_at <= ', $end_date);
        }
        if ($user_id) {
            $this->db->where('cashier_id', $user_id);
        }
        if ($order_type != "") {
            if ($order_type == 1) {
                $this->db->where('is_delivery', 0);
                $this->db->where('is_take_away', 0);
            } elseif ($order_type == 2) {
                $this->db->where('is_delivery', 0);
                $this->db->where('is_take_away', 1);
            } elseif ($order_type == 3) {
                $this->db->where('is_delivery', 1);
                $this->db->where('is_take_away', 0);
            }
        }
        $total_voucher = $this->db->get()->row();

        //TOTAL HPP
        $this->db->select('IFNULL(sum(total_cogs),0) as total_cogs', false)
            ->from('bill b')
            ->where('b.is_refund', 0);
        if ($start_date) {
            $this->db->where('b.created_at >= ', $start_date);
        }
        if ($end_date) {
            $this->db->where('b.created_at <= ', $end_date);
        }
        if ($user_id) {
            $this->db->where('cashier_id', $user_id);
        }
        if ($order_type != "") {
            if ($order_type == 1) {
                $this->db->where('is_delivery', 0);
                $this->db->where('is_take_away', 0);
            } elseif ($order_type == 2) {
                $this->db->where('is_delivery', 0);
                $this->db->where('is_take_away', 1);
            } elseif ($order_type == 3) {
                $this->db->where('is_delivery', 1);
                $this->db->where('is_take_away', 0);
            }
        }
        if ($payment_option != "") {
            $this->db->where("b.id in (select bill_id from bill_payment where payment_option='" . $payment_option . "')");
        }
        $total_hpp = $this->db->get()->row();

        //SHARING
        $this->db->select('sum(bi.amount) as amount', false)
            ->from('bill b')
            ->join('bill_information bi', 'b.id=bi.bill_id')
            ->where('bi.info like ', '%sharing%')
            ->where('bi.type', 4)
            ->where('b.is_refund', 0);
        if ($start_date) {
            $this->db->where('b.created_at >= ', $start_date);
        }
        if ($end_date) {
            $this->db->where('b.created_at <= ', $end_date);
        }
        if ($user_id) {
            $this->db->where('cashier_id', $user_id);
        }
        if ($order_type != "") {
            if ($order_type == 1) {
                $this->db->where('is_delivery', 0);
                $this->db->where('is_take_away', 0);
            } elseif ($order_type == 2) {
                $this->db->where('is_delivery', 0);
                $this->db->where('is_take_away', 1);
            } elseif ($order_type == 3) {
                $this->db->where('is_delivery', 1);
                $this->db->where('is_take_away', 0);
            }
        }
        if ($payment_option != "") {
            $this->db->where("b.id in (select bill_id from bill_payment where payment_option='" . $payment_option . "')");
        }
        $total_sharing = $this->db->get()->row();

        return array(
            "total_petty_cash" => (!empty($total_petty_cash) ? $total_petty_cash->amount : 0),
            "total_dp" => (!empty($total_dp) ? $total_dp->amount : 0),
            "total_discount" => (!empty($total_discount) ? $total_discount->amount : 0),
            "total_courier_service" => (!empty($total) ? $total->total_courier_service : 0),
            "sum_total_price" => (!empty($total) ? $total->sum_total_price : 0),
            "sum_total_cogs" => (!empty($total) ? $total->sum_total_cogs : 0),
            "sum_profit" => (!empty($total) ? $total->sum_total_price - $total->sum_total_cogs : 0),
            "total_transaction" => (!empty($total) ? $total->total_transaction : 0),
            "total_customer_count" => (!empty($total) ? $total->total_customer_count : 0),
            "total_quantity_order" => (!empty($total_quantity_order) ? $total_quantity_order->amount : 0),
            "total_dinein" => (!empty($total_other) ? $total_other->total_dinein : 0),
            "total_count_dinein" => (!empty($total_other) ? $total_other->total_count_dinein : 0),
            "total_takeaway" => (!empty($total_other) ? $total_other->total_takeaway : 0),
            "total_count_takeaway" => (!empty($total_other) ? $total_other->total_count_takeaway : 0),
            "total_delivery" => (!empty($total_other) ? $total_other->total_delivery : 0),
            "total_count_delivery" => (!empty($total_other) ? $total_other->total_count_delivery : 0),
            "total_promo" => (!empty($total_promo) ? $total_promo->total_promo : 0),
            "total_count_promo" => (!empty($total_promo) ? $total_promo->total_count_promo : 0),
            "total_voucher" => (!empty($total_voucher) ? $total_voucher->total_voucher : 0),
            "total_count_voucher" => (!empty($total_voucher) ? $total_voucher->total_count_voucher : 0),
            "total_dp_out" => (!empty($total_dp_out) ? $total_dp_out->dp_out : 0),
            "total_dp_in" => (!empty($total_dp_in) ? $total_dp_in->dp_in : 0),
            "total_balance_cash" => (!empty($total_balance_cash) ? $total_balance_cash->balance_cash : 0),
            "total_bon_bill" => (!empty($total_bon_bill) ? $total_bon_bill->amount : 0),
            "total_pending_bill" => (!empty($total_pending_bill) ? $total_pending_bill->amount : 0),
            "total_compliment" => (!empty($total_compliment) ? $total_compliment->amount : 0),
            "total_sell" => (!empty($total_sell) ? $total_sell->total_price : 0),
            "total_hpp" => (!empty($total_hpp) ? $total_hpp->total_cogs : 0),
            "total_ongkir" => (!empty($total_ongkir) ? $total_ongkir->amount : 0),
            "total_tax" =>  (!empty($total_sub_tax) ? $total_sub_tax->amount : 0),
            "total_sharing" => (!empty($total_sharing) ? $total_sharing->amount : 0),
            "total_pembulatan" => (!empty($total_pembulatan) ? $total_pembulatan->amount : 0)
        );
    }

    public function get_transaction_data($user_id = FALSE, $start_date = FALSE, $end_date = FALSE, $order_type = "", $payment_option = "")
    {
        $this->db->select('
					receipt_number, payment_date,
					total_price,customer_count,
					order_id,IF(is_delivery=0 and is_take_away=0,"Dine In",IF(is_delivery=1,"Delivery","Takeaway")) as order_type
        ', false)
            ->from('bill')
            ->where('bill.is_refund', 0);
        if ($payment_option != "") {
            $this->db->where("bill.id in (select bill_id from bill_payment where bill_id=bill.id payment_option='" . $payment_option . "')");
        }
        if ($start_date) {
            $this->db->where('payment_date >= ', $start_date);
        }

        if ($end_date) {
            $this->db->where('payment_date <= ', $end_date);
        }

        if ($user_id) {
            $this->db->where('cashier_id', $user_id);
        }
        if ($order_type != "") {
            if ($order_type == 1) {
                $this->db->where('is_delivery', 0);
                $this->db->where('is_take_away', 0);
            } elseif ($order_type == 2) {
                $this->db->where('is_delivery', 0);
                $this->db->where('is_take_away', 1);
            } elseif ($order_type == 3) {
                $this->db->where('is_delivery', 1);
                $this->db->where('is_take_away', 0);
            }
        }
        $this->db->order_by('payment_date', 'asc');

        return $this->db->get()->result();

    }


    public function get_sales_menu_data($category_id = FALSE, $start_date = FALSE, $end_date = FALSE,$menu_id = FALSE)
    {
        $this->db->select('menu.id as id, 
            menu_id, bill_menu.menu_name, sum(quantity) as total_quantity, 
            sum(price*quantity) as total_price, 
            sum(cogs*quantity) as total_cogs,
            (sum(price*quantity)-sum(cogs*quantity)) as profit,
            category_name,
            bill_menu.created_at')
            ->join('bill', 'bill.id = bill_menu.bill_id')
            ->join('menu', 'menu.id = bill_menu.menu_id')
            ->join('category', 'category.id = menu.category_id')
            ->from('bill_menu')
            ->where('bill.is_refund', 0)
            ->group_by('bill_menu.menu_id');


        if ($category_id) {
            $this->db->where('outlet_id', $category_id);
        }
		
		if ($menu_id) {
            $this->db->where('menu.id', $menu_id);
        }

        if ($start_date) {
            $this->db->where('bill.created_at >= ', $start_date);
        }

        if ($end_date) {
            $this->db->where('bill.created_at <= ', $end_date);
        }

        return $this->db->get()->result();
    }

    public function get_moving_item_data($category_id = FALSE, $start_date = FALSE, $end_date = FALSE)
    {
        $this->db->select('menu.id as id, 
            menu_id, menu.menu_name, 
            sum(quantity) as total_quantity, 
            sum(price*quantity) as total_price,
            price as harga_menu,
            cogs as harga_hpp,
            sum(cogs*quantity) as total_cogs,
            (sum(price*quantity)-sum(cogs*quantity)) as total_profit, 
            category_name,
            bill_menu.created_at,
            (
                select  sum(quantity)  as reguler 
                from bill_menu 
                join bill on bill.id = bill_menu.bill_id 
                join bill_payment bill_payment ON(bill_menu.bill_id = bill_payment.bill_id) 
                where bill.is_refund = 0 and bill_payment.payment_option != 5 and bill_menu.menu_id = menu.id
                
                
             
            ) as qty_reguler, 
             (
                select  sum(quantity)  as reguler 
                from bill_menu
                join bill on bill.id = bill_menu.bill_id  
                join bill_payment bill_payment ON(bill_menu.bill_id = bill_payment.bill_id) 
                where bill.is_refund = 0 and bill_payment.payment_option != 5 and bill_menu.menu_id = menu.id
                
                
             
            ) * price as total_reguler, 
            (
                select  sum(quantity)  as compliment 
                from bill_menu
                join bill on bill.id = bill_menu.bill_id   
                join bill_payment   ON(bill_menu.bill_id = bill_payment.bill_id)
             
                where bill.is_refund = 0 and bill_payment.payment_option = 5 and bill_menu.menu_id = menu.id
             
            ) as  qty_compliment,
            (
                select  sum(quantity)  as compliment 
                from bill_menu
                join bill on bill.id = bill_menu.bill_id  
                join bill_payment bill_payment ON(bill_menu.bill_id = bill_payment.bill_id) 
                where bill.is_refund = 0 and bill_payment.payment_option = 5 and bill_menu.menu_id = menu.id
                
                
             
            ) * cogs as total_compliment, 
        ')
            ->join('menu', 'menu.id = bill_menu.menu_id')
            ->join('category', 'category.id = menu.category_id')
            ->from('bill_menu')
            ->join('bill', 'bill.id = bill_menu.bill_id')
            ->where('bill.is_refund', 0)
            ->group_by('bill_menu.menu_id');


        if ($category_id) {
            $this->db->where('outlet_id', $category_id);
        }

        if ($start_date) {
            $this->db->where('bill_menu.created_at >= ', $start_date);
        }

        if ($end_date) {
            $this->db->where('bill_menu.created_at <= ', $end_date);
        }

        return $this->db->get()->result();
    }

    public function get_open_close_data($open_by = FALSE, $close_by = FALSE, $start_date = FALSE, $end_date = FALSE)
    {
        $this->db->select('open_close_cashier.id, open_at, open_by, close_by,
            (SELECT name from users where users.id = open_by) as open_by_user, 
            (SELECT name from users where users.id = close_by) as  close_by_user, 
            close_at, total_transaction, total_cash')
            ->from('open_close_cashier');

        if ($start_date) {
            $this->db->where('open_at >= ', $start_date);
        }

        if ($end_date) {
            $this->db->where('close_at <= ', $end_date);
        }

        if ($open_by) {
            $this->db->where('open_by', $open_by);
        }

        if ($close_by) {
            $this->db->where('close_by', $close_by);
        }

        return $this->db->get()->result();
    }

    public function get_member_by_id($member_id)
    {
        $this->db->select('*')
            ->from('member')->join('member_category', 'member.member_category_id = member_category.id')
            ->where('member.id', $member_id);

        return $this->db->get()->row();
    }

    public function get_member_by_id_db($id)
    {
        $this->db->select('*')
            ->from('member')->join('member_category', 'member.member_category_id = member_category.id')
            ->where('member.id', $id);

        return $this->db->get()->row();
    }
    public function get_petty_cash_data($ge_id = FALSE,$start_date=FALSE, $end_date=FALSE)
    {
          $this->db->select('pc.id,pc.date,pc.description,pc.amount,u.name, ge.name as gename')
        
        ->from('petty_cash pc')
         ->join("users u","pc.user_id=u.id",'left')
         //->join("store s","pc.store_id=s.id")
         ->join("general_expenses ge", "pc.ge_id = ge.id");
        

        // if($store_id){
        //     $this->db->where('pc.store_id', $store_id);
        // }
        if($ge_id){
            $this->db->where('ge.id', $ge_id);
        }
        if($start_date){
            $this->db->where('pc.date >= ', $start_date);
        }

        if($end_date){
            $this->db->where('pc.date <= ', $end_date);
        }


        return $this->db->get()->result();
    }
    function get_bill_payment($bill_id = FALSE)
    {
        $this->db->select('*')
            ->from('bill_payment')
            ->join('bill', 'bill.id = bill_payment.bill_id')
            ->join('enum_payment_option', 'bill_payment.payment_option = enum_payment_option.id')
            ->where('bill.is_refund', 0)
            ->where('bill_payment.bill_id', $bill_id);

        $result = $this->db->get()->result();

        return $result;

    }

    function get_void_data($start_date = FALSE, $end_date = FALSE)
    {

        $this->db->select(
            'void.id, 
            menu.menu_name, amount,(SELECT `users`.`name`  FROM users
      WHERE `users`.`id`= `void`.`user_unlock_id`) as user_unlock_name, void_note, 
            is_deduct_stock, users.name,void.created_at')
            ->from('void')
            ->join('users', 'users.id = void.created_by')
            ->join('menu', 'menu.id = void.order_menu_id');

        if ($start_date && $end_date) {
            $this->db->where('void.created_at >= ', $start_date);
            $this->db->where('void.created_at <= ', $end_date);

        }

        $result = $this->db->get()->result();
        return $result;


    }


    function get_sales_category_data($start_date = FALSE, $end_date = FALSE)
    {
        $this->db->select('menu.id as id, 
            menu_id, 
            menu.menu_name, 
            sum(quantity) as total_quantity, 
            sum(price*quantity) as total_price, 
            sum(cogs * quantity) as total_cogs, 
            (sum(price*quantity)-sum(cogs * quantity)) as profit, 
            category_name,
            bill_menu.created_at,
            outlet_name
        ')
            ->join('menu', 'menu.id = bill_menu.menu_id')
            ->join('category', 'category.id = menu.category_id')
            ->join('outlet', 'outlet.id = category.outlet_id')
            ->from('bill_menu')
            ->join('bill b', 'b.id = bill_menu.bill_id')
            ->where('b.is_refund', 0)
            ->group_by('category.outlet_id');
        if ($start_date && $end_date) {
            $this->db->where('bill_menu.created_at >= ', $start_date);
            $this->db->where('bill_menu.created_at <= ', $end_date);
        }

        $result = $this->db->get()->result();

        return $result;


    }

    function get_all_menu_ingredients($menu_id)
    {
        $this->db->select('mi.*,u.code,i.name as inventory_name')
            ->from('menu_ingredient mi')
            ->join('inventory i', 'mi.inventory_id=i.id')
            ->join('uoms u', "mi.uom_id=u.id")
            ->where('mi.menu_id', $menu_id);
        return $this->db->get()->result();
    }

    /*
    *   Modified by: Moh Tri R
    *   Modified at: 26/08/2016
    *   change table name of side_dish, from side_dish_ingredients to side_dish_ingredient
    */
    function get_all_side_dish_ingredients($menu_id)
    {
        $this->db->select('msd.menu_id as menu_id, sdi.*, u.code, i.name as inventory_name, i.uom_id')
            ->from('menu_side_dish msd')
            ->join('side_dish_ingredient sdi', 'sdi.side_dish_id=msd.side_dish_id')
            ->join('inventory i', 'i.id=sdi.inventory_id')
            ->join('uoms u', "u.id=i.uom_id")
            ->where('msd.menu_id', $menu_id);
        return $this->db->get()->result();
    }

    /*
    *   Added by: Moh Tri R
    *   Added at: 26/08/2016
    *   check if exist inventory compositions
    */
    function get_all_inventory_compositions($inventory_id)
    {
        $this->db->select('*')
            ->from('inventory_compositions')
            ->where('parent_inventory_id', $inventory_id);
        return $this->db->get()->result();
    }

    public function add_bill_cogs($data)
    {
        if (count($data) == count($data, COUNT_RECURSIVE)) return $this->save('bill_menu_inventory_cogs', $data);
        else return $this->db->insert_batch('bill_menu_inventory_cogs', $data);
    }

    function get_by_order_id($order_id = "")
    {
        $this->db->select("o.*,t.table_name,
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
        ) as counter,
        u.name as waiter_name
      ", false)
            ->from("order o")
            ->join("users u", "o.created_by=u.id", "left")
            ->join("order_menu om", "o.id=om.order_id")
            ->join("table t", "t.id=o.table_id", "left")
            ->group_by("o.id")
            ->where("o.id", $order_id);
        return $this->db->get()->row();
    }

    function get_order_menu($where = '', $is_hybrid = FALSE)
    {
        $select = "";
        if ($is_hybrid) {
            $select = "om.id, om.order_id, om.menu_id, bm.quantity,
            om.waiter_id, om.process_status, om.cooking_status, om.kitchen_status,
            om.note, om.created_at, om.created_by, om.dinein_takeaway,
            om.is_check, om.finished_at, om.post_to,m.menu_name,m.is_instant,m.process_checker,c.outlet_id";
        } else {
            $select = "om.id, om.order_id, om.menu_id, om.quantity,
            om.waiter_id, om.process_status, om.cooking_status, om.kitchen_status,
            om.note, om.created_at, om.created_by, om.dinein_takeaway,
            om.is_check, om.finished_at, om.post_to,m.menu_name,m.is_instant,m.process_checker,c.outlet_id";
        }
        $this->db->select($select)
            ->from("order_menu om")
            ->join("menu m", "om.menu_id=m.id", "left")
            ->join("category c", "m.category_id=c.id");
        if ($is_hybrid) {
            $this->db->join("bill_menu bm", "bm.order_menu_id=om.id");
        }
            $this->db->where($where);
        return $this->db->get()->result();
    }

    function get_history($order_id = "")
    {
        $this->db->select("
        o.*,t.floor_id,t.table_name,f.floor_name,f.store_id,s.store_name,
        (select sum(customer_count) from bill where order_id=o.id limit 1) as customer_count,
        ( select a.cashier_id from bill a where a.order_id=o.id limit 1) as cashier_id
      ")
            ->from("order o")
            ->join("table t", "o.table_id=t.id", "left")
            ->join("floor f", "t.floor_id=f.id", "left")
            ->join("store s", "f.store_id=s.id", "left")
            ->where("o.id", $order_id);
        return $this->db->get()->row();
    }

    function update_order_menu_suspend_kitchen()
    {
        $this->db->where("process_status", 1);
        $this->db->where_in("cooking_status", array(1, 2));
        $this->db->update("order_menu", array("cooking_status" => 3));
    }

    function update_order_menu_suspend_checker()
    {
        $this->db->where("process_status", 1);
        $this->db->where_in("cooking_status", array(7));
        $this->db->update("order_menu", array("cooking_status" => 3));
    }

    function get_order_menu_by_order_id($order_id = "")
    {
        $this->db->select("om.*,m.menu_name,m.menu_price,m.use_taxes,m.is_promo")
            ->from("order_menu om")
            ->join("menu m", "om.menu_id=m.id")
            ->where("om.order_id", $order_id);
        return $this->db->get()->result();
    }

    function get_customer_auto_complete($params = array())
    {
        $this->db->select("b.customer_name,IFNULL(b.customer_phone,'') as customer_phone,IFNULL(b.customer_address,'') as customer_address", false)
            ->distinct()->from("bill b")->where($params)->where("b.customer_name !=", '');
        return $this->db->get()->result();
    }

    function get_open_close_cashier_by_id($id = 0)
    {
        return $this->db->query("select o.*,u.name as open_by_name,u2.name as close_by_name from open_close_cashier o left join users u on o.open_by=u.id left join users u2 on o.close_by=u2.id where o.id='" . $id . "'")->row();
    }
    // function get_menu_by_open_close_cashier_by_id($id = 0)
    // {
    //     $this->db->select("*")
    //              ->from("open_close_cashier opc")
    //              ->join("bill as bl", "" )
    //              ->join("menu as mn", )
    //              ->where("o.id", $id );


    //     return $this->db->get()->result();

    // }
    function get_balance_cash_history($from_date = "", $to_date = "")
    {
        return $this->db->query("select IFNULL(sum(amount),0) as amount from balance_cash_history where date>='" . $from_date . "' and date<='" . $to_date . "'")->row();
    }


    function get_balance_cash_today($from_date = "", $to_date = "", $status = 0)
    {
        $date = strtotime($from_date);
        if ($status == 2) {
            $from_date_balance = " >= '".date('Y-m-d', $date)."' AND date <= '".$to_date."'";
        } else {
            $from_date_balance = " = curdate() ";
        }
        return $this->db->query("select 
                            ((IFNULL((select sum(amount) from balance_cash_history where date(date) ".$from_date_balance."), 0)) 
                            - 
                        (IFNULL((select sum(amount) from petty_cash where date(date) ".$from_date_balance." AND date <= '" . $from_date . "'), 0)) 
                        )
                        as amount
                        from dual;
                         ")->row();
    }

    function get_count_open_close_first()
    {
        return $this->db->query("select id from open_close_cashier where date(open_at) = curdate() ORDER BY  open_at ASC LIMIT 1")->row();
    }


    function get_oc_cashier($params = array())
    {
        $oc_cashier = $this->get_open_close_cashier_by_id($params['id']);
        //$oc_menu = $this->get_menu_by_open_close_cashier_by_id($params['id']);
        $results = array();

        $results['oc_cashier'] = $oc_cashier;

        $results['oc_menu'] = $this->db->query("
            select SUM(blm.quantity) as amount 
            from bill_menu blm
            inner join bill bl on bl.id=blm.bill_id 
            where bl.is_refund = 0 and bl.created_at>='" . $oc_cashier->open_at . "' " . ($oc_cashier->close_at != "" ? " and bl.created_at<='" . $oc_cashier->close_at . "'" : ""))->row();

        $results['oc_category'] = $this->db->query("
            select 
            ol.outlet_name as ctgname ,mn.menu_name as mnname,sum(blm.quantity*blm.price) AS mnpric, sum(blm.quantity) as quantt
            from category ctg
            LEFT JOIN outlet ol on ol.id = ctg.outlet_id
            LEFT JOIN menu mn ON mn.category_id = ctg.id
            LEFT JOIN bill_menu blm ON blm.menu_id = mn.id
            LEFT JOIN bill bl ON bl.id = blm.bill_id
            where bl.is_refund = 0 and bl.created_at>='" . $oc_cashier->open_at . "' " . ($oc_cashier->close_at != "" ? " and bl.created_at<='" . $oc_cashier->close_at . "'" : "")." group by ol.outlet_name")->result();

        $results['oc_takeaway'] = $this->db->query("
            select count(id) as takeaway, sum(total_price) as ttltkw
            from bill
            where is_refund = 0 and bill.created_at>='" . $oc_cashier->open_at . "' " . ($oc_cashier->close_at != "" ? " and bill.created_at<='" . $oc_cashier->close_at . "'" : "")." and is_take_away = '1'")->row();

        $results['oc_delivery'] = $this->db->query("
            select count(id) as delivery, sum(total_price) as ttldlv
            from bill
            where is_refund = 0 and bill.created_at>='" . $oc_cashier->open_at . "' " . ($oc_cashier->close_at != "" ? " and bill.created_at<='" . $oc_cashier->close_at . "'" : "")." and is_delivery = '1'")->row();

        $results['oc_dinein'] = $this->db->query("
            select count(id) as dinein, sum(total_price) as ttldn
            from bill
            where is_refund = 0 and bill.created_at>='" . $oc_cashier->open_at . "' " . ($oc_cashier->close_at != "" ? " and bill.created_at<='" . $oc_cashier->close_at . "'" : "")." and is_take_away = '0' and is_delivery='0'")->row();

        $results['begin_end_receipt'] = $this->db->query("
            select max(receipt_number) as end, min(receipt_number) as begin
            from bill
            where is_refund = 0 and bill.created_at>='" . $oc_cashier->open_at . "' " . ($oc_cashier->close_at != "" ? " and bill.created_at<='" . $oc_cashier->close_at . "'" : "")." and is_take_away = '0' and is_delivery='0'")->row();

        $results['net_sales'] = $this->db->query("select sum(price*quantity) as amount from bill_menu inner join bill on bill.id=bill_menu.bill_id where bill.is_refund = 0 and bill.created_at>='" . $oc_cashier->open_at . "' " . ($oc_cashier->close_at != "" ? " and bill.created_at<='" . $oc_cashier->close_at . "'" : ""))->row();

//        $results['taxes'] = $this->db->query("select SUM(IF(bi.info like '%ppn%',bi.amount,0)) as amount
//      from bill_information bi where created_at>='" . $oc_cashier->open_at . "' " . ($oc_cashier->close_at != "" ? " and created_at<='" . $oc_cashier->close_at . "'" : ""))->row();
        $results['taxes'] = $this->db->query("select SUM(bi.amount) as amount
      from bill_information bi join bill b on b.id = bi.bill_id where b.is_refund = 0 and bi.type=1 and bi.info not like '%ongkos kirim%' and bi.info not like '%pembulatan%' and bi.created_at>='" . $oc_cashier->open_at . "' " . ($oc_cashier->close_at != "" ? " and bi.created_at<='" . $oc_cashier->close_at . "'" : ""))->row();

        $results['taxes_foreach'] = $this->db->query("select info, sum(amount) as amount
      from bill_information bi join bill b on b.id = bi.bill_id where b.is_refund = 0 and bi.type=1 and bi.info not like '%ongkos kirim%' and bi.info not like '%pembulatan%' and bi.created_at>='" . $oc_cashier->open_at . "' " . ($oc_cashier->close_at != "" ? " and bi.created_at<='" . $oc_cashier->close_at . "'" : "")."group by info")->result();


        $results['round_up'] = $this->db->query("select SUM(IF(bi.info like '%pembulatan%',bi.amount,0)) as amount from bill_information bi join bill b on b.id = bi.bill_id where b.is_refund = 0 and bi.created_at>='" . $oc_cashier->open_at . "' " . ($oc_cashier->close_at != "" ? " and bi.created_at<='" . $oc_cashier->close_at . "'" : ""))->row();

        $results['delivery_charge'] = $this->db->query("select sum(bi.amount) as amount from bill b inner join bill_information bi on b.id=bi.bill_id where b.is_refund = 0 and b.is_delivery=1 and bi.info like 'ongkos kirim%' and b.created_at>='" . $oc_cashier->open_at . "' " . ($oc_cashier->close_at != "" ? " and b.created_at<='" . $oc_cashier->close_at . "'" : "") . "")->row();

        $results['transaction'] = $this->db->query("select count(id) transaction_count from bill where is_refund = 0 and bill.created_at>='" . $oc_cashier->open_at . "' " . ($oc_cashier->close_at != "" ? " and bill.created_at<='" . $oc_cashier->close_at . "'" : ""))->row();
        //PENAMBAH
        $results['cash'] = $this->db->query("select count(payment_option=1) as countd, sum(amount) as amount from bill_payment join bill b on b.id = bill_payment.bill_id where b.is_refund = 0 and payment_option=1 and bill_payment.created_at>='" . $oc_cashier->open_at . "' " . ($oc_cashier->close_at != "" ? " and bill_payment.created_at<='" . $oc_cashier->close_at . "'" : ""))->row();

        $results['debit'] = $this->db->query("select count(bp.payment_option) as countd, ba.bank_name,sum(bp.amount) as amount from bill_payment bp join bill b on b.id = bp.bill_id inner join bank_account ba on bp.bank_account_id=ba.id where b.is_refund = 0 and bp.payment_option=2 and bp.created_at>='" . $oc_cashier->open_at . "' " . ($oc_cashier->close_at != "" ? " and bp.created_at<='" . $oc_cashier->close_at . "'" : "") . " group by bp.bank_account_id")->result();

        $results['credit'] = $this->db->query("select count(bp.payment_option) as countd, ba.bank_name,sum(bp.amount) as amount from bill_payment bp join bill b on b.id = bp.bill_id inner join bank_account ba on bp.bank_account_id=ba.id where b.is_refund = 0 and bp.payment_option=3 and bp.created_at>='" . $oc_cashier->open_at . "' " . ($oc_cashier->close_at != "" ? " and bp.created_at<='" . $oc_cashier->close_at . "'" : "") . " group by bp.bank_account_id")->result();

        $results['flazz'] = $this->db->query("select count(bp.payment_option) as countd, ba.bank_name,sum(bp.amount) as amount from bill_payment bp join bill b on b.id = bp.bill_id inner join bank_account ba on bp.bank_account_id=ba.id where b.is_refund = 0 and bp.payment_option=11 and bp.created_at>='" . $oc_cashier->open_at . "' " . ($oc_cashier->close_at != "" ? " and bp.created_at<='" . $oc_cashier->close_at . "'" : "") . " group by bp.bank_account_id")->result();

        $results['cash_dp'] = $this->db->query("select IFNULL(sum(r.down_payment),0) as amount from reservation r where r.dp_type=1 and r.status in(1,2) and r.created_at>='" . $oc_cashier->open_at . "' " . ($oc_cashier->close_at != "" ? " and r.created_at<='" . $oc_cashier->close_at . "'" : "") . "")->row();

        $results['transfer_dp'] = $this->db->query("select IFNULL(sum(r.down_payment),0) as amount from reservation r where r.dp_type=2 and r.status in(1,2) and r.created_at>='" . $oc_cashier->open_at . "' " . ($oc_cashier->close_at != "" ? " and r.created_at<='" . $oc_cashier->close_at . "'" : "") . "")->row();

        $results['transfer_direct_dp'] = $this->db->query("select IFNULL(sum(r.down_payment),0) as amount from reservation r where r.dp_type=3 and r.status in(1,2) and r.created_at>='" . $oc_cashier->open_at . "' " . ($oc_cashier->close_at != "" ? " and r.created_at<='" . $oc_cashier->close_at . "'" : "") . "")->row();
        //PENGURANG
        $results['delivery'] = $this->db->query("select sum(bc.courier_service_value) as amount from bill b inner join bill_courier_service bc on b.id=bc.bill_id where b.is_refund = 0 and b.is_delivery=1 and b.created_at>='" . $oc_cashier->open_at . "' " . ($oc_cashier->close_at != "" ? " and b.created_at<='" . $oc_cashier->close_at . "'" : "") . "")->row();

        $results['bon'] = $this->db->query("select sum(amount) as amount from bill_payment join bill on bill.id = bill_payment.bill_id where bill.is_refund = 0 and payment_option=9 and bill_payment.created_at>='" . $oc_cashier->open_at . "' " . ($oc_cashier->close_at != "" ? " and bill_payment.created_at<='" . $oc_cashier->close_at . "'" : "") . "")->row();

        $results['petty_cash'] = $this->db->query("select sum(amount) as amount from petty_cash where date>='" . $oc_cashier->open_at . "' " . ($oc_cashier->close_at != "" ? " and date<='" . $oc_cashier->close_at . "'" : "") . "")->row();

        $results['voucher'] = $this->db->query("select sum(bp.amount) as amount from bill_payment bp join bill b on b.id = bp.bill_id where b.is_refund = 0 and bp.payment_option=4 and bp.created_at>='" . $oc_cashier->open_at . "' " . ($oc_cashier->close_at != "" ? " and bp.created_at<='" . $oc_cashier->close_at . "'" : "") . "")->row();

        $results['compliment'] = $this->db->query("select sum(bp.amount) as amount from bill_payment bp join bill b on b.id = bp.bill_id where b.is_refund = 0 and bp.payment_option=5 and bp.created_at>='" . $oc_cashier->open_at . "' " . ($oc_cashier->close_at != "" ? " and bp.created_at<='" . $oc_cashier->close_at . "'" : "") . "")->row();

        $results['pending_bill_employee'] = $this->db->query("select count(b.id) as countd, sum(bp.amount) as amount from bill_payment bp join bill b on b.id = bp.bill_id where b.is_refund = 0 and bp.payment_option=7 and bp.created_at>='" . $oc_cashier->open_at . "' " . ($oc_cashier->close_at != "" ? " and bp.created_at<='" . $oc_cashier->close_at . "'" : "") . "")->row();

        $results['pending_bill_company'] = $this->db->query("select count(b.id) as countd, sum(bp.amount) as amount from bill_payment bp join bill b on b.id = bp.bill_id where b.is_refund = 0 and bp.payment_option=6 and bp.created_at>='" . $oc_cashier->open_at . "' " . ($oc_cashier->close_at != "" ? " and bp.created_at<='" . $oc_cashier->close_at . "'" : "") . "")->row();

        $results['cash_company'] = $this->db->query("select sum(bp.amount) as amount from bill_payment bp join bill b on b.id = bp.bill_id where b.is_refund = 0 and bp.payment_option=8 and bp.created_at>='" . $oc_cashier->open_at . "' " . ($oc_cashier->close_at != "" ? " and bp.created_at<='" . $oc_cashier->close_at . "'" : "") . "")->row();

        $results['discount'] = $this->db->query("select sum(amount) as amount from bill_information bi join bill b on b.id = bi.bill_id where b.is_refund = 0 and bi.type=2 and bi.created_at>='" . $oc_cashier->open_at . "' " . ($oc_cashier->close_at != "" ? " and bi.created_at<='" . $oc_cashier->close_at . "'" : "") . "")->row();


        $total_open_close_today = $this->get_count_open_close_first();

        if ($this->data['setting']['open_close_system'] == 2 && $total_open_close_today->id != $oc_cashier->id) {
            $results['balance_cash_history'] = $this->get_balance_cash_today($oc_cashier->open_at, $oc_cashier->close_at, $oc_cashier->status);
        } else {
            $results['balance_cash_history'] = $this->get_balance_cash_history($oc_cashier->open_at, $oc_cashier->close_at); 
         }

        // sum(IF(r.down_payment>b.total_price
        // ,b.total_price
        // ,r.down_payment
        // )) as amount
        $results['dp_out'] = $this->db->query("
        select  
          sum(r.down_payment) as amount
        from bill b
        inner join bill_payment bp on b.id=bp.bill_id 
        inner join `order` o on b.order_id=o.id
        inner join reservation r on o.reservation_id=r.id 
        where b.is_refund = 0 and b.created_at>='" . $oc_cashier->open_at . "' " . ($oc_cashier->close_at != "" ? " and b.created_at<='" . $oc_cashier->close_at . "'" : "") . " and bp.payment_option=10 and r.down_payment>0
      ")->row();
        return $results;
    }

    function get_new_order_for_notification($params = array())
    {
        return $this->db->query("
        select om.id, om.order_id, om.menu_id, om.quantity,
		om.waiter_id, om.process_status, om.cooking_status, om.kitchen_status,
		om.note, om.created_at, om.created_by, om.dinein_takeaway,
		om.is_check, om.finished_at, om.post_to,m.menu_name,c.outlet_id
        from order_menu om
        inner join menu m on om.menu_id=m.id
        inner join category c on m.category_id=c.id
        where m.is_instant='" . $params['is_instant'] . "' and om.order_id='" . $params['order_id'] . "' and om.cooking_status='" . $params['cooking_status'] . "' and om.process_status='" . $params['process_status'] . "'
      ")->result();
    }

    function delete_order($order_id = 0)
    {
        $this->db->query("
        DELETE `order`,order_menu,order_menu_option,order_menu_side_dish,order_package_menu
        FROM `order`
        INNER JOIN order_menu ON `order`.id = order_menu.order_id
        LEFT JOIN order_menu_option ON order_menu.id = order_menu_option.order_menu_id
        LEFT JOIN order_menu_side_dish ON order_menu.id = order_menu_side_dish.order_menu_id
        LEFT JOIN order_package_menu ON order_menu.id = order_package_menu.order_menu_id
        WHERE `order`.id = '" . $order_id . "'
      ");
    }

    function get_detail_order($order_id = 0)
    {
        return $this->db->select("o.*,u.name as waiter_name")->from("order o")
            ->join("users u", "o.created_by=u.id", "left")
            ->where("o.id", $order_id)
            ->get()->row();
    }

    function get_detail_order_menu($order_menu_id = 0)
    {
        return $this->db->select("om.*,u.name as waiter_name")->from("order_menu om")
            ->join("users u", "om.created_by=u.id", "left")
            ->where("om.id", $order_menu_id)
            ->get()->row();
    }

    function delete_bill($bill_id = 0)
    {
        $this->db->query("
        DELETE `bill`,bill_information,bill_payment,bill_menu,bill_menu_inventory_cogs,bill_menu_side_dish
        FROM `bill`
        LEFT JOIN bill_information ON `bill`.id = bill_information.bill_id
        LEFT JOIN bill_payment ON `bill`.id = bill_payment.bill_id
        LEFT JOIN bill_menu ON `bill`.id = bill_menu.bill_id
        LEFT JOIN bill_menu_inventory_cogs ON `bill_menu`.id=bill_menu_inventory_cogs.bill_menu_id
        LEFT JOIN bill_menu_side_dish ON `bill_menu`.id=bill_menu_side_dish.bill_menu_id
        WHERE `bill`.id = '" . $bill_id . "'
      ");
    }

    function get_bon_bill($start_date, $end_date)
    {
        return $this->db->query("
            select IFNULL(sum(amount),0) as amount from bill_payment join bill on bill.id = bill_payment.bill_id
            where bill.is_refund = 0 and payment_option=9 and bill.created_at>='" . $start_date . "' and bill.created_at<='" . $end_date . "'
        ")->row();
    }

    function get_pending_bill($start_date, $end_date)
    {
        return $this->db->query("
            select IFNULL(sum(amount),0) as amount from bill_payment join bill on bill.id = bill_payment.bill_id
            where bill.is_refund = 0 and payment_option in (6,7) and bill.created_at>='" . $start_date . "' and bill.created_at<='" . $end_date . "'
        ")->row();
    }

    function get_compliment($start_date, $end_date)
    {
        return $this->db->query("
			select IFNULL(sum(amount),0) as amount from bill_payment join bill on bill.id = bill_payment.bill_id
            where bill.is_refund = 0 and payment_option=5 and bill.created_at>='" . $start_date . "' and bill.created_at<='" . $end_date . "'
		")->row();
    }

    function get_voucher_bill($start_date, $end_date)
    {
        return $this->db->query("
            select IFNULL(sum(amount),0) as amount from bill_payment join bill on bill.id = bill_payment.bill_id
            where bill.is_refund = 0 and payment_option=4 and bill.created_at>='" . $start_date . "' and bill.created_at<='" . $end_date . "'
        ")->row();
    }

    function get_dp_in($start_date, $end_date)
    {
        return $this->db->query("
            select IFNULL(sum(down_payment),0) as amount from reservation where (status = 1 or status = 2) and created_at >= '" . $start_date . "' and created_at <= '" . $end_date . "'
        ")->row();
    }

    function get_dp_out($start_date, $end_date)
    {
        return $this->db->query("
            select IFNULL(sum(amount), 0) AS amount from bill_payment join bill on bill.id = bill_payment.bill_id
            where bill.is_refund = 0 and payment_option = 10 and bill.created_at >= '" . $start_date . "' and bill.created_at <= '" . $end_date . "'
        ")->row();
    }

    function get_petty_cash($start_date, $end_date)
    {
        //KAS KECIL
        return $this->db->query("
			select IFNULL(sum(amount),0) as amount from petty_cash 
			where date >= '" . $start_date . "' and date <= '" . $end_date . "'
		")->row();
    }

    public function get_enum_payment_option()
    {
        $this->db->select('*');
        $this->db->from('enum_payment_option');

        $this->db->order_by('id', 'ASC');
        $query = $this->db->get();
        $data = $query->result();

        $results = array();
        foreach ($data as $row) {
            $results[$row->id] = $row->value;
        }

        return $results;
    }

    public function get_account_by_inventory($bill_id=0) {
        $this->db->select('bm.id,
                            bm.menu_id,
                            SUM(bm.quantity) AS qty,
                            i.price,
                            SUM(bm.quantity * i.price) AS total,
                            mi.inventory_id,
                            ia.account_id,
                            a.name as acc_name,
                            ia.coa_type,
                            i.name')
            ->from('bill_menu bm')
            ->join('menu_ingredient mi', 'mi.menu_id = bm.menu_id')
            ->join('inventory_account ia', 'ia.inventory_id = mi.inventory_id')
            ->join('inventory i', 'ia.inventory_id = i.id')
            ->join('enum_coa_type ec', 'ec.id = ia.coa_type')
            ->join('account a', 'a.id = ia.account_id')
            ->where('mi.quantity >', 0)
            ->where('ec.name', 'cogs')
            ->where('bm.bill_id', $bill_id)
            ->group_by('i.id');

        return $this->db->get()->result();
    }

    public function get_total_credit_by_inventory($bill_id=0) {
        $this->db->select('SUM(bm.quantity * i.price) AS total')
            ->from('bill_menu bm')
            ->join('menu_ingredient mi', 'mi.menu_id = bm.menu_id')
            ->join('inventory_account ia', 'ia.inventory_id = mi.inventory_id')
            ->join('inventory i', 'ia.inventory_id = i.id')
            ->join('enum_coa_type ec', 'ec.id = ia.coa_type')
            ->where('mi.quantity >', 0)
            ->where('ec.name', 'cogs')
            ->where('bm.bill_id', $bill_id);

        return $this->db->get()->result_array();
    }

    public function get_menu_by_order_id($order_id){
        $this->db->select('menu.*');
        $this->db->from('menu');
        $this->db->join('order_menu','order_menu.menu_id=menu.id');
        $this->db->where('order_menu.order_id',$order_id);

        return $this->db->get()->result_array();
    }

    public function get_voucher_code($voucher_group_id, $voucher_qty){
        $this->db->select('voucher.*, vg.amount');
        $this->db->from('voucher');
        $this->db->join('voucher_group vg', 'vg.id = voucher.voucher_group_id');
        $this->db->where('voucher.voucher_group_id',$voucher_group_id);
        $this->db->where('voucher.status', 0);
        $this->db->limit($voucher_qty);
        $this->db->order_by('code','asc');

        return $this->db->get()->result_array();
    }

    public function get_account_by_inventory_side_dish($bill_id=0) {
        $this->db->select('SUM(bm.quantity * sdi.quantity * i.price) AS total,
                            ia.account_id,
                            a.name as acc_name,
                            sdi.inventory_id')
            ->from('side_dish_ingredient sdi')
            ->join('side_dish sd', 'sd.id = sdi.side_dish_id')
            ->join('bill_menu_side_dish bmsd', 'bmsd.side_dish_id = sd.id')
            ->join('bill_menu bm', 'bm.id = bmsd.bill_menu_id')
            ->join('inventory_account ia', 'ia.inventory_id = sdi.inventory_id')
            ->join('inventory i', 'i.id = sdi.inventory_id')
            ->join('account a', 'a.id = ia.account_id')
            ->where('ia.coa_type', 3)
            ->where('bm.bill_id', $bill_id)
            ->group_by('i.id');

        return $this->db->get()->result();
    }

    public function get_account_by_inventory_compositions($bill_id=0, $parent_inventory_id=0) {
        $this->db->select('a.name as acc_name,
                            ia.account_id,
                            SUM(
                                ic.quantity * (
                                    SELECT
                                        SUM(bm.quantity)
                                    FROM
                                        bill_menu bm
                                    WHERE
                                        bill_id = '.$bill_id.'
                                ) * i.price
                            ) AS total')
            ->from('inventory_compositions ic')
            ->join('inventory_account ia', 'ia.inventory_id = ic.inventory_id')
            ->join('inventory i', 'i.id = ic.inventory_id')
            ->join('account a', 'a.id = ia.account_id')
            ->where('ia.coa_type', 3)
            ->where('ic.parent_inventory_id', $parent_inventory_id)
            ->group_by('i.id');

        return $this->db->get()->result();
    }
	
	
	function get_sales_by_category_menu_data($start_date=FALSE, $end_date=FALSE,$store_id=FALSE,$outlet_id=FALSE,$category_id=FALSE){
        $this->db->select('
           store.store_name,outlet.outlet_name,category.category_name,sum(bill_menu.quantity) as total_quantity,
		   sum(bill_menu.price*bill_menu.quantity) as total_price,date(bill_menu.created_at) as created_at
        ')
        ->from('bill_menu')
        ->join('bill', 'bill.id = bill_menu.bill_id')
        ->join('menu', 'menu.id = bill_menu.menu_id','left')
        ->join('category', 'category.id = menu.category_id','left')
        ->join('outlet', 'outlet.id = category.outlet_id','left')
        ->join('store', 'store.id = outlet.store_id')
        ->where('bill.is_refund', 0)
        ->group_by('store.id,outlet.id,category.id,date(bill_menu.created_at)');
        if($start_date && $end_date){
            $this->db->where('bill_menu.created_at >= ', $start_date);
            $this->db->where('bill_menu.created_at <= ', $end_date);
        }
        if($store_id){
            $this->db->where('outlet.store_id', $store_id);
        }
		if($outlet_id){
            $this->db->where('outlet.id', $outlet_id);
        }
		if($category_id){
            $this->db->where('category.id', $category_id);
        }
         $result = $this->db->get()->result();
        return $result;


    }
    
	/*
	* get order menu check with sidedish data
	*/
    function get_order_menu_check_sidedish($condition = array(), $dining_type, $menu_id, $receipt_number){
		$select = array(
			"order_menu.id", "order_menu.order_id", "order_menu.menu_id",
			"order_menu.quantity", "order_menu.waiter_id", "order_menu.process_status",
			"order_menu.cooking_status", "order_menu.kitchen_status", "order_menu.note",
			"order_menu.created_at", "order_menu.created_by", "order_menu.dinein_takeaway",
			"order_menu.is_check", "order_menu.finished_at", "order_menu.post_to",
		);
        $this->db->select($select)
            ->from('order_menu')
            ->join('order_menu_side_dish','order_menu_side_dish.order_menu_id = order_menu.id', 'left')
            ->where($condition);

        if ($dining_type == 3 && $receipt_number == "") {
            $this->db->where("order_menu.id not in (select order_menu_id from bill_menu where menu_id = ".$menu_id.")");
        }
         
        return $this->db->get()->result();
    }
}