<div class="col-lg-12">
	<div class="panel panel-default">
		<div class="panel-heading">
			<table class="col-lg-8 form-table-cms">
				<tbody>
					<tr>
						<td><label>Open Oleh</label></td>
						<td><?php echo $open_close->open_by_name ?></td>
					</tr>
					<tr>
						<td><label>Close Oleh</label></td>
						<td><?php echo $open_close->close_by_name ?></td>
					</tr>
					<tr>
						<td><label>Waktu Mulai</label></td>
						<td><?php echo date("d/m/Y H:i:s",strtotime($open_close->open_at)) ?></td>
					</tr>
					<tr>
						<td><label>Waktu Akhir</label></td>
						<td><?php echo date("d/m/Y H:i:s",strtotime($open_close->close_at)) ?></td>
					</tr>
					<tr>
						<td><label>Saldo Awal</label></td>
						<td><?php echo "Rp. ".number_format($open_close->begin_balance,0) ?></td>
					</tr>
					<?php if($balance_cash_history->amount>0): ?>
					<tr>
						<td><label>Penambah Saldo</label></td>
						<td><?php echo "Rp. ".number_format($balance_cash_history->amount,0) ?></td>
					</tr>
					<tr>
						<td><label>Saldo Akhir</label></td>
						<td><?php echo "Rp. ".number_format($open_close->begin_balance+$balance_cash_history->amount,0) ?></td>
					</tr>
					<?php endif; ?>
					<tr>
						<td><label>Total Transaksi</label></td>
						<td><?php echo $open_close->total_transaction ?></td>
					</tr>
					<tr>
						<td><label>Total Cash</label></td>
						<td><?php echo "Rp. ".number_format($open_close->total_cash,0) ?></td>
					</tr>
					<?php if($setting['cash_on_hand']==1): ?>
					<tr>
						<td><label>Cash On Hand</label></td>
						<td><?php echo "Rp. ".number_format($open_close->cash_on_hand,0) ?></td>
					</tr>
					<?php endif; ?>
				</tbody>
			</table>
			<div class="clearfix"></div>
		</div>
		<div class="panel-body">
			<table class="table table-striped table-bordered dt-responsive">
				<thead>
					<tr>
						<th>Nama</th>
						<th>Jumlah</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						foreach($open_close_detail as $d){
							echo "<tr>";
							echo "<td style='padding-left:25px;font-weight:".($d->is_enhancher==0 ? "bold" : "normal")."'>".$d->name."</td>";
							echo "<td align='right'>".number_format($d->value,0)."</td>";
							echo "</tr>";
						}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>