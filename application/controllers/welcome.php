<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

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
  function optimization()
  {
    /*
      ALTER TABLE order_menu ADD INDEX `order_menu_index` (`id`,`order_id`, `menu_id`, `cooking_status`, `is_check`) USING BTREE ;
      ALTER TABLE order_menu_inventory_cogs ADD INDEX `order_menu_inventory_cogs_index` (`order_menu_id`, `inventory_id`, `uom_id`) USING BTREE ;
      ALTER TABLE `order_menu_option` ADD INDEX `order_menu_option_index` (`order_menu_id`, `menu_option_value_id`) USING BTREE;
      ALTER TABLE `menu_ingredient` DROP INDEX `FK_menu_ingredient_menu` ,ADD INDEX `FK_menu_ingredient_menu` (`menu_id`, `inventory_id`, `uom_id`) USING BTREE ;
      ALTER TABLE `order` ADD INDEX `order_index` (`id`,`table_id`, `customer_name`, `delivery_cost_id`, `reservation_id`) USING BTREE ;
      ALTER TABLE `inventory` ADD INDEX `inventory_index` (`name`, `uom_id`) USING BTREE ;
      ALTER TABLE `reservation` ADD INDEX `reservation_index` (`customer_name`, `table_id`, `order_id`) USING BTREE ;
      ALTER TABLE `users` ADD INDEX `users_index` (`id`, `outlet_id`, `pin`) USING BTREE ;
      ALTER TABLE `stock` ADD INDEX `stock_index` (`id`, `store_id`, `outlet_id`, `inventory_id`, `uom_id`) USING BTREE ;
      ALTER TABLE `stock_history` ADD INDEX `stock_history_index` (`id`, `store_id`, `outlet_id`, `inventory_id`, `uom_id`) USING BTREE ;
      ALTER TABLE `inventory_compositions` ADD INDEX `inventory_composition` (`id`, `parent_inventory_id`, `inventory_id`, `uom_id`) USING BTREE ;
      ALTER TABLE `inventory_convertion` ADD INDEX `inventory_convertion_index` (`id`, `store_id`, `inventory_id`, `uom_id`) USING BTREE ;
      ALTER TABLE `feature` ADD INDEX `feature_index` (`id`, `name`, `key`, `users_unlock`) USING BTREE ;
      ALTER TABLE `master_general_setting` ADD INDEX `master_general_setting_index` (`id`, `name`, `value`) USING BTREE ;
      ALTER TABLE `groups` ADD INDEX `groups_index` (`id`, `name`) USING BTREE ;
    */
  }
  public function clear_data()
  {
    // //SET GLOBAL sql_mode='NO_AUTO_VALUE_ON_ZERO'
    //  $this->db->query("SET sql_mode='NO_AUTO_VALUE_ON_ZERO';");
    // $this->db->query("SET FOREIGN_KEY_CHECKS=0;");
    // $tables="account_data,account_data_detail,bill,bill_information,bill_menu,bill_menu_inventory_cogs,bill_menu_side_dish,bill_payment,category";
    // $tables.=",compliment,compliment_store,compliment_usage,discount,enum_delivery_cost,extra_charge,feature_access,users,users_groups";
    // $tables.=",floor,general_entries,general_setting,inventory,inventory_history,inventory_opname,inventory_report,inventory_stock,inventory_stock_transaction,login_attempts";
    // $tables.=",member,member_category,menu,menu_ingredient,menu_option,menu_option_value,menu_promo,menu_promo_side_dish,menu_side_dish,notification";
    // $tables.=",open_close_cashier,order,order_company,order_menu,order_menu_inventory_cogs,order_menu_option,order_menu_side_dish,outlet,promo_cc,promo_cc_category";
    // $tables.=",promo_discount,promo_discount_category,promo_schedule,purchase_order,purchase_order_detail,purchase_order_receive,purchase_order_receive_detail,reservation";
    // $tables.=",server_sync,setting_mealtime,side_dish,side_dish_ingredient,stock,stock_history,stock_opname_history,stock_request,stock_request_detail,stock_request_fifo_detail,stock_transfer_history";
    // $tables.=",store,supplier,table,table_merge,taxes,template_reservation_note,transfer_menu_history,void,voucher,voucher_availability,voucher_group";
    // $tables.=",petty_cash,refund";
    // foreach(explode(",",$tables) as $t){
    //   $this->db->query("DELETE FROM `$t`");
    // }
    // $this->db->query("SET FOREIGN_KEY_CHECKS=1;");
  }
	public function index()
	{
        $this->load->model('options_model');
        $this->load->model('order_model');
        $this->load->model('ion_auth_model');

        echo "<pre>";

        $start_week = strtotime("last monday midnight");
        $end_week = strtotime("+1 week",$start_week);
       
        // $today = date("Y-m-d",$end_week);

        $last_month = strtotime("first day of this month");
        $next_month = strtotime("last day of this month");

        $yest = strtotime("-1 days");
        $today = strtotime("today");

         $start_week = date("Y-m-d",$yest);
        $end_week = date("Y-m-d",$today);

    	$book_date = strtotime('2015-09-08 15:08');
        $from_time = strtotime("+12 hours", now()) ;


    	// $password = $this->ion_auth_model->get_user_by_password('backdoor');
     //                var_dump($password);

	}
  /*
    delete from bill;
delete from bill_information;
delete from bill_menu;
delete from bill_menu_inventory_cogs;
delete from bill_menu_side_dish;
delete from bill_payment;
delete from `order`;
delete from order_menu;
delete from order_menu_inventory_cogs;
delete from order_menu_option;
delete from order_menu_side_dish;
delete from account_data;
delete from account_data_detail;
delete from stock;
delete from stock_history;
delete from inventory;
delete from inventory_uoms;
delete from inventory_compositions;
delete from inventory_convertion;
delete from inventory_history;
delete from inventory_opname;
delete from inventory_process;
delete from inventory_stock;
delete from inventory_stock_transaction;
delete from inventory_report;
delete from menu;
delete from menu_ingredient;
delete from menu_option;
delete from menu_option_value;
delete from menu_promo;
delete from menu_promo_side_dish;
delete from menu_side_dish;
delete from category;
delete from discount;
delete from floor;
delete from `table`;
delete from petty_cash;
delete from refund;
delete from reservation;
delete from purchase_order;
delete from purchase_order_detail;
delete from purchase_order_receive;
delete from purchase_order_receive_detail;
delete from supplier;
delete from transfer_menu_history;
delete from void;
delete from template_global;
delete from template_reservation_note;

*/

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */