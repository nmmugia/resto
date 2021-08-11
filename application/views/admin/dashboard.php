<?php if (! defined('BASEPATH')) exit('No direct script access allowed');?>
<?php if ($this->groups_access->have_access('admincms')): ?>
  <?php if($module['TOP']==1): ?>
  <div class="col-lg-12">
    <div class="panel panel-default">
      <form method="POST" action="" id="form_dashboard">
        <input type="hidden" name="search[store_id]" value="<?php echo $search['store_id'] ?>">
        <input type="hidden" name="search[date]" value="<?php echo $search['date'] ?>">
        <input type="hidden" name="search[from]" value="<?php echo $search['from'] ?>">
        <input type="hidden" name="search[to]" value="<?php echo $search['to'] ?>">
        <input type="hidden" name="search[type]" value="<?php echo $search['type'] ?>">
        <div class="panel-heading">
          <div class="clear-export"></div>
          <table class="col-lg-5 form-table-cms">
            <tbody>
              <tr>
                <td><label>Dari</label></td>
                <td><?php echo date("01 F Y") ?></td>
                <td><label>Sampai</label></td>
                <td><?php echo date("d F Y") ?></td>
              </tr>
            </tbody>
          </table>
          <div class="pull-right col-lg-6">
            <?php if (isset($store->store_last_sync)): ?>
            <div class="pull-left" style="padding-top: 6px;">Terakhir Sinkronisasi</div>
            <div class="pull-left" style="padding-top: 6px;"> : <?php echo $store->store_last_sync?></div>
            <?php endif; ?>
            <div id="sync-me-now" class="btn btn-default btn-primary center-block pull-right">Sinkronisasi Database Dari Server</div>
          </div>
          <div class="clearfix"></div>
        </div>
        <div class="panel-body">
          <div class="col-sm-4">
            <h5><b>Summary Sales</b></h5>
            <table class="table table-bordered border">
              <tbody>
                <tr>
                  <td><b>Total Sales Today</b></td>
                  <td class="text-right"><?php echo number_format($lists['summary']->total_sales_today,2) ?></td>
                </tr>
                <tr>
                  <td><b>Total Sales This Month</b></td>
                  <td class="text-right"><?php echo number_format($lists['summary']->total_sales_current_month,2) ?></td>
                </tr>
                <tr>
                  <td><b>Total Sales This Year</b></td>
                  <td class="text-right"><?php echo number_format($lists['summary']->total_sales_current_year,2) ?></td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="col-sm-4">
            <h5><b><a href="javascript:void(0);" id="goto_sales_by_waiter" url="<?php echo base_url(SITE_ADMIN."/analisys_top_products/sales_by_waiter_report") ?>">Sales By Waiter</a></b></h5>
            <table class="table table-bordered border">
              <thead>
                <tr>
                  <th>Waiter</th>
                  <th>Revenue</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($lists['sales_by_waiter'] as $l): ?>
                  <tr>
                    <td><?php echo $l->name ?></td>
                    <td class="text-right"><?php echo number_format($l->revenue,2) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div class="col-sm-4">
            <h5><b><a href="javascript:void(0);" id="goto_sales_by_department" url="<?php echo base_url(SITE_ADMIN."/analisys_top_products/sales_by_department_category_report") ?>">Sales By Department</a></b></h5>
            <table class="table table-bordered border">
              <thead> 
                <tr>
                  <th>Department</th>
                  <th>Revenue</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                  $total_revenue=0;
                  foreach($lists['sales_by_department'] as $l): 
                  $total_revenue+=$l->revenue;
                ?>
                  <tr>
                    <td><?php echo $l->name ?></td>
                    <td class="text-right"><?php echo number_format($l->revenue,2) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div class="clearfix"></div>
          <div class="col-sm-6">
            <div id="sales_by_department_chart_pie"></div>
          </div>
          <div class="col-sm-6">
            <div id="sales_by_waiter_chart"></div>
          </div>
          <div class="clearfix"></div>
          <div class="col-sm-12">
            <div id="sales_by_day_chart"></div>
          </div><div class="clearfix"></div>
          <?php if($setting['report_total_customer'] == 1): ?>
          <div class="col-sm-12">
            <div id="customer_by_day_chart"></div>
          </div>
          <?php endif; ?>
        </div>
      </form>
    </div>
  </div>
  <script type="text/javascript">
    var sales_by_department_series_data_pie=
    [
      <?php 
        $counter=1;
        foreach($lists['sales_by_department'] as $l){
          echo "{";
          echo "name:"."'".$l->name."',";
          echo "y:".($l->revenue/$total_revenue*100);
          echo "}";
          if($counter!=sizeof($lists['sales_by_department']))echo ",";
          $counter++;
        }
      ?>
    ];
    var sales_by_day_categories_data=[
      <?php 
        $counter=1;
        foreach($lists['sales_by_day'] as $l){
          $label=date("d M Y",strtotime($l->date));
          echo "'".$label."'";
          if($counter!=sizeof($lists['sales_by_day']))echo ",";
          $counter++;
        }
      ?>
    ];
    var sales_by_day_series_data=[
      <?php 
        $counter=1;
        foreach($lists['sales_by_day'] as $l){
          echo $l->revenue;
          if($counter!=sizeof($lists['sales_by_day']))echo ",";
          $counter++;
        }
      ?>
    ];
    var customer_by_day_categories_data=[
      <?php 
        $counter=1;
        foreach($lists['customer_by_day'] as $l){
          $label=date("d M Y",strtotime($l->date));
          echo "'".$label."'";
          if($counter!=sizeof($lists['customer_by_day']))echo ",";
          $counter++;
        }
      ?>
    ];
    var customer_by_day_series_data=[
      <?php 
        $counter=1;
        foreach($lists['customer_by_day'] as $l){
          echo $l->total_customer;
          if($counter!=sizeof($lists['customer_by_day']))echo ",";
          $counter++;
        }
      ?>
    ];
    var sales_by_waiter_categories_data=[
      <?php 
        $counter=1;
        foreach($lists['sales_by_waiter'] as $l){
          echo "'".$l->name."'";
          if($counter!=sizeof($lists['sales_by_waiter']))echo ",";
          $counter++;
        }
      ?>
    ];
    var sales_by_waiter_series_data=[
      <?php 
        $counter=1;
        foreach($lists['sales_by_waiter'] as $l){
          echo $l->revenue;
          if($counter!=sizeof($lists['sales_by_waiter']))echo ",";
          $counter++;
        }
      ?>
    ];
  </script> 
  <?php else: ?>
  <div class="row">
    <img style="width:400px;margin:-40px auto auto auto; display:block;" src="<?php echo base_url() ?>assets/img/image-dashboard-bos.png" alt="logo"/>
  </div>
  <div class="col-md-4 col-md-offset-4">
    <div id="sync-me-now" class="btn btn-default btn-primary center-block">Sync Database dari Server</div>
    <?php if (isset($store->store_last_sync)): ?>
      <h4 style="text-align:center">Waktu terakhir sinkronisasi</h4>
      <h4 style="text-align:center"><?php echo $store->store_last_sync?></h4>
    <?php endif; ?>
  </div>
<?php endif; ?>
<?php endif; ?>
<?php if ($this->groups_access->have_access('hrd')) { ?>
<?php if($module['HRD']==1): ?>
	<div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-body">
					<div class="col-lg-3 col-md-3">
		                <div class="panel panel-green">
		                    <div class="panel-heading">
		                        <div class="row">
		                            <div class="col-xs-3">
		                                <i class="fa   fa-5x"></i>
		                            </div>
		                            <div class="col-xs-9 text-right">
		                                <div class="huge"></div>
		                                <div>Daftar Absensi</div>
		                            </div>
		                        </div>
		                    </div>
		                    <a href="<?php echo base_url(SITE_ADMIN . '/hrd_attendance'); ?>">
		                        <div class="panel-footer">
		                            <span class="pull-left">Lihat Detail</span>
		                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>

		                            <div class="clearfix"></div>
		                        </div>
		                    </a>
		                </div>
		            </div>
		            <div class="col-lg-3 col-md-3">
		                <div class="panel panel-red">
		                    <div class="panel-heading">
		                        <div class="row">
		                            <div class="col-xs-3">
		                                <i class="fa   fa-5x"></i>
		                            </div>
		                            <div class="col-xs-9 text-right">
		                                <div class="huge"></div>
		                                <div>Input Appraisal</div>
		                            </div>
		                        </div>
		                    </div>
		                    <a href="<?php echo base_url(SITE_ADMIN . '/hrd_appraisal/add_process_appraisal'); ?>">
		                        <div class="panel-footer">
		                            <span class="pull-left">Lihat Detail</span>
		                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>

		                            <div class="clearfix"></div>
		                        </div>
		                    </a>
		                </div>
		            </div>
		            <div class="col-lg-3 col-md-3">
		                <div class="panel panel-yellow">
		                    <div class="panel-heading">
		                        <div class="row">
		                            <div class="col-xs-3">
		                                <i class="fa   fa-5x"></i>
		                            </div>
		                            <div class="col-xs-9 text-right">
		                                <div class="huge"></div>
		                                <div>Slip Gaji</div>
		                            </div>
		                        </div>
		                    </div>
		                    <a href="<?php echo base_url(SITE_ADMIN . '/hrd_payroll/slip'); ?>">
		                        <div class="panel-footer">
		                            <span class="pull-left">Lihat Detail</span>
		                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>

		                            <div class="clearfix"></div>
		                        </div>
		                    </a>
		                </div>
		            </div>
		            <div class="col-lg-3 col-md-3">
		                <div class="panel panel-green">
		                    <div class="panel-heading">
		                        <div class="row">
		                            <div class="col-xs-3">
		                                <i class="fa   fa-5x"></i>
		                            </div>
		                            <div class="col-xs-9 text-right">
		                                <div class="huge"></div>
		                                <div>Tambah Pegawai</div>
		                            </div>
		                        </div>
		                    </div>
		                    <a href="<?php echo base_url(SITE_ADMIN . '/hrd_staff/add_staff'); ?>">
		                        <div class="panel-footer">
		                            <span class="pull-left">Lihat Detail</span>
		                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>

		                            <div class="clearfix"></div>
		                        </div>
		                    </a>
		                </div>
		            </div>
    			</div>

    		</div>
   	 	</div>
   	 	  <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-body">
					 <div id="attendance-statistic">

 						</div>
    			</div>

    		</div>
   	 	</div>
   	 </div>
<?php endif; ?>
<?php }?>

<input type="hidden" id="admin_url" value="<?php echo base_url(SITE_ADMIN) ?>"/>