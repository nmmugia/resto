<?php

/**
 * group access class
 * description: produce side menu admin and POS access
 */
class Groups_access {

    protected $access_menu = array();
    protected $_cache_groups_in_feature;

    function __construct()
    {
        $this->load->model('sidebar_menu_model');
    }


    /**
     * have_access in pos
     *
     * @param string check_feature to check
     * @param bool group_id id
     *
     * @return bool                 
     *
     * @author fkartika
     */

     public function check_feature_access($check_feature, $redirect_url=FALSE ){
        if(!$this->have_access($check_feature)){
            if($redirect_url){
                redirect($redirect_url);
            }
            show_404();
        }
    }
    
    public function have_access($check_feature, $group_id=false ){

        $this->load->model('feature_model');

        $group_id || $group_id = $this->group_id;

        if (isset($this->_cache_groups_in_feature[$group_id]))
        {
            $array_feature = $this->_cache_groups_in_feature[$group_id];
        }
        else
        {
            $groups_feature = $this->feature_model->get_selected_access($group_id);
            $array_feature = array();
            foreach ($groups_feature as $row)
            {
                $array_feature[] = $row->key;
            }
            $this->_cache_groups_in_feature[$group_id] = $array_feature;
        }

        if (in_array($check_feature, $array_feature))
        {
            return TRUE;
        }

        return FALSE;
    }
    
    /**
     * check side menu admin permission
     * @param  boolean $class  
     * @param  boolean $method 
     *
     * @author fkartika
     */
    public function check_menu_permission($class = FALSE, $method = FALSE ){

        if($this->session->userdata('menu_access')){
            $this->access_menu = $this->session->userdata('menu_access');
        }else{
            exit();
        }
        if(!$class)
            $class             = $this->router->fetch_class();

        if(!$method){
            // $method            = $this->router->fetch_method();
            $method            = 'index';
        }

        if (!isset($this->access_menu[$class][$method])){
            redirect(SITE_ADMIN);
        }
    }


/**
 * get menu and store into session
 * @param  int $group_id 
 *
 * @return array           [mixed array & string]
 * @author fkartika
 */
    function build_menu($group_id){
        $menu = $this->sidebar_menu_model->get_groups_menu($group_id);
        $menu_temp = array();
        $menu_access = array();

        foreach ($menu as $row){
            $menu_temp[$row->parent_id][] = $row;

            $access_temp = explode("/",$row->url);
            if(!empty($access_temp)){
                $class = @$access_temp[0];

                $method = "index";
                if(isset($access_temp[1]))
                    $method = $access_temp[1];

                $menu_access[$class][$method] =  $row->sidebar_menu_id;
            }

        }

        if(isset($menu_temp[0])){

            if(!$this->session->userdata('menu_access')){
                // $this->set_session($menu_access);
            }

            $tree = $this->create_tree($menu_temp, $menu_temp[0], SITE_ADMIN);
        }else{ 
            $tree = null;
        }
        return $tree;
    }


 /**
 * create_tree create sidebar menu
 * @param  array &$list    [array 2D]
 * @param  array $parent   
 * @param  string $route_url [concat route url when controller use subfolder ]
 * 
 * @return array          [mixed string html & object]
 * @author fkartika
 */
		function get_links($target="")
	 {
			$menu=array(
				"categories"=>"categories/add,categories/edit",
				"menus"=>"menus/add,menus/edit,menus/imports",
				"sidedish"=>"menus/add_sidedish,menus/edit_sidedish",
				"outlet"=>"outlet/add,outlet/edit",
				"store"=>"store/add,store/edit",
				"delivery_costs"=>"delivery_costs/add,delivery_costs/edit",
				"floor"=>"floor/add,floor/edit",
				"table"=>"table",
				"promo/discount"=>"promo/add_discount,promo/edit_discount",
				"promo/creditcard"=>"promo/creditcard_add,promo/creditcard_edit",
				"promo/promenu"=>"promo/promenu_add,promo/promenu_edit",
				"promo/schedule"=>"promo/schedule_add,promo/schedule_edit",
				"system/tax"=>"system/add_tax,system/edit_tax",
				"order_company"=>"order_company/add,order_company/edit",
				"member"=>"member/add,member/edit,member/imports",
				"voucher"=>"voucher/add_voucher_group,voucher/edit_voucher_group",
				"compliment"=>"compliment/add_compliment,compliment/edit_compliment",
				"uoms"=>"uoms/add,uoms/edit,uoms/imports",
				"inventory/manage"=>"inventory/add_inventory,inventory/edit_inventory,inventory/compositions",
				"supplier/supplier_list"=>"supplier/add,supplier/edit,supplier/imports",
				"hrd/setting_salary_component_list"=>"hrd/add_salary_component,hrd/edit_salary_component",
				"hrd/setting_jobs_list"=>"hrd/add_jobs,hrd/edit_jobs,hrd/set_salary_component",
				"stock_transfer/request"=>"stock_transfer/add_request,stock_transfer/edit_request,stock_transfer/detail,stock_transfer/cancel,stock_transfer/arrive",
				"stock_transfer/receive"=>"stock_transfer/detail_transfer,stock_transfer/process",
				"inventory_convertions"=>"inventory_convertions/add,inventory_convertions/edit",
				"inventory_process"=>"inventory_process/add,inventory_process/edit",
				"purchase_order/po_list"=>"purchase_order/add,purchase_order/po_create,purchase_order/edit,purchase_order/history,purchase_order/detail",
				"receive_stocks/listing"=>"receive_stocks/receive,receive_stocks/history",
				"inventory"=>"inventory/compositions",
				"stock/stocklet"=>"stock/adjustment,stock/set_outlet_stock,stock/list_stock,stock/transfer,stock/detail",
				"target_settings"=>"target_settings/add,target_settings/edit",
				"hrd/setting_office_hours"=>"hrd/add_office_hours,hrd/edit_office_hours",
				"hrd_schedule/index"=>"hrd_schedule/standard_schedule,hrd_schedule/history_holiday,hrd_schedule/set_change_schedule",
				"hrd_appraisal/setting_template_appraisal"=>"hrd_appraisal/view_template_appraisal",
				"hrd_appraisal/process_appraisal"=>"hrd_appraisal/add_process_appraisal,hrd_appraisal/edit_process_appraisal",
				"hrd_audit/setting_audit"=>"hrd_audit/view_template_audit",
				"hrd_staff/staff_list"=>"hrd_staff/detail_staff",
				"hrd_loan"=>"hrd_loan/add_loan,hrd_loan/edit_loan",
				"hrd_reimburse"=>"hrd_reimburse/add_reimburse,hrd_reimburse/edit_reimburse",
				"hrd_appraisal/setting_template_appraisal"=>"hrd_appraisal/add_template_appraisal,hrd_appraisal/edit_template_appraisal,hrd_appraisal/view_template_appraisal",
				"hrd_audit/setting_audit"=>"hrd_audit/add_template_audit,hrd_audit/edit_template_audit,hrd_audit/view_template_audit",
				"hrd_resign_reason"=>"hrd_resign_reason/add,hrd_resign_reason/edit",
				"hrd_recruitment/recruitment_list"=>"hrd_recruitment/add_recruitment,hrd_recruitment/edit_recruitment,hrd_recruitment/view_recruitment",
				"staff/staff_list"=>"staff/add_staff,staff/edit_staff,staff/imports,staff/packlaring",
				"reports/transaction"=>"reports/detail_transaction",
				"reports/sales_menu"=>"reports/get_detail_sales_menu",
				"reports/open_close"=>"reports/open_close_detail",
				"reports/refunds"=>"reports/detail_refund",
				"inventory_convertions"=>"inventory_process/index",
			);
			return isset($menu[$target]) ? $menu[$target] : "";
	 }
   function create_tree(&$list, $parent, $route_url){
     $active_url = $_SERVER['REQUEST_URI'];
    $html_out  = "";
    $tree  = "";
    $return= array() ;
    foreach ($parent as $row){
        $html_out .= '<li>';
        $name = $row->name;
        $url = $route_url.'/'.$row->url;
        $attribute = 'class="sidemenu"';
        $attribute .= 'data-active="'.(stripos($active_url, $url)) !== FALSE ? "active" : "".'"';
        
        if($row->parent_id ==0){
            $name = '<i class="fa fa-bar-chart-o fa-fw"></i>'.$row->name;
        }

        if(isset($list[$row->id])){
            $url = "";
            $attribute .= ' pathname ="'.$url.'"';
            $html_out .= anchor($url,$name.'<span class="fa arrow"></span>',$attribute );
            $html_out .= '<ul class="nav nav-second-level">';

            $temp = $this->create_tree($list, $list[$row->id], $route_url);
            $row->children = $temp['tree'];

            $html_out .= $temp['html_out'];
            $html_out .= '</ul>';
        }
        else{
            $attribute .= ' pathname ="'.$url.'" links="'.$this->get_links($row->url).'"';
            $html_out .= anchor($url, $name ,$attribute);
            $row->children = array();

        }
        $html_out .= '</li>';
        $row->html_out = $html_out;
        $tree[] = $row;

    } 
    $return = array(
        'tree' => $tree,
        'html_out' =>$html_out
        );
    return $return;

    }



        /**
     * __call
     *
     * Acts as a simple way to call model methods without loads of stupid alias'
     *
     **/
        public function __call($method, $arguments)
        {
            if (!method_exists( $this->sidebar_menu_model, $method) )
            {
                throw new Exception('Undefined method ::' . $method . '() called');
            }

            return call_user_func_array( array($this->sidebar_menu_model, $method), $arguments);
        }

    /**
     * __get
     *
     * Enables the use of CI super-global without having to define an extra variable.
     *
     * I can't remember where I first saw this, so thank you if you are the original author. -Militis
     *
     * @access  public
     * @param   $var
     * @return  mixed
     */
    public function __get($var)
    {
        return get_instance()->$var;
    }


}
