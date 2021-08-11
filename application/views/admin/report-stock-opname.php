<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

?>
<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <table class="col-lg-12 form-table-cms">
                <tbody>
                <tr>
                    <td><label><?php echo $this->lang->line('column_start_date'); ?></label></td>
                    <td><input id="start_date" type="text" class="date-input form-control"/></td>
                    <td><label><?php echo $this->lang->line('column_outlet'); ?></label></td>
                    <td>
                        <select id="outlet_sel" class="def-select form-control">
                            <option value="0" selected="selected">All</option>
                            <?php
                            foreach ($all_outlet as $outlet) {
                                ?>
                                <option
                                    value="<?php echo $outlet->outlet_id ?>"><?php echo $outlet->outlet_name . "-" . $outlet->store_name; ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label><?php echo $this->lang->line('column_end_date'); ?></label></td>
                    <td><input id="end_date" type="text" class="date-input form-control"/></td>
                    
                </tr>
                <tr>
                    <td><label><?php echo $this->lang->line('column_date_month'); ?></label></td>
                    <td>
                        <select id="month_sel" class="def-select form-control">
                            <option value="0" selected="selected">All</option>
                            <?php
                            $start    = new DateTime('2009-01-01');
                            $interval = new DateInterval('P1M');
                            $end      = new DateTime('2010-01-01');
                            $period   = new DatePeriod($start, $interval, $end);

                            foreach ($period as $dt) {

                                ?>
                                <option
                                    value="<?php echo $dt->format('n') ?>"><?php echo $dt->format('F') ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td><label><?php echo $this->lang->line('column_date_year'); ?></label></td>
                    <td>
                        <select id="year_sel" class="def-select form-control">
                            <option value="0" selected="selected">All</option>
                            <?php
                            $start    = new DateTime('2014-01-01');
                            $interval = new DateInterval('P1Y');
                            $end      = new DateTime();
                            $period   = new DatePeriod($start, $interval, $end);

                            foreach ($period as $dt) {

                                ?>
                                <option
                                    value="<?php echo $dt->format('Y') ?>"><?php echo $dt->format('Y') ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </td>
                    <td></td>
                    <td></td>
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


            <div class="clearfix"></div>
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">

            <table class="table table-striped table-bordered table-hover dt-responsive sales-table">
                <thead>
                <tr>
                    <th><?php echo $this->lang->line('column_date'); ?></th>
                    <th><?php echo $this->lang->line('column_outlet'); ?></th>
                    <th>Bahan</th>
                    <th>Jumlah terpakai</th>
                    <th>Sisa bahan</th>
                    <th>Stok opname</th>
                    <th>Satuan</th>
                </tr>
                </thead>
                <tr>
                    <th colspan="7" align="center" valign="middle"><?php echo $this->lang->line('empty_data'); ?></th>
                </tr>
            </table>

            <!-- /.table-responsive -->
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->
</div>
<input type="hidden" id="report_type" value="stock_opname"/>
<input type="hidden" id="admin_url" value="<?php echo base_url(SITE_ADMIN) ?>"/>
<!-- /.col-lg-12 -->

<!-- /.panel-hidden -->
<div class="panel-hidden" style="display:none">
    <h1 class="page-header">Laporan Penggunaan Bahan</h1>
    <table class="col-lg-12 form-table-cms" style="width:100%;">
        <tbody>
        <tr>
            <td><label><?php echo $this->lang->line('column_start_date'); ?></label></td>
            <td>:</td>
            <td><label id='start_date_hidden'></label></td>
            <td><label> Outlet</label></td>
            <td>:</td>
            <td><label id='outlet_hidden'></label></td>
        </tr>
        <tr>
            <td><label><?php echo $this->lang->line('column_end_date'); ?></label></td>
            <td>:</td>
            <td><label id='end_date_hidden'></label></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td><label><?php echo $this->lang->line('column_date_month'); ?></label></td>
            <td>:</td>
            <td><label id='month_hidden'></label></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td><label><?php echo $this->lang->line('column_date_year'); ?></label></td>
            <td>:</td>
            <td><label id='year_hidden'></label></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        </tbody>
    </table>
    <table class="sales-table" id="table-sales-hidden" style="border-collapse: collapse;width:100%;">
        <thead>
        <tr>
            <th><?php echo $this->lang->line('column_date'); ?></th>
            <th>Outlet</th>
            <th>Bahan</th>
            <th>Jumlah Terpakai</th>
            <th>Sisa Bahan</th>
            <th>Satuan</th>
            
        </tr>
        </thead>
        <tr>
            <th colspan="7" align="center" valign="middle"><?php echo $this->lang->line('empty_data'); ?></th>
        </tr>
    </table>

    <!-- /.table-responsive -->
</div>