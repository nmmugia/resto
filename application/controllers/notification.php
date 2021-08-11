 <?php if (! defined('BASEPATH')) exit('No direct script access allowed');

 class Notification extends CI_Controller
 {
 	public function __construct()
 	{
 		parent::__construct();
 		$this->load->model('notification_model');
 	}

 	function delete_notif(){
 		$id  = $this->input->post('notif_id');
 		$this->notification_model->delete('notification', $id);
 	}

 	function update_notif(){
 		$id  = $this->input->post('notif_id');
 		$this->notification_model->seen($id);
 	}
 }