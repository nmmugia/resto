<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

     function list_order_payment($order_payment)
    {
        $ci=& get_instance();
				$ci->load->model("store_model");
				$general_setting= $ci->store_model->get_general_setting();
				$setting=array();
        foreach ($general_setting as $key => $row) {
           $setting[$row->name] = $row->value;
        }
        $order_list_data = '';
        $cooking_time = '';
        $order_bill_data = '';
        $start_cooking = '';
        $end_cooking = '';
        if (! empty($order_payment)) {
            foreach ($order_payment['order_list'] as $order) {
                $start_cooking = $order->start_cooking;
                $end_cooking = $order->end_cooking;
                $background_color="";
                if($setting["use_primary_additional_color"]==1 && isset($order->is_additional)){
                  if($order->is_additional==0){
                    $background_color="background-color:".$setting["primary_bg_color"].";";
                  }else{
                    if($order->dinein_takeaway==0){
                      $background_color="background-color:".$setting["additional_bg_color"].";";
                    }else{
                      $background_color="background-color:".$setting["takeaway_bg_color"].";";
                    }
                  }
                }
                $order_list_data .= '<tr class="tOrder" style="'.$background_color.'">';
                $order_list_data .= '<td class="border-side status_menu_order" style="padding-left:10px;" id="status_menu_'. $order->order_menu_id .'">' . $order->cooking_status_name. '</td>';
                $order_list_data .= '<td>';
                $order_list_data .= $order->menu_name;
                if(sizeof($order->option_list) != 0)
                {
                    foreach ($order->option_list as $opt) {
                        $order_list_data .= ' <br/>(' . $opt->option_name .' - '. $opt->option_value_name . ')';
                    }
                }
                else
                {
                    $order_list_data .= "";
                }
                
                foreach ($order->side_dish_list as $sdh ) {
                    $order_list_data .= '<br/>-- ' . $sdh->name . ' ('. $sdh->origin_price .')';
                   
                }

                // $order_list_data .= (! empty($order->option_list) ? ' <br/>(' . $order->option_list->option_name - $order->option_list->option_value_name . ')' : '');
                $order_list_data .= '</td>';
                $order_list_data .= '<td class="border-side tb-align-right">' . $order->quantity . '</td>';
                $order_list_data .= '<td class="tb-align-right" style="padding-right: 10px">Rp ' . number_format($order->menu_price,0,"","."). '</td>';
                $order_list_data .= '<td style="display: none">' . $order->menu_id . '</td>';
                $order_list_data .= '<td style="display: none">' . $order->note . '</td>';
                $order_list_data .= '<td style="display: none"></td>';
                $order_list_data .= '<td style="display: none"></td>';
                $order_list_data .= '<td style="display: none">' . $order->order_menu_id . '</td>';
                $order_list_data .= '<td style="display: none">' . $order->cooking_status . '</td>';
                $order_list_data .= '<td style="display: none">' . $order->process_status . '</td>';
                $order_list_data .= '<td style="display: none">' . $order->quantity . '</td>';
                $order_list_data .= '<td style="display: none">' . $order->dinein_takeaway . '</td>';
                $order_list_data .= '</tr>';

                // foreach ($order->side_dish_list as $sdh) {
                //     $order_list_data .= '<tr>';
                //     $order_list_data .= '<td></td>';
                //     $order_list_data .= '<td>-- ' . $sdh->name . '</td>';
                //     $order_list_data .= '<td class="border-side tb-align-center">' . $sdh->quantity . '</td>';
                //     $order_list_data .= '<td class="tb-align-right" style="padding-right: 10px">Rp ' . $sdh->price . '</td>';
                //     $order_list_data .= '</tr>';
                // }

            }

            if ($order_payment['subtotal'] != '0') {
                $order_bill_data .= '<tr>';
                $order_bill_data .= '<td style="width:40%"></td>';
                $order_bill_data .= '<td style="width:30%"><b>Subtotal</b></td>';
                $order_bill_data .= '<td style="width:30%" id="subtotal-price" class="tb-align-right">Rp ' . $order_payment['subtotal'] . '</td>';
                $order_bill_data .= '</tr>';

                foreach ($order_payment['tax_price'] as $tax) {

                    $order_bill_data .= '<tr>';
                    $order_bill_data .= '<td></td>';
                    $order_bill_data .= '<td><b>' . $tax['name'] . '</b></td>';
                    $order_bill_data .= '<td id="tax-price" class="tb-align-right">Rp ' . $tax['value'] . '</td>';
                    $order_bill_data .= '</tr>';

                }

                foreach ($order_payment['extra_charge_price'] as $xtra) {

                    $order_bill_data .= '<tr>';
                    $order_bill_data .= '<td></td>';
                    $order_bill_data .= '<td><b>' . $xtra['name'] . '</b></td>';
                    $order_bill_data .= '<td class="tb-align-right">Rp ' . $xtra['value'] . '</td>';
                    $order_bill_data .= '</tr>';

                }
                if(isset($order_payment['delivery_cost']))
                {
                  $order_bill_data .= '<tr>';
                  $order_bill_data .= '<td></td>';
                  $order_bill_data .= '<td><b>Ongkir</b></td>';
                  $order_bill_data .= '<td class="tb-align-right">Rp ' . number_format($order_payment['delivery_cost'] , 0, "", ".") . '</td>';
                  $order_bill_data .= '</tr>';

                }
                $order_bill_data .= '<tr>';
                $order_bill_data .= '<td></td>';
                $order_bill_data .= '<td><b>Total</b></td>';
                $order_bill_data .= '<td id="total-price" class="tb-align-right"><b>Rp ' . $order_payment['total_price'] . '</b><input id="totalBill" type="hidden" value="' . str_replace('.', '', $order_payment['total_price']) . '">';
                $order_bill_data .= '</td>';
                $order_bill_data .= '</tr>';
            }
        }

        $return_data['order_list'] = $order_list_data;
        $return_data['cooking_time'] = array('start_cooking' => $start_cooking, 'end_cooking' => $end_cooking);
        $return_data['order_bill'] = $order_bill_data;

        return $return_data;
    }

    function create_shape_table($table_shape, $status_name)
    {
        $shape ="";
        switch ($table_shape) {
            case "labeledTriangle":
            $shape = 'dine-in-order label-triangle-' . $status_name;
            break;

            case "labeledRect":
            $shape = 'dine-in-order label-rect-' . $status_name;
            break;

            case "labeledCircle":
            $shape = 'dine-in-order label-circle-' . $status_name;
            break;

            default:
            $shape = 'dine-in-order label-rect-' . $status_name;
        }
        return $shape;
    }