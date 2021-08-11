<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Checker extends Checker_Controller {

  public $config_pagination=array(
    'full_tag_open' => '<ul class="pagination" style="margin: 0px;">',
    'full_tag_close' => '</ul>',
    'first_tag_open' => '<li>',
    'first_tag_close' => '</li>',
    'first_link' => 'First',
    'last_tag_open' => '<li>',
    'last_tag_close' => '</li>',
    'last_link' => 'Last',
    'next_tag_open' => '<li>',
    'next_tag_close' => '</li>',
    'next_link' => 'Next',
    'prev_tag_open' => '<li>',
    'prev_tag_close' => '</li>',
    'prev_link' => 'Prev',
    'cur_tag_open' => '<li class="active"><a href="javascript:void(0)">',
    'cur_tag_close' => '</a></li>',
    'num_tag_open' => '<li>',
    'num_tag_close' => '</li>'
  );
  public function __construct()
  {
    parent::__construct();
    $this->load->helper(array('order'));
    $this->load->model('order_model');
    $this->data['data_store']  = $this->store_model->get_store($this->data['user_profile_data']->store_id);
    $this->data['data_outlet'] = $this->store_model->get_outlet($this->data['user_profile_data']->outlet_id);
    $this->data['ip_address']=$this->get_client_ip();
  }
  function paging($param = array()) {
    $this->load->library("pagination");
    if(isset($param['first_url']))$this->config_pagination['first_url'] = $param['first_url'];
    if(isset($param['suffix']))$this->config_pagination['suffix'] = $param['suffix'];
    $this->config_pagination['base_url'] = $param['base_url'];
    $this->config_pagination['total_rows'] = $param['total_rows'];
    $this->config_pagination['per_page'] = $param['per_page'];
    $this->config_pagination['num_links'] = $param['num_links'];
    $this->config_pagination['uri_segment'] = $param['uri_segment'];
    $this->pagination->initialize($this->config_pagination);
    return $this->pagination->create_links();
  }
  function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
  }
  public function index(){
    $this->load->model("checker_model");
    $this->groups_access->check_feature_access('checker');
    $this->data['title']           = "Checker";
    $this->data['theme']           = 'floor-theme';
    $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
    $this->data['message_success'] = $this->session->flashdata('message_success');
    $this->load->view('header_v');
    if($this->data['setting']['theme']==1){
      $this->data['data_menu_order'] = $this->checker_model->get_order_menu_checker($this->data['user_profile_data']->outlet_id);
      $this->data['list_detail_view']= $this->load->view('checker/list_detail_view', $this->data, true);
    }else{
      $slide_setting=$this->session->userdata("slide_setting");
      if($slide_setting=="")$slide_setting="slide-up";
      $this->data['slide_setting']=$slide_setting;
      $checker_setting=$this->session->userdata("checker_setting");
      if($checker_setting=="")$checker_setting=array();
      if(!isset($checker_setting['dinein']))$checker_setting['dinein']=0;
      if(!isset($checker_setting['takeaway_delivery']))$checker_setting['takeaway_delivery']=0;
      $offset=0;
      $perpage=8;
			$post_to=0;
			if($this->data['setting']['checker_group']==1){
        $post_to=$this->get_client_ip();
			}
      $this->data['data_menu_order'] = $this->checker_model->get_order_menu_checker2($this->data['user_profile_data']->outlet_id,$checker_setting,$post_to);
      $pagination = array(
        'base_url' => base_url("checker/get_data_left"),
        'total_rows' => (isset($this->data['data_menu_order'][0]) ? sizeof($this->data['data_menu_order'][0]) : 0),
        'per_page' => $perpage,
        'num_links' => 3,
        'uri_segment' => 3
      );
      $this->data['pagination']=$this->paging($pagination);
      $perpage2=4;
      $pagination2 = array(
        'base_url' => base_url("checker/get_data_right"),
        'total_rows' => (isset($this->data['data_menu_order'][1]) ? sizeof($this->data['data_menu_order'][1]) : 0),
        'per_page' => $perpage2,
        'num_links' => 3,
        'uri_segment' => 3
      );
      $this->data['pagination2']=$this->paging($pagination2);
      $this->data['offset']=$offset;
      $this->data['perpage']=$perpage;
      $this->data['perpage2']=$perpage2;
      $this->data['checker_setting']=$checker_setting;
      $this->data['checker_left']=$this->load->view("checker/checker_left",$this->data,true);
      $this->data['checker_right']=$this->load->view("checker/checker_right",$this->data,true);
      $this->data['list_detail_view']= $this->load->view('checker/list_detail_view_mode2', $this->data, true);
    }
    $this->load->view('checker/index',$this->data);
  }
  public function get_data(){
    $this->load->model("checker_model");
    if($this->data['setting']['theme']==1){
      $this->data['data_menu_order'] = $this->checker_model->get_order_menu_checker($this->data['user_profile_data']->outlet_id);
      $this->load->view('checker/list_detail_view', $this->data);
    }else{
      $checker_setting=$this->session->userdata("checker_setting");
      if($checker_setting=="")$checker_setting=array();
      if(!isset($checker_setting['dinein']))$checker_setting['dinein']=0;
      if(!isset($checker_setting['takeaway_delivery']))$checker_setting['takeaway_delivery']=0;
      $offset=0;
      $perpage=8;
      $post_to=0;
			if($this->data['setting']['checker_group']==1){
        $post_to=$this->get_client_ip();
			}
      $this->data['data_menu_order'] = $this->checker_model->get_order_menu_checker2($this->data['user_profile_data']->outlet_id,$checker_setting,$post_to);
      $pagination = array(
        'base_url' => base_url("checker/get_data_left"),
        'total_rows' => (isset($this->data['data_menu_order'][0]) ? sizeof($this->data['data_menu_order'][0]) : 0),
        'per_page' => $perpage,
        'num_links' => 3,
        'uri_segment' => 3
      );
      $this->data['pagination']=$this->paging($pagination);
      $perpage2=4;
      $pagination2 = array(
        'base_url' => base_url("checker/get_data_right"),
        'total_rows' => (isset($this->data['data_menu_order'][1]) ? sizeof($this->data['data_menu_order'][1]) : 0),
        'per_page' => $perpage2,
        'num_links' => 3,
        'uri_segment' => 3
      );
      $this->data['pagination2']=$this->paging($pagination2);
      $this->data['offset']=$offset;
      $this->data['perpage']=$perpage;
      $this->data['perpage2']=$perpage2;
      $this->data['checker_setting']=$checker_setting;
      $this->data['checker_left']=$this->load->view("checker/checker_left",$this->data,true);
      $this->data['checker_right']=$this->load->view("checker/checker_right",$this->data,true);
      $this->load->view("checker/list_detail_view_mode2",$this->data);
    }
  }
  function get_data_left($offset=0)
  {
    $this->load->model("checker_model");
    $checker_setting=$this->session->userdata("checker_setting");
    if($checker_setting=="")$checker_setting=array();
    if(!isset($checker_setting['dinein']))$checker_setting['dinein']=0;
    if(!isset($checker_setting['takeaway_delivery']))$checker_setting['takeaway_delivery']=0;
    $perpage=8;
    $post_to=0;
		if($this->data['setting']['checker_group']==1){
			$post_to=$this->get_client_ip();
		}
    $this->data['data_menu_order'] = $this->checker_model->get_order_menu_checker2($this->data['user_profile_data']->outlet_id,$checker_setting,$post_to);
    $pagination = array(
      'base_url' => base_url("checker/get_data_left"),
      'total_rows' => (isset($this->data['data_menu_order'][0]) ? sizeof($this->data['data_menu_order'][0]) : 0),
      'per_page' => $perpage,
      'num_links' => 3,
      'uri_segment' => 3
    );
    $this->data['pagination']=$this->paging($pagination);
    $this->data['offset']=$offset;
    $this->data['perpage']=$perpage;
    $content=$this->load->view("checker/checker_left",$this->data,true);
    echo json_encode(array(
      "content" => $content
    ));
  }
  function get_data_right($offset=0)
  {
		$this->load->model("checker_model");
    $checker_setting=$this->session->userdata("checker_setting");
    if($checker_setting=="")$checker_setting=array();
    if(!isset($checker_setting['dinein']))$checker_setting['dinein']=0;
    if(!isset($checker_setting['takeaway_delivery']))$checker_setting['takeaway_delivery']=0;
    $perpage2=4;
    $post_to=0;
		if($this->data['setting']['checker_group']==1){
			$post_to=$this->get_client_ip();
		}
    $this->data['data_menu_order'] = $this->checker_model->get_order_menu_checker2($this->data['user_profile_data']->outlet_id,$checker_setting,$post_to);
    $pagination2 = array(
      'base_url' => base_url("checker/get_data_right"),
      'total_rows' => (isset($this->data['data_menu_order'][1]) ? sizeof($this->data['data_menu_order'][1]) : 0),
      'per_page' => $perpage2,
      'num_links' => 3,
      'uri_segment' => 3
    );
    $this->data['pagination2']=$this->paging($pagination2);
    $this->data['offset']=$offset;
    $this->data['perpage2']=$perpage2;
    $content=$this->load->view("checker/checker_right",$this->data,true);
    echo json_encode(array(
      "content" => $content
    ));
  }
  public function posts()
	{
    $this->load->model("kitchen_model");
    $order_menu_id = $this->input->post('order_menu_id');
    $cooking_status = $this->input->post('cooking_status');
    $table_id = $this->input->post('table_id');
    $order_id = $this->input->post('order_id');
    $order_package_menu_id = $this->input->post('order_package_menu_id');
    $return_data=array();
    $data_print=array();
    $data_print['setting']=$this->data['setting'];
    $data_print['data_store']= $this->data['data_store'];
    $data_print['store_name']= $this->data['data_store'][0]->store_name;
    $data_print['order']=$this->order_model->get_by_order_id($order_id);
    $data_print['order_lists']=$this->order_model->get_order_menu_by_order($order_id,array(7),$order_menu_id);
    if (! empty($order_menu_id)) {
      //update status cooking to canceled
      foreach($order_menu_id as $key=>$o){
        $process_status=1;
        $data_order = $this->order_model->get_one('order_menu', $o);
        $order=$this->order_model->get_one('order', $data_order->order_id);

        $data = array('cooking_status' => $cooking_status,'process_status'   => $process_status);
        $result_update = $this->kitchen_model->update_status_cooking_by_id($o, $data);
				if(isset($order_package_menu_id[$key]) && $order_package_menu_id[$key]!=0){
					$this->kitchen_model->save("order_package_menu",$data,$order_package_menu_id[$key]);					
				}
        $result = $this->kitchen_model->get_order_menu_by_id($o);
        
        $order_data = $this->order_model->get_data_table($result[0]->order_id);
        
        if($table_id!=0){
          $arr_merge = $this->order_model->get_merge_table_byparent($table_id);
          $result[0]->arr_merge_table= $arr_merge;
          $table_data = $this->order_model->get_one('table', $table_id);
          if($data_order->dinein_takeaway==1){
            $msg= "Takeaway ,Lantai ".$order_data->floor_name.'-Meja '.$table_data->table_name. " : ".$result[0]->menu_name." done";
          }else{
            $msg= "Lantai ".$order_data->floor_name.'-Meja '.$table_data->table_name. " : ".$result[0]->menu_name." done";
          }
        }else{
          if($order->is_take_away==1){
            $msg= "Takeaway ".$order_data->customer_name."</b> : ".$result[0]->menu_name." done";
          }elseif($order->is_delivery==1){
            $msg= "Delivery Order ".$order_data->customer_name."</b> : ".$result[0]->menu_name." done";
          }
        }
        if($cooking_status ==3){
          $this->load->model('user_model');
          if($this->data['setting']["notification"]==1){
            $data = array(
              'from_user' => $this->data['user_profile_data']->id,
              'to_user'  => $order->created_by,
              'message'  => $msg,
              'seen'  =>  0,
              'date'  => date("Y-m-d H:i:s")
              );
            $notif_id= $this->order_model->save('notification', $data);
            $result[0]->notification[] =array(
              'to_user'=> $order->created_by,
              'notif_id' => $notif_id,
              'msg' => $msg
            );            
          }
        }
        $return_data[]=$result[0];
      }
      echo json_encode($return_data);
      $this->load->helper(array('printer'));
      //get printer checker kitchen
      $this->load->model("setting_printer_model");
      $printer_arr_obj = $this->setting_printer_model->get_printer_by_enum_printer_type(array("name_type"=>"printer_checker_kitchen"));
      $outlet_id=$this->data['data_outlet'][0]->id;
      foreach ($printer_arr_obj as $value) {
          //check outlet same with outlet id printer
          if ($outlet_id == $value->outlet_id) {
              //build object printer setting for printer helper
              $printer_setting = new stdClass();
              $printer_setting->id = $value->id;
              $printer_setting->name = $value->alias_name;
              $order_payment['outlet_data'] = $value->alias_name;
              $printer_setting->value = $value->name_printer;
              $printer_setting->default = $value->logo;
              $printer_setting->description = $value->printer_width;

              $data_waiter=array();
              foreach($data_print['order_lists'] as $o){
                $options=$this->order_model->get_option_by_order_menu($o->order_menu_id);
                $o->options=$options;
                $side_dish=$this->order_model->get_side_dish_by_order_menu($o->order_menu_id, $o->is_promo);
                $o->side_dishes=$side_dish;
                $data_waiter=$o;
              }
              
              if ($value->printer_width == 'generic') {
                @print_list_menu_generic($value->name_printer,$data_print, $data_waiter, $printer_setting);
              } else {
                @print_list_menu($value->name_printer,$data_print, $data_waiter, $printer_setting);
              }
          }                        
      }
    }
	}
  function call_waiter()
  {
    if($this->input->server('REQUEST_METHOD') == 'POST'){
      $this->load->model("notification_model");
      $user_id=$this->input->post("user_id");
      $user=$this->notification_model->get_one("users",$user_id);
      $message="Anda dipanggil oleh ".$this->data['user_profile_data']->name." (Checker) ";
      $notification = array();
      $data = array(
        'from_user' => $this->data['user_profile_data']->id,
        'to_user'  => $user_id,
        'message'  => $message,
        'seen'  =>  0,
        'date'  => date("Y-m-d H:i:s")
        );
      $notif_id= $this->notification_model->save('notification', $data);
      $notification[]=array(
        'to_user'=> $user_id,
        'notif_id' => $notif_id,
        'msg' => $message
      );
      echo json_encode(array(
        "notification"=>$notification
      ));
    }else{
      $this->load->model('user_model');
      $this->data['waiter_lists']=$this->user_model->get_online_by_group("'dinein'");
      $content=$this->load->view("checker/call_waiter",$this->data,true);
      echo json_encode(array(
        "waiter_online"=>sizeof($this->data['waiter_lists']),
        "content" => $content,
      ));
    }
  }
  function print_list_menu()
  {
    if ($this->input->is_ajax_request()) {
      $this->load->helper(array('printer'));
      $order_id      = $this->input->post('order_id');
      $cooking_status      = $this->input->post('cooking_status');
      $order_menu_ids      = $this->input->post('order_menu_ids');
      
      //get printer checker kitchen      
      $this->load->model("setting_printer_model");
      $printer_arr_obj = $this->setting_printer_model->get_printer_by_enum_printer_type(array("name_type"=>"printer_checker_kitchen"));
      $outlet_id=$this->data['data_outlet'][0]->id;
      foreach ($printer_arr_obj as $value) {
          //check outlet same with outlet id printer
          if ($outlet_id == $value->outlet_id) {
              //build object printer setting for printer helper
              $printer_setting = new stdClass();
              $printer_setting->id = $value->id;
              $printer_setting->name = $value->alias_name;
              $printer_setting->value = $value->name_printer;
              $printer_setting->default = $value->logo;
              $printer_setting->description = $value->printer_width;

              $data=array();
              $data['setting']=$this->data['setting'];
              $data['data_store']= $this->data['data_store'];
              $data['store_name']= $this->data['data_store'][0]->store_name;
              $data['order']=$this->order_model->get_by_order_id($order_id);
              $data['order_lists']=$this->order_model->get_order_menu_by_order($order_id,array($cooking_status),$order_menu_ids);
              $data_waiter=array();
              $outlet_lists=array();
              foreach($data['order_lists'] as $o){
                $options=$this->order_model->get_option_by_order_menu($o->order_menu_id);
                $o->options=$options;
                $side_dish=$this->order_model->get_side_dish_by_order_menu($o->order_menu_id, $o->is_promo);
                $o->side_dishes=$side_dish;
                $outlet_lists=$this->order_model->get_outlet_by_menu_id($o->menu_id);
                $data_waiter=$o;
              }

              if ($value->printer_width == 'generic') {
                @print_list_menu_generic($value->name_printer,$data, $data_waiter,$printer_setting);
              } else {
                @print_list_menu($value->name_printer,$data, $data_waiter,$printer_setting);
              }              
          }                        
      }
    }
  }

	function print_number()
  {
    if ($this->input->is_ajax_request()) {
      $this->load->helper(array('printer'));
      $string_number      = $this->input->get('string_number');
      //get printer checker kitchen
      $this->load->model("setting_printer_model");
      $printer_arr_obj = $this->setting_printer_model->get_printer_by_enum_printer_type(array("name_type"=>"printer_checker_kitchen"));
      $outlet_id=$this->data['data_outlet'][0]->id;
      foreach ($printer_arr_obj as $value) {
          //check outlet same with outlet id printer
          if ($outlet_id == $value->outlet_id) {
              //build object printer setting for printer helper
              $printer_setting = new stdClass();
              $printer_setting->id = $value->id;
              $printer_setting->name = $value->alias_name;
              $printer_setting->value = $value->name_printer;
              $printer_setting->default = $value->logo;
              $printer_setting->description = $value->printer_width;

              @print_number($value->name_printer,$string_number,$printer_setting);   
          }                        
      }
    }
  }
  function set_choice_type()
  {
    $is_checked=$this->input->get("is_checked");
    $value=$this->input->get("value");
    $setting=$this->session->userdata("checker_setting");
    if($setting=="")$setting=array();
    $setting[$value]=($is_checked=='true' ? 1 : 0);
    $checker_setting=array(
      "dinein" => (isset($setting['dinein']) ? $setting['dinein'] : 0),
      "takeaway_delivery" => (isset($setting['takeaway_delivery']) ? $setting['takeaway_delivery'] : 0)
    );
    $this->session->set_userdata(array("checker_setting" => $checker_setting));
  }
  function set_slide_setting()
  {
    $slide=$this->input->get("slide");
    $this->session->set_userdata(array("slide_setting" => $slide));
  }
  public function update_checklist()
  {
    $this->load->model("kitchen_model");
    $order_package_menu_id = $this->input->post('order_package_menu_id');
    $order_menu_id = $this->input->post('order_menu_id');
    $is_check = $this->input->post('is_check');
		if($order_package_menu_id==0){
			$this->kitchen_model->update_status_cooking_by_id($order_menu_id, array("is_check"=>$is_check));
		}else{
			$this->kitchen_model->update_where("order_package_menu",array("is_check"=>$is_check),array(
				"order_menu_id"=>$order_menu_id,
				"id"=>$order_package_menu_id,
			));
		}
  }
}
