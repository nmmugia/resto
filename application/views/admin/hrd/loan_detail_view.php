<?php if (! defined('BASEPATH')) exit('No direct script access allowed'); 
?>
<div class="col-lg-6"> 
    <div class="panel panel-default">
        <div class="panel-heading">
             Informasi
        </div>
        <div class="panel-body">
            <div class="form-group col-lg-12">
                <label for="name" class="col-sm-6 control-label"> NIP</label>

                <div class="col-sm-6">
                  <?php echo $data_users->nip;?>
                </div>
            </div>
            <div class="form-group col-lg-12">
                <label for="name" class="col-sm-6 control-label"> Nama</label>

                <div class="col-sm-6">
                  <?php echo $data_users->name;?>
                </div>
            </div>
            <div class="form-group col-lg-12">
                <label for="name" class="col-sm-6 control-label"> Tanggal Pinjam</label>

                 <div class="col-sm-6">
                  <?php echo $data_loan->loan_date;?>
                </div>
            </div>

            <div class="form-group col-lg-12">
                <label for="name" class="col-sm-6 control-label"> Total Pinjaman</label>

                 <div class="col-sm-6">

                  <?php echo "Rp. ".number_format($data_loan->loan_total,0,"",".");?>
                </div>
            </div>

            <div class="form-group col-lg-12">
                <label for="name" class="col-sm-6 control-label"> Jumlah Cicilan Perbulan</label>

                <div class="col-sm-6">
                  <?php  
                  if(empty($data_loan->instalment)) $data_loan->instalment = 1;
                  $instalment_total = $data_loan->loan_total/$data_loan->instalment; 
                   echo "Rp. ".number_format(round($instalment_total,2),0,"",".")
                 ?>
                </div>
            </div>

             <div class="form-group col-lg-12">
                <label for="name" class="col-sm-6 control-label">Sisa Hutang</label>

                <div class="col-sm-6">
                  <?php  
                   
                   echo "Rp. ".number_format(round($sisa_hutang->outstanding_total,2),0,"",".")
                 ?>
                </div>
            </div>

             <div class="form-group col-lg-12">
                <label for="name" class="col-sm-6 control-label">Status Hutang</label>

                <div class="col-sm-6">
                  <?php  
                   if($sisa_hutang->repayment_total == $data_loan->loan_total){
                      echo "Lunas";
                   }else{
                    echo "Belum Lunas";
                   }
                 ?>
                </div>
            </div>
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel --> 
</div> 

<div class="col-lg-6">
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
    <div class="panel panel-default">
          <?php 
          if($sisa_hutang->outstanding_total > 0 || $sisa_hutang->outstanding_total == null){
          ?>
           <a href="#" class="btn btn-primary pull-right"  
                    data-toggle="modal" data-target="#repayment-modal"  
                    >  
            Tambah Pembayaran</a>

          <?php } ?>
        <div class="panel-heading">
             Data Pembayaran  

        </div>
        <div class="panel-body">
            <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-repayments">
                <thead>
                <tr> 
                    <th  >Tanggal</th>
                    <th  >Pembayaran</th> 
                    <th >Metode Pembayaran</th> 
                    <th >Aksi</th> 
                </tr>
                </thead>
            </table>
            <input type="hidden" id="dataProcessUrlRepayments" value="<?php echo $data_url;?>"/>
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel --> 
</div> 

    <div class="modal fade" id="repayment-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="exampleModalLabel">Tambah Pembayaran </h4>
          </div>
          <div class="modal-body"> 
            <div class="row">
                <div class="form-group col-lg-12">
                    <label for="recipient-name" class="col-sm-4 control-label">Total</label>
                   
                    <div class="col-sm-7">
                      <div class="input-group">
                       <div class="input-group-addon">Rp. </div>
                        <input type="text" id="repayment_total" class="form-control requiredTextField qty-input">
                          <input type="hidden" id="loan_id" value="<?php echo $data_loan->id;?>">
                    </div>
                      
                    </div>
                </div> 
                <div class="form-group col-lg-12">
                    <label for="floor_name" class="col-sm-4 control-label">Tanggal Bayar</label> 
                    <div class="col-sm-7">  
                        <div class='input-group date ' id='repayment-date'>
                          <input type="text" class="form-control no-special-char requiredTextField" id="repayment_date" onkeydown="return false"  > 
                          <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                        </div> 
                    </div>
                </div>  
                </div>
           
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary"  id="save-repayment" data-action='save'>Simpan</button>
          </div>
        </div>
      </div>
    </div>
</div> 
<input type="hidden" id="admin_url" value="<?php echo base_url(SITE_ADMIN) ?>"/>
<input type="hidden" id="root_base_url" value="<?php echo base_url(); ?>"/> 
<!-- /.col-lg-12 -->