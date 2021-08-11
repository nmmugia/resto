<?php

class Report_Model  extends MY_Model{
	function __construct(){
    parent::__construct();
  }
  public function hourly_sales_report($params=array()){
    return $this->db->query("
      SELECT
        DATE_FORMAT(b.end_order,'%H') as hour,(select count(id) from bill where 
        DATE_FORMAT(end_order,'%H')=DATE_FORMAT(b.end_order,'%H') and date(end_order)='".$params['date']."') as no_of_transaction,sum((bm.quantity*bm.price)) as revenue
      FROM bill b
      inner join bill_menu bm on b.id=bm.bill_id
      where b.is_refund = 0 and date(b.end_order)='".$params['date']."'
      group by DATE_FORMAT(b.end_order,'%H')
      order by DATE_FORMAT(b.end_order,'%H') ASC
    ")->result();
  }
  public function sales_summary_report($params=array()){
    return $this->db->query("
      SELECT
        m.menu_name,sum(bm.quantity*bm.price) as sales,sum(bm.quantity*bm.cogs) as cogs
      FROM bill b
      inner join bill_menu bm on b.id=bm.bill_id
      inner join menu m on m.id=bm.menu_id
      where b.is_refund = 0 and date(b.end_order)='".$params['date']."'
      group by bm.menu_id
      order by m.menu_name ASC
    ")->result();
  }
  public function sales_by_day_report($params=array()){
    return $this->db->query("
      SELECT
        DATE(b.end_order) as date,(select count(id) from bill where DATE(end_order)=DATE(b.end_order)) as no_of_transaction,sum((bm.quantity*bm.price)) as revenue
      FROM bill b
      inner join bill_menu bm on bm.bill_id=b.id
      where b.is_refund = 0 and DATE_FORMAT(b.end_order,'%Y-%m')='".$params['date']."'
      group by DATE(b.end_order)
      order by b.end_order ASC
    ")->result();
  }
  public function customer_by_day_report($params=array()){
    return $this->db->query("
      SELECT
        DATE(b.end_order) as date,(select count(id) from bill where DATE(end_order)=DATE(b.end_order)) as no_of_transaction,sum(customer_count) as total_customer
      FROM bill b
      where b.is_refund = 0 and DATE_FORMAT(b.end_order,'%Y-%m')='".$params['date']."'
      group by DATE(b.end_order)
      order by b.end_order ASC
    ")->result();
  }
  public function sales_by_department_category_report($params=array()){
    return $this->db->query("
      select
        ".($params['type']=="department" ? "o.outlet_name" : "c.category_name")." as name,count(bm.id) as no_of_transaction,sum(bm.quantity*bm.price) as revenue
      from bill b
      left join bill_menu bm on b.id=bm.bill_id
      left join menu m on m.id=bm.menu_id
      left join category c on c.id=m.category_id
      left join outlet o on o.id=c.outlet_id
      where b.is_refund = 0 and date(bm.created_at)>='".$params['from']."' and date(bm.created_at)<='".$params['to']."'
      group by ".($params['type']=="department" ? "c.outlet_id" : "c.id")."
      order by ".($params['type']=="department" ? "o.outlet_name" : "c.category_name")." ASC
    ")->result();
  }
  public function top_worst_product_report($params=array()){
    return $this->db->query("
      SELECT
        m.menu_name,sum(bm.quantity*bm.price) as sales,sum(bm.quantity*bm.cogs) as cogs,sum((bm.quantity * bm.price)-(bm.quantity * bm.cogs)) as gross_profit
      FROM bill b
      inner join bill_menu bm on b.id=bm.bill_id
      inner join menu m on m.id=bm.menu_id
      where b.is_refund = 0 and date(b.start_order)>='".$params['from']."' and date(b.end_order)<='".$params['to']."'
      group by bm.menu_id
      order by sales desc,gross_profit desc
    ")->result();
  }
  public function sales_by_waiter_report($params=array()){
    $parameter=(isset($params['perpage']) ? " limit ".$params['offset'].",".$params['perpage'] : "");
    return $this->db->query("
      select
        u.name,count(b.id) as no_of_transaction,sum(bm.quantity*bm.price) as revenue
      from bill b
      inner join bill_menu bm on b.id=bm.bill_id
      inner join users u on u.id=bm.created_by
      where is_refund = 0 and date(bm.created_at)>='".$params['from']."' and date(bm.created_at)<='".$params['to']."'
      group by bm.created_by
      order by revenue DESC
    ".$parameter)->result();
  }
  public function inventory_used_report($params=array()){
    return $this->db->query("
      select
        mi.inventory_id,i.name,i.unit,sum((bm.quantity*mi.quantity)) as total_used
      from bill b
      inner join bill_menu bm on b.id=bm.bill_id
      inner join menu_ingredient mi on mi.menu_id=bm.menu_id
      inner join inventory i on i.id=mi.inventory_id
      where b.is_refund = 0 and date(b.start_order)>='".$params['from']."' and date(b.end_order)<='".$params['to']."'
      group by mi.inventory_id
      order by i.name ASC
    ")->result();
  }
  public function inventory_used_detail_report($params=array()){
    return $this->db->query("
      select
        m.menu_name,sum((bm.quantity)) as total_used
      from bill b
      inner join bill_menu bm on b.id=bm.bill_id
      inner join menu m on m.id=bm.menu_id
      inner join menu_ingredient mi on mi.menu_id=bm.menu_id
      where b.is_refund = 0 and date(b.start_order)>='".$params['from']."' and date(b.end_order)<='".$params['to']."' and mi.inventory_id='".$params['inventory_id']."'
      group by bm.menu_id
      order by m.menu_name ASC
    ")->result();
  }
  public function summary(){
    return $this->db->query("
      select (
        SELECT
          sum((bm.quantity*bm.price))
        FROM bill b
        inner join bill_menu bm on bm.bill_id=b.id
        where b.is_refund = 0 and DATE(b.end_order)=DATE(NOW())
      )  as total_sales_today,
      (
        SELECT
          sum((bm.quantity*bm.price))
        FROM bill b
        inner join bill_menu bm on bm.bill_id=b.id
        where b.is_refund = 0 and DATE_FORMAT(b.end_order,'%Y%m')=DATE_FORMAT(NOW(),'%Y%m')
      )  as total_sales_current_month,
      (
        SELECT
          sum((bm.quantity*bm.price))
        FROM bill b
        inner join bill_menu bm on bm.bill_id=b.id
        where b.is_refund = 0 and YEAR(b.end_order)=YEAR(NOW())
      )  as total_sales_current_year
    ")->row();
  }
  public function total_sales_waiter($params=array())
  {
    $parameter=($params['user_id']!="" ? " and u.id='".$params['user_id']."'" : "");
    return $this->db->query("
      SELECT u.id,u.name,(select sum(bm.quantity*bm.price) from bill_menu bm join bill b on b.id=bm.bill_id where b.is_refund = 0 and date(bm.created_at)='".$params['date']."' and created_by=u.id) as total_sales 
      from users u 
      join users_groups g on u.id = g.user_id
      join feature_access fa on g.group_id=fa.groups_id
      join feature f on fa.feature_id=f.id
      where f.key in ('dinein')
      ".$parameter."
      group by u.id
    ")->result();
  }
  public function total_sales_waiter_detail($params=array())
  {
    return $this->db->query("
      SELECT u.id,u.name,m.menu_name,sum(bm.quantity) as quantity,bm.price
        from bill_menu bm 
        join bill b on b.id=bm.bill_id 
      inner join users u on bm.created_by=u.id 
      inner join menu m on bm.menu_id=m.id 
      where b.is_refund = 0 and u.id='".$params['user_id']."' and date(bm.created_at)='".$params['date']."'
      group by bm.menu_id,bm.price
    ")->result();
  }
  public function total_quantity_order_table_waiter($params=array()){
    $parameter=($params['user_id']!="" ? " and u.id='".$params['user_id']."'" : "");
    return $this->db->query("
      SELECT u.id,u.name,
        (select sum(bm.quantity) from bill_menu bm join bill b on b.id=bm.bill_id where b.is_refund = 0 and date(bm.created_at)='".$params['date']."' and created_by=u.id) as total_quantity,
        (select count(b.id) from bill b where b.is_refund = 0 and b.id in (
          select bm.bill_id from bill_menu bm where date(bm.created_at)='".$params['date']."' and created_by=u.id
        )) as total_bill
      from users u 
      join users_groups g on u.id = g.user_id
      join feature_access fa on g.group_id=fa.groups_id
      join feature f on fa.feature_id=f.id
      where f.key in ('dinein')
      ".$parameter."
      group by u.id
    ")->result();
  }
  public function total_quantity_order_table_waiter_detail($params=array()){
    return $this->db->query("
      SELECT t.table_name,count(b.id) as table_count
        from bill b
      inner join `table` t on b.table_id=t.id
      where b.is_refund = 0 and b.table_id!=0 and b.id in (
        select bm.bill_id from bill_menu bm
        where date(bm.created_at)='".$params['date']."' and bm.created_by='".$params['user_id']."'
      )
      group by b.table_id
    ")->result();
  }
  public function achievement_waiter($params=array())
  {
    return $this->db->query("
      SELECT t.id as target_id,u.id,u.name,t.target_type,t.is_percentage,t.reward,t.target_by_total,
        (
          select sum(bm.quantity*bm.price) from bill_menu bm join bill b on b.id=bm.bill_id where b.is_refund = 0 and bm.created_by=u.id and YEAR(bm.created_at)='".$params['year']."' and MONTH(bm.created_at)='".$params['month']."'
        ) as achievement_by_total,
        IFNULL((
          SELECT IF((
            select sum(bm.quantity) from bill_menu bm join bill b on b.id=bm.bill_id where b.is_refund = 0 and td.menu_id=bm.menu_id AND bm.created_by = u.id
            AND YEAR(bm.created_at)= '".$params['year']."' AND MONTH(bm.created_at)= '".$params['month']."'
          ) >= td.target_qty,0,1) FROM target_detail td
          WHERE td.target_id = t.id
          group by td.target_id
        ),0) as achievement_by_item,
        IFNULL(
          (
            SELECT

            sum((
              select sum(bm.quantity*bm.price) from bill_menu bm join bill b on b.id=bm.bill_id where b.is_refund = 0 and td.menu_id=bm.menu_id AND bm.created_by = u.id
              AND YEAR(bm.created_at)= '2016' AND MONTH(bm.created_at)= '2'
            ))
            FROM
              target_detail td
            WHERE
              td.target_id = t.id
            group by td.target_id
          ),
          0
        ) as achievement_by_item_total
      from users u 
      inner join users_groups g on u.id = g.user_id
      inner join feature_access fa on g.group_id=fa.groups_id
      inner join feature f on fa.feature_id=f.id
      inner join target t on t.user_id=u.id
      where f.key in ('dinein')
      group by u.id,t.id
    ")->result();
  }
  public function achievement_waiter_detail_by_total($params=array())
  {
    return $this->db->query("
      SELECT m.menu_name,sum(bm.quantity) as quantity,bm.price
      from target t 
      inner join users u on t.user_id=u.id
      inner join bill_menu bm on bm.created_by=u.id
      inner join bill b on b.id=bm.bill_id
      inner join menu m on bm.menu_id=m.id
      where b.is_refund = 0 and t.id='".$params['target_id']."' and YEAR(bm.created_at)= '".$params['year']."' and MONTH(bm.created_at)= '".$params['month']."'
      group by bm.menu_id,bm.price
    ")->result();
  }
  public function achievement_waiter_detail_by_item($params=array())
  {
    return $this->db->query("
      SELECT m.menu_name,td.target_qty,sum(bm.quantity) as quantity,bm.price
      from target t 
      inner join users u on t.user_id=u.id
      inner join target_detail td on td.target_id=t.id
      inner join bill_menu bm on td.menu_id=bm.menu_id
      inner join bill b on b.id=bm.bill_id
      inner join menu m on bm.menu_id=m.id
      where b.is_refund = 0 and t.id='".$params['target_id']."' and YEAR(bm.created_at)= '".$params['year']."' and MONTH(bm.created_at)= '".$params['month']."'
      group by bm.menu_id,bm.price
    ")->result();
  }
  public function kitchen_duration($params=array())
  {
    return $this->db->query("
      select m.menu_name,m.duration,bm.quantity,bm.created_at,bm.finished_at
      from bill_menu bm
      join bill b on b.id=bm.bill_id
      inner join menu m on bm.menu_id=m.id
      where b.is_refund = 0 and m.duration>0 and year(bm.created_at)='".$params['year']."' and month(bm.created_at)='".$params['month']."' and bm.finished_at!=''
      group by bm.created_at
      order by bm.created_at asc
    ")->result();
  }
  public function get_reward_kitchen($params=array())
  {
    $result=$this->db->query("
      select SUM(IF((TIMESTAMPDIFF(SECOND,bm.created_at,bm.finished_at)/60)<m.duration,(bm.quantity*".$params['reward_kitchen']->reward."),0)) as total_reward_kitchen
      from bill_menu bm
      join bill b on b.id=bm.bill_id
      inner join menu m on bm.menu_id=m.id
      where b.is_refund = 0 and m.duration>0 and year(bm.created_at)='".$params['year']."' and month(bm.created_at)='".$params['month']."' and bm.finished_at!=''
    ")->row();
    return $result->total_reward_kitchen;
  }
  public function inventory_adjustment($params=array()){
    return $this->db->query("
      select i.name,sum(sh.quantity) as quantity,u.code,o.outlet_name,(
        select sum(quantity) from stock_history where outlet_id=sh.outlet_id and inventory_id=sh.inventory_id and uom_id=sh.uom_id 
      ) as last_stock
      from stock_history sh 
      inner join outlet o on sh.outlet_id=o.id
      inner join inventory i on sh.inventory_id=i.id
      inner join uoms u on sh.uom_id=u.id
      where sh.status=4 and date(sh.created_at)='".$params['date']."' and sh.store_id='".$params['store_id']."' 
      ".($params['outlet_id']!="" ? "and sh.outlet_id='".$params['outlet_id']."'" : "")."
      group by sh.inventory_id
      order by o.outlet_name,i.name asc
    ")->result(); 
  }
  public function summary_year($params=array()){
    return $this->db->query("
      select m.*,b.total_dinein,b.total_delivery,b.total_takeaway,pc.total_petty_cash,b.total_customer
      from hr_enum_months m
      left join (
        select month(created_at) as month, 
        sum(IF(is_delivery=0 and is_take_away=0,total_price,0)) as total_dinein,
        sum(IF(is_delivery=1 and is_take_away=0,total_price,0)) as total_delivery,
        sum(IF(is_delivery=0 and is_take_away=1,total_price,0)) as total_takeaway,
        sum(customer_count) as total_customer
        from bill where is_refund = 0 and year(created_at)='".$params['year']."' 
        group by month(created_at)
      ) as b on b.month=m.id
      left join (
        select month(date) as month,sum(amount) as total_petty_cash 
        from petty_cash 
        where year(date)='".$params['year']."'
        group by month(date)
      ) as pc on pc.month=m.id
    ")->result();
  }
  public function taxes($params=array()){
    return $this->db->query("
      select date(bi.created_at) AS date, SUM(IF(bi.info like '%".$params['tax_name']."%',bi.amount,0)) as total_taxes
      from bill_information bi 
      join bill b on b.id=bi.bill_id and b.is_refund = 0
      where type = 1 AND bi.info not like '%pembulatan%' and bi.info not like '%ongkos kirim%' and date(bi.created_at) BETWEEN '".$params['start_date']."' AND '".$params['end_date']."' 
      group by date(bi.created_at)
    ")->result();
  }
	public function spoiled($params=array()){
		return $this->db->query("
			SELECT
				i.name,u.code,o.outlet_name,(sh.quantity*-1) as quantity,sh.created_at,sh.description, 
        (sh.quantity * sh.price) as cost
			FROM stock_history sh
			JOIN `uoms` u ON `sh`.`uom_id` = `u`.`id`
			JOIN `outlet` o ON `sh`.`outlet_id` = `o`.`id`
			JOIN `inventory` i ON `sh`.`inventory_id` = `i`.`id`
			WHERE `sh`.`status` = 7 and date(sh.created_at) <= '".$params['end_date']."' and date(sh.created_at) >= '".$params['start_date']."' ".($params['outlet_id']!="" ? " and sh.outlet_id='".$params['outlet_id']."' " : "").($params['inventory_id']!="" ? " and sh.inventory_id='".$params['inventory_id']."' " : "")."
			GROUP BY `sh`.`outlet_id`,`sh`.`inventory_id`,`sh`.`uom_id`,sh.created_at
			ORDER BY `outlet_name`,i.name ASC
		")->result();
	}
  public function summary_inventory($params=array()){
    return $this->db->query("
      select sh.id,sh.outlet_id,o.outlet_name,sh.inventory_id,i.name,sh.uom_id,u.code,sh.`status`,COALESCE (a.beginning_stock, 0) as beginning_stock,
	  sum(IF(sh.`status`=1,sh.quantity,0)) as total_1,
	  sum(IF(sh.`status`=2,sh.quantity,0)) as total_2,
	  sum(IF(sh.`status`=3,sh.quantity,0)) as total_3,
	  sum(IF(sh.`status`=4,sh.quantity,0)) as total_4,
	  sum(IF(sh.`status`=5,sh.quantity,0)) as total_5,
	  sum(IF(sh.`status`=6,sh.quantity,0)) as total_6,
	  sum(IF(sh.`status`=7,sh.quantity,0)) as total_7,
    sum(IF(sh.`status`=8,sh.quantity,0)) as total_8,
    sum(IF(sh.`status`=9,sh.quantity,0)) as total_9,
    sum(IF(sh.`status`=10,sh.quantity,0)) as total_10,
	  sum(sh.quantity)+COALESCE (a.beginning_stock, 0) as last_stock
      from stock_history sh 
      inner join uoms u on sh.uom_id=u.id
      inner join inventory i on sh.inventory_id=i.id
      inner join outlet o on sh.outlet_id=o.id
      left join (
        select outlet_id,inventory_id,uom_id,sum(quantity) as beginning_stock from stock_history
        where date(created_at)<'".$params['start_date']."'
        group by outlet_id,inventory_id,uom_id
      ) as a on sh.outlet_id=a.outlet_id and sh.inventory_id=a.inventory_id and sh.uom_id=a.uom_id
      where date(sh.created_at)>='".$params['start_date']."' and date(sh.created_at)<='".$params['end_date']."'
      ".($params['outlet_id']!="" ? " and sh.outlet_id='".$params['outlet_id']."'" : "").($params['inventory_id']!="" ? " and sh.inventory_id='".$params['inventory_id']."'" : "").($params['store_id']!="" ? " and o.store_id='".$params['store_id']."'" : "")."
      group by sh.outlet_id,sh.inventory_id,sh.uom_id
      order by outlet_name,name,id asc
    ")->result();
  }	

   public function opname_daily($params=array()){
    $where = "";

    if($params){
      $where .= "where ";
    }

    $where .= implode(" and ", $params);
    return $this->db->query("
       SELECT 
          `i`.`id` as inventory_id, 
          `s`.`outlet_id`,
          `o`.`outlet_name`, 
          `i`.`name`,
          `i`.`unit`,
            u.`code`,
          `s`.`store_id`,
          `s`.`uom_id`,
           s.`status`,
            sum(IF(s.`status`=1 and (date(s.created_at) BETWEEN curdate() and curdate()),s.quantity,0)) as total_1,
            sum(IF(s.`status`=2 and (date(s.created_at) BETWEEN curdate() and curdate()),s.quantity,0)) as total_2,
            sum(IF(s.`status`=3 and (date(s.created_at) BETWEEN curdate() and curdate()),s.quantity,0)) as total_3,
            sum(IF(s.`status`=4 and (date(s.created_at) BETWEEN curdate() and curdate()),s.quantity,0)) as total_4,
            sum(IF(s.`status`=5 and (date(s.created_at) BETWEEN curdate() and curdate()),s.quantity,0)) as total_5,
            sum(IF(s.`status`=6 and (date(s.created_at) BETWEEN curdate() and curdate()),s.quantity,0)) as total_6,
            sum(IF(s.`status`=7 and (date(s.created_at) BETWEEN curdate() and curdate()),s.quantity,0)) as total_7,
            sum(IF(s.`status`=8 and (date(s.created_at) BETWEEN curdate() and curdate()),s.quantity,0)) as total_8,
            sum(IF(s.`status`=9 and (date(s.created_at) BETWEEN curdate() and curdate()),s.quantity,0)) as total_9,
            sum(IF(s.`status`=10 and (date(s.created_at) BETWEEN curdate() and curdate()),s.quantity,0)) as total_10,
            sum(s.quantity) as last_stock,
            sum(IF( (date(s.created_at) < curdate()  ),s.quantity,0)) as beginning_stock
         
        FROM
          (`inventory` i)
        JOIN `stock_history` s ON `s`.`inventory_id` = `i`.`id`
        JOIN `uoms` u ON `s`.`uom_id` = `u`.`id`
        LEFT JOIN `store` st ON `st`.`id` = `s`.`store_id`
        JOIN `outlet` o ON `o`.`id` = `s`.`outlet_id`
        ".$where."
        GROUP BY
          `s`.`outlet_id`,
          `s`.`inventory_id`,
          `s`.`uom_id`
        ORDER BY
          `o`.`outlet_name`,
          `i`.`name` ASC
    ")->result();
  } 
  public function detail_inventory($params=array()){
    return $this->db->query("
      (select sh.id,sh.outlet_id,o.outlet_name,sh.inventory_id,i.name,sh.uom_id,u.code,date(sh.created_at) as date,sh.`status`,eshs.`value`,sum(sh.quantity) as total_quantity,a.beginning_stock,1 as range_status
      from stock_history sh 
      inner join uoms u on sh.uom_id=u.id
      inner join inventory i on sh.inventory_id=i.id
      inner join outlet o on sh.outlet_id=o.id
      inner join enum_stock_history_status eshs on eshs.id=sh.`status`
      left join (
        select outlet_id,inventory_id,uom_id,sum(quantity) as beginning_stock from stock_history
        where date(created_at)<'".$params['start_date']."'
        group by outlet_id,inventory_id,uom_id
      ) as a on sh.outlet_id=a.outlet_id and sh.inventory_id=a.inventory_id and sh.uom_id=a.uom_id
      where date(sh.created_at)>='".$params['start_date']."' and date(sh.created_at)<='".$params['end_date']."'
      ".($params['outlet_id']!="" ? " and sh.outlet_id='".$params['outlet_id']."'" : "").($params['inventory_id']!="" ? " and sh.inventory_id='".$params['inventory_id']."'" : "")." and o.store_id='".$params['store_id']."'
      group by sh.outlet_id,sh.inventory_id,sh.uom_id,date(sh.created_at),eshs.`value`)
      UNION 
      (select sh.id,sh.outlet_id,o.outlet_name,sh.inventory_id,i.name,sh.uom_id,u.code,date(sh.created_at) as date,sh.`status`,eshs.`value`,sum(sh.quantity) as total_quantity,sum(sh.quantity) as beginning_stock,0 as range_status
      from stock_history sh 
      inner join uoms u on sh.uom_id=u.id
      inner join inventory i on sh.inventory_id=i.id
      inner join outlet o on sh.outlet_id=o.id
      inner join enum_stock_history_status eshs on eshs.id=sh.`status`
      where date(sh.created_at)<'".$params['start_date']."'
      ".($params['outlet_id']!="" ? " and sh.outlet_id='".$params['outlet_id']."'" : "").($params['inventory_id']!="" ? " and sh.inventory_id='".$params['inventory_id']."'" : "")." and o.store_id='".$params['store_id']."'
      group by sh.outlet_id,sh.inventory_id,sh.uom_id)
      order by outlet_name,name,id asc
    ")->result();
  }
  // public function summary_inventory($params=array()){
     // return $this->db->query("
      // select sh.outlet_id,o.outlet_name,sh.inventory_id,i.name,sh.uom_id,u.code,date(b.date) as date,sh.`status`,eshs.`value`,b.total_quantity,a.beginning_stock
      // from inventory i 
      // inner join stock_history sh on sh.inventory_id=i.id
      // inner join uoms u on sh.uom_id=u.id
      // inner join outlet o on sh.outlet_id=o.id
      // inner join enum_stock_history_status eshs on eshs.id=sh.`status`
      // left join (
        // select outlet_id,inventory_id,uom_id,sum(quantity) as beginning_stock from stock_history
        // where date(created_at)<'".$params['start_date']."'
        // group by outlet_id,inventory_id,uom_id
      // ) as a on sh.outlet_id=a.outlet_id and sh.inventory_id=a.inventory_id and sh.uom_id=a.uom_id
      // left join (
        // select outlet_id,inventory_id,uom_id,date(created_at) as date,sum(quantity) as total_quantity from stock_history
        // where date(created_at)>='".$params['start_date']."' and date(created_at)<='".$params['end_date']."'
        // group by outlet_id,inventory_id,uom_id
      // ) as b on sh.outlet_id=a.outlet_id and sh.inventory_id=a.inventory_id and sh.uom_id=a.uom_id
      // where 1=1 
      // ".($params['outlet_id']!="" ? " and sh.outlet_id='".$params['outlet_id']."'" : "").($params['inventory_id']!="" ? " and sh.inventory_id='".$params['inventory_id']."'" : "")."
      // group by sh.outlet_id,sh.inventory_id,sh.uom_id,b.date,eshs.`value`
      // order by o.outlet_name,i.name,sh.id asc
    // ")->result();
  // }
 public function employee_schedule($params=array())
  {
    $parameter="";
    if($params['user_id']!=""){
      $parameter.=" u.id='".$params['user_id']."'";
    }

    return $this->db->query("
      SELECT
        u. NAME AS uname,
        hs.start_date as start_date,
        hs.end_date as end_date,
        hsd.start_time,
        hsd.end_time
      FROM
        users u
      JOIN hr_schedules hs ON hs.user_id = u.id
      JOIN hr_schedule_detail hsd ON hsd.schedule_id = hs.id
      where date(hs.start_date)>='".$params['start_date']."' ".$parameter." 
      ORDER BY
          u.name ASC
    ")->result();

  }
  public function summary_receive_order($params=array()){
    $parameter="";
    if($params['supplier_id']!=""){
      $parameter.=" and po.supplier_id='".$params['supplier_id']."'";
    }
    if($params['payment_method']!=""){
      $parameter.=" and por.payment_method='".$params['payment_method']."'";
    }
    return $this->db->query("
      select 
        por.*,s.name,po.number
      from purchase_order_receive por 
      inner join purchase_order po on por.purchase_order_id=po.id
      inner join supplier s on po.supplier_id=s.id
      where date(por.incoming_date)>='".$params['start_date']."' and date(por.incoming_date)<='".$params['end_date']."' and po.store_id='".$params['store_id']."'
    ".$parameter."
      order by por.incoming_date asc
    ")->result();
  }

  public function summary_retur_order($params=array()){
    $parameter="";
    if($params['supplier_id']!=""){
      $parameter.=" and po.supplier_id='".$params['supplier_id']."'";
    }
    return $this->db->query("
      select 
        por.*,s.name,po.number as po_number
      from purchase_order_retur por 
      inner join purchase_order po on por.purchase_order_id=po.id
      inner join supplier s on po.supplier_id=s.id
      where date(por.retur_date)>='".$params['start_date']."' and date(por.retur_date)<='".$params['end_date']."'
    ".$parameter."
      order by por.retur_date asc
    ")->result();
  }
  
	public function compliment($params=array()){
    return $this->db->query("
      select 
        sum(bp.amount) as total_transaction, b.*, bp.created_at as payment_date, u.name, u.phone
      from bill_payment bp 
      join bill b on bp.bill_id = b.id
      join users u on u.id = bp.info
      where b.is_refund = 0 and date(b.created_at) >= '".$params['start_date']."' and date(b.created_at) <= '".$params['end_date']."'
			and bp.payment_option = 5
      group by b.id
      order by b.created_at desc
    ")->result();
  }
	public function bon_bill($params=array()){
    return $this->db->query("
      select 
        sum(bp.amount) as total_transaction,b.*,bp.created_at as payment_date
      from bill_payment bp 
      inner join bill b on bp.bill_id=b.id
      where b.is_refund = 0 and date(b.created_at)>='".$params['start_date']."' and date(b.created_at)<='".$params['end_date']."'
			and bp.payment_option=9
      group by b.id
      order by b.created_at desc
    ")->result();
  }
  public function member_transaction($params=array()){
    $parameter="";
    if($params['member_id']!=""){
      $parameter.=" and b.member_id='".$params['member_id']."'";
    }
    return $this->db->query("
      select 
        sum(bp.amount) as total_transaction,b.*,bp.created_at as payment_date,m.name,mc.name as member_category_name
      from bill_payment bp 
      inner join bill b on bp.bill_id=b.id
      inner join member m on m.id=b.member_id
      inner join member_category mc on mc.id=m.member_category_id
      where b.is_refund = 0 and date(b.created_at)>='".$params['start_date']."' and date(b.created_at)<='".$params['end_date']."'
    ".$parameter."
      group by b.id
      order by b.id
    ")->result();
  }
  public function voucher_used($params=array()){
    return $this->db->query("
      select 
        b.*,vc.name as voucher_group_name,vc.amount,v.code
      from bill_payment bp 
      inner join voucher v on bp.info=v.code
      inner join voucher_group vc on v.voucher_group_id=vc.id
      inner join bill b on bp.bill_id=b.id
      where b.is_refund = 0 and bp.payment_option=4 and date(b.created_at)>='".$params['start_date']."' and date(b.created_at)<='".$params['end_date']."'
      order by b.id
    ")->result();
  }
  public function promo_used($params=array()){
    return $this->db->query("
      select 
        b.*,pd.name,pd.discount,ps.schedule_name
      from bill b
      inner join promo_discount pd on b.promo_id=pd.id
      inner join promo_schedule ps on pd.promo_schedule_id=ps.id
      where b.is_refund = 0 and date(b.created_at)>='".$params['start_date']."' and date(b.created_at)<='".$params['end_date']."'
      group by b.id
      order by b.id
    ")->result();
  }
  public function promo_cc($params=array()){
    // return $this->db->query("
    //   select 
    //     b.*,pc.name,pc.discount,ps.schedule_name
    //   from bill b
    //   inner join bill_information bi on b.id=bi.bill_id
    //   inner join promo_cc pc on bi.enum_ref_id = pc.id
    //   inner join promo_schedule ps on pc.promo_schedule_id=ps.id
    //   where bi.enum_ref_table = 2 and ".$params['promo_cc_id'] ? "pc.id = ".$params['promo_cc_id'] : ""." and date(b.created_at)>='".$params['start_date']."' and date(b.created_at)<='".$params['end_date']."'
    //   group by b.id
    //   order by b.id
    // ")->result();
    
    $this->db->select('b.*,pc.name,pc.discount,ps.schedule_name')
    ->from('bill b')
    ->join('bill_information bi','b.id=bi.bill_id','inner')
    ->join('promo_cc pc','bi.enum_ref_id = pc.id','inner')
    ->join('promo_schedule ps','pc.promo_schedule_id=ps.id','inner')
    ->where('bi.enum_ref_table', 2)
    ->where('b.is_refund', 0);
    if($params['start_date']){
        $this->db->where('b.created_at >= ', $params['start_date']);
    }
    if($params['end_date']){
        $this->db->where('b.created_at <= ', $params['end_date']);
    }
    if($params['promo_cc_id'] != 0){
        $this->db->where('pc.id ', $params['promo_cc_id']);
    }
    $this->db->group_by("b.id");
    return $this->db->get()->result();
  }
	//REPORT UNTUK ABSENSI PER PERIODE BERDASARKAN NAMA PEGAWAI DAN STATUS ABSENSI HADIR,OFF,T1,T2,Ijin Pulang,Ijin Sakit
	public function report_attendance_periode($params=array()){
		return $this->db->query("
			SELECT s.enum_repeat,s.start_date,s.end_date,u.id,u.name,hra.created_at,
				IF(a.total>0,1,0) as present,IF(b.total>0,1,0) as permission_go_home,
				IF(c.total>0,1,0) as sick,d.off,IF(e.user_id is not null,1,0) as late_1,
				IF(f.user_id is not null,1,0) as late_2,IF(g.total>0,1,0) as permission_alpha
			from users u
			left join hr_attendances hra on u.id=hra.user_id
			left join (
				select hrs.*,hoh.checkin_time,hoh.checkout_time
				from hr_schedules hrs
				inner join hr_schedule_detail hrsd on hrs.id=hrsd.schedule_id
				inner join hr_office_hours hoh on hrsd.office_hour_id=hoh.id
			) s on s.user_id=u.id and IF(s.enum_repeat=1,s.start_date<=hra.created_at,s.start_date<=hra.created_at and hra.created_at<=s.end_date)
			left join (
				select 
					user_id,created_at,count(*) as total
				from hr_attendances
				where enum_status_attendance in (1)
				group by user_id,created_at
			) a on a.user_id=u.id and a.created_at=hra.created_at
			left join (
				select 
					user_id,created_at,count(*) as total
				from hr_attendances
				where enum_status_attendance in (7)
				group by user_id,created_at
			) b on b.user_id=u.id and b.created_at=hra.created_at
			left join (
				select 
					user_id,created_at,count(*) as total
				from hr_attendances
				where enum_status_attendance in (4)
				group by user_id,created_at
			) c on c.user_id=u.id and c.created_at=hra.created_at
			left join (
				select 
					user_id,group_concat(day) as off
				from hr_employee_holidays
				group by user_id
			) d on d.user_id=u.id
			left join (
				select 
					user_id,created_at,checkin_time,checkout_time
				from hr_attendances
				group by user_id,created_at
			) e on e.user_id=u.id and e.created_at=hra.created_at 
			and e.checkin_time>DATE_ADD(s.checkin_time,INTERVAL (select value from hr_setting where name='max_late') minute)
			and e.checkin_time<=DATE_ADD(s.checkin_time,INTERVAL (select value from hr_setting where name='max_late')+10 minute)
			left join (
				select 
					user_id,created_at,checkin_time,checkout_time
				from hr_attendances
				group by user_id,created_at
			) f on f.user_id=u.id and f.created_at=hra.created_at 
			and f.checkin_time>DATE_ADD(s.checkin_time,INTERVAL (select value from hr_setting where name='max_late')+10 minute)
			left join (
				select 
					user_id,created_at,count(*) as total
				from hr_attendances
				where enum_status_attendance in (3)
				group by user_id,created_at
			) g on g.user_id=u.id and g.created_at=hra.created_at
			where hra.created_at>='".$params['start_date']."' and hra.created_at<='".$params['end_date']."'
			group by u.id,hra.created_at
			order by created_at,u.name asc
		")->result();
	}

  //REPORT UNTUK ABSENSI JUMLAH KETERLAMBATAN DALAM MENIT PER HARI
  public function report_attendance_overdue($params=array()){

        return $this->db->query('SELECT
              b.schedule_id as id,
              a.user_id,
              us.name,
              c.checkin_time,
              floor(
                (
                  TIME_TO_SEC(ha.checkin_time)- TIME_TO_SEC(c.checkin_time)
                )/ 60
              )AS overdue,
              ha.checkin_time AS actual_checkin,
              ha.enum_status_attendance,
            date_format(ha.created_at, "%Y-%m-%d") created_at
            FROM
              hr_schedules a
              JOIN users us ON(us.id = a.user_id)
            JOIN hr_schedule_detail b ON(a.id = b.schedule_id)
            JOIN hr_office_hours c ON(b.office_hour_id = c.id)
            LEFT JOIN hr_attendances ha ON(
              ha.user_id = a.user_id
              AND date_format(ha.created_at, "%Y-%m-%d") BETWEEN "'.$params['start_date'].'" AND "'.$params['end_date'].'"
            ) 
            where  date_format(ha.created_at, "%Y-%m-%d") IS NOT NULL  
            GROUP BY a.user_id,date_format(ha.created_at, "%Y-%m-%d")
            ORDER BY  date_format(ha.created_at, "%Y-%m-%d"),a.user_id 

    ')->result();
  }
    public function reprint_billings($params=array()){
        $this->db->select('
          bill.receipt_number,bill.payment_date,bill.total_price,bill.customer_count,bill.is_take_away as order_type,bill.order_id,
          table.table_name,users.name as cashier,bill.customer_name
        ')
        ->from('bill')
        ->join('users','users.id=bill.cashier_id','left')
        ->join('table','table.id=bill.table_id','left')
        ->where('bill.is_refund', 0);
        if($params['start_date']){
            $this->db->where('payment_date >= ', $params['start_date']);
        }
        if($params['end_date']){
            $this->db->where('payment_date <= ', $params['end_date']);
        }
        $this->db->group_by("bill.id");
        return $this->db->get()->result();
    }

    /*
    *   Created by : M. Tri
    *   Created at : 30/08/2016
    *   Report function to reporting pending bill information, both company and employee
    */

    public function pending_bill($params=array()){
      $columns = "";
      $join = "";
      $on = "";

      if ($params['type'] == 6) {
        $columns = ", oc.company_name AS customer_name, oc.land_phone AS customer_phone";
        $join = "order_company oc";
        $on = "oc.id = bp.info";
      } else if ($params['type'] == 7) {
        $columns = ", m.name AS customer_name, m.land_phone AS customer_phone";
        $join = "member m";
        $on = "m.id = bp.info";
      }
      
      $this->db->select('SUM(bp.amount) AS total_transaction,
                         b.*,
                         bp.created_at AS payment_date'.$columns)
               ->from('bill_payment bp')
               ->join('bill b', 'bp.bill_id = b.id')
               ->join($join, $on)
               ->where('b.is_refund', 0)
               ->where('DATE(b.created_at) >=', $params['start_date'])
               ->where('DATE(b.created_at) <=', $params['end_date'])
               ->where('bp.payment_option', $params['type'])
               ->group_by('b.id')
               ->order_by('b.created_at', 'DESC');

      return $this->db->get()->result();
    }

    public function get_aging_report($params = array()){
      $filter_date = $params['filter_date'];
      $supplier_id = $params['supplier_id'];
      $this->db->select("i.name,
                                 (pord.received_quantity * pord.price) AS total_payment,
                                 IF((DATEDIFF('".$filter_date."', po.order_at) >= 0 AND DATEDIFF('".$filter_date."', po.order_at) <= 30), (pord.received_quantity * pord.price), 0) AS due_1,
                                 IF((DATEDIFF('".$filter_date."', po.order_at) > 30 AND DATEDIFF('".$filter_date."', po.order_at) <= 60), (pord.received_quantity * pord.price), 0) AS due_2,
                                 IF((DATEDIFF('".$filter_date."', po.order_at) > 60 AND DATEDIFF('".$filter_date."', po.order_at) <= 90), (pord.received_quantity * pord.price), 0) AS due_3,
                                 IF((DATEDIFF('".$filter_date."', po.order_at) > 90), (pord.received_quantity * pord.price), 0) AS due_4,
                                ", false)
      ->from('purchase_order_receive_detail pord')
      ->join('purchase_order_receive por', 'por.id = pord.purchase_order_receive_id')
      ->join('purchase_order_detail pod', 'pod.id = pord.purchase_order_detail_id')
      ->join('purchase_order po', 'po.id = pod.purchase_order_id')
      ->join('inventory i', 'i.id = pod.inventory_id')
      ->where('por.payment_method', 2)
      ->where('por.payment_status', 0);
      if($supplier_id){
        $this->db->where('po.supplier_id', $supplier_id);  
      }
      
       return $this->db->get()->result();
    }


    public function get_report_product($where){


      $this->db->select("menu.menu_name, sum(bill_menu.quantity) as jumlah",false);
      $this->db->from('bill_menu');
      $this->db->join('menu', 'bill_menu.menu_id = menu.id');
      $this->db->where($where);
      $this->db->group_by("menu.id");
      $query1 =  $this->db->get()->result();

      $open_at = $where['bill_menu.created_at >='];
      $close_at = $where['bill_menu.created_at <='];
      $this->db->select("menu.menu_name, '0' as jumlah",false);
      $this->db->from('menu'); 
      $this->db->where("id not in (SELECT menu.id
          FROM (`bill_menu`) RIGHT JOIN `menu` ON (`bill_menu`.`menu_id` = `menu`.`id` )
          WHERE `bill_menu`.`created_at` >= '".$open_at."' 
          AND `bill_menu`.`created_at` <= '".$close_at."' GROUP BY menu.id
           )"
      ); 
      $query2 =  $this->db->get()->result();

      return array_merge($query1, $query2);
    }


    public function get_report_pettycash($where){ 
      $this->db->select("description,amount",false);
      $this->db->from('petty_cash'); 
      $this->db->where($where); 
      $query1 =  $this->db->get()->result(); 
      return $query1;
    }

    public function member_discount_detail($params=array()){
      $parameter="";
      if(isset($params['member_id']) && $params['member_id']!=""){
        $parameter.=" and b.member_id='".$params['member_id']."'";
      }
      return $this->db->query("
        select 
          sum(bp.amount) as total_transaction,b.*,bp.created_at as payment_date,m.name,mc.name as member_category_name,bi.info,u.name as approver_name
        from bill_payment bp 
        inner join bill b on bp.bill_id=b.id
        inner join member m on m.id=b.member_id
        inner join member_category mc on mc.id=m.member_category_id
        inner join bill_information bi on bi.bill_id=b.id and bi.info like 'Member%' 
        left join users u on bi.enum_ref_id = u.id
        where b.is_refund = 0 and date(b.created_at)>='".$params['start_date']."' and date(b.created_at)<='".$params['end_date']."'
      ".$parameter."
        group by b.id
        order by b.id
      ")->result();
    }

    public function get_count_kontra_bon($store_id) {
      $where = "";
      if ($store_id != 0) {
        $where = "AND store_id = ".$store_id;
      }
      return $this->db->query("
        SELECT COUNT(*) AS qty_kontra_bon
        FROM purchase_order_receive
        WHERE payment_method = 2
          AND payment_status = 0
          ".$where."
      ")->row();
    }

    public function delivery_service($params = array())
    {
      return $this->db->query("
        SELECT
          delivery_courier.id,
          company_name,
          courier_code,
          courier_name,
          SUM(amount) AS amount
        FROM
          bill_information bi
        JOIN delivery_courier ON delivery_courier.id = bi.enum_ref_id
        JOIN delivery_company ON delivery_company.id = delivery_courier.delivery_company_id
        WHERE
          enum_ref_id <> 0
        AND type = 2
        AND info LIKE '%ongkos kirim%'
        AND delivery_company.is_active = 1
        AND delivery_courier.is_active = 1
        AND DATE(bi.created_at) BETWEEN '".$params['start_date']."'
        AND '".$params['end_date']."'
        GROUP BY
          delivery_courier.id
      ")->result();
    }

}