<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Stock_Opname extends Admin_Controller{

	public function __construct()
	{
		parent::__construct();
	}
	public function index()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST'){
			$this->load->model("stock_model");
			$detail=$this->input->post("detail");
			$store_id=$this->data['setting']['store_id'];
			for($x=0;$x<sizeof($detail['inventory_id']);$x++){
				if ($this->data['setting']['opname_method'] == 'DIFF') {
					$difference = $detail['qty'][$x];
				} elseif ($this->data['setting']['opname_method'] == 'EXIST') {
					$difference=$detail['qty'][$x]-$detail['last_stock'][$x];
				}					
				$inventory_id=$detail['inventory_id'][$x];
				$uom_id=$detail['uom_id'][$x];
				$outlet_id=$detail['outlet_id'][$x];
				$price=0;
				if($detail['qty'][$x]!=""){
					if($difference > 0){
						//insert stock
						$array = array(
							'store_id' => $store_id,
							'outlet_id' => $outlet_id,
							'inventory_id' => $inventory_id,
							'uom_id' => $uom_id,
							'quantity' => $difference,
							'created_at'=>date("Y-m-d h:i:s"),
							'purchase_date' =>date("Y-m-d h:i:s"),
							'price' => $price
							);
						$this->stock_model->save('stock', $array);
						//insert stock history
						$array['status']=4;
						$save = $this->stock_model->save('stock_history', $array);
					}else if($difference < 0){
						$difference *= -1;
						$data_stocks = $this->stock_model->get_stock_detail_by_inventory_id(array(
							"store_id"=>$store_id,
							"outlet_id"=>$outlet_id,
							"inventory_id"=>$inventory_id,
							"uom_id"=>$uom_id,
							));
						foreach ($data_stocks as $stock) {
							$data_stock_history = array(
								"store_id"=>$stock->store_id,
								"outlet_id"=>$stock->outlet_id,
								"quantity"=>$stock->quantity,
								"inventory_id"=>$stock->inventory_id,
								"uom_id"=>$stock->uom_id,
								"price"=>$stock->price,
								"status"=>4,
								"created_at"=>date("Y-m-d H:i:s"),
								"purchase_date"=>date("Y-m-d H:i:s"),
								);
							if($difference > 0){
								if($stock->quantity >= $difference){
									//UPDATE STOCK
									$this->stock_model->save("stock",array(
										"quantity"=>$stock->quantity - $difference,     
										),$stock->id);
									//INSERT STOCK HISTORY
									$data_stock_history['quantity'] = ($difference*-1);
									$this->stock_model->insert_stock_history($data_stock_history);
									$difference = 0;
								}else{
									$transfered = $stock->quantity;
									//UPDATE STOCK
									$this->stock_model->save("stock",array(
										"quantity"=>$stock->quantity - $transfered,     
										),$stock->id);
									//INSERT STOCK HISTORY
									$data_stock_history['quantity'] = ($transfered*-1);
									$this->stock_model->insert_stock_history($data_stock_history);
									$difference -= $transfered;
								}
							}else break;
						}
						if($difference>0){
							$array = array(
								'store_id' => $store_id,
								'outlet_id' => $outlet_id,
								'inventory_id' => $inventory_id,
								'uom_id' => $uom_id,
								'quantity' => -1*$difference,
								'created_at' =>date("Y-m-d H:i:s"),
								'purchase_date' =>date("Y-m-d H:i:s"),
								'price' => 0
								);
							$this->stock_model->save('stock', $array);
							$array = array(
								'store_id' => $store_id,
								'outlet_id' => $outlet_id,
								'inventory_id' => $inventory_id,
								'uom_id' => $uom_id,
								'quantity' => -1*$difference,
								'created_at' =>date("Y-m-d H:i:s"),
								'purchase_date' =>date("Y-m-d H:i:s"),
								'price' =>0,
								'status'=>4
								);
							$this->stock_model->save('stock_history', $array);          
						}
					} 
				}
			}
			$this->session->set_flashdata('message_success', "Stock Opname Harian Berhasil Disimpan");
			redirect(SITE_ADMIN . '/stock_opname', 'refresh');
		}else{
			$this->data['title']    = "Stok Opname Harian";
			$this->data['subtitle'] = "Stok Opname Harian";
			$this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
			$this->data['message_success'] = $this->session->flashdata('message_success');
			$this->data['outlet_lists']=$this->store_model->get_outlets(array('store_id' => $this->data['setting']['store_id']));
			$this->data['inventory_lists']=$this->store_model->get("inventory")->result();
			$this->data['content'] .= $this->load->view('admin/stock-opname-daily', $this->data, true);
			$this->render('admin');
		}
	}
	public function get_summary_inventory_data()
	{
		$this->load->model("report_model");
		$start_date=date("Y-m-d");
		$end_date=date("Y-m-d");
		$outlet_id=$this->input->post("outlet_id");
		$inventory_id=$this->input->post("inventory_id");
		$this->data['is_print'] = FALSE;
		$this->data['start_date'] =  $start_date;
		$this->data['end_date'] =  $end_date;
		$this->data['outlet_id'] =  $outlet_id;
		$this->data['inventory_id'] =  $inventory_id;
		$this->data['history_status']=$this->store_model->get_all_where("enum_stock_history_status");
		$this->data['data_store'] = $this->store_model->get_by('store',$this->data['setting']['store_id']);
		$params = array();
		if($outlet_id){
			array_push($params,"outlet_id = '".$outlet_id."'");
		}

		if($inventory_id){
			array_push($params,"inventory_id = '".$inventory_id."'");
		}
		array_push($params, "o.store_id = ".$this->data['setting']['store_id']) ;
		$this->data['results']=$this->report_model->opname_daily($params);
		$this->load->view('admin/stock-opname-daily-table', $this->data);
	}
}