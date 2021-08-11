<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
 

?>
<div class="col-lg-12">
    <div class="panel panel-default">
    <div class="panel-heading "><h2>Detail Stock</h2>
    </div>
        <div class="panel-body">
            <div class="col-lg-6">
                <form class="form-horizontal">
                    <div class="form-group">
                        <label for="inputVoucher1" class="  col-xs-4" style="text-align:left">Restoran</label>
                        <label class="  col-xs-1">  :</label>
                        <div class="  col-xs-4" style="text-align:left"> <?php echo $detail_stock[0]->store_name;?></div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword" class=" col-xs-4" style="text-align:left">Outlet</label>
                        <label class="  col-xs-1">  :</label>
                        <div class=" col-xs-4" style="text-align:left"><?php echo $detail_stock[0]->outlet_name?></div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword" class=" col-xs-4" style="text-align:left">Item</label>
                        <label class="  col-xs-1">  :</label>
                        <div class=" col-xs-4" style="text-align:left"> <?php echo $detail_stock[0]->name ?></div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword" class="  col-xs-4" style="text-align:left">Banyak Stock</label>
                        <label class=" col-xs-1">  :</label>
                         <div class="  col-xs-4" style="text-align:left"> <?php echo $detail_stock[0]->jumlah_stok;?> <?php echo $detail_stock[0]->code;?></div>
                    </div>
                </form>  
            </div>
        </div>
    </div>
</div>
<div class="col-lg-12">
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
       
        <!-- /.panel-heading -->
        <div class="panel-body">

            <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-stock-detail">
                <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jumlah</th>
                     <th>Satuan</th> 
                      <th>Harga</th> 
                </tr>
                </thead>
            </table>
            <input type="hidden" id="dataProcessUrl"
                   value="<?php echo base_url(SITE_ADMIN . '/stock/get_detail_stock/'.$outlet_id.'/'.$inventory_id."/".$uom_id); ?>"/>

            <!-- /.table-responsive -->
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->

<script data-main="<?php echo base_url('assets/js/main-admin'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>