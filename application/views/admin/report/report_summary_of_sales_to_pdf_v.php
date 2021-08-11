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
        <h2><label>SUMMARY OF SALES</label></h2>
    </div>
   
    </div>

<div class="panel-body">

             <table class="col-lg-6 form-table-cms" style="width:50%">
                <tbody>
                    <tr>
                        <td><label>Periode </label></td>
                        <td><label id="periode"><?php echo $periode; ?></label></td>
                    </tr>   
                    <tr>
                        <td>Cabang </td>
                        <td>: <?php echo isset($store->store_name) ? $store->store_name : "-";?></td>
                    </tr>                 
                </tbody>
            </table>

            <div class="clearfix"></div>

            <table class="table table-striped table-bordered table-hover dt-responsive" cellspacing="0" width="100%" id="table-transaction">
                <thead>
                <tr>
                    <th>No. Bill</th>
                    <th width="15%">Qty</th>
                    <th width="15%">Gross</th>
                    <th width="15%">Net</th>
                    <th width="15%">tax</th>
                    <th width="15%">Net + Tax</th>
                    <th width="15%">Cost</th>
                </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($data as $key => $row) {
                       echo '
                        <tr class="border_bottom">
                            <td>'.$row->no_bill.'</td>
                            <td align="right">'.$row->qty.'</td>
                            <td align="right">'.$row->gross.'</td>
                            <td align="right">'.$row->nett.'</td>
                            <td align="right">'.$row->tax.'</td>                      
                            <td align="right">'.$row->net_tax.'</td>               
                            <td align="right">'.$row->cost.'</td> 
                        </tr>';                        
                     }
                    ?>

                    <!-- SUMMARY ROW -->
                    <tr>
                        <th align="right">GRAND TOTAL</th>
                        <th align="right"><?php echo ($total_qty); ?></th>
                        <th width="12%" align="right"><?php echo convert_rupiah($total_gross); ?></th>
                        <th width="12%" align="right"><?php echo convert_rupiah($total_nett); ?></th>
                        <th width="12%" align="right"><?php echo convert_rupiah($total_tax); ?></th>
                        <th width="12%" align="right"><?php echo convert_rupiah($total_net_tax); ?></th>
                        <th width="12%" align="right"><?php echo convert_rupiah($total_cost); ?></th>
                    </tr>
                    <script type="text/javascript">
                    </script>
                </tbody>    
             
            </table>
            <!-- /.table-responsive -->

            
        </div>
