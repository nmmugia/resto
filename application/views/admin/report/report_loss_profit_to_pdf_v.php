	<style>
		table th, table td {
			word-wrap: break-word;
			max-width: 50px;
		}
		
	/*	th {
			background-color: #3CB371;
			color: #fff;
		}*/
		
		table {
			width: 100%;
		}

		th {
			height: 50px;
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
	<div class="report_header" style="display:<?php echo ($is_print)? 'block': 'none'; ?>" >
	<div>
		<h2><label>Laporan Untung/Rugi</label></h2>
	</div>
	<table >
		<tr>
			<td>Start Date</td>
			<td><?php echo isset($start_date) ? $start_date : "-";?></td>
		</tr>
		<tr>
			<td>End Date</td>
			<td><?php echo isset($end_date) ? $end_date : "-";?></td>
		</tr>
		
	</table>
	</div>

	<?php
	        $html = ' 
        <div class="panel-body">
        <table class="table" style="">
        <tbody>
        ';

        $total_price = 0;
        $total_cogs = 0;

         $html .= 
            '<tr>
                <td><h3><label>Penjualan</label></h3></td>
                <td><label id="periode"></label></td>

                <td><h3><label>Penggunaan Bahan</label></h3></td>
                <td><label id="periode"></label></td>

            </tr>';

        if(!empty($all_category)){
           

            foreach ($all_category as $key => $row) {
             $html .= '
             <tr >              
             <td class=""><label>'.$row->outlet_name.'</label></td>
             <td><label>'.convert_rupiah($row->total_price).'</label></td>

             <td><label>'.convert_rupiah($row->total_cogs).'</label></td>
             <td class=""><label></label></td>
             </tr>';
             $total_price += $row->total_price;
             $total_cogs += $row->total_cogs;

            }



            $html .= '
             <tr>              
             <td><h4><label>TOTAL</label></h4></td>
             <td><h4><label>'.convert_rupiah($total_price).'</label></h4></td>
                          
             <td><h4><label>'.convert_rupiah($total_cogs).'</label></h4></td>
             <td><label></label></td>
             </tr>

             ';

               $html .= 
            '<tr>
                <td><label></label></td>
                <td><label>	</label></td>
            </tr>';



        }

        $total_minus = 0;
        if(!empty($all_minus)){

            foreach ($all_minus as $key => $row) {
             $html .= '
             <tr>              
             <td><label>'.$row->info.'</label></td>
             <td><label>'.convert_rupiah($row->amount).'</label></td>
             </tr>';
             $total_minus += $row->amount;

            }

        $html .= '
             <tr>              
             <td><h4><label>TOTAL DISKON</label></h4></td>
             <td><h4><label>'.convert_rupiah($total_minus).'</label></h4></td>
             </tr>';
        }
        $html .= '
             <tr>              
             <td><h4><label>TOTAL BON BILL</label></h4></td>
             <td><h4><label>'.convert_rupiah($total_bon_bill).'</label></h4></td>
             </tr>';
		$html .= '
             <tr>              
             <td><h4><label>TOTAL PENDING BILL</label></h4></td>
             <td><h4><label>'.convert_rupiah($total_pending_bill).'</label></h4></td>
             </tr>';
        $html .= '
             <tr>              
             <td><h4><label>TOTAL VOUCHER BILL</label></h4></td>
             <td><h4><label>'.convert_rupiah($total_voucher_bill).'</label></h4></td>
             </tr>';
		$html .= '
             <tr>              
             <td><h4><label>TOTAL Compliment</label></h4></td>
             <td><h4><label>'.convert_rupiah($total_compliment).'</label></h4></td>
             </tr>';
        $html .= 
            '<tr>
            <td><label>DP OUT</label></td>
            <td><label>'.convert_rupiah($total_dp_out).'</label></td>
            </tr>'; 
        $html .= 
            '<tr>
            <td><label>KAS KECIL</label></td>
            <td><label>'.convert_rupiah($total_petty_cash).'</label></td>
            </tr>'; 
        $html .= 
            '<tr>
                <td><label></label></td>
                <td><label></label></td>
            </tr>';


        $nett = $total_price-$total_minus-$total_bon_bill-$total_pending_bill-$total_voucher_bill-$total_compliment-$total_dp_out-$total_petty_cash;
        $html .= '
             <tr>              
             <td><h4><label>NETT PENJUALAN</label></h4></td>
             <td><h4><label>'.convert_rupiah($nett).'</label></h4></td>
             </tr>
             ';

        $html .= 
            '<tr>
                <td><label></label></td>
                <td><label></label></td>
            </tr>';


        $total_plus = 0;
        if(!empty($all_plus)){

            foreach ($all_plus as $key => $row) {
             $html .= '
             <tr>              
             <td><label>'.$row->info.'</label></td>
             <td><label>'.convert_rupiah($row->amount).'</label></td>
             </tr>';
             $total_plus += $row->amount;

            }

            $html .= '
             <tr>              
             <td><h4><label>TOTAL TAX</label></h4></td>
             <td><h4><label>'.convert_rupiah($total_plus).'</label></h4></td>
             </tr>';
        }

        $html .= 
            '<tr>
            <td><label>DP IN</label></td>
            <td><label>'.convert_rupiah($total_dp_in).'</label></td>
            </tr>';         

        $html .= 
            '<tr>
            <td><label></label></td>
            <td><label></label></td>
            </tr>';

        $gross = $nett+$total_plus+$total_dp_in;
        $html .= '
             <tr>              
             <td><h4><label>GROSS PENJUALAN</label></h4></td>
             <td><h4><label>'.convert_rupiah($gross).'</label></h4></td>
             </tr>';      

        $profit_loss = $gross-$total_cogs;
        $html .= '
             <tr>              
             <td><h4><label>UNTUNG/RUGI</label></h4></td>
             <td><h4><label>'.convert_rupiah($profit_loss).'</label></h4></td>
             </tr>';      



        $html .= ' 
        </tbody>
        </table>
        </div>
        ';
        echo $html;
	?>
	
