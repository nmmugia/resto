<!-- popup -->
<div class="popup-block" style="display:none;">
    <div class="popup-order">
        <div class="col-lg-12">
            <form id="form-input-order">
                <div class="title-bg">
                    <h4 class="title-name menu-name"></h4>
                    <input type="hidden" id="menu_id_selected" value="">
                    <input type="hidden" id="menu_price_selected" value="">
                    <input type="hidden" id="menu_order_id_selected" value="">
                    <input type="hidden" id="menu_cooking_status" value="">
                    <input type="hidden" id="is_already_process" value="">
                    <input type="hidden" id="price-menu" value="0">
                    <input type="hidden" id="discount-name" value="">
                    <input id="temp_total_ordered" name="temp_total_ordered" type="hidden" value='0'/>
                    
                </div>
                <div class="popup-order-panel">
                    <div class="dark-theme-con"
                         style="float:left;width:50%;height:310px;overflow-x: hidden;overflow-y: auto">
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

                    <button type="reset" class="btn btn-std btn-cancel"
                            style="float:right;margin-right:10px;"><?php echo $this->lang->line('ds_submit_cancel'); ?>
                    </button>
                    <button class="btn btn-std btn-save"
                            style="float:right;"><?php echo $this->lang->line('ds_submit_save'); ?></button>
                    <button  id = "btn-discount-single" 
                            style="float:right;"
                            class="btn btn-std " >Diskon</button>
                    <button class="btn btn-std btn-delete-order"
                            style="float:right;"><?php echo $this->lang->line('ds_btn_delete'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- popup -->
<div class="popup-discount" style="display:none;">
    <input type="hidden" id="is_single_discount" value="1" />

    <div class="discount-content">
        <div class="col-lg-12   ">
            <form id="form-input-order">
                <div class="title-bg">
                    <h4 class="title-name">Pilih Diskon</h4>
                    
                </div>
                <div class="popup-order-panel">
                    <div class="dark-theme-con" style="height:310px;padding-top: 10px;">
                               
                                <table class="table">
                                    <tr>
                                        <td>Subtotal:</td>
                                        <td id="subtotal-price-discount" data-price='0' colspan="2">0</td>
                                    </tr>
                                     <tr>
                                        <td>Diskon:</td>
                                        <td>% <input type="text" id="input-discount-percent" class="form-control" value='0'></td>
                                        <td>Rp. <input type="text" id="input-discount-amount" class="form-control" value='0'/></td>
                                    </tr>
                                    
                                     <tr>
                                        <td>Pilih Diskon</td>
                                        <td colspan="2">
                                            <select class="form-control" id="ddl_discount">
                                                <?php
                                                    echo "<option value='0' data-discount='0' data-type='none'>Tidak diskon</option>";;
                                                foreach ($all_discount as $key => $row) {
                                                    if($row->is_percentage == 1){
                                                        echo "<option data-name='".$row->name."' data-discount='".$row->value."' data-type='percent' value='".$row->id."'>".$row->name." (".$row->value."%)</option>";

                                                    }else{
                                                        echo "<option data-name='".$row->name."' data-discount='".$row->value."' data-type='amount' value='".$row->id."'>".$row->name." (Rp. ".number_format($row->value,0,"",".").")</option>";

                                                    }
                                                }
                                                    echo "<option id='other-discount' value='other'>Lainnya</option>";;

                                                ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr id="form-discount-name" style="display:none">
                                        <td>Nama Diskon</td>
                                        <td colspan="2"><input type="text" id="input-discount-name" class="form-control"/></td>
                                    </tr>

                                </table>
                       
                    </div>
                     <button type="reset" class="btn btn-std btn-cancel"
                            style="float:left;margin-right:10px;"><?php echo $this->lang->line('ds_submit_cancel'); ?>
                    </button>
                    <button type="reset" class="btn btn-std btn-save-discount"
                            style="float:left;margin-right:10px;">Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="new-order-checkout" class="popup-block-custom" style="display:none;">
    <div class="popup-order-custom">
        <div class="col-lg-12">
            <div class="col-lg-12">
                <div class="title-bg">
                    <h4 class="title-name"><?php echo $this->lang->line('ds_nav_category'); ?></h4>
                </div>
                <div class="dark-theme-con">
                    <div class="row">
                        <div class="col-lg-12" style="padding: 0 30px">
                            <div
                                style="height: 190px;display: block;overflow-x: scroll;overflow-y:hidden;white-space: nowrap">
                                <?php if (!empty($categories)) {
                                    foreach ($categories as $category) {
                                        echo '<a href="' . base_url('cashier/get_menus') . '" class="order-menu-categories get_menus" data-category="' . $category->id . '">';
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
            <div class="col-lg-12">
                <div class="title-bg">
                    <h4 class="title-name category-name">Menu</h4>
                </div>
                <div class="dark-theme-con" style="height: 200px">
                    <div class="col-lg-12" style="height:164px;overflow-x:hidden;overflow-y: auto">
                        <div class="row container-menus">
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
                                echo '<h5 style="color:#000;text-align: center">' . $this->lang->line('ds_choose_category') . '</h5>';
                            } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="footer-bg">
                    <button class="btn btn-std btn-cancel-new-order"
                            style="margin-left:10px;"><?php echo $this->lang->line('ds_submit_cancel'); ?>
                    </button>
                </div>
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
                    <a href="<?php echo $back_url; ?>" class="btn btn-glass"
                       style="float:right;"><?php echo $this->lang->line('ds_btn_back'); ?></a>
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
                    <p class="resto-name-mini"><?php 
                    if (!empty($data_store)) {
                            echo $data_store->store_name;
                        } 
                        ?></p>
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
                        <p style="float:left;margin-left:100px;"><?php echo $order_mode; ?></p>

                        <p style="float:right;margin-right:20px;"><?php echo $staff_mode; ?></p>

                        <p style="text-align:center"><?php echo $this->lang->line('ds_nav_orders') ?></p>
                    </div>
                </div>
                <!-- End Order Menu -->
            </div>

            <div class="order-wrapper">

            <div class="col-md-6">
                <div class="row">
                    <div style="margin-left:10px;margin-right: 10px">
                        <div class="title-bg" style="position: relative">
                            <h4 class="title-name"><?php echo $this->lang->line('ds_nav_orders') ?></h4>
                            <button type="reset" class="btn btn-trans btn-small-trans btn-new-order"
                                    style="position: absolute;top: 0;right: 5px"><?php echo $this->lang->line('ds_btn_add_order'); ?></button>
                        </div>
                        <div class="bill-theme-con bill-theme-cus" style="height:500px;">
                            <div id="table-bill-list-checkout" style="height: 100px;overflow-y: auto">
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
                            <table style="width: 100%;height: 100px;" id="paymentAmount">
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div style="margin-left:10px;margin-right: 10px">
                        <div class="title-bg">
                            <h4 class="title-name">Pembayaran</h4>
                        </div>
                        <div class="dark-theme-con" style="height:500px;">
                            <div class="col-lg-12">
                                <div class="row">
                                    <label for="input-payment" class="sr-only"></label>
                                    <input id="input-payment" name="input-payment" class="form-control input-pin pBill "
                                           type="text" pattern="\d*" autocomplete="off" novalidate/>

                                    <div class="clearfix"></div>
                                    <div class="calc-button-co" style="overflow:hidden;">
                                        <div style="width:400px;margin:auto;">
                                            <button class="btn btn-calc pBill number">7</button>
                                            <button class="btn btn-calc pBill number">8</button>
                                            <button class="btn btn-calc pBill number">9</button>
                                            <button class="btn btn-calc-clear pBill clearNumber">C</button>
                                            <button class="btn btn-calc pBill deleteNumber">&laquo;</button>
                                            <div class="clearfix"></div>
                                            <button class="btn btn-calc pBill number">4</button>
                                            <button class="btn btn-calc pBill number">5</button>
                                            <button class="btn btn-calc pBill number">6</button>
                                            <button class="btn btn-calc pBill number50">50k</button>
                                            <button class="btn btn-calc pBill number75">75k</button>
                                            <div class="clearfix"></div>
                                            <button class="btn btn-calc pBill number">1</button>
                                            <button class="btn btn-calc pBill number">2</button>
                                            <button class="btn btn-calc pBill number">3</button>
                                            <button class="btn btn-calc pBill number100">100k</button>
                                            <button id="print-bill" class="btn btn-calc pBill btn-metode" disabled="disabled">Print</button>
                                            <div class="clearfix"></div>
                                            <button class="btn btn-calc pBill number">0</button>
                                            <button class="btn btn-calc pBill number">00</button>
                                            <button class="btn btn-calc pBill btn-exactly">Pas</button>
                                            <button class="btn btn-calc pBill payment-ok">OK</button>
                                            <button id="done-payment" class="btn btn-metode" disabled="disabled">Done
                                            </button>
                                        </div>
                                        <div class="clearfix" style="margin-bottom:10px;"></div>
                                            <button id="btn-discount" class="btn btn-metode pBill " >Diskon</button>

                                        <div style="width:400px;margin:auto;">
                                            <button id="cash-payment" class="btn btn-metode pBill active" >Cash</button>
                                            <button id="debit-payment" class="btn btn-metode pBill">Debit</button>
                                            <button id="credit-payment" class="btn btn-metode pBill">Credit</button>
                                            <button id="pending-bill-print" class="btn btn-metode">Pending</button>
                                            <button id="reset-payment" class="btn btn-metode">Reset</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            </div> <!-- End order wrapper -->

            <!-- End row -->
        </div>
    </div>
    <!-- End row -->
</div>
<!-- End container fluid -->
</div>
<!-- End page wrapper -->
<input id="less_payments" type="hidden" value="<?php echo $this->lang->line('ds_less_payments'); ?>"/>
<input id="error_card_number" type="hidden" value="<?php echo $this->lang->line('ds_card_number'); ?>"/>
<input id="order_id" name="order_id" type="hidden" value="<?php echo $order_id ?>"/>
<input id="is_checkout" name="is_checkout" type="hidden" value="1"/>
<script data-main=" <?php echo base_url('assets/js/main-cashier'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>