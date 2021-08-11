<?php 
  if (!empty($data_menu_order)) {
    foreach ($data_menu_order as $menu_order) {
      if ($menu_order->table_id != 0) {
?>
    <div class="title-bg title-bg-kitchen" style="background: #3c1f11;">
      <div class="col-md-12">
        <h4 class="title-name left">Meja <?php echo $menu_order->table_name; ?></h4>
        <button type="button" title="Print List Menu" class="btn btn-trans btn-option-list right print_list_menu" data-type="custom"  cooking-status="1,2"  order-id="<?php echo (isset($menu_order->order[0]->order->order_id ) ? $menu_order->order[0]->order->order_id  : "")?>"><i class="fa fa-print"></i></button>
      </div>
    </div>
    <div class="dark-theme-con" style="padding-bottom:15px;">

        <table class="kitchen-table" table-id="<?php echo $menu_order->table_id;?>">
            <thead>
            <tr>
                <th style="width:8%;">JAM PESAN</th>
                <th style="width:25%;">NAMA MENU</th>
                <th class="" style="width:5%;">PORSI</th>
                <th>CATATAN</th>
                <th class="" style="width:10%">STATUS</th>
                <th style="width:175px;">AKSI</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($menu_order->order as $menu_order_value) {?>
                <tr class='kitchen-order' process_checker="<?php echo $menu_order_value->order->process_checker ?>">
                    <td><?php echo date("H:i:s",strtotime($menu_order_value->order->created_at)); ?></td>
                    <td><?php echo $menu_order_value->order->menu_name; ?></td>
                    <td class="border-side-white">
                        <center><?php echo $menu_order_value->order->quantity; ?></center>
                    </td>
                    <td><?php 
                        echo "Tipe : ".($menu_order_value->order->dinein_takeaway==0 ? "Dine In" : "Takeaway")."<br>";
                        foreach ($menu_order_value->option_list as $option) {
                            echo '- ' . $option->option_name . ' : ' . $option->option_value_name . '<br>';
                        }
                        foreach ($menu_order_value->side_dish_list as $side_dish) {
                            echo '- ' . $side_dish->name . '<br> ';
                        }
                        if (!empty($menu_order_value->order->note)) {
                            echo 'catatan : ' . $menu_order_value->order->note;
                        }
                        ?>
                    </td>
                    <td class="border-side-white">
                        <center><?php echo $menu_order_value->order->status_name; ?></center>
                    </td>
                    <td order-id="<?php echo $menu_order_value->order->order_id;?>" align="center">
                        <?php if($menu_order_value->order->cooking_status==1): ?>
                        <button class="btn btn-status btn-cooking" <?php if ($menu_order_value->order->cooking_status == 2)echo 'disabled="true"';?>><img src="<?php echo base_url() ?>assets/img/ico-cooking.png"/></button>
                        <?php endif; ?>
                        <?php if($menu_order_value->order->cooking_status==2): ?>
                        <button class="btn btn-status btn-ready" ><img src="<?php echo base_url() ?>assets/img/ico-ready.png"/></button>
                        <?php endif; ?>
                        <?php if($menu_order_value->order->cooking_status==1): ?>
                        <button class="btn btn-status btn-delete"><img src="<?php echo base_url() ?>assets/img/ico-delete.png"/></button>
                        <?php endif; ?>
                    </td>
                    <input id="menu_order_id" type="hidden"
                           value="<?php echo $menu_order_value->order->id; ?>"/>

                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
      <?php 
        }else {
          foreach ($menu_order->order as $order_takeaway) { 
      ?>
    <div class="title-bg title-bg-kitchen">
      <div class="col-md-12">
        <h4 class="title-name left"><?php echo $order_takeaway[0]->order->customer_name; ?></h4>
        <button type="button" title="Print List Menu" class="btn btn-trans btn-option-list right print_list_menu" data-type="custom"  cooking-status="1,2"  order-id="<?php echo (isset($order_takeaway[0]->order->order_id ) ? $order_takeaway[0]->order->order_id  : "")?>"><i class="fa fa-print"></i></button>
      </div>
    </div>
    <div class="dark-theme-con" style="padding-bottom:15px;">
      <table class="kitchen-table">
          <thead>
          <tr>
              <th style="width:8%;">JAM PESAN</th>
              <th style="width:25%;">NAMA MENU</th>
              <th class="" style="width:5%;">PORSI</th>
              <th>CATATAN</th>
              <th class="" style="width:10%">STATUS</th>
              <th style="width:175px;">AKSI</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($order_takeaway as $takeaway) { ?>
              <tr class='kitchen-order'>
                  <td><?php echo date("H:i:s",strtotime($takeaway->order->created_at)); ?></td>
                  <td><?php echo $takeaway->order->menu_name; ?></td>
                  <td class="border-side-white">
                      <center><?php echo $takeaway->order->quantity   ; ?></center>
                  </td>
                  <td><?php foreach ($takeaway->option_list as $option) {
                          echo '- ' . $option->option_name . ' : ' . $option->option_value_name . '<br>';
                      }
                      foreach ($takeaway->side_dish_list as $side_dish) {
                          echo '- ' . $side_dish->name . '<br> ';
                      }
                      if (!empty($takeaway->notes)) {
                          echo 'catatan : ' . $takeaway->notes;
                      }
                      ?>
                  </td>
                  <td class="border-side-white">
                      <center><?php echo $takeaway->order->status_name; ?></center>
                  </td>
                  <td align="center">
                      <?php if($takeaway->order->cooking_status==1): ?>
                      <button class="btn btn-status btn-cooking" <?php if ($takeaway->order->cooking_status == 2)echo 'disabled="true"'; ?>><img src="<?php echo base_url() ?>assets/img/ico-cooking.png"/></button>
                      <?php endif; ?>
                      <?php if($takeaway->order->cooking_status==2): ?>
                      <button class="btn btn-status btn-ready" ><img src="<?php echo base_url() ?>assets/img/ico-ready.png"/></button>
                      <?php endif; ?>
                      <?php if($takeaway->order->cooking_status==1): ?>
                      <button class="btn btn-status btn-delete"><img src="<?php echo base_url() ?>assets/img/ico-delete.png"/></button>
                      <?php endif; ?>
                  </td>
                  <input id="menu_order_id" type="hidden"
                         value="<?php echo $takeaway->order->id; ?>"/>
              </tr>
          <?php } ?>

          </tbody>
      </table>
    </div>
  <?php
          }
        }
      }
    }
    else {
      echo '<p style="text-align: center;font-size: 25px;font-weight: bold;color:#FFFFFF;margin-top:50px;">' . $this->lang->line('ds_no_order_kitchen') . '</p>';
    } 
  ?>