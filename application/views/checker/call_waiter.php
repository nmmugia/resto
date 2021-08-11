    <br><br><br><br><br><br><br><br><br><br>
<div class="col-lg-4 col-lg-offset-4">
  <div class="col-lg-12">
    <div class="row">
      <div class="row">
                <div class="title-bg-popup">
                    <a  class="btn btn-std btn-cancel btn-distance" style="float:right"> X </a></div>
      <div class="title-bg title-bg-member">
        <h4 class="title-popup">Panggil Waiter</h4>
      </div>
      <form action="" method="post" id="form_call_waiter">
        <div class="dark-theme-con" style="display:table;width:100%;padding:10px;">
          <div >
            <span><b>Waiter :</b></span>
            <select class="form-control" id="waiter_user_id" name="waiter_user_id" style="margin-bottom:15px;">
              <?php
                  echo "<option value=''>Pilih Waiter</option>";                                
                foreach ($waiter_lists as $row) {
                  echo "<option value='".$row->id."'>".$row->name."</option>";                                
                }
              ?>
            </select>
          </div>
          <div class="col-lg-12">
            <div class="row">
              <div class="button-wrapper">
                <a href="javascript:void(0);" class="btn btn-std btn-cancel btn-distance">Batal</a>
                <a href="javascript:void(0);" class="btn btn-std btn-distance" id="btn_call_waiter">Kirim Notifikasi</a>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>