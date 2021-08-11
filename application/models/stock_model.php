<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Stock_model extends MY_Model
{
	function __construct(){
		parent::__construct();
	}


      
    public function get_all_stock()
    {
      $query = $this->db->query('
        select  i.id,sum(s.quantity) as quantity,i.name,s.purchase_date,s.price  from stock s 
        join inventory i on(s.inventory_id = i.id)
        group by s.inventory_id 
      ');
     
      return $query->result();
    }
    public function get_all_stock_by_store_id($store_id)
    {
      $query = $this->db->query('
        select  i.id,sum(s.quantity) as quantity,i.name,s.purchase_date,s.price  from stock s 
        join inventory i on(s.inventory_id = i.id)
        where s.store_id = "'.$store_id.'"
        group by s.inventory_id 
      ');
     
      return $query->result();
    }
    public function get_all_stock_by_outlet_id($outlet_id)
    {
      $query = $this->db->query('
        select  i.id,sum(s.quantity) as quantity,i.name,s.purchase_date,s.price,s.uom_id,u.code  from stock_history s 
        left join uoms u on s.uom_id=u.id
        join inventory i on(s.inventory_id = i.id)
        where s.outlet_id = "'.$outlet_id.'"
        group by s.inventory_id,s.uom_id
      ');
     
      return $query->result();
    }
    public function get_stock_by_inventory_id($id,$uom_id="",$outlet_id="")
    {
      $query = $this->db->query('
        select * from stock where inventory_id = "'.$id.'" '.($uom_id!="" ? " and uom_id='".$uom_id."'" : "").($outlet_id!="" ? " and outlet_id='".$outlet_id."'" : "").'
        order by purchase_date ASC
      ');
     
      return $query->result();
    }
    public function get_stock_detail_by_inventory_id($params=array())
    {
      $query = $this->db->query('
        select s.*,i.name as inventory_name from stock s
        inner join inventory i on s.inventory_id=i.id
        where s.store_id="'.$params['store_id'].'" and s.outlet_id="'.$params['outlet_id'].'" and s.inventory_id = "'.$params['inventory_id'].'" and s.uom_id="'.$params['uom_id'].'"
        order by s.purchase_date ASC, s.id ASC
      ');
     
      return $query->result();
    }
    public function get_last_price($params=array())
    {
      $query = $this->db->query('
        select * from stock where store_id="'.$params['store_id'].'" and outlet_id="'.$params['outlet_id'].'" and inventory_id = "'.$params['inventory_id'].'" and uom_id="'.$params['uom_id'].'"
        order by created_at DESC
        limit 0,1
      ');
     
      return $query->row();
    }
     public function get_stock($where)
    {
      $this->db->select('*')->from("stock")->where($where)->order_by('purchase_date', 'ASC');
      $result = $this->db->get()->result();        
      return $result;
    }
    public function delete_stock_by_id($where){
      //echo $outlet_id;
      foreach ($where as $key => $value) {
        $this->db->where($key, $value);
      }
      $this->db->delete('stock');
      return ($this->db->affected_rows() > 0);
    }
    public function update_stock_by_id($stock_id, $data,$cond=array())
    {
      if(sizeof($cond)>0){
        $this->db->where($cond);
      }
      $this->db->where('id', $stock_id)->update('stock', $data); 
      
      return ($this->db->affected_rows() > 0);
    }
    public function insert_stock_history($data){
       $this->db->insert('stock_history', $data);
       $id = $this->db->insert_id();

       return $id;
    }
    public function insert_stock($data){
       $this->db->insert('stock', $data);
       $id = $this->db->insert_id();
       
       return $id;
    }
    public function get_sum_stock($tgl,$id)
    {
     
      $query = $this->db->query('
        select IFNULL(sum(quantity),0) as jumlah_stok from stock where inventory_id ="'.$id.'" and 
        DATE_FORMAT(purchase_date,"%Y-%m-%d %H:%i") >=
        DATE_FORMAT( "'.$tgl.'","%Y-%m-%d %H:%i") 
      ');
     
      return $query->result();
    }

    public function update_stock_opname($store_id, $outlet_id, $inventory_id, $difference){
      if($difference > 0){
        //insert stock
          $data_stock = array(
          "store_id"=>$store_id,
          "outlet_id"=>$outlet_id,
          "quantity"=>$difference,
          "inventory_id"=>$inventory_id,
          "price"=>0,
          "purchase_date"=>date("Y-m-d h:i:s")
        );
        $this->stock_model->insert_stock($data_stock);
        //insert stock history  
        $data_stock['status'] = 4;
        $this->stock_model->insert_stock_history($data_stock);    
      }else if($difference < 0){
          $difference *= -1;
          $where = array(
              "inventory_id" => $inventory_id,
              "outlet_id" => $outlet_id,
              "store_id" => $store_id
            );
          $stocks = $this->stock_model->get_stock($where);

          foreach ($stocks as $stock) {
            //echo $stock->quantity."<br>";
            $data_stock = array(
              "store_id"=>$store_id,
              "outlet_id"=>$outlet_id,
              "quantity"=>$difference,
              "inventory_id"=>$inventory_id,
              "price"=>$stock->price,
              "purchase_date"=>date("Y-m-d h:i:s")
            );
           
            if($difference > 0){
                if($stock->quantity > $difference){
                  //update origin     
                  $data_update_stock = array( "quantity" => $stock->quantity - $difference);
                  
                  $this->stock_model->update_stock_by_id($stock->id, $data_update_stock);
                  
                  //insert history
                  $data_stock['status'] = 4;
                  $data_stock['quantity'] = $difference *= -1;
                  $data_stock['purchase_date'] = $stock->purchase_date;
                  $this->stock_model->insert_stock_history($data_stock);    

                  $difference = 0;
                }elseif($stock->quantity == $difference){
                  //delete stock
                  $this->stock_model->delete_stock_by_id(array("id" => $stock->id));
                  //insert history

                  $data_stock['status'] = 4;
                  $data_stock['quantity'] = $difference *= -1;
                  $data_stock['purchase_date'] = $stock->purchase_date;
                  $this->stock_model->insert_stock_history($data_stock);    
                  $difference = 0;
                }else{
                  $deleted = $stock->quantity;
                  
                  //delete stock
                  $this->stock_model->delete_stock_by_id(array("id" => $stock->id));
                   //insert history
                  $data_stock['status'] = 4;
                  $quantity_history = $deleted * -1;
                  $data_stock['quantity'] = $quantity_history;
                  $data_stock['purchase_date'] = $stock->purchase_date;
                  $this->stock_model->insert_stock_history($data_stock);    

                  $difference -= $deleted;
                
                }
            }
              
          }
      }
      

    }

    public function add_stock_history($data)
    {
        if (count($data) == count($data, COUNT_RECURSIVE)) return $this->db->insert('stock_history', $data);
        else return $this->db->insert_batch('stock_history', $data);
      
    }

    public function add_stock($data)
    {
        if (count($data) == count($data, COUNT_RECURSIVE)) return $this->db->insert('stock', $data);
        else return $this->db->insert_batch('stock', $data);

    }
    public function get_average_price($conditions=array())
    {
      return $this->db->select("IFNULL((sum(quantity*price)/sum(quantity)),0) as last_hpp",false)
      ->from("stock_history")
      ->where($conditions)->get()->row();
    }
}