<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="col-lg-12">
  <?php
  if (!empty($message_success)) {
      echo '<div class="alert alert-success" role="alert">';
      echo $message_success;
      echo '</div>';
  }
  if (!empty($message)) {
      echo '<div class="alert alert-danger" role="alert">';
      echo $message;
      echo '</div>';
  }
  ?>
  <div class="panel panel-default">
    <div class="panel-body">
      <form role="form" class="form-horizontal" method="post" action="<?= base_url(SITE_ADMIN."/beginning_balances") ?>">
        <table class="table table-bordered" id="beginning_balance_table">
          <thead>
            <tr>
              <th width="100" class="text-center">Kode Akun</th>
              <th class="text-center">Nama Akun</th>
              <th width="200" class="text-center">Saldo Awal</th>
            </tr>
          </thead>
          <tbody>
            <?php 
              $counter=0;
              foreach($coa_types as $ct){
                echo '<tr style="background:lightgray;">';
                echo '<td colspan="3"><b>'.$ct->account_type_name.'</b></td>';
                echo '</tr>';
                foreach($data[$ct->account_type_id] as $d){
                  echo '<tr>';
                  echo '<td>'.$d->code.'</td>';
                  echo '<td>'.$d->name.'</td>';
                  // if($d->is_exists==0){
                    echo '<td class="form-group">
                      <input type="hidden" name="detail[account_id][]" value="'.$d->id.'">
                      <input type="text" class="form-control text-right only_numeric" name="detail[amount]['.$counter.']" value="0">
                      <span class="help-block" style="display:none;"></span>
                    </td>';
                    $counter++;
                  // }else{
                    // echo '<td></td>';
                  // }
                  echo '</tr>';
                }
              }
            ?>
          </tbody>
        </table>
        <div class="form-group">
          <div class="text-center">
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>