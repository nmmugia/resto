<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="col-lg-12">
  <div class="panel panel-default">
    <div class="panel-heading">
      <form id="formFilter" method="POST" target="_blank">
        <input type="hidden" id="type" name="type" value="attendance_overdue">
        <div class="clear-export"></div>
        <table class="col-lg-8 form-table-cms">
          <tbody>
						<tr>
							<td><label>Dari Tanggal</label></td>
							<td class="col-sm-8">
								<div class='input-group date ' id='report_attendance_start_date'>
									<?php echo form_input(array('name' => 'start_date',
										 'id' => 'input_start_date',
										 'type' => 'text',
										 'class' => 'form-control date',
										 'onkeydown'=>'return false',
										 'value'=>date("Y-m-d")
									)); ?>
									<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
								</div> 
							</td>
						</tr>
						<tr>
							<td><label>Sampai Tanggal</label></td>
							<td class="col-sm-8">
								<div class='input-group date ' id='report_attendance_end_date'>
									<?php echo form_input(array('name' => 'end_date',
										 'id' => 'input_end_date',
										 'type' => 'text',
										 'class' => 'form-control date',
										 'onkeydown'=>'return false',
										 'value'=>date("Y-m-d")
									)); ?>
									<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
								</div> 
							</td>
						</tr>
						<tr>
							<td colspan="4" align="right">
								<button id="filter_submit_attendance_overdue" class="btn btn-default"><i class="fa fa-filter" aria-hidden="true"></i> Filter</button>
								<button id="export_overdue_pdf" type="submit" class="btn btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Export PDF</button>
								
							</td>
						</tr>
           
          </tbody>
        </table>
      </form>
      <div class="clearfix"></div>
    </div>
    <div id="report_content">
    	<style>
  table th, table td {
    word-wrap: break-word;
    max-width: 50px;
  }
  .table th
  {
    text-align:center;
  }
  table {
    width: 100%;   
  }
  .bold{
    font-weight:bold;
  }
  th {
    height: 50px;
  }
  table {
    border-collapse: collapse;
  }
  .border{
    margin-bottom:15px;
  }
  .border td, .border th{
    border: solid 1px #000;
    padding-left: 5px;
    padding-right: 5px;
  }
  .text-right{
    text-align:right;
  }
  .text-center{
    text-align:center;
  }
  h4,h5{
    margin-top:3px;
    margin-bottom:3px;
  }
  .is_print{
    font-size:11px;
  }
	.table>thead>tr>th, .table>tbody>tr>th, .table>tfoot>tr>th, .table>thead>tr>td, .table>tbody>tr>td, .table>tfoot>tr>td{
		padding:5px;
		font-size:11px;
		height:auto;
	}
	.table.is_print td,.table.is_print th{
		font-size:8px !important;
	}
	@media print{
		.table.is_print td,.table.is_print th{
			font-size:8px !important;
		}
	}
	.detail_row{
		min-width:10px !important;
		max-width:10px !important;
		width:10px !important;
		text-align:center;
		font-weight:bold;
	}
</style>
<div class="panel-body <?php echo ($is_print==true ? "is_print" : ""); ?>">
  <div class="text-center">
   <h3><?php echo (!empty($data_store))?$data_store->store_name:"";?></h3>
    <h4><label>Laporan Terlambat</label></h4>
		<h5><?php echo date("d/m/Y",strtotime($start_date))." s/d ".date("d/m/Y",strtotime($end_date)) ?></h5>
  </div>
  <table class="table table-bordered <?php echo ($is_print==true ? "border" : ""); ?>" >
    <thead>
      <tr style="background-color:#337ab7;color:white;">
        <th rowspan="2" width="150px;" style="vertical-align:middle">Nama Pegawai</th>
        <?php 
					$start=$start_date;
					while(strtotime($start)<=strtotime($end_date)){
						echo "<th>".date("d",strtotime($start))."</th>";
						$start=date("Y-m-d",strtotime($start." +1day"));
					}
				?>
				<th rowspan="2"   style="vertical-align:middle">Total</th>
      </tr>
			<tr style="background-color:#337ab7;color:white;">
				<?php 
					$days=array(
						"Mon"=>"S",
						"Tue"=>"S",
						"Wed"=>"R",
						"Thu"=>"K",
						"Fri"=>"J",
						"Sat"=>"S",
						"Sun"=>"M",
					);
					$start=$start_date;
					while(strtotime($start)<=strtotime($end_date)){
						echo "<th>".$days[date("D",strtotime($start))]."</th>";
						$start=date("Y-m-d",strtotime($start." +1day"));
					}
					 
				?>
			</tr>
			 
    </thead>
    <tbody>
    <?php
    $i = 0;
			foreach($results as $r){

				echo "<tr>";
				echo "<td>".$r['name']."</td>";
				$start=$start_date;
				$total[$i] = 0;
				while(strtotime($start)<=strtotime($end_date)){	
					
					$text="0";
					if(isset($r['detail'][$start]) && sizeof($r['detail'][$start])>0){
						$data=$r['detail'][$start];
						if($data->overdue > 0){
							$text = $data->overdue;	
							$total[$i]+=$data->overdue;
						}
						
					}
					echo "<td class='detail_row' style='".($text !="0" ? "background-color:red;" : "")."'>".$text."</td>";
					$start=date("Y-m-d",strtotime($start." +1day"));
				}

				echo "<td class='detail_row'>".$total[$i]."</td>";
				echo "</tr>";
				$i++;
			}
    ?>
    </tbody>
  </table>
</div>
    </div>
    <input type="hidden" id="report_type" value="attendance_overdue"/>
  </div>
</div>