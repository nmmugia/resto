<?php if (! defined('BASEPATH')) exit('No direct script access allowed');


class Checker_Groups extends Admin_Controller
{
  public function index(){
    
    $this->data['title']    = "Set Grup Checker";
    $this->data['subtitle'] = "Set Grup Checker";
		$this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
		$this->data['message_success'] = $this->session->flashdata('message_success');
		if (isset($_POST) && !empty($_POST)) {
			$users=$this->input->post("users");
			$checker_number=$this->input->post("checker_number");
			for($x=0;$x<sizeof($users);$x++){
				$user_id=$users[$x];
				$this->store_model->delete_by_where("checker_group",array("user_id"=>$user_id));
				$this->store_model->save("checker_group",array(
					"user_id"=>$user_id,
					"checker_number"=>$checker_number,
				));
			}
			$this->session->set_flashdata('message_success', "Pengaturan grup checker berhasil disimpan.");
			redirect(base_url(SITE_ADMIN."/checker_groups"));
		}else{
			$this->data['groups']= array(1=>"A",2=>"B",3=>"C");
			$this->data['lists']=$this->store_model->get_checker_groups();
			$this->data['users']=$this->store_model->get_checker_users();
			$this->data['content'] .= $this->load->view('admin/checker-group', $this->data, true);
			$this->render('admin');
		}
  }
}