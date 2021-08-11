    <style>
        table th, table td {
            word-wrap: break-word;
            max-width: 50px;
        }
        
      th {
        border : 1pt solid black;
            /*background-color: #3CB371;*/
            /*color: #fff;*/
        }
        
        table {
            width: 100%;
        }

        th {
            height: 50px;
        }

        tr.border_bottom td {
          border:1pt solid black;
      }
        
        table {
            border-collapse: collapse;
        }
        
        .border td, .border th{
            border: solid 1px #000;
            padding-left: 5px;
            padding-right: 5px;
        }
    </style>
    <div class="report_header">
    <div>
        <h2><label>Laporan Penerimaan Barang</label></h2>
    </div>
   
    </div>
    <div class="col-lg-12">
  <div class="panel panel-default"> 
        <div class="panel-body">
        <form id="formFilter" class="form-horizontal" method="POST">
            <table class="table table-striped table-bordered table-hover dt-responsive" cellspacing="0" width="100%" id="table-report-receive">
                
               <tbody>
               <?php if($detail['start_date']){?>
                <tr>
                    <td>Tanggal Awal<td>
                    <td><?php   echo (!empty($detail))? $detail['start_date']:"";?><td>
                </tr>
                <?php }?>
                 <?php if($detail['end_date']){?>
                 <tr>
                    <td>Tanggal Akhir<td>
                    <td><?php echo (!empty($detail))? $detail['end_date']:"";?><td>
                </tr>
                 <?php }?>
                 <tr>
                    <td>Supplier<td>
                    <td> <?php echo (!empty($detail))? $detail['supplier_name']:"";?><td>
                </tr>
                 <tr>
                    <td>Bahan<td>
                    <td> <?php echo (!empty($detail))? $detail['inventory_name']:"";?><td>
                </tr>
               </tbody>
            </table> 
            </form>
        </div>
    </div>
</div>
<br>
<div class="col-lg-12">
  <div class="panel panel-default"> 

        <div class="panel-body"> 

            <div class="clearfix"></div>

            <table class="table table-striped table-bordered table-hover dt-responsive" cellspacing="0" width="100%" id="table-report-receive">
                <thead>
                <tr>
                    <th>Tanggal Kedatangan</th>
                    <th>No Pembayaran</th>
                    <th>Supplier</th>
                    <th>Nama Bahan</th>
                    <th>Jumlah Item</th>
                    <th>Harga Per Item</th>
                    <th>Total </th>
                </tr>
                </thead>
               <tbody>
                    <?php
                        
                    foreach ($all_history as $key => $row) {

                       echo '
                         <tr class="border_bottom">
                         <td>'.convert_date_with_time($row->incoming_date).'</td>
                         <td>'.$row->payment_no.'</td>
                         <td>'.$row->supplier_name.'</td>
                         <td>'.$row->inventory_name.'</td>
                         <td>'.$row->received_quantity.'</td>
                         <td>'.convert_rupiah($row->price).'</td>
                         <td>'.convert_rupiah($row->total_per_item).'</td> 
                         </tr>';

                        
                     }
                    ?>

                </tbody>    
            </table> 
        </div>
     </div>
     </div>
