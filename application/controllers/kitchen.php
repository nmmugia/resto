<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Kitchen extends Store_config {

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
	function __construct()
    {
        parent::__construct();
        $this->load->model('kitchen_model');
        $this->load->model('order_model');
        $this->load->library('groups_access');
		
        $this->data['data_store']  = array();
        $this->data['data_outlet'] = array();
		    $this->data['user'] = array();
		
        $this->data['data_store']  = $this->get_data_store();
        $this->data['data_outlet'] = $this->get_data_outlet();


        if ($this->ion_auth->logged_in()) {
            $user                    = $this->ion_auth->user()->row();
            $user_groups             = $this->ion_auth->get_users_groups($user->id)->result();
            $this->data['user_profile_data']=$user;
            $this->data['user_id']   = $user->id;
            $this->data['user_name'] = $user->name;
            $this->data['group_id']  = $user_groups[0]->id;
            $this->data['group_name']  = $user_groups[0]->name;
			      $this->data['user'] = $user;
            $this->data['outlet_data']=$this->kitchen_model->get_one("outlet",$user->outlet_id);
            $this->groups_access->group_id = $user_groups[0]->id;
            $this->groups_access->have_access('kitchen');
        }
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
	public function index()
	{
		if (! $this->ion_auth->logged_in()) {
      $this->load->view('header_v');
      $this->load->view('login_v', $this->data);
    }
    else if ($this->groups_access->have_access('kitchen')) 
    {
			$this->load->view('header_v');
      if($this->data['setting']['theme']==1){
        $this->data['data_menu_order'] = $this->kitchen_model->get_order_menu_kitchen($this->data['user']->outlet_id);
        $this->data['list_detail_kitchen']=$this->load->view("list_detail_kitchen",$this->data,true);
        $this->load->view('kitchen_v',$this->data);
      }else{
        $slide_setting=$this->session->userdata("slide_setting");
        if($slide_setting=="")$slide_setting="slide-up";
        $this->data['slide_setting']=$slide_setting;
        $kitchen_setting=$this->session->userdata("kitchen_setting");
        if($kitchen_setting=="")$kitchen_setting=array();
        if(!isset($kitchen_setting['dinein']))$kitchen_setting['dinein']=0;
        if(!isset($kitchen_setting['takeaway_delivery']))$kitchen_setting['takeaway_delivery']=0;
        $offset=0;
        $perpage=8;
        $this->data['data_menu_order'] = $this->kitchen_model->get_order_menu_kitchen2($this->data['user']->outlet_id,$kitchen_setting,($this->data['setting']['dining_type'] == 3) ? true : false);
        $pagination = array(
          'base_url' => base_url("kitchen/get_data_left"),
          'total_rows' => (isset($this->data['data_menu_order'][0]) ? sizeof($this->data['data_menu_order'][0]) : 0),
          'per_page' => $perpage,
          'num_links' => 3,
          'uri_segment' => 3
        );
        $this->data['pagination']=$this->paging($pagination);
        $perpage2=4;
        $pagination2 = array(
          'base_url' => base_url("kitchen/get_data_right"),
          'total_rows' => (isset($this->data['data_menu_order'][1]) ? sizeof($this->data['data_menu_order'][1]) : 0),
          'per_page' => $perpage2,
          'num_links' => 3,
          'uri_segment' => 3
        );
        $this->data['pagination2']=$this->paging($pagination2);
        $this->data['offset']=$offset;
        $this->data['perpage']=$perpage;
        $this->data['perpage2']=$perpage2;
        $this->data['kitchen_setting']=$kitchen_setting;
        $this->data['kitchen_mode2_left']=$this->load->view("kitchen_mode2_left",$this->data,true);
        $this->data['kitchen_mode2_right']=$this->load->view("kitchen_mode2_right",$this->data,true);
        $this->data['list_detail_kitchen']=$this->load->view("list_detail_kitchen_mode_2",$this->data,true);
        $this->load->view('kitchen_v_mode_2',$this->data);
      }
		}
    else {
        redirect(base_url(), 'refresh');
    }
	}

  public function update_cooking_process() {
    if (!$this->ion_auth->logged_in()) {
      echo json_encode('false');
    } else if ($this->groups_access->have_access('kitchen')) {
      $order_menu_id = $this->input->post('order_menu_id');
      $order_package_menu_id = $this->input->post('order_package_menu_id');
      $table_id = $this->input->post('table_id');
      $process_status = $this->input->post('process');

      if (!empty($order_menu_id)) {
        $data_order = $this->order_model->get_one('order_menu', $order_menu_id);
        $quantity_process = ($process_status == 'up') ? $data_order->quantity_process + 1 : $data_order->quantity_process - 1;

        $update_data = array(
          'quantity_process' => $quantity_process
        );

        $update = $this->order_model->save('order_menu', $update_data, $order_menu_id);

        $data_order = $this->order_model->get_one('order_menu', $order_menu_id);

        echo json_encode($data_order);
      }
    } else {
      echo json_encode('false');
    }
  }
	
	public function update_cooking_status()
	{
		if (! $this->ion_auth->logged_in()) {
            echo json_encode('false');
        }
        else if ($this->groups_access->have_access('kitchen')) {
            $order_menu_id = $this->input->post('order_menu_id');
            $order_package_menu_id = $this->input->post('order_package_menu_id');
            $cooking_status = $this->input->post('cooking_status');
            $table_id = $this->input->post('table_id');

            if (! empty($order_menu_id)) {
                //update status cooking to canceled
                $process_status=1;
                if($cooking_status == 6)
                {
                    $process_status = 0;
                    $data_order = $this->order_model->get_one('order_menu', $order_menu_id);

                    $outlet_id = $this->order_model->get_outlet_id_by_order_id($data_order->order_id);
                    $outlet_id =  $outlet_id[0]->outlet_id;

                    $menu_ingredient = new stdclass();     
                    $menu_ingredient = $this->order_model->one_menu_ingredient_with_stock($data_order->menu_id);                
                    $this->order_model->increase_inventory_stock($menu_ingredient->ingredient, $data_order->quantity); 

                    $menu_outlet = $this->order_model->get_menu_outlet($outlet_id);                
                    if($this->data['setting']['zero_stock_order'] == 0){
                      $this->order_model->all_menu_ingredient_with_stock($menu_outlet);
                    }else{
                      foreach($menu_outlet as $m){
                        $m->total_available=0;
                      }
                    }

                }

				$data = array('cooking_status' => $cooking_status,
                            'process_status'   => $process_status);
                            if($cooking_status==1){
                              $data['is_check']=0;
                            }
				$result_update = $this->kitchen_model->update_status_cooking_by_id($order_menu_id, $data);
				if($order_package_menu_id!=0){
					$this->kitchen_model->save("order_package_menu",$data,$order_package_menu_id);					
				}
				$result = $this->kitchen_model->get_order_menu_by_id($order_menu_id);
                
                $order_data = $this->order_model->get_data_table($result[0]->order_id);
                if($table_id){
                    $arr_merge = $this->order_model->get_merge_table_byparent($table_id);
                    $result[0]->arr_merge_table= $arr_merge;
                    $table_data = $this->order_model->get_one('table', $table_id);
                    $msg= $order_data->floor_name.'-Meja '.$table_data->table_name. " : ".$result[0]->menu_name." done";
                }else{
                    $msg= "Take Away-<b>".$order_data->customer_name."</b> : ".$result[0]->menu_name." done";

                }
                

                if($cooking_status ==3){
                    $this->kitchen_model->update_status_cooking_by_id($order_menu_id,array('finished_at'=>date("Y-m-d H:i:s")));
                    $this->load->model('user_model');
                    
                    $all_user = $this->user_model->get_online_users_bygroup();
                    // $msg= "Menu ".$result[0]->menu_name." untuk meja ".$table_data->table_name." done";
                    $result[0]->notification = array();
                    if($this->data['setting']["notification"]==1){
                      foreach ($all_user as $key => $row) {
                        $data = array(
                          'from_user' => $this->data['user']->id,
                          'to_user'  => $row->id,
                          'message'  => $msg,
                          'seen'  =>  0,
                          'date'  => date("Y-m-d H:i:s")
                          );
                          $notif_id= $this->order_model->save('notification', $data);
                          $result[0]->notification[] =array('to_user'=> $row->id,
                                                      'notif_id' => $notif_id,
                                                      'msg' => $msg);
                      }
                    }
                }

                echo json_encode($result);
            }
        }
        else {
            echo json_encode('false');
        }
	}
	public function update_checklist()
  {
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
	public function get_menu_order_by_id()
	{
		if (! $this->ion_auth->logged_in()) {
            echo json_encode('false');
        }
        else if ($this->groups_access->have_access('kitchen')) {
            $order_id = $this->input->post('order_id');

            if (! empty($order_id)) {
				        $result = $this->kitchen_model->get_menu_order_by_id($order_id);
				
                echo json_encode($result);
            }
        }
        else {
            echo json_encode('false');
        }
	}
  function print_list_menu()
  {
    if ($this->input->is_ajax_request()) {
      $this->load->model("order_model");
      $this->load->helper(array('printer'));
      $order_id      = $this->input->post('order_id');
      $cooking_status      = $this->input->post('cooking_status');
      $order_menu_ids      = $this->input->post('order_menu_ids');
      
      //get printer kitchen
      $this->load->model("setting_printer_model");
      $printer_arr_obj = $this->setting_printer_model->get_printer_by_enum_printer_type(array("name_type"=>"printer_kitchen"));
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
              $data['order_lists']=$this->order_model->get_order_menu_by_order($order_id,explode(",",$cooking_status),$order_menu_ids);
              $data_waiter=array();
              foreach($data['order_lists'] as $o){
                $options=$this->order_model->get_option_by_order_menu($o->order_menu_id);
                $o->options=$options;
                $side_dish=$this->order_model->get_side_dish_by_order_menu($o->order_menu_id, $o->is_promo);
                $o->side_dishes=$side_dish;
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
  function get_data_left_right($offset=0,$offset2=0)
  {
    if($this->data['setting']['theme']==1){
      $this->data['data_menu_order'] = $this->kitchen_model->get_order_menu_kitchen($this->data['user']->outlet_id);
      $content=$this->load->view("list_detail_kitchen",$this->data,true);
      echo json_encode(array(
        "theme" => $this->data['setting']['theme'],
        "content" => $content,
      ));
    }else{
      $kitchen_setting=$this->session->userdata("kitchen_setting");
      if($kitchen_setting=="")$kitchen_setting=array();
      if(!isset($kitchen_setting['dinein']))$kitchen_setting['dinein']=0;
      if(!isset($kitchen_setting['takeaway_delivery']))$kitchen_setting['takeaway_delivery']=0;
      $perpage=8;
      $this->data['data_menu_order'] = $this->kitchen_model->get_order_menu_kitchen2($this->data['user']->outlet_id,$kitchen_setting);
      $pagination = array(
        'base_url' => base_url("kitchen/get_data_left"),
        'total_rows' => (isset($this->data['data_menu_order'][0]) ? sizeof($this->data['data_menu_order'][0]) : 0),
        'per_page' => $perpage,
        'num_links' => 3,
        'uri_segment' => 3
      );
      $this->data['pagination']=$this->paging($pagination);
      $this->data['offset']=$offset;
      $this->data['perpage']=$perpage;
      $content_left=$this->load->view("kitchen_mode2_left",$this->data,true);
      $perpage2=4;
      
      $pagination2 = array(
        'base_url' => base_url("kitchen/get_data_right"),
        'total_rows' => (isset($this->data['data_menu_order'][1]) ? sizeof($this->data['data_menu_order'][1]) : 0),
        'per_page' => $perpage2,
        'num_links' => 3,
        'uri_segment' => 3
      );
      $this->data['pagination2']=$this->paging($pagination2);
      $this->data['offset']=$offset2;
      $this->data['perpage2']=$perpage2;
      $content_right=$this->load->view("kitchen_mode2_right",$this->data,true);      
      echo json_encode(array(
        "theme" => $this->data['setting']['theme'],
        "content_left" => $content_left,
        "content_right" => $content_right,
      ));
    }
  }
  function get_data_left($offset=0)
  {
    $kitchen_setting=$this->session->userdata("kitchen_setting");
    if($kitchen_setting=="")$kitchen_setting=array();
    if(!isset($kitchen_setting['dinein']))$kitchen_setting['dinein']=0;
    if(!isset($kitchen_setting['takeaway_delivery']))$kitchen_setting['takeaway_delivery']=0;
    $perpage=8;
    $this->data['data_menu_order'] = $this->kitchen_model->get_order_menu_kitchen2($this->data['user']->outlet_id,$kitchen_setting,($this->data['setting']['dining_type'] == 3) ? true : false);
    $pagination = array(
      'base_url' => base_url("kitchen/get_data_left"),
      'total_rows' => (isset($this->data['data_menu_order'][0]) ? sizeof($this->data['data_menu_order'][0]) : 0),
      'per_page' => $perpage,
      'num_links' => 3,
      'uri_segment' => 3
    );
    $this->data['pagination']=$this->paging($pagination);
    $this->data['offset']=$offset;
    $this->data['perpage']=$perpage;
    $content=$this->load->view("kitchen_mode2_left",$this->data,true);
    echo json_encode(array(
      "content" => $content
    ));
  }
  function get_data_right($offset=0)
  {
    $kitchen_setting=$this->session->userdata("kitchen_setting");
    if($kitchen_setting=="")$kitchen_setting=array();
    if(!isset($kitchen_setting['dinein']))$kitchen_setting['dinein']=0;
    if(!isset($kitchen_setting['takeaway_delivery']))$kitchen_setting['takeaway_delivery']=0;
    $perpage2=4;
    $this->data['data_menu_order'] = $this->kitchen_model->get_order_menu_kitchen2($this->data['user']->outlet_id,$kitchen_setting,($this->data['setting']['dining_type'] == 3) ? true : false);
    $pagination2 = array(
      'base_url' => base_url("kitchen/get_data_right"),
      'total_rows' => (isset($this->data['data_menu_order'][1]) ? sizeof($this->data['data_menu_order'][1]) : 0),
      'per_page' => $perpage2,
      'num_links' => 3,
      'uri_segment' => 3
    );
    $this->data['pagination2']=$this->paging($pagination2);
    $this->data['offset']=$offset;
    $this->data['perpage2']=$perpage2;
    $content=$this->load->view("kitchen_mode2_right",$this->data,true);
    echo json_encode(array(
      "content" => $content
    ));
  }
  function set_choice_type()
  {
    $is_checked=$this->input->get("is_checked");
    $value=$this->input->get("value");
    $setting=$this->session->userdata("kitchen_setting");
    if($setting=="")$setting=array();
    $setting[$value]=($is_checked=='true' ? 1 : 0);
    $kitchen_setting=array(
      "dinein" => (isset($setting['dinein']) ? $setting['dinein'] : 0),
      "takeaway_delivery" => (isset($setting['takeaway_delivery']) ? $setting['takeaway_delivery'] : 0)
    );
    $this->session->set_userdata(array("kitchen_setting" => $kitchen_setting));
  }
  public function posts()
	{
    $post_to = $this->input->post('post_to');
    $order_menu_id = $this->input->post('order_menu_id');
    $order_package_menu_id = $this->input->post('order_package_menu_id');
    $cooking_status = $this->input->post('cooking_status');
    $table_id = $this->input->post('table_id');
    $this->load->model('user_model');
    $all_user = $this->user_model->get_online_users_bygroup();
    $return_data=array();
    if (! empty($order_menu_id)) {
      $count_all_ready = 0;
      foreach($order_menu_id as $key=>$o){
        $process_status=1;
        $data = array('cooking_status' => $cooking_status[$key],'process_status'   => $process_status,'is_check'=>0,'finished_at'=>date("Y-m-d H:i:s"),"post_to"=>$post_to);
				if(isset($order_package_menu_id[$key]) && $order_package_menu_id[$key]!=0){
					$this->kitchen_model->save("order_package_menu",array('cooking_status' => $cooking_status[$key],'process_status'   => $process_status,'is_check'=>0),$order_package_menu_id[$key]);					
					$check_all=$this->kitchen_model->get_all_where("order_package_menu",array("order_menu_id"=>$o,"cooking_status !="=>3,"cooking_status !="=>7));
					if(sizeof($check_all)==0){
						$in_checker=$this->kitchen_model->get_all_where("order_package_menu",array("order_menu_id"=>$o,"cooking_status !="=>7));
						if(sizeof($in_checker)==0){
							$this->kitchen_model->save("order_menu",array("cooking_status"=>3),$o);
						}else{
							$this->kitchen_model->save("order_menu",array("cooking_status"=>7),$o);							
						}
					}
				}else{
					$result_update = $this->kitchen_model->update_status_cooking_by_id($o, $data);
				}
        $result = $this->kitchen_model->get_order_menu_by_id($o);
        if(!empty($result)){
          if ($result[0]->cooking_status != 3) {
            $count_all_ready++;
          }
          $order_id=$result[0]->order_id;
          $order_data = $this->order_model->get_data_table($result[0]->order_id);
          if($table_id){
            $arr_merge = $this->order_model->get_merge_table_byparent($table_id);
            $result[0]->arr_merge_table = $arr_merge;
            $table_data = $this->order_model->get_one('table', $table_id);
            $number_guest = $table_data->customer_count;
            $table_name = $table_data->table_name;
            $table_shape = $table_data->table_shape;
            $msg= $order_data->floor_name.'-Meja '.$table_data->table_name. " : ".$result[0]->menu_name." done";
          }else{
            $msg= "Take Away-<b>".$order_data->customer_name."</b> : ".$result[0]->menu_name." done";
          }
          if($cooking_status[$key]==3){
            $result[0]->notification = array();
            if($this->data['setting']["notification"]==1){
              foreach ($all_user as $key => $row) {
                $data = array(
                  'from_user' => $this->data['user']->id,
                  'to_user'  => $row->id,
                  'message'  => $msg,
                  'seen'  =>  0,
                  'date'  => date("Y-m-d H:i:s")
                );
                $notif_id= $this->order_model->save('notification', $data);
                $result[0]->notification[] =array('to_user'=> $row->id,'notif_id' =>$notif_id,'msg' => $msg);
              }
            }
          }
          $return_data['notify_cooking_status'][]=$result[0];          
        }
      }

      if ($this->data['setting']['dining_type'] == 3) {
        if ($count_all_ready == 0 && $table_id != 0) {
          $table_status = 2;
          $this->order_model->save("table", array("table_status" => $table_status), $table_id);
          $status_name = $this->order_model->get_one('enum_table_status', $table_status)->status_name;
          $this->load->helper("order_helper");

          $return_data['number_guest']    = $number_guest;
          $return_data['table_status']    = $table_status;
          $return_data['table_name']      = $table_name;
          $return_data['table_id']        = $table_id;
          $return_data['order_id']        = $order_id;
          $return_data['status_name']     = $status_name;
          $return_data['arr_merge_table'] = $arr_merge;
          $return_data['arr_menu_outlet'] = FALSE;
          $return_data['status_class']    = @create_shape_table($table_shape, $status_name);
        }
      }

      echo json_encode($return_data);
      $this->load->helper("printer_helper");
      //get printer kitchen
      $this->load->model("setting_printer_model");
      $printer_arr_obj = $this->setting_printer_model->get_printer_by_enum_printer_type(array("name_type"=>"printer_kitchen"));
      
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
              $data['order_lists']=$this->order_model->get_order_menu_by_order($order_id,$cooking_status,$order_menu_id);
              $data_waiter=array();
              $checkers=false;
              foreach($data['order_lists'] as $o){
                if($o->process_checker==1)$checkers=true;
                $options=$this->order_model->get_option_by_order_menu($o->order_menu_id);
                $o->options=$options;
                $side_dish=$this->order_model->get_side_dish_by_order_menu($o->order_menu_id, $o->is_promo);
                $o->side_dishes=$side_dish;
                $data_waiter=$o;
              }
              if($checkers==true)
              {
                if ($value->printer_width == 'generic') {
                  @print_list_menu_generic($value->name_printer,$data, $data_waiter,$printer_setting);
                } else {
                  @print_list_menu($value->name_printer,$data, $data_waiter,$printer_setting);
                }                
              }
          }                        
      }
    }
  }
  function set_slide_setting()
  {
    $slide=$this->input->get("slide");
    $this->session->set_userdata(array("slide_setting" => $slide));
  }
  public function histories()
	{
    $this->load->view('header_v');
    $this->data['lists']=$this->kitchen_model->histories($this->data['user']->outlet_id);
    $this->load->view('kitchen_history_v',$this->data);
	}
  public function inventory()
  {
    $this->load->view('header_v');    
    $this->data['data_url'] = base_url('kitchen/get_history_inventory');

    $this->data['inventories']=$this->kitchen_model->get("inventory")->result();
    $this->load->view('inventory_history_v',$this->data);
  }

  public function get_history_inventory(){
		$this->load->library(array('datatables'));
		$this->load->helper(array('datatables'));  
		$this->datatables->select('
			i.name,concat(i.name,IF(u.code IS NOT NULL,CONCAT(" (", u.code, ")"),"")) as join_name,
			i.unit,u.code,
			sum(sh.quantity) as sisa_stok,
			sum(if(sh.status=7,-1*sh.quantity,0)) as total_spoiled,
			sum(if(sh.status=1,-1*sh.quantity,0)) as total_used
		',false)
		->from('stock_history sh')
		->join('uoms u','sh.uom_id=u.id')
		->join('inventory i','sh.inventory_id=i.id')
		->where('date(sh.created_at)<=current_date()')
		->where('sh.outlet_id',$this->data['user']->outlet_id)
		->group_by('sh.outlet_id,sh.inventory_id,sh.uom_id')
		->unset_column('total_spoiled')
		->add_column('total_spoiled','$1','convert_quantity(total_spoiled)')
		->unset_column('total_used')
		->add_column('total_used','$1','convert_quantity(total_used)')
		->unset_column('sisa_stok')
		->add_column('sisa_stok','$1','convert_quantity(sisa_stok)');
		echo $this->datatables->generate();
  }
 
  public function save_spoiled(){
    $this->load->model('stock_model');
    $inventory_id = $this->input->post('inventory_id');
    $uom_id = $this->input->post('uom_id');
    $outlet_id = $this->input->post('outlet_id');
    $store_id = $this->input->post('store_id');
    $quantity_spoiled = $this->input->post('quantity');
    $description = $this->input->post('description');
    
    $ret_data['data'] = array();
    $ret_data['status']  = false;
    $ret_data['message'] = "";

    $data_stocks = $this->stock_model->get_stock_by_inventory_id($inventory_id,$uom_id);
    $total_stok = 0;
    $save_spoiled  = false;
    if(!empty($data_stocks)){
      $total_qty=0;
      foreach($data_stocks as $d){
        $total_qty+=$d->quantity;
      }
      // $total_stok = $data_stocks[0]->quantity - $quantity_spoiled; 
      $total_stok = $total_qty - $quantity_spoiled; 
      if($total_stok >= 0){
        $remain=$quantity_spoiled;
        foreach($data_stocks as $d){
          $qty=0;
          $stok=$d->quantity;
          if($d->quantity>0 && $remain>0){
            if($d->quantity>=$remain){
              $qty=$remain;
              $remain=0;
            }else{
              $qty=$d->quantity;
              $remain-=$d->quantity;
            }
            $data_update_stock = array( "quantity" => ($d->quantity-$qty));
            $save_spoiled = $this->stock_model->update_stock_by_id($d->id, $data_update_stock);
            if($save_spoiled){
                $data_stock_history = array(
                  "store_id"=>$d->store_id,
                  "outlet_id"=>$d->outlet_id,
                  "quantity"=> -$quantity_spoiled,
                  "inventory_id"=>$inventory_id,
                  "uom_id"=>$uom_id,
                  "price"=> $d->price,
                  "description"=> $description,
                  "status"=>7
                );

              $this->stock_model->insert_stock_history($data_stock_history);   
            }
            if($remain<=0){
              break;
            }
          }
        }
      }else{ 
           $ret_data['message'] = "Sisa Stok Kurang Dari Nol";
      } 
    }else{
          $ret_data['message'] = "Tidak Ada Stok";
    } 
   
    if($save_spoiled){
        $ret_data['status']  = true;
        $ret_data['message'] = "success";
    } 
    echo json_encode($ret_data);
  
  }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */