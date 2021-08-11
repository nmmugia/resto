<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      Diky Pratansyah <pratansyah@gmail.com>
 * @copyright   2015 Digital Oasis
 * @since       2.0.0
 */

class Stock_transfer extends Admin_Controller{

    /**
     * Hold data for currently logged in user
     * @var mixed
     */
	private $_store_data;

    /**
     * Global setting for store
     * @var mixed
     */
    private $_setting;

    /**
     * Stock request last sync
     * @var string
     */
    private $_last_sync;

    /**
     * Api URL for stock transfer handler
     * @var string
     */
    private $_api_url;

	public function __construct()
	{
		parent::__construct();
        $this->load->model('stock_request_fifo_detail_model');
        $this->load->model('stock_request_detail_model');
        $this->load->model('stock_transfer_history_model');
        $this->load->model('stock_transfer_model');
        $this->load->model('store_model');
        $this->load->model('stock_model');
		$this->load->model('inventory_model');

    $this->_store_data = $this->ion_auth->user()->row();
		$this->_setting = $this->data['setting'];
		$this->data['status_requester'] = array(
                                        1 => 'Mengirim request',
                                        2 => 'Diterima',
                                        10 => 'Gagal',
                                        20 => 'Dibatalkan');
		$this->data['status_supplier'] = array(
                                        1 => 'Belum terhubung',
                                        2 => 'Request diterima',
                                        3 => 'Terkirim',
                                        10 => 'Ditolak');
        $this->_last_sync = $this->store_model->get_stock_last_sync($this->_setting['store_id']);
        $this->_api_url = $this->_setting['server_base_url'].'api/stock_transfer/';
	}

    /**
     * Display list of transfer requests in which the currently logged in store have a role as requester
     * @return void
     */
    public function request()
    {
        $this->data['title']    = "Transfer Stock";
        $this->data['subtitle'] = "Permintaan Transfer Stock";
     
        $cond = array(
            'requester_store_id'    => $this->_setting['store_id'],
            'requester_status'      => 1,
            'supplier_status'       => array(1, 2)
        );
        $requests = $this->stock_transfer_model->get($cond);
        $unique_ids = array_map(function($elem){
            return "'".$elem->unique_id."'";
        }, $requests);
        if(empty($unique_ids))$unique_ids="''";
        $cond = array(
            'requester_store_id'    => $this->_setting['store_id'],
            'unique_id'             => $unique_ids
        );
        $requests = $this->fetch_data($cond, false, 'requester');
        if(false !== $requests){
            foreach ($requests as $request) {
                $this->stock_transfer_model->update(
                    array('supplier_status' => $request->supplier_status, 'sent_at' => $request->sent_at),
                    array('unique_id' => $request->unique_id)
                );
                if(3 == $request->supplier_status){
                    $detail_server  = $this->get_transfer_detail($request->unique_id);
                    $local_request  = array_pop($this->stock_transfer_model->get(array('unique_id' => $request->unique_id)));
                    foreach ($detail_server as $detail) {
                        $cond = array('stock_request_id' => $local_request->id, 'inventory_id' => $detail->inventory_id);
                        $local_detail = array_pop($this->stock_request_detail_model->get($cond));
                        $this->stock_request_detail_model->update(array('provided_quantity' => $detail->provided_quantity), $cond);
                        $detail->fifo_detail = array_map(function($fifo_detail) use($local_detail){
                            $fifo_detail->stock_request_detail_id = $local_detail->id;
                            return (array)$fifo_detail;
                        }, $detail->fifo_detail);
                        if(sizeof($detail->fifo_detail)>0){
                          $this->stock_request_fifo_detail_model->add($detail->fifo_detail);
                        }
                    }
                }
            }
        }

        $table = $this->stock_transfer_model->get_transfer_request(array('requester_store_id' => $this->_setting['store_id']));
        // Determine what action can be done based on status
        foreach ($table as $item) {
            $item->action = array();
            $button = array();
            if((1 == $item->supplier_status || 2 == $item->supplier_status) && 1 == $item->requester_status){
                $button['href']   = base_url().SITE_ADMIN.'/stock_transfer/detail/'.$item->id;
                $button['action'] = 'Detail';
                array_push($item->action, $button);

                $button = array();
                $button['href']   = base_url().SITE_ADMIN.'/stock_transfer/cancel/'.$item->id;
                $button['class']  = 'cancel-request-transfer';
                $button['action'] = 'Batalkan';
                array_push($item->action, $button);
            }
            elseif (3 == $item->supplier_status && 1 == $item->requester_status) {
                $button['href']   = base_url().SITE_ADMIN.'/stock_transfer/arrive/'.$item->id;
                $button['action'] = 'Terima';
                array_push($item->action, $button);
            }
            else {
                $button['href']   = base_url().SITE_ADMIN.'/stock_transfer/detail/'.$item->id;
                $button['action'] = 'Detail';
                array_push($item->action, $button);
            }
        }

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['table'] = $table;
        $this->data['content'] .= $this->load->view('admin/stock-transfer-request', $this->data, true);

        $this->render('admin');
    }

    /**
     * Display list of transfer requests in which the currently logged in store have a role as receiver 
     * @return void
     */
    public function receive()
    {
        $this->data['title']    = "Transfer Stock";
        $this->data['subtitle'] = "Penerimaan Transfer Stock";
    
        // Fetch new data based on last sync
        $requests = $this->fetch_data(array('supplier_store_id' => $this->_setting['store_id']), true, '');
        if(false !== $requests){
            foreach ($requests as $request) {
              $check=$this->stock_request_detail_model->get_all_where("stock_request",array("unique_id"=>$request->unique_id));
              if(sizeof($check)==0){
                $detail = $request->detail;
                unset($request->detail);
                unset($request->id);
                unset($request->supplier_name);
                unset($request->requester_name);

                $request_id = $this->stock_transfer_model->add($request);
                $detail = array_map(function($item) use($request_id){
                    $item->stock_request_id = $request_id;
                    $item->created_at = date('Y-m-d H:i:s',time());
                    return (array)$item;
                }, $detail);
                 
                $this->stock_request_detail_model->add($detail);
              }
            }
        }

        // Check status of requester
        $cond = array(
            'supplier_store_id'    => $this->_setting['store_id'],
            'requester_status'      => 1,
            'supplier_status'       => array(1, 2)
        );
        $requests = $this->stock_transfer_model->get($cond);
        $unique_ids = array_map(function($elem){
            return "'".$elem->unique_id."'";
        }, $requests);
        if(empty($unique_ids))$unique_ids="''";
        $cond = array(
            'supplier_store_id'    => $this->_setting['store_id'],
            'unique_id'             => $unique_ids
        );
        $requests = $this->fetch_data($cond, false);
        if(false !== $requests){
            foreach ($requests as $request) {
                $cond = array('unique_id' => $request->unique_id);
                $data = array('requester_status' => $request->requester_status);
                $this->stock_transfer_model->update($data, $cond);
            }
        }

        // Check whether stocks has been received or not
        $cond = array(
            'supplier_store_id'    => $this->_setting['store_id'],
            'requester_status'      => 1,
            'supplier_status'       => 3
        );
        $requests = $this->stock_transfer_model->get($cond);
        $unique_ids = array_map(function($elem){
            return "'".$elem->unique_id."'";
        }, $requests);
        if(empty($unique_ids))$unique_ids="''";
        $cond = array(
            'supplier_store_id'    => $this->_setting['store_id'],
            'unique_id'             => $unique_ids
        );
        $requests = $this->fetch_data($cond, false);
        if(false !== $requests){
            foreach ($requests as $request) {
                $cond = array('unique_id' => "'".$request->unique_id."'");
                $local_request = array_pop($this->stock_transfer_model->get_transfer_request($cond));
                if(isset($request->detail)){
                    foreach ($request->detail as $detail) {
                        $data = array('received_quantity' => $detail->received_quantity);
                        $cond = array(
                            'inventory_id'      => $detail->inventory_id,
                            'stock_request_id'  => $local_request->id
                        );
                        $this->stock_request_detail_model->update($data, $cond);
                    }
                }

                $cond = array('unique_id' => $request->unique_id);
                $data = array(
                    'requester_status'  => $request->requester_status,
                    'finished_at'       => $request->finished_at
                );

                $this->stock_transfer_model->update($data, $cond);
            }
        }
        
        $table = $this->stock_transfer_model->get_transfer_request(array('supplier_store_id' => $this->_setting['store_id']));
        // Determine what action can be done based on status
        foreach ($table as $item) {
            $item->action = array();
            $button = array();
            if(1 == $item->supplier_status && 1 == $item->requester_status){
                $button['href']   = base_url().SITE_ADMIN.'/stock_transfer/process/'.$item->id;
                $button['action'] = 'Proses';
                array_push($item->action, $button);
            }
            else {
                $button['href']   = base_url().SITE_ADMIN.'/stock_transfer/detail_transfer/'.$item->id;
                $button['action'] = 'Detail';
                array_push($item->action, $button);
            }
        }

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['table'] = $table;
        $this->data['content'] .= $this->load->view('admin/stock-transfer-receive', $this->data, true);

        $this->render('admin');
    }

    /**
     * Display page to process stock request
     * @param  integer $request_id Id of stock transfer request to be processed
     * @return void
     */             
    public function process($request_id=-1)
    {
      $this->load->model("stock_model");
        if($request_id < 1) {
            $this->session->flashdata('message', 'Tidak ada detail untuk transfer tersebut');
            redirect(SITE_ADMIN.'/stock_transfer/request');
        }

        $request    = array_pop($this->stock_transfer_model->get_transfer_request(array('id' => $request_id)));
        if($this->input->post()){
            extract($this->input->post());
            $sent_at = date('Y-m-d H:i:s',time());
            $this->stock_transfer_model->update(array('supplier_status' => 3, 'sent_at' => $sent_at), array('id' => $request_id));
            $this->update_status('supplier', $request->unique_id, 3);

            // insert into table stock_transfer
            $data_stock_transfer = array(
              'origin_outlet_id' => $request->supplier_outlet_id,
              'destination_outlet_id' => $request->requester_outlet_id,
              'created_at' => date("Y-m-d H:i:s")
            );
            $stock_transfer_id = $this->stock_model->save('stock_transfer', $data_stock_transfer);

            foreach ($transfer as $stock_transfer_detail_id => $item) {
              $value = $item['quantity'];
              $api = array('provided_quantity' => $value, 'inventory_id' => $item['inventory_id']);
              $this->stock_request_detail_model->update(array('provided_quantity' => $value), array('id' => $stock_transfer_detail_id));
              $transfer_remaining = $item['quantity'];
              $transfered = 0;
              $data_stocks = $this->stock_model->get_stock_detail_by_inventory_id(array(
                "store_id"=>$this->data['setting']['store_id'],
                "outlet_id"=>$request->supplier_outlet_id,
                "inventory_id"=>$item['inventory_id'],
                "uom_id"=>$item['uom_id'],
              ));
              $sum_quantity=0;
              $sum_total=0;

              foreach ($data_stocks as $stock) {
                $data_stock_history = array(
                  "store_id"=>$stock->store_id,
                  "outlet_id"=>$stock->outlet_id,
                  "inventory_id"=>$stock->inventory_id,
                  "uom_id"=>$stock->uom_id,
                  "price"=>$stock->price,
                  "status"=>2,
                  "created_at"=>date("Y-m-d H:i:s"),
                  "purchase_date"=>$stock->purchase_date,
                );
                if($transfer_remaining > 0){
                  $fifo_detail = array();
                  $fifo_detail['stock_request_detail_id'] = $stock_transfer_detail_id;
                  $fifo_detail['created_by']              = $this->_setting['store_id'];
                  $fifo_detail['created_at']              = date('Y-m-d H:i:s',time());
                  $fifo_detail['price']                   = $data_stock_history['price'];
                  $fifo_detail['purchase_date']           = $data_stock_history['purchase_date'];
                  $transfer_history = array();
                  $transfer_history['inventory_id']       = $stock->inventory_id;
                  $transfer_history['uom_id']             = $stock->uom_id;
                  $transfer_history['purchase_date']      = $data_stock_history['purchase_date'];
                  $transfer_history['price']              = $data_stock_history['price'];
                  $transfer_history['stock_transfer_id']  = $stock_transfer_id;
                  if($stock->quantity >= $transfer_remaining){
                    $sum_quantity+=$transfer_remaining;
                    $sum_total+=($transfer_remaining*$data_stock_history['price']);
                    //UPDATE STOCK
                    $this->stock_model->save("stock",array(
                      "quantity"=>$stock->quantity - $transfer_remaining,     
                    ),$stock->id);
                    //INSERT STOCK HISTORY
                    $data_stock_history['quantity'] = ($transfer_remaining*-1);
                    $this->stock_model->insert_stock_history($data_stock_history);
                    $fifo_detail['quantity']                = $transfer_remaining;
                    $transfer_history['quantity']               = $transfer_remaining;
                    $transfer_remaining = 0;
                  }else{
                    $sum_quantity+=$stock->quantity;
                    $sum_total+=($stock->quantity*$data_stock_history['price']);
                    $transfered = $stock->quantity;
                    //UPDATE STOCK
                    $this->stock_model->save("stock",array(
                      "quantity"=>$stock->quantity - $transfered,     
                    ),$stock->id);
                    //INSERT STOCK HISTORY
                    $data_stock_history['quantity'] = ($transfered*-1);
                    $this->stock_model->insert_stock_history($data_stock_history);
                    $fifo_detail['quantity']                = $transfered;
                    $transfer_history['quantity']               = $transfered;
                    $transfer_remaining -= $transfered;
                  }
                  if($this->data['setting']['stock_method']=="FIFO"){
                    $api['fifo_detail'][] = $fifo_detail;
                    $this->stock_request_fifo_detail_model->add($fifo_detail);
                    $api['transfer_history'][] = $transfer_history;
                    $this->stock_transfer_history_model->add($transfer_history);
                  }
                }else break;
              }
              if($this->data['setting']['stock_method']=="AVERAGE"){
                $average_price=0;
                if($sum_total!=0 && $sum_quantity!=0)$average_price=$sum_total/$sum_quantity;
                $fifo_detail = array();
                $fifo_detail['stock_request_detail_id'] = $stock_transfer_detail_id;
                $fifo_detail['created_by']              = $this->_setting['store_id'];
                $fifo_detail['created_at']              = date('Y-m-d H:i:s',time());
                $fifo_detail['price']                   = $average_price;
                $fifo_detail['purchase_date']           = date('Y-m-d H:i:s');
                $fifo_detail['quantity']                = $item['quantity'];
                $transfer_history = array();
                $transfer_history['inventory_id']       = $stock->inventory_id;
                $transfer_history['uom_id']             = $stock->uom_id;
                $transfer_history['purchase_date']      = $data_stock_history['purchase_date'];
                $transfer_history['stock_transfer_id']  = $stock_transfer_id;
                $transfer_history['price']              = $average_price;
                $transfer_history['created_by']         = $this->_setting['store_id'];
                $transfer_history['quantity']           = $item['quantity'];
                $api['fifo_detail'][] = $fifo_detail;
                $this->stock_request_fifo_detail_model->add($fifo_detail);
                $api['transfer_history'][] = $transfer_history;
                $this->stock_transfer_history_model->add($transfer_history);

                $this->process_save_method_average($stock->inventory_id);
              }
              $api['sent_at'] = $sent_at;
              $this->add_transfer_detail($request->unique_id , $api);
              
            }
            $this->session->flashdata('message_success', 'Pengiriman berhasil dilakukan');
            $this->prints($stock_transfer_id);
        }

        $detail = $this->stock_request_detail_model->get_items(array('stock_request_id' => $request_id));
        $detail = array_map(function($item){
            $item->sum_quantity = (null == $item->sum_quantity) ? 0 : $item->sum_quantity;
            $item->spinner      = ($item->request_quantity <= $item->sum_quantity) ? $item->sum_quantity : $item->sum_quantity;
            return $item;
        }, $detail);

        $this->data['title']    = "Transfer Stock";
        $this->data['subtitle'] = "Proses Permintaan Transfer";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['request']  = $request;
        $this->data['detail']   = $detail;
        $this->data['content'] .= $this->load->view('admin/stock-transfer-process', $this->data, true);

        $this->render('admin');
    }

    // function for process save stock with AVERAGE method
    // parameter : inventory_id
    // created by : bening
    public function process_save_method_average($inventory_id){

        $data_inventory = $this->stock_model->get_all_where('stock',array('inventory_id'=>$inventory_id));
        
        //get all stock by inventory id
        $data_stocks = $this->stock_model->get_stock_detail_by_inventory_id(array(
            "store_id" => $data_inventory->store_id,
            "outlet_id" => $data_inventory->outlet_id,
            "inventory_id" => $data_inventory->inventory_id,
            "uom_id" => $data_inventory->uom_id,
        ));
        //looping for get new price and quantity
        $average_price = 0;
        $total_quantity = 0;
        $total_price = 0;
        foreach ($data_stocks as $stock) {
            $total_price += ($stock->quantity * $stock->price);   
            $total_quantity += $stock->quantity;

            //DELETE STOCK
            $this->stock_model->delete("stock", $stock->id);
        }

        //count average price
        if ($total_price != 0 && $total_quantity != 0) $average_price = $total_price / $total_quantity;

        // Insert new stock with new quantity and average price 
        $array = array(
            'store_id' => $data_inventory->store_id,
            'outlet_id' => $data_inventory->outlet_id,
            'inventory_id' => $data_inventory->inventory_id,
            'uom_id' => $data_inventory->uom_id,
            'quantity' => $total_quantity,
            'created_at' => $data_inventory->created_at,
            'purchase_date' => $data_inventory->purchase_date,
            'price' => $average_price
        );
        $this->stock_model->save('stock', $array);        
    }

    /**
     * Display page to input stock arrival
     * @param  integer $request_id Id of stock transfer
     * @return void
     */
    public function arrive($request_id=-1)
    {
        if($request_id < 1) {
            $this->session->flashdata('message', 'Tidak ada detail untuk transfer tersebut');
            redirect(SITE_ADMIN.'/stock_transfer/request');
        }
        $request        = array_pop($this->stock_transfer_model->get_transfer_request(array('id' => $request_id)));
        if($this->input->post()){
            extract($this->input->post());
            $this->stock_transfer_model->update(array('requester_status' => 2), array('id' => $request_id));
            $this->update_status('requester', $request->unique_id, 2);
            
            $api = array();

            $data_stock_transfer = array(
              'origin_outlet_id' => $request->supplier_outlet_id,
              'destination_outlet_id' => $request->requester_outlet_id,
              'created_at' => date("Y-m-d H:i:s")
            );
            $stock_transfer_id = $this->stock_model->save('stock_transfer', $data_stock_transfer);

            foreach ($transfer as $stock_transfer_detail_id => $item) {
                $transfer_detail['inventory_id'] = $item['inventory_id'];
                $transfer_detail['received_quantity'] = $item['quantity'];
                array_push($api, $transfer_detail);

                $quantity = $item['quantity'];
                $this->stock_request_detail_model->update(array('received_quantity' => $quantity), array('id' => $stock_transfer_detail_id));
                $fifo_detail = $this->stock_request_fifo_detail_model->get(array('stock_request_detail_id' => $stock_transfer_detail_id));

                $data = array();
                $data['transfer_history']   = array();
                $data['stock_history']      = array();
                $data['stock']              = array();
                foreach ($fifo_detail as $fifo_item) {
                    if($quantity >= $fifo_item->quantity){
                        $trans_quantity = $fifo_item->quantity;
                        $quantity -= $trans_quantity;
                    }
                    else{
                        $trans_quantity = $quantity;
                        $quantity = 0;
                    }

                    $transfer_history['inventory_id']           = $item['inventory_id'];
                    $transfer_history['uom_id']                 = $item['uom_id'];
                    $transfer_history['quantity']               = $trans_quantity;
                    $transfer_history['purchase_date']          = $fifo_item->purchase_date;
                    $transfer_history['price']                  = $fifo_item->price;
                    $transfer_history['stock_transfer_id']      = $stock_transfer_id;
                    $data['transfer_history'][]                 = $transfer_history;

                    $stock_history = array();
                    $stock_history['store_id']      = $this->_setting['store_id'];
                    $stock_history['outlet_id']     = $outlet_id;
                    $stock_history['inventory_id']  = $item['inventory_id'];
                    $stock_history['quantity']      = $trans_quantity;
                    $stock_history['price']         = $fifo_item->price;
                    $stock_history['purchase_date'] = $fifo_item->purchase_date;
                    $stock_history['created_at']    = date('Y-m-d H:i:s', time());
                    $stock_history['status']        = 5;
                    $stock_history['uom_id']        = $item['uom_id'];
                    $data['stock_history'][]        = $stock_history;

                    $stock = array();
                    $stock['store_id']      = $this->_setting['store_id'];
                    $stock['outlet_id']     = $outlet_id;
                    $stock['inventory_id']  = $item['inventory_id'];
                    $stock['quantity']      = $trans_quantity;
                    $stock['price']         = $fifo_item->price;
                   
                    $stock['purchase_date'] = $fifo_item->purchase_date;
                    $stock['created_at']    = date('Y-m-d H:i:s', time());
                    $stock['uom_id']    = $item['uom_id'];
                    $data['stock'][]        = $stock;

                }
                if(!empty($data['stock_history'])) $this->stock_model->add_stock_history($data['stock_history']);
                if(!empty($data['stock'])) $this->stock_model->add_stock($data['stock']);
                if(!empty($data['transfer_history'])) $this->stock_transfer_history_model->add($data['transfer_history']);
            }

            $this->stock_transfer_model->update(array('finished_at' => date('Y-m-d H:i:s', time())), array('unique_id' => $request->unique_id));
            $this->finish_transfer($api, $request->unique_id);
            
            $this->session->flashdata('message_success', 'Pengiriman berhasil dilakukan');
            redirect(SITE_ADMIN.'/stock_transfer/request');

        }

        $detail = $this->stock_request_detail_model->get_items(array('stock_request_id' => $request_id));
        $outlets = $this->store_model->get_outlets(array('store_id' => $this->_setting['store_id']));

        $this->data['title']    = "Transfer Stock";
        $this->data['subtitle'] = "Terima Barang";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['request']  = $request;
        $this->data['detail']   = $detail;
        $this->data['outlets']   = $outlets;
        $this->data['content'] .= $this->load->view('admin/stock-transfer-arrive', $this->data, true);

        $this->render('admin');
    }

    /**
     * Cancel a request then redirect to transfer list
     * @param  integer $request_id ID of stock transfer
     * @return void
     */
    public function cancel($request_id=-1)
    {
        if($request_id < 1) {
            $this->session->flashdata('message', 'Tidak ada detail untuk transfer tersebut');
            redirect(SITE_ADMIN.'/stock_transfer/request');
        }
        $request        = array_pop($this->stock_transfer_model->get_transfer_request(array('id' => $request_id)));
        $local  = $this->stock_transfer_model->update(array('requester_status' => 20), array('id' => $request_id));
        $server = $this->update_status('requester', $request->unique_id, 20);
        
        if($local && $server) $this->session->flashdata('message_success', 'Pembatalan berhasil dilakukan');
        else $this->session->flashdata('message_success', 'Pembatalan gagal');
        redirect(SITE_ADMIN.'/stock_transfer/request');

    }

    /**
     * Render page for adding transfer stock request
     * @return void
     */
    public function add_request()
    {
        $selection = array();
        if($this->input->post()) {
            $this->form_validation->set_rules('detail[qty][]', 'Inventory Quantity', 'required|xss_clean|min_length[1]|max_length[50]|is_natural_no_zero');
            $this->form_validation->set_rules('detail[id][]', 'Inventory Name', 'required|xss_clean|min_length[1]|max_length[11]|is_natural_no_zero');
            $this->form_validation->set_rules('store-id', 'Request ke', 'required|xss_clean|min_length[1]|max_length[11]|is_natural_no_zero');
            $this->form_validation->set_rules('outlet-id', 'Outlet', 'required|xss_clean|min_length[1]|max_length[11]|is_natural_no_zero');
            if($this->form_validation->run()){
                $data = array();
                $data['unique_id']          = $this->generate_random_name(20);
                $data['requester_store_id'] = $this->_setting['store_id'];
                $data['requester_outlet_id'] = $this->input->post('requester_outlet_id');
                $data['supplier_store_id']  = $this->input->post('store-id');
                $data['supplier_outlet_id'] = $this->input->post('outlet-id');
                $data['request_at'] = date('Y-m-d H:i:s', time());
                $data['created_at'] = date('Y-m-d H:i:s', time());
                $data['requester_status']   = 1;
                $data['supplier_status']    = 1;
                $data['created_by']         = $this->_store_data->id;
                $request_id = $this->stock_transfer_model->add($data);
                $response = $this->_curl_connect($data, $this->_api_url.'add');

                // Add data to transfer stock detail
                $detail = $this->input->post("detail");
                for ($i = 0; $i < sizeof($detail['id']); $i++) {
                    $item = array();
                    $item['inventory_id']       = $detail['id'][$i];
                    $item['request_quantity']   = $detail['qty'][$i];
                    $item['created_by']         = $this->_store_data->id;
                    $item['uom_id']   = $detail['uom_id'][$i];
                    $this->_curl_connect($item, $this->_setting['server_base_url'].'api/stock_transfer/add_detail/'.$response->request_id);
                    $item['stock_request_id']   = $request_id;
                    $id = $this->stock_request_detail_model->add($item);
                }
                if($id) {
                    $this->session->flashdata('message_success', 'Permintaan transfer berhasil ditambahkan');
                    redirect(SITE_ADMIN.'/stock_transfer/request');
                }
                else{
                    $this->session->flashdata('message', 'Terjadi kesalahan, silahkan ulangi proses');
                    redirect(SITE_ADMIN.'/stock_transfer/add_request');
                }                
            }
        }

        $this->data['title']    = "Transfer Stock";
        $this->data['subtitle'] = "Request Order";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');
        $store = $this->store_model->get_all_store();

        // Remove currently logged in store from array
        $key = array_search(
            $this->data['setting']['store_id'], 
            array_column(
                array_map(function($elem){
                    return (array)$elem;
                }, $store),
                'id'), 
            true);
        if($key !== false) unset($store[$key]);

        $this->data['inventories'] = $this->inventory_model->get_all_inventories();
        $this->data['stores'] = $store;
        $this->data['outlets']  = $this->store_model->get_outlets(array('store_id' => $this->_setting['store_id']));
        $this->data['add_inventory']=$this->load->view("admin/purchase-order-add-po-create",$this->data,true);

        $this->load->model('categories_model');
        $this->data['data_store'] = $this->categories_model->get_one('store',  $this->data['setting']['store_id']);
        $this->data['content'] .= $this->load->view('admin/stock-transfer-add', $this->data, true);

        $this->render('admin');
    }

    /**
     * Display detail page for stock transfer
     * @param  integer $request_id Id of stock transfer
     * @return void
     */
    public function detail($request_id=-1)
    {
        if($request_id < 1) {
            $this->session->flashdata('message', 'Tidak ada detail untuk transfer tersebut');
            redirect(SITE_ADMIN.'/stock_transfer/request');
        }

        $request    = array_pop($this->stock_transfer_model->get_transfer_request(array('id' => $request_id)));
        $detail     = $this->stock_request_detail_model->get_items(array('stock_request_id' => $request_id));

        $this->data['title']    = "Transfer Stock";
        $this->data['subtitle'] = "Detail Request";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');
        $this->data['back_url']=base_url(SITE_ADMIN."/stock_transfer/request");
        $this->data['request']  = $request;
        $this->data['detail']   = $detail;
        $this->data['content'] .= $this->load->view('admin/stock-transfer-detail', $this->data, true);

        $this->render('admin');
    }
    public function detail_transfer($request_id=-1)
    {
        if($request_id < 1) {
            $this->session->flashdata('message', 'Tidak ada detail untuk transfer tersebut');
            redirect(SITE_ADMIN.'/stock_transfer/receive');
        }

        $request    = array_pop($this->stock_transfer_model->get_transfer_request(array('id' => $request_id)));
        $detail     = $this->stock_request_detail_model->get_items(array('stock_request_id' => $request_id));

        $this->data['title']    = "Transfer Stock";
        $this->data['subtitle'] = "Detail Request";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');
        $this->data['back_url']=base_url(SITE_ADMIN."/stock_transfer/receive");
        $this->data['request']  = $request;
        $this->data['detail']   = $detail;
        $this->data['content'] .= $this->load->view('admin/stock-transfer-detail', $this->data, true);

        $this->render('admin');
    }
    /**
     * Get all outlet from spesific store
     * @param  integer $store_id The id of store to fetch outlets from
     * @return void
     */
    public function get_outlet($store_id=-1)
    {
        if($store_id < 1) {
            $this->_response(array('status' => false));
        }
        $outlets = $this->store_model->get_outlets(array('store_id' => $store_id));
        $this->_response(array('status' => true, 'outlets' => $outlets));
    }

    /**
     * Get single inventory item
     * @param  integer $inventory_id The id of inventory to be fetched
     * @return void
     */
    public function get_inventory($inventory_id=-1)
    {
        if($inventory_id < 1) {
            $this->_response(array('status' => false));
        }
        $inventory = $this->inventory_model->get_inventory_unit($inventory_id);
        $result = array('status' => true);
        $result = array_merge($result, (array)$inventory[0]);
        $this->_response($result);
    }

    public function update_status($type='', $unique_id='', $status_code=0)
    {
        $status = 'supplier' === $type ? 'supplier_status' : 'requester_status';
        $store_id = 'supplier' === $type ? 'supplier_store_id' : 'requester_store_id';
        $data = array(
            $status     => $status_code,
            $store_id   => $this->_setting['store_id'],
            'unique_id' => $unique_id
        );
        $result = $this->_curl_connect(array('data' => json_encode($data)), $this->_api_url.'update_status/'.$type);

        return $result;
    }

    public function finish_transfer($transfer_detail=array(), $unique_id='')
    {
        $data = array(
            'finished_at'       => date('Y-m-d H:i:s', time()),
            'unique_id'         => $unique_id,
            'transfer_detail'   => $transfer_detail
        );
        $result = $this->_curl_connect(array('data' => json_encode($data)), $this->_api_url.'finish_transfer/');

        return $result;
    }

    public function prints($id)
    {
      $data = $this->input->post("prints");

      $this->load->helper("printer_helper");
      $this->db->select('i.name as item_name,
                          sth.quantity,
                          u.code,
                          (SELECT outlet_name FROM outlet WHERE id = st.origin_outlet_id) AS origin_outlet,
                          st.created_at', false)
                ->from('stock_transfer_history sth')
                ->join('stock_transfer st', 'st.id = sth.stock_transfer_id')
                ->join('inventory i', 'i.id = sth.inventory_id')
                ->join('uoms u', 'u.id = sth.uom_id')
                ->where('st.id', $id);
      $detail = $this->db->get()->result();
      $this->data['detail'] = $detail;

      //get printer dot matrix transfer stock
      $this->load->model("setting_printer_model");
      $printer_arr_obj = $this->setting_printer_model->get_printer_by_enum_printer_type(array("name_type"=>"printer_matrix_transfer"));
      foreach ($printer_arr_obj as $printer_obj) {
        $printer_location = $printer_obj->name_printer;
        print_transfer_outlet($printer_location, $this->data);
      }
      redirect(SITE_ADMIN.'/stock_transfer/receive');
    }

    /**
     * Fetch new data from API
     * @param  mixed   $cond        Condition for fetching data
     * @param  mixed   $last_sync   A flag to mark whether to use last sync time or not
     * @param  boolean $ajax A flag to mark whether the result should be displayed as json for ajax response of to be returned
     * @return mixed         Optional return when ajax flag is false
     */
    public function fetch_data($cond=array(), $last_sync=false ,$type='')
    {
        $data = $last_sync ? array( 'request_at' => "'".$this->_last_sync."'") : array();
        if(count($cond) > 0) $data = array_merge($data, $cond);
        // var_dump($data);die();
        $result = $this->_curl_connect(array('data' => json_encode($data)), $this->_api_url.'get_request/'.$type);
        $this->_update_last_sync();

        return $result;        
    }

    public function add_transfer_detail($unique_id='', $data=array())
    {
        $data = array_merge($data, array('unique_id' => "'".$unique_id."'"));
        $result = $this->_curl_connect(array('data' => json_encode($data)), $this->_api_url.'add_transfer_detail');
        return $result;

    }

    public function get_transfer_detail($unique_id='', $data=array())
    {
        $data = array_merge($data, array('unique_id' => "'".$unique_id."'"));
        $result = $this->_curl_connect(array('data' => json_encode($data)), $this->_api_url.'get_transfer_detail');
        return $result;
    }

    private function _update_last_sync()
    {
        $this->_last_sync = date('Y-m-d H:i:s',time());
        $this->store_model->update(
            array('stock_request_last_sync' => $this->_last_sync),
            array('id' => $this->_setting['store_id']));
    }

    /**
     * Display array in json format then immediately stop execution
     * @param  mixed $result array to be encoded
     * @return void
     */
    private function _response($result)
    {
        echo json_encode($result);
        die();
    }

    /**
     * Function to connect to API handler
     * @param  array  $data Data to be sent
     * @param  string $url  Url API
     * @return boolean Status
     */
    private function _curl_connect($data=array(), $url=''){
        //open connection
        $ch = curl_init();

        curl_setopt_array($ch, array(CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => 'Api Stock Request',
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $data));

        //execute post
        $result = curl_exec($ch);
        /* Check HTTP Code */
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //close connection
        curl_close($ch);

        /* 200 Response! */

        // var_dump($result);die();
        $result = json_decode($result);

        if ($status == 200) {
            if(empty($result->data)) return false;
            if ($result->status == TRUE) {
                return $result->data;

            }
            else {
            }


        }
        else {
        // curl failed
        }

        return FALSE;


    }
}