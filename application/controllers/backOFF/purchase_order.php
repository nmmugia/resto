<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      Diky Pratansyah <pratansyah@gmail.com>
 * @copyright   2015 Digital Oasis
 * @since       2.0.0
 */

class Purchase_Order extends Admin_Controller{

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

	public function __construct()
	{
		parent::__construct();

        $this->load->model('stock_request_detail_model');
        $this->load->model('stock_transfer_model');
        $this->load->model('purchase_order_model');
        $this->load->model('purchase_order_receive_model');
        $this->load->model('purchase_order_receive_detail_model');
        $this->load->model('purchase_order_detail_model');
        $this->load->model('supplier_model');
        $this->load->model('inventory_model');
        $this->_store_data = $this->ion_auth->user()->row();
      $this->_setting = $this->data['setting'];
	}

    public function po_list()
    {
        $this->data['title']    = "Purchase Order";
        $this->data['subtitle'] = "Purchase Order";

        // $purchase_order = $this->purchase_order_model->get();

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');
        // $this->data['purchase_order'] = $purchase_order;
        $this->data['supplier_lists']=$this->store_model->get("supplier")->result();
        $this->data['content'] .= $this->load->view('admin/purchase-order-list', $this->data, true);
        $this->render('admin');
    }
    public function get_po_data()
    {
      $this->load->library(array('datatables'));
      $this->load->helper(array('datatables'));
      $where=array();
      if($_POST){
        $data=$this->input->post();
        if($data['columns'][1]['search']['value']!=""){
          $where=array_merge($where,array("date(po.order_at)"=>$data['columns'][1]['search']['value']));
        }
        if($data['columns'][2]['search']['value']!=""){
          $where=array_merge($where,array("s.id"=>$data['columns'][2]['search']['value']));
        }
      }
      $this->datatables->select('po.id,po.number,po.order_at,po.description,s.name as supplier_name,sum(por.total-por.discount) as total_po')
      ->from('purchase_order po')
      ->join('supplier s', 's.id = po.supplier_id')
      ->join('purchase_order_receive por', 'po.id = por.purchase_order_id','left');
      foreach($where as $key=>$value){
        $this->datatables->where($key,$value);
      }
      $this->datatables->group_by("po.id")
      ->add_column('actions',"$1",'generate_action_for_po(id,status)','id,status');
      echo $this->datatables->generate();
    }
    public function add()
    {
        $this->data['title']    = "Purchase Order";
        $this->data['subtitle'] = "Tambah Purchase Order";

        $cond = array(
            'supplier_store_id'       => $this->_setting['store_id'],
            'supplier_status'   => 2,
            'requester_status'  => 1
        );

        // Add validation. If there's no request, can't add purchase order
        $stock_request = $this->stock_transfer_model->get_transfer_request($cond);
        if(!$stock_request){
            // redirect(SITE_ADMIN.'/purchase_order/po_create');
            $this->session->set_flashdata('message', 'Tidak dapat melakukan Purchase Order. Tidak ada request stock.');
            redirect(SITE_ADMIN.'/purchase_order/po_list');
        }

        if($this->input->post()){
            $this->form_validation->set_rules('order_date', 'Tanggal Order', 'required|xss_clean');
            $this->form_validation->set_rules('supplier', 'Supplier', 'required|xss_clean|min_length[1]|max_length[50]|is_natural_no_zero');
            // $this->form_validation->set_rules('po_number', 'Nomor Purchase Order', 'required|xss_clean|numeric|min_length[7]|max_length[15]');
            $this->form_validation->set_rules('description', 'Keterangan', 'xss_clean');
            if($this->form_validation->run()){
                $max_id = $this->purchase_order_model->get_max_po_number();
                $maxDay = substr($max_id, 3, 8);
                $current_number = substr($max_id, 11, 4);
                 $today  = date('Ymd');
                if ($maxDay != $today) {
                    $new_po_number = "PO-".$today . '0001';
                }
                else {
                    $new_po_number = "PO-".$maxDay.str_pad($current_number + 1, 4, 0, STR_PAD_LEFT); 
                } 
                $order = array(
                    'order_at'      => $this->input->post('order_date'),
                    'store_id'      => $this->_setting['store_id'],
                    'supplier_id'   => $this->input->post('supplier'),
                    'number'        => $new_po_number,
                    'description'   => $this->input->post('description'),
                    'created_by'    => $this->_store_data->user_id,
                    'status'        => 0
                );
                $order_id = $this->purchase_order_model->add($order);
                $sum = $this->input->post('sum');
                $check = $this->input->post('check');
                $uom = $this->input->post('uom');
                $order_details = array();
                $status = false;

                if($check){
                    foreach ($check as $key => $value) {
                        $order_detail = array(
                            'purchase_order_id' => $order_id,
                            'store_id'          => $this->_setting['store_id'],
                            'inventory_id'      => $key,
                            'quantity'          => $sum[$key],
                            'created_by'        => $this->_store_data->user_id,
                            'uom_id'        => $uom[$key],
                        );
                        array_push($order_details, $order_detail);
                    }
                    $status = $this->purchase_order_detail_model->add($order_details);
                }

                if($status) {
                    $this->session->set_flashdata('message_success', 'Purchase order berhasil ditambahkan');
                    redirect(SITE_ADMIN.'/purchase_order/po_list');
                }
            }
        }

        $purchase_order = $this->purchase_order_model->get();
        $suppliers      = $this->supplier_model->get();
        $stock_request = $this->stock_transfer_model->get_transfer_request($cond);
        $items = array();
        foreach ($stock_request as $request) {
            $detail = $this->stock_request_detail_model->get_items(array('stock_request_id' => $request->id));
            $detail = array_map(function($elem) use($request){
                $elem->requester_name = $request->requester_name;
                return $elem;
            }, $detail);
            $items = array_merge($items, $detail);
        }

        $array_id = array();
        $recaps = array();
        foreach ($items as $item) {
            if(!in_array($item->inventory_id, $array_id)){
                $recaps[$item->inventory_id]['name'] = $item->name;
                $recaps[$item->inventory_id]['sum']  = $item->request_quantity;
                array_push($array_id, $item->inventory_id);
            }
            else{
                $recaps[$item->inventory_id]['sum'] += $item->request_quantity;
            }
        }


        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['purchase_order']  = $purchase_order;
        $this->data['suppliers']       = $suppliers;
        $this->data['items']           = $items;
        $this->data['recaps']          = $recaps;
        $this->data['content']        .= $this->load->view('admin/purchase-order-add', $this->data, true);

        $this->render('admin');
    }

    public function edit($order_id=-1)
    {
        if($order_id < 1) {
            $this->session->set_flashdata('message', 'Tidak ada detail untuk purchase order tersebut');
            redirect(SITE_ADMIN.'/purchase_order/po_list');
        }
        $this->data['title']    = "Purchase Order";
        $this->data['subtitle'] = "Ubah Purchase Order";

        if($this->input->post()){
            $this->form_validation->set_rules('order_date', 'Tanggal Order', 'required|xss_clean');
            $this->form_validation->set_rules('supplier', 'Supplier', 'required|xss_clean|min_length[1]|max_length[50]|is_natural_no_zero');
            $this->form_validation->set_rules('po_number', 'Nomor Purchase Order', 'required|xss_clean|min_length[7]|max_length[15]');
            $this->form_validation->set_rules('description', 'Keterangan', 'xss_clean');
            if($this->form_validation->run()){
                $update = array(
                    'order_at'      => $this->input->post('order_date'),
                    'supplier_id'   => $this->input->post('supplier'),
                    'number'        => $this->input->post('po_number'),
                    'description'   => $this->input->post('description'),
                    'modified_by'   => $this->_store_data->user_id,
                    'modified_at'   => date('Y-m-d H:i'),
                    'status'        => 0
                );
                $status_order = $this->purchase_order_model->update($update, array('id' => $order_id));
                $quantities = $this->input->post('quantity');
                foreach ($quantities as $id => $quantity) {
                    $update = array(
                        'quantity'      => $quantity
                    );

                    $cond = array(
                        'id' => $id
                    );
                    $detail_status = $this->purchase_order_detail_model->update($update, $cond);
                }
                if($status_order){
                    $this->session->set_flashdata('message_success', 'Purchase order berhasil diubah');
                    redirect(SITE_ADMIN.'/purchase_order/po_list');
                }
            }

        }

        $purchase_order = array_pop($this->purchase_order_model->get(array('purchase_order.id' => $order_id)));
        $details        = $this->purchase_order_detail_model->get(array('purchase_order_id' => $purchase_order->id));
        foreach ($details as $detail) {
            $detail->item = array_pop($this->purchase_order_model->get_all_where('inventory', array('id' => $detail->inventory_id)));
            $uom=$this->inventory_model->get_one("uoms",$detail->uom_id);
            $detail->uom = $uom;
        }
        $suppliers      = $this->supplier_model->get();

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['purchase_order'] = $purchase_order;
        $this->data['suppliers']      = $suppliers;
        $this->data['details']        = $details;
        $this->data['content'] .= $this->load->view('admin/purchase-order-edit', $this->data, true);

        $this->render('admin');
    }

    public function delete($order_id=-1)
    {
        $this->purchase_order_detail_model->delete_by_limit("purchase_order_detail",array('purchase_order_id' => $order_id),0);
        $status = $this->purchase_order_model->delete(array('id' => $order_id));
        if($status) $this->session->set_flashdata('message_success', 'Purchase order berhasil dihapus');
        else $this->session->set_flashdata('message', 'Tidak bisa menghapus purchase order');

        redirect(SITE_ADMIN.'/purchase_order/po_list', 'refresh');
    }

    /**
     * History receiving goods
     * @param  integer $purchase_order_id Id of purchase order
     * @return void
     */             
    public function history($purchase_order_id=-1)
    {
        $this->data['title']    = "Penerimaan Barang";
        $this->data['subtitle'] = "Detail Purchase Order";

        $purchase_order = $this->purchase_order_model->get(array('purchase_order.id' => $purchase_order_id));
        $detail = $this->purchase_order_receive_model->get(array('purchase_order_id' => $purchase_order_id));
        $detail_po = $this->purchase_order_detail_model->get(array('purchase_order_detail.purchase_order_id' => $purchase_order_id));

        $this->data['message']          = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success']  = $this->session->flashdata('message_success');

        $this->data['purchase_order']   = array_pop($purchase_order);
        $this->data['detail']           = $detail;
        $this->data['detail_po']           = $detail_po;
        $this->data['receive']          = array_pop($detail);
        $this->data['content']         .= $this->load->view('admin/purchase-order-history', $this->data, true);

        $this->render('admin');
    }

    /**
     * Display detail page for each receive stocks
     * @param  integer $purchase_order_id           id of purchase order
     * @param  integer $purchase_order_receive_id   id of receive stocks
     * @return void
     */
    public function detail($purchase_order_id=-1, $purchase_order_receive_id=-1)
    {
        $detail = $this->purchase_order_receive_detail_model->get(array('purchase_order_receive_id' => $purchase_order_receive_id));
        if(count($detail) < 1) {
            $this->session->set_flashdata('message', 'Tidak ada detail untuk penerimaan barang tersebut');
            redirect(SITE_ADMIN.'/receive_stocks/listing');
        }

        $this->data['title']    = "Penerimaan Barang";
        $this->data['subtitle'] = "Detail Penerimaan Barang";

        $this->data['message']          = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success']  = $this->session->flashdata('message_success');

        $purchase_order = $this->purchase_order_model->get(array('purchase_order.id' => $purchase_order_id));
        $receive        = $this->purchase_order_receive_model->get(array('id' => $purchase_order_receive_id));

        $this->data['purchase_order']   = array_pop($purchase_order);
        $this->data['detail']           = $detail;
        $this->data['receive']          = array_pop($receive);
        $this->data['content']         .= $this->load->view('admin/purchase-order-detail', $this->data, true);

        $this->render('admin');
    }
    public function po_create()
    {
      $this->data['title']    = "Purchase Order";
      $this->data['subtitle'] = "Tambah Purchase Order";
      if($this->input->post()){
        $this->form_validation->set_rules('order_date', 'Tanggal Order', 'required|xss_clean');
        $this->form_validation->set_rules('detail[qty][]', 'Jumlah', 'required|xss_clean|greater_than[0]');
        $this->form_validation->set_rules('detail[uom_id][]', 'Satuan', 'required|xss_clean');
        $this->form_validation->set_rules('supplier', 'Supplier', 'required|xss_clean|min_length[1]|max_length[50]|is_natural_no_zero');
        $this->form_validation->set_rules('description', 'Keterangan', 'xss_clean');
        if($this->form_validation->run()){
          $detail=$this->input->post("detail");
          $max_id = $this->purchase_order_model->get_max_po_number();
          $maxDay = substr($max_id, 3, 8);
          $current_number = substr($max_id, 11, 4);
          $today  = date('Ymd');
          if ($maxDay != $today) {
            $new_po_number = "PO-".$today . '0001';
          }
          else {
            $new_po_number = "PO-".$maxDay.str_pad($current_number + 1, 4, 0, STR_PAD_LEFT); 
          } 

       
          $order = array(
            'order_at'      => $this->input->post('order_date'),
            'store_id'      => $this->_setting['store_id'],
            'supplier_id'   => $this->input->post('supplier'),
            'number'        => $new_po_number,
            'description'   => $this->input->post('description'),
            'created_by'    => $this->_store_data->user_id,
            'status'        => 0
          );
          $order_id = $this->purchase_order_model->add($order);
          $check = $this->input->post('check');
          $order_details = array();
          $status = false;
          for($x=0;$x<sizeof($detail['id']);$x++) {
            $order_detail = array(
              'purchase_order_id' => $order_id,
              'store_id'          => $this->_setting['store_id'],
              'inventory_id'      => $detail['id'][$x],
              'quantity'          => $detail['qty'][$x],
              'created_by'        => $this->_store_data->user_id,
              'uom_id'            => $detail['uom_id'][$x]
            );
            array_push($order_details, $order_detail);
          }
          $status = $this->purchase_order_detail_model->add($order_details);
          if($status) {
            $this->session->set_flashdata('message_success', 'Purchase order berhasil ditambahkan');
            redirect(SITE_ADMIN.'/purchase_order/po_list');
          }
        }
      }
      $suppliers                     = $this->supplier_model->get();
      $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
      $this->data['message_success'] = $this->session->flashdata('message_success');
      $this->data['suppliers']       = $suppliers;
      $this->data['inventories']     = $this->inventory_model->get_all_inventories();
      $this->data['add_po_create']=$this->load->view("admin/purchase-order-add-po-create",$this->data,true);
      $this->data['content']        .= $this->load->view('admin/purchase-order-po-create', $this->data, true);
      $this->render('admin');
    }
    function add_po_create(){
      $this->data['inventories']     = $this->inventory_model->get_all_inventories();
      $content=$this->load->view("admin/purchase-order-add-po-create",$this->data,true);
      echo json_encode(array(
        "content" => $content
      ));
    }
}