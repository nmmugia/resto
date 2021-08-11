<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/15/2014
 * Time: 11:38 AM
 */

/*
| -------------------------------------------------------------------------
| Config
| -------------------------------------------------------------------------
|
|
*/

$config['site_title']           = 'POS';
$config['site_title_delimiter'] = '-';
$config['credits_link']         = 'http://aloha.com';
$config['script_name']          = '';

if (isset($_SERVER['REMOTE_ADDR'])) $config['node_server_ip'] = "http://" . $_SERVER['SERVER_NAME'] . "";
else
    $config['node_server_ip'] = "http://localhost";

$config['node_server_port'] = "4312";

// table size
$config['default_table_width']  = "1000"; //px
$config['default_table_height'] = "800"; //px
$config['printer_address']      = "2:Outlet 1,3:Outlet 2,4:Outlet 3,5:Outlet 4,6:Outlet 4"; //printer kitchen
$config['printer_cashier']      = "Outlet 5"; //printer cashier

// user account to sync database from server
$config['sync_user_username'] = "backdoor";
$config['sync_user_password'] = "1qaz2wsx1@";
$config['sync_user_email']    = "backdoor@mail.com";

// store outlet config
$config['store_id']        = "3";
$config['server_base_url'] = "http://localhost/bosresto_server/";
$config['php_exe_path']    = 'C:\xampp\php\php.exe';

$config['environment']    = ENVIRONMENT;//production or development

//post order menu to ready , handle order menu stack in cashier
$config['72_80'] = 55; // isi dengan 0 / 55
$config['printer_format'] = 1; // isi dengan 1 / 2
//NOTIFICATION
//0 not used notification after action kitchen
//1 use notification after action kitchen
$config['notification']=0;  // 0/1
//VOUCHER METHOD
//1. Based on voucher code
//2. Based on voucher category and quantity use
$config['voucher_method'] = 2;
//BACKGROUND COLOR FOR PRIMARY ORDER , ADDITIONAL ORDER , DINE IN WITH TAKEAWAY ORDER
$config['use_primary_additional_color']=1; // 0 / 1
$config['primary_bg_color']="white";
$config['additional_bg_color']="lightgreen";
$config['takeaway_bg_color']="pink";
//USING RESERVATION ONLINE
$config['use_reservation_online']=0; // 0 / 1
$config['register_reservation_online_store_id']=10;
$config['get_data_reservation_online_url']="http://localhost/bosrestolite/api/reservation/get_online_data";
//TARGET PRINTER PRINT LIST MENU ON CASHIER
//1. Cashier
//2. Checker
$config['target_print_list_menu']=1;
//SETTING PRINTER LOCATION LX DOT MATRIX FOR RECEIVE ORDER IN WAREOHUSE
$config['printer_po_warehouse']="\\\\192.168.1.26\warehouse_printer";
//USER WITH OUTLET != 0 CAN ACCESS STOCK OPNAME AND INVENTORY OUTLET ?
// 1. YES
// 2. NO
$config['outlet_not_zero_can_opname']=0; // 0 / 1
//LAST 4 DIGIT BILL NUMBER USING SEQUENCE OR RANDOM
// 1. SEQUENCE
// 2. RANDOM
$config['bill_auto_number']=2; // 1 / 2
//0 - 100
//WILL SHOW X% TAXES ON REPORT TAXES 
$config['percentage_report_taxes']=100;
//INVENTORY STOCK METHOD
$config['stock_method']="FIFO"; // FIFO / AVERAGE
$config['printer_hrd']="printer_hrd";

$config['version']="v1.1.156";
