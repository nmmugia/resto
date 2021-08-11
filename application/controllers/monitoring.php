<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Monitoring extends Monitoring_Controller
{
    public $config_pagination = array(
        'full_tag_open' => '<ul class="pagination" style="margin: 0px;">',
        'full_tag_close' => '</ul>',
        'first_tag_open' => '<li>',
        'first_tag_close' => '</li>',
        'first_link' => 'First',
        'last_tag_open' => '<li>',
        'last_tag_close' => '</li>',
        'last_link' => 'Last',
        'next_tag_open' => '<li>',
        'next_tag_close' => '</li>',
        'next_link' => 'Next',
        'prev_tag_open' => '<li>',
        'prev_tag_close' => '</li>',
        'prev_link' => 'Prev',
        'cur_tag_open' => '<li class="active"><a href="javascript:void(0)">',
        'cur_tag_close' => '</a></li>',
        'num_tag_open' => '<li>',
        'num_tag_close' => '</li>'
    );

    function __construct()
    {
        parent::__construct();
        $this->data['data_store'] = $this->store_model->get_store($this->data['user_profile_data']->store_id);
        $this->data['data_outlet'] = $this->store_model->get_outlet($this->data['user_profile_data']->outlet_id);
    }

    function paging($param = array())
    {
        $this->load->library("pagination");
        if (isset($param['first_url'])) $this->config_pagination['first_url'] = $param['first_url'];
        if (isset($param['suffix'])) $this->config_pagination['suffix'] = $param['suffix'];
        $this->config_pagination['base_url'] = $param['base_url'];
        $this->config_pagination['total_rows'] = $param['total_rows'];
        $this->config_pagination['per_page'] = $param['per_page'];
        $this->config_pagination['num_links'] = $param['num_links'];
        $this->config_pagination['uri_segment'] = $param['uri_segment'];
        $this->pagination->initialize($this->config_pagination);
        return $this->pagination->create_links();
    }

    public function index()
    {
        $monitoring_setting = $this->session->userdata("monitoring_setting");
        if ($monitoring_setting == "") $monitoring_setting = array();
        if (!isset($monitoring_setting['start_date'])) $monitoring_setting['start_date'] = date("Y-m-d");
        if (!isset($monitoring_setting['end_date'])) $monitoring_setting['end_date'] = date("Y-m-d");
        $this->load->model("checker_model");
        $this->data['title'] = "Monitoring";
        $this->data['theme'] = 'floor-theme';
        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');
        $this->load->view('header_v');
        $offset = 0;
        $perpage = 12;
        $this->data['data_menu_order'] = $this->checker_model->get_order_menu_reservation($monitoring_setting);
        // echo "<pre>";
        // print_r($this->db->last_query());
        // echo "</pre>";
        
        $this->data['data_without_menu_order'] = $this->checker_model->get_reservation_without_order_menu($monitoring_setting);
        // echo "<pre>";
        // print_r($this->data['data_without_menu_order']);
        // echo "</pre>";
        $pagination = array(
            'base_url' => base_url("monitoring/get_data"),
            'total_rows' => (sizeof($this->data['data_menu_order']) + sizeof($this->data['data_without_menu_order'])),
            'per_page' => $perpage,
            'num_links' => 3,
            'uri_segment' => 3
        );
        $this->data['pagination'] = $this->paging($pagination);
        $this->data['offset'] = $offset;
        $this->data['monitoring_setting'] = $monitoring_setting;
        $this->data['perpage'] = $perpage;
    
        $this->data['list_view'] = $this->load->view('monitoring/list_view', $this->data, true);
        $this->load->view('monitoring/index', $this->data);
    }

    public function get_data($offset = 0)
    {
        $monitoring_setting = array(
            "start_date" => $this->input->get("start_date"),
            "end_date" => $this->input->get("end_date"),
        );
        $this->load->model("checker_model");
        $perpage = 12;
        $this->data['data_without_menu_order'] = $this->checker_model->get_reservation_without_order_menu($monitoring_setting);
        $this->data['data_menu_order'] = $this->checker_model->get_order_menu_reservation($monitoring_setting);
        $pagination = array(
            'base_url' => base_url("monitoring/get_data"),
            'total_rows' => (sizeof($this->data['data_menu_order']) + sizeof($this->data['data_without_menu_order'])),
            'per_page' => $perpage,
            'num_links' => 3,
            'uri_segment' => 3
        );
        $this->data['pagination'] = $this->paging($pagination);
        $this->data['offset'] = $offset;
        $this->data['perpage'] = $perpage;
        $this->load->view("monitoring/list_view", $this->data);
    }

    public function search()
    {
        $monitoring_setting = array(
            "start_date" => $this->input->get("start_date"),
            "end_date" => $this->input->get("end_date"),
        );
        $this->session->set_userdata(array("monitoring_setting" => $monitoring_setting));
        $this->load->model("checker_model");
        $perpage = 12;
        $this->data['data_without_menu_order'] = $this->checker_model->get_reservation_without_order_menu($monitoring_setting);
        $this->data['data_menu_order'] = $this->checker_model->get_order_menu_reservation($monitoring_setting);
        $pagination = array(
            'base_url' => base_url("monitoring/get_data"),
            'total_rows' => (sizeof($this->data['data_menu_order']) + sizeof($this->data['data_without_menu_order'])),
            'per_page' => $perpage,
            'num_links' => 3,
            'uri_segment' => 3
        );
        $this->data['pagination'] = $this->paging($pagination);
        $this->data['offset'] = 0;
        $this->data['perpage'] = $perpage;
        $this->load->view("monitoring/list_view", $this->data);
    }

    function print_list_menu()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->helper(array('printer'));
            $this->load->model("order_model");
            $order_id = $this->input->post('order_id');

            //get printer reservation            
            $this->load->model("setting_printer_model");
            $printer_arr_obj = $this->setting_printer_model->get_printer_by_enum_printer_type(array("name_type"=>"printer_reservation"));
            
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

                $data = array();
                $data['setting'] = $this->data['setting'];
                $data['data_store'] = $this->data['data_store'];
                $data['store_name'] = $this->data['data_store']->store_name;
                $data['order'] = $this->order_model->get_by_order_id($order_id);
                $data['order_lists'] = $this->order_model->get_order_menu_by_order($order_id, array(0));
                $data_waiter = array();
                foreach ($data['order_lists'] as $o) {
                    $options = $this->order_model->get_option_by_order_menu($o->order_menu_id);
                    $o->options = $options;
                    $side_dish = $this->order_model->get_side_dish_by_order_menu($o->order_menu_id, $o->is_promo);
                    $o->side_dishes = $side_dish;
                    $data_waiter = $o;
                }

                print_list_menu($printer_location, $data, $data_waiter, $printer_setting);
            }
            
        }
    }

    public function posts()
    {
        $this->load->model("kitchen_model");
        $this->load->model("order_model");
        $this->load->model("stock_model");
        $this->load->model("order_menu_inventory_cogs_model");
        $order_menu_id = $this->input->post('order_menu_id');
        $table_id = $this->input->post('table_id');
        $order_id = $this->input->post('order_id');
        $reservation_id = $this->input->post('reservation_id');
		$reservation_data=$this->order_model->get_one("reservation",$reservation_id);
        $return_data = array();
        $return_data['status'] = true;
        if ($table_id != 0) {
            $current_table = $this->order_model->get_one("table", $table_id);
            if ($current_table->table_status != 1 && $current_table->table_status != 6) {
                $return_data['status'] = false;
                $return_data['message'] = "Meja " . $current_table->table_name . " harus kosong agar post dapat dilakukan!";
            }
        }else{
			if(sizeof($reservation_data)>0 && $reservation_data->order_type==1){
				$return_data['status'] = false;
                $return_data['message'] = "Reservasi ini tidak dapat dipost,silahkan edit data reservasi dan isi pilih meja terlebih dahulu!";
			}
		}
        if($return_data['status']==true){
            if (!empty($order_menu_id)) {
                $order = $this->order_model->get_one('order', $order_id);
                $this->kitchen_model->save("reservation", array("status" => 2, "status_posting" => 1), $order->reservation_id);
                if ($table_id != 0) {
                    $this->order_model->save('table', array('table_status' => 3), $order->table_id);
                }
                $order_menu_new = $this->order_model->get_order_menu(array('om.order_id' => $order_id, 'om.cooking_status' => 0));
                $outlets = array();
                foreach ($order_menu_new as $o) {
                    $ingredients = $this->order_model->get_all_menu_ingredients($o->menu_id);
                    foreach ($ingredients as $i) {
                        $difference = $o->quantity * $i->quantity;
                        $data_stocks = $this->stock_model->get_stock_detail_by_inventory_id(array(
                            "store_id" => $this->data['setting']['store_id'],
                            "outlet_id" => $o->outlet_id,
                            "inventory_id" => $i->inventory_id,
                            "uom_id" => $i->uom_id,
                        ));
                        foreach ($data_stocks as $stock) {
                            $data_stock_history = array(
                                "store_id" => $stock->store_id,
                                "outlet_id" => $stock->outlet_id,
                                "quantity" => $stock->quantity,
                                "inventory_id" => $stock->inventory_id,
                                "uom_id" => $stock->uom_id,
                                "price" => $stock->price,
                                "status" => 1,
                                "created_at" => date("Y-m-d H:i:s"),
                                "purchase_date" => date("Y-m-d H:i:s"),
                            );
                            if ($difference > 0) {
                                if ($stock->quantity >= $difference) {
                                    //UPDATE STOCK
                                    $this->stock_model->save("stock", array(
                                        "quantity" => $stock->quantity - $difference,
                                    ), $stock->id);
                                    //INSERT STOCK HISTORY
                                    $data_stock_history['quantity'] = ($difference * -1);
                                    $this->stock_model->insert_stock_history($data_stock_history);
                                    $cogs = array(
                                        'order_menu_id' => $o->id,
                                        'inventory_id' => $stock->inventory_id,
                                        'uom_id' => $stock->uom_id,
                                        'inventory_name' => $stock->inventory_name,
                                        'quantity' => $difference,
                                        'cogs' => $stock->price * $difference,
                                        'inventory_purchase_date' => date("Y-m-d H:i:s"),
                                        'created_at' => date("Y-m-d H:i:s")
                                    );
                                    $this->order_menu_inventory_cogs_model->add($cogs);
                                    $difference = 0;
                                } else {
                                    $transfered = $stock->quantity;
                                    //UPDATE STOCK
                                    $this->stock_model->save("stock", array(
                                        "quantity" => $stock->quantity - $transfered,
                                    ), $stock->id);
                                    //INSERT STOCK HISTORY
                                    $data_stock_history['quantity'] = ($transfered * -1);
                                    $this->stock_model->insert_stock_history($data_stock_history);
                                    $cogs = array(
                                        'order_menu_id' => $o->id,
                                        'inventory_id' => $stock->inventory_id,
                                        'uom_id' => $stock->uom_id,
                                        'inventory_name' => $stock->inventory_name,
                                        'quantity' => $transfered,
                                        'cogs' => $stock->price * $transfered,
                                        'inventory_purchase_date' => date("Y-m-d H:i:s"),
                                        'created_at' => date("Y-m-d H:i:s")
                                    );
                                    $this->order_menu_inventory_cogs_model->add($cogs);
                                    $difference -= $transfered;
                                }
                            } else break;
                        }
                        if ($difference > 0) {
                            $array = array(
                                'store_id' => $this->data['setting']['store_id'],
                                'outlet_id' => $o->outlet_id,
                                'inventory_id' => $i->inventory_id,
                                'uom_id' => $i->uom_id,
                                'quantity' => -1 * $difference,
                                'created_at' => date("Y-m-d H:i:s"),
                                'purchase_date' => date("Y-m-d H:i:s"),
                                'price' => 0
                            );
                            $this->stock_model->save('stock', $array);
                            $array = array(
                                'store_id' => $this->data['setting']['store_id'],
                                'outlet_id' => $o->outlet_id,
                                'inventory_id' => $i->inventory_id,
                                'uom_id' => $i->uom_id,
                                'quantity' => -1 * $difference,
                                'created_at' => date("Y-m-d H:i:s"),
                                'purchase_date' => date("Y-m-d H:i:s"),
                                'price' => 0,
                                'status' => 1
                            );
                            $this->stock_model->save('stock_history', $array);
                        }
                    }
                    if ($o->is_instant == 1) {
                        $cooking_status = $this->order_model->get_one("enum_cooking_status", 3);
                        $this->order_model->update_where('order_menu', array(
                            'process_status' => 1,
                            'cooking_status' => 3
                        ), array('id' => $o->id));
                        $this->order_model->update_where("order_package_menu", array(
                            'process_status' => 1,
                            'cooking_status' => 3
                        ), array("order_menu_id" => $o->id));
                    } else {
                        if (!in_array($o->outlet_id, $outlets)) array_push($outlets, (int)$o->outlet_id);
                    }
                }
                if ($this->data['setting']["use_kitchen"] == 1) {
                    $data_update = array(
                        'process_status' => 1,
                        'cooking_status' => 1
                    );
                } else {
                    $data_update = array(
                        'process_status' => 1,
                        'cooking_status' => 3
                    );
                }
                $data_update['created_at'] = date("Y-m-d H:i:s");
                // $this->order_model->update_where('order_menu', $data_update, array('order_id' => $order_id, 'cooking_status'=>0));
                $order_menu_remain = $this->order_model->get_all_where('order_menu', array('order_id' => $order_id, 'cooking_status' => 0));
                $this->cashier_model->update_where('order_menu', $data_update, array('order_id' => $order_id, 'cooking_status' => 0));

                foreach ($order_menu_remain as $remain) {
                    $data_update_package_menu = $data_update;
                    unset($data_update_package_menu['created_at']);
                    $this->order_model->update_where('order_package_menu', $data_update_package_menu, array('order_menu_id' => $remain->id, 'cooking_status' => 0));
                }
                foreach ($order_menu_id as $o) {
                    $result = $this->kitchen_model->get_order_menu_by_id($o);
                    $return_data['data'][] = $result[0];
                }
                $return_data['outlets'] = $outlets;
                $return_data['table_id']= $table_id;
                echo json_encode($return_data);
            } else {
                $reservation = $this->order_model->get_one("reservation", $reservation_id);
                $table_id = $reservation->table_id;
                $data_update = array('table_status' => 4, 'customer_count' => $reservation->customer_count);
                $this->store_model->update_status_table($table_id, $data_update);
                $check_reservation_menu = $this->order_model->get_all_where("order", array("reservation_id" => $reservation_id, "reservation_id !=" => 0));
                if (sizeof($check_reservation_menu) > 0) {
                    $reservation = $check_reservation_menu[0];
                    $order_id = $reservation->id;
                } else {
                    $data_order_where = $this->order_model->get_all_where("order", array("table_id" => $table_id, "order_status" => 0, "reservation_id" => 0));
                    if (empty($data_order_where)) {
                        $data = array(
                            'table_id' => $table_id,
                            'is_take_away' => 0,
                            'created_at' => date("Y-m-d H:i:s"),
                            'created_by' => $reservation->created_by,
                            'has_synchronized' => 0,
                            'reservation_id' => $reservation_id,
                            'customer_name' => (sizeof($reservation) > 0 ? $reservation->customer_name : ""),
                            'start_order' => $reservation->book_date
                        );
                        $order_id = $this->order_model->save_order($data);
                    } else {
                        $order_id = $data_order_where[(sizeof($data_order_where) - 1)]->id;
                    }
                }
                $data_reservation = array('status' => 2, "status_posting" => 1);
                $save = $this->order_model->save('reservation', $data_reservation, $reservation_id);
                $return_data['data'] = array();
                $return_data['table_id']= $table_id;
                $return_data['outlets'] = array();
                echo json_encode($return_data);
            }
        }else{
            echo json_encode($return_data);
        }
    }
}