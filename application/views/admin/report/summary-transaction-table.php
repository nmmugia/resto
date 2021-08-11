<table class="col-lg-6 form-table-cms">
	<tbody>		
		<tr>
			<td><label>Periode</label></td>
			<td><label id="periode"><?php echo date("d/m/Y H:i:s",strtotime($params['start_date']))." s/d ".date("d/m/Y H:i:s",strtotime($params['end_date'])) ?></label></td>
		</tr>
	</tbody>
</table>
<div class="clearfix" />
<table class="col-lg-6 form-table-cms">
	<tbody>
		<tr>
            <td><label>Total Penjualan</label></td>
            <td><label id="total_sell"><?php echo number_format(@$summary->total_sell,0) ?></label></td>
        </tr>
        <tr>
            <td><label>Total Tax</label></td>
            <td><label id="total_tax"><?php echo number_format(@$summary->total_tax,0) ?></label></td>
        </tr>
        <tr>
            <td><label>Total Pembulatan</label></td>
            <td><label id="total pembulatan"><?php echo number_format(@$summary->total_pembulatan,0) ?></label></td>
        </tr>
        <tr>
            <td><label>Total Ongkos Kirim</label></td>
            <td><label id="total_ongkir"><?php echo number_format(@$summary->total_ongkir,0) ?></label></td>
        </tr>
        <tr>
            <td><label>DP IN</label></td>
            <td><label id="total_dp_in"><?php echo number_format(@$summary->total_dp_in,0) ?></label></td>
        </tr>
        <tr>
            <td><label>Total Penambahan Modal</label></td>
            <td><label id="total_balace_cash"><?php echo number_format(@$summary->total_balace_cash,0) ?></label></td>
        </tr>
            <td><label></label></td>
            <td><label></label></td>
        </tr>
        <?php if ($setting['delivery_company'] == 1) { ?>
        <tr>
            <td><label></label></td>
            <td><label></label></td>
        </tr>
        <?php } ?>
		<tr>
			<td><label style="margin-top: 5px;">----------------------------------------------------</label></td>
			<td><label style="margin-top: 5px;">--------------------------------------</label></td>
		</tr>
        <tr>
            <td><label>Total Pendapatan</label></td>
            <td><label id="total_cost">
                <?php
                    $total_pendapatan = @($summary->total_sell+$summary->total_tax+$summary->total_pembulatan+$summary->total_ongkir+$summary->total_dp_in+$summary->total_balace_cash);
                    echo number_format($total_pendapatan,0);
                ?>
            </label></td>
        </tr>
	</tbody>
</table>
<table class="col-lg-6 form-table-cms">
	<tbody>
        <tr>
                <td><label>Total Diskon</label></td>
                <td><label id="total_discount"><?php echo number_format(@$summary->total_discount,0) ?></label></td>
        </tr>
        <tr>
                <td><label>Total Bon Bill</label></td>
                <td><label id="total_bon_bill"><?php echo number_format(@$summary->total_bon_bill,0) ?></label></td>
        </tr>
        <tr>
                <td><label>Total Voucher</label></td>
                <td><label id="total_profit"><?php echo number_format(@$summary->total_voucher,0) ?></label></td>
        </tr>
        <tr>
                <td><label>Total Pending Bill</label></td>
                <td><label id="total_pending_bill"><?php echo number_format(@$summary->total_pending_bill,0) ?></label></td>
        </tr>
        <tr>
                <td><label>Total Compliment</label></td>
                <td><label id="total_compliment"><?php echo number_format(@$summary->total_compliment,0) ?></label></td>
        </tr>
        <tr>
                <td><label>DP OUT</label></td>
                <td><label id="total_dp_out"><?php echo number_format(@$summary->total_dp_out,0) ?></label></td>
        </tr>
        <tr>
                <td><label>Total Kas Kecil</label></td>
                <td><label id="total_petty_cash"><?php echo number_format(@$summary->total_petty_cash,0) ?></label></td>
        </tr>
        <?php if ($setting['delivery_company'] == 1) { ?>
        <tr>
                <td><label>Total Jasa Kurir</label></td>
                <td><label id=""><?php echo number_format(@$summary->total_courier_service,0) ?></label></td>
        </tr>
        <?php } ?>
        <tr>
			<td><label>----------------------------------------------------</label></td>
			<td><label>--------------------------------------</label></td>
        </tr>
        <tr>
            <td><label>Total Pengurangan</label></td>
            <td><label>
                <?php
                    $total_pengurangan = $summary->total_discount + $summary->total_bon_bill + $summary->total_voucher + $summary->total_pending_bill + $summary->total_compliment + $summary->total_dp_out + $summary->total_petty_cash + (($setting['delivery_company'] == 1) ? $summary->total_courier_service : 0);
                    echo number_format($total_pengurangan, 0);
                ?>
            </label></td>
        </tr>
	</tbody>
</table>
<div class="clearfix" />
<table class="col-lg-6 form-table-cms">
	<tbody>
        <tr>
            <td><label></label></td>
            <td><label></label></td>
        </tr>
        <tr>
            <td><label>Total Gross (Pendapatan-Pengurangan) </label></td>
            <td><label><?php echo number_format(($total_pendapatan - $total_pengurangan), 0) ?></label></td>
        </tr>
        <tr>
            <td><label>Total HPP</label></td>
            <td><label id="total_hpp"><?php echo number_format(@$summary->total_hpp,0) ?></label></td>
        </tr>
		<tr>
				<td><label>Total Nett</label></td>
				<td><label id="total_profit"><?php echo number_format((($total_pendapatan - $summary->total_hpp) - $total_pengurangan), 0) ?></label></td>
		</tr>
		 
		<tr>
				<td><label>Total Jasa Kurir</label></td>
				<td><label id="total_courier_service"><?php echo number_format(@$summary->total_courier_service,0) ?></label></td>
		</tr>
		<tr>
				<td><label>Total Transaksi</label></td>
				<td><label id="total_transaction"><?php echo number_format(@$summary->total_transaction,0) ?></label></td>
		</tr>

		<tr>
				<td><label>Total Customer</label></td>
				<td><label id="total_customer"><?php echo number_format(@$summary->total_customer_count,0) ?></label></td>
		</tr>

		<tr>
				<td><label>Total Penjualan Menu</label></td>
				<td><label id="total_quantity"><?php echo number_format(@$summary->total_quantity_order,0) ?></label></td>
		</tr>
        <?php if($summary->total_sharing > 0): ?>
        <tr>
                <td><label>Total Revenue Sharing</label></td>
                <td><label id="total_revenue_sharing"><?php echo number_format(@$summary->total_sharing, 0) ?></label></td>
        </tr>
        <?php endif; ?>
		 
	</tbody>
</table>

<table class="col-lg-6 form-table-cms">
	<tbody>
        <tr>
            <td><label></label></td>
            <td><label></label></td>
        </tr>		 
		<tr>
			<td><label>Jumlah Dinein</label><br><i>kasir | waktu | tipe bayar</i></td>
			<td><label id="dinein_count"><?php echo number_format(@$summary->total_count_dinein,0) ?></label></td>
		</tr>
		<tr>
			<td><label>Total Dinein</label><br><i>kasir | waktu | tipe bayar</i></td>
			<td><label id="dinein_total"><?php echo number_format(@$summary->total_dinein,0) ?></label></td>
		</tr>
		<tr>
			<td><label>Jumlah Takeaway</label><br><i>kasir | waktu | tipe bayar</i></td>
			<td><label id="takeaway_count"><?php echo number_format(@$summary->total_count_takeaway,0) ?></label></td>
		</tr>
		<tr>
			<td><label>Total Takeaway</label><br><i>kasir | waktu | tipe bayar</i></td>
			<td><label id="takeaway_total"><?php echo number_format(@$summary->total_takeaway,0) ?></label></td>
		</tr>
		<tr>
			<td><label>Jumlah Delivery</label><br><i>kasir | waktu | tipe bayar</i></td>
			<td><label id="delivery_count"><?php echo number_format(@$summary->total_count_delivery,0) ?></label></td>
		</tr>
		<tr>
			<td><label>Total Delivery</label><br><i>kasir | waktu | tipe bayar</i></td>
			<td><label id="delivery_total"><?php echo number_format(@$summary->total_delivery,0) ?></label></td>
		</tr> 
		 
	</tbody>
</table>
<div class="clearfix"></div>
<div class="loading-summary">
    <img src="<?php echo base_url()?>assets/img/loadgif.gif">
</div>