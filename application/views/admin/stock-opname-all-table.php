<?php foreach($detail_stock as $l): ?>
  <tr>
    <td><?php echo $l->outlet_name; ?></td>
    <td><?php echo $l->name; ?></td>
    <td><?php echo (int)$l->stock_system." ".$l->code; ?></td>
    <td>
      <input type="hidden" name="detail[outlet_id][]" value="<?php echo $l->outlet_id ?>">
      <input type="hidden" name="detail[stock_system][]" value="<?php echo $l->stock_system ?>" class="stock_system">
      <input type="hidden" name="detail[inventory_id][]" value="<?php echo $l->id ?>">
      <input type="hidden" name="detail[uom_id][]" value="<?php echo $l->uom_id ?>">
      <input type="text" name="detail[qty][]" class="form-control col-sm-5 only_number qty">
      <input type="text" name="detail[price][]" class="form-control col-sm-5 only_number price" placeholder="Harga HPP" style="display:none;">
    </td>
  </tr>
<?php endforeach; ?>