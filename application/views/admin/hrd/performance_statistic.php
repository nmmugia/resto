<?php if (! defined('BASEPATH')) exit('No direct script access allowed'); 
?>
<div class="col-lg-12">
    <div class="panel panel-default"> 
        <div class="panel-body"> 
            <div class="result">
                <?php
                if (!empty($message_success)) {
                    echo '<div class="alert alert-success" role="alert">';
                    echo $message_success;
                    echo '</div>';
                }
                if (!empty($message)) {
                    echo '<div class="alert alert-danger" role="alert">';
                    echo $message;
                    echo '</div>';
                }
                ?>
            </div>  
            <div class="row">
                <div class="col-lg-12 form-group">
 
            <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-perfomance-statistic">
                <thead>
                 <tr>  
                    <th  >Keterangan</th> 
                    <th  >Jumlah (Hari)</th> 
                </tr>
                </thead>
            </table>
            <input type="hidden" id="dataProcessUrlAttendanceSta" value="<?php echo base_url(SITE_ADMIN . '/hrd_attendance/get_attendance_statistic/'.$data_users->id); ?>"/>
                </div> 
            </div>
              
        </div> 
    </div>
</div>