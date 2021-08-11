<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/15/2014
 * Time: 3:08 PM
 */
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?php echo $this->data['setting']['site_title'];
        if (isset($title)) echo " " . $this->data['setting']['site_title_delimiter'] . " " . $title; ?></title>

    <!-- Bootstrap Core CSS -->
    <link href="<?php echo base_url('assets/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?php echo base_url('assets/css/admin.css'); ?>" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="<?php echo base_url('assets/font-awesome/css/font-awesome.min.css'); ?>" rel="stylesheet"
          type="text/css">

	<link href="<?php echo base_url('assets/css/module-highlight.css'); ?>" rel="stylesheet">
	<script src="<?php echo base_url('assets/js/libs/jquery.js'); ?>"></script>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body style="background-color: #3B1E10;">

<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="col-lg-4 col-md-offset-2"><br>
                        <img   src="<?php echo base_url() ?>assets/img/reskin/logo.png" width=200px
                             alt="logo"/>
                </div>
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Silahkan Login </h3>
                </div>
                <div class="panel-body">
                    <?php
                    if(!empty($message_error)){
                        echo '<div class="alert alert-danger" role="alert">'.$message_error.'</div>';

                    }else if(!empty($message_success)){
                        echo '<div class="alert alert-success" role="alert">'.$message_success.'</div>';

                    }
                    ?>
                    <?php echo form_open(base_url(uri_string()));?>
                        <fieldset>
                            <div class="form-group">
                                <?php echo form_input($identity);?>
                            </div>
                            <div class="form-group">
                                <?php echo form_input($password);?>
                            </div>
                            <?php echo form_submit('submit', lang('ds_submit_login'),'class="btn btn-lg btn-success btn-block"');?>
                        </fieldset>
                    <?php echo form_close();?>

                    <div class="clearfix"></div>
                    <a href="<?php echo base_url(); ?>" style="margin-top:15px;" class="btn btn-primary btn-block">
                        POS Login</a>
						 <?php if( $this->config->item('environment') == "development") {?> <a href="<?php echo base_url(); ?>config" style="margin-top:15px;" class="btn btn-danger btn-block">
						 CONFIG</a><?php }?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="error message">
	<h3><?php echo $this->lang->line("module_expired_header");?></h3>
	<?php
		$module_expired = $this->session->userdata("module_expired");
		foreach($module_expired as $module):
			echo "<p>".sprintf($this->lang->line("module_expired"), $module->name, date("d-m-Y", strtotime($module->due_date)))."</p>";
		endforeach
	?>
</div>
<input id="isExpiredModule" value="<?php echo (count($module_expired) > 0 ? true : false);?>" hidden />
</body>
<script>
	$(function() {
		
		var myMessages = ['info','warning','error','success'];
		var isExpiredModule = $("#isExpiredModule").val();
		console.log("login admin ");
		// module highlight
		moduleHighlightInit();
		hideAllMessages();
		
		if(isExpiredModule){
			setTimeout(function(){ 
				showMessage("error"); 
			}, 500);
		}
		
		function moduleHighlightInit(){
			// When message is clicked, hide it
			$('.message').click(function(){
				$(this).animate({top: -$(this).outerHeight()}, 500);
			}); 
		}
		
		function hideAllMessages(){
			var messagesHeights = new Array(); // this array will store height for each
			for (i=0; i<myMessages.length; i++){
				messagesHeights[i] = $('.' + myMessages[i]).outerHeight(); // fill array
				$('.' + myMessages[i]).css('top', -messagesHeights[i]); //move element outside viewport
			}
		}
		
		function showMessage(type){
			hideAllMessages();
			$('.'+type).animate({top:"0"}, 500);
		}
	});
</script>
</html>
