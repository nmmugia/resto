<style>
    table th, table td {
        word-wrap: break-word;
        max-width: 50px;
        font-size: 10px;
    }

    th {
        border: 1pt solid black;
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
        border: 1pt solid black;
    }

    table {
        border-collapse: collapse;
    }

    .border td, .border th {
        border: solid 1px #000;
        padding-left: 5px;
        padding-right: 5px;
    }

    i {
        display: none;
    }

    .text-center {
        text-align: center;
    }

    h4, h5 {
        margin-top: 3px;
        margin-bottom: 3px;
    }
</style>
<div class="panel-body">
    <?php if($from==-1): ?>
        <div class="text-center" style="margin-bottom:15px;">
            <h4><label>Laporan Reprint Billing</label></h4>
            <h5><?php echo date("d F Y", strtotime($params['start_date'])) . " s/d " . date("d F Y", strtotime($params['end_date'])); ?></h5>
        </div>
   <?php else: ?>

    <table class="table table-striped table-bordered table-hover dt-responsive" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th>Tanggal</th>
            <th>Meja</th>
            <th>Customer</th>
            <th>Receipt</th>
            <th>Pembelian</th>
            <th>Grand Total</th>
            <th>Jumlah Pelanggan</th>
            <th>Order ID</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $counter = 0;
        foreach ($lists as $key => $row) {
            if ($counter >=$from && $counter < $to) {
                $order_type = ($row->order_type == 0) ? "Dine in" : "Takeaway";
                echo '
                     <tr class="border_bottom">

                     <td>' . convert_date_with_time($row->payment_date) . '</td>
                     <td>' . $row->table_name . '</td>
                     <td>' . $row->customer_name . '</td>
                     <td>' . $row->receipt_number . '</td>
                     <td>' . $order_type . '</td>
                     <td>' . convert_rupiah($row->total_price) . '</td>
                     <td>' . $row->customer_count . '</td>
                     <td>' . $row->order_id . '</td>

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
