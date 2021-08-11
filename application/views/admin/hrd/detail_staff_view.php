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
<script>
 
 var url = document.location.toString();
        if (url.match('#')) { 
            $('.nav-tabs a[href=#'+url.split('#')[1]+']').tab('show') ;
        } 

        // With HTML5 history API, we can easily prevent scrolling!
        $('.nav-tabs a').on('shown.bs.tab', function (e) {
            if(history.pushState) {
                history.pushState(null, null, e.target.hash); 
            } else {
                window.location.hash = e.target.hash; //Polyfill for old browsers
            }
        })
</script>
    <div class="col-lg-12" style="padding: 0 !important"> 
     <ol class="breadcrumb">
        <li><a href="#">Home</a></li>
        <li><a href="<?php echo base_url(SITE_ADMIN . '/hrd_staff/staff_list'); ?>">Staff</a></li> 
        <li class="active"><?php echo $users->name;?></li>
    </ol>
        <div class="row">
            <div class="col-lg-12">
            <!-- Nav tabs -->
                  <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Log Absen</a></li>
                    <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Status Kepegawaian</a></li>
                    <li role="presentation"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab">Gaji</a></li>
                    <li role="presentation"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">Performance</a></li>
                    <li role="presentation"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">Performance</a></li>
                  </ul>

                  <!-- Tab panes -->
                  <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="home">
                       
                    </div>
                    <div role="tabpanel" class="tab-pane" id="profile">
                        <?php $this->load->view('admin/hrd/history_employee_status.php'); ?>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="messages">
                          <?php $this->load->view('admin/hrd/history_employee_payroll.php'); ?>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="settings"> 
                    aa
                    </div>
                  </div>
            </div>
        </div>
    </div>
<?php echo form_close(); ?>

<script data-main="<?php echo base_url('assets/js/main-hrd'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>