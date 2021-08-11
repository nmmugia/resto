<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="col-lg-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <table class="col-lg-6 form-table-cms">
          <tbody>
            <tr>
              <td><label>Bagian</label></td>
              <td class="col-sm-8">
                <?php echo $data_transfer->outlet_name;?>
              </td>
            </tr>
            <tr>
              <td><label>Waktu</label></td>
              <td class="col-sm-8">
                <?php echo  date('H:i:s', strtotime($data_transfer->created_at));?>
              </td>
            </tr> 
          </tbody>
        </table>

        <table class="col-lg-6 form-table-cms">
          <tbody>
            <tr>
              <td><label>No</label></td>
              <td class="col-sm-8"> 
              </td>
            </tr>
            <tr>
              <td><label>Tanggal</label></td>
              <td class="col-sm-8">
                <?php echo  date('Y-m-d', strtotime($data_transfer->created_at));?>
              </td>
            </tr> 
          </tbody>
        </table>
        <div class="clearfix"></div>
      </div> 
      <div class="panel-body">
        <div class="text-center"> 
        </div>
        <table class="table table-bordered  " >
          <thead>
            <tr>
              <th width="20px">No</th>
              <th align="center">Item</th>
              <th align="center">Quantity</th>
              <th align="center">Satuan</th>
            </tr>
          </thead>
          <tbody>
          <?php
          $i = 1;
            foreach ($results as $key => $value) { ?>
             <tr>
              <td width="20px"><?php echo $i;?></td>
              <td><?php echo $value->name;?></td>
              <td><?php echo $value->quantity;?></td>
              <td><?php echo $value->code;?></td>
            </tr>
           <?php $i++; }
          ?>
          </tbody>
        </table>
      </div> 
      <div class="panel-footer">
        <center>
          <a href="<?php echo base_url(SITE_ADMIN . '/reports/transfer_menu');?>" class="btn btn-primary">Kembali</a>
        </center>
      </div>
    </div>
</div>