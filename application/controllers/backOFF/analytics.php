<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/17/2014
 * Time: 8:27 PM
 */
class Analytics extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper("order");
        $this->load->model("categories_model");
    }

    public function index()
    {
        redirect(SITE_ADMIN . '/analytics/sales');
    }

    public function sales()
    {
        $this->data['title']    = $this->lang->line('sale_analytics_title');
        $this->data['subtitle'] = $this->lang->line('sale_analytics_title');

        $this->load->model('store_model');

        // $this->data['all_store']  = $this->store_model->get_all_store();
        // $this->data['all_outlet'] = $this->store_model->get_all_outlet();
        // $this->data['all_store']=array("0"=>"Pilih Resto");
        $this->data['all_store']  = $this->store_model->get_store_dropdown();
        $this->data['all_store'][0]="All";
        $this->data['all_category'] = $this->categories_model->get_outlet_dropdown_report();
        $this->data['content'] .= $this->load->view('admin/analytics-sales', $this->data, true);
        $this->render('analytics');
    }

    public function get_sales_data()
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
        $ret_data['message'] = '<div style="border: 1px solid #999;text-align: center;padding: 10px;margin-top: 20px">No Data</div>';
        $total_days_of_diff  = 0;
        $total_year_of_diff  = 0;
        $from="";
        $to="";
        if (empty($start_date) && empty($end_date) && empty($month) && empty($year) && empty($store)) {

        }
        else {
            if (! empty($month) || ! empty($year)) {
                if (! empty($month)) {
                    //monthly
                    $date_format_group = "Week";
                    if (empty($year)) {
                        $year       = date("Y");
                        $order_data = $this->order_model->get_order_by_date_range($year . "-" . $month . "-01 00:00:00", $year . "-" . $month . "-31 23:59:59");
                        $from=$year . "-" . $month . "-01 00:00:00";
                        $to=$year . "-" . $month . "-31 23:59:59";
                    }
                    else{
                        $order_data = $this->order_model->get_order_by_date_range($year . "-" . $month . "-01 00:00:00", $year . "-" . $month . "-31 23:59:59");
                        $from=$year . "-" . $month . "-01 00:00:00";
                        $to=$year . "-" . $month . "-31 23:59:59";
                    }
                }
                else {
                    //yearly
                    $date_format_group = "Month";
                    $order_data        = $this->order_model->get_order_by_date_range($year . "-01-01 00:00:00", $year . "-12-31 23:59:59");
                    $from=$year . "-01-01 00:00:00";
                    $to=$year . "-12-31 23:59:59";

                }
            }
            else if (! empty($start_date) && ! empty($end_date)) {
                //get berdasarkan start & end date
                $order_data = $this->order_model->get_order_by_date_range($start_date . " 00:00:00", $end_date . " 23:59:59");
                $from=$start_date . " 00:00:00";
                $to=$end_date . " 23:59:59";

                $diff_start = new DateTime($start_date);
                $diff_end   = new DateTime($end_date);
                $interval   = $diff_end->diff($diff_start);

                $total_year_of_diff = $interval->format('%y');
                $total_days_of_diff = $interval->format('%a');
            }
            else {
                $order_data = array();
            }


                // echo "<pre>";
                // print_r($order_data);
                // exit;
            //fetching all data
            $res_data             = array();
            $outlet_customer_data = array();
            foreach ($order_data as $key => $single_order) {
                $history_data = $this->order_model->get_one("order",$single_order->id);
                if (! empty($history_data)) {
                    $history=$this->order_model->calculate_total_order_bill_report($single_order->id,$from,$to);
                    $to_time   = strtotime($history_data->start_order);
                    $from_time = strtotime($history_data->end_order);
                    $minutes   = round(abs($to_time - $from_time) / 60, 0);

                    $get_year       = date("Y", $to_time);
                    $get_month      = date("F", $to_time);
                    $get_month_year = date("m-Y", $to_time);
                    $get_date       = date("d-m-Y", $to_time);
                    $get_hours      = (string)date("H", $to_time);


                    $tax_percentage_total    = 0;
                    $additional_charge_total = 0;

                    if (isset($history['tax_price'])) {
                        foreach ($history['tax_price'] as $tax_price) {
                            $tax_percentage_total += $tax_price['tax_percentage'];
                        }
                    }

                    if (isset($history['extra_charge_price'])) {
                        foreach ($history['extra_charge_price'] as $extra_charge) {
                            $additional_charge_total += $extra_charge['charge_value'];
                        }
                    }
// echo " --- ".$single_order->id." : ".sizeof($history['order_list'])."<br>";
                    foreach ($history['order_list'] as $oKey => $single_list) {
                        $side_dish_count_item = 0;
                        $side_dish_hpp        = 0;
                        $side_dish_price      = 0;

                        if (! empty($store) && $store != 0) {
                            if ($store != $single_list->store_id) {
                                continue;
                            }
                        }

                        if (! empty($outlet) && $outlet != 0) {
                            if ($outlet != $single_list->outlet_id) {
                                continue;
                            }
                        }

                        if (isset($single_list->side_dish_list)) {
                            foreach ($single_list->side_dish_list as $side_dish) {
                                $side_dish_hpp += round($side_dish->origin_price * $side_dish->quantity, 0, PHP_ROUND_HALF_UP);
                                $side_dish_price += round($side_dish->price * $side_dish->quantity, 0, PHP_ROUND_HALF_UP);
                                $side_dish_count_item += $side_dish->quantity;
                            }
                        }

                        $total_count_item = $side_dish_count_item + $single_list->quantity;
                        // $total_count_item = $single_list->quantity;
                        $menu_price       = round($single_list->menu_price * $single_list->quantity, 0, PHP_ROUND_HALF_UP);
                        $menu_hpp         = round($single_list->menu_hpp * $single_list->quantity, 0, PHP_ROUND_HALF_UP);
                        $gross_revenue    = round((($menu_price + $side_dish_price) + (($menu_price + $side_dish_price) * $tax_percentage_total / 100) + $additional_charge_total), 0, PHP_ROUND_HALF_UP);
                        $gross_expenses   = $menu_hpp + $side_dish_hpp;
                        $gross_profit     = $gross_revenue - $gross_expenses;

                        $res_data[] = array('store_id' => $single_list->store_id,
                                            'order_id' => $single_list->order_id,
                                            'store_name' => $single_list->store_name,
                                            'outlet_id' => $single_list->outlet_id,
                                            'outlet_name' => $single_list->outlet_name,
                                            'customer_count' => $single_list->customer_count,
                                            'item_count' => $total_count_item,
                                            'gross_revenue' => $gross_revenue,
                                            'gross_expenses' => $gross_expenses,
                                            'gross_profit' => $gross_profit,
                                            'get_month' => $get_month,
                                            'get_month_year' => $get_month_year,
                                            'get_year' => $get_year,
                                            'get_date' => $get_date,
                                            'get_hours' => $get_hours,
                                            'order_date' => $history_data->start_order,
                                            'order_end' => $history_data->end_order,
                                            'receipt_id' => $single_list->receipt_number
                                          );

                        if (isset($outlet_customer_data[$single_list->receipt_number][$single_list->outlet_id])) {
                            $outlet_customer_data[$single_list->receipt_number][$single_list->outlet_id] += 1;
                        }
                        else {
                            $outlet_customer_data[$single_list->receipt_number][$single_list->outlet_id] = 1;
                        }

                    }
                }
            }

            $result       = array();
            $result_chart = array();
            // echo "<pre>";
            // print_r($res_data);
            // exit;
            if (count($res_data) > 0) {
                foreach ($res_data as $row) {

                    if ($row['customer_count'] == '0') {
                        $row['customer_count'] = 1;
                    }

                    $row['customer_count'] = $row['customer_count'] / $outlet_customer_data[$row['receipt_id']][$row['outlet_id']];

                    // Chart
                    if ((empty($month) && ! empty($year)) || $total_days_of_diff > 31) {
                        // yearly
                        if ($total_year_of_diff > 0) {
                            $result_chart[$row['get_year']]['get_year'] = $row['get_year'];
                            if (isset($result_chart[$row['get_year']]['customer_count'])) {
                                $result_chart[$row['get_year']]['customer_count'] += $row['customer_count'];
                            }
                            else {
                                $result_chart[$row['get_year']]['customer_count'] = $row['customer_count'];
                            }

                            if (isset($result_chart[$row['get_year']]['item_count'])) {
                                $result_chart[$row['get_year']]['item_count'] += $row['item_count'];
                            }
                            else {
                                $result_chart[$row['get_year']]['item_count'] = $row['item_count'];
                            }

                            if (isset($result_chart[$row['get_year']]['gross_revenue'])) {
                                $result_chart[$row['get_year']]['gross_revenue'] += $row['gross_revenue'];
                            }
                            else {
                                $result_chart[$row['get_year']]['gross_revenue'] = $row['gross_revenue'];
                            }

                            if (isset($result_chart[$row['get_year']]['gross_expenses'])) {
                                $result_chart[$row['get_year']]['gross_expenses'] += $row['gross_expenses'];
                            }
                            else {
                                $result_chart[$row['get_year']]['gross_expenses'] = $row['gross_expenses'];
                            }

                            if (isset($result_chart[$row['get_year']]['gross_profit'])) {
                                $result_chart[$row['get_year']]['gross_profit'] += $row['gross_profit'];
                            }
                            else {
                                $result_chart[$row['get_year']]['gross_profit'] = $row['gross_profit'];
                            }
                        }
                        else {
                            $result_chart[$row['get_month']]['get_month'] = $row['get_month'];
                            if (isset($result_chart[$row['get_month']]['customer_count'])) {
                                $result_chart[$row['get_month']]['customer_count'] += $row['customer_count'];
                            }
                            else {
                                $result_chart[$row['get_month']]['customer_count'] = $row['customer_count'];
                            }

                            if (isset($result_chart[$row['get_month']]['item_count'])) {
                                $result_chart[$row['get_month']]['item_count'] += $row['item_count'];
                            }
                            else {
                                $result_chart[$row['get_month']]['item_count'] = $row['item_count'];
                            }

                            if (isset($result_chart[$row['get_month']]['gross_revenue'])) {
                                $result_chart[$row['get_month']]['gross_revenue'] += $row['gross_revenue'];
                            }
                            else {
                                $result_chart[$row['get_month']]['gross_revenue'] = $row['gross_revenue'];
                            }

                            if (isset($result_chart[$row['get_month']]['gross_expenses'])) {
                                $result_chart[$row['get_month']]['gross_expenses'] += $row['gross_expenses'];
                            }
                            else {
                                $result_chart[$row['get_month']]['gross_expenses'] = $row['gross_expenses'];
                            }

                            if (isset($result_chart[$row['get_month']]['gross_profit'])) {
                                $result_chart[$row['get_month']]['gross_profit'] += $row['gross_profit'];
                            }
                            else {
                                $result_chart[$row['get_month']]['gross_profit'] = $row['gross_profit'];
                            }
                        }
                    }
                    else {
                        $result_chart[$row['get_date']]['get_date'] = $row['get_date'];

                        if (isset($result_chart[$row['get_date']]['customer_count'])) {
                            $result_chart[$row['get_date']]['customer_count'] += $row['customer_count'];
                        }
                        else {
                            $result_chart[$row['get_date']]['customer_count'] = $row['customer_count'];
                        }

                        if (isset($result_chart[$row['get_date']]['item_count'])) {
                            $result_chart[$row['get_date']]['item_count'] += $row['item_count'];
                        }
                        else {
                            $result_chart[$row['get_date']]['item_count'] = $row['item_count'];
                        }

                        if (isset($result_chart[$row['get_date']]['gross_revenue'])) {
                            $result_chart[$row['get_date']]['gross_revenue'] += $row['gross_revenue'];
                        }
                        else {
                            $result_chart[$row['get_date']]['gross_revenue'] = $row['gross_revenue'];
                        }

                        if (isset($result_chart[$row['get_date']]['gross_expenses'])) {
                            $result_chart[$row['get_date']]['gross_expenses'] += $row['gross_expenses'];
                        }
                        else {
                            $result_chart[$row['get_date']]['gross_expenses'] = $row['gross_expenses'];
                        }

                        if (isset($result_chart[$row['get_date']]['gross_profit'])) {
                            $result_chart[$row['get_date']]['gross_profit'] += $row['gross_profit'];
                        }
                        else {
                            $result_chart[$row['get_date']]['gross_profit'] = $row['gross_profit'];
                        }

                    }

                    // get top 10 peak hours
                    $result['get_hours'][$row['get_hours']][$row['outlet_id']]['outlet_id']   = $row['outlet_id'];
                    $result['get_hours'][$row['get_hours']][$row['outlet_id']]['outlet_name'] = $row['outlet_name'];
                    $result['get_hours'][$row['get_hours']][$row['outlet_id']]['store_id']    = $row['store_id'];
                    $result['get_hours'][$row['get_hours']][$row['outlet_id']]['store_name']  = $row['store_name'];

                    if (isset($result['get_hours'][$row['get_hours']][$row['outlet_id']]['item_count'])) {
                        $result['get_hours'][$row['get_hours']][$row['outlet_id']]['item_count'] += $row['item_count'];
                    }
                    else {
                        $result['get_hours'][$row['get_hours']][$row['outlet_id']]['item_count'] = $row['item_count'];
                    }

                    if (isset($result['get_hours'][$row['get_hours']][$row['outlet_id']]['customer_count'])) {
                        $result['get_hours'][$row['get_hours']][$row['outlet_id']]['customer_count'] += $row['customer_count'];
                    }
                    else {
                        $result['get_hours'][$row['get_hours']][$row['outlet_id']]['customer_count'] = $row['customer_count'];
                    }

                    if (isset($result['get_hours'][$row['get_hours']][$row['outlet_id']]['gross_profit'])) {
                        $result['get_hours'][$row['get_hours']][$row['outlet_id']]['gross_profit'] += $row['gross_profit'];
                    }
                    else {
                        $result['get_hours'][$row['get_hours']][$row['outlet_id']]['gross_profit'] = $row['gross_profit'];
                    }

                    // get top 10 peak day
                    $result['get_date'][$row['get_date']][$row['outlet_id']]['outlet_id']   = $row['outlet_id'];
                    $result['get_date'][$row['get_date']][$row['outlet_id']]['outlet_name'] = $row['outlet_name'];
                    $result['get_date'][$row['get_date']][$row['outlet_id']]['store_id']    = $row['store_id'];
                    $result['get_date'][$row['get_date']][$row['outlet_id']]['store_name']  = $row['store_name'];

                    if (isset($result['get_date'][$row['get_date']][$row['outlet_id']]['item_count'])) {
                        $result['get_date'][$row['get_date']][$row['outlet_id']]['item_count'] += $row['item_count'];
                    }
                    else {
                        $result['get_date'][$row['get_date']][$row['outlet_id']]['item_count'] = $row['item_count'];
                    }

                    if (isset($result['get_date'][$row['get_date']][$row['outlet_id']]['customer_count'])) {
                        $result['get_date'][$row['get_date']][$row['outlet_id']]['customer_count'] += $row['customer_count'];
                    }
                    else {
                        $result['get_date'][$row['get_date']][$row['outlet_id']]['customer_count'] = $row['customer_count'];
                    }

                    if (isset($result['get_date'][$row['get_date']][$row['outlet_id']]['gross_profit'])) {
                        $result['get_date'][$row['get_date']][$row['outlet_id']]['gross_profit'] += $row['gross_profit'];
                    }
                    else {
                        $result['get_date'][$row['get_date']][$row['outlet_id']]['gross_profit'] = $row['gross_profit'];
                    }

                    // get top 10 peak month
                    $result['get_month'][$row['get_month']][$row['outlet_id']]['outlet_id']   = $row['outlet_id'];
                    $result['get_month'][$row['get_month']][$row['outlet_id']]['outlet_name'] = $row['outlet_name'];
                    $result['get_month'][$row['get_month']][$row['outlet_id']]['store_id']    = $row['store_id'];
                    $result['get_month'][$row['get_month']][$row['outlet_id']]['store_name']  = $row['store_name'];

                    if (isset($result['get_month'][$row['get_month']][$row['outlet_id']]['item_count'])) {
                        $result['get_month'][$row['get_month']][$row['outlet_id']]['item_count'] += $row['item_count'];
                    }
                    else {
                        $result['get_month'][$row['get_month']][$row['outlet_id']]['item_count'] = $row['item_count'];
                    }

                    if (isset($result['get_month'][$row['get_month']][$row['outlet_id']]['customer_count'])) {
                        $result['get_month'][$row['get_month']][$row['outlet_id']]['customer_count'] += $row['customer_count'];
                    }
                    else {
                        $result['get_month'][$row['get_month']][$row['outlet_id']]['customer_count'] = $row['customer_count'];
                    }

                    if (isset($result['get_month'][$row['get_month']][$row['outlet_id']]['gross_profit'])) {
                        $result['get_month'][$row['get_month']][$row['outlet_id']]['gross_profit'] += $row['gross_profit'];
                    }
                    else {
                        $result['get_month'][$row['get_month']][$row['outlet_id']]['gross_profit'] = $row['gross_profit'];
                    }

                }

                $result_chart = array_values($result_chart);
            }

            $hours_array = array();
            $day_array   = array();
            $month_array = array();
            if ($result) {
                foreach ($result as $key => $res) {
                    if ($key == 'get_hours') {
                        foreach ($res as $jam => $h) {
                            foreach ($h as $detail) {
                                $detail['get_hours'] = $jam;
                                $hours_array[]       = $detail;
                            }
                        }
                    }
                    elseif ($key == 'get_date') {
                        foreach ($res as $jam => $h) {
                            foreach ($h as $detail) {
                                $detail['get_date'] = $jam;
                                $day_array[]        = $detail;
                            }
                        }
                    }
                    elseif ($key == 'get_month') {
                        foreach ($res as $jam => $h) {
                            foreach ($h as $detail) {
                                $detail['get_month'] = $jam;
                                $month_array[]       = $detail;
                            }
                        }
                    }
                }

                $this->load->helper(array('arrays'));

                $content = '<h4>Top 10 Peak Hour</h4>
                    <table class="table table-striped table-bordered table-hover dt-responsive" id="sales-table">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Jam</th>
                            <th>Resto</th>
                            <th>Outlet</th>
                            <th>Jumlah Item</th>
                            <th>Jumlah Tamu</th>
                            <th>Gross Profit</th>
                        </tr>
                        </thead><tbody>';

                // sorting by Gross Profit
                usort($hours_array, make_compare(['gross_profit', SORT_DESC], ['customer_count', SORT_DESC]));
                for ($i = 0; $i < count($hours_array); $i++) {
                    if ($i < 10) {
                        $content .= '<tr>';
                        $content .= '<td>' . ($i + 1) . '</td>';
                        $content .= '<td>' . $hours_array[$i]['get_hours'] . '.00</td>';
                        $content .= '<td>' . $hours_array[$i]['store_name'] . '</td>';
                        $content .= '<td>' . $hours_array[$i]['outlet_name'] . '</td>';
                        $content .= '<td>' . $hours_array[$i]['item_count'] . '</td>';
                        $content .= '<td>' . ceil($hours_array[$i]['customer_count']) . '</td>';
                        $content .= '<td>Rp ' . number_format($hours_array[$i]['gross_profit'], 0, "", ".") . '</td>';
                        $content .= '</tr>';
                    }
                    else {
                        break;
                    }
                }
                $content .= '</tbody>
                    </table>';

                $content .= '<h4>Top 10 Peak Day</h4>
                    <table class="table table-striped table-bordered table-hover dt-responsive" id="sales-table">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Resto</th>
                            <th>Outlet</th>
                            <th>Jumlah Item</th>
                            <th>Jumlah Tamu</th>
                            <th>Gross Profit</th>
                        </tr>
                        </thead><tbody>';

                // sorting by Gross Profit
                usort($day_array, make_compare(['gross_profit', SORT_DESC], ['customer_count', SORT_DESC]));
                for ($i = 0; $i < count($day_array); $i++) {
                    if ($i < 10) {

                        $tanggal = convert_indonesia_date($day_array[$i]['get_date']);
                        $content .= '<tr>';
                        $content .= '<td>' . ($i + 1) . '</td>';
                        $content .= '<td>' . $tanggal . '</td>';
                        $content .= '<td>' . $day_array[$i]['store_name'] . '</td>';
                        $content .= '<td>' . $day_array[$i]['outlet_name'] . '</td>';
                        $content .= '<td>' . $day_array[$i]['item_count'] . '</td>';
                        $content .= '<td>' . ceil($day_array[$i]['customer_count']) . '</td>';
                        $content .= '<td>Rp ' . number_format($day_array[$i]['gross_profit'], 0, "", ".") . '</td>';
                        $content .= '</tr>';
                    }
                    else {
                        break;
                    }
                }
                $content .= '</tbody>
                    </table>';

                $content .= '<h4>Top 10 Peak Month</h4>
                    <table class="table table-striped table-bordered table-hover dt-responsive" id="sales-table">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Bulan</th>
                            <th>Resto</th>
                            <th>Outlet</th>
                            <th>Jumlah Item</th>
                            <th>Jumlah Tamu</th>
                            <th>Gross Profit</th>
                        </tr>
                        </thead><tbody>';

                // sorting by Gross Profit
                usort($month_array, make_compare(['gross_profit', SORT_DESC], ['customer_count', SORT_DESC]));
                for ($i = 0; $i < count($month_array); $i++) {
                    if ($i < 10) {
                        $content .= '<tr>';
                        $content .= '<td>' . ($i + 1) . '</td>';
                        $content .= '<td>' . convert_indonesia_month($month_array[$i]['get_month']) . '</td>';
                        $content .= '<td>' . $month_array[$i]['store_name'] . '</td>';
                        $content .= '<td>' . $month_array[$i]['outlet_name'] . '</td>';
                        $content .= '<td>' . $month_array[$i]['item_count'] . '</td>';
                        $content .= '<td>' . ceil($month_array[$i]['customer_count']) . '</td>';
                        $content .= '<td>Rp ' . number_format($month_array[$i]['gross_profit'], 0, "", ".") . '</td>';
                        $content .= '</tr>';
                    }
                    else {
                        break;
                    }
                }
                $content .= '</tbody>
                    </table>';

                $ret_data['status']     = true;
                $ret_data['data']       = $content;
                $ret_data['data_chart'] = $result_chart;
            }
        }
        echo json_encode($ret_data);
    }

    public function table()
    {
        $this->data['title']    = $this->lang->line('table_analytics_title');
        $this->data['subtitle'] = $this->lang->line('table_analytics_title');

        $this->load->model('store_model');

        $this->data['all_store']  = $this->store_model->get_all_store();
        $this->data['all_outlet'] = $this->store_model->get_all_outlet();
        $this->data['content'] .= $this->load->view('admin/analytics-table', $this->data, true);
        $this->render('analytics');
    }

    public function get_table_data()
    {
        $start_date = $this->input->post('start_date');
        $end_date   = $this->input->post('end_date');
        $month      = $this->input->post('month');
        $year       = $this->input->post('year');
        $store      = $this->input->post('store');

        $this->load->model('order_model');
        $this->load->model('store_model');

        $ret_data            = array();
        $ret_data['status']  = false;
        $ret_data['message'] = '<div style="border: 1px solid #999;text-align: center;padding: 10px;margin-top: 20px">No Data</div>';
        $total_days_of_diff  = 0;
        $total_year_of_diff  = 0;
        $from="";
        $to="";
        if (empty($start_date) && empty($end_date) && empty($month) && empty($year) && empty($store)) {

        }
        else {

            $where = array('bill.table_id <>' => '0');
            if (! empty($month) || ! empty($year)) {
                if (! empty($month)) {
                    //monthly
                    $date_format_group = "Week";
                    if (empty($year)) {
                        $year       = date("Y");
                        $order_data = $this->order_model->get_order_by_date_range($year . "-" . $month . "-01 00:00:00", $year . "-" . $month . "-31 23:59:59", $where);
                        $from=$year . "-" . $month . "-01 00:00:00";
                        $to=$year . "-" . $month . "-31 23:59:59";
                    }
                    else{
                        $order_data = $this->order_model->get_order_by_date_range($year . "-" . $month . "-01 00:00:00", $year . "-" . $month . "-31 23:59:59", $where);
                        $from=$year . "-" . $month . "-01 00:00:00";
                        $to=$year . "-" . $month . "-31 23:59:59";
                      
                    }
                }
                else {
                    //yearly
                    $date_format_group = "Month";
                    $order_data        = $this->order_model->get_order_by_date_range($year . "-01-01 00:00:00", $year . "-12-31 23:59:59", $where);
                    $from=$year . "-01-01 00:00:00";
                    $to=$year . "-12-31 23:59:59";

                }
            }
            else if (! empty($start_date) && ! empty($end_date)) {
                //get berdasarkan start & end date
                $order_data = $this->order_model->get_order_by_date_range($start_date . " 00:00:00", $end_date . " 23:59:59", $where);
                $from=$start_date . " 00:00:00";
                $end_date . " 23:59:59";

                $diff_start = new DateTime($start_date);
                $diff_end   = new DateTime($end_date);
                $interval   = $diff_end->diff($diff_start);

                $total_year_of_diff = $interval->format('%y');
                $total_days_of_diff = $interval->format('%a');

            }
            else {
                $order_data = array();
            }

            // print_r($order_data);
            // exit;
            //fetching all data
            $res_data = array();
            foreach ($order_data as $key => $single_order) {
                // $history_data = $this->order_model->get_order_history_by_order($single_order->id);
                $history_data = $this->order_model->get_history($single_order->id);
                if (! empty($history_data)) {
                    $to_time   = strtotime($history_data->start_order);
                    $from_time = strtotime($history_data->end_order);
                    $history=$this->order_model->calculate_total_order_bill_report($single_order->id,$from,$to);
                    // echo $history['total_customer_count']."<br>";
                    // $history = json_decode($history_data->history);

                    $minutes   = round(abs($to_time - $from_time) / 60, 0);

                    $get_year       = date("Y", $to_time);
                    $get_month      = date("F", $to_time);
                    $get_month_year = date("m-Y", $to_time);
                    $get_date       = date("d-m-Y", $to_time);

                    if (! empty($store) && $store != 0) {
                        if ($store != $history_data->store_id) {
                            continue;
                        }
                    }

                    $res_data[] = array('table_id' => $history_data->table_id,
                                        'table_name' => $history_data->table_name,
                                        'floor_id' => $history_data->floor_id,
                                        'floor_name' => $history_data->floor_name,
                                        'store_id' => $history_data->store_id,
                                        'store_name' => $history_data->store_name,
                                        'customer_count' => $history['total_customer_count'],
                                        'get_minutes' => $minutes,
                                        'get_month' => $get_month,
                                        'get_month_year' => $get_month_year,
                                        'get_year' => $get_year,
                                        'get_date' => $get_date,
                                        'order_date' => $history_data->start_order,
                                        'order_end' => $history_data->end_order);


                }
            }

            $result          = array();
            $result_chart    = array();
            $arr_total_table = array();
            // echo "<pre>";
            // print_r($res_data);
            // exit;
            if (count($res_data) > 0) {
                foreach ($res_data as $row) {
                    // Chart
                    if ((empty($month) && ! empty($year)) || $total_days_of_diff > 31) {
                        // yearly
                        if ($total_year_of_diff > 0) {
                            $result_chart[$row['get_year']]['get_year'] = $row['get_year'];
                            if (isset($result_chart[$row['get_year']]['customer_count'])) {
                                $result_chart[$row['get_year']]['customer_count'] += $row['customer_count'];
                            }
                            else {
                                $result_chart[$row['get_year']]['customer_count'] = $row['customer_count'];
                            }

                            if (isset($result_chart[$row['get_year']]['get_minutes'])) {
                                $result_chart[$row['get_year']]['get_minutes'] += $row['get_minutes'];
                            }
                            else {
                                $result_chart[$row['get_year']]['get_minutes'] = $row['get_minutes'];
                            }

                            if (isset($arr_total_table[$row['get_year']][$row['table_id']])) {
                                $arr_total_table[$row['get_year']][$row['table_id']] += 1;
                            }
                            else {
                                $arr_total_table[$row['get_year']][$row['table_id']] = 1;
                            }

                            $result_chart[$row['get_year']]['total_table'] = count($arr_total_table[$row['get_year']]);
                        }
                        else {
                            $result_chart[$row['get_month']]['get_month'] = $row['get_month'];
                            if (isset($result_chart[$row['get_month']]['customer_count'])) {
                                $result_chart[$row['get_month']]['customer_count'] += $row['customer_count'];
                            }
                            else {
                                $result_chart[$row['get_month']]['customer_count'] = $row['customer_count'];
                            }

                            if (isset($result_chart[$row['get_month']]['get_minutes'])) {
                                $result_chart[$row['get_month']]['get_minutes'] += $row['get_minutes'];
                            }
                            else {
                                $result_chart[$row['get_month']]['get_minutes'] = $row['get_minutes'];
                            }

                            if (isset($arr_total_table[$row['get_month']][$row['table_id']])) {
                                $arr_total_table[$row['get_month']][$row['table_id']] += 1;
                            }
                            else {
                                $arr_total_table[$row['get_month']][$row['table_id']] = 1;
                            }

                            $result_chart[$row['get_month']]['total_table'] = count($arr_total_table[$row['get_month']]);
                        }
                    }
                    else {
                        $result_chart[$row['get_date']]['get_date'] = $row['get_date'];
                        if (isset($result_chart[$row['get_date']]['customer_count'])) {
                            $result_chart[$row['get_date']]['customer_count'] += $row['customer_count'];
                        }
                        else {
                            $result_chart[$row['get_date']]['customer_count'] = $row['customer_count'];
                        }

                        if (isset($result_chart[$row['get_date']]['get_minutes'])) {
                            $result_chart[$row['get_date']]['get_minutes'] += $row['get_minutes'];
                        }
                        else {
                            $result_chart[$row['get_date']]['get_minutes'] = $row['get_minutes'];
                        }

                        if (isset($arr_total_table[$row['get_date']][$row['table_id']])) {
                            $arr_total_table[$row['get_date']][$row['table_id']] += 1;
                        }
                        else {
                            $arr_total_table[$row['get_date']][$row['table_id']] = 1;
                        }

                        $result_chart[$row['get_date']]['total_table'] = count($arr_total_table[$row['get_date']]);

                    }

                    // get top 10
                    $result[$row['table_id']]['table_id']   = $row['table_id'];
                    $result[$row['table_id']]['table_name'] = $row['table_name'];
                    $result[$row['table_id']]['store_id']   = $row['store_id'];
                    $result[$row['table_id']]['store_name'] = $row['store_name'];
                    if (isset($result[$row['table_id']]['customer_count'])) {
                        $result[$row['table_id']]['customer_count'] += $row['customer_count'];
                    }
                    else {
                        $result[$row['table_id']]['customer_count'] = $row['customer_count'];
                    }

                    if (isset($result[$row['table_id']]['get_minutes'])) {
                        $result[$row['table_id']]['get_minutes'] += $row['get_minutes'];
                    }
                    else {
                        $result[$row['table_id']]['get_minutes'] = $row['get_minutes'];
                    }

                }
                $result       = array_values($result);
                $result_chart = array_values($result_chart);
            }

            if ($result) {
                $this->load->helper(array('arrays'));

                $content = '<h4>Top 10 Seated table</h4>
                    <table class="table table-striped table-bordered table-hover dt-responsive" id="sales-table">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Meja</th>
                            <th>Resto</th>
                            <th>Average Lama duduk</th>
                            <th>Jumlah Tamu</th>
                        </tr>
                        </thead><tbody>';

                // sorting by customer count desc
                usort($result, make_compare(['customer_count', SORT_DESC]));

                for ($i = 0; $i < count($result); $i++) {
                    if ($i < 10) {

                        $convert_minutes = $result[$i]['get_minutes'] . ' Menit';
                        if ($result[$i]['get_minutes'] >= 60) {
                            $hours           = floor($result[$i]['get_minutes'] / 60);
                            $minutes         = ($result[$i]['get_minutes'] % 60);
                            $convert_minutes = $hours . ' Jam ' . $minutes . ' Menit';
                            // $convert_minutes = round(abs($result[$i]['get_minutes']) / 60, 1) . ' Jam';
                        }

                        $content .= '<tr>';
                        $content .= '<td>' . ($i + 1) . '</td>';
                        $content .= '<td>' . $result[$i]['table_name'] . '</td>';
                        $content .= '<td>' . $result[$i]['store_name'] . '</td>';
                        $content .= '<td>' . $convert_minutes . '</td>';
                        $content .= '<td>' . $result[$i]['customer_count'] . '</td>';
                        $content .= '</tr>';
                    }
                    else {
                        break;
                    }
                }
                $content .= '</tbody>
                    </table>';

                $content .= '<h4>Top 10 Longest Seated table</h4>
                    <table class="table table-striped table-bordered table-hover dt-responsive" id="sales-table">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Meja</th>
                            <th>Resto</th>
                            <th>Average Lama duduk</th>
                            <th>Jumlah Tamu</th>
                        </tr>
                        </thead><tbody>';

                // sorting by longest minutes  desc
                usort($result, make_compare(['get_minutes', SORT_DESC]));

                for ($i = 0; $i < count($result); $i++) {
                    if ($i < 10) {

                        $convert_minutes = $result[$i]['get_minutes'] . ' Menit';
                        if ($result[$i]['get_minutes'] >= 60) {
                            $hours           = floor($result[$i]['get_minutes'] / 60);
                            $minutes         = ($result[$i]['get_minutes'] % 60);
                            $convert_minutes = $hours . ' Jam ' . $minutes . ' Menit';
                            // $convert_minutes = round(abs($result[$i]['get_minutes']) / 60, 1) . ' Jam';
                        }

                        $content .= '<tr>';
                        $content .= '<td>' . ($i + 1) . '</td>';
                        $content .= '<td>' . $result[$i]['table_name'] . '</td>';
                        $content .= '<td>' . $result[$i]['store_name'] . '</td>';
                        $content .= '<td>' . $convert_minutes . '</td>';
                        $content .= '<td>' . $result[$i]['customer_count'] . '</td>';
                        $content .= '</tr>';
                    }
                    else {
                        break;
                    }
                }
                $content .= '</tbody>
                    </table>';

                $ret_data['status']     = true;
                $ret_data['data']       = $content;
                $ret_data['data_chart'] = $result_chart;
            }
        }
        echo json_encode($ret_data);
    }

    public function staff()
    {
        $this->data['title']    = $this->lang->line('staff_analytics_title');
        $this->data['subtitle'] = $this->lang->line('staff_analytics_title');

        $this->load->model('store_model');

        $this->data['all_store']  = $this->store_model->get_all_store();
        $this->data['all_outlet'] = $this->store_model->get_all_outlet();
        $this->data['content'] .= $this->load->view('admin/analytics-staff', $this->data, true);
        $this->render('analytics');
    }

    public function get_staff_data()
    {
        $start_date = $this->input->post('start_date');
        $end_date   = $this->input->post('end_date');
        $month      = $this->input->post('month');
        $year       = $this->input->post('year');
        $store      = $this->input->post('store');

        $this->load->model('order_model');
        $this->load->model('store_model');

        $ret_data            = array();
        $ret_data['status']  = false;
        $ret_data['message'] = '<div style="border: 1px solid #999;text-align: center;padding: 10px;margin-top: 20px">No Data</div>';
        $total_days_of_diff  = 0;
        $total_year_of_diff  = 0;
        $from="";
        $to="";
        if (empty($start_date) && empty($end_date) && empty($month) && empty($year) && empty($store)) {

        }
        else {
            $where = array('bill.table_id <>' => '0');
            if (! empty($month) || ! empty($year)) {
                if (! empty($month)) {
                    //monthly
                    $date_format_group = "Week";
                    if (empty($year)) {
                        $year       = date("Y");
                        $order_data = $this->order_model->get_order_by_date_range($year . "-" . $month . "-01 00:00:00", $year . "-" . $month . "-31 23:59:59",$where);
                        $from=$year . "-" . $month . "-01 00:00:00";
                        $to=$year . "-" . $month . "-31 23:59:59";
                    }
                    else{
                        $order_data = $this->order_model->get_order_by_date_range($year . "-" . $month . "-01 00:00:00", $year . "-" . $month . "-31 23:59:59",$where);
                        $from=$year . "-" . $month . "-01 00:00:00";
                        $to=$year . "-" . $month . "-31 23:59:59";
                    }
                }
                else {
                    //yearly
                    $date_format_group = "Month";
                    $order_data        = $this->order_model->get_order_by_date_range($year . "-01-01 00:00:00", $year . "-12-31 23:59:59",$where);
                    $from=$year . "-01-01 00:00:00";
                    $to=$year . "-12-31 23:59:59";

                }
            }
            else if (! empty($start_date) && ! empty($end_date)) {
                //get berdasarkan start & end date
                $order_data = $this->order_model->get_order_by_date_range($start_date . " 00:00:00", $end_date . " 23:59:59",$where);

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
            $res_data = array();

            foreach ($order_data as $key => $single_order) {
                // $history_data = $this->order_model->get_order_history_by_order($single_order->id);
                $history_data = $this->order_model->get_history($single_order->id);
                if (! empty($history_data)) {
                    $history=$this->order_model->calculate_total_order_bill_report($single_order->id,$from,$to);

                    // $history = json_decode($history_data->history);

                    $to_time   = strtotime($history_data->start_order);
                    $from_time = strtotime($history_data->end_order);
                    $minutes   = round(abs($to_time - $from_time) / 60, 0);

                    $get_year       = date("Y", $to_time);
                    $get_month      = date("F", $to_time);
                    $get_month_year = date("m-Y", $to_time);
                    $get_date       = date("d-m-Y", $to_time);

                    if (! empty($store) && $store != 0) {
                        if ($store != $history_data->store_id) {
                            continue;
                        }
                    }
                    $role_staff_1 = $this->order_model->get_role_staff($history_data->cashier_id);
                    if ($role_staff_1) {
                        $role1 = $role_staff_1[0]->id;
                        $role1_access = $role_staff_1[0]->feature_accessed;
                    }
                    else {
                        $role1 = 0;
                        $role1_access = "";
                    }
                    $role_staff_2 = $this->order_model->get_role_staff($history_data->created_by);
                    if ($role_staff_2) {
                        $role2 = $role_staff_2[0]->id;
                        $role2_access = $role_staff_1[0]->feature_accessed;
                    }
                    else {
                        $role2 = 0;
                        $role2_access = "";
                    }

                    $res_data[] = array('table_id' => $history_data->table_id,
                                        'table_name' => $history_data->table_name,
                                        'floor_id' => $history_data->floor_id,
                                        'floor_name' => $history_data->floor_name,
                                        'store_id' => $history_data->store_id,
                                        'store_name' => $history_data->store_name,
                                        'cashier_id' => $history_data->cashier_id,
                                        'cashier_name' => (sizeof($role_staff_1)>0 ? $role_staff_1[0]->name : ""),
                                        'role_staff_cashier' => $role1,
                                        'access_staff_cashier' => $role1_access,
                                        'waiter_id' => $history_data->created_by,
                                        'waiter_name' => (sizeof($role_staff_2)>0 ? $role_staff_2[0]->name : ""),
                                        'role_staff_waiter' => $role2,
                                        'access_staff_waiter' => $role2_access,
                                        'customer_count' => $history['total_customer_count'],
                                        'get_minutes' => $minutes,
                                        'get_month' => $get_month,
                                        'get_month_year' => $get_month_year,
                                        'get_year' => $get_year,
                                        'get_date' => $get_date,
                                        'order_date' => $history_data->start_order,
                                        'order_end' => $history_data->end_order);


                }
            }

            $result                     = array();
            $result_chart               = array();
            $arr_total_customer_chart   = array();
            $arr_total_customer_per_day = array();
            // echo "<pre>";
            // print_r($res_data);
            // exit;
            if (count($res_data) > 0) {
                foreach ($res_data as $row) {
                    if ($row['customer_count'] == '0') {
                        $row['customer_count'] = 1;
                    }

                    // Chart
                    if ((empty($month) && ! empty($year)) || $total_days_of_diff > 31) {
                        // yearly
                        if ($total_year_of_diff > 0) {

                            $result_chart[$row['get_year']]['get_year'] = $row['get_year'];

                            if ($row['waiter_id'] != '0') {
                                if ($row['cashier_id'] != $row['waiter_id']) {
                                    // waiter
                                    if (isset($arr_total_customer_chart[$row['get_year']][$row['role_staff_waiter']]['customer_count'])) {
                                        $arr_total_customer_chart[$row['get_year']][$row['role_staff_waiter']]['customer_count'] += $row['customer_count'];
                                    }
                                    else {
                                        $arr_total_customer_chart[$row['get_year']][$row['role_staff_waiter']]['customer_count'] = $row['customer_count'];
                                    }

                                    // cashier
                                    if (isset($arr_total_customer_chart[$row['get_year']][$row['role_staff_cashier']]['customer_count'])) {
                                        $arr_total_customer_chart[$row['get_year']][$row['role_staff_cashier']]['customer_count'] += $row['customer_count'];
                                    }
                                    else {
                                        $arr_total_customer_chart[$row['get_year']][$row['role_staff_cashier']]['customer_count'] = $row['customer_count'];
                                    }

                                    $result_chart[$row['get_year']]['total_customer'] = $arr_total_customer_chart[$row['get_year']];
                                }
                                else {
                                    // cashier
                                    if (isset($arr_total_customer_chart[$row['get_year']][$row['role_staff_cashier']]['customer_count'])) {
                                        $arr_total_customer_chart[$row['get_year']][$row['role_staff_cashier']]['customer_count'] += $row['customer_count'];
                                    }
                                    else {
                                        $arr_total_customer_chart[$row['get_year']][$row['role_staff_cashier']]['customer_count'] = $row['customer_count'];
                                    }
                                    $result_chart[$row['get_year']]['total_customer'] = $arr_total_customer_chart[$row['get_year']];
                                }
                            }
                            else {
                                // cashier
                                if (isset($arr_total_customer_chart[$row['get_year']][$row['role_staff_cashier']]['customer_count'])) {
                                    $arr_total_customer_chart[$row['get_year']][$row['role_staff_cashier']]['customer_count'] += $row['customer_count'];
                                }
                                else {
                                    $arr_total_customer_chart[$row['get_year']][$row['role_staff_cashier']]['customer_count'] = $row['customer_count'];
                                }
                                $result_chart[$row['get_year']]['total_customer'] = $arr_total_customer_chart[$row['get_year']];
                            }

                        }
                        else {
                            $result_chart[$row['get_month']]['get_month'] = $row['get_month'];

                            if ($row['waiter_id'] != '0') {
                                if ($row['cashier_id'] != $row['waiter_id']) {
                                    // waiter
                                    if (isset($arr_total_customer_chart[$row['get_month']][$row['role_staff_waiter']]['customer_count'])) {
                                        $arr_total_customer_chart[$row['get_month']][$row['role_staff_waiter']]['customer_count'] += $row['customer_count'];
                                    }
                                    else {
                                        $arr_total_customer_chart[$row['get_month']][$row['role_staff_waiter']]['customer_count'] = $row['customer_count'];
                                    }

                                    // cashier
                                    if (isset($arr_total_customer_chart[$row['get_month']][$row['role_staff_cashier']]['customer_count'])) {
                                        $arr_total_customer_chart[$row['get_month']][$row['role_staff_cashier']]['customer_count'] += $row['customer_count'];
                                    }
                                    else {
                                        $arr_total_customer_chart[$row['get_month']][$row['role_staff_cashier']]['customer_count'] = $row['customer_count'];
                                    }

                                    $result_chart[$row['get_month']]['total_customer'] = $arr_total_customer_chart[$row['get_month']];
                                }
                                else {
                                    // cashier
                                    if (isset($arr_total_customer_chart[$row['get_month']][$row['role_staff_cashier']]['customer_count'])) {
                                        $arr_total_customer_chart[$row['get_month']][$row['role_staff_cashier']]['customer_count'] += $row['customer_count'];
                                    }
                                    else {
                                        $arr_total_customer_chart[$row['get_month']][$row['role_staff_cashier']]['customer_count'] = $row['customer_count'];
                                    }
                                    $result_chart[$row['get_month']]['total_customer'] = $arr_total_customer_chart[$row['get_month']];
                                }
                            }
                            else {
                                // cashier
                                if (isset($arr_total_customer_chart[$row['get_month']][$row['role_staff_cashier']]['customer_count'])) {
                                    $arr_total_customer_chart[$row['get_month']][$row['role_staff_cashier']]['customer_count'] += $row['customer_count'];
                                }
                                else {
                                    $arr_total_customer_chart[$row['get_month']][$row['role_staff_cashier']]['customer_count'] = $row['customer_count'];
                                }
                                $result_chart[$row['get_month']]['total_customer'] = $arr_total_customer_chart[$row['get_month']];
                            }
                        }
                    }
                    else {
                        $result_chart[$row['get_date']]['get_date'] = $row['get_date'];

                        if ($row['waiter_id'] != '0') {
                            if ($row['cashier_id'] != $row['waiter_id']) {
                                // waiter
                                if (isset($arr_total_customer_chart[$row['get_date']][$row['role_staff_waiter']]['customer_count'])) {
                                    $arr_total_customer_chart[$row['get_date']][$row['role_staff_waiter']]['customer_count'] += $row['customer_count'];
                                }
                                else {
                                    $arr_total_customer_chart[$row['get_date']][$row['role_staff_waiter']]['customer_count'] = $row['customer_count'];
                                }

                                // cashier
                                if (isset($arr_total_customer_chart[$row['get_date']][$row['role_staff_cashier']]['customer_count'])) {
                                    $arr_total_customer_chart[$row['get_date']][$row['role_staff_cashier']]['customer_count'] += $row['customer_count'];
                                }
                                else {
                                    $arr_total_customer_chart[$row['get_date']][$row['role_staff_cashier']]['customer_count'] = $row['customer_count'];
                                }

                                $result_chart[$row['get_date']]['total_customer'] = $arr_total_customer_chart[$row['get_date']];
                            }
                            else {
                                // cashier
                                if (isset($arr_total_customer_chart[$row['get_date']][$row['role_staff_cashier']]['customer_count'])) {
                                    $arr_total_customer_chart[$row['get_date']][$row['role_staff_cashier']]['customer_count'] += $row['customer_count'];
                                }
                                else {
                                    $arr_total_customer_chart[$row['get_date']][$row['role_staff_cashier']]['customer_count'] = $row['customer_count'];
                                }
                                $result_chart[$row['get_date']]['total_customer'] = $arr_total_customer_chart[$row['get_date']];
                            }
                        }
                        else {
                            // cashier
                            if (isset($arr_total_customer_chart[$row['get_date']][$row['role_staff_cashier']]['customer_count'])) {
                                $arr_total_customer_chart[$row['get_date']][$row['role_staff_cashier']]['customer_count'] += $row['customer_count'];
                            }
                            else {
                                $arr_total_customer_chart[$row['get_date']][$row['role_staff_cashier']]['customer_count'] = $row['customer_count'];
                            }
                            $result_chart[$row['get_date']]['total_customer'] = $arr_total_customer_chart[$row['get_date']];
                        }

                    }

                    // get top 10
                    if ($row['waiter_id'] != '0') {
                        if ($row['cashier_id'] != $row['waiter_id']) {
                            // waiter
                            $result[$row['waiter_id']]['user_id']    = $row['waiter_id'];
                            $result[$row['waiter_id']]['user_name']  = $row['waiter_name'];
                            $result[$row['waiter_id']]['group_id']   = $row['role_staff_waiter'];
                            $result[$row['waiter_id']]['group_access'] = $row['access_staff_waiter'];
                            $result[$row['waiter_id']]['store_id']   = $row['store_id'];
                            $result[$row['waiter_id']]['store_name'] = $row['store_name'];

                            if (isset($result[$row['waiter_id']]['customer_count'])) {
                                $result[$row['waiter_id']]['customer_count'] += $row['customer_count'];
                            }
                            else {
                                $result[$row['waiter_id']]['customer_count'] = $row['customer_count'];
                            }
                            if (isset($arr_total_customer_per_day[$row['waiter_id']][$row['get_date']])) {
                                $arr_total_customer_per_day[$row['waiter_id']][$row['get_date']] += $row['customer_count'];
                            }
                            else {
                                $arr_total_customer_per_day[$row['waiter_id']][$row['get_date']] = $row['customer_count'];
                            }
                            $result[$row['waiter_id']]['total_customer_per_day'] = count($arr_total_customer_per_day[$row['waiter_id']]);

                            // cashier
                            $result[$row['cashier_id']]['user_id']    = $row['cashier_id'];
                            $result[$row['cashier_id']]['user_name']  = $row['cashier_name'];
                            $result[$row['cashier_id']]['group_id']   = $row['role_staff_cashier'];
                            $result[$row['cashier_id']]['group_access']   = $row['access_staff_cashier'];
                            $result[$row['cashier_id']]['store_id']   = $row['store_id'];
                            $result[$row['cashier_id']]['store_name'] = $row['store_name'];

                            if (isset($result[$row['cashier_id']]['customer_count'])) {
                                $result[$row['cashier_id']]['customer_count'] += $row['customer_count'];
                            }
                            else {
                                $result[$row['cashier_id']]['customer_count'] = $row['customer_count'];
                            }
                            if (isset($arr_total_customer_per_day[$row['cashier_id']][$row['get_date']])) {
                                $arr_total_customer_per_day[$row['cashier_id']][$row['get_date']] += $row['customer_count'];
                            }
                            else {
                                $arr_total_customer_per_day[$row['cashier_id']][$row['get_date']] = $row['customer_count'];
                            }

                            $result[$row['cashier_id']]['total_customer_per_day'] = count($arr_total_customer_per_day[$row['cashier_id']]);
                        }
                        else {
                            $result[$row['cashier_id']]['user_id']    = $row['cashier_id'];
                            $result[$row['cashier_id']]['user_name']  = $row['cashier_name'];
                            $result[$row['cashier_id']]['group_id']   = $row['role_staff_cashier'];
                            $result[$row['cashier_id']]['group_access']   = $row['access_staff_cashier'];
                            $result[$row['cashier_id']]['store_id']   = $row['store_id'];
                            $result[$row['cashier_id']]['store_name'] = $row['store_name'];

                            if (isset($result[$row['cashier_id']]['customer_count'])) {
                                $result[$row['cashier_id']]['customer_count'] += $row['customer_count'];
                            }
                            else {
                                $result[$row['cashier_id']]['customer_count'] = $row['customer_count'];
                            }
                            if (isset($arr_total_customer_per_day[$row['cashier_id']][$row['get_date']])) {
                                $arr_total_customer_per_day[$row['cashier_id']][$row['get_date']] += $row['customer_count'];
                            }
                            else {
                                $arr_total_customer_per_day[$row['cashier_id']][$row['get_date']] = $row['customer_count'];
                            }

                            $result[$row['cashier_id']]['total_customer_per_day'] = count($arr_total_customer_per_day[$row['cashier_id']]);
                        }
                    }
                    else {

                        $result[$row['cashier_id']]['user_id']    = $row['cashier_id'];
                        $result[$row['cashier_id']]['user_name']  = $row['cashier_name'];
                        $result[$row['cashier_id']]['group_id']   = $row['role_staff_cashier'];
                        $result[$row['cashier_id']]['group_access']   = $row['access_staff_cashier'];
                        $result[$row['cashier_id']]['store_id']   = $row['store_id'];
                        $result[$row['cashier_id']]['store_name'] = $row['store_name'];

                        if (isset($result[$row['cashier_id']]['customer_count'])) {
                            $result[$row['cashier_id']]['customer_count'] += $row['customer_count'];
                        }
                        else {
                            $result[$row['cashier_id']]['customer_count'] = $row['customer_count'];
                        }
                        if (isset($arr_total_customer_per_day[$row['cashier_id']][$row['get_date']])) {
                            $arr_total_customer_per_day[$row['cashier_id']][$row['get_date']] += $row['customer_count'];
                        }
                        else {
                            $arr_total_customer_per_day[$row['cashier_id']][$row['get_date']] = $row['customer_count'];
                        }

                        $result[$row['cashier_id']]['total_customer_per_day'] = count($arr_total_customer_per_day[$row['cashier_id']]);
                    }


                }
                $result       = array_values($result);
                $result_chart = array_values($result_chart);
            }

            if ($result) {
                foreach ($result as &$value) {
                    $value["total_customer_per_day"] = round(abs($value['customer_count'] / $value['total_customer_per_day']), 0);
                }
                $this->load->helper(array('arrays'));

                $content = '<h4>Top 10 Waiter</h4>
                    <table class="table table-striped table-bordered table-hover dt-responsive" id="sales-table">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Waiter</th>
                            <th>Resto</th>
                            <th>Jumlah Tamu</th>
                            <th>Average jumlah tamu per hari</th>
                        </tr>
                        </thead><tbody>';

                // sorting by customer count desc
                usort($result, make_compare(['total_customer_per_day', SORT_DESC]));

                $count_waiter = 1;
                for ($i = 0; $i < count($result); $i++) {
                    // if ($result[$i]['group_id'] != '5') {
                    $tmp=explode(",",$result[$i]['group_access']);
                    if ( !in_array(array("dinein"),$tmp)) {
                        continue;
                    }

                    if ($count_waiter < 11) {
                        $content .= '<tr>';
                        $content .= '<td>' . ($count_waiter) . '</td>';
                        $content .= '<td>' . $result[$i]['user_name'] . '</td>';
                        $content .= '<td>' . $result[$i]['store_name'] . '</td>';
                        $content .= '<td>' . $result[$i]['customer_count'] . '</td>';
                        $content .= '<td>' . $result[$i]['total_customer_per_day'] . '</td>';
                        $content .= '</tr>';
                    }
                    else {
                        break;
                    }
                    $count_waiter++;

                }
                $content .= '</tbody>
                    </table>';

                $content .= '<h4>Top 10 Kasir</h4>
                    <table class="table table-striped table-bordered table-hover dt-responsive" id="sales-table">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kasir</th>
                            <th>Resto</th>
                            <th>Jumlah Tamu</th>
                            <th>Average jumlah tamu per hari</th>

                        </tr>
                        </thead><tbody>';

                // sorting by longest minutes  desc
                //usort($result, make_compare(['get_minutes', SORT_DESC]));

                $count_cashier = 1;
                for ($i = 0; $i < count($result); $i++) {
                    // if ($result[$i]['group_id'] != '3') {
                    $tmp=explode(",",$result[$i]['group_access']);
                    if ( !in_array(array("checkout"),$tmp)) {
                        continue;
                    }

                    if ($count_cashier < 11) {
                        $content .= '<tr>';
                        $content .= '<td>' . ($count_cashier) . '</td>';
                        $content .= '<td>' . $result[$i]['user_name'] . '</td>';
                        $content .= '<td>' . $result[$i]['store_name'] . '</td>';
                        $content .= '<td>' . $result[$i]['customer_count'] . '</td>';
                        $content .= '<td>' . $result[$i]['total_customer_per_day'] . '</td>';
                        $content .= '</tr>';
                    }
                    else {
                        break;
                    }
                    $count_cashier++;
                }
                $content .= '</tbody>
                    </table>';

                $ret_data['status']     = true;
                $ret_data['data']       = $content;
                $ret_data['data_chart'] = $result_chart;
            }
        }
        echo json_encode($ret_data);
    }

    public function menu()
    {
        $this->data['title']    = "Analisis Menu";
        $this->data['subtitle'] = "Analisis Menu";

        $this->load->model('store_model');

        $this->data['all_store']  = $this->store_model->get_all_store();
        $this->data['all_outlet'] = $this->store_model->get_all_outlet();
        $this->data['content'] .= $this->load->view('admin/analytics-menu', $this->data, true);
        $this->render('analytics');
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
        $ret_data['message'] = '<div style="border: 1px solid #999;text-align: center;padding: 10px;margin-top: 20px">No Data</div>';
        $total_days_of_diff  = 0;
        $total_year_of_diff  = 0;
        $from="";
        $to="";
        if (empty($start_date) && empty($end_date) && empty($month) && empty($year) && empty($store)) {

        }
        else {
            if (! empty($month) || ! empty($year)) {
                if (! empty($month)) {
                    //monthly
                    $date_format_group = "Week";
                    if (empty($year)) {
                        $year       = date("Y");
                        $order_data = $this->order_model->get_order_by_date_range($year . "-" . $month . "-01 00:00:00", $year . "-" . $month . "-31 23:59:59");
                        $from=$year . "-" . $month . "-01 00:00:00";
                        $to=$year . "-" . $month . "-31 23:59:59";
                    }
                    else
                        $order_data = $this->order_model->get_order_by_date_range($year . "-" . $month . "-01 00:00:00", $year . "-" . $month . "-31 23:59:59");
                      $from=$year . "-" . $month . "-01 00:00:00";
                      $to=$year . "-" . $month . "-31 23:59:59";
                }
                else {
                    //yearly
                    $date_format_group = "Month";
                    $order_data        = $this->order_model->get_order_by_date_range($year . "-01-01 00:00:00", $year . "-12-31 23:59:59");
                    $from=$year . "-01-01 00:00:00";
                    $to=$year . "-12-31 23:59:59";

                }
            }
            else if (! empty($start_date) && ! empty($end_date)) {
                //get berdasarkan start & end date
                $order_data = $this->order_model->get_order_by_date_range($start_date . " 00:00:00", $end_date . " 23:59:59");
                $from=$start_date . " 00:00:00";
                $to=$end_date . " 23:59:59";
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
                // $history_data = $this->order_model->get_order_history_by_order($single_order->id);
                $history_data = $this->order_model->get_history($single_order->id);
                if (! empty($history_data)) {
                    $history=$this->order_model->calculate_total_order_bill_report($single_order->id,$from,$to);
                    // $history = json_decode($history_data->history);

                    $to_time   = strtotime($history_data->start_order);
                    $from_time = strtotime($history_data->end_order);
                    $minutes   = round(abs($to_time - $from_time) / 60, 0);

                    $get_year       = date("Y", $to_time);
                    $get_month      = date("F", $to_time);
                    $get_month_year = date("m-Y", $to_time);
                    $get_date       = date("d-m-Y", $to_time);
                    $get_hours      = (string)date("H", $to_time);


                    $tax_percentage_total    = 0;
                    $additional_charge_total = 0;

                    if (isset($history['tax_price'])) {
                        foreach ($history['tax_price'] as $tax_price) {
                            $tax_percentage_total += $tax_price['tax_percentage'];
                        }
                    }

                    if (isset($history['extra_charge_price'])) {
                        foreach ($history['extra_charge_price'] as $extra_charge) {
                            $additional_charge_total += $extra_charge['charge_value'];
                        }
                    }

                    $counter_menu = 0;
                    $i = 0;
                    $order_list = $history['order_list'];
                    $total_order = sizeof($order_list);
                    // echo "<pre>";
                    // print_r($order_list);
                    // exit;
                    foreach($order_list as $i=>$single){
                    // while ($i < sizeof($order_list)) {

                        if (! empty($store) && $store != 0) {
                            if ($store != $order_list[$i]->store_id) {
                                 // $i +=1;
                                continue;
                            }
                        }

                        if (! empty($outlet) && $outlet != 0) {
                            if ($outlet != $order_list[$i]->outlet_id) {
                                 // $i +=1 ;
                                continue;
                                
                            }
                        }

                        
                        if(!isset($res_data[$order_list[$i]->menu_id]))
                        {
                            $side_dish_count_item = 0;
                            $side_dish_hpp        = 0;
                            $side_dish_price      = 0;

                       
                            // if (isset($order_list[$i]->side_dish_list)) {
                            //     foreach ($order_list[$i]->side_dish_list as $side_dish) {
                            //         $side_dish_hpp += $side_dish->side_dish_hpp;
                            //         $side_dish_price += $side_dish->side_dish_price;
                            //        // $side_dish_count_item += $side_dish->count;
                            //     }
                            // }

                            // $total_count_item = $side_dish_count_item + $single_list->count;
                            $total_count_item = $order_list[$i]->quantity;
                            $menu_price       = round($order_list[$i]->menu_price * $order_list[$i]->quantity, 0, PHP_ROUND_HALF_UP);
                            $menu_hpp         = round($order_list[$i]->menu_hpp * $order_list[$i]->quantity, 0, PHP_ROUND_HALF_UP);
                            // $gross_revenue    = round((($menu_price + $side_dish_price) + (($menu_price + $side_dish_price) * $tax_percentage_total / 100) + $additional_charge_total), 0, PHP_ROUND_HALF_UP);
                            $gross_revenue    = round(($menu_price  + ($menu_price  * $tax_percentage_total / 100) + $additional_charge_total), 0, PHP_ROUND_HALF_UP);
                            // $gross_expenses   = $menu_hpp + $side_dish_hpp;
                            $gross_expenses   = $menu_hpp;
                            $gross_profit     = $gross_revenue - $gross_expenses;

                            $res_data[$order_list[$i]->menu_id]['menu_id'] = $order_list[$i]->menu_id;
                            $res_data[$order_list[$i]->menu_id]['menu_name'] = $order_list[$i]->menu_name;    
                            $res_data[$order_list[$i]->menu_id]['menu_price'] = $order_list[$i]->menu_price;    
                            $res_data[$order_list[$i]->menu_id]['menu_count'] = $order_list[$i]->quantity;
                            $res_data[$order_list[$i]->menu_id]['store_id'] = $order_list[$i]->store_id;
                            $res_data[$order_list[$i]->menu_id]['store_name'] = $order_list[$i]->store_name;
                            $res_data[$order_list[$i]->menu_id]['outlet_id'] = $order_list[$i]->outlet_id;
                            $res_data[$order_list[$i]->menu_id]['outlet_name'] = $order_list[$i]->outlet_name;
                            $res_data[$order_list[$i]->menu_id]['get_month'] = $get_month;
                            $res_data[$order_list[$i]->menu_id]['get_month_year'] = $get_month_year;
                            $res_data[$order_list[$i]->menu_id]['get_year'] = $get_year;
                            $res_data[$order_list[$i]->menu_id]['get_date'] = $get_date;
                            $res_data[$order_list[$i]->menu_id]['get_hours'] = $get_hours;
                            $res_data[$order_list[$i]->menu_id]['order_date'] = $history_data->start_order;
                            $res_data[$order_list[$i]->menu_id]['order_end'] = $history_data->end_order;
                            $res_data[$order_list[$i]->menu_id]['receipt_id'] =    $order_list[$i]->receipt_number;
                            $res_data[$order_list[$i]->menu_id]['item_count'] = 1;
                            $res_data[$order_list[$i]->menu_id]['gross_profit'] = $gross_profit*$order_list[$i]->quantity;
                            $res_data[$order_list[$i]->menu_id]['gross_profit_item'] = $gross_profit;

                            if (isset($outlet_customer_data[$order_list[$i]->receipt_number][$order_list[$i]->outlet_id])) {
                                $outlet_customer_data[$order_list[$i]->receipt_number][$order_list[$i]->outlet_id] += 1;
                            }
                            else {
                                $outlet_customer_data[$order_list[$i]->receipt_number][$order_list[$i]->outlet_id] = 1;
                            }
                        }else{

                            $side_dish_count_item = 0;
                            $side_dish_hpp        = 0;
                            $side_dish_price      = 0;

                       
                            // if (isset($order_list[$i]->side_dish_list)) {
                            //     foreach ($order_list[$i]->side_dish_list as $side_dish) {
                            //         $side_dish_hpp += $side_dish->side_dish_hpp;
                            //         $side_dish_price += $side_dish->side_dish_price;
                            //     }
                            // }

                            $menu_price       = round($order_list[$i]->menu_price * $order_list[$i]->quantity, 0, PHP_ROUND_HALF_UP);
                            $menu_hpp         = round($order_list[$i]->menu_hpp * $order_list[$i]->quantity, 0, PHP_ROUND_HALF_UP);
                            // $gross_revenue    = round((($menu_price + $side_dish_price) + (($menu_price + $side_dish_price) * $tax_percentage_total / 100) + $additional_charge_total), 0, PHP_ROUND_HALF_UP);
                            $gross_revenue    = round(($menu_price  + ($menu_price  * $tax_percentage_total / 100) + $additional_charge_total), 0, PHP_ROUND_HALF_UP);
                            // $gross_expenses   = $menu_hpp + $side_dish_hpp;
                            $gross_expenses   = $menu_hpp;
                            $gross_profit     = $gross_revenue - $gross_expenses;

                            $res_data[$order_list[$i]->menu_id]['gross_profit'] += $gross_profit;
                            $res_data[$order_list[$i]->menu_id]['menu_count'] += $order_list[$i]->quantity;                          


                        }

                        
                        // $i++;  
                        



                    }      


                }
            }

            $result       = array();
            $result_chart = array();
            if (count($res_data) > 0) {
                foreach ($res_data as $row) {

                    if ($row['menu_count'] == '0') {
                        $row['menu_count'] = 1;
                    }

                    // $row['menu_count'] = $row['menu_count'] / $outlet_customer_data[$row['receipt_id']][$row['outlet_id']];

                    // Chart
                    if ((empty($month) && ! empty($year)) || $total_days_of_diff > 31) {
                        // yearly
                        if ($total_year_of_diff > 0) {
                            $result_chart[$row['get_year']]['get_year'] = $row['get_year'];
                            if (isset($result_chart[$row['get_year']]['menu_count'])) {
                                $result_chart[$row['get_year']]['menu_count'] += $row['menu_count'];
                            }
                            else {
                                $result_chart[$row['get_year']]['menu_count'] = $row['menu_count'];
                            }

                            if (isset($result_chart[$row['get_year']]['item_count'])) {
                                $result_chart[$row['get_year']]['item_count'] += $row['item_count'];
                            }
                            else {
                                $result_chart[$row['get_year']]['item_count'] = $row['item_count'];
                            }

                        }
                        else {
                            $result_chart[$row['get_month']]['get_month'] = $row['get_month'];
                            if (isset($result_chart[$row['get_month']]['menu_count'])) {
                                $result_chart[$row['get_month']]['menu_count'] += $row['menu_count'];
                            }
                            else {
                                $result_chart[$row['get_month']]['menu_count'] = $row['menu_count'];
                            }

                            if (isset($result_chart[$row['get_month']]['item_count'])) {
                                $result_chart[$row['get_month']]['item_count'] += $row['item_count'];
                            }
                            else {
                                $result_chart[$row['get_month']]['item_count'] = $row['item_count'];
                            }

                        }
                    }
                    else {
                        $result_chart[$row['get_date']]['get_date'] = $row['get_date'];

                        if (isset($result_chart[$row['get_date']]['menu_count'])) {
                            $result_chart[$row['get_date']]['menu_count'] += $row['menu_count'];
                        }
                        else {
                            $result_chart[$row['get_date']]['menu_count'] = $row['menu_count'];
                        }

                        if (isset($result_chart[$row['get_date']]['item_count'])) {
                            $result_chart[$row['get_date']]['item_count'] += $row['menu_count'];
                        }
                        else {
                            $result_chart[$row['get_date']]['item_count'] = $row['item_count'];
                        }


                    }

                    // get top 10 peak hours
                    $result['get_hours'][$row['get_hours']][$row['menu_id']]['outlet_id']   = $row['outlet_id'];
                    $result['get_hours'][$row['get_hours']][$row['menu_id']]['outlet_name'] = $row['outlet_name'];
                    $result['get_hours'][$row['get_hours']][$row['menu_id']]['store_id']    = $row['store_id'];
                    $result['get_hours'][$row['get_hours']][$row['menu_id']]['store_name']  = $row['store_name'];
                    $result['get_hours'][$row['get_hours']][$row['menu_id']]['menu_name']  = $row['menu_name'];
                    $result['get_hours'][$row['get_hours']][$row['menu_id']]['menu_price']  = $row['menu_price'];
                    $result['get_hours'][$row['get_hours']][$row['menu_id']]['menu_id'] = $row['menu_id'];
                    $result['get_hours'][$row['get_hours']][$row['menu_id']]['gross_profit']  = $row['gross_profit'];
                    $result['get_hours'][$row['get_hours']][$row['menu_id']]['gross_profit_item']  = $row['gross_profit_item'];

                    if (isset($result['get_hours'][$row['get_hours']][$row['menu_id']]['item_count'])) {
                        $result['get_hours'][$row['get_hours']][$row['menu_id']]['item_count'] += $row['item_count'];
                    }
                    else {
                        $result['get_hours'][$row['get_hours']][$row['menu_id']]['item_count'] = $row['item_count'];
                    }

                    if (isset($result['get_hours'][$row['get_hours']][$row['menu_id']]['menu_count'])) {
                        $result['get_hours'][$row['get_hours']][$row['menu_id']]['menu_count'] += $row['menu_count'];
                    }
                    else {
                        $result['get_hours'][$row['get_hours']][$row['menu_id']]['menu_count'] = $row['menu_count'];
                    }


                    // get top 10 peak day
                    $result['get_date'][$row['get_date']][$row['menu_id']]['menu_id']   = $row['menu_id'];
                    $result['get_date'][$row['get_date']][$row['menu_id']]['outlet_name'] = $row['outlet_name'];
                    $result['get_date'][$row['get_date']][$row['menu_id']]['store_id']    = $row['store_id'];
                    $result['get_date'][$row['get_date']][$row['menu_id']]['store_name']  = $row['store_name'];
                    $result['get_date'][$row['get_date']][$row['menu_id']]['menu_name']  = $row['menu_name'];
                    $result['get_date'][$row['get_date']][$row['menu_id']]['menu_price']  = $row['menu_price'];

                   

                    if (isset($result['get_date'][$row['get_date']][$row['menu_id']]['menu_count'])) {
                        $result['get_date'][$row['get_date']][$row['menu_id']]['menu_count'] += $row['menu_count'];
                    }
                    else {
                        $result['get_date'][$row['get_date']][$row['menu_id']]['menu_count'] = $row['menu_count'];
                    }


                    // get top 10 peak month
                    $result['get_month'][$row['get_month']][$row['menu_id']]['menu_id']   = $row['menu_id'];
                    $result['get_month'][$row['get_month']][$row['menu_id']]['outlet_name'] = $row['outlet_name'];
                    $result['get_month'][$row['get_month']][$row['menu_id']]['store_id']    = $row['store_id'];
                    $result['get_month'][$row['get_month']][$row['menu_id']]['store_name']  = $row['store_name'];
                    $result['get_month'][$row['get_month']][$row['menu_id']]['menu_name']  = $row['menu_name'];
                    $result['get_month'][$row['get_month']][$row['menu_id']]['menu_price']  = $row['menu_price'];

                    if (isset($result['get_month'][$row['get_month']][$row['menu_id']]['item_count'])) {
                        $result['get_month'][$row['get_month']][$row['menu_id']]['item_count'] += $row['item_count'];
                    }
                    else {
                        $result['get_month'][$row['get_month']][$row['menu_id']]['item_count'] = $row['item_count'];
                    }

                    if (isset($result['get_month'][$row['get_month']][$row['menu_id']]['menu_count'])) {
                        $result['get_month'][$row['get_month']][$row['menu_id']]['menu_count'] += $row['menu_count'];
                    }
                    else {
                        $result['get_month'][$row['get_month']][$row['menu_id']]['menu_count'] = $row['menu_count'];
                    }


                }

                $result_chart = array_values($result_chart);
            }

            $hours_array = array();
            $day_array   = array();
            $month_array = array();
            if ($result) {
                foreach ($result as $key => $res) {
                    if ($key == 'get_hours') {
                        foreach ($res as $jam => $h) {
                            foreach ($h as $detail) {
                                $detail['get_hours'] = $jam;
                                $hours_array[]       = $detail;
                            }
                        }
                    }
                    elseif ($key == 'get_date') {
                        foreach ($res as $jam => $h) {
                            foreach ($h as $detail) {
                                $detail['get_date'] = $jam;
                                $day_array[]        = $detail;
                            }
                        }
                    }
                    elseif ($key == 'get_month') {
                        foreach ($res as $jam => $h) {
                            foreach ($h as $detail) {
                                $detail['get_month'] = $jam;
                                $month_array[]       = $detail;
                            }
                        }
                    }
                }

                $this->load->helper(array('arrays'));

                $content = '<h4>Top 10 Profit</h4>
                    <table class="table table-striped table-bordered table-hover dt-responsive" id="sales-table">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Resto</th>
                            <th>Outlet</th>
                            <th>Menu</th>
                            <th>Jumlah Terjual</th>
                            <th>Profit per item</th>
                            <th>Total Profit</th>
                        </tr>
                        </thead><tbody>';

                // sorting by Gross Profit
                usort($hours_array, make_compare( ['gross_profit', SORT_DESC], ['menu_count', SORT_DESC]));
                for ($i = 0; $i < count($hours_array); $i++) {
                    if ($i < 10) {
                        $content .= '<tr>';
                        $content .= '<td>' . ($i + 1) . '</td>';
                        $content .= '<td>' . $hours_array[$i]['store_name'] . '</td>';
                        $content .= '<td>' . $hours_array[$i]['outlet_name'] . '</td>';
                        $content .= '<td>' . $hours_array[$i]['menu_name'] . '</td>';
                        $content .= '<td>' . ceil($hours_array[$i]['menu_count']) . '</td>';
                        $content .= '<td>Rp '. number_format($hours_array[$i]['gross_profit_item'], 0, "", ".") .'</td>';
                        $content .= '<td>Rp ' . number_format($hours_array[$i]['gross_profit'], 0, "", ".") . '</td>';
                        $content .= '</tr>';
                    }
                    else {
                        break;
                    }
                }
                $content .= '</tbody>
                    </table>';

                $content .= '<h4>Top 10 Peak Day</h4>
                    <table class="table table-striped table-bordered table-hover dt-responsive" id="sales-table">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Resto</th>
                            <th>Outlet</th>
                            <th>Menu</th>
                            <th>Jumlah Terjual</th>
                            <th>Harga</th>
                        </tr>
                        </thead><tbody>';

                // sorting by Gross Profit
                usort($day_array, make_compare(['menu_count', SORT_DESC]));
                for ($i = 0; $i < count($day_array); $i++) {
                    if ($i < 10) {

                        $tanggal = convert_indonesia_date($day_array[$i]['get_date']);
                        $content .= '<tr>';
                        $content .= '<td>' . ($i + 1) . '</td>';
                        $content .= '<td>' . $tanggal . '</td>';
                        $content .= '<td>' . $day_array[$i]['store_name'] . '</td>';
                        $content .= '<td>' . $day_array[$i]['outlet_name'] . '</td>';
                        $content .= '<td>' . $day_array[$i]['menu_name'] . '</td>';
                        $content .= '<td>' . ceil($day_array[$i]['menu_count']) . '</td>';
                        $content .= '<td>Rp ' . number_format($day_array[$i]['menu_price'], 0, "", ".") . '</td>';
                        $content .= '</tr>';
                    }
                    else {
                        break;
                    }
                }
                $content .= '</tbody>
                    </table>';

                $content .= '<h4>Top 10 Peak Month</h4>
                    <table class="table table-striped table-bordered table-hover dt-responsive" id="sales-table">
                        <thead>
                        <tr>
                           <th>No</th>
                            <th>Bulan</th>
                            <th>Resto</th>
                            <th>Outlet</th>
                            <th>Menu</th>
                            <th>Jumlah Terjual</th>
                            <th>Harga</th>
                        </tr>
                        </thead><tbody>';

                // sorting by Gross Profit
                usort($month_array, make_compare(['menu_count', SORT_DESC]));
                for ($i = 0; $i < count($month_array); $i++) {
                    if ($i < 10) {
                        $content .= '<tr>';
                        $content .= '<td>' . ($i + 1) . '</td>';
                        $content .= '<td>' . convert_indonesia_month($month_array[$i]['get_month']) . '</td>';
                        $content .= '<td>' . $month_array[$i]['store_name'] . '</td>';
                        $content .= '<td>' . $month_array[$i]['outlet_name'] . '</td>';
                        $content .= '<td>' . $month_array[$i]['menu_name'] . '</td>';
                        $content .= '<td>' . ceil($month_array[$i]['menu_count']) . '</td>';
                        $content .= '<td>Rp ' . number_format($month_array[$i]['menu_price'], 0, "", ".") . '</td>';
                        $content .= '</tr>';
                    }
                    else {
                        break;
                    }
                }
                $content .= '</tbody>
                    </table>';

                $ret_data['status']     = true;
                $ret_data['data']       = $content;
                $ret_data['data_chart'] = $result_chart;
                $ret_data['res_data'] = $history;
                // $ret_data['param'] = $history;
            }
        }
        echo json_encode($ret_data);
    }

    public function engineering()
    {
        $this->data['title']    = "Menu";
        $this->data['subtitle'] = "Menu";

        $this->load->model('store_model');

        $this->data['all_store']  = $this->store_model->get_all_store();
        $this->data['all_outlet'] = $this->store_model->get_all_outlet();
        $this->data['content'] .= $this->load->view('admin/analytics-engineering', $this->data, true);
        $this->render('analytics');
    }

    public function get_engineering_data()
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
        $ret_data['message'] = '<div style="border: 1px solid #999;text-align: center;padding: 10px;margin-top: 20px">No Data</div>';
        $total_days_of_diff  = 0;
        $total_year_of_diff  = 0;

        if (empty($start_date) && empty($end_date) && empty($month) && empty($year) && empty($store)) {

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

                    if (isset($history->extra_charge_price)) {
                        foreach ($history->extra_charge_price as $extra_charge) {
                            $additional_charge_total += $extra_charge->charge_value;
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

                       
                            if (isset($order_list[$i]->side_dish_list)) {
                                foreach ($order_list[$i]->side_dish_list as $side_dish) {
                                    $side_dish_hpp += round($side_dish->side_dish_hpp * $side_dish->count, 0, PHP_ROUND_HALF_UP);
                                    $side_dish_price += round($side_dish->side_dish_price * $side_dish->count, 0, PHP_ROUND_HALF_UP);
                                    $side_dish_count_item += $side_dish->count;
                                }
                            }

                            // $total_count_item = $side_dish_count_item + $single_list->count;
                            $total_count_item = $order_list[$i]->count;
                            $menu_price       = round($order_list[$i]->menu_price * $order_list[$i]->count, 0, PHP_ROUND_HALF_UP);
                            $menu_hpp         = round($order_list[$i]->menu_hpp * $order_list[$i]->count, 0, PHP_ROUND_HALF_UP);
                            $gross_revenue    = round((($menu_price + $side_dish_price) + (($menu_price + $side_dish_price) * $tax_percentage_total / 100) + $additional_charge_total), 0, PHP_ROUND_HALF_UP);
                            $gross_expenses   = $menu_hpp + $side_dish_hpp;
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

                            if (isset($outlet_customer_data[$history->order_data[0]->receipt_id][$order_list[$i]->outlet_id])) {
                                $outlet_customer_data[$history->order_data[0]->receipt_id][$order_list[$i]->outlet_id] += 1;
                            }
                            else {
                                $outlet_customer_data[$history->order_data[0]->receipt_id][$order_list[$i]->outlet_id] = 1;
                            }
                        }

                        $j = $i+1;

                            while ($j < sizeof($order_list)){ 

                                if($order_list[$i]->menu_id == $order_list[$j]->menu_id)
                                {

                                    $res_data[$order_list[$i]->menu_id]['menu_count'] += $order_list[$j]->count;
                                    $i++;
                                }
                                $j++;                           

                            }
                            $i++;  
                        

                                  

                    }      



                }
            }

            $result       = array();
            $result_chart = array();
            if (count($res_data) > 0) {
                foreach ($res_data as $row) {

                    if ($row['menu_count'] == '0') {
                        $row['menu_count'] = 1;
                    }

                    // Chart
                    if ((empty($month) && ! empty($year)) || $total_days_of_diff > 31) {
                        // yearly
                        if ($total_year_of_diff > 0) {
                            $result_chart[$row['get_year']]['get_year'] = $row['get_year'];
                            if (isset($result_chart[$row['get_year']]['menu_count'])) {
                                $result_chart[$row['get_year']]['menu_count'] += $row['menu_count'];
                            }
                            else {
                                $result_chart[$row['get_year']]['menu_count'] = $row['menu_count'];
                            }

                            if (isset($result_chart[$row['get_year']]['item_count'])) {
                                $result_chart[$row['get_year']]['item_count'] += $row['item_count'];
                            }
                            else {
                                $result_chart[$row['get_year']]['item_count'] = $row['item_count'];
                            }

                        }
                        else {
                            $result_chart[$row['get_month']]['get_month'] = $row['get_month'];
                            if (isset($result_chart[$row['get_month']]['menu_count'])) {
                                $result_chart[$row['get_month']]['menu_count'] += $row['menu_count'];
                            }
                            else {
                                $result_chart[$row['get_month']]['menu_count'] = $row['menu_count'];
                            }

                            if (isset($result_chart[$row['get_month']]['item_count'])) {
                                $result_chart[$row['get_month']]['item_count'] += $row['item_count'];
                            }
                            else {
                                $result_chart[$row['get_month']]['item_count'] = $row['item_count'];
                            }

                        }
                    }
                    else {
                        $result_chart[$row['get_date']]['get_date'] = $row['get_date'];

                        if (isset($result_chart[$row['get_date']]['menu_count'])) {
                            $result_chart[$row['get_date']]['menu_count'] += $row['menu_count'];
                        }
                        else {
                            $result_chart[$row['get_date']]['menu_count'] = $row['menu_count'];
                        }

                        if (isset($result_chart[$row['get_date']]['item_count'])) {
                            $result_chart[$row['get_date']]['item_count'] += $row['menu_count'];
                        }
                        else {
                            $result_chart[$row['get_date']]['item_count'] = $row['item_count'];
                        }


                    }

                    // get top 10 peak hours
                    $result['get_hours'][$row['get_hours']][$row['outlet_id']]['outlet_id']   = $row['outlet_id'];
                    $result['get_hours'][$row['get_hours']][$row['outlet_id']]['outlet_name'] = $row['outlet_name'];
                    $result['get_hours'][$row['get_hours']][$row['outlet_id']]['store_id']    = $row['store_id'];
                    $result['get_hours'][$row['get_hours']][$row['outlet_id']]['store_name']  = $row['store_name'];
                    $result['get_hours'][$row['get_hours']][$row['outlet_id']]['menu_name']  = $row['menu_name'];
                    $result['get_hours'][$row['get_hours']][$row['outlet_id']]['menu_price']  = $row['menu_price'];

                    if (isset($result['get_hours'][$row['get_hours']][$row['outlet_id']]['item_count'])) {
                        $result['get_hours'][$row['get_hours']][$row['outlet_id']]['item_count'] += $row['item_count'];
                    }
                    else {
                        $result['get_hours'][$row['get_hours']][$row['outlet_id']]['item_count'] = $row['item_count'];
                    }

                    if (isset($result['get_hours'][$row['get_hours']][$row['outlet_id']]['menu_count'])) {
                        $result['get_hours'][$row['get_hours']][$row['outlet_id']]['menu_count'] += $row['menu_count'];
                    }
                    else {
                        $result['get_hours'][$row['get_hours']][$row['outlet_id']]['menu_count'] = $row['menu_count'];
                    }


                    // get top 10 peak day
                    $result['get_date'][$row['get_date']][$row['outlet_id']]['outlet_id']   = $row['outlet_id'];
                    $result['get_date'][$row['get_date']][$row['outlet_id']]['outlet_name'] = $row['outlet_name'];
                    $result['get_date'][$row['get_date']][$row['outlet_id']]['store_id']    = $row['store_id'];
                    $result['get_date'][$row['get_date']][$row['outlet_id']]['store_name']  = $row['store_name'];
                    $result['get_date'][$row['get_date']][$row['outlet_id']]['menu_name']  = $row['menu_name'];
                    $result['get_date'][$row['get_date']][$row['outlet_id']]['menu_price']  = $row['menu_price'];

                    if (isset($result['get_date'][$row['get_date']][$row['outlet_id']]['item_count'])) {
                        $result['get_date'][$row['get_date']][$row['outlet_id']]['item_count'] += $row['item_count'];
                    }
                    else {
                        $result['get_date'][$row['get_date']][$row['outlet_id']]['item_count'] = $row['item_count'];
                    }

                    if (isset($result['get_date'][$row['get_date']][$row['outlet_id']]['menu_count'])) {
                        $result['get_date'][$row['get_date']][$row['outlet_id']]['menu_count'] += $row['menu_count'];
                    }
                    else {
                        $result['get_date'][$row['get_date']][$row['outlet_id']]['menu_count'] = $row['menu_count'];
                    }


                    // get top 10 peak month
                    $result['get_month'][$row['get_month']][$row['outlet_id']]['outlet_id']   = $row['outlet_id'];
                    $result['get_month'][$row['get_month']][$row['outlet_id']]['outlet_name'] = $row['outlet_name'];
                    $result['get_month'][$row['get_month']][$row['outlet_id']]['store_id']    = $row['store_id'];
                    $result['get_month'][$row['get_month']][$row['outlet_id']]['store_name']  = $row['store_name'];
                    $result['get_month'][$row['get_month']][$row['outlet_id']]['menu_name']  = $row['menu_name'];
                    $result['get_month'][$row['get_month']][$row['outlet_id']]['menu_price']  = $row['menu_price'];

                    if (isset($result['get_month'][$row['get_month']][$row['outlet_id']]['item_count'])) {
                        $result['get_month'][$row['get_month']][$row['outlet_id']]['item_count'] += $row['item_count'];
                    }
                    else {
                        $result['get_month'][$row['get_month']][$row['outlet_id']]['item_count'] = $row['item_count'];
                    }

                    if (isset($result['get_month'][$row['get_month']][$row['outlet_id']]['menu_count'])) {
                        $result['get_month'][$row['get_month']][$row['outlet_id']]['menu_count'] += $row['menu_count'];
                    }
                    else {
                        $result['get_month'][$row['get_month']][$row['outlet_id']]['menu_count'] = $row['menu_count'];
                    }


                }

                $result_chart = array_values($result_chart);
            }

            $hours_array = array();
            $day_array   = array();
            $month_array = array();
            if ($result) {
                foreach ($result as $key => $res) {
                    if ($key == 'get_hours') {
                        foreach ($res as $jam => $h) {
                            foreach ($h as $detail) {
                                $detail['get_hours'] = $jam;
                                $hours_array[]       = $detail;
                            }
                        }
                    }
                    elseif ($key == 'get_date') {
                        foreach ($res as $jam => $h) {
                            foreach ($h as $detail) {
                                $detail['get_date'] = $jam;
                                $day_array[]        = $detail;
                            }
                        }
                    }
                    elseif ($key == 'get_month') {
                        foreach ($res as $jam => $h) {
                            foreach ($h as $detail) {
                                $detail['get_month'] = $jam;
                                $month_array[]       = $detail;
                            }
                        }
                    }
                }

                $this->load->helper(array('arrays'));

                $content = '<h4>Top 10 Peak Hour</h4>
                    <table class="table table-striped table-bordered table-hover dt-responsive" id="sales-table">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Jam</th>
                            <th>Resto</th>
                            <th>Outlet</th>
                            <th>Menu</th>
                            <th>Jumlah Terjual</th>
                            <th>Harga</th>
                        </tr>
                        </thead><tbody>';

                // sorting by Gross Profit
                usort($hours_array, make_compare( ['menu_count', SORT_DESC]));
                for ($i = 0; $i < count($hours_array); $i++) {
                    if ($i < 10) {
                        $content .= '<tr>';
                        $content .= '<td>' . ($i + 1) . '</td>';
                        $content .= '<td>' . $hours_array[$i]['get_hours'] . '.00</td>';
                        $content .= '<td>' . $hours_array[$i]['store_name'] . '</td>';
                        $content .= '<td>' . $hours_array[$i]['outlet_name'] . '</td>';
                        $content .= '<td>' . $hours_array[$i]['menu_name'] . '</td>';
                        $content .= '<td>' . ceil($hours_array[$i]['menu_count']) . '</td>';
                        $content .= '<td>Rp ' . number_format($hours_array[$i]['menu_price'], 0, "", ".") . '</td>';
                        $content .= '</tr>';
                    }
                    else {
                        break;
                    }
                }
                $content .= '</tbody>
                    </table>';

                $content .= '<h4>Top 10 Peak Day</h4>
                    <table class="table table-striped table-bordered table-hover dt-responsive" id="sales-table">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Jam</th>
                            <th>Resto</th>
                            <th>Outlet</th>
                            <th>Menu</th>
                            <th>Jumlah Terjual</th>
                            <th>Harga</th>
                        </tr>
                        </thead><tbody>';

                // sorting by Gross Profit
                usort($day_array, make_compare(['menu_count', SORT_DESC]));
                for ($i = 0; $i < count($day_array); $i++) {
                    if ($i < 10) {

                        $tanggal = convert_indonesia_date($day_array[$i]['get_date']);
                        $content .= '<tr>';
                        $content .= '<td>' . ($i + 1) . '</td>';
                        $content .= '<td>' . $tanggal . '</td>';
                        $content .= '<td>' . $day_array[$i]['store_name'] . '</td>';
                        $content .= '<td>' . $day_array[$i]['outlet_name'] . '</td>';
                        $content .= '<td>' . $day_array[$i]['menu_name'] . '</td>';
                        $content .= '<td>' . ceil($day_array[$i]['menu_count']) . '</td>';
                        $content .= '<td>Rp ' . number_format($day_array[$i]['menu_price'], 0, "", ".") . '</td>';
                        $content .= '</tr>';
                    }
                    else {
                        break;
                    }
                }
                $content .= '</tbody>
                    </table>';

                $content .= '<h4>Top 10 Peak Month</h4>
                    <table class="table table-striped table-bordered table-hover dt-responsive" id="sales-table">
                        <thead>
                        <tr>
                           <th>No</th>
                            <th>Jam</th>
                            <th>Resto</th>
                            <th>Outlet</th>
                            <th>Menu</th>
                            <th>Jumlah Terjual</th>
                            <th>Harga</th>
                        </tr>
                        </thead><tbody>';

                // sorting by Gross Profit
                usort($month_array, make_compare(['menu_count', SORT_DESC]));
                for ($i = 0; $i < count($month_array); $i++) {
                    if ($i < 10) {
                        $content .= '<tr>';
                        $content .= '<td>' . ($i + 1) . '</td>';
                        $content .= '<td>' . convert_indonesia_month($month_array[$i]['get_month']) . '</td>';
                        $content .= '<td>' . $month_array[$i]['store_name'] . '</td>';
                        $content .= '<td>' . $month_array[$i]['outlet_name'] . '</td>';
                        $content .= '<td>' . $month_array[$i]['menu_name'] . '</td>';
                        $content .= '<td>' . ceil($month_array[$i]['menu_count']) . '</td>';
                        $content .= '<td>Rp ' . number_format($month_array[$i]['menu_price'], 0, "", ".") . '</td>';
                        $content .= '</tr>';
                    }
                    else {
                        break;
                    }
                }
                $content .= '</tbody>
                    </table>';

                $ret_data['status']     = true;
                $ret_data['data']       = $content;
                $ret_data['data_chart'] = $result_chart;
                $ret_data['menu_count'] = $res_data;
                $ret_data['param'] = $store.$outlet;
            }
        }
        echo json_encode($ret_data);
    }

}