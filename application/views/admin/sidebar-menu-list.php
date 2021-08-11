<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @Author: Fitria Kartika
 * @Date:   2015-10-12 11:37:36
 * @Last Modified by:   Fitria Kartika
 * @Last Modified time: 2015-10-13 18:28:12
 */
?>
<div class="col-lg-12">

    <div id="ajax-msg">

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

    </div>
    <div class="panel panel-default">
        <div class="panel-heading">

          <table class="col-lg-12 form-table-cms">
            <tbody>

              <tr>
                <td class="col-md-2"><label>Parent</label></td>
                <td class="col-md-3">
                  <?php 
                  echo form_dropdown('ddl_parent_menu', $ddl_menu, $this->input->post('ddl_parent_menu'), 
                    'id="ddl_parent_menu" field-name = "" 
                    class="form-control" 
                    style="autocomplete="on"');
                    ?>
                  </td>

                  <td>     

        <a href="<?php echo $add_url ?>" class="btn btn-primary pull-right"><i class='fa fa-plus'></i> Tambah</a>


                  </td>
                  <td></td>
                  
                </tr>
              </tbody>
            </table>



            <div class="clearfix"></div>
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">

            <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-sidebar">
                <thead>
                <tr>
                    <th>Nama</th>
                    <th>URL</th>
                    <th>Urutan</th>
                    <th>Hak Akses</th>
                    <th style="text-align: center; width:25%"><?php echo $this->lang->line('column_action'); ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>



                    <div class="clearfix"></div>
                </tbody>
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


<script data-main="<?php echo base_url('assets/js/main-sidebar'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>