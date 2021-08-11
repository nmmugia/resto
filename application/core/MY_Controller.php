<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/15/2014
 * Time: 10:26 AM
 */
class MY_Controller extends CI_Controller
{
    public    $data              = Array();
    protected $controller_name;
    protected $action_name;
    protected $previous_controller_name;
    protected $previous_action_name;
    protected $save_previous_url = false;
    protected $page_title;
    

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->library('ion_auth');
        $this->load->library('form_validation');
        $this->load->model('store_model');
        $this->load->model('report_model');
        $this->load->library('groups_access');
        $this->load->helper('module');
        
		// check module expired/grace_period
		get_module_expired();
		get_module_grace_period();
		
        //save the previous controller and action name from session
        $this->previous_controller_name = $this->session->flashdata('previous_controller_name');
        $this->previous_action_name     = $this->session->flashdata('previous_action_name');

        //set the current controller and action name
        $this->controller_name = $this->router->fetch_directory() . $this->router->fetch_class();
        $this->action_name     = $this->router->fetch_method();

        $this->data['content'] = '';
        $this->data['css']     = '';

        if ($this->ion_auth->logged_in()) {
            $user                     = $this->ion_auth->user()->row();
            if(sizeof($user)==0){
              redirect('auth/logout');
            }
            $this->data['user_profile_data']  = $user;
            $this->data['user_groups_data']   = $this->ion_auth->get_users_groups($this->data['user_profile_data']->id)->result();
            $this->data['user_profile_admin'] = base_url(SITE_ADMIN . '/staff/edit/' . $this->data['user_profile_data']->id . '/' . $this->data['user_groups_data'][0]->id);

            if(isset($this->data['user_profile_data']->id)){
                $this->load->model('notification_model');
                $this->data['list_notif'] = $this->notification_model->get_notification($this->data['user_profile_data']->id);
                $this->data['list_notif_unseen'] = $this->notification_model->get_notification($this->data['user_profile_data']->id, TRUE);

            }
            $user_groups              = $this->ion_auth->get_users_groups($user->id)->result();
            $this->data['user_id']    = $user->id;
            $this->data['user_name']  = $user->name;
            $this->data['group_id']   = $user_groups[0]->id;
            $this->data['group_name'] = $user_groups[0]->name;

            $this->groups_access->group_id = $user_groups[0]->id;

            $this->data['sidemenu'] =$this->groups_access->build_menu($this->data['user_groups_data'][0]->id);

            $this->data['count_kontra_bon'] = $this->report_model->get_count_kontra_bon($this->data['user_profile_data']->store_id);
        }
        $this->load->model("feature_model");
        $feature_confirmation= $this->feature_model->get_feature_securities();
        foreach($feature_confirmation as $f){
          $this->data['feature_confirmation'][$f->key]=$f->users_unlock;
        }
        $general_setting= $this->store_model->get_general_setting();
        
        foreach ($general_setting as $key => $row) {
           $this->data['setting'][$row->name] = $row->value;
        }
        $modules= $this->store_model->get("module")->result();
        
        foreach ($modules as $key => $row) {
           $this->data['module'][$row->name] = $row->is_installed;
        }

        if($this->data['setting']["notification"]==0){
          $this->db->query("delete from notification;");
        }
    }



    protected function render($template = 'main')
    {
        //save the controller and action names in session
        if ($this->save_previous_url) {
            $this->session->set_flashdata('previous_controller_name', $this->previous_controller_name);
            $this->session->set_flashdata('previous_action_name', $this->previous_action_name);
        }
        else {
            $this->session->set_flashdata('previous_controller_name', $this->controller_name);
            $this->session->set_flashdata('previous_action_name', $this->action_name);
        }

        $view_path = $this->controller_name . '/' . $this->action_name . '.php'; //set the path off the view
        if (file_exists(APPPATH . 'views/' . $view_path)) {
            $this->data['content'] .= $this->load->view($view_path, $this->data, true);  //load the view
        }

        $this->load->view("layouts/$template.tpl.php", $this->data);  //load the template
    }

    public function generate_random_name($length = 30)
    {
        $d      = date("d");
        $m      = date("m");
        $y      = date("Y");
        $t      = time();
        $dmt    = $d + $m + $y + $t;
        $ran    = rand(0, 10000000);
        $dmtran = $dmt + $ran;
        $un     = uniqid();
        $mdun   = md5($dmtran . $un);
        $sort   = substr($mdun, 0, $length);

        return $sort;
    }

    function update_table_merge($array_table, $data_update)
    {
         if(!empty($array_table))
                {
                    foreach ($array_table as $key => $row) {
                         $this->store_model->update_status_table($row->id, $data_update);

                    }
                }
    }

}

class Table_Controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        //login check
        if (! $this->ion_auth->logged_in()) {
            //redirect them to the login page
            redirect('login', 'refresh');
        }

        if(!$this->groups_access->have_access('dinein'))
            redirect('auth/logout');

        $store_id = $this->data['setting']['store_id'];
        if(empty($store_id)){
            redirect(base_url(), 'refresh');
        }
        $this->load->model('cashier_model');

        $this->data['theme']      = 'floor-theme';
        $this->data['data_store'] = $this->store_model->get_one('store', $store_id);
        if(empty($this->data['data_store'])){
            redirect(base_url(), 'refresh');
        }
        $this->data['data_open_close'] = $this->store_model->get_open_close();
        
        if( $this->data['setting']['open_close_system'] == 2){
            $this->data['data_open_close_today'] = $this->store_model->get_open_close_today(date("Y-m-d")); 
        }else{
            $this->data['data_open_close_today'] = false; 
        }
        
        // $this->session->sess_expiration = 3;
    }
}
class Checker_Controller extends MY_Controller
{
  public function __construct()
  {
    parent::__construct();
    if (! $this->ion_auth->logged_in()) {
      redirect('login', 'refresh');
    }
    if(!$this->groups_access->have_access('checker'))redirect('auth/logout');
    $store_id = $this->data['setting']['store_id'];
    if(empty($store_id))redirect(base_url(), 'refresh');
    $this->load->model('cashier_model');
    $this->data['theme']      = 'floor-theme';
    $this->data['data_store'] = $this->store_model->get_one('store', $store_id);
    if(empty($this->data['data_store']))redirect(base_url(), 'refresh');
    $this->data['data_open_close'] = $this->store_model->get_open_close();       
  }
}
class Monitoring_Controller extends MY_Controller
{
  public function __construct()
  {
    parent::__construct();
    if (! $this->ion_auth->logged_in()) {
      redirect('login', 'refresh');
    }
    if(!$this->groups_access->have_access('reservation_monitor'))redirect('auth/logout');
    $store_id = $this->data['setting']['store_id'];
    if(empty($store_id))redirect(base_url(), 'refresh');
    $this->load->model('cashier_model');
    $this->data['theme']      = 'floor-theme';
    $this->data['data_store'] = $this->store_model->get_one('store', $store_id);
    if(empty($this->data['data_store']))redirect(base_url(), 'refresh');
    $this->data['data_open_close'] = $this->store_model->get_open_close();       
  }
}
class Cashier_Controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (! $this->ion_auth->logged_in()) {
            //redirect them to the login page
            redirect('login', 'refresh');
        }

        // if(!$this->groups_access->have_access('dinein') && !$this->groups_access->have_access('checkout')){
          if (! $this->ion_auth->logged_in()) {
            redirect('auth/logout');
          }
        $store_id = $this->data['setting']['store_id'];
        if(empty($store_id)){
            redirect(base_url(), 'refresh');
        }

        $this->load->model('cashier_model');
        $this->data['theme']      = 'floor-theme';
        $this->data['data_open_close'] = $this->store_model->get_open_close();
        $this->data['data_store'] = $this->cashier_model->get_one('store', $store_id);
        if( $this->data['setting']['open_close_system'] == 2){
            $this->data['data_open_close_today'] = $this->store_model->get_open_close_today(date("Y-m-d")); 
        }else{
            $this->data['data_open_close_today'] = false; 
        }
        if(empty($this->data['data_store'])){
            redirect(base_url(), 'refresh');
        }
        // $this->session->sess_expiration = 3;
    }
}

class Admin_Controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->lang->load('backoffice', 'indonesian');
        $this->lang->load('administrator', 'indonesian');
        $this->save_previous_url = TRUE;
        //login check
        //
        if (! $this->ion_auth->logged_in()) {
        // if(!$this->groups_access->have_access('admincms') && !$this->groups_access->have_access('hrd') !$this->groups_access->have_access('backoffice')){
            redirect('auth/logout');
        }
        if (! $this->ion_auth->logged_in()) {
            //redirect them to the login page
            redirect(SITE_ADMIN . '/login', 'refresh');
        }
        
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
        if ($this->input->post($this->session->flashdata('csrfkey')) !== FALSE && $this->input->post($this->session->flashdata('csrfkey')) == $this->session->flashdata('csrfvalue')
        ) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }


}


class Hrd_Controller extends MY_Controller
{
  public function __construct()
  {


    parent::__construct();
    if (! $this->ion_auth->logged_in()) {
      redirect('login', 'refresh');
    }else{
         $this->load->model('hrd_model');  
        $this->auto_check_employee_status();
       
    } 
    $this->lang->load('backoffice', 'indonesian');
    $this->lang->load('administrator', 'indonesian');  
    $this->data['theme']      = 'floor-theme'; 
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
        if ($this->input->post($this->session->flashdata('csrfkey')) !== FALSE && $this->input->post($this->session->flashdata('csrfkey')) == $this->session->flashdata('csrfvalue')
        ) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    function auto_check_employee_status(){
        $data_employee = $this->hrd_model->get_job_history_by_date(); 
        foreach ($data_employee as $key => $value) { 
            $cond = array(
                "hjh.employee_id" => $value->employee_id,
                "hjh.e_affair_id" => $value->next_job
            );
            $check_data = $this->hrd_model->check_job_history($cond);
            if(empty($check_data)){

                $data_update_last_jobs = array(
                    "end_date" => $value->start_naik_jabatan
                );
                $this->hrd_model->save("hr_jobs_history",$data_update_last_jobs,$value->id); 
                
                $data_insert_new_affair = array( 
                    "employee_id" => $value->employee_id,
                    "e_affair_id" => $value->next_job,  
                    "jobs_id" => $value->jobs_id,  
                    "reimburse" => $value->reimburse,
                    "vacation" => $value->vacation,  
                    "store_id" => $value->store_id,
                    "start_date" => $value->start_naik_jabatan,
                    "end_date" => $value->end_naik_jabatan

                );
                $this->hrd_model->save("hr_jobs_history",$data_insert_new_affair);
            } 
        }

    }
}