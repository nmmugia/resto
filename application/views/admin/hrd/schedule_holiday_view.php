<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/16/2014
 * Time: 10:01 AM
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
     <ol class="breadcrumb">
        <li><a href="<?php echo base_url(SITE_ADMIN); ?>">Home</a></li>
        <li class="active"><?php echo $subtitle;?></li>
    </ol>
    <div class="panel panel-default">
        <div class="panel-heading">
            <form id="formFilter" method="POST" target="_blank">
            <div class="clear-export"></div>
                <table class="col-lg-8 form-table-cms">
                  <tbody>
                    <tr>
                      <td><label>Pegawai</label></td>
                      <td class="col-sm-8">
                        <?php echo form_dropdown('user_id', $users, '', 
                              'id="user_id" field-name = "Pegawai" 
                              class="def-select form-control" data-target-column="1"'); ?>
                      </td>
                    </tr>

                    <tr>
                      <td><label>Dari Tanggal</label></td>
                      <td class="col-sm-8">
                        <div class="input-group date" id="start-date">
                          <input id="id_start_date" type="text" name="start_date" value="<?php echo date("Y-m-01"); ?>" data-target-column="2" class="form-control date">
                          <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                        </div>
                      </td>
                    </tr>
                    
                    <tr>
                      <td><label>Sampai Tanggal</label></td>
                      <td class="col-sm-8">
                        <div class="input-group date" id="end-date">
                          <input id="id_end_date" type="text" name="end_date" value="<?php echo date("Y-m-d"); ?>" data-target-column="3" class="form-control date">
                          <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                        </div>
                      </td>
                    </tr>

                    
                    
                    <tr>
                      <td colspan="4" align="right">
                        <button type="button" class="btn btn-default" id="trigger_filter_hrd_holidays"><i class="fa fa-search" aria-hidden="true"></i> Cari</button>
                        &nbsp;
                        <a href="<?php echo base_url(SITE_ADMIN . '/hrd_schedule/add_holiday'); ?>" class="btn btn-primary pull-right">
                           <i class="fa fa-plus" aria-hidden="true"></i> Tambah</a>
                      </td>
                    </tr>
                  </tbody>
                </table>
            </form>
            <div class="clearfix"></div>
        </div>
         <div class="panel-body">
        <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-holidays-history">
            <thead>
            <tr> 
                <th width="20%">NIP</th>
                <th width="20%">Nama</th>
                <th width="10%">Mulai</th>  
                <th width="10%">Akhir</th>
                <th width="20%">Jumlah Hari</th> 
                <th width="20%">Actions</th> 
            </tr>
            </thead>
        </table>
        <input type="hidden" id="dataProcessUrl" value="<?php echo $data_url; ?>"/>
        </div>
    </div>
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->

<script data-main="<?php echo base_url('assets/js/main-hrd'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>