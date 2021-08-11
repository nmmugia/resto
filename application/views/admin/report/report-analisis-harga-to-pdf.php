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
    <?php if($from==-1){ ?>
    <div class="report_header">
    <div>
        <h2><label>Laporan Analisis Harga</label></h2>
    </div>
   
    </div>
     <?php } ?>

<div class="panel-body">

        <?php if($from==-1): ?>
             <table class="col-lg-6 form-table-cms" style="width:50%">
                <tbody>
                  <tr>
                    <td>Resto</td>
                    <td>: <?php echo isset($store->store_name) ? $store->store_name : "Semua Resto";?></td>
                  </tr>
                </tbody>
            </table>
            <br>
            <div class="clearfix"></div>

        <?php else: ?>
            <table class="table table-striped table-bordered table-hover dt-responsive" cellspacing="0" width="100%" id="table-price-analyst">
                <thead>
                <tr>
                  <th>Kategori</th>
                  <th>Menu</th>
                  <th>Harga Jual</th>
                  <th>Harga Pokok</th>
                  <th>Untung Kotor</th>
                  <th>Margin (%)</th>
                  <th>Markup (%)</th>
                </tr>
                </thead>

                <tbody>
                    <?php
                        
                    $counter=0;
                    foreach ($all_price_analyst as $key => $row) {
                      if($counter>=$from && $counter<$to){

                       echo '
                         <tr class="border_bottom">
                         <td>'.$row->ctgname.'</td>
                         <td>'.$row->menu_name.'</td>
                         <td  align="right">Rp. '.$row->menu_price.'</td>
                         <td  align="right">Rp. '.$row->menu_hpp.'</td>
                         <td  align="right">Rp. '.$row->gross.'</td>
                         <td  align="right">'.round($row->margin, 1).' %</td>
                         <td  align="right">'.round($row->markup, 1).' %</td>
                         </tr>';

                        }
                      $counter++;
                        
                     }
                    ?>

                </tbody>    
             
            </table>

           <?php endif; ?>
            <!-- /.table-responsive -->
        </div>
