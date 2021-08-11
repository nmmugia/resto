<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/17/2014
 * Time: 8:27 PM
 */
class Reports extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        redirect(SITE_ADMIN . '/reports/sales');
    }

    public function sales()
    {
        $this->data['title']    = $this->lang->line('sales_report_title');
        $this->data['subtitle'] = $this->lang->line('sales_report_title');

        $this->load->model('store_model');

        $this->data['all_store']  = $this->store_model->get_all_store();
        $this->data['all_outlet'] = $this->store_model->get_all_outlet();
        $this->data['content'] .= $this->load->view('admin/report-sales', $this->data, true);
        $this->render('report');
    }

    public function get_sales_detail($order_id)
    {
        $this->load->model('order_model');

        $data_order_menu = $this->order_model->calculate_total_order($order_id);
        $data            = $this->list_order_payment($data_order_menu);
        $data['data_order']     = $this->order_model->get_one('order', $order_id);

        $cashier_data = $this->order_model->get_one('users', $data['data_order']->cashier_id);
        $data['data_order']->cashier = $cashier_data->name;

        $this->data['content'] .= $this->load->view('admin/report-detail-sales', $data, true);
        $this->render('report');
    }

    private function list_order_payment($order_payment)
    {
        $order_list_data = '';
        $order_bill_data = '';
        if (! empty($order_payment)) {
            foreach ($order_payment['order_list'] as $order) {
                $order_list_data .= '<tr class="tOrder">';
                $order_list_data .= '<td>';
                $order_list_data .= $order->menu_name;
               if($order->discount_price != 0){
                    $order_list_data .= ' </br>DISKON ('.$order->discount_percent.'%)-'.$order->discount_name;
                }

                if (sizeof($order->option_list) != 0) {
                    foreach ($order->option_list as $opt) {
                        $order_list_data .= ' <br/>(' . $opt->option_name . ' - ' . $opt->option_value_name . ')';
                    }
                }
                else {
                    $order_list_data .= "";
                }
                // $order_list_data .= (! empty($order->option_list) ? ' <br/>(' . $order->option_list . ')' : '');
                $order_list_data .= '</td>';
                $order_list_data .= '<td class="border-side tb-align-center">' . $order->count . '</td>';
                $order_list_data .= '<td class="tb-align-right price-menu"  style="text-align:right;padding-right:70px;  data-price="'.$order->menu_price.'" style="padding-right: 10px">Rp ' . $order->menu_price . '';
                if($order->discount_price != 0){
                    $order_list_data .= '</br>- '.$order->discount_price;
                }
                $order_list_data .= '</td>';
                
                $order_list_data .= '</tr>';

                
                    # code...
                
                foreach ($order->side_dish_list as $sdh) {
                    $order_list_data .= '<tr>';
                    $order_list_data .= '<td>-- ' . $sdh->name . '</td>';
                    $order_list_data .= '<td class="border-side tb-align-center">' . $sdh->count . '</td>';
                    $order_list_data .= '<td class="tb-align-right" style="padding-right: 10px" >Rp ' . $sdh->price . '</td>';
                    $order_list_data .= '</tr>';
                }
            
            }

            if ($order_payment['subtotal'] != '0') {
                $order_bill_data .= '<tr>';
                // $order_bill_data .= '<td style="width:40%"></td>';
                $order_bill_data .= '<td style="width:30%"><b>Subtotal</b></td>';
                $order_bill_data .= '<td style="width:30%" id="subtotal-price" class="tb-align-right" data-price="' . $order_payment['subtotal'] . '">Rp ' . $order_payment['subtotal'] . '</td>';
                $order_bill_data .= '</tr>';

                $order_bill_data .= '<tr>';
                $order_bill_data .= '<td style="width:30%"><b>Diskon ('.$order_payment['discount_percent'].'%) '.$order_payment['discount_name'].'</b></td>';
                $order_bill_data .= '<td style="width:30%" id="discount-total" data-price="0" data-name="" class="tb-align-right">Rp ' . number_format($order_payment['discount_total'],0,'', '.') . '</td>';
                $order_bill_data .= '</tr>';
                foreach ($order_payment['tax_price'] as $tax) {

                    $order_bill_data .= '<tr>';
                    // $order_bill_data .= '<td></td>';
                    $order_bill_data .= '<td><b>' . $tax['name'] . '</b></td>';
                    $order_bill_data .= '<td id="tax-price" class="tb-align-right">Rp ' . $tax['value'] . '</td>';
                    $order_bill_data .= '</tr>';

                }

                foreach ($order_payment['extra_charge_price'] as $xtra) {

                    $order_bill_data .= '<tr>';
                    // $order_bill_data .= '<td></td>';
                    $order_bill_data .= '<td><b>' . $xtra['name'] . '</b></td>';
                    $order_bill_data .= '<td class="tb-align-right">Rp ' . $xtra['value'] . '</td>';
                    $order_bill_data .= '</tr>';

                }

                $order_bill_data .= '<tr>';
                // $order_bill_data .= '<td></td>';


                $order_bill_data .= '<td><b>Total</b></td>';
                $order_bill_data .= '<td id="total-price" class="tb-align-right"><b>Rp ' . $order_payment['total_price'] . '</b>';
                $order_bill_data .= '</td>';
                $order_bill_data .= '</tr>';
            }
        }
        $return_data['order_list'] = $order_list_data;
        $return_data['order_bill'] = $order_bill_data;

        return $return_data;
    }


    public function get_sales_data()
    {
        $start_date = $this->input->post('start_date');
        $end_date   = $this->input->post('end_date');
        $month      = $this->input->post('month');
        $year       = $this->input->post('year');
        $store      = $this->input->post('store');
        $outlet     = $this->input->post('outlet');
        $payment_method     = $this->input->post('payment_method');

        $this->load->model('order_model');
        $this->load->model('store_model');

        $ret_data            = array();
        $ret_data['data']    = null;
        $ret_data['status']  = false;
        $ret_data['message'] = "";
        $order_data = '';
        if (empty($start_date) && empty($end_date) && empty($month) && empty($year) && empty($store) && empty($outlet) && empty($payment_method)) {
            $ret_data['message'] = "Harap isi filter tanggal/bulan/tahun";
        }
        else {
            //fetching order data only
            if (! empty($month) || ! empty($year)) {
                if (! empty($month)) {
                    //monthly
                    $date_format_group = "Y-m-d";
                    if (empty($year)) {
                        $year       = date("Y");
                        $order_data = $this->order_model->get_order_by_date_range("2013-" . $month . "-01 00:00:00", $year . "-" . $month . "-31 23:59:59");
                    }
                    else
                        $order_data = $this->order_model->get_order_by_date_range($year . "-" . $month . "-01 00:00:00", $year . "-" . $month . "-31 23:59:59");
                }
                else {
                    //yearly
                    $date_format_group = "F";
                    $order_data        = $this->order_model->get_order_by_date_range($year . "-01-01 00:00:00", $year . "-12-31 23:59:59");

                }
            }
            else if (! empty($start_date) || ! empty($end_date)) {
                if ($end_date > $start_date) {
                    //get berdasarkan start & end date
                    $order_data = $this->order_model->get_order_by_date_range($start_date . " 00:00:00", $end_date . " 23:59:59");
                }
                else if ($end_date == $start_date) {
                    //get berdasarkan start & end date
                    $order_data = $this->order_model->get_order_by_date_range($start_date . " 00:00:00", $end_date . " 23:59:59");
                }
                else {
                    $ret_data['message'] = "Tanggal Akhir harus lebih besar dari Tanggal Mulai";
                    $order_data          = '';
                }
            }else{
                $ret_data['message'] = "Harap isi filter tanggal/bulan/tahun";

            }

            if ($order_data != '') {
                $sales_count = 0;

                //fetching all data
                foreach ($order_data as $key => $single_order) {
                    $history_data = $this->order_model->get_order_history_by_order($single_order->id);
                    if (empty($history_data)) {
                        unset($order_data[$key]);
                        continue;
                    }

                    $single_order->history_data = json_decode($history_data->history);

                    $item_count                   = 0;
                    $takeaway_count               = 0;
                    $dinein_count                 = 0;
                    $stores                       = array();
                    $outlets                      = array();
                    $menu_price                   = 0;
                    $menu_hpp                     = 0;
                    $side_dish_price              = 0;
                    $side_dish_hpp                = 0;
                    $additional_charge_total      = 0;
                    $tax_percentage_total         = 0;
                    $single_order->takeaway_count = 0;
                    $single_order->dinein_count   = 0;
                    $discount_menu_price = 0;
                    // $single_order->order_type = $single_order->history_data->order_data[0]->order_type;
                    if ($single_order->history_data->order_data[0]->order_type == 'Takeaway') {
                        $takeaway_count++;
                        $single_order->takeaway_count = $takeaway_count;
                        $single_order->dinein_count   = $single_order->dinein_count;
                    }
                    else {
                        $dinein_count++;
                        $single_order->takeaway_count = $single_order->takeaway_count;
                        $single_order->dinein_count   = $dinein_count;
                    }

                     
                    foreach ($single_order->history_data->order_list as $ord_key => $single_order_list) {
                        
                            //filtering by payment method
                        if (!empty($payment_method)) {
                            if ($single_order->history_data->order_data[0]->payment_method != $payment_method) {

                                continue;
                            }
                        }


                        //filtering by outlet/store
                        if (! empty($store)) {
                            if ($single_order_list->store_id != $store) {
                                // array_splice($single_order->history_data->order_list,$ord_key,1);
                                continue;
                            }
                        }
                        if (! empty($outlet)) {
                            if ($single_order_list->outlet_id != $outlet) {
                                // array_splice($single_order->history_data->order_list,$ord_key,1);
                                continue;
                            }
                        }

                        
                        // echo $single_order_list->menu_id."=";
                        // echo count($single_order_list->side_dish_list);
                        if (isset($single_order_list->side_dish_list)) {
                            foreach ($single_order_list->side_dish_list as $side_dish) {
                                $side_dish_hpp += $side_dish->side_dish_hpp * $side_dish->count;
                                $side_dish_price += $side_dish->side_dish_price * $side_dish->count;
                                // echo $side_dish_hpp;
                            }
                        }


                        if(isset($single_order_list->discount_price)){
                            $discount_menu_price +=$single_order_list->count * $single_order_list->discount_price;
                        }
                    
                        $menu_price += $single_order_list->menu_price * $single_order_list->count;
                        $menu_hpp += $single_order_list->menu_hpp * $single_order_list->count;
                        if (! in_array($single_order_list->store_name, $stores)) {
                            $stores[] = $single_order_list->store_name;
                        }
                        if (! in_array($single_order_list->outlet_name, $outlets)) {
                            $outlets[] = $single_order_list->outlet_name;
                        }

                        //$item_count++;
                        $item_count = $item_count + $single_order_list->count;
                    }


                    //kalau failed di filtering outlet/store, menu_pricenya  0 jangan ditampilkan
                    if (empty($menu_price)) {
                        unset($order_data[$key]);
                        continue;
                    }
                    $sales_count++;
                    foreach ($single_order->history_data->tax_price as $tax_price) {
                        $tax_percentage_total += $tax_price->tax_percentage;
                    }
                    foreach ($single_order->history_data->extra_charge_price as $extra_charge) {
                        $additional_charge_total += $extra_charge->charge_value;
                    }

                    if(isset($single_order->history_data->order_data[0]->discount_price)){
                        $discount_total_price = $single_order->history_data->order_data[0]->discount_price;
                    }else{
                         $discount_total_price = 0;
                    }
                    
                    // $item_count += count($single_order->history_data->order_list);

                    $single_order->stores                  = $stores;
                    $single_order->outlets                 = $outlets;
                    $single_order->tax_percentage_total    = $tax_percentage_total;
                    $single_order->additional_charge_total = $additional_charge_total;
                    $single_order->side_dish_price         = $side_dish_price;
                    $single_order->side_dish_hpp           = $side_dish_hpp;
                    $single_order->menu_price              = $menu_price ;
                    $single_order->menu_hpp                = $menu_hpp;
                    $single_order->item_count              = $item_count;
                    $single_order->total_price             = ($menu_price + $side_dish_price - $discount_menu_price) + 
                                                        (($menu_price + $side_dish_price - $discount_menu_price) * $tax_percentage_total / 100) + 
                                                        $additional_charge_total - $discount_total_price;
                
                
                    $single_order->total_price_str         = "Rp " . number_format($single_order->total_price, 0, ",", ".");
                    $single_order->payment_method          = $single_order->history_data->order_data[0]->payment_method;

                }

                //grouping by month/date if necessary
                if (! empty($year) || ! empty($month)) {
                    $grouped_order = array();
                    foreach ($order_data as $single_order) {
                        $order_date               = new DateTime($single_order->order_date);
                        $order_date               = $order_date->format($date_format_group);
                        $single_order->order_date = $order_date;

                        $existing_grouped_order = false;

                        foreach ($grouped_order as $single_grouped_order) {
                            if ($single_grouped_order->order_date == $order_date) {
                                $existing_grouped_order = $single_grouped_order;
                                break;
                            }
                        }
                        if ($existing_grouped_order) {
                            $existing_grouped_order->subtotal_price += $single_order->subtotal_price;
                            $existing_grouped_order->total_price += $single_order->total_price;
                            $existing_grouped_order->tax_price += $single_order->tax_price;
                            $existing_grouped_order->item_count += $single_order->item_count;
                            $existing_grouped_order->menu_hpp += $single_order->menu_hpp;
                            $existing_grouped_order->side_dish_hpp += $single_order->side_dish_hpp;
                            $existing_grouped_order->takeaway_count += $single_order->takeaway_count;
                            $existing_grouped_order->dinein_count += $single_order->dinein_count;
                            $existing_grouped_order->total_price_str = "Rp " . number_format($existing_grouped_order->total_price, 0, ",", ".");
                            foreach ($single_order->stores as $store_item) {
                                if (! in_array($store_item, $existing_grouped_order->stores)) {
                                    $existing_grouped_order->stores[] = $store_item;
                                }
                            }
                            foreach ($single_order->outlets as $outlet_item) {
                                if (! in_array($outlet_item, $existing_grouped_order->outlets)) {
                                    $existing_grouped_order->outlets[] = $outlet_item;
                                }
                            }
                        }
                        else {
                            $grouped_order[] = $single_order;
                        }
                    }
                    $order_data = $grouped_order;
                }
                //reorder data index
                $order_data_temp = array();
                foreach ($order_data as $data) $order_data_temp[] = $data;
                $order_data              = $order_data_temp;
                $ret_data['sales_count'] = $sales_count;
                $ret_data['data']        = $order_data;
                $ret_data['status']      = true;
                $ret_data['message']     = "success";
            }
        }
        echo json_encode($ret_data);
    }

    public function customer()
    {
        $this->data['title']    = $this->lang->line('customer_report_title');
        $this->data['subtitle'] = $this->lang->line('customer_report_title');
        $this->load->model('store_model');

        $this->data['all_store']  = $this->store_model->get_all_store();
        $this->data['all_outlet'] = $this->store_model->get_all_outlet();
        $table_data               = array();
        foreach ($this->data['all_store'] as $single_store) {
            $floors = $this->store_model->get_floor_by_store($single_store->id);
            foreach ($floors as $floor) {
                $tables = $this->store_model->get_table_by_floor($single_store->id, $floor->id);

                foreach ($tables as $table) {
                    $table->store_name = $single_store->store_name;
                    // $table->table_name = "test dulu";
                    $table_data[] = $table;
                }
            }
        }
        $this->data['all_table'] = $table_data;
        // echo '<pre>';
        // print_r($this->data['all_outlet']);
        // echo '</pre>';
        $this->data['content'] .= $this->load->view('admin/report-customer', $this->data, true);
        $this->render('report');
    }

    public function get_customer_data()
    {
        $start_date = $this->input->post('start_date');
        $end_date   = $this->input->post('end_date');
        $month      = $this->input->post('month');
        $year       = $this->input->post('year');
        $store      = $this->input->post('store');
        $outlet     = $this->input->post('outlet');
        $table      = $this->input->post('table');
        // print_r($table);
        $this->load->model('order_model');
        $this->load->model('store_model');

        $ret_data            = array();
        $ret_data['data']    = null;
        $ret_data['status']  = false;
        $ret_data['message'] = "";
        $is_year             = false;

        if (empty($start_date) && empty($end_date) && empty($month) && empty($year) && empty($store) && empty($outlet) && empty($table)) {
            $ret_data['message'] = "Harap isi minimal 1 filter";
        }
        else {
            //fetching order data only
            if (! empty($month) || ! empty($year)) {
                if (! empty($month)) {
                    //monthly
                    $date_format_group = "Y-m-d";
                    if (empty($year)) {
                        $year       = date("Y");
                        $order_data = $this->order_model->get_order_by_date_range("2013-" . $month . "-01 00:00:00", $year . "-" . $month . "-31 23:59:59");
                    }
                    else
                        $order_data = $this->order_model->get_order_by_date_range($year . "-" . $month . "-01 00:00:00", $year . "-" . $month . "-31 23:59:59");
                }
                else {
                    //yearly
                    $date_format_group = "F";
                    $order_data        = $this->order_model->get_order_by_date_range($year . "-01-01 00:00:00", $year . "-12-31 23:59:59");

                }
            }
            else if (! empty($start_date) || ! empty($end_date)) {
                if ($end_date > $start_date) {
                    //get berdasarkan start & end date
                    $order_data = $this->order_model->get_order_by_date_range($start_date . " 00:00:00", $end_date . " 23:59:59");
                }
                else if ($end_date == $start_date) {
                    //get berdasarkan start & end date
                    $order_data = $this->order_model->get_order_by_date_range($start_date . " 00:00:00", $end_date . " 23:59:59");
                }
                else {
                    $ret_data['message'] = "Waktu akhir harus lebih besar dari waktu awal";
                    $order_data          = '';
                }
            }

            if ($order_data != '') {
                $sales_count = 0;
                $order_start = array();
                $order_ends  = array();
                //fetching all data
                foreach ($order_data as $key => $single_order) {
                    $history_data = $this->order_model->get_order_history_by_order($single_order->id);

                    if (empty($history_data)) {
                        unset($order_data[$key]);
                        continue;
                    }

                    $single_order->history_data = json_decode($history_data->history);

                    $item_count                = 0;
                    $guest_count               = 0;
                    $stores                    = array();
                    $outlets                   = array();
                    $menu_price                = 0;
                    $menu_hpp                  = 0;
                    $side_dish_price           = 0;
                    $side_dish_hpp             = 0;
                    $additional_charge_total   = 0;
                    $tax_percentage_total      = 0;
                    $single_order->order_type  = $single_order->history_data->order_data[0]->order_type;
                    $single_order->guest_count = 0;
                    $table_new_name            = 'Takeaway';

                    foreach ($single_order->history_data->order_list as $ord_key => $single_order_list) {
                        //filtering by outlet/store
                        if (! empty($store)) {
                            if ($single_order_list->store_id != $store) {
                                continue;
                            }
                        }
                        if (! empty($outlet)) {
                            if ($single_order_list->outlet_id != $outlet) {
                                continue;
                            }
                        }

                        // print_r($table);
                        if ($table != '') {
                            if ($table != 'all') {
                                // print_r($single_order->history_data->order_data[0]->table_id);
                                if ($single_order->history_data->order_data[0]->table_id != $table) {
                                    unset($order_data[$key]);
                                    continue;
                                }
                            }
                        }

                        if (isset($single_order_list->side_dish_list)) {
                            foreach ($single_order_list->side_dish_list as $side_dish) {
                                $side_dish_hpp += $side_dish->side_dish_hpp * $side_dish->count;
                                $side_dish_price += $side_dish->side_dish_price * $side_dish->count;
                            }
                        }
                        $menu_price += $single_order_list->menu_price * $single_order_list->count;
                        $menu_hpp += $single_order_list->menu_hpp * $single_order_list->count;
                        if (! in_array($single_order_list->store_name, $stores)) {
                            $stores[] = $single_order_list->store_name;
                        }
                        if (! in_array($single_order_list->outlet_name, $outlets)) {
                            $outlets[] = $single_order_list->outlet_name;
                        }
                        //$item_count++;
                        $item_count = $item_count + $single_order_list->count;
                    }

                    //kalau failed di filtering outlet/store, menu_pricenya  0 jangan ditampilkan
                    if (empty($menu_price)) {
                        unset($order_data[$key]);
                        continue;
                    }
                    $sales_count++;
                    foreach ($single_order->history_data->tax_price as $tax_price) {
                        $tax_percentage_total += $tax_price->tax_percentage;
                    }
                    foreach ($single_order->history_data->extra_charge_price as $extra_charge) {
                        $additional_charge_total += $extra_charge->charge_value;
                    }

                    if ($single_order->history_data->order_data[0]->table_id != '0') {
                        $table_dt = $this->order_model->get_one('table', $single_order->history_data->order_data[0]->table_id);
                        if ($table_dt) {
                            $table_new_name = $table_dt->table_name;
                        }
                    }

                    $single_order->table_name      = $table_new_name;
                    $single_order->stores          = $stores;
                    $single_order->outlets         = $outlets;
                    $single_order->item_count      = $item_count;
                    $single_order->guest_count     = $guest_count;
                    $single_order->total_price     = ($menu_price + $side_dish_price) + (($menu_price + $side_dish_price) * $tax_percentage_total / 100) + $additional_charge_total;
                    $single_order->total_price_str = "Rp " . number_format($single_order->total_price, 0, ",", ".");


                    if ($single_order->history_data->order_data[0]->order_type == "Takeaway") $single_order->guest_count = 1;
                    else
                        $single_order->guest_count = $single_order->history_data->order_data[0]->customer_count;

                    $guest_count += $single_order->guest_count;

                    $single_order->order_start[] = $single_order->order_date;
                    $single_order->order_ends[]  = $single_order->order_end;
                }

                //grouping by month/date if necessary
                if (! empty($year) || ! empty($month)) {
                    $grouped_order = array();
                    foreach ($order_data as $single_order) {
                        $order_date               = new DateTime($single_order->order_date);
                        $order_date               = $order_date->format($date_format_group);
                        $single_order->order_date = $order_date;

                        $existing_grouped_order = false;

                        foreach ($grouped_order as $single_grouped_order) {
                            if ($single_grouped_order->order_date == $order_date) {
                                $existing_grouped_order = $single_grouped_order;
                                break;
                            }
                        }
                        // print_r($single_order);
                        if ($existing_grouped_order) {
                            $existing_grouped_order->subtotal_price += $single_order->subtotal_price;
                            $existing_grouped_order->total_price += $single_order->total_price;
                            $existing_grouped_order->tax_price += $single_order->tax_price;
                            $existing_grouped_order->item_count += $single_order->item_count;
                            $existing_grouped_order->guest_count += $single_order->guest_count;
                            $existing_grouped_order->total_price_str = "Rp " . number_format($existing_grouped_order->total_price, 0, ",", ".");
                            foreach ($single_order->stores as $store_item) {
                                if (! in_array($store_item, $existing_grouped_order->stores)) {
                                    $existing_grouped_order->stores[] = $store_item;
                                }
                            }
                            foreach ($single_order->outlets as $outlet_item) {
                                if (! in_array($outlet_item, $existing_grouped_order->outlets)) {
                                    $existing_grouped_order->outlets[] = $outlet_item;
                                }
                            }
                            foreach ($single_order->order_start as $order_start_item) {
                                $existing_grouped_order->order_start[] = $order_start_item;
                            }
                            foreach ($single_order->order_ends as $order_end_item) {
                                $existing_grouped_order->order_ends[] = $order_end_item;
                            }
                        }
                        else {
                            $grouped_order[] = $single_order;
                        }
                    }
                    $order_data = $grouped_order;
                }
                //reorder data index
                $order_data_temp = array();
                foreach ($order_data as $data) $order_data_temp[] = $data;
                $order_data              = $order_data_temp;
                $ret_data['sales_count'] = $sales_count;
                $ret_data['data']        = $order_data;
                // print_r($order_data);
                $ret_data['status']  = true;
                $ret_data['message'] = "success";
            }
        }
        echo json_encode($ret_data);
    }

    public function store()
    {
        $this->data['title']    = $this->lang->line('store_report_title');
        $this->data['subtitle'] = $this->lang->line('store_report_title');

        $this->load->model('store_model');

        $this->data['all_store']  = $this->store_model->get_all_store();
        $this->data['all_outlet'] = $this->store_model->get_all_outlet();


        $this->data['content'] .= $this->load->view('admin/report-store', $this->data, true);
        $this->render('report');
    }

    public function get_store_data()
    {
        $start_date = $this->input->post('start_date');
        $end_date   = $this->input->post('end_date');
        $month      = $this->input->post('month');
        $year       = $this->input->post('year');
        $store      = $this->input->post('store');
        $outlet     = $this->input->post('outlet');

        $this->load->model('order_model');
        $this->load->model('store_model');

        $ret_data            = array();
        $ret_data['data']    = null;
        $ret_data['status']  = false;
        $ret_data['message'] = "";

        if (empty($start_date) && empty($end_date) && empty($month) && empty($year) && empty($store) && empty($outlet)) {
            $ret_data['message'] = "Harap isi minimal 1 filter";
        }
        else {
            //fetching order data only
            if (! empty($month) || ! empty($year)) {
                if (! empty($month)) {
                    //monthly
                    $date_format_group = "Y-m-d";
                    if (empty($year)) {
                        $year       = date("Y");
                        $order_data = $this->order_model->get_order_by_date_range("2013-" . $month . "-01 00:00:00", $year . "-" . $month . "-31 23:59:59");
                    }
                    else
                        $order_data = $this->order_model->get_order_by_date_range($year . "-" . $month . "-01 00:00:00", $year . "-" . $month . "-31 23:59:59");
                }
                else {
                    //yearly
                    $date_format_group = "F";
                    $order_data        = $this->order_model->get_order_by_date_range($year . "-01-01 00:00:00", $year . "-12-31 23:59:59");

                }
            }
            else if (! empty($start_date) || ! empty($end_date)) {
                if ($end_date > $start_date) {
                    //get berdasarkan start & end date
                    $order_data = $this->order_model->get_order_by_date_range($start_date . " 00:00:00", $end_date . " 23:59:59");
                }
                else if ($end_date == $start_date) {
                    //get berdasarkan start & end date
                    $order_data = $this->order_model->get_order_by_date_range($start_date . " 00:00:00", $end_date . " 23:59:59");
                }
                else {
                    $ret_data['message'] = "Waktu akhir harus lebih besar dari waktu awal";
                    $order_data          = '';
                }
            }
            if ($order_data != '') {
                $sales_count = 0;

                //fetching all data
                foreach ($order_data as $key => $single_order) {
                    $history_data = $this->order_model->get_order_history_by_order($single_order->id);

                    if (empty($history_data)) {
                        unset($order_data[$key]);
                        continue;
                    }

                    $single_order->history_data = json_decode($history_data->history);

                    $item_count                = 0;
                    $guest_count               = 0;
                    $stores                    = array();
                    $outlets                   = array();
                    $menu_price                = 0;
                    $menu_hpp                  = 0;
                    $side_dish_price           = 0;
                    $side_dish_hpp             = 0;
                    $additional_charge_total   = 0;
                    $tax_percentage_total      = 0;
                    $single_order->order_type  = $single_order->history_data->order_data[0]->order_type;
                    $single_order->guest_count = 0;
                    if ($single_order->order_type == 'Takeaway') {
                        $single_order->guest_count = 1;
                    }
                    else
                        $single_order->guest_count = $single_order->history_data->order_data[0]->customer_count;

                    $guest_count += $single_order->guest_count;
                    foreach ($single_order->history_data->order_list as $ord_key => $single_order_list) {
                        //filtering by outlet/store
                        if (! empty($store)) {
                            if ($single_order_list->store_id != $store) {
                                continue;
                            }
                        }
                        if (! empty($outlet)) {
                            if ($single_order_list->outlet_id != $outlet) {
                                continue;
                            }
                        }

                        if (isset($single_order_list->side_dish_list)) {
                            foreach ($single_order_list->side_dish_list as $side_dish) {
                                $side_dish_hpp += $side_dish->side_dish_hpp * $side_dish->count;
                                $side_dish_price += $side_dish->side_dish_price * $side_dish->count;
                            }
                        }
                        $menu_price += $single_order_list->menu_price * $single_order_list->count;
                        $menu_hpp += $single_order_list->menu_hpp * $single_order_list->count;

                        if (! in_array($single_order_list->store_name, $stores)) {
                            $stores[] = $single_order_list->store_name;
                        }
                        if (! in_array($single_order_list->outlet_name, $outlets)) {
                            $outlets[] = $single_order_list->outlet_name;
                        }
                        //$item_count++;
                        $item_count = $item_count + $single_order_list->count;
                    }
                    //kalau failed di filtering outlet/store, menu_pricenya  0 jangan ditampilkan
                    if (empty($menu_price)) {
                        unset($order_data[$key]);
                        continue;
                    }
                    $sales_count++;
                    foreach ($single_order->history_data->tax_price as $tax_price) {
                        $tax_percentage_total += $tax_price->tax_percentage;
                    }
                    foreach ($single_order->history_data->extra_charge_price as $extra_charge) {
                        $additional_charge_total += $extra_charge->charge_value;
                    }

                    $single_order->stores          = $stores;
                    $single_order->side_dish_hpp   = $side_dish_hpp;
                    $single_order->menu_price      = $menu_price;
                    $single_order->menu_hpp        = $menu_hpp;
                    $single_order->item_count      = $item_count;
                    $single_order->guest_count     = $guest_count;
                    $single_order->total_price     = ($menu_price + $side_dish_price) + (($menu_price + $side_dish_price) * $tax_percentage_total / 100) + $additional_charge_total;
                    $single_order->total_price_str = "Rp " . number_format($single_order->total_price, 0, ",", ".");


                }

                //grouping by month/date if necessary
                if (! empty($year) || ! empty($month)) {
                    $grouped_order = array();
                    foreach ($order_data as $single_order) {
                        // print_r($single_order);
                        // echo '///';
                        $order_date               = new DateTime($single_order->order_date);
                        $order_date               = $order_date->format($date_format_group);
                        $single_order->order_date = $order_date;

                        $existing_grouped_order = false;

                        foreach ($grouped_order as $single_grouped_order) {
                            if ($single_grouped_order->order_date == $order_date) {
                                $existing_grouped_order = $single_grouped_order;
                                break;
                            }
                        }
                        if ($existing_grouped_order) {
                            $existing_grouped_order->subtotal_price += $single_order->subtotal_price;
                            $existing_grouped_order->total_price += $single_order->total_price;
                            $existing_grouped_order->tax_price += $single_order->tax_price;
                            $existing_grouped_order->item_count += $single_order->item_count;
                            $existing_grouped_order->guest_count += $single_order->guest_count;
                            $existing_grouped_order->menu_hpp += $single_order->menu_hpp;
                            $existing_grouped_order->side_dish_hpp += $single_order->side_dish_hpp;
                            $existing_grouped_order->total_price_str = "Rp " . number_format($existing_grouped_order->total_price, 0, ",", ".");
                            foreach ($single_order->stores as $store_item) {
                                if (! in_array($store_item, $existing_grouped_order->stores)) {
                                    $existing_grouped_order->stores[] = $store_item;
                                }
                            }

                        }
                        else {
                            $grouped_order[] = $single_order;
                        }
                    }
                    $order_data = $grouped_order;
                }
                //reorder data index
                $order_data_temp = array();
                foreach ($order_data as $data) $order_data_temp[] = $data;
                $order_data              = $order_data_temp;
                $ret_data['sales_count'] = $sales_count;
                $ret_data['data']        = $order_data;
                // print_r($order_data);
                $ret_data['status']  = true;
                $ret_data['message'] = "success";
            }
        }
        echo json_encode($ret_data);
    }

    public function staff()
    {
        $this->data['title']    = $this->lang->line('staff_report_title');
        $this->data['subtitle'] = $this->lang->line('staff_report_title');

        $this->load->model('store_model');

        $this->data['all_store']  = $this->store_model->get_all_store();
        $this->data['all_outlet'] = $this->store_model->get_all_outlet();
        // $this->data['all_staff'] = $this->ion_auth->users(array(3,4,5))->result();
        $this->data['all_staff'] = $this->ion_auth->users(array(3, 5))->result();

        $this->data['content'] .= $this->load->view('admin/report-staff', $this->data, true);
        $this->render('report');
    }

    public function get_staff_data()
    {
        $start_date = $this->input->post('start_date');
        $end_date   = $this->input->post('end_date');
        $month      = $this->input->post('month');
        $year       = $this->input->post('year');
        $store      = $this->input->post('store');
        $outlet     = $this->input->post('outlet');
        $staff      = $this->input->post('staff');
        $role       = $this->input->post('role');
        //$role = 3;

        $this->load->model('order_model');
        $this->load->model('store_model');

        $ret_data            = array();
        $ret_data['data']    = null;
        $ret_data['status']  = false;
        $ret_data['message'] = "";

        if (empty($start_date) && empty($end_date) && empty($month) && empty($year) && empty($store) && empty($outlet) && empty($staff) && empty($role)) {
            $ret_data['message'] = "Harap isi minimal 1 filter";
        }
        else {
            //fetching order data only
            if (! empty($month) || ! empty($year)) {
                if (! empty($month)) {
                    //monthly
                    $date_format_group = "Y-m-d";
                    if (empty($year)) {
                        $year       = date("Y");
                        $order_data = $this->order_model->get_order_by_date_range("2013-" . $month . "-01 00:00:00", $year . "-" . $month . "-31 23:59:59");
                    }
                    else
                        $order_data = $this->order_model->get_order_by_date_range($year . "-" . $month . "-01 00:00:00", $year . "-" . $month . "-31 23:59:59");
                }
                else {
                    //yearly
                    $date_format_group = "F";
                    $order_data        = $this->order_model->get_order_by_date_range($year . "-01-01 00:00:00", $year . "-12-31 23:59:59");

                }
            }
            else if (! empty($start_date) || ! empty($end_date)) {
                if ($end_date > $start_date) {
                    //get berdasarkan start & end date
                    $order_data = $this->order_model->get_order_by_date_range($start_date . " 00:00:00", $end_date . " 23:59:59");
                }
                else if ($end_date == $start_date) {
                    //get berdasarkan start & end date
                    $order_data = $this->order_model->get_order_by_date_range($start_date . " 00:00:00", $end_date . " 23:59:59");

                }
                else {
                    $ret_data['message'] = "Waktu akhir harus lebih besar dari waktu awal";
                    $order_data          = '';
                }
            }

            if ($order_data != '') {
                $sales_count = 0;

                //fetching all data
                foreach ($order_data as $key => $single_order) {
                    $history_data = $this->order_model->get_order_history_by_order($single_order->id);
                    if (empty($history_data)) {
                        unset($order_data[$key]);
                        continue;
                    }

                    $single_order->history_data = json_decode($history_data->history);

                    $item_count               = 0;
                    $guest_count              = 0;
                    $single_order->order_type = $single_order->history_data->order_data[0]->order_type;

                    $waiter_data = 0;
                    if ($single_order->history_data->order_data[0]->waiter_id != 0) {
                        $waiter_data       = $this->ion_auth->user($single_order->history_data->order_data[0]->waiter_id)->row();
                        $waiter_data->role = "Waiter";
                    }

                    $cashier_data = 0;
                    if ($single_order->history_data->order_data[0]->cashier_id != 0) {
                        $cashier_data       = $this->ion_auth->user($single_order->history_data->order_data[0]->cashier_id)->row();
                        $cashier_data->role = "Cashier";
                    }


                    if ($single_order->history_data->order_data[0]->waiter_id == $single_order->history_data->order_data[0]->cashier_id) {
                        if ($this->ion_auth->in_group('waiter', $single_order->history_data->order_data[0]->waiter_id)) {
                            //true user role is waiter
                            $cashier_data = 0;
                        }
                        else {
                            //true user role is cashier
                            $waiter_data = 0;
                        }
                    }
                    if (! empty($staff)) {
                        //TODO role jgn di hardcode
                        //TODO filter role kitchen
                        if ($waiter_data) {//kalau filter cashier, waiter dihapus
                            if ($waiter_data->id != $staff) {//kalau filter cashier, waiter dihapus
                                $waiter_data = null;
                            }
                        }
                        if ($cashier_data) {//kalau filter waiter, cashier dihapus
                            if ($cashier_data->id != $staff) {//kalau filter waiter, cashier dihapus
                                $cashier_data = null;
                            }
                        }
                        if ($waiter_data == null && $cashier_data == null) unset($order_data[$key]);
                    }
                    if (! empty($role)) {
                        //TODO role jgn di hardcode
                        //TODO filter role kitchen
                        if (3 == $role) {//kalau filter cashier, waiter dihapus
                            $waiter_data = null;
                        }
                        else if (5 == $role) {//kalau filter waiter, cashier dihapus
                            $cashier_data = null;
                        }
                        if ($waiter_data == null && $cashier_data == null) unset($order_data[$key]);
                    }
                    $valid_store = true;
                    foreach ($single_order->history_data->order_list as $ord_key => $single_order_list) {
                        //filtering by outlet/store
                        if (! empty($store)) {
                            if ($single_order_list->store_id != $store) {
                                $valid_store = false;
                            }
                        }

                        $item_count += $single_order_list->count;
                    }

                    //kalau failed di filtering outlet/store,jangan ditampilkan
                    if (! $valid_store) {
                        unset($order_data[$key]);
                        continue;
                    }
                    $sales_count++;


                    if ($single_order->history_data->order_data[0]->order_type == "Takeaway") $single_order->guest_count = 1;
                    else
                        $single_order->guest_count = $single_order->history_data->order_data[0]->customer_count;

                    // $guest_count += $single_order->guest_count;

                    $single_order->waiter_data  = $waiter_data;
                    $single_order->cashier_data = $cashier_data;
                    $single_order->item_count   = $item_count;
                    // $single_order->guest_count     = $guest_count;
                }

                //grouping by month/date if necessary
                if (! empty($year) || ! empty($month)) {
                    $grouped_order = array();
                    foreach ($order_data as $single_order) {
                        $order_date               = new DateTime($single_order->order_date);
                        $order_date               = $order_date->format($date_format_group);
                        $single_order->order_date = $order_date;

                        $existing_grouped_order = false;

                        foreach ($grouped_order as $single_grouped_order) {
                            if ($single_grouped_order->order_date == $order_date && $single_grouped_order->cashier_id == $single_order->cashier_id) {
                                $existing_grouped_order = $single_grouped_order;
                                break;
                            }
                        }
                        if ($existing_grouped_order) {
                            $existing_grouped_order->item_count += $single_order->item_count;
                            $existing_grouped_order->guest_count += $single_order->guest_count;

                        }
                        else {
                            $grouped_order[] = $single_order;
                        }
                    }
                    $order_data = $grouped_order;
                }
                //reorder data index
                $order_data_temp = array();
                foreach ($order_data as $data) $order_data_temp[] = $data;
                $order_data       = $order_data_temp;
                $ret_data['data'] = $order_data;
                // print_r($order_data);
                $ret_data['status']  = true;
                $ret_data['message'] = "success";
            }
        }
        echo json_encode($ret_data);
    }

    public function ingredient()
    {
        $this->data['title']    = 'Penggunaan Bahan';
        $this->data['subtitle'] = 'Penggunaan Bahan';

        $this->load->model('store_model');

        $this->data['all_store']  = $this->store_model->get_all_store();
        $this->data['all_outlet'] = $this->store_model->get_all_outlet();
        $this->data['all_inventory']  = $this->store_model->get('inventory')->result();


        $this->data['content'] .= $this->load->view('admin/report-ingredient', $this->data, true);
        $this->render('report');
    }

    public function get_ingredient_data()
     {
        $start_date = $this->input->post('start_date');
        $end_date   = $this->input->post('end_date');
        $month      = $this->input->post('month');
        $year       = $this->input->post('year');
        $outlet     = $this->input->post('outlet');
        $inventory     = $this->input->post('inventory');

        $this->load->model('order_model');
        $this->load->model('store_model');
        $this->load->model('inventory_model');

        $ret_data            = array();
        $ret_data['data']    = null;
        $ret_data['status']  = false;
        $ret_data['message'] = "";
        $grouped_history = array();

        if (empty($start_date) && empty($end_date) && empty($month) && empty($year) && empty($inventory) && empty($outlet) 
            ) {
            $ret_data['message'] = "Harap lengkapi filter";
        }else
        {
            $where_outlet ='';
            if(!empty($outlet)){
                $where_outlet = $outlet;
            }

            $where_inv ='';
            if(!empty($inventory)){
                $where_inv = $inventory;
            }

            $data_trans = '';
             if (! empty($month) || ! empty($year)) {
                if (! empty($month)) {
                    //monthly
                    $date_format_group = "Y-m-d";
                    if (empty($year)) {
                        $year       = date("Y");
                        $data_trans = $this->inventory_model->get_stock_transaction_by_date_range($year ."-" . $month . "-01 00:00:00", $year . "-" . $month . "-31 23:59:59",$where_outlet,$where_inv);
                    }
                    else
                        $data_trans = $this->inventory_model->get_stock_transaction_by_date_range($year . "-" . $month . "-01 00:00:00", $year . "-" . $month . "-31 23:59:59",$where_outlet,$where_inv);
                }
                else {
                    //yearly
                    $date_format_group = "F";
                    $data_trans        = $this->inventory_model->get_stock_transaction_by_date_range($year . "-01-01 00:00:00", $year . "-12-31 23:59:59",$where_outlet,$where_inv);

                }
            }
            else if (! empty($start_date) || ! empty($end_date)) {
                if ($end_date > $start_date) {
                    //get berdasarkan start & end date
                    $data_trans = $this->inventory_model->get_stock_transaction_by_date_range($start_date . " 00:00:00", $end_date . " 23:59:59",$where_outlet,$where_inv);
                }
                else if ($end_date == $start_date) {
                    //get berdasarkan start & end date
                    $data_trans = $this->inventory_model->get_stock_transaction_by_date_range($start_date . " 00:00:00", $end_date . " 23:59:59",$where_outlet,$where_inv);

                }
                else {
                    $ret_data['message'] = "Waktu akhir harus lebih besar dari waktu awal";
                    $data_trans          = '';
                }
            }

            if(!empty($data_trans))
            {
                foreach ($data_trans as $key => $row) {
                  $row->total_stock=0;
                  $row->total_used = 0;

                  $inv_trans = $this->inventory_model->get_inventory_stock_transaction($row->id);
                  if(!empty($inv_trans)){
                    $row->total_used = $inv_trans->total_used;
                    }


                    $inventory_stock = $this->inventory_model->get_inventory_history_stock($row->outlet_id,$row->inventory_id,$row->date);
                  if(!empty($inventory_stock) ){
                    $row->total_stock = $inventory_stock->stock + $row->stock;
                    // $row->total_stock = $inventory_stock->stock;
                    
                  }

                  $row->total_stock_opname = 0;
                  $row->delta_stock = 0;
                  $stock_opname = $this->inventory_model->get_inventory_opname_stock($row->outlet_id,$row->inventory_id,$row->date);
                  if(!empty($stock_opname)){
                    // $row->total_stock = $inventory_stock->stock + $row->stock - $row->total_used;
                    $row->total_stock_opname = $stock_opname->stock;                    
                    $row->delta_stock = $row->total_stock_opname - $row->total_stock;
                  }                  

                  
                }

               
            }
                $ret_data['data'] = $data_trans;
                $ret_data['status']  = true;
                $ret_data['message'] = "success";
        }
       

        echo json_encode($ret_data);
    }

     public function menu()
    {
        $this->data['title']    = "Menu";
        $this->data['subtitle'] = "Menu";

        $this->load->model('store_model');

        $this->data['all_store']  = $this->store_model->get_all_store();
        $this->data['all_outlet'] = $this->store_model->get_all_outlet();
        $this->data['content'] .= $this->load->view('admin/report-menu', $this->data, true);
        $this->render('report');
    }

    public function get_menu_data()
    {
        $start_date = $this->input->post('start_date');
        $end_date   = $this->input->post('end_date');
        $month      = $this->input->post('month');
        $year       = $this->input->post('year');
        $store      = $this->input->post('store');
        $outlet     = $this->input->post('outlet');

        $this->load->model('order_model');
        $this->load->model('store_model');

        $ret_data            = array();
        $ret_data['status']  = false;
        $ret_data['message'] = '';
        $stores = array();
        $outlets = array();
        $total_days_of_diff  = 0;
        $total_year_of_diff  = 0;
          $result = array();

        if (empty($start_date) && empty($end_date) && empty($month) && empty($year) && empty($store)) {
            $ret_data['message'] = "Harap isi minimal 1 filter";

        }
        else {
            if (! empty($month) || ! empty($year)) {
                if (! empty($month)) {
                    //monthly
                    $date_format_group = "Week";
                    if (empty($year)) {
                        $year       = date("Y");
                        $order_data = $this->order_model->get_order_by_date_range($year . "-" . $month . "-01 00:00:00", $year . "-" . $month . "-31 23:59:59");
                    }
                    else
                        $order_data = $this->order_model->get_order_by_date_range($year . "-" . $month . "-01 00:00:00", $year . "-" . $month . "-31 23:59:59");
                }
                else {
                    //yearly
                    $date_format_group = "Month";
                    $order_data        = $this->order_model->get_order_by_date_range($year . "-01-01 00:00:00", $year . "-12-31 23:59:59");

                }
            }
            else if (! empty($start_date) && ! empty($end_date)) {
                //get berdasarkan start & end date
                $order_data = $this->order_model->get_order_by_date_range($start_date . " 00:00:00", $end_date . " 23:59:59");

                $diff_start = new DateTime($start_date);
                $diff_end   = new DateTime($end_date);
                $interval   = $diff_end->diff($diff_start);

                $total_year_of_diff = $interval->format('%y');
                $total_days_of_diff = $interval->format('%a');
            }
            else {
                $order_data = array();
            }


            //fetching all data
            $res_data             = array();
            $outlet_customer_data = array();
            foreach ($order_data as $key => $single_order) {
                $history_data = $this->order_model->get_order_history_by_order($single_order->id);
                if (! empty($history_data)) {
                    $history = json_decode($history_data->history);

                    $to_time   = strtotime($history->order_data[0]->order_date);
                    $from_time = strtotime($history->order_data[0]->order_end);
                    $minutes   = round(abs($to_time - $from_time) / 60, 0);

                    $get_year       = date("Y", $to_time);
                    $get_month      = date("F", $to_time);
                    $get_month_year = date("m-Y", $to_time);
                    $get_date       = date("d-m-Y", $to_time);
                    $get_hours      = (string)date("H", $to_time);


                    $tax_percentage_total    = 0;
                    $additional_charge_total = 0;

                    if (isset($history->tax_price)) {
                        foreach ($history->tax_price as $tax_price) {
                            $tax_percentage_total += $tax_price->tax_percentage;
                        }
                    }

                    $counter_menu = 0;
                    $i = 0;
                    $order_list = $history->order_list;
                    $total_order = sizeof($order_list);
                    while ($i < sizeof($order_list)) {

                        if (! empty($store) && $store != 0) {
                            if ($store != $order_list[$i]->store_id) {
                                 $i +=1;
                                continue;
                            }
                        }

                        if (! empty($outlet) && $outlet != 0) {
                            if ($outlet != $order_list[$i]->outlet_id) {
                                 $i +=1 ;
                                continue;
                                
                            }
                        }

                        
                        if(!isset($res_data[$order_list[$i]->menu_id]))
                        {
                            $side_dish_count_item = 0;
                            $side_dish_hpp        = 0;
                            $side_dish_price      = 0;
                            $total_count_item = $order_list[$i]->count;
                            $menu_price       = round($order_list[$i]->menu_price * $order_list[$i]->count, 0, PHP_ROUND_HALF_UP);
                            $menu_hpp         = round($order_list[$i]->menu_hpp * $order_list[$i]->count, 0, PHP_ROUND_HALF_UP);
                            // $gross_revenue    = round((($menu_price + $side_dish_price) + (($menu_price + $side_dish_price) * $tax_percentage_total / 100) + $additional_charge_total), 0, PHP_ROUND_HALF_UP);
                            $gross_revenue    = round(($menu_price  + ($menu_price  * $tax_percentage_total / 100) + $additional_charge_total), 0, PHP_ROUND_HALF_UP);
                            // $gross_expenses   = $menu_hpp + $side_dish_hpp;
                            $gross_expenses   = $menu_hpp;
                            $gross_profit     = $gross_revenue - $gross_expenses;

                            $res_data[$order_list[$i]->menu_id]['menu_id'] = $order_list[$i]->menu_id;
                            $res_data[$order_list[$i]->menu_id]['menu_name'] = $order_list[$i]->menu_name;    
                            $res_data[$order_list[$i]->menu_id]['menu_price'] = $order_list[$i]->menu_price;    
                            $res_data[$order_list[$i]->menu_id]['menu_count'] = $order_list[$i]->count;
                            $res_data[$order_list[$i]->menu_id]['store_id'] = $order_list[$i]->store_id;
                            $res_data[$order_list[$i]->menu_id]['store_name'] = $order_list[$i]->store_name;
                            $res_data[$order_list[$i]->menu_id]['outlet_id'] = $order_list[$i]->outlet_id;
                            $res_data[$order_list[$i]->menu_id]['outlet_name'] = $order_list[$i]->outlet_name;
                            $res_data[$order_list[$i]->menu_id]['get_month'] = $get_month;
                            $res_data[$order_list[$i]->menu_id]['get_month_year'] = $get_month_year;
                            $res_data[$order_list[$i]->menu_id]['get_year'] = $get_year;
                            $res_data[$order_list[$i]->menu_id]['get_date'] = $get_date;
                            $res_data[$order_list[$i]->menu_id]['get_hours'] = $get_hours;
                            $res_data[$order_list[$i]->menu_id]['order_date'] = $history->order_data[0]->order_date;
                            $res_data[$order_list[$i]->menu_id]['order_end'] = $history->order_data[0]->order_end;
                            $res_data[$order_list[$i]->menu_id]['receipt_id'] =    $history->order_data[0]->receipt_id;
                            $res_data[$order_list[$i]->menu_id]['item_count'] = 1;
                            $res_data[$order_list[$i]->menu_id]['gross_profit'] = $gross_profit*$order_list[$i]->count;
                            $res_data[$order_list[$i]->menu_id]['gross_profit_item'] = $gross_profit;
                            if (! in_array($order_list[$i]->store_id, $stores)) {
                                $stores[] = $order_list[$i]->store_id;
                            }
                            if (! in_array($order_list[$i]->outlet_id, $outlets)) {
                                $outlets[] = $order_list[$i]->outlet_id;
                            }

                            if (isset($outlet_customer_data[$history->order_data[0]->receipt_id][$order_list[$i]->outlet_id])) {
                                $outlet_customer_data[$history->order_data[0]->receipt_id][$order_list[$i]->outlet_id] += 1;
                            }
                            else {
                                $outlet_customer_data[$history->order_data[0]->receipt_id][$order_list[$i]->outlet_id] = 1;
                            }
                            

                        }else{

                            $side_dish_count_item = 0;
                            $side_dish_hpp        = 0;
                            $side_dish_price      = 0;

                            $menu_price       = round($order_list[$i]->menu_price * $order_list[$i]->count, 0, PHP_ROUND_HALF_UP);
                            $menu_hpp         = round($order_list[$i]->menu_hpp * $order_list[$i]->count, 0, PHP_ROUND_HALF_UP);
                            // $gross_revenue    = round((($menu_price + $side_dish_price) + (($menu_price + $side_dish_price) * $tax_percentage_total / 100) + $additional_charge_total), 0, PHP_ROUND_HALF_UP);
                            $gross_revenue    = round(($menu_price  + ($menu_price  * $tax_percentage_total / 100) + $additional_charge_total), 0, PHP_ROUND_HALF_UP);
                            // $gross_expenses   = $menu_hpp + $side_dish_hpp;
                            $gross_expenses   = $menu_hpp;
                            $gross_profit     = $gross_revenue - $gross_expenses;

                            $res_data[$order_list[$i]->menu_id]['gross_profit'] += $gross_profit;
                            $res_data[$order_list[$i]->menu_id]['menu_count'] += $order_list[$i]->count;                          


                        }
                            $res_data[$order_list[$i]->menu_id]['stores'] = $stores;
                            $res_data[$order_list[$i]->menu_id]['outlets'] = $outlets;
                        $i++;  


                    }      


                }
            }

             if ($res_data) {
                 foreach ($res_data as $key => $row) {
                    $result[] = $row;
                }
                // sorting by Gross Profit
                // usort($hours_array, make_compare( ['gross_profit', SORT_DESC], ['menu_count', SORT_DESC]));
              
            }
            $ret_data['status']     = true;
            $ret_data['data']       = $result;
        }
        echo json_encode($ret_data);
    }

      public function stock_opname()
    {
        $this->data['title']    = 'opname stok';
        $this->data['subtitle'] = 'opname stok';

        $this->load->model('store_model');

        $this->data['all_store']  = $this->store_model->get_all_store();
        $this->data['all_outlet'] = $this->store_model->get_all_outlet();


        $this->data['content'] .= $this->load->view('admin/report-stock-opname', $this->data, true);
        $this->render('report');
    }

    public function get_stock_opname_data()
     {
        $start_date = $this->input->post('start_date');
        $end_date   = $this->input->post('end_date');
        $month      = $this->input->post('month');
        $year       = $this->input->post('year');
        $outlet     = $this->input->post('outlet');

        $this->load->model('order_model');
        $this->load->model('store_model');
        $this->load->model('inventory_model');

        $ret_data            = array();
        $ret_data['data']    = null;
        $ret_data['status']  = false;
        $ret_data['message'] = "";
        $grouped_history = array();

        if (empty($start_date) && empty($end_date) && empty($month) && empty($year) && empty($store) && empty($outlet) && empty($staff) && empty($role)) {
            $ret_data['message'] = "Harap isi minimal 1 filter";
        }else
        {
            $where_outlet ='';
            if(!empty($outlet)){
                $where_outlet = $outlet;
            }
            $data_trans = '';
             if (! empty($month) || ! empty($year)) {
                if (! empty($month)) {
                    //monthly
                    $date_format_group = "Y-m-d";
                    if (empty($year)) {
                        $year       = date("Y");
                        $data_trans = $this->inventory_model->get_stock_transaction_by_date_range("2013-" . $month . "-01 00:00:00", $year . "-" . $month . "-31 23:59:59",$where_outlet);
                    }
                    else
                        $data_trans = $this->inventory_model->get_stock_transaction_by_date_range($year . "-" . $month . "-01 00:00:00", $year . "-" . $month . "-31 23:59:59",$where_outlet);
                }
                else {
                    //yearly
                    $date_format_group = "F";
                    $data_trans        = $this->inventory_model->get_stock_transaction_by_date_range($year . "-01-01 00:00:00", $year . "-12-31 23:59:59",$where_outlet);

                }
            }
            else if (! empty($start_date) || ! empty($end_date)) {
                if ($end_date > $start_date) {
                    //get berdasarkan start & end date
                    $data_trans = $this->inventory_model->get_stock_transaction_by_date_range($start_date . " 00:00:00", $end_date . " 23:59:59",$where_outlet);
                }
                else if ($end_date == $start_date) {
                    //get berdasarkan start & end date
                    $data_trans = $this->inventory_model->get_stock_transaction_by_date_range($start_date . " 00:00:00", $end_date . " 23:59:59",$where_outlet);

                }
                else {
                    $ret_data['message'] = "Waktu akhir harus lebih besar dari waktu awal";
                    $data_trans          = '';
                }
            }

            if(!empty($data_trans))
            {
                foreach ($data_trans as $key => $row) {
                  $row->total_stock=0;
                  $row->total_used = 0;
                  $inv_trans = $this->inventory_model->get_inventory_stock_transaction($row->id);
                  if(!empty($inv_trans)){
                    $row->total_used = $inv_trans->total_used;
                }   
                  $inventory_stock = $this->inventory_model->get_inventory_opname_stock($row->outlet_id,$row->inventory_id,$row->date);
                  if(!empty($inventory_stock)){
                    // $row->total_stock = $inventory_stock->stock + $row->stock - $row->total_used;
                    $row->total_stock = $inventory_stock->stock;
                    
                  } 
                  
                }

               
            }
                $ret_data['data'] = $data_trans;
                $ret_data['status']  = true;
                $ret_data['message'] = "success";
        }
       

        echo json_encode($ret_data);
    }

    public function export_report_to_pdf()
    {
        $this->load->helper(array('dompdf', 'file'));
        // page info here, db calls, etc.
        $html   = $this->input->post('html_string');
        $report = $this->input->post('report');
        // pdf_create($html, 'filename');
        // or
        $date     = new DateTime();
        $filename = 'assets/report/' . $report . '/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis') . '.pdf';
        $data     = pdf_create($html, '', false);
        write_file($filename, $data);
        //if you want to write it to disk and/or send it as an attachment

        echo json_encode($filename);
    }

    public function export_report_to_xls()
    {
        $data['html']   = $this->input->post('html_string');
        $report = $this->input->post('report');

        $this->load->library('excel');

        // Load the table view into a variable
        $html = $this->load->view('table_view', $data,true);
        // Put the html into a temporary file
        $tmpfile = time().'.html';
        file_put_contents($tmpfile, $html);


        $date     = new DateTime();
        $filename = 'assets/report/' . $report . '/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis') . '.xls';
        $inputFileType = 'HTML';
        $inputFileName = $tmpfile;
        $outputFileType = 'Excel5';
        $outputFileName = $filename;

        $objPHPExcelReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objPHPExcelReader->load($inputFileName);

        /** Borders for all data */
        $objPHPExcel->getActiveSheet()->getStyle(
            'A2:' .
            $objPHPExcel->getActiveSheet()->getHighestColumn() .
            $objPHPExcel->getActiveSheet()->getHighestRow()
        )->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $objPHPExcel->getActiveSheet()->getStyle(
            'A2:' .
            $objPHPExcel->getActiveSheet()->getHighestColumn() .
            $objPHPExcel->getActiveSheet()->getHighestRow()
        )->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        $latestBLColumn = $objPHPExcel->getActiveSheet()->getHighestDataColumn();
        $latestBLRow = $objPHPExcel->getActiveSheet()->getHighestRow();
        for ($column = 'A'; $column != $latestBLColumn; $column++) {
            for($row = 1; $row != $latestBLRow; $row++) {
                $cellval = PHPExcel_Shared_String::SanitizeUTF8($objPHPExcel->getActiveSheet()->getCell($column . $row)->getValue());
                $objPHPExcel->getActiveSheet()->getCell($column . $row)->setValueExplicit($cellval, PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->getStyle($column . $row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            }
        }

        foreach (range('A', $objPHPExcel->getActiveSheet()->getHighestDataColumn()) as $col) {
            $objPHPExcel->getActiveSheet()
                ->getColumnDimension($col)
                ->setAutoSize(true);
        }

        $objPHPExcelWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,$outputFileType);
        $objPHPExcel = $objPHPExcelWriter->save($outputFileName);

        // Delete temporary file
        unlink($tmpfile);

        echo json_encode($filename);
    }


}