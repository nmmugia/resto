<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/16/2014
 * Time: 11:05 AM
 */

echo form_open_multipart(base_url(uri_string()), array('class' => 'form-horizontal form-ajax'));
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
                                <?php
                                if (isset($use_username) && $use_username === true) { ?>
                                    <div class="form-group">
                                        <label for="username" class="col-sm-2 control-label">Username</label>

                                        <div class="col-sm-10">
                                            <?php echo form_input($username); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="password" class="col-sm-2 control-label">Password</label>

                                        <div class="col-sm-10">
                                            <?php echo form_input($password); ?>
                                            <small>* leave the password blank if don't want to change </small>
                                        </div>
                                    </div>
                                <?php
                                }
                                else {
                                    ?>
                                    <div class="form-group">
                                        <label for="store_id" class="col-sm-2 control-label"><?php echo $this->lang->line('column_store'); ?></label>

                                        <div class="col-sm-10">
                                            <?php
                                            echo form_dropdown('store_id', $store_id, $form_data->store_id, 'id="store_id_chained" field-name = "Store" class="form-control requiredDropdown" autocomplete="off"');
                                            ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="outlet_id" class="col-sm-2 control-label"><?php echo $this->lang->line('column_outlet'); ?></label>

                                        <div class="col-sm-10">
                                            <?php
                                            if($group_id == 4){
                                                echo form_dropdown('outlet_id', $outlet_id, $form_data->outlet_id, 'id="outlet_id_chained" field-name = "Outlet" class="form-control requiredDropdown" autocomplete="off"');
                                            }else{
                                                echo form_dropdown('outlet_id', $outlet_id, $form_data->outlet_id, 'id="outlet_id_chained" field-name = "Outlet" class="form-control" autocomplete="off"');
                                            }

                                            ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="username" class="col-sm-2 control-label">NIP</label>

                                        <div class="col-sm-10">
                                            <?php echo form_input($username); ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="form-group">
                                    <label for="name" class="col-sm-2 control-label">Name</label>

                                    <div class="col-sm-10">
                                        <?php echo form_input($name); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="email" class="col-sm-2 control-label">Email</label>

                                    <div class="col-sm-10">
                                        <?php echo form_input($email); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="address" class="col-sm-2 control-label">Address</label>

                                    <div class="col-sm-10">
                                        <?php echo form_textarea($address); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="phone" class="col-sm-2 control-label">Phone</label>

                                    <div class="col-sm-10">
                                        <?php echo form_input($phone); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="identity_type" class="col-sm-2 control-label">Identity</label>

                                    <div class="col-sm-10">
                                        <?php
                                        echo form_dropdown('identity_type', $identity_type, $form_data->identity_type, 'field-name = "Identity" class="form-control" autocomplete="off"');
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="identity_num" class="col-sm-2 control-label">Identity Number</label>

                                    <div class="col-sm-10">
                                        <?php echo form_input($identity_num); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="gender" class="col-sm-2 control-label">Gender</label>

                                    <div class="col-sm-10">
                                        <?php
                                        echo form_dropdown('gender', $gender, $form_data->gender, 'field-name = "Gender" class="form-control" autocomplete="off"');
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" name="btnAction" value="save"
                                                class="btn btn-primary"><?php echo $this->lang->line('ds_submit_save'); ?>
                                        </button>
                                        <button type="submit" name="btnAction" value="save_exit"
                                                class="btn btn-primary">
                                            <?php echo $this->lang->line('ds_submit_save_exit'); ?>
                                        </button>
                                        <a href="<?php echo $cancel_url ?>"
                                           class="btn btn-default"><?php echo $this->lang->line('ds_submit_cancel'); ?></a>
                                    </div>
                                </div>
                            </div>
                            <!-- /.row (nested) -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
            </div>
        </div>
    </div>
<?php echo form_hidden('id', $form_data->id); ?>
<?php echo form_hidden('group_id', $group_id); ?>
<?php echo form_hidden($csrf); ?>
<?php echo form_close(); ?>