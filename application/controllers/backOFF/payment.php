<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Payment extends Admin_Controller
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
        $this->load->model('account_model');
        $this->load->model('account_data_model');
        $this->load->model('purchase_order_model');
        $this->load->model('purchase_order_receive_model');
        $this->load->model('purchase_order_receive_detail_model');
        $this->load->model('purchase_order_detail_model');
        $this->load->helper(array('datatables'));

        $this->_store_data = $this->ion_auth->user()->row();
        $this->_setting = $this->data['setting'];
    }

    public function index()
    {
        $this->detail();
    }

    public function detail($mode = 'none', $purchase_order_id = -1, $purchase_order_receive_id = -1)
    {
        $detail = $this->purchase_order_receive_detail_model->get(array('purchase_order_receive_id' => $purchase_order_receive_id));
        if (count($detail) < 1 || 'none' == $mode) {
            $this->session->set_flashdata('message', 'Tidak ada detail untuk penerimaan barang tersebut');
            redirect(SITE_ADMIN . '/reports/kontra_bon');
        }

        $this->data['title'] = "Pembayaran";
        $this->data['subtitle'] = "Detail Kontra Bon";

        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $purchase_order = $this->purchase_order_model->get(array('purchase_order.id' => $purchase_order_id));
        $receive = $this->purchase_order_receive_model->get(array('purchase_order_receive.id' => $purchase_order_receive_id));
        $history_payment = $this->purchase_order_receive_model->get_history(array('purchase_order_receive_id' => $purchase_order_receive_id));

        $this->data['purchase_order'] = array_pop($purchase_order);
        $this->data['detail'] = $detail;
        $this->data['mode'] = $mode;
        $this->data['receive'] = array_pop($receive);
        $this->data['payment_history'] = array_pop($history_payment);
        $this->data['account_lists']=$this->purchase_order_model->get_all_where("account",array("account_type_id"=>1));
        $this->data['content'] .= $this->load->view('admin/kontra-bon-pay', $this->data, true);

        $this->render('admin');
    }

    public function bon($purchase_order_receive_id = -1, $tgl_bayar = -1,$account_id=0, $amount = 0)
    {
        $this->load->model("account_data_model");
        $has_paid = array_pop($this->purchase_order_receive_model->get_history($purchase_order_receive_id));
        $receive = array_pop($this->purchase_order_receive_model->get(array('purchase_order_receive.id' => $purchase_order_receive_id)));
        $payment_status = 1;
        if (($has_paid->total + $amount) == $receive->total) {
            $payment_status = 1;
        } else if (($has_paid->total + $amount) < $receive->total) {
            $payment_status = 0;
        }

        $data = array(
            'payment_status' => $payment_status,
            'payment_date' => $tgl_bayar
        );

        $data_payment_history = array(
            'purchase_order_receive_id' => $purchase_order_receive_id,
            'payment_date' => $tgl_bayar,
            'amount' => $amount
        );

        $payment_history = $this->purchase_order_receive_model->save('purchase_order_payment_history', $data_payment_history);

        $cond = array('id' => $purchase_order_receive_id);
        $status = $this->purchase_order_receive_model->update($data, $cond);
        $po_receive=$this->store_model->get_one("purchase_order_receive",$purchase_order_receive_id);
        $po=$this->store_model->get_one("purchase_order",$po_receive->purchase_order_id);
        $supplier=$this->store_model->get_one("supplier",$po->supplier_id);

        $this->prints($po_receive->purchase_order_id, $purchase_order_receive_id, $tgl_bayar, $amount, $has_paid);

        if ($status != false || $payment_status == 0) {
            if ($this->data['module']['ACCOUNTING'] == 1) {
                $debit = array(
                    'entry_type' => 5,
                    'foreign_id' => $purchase_order_receive_id,
                    'debit' => $amount,
                    'credit' => 0,
                    'info' => 'Pembayaran Hutang PO',
                    'account_id' => $supplier->account_payable_id,
                    'created_at'=>$tgl_bayar.' 00:00:00'
                );
                $this->account_data_model->add($debit);

                /*$credit = array(
                    'store_id' => $store_id,
                    'entry_type' => 5,
                    'foreign_id' => $purchase_order_receive_id,
                    'credit' => $po_receive->total,
                    'account_id' =>$account_id,
                    'created_at'=>$tgl_bayar.' 00:00:00'
                );
                $this->account_data_model->add($credit);*/

            }
            $this->session->set_flashdata('success_message', 'Bon berhasil dibayar');
        } else {
            $this->session->set_flashdata('message', 'Tidak bisa membayar bon, silahkan coba lagi');
        }
        redirect(SITE_ADMIN . '/reports/kontra_bon');
    }

    public function prints($purchase_order_id = -1, $purchase_order_receive_id = -1, $payment_date = '', $amount = 0, $is_history = false) {
        $this->load->helper("printer_helper");
        $this->load->model("setting_printer_model");
        $printer_arr_obj = $this->setting_printer_model->get_printer_by_enum_printer_type(array("name_type"=>"printer_matrix_po"));

        $has_paid = array_pop($this->purchase_order_receive_model->get_history($purchase_order_receive_id));
        $po_receive = $this->store_model->get_one("purchase_order_receive", $purchase_order_receive_id);
        $po = $this->store_model->get_one("purchase_order", $po_receive->purchase_order_id);
        $supplier = $this->store_model->get_one("supplier", $po->supplier_id);
        $detail = $this->store_model->get_all_where("purchase_order_payment_history", array('purchase_order_receive_id' => $purchase_order_receive_id));

        $this->data['supplier'] = $supplier;
        $this->data['po_receive'] = $po_receive;
        $this->data['payment_date'] = $payment_date;
        $this->data['detail'] = $detail;
        $this->data['has_paid'] = $has_paid;
        $this->data['amount'] = $amount;
        $this->data['is_history'] = $is_history;
        
        foreach ($printer_arr_obj as $printer_obj) {
            $printer_location = $printer_obj->name_printer;
            print_kontra_bon($printer_location, $this->data);
        }

        if ($is_history) {
            redirect(SITE_ADMIN . '/payment/detail/view/'.$purchase_order_id.'/'.$purchase_order_receive_id);
        } else {
            redirect(SITE_ADMIN . '/payment/detail/bon/'.$purchase_order_id.'/'.$purchase_order_receive_id);
        }
    }
}
