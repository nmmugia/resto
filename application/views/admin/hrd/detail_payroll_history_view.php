<?php if (! defined('BASEPATH')) exit('No direct script access allowed'); 
?>
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
    width:130px;
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
<a href="<?php echo base_url("admincms/hrd_payroll/print_dot_matrix/".$form_data->id) ?>" class="not-print btn btn-primary pull-right" style="margin-right:20px;">Cetak Dot Matrix</a>
<a href="javascript:window.print()" class="not-print btn btn-primary pull-right" style="margin-right:20px;">Cetak</a>
<div class="col-lg-12 table-print">
  <div class="panel panel-default">
    <div class="panel-body">
      <?php 
        $counter=0;
        foreach($detail as $d): 
      ?>
      <div class="col-sm-offset-3 col-sm-6 background_color_<?php echo ($d['data']->background_color=='' ? 'white' : $d['data']->background_color) ?>" data-test="red" style="border:1px solid black;padding:10px;width:50%;float:left;background-color:<?php echo ($d['data']->background_color=='' ? 'white' : $d['data']->background_color) ?>;">
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
              <td align="right"><?php echo number_format($l->value,0) ?></td>
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
<?php /*
<div class="col-lg-12">
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
    <?php 
    echo form_open(base_url(uri_string()), array('class' => 'form-horizontal form-ajax'));
    ?>
    <ol class="breadcrumb">
        <li><a href="#">Home</a></li>
        <li><a href="<?php echo base_url(SITE_ADMIN . '/hrd_staff/staff_list'); ?>">Staff</a></li>
        <li><a href="<?php echo base_url(SITE_ADMIN . '/hrd_staff/detail_staff/'.$form_data->user_id); ?>"><?php echo $form_data->name;?></a></li>
        <li class="active"><?php echo $subtitle;?></li>
    </ol>
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
     <div class="row ">
        <div class="col-lg-5 col-sm-offset-3">
            <div class="panel panel-default">
                <div class="panel-body"> 
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-md-3 text-left">
                                <label for="floor_name" class=" control-label">Nama</label> 
                            </div> 
                            <div class="col-md-5 text-left" >
                                <?php echo $form_data->name;?> 
                            </div>  
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-md-3 text-left">
                                <label for="floor_name" class=" control-label">Jabatan</label> 
                            </div> 
                            <div class="col-md-5 text-left">
                                <?php echo $form_data->jobs_name;?> 
                            </div>  
                        </div> 
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-md-3 text-left ">
                                <label for="floor_name" class=" control-label">Period</label> 
                            </div> 
                            <div class="col-md-5 text-left">
                                 <?php echo $form_data->period;?> 
                            </div>  
                        </div>
                     </div>
                </div>
            <!-- /.panel -->
            </div>
              

            <div class="panel panel-default">
                <div class="panel-heading">Slip Gaji</div>
                <div class="panel-body">
                     
                    <table class="table table-striped" id="subtrahend_container"
                           style="margin: 20px 0!important;">
                           <tr   class="countIngredient">
                                <td align="left">
                                    <b>Penambahan</b>
                                </td>
                            </tr> 
                        <?php
                        $counterSubtrahend = 0;
                        $total = 0;
                        if (! empty($data_enhancer_jobs_component)) { 
                            foreach ($data_enhancer_jobs_component as $po) { 
                            ?>
                             <tr   class="countIngredient">
                                <td>
                                    <div class="row"> 
                                        <div class="col-md-5 text-left" ><?php echo $po->component_name;?></div>
                                          <div class="col-md-2">
                                Rp.
                              </div> 
                                        <div class="col-md-5 text-right" style="float:right">  <?php   
                                        echo number_format($po->value,0,"",".");
                                        $total += $po->value;
                                        ?>
                                        </div> 
                                    </div>
                                </td>
                            </tr>
                            <?php
                                $counterSubtrahend++;
                            }
                        } ?>
                        <tr   class="countIngredient">
                            <td align="left">
                                <b>Pengurangan</b>
                            </td>
                        </tr>
                          
                        <?php
                        $counterSubtrahend = 0;
                        if (! empty($data_subtrahend_jobs_component)) { 
                            foreach ($data_subtrahend_jobs_component as $po) { 
                            ?>
                             <tr   class="countIngredient">
                                <td>
                                    <div class="row"> 
                                         <div class="col-md-5 text-left" ><?php echo $po->component_name;?></div>
                                           <div class="col-md-2">
                                                Rp.
                                            </div> 
                                        <div class="col-md-5 text-right" style="float:right"> <?php  

                                          echo number_format($po->value,0,"","."); 
                                        $total -= $po->value;
                                        ?>
                                        </div> 
                                    </div>
                                </td>
                            </tr>
                            <?php
                                $counterSubtrahend++;
                            }
                        } ?>
                    </table> 
                         
                </div>
                <!-- /.panel -->
            </div>

             <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-12">
                             <div class="col-md-5">
                                <label for="floor_name" class=" control-label">Total</label> 
                              </div> 
                              <div class="col-md-2">
                                Rp.
                              </div> 
                              <div class="col-md-5 text-right" style="float:right">
                                
                               <?php    echo number_format($total,0,"",".");?>
                               
                            </div> 
                            
                        </div>
                        <!-- /.row (nested) -->
                    </div>
                    <!-- /.panel-body -->
                </div>
            <!-- /.panel -->
            </div>
 
         </div>
     </div>
    <!-- /.panel --> 
 <?php echo form_close(); ?>
<input type="hidden" id="admin_url" value="<?php echo base_url(SITE_ADMIN) ?>"/>
<input type="hidden" id="root_base_url" value="<?php echo base_url(); ?>"/>
 
<input type="hidden" id="data_enhancer_salary_component" value='<?php echo json_encode($data_enhancer_salary_component) ?>'/>
<input type="hidden" id="data_substrahend_salary_component" value='<?php echo json_encode($data_substrahend_salary_component) ?>'/>
*/ ?>
<?php

// function add_enhancer_func($data, $count,$form_data ,$outlet_ddl)
// { 
//     $stuff = '
//     <tr id="enhancer-' . $count . '" class="countIngredient">
//         <td>
//             <div class="row"> 
//                 <div class="col-md-5">'.
//                 form_dropdown('
//                     enhancer['.$count.'][component_id]', 
//                     $outlet_ddl, 
//                     $data->component_id, 
//                     'id="ingredient_id_chained_'.$count.'"  
//                     class="form-control 
//                     requiredDropdown ingredient_id_chained" 
//                     autocomplete="off" disabled
//                     url-data="'.base_url(SITE_ADMIN).'/menus/get_inventory_unit" ').
//                  '
//                                              </div>
//                 <div class="col-md-3">
//                      Rp. ' . $data->value . '
//                 </div>
               
                 
//             </div>
//         </td>
//     </tr> ';
//     echo replace_newline($stuff);
// }

// function add_substrahend_func($data, $count,$form_data ,$outlet_ddl)
// { 
//     $stuff = '
   
//     echo replace_newline($stuff);
// }

// function replace_newline($string)
// {
//     return trim((string)str_replace(array("\r", "\r\n", "\n", "\t"), ' ', $string));
// }

?>