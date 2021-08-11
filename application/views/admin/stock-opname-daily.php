<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="col-lg-12">
	<div class="result">
		<?php
			if (! empty($message_success)) {
					echo '<div class="alert alert-success" role="alert">';
					echo $message_success;
					echo '</div>';
			}
		 
			if (! empty($message)) {
					echo '<div class="alert alert-danger" role="alert">';
					echo $message;
					echo '</div>';
			}
		?>
	</div>
  <div class="panel panel-default">
    <div class="panel-heading">
      <?php echo form_open(base_url(SITE_ADMIN."/stock_opname"), array('class' => 'form-horizontal form-ajax','id'=>'formFilter'));?>
        <div class="clear-export"></div>
        <table class="col-lg-5 form-table-cms">
          <tbody>
            <tr>
              <td class="col-sm-2"><label>Outlet</label></td>
              <td class="col-sm-2">
                <select class="form-control select2" data-width="100%" name="outlet_id" id="outlet_id">
                  <option value="">Semua Outlet</option>
                  <?php foreach($outlet_lists as $o): ?>
                  <option value="<?php echo $o->id ?>"><?php echo $o->outlet_name; ?></option>
                  <?php endforeach; ?>
                </select>
              </td>
            </tr>
            <tr>
              <td class="col-sm-2"><label>Inventory</label></td>
              <td class="col-sm-2">
                <select class="form-control select2"  data-width="100%" name="inventory_id" id="inventory_id">
                  <option value="">Semua Inventory</option>
                  <?php foreach($inventory_lists as $o): ?>
                  <option value="<?php echo $o->id ?>"><?php echo $o->name; ?></option>
                  <?php endforeach; ?>
                </select>
              </td>
            </tr>
            <tr>
              <td colspan="2" align="right">
                <button id="filter_stock_opname_daily" style="float:right;" class="btn btn-default">Filter</button>
              </td>
            </tr>
          </tbody>
        </table>
      </form>
      <div class="clearfix"></div>
    </div>
    <div id="report_content"></div>
  </div>
</div>