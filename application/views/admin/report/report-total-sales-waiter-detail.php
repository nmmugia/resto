<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="col-lg-12">
  <div class="panel panel-default">
    <div class="panel-heading">
      <form id="formFilter" method="get" target="_blank">
        <input type="hidden" id="type" name="type" value="total_sales_waiter_detail">
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
        <input type="hidden" name="date" value="<?php echo $date; ?>">
        <div class="clear-export"></div>
        <table class="col-lg-8 form-table-cms">
          <tbody>
            <tr>
              <td><label>Waiter</label></td>
              <td class="col-sm-8"><?php echo $user->name ?></td>
            </tr>
            <tr>
              <td><label>Tanggal</label></td>
              <td class="col-sm-8">
                <?php echo date("d/m/Y",strtotime($date)) ?>
              </td>
            </tr>
            <tr>
              <td colspan="2" align="right">
                <button id="export_pdf" type="submit" class="btn btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Export PDF</button>
              </td>
            </tr>
          </tbody>
        </table>
      </form>
      <div class="clearfix"></div>
    </div>
    <div id="report_content">
      <?php echo $report_total_sales_detail; ?>
    </div>
    <input type="hidden" id="report_type" value="total_sales_waiter_detail"/>
  </div>
</div>