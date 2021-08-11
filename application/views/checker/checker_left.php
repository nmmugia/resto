<?php
  if(isset($data_menu_order[0])):
?>
  <div class="text-center" id="pagination_left">
    <?php echo $pagination; ?>
  </div>
  <ul class="list-order-kitchen" style="margin: 0px 10px 0px 10px;">
<?php
    $counter=0;
    foreach ($data_menu_order[0] as $a):
      $menu_order=$a[0];
      if($counter>=$offset && $counter<($offset+$perpage)):
?>
    <li class="col-md-3" style="padding: 0px;margin-right:3px;width:24.5%;font-size: 11px;">
      <div class="title-bg title-bg-kitchen" style="padding:0px;">
        <div class="col-md-12" style="padding:0px 0px 0px 2px;">
          <div class="left" style="color: #881817;text-transform: uppercase;font-weight: bold;font-size: 11px;" title="<?php echo $menu_order->waiter_name_current; ?>"><?php echo ellipsize($menu_order->waiter_name_current,13); ?></div>
          <div class="right" style="color: #881817;text-transform: uppercase;font-weight: bold;font-size: 11px;"><?php echo date("d/m/Y H:i:s",strtotime($menu_order->created_at)); ?></div>
          <div style="clear:both"></div>
          <div style="float:left;font-weight:bold;font-size: 29px;color:green;margin-right: 2px;margin-left: 4px;line-height: 1;" title="<?php echo ($menu_order->type_origin=="dinein" ? "Meja : ".$menu_order->table_name : ($menu_order->table_id!=0 ? "Meja : ".$menu_order->table_name : $menu_order->counter)) ?>"><?php echo substr(($menu_order->type_origin=="dinein" ? $menu_order->table_name : ($menu_order->table_id!=0 ? $menu_order->table_name : $menu_order->counter)),0,3) ?></div>
          <div class="left">
            <h4 class="title-name" style="color:#881817;margin: 0px;font-size: 12px;padding: 0px;text-align:left;" title="<?php echo ($menu_order->customer_name!="" ? $menu_order->customer_name : ($menu_order->type_origin!="dinein" ? "MEJA : ".$menu_order->table_name : "")) ?>"><?php echo ellipsize(($menu_order->customer_name!="" ? $menu_order->customer_name : ($menu_order->type_origin!="dinein" ? "MEJA : ".$menu_order->table_name : "&nbsp;")),10); ?></h4>
            <div style="color: #881817;text-transform: uppercase;font-weight: bold;font-size: 11px;" title="<?php echo $menu_order->order_id; ?>"><?php echo $menu_order->order_id; ?></div>
          </div>
          <button title="Print List Menu" class="btn btn-option-list pull-right print_list_menu" style="margin-top: 0px;margin-left:1px;font-size: 13px;font-weight: bold;line-height: 1;padding: 0px 9px;height: 29px;border-radius: 4px !important;" cooking-status="7"  order-id="<?php echo (isset($menu_order->order_id) ? $menu_order->order_id  : "")?>"><i class="fa fa-print"></i></button>
          <button class="btn btn-option-list pull-right btn-mode2-post" style="margin-top: 0px;margin-left: 0px;font-size: 15px;font-weight: bold;line-height: 1;padding: 0px 9px;height: 29px;border-radius: 4px !important;">POST</button>
        </div>
      </div>
      <div class="dark-theme-con" style="overflow:auto;overflow-x:hidden;height:280px;padding-bottom:15px;">
        <table class="kitchen-table" table-id="<?php echo $menu_order->table_id;?>" order-id="<?php echo $menu_order->order_id;?>">
          <thead>
            <tr>
              <th style="width:80%;" colspan="2">MENU</th>
              <th style="width:10%;">JML</th>
              <th style="width:10%;">AKSI</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($a as $menu_order_value) {?>
              <tr class='kitchen-order' process_checker="<?php echo $menu_order_value->process_checker ?>">
                <td style="color:<?php echo ($menu_order_value->color!="" ? $menu_order_value->color : ""); ?>;background-color:<?php echo ($menu_order_value->background_color!="" ? $menu_order_value->background_color : ""); ?>">
                  <?php 
                    $notes="";
                    if (!empty($menu_order_value->note)) {
                      $notes.=$menu_order_value->note . '<br> ';
                    }
                    foreach ($menu_order_value->option_list as $option) {
                      $notes.='- ' . $option->option_value_name . '<br>';
                    }
                    foreach ($menu_order_value->side_dish_list as $side_dish) {
                      $notes.='- ' . $side_dish->name. '<br>';
                    }
                    $menu_name=$menu_order_value->menu_short_name;
                    if($menu_name==""){
                      $menu_name=$menu_order_value->menu_name;
                    }
                  ?>
                  <span title="<?php echo $menu_order_value->menu_name; ?>"><?php echo $menu_name; ?></span>
                  
                </td>
                <td>
                  <?php if($notes!=""): ?>
                  <div class="blink">
                    <img src="<?php echo base_url() ?>assets/img/notif.png" style="width: 9px;"/>
                    <div class="popup-notes"><?php echo $notes; ?></div>
                  </div>
                  <?php endif; ?>
                </td>
                <td class="border-side-white">
                  <center><?php echo $menu_order_value->quantity; ?></center>
                </td>
                <td class="border-side-white" align="center">
                  <button class="btn btn-status-kitchen checklist btn-mode2-checklist <?php echo ($menu_order_value->is_check==1 ? "active" : ""); ?>"><i class="fa fa-check"></i></button>
                </td>
                <input id="menu_order_id" type="hidden" order_package_menu_id="<?php echo $menu_order_value->order_package_menu_id ?>" value="<?php echo $menu_order_value->id; ?>"/>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </li>
<?php 
    endif;
    $counter++;
    endforeach;
?>
  </ul>
<?php
    endif; 
?>