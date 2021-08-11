<table class="table table-bordered">
  <thead>
    <tr>
      <th>Inventori</th>
      <th>Berkurang sebanyak</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($results as $r): ?>
    <tr>
      <td><?php echo $r['inventory_name'] ?></td>
      <td><?php echo $r['quantity'] ?> <?php echo $r['code'] ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>