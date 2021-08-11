<?php if (! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<link href="<?php echo base_url(); ?>assets/css/bootstrap-datetimepicker.css" rel="stylesheet">
<div class="col-lg-12">
  <div class="panel panel-default">
    <div class="panel-heading">
      <form method="POST" action="<?php echo base_url("admincms/analisys_top_products/inventory_used_report") ?>" id="form_inventory_used">
        <input type="hidden" name="search[store_id]" value="<?php echo $search['store_id'] ?>">
        <input type="hidden" name="search[inventory_id]" value="<?php echo $search['inventory_id'] ?>" id="inventory_id">
        <input type="hidden" name="search[from]" value="<?php echo $search['from'] ?>">
        <input type="hidden" name="search[to]" value="<?php echo $search['to'] ?>">
        <div class="clear-export"></div>
        <table class="col-lg-8 form-table-cms">
          <tbody>
            <tr>
              <td><label>Dari</label></td>
              <td><?php echo date("d/m/Y",strtotime($search['from'])) ?></td>
            </tr>
            <tr>
              <td><label>Sampai</label></td>
              <td><?php echo date("d/m/Y",strtotime($search['to'])) ?></td>
              </tr>
              <tr>
              <td colspan="2" align="right">
                <button class="btn btn-success" name="button" value="export_pdf"><i class="fa fa-filter" aria-hidden="true"></i> Export PDF</button>

            </tr>
          </tbody>
        </table>
      </form>
      <div class="clearfix"></div>
    </div>
    <div class="panel-body">
      <?php echo $detail_view ?>
    </div>
  </div>
</div>