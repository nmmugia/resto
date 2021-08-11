<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 2/9/2015
 * Time: 8:59 AM
 */
class Scheduler extends CI_Controller
{
    private $_url;
    public $data = array();

    function __construct()
    {
        parent::__construct();
        $this->load->model('store_model');
        $this->load->model('sync_model');
        $this->load->model('account_data_model');
        $this->load->helper('module');

        $general_setting = $this->store_model->get_general_setting();

        foreach ($general_setting as $key => $row) {
            $this->data['setting'][$row->name] = $row->value;
        }

        $modules= $this->store_model->get("module")->result();
        
        foreach ($modules as $key => $row) {
           $this->data['module'][$row->name] = $row->is_installed;
        }
    }


    public function index($id)
    {
        if (!empty($id)) {
            $form_data = $this->store_model->get_one('server_sync', $id);

            if (!empty($form_data)) {
                $this->{$form_data->controller}($id);
            }
        }
    }

    public function inventory($id)
    {
        $form_data = $this->store_model->get_one('server_sync', $id);
        set_time_limit(0);
        $stock = $this->store_model->get_all_where('stock', array('has_sync' => 0));
        $data = array();

            $url = $form_data->url;
        foreach ($stock as $key => $row) {
            $data['data'] = $row;
            $send['data_json'] = json_encode($data);
            $send['data_md5'] = md5($send['data_json']);
            $url_compl = $url . '_stock/';
            if ($this->curl_connect($send, $url_compl)) {
                $data_update = array('has_sync' => '1');
                $this->store_model->update_where('stock', $data_update, array('id' => $row->id));
				
				// check if sync success
				$form_data = $this->store_model->get_one('stock', $row->id);
				if(isset($form_data->has_sync) && $form_data->has_sync != "1"){
					$this->scheduler_log("DATA", "Failed sync data stock", false);
				}
            }
        }
        $stock_history = $this->store_model->get_all_where('stock_history', array('has_sync' => 0));
        $data = array();
        foreach ($stock_history as $key => $row) {
            $data['data'] = $row;
            $send['data_json'] = json_encode($data);
            $send['data_md5'] = md5($send['data_json']);
            $url_compl = $url . '_stock_history/';
            if ($this->curl_connect($send, $url_compl)) {
                $data_update = array('has_sync' => '1');
                $this->store_model->update_where('stock_history', $data_update, array('id' => $row->id));
				
				// check if sync success
				$form_data = $this->store_model->get_one('stock_history', $row->id);
				if(isset($form_data->has_sync) && $form_data->has_sync != "1"){
					$this->scheduler_log("DATA", "Failed sync data stock history", false);
				}
            }
        }
    }

    public function bill($id)
    {

        $form_data = $this->store_model->get_one('server_sync', $id);
        $time_limit = $form_data->interval * 0.5 * 60;
        set_time_limit($time_limit);

        $url = $form_data->url;

        $data = array();
        $send = array();
        $data['bill_information'] = array();
        $data['bill_payment'] = array();

        $setting = $this->store_model->get_all_where('master_general_setting', array('name' => 'store_id'));
        $bill_data = $this->store_model->get_all_where('bill', array('has_synchronized' => 0));

        foreach ($bill_data as $key => $row) {
            $data['bill_menu'] = array();
            $data['bill_order'] = array();

            $row->store_id = $setting[0]->value;

            $data['bill_order'] = $this->store_model->get_all_where('order', array('id' => $row->order_id));
            $data['bill_information'] = $this->store_model->get_all_where('bill_information', array('bill_id' => $row->id));
            $data['bill_payment'] = $this->store_model->get_all_where('bill_payment', array('bill_id' => $row->id));
            $data['bill_courier_service'] = $this->store_model->get_all_where('bill_courier_service', array('bill_id' => $row->id));

            $temp = $this->store_model->get_all_where('bill_menu', array('bill_id' => $row->id));
            $data['bill_menu'] = array_merge($data['bill_menu'], $temp);

            foreach ($data['bill_menu'] as $key => $menu) {
                $menu->bill_menu_inventory_cogs = array();
                $menu->bill_menu_side_dish = array();

                $temp = $this->store_model->get_all_where('bill_menu_inventory_cogs', array('bill_menu_id' => $menu->id));
                $menu->bill_menu_inventory_cogs = array_merge($menu->bill_menu_inventory_cogs, $temp);

                $temp = $this->store_model->get_all_where('bill_menu_side_dish', array('bill_menu_id' => $menu->id));
                $menu->bill_menu_side_dish = array_merge($menu->bill_menu_side_dish, $temp);

            }

            $data['bill'] = $row;

            if ($this->data['module']['ACCOUNTING'] == 1) {
                $account_data = $this->account_data_model->get_all_where('account_data', array('entry_type' => 3, 'foreign_id' => $row->id, 'has_synchronized' => 0));
                $data['account_data'] = $account_data;
                $account_data_detail = array();
                foreach ($account_data as $a) {
                    $detail = $this->account_data_model->get_all_where('account_data_detail', array('account_data_id' => $a->id, 'has_synchronized' => 0));
                    if (!empty($detail)) {
                        array_push($account_data_detail, $detail);
                        $data['account_data_detail'] = array_pop($account_data_detail);
                    }
                }
            }

            $data_json = json_encode($data);
            $data_md5 = md5($data_json);

            $send['data_md5'] = $data_md5;
            $send['data_json'] = $data_json;
                
            if ($this->curl_connect($send, $url)) {
                $data_update = array('has_synchronized' => '1');
                $this->store_model->update_where('bill', $data_update, array('id' => $row->id));
				// check if sync success
				$form_data = $this->store_model->get_all_where("bill", array('id' => $row->id, 'has_synchronized' => 0));
				if(!empty($form_data)){
					$this->scheduler_log("DATA", "Failed sync data Bill", false);
				}
                if ($this->data['module']['ACCOUNTING'] == 1) {
                    $this->account_data_model->update_where('account_data', $data_update, array('entry_type' => 3, 'foreign_id' => $row->id));
					// check if sync success
					$form_data = $this->account_data_model->get_all_where("account_data", array('entry_type' => 3, 'foreign_id' => $row->id, 'has_synchronized' => 0));
					if(!empty($form_data)){
						$this->scheduler_log("DATA", "Failed sync data Account Data", false);
					}
                    $account_data = $this->account_data_model->get_all_where('account_data', array('entry_type' => 3, 'foreign_id' => $row->id, 'has_synchronized' => 1));
                    foreach ($account_data as $a) {
                        $this->account_data_model->update_where('account_data_detail', $data_update, array('account_data_id' => $a->id));
                    }                    
                }
            }
        }

        $voucher = $this->store_model->get_all_where('voucher', array('status' => 1));
        $send_voucher['data_json'] = json_encode($voucher);
        $send_voucher['data_md5'] = md5($send_voucher['data_json']);
        $url_voucher = $url . '_voucher/';
        $this->curl_connect($send_voucher, $url_voucher);

        $member = $this->store_model->get('member')->result_array();
        $send['data_json'] = json_encode($member);
        $send['data_md5'] = md5($send['data_json']);
        $url_member = $url . '_member/';
        $this->curl_connect($send, $url_member);

        $compliment_usage = $this->store_model->get_all_where('compliment_usage', array('has_synchronized' => 0));
        $send['data_json'] = json_encode($compliment_usage);
        $send['data_md5'] = md5($send['data_json']);
        $url_compl = $url . '_compliment/';
        if ($this->curl_connect($send, $url_compl)) {

            foreach ($compliment_usage as $key => $row) {
                $data_update = array('has_synchronized' => '1');
                $this->store_model->update_where('compliment_usage', $data_update, array('id' => $row->id));
				// check if sync success
				$form_data = $this->store_model->get_all_where("compliment_usage", array('id' => $row->id, 'has_synchronized' => 0));
				if(!empty($form_data)){
					$this->scheduler_log("DATA", "Failed sync data Compliment Usage", false);
				}
            }

        }

        $void = $this->store_model->get_all_where('void', array('has_synchronized' => 0));

        $data = array();
        foreach ($void as $key => $row) {
            $row->store_id = $setting[0]->value;
            $data['data'] = $row;
            $send['data_json'] = json_encode($data);
            $send['data_md5'] = md5($send['data_json']);
            $url_compl = $url . '_void/';
            if ($this->curl_connect($send, $url_compl)) {
                $data_update = array('has_synchronized' => '1');
                $this->store_model->update_where('void', $data_update, array('id' => $row->id));
				// check if sync success
				$form_data = $this->store_model->get_all_where("void", array('id' => $row->id, 'has_synchronized' => 0));
				if(!empty($form_data)){
					$this->scheduler_log("DATA", "Failed sync data Void", false);
				}
            }
        }


        $open_close_cashier = $this->store_model->get_all_where('open_close_cashier',
            array('has_synchronized' => 0, 'status' => 2));
        $data = array();
        foreach ($open_close_cashier as $key => $row) {
            $row->store_id = $setting[0]->value;
            $data['data'] = $row;
            $send['data_json'] = json_encode($data);
            $send['data_md5'] = md5($send['data_json']);
            $detail = $this->store_model->get_all_where("open_close_cashier_detail", array("open_close_cashier_id" => $row->id));
            $send['data_detail'] = json_encode($detail);
            $url_compl = $url . '_open_close_cashier/';
            if ($this->curl_connect($send, $url_compl)) {
                $data_update = array('has_synchronized' => '1');
                $this->store_model->update_where('open_close_cashier', $data_update, array('id' => $row->id));
				// check if sync success
				$form_data = $this->store_model->get_all_where("open_close_cashier", array('id' => $row->id, 'has_synchronized' => 0));
				if(!empty($form_data)){
					$this->scheduler_log("DATA", "Failed sync data Open Close Cashier", false);
				}
            }
        }

        $balance_cash_history = $this->store_model->get_all_where('balance_cash_history', array('has_sync' => 0));
        $data = array();
        foreach ($balance_cash_history as $key => $row) {
            $row->store_id = $setting[0]->value;
            $data['data'] = $row;
            $send['data_json'] = json_encode($data);
            $send['data_md5'] = md5($send['data_json']);
            $url_compl = $url . '_balance_cash_history/';
            if ($this->curl_connect($send, $url_compl)) {
                $data_update = array('has_sync' => '1');
                $this->store_model->update_where('balance_cash_history', $data_update, array('id' => $row->id));
				// check if sync success
				$form_data = $this->store_model->get_all_where("balance_cash_history", array('id' => $row->id, 'has_sync' => 0));
				if(!empty($form_data)){
					$this->scheduler_log("DATA", "Failed sync data Balance Cash History", false);
				}
            }
        }

        $petty_cash = $this->store_model->get_all_where('petty_cash', array('has_sync' => 0));
        $data = array();
        foreach ($petty_cash as $key => $row) {
            $row->store_id = $setting[0]->value;
            $data['data'] = $row;
            $send['data_json'] = json_encode($data);
            $send['data_md5'] = md5($send['data_json']);
            $url_compl = $url . '_petty_cash/';
            if ($this->curl_connect($send, $url_compl)) {
                $data_update = array('has_sync' => '1');
                $this->store_model->update_where('petty_cash', $data_update, array('id' => $row->id));
				// check if sync success
				$form_data = $this->store_model->get_all_where("petty_cash", array('id' => $row->id, 'has_sync' => 0));
				if(!empty($form_data)){
					$this->scheduler_log("DATA", "Failed sync data Petty Cash", false);
				}
            }
        }

        $refund = $this->store_model->get_all_where('refund', array('has_sync' => 0));
        $data = array();
        if (!empty($refund)) {
            foreach ($refund as $key => $row) {
                $row->store_id = $setting[0]->value;
                $data['data'] = $row;
                $send['data_json'] = json_encode($data);
                $send['data_md5'] = md5($send['data_json']);
                $url_compl = $url . '_refund/';
                if ($this->curl_connect($send, $url_compl)) {
                    $data_update = array('has_sync' => '1');
                    $this->store_model->update_where('refund', $data_update, array('id' => $row->id));
					// check if sync success
					$form_data = $this->store_model->get_all_where("refund", array('id' => $row->id, 'has_sync' => 0));
					if(!empty($form_data)){
						$this->scheduler_log("DATA", "Failed sync data Refund", false);
					}
                }
            }
        }            

        $order_history = $this->store_model->get_all_where('order_history', array('has_sync' => 0));
        $data = array();
        foreach ($order_history as $key => $row) {
            $row->store_id = $setting[0]->value;
            $data['data'] = $row;
            $send['data_json'] = json_encode($data);
            $send['data_md5'] = md5($send['data_json']);
            $url_compl = $url . '_order_history/';
            if ($this->curl_connect($send, $url_compl)) {
                $data_update = array('has_sync' => '1');
                $this->store_model->update_where('order_history', $data_update, array('id' => $row->id));
				// check if sync success
				$form_data = $this->store_model->get_all_where("order_history", array('id' => $row->id, 'has_sync' => 0));
				if(!empty($form_data)){
					$this->scheduler_log("DATA", "Failed sync data Order History", false);
				}
            }
        }

        $reservation = $this->store_model->get_all_where('reservation', array('has_sync' => 0));
        $data = array();
        foreach ($reservation as $key => $row) {
            $row->store_id = $setting[0]->value;
            $data['data'] = $row;
            $send['data_json'] = json_encode($data);
            $send['data_md5'] = md5($send['data_json']);
            $url_compl = $url . '_reservation/';
            if ($this->curl_connect($send, $url_compl)) {
                $data_update = array('has_sync' => '1');
                $this->store_model->update_where('reservation', $data_update, array('id' => $row->id));
				// check if sync success
				$form_data = $this->store_model->get_all_where("reservation", array('id' => $row->id, 'has_sync' => 0));
				if(!empty($form_data)){
					$this->scheduler_log("DATA", "Failed sync data Reservation", false);
				}
            }
        }

        $backoffice_expenses = $this->store_model->get_all_where('backoffice_expenses', array('has_sync' => 0));
        $data = array();
        foreach ($backoffice_expenses as $key => $row) {
            $row->store_id = $setting[0]->value;
            $data['data'] = $row;
            $send['data_json'] = json_encode($data);
            $send['data_md5'] = md5($send['data_json']);
            $url_compl = $url . '_backoffice_expenses/';
            if ($this->curl_connect($send, $url_compl)) {
                $data_update = array('has_sync' => '1');
                $this->store_model->update_where('backoffice_expenses', $data_update, array('id' => $row->id));
				// check if sync success
				$form_data = $this->store_model->get_all_where("backoffice_expenses", array('id' => $row->id, 'has_sync' => 0));
				if(!empty($form_data)){
					$this->scheduler_log("DATA", "Failed sync data Backoffice Expenses", false);
				}
            }
        }
    }

    public function purchase_order($id)
    {
        $this->load->model('purchase_order_receive_detail_model', 'receive_detail');
        $this->load->model('purchase_order_retur_detail_model', 'retur_detail');
        $form_data = $this->store_model->get_one('server_sync', $id);
        $url = $form_data->url;

        $cond = array('has_synchronized' => 0);
        $po_data = $this->store_model->get_all_where('purchase_order', $cond);
        foreach ($po_data as $key => $po) {
            $data['purchase_order'] = $po;
            $data['purchase_order_detail'] = $this->store_model->get_all_where('purchase_order_detail', array('purchase_order_id' => $po->id));

            if ($this->data['module']['ACCOUNTING'] == 1) {
                $account_data = $this->account_data_model->get_all_where('account_data', array('entry_type' => 2, 'foreign_id' => $po->id, 'has_synchronized' => 0));
                $data['account_data'] = $account_data;
                $account_data_detail = array();
                foreach ($account_data as $acc) {
                    $detail = $this->account_data_model->get_all_where('account_data_detail', array('account_data_id' => $acc->id, 'has_synchronized' => 0));
                    if (!empty($detail)) {
                        array_push($account_data_detail, $detail);
                        $data['account_data_detail'] = array_pop($account_data_detail);
                    }
                }
            }

            $data_json = json_encode($data);
            $data_md5 = md5($data_json);

            $send['data_json'] = $data_json;
            $send['data_md5'] = $data_md5;

            $result = (array)$this->curl_connect($send, $url);

            if ($result) {
                $data_update = array('has_synchronized' => 1);
                $this->store_model->update_where('purchase_order', $data_update, array('id' => $po->id));
                $this->store_model->update_where('purchase_order_detail', $data_update, array('purchase_order_id' => $po->id));

                if ($this->data['module']['ACCOUNTING'] == 1) {
                    $this->account_data_model->update_where('account_data', $data_update, array('foreign_id' => $po->id));
                    $account_data = $this->account_data_model->get_all_where('account_data', array('entry_type' => 2, 'foreign_id' => $po->id, 'has_synchronized' => 1));
                    foreach ($account_data as $acc) {
                        $this->account_data_model->update_where('account_data_detail', $data_update, array('account_data_id' => $po->id));
                    }
                }
            }
        }

        $por_data = $this->store_model->get_all_where('purchase_order_receive', $cond);
        foreach ($por_data as $key => $por) {
            $image_name = $por->invoice_logo;
            if (!empty($image_name)) {
                unset($por->invoice_logo);
                // alternatively specify an URL, if PHP settings allow
                $imagedata = file_get_contents($image_name);
                $base64 = base64_encode($imagedata);
                $temp = array("image_name" => $image_name, "image_data" => $base64);
                $por->invoice_logo = $temp;
            }
            $data['purchase_order_receive'] = $por;
            $data['purchase_order_receive_detail'] = $this->receive_detail->get(array('purchase_order_receive_id' => $por->id));
            $data['purchase_order_detail'] = $this->store_model->get_all_where('purchase_order_detail', array('purchase_order_id' => $por->purchase_order_id));
            $get_po = array_pop($this->store_model->get_all_where('purchase_order', array('id' => $por->purchase_order_id)));
            $data['po_number'] = $get_po->number;

            if ($this->data['module']['ACCOUNTING'] == 1) {
                $account_data = $this->account_data_model->get_all_where('account_data', array('entry_type' => 5, 'foreign_id' => $por->id, 'has_synchronized' => 0));
                $data['account_data'] = $account_data;
                $account_data_detail = array();
                foreach ($account_data as $acc) {
                    $detail = $this->account_data_model->get_all_where('account_data_detail', array('account_data_id' => $acc->id, 'has_synchronized' => 0));
                    if (!empty($detail)) {
                        array_push($account_data_detail, $detail);
                        $data['account_data_detail'] = array_pop($account_data_detail);
                    }
                }
            }

            $data_json = json_encode($data);
            $data_md5 = md5($data_json);

            $send['data_json'] = $data_json;
            $send['data_md5'] = $data_md5;

            $result = (array)$this->curl_connect($send, $url.'_receive');

            if ($result) {
                $data_update = array('has_synchronized' => 1);
                $this->store_model->update_where('purchase_order_receive', $data_update, array('id' => $por->id));
                $this->store_model->update_where('purchase_order_receive_detail', $data_update, array('purchase_order_receive_id' => $por->id));

				// check if sync success
				$form_data = $this->store_model->get_one("purchase_order_receive", $por->id);
				if(isset($form_data->has_synchronized) && $form_data->has_synchronized != "1"){
					$this->scheduler_log("DATA", "Failed sync data Puchase Order Receive", false);
				}
				
                if ($this->data['module']['ACCOUNTING'] == 1) {
                    $this->account_data_model->update_where('account_data', $data_update, array('foreign_id' => $por->id));
                    $account_data = $this->account_data_model->get_all_where('account_data', array('entry_type' => 5, 'foreign_id' => $por->id, 'has_synchronized' => 1));
                    foreach ($account_data as $acc) {
                        $this->account_data_model->update_where('account_data_detail', $data_update, array('account_data_id' => $por->id));
                    }
					
					// check if sync success
					$form_data = $this->account_data_model->get_all_where("account_data", array('foreign_id' => $por->id, 'has_synchronized' => 0));
					if(!empty($form_data)){
						$this->scheduler_log("DATA", "Failed sync data Account Data", false);
					}
                }
            }
        }
    }

    public function account_data($id)
    {
        $this->load->model('account_data_model');
        $form_data = $this->store_model->get_one('server_sync', $id);
        $url = $form_data->url;
        $return = true;
        $account_data = $this->account_data_model->get_all_where("account_data", array("has_synchronized" => 0));
        foreach ($account_data as $a) {
            $account_data_detail = $this->account_data_model->get_all_where("account_data_detail", array("account_data_id" => $a->id));
            $data = array(
                "account_data" => $a,
                "account_data_detail" => $account_data_detail
            );
            $send = array();
            $json = json_encode($data);
            $md5 = md5($json);
            $send['data_json'] = $json;
            $send['data_md5'] = $md5;
            $result = $this->curl_connect($send, $url);
            if ($result) {
                //UPDATE LOCAL DATA HAS SYNCRONIZED
                $this->account_data_model->save("account_data", array("has_synchronized" => 1), $a->id);
                $this->account_data_model->update_where("account_data_detail", array("has_synchronized" => 1), array("account_data_id" => $a->id));
				
				// check if sync success
				$form_data = $this->account_data_model->get_one("account_data", $a->id);
				if(isset($form_data->has_synchronized) && $form_data->has_synchronized != "1"){
					$this->scheduler_log("DATA", "Failed sync data Account Data", false);
				}
            }
        }

        echo json_encode($return);
    }

    //BUILD DATA APPRAISAL PROCESS,DETAIL,DETAIL CATEGORY AND THEN SEND DATA TO SERVER
    public function appraisal_process($id = 0)
    {
        $this->load->model("sync_model");
        $this->load->model("hrd_model");
        $form_data = $this->store_model->get_one('server_sync', $id);
        $url = $form_data->url . "/appraisal_process";
        //GET PROCESS APPRAISAL WHERE DATA NOT YET SYNCHRONING
        $process_appraisal = $this->hrd_model->get_all_where("hr_appraisal_process", array("has_sync" => 0));
        $return = true;
        foreach ($process_appraisal as $a) {
            $process_appraisal_detail = $this->hrd_model->get_all_where("hr_appraisal_process_detail", array("appraisal_process_id" => $a->id));
            $data = array(
                "process_appraisal" => $a,
                "process_appraisal_detail" => $process_appraisal_detail,
                "process_appraisal_detail_category" => array(),
            );
            foreach ($process_appraisal_detail as $b) {
                $process_appraisal_detail_category = $this->hrd_model->get_all_where("hr_appraisal_process_detail_category", array("appraisal_process_detail_id" => $b->id));
                $data['process_appraisal_detail_category'] = array_merge($data['process_appraisal_detail_category'], $process_appraisal_detail_category);
            }
            $send = array();
            $json = json_encode($data);
            $md5 = md5($json);
            $send['data_json'] = $json;
            $send['data_md5'] = $md5;
            $result = $this->curl_connect($send, $url);
            if ($result) {
                //UPDATE LOCAL DATA HAS SYNCRONIZED
                $this->hrd_model->save("hr_appraisal_process", array("has_sync" => 1), $a->id);
				// check if sync success
				$form_data = $this->hrd_model->get_one("hr_appraisal_process", $a->id);
				if(isset($form_data->has_sync) && $form_data->has_sync != "1"){
					$this->scheduler_log("DATA", "Failed sync data HR Appraisal Process", false);
				}
            }
        }
        return json_encode($return);
    }

    //BUILD DATA AUDIT PROCESS,DETAIL,DETAIL CATEGORY AND THEN SEND DATA TO SERVER
    public function audit_process($id = 0)
    {
        $this->load->model("sync_model");
        $this->load->model("hrd_model");
        $form_data = $this->store_model->get_one('server_sync', $id);
        $url = $form_data->url . "/audit_process";
        //GET PROCESS AUDIT WHERE DATA NOT YET SYNCHRONING
        $process_audit = $this->hrd_model->get_all_where("hr_audit_process", array("has_sync" => 0));
        $return = true;
        foreach ($process_audit as $a) {
            $process_audit_detail = $this->hrd_model->get_all_where("hr_audit_process_detail", array("audit_process_id" => $a->id));
            $data = array(
                "process_audit" => $a,
                "process_audit_detail" => $process_audit_detail,
                "process_audit_detail_category" => array(),
            );
            foreach ($process_audit_detail as $b) {
                $process_audit_detail_category = $this->hrd_model->get_all_where("hr_audit_process_detail_category", array("audit_process_detail_id" => $b->id));
                $data['process_audit_detail_category'] = array_merge($data['process_audit_detail_category'], $process_audit_detail_category);
            }
            $send = array();
            $json = json_encode($data);
            $md5 = md5($json);
            $send['data_json'] = $json;
            $send['data_md5'] = $md5;
            $result = $this->curl_connect($send, $url);
            if ($result) {
                //UPDATE LOCAL DATA HAS SYNCRONIZED
                $this->hrd_model->save("hr_audit_process", array("has_sync" => 1), $a->id);
				// check if sync success
				$form_data = $this->hrd_model->get_one("hr_audit_process", $a->id);
				if(isset($form_data->has_sync) && $form_data->has_sync != "1"){
					$this->scheduler_log("DATA", "Failed sync data HR Audit Process", false);
				}
            }
        }
        return json_encode($return);
    }

    //BUILD DATA REIMBURSE AND THEN SEND DATA TO SERVER
    public function reimburse($id = 0)
    {
        $this->load->model("sync_model");
        $this->load->model("hrd_model");
        $form_data = $this->store_model->get_one('server_sync', $id);
        $url = $form_data->url . "/reimburse";
        //GET REIMBURSE WHERE DATA NOT YET SYNCHRONING
        $reimburse = $this->hrd_model->get_all_where("hr_reimburse", array("has_sync" => 0));
        $send = array();
        $json = json_encode($reimburse);
        $md5 = md5($json);
        $send['data_json'] = $json;
        $send['data_md5'] = $md5;
        $result = $this->curl_connect($send, $url);
        $return = true;
        if ($result) {
            //UPDATE LOCAL DATA HAS SYNCRONIZED
            foreach ($reimburse as $a) {
                $this->hrd_model->save("hr_reimburse", array("has_sync" => 1), $a->id);
				// check if sync success
				$form_data = $this->hrd_model->get_one("hr_reimburse", $a->id);
				if(isset($form_data->has_sync) && $form_data->has_sync != "1"){
					$this->scheduler_log("DATA", "Failed sync data HR Reimburse", false);
				}
            }
        } else {
            $return = false;
        }
        return json_encode($return);
    }

    //BUILD DATA ATTENDANCE AND THEN SEND DATA TO SERVER
    public function attendance($id = 0)
    {
        $this->load->model("sync_model");
        $this->load->model("hrd_model");
        $form_data = $this->store_model->get_one('server_sync', $id);
        $url = $form_data->url . "/attendance";
        //GET ATTENDANCE WHERE DATA NOT YET SYNCHRONING
        $attendances = $this->hrd_model->get_all_where("hr_attendances", array("has_sync" => 0));
        $histories = $this->hrd_model->get_all_where("hr_history_fingerprint", array("has_sync" => 0));



        $data = array(
            "store_id" => $this->data['setting']['store_id'],
            "attendances" => $attendances,
            "histories" => $histories,
        );


        $file_data_arr = array();
        for ($i=0; $i < sizeof($attendances); $i++) { 



            $value = $attendances[$i];
            $file_name = $value->attachment;

            if (!empty($file_name)){

            $file_data = file_get_contents($file_name);
             // alternatively specify an URL, if PHP settings allow
            $base64 = base64_encode($file_data);
            $temp = array("file_name" => $file_name, "file_data" => $base64);
            array_push($file_data_arr, $temp);

        }

        }

        $data['file_send'] = $file_data_arr;

        

        $send = array();
        $json = json_encode($data);
        $md5 = md5($json);
        $send['data_json'] = $json;
        $send['data_md5'] = $md5;
        $result = $this->curl_connect($send, $url);
        $return = true;
        if ($result) {

            $this->hrd_model->update_where("hr_attendances", array("has_sync" => 1), array("has_sync" => 0));
            $this->hrd_model->update_where("hr_history_fingerprint", array("has_sync" => 1), array("has_sync" => 0));
			// check if sync success
			$form_data = $this->hrd_model->get_all_where("hr_attendances", array("has_sync" => 0));
			if(!empty($form_data)){
				$this->scheduler_log("DATA", "Failed sync data HR Attendances", false);
			}
			$form_data = $this->hrd_model->get_all_where("hr_history_fingerprint", array("has_sync" => 0));
			if(!empty($form_data)){
				$this->scheduler_log("DATA", "Failed sync data HR History Fingerprint", false);
			}
        } else {
            $return = false;
        }
        return json_encode($return);
    }

    //BUILD DATA Office Hours AND THEN SEND DATA TO SERVER
     public function office_hours($id = 0)
    {
        $this->load->model("sync_model");
        $this->load->model("hrd_model");
        $form_data = $this->store_model->get_one('server_sync', $id);
        $url = $form_data->url . "/office_hours";
        //GET office_hours WHERE DATA NOT YET SYNCHRONING
        $office_hours = $this->hrd_model->get_all_where("hr_office_hours", array("has_sync" => 0));

        $data = array(
            "store_id" => $this->data['setting']['store_id'],
            "office_hours" => $office_hours,
        );

        $send = array();
        $json = json_encode($data);
        $md5 = md5($json);
        $send['data_json'] = $json;
        $send['data_md5'] = $md5;
        $result = $this->curl_connect($send, $url);
        $return = true;
        if ($result) {
            $this->hrd_model->update_where("hr_office_hours", array("has_sync" => 1), array("has_sync" => 0));
			// check if sync success
			$form_data = $this->hrd_model->get_all_where("hr_office_hours", array("has_sync" => 0));
			if(!empty($form_data)){
				$this->scheduler_log("DATA", "Failed sync data HR Office Hours", false);
			}
        } else {
            $return = false;
        }
        return json_encode($return);
    }
    //BUILD DATA hr_setting AND THEN SEND DATA TO SERVER
     public function setting_hr($id = 0)
    {
        $this->load->model("sync_model");
        $this->load->model("hrd_model");
        $form_data = $this->store_model->get_one('server_sync', $id);
        $url = $form_data->url . "/setting_hr";
        //GET office_hours WHERE DATA NOT YET SYNCHRONING
        $setting_hr = $this->hrd_model->get_all_where("hr_setting", array("has_sync" => 0));

        $data = array(
            "store_id" => $this->data['setting']['store_id'],
            "setting_hr" => $setting_hr,
        );

        $send = array();
        $json = json_encode($data);
        $md5 = md5($json);
        $send['data_json'] = $json;
        $send['data_md5'] = $md5;
        $result = $this->curl_connect($send, $url);
        $return = true;
        if ($result) {
            $this->hrd_model->update_where("hr_setting", array("has_sync" => 1), array("has_sync" => 0));
			// check if sync success
			$form_data = $this->hrd_model->get_all_where("hr_setting", array("has_sync" => 0));
			if(!empty($form_data)){
				$this->scheduler_log("DATA", "Failed sync data HR Setting", false);
			}
        } else {
            $return = false;
        }
        return json_encode($return);
    }

    //BUILD DATA LOAN AND THEN SEND DATA TO SERVER
    public function loan($id = 0)
    {
        $this->load->model("sync_model");
        $this->load->model("hrd_model");
        $form_data = $this->store_model->get_one('server_sync', $id);
        $url = $form_data->url . "/loan";
        //GET LOAN WHERE DATA NOT YET SYNCHRONING
        $loans = $this->hrd_model->get_all_where("hr_loan", array("has_sync" => 0));
        $repayments = $this->hrd_model->get_all_where("hr_repayments", array("has_sync" => 0));
        $data = array(
            "store_id" => $this->data['setting']['store_id'],
            "loans" => $loans,
            "repayments" => $repayments,
        );
        $send = array();
        $json = json_encode($data);
        $md5 = md5($json);
        $send['data_json'] = $json;
        $send['data_md5'] = $md5;
        $result = $this->curl_connect($send, $url);
        $return = true;
        if ($result) {
            $this->hrd_model->update_where("hr_loan", array("has_sync" => 1), array("has_sync" => 0));
            $this->hrd_model->update_where("hr_repayments", array("has_sync" => 1), array("has_sync" => 0));
			// check if sync success
			$form_data = $this->hrd_model->get_all_where("hr_loan", array("has_sync" => 0));
			if(!empty($form_data)){
				$this->scheduler_log("DATA", "Failed sync data HR Loan", false);
			}
			$form_data = $this->hrd_model->get_all_where("hr_repayments", array("has_sync" => 0));
			if(!empty($form_data)){
				$this->scheduler_log("DATA", "Failed sync data HR Repayments", false);
			}
        } else {
            $return = false;
        }
        return json_encode($return);
    }

    //BUILD DATA PAYROLL AND THEN SEND DATA TO SERVER
    public function payroll($id = 0)
    {
        $this->load->model("sync_model");
        $this->load->model("hrd_model");
        $form_data = $this->store_model->get_one('server_sync', $id);
        $url = $form_data->url . "/payroll";
        //GET PAYROLL WHERE DATA NOT YET SYNCHRONING
        $payroll = $this->hrd_model->get_all_where("hr_payroll_history", array("has_sync" => 0));
        $detail_payroll = $this->hrd_model->get_all_where("hr_detail_payroll_history", array("has_sync" => 0));
        $job_history = $this->hrd_model->get_all_where("hr_jobs_history", array("has_sync" => 0));
        $data = array(
            "store_id" => $this->data['setting']['store_id'],
            "payroll" => $payroll,
            "detail_payroll" => $detail_payroll,
            "job_history" => $job_history,
        );
        $send = array();
        $json = json_encode($data);
        $md5 = md5($json);
        $send['data_json'] = $json;
        $send['data_md5'] = $md5;
        $result = $this->curl_connect($send, $url);
        $return = true;
        if ($result) {
            $this->hrd_model->update_where("hr_payroll_history", array("has_sync" => 1), array("has_sync" => 0));
            $this->hrd_model->update_where("hr_detail_payroll_history", array("has_sync" => 1), array("has_sync" => 0));
            $this->hrd_model->update_where("hr_jobs_history", array("has_sync" => 1), array("has_sync" => 0));
			// check if sync success
			$form_data = $this->hrd_model->get_all_where("hr_payroll_history", array("has_sync" => 0));
			if(!empty($form_data)){
				$this->scheduler_log("DATA", "Failed sync data HR Payroll History", false);
			}
			$form_data = $this->hrd_model->get_all_where("hr_detail_payroll_history", array("has_sync" => 0));
			if(!empty($form_data)){
				$this->scheduler_log("DATA", "Failed sync data HR Detail Payrll History", false);
			}
			$form_data = $this->hrd_model->get_all_where("hr_jobs_history", array("has_sync" => 0));
			if(!empty($form_data)){
				$this->scheduler_log("DATA", "Failed sync data HR Job History", false);
			}
        } else {
            $return = false;
        }
        return json_encode($return);
    }

    //BUILD DATA SCHEDULE AND THEN SEND DATA TO SERVER
    public function schedule($id = 0)
    {
        $this->load->model("sync_model");
        $this->load->model("hrd_model");
        $form_data = $this->store_model->get_one('server_sync', $id);
        $url = $form_data->url . "/schedule";
        //GET SCHEDULE WHERE DATA NOT YET SYNCHRONING
        $schedule = $this->hrd_model->get_all_where("hr_schedules", array("has_sync" => 0));
        $schedule_detail = $this->hrd_model->get_all_where("hr_schedule_detail", array("has_sync" => 0));
        $data = array(
            "store_id" => $this->data['setting']['store_id'],
            "schedule" => $schedule,
            "schedule_detail" => $schedule_detail,
        );
        $send = array();
        $json = json_encode($data);
        $md5 = md5($json);
        $send['data_json'] = $json;
        $send['data_md5'] = $md5;
        $result = $this->curl_connect($send, $url);
        $return = true;
        if ($result) {
            $this->hrd_model->update_where("hr_schedules", array("has_sync" => 1), array("has_sync" => 0));
            $this->hrd_model->update_where("hr_schedule_detail", array("has_sync" => 1), array("has_sync" => 0));
			// check if sync success
			$form_data = $this->hrd_model->get_all_where("hr_schedules", array("has_sync" => 0));
			if(!empty($form_data)){
				$this->scheduler_log("DATA", "Failed sync data HR Schedule", false);
			}
			$form_data = $this->hrd_model->get_all_where("hr_schedule_detail", array("has_sync" => 0));
			if(!empty($form_data)){
				$this->scheduler_log("DATA", "Failed sync data HR Schedule Detail", false);
			}	
        } else {
            $return = false;
        }
        return json_encode($return);
    }

    public function hrd($id = 0)
    {
        $this->appraisal_process($id);
        $this->audit_process($id);
        $this->reimburse($id);
        $this->attendance($id);
        $this->office_hours($id);
        $this->setting_hr($id);
        $this->loan($id);
        $this->payroll($id);
        $this->schedule($id);
        echo json_encode(true);
    }

    function curl_connect($data, $url)
    {
        //open connection
        $ch = curl_init();

        curl_setopt_array($ch, array(CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => 'Api Post Scheduler',
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $data));

        //execute post
        $result = curl_exec($ch);
        /* Check HTTP Code */
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		$error = curl_error($ch);
        //close connection
        curl_close($ch);
        /* 200 Response! */
         
        $result = json_decode($result);

        if ($status == 200) {
            if (empty($result->status) || !$result->status) {
				$errorLog = array(
					"data" => $data,
					"url" => $url,
				);
				$this->scheduler_log("FAILED", $result->msg, (string) json_encode($errorLog));
				return FALSE;
			}
			
            if ($result->status === TRUE) {
                //var_dump($result->status);    
                return TRUE;
            }

            if ($result->status === 'data') {
                //var_dump($result->data);               
                return $result->data;
            }
        } else {
			$errorLog = array(
				"data" => $data,
				"url" => $url,
			);
			$error = (empty($error) ? "Not valid URL" : $error);
			$this->scheduler_log("CONNECTION", $error, json_encode($errorLog));
        }

        return FALSE;
    }
	
	public function tesLog(){
		$url_compl = "http://localhost/bosresto_server/scheduler/api";
		$send = array();
		$return = $this->curl_connect($send, $url_compl);
		echo "<pre>";
		print_r($return);
	}
	
	public function scheduler_log($type = "error", $message = false, $data = false){
		$filename = "scheduler-".date("Y-m-d").".csv";
		$delimiter = ";";
		$path = FCPATH."application\logs\\";
		$fullpath = $path.$filename;
		
		if(!file_exists($fullpath)){
			//Something to write to txt log
			$log = "sep=".$delimiter.PHP_EOL;
			$log .= "Date".$delimiter."Type".$delimiter."Message".$delimiter."Data".PHP_EOL;
			file_put_contents($fullpath, $log, FILE_APPEND | LOCK_EX);
		}
		$textlog = date("Y-m-d H:i:s").$delimiter.$type.$delimiter.$message.$delimiter.$data.PHP_EOL;
		file_put_contents($fullpath, $textlog, FILE_APPEND);
	}
}