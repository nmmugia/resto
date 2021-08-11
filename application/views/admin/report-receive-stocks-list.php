<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      Diky Pratansyah <pratansyah@gmail.com>
 * @copyright   2015 Digital Oasis
 * @since       2.0.0
 */

?>
<style>
   .form-group label{
            text-align: left !important;
    }
</style>
<link href="<?php echo base_url(); ?>assets/css/bootstrap-datetimepicker.css" rel="stylesheet">
<div class="col-lg-12">
  <div class="panel panel-default"> 
        <div class="panel-body">
        <form id="formFilter" class="form-horizontal" method="POST">
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="col-sm-5 control-label">Tanggal Awal</label>
                   <div class="col-sm-7">
                        <div class='input-group date ' id='start_date_picker'>
                          <input type="text" class="form-control no-special-char" onkeydown="return false" 
                          name="start_date" id="start_date"
                            value=""> 
                          <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                        </div>   
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-5 control-label">Tanggal Akhir</label>
                    <div class="col-sm-7">
                        <div class='input-group date ' id='end_date_picker'>
                          <input type="text" class="form-control no-special-char" onkeydown="return false" 
                          name="end_date" id="end_date"
                            value=""> 
                          <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                        </div>   
                    </div>
                </div>
               
            </div>

             <div class="col-lg-6">
                <div class="form-group">
                    <label class="col-sm-4 control-label">Supplier</label>
                    <div class="col-sm-8">
                         <select class="form-control" name="supplier_id" id="supplier_id">
                           <option value="0">All</option>
                            <?php foreach($suppliers as $supplier): ?>
                                  <option value="<?php echo $supplier->id; ?>"><?php echo $supplier->name; ?></option>
                              <?php endforeach ?>
                          </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Bahan</label>
                    <div class="col-sm-8">
                        <select class="form-control" name="inventory_id" id="inventory_id">
                          <option value="0">All</option>
                            <?php foreach($inventories as $inventory): ?>
                                  <option value="<?php echo $inventory->id; ?>"><?php echo $inventory->name; ?></option>
                              <?php endforeach ?>
                          </select>
                    </div>
                </div>
                 
            </div>
            <div class="col-lg-12">
                <div class="form-group">
                    <label class="col-sm-6 control-label"> </label>
                    <div class="col-sm-6">
                       <button id="filter_submit" class=" btn btn-primary">Filter</button>
                     
                        <button id="export_pdf" class="hide_btn btn btn-primary" style="display: none">Export PDF</button>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
<div class="col-lg-12"> 
    <div class="panel panel-default">
       
        <div class="panel-heading">

            <div class="clearfix"></div>
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body"> 

            <table class="table table-striped table-bordered table-hover dt-responsive" cellspacing="0" width="100%" id="table-report-receive">
                <thead>
                <tr>
                    <th>Tanggal Kedatangan</th>
                    <th>No Pembayaran</th>
                    <th>Supplier</th>
                    <th>Nama Bahan</th>
                    <th>Jumlah Item</th>
                    <th>Harga Per Item</th>
                    <th>Total </th>
                </tr>
                </thead>
               <!--  <tr>
                    <th colspan="9" align="center" valign="middle"><?php echo $this->lang->line('empty_data'); ?></th>
                </tr> --> 
            </table>

            <input type="hidden" id="dataProcessUrl"
                   value="<?php echo $data_url; ?>"/>

                     <input type="hidden" id="is_in_report_page"
                   value="true"/>
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->

<script data-main="<?php echo base_url('assets/js/main-admin'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>