<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/15/2014
 * Time: 10:24 AM
 */
// include_once(APPPATH.'hooks/Module.php');
class Admin extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
      if (!$this->ion_auth->logged_in()) {
        //redirect them to the login page
        redirect(SITE_ADMIN . '/login', 'refresh');
     }else {
        if($this->data['module']['TOP']==1){
          $this->load->model("report_model","report");
          $lists=array(
            "sales_by_waiter"=>array(),
            "sales_by_department"=>array(),
            "sales_by_day"=>array(),
            "summary"=>array()
          );
          $search=array(
            "store_id"=>$this->data['setting']['store_id'],
            "from"=>date("Y-m-01"),
            "to"=>date("Y-m-d"),
            "type"=>"department",
            "date"=>date("Y-m"),
            "offset"=>0,
            "perpage"=>5
          );
          $lists['sales_by_waiter']=$this->report->sales_by_waiter_report($search);
          $lists['sales_by_department']=$this->report->sales_by_department_category_report($search);
          $lists['sales_by_day']=$this->report->sales_by_day_report($search);
          $lists['customer_by_day']=$this->report->customer_by_day_report($search);
          $lists['summary']=$this->report->summary();
          $this->data['search']=$search;
          $this->data['lists']=$lists;
        }
        $this->data['title']    = "Dashboard";
        $this->data['subtitle'] = "Dashboard";
        $this->load->model('categories_model');
        $this->data['total_category'] = $this->categories_model->total_rows('category');
        $this->data['total_menu']     = $this->categories_model->total_rows('menu');
        $this->data['total_store']    = $this->categories_model->total_rows('store');
        $this->data['total_outlet']   = $this->categories_model->total_rows('outlet');            
        $this->data['store'] = $this->categories_model->get('store')->row();
        //load content
        $this->data['content'] .= $this->load->view('admin/dashboard', $this->data, true);
        $this->render('admin');
      }
    }

    public function login()
    {
		check_module_allowed();
        $this->data['title']    = "Login";
        $this->data['subtitle'] = "Login";

        //validate form input
        $this->form_validation->set_rules('identity', 'Identity', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == true) {
            $remember = true;
            $is_admin = true;

            if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember, false, $is_admin)) {
                $this->session->set_flashdata('messages', $this->ion_auth->messages());
                redirect(SITE_ADMIN, 'refresh');
            }
            else {
                $this->session->set_flashdata('message_error', $this->ion_auth->errors());               
                redirect(SITE_ADMIN . '/login');
            }
        }
        else {
            $this->data['message_error'] = "";
            $this->data['message_success'] = "";
            if(validation_errors()){
                 $this->data['message_error'] = validation_errors() ;
            }elseif ($this->session->flashdata('message_success')) {
                 $this->data['message_success'] =$this->session->flashdata('message_success');
            }else{
                 $this->data['message_error'] =$this->session->flashdata('message_error');
            }

            $this->data['identity'] = array('name'        => 'identity',
                                            'placeholder' => "Username",
                                            'id'          => 'identity',
                                            'type'        => 'text',
                                            'value'       => $this->form_validation->set_value('identity'),
                                            'class'       => 'form-control');
            $this->data['password'] = array('name'        => 'password',
                                            'placeholder' => "Password",
                                            'id'          => 'password',
                                            'type'        => 'password',
                                            'class'       => 'form-control');

            $this->render('admin-login');
        }
    }

    public function logout()
    {
        $this->data['title'] = "Logout";

        //log the user out
        $logout = $this->ion_auth->logout();

        //redirect them to the login page
        $this->session->set_flashdata('message_success', $this->ion_auth->messages());
        redirect(SITE_ADMIN . '/login', 'refresh');
    }
		public function update_store_id()
		{
			$store_id=$this->input->post("store_id");
			//update ID store in master general setting after download 
			$this->categories_model->save_by('master_general_setting', array('value' => $store_id), "store_id", 'name');			
		}
    public function save_sync()
    {
        if ($this->input->is_ajax_request()) {			
            $user     = $this->input->post('user');
            $groups     = $this->input->post('groups');
            $outlet   = $this->input->post('outlet');
            $floor    = $this->input->post('floor');
            $table    = $this->input->post('table');
            $category = $this->input->post('category');
            $menu     = $this->input->post('menu');
            $side_dish     = $this->input->post('side_dish');
            $side_dish_ingredient     = $this->input->post('side_dish_ingredient');
            $store    = $this->input->post('store');
            $taxes    = $this->input->post('tax');
            $inventory = $this->input->post('inventory');
            $menu_ingredient = $this->input->post('menu_ingredient');
            $compliment =  $this->input->post('compliment');
            $member_category =  $this->input->post('member_category');
            $member =  $this->input->post('member');
            $promo_cc =  $this->input->post('promo_cc');
            $promo_discount =  $this->input->post('promo_discount');
            $promo_cc_category =  $this->input->post('promo_cc_category');
            $promo_cc_menu = $this->input->post('promo_cc_menu');
            $promo_discount_category =  $this->input->post('promo_discount_category');
            $promo_discount_menu =  $this->input->post('promo_discount_menu');
            $voucher_group =  $this->input->post('voucher_group');
            $voucher_availability =  $this->input->post('voucher_availability');
            $voucher =  $this->input->post('voucher');
            $promo_schedule = $this->input->post('promo_schedule');
            $supplier = $this->input->post('supplier');
            $account = $this->input->post('account');
            $feature = $this->input->post('feature');
            $feature_access = $this->input->post('feature_access');
            $order_company = $this->input->post('order_company');
            $delivery_cost = $this->input->post('delivery_cost');
            $bank_account = $this->input->post('bank_account');
            $uom = $this->input->post('uom');
            $inventory_uom = $this->input->post('inventory_uom');
            $enum_card_type = $this->input->post('enum_card_type');
            $bank_account_card = $this->input->post('bank_account_card');
            $inventory_composition = $this->input->post('inventory_composition');
            $job = $this->input->post('job');
            $employee_affair = $this->input->post('employee_affair');
            $salary_component = $this->input->post('salary_component');
            $job_component = $this->input->post('job_component');
            $appraisal_template = $this->input->post('appraisal_template');
            $audit_template = $this->input->post('audit_template');
            $transaction_configuration = $this->input->post('transaction_configuration');
            $inventory_convertion = $this->input->post('inventory_convertion');
            $inventory_category = $this->input->post('inventory_category'); 
            $inventory_account = $this->input->post('inventory_account');
            $enum_coa_type = $this->input->post('enum_coa_type');
            $general_expenses = $this->input->post('general_expenses');
            $modules_due_date = $this->input->post('modules_due_date');
            $delivery_company = $this->input->post('delivery_company');
            $delivery_courier = $this->input->post('delivery_courier');
            
            $userArr       = array();
            $userGroupArr  = array();
            $outletArr     = array();
            $floorArr      = array();
            $tableArr      = array();
            $categoryArr   = array();
            $menuArr       = array();
            $menuOptArr    = array();
            $menuOptValArr = array();
            $menuSideArr   = array();
            $storeArr      = array();
            $taxesArr      = array();
            $inventoryArr      = array();
            $menuIngredientArr      = array();
            $complimentArr      = array();
            $compliment_store_array = array();
            $menu_promo_array = array();
            $menu_promo_side_dish_array = array();
            $groupsArray = array();
            $jobArr = array();
            $employeeAffairArr = array();
            $salaryComponentArr = array();
            $jobComponentArr = array();
            $appraisalTemplateArr = array();
            $appraisalCategoryArr = array();
            $appraisalDetailCategoryArr = array();
            $auditTemplateArr = array();
            $auditCategoryArr = array();
            $auditDetailCategoryArr = array();
            $transactionConfigurationArr = array();
            $inventoryCategoryArr = array();
            $inventoryAccountArr = array();
            $enumCoaTypeArr = array();

            $this->load->model('categories_model');

            $temp = array();            
            if(!empty($delivery_company)){

               if($delivery_company == '1'){
                $this->categories_model->delete_all('delivery_company');
                }else {
                    foreach ($delivery_company as $u) {                       

                        $avail = count($this->categories_model->get_one('delivery_company', $u['id']));
                        if ($avail > 0) {
                            $save = $this->categories_model->save('delivery_company', $u, $u['id']);
                        }
                        else {
                            $save = $this->categories_model->save('delivery_company', $u);
                        }

                        $temp[] = $u['id'];
                    }


                    $this->categories_model->delete_diffrence('delivery_company', 'id', $temp);

                }

            }//end delivery_company

            $temp = array();
            if(!empty($delivery_courier)){

               if($delivery_courier == '1'){
                $this->categories_model->delete_all('delivery_courier');
                }else {
                    foreach ($delivery_courier as $u) {                       

                        $avail = count($this->categories_model->get_one('delivery_courier', $u['id']));
                        if ($avail > 0) {
                            $save = $this->categories_model->save('delivery_courier', $u, $u['id']);
                        }
                        else {
                            $save = $this->categories_model->save('delivery_courier', $u);
                        }

                        $temp[] = $u['id'];
                    }


                    $this->categories_model->delete_diffrence('delivery_courier', 'id', $temp);

                }

            }//end delivery_courier
            
            if (!empty($job)) {
              if($job == '1'){
                $this->categories_model->delete_all('hr_jobs');
              }else {
                foreach ($job as $u) {
                  $array = array(
                    'id' => $u['id'],
                    'store_id' => $u['store_id'],
                    'jobs_name' => $u['jobs_name'],
                    'note' => $u['note'],
                    'is_grantor' => $u['is_grantor'],
                  );
                  $avail = count($this->categories_model->get_one('hr_jobs', $u['id']));
                  if ($avail > 0) {
                    $save = $this->categories_model->save('hr_jobs', $array, $u['id']);
                  }else {
                    $save = $this->categories_model->save('hr_jobs', $array);
                  }
                  $jobArr[] = $u['id'];
                }
                $this->categories_model->delete_diffrence('hr_jobs', 'id', $jobArr);
              }
            }
            if (!empty($employee_affair)) {
              if($employee_affair == '1'){
                $this->categories_model->delete_all('hr_enum_employee_affair');
              }else {
                foreach ($employee_affair as $u) {
                  $array = array(
                    'id' => $u['id'],
                    'name' => $u['name'],
                    'during' => $u['during'],
                    'next_job' => $u['next_job'],
                    'background_color' => $u['background_color'],
                  );
                  $avail = count($this->categories_model->get_one('hr_enum_employee_affair', $u['id']));
                  if ($avail > 0) {
                    $save = $this->categories_model->save('hr_enum_employee_affair', $array, $u['id']);
                  }else {
                    $save = $this->categories_model->save('hr_enum_employee_affair', $array);
                  }
                  $employeeAffairArr[] = $u['id'];
                }
                $this->categories_model->delete_diffrence('hr_enum_employee_affair', 'id', $employeeAffairArr);
              }
            }
            if (!empty($salary_component)) {
              if($salary_component == '1'){
                $this->categories_model->delete_all('hr_salary_component');
              }else {
                foreach ($salary_component as $u) {
                  $array = array(
                    'id' => $u['id'],
                    'name' => $u['name'],
                    'is_enhancer' => $u['is_enhancer'],
                    'key' => $u['key'],
                    // 'formula_default' => $u['formula_default'],
                    'is_static' => $u['is_static'],
                  );
                  $avail = count($this->categories_model->get_one('hr_salary_component', $u['id']));
                  if ($avail > 0) {
                    $save = $this->categories_model->save('hr_salary_component', $array, $u['id']);
                  }else {
                    $save = $this->categories_model->save('hr_salary_component', $array);
                  }
                  $salaryComponentArr[] = $u['id'];
                }
                $this->categories_model->delete_diffrence('hr_salary_component', 'id', $salaryComponentArr);
              }
            }
            if (!empty($job_component)) {
              if($job_component == '1'){
                $this->categories_model->delete_all('hr_jobs_components');
              }else {
                foreach ($job_component as $u) {
                  $array = array(
                    'id' => $u['id'],
                    'job_id' => $u['job_id'],
                    'component_id' => $u['component_id'],
                    'value' => $u['value'],
                  );
                  $avail = count($this->categories_model->get_one('hr_jobs_components', $u['id']));
                  if ($avail > 0) {
                    $save = $this->categories_model->save('hr_jobs_components', $array, $u['id']);
                  }else {
                    $save = $this->categories_model->save('hr_jobs_components', $array);
                  }
                  $jobComponentArr[] = $u['id'];
                }
                $this->categories_model->delete_diffrence('hr_jobs_components', 'id', $jobComponentArr);
              }
            }
            if (!empty($appraisal_template)) {
              if($appraisal_template == '1'){
                $this->categories_model->delete_all('hr_appraisal_template');
                $this->categories_model->delete_all('hr_appraisal_category');
                $this->categories_model->delete_all('hr_appraisal_detail_category');
              }else {
                foreach ($appraisal_template['appraisal_template'] as $u) {
                  $array = array(
                    'id' => $u['id'],
                    'name' => $u['name'],
                    'description' => $u['description'],
                  );
                  $avail = count($this->categories_model->get_one('hr_appraisal_template', $u['id']));
                  if ($avail > 0) {
                    $save = $this->categories_model->save('hr_appraisal_template', $array, $u['id']);
                  }else {
                    $save = $this->categories_model->save('hr_appraisal_template', $array);
                  }
                  $appraisalTemplateArr[] = $u['id'];
                }
                $this->categories_model->delete_diffrence('hr_appraisal_template', 'id', $appraisalTemplateArr);
                foreach ($appraisal_template['appraisal_category'] as $u) {
                  $array = array(
                    'id' => $u['id'],
                    'appraisal_template_id' => $u['appraisal_template_id'],
                    'name_category' => $u['name_category'],
                  );
                  $avail = count($this->categories_model->get_one('hr_appraisal_category', $u['id']));
                  if ($avail > 0) {
                    $save = $this->categories_model->save('hr_appraisal_category', $array, $u['id']);
                  }else {
                    $save = $this->categories_model->save('hr_appraisal_category', $array);
                  }
                  $appraisalCategoryArr[] = $u['id'];
                }
                $this->categories_model->delete_diffrence('hr_appraisal_category', 'id', $appraisalCategoryArr);
                foreach ($appraisal_template['appraisal_detail_category'] as $u) {
                  $array = array(
                    'id' => $u['id'],
                    'appraisal_category_id' => $u['appraisal_category_id'],
                    'name' => $u['name'],
                    'point' => $u['point'],
                  );
                  $avail = count($this->categories_model->get_one('hr_appraisal_detail_category', $u['id']));
                  if ($avail > 0) {
                    $save = $this->categories_model->save('hr_appraisal_detail_category', $array, $u['id']);
                  }else {
                    $save = $this->categories_model->save('hr_appraisal_detail_category', $array);
                  }
                  $appraisalDetailCategoryArr[] = $u['id'];
                }
                $this->categories_model->delete_diffrence('hr_appraisal_detail_category', 'id', $appraisalDetailCategoryArr);
              }
            }
            if (!empty($audit_template)) {
              if($audit_template == '1'){
                $this->categories_model->delete_all('hr_audit_template');
                $this->categories_model->delete_all('hr_audit_template_category');
                $this->categories_model->delete_all('hr_audit_template_detail_category');
              }else {
                foreach ($audit_template['audit_template'] as $u) {
                  $array = array(
                    'id' => $u['id'],
                    'name' => $u['name'],
                    'description' => $u['description'],
                  );
                  $avail = count($this->categories_model->get_one('hr_audit_template', $u['id']));
                  if ($avail > 0) {
                    $save = $this->categories_model->save('hr_audit_template', $array, $u['id']);
                  }else {
                    $save = $this->categories_model->save('hr_audit_template', $array);
                  }
                  $auditTemplateArr[] = $u['id'];
                }
                $this->categories_model->delete_diffrence('hr_audit_template', 'id', $auditTemplateArr);
                foreach ($audit_template['audit_category'] as $u) {
                  $array = array(
                    'id' => $u['id'],
                    'audit_template_id' => $u['audit_template_id'],
                    'name_category' => $u['name_category'],
                  );
                  $avail = count($this->categories_model->get_one('hr_audit_template_category', $u['id']));
                  if ($avail > 0) {
                    $save = $this->categories_model->save('hr_audit_template_category', $array, $u['id']);
                  }else {
                    $save = $this->categories_model->save('hr_audit_template_category', $array);
                  }
                  $auditCategoryArr[] = $u['id'];
                }
                $this->categories_model->delete_diffrence('hr_audit_template_category', 'id', $auditCategoryArr);
                foreach ($audit_template['audit_detail_category'] as $u) {
                  $array = array(
                    'id' => $u['id'],
                    'audit_category_id' => $u['audit_category_id'],
                    'name' => $u['name'],
                    'point' => $u['point'],
                  );
                  $avail = count($this->categories_model->get_one('hr_audit_template_detail_category', $u['id']));
                  if ($avail > 0) {
                    $save = $this->categories_model->save('hr_audit_template_detail_category', $array, $u['id']);
                  }else {
                    $save = $this->categories_model->save('hr_audit_template_detail_category', $array);
                  }
                  $auditDetailCategoryArr[] = $u['id'];
                }
                $this->categories_model->delete_diffrence('hr_audit_template_detail_category', 'id', $auditDetailCategoryArr);
              }
            }
            if (!empty($groups)) {
                if($groups == '1'){
                    $this->categories_model->delete_all('groups');
                }else {
                    foreach ($groups as $u) {
                        $array = array('id' => $u['id'],
                                       'name' => $u['name'],
                                       'description' => $u['description']
                                ); 

                        $avail = count($this->categories_model->get_one('groups', $u['id']));
                        if ($avail > 0) {
                            $save = $this->categories_model->save('groups', $array, $u['id']);
                        }
                        else {
                            $save = $this->categories_model->save('groups', $array);
                        }

                        $groupsArray[] = $u['id'];
                    }

                    $this->categories_model->delete_diffrence('groups', 'id', $groupsArray);
                }
            }


            if (!empty($user)) {
                if($user == '1'){
                    $this->categories_model->delete_all('users', '1');
                }else {
                    $userArr[]      = '1';
                    $userGroupArr[] = '1';
                    foreach ($user as $u) {
                        $array = array('id' => $u['id'],
                                       'ip_address' => $u['ip_address'],
                                       'username' => $u['username'],
                                       'password' => $u['password'],
                                       'salt' => $u['salt'],
                                       'email' => $u['email'],
                                       'activation_code' => $u['activation_code'],
                                       'forgotten_password_code' => $u['forgotten_password_code'],
                                       'forgotten_password_time' => $u['forgotten_password_time'],
                                       'remember_code' => $u['remember_code'],
                                       'created_on' => $u['created_on'],
                                       'last_login' => $u['last_login'],
                                       'active' => $u['active'],
                                       'name' => $u['name'],
                                       'company' => $u['company'],
                                       'phone' => $u['phone'],
                                       'address' => $u['address'],
                                       'identity_type' => $u['identity_type'],
                                       'identity_num' => $u['identity_num'],
                                       'gender' => $u['gender'],
                                       'store_id' => $u['store_id'],
                                       'pin' => $u['pin'],
                                       'outlet_id' => $u['outlet_id'], 
                                        'nip' => $u['nip'],
                                        'account_branch' => $u['account_branch'],
                                        'account_no' => $u['account_no'],
                                        'account_name' => $u['account_name'],
                                        'account_bank_id' => $u['account_bank_id'] );

                        $avail = $this->categories_model->get_one('users', $u['id']);
                        if (count($avail) > 0) {
                          if($avail->active!=$u['active']){
                            unset($array['active']);
                          }
                            $save = $this->categories_model->save('users', $array, $u['id']);
                        }
                        else {
                            $save = $this->categories_model->save('users', $array);
                        }

                        $userArr[] = $u['id'];

                        foreach ($u['groups_user'] as $ux) {
                            $array = array('id' => $ux['id'],
                                           'user_id' => $ux['user_id'],
                                           'group_id' => $ux['group_id']);

                            $avail = count($this->categories_model->get_one('users_groups', $ux['id']));
                            if ($avail > 0) {
                                $save = $this->categories_model->save('users_groups', $array, $ux['id']);
                            }
                            else {
                                $save = $this->categories_model->save('users_groups', $array);
                            }

                            $userGroupArr[] = $ux['id'];

                        }
                    }

                    $this->categories_model->delete_diffrence('users', 'id', $userArr);
                    $this->categories_model->delete_diffrence('users_groups', 'id', $userGroupArr);
                }
            }


            if (!empty($store)) {
                if($store == '1'){
                    $this->categories_model->delete_all('store');
                }else {
                    foreach ($store as $u) {
                        //save image
                        if (! empty($u['store_logo'])) {
                            $this->save_image($this->data['setting']['server_base_url'] . $u['store_logo'], 'uploads/store');
                        }

                        $array = array('id' => $u['id'],
                                       'store_name' => $u['store_name'],
                                       'store_address' => $u['store_address'],
                                       'store_description' => $u['store_description'],
                                       'store_facebook' => $u['store_facebook'],
                                       'store_twitter' => $u['store_twitter'],
                                       'store_instagram' => $u['store_instagram'],
                                       'store_phone' => $u['store_phone'],
                                       'store_logo' => $u['store_logo'],
                                       'store_last_sync' =>  date("Y-m-d H:i:s"));

                        $avail = count($this->categories_model->get_one('store', $u['id']));
                        if ($avail > 0) {
                            $save = $this->categories_model->save('store', $array, $u['id']);
                        }
                        else {
                            $save = $this->categories_model->save('store', $array);
                        }

                        $storeArr[] = $u['id'];
                    }

                    $this->categories_model->delete_diffrence('store', 'id', $storeArr);
                }
            }


            if (!empty($outlet)) {
                if($outlet == '1'){
                    $this->categories_model->delete_all('outlet');
                }else {
                    foreach ($outlet as $u) {
                        $array = array('id' => $u['id'],
                                       'outlet_name' => $u['outlet_name'],
                                       'is_warehouse' => $u['is_warehouse'],
                                       'account_id' => $u['account_id'],
                                       'store_id' => $u['store_id'],
                                       'checking_order' => $u['checking_order'],
                                     );

                        $avail = count($this->categories_model->get_one('outlet', $u['id']));
                        if ($avail > 0) {
                            $save = $this->categories_model->save('outlet', $array, $u['id']);
                        }
                        else {
                            $save = $this->categories_model->save('outlet', $array);
                        }

                        $outletArr[] = $u['id'];
                    }

                    $this->categories_model->delete_diffrence('outlet', 'id', $outletArr);
                }
            }
            $this->load->model("delivery_cost_model");
            if (!empty($delivery_cost)) {
                if($delivery_cost == '1'){
                    // $this->delivery_cost_model->delete_all('enum_delivery_cost');
                }else {
                    foreach ($delivery_cost as $u) {
                        $array = array('id' => $u['id'],
                                       'delivery_cost_name' => $u['delivery_cost_name'],
                                       'delivery_cost' => $u['delivery_cost'],
                                       'created_at' => $u['created_at'],
                                       'is_percentage' => $u['is_percentage'],
                                       'created_by' => $u['created_by']);

                        $avail = count($this->delivery_cost_model->get_one('enum_delivery_cost', $u['id']));
                        if ($avail > 0) {
                            $save = $this->delivery_cost_model->save('enum_delivery_cost', $array, $u['id']);
                        }
                        else {
                            $save = $this->delivery_cost_model->save('enum_delivery_cost', $array);
                        }

                        $deliveryCostArr[] = $u['id'];
                    }

                    $this->delivery_cost_model->delete_diffrence('enum_delivery_cost', 'id', $deliveryCostArr);
                }
            }
            $this->load->model("bank_account_model");
            if (!empty($bank_account)) {
                if($bank_account == '1'){
                    // $this->delivery_cost_model->delete_all('enum_delivery_cost');
                }else {
                    foreach ($bank_account as $u) {
                        $array = array('id' => $u['id'],
                                       'bank_name' => $u['bank_name'],
                                       'store_id' => $u['store_id'],
                                       'account_id' => $u['account_id']
                                       );

                        $avail = count($this->delivery_cost_model->get_one('bank_account', $u['id']));
                        if ($avail > 0) {
                            $save = $this->delivery_cost_model->save('bank_account', $array, $u['id']);
                        }
                        else {
                            $save = $this->delivery_cost_model->save('bank_account', $array);
                        }

                        $deliveryCostArr[] = $u['id'];
                    }

                    $this->delivery_cost_model->delete_diffrence('bank_account', 'id', $deliveryCostArr);
                }
            }
            if (!empty($floor)) {
                if($floor == '1'){
                    $this->categories_model->delete_all('floor');
                }else {
                    foreach ($floor as $u) {
                        $array = array('id' => $u['id'],
                                       'floor_name' => $u['floor_name'],
                                       'store_id' => $u['store_id'],
                                       'is_active' => $u['is_active']);


                        $avail = count($this->categories_model->get_one('floor', $u['id']));
                        if ($avail > 0) {
                            $save = $this->categories_model->save('floor', $array, $u['id']);
                        }
                        else {
                            $save = $this->categories_model->save('floor', $array);
                        }

                        $floorArr[] = $u['id'];
                    }

                    $this->categories_model->delete_diffrence('floor', 'id', $floorArr);
                }
            }
            if (!empty($supplier)) {
                if($supplier == '1'){
                    $this->categories_model->delete_all('supplier');
                }else {
                    foreach ($supplier as $u) {
                        $array = array('id' => $u['id'],
                                       'name' => $u['name'],
                                       'address' => $u['address'],
                                       'contact_name' => $u['contact_name'],
                                       'phone' => $u['phone'],
                                       'email' => $u['email'],
                                       'created_at' => $u['created_at'],
                                       'created_by' => $u['created_by'],
                                       'account_receivable_id' => $u['account_receivable_id'],
                                       'account_payable_id' => $u['account_payable_id'],
                                );


                        $avail = count($this->categories_model->get_one('supplier', $u['id']));
                        if ($avail > 0) {
                            $save = $this->categories_model->save('supplier', $array, $u['id']);
                        }
                        else {
                            $save = $this->categories_model->save('supplier', $array);
                        }

                        $floorArr[] = $u['id'];
                    }

                    $this->categories_model->delete_diffrence('supplier', 'id', $floorArr);
                }
            }
            if (!empty($account)) {
                if($account == '1'){
                    $this->categories_model->delete_all('account');
                }else {
                    foreach ($account as $u) {
                        $array = array('id' => $u['id'],
                                        'code' => $u['code'],
                                       'name' => $u['name'],
                                       'account_type_id' => $u['account_type_id'],
                                       'is_active' => $u['is_active'],
                                       'created_at' => $u['created_at'],
                                       'created_by' => $u['created_by'],
                                       'modified_at' => $u['modified_at'],
                                       'modified_by' => $u['modified_by']
                                );


                        $avail = count($this->categories_model->get_one('account', $u['id']));
                        if ($avail > 0) {
                            $save = $this->categories_model->save('account', $array, $u['id']);
                        }
                        else {
                            $save = $this->categories_model->save('account', $array);
                        }

                        $floorArr[] = $u['id'];
                    }

                    $this->categories_model->delete_diffrence('account', 'id', $floorArr);
                }
            }

            if (!empty($table)) {
                if($table == '1'){
                    $this->categories_model->delete_all('table');
                }else {
                    foreach ($table as $u) {
                        $avail = count($this->categories_model->get_one('table', $u['id']));
                        if ($avail > 0) {
                            $array = array('id' => $u['id'],
                                           'table_name' => $u['table_name'],
                                           'floor_id' => $u['floor_id'],
                                           'pos_x' => $u['pos_x'],
                                           'pos_y' => $u['pos_y'],
                                           'width' => $u['width'],
                                           'height' => $u['height'],
                                           'rotate' => $u['rotate'],
                                           'table_shape' => $u['table_shape'],
                                           'status' => $u['status'],
                                           'json_data' => $u['json_data'],
                                           'is_active' => $u['is_active']);
                            $save  = $this->categories_model->save('table', $array, $u['id']);
                        }
                        else {
                            $array = array('id' => $u['id'],
                                           'table_name' => $u['table_name'],
                                           'floor_id' => $u['floor_id'],
                                           'pos_x' => $u['pos_x'],
                                           'pos_y' => $u['pos_y'],
                                           'width' => $u['width'],
                                           'height' => $u['height'],
                                           'rotate' => $u['rotate'],
                                           'table_shape' => $u['table_shape'],
                                           'status' => $u['status'],
                                           'customer_count' => $u['customer_count'],
                                           'table_status' => $u['table_status'],
                                           'json_data' => $u['json_data'],
                                           'is_active' => $u['is_active']);
                            $save  = $this->categories_model->save('table', $array);
                        }

                        $tableArr[] = $u['id'];
                    }

                    $this->categories_model->delete_diffrence('table', 'id', $tableArr);
                }
            }


            if (!empty($category)) {
                if($category == '1'){
                    $this->categories_model->delete_all('category');
                }else {
                    foreach ($category as $u) {
                        //save image
                        if (! empty($u['icon_url'])) {
                            $this->save_image($this->data['setting']['server_base_url'] . $u['icon_url'], 'uploads/category');
                        }

                        $array = array('id' => $u['id'],
                                       'category_name' => $u['category_name'],
                                       'outlet_id' => $u['outlet_id'],
                                       'is_package' => $u['is_package'],
                                       'is_active' => $u['is_active'],
                                       'icon_url' => $u['icon_url']);

                        $avail = count($this->categories_model->get_one('category', $u['id']));
                        if ($avail > 0) {
                            $save = $this->categories_model->save('category', $array, $u['id']);
                        }
                        else {
                            $save = $this->categories_model->save('category', $array);
                        }

                        $categoryArr[] = $u['id'];
                    }

                    $this->categories_model->delete_diffrence('category', 'id', $categoryArr);
                }
            }

            if (!empty($menu)) {
                if($menu == '1'){
                    $this->categories_model->delete_all('menu');
                }else {
                    foreach ($menu as $u) {
                        //save image
                        if (! empty($u['icon_url'])) {
                            $this->save_image($this->data['setting']['server_base_url'] . $u['icon_url'], 'uploads/menu');
                        }

                        $array = array('id' => $u['id'],
                                       'menu_name' => $u['menu_name'],
                                       'menu_price' => $u['menu_price'],
                                       'point' => $u['point'],
                                       'menu_hpp' => $u['menu_hpp'],
                                       'category_id' => $u['category_id'],
                                       'is_promo' => $u['is_promo'],
                                       'has_schedule' => $u['has_schedule'],
                                       'is_enable_outside_schedule' => $u['is_enable_outside_schedule'],
                                       'promo_schedule_id' => $u['promo_schedule_id'],
                                       'created_at' => $u['created_at'],
                                       'created_by' => $u['created_by'],
                                       'modified_at' => $u['modified_at'],
                                       'modified_by' => $u['modified_by'],
                                       'icon_url' => $u['icon_url'],
                                       'available' => $u['available'],
                                       'is_instant' => $u['is_instant'],
                                       'process_checker' => $u['process_checker'],
                                       'color' => $u['color'],
                                       'use_taxes' => $u['use_taxes'],
                                       'menu_short_name' => $u['menu_short_name'],
                                       'duration'=>$u['duration'],
                                       'background_color'=>$u['background_color'],
                                       'position'=>$u['position'],
                                       'is_active'=>$u['is_active']
                                 );

                        $avail = count($this->categories_model->get_one('menu', $u['id']));
                        if ($avail > 0) {
                            $save = $this->categories_model->save('menu', $array, $u['id']);
                        }
                        else {
                            $save = $this->categories_model->save('menu', $array);
                        }

                        $menuArr[] = $u['id'];

                        if (! empty($u['menu_option'])) {
                            foreach ($u['menu_option'] as $ux) {
                                $array = array('id' => $ux['id'],
                                               'option_name' => $ux['option_name'],
                                               'menu_id' => $ux['menu_id'],
                                               'sequence' => $ux['sequence']);

                                $avail = count($this->categories_model->get_one('menu_option', $ux['id']));
                                if ($avail > 0) {
                                    $save = $this->categories_model->save('menu_option', $array, $ux['id']);
                                }
                                else {
                                    $save = $this->categories_model->save('menu_option', $array);
                                }

                                $menuOptArr[] = $ux['id'];

                                foreach ($ux['values'] as $uxa) {
                                    $array = array('id' => $uxa['id'],
                                                   'option_value_name' => $uxa['option_value_name'],
                                                   'option_id' => $uxa['option_id'],
                                                   'sequence' => $uxa['sequence']);

                                    $avail = count($this->categories_model->get_one('menu_option_value', $uxa['id']));
                                    if ($avail > 0) {
                                        $save = $this->categories_model->save('menu_option_value', $array, $uxa['id']);
                                    }
                                    else {
                                        $save = $this->categories_model->save('menu_option_value', $array);
                                    }

                                    $menuOptValArr[] = $uxa['id'];
                                }
                            }
                        }

                        if (! empty($u['menu_side_dish'])) {
                            foreach ($u['menu_side_dish'] as $ux) {
                                $array = array('id' => $ux['id'],
                                               'side_dish_id' => $ux['side_dish_id'],
                                               'menu_id' => $ux['menu_id'],
                                               'created_at' => $u['created_at'],
                                               'created_by' => $u['created_by'],
                                               'modified_at' => $u['modified_at'],
                                               'modified_by' => $u['modified_by'],
                                               'sequence' => $ux['sequence']);

                                $avail = count($this->categories_model->get_one('menu_side_dish', $ux['id']));
                                if ($avail > 0) {
                                    $save = $this->categories_model->save('menu_side_dish', $array, $ux['id']);
                                }
                                else {
                                    $save = $this->categories_model->save('menu_side_dish', $array);
                                }

                                $menuSideArr[] = $ux['id'];
                            }
                        } //end sidedish


                        if (! empty($u['menu_promo'])) {
                            foreach ($u['menu_promo'] as $ux) {                               

                                $avail = count($this->categories_model->get_one('menu_promo', $ux['id']));
                                if ($avail > 0) {
                                    $save = $this->categories_model->save('menu_promo', $ux, $ux['id']);
                                }
                                else {
                                    $save = $this->categories_model->save('menu_promo', $ux);
                                }

                                $menu_promo_array[] = $ux['id'];
                            }
                        } //end menu_promo

                        if (! empty($u['menu_promo_side_dish'])) {
                            foreach ($u['menu_promo_side_dish'] as $ux) {                               

                                $avail = count($this->categories_model->get_one('menu_promo_side_dish', $ux['id']));
                                if ($avail > 0) {
                                    $save = $this->categories_model->save('menu_promo_side_dish', $ux, $ux['id']);
                                }
                                else {
                                    $save = $this->categories_model->save('menu_promo_side_dish', $ux);
                                }

                                $menu_promo_side_dish_array[] = $ux['id'];
                            }
                        } //end menu_promo





                    }

                    $this->categories_model->delete_diffrence('menu', 'id', $menuArr);
                    $this->categories_model->delete_diffrence('menu_option', 'id', $menuOptArr);
                    $this->categories_model->delete_diffrence('menu_option_value', 'id', $menuOptValArr);
                    $this->categories_model->delete_diffrence('menu_side_dish', 'id', $menuSideArr);
                    $this->categories_model->delete_diffrence('menu_promo', 'id', $menu_promo_array);
                    $this->categories_model->delete_diffrence('menu_promo_side_dish', 'id', $menu_promo_side_dish_array);
                }
            }

            if (!empty($taxes)) {
                if($taxes == '1'){
                    $this->categories_model->delete_all('taxes');
                    $this->db->truncate('order_taxes');
                }else {
                    $get_order_taxes = $this->categories_model->get('order_taxes')->result();                    
                    foreach ($taxes as $u) {
                        $array = array('id' => $u['id'],
                                       'tax_name' => $u['tax_name'],
                                       'account_id' => $u['account_id'],
                                       'tax_percentage' => $u['tax_percentage'],
                                       'is_service' => $u['is_service']);

                        $avail = count($this->categories_model->get_one('taxes', $u['id']));
                        if ($avail > 0) {
                            $save = $this->categories_model->save('taxes', $array, $u['id']);
                        }
                        else {
                            $save = $this->categories_model->save('taxes', $array);
                        }

                        if ($save > 0) {
                          if (empty($get_order_taxes)) {
                            for ($i=1; $i <= 3; $i++) { 
                              $array = array(
                                'tax_id'      => $save,
                                'order_type'  => $i
                              );
                              $order_taxes = $this->categories_model->save('order_taxes', $array);
                            }
                          } 
                        }

                        $taxesArr[] = $u['id'];
                    }
                    $this->categories_model->delete_diffrence('taxes', 'id', $taxesArr);
                }
            }

            if (!empty($inventory_category)) {
              if($inventory_category == '1'){
                    $this->categories_model->delete_all('inventory_category');
                }else {
                    foreach ($inventory_category as $u) {
                        $array = array('id' => $u['id'],
                                       'category_name' => $u['category_name'],
                                       'is_active' => $u['is_active']);

                        $avail = count($this->categories_model->get_by('inventory_category', $u['id'], 'id'));
                        if ($avail > 0) {
                            $save = $this->categories_model->save_by('inventory_category', $array, $u['id'], 'id');
                        }
                        else {
                            $save = $this->categories_model->save('inventory_category', $array);
                        }

                        $inventoryCategoryArr[] = $u['id'];
                    }

                    $this->categories_model->delete_diffrence('inventory_category', 'id', $inventoryCategoryArr);
                }
            }

            if (!empty($inventory_account)) {
              if($inventory_account == '1'){
                    $this->categories_model->delete_all('inventory_account');
                }else {
                    foreach ($inventory_account as $u) {
                        $array = array('id' => $u['id'],
                                       'inventory_id' => $u['inventory_id'],
                                       'account_id' => $u['account_id'],
                                       'coa_type' => $u['coa_type']);

                        $avail = count($this->categories_model->get_by('inventory_account', $u['id'], 'id'));
                        if ($avail > 0) {
                            $save = $this->categories_model->save_by('inventory_account', $array, $u['id'], 'id');
                        }
                        else {
                            $save = $this->categories_model->save('inventory_account', $array);
                        }

                        $inventoryAccountArr[] = $u['id'];
                    }

                    $this->categories_model->delete_diffrence('inventory_account', 'id', $inventoryAccountArr);
                }
            }

            if (!empty($inventory)) {
                if($inventory == '1'){
                    $this->categories_model->delete_all('inventory');
                }else {
                    foreach ($inventory as $u) {
                        $array = array('id' => $u['id'],
                                       'name' => $u['name'],
                                       'unit' => $u['unit'],
                                       'price' => $u['price'],
                                       'minimal_stock' => $u['minimal_stock'],
                                       'uom_id' => $u['uom_id'],
                                       'is_active' => $u['is_active'],
                                       'category_id' => $u['category_id']
                                       );

                        $avail = count($this->categories_model->get_by('inventory', $u['id'], 'id'));
                        if ($avail > 0) {
                            $save = $this->categories_model->save_by('inventory', $array, $u['id'], 'id');
                        }
                        else {
                            $save = $this->categories_model->save('inventory', $array);
                        }

                        $inventoryArr[] = $u['id'];
                    }
                    $this->categories_model->delete_diffrence('inventory', 'id', $inventoryArr);
                }
            }
            if (!empty($uom)) {
                if($uom == '1'){
                    $this->categories_model->delete_all('uoms');
                }else {
                    foreach ($uom as $u) {
                        $array = array('id' => $u['id'],
                                       'code' => $u['code'],
                                       'name' => $u['name'],
                                       );

                        $avail = count($this->categories_model->get_by('uoms', $u['id'], 'id'));
                        if ($avail > 0) {
                            $save = $this->categories_model->save_by('uoms', $array, $u['id'], 'id');
                        }
                        else {
                            $save = $this->categories_model->save('uoms', $array);
                        }

                        $uomArr[] = $u['id'];
                    }
                    $this->categories_model->delete_diffrence('uoms', 'id', $uomArr);
                }
            }
            if (!empty($inventory_uom)) {
                if($inventory_uom == '1'){
                    $this->categories_model->delete_all('inventory_uoms');
                }else {
                    foreach ($inventory_uom as $u) {
                        $array = array('id' => $u['id'],
                                       'inventory_id' => $u['inventory_id'],
                                       'uom_id' => $u['uom_id'],
                                       );

                        $avail = count($this->categories_model->get_by('inventory_uoms', $u['id'], 'id'));
                        if ($avail > 0) {
                            $save = $this->categories_model->save_by('inventory_uoms', $array, $u['id'], 'id');
                        }
                        else {
                            $save = $this->categories_model->save('inventory_uoms', $array);
                        }

                        $inventoryUomArr[] = $u['id'];
                    }
                    $this->categories_model->delete_diffrence('inventory_uoms', 'id', $inventoryUomArr);
                }
            }
            if (!empty($inventory_composition)) {
                if($inventory_composition == '1'){
                    $this->categories_model->delete_all('inventory_compositions');
                }else {
                    foreach ($inventory_composition as $u) {
                        $array = array('id' => $u['id'],
                                       'parent_inventory_id' => $u['parent_inventory_id'],
                                       'inventory_id' => $u['inventory_id'],
                                       'uom_id' => $u['uom_id'],
                                       'quantity' => $u['quantity'],
                                       );

                        $avail = count($this->categories_model->get_by('inventory_compositions', $u['id'], 'id'));
                        if ($avail > 0) {
                            $save = $this->categories_model->save_by('inventory_compositions', $array, $u['id'], 'id');
                        }
                        else {
                            $save = $this->categories_model->save('inventory_compositions', $array);
                        }

                        $inventoryCompositionArr[] = $u['id'];
                    }
                    $this->categories_model->delete_diffrence('inventory_compositions', 'id', $inventoryCompositionArr);
                }
            }
            if (!empty($enum_coa_type)) {
                if($enum_coa_type == '1'){
                    $this->categories_model->delete_all('enum_coa_type');
                }else {
                    foreach ($enum_coa_type as $u) {
                        $array = array('id' => $u['id'],
                                       'name' => $u['name'],
                                       'description' => $u['description'],
                                       );

                        $avail = count($this->categories_model->get_by('enum_coa_type', $u['id'], 'id'));
                        if ($avail > 0) {
                            $save = $this->categories_model->save_by('enum_coa_type', $array, $u['id'], 'id');
                        }
                        else {
                            $save = $this->categories_model->save('enum_coa_type', $array);
                        }

                        $enumCoaTypeArr[] = $u['id'];
                    }
                    $this->categories_model->delete_diffrence('enum_coa_type', 'id', $enumCoaTypeArr);
                }
            }
            if (!empty($enum_card_type)) {
                if($enum_card_type == '1'){
                    $this->categories_model->delete_all('enum_card_type');
                }else {
                    foreach ($enum_card_type as $u) {
                        $array = array('id' => $u['id'],
                                       'card_name' => $u['card_name'],
                                       'description' => $u['description'],
                                       );

                        $avail = count($this->categories_model->get_by('enum_card_type', $u['id'], 'id'));
                        if ($avail > 0) {
                            $save = $this->categories_model->save_by('enum_card_type', $array, $u['id'], 'id');
                        }
                        else {
                            $save = $this->categories_model->save('enum_card_type', $array);
                        }

                        $enumCardTypeArr[] = $u['id'];
                    }
                    $this->categories_model->delete_diffrence('enum_card_type', 'id', $enumCardTypeArr);
                }
            }
            if (!empty($bank_account_card)) {
                if($bank_account_card == '1'){
                    $this->categories_model->delete_all('bank_account_card');
                }else {
                    foreach ($bank_account_card as $u) {
                        $array = array('id' => $u['id'],
                                       'bank_account_id' => $u['bank_account_id'],
                                       'card_type_id' => $u['card_type_id'],
                                       );

                        $avail = count($this->categories_model->get_by('bank_account_card', $u['id'], 'id'));
                        if ($avail > 0) {
                            $save = $this->categories_model->save_by('bank_account_card', $array, $u['id'], 'id');
                        }
                        else {
                            $save = $this->categories_model->save('bank_account_card', $array);
                        }

                        $bankAccountCardArr[] = $u['id'];
                    }
                    $this->categories_model->delete_diffrence('bank_account_card', 'id', $bankAccountCardArr);
                }
            }
            if (!empty($menu_ingredient)) {
                if($menu_ingredient == '1'){
                    $this->categories_model->delete_all('menu_ingredient');
                }else {
                    foreach ($menu_ingredient as $u) {
                        $array = array('id' => $u['id'],
                                       'menu_id' => $u['menu_id'],
                                       'inventory_id' => $u['inventory_id'],
                                       'uom_id' => $u['uom_id'],
                                       'quantity' => $u['quantity'],
                                       'sequence' => $u['sequence'],
                                       'is_active' => $u['is_active']);

                        $avail = count($this->categories_model->get_one('menu_ingredient', $u['id']));
                        if ($avail > 0) {
                            $save = $this->categories_model->save('menu_ingredient', $array, $u['id']);
                        }
                        else {
                            $save = $this->categories_model->save('menu_ingredient', $array);
                        }

                        $menuIngredientArr[] = $u['id'];
                    }
                    $this->categories_model->delete_diffrence('menu_ingredient', 'id', $menuIngredientArr);
                }
            }

            if (!empty($compliment)) {
                if($compliment == '1'){
                    $this->categories_model->delete_all('compliment');
                }else {
                    foreach ($compliment as $u) {
                        $array = array('id' => $u['id'],
                                       'user_id' => $u['user_id'],
                                       'is_cogs' => $u['is_cogs'],
                                       'cogs_limit' => $u['cogs_limit'],
                                       'is_discount' => $u['is_discount'],
                                       'discount' => $u['discount'],
                                       'discount_limit' => $u['discount_limit'],
                                       'is_available_all_store' => $u['is_available_all_store'],
                                       'reset_period' => $u['reset_period'],
                                       'created_at' => $u['created_at'],
                                       'created_by' => $u['created_by'],
                                       'modified_at' => $u['modified_at'],
                                       'modified_by' => $u['modified_by'],
                                       );

                        $avail = count($this->categories_model->get_one('compliment', $u['id']));
                        if ($avail > 0) {
                            $save = $this->categories_model->save('compliment', $array, $u['id']);
                        }
                        else {
                            $save = $this->categories_model->save('compliment', $array);
                        }

                        $complimentArr[] = $u['id'];


                        if(!empty($u['compliment_store'])){

                            foreach ($u['compliment_store'] as $u) {
                                $array = array('id' => $u['id'],
                                   'compliment_id' => $u['compliment_id'],
                                   'store_id' => $u['store_id'],
                                   'created_at' => $u['created_at'],
                                   'created_by' => $u['created_by'],
                                   );

                                $avail = count($this->categories_model->get_one('compliment_store', $u['id']));
                                if ($avail > 0) {
                                    $save = $this->categories_model->save('compliment_store', $array, $u['id']);
                                }
                                else {
                                    $save = $this->categories_model->save('compliment_store', $array);
                                }

                                $compliment_store_array[] = $u['id'];                            
                            }

                        }



                    }//end foreach compliment


                    $this->categories_model->delete_diffrence('compliment_store', 'id', $compliment_store_array);
                    $this->categories_model->delete_diffrence('compliment', 'id', $complimentArr);
                }
            }//end compliment


            $member_category_array = array();
            $member_array = array();
            
            if(!empty($member_category)){

               if($member_category == '1'){
                $this->categories_model->delete_all('member_category');
                }else {
                    foreach ($member_category as $u) {
                       

                        $avail = count($this->categories_model->get_one('member_category', $u['id']));
                        if ($avail > 0) {
                            $save = $this->categories_model->save('member_category', $u, $u['id']);
                        }
                        else {
                            $save = $this->categories_model->save('member_category', $u);
                        }

                        $member_category_array[] = $u['id'];
                    }


                    $this->categories_model->delete_diffrence('member_category', 'id', $member_category_array);

                }

            }//end member category

            if(!empty($member)){

               if($member == '1'){
                $this->categories_model->delete_all('member');
                }else {
                    foreach ($member as $u) {
                       

                        $avail = count($this->categories_model->get_one('member', $u['id']));
                        if ($avail > 0) {
                            $save = $this->categories_model->save('member', $u, $u['id']);
                        }
                        else {
                            $save = $this->categories_model->save('member', $u);
                        }

                        $member_array[] = $u['id'];
                    }


                    $this->categories_model->delete_diffrence('member', 'id', $member_array);

                }

            }//end member

            $temp = array();            
            if(!empty($promo_cc)){

               if($promo_cc == '1'){
                $this->categories_model->delete_all('promo_cc');
                }else {
                    foreach ($promo_cc as $u) {
                       

                        $avail = count($this->categories_model->get_one('promo_cc', $u['id']));
                        if ($avail > 0) {
                            $save = $this->categories_model->save('promo_cc', $u, $u['id']);
                        }
                        else {
                            $save = $this->categories_model->save('promo_cc', $u);
                        }

                        $temp[] = $u['id'];
                    }


                    $this->categories_model->delete_diffrence('promo_cc', 'id', $temp);

                }

            }//end promo_cc


            $temp = array();            
            if(!empty($promo_discount)){

               if($promo_discount == '1'){
                $this->categories_model->delete_all('promo_discount');
                }else {
                    foreach ($promo_discount as $u) {
                       

                        $avail = count($this->categories_model->get_one('promo_discount', $u['id']));
                        if ($avail > 0) {
                            $save = $this->categories_model->save('promo_discount', $u, $u['id']);
                        }
                        else {
                            $save = $this->categories_model->save('promo_discount', $u);
                        }

                        $temp[] = $u['id'];
                    }


                    $this->categories_model->delete_diffrence('promo_discount', 'id', $temp);

                }

            }//end promo_discount


            $temp = array();            
            if(!empty($promo_cc_category)){

               if($promo_cc_category == '1'){
                $this->categories_model->delete_all('promo_cc_category');
                }else {
                    foreach ($promo_cc_category as $u) {
                       

                        $avail = count($this->categories_model->get_one('promo_cc_category', $u['id']));
                        if ($avail > 0) {
                            $save = $this->categories_model->save('promo_cc_category', $u, $u['id']);
                        }
                        else {
                            $save = $this->categories_model->save('promo_cc_category', $u);
                        }

                        $temp[] = $u['id'];
                    }


                    $this->categories_model->delete_diffrence('promo_cc_category', 'id', $temp);

                }

            }//end promo_cc_category


            $temp = array();            
            if(!empty($promo_discount_category)){

               if($promo_discount_category == '1'){
                $this->categories_model->delete_all('promo_discount_category');
                }else {
                    foreach ($promo_discount_category as $u) {                       

                        $avail = count($this->categories_model->get_one('promo_discount_category', $u['id']));
                        if ($avail > 0) {
                            $save = $this->categories_model->save('promo_discount_category', $u, $u['id']);
                        }
                        else {
                            $save = $this->categories_model->save('promo_discount_category', $u);
                        }

                        $temp[] = $u['id'];
                    }


                    $this->categories_model->delete_diffrence('promo_discount_category', 'id', $temp);

                }

            }//end promo_discount_category


            $temp = array();         
            if(!empty($promo_discount_menu)){

               if($promo_discount_menu == '1'){
                $this->categories_model->delete_all('promo_discount_menu');
                }else {
                    foreach ($promo_discount_menu as $u) {                       

                        $avail = count($this->categories_model->get_one('promo_discount_menu', $u['id']));
                        if ($avail > 0) {
                            $save = $this->categories_model->save('promo_discount_menu', $u, $u['id']);
                        }
                        else {
                            $save = $this->categories_model->save('promo_discount_menu', $u);
                        }

                        $temp[] = $u['id'];
                    }


                    $this->categories_model->delete_diffrence('promo_discount_menu', 'id', $temp);

                }

            }//end promo_discount_menu

            // add server sync to table promo cc menu
            $temp = array();         
            if(!empty($promo_cc_menu)){
              if($promo_cc_menu == '1'){
                $this->categories_model->delete_all('promo_cc_menu');
              } else {
                foreach ($promo_cc_menu as $u) {                       

                  $avail = count($this->categories_model->get_one('promo_cc_menu', $u['id']));
                  if ($avail > 0) {
                    $save = $this->categories_model->save('promo_cc_menu', $u, $u['id']);
                  }
                  else {
                    $save = $this->categories_model->save('promo_cc_menu', $u);
                  }

                  $temp[] = $u['id'];
                }
                $this->categories_model->delete_diffrence('promo_cc_menu', 'id', $temp);
              }
            } //end promo_cc_menu

            $temp = array();            
            if(!empty($promo_schedule)){

               if($promo_schedule == '1'){
                $this->categories_model->delete_all('promo_schedule');
                }else {
                    foreach ($promo_schedule as $u) {                       

                        $avail = count($this->categories_model->get_one('promo_schedule', $u['id']));
                        if ($avail > 0) {
                            $save = $this->categories_model->save('promo_schedule', $u, $u['id']);
                        }
                        else {
                            $save = $this->categories_model->save('promo_schedule', $u);
                        }

                        $temp[] = $u['id'];
                    }


                    $this->categories_model->delete_diffrence('promo_schedule', 'id', $temp);

                }

            }//end promo_schedule


            $temp = array();            
            if(!empty($voucher_group)){

               if($voucher_group == '1'){
                $this->categories_model->delete_all('voucher_group');
                }else {
                    foreach ($voucher_group as $u) {                       

                        $avail = count($this->categories_model->get_one('voucher_group', $u['id']));
                        if ($avail > 0) {
                            $save = $this->categories_model->save('voucher_group', $u, $u['id']);
                        }
                        else {
                            $save = $this->categories_model->save('voucher_group', $u);
                        }

                        $temp[] = $u['id'];
                    }


                    $this->categories_model->delete_diffrence('voucher_group', 'id', $temp);

                }

            }//end voucher_group

            $temp = array();            
            if(!empty($voucher_availability)){

               if($voucher_availability == '1'){
                $this->categories_model->delete_all('voucher_availability');
                }else {
                    foreach ($voucher_availability as $u) {                       

                        $avail = count($this->categories_model->get_one('voucher_availability', $u['id']));
                        if ($avail > 0) {
                            $save = $this->categories_model->save('voucher_availability', $u, $u['id']);
                        }
                        else {
                            $save = $this->categories_model->save('voucher_availability', $u);
                        }

                        $temp[] = $u['id'];
                    }


                    $this->categories_model->delete_diffrence('voucher_availability', 'id', $temp);

                }

            }//end voucher_group


            $temp = array();            
            if(!empty($voucher)){

               if($voucher == '1'){
                $this->categories_model->delete_all('voucher');
                }else {
                    foreach ($voucher as $u) {                       

                        $avail = $this->categories_model->get_one('voucher', $u['id']);
                        if (count($avail)> 0 || $avail->status != 0) {
                            $save = $this->categories_model->save('voucher', $u, $u['id']);
                        }
                        else {
                            $save = $this->categories_model->save('voucher', $u);
                        }

                        $temp[] = $u['id'];
                    }


                    $this->categories_model->delete_diffrence('voucher', 'id', $temp);

                }

            }//end voucher_group

            $temp = array();            
            if(!empty($feature)){

               if($feature == '1'){
                $this->categories_model->delete_all('feature');
                }else {
                    foreach ($feature as $u) {                       

                        $avail = $this->categories_model->get_one('feature', $u['id']);
                        if (count($avail)> 0 || $avail->status != 0) {
                            // $save = $this->categories_model->save('feature', $u, $u['id']);
                        }
                        else {
                            $save = $this->categories_model->save('feature', $u);
                        }

                        $temp[] = $u['id'];
                    }


                    $this->categories_model->delete_diffrence('feature', 'id', $temp);

                }

            }//end feature


            $temp = array();            
            if(!empty($feature_access)){

               if($feature_access == '1'){
                $this->categories_model->delete_all('feature_access');
                }else {
                    foreach ($feature_access as $u) {                       

                        $avail = $this->categories_model->get_one('feature_access', $u['id']);
                        if (count($avail)> 0 || $avail->status != 0) {
                            $save = $this->categories_model->save('feature_access', $u, $u['id']);
                        }
                        else {
                            $save = $this->categories_model->save('feature_access', $u);
                        }

                        $temp[] = $u['id'];
                    }


                    $this->categories_model->delete_diffrence('feature_access', 'id', $temp);

                }

            }//end feature_access
            

            $temp = array();            
             if (!empty($side_dish)) {
                if($side_dish == '1'){
                    $this->categories_model->delete_all('side_dish');
                }else {
                    foreach ($side_dish as $u) {
                        $array = array('id' => $u['id'],
                                       'store_id' => $u['store_id'],
                                       'name' => $u['name'],
                                       'price' => $u['price'],
                                       'created_at' => $u['created_at'],
                                       'modified_at' => $u['modified_at'],
                                       'modified_by' => $u['modified_by'],
                                       'created_by' => $u['created_by']
                                       );

                        $avail = count($this->categories_model->get_one('side_dish', $u['id']));
                        if ($avail > 0) {
                            $save = $this->categories_model->save('side_dish', $array, $u['id']);
                        }
                        else {
                            $save = $this->categories_model->save('side_dish', $array);
                        }

                        $temp[] = $u['id'];
                    }
                    $this->categories_model->delete_diffrence('side_dish', 'id', $temp);
                }
            }

            $temp = array();            
             if (!empty($side_dish_ingredient)) {
                if($side_dish_ingredient == '1'){
                    $this->categories_model->delete_all('side_dish_ingredient');
                }else {
                    foreach ($side_dish_ingredient as $u) {
                        $array = array('id' => $u['id'],
                                       'side_dish_id' => $u['side_dish_id'],
                                       'inventory_id' => $u['inventory_id'],
                                       'quantity' => $u['quantity'],
                                       'sequence' => $u['sequence'],
                                       'created_at' => $u['created_at'],
                                       'modified_at' => $u['modified_at'],
                                       'modified_by' => $u['modified_by'],
                                       'created_by' => $u['created_by']
                                       );

                        $avail = count($this->categories_model->get_one('side_dish_ingredient', $u['id']));
                        if ($avail > 0) {
                            $save = $this->categories_model->save('side_dish_ingredient', $array, $u['id']);
                        }
                        else {
                            $save = $this->categories_model->save('side_dish_ingredient', $array);
                        }

                        $temp[] = $u['id'];
                    }
                    $this->categories_model->delete_diffrence('side_dish_ingredient', 'id', $temp);
                }
            }


            $temp = array();            
            if(!empty($order_company)){

               if($order_company == '1'){
                    $this->categories_model->delete_all('order_company');
                }else {
                    foreach ($order_company as $u) {                       

                        $avail = $this->categories_model->get_one('order_company', $u['id']);
                        if (count($avail)> 0 || $avail->status != 0) {
                            $save = $this->categories_model->save('order_company', $u, $u['id']);
                        }
                        else {
                            $save = $this->categories_model->save('order_company', $u);
                        }

                        $temp[] = $u['id'];
                    }


                    $this->categories_model->delete_diffrence('order_company', 'id', $temp);

                }

            }//end feature_access

            // insert transaction_configuration by bening
            $this->load->model("transaction_configuration_model");
            if (!empty($transaction_configuration)) {
                if($transaction_configuration == '1'){
                    $this->transaction_configuration_model->delete_all('resto_transaction_configuration');
                }else {
                    foreach ($transaction_configuration as $u) {
                        $array = array('id' => $u['id'],
                                       'max_cost' => $u['max_cost'],
                                       'max_day_last_update' => $u['max_day_last_update'],
                                       'periode_cost' => $u['periode_cost'],
                                       'resto_id' => $u['resto_id'],
                                       'transaction_type' => $u['transaction_type'],
                                     );

                        $avail = count($this->transaction_configuration_model->get_one('resto_transaction_configuration', $u['id']));
                        if ($avail > 0) {
                            $save = $this->transaction_configuration_model->save('resto_transaction_configuration', $array, $u['id']);
                        }
                        else {
                            $save = $this->transaction_configuration_model->save('resto_transaction_configuration', $array);
                        }

                        $transactionConfigurationArr[] = $u['id'];
                    }

                    $this->transaction_configuration_model->delete_diffrence('resto_transaction_configuration', 'id', $transactionConfigurationArr);
                }
            }
			$temp = array();            
            if(!empty($inventory_convertion)){

               if($inventory_convertion == '1'){
                    $this->categories_model->delete_all('inventory_convertion');
                }else {
                    foreach ($inventory_convertion as $u) {                       

                        $avail = $this->categories_model->get_one('inventory_convertion', $u['id']);
                        if (count($avail)> 0 || $avail->status != 0) {
                            $save = $this->categories_model->save('inventory_convertion', $u, $u['id']);
                        }
                        else {
                            $save = $this->categories_model->save('inventory_convertion', $u);
                        }

                        $temp[] = $u['id'];
                    }


                    $this->categories_model->delete_diffrence('inventory_convertion', 'id', $temp);

                }

            }
      $temp = array();            
            if(!empty($general_expenses)){

               if($general_expenses == '1'){
                    $this->categories_model->delete_all('general_expenses');
                }else {
                    foreach ($general_expenses as $u) {                       

                        $avail = $this->categories_model->get_one('general_expenses', $u['id']);
                        if (count($avail)> 0 || $avail->status != 0) {
                            $save = $this->categories_model->save('general_expenses', $u, $u['id']);
                        }
                        else {
                            $save = $this->categories_model->save('general_expenses', $u);
                        }

                        $temp[] = $u['id'];
                    }


                    $this->categories_model->delete_diffrence('general_expenses', 'id', $temp);

                }

            }      
			
			// update module due date
            if(!empty($modules_due_date)){
               if($modules_due_date != '1'){
                    foreach ($modules_due_date as $module) { 
						$this->categories_model->save("module", array(
							"due_date" => ($module["due_date"] == "0000-00-00" ? NULL : $module["due_date"]),
							"reminder" => $module["reminder"],
							"is_installed" => ((date("Y-m-d") <= date("Y-m-d", strtotime($module["due_date"])) || $module["due_date"] == "0000-00-00") ? 1 : 0),
						), $module["module_id"]);
                    }
                }
            }
        }
    }

    private function save_image($my_img, $folderpath)
    {
		$fullpath = "";
        if ($folderpath != "" && $folderpath) {
            $fullpath = $folderpath . "/" . basename($my_img);
        }
        $ch = curl_init($my_img);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $rawdata = curl_exec($ch);
        curl_close($ch);
        if (file_exists($fullpath)) {
            unlink($fullpath);
        }
        $fp = fopen($fullpath, 'x');
        fwrite($fp, $rawdata);
		//echo "<script> alert('" . FCPATH .  $folderpath . "/" . basename($my_img) . "'); </script>";
		//rename(FCPATH . $folderpath . "/" . basename($my_img),FCPATH . $folderpath . "/" . "resto_logo.png");
        fclose($fp);
    }
}