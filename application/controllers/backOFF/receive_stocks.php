<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      Diky Pratansyah <pratansyah@gmail.com>
 * @copyright   2015 Digital Oasis
 * @since       2.0.0
 */
class Receive_Stocks extends Admin_Controller
{

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

    // public $group_id = 1;

    public function __construct()
    {
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

        $this->_store_data = $this->ion_auth->user()->row();
        $this->_setting = $this->data['setting'];
    }

    /**
     * Display list of transfer requests in which the currently logged in store have a role as requester
     * @return void
     */
    public function listing()
    {
        $this->data['title'] = "Penerimaan Barang";
        $this->data['subtitle'] = "Penerimaan Barang";

        // $purchase_order = $this->purchase_order_model->get();

        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        // $this->data['purchase_order'] = $purchase_order;
        $this->data['supplier_lists'] = $this->store_model->get("supplier")->result();
        $this->data['content'] .= $this->load->view('admin/receive-stocks-list', $this->data, true);

        $this->render('admin');
    }

    public function get_receive_po_data()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));
        $where = array();
        if ($_POST) {
            $data = $this->input->post();
            if ($data['columns'][1]['search']['value'] != "") {
                $where = array_merge($where, array("date(po.order_at)" => $data['columns'][1]['search']['value']));
            }
            if ($data['columns'][2]['search']['value'] != "") {
                $where = array_merge($where, array("s.id" => $data['columns'][2]['search']['value']));
            }
        }
        $this->datatables->select('po.id,po.status,po.number,date(po.order_at) as order_at,po.description,s.name as supplier_name,sum(por.total-por.discount) as total_po')
            ->from('purchase_order po')
            ->join('supplier s', 's.id = po.supplier_id')
            ->join('purchase_order_receive por', 'po.id = por.purchase_order_id', 'left');
        foreach ($where as $key => $value) {
            $this->datatables->where($key, $value);
        }
        $this->datatables->group_by("po.id")
            ->unset_column('total_po')
            ->add_column('total_po', "$1", 'convert_rupiah(total_po)', 'total_po')
            ->add_column('actions', "$1", 'generate_action_for_receive_po(id,status)', 'id,status')
            ->unset_column('status')
            ->add_column('status', '$1', 'check_status_po(status)', 'status');
        echo $this->datatables->generate();
    }

    public function report_listing()
    {
        $this->data['title'] = "Laporan Penerimaan Barang";
        $this->data['subtitle'] = "Laporan Penerimaan Barang";

        $purchase_order = $this->purchase_order_model->get();

        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->load->model('inventory_model');

        $this->data['suppliers'] = $this->supplier_model->get();
        $this->data['inventories'] = $this->inventory_model->get_all_inventories();
        $this->data['data_url'] = base_url(SITE_ADMIN . '/receive_stocks/get_history_receive');
        $this->data['purchase_order'] = $purchase_order;
        $this->data['content'] .= $this->load->view('admin/report-receive-stocks-list', $this->data, true);

        $this->render('admin');
    }

    public function get_history_receive()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $post_array = array();
        parse_str($this->input->post('param'), $post_array);

        $inventory_id = $post_array['inventory_id'];
        $supplier_id = $post_array['supplier_id'];
        $start_date = $post_array['start_date'];
        $end_date = $post_array['end_date'];

        $this->datatables->select("por.id,
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
                                 por.received_quantity", false)
            ->from('purchase_order_receive a')
            ->join("purchase_order_receive_detail por", "a.id = por.purchase_order_receive_id")
            ->join("purchase_order po", "  po.id = a.purchase_order_id")
            ->join("supplier s", "s.id = po.supplier_id")
            ->join("purchase_order_detail pod", "  pod.id = por.purchase_order_detail_id")
            ->join("inventory i ", " i.id = pod.inventory_id");

        if ($start_date) {
            $this->datatables->where('date(a.incoming_date) >=', $start_date);
        }

        if ($end_date) {
            $this->datatables->where('date(a.incoming_date) <=', $end_date);
        }

        if ($inventory_id) {
            $this->datatables->where('pod.inventory_id ', $inventory_id);
        }
        echo $this->datatables->generate();
    }

    public function export_report_to_pdf()
    {
        $this->load->helper(array('datatables'));
        $this->load->model('order_model');
        $this->load->helper(array('dompdf', 'file'));
        // page info here, db calls, etc.

        $supplier_id = $this->input->post('supplier_id');
        $inventory_id = $this->input->post('inventory_id');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        $data['is_print'] = TRUE;
        $pdf_orientation = "landscape";
        $post_array = array();
        $post_array["supplier_name"] = "All";
        $post_array["inventory_name"] = "All";
        if ($supplier_id) {
            $post_array["supplier_id"] = $supplier_id;
            $data_supplier = $this->order_model->get_one('supplier', $supplier_id);
            $post_array["supplier_name"] = $data_supplier->name;
        }

        if ($inventory_id) {
            $post_array["inventory_id"] = $inventory_id;
            $data_inventory = $this->order_model->get_one('inventory', $inventory_id);
            $post_array["inventory_name"] = $data_inventory->name;
        }
        $post_array["start_date"] = $start_date;
        $post_array["end_date"] = $end_date;
        $this->load->model('inventory_model');


        $data['all_history'] = $this->inventory_model->get_history_receive_data($post_array);
        $data['detail'] = $post_array;
        $report = 'laporan_penerimaan_barang';
        $html = $this->load->view('admin/report/report_receive_stock_to_pdf_v', $data, true);


        $date = new DateTime();
        $data = pdf_create($html, '', false, $pdf_orientation);

        $filename = 'report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis') . '.pdf';
        $filename = 'assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis') . '.pdf';
        write_file($filename, $data);
        echo json_encode($filename);
    }

    /**
     * Receive goods that were ordered from purchase order menu
     * @param  integer $purchase_order_id Id of purchase order
     * @return void
     */
    public function receive($purchase_order_id = -1)
    {
        $purchase_order = $this->purchase_order_model->get(array('purchase_order.id' => $purchase_order_id));
        if (count($purchase_order) < 1) {
            $this->session->set_flashdata('message', 'Tidak ada purchase order dengan id tersebut');
            redirect(SITE_ADMIN . '/receive_stocks/listing');
        }
        $purchase = $purchase_order[0];
        $supplier = array_pop($this->supplier_model->get(array('id' => $purchase->supplier_id)));
        $price = array();
        $received = array();
        $retured = array();
        $total = 0;
        $discount = 0;
        $sub_total = 0;
        $last_id_stock = array();
        $qty = 0;
        if ($this->input->post()) {
            $this->form_validation->set_rules('received_date', 'Tanggal Datang', 'required|xss_clean');
            $this->form_validation->set_rules('price[]', 'Harga Satuan', 'required|numeric');
            $this->form_validation->set_rules('received[]', 'Jumlah Diterima', 'required|numeric');
            $this->form_validation->set_rules('method', 'Metode Pembayaran', 'required');
            if ($this->input->post('method') == 'bon')
                $this->form_validation->set_rules('bon_date', 'Tanggal Kontra Bon', 'required|xss_clean');
            $this->form_validation->set_rules('status', 'Status Order', 'required');
            // $this->form_validation->set_rules('invoice_logo', 'Logo Invoice', 'required');
            // $this->form_validation->set_rules('payment_number', 'Nomor Pembayaran', 'required');
            // $this->form_validation->set_rules('received_date', 'Tanggal Datang', 'required|xss_clean');

            if ($this->form_validation->run() == true) {
                $image_name = $purchase->invoice_logo;
                $isUpload = TRUE;
                if (!empty($_FILES['invoice_logo']['name'])) {
                    //upload config
                    $newname = $this->generate_random_name();
                    $config['upload_path'] = './uploads/invoice_logo/';
                    $config['allowed_types'] = '*';
                    $config['max_size'] = '1000';
                    $config['overwrite'] = FALSE;
                    $config['file_name'] = $newname;
                    $this->load->library('upload', $config);

                    if (!$this->upload->do_upload('invoice_logo')) {
                        $this->session->set_flashdata('message', $this->upload->display_errors());
                        $isUpload = FALSE;
                    } else {
                        $this->load->library('image_moo');
                        $this->image_moo->load($this->upload->data()['full_path'])->set_background_colour("#FFFFFF")->stretch(150, 81);
                        $image_name = 'uploads/invoice_logo/' . $this->upload->data()['file_name'];
                        $isUpload = TRUE;
                        if (!empty($form_data['invoice_logo'])) {
                            $url = './' . $form_data['invoice_logo'];
                            if (file_exists($url)) {
                                unlink($url);
                            }
                        }
                        // $data_array = array('value' => $image_name);
                        // $this->categories_model->save_by('master_general_setting', $data_array, "invoice_logo", 'name');
                    }
                }

                $max_id = $this->purchase_order_model->get_max_payment_number();
                $maxDay = substr($max_id, 4, 8);
                $current_number = substr($max_id, 12, 5);
                $today = date('Ymd');

                if ($maxDay != $today) {
                    $new_payment_no = "PNO-" . $today . '0001';
                } else {
                    $new_payment_no = "PNO-" . $maxDay . str_pad($current_number + 1, 4, 0, STR_PAD_LEFT);
                }

                $mega_status = true; // Status to determine whether all transaction is success or failed
                $receive = array(
                    'purchase_order_id' => $purchase_order_id,
                    'store_id' => $this->_setting['store_id'],
                    'incoming_date' => $this->input->post('received_date'),
                    'total' => $this->input->post('total'),
                    'discount' => $this->input->post('discount'),
                    'discount_description' => $this->input->post('discount_description'),
                    'payment_method' => $this->input->post('method') == 'cash' ? 1 : 2,
                    'payment_date' => $this->input->post('bon_date'),
                    'payment_no' => $new_payment_no,
                    'payment_status' => $this->input->post('method') == 'cash' ? 1 : 0,
                    'has_journaled' => 0,
                    'invoice_logo' => $image_name
                );                    

                $status = array(
                    'status' => $this->input->post('status') == 'closed' ? 1 : 2
                );
                $cond = array(
                    'id' => $purchase_order_id
                );


                $outlet_destination_id = $this->input->post('outlet_destination_id');
                $auto_convert = $this->input->post('auto_convert');
                $price = $this->input->post('price');
                $received = $this->input->post('received');
                // $data_outlet =  $this->store_model->get_outlets(array('store_id' =>  $this->_setting['store_id'],'is_warehouse' => 1));
                $data_outlet = $this->store_model->get_outlets(array('store_id' => $this->_setting['store_id'], 'id' => $outlet_destination_id));
                if (sizeof($data_outlet) == 0) {
                    $this->session->set_flashdata('message', 'Penerimaan barang gagal ditambahkan,silahkan sinkronisasi server terlebih dahulu dikarenakan outlet pada store ini belum update!');
                    redirect(SITE_ADMIN . '/receive_stocks/listing', 'refresh');
                }

                $temp_status = $this->purchase_order_model->update($status, $cond);
                if ($temp_status == false) $mega_status = false;

                $receive_id = $this->purchase_order_receive_model->add($receive);
                foreach ($received as $key => $amount) {
                    if ($amount > 0) {
                        $receive_detail = array(
                            'has_synchronized' => 0,
                            'store_id' => $this->_setting['store_id'],
                            'purchase_order_detail_id' => $key,
                            'received_quantity' => $amount,
                            'price' => $price[$key],
                            'purchase_order_receive_id' => $receive_id,
                        );
                        $temp_status = $this->purchase_order_receive_detail_model->add($receive_detail);
                        if ($temp_status == false) $mega_status = false;
                    }
                }                    

                $detail_receive = $this->purchase_order_receive_detail_model->get(array('purchase_order_receive_id' => $receive_id));
                $i = 0;
                foreach ($detail_receive as $data) {
                    if ($auto_convert == 1 && $data->uom_id != $data->origin_uom_id) {
                        $received_quantity = $data->received_quantity;
                        $uom_id = $data->uom_id;
                        $price = $data->price;

                        $convertion = $this->inventory_model->convertion(array("inventory_id" => $data->inventory_id, "uom_id" => $data->uom_id));
                        if ($convertion > 0) {
                            $received_quantity = $data->received_quantity * $convertion;
                            $uom_id = $data->origin_uom_id;
                            $price = round(($data->price / $convertion), 0);
                        }

                        //INSERT STOCK
                        $array = array(
                            'store_id' => $this->_setting['store_id'],
                            'outlet_id' => $data_outlet[0]->id,
                            'inventory_id' => $data->inventory_id,
                            'uom_id' => $uom_id,
                            'quantity' => $received_quantity,
                            'created_at' => $this->input->post('received_date'),
                            'purchase_date' => $this->input->post('received_date'),
                            'price' => $price
                        );
                        $last_id_stock[$i] = $this->categories_model->save('stock', $array);
                        
                        //INSERT STOCK HISTORY
                        $array['status'] = 3;
                        $save = $this->categories_model->save('stock_history', $array);

                        if ($this->data['setting']['stock_method'] == "AVERAGE") {
                            $this->process_save_method_average($array);
                        }
                        
                    } else {
                        //INSERT STOCK
                        $array = array(
                            'store_id' => $this->_setting['store_id'],
                            'outlet_id' => $data_outlet[0]->id,
                            'inventory_id' => $data->inventory_id,
                            'uom_id' => $data->uom_id,
                            'quantity' => $data->received_quantity,
                            'created_at' => $this->input->post('received_date'),
                            'purchase_date' => $this->input->post('received_date'),
                            'price' => $data->price
                        );
                        $last_id_stock[$i] = $this->categories_model->save('stock', $array);
                        //INSERT STOCK HISTORY
                        $array['status'] = 3;
                        $save = $this->categories_model->save('stock_history', $array);

                        if ($this->data['setting']['stock_method'] == "AVERAGE") {
                            $this->process_save_method_average($array);
                        }
                    }
                    $i++;
                }

                $inventory_id = $this->input->post('inventory_id');
                if ($this->data['module']['ACCOUNTING'] == 1) {
                    $param_journal = array(
                        'status' => 'receive',
                        'receive_id' => $receive_id,
                        'mega_status' => $mega_status,
                        'supplier_acc' => $supplier->account_payable_id
                    );
                    $this->set_journal($param_journal);
                }

                if ($mega_status == true)
                    $this->session->set_flashdata('message_success', 'Penerimaan barang berhasil ditambahkan');
                else
                    $this->session->set_flashdata('message', 'Penerimaan barang gagal ditambahkan');
                redirect(SITE_ADMIN . '/receive_stocks/listing', 'refresh');
            } else {
                $price = $this->input->post('price');
                $received = $this->input->post('received');
                $total = $this->input->post('total');
                $discount = $this->input->post('discount');
            }
        }
        $detail = $this->purchase_order_detail_model->get(array('purchase_order_id' => $purchase_order_id));
        $already_received = false;
        foreach ($detail as $item) {
            $previous[$item->id] = array_pop($this->purchase_order_receive_detail_model->get_previous(array('purchase_order_detail_id' => $item->id)));
            if ($previous[$item->id]->previous != null) $already_received = true;
        }

        $this->data['price'] = $price;
        $this->data['received'] = $received;
        $this->data['total'] = $total;
        $this->data['sub_total'] = $sub_total;
        $this->data['discount'] = $discount;
        $this->data['previous'] = $previous;
        $this->data['already_received'] = $already_received;

        $this->data['title'] = "Penerimaan Barang";
        $this->data['subtitle'] = "Penerimaan Barang";

        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');
        
        $this->data['outlet_lists'] = $this->store_model->get_outlets(array('store_id' => $this->_setting['store_id']));
        
        $this->data['invoice_logo'] = array(
            'name' => 'invoice_logo',
            'id' => 'invoice_logo',
            'type' => 'file',
            'class' => 'form-control maxUploadSize',
            'placeholder' => '',
            'data-maxsize' => '1000000', // byte
            'value' => $this->form_validation->set_value('invoice_logo', $purchase_order[0]->invoice_logo)
        );
        $this->data['purchase_order'] = array_pop($purchase_order);
        // print_r($this->data['purchase_order']);
        $this->data['detail'] = $detail;
        $this->data['content'] .= $this->load->view('admin/receive-stocks-receive', $this->data, true);

        $this->render('admin');
    }

    // function for process save stock with AVERAGE method
    // parameter : data_inventory
    // created by : bening
    public function process_save_method_average($data_inventory){

        $this->load->model("stock_model");
        
        //get all stock by inventory id
        $data_stocks = $this->stock_model->get_stock_detail_by_inventory_id(array(
            "store_id" => $data_inventory['store_id'],
            "outlet_id" => $data_inventory['outlet_id'],
            "inventory_id" => $data_inventory['inventory_id'],
            "uom_id" => $data_inventory['uom_id'],
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
        if ($total_price != 0 && $total_quantity != 0) $average_price = round($total_price / $total_quantity, 0);

        // Insert new stock with new quantity and average price 
        $array = array(
            'store_id' => $data_inventory['store_id'],
            'outlet_id' => $data_inventory['outlet_id'],
            'inventory_id' => $data_inventory['inventory_id'],
            'uom_id' => $data_inventory['uom_id'],
            'quantity' => $total_quantity,
            'created_at' => $data_inventory['created_at'],
            'purchase_date' => $data_inventory['purchase_date'],
            'price' => $average_price
        );
        $this->stock_model->save('stock', $array);        
    }

    /**
     * History receiving goods
     * @param  integer $purchase_order_id Id of purchase order
     * @return void
     */
    public function history($purchase_order_id = -1)
    {
        $this->data['title'] = "Penerimaan Barang";
        $this->data['subtitle'] = "History Penerimaan Barang";

        $purchase_order = $this->purchase_order_model->get(array('purchase_order.id' => $purchase_order_id));
        $detail_pembayaran = $this->purchase_order_receive_model->get(array('purchase_order_id' => $purchase_order_id));
        $data_pesanan = array();

        foreach ($detail_pembayaran as $data_pembayaran) {
            $detail_pesanan = $this->purchase_order_receive_detail_model->get(array('purchase_order_receive_id' => $data_pembayaran->id));
            foreach ($detail_pesanan as $key => $value) {
                $value->payment_no = $data_pembayaran->payment_no;
                array_push($data_pesanan, $value);
            }
        }

        if (count($data_pesanan) < 1) {
            $this->session->set_flashdata('message', 'Tidak ada data pesanan untuk penerimaan barang tersebut');
            redirect(SITE_ADMIN . '/receive_stocks/listing');
        }

        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['purchase_order'] = array_pop($purchase_order);
        $this->data['detail_pembayaran'] = $detail_pembayaran;
        $this->data['detail_pesanan'] = $data_pesanan;
        $this->data['content'] .= $this->load->view('admin/receive-stocks-history', $this->data, true);

        $this->render('admin');
    }

    public function prints($purchase_order_id = -1, $purchase_order_receive_id = -1)
    {
        $this->load->helper("printer_helper");
        $detail = $this->purchase_order_receive_detail_model->get(array('purchase_order_receive_id' => $purchase_order_receive_id));
        if (count($detail) < 1) {
            $this->session->set_flashdata('message', 'Tidak ada detail untuk penerimaan barang tersebut');
            redirect(SITE_ADMIN . '/receive_stocks/listing');
        }
        $purchase_order = $this->purchase_order_model->get(array('purchase_order.id' => $purchase_order_id));
        $receive = $this->purchase_order_receive_model->get(array('id' => $purchase_order_receive_id));

        $this->data['purchase_order'] = array_pop($purchase_order);
        $this->data['detail'] = $detail;

        $this->data['receive'] = array_pop($receive);
        
        //get printer PO
        $this->load->model("setting_printer_model");
        $printer_arr_obj = $this->setting_printer_model->get_printer_by_enum_printer_type(array("name_type"=>"printer_matrix_po"));
        
        foreach ($printer_arr_obj as $printer_obj) {
            $printer_location = $printer_obj->name_printer;
            print_receive_po($printer_location, $this->data);
        }
        
        redirect(SITE_ADMIN . '/receive_stocks/history/'.$purchase_order_id);
    }

    /**
     * Display detail page for each receive stocks
     * @param  integer $purchase_order_id id of purchase order
     * @param  integer $purchase_order_receive_id id of receive stocks
     * @return void
     */
    public function detail($purchase_order_id = -1, $purchase_order_receive_id = -1)
    {
        $detail = $this->purchase_order_receive_detail_model->get(array('purchase_order_receive_id' => $purchase_order_receive_id));
        if (count($detail) < 1) {
            $this->session->set_flashdata('message', 'Tidak ada detail untuk penerimaan barang tersebut');
            redirect(SITE_ADMIN . '/receive_stocks/listing');
        }

        $this->data['title'] = "Penerimaan Barang";
        $this->data['subtitle'] = "Detail History";

        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $purchase_order = $this->purchase_order_model->get(array('purchase_order.id' => $purchase_order_id));
        $receive = $this->purchase_order_receive_model->get(array('id' => $purchase_order_receive_id));

        $this->data['purchase_order'] = array_pop($purchase_order);
        $this->data['detail'] = $detail;
        $this->data['receive'] = array_pop($receive);
        $this->data['content'] .= $this->load->view('admin/receive-stocks-detail', $this->data, true);

        $this->render('admin');
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
     * @param  array $data Data to be sent
     * @param  string $url Url API
     * @return boolean Status
     */
    private function _curl_connect($data = array(), $url = '')
    {
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
            if ($result->status == TRUE) {
                return $result->data;

            } else {
            }


        } else {
            // curl failed
        }

        return FALSE;


    }

    public function set_journal($param = array()) {
        $mega_status = $param['mega_status'];
        if ($param['status'] == 'receive') {
            $inventory_id = $this->input->post('inventory_id');
            if ($this->input->post('method') == 'bon') {
                $sub_debit = $this->input->post('sub_total');
                $i = 0;
                foreach ($inventory_id as $inventory) {
                    $acc = array_pop($this->inventory_model->get_inventory_account($inventory));
                    $debit = array(
                        'store_id' => $this->_setting['store_id'],
                        'entry_type' => 5,
                        'foreign_id' => $param['receive_id'],
                        'debit' => $sub_debit[$i],
                        'credit' => 0,
                        'info' => (!empty($acc)) ? $acc->name : '',
                        'account_id' => (!empty($acc)) ? $acc->account_id : 0
                    );
                    $temp_status = $this->account_data_model->add($debit);
                    if ($temp_status == false) $mega_status = false;
                    $i++;
                }

                $acc_id = $param['supplier_acc'];
                $acc = $this->account_data_model->get_by('account', $acc_id);
                $kredit = array(
                    'store_id' => $this->_setting['store_id'],
                    'entry_type' => 5,
                    'foreign_id' => $param['receive_id'],
                    'debit' => 0,
                    'credit' => $this->input->post('grand_total'),
                    'account_id' => (!empty($acc)) ? $acc_id : 0,
                    'info' => (!empty($acc)) ? $acc->name : ''
                );
                $temp_status = $this->account_data_model->add($kredit);
                if ($temp_status == false) $mega_status = false;
            } else {
                $sub_debit = $this->input->post('sub_total');
                $i = 0;
                foreach ($inventory_id as $inventory) {
                    $acc = array_pop($this->inventory_model->get_inventory_account($inventory));
                    $debit = array(
                        'store_id' => $this->_setting['store_id'],
                        'entry_type' => 5,
                        'foreign_id' => $param['receive_id'],
                        'debit' => $sub_debit[$i],
                        'credit' => 0,
                        'info' => (!empty($acc)) ? $acc->name : '',
                        'account_id' => (!empty($acc)) ? $acc->account_id : 0
                    );
                    $temp_status = $this->account_data_model->add($debit);
                    if ($temp_status == false) $mega_status = false;
                    $i++;
                }

                $acc_id = $this->data['setting']['cash_account_id'];
                $acc = $this->account_data_model->get_by('account', $acc_id);
                $kredit = array(
                    'store_id' => $this->_setting['store_id'],
                    'entry_type' => 5,
                    'foreign_id' => $param['receive_id'],
                    'debit' => 0,
                    'credit' => $this->input->post('grand_total'),
                    'account_id' => (!empty($acc)) ? $acc_id : 0,
                    'info' => (!empty($acc)) ? $acc->name : ''
                );
                $temp_status = $this->account_data_model->add($kredit);
                if ($temp_status == false) $mega_status = false;
            }
        }
    }
}