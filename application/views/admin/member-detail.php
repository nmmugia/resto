<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

?>

<style type="text/css">
.ui-datepicker-trigger {
    margin: 4px;
}
</style>

    <div class="col-lg-12" style="padding: 0 !important">
 
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="discount_name" class="col-sm-2 control-label">Resto</label>


                                    <label for="discount_value" class="col-sm-10 control-label">
                                            <?php  
                                            echo $form_data->store_name;
                                            ?>
                                        </label>
                                    </div>


                                    <div class="form-group">
                                        <label for="discount_value" class="col-sm-2 control-label">Nama</label>
                                        <label for="discount_value" class="col-sm-10 control-label">
                                            <?php  
                                            echo $form_data->name;
                                            ?>
                                        </label>

                                       <!--  <div class="col-sm-10">
                                            <?php echo form_input($name); ?>
                                        </div> -->
                                    </div>
                                    <div class="form-group">
                                        <label for="discount_value" class="col-sm-2 control-label">ID Member</label>

                                        <label for="discount_value" class="col-sm-10 control-label">
                                            <?php  
                                            echo $form_data->member_id;
                                            ?>
                                        </label>
                                    </div>

                                    <div class="form-group">
                                        <label for="discount_value" class="col-sm-2 control-label">Alamat</label>

                                        <label for="discount_value" class="col-sm-10 control-label">
                                            <?php  
                                            echo $form_data->address;
                                            ?>
                                        </label>
                                    </div>
                               

                                    <div class="form-group">
                                        <label for="discount_value" class="col-sm-2 control-label">Negara</label>

                                       <label for="discount_value" class="col-sm-10 control-label">
                                            <?php  
                                            echo $form_data->country;
                                            ?>
                                        </label>



                                        <div class="form-group">
                                            <label for="discount_value" class="col-sm-2 control-label">Provinsi</label>

                                            <label for="discount_value" class="col-sm-10 control-label">
                                            <?php  
                                            echo $form_data->province;
                                            ?>
                                        </label>
                                        </div>



                                        <div class="form-group">
                                            <label for="discount_value" class="col-sm-2 control-label">Kota</label>

                                            <label for="discount_value" class="col-sm-10 control-label">
                                            <?php  
                                            echo $form_data->city;
                                            ?>
                                        </label>
                                        </div>


                                


                                    <div class="form-group">
                                        <label for="discount_value" class="col-sm-2 control-label">Kode POS</label>

                                        <label for="discount_value" class="col-sm-10 control-label">
                                            <?php  
                                            echo $form_data->postal_code;
                                            ?>
                                        </label>
                                    </div>


                                    <div class="form-group">
                                        <label for="discount_value" class="col-sm-2 control-label">Telepon</label>

                                        <label for="discount_value" class="col-sm-10 control-label">
                                            <?php  
                                            echo $form_data->land_phone;
                                            ?>
                                        </label>
                                    </div>


                                    <div class="form-group">
                                        <label for="discount_value" class="col-sm-2 control-label">Handphone</label>

                                        <label for="discount_value" class="col-sm-10 control-label">
                                            <?php  
                                            echo $form_data->mobile_phone;
                                            ?>
                                        </label>
                                    </div>


                                    

                                     <div class="form-group">
                                        <label for="discount_value" class="col-sm-2 control-label">Kategori Member</label>

                                        <label for="discount_value" class="col-sm-10 control-label">
                                            <?php  
                                            echo $form_data->member_category;
                                            ?>
                                        </label>
                                    </div>


                                    <div class="form-group">
                                        <label for="discount_value" class="col-sm-2 control-label">Benefit</label>

                                        <label for="discount_value" class="col-sm-10 control-label">
                                            <?php  
                                            echo 'Diskon '.$form_data->discount.'%';
                                            ?>
                                        </label>
                                    </div>

                                     <div class="form-group">
                                        <label for="discount_value" class="col-sm-2 control-label">Tanggal Bergabung</label>

                                       <label for="discount_value" class="col-sm-10 control-label">
                                            <?php  
                                            echo date_format(date_create($form_data->join_date),'Y-m-d');
                                            ?>
                                        </label>
                                    </div>

                                    <div class="form-group">
                                        <label for="discount_value" class="col-sm-2 control-label">Transaksi Terakhir</label>

                                       <label for="discount_value" class="col-sm-10 control-label">
                                            <?php 
                                            if(!empty($form_data->last_transaction_date)){
                                                echo date_format(date_create($form_data->last_transaction_date),'Y-m-d');

                                            }
                                            else{
                                                echo "-";
                                            }
                                            ?>
                                        </label>
                                    </div>

                                    <div class="form-group">
                                        <label for="discount_value" class="col-sm-2 control-label">Total Point</label>

                                        <label for="discount_value" class="col-sm-10 control-label">
                                            <?php  
                                            echo $form_data->point;
                                            ?>
                                        </label>
                                    </div>


                                     <div class="form-group">
                                        <label for="discount_value" class="col-sm-2 control-label">Total Transaksi</label>

                                        <label for="discount_value" class="col-sm-10 control-label">
                                            <?php  
                                            echo $form_data->total_spending;
                                            ?>
                                        </label>
                                    </div>
                                <table class="table table-striped table-bordered table-hover dt-responsive" cellspacing="0" width="100%" id="table-member-transaction">
                                  <thead>
                                    <tr>
                                      <th>Tanggal</th>
                                      <th>Receipt</th>
                                      <th>Pembelian</th>
                                      <th>Grand Total</th>
                                      <th>Jumlah Item</th>
                                      <th>Jumlah Pelanggan</th>
                                      <th>Order ID</th>
                                      <th>Aksi</th>
                                    </tr>
                                  </thead>
                                </table>
                                <input type="hidden" id="dataProcessUrl" value="<?php echo base_url(SITE_ADMIN."/member/get_transaction_data/".$form_data->member_id); ?>"/>
                                <div class="form-group">
                                    <div class="text-center">
                                        
                                        <a href="<?php echo base_url(SITE_ADMIN . '/member'); ?>"
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
<script data-main="<?php echo base_url('assets/js/main-member'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>