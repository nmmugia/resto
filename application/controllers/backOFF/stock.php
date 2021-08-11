<?php if (! defined('BASEPATH')) exit('No direct script access allowed');


class Stock extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function stocklet()
    {

        $this->data['title']    = "Outlet Stock";
        $this->data['subtitle'] = "Outlet Stock";
     
        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        //$this->data['data_url'] = base_url(SITE_ADMIN . '/inventory/get_outlet_stock');;
        //load content
        $this->data['content'] .= $this->load->view('admin/stock-outlet-list', $this->data, true);
        $this->render('admin');
    }
     public function get_data_outlet_stock()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));
 
        $this->datatables->select('outlet.id, outlet_name, store_name,value')
                        ->from('outlet')
                        ->join('store', 'store.id = outlet.store_id')
                         ->join('enum_outlet_category eo', 'eo.id = outlet.is_warehouse')
                         ->where('store_id', $this->data['setting']['store_id']);
                         if($this->data['user_profile_data']->outlet_id!=0){
                           $this->datatables->where("outlet.id",$this->data['user_profile_data']->outlet_id);
                         }
                         $this->datatables->add_column('actions', "<div class='btn-group'>
                                <a href='" . base_url(SITE_ADMIN . '/stock/list_stock/$1') . "'  class='btn btn-default'>  Stok</a>
                                <a href='" . base_url(SITE_ADMIN . '/stock/transfer/$1') . "' class='btn btn-default'  > Transfer Stok</a>
                            </div>", 'id');
                            //<a href='" . base_url(SITE_ADMIN . '/stock/pembelian/$1') . "' class='btn btn-default'  > Tambah Stok</a>
        
        echo $this->datatables->generate();
    }
     public function transfer()
    {
        $this->load->model('categories_model');
        $this->load->model('stock_model');
         $this->load->model('store_model');
        $this->data['title']    = "Transfer Stock";
        $this->data['subtitle'] = "Transfer Stock";
     
        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');
        
        $user_data = $this->ion_auth->user()->row();
        $data_store = $this->store_model->get_store($this->data['setting']['store_id']);
        $this->data['data_store']    = $data_store[0];
        $outlet_id = $this->uri->segment(4);
        
        $data_outlet= $this->store_model->get_outlet_not_id($data_store[0]->id,$outlet_id);

        $this->data['outlets'] = $data_outlet;

       
        $this->data['origin_outlet_id'] = $outlet_id;
        
        $this->data['transfers'] = array('name' => 'transfers',
                                           'id' => 'transfers',
                                           'type' => 'hidden',
                                           'class' => 'form-control requiredTextField',
                                           'field-name' => 'transfers'
                                           );
        $data_stocks                     = $this->stock_model->get_all_stock_by_outlet_id($outlet_id);
        $this->data['data_stocks']    = $data_stocks;

        $this->data['content'] .= $this->load->view('admin/stock-transfer', $this->data, true);
        $this->render('admin');
    }
    public function transfer_add(){
        $this->load->model('stock_model');
        $destination_store_id = $this->input->post("store_id");
        $origin_outlet_id = $this->input->post("origin_outlet_id");
        $destination_outlet_id = $this->input->post("outlet_id");
        $transfers = $this->input->post("transfers");
        $data_items = json_decode($transfers,true);

        $data_stock_transfer = array(
          'origin_outlet_id' => $origin_outlet_id,
          'destination_outlet_id' => $destination_outlet_id,
          'created_at' => date("Y-m-d H:i:s")
        );
        $stock_transfer_id = $this->stock_model->save('stock_transfer', $data_stock_transfer);
       
        $transfer_remaining = 0;
        $transfered = 0;
        $transfer_history = array();
        $x = 0;
        foreach ($data_items as $item) {
          $temp=explode("_",$item['product_id']);
          $item['product_id']=$temp[0];
          $uom_id=$temp[1];
          $transfer_remaining = $item['product_amount'];
         
          $data_stocks = $this->stock_model->get_stock_detail_by_inventory_id(array(
            "store_id"=>$this->data['setting']['store_id'],
            "outlet_id"=>$origin_outlet_id,
            "inventory_id"=>$item['product_id'],
            "uom_id"=>$uom_id,
          ));
          $sum_total=0;
          $sum_quantity=0;
          foreach ($data_stocks as $stock) {
            $data_stock_history = array(
              "store_id"=>$stock->store_id,
              "outlet_id"=>$stock->outlet_id,
              "quantity"=>$stock->quantity,
              "inventory_id"=>$stock->inventory_id,
              "uom_id"=>$stock->uom_id,
              "price"=>$stock->price,
              "status"=>2,
              "created_at"=>date("Y-m-d H:i:s"),
              "purchase_date"=>$stock->purchase_date,
            );
            if($transfer_remaining > 0){
                if($stock->quantity >= $transfer_remaining){
                  $sum_quantity+=$transfer_remaining;
                  $sum_total+=($transfer_remaining*$stock->price);
                  //UPDATE STOCK
                  $this->stock_model->save("stock",array(
                    "quantity"=>$stock->quantity - $transfer_remaining,     
                  ),$stock->id);
                  //INSERT STOCK HISTORY
                  $data_stock_history['quantity'] = ($transfer_remaining*-1);
                  $this->stock_model->insert_stock_history($data_stock_history);
                  if($this->data['setting']['stock_method']=="FIFO"){
                    //INSERT STOCK DESTINATION
                    $stock_destination=$this->stock_model->get_all_where("stock",array(
                      "store_id"=>$destination_store_id,
                      "outlet_id"=>$destination_outlet_id,
                      "inventory_id"=>$item['product_id'],
                      "uom_id"=>$uom_id
                    ));
                    if(sizeof($stock_destination)>0){
                      $stock_destination=$stock_destination[0];
                      $this->stock_model->save("stock",array(
                        "quantity"=>$stock_destination->quantity + $transfer_remaining,     
                      ),$stock_destination->id);
                    }else{
                      $array = array(
                        'store_id' => $destination_store_id,
                        'outlet_id' => $destination_outlet_id,
                        'inventory_id' => $item['product_id'],
                        'uom_id' => $uom_id,
                        'quantity' => $transfer_remaining,
                        'created_at'=>date("Y-m-d H:i:s"),
                        'purchase_date' =>$stock->purchase_date,
                        'price' => $stock->price
                      );
                      $this->stock_model->save('stock', $array);
                    }
                    //INSERT DESTINATION STOCK HISTORY
                    $data_destination_stock_history = array(
                      "store_id"=>$destination_store_id,
                      "outlet_id"=>$destination_outlet_id,
                      "quantity"=>$transfer_remaining,
                      "inventory_id"=>$item['product_id'],
                      "uom_id"=>$uom_id,
                      "price"=>$stock->price,
                      "status"=>5,
                      "created_at"=>date("Y-m-d H:i:s"),
                      "purchase_date"=>$stock->purchase_date,
                    );
                    $this->stock_model->insert_stock_history($data_destination_stock_history);
                  }

                  $data_transfer_history = array(
                    'inventory_id' => $item['product_id'],
                    'uom_id' => $uom_id,
                    'quantity' => $transfer_remaining,
                    'purchase_date' => $stock->purchase_date,
                    'price' => $stock->price,
                    'stock_transfer_id' => $stock_transfer_id
                  );
                  $transfer_history[$x] = $this->stock_model->save('stock_transfer_history', $data_transfer_history);

                  $transfer_remaining = 0;
                }else{
                  $sum_quantity+=$stock->quantity;
                  $sum_total+=($stock->quantity*$stock->price);
                  $transfered = $stock->quantity;
                  //UPDATE STOCK
                  $this->stock_model->save("stock",array(
                    "quantity"=>$stock->quantity - $transfered,     
                  ),$stock->id);
                  //INSERT STOCK HISTORY
                  $data_stock_history['quantity'] = ($transfered*-1);
                  $this->stock_model->insert_stock_history($data_stock_history);
                  if($this->data['setting']['stock_method']=="FIFO"){
                    //INSERT STOCK DESTINATION
                    $stock_destination=$this->stock_model->get_all_where("stock",array(
                      "store_id"=>$destination_store_id,
                      "outlet_id"=>$destination_outlet_id,
                      "inventory_id"=>$item['product_id'],
                      "uom_id"=>$uom_id
                    ));
                    if(sizeof($stock_destination)>0){
                      $stock_destination=$stock_destination[0];
                      $this->stock_model->save("stock",array(
                        "quantity"=>$stock_destination->quantity + $transfered,     
                      ),$stock_destination->id);
                    }else{
                      $array = array(
                        'store_id' => $destination_store_id,
                        'outlet_id' => $destination_outlet_id,
                        'inventory_id' => $item['product_id'],
                        'uom_id' => $uom_id,
                        'quantity' => $transfered,
                        'created_at'=>date("Y-m-d H:i:s"),
                        'purchase_date' => $stock->purchase_date,
                        'price' => $stock->price
                      );
                      $this->stock_model->save('stock', $array);
                    }
                    //INSERT DESTINATION STOCK HISTORY
                    $data_destination_stock_history = array(
                      "store_id"=>$destination_store_id,
                      "outlet_id"=>$destination_outlet_id,
                      "quantity"=>$transfered,
                      "inventory_id"=>$item['product_id'],
                      "uom_id"=>$uom_id,
                      "price"=>$stock->price,
                      "status"=>5,
                      "created_at"=>date("Y-m-d H:i:s"),
                      "purchase_date"=>$stock->purchase_date,
                    );
                    $this->stock_model->insert_stock_history($data_destination_stock_history);
                  }

                  $data_transfer_history = array(
                    'inventory_id' => $item['product_id'],
                    'uom_id' => $uom_id,
                    'quantity' => $transfered,
                    'purchase_date' => $stock->purchase_date,
                    'price' => $stock->price,
                    'stock_transfer_id' => $stock_transfer_id
                  );
                  $transfer_history[$x] = $this->stock_model->save('stock_transfer_history', $data_transfer_history);

                  $transfer_remaining -= $transfered;
                }
            }else break;
          }
          if($this->data['setting']['stock_method']=="AVERAGE"){
            $average_price=0;
            if($sum_total!=0 && $sum_quantity!=0)$average_price=$sum_total/$sum_quantity;
            $array = array(
              'store_id' => $destination_store_id,
              'outlet_id' => $destination_outlet_id,
              'inventory_id' => $item['product_id'],
              'uom_id' => $uom_id,
              'quantity' => $item['product_amount'],
              'created_at'=>date("Y-m-d H:i:s"),
              'purchase_date' =>$stock->purchase_date,
              'price' => $average_price
            );
            $this->stock_model->save('stock', $array);
            //INSERT DESTINATION STOCK HISTORY
            $data_destination_stock_history = array(
              "store_id"=>$destination_store_id,
              "outlet_id"=>$destination_outlet_id,
              "quantity"=>$item['product_amount'],
              "inventory_id"=>$item['product_id'],
              "uom_id"=>$uom_id,
              "price"=>$average_price,
              "status"=>5,
              "created_at"=>date("Y-m-d H:i:s"),
              "purchase_date"=>$stock->purchase_date,
            );
            $this->stock_model->insert_stock_history($data_destination_stock_history);
          }
          $x++;
        }
        $this->session->set_flashdata('message_success', "Stock Transfered");
        $this->prints($stock_transfer_id);
        redirect(SITE_ADMIN . '/stock/transfer/'.$origin_outlet_id, 'refresh');
    }
    public function list_stock(){
      $this->data['title']    = "Outlet Stock";
      $this->data['subtitle']    = "Outlet Stock";
      
      $this->load->model('store_model');
      $outlet_id = $this->uri->segment(4);
      $data_store = array();
      $data_outlet = array();

      $data_outlet = $this->store_model->get_outlet($outlet_id);
      if(!empty($data_outlet)){
        $data_store = $this->store_model->get_store($data_outlet[0]->store_id);  
      }
      

      $this->data['outlet_id'] = $outlet_id;
      $this->data['data_outlet'] = $data_outlet;
      $this->data['data_store'] = $data_store;
      $this->data['content'] .= $this->load->view('admin/stock-list', $this->data, true);
      $this->render('admin');
    }
    public function get_data_stock(){
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));
        $outlet_id = $this->uri->segment(4);
        $column = '';

        /*if ($this->data['setting']['stock_method'] == 'FIFO') {
          $column = ', s.created_at';
        }

        if ($this->data['setting']['stock_method'] == 'AVERAGE') {
          $column = '';
        }*/

        $this->datatables->select('i.id, sum(s.quantity) as jumlah, i.name, i.unit, s.price, s.outlet_id, s.uom_id, u.code, s.created_at', false)
                        ->from('stock_history s')
                        ->join('uoms u','s.uom_id = u.id', 'left')
                        ->join('inventory i', 's.inventory_id = i.id')
                        ->group_by('s.inventory_id, s.uom_id'.$column)
                        ->where('outlet_id',$outlet_id);
        
        echo $this->datatables->generate();
    }
    public function detail(){
      $this->data['title']    = "Stok Detail";
      $this->data['subtitle']    = "Stok Detail";
      $outlet_id = $this->uri->segment(4);
      $inventory_id = $this->uri->segment(5);
      $uom_id = $this->uri->segment(6);
      $this->load->model('inventory_model');

      $detail_stock = $this->inventory_model->get_inventory_by_id($inventory_id,$outlet_id,$uom_id);
     
      $this->data['detail_stock'] = $detail_stock;
      $this->data['inventory_id'] = $inventory_id;
      $this->data['outlet_id'] = $outlet_id;
      $this->data['uom_id'] = $uom_id;

      $this->data['content'] .= $this->load->view('admin/stock-detail', $this->data, true);
      $this->render('admin');
    }
    public function opname(){
      $this->data['title']    = "Stok Detail";
      $this->data['subtitle']    = "Stok Detail";
      $inventory_id = $this->uri->segment(5);
      $outlet_id = $this->uri->segment(4);
      $uom_id = $this->uri->segment(6);
      $this->load->model('inventory_model');

      $detail_stock = $this->inventory_model->get_inventory_by_id($inventory_id,$outlet_id,$uom_id);
     
      $this->data['detail_stock'] = $detail_stock;
      $this->data['inventory_id'] = $inventory_id;
      $this->data['outlet_id'] = $outlet_id;
      $this->data['uom_id'] = $uom_id;

      $this->data['content'] .= $this->load->view('admin/stock-opname', $this->data, true);
      $this->render('admin');
    }
    public function get_detail_stock(){
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $outlet_id = $this->uri->segment(4);
        $inventory_id = $this->uri->segment(5);
        $uom_id = $this->uri->segment(6);
       
        $this->datatables->select('s.purchase_date as tanggal,s.quantity as jumlah,i.unit,s.price,u.code')
                        ->from('stock s')
                        ->join('uoms u',"s.uom_id=u.id","left")
                        ->join('inventory i', 's.inventory_id = i.id')
                        
                        ->where('inventory_id',$inventory_id)  
                        ->where('outlet_id',$outlet_id);
        if($uom_id!=""){
          $this->db->where("s.uom_id",$uom_id);
        }
        echo $this->datatables->generate();
    }
    public function pembelian($id=""){
      $this->data['title']    = "Form Tambah Stok";
      $this->data['subtitle']    = "Form Tambah Stok";
      $outlet_id = $this->uri->segment(4);
      $this->load->model('store_model');
      $this->load->model('categories_model');
      $this->load->model('inventory_model');
      $this->data['origin_outlet_id'] = $outlet_id;
      
      
      $data_outlet = $this->store_model->get_dropdown_outlet_by_outlet_id($outlet_id);
      $this->data['outlet_id']    = $data_outlet;


      $data_store = $this->store_model->get_store_by_outlet_id($outlet_id);
      $this->data['store_id']    = $data_store;

      $inventory                     = $this->inventory_model->get_inventory();
      $this->data['inventory']    = $inventory;
       $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
      
       $this->data['message_success'] = $this->session->flashdata('message_success');
      

      $this->data['content'] .= $this->load->view('admin/stock-add', $this->data, true);
      $this->render('admin');
    }
    public function add(){
       $this->load->model('stock_model');
        $this->load->model('categories_model');

        $destination_store_id = $this->input->post("store_id");
        $outlet_id = $this->input->post("outlet_id");
        $quantity = $this->input->post("quantity");
        $inventory_id = $this->input->post("inventory_id");
        
        $purchase_date = $this->input->post("purchase_date");
        $time_purchase = $this->input->post("time_purchase");
        $price = $this->input->post("price");

         $origin_outlet_id = $this->input->post("origin_outlet_id");

       
        $tanggal = date("Y-m-d H:i", strtotime($purchase_date));

        $array = array(
                  'store_id' => $this->input->post('store_id'),
                  'outlet_id' => $this->input->post('outlet_id'),
                  'inventory_id' => $this->input->post('inventory_id'),
                  'quantity' => $this->input->post('quantity'),
                  'purchase_date' => $tanggal,
                  'price' => $this->input->post('price'),
                  'uom_id' => $this->input->post('uom_id'),

        );

        $save = $this->categories_model->save('stock', $array);


        $array = array(
                  'store_id' => $this->input->post('store_id'),
                  'outlet_id' => $this->input->post('outlet_id'),
                  'inventory_id' => $this->input->post('inventory_id'),
                  'quantity' => $this->input->post('quantity'),
                  'created_at' => $tanggal,
                  'price' => $this->input->post('price'),
                  'uom_id' => $this->input->post('uom_id'),
                  'status'=>3

        );

        $save = $this->categories_model->save('stock_history', $array);
        if ($save === false) {
            $this->session->set_flashdata('message', $this->lang->line('error_add'));
        }
        else {
            $this->session->set_flashdata('message_success', $this->lang->line('success_add'));
        }

         redirect(SITE_ADMIN . '/stock/pembelian/'.$origin_outlet_id, 'refresh');

    }
    public function get_stock_by_date(){
      
      $tgl_upname_value = $this->input->post("tgl_opname_value");
      $inventory_id = $this->input->post("inventory_id");
      $this->load->model('stock_model');
      $data = $this->stock_model->get_sum_stock($tgl_upname_value,$inventory_id);
     echo json_encode($data);
       
    }
    public function opname_add(){
      $this->load->model('stock_model');
      $difference = $this->input->post("difference");
      $store_id = $this->input->post("store_id");
      $inventory_id = $this->input->post("inventory_id");
      $outlet_id = $this->input->post("outlet_id");
      $uom_id = $this->input->post("uom_id");
      $price = $this->input->post("price");
      $quantity_opname = $this->input->post("quantity_opname");
      if($quantity_opname!=""){
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
            $this->categories_model->save('stock', $array);
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
            $this->categories_model->save('stock_history', $array);          
          }
        } 
      }
      /*
      if($difference > 0){
        //insert stock
          $data_stock = array(
          "store_id"=>$store_id,
          "outlet_id"=>$outlet_id,
          "quantity"=>$difference,
          "inventory_id"=>$inventory_id,
          "uom_id"=>$uom_id,
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
              "uom_id" => $uom_id,
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
              "uom_id"=>$uom_id,
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
      } */
      
      $this->session->set_flashdata('message_success', "Stock Opname Sukses");
      redirect(SITE_ADMIN . '/stock/list_stock/'.$outlet_id, 'refresh');
    }
	public function adjustment(){
    if ($this->input->server('REQUEST_METHOD') == 'POST'){
      $this->load->model("stock_model");
      $detail=$this->input->post("detail");
      $store_id=$this->data['setting']['store_id'];
      for($x=0;$x<sizeof($detail['inventory_id']);$x++){
        $difference=$detail['qty'][$x]-$detail['stock_system'][$x];
        $inventory_id=$detail['inventory_id'][$x];
        $uom_id=$detail['uom_id'][$x];
        $outlet_id=$detail['outlet_id'][$x];
        $price=$detail['price'][$x];
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
              $this->categories_model->save('stock', $array);
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
              $this->categories_model->save('stock_history', $array);          
            }
          } 
        }
      }
      $this->session->set_flashdata('message_success', "Stock Opname Berhasil Disimpan");
      redirect(SITE_ADMIN . '/stock/stocklet', 'refresh');
    }else{
      $this->data['title']    = "Stok Opname";
      $this->data['subtitle']    = "Stok Opname";
      $this->load->model('inventory_model');
      $this->data['inventory_lists']=$this->inventory_model->get("inventory")->result();
      $this->data['outlets']=$this->inventory_model->get("outlet")->result();
      $this->data['detail_stock'] = $this->inventory_model->inventory_opname();
      $this->data['content'] .= $this->load->view('admin/stock-opname-all', $this->data, true);
      $this->render('admin');  
    }
  }
  public function get_adjustment_by_outlet()
  {
    $this->load->model('inventory_model');
    $outlet_id=$this->input->post("outlet_id");
    $inventory_id=$this->input->post("inventory_id");
    $this->data['detail_stock'] = $this->inventory_model->inventory_opname(array(
      "outlet_id"=>$outlet_id,
      "inventory_id"=>$inventory_id,
    ));
    $content= $this->load->view('admin/stock-opname-all-table', $this->data, true);
    echo json_encode(array(
      "content"=>$content
    ));
  }
  public function lists()
  {
    $this->data['title']    = "Daftar Stok Inventory";
    $this->data['subtitle']    = "Stok Inventory";
    $this->load->model('inventory_model');
    $this->data['stocks'] = $this->inventory_model->inventory_opname();
    $this->data['content'] .= $this->load->view('admin/stock-all', $this->data, true);
    $this->render('admin');  
  }
  public function set_outlet_stock(){
    $this->load->model('inventory_model');
    $this->load->model('store_model');
    $data_store = $this->store_model->get_store($this->data['setting']['store_id']);

    if ($this->input->server('REQUEST_METHOD') == 'POST'){ 
      $this->load->model('stock_model');

      $detail = $this->input->post("detail");
      if(!empty($detail['outlet_id'])){
        foreach ($detail['outlet_id'] as $key => $value) {
          $inventory_id = $key;
          $inventory_price = (isset($detail['inventory_id'][$key]) ? $detail['inventory_id'][$key] : 0);
          $uom_id = (isset($detail['uom_id'][$key]) ? $detail['uom_id'][$key] : 0);
          foreach ($value as $key_outlet => $value_outlet) {
            if($uom_id!=0){
              $outlet_id = $value_outlet;
              $data_stock = array(
                "store_id"=>$data_store[0]->id,
                "outlet_id"=>$value_outlet,
                "quantity"=>0,
                "inventory_id"=>$inventory_id,
                "price"=>$inventory_price,
                "created_at"=>date("Y-m-d h:i:s"),
                "purchase_date"=>date("Y-m-d h:i:s"),
                "uom_id"=>$uom_id
              );
              $this->stock_model->insert_stock($data_stock); 
              $data_stock['status']=4;
              $this->stock_model->insert_stock_history($data_stock);               
            }
          }
        } 

         redirect(SITE_ADMIN . '/stock/set_outlet_stock', 'refresh');
      }
      
    } 
   
    
    $all_inventory = $this->inventory_model->all_inventory();
    $data_stocks = array();
  

    $newArray = array();
    foreach($all_inventory as $entity)
    {
        if(!isset($newArray[$entity->id]))
        {
             $newArray[$entity->id]['id'] = $entity->id;
             $newArray[$entity->id]['name'] = $entity->name;
             $newArray[$entity->id]['unit'] = $entity->unit;
             $newArray[$entity->id]['stock_system'] = $entity->stock_system; 
             $newArray[$entity->id]['price'] = $entity->price;
             $newArray[$entity->id]['uom_id'] = $entity->uom_id;
        }

        $newArray[$entity->id]['outlets'][] = $entity->outlet_id;
    }
    // print_r($newArray);
    // die();

    $this->data['detail_stock'] = $newArray;

    $this->data['outlets']= $this->store_model->get_outlet_by_store_id($data_store[0]->id); 

    $this->data['content'] .= $this->load->view('admin/set-all-outlet-stock', $this->data, true);
    $this->render('admin');  
  }

  public function prints($id) {
    $data = $this->input->post("prints");
    
    $this->load->helper("printer_helper");
    $this->db->select(' i.name as item_name,
                        sth.quantity,
                        u.code,
                        (SELECT outlet_name FROM outlet WHERE id = st.origin_outlet_id) AS origin_outlet,
                        st.created_at
                      ', false)
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

    redirect(SITE_ADMIN . '/reports/transfer_menu', 'refresh');
  }

  function findObjectById($data,$id){
    $array = $data; 
    foreach ( $array as $element ) {
        if ( $id == $element->id ) {
            return $element;
        }
    }

    return false;
  }


}