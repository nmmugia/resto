<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/17/2014
 * Time: 8:29 PM
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
      <?php if($this->data['user_profile_data']->outlet_id==0 or ($this->data['user_profile_data']->outlet_id!=0 && $this->config->item("outlet_not_zero_can_opname")==1)): ?>        
        <?php endif; ?>
        <!-- /.panel-heading -->
        <div class="panel-body">
            <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-outlet">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Outlet</th>
                   
                     <th>Kategori</th>
                    <th style="text-align: center" width="30%"><?php echo $this->lang->line('column_action'); ?></th>
                </tr>
                </thead>
            </table>
            <input type="hidden" id="dataProcessUrl"
                   value="<?php echo base_url(SITE_ADMIN . '/stock/get_data_outlet_stock'); ?>"/>

            <!-- /.table-responsive -->
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->

<script data-main="<?php echo base_url('assets/js/main-admin'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>