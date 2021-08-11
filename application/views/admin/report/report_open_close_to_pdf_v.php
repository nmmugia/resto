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
        <h2><label>Laporan Open Close</label></h2>
    </div>
   
    </div>
    <?php } ?>

<div class="panel-body">

        <?php if($from==-1): ?>
             <table class="col-lg-6 form-table-cms" style="width:50%">
                <tbody>

                   <tr>
                    <td><label>Periode</label></td>
                    <td><label id="periode"><?php echo ($periode)? $periode : "-"; ?></label></td>

                </tr>
              
                </tbody>
            </table>
            <br>
            <div class="clearfix"></div>

            <?php else: ?>
            <table class="table table-striped table-bordered table-hover dt-responsive" cellspacing="0" width="100%" id="table-transaction">
                <thead>
                <tr>
                    <th>Open</th>
                    <th>Open Oleh</th>
                    <th>Close</th>
                    <th>Close Oleh</th>
                    <th>Total Transaksi</th>
                    <th>Total Cash</th>
                </tr>
                </thead>

                <tbody>
                    <?php
                        
                    $counter=0;

                    foreach ($all_open_close as $key => $row) {
                      if($counter>=$from && $counter<$to){

                       echo '
                         <tr class="border_bottom">
                         <td>'.convert_date_with_time($row->open_at).'</td>
                         <td>'.$row->open_by_user.'</td>
                         <td>'.convert_date_with_time($row->close_at).'</td>
                         <td>'.$row->close_by_user.'</td>
                         <td>'.$row->total_transaction.'</td>
                         <td>'.convert_rupiah($row->total_cash).'</td>

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
