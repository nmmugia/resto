<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

?>
<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <table class="col-lg-4 form-table-cms">
                <tbody>
                   <tr>
                        <td>Resto</td>
                        <td>: <?php echo $bill_detail->store; ?></td>
                    </tr>
                     <tr>
                        <td>Mulai</td>                        
                        <td>: <?php echo $bill_detail->start_order; ?></td>
                    </tr>
                    
                    <tr>
                        <td>Selesai</td>
                        <td>: <?php echo $bill_detail->end_order; ?></td>
                    </tr>

                    <tr>
                        <td>Kasir</td>
                        <td>: <?php echo $bill_detail->cashier; ?></td>
                    </tr>


                    <!-- <tr>
                        <td>Jumlah Tamu</td>
                        <td>: <?php// echo $bill_detail->cashier; ?></td>
                    </tr>
 -->
                    <?php if($bill_detail->is_take_away==0 && $bill_detail->is_delivery==0): ?>
                    <tr>
                        <td>Meja</td>
                        <td>: <?php echo $bill_detail->table_name; ?></td>
                    </tr>
                    <?php elseif($bill_detail->is_take_away==1): ?>
                    <tr>
                        <td>Nama</td>
                        <td>: <?php echo $bill_detail->customer_name; ?></td>
                    </tr>
                    <?php elseif($bill_detail->is_delivery==1): ?>
                    <tr>
                        <td>Nama</td>
                        <td>: <?php echo $bill_detail->customer_name; ?></td>
                    </tr>
                    <tr>
                        <td>Telepon</td>
                        <td>: <?php echo $bill_detail->customer_phone; ?></td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>: <?php echo $bill_detail->customer_address; ?></td>
                    </tr>
                    <?php endif; ?>

                    <tr>
                        <td>Nomor Bill</td>
                        <td>: <?php echo $bill_detail->receipt_number; ?></td>
                    </tr>

                    <tr>
                        <td>Grand Total</td>
                        <td>: <?php echo $bill_detail->total_price; ?></td>
                    </tr>

                    <tr>
                        <td>HPP</td>
                        <td>: <?php echo $bill_detail->total_cogs; ?></td>
                    </tr>

                    <tr>
                        <td>Profit</td>
                        <td>: <?php echo $bill_detail->profit; ?></td>
                    </tr>
                </tbody>
            </table>
            
            <div class="clearfix"></div>
           <table class="col-lg-6 form-table-cms">
            <thead>
                <tr>
                    <td style="width:40%"><label>Menu</label></td>
                    <td class="border-side" style="width:20%"><label>Jumlah</label></td>
                    <td style="width:40%;text-align:center"><label>Harga</label></td>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($order_list)) {
                    //echo $order_list;
                } ?>
            </tbody>
        </table>
        <table class=" col-lg-6 form-table-cms">
            <tbody>
                <?php if (!empty($order_bill)) {
                    //echo $order_bill;
                } ?>
            </tbody>
        </table>

        <table class="col-lg-6" style="width: 100%;height: 100px;" id="paymentAmount">
        </table>

            <div class="clearfix"></div>
        </div>
        <!-- /.panel-heading -->
      
    </div>
    <!-- /.panel -->
</div>
<input type="hidden" id="report_type" value="sales"/>
<input type="hidden" id="admin_url" value="<?php echo base_url(SITE_ADMIN) ?>"/>
<!-- /.col-lg-12 -->

<!-- /.panel-hidden -->
<div class="panel-hidden" style="display:none">

    <h1 class="page-header"><?php echo $this->lang->line('sales_report_title'); ?></h1>
    <table class="col-lg-12 form-table-cms" style="width:100%;">
        <tbody>
        <tr>
            <td><label><?php echo $this->lang->line('column_start_date'); ?></label></td>
            <td>:</td>
            <td><label id='start_date_hidden'></label></td>
            <td><label> <?php echo $this->lang->line('column_store'); ?></label></td>
            <td>:</td>
            <td><label id='store_hidden'></label></td>
        </tr>
        <tr>
            <td><label><?php echo $this->lang->line('column_end_date'); ?></label></td>
            <td>:</td>
            <td><label id='end_date_hidden'></label></td>
            <td><label><?php echo $this->lang->line('column_outlet'); ?></label></td>
            <td>:</td>
            <td><label id='outlet_hidden'></label></td>
        </tr>
        <tr>
            <td><label><?php echo $this->lang->line('column_date_month'); ?></label></td>
            <td>:</td>
            <td><label id='month_hidden'></label></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td><label><?php echo $this->lang->line('column_date_year'); ?></label></td>
            <td>:</td>
            <td><label id='year_hidden'></label></td>
            <td></td>
            <td></td>
        </tr>

         <tr>
            <td><label>payment</label></td>
            <td>:</td>
            <td><label id='payment_hidden'></label></td>
            <td></td>
            <td></td>
        </tr>

        </tbody>
    </table>

    <table id="table-sales-hidden" class="sales-table" style="border-collapse: collapse;width:100%;">
        <tdead class="custom-hidden-table">
        </thead>
    </table>

    <!-- /.table-responsive -->
</div>
<!-- /.panel-hidden -->