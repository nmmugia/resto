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
                                  <label for="inventory_id" class="col-sm-2 control-label">Nama Inventori</label>
                                  <div class="col-sm-10" style="margin-top:7px;">
                                    <input type="hidden" name="parent_inventory_id" value="<?php echo $form_data->id ?>" id="parent_inventory_id">
                                    <?php echo $form_data->name; ?>
                                  </div>
                                </div>
                                <table class="table table-bordered" id="inventory_composition">
                                  <thead>
                                    <tr>
                                      <th>Inventory</th>
                                      <th width="150">Satuan</th>
                                      <th width="100">Jumlah</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php foreach($inventory_compositions as $i): ?>
                                    <tr>
                                      <td>
                                        <?php echo $i->inventory_name; ?>
                                      </td>
                                      <td>
                                        <?php echo $i->code; ?>
                                      </td>
                                      <td>
                                        <?php echo $i->quantity; ?>
                                      </td>                                      
                                    </tr>
                                    <?php endforeach; ?>
                                  </tbody>
                                </table>
                                <div class="text-center">
                                    <a href="<?php echo $cancel_url; ?>" class="btn btn-default">Kembali</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php echo form_close(); ?>