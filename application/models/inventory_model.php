<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory_model extends MY_Model
{
  function __construct(){
    parent::__construct();
  }
  public function get_hpp_from_bill($bill_id=0){
    return $this->db->query("
      select sum((mi.quantity*i.price)*bm.quantity) as hpp
      from bill_menu bm
      inner join menu_ingredient mi on bm.menu_id=mi.menu_id
      inner join inventory i on mi.inventory_id=i.id
      where bm.bill_id='".$bill_id."'
      ")->row();
  }
  public function get_inventory()
  {
    $this->db->select('*');
    $this->db->from('inventory');
    $this->db->order_by('name', 'ASC');
    $query = $this->db->get();
    $data  = $query->result();

    $results    = array();
    $results[0] = 'Pilih Bahan';
    foreach ($data as $row) {
      $results[''.$row->id.'"uom_id="'.$row->uom_id.''] = $row->name;
    } 

    return $results;
  }
  public function get_uoms()
  {
    $this->db->select('*');
    $this->db->from('uoms');
    $this->db->order_by('name', 'ASC');
    $query = $this->db->get();
    $data  = $query->result();

    $results    = array();
    $results[0] = 'Pilih Satuan';
    foreach ($data as $row) {
      $results[$row->id] = str_replace("'","&#39;",$row->code);
    }

    return $results;
  }
  public function get_inventory_uoms($inventory_id="")
  {
    $this->db->select('iu.*,u.code,u.name');
    $this->db->from('inventory_uoms iu')->join("uoms u","iu.uom_id=u.id")->where("iu.inventory_id",$inventory_id);
    $this->db->order_by('name', 'ASC');
    $query = $this->db->get();
    $data  = $query->result();
    return $data;
  }
  public function get_history_receive_data($post_array){
    $inventory_id = 0;
    $supplier_id = 0;

    if(isset($post_array['inventory_id'])) $inventory_id = $post_array['inventory_id'];


    if(isset($post_array['supplier_id'])) $supplier_id = $post_array['supplier_id'];
    $start_date = $post_array['start_date'];
    $end_date = $post_array['end_date'];


    $where = "";
    if($start_date && $end_date){
      $where = 'where  a.payment_date >= "'.$start_date.'" 
      AND  a.payment_date <= "'.$end_date.'" ';
      if($inventory_id){
        $where .= ' AND pod.inventory_id = '.$inventory_id;
      }
    }

    if($inventory_id   && !$start_date){
      $where = 'where pod.inventory_id = '.$inventory_id;
    } 

    $this->db->select("por.id,
      a.id,
      i.name as inventory_name,
      a.discount,
      a.incoming_date,
      a.payment_date,
      a.payment_no, 
      a.payment_method,
      a.payment_status,
      pod.inventory_id,
      por.price,
      (por.price * por.received_quantity) as total_per_item,
      s.name as supplier_name,
      por.received_quantity")
    ->from('purchase_order_receive a')
    ->join("purchase_order_receive_detail por","a.id = por.purchase_order_receive_id")
    ->join("purchase_order po","  po.id = a.purchase_order_id")
    ->join("supplier s","  s.id = po.supplier_id")
    ->join("purchase_order_detail pod","  pod.id = por.purchase_order_detail_id")
    ->join("inventory i "," i.id = pod.inventory_id");

    if($start_date){
      $this->db->where('date(a.incoming_date) >= ', $start_date);
    }

    if($end_date){
      $this->db->where('date(a.incoming_date) <= ', $end_date);
    } 

    if($inventory_id){
      $this->db->where('pod.inventory_id ', $inventory_id);
    } 
    $results = array();
    $query = $this->db->get();
    $results  = $query->result();
    return $results;
  }
  public function get_inventory_convertions($inventory_id=""){
    $this->db->select("ic.*,i.name as inventory_name,i.uom_id as default_uom_id,u.code as uom_code_to,u2.code as uom_code_from")
    ->from("inventory_convertion ic")
    ->join("inventory i","ic.inventory_id=i.id")
    ->join("uoms u","i.uom_id=u.id")
    ->join("uoms u2","ic.uom_id=u2.id")
    ->where("ic.inventory_id",$inventory_id);
    return $this->db->get()->result();
  }
  public function get_inventory_convertion_drop_down()
  {
    $this->db->select('i.*');
    $this->db->from('inventory i')->join("inventory_uoms iu","iu.inventory_id=i.id")->group_by("i.id");
    $this->db->order_by('i.name', 'ASC');
    $query = $this->db->get();
    $data  = $query->result();

    $results    = array();
    $results[0] = 'Pilih Inventori';
    foreach ($data as $row) {
      $results[$row->id] = $row->name."( ".$row->unit." )";
    }

    return $results;
  }
  public function get_inventory_process_drop_down()
  {
    $query=$this->db->query("
      select i.*
      from inventory_convertion ic
      inner join inventory i on ic.inventory_id=i.id
      ");
    $data  = $query->result();

    $results    = array();
    $results[0] = 'Pilih Inventori';
    foreach ($data as $row) {
      $results[$row->id] = $row->name."( ".$row->unit." )";
    }

    return $results;
  }
  public function get_all_inventories()
  {
    $this->db->select('*');
    $this->db->from('inventory');
    $this->db->where('is_active', 1);
    $this->db->order_by('name', 'ASC');
    $query = $this->db->get();
    $result  = $query->result();

    return $result;
  }

  public function get_inventory_unit($inventory_id)
  {
    $this->db->select('unit');
    $this->db->from('inventory');
    $this->db->where('id', $inventory_id);
    $query = $this->db->get();
    $result = $query->result();

    return $result;
  }

  public function get_all_inventory_unit()
  {
    $this->db->select('id, unit');
    $this->db->from('inventory');
    $query = $this->db->get();
    $result = $query->result();

    return $result;

  }

  public function check_outlet_have_inventory($outlet_id, $inventory_id){
    $this->db->select('*');
    $this->db->from('inventory_stock');
    $this->db->where('outlet_id', $outlet_id);
    $this->db->where('id', $inventory_id);
    $query = $this->db->get();
    return $query->num_rows();
  }

  public function save_stock_history($inventory_stock_id, $user_data){
    $this->db->select('inventory.name as inventory_name, inventory.unit,
      outlet.id as outlet_id, outlet.outlet_name, 
      store.id as store_id, store.store_name ,
      inventory_stock.stock, inventory_stock.inventory_id,
      inventory_stock.id as inventory_stock_id, inventory_stock.created_date');
    $this->db->from('inventory_stock');
    $this->db->join('inventory', 'inventory.inventory_id = inventory_stock.inventory_id');
    $this->db->join('outlet', 'outlet.id = inventory_stock.outlet_id');
    $this->db->join('store', 'store.id = outlet.store_id');
    $this->db->where('inventory_stock.id', $inventory_stock_id);

    $result = $this->db->get()->row();

    if(!empty($result)){
      $data_history = array(
        'user_id' => $user_data->id,
        'username' => $user_data->username,
        'store_id'   => $result->store_id,
        'store_name'   => $result->store_name,
        'outlet_id'   => $result->outlet_id,
        'outlet_name' =>$result->outlet_name,            
        'total' =>  $result->stock,
        'inventory_id' => $result->inventory_id,
        'inventory_name' => $result->inventory_name,
        'unit' =>  $result->unit,                  
        'date' =>  $result->created_date

        );

      $new_history = array(
        'description'=> json_encode($data_history),
        'date'  => $result->created_date
        );
      $this->inventory_model->save('inventory_history', $new_history);

    }


  }

  public function get_history_by_date_range($start_date, $end_date, $outlet_id =FALSE){
    $query = $this->db->select('*, sum(i.stock) as total')
    ->from('inventory_history i')
    ->join('outlet', 'outlet.id = i.outlet_id')
    ->join('inventory', 'inventory.id = i.inventory_id');
    if ($start_date) $query->where('date >', $start_date);
    if ($end_date) $query->where('date <', $end_date);

    if ($outlet_id) {
      $query->where('outlet_id',$outlet_id);
    }
    $query->group_by('i.inventory_id');
    $query->group_by('i.outlet_id');
    $query->group_by('date');

    return $this->db->get()->result();
  }


  public function get_inventory_stock_transaction($inventory_stock_id){
    $query = $this->db->select('sum(total) as total_used, order_id')
    ->from('inventory_stock_transaction')
    ->where('inventory_stock_id', $inventory_stock_id)
    ->group_by('inventory_stock_id');
    return $query->get()->row();
  }

  public function get_stock_opname_by_date_range($start_date, $end_date, $where_outlet = FALSE){
    $query = $this->db->select("
      s.id,                       
      s.inventory_id, s.outlet_id, ot.outlet_name, s.outlet_id, 
      s.inventory_history_date, s.stock,
      s.date,
      u.name as inventory_name, u.unit")
    ->select("
      DATE_FORMAT(s.date, '%Y-%m-%d') as order_date", FALSE)
    ->from('inventory_stock s')
    ->join('inventory u', 'u.id = s.inventory_id')
    ->join('outlet ot', 'ot.id = s.outlet_id');

    if ($start_date) $query->where('date >', $start_date);
    if ($end_date) $query->where('date <', $end_date);

    if ($where_outlet) {
      $query->where('outlet_id', $where_outlet);
    }
    $query->order_by('s.date', 'desc');        
    return $this->db->get()->result();

  }

  public function get_stock_transaction_by_date_range($start_date, $end_date, $where_outlet = FALSE,
    $where_inv = FALSE){
// sum(t.total) as total_used, t.order_id, t.inventory_stock_id,
    $query = $this->db->select("
      s.id,

      s.inventory_id, s.outlet_id, ot.outlet_name, s.outlet_id, 
      s.inventory_history_date, s.stock,
      s.date,
      u.name as inventory_name, u.unit")
    ->select("
      DATE_FORMAT(s.date, '%Y-%m-%d') as order_date", FALSE)
    ->from('inventory_stock s')
    ->join('inventory u', 'u.id = s.inventory_id')
    ->join('outlet ot', 'ot.id = s.outlet_id');

    if ($start_date) $query->where('s.date >=', $start_date);
    if ($end_date) $query->where('s.date <=', $end_date);

    if ($where_outlet) {
      $query->where('outlet_id', $where_outlet);
    }

    if ($where_inv) {
      $query->where('s.inventory_id', $where_inv);
    }

    $query->order_by('s.date', 'desc');
    return $this->db->get()->result();
  }

  function get_inventory_history_grouped(){
    $query = $this->db->query('
      select i.id, i.outlet_id, i.inventory_id, i.date,
      stock from inventory_history i
      group by i.outlet_id, i.inventory_id
      ');

    return $query->result();
  }


  function get_inventory_history_stock($outlet_id,$inventory_id,$date){
    $query = $this->db->query('
      select outlet_id,inventory_id, date, sum(stock) as stock
      from inventory_history
      where date ="'.$date.'"
      and outlet_id ='.$outlet_id.'
      and inventory_id ='.$inventory_id.'
      group by date');
    return $query->row();
  }

  function get_yesterday_remain_stock($outlet_id,$inventory_id,$date, $order_date){
    $result = 0;

    $inv_history = $this->get_inventory_history_stock($outlet_id,$inventory_id,$date);
    if(!empty($inv_history)){

      $result = $inv_history->stock;
      $query = $this->db->query("
        select o.id, sum(i.total) as total from orders o
        join inventory_stock_transaction i
        on i.order_id = o.id
        join inventory_stock s on s.id = i.inventory_stock_id
        where STR_TO_DATE(o.order_date,'%Y-%m-%d') = '".$order_date."'
        and s.inventory_id = ".$inventory_id."
        and s.outlet_id = ".$outlet_id."
        group by i.id
        "
        );

      $order = $query->row();
      if(!empty($order)){
        $result   -= $order->total;
      }
    }

    return $result;
  }

  function update_inventory_stock()
  {

// $date = date('Y-m-d', strtotime("+1 days"));
    $date = date('Y-m-d');

    $all_inventory = $this->get_inventory_history_grouped();

    foreach ($all_inventory as $key => $inv) 
    {
      $query = $this->db->query("
        select 
        i.id, i.outlet_id, i.inventory_id, i.date,
        i.stock from inventory_history i
        where outlet_id =  ".$inv->outlet_id."
        and inventory_id =  ".$inv->inventory_id."         
        order by date desc
        limit 1 
        ");
      $single_inv_hist = $query->row();
      if(!empty($single_inv_hist))
      {
        $where = array(
          'outlet_id' => $single_inv_hist->outlet_id,
          'inventory_id' => $single_inv_hist->inventory_id,
          'date'  =>$date
          );
        $now_inventory_stock= $this->get_all_where('inventory_stock', $where);
        if(empty($now_inventory_stock)){

          $query = $this->db->query('select 
            i.id, i.outlet_id, i.inventory_id, i.date, i.inventory_history_date,
            i.stock from inventory_stock i
            where outlet_id = '.$single_inv_hist->outlet_id.'
            and inventory_id = '.$single_inv_hist->inventory_id.'
            order by date desc
            limit 1 
            ');

          $last_inventory_stock = $query->result();

          if(!empty($last_inventory_stock)){
            foreach ($last_inventory_stock as $key => $single_last_inv_stock) 
            {

              if($single_last_inv_stock->inventory_history_date != $single_inv_hist->date )
              {
                $new_stock = $single_inv_hist->stock + $single_last_inv_stock->stock;
                $inventory_history_date = $single_inv_hist->date;
              }else{
                if($single_last_inv_stock->stock <= 0){
                  $new_stock = $this->get_yesterday_remain_stock($single_inv_hist->outlet_id,
                    $single_inv_hist->inventory_id,
                    $single_inv_hist->date, $single_last_inv_stock->date);
                  $inventory_history_date = $single_last_inv_stock->inventory_history_date;
                }else{
                  $new_stock = $single_last_inv_stock->stock;
                  $inventory_history_date = $single_last_inv_stock->inventory_history_date;
                }
              }


            }    
          }else{
            $new_stock = 0;
            $inventory_history_date =  $single_inv_hist->date;
          }

          $data_insert = array(
            'outlet_id'=> $single_inv_hist->outlet_id,
            'inventory_id' => $single_inv_hist->inventory_id,
            'stock' => $new_stock,
            'date' => $date,
            'inventory_history_date' =>$inventory_history_date
            );

          $this->save('inventory_stock', $data_insert);     
        }


      }
    }



  }




  function get_table_bydate($table, $date){
    $query = $this->db->select('*')
    ->from($table);
    $query->where('date', $date);
    $result = $this->db->get()->result();
    return $result;
  }

  function get_inventory_opname_stock($outlet_id,$inventory_id,$date){
    $query = $this->db->query('
      select outlet_id,inventory_id, date, sum(stock) as stock
      from inventory_opname
      where date ="'.$date.'"
      and outlet_id ='.$outlet_id.'
      and inventory_id ='.$inventory_id.'
      group by date');
    return $query->row();
  }

  function get_stock_opname_transaction_by_date_range($start_date, $end_date, $where_outlet = FALSE){
    $query = $this->db->select("
      sum(t.stock) as total_used,
      t.inventory_id, t.outlet_id, ot.outlet_name, t.outlet_id, 
      t.date, t.stock, 
      u.name as inventory_name, u.unit")

    ->from('inventory_opname t')
// ->join('inventory_stock s', 't.inventory_stock_id = s.id')
    ->join('inventory u', 'u.id = t.inventory_id')
    ->join('outlet ot', 'ot.id = t.outlet_id');
// ->join('order o','o.id = t.order_id');

    if ($start_date) $query->where('t.date >', $start_date);
    if ($end_date) $query->where('t.date <', $end_date);

    if ($where_outlet) {
      $query->where('outlet_id', $where_outlet);
    }
// $query->group_by('t.inventory_id');
// $query->group_by('t.outlet_id');

    return $this->db->get()->result();
  }

  function get_inventory_stock_transaction_byorder($id){
    $query = $this->db->query('
      select sum(i.total) as total, i.id, i.inventory_stock_id
      from inventory_stock_transaction i
      where i.order_id = '.$id.'
      group by i.order_id, i.inventory_stock_id

      ');

    return $query->result();
  }
  public function get_inventory_by_id($inventory_id,$outlet_id,$uom_id=""){
    $query = $this->db->select('sum(s.quantity) as jumlah_stok,st.store_name,o.outlet_name,i.name,i.unit,s.store_id,u.code')
    ->from("stock s")
    ->join("uoms u","s.uom_id=u.id","left")
    ->join('inventory i', 's.inventory_id = i.id')
    ->join('store st', 'st.id = s.store_id')
    ->join('outlet o', 'o.id = s.outlet_id ')
    ->where('s.outlet_id', $outlet_id)
    ->where('s.inventory_id', $inventory_id);
    ;
    if($uom_id!=""){
      $this->db->where("s.uom_id",$uom_id);
    }
    $result = $this->db->get()->result();
    return $result;
  }
  public function inventory_opname($params=array()){
    $query = $this->db->select('sum(s.quantity) as stock_system,i.id,st.store_name,s.outlet_id,o.outlet_name,i.name,i.unit,s.store_id,u.code,s.uom_id')
    ->from("inventory i")
    ->join('stock_history s', 's.inventory_id = i.id')
    ->join("uoms u","s.uom_id=u.id")
    ->join('store st', 'st.id = s.store_id','left')
    ->join('outlet o', 'o.id = s.outlet_id');
    if(isset($params['outlet_id']) && $params['outlet_id']!=""){
      $this->db->where("o.id",$params['outlet_id']);
    }
    if(isset($params['inventory_id']) && $params['inventory_id']!=""){
      $this->db->where("i.id",$params['inventory_id']);
    }
    $this->db->group_by("s.outlet_id,s.inventory_id,s.uom_id");
    $this->db->order_by("o.outlet_name,i.name","ASC");
    $result = $this->db->get()->result();
    return $result;
  }

  public function all_inventory(){
    $query = $this->db->select('sum(s.quantity) as stock_system,i.id,st.store_name,s.outlet_id,o.outlet_name,i.name,i.unit,s.store_id,i.price,i.uom_id')
    ->from("inventory i")
    ->join('stock s', 's.inventory_id = i.id',"LEFT")
    ->join('store st', 'st.id = s.store_id',"LEFT")
    ->join('outlet o', 'o.id = s.outlet_id ',"LEFT")
    ->where("i.is_active",1);
    $this->db->group_by("i.id,s.outlet_id");
    $this->db->order_by("i.name","ASC");
    $result = $this->db->get()->result();
    return $result;
  }
  function get_sum_inventory($table_inv, $outlet_id, $date = FALSE ){
    if(!$date){
      $date = date('Y-m-d');
    }
    $query = $this->db->select("*, sum(stock)");
    $query->from($table_inv);
    $query->where('date', $date);
    $query->where('outlet_id', $outlet_id);
    $query->group_by('date');
    $query->group_by('outlet_id');
    $query->group_by('inventory_id');

    return $query->get()->result();
  }
  function get_inventory_composition($cond=array())
  {
    $this->db->select("ic.*,i.name as inventory_name,u.code")
    ->from("inventory_compositions ic")
    ->join("inventory i","ic.inventory_id=i.id")
    ->join("uoms u","ic.uom_id=u.id")
    ->where($cond);
    return $this->db->get()->result();
  }
  public function get_inventory_convertion($cond=array())
  {
    $this->db->select("ic.*,i.name as inventory_name,u2.code")
    ->from("inventory_convertion ic")
    ->join("inventory i","i.id=ic.inventory_id")
    ->join('uoms u','i.uom_id=u.id')
    ->join('uoms u2','ic.uom_id=u2.id')
    ->where($cond)
    ->where("ic.uom_id !=i.uom_id");
    return $this->db->get()->result();
  }
  public function get_transfer_menu($params=array()){
    $this->db->select('st.id,
      st.created_at,
      (SELECT outlet_name FROM outlet WHERE id = st.origin_outlet_id) AS origin_outlet,
      (SELECT outlet_name FROM outlet WHERE id = st.destination_outlet_id) AS destination_outlet', false)
    ->from('stock_transfer st')
    ->where('DATE(st.created_at) >= ', $params['date'])
    ->where('DATE(st.created_at) <= ', $params['report_end_date']);
    return $this->db->get()->result(); 
  }

  public function get_transfer_menu_by_id($id){
    $this->db->select('st.id,
      st.created_at,
      outlet.outlet_name')
    ->from('stock_transfer st')
    ->join("outlet","st.origin_outlet_id = outlet.id")
    ->where('st.id', $id);
    return $this->db->get()->row(); 
  }

  public function get_transfer_menu_detail($id){
    $this->db->select('inventory.name,
      uoms.code, 
      stock_transfer_history.price,
      stock_transfer_history.quantity
      ', false)
    ->from('stock_transfer_history')
    ->join('inventory','stock_transfer_history.inventory_id =  inventory.id')
    ->join('uoms','stock_transfer_history.uom_id =  uoms.id')
    ->where('stock_transfer_history.stock_transfer_id',$id);
    return $this->db->get()->result(); 
  }

  public function get_transfer_inventory($params=array()){


    $where = "";
    if ($params['inventory_id'] != 0) {
      $where .= ' and sth.inventory_id = ' .$params['inventory_id'];
    }
    if ($params['store_id_start'] != 0) {
      $where .= ' and st.created_by = ' .$params['store_id_start'];
    } 
    if ($params['store_id_end'] != 0) {
      $store = ' and s.id = '. $params['store_id_end'];
    } else {
      $store = ' ';
    }
    if ($params['outlet_id_start'] != 0) {
      $where .= ' and st.origin_outlet_id = ' .$params['outlet_id_start'];
    }
    if ($params['outlet_id_end'] != 0) {
      $where .= ' and st.destination_outlet_id = ' .$params['outlet_id_end'];
    }

    return $this->db->query("
      SELECT
      (SELECT
      store_name
      FROM
      store
      WHERE
      id = st.created_by
    ) AS storestart,
    (
    SELECT
    outlet_name
    FROM
    outlet
    WHERE
    id = st.origin_outlet_id
  ) AS origin,
  (
  SELECT
  store_name
  FROM
  store s
  JOIN outlet o ON o.store_id = s.id
  WHERE
  o.id = st.origin_outlet_id ".$store."
  GROUP BY s.store_name
  ) AS storeend,
  (
  SELECT
  outlet_name
  FROM
  outlet
  WHERE
  id = st.destination_outlet_id 
  ) AS destination,
  i. NAME AS inventoryname,
  sth.quantity AS quantity,
  i.unit AS unit,
  sth.purchase_date AS podate,
  sth.price AS price,
  st.created_at AS date
  FROM
  stock_transfer_history sth
  JOIN stock_transfer st ON sth.stock_transfer_id = st.id
  JOIN inventory i ON i.id = sth.inventory_id

  WHERE DATE(st.created_at) >= '".$params['start_date']."' AND DATE(st.created_at) <= '".$params['end_date']."' ".$where 

  )->result();
  }

  public function get_inventory_stock_data($params=array()){
    return $this->db->query("
      select i.id,i.name,sh.uom_id,u.code,
      sum(sh.quantity)AS last_stock,
      (sum(IF(sh.status=1,sh.quantity*-1,0))) AS used,
      (sum(IF(sh.status=7,sh.quantity*-1,0))) AS spoiled,
      (IFNULL(s2.incoming_stock,0)+IFNULL(p.po_receive,0)) as incoming_stock
      from stock_history sh
      inner join inventory i on sh.inventory_id=i.id
      inner join uoms u on sh.uom_id=u.id
      left join (
      select sqd.inventory_id,sqd.uom_id,sum(sqd.received_quantity) as incoming_stock
      from stock_request sq 
      inner join stock_request_detail sqd on sq.id=sqd.stock_request_id
      where sq.requester_status=2 and (date(sq.finished_at) BETWEEN '".$params['date']."' and '".$params['report_end_date']."') and sq.requester_store_id='".$params['store_id']."'
      group by sqd.inventory_id,sqd.uom_id
      ) s2 on s2.inventory_id=sh.inventory_id and s2.uom_id=sh.uom_id
      left join (
      select pod.inventory_id,pod.uom_id,sum(pord.received_quantity) po_receive
      from purchase_order_receive_detail pord 
      inner join purchase_order_detail pod on pord.purchase_order_detail_id=pod.id
      where date(pord.created_at) BETWEEN '".$params['date']."' and '".$params['report_end_date']."'
      group by pod.inventory_id,pod.uom_id
      ) p on p.inventory_id=sh.inventory_id and p.uom_id=sh.uom_id
      WHERE date(sh.created_at) BETWEEN '".$params['date']."' and '".$params['report_end_date']."' and sh.store_id='".$params['store_id']."'
      group by sh.inventory_id,sh.uom_id
      ")->result();
  }
  public function get_inventory_stock_data_detail($params=array())
  {
    return $this->db->query("
      select
      m.menu_name,u.code,i.unit,sum(om.quantity*mi.quantity) as used
      from order_menu om
      inner join `order` o on om.order_id=o.id
      inner join menu m on om.menu_id=m.id
      inner join menu_ingredient mi on mi.menu_id=m.id
      inner join uoms u on mi.uom_id=u.id
      inner join inventory i on mi.inventory_id=i.id
      where date(o.start_order)>='".$params['from_date']."' and date(o.start_order)<='".$params['to_date']."' and mi.inventory_id='".$params['inventory_id']."' and mi.uom_id='".$params['uom_id']."'
      group by om.menu_id
      ")->result();
  }
  public function convertion($params=array())
  {
    $convert=$this->db->query("select*from inventory_convertion where inventory_id='".$params['inventory_id']."' and uom_id='".$params['uom_id']."' order by created_at desc")->row();
    $convertion=0;
    if(sizeof($convert)>0){
      $convertion=$convert->convertion;
    }
    return $convertion;
  }


  public function cost_opname($params=array())
  {
    $where = '';
    if ($params['inventory_id'] != 0) {
      $where .= 'AND inventory_id = ' .$params['inventory_id'];
    }

    return $this->db->query("
      SELECT inventory.name, stock_history.quantity,stock_history.created_at, stock_history.price FROM inventory
      JOIN stock_history ON stock_history.inventory_id = inventory.id
      WHERE stock_history.status = 4 AND date(stock_history.created_at)>='".$params['start_date']."' AND date(stock_history.created_at)<='".$params['end_date']."' ".$where
      )->result();
  }

  public function get_inventory_account($id = -1) {
    $this->db->select('ia.account_id, a.name, ia.inventory_id')
    ->from('inventory_account ia')
    ->join('account a', 'a.id = ia.account_id')
    ->where('ia.inventory_id', $id)
    ->where('ia.coa_type', 2);
    return $this->db->get()->result();
  }

  public function get_menu_ingredient($menu_id = 0, $order_id = 0)
  {
    $this->db->select('(
      SELECT
      quantity
      FROM
      menu_ingredient
      WHERE
      menu_id = '.$menu_id.'
      AND inventory_id = oc.inventory_id
      ) AS qty_inventory,
      oc.id,
      oc.inventory_purchase_date,
      oc.cogs,
      oc.uom_id,
      oc.quantity,
      oc.inventory_id', TRUE)
    ->from('order_menu_inventory_cogs oc')
    ->join('order_menu om', 'om.id = oc.order_menu_id')
    ->where('om.menu_id', $menu_id)
    ->where('om.order_id', $order_id);
    return $this->db->get()->result();
  }

  public function get_kontra_bon($params = array()) {
    $parameter="";
    if($params['supplier_id']>0){
      $parameter.=" and s.id='".$params['supplier_id']."'";
    }
    return $this->db->query("
      SELECT
      por.*, po.number,
      po.id AS po_id,
      s. NAME AS supname,
      st.store_name AS stname
      FROM
      (
      purchase_order_receive por,
      purchase_order po
    )
    JOIN supplier s ON s.id = po.supplier_id
    JOIN store st ON st.id = por.store_id
    WHERE
    payment_method = 2
    AND po.id = por.purchase_order_id
    AND date(por.created_at) >= '".$params['start_date']."'
    AND date(por.created_at) <= '".$params['end_date']."'".$parameter."
    order by por.payment_status asc, por.payment_date asc
    ")->result();
  }

  public function get_daily_inventory_stock_data($params=array()){
    return $this->db->query("
      select  
      i.name, 
      IFNULL(a.beginning_stock,0) as beginning_stock,
      (IFNULL(s2.incoming_stock,0)+IFNULL(p.po_receive,0)) as incoming_stock,
      (sum(IF(sh.status=1,sh.quantity*-1,0))) AS used_stock,  
      sum(sh.quantity)AS last_stock
      from stock_history sh
      inner join inventory i on sh.inventory_id=i.id
      inner join uoms u on sh.uom_id=u.id
      left join (
      select sqd.inventory_id,sqd.uom_id,sum(sqd.received_quantity) as incoming_stock
      from stock_request sq 
      inner join stock_request_detail sqd on sq.id=sqd.stock_request_id
      where sq.requester_status=2 and (sq.finished_at BETWEEN '".$params['start_date']."' and '".$params['end_date']."') and sq.requester_store_id='".$params['store_id']."'
      group by sqd.inventory_id,sqd.uom_id
      ) s2 on s2.inventory_id=sh.inventory_id and s2.uom_id=sh.uom_id
      left join (
      select outlet_id,inventory_id,uom_id,sum(quantity) as beginning_stock from stock_history
      where created_at< '".$params['start_date']."'
      group by outlet_id,inventory_id,uom_id
      ) as a on sh.outlet_id=a.outlet_id and sh.inventory_id=a.inventory_id and sh.uom_id=a.uom_id 
      left join (
      select pod.inventory_id,pod.uom_id,sum(pord.received_quantity) po_receive
      from purchase_order_receive_detail pord 
      inner join purchase_order_detail pod on pord.purchase_order_detail_id=pod.id
      where pord.created_at BETWEEN '".$params['start_date']."' and '".$params['end_date']."'
      group by pod.inventory_id,pod.uom_id
      ) p on p.inventory_id=sh.inventory_id and p.uom_id=sh.uom_id
      WHERE sh.created_at BETWEEN '".$params['start_date']."' and '".$params['end_date']."' and sh.store_id='".$params['store_id']."'
      group by sh.inventory_id,sh.uom_id
      ")->result();
  }
}