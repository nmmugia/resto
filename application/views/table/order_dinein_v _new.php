<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/24/2014
 * Time: 11:32 AM
 */
?>
<input id="order_id" name="order_id" type="hidden" value="<?php echo $order_id ?>"/>
<input id="order_is_view" name="order_is_view" type="hidden" value="<?php echo $order_is_view ?>"/>
<input id="already_process" name="already_process" type="hidden" value="<?php echo $already_process ?>"/>
<input id="already_completed" name="already_completed" type="hidden"
       value="<?php echo $table_data->table_status ?>"/>
<input id="is_dine_in" name="is_dine_in" type="hidden" value="1"/>

<!-- popup -->
<div class="popup-block" id="popup-new-order" style="display:none;">
    <div class="popup-order">
        <div class="col-lg-12">
            <form id="form-input-order">
            <div class="title-bg-popup">
                    <a  class="btn btn-std btn-cancel btn-distance" style="float:right"> X </a></div>
                <div class="title-bg">
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
                                <td><?php echo $this->lang->line('ds_lbl_amount'); ?>
                                <td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default btn-number" data-type="minus"
                                                    data-field="quantity">
                                                <span class="glyphicon glyphicon-minus"></span>
                                            </button>
                                        </span>
                                        <input type="text" name="quantity" class="form-control input-number count-order"
                                               value="1"
                                               min="1" max="1000">
                                      <span class="input-group-btn">
                                          <button type="button" class="btn btn-default btn-number" data-type="plus"
                                                  data-field="quantity">
                                              <span class="glyphicon glyphicon-plus"></span>
                                          </button>
                                      </span>
                                    </div>
                                <td>
                            </tr>
                            <tr>
                                <td colspan="4"><?php echo $this->lang->line('ds_lbl_option'); ?>
                                <td>
                            </tr>
                            <tr>
                                <td class="menu-option" colspan="4">
                                <td>
                            </tr>
                            <tr>
                                <td colspan="4"><?php echo $this->lang->line('ds_lbl_side_dish'); ?>
                                <td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <table class="side-dish"></table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="order-note">
                        <p><?php echo $this->lang->line('ds_lbl_notes'); ?></p>
                        <textarea class="form-control order-notes" style="resize:none;height:274px;"></textarea>
                    </div>
                    <div class="clearfix"></div>
                    <div style="display: block;width: 100%;height: 50px">
                        <button type="reset" class="btn btn-std btn-cancel-dine-in"
                                style="float:right;"><?php echo $this->lang->line('ds_submit_cancel'); ?></button>

                        <button class="btn btn-std btn-delete-order"
                                style="float:right;"><?php echo $this->lang->line('ds_btn_delete'); ?></button>
                        <button class="btn btn-std btn-save"
                                style="float:right;"><?php echo $this->lang->line('ds_submit_save'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- end popup -->

<nav class="header navbar navbar-default" role="navigation">
    <div class="container-fluid header-bg">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header" style="width:100%;">
            <div class="row">
                 <span class="icon-bar">
                    <a href="<?php echo base_url('table'); ?>" class="btn btn-glass"
                       style="float:right;"><?php echo $this->lang->line('ds_btn_back'); ?></a>
                </span>
                <span class="icon-bar">
                    <a id="btn-process-order" href="<?php echo base_url('table/process_order'); ?>"
                       class="btn btn-glass"
                       style="float:right;margin-right:10px;"><?php echo $this->lang->line('ds_btn_process_order'); ?></a>
                </span>
                <span class="icon-bar">
                    <a id="btn-checkout-order" href="<?php echo base_url('table/checkout_order'); ?>"
                       class="btn btn-glass"
                       style="float:right;margin-right:10px;"><?php echo $this->lang->line('ds_btn_checkout_order'); ?></a>
                </span>
                <span class="icon-bar">
                    <a id="btn-pending-bill" href="<?php echo base_url('table/pending_bill'); ?>"
                       class="btn btn-glass"
                       style="float:right;margin-right:10px;"><?php echo $this->lang->line('ds_btn_pending_bill'); ?></a>
                </span>
                <span class="icon-bar">
                    <a id="btn-reset-order" href="<?php echo base_url('table/reset_dine_in'); ?>"
                       class="btn btn-glass"
                       style="float:right;margin-right:10px;"><?php echo $this->lang->line('ds_btn_reset'); ?></a>
                </span>
                <a class="navbar-brand logo-header" href="#">
                    <img src="<?php echo base_url() ?>assets/img/ico-spoon.png" alt="logo"/>
                </a>

                <div class="logo-resto-header">
                    <div class="logo-resto-mini">
                        <?php
                        if (!empty($data_store) && !empty($data_store->store_logo)) {
                            echo '<img src="' . base_url($data_store->store_logo) . '" alt="logo"/>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="colorfull-line"></div>
</nav>

<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <!-- Order Menu Panel -->
                <div class="row">
                    <div class="sub-header-co-table">
                        <p style="float:left;margin-left:10px;"><?php echo $table_data->table_name . ' : ' . $table_data->customer_count; ?>
                            orang</p>

                        <p style="float:right;margin-right:20px;"><?php echo ucfirst($this->data['group_name']) . ': <b>' . $user_name . '</b>'; ?></p>
                    </div>
                </div>
                <!-- End Order Menu -->
            </div>
            <?php if ($this->session->flashdata('message')) { ?>
                <div class="alert alert-danger">
                    <?php echo $this->session->flashdata('message'); ?>
                </div>
            <?php } ?>
			
			<div class="col-lg-4">
                <div style="">
                    <div class="title-bg">
                        <h4 class="title-name left">Category</h4>
						<!-- Button untuk switch dari mode thumbnail ke mode list -->
						<button class="btn btn-option-list right"><img src="<?php echo base_url() ?>assets/img/icon-list.png" alt="list"/></button>
						<button class="btn btn-option-list right"><img src="<?php echo base_url() ?>assets/img/icon-thumb.png" alt="list"/></button>
                    </div>
                    <div class="dark-theme-con">
                        <div class="row" style="margin: 0 !important;">
                            <div class="col-lg-12" style="height: 440px;display: block;overflow-x: hidden;overflow-y:scroll;">
                                
									<!-- Mode List Category -->
									<ul class="list-category-text">
										<li>Western</li>
										<li>Chinese</li>
										<li>Indonesian</li>
									</ul>
								
                                    <?php if (!empty($categories)) {
                                          foreach ($categories as $category) {
                                          echo '<a href="' . base_url('table/get_menus') . '" class="order-menu-categories get_menus" data-category="' . $category->id . '">';
                                                if (empty($category->icon_url)) {
                                                    echo '<img src="' . base_url('assets/img/default-category.jpg') . '" alt="menu"/>';
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
                                 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			
            <div class="col-lg-4">
                <div class="row">
                    <div style="margin: 0 10px 10px 10px">
                        <div class="col-lg-12" style="padding: 0 !important;">
                            <div class="title-bg-custom">
                              <div class="input-group" style="margin-right: 5px;">
                              <span class="input-group-addon" style="color:#881817;font-size:16px;font-family: 'robotobold';">MENU</span>
                              <input type="text" class="search form-control" placeholder="Search">
                              </div>                           
                            </div>
                            <div class="dark-theme-con" style="height:440px">
                                <div class="col-lg-12" style="height:100%;overflow-x:hidden;overflow-y: auto">
                                    <div class="container-menus">
									
										<!-- Mode List Menu -->
										<ul class="list-category-text">
											<li>
												<span class="left">Ayam Goreng</span>
												<span class="right">Rp 50.000</span>
											</li>
											<li>
												<span class="left">Ayam bakar</span>
												<span class="right">Rp 52.000</span>
											</li>
											<li>
												<span class="left">Ayam Rebus</span>
												<span class="right">Rp 51.000</span>
											</li>
										</ul>
									
                                        <?php if (!empty($menus)) {
                                            foreach ($menus as $menu) {
                                                $image = base_url('assets/img/default-menus.jpg');
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
                                            echo '<h5 style="color:#000;text-align: center">' . $this->lang->line('ds_menu_empty') . '</h5>';
                                        } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End row -->
            </div>
			
			<div class="col-lg-4">
                <div class="row">
                    <div style="margin: 0 10px 10px 10px">
                        <div class="title-bg">
                            <h4 class="title-name">Orders</h4>
                        </div>
                        <div class="bill-theme-con" style="height:440px;overflow-y: auto">
                            <div id="table-bill-list" style="">
                                <table class="bill-table">
                                    <thead>
                                    <tr>
                                        <th style="width:40%">Menu</th>
                                        <th class="border-side" style="width:20%">Jumlah</th>
                                        <th style="width:40%">Harga</th>
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
                    </div>
                </div>
                <!-- End row -->
            </div>
        
        <!-- End row -->
    </div>
    </div>
    <!-- End container fluid -->
</div>
<!-- End page wrapper -->

<script data-main=" <?php echo base_url('assets/js/main-table'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>