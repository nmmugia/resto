<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Reports extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('bill_model');
        $this->load->model('supplier_model');
        $this->data['all_supplier'] = $this->supplier_model->get_supplier_dropdown();
        $this->data['months'] = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
    }

    public function index()
    {
        redirect(SITE_ADMIN . '/reports/transaction');
    }
    // Laporan Tranfer Barang
	public function transfer_menu()
	{
		$this->data['title'] = "Laporan Transfer Barang";
        $this->data['subtitle'] = "Laporan Transfer Barang";

        $this->load->model("inventory_model");
        $date = date("Y-m-d");
        $report_end_date = date("Y-m-d");
        $this->data['is_print'] = FALSE;
        $this->data['date'] = $date;
        $this->data['report_end_date'] = $report_end_date;
        $this->data['data_store'] = $this->store_model->get_store($this->data['setting']['store_id']);
        $this->data['results'] = $this->inventory_model->get_transfer_menu(array("date" => $date, "report_end_date" => $report_end_date));

        $this->data['content'] .= $this->load->view('admin/report/report-transfer-menu', $this->data, true);
        $this->render('report');
	}

    // Laporan Tranfer Barang
    public function transfer_menu_detail()
    {
        $this->data['title'] = "Detail Transfer Barang";
        $this->data['subtitle'] = "Detail Transfer Barang";

        $this->load->model("inventory_model");
        $id = $this->uri->segment(4);

        $this->data['results'] = $this->inventory_model->get_transfer_menu_detail($id);
        $this->data['data_transfer'] = $this->inventory_model->get_transfer_menu_by_id($id);
        $this->data['content'] .= $this->load->view('admin/report/report-transfer-menu-detail', $this->data, true);
        $this->render('report');
    }
    // Laporan Transfer Inventory
    public function transfer_inventory()
    {
        $this->data['title'] = "Laporan Transfer Inventory";
        $this->data['subtitle'] = "Laporan Transfer Inventory";

        $this->load->model("inventory_model");
        $this->load->model("categories_model");
        $this->load->model("store_model");

        $start_date = date("Y-m-d");
        $end_date = date("Y-m-d");
        $store_id_start = $this->data['setting']['store_id'];
        $store_id_end = $this->input->post('store_id_end');
        $outlet_id_start = $this->input->post('outlet_id_start');
        $outlet_id_end = $this->input->post('outlet_id_end');
        $inventory_id = $this->input->post('inventory_id');

        $this->data['is_print'] = FALSE;
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['store_id'] = $store_id_start;

        $this->data['store_id_start']  = $this->store_model->get_store_dropdown($store_id_start);
        $this->data['store_id_end']  = $this->store_model->get_store_dropdown($store_id_start);
        $this->data['inventory_lists'] = $this->store_model->get("inventory")->result();

        $this->data['all_outlet_start'] = $this->categories_model->get_outlet_dropdown_from_warehouse();
        $this->data['all_outlet_end'] = $this->categories_model->get_outlet_dropdown();
        $this->data['data_store'] = $this->store_model->get_store($this->data['setting']['store_id']);
        
        $this->data['results'] = $this->inventory_model->get_transfer_inventory(array("store_id_start" => $store_id_start,"outlet_id_start" => $outlet_id_start, "store_id_end" => $store_id_end, "outlet_id_end" => $outlet_id_end,"inventory_id" => $inventory_id, "start_date" => $start_date, "end_date" => $end_date));


        $this->data['content'] .= $this->load->view('admin/report/report-transfer-inventory', $this->data, true);
        $this->render('report');
    }
	public function get_transfer_menu()
    {
        $this->load->model("inventory_model");
        $date = $this->input->post('date');
        $report_end_date = $this->input->post('report_end_date');
        $this->data['is_print'] = FALSE;
        $this->data['date'] = $date;
        $this->data['report_end_date'] = $report_end_date;
        $this->data['data_store'] = $this->store_model->get_store($this->data['setting']['store_id']);
        $this->data['results'] = $this->inventory_model->get_transfer_menu(array("date" => $date, "report_end_date" => $report_end_date));
        $this->load->view('admin/report/report-transfer-menu-pdf', $this->data);
    }
    public function get_transfer_inventory()
    {
        $this->load->model("inventory_model");
        $this->load->model("categories_model");
        $this->load->model("store_model");

        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $store_id_start = $this->input->post('store_id_start');
        $store_id_end = $this->input->post('store_id_end');
        $outlet_id_start = $this->input->post('outlet_id_start');
        $outlet_id_end = $this->input->post('outlet_id_end');
        $inventory_id = $this->input->post('inventory_id');

        $this->data['is_print'] = FALSE;
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['store_id'] = $store_id_start;

        $this->data['store_id_start']  = $this->store_model->get_store_dropdown($store_id_start);
        $this->data['store_id_end']  = $this->store_model->get_store_dropdown($store_id_start);
        $this->data['inventory_lists'] = $this->store_model->get("inventory")->result();

        $this->data['all_outlet_start'] = $this->categories_model->get_outlet_dropdown_from_warehouse();
        $this->data['all_outlet_end'] = $this->categories_model->get_outlet_dropdown();

        $this->data['results'] = $this->inventory_model->get_transfer_inventory(array("store_id_start" => $store_id_start,"outlet_id_start" => $outlet_id_start, "store_id_end" => $store_id_end, "outlet_id_end" => $outlet_id_end,"inventory_id" => $inventory_id, "start_date" => $start_date, "end_date" => $end_date));
       
        $this->load->view('admin/report/report-transfer-inventory-pdf', $this->data);
    }
    public function spoiled()
    {
        $this->data['title'] = "Laporan Spoiled";
        $this->data['subtitle'] = "Laporan Spoiled";
        $this->data['outlet_lists'] = $this->store_model->get("outlet")->result();
        $this->data['inventory_lists'] = $this->store_model->get("inventory")->result();
        $this->data['content'] .= $this->load->view('admin/report/report-spoiled', $this->data, true);
        $this->render('report');
    }

    public function get_spoiled_data()
    {
        $this->load->model("report_model");
        $inventory_id = $this->input->post("inventory_id");
        $outlet_id = $this->input->post("outlet_id");
        $start_date = $this->input->post("start_date");
        $end_date = $this->input->post("end_date");
        $this->data['is_print'] = FALSE;
        $this->data['inventory_id'] = $inventory_id;
        $this->data['outlet_id'] = $outlet_id;
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['outlet'] = $this->store_model->get_one("outlet", $outlet_id);
        $this->data['inventory'] = $this->store_model->get_one("outlet", $inventory_id);
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['lists'] = $this->report_model->spoiled(array("inventory_id" => $inventory_id, "outlet_id" => $outlet_id, "start_date" => $start_date, "end_date" => $end_date));
        $this->load->view('admin/report/report-spoiled-pdf', $this->data);
    }

    public function compliments()
    {
        $this->data['title'] = "Laporan Compliment";
        $this->data['subtitle'] = "Laporan Compliment";

        $start_date = date("Y-m-d");
        $end_date = date("Y-m-d");
        $this->load->model("report_model");
        $this->data['is_print'] = FALSE;
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->compliment(array("start_date" => $start_date, "end_date" => $end_date));
        
        $this->data['content'] .= $this->load->view('admin/report/report-compliment', $this->data, true);
        $this->render('report');
    }

    public function get_compliment_data()
    {
        $this->load->model("report_model");
        $start_date = $this->input->post("start_date");
        $end_date = $this->input->post("end_date");
        $this->data['is_print'] = FALSE;
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->compliment(array("start_date" => $start_date, "end_date" => $end_date));
        $this->load->view('admin/report/report-compliment-pdf', $this->data);
    }

    public function bon_bills()
    {
        $this->data['title'] = "Laporan BON Bill";
        $this->data['subtitle'] = "Laporan BON Bill";



        
        $start_date = date("Y-m-d");
        $end_date = date("Y-m-d");
        $this->load->model("report_model");
        $this->data['is_print'] = FALSE;
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->bon_bill(array("start_date" => $start_date, "end_date" => $end_date));



        $this->data['content'] .= $this->load->view('admin/report/report-bon-bill', $this->data, true);
        $this->render('report');
    }

    public function get_bon_bill_data()
    {
        $this->load->model("report_model");
        $start_date = $this->input->post("start_date");
        $end_date = $this->input->post("end_date");
        $this->data['is_print'] = FALSE;
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->bon_bill(array("start_date" => $start_date, "end_date" => $end_date));
        $this->load->view('admin/report/report-bon-bill-pdf', $this->data);
    }

    public function promo_useds()
    {
        $this->data['title'] = "Laporan Penggunaan Promo";
        $this->data['subtitle'] = "Laporan Penggunaan Promo";


        $start_date = date("Y-m-d");
        $end_date = date("Y-m-d");
        $this->load->model("report_model");
        $this->data['is_print'] = FALSE;
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->promo_used(array("start_date" => $start_date, "end_date" => $end_date));


        $this->data['content'] .= $this->load->view('admin/report/report-promo-used', $this->data, true);
        $this->render('report');
    }

    public function get_promo_used_data()
    {
        $this->load->model("report_model");
        $start_date = $this->input->post("start_date");
        $end_date = $this->input->post("end_date");
        $this->data['is_print'] = FALSE;
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->promo_used(array("start_date" => $start_date, "end_date" => $end_date));
        $this->load->view('admin/report/report-promo-used-pdf', $this->data);
    }

    public function promo_cc()
    {
        $this->data['title'] = "Laporan Penggunaan Promo Kartu Kredit";
        $this->data['subtitle'] = "Laporan Penggunaan Promo Kartu Kredit";


        $start_date = date("Y-m-d 00:00");
        $end_date = date("Y-m-d 23:59");
        $this->load->model("report_model");
        $this->load->model("cashier_model");
        $this->data['is_print'] = FALSE;
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['all_promo_cc'] = $this->cashier_model->get_promo_cc_dropdown($this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->promo_cc(array("start_date" => $start_date, "end_date" => $end_date, 'promo_cc_id' => 0));
        
        $this->data['content'] .= $this->load->view('admin/report/report-promo-cc', $this->data, true);
        $this->render('report');
    }

    public function get_promo_cc_data()
    {
        $this->load->model("report_model");
        $start_date = $this->input->post("start_date");
        $end_date = $this->input->post("end_date");
        $promo_cc_id = $this->input->post("promo_cc_id");
        $promo_cc_id = explode("-", $promo_cc_id);
        $promo_cc_id = $promo_cc_id[0];
        $this->data['is_print'] = FALSE;
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['promo_cc_id'] = $promo_cc_id;
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->promo_cc(array("start_date" => $start_date, "end_date" => $end_date, 'promo_cc_id' => $promo_cc_id));
        
        $this->load->view('admin/report/report-promo-cc-pdf', $this->data);
    }

    public function voucher_useds()
    {
        $this->data['title'] = "Laporan Penggunaan Voucher";
        $this->data['subtitle'] = "Laporan Penggunaan Voucher";


        $start_date = date("Y-m-d");
        $end_date = date("Y-m-d");


        $this->load->model("report_model");
        $this->data['is_print'] = FALSE;
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->voucher_used(array("start_date" => $start_date, "end_date" => $end_date));



        $this->data['content'] .= $this->load->view('admin/report/report-voucher-used', $this->data, true);
        $this->render('report');
    }

    public function get_voucher_used_data()
    {
        $this->load->model("report_model");
        $start_date = $this->input->post("start_date");
        $end_date = $this->input->post("end_date");
        $this->data['is_print'] = FALSE;
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->voucher_used(array("start_date" => $start_date, "end_date" => $end_date));
        $this->load->view('admin/report/report-voucher-used-pdf', $this->data);
    }

    public function member_transactions()
    {
        $this->data['title'] = "Laporan Transaksi Member";
        $this->data['subtitle'] = "Laporan Transaksi Member";
        $this->data['member_lists'] = $this->store_model->get("member")->result();


        $start_date = date("Y-m-d");
        $end_date = date("Y-m-d");
        $member_id = $this->input->post("member_id");
        $this->load->model("report_model");
        $this->data['is_print'] = FALSE;
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['member_id'] = $member_id;
        $this->data['member'] = $this->store_model->get_one("member", $member_id);
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->member_transaction(array("start_date" => $start_date, "end_date" => $end_date, "member_id" => $member_id));


        $this->data['content'] .= $this->load->view('admin/report/report-member-transaction', $this->data, true);
        $this->render('report');
    }

    public function get_member_transaction_data()
    {
        $this->load->model("report_model");
        $start_date = $this->input->post("start_date");
        $end_date = $this->input->post("end_date");
        $member_id = $this->input->post("member_id");
        $this->data['is_print'] = FALSE;
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['member_id'] = $member_id;
        $this->data['member'] = $this->store_model->get_one("member", $member_id);
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->member_transaction(array("start_date" => $start_date, "end_date" => $end_date, "member_id" => $member_id));
        $this->load->view('admin/report/report-member-transaction-pdf', $this->data);
    }

    public function summary_receive_orders()
    {
        $this->data['title'] = "Laporan Summary Penerimaan Barang";
        $this->data['subtitle'] = "Laporan Summary Penerimaan Barang";
        $this->data['all_store']  = $this->store_model->get_store_dropdown();
        $this->data['supplier_lists'] = $this->store_model->get("supplier")->result();


        
        $start_date = date("Y-m-d");
        $end_date = date("Y-m-d");
        $this->load->model("report_model");
        $supplier_id = $this->input->post("supplier_id");
        $payment_method = $this->input->post("payment_method");
        $this->data['is_print'] = FALSE;
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['supplier_id'] = $supplier_id;
        $this->data['store_id'] = $this->data['setting']['store_id'];
        $this->data['payment_method'] = $payment_method;
        $this->data['supplier'] = $this->store_model->get_one("supplier", $supplier_id);
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->summary_receive_order(array("start_date" => $start_date, "end_date" => $end_date, "supplier_id" => $supplier_id, "payment_method" => $payment_method,"store_id"=>$this->data['setting']['store_id']));



        $this->data['content'] .= $this->load->view('admin/report/report-summary-receive-order', $this->data, true);
        $this->render('report');
    }

    public function get_summary_receive_order_data()
    {
        $this->load->model("report_model");
        $start_date = $this->input->post("start_date");
        $end_date = $this->input->post("end_date");
        $supplier_id = $this->input->post("supplier_id");
        $payment_method = $this->input->post("payment_method");
        $this->data['is_print'] = FALSE;
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['supplier_id'] = $supplier_id;
        $this->data['store_id'] = $this->data['setting']['store_id'];
        $this->data['payment_method'] = $payment_method;
        $this->data['supplier'] = $this->store_model->get_one("supplier", $supplier_id);
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->summary_receive_order(array("start_date" => $start_date, "end_date" => $end_date, "supplier_id" => $supplier_id, "payment_method" => $payment_method,"store_id"=>$this->data['setting']['store_id']));
        $this->load->view('admin/report/report-summary-receive-order-pdf', $this->data);
    }

    public function summary_retur_orders()
    {
        $this->data['title'] = "Laporan Summary Pengembalian Barang";
        $this->data['subtitle'] = "Laporan Summary Pengembalian Barang";
        $this->data['supplier_lists'] = $this->store_model->get("supplier")->result();
        
        $start_date = date("Y-m-d");
        $end_date = date("Y-m-d");
        $this->load->model("report_model");
        $supplier_id = $this->input->post("supplier_id");
        $this->data['is_print'] = FALSE;
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['supplier_id'] = $supplier_id;
        $this->data['supplier'] = $this->store_model->get_one("supplier", $supplier_id);
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->summary_retur_order(array("start_date" => $start_date, "end_date" => $end_date, "supplier_id" => $supplier_id));

        $this->data['content'] .= $this->load->view('admin/report/report-summary-retur-order', $this->data, true);
        $this->render('report');
    }

    public function get_summary_retur_order_data()
    {
        $this->load->model("report_model");
        $start_date = $this->input->post("start_date");
        $end_date = $this->input->post("end_date");
        $supplier_id = $this->input->post("supplier_id");
        $this->data['is_print'] = FALSE;
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['supplier_id'] = $supplier_id;
        $this->data['supplier'] = $this->store_model->get_one("supplier", $supplier_id);
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->summary_retur_order(array("start_date" => $start_date, "end_date" => $end_date, "supplier_id" => $supplier_id));
        $this->load->view('admin/report/report-summary-retur-order-pdf', $this->data);
    }

    public function summary_inventories()
    {
        $this->data['title'] = "Laporan Summary Inventory";
        $this->data['subtitle'] = "Laporan Summary Inventory";
        $this->data['all_store']  = $this->store_model->get_store_dropdown();
        $this->data['outlet_lists'] = $this->store_model->get_outlets(array('store_id' => $this->data['setting']['store_id']));
        $this->data['inventory_lists'] = $this->store_model->get("inventory")->result();

        $this->load->model("report_model");
        $start_period = date('Y-m-01');
        $end_period = date('Y-m-t');
        $outlet_id = $this->input->post("outlet_id");
        $inventory_id = $this->input->post("inventory_id");
        $this->data['is_print'] = FALSE;
        $this->data['start_date'] = $start_period;
        $this->data['end_date'] = $end_period;
        $this->data['store_id'] =  $this->data['setting']['store_id'];
        $this->data['outlet_id'] = $outlet_id;
        $this->data['inventory_id'] = $inventory_id;
        $this->data['history_status'] = $this->store_model->get("enum_stock_history_status")->result();
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->summary_inventory(array("start_date" => $start_period, "end_date" => $end_period, "outlet_id" => $outlet_id, "inventory_id" => $inventory_id,"store_id"=>$this->data['setting']['store_id']));

        $this->data['content'] .= $this->load->view('admin/report/report-summary-inventory', $this->data, true);
        $this->render('report');
    }

	public function detail_inventories()
    {
        $this->data['title'] = "Laporan Detail Inventory";
        $this->data['subtitle'] = "Laporan Detail Inventory";
        $this->data['all_store']  = $this->store_model->get_store_dropdown();
        $this->data['outlet_lists'] = $this->store_model->get_outlets(array('store_id' => $this->data['setting']['store_id']));
        $this->data['inventory_lists'] = $this->store_model->get("inventory")->result();



        $this->load->model("report_model");
        $start_date = date("Y-m-d");
        $end_date = date("Y-m-d");
        $outlet_id = $this->input->post("outlet_id");
        $inventory_id = $this->input->post("inventory_id");
        $this->data['is_print'] = FALSE;
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['store_id'] =  $this->data['setting']['store_id'];
        $this->data['outlet_id'] = $outlet_id;
        $this->data['inventory_id'] = $inventory_id;
        $this->data['history_status'] = $this->store_model->get("enum_stock_history_status")->result();
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->detail_inventory(array("start_date" => $start_date, "end_date" => $end_date, "outlet_id" => $outlet_id, "inventory_id" => $inventory_id,"store_id"=>$this->data['setting']['store_id'])); 





        $this->data['content'] .= $this->load->view('admin/report/report-detail-inventory', $this->data, true);
        $this->render('report');
    }

    public function get_summary_inventory_data()
    {
        $this->load->model("report_model");
        $start_period = $this->input->post('start_period');
        $end_period = $this->input->post('end_period');
        $start_date = date('d F Y', strtotime($start_period));
        $end_date = date('d F Y', strtotime($end_period));
        $outlet_id = $this->input->post("outlet_id");
        $inventory_id = $this->input->post("inventory_id");
        $this->data['is_print'] = FALSE;
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['store_id'] =  $this->data['setting']['store_id'];
        $this->data['outlet_id'] = $outlet_id;
        $this->data['inventory_id'] = $inventory_id;
        $this->data['history_status'] = $this->store_model->get("enum_stock_history_status")->result();
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
		$this->data['results'] = $this->report_model->summary_inventory(array("start_date" => $start_period, "end_date" => $end_period, "outlet_id" => $outlet_id, "inventory_id" => $inventory_id,"store_id"=>$this->data['setting']['store_id']));			
		$this->load->view('admin/report/report-summary-inventory-pdf', $this->data);
    }

	public function get_detail_inventory_data()
    {
        $this->load->model("report_model");
        $start_date = $this->input->post("start_date");
        $end_date = $this->input->post("end_date");
        $outlet_id = $this->input->post("outlet_id");
        $inventory_id = $this->input->post("inventory_id");
        $this->data['is_print'] = FALSE;
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['store_id'] =  $this->data['setting']['store_id'];
        $this->data['outlet_id'] = $outlet_id;
        $this->data['inventory_id'] = $inventory_id;
        $this->data['history_status'] = $this->store_model->get("enum_stock_history_status")->result();
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
		$this->data['results'] = $this->report_model->detail_inventory(array("start_date" => $start_date, "end_date" => $end_date, "outlet_id" => $outlet_id, "inventory_id" => $inventory_id,"store_id"=>$this->data['setting']['store_id']));			
		$this->load->view('admin/report/report-detail-inventory-pdf', $this->data);
    }
    public function cost_opname()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));
        $this->data['title']    = "Laporan Cost Opname";
        $this->data['subtitle'] = "Laporan Cost Opname";
        $this->load->model("inventory_model");
        $this->data['all_inventories'] = $this->inventory_model->get_inventory_convertion_drop_down();
        $this->data['data_url'] = base_url(SITE_ADMIN . '/reports/get_cost_opname');



        $inventory_id = $this->input->post('inventory_id');
        $start_date=date("Y-m-d")." 00:00";
        $end_date=date("Y-m-d")." 23:59";
        $this->data['is_print'] = FALSE;
        $this->load->model("inventory_model");      
        $results=$this->inventory_model->cost_opname(array("inventory_id"=>$inventory_id, "start_date"=>$start_date,"end_date"=>$end_date));
        $this->data['inventories']=$results;




        $this->data['content'] .= $this->load->view('admin/report/report-cost-opname', $this->data, true);
        $this->render('report');
    }

    public function get_cost_opname()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));
        $inventory_id = $this->input->post('inventory_id');
        $start_date=$this->input->post("start_date");
        $end_date=$this->input->post("end_date");
        $this->data['is_print'] = FALSE;
        $this->load->model("inventory_model");      
        $results=$this->inventory_model->cost_opname(array("inventory_id"=>$inventory_id, "start_date"=>$start_date,"end_date"=>$end_date));
        $this->data['inventories']=$results;
        $this->load->view('admin/report/report-cost-opname-pdf', $this->data);
    }

    public function taxes()
    {
        $this->data['title'] = "Laporan Pajak & Service";
        $this->data['subtitle'] = "Laporan Pajak & Service";
       
        $store_id = $this->input->post("store_id");
        $start_date = date("Y-m-d");
        $end_date = date("Y-m-d");
        $tax_name = $this->input->post("tax_name");
        $this->load->model("report_model");
        $this->data['is_print'] = FALSE;
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->taxes(array("start_date" => $start_date, "end_date" => $end_date, "tax_name" => strtolower($tax_name)));
        $this->data['taxes'] = $this->store_model->get('taxes')->result();

        $this->data['content'] .= $this->load->view('admin/report/report-taxes-year', $this->data, true);
        $this->render('report');
    }

    public function get_taxes_year_data()
    {
        $this->load->model("report_model");
        $start_date = $this->input->post("start_date");
        $end_date = $this->input->post("end_date");
        $tax_name = $this->input->post("tax_name");
        $this->data['is_print'] = FALSE;
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->taxes(array("start_date" => $start_date, "end_date" => $end_date, "tax_name" => strtolower($tax_name)));
        $this->load->view('admin/report/report-taxes-year-pdf', $this->data);
    }

    public function summary_years()
    {
        $this->data['title'] = "Laporan Summary Per Tahun";
        $this->data['subtitle'] = "Laporan Summary Per Tahun";

        $year = date("Y");
        $this->load->model("report_model");
        $this->data['is_print'] = FALSE;
        $this->data['year'] = $year;
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->summary_year(array("year" => $year));


        $this->data['content'] .= $this->load->view('admin/report/report-summary-year', $this->data, true);
        $this->render('report');


    }

    public function get_summary_year_data()
    {
        $this->load->model("report_model");
        $year = $this->input->post("year");
        $this->data['is_print'] = FALSE;
        $this->data['year'] = $year;
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->summary_year(array("year" => $year));
        $this->load->view('admin/report/report-summary-year-pdf', $this->data);
    }

    public function refunds()
    {
        $this->data['title'] = "Laporan Refund";
        $this->data['subtitle'] = "Laporan Refund";
        $this->data['data_url'] = base_url(SITE_ADMIN . '/reports/get_refund_data');
        $this->data['content'] .= $this->load->view('admin/report/report-refund', $this->data, true);
        $this->render('report');
    }

    public function get_refund_data()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $post_array = array();
        parse_str($this->input->post('param'), $post_array);

        $start_date = $post_array['start_date'];
        $end_date = $post_array['end_date'];
        $store_id = $this->data['setting']['store_id'];

        $this->datatables->select('r.id, r.created_at, u.name, r.refund_key')
            ->from('refund r')
            ->join('bill b', 'b.refund_key = r.refund_key')
            ->join("users u", "r.created_by = u.id", 'left')
            ->where('b.is_refund', 0);
        if ($start_date) {
            $this->datatables->where('r.created_at >= ', $start_date);
        }

        if ($end_date) {
            $this->datatables->where('r.created_at <= ', $end_date);
        }

        $this->datatables->add_column('cost_refund', '$1', 'get_cost_refund(id)');

        $this->datatables->add_column('actions', "<div class='btn-group'>
        <a href='" . base_url(SITE_ADMIN . '/reports/detail_refund/$1') . "' target='_blank' class='btn btn-default' rel='tooltip' data-tooltip='tooltip' target='_blank' title='Detail'>
        <i class='fa fa-search'></i></a>", 'id');
        echo $this->datatables->generate();
    }

    public function detail_refund($id = 0)
    {
        $refund = $this->store_model->get_one("refund", $id);
        if (sizeof($refund) == 0) redirect(base_url(SITE_ADMIN . "reports/refund"));

        // get bill before refund
        $old_bill = array_pop($this->bill_model->get_all_where('bill', array('is_refund' => 1, 'refund_key' => $refund->refund_key)));
        $old_bill_menu = $this->bill_model->get_all_where('bill_menu', array('bill_id' => $old_bill->id));
        $old_bill_information = $this->bill_model->get_all_where('bill_information', array('bill_id' => $old_bill->id));
        $old_bill_payment = $this->bill_model->get_all_where('bill_payment', array('bill_id' => $old_bill->id));
        $waiter = $this->store_model->get_one("users", $old_bill->created_by);
        $cashier = $this->store_model->get_one("users", $old_bill->created_by);
        $table = $this->store_model->get_one("table", $old_bill->table_id);
        $old_bill->waiter_name = $waiter->name;
        $old_bill->cashier_name = $cashier->name;
        $old_bill->table_name = (!empty($table) ? $table->table_name : "");
        foreach ($old_bill_payment as $bp) {
            $payment_option = $this->store_model->get_one("enum_payment_option", $bp->payment_option);
            $bp->payment_option_name = $payment_option->value;
        }

        // get bill after refund
        $new_bill = array_pop($this->bill_model->get_all_where('bill', array('is_refund' => 0, 'refund_key' => $refund->refund_key)));
        if (empty($new_bill)) {
            $new_bill = array_pop($this->bill_model->get_all_where('bill', array('created_at' => $refund->created_at)));
        }
        $new_bill_menu = $this->bill_model->get_all_where('bill_menu', array('bill_id' => $new_bill->id));
        $new_bill_information = $this->bill_model->get_all_where('bill_information', array('bill_id' => $new_bill->id));
        $new_bill_payment = $this->bill_model->get_all_where('bill_payment', array('bill_id' => $new_bill->id));
        $waiter = $this->store_model->get_one("users", $new_bill->created_by);
        $cashier = $this->store_model->get_one("users", $new_bill->created_by);
        $table = $this->store_model->get_one("table", $new_bill->table_id);
        $new_bill->waiter_name = $waiter->name;
        $new_bill->cashier_name = $cashier->name;
        $new_bill->table_name = (!empty($table) ? $table->table_name : "");
        foreach ($new_bill_payment as $bp) {
            $payment_option = $this->store_model->get_one("enum_payment_option", $bp->payment_option);
            $bp->payment_option_name = $payment_option->value;
        }

        $this->data['old_bill'] = $old_bill;
        $this->data['old_bill_menu'] = $old_bill_menu;
        $this->data['old_bill_information'] = $old_bill_information;
        $this->data['old_bill_payment'] = $old_bill_payment;
        
        $this->data['new_bill'] = $new_bill;
        $this->data['new_bill_menu'] = $new_bill_menu;
        $this->data['new_bill_information'] = $new_bill_information;
        $this->data['new_bill_payment'] = $new_bill_payment;
        
        $this->data['title'] = "Detail Refund";
        $this->data['data_store'] = $this->store_model->get_store($this->data['setting']['store_id']);
        $this->data['content'] .= $this->load->view('admin/report/report-refund-detail', $this->data, true);
        $this->render('report');
    }

    public function petty_cash()
    {
        $this->data['title'] = "Laporan Kas Kecil";
        $this->data['subtitle'] = "Laporan Kas Kecil";
        $this->load->model("petty_cash_model");
        $this->data['all_ge'] = $this->petty_cash_model->get_general_expenses();
        $this->data['data_url'] = base_url(SITE_ADMIN . '/reports/get_petty_cash_data');
        $this->data['content'] .= $this->load->view('admin/report/report-petty-cash', $this->data, true);
        $this->render('report');
    }

    public function get_petty_cash_data()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $post_array = array();
        parse_str($this->input->post('param'), $post_array);
        

        //$store_id = $post_array['store_id'];
        $ge_id = $post_array['ge_id'];
        $start_date = $post_array['start_date'];
        $end_date = $post_array['end_date'];

        $this->datatables->select('pc.id,pc.date,pc.description,pc.amount,u.name, ge.name as gename')
        ->unset_column("amount")
        ->add_column("amount","$1","convert_rupiah(amount)")
        ->from('petty_cash pc')
         ->join("users u","pc.user_id=u.id",'left')
         //->join("store s","pc.store_id=s.id")
         ->join("general_expenses ge", "pc.ge_id = ge.id");
        

        // if($store_id){
        //     $this->datatables->where('pc.store_id', $store_id);
        // }
        if($ge_id){
            $this->datatables->where('ge.id', $ge_id);
        }
        if($start_date){
            $this->datatables->where('pc.date >= ', $start_date);
        }

        if($end_date){
            $this->datatables->where('pc.date <= ', $end_date);
        }


        echo $this->datatables->generate();
        // $this->load->library(array('datatables'));
        // $this->load->helper(array('datatables'));
        // $where = array();
        // if ($_POST) {
        //     $data = $this->input->post();
        //     if ($data['columns'][0]['search']['value'] != "") {
        //         $where = array_merge($where, array("date(pc.date) >=" => $data['columns'][0]['search']['value']));
        //     }
        //     if ($data['columns'][1]['search']['value'] != "") {
        //         $where = array_merge($where, array("date(pc.date) <=" => $data['columns'][1]['search']['value']));
        //     }
        //     if ($data['columns'][2]['search']['value'] != "") {
        //         $where = array_merge($where, array("ge.id =" => $data['columns'][2]['search']['value']));
        //     }
        // }
        // $this->datatables->select('pc.id,pc.date,pc.description,pc.amount,u.name as user,ge.name')
        //     ->unset_column("amount")
        //     ->add_column("amount", "$1", "convert_rupiah(amount)")
        //     ->from('petty_cash pc')
        //     ->join("users u", "pc.user_id=u.id", 'left')
        //     ->join("general_expenses ge", "pc.ge_id = ge.id");
        // foreach ($where as $key => $value) {
        //     $this->datatables->where($key, $value);
        // }
        // echo $this->datatables->generate();
    }

    public function side_dish()
    {
        $this->data['title'] = "Laporan Side Dish";
        $this->data['subtitle'] = "Laporan Side Dish";
        $this->load->model('categories_model');
        $this->data['all_menus'] = $this->categories_model->get_menu_sd_dropdown();
        $this->data['data_url'] = base_url(SITE_ADMIN . '/reports/get_side_dish_data');
        $this->data['content'] .= $this->load->view('admin/report/report-side-dish', $this->data, true);
        $this->render('report');
    }

    public function get_side_dish_data()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $post_array = array();
        parse_str($this->input->post('param'), $post_array);
        

        //$store_id = $post_array['store_id'];
        $menu_id = $post_array['menu_id'];
        $start_date = $post_array['start_date'];
        $end_date = $post_array['end_date'];

        $this->datatables->select('bmsd.side_dish_name as sdname,bmsd.price as sdprice, m.menu_name as bmname, bl.receipt_number as blreceipt')

        ->unset_column("sdprice")
        ->add_column("sdprice","$1","convert_rupiah(sdprice)")

        ->from('bill_menu_side_dish bmsd')
        ->join("bill_menu bm","bm.id=bmsd.bill_menu_id")
        ->join("menu m","m.id=bm.menu_id")
        ->join("bill bl", "bl.id = bm.bill_id")
        ->where("bl.is_refund", 0);
        

        // if($store_id){
        //     $this->datatables->where('pc.store_id', $store_id);
        // }
        if($menu_id){
            $this->datatables->where('bm.menu_id', $menu_id);
        }
        if($start_date){
            $this->datatables->where('bl.created_at >= ', $start_date);
        }

        if($end_date){
            $this->datatables->where('bl.created_at <= ', $end_date);
        }

            $this->datatables->order_by('blreceipt', 'desc');

        echo $this->datatables->generate();
    }

    public function delete_order()
    {
        $this->data['title'] = "Laporan Delete Order";
        $this->data['subtitle'] = "Laporan Delete Order";
        $this->data['data_url'] = base_url(SITE_ADMIN . '/reports/get_delete_order_data');
        $this->data['content'] .= $this->load->view('admin/report/report-delete-order', $this->data, true);
        $this->render('report');
    }

    public function get_delete_order_data()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $post_array = array();
        parse_str($this->input->post('param'), $post_array);

        $start_date = $post_array['start_date'];
        $end_date = $post_array['end_date'];

        $this->datatables->select('oh.id,oh.created_at,u.name')
            ->from('order_history oh')
            ->join("users u", "oh.created_by=u.id", 'left');
        if ($start_date) {
            $this->datatables->where('oh.created_at >= ', $start_date);
        }

        if ($end_date) {
            $this->datatables->where('oh.created_at <= ', $end_date);
        }
        $this->datatables->order_by('oh.created_at', 'desc')->add_column('actions', "<div class='btn-group'>
        <a href='" . base_url(SITE_ADMIN . '/reports/detail_delete_order/$1') . "' target='_blank' class='btn btn-default' rel='tooltip' data-tooltip='tooltip' target='_blank' title='Detail'>
        <i class='fa fa-search'></i></a>", 'id');
        echo $this->datatables->generate();
    }

    public function detail_delete_order($id = 0)
    {
        $delete_order = $this->store_model->get_one("order_history", $id);
        if (sizeof($delete_order) == 0) redirect(base_url(SITE_ADMIN . "/reports/delete_order"));
        $data = json_decode($delete_order->data);
        if (sizeof($data->order) == 0) redirect(base_url(SITE_ADMIN . "/reports/delete_order"));
        $waiter = $this->store_model->get_one("users", $data->order->created_by);
        $table = $this->store_model->get_one("table", $data->order->table_id);
        $data->order->waiter_name = $waiter->name;
        $data->order->table_name = (!empty($table) ? $table->table_name : "");
        foreach ($data->order_menu as $bm) {
            $menu = $this->store_model->get_one("menu", $bm->menu_id);
            $bm->menu_name = $menu->menu_name;
        }
        $this->data['data'] = $data;
        $this->data['title'] = "Detail Delete Order";
        $this->data['data_store'] = $this->store_model->get_store($this->data['setting']['store_id']);
        $this->data['content'] .= $this->load->view('admin/report/report-delete-order-detail', $this->data, true);
        $this->render('report');
    }

    public function void()
    {
        $this->data['title'] = "Laporan Void";
        $this->data['subtitle'] = "Laporan Void";

        // $this->load->model('user_model');

        $this->data['data_url'] = base_url(SITE_ADMIN . '/reports/get_void_data');
        $this->data['content'] .= $this->load->view('admin/report/report-void', $this->data, true);
        $this->render('report');
    }

    public function get_void_data()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $post_array = array();
        parse_str($this->input->post('param'), $post_array);

        $start_date = $post_array['start_date'];
        $end_date = $post_array['end_date'];

        $where = "";
        if ($start_date && $end_date) {
            $where = 'where void.created_at >= "' . $start_date . '" AND void.created_at <= "' . $end_date . '" ';
        }


        $this->datatables->select(
            'void.id, 
            menu.menu_name, amount,void_note, 
            is_deduct_stock, users.name,void.created_at,u2.name as user_unlock_name,
            (select sum(amount) 
                from void
                ' . $where . '  
                ) as total_amount,
            menu.menu_hpp * amount as cost_void ') // menambahkan kolom cost void (nominal uang yang terbuang karena void)
            ->from('void')
            ->join('users', 'users.id = void.created_by')
            ->join('users u2', 'u2.id = void.user_unlock_id', 'left')
            ->join('menu', 'menu.id = void.order_menu_id')
            ->unset_column('is_deduct_stock')
            ->unset_column('created_at')
            ->unset_column('cost_void')
            ->add_column('cost_void', '$1', 'convert_rupiah(cost_void)')
            ->add_column('is_deduct_stock', '$1', 'convert_status_int(is_deduct_stock)')
            ->add_column('created_at', '$1', 'convert_local_time(created_at)');

        if ($start_date && $end_date) {
            $this->datatables->where('void.created_at >= ', $start_date);
            $this->datatables->where('void.created_at <= ', $end_date);
            $this->datatables->add_column('periode', '$1', 'transaction_periode(' . $start_date . ',' . $end_date . ' )');

        }


        echo $this->datatables->generate();

    }


    public function open_close_detail($id = 0)
    {
        $this->load->model("order_model");
        $this->data['title'] = "Detail Open Close";
        $this->data['subtitle'] = "Detail Open Close";
        $this->load->model('user_model');
        $this->data['open_close'] = $this->order_model->get_open_close_cashier_by_id($id);
        $this->data['balance_cash_history'] = $this->order_model->get_balance_cash_history($this->data['open_close']->open_at, $this->data['open_close']->close_at);
        $this->data['open_close_detail'] = $this->user_model->get_all_where("open_close_cashier_detail", array("open_close_cashier_id" => $id));
        $this->data['content'] .= $this->load->view('admin/report/report-openclose-detail', $this->data, true);
        $this->render('report');
    }

    public function open_close()
    {
        $this->data['title'] = "Laporan Open Close";
        $this->data['subtitle'] = "Laporan Open Close Kasir";

        $this->load->model('user_model');

        // $this->data['all_store']  = $this->store_model->get_all_store();
        $this->data['all_cashier'] = $this->user_model->get_user_dropdown(0, 3, $this->data['setting']['store_id']);
        $this->data['data_url'] = base_url(SITE_ADMIN . '/reports/get_open_close_data');
        $this->data['content'] .= $this->load->view('admin/report/report-openclose', $this->data, true);
        $this->render('report');
    }

    public function print_open_close($id = 0)
    {
        $this->load->model("order_model");
        $this->load->model("inventory_model");
        $data_open_close = $this->order_model->get_one("open_close_cashier", $id);
        if (sizeof($data_open_close) == 0) redirect(base_url(SITE_ADMIN . "reports/open_close"));

        $this->load->helper(array('printer'));

        $data_print = $this->order_model->get_oc_cashier(array("id" => $data_open_close->id));
        $data_print['cash']->amount -= ($data_print['petty_cash']->amount + $data_print['delivery']->amount);
        $data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $data_print['store_data'] = $this->data['data_store'];
        $data_print['setting'] = $this->data['setting'];

        //get printer cashier
        $this->load->model("setting_printer_model");
        $this->load->model("report_model");
        $printer_arr_obj = $this->setting_printer_model->get_printer_by_enum_printer_type(array("name_type"=>"printer_cashier"));
        
        foreach ($printer_arr_obj as $printer_obj) {            
            $printer_location = $printer_obj->name_printer;
            //build object printer setting for printer helper
            $printer_setting = new stdClass();
            $printer_setting->id = $printer_obj->id;
            $printer_setting->name = $printer_obj->alias_name;
            $printer_setting->value = $printer_obj->name_printer;
            $printer_setting->default = $printer_obj->logo;
            $printer_setting->description = $printer_obj->printer_width;
            $printer_setting->font_size = $printer_obj->font_size;
            
            if ($this->data['setting']['open_close_format'] == 1) {
                if ($printer_obj->printer_width == 'generic') {
                    print_open_close_bill_mode2_generic($printer_location, $data_print, $this->data['user_profile_data'], $printer_setting);
                } else {
                    print_open_close_bill_mode2($printer_location, $data_print, $this->data['user_profile_data'], $printer_setting);
                }                
            } elseif ($this->data['setting']['open_close_format'] == 2) {
                $data_print['cash']->amount += ($data_print['petty_cash']->amount + $data_print['delivery']->amount);
                if ($printer_obj->printer_width == 'generic') {
                    print_open_close_bill_mode3_generic($printer_location, $data_print, $this->data['user_profile_data'], $printer_setting);
                } else {
                    print_open_close_bill_mode3($printer_location, $data_print, $this->data['user_profile_data'], $printer_setting);
                }                
            }elseif ($this->data['setting']['open_close_format'] == 3) {
                $data_print['cash']->amount += ($data_print['petty_cash']->amount + $data_print['delivery']->amount);
                if ($printer_obj->printer_width == 'generic') {
                    print_open_close_bill_mode4_generic($printer_location, $data_print, $this->data['user_profile_data'], $printer_setting);
                } else {
                    print_open_close_bill_mode4($printer_location, $data_print, $this->data['user_profile_data'], $printer_setting);
                }                
            } else {
                $data_print['cash']->amount += ($data_print['petty_cash']->amount + $data_print['delivery']->amount);
                if ($printer_obj->printer_width == 'generic') {
                    print_open_close_bill_mode_generic($printer_location, $data_print, $this->data['user_profile_data'], $printer_setting);
                } else {
                    print_open_close_bill_mode($printer_location, $data_print, $this->data['user_profile_data'], $printer_setting);
                }                
            }

            //auto print report product
            if($this->data['setting']['auto_print_report_product']){
                $printer_setting->printer_width = $printer_obj->printer_width;
                // $date = date('Y-m-d');
                $where = array(
                    "bill_menu.created_at >="=>$data_print['oc_cashier']->open_at,
                    "bill_menu.created_at <="=>$data_print['oc_cashier']->close_at
                ); 
                $data_print['report_products'] = $this->report_model->get_report_product($where);
                if($data_print['report_products']){
                print_report_product($printer_location, $data_print, $this->data['user_profile_data'], $printer_setting);    
                }
                   
            }

            //auto print report pettycash
            if($this->data['setting']['auto_print_report_pettycash']){
                $printer_setting->printer_width = $printer_obj->printer_width;
                // $date = date('Y-m-d');
                $where = array(
                    "date >="=>$data_print['oc_cashier']->open_at,
                    "date <="=>$data_print['oc_cashier']->close_at
                ); 
                $data_print['report_pettycashs'] = $this->report_model->get_report_pettycash($where);
                if($data_print['report_pettycashs']){
                print_report_pettycash($printer_location, $data_print, $this->data['user_profile_data'], $printer_setting);   
                }
                
            }

            //auto print report stock
            if($this->data['setting']['auto_print_report_stock']){
                $printer_setting->printer_width = $printer_obj->printer_width;
                // $date = date('Y-m-d');
                $where = array(
                    "store_id" => $this->data['setting']['store_id'], 
                    "start_date" => $data_print['oc_cashier']->open_at, 
                    "end_date" => $data_print['oc_cashier']->close_at
                ); 
 
                $data_print['report_stocks'] = $this->inventory_model->get_daily_inventory_stock_data($where);
                if( $data_print['report_stocks']){
                print_report_stock($printer_location, $data_print, $this->data['user_profile_data'], $printer_setting);    
                } 
            }
        }

        redirect(base_url(SITE_ADMIN."/reports/open_close"));
    }

    public function strest_test_printer()
    {

        echo date('h:i:s') . "<br>";

        //sleep for 5 seconds
        sleep(50);

        //start again
        echo date('h:i:s');

    }

    public function get_open_close_data()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $post_array = array();
        parse_str($this->input->post('param'), $post_array);

        $open_by = $post_array['open_by'];
        $close_by = $post_array['close_by'];
        $start_date = $post_array['start_date'];
        $end_date = $post_array['end_date'];

        $this->datatables->select('open_close_cashier.id, open_at, open_by, close_by,
            (SELECT name from users where users.id = open_by) as open_by_user, 
            (SELECT name from users where users.id = close_by) as  close_by_user, 
            close_at, total_transaction, total_cash')
            ->from('open_close_cashier')
            ->unset_column('open_at')
            ->unset_column('open_at')
            ->unset_column('total_cash')
            ->add_column('total_cash', '$1', 'convert_rupiah(total_cash)')
            ->add_column('open_at', '$1', 'convert_date_with_time(open_at)')
            ->add_column('close_at', '$1', 'convert_date_with_time(close_at)')
            ->add_column('actions', "<div class='btn-group'>
					<a href='" . base_url(SITE_ADMIN . '/reports/print_open_close/$1') . "' class='btn btn-default print_open_close_cashier' rel='tooltip' data-tooltip='tooltip' title='Cetak'><i class='fa fa-print'></i></a>
					<a target='_blank' href='" . base_url(SITE_ADMIN . '/reports/open_close_detail/$1') . "' class='btn btn-default' rel='tooltip' data-tooltip='tooltip' title='Detail'>Detail</a>
				</div>", 'id');

        if ($start_date) {
            $this->datatables->where('open_at >= ', $start_date);
        }

        if ($end_date) {
            $this->datatables->where('close_at <= ', $end_date);
        }

        if ($open_by) {
            $this->datatables->where('open_by', $open_by);
        }

        if ($close_by) {
            $this->datatables->where('close_by', $close_by);
        }


        echo $this->datatables->generate();

    }

    public function transaction()
    {
        $this->data['title'] = "Laporan Transaksi";
        $this->data['subtitle'] = "Laporan Transaksi";

        $this->load->model('user_model');
        $this->data['order_types'] = array(1 => "Dine In", 2 => "Take Away", 3 => "Delivery");
        $this->data['all_cashier'] = $this->user_model->get_user_dropdown(0, false, $this->data['setting']['store_id']); //3 untuk group kasir
        $this->data['data_url'] = base_url(SITE_ADMIN . '/reports/get_transaction_data');
        $this->load->model('order_model');
        $this->data['payment_options'] = $this->order_model->get_enum_payment_option();
        $this->data['content'] .= $this->load->view('admin/report/report-transaction', $this->data, true);
        $this->render('report');
    }

    public function get_summary_transaction()
    {
        $this->load->model("order_model");
        $post_array = $this->input->post();
        $user_id = $post_array['user_id'];
        $start_date = $post_array['start_date'];
        $end_date = $post_array['end_date'];
        $order_type = $post_array['order_type'];
        $payment_option = $post_array['payment_option'];
        $this->data['summary'] = $this->order_model->get_summary_transaction(array(
            "user_id" => $user_id,
            "start_date" => $start_date,
            "end_date" => $end_date,
            "order_type" => $order_type,
            "payment_option" => $payment_option,
        ));
        $this->data['params'] = array(
            "user_id" => $user_id,
            "start_date" => $start_date,
            "end_date" => $end_date,
            "order_type" => $order_type,
            "payment_option" => $payment_option,
        );
        $this->data['summary'] = (object)$this->data['summary'];
        $content = $this->load->view("admin/report/summary-transaction-table", $this->data, true);
        echo json_encode(array(
            "content" => $content
        ));
    }

    public function get_transaction_data()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $post_array = array();
        parse_str($this->input->post('param'), $post_array);

        $user_id = $post_array['user_id'];
        $start_date = $post_array['start_date'];
        $end_date = $post_array['end_date'];
        $order_type = $post_array['order_type'];
        $payment_option = $post_array['payment_option'];
        $this->datatables->select('
					receipt_number as id, 
					bill.id as bill_id, 
					bill.order_id,
					receipt_number, payment_date, 
					cashier_id,
					bill.is_take_away as order_type, 
					bill.is_delivery as is_delivery,
					total_price, total_price as total_price_rp, 
					total_cogs,  total_cogs as total_cogs_rp ,
					customer_count
        ', false)
            ->from('bill')
            ->join("bill_payment", "bill.id=bill_payment.bill_id")
            ->where('bill.is_refund', 0)
            ->group_by("bill.id")
            ->unset_column('payment_date')
            ->add_column('order_type', '$1', 'convert_order_type(order_type,is_delivery)')
            ->add_column('profit_rp', '$1', 'convert_rupiah(profit)')
            ->add_column('total_price_rp', '$1', 'convert_rupiah(total_price)')
            ->add_column('total_cogs_rp', '$1', 'convert_rupiah(total_cogs)')
            ->add_column('payment_date', '$1', 'convert_date_with_time(payment_date)')
            ->add_column('actions', "<div class='btn-group'>
                                    <a href='" . base_url(SITE_ADMIN . '/reports/detail_transaction/$1') . "'  
                                    class='btn btn-default'
                                    rel='tooltip' data-tooltip='tooltip' target='_blank' title='Detail'
                                    >
                                    <i class='fa fa-search'></i></a>", 'id');

        if ($payment_option != "") {
            $this->datatables->where("bill_payment.payment_option", $payment_option);
        }

        if ($order_type != "") {
            if ($order_type == 1) {
                $this->datatables->where("bill.is_delivery", 0);
                $this->datatables->where("bill.is_take_away", 0);
            } elseif ($order_type == 2) {
                $this->datatables->where("bill.is_delivery", 0);
                $this->datatables->where("bill.is_take_away", 1);
            } elseif ($order_type == 3) {
                $this->datatables->where("bill.is_delivery", 1);
                $this->datatables->where("bill.is_take_away", 0);
            }
        }

        if ($start_date) {
            $this->datatables->where('payment_date >= ', $start_date);
        }

        if ($end_date) {
            $this->datatables->where('payment_date <= ', $end_date);
        }
        if ($user_id) {
            $this->datatables->where('cashier_id', $user_id);
        }


        echo $this->datatables->generate();

    }


    public function detail_transaction($receipt_number)
    {

        $this->data['title'] = "Detail Transaksi";
        $this->data['subtitle'] = "Detail Transaksi";

        $this->load->helper(array('datatables'));

        $this->load->model('order_model');

        $data['bill_detail'] = $this->order_model->get_bill_detail($receipt_number);
        $data['bill_detail']->store = $this->order_model->get('store')->row()->store_name;
        // $data            = $this->list_order_payment($data_order_menu);

        $data['url_transaction_order'] = base_url(SITE_ADMIN . '/reports/get_data_transaction_order/' . $data['bill_detail']->bill_id);

        $data['url_transaction_sidedish'] = base_url(SITE_ADMIN . '/reports/get_data_transaction_sidedish/' . $data['bill_detail']->bill_id);

        $data['url_transaction_minus'] = base_url(SITE_ADMIN . '/reports/data_transaction_minus/' . $data['bill_detail']->bill_id);

        $data['url_transaction_plus'] = base_url(SITE_ADMIN . '/reports/data_transaction_plus/' . $data['bill_detail']->bill_id);

        $data['url_transaction_payment'] = base_url(SITE_ADMIN . '/reports/data_transaction_payment/' . $data['bill_detail']->bill_id);

        $this->data['content'] .= $this->load->view('admin/report/report-detail-transaction', $data, true);
        $this->render('report');

    }

    public function get_data_transaction_order($bill_id)
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $this->datatables->select('menu_id as id, menu.menu_name as menu_name, sum(quantity) as quantity, price,
            sum((price*quantity)) as subtotal,
            sum(cogs) as cogs')
            ->from('bill_menu')
            ->join('menu','menu.id=bill_menu.menu_id')
            ->where('bill_id', $bill_id)
            ->group_by('menu_id')
            ->unset_column('price')
            ->unset_column('subtotal')
            ->unset_column('cogs')
            ->add_column('price', '$1', 'convert_rupiah(price)')
            ->add_column('subtotal', '$1', 'convert_rupiah(subtotal)')
            ->add_column('cogs', '$1', 'convert_rupiah(cogs)');
        // ->add_column('actions', "<div class='btn-group'>
        //                             <a href='" . base_url(SITE_ADMIN . '/reports/detail_transaction/$1') . "'  
        //                             class='btn btn-default'
        //                             rel='tooltip' data-tooltip='tooltip' target='_blank' title='Detail'
        //                             >
        //                             <i class='fa fa-search'></i></a>", 'id');

        echo $this->datatables->generate();

    }

    public function get_data_transaction_sidedish($bill_id)
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $this->datatables->select('bmsd.side_dish_name as sdname, bmsd.price as sdprice')
            ->from('bill_menu_side_dish bmsd')
            ->join('bill_menu bm', 'bm.id=bmsd.bill_menu_id')
            ->where('bm.bill_id', $bill_id)
            ->unset_column('sdprice')
            ->add_column('sdprice', '$1', 'convert_rupiah(sdprice)');

        echo $this->datatables->generate();

    }

    public function summary_of_sales(){
        $this->data['title']    = "Summary of Sales";
        $this->data['subtitle'] = "Summary of Sales";
        $this->data['data_url'] = base_url(SITE_ADMIN . '/reports/get_sos_data');
        $this->data['content'] .= $this->load->view('admin/report/report_summary_of_sales', $this->data, true);
        $this->render('report');
    }

    public function get_sos_data(){
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $post_array = array();
        parse_str($this->input->post('param'), $post_array);

        $user_id = FALSE;
        $start_date = $post_array['start_date'];
        $end_date = $post_array['end_date'];

        $jsonObj = $this->get_sos_raw_data($start_date, $end_date,true);
        echo json_encode($jsonObj);
    }

    public function get_sos_raw_data($start_date, $end_date, $convert_rupiah=false){
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));
        $this->load->model('bill_model');
        $this->load->model('bill_information_model');

        $where_clause = array();
        if($start_date && $end_date){
            $where_clause["payment_date >="] = $start_date;
            $where_clause["payment_date <="] = $end_date;
        }

        // get bill data with filtering
        $bill_data_result = $this->bill_model->get_sales_of_sales(array('start_date'=>$start_date, 'end_date'=>$end_date));
        $recordsTotal = count($bill_data_result);
        $all_bill_data_array = array();

        if($bill_data_result){
            foreach ($bill_data_result as $bill_data) {
                $bill_data_array = array();
                $bill_data_array['no_bill'] = $bill_data->receipt_number;
                $bill_data_array['qty'] = $bill_data->qty;

                $bill_info_array = $this->bill_information_model->get_all(array('bill_id' => $bill_data->id));
                $tax = 0;
                $service = 0;
                $discount = 0;
                $pembulatan = 0;
                foreach ($bill_info_array as $bill_info) {
                    if($bill_info->type == 2){
                        $discount += $bill_info->amount;
                    }else if(strpos(strtolower($bill_info->info), 'service') !== false){
                        $service += $bill_info->amount;
                    }else if(strpos(strtolower($bill_info->info), 'pembulatan') !== false){
                        $pembulatan += $bill_info->amount;
                    }else  if($bill_info->type == 1 && !strpos(strtolower($bill_info->info), 'pembulatan')){
                        $tax += $bill_info->amount;                        
                    }
                }
                if($convert_rupiah){
                    $bill_data_array['cost'] = convert_rupiah($bill_data->total_cogs);
                    $bill_data_array['gross'] = convert_rupiah($bill_data->total_price);
                    $bill_data_array['nett'] = convert_rupiah($bill_data->total_price - $tax - $service + $discount);
                    $bill_data_array['discount'] = convert_rupiah($discount);
                    $bill_data_array['tax'] = convert_rupiah($tax);
                    $bill_data_array['service'] = convert_rupiah($service);
                    $bill_data_array['net_tax'] = convert_rupiah(($bill_data->total_price - $tax - $service + $discount) + $tax);
                }else{
                    $bill_data_array['cost'] = $bill_data->total_cogs;
                    $bill_data_array['gross'] = $bill_data->total_price;
                    $bill_data_array['nett'] = ($bill_data->total_price - $tax - $service + $discount);
                    $bill_data_array['discount'] = ($discount);
                    $bill_data_array['tax'] = ($tax);
                    $bill_data_array['service'] = ($service);
                    $bill_data_array['net_tax'] = ($bill_data->total_price - $tax - $service + $discount) + $tax;
                }
                $bill_data_array['actions'] = "<div class='btn-group'>
                                    <a href='". base_url(SITE_ADMIN . '/reports/sos_details/' . $bill_data->id) ."'  
                                    class='btn btn-default'
                                    rel='tooltip' data-tooltip='tooltip' target='_blank' title='Detail'
                                    >
                                    <i class='fa fa-search'></i></a>";

                $bill_data_array = (object)$bill_data_array;
                array_push($all_bill_data_array, $bill_data_array);
            }
        }
        $jsonObj = new StdClass();
        $jsonObj->draw = 1;
        $jsonObj->recordsTotal = $recordsTotal;
        $jsonObj->recordsFiltered = $recordsTotal;
        $jsonObj->data = $all_bill_data_array;

        return $jsonObj;
    }

    function data_transaction_minus($bill_id)
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $this->datatables->select('id, info, amount')
            ->from('bill_information')
            ->where('type', 2)
            ->where('bill_id', $bill_id)
            ->unset_column('amount')
            ->add_column('amount', '$1', 'convert_rupiah(amount)');

        echo $this->datatables->generate();
    }

    function data_transaction_plus($bill_id)
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $this->datatables->select('id, info, amount')
            ->from('bill_information')
            ->where('type', 1)
            ->where('bill_id', $bill_id)
            ->unset_column('amount')
            ->add_column('amount', '$1', 'convert_rupiah(amount)');

        echo $this->datatables->generate();
    }


    function data_transaction_payment($bill_id)
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $this->datatables->select('bill_payment.id, 
            info, amount, 
            enum_payment_option.value as payment_option')
            ->join('enum_payment_option', 'bill_payment.payment_option = enum_payment_option.id')
            ->from('bill_payment')
            ->where('bill_id', $bill_id)
            ->unset_column('amount')
            ->add_column('amount', '$1', 'convert_rupiah(amount)');

        echo $this->datatables->generate();

    }

    function sales_category()
    {
        $this->data['title'] = "Laporan Penjualan Per Kategori";
        $this->data['subtitle'] = "Laporan Penjualan Per Kategori";

        $this->load->model('categories_model');

        $this->data['all_outlet'] = $this->categories_model->get_outlet_dropdown_by_store_id($this->data['setting']['store_id']);
		$outlet = $this->categories_model->get_outlet($this->data['setting']['store_id']);
        $this->data['all_category'] = $this->categories_model->get_category_dropdown(0); //3 untuk group kasir
        $this->data['data_url'] = base_url(SITE_ADMIN . '/reports/get_sales_category_data');
        $this->data['content'] .= $this->load->view('admin/report/report-sales-category', $this->data, true);
        $this->render('report');
    }
    
    // Report Penjualan Per Outlet
     
     
    function sales_outlet(){
        $this->data['title']    = "Laporan Penjualan Per ".$this->lang->line('outlet_title');
        $this->data['subtitle'] = "Laporan Penjualan Per ".$this->lang->line('outlet_title');

        $this->load->model('categories_model');

        $this->data['all_category'] = $this->categories_model->get_category_dropdown(0); //3 untuk group kasir
        $this->data['data_url'] = base_url(SITE_ADMIN . '/reports/get_sales_outlet_data');
        $this->data['content'] .= $this->load->view('admin/report/report-sales-outlet', $this->data, true);
        $this->render('report');
    }

    // Penganmbilan data report penjualan per menu

    function get_sales_outlet_data(){
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $post_array = array();
        parse_str($this->input->post('param'), $post_array);

        $start_date = $post_array['start_date'];
        $end_date = $post_array['end_date'];

        $where = "";
        if($start_date && $end_date){
            $where = 'where bill.created_at >= "'.date("Y-m-d H:i:s",strtotime($start_date)).'" 
            AND bill.created_at <= "'.date("Y-m-d H:i:s",strtotime($end_date)).'" ';
        }

        $this->datatables->select('
            menu.id  as id, 
            menu_id, 
            menu.menu_name, 
            sum(quantity) as total_quantity, 
            sum(price*quantity) as total_price, 
            sum(cogs*quantity) as total_cogs, 
            sum(price*quantity)-sum(cogs*quantity) as profit, 
            category_name,
            outlet_name,
            bill.created_at,
            (SELECT sum(quantity)                 
                from bill_menu
                join bill on bill.id = bill_menu.bill_id and bill.is_refund = 0
                join menu on menu.id = bill_menu.menu_id
                join category on category.id =  menu.category_id
                '.$where.' and bill.is_refund = 0
                ) as sum_total_quantity,

            (SELECT sum(price*quantity)
                from bill_menu
                join bill on bill.id = bill_menu.bill_id and bill.is_refund = 0
                join menu on menu.id = bill_menu.menu_id
                join category on category.id =  menu.category_id
                '.$where.' and bill.is_refund = 0
                ) as sum_total_price,

            (SELECT sum(cogs*quantity)                 
                from bill_menu
                join bill on bill.id = bill_menu.bill_id and bill.is_refund = 0
                join menu on menu.id = bill_menu.menu_id
                join category on category.id =  menu.category_id
                '.$where.' and bill.is_refund = 0
                ) as sum_total_cogs,

            (SELECT (sum(price*quantity)-sum(cogs*quantity))              
                from bill_menu
                join bill on bill.id = bill_menu.bill_id and bill.is_refund = 0
                join menu on menu.id = bill_menu.menu_id
                join category on category.id =  menu.category_id
                '.$where.' and bill.is_refund = 0
                ) as sum_profit
            ')
            ->join('bill', 'bill.id = bill_menu.bill_id')
            ->join('menu', 'menu.id = bill_menu.menu_id')
            ->join('category', 'category.id = menu.category_id','left')
            ->join('outlet', 'outlet.id = category.outlet_id','left')
            
            ->from('bill_menu')
            ->where('bill.is_refund', 0)
            ->group_by('category.outlet_id')

            ->unset_column('created_at')

            ->add_column('created_at', '$1', 'convert_date(created_at)')
            ->add_column('total_price', '$1', 'convert_rupiah(total_price)')
            ->add_column('total_cogs', '$1', 'convert_rupiah(total_cogs)')
            ->add_column('total_profit', '$1', 'convert_rupiah(profit)');

        if($start_date && $end_date){
            $this->datatables->where('bill.created_at >= ', date("Y-m-d H:i:s",strtotime($start_date)));
            $this->datatables->where('bill.created_at <= ', date("Y-m-d H:i:s",strtotime($end_date)));
            $this->datatables->add_column('periode', '$1', 'transaction_periode('.$start_date.','.$end_date.' )');
        }

        echo $this->datatables->generate();
    }

    function sales_menu()
    {
        $this->data['title'] = "Laporan Penjualan Per Menu";
        $this->data['subtitle'] = "Laporan Penjualan Per Menu";
        $this->load->model('categories_model');
        $this->data['all_category'] = $this->categories_model->get_outlet($this->data['setting']['store_id']); //3 untuk group kasir
        $this->data['all_menus'] = $this->categories_model->get_menu_dropdown();
        $this->data['data_url'] = base_url(SITE_ADMIN . '/reports/get_sales_menu_data');
        $this->data['content'] .= $this->load->view('admin/report/report-sales-menu', $this->data, true);
        $this->render('report');
    }

    function moving_item()
    {
        $this->data['title'] = "Laporan Moving Item";
        $this->data['subtitle'] = "Laporan Moving Item";

        $this->load->model('categories_model');

        $this->data['all_category'] = $this->categories_model->get_outlet($this->data['setting']['store_id']); //3 untuk group kasir
        $this->data['data_url'] = base_url(SITE_ADMIN . '/reports/get_moving_item_data');
        $this->data['content'] .= $this->load->view('admin/report/report-moving-item', $this->data, true);
        $this->render('report');
    }

	/*
	* get sales category data
	* input:
	* - outlet_id
	* - category_menu_id
	* - start_date
	* - end_date
	*/
    function get_sales_category_data()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));
        $post_array = array();
        
		parse_str($this->input->post('param'), $post_array);
		
        $store_id = $this->data['setting']['store_id'];
        $start_date = $post_array['start_date'];
        $end_date = $post_array['end_date'];
        $outlet_id = $post_array['outlet_id'];
        $category_menu_id = $post_array['category_menu_id'];
        $this->datatables->select('
			store.store_name,outlet.outlet_name,bill_menu.created_at,category.category_name,sum(bill_menu.quantity) as total_quantity,
			sum(bill_menu.quantity*bill_menu.price) as total_price
        ')
        ->from('bill_menu')
        ->join('bill', 'bill.id = bill_menu.bill_id')
        ->join('menu', 'menu.id = bill_menu.menu_id')
        ->join('category', 'category.id = menu.category_id')
        ->join('outlet', 'outlet.id = category.outlet_id')
        ->join('store', 'store.id = outlet.store_id')
        ->where('bill.is_refund', 0)
        ->group_by('store.id,outlet.id,category.id,date(bill_menu.created_at)')
        ->unset_column('created_at')
        ->add_column('created_at', '$1', 'convert_date(created_at)')
        ->add_column('total_price', '$1', 'convert_rupiah(total_price)');
        if($start_date && $end_date){
			$this->datatables->where('bill.created_at >= ', date("Y-m-d H:i:s",strtotime($start_date)));
			$this->datatables->where('bill.created_at <= ', date("Y-m-d H:i:s",strtotime($end_date)));
        }
        if($store_id){
            $this->datatables->where('store.id', $store_id);
        }
		if($outlet_id){
            $this->datatables->where('outlet.id', $outlet_id);
        }
		if($category_menu_id){
            $this->datatables->where('category.id', $category_menu_id);
        }
        echo $this->datatables->generate();
    }

    function get_moving_item_data()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $post_array = array();
        parse_str($this->input->post('param'), $post_array);

        $category_id = $post_array['category_id'];
        $start_date = $post_array['start_date'];
        $end_date = $post_array['end_date'];


        $where = "";
        if ($start_date && $end_date) {
            $where = 'where bill_menu.created_at >= "' . $start_date . '" 
                        AND bill_menu.created_at <= "' . $end_date . '" ';
            if ($category_id) {
                $where .= ' AND outlet_id = ' . $category_id;
            }
        }

        if ($category_id && !$start_date) {
            $where = 'where outlet_id = ' . $category_id;
        }

        $this->datatables->select('menu.id as id, 
            menu_id, menu.menu_name, 
            sum(quantity) as total_quantity, 
            sum(price*quantity) as total_price,
            price as harga_menu,
            cogs as harga_hpp,
            sum(cogs*quantity) as total_cogs,
            (sum(price*quantity)-sum(cogs*quantity)) as total_profit, 
            category_name,
            bill_menu.created_at,
            (
                select  sum(quantity)  as reguler 
                from bill_menu 
                join bill on bill.id = bill_menu.bill_id 
                join bill_payment ON (bill_menu.bill_id = bill_payment.bill_id) 
                where bill.is_refund = 0 and bill_payment.payment_option != 5 and bill_menu.menu_id = menu.id
                 
            ) as qty_reguler, 
             (
                select  sum(quantity)  as reguler 
                from bill_menu 
                join bill on bill.id = bill_menu.bill_id 
                join bill_payment ON (bill_menu.bill_id = bill_payment.bill_id) 
                where bill.is_refund = 0 and bill_payment.payment_option != 5 and bill_menu.menu_id = menu.id
                
                
             
            ) * price as total_reguler, 
            (
                select  sum(quantity)  as compliment 
                from bill_menu 
                join bill on bill.id = bill_menu.bill_id  
                join bill_payment ON (bill_menu.bill_id = bill_payment.bill_id)
             
                where bill.is_refund = 0 and bill_payment.payment_option = 5 and bill_menu.menu_id = menu.id
             
            ) as  qty_compliment,
            (
                select  sum(quantity) as compliment 
                from bill_menu 
                join bill on bill.id = bill_menu.bill_id 
                join bill_payment ON (bill_menu.bill_id = bill_payment.bill_id) 
                where bill.is_refund = 0 and bill_payment.payment_option = 5 and bill_menu.menu_id = menu.id
                
                
             
            ) * cogs as total_compliment
        ')
            ->join('menu', 'menu.id = bill_menu.menu_id')
            ->join('category', 'category.id = menu.category_id')
            ->from('bill_menu')
            ->group_by('bill_menu.menu_id')
            ->unset_column('created_at')
            ->add_column('created_at', '$1', 'convert_date(created_at)')
            ->add_column('total_price', '$1', 'convert_rupiah(total_price)')
            ->add_column('harga_menu', '$1', 'convert_rupiah(harga_menu)')
            ->add_column('harga_hpp', '$1', 'convert_rupiah(harga_hpp)')
            ->add_column('total_cogs', '$1', 'convert_rupiah(total_cogs)')
            ->add_column('total_profit', '$1', 'convert_rupiah(total_profit)');

        if ($category_id) {
            $this->datatables->where('outlet_id', $category_id);
        }

        if ($start_date) {
            $this->datatables->where('bill_menu.created_at >= ', $start_date);
        }

        if ($end_date) {
            $this->datatables->where('bill_menu.created_at <= ', $end_date);
        }

        if ($start_date && $end_date) {
            $this->datatables->add_column('periode', '$1', 'transaction_periode(' . $start_date . ',' . $end_date . ' )');
        } else {
            $this->datatables->add_column('periode', '$1', '-');

        }


        $this->datatables->add_column('actions', "<div class='btn-group'>
            <a href='" . urldecode(urlencode(base_url(SITE_ADMIN . '/reports/get_detail_sales_menu/$1/' . strtotime($start_date) . '/' . strtotime($end_date)))) . "'  
            class='btn btn-default'
            rel='tooltip' data-tooltip='tooltip' target='_blank' title='Detail'
            >
            <i class='fa fa-search'></i></a>", 'id');

        echo $this->datatables->generate();
    }

    function get_sales_menu_data()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $post_array = array();
        parse_str($this->input->post('param'), $post_array);

        $category_id = $post_array['category_id'];
        $menu_id = $post_array['menu_id'];
        $start_date = $post_array['start_date'];
        $end_date = $post_array['end_date'];


        $where = "where 1=1 ";
        if ($start_date && $end_date) {
            $where = 'and bill.created_at >= "' . $start_date . '" 
                        AND bill.created_at <= "' . $end_date . '" ';
        }
		if ($category_id) {
			$where .= ' AND outlet_id = ' . $category_id;
		}
		if ($menu_id) {
			$where .= ' AND menu.id = ' . $menu_id;
		}
        // if ($category_id && !$start_date) {
            // $where = 'where outlet_id = ' . $category_id;
        // }

        $this->datatables->select('menu.id as id, 
            menu_id, menu.menu_name, 
            sum(quantity) as total_quantity, 
            sum(price*quantity) as total_price,
            sum(cogs*quantity) as total_cogs,
            (sum(price*quantity)-sum(cogs*quantity)) as total_profit, 
            category_name,
            bill_menu.created_at,
            (SELECT sum(quantity)                 
                from bill_menu
				join bill on bill_menu.bill_id=bill.id and bill.is_refund = 0 
                join menu on menu.id = bill_menu.menu_id
                join category on category.id =  menu.category_id
                ' . $where . ' 
                ) as sum_total_quantity,


            (SELECT sum(price*quantity)                 
                from bill_menu
                join bill on bill_menu.bill_id=bill.id and bill.is_refund = 0 
                join menu on menu.id = bill_menu.menu_id
                join category on category.id =  menu.category_id
                ' . $where . ' 
                ) as sum_total_price,

            (SELECT sum(cogs*quantity)                 
                from bill_menu
				join bill on bill_menu.bill_id=bill.id and bill.is_refund = 0 
                join menu on menu.id = bill_menu.menu_id
                join category on category.id =  menu.category_id
                ' . $where . ' 
                ) as sum_total_cogs,

            (SELECT (sum(price*quantity)-sum(cogs*quantity))                
                from bill_menu
				join bill on bill_menu.bill_id=bill.id and bill.is_refund = 0 
                join menu on menu.id = bill_menu.menu_id
                join category on category.id =  menu.category_id
                ' . $where . ' 
                ) as sum_profit
        ')
            ->join('bill', 'bill.id = bill_menu.bill_id')
            ->join('menu', 'menu.id = bill_menu.menu_id')
            ->join('category', 'category.id = menu.category_id')
            ->from('bill_menu')
            ->group_by('bill_menu.menu_id')
            ->unset_column('created_at')
            ->add_column('created_at', '$1', 'convert_date(created_at)')
            ->add_column('total_price', '$1', 'convert_rupiah(total_price)')
            ->add_column('total_cogs', '$1', 'convert_rupiah(total_cogs)')
            ->add_column('total_profit', '$1', 'convert_rupiah(total_profit)');

        if ($category_id) {
            $this->datatables->where('outlet_id', $category_id);
        }
		if ($menu_id) {
            $this->datatables->where('menu.id', $menu_id);
        }
        if ($start_date) {
            $this->datatables->where('bill.created_at >= ', $start_date);
        }

        if ($end_date) {
            $this->datatables->where('bill.created_at <= ', $end_date);
        }

        if ($start_date && $end_date) {
            $this->datatables->add_column('periode', '$1', 'transaction_periode(' . $start_date . ',' . $end_date . ' )');
        } else {
            $this->datatables->add_column('periode', '$1', '-');

        }


        $this->datatables->add_column('actions', "<div class='btn-group'>
            <a href='" . urldecode(urlencode(base_url(SITE_ADMIN . '/reports/get_detail_sales_menu/$1/' . strtotime($start_date) . '/' . strtotime($end_date)))) . "'  
            class='btn btn-default'
            rel='tooltip' data-tooltip='tooltip' target='_blank' title='Detail'
            >
            <i class='fa fa-search'></i></a>", 'id');

        echo $this->datatables->generate();
    }


    public function get_detail_sales_menu($menu_id, $start_date = FALSE, $end_date = FALSE)
    {


        $this->data['title'] = "Detail Penjualan Menu";
        $this->data['subtitle'] = "Detail Penjualan Menu";

        $this->load->helper(array('datatables'));
        $this->load->model('order_model');

        $data['start_date'] = FALSE;
        $data['end_date'] = FALSE;
        if ($start_date) {
            $data['start_date'] = date('Y-m-d H:i:s', $start_date);
            $data['end_date'] = date('Y-m-d H:i:s', $end_date);
        }

        $data['menu_detail'] = $this->order_model->get_sales_menu_detail($menu_id, $data['start_date'], $data['end_date']);
        $data['menu_detail']->store = $this->order_model->get('store')->row()->store_name;

        $data['url_detail_menu'] = base_url(SITE_ADMIN . '/reports/get_detail_sales_datatable/' . $menu_id . '/' . $start_date . '/' . $end_date);

        $this->data['content'] .= $this->load->view('admin/report/report-sales-menu-detail', $data, true);
        $this->render('report');

    }

    function get_detail_sales_datatable($menu_id, $start_date = FALSE, $end_date = FALSE)
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $this->datatables->select('
            bill_menu.menu_id as id, 
            menu_id, bill_menu.menu_name, 
            sum(quantity) as quantity, 
            sum(price*quantity) as price, 
            sum(cogs*quantity) as cogs, 
            (sum(price*quantity)-sum(cogs*quantity)) as profit, 
            bill_menu.created_at, bill.receipt_number')
            ->from('bill_menu')
            ->join('bill', 'bill.id = bill_menu.bill_id')
            ->where('bill_menu.menu_id', $menu_id)
            ->where('bill.is_refund', 0)
            ->group_by('bill_menu.bill_id')
            ->unset_column('created_at')
            ->add_column('created_at', '$1', 'convert_date_with_time(created_at)')
            ->add_column('price', '$1', 'convert_rupiah(price)')
            ->add_column('cogs', '$1', 'convert_rupiah(cogs)')
            ->add_column('profit', '$1', 'convert_rupiah(profit)');


        if ($start_date) {
            $this->datatables->where('bill_menu.created_at >= ', date('Y-m-d H:i:s', $start_date));
        }

        if ($end_date) {
            $this->datatables->where('bill_menu.created_at <= ', date('Y-m-d H:i:s', $end_date));
        }


        // $this->datatables->add_column('actions', "<div class='btn-group'>
        //     <a href='" . urldecode(urlencode(base_url(SITE_ADMIN . '/reports/get_detail_sales_menu/$1/'.strtotime($start_date).'/'.strtotime($end_date)) )). "'  
        //     class='btn btn-default'
        //     rel='tooltip' data-tooltip='tooltip' target='_blank' title='Detail'
        //     >
        //     <i class='fa fa-search'></i></a>", 'id');


        echo $this->datatables->generate();

    }

    public function profit_lose()
    {
        $this->data['title'] = "Laporan Untung/Rugi";
        $this->data['subtitle'] = "Laporan Untung/Rugi";

        $this->load->model('order_model');

        // $this->data['all_store']  = $this->store_model->get_all_store();

        $this->data['data_url'] = base_url(SITE_ADMIN . '/reports/get_report_profit_lose');
        $this->data['content'] .= $this->load->view('admin/report/report-profit-loss', $this->data, true);
        $this->render('report');
    }


    public function get_report_profit_lose()
    {
        $this->load->helper(array('datatables'));
        $this->load->model('order_model');

        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $all_category = $this->order_model->get_sales_category_data($start_date, $end_date);
        $all_minus = $this->order_model->get_calculate_payment_bill(2, $start_date, $end_date);
        $all_plus = $this->order_model->get_calculate_payment_bill(1, $start_date, $end_date);
        $bon_bill = $this->order_model->get_bon_bill($start_date, $end_date);
        $pending_bill = $this->order_model->get_pending_bill($start_date, $end_date);
        $voucher_bill = $this->order_model->get_voucher_bill($start_date, $end_date);
        $total_compliment = $this->order_model->get_compliment($start_date, $end_date);
        $dp_in = $this->order_model->get_dp_in($start_date, $end_date);
        $dp_out = $this->order_model->get_dp_out($start_date, $end_date);
        $dp_kas_kecil = $this->order_model->get_petty_cash($start_date, $end_date);

        $data['all_category'] = $all_category;
        $data['all_minus'] = $all_minus;
        $data['all_plus'] = $all_plus;
        $data['total_bon_bill'] = $bon_bill->amount;
        $data['total_voucher_bill'] = $voucher_bill->amount;
        $data['total_pending_bill'] = $pending_bill->amount;
        $data['total_dp_in'] = $dp_in->amount;
        $data['total_dp_out'] = $dp_out->amount;
        $data['total_petty_cash'] = $dp_kas_kecil->amount;
        $data['total_compliment'] = $total_compliment->amount;


        $data['is_print'] = FALSE;

        $html = $this->load->view('admin/report/report_loss_profit_to_pdf_v', $data, true);

        echo $html;

    }
	function export_report_to_xls()
    {
		$this->load->library(array('excel'));
		$this->load->model("report_model");
		$style = array(
          "border" => array(
            'borders' => array(
              'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN
              )
            )
          ),
          "center" => array(
            'alignment' => array(
              'wrap'       => true,
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
          ),
          "bold"=>array(
            'font' => array(
              'bold' => true
            ),
          ),
          "header_table" => array(
            'alignment' => array(
              'wrap'       => true,
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
            'font' => array(
              'bold' => true
            ),
            'fill' => array(
              'type' => PHPExcel_Style_Fill::FILL_SOLID,
              'color' => array('rgb' => 'D3E6FC')
            ),
          ),
        );
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$type = $this->input->post('type');
			$outlet_id = $this->input->post('outlet_id');
			$inventory_id = $this->input->post('inventory_id');
			$month_year = $this->input->post('month_year');
		}else{
			$type = $this->input->get('type');
			$outlet_id = $this->input->get('outlet_id');
			$inventory_id = $this->input->get('inventory_id');
			$month_year = $this->input->get('month_year');
		}
		if($type=="summary_inventory"){
			$extract_month_year = explode("-", $month_year);
			if(empty($extract_month_year[0])){
				$month = date('m');
				$year = date('Y');
			}else{
				$month = $extract_month_year[0];
				$year = $extract_month_year[1];
			}
			$start_date=date("Y-m-d",strtotime($year."-".$month."-01"));
			$end_date=date("Y-m-t",strtotime($start_date));
			$store = $this->store_model->get_by('store', $this->data['setting']['store_id']);
			$lists = $this->report_model->summary_inventory(array("start_date" => $start_date, "end_date" => $end_date, "outlet_id" => $outlet_id, "inventory_id" => $inventory_id,"store_id"=>$this->data['setting']['store_id']));
			$history_status = $this->store_model->get("enum_stock_history_status")->result();
			
			$this->excel->setActiveSheetIndex(0);
			$this->excel->getActiveSheet()->setTitle('Laporan Summary Inventory');
			$this->excel->getActiveSheet()->setCellValue('A1', 'Laporan Summary Inventory');
			$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
			$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->mergeCells('A1:K1');
			$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$this->excel->getActiveSheet()->setCellValue('A2', $store->store_name);  
			$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(15); 
			$this->excel->getActiveSheet()->mergeCells('A2:K2');
			$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);   

			$this->excel->getActiveSheet()->setCellValue('A3', date("F Y",strtotime($start_date)));
			$this->excel->getActiveSheet()->mergeCells('A3:K3');
			$this->excel->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);   
			
			$this->excel->getActiveSheet()->setCellValue("A5","Outlet");
			$this->excel->getActiveSheet()->setCellValue("B5","Inventory");
			$this->excel->getActiveSheet()->setCellValue("C5","Stok Awal");
			$this->excel->getActiveSheet()->setCellValue("D5","Penjualan");
			$this->excel->getActiveSheet()->setCellValue("E5","Transfer");
			$this->excel->getActiveSheet()->setCellValue("F5","Pembelian");
			$this->excel->getActiveSheet()->setCellValue("G5","Opname");
			$this->excel->getActiveSheet()->setCellValue("H5","Penerimaan");
			$this->excel->getActiveSheet()->setCellValue("I5","Proses Inventory");
			$this->excel->getActiveSheet()->setCellValue("J5","Spoiled");
			$this->excel->getActiveSheet()->setCellValue("K5","Stok Akhir");
			$this->excel->getActiveSheet()->getStyle('A5:K5')->applyFromArray($style['header_table']);
			$row=6;
			foreach($lists as $d){
			  $this->excel->getActiveSheet()->setCellValue("A".$row,$d->outlet_name);
			  $this->excel->getActiveSheet()->setCellValue("B".$row,$d->name.' ( '.$d->code.' ) ');
			  $this->excel->getActiveSheet()->setCellValue("C".$row,$d->beginning_stock)->getStyle('C'.$row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			  $counter_character="C";
			  $counter=1;
			  foreach($history_status as $h){
				$counter_character++;
				$this->excel->getActiveSheet()->setCellValue($counter_character.$row,$d->{"total_".$counter})->getStyle($counter_character.$row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
				$counter++;
			  }
			  $counter_character++;
			  $this->excel->getActiveSheet()->setCellValue($counter_character.$row,$d->last_stock)->getStyle($counter_character.$row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			  $row++;
			}
			$this->excel->getActiveSheet()->getStyle('A'.$row.':K'.($row))->applyFromArray($style['bold']);
			
			$this->excel->getActiveSheet()->getStyle('A5:K'.($row))->applyFromArray($style['border']);
			foreach(range('A',$this->excel->getActiveSheet()->getHighestDataColumn()) as $columnID) {
			  $this->excel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
			}
		}
		$date     = new DateTime();
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, "Excel2007");
		$filename = 'report_' . $type . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis') . '.xlsx';
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		$objWriter->save('php://output');
    }
    public function export_report_to_pdf()
    {
        $this->load->helper(array('datatables'));
        $this->load->model('order_model');
        $this->load->helper(array('dompdf', 'file'));
        // page info here, db calls, etc.

        // $category_id = $this->input->post('category_id');
        // $user_id = $this->input->post('user_id');
        // $start_date = $this->input->post('start_date');
        // $end_date = $this->input->post('end_date');
        // $month_year = $this->input->post('month_year');
        // $open_by = $this->input->post('open_by');
        // $close_by = $this->input->post('close_by');
        // $type= $this->input->post('type');

        // $date = $this->input->post('date');
        // $report_end_date = $this->input->post('report_end_date');
        // $store_id = $this->input->post('store_id');

        $data['is_print'] = TRUE;
        $pdf_orientation = "landscape";

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $category_menu_id = $this->input->post('category_id');
            $category_id = $this->input->post('category_menu_id');
            $user_id = $this->input->post('user_id');
            $date = $this->input->post('date');
            $inventory_id = $this->input->post('inventory_id');
            $start_date = $this->input->post('start_date');
            $end_date = $this->input->post('end_date');
            $open_by = $this->input->post('open_by');
            $close_by = $this->input->post('close_by');
            $type = $this->input->post('type');
            $store_id = $this->input->post('store_id');
            $uom_id = $this->input->post('uom_id');
            $month_year = $this->input->post('month_year');
            $report_end_date = $this->input->post('report_end_date');
            $target_id = $this->input->post('target_id');
            $month = $this->input->post('month');
            $year = $this->input->post('year');
            $order_type = $this->input->post('order_type');
            $outlet_id = $this->input->post('outlet_id');
            $supplier_id = $this->input->post('supplier_id');
            $payment_method = $this->input->post('payment_method');
            $member_id = $this->input->post('member_id');
            $payment_option = $this->input->post('payment_option');
            $menu_id = $this->input->post('menu_id');
            $from_date = $this->input->post('from_date');
            $to_date = $this->input->post('to_date');
            $promo_cc_id = $this->input->post('promo_cc_id');
            $ge_id= $this->input->post('ge_id');

            $store_id_start = $this->input->post('store_id_start');
            $store_id_end = $this->input->post('store_id_end');
            $outlet_id_start = $this->input->post('outlet_id_start');
            $outlet_id_end = $this->input->post('outlet_id_end');
            $category_menu_id = $this->input->post('category_menu_id');

        } else {
            $category_id = $this->input->get('category_id');
            $category_menu_id = $this->input->get('category_menu_id');
            $user_id = $this->input->get('user_id');
            $date = $this->input->get('date');
            $inventory_id = $this->input->get('inventory_id');
            $start_date = $this->input->get('start_date');
            $end_date = $this->input->get('end_date');
            $open_by = $this->input->get('open_by');
            $close_by = $this->input->get('close_by');
            $type = $this->input->get('type');
            $store_id = $this->input->get('store_id');
            $uom_id = $this->input->get('uom_id');
            $month_year = $this->input->get('month_year');
            $report_end_date = $this->input->get('report_end_date');
            $target_id = $this->input->get('target_id');
            $month = $this->input->get('month');
            $year = $this->input->get('year');
            $order_type = $this->input->get('order_type');
            $outlet_id = $this->input->get('outlet_id');
            $supplier_id = $this->input->get('supplier_id');
            $member_id = $this->input->get('member_id');
            $payment_option = $this->input->get('payment_option');
            $menu_id = $this->input->get('menu_id');
            $from_date = $this->input->get('from_date');
            $to_date = $this->input->get('to_date');
            $promo_cc_id = $this->input->get('promo_cc_id');
            $ge_id= $this->input->get('ge_id');

            $store_id_start = $this->input->get('store_id_start');
            $store_id_end = $this->input->get('store_id_end');
            $outlet_id_start = $this->input->get('outlet_id_start');
            $outlet_id_end = $this->input->get('outlet_id_end');
            $category_menu_id = $this->input->get('category_menu_id');
        }

        $data['periode'] = transaction_periode($start_date,$end_date);
        $data['store'] =$this->order_model->get_by('store', $this->data['setting']['store_id']);
      
        if ($type == "sales_menu") {

            $data['all_sales_menu'] = $this->order_model->get_sales_menu_data($category_id, $start_date, $end_date,$menu_id);

            $data['total_cogs'] = 0;
            $data['total_price'] = 0;
            $data['total_profit'] = 0;
            $data['total_quantity'] = 0;
            foreach ($data['all_sales_menu'] as $key => $row) {
                $data['total_cogs'] += $row->total_cogs;
                $data['total_price'] += $row->total_price;
                $data['total_profit'] += $row->profit;
                $data['total_quantity'] += $row->total_quantity;
            }

            $data['periode'] = transaction_periode($start_date, $end_date);
            $report = 'sales_menu';
            // $html = $this->load->view('admin/report/report_sales_menu_to_pdf_v', $data, true);

            $date = new DateTime();
            $perpage = 30;
            $offset = 0;
            $total_page = ceil(sizeof($data['all_sales_menu']) / $perpage);
            $filenames = array();
            $data['from'] = -1;
            $html = $this->load->view('admin/report/report_sales_menu_to_pdf_v', $data, true);
            $data_pdf = pdf_create($html, '', false, $pdf_orientation);
            $filename = 'assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis') . '_0' . '.pdf';
            array_push($filenames, $filename);
            write_file($filename, $data_pdf);
            echo json_encode($filename);
            for ($x = 0; $x < $total_page; $x++) {
                $data['from'] = $x * $perpage;
                $data['to'] = ($x * $perpage) + $perpage;
                if ($data['to'] > sizeof($data['all_sales_menu'])) $data['to'] = sizeof($data['all_sales_menu']);
                $html = $this->load->view('admin/report/report_sales_menu_to_pdf_v', $data, true);
				$data_pdf = pdf_create($html, '', false, $pdf_orientation);
                $filename = 'assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis') . '_' . ($x + 1) . '.pdf';
                array_push($filenames, $filename);
                write_file($filename, $data_pdf);
                echo json_encode($filename);
            }
            $this->load->library("PDFMerger/PDFMerger");
            $pdf = new PDFMerger;
            foreach ($filenames as $file) {
                $pdf->addPDF($file, 'all', 'L');
            }
            $merge = 'assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis') . '.pdf';
            $pdf->merge('file', $merge);
            foreach ($filenames as $file) {
                @unlink($file);
            }
            // redirect($merge);
            $file_url = $merge;
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary"); 
            header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\""); 
            readfile($file_url);

        }else if($type == "sales_outlet"){
             

            $pdf_orientation= "portrait";
            $data['data'] =$this->order_model->get_sales_category_data($start_date, $end_date);

            $data['total_cogs'] = 0;
            $data['total_price'] = 0;
            $data['total_profit'] =0;
            $data['total_quantity'] =0;

            foreach ($data['data']  as $key => $row) {
             $data['total_cogs'] += $row->total_cogs;
             $data['total_price'] += $row->total_price;
             $data['total_profit'] += $row->profit;
             $data['total_quantity'] += $row->total_quantity;
            }

            $data['periode'] = transaction_periode($start_date,$end_date);
            $report = 'sales_outlet';
            // $html = $this->load->view('admin/report/report_sales_menu_to_pdf_v', $data, true);
           
            $date     = new DateTime();
            $perpage=38;
            $offset=0;
            $total_page=ceil(sizeof($data['data'])/$perpage);
            $filenames=array();
            
            $data['from']=-1;
            $html = $this->load->view('admin/report/report_sales_outlet_to_pdf_v', $data, true);
            $data_pdf = pdf_create($html, '', false,  $pdf_orientation);
            $filename = 'assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis').'_0' . '.pdf'; 
            array_push($filenames,$filename);
            write_file($filename, $data_pdf);
            echo json_encode($filename);
            for($x=0;$x<$total_page;$x++){
              $data['from']=$x*$perpage;
              $data['to']=($x*$perpage)+$perpage;
              if($data['to']>sizeof($data['data']))$data['to']=sizeof($data['data']);
              $html = $this->load->view('admin/report/report_sales_outlet_to_pdf_v', $data, true);
              $data_pdf = pdf_create($html, '', false, $pdf_orientation);
              $filename = 'assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis').'_'.($x+1) . '.pdf'; 
              array_push($filenames,$filename);
              write_file($filename, $data_pdf);
              echo json_encode($filename);
            }
            $this->load->library("PDFMerger/PDFMerger");
            $pdf = new PDFMerger;
            foreach($filenames as $file){
              $pdf->addPDF($file, 'all');         
            }
            

            $merge='assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis'). '.pdf';
            $pdf->merge('file', $merge);
            
            foreach($filenames as $file){
              unlink($file);
            }
             // redirect($merge);

             $file_url = $merge;
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary"); 
            header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\""); 
            readfile($file_url);









            } else if($type == "petty_cash"){
            $pdf_orientation= "portrait";
          
             $data['all_petty_cash'] =$this->order_model->get_petty_cash_data($ge_id,$start_date,$end_date);
             
            $report = 'petty_cash';
            // $html = $this->load->view('admin/report/report_open_close_to_pdf_v', $data, true);

            $date     = new DateTime();
            $perpage=40;
            $offset=0;
            $total_page=ceil(sizeof($data['all_petty_cash'])/$perpage);

            $filenames=array();
            
            $data['from']=-1;
            $html = $this->load->view('admin/report/report_petty_cash_to_pdf_v', $data, true);
            $data_pdf = pdf_create($html, '', false, $pdf_orientation);
            $filename = 'assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis').'_0' . '.pdf'; 
            array_push($filenames,$filename);
            write_file($filename, $data_pdf);
            echo json_encode($filename);
            for($x=0;$x<$total_page;$x++){
              $data['from']=$x*$perpage;
              $data['to']=($x*$perpage)+$perpage;
              if($data['to']>sizeof($data['all_petty_cash']))$data['to']=sizeof($data['all_petty_cash']);
              $html = $this->load->view('admin/report/report_petty_cash_to_pdf_v', $data, true);
              $data_pdf = pdf_create($html, '', false, $pdf_orientation);
              $filename = 'assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis').'_'.($x+1) . '.pdf'; 
              array_push($filenames,$filename);
              write_file($filename, $data_pdf);
              echo json_encode($filename);
            }
            $this->load->library("PDFMerger/PDFMerger");
            $pdf = new PDFMerger;
            foreach($filenames as $file){
              $pdf->addPDF($file, 'all');         
            }
             $merge='assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis'). '.pdf';
             $pdf->merge('file', $merge);
            foreach($filenames as $file){
              @unlink($file);
             }
            // redirect($merge);
            $file_url = $merge;
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary"); 
            header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\""); 
            readfile($file_url);

        }else if($type == "price_analyst"){

            $pdf_orientation= "portrait";

            $data['all_price_analyst'] = $this->order_model->get_price_analyst_data($store_id,$outlet_id,$category_menu_id);
             
            $report = 'price_analyst';

            $date     = new DateTime();
            $perpage=30;
            $offset=0;
            $total_page=ceil(sizeof($data['all_price_analyst'])/$perpage);

            $filenames=array();
            
            $data['from']=-1;
            $html = $this->load->view('admin/report/report-analisis-harga-to-pdf', $data, true);
            $data_pdf = pdf_create($html, '', false, $pdf_orientation);
            $filename = 'assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis').'_0' . '.pdf'; 
            array_push($filenames,$filename);
            write_file($filename, $data_pdf);
            echo json_encode($filename);
            for($x=0;$x<$total_page;$x++){
              $data['from']=$x*$perpage;
              $data['to']=($x*$perpage)+$perpage;
              if($data['to']>sizeof($data['all_price_analyst']))$data['to']=sizeof($data['all_price_analyst']);
              $html = $this->load->view('admin/report/report-analisis-harga-to-pdf', $data, true);
              $data_pdf = pdf_create($html, '', false, $pdf_orientation);
              $filename = 'assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis').'_'.($x+1) . '.pdf'; 
              array_push($filenames,$filename);
              write_file($filename, $data_pdf);
              echo json_encode($filename);
            }
            $this->load->library("PDFMerger/PDFMerger");
            $pdf = new PDFMerger;
            foreach($filenames as $file){
              $pdf->addPDF($file, 'all');         
            }
             $merge='assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis'). '.pdf';
             $pdf->merge('file', $merge);
            foreach($filenames as $file){
              @unlink($file);
             }
            // redirect($merge);
            $file_url = $merge;
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary"); 
            header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\""); 
            readfile($file_url);
        }else if ($type == "moving_item") {

            $data['all_sales_menu'] = $this->order_model->get_moving_item_data($category_id, $start_date, $end_date);
            $data['periode'] = transaction_periode($start_date, $end_date);
            $report = 'moving_item';
            // $html = $this->load->view('admin/report/report_moving_item_to_pdf_v', $data, true);

            $date = new DateTime();
            $perpage = 20;
            $offset = 0;
            $total_page = ceil(sizeof($data['all_sales_menu']) / $perpage);
            $filenames = array();

            $data['from'] = -1;
            $html = $this->load->view('admin/report/report_moving_item_to_pdf_v', $data, true);
            $data_pdf = pdf_create($html, '', false, $pdf_orientation);
            $filename = 'assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis') . '_0' . '.pdf';
            array_push($filenames, $filename);
            write_file($filename, $data_pdf);
            echo json_encode($filename);
            for ($x = 0; $x < $total_page; $x++) {
                $data['from'] = $x * $perpage;
                $data['to'] = ($x * $perpage) + $perpage;
                if ($data['to'] > sizeof($data['all_sales_menu'])) $data['to'] = sizeof($data['all_sales_menu']);
                $html = $this->load->view('admin/report/report_moving_item_to_pdf_v', $data, true);
                $data_pdf = pdf_create($html, '', false, $pdf_orientation);
                $filename = 'assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis') . '_' . ($x + 1) . '.pdf';
                array_push($filenames, $filename);
                write_file($filename, $data_pdf);
                echo json_encode($filename);
            }
            $this->load->library("PDFMerger/PDFMerger");
            $pdf = new PDFMerger;
            foreach ($filenames as $file) {
                $pdf->addPDF($file, 'all');
            }
            $merge = 'assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis') . '.pdf';
            $pdf->merge('file', $merge);
            foreach ($filenames as $file) {
                @unlink($file);
            }
           //edirect($merge);
           $file_url = $merge;
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary"); 
            header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\""); 
            readfile($file_url);

        } else if ($type == "profit_loss") {
            $all_category = $this->order_model->calculate_sales_menu_category($start_date, $end_date);
            $all_minus = $this->order_model->get_calculate_payment_bill(2, $start_date, $end_date);
            $all_plus = $this->order_model->get_calculate_payment_bill(1, $start_date, $end_date);
            $bon_bill = $this->order_model->get_bon_bill($start_date, $end_date);
            $pending_bill = $this->order_model->get_pending_bill($start_date, $end_date);
            $voucher_bill = $this->order_model->get_voucher_bill($start_date, $end_date);
            $dp_in = $this->order_model->get_dp_in($start_date, $end_date);
            $dp_out = $this->order_model->get_dp_out($start_date, $end_date);
            $dp_kas_kecil = $this->order_model->get_petty_cash($start_date, $end_date);
            $total_compliment = $this->order_model->get_compliment($start_date, $end_date);
            $data['all_category'] = $all_category;
            $data['all_minus'] = $all_minus;
            $data['all_plus'] = $all_plus;
            $data['total_bon_bill'] = $bon_bill->amount;
            $data['total_voucher_bill'] = $voucher_bill->amount;
            $data['total_pending_bill'] = $pending_bill->amount;
            $data['total_dp_in'] = $dp_in->amount;
            $data['total_dp_out'] = $dp_out->amount;
            $data['total_petty_cash'] = $dp_kas_kecil->amount;
            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;
            $data['total_compliment'] = $total_compliment->amount;
            $report = 'profit_loss';
            $html = $this->load->view('admin/report/report_loss_profit_to_pdf_v', $data, true);
            $pdf_orientation = "portrait";
        } else if ($type == "transaction") {
            $pdf_orientation = "portrait";
            $data['all_transaction'] = $this->order_model->get_transaction_data($user_id, $start_date, $end_date, $order_type);
            $data['periode'] = transaction_periode($start_date, $end_date);
            $report = 'transaction';

            $data['summary'] = $this->order_model->get_summary_transaction(array(
                "user_id" => $user_id,
                "start_date" => $start_date,
                "end_date" => $end_date,
                "order_type" => $order_type,
                "payment_option" => $payment_option,
            ));
            $data['params'] = array(
                "user_id" => $user_id,
                "start_date" => $start_date,
                "end_date" => $end_date,
                "order_type" => $order_type,
                "payment_option" => $payment_option,
            );
            $data['summary'] = (object)$data['summary'];
            // $html = $this->load->view('admin/report/report_transaction_to_pdf_v', $data, true);
            $date = new DateTime();
            $perpage = 38;
            $offset = 0;
            $total_page = ceil(sizeof($data['all_transaction']) / $perpage);
            $filenames = array();

            $data['from'] = -1;
            $html = $this->load->view('admin/report/report_transaction_to_pdf_v', $data, true);
            $data_pdf = pdf_create($html, '', false, $pdf_orientation);
            $filename = 'assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis') . '_0' . '.pdf';
            array_push($filenames, $filename);
            write_file($filename, $data_pdf);
            echo json_encode($filename);
            for ($x = 0; $x < $total_page; $x++) {
                $data['from'] = $x * $perpage;
                $data['to'] = ($x * $perpage) + $perpage;
                if ($data['to'] > sizeof($data['all_transaction'])) $data['to'] = sizeof($data['all_transaction']);
                $html = $this->load->view('admin/report/report_transaction_to_pdf_v', $data, true);
                $data_pdf = pdf_create($html, '', false, $pdf_orientation);
                $filename = 'assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis') . '_' . ($x + 1) . '.pdf';
                array_push($filenames, $filename);
                write_file($filename, $data_pdf);
                echo json_encode($filename);
            }
            $this->load->library("PDFMerger/PDFMerger");
            $pdf = new PDFMerger;
            foreach ($filenames as $file) {
                $pdf->addPDF($file, 'all');
            }
            $merge = 'assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis') . '.pdf';
            $pdf->merge('file', $merge);
            foreach ($filenames as $file) {
                @unlink($file);
            }
           //redirect($merge);

            $file_url = $merge;
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary"); 
            header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\""); 
            readfile($file_url);
        } else if ($type == "open_close") {

            $data['all_open_close'] = $this->order_model->get_open_close_data($open_by, $close_by, $start_date, $end_date);
            $data['periode'] = transaction_periode($start_date, $end_date);
            $report = 'open_close';
            // $html = $this->load->view('admin/report/report_open_close_to_pdf_v', $data, true);

            $date = new DateTime();
            $perpage = 30;
            $offset = 0;
            $total_page = ceil(sizeof($data['all_open_close']) / $perpage);
            $filenames = array();

            $data['from'] = -1;
            $html = $this->load->view('admin/report/report_open_close_to_pdf_v', $data, true);
            $data_pdf = pdf_create($html, '', false, $pdf_orientation);
            $filename = 'assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis') . '_0' . '.pdf';
            array_push($filenames, $filename);
            write_file($filename, $data_pdf);
            echo json_encode($filename);
            for ($x = 0; $x < $total_page; $x++) {
                $data['from'] = $x * $perpage;
                $data['to'] = ($x * $perpage) + $perpage;
                if ($data['to'] > sizeof($data['all_open_close'])) $data['to'] = sizeof($data['all_open_close']);
                $html = $this->load->view('admin/report/report_open_close_to_pdf_v', $data, true);
                $data_pdf = pdf_create($html, '', false, $pdf_orientation);
                $filename = 'assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis') . '_' . ($x + 1) . '.pdf';
                array_push($filenames, $filename);
                write_file($filename, $data_pdf);
                echo json_encode($filename);
            }
            $this->load->library("PDFMerger/PDFMerger");
            $pdf = new PDFMerger;
            foreach ($filenames as $file) {
                $pdf->addPDF($file, 'all');
            }
            $merge = 'assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis') . '.pdf';
            $pdf->merge('file', $merge);
            foreach ($filenames as $file) {
                @unlink($file);
            }
            //redirect($merge);
            $file_url = $merge;
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary"); 
            header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\""); 
            readfile($file_url);


        } else if ($type == "sales_category") {
			$store_id = $this->data['setting']['store_id'];
			$data['data'] = $this->order_model->get_sales_by_category_menu_data($start_date, $end_date,$store_id,$outlet_id,$category_menu_id);
			$data['total_cogs'] = 0;
            $data['total_price'] = 0;
            $data['total_profit'] =0;
            $data['total_quantity'] =0;

            foreach ($data['data']  as $key => $row) {
             // $data['total_cogs'] += $row->total_cogs;
             $data['total_price'] += $row->total_price;
             // $data['total_profit'] += $row->profit;
             $data['total_quantity'] += $row->total_quantity;
            }
            $data['periode'] = transaction_periode($start_date, $end_date);
			
            $report = 'sales_category';
            $html = $this->load->view('admin/report/report_sales_category_to_pdf_v', $data, true);

        } else if ($type == "void") {

            $data['data'] = $this->order_model->get_void_data($start_date, $end_date);
            $data['total_amount'] = 0;
            foreach ($data['data'] as $key => $row) {
                $data['total_amount'] += $row->amount;
            }
            $data['periode'] = transaction_periode($start_date, $end_date);
            $report = 'void';
            // $html = $this->load->view('admin/report/report_void_to_pdf_v', $data, true);

            $date = new DateTime();
            $perpage = 20;
            $offset = 0;
            $total_page = ceil(sizeof($data['data']) / $perpage);
            $filenames = array();

            $data['from'] = -1;
            $html = $this->load->view('admin/report/report_void_to_pdf_v', $data, true);
            $data_pdf = pdf_create($html, '', false, $pdf_orientation);
            $filename = 'assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis') . '_0' . '.pdf';
            array_push($filenames, $filename);
            write_file($filename, $data_pdf);
            echo json_encode($filename);
            for ($x = 0; $x < $total_page; $x++) {
                $data['from'] = $x * $perpage;
                $data['to'] = ($x * $perpage) + $perpage;
                if ($data['to'] > sizeof($data['data'])) $data['to'] = sizeof($data['data']);
                $html = $this->load->view('admin/report/report_void_to_pdf_v', $data, true);
                $data_pdf = pdf_create($html, '', false, $pdf_orientation);
                $filename = 'assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis') . '_' . ($x + 1) . '.pdf';
                array_push($filenames, $filename);
                write_file($filename, $data_pdf);
                echo json_encode($filename);
            }
            $this->load->library("PDFMerger/PDFMerger");
            $pdf = new PDFMerger;
            foreach ($filenames as $file) {
                $pdf->addPDF($file, 'all');
            }
            $merge = 'assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis') . '.pdf';
            $pdf->merge('file', $merge);
            foreach ($filenames as $file) {
                @unlink($file);
            }
            //redirect($merge);
            $file_url = $merge;
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary"); 
            header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\""); 
            readfile($file_url);
        
        } else if ($type == "inventory_stock") {
            $pdf_orientation = "portrait";
            $this->load->model("inventory_model");
            $data['date'] = $date;
            $data['report_end_date'] = $report_end_date;
            $data['data_store'] = $this->store_model->get_store($this->data['setting']['store_id']);
            $data['results'] = $this->inventory_model->get_inventory_stock_data(array("store_id" => $this->data['setting']['store_id'], "date" => $date, "report_end_date" => $report_end_date));
            $report = 'inventory_stock';
            $html = $this->load->view('admin/report/report-inventory-stock-pdf', $data, true);
		} else if ($type == "transfer_menu") {
            $pdf_orientation = "portrait";
            $this->load->model("inventory_model");

            $data['date'] = $date;
            $data['report_end_date'] = $report_end_date;

            $data['data_store'] = $this->store_model->get_store($this->data['setting']['store_id']);
            $data['results'] = $this->inventory_model->get_transfer_menu(array("store_id" => $this->data['setting']['store_id'], "date" => $date, "report_end_date" => $report_end_date));

            $report = 'transfer_menu';
            $html = $this->load->view('admin/report/report-transfer-menu-pdf', $data, true);

        } else if ($type == "transfer_inventory") {

            $pdf_orientation = "portrait";
            $this->load->model("inventory_model");
            $this->load->model("categories_model");
            $this->load->model("store_model");

            $start_date = $this->input->post('start_date');
            $end_date = $this->input->post('end_date');
            $store_id_start = $this->input->post('store_id_start');
            $store_id_end = $this->input->post('store_id_end');
            $outlet_id_start = $this->input->post('outlet_id_start');
            $outlet_id_end = $this->input->post('outlet_id_end');
            $inventory_id = $this->input->post('inventory_id');

            $data['is_print'] = True;
            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;
            $data['store_id'] = $store_id_start;

            $data['store_id_start']  = $this->store_model->get_store_dropdown($store_id_start);
            $data['store_id_end']  = $this->store_model->get_store_dropdown($store_id_start);
            $data['inventory_lists'] = $this->store_model->get("inventory")->result();

            $data['all_outlet_start'] = $this->categories_model->get_outlet_dropdown_from_warehouse();
            $data['all_outlet_end'] = $this->categories_model->get_outlet_dropdown_from_warehouse();

            $data['results'] = $this->inventory_model->get_transfer_inventory(array("store_id_start" => $store_id_start,"outlet_id_start" => $outlet_id_start, "store_id_end" => $store_id_end, "outlet_id_end" => $outlet_id_end,"inventory_id" => $inventory_id, "start_date" => $start_date, "end_date" => $end_date));

            $report = 'transfer_inventory';
            $html = $this->load->view('admin/report/report-transfer-inventory-pdf', $data, true);



        } else if ($type == "inventory_stock_detail") {
            $pdf_orientation = "portrait";
            $this->load->model("inventory_model");
            $data['from_date'] = $from_date;
            $data['to_date'] = $to_date;
            $data['report_end_date'] = $report_end_date;
            $data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
            $data['data_inventory'] = $this->store_model->get_by('inventory', $inventory_id);
            $data['data_uom'] = $this->store_model->get_one('uoms', $uom_id);
            $data['results'] = $this->inventory_model->get_inventory_stock_data_detail(array("uom_id" => $uom_id, "store_id" => $this->data['setting']['store_id'], "inventory_id" => $inventory_id, "from_date" => $from_date,"to_date"=>$to_date));
            $report = 'inventory_stock_detail';
            $html = $this->load->view('admin/report/report-inventory-stock-detail-pdf', $data, true);

        } else if ($type == "total_sales_waiter") {
            $pdf_orientation = "portrait";
            $this->load->model("report_model");
            $data['user_id'] = $user_id;
            $data['date'] = $date;
            $data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
            $data['results'] = $this->report_model->total_sales_waiter(array("user_id" => $user_id, "date" => $date));
            $report = 'total_sales_waiter';
            $html = $this->load->view('admin/report/report-total-sales-waiter-pdf', $data, true);
        } else if ($type == "total_sales_waiter_detail") {
            $pdf_orientation = "portrait";
            $this->load->model("report_model");
            $data['user_id'] = $user_id;
            $data['date'] = $date;
            $data['user'] = $this->store_model->get_one('users', $user_id);
            $data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
            $data['results'] = $this->report_model->total_sales_waiter_detail(array("user_id" => $user_id, "date" => $date));
            $data['results2'] = $this->report_model->total_quantity_order_table_waiter_detail(array("user_id" => $user_id, "date" => $date));
            $report = 'total_sales_waiter_detail';
            $html = $this->load->view('admin/report/report-total-sales-waiter-detail-pdf', $data, true);
        } else if ($type == "total_quantity_order_table_waiter") {
            $pdf_orientation = "portrait";
            $this->load->model("report_model");
            $data['user_id'] = $user_id;
            $data['date'] = $date;
            $data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
            $data['results'] = $this->report_model->total_quantity_order_table_waiter(array("user_id" => $user_id, "date" => $date));
            $report = 'total_quantity_order_table_waiter';
            $html = $this->load->view('admin/report/report-total-quantity-order-table-waiter-pdf', $data, true);
        } else if ($type == "achievement_waiter") {
            $pdf_orientation = "portrait";
            $this->load->model("report_model");
            $temp = explode("-", $month_year);
            $month = $temp[0];
            $year = $temp[1];
            $data['is_print'] = TRUE;
            $data['month_year'] = $month_year;
            $data['user_id'] = $user_id;
            $data['month'] = $month;
            $data['year'] = $year;
            $data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
            $data['results'] = $this->report_model->achievement_waiter(array("user_id" => $user_id, "month" => $month, "year" => $year));
            $report = 'achievement_waiter';
            $html = $this->load->view('admin/report/report-achievement-waiter-pdf', $data, true);
        } else if ($type == "achievement_waiter_detail") {
            $pdf_orientation = "portrait";
            $this->load->model("report_model");
            $data['is_print'] = TRUE;
            $data['target_id'] = $target_id;
            $data['user_id'] = $user_id;
            $data['month'] = $month;
            $data['year'] = $year;
            $data['target'] = $this->store_model->get_one("target", $target_id);
            $data['user'] = $this->store_model->get_one("users", $user_id);
            $data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
            if ($data['target']->target_type == 1) {
                $data['results'] = $this->report_model->achievement_waiter_detail_by_total(array("target_id" => $target_id, "month" => $month, "year" => $year));
            } else {
                $data['results'] = $this->report_model->achievement_waiter_detail_by_item(array("target_id" => $target_id, "month" => $month, "year" => $year));
            }
            $report = 'achievement_waiter_detail';
            $html = $this->load->view('admin/report/report-achievement-waiter-detail-pdf', $data, true);
        } else if ($type == "kitchen_duration") {
            $pdf_orientation = "portrait";
            $this->load->model("report_model");
            $temp = explode("-", $month_year);
            $month = $temp[0];
            $year = $temp[1];
            $data['is_print'] = TRUE;
            $data['month_year'] = $month_year;
            $data['user_id'] = $user_id;
            $data['month'] = $month;
            $data['year'] = $year;
            $data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
            $data['reward_kitchen'] = $this->report_model->get_all_where("reward_kitchen", array());
            if (sizeof($data['reward_kitchen']) > 0) $data['reward_kitchen'] = $data['reward_kitchen'][0];
            $data['results'] = $this->report_model->kitchen_duration(array("month" => $month, "year" => $year));
            $report = 'kitchen_duration';
            $html = $this->load->view('admin/report/report-kitchen-duration-pdf', $data, true);
        } elseif ($type == "summary_year") {
            $pdf_orientation = "portrait";
            $this->load->model("report_model");
            $this->data['year'] = $year;
            $this->data['is_print'] = TRUE;
            $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
            $this->data['results'] = $this->report_model->summary_year(array("year" => $year));
            $report = 'summary_transaction_year';
            $html = $this->load->view('admin/report/report-summary-year-pdf', $this->data, true);
        } elseif ($type == "taxes") {
            $pdf_orientation = "portrait";
            $this->load->model("report_model");
            $start_date = $this->input->post("start_period");
            $end_date = $this->input->post("end_period");
            $tax_name = $this->input->post("tax_name");
            $this->data['start_date'] = $start_date;
            $this->data['end_date'] = $end_date;
            $this->data['is_print'] = TRUE;
            $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
            $this->data['results'] = $this->report_model->taxes(array("start_date" => $start_date, "end_date" => $end_date, "tax_name" => $tax_name));
            $report = 'taxes';
            $html = $this->load->view('admin/report/report-taxes-year-pdf', $this->data, true);
        } elseif ($type == "summary_inventory") {
            $pdf_orientation = "portrait";
            $this->load->model("report_model");
            $this->data['is_print'] = TRUE;
			$temp = explode("-", $month_year);
			$month = $temp[0];
			$year = $temp[1];
			$start_date=date("Y-m-d",strtotime($year."-".$month."-01"));
			$end_date=date("Y-m-t",strtotime($start_date));
            $this->data['start_date'] = $start_date;
            $this->data['end_date'] = $end_date;
            $this->data['month'] = $month;
            $this->data['year'] = $year;
            $this->data['outlet_id'] = $outlet_id;
            $this->data['inventory_id'] = $inventory_id;
            $this->data['history_status'] = $this->store_model->get("enum_stock_history_status")->result();
            $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
            $this->data['results'] = $this->report_model->summary_inventory(array("start_date" => $start_date, "end_date" => $end_date, "outlet_id" => $outlet_id, "inventory_id" => $inventory_id,"store_id"=>$this->data['setting']['store_id']));
            $report = 'summary_inventory';
            $html = $this->load->view('admin/report/report-summary-inventory-pdf', $this->data, true);
        }elseif ($type == "detail_inventory") {
            $pdf_orientation = "portrait";
            $this->load->model("report_model");
            $this->data['is_print'] = TRUE;
            $this->data['start_date'] = $start_date;
            $this->data['end_date'] = $end_date;
            $this->data['outlet_id'] = $outlet_id;
            $this->data['inventory_id'] = $inventory_id;
            $this->data['history_status'] =$this->store_model->get("enum_stock_history_status")->result();
            $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
            $this->data['results'] = $this->report_model->detail_inventory(array("start_date" => $start_date, "end_date" => $end_date, "outlet_id" => $outlet_id, "inventory_id" => $inventory_id,"store_id"=>$this->data['setting']['store_id']));
            $report = 'detail_inventory';
            $html = $this->load->view('admin/report/report-detail-inventory-pdf', $this->data, true);
        } elseif ($type == "summary_receive_order") {
            $pdf_orientation = "portrait";
            $this->load->model("report_model");
            $this->data['is_print'] = TRUE;
            $this->data['start_date'] = $start_date;
            $this->data['end_date'] = $end_date;
            $this->data['supplier_id'] = $supplier_id;
            $this->data['payment_method'] = $payment_method;
            $this->data['supplier'] = $this->store_model->get_one("supplier", $supplier_id);
            $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
            $this->data['results'] = $this->report_model->summary_receive_order(array("start_date" => $start_date, "end_date" => $end_date, "supplier_id" => $supplier_id, "payment_method" => $payment_method));
            $report = 'summary_receive_order';
            $html = $this->load->view('admin/report/report-summary-receive-order-pdf', $this->data, true);
        } elseif ($type == "summary_retur_order") {
            $pdf_orientation = "portrait";
            $this->load->model("report_model");
            $this->data['is_print'] = TRUE;
            $this->data['start_date'] = $start_date;
            $this->data['end_date'] = $end_date;
            $this->data['supplier_id'] = $supplier_id;
            $this->data['supplier'] = $this->store_model->get_one("supplier", $supplier_id);
            $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
            $this->data['results'] = $this->report_model->summary_retur_order(array("start_date" => $start_date, "end_date" => $end_date, "supplier_id" => $supplier_id));
            $report = 'summary_retur_order';
            $html = $this->load->view('admin/report/report-summary-retur-order-pdf', $this->data, true);
        } elseif ($type == "member_transaction") {
            $pdf_orientation = "portrait";
            $this->load->model("report_model");
            $this->data['is_print'] = TRUE;
            $this->data['start_date'] = $start_date;
            $this->data['end_date'] = $end_date;
            $this->data['member_id'] = $member_id;
            $this->data['member'] = $this->store_model->get_one("member", $member_id);
            $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
            $this->data['results'] = $this->report_model->member_transaction(array("start_date" => $start_date, "end_date" => $end_date, "member_id" => $member_id));
            $report = 'member_transaction';
            $html = $this->load->view('admin/report/report-member-transaction-pdf', $this->data, true);
        } elseif ($type == "voucher_used") {
            $pdf_orientation = "portrait";
            $this->load->model("report_model");
            $this->data['is_print'] = TRUE;
            $this->data['start_date'] = $start_date;
            $this->data['end_date'] = $end_date;
            $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
            $this->data['results'] = $this->report_model->voucher_used(array("start_date" => $start_date, "end_date" => $end_date));
            $report = 'voucher_used';
            $html = $this->load->view('admin/report/report-voucher-used-pdf', $this->data, true);
        } elseif ($type == "promo_used") {
            $pdf_orientation = "portrait";
            $this->load->model("report_model");
            $this->data['is_print'] = TRUE;
            $this->data['start_date'] = $start_date;
            $this->data['end_date'] = $end_date;
            $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
            $this->data['results'] = $this->report_model->promo_used(array("start_date" => $start_date, "end_date" => $end_date));
            $report = 'promo_used';
            $html = $this->load->view('admin/report/report-promo-used-pdf', $this->data, true);
        } elseif ($type == "promo_cc") {
            $pdf_orientation = "portrait";
            $this->load->model("report_model");
            $this->data['is_print'] = TRUE;
            $this->data['start_date'] = $start_date;
            $this->data['end_date'] = $end_date;
            $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
            $this->data['results'] = $this->report_model->promo_cc(array("start_date" => $start_date, "end_date" => $end_date, "promo_cc_id" => $promo_cc_id));
            $report = 'promo_cc';
            $html = $this->load->view('admin/report/report-promo-cc-pdf', $this->data, true);
        } elseif ($type == "bon_bill") {
            $pdf_orientation = "portrait";
            $this->load->model("report_model");
            $this->data['is_print'] = TRUE;
            $this->data['start_date'] = $start_date;
            $this->data['end_date'] = $end_date;
            $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
            $this->data['results'] = $this->report_model->bon_bill(array("start_date" => $start_date, "end_date" => $end_date));
            $report = 'bon_bill';
            $html = $this->load->view('admin/report/report-bon-bill-pdf', $this->data, true);
        } elseif ($type == "pending_bill") {
            $pdf_orientation = "portrait";
            $this->load->model("report_model");
            $pending_type = $this->uri->segment(4);
            if ($pending_type == 6) {
                $subtitle = "Laporan Pending Bill Perusahaan";
            } else if ($pending_type == 7) {
                $subtitle = "Laporan Pending Bill Karyawan";
            }

            $this->data['title'] = $title;
            $this->data['subtitle'] = $subtitle;
            $this->data['is_print'] = TRUE;
            $this->data['start_date'] = $start_date;
            $this->data['end_date'] = $end_date;
            $this->data['pending_type'] = $pending_type;
            $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
            $this->data['results'] = $this->report_model->pending_bill(array("start_date" => $start_date, "end_date" => $end_date, "type" => $pending_type));
            $report = 'pending_bill';
            $html = $this->load->view('admin/report/report-pending-bill-pdf', $this->data, true);
        }  elseif ($type == "compliment") {
            $pdf_orientation = "portrait";
            $this->load->model("report_model");
            $this->data['is_print'] = TRUE;
            $this->data['start_date'] = $start_date;
            $this->data['end_date'] = $end_date;
            $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
            $this->data['results'] = $this->report_model->compliment(array("start_date" => $start_date, "end_date" => $end_date));
            $report = 'compliment';
            $html = $this->load->view('admin/report/report-compliment-pdf', $this->data, true);
        } elseif ($type == "spoiled") {
            $pdf_orientation = "portrait";
            $this->load->model("report_model");
            $this->data['is_print'] = TRUE;
            $this->data['inventory_id'] = $inventory_id;
            $this->data['outlet_id'] = $outlet_id;
            $this->data['start_date'] = $start_date;
            $this->data['end_date'] = $end_date;
            $this->data['outlet'] = $this->store_model->get_one("outlet", $outlet_id);
            $this->data['inventory'] = $this->store_model->get_one("outlet", $inventory_id);
            $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
            $this->data['lists'] = $this->report_model->spoiled(array("inventory_id" => $inventory_id, "outlet_id" => $outlet_id, "start_date" => $start_date, "end_date" => $end_date));
            $report = 'spoiled';
            $html = $this->load->view('admin/report/report-spoiled-pdf', $this->data, true);
        }elseif ($type=="cost_opname") {
          $pdf_orientation="portrait";
          $report = 'cost_opname';
          $this->load->model("report_model");
          $this->data['is_print'] = TRUE;
          $this->load->model("inventory_model");      
          $results=$this->inventory_model->cost_opname(array("inventory_id"=>$inventory_id, "start_date"=>$start_date,"end_date"=>$end_date));
          $this->data['inventories']=$results;          
          $html=$this->load->view('admin/report/report-cost-opname-pdf', $this->data,true);
        }else  if($type == "sos"){
            $jsonObj = $this->get_sos_raw_data($start_date, $end_date,false);
            $data['data'] =$jsonObj->data;
            $data['total_gross'] =0;
            $data['total_nett'] =0;
            $data['total_tax'] =0;
            $data['total_net_tax'] =0;
            $data['total_cost'] =0;
            $data['total_qty'] =0;
            foreach ($jsonObj->data  as $row) {
                $data['total_gross'] += $row->gross;
                $data['total_nett'] += $row->nett;
                $data['total_tax'] += $row->tax;
                $data['total_net_tax'] += $row->net_tax;
                $data['total_cost'] += $row->cost;
                $data['total_qty'] += $row->qty;
            }
            $data['periode'] = transaction_periode($start_date,$end_date);
            $report = 'sos';
            $html = $this->load->view('admin/report/report_summary_of_sales_to_pdf_v', $data, true);
        }else  if($type == "inventory_adjustment"){

            $this->load->model("report_model");
            $date = $this->input->post("date");
            $outlet_id = $this->input->post("outlet_id");
            $data['is_print'] = TRUE;
            $data['date'] = $date;
            $data['outlet_id'] = $outlet_id;
            $data['store_id'] =  $this->data['setting']['store_id'];
            $data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
            $data['results'] = $this->report_model->inventory_adjustment(array("date" => $date, "outlet_id" => $outlet_id,"store_id"=>$this->data['setting']['store_id']));

            $data['periode'] = transaction_periode($start_date,$end_date);
            $report = 'inventory_adjustment';
            $html = $this->load->view('admin/report/report-inventory-adjustment-pdf', $data, true);
        }else if($type == "aging_report"){

            $this->load->model("report_model");
            $supplier_id = $this->input->post("supplier_id");
            $filter_date = $this->input->post("filter_date"); 
            
            //GET DATA STORE
            $data['store_id'] =  $this->data['setting']['store_id'];
            $data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);

            //GET DATA AGING
            $params = array();
            $params['supplier_id'] = $supplier_id;
            $params['filter_date'] = $filter_date;
            $data['results'] = $this->report_model->get_aging_report($params);

            //SEND DATA 
            $data['filter_date'] = $start_date; 
            $data['is_print'] = TRUE;
            $data['report_end_date'] = date("Y-m-d");
            $report = 'aging';
            $html = $this->load->view('admin/report/report-umur-hutang-pdf', $data, true); 
        }else if($type == "member_discount_detail"){
            $pdf_orientation = "portrait";
            $this->load->model("report_model");
            $this->data['is_print'] = TRUE;
            $this->data['start_date'] = $start_date;
            $this->data['end_date'] = $end_date;
            $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
            $this->data['results'] = $this->report_model->member_discount_detail(array("start_date" => $start_date, "end_date" => $end_date));
            $report = 'promo_used';
            $html = $this->load->view('admin/report/report-member-discount_detail-pdf', $this->data, true);
        }else if ($type == "delivery_service") {
            $pdf_orientation = "portrait";
            $this->load->model("report_model");
            $start_date = $this->input->post("start_period");
            $end_date = $this->input->post("end_period");
            $this->data['start_date'] = $start_date;
            $this->data['end_date'] = $end_date;
            $this->data['is_print'] = TRUE;
            $this->data['results'] = $this->report_model->delivery_service(array("start_date" => $start_date, "end_date" => $end_date));
            $report = 'delivery_service';
            $html = $this->load->view('admin/report/report-delivery-service-pdf', $this->data, true);
        }
        $date = new DateTime();
        $data = pdf_create($html, '', false, $pdf_orientation);
        if (in_array($type, array("inventory_adjustment","detail_inventory","transfer_menu","transfer_inventory","spoiled", "compliment", "bon_bill", "pending_bill", "inventory_stock", "inventory_stock_detail", "total_sales_waiter", "total_sales_waiter_detail", "total_quantity_order_table_waiter", "achievement_waiter","taxes", "achievement_waiter_detail", "kitchen_duration", "summary_year", "summary_inventory", "summary_receive_order", "summary_retur_order", "member_transaction", "voucher_used", "promo_used","promo_cc","cost_opname","sos","profit_loss","sales_category","aging_report","member_discount_detail","delivery_service"))) {
            $filename = 'report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis') . '.pdf';
            header("Content-type:application/pdf");
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            echo $data;
        } else {

            $filename = 'assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis') . '.pdf';
            write_file($filename, $data);
            echo json_encode($filename);

        }


        //if you want to write it to disk and/or send it as an attachment


    }

    public function inventory_stocks()
    {
        $this->data['title'] = "Laporan Stok Inventory";
        $this->data['subtitle'] = "Laporan Stok Inventory";
        $this->data['all_store']  = $this->store_model->get_store_dropdown();
        $this->data['data_url'] = base_url(SITE_ADMIN . '/reports/get_inventory_stock_data');


        $this->load->model("inventory_model");
        $date = date("Y-m-d");
        $report_end_date = date("Y-m-d");
        $this->data['is_print'] = FALSE;
        $this->data['date'] = $date;
        $this->data['report_end_date'] = $report_end_date;
        $this->data['store_id'] =  $this->data['setting']['store_id'];
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->inventory_model->get_inventory_stock_data(array("store_id" => $this->data['setting']['store_id'], "date" => $date, "report_end_date" => $report_end_date));





        $this->data['content'] .= $this->load->view('admin/report/report-inventory-stock', $this->data, true);
        $this->render('report');
    }

    public function get_inventory_stock_data()
    {
        $this->load->model("inventory_model");
        $date = $this->input->post('date');
        $report_end_date = $this->input->post('report_end_date');
        $this->data['is_print'] = FALSE;
        $this->data['date'] = $date;
        $this->data['report_end_date'] = $report_end_date;
        $this->data['store_id'] =  $this->data['setting']['store_id'];
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->inventory_model->get_inventory_stock_data(array("store_id" => $this->data['setting']['store_id'], "date" => $date, "report_end_date" => $report_end_date));
        $this->load->view('admin/report/report-inventory-stock-pdf', $this->data);
    }

    public function inventory_stock_detail()
    {
        $uom_id = $this->input->get("uom_id");
        $inventory_id = $this->input->get("inventory_id");
        $from_date = $this->input->get("from_date");
        $to_date = $this->input->get("to_date");
        $this->data['is_print'] = FALSE;
        $this->data['from_date'] = $from_date;
        $this->data['to_date'] = $to_date;
        $this->load->model("inventory_model");
        $this->data['title'] = "Laporan Detail Stok Inventory";
        $this->data['subtitle'] = "Laporan Detail Stok Inventory";
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['data_inventory'] = $this->inventory_model->get_by('inventory', $inventory_id);
        $this->data['data_uom'] = $this->inventory_model->get_one("uoms", $uom_id);
        $this->data['data_url'] = base_url(SITE_ADMIN . '/reports/get_inventory_stock_detail_data');
        $this->data['results'] = $this->inventory_model->get_inventory_stock_data_detail(array("uom_id" => $uom_id, "store_id" => $this->data['setting']['store_id'], "inventory_id" => $inventory_id, "from_date" => $from_date,"to_date"=>$to_date));
        $this->data['report_inventory_detail'] = $this->load->view('admin/report/report-inventory-stock-detail-pdf', $this->data, true);
        $this->data['content'] .= $this->load->view('admin/report/report-inventory-stock-detail', $this->data, true);
        $this->render('report');
    }

    public function total_sales_waiter()
    {
        $this->load->model("user_model");
        $this->load->model("report_model");
        $this->data['title'] = "Craft Productivity";
        $this->data['subtitle'] = "Laporan Total Penjualan Waiter";

        $this->data['data_url'] = base_url(SITE_ADMIN . '/reports/get_total_sales_waiter');
        $this->data['waiter_lists'] = $this->user_model->get_online_by_group("'dinein'");

        $user_id = $this->input->post('user_id');
        $date = date("Y-m-d");
        $this->data['is_print'] = FALSE;
        $this->data['date'] = $date;
        $this->data['user_id'] = $user_id;
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->total_sales_waiter(array("user_id" => $user_id, "date" => $date));

        $this->data['content'] .= $this->load->view('admin/report/report-total-sales-waiter', $this->data, true);
        $this->render('report');
    }

    public function total_sales_waiter_detail()
    {
        $this->load->model("report_model");
        $this->data['title'] = "Craft Productivity";
        $this->data['subtitle'] = "Laporan Detail Total Penjualan Waiter";
        $user_id = $this->input->get('user_id');
        $date = $this->input->get('date');
        $this->data['is_print'] = FALSE;
        $this->data['date'] = $date;
        $this->data['user_id'] = $user_id;
        $this->data['user'] = $this->store_model->get_one('users', $user_id);
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->total_sales_waiter_detail(array("user_id" => $user_id, "date" => $date));
        $this->data['results2'] = $this->report_model->total_quantity_order_table_waiter_detail(array("user_id" => $user_id, "date" => $date));
        $this->data['report_total_sales_detail'] = $this->load->view("admin/report/report-total-sales-waiter-detail-pdf", $this->data, true);
        $this->data['content'] .= $this->load->view('admin/report/report-total-sales-waiter-detail', $this->data, true);
        $this->render('report');
    }

    public function get_total_sales_waiter()
    {
        $this->load->model("report_model");
        $user_id = $this->input->post('user_id');
        $date = $this->input->post('date');
        $this->data['is_print'] = FALSE;
        $this->data['date'] = $date;
        $this->data['user_id'] = $user_id;
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->total_sales_waiter(array("user_id" => $user_id, "date" => $date));
        $this->load->view('admin/report/report-total-sales-waiter-pdf', $this->data);
    }

    public function total_quantity_order_table_waiter()
    {
        $this->load->model("user_model");
        $this->load->model("report_model");
        $this->data['title'] = "Craft Productivity";
        $this->data['subtitle'] = "Laporan Total Kuantitas Penjualan Waiter";
        $this->data['data_url'] = base_url(SITE_ADMIN . '/reports/get_total_quantity_order_table_waiter');
        $this->data['waiter_lists'] = $this->user_model->get_online_by_group("'dinein'");


        $user_id = $this->input->post('user_id');
        $date = date("Y-m-d");
        $this->data['is_print'] = FALSE;
        $this->data['date'] = $date;
        $this->data['user_id'] = $user_id;
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->total_quantity_order_table_waiter(array("user_id" => $user_id, "date" => $date));

        $this->data['content'] .= $this->load->view('admin/report/report-total-quantity-order-table-waiter', $this->data, true);
        $this->render('report');
    }

    public function get_total_quantity_order_table_waiter()
    {
        $this->load->model("report_model");
        $user_id = $this->input->post('user_id');
        $date = $this->input->post('date');
        $this->data['is_print'] = FALSE;
        $this->data['date'] = $date;
        $this->data['user_id'] = $user_id;
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->total_quantity_order_table_waiter(array("user_id" => $user_id, "date" => $date));
        $this->load->view('admin/report/report-total-quantity-order-table-waiter-pdf', $this->data);
    }

    public function achievement_waiter()
    {
        $this->load->model("user_model");
        $this->load->model("report_model");
        $this->data['title'] = "Craft Productivity";
        $this->data['subtitle'] = "Laporan Pencapaian Target Waiter";
        $this->data['data_url'] = base_url(SITE_ADMIN . '/reports/get_achievement_waiter');
        $this->data['waiter_lists'] = $this->user_model->get_online_by_group("'dinein'");


        $user_id = $this->input->post('user_id');
        $month_year = date("m-Y");
        $temp = explode("-", $month_year);
        $month = $temp[0];
        $year = $temp[1];
        $this->data['is_print'] = FALSE;
        $this->data['month_year'] = $month_year;
        $this->data['user_id'] = $user_id;
        $this->data['month'] = $month;
        $this->data['year'] = $year;
        $this->data['user'] = $this->store_model->get_one("users", $user_id);
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->achievement_waiter(array("user_id" => $user_id, "month" => $month, "year" => $year));
        $this->data['content'] .= $this->load->view('admin/report/report-achievement-waiter', $this->data, true);
        $this->render('report');
    }

    public function get_achievement_waiter()
    {
        $this->load->model("report_model");
        $user_id = $this->input->post('user_id');
        $month_year = $this->input->post('month_year');
        $temp = explode("-", $month_year);
        $month = $temp[0];
        $year = $temp[1];
        $this->data['is_print'] = FALSE;
        $this->data['month_year'] = $month_year;
        $this->data['user_id'] = $user_id;
        $this->data['month'] = $month;
        $this->data['year'] = $year;
        $this->data['user'] = $this->store_model->get_one("users", $user_id);
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->achievement_waiter(array("user_id" => $user_id, "month" => $month, "year" => $year));
        $this->load->view('admin/report/report-achievement-waiter-pdf', $this->data);
    }

    public function achievement_waiter_detail()
    {
        $this->load->model("report_model");
        $target_id = $this->input->get('target_id');
        $user_id = $this->input->get('user_id');
        $month = $this->input->get('month');
        $year = $this->input->get('year');
        $this->data['is_print'] = FALSE;
        $this->data['target_id'] = $target_id;
        $this->data['user_id'] = $user_id;
        $this->data['month'] = $month;
        $this->data['year'] = $year;
        $this->data['target'] = $this->store_model->get_one("target", $target_id);
        $this->data['user'] = $this->store_model->get_one("users", $user_id);
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        if ($this->data['target']->target_type == 1) {
            $this->data['results'] = $this->report_model->achievement_waiter_detail_by_total(array("target_id" => $target_id, "month" => $month, "year" => $year));
        } else {
            $this->data['results'] = $this->report_model->achievement_waiter_detail_by_item(array("target_id" => $target_id, "month" => $month, "year" => $year));
        }
        $this->data['report_achievement_waiter_detail'] = $this->load->view("admin/report/report-achievement-waiter-detail-pdf", $this->data, true);
        $this->data['content'] .= $this->load->view('admin/report/report-achievement-waiter-detail', $this->data, true);
        $this->render('report');
    }

    public function kitchen_duration()
    {
        $this->load->model("user_model");
        $this->data['title'] = "Craft Productivity";
        $this->data['subtitle'] = "Laporan Waktu Proses Pesanan Kitchen";
        $this->data['data_url'] = base_url(SITE_ADMIN . '/reports/get_kitchen_duration');

        $this->load->model("report_model");
        $month_year = date("m-Y");
        $temp = explode("-", $month_year);
        $month = $temp[0];
        $year = $temp[1];
        $this->data['is_print'] = FALSE;
        $this->data['month_year'] = $month_year;
        $this->data['month'] = $month;
        $this->data['year'] = $year;
        $this->data['reward_kitchen'] = $this->report_model->get_all_where("reward_kitchen", array());
        if (sizeof($this->data['reward_kitchen']) > 0) $this->data['reward_kitchen'] = $this->data['reward_kitchen'][0];
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->kitchen_duration(array("month" => $month, "year" => $year));


        $this->data['content'] .= $this->load->view('admin/report/report-kitchen-duration', $this->data, true);
        $this->render('report');
    }

    public function get_kitchen_duration()
    {
        $this->load->model("report_model");
        $month_year = $this->input->post('month_year');
        $temp = explode("-", $month_year);
        $month = $temp[0];
        $year = $temp[1];
        $this->data['is_print'] = FALSE;
        $this->data['month_year'] = $month_year;
        $this->data['month'] = $month;
        $this->data['year'] = $year;
        $this->data['reward_kitchen'] = $this->report_model->get_all_where("reward_kitchen", array());
        if (sizeof($this->data['reward_kitchen']) > 0) $this->data['reward_kitchen'] = $this->data['reward_kitchen'][0];
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->kitchen_duration(array("month" => $month, "year" => $year));
        $this->load->view('admin/report/report-kitchen-duration-pdf', $this->data);
    }

    public function adjustment()
    {
        $this->load->model("report_model");
        $this->load->model("store_model");
        $this->data['title'] = "Laporan Stok Opname";
        $this->data['subtitle'] = "Laporan Stok Opname";
        $this->data['outlets'] = $this->report_model->get_all_where("outlet", array("store_id" => $this->data['setting']['store_id']));
        $this->data['all_store']  = $this->store_model->get_store_dropdown();
        $this->data['data_url'] = base_url(SITE_ADMIN . '/reports/get_inventory_adjusment');



        $this->load->model("report_model");
        $date = date("Y-m-d");
        $outlet_id = $this->input->post("outlet_id");
        $this->data['is_print'] = FALSE;
        $this->data['date'] = $date;
        $this->data['outlet_id'] = $outlet_id;
        $this->data['store_id'] =  $this->data['setting']['store_id'];
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->inventory_adjustment(array("date" => $date, "outlet_id" => $outlet_id,"store_id"=>$this->data['setting']['store_id']));



        $this->data['content'] .= $this->load->view('admin/report/report-inventory-adjusment', $this->data, true);
        $this->render('report');
    }

    public function get_inventory_adjustment()
    {
        $this->load->model("report_model");
        $date = $this->input->post("date");
        $outlet_id = $this->input->post("outlet_id");
        $this->data['is_print'] = FALSE;
        $this->data['date'] = $date;
        $this->data['outlet_id'] = $outlet_id;
        $this->data['store_id'] =  $this->data['setting']['store_id'];
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->inventory_adjustment(array("date" => $date, "outlet_id" => $outlet_id,"store_id"=>$this->data['setting']['store_id']));
        $this->load->view('admin/report/report-inventory-adjustment-pdf', $this->data);
    }

    /*
    *   Created by : M. Tri
    *   Created at : 30/08/2016
    *   Report function to reporting pending bill information, both company and employee
    */

    public function pending_bill($type) {
        $this->load->model("report_model");

        $title = "Laporan Pending Bill";        
        if ($type == 6) {
            $subtitle = "Laporan Pending Bill Perusahaan";
        } else if ($type == 7) {
            $subtitle = "Laporan Pending Bill Karyawan";
        }

        $this->data['title'] = $title;
        $this->data['subtitle'] = $subtitle;

        $start_date = date("Y-m-d");
        $end_date = date("Y-m-d");
        
        $this->data['is_print'] = FALSE;
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['pending_type'] = $type;
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->pending_bill(array("start_date" => $start_date, "end_date" => $end_date, 'type' => $type));

        $this->data['content'] .= $this->load->view('admin/report/report-pending-bill', $this->data, true);
        $this->render('report');
    }

    public function get_pending_bill_data($type) {
        $this->load->model("report_model");
        $title = "Laporan Pending Bill";        
        if ($type == 6) {
            $subtitle = "Laporan Pending Bill Perusahaan";
        } else if ($type == 7) {
            $subtitle = "Laporan Pending Bill Karyawan";
        }

        $this->data['title'] = $title;
        $this->data['subtitle'] = $subtitle;

        $start_date = $this->input->post("start_date");
        $end_date = $this->input->post("end_date");
        $this->data['is_print'] = FALSE;
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['pending_type'] = $type;
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->pending_bill(array("start_date" => $start_date, "end_date" => $end_date, 'type' => $type));
        $this->load->view('admin/report/report-pending-bill-pdf', $this->data);
    }

    public function aging() {
        $this->data['title'] = "Laporan Umur Hutang";
        $this->data['subtitle'] = "Laporan Umur Hutang";

        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['supplier_lists'] = $this->store_model->get("supplier")->result();

        $this->data['content'] .= $this->load->view('admin/report/report-umur-hutang', $this->data, true);

        $this->render('report');
    }

    public function get_aging_data() {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $post_array = array();
        parse_str($this->input->post('param'), $post_array);

        $filter_date = $post_array['filter_date'];
        $supplier_id = $post_array['supplier_id'];

        $this->datatables->select("i.name,
                                   (pord.received_quantity * pord.price) AS total_payment,
                                   IF((DATEDIFF('".$filter_date."', po.order_at) >= 0 AND DATEDIFF('".$filter_date."', po.order_at) <= 30), (pord.received_quantity * pord.price), 0) AS due_1,
                                   IF((DATEDIFF('".$filter_date."', po.order_at) > 30 AND DATEDIFF('".$filter_date."', po.order_at) <= 60), (pord.received_quantity * pord.price), 0) AS due_2,
                                   IF((DATEDIFF('".$filter_date."', po.order_at) > 60 AND DATEDIFF('".$filter_date."', po.order_at) <= 90), (pord.received_quantity * pord.price), 0) AS due_3,
                                   IF((DATEDIFF('".$filter_date."', po.order_at) > 90), (pord.received_quantity * pord.price), 0) AS due_4,
                                  ", false)
        ->from('purchase_order_receive_detail pord')
        ->join('purchase_order_receive por', 'por.id = pord.purchase_order_receive_id')
        ->join('purchase_order_detail pod', 'pod.id = pord.purchase_order_detail_id')
        ->join('purchase_order po', 'po.id = pod.purchase_order_id')
        ->join('inventory i', 'i.id = pod.inventory_id')
        ->where('por.payment_method', 2)
        ->where('por.payment_status', 0);

        if($supplier_id){
            $this->datatables->where('po.supplier_id', $supplier_id);
        }

        $this->datatables->unset_column('due_1');
        $this->datatables->add_column('due_1', '$1', 'convert_rupiah(due_1)');
        $this->datatables->unset_column('due_2');
        $this->datatables->add_column('due_2', '$1', 'convert_rupiah(due_2)');
        $this->datatables->unset_column('due_3');
        $this->datatables->add_column('due_3', '$1', 'convert_rupiah(due_3)');
        $this->datatables->unset_column('due_4');
        $this->datatables->add_column('due_4', '$1', 'convert_rupiah(due_4)');
        $this->datatables->unset_column('total_payment');
        $this->datatables->add_column('total_payment', '$1', 'convert_rupiah(total_payment)');

        echo $this->datatables->generate();
    }

    public function price_analyst() {
        $this->data['title'] = "Laporan Analisis Harga";
        $this->data['subtitle'] = "Laporan Analisis Harga";

        $this->load->model('categories_model');

        $this->data['all_outlet'] = $this->categories_model->get_outlet_dropdown_by_store_id($this->data['setting']['store_id']);
        $this->data['all_category'] = $this->categories_model->get_chained_category_dropdown(0);

        $this->data['data_url'] = base_url(SITE_ADMIN . '/reports/get_price_analyst');
        $this->data['content'] .= $this->load->view('admin/report/report-analisis-harga', $this->data, true);
        $this->render('report');
    }

    public function get_price_analyst() {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $post_array = array();
        parse_str($this->input->post('param'), $post_array);

        $store_id = $this->data['setting']['store_id'];
        $outlet_id = $post_array['outlet_id'];
        $category_menu_id = $post_array['category_menu_id'];

        $this->datatables->select('
                s.store_name as strname,
                c.category_name as ctgname,
                m.menu_name, 
                m.menu_hpp, 
                m.menu_price,
                (m.menu_price - m.menu_hpp) AS gross')
        ->from('menu m')
        ->join('category c', 'c.id=m.category_id')
        ->join('outlet o', 'o.id=c.outlet_id')
        ->join('store s', 's.id=o.store_id')

        ->add_column('margin', '$1', 'margin(menu_price, menu_hpp)')
        ->add_column('markup', '$1', 'markup(menu_price, menu_hpp)')
        ->add_column('menu_price', '$1', 'convert_rupiah(menu_price)')
        ->add_column('menu_hpp', '$1', 'convert_rupiah(menu_hpp)')
        ->add_column('gross', '$1', 'convert_rupiah(gross)');
        if($store_id){
            $this->datatables->where('s.id', $store_id);
        }
        if($outlet_id){
            $this->datatables->where('o.id', $outlet_id);
        }
        if($category_menu_id){
            $this->datatables->where('c.id', $category_menu_id);
        }

        echo $this->datatables->generate();
    }

    public function list_inventory() {
        $this->data['title'] = "Laporan Data Inventory";
        $this->data['subtitle'] = "Laporan Data Inventory";
        $this->data['inventory_lists']=$this->store_model->get("inventory")->result();

        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['supplier_lists'] = $this->store_model->get("supplier")->result();
        $this->data['content'] .= $this->load->view('admin/report/report-data-inventory', $this->data, true);

        $this->render('report');
    }

    public function get_inventory_data() {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $post_array = array();
        parse_str($this->input->post('param'), $post_array);

        $inventory_id = $post_array['inventory_id'];

        $this->datatables->select('i.name,
                                   CONCAT(SUM(sh.quantity)," ",u.code) AS on_hand,
                                   ROUND(AVG(sh.price), 0) * SUM(sh.quantity) AS total_value,
                                   ROUND(AVG(sh.price), 0) AS average_cost,
                                   (SELECT price FROM stock_history WHERE inventory_id = i.id HAVING MAX(created_at)) AS current_price
                                  ', false)
        ->from('inventory i')
        ->join('stock_history sh', 'sh.inventory_id = i.id')
        ->join('uoms u', 'u.id = i.uom_id');

        if ($inventory_id) {
            $this->datatables->where('i.id', $inventory_id);
        }
        $this->datatables->group_by('i.id');

        $this->datatables->unset_column('total_value');
        $this->datatables->add_column('total_value', '$1', 'convert_rupiah(total_value)');
        $this->datatables->unset_column('average_cost');
        $this->datatables->add_column('average_cost', '$1', 'convert_rupiah(average_cost)');
        $this->datatables->unset_column('current_price');
        $this->datatables->add_column('current_price', '$1', 'convert_rupiah(current_price)');

        echo $this->datatables->generate();
    }

    public function kontra_bon()
    {
        $this->data['title'] = "Kontra Bon";
        $this->data['subtitle'] = "Kontra Bon";

        $this->load->model("inventory_model");
        $this->load->model("report_model");
        $supplier_id = 0;
        $start_date = date("Y-m-d")." 00:00";
        $end_date = date("Y-m-d")." 23:59";
        $this->data['is_print'] = FALSE;
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['results'] = $this->inventory_model->get_kontra_bon(array("start_date" => $start_date, "end_date" => $end_date, "supplier_id" => $supplier_id));

        $this->data['content'] = $this->load->view('admin/report/report-kontra-bon', $this->data, true);
        $this->render('report');
    }

    public function get_data_kontra_bon() {
        $this->load->model("inventory_model");
        $supplier_id = $this->input->post('supplier_id');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $this->data['is_print'] = TRUE;
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['results'] = $this->inventory_model->get_kontra_bon(array("start_date" => $start_date, "end_date" => $end_date, "supplier_id" => $supplier_id));
        $this->load->view('admin/report/report-kontra-bon-pdf', $this->data);
    }


    public function member_discount_detail()
    {
        $this->data['title'] = "Laporan Penggunaan Promo Member";
        $this->data['subtitle'] = "Laporan Penggunaan Promo Member";


        $start_date = date("Y-m-d");
        $end_date = date("Y-m-d");
        $this->load->model("report_model");
        $this->data['is_print'] = FALSE;
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->member_discount_detail(array("start_date" => $start_date, "end_date" => $end_date));


        $this->data['content'] .= $this->load->view('admin/report/report-member-discount_detail', $this->data, true);
        $this->render('report');
    }

    public function get_member_discount_detail(){
        $this->load->model("report_model");
        $start_date = $this->input->post("start_date");
        $end_date = $this->input->post("end_date");
        $this->data['is_print'] = FALSE;
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->member_discount_detail(array("start_date" => $start_date, "end_date" => $end_date));
        $this->load->view('admin/report/report-member-discount_detail-pdf', $this->data);
    }

    public function delivery_service()
    {
        $this->data['title'] = "Laporan Jasa Kurir";
        $this->data['subtitle'] = "Laporan Jasa Kurir";

        $start_date = date("Y-m-d");
        $end_date = date("Y-m-d");
        $this->load->model("report_model");
        $this->data['is_print'] = FALSE;
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['results'] = $this->report_model->delivery_service(array("start_date" => $start_date, "end_date" => $end_date));

        $this->data['content'] .= $this->load->view('admin/report/report-delivery-service', $this->data, true);
        $this->render('report');
    }

    public function get_data_delivery_service()
    {
        $this->load->model("report_model");
        $start_date = $this->input->post("start_date");
        $end_date = $this->input->post("end_date");
        $this->data['is_print'] = FALSE;
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['results'] = $this->report_model->delivery_service(array("start_date" => $start_date, "end_date" => $end_date));
        $this->load->view('admin/report/report-delivery-service-pdf', $this->data);
    }
}