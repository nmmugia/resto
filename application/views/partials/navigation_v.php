<?php 
        $active_url = $_SERVER['REQUEST_URI'];

?>
<link href="<?php echo base_url(); ?>assets/js/plugins/bootstrap-toggle/bootstrap-toggle.min.css" rel="stylesheet">
<input type="hidden" id="open_close_status" value="<?php echo  $data_open_close->status ?>"/>


<nav class="header navbar navbar-default" role="navigation">
    <div class="header-bg">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header" style="">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false" style="margin-top:25px;">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
				<a class="navbar-brand logo-header" href="<?php echo base_url(); ?>">
                    <img src="<?php echo base_url() ?>assets/img/reskin/logo.png" alt="logo"/>
                </a>
				<!--
                <div class="logo-resto-header">
                    <div class="logo-resto-mini">
                        <?php
                        if (!empty($data_store) && !empty($data_store->store_logo)) {
                            echo '<img src="' . base_url($data_store->store_logo) . '" alt="logo"/>';
                        }
                        ?>
                    </div>
                </div> 
				-->
		 </div>
		  <div class="collapse navbar-collapse collapse-custom" id="bs-example-navbar-collapse-1" style="">
               
					<?php
                    $active = '';
                    $close_status = 'Closed';
                    if($data_open_close->status==1){
                        $active = '-active';
                        $close_status = 'Open';
                    }
                    ?>
					<?php 
						if(!isset($menu)) $menu = "";
						$kitchenmenu = array(
							"stock", "order", "menu_history", "inventory_history", 
						);
						$posmenu = array();
						$posmenu_after_divider = array();
						// insert POS menu
						if($this->groups_access->have_access('dinein')){
							$posmenu[] = (object) array(
								"name" => $this->lang->line('ds_btn_dine_in'), 
                                "parent_class" => "",
								"url" => base_url('table'),
								"data_active" => ((stripos($active_url, 'table')) !== FALSE ? 'active' : ''),
							);
						}
						if($this->groups_access->have_access('delivery')){
							$posmenu[] = (object) array(
								"name" => $this->lang->line('ds_btn_delivery'), 
                                "parent_class" => "",
								"url" => base_url('cashier/delivery'),
								"data_active" => ((stripos($active_url, 'delivery')) !== FALSE ? 'active' : ''),
							);
						}
						if($this->groups_access->have_access('takeaway')){
							$posmenu[] = (object) array(
								"name" => $this->lang->line('ds_btn_take_away'), 
                                "parent_class" => "",
								"url" => base_url('cashier/takeaway'),
								"data_active" => ((stripos($active_url, 'takeaway')) !== FALSE ? 'active' : ''),
							);
						}
						if($this->groups_access->have_access('reservation')){
							$posmenu[] = (object) array(
								"name" => "Reservasi", 
                                "parent_class" => "hidden-sm",
								"url" => base_url('reservation'),
								"data_active" => ((stripos($active_url, 'reservation')) !== FALSE ? 'active' : ''),
							);
						}
						if($this->groups_access->have_access('petty_cash')){
							$posmenu_after_divider[] = (object) array(
								"name" => "Kas Kecil", 
                                "parent_class" => "hidden-xs hidden-sm",
								"url" => base_url('petty_cash'),
								"classname" => "menu_petty_cash",
								"feature_confirmation" => $feature_confirmation['petty_cash'],
								"data_active" => ((stripos($active_url, 'petty_cash')) !== FALSE ? 'active' : ''),
							);
						}
						if($this->groups_access->have_access('refund')){
							$posmenu_after_divider[] = (object) array(
								"name" => "Refund", 
                                "parent_class" => "hidden-xs hidden-sm",
								"url" => base_url('cashier/refund'),
								"classname" => "menu_refund",
								"feature_confirmation" => $feature_confirmation['refund'],
								"data_active" => ((stripos($active_url, 'refund')) !== FALSE ? 'active' : ''),
							);
						}
                        if ($this->data['setting']['summary_sales_on_cashier'] == 1) {
                            if($this->groups_access->have_access('sales_report')){
                                $posmenu_after_divider[] = (object) array(
                                    "name" => "Report", 
                                    "parent_class" => "hidden-xs hidden-sm",
                                    "url" => "#",
                                    "classname" => "menu_report",
                                    "feature_confirmation" => $feature_confirmation['sales_report'],
                                    "data_active" => ((stripos($active_url, 'menu_report')) !== FALSE ? 'active' : ''),
                                );
                            }
                        }
						$divider = (count($posmenu) > 0 && count($posmenu_after_divider) > 0);
					?>
					<?php foreach($posmenu as $pos): ?>
					<span class="icon-bar <?php echo $pos->parent_class?>">
                       <a data-active="<?php echo $pos->data_active;?>"
						href="<?php echo $pos->url;?>" class="btn btn-glass"
						style="float:left;margin-right:5px;"><?php echo $pos->name;?></a>
					</span>
					<?php endforeach; ?>
					<?php if(count($posmenu) > 0 || count($posmenu_after_divider) > 0): ?>
					<span class="icon-bar hidden-xs hidden-sm">
						<a href="#" class="btn-glass"
							style="float:left;margin-right:5px; pointer-events: none;">|</a>
					</span>
					<?php endif;?>
					
					<?php foreach($posmenu_after_divider as $pos): ?>
					
					<span class="icon-bar <?php echo $pos->parent_class?>">
						<a data-active="<?php echo $pos->data_active;?>"
							href="<?php echo $pos->url;?>" class="btn btn-glass <?php echo $pos->classname;?>"
							feature_confirmation="<?php echo $pos->feature_confirmation;?>"
							style="float:left;margin-right:5px;"><?php echo $pos->name;?></a>

					</span>
					<?php endforeach; ?>
            <?php if($this->groups_access->have_access('checker')): ?>
			  <span class="icon-bar">
                <a href="<?php echo base_url();?>checker" class="btn btn-glass <?php echo (stripos($active_url, 'checker')) !== FALSE ? 'active' : ''; ?>"
					style="float:left;margin-right:5px;">Checker</a>
              </span>
            <?php endif; ?>
			
			<?php if($this->groups_access->have_access('kitchen')): ?>
              <span class="icon-bar">
				<ul class="nav navbar-nav btn btn-glass dropdown-menu-cs <?php echo (in_array($menu, $kitchenmenu) ? 'active' : '')?>" style="margin-right:5px;">
					<li class="dropdown">
					  <a class="dropdown-toggle"
						data-toggle="dropdown" >Kitchen <span class="caret hidden-xs"></span></a>
					  <ul class="dropdown-menu">			
						<li>
							<a class="<?php echo ($menu == 'stock' ? 'active' : '')?>" href="<?php echo base_url("stock") ?>"><?php echo $this->lang->line('ds_lbl_stock') ?></a>
						</li>
						<li>
							<a class="<?php echo ($menu == 'order' ? 'active' : '')?>" href="<?php echo base_url("kitchen") ?>">Pesanan</a>
						</li>
						<li>
							<a  class="<?php echo ($menu == 'menu_history' ? 'active' : '')?>"href="<?php echo base_url("kitchen/histories") ?>">Histori Pengeluaran Menu</a>
						</li>
						<li>
							<a class="<?php echo ($menu == 'inventory_history' ? 'active' : '')?>" href="<?php echo base_url("kitchen/inventory") ?>">Inventory</a>
						</li>
					  </ul>
					</li>
				</ul>
              </span>
            <?php endif; ?>
			
			<?php if($this->groups_access->have_access('reservation_monitor')): ?>
              <span class="icon-bar hidden-xs hidden-sm">
                <a href="<?php echo base_url();?>monitoring" class="btn btn-glass <?php echo (stripos($active_url, 'monitoring')) !== FALSE ? 'active' : ''; ?>"
					style="float:left;margin-right:5px;">Monitoring</a>
              </span>
            <?php endif; ?>

            <span class="icon-bar">
                <a href="<?php echo base_url('auth/logout'); ?>" class="btn btn-glass logout-btn"><?php echo $this->lang->line('ds_btn_logout'); ?></a>
            </span>

            <?php if(!$this->groups_access->have_access('reservation_monitor')): ?>
            <?php if($setting["notification"]==1): ?>
                    <span class="icon-bar hidden-xs hidden-sm">
                        <a href="#" class="btn btn-glass" id="btn-notif"
                           style="float:right;position:relative;margin-right:5px;">Notification
                            <?php 
                                if(!empty($this->data['list_notif_unseen'])) {
                                    echo '<div class="counter-notification">';
                                    echo sizeof($this->data['list_notif_unseen'])."</div>";
                                    
                                }
                                
                            ?>
                        </a>
                    </span>
            <?php endif; ?>
            <?php endif; ?>
					
			<?php
            if($this->groups_access->have_access('openclose')):
            if (!stripos($active_url, 'openclose')) {
                ?>
				<span class="icon-bar openclose hidden-xs hidden-sm" 
					data-placement="bottom" 
					data-tooltip="tooltip" 
					title="<?php echo 'Click to '.($active ? "Close" : "Open");?>">
					<input id="openCloseToggle" type="checkbox" disabled hidden
						<?php echo ($active ? "checked" : "");?>
						data-toggle="toggle" 
						data-on="OPEN" 
						data-off="CLOSED"
						data-onstyle="toggle-openclose"
						data-offstyle="toggle-openclose">
				</span>
                 <?php
            }
			endif?>

            <?php if($this->groups_access->have_access('checker')): ?>
                <span class="icon-bar hidden-xs hidden-sm">
                    <a id="call_waiter" class="btn-call-waiter"><i class="fa fa-phone-square fa-3x" aria-hidden="true"></i></a>
                </span>
            <?php endif; ?>
				
				</div>
        <div class="text-center" id="alert_open_close_cashier" style="padding:15px;color: #a94442;background-color: #f2dede;border-color: #ebccd1;<?php echo ($data_open_close->status!=1 ? "" : "display:none;") ?>">SILAHKAN OPEN CASHIER UNTUK MEMULAI TRANSAKSI</div>
    </div>
   
    <div class="clearfix"></div>
</nav>

        <div class="notification-container" style="display:none;">
                <div class="button-hide" >
                    <a class="" href="#"><img src="<?php echo base_url() ?>assets/img/icon-close.png"> Tutup</a>
                </div>
                <div id="notification-container-list">
                <?php 
                if(!empty($this->data['list_notif'])) {
                    echo "<div id='notification-container'>";
                    foreach ($this->data['list_notif'] as $key => $row) {                        
                        $seen_class = "";
                        if($row->seen == 0){
                            $seen_class = "unseen-notif";
                        }
                        ?>
                        <div class="list-notification <?php echo $seen_class ?>" id="notif-<?php echo $row->id ?>" data-id="<?php echo $row->id ?>" >
                           <p class="content-notif"><?php echo $row->message ;?></p>
                           <a class="button-ok-notif" href="#" data-id="<?php echo $row->id ?>"></a>
                       </div>
                       <?php 
                   }
                   echo "</div>";
               }else{
                echo '<div class="list-notification empty-notif" >Tidak ada notifikasi.</div>';
            }
            ?>
                </div>

        </div>

<!-- open close pop up -->

<div class="popup-block" id="popup-openclose" style="display:none;">
    <div class="popup-pin" style="">
        <div class="col-lg-12">
            <div class="popup-panel pin-header" style="text-align:center;height:auto;display:table;padding:0 0 10px 0 !important;">
                <?php if($data_open_close->status!=1): ?>
                <input id="openclose_begin_balance" value="120"   <?php if($data_open_close_today){ echo "disabled"; }?> class="input-pin" onkeypress="if ( isNaN( String.fromCharCode(event.keyCode) )) return false;" placeholder="Saldo Awal" style="font-size:30px !important;" type="text" pattern="\d*" 
                autocomplete="off"/>
                <?php if($data_open_close_today){?>
                <input type="hidden" id="begin_balance_value" value="<?php echo $data_open_close->total_cash;?>">
                <?php }?>
                <?php else: ?>
		        <?php if($setting['cash_on_hand']==1): ?>
                <input id="openclose_cash_on_hand" class="input-pin" placeholder="Cash On Hand" style="font-size:30px !important;" onkeypress="if ( isNaN( String.fromCharCode(event.keyCode) )) return false;" type="text" pattern="\d*" autocomplete="off" novalidate/>
                <?php endif; ?>
								<?php endif; ?>
                <input id="pin_input" name="" class="input-pin " placeholder="Masukkan PIN" style="font-size:30px !important;" type="password" pattern="\d*" autocomplete="off" novalidate/>
                <div class="calc-button-container">
                    <div class="cashier-pin-container">
                        <button class="btn btn-calc btn-pin number" data-value="0">1</button>
                        <button class="btn btn-calc btn-pin number" data-value="0">2</button>
                        <button class="btn btn-calc btn-pin number" data-value="0">3</button>
                        <button class="btn btn-calc btn-pin number" data-value="0">4</button>
                        <button class="btn btn-calc btn-pin number" data-value="0">5</button>
                        <button class="btn btn-calc btn-pin number" data-value="0">6</button>
                        <button class="btn btn-calc btn-pin number" data-value="0">7</button>
                        <button class="btn btn-calc btn-pin number" data-value="0">8</button>
                        <button class="btn btn-calc btn-pin number" data-value="0">9</button>
                        <button class="btn btn-calc btn-clear clearNumber btn-open">C</button>
                        <button class="btn btn-calc btn-pin number">0</button>
                        <button class="btn btn-calc btn-open" id="btn-enter-open">OK</button>
                    </div>
                </div>
                <button class="btn btn-std btn-cancel"
                        style="width:50%;margin-top:10px;"><?php echo $this->lang->line('ds_submit_cancel'); ?></button>
            </div>
        </div>
    </div>
</div>
<div class="popup-block" id="popup-feature-confirmation" style="display:none;">
    <div class="popup-pin" style="">
        <div class="col-xs-12">
            <div class="popup-panel pin-header" style="text-align:center;height:auto;display:table;padding:0 0 10px 0 !important;">
                <input class="input-pin" placeholder="INPUT PIN" style="font-size:30px !important;" type="password" pattern="\d*" autocomplete="off" novalidate/>
                <div class="calc-button-container">
                    <div class="cashier-pin-container">
                        <button type="button" class="btn btn-calc btn-pin-confirmation" data-value="1">1</button>
                        <button type="button" class="btn btn-calc btn-pin-confirmation" data-value="2">2</button>
                        <button type="button" class="btn btn-calc btn-pin-confirmation" data-value="3">3</button>
                        <button type="button" class="btn btn-calc btn-pin-confirmation" data-value="4">4</button>
                        <button type="button" class="btn btn-calc btn-pin-confirmation" data-value="5">5</button>
                        <button type="button" class="btn btn-calc btn-pin-confirmation" data-value="6">6</button>
                        <button type="button" class="btn btn-calc btn-pin-confirmation" data-value="7">7</button>
                        <button type="button" class="btn btn-calc btn-pin-confirmation" data-value="8">8</button>
                        <button type="button" class="btn btn-calc btn-pin-confirmation" data-value="9">9</button>
                        <button type="button" class="btn btn-calc btn-pin-confirmation clearNumber" id="btn-feature-confirmation-clear">C</button>
                        <button type="button" class="btn btn-calc btn-pin-confirmation" data-value="0">0</button>
                        <button type="button" class="btn btn-calc btn-pin-confirmation" id="btn-feature-confirmation-ok">OK</button>
                    </div>
                </div>
                <button class="btn btn-std" id="btn-feature-confirmation-cancel"
                        style="width:50%;margin-top:0px;"><?php echo $this->lang->line('ds_submit_cancel'); ?></button>
            </div>
        </div>
    </div>
</div>
<?php if($this->data['setting']['summary_sales_on_cashier'] == 1): ?>
<div class="popup-block popup-sales-report" style="display:none;">
    <div class="col-lg-4 col-lg-offset-4" style="width: 50%; margin-left: 25%; margin-top: 5%;">
        <div class="col-lg-12">
            <div class="row">
                <div class="title-bg-popup">
                    <a class="btn btn-std btn-cancel btn-distance" style="float:right">X</a>
                </div>
                <div class="title-bg title-bg-member">
                    <h4 class="title-popup">Summary Sales Today</h4>
                </div>
                <div class="dark-theme-con" style="display:table;width:100%;padding:10px;">
                    <table class="table table-bordered border">
                        <tbody>
                            <tr>
                                <td><label>Total Penjualan</label></td>
                                <td id="sum_total_price" class="text-right"></td>
                                <td><label>Total Cash</label></td>
                                <td id="total_cash" class="text-right"></td>
                            </tr>
                            <tr>
                                <td><label>Total Transaksi</label></td>
                                <td id="total_transaction" class="text-right"></td>
                                <td><label>Total Debit</label></td>
                                <td id="total_debit" class="text-right"></td>
                            </tr>
                            <tr>
                                <td><label>Total Customer</label></td>
                                <td id="total_customer_count" class="text-right"></td>
                                <td><label>Total Credit</label></td>
                                <td id="total_credit" class="text-right"></td>
                            </tr>
                            <tr>
                                <td><label>Total Penjualan Menu</label></td>
                                <td id="total_quantity_order" class="text-right"></td>
                                <td><label>Total Diskon</label></td>
                                <td id="total_discount" class="text-right"></td>
                            </tr>
                            <tr>
                                <td><label>Total Penggunaan Voucher</label></td>
                                <td id="total_count_voucher" class="text-right"></td>
                                <td><label>Total Tax</label></td>
                                <td id="total_tax" class="text-right"></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="button-wrapper">
                                <a class="btn btn-std btn-cancel btn-distance">Tutup</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<div class="popup-block popup-input" id="popup-ajax" style="display:none;"></div>
<!-- end pop up -->