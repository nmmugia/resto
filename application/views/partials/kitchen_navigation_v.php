

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
<nav class="header navbar navbar-default" role="navigation">
	<div class="header-bg">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false" style="margin-top:25px;">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand logo-header" href="<?php echo base_url(); ?>">
			<img src="<?php echo base_url() ?>assets/img/reskin/logo.png" alt="logo"/>
			</a>
		</div>
		<div class="collapse navbar-collapse collapse-custom" id="bs-example-navbar-collapse-1" style="">
			<span class="icon-bar">
			<a class="btn btn-glass" style="float:right;margin-right:40px" href="<?php echo base_url("auth/logout") ?>">Logout</a> 
			</span>
			
            <?php 
				$posmenu = array();
				$posmenu_after_divider = array();
				// insert POS menu
				if($this->groups_access->have_access('dinein')){
					$posmenu[] = (object) array(
						"name" => $this->lang->line('ds_btn_dine_in'), 
						"url" => base_url('table')
					);
				}
				if($this->groups_access->have_access('delivery')){
					$posmenu[] = (object) array(
						"name" => $this->lang->line('ds_btn_delivery'), 
						"url" => base_url('cashier/delivery')
					);
				}
				if($this->groups_access->have_access('takeaway')){
					$posmenu[] = (object) array(
						"name" => $this->lang->line('ds_btn_take_away'), 
						"url" => base_url('cashier/takeaway')
					);
				}
				if($this->groups_access->have_access('reservation')){
					$posmenu[] = (object) array(
						"name" => "Reservasi", 
						"url" => base_url('reservation')
					);
				}
				if($this->groups_access->have_access('petty_cash')){
					$posmenu_after_divider[] = (object) array(
						"name" => "Kas Kecil", 
						"url" => base_url('petty_cash'),
						"classname" => "menu_petty_cash",
						"feature_confirmation" => $feature_confirmation['petty_cash'],
					);
				}
				if($this->groups_access->have_access('refund')){
					$posmenu_after_divider[] = (object) array(
						"name" => "Refund", 
						"url" => base_url('cashier/refund'),
						"classname" => "menu_refund",
						"feature_confirmation" => $feature_confirmation['refund'],
					);
				}
				$divider = (count($posmenu) > 0 && count($posmenu_after_divider) > 0);
			?>
            <?php if(count($posmenu) > 0 || count($posmenu_after_divider) > 0): ?>
			<span class="icon-bar">
				<ul class="nav navbar-nav btn btn-glass">
					<li class="">
					  <a href="#" class="dropdown-toggle" 
						data-toggle="dropdown" role="button" 
						aria-haspopup="true">POS</a>
						<ul class="dropdown-menu">
						<?php foreach($posmenu as $pos): ?>
						<li><a href="<?php echo $pos->url;?>"><?php echo $pos->name;?></a></li>
						<?php endforeach; ?>
						<?php if($divider): ?>
						<li role="separator" class="divider"></li>
						<?php endif; ?>
						<?php foreach($posmenu_after_divider as $pos): ?>
						<li>
							<a href="<?php echo $pos->url;?>" 
								class="<?php echo $pos->classname;?>"
								feature_confirmation="<?php echo $pos->feature_confirmation;?>"><?php echo $pos->name;?></a>
						</li>
						<?php endforeach; ?>
						</ul>
					</li>
				</ul>
			</span>
            <?php endif; ?>
            <?php if($this->groups_access->have_access('checker')): ?>
			<span class="icon-bar">
                <a href="<?php echo base_url();?>checker" class="btn btn-glass <?php echo (stripos($menu, 'checker')) !== FALSE ? 'active' : ''; ?>"
					style="float:left;margin-right:5px;">Checker</a>
			</span>
            <?php endif; ?>
            <?php if($this->groups_access->have_access('checker')): ?>
			<a href="#" class="btn-glass"
			   style="float:left;margin-right:5px; pointer-events: none;">|</a>
			</span>
            <?php endif; ?>
			<span class="icon-bar"> 
			<a class="btn btn-glass <?php echo ($menu == 'stock')?'active':'';?>" style="float:left;margin-right:10px;" href="<?php echo base_url("stock") ?>">
			<?php echo $this->lang->line('ds_lbl_stock') ?></a>
			</span> 
			<span class="icon-bar">
			<a href="<?php echo base_url("kitchen") ?>" class="btn btn-glass <?php echo ($menu == 'order')?'active':'';?>" style="float:left;">Pesanan</a>
			</span> 
			<span class="icon-bar">
			<a href="<?php echo base_url("kitchen/histories") ?>" class="btn btn-glass <?php echo ($menu == 'menu_history')?'active':'';?>" style="float:left;">Histori Pengeluaran Menu</a>
			</span>
			<span class="icon-bar">
			<a href="<?php echo base_url("kitchen/inventory") ?>" class="btn btn-glass <?php echo ($menu == 'inventory_history')?'active':'';?>" style="float:left;"> Inventory</a>
			</span>            
		</div>
	</div>
	<div class="clearfix"></div>
</nav>