<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="col-lg-12">
  <div class="panel panel-default">
    <div class="panel-heading">
      <form id="formFilter" method="get" target="_blank">
        <input type="hidden" id="type" name="type" value="inventory_stock_detail">
        <input type="hidden" name="store_id" value="<?php echo $data_store->id; ?>">
        <input type="hidden" name="inventory_id" value="<?php echo $data_inventory->id; ?>">
        <input type="hidden" name="uom_id" value="<?php echo $data_uom->id; ?>">
        <input type="hidden" name="from_date" value="<?php echo $from_date; ?>">
        <input type="hidden" name="to_date" value="<?php echo $to_date; ?>">
        <div class="clear-export"></div>
        <table class="col-lg-5 form-table-cms">
          <tbody>
            <tr>
              <td><label>Resto</label></td>
              <td class="col-sm-8"><?php echo $data_store->store_name; ?></td>
            </tr>
            <tr>
              <td><label>Inventory</label></td>
              <td class="col-sm-8"><?php echo $data_inventory->name." (".$data_uom->code.")"; ?></td>
            </tr>
            <tr>
              <td><label>Tanggal</label></td>
              <td class="col-sm-8">
                <?php echo date("d/m/Y",strtotime($from_date))." s/d ".date("d/m/Y",strtotime($to_date)) ?>
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <button id="export_pdf" type="submit" class="btn btn-default">Export PDF</button>
              </td>
            </tr>
          </tbody>
        </table>
      </form>
      <div class="clearfix"></div>
    </div>
    <div id="report_content">
      <?php echo $report_inventory_detail; ?>
    </div>
    <input type="hidden" id="report_type" value="inventory_stock_detail"/>
  </div>
</div>