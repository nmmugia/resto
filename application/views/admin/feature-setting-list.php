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
      <form role="form" class="form-horizontal" method="post" action="<?= base_url(SITE_ADMIN."/feature_settings") ?>">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th class="text-center">Nama Fitur</th>
              <th class="text-center">User Unlock Akses</th>
              <th width="100" class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php 
              foreach($lists as $l){
                  echo '<tr>';
                  echo '<td>'.$l->name.'</td>';
                  echo '<td>'.$l->users_can_confirmation.'</td>';
                  echo '<td><a href="'.base_url(SITE_ADMIN."/feature_settings/set/".$l->id).'" class="btn btn-default set_feature_unlock">Set User Unlock</a></td>';
                  echo '</tr>';
                }
            ?>
          </tbody>
        </table>
      </form>
    </div>
  </div>
</div>
<div class="modal fade" id="feature_setting_modal" tabindex="-1" role="dialog"></div>