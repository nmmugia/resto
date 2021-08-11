<?php if (! defined('BASEPATH')) exit('No direct script access allowed'); 
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
                    <div class="col-md-2">
                      <a href="<?php echo base_url("attendances") ?>" target="_blank" class="btn btn-primary">       Daftar Absensi Harian
                        </a>
                    
                    </div>
                     <div class="col-md-3">
                        <button onclick="App.downloadLogData('fingerprint_attendances')" 
                                class="btn btn-danger   sync-to-server"> 
                                Download Log Absensi
                        </button>
                    </div>
                </div>  
            </div>
        </div>
    </div>
     <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">  
                <div class="col-md-12">
                    <div class="col-lg-2">
                        <label for="floor_name" class=" control-label">Tanggal</label> 
                    </div>
                    <div class="col-md-3">
                      <div class="col-md-12">
                        <div class='input-group date' id='start-date'>
                          <input type="text" class="form-control" id="attendance_date" onkeydown="return false" value="<?php echo date("Y-m-d") ?>"> 
                          <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                        </div> 
                      </div>
                    </div>  
					<div class="col-md-2">
						<label for="floor_name" class=" control-label">Jam Kerja</label> 
                    </div> 
                    <div class="col-md-5">
                      <div class="col-md-8">
                        <select class="form-control select2" id="office_hour_id">
													<option value="">Semua Jam Kerja</option>
													<?php foreach($office_hour_lists as $l): ?>
													<option value="<?php echo $l->id ?>"><?php echo $l->name." (".$l->checkin_time." - ".$l->checkout_time.")" ?></option>
													<?php endforeach; ?>
												</select>
                      </div>
                      <div class="col-md-4">
                        <a href="javascript:void(0);" class="btn btn-primary" id="search_attendance">Cari</a>
                      </div> 
                    </div> 
                </div> 
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        
        <div class="panel-body">
            <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-attendance">
                <thead>
                <tr>  
                    <th>Nama</th> 
                    <th>Tanggal</th>
                    <th>Jadwal Kerja</th>
                    <th>Aktual Masuk</th>
                    <th>Aktual Kerja</th>
                    <th  >Absen Masuk</th>
                    <th  >Absen Pulang</th>
                    <th  >Status</th>
                    <th  >Lampiran</th>
                    <th style="text-align: center">Tindakan</th>
                    <!--<th style="text-align: center">Action</th>-->
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