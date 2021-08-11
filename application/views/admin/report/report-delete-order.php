<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

?>
<div class="col-lg-12">
  <div class="panel panel-default">
    <div class="panel-heading">
      <form id="formFilter" method="get">
            <div class="clear-export"></div>
                <table class="col-lg-8 form-table-cms">
                    <tbody>                   
                        <tr>
                            <td><label>Waktu Mulai</label></td>
                            <td class="col-sm-8">
                                <div class='input-group date ' id='start_date'>
                                  <?php echo form_input(array('name' => 'start_date',
                                   'id' => 'input_start_date',
                                   'type' => 'text',
                                   'class' => 'form-control date',
                                   'onkeydown'=>'return false',
                                   'value'=>date("Y-m-d")." 00:00",
                                   )); ?>
                                  <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                  </span>
                                </div> 
                            </td>
                        </tr>
                        <tr>
                            <td><label>Waktu Akhir</label></td>
                            <td class="col-sm-8">
                              <div class='input-group date ' id='end_date'>
                                 <?php echo form_input(array('name' => 'end_date',
                                   'id' => 'input_end_date',
                                   'type' => 'text',
                                   'class' => 'form-control date',
                                   'onkeydown'=>'return false',
                                   'value'=>date("Y-m-d")." 23:59",
                                   )); ?>
                                   <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-calendar">
                                      </span>
                                  </span>
                              </div>
                            </td>                    
                      </tr>
                      <tr>
                          <td colspan="4" align="right">
                              <button id="filter_submit" class="btn btn-default "><i class="fa fa-filter" aria-hidden="true"></i> Filter</button>
                          </td>
                      </tr>
                  </tbody>
              </table>
        </form>
      <div class="clearfix"></div>
    </div>
    <div class="panel-body" id="tabelDeleteOrder">
      <div class="clearfix"></div>
      <table class="table table-striped table-bordered table-hover dt-responsive" cellspacing="0" width="100%" id="dataTables-delete-order">
        <thead>
          <tr>
            <th>Waktu</th>
            <th>Oleh</th>
            <th style="width:60px">Aksi</th>
          </tr>
        </thead>
      </table>
      <input type="hidden" id="dataProcessUrl" value="<?php echo $data_url; ?>"/>
      <input type="hidden" id="report_type" value="delete_order"/>
    </div>
  </div>
</div>