<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/17/2014
 * Time: 8:29 PM
 */

?>
<div class="col-lg-12">
   
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="col-lg-12">
                <form class="form-horizontal">
                    <div class="form-group">
                        <label for="inputVoucher1" class="  col-xs-2" style="text-align:left">Restoran</label>
                        <label class="  col-xs-1">  :</label>
                        <div class="  col-xs-4" style="text-align:left"> <?php if(!empty($data_store)) echo $data_store[0]->store_name;?></div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword" class=" col-xs-2" style="text-align:left">Outlet</label>
                        <label class="  col-xs-1">  :</label>
                        <div class=" col-xs-4" style="text-align:left"><?php if(!empty($data_outlet)) echo $data_outlet[0]->outlet_name;?></div>
                    </div>
                     
                </form>  
            </div>
            <?php /*
             <div class="col-lg-12  ">
                <form class="form-horizontal" style="float:right;padding-right:30px;">
                    <div class="form-group">
                        <a href="<?php echo base_url(SITE_ADMIN . '/stock/pembelian/'.$outlet_id); ?>" class="btn btn-primary">Tambah Stok</a>
                    </div>
                     
                </form>  
            </div>*/ ?>
            <div class="col-lg-12">

                <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-stock-list">
                    <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Jumlah</th>
                         <th>Satuan</th> 
                    </tr>
                    </thead>
                </table>
                <input type="hidden" id="dataProcessUrl" value="<?php echo base_url(SITE_ADMIN . '/stock/get_data_stock/'.$outlet_id); ?>"/>
            </div>
            <!-- /.table-responsive -->
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->

<script data-main="<?php echo base_url('assets/js/main-admin'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>