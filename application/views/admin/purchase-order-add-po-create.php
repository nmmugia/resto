<tr>
  <td>
    <select class="form-control inventory_id select2" name="detail[id][]">
      <option value="">Pilih Inventori</option>
      <?php foreach($inventories as $i): ?>
      <option value="<?php echo $i->id ?>" unit="<?php echo $i->unit; ?>" uom_id="<?php echo $i->uom_id; ?>"><?php echo $i->name; ?></option>
      <?php endforeach; ?>
    </select>
  </td>
  <td width="100px"><input id="purchase-qty" class="form-control only_number qty" name="detail[qty][]" data-max="999999999"></td>

  <td class="uom" width="80px">
    <select class="form-control uom_id" name="detail[uom_id][]">
      <option value="">Pilih Satuan</option>
    </select>
  </td>
  <td><a href="javascript:void(0);" class="btn btn-sm btn-danger remote_item_po"><i class="fa fa-remove"></i></a></td>
</tr>