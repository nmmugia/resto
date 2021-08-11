<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->library('ion_auth');
		$this->load->library('form_validation');
		$this->load->library('groups_access');
		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->database();		
		
		//$this->data['data_store'] = $this->get_data_store();
	}

	//redirect if needed, otherwise display the user list
	function index()
	{
		$this->session->set_flashdata('message', '');
		redirect('auth/login', 'refresh');
	}
	
	//log the user in
	function login()
	{
		$this->load->library('encrypt');
		$this->data['title'] = "Login";

		//validate form input
		$this->form_validation->set_rules('identity', 'Identity', 'required');
		// $this->form_validation->set_rules('password', 'Password', 'required');
		if ($this->form_validation->run() == true)
		{
			
			//check to see if the user is logging in
			//check for "remember me"
			$remember = false;
			$identity = $this->input->post('identity');
			// $password = 'password';
			$password = NULL;
			
			
			if ($this->ion_auth->login($identity, $password, $remember))
			{
		        $user=$this->ion_auth->user()->row();
		        $group=$this->ion_auth->get_users_groups($user->id)->row();
				//if the login is successful
				//redirect them back to the home page
				if ($this->groups_access->have_access('dinein',$group->id) || $this->groups_access->have_access('checkout',$group->id))
				{
					$this->session->set_flashdata('message', $this->ion_auth->messages());
					redirect('table', 'refresh');
				}
				else if ($this->groups_access->have_access('kitchen',$group->id))
				{
					$this->session->set_flashdata('message', $this->ion_auth->messages());
					redirect('kitchen', 'refresh');
				}
    			else if ($this->groups_access->have_access('checker',$group->id))
				{
					$this->session->set_flashdata('message', $this->ion_auth->messages());
					redirect('checker', 'refresh');
        		}
        		else if ($this->groups_access->have_access('reservation_monitor',$group->id))
				{
					$this->session->set_flashdata('message', $this->ion_auth->messages());
					redirect('monitoring', 'refresh');
				}else{
          			$this->session->set_flashdata('message', $this->ion_auth->messages());
					redirect(base_url(), 'refresh');
        		}
			}
			else
			{
				//if the login was un-successful
				//redirect them back to the login page
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect('auth/login', 'refresh'); //use redirects instead of loading views for compatibility with MY_Controller libraries
			}
		}
		else
		{
			//the user is not logging in so display the login page
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			$this->session->set_flashdata('message', $this->data['message']);
            redirect(base_url() . 'login', 'refresh');
		}
	}

	//log the user out
	function logout()
	{
		$this->data['title'] = "Logout";

		//log the user out
		$id = $this->session->userdata('user_id');
		if(isset($id) && !empty($id)){
        	$this->db->update('users', array('is_login' =>0), array('id' => $id));

		}
		$logout = $this->ion_auth->logout();

		//redirect them to the login page
		$this->session->set_flashdata('message', $this->ion_auth->messages());
		redirect('auth/login', 'refresh');
	}
	
	function _get_csrf_nonce()
	{
		$this->load->helper('string');
		$key   = random_string('alnum', 8);
		$value = random_string('alnum', 20);
		$this->session->set_flashdata('csrfkey', $key);
		$this->session->set_flashdata('csrfvalue', $value);

		return array($key => $value);
	}
	
	function _valid_csrf_nonce()
	{
		if ($this->input->post($this->session->flashdata('csrfkey')) !== FALSE &&
			$this->input->post($this->session->flashdata('csrfkey')) == $this->session->flashdata('csrfvalue'))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	function _render_page($view, $data=null, $render=false)
	{

		$this->viewdata = (empty($data)) ? $this->data: $data;

		$view_html = $this->load->view($view, $this->viewdata, $render);

		if (!$render) return $view_html;
	}
}
