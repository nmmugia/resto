<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/16/2014
 * Time: 10:01 AM
 */
?>
<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <table class="col-lg-12 form-table-cms">
                <tbody>
                <tr>
                    <td style="width: 10%"><label><?php echo $this->lang->line('column_start_date'); ?></label></td>
                    <td style="width: 40%"><input id="start_date" type="text" class="date-input form-control"/></td>
                    <td><label><?php echo $this->lang->line('column_store'); ?></label></td>
                    <td>
                        <select id="store_sel" class="def-select form-control">
                            <option value="0" selected="selected">All</option>
                            <?php
                            foreach ($all_store as $store) {
                                ?>
                                <option
                                    value="<?php echo $store->id ?>"><?php echo $store->store_name; ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label><?php echo $this->lang->line('column_end_date'); ?></label></td>
                    <td><input id="end_date" type="text" class="date-input form-control"/></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td style="width: 10%"><label><?php echo $this->lang->line('column_date_month'); ?></label></td>
                    <td style="width: 40%">
                        <select id="month_sel" class="def-select form-control">
                            <option value="0" selected="selected">All</option>
                            <?php
                            $start    = new DateTime('2009-01-01');
                            $interval = new DateInterval('P1M');
                            $end      = new DateTime('2010-01-01');
                            $period   = new DatePeriod($start, $interval, $end);

                            foreach ($period as $dt) {

                                ?>
                                <option
                                    value="<?php echo $dt->format('n') ?>"><?php echo $dt->format('F') ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td><label><?php echo $this->lang->line('column_date_year'); ?></label></td>
                    <td>
                        <select id="year_sel" class="def-select form-control">
                            <option value="0" selected="selected">All</option>
                            <?php
                            $start    = new DateTime('2014-01-01');
                            $interval = new DateInterval('P1Y');
                            $end      = new DateTime();
                            $period   = new DatePeriod($start, $interval, $end);

                            foreach ($period as $dt) {

                                ?>
                                <option
                                    value="<?php echo $dt->format('Y') ?>"><?php echo $dt->format('Y') ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="4">
                        <button id="filter_submit" class="btn btn-default" style="float:right;">Filter</button>
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="clearfix"></div>
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active">
                    <a href="#top10Container" aria-controls="top10Container" role="tab"
                       data-toggle="tab"><?php echo $this->lang->line('column_top10'); ?></a>
                </li>
                <li role="presentation">
                    <a href="#chartContainer" aria-controls="chartContainer"
                       role="tab" data-toggle="tab"><?php echo $this->lang->line('column_chart'); ?></a>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="top10Container"></div>
                <div role="tabpanel" class="tab-pane " id="chartContainer"></div>
            </div>
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->
</div>
<input type="hidden" id="analytics_type" value="table"/>
<input type="hidden" id="admin_url" value="<?php echo base_url(SITE_ADMIN) ?>"/>
<!-- /.col-lg-12 -->