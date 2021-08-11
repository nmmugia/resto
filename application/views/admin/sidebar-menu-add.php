<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

echo form_open_multipart(base_url(uri_string()), array('class' => 'form-horizontal form-ajax'));
?>
<style type="text/css">
.ui-datepicker-trigger {
    margin: 4px;
}
</style>
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
                                    <label for="discount_name" class="col-sm-2 control-label">Menu Parent</label>

                                    <div class="col-sm-10">
                                                        
                                        <?php 
                                        $disabled ="";
                                        if($action == 'edit'){
                                            // $disabled = 'disabled';
                                        }
                                        echo form_dropdown('parent_id', $ddl_menu, $parent_id, 
                                            'id="group_chained" field-name = "parent" 
                                            class="form-control " autocomplete="on"  '.$disabled.' ');
                                        ?>
                                    </div>
                                </div>

                              
                                <div class="form-group">
                                    <label for="discount_name" class="col-sm-2 control-label">Nama</label>

                                    <div class="col-sm-10">
                                        <?php echo form_input($name); ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="discount_name" class="col-sm-2 control-label">URL Controller</label>

                                    <div class="col-sm-10">
                                        <?php echo form_input($url); ?>
                                        <small>Contoh: <b>example/menu</b></small>

                                    </div>
                                </div>

                                 <div class="form-group">
                                    <label for="discount_name" class="col-sm-2 control-label">Module</label>
                                    <div class="col-sm-10">
                                      <select name="module" class="form-control">
                                        <?php foreach($module_lists as $m): ?>
                                        <option value="<?php echo $m->id ?>" <?php echo (isset($form_data) && $form_data->module_id==$m->id ? "selected" : "") ?>><?php echo $m->name ?></option>
                                        <?php endforeach; ?>
                                      </select>
                                    </div>
                                </div>
                 
                                 <div class="form-group" id="panel_store" >
                                    <label for="discount_name" class="col-sm-2 control-label">Hak Akses</label>

                                    <div class="col-sm-10">
                                        <div class="row">
                                         <div class="col-xs-5">
                                            <select name="from" class="multiselect form-control" size="8" 
                                            multiple="multiple" 
                                            data-right="#multiselect_to_1" 
                                            data-right-all="#right_All_1" 
                                            data-right-selected="#right_Selected_1" 
                                            data-left-all="#left_All_1" 
                                            data-left-selected="#left_Selected_1">

                                            <?php 
                                                foreach ($groups as $key => $row) {
                                                    # code...
                                                    echo "<option value='".$row->id."'>".$row->name."</option>";
                                                }
                                            ?>
                                            </select>
                                        </div>

                                        <div class="col-xs-2">
                                            <button type="button" id="right_All_1" class="btn btn-block"><i class="glyphicon glyphicon-forward"></i></button>
                                            <button type="button" id="right_Selected_1" class="btn btn-block"><i class="glyphicon glyphicon-chevron-right"></i></button>
                                            <button type="button" id="left_Selected_1" class="btn btn-block"><i class="glyphicon glyphicon-chevron-left"></i></button>
                                            <button type="button" id="left_All_1" class="btn btn-block"><i class="glyphicon glyphicon-backward"></i></button>
                                        </div>

                                        <input type="hidden" name="menu_access" id="menu_access" value="45" >
                                        <div class="col-xs-5">
                                            <select name="to" id="multiselect_to_1" class="form-control" size="8" multiple="multiple">
                                                <?php 
                                                foreach ($selected_groups as $key => $row) {
                                                    # code...
                                                    echo "<option value='".$row->id."'>".$row->name."</option>";
                                                }
                                            ?>
                                            </select>
                                        </div>
                                    </div>
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
                                        <a href="<?php echo $cancel_url; ?>"
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
<?php echo form_close(); ?>
<script data-main="<?php echo base_url('assets/js/main-sidebar'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>