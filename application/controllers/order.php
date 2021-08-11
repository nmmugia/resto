<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Order extends Store_config
{

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     *        http://example.com/index.php/welcome
     *    - or -
     *        http://example.com/index.php/welcome/index
     *    - or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */

    function __construct()
    {
        parent::__construct();

        $this->load->model('categories_model');
        $this->load->model('order_model');

        $this->data['data_store']  = array();
        $this->data['data_outlet'] = array();

        $this->data['data_store']  = $this->get_data_store();
        $this->data['data_outlet'] = $this->get_data_outlet();
        if ($this->ion_auth->logged_in()) {
          $user                    = $this->ion_auth->user()->row();
          $user_groups             = $this->ion_auth->get_users_groups($user->id)->result();
          $this->groups_access->group_id = $user_groups[0]->id;
        }
    }

    public function index()
    {
        if (! $this->ion_auth->logged_in()) {
            $this->load->view('header_v');
            $this->load->view('login_v', $this->data);
        }
        else if ($this->groups_access->have_access('dinein')) {
            $this->data['waiter']       = $this->ion_auth->user()->row();
            $this->data['categories']   = array();
            $this->data['menus']        = array();
            $this->data['order_list']   = array();
            $this->data['table_status'] = '';

            if (! empty($this->data['data_outlet'])) $this->data['categories'] = $this->categories_model->get_category_by_outlet($this->data['data_outlet'][0]->id);

            if (! empty($this->data['categories'])) $this->data['menus'] = $this->categories_model->get_menu_by_category($this->data['categories'][0]->id,'');

            if ($this->input->post('table_empty') != '') $this->session->set_userdata('table_empty', $this->input->post('table_empty'));

            if ($this->session->userdata('table_empty') == 1) {
                $table_id     = $this->input->post('table_id');
                $table_status = $this->input->post('table_status');
                $number_guest = $this->input->post('number_guest');
                $order_id     = $this->input->post('order_id');


                if (($table_id != '') && ($table_status != '') && ($number_guest != '') && ($order_id != '')) {
                    $data_table_session = array('table_status' => $table_status, 'table_id' => $table_id, 'number_guest' => $number_guest, 'order_id' => $order_id);

                    $this->session->set_userdata($data_table_session);

                }

                $this->data['table_status'] = $this->session->userdata('table_status');
                $this->data['table_id']     = $this->session->userdata('table_id');
                $this->data['number_guest'] = $this->session->userdata('number_guest');
                $this->data['order_id']     = $this->session->userdata('order_id');

                $data_update = array('table_status' => 4);

                $result = $this->store_model->update_status_table($this->data['table_id'], $data_update);

                if ($result) {
                    $this->session->set_userdata('table_empty', 4);
                    $this->session->set_userdata('table_status', 4);
                    $this->data['table_status'] = $this->session->userdata('table_status');
                }

                if ($this->data['order_id'] == 0) {
                    $data                   = array('table_id' => $this->data['table_id'], 'subtotal_price' => 0, 'total_price' => 0, 'tax_price' => 0, 'purchase_method' => 1, 'payment_method' => 0);
                    $this->data['order_id'] = $this->order_model->save_order($data);
                    $this->session->set_userdata('order_id', $this->data['order_id']);
                }

                $this->data['order_payment'] = $this->order_model->get_order_by_id($this->data['order_id']);
                $this->data['order_list']    = $this->order_model->get_order_menu_by_order($this->data['order_id']);
            }
            else {
                if (($this->input->post('table_status_hidden') != '') && ($this->input->post('table_id_hidden') != '') && ($this->input->post('number_guest_hidden') != '') && ($this->input->post('order_id_hidden') != '')) {
                    $data_table_session = array('table_status' => $this->input->post('table_status_hidden'), 'table_id' => $this->input->post('table_id_hidden'), 'number_guest' => $this->input->post('number_guest_hidden'), 'order_id' => $this->input->post('order_id_hidden'));

                    $this->session->set_userdata($data_table_session);

                }

                $this->data['table_status'] = $this->session->userdata('table_status');
                $this->data['table_id']     = $this->session->userdata('table_id');
                $this->data['number_guest'] = $this->session->userdata('number_guest');
                $this->data['order_id']     = $this->session->userdata('order_id');

                $this->data['order_payment'] = $this->order_model->get_order_by_id($this->data['order_id']);
                $this->data['order_list']    = $this->order_model->get_order_menu_by_order($this->data['order_id']);
            }

            $this->load->view('header_v');
            $this->load->view('order_v', $this->data);
        }
        else {
            redirect(base_url(), 'refresh');
        }
    }

    public function new_order()
    {
        if ($this->input->is_ajax_request()) {
            $table_id     = $this->input->post('table_id');
            $table_status = $this->input->post('table_status');
            $number_guest = $this->input->post('number_guest');
            $order_id     = $this->input->post('order_id');


            if (($table_id != '') && ($table_status != '') && ($number_guest != '') && ($order_id != '')) {
                $data_table_session = array('table_status' => $table_status, 'table_id' => $table_id, 'number_guest' => $number_guest, 'order_id' => $order_id);

                $this->session->set_userdata($data_table_session);

            }

            $this->data['table_status'] = $this->session->userdata('table_status');
            $this->data['table_id']     = $this->session->userdata('table_id');
            $this->data['number_guest'] = $this->session->userdata('number_guest');
            $this->data['order_id']     = $this->session->userdata('order_id');

            $data_update = array('table_status' => 4);

            $result = $this->store_model->update_status_table($this->data['table_id'], $data_update);

            if ($result) {
                $this->session->set_userdata('table_empty', 4);
                $this->session->set_userdata('table_status', 4);
                $this->data['table_status'] = $this->session->userdata('table_status');
            }

            if ($this->data['order_id'] == 0) {
                $data                   = array('table_id' => $this->data['table_id'], 'subtotal_price' => 0, 'total_price' => 0, 'tax_price' => 0, 'purchase_method' => 1, 'payment_method' => 0);
                $this->data['order_id'] = $this->order_model->save_order($data);
                $this->session->set_userdata('order_id', $this->data['order_id']);
            }

            $return_data['number_guest'] = $this->data['number_guest'];
            $return_data['table_status'] = $this->data['table_status'];
            $return_data['table_id']     = $this->data['table_id'];
            $return_data['order_id']     = $this->data['order_id'];
            $return_data['status_name']  = $this->categories_model->get_one('enum_table_status', $this->data['table_status'])->status_name;
            echo json_encode($return_data);
        }
    }

    public function empty_order()
    {
        if ($this->input->is_ajax_request()) {
            $table_id     = $this->input->post('table_id');
            $table_status = $this->input->post('table_status');
            $redirect     = base_url('table');
            if ((! empty($table_id)) or ($table_id != 0)) {
                if ($table_status != 1) {
                    //update status table
                    $data_update = array('table_status' => 1);
                    $result      = $this->store_model->update_status_table($table_id, $data_update);

                    if ($result) {						
						$data_update = array('order_menu.cooking_status' => 4);
						$result      = $this->order_model->update_status_cooking_canceled($table_id, $data_update);
												
                        $this->session->set_flashdata('message', 'Meja berhasil dikosongkan');
                        $redirect = base_url('table');
                    }
                    else {
                        $this->session->set_flashdata('message', 'Meja tidak dapat dikosongkan');
                        $redirect = base_url('order');
                    }
                }
                else {
                    $this->session->set_flashdata('message', 'Meja berhasil dikosongkan');
                    $redirect = base_url('table');
                }
            }
            else {
                $this->session->set_flashdata('message', 'Meja tidak dapat dikosongkan');
                $redirect = base_url('table');
            }

            $return_data['number_guest'] = 0;
            $return_data['table_status'] = 1;
            $return_data['table_id']     = $table_id;
            $return_data['order_id']     = 0;
            $return_data['status_name']  = $this->categories_model->get_one('enum_table_status', 1)->status_name;
            $return_data['redirect']     = $redirect;
            echo json_encode($return_data);
        }
    }

    public function checkout_order()
    {
        if ($this->input->is_ajax_request()) {
            $table_id     = $this->input->post('table_id');
            $table_status = $this->input->post('table_status');
            $redirect     = base_url('table');
            if ((! empty($table_id)) or ($table_id != 0)) {
                if ($table_status == 3) {
                    //update status table
                    $data_update  = array('table_status' => 2);
                    $table_status = 2;
                    $result       = $this->store_model->update_status_table($table_id, $data_update);

                    if ($result) {
                        //send notif to cashier

                        $this->session->set_flashdata('message', 'Checkout berhasil');
                        $redirect = base_url('table');
                    }
                    else {
                        $this->session->set_flashdata('message', 'Checkout gagal');
                        $redirect = base_url('order');
                    }
                }
                else {
                    $this->session->set_flashdata('message', 'Pesanan belum/sudah diproses, checkout tidak dapat dilakukan');
                    $redirect = base_url('table');
                }
            }
            else {
                $this->session->set_flashdata('message', 'Checkout gagal');
                $redirect = base_url('table');
            }

            $return_data['number_guest'] = 0;
            $return_data['table_status'] = $table_status;
            $return_data['table_id']     = $table_id;
            $return_data['order_id']     = 0;
            $return_data['status_name']  = $this->categories_model->get_one('enum_table_status', $table_status)->status_name;
            $return_data['redirect']     = $redirect;
            echo json_encode($return_data);
        }
    }

    public function process_neworder()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post('table_id') != '') $this->session->set_userdata('table_id', $this->input->post('table_id'));

            if ($this->input->post('table_status') != '') $this->session->set_userdata('table_status', $this->input->post('table_status'));

            $table_id     = $this->session->userdata('table_id');
            $table_status = $this->session->userdata('table_status');
            $redirect     = base_url('table');
            if ((! empty($table_id)) || ($table_id != 0)) {
                if ($table_status == 4) {
                    //update status table
                    $data_update  = array('table_status' => 3);
                    $table_status = 3;
                    $result       = $this->store_model->update_status_table($table_id, $data_update);

                    if ($result) {
                        $this->session->set_flashdata('message', 'Pesanan berhasil diproses');
                        $redirect = base_url('table');
                    }
                    else {
                        $this->session->set_flashdata('message', 'Pesanan gagal diproses');
                        $redirect = base_url('table');
                    }
                }
                else if ($table_status == 3 || $table_status == 2 || $table_status == 5) {
                    //update status table
                    $data_update  = array('table_status' => 3);
                    $table_status = 3;
                    $result       = $this->store_model->update_status_table($table_id, $data_update);

                    $this->session->set_flashdata('message', 'Pesanan berhasil diproses');
                    $redirect = base_url('table');
                }
                else {
                    $this->session->set_flashdata('message', 'Tidak ada pesanan yang dapat diproses.');
                    $redirect = base_url('order');
                }
            }
            else {
                $this->session->set_flashdata('message', 'Pesanan gagal diproses');
                $redirect = base_url('table');
            }

            $return_data['number_guest'] = 0;
            $return_data['table_status'] = $table_status;
            $return_data['table_id']     = $table_id;
            $return_data['order_id']     = 0;
            $return_data['status_name']  = $this->categories_model->get_one('enum_table_status', $table_status)->status_name;
            $return_data['redirect']     = $redirect;
            echo json_encode($return_data);
        }
    }

    public function get_menu_by_catergory()
    {
        if (! $this->ion_auth->logged_in()) {
            redirect(base_url(), 'refresh');
        }
        else if ($this->groups_access->have_access('dinein')) {
            $category_id = $this->input->post('category_id');
            $menus       = $this->categories_model->get_menu_by_category($category_id,'');

            echo json_encode($menus);
        }
        else {
            redirect(base_url(), 'refresh');
        }
    }

    public function empty_table()
    {
        if (! $this->ion_auth->logged_in()) {
            redirect(base_url(), 'refresh');
        }
        else if ($this->groups_access->have_access('dinein') || $this->groups_access->have_access('checkout')) {
            $table_id     = $this->input->post('table_id');
            $table_status = $this->input->post('table_status');
            if ((! empty($table_id)) or ($table_id != 0)) {
                if ($table_status != 1) {
                    //update status table
                    $data_update = array('table_status' => 1);
                    $result      = $this->store_model->update_status_table($table_id, $data_update);

                    if ($result) {
					
                        $this->session->set_flashdata('message', 'Meja berhasil dikosongkan');
                        redirect('table', 'refresh');
                    }
                    else {
                        $this->session->set_flashdata('message', 'Meja tidak dapat dikosongkan');
                        redirect('order', 'refresh');
                    }
                }
                else {
                    $this->session->set_flashdata('message', 'Meja berhasil dikosongkan');
                    redirect('table', 'refresh');
                }
            }
            else {
                $this->session->set_flashdata('message', 'Meja tidak dapat dikosongkan');
                redirect('table', 'refresh');
            }
        }
        else {
            redirect(base_url(), 'refresh');
        }
    }

    public function process_order()
    {
        if (! $this->ion_auth->logged_in()) {
            redirect(base_url(), 'refresh');
        }
        else if ($this->groups_access->have_access('dinein')) {
            if ($this->input->post('table_id') != '') $this->session->set_userdata('table_id', $this->input->post('table_id'));

            if ($this->input->post('table_status') != '') $this->session->set_userdata('table_status', $this->input->post('table_status'));

            $table_id     = $this->session->userdata('table_id');
            $table_status = $this->session->userdata('table_status');
            if ((! empty($table_id)) || ($table_id != 0)) {
                if ($table_status == 4) {
                    //update status table
                    $data_update = array('table_status' => 3);
                    $result      = $this->store_model->update_status_table($table_id, $data_update);

                    if ($result) {
                        //send notif to kitchen

                        // $this->session->set_flashdata('message', 'Pesanan berhasil diproses');
                        // redirect('table', 'refresh');


                        // $data_update = array(
                        // 'order_menu.cooking_status' => 2
                        // );
                        // $result = $this->order_model->update_status_cooking_menu_order($table_id,$data_update);
                        // if($result) {
                        // $this->session->set_flashdata('message', 'Pesanan berhasil diproses');
                        // redirect('table', 'refresh');
                        // }
                        // else
                        // {
                        // $this->session->set_flashdata('message', 'Status cooking gagal diupdate');
                        // redirect('table', 'refresh');
                        // }
                    }
                    else {
                        $this->session->set_flashdata('message', 'Pesanan gagal diproses');
                        // redirect('table', 'refresh');
                    }
                }
                else if ($table_status == 3 || $table_status == 2 || $table_status == 5) {
                    //send notif to kitchen

                    //update status table
                    $data_update = array('table_status' => 3);
                    $result      = $this->store_model->update_status_table($table_id, $data_update);

                    $this->session->set_flashdata('message', 'Pesanan berhasil diproses');
                    // redirect('table', 'refresh');

                    // $data_update = array(
                    // 'order_menu.cooking_status' => 2
                    // );
                    // $result = $this->order_model->update_status_cooking_menu_order($table_id,$data_update);
                    // if($result) {
                    // $this->session->set_flashdata('message', 'Pesanan berhasil diproses');
                    // redirect('table', 'refresh');
                    // }
                    // else
                    // {
                    // $this->session->set_flashdata('message', 'Status cooking gagal diupdate');
                    // redirect('table', 'refresh');
                    // }
                }
                else {
                    $this->session->set_flashdata('message', 'Tidak ada pesanan yang dapat diproses.');
                    redirect('order', 'refresh');
                }
            }
            else {
                $this->session->set_flashdata('message', 'Pesanan gagal diproses');
                // redirect('table', 'refresh');
            }
        }
        else {
            redirect(base_url(), 'refresh');
        }
    }

    public function checkout()
    {
        if (! $this->ion_auth->logged_in()) {
            redirect(base_url(), 'refresh');
        }
        else if ($this->groups_access->have_access('checkout')) {
            $table_id     = $this->input->post('table_id');
            $table_status = $this->input->post('table_status');
            if ((! empty($table_id)) or ($table_id != 0)) {
                if ($table_status == 3) {
                    //update status table
                    $data_update = array('table_status' => 2);
                    $result      = $this->store_model->update_status_table($table_id, $data_update);

                    if ($result) {
                        //send notif to cashier

                        $this->session->set_flashdata('message', 'Checkout berhasil');
                        redirect('table', 'refresh');
                    }
                    else {
                        $this->session->set_flashdata('message', 'Checkout gagal');
                        redirect('order', 'refresh');
                    }
                }
                else {
                    $this->session->set_flashdata('message', 'Pesanan belum/sudah diproses, checkout tidak dapat dilakukan');
                    redirect('table', 'refresh');
                }
            }
            else {
                $this->session->set_flashdata('message', 'Checkout gagal');
                redirect('table', 'refresh');
            }
        }
        else {
            redirect(base_url(), 'refresh');
        }
    }

    public function back_to_table_page()
    {
        if (! $this->ion_auth->logged_in()) {
            redirect(base_url(), 'refresh');
        }
        else if ($this->groups_access->have_access('dinein')) {
            redirect('table', 'refresh');
        }
        else {
            redirect(base_url(), 'refresh');
        }
    }

    public function get_menu_accessories()
    {
        if (! $this->ion_auth->logged_in()) {
            redirect(base_url(), 'refresh');
        }
        else if ($this->groups_access->have_access('dinein')) {
            $menu_id = $this->input->post('menu_id');
            $menu_data = $this->categories_model->get_one_menu($menu_id);

            if (! empty($menu_id)) {
                $data              = array();
                $data['side_dish'] = $this->order_model->get_side_dish_by_menu($menu_id, $menu_data->is_promo);
                $data['option']    = array();
                $option            = $this->order_model->get_option_by_menu($menu_id);

                $tmp = array();

                foreach ($option as &$value) {
                    $tmp[$value->option_id][] = $value;
                }

                foreach ($tmp as $type => $labels) {
                    $data['option'][] = array('option_id' => $type, 'option_value' => $labels);
                }

                echo json_encode($data);
            }
        }
        else {
            redirect(base_url(), 'refresh');
        }
    }

    public function save_order_menu()
    {
        if (! $this->ion_auth->logged_in()) {
            redirect(base_url(), 'refresh');
        }
        else if ($this->groups_access->have_access('dinein')) {
            $menu_id   = $this->input->post('menu_id');
            $order_id  = $this->input->post('order_id');
            $count     = $this->input->post('count');
            $option    = $this->input->post('option');
            $side_dish = $this->input->post('side_dish');
            $notes     = $this->input->post('notes');
            $is_edit   = $this->input->post('is_edit');
            $count_old = $this->input->post('count_old');

            $menu_price = $this->input->post('menu_price');

            $subtotal_price = $this->input->post('subtotal_price');
            $total_price    = $this->input->post('total_price');

            $data_order = array();
            // var_dump($is_edit);
            if ($is_edit == 'true') {
                $data   = array('count' => $count, 'options' => $option, 'side_dishes' => $side_dish, 'notes' => $notes);
                $result = $this->order_model->update_order_menu($menu_id, $data);

                $subtotal_price = $subtotal_price - ($count_old * $menu_price);
                $subtotal_price = $subtotal_price + ($count * $menu_price);
                $tax_price      = ($subtotal_price * 10) / 100;
                $total_price    = $subtotal_price + $tax_price;

                $data_order = array('subtotal_price' => $subtotal_price, 'total_price' => $total_price, 'tax_price' => $tax_price);
            }
            else {
                $data = array('menu_id' => $menu_id, 'order_id' => $order_id, 'count' => $count, 'options' => $option, 'side_dishes' => $side_dish, 'notes' => $notes, 'cooking_status' => 1);

                $result = $this->order_model->save_order_menu($data);

                $subtotal_price = $subtotal_price + ($count * $menu_price);
                $tax_price      = ($subtotal_price * 10) / 100;
                $total_price    = $subtotal_price + $tax_price;

                $data_order = array('subtotal_price' => $subtotal_price, 'total_price' => $total_price, 'tax_price' => $tax_price);

            }
            if ($result) {

                $result_update = $this->order_model->update_order_by_id($order_id, $data_order);
                if ($result_update) {
                    $data['order_payment'] = $this->order_model->get_order_by_id($order_id);
                    $data['order_list']    = $this->order_model->get_order_menu_by_order($order_id);
                    echo json_encode($data);
                }
                else
                    echo json_encode(0);
            }
            else
                echo json_encode(0);
        }
        else {
            redirect(base_url(), 'refresh');
        }
    }

    public function delete_order_menu()
    {
        if (! $this->ion_auth->logged_in()) {
            redirect(base_url(), 'refresh');
        }
        else if ($this->groups_access->have_access('dinein')) {
            $menu_id        = $this->input->post('menu_id');
            $order_id       = $this->input->post('order_id');
            $count          = $this->input->post('count');
            $menu_price     = $this->input->post('menu_price');
            $subtotal_price = $this->input->post('subtotal_price');
            $total_price    = $this->input->post('total_price');

            $subtotal_price = $subtotal_price - ($count * $menu_price);
            $tax_price      = ($subtotal_price * 10) / 100;
            $total_price    = $subtotal_price + $tax_price;

            $result = $this->order_model->delete_order_menu($menu_id);

            if ($result) {
                $data_order = array('subtotal_price' => $subtotal_price, 'total_price' => $total_price, 'tax_price' => $tax_price);

                $result_update = $this->order_model->update_order_by_id($order_id, $data_order);
                if ($result_update) {
                    $data['order_payment'] = $this->order_model->get_order_by_id($order_id);
                    $data['order_list']    = $this->order_model->get_order_menu_by_order($order_id);
                    echo json_encode($data);
                }
                else
                    echo json_encode(0);
            }
            else
                echo json_encode(0);
        }
        else {
            redirect(base_url(), 'refresh');
        }
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */