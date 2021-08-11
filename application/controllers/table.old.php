<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Table extends Store_config
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

        $this->load->model('order_model');

        $this->data['data_store']  = array();
        $this->data['data_outlet'] = array();

        $this->data['data_store']  = $this->get_data_store();
        $this->data['data_outlet'] = $this->get_data_outlet();

        if ($this->ion_auth->logged_in()) {
            $user                     = $this->ion_auth->user()->row();
            $user_groups              = $this->ion_auth->get_users_groups($user->id)->result();
            $this->data['user_id']    = $user->id;
            $this->data['user_name']  = $user->name;
            $this->data['group_id']   = $user_groups[0]->id;
            $this->data['group_name'] = $user_groups[0]->name;
        }
    }

    public function index()
    {

        if (! $this->ion_auth->logged_in()) {
            $this->load->view('header_v');
            $this->load->view('login_v', $this->data);
        }
        else if ($this->ion_auth->in_group(array('waiter')))
		{

            $store_id                 = $this->data['data_store'][0]->id;
            $floor_id                 = 1;
            $this->data['floor_name'] = '';
            $this->data['floor_id']   = '';

            $this->data['data_table'] = $this->store_model->get_table_by_floor($store_id, $floor_id);
            if (! (empty($this->data['data_table']))) {
                $this->data['floor_name'] = $this->data['data_table'][0]->floor_name;
                $this->data['floor_id']   = $this->data['data_table'][0]->floor_id;

                foreach ($this->data['data_table'] as $data_table) {
					if($data_table->table_status != 1)
					{
						$order_id = $this->store_model->get_order_by_table($data_table->table_id);
						if ($order_id) $data_table->order_id = $order_id->order_id;
						else $data_table->order_id = 0;
					}else $data_table->order_id = 0;
                }
            }
			
			// echo '<pre>';
			// print_r($this->data['data_table']);
			// echo '</pre>';
			
            $this->load->view('header_v');
            $this->load->view('table_v', $this->data);
        }
		else if($this->ion_auth->in_group(array('cashier')))
		{
			$store_id                 = $this->data['data_store'][0]->id;
            $floor_id                 = 1;
            $this->data['floor_name'] = '';
            $this->data['floor_id']   = '';

            $this->data['data_table'] = $this->store_model->get_table_by_floor($store_id, $floor_id);
            if (! (empty($this->data['data_table']))) {
                $this->data['floor_name'] = $this->data['data_table'][0]->floor_name;
                $this->data['floor_id']   = $this->data['data_table'][0]->floor_id;

                foreach ($this->data['data_table'] as $data_table) {
                    $order_id = $this->store_model->get_order_by_table($data_table->table_id);
                    if ($order_id) $data_table->order_id = $order_id->order_id;
                    else
                        $data_table->order_id = 0;
                }
            }

            $this->load->view('header_v');
            $this->load->view('table_v', $this->data);
		}
        else {
            redirect(base_url(), 'refresh');
        }
    }

    public function previous_floor()
    {

        if (! $this->ion_auth->logged_in()) {
            redirect(base_url(), 'refresh');
        }
        else if ($this->ion_auth->in_group(array('waiter'))) {
            $store_id                 = $this->input->post('store_id');
            $floor_id                 = $this->input->post('floor_id');
            $this->data['floor_name'] = '';
            $this->data['floor_id']   = '';

            $this->data['data_table'] = $this->store_model->get_table_by_floor($store_id, $floor_id);
            if (! (empty($this->data['data_table']))) {
                $this->data['floor_name'] = $this->data['data_table'][0]->floor_name;
                $this->data['floor_id']   = $this->data['data_table'][0]->floor_id;

                foreach ($this->data['data_table'] as $data_table) {
                    $order_id = $this->store_model->get_order_by_table($data_table->table_id);
                    if ($order_id) $data_table->order_id = $order_id->order_id;
                    else
                        $data_table->order_id = 0;
                }
            }
            echo json_encode($this->data);
        }
        else {
            redirect(base_url(), 'refresh');
        }
    }

    public function next_floor()
    {

        if (! $this->ion_auth->logged_in()) {
            redirect(base_url(), 'refresh');
        }
        else if ($this->ion_auth->in_group(array('waiter'))) {
            $store_id                 = $this->input->post('store_id');
            $floor_id                 = $this->input->post('floor_id');
            $this->data['floor_name'] = '';
            $this->data['floor_id']   = '';
            $this->data['order_id']   = '';

            $all_floor = $this->store_model->get_floor_by_store($store_id);
            if ($floor_id <= sizeof($all_floor)) {
                $this->data['data_table'] = $this->store_model->get_table_by_floor($store_id, $floor_id);
                if (! (empty($this->data['data_table']))) {
                    $this->data['floor_name'] = $this->data['data_table'][0]->floor_name;
                    $this->data['floor_id']   = $this->data['data_table'][0]->floor_id;

                    foreach ($this->data['data_table'] as $data_table) {
                        $order_id = $this->store_model->get_order_by_table($data_table->table_id);
                        if ($order_id) $data_table->order_id = $order_id->order_id;
                        else
                            $data_table->order_id = 0;
                    }
                }
                echo json_encode($this->data);
            }
            else {
                //echo "end";
            }
        }
        else {
            redirect(base_url(), 'refresh');
        }
    }

    public function change_table()
    {

        if (! $this->ion_auth->logged_in()) {
            redirect(base_url(), 'refresh');
        }
        else if ($this->ion_auth->in_group(array('waiter'))) {
            $first_table  = $this->input->post('first_table');
            $second_table = $this->input->post('second_table');
            $status_table = $this->input->post('status_table');

            if (! empty($first_table) && ! empty($second_table) && ! empty($status_table)) {
                $first_data_update = array('table_status' => 1);
                $result            = $this->store_model->update_status_table($first_table, $first_data_update);

                if ($result) {
                    $second_data_update = array('table_status' => $status_table);
                    $result             = $this->store_model->update_status_table($second_table, $second_data_update);
                    if ($result) {
                        $data_order_update = array('table_id' => $second_table);
                        $result            = $this->order_model->update_order_by_table($first_table, $data_order_update);
                        if ($result) {
                            //echo 'success';

                            $this->load->model('categories_model');
                            $table1 = $this->categories_model->get_one('table', $first_table);
                            $table2 = $this->categories_model->get_one('table', $second_table);
                            $order1 = $this->store_model->get_order_by_table($first_table);
                            if ($order1) {
                                $order1 = $order1->order_id;
                            }
                            else {
                                $order1 = 0;
                            }
                            $order2 = $this->store_model->get_order_by_table($second_table);
                            if ($order2) {
                                $order2 = $order2->order_id;
                            }
                            else {
                                $order2 = 0;
                            }

                            $return_data['table1']['number_guest'] = $table1->customer_count;
                            $return_data['table1']['table_status'] = $table1->table_status;
                            $return_data['table1']['table_id']     = $first_table;
                            $return_data['table1']['order_id']     = $order1;
                            $return_data['table1']['status_name']  = $this->categories_model->get_one('enum_table_status', $table1->table_status)->status_name;

                            $return_data['table2']['number_guest'] = $table2->customer_count;
                            $return_data['table2']['table_status'] = $table2->table_status;
                            $return_data['table2']['table_id']     = $second_table;
                            $return_data['table2']['order_id']     = $order2;
                            $return_data['table2']['status_name']  = $this->categories_model->get_one('enum_table_status', $table2->table_status)->status_name;
                            $return_data['status']                 = true;
                            echo json_encode($return_data);
                        }
                        else
                            echo 'failed';
                    }
                    else
                        echo 'failed';
                }
                else
                    echo 'failed';
            }
            else
                echo 'failed';
        }
        else {
            redirect(base_url(), 'refresh');
        }
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */