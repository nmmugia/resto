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
                  <label for="category_name" class="col-sm-2 control-label">Nama Template</label>
                  <div class="col-sm-10">
                    <?php echo form_input($template_name); ?>
                  </div>
                </div>
                <div class="form-group">
                  <label for="category_name" class="col-sm-2 control-label">Note</label>
                  <div class="col-sm-10">
                    <?php echo form_textarea($note); ?>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" name="btnAction" value="save" class="btn btn-primary"><?php echo $this->lang->line('ds_submit_save'); ?></button>
                    <button type="submit" name="btnAction" value="save_exit" class="btn btn-primary"><?php echo $this->lang->line('ds_submit_save_exit'); ?></button>
                    <a href="<?php echo base_url(SITE_ADMIN . '/reservation_template_notes'); ?>" class="btn btn-default"><?php echo $this->lang->line('ds_submit_cancel'); ?></a>
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