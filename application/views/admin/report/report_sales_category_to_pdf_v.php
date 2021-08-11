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
        <h2><label>Laporan Penjualan Per Kategori</label></h2>
    </div>
   
    </div>

<div class="panel-body">

             <table class="col-lg-6 form-table-cms" style="width:50%">
                <tbody>

                   <tr>
                    <td><label>Periode</label></td>
                    <td><label id="periode"><?php echo $periode; ?></label></td>

                </tr>


				<tr>
					<td><label>Total Penjualan</label></td>
					<td><label id="total_price"><?php echo convert_rupiah($total_price); ?></label></td>

				</tr>
              
                </tbody>
            </table>

            <div class="clearfix"></div>

            <table class="table table-striped table-bordered table-hover dt-responsive" cellspacing="0" width="100%" id="table-transaction">
                <thead>
                <tr>
                    <!-- <th>Tanggal</th> -->
                    <th><?php echo $this->lang->line('outlet_title');?></th>
                    <th>Kategori</th>
                    <th>Banyak</th>
                    <th>Grand Total</th>
                </tr>
                </thead>
                <tbody>
                    <?php
                        
                    foreach ($data as $key => $row) {
                        // <td>'.convert_date_with_time($row->created_at).'</td>
                       echo '
                         <tr class="border_bottom">
                         
                         <td>'.$row->outlet_name.'</td>
                         <td>'.$row->category_name.'</td>
                         <td>'.$row->total_quantity.'</td>
                         <td>'.convert_rupiah($row->total_price).'</td>

                         </tr>';

                        
                     }
                    ?>

                </tbody>    
             
            </table>

            <!-- /.table-responsive -->
        </div>
