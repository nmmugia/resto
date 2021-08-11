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
          <?php if($users->active==1): ?>
            <a href="<?php echo base_url(SITE_ADMIN . '/hrd_payroll/add_payroll_history/'.$employee_id); ?>" class="btn btn-primary pull-right ">
                     <div id="title-form-emp-affair" class="btn-add">Tambah History Gaji </div>
            </a>  
          <?php endif; ?>
            <div class="clearfix"></div>
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">

            <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-payroll-history">
                <thead>
                <tr> 
                    <th width="20%">Nama</th>
                     <th width="20%"> Kantor Cabang</th>
                    <th width="20%">Jabatan</th>  
                    <th width="20%">Periode</th>
                    <th width="20%">Jumlah</th>
                    <th width="20%">View</th>
                    <th style="text-align: center" ><?php echo $this->lang->line('column_action'); ?></th>
                </tr>
                </thead>
            </table>
            <input type="hidden" id="dataProcessUrlPayroll" value="<?php echo base_url(SITE_ADMIN . '/hrd_payroll/get_payroll_history/'.$employee_id); ?>"/>

            <!-- /.table-responsive -->
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel --> 
 </div> 
<input type="hidden" id="admin_url" value="<?php echo base_url(SITE_ADMIN) ?>"/>
<input type="hidden" id="root_base_url" value="<?php echo base_url(); ?>"/>

<input type="hidden" id="employee_id" value="<?php echo $employee_id ?>"/>
<!-- /.col-lg-12 -->