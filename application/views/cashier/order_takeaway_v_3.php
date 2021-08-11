<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/24/2014
 * Time: 11:32 AM
 */
?>
<link rel="stylesheet" href="<?php echo base_url() ?>assets/js/plugins/easyautocomplete/easy-autocomplete.min.css">
<input id="order_id" name="order_id" type="hidden" value="<?php echo $order_id ?>"/>
<input id="order_is_view" name="order_is_view" type="hidden" value="<?php echo $order_is_view ?>"/>
<input id="is_takeaway" name="is_takeaway" type="hidden" value="1"/>
<input id="zero_stock_order"  type="hidden" value="<?php echo $setting['zero_stock_order'] ?>"/>
<input id="temp_total_ordered" name="temp_total_ordered" type="hidden" value='0'/>
<input id="is_take_away" name="is_take_away" type="hidden" value='1'/>
<input id="void_manager_confirmation"  type="hidden" value="<?php echo $setting['void_manager_confirmation'] ?>"/>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/plugins/list.min.js"></script>
<!-- popup -->
<div class="popup-block" id="popup-new-order" style="display:none;">
    <div class="popup-order">
        <div class="col-lg-12">
            <form id="form-input-order">
            <div class="title-bg-popup">
                    <a  class="btn btn-std btn-cancel btn-distance" style="float:right"> X </a></div>
                <div class="title-bg title-bg-food">
                    <h4 class="title-popup menu-name"></h4>
                    <input type="hidden" id="menu_id_selected" value="">
                    <input type="hidden" id="menu_order_id_selected" value="">
                    <input type="hidden" id="is_already_process" value="">
                </div>
                <div class="popup-order-panel">
                    <div class="dark-theme-con"
                         style="float:left;width:50%;height:310px;overflow-x: hidden;overflow-y: auto;padding:5px;">
                        <table class="acc-table" style="width: 100%">
                            <tbody>
                            <tr>
                                <td>
									<label><?php echo $this->lang->line('ds_lbl_amount'); ?></label>
                                    <div class="input-group" style="width:100%;">
                                        <button type="button" class="btn btn-float btn-number" style="left:4px;" data-type="minus"
                                                data-field="quantity">
                                            <span class="glyphicon glyphicon-minus"></span>
                                        </button>
                                        <input type="text" name="quantity" class="form-control input-number count-order"
                                               value="1"
                                               min="1" max="1000" maxlength="3">
                                        <button type="button" class="btn btn-float btn-default btn-number" style="right:4px;" data-type="plus"
                                                data-field="quantity">
                                            <span class="glyphicon glyphicon-plus"></span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4"><label><?php echo $this->lang->line('ds_lbl_option'); ?></label>
                                </td>
                            </tr>
                            <tr>
                                <td class="menu-option" colspan="4">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4"><label><?php echo $this->lang->line('ds_lbl_side_dish'); ?></label>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <table class="side-dish" style="width:100%;"></table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="order-note">
                        <label class="pull-left" id="waiter_name_order_menu" style="display:none;"></label>
                        <div class="clearfix"></div>
                        <label><?php echo $this->lang->line('ds_lbl_notes'); ?></label>
                        <textarea class="form-control order-notes" style="resize:none;height:274px;"></textarea>
                    </div>
                    <div class="clearfix"></div>
                    <div class="button-wrapper">
                        <button type="reset" class="btn btn-std btn-cancel-dine-in"
                                style=""><?php echo $this->lang->line('ds_submit_cancel'); ?></button>


                         <?php if($this->groups_access->have_access('void_order')):?>
                        <button  type="button" class="btn btn-std btn-void-order"
                        feature_confirmation="<?php echo ($feature_confirmation['void_order']) ?>"
                                style="display:none;"><?php echo $this->lang->line('ds_btn_void'); ?></button>
                         <?php endif ?>

                        <button class="btn btn-std btn-delete-order"
                                style="display:none;"><?php echo $this->lang->line('ds_btn_cancel'); ?></button>
                        <button class="btn btn-std btn-save"><?php echo $this->lang->line('ds_submit_ok'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="popup-block" id="popup-void" style="display:none;z-index:9;">
    <div class="col-lg-4 col-lg-offset-4" style="margin-top:9%;">
        <div class="col-lg-12">
        <div class="title-bg-popup">
                    <a  class="btn btn-std btn-cancel btn-distance" style="float:right"> X </a></div>
                <div class="title-bg title-bg-food">
                    <h4 class="title-popup menu-name">Void</h4>
                    <input type="hidden" id="menu_id_selected" value="">
                    <input type="hidden" id="menu_order_id_selected" value="">
                    <input type="hidden" id="is_already_process" value="">
                </div>
                <div class="popup-order-panel">
                    <div class="dark-theme-con"
                         style="float:left;width:100%;overflow-x: hidden;overflow-y: auto;padding:5px;">
                        <table class="acc-table" style="width: 100%">
                            <tbody>
                            <tr>
                                <td>
									<label>Banyak void</label>
                                    <div class="input-group" style="width:100%;">
                                        <button type="button" class="btn btn-float btn-number" style="left:4px;" data-type="minus"
                                                data-field="quantity">
                                            <span class="glyphicon glyphicon-minus"></span>
                                        </button>
                                        <input type="text" name="quantity" id="input_void_count"  class="form-control input-number count-order"
                                               value="1"
                                               min="1" max="1000" maxlength="3">
                                        <button type="button" class="btn btn-float btn-default btn-number" style="right:4px;" data-type="plus"
                                                data-field="quantity">
                                            <span class="glyphicon glyphicon-plus"></span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="order-note" style="width:100%;padding-left:0;height:230px;">
                        <label>Alasan void</label>
                        <textarea class="form-control" name="input_void_note" id="input_void_note" style="resize:none;height:184px;"></textarea>
                    </div>
                    <div class="form-group">
                        <label><input type="checkbox" name="is_decrease_stock" value='0' id="is_decrease_stock"> Memotong Stock</label>
                    </div>
                    <div class="clearfix"></div>
                    <div class="button-wrapper">
                        <button type="reset" class="btn btn-std btn-cancel-dine-in">Batal</button>                        
                        <button class="btn btn-std btn-save-void"><?php echo $this->lang->line('ds_submit_save'); ?></button>
                    </div>
                </div>
        </div>
    </div>
</div>


<div class="popup-block popup-input" style="display:none;z-index:9;">
    <div class="col-lg-4 col-lg-offset-4 input-payment-method">
        <div class="col-lg-12">
            <div class="row">
                <div class="title-bg-custom">
                    <h4 class="title-name"></h4>
                </div>
                <div class="dark-theme-con" style="display:table;width:100%;padding:10px;">
                    <input class="form-control" type="text" name="input_pin" id="input_pin"/>
                    <div class="col-lg-12">
                        <div class="row">
                        <a href="#" class="btn btn-std pull-right btn-cancel-void">Batal</a>
                        <a href="#" class="btn btn-std pull-right btn-void-confirm" id="btn-ok-input"><i class="fa fa-check"></i> OK</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- end popup -->

<?php
  $this->load->view('partials/navigation_v');
?>


<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 order-header">
                <div class="col-sm-5 col-lg-4 col-md-4">
					<div class="row">
						<div class="resto-info-mini">
							<div class="resto-info-pic">
							
							</div>
							<div class="resto-info-name">
								<?php echo $data_store->store_name; ?>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-2 col-lg-4 col-md-4">
				</div>
				 <div class="col-sm-5 col-lg-4 col-md-4">
					<div class="row">
						<div class="margin-wrap">
							<div class="panel-info panel-info-fixed">
								<div class="col-xs-4">
									<p class="role-info text-left">Take Away</p>
								</div>
                <div class="col-xs-4">
									<p class="role-info text-center">Waiter</p>
									<p class="role-name text-center"><?php echo $data_order->waiter_name; ?></p>
								</div>
								<div class="col-xs-4">
									<p class="role-info text-right"><?php echo ucfirst($this->data['group_name']); ?></p>
									<p class="role-name text-right"><?php echo $user_name; ?></p>
								</div>
							</div>
						</div>
					</div>
				</div>
            </div>
            <?php if ($this->session->flashdata('message')) { ?>
                <div class="alert alert-danger">
                    <?php echo $this->session->flashdata('message'); ?>
                </div>
            <?php } ?>

            <div class="col-md-12 hidden-lg hidden-md order-button-container" align="right">
                <a href="#" id="hidden_customer"  class="btn btn-danger btn-sm mobile-sub-menu active">Pelanggan</a>
                <a href="#" id="hidden_menu"  class="btn btn-danger btn-sm mobile-sub-menu">List Menu</a>
                <a href="#" id="hidden_order" class="btn btn-danger btn-sm mobile-sub-menu">Order Menu</a>
            </div>

            <div class="order-wrapper">

             <div class="col-md-4" id="hidden_customer_dv">
                <div class="row">
                    <div class="panel-wrapper">
                        <div class="title-bg-custom">
                            <h4 class="title-name left"style="font-size: 16px;">Data Pemesan</h4>
                        </div>
                        <div class="col-lg-12" style="padding: 0 !important;margin-bottom: 10px">
                            <div class="title-bg-custom" style="background-color: #ddd">
                                <div>
                                    <div class="form-group" style="margin-bottom:0;">
                                        <!--
                                        <label for="customer_name" class="col-lg-2"
                                               style="font-size: 18px"><?php echo $this->lang->line('ds_lbl_name'); ?></label>
                                        -->
                                        <div class="col-sm-6 col-xs-6" style="padding:0px;">
                                          <input id="customer_name" name="customer_name" class="form-control"
                                                                       placeholder="<?php echo $this->lang->line('ds_lbl_name'); ?>"
                                                                       value="<?php if (!empty($data_order)) echo $data_order->customer_name ?>"/>
                                        </div>
                                        <div class="col-sm-6 col-xs-6" style="padding:0px;">
                                          <input id="customer_phone" name="customer_phone" class="form-control only_numeric"
                                                 placeholder="<?php echo $this->lang->line('ds_lbl_phone'); ?>"
                                                 value="<?php if (!empty($data_order)) echo $data_order->customer_phone ?>"/>                    
                                        </div>
                                       
                                    </div>
                                </div>
                                
                                <div class="clearfix"></div>
                            </div>
                        </div>

                        <div class="title-bg-custom hidden-xs hidden-sm">
                            <h4 class="title-name left"style="font-size: 16px;"><?php echo $this->lang->line('ds_nav_category') ?></h4>
                            <button id="btn-category-list" class="btn btn-option-list right active"><img src="<?php echo base_url() ?>assets/img/icon-list.png" alt="list"/>&nbsp;&nbsp;List View</button>
                            <button id="btn-category-thumb" class="btn btn-option-list right"><img src="<?php echo base_url() ?>assets/img/icon-thumb.png" alt="list"/>&nbsp;&nbsp;Grid View</button>

                        </div>
                        <div class="dark-theme-con hidden-xs hidden-sm">
                            <div class="row" style="margin: 0 !important;">
                                <div class="col-lg-12 order-container-mini-2 mobile-scroll" style="display: block;overflow-x: hidden;overflow-y:auto;">

                                        <?php if (!empty($categories)) { ?>
                                        <ul class="list-category-text" id="list-category-text">                                   
                                            <?php
                                            echo '<a href="'.base_url('cashier/get_menus') . '" class="get_menus" data-category=""><li>Semua</li></a>';
                                            foreach ($categories as $category) { 
                                                echo '<a href="' . base_url('cashier/get_menus') . '" class="get_menus" data-category="' . $category->id . '"><li>';
                                                echo $category->category_name . '</li></a>';
                                            }       
                                            echo  '</ul>';

                                        }
                                        else {
                                            echo '<h5 style="color:#000;text-align: center">' . $this->lang->line('ds_category_empty') . '</h5>';
                                        } ?>
                                        <!-- end Mode List Category -->

                                        <span id="thumb-category-text" style="display:none;">
                                            <?php if (!empty($categories)) {
                                            echo '<a href="' . base_url('cashier/get_menus') . '" class="order-menu-categories get_menus" data-category="">';
                                            echo '<img src="' . base_url('assets/img/default.jpg') . '" alt="menu"/>';
                                            echo '<p>Semua</p>';
                                            echo '</a>';
                                            foreach ($categories as $category) {
                                                echo '<a href="' . base_url('cashier/get_menus') . '" class="order-menu-categories get_menus" data-category="' . $category->id . '">';
                                                if (empty($category->icon_url)) {
                                                    echo '<img src="' . base_url('assets/img/default.jpg') . '" alt="menu"/>';
                                                }
                                                else {
                                                    echo '<img src="' . base_url($category->icon_url) . '" alt="menu"/>';
                                                }
                                                echo '<p>' . $category->category_name . '</p>';
                                                echo '</a>';
                                            }
                                        }
                                        else {
                                            echo '<h5 style="color:#000;text-align: center">' . $this->lang->line('ds_category_empty') . '</h5>';
                                        } ?>
                                        </span>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
           
          
            <div class="col-md-4 hidden-xs hidden-sm" id="hidden_menu_dv">
                <div class="row">
                    <div class="panel-wrapper">                  
                        <div class="col-lg-12" style="padding: 0 !important;" id="menus">
                            <div class="title-bg-custom">
                              <div class="input-group menu-search-container">
                              <span class="input-group-addon" style="color:#881817;font-size:16px;font-family: 'robotobold';">MENU</span>
                              <input type="text" class="search form-control" placeholder="Search">
                              </div>                           
                            </div>
                            <!-- <div class="dark-theme-con res-height" style="height: 440px"> -->
                            <div class="dark-theme-con order-container mobile-scroll" style="display: block;overflow-x: hidden;overflow-y:auto;">
                                <div class="col-lg-12">
                                    <div class="container-menus">


                                         <!-- Mode List Menu -->
                                        <ul class="list-category-text list" id="list-menu-text">
                                        <?php if (!empty($menus)) { ?>
                                          <?php  foreach ($menus as $menu) { ?>
                                            <li data-id="<?php echo $menu->id; ?>"
                                                     data-name="<?php echo $menu->menu_name; ?>"
                                                     data-price="<?php echo $menu->menu_price; ?>"
                                                     class="add-order-menu">
                                                <span class="left name" style="width:75%;"><?php echo $menu->menu_name; ?></span>
                                                <span id="" class="right <?php echo ($setting['zero_stock_order']==1 ? "hide" : "") ?> <?php echo 'total-available-'.$menu->id ?>" 
                                                        data-outlet="<?php echo $menu->outlet_id ?>" style="width:10%;text-align:right;"><?php echo $menu->total_available; ?>
                                                    </span>
                                                    
                                                <span class="right"><?php echo number_format($menu->menu_price,0,"",".");?></span>
                                                <span class="left">Rp</span>
                                            
                                            </li>
                                        <?php }
                                        echo '</ul>';
                                        }
                                        else {
                                            echo '<h5 style="color:#000;text-align: center">' . $this->lang->line('ds_choose_category') . '</h5>';
                                        } ?>

                                        <span id="thumb-menu-text" style="display:none;">                                    
                                        <?php if (!empty($menus)) {
                                            foreach ($menus as $menu) {
                                                $image = base_url('assets/img/default-category.jpg');
                                                if ($menu->icon_url != '') {
                                                    $image = base_url($menu->icon_url);
                                                }
                                                ?>
                                                <div data-id="<?php echo $menu->id; ?>"
                                                     data-name="<?php echo $menu->menu_name; ?>"
                                                     data-price="<?php echo $menu->menu_price; ?>"
                                                     class="menu-order add-order-menu">
                                                    <img src="<?php echo $image; ?>" alt="menu"/>

                                                    <p><?php echo $menu->menu_name; ?></p>
                                                </div>
                                            <?php }
                                        }
                                        else {
                                            echo '<h5 style="color:#000;text-align: center">' . $this->lang->line('ds_choose_category') . '</h5>';
                                        } ?>

                                    </span>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.col-md-4 -->
           
            <div class="col-md-4">
                <div class="row">
                    <div class="panel-wrapper hidden-xs hidden-sm" style="margin: 0 15px 10px 15px" id="hidden_order_dv">
						<div class="clearfix"></div>
                        <div class="title-bg-custom">
                            <h4 class="title-name left">Pesanan</h4>
                        </div>
                        <?php if($printers_checker_setting ) {
                                if($printers_checker_setting->value == 0) { ?>
                        <div class="dark-theme-con order-container mobile-scroll" style="display: block;overflow-x: hidden;overflow-y:auto;"">
                                <?php } else { ?>
                        <div class="dark-theme-con order-container mobile-scroll" style="display: block;overflow-x: hidden;overflow-y:auto;">
                        <?php } }?>
                        
                            <div id="table-bill-list">
                                <table class="bill-table" style="border-bottom:1px solid #e1e1e1;">
                                    <thead>
                                    <tr>
                                        <th style="width:10%;text-align:left;padding-left:10px;">Status</th>
                                        <th style="width:40%;text-align:left;">Menu</th>
                                        <th style="width:15%;text-align:right;">Jumlah</th>
                                        <th style="width:35%;text-align:right;padding-right:10px;">Harga</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (!empty($order_list)) {
                                        echo $order_list;
                                    } ?>
                                    </tbody>
                                </table>
                            </div>
                            <table class="total-payment">
                                <tbody>
                                <?php if (!empty($order_bill)) {
                                    echo $order_bill;
                                } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="clearfix"></div>
                         
                        
                    </div>
                </div>
                <?php if($printers_checker_setting && !empty($printers_checker)) {
                                if($printers_checker_setting->value == 0) { ?>
                            <div class="title-bg-custom">
                                <h4 class="title-name left">Select Printer Checker</h4>
                            </div>
                            <div class="dark-theme-con" style="min-height: 60px;max-height: 585px;display: block;overflow-x: hidden;overflow-y:scroll;">
                                <?php foreach($printers_checker as $key=>$value): ?>
                                    <input type="radio" name="pinter_checker" value="<?php echo $key ?>" <?php if($printers_checker_setting->default == $value) { echo "checked"; }?> > <?php echo $value ?>
                                <?php endforeach; ?>
                            </div>
                        <?php } } ?>                        
                    </div>
            </div><!-- /.col-md-4 -->



            <div class="col-md-12">
                <div class="row">
                    <div class="panel-wrapper">
                        <div>
                            <table style="float:right;">
                                <tr>
                                    <?php if($this->groups_access->have_access('delete_order')):?>
                                    <td class="hidden-xs">
                                        <a href="javascript:void(0);" order_id="<?php echo $order_id ?>" class="btn btn-std delete_order_all"
                                        feature_confirmation="<?php echo ($feature_confirmation['delete_order']) ?>"
                                        style="float:right;margin-right:10px;">Delete Order</a>
                                    </td>
                                    <?php endif; ?>
                                    <td>
										<span class="icon-bar">
                                            <a id="btn-reset-takeaway" href="<?php echo base_url('cashier/reset_takeaway'); ?>"
												class="btn btn-std"
												style="float:right;margin-right:10px;"><?php echo $this->lang->line('ds_btn_reset'); ?></a>
                                        </span>
                                    </td>  
                                     <?php if($this->groups_access->have_access('post_to_ready')): ?>
                                    <td class="hidden-xs">
                                      <span class="icon-bar">
                                        <a href="<?php echo base_url('table/post_to_ready/'.$order_id."/2"); ?>" class="btn btn-std-yellow post_to_ready"
                                        feature_confirmation="<?php echo $feature_confirmation['post_to_ready'] ?>"
                                        style="float:right;margin-right:10px;">POST</a>
                                      </span>
                                    </td>
                                    <?php endif; ?>
                                    <td>
										<span class="icon-bar">
											<a id="btn-process-takeaway" href="<?php echo base_url('cashier/process_takeaway'); ?>"
												class="btn btn-std-yellow"
                                                style="float:right;"><?php echo $this->lang->line('ds_btn_process_order'); ?></a>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                         </div>
                     </div>
                 </div>
             </div><!-- /.col-md-4 -->
			
         </div>
     </div>
        <!-- End row -->

</div>
        <!-- End row order-wrapper -->

</div>


    <!-- End container fluid -->
</div>
<!-- End page wrapper -->

<script data-main=" <?php echo base_url('assets/js/main-table'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>