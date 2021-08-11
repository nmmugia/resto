<body id="table-theme">
<div id="cover"></div>
<div id="server-error-message" title="Server Error" style="display: none">
    <p>
        Internal server error. Please contact administrator if the problem persists
    </p>
</div>
<input id="base_url" type="hidden" value="<?php echo base_url(); ?>"/>
<input id="user_id" type="hidden" value="<?php echo $user_id ?>"/>
<input id="group_id" type="hidden" value="<?php echo $group_id; ?>"/>
<input id="group_name" type="hidden" value="<?php echo $group_name; ?>"/>
<input id="user_name" type="hidden" value="<?php echo $user_name; ?>"/>
<input type="hidden" id="node_url" value="<?php echo $this->config->item('node_server_ip'); ?>:<?php echo $this->config->item('node_server_port'); ?>"/>
<script src="<?php echo $this->config->item('node_server_ip'); ?>:<?php echo $this->config->item('node_server_port'); ?>/socket.io/socket.io.js"></script>
<!-- popup -->
<div class="popup-block" style="display:none;">
    <div class="popup-table">
        <div class="col-lg-12">
            <div class="title-bg">
                <h4 class="title-popup"><b>Jumlah Tamu</b></h4>
                <h4 class="title-popup">Silakan Pilih Jumlah Tamu</h4>
            </div>
            <div class="popup-panel">
                <div class="popup-button-co" style="height:300px;overflow-x:hidden !important;overflow:scroll;">
                    <?php for ($i = 1; $i < 13; $i++) { ?>
                        <a class="btn btn-lite new_order" href="<?php echo base_url('order/new_order'); ?>"
                           data-guest="<?php echo $i ?>"><?php echo $i ?></a>
                    <?php } ?>
                    <input type="hidden" id="new_table_id" value=""/>
                    <input type="hidden" id="new_order_url" value="<?php echo base_url('order'); ?>"/>
                </div>
                <button class="btn btn-std" style="width:50%;float:right;margin-top:30px;">Batal</button>
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
                <span class="icon-bar"><?php echo form_open("auth/logout"); ?>
                    <button class="btn btn-glass" style="float:right;" type="submit">Logout
                    </button><?php echo form_close(); ?></span>
                <span class="icon-bar"><button class="btn btn-glass" style="float:right;margin-right:10px;">Take Away
                    </button></span>
                <a class="navbar-brand logo-header" href="#"><img
                        src="<?php echo base_url() ?>assets/img/ico-spoon.png"
                        alt="logo"/></a>

                <div class="logo-resto-header">
                    <div class="logo-resto-mini">
                        <img src="<?php if (!empty($data_store)) {
                            echo base_url() . $data_store[0]->store_logo;
                        } ?>" alt="logo"/>
                    </div>
                    <p class="resto-name-mini"><?php if (!empty($data_store)) {
                            echo $data_store[0]->store_name;
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
                        <p><?php if (!empty($data_outlet)) {
                                echo $data_outlet[0]->outlet_name . " - ";
                            } ?><?php if (!empty($floor_name)) {
                                echo $floor_name;
                            } ?></p>

                        <p id="floor_name" style="display:none;"><?php if (!empty($floor_name)) {
                                echo $floor_name;
                            } ?></p>
                        <input type="hidden" id="floor_id" value="<?php if (!empty($floor_id)) {
                            echo $floor_id;
                        } ?>">
                        <input type="hidden" id="store_id" value="<?php if (!empty($data_store)) {
                            echo $data_store[0]->id;
                        } ?>">
                    </div>
                </div>
                <!--
                <?php if ($this->session->flashdata('message')) { ?>
                    <div class="alert alert-danger">
                        <?php echo $this->session->flashdata('message'); ?>
                    </div>
                <?php } ?>
                -->
                <div class="row">
                    <form style="display:none" action="order" method="POST">
                        <button class="btn btn-lite btn-hidden"></button>
                        <input type="hidden" name="table_empty" value="0">
                        <input type="hidden" id="number_guest_hidden" name="number_guest_hidden" value="">
                        <input type="hidden" id="table_id_hidden" name="table_id_hidden" value="">
                        <input type="hidden" id="table_status_hidden" name="table_status_hidden" value="">
                        <input type="hidden" id="order_id_hidden" name="order_id_hidden" value="">
                    </form>
                    <div class="table-container">
                        <?php if (!empty($data_table)) {
                            foreach ($data_table as $table) { ?>
                                <div id="tab_layout_<?php echo $table->table_id; ?>"
                                     class="table-list table2 status-<?php echo $table->status_name ?>"
                                     style="top:<?php echo $table->pos_y ?>px;left:<?php echo $table->pos_x ?>%;">
                                    <p class="table-status"><?php echo $table->table_status ?></p>

                                    <p class="table-name"><?php echo $table->status_name ?></p>
                                    <input type="hidden" id="table_id_selected"
                                           value="<?php echo $table->table_id; ?>">
                                    <input type="hidden" id="number_guest_selected"
                                           value="<?php echo $table->customer_count; ?>">
                                    <input type="hidden" id="table_status_selected"
                                           value="<?php echo $table->table_status; ?>">
                                    <input type="hidden" id="order_id_selected"
                                           value="<?php echo $table->order_id; ?>">
                                </div>
                            <?php }
                        }
                        else {
                            echo 'Tidak ada list meja';
                        } ?>
                        <!-- End Table Panel -->
                    </div>
                </div>
            </div>
        </div>
        <!-- End row -->
        <div class="row navigation-floor">
            <div class="col-lg-12">
                <a href="#" class="btn-prev"><img src="<?php echo base_url() ?>assets/img/button_prev.png"
                                                  alt="nav"/></a>
                <a href="#" class="btn btn-big-glass btn-change-table">Pindahkan Meja</a>
                <a href="#" class="btn-next"><img src="<?php echo base_url() ?>assets/img/button_next.png"
                                                  alt="nav"/></a>
            </div>
        </div>
        <!-- End row -->
    </div>
    <!-- End container fluid -->
</div>
<!-- End page wrapper -->

<script type="text/javascript" src="<?php echo base_url() ?>assets/js/libs/require.js"
        data-main="<?php echo base_url() ?>assets/js/main-table"></script>
</body>
</html>