<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

// include_once(APPPATH.'hooks/Module.php');
class Login extends CI_Controller
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
	
    function __construct(){
        parent::__construct();

        $this->load->library('session');
        $this->load->library('groups_access');
        $this->load->library('ion_auth');
        $this->load->library('form_validation');
        $this->load->helper('module');
        
		// check module expired/grace_period
		get_module_expired();
		get_module_grace_period();
		
        if ($this->ion_auth->logged_in()) {
          $user                     = $this->ion_auth->user()->row();
          $user_groups              = $this->ion_auth->get_users_groups($user->id)->result();
          $this->groups_access->group_id = $user_groups[0]->id;
        }
    }

    public function index(){
		check_module_allowed();
		$type_login=$this->session->userdata("type_login");
        if (! $this->ion_auth->logged_in()) {
            $this->load->view('header_v');
            $this->load->view('login_v');
        }else if (($this->groups_access->have_access('admincms') || $this->groups_access->have_access('hrd') || $this->groups_access->have_access('backoffice')) && $type_login==2) {
            redirect(SITE_ADMIN, 'refresh');
        }else if ($this->groups_access->have_access('dinein') || $this->groups_access->have_access('checkout')) {
            redirect('table', 'refresh');
        }else if ($this->groups_access->have_access('kitchen')) {
            redirect('kitchen', 'refresh');
        }else if ($this->groups_access->have_access('checker')) {
            redirect('checker', 'refresh');
        }else if ($this->groups_access->have_access('reservation_monitor')) {
            redirect('monitoring', 'refresh');
        }else {
            if ($this->ion_auth->logged_in()) {
                redirect(base_url('auth/logout'), 'refresh');
            }
            else {
                redirect(base_url(), 'refresh');
            }
        }
    }
    public function check_feature_confirmation()
    {
      $this->load->model("user_model");
      $this->load->library('encrypt');
      $users_unlock=$this->input->post("users_unlock");
      $pin=$this->input->post("pin");
      $check=false;
      $user=array();
      foreach(explode(",",$users_unlock) as $u){
        $user_data = $this->user_model->get_all_where("users",array("id"=>$u));
        if(sizeof($user_data)>0){
          $user_data=$user_data[0];
          if($this->encrypt->decode($user_data->pin)==$pin){
            $check=true;
            $user=$user_data;
            break;            
          }
        }
      }
      echo json_encode(array(
        "status" => $check,
        "data"=>$user
      ));
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */