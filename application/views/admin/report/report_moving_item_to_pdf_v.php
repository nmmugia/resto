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
        <h2><label>Laporan Moving Item</label></h2>
    </div>
   
    </div>
    <?php } ?>

<div class="panel-body">

        <?php if($from==-1): ?>
             <table class="col-lg-6 form-table-cms" style="width:50%">
                <tbody>

                   <tr>
                    <td><label>Periode</label></td>
                    <td><label id="periode"><?php echo $periode; ?></label></td>

                </tr> 
                </tbody>
            </table>

            <div class="clearfix"></div>

        <?php else: ?>
            <table class="table table-striped table-bordered table-hover dt-responsive" cellspacing="0" width="100%" id="table-transaction">
                <thead>
                 <tr>  
                   <!--  <th>No</th> -->
                    <th rowspan ="2">Nama Menu</th> 
                    <th colspan="2" align="center">HARGA</th> 
                    <th colspan="3" align="center">QTY</th>
                    
                     <th colspan="2" align="center">TOTAL</th> 
                </tr>
                 <tr>  
                     
                    <th align="center">Selling</th>
                    <th align="center">Costing</th>
                    <th align="center">Reguler</th>
                    <th align="center">MC</th>
                    <th align="center">Tot</th>
                    <th align="center"> Reguler</th>
                    <th align="center">MC</th> 
                </tr>
                </thead>
                <tbody>
                    <?php
                        
                    $counter=0;
                    // $no=1;
                    foreach ($all_sales_menu as $key => $row) {
                      if($counter>=$from && $counter<$to){
                        $qty_compliment = (!empty($row->qty_compliment))?$row->qty_compliment:0;
                       echo '
                         <tr class="border_bottom"> 
                         
                         <td  >'.$row->menu_name.'</td>
                         <td align="center">'.convert_rupiah($row->harga_menu).'</td>
                         <td align="center">'.convert_rupiah($row->harga_hpp).'</td>
                         <td align="center">'.$row->qty_reguler.'</td>
                         <td align="center">'.$qty_compliment.'</td>
                         <td align="center">'.$row->total_quantity.'</td>
                         <td align="center">'.convert_rupiah($row->total_reguler).'</td>
                         <td align="center">'.convert_rupiah($row->total_compliment).'</td> 

                         </tr>';

                      }
                      $counter++;
                        // $no++;
                     }
                    ?>

                </tbody>    
             
            </table>

        <?php endif; ?>
            <!-- /.table-responsive -->
        </div>
