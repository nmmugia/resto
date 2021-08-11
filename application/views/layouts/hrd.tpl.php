<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/15/2014
 * Time: 11:50 AM
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
  <link href="<?php echo base_url(); ?>assets/css/splitter.css" rel="stylesheet">
    <!-- Bootstrap Core CSS -->
    <link href="<?php echo base_url('assets/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="<?php echo base_url('assets/css/plugins/metisMenu/metisMenu.min.css'); ?>" rel="stylesheet">

    <!-- Timeline CSS -->
    <link href="<?php echo base_url('assets/css/plugins/timeline.css'); ?>" rel="stylesheet">

    <!-- DataTables CSS -->

    <link href="<?php echo base_url('assets/js/plugins/dataTables/css/dataTables.bootstrap.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/js/plugins/dataTables/css/dataTables.responsive.css'); ?>" rel="stylesheet">


    <link href="<?php echo base_url('assets/js/plugins/jquery-ui/jquery-ui.min.css'); ?>" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?php echo base_url('assets/css/admin.css'); ?>" rel="stylesheet">

    <!-- Morris Charts CSS -->
    <link href="<?php echo base_url('assets/css/plugins/morris.css'); ?>" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="<?php echo base_url('assets/font-awesome/css/font-awesome.min.css'); ?>" rel="stylesheet"
          type="text/css">

    <link href="<?php echo base_url('assets/js/plugins/timepicker/jquery.timepicker.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/js/plugins/select2/select2.min.css'); ?>" rel="stylesheet">


    <link href="<?php echo base_url(); ?>assets/css/bootstrap-datetimepicker.css" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/plugins/list.min.js"></script>
    <style>
    label  {
        margin-top:-7px;
    }

</style>
</head>

<body>
<div id="cover"></div>
<div id="wrapper">

    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?php echo base_url(SITE_ADMIN); ?>">POS 
                <?php 
                    echo $user_groups_data[0]->description;
                ?></a>
        </div>
        <!-- /.navbar-header -->

        <ul class="nav navbar-top-links navbar-right">
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-user">
                    <li><a href="<?php echo base_url(SITE_ADMIN . '/logout'); ?>"><i class="fa fa-sign-out fa-fw"></i>
                            Logout 
                            <?php 
                            echo $user_profile_data->name;
                            ?>
                        </a>
                    </li>
                </ul>
                <!-- /.dropdown-user -->
            </li>
            <!-- /.dropdown -->
        </ul>
        <!-- /.navbar-top-links -->

        <div class="navbar-default sidebar" role="navigation">
            <div class="sidebar-nav navbar-collapse">
                <?php $this->load->view('partials/adminmenu.tpl.php'); ?>
            </div>
            <!-- /.sidebar-collapse -->
        </div>
        <!-- /.navbar-static-side -->
    </nav>

    <div id="page-wrapper">
        <?php
        if (isset($is_draw) && $is_draw === true) {
            ?>
            <div class="row">
                <?php echo $content; ?>
            </div>
        <?php
        }
        else {
            ?>
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header"><?php if (isset($subtitle)) echo $subtitle; ?></h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <?php echo $content; ?>
            </div>
        <?php } ?>
    </div>
    <!-- /#page-wrapper -->

</div>
<!-- /#wrapper -->

<input type="hidden" id="root_base_url" value="<?php echo base_url(); ?>"/>
<input type="hidden" id="store_id_config" value="<?php echo $this->data['setting']['store_id']; ?>"/>
<input type="hidden" id="server_base_url" value="<?php echo $this->data['setting']['server_base_url']; ?>"/>
<script data-main="<?php echo base_url('assets/js/main-hrd'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>
</body>

</html>