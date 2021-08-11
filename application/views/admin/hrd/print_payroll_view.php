<?php if (! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<style type="text/css">
  .border-none,.border-none td{
    border-top:0px !important;
    padding-top: 1px !important;
    padding-bottom: 1px !important;
  }
  .border td,.border tr{
    border:1px solid black !important;
    padding: 1px 0px 1px 0px !important;
  }
  .border-head td:not(.border-none){
    border:1px solid black !important;
    padding: 5px !important;
    padding-top: 1px !important;
    padding-bottom: 1px !important;
  }
  img{
    width:115px;
    height:60px;
  }
  @media print{
    .table-print{
      font-size:9px !important;
    }    
    .page-header{
      display:none;
    }
    .panel-body,.panel{
      padding:0px important;
      border:0px !important;
    }
    img{
      width:100px;
      height:50px;
    }
    .page-break{
      page-break-before: always;
    }
    .not-print{
      display:none;
    }
		.background_color_green,.background_color_green td,.background_color_green th{
			background-color:green !important;
		}
		.background_color_lightblue,.background_color_lightblue td,.background_color_lightblue th{
			background-color:lightblue !important;
		}
		.background_color_yellow,.background_color_yellow td,.background_color_yellow th{
			background-color:yellow !important;
		}
  }
</style>
<a href="javascript:window.print()" class="not-print btn btn-primary pull-right" style="margin-right:20px;">Cetak</a>
<div class="col-lg-12 table-print">
  <div class="panel panel-default">
    <div class="panel-body">
      <?php 
        $counter=0;
        foreach($detail as $d): 
      ?>
      <div class="col-sm-6 background_color_<?php echo ($d['data']->background_color=='' ? 'white' : $d['data']->background_color) ?>" style="border:1px solid black;padding:10px;width:50%;float:left;background-color:<?php echo ($d['data']->background_color=='' ? 'white' : $d['data']->background_color) ?>;">
        <table class="table border-head"  style="margin-bottom:5px;">
          <tbody>
            <tr>
              <td rowspan="3" width="100px" class="border-none">
              <?php 
              if(@getimagesize(base_url($data_store->store_logo))){ ?>
                 <img src="<?php echo base_url($data_store->store_logo) ?>">
              <?php } else{
                echo "LOGO";
              }
              ?>
               
              </td>
              <td><b>Slip Gaji Periode</b></td>
              <td align="center"><b><?php echo date("F Y",strtotime($periode)); ?></b></td>
            </tr>
            <tr>
              <td><b>Nama</b></td>
              <td align="center"><b><?php echo $d['data']->name; ?></b></td>
            </tr>
            <tr>
              <td><b>Jabatan</b></td>
              <td align="center"><b><?php echo $d['data']->jobs_name." - ".$d['data']->employee_affair_name; ?></b></td>
            </tr>
          </tbody>
        <table>
        <table class="table border-none" style="margin-bottom:2px;">
          <tbody>
            <tr>
              <td colspan="3"><b>Penambahan</b></td>
            </tr>
            <?php 
              $total_plus=0;
              foreach($d['detail'] as $l): 
              if($l->is_enhancer==1):
              $total_plus+=$l->value;
            ?>
            <tr>
              <td style="padding-left:30px;"><b><?php echo $l->component_name; ?></b></td>
              <td></td>
              <td align="right"><?php echo number_format((!empty($l->value))?$l->value:0,0) ?></td>
            </tr>
            <?php 
              endif;
              endforeach; 
            ?>
            <tr style="border-top: 1px solid black;">
              <td colspan="2" style="padding-left:30px;"><b>Total Penambah</b></td>
              <td align="right"><?php echo number_format($total_plus,0) ?></td>
            </tr>
            <tr>
              <td colspan="3"><b>Pengurangan</b></td>
            </tr>
            <?php 
              $total_minus=0;
              $total_attendances=0;
              foreach($d['detail'] as $l): 
              if($l->is_enhancer==-1 && !in_array($l->key,array("late_1","late_2","permission_go_home","permission_alpha","alpha"))):
              $total_minus+=$l->value;
            ?>
            <tr>
              <td style="padding-left:30px;"><b><?php echo $l->component_name; ?></b></td>
              <td></td>
              <td align="right"><?php echo number_format($l->value,0) ?></td>
            </tr>
            <?php 
              else:
                if(in_array($l->key,array("late_1","late_2","permission_go_home","permission_alpha","alpha")))$total_attendances+=$l->value;
              endif;
              endforeach; 
              if($total_attendances>$d['payroll_static_data']['insentive']){
                $total_attendances=$d['payroll_static_data']['insentive'];
              }
              $total_minus+=$total_attendances;
            ?>
            <tr>
              <td style="padding-left:30px;"><b>Pengurang Insentif</b></td>
              <td></td>
              <td align="right"><?php echo number_format($total_attendances,0) ?></td>
            </tr>
            <?php 
              
              foreach($d['detail'] as $l): 
              if($l->is_enhancer==-1 && in_array($l->key,array("late_1","late_2","permission_go_home","permission_alpha","alpha"))):
              $total_attendances+=$l->value;
            ?>
            <tr>
              <td style="padding-left:40px;"><?php echo $l->component_name; ?></td>
              <td><?php echo $l->formula_default." % : ".$d['payroll_static_data'][$l->key] ?></td>
              <td align="right"><?php echo number_format($l->value,0) ?></td>
            </tr>
            <?php 
              endif;
              endforeach; 
            ?>
            <tr style="border-top: 1px solid black;">
              <td colspan="2" style="padding-left:30px;"><b>Total Pengurang</b></td>
              <td align="right"><?php echo number_format($total_minus,0) ?></td>
            </tr>
          </tbody>
          <tfoot class="border-head">
            <tr>
              <td colspan="2" align="center"><b>Take Home Pay (THP)</b></td>
              <td align="right"><b><?php echo number_format($total_plus-$total_minus,0) ?></b></td>
            </tr>
          </tfoot>
        </table>
        <table class="table table-bordered border" style="margin-bottom: 0px;">
          <tbody>
            <tr>
              <td align="center">Mengetahui</td>
              <td align="center">Menerima</td>
            </tr>
            <tr>
              <td style="height:50px;"></td>
              <td></td>
            </tr>
            <tr>
              <td align="center" rowspan="2" style="vertical-align:middle;">Finance</td>
              <td align="center"><?php echo $d['data']->name ?></td>
            </tr>
            <tr>
              <td align="center"><?php echo $d['data']->jobs_name ?></td>
            </tr>
          </tbody>
        </table>
      </div>
      <?php 
        $counter++;
        if($counter%4==0 && $counter!=sizeof($detail)){
          echo '<div class="page-break"></div>';
          echo '<div class="clearfix" style="margin-bottom:10px;">&nbsp;</div>';
        }
        endforeach; 
      ?>
    </div>
  </div>
</div>