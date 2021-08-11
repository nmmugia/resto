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
        <div class="panel-body">

            <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-finger-template">
                <thead>
                 <tr>   
                    <th  >Finger ID</th>
                    <th  >Size</th> 
                </tr>
                </thead>
            </table>
            <input type="hidden" id="dataProcessUrlFingertemplates" value="<?php echo base_url(SITE_ADMIN . '/hrd_staff/get_finger_template_byid/'.$employee_id); ?>"/>

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