<link href="<?php echo base_url(); ?>assets/css/bootstrap-datetimepicker.css" rel="stylesheet">
<?php if(sizeof($data_order)>0): ?>
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
                    <div id="select-bank" style="display:none;">
                        <span ><b>BANK :</b></span>
                        <select class="form-control" id="ddl_bank" style="margin-bottom:15px;">
                            <?php
                            foreach ($bank as $key => $row) {
                               echo "<option data-account-id='".$row->account_id."' value='".$row->id."'>".$row->bank_name."</option>";                                
                            }
                            ?>
                        </select>
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
                    <h4 class="title-popup">Input Voucher</h4>
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
 
                    <input class="form-control" type="hidden" name="value" id="value"/>
 
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
                     <div id="select-bank"  >
                        <input class="form-control" type="hidden" name="member_id_val" id="member_id_val"/>
                        <input class="form-control" type="text" name="search_member" id="search_member" readonly="true" style="cursor: text;" /> 
                        <!-- <select class="form-control select2" id="member_id_val" style="margin-bottom:15px;">
                            <?php
                            foreach ($non_employee_members as $key => $row) {
                               echo "<option data-account-id='".$row->id."' value='".$row->member_id."'>".$row->name." - ".$row->member_id."</option>";                                
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
    <div class="col-lg-4 col-lg-offset-4 input-payment-method">
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
<?php endif; ?>
<?php
  $this->load->view('partials/navigation_v');
?>

<div id="page-wrapper">
	<div class="col-lg-12 order-header">
                <div class="col-sm-3">
					<div class="row">
						<div class="resto-info-mini">
							<div class="resto-info-pic">
							
							</div>
							<div class="resto-info-name">
								<?php echo $data_store->store_name; ?>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-9">
					<div class="row">
						<div class="margin-wrap">
							<div class="panel-info">
								<div class="col-sm-8" style="margin-bottom: 9px;">
								  <form method="post" action="<?php echo base_url("cashier/refund") ?>">
									<p class="role-info text-left">Refund : Input 4 Digit Terakhir Nomor Bill</p>
									<div class="col-sm-4" style="padding-left: 0px;">
										<div class='input-group date' id='refund_date'>
										  <input type="text" class="form-control" name="refund_date" onkeydown="return false" value="<?php echo $refund_date ?>"> 
										  <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
										</div>
									</div>
									<div class="col-sm-5" style="padding-left:0px;">
									  <input type="text" name="receipt_number" class="form-control" placeholder="Input 4 Digit Terakhir Nomor Bill" value="<?php echo $receipt_number ?>">
									</div>
									<div class="col-sm-1" style="padding-left: 0px;">
									  <button type="submit" href="javascript:void(0);" class="btn btn-primary btn-mini">Cari Data</button>
									</div>
									<div class="clearfix"></div>
								  </form>
								</div>
								<div class="col-xs-4">
									<p class="role-info text-right">Cashier</p>
									<p class="role-name text-right"><?php echo $staff_mode; ?></p>
								</div>
							</div>
						</div>
					</div>
				</div>
      <div class="clearfix"></div>
      <?php if($message): ?>
      <center><div class="alert alert-danger text-center col-sm-offset-4 col-sm-4" align="center"><?php echo $message; ?></div></center>
      <?php endif; ?>
    </div>
    <?php if(sizeof($data_order)>0): ?>

        <div class="col-md-4" style="display:none;">
            <div class="row">
                <div style="margin-left:15px;margin-right: 15px;visibility:hidden;">
                    <div class="title-bg-custom" style="position: relative">
                        <h4 class="title-name left"><?php echo $this->lang->line('ds_nav_orders') ?></h4>
                        
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
          <div style="height: 220px;overflow-y: auto">
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
        <div class="col-md-8">
            <div class="row">
                <div style="margin-left:15px;margin-right: 15px">
                    <div class="title-bg-custom" style="position: relative">
                        <h4 class="title-name left">Bayar</h4>
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
                        <button type="reset" title="Print Preview Bill" class="btn btn-trans btn-option-list right btn-preview-bill" data-type='custom'><i class="fa fa-print"></i></button>
                        
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
                            <button class="btn btn-splitter left splitter-border-2" style="display:none;" id="split-all-left"><i class="fa fa-angle-double-left fa-2x rotate-up"></i></button>
                            <button title="Split Bill" class="btn btn-splitter left splitter-border-1" id="split-single-left" feature_confirmation="<?php echo ($feature_confirmation['split_bill']) ?>"><i class="fa fa-angle-left fa-2x rotate-up"></i></button>
          </div>
                        <div style="height: 220px;overflow-y: auto">
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
                        <h4 class="title-name left">Pembayaran</h4>
                    </div>
                    <div class="dark-theme-con" style="height:500px;">
                        <div class="col-lg-12">
                            <div class="row">
                                <label for="input-payment" class="sr-only"></label>
                                <input id="input-payment" name="input-payment" class="form-control input-pin pBill "
                                       type="text" pattern="\d*" autocomplete="off" novalidate/>

                                <div class="clearfix"></div>
                                <div class="calc-button-container">
                                    <p class="cc-text pull-left" id="text-code"></p>
                                    <p class="sisa-text pull-right">Sisa : <b class="payment-sisa">Rp. 0</b></p>
                                </div>
                                <div class="calc-button-co" style="overflow:hidden;">
                                    <div class="calc-button-container" style="display:none">
                                        <button id="" class="btn btn-cc pull-left" ><i class="fa fa-credit-card"></i></button>
                                        <p class="cc-text">Kartu Kredit :</p>
                                    </div>
                                    <div class="calc-button-container" style="display:">
                                        
                                    </div>
                                    <div class="clearfix" ></div>
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

    <?php endif; ?>
    <!-- End row -->
</div>
<!-- End page wrapper -->
<?php if(sizeof($data_order)>0): ?>
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
<input id="is_refund" type="hidden" value="<?php echo $bill->receipt_number ?>" />
<input id="voucher_method" name="voucher_method" type="hidden" value="<?php echo $setting["voucher_method"]?>" />
<input id="tax_service_method" name="tax_service_method" type="hidden" value="<?php echo $setting["tax_service_method"]?>" />

<?php
  $delivery_cost=$data_order->delivery_cost;
?>
<input id="delivery_cost" name="delivery_cost" type="hidden" value="<?php echo $delivery_cost; ?>"/>
<?php endif; ?>
<script data-main=" <?php echo base_url('assets/js/main-cashier'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>