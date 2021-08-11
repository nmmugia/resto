<?php 
  $counter=1;
  foreach($lists as $l): ?>
<tr>
  <td><?php echo $counter; ?></td>
  <td><?php echo strtoupper($l->name); ?></td>
  <td align="center"><?php echo date("d F Y",strtotime($l->date)); ?></td>
  <td align="center"><?php echo date("H:i:s",strtotime($l->time_in)); ?></td>
  <td align="center"><?php echo ($l->time_out!="" ? date("H:i:s",strtotime($l->time_out)) : "" ); ?></td>
</tr>
<?php 
  $counter++;
  endforeach; 
?>