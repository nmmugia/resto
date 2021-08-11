<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

?>
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
<div class="col-lg-12">
    <div class="panel panel-default">
    
        <!-- <div class="panel-body">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#people-tab" data-toggle="tab">by People</a></li>
                <li><a href="#group-tab" data-toggle="tab">by Groups</a></li>.
            </ul>
        </div> -->
		
        <div class="panel-body">
            <div class="tab-content">
                <div class="tab-pane fade in" id="people-tab" style="display:none">
                    <?php 
                        echo form_open(site_url($url_exchange), array('class' => 'form-horizontal form-ajax'));

                        // print_r($url_exchange);

                    ?>
                        <div class="panel-body">
                            <div class="form-group">
                                <div class="col-lg-12">
                                    <div class="col-sm-2">
                                    <label class="control-label">Tanggal Mulai</label>
                                    </div>
                                    <div class="col-sm-3">
                                    <div class='input-group date ' id='start-date'>
                                        <input type="text" class="form-control no-special-char" field-name="Tanggal Mulai" name="start_date" onkeydown="return false" value="<?php echo date("Y-m-d"); ?>"> 
                                        <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div> 
                                    </div>
                              </div>
                            </div>

                            <div class="form-group" id="container-end-date-exchange">
                                <div class="col-lg-12">
                                    <div class="col-sm-2">
                                    <label class="control-label">Tanggal Akhir</label>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class='input-group date ' id='end-date'>
                                            <input type="text" class="form-control no-special-char" field-name="Tanggal Akhir" name="end_date" onkeydown="return false" value="<?php echo date("Y-m-d"); ?>"> 
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div> 
                                    </div>                                
                                </div>
                            </div>

                            <input type="hidden" id="repeat-status" value="0">
                            <div class="form-group">
                                <div class="col-lg-12">
                                    <div class="col-sm-2">
                                    <label for="inputPassword3" class="control-label">Pengulangan</label>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="radio" name="repeat-exchange" class="repeat-status" value="0"> 
                                        Ada Tanggal Akhir
                                        <input type="radio" name="repeat-exchange" class="repeat-status" value="1" > 
                                        Berlaku Seterusnya
                                    </div>
                                </div>
                            </div>

                            <div class="form-group" id="panel_store" >
                                <!-- <label for="discount_name" class="col-sm-2 control-label">&nbsp;</label> -->
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-xs-5">
                                            <label class="control-label">Dari Jam Kerja</label>
                                            <select class="form-control select2" data-width="100%" name="from_office_hour" id="from_office_hour">
                                                <option value="">Pilih Template Jam Kerja</option>
                                                <?php foreach($office_hours as $o): ?>
                                                <option value="<?php echo $o->id ?>"><?php echo $o->name ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <select class="multiselect form-control" size="15" name="employees_from[]" id="multiselect_from_1"
                                                multiple="multiple" 
                                                data-right="#multiselect_to_1" 
                                                data-right-all="#right_All_1" 
                                                data-right-selected="#right_Selected_1" 
                                                data-left-all="#left_All_1" 
                                                data-left-selected="#left_Selected_1">
                                            </select>
                                        </div>
                                        
                                        <div class="col-xs-2">
                                        <br><br><br>
                                            <button type="button" id="right_All_1" class="btn btn-block"><i class="glyphicon glyphicon-forward"></i></button>
                                            <button type="button" id="right_Selected_1" class="btn btn-block"><i class="glyphicon glyphicon-chevron-right"></i></button>
                                            <button type="button" id="left_Selected_1" class="btn btn-block"><i class="glyphicon glyphicon-chevron-left"></i></button>
                                            <button type="button" id="left_All_1" class="btn btn-block"><i class="glyphicon glyphicon-backward"></i></button>
                                        </div>
                                        
                                        <div class="col-xs-5">
                                            <label class="control-label">Ke Jam Kerja</label>
                                            <select class="form-control select2" data-width="100%" name="to_office_hour" id="to_office_hour">
                                                <option value="">Pilih Template Jam Kerja</option>
                                                <?php foreach($office_hours as $o): ?>
                                                <option value="<?php echo $o->id ?>"><?php echo $o->name ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <select name="employees_to[]" id="multiselect_to_1" class="form-control" size="15" multiple="multiple"></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <div class="col-sm-12" align="right">
                                    <button type="submit" name="btnAction" value="save" class="btn btn-primary"><?php echo $this->lang->line('ds_submit_save'); ?></button>  
                                    <a href="<?php echo base_url(SITE_ADMIN . '/hrd_schedule/'); ?>" class="btn btn-default"><?php echo $this->lang->line('ds_submit_cancel'); ?></a>
                                </div>
                            </div> 
                        </div>
                    <?php echo form_close(); ?>
                </div>

                <div class="tab-pane fade in active" id="group-tab">
                    <?php 
                        echo form_open(site_url($url_rolling), array('class' => 'form-horizontal form-ajax'));
                    ?>
                    <div class="panel-body">



                        <div class="form-group" >
                            <div class="col-lg-12">
                                <div class="col-lg-2">
                                <label for="inputPassword3" class=" control-label">Tanggal Mulai</label>
                                </div>
                                <div class="col-sm-3">
                                    <div class='input-group date ' id='start-date-rolling'>
                                        <input type="text" class="form-control no-special-char" field-name="Tanggal Mulai" name="start_date_rolling" onkeydown="return false" value="<?php echo date("Y-m-d"); ?>"> 
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div> 
                                </div>  
                            </div>
                        </div>                        

                        
   
                        <div class="form-group" id="conta-end-date">
                            <div class="col-lg-12">
                                <div class="col-lg-2">
                                <label for="inputPassword3" class="control-label">Tanggal Akhir</label>
                                </div>
                                <div class="col-sm-3">
                                    <div class='input-group date ' id='end-date-rolling'>
                                        <input type="text" class="form-control no-special-char" field-name="Tanggal Akhir" value="<?php echo date("Y-m-d"); ?>" name="end_date_rolling" onkeydown="return false"> 
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div> 
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-lg-12">
                                <input type="hidden" id="repeat-status" value="">
                          
                                    <div class="col-lg-2">
                                    <label for="inputPassword3" class="control-label">Pengulangan</label>
                                    </div>
                                    <div class="col-sm-5">
                                        <input type="radio" name="repeat-rolling" class="repeat-status" value="0" checked=""> 
                                        Ada Tanggal Akhir
                                        <input type="radio" name="repeat-rolling" class="repeat-status" value="1"> 
                                        Berlaku Seterusnya
                                    </div>
                             
                            </div>
                        </div>
                
                        <div class="form-group">
                            <div class="col-lg-12">
                                <table class="table table-bordered">
                                  <thead>
                                    <tr>
                                      <th>Shift</th>
                                      <th>Jam Kerja</th>
                                      <th>Ganti Jam Kerja</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php foreach($office_hours as $l): ?>
                                      <tr office_hour_id="<?php echo $l->id ?>">
                                        <td><?php echo $l->name ?></td>
                                        <td><?php echo date("H:i:s",strtotime($l->checkin_time))." s/d ".date("H:i:s",strtotime($l->checkout_time)) ?></td>
                                        <td>
                                          <select class="form-control select2 office_hour_target_id" value_before="<?php echo $l->id ?>" data-width="100%" name="detail[<?php echo $l->id ?>]">
                                            <option value="<?php echo $l->id ?>"><?php echo $l->name." ( ".date("H:i:s",strtotime($l->checkin_time))." s/d ".date("H:i:s",strtotime($l->checkout_time))." )" ?></option>
                                            <?php foreach($office_hour_targets as $o): ?>
                                              <option value="<?php echo $o->id ?>"><?php echo $o->name." ( ".date("H:i:s",strtotime($o->checkin_time))." s/d ".date("H:i:s",strtotime($o->checkout_time))." )" ?></option>
                                            <?php endforeach; ?>
                                          </select>
                                        </td>
                                      </tr>
                                    <?php endforeach; ?>
                                  </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="form-group">
                            <div class="col-sm-12" align="right">
                                <button type="submit" name="btnAction" value="save" class="btn btn-primary"><?php echo $this->lang->line('ds_submit_save'); ?></button>  
                                <a href="<?php echo base_url(SITE_ADMIN . '/hrd_schedule/'); ?>" class="btn btn-default"><?php echo $this->lang->line('ds_submit_cancel'); ?></a>
                            </div>
                        </div> 
                    </div>
                    <?php echo form_close(); ?>
                </div>
            <!-- /.table-responsive -->
            </div>
        </div>			
        <!-- /.panel -->
    </div>
</div>
<!-- /.col-lg-12 -->