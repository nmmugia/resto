<?php if (! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Daftar Absensi</title>
    <link href="<?php echo base_url('assets/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/js/plugins/jquery-ui/jquery-ui.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/admin.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/font-awesome/css/font-awesome.min.css'); ?>" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="<?php echo base_url("assets/js/libs/jquery.js") ?>"></script>
</head>
<body>
  <div id="wrapper">
    <div id="page-wrapper" style="margin: 0px;">
      <div class="row">
        <div class="col-lg-12">
          <h1 class="page-header">Daftar Absensi</h1>
        </div>
      </div>
      <div class="row">
        <table class="table table-bordered" id="attendance_table">
          <thead>
            <tr>
              <th width="5%">No</th>
              <th width="55%">Nama Pegawai</th>
              <th width="15%" class="text-center">Tanggal</th>
              <th width="15%" class="text-center">Jam Masuk</th>
              <th width="15%" class="text-center">Jam Pulang</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    var BASE_URL="<?php echo base_url() ?>";
    $(document).ready(function(){
      setInterval(function(){
        $.ajax({
          url:BASE_URL + "attendances/get_attendances",
          success:function(response){
            $("#attendance_table tbody").html(response);
          }
        });        
      },10000)
    });
  </script>
</html>