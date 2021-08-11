<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Retur_Order extends Admin_Controller {

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

    public function __construct() {
        parent::__construct();
        $this->load->model('supplier_model');
        $this->load->model('account_data_model');
        $this->load->model('purchase_order_model');
        $this->load->model('purchase_order_receive_model');
        $this->load->model('purchase_order_receive_detail_model');
        $this->load->model('purchase_order_retur_model');
        $this->load->model('purchase_order_retur_detail_model');
        $this->load->model('purchase_order_detail_model');
        $this->load->model('categories_model');
        $this->load->model('store_model');
        $this->load->model('inventory_model');
        $this->load->model('stock_model');

        $this->_store_data = $this->ion_auth->user()->row();
        $this->_setting = $this->data['setting'];
    }

    public function index() {
        $this->listing();
    }

    /**
     * Display list of transfer requests in which the currently logged in store have a role as requester
     * @return void
     */
    public function listing() {
        $this->data['title'] = "Pengembalian Barang";
        $this->data['subtitle'] = "Pengembalian Barang";

        $this->data['supplier_lists'] = $this->store_model->get("supplier")->result();
        $supplier_id = $this->input->post("supplier_id");
        $start_date = date("Y-m-d H:m:s");
        $end_date = date("Y-m-d H:m:s");
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['supplier_id'] = $supplier_id;

        $this->data['content'] .= $this->load->view('admin/retur-order-list', $this->data, true);

        $this->render('admin');
    }

    public function get_received_po_data() {
    	$this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));
        $where = array();
        if ($_POST) {
            $data = $this->input->post();
            if ($data['columns'][1]['search']['value'] != "") {
                $where = array_merge($where, array("s.id" => $data['columns'][1]['search']['value']));
            }
            if ($data['columns'][2]['search']['value'] != "") {
                $where = array_merge($where, array("date(por.incoming_date) >= " => $data['columns'][2]['search']['value']));
            }
            if ($data['columns'][3]['search']['value'] != "") {
                $where = array_merge($where, array("date(por.incoming_date) <= " => $data['columns'][3]['search']['value']));
            }
        }
        $this->datatables->select('por.id, por.purchase_order_id, po.number, por.incoming_date, s.name, SUM(pord.received_quantity) AS quantity')
            ->from('purchase_order_receive_detail pord')
            ->join('purchase_order_receive por', 'por.id = pord.purchase_order_receive_id')
            ->join('purchase_order_detail pod', 'pod.id = pord.purchase_order_detail_id')
            ->join('purchase_order po', 'po.id = pod.purchase_order_id')
            ->join('supplier s', 's.id = po.supplier_id');
        foreach ($where as $key => $value) {
            $this->datatables->where($key, $value);
        }
        $this->datatables->group_by('po.number')
            ->group_by('por.incoming_date')
            ->add_column('actions',"$1",'generate_action_for_retur_po(id, quantity, purchase_order_id)');
        echo $this->datatables->generate();
    }

    public function retur($purchase_order_receive_id = -1) {
        $purchase_order_receive = $this->purchase_order_receive_model->get(array('id' => $purchase_order_receive_id));
        if (count($purchase_order_receive) < 1) {
            $this->session->set_flashdata('message', 'Tidak ada purchase order yang sudah diterima dengan id tersebut');
            redirect(SITE_ADMIN . '/retur_order/');
        }

        $receive = $purchase_order_receive[0];
        $purchase_order = $this->purchase_order_model->get(array('purchase_order.id' => $receive->purchase_order_id));
        $purchase = $purchase_order[0];
        $supplier = array_pop($this->supplier_model->get(array('id' => $purchase->supplier_id)));

        $sub_total = 0;
        $total = 0;
        $price = array();

        if ($this->input->post()) {
            $this->form_validation->set_rules('retured_date', 'Tanggal Retur', 'required|xss_clean');
            if ($this->form_validation->run() == true) {
                $price = $this->input->post('price');
                $retured = $this->input->post('retured');
                $notes = $this->input->post('notes');
                $received = $this->input->post('received_quantity');

                $max_id = $this->purchase_order_retur_model->get_max_retur_number();
                $maxDay = substr($max_id, 3, 8);
                $current_number = substr($max_id, 11, 5);
                $today = date('Ymd');

                if ($maxDay != $today) {
                    $new_retur_no = "RO-" . $today . '0001';
                } else {
                    $new_retur_no = "RO-" . $maxDay . str_pad($current_number + 1, 4, 0, STR_PAD_LEFT);
                }

                $mega_status = true;
                $retur = array(
                    'store_id' => $this->_setting['store_id'],
                    'purchase_order_id' => $receive->purchase_order_id,
                    'retur_date' => $this->input->post('retured_date'),
                    'total' => $this->input->post('total'),
                    'number' => $new_retur_no
                );                

                $retur_id = $this->purchase_order_retur_model->add($retur);

                foreach ($retured as $key => $amount) {
                    if ($amount > 0 && $amount <= $received[$key]) {
                        $retur_detail = array(
                            'store_id' => $this->_setting['store_id'],
                            'retur_quantity' => $amount,
                            'price' => $price[$key],
                            'purchase_order_retur_id' => $retur_id,
                            'notes' => $notes[$key],
                            'purchase_order_receive_detail_id' => $key
                        );
                        $temp_status = $this->purchase_order_retur_detail_model->add($retur_detail);
                        if ($temp_status == false) $mega_status = false;
                    } else {
                        $this->purchase_order_retur_model->delete(array('id' => $retur_id));
                        $this->session->set_flashdata('message', 'Pengembalian barang gagal ditambahkan, jumlah pengembalian melebihi jumlah datang');
                        redirect(SITE_ADMIN . '/retur_order/retur/' . $purchase_order_receive_id);
                    }
                }

                $detail_retur = $this->purchase_order_retur_detail_model->get(array('purchase_order_retur_id' => $retur_id));
                foreach ($detail_retur as $data) {
                    $data_outlet = array_pop($this->inventory_model->get_all_where('stock_history', array('status' => 3, 'inventory_id' => $data->inventory_id), 1));                    

                    // check stock_method FIFO or AVERAGE
                    if ($this->data['setting']['stock_method'] == 'FIFO') {
                        // UPDATE STOCK
                        $get_stock = array_pop($this->stock_model->get_all_where('stock', array('outlet_id' => $data_outlet->outlet_id, 'inventory_id' => $data->inventory_id, 'purchase_date' => $receive->incoming_date)));
                        $this->stock_model->save('stock', array('quantity' => $get_stock->quantity - $data->retur_quantity), $get_stock->id);
                    }

                    if ($this->data['setting']['stock_method'] == 'AVERAGE') {
                        // UPDATE STOCK
                        $get_stock = array_pop($this->stock_model->get_all_where('stock', array('outlet_id' => $data_outlet->outlet_id, 'inventory_id' => $data->inventory_id)));
                        $price_before = $get_stock->quantity * $get_stock->price;
                        $cost_retur = $data->retur_quantity * $data->price;
                        $qty_after_retur = $get_stock->quantity - $data->retur_quantity;
                        $avg_price_after = round((($price_before - $cost_retur) / $qty_after_retur), 0);

                        $this->stock_model->save('stock', array('quantity' => $qty_after_retur, 'price' => $avg_price_after), $get_stock->id);
                    }

                    // INSERT STOCK HISTORY
                    $array = array(
                        'store_id' => $this->_setting['store_id'],
                        'outlet_id' => $data_outlet->outlet_id,
                        'inventory_id' => $data->inventory_id,
                        'quantity' => $data->retur_quantity * -1,
                        'price' => $data->price,
                        'status' => 9,
                        'created_at' => $this->input->post('retured_date'),
                        'purchase_date' => $purchase->order_at,
                        'uom_id' => $data->uom_id
                    );
                    $save = $this->inventory_model->save('stock_history', $array);
                }

                if ($this->data['module']['ACCOUNTING'] == 1) {
                    $param_journal = array(
                        'status' => 'retur',
                        'retur_id' => $retur_id,
                        'mega_status' => $mega_status,
                        'supplier_acc' => $supplier->account_payable_id
                    );
                    $this->set_journal($param_journal);
                }

                if ($mega_status == true)
                    $this->session->set_flashdata('message_success', 'Pengembalian barang berhasil ditambahkan');
                else
                    $this->session->set_flashdata('message', 'Pengembalian barang gagal ditambahkan');
                redirect(SITE_ADMIN . '/retur_order/', 'refresh');
            } else {
                $price = $this->input->post('price');
                $received = $this->input->post('retured');
                $total = $this->input->post('total');
                $received = $this->input->post('received_quantity');
            }
        }

        $detail = $this->purchase_order_retur_detail_model->get_received(array('pord.purchase_order_receive_id' => $purchase_order_receive_id));        
        $this->data['title'] = "Pengembalian Barang";
        $this->data['subtitle'] = "Pengembalian Barang";
        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');
        $this->data['price'] = $price;
        $this->data['sub_total'] = $sub_total;
        $this->data['total'] = $total;
        $this->data['detail'] = $detail;
        $this->data['purchase_order'] = array_pop($purchase_order);
        $this->data['purchase_order_receive'] = array_pop($purchase_order_receive);
        $this->data['content'] .= $this->load->view('admin/retur-order-retur', $this->data, true);

        $this->render('admin');
    }

    public function history($purchase_order_id = -1) {
        $this->data['title'] = "Pengembalian Barang";
        $this->data['subtitle'] = "Riwayat Pengembalian Barang";

        $purchase_order = $this->purchase_order_model->get(array('purchase_order.id' => $purchase_order_id));
        $pengembalian = $this->purchase_order_retur_model->get(array('purchase_order_id' => $purchase_order_id));

        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['purchase_order'] = array_pop($purchase_order);
        $this->data['detail_retur'] = $pengembalian;
        $this->data['content'] .= $this->load->view('admin/retur-stocks-history', $this->data, true);

        $this->render('admin');
    }

    public function set_journal($param = array()) {
        $mega_status = $param['mega_status'];
        if ($param['status'] == 'retur') {
            $inventory_id = $this->input->post('inventory_id');
            $sub_retur = $this->input->post('sub_total');
            $i = 0;
            $info = '';
            if ($sub_retur[$i] > 0) {
                foreach ($inventory_id as $inventory) {
                    $acc = array_pop($this->inventory_model->get_inventory_account($inventory));
                    if (!empty($acc)) {
                        $inventory = $this->inventory_model->get_one('inventory', $acc->inventory_id, TRUE);
                        $info = 'Retur '.$inventory['name'];
                    }
                    $debit = array(
                        'store_id' => $this->_setting['store_id'],
                        'entry_type' => 8,
                        'foreign_id' => $param['retur_id'],
                        'debit' => 0,
                        'credit' => $sub_retur[$i],
                        'info' => $info,
                        'account_id' => (!empty($acc)) ? $acc->account_id : 0
                    );
                    $temp_status = $this->account_data_model->add($debit);
                    if ($temp_status == false) $mega_status = false;
                    $i++;
                }
            }                

            $kredit = array(
                'store_id' => $this->_setting['store_id'],
                'entry_type' => 8,
                'foreign_id' => $param['retur_id'],
                'debit' => $this->input->post('total'),
                'credit' => 0,
                'info' => 'Total Retur',
                'account_id' => $this->data['setting']['cash_account_id']
            );
            $temp_status = $this->account_data_model->add($kredit);
            if ($temp_status == false) $mega_status = false;
        }
    }

    public function prints($purchase_order_id = -1, $purchase_order_retur_id = -1)
    {
        $this->load->helper("printer_helper");
        $detail = $this->purchase_order_retur_detail_model->get(array('purchase_order_retur_id' => $purchase_order_retur_id));
        if (count($detail) < 1) {
            $this->session->set_flashdata('message', 'Tidak ada detail untuk penerimaan barang tersebut');
            redirect(SITE_ADMIN . '/retur_order/listing');
        }
        $purchase_order = $this->purchase_order_model->get(array('purchase_order.id' => $purchase_order_id));
        $retur = $this->purchase_order_retur_model->get(array('id' => $purchase_order_retur_id));

        $this->data['purchase_order'] = array_pop($purchase_order);
        $this->data['detail'] = $detail;

        $this->data['retur'] = array_pop($retur);
        
        //get printer PO
        $this->load->model("setting_printer_model");
        $printer_arr_obj = $this->setting_printer_model->get_printer_by_enum_printer_type(array("name_type"=>"printer_matrix_po"));
        
        foreach ($printer_arr_obj as $printer_obj) {
            $printer_location = $printer_obj->name_printer;
            print_retur_po($printer_location, $this->data);
        }
        
        redirect(SITE_ADMIN . '/retur_order/history/'.$purchase_order_id);
    }
}