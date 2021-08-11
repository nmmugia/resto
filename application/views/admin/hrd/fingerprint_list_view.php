<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

?>
<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
<form id="formFilter" method="get" target="_blank">
  <input type="hidden" id="report_type" value="fingerprint" name="type"/>
    <div class="clear-export"></div>
            <table class="col-lg-8 form-table-cms">
                <tbody>
                    <tr>
                    <td><label>Waktu Mulai</label></td>
                    <td class="col-sm-8">
                            <div class='input-group date ' id='start-date'>
                             <?php echo form_input(array('name' => 'start_date',
                               'id' => 'input_start_date',
                               'type' => 'text',
                               'class' => 'form-control date',
                               'onkeydown'=>'return false',
                               'value'=>date("Y-m-d"),

                               )); ?>
                               <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar">
                                </span>
                            </span>
                        </div> 
                    </td>
                   
                </tr>
                <tr>
                    <td><label>Waktu Akhir</label></td>
                    <td class="col-sm-8">
                            <div class='input-group date ' id='end-date'>
                             <?php echo form_input(array('name' => 'end_date',
                               'id' => 'input_end_date',
                               'type' => 'text',
                               'class' => 'form-control date',
                               'onkeydown'=>'return false',
                               'value'=>date("Y-m-d"),

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
                        <button id="filter_submit" class="btn btn-default"><i class="fa fa-filter" aria-hidden="true"></i> Filter</button>
                        <button id="download_fingerprint" 
                                class="btn btn-danger"><i class="fa fa-download" aria-hidden="true"></i> 
                                Download Log Finger Print
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
</form>

            <div class="clearfix"></div>
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">


        
            <table class="table table-striped table-bordered table-hover dt-responsive width100" cellspacing="0" width="100%" id="table-fingerprint">
                <thead>
                <tr>

                    <th>Nama Pegawai</th>
                    <th>Tanggal</th>
                    <th>Jam Absen</th>

                </tr>
                </thead>
            </table>

            <input type="hidden" id="dataProcessUrl"
                   value="<?php echo $data_url; ?>"/>

            <!-- /.table-responsive -->
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->
</div>
