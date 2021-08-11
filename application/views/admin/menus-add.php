<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/16/2014
 * Time: 2:20 PM
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
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="#menuTab" data-toggle="tab">Menu</a>
                                    </li>
                                    <li><a href="#sideTab" data-toggle="tab">Side Dish</a>
                                    </li>
                                    <li><a href="#optionsTab" data-toggle="tab">Options</a>
                                    </li>
                                </ul>

                                <!-- Tab panes -->
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="menuTab" style="padding-top: 20px">

                                        <div class="form-group">
                                            <label for="menu_name" class="col-sm-2 control-label">Menu</label>

                                            <div class="col-sm-10">
                                                <?php echo form_input($menu_name); ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="menu_hpp" class="col-sm-2 control-label">initial Price</label>

                                            <div class="col-sm-10">
                                                <?php echo form_input($menu_hpp); ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="menu_price" class="col-sm-2 control-label">Price</label>

                                            <div class="col-sm-10">
                                                <?php echo form_input($menu_price); ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="category_id" class="col-sm-2 control-label">Category</label>

                                            <div class="col-sm-10">
                                                <?php
                                                echo form_dropdown('category_id', $category_id, $this->input->post('category_id'), 'field-name = "Category" class="form-control requiredDropdown" autocomplete="off"');
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
                                    </div>
                                    <div class="tab-pane fade in" id="sideTab" style="padding-top: 20px">
                                        <a id="add_side_dish" href="#" class="btn btn-success pull-right"><i
                                                class='fa fa-plus'></i> Add Side Dish</a>

                                        <div class="clearfix"></div>
                                        <table class="table table-striped" id="sidedish_container"
                                               style="margin: 20px 0!important;"></table>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="tab-pane fade in" id="optionsTab" style="padding-top: 20px">
                                        <a id="add_options" href="#" class="btn btn-success pull-right"><i
                                                class='fa fa-plus'></i> Add Option</a>

                                        <div class="clearfix"></div>
                                        <table class="table table-striped" id="options_container"
                                               style="margin: 20px 0!important;"></table>
                                        <div class="clearfix"></div>
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
                                            <a href="<?php echo base_url(SITE_ADMIN . '/menus'); ?>"
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
    </div>
<?php echo form_input($sidedishCount); ?>
<?php echo form_input($optionsCount); ?>
<?php echo form_input($optionsValueCount); ?>
<?php echo form_close(); ?>