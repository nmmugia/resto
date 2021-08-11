<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

echo form_open_multipart(base_url(uri_string()), array('class' => 'form-horizontal form-ajax'));
?>
    <style>
        .form-group label {
            text-align: left !important;

        }
    </style>
    <div class="col-lg-12" style="padding: 0 !important">
    <div class="result">
        <?php
        if (!empty($message_success)) {
            echo '<div class="alert alert-success" role="alert">';
            echo $message_success;
            echo '</div>';
        }
        if (!empty($message)) {
            echo '<div class="alert alert-danger" role="alert">';
            echo $message;
            echo '</div>';
        }
        ?>
    </div>
    <div class="row">
    <div class="col-lg-12">
    <div class="panel panel-default">
    <div class="panel-body">
    <div class="row">
    <div class="col-lg-12">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs">
        <li class="active"><a href="#toko-tab" data-toggle="tab">POS</a>
        </li>
        <li><a href="#printer-tab" data-toggle="tab">Printer</a>
        </li>
        <?php if ($module['ACCOUNTING'] == 1): ?>
            <li><a href="#account-tab" data-toggle="tab">Account</a>
            </li>
        <?php endif; ?>
        
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
    <div class="tab-pane fade in active" id="toko-tab" style="padding-top: 20px">
    <div class="col-lg-12">
    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Judul Site</label>

        <div class="col-sm-6">
            <?php echo form_input($site_title); ?>
        </div>
    </div>
    <div class="form-group" style="display: none;">
        <label for="tax_percentage" class="col-sm-3 control-label">Pemisah Judul</label>

        <div class="col-sm-6">
            <?php echo form_input($site_title_delimiter); ?>
        </div>
    </div>
    <!-- <div class="form-group">
                                                <label for="tax_name" class="col-sm-3 control-label">ID Toko</label>

                                                 <div class="col-sm-6">
                                                  <?php

    // echo form_dropdown('store_id', $store_lists, $this->form_validation->set_value('store_id', $form_data['store_id']),'id="store_id" field-name="nama toko" class="form-control " autocomplete="on"');

    ?>
                                                  <small>Pilih ID Toko ini.</small>
                                                </div>
                                            </div> -->
    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Alamat Server

        </label>

        <div class="col-sm-6">
            <?php echo form_input($server_base_url); ?>
            <small>http://your-domain-here/</small>
        </div>
    </div>
    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Private Key

        </label>

        <div class="col-sm-6">
            <?php echo form_input($private_key_resto); ?>
            <small>Isi agar sinkronisasi dapat dilakukan,bisa di dapatkan pada saat membuat store pada server.</small>
        </div>
    </div>
    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Tipe Dining</label>

        <div class="col-sm-6">
            <label class="radio-inline">
                <?php echo form_radio($dining_type_1); ?>
                <small>Casual Dining</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($dining_type_2); ?>
                <small>Fast Casual Dining</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($dining_type_3); ?>
                <small>Hybrid Casual Dining</small>
            </label>
            <br>

            <small>perbedaannya ada pada saat kasir melakukan order.</small>

        </div>
    </div>
    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Notifikasi</label>

        <div class="col-sm-6">
            <label class="radio-inline">
                <?php echo form_radio($notification_yes); ?>
                <small>Ya</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($notification_no); ?>
                <small>Tidak</small>
            </label>
            <br>

            <small>Apakah akan memakai notifikasi.</small>

        </div>
    </div>
    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Notifikasi Kontra Bon</label>

        <div class="col-sm-6">
            <label class="radio-inline">
                <?php echo form_radio($notification_kontra_bon_yes); ?>
                <small>Ya</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($notification_kontra_bon_no); ?>
                <small>Tidak</small>
            </label>
            <br>

            <small>Apakah akan memakai notifikasi kontra bon.</small>

        </div>
    </div>
    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Format Bill</label>

        <div class="col-sm-6">
            <label class="radio-inline">
                <?php echo form_radio($bill_auto_number_sequence); ?>
                <small>Berurutan</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($bill_auto_number_partial_random); ?>
                <small>Acak Sebagian</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($bill_auto_number_random); ?>
                <small>Acak Sepenuhnya</small>
            </label>
            <br>

            <small>Format nomor bill apakah berurutan atau acak</small>

        </div>
    </div>

    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Metode Perhitungan Tax & Service</label>

        <div class="col-sm-6">
            <label class="radio-inline">
                <?php echo form_radio($tax_service_method_1); ?>
                <small>Metode 1</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($tax_service_method_2); ?>
                <small>Metode 2</small>
            </label>
            <br>

            <small>Perhitungan service apakah masuk pajak atau tidak.</small>

        </div>
    </div>

    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Metode Stok</label>

        <div class="col-sm-6">
            <label class="radio-inline">
                <?php echo form_radio($stock_method_fifo); ?>
                <small>FIFO</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($stock_method_average); ?>
                <small>AVERAGE</small>
            </label>
            <br>

            <small>Metode stok yang digunakan FIFO / AVERAGE</small>

        </div>
    </div>

    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Metode Opname</label>

        <div class="col-sm-6">
            <label class="radio-inline">
                <?php echo form_radio($opname_method_different); ?>
                <small>SELISIH</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($opname_method_existing); ?>
                <small>STOK AKHIR</small>
            </label>
            <br>

            <small>Input stok opname berupa SELISIH / STOK AKHIR</small>

        </div>
    </div>

    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Prioritas Perhitungan HPP</label>

        <div class="col-sm-6">
            <label class="radio-inline">
                <?php echo form_radio($cogs_count_menu); ?>
                <small>MENU</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($cogs_count_inventory); ?>
                <small>INVENTORY</small>
            </label>
            <br>

            <small>Prioritas perhitungan menu HPP yang digunakan dari MENU / INVENTORY</small>

        </div>
    </div>
    <!-- <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Target Printer untuk print list menu</label>

        <div class="col-sm-6">
            <label class="radio-inline">
                <?php echo form_radio($target_print_list_menu_cashier); ?>
                <small>Kasir</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($target_print_list_menu_checker); ?>
                <small>Checker</small>
            </label>
            <br>

            <small>target printer yang digunakan saat tombol print list menu di kasir diklik.</small>

        </div>
    </div> -->
    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Warna Background Order</label>

        <div class="col-sm-6">
            <label class="radio-inline">
                <?php echo form_radio($use_primary_additional_color_yes); ?>
                <small>Ya</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($use_primary_additional_color_no); ?>
                <small>Tidak</small>
            </label>
            <br>

            <small>Apakah akan perbedaan warna background pada order.</small>

        </div>
    </div>
    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Warna Background Order Utama</label>

        <div class="col-sm-6">
            <?php

            echo form_dropdown('primary_bg_color', $color_lists, $this->form_validation->set_value('primary_bg_color', $form_data['primary_bg_color']), 'id="primary_bg_color" field-name="warna background order utama" class="form-control" autocomplete="on"');

            ?>
            <small>Warna background untuk orderan utama</small>
        </div>
    </div>
    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Warna Background Order Tambahan</label>

        <div class="col-sm-6">
            <?php

            echo form_dropdown('additional_bg_color', $color_lists, $this->form_validation->set_value('additional_bg_color', $form_data['additional_bg_color']), 'id="additional_bg_color" field-name="warna background order tambahan" class="form-control" autocomplete="on"');

            ?>
            <small>Warna background untuk orderan tambahan</small>
        </div>
    </div>
    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Warna Background Order Takeaway</label>

        <div class="col-sm-6">
            <?php

            echo form_dropdown('takeaway_bg_color', $color_lists, $this->form_validation->set_value('takeaway_bg_color', $form_data['takeaway_bg_color']), 'id="takeaway_bg_color" field-name="warna background order takeaway" class="form-control" autocomplete="on"');

            ?>
            <small>Warna background untuk orderan takeaway</small>
        </div>
    </div>
    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Metode Voucher</label>

        <div class="col-sm-9">
            <label class="radio-inline">
                <?php echo form_radio($voucher_method_1); ?>
                <img id='voucher_method_1_image' style="display:none;"
                     src="<?php echo base_url("assets/img/voucher_method_1.JPG") ?>"/>
                <small><a href="javascript:void(0);" id="show_voucher_method_1">Tipe 1</a></small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($voucher_method_2); ?>
                <img id='voucher_method_2_image' style="display:none;"
                     src="<?php echo base_url("assets/img/voucher_method_2.JPG") ?>"/>
                <small><a href="javascript:void(0);" id="show_voucher_method_2">Tipe 2</a></small>
            </label> <br>

            <small>Cara Penggunaan input voucher pada POS.</small>
        </div>
    </div>
    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Format Tutup Kasir</label>

        <div class="col-sm-9">
            <label class="radio-inline">
                <?php echo form_radio($open_close_format_1); ?>
                <small>Format 1</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($open_close_format_2); ?>
                <small>Format 2</small>
            </label>


            <label class="radio-inline">
                <?php echo form_radio($open_close_format_3); ?>
                <small>Format 3</small>
            </label> 
            <label class="radio-inline">
                <?php echo form_radio($open_close_format_4); ?>
                <small>Format 4</small>
            </label>

            <br>
            <small>Format Tutup Kasir</small>
        </div>
    </div>
    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Memakai Grup Checker</label>

        <div class="col-sm-9">
            <label class="radio-inline">
                <?php echo form_radio($checker_group_yes); ?>
                <small>Ya</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($checker_group_no); ?>
                <small>Tidak</small>
            </label> <br>

            <small>Memakai Grup Checker</small>
        </div>
    </div>
    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Input Cash On Hand</label>

        <div class="col-sm-9">
            <label class="radio-inline">
                <?php echo form_radio($coh_yes); ?>
                <small>Ya</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($coh_no); ?>
                <small>Tidak</small>
            </label> <br>

            <small>Input Cash On Hand pada saat closing kasir.</small>
        </div>
    </div>
    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Input Pengeluaran Admin</label>

        <div class="col-sm-9">
            <label class="radio-inline">
                <?php echo form_radio($cash_admin_yes); ?>
                <small>Ya</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($cash_admin_no); ?>
                <small>Tidak</small>
            </label> <br>

            <small>Input pengeluaran di backoffice.</small>
        </div>
    </div>

    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Order Out of Stock menu</label>

        <div class="col-sm-6">
            <label class="radio-inline">
                <input type="radio" name="zero_stock_order" value="1"
                    <?php
                    echo set_value('zero_stock_order', $zero_stock_order) == 1 ? "checked" : "";
                    ?>
                    />
                Ya
            </label>
            <label class="radio-inline">
                <input type="radio" name="zero_stock_order" value="0"
                    <?php
                    echo set_value('zero_stock_order', $zero_stock_order) == 0 ? "checked" : "";
                    ?>
                    />
                Tidak
            </label>
            <br>
            <small>Memungkinkan pelanggan untuk tetap pesan jika menu yang mereka pesan tidak ada dalam stok.</small>
        </div>
    </div>

    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Set Quantity Menu</label>

        <div class="col-sm-6">
            <label class="radio-inline">
                <?php echo form_radio($quantity_by_menu); ?>
                <small>MENU</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($quantity_by_inventory); ?>
                <small>INVENTORY</small>
            </label>
            <br>

            <small>Prioritas perhitungan jumlah stok menu berdasarkan MENU / INVENTORY</small>

        </div>
    </div>

    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Panjang Penempatan Meja</label>

        <div class="col-sm-6">
            <?php echo form_input($default_table_width); ?>
            <small>dalam pixel</small>

        </div>
    </div>
    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Lebar Penempatan Meja</label>

        <div class="col-sm-6">
            <?php echo form_input($default_table_height); ?>
            <small>dalam pixel</small>

        </div>
    </div>


    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">waktu lock reservasi</label>

        <div class="col-sm-6">
            <?php echo form_input($booking_start_lock); ?>
            <small>waktu untuk mulai menandai meja menjadi berstatus "booked" (dalam menit)</small>

        </div>
    </div>

    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Waktu selesai lock reservasi

        </label>

        <div class="col-sm-6">
            <?php echo form_input($booking_remove_lock); ?>
            <small>waktu untuk menghapus status "booked" jika meja tidak diisi (dalam menit)</small>
        </div>
    </div>
    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Jasa Ekspedisi Kurir</label>

        <div class="col-sm-9">
            <label class="radio-inline">
                <?php echo form_radio($delivery_company_yes); ?> 
                <small>Ya</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($delivery_company_no); ?> 
                <small> Tidak</small>
            </label> <br>

            <small>Apakah menggunakan jasa kurir lain.</small>
        </div>
    </div>
    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Biaya jasa kurir</label>

        <div class="col-sm-6">
            <?php echo form_input($courier_service); ?>
            <small>persentase biaya delivery order untuk kurir dihitung dari ongkos kirim.</small>
        </div>
    </div>
    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label"> Pembulatan</label>

        <div class="col-sm-6">
            <label class="radio-inline">
                <?php echo form_radio($nearest_round_0); ?>
                <small>Tidak</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($nearest_round_50); ?>
                <small>50</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($nearest_round_100); ?>
                <small>100</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($nearest_round_1000); ?>
                <small>1000</small>
            </label>

            <br>

            <small>Pembulatan ketika pembayaran.</small>

        </div>


    </div>
    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label"> Proses Kitchen</label>

        <div class="col-sm-6">
            <label class="radio-inline">
                <?php echo form_radio($use_kitchen_yes); ?>
                <small>Ya</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($use_kitchen_no); ?>
                <small>Tidak</small>
            </label>
            <br>

            <small>Apakah menggunakan proses pada kitchen.</small>

        </div>
    </div>

    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label"> Hitung Jumlah Proses Kitchen</label>

        <div class="col-sm-6">
            <label class="radio-inline">
                <?php echo form_radio($count_kitchen_process_yes); ?>
                <small>Ya</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($count_kitchen_process_no); ?>
                <small>Tidak</small>
            </label>
            <br>

            <small>Apakah jumlah proses cooking pada kitchen akan dihitung.</small>

        </div>
    </div>

    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label"> Hitung Waktu Proses Kitchen</label>

        <div class="col-sm-6">
            <label class="radio-inline">
                <?php echo form_radio($kitchen_timestamp_yes); ?>
                <small>Ya</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($kitchen_timestamp_no); ?>
                <small>Tidak</small>
            </label>
            <br>

            <small>Apakah waktu cooking pada kitchen akan dihitung.</small>

        </div>
    </div>

    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label"> Proses Checker</label>

        <div class="col-sm-6">
            <label class="radio-inline">
                <?php echo form_radio($use_role_checker_yes); ?>
                <small>Ya</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($use_role_checker_no); ?>
                <small>Tidak</small>
            </label>
            <br>

            <small>Apakah menggunakan proses checker</small>

        </div>
    </div>
    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label"> Proses Cleaning</label>

        <div class="col-sm-6">
            <label class="radio-inline">
                <?php echo form_radio($cleaning_process_yes); ?>
                <small>Ya</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($cleaning_process_no); ?>
                <small>Tidak</small>
            </label>
            <br>

            <small>Apakah menggunakan proses cleaning table.</small>

        </div>
    </div>
    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Tema yang digunakan</label>

        <div class="col-sm-6">
            <label class="radio-inline">
                <?php echo form_radio($theme_default); ?>
                <small>Default</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($theme_mini); ?>
                <small>Mode Mini</small>
            </label>

            <!-- <label class="radio-inline">
                <?php echo form_radio($theme_mobile); ?>
                <small>Mode Mobile</small>
            </label> -->
            <br>

            <small>Pilih tema yang akan digunakan. *akan berpengaruh ke tampilan POS.</small>

        </div>
    </div>
    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Support Mobile</label>

        <div class="col-sm-6">
            <label class="radio-inline">
                <?php echo form_radio($mobile_default); ?>
                <small>Default / Tidak</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($mobile_support); ?>
                <small>Support Mobile</small>
            </label>

            <br>

            <small>Pilih Support Tampilan Mobile / Tidak. *akan berpengaruh ke tampilan POS.</small>

        </div>
    </div>
    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Pilih Kategori Member</label>

        <div class="col-sm-6">
            <?php

            echo form_dropdown('member_karyawan_kategori_id', $member_category_lists, $this->form_validation->set_value('member_karyawan_kategori_id', $form_data['member_karyawan_kategori_id']), 'id="member_karyawan_kategori_id" field-name="member kategori karyawan" class="form-control" autocomplete="on"');

            ?>
            <small>Kategori Member yang termasuk dalam kategori karyawan</small>
        </div>


    </div>


    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Range Waktu Reservation</label>

        <div class="col-sm-4">
            <?php echo form_input($range_booking_time); ?>
            <small>(dalam menit)</small>

        </div>
    </div>

    
    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Metode Open Close</label>

        <div class="col-sm-9">
            <label class="radio-inline">
                <?php echo form_radio($open_close_metode_1); ?>
                <!-- <img id='voucher_method_1_image' style="display:none;"
                     src="<?php echo base_url("assets/img/voucher_method_1.JPG") ?>"/> -->
                <small><a href="javascript:void(0);" id="show_voucher_method_1">Tipe 1</a></small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($open_close_metode_2); ?>
                <!-- <img id='voucher_method_2_image' style="display:none;"
                     src="<?php echo base_url("assets/img/voucher_method_2.JPG") ?>"/> -->
                <small><a href="javascript:void(0);" id="show_voucher_method_2">Tipe 2</a></small>
            </label> <br>

            <small>Sistem Open Close Kasir pada POS.</small>
        </div>
    </div>

	<div class="form-group">
        <label for="check_server_before_close_transaction" class="col-sm-3 control-label">Cek Data Transaksi Server Sebelum Closing</label>

        <div class="col-sm-9">
            <label class="radio-inline">
                <?php echo form_radio($check_server_before_close_yes); ?> 
                <small>Ya</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($check_server_before_close_no); ?> 
                <small> Tidak</small>
            </label> <br>

            <small>Cek data transaksi di server sebelum closing cashier</small>
        </div>
    </div>

     <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Print Report Product</label>

        <div class="col-sm-9">
            <label class="radio-inline">
                <?php echo form_radio($auto_print_report_product_yes); ?> 
                <small>Ya</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($auto_print_report_product_no); ?> 
                <small> Tidak</small>
            </label> <br>

            <small>Auto Print Laporan Produk yang terjual ketika open close</small>
        </div>
    </div>
    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Print Report Kas Kecil</label>

        <div class="col-sm-9">
            <label class="radio-inline">
                <?php echo form_radio($auto_print_report_pettycash_yes); ?> 
                <small>Ya</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($auto_print_report_pettycash_no); ?> 
                <small> Tidak</small>
            </label> <br>

            <small>Auto Print Laporan Kas Kecil ketika open close</small>
        </div>
    </div>
    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Print Report Stock</label>

        <div class="col-sm-9">
            <label class="radio-inline">
                <?php echo form_radio($auto_print_report_stock_yes); ?> 
                <small>Ya</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($auto_print_report_stock_no); ?> 
                <small> Tidak</small>
            </label> <br>

            <small>Auto Print Laporan Stock ketika open close</small>
        </div>
    </div>

    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">View Report Total Customer</label>

        <div class="col-sm-9">
            <label class="radio-inline">
                <?php echo form_radio($report_total_customer_yes); ?> 
                <small>Ya</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($report_total_customer_no); ?> 
                <small> Tidak</small>
            </label> <br>

            <small>Menampilkan grafik trend jumlah customer by day per bulan existing.</small>
        </div>
    </div>

    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">View Report Summary Sales Today</label>

        <div class="col-sm-9">
            <label class="radio-inline">
                <?php echo form_radio($summary_sales_on_cashier_yes); ?> 
                <small>Ya</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($summary_sales_on_cashier_no); ?> 
                <small> Tidak</small>
            </label> <br>

            <small>Menampilkan total sales hari ini di halaman kasir.</small>
        </div>
    </div>

    <div class="form-group">
        <label for="tax_percentage" class="col-sm-3 control-label">Besaran Revenue Sharing</label>

        <div class="col-sm-6">
            <?php echo form_input($revenue_sharing); ?>
            <small>Besaran nilai revenue sharing.</small>

        </div>
    </div>
    
    </div>
    </div>

    <div class="tab-pane fade in" id="printer-tab">

    <div class="col-lg-12">
    <div class="clearfix"></div>
    <div class="form-group">
        <label for="printer_logo" class="col-sm-2 control-label">Logo Bill</label>

        <div class="col-sm-4">
            <?php echo form_input($printer_logo); ?>
            <small>*hanya BMP, max size 1 MB (150px x 81px)</small>
        </div>
    </div>
    <?php
    if (!empty($form_data['printer_logo'])) {
        ?>
        <div class="form-group" id="primaryimage">
            <label for="pages_slug" class="col-sm-2 control-label">Logo Bill URL</label>

            <div class="col-sm-10">
                <img class="gc_thumbnail" src="<?php echo base_url($form_data['printer_logo']); ?>"
                     style="padding:5px; border:1px solid #ddd"/>
                <a href="javascript:void(0);"
                   url-data="<?php echo base_url(SITE_ADMIN . '/system/remove_printer_logo'); ?>"
                   class="btn btn-danger removeImageMenu"><i class="fa fa-trash-o"></i></a>
            </div>
        </div>
    <?php
    }
    ?>
    <div class="form-group">
        <label for="tax_percentage" class="col-sm-2 control-label">Otomatis Print</label>

        <div class="col-sm-10">
            <label class="radio-inline">
                <?php echo form_radio($auto_print_yes); ?>
                <small>Ya</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($auto_print_no); ?>
                <small>Tidak</small>
            </label>
            <br>

            <small>Apakah akan otomatis print ke kitchen/checker.</small>

        </div>
    </div>
    <div class="form-group">
        <label for="tax_percentage" class="col-sm-2 control-label">Otomatis Print Checker Waiter</label>

        <div class="col-sm-10">
            <label class="radio-inline">
                <?php echo form_radio($checker_auto_yes); ?>
                <small>Ya</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($checker_auto_no); ?>
                <small>Tidak</small>
            </label> <br>

            <small>Apakah Printer Checker Waiter Otomatis.</small>
        </div>
    </div>
    <div class="form-group">
        <label for="tax_percentage" class="col-sm-2 control-label">Otomatis Print Checker Dapur</label>

        <div class="col-sm-10">
            <label class="radio-inline">
                <?php echo form_radio($checker_kitchen_auto_yes); ?>
                <small>Ya</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($checker_kitchen_auto_no); ?>
                <small>Tidak</small>
            </label> <br>

            <small>Apakah Printer Checker Dapur Otomatis.</small>
        </div>
    </div>
    <div class="form-group">
        <label for="tax_percentage" class="col-sm-2 control-label">Print Nomor</label>

        <div class="col-sm-10">
            <label class="radio-inline">
                <?php echo form_radio($print_number_yes); ?>
                <small>Ya</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($print_number_no); ?>
                <small>Tidak</small>
            </label>
            <br>

            <small>Print nomor manual pada checker?</small>

        </div>
    </div>
    <!-- <div class="form-group">
        <label for="tax_percentage" class="col-sm-2 control-label">Font Size Bill</label>

        <div class="col-sm-10">
            <label class="radio-inline">
                <?php echo form_radio($font_size_bill_1); ?>
                <small>1</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($font_size_bill_2); ?>
                <small>2</small>
            </label>
            <br>

            <small>ukuran font pada bill,semakin besar angka semakin besar font sizenya.</small>

        </div>
    </div> -->
    <div class="form-group">
        <label for="tax_percentage" class="col-sm-2 control-label">Format Bill</label>

        <div class="col-sm-10">
            <label class="radio-inline">
                <?php echo form_radio($printer_format_1); ?>
                <img id='printer_format_1_image' style="display:none;"
                     src="<?php echo base_url("assets/img/printer_format_1.JPG") ?>"/>
                <small><a href="javascript:void(0);" id="show_printer_format_1">Tipe 1</a></small>
            </label>

            <label class="radio-inline">
                <img id='printer_format_2_image' style="display:none;"
                     src="<?php echo base_url("assets/img/printer_format_2.JPG") ?>"/>
                <?php echo form_radio($printer_format_2); ?>
                <img id='printer_format_2_image' style="display:none;"
                     src="<?php echo base_url("assets/img/printer_format_2.JPG") ?>"/>
                <small><a href="javascript:void(0);" id="show_printer_format_2">Tipe 2</a></small>
            </label> <br>

            <small>Format bill yang digunakan.</small>
        </div>
    </div>
    <!-- <div class="form-group">
        <label for="tax_name" class="col-sm-2 control-label">Printer Dot Matrix</label>

        <div class="col-sm-4">
            <?php echo form_input($printer_dot_matrix); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="tax_name" class="col-sm-2 control-label">Printer Kasir</label>
        <div class="col-sm-4">
            <?php echo form_input($printer_cashier); ?>
        </div>
    </div> -->
    <!-- <div class="form-group">
        <label for="tax_name" class="col-sm-2 control-label"></label>
        

         <div class="col-sm-4">

           <a href="print_test_cashier" class="btn btn-info" ><i class="fa fa-print"></i> Test Printer</a>

        </div>
 


    </div> -->
    <!-- <div class="form-group">
        <label for="tax_percentage" class="col-sm-2 control-label"> </label>

        <div class="col-sm-10">
            <label class="radio-inline">
                <?php echo form_radio($cashier_printer_width_48); ?>
                <small>Lebar 48</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($cashier_printer_width_72); ?>
                <small>Lebar 72</small>
            </label>
            <label class="radio-inline">
                <?php echo form_radio($cashier_printer_width_72_plus); ?>
                <small>Lebar 72+</small>
            </label>
            <br>
            <small>Kertas Printer Lebar 48mm / 72mm</small>
        </div>
    </div>
    <div class="form-group">
        <label for="tax_percentage" class="col-sm-2 control-label"> </label>

        <div class="col-sm-10">
            <label class="radio-inline">
                <?php echo form_radio($cashier_use_logo_yes); ?>
                <small>Ya</small>
            </label>

            <label class="radio-inline">
                <?php echo form_radio($cashier_use_logo_no); ?>
                <small>Tidak</small>
            </label> <br>

            <small>Apakah Menggunakan Logo.</small>
        </div>
    </div>
    <div class="form-group">
        <label for="tax_name" class="col-sm-2 control-label">Printer Dapur</label>

        <div class="col-sm-10">
            <a id="add_printer_kitchen" href="#" class="btn btn-success"><i
                    class='fa fa-plus'></i> Tambah</a>

        </div>

    </div>
    <?php if (!empty($printer_kitchen)) { ?>
        <div class="form-group">
            <label for="tax_percentage" class="col-sm-2 control-label"> </label>

            <div class="col-sm-10">
                <label class="radio-inline">
                    <?php echo form_radio($kitchen_printer_width_48); ?>
                    <small>Lebar 48</small>
                </label>

                <label class="radio-inline">
                    <?php echo form_radio($kitchen_printer_width_72); ?>
                    <small>Lebar 72</small>
                </label>
                <label class="radio-inline">
                    <?php echo form_radio($kitchen_printer_width_72_plus); ?>
                    <small>Lebar 72+</small>
                </label><br>
                <small>Kertas Printer Lebar 48mm / 72mm</small>
            </div>
        </div>
        <div class="form-group">
            <label for="tax_percentage" class="col-sm-2 control-label"> </label>

            <div class="col-sm-10">
                <label class="radio-inline">
                    <?php echo form_radio($kitchen_use_logo_yes); ?>
                    <small>Ya</small>
                </label>

                <label class="radio-inline">
                    <?php echo form_radio($kitchen_use_logo_no); ?>
                    <small>Tidak</small>
                </label> <br>

                <small>Apakah Menggunakan Logo.</small>
            </div>
        </div>
    <?php } ?>
    <div class="form-group">
        <label for="tax_name" class="col-sm-2 control-label"></label>

        <div class="col-sm-10">
            <table class="table table-striped" id="printer_kitchen_container"
                >
                <?php
                $counter = 0;
                if (!empty($printer_kitchen)) {
                    echo '
                                                 <tr>
                                                 <td>
                                                 <div class="row ">
                                                 <div class="col-md-10 col-md-offset-1">
                                                 <div class="col-md-4">Outlet</div>
                                                 <div class="col-md-4">Nama Printer</div>
                                                 </div>
                                                 </div>
                                                 </tr>
                                                 </td>
                                                   ';
                    foreach ($printer_kitchen as $k => $row) {
                        add_printer_kitchen_func($row, $counter, $form_data, $all_outlet);
                        $counter++;
                    }

                } else {
                    echo "<h4>tidak ada printer dapur</h4>";
                }
                ?>
            </table>
        </div>
    </div>

    <div class="form-group">
        <label for="tax_name" class="col-sm-2 control-label">Checker/Service</label>

        <div class="col-sm-10">
            <a id="add_printer_checker" href="#" class="btn btn-success"><i
                    class='fa fa-plus'></i> Tambah</a>

        </div>

    </div>
    <?php if (!empty($printer_checker)) { ?>
        <div class="form-group">
            <label for="tax_percentage" class="col-sm-2 control-label"> </label>

            <div class="col-sm-10">
                <label class="radio-inline">
                    <?php echo form_radio($checker_printer_width_48); ?>
                    <small>Lebar 48</small>
                </label>

                <label class="radio-inline">
                    <?php echo form_radio($checker_printer_width_72); ?>
                    <small>Lebar 72</small>
                </label>
                <label class="radio-inline">
                    <?php echo form_radio($checker_printer_width_72_plus); ?>
                    <small>Lebar 72+</small>
                </label><br>
                <small>Kertas Printer Lebar 48mm / 72mm</small>
            </div>
        </div>
        <div class="form-group">
            <label for="tax_percentage" class="col-sm-2 control-label"> </label>

            <div class="col-sm-10">
                <label class="radio-inline">
                    <?php echo form_radio($checker_use_logo_yes); ?>
                    <small>Ya</small>
                </label>

                <label class="radio-inline">
                    <?php echo form_radio($checker_use_logo_no); ?>
                    <small>Tidak</small>
                </label> <br>

                <small>Apakah Menggunakan Logo.</small>
            </div>
        </div>
    <?php } ?>

    <div class="form-group">
        <label for="tax_name" class="col-sm-2 control-label"></label>

        <div class="col-sm-10">
            <table class="table table-striped" id="printer_checker_container"
                >
                <?php
                $counter = 0;
                if (!empty($printer_checker)) {
                    echo '
                                                 <tr>
                                                 <td>
                                                 <div class="row ">
                                                 <div class="col-md-10 col-md-offset-1">
                                                 <div class="col-md-4">Nama Printer</div>
                                                 </div>
                                                 </div>
                                                 </tr>
                                                 </td>
                                                   ';
                    foreach ($printer_checker as $k => $row) {
                        add_printer_checker_func($row, $counter, $form_data);
                        $counter++;
                    }

                } else {
                    echo "<h4>tidak ada printer Checker/Service</h4>";
                }
                ?>
            </table>
        </div>
    </div> <div class="form-group">
        <label for="tax_name" class="col-sm-2 control-label">Checker Dapur</label>

        <div class="col-sm-10">
            <a id="add_printer_checker_kitchen" href="#" class="btn btn-success"><i
                    class='fa fa-plus'></i> Tambah</a>

        </div>

    </div>
    <?php if (!empty($printer_checker_kitchen)) { ?>
        <div class="form-group">
            <label for="tax_percentage" class="col-sm-2 control-label"> </label>

            <div class="col-sm-10">
                <label class="radio-inline">
                    <?php echo form_radio($checker_kitchen_printer_width_48); ?>
                    <small>Lebar 48</small>
                </label>

                <label class="radio-inline">
                    <?php echo form_radio($checker_kitchen_printer_width_72); ?>
                    <small>Lebar 72</small>
                </label>
                <label class="radio-inline">
                    <?php echo form_radio($checker_kitchen_printer_width_72_plus); ?>
                    <small>Lebar 72+</small>
                </label><br>
                <small>Kertas Printer Lebar 48mm / 72mm</small>
            </div>
        </div>
        <div class="form-group">
            <label for="tax_percentage" class="col-sm-2 control-label"> </label>

            <div class="col-sm-10">
                <label class="radio-inline">
                    <?php echo form_radio($checker_kitchen_use_logo_yes); ?>
                    <small>Ya</small>
                </label>

                <label class="radio-inline">
                    <?php echo form_radio($checker_kitchen_use_logo_no); ?>
                    <small>Tidak</small>
                </label> <br>

                <small>Apakah Menggunakan Logo.</small>
            </div>
        </div>
    <?php } ?>
    <div class="form-group">
        <label for="tax_name" class="col-sm-2 control-label"></label>

        <div class="col-sm-10">
            <table class="table table-striped" id="printer_checker_kitchen_container"
                >
                <?php
                $counter = 0;
                if (!empty($printer_checker_kitchen)) {
                    echo '
                                                 <tr>
                                                 <td>
                                                 <div class="row ">
                                                 <div class="col-md-10 col-md-offset-1">
                                                 <div class="col-md-4">Outlet</div>
                                                 <div class="col-md-4">Nama Printer</div>
                                                 </div>
                                                 </div>
                                                 </tr>
                                                 </td>
                                                   ';
                    foreach ($printer_checker_kitchen as $k => $row) {
                        add_printer_checker_kitchen_func($row, $counter, $form_data, $all_outlet);
                        $counter++;
                    }

                } else {
                    echo "<h4>tidak ada printer Checker Dapur</h4>";
                }
                ?>
            </table>
        </div>
    </div>    
    <div class="form-group">
        <label for="tax_name" class="col-sm-2 control-label">Printer Gudang</label>

        <div class="col-sm-4">
            <?php echo form_input($printer_warehouse); ?>
        </div>

    </div>
    <div class="form-group">
        <label for="tax_name" class="col-sm-2 control-label">Printer Dot Matrix HRD</label>

        <div class="col-sm-4">
            <?php echo form_input($printer_hrd); ?>
        </div>

    </div> -->
    <div class="clearfix"></div>
    </div>
    

    </div>
    <?php if ($module['ACCOUNTING'] == 1): ?>
        <div class="tab-pane fade in" id="account-tab">
        <div class="col-lg-12">
        <div class="form-group">
            <label for="tax_name" class="col-sm-2 control-label">Persediaan</label>

            <div class="col-sm-6">
                <?php

                echo form_dropdown('inventory_account_id',
                    $accounts,
                    $this->form_validation->set_value('inventory_account_id', $form_data['inventory_account_id']),
                    'id="accounts_id" field-name="Account " class="form-control " autocomplete="on"');

                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="tax_name" class="col-sm-2 control-label">Kas</label>

            <div class="col-sm-6">
                <?php

                echo form_dropdown('cash_account_id',
                    $accounts,
                    $this->form_validation->set_value('cash_account_id', $form_data['cash_account_id']),
                    'id="accounts_id" field-name="Account " class="form-control " autocomplete="on"');

                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="tax_name" class="col-sm-2 control-label">Kas Sementara</label>

            <div class="col-sm-6">
                <?php

                echo form_dropdown('temporary_cash_account_id',
                    $accounts,
                    $this->form_validation->set_value('temporary_cash_account_id', $form_data['temporary_cash_account_id']),
                    'id="accounts_id" field-name="Account " class="form-control " autocomplete="on"');

                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="tax_name" class="col-sm-2 control-label">Prive</label>

            <div class="col-sm-6">
                <?php

                echo form_dropdown('prive_account_id',
                    $accounts,
                    $this->form_validation->set_value('prive_account_id', $form_data['prive_account_id']),
                    'id="accounts_id" field-name="Account " class="form-control " autocomplete="on"');

                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="tax_name" class="col-sm-2 control-label">Kredit Receivable</label>

            <div class="col-sm-6">
                <?php

                echo form_dropdown('credit_receivable_account_id',
                    $accounts,
                    $this->form_validation->set_value('credit_receivable_account_id', $form_data['credit_receivable_account_id']),
                    'id="accounts_id" field-name="Account " class="form-control " autocomplete="on"');

                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="tax_name" class="col-sm-2 control-label">Voucher Account</label>

            <div class="col-sm-6">
                <?php

                echo form_dropdown('voucher_account_id',
                    $accounts,
                    $this->form_validation->set_value('voucher_account_id', $form_data['voucher_account_id']),
                    'id="accounts_id" field-name="Account " class="form-control " autocomplete="on"');

                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="tax_name" class="col-sm-2 control-label">Debit Receivable</label>

            <div class="col-sm-6">
                <?php

                echo form_dropdown('debit_receivable_account_id',
                    $accounts,
                    $this->form_validation->set_value('debit_receivable_account_id', $form_data['debit_receivable_account_id']),
                    'id="accounts_id" field-name="Account " class="form-control " autocomplete="on"');

                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="tax_name" class="col-sm-2 control-label">Flazz Receivable</label>

            <div class="col-sm-6">
                <?php

                echo form_dropdown('flazz_receivable_account_id',
                    $accounts,
                    $this->form_validation->set_value('flazz_receivable_account_id', $form_data['flazz_receivable_account_id']),
                    'id="accounts_id" field-name="Account " class="form-control " autocomplete="on"');

                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="tax_name" class="col-sm-2 control-label">Cogs Account</label>

            <div class="col-sm-6">
                <?php

                echo form_dropdown('cogs_account_id',
                    $accounts,
                    $this->form_validation->set_value('cogs_account_id', $form_data['cogs_account_id']),
                    'id="accounts_id" field-name="Account " class="form-control " autocomplete="on"');

                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="tax_name" class="col-sm-2 control-label">Income Account</label>

            <div class="col-sm-6">
                <?php

                echo form_dropdown('income_account_id',
                    $accounts,
                    $this->form_validation->set_value('income_account_id', $form_data['income_account_id']),
                    'id="accounts_id" field-name="Account " class="form-control " autocomplete="on"');

                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="tax_name" class="col-sm-2 control-label">Tax</label>

            <div class="col-sm-6">
                <?php

                echo form_dropdown('tax_account_id',
                    $accounts,
                    $this->form_validation->set_value('tax_account_id', $form_data['tax_account_id']),
                    'id="accounts_id" field-name="Account " class="form-control " autocomplete="on"');

                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="tax_name" class="col-sm-2 control-label">Pembulatan</label>

            <div class="col-sm-6">
                <?php

                echo form_dropdown('pembulatan_account_id',
                    $accounts,
                    $this->form_validation->set_value('pembulatan_account_id', $form_data['pembulatan_account_id']),
                    'id="accounts_id" field-name="Account " class="form-control " autocomplete="on"');

                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="tax_name" class="col-sm-2 control-label">Piutang Dagang</label>

            <div class="col-sm-6">
                <?php

                echo form_dropdown('piutang_dagang_account_id',
                    $accounts,
                    $this->form_validation->set_value('piutang_dagang_account_id', $form_data['piutang_dagang_account_id']),
                    'id="accounts_id" field-name="Account " class="form-control " autocomplete="on"');

                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="tax_name" class="col-sm-2 control-label">Piutang Karyawan</label>

            <div class="col-sm-6">
                <?php

                echo form_dropdown('piutang_karyawan_account_id',
                    $accounts,
                    $this->form_validation->set_value('piutang_karyawan_account_id', $form_data['piutang_karyawan_account_id']),
                    'id="accounts_id" field-name="Account " class="form-control " autocomplete="on"');

                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="tax_name" class="col-sm-2 control-label">Bank Sementara</label>

            <div class="col-sm-6">
                <?php

                echo form_dropdown('temporary_bank_account_id',
                    $accounts,
                    $this->form_validation->set_value('temporary_bank_account_id', $form_data['temporary_bank_account_id']),
                    'id="accounts_id" field-name="Account " class="form-control " autocomplete="on"');

                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="tax_name" class="col-sm-2 control-label">Diskon</label>

            <div class="col-sm-6">
                <?php

                echo form_dropdown('discount_account_id',
                    $accounts,
                    $this->form_validation->set_value('discount_account_id', $form_data['discount_account_id']),
                    'id="accounts_id" field-name="Account " class="form-control" autocomplete="on"');

                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="tax_name" class="col-sm-2 control-label">Delivery Cost</label>

            <div class="col-sm-6">
                <?php

                echo form_dropdown('delivery_cost_account_id',
                    $accounts,
                    $this->form_validation->set_value('delivery_cost_account_id', $form_data['delivery_cost_account_id']),
                    'id="accounts_id" field-name="Account " class="form-control " autocomplete="on"');

                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="tax_name" class="col-sm-2 control-label">Hutang DP</label>

            <div class="col-sm-6">
                <?php

                echo form_dropdown('hutang_dp_account_id',
                    $accounts,
                    $this->form_validation->set_value('hutang_dp_account_id', $form_data['hutang_dp_account_id']),
                    'id="accounts_id" field-name="Account " class="form-control " autocomplete="on"');

                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="tax_name" class="col-sm-2 control-label">DP Bank</label>

            <div class="col-sm-6">
                <?php

                echo form_dropdown('bank_dp_account_id',
                    $accounts,
                    $this->form_validation->set_value('bank_dp_account_id', $form_data['bank_dp_account_id']),
                    'id="accounts_id" field-name="Account " class="form-control " autocomplete="on"');

                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="tax_name" class="col-sm-2 control-label">Pendapatan Lainnya</label>

            <div class="col-sm-6">
                <?php

                echo form_dropdown('other_income_account_id',
                    $accounts,
                    $this->form_validation->set_value('other_income_account_id', $form_data['other_income_account_id']),
                    'id="accounts_id" field-name="Account " class="form-control " autocomplete="on"');

                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="tax_name" class="col-sm-2 control-label">Kas Kecil</label>

            <div class="col-sm-6">
                <?php

                echo form_dropdown('petty_cash_account_id',
                    $accounts,
                    $this->form_validation->set_value('petty_cash_account_id', $form_data['petty_cash_account_id']),
                    'id="accounts_id" field-name="Account " class="form-control " autocomplete="on"');

                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="tax_name" class="col-sm-2 control-label">Biaya Lainnya</label>

            <div class="col-sm-6">
                <?php

                echo form_dropdown('other_cost_account_id',
                    $accounts,
                    $this->form_validation->set_value('other_cost_account_id', $form_data['other_cost_account_id']),
                    'id="accounts_id" field-name="Account " class="form-control " autocomplete="on"');

                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="tax_name" class="col-sm-2 control-label">Biaya Jasa Kurir</label>

            <div class="col-sm-6">
                <?php

                echo form_dropdown('courier_service_cost_account_id',
                    $accounts,
                    $this->form_validation->set_value('courier_service_cost_account_id', $form_data['courier_service_cost_account_id']),
                    'id="accounts_id" field-name="Account " class="form-control " autocomplete="on"');

                ?>
            </div>
        </div>
        </div>

        </div>
    <?php endif; ?>
    
        <!-- <div class="tab-pane fade in" id="printertest-tab">
            <div class="col-lg-12">
                <div class="form-group">
                        <label for="printer_test" class="col-sm-2 control-label">Printer Test</label>

                        <div class="col-sm-4">
                         <?php echo form_input($printer_test); ?>
                        </div>
                </div>

        </div> -->
    </div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" value="save" id="btn-save-setting"
                    class="btn btn-primary"><?php echo $this->lang->line('ds_submit_save'); ?>
            </button>

        </div>
    </div>
    
    </div>
    <!-- /.row (nested) -->
    </div>
    <!-- /.panel-body -->
    </div>
    <!-- /.panel -->
    </div>
    </div>
    </div>
    </div>
    <input type="hidden" id="data_outlet" value='<?php echo json_encode($all_outlet) ?>'/>

<?php echo form_hidden($csrf); ?>
<?php echo form_input($printer_kitchen_count); ?>
<?php echo form_input($printer_checker_count); ?>
<?php echo form_input($printer_checker_kitchen_count); ?>
<?php echo form_close(); ?>

<?php
function add_printer_checker_func($printer_checker, $count, $form_data)
{
    $stuff = '
    <tr id="printer_checker_' . $count . '" class="count_printer_checker">
        <td>
            <div class="row ">
                <div class="col-md-10 col-md-offset-1">

                <div class="col-md-4"><input type="text"  class="form-control requiredTextField"
                                             id="printer_checker_' . $count . '"
                                             field-name="nama printer" placeholder=""
                                             name="printer_checker[' . $count . '][printer_name]"
                                             value="' . $printer_checker->printer_name . '"  /></div>
                

                <div class="col-md-1">
                    <button id="remove_printer_checker_' . $count . '" type="button"
                            class="btn btn-mini btn-danger pull-right">
                        <i class="fa fa-trash-o"></i></button>
                </div>
            </div>

            </div>
        </td>
    </tr>
    ';
    echo replace_newline($stuff);
}
function add_printer_checker_kitchen_func($printer_checker_kitchen, $count, $form_data, $outlet_ddl)
{
    $stuff = '
    <tr id="printer_checker_kitchen_' . $count . '" class="count_printer_checker_kitchen">
        <td>
            <div class="row ">
                <div class="col-md-10 col-md-offset-1">
                <div class="col-md-4">' .
                    form_dropdown('printer_checker_kitchen[' . $count . '][outlet_checker_kitchen_id]', $outlet_ddl, (int)$printer_checker_kitchen->outlet_id, 'id="printer_checker_kitchen_id_chained_' . $count . '" field-name = "Outlet"
                                class="form-control requiredDropdown printer_checker_kitchen_id_chained" autocomplete="off" url-data="' . base_url(SITE_ADMIN) . '/menus/get_inventory_unit" ') .
                    '
                                                </div>

                <div class="col-md-4"><input type="text"  class="form-control requiredTextField"
                                             id="printer_checker_kitchen_' . $count . '"
                                             field-name="nama printer" placeholder=""
                                             name="printer_checker_kitchen[' . $count . '][printer_name]"
                                             value="' . $printer_checker_kitchen->printer_name . '"  /></div>
                

                <div class="col-md-1">
                    <button id="remove_printer_checker_kitchen_' . $count . '" type="button"
                            class="btn btn-mini btn-danger pull-right">
                        <i class="fa fa-trash-o"></i></button>
                </div>
            </div>

            </div>
        </td>
    </tr>
    ';
    echo replace_newline($stuff);
}

function add_printer_kitchen_func($printer_kitchen, $count, $form_data, $outlet_ddl)
{
    $stuff = '
    <tr id="printer_kitchen_' . $count . '" class="count_printer_kitchen">
        <td>
            <div class="row ">
                <div class="col-md-10 col-md-offset-1">
                <div class="col-md-4">' .
        form_dropdown('printer_kitchen[' . $count . '][outlet_id]', $outlet_ddl, (int)$printer_kitchen->outlet_id, 'id="printer_kitchen_id_chained_' . $count . '" field-name = "Outlet"
                    class="form-control requiredDropdown printer_kitchen_id_chained" autocomplete="off" url-data="' . base_url(SITE_ADMIN) . '/menus/get_inventory_unit" ') .
        '
                                    </div>

       <div class="col-md-4"><input type="text"  class="form-control requiredTextField"
                                    id="printer_kitchen_unit_chained_' . $count . '"
                                             field-name="nama printer" placeholder="Satuan"
                                             name="printer_kitchen[' . $count . '][printer_name]"
                                             value="' . $printer_kitchen->printer_name . '"  /></div>
               
                <div class="col-md-1">
                    <button id="remove_printer_kitchen_' . $count . '" type="button"
                            class="btn btn-mini btn-danger pull-right">
                        <i class="fa fa-trash-o"></i></button>
                </div>
            </div>

            </div>
        </td>
    </tr>
    ';
    echo replace_newline($stuff);
}

function replace_newline($string)
{
    return trim((string)str_replace(array("\r", "\r\n", "\n", "\t"), ' ', $string));
}
?>
