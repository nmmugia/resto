<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by BOSRESTO.
 * User: AZIS
 * Date: 09/06/2016
 * Time: 14:00 PM
 */ 
?>
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
     <div class="panel panel-default">
        <div class="panel-body">
            <div class="row"> 
                <div class="col-md-12"  >
                    
                    <table class="col-lg-8 form-table-cms">
                    <tbody>
                        <tr>
                            <td ><label>Dari Tanggal</label></td>
                            <td class="col-sm-8">
                                <div class='input-group date' id="start_date">
                                    <?php echo form_input(array(
                                         'id' => 'input_start_date',
                                         'name' => 'start_date',
                                         'type' => 'text',
                                         'class' => 'form-control date', 
                                         'onkeydown'=>'return false',
                                         'value'=>date("Y-m-d")
                                    )); ?>
                                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div> 
                            </td>
                        </tr>
                        <tr id="end_date_view" style="display:show">
                            <td ><label>Sampai Tanggal</label></td>
                            <td class="col-sm-8">
                                <div class='input-group date' id="end_date">
                                    <?php echo form_input(array(
                                         'id' => 'input_end_date',
                                         'name' => 'end_date',
                                         'type' => 'text',
                                         'class' => 'form-control end_date', 
                                         'onkeydown'=>'return false',
                                         'value'=>date("Y-m-d")
                                    )); ?>
                                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div> 
                            </td>
                        </tr>
                        <tr>
                            <td><label>Pengulangan</label></td>
                            <td class="col-sm-8">
                                <div class='input-group'>
                                        <input type="radio" name="repeat_exchange" class="repeat_status" value="0" checked=""> 
                                        Ada Tanggal Akhir
                                        &nbsp;
                                        <input type="radio" name="repeat_exchange" class="repeat_status" value="1" > 
                                        Berlaku Seterusnya
                                </div>
                            </td>
                        </tr>
                        <tr >
                            <td><label></label></td>
                            <td class="col-sm-8">
                                <div class="col-sm-6">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Tukar Shift Pegawai</label></td>
                            <td class="col-sm-8">
                                <div>
                                    <select class="form-control select2" data-width="100%" name="user_1" id="user_1">
                                                <option value="">Pilih Pegawai</option>
                                                <?php foreach($pegawai as $o): ?>
                                                <option value="<?php echo $o->id ?>"><?php echo $o->name ?></option>
                                                <?php endforeach; ?>
                                    </select>

                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><label></label></td>
                            <td class="col-sm-8">
                                <div>
                                    <select class="form-control select2" data-width="100%" name="user_2" id="user_2">
                                                <option value="">Pilih Pegawai</option>
                                                <?php foreach($pegawai as $o): ?>
                                                <option value="<?php echo $o->id ?>"><?php echo $o->name ?></option>
                                                <?php endforeach; ?>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top"><label>Alasan Tukar Shift</label></td>
                            <td class="col-sm-8">
                                <div>
                                    <?php echo form_textarea($description);?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" align="right">
                                <button id="tukar_shift" class="btn btn-primary"><i class="fa fa-exchange" aria-hidden="true"></i> Tukar / Simpan </button>

                                <!-- <button id="export_pdf" class="btn btn-success hide_btn" ><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Export PDF</button> -->
                            </td>
                        </tr>
                    </tbody>
                    </table>

                </div>  
            </div>
        </div>
    </div>
    <!--  <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">  
                <div class="col-md-12">
                    form
                </div> 
            </div>
        </div>
    </div> -->
    <div class="panel panel-default">
        
        <div class="panel-body">
            <table class="table table-striped table-bordered table-hover dt-responsive" id="table-shift">
                <thead>
                <tr>  
                    <th  >Nama</th> 
                    <th  >Tanggal Mulai</th>
                    <th  >Tanggal Akhir</th>
                    <th  >Jam Masuk</th>
                    <th  >Jam Pulang</th>
                    <th  >Jadwal / Shift</th>
                </tr>
                </thead>
            </table>
            <input type="hidden" id="dataProcessUrl" value="<?php echo $data_url;?>"/>

            <!-- /.table-responsive -->
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel --> 
</div> 
<input type="hidden" id="admin_url" value="<?php echo base_url(SITE_ADMIN) ?>"/>
<input type="hidden" id="root_base_url" value="<?php echo base_url(); ?>"/> 
<!-- /.col-lg-12 -->