<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/16/2014
 * Time: 1:09 PM
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
                                <div class="form-group">
                                    <label for="category_name" class="col-sm-2 control-label">Category</label>

                                    <div class="col-sm-10">
                                        <?php echo form_input($category_name); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="outlet_id" class="col-sm-2 control-label"><?php echo $this->lang->line('column_outlet'); ?></label>

                                    <div class="col-sm-10">
                                        <?php
                                        echo form_dropdown('outlet_id', $outlet_id, $form_data->outlet_id, 'field-name = "Outlet" class="form-control requiredDropdown" autocomplete="off"');
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="icon_url" class="col-sm-2 control-label">Icon</label>

                                    <div class="col-sm-10">
                                        <?php echo form_input($icon_url); ?>
                                        <small>*only JPG, JPEG dan PNG, max size 1 MB</small>
                                    </div>
                                </div>
                                <?php
                                if (! empty($form_data->icon_url)) {
                                    ?>
                                    <div class="form-group" id="primaryimage">
                                        <label for="pages_slug" class="col-sm-2 control-label sr-only">Icon
                                            URL</label>

                                        <div class="col-sm-10">
                                            <img class="gc_thumbnail"
                                                 src="<?php echo base_url($form_data->icon_url); ?>"
                                                 style="padding:5px; border:1px solid #ddd"/>

                                            <a href="#"
                                               url-data="<?php echo base_url(SITE_ADMIN . '/categories/remove_image'); ?>"
                                               rel="<?php echo $form_data->id; ?>"
                                               class="btn btn-danger removeImageMenu"><i
                                                    class="fa fa-trash-o"></i></a>
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" name="btnAction" value="save"
                                                class="btn btn-primary"><?php echo $this->lang->line('ds_submit_save'); ?>
                                        </button>
                                        <button type="submit" name="btnAction" value="save_exit"
                                                class="btn btn-primary">
                                            <?php echo $this->lang->line('ds_submit_save_exit'); ?>
                                        </button>
                                        <a href="<?php echo base_url(SITE_ADMIN . '/categories'); ?>"
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
<?php echo form_hidden($csrf); ?>
<?php echo form_close(); ?>