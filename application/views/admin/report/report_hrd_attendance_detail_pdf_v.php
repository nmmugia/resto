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
        <h2><label>Laporan Detail Absensi</label></h2>
    </div>
   
    </div>
     <?php } ?>

<div class="panel-body">

        <?php if($from==-1): ?>

            <div class="clearfix"></div>

        <?php else: ?>
            <table class="table table-striped table-bordered table-hover dt-responsive" cellspacing="0" width="100%" id="table-attendance-detail">
                <thead>
                <tr>
                    <th >Nama Pegawai</th>
                    <th >Tanggal</th>
                    <th >Jam Masuk</th>
                    <th >Jam Keluar</th>
                    <th >Absen Masuk</th>
                    <th >Absen keluar</th>
                    <th >Absen Overtime</th>
                    <th >Status</th>
                    <th >Note</th>
                </tr>
                </thead>

                <tbody>
                    <?php
                        
                    $counter=0;
                    foreach ($all_attendance_detail as $key => $row) {
                      if($counter>=$from && $counter<$to){

                       echo '
                         <tr class="border_bottom">
                         <td>'.$row->uname.'</td>
                         <td>'.convert_date_with_time($row->cdate).'</td>
                         <td>'.$row->masuk.'</td>
                         <td>'.$row->keluar.'</td>
                         <td>'.$row->amasuk.'</td>
                         <td>'.$row->akeluar.'</td>
                         <td>'.$row->abkeluar.'</td>
                         <td>'.$row->estatus.'</td>
                         <td>'.$row->note.'</td>

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
