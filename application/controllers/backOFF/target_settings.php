<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Target_Settings extends Admin_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model("user_model");
    $this->load->model("target_model");
  }

  public function index()
  {
    $this->data['title']    = "Craft Productivity";
    $this->data['subtitle'] = "Pengaturan Target & Reward";
    $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
    $this->data['message_success'] = $this->session->flashdata('message_success');
    $this->data['add_url']  = base_url(SITE_ADMIN . '/target_settings/add');
    $this->data['data_url'] = base_url(SITE_ADMIN . '/target_settings/get_data');
    $this->data['outlets']=$this->store_model->get_all_where("outlet",array("store_id"=>$this->data['setting']['store_id']));
    $this->data['content'] .= $this->load->view('admin/craft/target-setting-list', $this->data, true);
    $this->render('admin');
  }
  public function get_data()
  {
    $this->load->library(array('datatables'));
    $this->load->helper(array('datatables'));
    $this->datatables->select('t.id,t.user_id,t.target_type,t.reward,u.name,t.target_by_total,t.is_percentage')
    ->from("target t")
    ->join("users u","t.user_id=u.id")
    ->unset_column("t.target_type")
    ->unset_column("t.reward")
    ->add_column("target_type","$1","check_target_type(target_type)")
    ->add_column("reward","$1",'check_reward(is_percentage,reward)','is_percentage,reward')
    ->add_column('actions', "<div class='btn-group'>
      <a href='" . base_url(SITE_ADMIN . '/target_settings/edit/$1') . "'  class='btn btn-default'><i class='fa fa-pencil'></i> Edit</a>
      <a href='" . base_url(SITE_ADMIN . '/target_settings/delete/$1') . "' class='btn btn-danger deleteNow' rel='pengaturan target'><i class='fa fa-trash-o'></i> Hapus</a>
    </div>", 'id');
    echo $this->datatables->generate();
  }
  public function add()
  {
    $this->data['title']    = "Craft Productivity";
    $this->data['subtitle'] = "Pengaturan Target & Reward";
    $this->form_validation->set_rules('user_id', 'Waiter', 'required');
    $this->form_validation->set_rules('reward', 'Reward', 'required');
    if ($this->form_validation->run() == true) {
      $data = $this->input->post();
      $save=$this->user_model->save("target",array(
        "user_id"=>$data['user_id'],
        "created_at"=>date("Y-m-d H:i:s"),
        "target_type"=>$data['target_type'],
        "target_by_total"=>$data['target_by_total'],
        "reward"=>$data['reward'],
        "is_percentage" => (isset($data['is_percentage']) ? 1 : 0)
      ));
      if ($save === false) {
        $this->session->set_flashdata('message', 'Gagal menyimpan data');
      }else {
        if(isset($data['detail']['menu_id'])){
          for($x=0;$x<sizeof($data['detail']['menu_id']);$x++){
            if($data['detail']['menu_id'][$x]!="" && $data['detail']['target_qty'][$x]>0){
              $this->user_model->save("target_detail",array(
                "target_id"=>$save,
                "menu_id"=>$data['detail']['menu_id'][$x],
                "target_qty"=>$data['detail']['target_qty'][$x]
              ));                      
            }
          }          
        }
        $this->session->set_flashdata('message_success', 'Berhasil menyimpan data');                  
      }         
      $btnaction = $this->input->post('btnAction');
      if ($btnaction == 'save_exit') {
        redirect(SITE_ADMIN . '/target_settings', 'refresh');
      }
      else {
        redirect(SITE_ADMIN . '/target_settings/add/', 'refresh');
      }
    }
    else {
      $this->data['users']=$this->user_model->get_online_by_group("'dinein'");
      $this->data['menu_lists']=$this->user_model->get_all_where("menu",array());
      $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
      $this->data['message_success'] = $this->session->flashdata('message_success');
      $this->data['content'] .= $this->load->view('admin/craft/target-setting-add', $this->data, true);
      $this->render('admin');
    }
  }
  public function edit()
  {
    $id = $this->uri->segment(4);
    if (empty($id))redirect(SITE_ADMIN . '/target_settings');
    $target = $this->target_model->get($id);
    if (empty($target))redirect(SITE_ADMIN . '/target_settings');
    $this->data['target'] = $target;
    $this->data['detail'] = $this->target_model->get_detail($id);
    $this->data['title']    = "Craft Productivity";
    $this->data['subtitle'] = "Pengaturan Target & Reward";
    $this->form_validation->set_rules('user_id', 'Nama Waiter', 'required');
    if (isset($_POST) && ! empty($_POST)) {
      if ($this->form_validation->run() === TRUE) {
        $data = $this->input->post();
        $save=$this->user_model->save("target",array(
          "user_id"=>$data['user_id'],
          "target_type"=>$data['target_type'],
          "target_by_total"=>$data['target_by_total'],
          "reward"=>$data['reward'],
          "is_percentage" => (isset($data['is_percentage']) ? 1 : 0)
        ),$id);
        if ($save === false) {
          $this->session->set_flashdata('message', 'Gagal menyimpan data');
        }else {
          $this->target_model->delete_by_limit('target_detail', array("target_id"=>$id),0);
          if(isset($data['detail']['menu_id'])){
            for($x=0;$x<sizeof($data['detail']['menu_id']);$x++){
              if($data['detail']['menu_id'][$x]!="" && $data['detail']['target_qty'][$x]>0){
                $this->user_model->save("target_detail",array(
                  "target_id"=>$id,
                  "menu_id"=>$data['detail']['menu_id'][$x],
                  "target_qty"=>$data['detail']['target_qty'][$x]
                ));                      
              }
            }          
          }
          $this->session->set_flashdata('message_success', 'Berhasil menyimpan data');                  
        }       
        $btnaction = $this->input->post('btnAction');
        if ($btnaction == 'save_exit') {
          redirect(SITE_ADMIN . '/target_settings', 'refresh');
        }else {
          redirect(SITE_ADMIN . '/target_settings/edit/' . $id, 'refresh');
        }
      }
    }
    $this->data['csrf'] = $this->_get_csrf_nonce();
    $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
    $this->data['message_success'] = $this->session->flashdata('message_success');
    $this->data['cancel_url']      = base_url(SITE_ADMIN . '/target_settings');        
    $this->data['users']=$this->user_model->get_online_by_group("'dinein'");
    $this->data['menu_lists']=$this->user_model->get_all_where("menu",array());
    $this->data['content'] .= $this->load->view('admin/craft/target-setting-edit', $this->data, true);
    $this->render('admin');
  }

  public function delete()
  {
    $id = $this->uri->segment(4);
    if (empty($id))redirect(SITE_ADMIN . '/target_settings');
    $target = $this->target_model->get($id);
    if (empty($target))redirect(SITE_ADMIN . '/target_settings');
    $result = $this->target_model->delete_by_limit('target', array("id"=>$id),0);
    $this->target_model->delete_by_limit('target_detail', array("target_id"=>$id),0);
    if ($result) {
      $this->session->set_flashdata('message_success', 'Berhasil menghapus data');
    }else {
      $this->session->set_flashdata('message', 'Error. Gagal menghapus data');
    }
    redirect(SITE_ADMIN . '/target_settings', 'refresh');
  }
  function reward_kitchen()
  {
    if($this->input->server('REQUEST_METHOD') == 'POST'){
      $data=$this->input->post();
      $reward_kitchen=$this->target_model->get_all_where("reward_kitchen",array());
      if(sizeof($reward_kitchen)==0){
        $this->target_model->save("reward_kitchen",array(
          "created_at" => date("Y-m-d H:i:s"),
          "reward"=>$data['reward'],
          "calculate_to_payroll"=>$data['calculate_to_payroll'],
          "outlet_id" => $data['outlet_id']
        ));        
      }else{
        $reward_kitchen=$reward_kitchen[0];
        $this->target_model->save("reward_kitchen",array(
          "created_at" => date("Y-m-d H:i:s"),
          "reward"=>$data['reward'],
          "calculate_to_payroll"=>$data['calculate_to_payroll'],
          "outlet_id" => $data['outlet_id']
        ),$reward_kitchen->id);
      }
    }else{
      redirect(base_url(SITE_ADIN."/target_settings"));
    }
    
  }
  function get_reward_kitchen()
  {
    $reward_kitchen=$this->target_model->get_all_where("reward_kitchen",array());
    echo json_encode(array(
      "data"=>(sizeof($reward_kitchen)>0 ? $reward_kitchen[0] : array())
    ));
  }
}