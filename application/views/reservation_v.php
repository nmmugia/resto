<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

?>
<style type="text/css">
.panel-heading{
    background-color: #fff;

}
</style>
<!-- popup -->
    <link href="<?php echo base_url('assets/js/plugins/dataTables/css/dataTables.bootstrap.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/js/plugins/dataTables/css/dataTables.responsive.css'); ?>" rel="stylesheet">

    <script type="text/javascript" src="<?php echo base_url();?>assets/js/plugins/list.min.js"></script>
   <div class="popup-block" id="popup-reservation-note" style="display:none;">
    <div class="col-lg-6 col-lg-offset-3" style="margin-top:50px;">
        <div class="col-lg-12">
            
            <div class="title-bg">
                <h4 class="title-popup"><b>Hapus Status Reserved</b></h4>
            </div>
            <form id="form-reservation-note" action="" method="post">
                <div class="popup-panel" style="height:auto;display:table;">
                    <div class="popup-button-co" id="reservation-note" style="height:auto;display:table;">


                    </div>

                    <div class="col-lg-12">
                        <button class="btn btn-std btn-close-reserv pull-right"
                        style="width:150px;margin-top:10px;">Batal</button>
                        <button class="btn btn-std btn-save-replace-reserv pull-right" data-status=""
                        style="width:150px;margin-top:10px;"><i class="fa fa-save"></i> Simpan</button>
                </div>

                </div>

            </form>

               

            </div>
        </div>
    </div>
  <div class="popup-block" id="popup-reservation-template-note" style="display:none;">
    <div class="col-lg-6 col-lg-offset-3" style="margin-top:50px;">
      <div class="col-lg-12">
        <div class="title-bg">
          <h4 class="title-name"><b>Pilih Aturan Reservasi</b></h4>
        </div>
        <form action="<?php echo base_url("reservation/prints") ?>" method="post">
          <input type="hidden" name="prints[reservation_id]" id="print_reservation_id" value="">
          <div class="popup-panel" style="height:auto;display:table;">
            <div class="popup-button-co" id="reservation_template_content" style="height:auto;display:table;">
              <div style="width:100%;padding-left:0;">
                <select name="prints[template_id]">
                  <?php foreach($template_lists as $t): ?>
                  <option value="<?php echo $t->id ?>"><?php echo $t->template_name ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="col-lg-12 text-center">
              <button type="button" class="btn btn-std btn-close-reserv btn-cancel-reservation" style="width:150px;margin-top:10px;">Batal</button>
              <button type="submit" class="btn btn-std" style="width:150px;margin-top:10px;"><i class="fa fa-printer"></i> Cetak</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
<!-- end popup -->

<?php
  $this->load->view('partials/navigation_v');
?>



<div id="page-wrapper">
	<div class="container-fluid">
			<div class="col-lg-12" style="margin-top:40px;margin-bottom:15px;">
                <div class="row" style="display:none">
                <div class="col-sm-4">
					<div class="row">
						<div class="resto-info-mini">
							<div class="resto-info-pic">
							
							</div>
							<div class="resto-info-name">
								<?php echo $data_store->store_name; ?>
							</div>
						</div>
					</div>
				</div>
				<div class="<?php echo ($this->config->item("use_reservation_online")==1 ? "col-sm-2" : "col-sm-4") ?>">
				</div>
				 <div class="<?php echo ($this->config->item("use_reservation_online")==1 ? "col-sm-6" : "col-sm-4") ?>">
					<div class="row">
						<div class="margin-wrap">
							<div class="panel-info">
								<div class="col-xs-6" style="padding-top:2px;">
									<a href="<?php echo $add_url ?>" class="btn btn-std-yellow">Tambah</a>
                  <?php if($this->config->item("use_reservation_online")): ?>
									<a href="<?php echo base_url("reservation/get_online_data") ?>" id="get_online_data" class="btn btn-std-yellow">Tarik Data Online</a>
                  <?php endif; ?>
								</div>
								<div class="col-xs-6">
									<p class="role-info text-right"><?php echo ucfirst($this->data['group_name']); ?></p>
									<p class="role-name text-right"><?php echo $user_name; ?></p>
								</div>
							</div>
						</div>
					</div>
				</div>
				</div>
            </div>
           <!--  <div class="col-lg-12">
                <div class="row">
                    <div class="sub-header-co-table">
                        <p style="float:left;margin-left:10px;">Take Away</p>

                        <p style="float:right;margin-right:20px;">Cashier : <b><?php echo $user_name; ?></b></p>
                    </div>
                </div>
            </div> -->
            <?php if ($this->session->flashdata('message')) { ?>
                <div class="alert alert-danger">
                    <?php echo $this->session->flashdata('message'); ?>
                </div>
            <?php } ?>
			<div class="clearfix"></div>
            <div class="panel panel-default">
				
				<div class="col-lg-12">
					<div class="row">
						<div class="title-bg-custom">
							<h4 class="title-name left">reservasi</h4>
							<a href="<?php echo $add_url ?>" class="btn btn-std-yellow" style="margin-top:5px;float:right;margin-right:10px;">Tambah</a>
		  <?php if($this->config->item("use_reservation_online")): ?>
							<a style="margin-top:5px;float:right;margin-right:10px;" href="<?php echo base_url("reservation/get_online_data") ?>" id="get_online_data" class="btn btn-std-yellow">Tarik Data Online</a>
                  <?php endif; ?>
							
						</div>
					</div>
                </div><!-- /.col-md-12 -->
				
				<div class="panel-body">
					<div class="result">
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
					
					</div>

					<div style="padding-top:20px;">
					<table class="table table-striped table-bordered table-hover dt-responsive  " cellspacing="0" width="100%" id="dataTables-reservation">
						<thead>
						<tr>
							<th>Atas Nama</th>
							<th>Nomor Kontak</th>
							<th>Waktu</th>
							<th>Status</th>
							<th>Tipe Order</th>
							<th width="140px">Jumlah Tamu</th>
							<th>Catatan</th>
							<th>Meja</th>
							<th width="150px">DP</th>
							<th style="text-align: center; width:11%">Aksi</th>
						</tr>
						</thead>
					</table>
					<input type="hidden" id="dataProcessUrl"
						value="<?php echo $data_url ?>"/>

					<!-- /.table-responsive -->
					
				
					</div>
				</div>
			</div>
    </div>
</div>


    <!-- End container fluid -->
<!-- End page wrapper -->

<script data-main=" <?php echo base_url('assets/js/main-cashier'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>