<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

?>
<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <table class="col-lg-8 form-table-cms">
                <tbody>
                   <tr>
                        <td>Resto</td>
                        <td>: <?php echo $menu_detail->store; ?></td>
                    </tr>
                     <tr>
                        <td>Mulai</td>                        
                        <td>: <?php echo ($start_date) ? $start_date : "-"; ?></td>
                    </tr>
                    
                    <tr>
                        <td>Selesai</td>
                        <td>: <?php echo ($end_date) ? $end_date : "-"; ?></td>
                    </tr>

                    <tr>
                        <td>Menu</td>
                        <td>: <?php echo $menu_detail->menu_name; ?></td>
                    </tr>

                    <tr>
                        <td>Grand Total</td>
                        <td>: <?php echo convert_rupiah($menu_detail->total_price); ?></td>
                    </tr>

                    <tr>
                        <td>HPP</td>
                        <td>: <?php echo convert_rupiah($menu_detail->total_cogs); ?></td>
                    </tr>

                    <tr>
                        <td>Profit</td>
                        <td>: <?php echo convert_rupiah($menu_detail->profit); ?></td>
                    </tr>
                </tbody>
            </table>
            
  
            <div class="clearfix"></div>
        </div>
        <!-- /.panel-heading -->


        <div class="panel-body">

            <table class="table table-striped table-bordered table-hover dt-responsive" cellspacing="0" width="100%" id="table-sales-menu-detail">
                <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nomor Bill</th>
                    <th>Banyak</th>
                    <th>Grand Total</th>
                    <th>HPP</th>
                    <th>Profit</th>
                    <!-- <th>Aksi</th> -->
                </tr>
                </thead>
               
            </table>

            <input type="hidden" id="data_sales_menu_detail"
                   value="<?php echo $url_detail_menu; ?>"/>

            <div class="clearfix"></div>

        
        </div>
      
    </div>
    <!-- /.panel -->
</div>
<input type="hidden" id="report_type" value="sales_menu_detail"/>
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