<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
echo form_open_multipart(base_url(uri_string()).'/update', array('class' => 'form-horizontal form-ajax'));
?>
<div class="col-lg-12">

    <div id="ajax-msg">

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
    <div class="form-group">
        <div class="panel panel-default">        
            <div class="panel-body">
                <table class="table table-striped table-bordered table-hover dt-responsive">
                    <thead>
                        <tr>
                            <th>Type Order</th>
                            <th>Tax &amp; Services</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Dine In</td>
                            <td>
                                <?php
                                    $taxes = $this->tax_model->get_taxes(1, $tax_method);
                                    foreach ($taxes as $key => $value) {
                                        $data = array(
                                            'name'        => 'taxes['.$value->id.']',
                                            'value'       => 1,
                                            'checked'     => $value->is_active == 1 ? TRUE : FALSE,
                                            'style'       => 'margin:10px',
                                        );

                                        echo form_checkbox($data);
                                        echo " ".$value->tax_name." (".$value->tax_percentage."%)";
                                        echo "</br>";
                                    }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Takeaway</td>
                            <td>
                                <?php
                                    $taxes = $this->tax_model->get_taxes(2, $tax_method);
                                    foreach ($taxes as $key => $value) {
                                        $data = array(
                                            'name'        => 'taxes['.$value->id.']',
                                            'value'       => 1,
                                            'checked'     => $value->is_active == 1 ? TRUE : FALSE,
                                            'style'       => 'margin:10px',
                                        );

                                        echo form_checkbox($data);
                                        echo " ".$value->tax_name." (".$value->tax_percentage."%)";
                                        echo "</br>";
                                    }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Delivery</td>
                            <td>
                                <?php
                                    $taxes = $this->tax_model->get_taxes(3, $tax_method);
                                    foreach ($taxes as $key => $value) {
                                        $data = array(
                                            'name'        => 'taxes['.$value->id.']',
                                            'value'       => 1,
                                            'checked'     => $value->is_active == 1 ? TRUE : FALSE,
                                            'style'       => 'margin:10px',
                                        );

                                        echo form_checkbox($data);
                                        echo " ".$value->tax_name." (".$value->tax_percentage."%)";
                                        echo "</br>";
                                    }
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- /.panel-body -->
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" name="btnAction" value="save"
                            class="btn btn-primary"><?php echo $this->lang->line('ds_submit_save'); ?>
                    </button>
                </div>
            </div>
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
        