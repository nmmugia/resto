<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

?>
<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
<form id="formFilter" method="POST">
<input type="hidden" id="type" name="type" value="attendance_detail">
    <div class="clear-export"></div>
            <table class="col-lg-8 form-table-cms">
                <tbody>

                 <tr>
                    <td><label>Pegawai</label></td>
                    <td>   
                        <?php echo form_dropdown('user_id', $data_users, 
                                            "", 
                                            'id="user_id" field-name = "Nama Pegawai" 
                                            class="form-control requiredDropdown select2" autocomplete="on"');
                        ?>
                    </td>

                    </tr>

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
                               <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar">
                                </span>
                            </span>
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
                               <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar">
                                </span>
                            </span>
                        </div> 
                    </td>
                    
                    
                </tr>
               
               
                <tr>
                    <td colspan="4" align="right">
                        
                        <!-- <button id="export_xls" class="hide_btn btn btn-default" style="float:right;display: none">Export XLS</button> -->
                        <button id="filter_submit_attendance_detail" class="btn btn-default"><i class="fa fa-filter" aria-hidden="true"></i> Filter</button>

                        <button id="export_pdf" class="hide_btn btn btn-success" ><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Export PDF</button>
                    </td>
                </tr>
                </tbody>
            </table>
</form>

            <div class="clearfix"></div>
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body"> 

            <div class="clearfix"></div>

            <table class="table table-striped table-bordered table-hover dt-responsive" cellspacing="0" width="100%" id="report-attendance-detail">
                <thead>
                <tr>  
                    <th>Nama</th> 
                    <th>Tanggal</th>
                    <th>Jam Masuk</th>
                    <th>Jam Keluar</th>
                    <th>Absen Masuk</th>
                    <th>Absen Keluar</th>
                    <th>Status</th>
                    <th>Note</th>
                </tr>
                </thead>
               <!--  <tr>
                    <th colspan="9" align="center" valign="middle"><?php echo $this->lang->line('empty_data'); ?></th>
                </tr> -->
            </table>

            <input type="hidden" id="dataProcessUrlReportAttendance"  value="<?php echo $data_url; ?>"/>

            <!-- /.table-responsive -->
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->
</div>
<input type="hidden" id="report_type" value="transaction"/>
<!-- /.col-lg-12 -->