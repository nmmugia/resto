<link rel="stylesheet" href="<?php echo base_url() ?>assets/js/plugins/easyautocomplete/easy-autocomplete.min.css">
<input id="temp_total_ordered" name="temp_total_ordered" type="hidden" value='0'/>
<input id="zero_stock_order"  type="hidden" value="<?php echo $setting['zero_stock_order'] ?>"/>
<input id="tax_percentages"  type="hidden" value="<?php echo $tax_percentages ?>"/>
<input id="order_type" name="order_type" type="hidden" value="<?php echo $form_data->order_type ?>"/>
<input id="reservation_id"  type="hidden" value="<?php echo $form_data->id ?>"/>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/plugins/list.min.js"></script>
<?php 
  foreach ($taxes as $tax) {
    echo '<input type="hidden" id="tax-price" account-id="' . $tax->account_id . '" percentage="'.$tax->tax_percentage.'" tax-name="'.$tax->tax_name.'" service="'.$tax->is_service.'">';
  }

  foreach ($taxes_dine_in as $tax) {
    echo '<input type="hidden" id="tax-price-dinein" account-id="' . $tax->account_id . '" percentage="'.$tax->tax_percentage.'" tax-name="'.$tax->tax_name.'" service="'.$tax->is_service.'">';
  }

  foreach ($taxes_takeaway as $tax) {
    echo '<input type="hidden" id="tax-price-takeaway" account-id="' . $tax->account_id . '" percentage="'.$tax->tax_percentage.'" tax-name="'.$tax->tax_name.'" service="'.$tax->is_service.'">';
  }

  foreach ($taxes_delivery as $tax) {
    echo '<input type="hidden" id="tax-price-delivery" account-id="' . $tax->account_id . '" percentage="'.$tax->tax_percentage.'" tax-name="'.$tax->tax_name.'" service="'.$tax->is_service.'">';
  }
?>
<div class="popup-block" id="popup-new-order" style="display:none;">
    <div class="popup-order">
        <div class="col-lg-12">
            <form id="form-input-order">
            <div class="title-bg-popup">
                    <a  class="btn btn-std btn-cancel btn-distance" style="float:right"> X </a></div>
                <div class="title-bg title-bg-food">
                    <h4 class="title-popup menu-name"></h4>
                    <input type="hidden" id="menu_id_selected" value="">
                    <input type="hidden" id="menu_order_id_selected" value="">
                    <input type="hidden" id="is_already_process" value="">
                </div>
                <div class="popup-order-panel">
                    <div class="dark-theme-con"
                         style="float:left;width:50%;height:310px;overflow-x: hidden;overflow-y: auto;padding:5px;">
                        <table class="acc-table" style="width: 100%">
                            <tbody>
                            <tr>
                                <td>
									<label><?php echo $this->lang->line('ds_lbl_amount'); ?></label>
                                    <div class="input-group" style="width:100%;">
                                        <button type="button" class="btn btn-float btn-number" style="left:4px;" data-type="minus"
                                                data-field="quantity">
                                            <span class="glyphicon glyphicon-minus"></span>
                                        </button>
                                        <input type="text" name="quantity" class="form-control input-number count-order"
                                               value="1"
                                               min="1" max="1000">
                                        <button type="button" class="btn btn-float btn-default btn-number" style="right:4px;" data-type="plus"
                                                data-field="quantity">
                                            <span class="glyphicon glyphicon-plus"></span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4"><label><?php echo $this->lang->line('ds_lbl_option'); ?></label>
                                </td>
                            </tr>
                            <tr>
                                <td class="menu-option" colspan="4">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4"><label><?php echo $this->lang->line('ds_lbl_side_dish'); ?></label>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <table class="side-dish" style="width:100%;"></table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="order-note">
                        <label><?php echo $this->lang->line('ds_lbl_notes'); ?></label>
                        <textarea class="form-control order-notes" style="resize:none;height:274px;"></textarea>
                    </div>
                    <div class="clearfix"></div>
                    <div class="button-wrapper">
                        <button type="reset" class="btn btn-std btn-cancel-dine-in" style=""><?php echo $this->lang->line('ds_submit_cancel'); ?></button>
                        <button class="btn btn-std btn-delete-order" style="display:none;"><?php echo $this->lang->line('ds_btn_cancel'); ?></button>
                        <button class="btn btn-std" id="btn-save-reservation"><?php echo $this->lang->line('ds_submit_ok'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
echo form_open_multipart(base_url(uri_string()), array('class' => 'form-horizontal form-ajax',"id"=>"from_reservasi"));

?>
<style type="text/css">
.panel-heading{
    background-color: #fff;

}



.ui-datepicker-trigger {
    margin: 4px;
}


</style>

    <link href="<?php echo base_url(); ?>assets/css/bootstrap-datetimepicker.css" rel="stylesheet">

<?php
  $this->load->view('partials/navigation_v');
?>

                
              
<div id="page-wrapper">
    <div class="container-fluid">
			
			<div class="col-lg-12" style="margin-top:30px;margin-bottom:15px;">
                <div class="row" style="display:none">
					<div class="col-sm-4">
						<div class="row">
							<div class="resto-info-mini">
								<div class="resto-info-pic"></div>
								<div class="resto-info-name">
									<?php echo $data_store->store_name; ?>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-4">
					</div>
					 <div class="col-sm-4">
						<div class="row">
							<div class="margin-wrap">
								<div class="panel-info">
									<div class="col-xs-6">
										<p class="role-info text-left">reservation</p>
									</div>
									<div class="col-xs-6">
										<p class="role-info text-right"><?php echo ucfirst($this->data['group_name']); ?></p>
										<p class="role-name text-right"><?php echo $user_name; ?></p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
            </div><!-- /.col-md-12 -->
			<div class="clearfix"></div>
           
			<div class="panel panel-default">
                
                <div class="col-lg-12">
					<div class="row">
						<div class="title-bg-custom">
							<h4 class="title-name left"><?php if (isset($subtitle)) echo $subtitle; ?></h4>
						</div>
					</div>
                </div><!-- /.col-md-12 -->
                
                <div class="panel-body">
					
                   <div class="result">
                    <?php

                    if (! empty($message_success)) {
                        echo '<div class="alert alert-success" role="alert">';
                        echo $message_success;
                        echo '</div>';
                    }
                    if (! empty($message)) {
                        echo '<div class="alert alert-danger" role="alert">';
                        echo $message;
                        echo '</div>';
                    }
                    ?>
					</div>
          <ul class="nav nav-tabs">
                    <li class="active"><a href="#reservation-info-tab" data-toggle="tab">Data Informasi</a>
                    </li>
                    <li><a href="#reservation-menu-tab" data-toggle="tab">Menu</a>
                    </li>
                  </ul>
                  <div class="tab-content">
                    <div class="tab-pane fade in active" id="reservation-info-tab" style="padding-top: 20px">
                      <div class="col-lg-12">
                         <div class="row" style="padding-top:20px;">
                                <div class="col-lg-12">

                                        <div class="form-group">
                                            <label for="discount_value" class="col-sm-2 control-label">Atas nama</label>

                                            <div class="col-sm-10">
                                                <?php echo form_input($customer_name); ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="discount_value" class="col-sm-2 control-label">Nomor kontak</label>

                                            <div class="col-sm-10">
                                                <?php echo form_input($phone); ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                          <label for="discount_value" class="col-sm-2 control-label">Alamat</label>
                                          <div class="col-sm-10">
                                              <?php echo form_textarea($customer_address); ?>
                                          </div>
                                      </div>


                      <div class="form-group">
                                            <label for="discount_value" class="col-sm-2 control-label">Waktu</label>

                                            <div class="col-sm-3">
                          <div class='input-group date' id='panel_calendar'>
                            <?php echo form_input($book_date); ?>

                            <!-- <input type='text' class="form-control" /> -->
                            <span class="input-group-addon">
                              <span class="glyphicon glyphicon-calendar">
                              </span>
                            </span>
                          </div>
                          <small>waktu revervasi harus lebih dari waktu sekarang</small>
                                            </div>
                                        </div>


                                        <div class="form-group" <?php echo ($form_data->order_type==1 ? "" : "style='display:none;'") ?>>
                                            <label for="discount_value" class="col-sm-2 control-label">Jumlah tamu</label>

                                            <div class="col-sm-10">
                                                <?php echo form_input($customer_count); ?>
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <label for="discount_value" class="col-sm-2 control-label">Catatan</label>

                                            <div class="col-sm-10">
                                                <?php echo form_textarea($book_note); ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                          <label for="tax_percentage" class="col-sm-2 control-label">Tipe Pesanan</label>
                                          <div class="col-sm-10">
                                            <label class="radio-inline">
                                              <?php echo form_radio($order_type_dinein); ?>
                                               <small>Dine In</small>
                                            </label>
                                            <label class="radio-inline">
                                              <?php echo form_radio($order_type_takeaway); ?>
                                               <small>Takeaway</small>
                                            </label>
                                            <label class="radio-inline">
                                              <?php echo form_radio($order_type_delivery); ?>
                                               <small>Delivery Order</small>
                                            </label>
                                            <br>
                                            <small>Dinein / Takeaway / Delivery Order ?</small>
                                          </div>
                                        </div>
                                        <div class="form-group" <?php echo ($form_data->order_type==1 ? "" : "style='display:none;'") ?>>
                                            <label for="discount_value" class="col-sm-2 control-label">Meja</label>

                                            <div class="col-sm-10">
                                                <?php
                                                echo form_dropdown('table_id', $table, 
                                                $form_data->table_id, 
                                                'id="table_id" field-name = "meja" 
                                                class="form-control" autocomplete="on"');

                                                ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                              <label for="discount_value" class="col-sm-2 control-label">Down Payment</label>

                                              <div class="col-sm-10">
                                                  <?php echo form_input($down_payment); ?>
                                              </div>
                                          </div>
                                          <div class="form-group">
                                        <label for="tax_percentage" class="col-sm-2 control-label">Tipe Down Payment</label>

                                        <div class="col-sm-10">
                                          <label class="radio-inline">
                                            <?php echo form_radio($dp_type_cash); ?>
                                             <small>Cash</small>
                                          </label>

                                          <label class="radio-inline">
                                            <?php echo form_radio($dp_type_transfer); ?>
                                             <small>Transfer</small>
                                          </label>
                                          <label class="radio-inline">
                                            <?php echo form_radio($dp_type_transfer_direct); ?>
                                             <small>Kartu</small>
                                          </label>
                                          <br>
                                          <small>DP cash atau DP Transfer atau DP Kartu?</small>

                                        </div>
                                      </div>


                                   
                              
                      
                                </div><!-- /.col-md-12 -->
                               
                            </div> 
                          </div> 
                    </div> 
                    <div class="tab-pane fade in" id="reservation-menu-tab">
                        <div class="order-wrapper">
                           <div class="col-md-4">
                              <div class="row">
                                  <div class="panel-wrapper">
                                      <div class="title-bg-custom">
                                          <h4 class="title-name left"style="font-size: 16px;"><?php echo $this->lang->line('ds_nav_category') ?></h4>
                                          <button id="btn-category-list" class="btn btn-option-list right active"><img src="<?php echo base_url() ?>assets/img/icon-list.png" alt="list"/></button>
                                          <button id="btn-category-thumb" class="btn btn-option-list right"><img src="<?php echo base_url() ?>assets/img/icon-thumb.png" alt="list"/></button>

                                      </div>
                                      <div class="dark-theme-con">
                                          <div class="row" style="margin: 0 !important;">
                                              <div class="col-lg-12" style="height: 440px;display: block;overflow-x: hidden;overflow-y:scroll;background-image: url('../../assets/img/reskin/bg-table.jpg');">

                                                      <?php if (!empty($categories)) { ?>
                                                      <ul class="list-category-text" id="list-category-text">                                   
                                                          <?php foreach ($categories as $category) { 
                                                              echo '<a href="' . base_url('cashier/get_menus') . '" class="get_menus" data-category="' . $category->id . '"><li>';
                                                              echo $category->category_name . '</li></a>';
                                                          }       
                                                          echo  '</ul>';

                                                      }
                                                      else {
                                                          echo '<h5 style="color:#000;text-align: center">' . $this->lang->line('ds_category_empty') . '</h5>';
                                                      } ?>
                                                      <!-- end Mode List Category -->

                                                      <span id="thumb-category-text" style="display:none;">
                                                          <?php if (!empty($categories)) {
                                                          foreach ($categories as $category) {
                                                              echo '<a href="' . base_url('cashier/get_menus') . '" class="order-menu-categories get_menus" data-category="' . $category->id . '">';
                                                              if (empty($category->icon_url)) {
                                                                  echo '<img src="' . base_url('assets/img/default-category.jpg') . '" alt="menu"/>';
                                                              }
                                                              else {
                                                                  echo '<img src="' . base_url($category->icon_url) . '" alt="menu"/>';
                                                              }
                                                              echo '<p>' . $category->category_name . '</p>';
                                                              echo '</a>';
                                                          }
                                                      }
                                                      else {
                                                          echo '<h5 style="color:#000;text-align: center">' . $this->lang->line('ds_category_empty') . '</h5>';
                                                      } ?>
                                                      </span>


                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                         
                        
                          <div class="col-md-4">
                              <div class="row">
                                  <div class="panel-wrapper">                  
                                      <div class="col-lg-12" style="padding: 0 !important;" id="menus">
                                          <div class="title-bg-custom">
                                            <div class="input-group" style="width:68%;float:left;padding-top: 5px;">
                                               <div class="input-group-addon" style="color:#881817;font-size:16px;font-family: 'robotobold';">MENU</div>
                                               <input class="search form-control" placeholder="Search" />
                                            </div>
                                            <div style="width:32%;float:left">
                                              <button id="btn-menu-list" class="btn btn-option-list right active"><img src="<?php echo base_url() ?>assets/img/icon-list.png" alt="list"/></button>
                                              <button id="btn-menu-thumb" class="btn btn-option-list right"><img src="<?php echo base_url() ?>assets/img/icon-thumb.png" alt="list"/></button>
                                              <input type="hidden" id="menu-view-type" value="list"/>
                                            </div>
                                          </div>
                                          <!-- <div class="dark-theme-con res-height" style="height: 440px"> -->
                                          <div class="dark-theme-con" style="height: 440px">
                                              <div class="col-lg-12" style="height:100%;overflow-x:hidden;overflow-y: auto;background-image: url('../../assets/img/reskin/bg-table.jpg');">
                                                  <div class="container-menus">


                                                       <!-- Mode List Menu -->
                                                      <ul class="list-category-text list" id="list-menu-text">
                                                      <?php if (!empty($menus)) { ?>
                                                        <?php  foreach ($menus as $menu) { ?>
                                                          <li data-id="<?php echo $menu->id; ?>"
                                                                   data-name="<?php echo $menu->menu_name; ?>"
                                                                   data-price="<?php echo $menu->menu_price; ?>"
                                                                   class="add-order-menu">
                                                              <span class="left name" style="width:75%;"><?php echo $menu->menu_name; ?></span>
                                                              <span id="" class="right <?php echo ($setting['zero_stock_order']==1 ? "hide" : "") ?> <?php echo 'total-available-'.$menu->id ?>" 
                                                                      data-outlet="<?php echo $menu->outlet_id ?>" style="width:10%;text-align:right;"><?php echo $menu->total_available; ?>
                                                                  </span>
                                                                  
                                                              <span class="right"><?php echo number_format($menu->menu_price,0,"",".");?></span>
                                                              <span class="left">Rp</span>
                                                          
                                                          </li>
                                                      <?php }
                                                      echo '</ul>';
                                                      }
                                                      else {
                                                          echo '<h5 style="color:#000;text-align: center">' . $this->lang->line('ds_choose_category') . '</h5>';
                                                      } ?>

                                                      <span id="thumb-menu-text" style="display:none;">                                    
                                                      <?php if (!empty($menus)) {
                                                          foreach ($menus as $menu) {
                                                              $image = base_url('assets/img/default-category.jpg');
                                                              if ($menu->icon_url != '') {
                                                                  $image = base_url($menu->icon_url);
                                                              }
                                                              ?>
                                                              <div data-id="<?php echo $menu->id; ?>"
                                                                   data-name="<?php echo $menu->menu_name; ?>"
                                                                   data-price="<?php echo $menu->menu_price; ?>"
                                                                   class="menu-order add-order-menu">
                                                                  <img src="<?php echo $image; ?>" alt="menu"/>

                                                                  <p><?php echo $menu->menu_name; ?></p>
                                                              </div>
                                                          <?php }
                                                      }
                                                      else {
                                                          echo '<h5 style="color:#000;text-align: center">' . $this->lang->line('ds_choose_category') . '</h5>';
                                                      } ?>

                                                  </span>


                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div><!-- /.col-md-4 -->
                         
                          <div class="col-md-4">
                              <div class="row">
                                  <div class="panel-wrapper">
                                      <div class="title-bg-custom">
                                        <div class="col-sm-4">
                                          <h4 class="title-name left">Pesanan</h4>
                                        </div>
                                        <div class="col-sm-6" style="padding: 0px;margin-top: 7px;">
                                          <select style="<?php echo ($form_data->order_type==3 ? "" : "display:none;") ?>" id="reservation_delivery_cost_id" name="reservation_delivery_cost_id" class="form-control" placeholder="<?php echo $this->lang->line('ds_lbl_delivery_cost'); ?>">
                                            <option value="" data-delivery_cost="">Pilih Ongkos Kirim</option>
                                            <?php foreach($delivery_cost_lists as $d): ?>
                                            <option value="<?php echo $d->id; ?>" data-delivery_cost="<?php echo $d->delivery_cost; ?>" <?php echo (sizeof($order_data)>0 && $order_data->delivery_cost_id==$d->id ? "selected" : "") ?> data-is_percentage="<?php echo $d->is_percentage; ?>" ><?php echo $d->delivery_cost_name; ?></option>
                                            <?php endforeach; ?>
                                          </select>
                                        </div>
                                        <div class="col-sm-2" style="padding: 0px;margin-top: 7px;">
                                          <a href="javascript:void(0);" class="btn btn-danger right" id="reload_reservation_order"><i class="fa fa-refresh"></i></a>
                                        </div>
                                      </div>
                                      <div class="bill-theme-con" style="height:440px;overflow-y: auto;background-image: url('../..//assets/img/reskin/bg-table.jpg');">
                                          <div id="table-bill-list" style="">
                                              <table class="bill-table" style="border-bottom:1px solid #e1e1e1;">
                                                  <thead>
                                                  <tr>
                                                      <th style="width:45%;text-align:left;">Menu</th>
                                                      <th style="width:15%;text-align:right;">Jumlah</th>
                                                      <th style="width:35%;text-align:right;">Harga</th>
                                                      <th style="width:5%;text-align:right;padding-right:10px;"></th>
                                                  </tr>
                                                  </thead>
                                                  <tbody>
                                                    <?php echo $order_list; ?>
                                                  </tbody>
                                              </table>
                                          </div>
                                          <table class="total-payment">
                                              <tbody>
                                              </tbody>
                                          </table>
                                      </div>
                                  </div>
                              </div>
                          </div><!-- /.col-md-4 -->
                       </div>
                      </div>
                    </div>
                  </div> 
                  <div class="form-group">
                    <div class="text-center">
                          <button  id="edit_reservasi" name="btn_action" value="save"
                                  class="btn btn-std-yellow"><?php echo $this->lang->line('ds_submit_save'); ?>
                          </button>
                          <button id="edit_exit_reservasi" name="btn_action" value="save_exit"
                                  class="btn btn-std-yellow">
                              <?php echo $this->lang->line('ds_submit_save_exit'); ?>
                          </button>
                          <a href="<?php echo base_url('reservation'); ?>"
                             class="btn btn-std"><?php echo $this->lang->line('ds_submit_cancel'); ?></a>
                  </div>
                    <!-- /.table-responsive -->
                </div>
	
    </div>
    <!-- End container fluid -->
</div>
<!-- End page wrapper -->
<?php echo form_hidden('id', $form_data->id); ?>
<?php echo form_close(); ?>

<script data-main=" <?php echo base_url('assets/js/main-cashier'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>