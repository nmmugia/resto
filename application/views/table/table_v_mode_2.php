<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<style>
  @media(min-width:775px){
    .dine-in-order{
      /*width: 19% !important;*/
      height: 150px !important;
      float:left;
      position:relative;
      margin:5px;
    }
  }
</style>
<!--Add the following script at the bottom of the web page (before </body></html>)-->
<script type="text/javascript" async="async" defer="defer" data-cfasync="false" src="https://mylivechat.com/chatinline.aspx?hccid=97374916"></script>
<div class="popup-block" id="popup-customer-count" style="display:none;">
    <div class="popup-table">
        <div class="col-lg-12">
            <div class="title-bg title-bg-customer" style="border-radius: 10px 10px 0px 0px;">
                <h4 class="title-popup"><b><?php echo $this->lang->line('ds_lbl_guest_amount'); ?></b></h4>
                <!-- <h4 class="title-name"><?php echo $this->lang->line('ds_lbl_choose_guest_amount'); ?></h4>-->
            </div>
            <div class="popup-panel" style="text-align: center;height:auto;display:table;">
                <div class="popup-button-co" style="height:auto;display:table;">
                    <?php for ($i = 1; $i < 13; $i++) { ?>
                        <a class="btn btn-lite new_order" href="<?php echo base_url('table/new_order'); ?>"
                           data-guest="<?php echo $i ?>"><p
                                style="margin-right: 2px; margin-top:10px;"><?php echo $i ?></p></a>
                    <?php } ?>
						<!--  <a class="btn btn-lite new_order reservation-active" href="<?php echo base_url('table/new_order'); ?>"
                           data-guest="<?php echo $i ?>"><p
                                style="margin-right: 2px; margin-top:5px;"><?php echo $i ?></p></a> -->
                    <input type="hidden" id="reservation_status" value=""/>
                    <input type="hidden" id="reservation_id" value=""/>
                    <input type="hidden" id="new_table_id" value=""/>
                    <input type="hidden" id="new_order_url" value="<?php echo base_url('table/order_dine_in'); ?>"/>
                </div>
                <button class="btn btn-std btn-cancel-new-order"
                        style="width:50%;margin-top:10px;"><?php echo $this->lang->line('ds_submit_cancel'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="popup-block" id="popup-open-close" style="display:none;">
    <div class="popup-table">
        <div class="col-lg-12">
            <div class="title-bg title-bg-customer">
                <h4 class="title-popup"><b>Cashier Closed</b></h4>
            </div>
            <div class="popup-panel" style="text-align: center;height:auto;display:table;">                
                <h4 class="title-popup"><b>Silahkan Open Cashier untuk memulai transaksi</b></h4>
                <button class="btn btn-std btn-cancel-new-order"
                        style="width:50%;margin-top:20px;"><?php echo $this->lang->line('ds_submit_cancel'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="popup-block" id="popup-block-reservation-detail" style="display:none;">
    <div class="col-lg-9 col-lg-offset-2" style="margin-top:20px;">
        <div class="col-lg-12">
            <div class="title-bg">
                <h4 class="title-name"><b>Reserved</h4>
                <h4 class="title-name"><?php echo $this->lang->line('ds_lbl_choose_guest_amount'); ?></h4>
            </div>
            <div class="popup-panel" style="text-align: center;height:auto;display:table;">
                <div class="popup-button-co" id="reservation-detail" style="height:auto;display:table;">
                    <a class="col-xs-4" href="#">
					<div class="col-xs-12 btn btn-reservation-menu">
						<p class="reservation-menu-title">Datang</p>
						<table class="table-reservation-option">
							<tbody>
								<tr>
									<td>Nama</td>
									<td>:</td>
									<td>Budi</td>
								</tr>
								<tr>
									<td>Kontak</td>
									<td>:</td>
									<td>0123 4556 1234</td>
								</tr>
								<tr>
									<td>Jumlah</td>
									<td>:</td>
									<td>5 Orang</td>
								</tr>
								<tr>
									<td>Waktu</td>
									<td>:</td>
									<td>12 Sep 2015, 13:00</td>
								</tr>
							</tbody>
						</table>
					</div>
					</a>
					<a class="col-xs-4" href="#">
						<div class="col-xs-12 btn btn-reservation-menu" style="padding-top:55px;">
							<p class="reservation-menu-title">Tidak Datansssg</p>
							<p>Isi meja dengan tamu lain</p>
						</div>
					</a>
					<a class="col-xs-4" href="#">
						<div class="col-xs-12 btn btn-reservation-menu" style="padding-top:75px;">
							<p class="reservation-menu-title">Hapus Status Reserved</p>
						</div>
					</a>
                </div>
              
                <div class="col-lg-12">
                  <button class="btn btn-std btn-close-reserv"
                  style="width:50%;margin-top:10px;"><?php echo $this->lang->line('ds_submit_cancel'); ?></button>

              </div>

            </div>
        </div>
    </div>
</div>

<div class="popup-block" id="popup-reservation-note" style="display:none;">
    <div class="col-lg-6 col-lg-offset-3" style="margin-top:50px;">
        <div class="col-lg-12">
            <div class="title-bg">
                <h4 class="title-name"><b>Hapus Reservasi</h4>
            </div>
            <form id="form-reservation-note" action="" method="post">
            <div class="popup-panel" style="height:auto;display:table;">
                <div class="popup-button-co" id="reservation-note" style="height:auto;display:table;">
                    
                    <div class="form-group">
                        <div class="col-xs-12">
                            <div class="row">
                                <div class="checkbox">
                                    <div class="col-xs-10">
                                        <label class="radio-inline">
                                            <input type="radio">Pelanggan Tidak Bisa Dihubungi</label>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <div class="row">
                                <div class="checkbox">

                                     <div class="col-xs-12">
                                        <label class="radio-inline col-xs-12">
                                            <input type="radio">
                                            <textarea class="form-control" style="resize:none"></textarea>
                                        </label>
                                    </div>

                                
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </form>

                <div class="col-lg-12" align="center">
                    <button class="btn btn-std btn-cancel-new-order"
                        style="width:150px;margin-top:10px;">Batal</button>
                    <button class="btn btn-std btn-save-replace-reserv" data-status=""
                        style="width:150px;margin-top:10px;"><i class="fa fa-save"></i> Simpan</button>
                </div>
            </div>
        </div>
    </div>

</div>


<!-- end popup -->
<?php
  $this->load->view('partials/navigation_v');
?>

<div id="page-wrapper">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <div class="row">
          <div class="dine-in-container">
            <div id="inner_container" style="<?php echo ($data_open_close->status!=1 ? "visibility:hidden;" : "") ?>">
              <div class="col-xs-2 responsive-option">
                <div class="table-sidemenu">
                  <div class="resto-option">
                    <div class="resto-pic">
                      <img class="resto-pic" src="<?php echo base_url($store_logo); ?>">
                    </div>
                    <div class="resto-floor">
                      <div class="col-xs-2">
                        <div class="row">
                          <a href="<?php echo base_url('table/change_floor'); ?>" data-id="prev"
                             class="btn-prev btn-change-floor"><i class="fa fa-angle-left fa-2x"></i></a>
                        </div>
                      </div>
                      <div class="col-xs-8">
                         <p id="floor_name"><?php if (!empty($floor_name)) {
                            echo $floor_name;
                          } ?></p>
                        <input type="hidden" id="floor_default_id" value="<?php echo $floor_id; ?>"/>
                        <input type="hidden" id="floor_id" value="<?php echo $floor_id; ?>"/>
                      </div>
                      <div class="col-xs-2">
                        <div class="row">
                            <a href="<?php echo base_url('table/change_floor'); ?>" data-id="next"
                             class="btn-next btn-change-floor"><i class="fa fa-angle-right fa-2x"></i></a>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-xs-12 responsive-move">
                    <div class="row">
                      
                      <?php if($this->groups_access->have_access('change_table')): ?>
                      <a href="#" class="btn btn-big-glass btn-change-table" feature_confirmation="<?php echo ($feature_confirmation['change_table']) ?>">
                        <span class="option-text">Pindahkan Meja</span>
                        <span class="option-icon"><i class="fa fa-share-square"></i></span>
                      </a>
                      <?php  endif ?>
                      
                      <?php if($this->groups_access->have_access('merge_table')): ?>
                      <a href="#" class="btn btn-big-glass btn-merge-table" feature_confirmation="<?php echo ($feature_confirmation['merge_table']) ?>">
                        <span class="option-text">Gabungkan Meja</span>
                        <span class="option-icon"><i class="fa fa-chain"></i></span>
                      </a>
                      <a href="#" class="btn btn-big-glass btn-ok-merge" style="display:none;">
                        <span class="option-text">Gabungkan</span>
                        <span class="option-icon"><i class="fa fa-thumbs-up"></i></span>
                      </a>
                      <?php  endif ?>
                      
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xs-10 responsive-list-menu">
                  <!-- Mode List Table -->
                  <?php 
                  if($this->session->userdata('view_table_list')=='')
                  {
                      $display = 'display:none;';
                  }
                  else{
                      $display = 'display:block;';
                  }
                  ?>
                  <div id="table-view-list" class="col-lg-6 col-lg-offset-3" style="z-index:1;margin-top:20px;position:relative;<?php echo $display;?>">
                      <!-- <div class="badge-table">
                          1
                      </div> -->
                      <div class="table-list-container">
                          <div class="floot-list-container left">
                              <h3 class="title-list-text">Lantai</h3>
                              <?php if (!empty($floors)) {
                                  echo  '<ul class="floor-list">';
                                  foreach ($floors as $floor) {
                                      echo '<li><a href="' . base_url('table/change_floor') . '" class="btn-change-floor" data-id="'.$floor->id.'" style="display:block;">'.$floor->floor_name.'</a></li>';
                                  }
                                  echo '</ul>';
                             } ?>
                                  
                          </div>
                          <div id="table-list" class="table-list left">
                              <h3 class="title-list-text">Meja</h3>
                              <div id="wrap-list-table">
                              <?php       if (!empty($data_table)) {
                                
                                  foreach ($data_table as $table) {

                                     $new_style ='';
                                     $merge_badge ='';
                                     $custom_data ='';
                                    // if($table->parent_id!=0)
                                    // {
                                      // $merge_badge = '<div class="badge-table-small">'.$table->parent_name.'</div>';
                                      // $custom_data = 'data-parent-id="' . $table->parent_id . '" ';  
                                    // }else{
                                      // $custom_data = 'data-parent-id="0"';                                    
                                    // }
                                    if(isset($table->is_merged->parent_id) && $table->is_merged->parent_id!=0)
                                    {
                                      $merge_badge = '<div class="badge-table-small">'.$table->is_merged->parent_name.'</div>';
                                      $custom_data = 'data-parent-id="' . $table->is_merged->parent_id . '" ';  
                                    }else{
                                      $custom_data = 'data-parent-id="0"';                                    
                                    }

                                     $custom_data .= 'data-connect_to_reservation="' . $table->connect_to_reservation . '" ';
                                     $custom_data .= 'data-reservation-id="' . $table->reservation_id . '" ';
                                     $custom_data .= 'data-table-id="' . $table->table_id . '" ';
                                     $custom_data .= 'data-table-status="' . $table->id . '" ';
                                     $custom_data .= 'data-order-id="' . $table->order_id . '" ';
                                     $custom_data .= 'data-customer-count="' . $table->customer_count . '" ';
                                     $custom_data .= 'data-table-name="' . $table->table_name . '" ';
                                     $data_table_list ='';
                                     $list_style ='float:left;';
                                     $shape = 'table-list-text label-rect-' . $table->status_name;
                                     $data_table_list .= '<a href="#"  id="list_layout_' . $table->table_id . '" '.$custom_data.' class="' . $shape . '" style="'.$list_style.'">' . $table->table_name;

                                     if($table->status_unavailable > 0 && ($table->status_name!='empty' && $table->status_name !='select' ))
                                     {
                                      $data_table_list.= '<div class="warning-table-small"></div>';
                                      }


                                     if(!empty($table->is_parent))
                                     // if($table->is_parent!=0)
                                     {
                                      $data_table_list .=  '<div class="badge-table-small">'.$table->table_name.'</div>';
                                      }

                                  $data_table_list .= $merge_badge;

                                  $data_table_list .='</a>';
                                  echo $data_table_list;
                                  }
                              } 
                              ?>
                              </div>

                          </div>
                      </div>
                  </div>
                  

                  <input type="hidden" id="all_table_empty" value="<?php echo $all_table_empty; ?>"/>
                  <?php
                  if($this->session->userdata('view_table_list')=='')
                  {
                      $display_table = 'display:block;';
                  }
                  else{
                      $display_table = 'display:none;';
                  }
                  echo '<div id="table-parent" style="'.$display_table.' position:relative;margin:0 auto;width: ' . $default_table_width . ';">';
                  if (!empty($data_table)) {
                      foreach ($data_table as $table) {
                        switch ($table->table_shape) {
                            case "labeledTriangle":
                                $shape = 'table-mini dine-in-order label-triangle-' . $table->status_name;
                                break;
                            case "labeledRect":
                                $shape = 'table-mini dine-in-order label-rect-' . $table->status_name;
                                break;
                            case "labeledCircle":
                                $shape = 'table-mini dine-in-order label-circle-' . $table->status_name;
                                break;
                            default:
                                $shape = 'table-mini dine-in-order label-rect-' . $table->status_name;
                        }
                        $new_style ='';
                        $merge_badge ='';
                        $custom_data ='';
                        // if($table->parent_id!=0)
                        // {
                          // $merge_badge = '<div class="badge-table">'.$table->parent_name.'</div>';                                   
                          // $custom_data = 'data-parent-id="' . $table->parent_id . '" ';
                        // }else{
                          // $custom_data = 'data-parent-id="0"';                                    
                        // }
                        if(!empty($table->is_merged))
                        {
                          $merge_badge = '<div class="badge-table">'.$table->is_merged->parent_name.'</div>';                                   
                          $custom_data = 'data-parent-id="' . $table->is_merged->parent_id . '" ';
                        }else{
                          $custom_data = 'data-parent-id="0"';                                    
                        }

                        $new_style .= 'width: 23.95%;';
                        $new_style .= 'height:50px;';
												$custom_data .= 'data-connect_to_reservation="' . $table->connect_to_reservation . '" ';
                        $custom_data .= 'data-reservation-id="' . $table->reservation_id . '" ';
                        $custom_data .= 'data-table-id="' . $table->table_id . '" ';
                        $custom_data .= 'data-table-status="' . $table->id . '" ';
                        $custom_data .= 'data-order-id="' . $table->order_id . '" ';
                        $custom_data .= 'data-customer-count="' . $table->customer_count . '" ';
                        $custom_data .= 'data-table-name="' . $table->table_name . '" ';

                        if($table->status_name=="select"){
													$custom_data.='feature_confirmation="'.$feature_confirmation['reservation'].'"';
												}
                        echo '<div id="tab_layout_' . $table->table_id . '" ' . $custom_data . ' class="' . $shape . '" style="' . $new_style . '">';
                        if($table->status_unavailable > 0 && ($table->status_name!='empty' && $table->status_name !='select' ) )
                        {
                            echo '<div class="warning-table"></div>';
                        }

                        if(!empty($table->is_parent))
                        // if($table->is_parent!=0)
                        {
                             echo  '<div class="badge-table">'.$table->table_name.'</div>';
                           
                         }
                         
                         echo $merge_badge;


                         echo '<span class="v-middle">' . $table->table_name . '</span></div>';
                       }

                   }
                   else {
                      echo '<p style="text-align: center;margin-top:30px;font-size: 25px;font-weight: bold">' . $this->lang->line('ds_table_empty') . '</p>';
                  }
                  echo '</div>';
                  ?>
                  <!-- End Table Panel -->
              </div>                    
            </div>
          </div>
        </div>
      </div>
    </div>	
  </div>
</div>
<script data-main="<?php echo base_url('assets/js/main-table'); ?>" src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>