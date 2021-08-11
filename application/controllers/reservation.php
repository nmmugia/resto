<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Reservation extends Cashier_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('order_model');
        $this->load->model('table_model');
        $this->load->model('tax_model');
        $this->load->model('checker_model');
        $this->groups_access->check_feature_access('dinein');
        $all_cooking_status = array();
        foreach ($this->order_model->get("enum_cooking_status")->result() as $a) {
            $all_cooking_status[$a->id] = $a->status_name;
        }
        $this->data['all_cooking_status'] = json_encode($all_cooking_status);
    }

    public function index()
    {
        if ($this->data['data_open_close']->status != 1) redirect(base_url());
        $this->groups_access->check_feature_access('reservation');
        //load content
        $this->data['title'] = "Reservasi";
        $this->data['theme'] = 'floor-theme';
        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');
        $this->data['template_lists'] = $this->order_model->get("template_reservation_note")->result();
        $this->data['add_url'] = base_url('reservation/add');
        $this->data['data_url'] = base_url('reservation/get_data');
        //load content
        $this->data['content'] .= $this->load->view('reservation_v', $this->data, true);
        $this->render('cashier');
    }

    public function get_data()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));
        $feature_confirmation = str_replace(",", "-", $this->data['feature_confirmation']['reservation']);

        $this->datatables->select('reservation.id, customer_name,reservation.customer_count,phone, 
            book_date, book_note, table.table_name,reservation.down_payment,reservation.order_type,enum_reservation_status.value as status_reservasi,reservation.status')
            ->from('reservation')
            ->join('table', 'table.id = reservation.table_id', 'left')
            ->join('enum_reservation_status', 'enum_reservation_status.id = reservation.status', 'left')
            ->unset_column("down_payment")
            ->add_column("down_payment", "$1", "convert_rupiah(down_payment)")
            ->unset_column('order_type')
            ->add_column('order_type', '$1', 'set_enum_order_type(order_type)') 
            ->add_column("actions", "$1", "get_reservation_action_button(id,status,book_date, ".$feature_confirmation.")");
        echo $this->datatables->generate();
    }


    public function _book_date_check($value, $id = 0)
    {
        $this->load->model('reservation_model');
        $range_time = $this->data['setting']['range_booking_time'];
        $available_date = date('Y-m-d H:i:s', strtotime("+" . $range_time . " minutes", strtotime($value)));
        $available_date_min = date('Y-m-d H:i:s', strtotime("-" . $range_time . " minutes", strtotime($value)));
        $booK_date = date('Y-m-d H:i:s', strtotime($value));
        $form_data = $this->reservation_model->get_reservation(array(
            "book_date >=" => $available_date_min,
            "book_date <=" => $available_date,
            "status" => 1,
            "table_id" => $this->input->post('table_id'),
            "id !=" => $id
        ));
        if ($form_data && $this->input->post('table_id') != 0) {
            $this->form_validation->set_message('_book_date_check', 'Waktu reservation tidak dapat dipesan.');
            return false;
        }

        return TRUE;
    }

    public function add()
    {
        $this->data['title'] = "Tambah Reservasi";
        $this->data['subtitle'] = "Tambah Reservasi";
        //display the create user form
        //set the flash data error message if there is one
        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['customer_name'] = array('name' => 'customer_name',
            'id' => 'customer_name',
            'type' => 'text',
            'class' => 'form-control requiredTextField',
            'field-name' => 'nama',
            'placeholder' => 'Masukan nama',
            'value' => $this->form_validation->set_value('customer_name'));

        $this->data['phone'] = array('name' => 'phone',
            'id' => 'phone',
            'type' => 'text',
            'class' => 'form-control requiredTextField only_numeric',
            'field-name' => 'kontak',
            'placeholder' => 'Masukan nomor kontak',
            'value' => $this->form_validation->set_value('phone'));

        $this->load->model('store_model');
        $booking_start_lock = $this->store_model->get_general_setting('booking_start_lock')->value;

        $this->data['book_date'] = array('name' => 'book_date',
            'id' => 'book_date',
            'type' => 'text',
            'class' => 'form-control requiredTextField',
            'field-name' => 'waktu reservasi',
            'placeholder' => 'Masukan waktu reservasi',
            'onkeydown' => 'return false',
            'value' => date("Y-m-d H:i", (time() + (60*$booking_start_lock))));
            // 'readonly'=> true,
            // 'value' => $this->form_validation->set_value('book_date', $this->input->post('book_date')));

        $this->data['customer_count'] = array('name' => 'customer_count',
            'id' => 'customer_count',
            'type' => 'text',
            'class' => 'form-control only_numeric',
            'field-name' => 'jumlah tamu',
            'placeholder' => 'Masukan jumlah tamu',
            'value' => $this->form_validation->set_value('customer_count', $this->input->post('customer_count')));
        $this->data['down_payment'] = array('name' => 'down_payment',
            'id' => 'down_payment',
            'type' => 'text',
            'maxlength' => 11,
            'class' => 'form-control only_numeric',
            'field-name' => 'jumlah DP',
            'placeholder' => 'Masukan jumlah DP',
            'value' => $this->form_validation->set_value('down_payment', $this->input->post('down_payment')));
        $this->data['customer_address'] = array('name' => 'customer_address',
            'id' => 'customer_address',
            'type' => 'text',
            'rows' => 3,
            'class' => 'form-control',
            'field-name' => 'catatan',
            'placeholder' => 'Alamat',
            'value' => $this->form_validation->set_value('customer_address', $this->input->post('customer_address')));

        $this->data['book_note'] = array('name' => 'book_note',
            'id' => 'book_note',
            'type' => 'text',
            'class' => 'form-control',
            'field-name' => 'catatan',
            'placeholder' => 'Masukan catatan',
            'value' => $this->form_validation->set_value('book_note', $this->input->post('book_note')));
        $this->data['dp_type_cash'] = array('name' => 'dp_type',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Tipe DP',
            'placeholder' => '',
            'checked' => 'checked',
            'value' => "1"
        );

        $this->data['dp_type_transfer'] = array('name' => 'dp_type',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Tipe DP',
            'placeholder' => '',
            'checked' => ($this->form_validation->set_value('dp_type', $this->input->post('dp_type')) == 2) ? 'true' : '',
            'value' => "2");
        $this->data['dp_type_transfer_direct'] = array('name' => 'dp_type',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Tipe DP',
            'placeholder' => '',
            'checked' => ($this->form_validation->set_value('dp_type', $this->input->post('dp_type')) == 3) ? 'true' : '',
            'value' => "3");
        $this->data['order_type_dinein'] = array('name' => 'order_type',
            'type' => 'radio',
            'class' => 'requiredTextField order_type_reservation',
            'field-name' => 'Tipe Pesanan',
            'placeholder' => '',
            'checked' => 'checked',
            'value' => "1"
        );
        $this->data['order_type_takeaway'] = array('name' => 'order_type',
            'type' => 'radio',
            'class' => 'requiredTextField order_type_reservation',
            'field-name' => 'Tipe Pesanan',
            'placeholder' => '',
            'checked' => ($this->form_validation->set_value('order_type', $this->input->post('order_type')) == 2) ? 'true' : '',
            'value' => "2");
        $this->data['order_type_delivery'] = array('name' => 'order_type',
            'type' => 'radio',
            'class' => 'requiredTextField order_type_reservation',
            'field-name' => 'Tipe Pesanan',
            'placeholder' => '',
            'checked' => ($this->form_validation->set_value('order_type', $this->input->post('order_type')) == 3) ? 'true' : '',
            'value' => "3");
        //load content
        $this->data['table'] = $this->store_model->get_table_dropdown();
        $this->data['categories'] = $this->cashier_model->get_category_by_store($this->data['data_store']->id);
        $this->data['menus'] = $this->cashier_model->get_menus_by_store($this->data['data_store']->id);
        if ($this->data['setting']['zero_stock_order'] == 0) {
            $this->order_model->all_menu_ingredient_with_stock($this->data['menus']);
        } else {
            foreach ($this->data['menus'] as $m) {
                $m->total_available = 0;
            }
        }
        $taxes = $this->db->get('taxes')->result();
        $tax_method = $this->data['setting']['tax_service_method'];
        $taxes_dine_in = $this->tax_model->get_taxes(1,$tax_method,1);
        $taxes_takeaway = $this->tax_model->get_taxes(2,$tax_method,1);
        $taxes_delivery = $this->tax_model->get_taxes(3,$tax_method,1);
        $tax_percentages = 0;
        foreach ($taxes as $t) {
            $tax_percentages += $t->tax_percentage;
        }
        $this->data['tax_percentages'] = $tax_percentages;
        $this->data['taxes'] = $taxes;
        $this->data['taxes_dine_in'] = $taxes_dine_in;
        $this->data['taxes_takeaway'] = $taxes_takeaway;
        $this->data['taxes_delivery'] = $taxes_delivery;
        $this->load->model("delivery_cost_model");
        $this->data['delivery_cost_lists'] = $this->delivery_cost_model->get('enum_delivery_cost')->result();
        $this->data['content'] .= $this->load->view('reservation_add_v', $this->data, true);
        $this->render('cashier');
    

    }

    public function add_reservation(){
        $id = $this->input->post('id');
        $this->data['title'] = "Tambah Reservasi";
        $this->data['subtitle'] = "Tambah Reservasi";

        //validate form input
        $this->form_validation->set_rules('customer_name', 'nama', 'required|max_length[100]');
        //
        $this->form_validation->set_rules('phone', 'nomor kontak', 'required|numeric|max_length[20]');
        $this->form_validation->set_rules('book_date', 'waktu reservasi', 'required|callback__book_date_check');
        $this->form_validation->set_rules('down_payment', 'DP', 'greater_than[-1]');
        $this->form_validation->set_rules('book_note', 'meja', 'max_length[500]');
        //

        if ($this->input->post('order_type') == 1 ) {

            $this->form_validation->set_rules('customer_count', 'jumlah tamu', 'required|is_natural_no_zero');
            $this->form_validation->set_rules('table_id', 'Nomor Meja', 'required|is_natural_no_zero');

        }

        if ($this->input->post('order_type') == 2 || $this->input->post('order_type') == 3) {
            $this->form_validation->set_rules('detail_menu', 'menu', 'required');
        }
        if ($this->form_validation->run() == true) {

            $created_at = date("Y-m-d H:i:s");
            $created_by = $this->data['user_profile_data']->id;
            $book_date = $this->input->post('book_date');
            $down_payment = $this->input->post('down_payment');
            $dp_type = $this->input->post('dp_type');
            $order_type = $this->input->post('order_type');
            $reservation_delivery_cost_id = $this->input->post('reservation_delivery_cost_id');
            $reservation_delivery_cost = 0;


            
            if ($reservation_delivery_cost_id != "") {
                $enum_delivery = $this->store_model->get_one("enum_delivery_cost", $reservation_delivery_cost_id);
                $reservation_delivery_cost = $enum_delivery->delivery_cost;
            }
            $data_array = array('customer_name' => $this->input->post('customer_name'),
                'customer_count' => $this->input->post('customer_count'),
                'customer_address' => $this->input->post('customer_address'),
                'table_id' => ($this->input->post('table_id') != "" ? $this->input->post('table_id') : 0),
                'book_date' => date('Y-m-d H:i:s', strtotime($book_date)),
                'phone' => $this->input->post('phone'),
                'book_note' => $this->input->post('book_note'),
                'status' => 1,
                'created_at' => $created_at,
                'created_by' => $created_by,
                'down_payment' => $down_payment,
                'dp_type' => $dp_type,
                'order_type' => $order_type,
            );
            $reservation_id = $this->store_model->save('reservation', $data_array);
            if ($down_payment > 0) {
                $this->load->model("account_model");
                $account_bank = $this->account_model->get_data($this->data['setting']['bank_dp_account_id']);
                $account_cash = $this->account_model->get_data($this->data['setting']['cash_account_id']);
                $account_payable = $this->account_model->get_data($this->data['setting']['hutang_dp_account_id']);
                if (sizeof($account_bank) > 0 && sizeof($account_cash) > 0 && sizeof($account_payable) > 0) {
                    $account_data = array(
                        'has_synchronized' => 0,
                        'store_id' => $this->data['setting']['store_id'],
                        'account_id' => ($dp_type == 1 ? $account_cash->id : $account_bank->id),
                        'entry_type' => 7,
                        'foreign_id' => $reservation_id,
                        'info' => "Down Payment Reservasi",
                        'debit' => (($dp_type == 1 ? $account_cash->default_balance : $account_bank->default_balance) == 1 ? $down_payment : 0),
                        'credit' => (($dp_type == 1 ? $account_cash->default_balance : $account_bank->default_balance) == 0 ? $down_payment : 0),
                        'created_at' => date("Y-m-d H:i:s")
                    );
                    $this->account_model->save('account_data', $account_data);
                    $account_data = array(
                        'has_synchronized' => 0,
                        'store_id' => $this->data['setting']['store_id'],
                        'account_id' => $account_payable->id,
                        'entry_type' => 7,
                        'foreign_id' => $reservation_id,
                        'info' => "Down Payment Reservasi",
                        'debit' => ($account_payable->default_balance == 1 ? $down_payment : 0),
                        'credit' => ($account_payable->default_balance == 0 ? $down_payment : 0),
                        'created_at' => date("Y-m-d H:i:s")
                    );
                    $this->account_model->save('account_data', $account_data);
                }
            }
            $detail = $this->input->post("detail_menu");

            if (!empty($detail) && sizeof($detail) > 0) {
                $data_order = array(
                    'table_id' => ($this->input->post('table_id') != "" ? $this->input->post('table_id') : 0),
                    'customer_name' => $this->input->post('customer_name'),
                    'customer_phone' => $this->input->post('phone'),
                    'customer_address' => $this->input->post('customer_address'),
                    'is_take_away' => ($order_type == 2 ? 1 : 0),
                    'is_delivery' => ($order_type == 3 ? 1 : 0),
                    'created_at' => $created_at,
                    'created_by' => $created_by,
                    'has_synchronized' => 0,
                    'start_order' => date('Y-m-d H:i:s', strtotime($book_date)),
                    'reservation_id' => $reservation_id,
                    'delivery_cost_id' => ($reservation_delivery_cost_id == "" ? NULL : $reservation_delivery_cost_id),
                    'delivery_cost' => $reservation_delivery_cost
                );
                $order_id = $this->order_model->save_order($data_order);
                for ($x = 0; $x < sizeof($detail['menu_id']); $x++) {
                    $data_order_menu = array(
                        'menu_id' => $detail['menu_id'][$x],
                        'order_id' => $order_id,
                        'quantity' => $detail['quantity'][$x],
                        'note' => $detail['notes'][$x],
                        'created_at' => $created_at,
                        'created_by' => $created_by,
                        'cooking_status' => 0,
                        'dinein_takeaway' => 0
                    );
                    $result = $this->order_model->save_order_menu($data_order_menu);
                    $menu_packages = $this->order_model->get_all_where("menu_promo", array("parent_menu_id" => $detail['menu_id'][$x]));
                    foreach ($menu_packages as $m) {
                        $this->order_model->save("order_package_menu", array(
                            "order_menu_id" => $result,
                            "menu_id" => $m->package_menu_id,
                            "quantity" => $m->quantity * $detail['quantity'][$x],
                            "cooking_status" => 0,
                            "process_status" => 0,
                            "is_check" => 0
                        ));
                    }
                    if (!empty($detail['side_dish'][$x])) $this->order_model->save_side_dish_order_menu($detail['side_dish'][$x], $result);
                    if (!empty($detail['option'][$x]) && $detail['option'][$x] != '0') $this->order_model->save_option_order_menu($detail['option'][$x], $result);
                }
            }
            $table = new stdclass();
            if ($reservation_id === false) {
                $this->session->set_flashdata('message', 'Gagal menyimpan data');
            } else {
                $this->session->set_flashdata('message_success', 'Berhasil menyimpan data');
                $this->data['message'] = "Berhasil menyimpan data";
                $table = $this->table_model->get_table($this->input->post("table_id"));
                if(isset($detail) && !empty($detail)){
                    $detail_order = $this->checker_model->get_order_menu_reservation_by_id($reservation_id);
                }
             

                
            }
            echo json_encode(
                array(
                    "success" => true,
                    "message" => $this->data['message'],
                    "detail_order"=>isset($detail_order)?$detail_order[0]:array(),
                    "reservation_id"=>isset($reservation_id)?$reservation_id:0,
                    "order_id"=>isset($order_id)?$order_id:0,
                    "customer_name"=>$this->input->post('customer_name'),
                    "reservation_date"=>isset($book_date)?date("d/m/Y H:i:s",strtotime($book_date)):"",
                    "table_id"=>$this->input->post('table_id'),
                    "table_number"=>isset($table->table_name)?$table->table_name:"",
                    "operator_name"=>$this->data['user_profile_data']->name
                    
                )
            );
        } else {
            //display the create user form
            //set the flash data error message if there is one
            $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            $this->data['message_success'] = $this->session->flashdata('message_success');
            //$this->data['content'] .= $this->load->view('reservation_add_v', $this->data, true);
            //$this->render('cashier');
            echo json_encode(
                array(
                    "success" => false,
                    "message" => $this->data['message']
                )
            );
            
        }
        die();
    }

    function reload_reservation_order()
    {
        $reservation_id = $this->input->get("reservation_id");
        $this->get_order_list_reservation($reservation_id);
        echo json_encode(array(
            "content" => $this->data['order_list']
        ));
    }

    public function edit()
    {
        $id = $this->uri->segment(3);

        if (empty($id)) {
            redirect('reservation');
        }
        $this->load->model('reservation_model');

        $form_data = $this->store_model->get_one('reservation', $id);

        $this->load->model('store_model');

        if (empty($form_data)) {
            redirect('reservation');
        }

        $this->data['form_data'] = $form_data;
        $this->data['title'] = "Edit Reservasi";
        $this->data['subtitle'] = "Edit Reservasi";

        //validate form input
        $this->form_validation->set_rules('customer_name', 'nama', 'required|max_length[100]');
        $this->form_validation->set_rules('phone', 'nomor kontak', 'required|numeric|max_length[20]');
        $this->form_validation->set_rules('book_date', 'waktu reservasi', 'required|callback__book_date_check[' . $id . ']');
        $this->form_validation->set_rules('down_payment', 'DP', 'greater_than[-1]');
        $this->form_validation->set_rules('book_note', 'meja', 'max_length[500]');

        if ($this->input->post('order_type') == 1 ) {

            $this->form_validation->set_rules('customer_count', 'jumlah tamu', 'required|is_natural_no_zero');
            $this->form_validation->set_rules('table_id', 'Nomor Meja', 'required|is_natural_no_zero');

        }

        

        //display the edit user form

        //set the flash data error message if there is one
        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');
        $this->data['cancel_url'] = base_url('reservation/reservation');

        $this->data['customer_name'] = array('name' => 'customer_name',
            'id' => 'customer_name',
            'type' => 'text',
            'class' => 'form-control requiredTextField',
            'field-name' => 'nama',
            'placeholder' => 'Masukan nama',
            'value' => $this->form_validation->set_value('customer_name', $form_data->customer_name));

        $this->data['phone'] = array('name' => 'phone',
            'id' => 'phone',
            'type' => 'text',
            'class' => 'form-control requiredTextField only_numeric',
            'field-name' => 'kontak',
            'placeholder' => 'Masukan nomor kontak',
            'value' => $this->form_validation->set_value('phone', $form_data->phone));

        $this->data['book_date'] = array('name' => 'book_date',
            'id' => 'book_date',
            'type' => 'text',
            'class' => 'form-control requiredTextField',
            'field-name' => 'waktu reservasi',
            'placeholder' => 'Masukan waktu reservasi',
            'onkeydown' => 'return false',
            // 'readonly'=> true,
            'value' => $this->form_validation->set_value('book_date', $form_data->book_date));

        $this->data['customer_count'] = array('name' => 'customer_count',
            'id' => 'customer_count',
            'type' => 'text',
            'class' => 'form-control requiredTextField ',
            'field-name' => 'jumlah tamu',
            'placeholder' => 'Masukan jumlah tamu',
            'value' => $this->form_validation->set_value('customer_count', $form_data->customer_count));

        $this->data['down_payment'] = array('name' => 'down_payment',
            'id' => 'down_payment',
            'type' => 'text',
            'maxlength' => 11,
            'class' => 'form-control only_numeric',
            ($form_data->down_payment > 0 ? 'readonly' : 'not_readonly') => '',
            'field-name' => 'jumlah DP',
            'placeholder' => 'Masukan jumlah DP',
            'value' => $this->form_validation->set_value('down_payment', $form_data->down_payment));


        $this->data['book_note'] = array('name' => 'book_note',
            'id' => 'book_note',
            'type' => 'text',
            'class' => 'form-control',
            'field-name' => 'catatan',
            'placeholder' => 'Masukan catatan',
            'value' => $this->form_validation->set_value('book_note', $form_data->book_note));

        $this->data['dp_type_cash'] = array('name' => 'dp_type',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Tipe DP',
            'placeholder' => '',
            'checked' => 'checked',
            'value' => "1"
        );
        $this->data['customer_address'] = array('name' => 'customer_address',
            'id' => 'customer_address',
            'type' => 'text',
            'rows' => 3,
            'class' => 'form-control',
            'field-name' => 'catatan',
            'placeholder' => 'Alamat',
            'value' => $this->form_validation->set_value('customer_address', $form_data->customer_address));
        $this->data['dp_type_transfer'] = array('name' => 'dp_type',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Tipe DP',
            'placeholder' => '',
            'checked' => $this->form_validation->set_value('dp_type', $form_data->dp_type) == 2 ? 'true' : '',
            'value' => "2");
        $this->data['dp_type_transfer_direct'] = array('name' => 'dp_type',
            'type' => 'radio',
            'class' => 'requiredTextField',
            'field-name' => 'Tipe DP',
            'placeholder' => '',
            'checked' => $this->form_validation->set_value('dp_type', $form_data->dp_type) == 3 ? 'true' : '',
            'value' => "3");
        $this->data['order_type_dinein'] = array('name' => 'order_type',
            'type' => 'radio',
            'class' => 'requiredTextField order_type_reservation',
            'field-name' => 'Tipe Pesanan',
            'placeholder' => '',
            'checked' => 'checked',
            'value' => "1"
        );
        $this->data['order_type_takeaway'] = array('name' => 'order_type',
            'type' => 'radio',
            'class' => 'requiredTextField order_type_reservation',
            'field-name' => 'Tipe Pesanan',
            'placeholder' => '',
            'checked' => ($this->form_validation->set_value('order_type', $form_data->order_type) == 2) ? 'true' : '',
            'value' => "2");
        $this->data['order_type_delivery'] = array('name' => 'order_type',
            'type' => 'radio',
            'class' => 'requiredTextField order_type_reservation',
            'field-name' => 'Tipe Pesanan',
            'placeholder' => '',
            'checked' => ($this->form_validation->set_value('order_type', $form_data->order_type) == 3) ? 'true' : '',
            'value' => "3");
        $this->data['table'] = $this->store_model->get_table_dropdown();
        $this->data['table'][0] = "Pilih Meja";
        $this->data['categories'] = $this->cashier_model->get_category_by_store($this->data['data_store']->id);
        $this->data['menus'] = $this->cashier_model->get_menus_by_store($this->data['data_store']->id);
        if ($this->data['setting']['zero_stock_order'] == 0) {
            $this->order_model->all_menu_ingredient_with_stock($this->data['menus']);
        } else {
            foreach ($this->data['menus'] as $m) {
                $m->total_available = 0;
            }
        }
        $taxes = $this->db->get('taxes')->result();
        $tax_method = $this->data['setting']['tax_service_method'];
        $taxes_dine_in = $this->tax_model->get_taxes(1,$tax_method,1);
        $taxes_takeaway = $this->tax_model->get_taxes(2,$tax_method,1);
        $taxes_delivery = $this->tax_model->get_taxes(3,$tax_method,1);
        $tax_percentages = 0;
        foreach ($taxes as $t) {
            $tax_percentages += $t->tax_percentage;
        }
        $this->data['tax_percentages'] = $tax_percentages;
        $this->data['taxes'] = $taxes;
        $this->data['taxes_dine_in'] = $taxes_dine_in;
        $this->data['taxes_takeaway'] = $taxes_takeaway;
        $this->data['taxes_delivery'] = $taxes_delivery;
        $this->get_order_list_reservation($id);
        $this->load->model("delivery_cost_model");
        $this->data['order_data'] = $this->order_model->get_all_where("order", array("reservation_id" => $id));
        if (sizeof($this->data['order_data']) > 0) {
            $this->data['order_data'] = $this->data['order_data'][0];
        }
        $this->data['delivery_cost_lists'] = $this->delivery_cost_model->get('enum_delivery_cost')->result();
        $this->data['content'] .= $this->load->view('reservation_edit_v.php', $this->data, true);

        $this->render('cashier');
    }


    function edit_reservation(){
        //validate form input
        $id = $this->input->post("id");
        $this->load->model('reservation_model');
        $form_data = $this->store_model->get_one('reservation', $id);
        $this->load->model('store_model');
        $this->form_validation->set_rules('customer_name', 'nama', 'required|max_length[100]');
        $this->form_validation->set_rules('phone', 'nomor kontak', 'required|numeric|max_length[20]');
        $this->form_validation->set_rules('book_date', 'waktu reservasi', 'required|callback__book_date_check[' . $id . ']');
        $this->form_validation->set_rules('down_payment', 'DP', 'greater_than[-1]');
        $this->form_validation->set_rules('book_note', 'meja', 'max_length[500]');

        if ($this->input->post('order_type') == 1 ) {

            $this->form_validation->set_rules('customer_count', 'jumlah tamu', 'required|is_natural_no_zero');
            $this->form_validation->set_rules('table_id', 'Nomor Meja', 'required|is_natural_no_zero');

        }
        if (isset($_POST) && !empty($_POST)) {
           
            if ($this->form_validation->run() === TRUE) {
                $created_at = date("Y-m-d H:i:s");
                $created_by = $this->data['user_profile_data']->id;
                $book_date = $this->input->post('book_date');
                $detail = $this->input->post('detail_menu');
                $down_payment = $this->input->post("down_payment");
                $dp_type = $this->input->post('dp_type');
                $order_type = $this->input->post('order_type');
                $reservation_delivery_cost_id = $this->input->post('reservation_delivery_cost_id');
                $reservation_delivery_cost = 0;
                if ($reservation_delivery_cost_id != "") {
                    $enum_delivery = $this->store_model->get_one("enum_delivery_cost", $reservation_delivery_cost_id);
                    $reservation_delivery_cost = $enum_delivery->delivery_cost;
                }

                $data_array = array('customer_name' => $this->input->post('customer_name'),
                    'customer_count' => $this->input->post('customer_count'),
                    'customer_address' => $this->input->post('customer_address'),
                    'table_id' => $this->input->post('table_id'),
                    'book_date' => date('Y-m-d H:i:s', strtotime($book_date)),
                    'phone' => $this->input->post('phone'),
                    'book_note' => $this->input->post('book_note'),
                    'status' => 1,
                    'modified_at' => $created_at,
                    'modified_by' => $created_by,
                    'down_payment' => $down_payment,
                    'dp_type' => $dp_type,
                    'order_type' => $order_type,
                );
                if ($down_payment > 0 && $form_data->down_payment != $down_payment) {
                    $data_array['created_at'] = $created_at;
                }
                $save = $this->store_model->save('reservation', $data_array, $id);
                if ($form_data->table_id != $this->input->post('table_id')) {
                    $this->store_model->save('table', array('table_status' => 1), $form_data->table_id);
                }
                if ($form_data->down_payment == 0 && $down_payment > 0) {
                    $this->load->model("account_model");
                    $account_bank = $this->account_model->get_data($this->data['setting']['bank_dp_account_id']);
                    $account_cash = $this->account_model->get_data($this->data['setting']['cash_account_id']);
                    $account_payable = $this->account_model->get_data($this->data['setting']['hutang_dp_account_id']);
                    if (sizeof($account_bank) > 0 && sizeof($account_cash) > 0 && sizeof($account_payable) > 0) {
                        $account_data = array(
                            'has_synchronized' => 0,
                            'store_id' => $this->data['setting']['store_id'],
                            'account_id' => ($dp_type == 1 ? $account_cash->id : $account_bank->id),
                            'entry_type' => 7,
                            'foreign_id' => $id,
                            'info' => "Down Payment Reservasi",
                            'debit' => (($dp_type == 1 ? $account_cash->default_balance : $account_bank->default_balance) == 1 ? $down_payment : 0),
                            'credit' => (($dp_type == 1 ? $account_cash->default_balance : $account_bank->default_balance) == 0 ? $down_payment : 0),
                            'created_at' => date("Y-m-d H:i:s")
                        );
                        $this->account_model->save('account_data', $account_data);
                        $account_data = array(
                            'has_synchronized' => 0,
                            'store_id' => $this->data['setting']['store_id'],
                            'account_id' => $account_payable->id,
                            'entry_type' => 7,
                            'foreign_id' => $id,
                            'info' => "Down Payment Reservasi",
                            'debit' => ($account_payable->default_balance == 1 ? $down_payment : 0),
                            'credit' => ($account_payable->default_balance == 0 ? $down_payment : 0),
                            'created_at' => date("Y-m-d H:i:s")
                        );
                        $this->account_model->save('account_data', $account_data);
                    }
                }
                $data_order = $this->order_model->get_all_where("order", array("reservation_id" => $id));
                if (sizeof($data_order) > 0) {
                    $data_order = $data_order[0];
                    $order_id = $data_order->id;
                    $this->order_model->update_order_by_id($order_id, array(
                        'table_id' => ($this->input->post('table_id') != "" ? $this->input->post('table_id') : 0),
                        'customer_name' => $this->input->post('customer_name'),
                        'customer_phone' => $this->input->post('phone'),
                        'customer_address' => $this->input->post('customer_address'),
                        'is_take_away' => ($order_type == 2 ? 1 : 0),
                        'is_delivery' => ($order_type == 3 ? 1 : 0),
                        'created_at' => $created_at,
                        'created_by' => $created_by,
                        'has_synchronized' => 0,
                        'start_order' => date('Y-m-d H:i:s', strtotime($book_date)),
                        'reservation_id' => $id,
                        'delivery_cost_id' => ($reservation_delivery_cost_id != "" ? $reservation_delivery_cost_id : NULL),
                        'delivery_cost' => $reservation_delivery_cost,
                    ));
                    $order_menus = $this->order_model->get_all_where("order_menu", array("order_id" => $order_id));
                    foreach ($order_menus as $om) {
                        $this->order_model->delete_by_limit("order_menu_side_dish", array("order_menu_id" => $om->id), 0);
                        $this->order_model->delete_by_limit("order_menu_option", array("order_menu_id" => $om->id), 0);
                    }
                    $this->order_model->delete_by_limit("order_menu", array("order_id" => $order_id), 0);

                    for ($x = 0; $x < sizeof($detail['menu_id']); $x++) {
                        $data_order_menu = array(
                            'menu_id' => $detail['menu_id'][$x],
                            'order_id' => $order_id,
                            'quantity' => $detail['quantity'][$x],
                            'note' => $detail['notes'][$x],
                            'created_at' => $created_at,
                            'created_by' => $created_by,
                            'cooking_status' => 0,
                            'dinein_takeaway' => 0
                        );
                        $result = $this->order_model->save_order_menu($data_order_menu);
                        $menu_packages = $this->order_model->get_all_where("menu_promo", array("parent_menu_id" => $detail['menu_id'][$x]));
                        foreach ($menu_packages as $m) {
                            $this->order_model->save("order_package_menu", array(
                                "order_menu_id" => $result,
                                "menu_id" => $m->package_menu_id,
                                "quantity" => $m->quantity * $detail['quantity'][$x],
                                "cooking_status" => 0,
                                "process_status" => 0,
                                "is_check" => 0
                            ));
                        }
                        if (!empty($detail['side_dish'][$x])) $this->order_model->save_side_dish_order_menu($detail['side_dish'][$x], $result);
                        if (!empty($detail['option'][$x]) && $detail['option'][$x] != '0') $this->order_model->save_option_order_menu($detail['option'][$x], $result);
                    }
                } else {
                    if (!empty($detail) && sizeof($detail) > 0) {
                        $data_order = array(
                            'table_id' => ($this->input->post('table_id') != "" ? $this->input->post('table_id') : 0),
                            'customer_name' => $this->input->post('customer_name'),
                            'customer_phone' => $this->input->post('phone'),
                            'customer_address' => $this->input->post('customer_address'),
                            'is_take_away' => ($order_type == 2 ? 1 : 0),
                            'is_delivery' => ($order_type == 3 ? 1 : 0),
                            'created_at' => $created_at,
                            'created_by' => $created_by,
                            'has_synchronized' => 0,
                            'start_order' => date('Y-m-d H:i:s', strtotime($book_date)),
                            'reservation_id' => $id,
                            'delivery_cost_id' => ($reservation_delivery_cost_id != "" ? $reservation_delivery_cost_id : NULL),
                            'delivery_cost' => $reservation_delivery_cost,
                        );
                        $order_id = $this->order_model->save_order($data_order);
                        for ($x = 0; $x < sizeof($detail['menu_id']); $x++) {
                            $data_order_menu = array(
                                'menu_id' => $detail['menu_id'][$x],
                                'order_id' => $order_id,
                                'quantity' => $detail['quantity'][$x],
                                'note' => $detail['notes'][$x],
                                'created_at' => $created_at,
                                'created_by' => $created_by,
                                'cooking_status' => 0,
                                'dinein_takeaway' => 0
                            );
                            $result = $this->order_model->save_order_menu($data_order_menu);
                            $menu_packages = $this->order_model->get_all_where("menu_promo", array("parent_menu_id" => $detail['menu_id'][$x]));
                            foreach ($menu_packages as $m) {
                                $this->order_model->save("order_package_menu", array(
                                    "order_menu_id" => $result,
                                    "menu_id" => $m->package_menu_id,
                                    "quantity" => $m->quantity * $detail['quantity'][$x],
                                    "cooking_status" => 0,
                                    "process_status" => 0,
                                    "is_check" => 0
                                ));
                            }
                            if (!empty($detail['side_dish'][$x])) $this->order_model->save_side_dish_order_menu($detail['side_dish'][$x], $result);
                            if (!empty($detail['option'][$x]) && $detail['option'][$x] != '0') $this->order_model->save_option_order_menu($detail['option'][$x], $result);
                        }
                    }
                }
                if ($save === false) {
                    $success = false;
                    $this->data['message'] =  'Gagal menyimpan data';
                } else {
                    $success = true;
                    $this->data['message'] =  'Berhasil menyimpan data';
                    $this->session->set_flashdata('message_success', 'Berhasil menyimpan data');
                     if (!empty($detail) && sizeof($detail) > 0) {
                        $detail_order = $this->checker_model->get_order_menu_reservation_by_id($id);
                    }
                    $table = $this->table_model->get_table($this->input->post("table_id"));
                }
            }else{
                $success = false;
                $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            }
        }
        echo json_encode(
                array(
                    "success" => $success,
                    "message" => $this->data['message'],
                    "detail_order"=>isset($detail_order)?$detail_order[0]:array(),
                    "reservation_id"=>$id,
                    "order_id"=>isset($order_id)?$order_id:0,
                    "customer_name"=>$this->input->post('customer_name'),
                    "reservation_date"=>isset($book_date)?date("d/m/Y H:i:s",strtotime($book_date)):"",
                    "table_id"=>$this->input->post('table_id'),
                    "table_number"=>isset($table->table_name)?$table->table_name:"",
                    "operator_name"=>$this->data['user_profile_data']->name      
                )
        );
        die();
    }

    function get_order_list_reservation($reservation_id = "")
    {
        $order_reservation = $this->cashier_model->get_all_where("order", array("reservation_id" => $reservation_id, "reservation_id !=" => 0));
        $order_list_data = "";
        if (sizeof($order_reservation) > 0) {
            $order_reservation = $order_reservation[0];
            $order_menus = $this->order_model->get_order_menu_by_order_id($order_reservation->id);
            foreach ($order_menus as $o) {
                $side_dish = array();
                $option = array();
                $order_side_dish = $this->order_model->get_side_dish_by_order_menu($o->id, $o->is_promo);
                $order_option = $this->order_model->get_option_by_order_menu($o->id);
                $total_sidedish = 0;
                foreach ($order_side_dish as $oss) {
                    array_push($side_dish, $oss->side_dish_id);
                    $total_sidedish += $oss->price * $o->quantity;
                }
                foreach ($order_option as $oss) {
                    array_push($option, $oss->menu_option_value_id);
                }
                $order_list_data .= '<tr class="tOrder" price="' . ($o->menu_price) . '" total_sidedish="' . $total_sidedish . '" is_taxes="' . $o->use_taxes . '">';
                $order_list_data .= '<td>';
                $order_list_data .= '<input type="hidden" name="detail_menu[side_dish][]" class="side_dish" value="' . implode(",", $side_dish) . '">';
                $order_list_data .= '<input type="hidden" name="detail_menu[option][]" class="option" value="' . implode(",", $option) . '">';
                $order_list_data .= '<input type="hidden" name="detail_menu[menu_id][]" class="menu_id" value="' . $o->menu_id . '">';
                $order_list_data .= '<input type="hidden" name="detail_menu[quantity][]" class="qty" value="' . $o->quantity . '">';
                $order_list_data .= '<input type="hidden" name="detail_menu[notes][]" class="notes" value="' . $o->note . '">';
                $order_list_data .= $o->menu_name;
                foreach ($order_option as $oo) {
                    $order_list_data .= ' <br/>(' . $oo->option_name . ' - ' . $oo->option_value_name . ')';
                }

                foreach ($order_side_dish as $oss) {
                    $order_list_data .= '<br/>-- ' . $oss->name . ' (' . $oss->price . ')';
                }

                $order_list_data .= '</td>';
                $order_list_data .= '<td class="border-side tb-align-right">' . $o->quantity . '</td>';
                $order_list_data .= '<td class="tb-align-right">Rp ' . number_format(($o->menu_price * $o->quantity) + $total_sidedish, 0, "", ".") . '</td>';
                $order_list_data .= '<td class="tb-align-right" style="padding-right: 10px"><a href="javascript:void(0);" class="btn btn-danger btn-xs remove_reservation_menu"><i class="fa fa-remove"></i></a></td>';
                $order_list_data .= '</tr>';
            }
        }

        $this->data['order_list'] = $order_list_data;

    }

    public function delete()
    {

        $id = $this->uri->segment(3);

        if (empty($id)) {
            redirect('reservation');
        }

        $form_data = $this->store_model->get_one('reservation', $id);

        if (empty($form_data)) {
            redirect('reservation');
        }
        $table = $this->store_model->get_one("table", $form_data->table_id);
        if (sizeof($table) > 0) {
            if ($table->table_status == 6) {
                $this->store_model->update_where("table", array("table_status" => 1), array("table_id" => $table->table_id));
            }
        }
        $order = $this->store_model->get_all_where("order", array("reservation_id" => $id));
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
                    'foreign_id' => $id,
                    'info' => "Cancel Down Payment Reservasi",
                    'debit' => ($account_payable->default_balance == 0 ? $down_payment : 0),
                    'credit' => ($account_payable->default_balance == 1 ? $down_payment : 0),
                    'created_at' => date("Y-m-d H:i:s")
                );
                $this->account_model->save('account_data', $account_data);
                $account_data = array(
                    'has_synchronized' => 0,
                    'store_id' => $this->data['setting']['store_id'],
                    'account_id' => $account_other_income->id,
                    'entry_type' => NULL,
                    'foreign_id' => $id,
                    'info' => "Cancel Down Payment Reservasi",
                    'debit' => ($account_other_income->default_balance == 0 ? $down_payment : 0),
                    'credit' => ($account_other_income->default_balance == 1 ? $down_payment : 0),
                    'created_at' => date("Y-m-d H:i:s")
                );
                $this->account_model->save('account_data', $account_data);
            }
        }
        $result = $this->store_model->delete('reservation', $id);
        if ($result) {
            $this->session->set_flashdata('message_success', 'Berhasil menghapus data');
        } else {
            $this->session->set_flashdata('message', 'Error. Gagal menghapus data');
        }

        redirect('reservation', 'refresh');
    }


    public function get_table_reservation()
    {
        $book_date = $this->input->post('book_date');
        $book_date = strtotime($book_date);

        $this->load->model('reservation_model');
        $this->load->model('table_model');

        $booking_start_lock = $this->data['setting']['booking_start_lock'];
        $reservation_in_table = $this->reservation_model->get('reservation')->result();

        $table = $this->table_model->get_data_all_table();

        $msg = "<option value='0'>Pilih Meja</option>";
        foreach ($table as $key => $row) {
            $reservation_in_table = $this->reservation_model->get_table($row->id);

            $is_merged = $this->order_model->is_table_merge($row->id);

            if (!empty($reservation_in_table)) {
                $from_time = strtotime("-" . $booking_start_lock . " minutes", strtotime($reservation_in_table->book_date));
                if ($book_date >= $from_time) {

                    if (!empty($is_merged) && $book_date > strtotime("+12 hours", now())) {
                        $msg .= "<option value='" . $row->id . "'>" . $row->table_name . "</option>";

                    } else if (empty($is_merged)) {
                        $msg .= "<option value='" . $row->id . "'>" . $row->table_name . "</option>";
                    }

                }
            } else {

                if (!empty($is_merged) && $book_date > strtotime("+12 hours", now())) {
                    $msg .= "<option value='" . $row->id . "'>" . $row->table_name . "</option>";

                } else if (empty($is_merged)) {
                    $msg .= "<option value='" . $row->id . "'>" . $row->table_name . "</option>";
                }


            }


        }

        $return_data['status'] = TRUE;
        $return_data['msg'] = $msg;

        echo json_encode($return_data);

    }

    public function temp_save_order_menu()
    {
        if ($this->input->is_ajax_request()) {
            $menu_id = $this->input->post('menu_id');
            $count = $this->input->post('count');
            $option = $this->input->post('option');
            $side_dish = $this->input->post('side_dish');
            $notes = $this->input->post('notes');
            $outlet_id = $this->input->post('outlet_id');

            $return_data['status'] = FALSE;
            $return_data['msg'] = "";
            $ingredient_menu_id = $menu_id;
            $data_menu = $this->order_model->get_one("menu", $menu_id);
            $menu_ingredient = new stdclass();
            $menu_ingredient = $this->order_model->one_menu_ingredient_with_stock($ingredient_menu_id);
            $total_available = $menu_ingredient->total_available;
            if ($this->data['setting']['zero_stock_order'] == 0 && $count > $total_available) {
                $result = FALSE;
                $return_data['status'] = FALSE;
                $return_data['msg'] = "Gagal menambah pesanan, stok tidak mencukupi. Lakukan refresh halaman.";
            } else {
                $result = TRUE;
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
                $total_sidedish = 0;
                if ($side_dish != "") {

                    foreach (explode(",", $side_dish) as $sdh) {
                        $sdh_data = $this->order_model->get_one("side_dish", $sdh);
                        $total_sidedish += $sdh_data->price * $count;

                    }
                }
                $order_list_data = '<tr class="tOrder" price="' . ($data_menu->menu_price) . '" total_sidedish="' . $total_sidedish . '" is_taxes="' . $data_menu->use_taxes . '">';
                $order_list_data .= '<td>';
                $order_list_data .= '<input type="hidden" name="detail_menu[side_dish][]" class="side_dish" value="' . $side_dish . '">';
                $order_list_data .= '<input type="hidden" name="detail_menu[option][]" class="option" value="' . $option . '">';
                $order_list_data .= '<input type="hidden" name="detail_menu[menu_id][]" class="menu_id" value="' . $menu_id . '">';
                $order_list_data .= '<input type="hidden" name="detail_menu[quantity][]" class="qty" value="' . $count . '">';
                $order_list_data .= '<input type="hidden" name="detail_menu[notes][]" class="notes" value="' . $notes . '">';
                $order_list_data .= $data_menu->menu_name;
                if (!empty($option) && $option != "0") {
                    foreach (explode(",", $option) as $opt) {
                        $opt_data = $this->order_model->get_option_value_by_id($opt);
                        $order_list_data .= ' <br/>(' . $opt_data->option_name . ' - ' . $opt_data->option_value_name . ')';
                    }
                }

                if ($side_dish != "") {

                    foreach (explode(",", $side_dish) as $sdh) {
                        $sdh_data = $this->order_model->get_one("side_dish", $sdh);
                        $order_list_data .= '<br/>-- ' . $sdh_data->name . ' (' . $sdh_data->price . ')';

                    }
                }

                $order_list_data .= '</td>';
                $order_list_data .= '<td class="border-side tb-align-right">' . $count . '</td>';
                $order_list_data .= '<td class="tb-align-right">Rp ' . number_format(($data_menu->menu_price * $count) + $total_sidedish, 0, "", ".") . '</td>';
                $order_list_data .= '<td class="tb-align-right" style="padding-right: 10px"><a href="javascript:void(0);" class="btn btn-danger btn-xs remove_reservation_menu"><i class="fa fa-remove"></i></a></td>';
                $order_list_data .= '</tr>';

                $return_data['order_list'] = $order_list_data;
                $return_data['status'] = TRUE;

            }
            echo json_encode($return_data);
        }
    }

    function get_customer_auto_complete()
    {
        $is_delivery = $this->input->get("is_delivery");
        $is_takeaway = $this->input->get("is_takeaway");
        $params = array("b.is_delivery" => $is_delivery, "b.is_take_away" => $is_takeaway);
        $result = $this->order_model->get_customer_auto_complete($params);
        echo json_encode($result);
    }

    public function prints()
    {
        $data = $this->input->post("prints");
        $reservation_id = $data['reservation_id'];
        $template_id = $data['template_id'];
        $this->load->helper("printer_helper");
        $this->data['reservation'] = $this->store_model->get_one('reservation', $reservation_id);
        if (sizeof($this->data['reservation']) == 0) redirect(base_url("reservation"));
        $this->data['order_reservation'] = $this->cashier_model->get_all_where("order", array("reservation_id" => $reservation_id));
        $detail = array();
        if (sizeof($this->data['order_reservation']) > 0) {
            $this->data['order_reservation'] = $this->data['order_reservation'][0];
            $order_menus = $this->order_model->get_order_menu_by_order_id($this->data['order_reservation']->id);
            foreach ($order_menus as $o) {
                $o->menu_side_dish = $this->order_model->get_side_dish_by_order_menu($o->id, $o->is_promo);
                $o->menu_options = $this->order_model->get_option_by_order_menu($o->id);
                $detail[] = $o;
            }
        }
        $this->data['tax_method'] = $this->data['setting']['tax_service_method'];
        $this->data['round'] = $this->data['setting']['nearest_round'];
        $this->data['taxes'] = $this->tax_model->get_taxes($this->data['reservation']->order_type, $this->data['tax_method'], 1);
        $this->data['detail'] = $detail;
        $template = $this->order_model->get_one("template_reservation_note", $template_id);
        $notes = (sizeof($template) > 0 ? explode("\n", $template->note) : array());
        $this->data['template'] = $notes;
        //get printer dot matrix reservation
        $this->load->model("setting_printer_model");
        $printer_arr_obj = $this->setting_printer_model->get_printer_by_enum_printer_type(array("name_type"=>"printer_matrix_reservation"));
        foreach ($printer_arr_obj as $printer_obj) {
            $printer_location = $printer_obj->name_printer;
            print_reservation_dp($printer_location, $this->data);
        }
        redirect(base_url("reservation"));
    }

    public function get_online_data()
    {
        $url = $this->config->item("get_data_reservation_online_url");
        $send = array(
            "store_id" => $this->config->item("register_reservation_online_store_id")
        );
        $results = $this->curl_connect($send, $url);
        if (is_array($results)) {
            foreach ($results as $r) {
                $params = array(
                    'customer_name' => $r->customer_name,
                    'customer_count' => $r->customer_count,
                    'customer_address' => $r->customer_address,
                    'table_id' => 0,
                    'book_date' => date('Y-m-d H:i:s', strtotime($r->book_date)),
                    'phone' => $r->phone,
                    'book_note' => "",
                    'status' => 1,
                    'created_at' => $r->created_at,
                    'down_payment' => $r->down_payment,
                    'dp_type' => $r->dp_type,
                    'order_type' => $r->order_type,
                );
                $this->order_model->save("reservation", $params);
            }
            if (sizeof($results) > 0) {
                $return = array(
                    "status" => true,
                    "message" => "Pengambilan data reservasi online berhasil dilakukan!"
                );
            } else {
                $return = array(
                    "status" => false,
                    "message" => "Belum ada data baru reservasi online!"
                );
            }
        } else {
            $return = array(
                "status" => false,
                "message" => "Pengambilan data reservasi online gagal,cek kembali koneksi anda!"
            );
        }
        echo json_encode($return);
    }

    function curl_connect($data, $url)
    {
        //open connection
        $ch = curl_init();
        curl_setopt_array($ch, array(CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => 'Api Get Reservation',
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $data));

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