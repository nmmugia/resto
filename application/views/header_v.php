<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Bosresto</title>
    <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/img/icon-bosresto.ico">
    <link href="<?php echo base_url(); ?>assets/css/style.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/fonts/opensans/stylesheet.css"  rel='stylesheet' type='text/css'>
    <link href="<?php echo base_url(); ?>assets/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/mycustom.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/button.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/panel.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/kitchen.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/fonts/roboto/stylesheet.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/stock.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/js/plugins/jquery-ui/jquery-ui.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/js/plugins/select2/select2.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/js/plugins/qtip2/jquery.qtip.min.css" rel="stylesheet">
	<link href="<?php echo base_url(); ?>assets/js/plugins/virtual-keyboard/keyboard.css" rel="stylesheet">
	<link href="<?php echo base_url('assets/css/module-highlight.css'); ?>" rel="stylesheet">
    <!--<script src="<?php echo base_url(); ?>assets/bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url() ?>assets/js/libs/require.js" data-main="<?php echo base_url() ?>assets/js/main-header"></script>-->
</head>
<div class="error message" style="top:-1000px">
	<h3><?php echo $this->lang->line("module_expired_header");?></h3>
	<?php
		$module_expired = $this->session->userdata("module_expired");
		foreach($module_expired as $module):
			echo "<p>".sprintf($this->lang->line("module_expired"), $module->name, date("d-m-Y", strtotime($module->due_date)))."</p>";
		endforeach
	?>
</div>
<input id="isExpiredModule" value="<?php echo (count($module_expired) > 0 ? true : false);?>" hidden />