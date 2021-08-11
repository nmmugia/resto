<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cashier extends Cashier_Controller
{
    /**
     * Global setting for store
     * @var mixed
     */
    private $_setting;

    /** 
     * Hold data for currently logged in user
     * @var mixed
     */
    private $_store_data;

    public function __construct()
    {
        parent::__construct();

        $this->load->model('order_model');
        $this->load->model('bill_model');
        $this->load->model('stock_model');
        $this->load->model('account_data_model');
        $this->load->model('order_menu_inventory_cogs_model');
        $this->load->model("inventory_model");
        $this->load->model("tax_model");
        $this->load->model("delivery_courier_model");
        $this->load->model("categories_model");
        $this->load->helper(array('order'));

        $this->_setting = $this->data['setting'];
        $this->_store_data = $this->ion_auth->user()->row();
        $all_cooking_status = array();
        foreach ($this->order_model->get("enum_cooking_status")->result() as $a) {
            $all_cooking_status[$a->id] = $a->status_name;
        }
        $this->data['all_cooking_status'] = json_encode($all_cooking_status);
    }

    public function index()
    {
        redirect(base_url('table'));
    }

    public function delivery()
    {
        if ($this->data['data_open_close']->status != 1) redirect(base_url());
        $this->groups_access->check_feature_access('delivery');
        $this->data['delivery_order'] = $this->cashier_model->get_order_delivery(0);
        //load content
        $this->data['title'] = "Cashier - Delivery";
        $this->data['theme'] = 'floor-theme';
        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');
        $this->data['content'] .= $this->load->view('cashier/orderlist_delivery_v', $this->data, true);
        $this->render('cashier');
    }

    public function order_delivery()
    {
        $this->groups_access->check_feature_access('delivery');

        $edit_id = $this->uri->segment(3);
        $date_now = date("Y-m-d H:i:s");
        if (!empty($edit_id)) {
            $order_id = $edit_id;
            $this->data['order_is_view'] = 1;
            $isEdit = true;
        } else {
            $order_id = "";
            $this->data['order_is_view'] = 0;
            $isEdit = false;
        }

        if (empty($order_id)) {
            $save_data = array('table_id' => 0,
                'is_take_away' => 0,
                'is_delivery' => 1,
                'created_at' => $date_now,
                'created_by' => $this->data['user_id'],
                'has_synchronized' => 0,
                'start_order' => $date_now
            );
            $this->data['order_id'] = $this->cashier_model->save('order', $save_data);
            $this->session->set_userdata('order_id_delivery', $this->data['order_id']);
        } else {
            $this->data['order_id'] = $order_id;
        }

        $this->data['data_order'] = $this->order_model->get_detail_order($this->data['order_id']);
        if (empty($this->data['data_order'])) {
            redirect(base_url('cashier/delivery'));
        }
        $this->load->model("delivery_cost_model");
        if ($isEdit === false) {
            $data_save = array('start_order' => $date_now);
            $this->cashier_model->save('order', $data_save, $this->data['order_id']);
        }

        $tax_method = $this->data['setting']['tax_service_method'];
        $get_order_taxes = $this->tax_model->get_taxes(3, $tax_method, 1);
        $order_payment = $this->order_model->calculate_total_order_bill($get_order_taxes, $this->data['order_id']);
        $res = list_order_payment($order_payment);

        $this->data['order_list'] = $res['order_list'];
        $this->data['cooking_time'] = $res['cooking_time'];
        $this->data['order_bill'] = $res['order_bill'];
        $this->data['categories'] = $this->cashier_model->get_category_by_store_menu($this->data['data_store']->id);
        $this->data['menus'] = $this->cashier_model->get_menus_by_store($this->data['data_store']->id);
        if ($this->data['setting']['zero_stock_order'] == 0) {
            $this->order_model->all_menu_ingredient_with_stock($this->data['menus']);
        } else {
            foreach ($this->data['menus'] as $m) {
                $m->total_available = 0;
            }
        }

        $already_process = $this->order_model->paged('order_menu', 1, 0, array('order_id' => $this->data['order_id'], 'process_status' => '1'));
        $this->data['already_process'] = $already_process->num_rows();
		$this->data['delivery_cost_formatted'] = array();

        if ($this->data['setting']['delivery_company'] != 1) {
            $this->data['delivery_cost_lists'] = $this->delivery_cost_model->get('enum_delivery_cost')->result();            
        } else {
            $this->data['delivery_cost_lists'] = $this->delivery_courier_model->get_courier_lists()->result();
        }

        foreach($this->data['delivery_cost_lists'] as $cost){
            $delivery_cost = number_format($cost->delivery_cost, 0, '', '.');
            $this->data['delivery_cost_formatted'][$cost->id] = ($cost->is_percentage ? $delivery_cost."%" : "Rp ".$delivery_cost);
        }

        $all_cooking_status = array();
        foreach ($this->order_model->get("enum_cooking_status")->result() as $a) {
            $all_cooking_status[$a->id] = $a->status_name;
        }
        $this->data['all_cooking_status'] = json_encode($all_cooking_status);

        $this->load->model('table_model');
        $this->data['printers_checker'] = $this->table_model->get_printer_checker_dropdown();
        $this->data['use_role_checker'] = $this->table_model->get_by("master_general_setting", "use_role_checker", "name");
        $this->data['printers_checker_setting'] = $this->order_model->get_by("master_general_setting", "auto_checker", "name");

        //load content
        $this->data['title'] = "Cashier - Order Delivery";
        $this->data['theme'] = 'floor-theme';
        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');
		$this->data['setting']['mobile_mode'] = 1;
        if ($this->data['setting']['mobile_mode'] == 1){
        $this->data['content'] .= $this->load->view('cashier/order_delivery_v_3', $this->data, true);
        } else {  
        $this->data['content'] .= $this->load->view('cashier/order_delivery_v', $this->data, true);
        }
        $this->render('cashier');
    }

    public function reset_delivery()
    {
        if ($this->input->is_ajax_request()) {
            $order_id = $this->input->post('order_id');

            $order_data = $this->order_model->get_one('order', $order_id);

            if ($order_data) {
                $order_data_status_new = $this->order_model->paged('order_menu', 0, 0, array('order_id' => $order_id,
                    'cooking_status' => '0'))->result();
                if (empty($order_data->end_order)) {

                    $already_process = $this->order_model->paged('order_menu', 1, 0, array('order_id' => $order_id,
                        'process_status' => '1'));

                    $is_processed = $already_process->num_rows();


                    if ($is_processed == 0) {
                        //delete order menu
                        $this->cashier_model->delete_by_limit('order_menu', array('order_id' => $order_id), 0);
                        $this->cashier_model->delete_by_limit('order', array('id' => $order_id), 0);
                    }
                } else {
                    $data_save_old = array('end_order' => date("Y-m-d H:i:s"));
                    $this->cashier_model->save('order', $data_save_old, $order_id);

                }
                if ($is_processed == 0) {

                    foreach ($order_data_status_new as $key => $row) {
                        $menu_ingredient = new stdclass();
                        $menu_ingredient = $this->order_model->one_menu_ingredient_with_stock($row->menu_id);
                        $this->order_model->increase_inventory_stock($menu_ingredient->ingredient, $row->quantity);
                    }

                    $return_data['order_id'] = $order_id;
                    $return_data['url_redir'] = base_url('cashier/delivery');
                    $return_data['status'] = true;
                    $this->session->set_userdata('order_id_delivery', '');

                    $return_data['status'] = true;
                } else {
                    $return_data['status'] = false;
                }
                echo json_encode($return_data);

            } else {
                echo json_encode(0);
            }
        }
    }

    public function process_delivery()
    {
        if ($this->input->is_ajax_request()) {
            $order_id = $this->input->post('order_id');
            $customer_name = $this->input->post('customer_name');
            $customer_phone = $this->input->post('customer_phone');
            $customer_address = $this->input->post('customer_address');
            $delivery_cost_id = $this->input->post('delivery_cost_id');
            $delivery_cost = $this->input->post('delivery_cost');

            $data_save = array('customer_name' => $customer_name, 'customer_phone' => $customer_phone, 'customer_address' => $customer_address
                // 'order_status' => 1
            );
            $result = $this->cashier_model->save('order', $data_save, $order_id);

            // $this->cashier_model->update_where('order_menu', $data_update, array('order_id' => $order_id));
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
            $data_update['created_at'] = date("Y-m-d H:i:s");
            $order_menu_new = $this->order_model->get_order_menu(array('om.order_id' => $order_id, 'om.cooking_status' => 0));
            $return_data['status_menu'] = [];
            $outlets = array();
            foreach ($order_menu_new as $o) {
                $ingredients = $this->order_model->get_all_menu_ingredients($o->menu_id);

                // mengecek menu side dish
                // created by: tri
                // created at: 26/08/2016
                if (!empty($ingredients)) {
                    foreach ($ingredients as $i) {
                        $this->process_inventory($o, $i);
                    }
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
                        if (!in_array($o->outlet_id, $outlets)) array_push($outlets, (int)$o->outlet_id);
                        $cooking_status = $this->order_model->get_one("enum_cooking_status", $data_update['cooking_status']);
                        $return_data['status_menu'][] = array("id" => $o->id, "cooking_status_name" => $cooking_status->status_name, "cooking_status" => $data_update['cooking_status']);
                    }
                }
            }
            $return_data['outlets'] = $outlets;
            $order_menu_remain = $this->order_model->get_all_where('order_menu', array('order_id' => $order_id, 'cooking_status' => 0));
            $this->cashier_model->update_where('order_menu', $data_update, array('order_id' => $order_id, 'cooking_status' => 0));

            foreach ($order_menu_remain as $remain) {
                $data_update_package_menu = $data_update;
                unset($data_update_package_menu['created_at']);
                $this->order_model->update_where('order_package_menu', $data_update_package_menu, array('order_menu_id' => $remain->id, 'cooking_status' => 0));
            }

            if ($result) {
                $data_order = $this->order_model->get_one('order', $order_id);
                if (!empty($data_order)) {
                    // get bill
                    $order_payment = array();
                    $order_payment['setting'] = $this->data['setting'];
                    $order_payment['store_data'] = $this->data['data_store'];
                    $order_payment['customer_data'] = $customer_name;
                    $order_menu_ids=array();
                    // if ($this->data['setting']['auto_print'] == 1) {
                        //if($this->data['setting']['use_kitchen']==1 && !empty($this->data['setting']['printer_kitchen'])){
                        $outlet_id_data = $this->order_model->get_outlet_id_by_order_id($order_id);
                        foreach ($outlet_id_data as $outlet_id) {
                            $order_payment['order_list'] = $this->order_model->kitchen_order($order_id, TRUE, $outlet_id->outlet_id);
                            if (count($order_payment['order_list']) > 0) {
                                foreach($order_payment['order_list'] as $a){
                                    array_push($order_menu_ids,$a->id);
                                }
                                $order_payment['outlet_data'] = $outlet_id->outlet_name;

                                $this->load->helper(array('printer'));
                                $this->load->model("setting_printer_model");
                                if ($this->data['setting']['auto_print'] == 1) {
                                    //get printer kitchen
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

                                if ($this->data['setting']['auto_checker'] == 1) {
                                    //get printer checker/service
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

                                if ($this->data['setting']['auto_checker_kitchen'] == 1) {
                                    //get printer checker kitchen
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
                        //}
                        
                    // }
                    foreach($order_menu_ids as $order_menu_id){
                        $update_array = array('kitchen_status' => '1');
                        $this->order_model->save('order_menu', $update_array,$order_menu_id);
                    }
                }
                $this->session->set_userdata('order_id_delivery', '');
                $return_data['number_guest'] = 0;
                $return_data['order_id'] = $order_id;
                $return_data['url_redir'] = base_url('cashier/delivery');
                echo json_encode($return_data);
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
            // check if quantity order > 0
            // $$order_remaining is counter for quantity order
            if($order_remaining > 0){
                if($stock->quantity > $order_remaining){
                    // if quantity in stock > quantity order
                    // update quantity in stock after deducting quantity order
                    $this->stock_model->save("stock", array("quantity" => $stock->quantity - $order_remaining), $stock->id);

                    $data_stock_history = array(
                        "store_id" => $stock->store_id,
                        "outlet_id" => $stock->outlet_id,
                        "quantity" => ($order_remaining * -1),
                        "inventory_id" => $stock->inventory_id,
                        "uom_id" => $stock->uom_id,
                        "price" => $stock->price,
                        "status" => 1,
                        "created_at" => date("Y-m-d H:i:s"),
                        "purchase_date" => $stock->purchase_date,
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
                        "purchase_date" => $stock->purchase_date,
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
                        "purchase_date" => $stock->purchase_date,
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
            'store_id' => $this->data['setting']['store_id'],
            'outlet_id' => $data_order_menu->outlet_id,
            'inventory_id' => $data_stocks[0]->inventory_id,
            'uom_id' => $data_stocks[0]->uom_id,
            'quantity' => $total_quantity,
            'created_at' => date("Y-m-d H:i:s"),
            'purchase_date' => $stock->purchase_date,
            'price' => $average_price
        );
        $new_id_stock = $this->stock_model->save('stock', $array);

        // process save to stock history and update quantity stock 
        // check if total stock quantity > quantity order
        if($total_quantity > $order_remaining){
            // update stock quantity (stock quantity - order quantity)
            $this->stock_model->save("stock", array("quantity" => $total_quantity - $order_remaining), $new_id_stock);

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

    public function takeaway()
    {
        if ($this->data['data_open_close']->status != 1) redirect(base_url());
        $this->groups_access->check_feature_access('takeaway');
        $this->data['takeaway_order'] = $this->cashier_model->get_order_takeaway(0);
        //load content
        $this->data['title'] = "cashier - Take Away";
        $this->data['theme'] = 'floor-theme';
        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');
        $this->data['content'] .= $this->load->view('cashier/orderlist_v', $this->data, true);
        $this->render('cashier');
    }

    public function order_takeaway()
    {
        $this->groups_access->check_feature_access('takeaway');

        $edit_id = $this->uri->segment(3);
        $date_now = date("Y-m-d H:i:s");
        
        if (!empty($edit_id)) {
            $order_id = $edit_id;
            $this->data['order_is_view'] = 1;
            $isEdit = true;
        } else {
            $order_id ="";
            $this->data['order_is_view'] = 0;
            $isEdit = false;
        }

        if (empty($order_id)) {
            $save_data = array('table_id' => 0,
                'is_take_away' => 1,
                'created_at' => $date_now,
                'created_by' => $this->data['user_id'],
                'has_synchronized' => 0,
                'start_order' => $date_now
            );
            $this->data['order_id'] = $this->cashier_model->save('order', $save_data);
            $this->session->set_userdata('order_id_takeaway', $this->data['order_id']);
        } else {
            $this->data['order_id'] = $order_id;
        }

        $this->data['data_order'] = $this->order_model->get_detail_order($this->data['order_id']);

        if (empty($this->data['data_order'])) {
            redirect(base_url('cashier/takeaway'));
        }

        if ($isEdit === false) {
            $data_save = array('start_order' => $date_now);
            $this->cashier_model->save('order', $data_save, $this->data['order_id']);
        }
        
        $tax_method = $this->data['setting']['tax_service_method'];
        $get_order_taxes = $this->tax_model->get_taxes(2, $tax_method, 1);
        $order_payment = $this->order_model->calculate_total_order_bill($get_order_taxes, $this->data['order_id']);
        $res = list_order_payment($order_payment);

        $this->data['order_list'] = $res['order_list'];
        $this->data['order_bill'] = $res['order_bill'];
        $this->data['categories'] = $this->cashier_model->get_category_by_store($this->data['data_store']->id,TRUE);
        $this->data['menus'] = $this->cashier_model->get_menus_by_store($this->data['data_store']->id);
        if ($this->data['setting']['zero_stock_order'] == 0) {
            $this->order_model->all_menu_ingredient_with_stock($this->data['menus']);
        } else {
            foreach ($this->data['menus'] as $m) {
                $m->total_available = 0;
            }
        }

        $already_process = $this->order_model->paged('order_menu', 1, 0, array('order_id' => $this->data['order_id'], 'process_status' => '1'));
        $this->data['already_process'] = $already_process->num_rows();

        $this->load->model('table_model');
        $this->data['printers_checker'] = $this->table_model->get_printer_checker_dropdown();
        $this->data['use_role_checker'] = $this->table_model->get_by("master_general_setting", "use_role_checker", "name");
        $this->data['printers_checker_setting'] = $this->order_model->get_by("master_general_setting", "auto_checker", "name");

        //load content
        $this->data['title'] = "cashier - Order Take Away";
        $this->data['theme'] = 'floor-theme';
        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');
        if ($this->data['setting']['mobile_mode'] == 1){
        $this->data['content'] .= $this->load->view('cashier/order_takeaway_v_3', $this->data, true);
        }else{
        $this->data['content'] .= $this->load->view('cashier/order_takeaway_v', $this->data, true);
        }
        //print_r('expression');
        $this->render('cashier'); 
    }

    public function get_menus()
    {
        if ($this->input->is_ajax_request()) {
            $category_id = $this->input->post('category_id');
            $menu_type = $this->input->post('menu_type');
            $menus = $this->cashier_model->get_menus_by_category(array("category_id" => $category_id, "available" => 1));
            // $menus = $this->cashier_model->paged_join('menu', 'category','menu.*, category.outlet_id',
            // 'menu.category_id = category.id',
            // 0, 0, array('category_id' => $category_id, 'available' => 1),
            // 'menu_name ASC');         

            if (sizeof($menus) > 0) {
                $content = '';
                $thumb = 'none';
                $list = 'none';
                if ($this->data['setting']['zero_stock_order'] == 0) {
                    if ($this->data['setting']['stock_menu_by_inventory'] == 1) {
                        $this->order_model->all_menu_ingredient_with_stock($data_menu);
                    } else {
                        foreach ($data_menu as $m) {
                            $m->total_available = $m->menu_quantity;
                        }
                    }
                } else {
                    foreach ($menus as $m) {
                        $m->total_available = 0;
                    }
                }


                if ($menu_type == "thumb") {
                    $thumb = 'block';
                } else {
                    $list = 'block';
                }

                $content .= '<ul class="list-category-text list" id="list-menu-text" style="display:' . $list . '"> ';

                foreach ($menus as $menu) {
                    $content .= '<li data-id="' . $menu->id . '" data-name="' . $menu->menu_name . '" data-price="' . $menu->menu_price . '" data-option-count="' . $menu->menu_option_count . '" data-side-dish-count="' . $menu->menu_side_dish_count . '"  class="add-order-menu">';
                    $content .= '<span class="left name" style="width:75%;">' . $menu->menu_name . '</span>';
                    $content .= '<span id=""  data-outlet="' . $menu->outlet_id . '" class="right ' . ($this->data['setting']['zero_stock_order'] == 1 ? "hide" : "") . ' total-available-' . $menu->id . '" style="margin-left:50px;">' . $menu->total_available . '</span>';
                    $content .= '<span class="right">' . number_format($menu->menu_price, 0, "", ".") . '</span><span class="left">Rp</span>
                    </li>';
                }
                $content .= '</ul>';

                $content .= '<span id="thumb-menu-text" style="display:' . $thumb . '"> ';
                foreach ($menus as $menu) {
                    if (!empty($menu->icon_url)) {
                        $image = base_url($menu->icon_url);
                    } else {
                        $image = base_url('assets/img/default-category.jpg');
                    }

                    $content .= '<div data-id="' . $menu->id . '" data-name="' . $menu->menu_name . '" data-price="' . $menu->menu_price . '" data-option-count="' . $menu->menu_option_count . '" data-side-dish-count="' . $menu->menu_side_dish_count . '" class="menu-order add-order-menu"><img src="' . $image . '" alt="menu"/><p>' . $menu->menu_name . '</p></div>';

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
            $order_menu_data = $this->order_model->get_one('order_menu', $menu_order_id);

            if (!empty($menu_id)) {
                $side_dish = $this->order_model->get_side_dish_by_menu($menu_id, $menu_data->is_promo);
                if ($side_dish) {
                    $content = '';
                    foreach ($side_dish as $side) {
                        $checked = '';
                        if ($menu_order_id != '0') {
                            $sideOpt = explode(',', $order_menu_data->side_dishes);
                            foreach ($sideOpt as $opt) {
                                if ($opt == $side->id) {
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
                                    $opts = explode(',', $order_menu_data->options);
                                    foreach ($opts as $opt) {
                                        if ($opt == $value->id) {
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

    public function save_order_menu_discount()
    {
        $order_id = $this->input->post('order_id');
        $discount_price = $this->input->post('discount_price');
        $discount_name = $this->input->post('discount_name');
        $type = $this->input->post('type');

        $return_data['status'] = FALSE;
        $return_data['msg'] = "";

        //discount for each menu
        if ($type == "menu") {
            $old_data_menu = $this->order_model->get_one('order_menu', $order_id);
            $count = $old_data_menu->quantity;

            $data = array('discount_price' => $discount_price,
                'discount_name' => $discount_name);

            $result = $this->order_model->update_order_menu($order_id, $data);
            $order_id = $old_data_menu->order_id;

        } else {
            $data = array('discount_price' => $discount_price,
                'discount_name' => $discount_name);
            $result = $this->order_model->save('order', $data, $order_id);
        }


        if ($result) {

            $order_payment = $this->order_model->calculate_total_order($order_id);
            $res = list_order_payment($order_payment);
            $return_data['order_list'] = $res['order_list'];
            $return_data['order_bill'] = $res['order_bill'];
            $return_data['status'] = TRUE;
        }

        echo json_encode($return_data);

    }

    public function save_delivery_cost_order()
    {
        $order_id = $this->input->post("order_id");
        $delivery_cost_id = $this->input->post("delivery_cost_id");
        $delivery_cost = $this->input->post("delivery_cost");
        $tax_method = $this->data['setting']['tax_service_method'];
        $get_order_taxes = $this->tax_model->get_taxes(3, $tax_method, 1);
        $result = array();
        if ($order_id != "") {

            if ($this->data['setting']['delivery_company'] != 1) {
                $enum_delivery_cost = $this->order_model->get_one('enum_delivery_cost', $delivery_cost_id);
            } else {
                $enum_delivery_cost = (object) array_pop($this->delivery_courier_model->get_courier_lists($delivery_cost_id)->result());
            }
            
            $order_payment = $this->order_model->calculate_total_order_bill($get_order_taxes, $order_id);
            if ($enum_delivery_cost) {
                if ($enum_delivery_cost->is_percentage) {
                    $total_delivery_cost = ($order_payment['subtotal_value'] * $enum_delivery_cost->delivery_cost) / 100;
                } else {
                    $total_delivery_cost = $delivery_cost;
                }


                $this->cashier_model->update_where('order', array("delivery_cost_id" => $delivery_cost_id, "delivery_cost" => $total_delivery_cost), array('id' => $order_id));
            }

            $order_payment = $this->order_model->calculate_total_order_bill($get_order_taxes, $order_id);
            $res = list_order_payment($order_payment);
            $total_delivery_cost = 0;

            $result = array(
                "order_list" => $res['order_list'],
                "order_bill" => $res['order_bill']
            );
        }
        echo json_encode(array(
            "data" => $result
        ));
    }

    public function save_order_menu()
    {
        if ($this->input->is_ajax_request()) {
            $menu_id = $this->input->post('menu_id');
            $order_id = $this->input->post('order_id');
            $count = $this->input->post('count');
            $option = $this->input->post('option');
            $side_dish = $this->input->post('side_dish');
            $notes = $this->input->post('notes');
            $is_edit = $this->input->post('is_edit');
            $is_checkout = $this->input->post('isCheckout');
            // $discount_price  = $this->input->post('discount_price');
            // $discount_name  = $this->input->post('discount_name');

            $return_data['status'] = FALSE;
            $return_data['msg'] = "";

            $ingredient_menu_id = $menu_id;
            if ($is_edit == 'true') {
                $old_data_menu = $this->order_model->get_one('order_menu', $menu_id);
                $ingredient_menu_id = $old_data_menu->menu_id;
            }

            $outlet_id = $this->order_model->get_outlet_by_menu_id($ingredient_menu_id);
            $outlet_id = $outlet_id->outlet_id;

            $menu_ingredient = $this->order_model->one_menu_ingredient_with_stock($ingredient_menu_id);
            $total_available = $menu_ingredient->total_available;

            if ($this->data['setting']['zero_stock_order'] == 0 && $count > $total_available) {
                $result = FALSE;
                $return_data['status'] = FALSE;
                $return_data['msg'] = "Gagal menambah pesanan, stok tidak mencukupi. Lakukan refresh halaman.";

            } else {

                if ($is_edit == 'true') {
                    if ($is_checkout == '1') {
                        $data = array('count' => ($old_data_menu->quantity + $count),
                            'options' => $option,
                            'side_dishes' => $side_dish,
                            'notes' => $notes,
                            'cooking_status' => 3,
                            'process_status' => 1,
                            // 'discount_name' => $discount_name,
                            // 'discount_price' => $discount_price
                        );
                    } else {
                        $data = array('count' => $count,
                            'options' => $option,
                            'side_dishes' => $side_dish,
                            'notes' => $notes,
                            // 'discount_name' => $discount_name,
                            // 'discount_price' => $discount_price
                        );
                    }

                    $result = $this->order_model->update_order_menu($menu_id, $data);

                } else {
                    if ($is_checkout == '1') {
                        $data = array('menu_id' => $menu_id,
                            'order_id' => $order_id,
                            'count' => $count,
                            'options' => $option,
                            'side_dishes' => $side_dish,
                            'notes' => $notes,
                            'cooking_status' => 3,
                            'process_status' => 1,
                            // 'discount_name' => $discount_name,
                            // 'discount_price' => $discount_price
                        );
                    } else {
                        $data = array('menu_id' => $menu_id,
                            'order_id' => $order_id,
                            'count' => $count,
                            'options' => $option,
                            'side_dishes' => $side_dish,
                            'notes' => $notes,
                            'cooking_status' => 1,
                            // 'discount_name' => $discount_name,
                            // 'discount_price' => $discount_price
                        );
                    }

                    $result = $this->order_model->save_order_menu($data);
                }

                $this->order_model->decrease_inventory_stock($menu_ingredient->ingredient, $count);

            }

            if ($result) {
                $menu_outlet = $this->order_model->get_menu_outlet($outlet_id);
                if ($this->data['setting']['zero_stock_order'] == 0) {
                    $this->order_model->all_menu_ingredient_with_stock($menu_outlet);
                } else {
                    foreach ($menu_outlet as $m) {
                        $m->total_available = 0;
                    }
                }

                $order_payment = $this->order_model->calculate_total_order($order_id);
                $res = list_order_payment($order_payment);
                $return_data['order_list'] = $res['order_list'];
                $return_data['order_bill'] = $res['order_bill'];
                $return_data['arr_menu_outlet'] = $menu_outlet;
                $return_data['status'] = TRUE;
            }

            echo json_encode($return_data);
        }
    }

    public function delete_order_menu()
    {
        if ($this->input->is_ajax_request()) {
            $menu_id = $this->input->post('menu_id');
            $order_id = $this->input->post('order_id');
            $count = $this->input->post('count');
            $outlet_id = $this->order_model->get_outlet_id_by_order_id($order_id);

            $order_data = $this->order_model->get_one('order_menu', $menu_id);
            $result = $this->order_model->delete_order_menu($menu_id);
            $return_data['status'] = FALSE;

            if ($result && !empty($outlet_id)) {
                $outlet_id = $outlet_id[0]->outlet_id;

                $menu_ingredient = $this->order_model->one_menu_ingredient_with_stock($order_data->menu_id);
                $this->order_model->increase_inventory_stock($menu_ingredient->ingredient, $count);

                $menu_outlet = $this->order_model->get_menu_outlet($outlet_id);
                if ($this->data['setting']['zero_stock_order'] == 0) {
                    $this->order_model->all_menu_ingredient_with_stock($menu_outlet);
                } else {
                    foreach ($menu_outlet as $m) {
                        $m->total_available = 0;
                    }
                }

                $order_payment = $this->order_model->calculate_total_order($order_id);
                $res = list_order_payment($order_payment);
                $return_data['order_list'] = $res['order_list'];
                $return_data['arr_menu_outlet'] = $menu_outlet;
                $return_data['order_bill'] = $res['order_bill'];
                $return_data['status'] = TRUE;
            }

            echo json_encode($return_data);
        }
    }

    public function reset_takeaway()
    {
        if ($this->input->is_ajax_request()) {
            $order_id = $this->input->post('order_id');

            $order_data = $this->order_model->get_one('order', $order_id);

            if ($order_data) {
                $order_data_status_new = $this->order_model->paged('order_menu', 0, 0, array('order_id' => $order_id,
                    'cooking_status' => '0'))->result();
                if (empty($order_data->end_order)) {

                    $already_process = $this->order_model->paged('order_menu', 1, 0, array('order_id' => $order_id,
                        'process_status' => '1'));

                    $is_processed = $already_process->num_rows();


                    if ($is_processed == 0) {
                        //delete order menu
                        $this->cashier_model->delete_by_limit('order_menu', array('order_id' => $order_id), 0);
                        $this->cashier_model->delete_by_limit('order', array('id' => $order_id), 0);
                    }
                } else {
                    $data_save_old = array('end_order' => date("Y-m-d H:i:s"));
                    $this->cashier_model->save('order', $data_save_old, $order_id);

                }
                if ($is_processed == 0) {

                    foreach ($order_data_status_new as $key => $row) {
                        $menu_ingredient = new stdclass();
                        $menu_ingredient = $this->order_model->one_menu_ingredient_with_stock($row->menu_id);
                        $this->order_model->increase_inventory_stock($menu_ingredient->ingredient, $row->quantity);
                    }

                    $return_data['order_id'] = $order_id;
                    $return_data['url_redir'] = base_url('cashier/takeaway');
                    $return_data['status'] = true;
                    $this->session->set_userdata('order_id_takeaway', '');

                    $return_data['status'] = true;
                } else {
                    $return_data['status'] = false;
                }
                echo json_encode($return_data);

            } else {
                echo json_encode(0);
            }
        }
    }

    public function process_takeaway()
    {
        if ($this->input->is_ajax_request()) {
            $order_id = $this->input->post('order_id');
            $customer_name = $this->input->post('customer_name');
            $customer_phone = $this->input->post('customer_phone');
            $return_data = array();
            $data_save = array('customer_name' => $customer_name, "customer_phone" => $customer_phone);
            $result = $this->cashier_model->save('order', $data_save, $order_id);
            // $this->cashier_model->update_where('order_menu', $data_update, array('order_id' => $order_id));
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
            $data_update['created_at'] = date("Y-m-d H:i:s");
            $order_menu_new = $this->order_model->get_order_menu(array('om.order_id' => $order_id, 'om.cooking_status' => 0));
            $return_data['status_menu'] = [];
            $outlets = array();
            foreach ($order_menu_new as $o) {
                $ingredients = $this->order_model->get_all_menu_ingredients($o->menu_id);
                if (!empty($ingredients)) {
                    foreach ($ingredients as $i) {
                        // proses inventory untuk komposisi menu
                        $this->process_inventory($o, $i);
                    }
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
                        if (!in_array($o->outlet_id, $outlets)) array_push($outlets, (int)$o->outlet_id);
                        $cooking_status = $this->order_model->get_one("enum_cooking_status", $data_update['cooking_status']);
                        $return_data['status_menu'][] = array("id" => $o->id, "cooking_status_name" => $cooking_status->status_name, "cooking_status" => $data_update['cooking_status']);
                    }
                }
            }
            $return_data['outlets'] = $outlets;

            $order_menu_remain = $this->order_model->get_all_where('order_menu', array('order_id' => $order_id, 'cooking_status' => 0));
            $this->cashier_model->update_where('order_menu', $data_update, array('order_id' => $order_id, 'cooking_status' => 0));

            foreach ($order_menu_remain as $remain) {
                $data_update_package_menu = $data_update;
                unset($data_update_package_menu['created_at']);
                $this->order_model->update_where('order_package_menu', $data_update_package_menu, array('order_menu_id' => $remain->id, 'cooking_status' => 0));
            }


            if ($result) {
                $data_order = $this->order_model->get_one('order', $order_id);
                if (!empty($data_order)) {
                    // get bill
                    $order_payment = array();
                    $order_payment['setting'] = $this->data['setting'];
                    $order_payment['store_data'] = $this->data['data_store'];
                    $order_payment['customer_data'] = $customer_name;
                    $order_menu_ids=array();
                    // if ($this->data['setting']['auto_print'] == 1) {
                        //if($this->data['setting']['use_kitchen']==1 && !empty($this->data['setting']['printer_kitchen'])){
                        $outlet_id_data = $this->order_model->get_outlet_id_by_order_id($order_id);
                        foreach ($outlet_id_data as $outlet_id) {
                            $order_payment['order_list'] = $this->order_model->kitchen_order($order_id, TRUE, $outlet_id->outlet_id);
                            if (count($order_payment['order_list']) > 0) {
                                foreach($order_payment['order_list'] as $a){
                                    array_push($order_menu_ids,$a->id);
                                }
                                $order_payment['outlet_data'] = $outlet_id->outlet_name;

                                $this->load->helper(array('printer'));                                
                                $this->load->model("setting_printer_model");
                                if ($this->data['setting']['auto_print'] == 1) {
                                    //get printer kitchen
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

                                if ($this->data['setting']['auto_checker'] == 1) {
                                    //get printer checker/service
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

                                if ($this->data['setting']['auto_checker_kitchen'] == 1) {
                                    //get printer checker kitchen
                                    $printer_arr_obj = $this->setting_printer_model->get_printer_by_enum_printer_type(array("name_type"=>"printer_checker_kitchen")); 
                                    foreach ($printer_arr_obj as $value) {
                                        //check outlet same with outlet id printer
                                        if ($outlet_id->outlet_id == $value->outlet_id) {
                                            //build object printer setting for printer helper

                                              //get name alias printer
                                            
                                            $printer_setting = new stdClass();
                                            $printer_setting->id = $value->id;
                                            $printer_setting->name = $value->alias_name;
                                            $printer_setting->value = $value->name_printer;
                                            $printer_setting->default = $value->logo;
                                            $printer_setting->description = $value->printer_width;
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
                        foreach($order_menu_ids as $order_menu_id){
                            $update_array = array('kitchen_status' => '1');
                            $this->order_model->save('order_menu', $update_array,$order_menu_id);
                        }
                }
                $this->session->set_userdata('order_id_takeaway', '');
                $return_data['number_guest'] = 0;
                $return_data['order_id'] = $order_id;
                $return_data['url_redir'] = base_url('cashier/takeaway');
                echo json_encode($return_data);
            } else {
                echo json_encode(0);
            }
        }
    }

    public function checkout()
    {
        $order_id = $this->uri->segment(3);
        $order_type = 1;

        if (empty($order_id)) {
            redirect(base_url('table'));
        }

        $this->data['data_order'] = $this->order_model->get_one('order', $order_id);
        $this->data['reservation'] = $this->order_model->get_one('reservation', $this->data['data_order']->reservation_id);

        if (empty($this->data['data_order'])) {
            redirect(base_url('table'));
        }

        // already paid bill
        if (!empty($this->data['data_order']->end_order)) {
            redirect(base_url('table/order_dine_in/' . $order_id));
        }

        if ($this->data['data_order']->table_id != 0) {
            $get_table = $this->order_model->get_one('table', $this->data['data_order']->table_id);
            $order_in_table = $this->order_model->get_all_order_in_table($this->data['data_order']->table_id);
            if ($this->data['setting']['dining_type'] != 3) {
                if ($get_table->table_status != 2 && sizeof($order_in_table) < 2) {
                    redirect(base_url('table'));
                }
            }
        }

        if ($this->data['data_order']->is_take_away == 1) {
            $order_type = 2;
        } else if ($this->data['data_order']->is_delivery == 1) {
            $order_type = 3;
        }
        $tax_method = $this->data['setting']['tax_service_method'];
        $get_order_taxes = $this->tax_model->get_taxes($order_type, $tax_method, 1);

        $this->data['back_url'] = ($this->data['data_order']->table_id == 0) ? ($this->data['data_order']->is_take_away == 1 ? base_url('cashier/takeaway') : base_url('cashier/delivery')) : base_url('table');

        $this->data['order_name'] = $this->data['data_order']->customer_name;
        $this->data['order_phone'] = $this->data['data_order']->customer_phone;
        $this->data['order_address'] = $this->data['data_order']->customer_address;
        $this->data['order_mode'] = 'Nama';
        if ($this->data['data_order']->is_take_away == '0' && $this->data['data_order']->is_delivery == '0') {
            $this->data['order_mode'] = 'Table';
            $data_table = $this->order_model->get_one('table', $this->data['data_order']->table_id);
            if ($this->data['data_order']->table_id != 0) {
                $this->data['order_name'] = 'Meja ' . $data_table->table_name . ($this->data['data_order']->customer_name != "" ? " (" . $this->data['data_order']->customer_name . ")" : "") . ', ' . $data_table->customer_count . ' orang';
            } else {
                $this->data['order_mode'] = 'Fast Order Dine In';
                $this->data['order_name'] = "";
            }
        }


        $this->data['staff_mode'] = $this->data['user_profile_data']->name;

        $order_payment = array();
        // get all menu by order id
        $order_menus = $this->order_model->get_menu_by_order_id($order_id); 

        $canceled = ($this->data['setting']['dining_type'] != 3) ? TRUE : FALSE;
        $checkout = ($this->data['setting']['dining_type'] != 3) ? FALSE : TRUE;
        // check priority cogs count in setting
        if($this->data['setting']["priority_cogs_count"] == 0){
            // count cogs from menu
            $order_payment = $this->order_model->calculate_total_order_bill($get_order_taxes, $order_id, $canceled, FALSE, $checkout);
        } else {
            // check if user module inventory
            if ($this->data['module']['INVENTORY'] == 1) {
                // count cogs from inventory
                $order_payment = $this->order_model->calculate_total_order_bill($get_order_taxes, $order_id, $canceled, TRUE, $checkout);
            } else {
                // count cogs from menu
                $order_payment = $this->order_model->calculate_total_order_bill($get_order_taxes, $order_id, $canceled, FALSE, $checkout);
            }
        
            if ($order_payment["total_hpp"] == 0) {
                // count cogs from menu
                $order_payment = $this->order_model->calculate_total_order_bill($get_order_taxes, $order_id, $canceled, FALSE, $checkout);
            }
        }

        $res = $this->list_order_payment($order_payment);
        $this->data['order_list'] = $res['order_list'];
        if ($res['order_list'] == "") {
            redirect("table");
        }
        $this->data['order_bill'] = $res['order_bill'];
        $this->data['total_tax'] = $res['total_tax'];
        $this->data['total_price'] = $res['total_price'];
        $this->data['order_id'] = $order_id;

        $get_round_up = $this->data['setting']['is_round_up'];
        $this->data['is_round_up'] = (!empty($get_round_up)) ? $get_round_up : 0;

        $get_nearest_round = $this->data['setting']['nearest_round'];

        $this->data['nearest_round'] = (!empty($get_nearest_round) && (!(empty($get_round_up)) && $get_round_up == 1)) ? $get_nearest_round : 0;

        $this->data['categories'] = $this->cashier_model->get_category_by_store($this->data['data_store']->id);
        $this->data['menus'] = $this->cashier_model->get_menus_by_store($this->data['data_store']->id);

        $this->data['promo_id'] = $this->cashier_model->get_promo_discount_dropdown($this->data['data_store']->id);
        $this->data['promo_cc'] = $this->cashier_model->get_promo_cc_dropdown($this->data['data_store']->id);
        $this->data['store_id'] = $this->data['data_store']->id;
        $this->data['order_company'] = $this->store_model->get_order_company_dropdown();
        $this->data['employees'] = $this->store_model->get_member_employee_dropdown();
        // $this->data['bank'] = $this->store_model->get_all_bank();
        $this->data['bank'] = $this->store_model->get_all_bank_dropdown();
        $this->data['bank_account_card'] = $this->store_model->get_bank_account_card_dropdown();
        $data_compliment = $this->store_model->get_compliment_data();
        $array_compliments = array();
        $this->load->library('encrypt');
        foreach ($data_compliment as $key => $value) {
            $compliment = new stdClass();
            $compliment->user_id = $value->user_id;
            $compliment->name = $value->name;
            array_push($array_compliments, $compliment);
        }

        $this->data['compliments'] = $array_compliments;

        $this->data['non_employee_members'] = $this->store_model->get_all_non_employee_member();
        $this->data['data_url'] = base_url('cashier/get_data_member');
        
        //load content
        $this->data['voucher_categories']=array();
        if ($this->data['setting']["voucher_method"] == 2) {
            $this->data['voucher_categories'] = $this->store_model->get("voucher_group")->result();
        }
        $this->data['title'] = "cashier - Checkout";
        $this->data['theme'] = 'floor-theme';
        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');
        $this->data['content'] .= $this->load->view('cashier/checkout_v', $this->data, true);
        $this->render('cashier');
    }

    public function get_data_member() {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));
        $this->datatables->select('member.id,
                                    member_id,
                                    member.name,
                                    member.birth_date,
                                    city.name AS city,
                                    email,
                                    mobile_phone')
            ->from('member')
            ->join('city', 'city.id = member.city_id', 'left')
            ->where('member_category_id != (SELECT value FROM master_general_setting WHERE name = "member_karyawan_kategori_id")')
            ->add_column("actions", "$1", "get_member_choice_button(id, name, member_id)");
        echo $this->datatables->generate();
    }

    public function payment_bill()
    {
		if ($this->input->is_ajax_request()) {
            $this->load->model("account_model");
            $order_id = $this->input->post('order_id');
            $data_bayar = $this->input->post('data_bayar');
            $payment_total = $this->input->post('payment_total');
            $delivery_cost_id = $this->input->post('delivery_cost_id');
            $delivery_cost = $this->input->post('delivery_cost');

            $taxes = $this->input->post('taxes');
            $promo_total = $this->input->post('promo_total');
            $promo_cc = $this->input->post('promo_cc');
            $promo_id = $this->input->post('promo_id');
            $promo_cc_id = $this->input->post('promo_cc_id');
            $promo_name = $this->input->post('promo_name');
            $promo_cc_name = $this->input->post('promo_cc_name');
            $customer_payment = $this->input->post('customer_payment');
            $payment_option = $this->input->post('payment_option');
            $compliment_total = $this->input->post('compliment_total');
            $discount_member_id = $this->input->post('discount_member_id');
            $discount_member_percentage = $this->input->post('discount_member_percentage');
            $sub_total_2 = $this->input->post('sub_total_2');
            $discount_member_total = $this->input->post('discount_member_total');
            $taxes = json_decode($taxes, true);
            $round_total = $this->input->post('round_total');
            $is_round_up = $this->input->post('is_round_up');
            $customer_cash_payment = $this->input->post('customer_cash_payment');
            $pending_bill = json_decode($this->input->post('pending_bill'), true);
            $pending_bill_employee = json_decode($this->input->post('pending_bill_employee'), true);
            $date_now = date("Y-m-d H:i:s");
            $payment_option = json_decode($payment_option, true);
            $grand_total = $this->input->post('grand_total');
            $voucher_quantity = $this->input->post('voucher_quantity');
            $receipt_number = $this->input->post('receipt_number');
            $kembalian = $this->input->post('kembalian');
            $data_bayar = json_decode($data_bayar, true);
            $this->db->trans_begin();

            $data_order = $this->order_model->get_by_order_id($order_id);
            $data_table = $this->order_model->get_one('table', $data_order->table_id);
            $refund_data = array();

            if ($receipt_number != "") {                
                // process for refund
                $order_status = $data_order->order_status;
                if ($this->data['setting']['dining_type'] == 1) {
                    $table_status = isset($data_table->table_status) ? $data_table->table_status : 0;
                } else {
                    $table_status = 0;
                }
                $refund_data = $this->process_refund($receipt_number, $data_bayar, $order_status, $table_status);
            }

            if (sizeof($data_order) == 0) {
                $return_data['status'] = FALSE;
                echo json_encode($return_data);
                return false;
            }
            $reservation = $this->order_model->get_one('reservation', $data_order->reservation_id);                

            $total_debit = 0;
            $total_credit = 0;
            if (empty($data_order)) {
                $return_data['status'] = FALSE;
                return;
            }

            if ($receipt_number == "") {
                if ($data_order->order_status == 1) {
                    $return_data['status'] = FALSE;
                    if ($data_order->table_id != 0 && $data_table->table_status != 7) {
                        $table_status = 1;
                        if ($this->data['setting']['dining_type'] == 3) {
                            $table_status = 3;
                        } else {
                            if ($this->data['setting']['cleaning_process'] == 1) $table_status = 7;
                        }
                        $data_update = array('table_status' => $table_status);
                        $this->cashier_model->save('table', $data_update, $data_order->table_id);
                    }
                    return;
                }
            }                

            $subtotal_1 = $payment_total + $promo_total + $promo_cc + $discount_member_total;
            $down_payment = $this->input->post('down_payment');
            $origin_grand_total = $this->input->post('origin_grand_total');
            
            if (!empty($data_bayar)) {
                if (isset($pending_bill['code']) && $pending_bill['code'] != "") {
                    $order_company = $this->cashier_model->get_one("order_company", $pending_bill['code']);
                    $this->cashier_model->save("order_company", array(
                        "down_payment" => $order_company->down_payment - $grand_total
                    ), $pending_bill['code']);
                }

                // generate new bill
                $today = date('Ymd');

                switch ($this->data['setting']["bill_auto_number"]) {
                    case 2:
                        $exist_numbers = $this->cashier_model->get_four_digit_receipt_id();
                        $exist_numbers = explode(",", $exist_numbers);
                        $new_receipt_id = $today . $this->generate_unique_number($exist_numbers);
                        break;
                    
                    case 3:
                        $prefix = $this->generate_random_number(1,8);

                        $exist_numbers = $this->cashier_model->get_four_digit_receipt_id();
                        $exist_numbers = explode(",", $exist_numbers);
                        $new_receipt_id = $prefix . $this->generate_unique_number($exist_numbers);
                        
                        break;

                    default:
                        $max_id = $this->cashier_model->get_max_receipt_id();
                        $maxDay = substr($max_id, 0, 8);
                        if ($maxDay != $today) {
                            $new_receipt_id = $today . '0001';
                        } else {
                            $new_receipt_id = $max_id + 1;
                        }
                        break;
                }

                if (($receipt_number != "") && ($receipt_number >= $new_receipt_id)) {
                    $new_receipt_id = $receipt_number + 1;
                }

                if ($data_order->is_take_away == 1 || $data_order->is_delivery == 1) {
                    $customer_count = 1;
                } else {
                    if ($this->data['setting']['dining_type'] == 1) {
                        $customer_count = $data_table->customer_count;
                    } else {
                        $customer_count = 1;
                    }
                }

                if ($customer_count == 0) $customer_count = 1;
                $data_bill = array('cashier_id' => $this->data['user_profile_data']->id,
                    'total_price' => $grand_total,
                    'order_id' => $order_id,
                    'is_take_away' => $data_order->is_take_away,
                    'is_delivery' => $data_order->is_delivery,
                    'payment_date' => $date_now,
                    'table_id' => $data_order->table_id,
                    'customer_count' => $customer_count,
                    'customer_name' => $data_order->customer_name,
                    'customer_phone' => $data_order->customer_phone,
                    'customer_address' => $data_order->customer_address,
                    'promo_id' => (int)$promo_id,
                    'created_at' => $date_now,
                    'created_by' => $this->data['user_profile_data']->id,
                    'start_order' => $data_order->start_order,
                    'end_order' => $date_now,
                    'member_id' => ($discount_member_id > 0) ? $discount_member_id : NULL,
                    'receipt_number' => $new_receipt_id);

                $bill_id = $this->cashier_model->save('bill', $data_bill);

                 if ($bill_id) {
                    if ($receipt_number != "") {
                        $update_bill = array(
                            'is_refund' => 1,
                            'has_synchronized' => 0,
                            'refund_key' => $refund_data['refund_key']
                        );

                        $this->cashier_model->save('bill', $update_bill, $refund_data['ref_bill_id']);
                        $this->cashier_model->save('bill', array('refund_key' => $refund_data['refund_key']), $bill_id);

                        $this->bill_model->save('refund', array(
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => $this->data['user_id'],
                            'refund_key' => $refund_data['refund_key']
                        ));
                    }
                } 

                if ($data_order->is_delivery == 1) {
                    $percentage = 0;
                    $value = 0;
                    if ($this->_setting['delivery_company'] != 1) {
                        $percentage = $this->_setting['courier_service'];
                        $value = ($delivery_cost != 0 && $this->_setting['courier_service'] != 0 ? $delivery_cost * $this->_setting['courier_service'] / 100 : 0);
                    } else {
                        $get_courier_account = $this->delivery_courier_model->get_one('delivery_courier', $delivery_cost_id);
                        $percentage = $get_courier_account->commission;
                        $value = $grand_total * ($percentage / 100);
                    }
                    $data_bill_courier_service = array(
                        "bill_id" => $bill_id,
                        "courier_service_percentage" => $percentage,
                        "courier_service_value" => $value,
                    );
                    $this->cashier_model->save('bill_courier_service', $data_bill_courier_service);
                    
                    // Account journal : Jasa Kurir
                    if ($this->data['module']['ACCOUNTING'] == 1) {
                        $cost = 0;
                        $account_id = 0;
                        if ($this->_setting['delivery_company'] != 1) {
                            $cost = ($delivery_cost != 0 && $this->_setting['courier_service'] != 0 ? $delivery_cost * $this->_setting['courier_service'] / 100 : 0);
                            $account_id = $this->data['setting']['courier_service_cost_account_id'];
                        } else {
                            $get_courier_account = $this->delivery_courier_model->get_one('delivery_courier', $delivery_cost_id);
                            $cost = $delivery_cost;
                            $account_id = $get_courier_account->account_id;
                        }
                        $param_journal = array(
                            'status' => 'courier_service',
                            'bill_id' => $bill_id,
                            'delivery_cost' => $cost,
                            'account_id' => $account_id
                        );
                        $this->set_journal($param_journal);
                    }
                }

                if ($compliment_total > 0) {
                    $round_total = 0;
                }

                if ($round_total != 0) {
                    $data_bill_info_pembulatan = array(
                        'bill_id' => $bill_id,
                        'info' => "Pembulatan",
                        'amount' => $round_total,
                        'created_at' => $date_now,
                        'created_by' => $this->data['user_profile_data']->id
                    );

                    // Account journal : Pembulatan
                    if ($this->data['module']['ACCOUNTING'] == 1) {
                        $param_round_journal = array(
                            'status' => 'round',
                            'bill_id' => $bill_id,
                            'round_total' => $round_total
                        );
                        $this->set_journal($param_round_journal);
                    }

                    if ($is_round_up) {
                        $data_bill_info_pembulatan['type'] = 1;
                    } else {
                        $data_bill_info_pembulatan['type'] = 2;
                    }
                    
                    $total_credit += $round_total;

                    $result_bill_info_pembulatan = $this->cashier_model->save('bill_information', $data_bill_info_pembulatan);
                }

                // save data kembalian
                if ($compliment_total <= 0) {
                    $kembalian = $customer_payment - $grand_total;
                }
                
                if ($kembalian > 0) {
                    $data_info_kembalian = array(
                        'bill_id'    => $bill_id,
                        'type'       => 3,
                        'info'       => 'Kembalian',
                        'amount'     => $kembalian,
                        'created_at' => $date_now,
                        'created_by' => $this->data['user_profile_data']->id
                    );
                    $this->cashier_model->save('bill_information', $data_info_kembalian);
                }

                $account_data_discount_promo = array();
                //insert pengurang 
                if (!empty($promo_id)) {
                    $promo_id = explode("-", $promo_id);
                    $promo_id = $promo_id[0];

                    $promo_db = $this->order_model->get_all_where("promo_discount", array("id" => $promo_id))[0];
                    $enum_bill_info_db = $this->order_model->get_all_where("enum_bill_information", array("value" => 'promo'))[0];

                    $data_bill_info= array('bill_id' => $bill_id,
                        'type' => 2,
                        'info' => $promo_name,
                        'amount' => $promo_total,
                        'enum_ref_table' => $enum_bill_info_db->id,
                        'enum_ref_id' => $promo_db->id,
                        'created_at' => $date_now,
                        'created_by' => $this->data['user_profile_data']->id
                    );

                    // Account journal : Pengurang (diskon)
                    if ($this->data['module']['ACCOUNTING'] == 1) {
                        $param_disc_journal = array(
                            'status' => 'discount',
                            'bill_id' => $bill_id,
                            'promo_total' => $promo_total
                        );
                        $this->set_journal($param_disc_journal);
                    }

                    $total_debit += $promo_total;

                    $result_bill_info = $this->cashier_model->save('bill_information', $data_bill_info);
                } else {
                    $promo_id = 0;
                }

                if (!empty($promo_cc_id)) {
                    $promo_cc_id = explode("-", $promo_cc_id);
                    $promo_cc_id = $promo_cc_id[0];

                    $promo_cc_db = $this->order_model->get_all_where("promo_cc", array("id" => $promo_cc_id))[0];
                    $enum_bill_info_db = $this->order_model->get_all_where("enum_bill_information", array("value" => 'promo cc'))[0];

                    $data_bill_info = array('bill_id' => $bill_id,
                        'type' => 2,
                        'info' => $promo_cc_name,
                        'amount' => $promo_cc,
                        'enum_ref_table' => $enum_bill_info_db->id,
                        'enum_ref_id' => $promo_cc_db->id,
                        'created_at' => $date_now,
                        'created_by' => $this->data['user_profile_data']->id
                    );

                    // Account journal : Pengurang (promo cc)
                    if ($this->data['module']['ACCOUNTING'] == 1) {
                        $param_promocc_journal = array(
                            'status' => 'promo_cc',
                            'bill_id' => $bill_id,
                            'promo_cc' => $promo_cc
                        );
                        $this->set_journal($param_promocc_journal);
                    }

                    $total_debit += $promo_cc;

                    $result_bill_info = $this->cashier_model->save('bill_information', $data_bill_info);
                } else {
                    $promo_cc_id = 0;
                }

                $account_data_diskon = array();
                if (!empty($discount_member_percentage)) {
                    $data_bill_info = array('bill_id' => $bill_id,
                        'type' => 2,
                        'info' => "Member(" . $discount_member_percentage . "%)",
                        'amount' => $discount_member_total,
                        'created_at' => $date_now,
                        'created_by' => $this->data['user_profile_data']->id,
                        'enum_ref_id'=>$this->input->post('user_unlock_member_bill')
                    );

                    // Account journal : Pengurang (diskon member)
                    if ($this->data['module']['ACCOUNTING'] == 1) {
                        $param_disc_member_journal = array(
                            'status' => 'disc_member',
                            'bill_id' => $bill_id,
                            'disc_member' => $discount_member_total
                        );
                        $this->set_journal($param_disc_member_journal);
                    }

                    $total_debit += $discount_member_total;

                    $result_bill_info = $this->cashier_model->save('bill_information', $data_bill_info);
                }
                
                // get bill
                $order_payment = array();
                $order_payment['reservation'] = $reservation;
                $order_payment['setting'] = $this->data['setting'];
                if ($data_order->is_take_away == '0' && $data_order->is_delivery == '0') {
                    $order_payment['order_mode'] = 'Meja';
                    if ($this->data['setting']['dining_type'] != 2) {
                        $order_payment['order_mode_name'] = $data_table->table_name;
                    } else {
                        $order_payment['order_mode'] = 'Nama';
                        $order_payment['order_mode_name'] = $data_order->customer_name;
                    }
                } else {
                    $order_payment['order_mode'] = 'Nama';
                    $order_payment['order_mode_name'] = $data_order->customer_name;
                }

                $order_payment['order_list'] = $data_bayar;

                $order_payment['change_due'] = $kembalian;

                $order_payment['subtotal'] = $subtotal_1;
                $order_payment['subtotal_2'] = $sub_total_2;
                $order_payment['subtotal_value'] = $subtotal_1;
                $order_payment['grand_total'] = $grand_total;
                $order_payment['customer_cash_payment'] = $customer_cash_payment;
                $new_tax_price = [];

                //bill information info tax
                $tax_total = 0;
                $account_data_tax = array();
                foreach ($taxes as $tax) {
                    $tax_total += $tax['tax_total'];
                    $tax_bill = array();
                    $tax_bill['tax_percentage'] = 0;
                    $tax_bill['value'] = 0;

                    if ($compliment_total > 0 || $pending_bill['is_banquet'] == 1 || $payment_option[0]['type'] == 5) {
                        $tax['tax_total'] = 0;
                    }

                    $tax_bill['tax_value'] = $tax['tax_total'];
                    array_push($new_tax_price, $tax_bill);
                    $data_bill_info = array('bill_id' => $bill_id,
                        'type' => 1,
                        'info' => $tax['tax_name'],
                        'amount' => $tax['tax_total'],
                        'created_at' => $date_now,
                        'created_by' => $this->data['user_profile_data']->id
                    );

                    $result_bill_info = $this->cashier_model->save('bill_information', $data_bill_info);

                    // Account journal : Tax
                    if ($this->data['module']['ACCOUNTING'] == 1) {
                        $param_tax_journal = array(
                            'status' => 'tax',
                            'bill_id' => $bill_id,
                            'acc_id' => $tax['account_id'],
                            'tax' => $tax['tax_total'],
                            'info' => $tax['tax_name']
                        );
                        $this->set_journal($param_tax_journal);
                    }

                    $total_credit += $tax['tax_total'];

                }

                if ($data_order->is_delivery == 1) {

                    $data_bill_info_delivery_cost = array(
                        'type' => ($this->_setting['delivery_company'] != 1) ? 1 : 2,
                        'bill_id' => $bill_id,
                        'info' => "Ongkos Kirim",
                        'amount' => $delivery_cost,
                        'enum_ref_id' => ($this->_setting['delivery_company'] != 1) ? 0 : $delivery_cost_id,
                        'created_at' => $date_now,
                        'created_by' => $this->data['user_profile_data']->id
                    );
                    $this->cashier_model->save('bill_information', $data_bill_info_delivery_cost);

                    // Account journal : Delivery Services
                    if ($this->data['module']['ACCOUNTING'] == 1) {
                        $param_deliv_journal = array(
                            'status' => 'deliv_serv',
                            'bill_id' => $bill_id,
                            'delivery_cost' => $delivery_cost
                        );
                        $this->set_journal($param_deliv_journal);
                    }

                    $total_credit += $delivery_cost;

                }

                $grandtotal_hpp_product = 0;
                $grandtotal_hpp_side_dish = 0;
                $total_cogs = 0;
                $total_price = 0;
                
                foreach ($data_bayar as $product) {   
                    //get menu HPP
                    $total_hpp_product = $product['menu_hpp'];
                    
                    //get total price side dish
                    $menu_data = $this->categories_model->get_one_menu($product['menu_id']);
                    $side_dish = $this->order_model->get_side_dish_by_order_menu($product['product_id'], $menu_data->is_promo);
                    $total_hpp_side_dish = 0;
                    if ($side_dish) {
                        foreach ($side_dish as $sd) { 
                            $total_hpp_side_dish += $sd->price; 
                        }
                    }
					
					if(isset($side_dish[0]) && $side_dish[0]->side_dish_id != 0){
						// with same sidedish
						$order_menu = $this->order_model->get_order_menu_check_sidedish(array(
							'order_menu.order_id' => $order_id, 
							'order_menu.menu_id' => $product['menu_id'],
							'order_menu_side_dish.side_dish_id' => $side_dish[0]->side_dish_id
						), $this->data['setting']['dining_type'], $product['menu_id'], $receipt_number);
					} else {
						// without sidedish
						$order_menu = $this->order_model->get_order_menu_check_sidedish(array(
							'order_menu.order_id' => $order_id, 
							'order_menu.menu_id' => $product['menu_id'],
							'order_menu_side_dish.side_dish_id is null' => NULL
						), $this->data['setting']['dining_type'], $product['menu_id'], $receipt_number);
					}
                 
                    //COGS - bill menu = menu_hpp + price_side_dish
                    if (sizeof($order_menu) >= 1) { 
                        $total_pay = $product['product_amount'];
                        foreach ($order_menu as $key) {
                            $data_bill_menu = array(
                                'bill_id'       => $bill_id,
                                'order_menu_id' => $key->id,
                                'menu_id'       => $product['menu_id'],
                                'menu_name'     => $product['product_name'],
                                'price'         => $product['origin_price'],
                                'cogs'          => $total_hpp_product + $total_hpp_side_dish,
                                'created_at'    => $key->created_at,
                                'created_by'    => $key->created_by,
                                'finished_at'   => $key->finished_at,
                                'use_taxes'     => $product['use_taxes']
                            );
                            $order_menu_paid = array_pop($this->db->query("SELECT IFNULL(SUM(quantity), 0) AS paid_quantity FROM bill_menu JOIN bill ON bill.id = bill_menu.bill_id WHERE order_menu_id = ".$key->id." AND bill.is_refund = 0")->result());
							
                            $paid_quantity = $order_menu_paid->paid_quantity;
                            if (($key->quantity - $paid_quantity) > 0) {
                                if ($paid_quantity > 0) {
                                    if ($paid_quantity < $key->quantity) {
                                        if ($key->quantity - ($total_pay + $paid_quantity) > 0) {
                                            $pay = $total_pay;
                                            $data_bill_menu['quantity'] = $pay;

                                            $total_price += $product['origin_price'];
                                            $bill_menu_id = $this->bill_model->save('bill_menu', $data_bill_menu);

                                            $order_menus = $this->order_model->get_order_menu_by_id($product['product_id']);
                                            foreach ($order_menus as $item) {
                                                $order_cogs = $this->order_menu_inventory_cogs_model->get_all(array('order_menu_id' => $item->id));
                                                array_walk($order_cogs, function ($item) use (&$total_cogs, $bill_menu_id) {
                                                    unset($item->id);
                                                    unset($item->order_menu_id);
                                                    unset($item->uom_id);
                                                    $item->bill_menu_id = $bill_menu_id;
                                                    $total_cogs += $item->cogs;
                                                    $this->order_model->add_bill_cogs($item);
                                                });
                                            }

                                            if ($side_dish) {
                                                foreach ($side_dish as $sd) {
                                                    $data_side_dish = array(
                                                        'side_dish_name'    => $sd->name,
                                                        'side_dish_id'      => $sd->side_dish_id,
                                                        'price'             => $sd->price,
                                                        'bill_menu_id'      => $bill_menu_id,
                                                        'cogs'              => $sd->price,
                                                        'created_at'        => $date_now,
                                                        'created_by'        => $this->data['user_profile_data']->id
                                                    );
                                                    $result_side_dish = $this->cashier_model->save('bill_menu_side_dish', $data_side_dish);
                                                }
                                            }

                                            $total_pay -= $pay;
                                            if ($total_pay <= 0) {
                                                break;
                                            }
                                        } else {
                                            $pay = $key->quantity - $paid_quantity;
                                            $data_bill_menu['quantity'] = $pay;

                                            $total_price += $product['origin_price'];
                                            $bill_menu_id = $this->bill_model->save('bill_menu', $data_bill_menu);

                                            $order_menus = $this->order_model->get_order_menu_by_id($product['product_id']);
                                            foreach ($order_menus as $item) {
                                                $order_cogs = $this->order_menu_inventory_cogs_model->get_all(array('order_menu_id' => $item->id));
                                                array_walk($order_cogs, function ($item) use (&$total_cogs, $bill_menu_id) {
                                                    unset($item->id);
                                                    unset($item->order_menu_id);
                                                    unset($item->uom_id);
                                                    $item->bill_menu_id = $bill_menu_id;
                                                    $total_cogs += $item->cogs;
                                                    $this->order_model->add_bill_cogs($item);
                                                });
                                            }

                                            if ($side_dish) {
                                                foreach ($side_dish as $sd) {
                                                    $data_side_dish = array(
                                                        'side_dish_name'    => $sd->name,
                                                        'side_dish_id'      => $sd->side_dish_id,
                                                        'price'             => $sd->price,
                                                        'bill_menu_id'      => $bill_menu_id,
                                                        'cogs'              => $sd->price,
                                                        'created_at'        => $date_now,
                                                        'created_by'        => $this->data['user_profile_data']->id
                                                    );
                                                    $result_side_dish = $this->cashier_model->save('bill_menu_side_dish', $data_side_dish);
                                                }
                                            }

                                            $total_pay -= $pay;
                                            if ($total_pay <= 0) {
                                                break;
                                            }
                                        }
                                    } else {
                                        $pay = $total_pay;
                                        $data_bill_menu['quantity'] = $pay;

                                        $total_price += $product['origin_price'];
                                        $bill_menu_id = $this->bill_model->save('bill_menu', $data_bill_menu);

                                        $order_menus = $this->order_model->get_order_menu_by_id($product['product_id']);
                                        foreach ($order_menus as $item) {
                                            $order_cogs = $this->order_menu_inventory_cogs_model->get_all(array('order_menu_id' => $item->id));
                                            array_walk($order_cogs, function ($item) use (&$total_cogs, $bill_menu_id) {
                                                unset($item->id);
                                                unset($item->order_menu_id);
                                                unset($item->uom_id);
                                                $item->bill_menu_id = $bill_menu_id;
                                                $total_cogs += $item->cogs;
                                                $this->order_model->add_bill_cogs($item);
                                            });
                                        }

                                        if ($side_dish) {
                                            foreach ($side_dish as $sd) {
                                                $data_side_dish = array(
                                                    'side_dish_name'    => $sd->name,
                                                    'side_dish_id'      => $sd->side_dish_id,
                                                    'price'             => $sd->price,
                                                    'bill_menu_id'      => $bill_menu_id,
                                                    'cogs'              => $sd->price,
                                                    'created_at'        => $date_now,
                                                    'created_by'        => $this->data['user_profile_data']->id
                                                );
                                                $result_side_dish = $this->cashier_model->save('bill_menu_side_dish', $data_side_dish);
                                            }
                                        }

                                        $total_pay -= $pay;
                                        if ($total_pay <= 0) {
                                            break;
                                        }
                                    }
                                } else {
                                    if ($total_pay < $key->quantity) {
                                        $pay = $total_pay;
                                        $data_bill_menu['quantity'] = $pay;

                                        $total_price += $product['origin_price'];
                                        $bill_menu_id = $this->bill_model->save('bill_menu', $data_bill_menu);

                                        $order_menus = $this->order_model->get_order_menu_by_id($product['product_id']);
                                        foreach ($order_menus as $item) {
                                            $order_cogs = $this->order_menu_inventory_cogs_model->get_all(array('order_menu_id' => $item->id));
                                            array_walk($order_cogs, function ($item) use (&$total_cogs, $bill_menu_id) {
                                                unset($item->id);
                                                unset($item->order_menu_id);
                                                unset($item->uom_id);
                                                $item->bill_menu_id = $bill_menu_id;
                                                $total_cogs += $item->cogs;
                                                $this->order_model->add_bill_cogs($item);
                                            });
                                        }

                                        if ($side_dish) {
                                            foreach ($side_dish as $sd) {
                                                $data_side_dish = array(
                                                    'side_dish_name'    => $sd->name,
                                                    'side_dish_id'      => $sd->side_dish_id,
                                                    'price'             => $sd->price,
                                                    'bill_menu_id'      => $bill_menu_id,
                                                    'cogs'              => $sd->price,
                                                    'created_at'        => $date_now,
                                                    'created_by'        => $this->data['user_profile_data']->id
                                                );
                                                $result_side_dish = $this->cashier_model->save('bill_menu_side_dish', $data_side_dish);
                                            }
                                        }

                                        $total_pay -= $pay;
                                        if ($total_pay <= 0) {
                                            break;
                                        }
                                    } else {
                                        $pay = $key->quantity;
                                        $data_bill_menu['quantity'] = $pay;

                                        $total_price += $product['origin_price'];
                                        $bill_menu_id = $this->bill_model->save('bill_menu', $data_bill_menu);

                                        $order_menus = $this->order_model->get_order_menu_by_id($product['product_id']);
                                        foreach ($order_menus as $item) {
                                            $order_cogs = $this->order_menu_inventory_cogs_model->get_all(array('order_menu_id' => $item->id));
                                            array_walk($order_cogs, function ($item) use (&$total_cogs, $bill_menu_id) {
                                                unset($item->id);
                                                unset($item->order_menu_id);
                                                unset($item->uom_id);
                                                $item->bill_menu_id = $bill_menu_id;
                                                $total_cogs += $item->cogs;
                                                $this->order_model->add_bill_cogs($item);
                                            });
                                        }

                                        if ($side_dish) {
                                            foreach ($side_dish as $sd) {
                                                $data_side_dish = array(
                                                    'side_dish_name'    => $sd->name,
                                                    'side_dish_id'      => $sd->side_dish_id,
                                                    'price'             => $sd->price,
                                                    'bill_menu_id'      => $bill_menu_id,
                                                    'cogs'              => $sd->price,
                                                    'created_at'        => $date_now,
                                                    'created_by'        => $this->data['user_profile_data']->id
                                                );
                                                $result_side_dish = $this->cashier_model->save('bill_menu_side_dish', $data_side_dish);
                                            }
                                        }

                                        $total_pay -= $pay;
                                        if ($total_pay <= 0) {
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    $grandtotal_hpp_product += $total_hpp_product * $product['product_amount'];
                    $grandtotal_hpp_side_dish += $total_hpp_side_dish * $product['product_amount'];

                }

                // added and modified by: M. Tri R
                // untuk mengecek account inventory dari masing-masing menu, side dish, dan turunan
                $account = $this->order_model->get_account_by_inventory($bill_id);
                
                $total_by_inventory = $this->order_model->get_total_credit_by_inventory($bill_id);
                $total_credit += $total_by_inventory[0]['total'];

                // Account journal : Pendapatan Inventory
                if ($this->data['module']['ACCOUNTING'] == 1) {
                    $param_profit_inventory = array(
                        'status' => 'inventory',
                        'bill_id' => $bill_id,
                        'account' => $account
                    );
                    $this->set_journal($param_profit_inventory);

                    if (!empty($side_dish)) {
                        $account_side_dish = $this->order_model->get_account_by_inventory_side_dish($bill_id);
                        $param_profit_inventory = array(
                            'status' => 'inventory',
                            'bill_id' => $bill_id,
                            'account' => $account_side_dish
                        );
                        $this->set_journal($param_profit_inventory);

						if(!empty($account_side_dish)){
							$total_by_side_dish = array_pop($account_side_dish);
							$total_credit += $total_by_side_dish->total;
						}
                    }
                }

                //update cogs bill 
                $data_update_bill = array(
                    "total_cogs" => $grandtotal_hpp_product + $grandtotal_hpp_side_dish
                );

                $this->cashier_model->save('bill', $data_update_bill, $bill_id);

                foreach ($payment_option as $option) {
                    $account_data_detail = array(
                        'account_data_id' => 0,
                        'info' => '',
                        'store_id' => $this->_setting['store_id'],
                        'description' => '',
                        'created_at' => date("Y-m-d H:i:s")
                    );

                    // Account journal : Payment option
                    if ($this->data['module']['ACCOUNTING'] == 1) {
                        $param_payment_option_journal = array(
                            'status' => 'payment_option',
                            'bill_id' => $bill_id,
                            'payment' => $option
                        );
                        $this->set_journal($param_payment_option_journal);
                    }

                    $data_bill_payment =
                        array('bill_id' => $bill_id,
                            'payment_option' => $option['type'],
                            'amount' => $option['amount'],
                            'info' => $option['code'],
                            'created_at' => $date_now,
                            'created_by' => $this->data['user_profile_data']->id,
                        );


                    if ($option['type'] == 2) {
                        $data_bill_payment['bank_account_id'] = $option['bankId'];
                        $data_bill_payment['card_type_id'] = $option['cardTypeId'];                        
                        $account_data_detail['info'] = $option['code'];
                        $account_data_detail['description'] = 'Debit';
                    } elseif ($option['type'] == 3) {
                        $data_bill_payment['bank_account_id'] = $option['bankId'];
                        $data_bill_payment['card_type_id'] = $option['cardTypeId'];
                        $account_data_detail['info'] = $option['code'];
                        $account_data_detail['description'] = 'Kredit';
                    } elseif ($option['type'] == 4) {
                        if ($this->data['setting']["voucher_method"] == 1) {
                            //update usage voucher
                            $data_voucher = array(
                                'status' => 1,
                                'usage_date' => $date_now,
                                'created_by' => $this->data['user_profile_data']->id
                            );

                            $result_voucher = $this->cashier_model->update_where('voucher', $data_voucher, array("code" => $option['code']));
                        } else {

                            $voucher = $this->order_model->get_voucher_code($option['code'],$voucher_quantity);

                            foreach ($voucher as $key) {

                            $data_bill_payment2 =
                                array('bill_id' => $bill_id,
                                    'payment_option' => $option['type'],
                                    'amount' => $key['amount'],
                                    'info' => $key['code'],
                                    'created_at' => $date_now,
                                    'created_by' => $this->data['user_profile_data']->id,
                                );
                                
                            $this->cashier_model->save('bill_payment', $data_bill_payment2);
                            }


                            if ($voucher_quantity != 0) {
                                $where = array(
                                    "voucher_group_id" => $option['code'],
                                    "status" => 0
                                );
                                $result_voucher = $this->order_model->update_voucher_with_limit($where, $voucher_quantity);
                            }
                        }

                    } elseif ($option['type'] == 5) {
                        $data_compliment_usage = array(
                            'user_id' => $option['code'],
                            'created_at' => $date_now,
                            'created_by' => $this->data['user_profile_data']->id,
                            'amount' => $compliment_total,
                            'created_by' => $this->data['user_profile_data']->id
                        );
                        $data_bill_payment['amount'] = $compliment_total;
                        $result_compliment = $this->cashier_model->save('compliment_usage', $data_compliment_usage);

                        $data_bill_info_compliment = array(
                            'bill_id' => $bill_id,
                            'info' => "Compliment",
                            'amount' => $compliment_total,
                            'created_at' => $date_now,
                            'created_by' => $this->data['user_profile_data']->id,
                            'type' => $option['type']
                        );
                        $this->cashier_model->save('bill_information', $data_bill_info_compliment);

                    } elseif ($option['type'] == 11) {
                        $data_bill_payment['bank_account_id'] = $option['bankId'];
                        $data_bill_payment['card_type_id'] = $option['cardTypeId'];
                        $account_data_detail['info'] = $option['code'];
                        $account_data_detail['description'] = 'Flazz';
                    }

                    $total_debit += $option['amount'];
                    

                    if ($option['type'] != 4 || $this->data['setting']["voucher_method"] == 1) {
                        $result_bill_payment = $this->cashier_model->save('bill_payment', $data_bill_payment);
                    }

                    if ($this->data['setting']['revenue_sharing'] > 0) {
                        if ($option['type'] != 5) {
                            $data_bill_info_sharing = array('bill_id' => $bill_id,
                                'type' => 4,
                                'info' => "Sharing (" . $this->data['setting']['revenue_sharing'] . "%)",
                                'amount' => ($this->data['setting']['revenue_sharing'] / 100) * $sub_total_2,
                                'created_at' => $date_now,
                                'created_by' => $this->data['user_profile_data']->id
                            );

                            $result_bill_info = $this->cashier_model->save('bill_information', $data_bill_info_sharing);
                        }   
                    }

                }

                if ($total_debit > $total_credit) {
                    $pendapatan_lain_lain = $total_debit - $total_credit;

                    // Account journal : Pendapatan lain-lain
                    if ($this->data['module']['ACCOUNTING'] == 1) {
                        $param_other_journal = array(
                            'status' => 'lain-lain',
                            'bill_id' => $bill_id,
                            'credit' => $pendapatan_lain_lain
                        );
                        $this->set_journal($param_other_journal);
                    }
                }

                $this->load->model("inventory_model");
                $hpp=$this->inventory_model->get_hpp_from_bill($bill_id);
                $bill_hpp=0;
                if(sizeof($hpp)>0){
                    $bill_hpp=$hpp->hpp;
                }
                if($bill_hpp>0){
                    // Account journal : HPP
                    if ($this->data['module']['ACCOUNTING'] == 1) {
                        $param_hpp = array(
                            'status' => 'hpp',
                            'bill_id' => $bill_id,
                            'bill_hpp' => $bill_hpp
                        );
                        $this->set_journal($param_hpp);
                    }
                }

                $order_payment['tax_price'] = $new_tax_price;

                $order_payment['bill'] = $this->cashier_model->get_by('bill', $bill_id);
                $order_payment['bill_plus'] = $this->cashier_model->get_all_where('bill_information', array('bill_id' => $bill_id, 'type' => 1));
                $order_payment['bill_minus'] = $this->cashier_model->get_all_where('bill_information', array('bill_id' => $bill_id, 'type' => 2));
                $order_payment['bill_payment'] = $this->order_model->get_bill_payment($bill_id);

                $return_data['status'] = TRUE;
                $return_data['url_redir'] = base_url('cashier/takeaway');

                $order_payment['store_data'] = $this->data['data_store'];
                $order_payment['customer_data'] = ($pending_bill['amount'] > 0) ? $pending_bill['code_name'] : $pending_bill_employee['code_name'];
                $order_payment['customer_phone'] = $data_bill['customer_phone'];
                $order_payment['customer_address'] = $data_bill['customer_address'];

                $habis = false;
                $table_status = 3;

                if ($receipt_number != "" && $data_order->order_status == 1) {
                    $table_status = 1;
                }

                if ($receipt_number == "") {
                    $data_check = $this->order_model->get_calculate_total_order_bill($order_id, TRUE);
                    $product_amount = array_pop($data_bayar)['product_amount'];
                    //order selesai ketika semua item sudah dibayar
                    
                    if ($data_check->sum_quantity - $data_check->quantity_bill == 0) {
                        $habis = TRUE;
                        $table_status = 1;
                        $this->session->set_userdata('order_id_dine_in', '');
                        $data_order_save = array('end_order' => $date_now, 'order_status' => 1);
                        if ($this->data['setting']['dining_type'] != 3) {
                            if ($this->data['setting']['cleaning_process'] == 1) $table_status = 7;
                            $result_data_order = $this->cashier_model->save('order', $data_order_save, $order_id);
                        } else {
                            $table_status = 3;
                        }
                        unset($data_order_save['order_status']);
                        $result_data_bill = $this->cashier_model->save('bill', $data_order_save, $bill_id);

                        $return_data['url_redir'] = base_url('cashier/takeaway');

                    } else {
                        $count_status_new = $this->order_model->get_count_cooking_status_order($order_id, 0)->quantity;
                        if ($count_status_new > 0)
                            $table_status = 4;
                    }
                }

                // create dine in order in
                if ($data_order->table_id != 0) {
                    $return_data['url_redir'] = base_url('table/order_dine_in/' . $order_id);
                    
                    $data_update = array('table_status' => $table_status);
                    if ($receipt_number == "") {
                        $result_data_order = $this->cashier_model->save('table', $data_update, $data_order->table_id);
                    }

                    $status_name = $this->order_model->get_one('enum_table_status', $table_status)->status_name;

                    $arr_merge = $this->order_model->get_merge_table_byparent($data_order->table_id);
                    $this->update_table_merge($arr_merge, $data_update);
                    foreach ($arr_merge as $key => $row) {
                        $row->status_class = create_shape_table($row->table_shape, $status_name);
                    }
                    //order selesai ketika semua item sudah dibayar
                    if ($habis) {

                        $return_data['url_redir'] = base_url('table');
                        $this->order_model->delete_by_limit('table_merge', array('parent_id' => $data_order->table_id), 0);

                    }

                    $return_data['arr_merge_table'] = $arr_merge;
                    $return_data['number_guest'] = $data_table->customer_count;
                    $return_data['table_status'] = $table_status;
                    $return_data['table_id'] = $data_order->table_id;
                    $return_data['status_name'] = $status_name;
                    $return_data['order_id'] = $order_id;

                    $return_data['status_class'] = create_shape_table($data_table->table_shape, $status_name);

                }
                
                if ($this->db->trans_status() === FALSE) {
                    $return_data['status'] = FALSE;
                    $this->db->trans_rollback();
                } else {
                    $this->db->trans_commit();
                }
                
                if ($data_order->is_delivery == 1) {
                    $return_data['url_redir'] = base_url('cashier/delivery');
                }
                $order_payment['data_order'] = $data_order;
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

                    $order_payment['header_bill'] = $this->order_model->get_all_where("template_global", array("reff" => "HEADER_BILL"));
                    if (sizeof($order_payment['header_bill']) > 0) $order_payment['header_bill'] = $order_payment['header_bill'][0];

                    $order_payment['footer_bill'] = $this->order_model->get_all_where("template_global", array("reff" => "FOOTER_BILL"));
                    if (sizeof($order_payment['footer_bill']) > 0) $order_payment['footer_bill'] = $order_payment['footer_bill'][0];


                    

                    $order_payment['setting'] = $this->data['setting'];                    
                    if ($this->data['setting']['printer_format'] == 2) {
                      
                        if ($printer_obj->printer_width == 'generic') {
                            @print_checkout_bill2_generic($printer_location, $order_payment, $this->data['user_profile_data'], FALSE, FALSE, $printer_setting);
                        } else {
                            @print_checkout_bill2($printer_location, $order_payment, $this->data['user_profile_data'], FALSE, FALSE, $printer_setting);
                        }
                    } else {
                        
                        if ($printer_obj->printer_width == 'generic') {
                            @print_checkout_bill_generic($printer_location, $order_payment, $this->data['user_profile_data'], FALSE, FALSE, $printer_setting);
                        } else {
                            @print_checkout_bill($printer_location, $order_payment, $this->data['user_profile_data'], FALSE, FALSE, $printer_setting);
                        }
                    }
                }

                $return_data['url_redir'] = ($data_order->is_take_away == 1 ? base_url("cashier/takeaway") : ($data_order->is_delivery == 1 ? base_url('cashier/delivery') : base_url('table')));
                echo json_encode($return_data);
            } else {
                $return_data['status'] = FALSE;
                echo json_encode($return_data);
            }            
        }
    }

    public function print_list_menu()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model("order_model");
            $this->load->helper(array('printer'));
            $order_id = $this->input->post('order_id');
            $data = array();
            $data['setting'] = $this->data['setting'];
            $data['data_store'] = $this->data['data_store'];
            $data['store_name'] = $this->data['data_store']->store_name;
            $data['order'] = $this->order_model->get_by_order_id($order_id);            
            $data['order_lists'] = $this->order_model->get_order_menu_by_order($order_id);
            foreach ($data['order_lists'] as $o) {
                $options = $this->order_model->get_option_by_order_menu($o->order_menu_id);
                $o->options = $options;
                $side_dish = $this->order_model->get_side_dish_by_order_menu($o->order_menu_id, $o->is_promo);
                $o->side_dishes = $side_dish;
            }

            if ($this->data['setting']['auto_checker'] == 1) {

                //get printer checker/service
                $this->load->model("setting_printer_model");
                $printer_arr_obj = $this->setting_printer_model->get_printer_by_enum_printer_type(array("name_type"=>"printer_checker_service"));
                foreach ($printer_arr_obj as $value) {
                    //get all tables in setting printer
                    $printer_tables = $this->order_model->get_all_where("setting_printer_table", array('printer_id'=>$value->id) );
                    foreach ($printer_tables as $key) {
                        //check table in list printer table
                        if ($data['order']->table_id == $key->table_id) {
                            //build object printer setting for printer helper
                            $printer_setting = new stdClass();
                            $printer_setting->id = $value->id;
                            $printer_setting->name = $value->alias_name;
                            $printer_setting->value = $value->name_printer;
                            $printer_setting->default = $value->logo;
                            $printer_setting->description = $value->printer_width;

                            print_list_menu($value->name_printer, $data, $this->data['user_profile_data'], $printer_setting);
                        }
                    }                        
                }
            } 
        }
    }

    public function print_preview_bill()
    {
        if ($this->input->is_ajax_request()) {
            $order_id = $this->input->post('order_id');
            $data_bayar = $this->input->post('data_bayar');
            $payment_total = $this->input->post('payment_total');
            $tax_total = $this->input->post('tax_total');
            $promo_total = $this->input->post('promo_total');
            $promo_cc = $this->input->post('promo_cc');
            $promo_id = $this->input->post('promo_id');
            $promo_cc_id = $this->input->post('promo_cc_id');
            $promo_name = $this->input->post('promo_name');
            $promo_cc_name = $this->input->post('promo_cc_name');
            $customer_payment = $this->input->post('customer_payment');
            $payment_option = $this->input->post('payment_option');
            $compliment_total = $this->input->post('compliment_total');
            $discount_member_id = $this->input->post('discount_member_id');
            $discount_member_percentage = $this->input->post('discount_member_percentage');
            $sub_total_2 = $this->input->post('sub_total_2');
            $discount_member_total = $this->input->post('discount_member_total');
            $taxes = $this->input->post('taxes');
            $taxes = json_decode($taxes, true);
            $grand_total = $this->input->post('grand_total');
            $customer_cash_payment = $this->input->post('customer_cash_payment');
            $pending_bill = json_decode($this->input->post('pending_bill'), true);
            $delivery_cost_id = $this->input->post('delivery_cost_id');
            $delivery_cost = $this->input->post('delivery_cost');
            $round_total = $this->input->post('round_total');
            $is_round_up = $this->input->post('is_round_up');
            $down_payment = $this->input->post('down_payment');

            if ($compliment_total > 0) {
                $round_total = 0;
            }

            $date_now = date("Y-m-d H:i:s");
            $payment_option = json_decode($payment_option, true);

            $subtotal_1 = $payment_total + $promo_total + $promo_cc + $discount_member_total;
            $data_bayar = json_decode($data_bayar, true);

            $order_payment = array();
            $order_payment['setting'] = $this->data['setting'];
            $order_payment['grand_total'] = $grand_total;

            $data_order = $this->order_model->get_by_order_id($order_id);
            $reservation = $this->order_model->get_one('reservation', $data_order->reservation_id);
            $order_payment['reservation'] = $reservation;
            if ($data_order->is_take_away == '0' && $data_order->is_delivery == '0') {
                $data_table = $this->order_model->get_one('table', $data_order->table_id);
                $order_payment['order_mode'] = 'Meja';
                $order_payment['order_mode_name'] = $data_table->table_name;
            } else {
                $order_payment['order_mode'] = 'Nama';
                $order_payment['order_mode_name'] = $data_order->customer_name;

            }


            //bill information info taxc
            $order_payment['bill_plus'] = array();

            $i = 0;
            foreach ($taxes as $tax) {

                $tax_bill = array();

                if ($compliment_total > 0 || $pending_bill['amount'] > 0 || $payment_option[0]['type'] == 5) {
                    $tax['tax_total'] = 0;
                }

                $order_payment['bill_plus'][$i] = new stdClass();
                $order_payment['bill_plus'][$i]->info = $tax['tax_name'];
                $order_payment['bill_plus'][$i]->amount = $tax['tax_total'];
                $i++;
            }
            if ($data_order->is_delivery == 1) {
                $order_payment['bill_plus'][$i] = new stdClass();
                $order_payment['bill_plus'][$i]->info = "Ongkir";
                $order_payment['bill_plus'][$i]->amount = $delivery_cost;
            }

            $order_payment['bill_minus'] = array();
            $i = 0;
            if (!empty($data_bayar)) {
                //insert pengurang 
                if (!empty($promo_id)) {
                    $promo_id = explode("-", $promo_id);
                    $promo_id = $promo_id[0];
                    $order_payment['bill_minus'][$i] = new stdClass();
                    $order_payment['bill_minus'][$i]->info = $promo_name;
                    $order_payment['bill_minus'][$i]->amount = $promo_total;

                    $i++;

                }

                if (!empty($promo_cc_id)) {
                    $promo_cc_id = explode("-", $promo_cc_id);
                    $promo_cc_id = $promo_cc_id[0];
                    $order_payment['bill_minus'][$i] = new stdClass();
                    $order_payment['bill_minus'][$i]->info = "Promo CC"; //$promo_cc_name;
                    $order_payment['bill_minus'][$i]->amount = $promo_cc;

                    $i++;

                }

                if (!empty($discount_member_percentage)) {

                    $order_payment['bill_minus'][$i] = new stdClass();
                    $order_payment['bill_minus'][$i]->info = "Member (" . $discount_member_percentage . "%)";
                    $order_payment['bill_minus'][$i]->amount = $discount_member_total;

                    $i++;

                }


                $order_payment['bill_payment'] = array();
                $i = 0;
                foreach ($payment_option as $option) {
                    $payment_method = $this->order_model->get_by('enum_payment_option', $option['type'], 'id');
                    $order_payment['bill_payment'][$i] = new stdClass();
                    $order_payment['bill_payment'][$i]->value = $payment_method->value;
                    $order_payment['bill_payment'][$i]->amount = $option['amount'];
                    if ($pending_bill['amount'] > 0) {
                        $option['code'] = $pending_bill['code_name'];
                    }
                    $order_payment['bill_payment'][$i]->info = $option['code'];
                    $i++;

                }
                // get bill
                $order_payment['order_list'] = $data_bayar;

                if ($compliment_total > 0) {
                    $order_payment['change_due'] = 0;
                } else {
                    $customer_payment += ($down_payment != 0) ? $down_payment : 0;
                    $order_payment['change_due'] = $customer_payment - $grand_total;
                }
                
                $order_payment['subtotal'] = $subtotal_1;
                $order_payment['subtotal_2'] = $sub_total_2;
                $order_payment['subtotal_value'] = $subtotal_1;
                $order_payment['round_total'] = $round_total;
                $order_payment['total_price'] = $grand_total;
                $order_payment['store_data'] = $this->data['data_store'];
                $order_payment['customer_cash_payment'] = $customer_cash_payment;
                $order_payment['customer_data'] = $pending_bill['code_name'];
                $order_payment['data_order'] = $data_order;

                $this->load->helper(array('printer'));
                $this->load->model("setting_printer_model");

                //get printer cashier
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

                    $order_payment['header_bill'] = $this->order_model->get_all_where("template_global", array("reff" => "HEADER_BILL"));
                    if (sizeof($order_payment['header_bill']) > 0) $order_payment['header_bill'] = $order_payment['header_bill'][0];
                    
                    $order_payment['footer_bill'] = $this->order_model->get_all_where("template_global", array("reff" => "FOOTER_BILL"));
                    if (sizeof($order_payment['footer_bill']) > 0) $order_payment['footer_bill'] = $order_payment['footer_bill'][0];

                     $order_payment['bill_temporary'] = $this->order_model->get_all_where("template_global", array("reff" => "BILL_TEMPORARY"));
                    if (sizeof($order_payment['bill_temporary']) > 0){
                        $order_payment['bill_temporary'] = $order_payment['bill_temporary'][0];
                    } 
                    $order_payment['setting'] = $this->data['setting'];
                    if ($this->data['setting']['printer_format'] == 2) {
                        if ($printer_obj->printer_width == 'generic') {
                            @print_checkout_bill2_generic($printer_location, $order_payment, $this->data['user_profile_data'], FALSE, TRUE, $printer_setting);
                        } else {
                            @print_checkout_bill2($printer_location, $order_payment, $this->data['user_profile_data'], FALSE, TRUE, $printer_setting);
                        }                        
                    } else {
                        if ($printer_obj->printer_width == 'generic') {
                            @print_checkout_bill_generic($printer_location, $order_payment, $this->data['user_profile_data'], FALSE, TRUE, $printer_setting);
                        } else {
                            @print_checkout_bill($printer_location, $order_payment, $this->data['user_profile_data'], FALSE, TRUE, $printer_setting);
                        }                        
                    }
                }

                $return_data['status'] = TRUE;

            } else {
                $return_data['status'] = FALSE;
                echo json_encode($return_data);
            }


        }
    }


    public function print_pending_bill()
    {
        if ($this->input->is_ajax_request()) {
            $order_id = $this->input->post('order_id');
            $data_order = $this->order_model->get_one('order', $order_id);

            if (!empty($data_order)) {
                // get bill
                $order_payment = $this->order_model->calculate_total_order($order_id, TRUE);
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

                    if ($printer_obj->printer_width == 'generic') {
                        print_checkout_bill_generic($printer_location, $order_payment, $this->data['user_profile_data'], TRUE, TRUE, $printer_setting);
                    } else {
                        print_checkout_bill($printer_location, $order_payment, $this->data['user_profile_data'], TRUE, TRUE, $printer_setting);
                    }                    
                }

                $return_data['status'] = TRUE;
                echo json_encode($return_data);
            } else {
                $return_data['status'] = FALSE;
                echo json_encode($return_data);
            }


        }
    }


    private function list_order_payment($order_payment)
    {
        $order_list_data = '';
        $order_bill_data = '';
        $total_tax = 0;
        if (!empty($order_payment)) {
            foreach ($order_payment['order_list'] as $order) {
                $order_list_data .= '<tr class="tOrder ' . $order->order_menu_id . '" id="' . $order->order_menu_id . '" menu_id ="' . $order->menu_id . '"  data-category="' . $order->category_id . '" data-use_taxes="' . $order->use_taxes . '">';
                $order_list_data .= '<td style="padding-left:10px;">';
                $order_list_data .= $order->menu_name;

                foreach ($order->side_dish_list as $sdh) {
                    $order_list_data .= ' <br/>-- ' . $sdh->name . ' (' . $sdh->origin_price . ')';

                }

                $order_list_data .= '</td>';
                $order->quantity = (empty($order->quantity)) ? $order->quantity : $order->quantity;
                $order_list_data .= '<td class="border-side tb-align-right">' . $order->quantity . '</td>';
                $order_list_data .= '<td class="tb-align-right price-menu" data-price="' . $order->origin_price . '" data-menu-hpp="' . $order->menu_hpp . '"  style="padding-right: 10px">Rp ' . number_format($order->menu_price, 0, "", ".") . '';

                $order_list_data .= '</td>';

                $order_list_data .= '<td style="display: none">' . $order->menu_id . '</td>';
                $order_list_data .= '<td style="display: none">' . $order->note . '</td>';
                $order_list_data .= '<td style="display: none"></td>';
                $order_list_data .= '<td style="display: none"></td>';
                $order_list_data .= '<td style="display: none">' . $order->order_menu_id . '</td>';
                $order_list_data .= '<td style="display: none">' . $order->cooking_status . '</td>';
                $order_list_data .= '<td style="display: none">' . $order->process_status . '</td>';
                $order_list_data .= '<td style="display: none">' . $order->quantity . '</td>';
                $order_list_data .= '</tr>';


                # code...

                // foreach ($order->side_dish_list as $sdh ) {
                //     $order_list_data .= '<tr>';
                //     $order_list_data .= '<td>-- ' . $sdh->name . '</td>';
                //     $order_list_data .= '<td class="border-side tb-align-center">' . $sdh->quantity . '</td>';
                //     $order_list_data .= '<td class="tb-align-right" data-price= "' . $sdh->origin_price . '" style="padding-right: 10px" >Rp ' . $sdh->price . '</td>';
                //     $order_list_data .= '</tr>';
                // }

            }

            if ($order_payment['subtotal'] != '0') {
                $order_bill_data .= '<tr>';
                $order_bill_data .= '<td style="width:30%"></td>';
                $order_bill_data .= '<td style="width:30%"><b>Subtotal 1</b></td>';
                $order_bill_data .= '<td style="width:40%" id="subtotal-price" class="tb-align-right" data-price="' . $order_payment['subtotal'] . '">Rp ' . $order_payment['subtotal'] . '</td>';
                $order_bill_data .= '</tr>';
                $order_bill_data .= '<tr class="discount-order-list">';
                $order_bill_data .= '<td class="border-bottom</td>';
                $order_bill_data .= '<td style="width:30%"></td>';
                $order_bill_data .= '<td style="width:30%">---------------</td>';
                $order_bill_data .= '</tr>';
                // $order_bill_data .= '<tr>';
                // $order_bill_data .= '<td style="width:40%"></td>';
                // $order_bill_data .= '<td style="width:30%"><b>Diskon ('.$order_payment['discount_percent'].'%) '.$order_payment['discount_name'].'</b></td>';
                // $order_bill_data .= '<td style="width:30%" id="discount-total" data-price="0" data-name="" class="tb-align-right">Rp ' . number_format($order_payment['discount_total'],0,'', '.') . '</td>';
                // $order_bill_data .= '</tr>';
                $total_tax = 0;

                // $order_bill_data .= '<tr>';
                // $order_bill_data .= '<td></td>';
                // $order_bill_data .= '<td><b>Compliment</b></td>';
                // $order_bill_data .= '<td id="sub-total-2" class="tb-align-right">';
                // $order_bill_data .= '</td>';
                // $order_bill_data .= '</tr>';

                // $order_bill_data .= '<tr>';
                // $order_bill_data .= '<td></td>';
                // $order_bill_data .= '<td><b>Promo</b></td>';
                // $order_bill_data .= '<td id="sub-total-2" class="tb-align-right">';
                // $order_bill_data .= '</td>';
                // $order_bill_data .= '</tr>';

                $order_bill_data .= '<tr>';
                $order_bill_data .= '<td></td>';
                $order_bill_data .= '<td><b>Sub Total 2</b></td>';
                $order_bill_data .= '<td id="sub-total-2" class="tb-align-right">';
                $order_bill_data .= '</td>';
                $order_bill_data .= '</tr>';

                $order_bill_data .= '<tr>';
                $order_bill_data .= '<td class="border-bottom</td>';
                $order_bill_data .= '<td style="width:30%"></td>';
                $order_bill_data .= '<td style="width:30%"> ---------------</td>';
                $order_bill_data .= '</tr>';


                foreach ($order_payment['tax_price'] as $tax) {

                    $order_bill_data .= '<tr>';
                    $order_bill_data .= '<td></td>';
                    $order_bill_data .= '<td><b>' . $tax['name'] . '</b></td>';
                    $order_bill_data .= '<td id="tax-price" account-id="' . $tax['account_id'] . '" percentage="' . $tax['tax_percentage'] . '" tax-origin-name="' . $tax['origin_name'] . '" tax-name="' . $tax['name'] . '" service="' . $tax['is_service'] . '" class="tb-align-right">Rp ' . $tax['value'] . '</td>';
                    $order_bill_data .= '</tr>';
                    $total_tax += $tax['value'];

                }

                foreach ($order_payment['extra_charge_price'] as $xtra) {

                    $order_bill_data .= '<tr>';
                    $order_bill_data .= '<td></td>';
                    $order_bill_data .= '<td><b>' . $xtra['name'] . '</b></td>';
                    $order_bill_data .= '<td class="tb-align-right">Rp ' . $xtra['value'] . '</td>';
                    $order_bill_data .= '</tr>';

                }
                if (isset($order_payment['delivery_cost'])) {
                    $order_bill_data .= '<tr>';
                    $order_bill_data .= '<td></td>';
                    $order_bill_data .= '<td><b>Ongkir</b></td>';
                    $courier_service = $this->data['setting']['courier_service'];
                    // $order_payment['delivery_cost']=$order_payment['delivery_cost']-($courier_service*$order_payment['delivery_cost']/100);
                    $order_bill_data .= '<td id="delivery_cost" cost="' . $order_payment['delivery_cost'] . '" class="tb-align-right">Rp ' . number_format($order_payment['delivery_cost'], 0, "", ".") . '</td>';
                    $order_bill_data .= '</tr>';
                }
                $order_bill_data .= '<tr>';
                $order_bill_data .= '<td></td>';
                $order_bill_data .= '<td><b>Pembulatan</b></td>';
                $order_bill_data .= '<td id="pembulatan" class="tb-align-right">';
                $order_bill_data .= '</td>';
                $order_bill_data .= '</tr>';

                $order_bill_data .= '<tr  >';
                $order_bill_data .= '<td class="border-bottom</td>';
                $order_bill_data .= '<td style="width:30%" class="tb-align-right"></td>';
                $order_bill_data .= '<td style="width:30%"> ---------------</td>';
                $order_bill_data .= '</tr>';

                //  $order_bill_data .= '<tr>';
                // $order_bill_data .= '<td></td>';
                // $order_bill_data .= '<td><b>Pembulatan</b></td>';
                // $order_bill_data .= '<td id="sub-total-2" class="tb-align-right">';
                // $order_bill_data .= '</td>';
                // $order_bill_data .= '</tr>';

                // $order_bill_data .= '<tr>';
                // $order_bill_data .= '<td></td>';
                // $order_bill_data .= '<td><b>Payment 1</b></td>';
                // $order_bill_data .= '<td id="sub-total-2" class="tb-align-right">';
                // $order_bill_data .= '</td>';
                // $order_bill_data .= '</tr>';

                // $order_bill_data .= '<tr>';
                // $order_bill_data .= '<td></td>';
                // $order_bill_data .= '<td><b>Payment 2</b></td>';
                // $order_bill_data .= '<td id="sub-total-2" class="tb-align-right">';
                // $order_bill_data .= '</td>';
                // $order_bill_data .= '</tr>';

                if (isset($order_payment['reservation']) && sizeof($order_payment['reservation']) > 0 && $order_payment['reservation']->down_payment > 0) {
                    $order_bill_data .= '<tr>';
                    $order_bill_data .= '<td></td>';
                    $order_bill_data .= '<td><b>DP</b></td>';
                    $order_bill_data .= '<td id="reservation_down_payment" cost="' . $order_payment['reservation']->down_payment . '" class="tb-align-right">Rp ' . number_format($order_payment['reservation']->down_payment, 0, "", ".") . '</td>';
                    $order_bill_data .= '</tr>';
                }
                $order_bill_data .= '<tr>';
                $order_bill_data .= '<td></td>';
                $order_bill_data .= '<td><b>Grand Total</b></td>';
                $order_bill_data .= '<td id="total-price" class="tb-align-right"><b>Rp ' . $order_payment['total_price'] . '</b>';
                $order_bill_data .= '</td>';
                $order_bill_data .= '</tr>';
                $order_bill_data .= '<input id="totalBill" type="hidden" value="' . str_replace('.', '', $order_payment['total_price']) . '">';

                $order_bill_data .= '<tr class="payment-method">';
                $order_bill_data .= '<td class="border-bottom</td>';
                $order_bill_data .= '<td style="width:30%" class="tb-align-right"></td>';
                $order_bill_data .= '<td style="width:30%"></td>';
                $order_bill_data .= '</tr>';

                $order_bill_data .= '<tr>';
                $order_bill_data .= '<td></td>';
                $order_bill_data .= '<td><b>Kembalian </b></td>';
                $order_bill_data .= '<td  style="width:30%" class="tb-align-right payment-text">Rp 0';
                $order_bill_data .= '</td>';
                $order_bill_data .= '<td style="width:30%"></td>';
                $order_bill_data .= '</tr>';

            }
        }
        $return_data['order_list'] = $order_list_data;
        $return_data['order_bill'] = $order_bill_data;
        $return_data['total_tax'] = $total_tax;
        $return_data['total_price'] = $order_payment['total_price'];
        return $return_data;
    }

    function get_voucher_detail()
    {
        $this->load->helper('datatables');
        $return_data['status'] = FALSE;
        $return_data['msg'] = "Kode voucher tidak boleh kosong.";

        if ($this->input->post('code')) {

            $code = $this->input->post('code');
            $data = $this->order_model->get_voucher_bycode($code);
            if ($data) {
                $where = array(
                    'store_id' => $this->data['data_store']->id,
                    'voucher_group_id' => $data->voucher_group_id
                );
                $voucher_availability = $this->order_model->get_all_where('voucher_availability', $where);

                if ($data->is_available_all_store == 1 || !empty($voucher_availability)) {
                    $msg = '<table class="table-bill-preview">
                   <tr>
                   <td>Nominal</td>
                   <td>:</td>
                   <td>' . convert_rupiah($data->amount) . '</td>
                   </tr>
                   <tr>
                   <td>Tanggal Berlaku</td>
                   <td>:</td>
                   <td>' . convert_date($data->start_valid_date) . ' - ' . convert_date($data->expire_date) . '</td>
                   </tr>
                   </table>
                   ';

                    $return_data['status'] = TRUE;
                    $return_data['nominal'] = $data->amount;
                    $return_data['minimum_order'] = $data->minimum_order;
                    $return_data['msg'] = $msg;
                } else {
                    $return_data['msg'] = "Voucher tidak terdaftar.";
                }
            } else {
                $return_data['msg'] = "Voucher tidak terdaftar.";
            }
        } else {
            if ($this->data['setting']["voucher_method"] == 2) {
                $voucher_category = $this->input->post('voucher_category');
                $voucher_quantity = $this->input->post('voucher_quantity');
                if (true) {
                    $where = array(
                        "voucher.voucher_group_id" => $voucher_category,
                        "voucher.status" => 0,
                        "voucher.expire_date >= " => date('Y-m-d')
                    );
                    $vouchers = $this->order_model->get_all_where('voucher', $where);

                    $voucher_groups = $this->order_model->get_one('voucher_group', $voucher_category);
                    $where2 = array(
                        'store_id' => $this->data['data_store']->id,
                        'voucher_group_id' => $voucher_category
                    );
                    $voucher_availability = $this->order_model->get_all_where('voucher_availability', $where2);

                    if (count($vouchers) >= 1 || !empty($voucher_availability)) {
                        if (count($vouchers) < $voucher_quantity) {
                            $return_data['msg'] = "Voucher Hanya tersedia " . count($vouchers) . " Buah";
                        } else {
                            $return_data['status'] = TRUE;
                            $msg = '<table class="table-bill-preview">
                   <tr>
                   <td>Nominal</td>
                   <td>:</td>
                   <td>' . convert_rupiah($voucher_groups->amount * $voucher_quantity) . '</td>
                   </tr>
                   <tr>
                   <td>Tanggal Berlaku</td>
                   <td>:</td>
                   <td>' . convert_date($voucher_groups->start_valid_date) . ' - ' . convert_date($voucher_groups->end_valid_date) . '</td>
                   </tr>
                   </table>
                   ';
                            $return_data['nominal'] = $voucher_groups->amount * $voucher_quantity;
                            $return_data['minimum_order'] = $voucher_groups->minimum_order;
                            $return_data['voucher_name'] = $voucher_groups->name;
                            $return_data['voucher_quantity'] = $voucher_quantity;
                            $return_data['voucher_category'] = $voucher_category;
                            $return_data['msg'] = $msg;
                        }
                    } else {
                        $return_data['msg'] = "Voucher Sudah Habis";
                    }
                } else {

                    $return_data['msg'] = "Silahkan Isi data dengan benar";
                }
            }
        }

        echo json_encode($return_data);

    }

    function get_user_compliment()
    {
        $return_data['status'] = FALSE;
        $return_data['msg'] = "User tidak terdaftar";

        if ($this->input->post('code')) {

            $user_id = $this->input->post('code');
            $order_id = $this->input->post('order_id');

            // $data_access = $this->store_model->get_user_access($pin, false);
            // if ($data_access) {
                $data = $this->order_model->get_compliment_by_username($user_id);

                if ($data) {
                    if ($data->reset_period == 1) {
                        $date = new DateTime("today");
                        $start_date = $date->format('Y-m-d');
                        $end_date = FALSE;
                    } else if ($data->reset_period == 2) {
                        $date1 = new DateTime('last monday midnight');
                        $start_date = $date1->format('Y-m-d');

                        $date2 = new DateTime('next sunday midnight');                        
                        $end_date = $date2->format('Y-m-d');
                    } else {
                        $date1 = new DateTime("first day of this month");
                        $start_date = $date1->format('Y-m-d');

                        $date2 = new DateTime("last day of this month");
                        $end_date = $date2->format('Y-m-d');
                    }

                    //echo $pin;
                    $compl_usage = $this->order_model->get_compliment_usage($data->id, $start_date, $end_date, $data->reset_period);
                    $total_price_hpp = $this->order_model->calculate_menu_hpp($order_id);
                    $amount_left = ($data->cogs_limit - $compl_usage);
                    $return_data['is_cogs'] = $data->is_cogs;
                    $message = ', sisa limit hpp Rp ' . number_format($amount_left, 0, "", ".");
                    if ($data->is_cogs == 0) {
                        $amount_left = 0;
                        $message = ", sisa limit hpp :  unlimited";
                    }

                    $disabled = "";

                    $msg = '<p>Compliment Milik : ' . $data->name . $message . '</p>
                        <form id="myForm">
                            <div class="form-group">
                                <label class="col-lg-12">Pilih Compliment</label>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <div class="row">
                                        <div class="checkbox">
                                            <div>
                                                <label><input id="complimentHpp" type="radio" class="complimentTypeClass" name="compliment_type" value="0"> Compliment HPP</label>
                                            </div>
                                            <div>
                                                <label><input id="complimentPrice" type="radio" class="complimentTypeClass" name="compliment_type" value="1" checked> Compliment Harga Jual</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        ';

                    if ($amount_left <= 0 && $data->is_cogs == 1) {
                        $return_data['msg'] = "Compliment Tidak mencukupi";
                    } else {
                        $return_data['status'] = TRUE;
                        $return_data['msg'] = $msg;
                    }
                    $return_data['cogs_limit'] = $amount_left;
                    $return_data['total_price_hpp'] = $total_price_hpp;

                } else {
                    $return_data['msg'] = "User tidak terdaftar.";
                }
            // }
        }

        echo json_encode($return_data);
    }

    function test()
    {
        $print_data = $this->order_model->get_oc_cashier(array("id" => 2));
        $total_plus = $print_data['cash']->amount + $print_data['cash_dp']->amount + $print_data['transfer_dp']->amount;
        foreach ($print_data['debit'] as $d) {
            $total_plus += $d->amount;
        }
        foreach ($print_data['credit'] as $d) {
            $total_plus += $d->amount;
        }
        $total_minus = $print_data['petty_cash']->amount + $print_data['dp_out']->amount + $print_data['bon']->amount + $print_data['voucher']->amount + $print_data['compliment']->amount + $print_data['discount']->amount + $print_data['cash_company']->amount + $print_data['pending_bill_company']->amount + $print_data['pending_bill_employee']->amount + $print_data['delivery']->amount;
        echo $total_plus + $total_minus;
        echo "<pre>";
        print_r($print_data);
    }

    function update_open_close()
    {

        $this->groups_access->check_feature_access('openclose');

        $return_data['status'] = FALSE;
        $return_data['msg'] = "Akses ditolak.";
        $this->load->model("report_model");
        $cash_on_hand = $this->input->post('cash_on_hand');
        $begin_balance = $this->input->post('begin_balance');
        $pin = $this->input->post('pin');
        $status = $this->input->post('status');
        $return_data['url'] = base_url('table');
		
        if ($pin) {

            $data = $this->store_model->get_user_open_close($pin);

            if ($data) {
                $date = date('Y-m-d H:i:s');
                $by = $data->id;

                //if status open(1) then close cashier
                if ($status == 1) {

                    $data_open_close = $this->store_model->get_open_close();

                    $new_status = 2;

                    $bill_summary = $this->order_model->get_sum_bill_payment($data_open_close->open_at, $date);
                    $total_transaction = is_null($bill_summary->total_transaction) ? 0 : $bill_summary->total_transaction;
					
					// check server data
					if(isset($this->data['setting']['check_server_before_close_transaction']) &&
						$this->data['setting']['check_server_before_close_transaction'] == 1){
						$url = $this->data['setting']['server_base_url'] . "api/cashier/summary_bill";
						$send = array(
							"store_id" => $this->data['setting']['store_id'],
							"open_at" => $data_open_close->open_at,
							"close_at" => $date,
						);
						$results = $this->curl_connect(array(), $url."?".http_build_query($send));
						
						if($results->total_transaction != $total_transaction){
							$return_data['status'] = FALSE;
							$return_data['msg'] = "Data di server belum ter-update, harap tunggu sampai sinkronisasi data ke server selesai.";
							echo json_encode($return_data);
							return;
						}
					}
					
                    $total_cash = is_null($bill_summary->total_cash) ? 0 : $bill_summary->total_cash;

                    $print_data = $this->order_model->get_oc_cashier(array("id" => $data_open_close->id));


                    if ($this->data['setting']['open_close_format'] != 3) {
                        $print_data['cash']->amount -= ($print_data['petty_cash']->amount + $print_data['delivery']->amount);
                    }

                    $omzet = $print_data['net_sales']->amount + $print_data['taxes']->amount + $print_data['round_up']->amount + $print_data['delivery_charge']->amount;
                    $total = $omzet - $print_data['voucher']->amount - $print_data['compliment']->amount - $print_data['cash_company']->amount - $print_data['pending_bill_company']->amount - $print_data['pending_bill_employee']->amount;
                    $total -= ($print_data['petty_cash']->amount + $print_data['dp_out']->amount + $print_data['delivery']->amount + $print_data['discount']->amount + $print_data['bon']->amount);

                    $plus = array();
                    if($this->data['setting']['open_close_format']==1){
                        $total_card = 0;
                        foreach ($print_data['debit'] as $d) {
                            $total_card += $d->amount;
                        }
                        foreach ($print_data['credit'] as $d) {
                            $total_card += $d->amount;
                        }
                        foreach ($print_data['flazz'] as $d) {
                            $total_card += $d->amount;
                        }
                        $total2 = $total - $total_card;
                        array_push($plus, array("name" => "Cash", "value" => $total2, "is_enhancher" => 1));
                        array_push($plus, array("name" => "DP Cash", "value" => $print_data['cash_dp']->amount, "is_enhancher" => 0));
                        array_push($plus, array("name" => "DP Card", "value" => $print_data['transfer_direct_dp']->amount, "is_enhancher" => 0));
                        array_push($plus, array("name" => "DP Transfer", "value" => $print_data['transfer_dp']->amount, "is_enhancher" => 0));
                        $total_plus=$total2;
                        foreach ($print_data['debit'] as $d) {
                            if ($d->amount != 0) array_push($plus, array("name" => $d->bank_name."-DB", "value" => $d->amount, "is_enhancher" => 1));
                            $total_plus+=$d->amount;
                        }
                        foreach ($print_data['credit'] as $d) {
                            if ($d->amount != 0) array_push($plus, array("name" => $d->bank_name."-CR", "value" => $d->amount, "is_enhancher" => 1));
                            $total_plus+=$d->amount;
                        }
                        foreach ($print_data['flazz'] as $d) {
                            if ($d->amount != 0) array_push($plus, array("name" => $d->bank_name."-Flazz", "value" => $d->amount, "is_enhancher" => 1));
                            $total_plus+=$d->amount;
                        }
                        array_push($plus, array("name" => "Jumlah", "value" => $total_plus, "is_enhancher" => 0));
                        $total_minus =
                        $print_data['petty_cash']->amount +
                        $print_data['dp_out']->amount+
                        $print_data['bon']->amount +
                        $print_data['voucher']->amount +
                        $print_data['compliment']->amount +
                        $print_data['discount']->amount +
                        $print_data['cash_company']->amount +
                        $print_data['pending_bill_company']->amount +
                        $print_data['pending_bill_employee']->amount +
                        $print_data['delivery']->amount;
                        array_push($plus, array("name" => "Kas Kecil", "value" => $print_data['petty_cash']->amount, "is_enhancher" => -1));
                        array_push($plus, array("name" => "Delivery", "value" => $print_data['delivery']->amount, "is_enhancher" => -1));
                        array_push($plus, array("name" => "DP Out", "value" => $print_data['dp_out']->amount, "is_enhancher" => -1));
                        array_push($plus, array("name" => "BON", "value" => $print_data['bon']->amount, "is_enhancher" => -1));
                        array_push($plus, array("name" => "Discount", "value" => $print_data['discount']->amount, "is_enhancher" => -1));
                        if ($print_data['voucher']->amount != 0) array_push($plus, array("name" => "Voucher", "value" => $print_data['voucher']->amount, "is_enhancher" => -1));
                        if ($print_data['compliment']->amount != 0) array_push($plus, array("name" => "Compliment", "value" => $print_data['compliment']->amount, "is_enhancher" => -1));
                        if ($print_data['cash_company']->amount != 0) array_push($plus, array("name" => "Cash Company", "value" => $print_data['cash_company']->amount, "is_enhancher" => -1));
                        if ($print_data['pending_bill_company']->amount != 0) array_push($plus, array("name" => "Pending Bill Company", "value" => $print_data['pending_bill_company']->amount, "is_enhancher" => -1));
                        if ($print_data['pending_bill_employee']->amount != 0) array_push($plus, array("name" => "Pending Bill Employee", "value" => $print_data['pending_bill_employee']->amount, "is_enhancher" => -1));
                        array_push($plus, array("name" => "Jumlah", "value" => $total_minus, "is_enhancher" => 0));
                        $total_plus+=$print_data['petty_cash']->amount+$print_data['dp_out']->amount+$print_data['delivery']->amount+$print_data['bon']->amount;
                        array_push($plus, array("name" => "Omset", "value" => $total_plus, "is_enhancher" => 0));
                        array_push($plus, array("name" => "Saldo Awal", "value" =>$print_data['oc_cashier']->begin_balance, "is_enhancher" => 0));
                        if($print_data['balance_cash_history']->amount>0) {
                            array_push($plus, array("name" => "Penambah", "value" =>$print_data['balance_cash_history']->amount, "is_enhancher" => 0));
                            array_push($plus, array("name" => "Saldo Akhir", "value" =>$print_data['oc_cashier']->begin_balance+$print_data['balance_cash_history']->amount, "is_enhancher" => 0));
                        }
                        if($this->data['setting']['cash_on_hand']==1) {
                            array_push($plus, array("name" => "Cash On Hand", "value" => $cash_on_hand, "is_enhancher" => 0));
                            array_push($plus, array("name" => "Selisih Cash", "value" =>$print_data['cash']->amount+$print_data['cash_dp']->amount -  $cash_on_hand, "is_enhancher" => 0));
                        }
                    } elseif ($this->data['setting']['open_close_format']==2) {
                        array_push($plus, array("name" => "Net Sales", "value" => $print_data['net_sales']->amount, "is_enhancher" => 1));
                        array_push($plus, array("name" => "Ppn", "value" => $print_data['taxes']->amount, "is_enhancher" => 1));
                        array_push($plus, array("name" => "Pembulatan", "value" => $print_data['round_up']->amount, "is_enhancher" => 1));
                        array_push($plus, array("name" => "Ongkos Kirim", "value" => $print_data['delivery_charge']->amount, "is_enhancher" => 1));
                        array_push($plus, array("name" => "Omset", "value" => $omzet, "is_enhancher" => 0));

                        array_push($plus, array("name" => "Kas Kecil", "value" => $print_data['petty_cash']->amount, "is_enhancher" => -1));
                        array_push($plus, array("name" => "DP Out", "value" => $print_data['dp_out']->amount, "is_enhancher" => -1));
                        array_push($plus, array("name" => "Komisi Delivery", "value" => $print_data['delivery']->amount, "is_enhancher" => -1));
                        array_push($plus, array("name" => "Discount", "value" => $print_data['discount']->amount, "is_enhancher" => -1));
                        array_push($plus, array("name" => "BON", "value" => $print_data['bon']->amount, "is_enhancher" => -1));
                        if ($print_data['voucher']->amount != 0) array_push($plus, array("name" => "Voucher", "value" => $print_data['voucher']->amount, "is_enhancher" => -1));
                        if ($print_data['compliment']->amount != 0) array_push($plus, array("name" => "Compliment", "value" => $print_data['compliment']->amount, "is_enhancher" => -1));
                        if ($print_data['cash_company']->amount != 0) array_push($plus, array("name" => "Cash Company", "value" => $print_data['cash_company']->amount, "is_enhancher" => -1));
                        if ($print_data['pending_bill_company']->amount != 0) array_push($plus, array("name" => "Pending Bill Company", "value" => $print_data['pending_bill_company']->amount, "is_enhancher" => -1));
                        if ($print_data['pending_bill_employee']->amount != 0) array_push($plus, array("name" => "Pending Bill Employee", "value" => $print_data['pending_bill_employee']->amount, "is_enhancher" => -1));
                        array_push($plus, array("name" => "Jml Setoran", "value" => $total, "is_enhancher" => 0));
                        $total_card = 0;
                        foreach ($print_data['debit'] as $d) {
                            if ($d->amount != 0) array_push($plus, array("name" => $d->bank_name."-DB", "value" => $d->amount, "is_enhancher" => -1));
                            $total_card += $d->amount;
                        }
                        foreach ($print_data['credit'] as $d) {
                            if ($d->amount != 0) array_push($plus, array("name" => $d->bank_name."-CR", "value" => $d->amount, "is_enhancher" => -1));
                            $total_card += $d->amount;
                        }
                        foreach ($print_data['flazz'] as $d) {
                            if ($d->amount != 0) array_push($plus, array("name" => $d->bank_name."-Flazz", "value" => $d->amount, "is_enhancher" => -1));
                            $total_card += $d->amount;
                        }
                        $total2 = $total - $total_card;
                        array_push($plus, array("name" => "Cash", "value" => $total2, "is_enhancher" => -1));
                        if ($this->data['setting']['cash_on_hand'] == 1) {
                            array_push($plus, array("name" => "Cash On Hand", "value" =>  $cash_on_hand, "is_enhancher" => 0));
                            array_push($plus, array("name" => "Selisih", "value" => $total2 -  $cash_on_hand, "is_enhancher" => 0));
                        }
                        array_push($plus, array("name" => "DP Cash", "value" => $print_data['cash_dp']->amount, "is_enhancher" => 0));
                        array_push($plus, array("name" => "DP Card", "value" => $print_data['transfer_direct_dp']->amount, "is_enhancher" => 0));
                        array_push($plus, array("name" => "DP Transfer", "value" => $print_data['transfer_dp']->amount, "is_enhancher" => 0));
                    } elseif ($this->data['setting']['open_close_format']==3) {
                        $total2 = $print_data['cash']->amount;
                        $total_card = 0;
                        foreach ($print_data['debit'] as $d) {
                            $total_card += $d->amount;
                        }
                        foreach ($print_data['credit'] as $d) {
                            $total_card += $d->amount;
                        }
                        foreach ($print_data['flazz'] as $d) {
                            $total_card += $d->amount;
                        }
                        $pendapatan=$total2+$total_card+$print_data['cash_dp']->amount+ $print_data['transfer_dp']->amount+ $print_data['transfer_direct_dp']->amount;
                        $total_cash -= $print_data['compliment']->amount;
                        array_push($plus, array("name" => "Saldo Awal", "value" =>$print_data['oc_cashier']->begin_balance, "is_enhancher" => 0));
                        array_push($plus, array("name" => "Pendapatan", "value" =>$pendapatan, "is_enhancher" => 0));
                        array_push($plus, array("name" => "Cash", "value" =>$total2, "is_enhancher" => 1));
                        foreach ($print_data['debit'] as $d) {
                            if ($d->amount != 0) array_push($plus, array("name" => $d->bank_name."-DB", "value" => $d->amount, "is_enhancher" => 1));
                        }
                        foreach ($print_data['credit'] as $d) {
                            if ($d->amount != 0) array_push($plus, array("name" => $d->bank_name."-CR", "value" => $d->amount, "is_enhancher" => 1));
                        }
                        foreach ($print_data['flazz'] as $d) {
                            if ($d->amount != 0) array_push($plus, array("name" => $d->bank_name."-Flazz", "value" => $d->amount, "is_enhancher" => 1));
                        }
                        if ($print_data['delivery']->amount != 0)array_push($plus, array("name" => "Delivery", "value" => $print_data['delivery']->amount, "is_enhancher" => -1));
                        if ($print_data['dp_out']->amount != 0)array_push($plus, array("name" => "DP Out", "value" => $print_data['dp_out']->amount, "is_enhancher" => -1));
                        if ($print_data['bon']->amount != 0)array_push($plus, array("name" => "BON", "value" => $print_data['bon']->amount, "is_enhancher" => -1));
                        if ($print_data['discount']->amount != 0)array_push($plus, array("name" => "Discount", "value" => $print_data['discount']->amount, "is_enhancher" => -1));
                        if ($print_data['voucher']->amount != 0) array_push($plus, array("name" => "Voucher", "value" => $print_data['voucher']->amount, "is_enhancher" => -1));
                        if ($print_data['compliment']->amount != 0) array_push($plus, array("name" => "Compliment", "value" => $print_data['compliment']->amount, "is_enhancher" => -1));
                        if ($print_data['cash_company']->amount != 0) array_push($plus, array("name" => "Cash Company", "value" => $print_data['cash_company']->amount, "is_enhancher" => -1));
                        if ($print_data['pending_bill_company']->amount != 0) array_push($plus, array("name" => "Pending Bill Company", "value" => $print_data['pending_bill_company']->amount, "is_enhancher" => -1));
                        if ($print_data['pending_bill_employee']->amount != 0) array_push($plus, array("name" => "Pending Bill Employee", "value" => $print_data['pending_bill_employee']->amount, "is_enhancher" => -1));
                        array_push($plus, array("name" => "Jumlah", "value" => $print_data['cash']->amount+$print_data['oc_cashier']->begin_balance, "is_enhancher" => 0));
                        array_push($plus, array("name" => "Modal", "value" => $print_data['balance_cash_history']->amount, "is_enhancher" => 0));
                        array_push($plus, array("name" => "Kas Kecil", "value" => $print_data['petty_cash']->amount, "is_enhancher" => 0));
                        array_push($plus, array("name" => "Grand Total", "value" => $print_data['balance_cash_history']->amount - $print_data['petty_cash']->amount, "is_enhancher" => 0));
                    } elseif ($this->data['setting']['open_close_format']==4) {

                        array_push($plus, array("name" => "Items Sales", "value" => $print_data['oc_menu']->amount, "is_enhancher" => 0));
                        array_push($plus, array("name" => "Net Sales", "value" => $print_data['net_sales']->amount, "is_enhancher" => 1));

                        if($print_data['taxes']->amount!=0)
                            array_push($plus, array("name" => "Ppn", "value" => $print_data['taxes']->amount, "is_enhancher" => 1));

                        if($print_data['round_up']->amount!=0)
                            array_push($plus, array("name" => "Pembulatan", "value" => $print_data['round_up']->amount, "is_enhancher" => 1));

                        if($print_data['delivery_charge']->amount!=0)
                            array_push($plus, array("name" => "Ongkos Kirim", "value" => $print_data['delivery_charge']->amount, "is_enhancher" => 1));

                        if($print_data['petty_cash']->amount!=0)
                            array_push($plus, array("name" => "Kas Kecil", "value" => $print_data['petty_cash']->amount, "is_enhancher" => -1));

                        if($print_data['dp_out']->amount!=0)
                            array_push($plus, array("name" => "DP Out", "value" => $print_data['dp_out']->amount, "is_enhancher" => -1));

                        if($print_data['delivery']->amount!=0)
                            array_push($plus, array("name" => "Komisi Delivery", "value" => $print_data['delivery']->amount, "is_enhancher" => -1));

                        if($print_data['discount']->amount != 0)
                            array_push($plus, array("name" => "Discount", "value" => $print_data['discount']->amount, "is_enhancher" => -1));

                        if($print_data['bon']->amount != 0)
                            array_push($plus, array("name" => "BON", "value" => $print_data['bon']->amount, "is_enhancher" => -1));

                        if ($print_data['voucher']->amount != 0) 
                            array_push($plus, array("name" => "Voucher", "value" => $print_data['voucher']->amount, "is_enhancher" => -1));

                        if ($print_data['compliment']->amount != 0) 
                            array_push($plus, array("name" => "Compliment", "value" => $print_data['compliment']->amount, "is_enhancher" => -1));

                        if ($print_data['cash_company']->amount != 0) 
                            array_push($plus, array("name" => "Cash Company", "value" => $print_data['cash_company']->amount, "is_enhancher" => -1));

                        if ($print_data['pending_bill_company']->amount != 0) 
                            array_push($plus, array("name" => "Pending Bill Company", "value" => $print_data['pending_bill_company']->amount, "is_enhancher" => -1));

                        if ($print_data['pending_bill_employee']->amount != 0)
                            array_push($plus, array("name" => "Pending Bill Employee", "value" => $print_data['pending_bill_employee']->amount, "is_enhancher" => -1));

                        array_push($plus, array("name" => "Total Cash", "value" => $print_data['cash']->amount, "is_enhancher" => 1));

                        $total_card = 0;
                        $jml=$print_data['cash']->countd;
                        foreach ($print_data['debit'] as $d) {
                            if ($d->amount != 0)
                                array_push($plus, array("name" => $d->bank_name."-DB", "value" => $d->amount, "is_enhancher" => -1));
                            $jml+=$d->countd;
                            $total_card += $d->amount;
                        }
                        foreach ($print_data['credit'] as $d) {
                            if ($d->amount != 0) 
                                array_push($plus, array("name" => $d->bank_name."-CR", "value" => $d->amount, "is_enhancher" => -1));
                            $jml+=$d->countd;
                            $total_card += $d->amount;
                        }
                        foreach ($print_data['flazz'] as $d) {
                            if ($d->amount != 0) 
                                array_push($plus, array("name" => $d->bank_name."-Flazz", "value" => $d->amount, "is_enhancher" => -1));
                            $jml+=$d->countd;
                            $total_card += $d->amount;
                        }

                        foreach ($print_data['taxes_foreach'] as $d) {
                            if ($d->amount != 0) 
                                array_push($plus, array("name" => $d->info, "value" => $d->amount, "is_enhancher" => 1));
                        }

                        $bill = $print_data['transaction']->transaction_count;  

                        if ($bill == 0){
                            $pembagijml = 1;
                        } else {
                            $pembagijml = $bill;
                        }

                        $total2 = $total - $total_card;

                        $total_cash = $print_data['cash']->amount;

                        $avg_bill = ($total_card + $total_cash) / $pembagijml;

                        $ttl_menu = $print_data['oc_menu']->amount;

                        if ($ttl_menu == 0){
                            $pembagimnu = 1;
                        } else {
                            $pembagimnu = $ttl_menu;
                        }

                        $avg_menu = ($total_card + $total_cash) / $pembagimnu;

                        $ttl_covers = $print_data['transaction']->transaction_count;

                        if ($ttl_covers == 0){
                            $pembagimnu = 1;
                        } else {
                            $pembagimnu = $ttl_covers;
                        }

                        $avg_covers = ($total_card + $total_cash) / $pembagimnu;

                        array_push($plus, array("name" => "Total Bills", "value" => $bill, "is_enhancher" => 0));

                        array_push($plus, array("name" => "Avg Bills", "value" => $avg_bill, "is_enhancher" => 0));

                        array_push($plus, array("name" => "Total Menu", "value" => $ttl_menu, "is_enhancher" => 0));

                        array_push($plus, array("name" => "Avg Menu", "value" => $avg_menu, "is_enhancher" => 0));

                        $ttl_gr = 0;
                        foreach($print_data['oc_category'] as $d){
                            $ttl_gr += $d->mnpric;
                            array_push($plus, array("name" => $d->ctgname, "value" => $d->quantt, "is_enhancher" => 0));

                            array_push($plus, array("name" => $d->ctgname, "value" => $d->mnpric, "is_enhancher" => 0));
                        }

                        array_push($plus, array("name" => "Total Group", "value" => $ttl_gr, "is_enhancher" => 0));

                        array_push($plus, array("name" => "Total Dine In", "value" => $print_data['oc_dinein']->ttldn, "is_enhancher" => 0));
                        array_push($plus, array("name" => "Total Take Away", "value" => $print_data['oc_takeaway']->ttltkw, "is_enhancher" => 0));
                        array_push($plus, array("name" => "Total Delivery", "value" => $print_data['oc_delivery']->ttldlv, "is_enhancher" => 0));


                        if ($this->data['setting']['cash_on_hand'] == 1) {
                            array_push($plus, array("name" => "Cash On Hand", "value" =>  $cash_on_hand, "is_enhancher" => 0));
                            array_push($plus, array("name" => "Selisih", "value" => $total2 -  $cash_on_hand, "is_enhancher" => 0));
                        }

                        array_push($plus, array("name" => "DP Cash", "value" => $print_data['cash_dp']->amount, "is_enhancher" => 0));
                        array_push($plus, array("name" => "DP Card", "value" => $print_data['transfer_direct_dp']->amount, "is_enhancher" => 0));
                        array_push($plus, array("name" => "DP Transfer", "value" => $print_data['transfer_dp']->amount, "is_enhancher" => 0));
                    }
                    $data_save = array(
                        'status' => 2,
                        'close_at' => $date,
                        'close_by' => $by,
                        'total_transaction' => $total_transaction,
                        'total_cash' => $total_cash,
                        'cash_on_hand' => $cash_on_hand
                    );
                    $this->store_model->save('open_close_cashier', $data_save, $data_open_close->id);
                    foreach ($plus as $p) {
                        $this->store_model->save('open_close_cashier_detail', array(
                            "open_close_cashier_id" => $data_open_close->id,
                            "name" => $p['name'],
                            "value" => $p['value'],
                            "is_enhancher" => $p['is_enhancher'],
                            ));
                    }

                } else {
                    $new_status = 1;
                    $data_save = array(
                        'status' => 1,
                        'open_at' => $date,
                        'open_by' => $by,
                        'begin_balance' => $begin_balance
                        );

                    $id = $this->store_model->save('open_close_cashier', $data_save);
                }

                if ($new_status == 2) {

                    $return_data['url'] = base_url('auth/logout');

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

                        $data_print = $this->order_model->get_oc_cashier(array("id" => $data_open_close->id));
                        $data_print['cash']->amount -= (
                            $data_print['petty_cash']->amount
                            + $data_print['delivery']->amount
                            );
                        $data_print['store_data'] = $this->data['data_store'];
                        $data_print['setting'] = $this->data['setting'];

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
                        } 
                        elseif ($this->data['setting']['open_close_format'] == 3) {
                            $data_print['cash']->amount += ($data_print['petty_cash']->amount + $data_print['delivery']->amount);
                            if ($printer_obj->printer_width == 'generic') {
                                print_open_close_bill_mode4_generic($printer_location, $data_print, $this->data['user_profile_data'], $printer_setting);
                            } else {
                                print_open_close_bill_mode4($printer_location, $data_print, $this->data['user_profile_data'], $printer_setting);
                            }                            
                        }else {                            
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

                            $where = array(
                                "store_id" => $this->data['setting']['store_id'], 
                                "start_date" => $data_print['oc_cashier']->open_at, 
                                "end_date" => $data_print['oc_cashier']->close_at
                                ); 

                            $data_print['report_stocks'] = $this->inventory_model->get_daily_inventory_stock_data($where);
                            if($data_print['report_stocks']){
                                print_report_stock($printer_location, $data_print, $this->data['user_profile_data'], $printer_setting);    
                            } 
                        }
                    }
                }

                $return_data['status'] = TRUE;
                $return_data['msg'] = "";
            }


        }

        echo json_encode($return_data);

    }

    function print_open_close_bill($data_open_close)
    {

        if (!empty($data_open_close)) {
            // get bill
            $open_at = $data_open_close->open_at;
            $close_at = $data_open_close->close_at;

            $data_order['bill_payment'] = $this->order_model->get('enum_payment_option')->result();
            foreach ($data_order['bill_payment'] as $key => $row) {
                $row->total = $this->order_model->get_sum_bill_payment_byoption($open_at, $close_at, $row->id);
            }

            $data_order['bill_information'] = $this->order_model->get_sum_bill_information($open_at, $close_at);
            $data_order['outlet'] = $this->store_model->get_outlets(array('store_id' => $this->data['setting']['store_id']));
            foreach ($data_order['outlet'] as $key => $row) {
                $row->data = $this->order_model->get_sales_menu_data($row->id, $open_at, $close_at);
            }
            $data_order['bill_menu_category'] = $this->order_model->get('category')->result();

            foreach ($data_order['bill_menu_category'] as $key => $row) {
                $row->data = $this->order_model->calculate_sales_menu_category($open_at, $close_at, $row->id);
            }

            $data_order['total_cash'] = $data_open_close->total_cash;
            $data_order['total_transaction'] = $data_open_close->total_transaction;
            $data_order['open_by'] = $data_open_close->open_by;
            $data_order['close_by'] = $data_open_close->close_by;
            $data_order['open_at'] = $data_open_close->open_at;
            $data_order['close_at'] = $data_open_close->close_at;
            $data_order['date'] = date_format(date_create($close_at), 'd M Y');
            $data_order['time'] = date_format(date_create($open_at), 'H:i') . '-' . date_format(date_create($close_at), 'H:i');

            $data_order['bill_list'] = $this->order_model->get_bill_by_payment_date($open_at, $close_at);
            foreach ($data_order['bill_list'] as $key => $row) {
                $data = $this->order_model->calculate_open_close_bill($row->id);
                $row->order_menu_bill = $data;
            }

            //print struck
            $this->load->helper(array('printer'));
            $this->load->model("setting_printer_model");
            //get printer cashier
            $printer_arr_obj = $this->setting_printer_model->get_printer_by_enum_printer_type(array("name_type"=>"printer_cashier"));
            foreach ($printer_arr_obj as $printer_obj) {
                $printer_location = $printer_obj->name_printer;

                $data_order['store_data'] = $this->data['data_store'];
                $data_order['setting'] = $this->data['setting'];
                print_open_close_bill($printer_location, $data_order, $this->data['user_profile_data']);
            }
        }

    }

    function get_detail_promo()
    {
        $promo = $this->input->post('promo_id');

        $split = explode("-", $promo);
        $promo_id = $split[0];
        $promo_type = $split[1];
        if ($promo_type === "cc") {
            $table = "cc";
        } else {
            $table = "discount";
        }
        $data_promo = $this->cashier_model->get_detail_promo($promo_type, $promo_id);
        echo json_encode($data_promo);
    }

    function get_detail_promo_menu()
    {
        $promo = $this->input->post('promo_id');

        $split = explode("-", $promo);
        $promo_id = $split[0];
        $promo_type = $split[1];
        if ($promo_type === "cc") {
            $table = "cc";
        } else {
            $table = "discount";
        }
        $data_promo = $this->cashier_model->get_detail_promo_menu($promo_type, $promo_id);
        echo json_encode($data_promo);
    }

    function get_member_detail()
    {
        $this->load->helper('datatables');
        $return_data['status'] = FALSE;
        $return_data['msg'] = "Kode Member tidak boleh kosong.";

        if ($this->input->post('member_id')) {
            $member_id = $this->input->post('member_id');
            $data = $this->order_model->get_member_by_id($member_id);
            if ($data) {
                $return_data['status'] = TRUE;
                $return_data['percentage'] = $data->discount;
            } else {
                $return_data['msg'] = "Kode Member tidak terdaftar.";
            }
        }
        echo json_encode($return_data);
    }

    function get_member_detail_by_db_id()
    {
        $this->load->helper('datatables');
        $return_data['status'] = FALSE;
        $return_data['msg'] = "Kode Member tidak boleh kosong.";

        if ($this->input->post('member_id')) {
            $member_id = $this->input->post('member_id');
            $data = $this->order_model->get_member_by_id_db($member_id);
            if ($data) {
                $return_data['status'] = TRUE;
                $return_data['percentage'] = $data->discount;
            } else {
                $return_data['msg'] = "Kode Member tidak terdaftar.";
            }
        }
        echo json_encode($return_data);
    }

    function get_order_company()
    {
        $return_data['status'] = FALSE;
        $return_data['msg'] = "Order Company ID tidak boleh kosong.";

        if ($this->input->post('order_company_id')) {

            $order_company_id = $this->input->post('order_company_id');
            $data = $this->order_model->get_one('order_company', $order_company_id);
            if ($data) {
                $return_data['status'] = TRUE;
                $return_data['data'] = $data;

            } else {
                $return_data['msg'] = "Data Order Perusahaan tidak terdaftar.";
            }

        }

        echo json_encode($return_data);
    }

    function get_customer_auto_complete()
    {
        $is_delivery = $this->input->get("is_delivery");
        $is_takeaway = $this->input->get("is_takeaway");
        $params = array("b.is_delivery" => $is_delivery, "b.is_take_away" => $is_takeaway);
        $result = $this->order_model->get_customer_auto_complete($params);
        echo json_encode($result);
    }

    public function generate_unique_number($exist_numbers = array())
    {
        $unique_number = substr(uniqid("", 1), -4);
        if (in_array($unique_number, $exist_numbers)) {
            $unique_number = $this->generate_unique_number($exist_numbers);
        }

        return $unique_number;
    }

    public function generate_random_number($min, $max)
    {
        $numbers = range($min, $max);
        shuffle($numbers);
        return implode("", $numbers);
    }

    public function refund()
    {
        if ($this->data['data_open_close']->status != 1) redirect(base_url());
        $this->groups_access->check_feature_access('refund');
        $this->data['data_order'] = array();
        $receipt_number = "";
        $refund_date = date("Y-m-d");
        if ($_POST) {
            $receipt_number = $this->input->post("receipt_number");
            $refund_date = $this->input->post("refund_date");
            $order_type = 1;
            $bill = $this->order_model->get_all_where("bill", array("SUBSTR(receipt_number,-4)" => $receipt_number, "date(payment_date)" => $refund_date, 'is_refund' => 0));
            if (sizeof($bill) > 0) {
                $bill = $bill[0];
            } else {
                $this->session->set_flashdata('message', 'Data pembayaran tidak ditemukan!');
                redirect(base_url('cashier/refund'));
            }
            $this->data['bill'] = $bill;
            $order_id = $bill->order_id;
            $this->data['data_order'] = $this->order_model->get_one('order', $order_id);

            $this->data['reservation'] = $this->order_model->get_one('reservation', $this->data['data_order']->reservation_id);
            $this->data['order_name'] = $this->data['data_order']->customer_name;
            $this->data['order_phone'] = $this->data['data_order']->customer_phone;
            $this->data['order_address'] = $this->data['data_order']->customer_address;
            $this->data['order_mode'] = 'Nama';
            if ($this->data['data_order']->is_take_away == '0' && $this->data['data_order']->is_delivery == '0') {
                $this->data['order_mode'] = 'Table';
                $data_table = $this->order_model->get_one('table', $this->data['data_order']->table_id);
                if(sizeof($data_table)>0){
                    $this->data['order_name'] = 'Meja ' . $data_table->table_name . ($this->data['data_order']->customer_name != "" ? " (" . $this->data['data_order']->customer_name . ")" : "") . ', ' . $data_table->customer_count . ' orang';
                }else{
                    $this->data['order_name'] = 'Fast Order Dine In';
                }
            }

            if ($bill->is_take_away == 1) {
                $order_type = 2;
            } else if ($bill->is_delivery == 1) {
                $order_type = 3;
            }
            $tax_method = $this->data['setting']['tax_service_method'];
            $get_order_taxes = $this->tax_model->get_taxes($order_type, $tax_method, 1);

            $order_payment = $this->order_model->calculate_total_order_bill_for_refund($get_order_taxes, $bill->receipt_number, $order_id, true);
            
            $res = $this->list_order_refund($order_payment);
            $this->data['order_list'] = $res['order_list'];
            $this->data['order_bill'] = $res['order_bill'];
            $this->data['total_tax'] = $res['total_tax'];
            $this->data['total_price'] = $res['total_price'];
            $this->data['order_id'] = $order_id;
            $get_round_up = $this->data['setting']['is_round_up'];
            $this->data['is_round_up'] = (!empty($get_round_up)) ? $get_round_up : 0;
            $get_nearest_round = $this->data['setting']['nearest_round'];
            $this->data['nearest_round'] = (!empty($get_nearest_round) && (!(empty($get_round_up)) && $get_round_up == 1)) ? $get_nearest_round : 0;
            $this->data['promo_id'] = $this->cashier_model->get_promo_discount_dropdown($this->data['data_store']->id);
            $this->data['store_id'] = $this->data['data_store']->id;
            $this->data['order_company'] = $this->store_model->get_order_company_dropdown();
            $this->data['employees'] = $this->store_model->get_member_employee_dropdown();
            $this->data['bank'] = $this->store_model->get_all_bank();
            $this->data['bank_account_card'] = $this->store_model->get_bank_account_card_dropdown();
            $data_compliment = $this->store_model->get_compliment_data();
            $array_compliments = array();
            $this->load->library('encrypt');
            foreach ($data_compliment as $key => $value) {
                $compliment = new stdClass();
                $compliment->user_id = $value->user_id;
                $compliment->name = $value->name;
                array_push($array_compliments, $compliment);
            }
            $this->data['compliments'] = $array_compliments;
            $this->data['non_employee_members'] = $this->store_model->get_all_non_employee_member();
            $this->data['data_url'] = base_url('cashier/get_data_member');
            $this->data['voucher_categories']=array();
            if ($this->data['setting']["voucher_method"] == 2) {
                $this->data['voucher_categories'] = $this->store_model->get("voucher_group")->result();
            }
        }
        $this->data['refund_date'] = $refund_date;
        $this->data['receipt_number'] = $receipt_number;
        $this->data['staff_mode'] = $this->data['user_profile_data']->name;
        $this->data['title'] = "cashier - Checkout";
        $this->data['theme'] = 'floor-theme';
        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');
        $this->data['content'] .= $this->load->view('cashier/refund_v', $this->data, true);
        $this->render('cashier');
    }

    /*
    *   Created by: Mohamad Tri Ramdhani
    *   Updated at: 16/08/2016
    *   Function name: process_refund
    */

    public function process_refund($receipt_number, $data_bayar, $order_status, $table_status) {
        // get data bill from receipt number
        $history_bill = array_pop($this->bill_model->get_all_where('bill', array('receipt_number' => $receipt_number)));
        $history_bill_courier_services = $this->bill_model->get_all_where('bill_courier_service', array('bill_id' => $history_bill->id));
        $history_bill_information = $this->bill_model->get_all_where('bill_information', array('bill_id' => $history_bill->id));
        $history_bill_menu = $this->bill_model->get_bill_menu_for_refund($history_bill->id);
        $history_bill_payment = $this->bill_model->get_all_where('bill_payment', array('bill_id' => $history_bill->id));
        $history_bill_menu_inventory_cogs = array();
        $history_bill_menu_side_dish = array();
        foreach ($history_bill_menu as $bm) {
            array_push($history_bill_menu_inventory_cogs, $this->bill_model->get_all_where('bill_menu_inventory_cogs', array('bill_menu_id' => $bm->id)));
            array_push($history_bill_menu_side_dish, $this->bill_model->get_all_where('bill_menu_side_dish', array('bill_menu_id' => $bm->id)));
        }

        // get order menu for further update order data
        $history_order = $this->order_model->get_one('order', $history_bill->order_id);
        $history_order_menu = $this->order_model->get_all_where('order_menu', array('order_id' => $history_bill->order_id));
        $history_order_menu_inventory_cogs = array();
        $history_order_menu_option = array();
        $history_order_menu_side_dish = array();
        $history_order_package_menu = array();
        $temp_inventory_cogs = array();
        foreach ($history_order_menu as $om) {
            $inventory_cogs = $this->order_model->get_all_where('order_menu_inventory_cogs', array('order_menu_id' => $om->id));
            array_push($history_order_menu_inventory_cogs, $inventory_cogs);
            $temp_inventory_cogs[$om->id] = $inventory_cogs;
            array_push($history_order_menu_option, $this->order_model->get_all_where('order_menu_option', array('order_menu_id' => $om->id)));
            array_push($history_order_menu_side_dish, $this->order_model->get_all_where('order_menu_side_dish', array('order_menu_id' => $om->id)));
            array_push($history_order_package_menu, $this->order_model->get_all_where('order_package_menu', array('order_menu_id' => $om->id)));
        }

        // IF ACCOUNTING MODULE IS INSTALLED
        if ($this->data['module']['ACCOUNTING'] == 1) {
            // delete account data by old bill
            $this->account_data_model->delete_by_limit('account_data', array('entry_type' => 3, 'foreign_id' => $history_bill->id), 0);
        }
        
        $index = 0;
        foreach ($history_bill_menu as $om) {
            $data_menu = $this->order_model->get_outlet_by_menu_id($om->menu_id);
            $outlet_id = $data_menu->outlet_id;
            $qty = $om->quantity;
            $check = false;
            $temp=array();
            foreach ($data_bayar as $product) {
                if ($product['menu_id'] == $om->menu_id) {
                    $qty -= $product['product_amount'];                    
                }
            }

            if ($qty != 0) {
                $ingredients = $this->order_model->get_all_menu_ingredients($om->menu_id);
                foreach ($ingredients as $i) {
                    $price = 0;
                    foreach ($history_bill_menu_inventory_cogs[$index] as $t) {
                        if ($i->inventory_id == $t->inventory_id) {
                            $price = ($t->quantity > 0) ? $t->cogs / $t->quantity : 0;
                        }
                    }

                    $get_stock_outlet = $this->inventory_model->get_all_where('stock', array('outlet_id' => $outlet_id, 'inventory_id' => $i->inventory_id));

                    if (!empty($get_stock_outlet)) {
                        // insert stock history
                        $array = array(
                            'store_id' => $this->data['setting']['store_id'],
                            'outlet_id' => $outlet_id,
                            'inventory_id' => $i->inventory_id,
                            'uom_id' => $i->uom_id,
                            'quantity' => $qty * $i->quantity,
                            'created_at' => date("Y-m-d H:i:s"),
                            'purchase_date' => date("Y-m-d H:i:s"),
                            'price' => $price,
                            'status' => 10
                        );

                        $this->order_model->save('stock_history', $array);

                        if ($this->data['setting']['stock_method'] == "AVERAGE") {
                            $this->process_save_method_average($array);
                        }

                        if ($this->data['setting']['stock_method'] == "FIFO") {
                            $this->process_save_method_fifo($array);
                        }
                    }
                }
            }
            $index++;
        }

        // generate for refund key
        $refund_key = md5($this->data['setting']['store_id'] . $history_bill->order_id . $receipt_number . $history_bill->created_at);

        $data['refund_key'] = $refund_key;
        $data['ref_bill_id'] = $history_bill->id;
        return $data;
    }

    public function list_order_refund($order_refund) {
        $order_list_data = '';
        $order_bill_data = '';
        $total_tax = 0;
        if (!empty($order_refund)) {
            foreach ($order_refund['order_list'] as $order) {
                $order_list_data .= '<tr class="tOrder ' . $order->order_menu_id . '" id="' . $order->order_menu_id . '" menu_id ="' . $order->menu_id . '"  data-category="' . $order->category_id . '" data-use_taxes="' . $order->use_taxes . '">';
                $order_list_data .= '<td style="padding-left:10px;">';
                $order_list_data .= $order->menu_name;

                foreach ($order->side_dish_list as $sdh) {
                    $order_list_data .= ' <br/>-- ' . $sdh->name . ' (' . $sdh->origin_price . ')';

                }

                $order_list_data .= '</td>';
                $order->quantity = (empty($order->quantity)) ? $order->quantity : $order->quantity;
                $order_list_data .= '<td class="border-side tb-align-right">' . $order->quantity . '</td>';
                $order_list_data .= '<td class="tb-align-right price-menu" data-price="' . $order->origin_price . '" data-menu-hpp="' . $order->menu_hpp . '"  style="padding-right: 10px">Rp ' . number_format($order->menu_price, 0, "", ".") . '';

                $order_list_data .= '</td>';

                $order_list_data .= '<td style="display: none">' . $order->menu_id . '</td>';
                $order_list_data .= '<td style="display: none"></td>';
                $order_list_data .= '<td style="display: none"></td>';
                $order_list_data .= '<td style="display: none"></td>';
                $order_list_data .= '<td style="display: none">' . $order->order_menu_id . '</td>';
                $order_list_data .= '<td style="display: none"></td>';
                $order_list_data .= '<td style="display: none"></td>';
                $order_list_data .= '<td style="display: none">' . $order->quantity . '</td>';
                $order_list_data .= '</tr>';

            }

            if ($order_refund['subtotal'] != '0') {
                $order_bill_data .= '<tr>';
                $order_bill_data .= '<td style="width:30%"></td>';
                $order_bill_data .= '<td style="width:30%"><b>Subtotal 1</b></td>';
                $order_bill_data .= '<td style="width:40%" id="subtotal-price" class="tb-align-right" data-price="' . $order_refund['subtotal'] . '">Rp ' . $order_refund['subtotal'] . '</td>';
                $order_bill_data .= '</tr>';
                $order_bill_data .= '<tr class="discount-order-list">';
                $order_bill_data .= '<td class="border-bottom</td>';
                $order_bill_data .= '<td style="width:30%"></td>';
                $order_bill_data .= '<td style="width:30%">---------------</td>';
                $order_bill_data .= '</tr>';
                
                $total_tax = 0;

                $order_bill_data .= '<tr>';
                $order_bill_data .= '<td></td>';
                $order_bill_data .= '<td><b>Sub Total 2</b></td>';
                $order_bill_data .= '<td id="sub-total-2" class="tb-align-right">';
                $order_bill_data .= '</td>';
                $order_bill_data .= '</tr>';

                $order_bill_data .= '<tr>';
                $order_bill_data .= '<td class="border-bottom</td>';
                $order_bill_data .= '<td style="width:30%"></td>';
                $order_bill_data .= '<td style="width:30%"> ---------------</td>';
                $order_bill_data .= '</tr>';


                foreach ($order_refund['tax_price'] as $tax) {

                    $order_bill_data .= '<tr>';
                    $order_bill_data .= '<td></td>';
                    $order_bill_data .= '<td><b>' . $tax['name'] . '</b></td>';
                    $order_bill_data .= '<td id="tax-price" account-id="' . $tax['account_id'] . '" percentage="' . $tax['tax_percentage'] . '" tax-origin-name="' . $tax['origin_name'] . '" tax-name="' . $tax['name'] . '" service="' . $tax['is_service'] . '" class="tb-align-right">Rp ' . $tax['value'] . '</td>';
                    $order_bill_data .= '</tr>';
                    $total_tax += $tax['value'];

                }

                foreach ($order_refund['extra_charge_price'] as $xtra) {

                    $order_bill_data .= '<tr>';
                    $order_bill_data .= '<td></td>';
                    $order_bill_data .= '<td><b>' . $xtra['name'] . '</b></td>';
                    $order_bill_data .= '<td class="tb-align-right">Rp ' . $xtra['value'] . '</td>';
                    $order_bill_data .= '</tr>';

                }
                if (isset($order_refund['delivery_cost'])) {
                    $order_bill_data .= '<tr>';
                    $order_bill_data .= '<td></td>';
                    $order_bill_data .= '<td><b>Ongkir</b></td>';
                    $courier_service = $this->data['setting']['courier_service'];
                    $order_bill_data .= '<td id="delivery_cost" cost="' . $order_refund['delivery_cost'] . '" class="tb-align-right">Rp ' . number_format($order_refund['delivery_cost'], 0, "", ".") . '</td>';
                    $order_bill_data .= '</tr>';
                }
                $order_bill_data .= '<tr>';
                $order_bill_data .= '<td></td>';
                $order_bill_data .= '<td><b>Pembulatan</b></td>';
                $order_bill_data .= '<td id="pembulatan" class="tb-align-right">';
                $order_bill_data .= '</td>';
                $order_bill_data .= '</tr>';

                $order_bill_data .= '<tr  >';
                $order_bill_data .= '<td class="border-bottom</td>';
                $order_bill_data .= '<td style="width:30%" class="tb-align-right"></td>';
                $order_bill_data .= '<td style="width:30%"> ---------------</td>';
                $order_bill_data .= '</tr>';

                if (isset($order_refund['reservation']) && sizeof($order_refund['reservation']) > 0 && $order_refund['reservation']->down_payment > 0) {
                    $order_bill_data .= '<tr>';
                    $order_bill_data .= '<td></td>';
                    $order_bill_data .= '<td><b>DP</b></td>';
                    $order_bill_data .= '<td id="reservation_down_payment" cost="' . $order_refund['reservation']->down_payment . '" class="tb-align-right">Rp ' . number_format($order_refund['reservation']->down_payment, 0, "", ".") . '</td>';
                    $order_bill_data .= '</tr>';
                }
                $order_bill_data .= '<tr>';
                $order_bill_data .= '<td></td>';
                $order_bill_data .= '<td><b>Grand Total</b></td>';
                $order_bill_data .= '<td id="total-price" class="tb-align-right"><b>Rp ' . $order_refund['total_price'] . '</b>';
                $order_bill_data .= '</td>';
                $order_bill_data .= '</tr>';
                $order_bill_data .= '<input id="totalBill" type="hidden" value="' . str_replace('.', '', $order_refund['total_price']) . '">';

                $order_bill_data .= '<tr class="payment-method">';
                $order_bill_data .= '<td class="border-bottom</td>';
                $order_bill_data .= '<td style="width:30%" class="tb-align-right"></td>';
                $order_bill_data .= '<td style="width:30%"></td>';
                $order_bill_data .= '</tr>';

                $order_bill_data .= '<tr>';
                $order_bill_data .= '<td></td>';
                $order_bill_data .= '<td><b>Kembalian </b></td>';
                $order_bill_data .= '<td  style="width:30%" class="tb-align-right payment-text">Rp 0';
                $order_bill_data .= '</td>';
                $order_bill_data .= '<td style="width:30%"></td>';
                $order_bill_data .= '</tr>';

            }
        }
        $return_data['order_list'] = $order_list_data;
        $return_data['order_bill'] = $order_bill_data;
        $return_data['total_tax'] = $total_tax;
        $return_data['total_price'] = $order_refund['total_price'];
        return $return_data;
    }

    // function for process save stock with AVERAGE method
    // parameter : data_inventory
    // created by : bening
    public function process_save_method_average($data_inventory){

        $this->load->model("stock_model");
        
        //get all stock by inventory id
        $data_stocks = $this->stock_model->get_stock_detail_by_inventory_id(array(
            "store_id" => $data_inventory->store_id,
            "outlet_id" => $data_inventory->outlet_id,
            "inventory_id" => $data_inventory->inventory_id,
            "uom_id" => $data_inventory->uom_id,
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
            'store_id' => $data_inventory->store_id,
            'outlet_id' => $data_inventory->outlet_id,
            'inventory_id' => $data_inventory->inventory_id,
            'uom_id' => $data_inventory->uom_id,
            'quantity' => $total_quantity,
            'created_at' => $data_inventory->created_at,
            'purchase_date' => $data_inventory->purchase_date,
            'price' => $average_price
        );
        $this->stock_model->save('stock', $array);
    }

    public function process_save_method_fifo($data_inventory) {

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

    /*
    *   Created by: Mohamad Tri Ramdhani
    *   Date: 12/08/2016
    */

    // fungsi untuk menambahkan data keuangan ke dalam jurnal (account_data)
    public function set_journal($params = array()) {
        if ($params['status'] == 'courier_service') {
            // status pengurangan sebagai jasa kurir
            $account_data_courier_service = array(
                'has_synchronized' => 0,
                'store_id' => $this->data['setting']['store_id'],
                'account_id' => $params['account_id'],
                'entry_type' => 3,
                'foreign_id' => $params['bill_id'],
                'info' => "Jasa Kurir",
                'debit' => $params['delivery_cost'],
                'credit' => 0,
                'created_at' => date("Y-m-d H:i:s")
            );
            $this->account_model->save('account_data', $account_data_courier_service);
        } else if ($params['status'] == 'round') {
            // status pertambahan sebagai pembulatan dari biaya total
            $account_data_pembulatan = array(
                'has_synchronized' => 0,
                'store_id' => $this->_setting['store_id'],
                'account_id' => $this->_setting['pembulatan_account_id'],
                'entry_type' => 3,
                'foreign_id' => $params['bill_id'],
                'info' => 'Pembulatan',
                'credit' => $params['round_total'],
                'debit' => 0,
                'created_at' => date("Y-m-d H:i:s")
            );
            $this->account_model->save('account_data', $account_data_pembulatan);
        } else if ($params['status'] == 'discount') {
            // status pengurang sebagai diskon
            $account_data_discount_promo = array(
                'has_synchronized' => 0,
                'store_id' => $this->_setting['store_id'],
                'account_id' => $this->_setting['discount_account_id'],
                'entry_type' => 3,
                'foreign_id' => $params['bill_id'],
                'info' => 'Diskon',
                'credit' => 0,
                'debit' => $params['promo_total'],
                'created_at' => date("Y-m-d H:i:s")
            );
            $this->account_model->save('account_data', $account_data_discount_promo);
        } else if ($params['status'] == 'promo_cc') {
            // status pengurang sebagai promo CC
            $account_data_promo_cc =  array(
                'has_synchronized' => 0,
                'store_id' => $this->_setting['store_id'],
                'account_id' => $this->_setting['discount_account_id'],
                'entry_type' => 3,
                'foreign_id' => $params['bill_id'],
                'info' => 'Promo CC',
                'credit' => 0,
                'debit' => $params['promo_cc'],
                'created_at' => date("Y-m-d H:i:s")
            );
            $this->account_model->save('account_data', $account_data_promo_cc);
        } else if ($params['status'] == 'disc_member') {
            // status pengurang sebagai diskon member
            $account_disc_member = array(
                'has_synchronized' => 0,
                'store_id' => $this->_setting['store_id'],
                'account_id' => $this->_setting['discount_account_id'],
                'entry_type' => 3,
                'foreign_id' => $params['bill_id'],
                'info' => 'Diskon Member',
                'credit' => 0,
                'debit' => $params['disc_member'],
                'created_at' => date("Y-m-d H:i:s")
            );
            $this->account_model->save('account_data', $account_disc_member);
        } else if ($params['status'] == 'tax') {
            // status penambah sebagai pajak dan layanan (tax and services)
            $account_data_tax = array(
                'has_synchronized' => 0,
                'store_id' => $this->_setting['store_id'],
                'account_id' => $params['acc_id'],
                'entry_type' => 3,
                'foreign_id' => $params['bill_id'],
                'info' => $params['info'],
                'credit' => $params['tax'],
                'debit' => 0,
                'created_at' => date("Y-m-d H:i:s")
            );
            $this->account_model->save('account_data', $account_data_tax);
        } else if ($params['status'] == 'delivery') {
            // status penambah sebagai ongkos delivery
            $account_data_delivery_services = array(
                'has_synchronized' => 0,
                'store_id' => $this->_setting['store_id'],
                'account_id' => $this->_setting['delivery_cost_account_id'],
                'entry_type' => 3,
                'foreign_id' => $params['bill_id'],
                'info' => 'Delivery Cost',
                'credit' => $params['delivery_cost'],
                'debit' => 0,
                'created_at' => date("Y-m-d H:i:s")
            );
            $this->account_model->save('account_data', $account_data_delivery_services);
        } else if ($params['status'] == 'inventory') {
            // status penambah per inventory
            $account = $params['account'];
            foreach ($account as $acc) {
                $account_data_inventory = array(
                    'has_synchronized' => 0,
                    'store_id' => $this->_setting['store_id'],
                    'entry_type' => 3,
                    'foreign_id' => $params['bill_id'],
                    'info' => $acc->acc_name,
                    'account_id' => $acc->account_id,
                    'credit' => $acc->total,
                    'debit' => 0,
                    'created_at' => date("Y-m-d H:i:s")
                );
                $this->account_model->save('account_data', $account_data_inventory);
            }
        } else if ($params['status'] == 'payment_option') {
            // status pengurang akibat metode pembayaran
            $payment_option = $params['payment'];
            $payment_type = $payment_option['type'];
            $account_id = 0;
            $info = '';
            $detail_info = '';
            $detail_desc = '';
            $desc = $this->account_data_model->get_by('enum_payment_option', $payment_type);

            if ($payment_type == 1) {
                $account_id = $this->_setting['temporary_cash_account_id'];
                $detail_desc = $desc->value;
            } else if ($payment_type == 2) {
                $account_id = $this->_setting['debit_receivable_account_id'];
                $detail_info = $payment_option['code'];
                $detail_desc = $desc->value;
            } else if ($payment_type == 3) {
                $account_id = $this->_setting['credit_receivable_account_id'];
                $detail_info = $payment_option['code'];
                $detail_desc = $desc->value;
            } else if ($payment_type == 4) {
                $account_id = $this->_setting['voucher_account_id'];
                $detail_desc = $desc->value;
            } else if ($payment_type == 5) {
                $account_id = $this->_setting['voucher_account_id'];
                $detail_desc = $desc->value;
            } else if ($payment_type == 6) {
                $account_id = $this->_setting['piutang_dagang_account_id'];
                $detail_desc = $desc->value;
            } else if ($payment_type == 7) {
                $account_id = $this->_setting['piutang_karyawan_account_id'];
                $detail_desc = $desc->value;
            } else if ($payment_type == 8) {
                $account_id = $this->_setting['temporary_cash_account_id'];
                $detail_desc = $desc->value;
            } else if ($payment_type == 9) {
                $account_id = $this->_setting['prive_account_id'];
                $detail_desc = $desc->value;
            } else if ($payment_type == 10) {
                $account_id = $this->_setting['hutang_dp_account_id'];
                $detail_desc = $desc->value;
            } else if ($payment_type == 11) {
                $account_id = $this->_setting['flazz_receivable_account_id'];
                $detail_info = $payment_option['code'];
                $detail_desc = $desc->value;
            }

            $account_name = $this->account_model->get_by('account', $account_id);

            if (!empty($account_name)) {
                $account_data_payment_option = array(
                    'has_synchronized' => 0,
                    'store_id' => $this->_setting['store_id'],
                    'entry_type' => 3,
                    'foreign_id' => $params['bill_id'],
                    'account_id' => $account_id,
                    'info' => ($payment_type == 2 || $payment_type == 3) ? $payment_option['bankId'] : $account_name->name,
                    'credit' => 0,
                    'debit' => $payment_option['amount'],
                    'created_at' => date("Y-m-d H:i:s")
                );

                $result_account_data = $this->account_data_model->add($account_data_payment_option);

                $account_data_detail['account_data_id'] = $result_account_data;
            }

            $account_data_detail['info'] = $detail_info;
            $account_data_detail['description'] = $detail_desc;            

            $this->account_data_model->add_detail($account_data_detail);
        } else if ($params['status'] == 'hpp') {
            $debit_hpp = array(
                'store_id' => $this->_setting['store_id'],
                'entry_type' => 3,
                'foreign_id' => $params['bill_id'],
                'info' => 'HPP',
                'debit' => $params['bill_hpp'],
                'credit' => 0,
                'account_id' => $this->data['setting']['cogs_account_id']
            );
            $this->account_data_model->add($debit_hpp);
            
            $credit_hpp = array(
                'store_id' => $this->_setting['store_id'],
                'entry_type' => 3,
                'foreign_id' => $params['bill_id'],
                'info' => 'HPP',
                'debit' => 0,
                'credit' => $params['bill_hpp'],
                'account_id' => $this->_setting['inventory_account_id']
            );
            $this->account_data_model->add($credit_hpp);
        } else if ($params['status'] == 'lain-lain') {
            // status penambah jika total debit > total credit
            $account_data_other_profit = array(
                'has_synchronized' => 0,
                'store_id' => $this->_setting['store_id'],
                'entry_type' => 3,
                'foreign_id' => $params['bill_id'],
                'account_id' => $this->_setting['other_income_account_id'],
                'credit' => $params['credit'],
                'debit' => 0,
                'info' => 'Pendapatan Lain-lain',
                'created_at' => date("Y-m-d H:i:s")
            );
            $this->account_data_model->add($account_data_other_profit);
        } else if ($params['status'] == 'refund') {
            // status refund per inventory
            $account = $params['account'];
            foreach ($account as $acc) {
                $account_data_inventory = array(
                    'has_synchronized' => 0,
                    'store_id' => $this->_setting['store_id'],
                    'entry_type' => 3,
                    'foreign_id' => $params['bill_id'],
                    'info' => 'refund '.$acc->name,
                    'account_id' => $acc->account_id,
                    'credit' => 0,
                    'debit' => $acc->total,
                    'created_at' => date("Y-m-d H:i:s")
                );
                $this->account_model->save('account_data', $account_data_inventory);
            }
        }
    }

    public function get_sales_today(){
        if ($this->input->is_ajax_request()) {
            $this->load->model('order_model');
            $user_id = 0;
            $start_date = date('Y-m-d 00:00');
            $end_date = date('Y-m-d 23:59');
            $order_type = '';
            $payment_option = '';
            $reports['summary'] = $this->order_model->get_summary_transaction(array(
                'user_id' => $user_id,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'order_type' => $order_type,
                'payment_option' => $payment_option,
            ));
            $reports['total_cash'] = $this->order_model->get_summary_transaction(array(
                'user_id' => $user_id,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'order_type' => $order_type,
                'payment_option' => 1,
            ));
            $reports['total_debit'] = $this->order_model->get_summary_transaction(array(
                'user_id' => $user_id,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'order_type' => $order_type,
                'payment_option' => 2,
            ));
            $reports['total_credit'] = $this->order_model->get_summary_transaction(array(
                'user_id' => $user_id,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'order_type' => $order_type,
                'payment_option' => 3,
            ));
            echo json_encode($reports);
        }
    }
	
	function curl_connect($data, $url)
    {
        //open connection
        $ch = curl_init();
        curl_setopt_array($ch, array(
			CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            // CURLOPT_POST => 1,
            // CURLOPT_POSTFIELDS => $data
		));

        //execute post
        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
		
        /* 200 Response! */
        $result = json_decode($result);
        if ($status == 200) {
            return $result;
        }
        return FALSE;
    }
}