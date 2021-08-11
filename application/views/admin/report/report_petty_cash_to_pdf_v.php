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
        <h2><label>Laporan Kas Kecil</label></h2>
    </div>
   
    </div>
     <?php } ?>

<div class="panel-body">

        <?php if($from==-1): ?>
             <table class="col-lg-6 form-table-cms" style="width:50%">
                <tbody>
                  <tr>
                    <td><label>Periode</label></td>
                    <td>: <label id="periode"><?php echo ($periode)? $periode : "-"; ?></label></td>

                </tr>
              
                </tbody>
            </table>
            <br>
            <div class="clearfix"></div>

        <?php else: ?>
            <table class="table table-striped table-bordered table-hover dt-responsive" cellspacing="0" width="100%" id="table-transaction">
                <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Oleh</th>
                    <th>Jenis Pengeluaran</th>
                    <th>Deskripsi</th>
                    <th>Jumlah</th>
                </tr>
                </thead>

                <tbody>
                    <?php
                        
                    $counter=0;
                    foreach ($all_petty_cash as $key => $row) {
                      if($counter>=$from && $counter<$to){

                       echo '
                         <tr class="border_bottom">
                      
                         <td>'.convert_date_with_time($row->date).'</td>
                         <td>'.$row->name.'</td>
                         <td>'.$row->gename.'</td>
                         <td>'.$row->description.'</td>
                         <td>'.convert_rupiah($row->amount).'</td>

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
