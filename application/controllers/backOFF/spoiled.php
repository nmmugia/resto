<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Spoiled extends Admin_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model("stock_model");
  }

  public function index()
  {
    $this->data['title']    = "Spoiled";
    $this->data['subtitle'] = "Spoiled";
		$this->data['outlet_lists']=$this->store_model->get_all_where("outlet", array('store_id' => $this->data['setting']['store_id']));
		$this->data['inventory_lists']=$this->store_model->get("inventory")->result();
    $this->data['data_url'] = base_url(SITE_ADMIN . '/spoiled/get_history_inventory');
		$this->data['inventories']=$this->stock_model->get("inventory")->result();
    $this->data['content'] .= $this->load->view('admin/spoiled-list', $this->data, true);
    $this->render('admin');
  }
	public function get_history_inventory(){
		$this->load->library(array('datatables'));
		$this->load->helper(array('datatables'));
		$where=array();
		if($_POST){
			$data=$this->input->post();
			if($data['columns'][0]['search']['value']!=""){
				$where=array_merge($where,array("sh.outlet_id"=>$data['columns'][0]['search']['value']));
			}
			if($data['columns'][1]['search']['value']!=""){
				$where=array_merge($where,array("sh.inventory_id"=>$data['columns'][1]['search']['value']));
			}
		}
		$this->datatables->select('
			i.name,concat(i.name,IF(u.code IS NOT NULL,CONCAT(" (", u.code, ")"),"")) as join_name,
			i.unit,u.code,o.outlet_name, sh.inventory_id, o.is_warehouse,
			sum(if(sh.status=7,-1*sh.quantity,0)) as total_spoiled', false)
		->from('stock_history sh')
		->join('uoms u','sh.uom_id=u.id')
		->join('outlet o','sh.outlet_id=o.id')
		->join('inventory i','sh.inventory_id=i.id')
		->where('date(sh.created_at) <=', date("Y-m-d"))
    ->where('o.store_id', $this->data['setting']['store_id']);
		foreach($where as $key=>$value){
			$this->datatables->where($key,$value);
		}
		$this->datatables->group_by('sh.outlet_id,sh.inventory_id,sh.uom_id')
		->unset_column('total_spoiled')
		->add_column('total_spoiled','$1','convert_quantity(total_spoiled)')
    ->unset_column('cost_spoiled')
    ->add_column('cost_spoiled', '$1', 'get_cost_spoiled(inventory_id, is_warehouse)');
		echo $this->datatables->generate();
  }
  public function save_spoiled(){
    $this->load->model('stock_model');
    $inventory_id = $this->input->post('inventory_id');
    $uom_id = $this->input->post('uom_id');
    $outlet_id = $this->input->post('outlet_id');
    $store_id = $this->input->post('store_id');
    $quantity_spoiled = $this->input->post('quantity');
    $description = $this->input->post('description');
    
    $ret_data['data'] = array();
    $ret_data['status']  = false;
    $ret_data['message'] = "";

    $data_stocks = $this->stock_model->get_stock_by_inventory_id($inventory_id,$uom_id,$outlet_id);
    $total_stok = 0;
    $save_spoiled  = false;
    if(!empty($data_stocks)){
      $total_qty=0;
      foreach($data_stocks as $d){
        $total_qty+=$d->quantity;
      }
      // $total_stok = $data_stocks[0]->quantity - $quantity_spoiled; 
      $total_stok = $total_qty - $quantity_spoiled; 
      if($total_stok >= 0){
        $remain=$quantity_spoiled;
        foreach($data_stocks as $d){
          $qty=0;
          $stok=$d->quantity;
          if($d->quantity>0 && $remain>0){
            if($d->quantity>=$remain){
              $qty=$remain;
              $remain=0;
            }else{
              $qty=$d->quantity;
              $remain-=$d->quantity;
            }
            $data_update_stock = array( "quantity" => ($d->quantity-$qty));
            $save_spoiled = $this->stock_model->update_stock_by_id($d->id, $data_update_stock);
            if($save_spoiled){
                $data_stock_history = array(
                  "store_id"=>$d->store_id,
                  "outlet_id"=>$d->outlet_id,
                  "quantity"=> -$quantity_spoiled,
                  "inventory_id"=>$inventory_id,
                  "uom_id"=>$uom_id,
                  "price"=> $d->price,
                  "description"=> $description,
                  "status"=>7
                );

              $this->stock_model->insert_stock_history($data_stock_history);   
            }
            if($remain<=0){
              break;
            }
          }
        }
      }else{ 
           $ret_data['message'] = "Sisa Stok Kurang Dari Nol";
      } 
    }else{
          $ret_data['message'] = "Tidak Ada Stok";
    } 
   
    if($save_spoiled){
        $ret_data['status']  = true;
        $ret_data['message'] = "success";
    } 
    echo json_encode($ret_data);
  
  }
}