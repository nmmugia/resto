<?php
  $this->load->view('partials/navigation_v');
?>

<div id="page-wrapper">
	
		<div class="col-md-12 order-header">
			
			<div class="col-sm-4">
				<div class="row">
					<div class="resto-info-mini">
						<div class="resto-info-pic"></div>
						<div class="resto-info-name">
							<?php echo $data_store->store_name; ?>
						</div>
					</div>
				</div>
			</div>
			
			<div class="col-sm-4"></div>
			
			<div class="col-sm-4">
				<div class="row">
					<div class="margin-wrap">
						<div class="panel-info panel-info-fixed">
							<div class="col-xs-6" style="padding-top:2px;">
								<a href="<?php echo base_url('cashier/order_takeaway'); ?>"
									class="btn btn-std-yellow"><?php echo $this->lang->line('ds_btn_add_order'); ?></a>
							</div>
							<div class="col-xs-6">
								<p class="role-info text-right"><?php echo ucfirst($this->data['group_name']); ?></p>
								<p class="role-name text-right"><?php echo $user_name; ?></p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
        </div><!-- /.col-md-12 -->
			<?php
			
            foreach ($takeaway_order as $order) {
            echo '
			<div class="col-md-4 list_order" data-order_id="'.$order->id.'">
				<div class="row">
					<div class="panel-wrapper">
						<div class="title-bg title-bg-take-away">
							<div class="col-xs-6" style="padding-left: 2px;">
								<p class="order-number" style="font-size:16px;">'.$order->counter.' | No. Order : ' . $order->id . '</p>
								<p class="order-customer">' . $order->customer_name . '</p>
								
							</div>
							<div class="col-xs-6">
								<div class="row">
									<p class="order-time"><i class="fa fa-clock-o"></i> ' . (date('d/m/Y H:i', strtotime($order->start_order))) . '</p>
								</div>
                <p class="order-customer" style="text-align: right;margin-right: -15px;">' . $order->customer_phone . '</p>
							</div>
						</div>
						<div class="bill-theme-con order-takeaway">
							<div id="table-bill-list" style="height:260px;overflow-y: auto;border-bottom:1px solid #e1e1e1;">
								<table class="bill-table">
									<thead>
										<tr>
											<th style="width:55%;text-align:left;padding-left:10px;">Menu</th>
											<th style="width:15%;text-align:right;">Jumlah</th>
											<th style="width:30%;text-align:right;padding-right:10px;">Status</th>
										</tr>
									</thead>
									<tbody>';
                  $counter_processed=0;
                  $counter_not_ready=0;
									foreach ($order->values as $val) {
                    $background_color="";
                    if($this->data['setting']["use_primary_additional_color"]==1 && isset($val->is_additional)){
                      if($val->is_additional==0){
                        $background_color="background-color:".$this->data['setting']["primary_bg_color"].";";
                      }else{
                        $background_color="background-color:".$this->data['setting']["additional_bg_color"].";";
                      }
                    }
                    if($val->cooking_status!=3)$counter_not_ready++;
                    // if ($val->process_status == '1') {
                      $counter_processed++;
                      echo '
                      <tr style="'.$background_color.'">
                        <td style="padding-left:10px;">' . $val->menu_name . '</td>
                        <td class="text-right">'.$val->quantity.'</td>
                        <td style="padding-right:10px;" class="text-right" data-order_menu_id="'.$val->order_menu_id.'" data-cooking_status="'.$val->cooking_status.'">' . $val->status_name . '</td>
                      </tr>';
                    // }
                  }
										echo '
									</tbody>
								</table>
							</div>  
							<div class="button-wrapper">
								<a href="' . base_url('cashier/order_takeaway/' . $order->id) . '"
									class="btn btn-std-yellow" style="margin-right:5px;">detail</a>';
                  if($setting['use_kitchen']==0 && $setting['use_role_checker']==0)$counter_not_ready=0;
                  if($this->groups_access->have_access('checkout')){
                    echo '
                                  <a id="btn_payment_order_delivery" href="' . base_url('cashier/checkout/' . $order->id) . '"
                    class="btn btn-std-yellow" style="margin-left:5px;display:'.(($counter_processed>0 && $counter_not_ready==0) ? "inline-block" : "none").'">bayar</a>';
                  }
                  echo '
							</div>
						</div>
					</div>
				</div>
			</div>';
			}
            ?>
        


        
    
    <!-- End container fluid -->
</div>

<script data-main=" <?php echo base_url('assets/js/main-table'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>