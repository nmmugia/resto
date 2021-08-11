<form method="post" action="">
	<div class="panel-body">
		<table class="table table-bordered" style="font-size: 11px;">
			<thead>
				<tr>
					<th>Outlet</th>
					<th>Inventory</th>
					<th>Stok Awal</th>
					<th>Tanggal</th>
					<th>Penjualan</th>
					<th>Transfer</th>
					<th>Pembelian</th>
					<th>Opname</th>
					<th>Penerimaan</th>
					<th>Proses Inventory</th>
					<th>Spoiled</th>
					<th>Void</th>
					<th>Retur</th> 
					<th>Refund</th> 
					<th>Stok Akhir</th>
					<th>Stok</th>
				</tr>
			</thead>
			<tbody>
			<?php
				$inventories=array();
				foreach($results as $r){
					if(!isset($inventories[$r->outlet_id."_".$r->inventory_id."_".$r->uom_id])){
						$inventories[$r->outlet_id."_".$r->inventory_id."_".$r->uom_id]=array(
							"data" => $r,
							"detail"=>array()
						);
					}
					array_push($inventories[$r->outlet_id."_".$r->inventory_id."_".$r->uom_id]['detail'],$r);
				}
				foreach($inventories as $i){
					$check=0;
					$counter=0;
					// foreach($i['detail'] as $d){
					// 	// if($d->range_status==1){
					// 	// 	$check=1;
					// 	// 	$counter++;
					// 	// }
					// }
					$last_stock=$i['data']->beginning_stock;
					echo '<tr style="background:lightgray;">';
					echo '<td>'.$i['data']->outlet_name.'</td>';
					echo '<td>'.$i['data']->name.'</td>';
					echo '<td align="right">'.number_format($i['data']->beginning_stock,2).' '.$i['data']->code.'</td>';
					 echo '<td >'.date('Y-m-d').'</td>';
					$counter=1;
					foreach($history_status as $h){
						echo '<td align="right">'.number_format($i['data']->{"total_".$counter},2).'</td>';
						$counter++;
					}
					echo '<td align="right">'.number_format($i['data']->last_stock,2).'</td>';
					if($check==0){						
						echo '<td width="100px">
							<input type="hidden" name="detail[outlet_id][]" value="'.$i['data']->outlet_id.'">
							<input type="hidden" name="detail[inventory_id][]" value="'.$i['data']->inventory_id.'">
							<input type="hidden" name="detail[uom_id][]" value="'.$i['data']->uom_id.'">
							<input type="hidden" name="detail[last_stock][]" value="'.$i['data']->last_stock.'">
							<input type="text" name="detail[qty][]" class="form-control">
						</td>';
					}
					echo '</tr>';
					// $counter2=0;
					// foreach($i['detail'] as $d){
					// 	if($d->range_status==1){
					// 		$counter2++;
					// 		echo '<tr>';
					// 		echo '<td></td>';
					// 		echo '<td></td>';
					// 		echo '<td></td>';
					// 		echo '<td>'.date("d/m/Y",strtotime($d->date)).'</td>';
					// 		foreach($history_status as $h){
					// 			if($h->id==$d->status){
					// 				$last_stock+=$d->total_quantity;
					// 				echo '<td align="right">'.number_format($d->total_quantity,2).' '.$d->code.'</td>';
					// 			}else{
					// 				echo '<td>1</td>';
					// 			}
					// 		}
					// 		echo '<td align="right">'.number_format($last_stock,2).'</td>';
					// 		if($counter==$counter2){						
					// 			echo '<td>
					// 				<input type="hidden" name="detail[outlet_id][]" value="'.$i['data']->outlet_id.'">
					// 				<input type="hidden" name="detail[inventory_id][]" value="'.$i['data']->inventory_id.'">
					// 				<input type="hidden" name="detail[uom_id][]" value="'.$i['data']->uom_id.'">
					// 				<input type="text" name="detail[qty][]" class="form-control">
					// 			</td>';
					// 		}else{
					// 			echo '<td></td>';
					// 		}
					// 		echo '</tr>';
					// 	}          
					// }
				}
			?>
			</tbody>
		</table>
		<div class="form-group">
			<div class="text-center">  
				<button type="submit" name="btnAction" value="save" class="btn btn-primary">Simpan</button>
			</div>
		</div>
	</div>
</form>