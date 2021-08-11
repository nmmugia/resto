<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/23/2014
 * Time: 4:04 PM
 */
?>
<div class="popup-block" style="display:none;">
    <div class="popup-table">
        <div class="col-lg-12">
            <div class="title-bg">
                <h4 class="title-name"><b><?php echo $this->lang->line('ds_lbl_guest_amount'); ?></b></h4>
                <h4 class="title-name"><?php echo $this->lang->line('ds_lbl_choose_guest_amount'); ?></h4>
            </div>
            <div class="popup-panel" style="text-align: center;height:auto;display:table;">
                <div class="popup-button-co" style="height:auto;display:table;">
                    <?php for ($i = 1; $i < 13; $i++) { ?>
                        <a class="btn btn-lite new_order" href="<?php echo base_url('table/new_order'); ?>"
                           data-guest="<?php echo $i ?>"><p
                                style="margin-right: 2px; margin-top:5px;"><?php echo $i ?></p></a>
                    <?php } ?>
                    <input type="hidden" id="new_table_id" value=""/>
                    <input type="hidden" id="new_order_url" value="<?php echo base_url('table/order_dine_in'); ?>"/>
                </div>
                <button class="btn btn-std btn-cancel-new-order"
                        style="width:50%;margin-top:10px;"><?php echo $this->lang->line('ds_submit_cancel'); ?></button>
            </div>
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
                    <a href="<?php echo base_url('auth/logout'); ?>" class="btn btn-glass"
                       style="float:right;"><?php echo $this->lang->line('ds_btn_logout'); ?></a>
                </span>
                <?php
                if ($this->groups_access->have_access('takeaway')) {
                    ?>
                    <span class="icon-bar">
                    <a href="<?php echo base_url('cashier/takeaway'); ?>" class="btn btn-glass"
                       style="float:right;margin-right:10px;"><?php echo $this->lang->line('ds_btn_take_away'); ?></a>
                </span>
                <?php
                }
                ?>
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
                    <p class="resto-name-mini"><?php if (!empty($data_store)) {
                            echo $data_store->store_name;
                        } ?></p>
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
                <!-- Table Panel -->
                <div class="row">
                    <div class="sub-header-co-table">
                        <p id="floor_name"><?php if (!empty($floor_name)) {
                                echo $floor_name;
                            } ?></p>
                        <input type="hidden" id="floor_default_id" value="<?php echo $floor_id; ?>"/>
                        <input type="hidden" id="floor_id" value="<?php echo $floor_id; ?>"/>
						<!-- Button untuk switch dari mode thumbnail ke mode list -->
						<button class="btn btn-option-list right"><img src="<?php echo base_url() ?>assets/img/icon-list.png" alt="list"/></button>
						<button class="btn btn-option-list right"><img src="<?php echo base_url() ?>assets/img/icon-thumb.png" alt="list"/></button>
                    </div>
                </div>
                <div class="row">
                    <div class="dine-in-container">
					
						<!-- Mode List Table -->
						<div class="col-lg-6 col-lg-offset-3" style="z-index:444;margin-top:20px;position:relative;">
							<div class="badge-table">
								1
							</div>
							<div class="warning-table">
								
							</div>
							<div class="table-list-container">
								<div class="floot-list-container left">
									<h3 class="title-list-text">Lantai</h3>
									<ul class="floor-list">
										<li>Lantai 1</li>
										<li>Lantai 2</li>
										<li>Lantai 3</li>
									</ul>
								</div>
								<div class="table-list left">
									<h3 class="title-list-text">Meja</h3>
									<a href="#" class="table-list-text cleaning">1</a>
									<a href="#" class="table-list-text order">2</a>
									<a href="#" class="table-list-text completed">3</a>
									<a href="#" class="table-list-text empty">4</a>
									<a href="#" class="table-list-text wait">5</a>
								</div>
							</div>
						</div>
						
                        <input type="hidden" id="all_table_empty" value="<?php echo $all_table_empty; ?>"/>
                        <?php
                        echo '<div id="table-parent" style="position:relative;margin:0 auto;display: block;width: ' . $default_table_width . ';height:' . $default_table_height . '">';
                        if (!empty($data_table)) {

                            foreach ($data_table as $table) {
                                switch ($table->table_shape) {
                                    case "labeledTriangle":
                                        $shape = 'dine-in-order label-triangle-' . $table->status_name;
                                        break;
                                    case "labeledRect":
                                        $shape = 'dine-in-order label-rect-' . $table->status_name;
                                        break;
                                    case "labeledCircle":
                                        $shape = 'dine-in-order label-circle-' . $table->status_name;
                                        break;
                                    default:
                                        $shape = 'dine-in-order label-rect-' . $table->status_name;
                                }

                                $new_style = 'width: ' . $table->width . 'px;';
                                $new_style .= 'height:' . $table->height . 'px;';
                                $new_style .= 'left:' . $table->pos_x . 'px;';
                                $new_style .= 'top:' . $table->pos_y . 'px;';
                                $new_style .= '-ms-transform: rotate(' . $table->rotate . 'deg);';
                                $new_style .= '-webkit-transform: rotate(' . $table->rotate . 'deg);';
                                $new_style .= 'transform: rotate(' . $table->rotate . 'deg);';
                                $new_style .= 'transform-origin: 0% 0%';

                                $span_style = '-ms-transform: rotate(-' . $table->rotate . 'deg);';
                                $span_style .= '-webkit-transform: rotate(-' . $table->rotate . 'deg);';
                                $span_style .= 'transform: rotate(-' . $table->rotate . 'deg);';
                                $span_style .= 'transform-origin: 50% 50% 0px';

                                $custom_data = 'data-table-id="' . $table->table_id . '" ';
                                $custom_data .= 'data-table-status="' . $table->id . '" ';
                                $custom_data .= 'data-order-id="' . $table->order_id . '" ';
                                $custom_data .= 'data-customer-count="' . $table->customer_count . '" ';

                                echo '<div id="tab_layout_' . $table->table_id . '" ' . $custom_data . ' class="' . $shape . '" style="' . $new_style . '"><span class="v-middle" style="' . $span_style . '">' . $table->table_name . '</span></div>';
                            }

                        }
                        else {
                            echo '<p style="text-align: center;font-size: 25px;font-weight: bold">' . $this->lang->line('ds_table_empty') . '</p>';
                        }
                        echo '</div>';
                        ?>
                        <!-- End Table Panel -->
                    </div>
                </div>
            </div>
        </div>
        <div class="row legend-container">
            <ul>
                <li><p><b>Keterangan</b></p></li>
                <li>
                    <div class="legend cleaning"></div>
                    <p>Cleaning</p></li>
                <li>
                    <div class="legend order"></div>
                    <p>Order</p></li>
                <li>
                    <div class="legend completed"></div>
                    <p>Completed</p></li>
                <li>
                    <div class="legend empty"></div>
                    <p>Empty</p></li>
                <li>
                    <div class="legend wait"></div>
                    <p>Wait</p></li>
            </ul>
        </div>
        <!-- End row -->
        <div class="row navigation-floor">
            <div class="col-lg-12">
                <a href="<?php echo base_url('table/change_floor'); ?>" data-id="prev"
                   class="btn-prev btn-change-floor"><img
                        src="<?php echo base_url() ?>assets/img/button_prev.png"
                        alt="nav"/></a>
                <a href="#" class="btn btn-big-glass btn-change-table">Pindahkan Meja</a>
                <a href="<?php echo base_url('table/change_floor'); ?>" data-id="next"
                   class="btn-next btn-change-floor"><img
                        src="<?php echo base_url() ?>assets/img/button_next.png"
                        alt="nav"/></a>
            </div>
        </div>
        <!-- End row -->
    </div>
    <!-- End container fluid -->
</div>
<!-- End page wrapper -->

<script data-main="<?php echo base_url('assets/js/main-table'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>