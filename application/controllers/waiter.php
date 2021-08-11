<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Waiter extends Store_config {

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
	
	function __construct(){
		parent::__construct();
    if ($this->ion_auth->logged_in()) {
      $user                    = $this->ion_auth->user()->row();
      $user_groups             = $this->ion_auth->get_users_groups($user->id)->result();
      $this->groups_access->group_id = $user_groups[0]->id;
  }
		$this->data['data_store'] = $this->data_store;
	}
	
	public function index()
	{
		if (! $this->ion_auth->logged_in()) {
            //redirect them to the login page
            redirect(base_url() . 'login', 'refresh');
        }
        elseif (! $this->groups_access->have_access('dinein')) {
            //redirect them to the home page because they must be an administrator to view this
            redirect(base_url(), 'refresh');
        }
        else {
            //load content			
			$this->load->view('header_v');
			$this->load->view('table_v',$this->data);
        }
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */