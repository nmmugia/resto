<?php if (! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<link href="<?php echo base_url(); ?>assets/css/bootstrap-datetimepicker.css" rel="stylesheet">
<div class="col-lg-12">
  <div class="panel panel-default">
    <div class="panel-heading">
      <form method="POST" action="<?php echo base_url("admincms/analisys_top_products/sales_by_day_report") ?>" id="form_report">
        <input type="hidden" name="search[store_id]" value="<?php echo $search['store_id'] ?>">
        <input type="hidden" name="image_data" id="image_data">
        <div class="clear-export"></div>
        <table class="col-lg-8 form-table-cms">
          <tbody>
            <?php /*
            <tr>
              <td><label>Store</label></td>
              <td class="col-sm-8">
                <select name="search[store_id]" class="form-control">
                  <?php foreach($store_lists as $s): ?>
                    <option value="<?php echo $s->id ?>" <?php echo ($search['store_id']==$s->id ? "selected" : "") ?>><?php echo $s->store_name ?></option>
                  <?php endforeach; ?>
                </select>
              </td>
            </tr>*/ ?>
            <tr>
              <td><label>Periode</label></td>
              <td class="col-sm-8">
                <div class='input-group date' id="month_date">
                  <?php 
                    echo form_input(array(
                      'name' => 'search[date]',
                      'type' => 'text',
                      'class' => 'form-control date',
                      'onkeydown'=>'return false',
                      'value' => $search['date']
                    )); 
                  ?>
                  <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </span>
                </div> 
              </td>
              </tr>
              <tr>
              <td colspan="2" align="right">
                <button class="btn btn-default" name="button" value="filter"><i class="fa fa-filter" aria-hidden="true"></i> Filter</button>
                <button class="btn btn-info" type="button" name="button" value="export_pdf" id="capture_chart" target_capture="#sales_by_day_chart"><i class="fa fa-photo" aria-hidden="true"></i> Capture</button>
                <button class="btn btn-success" name="button" value="export_pdf" id="report_export_pdf" style="display:none;"><i class="fa fa-filter" aria-hidden="true"></i> Export PDF</button>
              </td>
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