<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/18/2014
 * Time: 3:14 PM
 */
?>
    <div class="row" style="margin-top: 15px">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Table Management</div>
                <div class="panel-body form-inline">
                    <div class="btn-group">
                        <div class="input-group">
                            <span class="input-group-addon">Choose Floor</span>
                            <?php
                            echo form_dropdown('floor_id', $floor_id, '', 'id="table_floor_canvas" field-name = "Floor" class="form-control" autocomplete="off"');
                            ?>
                        </div>
                    </div>
                    <div class="btn-group">
                        <div class="input-group">
                            <span class="input-group-addon">Table name</span>
                            <?php
                            echo form_input($table_name);
                            ?>
                        </div>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-default control-add" id="addRect"><i class="fa fa-square"></i></button>
                        <button class="btn btn-default control-add" id="addTriangle"><i
                                class="fa fa-play fa-rotate-270"></i>
                        </button>
                        <button class="btn btn-default control-add" id="addCircle"><i class="fa fa-circle"></i></button>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-primary" id="save_table"><i class="fa fa-save"></i> Save</button>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-danger" id="delete_table"><i class="fa fa-trash"></i> Delete Table
                        </button>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-danger" id="clear_table"><i class="fa fa-trash"></i> Clear Floor</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body" style="background-color: #999">
                    <div id="canvasWrapper"
                         style="width: <?php echo $default_table_width; ?>;height: <?php echo $default_table_height; ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php echo form_input($loadDataTable); ?>
<?php echo form_input($saveDataTable); ?>
<?php echo form_input($getUniqueID); ?>
<?php echo form_input($deleteDataID); ?>
<?php echo form_input($clearDataTable); ?>
<?php echo form_input($defaultTableWidth); ?>
<?php echo form_input($defaultTableHeight); ?>