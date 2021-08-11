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
        <h2><label>Laporan Gaji</label></h2>
    </div>
   
    </div>
     <?php } ?>

<div class="panel-body">

        <?php if($from==-1): ?>
            
            <div class="clearfix"></div>

        <?php else: ?>
            <table class="table table-striped table-bordered table-hover dt-responsive" cellspacing="0" width="100%" id="table-attendance">
                <thead>
                <tr>
                    <th>Nama Pegawai</th>
                    <th>Jabatan</th>
                    <th>Tanggal</th>
                    <th>Tahun</th>
                    <th>Total Penerimaan</th> 
                    <th>Potongan</th>
                    <th>Total</th> 
                </tr>
                </thead>

                <tbody>
                    <?php
                        
                    $counter=0;
                    foreach ($data as $key => $row) {
                      if($counter>=$from && $counter<$to){

                       echo '
                         <tr class="border_bottom">
                         <td>'.$row->pname.'</td>
                         <td>'.$row->jobs_name.'</td>
                         <td>'.$row->months.'</td>
                         <td>'.$row->years.'</td>
                         <td>'.$row->total_penerimaan.'</td>
                         <td>'.$row->total_potongan.'</td>
                         <td>'.$row->total.'</td>

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
