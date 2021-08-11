<?php if (! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<style type="text/css">
  .table-print{
    text-align:justify;
  }
  .border-header{
    border-top: 1px solid black;
    border-bottom: 1px solid black;
    border-right: 0px;
    border-left: 0px;
  }
  @media print{   
    .page-header{
      display:none;
    }
    img.logo{
      width:200px !important;
      height:120px !important;
    }
    .panel-default {
      border-color: white;
    }
    img.footer-print{
      margin-top:65px;
      width:100%;
      height:180px !important;
    }
    .not-print{
      display:none;
    }
  }
</style>
<div class="col-lg-12 table-print">
  <div class="panel panel-default">
    <a href="javascript:window.print()" class="not-print btn btn-primary pull-right">Cetak</a>
    <div class="col-lg-12" style="padding:0px !important;">
      <?php if($data_store->store_logo!="" && file_exists($data_store->store_logo)): ?>
      <div class="col-lg-3 pull-left" style="padding:0px;">
        <img class="logo" src="<?php echo base_url($data_store->store_logo) ?>" width="230px" height="120px">
      </div>
      <div class="col-lg-8 text-center" style="height:100px;padding:45px 0px 0px 0px;">
      <?php else: ?>
      <div class="col-lg-12 text-center" style="height:100px;padding:45px 0px 0px 0px;">
      <?php endif; ?>
        <h4 style="margin-bottom:5px;"><?php echo ucwords($data_store->store_name); ?></h4>
        <h5 style="margin-top:0px;"><?php echo ucwords($data_store->store_address); ?></h5>
      </div>
    </div>
    <div class="clearfix"></div>
    <div class="border-header"></div>
    <div class="panel-body" style="padding:40px;">
      <div class="text-center">
        <h5 style="text-decoration:underline;margin-bottom:0px;"><b>SURAT KETERANGAN</b></h5>
        <h5><b><?php echo $resign->resign_number ?></b></h5>
      </div><br><br>
      <p>Dengan ini Manajemen <?php echo ucwords($data_store->store_name) ?> yang berdomisili di <?php echo $data_store->store_address ?> menerangkan bahwa :</p>
      <div style="margin-left:20px;">
        <div class="pull-left" style="width:120px;margin-bottom:5px;">Nama</div>
        <div class="pull-left">: <?php echo ucwords($resign->name) ?></div>
      </div>
      <div class="clearfix"></div>
      <div style="margin-left:20px;">
        <div class="pull-left" style="width:120px;">Jabatan</div>
        <div class="pull-left">: <?php echo ucwords($resign->jobs_name) ?></div>
      </div>
      <div style="margin-top:40px;">&nbsp;</div>
      <p>Pernah bekerja sebagai <?php echo ($resign->gender==1 ? "karyawan" : "karyawati") ?> di perusahaan kami sejak <?php echo date("d F Y",strtotime($resign->start)) ?> sampai <?php echo date("d F Y",strtotime($resign->date)) ?>. Atas karya dan jasanya selama ini Manajemen mengucapkan terima kasih yang sebesar-besarnya.</p>
      <br><br>
      <p>Demikian surat keterangan ini agar dipergunakan sebagaimana mestinya.</p>
      <br><br>
      
      <div class="pull-right">Bandung, <?php echo date("d F Y") ?></div>
      <div class="clearfix"></div>
      <div class="pull-right" style="margin-right:40px;">Hormat Kami,</div>
      <div class="clearfix"></div><br><br>
      <div class="pull-right" style="margin-right:70px;">HRD</div>
    </div>
    <?php if(file_exists("assets/img/footer_store.jpg")): ?>
    <div class="col-lg-12" style="padding:0px !important;">
      <img class="footer-print" src="<?php echo base_url("assets/img/footer_store.jpg") ?>" width="100%" height="300px">
    </div>
    <?php endif; ?>
  </div>
</div>