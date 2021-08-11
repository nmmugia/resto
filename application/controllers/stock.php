<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Stock extends Store_config {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
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
        $this->load->model('kitchen_model');
		
        $this->data['data_store']  = array();
        $this->data['data_outlet'] = array();
		$this->data['user'] = array();
		
        $this->data['data_store']  = $this->get_data_store();
        $this->data['data_outlet'] = $this->get_data_outlet();
		
        if ($this->ion_auth->logged_in()) {
            $user                    = $this->ion_auth->user()->row();
            $user_groups             = $this->ion_auth->get_users_groups($user->id)->result();
            $this->data['user_id']   = $user->id;
            $this->data['user_name'] = $user->name;
            $this->data['group_id']  = $user_groups[0]->id;
            $this->data['group_name']  = $user_groups[0]->name;
            $this->data['user'] = $user;
            $this->groups_access->group_id = $user_groups[0]->id;
        }
    }
	
	public function index()
	{
		if (! $this->ion_auth->logged_in()) {
            $this->load->view('header_v');
            $this->load->view('login_v', $this->data);
        }
        else if ($this->groups_access->have_access('kitchen',$this->data['group_id'])) 
		{
			if (! empty($this->data['data_outlet'])) $this->data['categories'] = $this->categories_model->get_category_by_outlet($this->data['data_outlet'][0]->id);
			
			if (! empty($this->data['categories'])) $this->data['menus'] = $this->categories_model->get_menu_by_category($this->data['categories'][0]->id,'all');
			
			// echo '<pre>';
			// print_r($this->data['menus']);
			// echo '</pre>';
			$this->load->view('header_v');
			$this->load->view('stock_v',$this->data);
		}
        else {
            redirect(base_url(), 'refresh');
        }
	}
	
	public function update_available_status()
	{
		if (! $this->ion_auth->logged_in()) {
            echo json_encode('false');
        }
        else if ($this->groups_access->have_access('kitchen')) {
            $menu_id = $this->input->post('menu_id');
            $available = $this->input->post('available');
            $quantity = $this->input->post('quantity');
			
			if($available == 0){
				$available = 1;
			}else
			{
				$available = 0;
			}

            if (! empty($menu_id)) {
                //update status available to 0/1
                if ($this->data['setting']['stock_menu_by_inventory'] != 1) {
                    $data = array('menu_quantity' => $quantity);
                } else {
                    $data = array('available' => $available);
                }   
				$result_update = $this->kitchen_model->update_status_available_by_id($menu_id, $data);
                if ($this->data['setting']['stock_menu_by_inventory'] != 1) {
                    $result_update = $quantity;
                }
                echo json_encode($result_update);
            }
        }
        else {
            echo json_encode('false');
        }
	}
	
	public function get_menu_by_catergory()
	{
		if (! $this->ion_auth->logged_in()) {
            redirect(base_url(), 'refresh');
        }
        else if ($this->groups_access->have_access('kitchen')) {
            $category_id = $this->input->post('category_id');
            $menus       = $this->categories_model->get_menu_by_category($category_id,'all');

            echo json_encode($menus);
        }
        else {
            redirect(base_url(), 'refresh');
        }
	}

    public function get_menu()
    {
        if ($this->input->is_ajax_request()) {
            $menu_id = $this->input->post('menu_id');
            $return_data = $this->kitchen_model->get_one('menu', $menu_id);
            echo json_encode($return_data);
        }
    }
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */