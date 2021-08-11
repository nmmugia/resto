<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

?>
<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
<form id="formFilter" method="POST">
    <div class="clear-export"></div>
            <table class="col-lg-8 form-table-cms">
                <tbody>

                 <tr>
                    <td><label>Kasir</label></td>
                    <td>   <?php 
                    echo form_dropdown('user_id', $all_cashier, '', 
                        'id="user_id" field-name = "" 
                        class="form-control" autocomplete="on"');
                        ?></td>

                    </tr>

                    <tr>
                    <td><label>Waktu Mulai</label></td>
                    <td class="col-sm-8">
                            <div class='input-group date ' id='start_date'>
                             <?php echo form_input(array('name' => 'start_date',
                               'id' => 'input_start_date',
                               'type' => 'text',
                               'class' => 'form-control date',
                               'onkeydown'=>'return false'

                               )); ?>
                               <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar">
                                </span>
                            </span>
                        </div> 
                    </td>
                  
                </tr>
                <tr>
                    <td><label>Waktu Akhir</label></td>
                    <td class="col-sm-8">
                            <div class='input-group date ' id='end_date'>
                             <?php echo form_input(array('name' => 'end_date',
                               'id' => 'input_end_date',
                               'type' => 'text',
                               'class' => 'form-control date',
                               'onkeydown'=>'return false'

                               )); ?>
                               <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar">
                                </span>
                            </span>
                        </div> 
                    </td>
                    
                    
                </tr>
               
               
                <tr>
                    <td colspan="4">
                        <button id="filter_submit" style="float:right;">Filter</button>
                        <button id="export_xls" class="hide_btn" style="float:right;display: none">Export XLS</button>
                        <button id="export_pdf" class="hide_btn" style="float:right;display: none">Export PDF</button>
                    </td>
                </tr>
                </tbody>
            </table>
</form>

            <div class="clearfix"></div>
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">

             <table class="col-lg-6 form-table-cms">
                <tbody>

                 <tr>
                    <td><label>Periode</label></td>
                    <td><label id="periode"></label></td>

                    </tr>

                    <tr>
                        <td><label>Total Pendapatan</label></td>
                        <td><label id="total_price"></label></td>

                    </tr>

                    <tr>
                    <td><label>Total HPP</label></td>
                    <td><label id="total_cogs"></label></td>
                  
                </tr>
                <tr>
                    <td><label>Total Profit</label></td>
                    <td><label id="total_profit"></label></td>
                </tr>
                
                <tr>
                    <td><label>Total Transaksi</label></td>
                    <td><label id="total_transaction"></label></td>
                </tr>

                <tr>
                    <td><label>Total Customer</label></td>
                    <td><label id="total_customer"></label></td>
                </tr>

                <tr>
                    <td><label>Total Penjualan Menu</label></td>
                    <td><label id="total_quantity"></label></td>
                </tr>
                </tbody>
            </table>

            <div class="clearfix"></div>

            <table class="table table-striped table-bordered table-hover dt-responsive" cellspacing="0" width="100%" id="table-transaction">
                <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Receipt</th>
                    <th>Pembelian</th>
                    <th>Grand Total</th>
                    <th>Jumlah Item</th>
                    <th>Jumlah Pelanggan</th>
                    <th>Order ID</th>
                    <th>Aksi</th>
                </tr>
                </thead>
               <!--  <tr>
                    <th colspan="9" align="center" valign="middle"><?php echo $this->lang->line('empty_data'); ?></th>
                </tr> -->
            </table>

            <input type="hidden" id="dataProcessUrl"
                   value="<?php echo $data_url; ?>"/>

            <!-- /.table-responsive -->
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->
</div>
<input type="hidden" id="report_type" value="transaction"/>
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
        <thead class="custom-hidden-table">
        </thead>
    </table>

    <!-- /.table-responsive -->
</div>
<!-- /.panel-hidden -->