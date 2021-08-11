<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**1
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 1/1/2015
 * Time: 2:59 PM
 */
class table extends Table_Controller
{

    /**
     * store enum table status
     * @var array
     */
    private $_enum_table_status = array();
    private $_date;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('order'));
        $this->load->model('order_model');
        $this->load->model('table_model');
        $this->load->model('stock_model');
        $this->load->model('inventory_model');
        $this->load->model('user_model');
        $this->load->model('order_menu_inventory_cogs_model');
        $this->load->model('tax_model');

        $this->data['default_table_width'] = ($this->data['setting']['default_table_width']) ? $this->data['setting']['default_table_width'] . 'px' : '1000px';
        $this->data['default_table_height'] = ($this->data['setting']['default_table_height']) ? $this->data['setting']['default_table_height'] . 'px' : '500px';

        $this->_enum_table_status = $this->table_model->get_enum_table_status();
        $this->_date = date("Y-m-d H:i:s");
        $all_cooking_status = array();
        foreach ($this->order_model->get("enum_cooking_status")->result() as $a) {
            $all_cooking_status[$a->id] = $a->status_name;
        }
        $this->data['all_cooking_status'] = json_encode($all_cooking_status);
        $user = $this->ion_auth->user()->row();
        $this->data['user'] = $user;

    }

    public function index()
    {
        $this->groups_access->check_feature_access('dinein');
        if ($this->data['setting']['dining_type'] == 1 || $this->data['setting']['dining_type'] == 3) {
            $this->casual_dinein();
        } else {
            $this->fast_dinein();
        }
    }

    public function fast_dinein()
    {
        $order_id = $this->session->userdata('order_id_dine_in');
        $this->data['order_is_view'] = 0;
        $this->data['data_order'] = $this->order_model->get_detail_order($order_id);

        $this->data['order_id'] = $order_id;
        if (@$this->data['data_order']->order_status == 1) {
            $this->session->set_userdata('order_id_dine_in', '');
            redirect(base_url('table'));
        }
        if (!empty($this->data['order_id'])) {
            $tax_method = $this->data['setting']['tax_service_method'];
            $get_order_taxes = $this->tax_model->get_taxes(1, $tax_method, 1);
            $order_payment = $this->order_model->calculate_total_order_bill($get_order_taxes, $this->data['order_id']);
            $res = list_order_payment($order_payment);
            $this->data['order_list'] = $res['order_list'];
            $this->data['order_bill'] = $res['order_bill'];
        }
        $this->data['categories'] = $this->cashier_model->get_category_by_store_menu($this->data['data_store']->id);
        $this->data['menus'] = $this->cashier_model->get_menus_by_store($this->data['data_store']->id);
        $this->data['printers_checker_setting'] = $this->order_model->get_by("master_general_setting", "auto_checker", "name");
         
        if ($this->data['setting']['zero_stock_order'] == 0) {
            if ($this->data['setting']['stock_menu_by_inventory'] == 1) {
                $this->order_model->all_menu_ingredient_with_stock($this->data['menus']);
            } else {
                foreach ($this->data['menus'] as $m) {
                    $m->total_available = $m->menu_quantity;
                }
            }
        } else {
            foreach ($this->data['menus'] as $m) {
                $m->total_available = 0;
            }
        }
        $this->data['title'] = "Order Fast Dine In";
        $this->data['theme'] = 'floor-theme';
        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');
        $this->data['content'] .= $this->load->view('table/order_fast_dinein_v', $this->data, true);
        $this->render('table');

    }

    public function casual_dinein()
    {

        $store_id = $this->data['data_store']->id;
        $store_data = $this->store_model->get_one('store', $store_id);
        $logo = ($store_data->store_logo != '') ? $store_data->store_logo : 'assets/img/reskin/default-resto-profile-pic.png';

        //get reserved floor id from session
        $reserved_floor_id = $this->session->userdata('floor_reserved');
        if ($reserved_floor_id != '') {
            $floor_id = $reserved_floor_id;
        } else {
            $get_floor = array_pop($this->cashier_model->get_all_where('floor', array('store_id' => $store_id, 'is_active' => 1)));
            if ($get_floor) {
                $floor_id = $get_floor->id;
            } else {
                $floor_id = 0;
            }
        }
        $this->load->model("reservation_model");
        // $this->reservation_model->update_expire_table();
        $this->data['floor_id'] = $floor_id;
        // $datatable              = $this->store_model->get_table_by_floor_id($store_id, $floor_id);
        $datatable = $this->store_model->get_table_by_floor($store_id, $floor_id);
        if (!(empty($datatable))) {
            $this->data['floor_name'] = $datatable[0]->floor_name;
            // foreach ($datatable as $data_table) {
            // $this->update_reservation_table($data_table);
            // }

            foreach ($datatable as $data_table) {
                $data_table->is_merged = '';
                $data_table->is_parent = '';

                $this->update_reservation_table($data_table);
                $order_id = $this->store_model->get_order_by_table($data_table->table_id);
                $connect_to_reservation = 0;
                if ($order_id) {
                    $connect_to_reservation = ($order_id->reservation_id > 0 ? 1 : 0);
                    $order_id = $order_id->order_id;
                }
                $data_table->connect_to_reservation = $connect_to_reservation;
                $data_table->is_merged = $this->order_model->get_parent_table_merge($data_table->table_id);
                $data_table->is_parent = $this->order_model->get_merge_table_byparent($data_table->table_id);

                if (!empty($data_table->is_merged)) {
                    $order_id = 0;
                }

                if ($order_id) {
                    $data_table->order_id = $order_id;
                    $order_menu_quantity = $this->order_model->get_quantity_cooking_status_order_menu($order_id);
                    //$this->update_status_table($data_table, $order_menu_quantity, $data_table->is_merged);

                    $data_table->status_unavailable = 0;
                    if (isset($order_menu_quantity['6'])) {
                        $data_table->status_unavailable = $order_menu_quantity['6'];
                    }
                } else {
                    $data_table->order_id = 0;
                    $data_table->status_unavailable = 0;
                }
            }
        }
        $all_table = $this->store_model->get_table_all($store_id);
        $this->data['all_table_empty'] = '';
        if (!(empty($all_table))) {
            foreach ($all_table as $all_data_table) {
                if ($all_data_table->table_status == 1) {
                    $this->data['all_table_empty'] += 1;
                } else {
                    $this->data['all_table_empty'] = 0;
                    break;
                }
            }
        }
        $all_floor = $this->store_model->get_floor_by_store($store_id);
        $this->data['data_table'] = $datatable;

        //load content
        $this->data['title'] = "Dine in";
        $this->data['theme'] = 'table-theme';
        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');
        $this->data['floors'] = $all_floor;
        $this->data['store_logo'] = $logo;
        if ($this->data['setting']['theme'] == 1) {
            $this->data['content'] .= $this->load->view('table/table_v', $this->data, true);
        } else {
            $this->data['content'] .= $this->load->view('table/table_v_mode_2', $this->data, true);
        }
        $this->render('table');
    }

    public function updateCleaningProcess()
    {
        $table_id = $this->input->post("table_id");
        $table_status = 1;
        $status_name = $this->_enum_table_status[$table_status];
        $this->table_model->save("table", array("table_status" => $table_status), $table_id);
        $data_table = $this->order_model->get_one('table', $table_id);
        $status_class = create_shape_table($data_table->table_shape, $status_name);
        echo json_encode(array(
            "table_id" => $table_id,
            "table_status" => $table_status,
            "status_name" => $status_name,
            "status_class" => $status_class
        ));

    }

    /**
     * update table into waiting status (yellow) if there is 'New' cooking status in order menu
     * @param  array $data_table [array of table]
     * @param  array $arr_merge array of table merge
     * @param  int $order_id
     *
     * @author fkartika
     */
    public function update_status_table($data_table, $order_menu, $arr_merge, $table_status = '')
    {
        $count_status_new = 0;
        if (isset($order_menu['0'])) {
            $count_status_new = $order_menu['0'];
        }
        $count_status_queue = 0;
        if (isset($order_menu['1'])) {
            $count_status_queue = $order_menu['1'];
        }

        if ($count_status_new > 0 || ($data_table->table_status == 3 && empty($order_menu))) {
            $table_status = 4;
            $status_name = $this->_enum_table_status[$table_status];

            $data_update = array('table_status' => $table_status);
            $this->order_model->save('table', $data_update, $data_table->table_id);

        } elseif ($data_table->table_status == 4 && $count_status_new == 0 && $count_status_queue > 0) {
            $table_status = 3;
            $status_name = $this->_enum_table_status[$table_status];

            $data_update = array('table_status' => $table_status);
            $this->order_model->save('table', $data_update, $data_table->table_id);
        }

        return $table_status;


    }

    /**
     * update table into select status (orange) if if there is a reservation
     * @param  array $data_table [array of table]
     * @author fkartika
     */

    public function update_reservation_table($data_table)
    {
        $this->load->model('reservation_model');

        $reservation_in_table = $this->reservation_model->get_table($data_table->table_id, 1);

        // $checking_table_used=$this->reservation_model->get_all_where("order",array('date(start_order)'=>date("Y-m-d"),'table_id'=>$data_table->table_id,'IFNULL(reservation_id,0)'=>0));
        $data_table->reservation_id = 0;
        if (!empty($reservation_in_table)) {
            $booking_start_lock = $this->store_model->get_general_setting('booking_start_lock')->value;
            $now = now();
            $from_time = strtotime("-" . $booking_start_lock . " minutes", strtotime($reservation_in_table->book_date));
            
            if ($now >= $from_time && ($data_table->table_status == 1 || $data_table->table_status == 6)) {
                $data_table->reservation_id = $reservation_in_table->id;
                $data_table->id = 6;
                $data_table->status_name = 'select';
                $this->reservation_model->save('table', array('table_status' => 6), $data_table->table_id);
            } else if ($data_table->table_status == 6) {
                $data_table->id = 1;
                $data_table->status_name = 'empty';
                $this->reservation_model->save('table', array('table_status' => 1), $data_table->table_id);

            }

        }

    }

    public function change_floor()
    {
        if ($this->input->is_ajax_request()) {
            $store_id = $this->data['data_store']->id;
            $floor_id = $this->input->post('floor_id');
            $destination = $this->input->post('destination');

            if ($destination == 'prev') {
                // get prev floor
                $c_floor = $this->order_model->paged('floor', 1, 0, array('store_id' => $store_id,
                    'id <' => $floor_id), 'id DESC');
            } else if ($destination == 'next') {
                // get next floor
                $c_floor = $this->order_model->paged('floor', 1, 0, array('store_id' => $store_id,
                    'id >' => $floor_id), 'id ASC');
            } else {
                $c_floor = $this->order_model->paged('floor', 1, 0, array('store_id' => $store_id,
                    'id' => $destination));
            }

            if ($c_floor->num_rows() > 0) {
                $floor_data = $c_floor->row();
                $datatable = $this->store_model->get_table_by_floor($store_id, $floor_data->id);
                if (!(empty($datatable))) {

                    $arr_table = '';
                    foreach ($datatable as $table) {

                        $table->is_merged = $this->order_model->get_parent_table_merge($table->table_id);
                        $table->is_parent = $this->order_model->get_merge_tablename_byparent($table->table_id);

                        $this->update_reservation_table($table);

                        $order_id = $this->store_model->get_order_by_table($table->table_id);
                        $connect_to_reservation = 0;
                        if (!empty($table->is_merged)) {
                            $order_id = 0;
                        }

                        if ($order_id) {
                            $new_order_id = $order_id->order_id;
                            $connect_to_reservation = ($order_id->reservation_id > 0 ? 1 : 0);
                            $count_status_unavailable = $this->order_model->get_count_cooking_status_order($order_id->order_id, 6);
                            $table->status_unavailable = $count_status_unavailable->quantity;
                        } else {
                            $new_order_id = 0;
                            $table->status_unavailable = 0;
                        }

                        switch ($table->table_shape) {
                            case "labeledTriangle":
                                $shape = 'dine-in-order label-triangle-' . $table->status_name;
                                break;
                            case "labeledRect":
                                $shape = 'dine-in-order label-rect-' . $table->status_name;
                                break;
                            case "labeledCircle":
                                $shape = 'dine-in-order label-circle-' . $table->status_name;
                                break;
                            default:
                                $shape = 'dine-in-order label-rect-' . $table->status_name;
                        }

                        $merge_badge = '';
                        $custom_data = '';
                        if (!empty($table->is_merged)) {
                            $merge_badge = '<div class="badge-table">' . $table->is_merged->parent_name . '</div>';
                            $custom_data = 'data-parent-id="' . $table->is_merged->parent_id . '" ';
                        } else {
                            $custom_data = 'data-parent-id="0"';
                        }

                        if ($this->data['setting']['theme'] == 1) {
                            $new_style = 'width: ' . $table->width . 'px;';
                            $new_style .= 'height:' . $table->height . 'px;';
                            $new_style .= 'left:' . $table->pos_x . 'px;';
                            $new_style .= 'top:' . $table->pos_y . 'px;';
                            $new_style .= '-ms-transform: rotate(' . $table->rotate . 'deg);';
                            $new_style .= '-webkit-transform: rotate(' . $table->rotate . 'deg);';
                            $new_style .= 'transform: rotate(' . $table->rotate . 'deg);';
                            $new_style .= 'transform-origin: 0% 0%;';

                            $span_style = '-ms-transform: rotate(-' . $table->rotate . 'deg);';
                            $span_style .= '-webkit-transform: rotate(-' . $table->rotate . 'deg);';
                            $span_style .= 'transform: rotate(-' . $table->rotate . 'deg);';
                            $span_style .= 'transform-origin: 50% 50% 0px;';
                        } else {
                            $new_style = 'width: 23.95%;';
                            $new_style .= 'height:50px;';
                        }

                        $custom_data .= 'data-table-id="' . $table->table_id . '" ';
                        $custom_data .= 'data-table-status="' . $table->id . '" ';
                        $custom_data .= 'data-order-id="' . $new_order_id . '" ';
                        $custom_data .= 'data-customer-count="' . $table->customer_count . '" ';
                        $custom_data .= 'data-table-name="' . $table->table_name . '" ';
                        $custom_data .= 'data-reservation-id="' . $table->reservation_id . '" ';
                        $custom_data .= 'data-connect_to_reservation="' . $connect_to_reservation . '" ';
                        if ($table->status_name == "select") {
                            $custom_data .= 'feature_confirmation="' . $this->data['feature_confirmation']['reservation'] . '"';
                        }
                        $arr_table .= '<div id="tab_layout_' . $table->table_id . '" ' . $custom_data . ' class="' . $shape . '" style="' . $new_style . '">';

                        if ($table->status_unavailable > 0 && $table->status_name != 'empty') {
                            $arr_table .= '<div class="warning-table"></div>';
                        }
                        if (!empty($table->is_parent)) {
                            $arr_table .= '<div class="badge-table">' . $table->table_name . '</div>';
                        }

                        $arr_table .= $merge_badge;
                        if ($this->data['setting']['theme'] == 1) {
                            $arr_table .= '<span class="v-middle" style="' . $span_style . '">' . $table->table_name . '</span></div>';
                        } else {
                            $arr_table .= '<span class="v-middle">' . $table->table_name . '</span></div>';
                        }
                    }

                    $data_table_list = '<h3 class="title-list-text">Meja</h3>';
                    $data_table_list .= '<div id="wrap-list-table">';

                    foreach ($datatable as $table) {

                        $table->is_merged = $this->order_model->get_parent_table_merge($table->table_id);
                        $table->is_parent = $this->order_model->get_merge_tablename_byparent($table->table_id);

                        $order_id = $this->store_model->get_order_by_table($table->table_id);
                        $connect_to_reservation = 0;
                        if (!empty($table->is_merged)) {
                            $order_id = 0;
                        }

                        if ($order_id) {
                            $new_order_id = $order_id->order_id;
                            $connect_to_reservation = ($order_id->reservation_id > 0 ? 1 : 0);
                            $count_status_unavailable = $this->order_model->get_count_cooking_status_order($order_id->order_id, 6);
                            $table->status_unavailable = $count_status_unavailable->quantity;
                        } else {
                            $new_order_id = 0;
                            $table->status_unavailable = 0;
                        }

                        $new_style = '';
                        $merge_badge = '';
                        $custom_data = '';
                        if (!empty($table->is_merged)) {

                            $merge_badge = '<div class="badge-table-small">' . $table->is_merged->parent_name . '</div>';
                            $custom_data = 'data-parent-id="' . $table->is_merged->parent_id . '" ';

                        } else {
                            $custom_data = 'data-parent-id="0"';
                        }

                        $custom_data .= 'data-table-id="' . $table->table_id . '" ';
                        $custom_data .= 'data-table-status="' . $table->id . '" ';
                        $custom_data .= 'data-order-id="' . $new_order_id . '" ';
                        $custom_data .= 'data-customer-count="' . $table->customer_count . '" ';
                        $custom_data .= 'data-reservation-id="' . $table->reservation_id . '" ';
                        $custom_data .= 'data-connect_to_reservation="' . $connect_to_reservation . '" ';
                        $list_style = '';

                        //$list_style .= 'left:' . ($table->pos_x)/3 . 'px;';
                        //$list_style .= 'top:' . ($table->pos_y)/3 . 'px;';

                        $shape = 'table-list-text label-rect-' . $table->status_name;
                        $data_table_list .= '<a href="#" ' . $custom_data . ' class="' . $shape . '" style="' . $list_style . '">' . $table->table_name;
                        if (!empty($table->is_parent)) {
                            $data_table_list .= '<div class="badge-table-small">' . $table->table_name . '</div>';
                        }

                        $data_table_list .= $merge_badge;

                        $data_table_list .= '</a>';
                    }
                    $data_table_list .= '</div>';


                    $return_data['status'] = TRUE;
                    $return_data['floor_name'] = $floor_data->floor_name;
                    $return_data['floor_id'] = $floor_data->id;
                    $return_data['data_table'] = $arr_table;
                    $return_data['data_table_list'] = $data_table_list;

                } else {
                    $return_data['status'] = TRUE;
                    $return_data['floor_name'] = $floor_data->floor_name;
                    $return_data['floor_id'] = $floor_data->id;
                    $return_data['data_table'] = '<p style="text-align: center;font-size: 25px;font-weight: bold">' . $this->lang->line('ds_table_empty') . '</p>';
                    $return_data['data_table_list'] = '<h3 class="title-list-text">Meja</h3><p style="text-align: center;font-size: 25px;font-weight: bold">' . $this->lang->line('ds_table_empty') . '</p>';

                }
            } else {
                $return_data['status'] = FALSE;
                $return_data['message'] = $this->lang->line('ds_last_floor');
            }

            echo json_encode($return_data);
        }
    }

    public function change_table()
    {
        if ($this->input->is_ajax_request() && $this->groups_access->have_access('change_table')) {
            $first_table = $this->input->post('first_table');
            $first_order = $this->input->post('first_order');
            $second_table = $this->input->post('second_table');
            $reservation_id = $this->input->post('reservation_id');
            $data_order=$this->order_model->get_one("order",$first_order);
            if(sizeof($data_order)>0){
                $reservation_id=(int)$data_order->reservation_id;
            }
            if (!empty($first_table) && !empty($second_table)) {
                $table1 = $this->order_model->get_one('table', $first_table);
                $table2 = $this->order_model->get_one('table', $second_table);
                if (!empty($table1) && !empty($table2)) {

                    $first_data_update = array('table_status' => $table2->table_status,
                        'customer_count' => $table2->customer_count);
                    $result1 = $this->store_model->update_status_table($first_table, $first_data_update);

                    if ($result1) {

                        $second_data_update = array('table_status' => $table1->table_status,
                            'customer_count' => $table1->customer_count);
                        $result2 = $this->store_model->update_status_table($second_table, $second_data_update);
                        if ($result2) {

                            $data_order_update = array('table_id' => $second_table);
                            // $orders_in_table = $this->order_model->get_all_order_in_table($first_table);
                            $result3 = true;
                            // foreach ($orders_in_table as $key => $row) {
                                $result3 = $this->order_model->save('order', $data_order_update, $first_order);
                            // }

                            if ($result3) {

                                if ($reservation_id != 0) {
                                    $this->store_model->save('reservation', array('table_id' => $second_table), $reservation_id);

                                }

                                // table 1
                                $return_data['table1']['number_guest'] = $table1->customer_count;
                                $return_data['table1']['reservation_id'] = $reservation_id;
                                $return_data['table1']['table_status'] = $table1->table_status;
                                $return_data['table1']['table_id'] = $table2->id;
                                $return_data['table1']['table_name'] = $table2->table_name;
                                $return_data['table1']['order_id'] = $first_order;
                                $return_data['table1']['status_name'] = $this->order_model->get_one('enum_table_status', $table1->table_status)->status_name;

                                switch ($table2->table_shape) {
                                    case "labeledTriangle":
                                        $shape1 = 'dine-in-order label-triangle-' . $return_data['table1']['status_name'];
                                        break;
                                    case "labeledRect":
                                        $shape1 = 'dine-in-order label-rect-' . $return_data['table1']['status_name'];
                                        break;
                                    case "labeledCircle":
                                        $shape1 = 'dine-in-order label-circle-' . $return_data['table1']['status_name'];
                                        break;
                                    default:
                                        $shape1 = 'dine-in-order label-rect-' . $return_data['table1']['status_name'];
                                }

                                $return_data['table1']['status_class'] = $shape1;

                                // table 2
                                $return_data['table2']['number_guest'] = $table2->customer_count;
                                $return_data['table2']['reservation_id'] = 0;
                                $return_data['table2']['table_status'] = $table2->table_status;
                                $return_data['table2']['table_id'] = $table1->id;
                                $return_data['table2']['table_name'] = $table1->table_name;
                                $return_data['table2']['order_id'] = 0;
                                $return_data['table2']['status_name'] = $this->order_model->get_one('enum_table_status', $table2->table_status)->status_name;

                                switch ($table1->table_shape) {
                                    case "labeledTriangle":
                                        $shape2 = 'dine-in-order label-triangle-' . $return_data['table2']['status_name'];
                                        break;
                                    case "labeledRect":
                                        $shape2 = 'dine-in-order label-rect-' . $return_data['table2']['status_name'];
                                        break;
                                    case "labeledCircle":
                                        $shape2 = 'dine-in-order label-circle-' . $return_data['table2']['status_name'];
                                        break;
                                    default:
                                        $shape2 = 'dine-in-order label-rect-' . $return_data['table2']['status_name'];
                                }

                                $return_data['table2']['status_class'] = $shape2;
                                $return_data['status'] = TRUE;

                                $return_data['data']['number_guest'] = $table1->customer_count;
                                $return_data['data']['table_name'] = $table2->table_name;
                                $return_data['data']['table_id'] = $table2->id;
                                $return_data['data']['order_id'] = $first_order;
                            } else {
                                $return_data['status'] = FALSE;
                                $return_data['msg'] = '1';
                            }
                        } else {
                            $return_data['status'] = FALSE;
                            $return_data['msg'] = '2';
                        }
                    } else {
                        $return_data['status'] = FALSE;
                        $return_data['msg'] = $first_data_update;
                    }
                } else {
                    $return_data['status'] = FALSE;
                    $return_data['msg'] = '4';
                }
            } else {
                $return_data['status'] = FALSE;
            }


            echo json_encode($return_data);
        }
    }

    public function new_order()
    {
        // $this->groups_access->check_feature_access('dinein', 'table');
        if ($this->input->is_ajax_request()) {
            $table_id = $this->input->post('table_id');
            $number_guest = $this->input->post('number_guest');
            $reservation_id = $this->input->post('reservation_id');
            $reservation_status = $this->input->post('reservation_status');

            $data_table = $this->order_model->get_one('table', $table_id);

            if (!empty($data_table)) {
                $data_update = array('table_status' => 4, 'customer_count' => $number_guest);
                $this->store_model->update_status_table($table_id, $data_update);
                $check_reservation_menu = $this->order_model->get_all_where("order", array("reservation_id" => $reservation_id, "reservation_id !=" => 0));
                $reservation = $this->order_model->get_one("reservation", $reservation_id);
                if (sizeof($check_reservation_menu) > 0) {
                    $reservation = $check_reservation_menu[0];
                    $order_id = $reservation->id;
                } else {
                    $data_order_where = $this->order_model->get_all_where("order", array("table_id" => $table_id, "order_status" => 0, "start_order <=" => date("Y-m-d H:i:s")));
                    if (empty($data_order_where)) {
                        $data = array('table_id' => $table_id,
                            'is_take_away' => 0,
                            'created_at' => $this->_date,
                            'created_by' => $this->data['user_id'],
                            'has_synchronized' => 0,
                            'reservation_id' => $reservation_id,
                            'customer_name' => (sizeof($reservation) > 0 ? $reservation->customer_name : ""),
                            'start_order' => $this->_date);
                        $order_id = $this->order_model->save_order($data);
                    } else {
                        $order_id = $data_order_where[(sizeof($data_order_where) - 1)]->id;
                    }

                }
                $this->session->set_userdata('order_id_dine_in', $order_id);

                $status_name = $this->order_model->get_one('enum_table_status', 4)->status_name;
                $arr_merge = $this->order_model->get_merge_table_byparent($table_id);
                $this->update_table_merge($arr_merge, $data_update);
                foreach ($arr_merge as $key => $row) {
                    $row->status_class = create_shape_table($row->table_shape, $status_name);
                }

                if (isset($reservation_id) && $reservation_id != 0) {
                    $data_reservation = array('status' => $reservation_status, "status_posting" => 1);
                    $save = $this->order_model->save('reservation', $data_reservation, $reservation_id);

                }

                $return_data['number_guest'] = $number_guest;
                $return_data['table_status'] = 4;
                $return_data['table_name'] = $data_table->table_name;
                $return_data['table_id'] = $table_id;
                $return_data['order_id'] = $order_id;
                $return_data['status_name'] = $status_name;
                $return_data['arr_merge_table'] = $arr_merge;
                $return_data['arr_menu_outlet'] = FALSE;
                $return_data['status_class'] = create_shape_table($data_table->table_shape, $status_name);
                echo json_encode($return_data);
            }
        }
    }


    public function order_dine_in()
    {
        $this->groups_access->check_feature_access('dinein', 'table');

        $edit_id = $this->uri->segment(3);
        if (!empty($edit_id)) {
            $order_id = $edit_id;
            $this->data['order_is_view'] = 1;
        } else {
            $order_id = $this->session->userdata('order_id_dine_in');
            $this->data['order_is_view'] = 0;
        }
        if (empty($order_id)) {
            redirect(base_url('table'));
        }

        $this->data['data_order'] = $this->order_model->get_detail_order($order_id);

        if (empty($this->data['data_order'])) {
            redirect(base_url('table'));
        }

        $this->data['order_id'] = $order_id;
        $this->data['table_data'] = $this->order_model->get_one('table', $this->data['data_order']->table_id);
        $table_merge = $this->table_model->get_table_merge($this->data['table_data']->id);
        $merge_status = count($table_merge) > 0 ? true : false;

        // var_dump($merge_status);die();

        if (empty($this->data['table_data'])) {
            redirect(base_url('table'));
        }
        if ($this->data['data_order']->order_status == 1) {
            redirect(base_url('table'));
        }
        //save reserved floor id
        $this->session->set_userdata('floor_reserved', $this->data['table_data']->floor_id);

        $tax_method = $this->data['setting']['tax_service_method'];
        $get_order_taxes = $this->tax_model->get_taxes(1, $tax_method, 1);
        $order_payment = $this->order_model->calculate_total_order_bill($get_order_taxes, $this->data['order_id']);
        $res = list_order_payment($order_payment);
        $this->data['order_list'] = $res['order_list'];
        $this->data['cooking_time'] = $res['cooking_time'];
        $this->data['order_bill'] = $res['order_bill'];
        $this->data['merge_status'] = $merge_status;
        $this->data['categories'] = $this->cashier_model->get_category_by_store_menu($this->data['data_store']->id);
        $this->data['menus'] = $this->cashier_model->get_menus_by_store($this->data['data_store']->id);

        if ($this->data['setting']['zero_stock_order'] == 0) {
            if ($this->data['setting']['stock_menu_by_inventory'] == 1) {
                $this->order_model->all_menu_ingredient_with_stock($this->data['menus']);
            } else {
                foreach ($this->data['menus'] as $m) {
                    $m->total_available = $m->menu_quantity;
                }
            }
        } else {
            foreach ($this->data['menus'] as $m) {
                $m->total_available = 0;
            }
        }
        $already_process = $this->order_model->paged('order_menu', 1, 0, array('order_id' => $order_id,
            'process_status' => '1'));

        $this->data['already_process'] = $already_process->num_rows();

        //load content
        $where = array(
            'table_status !=' => '1',
            'table.id !=' => $this->data['data_order']->table_id
        );
        $this->data['list_transfer_table'] = $this->table_model->get_data_all_table($where);
        $this->data['printers_checker'] = $this->table_model->get_printer_checker_dropdown();
        $this->data['use_role_checker'] = $this->table_model->get_by("master_general_setting", "use_role_checker", "name");
        $this->data['printers_checker_setting'] = $this->table_model->get_by("master_general_setting", "auto_checker", "name");
        $this->data['title'] = "Order Dine In";
        $this->data['theme'] = 'floor-theme';
        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');
        if ($this->data['setting']['mobile_mode'] == 1){
        $this->data['content'] .= $this->load->view('table/order_dinein_v_3', $this->data, true);
        }else{
        $this->data['content'] .= $this->load->view('table/order_dinein_v', $this->data, true);
        }
        $this->render('table');

    }


    public function get_menus()
    {
        $this->db->cache_on();
        if ($this->input->is_ajax_request()) {
            $category_id = $this->input->post('category_id');
            $menu_type = $this->input->post('menu_type');
            $menus = $this->cashier_model->get_menus_by_category(array("category_id" => $category_id, "available" => 1));
            // $menus = $this->cashier_model->paged_join('menu', 'category','menu.*, category.outlet_id','menu.category_id = category.id',0, 0, array(($category_id!="" ? 'category_id' : 'category_id !=' ) => $category_id,'available' => 1),'menu_name ASC');

            if (sizeof($menus) > 0) {

                $data_menu = $menus;
                if ($this->data['setting']['zero_stock_order'] == 0) {
                    if ($this->data['setting']['stock_menu_by_inventory'] == 1) {
                        $this->order_model->all_menu_ingredient_with_stock($data_menu);
                    } else {
                        foreach ($data_menu as $m) {
                            $m->total_available = $m->menu_quantity;
                        }
                    }
                } else {
                    foreach ($data_menu as $m) {
                        $m->total_available = 0;
                    }
                }

                $content = '';
                $thumb = 'none';
                $list = 'none';
                if ($menu_type == "thumb") {
                    $thumb = 'block';
                } else {
                    $list = 'block';
                }

                $content .= '<ul class="list-category-text list" id="list-menu-text" style="display:' . $list . '"> ';

                foreach ($data_menu as $menu) {
                    $content .= '<li data-id="' . $menu->id . '" data-name="' . $menu->menu_name . '" data-price="' . $menu->menu_price . '" data-option-count="' . $menu->menu_option_count . '" data-side-dish-count="' . $menu->menu_side_dish_count . '" class="add-order-menu">';
                    $content .= '<span class="left name" style="width:60%;">' . $menu->menu_name . '</span>';
                    $content .= '<span id=""  data-outlet="' . $menu->outlet_id . '" class="right ' . ($this->data['setting']['zero_stock_order'] == 1 ? "hide" : "") . ' total-available-' . $menu->id . '" style="'.($this->data['setting']['zero_stock_order'] == 1 ? "" : "margin-left:50px;").'">' . $menu->total_available . ' porsi</span>';
                    $content .= '<span class="right">Rp ' . number_format($menu->menu_price, 0, "", ".") . '</span>
                    </li>';
                }
                $content .= '</ul>';

                $content .= '<span id="thumb-menu-text" style="display:' . $thumb . '"> ';
                foreach ($data_menu as $menu) {
                    if (!empty($menu->icon_url)) {
                        $image = base_url($menu->icon_url);
                    } else {
                        $image = base_url('assets/img/default-category.jpg');
                    }

                    $content .= '<div data-id="' . $menu->id . '" data-name="' . $menu->menu_name . '" data-price="' . $menu->menu_price . '"  data-option-count="' . $menu->menu_option_count . '" data-side-dish-count="' . $menu->menu_side_dish_count . '" class="menu-order add-order-menu"><img src="' . $image . '" alt="menu"/><p>' . $menu->menu_name . '</p></div>';
                }
                $content .= '</span>';

                $return_data['content'] = $content;
            } else {
                $return_data['content'] = '<h5 style="color:#000;text-align: center">' . $this->lang->line('ds_choose_category') . '</h5>';
            }

            echo json_encode($return_data);


        }
    }

    public function get_menu_accessories()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('options_model');
            $this->load->model('categories_model');
            $menu_id = $this->input->post('menu_id');
            $menu_order_id = $this->input->post('menu_order_id');
            $menu_data = $this->categories_model->get_one_menu($menu_id);
            $order_menu_data = $this->order_model->get_detail_order_menu($menu_order_id);
            $return_data['order_menu_data'] = $order_menu_data;
            if (!empty($menu_id)) {
                $side_dish = $this->order_model->get_side_dish_by_menu($menu_id, $menu_data->is_promo);
                if ($side_dish) {
                    $content = '';
                    foreach ($side_dish as $side) {
                        $checked = '';
                        if ($menu_order_id != '0') {
                            $sideOpt = $this->order_model->get_side_dish_by_order_menu($menu_order_id, $menu_data->is_promo);
                            foreach ($sideOpt as $opt) {
                                if ($opt->side_dish_id == $side->id) {
                                    $checked = 'checked="true"';
                                    break;
                                }
                            }
                        }

                        $content .= '<tr>';
                        $content .= '<td class="checkbox">';
                        $content .= '<label><input type="checkbox" ' . $checked . ' class="chk_dish" value="' . $side->id . '">' . $side->name . ' - Rp' . $side->price . '</label>';
                        $content .= '</td>';
                        $content .= '<input type="hidden" id="price_side_dish" value="' . $side->price . '">';
                        $content .= '<input type="hidden" id="side_dish_id" value="' . $side->id . '">';
                        $content .= '</tr>';

                    }
                    $return_data['side_dish'] = $content;
                } else {
                    $return_data['side_dish'] = '<h5 style="color:#898989">' . $this->lang->line('ds_no_side_dish') . '</h5>';
                }

                $options = $this->options_model->get_options($menu_id);
                if ($options) {
                    $content = '';
                    $i = 0;
                    foreach ($options as $option) {
                        $content .= '<label>' . $option->option_name . '</label>';
                        $content .= '<select class="form-control options option_' . $i . '">';
                        $content .= '<option value="0">' . $this->lang->line('ds_choose_option') . '</option>';
                        if ($option->values) {
                            foreach ($option->values as $value) {
                                $value = (object)$value;

                                $selected = '';
                                if ($menu_order_id != '0') {
                                    $sideOpt = $this->order_model->get_option_by_order_menu($menu_order_id);
                                    // $opts = explode(',', $order_menu_data->options);
                                    foreach ($sideOpt as $opt) {
                                        if ($opt->menu_option_value_id == $value->id) {
                                            $selected = 'selected="true"';
                                            break;
                                        }
                                    }
                                }

                                $content .= '<option ' . $selected . ' value="' . $value->id . '">' . $value->option_value_name . '</option>';
                            }
                        }
                        $content .= '</select>';
                        $i++;
                    }
                    $return_data['options'] = $content;
                } else {
                    $return_data['options'] = '<h5 style="color:#898989">' . $this->lang->line('ds_no_option') . '</h5>';
                }

                echo json_encode($return_data);
            }
        }
    }

    public function save_order_menu()
    {
        if ($this->input->is_ajax_request()) {
            $fast_dinein = $this->input->post('fast_dinein');
            $menu_id = $this->input->post('menu_id');
            $order_id = $this->input->post('order_id');
            $count = $this->input->post('count');
            $option = $this->input->post('option');
            $side_dish = $this->input->post('side_dish');
            $notes = $this->input->post('notes');
            $is_edit = $this->input->post('is_edit');
            $outlet_id = $this->input->post('outlet_id');
            $is_take_away = $this->input->post('is_take_away');
            $is_delivery = $this->input->post('is_delivery');
            $dinein_takeaway = $this->input->post('dinein_takeaway');
            $order_type = 1;
            if ($fast_dinein == 1) {
                $order_type = 1;
            } elseif ($is_take_away == 1) {
                $order_type = 2;
            } elseif ($is_delivery == 1) {
                $order_type = 3;
            }
            $cooking_status = 0;
            $process_status = 0;
            if ($fast_dinein == 1) {
                $order_id = $this->session->userdata('order_id_dine_in');
                $cooking_status = 0;
                $process_status = 0;
                if ((int)$order_id == 0) {
                    $data_fast_order = array(
                        'table_id' => 0,
                        'is_take_away' => 0,
                        'created_at' => date("Y-m-d H:i:s"),
                        'created_by' => $this->data['user_id'],
                        'has_synchronized' => 0,
                        'reservation_id' => 0,
                        'customer_name' => "",
                        'start_order' => date("Y-m-d H:i:s")
                    );
                    $order_id = $this->order_model->save_order($data_fast_order);
                    $this->session->set_userdata('order_id_dine_in', $order_id);
                }
            }
            $return_data['status'] = FALSE;
            $return_data['msg'] = "";

            $ingredient_menu_id = $menu_id;
            if ($is_edit == 'true') {
                $old_data_menu = $this->order_model->get_one('order_menu', $menu_id);
                $ingredient_menu_id = @$old_data_menu->menu_id;
            }
            $check=true;
            if ($this->data['setting']['zero_stock_order'] == 0) {
                if ($this->data['setting']['stock_menu_by_inventory'] == 1) {
                    $menu_ingredient = $this->order_model->one_menu_ingredient_with_stock($ingredient_menu_id);
                    $total_available = $menu_ingredient->total_available;
                } else {
                    $menu_ingredient = $this->order_model->get_one('menu', $menu_id);
                    $total_available = $menu_ingredient->menu_quantity;
                    $menu_ingredient->ingredient = $this->order_model->get_menu_ingredient($menu_id);
                }
                
                if ($count > $total_available) {
                    $result = FALSE;
                    $return_data['status'] = FALSE;
                    $return_data['msg'] = "Gagal menambah pesanan, stok tidak mencukupi. Lakukan refresh halaman.";
                    $check=false;
                }
            }
            if($check==true){
                if ($is_edit == 'true') {
                    $data = array(
                        'quantity' => (@$old_data_menu->quantity + $count),
                        // 'options' => $option,
                        'note' => $notes,
                        'cooking_status' => $cooking_status,
                        'dinein_takeaway' => $dinein_takeaway);
                    $result = $this->order_model->update_order_menu($menu_id, $data);

//                    $check_menu_package = $this->order_model->get_one("menu", $old_data_menu->menu_id);
//                    $check_category_package = $this->order_model->get_one("category", $check_menu_package->category_id);
//                    if ($check_category_package->is_package == 1) {
                        $menu_packages = $this->order_model->get_all_where("menu_promo", array("parent_menu_id" => $old_data_menu->menu_id));
                        foreach ($menu_packages as $m) {
                            $this->order_model->update_where("order_package_menu", array(
                                "quantity" => $m->quantity * (@$old_data_menu->quantity + $count),
                                "cooking_status" => $cooking_status,
                            ), array("order_menu_id" => $menu_id, "menu_id" => $m->package_menu_id));
                        }
//                    }
                } else {
                    $data = array('menu_id' => $menu_id,
                        'order_id' => $order_id,
                        'quantity' => $count,
                        'note' => $notes,
                        'created_at' => $this->_date,
                        'created_by' => $this->data['user_id'],
                        'cooking_status' => $cooking_status,
                        'process_status' => $process_status,
                        'dinein_takeaway' => $dinein_takeaway);
                    $result = $this->order_model->save_order_menu($data);
//                    $check_menu_package = $this->order_model->get_one("menu", $menu_id);
//                    $check_category_package = $this->order_model->get_one("category", $check_menu_package->category_id);
//                    if ($check_category_package->is_package == 1) {
                        $menu_packages = $this->order_model->get_all_where("menu_promo", array("parent_menu_id" => $menu_id));
                        foreach ($menu_packages as $m) {
                            $this->order_model->save("order_package_menu", array(
                                "order_menu_id" => $result,
                                "menu_id" => $m->package_menu_id,
                                "quantity" => $m->quantity * $count,
                                "cooking_status" => $cooking_status,
                                "process_status" => $process_status,
                                "is_check" => 0
                            ));
                        }
//                    }
                }

                if (!empty($side_dish)) $this->order_model->save_side_dish_order_menu($side_dish, $result);
                $this->order_model->save_option_order_menu($option, $result);

                $store_id = $this->data['data_store']->id;

                /*
                $first = array_pop($menu_ingredient->ingredient);
                if(null != $first->id){
                    foreach ($ingredients as $ingredient) {
                        // Get all stock from stock, order by puchased date
                        // Proceed fifo algorithm to:
                        // 1. Decrease stock
                        // 2. Insert to stock_history
                        // Until quantity of required stock for menu is 0
                        // Condition for update stock: inventory_id, $outlet_id
                        $cond = array(
                            'inventory_id'  => $ingredient->inventory_id,
                            'uom_id'        => $ingredient->uom_id,
                            'outlet_id'     => $outlet_id
                        );
                        $stocks = $this->stock_model->get_stock($cond);
                        $counter = 0;
                        $value = $count * $ingredient->quantity;
                        while ($value > 0 && !empty($stocks)) {
                            $old_value = $value;
                            if(isset($stocks[$counter]))
                            {
                              $value -= $stocks[$counter]->quantity;
                              $inventory = $this->inventory_model->get_inventory_by_id($stocks[$counter]->inventory_id, $outlet_id);
                              $inventory = array_pop($inventory);
                              $quantity = ($value < 0) ? $value+$stocks[$counter]->quantity : $stocks[$counter]->quantity;

                              $pcs_price = ($stocks[$counter]->price!=0 && $stocks[$counter]->quantity!=0 ? $stocks[$counter]->price/$stocks[$counter]->quantity : 0);
                              $stock_history = array();
                              $stock_history['store_id']      = $store_id;
                              $stock_history['outlet_id']     = $outlet_id;
                              $stock_history['inventory_id']  = $stocks[$counter]->inventory_id;
                              $stock_history['uom_id']  = $stocks[$counter]->uom_id;
                              $stock_history['quantity']      = $quantity*-1;
                              $stock_history['price']         = $quantity*$pcs_price;
                              $stock_history['status']        = 1;
                              $stock_history['purchase_date'] = $stocks[$counter]->purchase_date;
                              $this->stock_model->insert_stock_history($stock_history);
                              $cogs = array(
                                  'order_menu_id'             => $result,
                                  'inventory_id'              => $stocks[$counter]->inventory_id,
                                  'uom_id'              => $stocks[$counter]->uom_id,
                                  'inventory_name'            => $inventory->name,
                                  'quantity'                  => $quantity,
                                  'cogs'                      => $pcs_price*$quantity,
                                  'inventory_purchase_date'   => $stocks[$counter]->purchase_date,
                                  'created_at'                => date("Y-m-d H:i:s")
                              );
                              $this->order_menu_inventory_cogs_model->add($cogs);

                              if($value >= 0)
                                  if($this->data['setting']['zero_stock_order']==1){
                                    $remaining_quantity = $stocks[$counter]->quantity-$quantity;
                                    $data = array(
                                        'quantity'  => $remaining_quantity,
                                        'price'     => $remaining_quantity*$pcs_price
                                    );
                                    $this->stock_model->update_stock_by_id($stocks[$counter]->id, $data);
                                  }else{
                                    $this->stock_model->delete_stock_by_id(array('id' => $stocks[$counter]->id));
                                  }
                              else{
                                  $remaining_quantity = $stocks[$counter]->quantity-$quantity;
                                  $data = array(
                                      'quantity'  => $remaining_quantity,
                                      'price'     => $remaining_quantity*$pcs_price
                                  );
                                  $this->stock_model->update_stock_by_id($stocks[$counter]->id, $data);
                              }
                            }else{
                              $value=-1;
                              break;
                            }

                            $counter++;
                        }
                    }
                }  */
            }

            if ($result) {
                $menu_outlet = $this->order_model->get_menu_outlet($outlet_id);
                if ($this->data['setting']['zero_stock_order'] == 0) {
                    if ($this->data['setting']['stock_menu_by_inventory'] == 1) {
                        $this->order_model->all_menu_ingredient_with_stock($menu_outlet);
                    } else {
                        foreach ($menu_outlet as $m) {
                            $m->total_available = $m->menu_quantity;
                        }
                    }
                } else {
                    foreach ($menu_outlet as $m) {
                        $m->total_available = 0;
                    }
                }

                $tax_method = $this->data['setting']['tax_service_method'];
                $get_order_taxes = $this->tax_model->get_taxes($order_type, $tax_method, 1);
                $order_payment = $this->order_model->calculate_total_order_bill($get_order_taxes, $order_id);
                $res = list_order_payment($order_payment);

                if (!($is_take_away) && !($is_delivery)) {
                    $table = $this->order_model->get_table_by_order_id($order_id);
                    $status_name = $this->order_model->get_one('enum_table_status', 4)->status_name;
                    if (sizeof($table) > 0) {
                        $data_update = array('table_status' => 4);
                        $this->store_model->update_status_table($table->id, $data_update);


                        $arr_merge = $this->order_model->get_merge_table_byparent($table->id);
                        $this->update_table_merge($arr_merge, $data_update);
                        foreach ($arr_merge as $key => $row) {
                            $row->status_class = create_shape_table($row->table_shape, $status_name);
                        }
                        $return_data['arr_merge_table'] = $arr_merge;
                        $return_data['table_status'] = 4;
                        $return_data['status_name'] = $status_name;
                        $return_data['table_id'] = $table->id;
                        $return_data['table_name'] = $table->table_name;
                        $shape = create_shape_table($table->table_shape, $return_data['status_name']);

                        $return_data['status_class'] = $shape;
                    } else {
                        $return_data['arr_merge_table'] = array();
                        $return_data['table_status'] = 4;
                        $return_data['status_name'] = $status_name;
                        $return_data['table_id'] = 0;
                        $return_data['table_name'] = "";
                    }

                }


                $return_data['order_id'] = $order_id;
                $return_data['arr_menu_outlet'] = $menu_outlet;
                $return_data['order_list'] = $res['order_list'];
                $return_data['order_bill'] = $res['order_bill'];
                $return_data['status'] = TRUE;
                $return_data['count'] = ($this->data['setting']['zero_stock_order'] == 0 ? $menu_ingredient->ingredient : 0);

            }
            echo json_encode($return_data);
        }
    }


    public function void_order_menu($is_refund = false, $data = array())
    {
        $return_data['status'] = FALSE;
        $return_data['msg'] = "Gagal mengambil data.";
        if ($this->input->is_ajax_request()) {
            if ($is_refund) {
                $order_menu_id = $data['order_menu_id'];
                $order_id = $data['order_id'];
                $table_id = $data['table_id'];
                $count = $data['count'];
                $is_decrease_stock = $data['is_decrease_stock'];
                $note = $data['note'];
                $user_unlock_void = $data['user_unlock_void'];
            } else {
                $order_menu_id = $this->input->post('order_menu_id');
                $order_id = $this->input->post('order_id');
                $table_id = $this->input->post('table_id');
                $count = $this->input->post('count');
                $is_decrease_stock = $this->input->post('is_decrease_stock');
                $note = $this->input->post('input_void_note');
                $user_unlock_void = $this->input->post('user_unlock_void');

                $this->form_validation->set_rules('input_void_note', 'Alasan void', 'required|max_length[300]');
            }   
            $order_data_list = $this->order_model->kitchen_order($order_id, FALSE, FALSE, $order_menu_id);
            $order_type = 1;

            if (!empty($order_menu_id) && !empty($order_id) && !empty($count) && !empty($is_decrease_stock)
                && !empty($order_data_list)
            ) {
                if (!$is_refund) {
                    if ($this->form_validation->run() != true) {
                        $return_data['msg'] = validation_errors();
                        echo json_encode($return_data);
                        return;
                    }
                }

                $order_data = $order_data_list[0];
                if ($order_data->is_delivery == 1) {
                    $order_type = 3;
                } elseif ($order_data->is_take_away == 1) {
                    $order_type = 2;
                }

                $outlet_id = $this->order_model->get_outlet_by_menu_id($order_data->menu_id);
                $outlets = array();
                if (!in_array($outlet_id, $outlets)) array_push($outlets, $outlet_id);
                $order_menu = $this->order_model->get_one("order_menu", $order_menu_id);
                
                $result = $this->order_model->update_order_menu($order_menu_id, array('quantity' => $order_data->base_quantity - $count));
                $menu_packages = $this->order_model->get_all_where("menu_promo", array("parent_menu_id" => $order_menu->menu_id));
                foreach ($menu_packages as $m) {
                    $this->order_model->update_where("order_package_menu", array(
                        "quantity" => $m->quantity * ($order_data->base_quantity - $count)
                    ), array("order_menu_id" => $order_menu_id, "menu_id" => $m->package_menu_id));
                }
                
                if ($result) {
                    $data = array();
                    $data['setting'] = $this->data['setting'];
                    $outlet_ids=array();
                    $order_list_by_outlet=array();
                    foreach($order_data_list as $l){
                        if(!in_array($l->outlet_id,$outlet_ids)){
                            array_push($outlet_ids,$l->outlet_id);
                        }
                        if(!isset($order_list_by_outlet[$l->outlet_id]))$order_list_by_outlet[$l->outlet_id]=array();
                        array_push($order_list_by_outlet[$l->outlet_id],$l);
                        if($l->is_package==1){
                            $l->quantity=($count*$l->package_quantity);
                        }else{
                            $l->quantity=$count;
                        }
                    }
                    $order_data_list[0]->quantity = $count;
                    $data['store_data'] = $this->data['data_store'];
                    $data['order_list'] = $order_data_list;
                    $data['outlet_data'] = $outlet_id->outlet_name;
                    $data['note'] = $note;
                    $data['table_data'] = $this->order_model->get_data_table($order_id);


                    $menu_outlet = array();
                    if (!$is_refund) {
                        if ($is_decrease_stock == "true") {
                            // proses void mengurangi stock (spoiled)
                            $outlet_id = $outlet_id->outlet_id;
                            $store_id = $this->data['data_store']->id;
                            $ingredients = $this->inventory_model->get_menu_ingredient($order_menu->menu_id, $order_id);

                            foreach ($ingredients as $ingredient) {
                                $array = array(
                                    'store_id' => $store_id,
                                    'outlet_id' => $outlet_id,
                                    'inventory_id' => $ingredient->inventory_id,
                                    'uom_id' => $ingredient->uom_id,
                                    'quantity' => ($count * $ingredient->qty_inventory) * -1,
                                    'created_at' => date("Y-m-d H:i:s"),
                                    'purchase_date' => $ingredient->inventory_purchase_date,
                                    'price' => $ingredient->cogs,
                                    'description' => $note,
                                    'status' => 7
                                );
                                $this->order_menu_inventory_cogs_model->save('stock_history', $array);

                                $array = array(
                                    'store_id' => $store_id,
                                    'outlet_id' => $outlet_id,
                                    'inventory_id' => $ingredient->inventory_id,
                                    'uom_id' => $ingredient->uom_id,
                                    'quantity' => ($count * $ingredient->qty_inventory),
                                    'created_at' => date("Y-m-d H:i:s"),
                                    'purchase_date' => $ingredient->inventory_purchase_date,
                                    'price' => $ingredient->cogs,
                                    'description' => $note,
                                    'status' => 1
                                );
                                $this->order_menu_inventory_cogs_model->save('stock_history', $array);
                            }
                                
                            $is_decrease_stock = 1;
                        } else {
                            // proses void tanpa mengurangi stock
                            $outlet_id = $outlet_id->outlet_id;
                            $store_id = $this->data['data_store']->id;
                            if($order_menu){                      
                                $ingredients = $this->inventory_model->get_menu_ingredient($order_menu->menu_id, $order_id);
                                
                                foreach ($ingredients as $ingredient) {
                                    $price = $ingredient->cogs / $ingredient->quantity;
                                    $array = array(
                                        'store_id' => $store_id,
                                        'outlet_id' => $outlet_id,
                                        'inventory_id' => $ingredient->inventory_id,
                                        'uom_id' => $ingredient->uom_id,
                                        'quantity' => $count * $ingredient->qty_inventory,
                                        'created_at' => $ingredient->inventory_purchase_date,
                                        'purchase_date' => $ingredient->inventory_purchase_date,
                                        'price' => $price
                                    );
                                    
                                    $get_stock_outlet = $this->inventory_model->get_all_where('stock', array('outlet_id' => $outlet_id, 'inventory_id' => $ingredient->inventory_id));

                                    if (!empty($get_stock_outlet)) {
                                        if ($this->data['setting']['stock_method'] == "AVERAGE") {
                                            $this->order_menu_inventory_cogs_model->save('stock', $array);
                                            $this->process_save_method_average($array);
                                        }
                                        if ($this->data['setting']['stock_method'] == "FIFO") {
                                            $this->process_save_method_fifo($array);
                                        }

                                        $array = array(
                                            'store_id' => $store_id,
                                            'outlet_id' => $outlet_id,
                                            'inventory_id' => $ingredient->inventory_id,
                                            'uom_id' => $ingredient->uom_id,
                                            'quantity' => $count * $ingredient->qty_inventory,
                                            'created_at' => date("Y-m-d H:i:s"),
                                            'purchase_date' => $ingredient->inventory_purchase_date,
                                            'price' => $price,
                                            'description' => $note,
                                            'status' => 8
                                        );
                                        $this->order_menu_inventory_cogs_model->save('stock_history', $array);

                                        if ($ingredient->quantity - $count > 0) {
                                            $this->order_menu_inventory_cogs_model->save("order_menu_inventory_cogs", array("quantity" => $ingredient->quantity - $count, "cogs" => $price * $count), $ingredient->id);
                                        } else {
                                            $this->order_menu_inventory_cogs_model->delete_where(array("id" => $ingredient->id));
                                        }
                                    }
                                }
                            }
                            $menu_outlet = $this->order_model->get_menu_outlet($outlet_id);
                            if ($this->data['setting']['zero_stock_order'] == 0) {
                                $this->order_model->all_menu_ingredient_with_stock($menu_outlet);
                            } else {
                                foreach ($menu_outlet as $m) {
                                    $m->total_available = 0;
                                }
                            }
                            $is_decrease_stock = 0;
                        }
                    }
                        
                    // exit;
                    $data_void = array(
                        'order_id' => $order_id,
                        'order_menu_id' => $order_data->menu_id,
                        'amount' => $count,
                        'void_note' => $note,
                        'is_deduct_stock' => $is_decrease_stock,
                        'created_by' => $this->data['user_id'],
                        'created_at' => $this->_date,
                        'user_unlock_id' => $user_unlock_void
                    );
                    $this->cashier_model->save('void', $data_void);

                    if ($count == $order_data->base_quantity) {
                        $result = $this->order_model->delete_order_menu($order_menu_id);
                        $this->order_model->delete_by_limit("order_package_menu", array("order_menu_id" => $order_data->id), 0);
                    }

                    $table_data = $this->order_model->get_data_table($order_id);
                    $tax_method = $this->data['setting']['tax_service_method'];
                    $get_order_taxes = $this->tax_model->get_taxes($order_type, $tax_method, 1);
                    $order_payment = $this->order_model->calculate_total_order_bill($get_order_taxes, $order_id);
                    $res = list_order_payment($order_payment);
                    $arr_merge = $this->order_model->get_merge_table_byparent($table_id);

                    $order_menu_quantity = $this->order_model->get_quantity_cooking_status_order_menu($order_id);
                    $table_status = "";
                    $status_name = "";
                    if (sizeof($table_data) > 0 && $table_data->table_id != 0) {
                        $table_status = $table_data->table_status;
                        $table_status = $this->update_status_table($table_data, $order_menu_quantity, $arr_merge, $table_status);
                        $status_name = $this->_enum_table_status[$table_data->table_status];
                    }

                    $data_update = array('table_status' => $table_status);
                    $this->update_table_merge($arr_merge, $data_update);
                    foreach ($arr_merge as $key => $row) {
                        $row->status_class = create_shape_table($row->table_shape, $status_name);
                    }

                    $return_data['order_list'] = $res['order_list'];
                    $return_data['order_bill'] = $res['order_bill'];
                    $return_data['arr_menu_outlet'] = $menu_outlet;
                    $return_data['arr_merge_table'] = $arr_merge;
                    $return_data['table_status'] = $table_status;
                    $return_data['table_id'] = $table_id;
                    $return_data['table_name'] = $table_data->table_name;
                    $return_data['status_name'] = $status_name;
                    $return_data['status_class'] = create_shape_table($table_data->table_shape, $status_name);
                    $return_data['status'] = TRUE;
                    $return_data['data'] = $this->input->post();
                    $return_data['outlets'] = $outlets;
                    $this->load->helper(array('printer'));

                    //get printer kitchen
                    $this->load->model("setting_printer_model");
                    $printer_arr_obj = $this->setting_printer_model->get_printer_by_enum_printer_type(array("name_type"=>"printer_kitchen"));
                    foreach ($printer_arr_obj as $value) {
                        //build object printer setting for printer helper
                        $printer_setting = new stdClass();
                        $printer_setting->id = $value->id;
                        $printer_setting->name = $value->alias_name;
                        $printer_setting->value = $value->name_printer;
                        $printer_setting->default = $value->logo;
                        $printer_setting->description = $value->printer_width;

                        if (isset($order_list_by_outlet[$value->outlet_id]) && sizeof($order_list_by_outlet[$value->outlet_id])>0) {
                            $data['order_list']=$order_list_by_outlet[$value->outlet_id];
                            $data['outlet_data']=@$this->order_model->get_one("outlet",$value->outlet_id)->outlet_name;
                            if ($value->printer_width == 'generic') {
                                @print_order_kitchen_generic($value->name_printer, $data, $this->data['user'], TRUE, $printer_setting); //true for void type
                            } else {
                                @print_order_kitchen_helper($value->name_printer, $data, $this->data['user'], TRUE, $printer_setting); //true for void type
                            }
                            
                        }                       
                    }
                }
            }

        }
        echo json_encode($return_data);

    }

    public function refund_void()
    {
        $return_data['status'] = FALSE;
        $return_data['msg'] = "Gagal mengambil data.";
        if ($this->input->is_ajax_request()) {
            $order_id = $this->input->post('order_id');
            $data_refund = $this->input->post('data_refund');

            $data_order = $this->order_model->get_one('order', $order_id);
            $data_void = json_decode($data_refund);

            if ($data_order->order_status != 1) {
                foreach ($data_void as $void) {
                    $data = array(
                        'order_menu_id' => $void->product_id,
                        'order_id' => $order_id,
                        'table_id' => $data_order->table_id,
                        'count' => $void->product_amount,
                        'is_decrease_stock' => "false",
                        'note' => "refund",
                        'user_unlock_void' => $this->data['user_profile_data']->id
                    );
                    $this->void_order_menu(true, $data);
                }
            }
                
            $return_data['status'] = TRUE;
            $return_data['msg'] = "";
        }
        echo json_encode($return_data);
    }

    // function for process save stock with FIFO method
    // parameter : data_inventory
    // created by : tri
    public function process_save_method_fifo($data_inventory)
    {
        // Insert stock
        $array = array(
            'store_id' => $data_inventory['store_id'],
            'outlet_id' => $data_inventory['outlet_id'],
            'inventory_id' => $data_inventory['inventory_id'],
            'uom_id' => $data_inventory['uom_id'],
            'quantity' => $data_inventory['quantity'],
            'created_at' => $data_inventory['created_at'],
            'purchase_date' => $data_inventory['purchase_date'],
            'price' => $data_inventory['price']
        );
        $this->stock_model->save('stock', $array);
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
        if ($total_price != 0 && $total_quantity != 0) $average_price = $total_price / $total_quantity;

        // Insert new stock with new quantity and average price 
        $array = array(
            'store_id' => $data_inventory['store_id'],
            'outlet_id' => $data_inventory['outlet_id'],
            'inventory_id' => $data_inventory['inventory_id'],
            'uom_id' => $data_inventory['uom_id'],
            'quantity' => $total_quantity,
            'created_at' => $data_inventory['created_at'],
            'purchase_date' => $data_inventory['purchase_date'],
            'price' => $average_price,
            'modified_at' => date('Y-m-d H:i:s')
        );
        $this->stock_model->save('stock', $array);        
    }

    public function reset_dine_in()
    {
        if ($this->input->is_ajax_request()) {
            $order_id = $this->input->post('order_id');
            $is_processed = 0;
            $arr_merge = array();
            $check_status = array();
            $order_data = $this->order_model->get_one('order', $order_id);
            $table_data = $this->order_model->get_one('table', $order_data->table_id);

            if ($order_data) {

                $order_data_status_new = $this->order_model->paged('order_menu', 0, 0, array('order_id' => $order_id,
                    'cooking_status' => '0'))->result();
                
                $tax_method = $this->data['setting']['tax_service_method'];
                $get_order_taxes = $this->tax_model->get_taxes(1, $tax_method, 1);
                $check_status = $this->order_model->calculate_total_order_bill($get_order_taxes, $order_id, TRUE);


                // $check_status = $this->order_model->check_reset_cooking_status($order_id);
                // if ($is_processed == 0 || $table_data->table_status == 5) {
                if (empty($check_status['order_list'])) {

                    foreach ($order_data_status_new as $key => $row) {
                        $menu_ingredient = new stdclass();
                        $menu_ingredient = $this->order_model->one_menu_ingredient_with_stock($row->menu_id);
                        $this->order_model->increase_inventory_stock($menu_ingredient->ingredient, $row->quantity);

                        $this->delete_order_menu_by_orderid($row->id, $row->order_id, $row->quantity);
                    }

                    // reset table
                    $data_save = array('customer_count' => 0,
                        'table_status' => 1);
                    $this->cashier_model->save('table', $data_save, $order_data->table_id);

                    $status_name = $this->order_model->get_one('enum_table_status', 1)->status_name;

                    $arr_merge = $this->order_model->get_merge_table_byparent($order_data->table_id);
                    $this->update_table_merge($arr_merge, $data_save);
                    foreach ($arr_merge as $key => $row) {
                        $row->status_class = create_shape_table($row->table_shape, $status_name);
                    }

                    $table = $this->order_model->get_one('table', $order_data->table_id);

                    $return_data['number_guest'] = $table->customer_count;
                    $return_data['table_status'] = $table->table_status;
                    $return_data['table_id'] = $order_data->table_id;
                    $return_data['table_name'] = $table->table_name;
                    $return_data['order_id'] = $order_id;
                    $return_data['status_name'] = $status_name;
                    $return_data['arr_merge_table'] = $arr_merge;

                    switch ($table->table_shape) {
                        case "labeledTriangle":
                            $shape = 'dine-in-order label-triangle-' . $return_data['status_name'];
                            break;
                        case "labeledRect":
                            $shape = 'dine-in-order label-rect-' . $return_data['status_name'];
                            break;
                        case "labeledCircle":
                            $shape = 'dine-in-order label-circle-' . $return_data['status_name'];
                            break;
                        default:
                            $shape = 'dine-in-order label-rect-' . $return_data['status_name'];
                    }

                    $this->order_model->delete_by_limit('table_merge', array('parent_id' => $order_data->table_id), 0);
                    $return_data['status_class'] = $shape;
                    $return_data['url_redir'] = base_url('table');
                    $return_data['status'] = true;
                } else {
                    $return_data['status'] = false;
                }

                if (empty($order_data->end_order)) {

                    $already_process = $this->order_model->paged('order_menu', 1, 0, array('order_id' => $order_id,
                        'process_status' => '1'));

                    $is_processed = $already_process->num_rows();


                    // if ($is_processed == 0 || $table_data->table_status == 5 ) {
                    if (empty($check_status['order_list'])) {
                        //delete order menu
                        $this->cashier_model->delete_by_limit('order_menu', array('order_id' => $order_id), 0);
                        $this->cashier_model->delete_by_limit('order', array('id' => $order_id), 0);
                    }
                } else {
                    $data_save_old = array('end_order' => date("Y-m-d H:i:s"));
                    $this->cashier_model->save('order', $data_save_old, $order_id);

                }
                echo json_encode($return_data);
            } else {
                echo json_encode(0);
            }
        }
    }

    public function pending_bill()
    {
        if ($this->input->is_ajax_request()) {
            $order_id = $this->input->post('order_id');
            $data_order = $this->order_model->get_one('order', $order_id);

            if (!empty($data_order)) {
                // get bill
                $tax_method = $this->data['setting']['tax_service_method'];
                $get_order_taxes = $this->tax_model->get_taxes(1, $tax_method, 1);
                $order_payment = $this->order_model->calculate_total_order_bill($get_order_taxes, $order_id, TRUE);
                $order_payment['store_data'] = $this->data['data_store'];
                $order_payment['payment_method'] = '';

                //print struck
                $this->load->helper(array('printer'));                

                $order_payment['setting'] = $this->data['setting'];

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

                    print_checkout_bill($printer_location, $order_payment, $this->data['user_profile_data'], TRUE, TRUE, $printer_setting);
                }
                

                $return_data['status'] = TRUE;
                echo json_encode($return_data);
            } else {
                $return_data['status'] = FALSE;
                echo json_encode($return_data);
            }


        }
    }

    public function update_printer_checker_default(){
        if ($this->input->is_ajax_request()) {            
            $printer_checker_selected = $this->input->post('printer_checker_selected');
            $this->session->set_userdata('printer_checker_selected', $printer_checker_selected);

            // if ($printer_checker_selected) {
                // $data_array = array('default' => $printer_checker_selected);
                // $save = $this->order_model->save_by('master_general_setting', $data_array, 'auto_checker', 'name');
                
                // echo json_encode($save);
            // } else {
            //     echo json_encode(0);
            // }
        }
    }

    public function process_order()
    {
        
        if ($this->input->is_ajax_request()) {
            $order_id = $this->input->post('order_id');

            $order_proc = $this->order_model->kitchen_prn($order_id);
            
            $data_order = $this->order_model->get_one('order', $order_id);

            if (!empty($data_order)) {
                $customer_name = $this->input->post('customer_name'); 
                $customer_phone = $this->input->post('customer_phone'); 
                $data_customers = array('customer_name' => $customer_name,'customer_phone' => $customer_phone);
                $update_data_order = $this->cashier_model->save('order', $data_customers, $order_id);

                $data_save = array('table_status' => 3);
                if ($data_order->table_id != 0) {
                    $result = $this->order_model->save('table', $data_save, $data_order->table_id);
                } else {
                    $result = true;
                }
                if ($this->data['setting']['dining_type'] == 2) {
                    $data_update = array(
                        'process_status' => 1,
                        'cooking_status' => 3
                    );
                } else {
                    if ($this->data['setting']["use_kitchen"] == 1) {
                        $data_update = array(
                            'process_status' => 1,
                            'cooking_status' => 1
                        );
                    } else {
                        $data_update = array(
                            'process_status' => 1,
                            'cooking_status' => ($this->data['setting']['use_role_checker'] == 1 ? 7 : 3)
                        );
                    }
                }

                $data_update['created_at'] = date("Y-m-d H:i:s");
                $order_menu_new = $this->order_model->get_order_menu(array('om.order_id' => $order_id, 'om.cooking_status' => 0), ($this->data['setting']['dining_type'] == 3) ? TRUE : FALSE);
                $return_data['status_menu'] = [];

                foreach ($order_menu_new as $o) {

                    if ($this->data['setting']['stock_menu_by_inventory'] != 1) {
                        $data_menu = $this->order_model->get_one('menu', $o->menu_id);
                        if (!empty($data_menu)) {
                            $this->order_model->save('menu', array('menu_quantity' => $data_menu->menu_quantity - $o->quantity), $o->menu_id);
                        }
                    }

                    // mengecek menu side dish dan turunan inventory
                    // created by: tri
                    // created at: 26/08/2016

                    $ingredients = $this->order_model->get_all_menu_ingredients($o->menu_id);
                    foreach ($ingredients as $i) {
                        $this->process_inventory($o, $i);
                    }

                    $side_dish = $this->order_model->get_all_side_dish_ingredients($o->menu_id);
                    if (!empty($side_dish)) {
                        foreach ($side_dish as $s) {
                            // proses inventory untuk menu side dish
                            $this->process_inventory($o, $s);
                        }
                    }

                    if ($o->is_instant == 1) {
                        $cooking_status = $this->order_model->get_one("enum_cooking_status", 3);
                        $return_data['status_menu'][] = array("id" => $o->id, "cooking_status_name" => $cooking_status->status_name, "cooking_status" => 3);
                        $this->order_model->update_where('order_menu', array(
                            'process_status' => 1,
                            'cooking_status' => 3
                        ), array('id' => $o->id));
                        $this->order_model->update_where("order_package_menu", array(
                            'process_status' => 1,
                            'cooking_status' => 3
                        ), array("order_menu_id" => $o->id));
                    } else {
                        if ($data_update['cooking_status'] == 7 && $o->process_checker == 0) {
                            $cooking_status = $this->order_model->get_one("enum_cooking_status", 3);
                            $return_data['status_menu'][] = array("id" => $o->id, "cooking_status_name" => $cooking_status->status_name, "cooking_status" => 3);
                            $this->order_model->update_where('order_menu', array(
                                'process_status' => 1,
                                'cooking_status' => 3
                            ), array('id' => $o->id));
                            $this->order_model->update_where("order_package_menu", array(
                                'process_status' => 1,
                                'cooking_status' => 3
                            ), array("order_menu_id" => $o->id));
                        } else {
                            $cooking_status = $this->order_model->get_one("enum_cooking_status", $data_update['cooking_status']);
                            $return_data['status_menu'][] = array("id" => $o->id, "cooking_status_name" => $cooking_status->status_name, "cooking_status" => $data_update['cooking_status']);
                        }
                    }

                    if ($this->data['setting']['dining_type'] == 3) {
                        $this->order_model->update_where('order_menu', $data_update, array('order_id' => $order_id, 'cooking_status' => 0, 'id' => $o->id));
                    }
                }

                $new_orders = array();
                if ($this->data['setting']["use_kitchen"] == 1) {
                    $new_orders = $this->order_model->get_new_order_for_notification(array('order_id' => $order_id, 'cooking_status' => 0, 'process_status' => 0, 'is_instant' => 0));
                }
                $outlets = array();
                $order_menu_remain = $this->order_model->get_all_where('order_menu', array('order_id' => $order_id, 'cooking_status' => 0));
                if ($this->data['setting']['dining_type'] != 3) {
                   $this->order_model->update_where('order_menu', $data_update, array('order_id' => $order_id, 'cooking_status' => 0));
                }
                foreach ($order_menu_remain as $remain) {
                    $data_update_package_menu = $data_update;
                    unset($data_update_package_menu['created_at']);
                    $this->order_model->update_where('order_package_menu', $data_update_package_menu, array('order_menu_id' => $remain->id, 'cooking_status' => 0));
                }

                $data_order = $this->order_model->get_data_table($order_id);
                $return_data['notification'] = array();
                foreach ($new_orders as $o) {
                    if (!in_array($o->outlet_id, $outlets)) array_push($outlets, (int)$o->outlet_id);
                    $order_package_menus = $this->order_model->get_all_where("order_package_menu", array("order_menu_id" => $o->id));
                    foreach ($order_package_menus as $p) {
                        $package_menu = $this->order_model->get_one("menu", $p->menu_id);
                        $order_package_menu_category = $this->order_model->get_one("category", $package_menu->category_id);
                        if (!in_array($order_package_menu_category->outlet_id, $outlets)) array_push($outlets, (int)$order_package_menu_category->outlet_id);
                    }
                    if ($this->data['setting']["notification"] == 1) {
                        $msg = "Pesanan baru menu " . $o->menu_name . " untuk meja " . $data_order->table_name;
                        $all_user = $this->user_model->get_online_all_kitchen(array("outlet_id" => $o->outlet_id));
                        foreach ($all_user as $key => $row) {
                            $data = array(
                                'from_user' => $this->data['user_profile_data']->id,
                                'to_user' => $row->id,
                                'message' => $msg,
                                'seen' => 0,
                                'date' => date("Y-m-d H:i:s")
                            );
                            $notif_id = $this->order_model->save('notification', $data);
                            array_push($return_data['notification'], array(
                                'to_user' => $row->id,
                                'notif_id' => $notif_id,
                                'msg' => $msg
                            ));
                        }
                    }
                }
                $return_data['outlets'] = $outlets;

                if ($result) {
                    if ($this->data['setting']['dining_type'] == 1) {
                        $this->session->set_userdata('order_id_dine_in', '');
                    }
                    $arr_merge = $this->order_model->get_merge_table_byparent($data_order->table_id);
                    $this->update_table_merge($arr_merge, $data_save);
                    foreach ($arr_merge as $key => $row) {
                        $row->status_class = create_shape_table($row->table_shape, $data_order->status_name);
                    }
                    $return_data['arr_merge_table'] = $arr_merge;
                    $return_data['number_guest'] = $data_order->customer_count;
                    $return_data['table_status'] = ($this->data['setting']["use_kitchen"] == 1 ? 3 : 2);
                    $return_data['floor_id'] = $data_order->floor_id;
                    $return_data['table_id'] = $data_order->table_id;
                    $return_data['table_name'] = $data_order->table_name;
                    $return_data['order_id'] = $order_id;
                    $return_data['status_name'] = $data_order->status_name;

                    $return_data['status_class'] = create_shape_table($data_order->table_shape, $data_order->status_name);
                    if ($this->data['setting']['dining_type'] == 2) {
                        $return_data['url_redir'] = base_url('cashier/checkout/' . $order_id);
                    } else {
                        $return_data['url_redir'] = base_url('table');
                    }

                    // get bill
                    $order_payment = array();
                    if(!empty($customer_name)){
                        $order_payment['customer_data'] = $customer_name; 
                    }else{
                        $order_payment['table_data'] = $data_order;    
                    }
                    $order_payment['setting'] = $this->data['setting'];
                    $order_payment['store_data'] = $this->data['data_store'];
                    

                    $this->load->helper(array('printer'));
                    $is_print = false;
                    if ($this->data['setting']['auto_print'] == 1) {
                        $is_print = true;
                    }
                    $outlet_id_data = $this->order_model->get_outlet_id_by_order_id($order_id);
                    $order_list = $this->order_model->kitchen_order($order_id, TRUE);        
                    $order_payment['order_list'] = $order_list;
                    if (sizeof($order_list) > 0 && $this->data['setting']['auto_checker'] == 1) {
                        $order_payment['DP'] = "DAFTAR PESANAN";

                        if ($order_proc[0]['kitten'] > 0){
                            $order_payment['DPT'] = "TAMBAHAN";
                        }

                        //get printer checker/service
                        $this->load->model("setting_printer_model");
                        $printer_arr_obj = $this->setting_printer_model->get_printer_by_enum_printer_type(array("name_type"=>"printer_checker_service"));
                        foreach ($printer_arr_obj as $value) {
                            //get all tables in setting printer
                            $printer_tables = $this->order_model->get_all_where("setting_printer_table", array('printer_id'=>$value->id) );
                            foreach ($printer_tables as $key) {
                                //check table in list printer table
                                if ($data_order->table_id == $key->table_id) {
                                    //build object printer setting for printer helper
                                    $printer_setting = new stdClass();
                                    $printer_setting->id = $value->id;
                                    $printer_setting->name = $value->alias_name;
                                    $printer_setting->value = $value->name_printer;
                                    $printer_setting->default = $value->logo;
                                    $printer_setting->description = $value->printer_width;
                                    
                                    //get name alias printer
                                    $order_payment['outlet_data'] = $value->alias_name;

                                    if ($value->printer_width == 'generic') {
                                        @print_order_kitchen_generic($value->name_printer, $order_payment, $this->data['user_profile_data'], false, $printer_setting);
                                    } else {
                                        if ($value->format_order == 1) {
                                            @print_order_kitchen_helper2($value->name_printer, $order_payment, $this->data['user_profile_data'], false, $printer_setting);
                                        } else {
                                            @print_order_kitchen_helper($value->name_printer, $order_payment, $this->data['user_profile_data'], false, $printer_setting);
                                        }
                                    }
                                }
                            }                        
                        }
                    }

					$order_menu_ids=array();
                    if(sizeof($order_list) > 0){
                        foreach ($outlet_id_data as $outlet_id) {
                            $order_payment['order_list'] = $this->order_model->kitchen_order($order_id, TRUE, $outlet_id->outlet_id);
                            if (count($order_payment['order_list']) > 0) {
                                foreach($order_payment['order_list'] as $a){
                                    array_push($order_menu_ids,$a->id);
                                }

                                if ($this->data['setting']['auto_print'] == 1) {
                                    // $order_payment['outlet_data'] = $outlet_id->outlet_name;
                                    $order_payment['DP'] = "DAFTAR PESANAN";

                                    if ($order_proc[0]['kitten'] == 0){                                
                                        $order_payment['DPT'] = " ";
                                    } else {
                                        $order_payment['DPT'] = "TAMBAHAN";
                                    }

                                    //get printer kitchen
                                    $this->load->model("setting_printer_model");
                                    $printer_arr_obj = $this->setting_printer_model->get_printer_by_enum_printer_type(array("name_type"=>"printer_kitchen"));
                                    foreach ($printer_arr_obj as $value) {
                                        //check outlet same with outlet id printer
                                        if ($outlet_id->outlet_id == $value->outlet_id) {
                                            //build object printer setting for printer helper
                                            $printer_setting = new stdClass();
                                            $printer_setting->id = $value->id;
                                            $printer_setting->name = $value->alias_name;
                                            $printer_setting->value = $value->name_printer;
                                            $printer_setting->default = $value->logo;
                                            $printer_setting->description = $value->printer_width;

                                            //get name alias printer
                                            $order_payment['outlet_data'] = $value->alias_name;

                                            if ($value->printer_width == 'generic') {
                                                @print_order_kitchen_generic($value->name_printer, $order_payment, $this->data['user_profile_data'], false, $printer_setting);
                                            } else {
                                                if ($value->format_order == 1) {
                                                    @print_order_kitchen_helper2($value->name_printer, $order_payment, $this->data['user_profile_data'], false, $printer_setting);
                                                } else {
                                                    @print_order_kitchen_helper($value->name_printer, $order_payment, $this->data['user_profile_data'], false, $printer_setting);
                                                }
                                            }
                                        }
                                    }
                                }                                

                                if ($this->data['setting']['auto_checker_kitchen'] == 1) {
                                    //get printer checker kitchen
                                    $this->load->model("setting_printer_model");
                                    $printer_arr_obj = $this->setting_printer_model->get_printer_by_enum_printer_type(array("name_type"=>"printer_checker_kitchen")); 
                                    foreach ($printer_arr_obj as $value) {
                                        //check outlet same with outlet id printer
                                        if ($outlet_id->outlet_id == $value->outlet_id) {
                                            //build object printer setting for printer helper
                                            $printer_setting = new stdClass();
                                            $printer_setting->id = $value->id;
                                            $printer_setting->name = $value->alias_name;
                                            $printer_setting->value = $value->name_printer;
                                            $printer_setting->default = $value->logo;
                                            $printer_setting->description = $value->printer_width;

                                            //get name alias printer
                                            $order_payment['outlet_data'] = $value->alias_name;

                                            if ($value->printer_width == 'generic') {
                                                @print_order_kitchen_generic($value->name_printer, $order_payment, $this->data['user_profile_data'], false, $printer_setting);
                                            } else {
                                                if ($value->format_order == 1) {
                                                    @print_order_kitchen_helper2($value->name_printer, $order_payment, $this->data['user_profile_data'], false, $printer_setting);
                                                } else {
                                                    @print_order_kitchen_helper($value->name_printer, $order_payment, $this->data['user_profile_data'], false, $printer_setting);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
					foreach($order_menu_ids as $order_menu_id){
						$update_array = array('kitchen_status' => '1');
						$this->order_model->save('order_menu', $update_array,$order_menu_id);
					}
                    echo json_encode($return_data);
                } else {
                    echo json_encode(0);
                }
            } else {
                echo json_encode(0);
            }
        }
    }
    
    // function for process inventory
    // parameter : data_inventory and data order menu
    // created by : bening
    public function process_inventory($data_order_menu, $data_inventory){
        $data_stocks = $this->stock_model->get_stock_detail_by_inventory_id(array(
            "store_id" => $this->data['setting']['store_id'],
            "outlet_id" => $data_order_menu->outlet_id,
            "inventory_id" => $data_inventory->inventory_id,
            "uom_id" => $data_inventory->uom_id,
        ));

        $order_remaining = $data_order_menu->quantity * $data_inventory->quantity;

        if ($this->data['setting']['stock_method'] == "FIFO") {
            $this->process_inventory_fifo($data_order_menu, $data_stocks, $order_remaining);                                  
        }

        if ($this->data['setting']['stock_method'] == "AVERAGE") {
            $this->process_inventory_average($data_order_menu, $data_stocks, $order_remaining);
        }
    }

    // function for process inventory with FIFO method
    // parameter : data_inventory, data order menu and quantity order
    // created by : bening
    public function process_inventory_fifo($data_order_menu, $data_stocks, $order_remaining){
        foreach ($data_stocks as $stock) {
            $get_purchase_date = $this->stock_model->get_by('stock', $stock->id);
            // check if quantity order > 0
            // $$order_remaining is counter for quantity order
            if($order_remaining > 0){
                if($stock->quantity > $order_remaining){
                    // if quantity in stock > quantity order
                    // update quantity in stock after deducting quantity order
                    $this->stock_model->save("stock", array("quantity" => $stock->quantity - $order_remaining, 'modified_at' => date('Y-m-d H:i:s')), $stock->id);

                    $data_stock_history = array(
                        "store_id" => $stock->store_id,
                        "outlet_id" => $stock->outlet_id,
                        "quantity" => ($order_remaining * -1),
                        "inventory_id" => $stock->inventory_id,
                        "uom_id" => $stock->uom_id,
                        "price" => $stock->price,
                        "status" => 1,
                        "created_at" => date("Y-m-d H:i:s"),
                        "purchase_date" => $stock->purchase_date
                    );
                    //INSERT STOCK HISTORY
                    $this->stock_model->insert_stock_history($data_stock_history);

                    //INSERT TO ORDER MENU INVENTORY COGS
                    $cogs = array(
                        'order_menu_id' => $data_order_menu->id,
                        'inventory_id' => $stock->inventory_id,
                        'uom_id' => $stock->uom_id,
                        'inventory_name' => $stock->inventory_name,
                        'quantity' => $order_remaining,
                        'cogs' => $stock->price * $order_remaining,
                        'inventory_purchase_date' => $stock->purchase_date,
                        'created_at' => date("Y-m-d H:i:s")
                    );
                    $this->order_menu_inventory_cogs_model->add($cogs);

                    // set quantity order to 0 for terminate looping
                    $order_remaining = 0;

                } elseif ($stock->quantity == $order_remaining) {
                    // if quantity stock same with quantity order
                    // delete stock
                    $this->stock_model->delete("stock", $stock->id);

                    $data_stock_history = array(
                        "store_id" => $stock->store_id,
                        "outlet_id" => $stock->outlet_id,
                        "quantity" => ($order_remaining * -1),
                        "inventory_id" => $stock->inventory_id,
                        "uom_id" => $stock->uom_id,
                        "price" => $stock->price,
                        "status" => 1,
                        "created_at" => date("Y-m-d H:i:s"),
                        "purchase_date" => $stock->purchase_date
                    );
                    //INSERT STOCK HISTORY
                    $this->stock_model->insert_stock_history($data_stock_history);

                    //INSERT TO ORDER MENU INVENTORY COGS
                    $cogs = array(
                        'order_menu_id' => $data_order_menu->id,
                        'inventory_id' => $stock->inventory_id,
                        'uom_id' => $stock->uom_id,
                        'inventory_name' => $stock->inventory_name,
                        'quantity' => $order_remaining,
                        'cogs' => $stock->price * $order_remaining,
                        'inventory_purchase_date' => $stock->purchase_date,
                        'created_at' => date("Y-m-d H:i:s")
                    );
                    $this->order_menu_inventory_cogs_model->add($cogs);

                    // set quantity order to 0 for terminate looping
                    $order_remaining = 0;
                } else {
                    // if quantity stock < quantity order

                    $order_quantity = $stock->quantity;

                    //DELETE STOCK
                    $this->stock_model->delete("stock", $stock->id);

                    $data_stock_history = array(
                        "store_id" => $stock->store_id,
                        "outlet_id" => $stock->outlet_id,
                        "quantity" => ($order_quantity * -1),
                        "inventory_id" => $stock->inventory_id,
                        "uom_id" => $stock->uom_id,
                        "price" => $stock->price,
                        "status" => 1,
                        "created_at" => date("Y-m-d H:i:s"),
                        "purchase_date" => $stock->purchase_date
                    );
                    //INSERT STOCK HISTORY
                    $this->stock_model->insert_stock_history($data_stock_history);

                    //INSERT TO ORDER MENU INVENTORY COGS
                    $cogs = array(
                        'order_menu_id' => $data_order_menu->id,
                        'inventory_id' => $stock->inventory_id,
                        'uom_id' => $stock->uom_id,
                        'inventory_name' => $stock->inventory_name,
                        'quantity' => $order_quantity,
                        'cogs' => $stock->price * $order_quantity,
                        'inventory_purchase_date' => $stock->purchase_date,
                        'created_at' => date("Y-m-d H:i:s")
                    );
                    $this->order_menu_inventory_cogs_model->add($cogs);

                    // set quantity order minus quantity order for next looping
                    $order_remaining -= $order_quantity;
                }
            } else break;       
        }  
        
    }

    // function for process inventory with AVERAGE method
    // parameter : data_inventory, data order menu and quantity order
    // created by : bening
    public function process_inventory_average($data_order_menu, $data_stocks, $order_remaining){
        
        //looping for get new price and quantity
        $total_quantity = 0;
        $id_stock = 0;
        $price_before = 0;
        $cost_sales = 0;
        $qty_after_sales = 0;
        $average_price = 0;
        foreach ($data_stocks as $stock) {
            $total_quantity += $stock->quantity;
            $id_stock = $stock->id;
            $price_before += ($stock->quantity * $stock->price);
            $cost_sales = $order_remaining * $stock->price;
            $qty_after_sales = $total_quantity - $order_remaining;
            $average_price = round((($price_before - $cost_sales) / $qty_after_sales), 0);
        }


        // process save to stock history and update quantity stock 
        if($total_quantity > $order_remaining){
            // update stock quantity (stock quantity - order quantity)
            $this->stock_model->save("stock", array("quantity" => $qty_after_sales, "price" => $average_price, 'modified_at' => date('Y-m-d H:i:s')), $id_stock);

            // insert to stock history
            $array = array(
                'store_id' => $this->data['setting']['store_id'],
                'outlet_id' => $data_order_menu->outlet_id,
                'inventory_id' => $data_stocks[0]->inventory_id,
                'uom_id' => $data_stocks[0]->uom_id,
                'quantity' => -1 * $order_remaining,
                'created_at' => date("Y-m-d H:i:s"),
                'purchase_date' => $stock->purchase_date,
                'price' => $average_price,
                'status' => 1
            );
            $this->stock_model->save('stock_history', $array);

            $cogs = array(
                'order_menu_id' => $data_order_menu->id,
                'inventory_id' => $data_stocks[0]->inventory_id,
                'uom_id' => $data_stocks[0]->uom_id,
                'inventory_name' => $data_stocks[0]->inventory_name,
                'quantity' => $order_remaining,
                'cogs' => $average_price,
                'inventory_purchase_date' => $stock->purchase_date,
                'created_at' => date("Y-m-d H:i:s")
            );
            $this->stock_model->save('order_menu_inventory_cogs', $cogs);
        } 
        
    }

    public function checkout_order()
    {
        if ($this->input->is_ajax_request()) {
            $order_id = $this->input->post('order_id');

            $data_order = $this->order_model->get_one('order', $order_id);

            if (!empty($data_order)) {

                if (!empty($data_order->end_order)) {
                    $return_data['status'] = false;
                    $return_data['message'] = 'Bill telah dibayar.';
                    echo json_encode($return_data);
                } else {
                    $return_data['status'] = true;

                    $order_in_table = $this->order_model->get_all_order_in_table($data_order->table_id);

                    if (sizeof($order_in_table) > 1) {
                        $is_split_order = TRUE;
                        $result = TRUE;

                        $data_save = array('table_status' => 2);
                        $table_status = $this->order_model->get_data_table($order_id);
                        $table_status = $table_status->table_status;
                        $table_status = 2;

                    } else {
                        $table_status = ($this->data['setting']['dining_type'] == 3) ? 3 : 2;
                        $data_save = array('table_status' => $table_status);
                        $is_split_order = FALSE;
                        // $result                = $this->order_model->save('table', $data_save, $data_order->table_id);
                    }
                    if ($data_order->table_id == 0) {
                        $result = true;
                    } else {
                        $result = $this->order_model->save('table', $data_save, $data_order->table_id);
                    }

                    if ($this->data['setting']['dining_type'] != 3) {
                        $data_update = array('process_status' => 1);
                        $this->order_model->update_where('order_menu', $data_update, array('order_id' => $order_id));
                    }   

                    $table = $this->order_model->get_one('table', $data_order->table_id);
                    $status_name = $this->order_model->get_one('enum_table_status', $table_status)->status_name;
                    $arr_merge = array();
                    if ($data_order->table_id != 0) {
                        $arr_merge = $this->order_model->get_merge_table_byparent($data_order->table_id);
                        $this->update_table_merge($arr_merge, $data_save);
                        foreach ($arr_merge as $key => $row) {
                            $row->status_class = create_shape_table($row->table_shape, $status_name);
                        }
                    }
                    if ($result) {
                        if ($data_order->table_id != 0) {
                            $this->session->set_userdata('order_id_dine_in', '');
                        }
                        $count_status_unavailable = $this->order_model->get_count_cooking_status_order($order_id, 6);
                        $return_data['warning_badge'] = false;
                        if (isset($count_status_unavailable->quantity) && $count_status_unavailable->quantity > 0) {
                            $return_data['warning_badge'] = true;
                        }


                        $return_data['arr_merge_table'] = $arr_merge;
                        $return_data['number_guest'] = @$table->customer_count;
                        $return_data['table_status'] = $table_status;
                        $return_data['is_split_order'] = $is_split_order;
                        $return_data['table_id'] = $data_order->table_id;
                        $return_data['table_name'] = @$table->table_name;
                        $return_data['order_id'] = $order_id;
                        $return_data['status_name'] = $status_name;
                        $return_data['status_class'] = create_shape_table(@$table->table_shape, $status_name);

                        $return_data['url_redir'] = base_url('cashier/checkout/' . $order_id);
                        echo json_encode($return_data);
                    } else {
                        echo json_encode(0);
                    }
                }
            } else {
                echo json_encode(0);
            }
        }
    }

    public function select_table()
    {
        if ($this->input->is_ajax_request()) {

            $table_id = $this->input->post('table_id');
            $table_select = $this->input->post('table_select');
            if (!empty($table_id) && !empty($table_select)) {

                $table = $this->order_model->get_one('table', $table_id);
                $return_data['number_guest'] = $table->customer_count;
                $return_data['table_status'] = $table_select;
                $return_data['table_id'] = $table_id;
                $return_data['table_name'] = $table->table_name;
                $return_data['status_name'] = $this->order_model->get_one('enum_table_status', $table_select)->status_name;

                switch ($table->table_shape) {
                    case "labeledTriangle":
                        $shape = 'dine-in-order label-triangle-' . $return_data['status_name'];
                        break;
                    case "labeledRect":
                        $shape = 'dine-in-order label-rect-' . $return_data['status_name'];
                        break;
                    case "labeledCircle":
                        $shape = 'dine-in-order label-circle-' . $return_data['status_name'];
                        break;
                    default:
                        $shape = 'dine-in-order label-rect-' . $return_data['status_name'];
                }

                $return_data['status_class'] = $shape;
                $return_data['url_redir'] = base_url('table');
                $return_data['status'] = true;

                echo json_encode($return_data);
            }
        }
    }

    function get_list_order()
    {
        $order_id = $this->input->post('order_id');
        $tax_method = $this->data['setting']['tax_service_method'];
        $get_order_taxes = $this->tax_model->get_taxes(1, $tax_method, 1);
        $order_payment = $this->order_model->calculate_total_order_bill($get_order_taxes, $order_id);
        $order_list_data = '';
        foreach ($order_payment['order_list'] as $order) {

            $order_list_data .= '<tr>';
            $order_list_data .= $order->menu_name;
            if (sizeof($order->option_list) != 0) {
                foreach ($order->option_list as $opt) {
                    // $order_list_data .= ' <br/>(' . $opt->option_name .' - '. $opt->option_value_name . ')';
                }
            }

            $order_list_data .= '<td style="width:10%">';
            $order_list_data .= '<span class="input-group-btn left">';
            $order_list_data .= '<button type="button" class="btn btn-default btn-number" data-type="minus" data-field="minus-' . $order->order_menu_id . '">';
            $order_list_data .= '<span class="glyphicon glyphicon-minus">';
            $order_list_data .= '<input type="text" name="minus-' . $order->order_menu_id . '" class=" input-number count-order" style="width: 60%;" value="' . $order->quantity . '" min="0" max="' . $order->quantity . '">';
            $order_list_data .= '</span></button></span>';
            $order_list_data .= '</td>';
            $order_list_data .= '<td>' . $order->menu_name . '</td>';
            $order_list_data .= '<td style="width:10%">';

            $order_list_data .= '<span class="input-group-btn">';
            $order_list_data .= '<button type="button" class="btn btn-default btn-number" data-type="plus" data-field="plus-' . $order->order_menu_id . '">';
            $order_list_data .= '<span class="glyphicon glyphicon-plus">';
            $order_list_data .= '<input type="text" name="plus-' . $order->order_menu_id . '" class=" input-number count-split-order" style="width: 60%;" value="0" min="0" max="' . $order->quantity . '">';
            $order_list_data .= '</span></button></span>';
            $order_list_data .= '</td>';

            $order_list_data .= '<td style="display: none">' . $order->menu_id . '</td>';
            $order_list_data .= '<td style="display: none">' . $order->options . '</td>';
            $order_list_data .= '<td style="display: none">' . $order->side_dishes . '</td>';
            $order_list_data .= '<td style="display: none">' . $order->order_menu_id . '</td>';
            $order_list_data .= '<td style="display: none">' . $order->quantity . '</td>';
            $order_list_data .= '</td>';
            // foreach ($order->side_dish_list as $sdh) {
            //     $order_list_data .= '<tr>';
            //     $order_list_data .= '<td></td>';
            //     $order_list_data .= '<td>-- ' . $sdh->name . '</td>';
            //     $order_list_data .= '<td class="border-side tb-align-center">' . $sdh->quantity . '</td>';
            //     $order_list_data .= '<td class="tb-align-right" style="padding-right: 10px">Rp ' . $sdh->price . '</td>';
            //     $order_list_data .= '</tr>';
            // }
            $order_list_data .= '</tr>';

        }
        $return_data['order_list'] = $order_list_data;
        echo json_encode($return_data);

    }


    /**
     * transfer order menu to other table which has order id
     * @return [array] [mixed]
     *
     * @author fkartika
     */
    function transfer_order_menu()
    {
        $return_data['status'] = FALSE;
        $return_data['msg'] = "Gagal transfer.";

        $order_menu_id = $this->input->post('order_menu_id');
        $quantity = $this->input->post('quantity');
        $to_table_id = $this->input->post('to_table_id');
        $transfer_note = $this->input->post('note');

        $this->form_validation->set_rules('order_menu_id', 'order', 'required');
        $this->form_validation->set_rules('quantity', 'jumlah', 'required');
        $this->form_validation->set_rules('from_table_id', 'meja', 'required');
        $this->form_validation->set_rules('to_table_id', 'meja', 'required');
        $this->form_validation->set_rules('note', 'alasan', 'required');

        if ($this->form_validation->run() == TRUE && $this->groups_access->have_access('transfer_order')) {
            $from_menu_data = $this->order_model->get_one('order_menu', $order_menu_id);
            $from_table_data = $this->order_model->get_data_table($from_menu_data->order_id);
            $from_table_id = $from_table_data->table_id;

            $merged_data = $this->order_model->get_parent_table_merge($to_table_id);

            if (!empty($merged_data)) {
                $to_table_data = $this->order_model->get_data_table(0, $merged_data->parent_id);
            } else {
                $to_table_data = $this->order_model->get_data_table(0, $to_table_id);
            }
            if (isset($to_table_data->order_id) && !is_null($to_table_data->order_id)) {

                if ($from_menu_data->quantity - $quantity == 0) {
                    $this->order_model->update_order_menu($from_menu_data->id, array('order_id' => $to_table_data->order_id));
                } else {
                    $data_insert = array(
                        'menu_id' => $from_menu_data->menu_id,
                        'order_id' => $to_table_data->order_id,
                        'quantity' => $quantity,
                        'note' => $from_menu_data->note,
                        'cooking_status' => $from_menu_data->cooking_status,
                        'process_status' => $from_menu_data->process_status,
                        'created_at' => $this->_date,
                        'created_by' => $this->data['user_id'],
                    );
                    $this->order_model->save_order_menu($data_insert);
                    $this->order_model->update_order_menu($from_menu_data->id, array('quantity' => $from_menu_data->quantity - $quantity));
                }

                $data_insert = array(
                    'store_id' => $from_menu_data->menu_id,
                    'waiter_id' => $this->data['user_id'],
                    'quantity' => $quantity,
                    'menu_id' => $from_menu_data->menu_id,
                    'from_table_id' => $from_table_id,
                    'to_table_id' => $to_table_id,
                    'notes' => $transfer_note,
                    'has_synchronized' => 0,
                    'created_at' => $this->_date,
                    'created_by' => $this->data['user_id'],
                );

                $this->order_model->save('transfer_menu_history', $data_insert);

                $tax_method = $this->data['setting']['tax_service_method'];
                $get_order_taxes = $this->tax_model->get_taxes(1, $tax_method, 1);
                $order_payment = $this->order_model->calculate_total_order_bill($get_order_taxes, $from_menu_data->order_id);
                $res = list_order_payment($order_payment);

                $arr_merge = array();
                $order_menu_quantity = $this->order_model->get_quantity_cooking_status_order_menu($from_menu_data->order_id, 0);
                $arr_merge = $this->order_model->get_merge_table_byparent($from_table_id);

                $table_status = $from_table_data->table_status;
                $table_status = $this->update_status_table($from_table_data, $order_menu_quantity, $arr_merge, $table_status);
                $status_name = $this->_enum_table_status[$table_status];

                $data_update = array('table_status' => $table_status);
                $this->update_table_merge($arr_merge, $data_update);
                foreach ($arr_merge as $key => $row) {
                    $row->status_class = create_shape_table($row->table_shape, $status_name);
                }

                $return_data['status'] = TRUE;
                $return_data['order_list'] = $res['order_list'];
                $return_data['order_bill'] = $res['order_bill'];
                $return_data['arr_merge_table'] = $arr_merge;
                $return_data['table_status'] = $table_status;
                $return_data['table_id'] = $from_table_id;
                $return_data['table_name'] = $from_table_data->table_name;
                $return_data['status_name'] = $status_name;
                $return_data['status_class'] = create_shape_table($from_table_data->table_shape, $status_name);

            } else {
                $return_data['msg'] = "Meja tujuan kosong/tidak tersedia.";
            }


        } else {
            $return_data['msg'] = validation_errors();
        }
        echo json_encode($return_data);
    }


    public function back_order()
    {
        if ($this->input->is_ajax_request()) {
            $order_id = $this->input->post('order_id');

            $count_status_new = $this->order_model->get_count_cooking_status_order($order_id, 0);
            $order_menu_data = $this->order_model->get_by('order_menu', $order_id, 'order_id');
            $table = $this->order_model->get_table_by_order_id($order_id);

            $table_status_id = '';
            if ($count_status_new->quantity > 0 && ($table->table_status != 2 || $table->table_status != 5)) {

                $table_status_id = 4;
            } elseif ($table->table_status == 4 && (($count_status_new->quantity == 0) && !empty($order_menu_data))) {
                $table_status_id = 3;
            } elseif ($table->table_status == 3 && empty($order_menu_data)) {
                $table_status_id = 4;
            }

            if ($table_status_id != '') {
                $data_update = array('table_status' => $table_status_id);
                $this->store_model->update_status_table($table->id, $data_update);

                $arr_merge = $this->order_model->get_merge_table_byparent($table->id);
                $status_name = $this->order_model->get_one('enum_table_status', $table_status_id)->status_name;

                $this->update_table_merge($arr_merge, $data_update);
                foreach ($arr_merge as $key => $row) {
                    $row->status_class = create_shape_table($row->table_shape, $status_name);
                }
                $order_in_table = $this->order_model->get_all_order_in_table($table->id);
                if (sizeof($order_in_table) > 0) {
                    $order_id = $order_in_table[0]->id;
                }

                if (sizeof($order_in_table) > 1) {
                    foreach ($order_in_table as $key => $row) {
                        $count_status_unavailable = $this->order_model->get_count_cooking_status_order($row->id, 6);
                        if ($count_status_unavailable->quantity == 0) {
                            continue;
                        }
                    }
                } else {
                    $count_status_unavailable = $this->order_model->get_count_cooking_status_order($order_id, 6);

                }

                $return_data['warning_badge'] = false;
                if (isset($count_status_unavailable->quantity) && $count_status_unavailable->quantity > 0) {
                    $return_data['warning_badge'] = true;
                }

                $return_data['table_status'] = $table_status_id;
                $return_data['table_id'] = $table->id;
                $return_data['order_id'] = $order_id;
                $return_data['status_name'] = $status_name;
                $return_data['arr_merge_table'] = $arr_merge;

                $shape = create_shape_table($table->table_shape, $return_data['status_name']);

                $return_data['status_class'] = $shape;
            }


            $return_data['url_redir'] = base_url('table');
            $return_data['status'] = true;
            $return_data['table_status_id'] = $table_status_id;
            echo json_encode($return_data);

        }

    }

    public function update_cooking_status()
    {
        if ($this->input->is_ajax_request()) {
            $menu_id = $this->input->post('menu_id');
            $order_id = $this->input->post('order_id');
            $cooking_status = $this->input->post('cooking_status');

            $process_status = 1;
            $menu_outlet = array();
            if ($cooking_status == 4 || $cooking_status == 6) {
                $process_status = 0;
                $data_order = $this->order_model->get_one('order_menu', $menu_id);

                $outlet_id = $this->order_model->get_outlet_by_menu_id($data_order->menu_id);
                $outlet_id = $outlet_id->outlet_id;

                $menu_ingredient = new stdclass();
                $menu_ingredient = $this->order_model->one_menu_ingredient_with_stock($data_order->menu_id);
                $this->order_model->increase_inventory_stock($menu_ingredient->ingredient, $data_order->quantity);

                $menu_outlet = $this->order_model->get_menu_outlet($outlet_id);
                $this->order_model->all_menu_ingredient_with_stock($menu_outlet);


            }
            $data = array('cooking_status' => $cooking_status,
                'process_status' => $process_status);

            $result = $this->order_model->update_order_menu($menu_id, $data);
            $return_data['status'] = FALSE;


            if ($result) {
                $tax_method = $this->data['setting']['tax_service_method'];
                $get_order_taxes = $this->tax_model->get_taxes(1, $tax_method, 1);
                $order_payment = $this->order_model->calculate_total_order_bill($get_order_taxes, $order_id);
                $res = list_order_payment($order_payment);

                $data_order = $this->order_model->get_data_table($order_id);

                // $table = $this->order_model->get_one('table', $data_order->table_id);
                if ($this->data['setting']['dining_type'] == 1) {
                    $this->session->set_userdata('order_id_dine_in', '');
                }

                $arr_merge = $this->order_model->get_merge_table_byparent($data_order->table_id);
                // $this->update_table_merge($arr_merge, array('table_status' =>3));

                $return_data['arr_merge_table'] = $arr_merge;
                $return_data['number_guest'] = $data_order->customer_count;
                $return_data['table_status'] = 3;
                $return_data['table_name'] = $data_order->table_name;
                $return_data['table_id'] = $data_order->table_id;
                $return_data['order_id'] = $order_id;
                $return_data['status_name'] = $data_order->status_name;
                $return_data['arr_menu_outlet'] = $menu_outlet;


                switch ($data_order->table_shape) {
                    case "labeledTriangle":
                        $shape = 'dine-in-order label-triangle-' . $return_data['status_name'];
                        break;
                    case "labeledRect":
                        $shape = 'dine-in-order label-rect-' . $return_data['status_name'];
                        break;
                    case "labeledCircle":
                        $shape = 'dine-in-order label-circle-' . $return_data['status_name'];
                        break;
                    default:
                        $shape = 'dine-in-order label-rect-' . $return_data['status_name'];
                }

                $return_data['status_class'] = $shape;

                $return_data['order_list'] = $res['order_list'];
                $return_data['order_bill'] = $res['order_bill'];
                $return_data['status'] = TRUE;
            }

            echo json_encode($return_data);
        }
    }

    function update_table_view()
    {
        if ($this->input->is_ajax_request()) {
            $view_type = $this->input->post('view_type');

            //1 for list
            if ($view_type == 1) {
                $this->session->set_userdata('view_table_list', 'active');
                $this->session->set_userdata('view_table_thumb', '');

            } else {
                $this->session->set_userdata('view_table_thumb', 'active');
                $this->session->set_userdata('view_table_list', '');
            }

            $return_data['status'] = TRUE;


            echo json_encode($return_data);
        }
    }

    /**
     * Merging/combine table
     * merge tables with orders in each table, table with status reservation/6 can't be merge.
     * there is priority to define the color(status) of combined table. Order by DESC
     * @return [type] [description]
     *
     * @author fkartika
     */
    function merge_table()
    {
        if ($this->input->is_ajax_request() && $this->groups_access->have_access('merge_table')) {
            $parent_id = $this->input->post('parent_id');
            $array_table = $this->input->post('array_table');
            $array_order_id = $this->input->post('array_order_id');
            $parent_order_id = $this->input->post('parent_order_id');
            $this->session->set_userdata('floor_reserved', $this->input->post('floor_id'));
            $data_parent = $this->order_model->get_data_table($parent_order_id, $parent_id);
            // echo "<pre>";
            // print_r($_POST);
            // print_r($data_parent);
            // exit;

            if (!empty($data_parent)) {
                $temp_order_id = 0;
                if (!is_null($data_parent->order_id)) {
                    $temp_order_id = $data_parent->order_id;

                }

                //color priority
                // $enum_table_priority= array(
                //     'empty' => 1,
                //     'select' => 2 ,
                //     'reserved' => 3,
                //     'completed' => 4,
                //     'waiting' => 6,
                //     'order' => 5,
                //     );
                $enum_table_priority = array(
                    1 => 1,
                    4 => 6,
                    3 => 5,
                    2 => 4,
                    5 => 3,
                    6 => 2,
                );
                $temp_enum_status = $enum_table_priority[$data_parent->table_status];
                $temp_table_status = $data_parent->table_status;
                $temp_table_status_name = $data_parent->status_name;

                $customer_count = $data_parent->customer_count;
                $customer_name = $data_parent->customer_name;
                $customer_phone = $data_parent->customer_phone;
                $customer_address = $data_parent->customer_address;
                $reservation_id = $data_parent->reservation_id;
                foreach ($array_table as $key => $row) {
                    $data_child = $this->order_model->get_data_table(@$array_order_id[$key], $row);
                    $priority = $enum_table_priority[$data_child->table_status];
                    $source_id = $temp_order_id;


                    if ($priority > $temp_enum_status) {
                        $temp_enum_status = $priority;
                        $temp_table_status = $data_child->table_status;
                        $temp_table_status_name = $data_child->status_name;

                        if (!is_null($data_child->order_id)) {
                            $temp_order_id = $data_child->order_id;
                        }
                    } else {
                        $source_id = $data_child->order_id;

                    }
                    if ((int)$data_child->reservation_id != 0) {
                        $customer_name = $data_child->customer_name;
                        $customer_phone = $data_child->customer_phone;
                        $customer_address = $data_child->customer_address;
                        $reservation_id = $data_child->reservation_id;
                    }
                    if (!is_null($source_id) && $source_id != 0) {
                        $data = array('order_id' => $temp_order_id);
                        $result = $this->order_model->save_by('order_menu', $data, $source_id, 'order_id');
                        $this->order_model->save_by('bill', $data, $source_id, 'order_id');
                        if ($result) {
                            $this->order_model->delete('order', $source_id, 'id');

                        }
                    }

                    $customer_count += $data_child->customer_count;

                    //hapus history merge
                    $this->order_model->delete('table_merge', $row, 'table_id');

                    $data = array(
                        'parent_id' => $parent_id,
                        'table_id' => $row
                    );
                    $this->order_model->save('table_merge', $data);
                }

                $data_save = array(
                    'customer_count' => $customer_count,
                    'table_status' => $temp_table_status);
                $this->order_model->save('table', $data_save, $data_parent->table_id);

                $data_save = array(
                    'customer_count' => 0,
                    'table_status' => $temp_table_status
                );

                $arr_merge = $this->order_model->get_merge_table_byparent($data_parent->table_id);
                $this->update_table_merge($arr_merge, $data_save);

                foreach ($arr_merge as $key => $row) {
                    $row->status_class = create_shape_table($row->table_shape, $temp_table_status_name);
                }

                $data = array(
                    'table_id' => $data_parent->table_id,
                    'customer_name' => $customer_name,
                    'customer_phone' => $customer_phone,
                    'customer_address' => $customer_address,
                    'reservation_id' => $reservation_id,
                );
                $result = $this->order_model->save('order', $data, $temp_order_id);

                $return_data['number_guest'] = $customer_count;
                $return_data['table_status'] = $temp_table_status;
                $return_data['table_id'] = $parent_id;
                $return_data['table_name'] = $data_parent->table_name;
                $return_data['order_id'] = $temp_order_id;
                $return_data['status_name'] = $temp_table_status_name;
                $return_data['arr_merge_table'] = $arr_merge;
                $return_data['arr_menu_outlet'] = FALSE;
                $return_data['status_class'] = create_shape_table($data_parent->table_shape, $temp_table_status_name);

                $return_data['status'] = TRUE;
            } else {

                $return_data['message'] = "Data not found.";
                $return_data['status'] = FALSE;

            }


            echo json_encode($return_data);
        }
    }

    /**
     * Cancel merged table status
     * Order must be transferred manually
     */
    public function cancel_merge($table_id)
    {
        if ($this->input->is_ajax_request() && $this->groups_access->have_access('merge_table')) {
            $tables = $this->table_model->get_table_merge($table_id);
            if ($tables) {
                $first_table = array_pop($tables);
                $return_data['table_id'] = array($first_table->parent_id, $first_table->table_id);
                $parent_table_id = $first_table->parent_id;
                foreach ($tables as $table) {
                    array_push($return_data['table_id'], $table->table_id);
                }

                $arr_merge = $this->order_model->get_merge_table_byparent($first_table->parent_id);
                $data_save = array('table_status' => 1, 'customer_count' => 0);
                $this->update_table_merge($arr_merge, $data_save);

                $status_name = $this->_enum_table_status[1];
                foreach ($arr_merge as $key => $row) {
                    $row->status_class = create_shape_table($row->table_shape, $status_name);
                }
                $parent_id = $first_table->parent_id;
                $status = $this->table_model->delete_table_merge($parent_id);
                $return_data['status'] = $status;
                $return_data['arr_merge_table'] = $arr_merge;
                $return_data['table_id'] = $first_table->parent_id;
                $return_data['status_name'] = $status_name;
            } else {
                $return_data['message'] = "Data not found.";
                $return_data['status'] = FALSE;
            }
            $return_data['url_redir'] = base_url('table');
            echo json_encode($return_data);
        }

    }

    public function get_order_combine()
    {

        $order_id = $this->input->post('order_id');
        // $order_id = 291;
        $return_data['message'] = "Data not found.";
        $return_data['status'] = FALSE;
        $return_data['data'] = "";

        $data_order = $this->order_model->get_order_combine($order_id);
        if (!empty($data_order)) {
            $html = "";
            $pos = 0;
            foreach ($data_order as $key => $order) {
                if ($pos % 2 == 0) {
                    $html .= "<tr>";
                }
                $html .= "<td><label><input type='radio' name='combine-order' value='" . $order->order_id . "'> Meja " . $order->table_name . " (" . $order->order_id . ")</label></td>";
                $pos += 1;

                if ($pos % 2 == 0) {
                    $html .= "</tr>";
                }

            }

            $return_data['status'] = TRUE;
            $return_data['data'] = $html;
        }

        echo json_encode($return_data);
    }


    function get_reservation_table()
    {
        $this->load->model('reservation_model');

        $return_data['status'] = FALSE;
        $return_data['msg'] = "Data error.";

        $table_id = $this->input->post('table_id');
        $data = $this->reservation_model->get_table($table_id, 1);

        if ($data) {
            $msg = ' <a class="col-xs-4 btn-reserv-come" href="javascript:void(0);" data-status="2" reservation_id="' . $data->id . '" >
                    <div class="col-xs-12 btn btn-reservation-menu">
                        <p class="reservation-menu-title">Datang</p>
                        <input type="hidden" id="reserved_customer_count" value="' . $data->customer_count . ' "/>
                        <table class="table-reservation-option">
                            <tbody>
                                <tr>
                                    <td>Nama</td>
                                    <td>:</td>
                                    <td>' . $data->customer_name . '</td>
                                </tr>
                                <tr>
                                    <td>Kontak</td>
                                    <td>:</td>
                                    <td>' . $data->phone . '</td>
                                </tr>
                                <tr>
                                    <td>Jumlah</td>
                                    <td>:</td>
                                    <td>' . $data->customer_count . ' Orang</td>
                                </tr>
                                <tr>
                                    <td>Waktu</td>
                                    <td>:</td>
                                    <td>' . $data->book_date . '</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    </a>

                    
                    <a class="col-xs-4" href="javascript:void(0);"  id="btn-reserv-unlock" data-status="1" data-reservation_id="' . $data->id . '" feature_confirmation="' . ($this->data['feature_confirmation']['reservation']) . '">
                        <div class="col-xs-12 btn btn-reservation-menu" style="padding-top:50px;">
                            <p class="reservation-menu-title">Unlock Reservasi</p>
                            <p>Simpan / Pending Reservasi</p>
                            <p>Isi meja dengan tamu lain</p>
                        </div>
                    </a>

                    <a class="col-xs-4" href="javascript:void(0);"  id="btn-reserv-delete" data-status="3">
                        <div class="col-xs-12 btn btn-reservation-menu" style="padding-top:50px;">
                            <p class="reservation-menu-title">Hapus Reservasi</p>
                            <p>Isi meja dengan tamu lain</p>
                        </div>
                    </a>



                    ';

            $return_data['status'] = TRUE;
            $return_data['reservation_id'] = $data->id;
            $return_data['msg'] = $msg;

        }

                    //<a class="col-xs-3" href="javascript:void(0);" id="btn-reserv-replace" data-status="4">
                    //<div class="col-xs-12 btn btn-reservation-menu" style="padding-top:55px;">
                    //   <p class="reservation-menu-title">Tidak Datang</p>
                    //   <p>Isi meja dengan tamu lain</p>
                    //   </div>
                    // </a>

        echo json_encode($return_data);

    }

    function get_reserved_note()
    {
        $this->load->model('reservation_model');

        $return_data['status'] = FALSE;
        $return_data['msg'] = "Data error.";

        $data = $this->reservation_model->get('template_reservation_note')->result();
        $msg = "";

        // $counter = 0;
        // $checked = "checked";
        // foreach ($data as $key => $row) {
        // if($counter>0){
        // $checked = '';
        // }
        // $msg .= '<div class="form-group">
        // <div class="col-xs-12">
        // <div class="row">
        // <div class="checkbox">
        // <div class="col-xs-10">
        // <label class="radio-inline">
        // <input name="reservation_note_id" type="radio" id="'.$row->id.'" value="'.$row->id.'" ' . $checked . '>'.$row->note.'</label>
        // </div>
        // </div>
        // </div>
        // </div>
        // </div>';
        // $counter++;
        // }

        // $checked = "";
        // if(empty($data)){
        $checked = "checked";
        // }
        $msg .= '            <div class="form-group">
                        <div class="col-xs-12">
                            <div class="row">
                                <div class="checkbox">
                                    <div class="col-xs-12">
                                        <label class="  col-xs-12">
                                            Catatan/Alasan
                                        </label>
                                    </div>
                                     <div class="col-xs-12">
                                        <label class="radio-inline col-xs-12">
                                            <input name="reservation_note_id" id="custom" type="radio" ' . $checked . ' value="custom" style="display:none;">
                                            <textarea class="form-control" style="resize:none" id="custom-note" name="custom-note"></textarea>
                                        </label>
                                    </div>

                                
                                </div>
                            </div>
                        </div>
                    </div>';

        $return_data['msg'] = $msg;
        $return_data['status'] = TRUE;


        echo json_encode($return_data);

    }

    function update_reservation_status()
    {
        $this->load->model('reservation_model');

        $return_data['status'] = FALSE;
        $return_data['msg'] = "Data error.";

        $reservation_id = $this->input->post('reservation_id');
        $status = $this->input->post('status');

        $post_array = array();
        parse_str($this->input->post('note'), $post_array);
        $template_note_id = $post_array['reservation_note_id'];
        if (!empty($reservation_id) && !empty($status)) {

            if ($template_note_id == 'custom' && !empty($post_array['custom-note'])) {

                $failed_note = $post_array['custom-note'];

            } else if ($template_note_id != 'custom') {
                $failed_note = $this->reservation_model->get_by('template_reservation_note', $template_note_id)->note;
            } else {
                $return_data['msg'] = "Catatan tidak boleh kosong";
                echo json_encode($return_data);
                return;
            }

            $data_reservation = array(
                'status' => $status,
                'failed_note' => $failed_note
            );

            $reservation = $this->reservation_model->get_by('reservation', $reservation_id);
            $table_id = $reservation->table_id;
            $save = $this->order_model->save('reservation', $data_reservation, $reservation_id);
            $data_table = $this->reservation_model->get_by('table', $table_id);
            $data_order = $this->reservation_model->get_all_where("order", array("reservation_id" => $reservation_id));
            //if delete reservation, empty table
            if ($status == '3') {
                $table_status = 1;
                if ($reservation->status_posting == 1 && sizeof($data_order) > 0) {
                    $this->reservation_model->save('table', array('table_status' => 1), $table_id);
                    $table_status = 1;
                }
                if (sizeof($data_table) > 0) {
                    $table_status=$data_table->table_status;
                    if ($data_table->table_status == 6) {
                        $this->reservation_model->save('table', array('table_status' => 1), $table_id);
                        $table_status = 1;
                    }
                }
                $return_data['table_status'] = $table_status;
                $return_data['table_id'] = $table_id;
                $return_data['status_name'] = $this->order_model->get_one('enum_table_status', $table_status)->status_name;
                $shape = "";
                if (sizeof($data_table) > 0) {
                    $shape = create_shape_table($data_table->table_shape, $return_data['status_name']);
                }
                $return_data['status_class'] = $shape;
            }

            if ($status == '4') {
                $table_status = $data_table->table_status;
                if (strtotime($reservation->book_date) >= strtotime(date("Y-m-d H:i:s")) && sizeof($data_order) > 0 && $data_order->order_status == 1) {
                    $this->reservation_model->save('table', array('table_status' => 1), $table_id);
                    $table_status = 1;
                }
                $return_data['table_status'] = $table_status;
                $return_data['table_id'] = $table_id;
                $return_data['status_name'] = $this->order_model->get_one('enum_table_status', $table_status)->status_name;
                $shape = "";
                if (sizeof($data_table) > 0) {
                    $shape = create_shape_table($data_table->table_shape, $return_data['status_name']);
                }
                $return_data['status_class'] = $shape;


            }
            if ($status == '3' || $status == '4') {
                $order = $this->store_model->get_all_where("order", array("reservation_id" => $reservation_id));
                if (sizeof($order) > 0) {
                    $order = $order[0];
                    $order_menus = $this->store_model->get_all_where("order_menu", array("order_id" => $order->id));
                    foreach ($order_menus as $o) {
                        $this->store_model->delete_by_limit('order_menu_inventory_cogs', array("order_menu_id" => $o->id), 0);
                        $this->store_model->delete_by_limit('order_menu_option', array("order_menu_id" => $o->id), 0);
                    }
                    $this->store_model->delete_by_limit('order_menu', array("order_id" => $order->id), 0);
                    $this->store_model->delete('order', $order->id);
                }
                $form_data = $this->store_model->get_one("reservation", $reservation_id);
                if ($form_data->down_payment > 0) {
                    $this->load->model("account_model");
                    $account_other_income = $this->account_model->get_data($this->data['setting']['other_income_account_id']);
                    $account_payable = $this->account_model->get_data($this->data['setting']['hutang_dp_account_id']);
                    if (sizeof($account_other_income) > 0 && sizeof($account_payable) > 0) {
                        $account_data = array(
                            'has_synchronized' => 0,
                            'store_id' => $this->data['setting']['store_id'],
                            'account_id' => $account_payable->id,
                            'entry_type' => NULL,
                            'foreign_id' => $reservation_id,
                            'info' => "Cancel Down Payment Reservasi",
                            'debit' => ($account_payable->default_balance == 0 ? $form_data->down_payment : 0),
                            'credit' => ($account_payable->default_balance == 1 ? $form_data->down_payment : 0),
                            'created_at' => date("Y-m-d H:i:s")
                        );
                        $this->account_model->save('account_data', $account_data);
                        $account_data = array(
                            'has_synchronized' => 0,
                            'store_id' => $this->data['setting']['store_id'],
                            'account_id' => $account_other_income->id,
                            'entry_type' => NULL,
                            'foreign_id' => $reservation_id,
                            'info' => "Cancel Down Payment Reservasi",
                            'debit' => ($account_other_income->default_balance == 0 ? $form_data->down_payment : 0),
                            'credit' => ($account_other_income->default_balance == 1 ? $form_data->down_payment : 0),
                            'created_at' => date("Y-m-d H:i:s")
                        );
                        $this->account_model->save('account_data', $account_data);
                    }
                }
            }

            $return_data['msg'] = $post_array['reservation_note_id'];
            $return_data['status'] = TRUE;

        }


        echo json_encode($return_data);


    }

    function check_general_setting()
    {
        $return_data['status'] = FALSE;
        $return_data['msg'] = "Pin dibutuhkan.";

        if ($this->input->post('pin')) {

            $pin = $this->input->post('pin');
            $name = $this->input->post('name');

            $data = $this->store_model->get_user_access($pin, 'void');

            if ($data) {

                $return_data['msg'] = "";
                $return_data['status'] = TRUE;
            } else {
                $return_data['msg'] = "Akses ditolak";
            }

        }

        echo json_encode($return_data);


    }

    public function delete_order_menu()
    {
        if ($this->input->is_ajax_request()) {
            $menu_id = $this->input->post('menu_id');
            $order_id = $this->input->post('order_id');
            $count = $this->input->post('count');

            $order_data = $this->order_model->get_one('order_menu', $menu_id);
            $outlet_id = "";
            if (!empty($order_data)) {
                $outlet_id = $this->order_model->get_outlet_by_menu_id($order_data->menu_id);
            }

            $result = $this->order_model->delete_order_menu($menu_id);
            $this->order_model->delete_by_limit("order_package_menu", array("order_menu_id" => $menu_id), 0);
            $return_data['status'] = FALSE;

            if (!empty($outlet_id)) {
                $menu_outlet = array();
                if ($order_data->cooking_status != 4) {
                    $outlet_id = $outlet_id->outlet_id;
                    $menu_ingredient = new stdclass();
                    $menu_ingredient = $this->order_model->one_menu_ingredient_with_stock($order_data->menu_id);

                    $store_id = $this->data['data_store']->id;
                    $first = array_pop($menu_ingredient->ingredient);
                    $ingredients = $this->order_menu_inventory_cogs_model->get_all(array('order_menu_id' => $menu_id));
                    
                    if (null != $first->id) {
                        foreach ($ingredients as $ingredient) {
                            $this->order_menu_inventory_cogs_model->delete(array('id' => $ingredient->id));
                        }
                    }

                    $menu_outlet = $this->order_model->get_menu_outlet($outlet_id);
                    if ($this->data['setting']['zero_stock_order'] == 0) {
                        $this->order_model->all_menu_ingredient_with_stock($menu_outlet);
                    } else {
                        foreach ($menu_outlet as $m) {
                            $m->total_available = 0;
                        }
                    }
                }

                $tax_method = $this->data['setting']['tax_service_method'];
                $get_order_taxes = $this->tax_model->get_taxes(1, $tax_method, 1);
                $order_payment = $this->order_model->calculate_total_order_bill($get_order_taxes, $order_id);
                $res = list_order_payment($order_payment);
                $return_data['order_list'] = $res['order_list'];
                $return_data['order_bill'] = $res['order_bill'];
                $return_data['arr_menu_outlet'] = $menu_outlet;
                $return_data['status'] = TRUE;
            }

            echo json_encode($return_data);
        }
    }

    function delete_order_menu_by_orderid($menu_id, $order_id, $count)
    {
        $order_data = $this->order_model->get_one('order_menu', $menu_id);
        $outlet_id = $this->order_model->get_outlet_by_menu_id($order_data->menu_id);

        $result = $this->order_model->delete_order_menu($menu_id);
        $return_data['status'] = FALSE;

        if ($result && !empty($outlet_id)) {
            $menu_outlet = array();
            if ($order_data->cooking_status != 4) {
                $outlet_id = $outlet_id->outlet_id;
                $menu_ingredient = new stdclass();
                $menu_ingredient = $this->order_model->one_menu_ingredient_with_stock($order_data->menu_id);

                $store_id = $this->data['data_store']->id;
                $first = array_pop($menu_ingredient->ingredient);
                $ingredients = $this->order_menu_inventory_cogs_model->get_all(array('order_menu_id' => $menu_id));
                // var_dump($ingredients);die();
                if (null != $first->id) {
                    foreach ($ingredients as $ingredient) {
                        $cond = array(
                            'inventory_id' => $ingredient->inventory_id,
                            'outlet_id' => $outlet_id,
                            'store_id' => $store_id
                        );
                        $stocks = $this->stock_model->get_all_where('stock_history', $cond, FALSE, array('purchase_date', 'DESC'));
                        $counter = 0;
                        $value = $count * $ingredient->quantity;
                        while ($value >= 0) {
                            $old_value = $value;
                            $value -= $stocks[$counter]->quantity;
                            $quantity = ($value < 0) ? $value + $stocks[$counter]->quantity : $stocks[$counter]->quantity;

                            $stock_history = array();
                            $stock_history['store_id'] = $store_id;
                            $stock_history['outlet_id'] = $outlet_id;
                            $stock_history['inventory_id'] = $ingredient->inventory_id;
                            $stock_history['quantity'] = $quantity;
                            $stock_history['price'] = $ingredient->cogs;
                            $stock_history['status'] = 1;
                            $stock_history['purchase_date'] = $ingredient->inventory_purchase_date;
                            $this->stock_model->add_stock_history($stock_history);

                            //Delete unnecessary field
                            unset($stock_history['status']);
                            $this->stock_model->add_stock($stock_history);
                            $this->order_menu_inventory_cogs_model->delete(array('id' => $ingredient->id));

                            $counter++;
                        }
                    }
                }

                $menu_outlet = $this->order_model->get_menu_outlet($outlet_id);
                if ($this->data['setting']['zero_stock_order'] == 0) {
                    $this->order_model->all_menu_ingredient_with_stock($menu_outlet);
                } else {
                    foreach ($menu_outlet as $m) {
                        $m->total_available = 0;
                    }
                }
            }

            $tax_method = $this->data['setting']['tax_service_method'];
            $get_order_taxes = $this->tax_model->get_taxes(1, $tax_method, 1);
            $order_payment = $this->order_model->calculate_total_order_bill($get_order_taxes, $order_id);
            $res = list_order_payment($order_payment);
            $return_data['order_list'] = $res['order_list'];
            $return_data['order_bill'] = $res['order_bill'];
            $return_data['arr_menu_outlet'] = $menu_outlet;
            $return_data['status'] = TRUE;
        }


    }

    function unlock_reserved()
    {
        $this->load->model('reservation_model');
        $reservation_id = $this->input->post('reservation_id');
        $reservation = $this->reservation_model->get_by('reservation', $reservation_id);
        $table_id = $reservation->table_id;
        $data_reservation = array('status' => 1, 'table_id' => 0);
        $this->order_model->save('reservation', $data_reservation, $reservation_id);
        $this->reservation_model->save('table', array('table_status' => 1), $table_id);
		if($reservation->order_type==1){
			$data_order=$this->reservation_model->get_all_where("order",array("reservation_id"=>$reservation_id));
			if(sizeof($data_order)>0){
				$this->order_model->save('order', array("table_id"=>0), $data_order[0]->id);
			}			
		}
    }

    function post_to_ready($order_id = 0, $order_type = 0)
    {
        $data_save = array('table_status' => 3);
        $data_order = $this->order_model->get_by_order_id($order_id);
        if (sizeof($data_order) > 0) {
            if ($data_order->table_id != 0) {
                $result = $this->order_model->save('table', $data_save, $data_order->table_id);
            }
            $data_update = array(
                'process_status' => 1,
                'cooking_status' => 3
            );
            $order_menu_updated = $this->order_model->get_all_where('order_menu', array('order_id' => $order_id, 'cooking_status !=' => 3, 'cooking_status !=' => 4, 'cooking_status !=' => 6));
            $this->order_model->update_where('order_menu', $data_update, array('order_id' => $order_id, 'cooking_status !=' => 3, 'cooking_status !=' => 4, 'cooking_status !=' => 6));
            foreach ($order_menu_updated as $o) {
                $this->order_model->update_where('order_package_menu', $data_update, array('order_menu_id' => $o->id, 'cooking_status !=' => 3, 'cooking_status !=' => 4, 'cooking_status !=' => 6));
            }
        }
        if ($order_type == 0) {
            redirect(base_url("table/order_dine_in/" . $order_id));
        } elseif ($order_type == 2) {
            redirect(base_url("cashier/order_takeaway/" . $order_id));
        } else {
            redirect(base_url("cashier/order_delivery/" . $order_id));
        }
    }

    function delete_order()
    {
        if ($this->input->is_ajax_request()) {
            $order_id = $this->input->post("order_id");
            $data_order = $this->order_model->get_by_order_id($order_id);
            if (sizeof($data_order) == 0) {
                echo json_encode(array(
                    "status" => false,
                    "message" => "Data tidak ditemukan!"
                ));
            } else {
                if ($data_order->reservation_id != 0) {
                    echo json_encode(array(
                        "status" => false,
                        "message" => "Data ini terkait dengan reservasi,silahkan hapus melalui menu reservasi!"
                    ));
                } else {
                    if ($data_order->table_id != 0) {
                        $this->order_model->save("table", array(
                            "table_status" => 1
                        ), $data_order->table_id);
                    }
                    $order = $this->order_model->get_one("order", $data_order->id);
                    $order_menu = $this->order_model->get_all_where("order_menu", array("order_id" => $data_order->id));
                    $order_menu_option = array();
                    $order_menu_side_dish = array();
                    $order_menu_inventory_cogs = array();
                    foreach ($order_menu as $om) {
                        $data_menu = $this->order_model->get_outlet_by_menu_id($om->menu_id);
                        $outlet_id = $data_menu->outlet_id;
                        array_push($order_menu_option, $this->order_model->get_all_where("order_menu_option", array("order_menu_id" => $om->id)));
                        array_push($order_menu_side_dish, $this->order_model->get_all_where("order_menu_side_dish", array("order_menu_id" => $om->id)));
                        $inventory_cogs = $this->order_model->get_all_where("order_menu_inventory_cogs", array("order_menu_id" => $om->id));
                        array_push($order_menu_inventory_cogs, $inventory_cogs);
                        $this->order_model->delete_by_limit("order_package_menu", array("order_menu_id" => $om->id), 0);
                        foreach ($inventory_cogs as $ingredient) {
                            $price = $ingredient->cogs / $ingredient->quantity;
                            $cond = array(
                                'inventory_id' => $ingredient->inventory_id,
                                'outlet_id' => $outlet_id,
                                'store_id' => $this->data['setting']['store_id']
                            );
                            $array = array(
                                'store_id' => $this->data['setting']['store_id'],
                                'outlet_id' => $outlet_id,
                                'inventory_id' => $ingredient->inventory_id,
                                'uom_id' => $ingredient->uom_id,
                                'quantity' => $ingredient->quantity,
                                'created_at' => date("Y-m-d H:i:s"),
                                'purchase_date' => date("Y-m-d H:i:s"),
                                'price' => $price
                            );
                            $this->order_menu_inventory_cogs_model->save('stock', $array);
                            $array = array(
                                'store_id' => $this->data['setting']['store_id'],
                                'outlet_id' => $outlet_id,
                                'inventory_id' => $ingredient->inventory_id,
                                'uom_id' => $ingredient->uom_id,
                                'quantity' => $ingredient->quantity,
                                'created_at' => date("Y-m-d H:i:s"),
                                'purchase_date' => date("Y-m-d H:i:s"),
                                'price' => $price,
                                'status' => 1
                            );
                            $this->order_menu_inventory_cogs_model->save('stock_history', $array);
                        }
                    }
                    if ($data_order->table_id != 0) {
                        $arr_merge = $this->order_model->get_merge_table_byparent($data_order->table_id);
                        if (!empty($arr_merge)) {
                            foreach ($arr_merge as $key => $row) {
                                $this->store_model->update_status_table($row->id, array('table_status' => 1));
                            }
                            $this->order_model->delete_by_limit('table_merge', array('parent_id' => $data_order->table_id), 0);
                        }
                    }
                    $this->order_model->delete_order($order_id);
                    $history = array(
                        "order" => $order,
                        "order_menu" => $order_menu,
                        "order_menu_option" => $order_menu_option,
                        "order_menu_side_dish" => $order_menu_side_dish,
                        "order_menu_inventory_cogs" => $order_menu_inventory_cogs,
                    );

                    $this->order_model->save("order_history", array(
                        "created_at" => date("Y-m-d H:i:s"),
                        "created_by" => $this->data['user']->id,
                        "data" => json_encode($history)
                    ));
                    echo json_encode(array(
                        "status" => true
                    ));
                }
            }
        }
    }

    function close_order()
    {
        if ($this->input->is_ajax_request()) {
            $order_id = $this->input->post('order_id');
            $date_now = date("Y-m-d H:i:s");

            $data_order = $this->order_model->get_by_order_id($order_id);
            $data_table = $this->order_model->get_one('table', $data_order->table_id);

            if (sizeof($data_order) == 0) {
                $return_data['status'] = FALSE;
                echo json_encode($return_data);
                return false;
            }

            if ($data_order->order_status == 0) {
                $this->session->set_userdata('order_id_dine_in', '');
                $data_order_save = array('end_order' => $date_now, 'order_status' => 1);
                $return_data['status'] = FALSE;
                if ($data_order->table_id != 0 && $data_table->table_status != 7) {
                    $table_status = 1;
                    if ($this->data['setting']['cleaning_process'] == 1) $table_status = 7;
                    $result_data_order = $this->cashier_model->save('order', $data_order_save, $order_id);
                }
                $data_update = array('table_status' => $table_status);
                $this->cashier_model->save('table', $data_update, $data_order->table_id);
            }

            if (!empty($data_order)) {
                $return_data['status'] = TRUE;
                $status_name = $this->order_model->get_one('enum_table_status', $table_status)->status_name;
                $arr_merge = $this->order_model->get_merge_table_byparent($data_order->table_id);
                $this->update_table_merge($arr_merge, $data_update);
                foreach ($arr_merge as $key => $row) {
                    $row->status_class = create_shape_table($row->table_shape, $status_name);
                }

                $return_data['arr_merge_table'] = $arr_merge;
                $return_data['number_guest'] = $data_table->customer_count;
                $return_data['table_status'] = $table_status;
                $return_data['table_id'] = $data_order->table_id;
                $return_data['status_name'] = $status_name;
                $return_data['order_id'] = $order_id;
                $return_data['status_class'] = create_shape_table($data_table->table_shape, $status_name);
                $return_data['url_redir'] = base_url('table');
                echo json_encode($return_data);
            } else {
                $return_data['status'] = FALSE;
                echo json_encode($return_data);
            }
        }
    }
}