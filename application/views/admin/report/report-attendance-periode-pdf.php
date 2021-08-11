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
    <h4><label>Laporan Absensi Periode</label></h4>
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
			foreach($results as $r){
				echo "<tr>";
				echo "<td>".$r['data']->name."</td>";
				$start=$start_date;
				while(strtotime($start)<=strtotime($end_date)){
					
					$text="";
					if(isset($r['detail'][$start]) && sizeof($r['detail'][$start])>0){
						$data=$r['detail'][$start];
						$offs=explode(",",$data->off);
						if(in_array(date("D",strtotime($start)),$offs)){
							$text="OFF";
						}elseif($data->permission_go_home==1){
							$text="P";
						}elseif($data->permission_alpha==1){
							$text="I";
						}elseif($data->sick==1){
							$text="S";
						}elseif($data->late_2==1){
							$text="<span style='color:red;'>T2</span>";
						}elseif($data->late_1==1){
							$text="<span style='color:orange;'>T1</span>";
						}elseif($data->present==1){
							$text="<span style='color:green;'>H</span>";
						}
					}
					echo "<td class='detail_row' style='".($text=="" ? "background-color:red;" : "")."'>".$text."</td>";
					$start=date("Y-m-d",strtotime($start." +1day"));
				}
				echo "</tr>";
			}
    ?>
    </tbody>
  </table>
</div>