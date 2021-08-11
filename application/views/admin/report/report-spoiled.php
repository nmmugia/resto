<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="col-lg-12">
  <div class="panel panel-default">
    <div class="panel-heading">
      <form id="formFilter" method="POST" target="_blank">
        <input type="hidden" id="type" name="type" value="spoiled">
        <div class="clear-export"></div>
        <table class="col-lg-8 form-table-cms">
          <tbody>
            <tr>
              <td><label>Outlet</label></td>
              <td class="col-sm-8">
                <select class="form-control select2" data-width="100%" name="outlet_id" id="outlet_id">
                  <option value="">Semua Outlet</option>
                  <?php foreach($outlet_lists as $o): ?>
                  <option value="<?php echo $o->id ?>"><?php echo $o->outlet_name; ?></option>
                  <?php endforeach; ?>
                </select>
              </td>
            </tr>
            <tr>
              <td><label>Inventory</label></td>
              <td class="col-sm-8">
                <select class="form-control select2" data-width="100%" name="inventory_id" id="inventory_id">
                  <option value="">Semua Inventory</option>
                  <?php foreach($inventory_lists as $o): ?>
                  <option value="<?php echo $o->id ?>"><?php echo $o->name; ?></option>
                  <?php endforeach; ?>
                </select>
              </td>
            </tr>
			<tr>
              <td><label>Tanggal Awal</label></td>
              <td class="col-sm-8">
               <div class='input-group date' id="input_date">
                    <?php echo form_input(array(
                      'name' => 'start_date',
                      'type' => 'text',
                      'class' => 'form-control date', 
                      'onkeydown'=>'return false',
                      'value'=>date("Y-m-d")
                    )); ?>
                  <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </span>
                </div> 
              </td>
            </tr>
            <tr>
              <td><label>Tanggal Akhir</label></td>
              <td class="col-sm-8">
                <div class='input-group date' id="report_end_date">
                    <?php echo form_input(array(
                      'name' => 'end_date',
                      'type' => 'text',
                      'class' => 'form-control end_date', 
                      'onkeydown'=>'return false',
                      'value'=>date("Y-m-d")
                    )); ?>
                  <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </span>
                </div>
              </td>
            </tr>
            <tr>
              <td colspan="2" align="right">
              <button id="filter_submit" class="btn btn-default"><i class="fa fa-filter" aria-hidden="true"></i> Filter</button>
                <!--<button id="export_xls" type="submit" class="btn btn-default" style="float:right;display: none">Export Excel</button>-->
                <button id="export_pdf" type="submit" class="btn btn-success" style="display: none"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Export PDF</button>
                
              </td>
            </tr>
          </tbody>
        </table>
      </form>
      <div class="clearfix"></div>
    </div>
    <div id="report_content"></div>
    <input type="hidden" id="report_type" value="spoiled"/>
  </div>
</div>