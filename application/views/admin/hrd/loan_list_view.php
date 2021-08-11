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
        <div class="panel-heading">
            <a href="<?php echo $add_url ?>" class="btn btn-primary pull-right">  
            Tambah Pinjaman</a>

            <div class="clearfix"></div>
        </div>
        <div class="panel-body">

            <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-loan">
                <thead>
                <tr> 
                    <th  >Cabang</th>
                    <th  >Nama</th>
                    <th >Jabatan</th>
                    <th >Tanggal Pinjam</th>
                    <th >Jumlah</th>
                     <th >Cicilan (Bulan)</th>
                     <th  >Terbayar</th>
                    <th  >outstanding</th>
                    <th >Detail</th>
                    <th style="text-align: center"><?php echo $this->lang->line('column_action'); ?></th>
                </tr>
                </thead>
            </table>
            <input type="hidden" id="dataProcessUrlLoan" value="<?php echo $data_url;?>"/>

            <!-- /.table-responsive -->
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel --> 
</div> 
<input type="hidden" id="admin_url" value="<?php echo base_url(SITE_ADMIN) ?>"/>
<input type="hidden" id="root_base_url" value="<?php echo base_url(); ?>"/> 
<!-- /.col-lg-12 -->