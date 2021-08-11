<?php if (! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<link href="<?php echo base_url(); ?>assets/css/bootstrap-datetimepicker.css" rel="stylesheet">
<div class="col-lg-12">
  <div class="panel panel-default">
    <div class="panel-heading">
      <form method="POST" action="<?php echo base_url("admincms/analisys_top_products/inventory_used_report") ?>" id="form_inventory_used">
        <input type="hidden" name="search[store_id]" value="<?php echo $search['store_id'] ?>">
        <input type="hidden" name="search[inventory_id]" value="<?php echo $search['inventory_id'] ?>" id="inventory_id">
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
              <td><label>Dari</label></td>
              <td class="col-sm-8">
                <div class='input-group date' id='input_date'>
                  <?php echo form_input(array(
                    'name' => 'search[from]',
                    'type' => 'text',
                    'class' => 'form-control date',
                    'onkeydown'=>'return false',
                    'value' => $search['from']
                  )); ?>
                  <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div> 
              </td>
  
            </tr>
            <tr>
              <td><label>Sampai</label></td>
              <td class="col-sm-8">
                <div class='input-group date' id='report_end_date'>
                  <?php echo form_input(array(
                    'name' => 'search[to]',
                    'type' => 'text',
                    'class' => 'form-control date',
                    'onkeydown'=>'return false',
                    'value' => $search['to']
                  )); ?>
                  <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div> 
              </td>
              </tr>
              <tr>
              <td  colspan="2" align="right">
                <button class="btn btn-default" name="button" value="filter" id="filter_inventory_used"><i class="fa fa-filter" aria-hidden="true"></i> Filter</button>
                <button class="btn btn-success" name="button" value="export_pdf" id="export_inventory_used"><i class="fa fa-filter" aria-hidden="true"></i> Export PDF</button>
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