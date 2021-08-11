<?php
  if(isset($data_menu_order[1])):
?>
  <div class="text-center" id="pagination_right">
    <?php echo $pagination2; ?>
  </div>
  <ul class="list-order-kitchen" style="margin: 0px 10px 0px 10px;">
<?php
    $counter=0;
    foreach ($data_menu_order[1] as $a):
      $menu_order=$a[0];
      if($counter>=$offset && $counter<($offset+$perpage2)):
?>
    <li class="col-md-6 tag-order" style="padding: 0px;margin-right:3px;width:49%;font-size: 11px;">
      <div class="title-bg title-bg-kitchen" style="padding:0px;">
        <div class="col-md-12" style="padding:0px 0px 0px 2px;background-color: lightgreen;">
          <div class="left" style="color: #881817;text-transform: uppercase;font-weight: bold;font-size: 11px;" title="<?php echo $menu_order->waiter_name_current; ?>"><?php echo ellipsize($menu_order->waiter_name_current,13); ?></div>
          <div class="right" style="color: #881817;text-transform: uppercase;font-weight: bold;font-size: 11px;"><?php echo date("d/m/Y H:i:s",strtotime($menu_order->created_at)); ?></div>
          <div style="clear:both"></div>
          <div style="float:left;font-weight:bold;font-size: 29px;color:green;margin-right: 2px;line-height: 1;" title="<?php echo ($menu_order->type_origin=="dinein" ? "Meja : ".$menu_order->table_name : ($menu_order->table_id!=0 ? "Meja : ".$menu_order->table_name : $menu_order->counter)) ?>"><?php echo substr(($menu_order->type_origin=="dinein" ? $menu_order->table_name : ($menu_order->table_id!=0 ? $menu_order->table_name : $menu_order->counter)),0,3) ?></div>
          <div class="left">
            <h4 class="title-name" style="color:#881817;margin: 0px;font-size: 12px;padding: 0px;text-align:left;" title="<?php echo ($menu_order->customer_name!="" ? $menu_order->customer_name : ($menu_order->type_origin!="dinein" ? "MEJA : ".$menu_order->table_name : "")) ?>"><?php echo ellipsize(($menu_order->customer_name!="" ? $menu_order->customer_name : ($menu_order->type_origin!="dinein" ? "MEJA : ".$menu_order->table_name : "&nbsp;")),10); ?></h4>
            <div style="color: #881817;text-transform: uppercase;font-weight: bold;font-size: 11px;" title="<?php echo $menu_order->order_id; ?>"><?php echo $menu_order->order_id.($menu_order->type_origin!="dinein" ? " | BUNGKUS" : ""); ?></div>
          </div>
          <button title="Print List Menu" class="btn btn-option-list pull-right print_list_menu" style="margin-top: 0px;margin-left:1px;font-size: 15px;font-weight: bold;line-height: 1;padding: 0px 5px;height: 29px;border-radius: 0px !important;" cooking-status="1,2"  order-id="<?php echo (isset($menu_order->order_id) ? $menu_order->order_id  : "")?>"><i class="fa fa-print"></i></button>
					<?php if($setting['checker_group']==0): ?>
						<button class="btn btn-option-list pull-right btn-mode2-post" style="margin-top: 0px;margin-left: 0px;font-size: 15px;font-weight: bold;line-height: 1;padding: 0px 5px;height: 29px;border-radius: 0px !important;">POST</button>
					<?php else: ?>
					<div class="btn-group pull-right" >
						<button type="button" style="margin-top: 0px;margin-left: 0px;font-size: 15px;font-weight: bold;line-height: 1;padding: 0px 5px;height: 29px;border-radius: 0px !important;" class="btn btn-option-list dropdown-toggle" data-toggle="dropdown">
						POST <span class="caret"></span></button>
						<ul class="dropdown-menu" role="menu">
						</ul>
					</div>
					<?php endif; ?>
        </div>
      </div>
      <div class="dark-theme-con" style="overflow:auto;overflow-x:hidden;height:280px;padding-bottom:15px;">
        <table class="kitchen-table" table-id="<?php echo $menu_order->table_id;?>" order-id="<?php echo $menu_order->order_id;?>">
          <thead>
            <tr>
              <th style="width:45%;" colspan="2">MENU</th>
              <th style="width:5%;">JML</th>
              <?php if($setting['count_kitchen_process']==1): ?>
              <th style="width:30%;">PROC</th>
              <?php endif; ?>
              <th style="width:20%;">AKSI</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($a as $menu_order_value) {?>
              <tr class='kitchen-order' process_checker="<?php echo $menu_order_value->process_checker ?>" >
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
                  <center><?php echo $menu_order_value->quantity; ?><input id="order-quantity<?php echo $menu_order_value->id; ?>" type="hidden" value="<?php echo $menu_order_value->quantity; ?>"/></center>
                </td>
                <?php if($setting['count_kitchen_process']==1): ?>
                <td class="border-side-white">
                  <center>
                    <button id="down-quantity<?php echo $menu_order_value->id; ?>" class="btn btn-status-kitchen unavailable btn-mode2-countdown" <?php echo (($menu_order_value->cooking_status!=2 || $menu_order_value->quantity_process == 0) ? "disabled" : "") ?>><i class="fa fa-chevron-down"></i></button>
                    <span class="count-cooking"><?php echo $menu_order_value->quantity_process; ?></span><input id="cooking-quantity<?php echo $menu_order_value->id; ?>" type="hidden" value="<?php echo $menu_order_value->quantity_process; ?>"/>
                    <button id="up-quantity<?php echo $menu_order_value->id; ?>" class="btn btn-status-kitchen cooking btn-mode2-countup" <?php echo (($menu_order_value->cooking_status!=2 || $menu_order_value->quantity_process >= $menu_order_value->quantity) ? "disabled" : "") ?>><i class="fa fa-chevron-up"></i></button>
                  </center>
                </td>
                <?php endif; ?>
                <td class="border-side-white" align="center">
                  <?php if(in_array($menu_order_value->cooking_status,array(1,2))): ?>
                  <button class="btn btn-status-kitchen cooking btn-mode2-cooking <?php echo ($menu_order_value->cooking_status==2 ? "active" : "") ?>">C</button>
									<?php if($menu_order_value->is_package==0): ?>
                  <button class="btn btn-status-kitchen unavailable btn-mode2-unavailable" <?php echo ($menu_order_value->cooking_status==2 ? "style='display:none;'" : ""); ?>>U</button>
                  <?php endif; ?>
                  <button class="btn btn-status-kitchen checklist btn-mode2-checklist <?php echo ($menu_order_value->is_check==1 || (sizeof($outlet_data)>0 ? $outlet_data->checking_order : 0)==0 ? "active" : ""); ?>" <?php echo ($menu_order_value->cooking_status!=2 || (sizeof($outlet_data)>0 ? $outlet_data->checking_order : 0)==0 ? "style='display:none;'" : ""); ?>><i class="fa fa-check"></i></button>
                  <?php endif; ?>
                </td>
                <input id="menu_order_id" order_package_menu_id="<?php echo $menu_order_value->order_package_menu_id ?>" type="hidden" value="<?php echo $menu_order_value->id; ?>"/>
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