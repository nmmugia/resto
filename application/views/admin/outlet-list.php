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
        <div class="panel-heading">
            <a href="<?php echo base_url(SITE_ADMIN . '/outlet/add'); ?>" class="btn btn-primary pull-right"><i
                    class='fa fa-plus'></i> Add Outlet</a>

            <div class="clearfix"></div>
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">

            <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-outlet">
                <thead>
                <tr>
                    <th>ID</th>
                    <th><?php echo $this->lang->line('column_outlet'); ?></th>
                    <th><?php echo $this->lang->line('column_store'); ?></th>
                    <th style="text-align: center"><?php echo $this->lang->line('column_action'); ?></th>
                </tr>
                </thead>
            </table>
            <input type="hidden" id="dataProcessUrl"
                   value="<?php echo base_url(SITE_ADMIN . '/outlet/getdatatables'); ?>"/>

            <!-- /.table-responsive -->
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->