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
        <h2><label>Laporan Absensi</label></h2>
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
                    <th >Store</th>
                    <th >Nama Pegawai</th>
                    <th >Tanggal</th>
                    <th >Jumlah Jam</th>
                    <th >Terlambat (Menit)</th>
                    <th >Overtime (Menit)</th>
                    <th >Hadir</th>
                    <th >Cuti (Day)</th>
                    <th >Sakit</th>
                    <th >Ijin</th>
                </tr>
                </thead>

                <tbody>
                    <?php
                        
                    $counter=0;
                    foreach ($data as $key => $row) {
                      if($counter>=$from && $counter<$to){

                       echo '
                         <tr class="border_bottom">
                         <td>'.$row->sname.'</td>
                         <td>'.$row->uname.'</td>
                         <td>'.convert_date_with_time($row->curdate).'</td>
                         <td>'.$row->jam.'</td>
                         <td>'.$row->telat.'</td>
                         <td>'.$row->over.'</td>
                         <td>'.$row->hadir.'</td>
                         <td>'.$row->sakit.'</td>
                         <td>'.$row->cuti.'</td>
                         <td>'.$row->ijin.'</td>

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
