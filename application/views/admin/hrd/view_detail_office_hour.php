<?php 
	if(sizeof($detail)>0){
		echo '<h4>'.date("d/m/Y",strtotime($detail[0]->start_date)).' s/d '.date("d/m/Y",strtotime($detail[0]->end_date)).'</h4>';
	}
?>
<table class="table table-bordered">
  <thead>
    <tr>
      <th>Nama Pegawai</th>
      <th width="300px">Jabatan</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($detail as $d): ?>
    <tr>
      <td><?php echo $d->name ?></td>
      <td><?php echo $d->jobs_name ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>