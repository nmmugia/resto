<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory_Process extends Admin_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('inventory_model');
    $this->load->model('categories_model');
  }

  public function index($inventory_id=0)
  {
    $inventory = $this->categories_model->get_one("inventory",$inventory_id);
    
    if($inventory){
      $this->data['title']    = "History Proses Inventori : ".$inventory->name;
      $this->data['subtitle'] = "History Proses Inventori : ".$inventory->name;
    } else {
      $this->data['title']    = "History Proses Inventori : - ";
      $this->data['subtitle'] = "History Proses Inventori : - ";
    }
    $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
    $this->data['message_success'] = $this->session->flashdata('message_success');
    $this->data['add_url']  = base_url(SITE_ADMIN . '/inventory_process/add');
    $this->data['data_url'] = base_url(SITE_ADMIN . '/inventory_process/get_data/'.$inventory_id);
    $this->data['content'] .= $this->load->view('admin/inventory-process-list', $this->data, true);
    $this->render('admin');
  }
  public function get_data($inventory_id=0)
  {
    $this->load->library(array('datatables'));
    $this->load->helper(array('datatables'));
    $this->datatables->select('ip.id,i.name,i.unit,ip.quantity,ip.created_at')
    ->from('inventory_process ip')
    ->join('inventory i', 'i.id = ip.inventory_id')
		->where('i.id',$inventory_id);
    // ->order_by('ip.created_at','desc');
    // ->add_column('actions', "<div class='btn-group'>
      // <a href='" . base_url(SITE_ADMIN . '/inventory_process/delete/$1') . "' class='btn btn-danger deleteNow' rel='konversi inventory'><i class='fa fa-trash-o'></i> Hapus</a>
    // </div>", 'id');
    echo $this->datatables->generate();
  }

  public function add()
  {
    $this->load->model("stock_model");
    // $this->data['title']    = "Tambah Proses";
    // $this->data['subtitle'] = "Tambah Proses";
    $this->form_validation->set_rules('store_id', 'Resto', 'required');
    $this->form_validation->set_rules('outlet_id', 'Outlet', 'required');
    $this->form_validation->set_rules('inventory_id', 'Inventori', 'required');
    $this->form_validation->set_rules('quantity', 'Jumlah Proses', 'required');

    if ($this->form_validation->run() == true) {

      $data = $this->input->post();
      $inventory=$this->inventory_model->get_one("inventory",$data['inventory_id']);
      $created_at=date("Y-m-d H:i:s");
      $save = $this->inventory_model->save('inventory_process',array(
        'store_id' => $data['store_id'],
        'outlet_id' => $data['outlet_id'],
        'inventory_id' => $data['inventory_id'],
        'quantity' => $data['quantity'],
        'created_at' => $created_at,
      ));
      $inventory_convertion=$this->inventory_model->get_inventory_convertion(array("inventory_id"=>$data['inventory_id']));
      $results=array();
      $temp=array();

      //GET DATA RELATED CONVERTION UOM
      foreach($inventory_convertion as $i){
        array_push($results,array(
          "inventory_id"=>$i->inventory_id,
          "inventory_name"=>$i->inventory_name,
          "uom_id"=>$i->uom_id,
          "code"=>$i->code,
          "quantity"=>1/$i->convertion*$data['quantity'],
          "convertion"=>$i->convertion
        ));
        array_push($temp,$i->inventory_id."_".$i->uom_id);
      }

      $inventory_compositions=$this->inventory_model->get_inventory_composition(array("parent_inventory_id"=>$data['inventory_id']));
      $results2=array();
      
      //GET DATA RELATED INVENTORY COMPOSITION
      foreach($inventory_compositions as $i){
        if(!in_array($i->inventory_id."_".$i->uom_id,$temp))
        {
          array_push($results,array(
            "inventory_id"=>$i->inventory_id,
            "inventory_name"=>$i->inventory_name,
            "uom_id"=>$i->uom_id,
            "code"=>$i->code,
            "quantity"=>$i->quantity*$data['quantity'],
            "convertion"=>$i->quantity
          ));        
        }else{
          $key=array_search($i->inventory_id."_".$i->uom_id,$temp);
          $results[$key]['quantity']+=$i->quantity*$data['quantity'];
        }
      }
      $average_hpp=0;
      $sum_quantity=0;
      $sum_total_price=0;
      $is_convertion = (!empty($inventory_convertion)) ? 1 : 0;
      $temp=array();
      foreach($results as $r){
        $data_stocks = $this->stock_model->get_stock_detail_by_inventory_id(array(
          "store_id"=>$data['store_id'],
          "outlet_id"=>$data['outlet_id'],
          "inventory_id"=>$r['inventory_id'],
          "uom_id"=>$r['uom_id'],
        ));
        if(!isset($temp[$data['store_id']."_".$data['outlet_id']."_".$r['inventory_id']."_".$r['uom_id']])){
          $temp[$data['store_id']."_".$data['outlet_id']."_".$r['inventory_id']."_".$r['uom_id']]=array();
        }
        $remaining=$r['quantity'];
        foreach($data_stocks as $stock){
          if(!isset($temp[$data['store_id']."_".$data['outlet_id']."_".$r['inventory_id']."_".$r['uom_id']][$stock->price])){
            $temp[$data['store_id']."_".$data['outlet_id']."_".$r['inventory_id']."_".$r['uom_id']][$stock->price]=array(
              "store_id"=>$stock->store_id,
              "outlet_id"=>$stock->outlet_id,
              "inventory_id"=>$stock->inventory_id,
              "uom_id"=>$stock->uom_id,
              "price"=>$stock->price,
              "quantity"=>0,
              "convertion"=>$r['convertion']
            );
          }
          $data_stock_history = array(
            "store_id"=>$stock->store_id,
            "outlet_id"=>$stock->outlet_id,
            "quantity"=>$stock->quantity,
            "inventory_id"=>$stock->inventory_id,
            "uom_id"=>$stock->uom_id,
            "price"=>$stock->price,
            "status"=>6,
            "created_at"=>date("Y-m-d H:i:s"),
            "purchase_date"=>date("Y-m-d H:i:s"),
          );
          if($remaining > 0){
            
            if($stock->quantity >= $remaining){
              $temp[$data['store_id']."_".$data['outlet_id']."_".$r['inventory_id']."_".$r['uom_id']][$stock->price]['quantity']+=$remaining;
              $sum_quantity+=$remaining;
              $sum_total_price+=$remaining*$data_stock_history['price'];
              //UPDATE STOCK
              $this->stock_model->save("stock",array(
                "quantity"=>$stock->quantity - $remaining,     
              ),$stock->id);
              //INSERT STOCK HISTORY
              $data_stock_history['quantity'] = ($remaining*-1);
              $this->stock_model->insert_stock_history($data_stock_history);
              $remaining = 0;
            }else{
              $transfered = $stock->quantity;
              $temp[$data['store_id']."_".$data['outlet_id']."_".$r['inventory_id']."_".$r['uom_id']][$stock->price]['quantity']+=$transfered;
              $sum_quantity+=$transfered;
              $sum_total_price+=$transfered*$data_stock_history['price'];
              //UPDATE STOCK
              $this->stock_model->save("stock",array(
                "quantity"=>$stock->quantity - $transfered,     
              ),$stock->id);
              //INSERT STOCK HISTORY
              $data_stock_history['quantity'] = ($transfered*-1);
              $this->stock_model->insert_stock_history($data_stock_history);
              $remaining -= $transfered;
            }
          }else break;
        }
        //INSERT IF REMAINING > 0 (PROCESS QTY MORE THAN STOCK)
        if($remaining>0){
          $temp[$data['store_id']."_".$data['outlet_id']."_".$r['inventory_id']."_".$r['uom_id']][0]=array(
            'store_id' => $data['store_id'],
            'outlet_id' => $data['outlet_id'],
            'inventory_id' => $r['inventory_id'],
            'uom_id' => $r['uom_id'],
            'price'=>0,
            'quantity'=>$remaining,
            'convertion'=>0
          );
          $sum_quantity+=$remaining;
          $array = array(
            'store_id' => $data['store_id'],
            'outlet_id' => $data['outlet_id'],
            'inventory_id' => $r['inventory_id'],
            'uom_id' => $r['uom_id'],
            'quantity' => -1*$remaining,
            'created_at' =>$created_at,
            'purchase_date' =>$created_at,
            'price' => 0
          );
          $this->categories_model->save('stock', $array);
          $array = array(
            'store_id' => $data['store_id'],
            'outlet_id' => $data['outlet_id'],
            'inventory_id' => $r['inventory_id'],
            'uom_id' => $r['uom_id'],
            'quantity' => -1*$remaining,
            'created_at' =>$created_at,
            'purchase_date' =>$created_at,
            'price' =>0,
            'status'=>6
          );
          $this->categories_model->save('stock_history', $array);          
        }
      }

      $fifo_price = 0;
      $average_price = 0;
      $quantity = 0;
      //INSERT DESTINATION STOCK
      foreach($temp as $price=>$list){
        foreach($list as $l){
          $fifo_price=$l['price']*$l['convertion'];
          $average_price=($sum_total_price!=0 && $sum_quantity!=0 ? ($sum_total_price/$sum_quantity)*$l['convertion'] : 0);
          $quantity = ($is_convertion == 1) ? $l['quantity']*$r['convertion'] : $l['quantity']/$r['convertion'];
        }
      }

      if($data['quantity']!=0){
        $array = array(
          'store_id' => $data['store_id'],
          'outlet_id' => $data['outlet_id'],
          'inventory_id' => $data['inventory_id'],
          'uom_id' => $inventory->uom_id,
          'quantity' => ($is_convertion == 1) ? $quantity : $data['quantity'],
          'created_at' =>$created_at,
          'purchase_date' =>$created_at,
          'price' => ($this->data['setting']['stock_method']==$fifo_price ? "" : $average_price)
        );
        $this->categories_model->save('stock', $array);
        //INSERT DESTINATION STOCK HISTORY
        $array = array(
          'store_id' => $data['store_id'],
          'outlet_id' => $data['outlet_id'],
          'inventory_id' => $data['inventory_id'],
          'uom_id' => $inventory->uom_id,
          'quantity' => ($is_convertion == 1) ? $quantity : $data['quantity'],
          'purchase_date' =>$created_at,
          'price' => ($this->data['setting']['stock_method']==$fifo_price ? "" : $average_price),
          'status'=>6
        );
        $this->categories_model->save('stock_history', $array);
      }
      // if ($save === false) {
        // $this->session->set_flashdata('message', 'Gagal menyimpan data');
      // }else {
			$return=array(
				"status"=>true,
				"message"=>"Berhasil menyimpan data"
			);
			echo json_encode($return);
        // $this->session->set_flashdata('message_success', 'Berhasil menyimpan data');                  
      // }         
      // $btnaction = $this->input->post('btnAction');
      // if ($btnaction == 'save_exit') {
        // redirect(SITE_ADMIN . '/inventory_process', 'refresh');
      // }
      // else {
        // redirect(SITE_ADMIN . '/inventory_process/add/', 'refresh');
      // }
    }
    else {
      $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
      $this->data['message_success'] = $this->session->flashdata('message_success');
      $this->data['quantity']   = array(
        'name' => 'quantity',
        'id' => 'ip_quantity',
        'type' => 'text',
        'class' => 'form-control requiredTextField only_number',
        'field-name' => 'Jumlah Proses',
        'placeholder' => 'Masukan Jumlah Proses',
        'value' => $this->form_validation->set_value('quantity')
      );
      $this->data['store_lists']      = $this->categories_model->get_store();
      $this->data['outlets']     = $this->categories_model->get_outlet_dropdown();
      
      // $this->data['store_lists']=$this->store_model->get_store_dropdown();
      // unset($this->data['store_lists'][0]);
      // $this->data['outlets']=$this->categories_model->get_outlet();
      $this->data['inventories']=$this->inventory_model->get_inventory_process_drop_down();
      $this->data['content'] .= $this->load->view('admin/inventory-process-add', $this->data, true);
      $this->render('admin');
    }
  }
  // public function edit()
  // {
    // $id = $this->uri->segment(4);
    // if (empty($id))redirect(SITE_ADMIN . '/inventory_process');
    // $form_data = $this->inventory_model->get_one('inventory_process', $id);
    // if (empty($form_data))redirect(SITE_ADMIN . '/inventory_process');
    // $this->data['form_data'] = $form_data;
    // $this->data['subtitle']  = "Edit Proses";
    // $this->form_validation->set_rules('store_id', 'Resto', 'required');
    // $this->form_validation->set_rules('inventory_id', 'Inventori', 'required');
    // $this->form_validation->set_rules('quantity', 'Jumlah Proses', 'required');
    // if (isset($_POST) && ! empty($_POST)) {
      // if ($this->form_validation->run() === TRUE) {
        // $data = $this->input->post();
        // $save=$this->inventory_model->save('inventory_process',array(
          // 'store_id' => $data['store_id'],
          // 'inventory_id' => $data['inventory_id'],
          // 'quantity' => $data['quantity'],
        // ),$id);
        // if ($save === false) {
          // $this->session->set_flashdata('message', 'Gagal menyimpan data');
        // }else {
          // $this->session->set_flashdata('message_success', 'Berhasil menyimpan data');
        // }
        // $btnaction = $this->input->post('btnAction');
        // if ($btnaction == 'save_exit') {
          // redirect(SITE_ADMIN . '/inventory_process', 'refresh');
        // }else {
          // redirect(SITE_ADMIN . '/inventory_process/edit/' . $id, 'refresh');
        // }
      // }
    // }
    // $this->data['csrf'] = $this->_get_csrf_nonce();
    // $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
    // $this->data['message_success'] = $this->session->flashdata('message_success');
    // $this->data['cancel_url']      = base_url(SITE_ADMIN . '/inventory_process');        
    // $this->data['quantity']   = array(
      // 'name' => 'quantity',
      // 'id' => 'quantity',
      // 'type' => 'text',
      // 'class' => 'form-control requiredTextField only_number',
      // 'field-name' => 'Jumlah Konversi',
      // 'placeholder' => 'Masukan Jumlah Proses',
      // 'value' => $this->form_validation->set_value('quantity',$form_data->quantity)
    // );
    // $this->data['store_lists']=$this->store_model->get_store_dropdown();
    // unset($this->data['store_lists'][0]);
    // $this->data['inventories']=$this->inventory_model->get_inventory_process_drop_down();
    // $this->data['outlets']=$this->categories_model->get_outlet();
    // $this->data['content'] .= $this->load->view('admin/inventory-process-edit', $this->data, true);
    // $this->render('admin');
  // }

  public function delete()
  {
    $id = $this->uri->segment(4);
    if (empty($id))redirect(SITE_ADMIN . '/inventory_process');
    $form_data = $this->inventory_model->get_one('inventory_process', $id);
    if (empty($form_data))redirect(SITE_ADMIN . '/inventory_process');
    $created_at=date("Y-m-d H:i:s");
    $inventory=$this->inventory_model->get_one("inventory",$form_data->inventory_id);
    $array = array(
      'store_id' => $form_data->store_id,
      'outlet_id' => $form_data->outlet_id,
      'inventory_id' => $form_data->inventory_id,
      'uom_id' => $inventory->uom_id,
      'quantity' => -1*$form_data->quantity,
      'purchase_date' =>$created_at,
      'price' => 0
    );
    $this->categories_model->save('stock', $array);
    $array = array(
      'store_id' => $form_data->store_id,
      'outlet_id' => $form_data->outlet_id,
      'inventory_id' => $form_data->inventory_id,
      'uom_id' => $inventory->uom_id,
      'quantity' => -1*$form_data->quantity,
      'purchase_date' =>$created_at,
      'price' =>0,
      'status'=>3
    );
    $this->categories_model->save('stock_history', $array);
    $inventory_convertion=$this->inventory_model->get_inventory_convertion(array("inventory_id"=>$form_data->inventory_id));
    $results=array();
    $temp=array();
    foreach($inventory_convertion as $i){
      array_push($results,array(
        "inventory_id"=>$i->inventory_id,
        "inventory_name"=>$i->inventory_name,
        "uom_id"=>$i->uom_id,
        "code"=>$i->code,
        "quantity"=>1/$i->convertion*$form_data->quantity
      ));
      array_push($temp,$i->inventory_id."_".$i->uom_id);
    }
    $inventory_compositions=$this->inventory_model->get_inventory_composition(array("parent_inventory_id"=>$form_data->inventory_id));
    $results2=array();
    foreach($inventory_compositions as $i){
      if(!in_array($i->inventory_id."_".$i->uom_id,$temp))
      {
        array_push($results,array(
          "inventory_id"=>$i->inventory_id,
          "inventory_name"=>$i->inventory_name,
          "uom_id"=>$i->uom_id,
          "code"=>$i->code,
          "quantity"=>$i->quantity*$form_data->quantity
        ));        
      }else{
        $key=array_search($i->inventory_id."_".$i->uom_id,$temp);
        $results[$key]['quantity']+=$i->quantity*$form_data->quantity;
      }
    }
    
    
    foreach($results as $r){
      $array = array(
        'store_id' => $form_data->store_id,
        'outlet_id' => $form_data->outlet_id,
        'inventory_id' => $r['inventory_id'],
        'uom_id' => $r['uom_id'],
        'quantity' => $r['quantity'],
        'purchase_date' =>$created_at,
        'price' => 0
      );
      $this->categories_model->save('stock', $array);
      $array = array(
        'store_id' => $form_data->store_id,
        'outlet_id' => $form_data->outlet_id,
        'inventory_id' => $r['inventory_id'],
        'uom_id' => $r['uom_id'],
        'quantity' => $r['quantity'],
        'purchase_date' =>$created_at,
        'price' =>0,
        'status'=>3
      );
      $this->categories_model->save('stock_history', $array);
    }
    
    $result = $this->inventory_model->delete('inventory_process', $id);
    if ($result) {
      $this->session->set_flashdata('message_success', 'Berhasil menghapus data');
    }else {
      $this->session->set_flashdata('message', 'Error. Gagal menghapus data');
    }
    redirect(SITE_ADMIN . '/inventory_process', 'refresh');
  }
  function show_tree_convertion()
  {
    $inventory_id=$this->input->get("inventory_id");
    $quantity=$this->input->get("quantity");
    $content="";
    $inventory_convertion=$this->inventory_model->get_inventory_convertion(array("inventory_id"=>$inventory_id));
    $results=array();
    $temp=array();
    foreach($inventory_convertion as $i){
      array_push($results,array(
        "inventory_id"=>$i->inventory_id,
        "inventory_name"=>$i->inventory_name,
        "uom_id"=>$i->uom_id,
        "code"=>$i->code,
        "quantity"=>1/$i->convertion*$quantity
      ));
      array_push($temp,$i->inventory_id."_".$i->uom_id);
    }
    $inventory_compositions=$this->inventory_model->get_inventory_composition(array("parent_inventory_id"=>$inventory_id));
    $results2=array();
    foreach($inventory_compositions as $i){
      if(!in_array($i->inventory_id."_".$i->uom_id,$temp))
      {
        array_push($results,array(
          "inventory_id"=>$i->inventory_id,
          "inventory_name"=>$i->inventory_name,
          "uom_id"=>$i->uom_id,
          "code"=>$i->code,
          "quantity"=>$i->quantity*$quantity
        ));        
      }else{
        $key=array_search($i->inventory_id."_".$i->uom_id,$temp);
        $results[$key]['quantity']+=$i->quantity*$quantity;
      }
    }
    $this->data['results']=$results;
    $content=$this->load->view("admin/inventory-process-tree",$this->data,true);
    echo json_encode(array(
      "content" => $content
    ));
  }
}