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
    <div class="panel panel-default">
        
        <!-- /.panel-heading -->
        <div class="panel-body"> 
            <div class="clearfix"></div><br>
            <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-schedule-staff"> 
            <thead>
            <tr> 
               
                <th width="200px">Nip</th>
                <th>Nama</th> 
                <th width="200px">Cuti</th>
                <th width="200px">Pindah Jadwal</th> 
            </tr>
            </thead>
            </table>
            <input type="hidden" id="dataProcessUrl"
                   value="<?php echo $data_url ?>"/>

            <!-- /.table-responsive -->
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->

<script data-main="<?php echo base_url('assets/js/main-hrd'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>