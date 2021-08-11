<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="col-lg-12">
  <div class="panel panel-default">
    <div class="panel-heading">
      <form id="formFilter" method="POST" target="_blank">
        <input type="hidden" id="type" name="type" value="data_inventory">
        <div class="clear-export"></div>
        <table class="col-lg-10 form-table-cms">
          <tbody>
            
            <tr>
              <td class="col-sm-1"><label>Inventory</label></td>
              <td class="col-sm-2">
                <select class="form-control select2" name="inventory_id" id="inventory_id">
                  <option value="">Semua Inventory</option>
                  <?php foreach($inventory_lists as $o): ?>
                  <option value="<?php echo $o->id ?>"><?php echo $o->name; ?></option>
                  <?php endforeach; ?>
                </select>
              </td>
            </tr>
            <tr>
              <td colspan="4">
                <button id="filter_submit" style="float:right;" class="btn btn-default">Filter</button>
              </td>
            </tr>
          </tbody>
        </table>
      </form>
      <div class="clearfix"></div>
    </div>
    <div class="panel-body">
        <table class="table table-striped table-bordered table-hover dt-responsive" cellspacing="0" width="100%" id="table-inventory-data">
            <thead>
                <tr>
                    <th width="8%">Inventory</th>
                    <th width="8%">Stok Saat Ini</th>
                    <th width="8%">Total Harga</th>
                    <th width="8%">Harga Rata-rata</th>
                    <th width="8%">Harga Saat Ini</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <input type="hidden" id="dataProcessUrl" value="<?php echo base_url(SITE_ADMIN."/reports/get_inventory_data"); ?>"/>
        <input type="hidden" id="report_type" value="data_inventory"/>
        <!-- /.table-responsive -->
    </div>    
  </div>
</div>