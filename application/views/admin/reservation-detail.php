<form method="post" class="form-horizontal form-ajax">
  <div class="col-lg-12" style="padding: 0 !important">
    <div class="row">
      <div class="col-lg-12">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="row">
              <div class="col-lg-12">
                <h2>Detail Reservasi</h2>
                <div class="form-group">
                  <label class="col-sm-3 control-label">Atas Nama</label>
                  <div class="col-sm-9" style="margin-top: 7px;"><?php echo $reservation->customer_name; ?></div>
                </div>
                <div class="form-group">
                  <label class="col-sm-3 control-label">Nomor Kontak</label>
                  <div class="col-sm-9" style="margin-top: 7px;"><?php echo $reservation->phone; ?></div>
                </div>
                <div class="form-group">
                  <label class="col-sm-3 control-label">Waktu Booking</label>
                  <div class="col-sm-9" style="margin-top: 7px;"><?php echo date("d/m/Y H:i:s",strtotime($reservation->book_date)); ?></div>
                </div>
                <div class="form-group">
                  <label class="col-sm-3 control-label">Jumlah Tamu</label>
                  <div class="col-sm-9" style="margin-top: 7px;"><?php echo $reservation->customer_count; ?></div>
                </div>
                <div class="form-group">
                  <label class="col-sm-3 control-label">Catatan</label>
                  <div class="col-sm-9" style="margin-top: 7px;"><?php echo $reservation->book_note; ?></div>
                </div>
                <div class="form-group">
                  <label class="col-sm-3 control-label">Meja</label>
                  <div class="col-sm-9" style="margin-top: 7px;"><?php echo $reservation->table_name; ?></div>
                </div>
                <div class="form-group">
                  <label class="col-sm-3 control-label">Down Payment</label>
                  <div class="col-sm-9" style="margin-top: 7px;"><?php echo number_format($reservation->down_payment,0,",",","); ?></div>
                </div>
                <div class="form-group">
                  <label class="col-sm-3 control-label">Status Reservasi</label>
                  <div class="col-sm-9" style="margin-top: 7px;"><?php echo $reservation->reservation_status; ?></div>
                </div>
                <?php if($reservation->status==3): ?>
                <div class="form-group">
                  <label class="col-sm-3 control-label">Catatan Gagal</label>
                  <div class="col-sm-9" style="margin-top: 7px;"><?php echo $reservation->failed_note; ?></div>
                </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
<script data-main="<?php echo base_url('assets/js/main-admin'); ?>" src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>