<?php if (! defined('BASEPATH')) exit('No direct script access allowed');


echo form_open(base_url(uri_string()), array('class' => 'form-horizontal form-ajax'));
?>
    <div class="col-lg-12" style="padding: 0 !important">
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
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                  <label for="inventory_id" class="col-sm-3 control-label">Nama Waiter</label>
                                  <div class="col-sm-9">
                                    <select name="user_id" id="user_id" field-name="nama waiter" class="form-control select2 requireDropDown">
                                      <option value="">Pilih Waiter</option>
                                      <?php foreach($users as $u): ?>
                                      <option value="<?php echo $u->id ?>" <?php echo ($u->id==$target->user_id ? "selected" : "") ?>><?php echo ucfirst($u->name) ?></option>
                                      <?php endforeach; ?>
                                    </select>
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label for="inventory_id" class="col-sm-3 control-label">Tipe Target</label>
                                  <div class="col-sm-9">
                                    <div class="checkbox">
                                      <label style="padding-left:0px;">
                                        <input type="radio" class="target_type" name="target_type" value="1" checked> Target Total Penjualan
                                      </label>
                                      <label>
                                        <input type="radio" class="target_type" name="target_type" value="2" <?php echo ($target->target_type==2 ? "checked" : "") ?>> Target Penjualan Item
                                      </label>
                                    </div>
                                  </div>
                                </div>
                                <div class="form-group" id="target_by_total_html" style="<?php echo ($target->target_type==1 ? "" : "display:none;") ?>">
                                  <label for="inventory_id" class="col-sm-3 control-label">Target By Total Penjualan</label>
                                  <div class="col-sm-9">
                                    <input type="text" class="form-control" name="target_by_total" id="target_by_total" value="<?php echo $target->target_by_total ?>">
                                  </div>
                                </div>
                                <div id="target_by_item_html" style="<?php echo ($target->target_type==2 ? "" : "display:none;") ?>">
                                  <a href="javascript:void(0);" id="add_target_menu" class="btn btn-primary pull-right">Tambah Target Menu</a>
                                  <table class="table table-bordered" id="target_detail_table">
                                    <thead>
                                      <tr>
                                        <th>Nama Menu</th>
                                        <th width="150">Jumlah Target</th>
                                        <th width="40">Aksi</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <?php foreach($detail as $d): ?>
                                      <tr>
                                        <td>
                                          <select name="detail[menu_id][]" class="form-control select2">
                                            <option value="">Pilih Menu</option>
                                            <?php foreach($menu_lists as $l): ?>
                                            <option value="<?php echo $l->id ?>" <?php echo ($l->id==$d->menu_id ? "selected" : "") ?>><?php echo $l->menu_name ?></option>
                                            <?php endforeach; ?>
                                          </select>
                                        </td>
                                        <td>
                                          <input type="text" name="detail[target_qty][]" class="form-control only_numbers" value="<?php echo $d->target_qty ?>">
                                        </td>
                                        <td>
                                          <a href="javascript:void(0);" class="btn btn-danger remove_target_detail">Hapus</a>
                                        </td>
                                      </tr>
                                      <?php endforeach; ?>
                                    </tbody>
                                  </table>
                                </div>
                                <div class="form-group">
                                  <label for="inventory_id" class="col-sm-3 control-label">Reward</label>
                                  <div class="col-sm-9">
                                    <div class="pull-left" style="margin-top: 5px;margin-right: 5px;">
                                      <input type="checkbox" name="is_percentage" value="1" <?php echo ($target->is_percentage==1 ? "checked" : "") ?>> % 
                                    </div>
                                    <div class="pull-left">
                                      <input type="text" class="form-control requiredTextField" name="reward" id="reward" value="<?= $target->reward ?>">
                                    </div>
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label for="inventory_id" class="col-sm-3 control-label">Masuk ke perhitungan Gaji ?</label>
                                  <div class="col-sm-9" style="margin-top:7px;">
                                    Ya
                                    <input type="hidden" name="calculate_to_payroll" id="calculate_to_payroll" value="1">
                                  </div>
                                </div>
                                <div class="form-group">
                                  <div class="col-sm-offset-4 col-sm-8">
                                    <button type="submit" name="btnAction" value="save" class="btn btn-primary"><?php echo $this->lang->line('ds_submit_save'); ?></button>
                                    <button type="submit" name="btnAction" value="save_exit" class="btn btn-primary"><?php echo $this->lang->line('ds_submit_save_exit'); ?></button>
                                    <a href="<?php echo base_url(SITE_ADMIN . '/target_settings'); ?>" class="btn btn-default"><?php echo $this->lang->line('ds_submit_cancel'); ?></a>
                                  </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php echo form_close(); ?>
<div id="tmp" style="display:none;">
  <select name="detail[menu_id][]" class="form-control">
    <option value="">Pilih Menu</option>
    <?php foreach($menu_lists as $l): ?>
    <option value="<?php echo $l->id ?>"><?php echo $l->menu_name ?></option>
    <?php endforeach; ?>
  </select>
</div>