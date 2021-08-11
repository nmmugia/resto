<?php if (! defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="col-lg-12">
  <?php 
    echo form_open(base_url(uri_string()), array('class' => 'form-horizontal'));
  ?>
  <ol class="breadcrumb">
    <li><a href="<?php echo base_url(SITE_ADMIN); ?>">Home</a></li>
    <li class="active">Atur Grup Checker</li> 
  </ol>
  <div class="result">
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
  </div>
  <div class="panel panel-default">
    <div class="panel-body">
      <div class="col-lg-12">
				
				<div class="form-group">
          <label class="col-sm-2 control-label"></label>
          <div class="col-sm-10">
            <table class="table table-bordered ">
							<thead>
								<tr>
									<th>Grup</th>
									<th>User</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($lists as $l): ?>
								<tr>
									<td><?php echo $l->checker_number_name ?></td>
									<td><?php echo $l->checker_users ?></td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
          </div>
        </div> 
				<div class="clearfix"></div>
        <div class="form-group">
          <label class="col-sm-2 control-label">Grup</label>
          <div class="col-sm-2">
            <select name="checker_number" class="form-control select2 requiredDropDown" field-name="Grup">
              <option value="">Pilih Grup</option>
              <?php foreach($groups as $key=>$value): ?>
              <option value="<?php echo $key ?>"><?php echo $value ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div> 
        <div class="form-group" id="panel_store" >
          <label for="discount_name" class="col-sm-2 control-label">&nbsp;</label>
          <div class="col-sm-10">
            <div class="row">
              <div class="col-xs-5">
                <select class="multiselect form-control" size="15" 
                  multiple="multiple" 
                  data-right="#multiselect_to_1" 
                  data-right-all="#right_All_1" 
                  data-right-selected="#right_Selected_1" 
                  data-left-all="#left_All_1" 
                  data-left-selected="#left_Selected_1">
                  <?php 
                    foreach ($users as $key => $row) {
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
              <div class="col-xs-5">
                <select name="users[]" id="multiselect_to_1" class="form-control requiredDropDown" field-name="Pegawai" size="15" multiple="multiple"></select>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-12">
        <div class="form-group">
          <div class="col-sm-8 col-sm-offset-4" >
            <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('ds_submit_save'); ?></button>  
            <a href="<?php echo base_url(SITE_ADMIN . '/checker_groups/'); ?>" class="btn btn-default"><?php echo $this->lang->line('ds_submit_cancel'); ?></a>
          </div>
        </div> 
      </div>
    </div>
  </div>
  <?php echo form_close(); ?>  
</div>
<script data-main="<?php echo base_url('assets/js/main-hrd'); ?>" src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>