<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Reprint_Billings extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->data['title'] = "Re-Print Billing";
        $this->data['subtitle'] = "Laporan Re-Print Billing";
        $this->data['content'] .= $this->load->view('admin/reprint-billing-list', $this->data, true);
        $this->render('admin');
    }

    public function get_data()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));
        $params = array();
        parse_str($this->input->post('param'), $params);
        $start_date = $params['start_date'];
        $end_date = $params['end_date'];
        $where = "";
        if ($start_date && $end_date) {
            $where = 'where payment_date >= "' . $start_date . '" AND payment_date <= "' . $end_date . '" ';
        }
        $this->datatables->select('
      bill.receipt_number,bill.payment_date,bill.total_price,bill.customer_count,bill.is_take_away as order_type,bill.order_id,
      table.table_name,users.name as cashier,bill.customer_name
    ')
            ->from('bill')
            ->join('users', 'users.id=bill.cashier_id', 'left')
            ->join('table', 'table.id=bill.table_id', 'left')
            ->add_column('order_type', '$1', 'convert_is_take_away(order_type)')
            ->add_column('total_price', '$1', 'convert_rupiah(total_price)')
            ->add_column('payment_date', '$1', 'convert_date_with_time(payment_date)')
            ->add_column('actions', "<div class='btn-group center'>
        <a href='" . base_url(SITE_ADMIN . '/reprint_billings/detail/$1') . "' class='btn btn-sm btn-default' rel='tooltip' data-tooltip='tooltip' target='_blank' title='Detail'><i class='fa fa-search'></i></a>
        <a href='" . base_url(SITE_ADMIN . '/reprint_billings/prints/$1') . "' class='btn btn-sm btn-default' rel='tooltip' data-tooltip='tooltip' target='_blank' title='Print'><i class='fa fa-print'></i></a>
    ", 'receipt_number');
        if ($start_date) {
            $this->datatables->where('payment_date >= ', $start_date);
        }
        if ($end_date) {
            $this->datatables->where('payment_date <= ', $end_date);
        }
        $this->datatables->group_by("bill.id");
        if ($start_date && $end_date) {
            $this->datatables->add_column('periode', '$1', 'transaction_periode(' . $start_date . ',' . $end_date . ' )');
        }
        echo $this->datatables->generate();
    }

    public function detail($receipt_number)
    {
        $this->load->helper(array('datatables'));
        $this->load->model('order_model');
        $this->data['title'] = "Detail Transaksi";
        $this->data['subtitle'] = "Detail Transaksi";
        $data['bill_detail'] = $this->order_model->get_bill_detail($receipt_number);
        $data['bill_detail']->store = $this->order_model->get('store')->row()->store_name;
        $data['url_transaction_order'] = base_url(SITE_ADMIN . '/reprint_billings/get_data_transaction_order/' . $data['bill_detail']->bill_id);
        $data['url_transaction_minus'] = base_url(SITE_ADMIN . '/reprint_billings/data_transaction_by_type/' . $data['bill_detail']->bill_id . "/2");
        $data['url_transaction_plus'] = base_url(SITE_ADMIN . '/reprint_billings/data_transaction_by_type/' . $data['bill_detail']->bill_id . "/1");
        $data['url_transaction_payment'] = base_url(SITE_ADMIN . '/reprint_billings/data_transaction_payment/' . $data['bill_detail']->bill_id);
        $this->data['content'] .= $this->load->view('admin/reprint-billing-detail', $data, true);
        $this->render('report');
    }

    public function get_data_transaction_order($bill_id)
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));
        $this->datatables->select('menu_id as id, menu_name, quantity, price,(price*quantity) as subtotal,cogs')
            ->from('bill_menu')
            ->where('bill_id', $bill_id)
            ->unset_column('price')
            ->unset_column('subtotal')
            ->unset_column('cogs')
            ->add_column('price', '$1', 'convert_rupiah(price)')
            ->add_column('subtotal', '$1', 'convert_rupiah(subtotal)')
            ->add_column('cogs', '$1', 'convert_rupiah(cogs)');
        echo $this->datatables->generate();
    }

    function data_transaction_by_type($bill_id, $type)
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));
        $this->datatables->select('id, info, amount')
            ->from('bill_information')
            ->where('type', $type)
            ->where('bill_id', $bill_id)
            ->unset_column('amount')
            ->add_column('amount', '$1', 'convert_rupiah(amount)');
        echo $this->datatables->generate();
    }

    function data_transaction_payment($bill_id)
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));
        $this->datatables->select('bill_payment.id, info, amount, enum_payment_option.value as payment_option,enum_payment_option.id as payment_id')
            ->join('enum_payment_option', 'bill_payment.payment_option = enum_payment_option.id')
            ->from('bill_payment')
            ->where('bill_id', $bill_id)
            ->unset_column('amount')
            ->add_column('amount', '$1', 'convert_rupiah(amount)')
            ->unset_column('payment_option');
        echo $this->datatables->generate();
    }

    function prints($receipt_number)
    {
        $this->load->model("bill_menu_model", "bill_menu");
        $this->load->model("bill_menu_sidedish_model", "bill_menu_sidedish");
        $this->load->model("order_model");
        $this->load->model("cashier_model");
        $data_store = $this->store_model->get_store($this->data['setting']['store_id']);
        $bill = $this->order_model->get_bill_detail($receipt_number);
        $bill_menus = $this->bill_menu->get_all_by_bill_id($bill->bill_id);
        $data_order = $this->order_model->get_by_order_id($bill->order_id);
        $data_table = $this->order_model->get_one('table', $data_order->table_id);
        $voucher = $this->order_model->get_voucher_detail($bill->bill_id);
        $params = array();
        $params['datetime'] = $bill->end_order;
        if ($data_order->is_take_away == '0' && $data_order->is_delivery == '0') {
            $params['order_mode'] = 'Meja';
            $params['order_mode_name'] = $data_table->table_name;
        } else {
            $params['order_mode'] = 'Nama';
            $params['order_mode_name'] = $data_order->customer_name;
        }
        $order_lists = array();
        foreach ($bill_menus as $b) {
            $data = array(
                "product_name" => $b->menu_name,
                "product_amount" => $b->quantity,
                "product_price" => $b->price,
                "side_dish_list" => array()
            );
            array_push($order_lists, $data);
        }
        $params['order_list'] = $order_lists;
        $params['bill_minus'] = $this->cashier_model->get_all_where('bill_information', array('bill_id' => $bill->bill_id, 'type' => 2));
        $subtotal_1 = 0;
        foreach ($params['order_list'] as $o) {
            $subtotal_1 += ($o['product_amount'] * $o['product_price']);
        }
        $sub_total_2 = 0;
        foreach ($params['bill_minus'] as $o) {
            $sub_total_2 += $o->amount;
        }
        $change_payment = 0;
        $get_change = array_pop($this->cashier_model->get_all_where('bill_information', array('bill_id' => $bill->bill_id, 'type' => 3)));
        
        $params['subtotal'] = $subtotal_1;
        $params['subtotal_2'] = $sub_total_2;
        $params['bill'] = $this->cashier_model->get_by('bill', $bill->bill_id);
        $params['bill_plus'] = $this->cashier_model->get_all_where('bill_information', array('bill_id' => $bill->bill_id, 'type' => 1));
        $plus_total = 0;
        foreach ($params['bill_plus'] as $plus) {
            $plus_total += $plus->amount;
        }

        $total_payment = 0;
        $params['grand_total'] = ($subtotal_1-$sub_total_2)+$plus_total;
        $params['bill_payment'] = $this->order_model->get_bill_payment($bill->bill_id);

        // get customer cash payment from payment option
        foreach ($params['bill_payment'] as $key) {
            if ($key->payment_option == 1) {
                $customer_cash_payment = $key->amount;
            }
            $total_payment += $key->amount;
        }

        $vou = 0;
        foreach ($voucher as $key) {
            $vou += $key->amount;
        }

        $total = $vou + $total_payment;
        if (!empty($get_change)) {
            $params['change_due'] = $get_change->amount;
            $change_payment = $get_change->amount;
        } else {
            $params['change_due'] = $total - $params['grand_total'];
        }

        $params['customer_cash_payment'] = (isset($customer_cash_payment)) ? $customer_cash_payment + $change_payment : 0;
        
        $params['store_data'] = $data_store[0];
        $data['user'] = $this->order_model->get_by("users", $bill->created_by);
        $params['customer_data'] = "";
        $params['setting'] = $this->data['setting'];
        $params['data_order'] = $data_order;
        $this->load->helper(array('printer'));

        //get printer cashier
        $this->load->model("setting_printer_model");
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

            $params['header_bill'] = $this->order_model->get_all_where("template_global", array("reff" => "HEADER_BILL"));
            if (sizeof($params['header_bill']) > 0) $params['header_bill'] = $params['header_bill'][0];
             

            $params['footer_bill'] = $this->order_model->get_all_where("template_global", array("reff" => "FOOTER_BILL"));
            if (sizeof($params['footer_bill']) > 0) $params['footer_bill'] = $params['footer_bill'][0];
            

             $params['bill_temporary'] = $this->order_model->get_all_where("template_global", array("reff" => "BILL_TEMPORARY"));
            if (sizeof($params['bill_temporary']) > 0) $params['bill_temporary'] = $params['bill_temporary'][0];
            $params['setting'] = $this->data['setting'];

            if ($this->data['setting']['printer_format'] == 2) {
                if ($printer_obj->printer_width == 'generic') {
                    @print_checkout_bill2_generic($printer_location, $params, $this->data['user_profile_data'], FALSE, TRUE, $printer_setting);
                } else {
                    @print_checkout_bill2($printer_location, $params, $this->data['user_profile_data'], FALSE, TRUE, $printer_setting);
                }
            } else {
                if ($printer_obj->printer_width == 'generic') {
                    @print_checkout_bill_generic($printer_location, $params, $data['user'], FALSE, TRUE, $printer_setting);
                } else {
                    @print_checkout_bill($printer_location, $params, $data['user'], FALSE, TRUE, $printer_setting);
                }
            }
        }
        redirect(SITE_ADMIN . '/reprint_billings', 'refresh');
    }

    function export_to_pdf()
    {
        $this->load->helper("datatables_helper");
        $this->load->model("report_model");
        $this->load->helper(array('dompdf', 'file'));
        $params = $this->input->post();
        $lists = $this->report_model->reprint_billings($params);
        $data['lists'] = $lists;
        $data['params'] = $params;
        $data['is_print'] = TRUE;
        $pdf_orientation = "portrait";
        // $date = new DateTime();
        // $perpage = 30;
        // $total_page = ceil(sizeof($lists) / $perpage);
        // $filenames = array();
        // $report = 'reprint_billing';
        // for ($x = 0; $x < $total_page; $x++) {
        //     $data['from'] = $x * $perpage;
        //     $data['to'] = ($x * $perpage) + $perpage;
        //     if ($data['to'] > sizeof($lists)) $data['to'] = sizeof($lists);
        //     $html = $this->load->view('admin/reprint-billing-pdf', $data, true);
        //     $data_pdf = pdf_create($html, '', false, $pdf_orientation);
        //     $filename = 'assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis') . '_' . ($x + 1) . '.pdf';
        //     array_push($filenames, $filename);
        //     write_file($filename, $data_pdf);
        //     echo json_encode($filename);
        // }
        // $this->load->library("PDFMerger/PDFMerger");
        // $pdf = new PDFMerger;
        // foreach ($filenames as $file) {
        //     $pdf->addPDF($file, 'all');
        // }
        // $merge = 'assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis') . '.pdf';
        // $pdf->merge('file', $merge);
        // foreach ($filenames as $file) {
        //     @unlink($file);
        // }
        // redirect($merge);

        // $data['all_open_close'] = $this->order_model->get_open_close_data($open_by, $close_by, $start_date, $end_date);
        //     $data['periode'] = transaction_periode($start_date, $end_date);
            $report = 'reprint_billing';
            // $html = $this->load->view('admin/report/report_open_close_to_pdf_v', $data, true);

            $date = new DateTime();
            $perpage = 100;
            $offset = 0;
            $total_page = ceil(sizeof($lists) / $perpage);
            $filenames = array();

            $data['from'] = -1;
            $html = $this->load->view('admin/reprint-billing-pdf', $data, true);
            $data_pdf = pdf_create($html, '', false, $pdf_orientation);
            $filename = 'assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis') . '_0' . '.pdf';
            array_push($filenames, $filename);
            write_file($filename, $data_pdf);
            echo json_encode($filename);
            for ($x = 0; $x < $total_page; $x++) {
                $data['from'] = $x * $perpage;
                $data['to'] = ($x * $perpage) + $perpage;
                if ($data['to'] > sizeof($lists)) $data['to'] = sizeof($lists);
                $html = $this->load->view('admin/reprint-billing-pdf', $data, true);
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
            // redirect($merge);

            // $filename = 'assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis') . '.pdf';
            // write_file($filename, $data);
            // echo json_encode($filename);

            $file_url = $merge;
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary"); 
            header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\""); 
            readfile($file_url);

    }
}