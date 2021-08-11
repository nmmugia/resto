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
        <h2><label>Laporan Appraisal</label></h2>
    </div>
   
    </div>

<div class="panel-body">
      <table class="col-lg-6 form-table-cms" style="width:50%">
                <tbody> 
                <?php if(!empty($users)){?>
                   <tr>
                    <td><label>Nama Pegawai</label></td>
                    <td><label  > <?php echo $users->name;?></label></td>

                </tr>

                <?php }?>
                
                <?php if(!empty($start_date) && !empty($end_date)){?>
                    <tr>
                        <td><label>Tanggal Mulai</label></td>
                        <td><label  ><?php echo $start_date;?></label></td>

                    </tr>
                 <tr>
                        <td><label>Tanggal Akhir</label></td>
                        <td><label  ><?php echo $end_date;?></label></td>

                    </tr>
                    <?php }else{ ?>
                    <tr>
                    <td><label> </label></td>
                    <td><label  ></td>

                </tr>
                <?php }?>
                </tbody>
            </table>

            <div class="clearfix"></div>

            <table class="table table-striped table-bordered table-hover dt-responsive" cellspacing="0" width="100%" id="table-transaction">
                <thead>
                <tr>
                    <th>Nama Pegawai</th>
                    <th>Period</th>
                    <th>Tanggal Appraisal</th>
                    <th>Nilai</th>
                    <th>Max Nilai</th>  
                </tr>
                </thead>
                <tbody>
                    <?php
                        
                    foreach ($all_data as $key => $row) {

                       echo '
                         <tr class="border_bottom">
                         <td>'.$row->name.'</td>
                        
                         <td>'.$row->period.'</td>
                         <td>'.$row->created_at.'</td>
                         <td>'.$row->total_nilai.'</td>
                         <td>'.$row->max_nilai .'</td> 
                         </tr>';

                        
                     }
                    ?>

                </tbody>    
             
            </table>

            <!-- /.table-responsive -->
        </div>
