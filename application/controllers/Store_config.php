<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Store_config extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->library('ion_auth');
		$this->load->library('groups_access');
		$this->load->library('form_validation');
		$this->load->model('store_model');
		$general_setting= $this->store_model->get_general_setting();
		foreach ($general_setting as $key => $row) {
			$this->data['setting'][$row->name] = $row->value;
		}
		
        if ($this->ion_auth->logged_in()) {
			$this->load->model("feature_model");
			$feature_confirmation= $this->feature_model->get_feature_securities();
			foreach($feature_confirmation as $f){
				$this->data['feature_confirmation'][$f->key]=$f->users_unlock;
			}
			$this->data['data_open_close'] = $this->store_model->get_open_close();
		}

	}
	
	function get_data_store()
	{
		
		if ($this->ion_auth->logged_in()) 
		{
	 
			$user_data = $this->ion_auth->user()->row();
			
			$data_store = $this->store_model->get_store($user_data->store_id);
			
			return $data_store;
	 
		}
		else
		{
			return array();
		}
	}
	
	function get_data_outlet()
	{
		if ($this->ion_auth->logged_in()) 
		{
			// if($this->ion_auth->in_group(array('waiter')))
			// {
				$user_data = $this->ion_auth->user()->row();
				$data_outlet = $this->store_model->get_outlet($user_data->outlet_id);
				return $data_outlet;
			// }
		}
		else
		{
			return array();
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */