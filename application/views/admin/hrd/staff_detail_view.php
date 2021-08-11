<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/16/2014
 * Time: 11:05 AM
 */

echo form_open_multipart(base_url(uri_string()), array('class' => 'form-horizontal form-ajax'));
?>
<style>
.form-horizontal .control-label{ 
text-align:left; 
}
</style>
 
    <div class="col-lg-12" style="padding: 0 !important"> 
     <ol class="breadcrumb">
        <li><a href="#">Home</a></li>
        <li><a href="<?php echo base_url(SITE_ADMIN . '/hrd_staff/staff_list'); ?>">Staff</a></li> 
        <li class="active"><?php echo $users->name;?></li>
    </ol>
        <div class="row">
            <div class="col-lg-12">
              <div class="pull-right" style="margin-right: 16px;">
                <a href="<?php echo base_url(SITE_ADMIN."/hrd_staff/exports/".$employee_id) ?>" class="btn btn-primary pull-right"><i class='fa fa-database'></i> Export PDF</a> 
              </div>
            <!-- Nav tabs -->
                  <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#log" aria-controls="home" role="tab" data-toggle="tab">Log Absen</a></li>
                    <li role="presentation"><a href="#status" aria-controls="profile" role="tab" data-toggle="tab">Status Kepegawaian</a></li>
                    <li role="presentation"><a href="#payroll" aria-controls="messages" role="tab" data-toggle="tab">Gaji</a></li>
                    <li role="presentation"><a href="#performance" aria-controls="settings" role="tab" data-toggle="tab">Data Fingerprint</a></li>
                  </ul>

                  <!-- Tab panes -->
                  <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="log">
                       <?php $this->load->view('admin/hrd/history_employee_attendance.php'); ?>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="status">
                        <?php $this->load->view('admin/hrd/history_employee_status.php'); ?>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="payroll">
                          <?php $this->load->view('admin/hrd/history_employee_payroll.php'); ?>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="performance"> 
                       <?php $this->load->view('admin/hrd/staff_fingerlist_view.php'); ?>
                    </div>
                  </div>
            </div>
        </div>
    </div>
<?php echo form_close(); ?>

<script data-main="<?php echo base_url('assets/js/main-hrd'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>