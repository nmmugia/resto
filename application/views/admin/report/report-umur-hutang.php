<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      Diky Pratansyah <pratansyah@gmail.com>
 * @copyright   2015 Digital Oasis
 * @since       2.0.0
 */

?>
<div class="col-lg-12">
    <div class="panel panel-default">
       
        <div class="panel-heading">
          <div class="col-lg-7 pull-left" style="padding-left:0px;">
            <form id="formFilter" method="post">
            <input type="hidden" id="type" name="type" value="aging_report">
              <div class="clear-export"></div>
                  <table class="col-lg-8 form-table-cms">
                      <tbody>    
                          <tr>
                              <td><label>Supplier</label></td>
                              <td class="col-sm-8">
                                 <select class="form-control select2" name="supplier_id" id="supplier_id">
                                    <option value="">Semua Supplier</option>
                                    <?php foreach($supplier_lists as $supplier): ?>
                                        <option value="<?php echo $supplier->id; ?>"><?php echo $supplier->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                              </td>
                              
                          </tr>               
                          <tr>
                              <td><label>Per Tanggal</label></td>
                              <td class="col-sm-8">
                                  <div class='input-group date ' id='purchase_order_date'>
                                    <?php echo form_input(array('name' => 'filter_date',
                                     'id' => 'input_order_date',
                                     'type' => 'text',
                                     'class' => 'form-control date',
                                     'onkeydown'=>'return false',
                                     'value'=>date("Y-m-d")." 00:00",
                                     )); ?>
                                    <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                  </div> 
                              </td>
                          </tr>                          
                        <tr>
                            <td colspan="4" align="right">
                                <button id="filter_submit" class="btn btn-default "  ><i class="fa fa-filter" aria-hidden="true"></i> Filter</button>
                                 <button id="export_pdf" type="submit" class="btn btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Export PDF</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
          </div>
          <div class="col-lg-5 pull-right">
          </div>
          <div class="clearfix"></div>
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">
            <table class="table table-striped table-bordered table-hover dt-responsive" cellspacing="0" width="100%" id="table-aging-data">
              <thead>
                <tr>
                  <th>Inventory</th>
                  <th>Total Hutang</th>
                  <th>0 - 30</th>
                  <th>31 - 60</th>
                  <th>61 - 90</th>
                  <th>90+</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
            <input type="hidden" id="dataProcessUrl" value="<?php echo base_url(SITE_ADMIN."/reports/get_aging_data"); ?>"/>
            <input type="hidden" id="report_type" value="aging_data"/>
            <!-- /.table-responsive -->
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->