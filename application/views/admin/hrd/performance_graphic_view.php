<?php if (! defined('BASEPATH')) exit('No direct script access allowed'); 
?>
<div class="col-lg-12">  
    <div class="panel panel-default"> 
        <div class="panel-body">
 			<div id="graph-attendance">

 			</div>
        </div> 
    </div> 
 </div> 
<input type="hidden" id="admin_url" value="<?php echo base_url(SITE_ADMIN) ?>"/>
<input type="hidden" id="root_base_url" value="<?php echo base_url(); ?>"/>

<input type="hidden" id="user_id" value="<?php echo $employee_id ?>"/>
<!-- /.col-lg-12 -->