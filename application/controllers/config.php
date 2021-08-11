<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Config extends CI_Controller
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
		$this->load->library('form_validation');
		$this->load->library('session');
        $this->load->library('ion_auth');

    }

    public function index()
    {
		if($this->config->item('environment') == "development"){
			$this->load->model("store_model");
			 if (isset($_POST) && ! empty($_POST)) {
				 $this->store_model->update_where("module",array("is_installed"=>0));
				foreach ($this->input->post() as $key => $row) {  
					$this->store_model->update_where(
								"module",
								array("is_installed"=>1), 
								array("name"=>$key)
						);
					//$save = $this->store_model->save_setting($key, $row);

				}   
			 }
			
			$data['modules'] = $this->store_model->get("module")->result();
			$this->load->view('config_v',$data);
		}else{
			redirect("auth/login");
		}
		
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */