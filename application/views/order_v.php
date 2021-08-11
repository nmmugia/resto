<body id="floor-theme">
<div id="cover"></div>
<div id="server-error-message" title="Server Error" style="display: none">
    <p>
        Internal server error. Please contact administrator if the problem persists
    </p>
</div>
<input id="base_url" type="hidden" value="<?php echo base_url(); ?>"/>
<input type="hidden" id="node_url" value="<?php echo $this->config->item('node_server_ip'); ?>:<?php echo $this->config->item('node_server_port'); ?>"/>
<script src="<?php echo $this->config->item('node_server_ip'); ?>:<?php echo $this->config->item('node_server_port'); ?>/socket.io/socket.io.js"></script>
<input id="order_id" name="order_id" type="hidden" value="<?php if (!empty($order_id)) {
    echo $order_id;
}
else {
    echo 0;
} ?>"/>

<!-- popup -->
<div class="popup-block" style="display:none;">
    <div class="popup-order">
        <div class="col-lg-12">
            <div class="title-bg">
                <h4 class="title-name menu-name"></h4>
                <input type="hidden" id="menu_id_selected" value="">
                <input type="hidden" id="menu_price_selected" value="">
                <input type="hidden" id="menu_order_id_selected" value="">
            </div>
            <div class="popup-order-panel">
                <div class="dark-theme-con" style="float:left;width:50%;height:310px;">
                    <table class="acc-table">
                        <tbody>
                        <tr>
                            <td>Jumlah
                            <td>
                            <td><input type="text" class="form-control count-order"/>
                            <td>
                        </tr>
                        <tr>
                            <td>Opsi
                            <td>
                            <td class="menu-option">

                            <td>
                        </tr>
                        <tr>
                            <td>Tambahan
                            <td>
                        </tr>
                        <tr>
                            <table class="side-dish">

                            </table>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="order-note">
                    <p>Catatan</p>
				<textarea class="form-control order-notes" style="resize:none;height:274px;">
					
				</textarea>
                </div>
                <button class="btn btn-trans btn-cancel" style="float:left;margin-top:15px;margin-right:10px;">Batal
                </button>
                <button class="btn btn-trans btn-save" style="float:left;margin-top:15px;">Simpan</button>
                <button class="btn btn-trans btn-delete-order" style="float:right;margin-top:15px;">Hapus Pesanan
                </button>
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
                <form action="order/back_to_table_page" method="POST">
                    <span class="icon-bar"><button class="btn btn-glass btn-back" style="float:right;">Kembali</button></span>
                </form>
				<span class="icon-bar">
                    <a class="btn btn-glass process_order" style="float:right;margin-right:30px;"
                       href="<?php echo base_url('order/process_neworder'); ?>"
                       data-table="<?php if (!empty($table_id)) {
                           echo $table_id;
                       }
                       else {
                           echo 0;
                       } ?>" data-status="<?php if (!empty($table_status)) {
                        echo $table_status;
                    }
                    else {
                        echo 0;
                    } ?>">Proses Pesanan</a>
				</span>
				<span class="icon-bar">
					<a class="btn btn-glass btn-glass checkout_order" style="float:right;margin-right:5px;"
                       href="<?php echo base_url('order/checkout_order'); ?>"
                       data-table="<?php if (!empty($table_id)) {
                           echo $table_id;
                       }
                       else {
                           echo 0;
                       } ?>" data-status="<?php if (!empty($table_status)) {
                        echo $table_status;
                    }
                    else {
                        echo 0;
                    } ?>">Checkout</a>
				</span>
				<span class="icon-bar">
					<a class="btn btn-glass btn-empty-table empty_order" style="float:right;margin-right:5px;"
                       href="<?php echo base_url('order/empty_order'); ?>"
                       data-table="<?php if (!empty($table_id)) {
                           echo $table_id;
                       }
                       else {
                           echo 0;
                       } ?>" data-status="<?php if (!empty($table_status)) {
                        echo $table_status;
                    }
                    else {
                        echo 0;
                    } ?>">Kosongkan</a>
				</span>
                <a class="navbar-brand logo-header" href="#"><img
                        src="<?php echo base_url() ?>assets/img/ico-spoon.png" alt="logo"/></a>
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
                    <div class="sub-header-co">
                        <p style="float:left;margin-left:130px;">Meja <?php if (!empty($table_id)) {
                                echo $table_id;
                            }
                            else {
                                echo 0;
                            } ?>, <?php if (!empty($number_guest)) {
                                echo $number_guest;
                            }
                            else {
                                echo 0;
                            } ?> Orang</p>

                        <p style="float:right;margin-right:20px;"><?php if (!empty($waiter)) {
                                echo $waiter->name;
                            } ?></p>
                    </div>
                </div>
                <!-- End Order Menu -->
            </div>
            <?php if ($this->session->flashdata('message')) { ?>
                <div class="alert alert-danger">
                    <?php echo $this->session->flashdata('message'); ?>
                </div>
            <?php } ?>
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-4-ord">
                        <div class="title-bg">
                            <h4 class="title-name">Orders</h4>
                        </div>
                        <div class="bill-theme-con" style="height:280px;overflow:scroll;overflow-x:hidden;">
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
                                    foreach ($order_list as $order) { ?>
                                        <tr>
                                            <td><?php echo $order->menu_name; ?></td>
                                            <td class="border-side"><?php echo $order->count; ?></td>
                                            <td>Rp <?php echo number_format($order->menu_price); ?></td>
                                            <input type="hidden" id="menu_id"
                                                   value="<?php echo $order->menu_id; ?>">
                                            <input type="hidden" id="menu_notes"
                                                   value="<?php echo $order->notes; ?>">
                                            <input type="hidden" id="menu_option"
                                                   value="<?php echo $order->options; ?>">
                                            <input type="hidden" id="menu_side_dish"
                                                   value="<?php echo $order->side_dishes; ?>">
                                            <input type="hidden" id="menu_order_id"
                                                   value="<?php echo $order->order_menu_id; ?>">
                                        </tr>
                                    <?php }
                                } ?>
                                </tbody>
                            </table>
                            <table class="total-payment">
                                <tbody>
                                <?php if (!empty($order_payment)) {
                                    foreach ($order_payment as $payment) { ?>
                                        <tr>
                                            <td style="width:40%"></td>
                                            <td style="width:30%"><b>Subtotal</b></td>
                                            <td style="width:30%" id="subtotal-price">
                                                Rp <?php echo number_format($payment->subtotal_price); ?></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td><b>Pajak 10%</b></td>
                                            <td id="tax-price">
                                                Rp <?php echo number_format($payment->tax_price); ?></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td><b>Total</b></td>
                                            <td id="total-price">
                                                Rp <?php echo number_format($payment->total_price); ?></td>
                                        </tr>
                                    <?php }
                                } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-6-ord">
                        <div class="title-bg">
                            <h4 class="title-name category-name">Menu</h4>
                        </div>
                        <div class="dark-theme-con" style="height:280px;">
                            <div class="col-lg-12" style="height:270px;overflow:scroll;overflow-x:hidden;">
                                <div class="row container-menus">
                                    <?php if (!empty($menus)) {
                                        foreach ($menus as $menu) { ?>
                                            <div class="menu-order menu">
                                                <img src="<?php echo base_url() ?><?php echo $menu->icon_url; ?>"
                                                     alt="menu"/>

                                                <p><?php echo $menu->menu_name; ?></p>
                                                <input type="hidden" id="menu_id" value="<?php echo $menu->id; ?>">
                                                <input type="hidden" id="menu_price"
                                                       value="<?php echo $menu->menu_price; ?>">
                                            </div>
                                        <?php }
                                    }
                                    else echo '<h5 style="color:#fff">' . 'Tidak ada menu' . '</h5>'; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- End row -->
            </div>
            <div class="col-lg-12" style="margin-top:10px;">
                <div class="title-bg">
                    <h4 class="title-name">Category</h4>
                </div>
                <div class="dark-theme-con" style="height:190px;overflow:scroll;overflow-y:hidden;">
                    <div style="width:1400px;">
                        <div class="row">
                            <?php if (!empty($categories)) {
                                foreach ($categories as $category) { ?>
                                    <div class="menu-order menu-category">
                                        <img src="<?php echo base_url() ?><?php echo $category->icon_url; ?>"
                                             alt="menu"/>

                                        <p><?php echo $category->category_name; ?></p>
                                        <input type="hidden" id="category_id" value="<?php echo $category->id; ?>">
                                    </div>
                                <?php }
                            }
                            else echo '<h5 style="color:#fff">' . 'Tidak ada kategori menu' . '</h5>'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End row -->
    </div>
    <!-- End container fluid -->
</div>
<!-- End page wrapper -->
</body>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/libs/require.js"
        data-main="<?php echo base_url() ?>assets/js/main-order"></script>
</html>