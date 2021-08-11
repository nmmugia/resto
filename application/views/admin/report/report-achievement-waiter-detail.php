<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="col-lg-12">
  <div class="panel panel-default">
    <div class="panel-heading">
      <form id="formFilter" method="get" target="_blank">
        <input type="hidden" id="type" name="type" value="achievement_waiter_detail">
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
        <input type="hidden" name="target_id" value="<?php echo $target_id; ?>">
        <input type="hidden" name="month" value="<?php echo $month; ?>">
        <input type="hidden" name="year" value="<?php echo $year; ?>">
        <div class="clear-export"></div>
        <table class="col-lg-8 form-table-cms">
          <tbody>
            <tr>
              <td><label>Waiter</label></td>
              <td class="col-sm-8"><?php echo $user->name ?></td>
            </tr>
            <tr>
              <td><label>Periode</label></td>
              <td class="col-sm-8">
                <?php echo date("F Y",strtotime($year."-".$month."-01")) ?>
              </td>
            </tr>
            <tr>
              <td><label>Tipe Target</label></td>
              <td class="col-sm-8"><?php echo ($target->target_type==1 ? "Target By Total Penjualan" : "Target By Penjualan Item") ?></td>
            </tr>
            <?php if($target->target_type==1): ?>
            <tr>
              <td><label>Jumlah Target</label></td>
              <td class="col-sm-8"><?php echo number_format($target->target_by_total,2,",",".") ?></td>
            </tr>
            <?php endif; ?>
            <tr>
              <td colspan="8" align="right">
                <button id="export_pdf" type="submit" class="btn btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Export PDF</button>
              </td>
            </tr>
          </tbody>
        </table>
      </form>
      <div class="clearfix"></div>
    </div>
    <div id="report_content">
      <?php echo $report_achievement_waiter_detail; ?>
    </div>
    <input type="hidden" id="report_type" value="achievement_waiter_detail"/>
  </div>
</div>