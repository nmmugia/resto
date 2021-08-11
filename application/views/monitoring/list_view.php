<style>
  .kitchen-table td{
    padding: 0px;
    text-transform: uppercase;
    font-weight: bold;
  }
  .kitchen-table th{
    padding: 0px;
  }
  .pagination > .active > a, .pagination > .active > span, .pagination > .active > a:hover, .pagination > .active > span:hover, .pagination > .active > a:focus, .pagination > .active > span:focus {
    background-color: #881817;
    border-color: #881817;
  }
  .pagination > li > a, .pagination > li > span {
    color: #881817;
  }
  .pagination > li > a:hover, .pagination > li > span:hover, .pagination > li > a:focus, .pagination > li > span:focus{
    color: #881817;
  }
</style>
<input type="hidden" value="<?php echo sizeof($data_menu_order)>0 || sizeof($data_without_menu_order); ?>" id="number_of_data"/>
<input type="hidden" value="<?php echo isset($perpage)?$perpage:0; ?>" id="perPage"/>
<div class="text-center" id="pagination">
  <?php echo $pagination; ?>
</div>
<ul class="list-order-kitchen" style="margin: 0px 10px 0px 10px;">
<?php
  if(sizeof($data_menu_order)>0 || sizeof($data_without_menu_order)>0):
  $counter=0;
?>

<?php
  foreach ($data_without_menu_order as $menu_order):
    if($counter>=$offset && $counter<($offset+$perpage)):
?>
    <li class="col-md-2" style="padding: 0px;margin-right:3px;width:16.4%" id="reservation_<?php echo $menu_order->reservation_id ?>">
      <div class="title-bg title-bg-kitchen" style="padding:0px;">
        <div class="col-md-12" style="padding:0px 0px 0px 2px;">
          <div class="left" style="color: #881817;text-transform: uppercase;font-weight: bold;font-size: 11px;" title="<?php echo $menu_order->operator_name; ?>"><?php echo ellipsize($menu_order->operator_name,15); ?></div>
          <div class="right" style="color: #881817;text-transform: uppercase;font-weight: bold;font-size: 11px;"><?php echo date("d/m/Y H:i:s",strtotime($menu_order->book_date)); ?></div>
          <div style="clear:both"></div>
          <div style="float:left;font-weight:bold;font-size: 29px;color:green;margin-right: 2px;line-height: 1;" title="<?php echo ($menu_order->table_id!=0 ? $menu_order->table_name : $menu_order->counter) ?>"><?php echo substr(($menu_order->table_id!=0 ? $menu_order->table_name : $menu_order->counter),0,3) ?></div>
          <div class="left">
            <h4 class="title-name" style="color:#881817;margin: 0px;font-size: 12px;padding: 0px;text-align:left;" title="<?php echo ($menu_order->customer_name!="" ? $menu_order->customer_name : "") ?>"><?php echo ellipsize(($menu_order->customer_name!="" ? $menu_order->customer_name : ""),10); ?></h4>
          </div>
          <button class="btn btn-option-list pull-right btn-mode2-post" style="margin-top: 0px;margin-left: 0px;font-size: 15px;font-weight: bold;line-height: 1;padding: 0px 5px;height: 29px;border-radius: 0px !important;<?php echo ($menu_order->status_posting==1 || strtotime(date("Y-m-d"))<strtotime(date("Y-m-d",strtotime($menu_order->book_date))) ? "display:none;" : "") ?>">POST</button>
        </div>
      </div>
      <div class="dark-theme-con" style="overflow:auto;overflow-x:hidden;height:220px;padding-bottom:15px;">
        <table class="kitchen-table" table-id="<?php echo $menu_order->table_id;?>" order-id="<?php echo $menu_order->order_id;?>" reservation-id="<?php echo $menu_order->reservation_id ?>">
          <thead>
            <tr>
              <th style="width:90%;" colspan="2">MENU</th>
              <th style="width:10%;">JML</th>
            </tr>
          </thead>
          <tbody>
            
          </tbody>
        </table>
      </div>
    </li>
<?php 
    endif;
    $counter++;
    endforeach;
?>
<?php
    foreach ($data_menu_order as $a):
      $menu_order=$a[0];
      if($counter>=$offset && $counter<($offset+$perpage)):
?>
    <li class="col-md-2" style="padding: 0px;margin-right:3px;width:16.4%" id="reservation_<?php echo $menu_order->reservation_id ?>">
      <div class="title-bg title-bg-kitchen" style="padding:0px;">
        <div class="col-md-12" style="padding:0px 0px 0px 2px;">
          <div class="left" style="color: #881817;text-transform: uppercase;font-weight: bold;font-size: 11px;" title="<?php echo $menu_order->waiter_name; ?>"><?php echo ellipsize($menu_order->waiter_name,15); ?></div>
          <div class="right" style="color: #881817;text-transform: uppercase;font-weight: bold;font-size: 11px;"><?php echo date("d/m/Y H:i:s",strtotime($menu_order->start_order)); ?></div>
          <div style="clear:both"></div>
          <div style="float:left;font-weight:bold;font-size: 29px;color:green;margin-right: 2px;line-height: 1;" title="<?php echo ($menu_order->table_id!=0 ? $menu_order->table_name : $menu_order->counter) ?>"><?php echo substr(($menu_order->table_id!=0 ? $menu_order->table_name : $menu_order->counter),0,3) ?></div>
          <div class="left">
            <h4 class="title-name" style="color:#881817;margin: 0px;font-size: 12px;padding: 0px;text-align:left;" title="<?php echo ($menu_order->customer_name!="" ? $menu_order->customer_name : "") ?>"><?php echo ellipsize(($menu_order->customer_name!="" ? $menu_order->customer_name : ""),10); ?></h4>
            <div style="color: #881817;text-transform: uppercase;font-weight: bold;font-size: 11px;" title="ORDER ID : <?php echo $menu_order->order_id; ?>"><?php echo $menu_order->order_id; ?></div>
          </div>
          <button title="Print List Menu" class="btn btn-option-list pull-right print_list_menu" style="margin-top: 0px;margin-left:1px;font-size: 13px;font-weight: bold;line-height: 1;padding: 0px 5px;height: 29px;border-radius: 0px !important;" order-id="<?php echo (isset($menu_order->order_id) ? $menu_order->order_id  : "")?>"><i class="fa fa-print"></i></button>
          <button class="btn btn-option-list pull-right btn-mode2-post" style="margin-top: 0px;margin-left: 0px;font-size: 15px;font-weight: bold;line-height: 1;padding: 0px 5px;height: 29px;border-radius: 0px !important;<?php echo ($menu_order->status_posting==1 || strtotime(date("Y-m-d"))<strtotime(date("Y-m-d",strtotime($menu_order->start_order))) ? "display:none;" : "") ?>">POST</button>
        </div>
      </div>
      <div class="dark-theme-con" style="overflow:auto;overflow-x:hidden;height:220px;padding-bottom:15px;">
        <table class="kitchen-table" table-id="<?php echo $menu_order->table_id;?>" order-id="<?php echo $menu_order->order_id;?>" reservation-id="<?php echo $menu_order->reservation_id ?>">
          <thead>
            <tr>
              <th style="width:90%;" colspan="2">MENU</th>
              <th style="width:10%;">JML</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($a as $menu_order_value) {?>
              <tr class='kitchen-order' process_checker="<?php echo $menu_order_value->process_checker ?>">
                <td style="color:<?php echo ($menu_order_value->color!="" ? $menu_order_value->color : ""); ?>;background-color:<?php echo ($menu_order_value->background_color!="" ? $menu_order_value->background_color : ""); ?>">
                  <?php 
                    $char_stop=12+5;
                    $notes="";
                    if (!empty($menu_order_value->note)) {
                      $notes.=$menu_order_value->note . '<br> ';
                    }
                    foreach ($menu_order_value->option_list as $option) {
                      $notes.='- '.$option->option_value_name . '<br>';
                    }
                    foreach ($menu_order_value->side_dish_list as $side_dish) {
                      $notes.='- ' . $side_dish->name. '<br>';
                    }
                    if($char_stop=="")$char_stop=15+5;
                    $menu_name=$menu_order_value->menu_short_name;
                    if($menu_name==""){
                      $menu_name=substr($menu_order_value->menu_name,0,$char_stop).(strlen($menu_order_value->menu_name)>$char_stop ? ".." : "");
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
                <input id="menu_order_id" type="hidden" value="<?php echo $menu_order_value->id; ?>"/>
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
<?php else: ?>
<center style="margin-top:20px;"><b>Data Reservasi tidak ditemukan!</b></center>
<?php endif; ?>
</ul>

