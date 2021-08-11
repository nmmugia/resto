<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/16/2014
 * Time: 2:20 PM
 */
?>
<div class="col-lg-12">
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
<div class="panel panel-default">
    
    <!-- /.panel-heading -->
    <div class="panel-body">

        <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-menus">
            <thead>
            <tr>
                <th>ID</th>
                <th>Menu</th>
                <th>Harga</th>
                <th><?php echo $this->lang->line('outlet_title');?></th>
                <th>Kategori</th>
                <th style="text-align: center"><?php echo $this->lang->line('column_action'); ?></th>
            </tr>
            </thead>
        </table>
        <input type="hidden" id="dataProcessUrl" value="<?php echo base_url(SITE_ADMIN.'/menus/getdatatables');?>"/>

        <!-- /.table-responsive -->
    </div>
    <!-- /.panel-body -->
</div>
<!-- /.panel -->
</div>
<!-- /.col-lg-12 -->