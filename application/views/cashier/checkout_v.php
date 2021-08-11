<!-- popup -->
<div class="popup-block" style="display:none;">
    <div class="popup-order">
        <div class="col-lg-12">
            <form id="form-input-order">
                <div class="title-bg">
                    <h4 class="title-name menu-name"></h4>
                    <input type="hidden" id="menu_id_selected" value="">
                    <input type="hidden" id="menu_price_selected" value="">
                    <input type="hidden" id="menu_order_id_selected" value="">
                    <input type="hidden" id="menu_cooking_status" value="">
                    <input type="hidden" id="is_already_process" value="">
                    <input type="hidden" id="price-menu" value="0">
                    <input type="hidden" id="discount-name" value="">
                    <input type="hidden" id="promo_cc_id" value="">
                    <input id="temp_total_ordered" name="temp_total_ordered" type="hidden" value='0'/>
                    
                </div>
                <div class="popup-order-panel">
                    <div class="dark-theme-con"
                         style="float:left;width:50%;height:310px;overflow-x: hidden;overflow-y: auto">
                        <table class="acc-table" style="width: 100%">
                            <tbody>
                            <tr>
                                <td><?php echo $this->lang->line('ds_lbl_amount'); ?>
                                <td>
                                <td>
                                    <div class="input-group">

                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default btn-number" data-type="minus"
                                                    data-field="quantity">
                                                <span class="glyphicon glyphicon-minus"></span>
                                            </button>
                                        </span>
                                        <input type="text" name="quantity" class="form-control input-number count-order"
                                               value="1"
                                               min="1" max="1000">
                                      <span class="input-group-btn">
                                          <button type="button" class="btn btn-default btn-number" data-type="plus"
                                                  data-field="quantity">
                                              <span class="glyphicon glyphicon-plus"></span>
                                          </button>
                                      </span>
                                    </div>
                                <td>
                            </tr>
                            <tr>
                                <td colspan="4"><?php echo $this->lang->line('ds_lbl_option'); ?>
                                <td>
                            </tr>
                            <tr>
                                <td class="menu-option" colspan="4">
                                <td>
                            </tr>
                            <tr>
                                <td colspan="4"><?php echo $this->lang->line('ds_lbl_side_dish'); ?>
                                <td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <table class="side-dish"></table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="order-note">
                        <p><?php echo $this->lang->line('ds_lbl_notes'); ?></p>
                        <textarea class="form-control order-notes" style="resize:none;height:274px;"></textarea>
                    </div>

    <div class="clearfix"></div>

                    <button type="reset" class="btn btn-std btn-cancel"
                            style="float:right;margin-right:10px;"><?php echo $this->lang->line('ds_submit_cancel'); ?>
                    </button>
                    <button class="btn btn-std btn-save"
                            style="float:right;"><?php echo $this->lang->line('ds_submit_save'); ?></button>
                    <button  id = "btn-discount-single" 
                            style="float:right;"
                            class="btn btn-std " >Diskon</button>
                    <button class="btn btn-std btn-delete-order"
                            style="float:right;"><?php echo $this->lang->line('ds_btn_delete'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- popup diskon -->
<div class="popup-discount" style="display:none;">
    <input type="hidden" id="is_single_discount" value="1" />

    <div class="discount-content">
        <div class="col-lg-12   ">
            <form id="form-input-order">
                <div class="title-bg">
                    <h4 class="title-name">Pilih Diskon</h4>
                    
                </div>
                <div class="popup-order-panel">
                    <div class="dark-theme-con" style="height:310px;padding-top: 10px;">
                               
                                <table class="table">
                                    <tr>
                                        <td>Subtotal:</td>
                                        <td id="subtotal-price-discount" data-price='0' colspan="2">0</td>
                                    </tr>
                                     <tr>
                                        <td>Diskon:</td>
                                        <td>% <input type="text" id="input-discount-percent" class="form-control" value='0'></td>
                                        <td>Rp <input type="text" id="input-discount-amount" class="form-control" value='0'/></td>
                                    </tr>
                                    
                                     <tr>
                                        <td>Pilih Diskon</td>
                                        <td colspan="2">
                                            <select class="form-control" id="ddl_discount">
                                                <?php
                                                    echo "<option value='0' data-discount='0' data-type='none'>Tidak diskon</option>";
                                                if(isset($all_discount) && sizeof($all_discount)>0){
                                                  foreach ($all_discount as $key => $row) {
                                                      if($row->is_percentage == 1){
                                                          echo "<option data-name='".$row->name."' data-discount='".$row->value."' data-type='percent' value='".$row->id."'>".$row->name." (".$row->value."%)</option>";

                                                      }else{
                                                          echo "<option data-name='".$row->name."' data-discount='".$row->value."' data-type='amount' value='".$row->id."'>".$row->name." (Rp ".number_format($row->value,0,"",".").")</option>";

                                                      }
                                                  }
                                                }
                                                    echo "<option id='other-discount' value='other'>Lainnya</option>";;

                                                ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr id="form-discount-name" style="display:none">
                                        <td>Nama Diskon</td>
                                        <td colspan="2"><input type="text" id="input-discount-name" class="form-control"/></td>
                                    </tr>

                                </table>
                       
                    </div>
                     <button type="reset" class="btn btn-std btn-cancel"
                            style="float:left;margin-right:10px;"><?php echo $this->lang->line('ds_submit_cancel'); ?>
                    </button>
                    <button type="reset" class="btn btn-std btn-save-discount"
                            style="float:left;margin-right:10px;">Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="new-order-checkout" class="popup-block-custom" style="display:none;">
    <div class="popup-order-custom">
        <div class="col-lg-12">
            <div class="col-lg-12">
                <div class="title-bg">
                    <h4 class="title-name"><?php echo $this->lang->line('ds_nav_category'); ?></h4>
                </div>
                <div class="dark-theme-con">
                    <div class="row">
                        <div class="col-lg-12" style="padding: 0 30px">
                            <div
                                style="height: 190px;display: block;overflow-x: scroll;overflow-y:hidden;white-space: nowrap">
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="title-bg">
                    <h4 class="title-name category-name">Menu</h4>
                </div>
                <div class="dark-theme-con" style="height: 200px">
                    <div class="col-lg-12" style="height:164px;overflow-x:hidden;overflow-y: auto">
                        <div class="row container-menus">
                            <?php if (!empty($menus)) {
                                foreach ($menus as $menu) {
                                    $image = base_url('assets/img/default-menus.jpg');
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
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="footer-bg">
                    <button class="btn btn-std btn-cancel-new-order"
                            style="margin-left:10px;"><?php echo $this->lang->line('ds_submit_cancel'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="popup-block" style="display:none;" id="single-bill">
	<div class="bill-preview-container">
		 <div class="col-lg-4 col-lg-offset-4">
            <div class="col-lg-12">
				<div class="row">
					<div class="title-bg">
						<h4 class="title-name">Print Bill</h4>
					</div>
					<div class="dark-theme-con" style="height: 500px;padding:10px;">
						<div class="col-lg-12 bill-preview">
							<h4 class="header-bill">
								<?php
                                  /*if (!empty($data_store)) {
                                        echo $data_store->store_name;
                                    } */

                                ?><br>Bandung<br>Telp. (022) 1234556
							</h4>
							<table class="table-bill-preview">
								<thead>
									<tr><th colspan="2" style="text-align:center"><?php
                                    /*$data_order_mode = explode(",", $order_mode);
                                    echo $data_order_mode[0];*/
                                    ?></th></tr>
								</thead>
								<tbody>
									<tr>
										<td>Jumlah : <?php //echo $data_order_mode[1]; ?></td>
										<td style="text-align:right"><?php //echo $staff_mode;?></td>
									</tr>
									<tr>
										<td>Rcpt# : 00001</td>
										<td style="text-align:right"><?php //echo date("d/m/y H:i")?></td>
									</tr>
								</tbody>
							</table>
							<div class="bill-counting">
							<table class="table">
								<tbody>
									<tr>
										<td>
											<table class="table-bill-preview" id="list-order">
												<tbody>
												 
												</tbody>
											</table>
										</td>
									</tr>
									  
									<tr>
										<td>
										 
                                             <table class="table-bill-preview" id="single-bill-print">
                                                <tbody>
                                                
                                                </tbody>
                                            </table>
										</td>
									</tr>
									<!-- <tr>
										<td>
											<table class="table-bill-preview">
												<tbody>
													<tr>
														<td style="width:10%;"></td>
														<td style="width:60%;font-size:16px;font-weight:bold;">Grand Total</td>
														<td style="width:30%;text-align:right;font-size:16px;font-weight:bold;" id="grand-total">Rp <?php echo $total_price;?></td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr> -->
									<tr>
										<td style="text-align:center">Terima Kasih</td>
									</tr>
								</tbody>
							</table>
							</div>
						</div>
						<div class="button-bottom-container">
							<a href="#" class="btn btn-std pull-right btn-cancel">Batal</a>
							<a href="#" class="btn btn-std pull-right"><i class="fa fa-print"></i> Print Bill</a>
						</div>
					</div>
				</div>
            </div>
        </div>
	</div>
</div>

<div class="popup-block popup-input" style="display:none;">
	<div class="col-lg-4 col-lg-offset-4 input-payment-method">
		<div class="col-lg-12">
			<div class="row">
            <div class="title-bg-popup">
                    <a  class="btn btn-std btn-cancel btn-distance" style="float:right"> X </a></div>
				<div class="title-bg title-bg-member">
                
					<h4 class="title-popup">Input Data Kartu Kredit</h4>
				</div>
        <form action="" method="post" id="form-payment">
          <div class="dark-theme-con" style="display:table;width:100%;padding:10px;">
            <div id="select-promo-cc" style="<?php echo (sizeof($promo_cc)==0 ? "display:none;" : "") ?>">
							<span ><b>Promo Kartu Kredit :</b></span>
							<?php
								echo form_dropdown('promo_cc', $promo_cc,"", 'id="promo_cc" field-name = "Promo" autocomplete="off" class="form-control"');
							?>
						</div>
            <div id="select-bank" style="display:none;">
              <span ><b>BANK :</b></span>
              <!---<select class="form-control" id="ddl_bank" style="margin-bottom:15px;">-->
                  <?php
                  // foreach ($bank as $key => $row) {
                     // echo "<option data-account-id='".$row->account_id."' value='".$row->id."'>".$row->bank_name."</option>";                                
                  // }
                  ?>
                  <?php
                    echo form_dropdown('ddl_bank', $bank,"", 
                    'id="ddl_bank" field-name = "bank" 
                    class="form-control" autocomplete="on" style="margin-bottom:15px;"');
                  ?>
              <!--</select>-->
            </div>
            <div id="select-bank-account-card">
              <span><b>JENIS KARTU :</b></span>
              <?php
                echo form_dropdown('bank_account_card_id', $bank_account_card,"", 
                'id="ddl_bank_account_card" field-name = "jenis kartu" 
                class="form-control" autocomplete="on" style="margin-bottom:15px;"');
              ?>
            </div>
            <span id="subtitle"><b>NOMOR KARTU :</b></span>
            <input class="form-control" type="text" name="value" id="value"/>
            <input class="form-control" type="hidden" name="confirm_type" id="confirm_type"/>
            <div class="col-lg-12">
              <div class="row">
                <div class="button-wrapper">
                  <a href="#" class="btn btn-std btn-cancel btn-distance">Batal</a>
                  <a href="#" class="btn btn-std btn-distance" id="btn-ok-input">OK</a>
                </div>
              </div>
            </div>
          </div>
        </form>
			</div>
		</div>
	</div>
</div>
<div class="popup-block popup-input-flazz" style="display:none;">
	<div class="col-lg-4 col-lg-offset-4 input-payment-method">
		<div class="col-lg-12">
			<div class="row">
            <div class="title-bg-popup">
                    <a  class="btn btn-std btn-cancel btn-distance" style="float:right"> X </a></div>
				<div class="title-bg title-bg-member">
					<h4 class="title-popup">Input Data Kartu Flazz</h4>
				</div>
        <form action="" method="post" id="form-payment">
          <div class="dark-theme-con" style="display:table;width:100%;padding:10px;">
						<div id="select-bank" >
              <span><b>BANK :</b></span>
							<?php
								echo form_dropdown('ddl_bank', $bank,"", 
								'id="ddl_flazz_bank" field-name = "bank" 
								class="form-control" autocomplete="on" style="margin-bottom:15px;"');
							?>
            </div>
            <div id="select-bank-account-card">
              <span><b>JENIS KARTU :</b></span>
              <?php
                echo form_dropdown('bank_account_card_id', $bank_account_card,"", 
                'id="ddl_flazz_bank_account_card" field-name = "jenis kartu" 
                class="form-control" autocomplete="on" style="margin-bottom:15px;"');
              ?>
            </div>
            <span id="subtitle"><b>NOMOR KARTU :</b></span>
            <input class="form-control only_numeric" maxlength="16" type="text" name="value_flazz" id="value_flazz"/>
            <input class="form-control" type="hidden" name="confirm_type_flazz" id="confirm_type_flazz"/>
            <div class="col-lg-12">
              <div class="row">
                <div class="button-wrapper">
                  <a href="#" class="btn btn-std btn-cancel btn-distance">Batal</a>
                  <a href="#" class="btn btn-std btn-distance" id="btn-ok-input-flazz">OK</a>
                </div>
              </div>
            </div>
          </div>
        </form>
			</div>
		</div>
	</div>
</div>
<div class="popup-block popup-voucher" style="display:none;">
    <div class="col-lg-4 col-lg-offset-4 input-payment-method">
        <div class="col-lg-12">
            <div class="row">
            <div class="title-bg-popup">
                    <a  class="btn btn-std btn-cancel btn-distance" style="float:right"> X </a></div>
                <div class="title-bg title-bg-member">
                    <h4 class="title-popup">Input Data Voucher</h4>
                </div>
                <form action="" method="POST" id="form-payment">
                <div class="dark-theme-con" style="display:table;width:100%;padding:10px;">
                    <div  >
                        <span ><b>Daftar Voucher :</b></span>
                        <select id="voucher_category" name="voucher_category" class="form-control">
                            <?php foreach($voucher_categories as $voucher_category){?>
                                <option value="<?php echo $voucher_category->id;?>"><?php echo $voucher_category->name;?></option>
                            <?php }?>
                        </select> 
                    </div>                   
                     <div  >
                        <span ><b>Jumlah Voucher :</b></span> 
                        <input type="text" name="voucher_quantity" id="voucher_quantity" class="form-control qty-input" value="1">
                    </div> 
                    <input class="form-control" type="hidden" name="value" id="value"/>
 
                    <input class="form-control" type="hidden" name="confirm_type" id="confirm_type"/>
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="button-wrapper">
                                <a href="#" class="btn btn-std btn-cancel btn-distance">Batal</a>
                                <a href="#" class="btn btn-std btn-distance" id="btn-ok-voucher">OK</a>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

            </div>
        </div>
    </div>
</div>
<div class="popup-block popup-compliment" style="display:none;">
    <div class="col-lg-4 col-lg-offset-4 input-payment-method">
        <div class="col-lg-12">
            <div class="row">
            <div class="title-bg-popup">
                    <a  class="btn btn-std btn-cancel btn-distance" style="float:right"> X </a></div>
                <div class="title-bg title-bg-member">
                    <h4 class="title-popup"></h4>
                </div>
                <form action="" method="post" id="form-payment">
                <div class="dark-theme-con" style="display:table;width:100%;padding:10px;">
                    <div  >
                        <span ><b>Compliment User :</b></span>
                        <select class="form-control select2" id="compliment_code" style="margin-bottom:15px;" name="code">

                            <?php
                            
                            foreach ($compliments as $key => $row) { 
                               echo "<option value='".$row->user_id."'>".$row->name."</option>";                                
                            }
                            ?>
                        </select>
                    </div>                   
 
                    <input class="form-control" type="hidden" name="code" id="value"/>
 
                    <input class="form-control" type="hidden" name="confirm_type" id="confirm_type"/>
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="button-wrapper">
                                <a href="#" class="btn btn-std btn-cancel btn-distance">Batal</a>
                                <a href="#" class="btn btn-std btn-distance" id="btn-ok-compliment">OK</a>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

            </div>
        </div>
    </div>
</div>
<div class="popup-block popup-member" style="display:none;">
    <div class="col-lg-4 col-lg-offset-4 input-payment-method">
        <div class="col-lg-12">
            <div class="row">
            <div class="title-bg-popup">
                    <a  class="btn btn-std btn-cancel btn-distance" style="float:right"> X </a></div>
                <div class="title-bg title-bg-member">
                    <h4 class="title-popup">Diskon Member</h4>
                </div>
                <form action="" method="post" id="form-payment">
                <div class="dark-theme-con" style="display:table;width:100%;padding:10px;">
                                     

                    <span id="subtitle"><b>ID Member :</b></span>
                     <div id="select-member">
                        <input class="form-control" type="hidden" name="member_id_val" id="member_id_val"/>
                        <input class="form-control" type="text" name="search_member" id="search_member" readonly="true" style="cursor: text;" />

                        <!-- <select class="form-control select2" id="member_id_val" style="margin-bottom:15px;">
                            <?php
                            foreach ($non_employee_members as $key => $row) {
                               echo "<option data-account-id='".$row->id."' value='".$row->id."'>".$row->name." - ".$row->member_id." - ".$row->birth_date." - ".$row->email." - ".$row->mobile_phone."</option>";                                
                            }
                            ?>
                        </select> -->

                    </div> 
                    <input class="form-control" type="hidden" name="confirm_type" id="confirm_type"/>
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="button-wrapper">
                                <a  class="btn btn-std btn-cancel btn-distance">Batal</a>
                                <a class="btn btn-std" id="btn-ok-member">OK</a>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

            </div>
        </div>
    </div>
</div>

<div class="popup-block popup-data-member" style="display:none;">
    <div class="col-lg-4 col-lg-offset-4 input-payment-method" style="width: 50%; margin-left: 25%;">
        <div class="col-lg-12">
            <div class="row">
                <div class="title-bg-popup">
                    <a class="btn btn-std btn-cancel-search-member btn-distance" style="float:right">X</a>
                </div>
                <div class="title-bg title-bg-member">
                    <h4 class="title-popup">Data Member</h4>
                </div>
                <div class="dark-theme-con" style="display:table;width:100%;padding:10px;">

                <table class="table table-striped table-bordered table-hover dt-responsive" cellspacing="0" width="100%" id="datatable-data-member">
                        <thead>
                        <tr>
                            <th>Nama</th>
                            <th>ID Member</th>
                            <th>Tgl. Lahir</th>
                            <th>Domisili</th>
                            <th>Email</th>
                            <th>No. Handphone</th>
                            <th style="text-align: center; width:11%">Aksi</th>
                        </tr>
                        </thead>
                    </table>
                    <input type="hidden" id="dataProcessUrl"
                        value="<?php echo $data_url ?>"/>

                    <div class="col-lg-12">
                        <div class="row">
                            <div class="button-wrapper">
                                <a class="btn btn-std btn-cancel-search-member btn-distance">Batal</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="popup-block popup-order-company" style="display:none;">
    <div class="col-lg-4 col-lg-offset-4" style="margin-top:50px;">
        <div class="col-lg-12">
            <div class="row">
            <div class="title-bg-popup">
                    <a  class="btn btn-std btn-cancel btn-distance" style="float:right"> X </a></div>
                <div class="title-bg title-bg-member">
                    <h4 class="title-popup"></h4>
                </div>
                <form action="" method="post" id="form-payment">
                <div class="dark-theme-con" style="display:table;width:100%;padding:10px;">
                 <div>

                      <!-- Nav tabs -->
                      <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" data-id="company"  class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Perusahaan</a></li>
                        <li role="presentation" data-id="employee" ><a href="#profile" aria-controls="profile"  role="tab" data-toggle="tab">Karyawan</a></li>
                      
                      </ul>

                      <!-- Tab panes -->
                      <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="home"  style="padding:10px;">
                            <?php
                            if(!empty($order_company)){
                                echo form_dropdown('order_company', $order_company, 
                               "", 
                                'id="order_company" field-name = "Order" 
                                class="form-control" autocomplete="on"');
                            }
                               
                            ?>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="profile" style="padding:10px;">
                             <?php
                                echo form_dropdown('employees', $employees, 
                               "", 
                                'id="employees" field-name = "Employees" 
                                class="form-control" autocomplete="on"');
                            ?> 
                        </div>
                        
                      </div>

                    </div>
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="button-wrapper">
                                <a href="#" class="btn btn-std btn-cancel btn-distance">Batal</a>
                                <a  class="btn btn-std btn-distance btn-ok-input" id="">Ok</a>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

            </div>
        </div>
    </div>
</div>

<div class="popup-block" style="display:none;" id="popup-confirm">
	<div class="col-lg-4 col-lg-offset-4 input-payment-method">
		<div class="col-lg-12">
			<div class="row">
            <div class="title-bg-popup">
                    <a  class="btn btn-std btn-cancel btn-distance" style="float:right"> X </a></div>
				<div class="title-bg">
					<h4 class="title-popup">Konfirmasi</h4>
				</div>
				<div class="dark-theme-con" style="display:table;width:100%;padding:10px;" >
                    <div id="popup-confirm-content">
                        <table class="table-bill-preview">
                            <tr>
                                <td>Nama</td>
                                <td>:</td>
                                <td>Joni</td>
                            </tr>
                            <tr>
                                <td>Benefit</td>
                                <td>:</td>
                                <td>Diskon 10%</td>
                            </tr>
                    </table>
                    </div>
					
					<div class="col-lg-12">
						<div class="row">
						<a href="#" class="btn btn-std pull-right btn-cancel">Batal</a>
						<a href="#" class="btn btn-std pull-right" id="btn-ok-confirm"><i class="fa fa-check"></i> OK</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
 

<div class="popup-block" style="display:none;" id="custom-bill">
    <div class="bill-preview-container">
         <div class="col-lg-4 col-lg-offset-4">
            <div class="col-lg-12">
                <div class="row">
                <div class="title-bg-popup">
                    <a  class="btn btn-std btn-cancel btn-distance" style="float:right"> X </a></div>
                    <div class="title-bg">
                        <h4 class="title-popup">Konfirmasi</h4>
                    </div>
                    <div class="dark-theme-con" style="height: 500px;padding:10px;">
                        <div class="col-lg-12 bill-preview">
                            <h4 class="header-bill">
                                <?php
                                  /*if (!empty($data_store)) {
                                        echo $data_store->store_name;
                                        echo '<br>'.$data_store->store_address.'
                                        <br>'.$data_store->store_phone;
                                    } */

                                ?>
                            </h4>
                            <table class="table-bill-preview">
                                <thead>
                                    <tr><th colspan="2" style="text-align:center">Meja <?php
                                    /*$data_order_mode = explode(",", $order_mode);
                                    echo $data_order_mode[0];*/
                                    ?></th></tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Jumlah : <?php //echo $data_order_mode[1]; ?></td>
                                        <td style="text-align:right"><?php //echo $staff_mode;?></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td style="text-align:right"><?php //echo date("d/m/y H:i")?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="bill-counting">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td>
                                            <table class="table-bill-preview" id="custom-bill-order">
                                                <tbody>
                                                  
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <table class="table-bill-preview" id="custom-bill-print">
                                                <tbody>
                                                  <?php if (!empty($order_bill)) {
                                                        echo $order_bill;
                                                    } ?>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                  
                                    <!-- <tr>
                                        <td>
                                            <table class="table-bill-preview">
                                                <tbody>
                                                    <tr>
                                                        <td style="width:10%;"></td>
                                                        <td style="width:60%;font-size:16px;font-weight:bold;">Grand Total</td>
                                                        <td style="width:30%;text-align:right;font-size:16px;font-weight:bold;" id="grand-total"> </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr> -->
                                    <tr>
                                        <td style="text-align:center">Terima Kasih</td>
                                    </tr>
                                </tbody>
                            </table>
                            </div>
                        </div>
                       
                        <div class="button-bottom-container">
                            <a href="#" class="btn btn-std pull-right btn-cancel">Batal</a>
                            <a href="#" class="btn btn-std pull-right"><i class="fa fa-print"></i> OK & Print Bill</a>
                            <a href="#" class="btn btn-std pull-right" id="print-checkout-bill"><i class="fa fa-print"></i>Print Bill</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- <div class="popup-block" style="display:none;" id="custom-bill">
	<div class="bill-preview-container">
		 <div class="col-lg-8 col-lg-offset-2">
            <div class="col-lg-12">
				<div class="row">
					<div class="title-bg">
						<h4 class="title-name">Konfirmasi</h4>
					</div>
					<div class="dark-theme-con" style="height: 495px;padding:10px;">
						<div class="col-lg-4 bill-preview">
							<h4 class="header-bill">
								<?php
                                  /*if (!empty($data_store)) {
                                        echo $data_store->store_name;
                                        echo '<br>'.$data_store->store_address.'
                                        <br>'.$data_store->store_phone;
                                    } */

                                ?>
							</h4>
							<table class="table-bill-preview">
                                <thead>
                                    <tr><th colspan="2" style="text-align:center">Meja <?php
                                    /*$data_order_mode = explode(",", $order_mode);
                                    echo $data_order_mode[0];*/
                                    ?></th></tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Jumlah : <?php //echo $data_order_mode[1]; ?></td>
                                        <td style="text-align:right"><?php echo $staff_mode;?></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td style="text-align:right"><?php //echo date("d/m/y H:i")?></td>
                                    </tr>
                                </tbody>
                            </table>
							<div class="bill-counting">
							<table class="table">
								<tbody>
									<tr>
										<td>
											<table class="table-bill-preview" id="custom-bill-order">
												<tbody>
                                                  
                                                </tbody>
											</table>
										</td>
									</tr>
									<tr>
										<td>
											<table class="table-bill-preview" id="custom-bill-print">
												<tbody>
                                                  <?php //if (!empty($order_bill)) {
                                                        //echo $order_bill;
                                                   // } ?>
                                                </tbody>
											</table>
										</td>
									</tr>
								  
									<!-- <tr>
										<td>
											<table class="table-bill-preview">
												<tbody>
													<tr>
														<td style="width:10%;"></td>
														<td style="width:60%;font-size:16px;font-weight:bold;">Grand Total</td>
														<td style="width:30%;text-align:right;font-size:16px;font-weight:bold;" id="grand-total"> </td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr> 
									<tr>
										<td style="text-align:center">Terima Kasih</td>
									</tr>
								</tbody>
							</table>
							</div>
						</div>
						<div class="col-lg-8">
							<div class="col-lg-12 voucher-container">
								<h4>Bonus Voucher</h4>
								<form class="form-horizontal">
									<div class="form-group">
										<label for="inputVoucher1" class="control-label col-xs-4" style="text-align:left">Voucher Rp 10.000</label>
										<label class="control-label col-xs-4">Kode :</label>
										<div class="col-xs-4">
											<input type="email" class="form-control" id="inputVoucher1" placeholder="Email">
										</div>
									</div>
									<div class="form-group">
										<label for="inputPassword" class="control-label col-xs-4" style="text-align:left">Voucher Rp 10.000</label>
										<label class="control-label col-xs-4">Kode :</label>
										<div class="col-xs-4">
											<input type="password" class="form-control" id="inputPassword" placeholder="Password">
										</div>
									</div>
								</form>
							</div>
							<div class="col-lg-12 voucher-container">
								<h4>Bonus Voucher</h4>
								<form class="form-horizontal">
								<div class="form-group">
									<label for="inputVoucher1" class="col-xs-4" style="text-align:left">Bonus Poin :</label>
									<label class="col-xs-4">15</label>
								</div>
								</form>
							</div>
						</div>
						<div class="button-bottom-container">
							<a href="#" class="btn btn-std pull-right btn-cancel">Batal</a>
							<a href="#" class="btn btn-std pull-right"><i class="fa fa-print"></i> OK & Print Bill</a>
						</div>
					</div>
				</div>
            </div>
        </div>
	</div>
</div> -->
<!-- end popup -->

<?php
  $this->load->view('partials/navigation_v');
?>

<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="order-wrapper">

            <div class="col-md-4">
                <div class="row">
                    <div style="margin-left:15px;margin-right: 15px;<?php echo ($data_order->is_delivery!=1 ? "" : "visibility:hidden;"); ?>">
                        <div class="title-bg-custom" style="position: relative">
                            <h4 class="title-name left"><?php echo $this->lang->line('ds_nav_orders') ?></h4>
							<!--
                            <?php if($data_order->is_delivery!=1): ?>
                              <?php if($this->groups_access->have_access('pending_bill')): ?>
                              <button type="reset" title="Pending Bill Perusahaan/Karyawan" class="btn btn-trans btn-option-list right" feature_confirmation="<?php echo ($feature_confirmation['pending_bill']) ?>" data-url="<?php echo base_url('cashier/get_company_member') ?>" id="btn-company"><i class="fa fa-credit-card"></i></button>
                              <?php endif; ?>
                              <?php if($this->groups_access->have_access('compliment_bill')): ?>
                              <button type="reset" title="Compliment" class="btn btn-trans btn-option-list right" feature_confirmation="<?php echo ($feature_confirmation['compliment_bill']) ?>" data-url="<?php echo base_url('cashier/get_user_compliment') ?>" id="btn-compliment"><i class="fa fa-gift"></i></button>
                              <?php endif; ?>
                              <?php if($this->groups_access->have_access('member_bill')): ?>
                              <button type="reset" title="Member" class="btn btn-trans btn-option-list right" feature_confirmation="<?php echo ($feature_confirmation['member_bill']) ?>" id="member-payment"  data-url="<?php echo base_url('cashier/get_discount_member') ?>" ><i class="fa fa-user"></i></button>
                              <?php endif; ?>
                              <?php if($this->groups_access->have_access('bon_bill')): ?>
                              <button type="reset" title="Bon Bill" class="btn btn-trans btn-option-list right" feature_confirmation="<?php echo ($feature_confirmation['bon_bill']) ?>" id="bon-payment"><i class="fa fa-money"></i></button>
                              <?php endif; ?>
                            <?php endif; ?>
							-->
                        </div>
                        <div class="bill-theme-con bill-theme-cus" style="height:500px;">
                            <div id="table-bill-list-checkout" style="height: 232px;overflow-y: auto">
                                <table class="bill-table" id="bill-table-left" style="border-bottom:1px solid #e1e1e1;">
                                    <thead>
                                    <tr>
                                        <th style="width:50%;text-align:left;padding-left:10px;">Menu</th>
                                        <th style="width:20%;text-align:right;">Jumlah</th>
                                        <th style="width:30%;text-align:right;padding-right:10px;">Harga</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (!empty($order_list)) {
                                        echo $order_list;
                                    } ?>
                                    </tbody>
                                </table>
                            </div>
							<div class="bill-mover">
								<button class="btn btn-splitter right splitter-border-1" id="split-all-right" ><i class="fa fa-angle-double-right fa-2x rotate-down"></i></button>
                <?php if($this->groups_access->have_access('split_bill')): ?>
								<button title="Split Bill" class="btn btn-splitter right splitter-border-2" id="split-single-right" feature_confirmation="<?php echo ($feature_confirmation['split_bill']) ?>"><i class="fa fa-angle-right fa-2x rotate-down"></i></button>
                <?php endif; ?>
							</div>
              <div style="height: 220px;overflow-y: auto;padding-top:10px;">
                            <table class="total-payment" id="total-payment-left" >
                                <tbody>
                                <?php if (!empty($order_bill)) {
                                    echo $order_bill;
                                } ?>
                                </tbody>
                            </table>
                           
              </div>
                        </div>
                    </div>
                </div>
            </div>
			
			<div class="col-md-4">
                <div class="row">
                    <div style="margin-left:15px;margin-right: 15px">
                        <div class="title-bg-custom" style="position: relative">
                            <h4 class="title-name left">Bayar</h4>                            
                            <button type="reset" title="Print Preview Bill" class="btn btn-trans btn-option-list right btn-preview-bill" data-type='custom'
                                    ><i class="fa fa-print"></i>&nbsp&nbspPrint Preview Bill</button>
							
							<div class="promo-select">
                                
                                <?php
                                echo form_dropdown('promo_id', $promo_id,"", 'id="promo_id" field-name = "Store"   autocomplete="off" class="form-control"');
                                ?>
                            </div>
                        </div>
                        <div class="bill-theme-con bill-theme-cus" style="height:500px;">
                            <div id="" style="height: 232px;overflow-y: auto">
                                <table class="bill-table" id="bill-table-right" style="border-bottom:1px solid #e1e1e1;">
                                    <thead>
                                    <tr>
                                        <th style="width:50%;text-align:left;padding-left:10px;">Menu</th>
                                        <th style="width:20%;text-align:right;">Jumlah</th>
                                        <th style="width:30%;text-align:right;padding-right:10px;">Harga</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php 
                                    // if (!empty($order_list)) {
                                    //     echo $order_list;
                                    // } 
                                    ?>
                                    </tbody>
                                </table>
                            </div>
							<div class="bill-mover">&nbsp;
                <?php if($data_order->is_delivery!=1): ?>
								<button class="btn btn-splitter left splitter-border-2" id="split-all-left"><i class="fa fa-angle-double-left fa-2x rotate-up"></i></button>
                <?php if($this->groups_access->have_access('split_bill')): ?>
								<button title="Split Bill" class="btn btn-splitter left splitter-border-1" id="split-single-left" feature_confirmation="<?php echo ($feature_confirmation['split_bill']) ?>"><i class="fa fa-angle-left fa-2x rotate-up"></i></button>
                <?php endif; ?>
                <?php endif; ?>
              </div>
                            <div style="height: 220px;overflow-y: auto;padding-top:10px;">
                                <table class="total-payment" id="total-payment-right" style="">
                                    <tbody>
                                    <?php if (!empty($order_bill)) {
                                        echo $order_bill;
                                    } ?>
                                    </tbody>
                                </table>
                            </div>
                         
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div style="margin-left:15px;margin-right: 15px">
                        <div class="title-bg-custom">
                            <div class="col-xs-8">
                                <div class="title-name left">Pembayaran (<?php echo $order_name; ?>)</div>
                            </div>
							<div class="col-xs-4 right">
								<p class="role-info text-right">Cashier</p>
								<p class="role-name text-right"><?php echo $staff_mode; ?></p>
							</div>
                        </div>
                        <div class="dark-theme-con" style="height:500px;">
							<div class="panel-info" style="display:none">
								<div class="col-xs-8">
									<p class="role-info text-left"><?php echo $order_mode; ?></p>
									<p class="role-name text-left"><?php echo $order_name; ?></p>
								</div>
								<div class="col-xs-4">
									<p class="role-info text-right">Cashier</p>
									<p class="role-name text-right"><?php echo $staff_mode; ?></p>
								</div>
							</div>
                            <div class="col-lg-12">
                                <div class="row">
                                    <label for="input-payment" class="sr-only"></label>
                                    <input id="input-payment" name="input-payment" class="form-control input-pin pBill "
                                           type="text" pattern="\d*" autocomplete="off" novalidate/>

                                    <div class="clearfix"></div>
									<div class="calc-button-container">
										<p class="cc-text pull-left" id="text-code"></p>
										<p class="sisa-text pull-right">Sisa : <b class="payment-sisa">Rp 0</b></p>
									</div>
                                    <div class="calc-button-co" style="overflow:hidden;">
										<div class="calc-button-container" style="display:none">
                                            <button id="" class="btn btn-cc pull-left" ><i class="fa fa-credit-card"></i></button>
											<p class="cc-text">Kartu Kredit :</p>
                                        </div>
										<div class="calc-button-container" style="display:">
											
                                        </div>
										
										<div class="calc-button-container" style="margin-bottom:10px;">
											
											  <?php if($this->groups_access->have_access('pending_bill')): ?>
											  <button style="margin:0px 1% 0px 0px !important" class="btn btn-metode pBill btn-option-list" type="reset" title="Pending Bill Perusahaan/Karyawan"  feature_confirmation="<?php echo ($feature_confirmation['pending_bill']) ?>" data-url="<?php echo base_url('cashier/get_company_member') ?>" id="btn-company">Pending</button>
											  <?php endif; ?>
											  <?php if($this->groups_access->have_access('compliment_bill')): ?>
											  <button style="margin:0px 1% 0px 0px !important" class="btn btn-metode pBill btn-option-list" type="reset" title="Compliment" feature_confirmation="<?php echo ($feature_confirmation['compliment_bill']) ?>" data-url="<?php echo base_url('cashier/get_user_compliment') ?>" id="btn-compliment">Cmplmnt</button>
											 <?php endif; ?>
											  <?php if($this->groups_access->have_access('member_bill')): ?>
											  <button style="margin:0px 1% 0px 0px !important" class="btn btn-metode pBill btn-option-list" type="reset" title="Member" feature_confirmation="<?php echo ($feature_confirmation['member_bill']) ?>" id="member-payment"  data-url="<?php echo base_url('cashier/get_discount_member') ?>" >Member</button>
											  <?php endif; ?>
											  <?php if($this->groups_access->have_access('bon_bill')): ?>
											  <button style="margin:0px 1% 0px 0px !important" class="btn btn-metode pBill btn-option-list" type="reset" title="Bon Bill" feature_confirmation="<?php echo ($feature_confirmation['bon_bill']) ?>" id="bon-payment">Bon Bill</button>
											  <?php endif; ?>
											
											<button type="reset" title="Print Preview Bill" class="btn btn-metode pBill btn-preview-bill" style="margin-right:0;" data-type='custom'>PRINT</button>
										</div>
										
										<div class="calc-button-container">
                      <button id="cash-payment" class="btn btn-metode pBill active" >Cash</button>
											<button id="credit-payment" class="btn btn-metode pBill">Credit</button>
											<button id="debit-payment" class="btn btn-metode pBill">Debit</button>
											<button id="flazz-payment" class="btn btn-metode pBill">Flazz</button>
                      <button id="voucher-payment" class="btn btn-metode" style="margin-right:0;" data-url="<?php echo base_url('cashier/get_voucher_detail') ?>">Voucher</button>
											<!-- <button id="pending-bill-print" class="btn btn-metode">Voucher</button> -->
                                            <!-- <button id="reset-payment" class="btn btn-metode">Member Point</button> -->
                                            <!--<button  class="btn btn-metode">Member Point</button>-->
										</div>
										<div class="clearfix" style="margin-bottom:10px;"></div>
                                        
										<div class="calc-button-container">
                                            <div style="width:55%;float:left;">
												<?php $number = array(7, 8, 9, 4, 5, 6, 1, 2, 3);
												for($i = 0; $i < count($number); $i++):?>
													<button class="btn btn-calc pBill number" data-value="0"><?php echo $number[$i];?></button>
												<?php endfor ?>
												<button class="btn btn-calc pBill btn-exactly" style="font-size:16px;">Pas</button>
												<button class="btn btn-calc pBill number">0</button>
												<button class="btn btn-calc pBill number">00</button>
											</div>
											<div style="width:45%;float:left;">
												<button class="btn btn-calc-direct pBill clearNumber">C</button>
												<button class="btn btn-calc-direct pBill deleteNumber clear-margin-right"><i class="fa fa-long-arrow-left"></i></button>
												
												<button class="btn btn-calc-direct pBill btn-blue" data-value="100000">100k</button>
												<!--<button class="btn btn-calc-direct pBill btn-blue" data-value="2000">2k</button>-->
												<button class="btn btn-calc-direct pBill btn-blue clear-margin-right" data-value="50000">50k</button>
												<!--<button class="btn btn-calc-direct pBill btn-blue" data-value="1000">1k</button>-->
												<button class="btn btn-calc-direct pBill btn-blue" data-value="20000">20k</button>
												<!--<button class="btn btn-calc-direct pBill btn-blue" data-value="500">500</button>-->
												<button class="btn btn-calc-direct pBill btn-blue clear-margin-right" data-value="10000">10k</button>
												<!--<button class="btn btn-calc-direct pBill btn-blue" data-value="200">200</button>-->
												<!--<button class="btn btn-calc-direct pBill btn-blue" data-value="5000">5k</button>-->
												<!--<button class="btn btn-calc-direct print-bill btn-blue" data-value="100">100</button>-->
												<button class="btn btn-calc pBill payment-ok">=</button>
												<button id="done-payment" data-type="done" class="btn btn-done" disabled="disabled">OK</button>
											</div>
											<!--
                                            <div class="clearfix"></div>
                                            <div class="clearfix"></div>
                                            <button class="btn btn-calc pBill number100">100k</button>
                                            <button id="print-bill" class="btn btn-calc pBill btn-metode" disabled="disabled">Print</button>
                                            <div class="clearfix"></div>
											<div class="clearfix" style="margin-bottom:10px;"></div>
                                            <button id="btn-discount" class="btn btn-metode pBill " >Diskon</button>
											-->
                                        </div>
										
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            </div> <!-- End order wrapper -->

            <!-- End row -->
        </div>
    </div>
    <!-- End row -->
</div>
<!-- End container fluid -->
</div>
<!-- End page wrapper -->
<input id="less_payments" type="hidden" value="<?php echo $this->lang->line('ds_less_payments'); ?>"/>
<input id="store_id" type="hidden" value="<?php echo $store_id?>"/>
<input id="total_tax" type="hidden" value="<?php echo $total_tax; ?>"/>
<input id="nearest_round" type="hidden" value="<?php echo $nearest_round; ?>"/>
<input id="is_round_up" type="hidden" value="<?php echo $is_round_up; ?>"/>
<input id="error_card_number" type="hidden" value="<?php echo $this->lang->line('ds_card_number'); ?>"/>
<input id="order_id" name="order_id" type="hidden" value="<?php echo $order_id ?>"/>
<input id="is_checkout" name="is_checkout" type="hidden" value="1"/>
<input id="is_delivery" name="is_delivery" type="hidden" value="<?php echo $data_order->is_delivery; ?>"/>
<input id="delivery_cost_id" name="delivery_cost_id" type="hidden" value="<?php echo $data_order->delivery_cost_id; ?>"/>
<input id="reservation_id" name="reservation_id" type="hidden" value="<?php echo $data_order->reservation_id; ?>" down_payment="<?php echo (sizeof($reservation)>0 ? $reservation->down_payment : 0 ); ?>" />

<input id="voucher_method" name="voucher_method" type="hidden" value="<?php echo $setting["voucher_method"]?>" />
<input id="tax_service_method" name="tax_service_method" type="hidden" value="<?php echo $setting["tax_service_method"]?>" />

<?php
  $delivery_cost=$data_order->delivery_cost;
?>
<input id="delivery_cost" name="delivery_cost" type="hidden" value="<?php echo $delivery_cost; ?>"/>
<script data-main=" <?php echo base_url('assets/js/main-cashier'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>