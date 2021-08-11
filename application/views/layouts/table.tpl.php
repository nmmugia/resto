<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/15/2014
 * Time: 11:50 AM
 */
?>
<!DOCTYPE html>
<html>

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

    <link href="<?php echo base_url('assets/js/plugins/jquery-ui/jquery-ui.min.css'); ?>" rel="stylesheet">
    <!-- <link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300' rel='stylesheet' type='text/css'> -->
 
    <!-- Custom CSS -->
    <link href="<?php echo base_url(); ?>assets/css/style.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/opensans.css"  rel='stylesheet' type='text/css'>
	<link href="<?php echo base_url(); ?>assets/fonts/roboto/stylesheet.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/mycustom.css" rel="stylesheet">
    <?php if($setting['theme']==2): ?>
        <link href="<?php echo base_url(); ?>assets/css/mode-mini.css" rel="stylesheet">
    <?php else: ?>
        <link href="<?php echo base_url(); ?>assets/css/mode-default.css" rel="stylesheet">
    <?php endif; ?>
    <link href="<?php echo base_url(); ?>assets/css/button.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/panel.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/kitchen.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/stock.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/js/plugins/virtual-keyboard/keyboard.css" rel="stylesheet">
	<link href="<?php echo base_url(); ?>assets/fonts/roboto/stylesheet.css" rel="stylesheet">
	<link href="<?php echo base_url(); ?>assets/js/plugins/qtip2/jquery.qtip.min.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/js/plugins/list.min.js"></script>
    <!-- Custom Fonts -->
    <link href="<?php echo base_url('assets/font-awesome/css/font-awesome.min.css'); ?>" rel="stylesheet"
          type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body id="<?php echo $theme;?>">
<audio controls="controls" id="bgsound_notification" style="display:none;">
  <source src="<?= base_url("assets/sounds/new.mp3"); ?>" type="audio/mp3" />
</audio>
<div id="cover"></div>
<div id="cover_close"></div>
<div id="server-error-message" title="Server Error" style="display: none">
    <p>
        <?php echo $this->lang->line('ds_server_error'); ?>
    </p>
</div>
<div id="server-timeout-message" title="Server Timeout" style="display: none">
    <p>
        <?php echo $this->lang->line('ds_server_timeout'); ?>
    </p>
</div>
<input type="hidden" id="open_close_status" value="<?php echo  $data_open_close->status ?>"/>
<input id="base_url" type="hidden" value="<?php echo base_url(); ?>"/>
<input id="user_id" type="hidden" value="<?php echo $user_id ?>"/>
<input id="group_id" type="hidden" value="<?php echo $group_id; ?>"/>
<input id="group_name" type="hidden" value="<?php echo $group_name; ?>"/>
<input id="user_name" type="hidden" value="<?php echo $user_name; ?>"/>
<input id="use_kitchen" type="hidden" value="<?php echo $setting['use_kitchen']; ?>"/>
<input id="dining_type" type="hidden" value="<?php echo $setting['dining_type']; ?>"/>
<input id="cleaning_process" type="hidden" value="<?php echo $setting['cleaning_process']; ?>"/>
<input id="use_role_checker" type="hidden" value="<?php echo $setting['use_role_checker']; ?>"/>
<input id="auto_checker" type="hidden" value="<?php echo $setting['auto_checker']; ?>"/>
<script type="text/javascript">
  var all_cooking_status=JSON.parse('<?php echo $all_cooking_status; ?>');
</script>
<input type="hidden" id="node_url" value="<?php echo $this->config->item('node_server_ip'); ?>:<?php echo $this->config->item('node_server_port'); ?>"/>
<script src="<?php echo $this->config->item('node_server_ip'); ?>:<?php echo $this->config->item('node_server_port'); ?>/socket.io/socket.io.js"></script>

<?php echo $content;?>
</body>

</html>