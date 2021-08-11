<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

?>
<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
<form id="formFilter" method="get" target="_blank">
  <input type="hidden" id="report_type" value="price_analyst" name="type"/>
    <div class="clear-export"></div>
            <table class="col-lg-8 form-table-cms">
                <tbody>

                  <tr>
                        <td><label>Outlet</label></td>
                        <td class="col-sm-8">
                           <?php 
                           echo form_dropdown('outlet_id', $all_outlet, '', 
                            'id="outlet_id" field-name = "Outlet" 
                            class="form-control select2" autocomplete="on"');
                            ?>
                            
                        </td>
                        
                    </tr>
          <tr>
                        <td><label>Kategori</label></td>
                        <td class="col-sm-8">
                           <?php 
                           echo form_dropdown('category_menu_id', $all_category, '', 
                            'id="category_menu_id" field-name = "Outlet" 
                            class="form-control select2" autocomplete="on" required');
                            ?>
                            
                        </td>
                        
                    </tr>
               
                <tr>
                    <td colspan="4" align="right">
                        <button id="filter_submit" class="btn btn-default"><i class="fa fa-filter" aria-hidden="true"></i> Filter</button>
                        <button id="export_pdf" class="btn btn-success hide_btn" style="display: none"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Export PDF</button>
                        
                    </td>
                </tr>
                </tbody>
            </table>
</form>

            <div class="clearfix"></div>
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">


        
            <table class="table table-striped table-bordered table-hover dt-responsive width100" cellspacing="0" width="100%" id="table-price-analyst">
                <thead>
                <tr>
                  <th>Kategori</th>
                  <th>Menu</th>
                  <th>Harga Jual</th>
                  <th>Harga Pokok</th>
                  <th>Untung Kotor</th>
                  <th>Margin (%)</th>
                  <th>Markup (%)</th>
                </tr>
                </thead>
            </table>

            <input type="hidden" id="dataProcessUrl"
                   value="<?php echo $data_url; ?>"/>

            <!-- /.table-responsive -->
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->
</div>
