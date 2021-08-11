<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
* 
*/
class System_taxes extends Admin_Controller
{

	private $created_at;
    private $created_by;
	
	function __construct()
	{
		parent::__construct();
        $this->load->model('tax_model');

        $this->created_at = date("Y-m-d H:i:s");
        $this->created_by =  $this->data['user_profile_data']->id;
	}

	public function index()
	{
		$this->data['title']    = "Setting Tax & Services";
        $this->data['subtitle'] = "Setting Tax & Services";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');
        $this->data['tax_method'] = $this->data['setting']['tax_service_method'];

        $this->data['content'] .= $this->load->view('admin/setting_taxes', $this->data, true);

        $this->render('admin');
	}

	public function update()
	{
		$get_taxes = $this->tax_model->get('order_taxes')->result();
		$data = $this->input->post('taxes');

		$is_active = 1;
		
		foreach ($get_taxes as $key => $value) {
			if (!empty($data[$value->id])) {
				$is_active = $data[$value->id];
			} else {
				$is_active = 0;
			}

			$array = array(
				'is_active' => $is_active
			);

			$this->tax_model->save('order_taxes', $array, $value->id);
		}
		
		redirect(SITE_ADMIN . '/system_taxes', 'refresh');
	}
}