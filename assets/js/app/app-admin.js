/*
 * Created by alta falconeri on 12/15/2014.
 */

define([
    "jquery",
    "jquery-ui", 
    "app/app-hrd-dashboard",
    "chained",
    "metisMenu",
    "Morris",
    'datatables',
    "bootstrap",
    "datatables-bootstrap",
    "timepicker",
    "datepair",
    "datetimepicker",
    "highcharts",
    "select2",
    "qtip",
], function ($, ui,hrdDashboard) {
    return {
        nodeUrl                : $('#node_url').val(),
        socket                  : false,
        baseUrl                : $('#root_base_url').val(),
        serverBaseUrl          : $('#server_base_url').val(),
        storeID                : $('#store_id_config').val(),
        privateKey                : $('#private_key').val(),
        overlayUI              : $('#cover'),
        sidedishCount          : document.getElementById("sidedishCount") != null ? document.getElementById("sidedishCount").value : 0,
        optionsCount           : document.getElementById("optionsCount") != null ? document.getElementById("optionsCount").value : 0,
        optionsValueCount      : document.getElementById("optionsValueCount") != null ? document.getElementById("optionsValueCount").value : 0,
        printerKitchenCount   :  document.getElementById("printer_kitchen_count") != null ? document.getElementById("printer_kitchen_count").value : 0,
        printerCheckerCount   :  document.getElementById("printer_checker_count") != null ? document.getElementById("printer_checker_count").value : 0,
        printerCheckerKitchenCount   :  document.getElementById("printer_checker_kitchen_count") != null ? document.getElementById("printer_checker_kitchen_count").value : 0,
        dataOutlet             :document.getElementById("data_outlet") != null ? JSON.parse($("#data_outlet").val()) : 0,
        leftDataOrder :[],
        rightDataOrder:[],
        hrdDashboard:hrdDashboard,
        enumOrder        : {
            SINGLE      : 1,
            ALL         : 2
            
        },
        listAccounts:[],
        indexTotalExecuteSync:0,
        indexTotalFailedSync:0,
        listFailedSync:[],
        totalSync:41,
        initSocketIO        : function () {
          App.socket = io(App.nodeUrl,{
            'reconnectionAttempts': 2
          });
          App.socket.on('reconnect_failed', function () {
            console.log("error init initSocketIO");
          });
          App.socket.on('connected', function (data) {
            console.log("connected");
          });
        
        },
        init                   : function () {
            // try {
              // App.initSocketIO();
            // } catch (err) {
              // console.log("error init initSocketIO");
            // }
            App.overlayUI.show();
            App.buttonSubmitEvent();

            App.initFunc(App);
			
			      App.initEvent();
            App.initSetLeftOrder();
            App.FeatureSettingUI();

            App.topProductDashboard();
            App.hrdDashboard.init();

            App.reportReceiveStock();
            App.initListTransferFilter();
						$('#show_printer_format_1').qtip({
							 content: {
									text: $('#printer_format_1_image') 
							 },
							 style: {
								"max-width": "410px" 
							}, 
              position: {
                  at: 'top center',
                  my: 'bottom center'
              }
						});
						$('#show_printer_format_2').qtip({
							 content: {
									text: $('#printer_format_2_image') 
							 },
							 style: {
								"max-width": "410px" 
							},
              position: {
                  at: 'top center',
                  my: 'bottom center'
              }
						});
						$('#show_voucher_method_1').qtip({
							 content: {
									text: $('#voucher_method_1_image') 
							 },
							 style: {
								"max-width": "410px" 
							}
						});
						$('#show_voucher_method_2').qtip({
							 content: {
									text: $('#voucher_method_2_image') 
							 },
							 style: {
								"max-width": "410px" 
							}
						});
						$(document).on('click',"#filter_stock_opname_daily",function(e){
							$("#report_content").html("");
							e.preventDefault();
							var request = $.ajax({
								type    : "POST",
								url     : App.baseUrl + "admincms/stock_opname/get_summary_inventory_data",
								data    : {
									outlet_id:$("#formFilter #outlet_id").val(),
									inventory_id:$("#formFilter #inventory_id").val(),
								}
							});
							request.done(function (msg) {
								$("#report_content").html(msg);
							});
						});
						$(document).on('click',".process_inventory",function(){
							inventory_id=$(this).attr("inventory_id");
							$("#ip_inventory_id").val(inventory_id).select2();
							$('#process-inventory-modal').modal('show'); 
						});
						$(document).on('click',"#save-process-inventory",function(evt){
							var target = $('.result');
							target.html('');
							App.overlayUI.show();
							check=true;
							$('.requiredDropdown,.requiredDropDown').each(function () {
									if ($(this).val() == '0' || $(this).val()=='' || $(this).val()==undefined) {
											target.empty().html('<div class="alert alert-danger" role="alert">Anda harus memilih pilihan pada ' + $(this).attr('field-name') + '</div>');
											App.overlayUI.hide();
											// evt.preventDefault();
											check=false;
											// return false;
									}
							});

							$('.requiredTextField').each(function () {
									if ($(this).val() == '') {
											target.empty().html('<div class="alert alert-danger" role="alert">Bagian ' + $(this).attr('field-name') + ' dibutuhkan</div>');
											App.overlayUI.hide();
											// evt.preventDefault();
											check=false;
											// return false;
									}
							});
							if(check==true){
								el=this;
								data=$(this).parents("form").serialize();
								url=$(this).parents("form").attr("action");
								$.ajax({
									url:url,
									dataType:"JSON",
									data:data,
									type:"POST",
									success:function(response){
										App.overlayUI.hide();
										App.alert(response.message);
										if(response.status==false){
											$(el).find(".result").html(response.message);
										}else{
											$('#process-inventory-modal').modal('hide'); 
											
										}
									}
								});								
							}
							return false;
						});
            //chained dropdown
            $("#outlet_id_chained").chained("#store_id_chained");
            $('.filter_date').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'YYYY-MM-DD',
            });
            $('#side-menu').metisMenu();
            $(".select2").select2();
            $(window).bind("load resize", function () {
                topOffset = 50;
                width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
                if (width < 768) {
                    $('div.navbar-collapse').addClass('collapse');
                    topOffset = 100; // 2-row-menu
                } else {
                    $('div.navbar-collapse').removeClass('collapse');
                }

                height = (this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height;
                height = height - topOffset;
                if (height < 1) height = 1;
                if (height > topOffset) {
                    $("#page-wrapper").css("min-height", (height) + "px");
                }
            });
            // $(".use_kitchen").on('click',function(){
              // value=$(this).val();
              // $(".use_role_checker[value='0']").click()
              // if(value==1){
                // $(".use_role_checker").parents(".form-group:first").show();
              // }else{
                // $(".use_role_checker").parents(".form-group:first").hide();
                
              // }
            // });
            $("#form-opname-single .qty-input").on('keyup',function(){
              qty=parseFloat($(this).val());
              if(isNaN(qty))qty=0;
              stock_system=parseFloat($("#form-opname-single #jumlah_stok").text());
              difference=qty-stock_system;
              if(difference>0){
                $("#form-opname-single .price input").val("");
                $("#form-opname-single .price").show();
              }else{
                $("#form-opname-single .price input").val("");
                $("#form-opname-single .price").hide();
              }
            });
            // $(".checkbox_outlet[already-checked]").on('click',function(){
              // value=$(this).is(":checked");
              // if(value==false){
                // $.ajax({
                  
                // });
              // }
            // });
            $("#stock_opname_all_table .qty").on('keyup',function(){
              qty=parseFloat($(this).val());
              if(isNaN(qty))qty=0;
              stock_system=parseFloat($(this).parents("tr").find(".stock_system").val());
              difference=qty-stock_system;
              if(difference>0){
                $(this).parents("tr").find(".price").val("").show();
              }else{
                $(this).parents("tr").find(".price").val("").hide();
              }
            });
            $("#add_inventory_composition").click(function(){
              inventory_id=$("#parent_inventory_id").val();
              $.ajax({
                url:App.baseUrl + "admincms/inventory/add_inventory_composition",
                type:"POST",
                data:{inventory_id:inventory_id},
                dataType:"JSON",
                success:function(response){
                  $("#inventory_composition tbody").append(response.content);
                  $("#inventory_composition tbody tr:last .select2").select2();
                }
              });
            });
            $(document).on("click",".remove_inventory_composition",function(){
              $(this).parents("tr").remove();
            });
            $(document).on("change",".detail_inventory_id",function(){
              inventory_id=$(this).val();
              el=this;
              if(inventory_id!=""){
                $.ajax({
                  url:App.baseUrl+"admincms/inventory/get_inventory_uoms",
                  type:"POST",
                  dataType:"JSON",
                  data:{inventory_id:inventory_id},
                  success:function(response){
                    $(el).parents("tr").find(".detail_uom_id").html(response.content);
                  }
                });
              }else{
                $(this).parents("tr").find(".detail_uom_id").html("<option value=''>Pilih Satuan</option>");
                $(this).parents("tr").find(".select2").select2();
              }
            });
            $("#convertion_inventory_id").change(function(){
              value=$(this).val();
              if(value!="0"){
                $.ajax({
                  url:App.baseUrl+"admincms/inventory_convertions/add_detail_convertion",
                  type:"POST",
                  dataType:"JSON",
                  data:{inventory_id:value},
                  success:function(response){
                    $("#inventory_convertion_uom tbody").html(response.content);
                  }
                })
              }else{
                $("#inventory_convertion_uom tbody").html("");  
              }
            });
            $(".target_type").change(function(){
              target_type=$(this).val();
              if(target_type==1){
                $("#target_by_total").val("");
                $("#target_by_total_html").show();
                $("#target_detail_table tbody").html("");
                $("#target_by_item_html").hide();
              }else{
                $("#target_by_total").val("");
                $("#target_by_total_html").hide();
                $("#target_detail_table tbody").html("");
                $("#target_by_item_html").show();
              }
            });
            $("#add_target_menu").click(function(){
              menu_html=$("#tmp").html();
              content='<tr>'+
              '<td>'+menu_html+'</td>'+
              '<td><input type="text" name="detail[target_qty][]" class="form-control only_numbers"></td>'+
              '<td><a href="javascript:void(0);" class="btn btn-danger remove_target_detail">Hapus</a></td>'
              '</tr>'+
              $("#target_detail_table tbody").append(content);
              $("#target_detail_table tbody tr:last select").select2();
            });
            $(document).on("click",".remove_target_detail",function(){
              $(this).parents("tr").remove();
            });
            $("#add_reward_kitcen").on('click',function(){
              $.ajax({
                url:App.baseUrl + "admincms/target_settings/get_reward_kitchen",
                dataType:"JSON",
                success:function(response){
                  if(response.data!=""){
                    $("#outlet_kitchen").val(response.data.outlet_id);
                    $("#kitchen_reward").val(response.data.reward);
                    $("#calculate_to_payroll").val(response.data.calculate_to_payroll);
                  }
                }
              });
            })
            $(document).on('click',"#save-kitchen-reward",function(){
              outlet_kitchen=$("#outlet_kitchen").val();
              if(outlet_kitchen==undefined)outlet_kitchen="";
              kitchen_reward=parseFloat($("#kitchen_reward").val()); 
              if(isNaN(kitchen_reward))kitchen_reward=0;
              calculate_to_payroll=$("#calculate_to_payroll").val();
              $.ajax({
                url:App.baseUrl + "admincms/target_settings/reward_kitchen",
                type:"POST",
                data:{reward:kitchen_reward,calculate_to_payroll:calculate_to_payroll,outlet_id:outlet_kitchen},
                success:function(){
                  $("#kitchen-reward-modal").modal("hide");
                }
              });
            });
            $("#stock_opname_outlet_id,#stock_opname_inventory_id").change(function(){
              var outlet_id=$("#stock_opname_outlet_id").val();
              var inventory_id=$("#stock_opname_inventory_id").val();
              App.overlayUI.show();
              $.ajax({
                url:App.baseUrl + "admincms/stock/get_adjustment_by_outlet",
                type:"POST",
                dataType:"JSON",
                data:{outlet_id:outlet_id,inventory_id:inventory_id},
                success:function(response){
                  $("#stock_opname_all_table tbody").html(response.content);
                  App.overlayUI.hide();
                }
              })
            });
						$(document).on("change","#spoiled_inventory_id",function(){
							inventory_id=$(this).val();
							el=this;
							if(inventory_id!=""){
								uom_id=$(this).find("option:selected").attr("uom_id");
								$.ajax({
									url:App.baseUrl+"admincms/inventory/get_inventory_uoms",
									type:"POST",
									dataType:"JSON",
									data:{inventory_id:inventory_id},
									success:function(response){
										$("#uom_id").html(response.content);
										$("#uom_id").val(uom_id);
										// $("#uom_id").select2();
									}
								});
							}else{
								$("#uom_id").html("<option value=''>Pilih Satuan</option>");
							}
						});
						$("#save-spoiled").on("click",function(){
              var outlet_id = $("#spoiled_outlet_id").val();
              var inventory_id = $("#spoiled_inventory_id").val();
              var uom_id = $("#uom_id").val();
              var quantity = $("#quantity").val();
              var description = $("#description").val();

              if(quantity.length === 0) {


                App.alert("Jumlah Stok harus diisi",function(){ return;}); 
              }else{
                var url = App.baseUrl+"admincms/spoiled/save_spoiled";
                  $.ajax({
                    url:url, 
                      type : 'POST',
                    data:{outlet_id:outlet_id,inventory_id:inventory_id,quantity:quantity,uom_id:uom_id,description:description},
                    success:function(response){
                          var parsedObject = JSON.parse(response);
                           App.alert(parsedObject.message);
                    }
                  }); 
              }
              
            });
						$("#trigger_filter_spoiled").on("click",function(){
              var inventory_id=$("#filter_inventory_id").val();
              var outlet_id=$("#filter_outlet_id").val();
              dataTables_spoiled.columns(0).search(outlet_id);
              dataTables_spoiled.columns(1).search(inventory_id).draw();
            });
            //datatables
            $('#dataTables-general-expenses').dataTable({
                "bProcessing": true,
                "bServerSide": true,
                "sServerMethod": "POST",
                "ajax": $('#dataProcessUrl').val(),
                "iDisplayLength": 10,
                "columns": [
                    {data: "name"},
                    {data: "description"},
                    {data: "amount"},
                    {data: "actions"}
                ],
                "columnDefs": [
                    {
                        "targets": 3,
                        "orderable": false,
                        "bSearchable": false,
                        "class": 'center-tr'
                    }
                ],
                "order": [[0, "desc"]]
            });
            $('#dataTables-delivery-cost').dataTable({
              "bProcessing"    : true,
              "bServerSide"    : true,
              "sServerMethod"  : "POST",
              "ajax"           : $('#dataProcessUrl').val(),
              "iDisplayLength" : 10,
              "columns"        : [
                {data : "delivery_cost_name"},
                {data : "delivery_cost"}
              ]
            });
            var dataTables_spoiled=$('#dataTables-spoiled').DataTable({
							"bProcessing"    : true,
              "bServerSide"    : true,
              "sServerMethod"  : "POST",
              "ajax"           : $('#dataProcessUrl').val(),
              "iDisplayLength" : 10,
              "columns"        : [
                {data : "outlet_name"},
                {data : "join_name"},
								{
									"data": "total_spoiled", // can be null or undefined
									"defaultContent": 0
								},
                {
                  "data": "cost_spoiled", // can be null or undefined
                  "defaultContent": 0
                },
              ],
              "columnDefs"     : [
                {
                  "targets"     : [2,3],
                  "orderable"   : false,
                  "bSearchable" : false,
                  "class"       : 'center-tr'
                }
              ]
            });
						$('#dataTables-category').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrl').val(),
                "iDisplayLength" : 10,
                "columns"        : [
                    {data : "id"},
                    {data : "category_name"},
                    {data : "outlet_name"},
                    {data : "store_name"},
                    {data : "actions"}

                ],
                "columnDefs"     : [
                    {
                        "targets"     : 0,
                        "visible"     : false,
                        "bSearchable" : false
                    },
                    {
                        "targets"     : 4,
                        "orderable"   : false,
                        "bSearchable" : false,
                        "class"       : 'center-tr'
                    }
                ],
                "order"          : [[0, "desc"]]

            });
            $('#dataTables-target-setting').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrl').val(),
                "iDisplayLength" : 10,
                "columns"        : [
                    {data : "name"},
                    {data : "target_type"},
                    {data : "reward"},
                    {data : "actions"}

                ],
                "columnDefs"     : [
                  {
                    "targets"     : 3,
                    "orderable"   : false,
                    "bSearchable" : false,
                    "class"       : 'center-tr'
                  }
                ]
            });
            $('#dataTables-inventory-convertion').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrl').val(),
                "iDisplayLength" : 10,
                "columns"        : [
                    {data : "inventory_name"},
                    {data : "actions"}

                ],
                "columnDefs"     : [
                  {
                    "targets"     : 1,
                    "orderable"   : false,
                    "bSearchable" : false,
                    "class"       : 'center-tr'
                  }
                ]
            });
            $('#dataTables-inventory').dataTable({
                "bProcessing": true,
                "bServerSide": true,
                "sServerMethod": "POST",
                "ajax": $('#dataProcessUrl').val(),
                "iDisplayLength": 10,
                "columns": [
                    {data: "name"},
                    {data: "price"},
                    {data: "unit"},
                    {data: "minimal_stock"},
                    {data: "actions"}


                ],
                "columnDefs": [
                    {
                        "targets": 4,
                        "orderable": false,
                        "bSearchable": false,
                        "class": 'center-tr'
                    }
                ],
                "order": [[0, "desc"]]

            });
            $('#dataTables-inventory-process').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrl').val(),
                "iDisplayLength" : 10,
                "columns"        : [
                    {data : "created_at"},
                    {data : "name"},
                    {data : "quantity"},
                    {data : "unit"},
                    // {data : "actions"}

                ],
                "columnDefs"     : [
                  {
                    "targets"     : 3,
                    "orderable"   : false,
                    "bSearchable" : false,
                    "class"       : 'center-tr'
                  }
                ],
                "order": [[0, "desc"]]
            });
            $('#dataTables-menus').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrl').val(),
                "iDisplayLength" : 10,
                "columns"        : [
                    {data : "id"},
                    {data : "menu_name"},
                    {data : "menu_price"},
                    {data : "outlet_name"},
                    {data : "category_name"},
                    {data : "actions"}

                ],
                "columnDefs"     : [
                    {
                        "targets"     : 0,
                        "visible"     : false,
                        "bSearchable" : false
                    },
                    {
                        "targets"     : 5,
                        "orderable"   : false,
                        "bSearchable" : false,
                        "class"       : 'center-tr'
                    }
                ],
                "order"          : [[0, "desc"]]

            });

            $('#dataTables-outlet').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrl').val(),
                "iDisplayLength" : 10,
                "columns"        : [
                    {data : "id"},
                    {data : "outlet_name"},
                    {data : "value"},
                    {data : "actions"}

                ],
                "columnDefs"     : [
                    {
                        "targets"     : 0,
                        "visible"     : false,
                        "bSearchable" : false
                    },
                    {
                        "targets"     : 3,
                        "orderable"   : false,
                        "bSearchable" : false,
                        "class"       : 'center-tr'
                    }
                ],
                "order"          : [[0, "desc"]]

            });
            var dataTables_purchase_order=$('#dataTables-purchase-order-list').DataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrl').val(),
                "iDisplayLength" : 10,
                "columns"        : [
                  {data : "number"},
                  {data : "order_at"},
                  {data : "supplier_name"},
                  {data : "description"},
                  {data : "actions"}
                ],
                "columnDefs"     : [
                  {
                    "targets"     : [4],
                    "orderable"   : false,
                    "bSearchable" : false,
                    "class"       : 'center-tr'
                  }
                ],
                "order"          : [[0, "desc"]]

            });
            $("#trigger_filter_po").on("click",function(){
              var supplier_id=$("#filter_supplier_id").val();
              var date=$(".filter_date input").val();
              column=$("#filter_supplier_id").data("target-column");
              dataTables_purchase_order.columns(column).search(supplier_id);
              column=$(".filter_date input").data("target-column");
              dataTables_purchase_order.columns(column).search(date).draw();
            });
            var dataTables_receive_purchase_order=$('#dataTables-receive-purchase-order-list').DataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrl').val(),
                "iDisplayLength" : 10,
                "columns"        : [
                  {data : "number"},
                  {data : "order_at"},
                  {data : "supplier_name"},
                  {data : "total_po"},
                  {data : "description"},
                  {data : "status"},
                  {data : "actions"}
                ],
                "columnDefs"     : [
                  {
                    "targets"     : 6,
                    "orderable"   : false,
                    "bSearchable" : false,
                    "class"       : 'center-tr'
                  },
                  {
                    "targets"     : 3,
                    "orderable"   : true,
                    "bSearchable" : false,
                    "class"       : 'center-tr'
                  }
                ],
                "order"          : [[0, "desc"]]
            });
            $("#trigger_filter_receive_po").on("click",function(){
              var supplier_id=$("#filter_supplier_id").val();
              var date=$(".filter_date input").val();
              column=$("#filter_supplier_id").data("target-column");
              dataTables_receive_purchase_order.columns(column).search(supplier_id);
              column=$(".filter_date input").data("target-column");
              dataTables_receive_purchase_order.columns(column).search(date).draw();
            });

            var dataTables_received_list=$('#dataTables-received-list').DataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrl').val(),
                "iDisplayLength" : 10,
                "columns"        : [
                  {data : "number"},
                  {data : "incoming_date"},
                  {data : "name"},
                  {data : "actions"}
                ],
                "columnDefs"     : [
                  {
                    "targets"     : [0,1,2],
                    "orderable"   : true,
                    "bSearchable" : true,
                    "class"       : 'left-tr'
                  }
                ],
                "order"          : [[1, "desc"]]
            });
            $("#trigger_filter_received_po").on("click",function(){
              var supplier_id=$("#filter_supplier_id").val();
              var start_date=$("#filter_start_date").val();
              var end_date=$("#filter_end_date").val();
              column=$("#filter_supplier_id").data("target-column");
              dataTables_received_list.columns(column).search(supplier_id);
              column=$("#filter_start_date").data("target-column");
              dataTables_received_list.columns(column).search(start_date).draw();
              column=$("#filter_end_date").data("target-column");
              dataTables_received_list.columns(column).search(end_date).draw();
            });

            // re print billing 
            $('#reprint_billing_start_date').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'YYYY-MM-DD HH:mm',
            });

            $('#reprint_billing_end_date').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'YYYY-MM-DD HH:mm',
            });
            $("#reprint_billing_start_date").on("dp.change", function (e) {

                $('#reprint_billing_end_date').datetimepicker({
                    sideBySide: true,
                    useCurrent: true,
                    format: 'YYYY-MM-DD HH:mm' 
                });
                
                $('#reprint_billing_end_date').data("DateTimePicker").minDate(e.date);

            }); 

             $("#reprint_billing_end_date").on("dp.change", function (e) {

                $('#reprint_billing_end_date').datetimepicker({
                    sideBySide: true,
                    useCurrent: true,
                    format: 'YYYY-MM-DD HH:mm' 
                });
                
                $('#reprint_billing_start_date').data("DateTimePicker").maxDate(e.date);

            });
            var reprint_billing_table=$('#table-reprint-billing').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "bFilter" :false,                   
                "bDestroy" :true,
                "autoWidth": false,
                "iDisplayLength" : 10,
                "ajax": {
                  "url": $('#dataProcessUrl').val(),
                  "type": 'POST',
                  "data": {
                    param: $('#formFilter').serialize()
                  }
                },
                "columns"        : [
                  {data : "payment_date"},
                  {data : "table_name"},
                  {data : "customer_name"},
                  {data : "receipt_number"},
                  {data : "order_type"},
                  {data : "total_price"},
                  {data : "customer_count"},
                  {data : "order_id"},
                  {data : "actions"}
                ],
                "columnDefs"     : [
                  {
                    "targets"     : [5,6,7],
                    "orderable" : false
                  }
                ],
              "order"          : [[0, "desc"]]
            });
            $("#reprint_billing_filter_submit").on('click', function (e) {
              $("#formFilter").removeAttr("action");
              $("#formFilter").removeAttr("target");
            });
            $("#reprint_billing_export_pdf").on('click', function (e) {
              $("#formFilter").attr("action",App.baseUrl + "admincms/reprint_billings/export_to_pdf");
              $("#formFilter").attr("target","_blank");
            });

            // $("#filter_submit").on('click', function (e) {
              //reprint_billing_table.refresh();
              // e.preventDefault();
            // });
            // end reprint billing
            $('#dataTables-stock-transfer-request').dataTable({
                "bLengthChange" : false,
                "bPaginate"     : false,
                "columnDefs"    : [
                    {
                        "targets"     : 4,
                        "orderable"   : false,
                        "bSearchable" : false,
                        "class"       : 'center-tr'
                    }
                ],
                "order"       : [[0, "desc"]]
            });

            $('#dataTables-stock-list').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrl').val(),
                "iDisplayLength" : 10,
                "columns"        : [
                    {data : "name"},
                    {data : "jumlah"},
                    {data : "code"}

                ],
                "columnDefs"     : [
                    
                    {
                        "targets"     : [1],
                        "orderable"   : false,
                        "bSearchable" : false,
                        "class"       : 'center-tr'
                    }
                ],
                "order"          : [[0, "desc"]]

            });

            $('#dataTables-stock-detail').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrl').val(),
                "iDisplayLength" : 10,
                "columns"        : [
                  {data : "tanggal"},
                    {data : "jumlah"},
                    {data : "code"},
                    {data : "price"} 

                ],
                "columnDefs"     : [
                    
                    {
                        "targets"     : 1,
                        "orderable"   : false,
                        "bSearchable" : false,
                        "class"       : 'center-tr'
                    }
                ],
                "order"          : [[0, "desc"]]

            });
            $('#dataTables-store').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrl').val(),
                "iDisplayLength" : 10,
                "columns"        : [
                    {data : "id"},
                    {data : "store_name"},
                    {data : "store_address"},
                    {data : "store_phone"},
                    {data : "actions"}

                ],
                "columnDefs"     : [
                    {
                        "targets"     : 0,
                        "visible"     : false,
                        "bSearchable" : false
                    },
                    {
                        "targets"     : 4,
                        "orderable"   : false,
                        "bSearchable" : false,
                        "class"       : 'center-tr'
                    }
                ],
                "order"          : [[0, "desc"]]

            });

            $('#dataTables-staff').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrl').val(),
                "iDisplayLength" : 10,
                "columns"        : [
                    {data : "id"},
                    {data : "store_name"},
                    {data : "outlet_name"},
                    {data : "username"},
                    {data : "name"},
                    {data : "email"},
                    {data : "phone"},
                    {data : "gender"},
                    {data : "actions"}

                ],
                "columnDefs"     : [
                    {
                        "targets"     : 0,
                        "visible"     : false,
                        "bSearchable" : false
                    },
                    {
                        "targets"     : 7,
                        "orderable"   : false,
                        "bSearchable" : false
                    },
                    {
                        "targets"     : 8,
                        "orderable"   : false,
                        "bSearchable" : false,
                        "class"       : 'center-tr'
                    }
                ],
                "order"          : [[0, "desc"]]

            });

            $('#dataTables-staff-special').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrl').val(),
                "iDisplayLength" : 10,
                "columns"        : [
                    {data : "id"},
                    {data : "username"},
                    {data : "name"},
                    {data : "email"},
                    {data : "phone"},
                    {data : "gender"},
                    {data : "actions"}

                ],
                "columnDefs"     : [
                    {
                        "targets"     : 0,
                        "visible"     : false,
                        "bSearchable" : false
                    },
                    {
                        "targets"     : 5,
                        "orderable"   : false,
                        "bSearchable" : false
                    },
                    {
                        "targets"     : 6,
                        "orderable"   : false,
                        "bSearchable" : false,
                        "class"       : 'center-tr'
                    }
                ],
                "order"          : [[0, "desc"]]

            });

            $('#dataTables-floor').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrl').val(),
                "iDisplayLength" : 10,
                "columns"        : [
                    {data : "id"},
                    {data : "floor_name"},
                    {data : "store_name"},
                    {data : "actions"}

                ],
                "columnDefs"     : [
                    {
                        "targets"     : 0,
                        "visible"     : false,
                        "bSearchable" : false
                    },
                    {
                        "targets"     : 3,
                        "orderable"   : false,
                        "bSearchable" : false,
                        "class"       : 'center-tr'
                    }
                ],
                "order"          : [[0, "desc"]]

            });

            $('#dataTables-sync').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrl').val(),
                "iDisplayLength" : 10,
                "columns"        : [
                    {data : "url"},
                    {data : "controller"},
                    {data : "start_time"},
                    {data : "end_time"},
                    {data : "interval"},
                    {data : "actions"}


                ],
                "columnDefs"     : [
                    {
                        "targets"     : 5,
                        "orderable"   : false,
                        "bSearchable" : false,
                        "class"       : 'center-tr'
                    }
                ],
                "order"          : [[0, "desc"]],
				        "sScrollX": '100%'
            });

            $('#dataTables-tax').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrl').val(),
                "iDisplayLength" : 10,
                "columns"        : [
                    {data : "tax_name"},
                    {data : "tax_percentage"},
                    {data : "actions"}


                ],
                "columnDefs"     : [
                    {
                        "targets"     : 2,
                        "orderable"   : false,
                        "bSearchable" : false,
                        "class"       : 'center-tr'
                    }
                ],
                "order"          : [[0, "desc"]]

            });
            $('#dataTables-charges').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrl').val(),
                "iDisplayLength" : 10,
                "columns"        : [
                    {data : "charge_name"},
                    {data : "charge_value"},
                    {data : "actions"}


                ],
                "columnDefs"     : [
                    {
                        "targets"     : 2,
                        "orderable"   : false,
                        "bSearchable" : false,
                        "class"       : 'center-tr'
                    }
                ],
                "order"          : [[0, "desc"]]

            });

      
            $('#dataTables-inventory-stock').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrl').val(),
                "iDisplayLength" : 10,
                "columns"        : [
                    {data : "date"},
                    {data : "outlet_name"},
                    {data : "name"},
                    {data : "minimal_stock"},                    
                    {data : "stock"},
                    {data : "unit"}
                    // {data : "actions"}


                ],
                // "columnDefs"     : [
                //     {
                //         "targets"     : 6,
                //         "orderable"   : false,
                //         "bSearchable" : false,
                //         "class"       : 'center-tr'
                //     }
                // ],
                "order"          : [[0, "desc"]]

            });
            $(document).on("change","#ip_inventory_id",function(){
              inventory_id=$(this).val();
              quantity=parseInt($("#ip_quantity").val());
              if(isNaN(quantity))quantity=0;
              App.ShowTreeConvertion(inventory_id,quantity);
            });
            $(document).on("keyup","#ip_quantity",function(){
              inventory_id=$("#ip_inventory_id").val();
              quantity=parseInt($(this).val());
              if(isNaN(quantity))quantity=0;
              App.ShowTreeConvertion(inventory_id,quantity);
            });
            //sortable
            $(".sortable").sortable();
            $(".sortable > span").disableSelection();

            $('#add_side_dish').on('click', function (e) {
                e.preventDefault();
                var countside = $('.countside').length;
                if (countside < 10) {
                    App.sidedishCount++;
                    App.add_option('sidedish', App.sidedishCount);
                } else {
                    // alert('Side dish already reached maximum quote');
                    App.alert('Side dish sudah mencapai batas maksimum');
                }
            });

            $('#add_options').on('click', function (e) {
                e.preventDefault();
                var countopt = $('.countopt').length;
                if (countopt < 10) {
                    App.optionsCount++;
                    App.add_option('options', App.optionsCount);
                } else {
                    App.alert('Opsi sudah mencapai batas maksimum');
                }
            });

            $('#add_printer_kitchen').on('click', function (e) {
                e.preventDefault();
                var printerKitchenCount = $('.printer_kitchen_count').length;
                App.printerKitchenCount++;
                App.add_option('printerKitchen', App.printerKitchenCount);
                
            });

            $('#add_printer_checker').on('click', function (e) {
                e.preventDefault();
                var printerKitchenCount = $('#printer_checker_count').length;
                App.printerKitchenCount++;
                App.add_option('printerChecker', App.printerKitchenCount);
                
            });

            $('#add_printer_checker_kitchen').on('click', function (e) {
                e.preventDefault();
                var printerKitchenCount = $('#printer_checker_kitchen_count').length;
                App.printerKitchenCount++;
                App.add_option('printerCheckerKitchen', App.printerKitchenCount);
                
            });
            

            $('#sidedish_container').sortable({
                axis                 : "y",
                items                : 'tr',
                handle               : '.handle',
                forceHelperSize      : true,
                forcePlaceholderSize : true
            });

            $('#options_container').sortable({
                axis                 : "y",
                items                : 'tr',
                handle               : '.handle',
                forceHelperSize      : true,
                forcePlaceholderSize : true
            });
            function delete_row() {
                console.log("delete row ");
            }

            $('.delete-purchase-order').on('click', function(e){
                e.preventDefault();
                var object = $(this);

                deleteNow = function() {
                    window.location = object.attr("href");
                }

                App.confirm('Anda yakin ingin menghapus purchase order?', deleteNow); 
            });

            $(document).ajaxStop(function () {
                $('.deleteNow').on('click', function (e) {
                    e.preventDefault();
                    var object = $(this);

                    function deleteNow() {
                        window.location = object.attr("href");
                    }

                    App.confirm('Anda yakin ingin menghapus ' + $(this).attr('rel') + '?', deleteNow);

                });
            });

            if (App.sidedishCount > 0) {
                for (var i = 0; i < App.sidedishCount; i++) {
                    $('#remove_sidedish_' + i + '').on('click', function (e) {
                        var str = $(this).attr('id');

                        function removeSideDish() {
                            $('#side-dish-' + str.substr(16)).remove();
                        }

                        App.confirm('Anda yakin ingin menghapus side dish?', removeSideDish);
                    });
                }
            }

            if (App.printerKitchenCount > 0) {
                
                for (var i = 0; i < App.printerKitchenCount; i++) {
                    $('#remove_printer_kitchen_' + i + '').on('click', function (e) {
                        var str = $(this).attr('id').split('_');

                        function removePrinterKitchen() {
                            $('#printer_kitchen_' + str[3]).remove();
                        }

                        App.confirm('Anda yakin ingin menghapus?', removePrinterKitchen);
                    });
                }
            }

            if (App.printerCheckerCount > 0) {
                
                for (var i = 0; i < App.printerCheckerCount; i++) {
                    $('#remove_printer_checker_' + i + '').on('click', function (e) {
                        var str = $(this).attr('id').split('_');
                        function removePrinter() {
                            $('#printer_checker_' + str[3]).remove();
                        }

                        App.confirm('Anda yakin ingin menghapus?', removePrinter);
                    });
                }
            }

            if (App.printerCheckerKitchenCount > 0) {
                
                for (var i = 0; i < App.printerCheckerKitchenCount; i++) {
                    $('#remove_printer_checker_kitchen_' + i + '').on('click', function (e) {
                        var str = $(this).attr('id').split('_');
                        function removePrinter() {
                            $('#printer_checker_kitchen_' + str[4]).remove();
                        }

                        App.confirm('Anda yakin ingin menghapus?', removePrinter);
                    });
                }
            }



            if (App.optionsCount > 0) {
                for (var i = 0; i < App.optionsCount; i++) {
                    $('#remove_options_' + i + '').on('click', function (e) {
                        var str = $(this).attr('id');

                        function removeOptions() {
                            $('#m-option-' + str.substr(15)).remove();
                        }

                        App.confirm('Anda yakin ingin menghapus option?', removeOptions);
                    });

                    $('#add_option_value_' + i + '').on('click', function (e) {
                        e.preventDefault();
                        var str = $(this).attr('id');
                        var count = str.substr(17);
                        var countoptvalue = $('.countoptvalue-' + count).length;
                        if (countoptvalue < 10) {
                            App.optionsValueCount++;
                            var appedendVal = '' +
                                '<div class="option-values-form countoptvalue-' + count + '">' +
                                '<div class="row">' +
                                '<div class="col-md-1"><a class="handle-item btn btn-mini"><i class="fa fa-bars"></i></a></div>' +
                                '<div class="col-md-10"><input type="text" class="form-control requiredTextField" placeholder="Enter Options value" field-name = "Option value" name="options[' + count + '][values][' + App.optionsValueCount + '][option_value_name]"/></div>' +
                                '<div class="col-md-1"><button id="remove_options_value_' + App.optionsValueCount + '" type="button" class="btn btn-mini btn-danger pull-right"><i class="fa fa-trash-o"></i></button></div>' +
                                '</div>' +
                                '</div>';

                            $('#option-items-' + count).append(appedendVal);

                            $('#remove_options_value_' + App.optionsValueCount + '').on('click', function (e) {
                                var optvallength = $('.countoptvalue-' + count).length;
                                var object = $(this);
                                if (optvallength > 1) {
                                    function removeOptionValue() {
                                        object.closest('.option-values-form').remove();
                                    }

                                    App.confirm('Anda yakin ingin menghapus option value ?', removeOptionValue);
                                } else {
                                    App.alert('Tidak dapat menghapus nilai opsi terakhir');
                                }
                            });

                        } else {
                            App.alert('Nilai opsi sudah mencapai batas maksimum');
                        }

                    });

                    if (App.optionsValueCount > 0) {
                        for (var j = 0; j < App.optionsValueCount; j++) {
                            $('#option-items-' + j).sortable({
                                axis                 : "y",
                                handle               : '.handle-item',
                                forceHelperSize      : true,
                                forcePlaceholderSize : true
                            });

                            $('#remove_options_value_' + j + '').on('click', function (e) {
                                var str = $(this).closest('.option-values-form').attr('class');
                                var count = str.substr(33);
                                var optvallength = $('.countoptvalue-' + count).length;
                                if (optvallength > 1) {
                                    function removeOptionValue() {
                                        $('#remove_options_value_' + j + '').closest('.option-values-form').remove();
                                    }

                                    App.confirm('Anda yakin ingin menghapus option value?', removeOptionValue);
                                } else {
                                    App.alert('Tidak dapat menghapus nilai opsi terakhir');
                                }
                            });
                        }
                    }
                }
            }


            $('.removeImageMenu').on('click', function (e) {
                e.preventDefault();
                var object = $(this);

                function remove_image() {
                    var id = object.attr('rel');
                    var url = object.attr('url-data');
                    var request = $.ajax({
                        type : 'POST',
                        url  : url,
                        data : {id : id}
                    });
                    request.done(function (msg) {
                        var parsedObject = JSON.parse(msg);
                        App.alert(parsedObject.message);
                        $('#primaryimage').remove();
                    });
                    request.fail(function (jqXHR, textStatus) {
                    });
                    request.always(function () {
                    });
                }

                App.confirm('Remove this Image?', remove_image);
            });

            $('#datepickertime .time').timepicker({
                'showDuration'  : true,
                'timeFormat'    : 'H:i',
                'scrollDefault' : 'now',
                'step'          : 30
            });

            
            $("#input_created_date").datepicker({
                dateFormat     : 'yy-mm-dd',
                constrainInput: true,
                showOn: 'button',
                buttonImage: App.baseUrl+'assets/img/calendar_icon.png'   
            });
            // initialize datepair
            var basicTimePair = document.getElementById('datepickertime');
            if (basicTimePair != null) {
                var datepair = new Datepair(basicTimePair);
            }
            
             $('#purchase_date').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'YYYY-MM-DD HH:mm' 
             });
             
             $('#purchase_order_date').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'YYYY-MM-DD HH:mm' 
             });
            $(document).on("click","#add_po_create",function(){
              $.ajax({
                url:App.baseUrl + "admincms/purchase_order/add_po_create",
                dataType:"JSON",
                success:function(response){
                  $("#po_create_table tbody").append(response.content)
                  $("#po_create_table tbody tr:last .select2").select2()
                }
              });
            });
            
            $('#purchase-qty').on('keyup', function(){
              value=parseFloat($(this).val());
              if(isNaN(value))value=0;
              max=parseFloat(isNaN($(this).data('max')) ? 0 : $(this).data('max'));
              if(value>max){
                $(this).val(max);
              }else if(value<0){
                $(this).val(0);
              }
            });

            $(document).on("change","#stock_add_inventory_id",function(){
              inventory_id=$(this).val();
              el=this;
              if(inventory_id!=""){
                uom_id=$(this).find("option:selected").attr("uom_id");
                $.ajax({
                  url:App.baseUrl+"admincms/inventory/get_inventory_uoms",
                  type:"POST",
                  dataType:"JSON",
                  data:{inventory_id:inventory_id},
                  success:function(response){
                    $("#stock_add_uom_id").html(response.content);
                    $("#stock_add_uom_id").val(uom_id);
                  }
                });
              }else{
                $("#stock_add_uom_id").html("<option value=''>Pilih Satuan</option>");
              }
            });
            $(document).on("change","#po_create_table tbody .inventory_id",function(){
              inventory_id=$(this).val();
              el=this;
              if(inventory_id!=""){
                uom_id=$(this).find("option:selected").attr("uom_id");
                $.ajax({
                  url:App.baseUrl+"admincms/inventory/get_inventory_uoms",
                  type:"POST",
                  dataType:"JSON",
                  data:{inventory_id:inventory_id},
                  success:function(response){
                    $(el).parents("tr").find("select.uom_id").html(response.content);
                    $(el).parents("tr").find("select.uom_id").val(uom_id);
                  }
                });
              }else{
                $(this).parents("tr").find(".uom_id").html("<option value=''>Pilih Satuan</option>");
              }
            });
            $(document).on("click","#po_create_table tbody .remote_item_po",function(){
              $(this).parents("tr").remove();
            });
            App.eventOpname();
            
            $('#sync-me-now').on('click', function () {
                App.verificationPrivateKey();
                // App.syncDatabaseFromServer();
            });

            // initialize datepair
            var basicTimePair = document.getElementById('purchase_date');
            if (basicTimePair != null) {
                var datepair = new Datepair(basicTimePair);
            }
      

            $("#request-transfer-select-store").change(function(){
                store_id = $(this).val();
                if(store_id > -1) {
                    $.ajax({
                        url: $('#admin_url').val()+'/stock_transfer/get_outlet/'+store_id,
                        dataType: 'json',
                        success: function(data) {
                            if(data.status) {
                                outlets = data.outlets;
                                $('#request-transfer-select-outlet').empty();
                                for(var i=0; i < outlets.length; i++) {
                                    option = '<option value="'+outlets[i].id+'">'+outlets[i].outlet_name+'</option>';
                                    $('#request-transfer-select-outlet').append(option);
                                }

                            }
                        }
                    });
                }
            });
            
            $('#request-transfer-add-inventory').on('click', function(){
                counter = $(this).data('counter');
                counter++;
                $(this).data('counter', counter);
                row = $("#inventory-list-0").clone(true);
                $("[id^=request-transfer-select-inventory-]").each(function(){
                    this_val = $(this).val();
                  if(this_val!=""){
                    row.find("option[value='"+this_val+"']").remove();
                  }
                });
                row.attr('id', 'inventory-list-'+counter);
                row.removeClass('hidden');
                row.find('label').attr('id', 'inventory-label-'+counter);
                row.find('select:first').attr('id', 'request-transfer-select-inventory-'+counter);
                row.find('select:first').attr('name', 'inventory-code[]');
                row.find('select:last').attr('name', 'inventory-uom[]');
                row.find('.quantity').attr('name', 'inventory-quantity[]');
                row.find('button#delete-inventory-0').attr('id', 'delete-inventory-'+counter);
                row.insertAfter(".inventory-row:last");
                $(".inventory-row:last select:first").select2();
                $('#save-request').css('visibility', 'visible');
            });

            $('[id^=delete-inventory-').on('click', function(){
                $(this).closest('tr').remove();
            });

            $('[id^=request-transfer-select-inventory-').on('change', function(){
                inventory_id=$(this).val();
                el=this;
                if(inventory_id!=""){
                  uom_id=$(this).find("option:selected").attr("uom_id");
                  $.ajax({
                    url:App.baseUrl+"admincms/inventory/get_inventory_uoms",
                    type:"POST",
                    dataType:"JSON",
                    data:{inventory_id:inventory_id},
                    success:function(response){
                      $(el).parents("tr").find("select.uom_id").html(response.content);
                      $(el).parents("tr").find("select.uom_id").val(uom_id);
                    }
                  });
                }else{
                  $(this).parents("tr").find(".uom_id").html("<option value=''>Pilih Satuan</option>");
                }
                // inventory_id = $(this).val();
                // select = $(this);
                // id = select.attr('id');
                // $.ajax({
                    // url: $('#admin_url').val()+'/stock_transfer/get_inventory/'+inventory_id,
                    // dataType: 'json',
                    // success: function(data) {
                        // if(data.status) {
                            // select.closest('tr').find('td label').html(data.unit);
                        // }
                    // }
                // });
            });
            
            $( ".stock-request-spinner" ).each(function(){
                min=parseInt(isNaN($(this).data('min')) ? 0 : $(this).data('min'));
                max=parseInt(isNaN($(this).data('max')) ? 0 : $(this).data('max'));
                $(this).spinner({
                    max: max,
                    min: min
                });
                $(this).spinner("value", $(this).data('value'));

            });
            $( ".stock-request-spinner" ).on('keyup',function(){
              value=parseInt($(this).val());
              if(isNaN(value))value=0;
              max=parseInt(isNaN($(this).data('max')) ? 0 : $(this).data('max'));
              if(value>max){
                $(this).val(max);
              }else if(value<0){
                $(this).val(0);
              }
            });
            $( ".po-spinner" ).each(function(){
                $(this).spinner({
                    max: $(this).data('max'),
                    min: 0,
                    change: function(event, ui){
                        id = $(this).data('id');
                        sum = 0;
                        $('.spinner-'+id+':enabled').each(function(){
                            sum += parseInt($(this).val());
                        });
                        $('#sum-'+id).html(sum);
                        $('#sum-'+id).closest('tr').find('input').val(sum);
                    }
                });
                $(this).spinner("value", $(this).data('value'));

            });

            $( ".receive-spinner" ).each(function(){
                min=parseInt(isNaN($(this).data('min')) ? 0 : $(this).data('min'));
                max=parseInt(isNaN($(this).data('max')) ? 0 : $(this).data('max'));
                $(this).spinner({
                    max: max,
                    min: min,
                    spin: function(event, ui){
                        tr = $(this).closest('tr');
                        pcs = ui.value;
                        price = tr.find('.pcs-price').val();
                        count_order = $('#detail_order').val();
                        sub_total = pcs*price;
                        tr.find('.sub-total').html(sub_total);
                        for (var i = 0; i < count_order; i++) {
                          pcs_price = $('#pcs-price'+i).val();
                          pcs_receive = $('#receive-qty'+i).val();
                          acc_debit = pcs_receive*pcs_price;
                          $('#debit-hidden'+i).val(acc_debit);
                        }
                        grand_total = 0;
                        $('.sub-total').each(function(){
                            grand_total += parseFloat($(this).html());
                        });
                        $('.grand-total').html(grand_total);
                        $('#total-hidden').val(grand_total);
                    }
                });
                $(this).spinner("value", $(this).data('value'));

            });

            $('.retur-qty').on('keyup', function(){
              value=parseFloat($(this).val());
              if(isNaN(value))value=0;
              if(value<0){
                $(this).val(0);
              }
              tr = $(this).closest('tr');
              ret_qty = tr.find('.retur-qty').val();
              price = tr.find('.pcs-price').val();
              count_order = $('#detail_order').val();
              sub_total = ret_qty*price;
              tr.find('.sub-total').html(App.number_format(sub_total));
              tr.find('.sub-total').attr("sub_total",sub_total);
              for (var i = 0; i < count_order; i++) {
                pcs_price = $('#pcs-price'+i).val();
                pcs_retur = $('#retur-qty'+i).val();
                acc_debit = pcs_retur*pcs_price;
                $('#debit-hidden'+i).val(acc_debit);

                maxVal = $('#max-retur-'+i).val();
                maxReturPrice = maxVal * pcs_price;

                if (parseInt(pcs_retur) > parseInt(maxVal)) {
                  $('#retur-qty'+i).val(maxVal);
                  tr.find('.sub-total').html(App.number_format(maxReturPrice));
                  tr.find('.sub-total').attr("sub_total",maxReturPrice);
                }
              }
              grand_total = 0;
              $('.sub-total').each(function(){
                  grand_total += parseFloat($(this).attr("sub_total"));
              });
              $('.grand-total').html(App.number_format(grand_total));
              $('#total-hidden').val(grand_total);
            });

            $('.pcs-price,.receive-qty').on('keyup', function(){
                retur_total = 0;
                tr = $(this).closest('tr');
                pcs = tr.find('.receive-qty').val();
                price = tr.find('.pcs-price').val();
                count_order = $('#detail_order').val();
                sub_total = pcs*price;
                tr.find('.sub-total').html(App.number_format(sub_total));
                tr.find('.sub-total').attr("sub_total",sub_total);
                for (var i = 0; i < count_order; i++) {
                  pcs_price = $('#pcs-price'+i).val();
                  pcs_receive = $('#receive-qty'+i).val();
                  acc_debit = pcs_receive*pcs_price;
                  $('#debit-hidden'+i).val(acc_debit);

                  if (parseInt(pcs_price) < 0) {
                    $('#pcs-price'+i).val(0);
                    tr.find('.sub-total').html(App.number_format(0));
                    tr.find('.sub-total').attr("sub_total",0);
                  }
                }
                grand_total = 0;
                $('.sub-total').each(function(){
                    grand_total += parseFloat($(this).attr("sub_total"));
                    // grand_total += parseInt($(this).html());
                });
                $('.grand-total').html(App.number_format(grand_total));
                $('#total-hidden').val(grand_total);
                $('#discount').trigger("keyup");
            });


            $('.receive-spinner').on('keyup', function(){
              value=parseFloat($(this).val());
              if(isNaN(value))value=0;
              max=parseFloat(isNaN($(this).data('max')) ? 0 : $(this).data('max'));
              // console.log(value);
              // console.log(max);
              if(value>max){
                $(this).val(max);
              }else if(value<0){
                $(this).val(0);
              }
            });
            $('.receive-spinner, .pcs-price, #discount').on('keyup', function(){
                value = $(this).val();
                if($.isNumeric(value) === false)
                    this.value = this.value.slice(0,-1);
            });

            $('#discount').on('keyup', function(){
                grand_total = ($('.grand-total:first').siblings("#total-hidden").val());
                discount = ($(this).val());

                if (discount > grand_total) {
                  $(this).val(grand_total);
                  discount = grand_total;
                }

                grand_total_final = grand_total - discount;
                $('.grand-total-final').html(App.number_format(grand_total_final));
                $('#grand-total-hidden').val(grand_total_final);
            });

            $('input[name="method"]').on('change', function(){
                if('cash' == $(this).val()) $('#bon-date').hide();
                else $('#bon-date').show();
            });

            $('.date-input').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'YYYY-MM-DD HH:mm'
            });
            $('.bon_date').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'YYYY-MM-DD HH:mm',
                minDate:new Date()
            });

            $('.check-order').on('click', function(){
                parent = $(this).closest('tr');
                id = parent.data('id');
                status = parent.find('.po-spinner').spinner( "option", "disabled");

                if(status == 'true') parent.find('.po-spinner').spinner("enable");
                else parent.find('.po-spinner').spinner( "option", "disabled", true);
                
                sum = 0;
                $('.spinner-'+id+':enabled').each(function(){
                    sum += parseInt($(this).val());
                });
                $('#hidden-sum-'+id).val(sum);
                $('#sum-'+id).html(sum);
            });

            // $('#dataTables-general-journal').dataTable();

            $('body').on('click', '.choose-account', function(e){
                e.preventDefault();
                console.log(App.listAccounts);
                console.log(App.listAccounts.indexOf($(this).data("id")));
                if(App.listAccounts.indexOf($(this).data("id")) >= 0 ) {
                    App.alert("Account Added");
                    return;   
                }
                row =   '<tr>'+
                            '<td><i class="fa fa-search"></i></td>'+
                            '<td>'+$(this).data('code')+'<input type="hidden" name="code['+$(this).data('id')+']" value="'+$(this).data('code')+'"/></td>'+
                            '<td>'+$(this).data('name')+'<input type="hidden" name="name['+$(this).data('id')+']" value="'+$(this).data('name')+'"/></td>'+
                            '<td><input type="text" name="debit['+$(this).data('id')+']" class="form-control debit" placeholder="debit" /></td>'+
                            '<td><input type="text" name="credit['+$(this).data('id')+']" class="form-control credit" placeholder="credit" /></td>'+
                            '<td><input type="text" name="info['+$(this).data('id')+']"class="form-control" placeholder="info"/></td>'+
                            '<td><a href="#" class="delete-row-account">Hapus</a></td>'+
                        '</tr>';
                $('#sticky-search').before(row);
                App.listAccounts.push($(this).data('id'));
            });

            $('body').on('keyup', '.credit', function(){
                value_credit = $(this).val();
                if($.isNumeric(value_credit) === false)
                    this.value = this.value.slice(0,-1);
                debit = $(this).closest('tr').find('.debit');
                console.log(value_credit);
                if(0 == value_credit.length)
                    debit.removeAttr('disabled');
                else
                    debit.attr('disabled', 'disabled');
                debit.val('');
            });
            $('body').on('keyup', '.debit', function(){
                value_debit = $(this).val();
                if($.isNumeric(value_debit) === false)
                    this.value = this.value.slice(0,-1);
                credit = $(this).closest('tr').find('.credit');
                console.log(value_debit);
                if(0 == value_debit.length)
                    credit.removeAttr('disabled');
                else
                    credit.attr('disabled', 'disabled');
                credit.val('');
            });
            $('body').on('change', '.debit, .credit', function(){
                debit = 0;
                credit = 0;
                $('.debit').each(function(){
                    val = parseInt($(this).val());
                    if(!isNaN(val))
                        debit += val;
                });
                $('.credit').each(function(){
                    val = parseInt($(this).val());
                    if(!isNaN(val))
                        credit += parseInt(val);
                });

                $('#total-debit').html(debit);
                $('#total-credit').html(credit);
                if(debit != credit){
                    $('#save-journal').addClass('disabled');
                    $('#summary').css('color', 'red');
                }
                else{
                    $('#save-journal').removeClass('disabled');
                    $('#summary').css('color', '');
                }
            });
            $('body').on('click', '.delete-row-account', function(e){
                e.preventDefault();
                $(this).closest('tr').remove();
                debit = 0;
                credit = 0;
                $('.debit').each(function(){
                    val = parseInt($(this).val());
                    if(!isNaN(val))
                        debit += val;
                });
                $('.credit').each(function(){
                    val = parseInt($(this).val());
                    if(!isNaN(val))
                        credit += parseInt(val);
                });

                $('#total-debit').html(debit);
                $('#total-credit').html(credit);
                if(debit != credit){
                    $('#save-journal').addClass('disabled');
                    $('#summary').css('color', 'red');
                }
                else{
                    $('#save-journal').removeClass('disabled');
                    $('#summary').css('color', '');
                }
            });

            $('.account-pop').on('click', function(){
                console.log("Test");
                rows = '';
                $.ajax({
                    url: $('#admin_url').val()+'/journal/get_accounts/',
                    dataType: 'json',
                    async: false,
                    success: function(data) {
                        if(data.status) {
                            accounts = data.accounts;
                            for(var i=0; i < accounts.length; i++) {
                                rows += '<tr>'+
                                            '<td>'+accounts[i].code+'</td>'+
                                            '<td>'+accounts[i].name+'</td>'+
                                            '<td>'+
                                                '<a href="#" class="choose-account" data-code="'+accounts[i].code+'" data-name="'+accounts[i].name+'" data-id="'+accounts[i].id+'">Pilih</a>'+
                                            '</td>'+
                                        '</tr>';
                            }

                        }
                    }
                });

                var table = '<table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-account-pop-up">'+
                                '<thead>'+
                                '<tr>'+
                                    '<th class="col-md-1">Kode</th>'+
                                    '<th class="col-md-2">Nama Akun</th>'+
                                    '<th class="col-md-2">Aksi</th>'+
                                '</tr>'+
                                '</thead>'+
                                '<tbody>'+
                                    rows+
                                '</tbody>'+
                            '</table>';

                var html = '<div class="modal fade" id="account-pop-up" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">'+
                              '<div class="modal-dialog">'+
                                '<div class="modal-content">'+
                                  '<div class="modal-header">'+
                                    '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
                                    '<h3 class="modal-title" id="exampleModalLabel"> Pilih akun </h3>'+
                                  '</div>'+
                                  '<div class="modal-body">'+
                                   table+
                                  '</div>'+
                                  '<div class="modal-footer">'+
                                   ' <button type="button" class="btn btn-default btn-cancel" data-dismiss="modal">Tutup</button>'+
                                 ' </div>'+
                                '</div>'+
                             ' </div>'+
                            '</div>';
                
                $("body").append(html);
                $('#dataTables-account-pop-up').dataTable();

                $("#account-pop-up").modal("show");
            });

            $('.cancel-request-transfer').on('click', function(){
                console.log($(this).data('id'));
            });

            App.overlayUI.hide();
            if($("body").hasClass("collapse")){
              window.location.reload();
            }

            $('.pay-bon').on('click', function (e) {
                e.preventDefault();
                var object = $(this);
                payNow = function () { 
                    var tgl_bayar = $("#payment_bon_date_val").val();
                    var account_id = $("#account_id").val();
                    var amount = $("#payment_amount").val();
                    if(account_id==undefined)account_id=0;
                    if(tgl_bayar.length == 0){
                        App.alert("Tanggal Bayar Kosong");
                    }else{
                        window.location = object.attr("href")+"/"+tgl_bayar+"/"+account_id+"/"+amount;
                    }
                    
                }
                App.confirm('Apakah anda yakin ingin membayar bon ini?', payNow);
            });
        },
        initListTransferFilter:function(){
          var options = {
            valueNames: [ 'name' ],
            searchClass:'search'
          };
          new List('inventories', options);
        },
        ShowTreeConvertion:function(inventory_id,quantity){
          if(inventory_id!="" && quantity>0){
            $.ajax({
              url:App.baseUrl + "admincms/inventory_process/show_tree_convertion",
              dataType:"JSON",
              data:{inventory_id:inventory_id,quantity:quantity},
              success:function(response){
                $("#show_tree_convertion").html(response.content);
              }
            })
          }
        },
        FeatureSettingUI:function(){
          $(document).on("click",".clear_feature_setting",function(){
            $("#users_unlock").val("");
          });
          $(document).on("click",".set_feature_unlock",function(){
            url=$(this).attr("href");
            $.ajax({
              url:url,
              dataType:"JSON",
              success:function(response){
                $("#feature_setting_modal").html(response.content);
                $("#feature_setting_modal").modal();
              }
            });
            return false;
          });
        },
        add_option             : function (e, count) {
            if (e == 'printerKitchen') {
               
                function createOutletOption(id)
                {

                    var appendOpt = ''+
                    '<select id="printer_kitchen_id_chained_'+id+'" name="printer_kitchen['+id+'][outlet_id]" field-name = "Outlet" class="form-control requiredDropdown ingredient_id_chained" autocomplete="off">';

                        for (var row in App.dataOutlet) {
                            appendOpt +=  '<option value="'+row+'">'+App.dataOutlet[row]+'</option>'
                           
                        };
                    appendOpt += '</select>';
                    return appendOpt;
                
                }

                 var appedendVal = '' +
                    '<tr id="printer_kitchen_' + count + '" class="count_printer_kitchen"><td>' +
                    '<div class="row"> <div class="col-md-10 col-md-offset-1">' +
                    '<div class="col-md-4">'+createOutletOption(count) +'</div>' +
                    '<div class="col-md-4"><input type="text" class="form-control requiredTextField" field-name = "nama printer" placeholder="" name="printer_kitchen[' + count + '][printer_name]"/></div>' +
                    '<div class="col-md-1"><button id="remove_printer_kitchen_' + count + '" type="button" class="btn btn-mini btn-danger pull-right"><i class="fa fa-trash-o"></i></button></div>' +
                    '</div></div>' +
                    '</td></tr>';



                $('#printer_kitchen_container').append(appedendVal);

                $('#remove_printer_kitchen_' + count + '').on('click', function (e) {
                    function removePrinterKitchen() {
                        $('#printer_kitchen_' + count).remove();
                    }

                    App.confirm('Anda yakin ingin menghapus?', removePrinterKitchen);
                });
            } // printer kitchen
            else if( e == 'printerChecker'){
                // function createOutletCheckerOption(id)
                // {

                //     var appendOpt = ''+
                //     '<select id="printer_checker_id_chained_'+id+'" name="printer_checker['+id+'][outlet_checker_id]" field-name = "Outlet" class="form-control requiredDropdown ingredient_id_chained" autocomplete="off">';

                //         for (var row in App.dataOutlet) {
                //             appendOpt +=  '<option value="'+row+'">'+App.dataOutlet[row]+'</option>'
                           
                //         };
                //     appendOpt += '</select>';
                //     return appendOpt;
                
                // }

                var appedendVal = '' +
                    '<tr id="printer_checker_' + count + '" class="count_printer_checker"><td>' +
                    '<div class="row"> <div class="col-md-10 col-md-offset-1">' +
                    
                    '<div class="col-md-4"><input type="text" class="form-control requiredTextField" field-name = "nama printer" placeholder="nama printer" name="printer_checker[' + count + '][printer_name]"/></div>' +
                    '<div class="col-md-1"><button id="remove_printer_checker_' + count + '" type="button" class="btn btn-mini btn-danger pull-right"><i class="fa fa-trash-o"></i></button></div>' +
                    '</div></div>' +
                    '</td></tr>';



                $('#printer_checker_container').append(appedendVal);

                $('#remove_printer_checker_' + count + '').on('click', function (e) {
                    function removePrinterKitchen() {
                        $('#printer_checker_' + count).remove();
                    }

                    App.confirm('Anda yakin ingin menghapus?', removePrinterKitchen);
                });
            }// end printer checker
            else if( e == 'printerCheckerKitchen'){
                function createOutletCheckerKitchenOption(id)
                {

                    var appendOpt = ''+
                    '<select id="printer_checker_kitchen_id_chained_'+id+'" name="printer_checker_kitchen['+id+'][outlet_checker_kitchen_id]" field-name = "Outlet" class="form-control requiredDropdown ingredient_id_chained" autocomplete="off">';

                        for (var row in App.dataOutlet) {
                            appendOpt +=  '<option value="'+row+'">'+App.dataOutlet[row]+'</option>'
                           
                        };
                    appendOpt += '</select>';
                    return appendOpt;
                
                }

                var appedendVal = '' +
                    '<tr id="printer_checker_kitchen_' + count + '" class="count_printer_checker_kitchen"><td>' +
                    '<div class="row"> <div class="col-md-10 col-md-offset-1">' +
                    '<div class="col-md-4">'+createOutletCheckerKitchenOption(count) +'</div>' +
                    '<div class="col-md-4"><input type="text" class="form-control requiredTextField" field-name = "nama printer" placeholder="nama printer" name="printer_checker_kitchen[' + count + '][printer_name]"/></div>' +
                    '<div class="col-md-1"><button id="remove_printer_checker_kitchen_' + count + '" type="button" class="btn btn-mini btn-danger pull-right"><i class="fa fa-trash-o"></i></button></div>' +
                    '</div></div>' +
                    '</td></tr>';



                $('#printer_checker_kitchen_container').append(appedendVal);

                $('#remove_printer_checker_kitchen_' + count + '').on('click', function (e) {
                    function removePrinterKitchen() {
                        $('#printer_checker_kitchen_' + count).remove();
                    }

                    App.confirm('Anda yakin ingin menghapus?', removePrinterKitchen);
                });
            }// end printer checker kitchen


        },
        number_format:function(number, decimals, dec_point, thousands_sep) {
          //  discuss at: http://phpjs.org/functions/number_format/
          // original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
          // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
          // improved by: davook
          // improved by: Brett Zamir (http://brett-zamir.me)
          // improved by: Brett Zamir (http://brett-zamir.me)
          // improved by: Theriault
          // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
          // bugfixed by: Michael White (http://getsprink.com)
          // bugfixed by: Benjamin Lupton
          // bugfixed by: Allan Jensen (http://www.winternet.no)
          // bugfixed by: Howard Yeend
          // bugfixed by: Diogo Resende
          // bugfixed by: Rival
          // bugfixed by: Brett Zamir (http://brett-zamir.me)
          //  revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
          //  revised by: Luke Smith (http://lucassmith.name)
          //    input by: Kheang Hok Chin (http://www.distantia.ca/)
          //    input by: Jay Klehr
          //    input by: Amir Habibi (http://www.residence-mixte.com/)
          //    input by: Amirouche
          //   example 1: number_format(1234.56);
          //   returns 1: '1,235'
          //   example 2: number_format(1234.56, 2, ',', ' ');
          //   returns 2: '1 234,56'
          //   example 3: number_format(1234.5678, 2, '.', '');
          //   returns 3: '1234.57'
          //   example 4: number_format(67, 2, ',', '.');
          //   returns 4: '67,00'
          //   example 5: number_format(1000);
          //   returns 5: '1,000'
          //   example 6: number_format(67.311, 2);
          //   returns 6: '67.31'
          //   example 7: number_format(1000.55, 1);
          //   returns 7: '1,000.6'
          //   example 8: number_format(67000, 5, ',', '.');
          //   returns 8: '67.000,00000'
          //   example 9: number_format(0.9, 0);
          //   returns 9: '1'
          //  example 10: number_format('1.20', 2);
          //  returns 10: '1.20'
          //  example 11: number_format('1.20', 4);
          //  returns 11: '1.2000'
          //  example 12: number_format('1.2000', 3);
          //  returns 12: '1.200'
          //  example 13: number_format('1 000,50', 2, '.', ' ');
          //  returns 13: '100 050.00'
          //  example 14: number_format(1e-8, 8, '.', '');
          //  returns 14: '0.00000001'

          number = (number + '')
            .replace(/[^0-9+\-Ee.]/g, '');
          var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function(n, prec) {
              var k = Math.pow(10, prec);
              return '' + (Math.round(n * k) / k)
                .toFixed(prec);
            };
          // Fix for IE parseFloat(0.55).toFixed(0) = 0;
          s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
            .split('.');
          if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
          }
          if ((s[1] || '')
            .length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1)
              .join('0');
          }
          return s.join(dec);
        },
        buttonSubmitEvent      : function () {
            var btnAct;
            $("button[name=btnAction]").on('click', function (event) {
                event.preventDefault();
                btnAct = $(this).attr("value");
                $(".form-ajax").trigger("submit");
            });

            $('.form-ajax').on("submit", function (evt) {
                App.overlayUI.show();
                var myForm = $(this);

                console.log("bening");

                var target = $('.result');
                target.html('');

                $('.NumericOnly').each(function () {
                    var pattern = /^([1-9][0-9]*)|([0]+)$/;
                    var checkNum = pattern.test($(this).val());
                    if (checkNum === false) {
                        target.empty().html('<div class="alert alert-danger" role="alert">' + $(this).attr('field-name') + ' hanya menerima numerik</div>');
                        App.overlayUI.hide();
                        evt.preventDefault();
                        return false;
                    }
                });

                $('.NumericWithZero').each(function () {
                    if ($(this).val() != '') {
                        var pattern = /^([0-9]*)$/;
                        var checkNum = pattern.test($(this).val());
                        if (checkNum === false) {
                            target.empty().html('<div class="alert alert-danger" role="alert">' + $(this).attr('field-name') + ' hanya menerima numerik</div>');
                            App.overlayUI.hide();
                            evt.preventDefault();
                            return false;
                        }
                    }
                });

                $('.requiredDropdown').each(function () {
                    if ($(this).val() == '0') {
                        target.empty().html('<div class="alert alert-danger" role="alert">Anda harus memilih pilihan pada ' + $(this).attr('field-name') + '</div>');
                        App.overlayUI.hide();
                        evt.preventDefault();
                        return false;
                    }
                });

                $('.requiredTextField').each(function () {
                    if ($(this).val() == '') {
                        target.empty().html('<div class="alert alert-danger" role="alert">Bagian ' + $(this).attr('field-name') + ' dibutuhkan</div>');
                        App.overlayUI.hide();
                        evt.preventDefault();
                        return false;
                    }
                });

                $('.maxUploadSize').each(function () {
                    var max_img_size = $(this).data('maxsize');
                    if (max_img_size != '') {
                        var input = $(this)[0];
                        // check for browser support (may need to be modified)
                        if (input.files && input.files.length == 1) {
                            if (input.files[0].size > max_img_size) {
                                target.empty().html('<div class="alert alert-danger" role="alert">' + "The file must be less than " + Math.round(max_img_size / 1024 / 1024) + "MB" + '</div>');
                                App.overlayUI.hide();
                                evt.preventDefault();
                                return false;
                            }
                        }
                    }
                });

                var tempElement = $("<input type='hidden'/>");
                tempElement
                    .attr("name", 'btnAction')
                    .val(btnAct)
                    .appendTo(myForm);
                tempElement = '';
            });
        },
        syncGroups:function(){
            var posts = [];
            var total = 0;
            var syncName = "groups";
            $.when(
                App.getDataPos(App.serverBaseUrl + 'api/store/groups/',syncName)
            ).done(function (groups) {
                // console.log("DONE  "+syncName);
              
                if (groups !== undefined) {
                    total = groups.length;
                    if (total > 0) {
                        a = {groups : groups};
                    } else {
                        a = {groups : 1};
                    }
                    posts.push(a);
                }
                App.saveSync(posts,syncName,false);
            });
           
        },
        syncUser:function(){
            var posts = [];
            var total = 0;
            var syncName = "user";
            $.when(
                App.getDataPos(App.serverBaseUrl + 'api/store/user/id/'+App.storeID,syncName)
            ).done(function (user) {
                // console.log("DONE  "+syncName);
              
                if (user !== undefined) {
                    total = user.length;
                    if (total > 0) {
                        a = {user : user};
                    } else {
                        a = {user : 1};
                    }
                    posts.push(a);
                }
                App.saveSync(posts,syncName,false);
            });
           
        },
        syncSupplier:function(){
            var posts = [];
            var total = 0;
            var syncName = "supplier";
            $.when(
               App.getDataPos(App.serverBaseUrl + 'api/store/supplier',syncName)
            ).done(function (supplier) {
                // console.log("DONE  "+syncName);
                if (supplier !== undefined) {
                    total = supplier.supplier.length;
                    if (total > 0) {
                        a = {supplier : supplier.supplier};
                    } else {
                        a = {supplier : 1};
                    }
                    posts.push(a);
                }
                App.saveSync(posts,syncName,false);
            });
            
        },
        syncDeliveryCompany:function(){
          var posts = [];
          var total = 0;
          var syncName = "delivery_company";
          $.when(
            App.getDataPos(App.serverBaseUrl + 'api/store/delivery_company',syncName)
          ).done(function (delivery_company) {
            if (delivery_company !== undefined) {
              total = delivery_company.delivery_company.length;
              if (total > 0) {
                a = {delivery_company : delivery_company.delivery_company};
              } else {
                a = {delivery_company : 1};
              }
              posts.push(a);

              total = delivery_company.delivery_courier.length;
              if (total > 0) {
                a = {delivery_courier : delivery_company.delivery_courier};
              } else {
                a = {delivery_courier : 1};
              }
              posts.push(a);
            }
            App.saveSync(posts,syncName,false);
          });
        },
        syncVoucher:function(){
            var posts = [];
            var total = 0;
            var syncName = "voucher";
            $.when(
              App.getDataPos(App.serverBaseUrl + 'api/store/voucher',syncName)
            ).done(function (voucher) {
                // console.log("DONE  "+syncName);
                 
                if (voucher !== undefined) {
                    total = voucher.voucher_group.length;
                    if (total > 0) {
                        a = {voucher_group : voucher.voucher_group};
                    } else {
                        a = {voucher_group : 1};
                    }
                    posts.push(a);

                    total = voucher.voucher_availability.length;
                    if (total > 0) {
                        a = {voucher_availability : voucher.voucher_availability};
                    } else {
                        a = {voucher_availability : 1};
                    }
                    posts.push(a);

                    total = voucher.voucher.length;
                    if (total > 0) {
                        a = {voucher : voucher.voucher};
                    } else {
                        a = {voucher : 1};
                    }
                    posts.push(a);                       

                }
                App.saveSync(posts,syncName,false);
            });
            
        },
        syncAccount:function(){
            var posts = [];
            var total = 0;
             var syncName = "account";
            $.when(
                App.getDataPos(App.serverBaseUrl + 'api/store/account',syncName)
            ).done(function (account) {
                // console.log("DONE  "+syncName);
               
                if (account !== undefined) {
                    total = account.account.length;
                    if (total > 0){
                        a = {account : account.account};
                    } else {
                        a = {account : 1};
                    }
                    posts.push(a);
                }
                App.saveSync(posts,syncName,false);
            });
           
        },
        syncPromo:function(){
            var posts = [];
            var total = 0;
             var syncName = "promo";
            $.when(
                App.getDataPos(App.serverBaseUrl + 'api/store/promo/id/' + App.storeID,syncName)
            ).done(function (promo) {
                // console.log("DONE  "+syncName);
               
               if (promo !== undefined) {
                    total = promo.promo_cc.length;
                    if (total > 0) {
                        a = {promo_cc : promo.promo_cc};
                    } else {
                        a = {promo_cc : 1};
                    }
                    posts.push(a);

                    total = promo.promo_discount.length;
                    if (total > 0) {
                        a = {promo_discount : promo.promo_discount};
                    } else {
                        a = {promo_discount : 1};
                    }
                    posts.push(a);

                    total = promo.promo_cc_category.length;
                    if (total > 0) {
                        a = {promo_cc_category : promo.promo_cc_category};
                    } else {
                        a = {promo_cc_category : 1};
                    }
                    posts.push(a);

                    total = promo.promo_discount_category.length;
                    if (total > 0) {
                        a = {promo_discount_category : promo.promo_discount_category};
                    } else {
                        a = {promo_discount_category : 1};
                    }
                    posts.push(a);

                      total = promo.promo_discount_menu.length;
                    if (total > 0) {
                        a = {promo_discount_menu : promo.promo_discount_menu};
                    } else {
                        a = {promo_discount_menu : 1};
                    }
                    posts.push(a);

                    total = promo.promo_cc_menu.length;
                    if (total > 0) {
                      a = {promo_cc_menu : promo.promo_cc_menu};
                    } else {
                      a = {promo_cc_menu : 1};
                    }
                    posts.push(a);

                     total = promo.promo_schedule.length;
                    if (total > 0) {
                        a = {promo_schedule : promo.promo_schedule};
                    } else {
                        a = {promo_schedule : 1};
                    }
                    posts.push(a);
                    


                }
                App.saveSync(posts,syncName,false);
            });
           
        },
        syncTax:function(){
            var posts = [];
            var total = 0;
             var syncName = "tax";
            $.when(
               App.getDataPos(App.serverBaseUrl + 'api/store/tax',syncName)
            ).done(function (tax) {
                // console.log("DONE  "+syncName);
                if (tax !== undefined) {
                    total = tax.length;
                    if (total > 0) {
                        a = {tax : tax};
                    } else {
                        a = {tax : 1};
                    }
                    posts.push(a);
                }
                App.saveSync(posts,syncName,false);
            });
        },
        syncCompliment:function(){
            var posts = [];
            var total = 0;
             var syncName = "compliment";
            $.when(
              App.getDataPos(App.serverBaseUrl + 'api/store/compliment',syncName)
            ).done(function (compliment) {
                // console.log("DONE  "+syncName);
                if (compliment !== undefined) {
                    total = compliment.length;
                    if (total > 0) {
                        a = {compliment : compliment};
                    } else {
                        a = {compliment : 1};
                    }
                    posts.push(a);
                }

                App.saveSync(posts,syncName,false);
            });
        },
        syncMember:function(){
            var posts = [];
            var total = 0;
             var syncName = "member";
            $.when(
                App.getDataPos(App.serverBaseUrl + 'api/store/member',syncName)
            ).done(function (member) {
                // console.log("DONE  "+syncName);
                if (member !== undefined) {
                    total = member.member_category.length;
                    if (total > 0) {
                        a = {member_category : member.member_category};
                    } else {
                        a = {member_category : 1};
                    }
                    posts.push(a);

                    total = member.member.length;
                    if (total > 0) {
                        a = {member : member.member};
                    } else {
                        a = {member : 1};
                    }
                    posts.push(a);
                }

                App.saveSync(posts,syncName,false);
            });
        },
        syncFeature:function(){
            var posts = [];
            var total = 0;
            var syncName = "feature";
            $.when(
               App.getDataPos(App.serverBaseUrl + 'api/store/feature',syncName)
            ).done(function (feature) {
                 // console.log("DONE  "+syncName);
                
                if (feature !== undefined) {
                   total = feature.feature_access.length;
                    if (total > 0) {
                        a = {feature_access : feature.feature_access};
                    } else {
                        a = {feature_access : 1};
                    }
                    posts.push(a);

                    total = feature.feature.length;
                    if (total > 0) {
                        a = {feature : feature.feature};
                    } else {
                        a = {feature : 1};
                    }
                    posts.push(a); 
                }
                
                App.saveSync(posts,syncName,false);
            });
           
        },
    
        syncStoreData:function(isDone){
            var posts = [];
            var total = 0;
            var syncName = "storedata";
            $.when(
               App.getDataPos(App.serverBaseUrl + 'api/store/storedata/id/' + App.storeID,syncName)
            ).done(function (storedata) {
                // console.log("DONE  "+syncName);
                if (storedata !== undefined) {
                    total = storedata.length;
                    if (total > 0) {
                        a = {store : storedata};
                    } else {
                        a = {store : 1};
                    }
                    posts.push(a);
                }
                
                App.saveSync(posts,syncName,function(){
                    App.syncOutlet();
                    App.syncFloor();
                    App.syncOrderCompany();
                });
            });
        },
        syncCategoryMenu:function(isDone){
             // console.log("SYNC CATEGORY MENU");

            var posts = [];
            var total = 0;
            var syncName = "category_menu";
            $.when(
              App.getDataPos(App.serverBaseUrl + 'api/store/category/id/' + App.storeID,syncName)
            ).done(function (category_menu) {
                // console.log("DONE  "+syncName);
                if (category_menu !== undefined) {
                    total = category_menu.length;
                    if (total > 0) {
                        a = {category : category_menu};
                    } else {
                        a = {category : 1};
                    }
                    posts.push(a);
                }
                
                App.saveSync(posts,syncName,App.syncMenu());
            });
          
        },
        syncMenu:function(isDone){
            // console.log("SYNC MENU");
 
            var posts = [];
            var total = 0;
            var syncName = "menu";
            $.when(
             App.getDataPos(App.serverBaseUrl + 'api/store/menu/id/' + App.storeID,syncName)
            ).done(function (menu) {
                // console.log("DONE  "+syncName);
                if (menu !== undefined) {
                    total = menu.length;
                    if (total > 0) {
                        a = {menu : menu};
                    } else {
                        a = {menu : 1};
                    }
                    posts.push(a);
                }
                
                App.saveSync(posts,syncName,function(){
                    App.syncInventory();
                    App.syncSideDish();
                });
            });
           
        },

        syncInventoryCategory:function(isDone){
            // console.log("SYNC INVENTORY CATEGORY");
            var posts = [];
            var total = 0;
            var syncName = "inventory_category";
            $.when(
              App.getDataPos(App.serverBaseUrl + 'api/store/inventory_category',syncName)
            ).done(function (inventory_category) {
                // console.log("DONE  "+syncName);
                if (inventory_category !== undefined) {
                    total = inventory_category.length;
                    if (total > 0) {
                        a = {inventory_category : inventory_category};
                    } else {
                        a = {inventory_category : 1};
                    }
                    posts.push(a);
                }
                
                App.saveSync(posts,syncName,App.syncInventory());
            });
        },

        syncInventoryAccount:function(isDone){
            // console.log("SYNC INVENTORY CATEGORY");
            var posts = [];
            var total = 0;
            var syncName = "inventory_account";
            $.when(
              App.getDataPos(App.serverBaseUrl + 'api/store/inventory_account',syncName)
            ).done(function (inventory_account) {
                // console.log("DONE  "+syncName);
                if (inventory_account !== undefined) {
                    total = inventory_account.length;
                    if (total > 0) {
                        a = {inventory_account : inventory_account};
                    } else {
                        a = {inventory_account : 1};
                    }
                    posts.push(a);
                }
                
                App.saveSync(posts,syncName,false);
            });
        },

        syncEnumCoaType:function(isDone){
            // console.log("SYNC INVENTORY CATEGORY");
            var posts = [];
            var total = 0;
            var syncName = "enum_coa_type";
            $.when(
              App.getDataPos(App.serverBaseUrl + 'api/store/enum_coa_type',syncName)
            ).done(function (enum_coa_type) {
                // console.log("DONE  "+syncName);
                if (enum_coa_type !== undefined) {
                    total = enum_coa_type.length;
                    if (total > 0) {
                        a = {enum_coa_type : enum_coa_type};
                    } else {
                        a = {enum_coa_type : 1};
                    }
                    posts.push(a);
                }
                
                App.saveSync(posts,syncName,false);
            });
        },

        syncInventory:function(isDone){
            // console.log("SYNC MENU");
            var posts = [];
            var total = 0;
            var syncName = "inventory";
            $.when(
                App.getDataPos(App.serverBaseUrl + 'api/store/inventory',syncName)
            ).done(function (inventory) {
                // console.log("DONE  "+syncName);
                if (inventory !== undefined) {
                    total = inventory.length;
                    if (total > 0) {
                        a = {inventory : inventory};
                    } else {
                        a = {inventory : 1};
                    }
                    posts.push(a);
                }
                
                App.saveSync(posts,syncName,App.syncMenuIngredient());
            });
        },
        syncUoms:function(isDone){
            // console.log("SYNC MENU");
            var posts = [];
            var total = 0;
            var syncName = "uom";
            $.when(
                App.getDataPos(App.serverBaseUrl + 'api/store/uoms',syncName)
            ).done(function (uom) {
                // console.log("DONE  "+syncName);
                if (uom !== undefined) {
                    total = uom.length;
                    if (total > 0) {
                        a = {uom : uom};
                    } else {
                        a = {uom : 1};
                    }
                    posts.push(a);
                }
                App.saveSync(posts,syncName,false);
            });
        },
        syncInventoryUoms:function(isDone){
            // console.log("SYNC MENU");
            var posts = [];
            var total = 0;
            var syncName = "inventory_uom";
            $.when(
                App.getDataPos(App.serverBaseUrl + 'api/store/inventory_uoms',syncName)
            ).done(function (inventory_uom) {
                // console.log("DONE  "+syncName);
                if (inventory_uom !== undefined) {
                    total = inventory_uom.length;
                    if (total > 0) {
                        a = {inventory_uom : inventory_uom};
                    } else {
                        a = {inventory_uom : 1};
                    }
                    posts.push(a);
                }
                App.saveSync(posts,syncName,false);
            });
        },
        syncAppraisalTemplate:function(isDone){
            // console.log("SYNC MENU");
            var posts = [];
            var total = 0;
            var syncName = "appraisal_template";
            $.when(
                App.getDataPos(App.serverBaseUrl + 'api/hrd/appraisal_template/id/'+App.storeID,syncName)
            ).done(function (appraisal_template) {
                // console.log("DONE  "+syncName);
                if (appraisal_template !== undefined) {
                    total = appraisal_template['appraisal_template'].length;
                    if (total > 0) {
                        a = {appraisal_template : appraisal_template};
                    } else {
                        a = {appraisal_template : 1};
                    }
                    posts.push(a);
                }
                App.saveSync(posts,syncName,false);
            });
        },
        syncAuditTemplate:function(isDone){
            // console.log("SYNC MENU");
            var posts = [];
            var total = 0;
            var syncName = "audit_template";
            $.when(
                App.getDataPos(App.serverBaseUrl + 'api/hrd/audit_template/id/'+App.storeID,syncName)
            ).done(function (audit_template) {
                // console.log("DONE  "+syncName);
                if (audit_template !== undefined) {
                    total = audit_template['audit_template'].length;
                    if (total > 0) {
                        a = {audit_template : audit_template};
                    } else {
                        a = {audit_template : 1};
                    }
                    posts.push(a);
                }
                App.saveSync(posts,syncName,false);
            });
        },
        syncJobComponent:function(isDone){
            // console.log("SYNC MENU");
            var posts = [];
            var total = 0;
            var syncName = "job_component";
            $.when(
                App.getDataPos(App.serverBaseUrl + 'api/hrd/job_components/id/'+App.storeID,syncName)
            ).done(function (job_component) {
                // console.log("DONE  "+syncName);
                if (job_component !== undefined) {
                    total = job_component.length;
                    if (total > 0) {
                        a = {job_component : job_component};
                    } else {
                        a = {job_component : 1};
                    }
                    posts.push(a);
                }
                App.saveSync(posts,syncName,false);
            });
        },
        syncJob:function(isDone){
            // console.log("SYNC MENU");
            var posts = [];
            var total = 0;
            var syncName = "job";
            $.when(
                App.getDataPos(App.serverBaseUrl + 'api/hrd/jobs/id/'+App.storeID,syncName)
            ).done(function (job) {
                // console.log("DONE  "+syncName);
                if (job !== undefined) {
                    total = job.length;
                    if (total > 0) {
                        a = {job : job};
                    } else {
                        a = {job : 1};
                    }
                    posts.push(a);
                }
                App.saveSync(posts,syncName,false);
            });
        },
        syncEmployeeAffair:function(isDone){
            // console.log("SYNC MENU");
            var posts = [];
            var total = 0;
            var syncName = "employee_affair";
            $.when(
                App.getDataPos(App.serverBaseUrl + 'api/hrd/employee_affairs/id/'+App.storeID,syncName)
            ).done(function (employee_affair) {
                // console.log("DONE  "+syncName);
                if (employee_affair !== undefined) {
                    total = employee_affair.length;
                    if (total > 0) {
                        a = {employee_affair : employee_affair};
                    } else {
                        a = {employee_affair : 1};
                    }
                    posts.push(a);
                }
                App.saveSync(posts,syncName,false);
            });
        },
        syncSalaryComponent:function(isDone){
            // console.log("SYNC MENU");
            var posts = [];
            var total = 0;
            var syncName = "salary_component";
            $.when(
                App.getDataPos(App.serverBaseUrl + 'api/hrd/salary_components/id/'+App.storeID,syncName)
            ).done(function (salary_component) {
                // console.log("DONE  "+syncName);
                if (salary_component !== undefined) {
                    total = salary_component.length;
                    if (total > 0) {
                        a = {salary_component : salary_component};
                    } else {
                        a = {salary_component : 1};
                    }
                    posts.push(a);
                }
                App.saveSync(posts,syncName,false);
            });
        },
        syncInventoryCompositions:function(isDone){
            // console.log("SYNC MENU");
            var posts = [];
            var total = 0;
            var syncName = "inventory_composition";
            $.when(
                App.getDataPos(App.serverBaseUrl + 'api/store/inventory_compositions',syncName)
            ).done(function (inventory_composition) {
                // console.log("DONE  "+syncName);
                if (inventory_composition !== undefined) {
                    total = inventory_composition.length;
                    if (total > 0) {
                        a = {inventory_composition : inventory_composition};
                    } else {
                        a = {inventory_composition : 1};
                    }
                    posts.push(a);
                }
                App.saveSync(posts,syncName,false);
            });
        },

        syncEnumCardType:function(isDone){
            // console.log("SYNC MENU");
            var posts = [];
            var total = 0;
            var syncName = "enum_card_type";
            $.when(
                App.getDataPos(App.serverBaseUrl + 'api/store/enum_card_type',syncName)
            ).done(function (enum_card_type) {
                // console.log("DONE  "+syncName);
                if (enum_card_type !== undefined) {
                    total = enum_card_type.length;
                    if (total > 0) {
                        a = {enum_card_type : enum_card_type};
                    } else {
                        a = {enum_card_type : 1};
                    }
                    posts.push(a);
                }
                App.saveSync(posts,syncName,false);
            });
        },
        syncBankAccountCard:function(isDone){
            // console.log("SYNC MENU");
            var posts = [];
            var total = 0;
            var syncName = "bank_account_card";
            $.when(
                App.getDataPos(App.serverBaseUrl + 'api/store/bank_account_card',syncName)
            ).done(function (bank_account_card) {
                // console.log("DONE  "+syncName);
                if (bank_account_card !== undefined) {
                    total = bank_account_card.length;
                    if (total > 0) {
                        a = {bank_account_card : bank_account_card};
                    } else {
                        a = {bank_account_card : 1};
                    }
                    posts.push(a);
                }
                App.saveSync(posts,syncName,false);
            });
        },
        syncSideDish:function(){
            
            var posts = [];
            var total = 0;
            var syncName = "side_dish";
            $.when(
               App.getDataPos(App.serverBaseUrl + 'api/store/side_dish/id/' + App.storeID,syncName)
            ).done(function (side_dish) {
                // console.log("DONE  "+syncName);
                if (side_dish !== undefined) {
                    total = side_dish.side_dish_ingredient.length;
                    if (total > 0) {
                        a = {side_dish_ingredient : side_dish.side_dish_ingredient};
                    } else {
                        a = {side_dish_ingredient : 1};
                    }
                    posts.push(a);

                    total = side_dish.side_dish.length;
                    if (total > 0) {
                        a = {side_dish : side_dish.side_dish};
                    } else {
                        a = {side_dish : 1};
                    }
                    posts.push(a);
                }
                
                App.saveSync(posts,syncName,false);
            });
        },
        syncMenuIngredient:function(){
            // console.log("SYNC MENU");
            var posts = [];
            var total = 0;
            var syncName = "menu_ingredient";
            $.when(
                App.getDataPos(App.serverBaseUrl + 'api/store/menu_ingredient',syncName)
            ).done(function (menu_ingredient) {
                // console.log("DONE  "+syncName);
                if (menu_ingredient !== undefined) {
                    total = menu_ingredient.length;
                    if (total > 0) {
                        a = {menu_ingredient : menu_ingredient};
                    } else {
                        a = {menu_ingredient : 1};
                    }
                    posts.push(a);
                }
                
                App.saveSync(posts,syncName,false);
            });
        },
        syncOutlet:function(){
            // console.log("SYNC OUTLET");
            var posts = [];
            var total = 0;
            var syncName = "outlet";
            $.when(
               App.getDataPos(App.serverBaseUrl + 'api/store/outlet/id/' + App.storeID,syncName)
            ).done(function (outlet) {
                // console.log("DONE  "+syncName);
                if (outlet !== undefined) {
                        total = outlet.length;
                        if (total > 0) {
                            a = {outlet : outlet};
                        } else {
                            a = {outlet : 1};
                        }
                        posts.push(a);
                    }
                App.saveSync(posts,syncName,App.syncCategoryMenu());
            });
        },
        
        syncDeliveryCost:function(){
            var posts = [];
            var total = 0;
            var syncName = "delivery_cost";
            $.when(
               App.getDataPos(App.serverBaseUrl + 'api/store/delivery_cost/id/' + App.storeID,syncName)
            ).done(function (delivery_cost) {
                // console.log("DONE  "+syncName);
                if (delivery_cost !== undefined) {
                        total = delivery_cost.length;
                        if (total > 0) {
                            a = {delivery_cost : delivery_cost};
                        } else {
                            a = {delivery_cost : 1};
                        }
                        posts.push(a);
                    }
                App.saveSync(posts,syncName,false);
            });
        },
        syncBankAccount:function(){
            var posts = [];
            var total = 0;
            var syncName = "bank_account";
            $.when(
               App.getDataPos(App.serverBaseUrl + 'api/store/bank_account/id/' + App.storeID,syncName)
            ).done(function (bank_account) {
                // console.log("DONE  "+syncName);
                if (bank_account !== undefined) {
                        total = bank_account.length;
                        if (total > 0) {
                            a = {bank_account : bank_account};
                        } else {
                            a = {bank_account : 1};
                        }
                        posts.push(a);
                    }
                App.saveSync(posts,syncName,false);
            });
        },
        syncFloor:function(){
            // console.log("SYNC FLOOR");
            var posts = [];
            var total = 0;
            var syncName = "floor";
            $.when(
               App.getDataPos(App.serverBaseUrl + 'api/store/floor/id/' + App.storeID,syncName)
            ).done(function (floor) {
                // console.log("DONE  "+syncName);
                if (floor !== undefined) {
                    total = floor.length;
                    if (total > 0) {
                        a = {floor : floor};
                    } else {
                        a = {floor : 1};
                    }
                    posts.push(a);
                }
                App.saveSync(posts,syncName,App.syncTable());
            });
        },
        syncOrderCompany:function(){
            // console.log("SYNC FLOOR");
            var posts = [];
            var total = 0;
            var syncName = "Order Company";
            $.when(
               App.getDataPos(App.serverBaseUrl + 'api/store/order_company/id/' + App.storeID,syncName)
            ).done(function (order_company) {
               console.log("DONE  "+syncName);
               console.log(order_company);
               console.log(order_company.length);
                if (order_company !== undefined) {
                    total = order_company.length;
                    if (total > 0) {
                        a = {order_company : order_company};
                    } else {
                        a = {order_company : 1};
                    }
                    posts.push(a);
                }
                console.log(posts);
                App.saveSync(posts,syncName,false);
            });
        },
        syncTable:function(){
            // console.log("SYNC FLOOR");
            var posts = [];
            var total = 0;
            var syncName = "table";
            $.when(
               App.getDataPos(App.serverBaseUrl + 'api/store/table/id/' + App.storeID,syncName)
            ).done(function (table) {
                // console.log("DONE  "+syncName);
                if (table !== undefined) {
                    total = table.length;
                    if (total > 0) {
                        a = {table : table};
                    } else {
                        a = {table : 1};
                    }
                    posts.push(a);
                }
                App.saveSync(posts,syncName,false);
            });
        },
		syncInventoryConvertion:function(){
            // console.log("SYNC Konversi Inventory");
            var posts = [];
            var total = 0;
            var syncName = "inventory_convertion";
            $.when(
                App.getDataPos(App.serverBaseUrl + 'api/store/inventory_convertion/id/' + App.storeID,syncName)
            ).done(function (inventory_convertion) {
                // console.log("DONE  "+syncName);
                if (inventory_convertion !== undefined) {
                    total = inventory_convertion.length;
                    if (total > 0) {
                        a = {inventory_convertion : inventory_convertion};
                    } else {
                        a = {inventory_convertion : 1};
                    }
                    posts.push(a);
                }
                
                App.saveSync(posts,syncName,false);
            });
        },
        syncGeneralExpenses:function(){
            // console.log("SYNC OUTLET");
            var posts = [];
            var total = 0;
            var syncName = "general_expenses";
            $.when(
               App.getDataPos(App.serverBaseUrl + 'api/store/general_expenses/id/' + App.storeID,syncName)
            ).done(function (general_expenses) {
                // console.log("DONE  "+syncName);
                if (general_expenses !== undefined) {
                        total = general_expenses.length;
                        if (total > 0) {
                            a = {general_expenses : general_expenses};
                        } else {
                            a = {general_expenses : 1};
                        }
                        posts.push(a);
                    }
                App.saveSync(posts,syncName,false);
            });
        },
        syncRestoTransactionConfiguration:function(){
            // console.log("SYNC TRANSACTION CONFIGURATION");
            var posts = [];
            var total = 0;
            var syncName = "transaction_configuration";
            $.when(
                App.getDataPos(App.serverBaseUrl + 'api/store/transaction_configuration/id/' + App.storeID,syncName)
            ).done(function (transaction_configuration) {
                // console.log("DONE  "+syncName);
                if (transaction_configuration !== undefined) {
                    total = transaction_configuration.length;
                    if (total > 0) {
                        a = {transaction_configuration : transaction_configuration};
                    } else {
                        a = {transaction_configuration : 1};
                    }
                    posts.push(a);
                }
                
                App.saveSync(posts,syncName,false);
            });
        },
        syncModulesDueData:function(){
            var posts = [];
            var total = 0;
            var syncName = "modules_due_date";
            $.when(
                App.getDataPos(App.serverBaseUrl + 'api/module/list/store_id/' + App.storeID,syncName)
            ).done(function (list) {
				console.log("list", list);
                if (list.status) {
                    total = list.data.length;
                    if (total > 0) {
                        a = {modules_due_date : list.data};
                    } else {
                        a = {modules_due_date : 1};
                    }
                    posts.push(a);
                }
                
                App.saveSync(posts,syncName,false);
            });
        },
        saveSync:function(posts,syncName,isDone){
            $.each(posts, function (i, data) {
                var request = $.ajax({
                    type : 'POST',
                    data : data,
                    url  : $('#admin_url').val() + '/admin/save_sync'
                });
                request.done(function (msg) {
                   
                    if (i == (posts.length - 1)) {
                        App.indexTotalExecuteSync++;
                        console.log(App.indexTotalExecuteSync+" SAVE " +syncName);
                        
                        //if have callback
                        if(isDone) return isDone();
                        
                        if(App.indexTotalExecuteSync == App.totalSync){
                            console.log("SAVE ALL request");
                            App.overlayUI.hide();

                            //reset total execute
                            App.indexTotalExecuteSync = 0;
                            
                            //reset failed sync
                            App.listFailedSync = [];
                            App.indexTotalFailedSync = 0;
                            setTimeout(function() {
                                $( "#progressbar" ).progressbar({
                                    value: 100
                                });
                                $("#popup-loading").modal("hide");
                                App.alert('Sync Success');
                                //location.reload();
                            }, 50);
                         
                        }
                    }
                });
                request.fail(function (jqXHR, textStatus) {
                    // App.overlayUI.hide();
                    App.indexTotalExecuteSync++;
                    App.alert('sync database failed',function(){
                        window.location.reload(true);
                    });
                });
                request.always(function () {
                });
            });
            var percentageProgress = (App.indexTotalExecuteSync/App.totalSync ) * 100;
           $( "#progressbar" ).progressbar({
                value: percentageProgress
            });
        },
        verificationPrivateKey:function(){
          url=App.serverBaseUrl+"api/store/check_private_key/ppk/"+App.privateKey;
          return $.ajax({
            url         : url,
            dataType    : 'jsonp',
            timeout     : 30000,
            crossDomain : true
          }).done(function (data, textStatus, jqXHR) {
            if(data.id!=undefined){ 
                var html = '<div class="modal fade" id="popup-loading" tabindex="99999" data-backdrop="static" data-keyboard="false" style="z-index:100000 !important;" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">'+
                          '<div class="modal-dialog">'+
                            '<div class="modal-content">'+
                              
                              '<div class="modal-body">'+ 
                               '<div id="progressbar"></div>'+
                              '</div>'+ 
                            '</div>'+
                         ' </div>'+
                        '</div>';
            
                $("body").append(html);
                $( "#progressbar" ).progressbar({
                  value: 0
                });
                $("#popup-loading").modal("show");
                 
                App.storeID =  data.id;
                App.updateMasterGeneralSetting(App.storeID);
                App.syncDatabaseFromServer();
                
            }else{
              App.alert("Incorrect Private key!");
            }
          }).fail(function (jqXHR, textStatus, errorThrown) {
            App.alert("Failes request to server,please try again!");
          });
        },
        updateMasterGeneralSetting:function(store_id){
            var url = App.baseUrl+'admincms/system/update_master_general_setting';
            var request = $.ajax({
                type : 'POST',
                url  : url,
                data : {store_id:store_id}
            });
            request.done(function (msg) { 
            });
            request.fail(function (jqXHR, textStatus) {
            });
            request.always(function () {
            });
        },
        syncDatabaseFromServer : function () {
            App.overlayUI.show();
            App.syncGroups();
            App.syncUser();
            App.syncStoreData();
            App.syncTax();
            App.syncCompliment();
            App.syncMember();
            App.syncPromo();
            App.syncVoucher();
            App.syncSupplier();
            App.syncAccount();
            App.syncFeature();  
            App.syncDeliveryCost();  
            App.syncBankAccount();  
            App.syncUoms();
            App.syncInventoryUoms();
            App.syncEnumCardType();
            App.syncBankAccountCard();
            App.syncInventoryCompositions();
            App.syncJob();
            App.syncEmployeeAffair();
            App.syncSalaryComponent();
            App.syncJobComponent();
            App.syncAppraisalTemplate();
            App.syncAuditTemplate();
            App.syncRestoTransactionConfiguration();
            App.syncInventoryConvertion();
            App.syncInventoryCategory();
            App.syncInventoryAccount();
            App.syncEnumCoaType();
            App.syncGeneralExpenses();            
            App.syncModulesDueData();
            App.syncDeliveryCompany();
        },
        getDataPos             : function (url,syncName) {
            return $.ajax({
                url         : url,
                dataType    : 'jsonp',
                timeout     : 20000, // 2 seconds timeout
                crossDomain : true
            }).done(function (data, textStatus, jqXHR) {
                if(App.indexTotalFailedSync > 0){
                    App.listFailedSync.splice(App.listFailedSync.indexOf(syncName),1);
                    App.indexTotalFailedSync--;    
                }
                
                
                if(App.indexTotalExecuteSync == App.totalSync){
                    App.listFailedSync = [];
                    App.indexTotalFailedSync = 0;
                }  
            }).fail(function (jqXHR, textStatus, errorThrown) {
                
                console.log("FAILED "+syncName);
                App.indexTotalFailedSync++;
                App.listFailedSync.push(syncName);
                if(App.indexTotalFailedSync > 0){
                    App.resync(syncName);
                    App.listFailedSync.splice(App.listFailedSync.indexOf(syncName),1);
                    App.indexTotalFailedSync--;
                }else{
                    App.overlayUI.hide();
                    App.listFailedSync = [];
                    App.indexTotalFailedSync = 0;
                }

            });

        },
        resync:function(syncName){
            console.log("resync "+syncName);
            switch (syncName) {
                case "supplier"         :  App.syncSupplier();break;
                case "voucher"          :  App.syncVoucher();break;
                case "account"          :  App.syncAccount();break;
                case "groups"           :  App.syncGroups();break;
                case "user"             :  App.syncUser();break;
                case "feature"          :  App.syncFeature();break;
                case "promo"            :  App.syncPromo();break;
                case "member"           :  App.syncMember();break;
                case "compliment"       :  App.syncCompliment();break;
                case "tax"              :  App.syncTax();break;
                case "storedata"        :  App.syncStoreData();break;
                case "outlet"           :  App.syncOutlet();break;
                case "floor"            :  App.syncFloor();break;
                case "category_menu"    :  App.syncCategoryMenu();break;
                case "menu"             :  App.syncMenu();break;
                case "inventory"        :  App.syncInventory();break;
                case "side_dish"        :  App.syncSideDish();break;
                case "menu_ingredient"  :  App.syncMenuIngredient();break;
                case "table"            :  App.syncTable();break;
                case "order_company"    :  App.syncOrderCompany();break;
                case "delivery_cost"    :  App.syncDeliveryCost();break;
                case "uom"                        : App.syncUoms();break;
                case "inventory_uom"              : App.syncInventoryUoms();break;
                case "card_type"                  : App.syncEnumCardType();break;
                case "bank_account_card"          : App.syncBankAccountCard();break;
                case "inventory_composition"      : App.syncInventoryCompositions();break;
                case "job"                        : App.syncJob();break;
                case "employee_affair"            : App.syncEmployeeAffair();break;
                case "salary_component"           : App.syncSalaryComponent();break;
                case "job_component"              : App.syncJobComponent();break;
                case "appraisal_template"         : App.syncAppraisalTemplate();break;
                case "audit_template"             : App.syncAuditTemplate();break;
                case "transaction_configuration"  : App.syncRestoTransactionConfiguration();break;
                case "inventory_convertion"       : App.syncInventoryConvertion();break;
                case "general_expenses"           : App.syncGeneralExpenses();break;
                case "inventory_category"         : App.syncInventoryCategory();break;
                case "inventory_account"          : App.syncInventoryAccount();break;
                case "coa_type"                   : App.syncEnumCoaType();break;
                case "modules_due_date"           : App.syncModulesDueData();break;
                case "delivery_company"           : App.syncDeliveryCompany();break;
            }
             
        },
        tableCanvas            : function () {

            /*var Default = {
             fill   : '#000000',
             stroke : null
             //hasBorders : false,
             //cornerColor: 'black',
             //transparentCorners: false,
             //cornerSize: 6
             //strokeWidth: $('[name="stroke-width"]').val() || 1,
             };

             $('.control-add').on('click', function (e) {
             var control = $(this).data('control');

             switch (control) {
             case 'rectangle':
             var i = 0;
             canvas.defaultCursor = 'crosshair';
             canvas.on('mouse:down', function (o) {
             i++;
             if (i < 2) {
             var pointer = canvas.getPointer(o.e);
             origX = pointer.x;
             origY = pointer.y;
             var rectangle = new fabric.Rect($.extend({}, Default, {
             left   : origX - 75,
             top    : origY - 75,
             width  : 150,
             height : 150
             }));


             canvas.add(rectangle);
             canvas.setActiveObject(rectangle);

             }
             });
             break;

             case 'circle':
             var i = 0;
             canvas.defaultCursor = 'crosshair';
             canvas.on('mouse:down', function (o) {
             i++;
             if (i < 2) {
             var pointer = canvas.getPointer(o.e);
             origX = pointer.x;
             origY = pointer.y;
             var circle = new fabric.Circle($.extend({}, Default, {
             left   : origX - 75,
             top    : origY - 75,
             radius : 75
             }));
             canvas.add(circle);
             canvas.setActiveObject(circle);
             }
             });
             break;

             case 'triangle':
             var i = 0;
             canvas.defaultCursor = 'crosshair';
             canvas.on('mouse:down', function (o) {
             i++;
             if (i < 2) {
             var pointer = canvas.getPointer(o.e);
             origX = pointer.x;
             origY = pointer.y;
             var triangle = new fabric.Triangle($.extend({}, Default, {
             left   : origX - 75,
             top    : origY - 75,
             width  : 150,
             height : 150
             }));
             canvas.add(triangle);
             canvas.setActiveObject(triangle);
             }
             });
             break;

             case 'line':
             var i = 0;
             canvas.defaultCursor = 'crosshair';
             canvas.on('mouse:down', function (o) {
             i++;
             if (i < 2) {
             var pointer = canvas.getPointer(o.e);
             origX = pointer.x;
             origY = pointer.y;
             var line = new fabric.Line([50, 100, 200, 200], $.extend({}, Default, {
             left : origX,
             top  : origY
             }));
             canvas.add(line);
             canvas.setActiveObject(line);
             }

             });
             break;
             }
             e.preventDefault();
             });

             canvas.on('object:selected', function (o) {

             }).on('selection:cleared', function () {

             }).on('object:added', function (o) {
             canvas.defaultCursor = 'default';
             var objtype = o.target.get('type');

             }).on('object:removed', function (o) {

             });*/
        },
        initEvent:function(){
            
            $('#split-choose-right').on('click', function (e){
                 var data = App.extractDataOrder($('.highlight-bill-left').get(0));
                if( typeof data.product_id == "undefined") return;
                $('.popup-choose-left').hide();
                $('.popup-choose-right').show();
                $("#right_value").val("");
                $('.popup-choose-right').css("left",$(this).offset().left - $('.popup-choose-right').width()/2 + 20);
                $('.popup-choose-right').css("top",$(this).offset().top - $('.popup-choose-right').height() - 20);
            });

            $('#split-choose-left').on('click', function (e){
                 var data = App.extractDataOrder($('.highlight-bill-right').get(0));
                if( typeof data.product_id == "undefined") return;
                $('.popup-choose-right').hide();
                $('.popup-choose-left').show();
                $("#left_value").val("");
                $('.popup-choose-left').css("left",$(this).offset().left - $('.popup-choose-left').width()/2 + 20);
                $('.popup-choose-left').css("top",$(this).offset().top - $('.popup-choose-left').height() - 20);
            });
            $('.btn-cancel').click(function () {
                $('.popup-block').hide();
                return;
            });
           
            /*$('.qty-input').on('keydown', function (e) {
                // Allow: backspace, delete, tab, escape, enter
                if ($.inArray(e.keyCode, [46, 8, 9, 27, 13]) !== -1 ||
                        // Allow: Ctrl+A
                    (e.keyCode == 65 && e.ctrlKey === true) ||
                        // Allow: home, end, left, right
                    (e.keyCode >= 35 && e.keyCode <= 39)) {
                    // let it happen, don't do anything
                    return;
                }
                // Ensure that it is a number and stop the keypress
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }
            });*/
           

            $('#store_id').on('change', function (e){
                var url = App.baseUrl+'admincms/outlet/get_outlet_not_id';
                var store_id = $(this).val();
                var outlet_id = $("#origin_outlet_id").val();
                if(store_id == 0 ){
                     $("#outlet_id").html(" ");
                     $("#outlet_id").prop("disabled", true);
                }else{
                    $("#outlet_id").prop("disabled", false);  
                }
                var request = $.ajax({
                    type : 'GET',
                    url  : url,
                    data : {outlet_id :outlet_id,store_id:store_id}
                });
                request.done(function (msg) {
                     $("#outlet_id").html("");
                    var parsedObject = JSON.parse(msg);
                    console.log(parsedObject);
                    var html =  "";
                    for (var i = 0; i < parsedObject.length; i++) {
                        html += "<option value='"+parsedObject[i].id+"'>"+parsedObject[i].outlet_name+"</option>";
                    };
                    
                      
                    $("#outlet_id").append(html);
                });
                request.fail(function (jqXHR, textStatus) {
                });
                request.always(function () {
                });
            });


            var timeout_id = 0,idTable = "";
            $(".bill-table").on('click', 'tbody  > tr', function () {
                idTable = $(this).parents("table:first").attr("id");
               
                if(idTable === "bill-table-left"){
                    $(this).parents("table:first").find("tbody  > tr").removeClass("highlight-bill-left");
                    $("#bill-table-right").find("tbody  > tr").find("td").removeClass("highlight");
                     $("#bill-table-right").find("tbody  > tr").removeAttr("class");
                    $(this).addClass("highlight-bill-left");
                    $(this).find('td').addClass("highlight");
                }else{
                    $("#bill-table-left").find("tbody  > tr").find("td").removeClass("highlight");
                    $(this).parents("table:first").find("tbody  > tr").removeClass("highlight-bill-right");
                     $("#bill-table-left").find("tbody  > tr").removeAttr("class");
                    $(this).addClass("highlight-bill-right");
                    $(this).find('td').addClass("highlight");
                }
            }).bind('mouseup',  function () {
                // $(this).find('tr').removeClass('highlight-bill');
                $(this).find('td').removeClass("highlight");
                clearTimeout(timeout_id);
            });

            $('#split-all-right').on('click', function (e){               
                var data = App.extractDataOrder($('.highlight-bill-left').get(0));
                if( typeof data.product_id == "undefined") return;
                App.sendToRight(data,App.enumOrder.ALL,$('.highlight-bill-left').get(0),false);
                App.resetHighlight();
                $("#bill-table-right").find("tbody  > tr").find('td').removeClass("highlight");
                App.updateTransfersField();
            });
            $('#split-single-right').on('click', function (e){   
                var data = App.extractDataOrder($('.highlight-bill-left').get(0));
                if( typeof data.product_id === "undefined") return;     
                App.sendToRight(data,App.enumOrder.SINGLE,$('.highlight-bill-left').get(0),1);
                $("#bill-table-right").find("tbody  > tr").find('td').removeClass("highlight");
                App.updateTransfersField();
            });
            $('#btn-ok-right').on('click', function (e){  
                var data = App.extractDataOrder($('.highlight-bill-left').get(0));
                if( typeof data.product_id == "undefined") return;
                if($("#right_value").val().length === 0 ) return;
                var amount = parseFloat($("#right_value").val());
                App.sendToRight(data,App.enumOrder.SINGLE,$('.highlight-bill-left').get(0),amount);
                
                $("#bill-table-right").find("tbody  > tr").find('td').removeClass("highlight");
                
                App.updateTransfersField();
            });


            $('#split-single-left').on('click', function (e){             
                var data = App.extractDataOrder($('.highlight-bill-right').get(0));
                if( typeof data.product_id === "undefined") return;  

                App.sendToLeft(data,App.enumOrder.SINGLE,$('.highlight-bill-right').get(0),1);
                $("#bill-table-left").find("tbody  > tr").find('td').removeClass("highlight");
                App.updateTransfersField();
            });
            $('#split-all-left').on('click', function (e){   
                var data = App.extractDataOrder($('.highlight-bill-right').get(0));
                if( typeof data.product_id === "undefined") return;      
                App.sendToLeft(data,App.enumOrder.ALL,$('.highlight-bill-right').get(0),false);
                App.resetHighlight();
                $("#bill-table-left").find("tbody  > tr").find('td').removeClass("highlight");
                App.updateTransfersField();
            });

            $('#btn-ok-left').on('click', function (e){               
                var data = App.extractDataOrder($('.highlight-bill-right').get(0));
                if( typeof data.product_id == "undefined") return;
                if($("#left_value").val().length === 0 ) return;

                var amount = parseInt($("#left_value").val());
             
                App.sendToLeft(data,App.enumOrder.SINGLE,$('.highlight-bill-right').get(0),amount);
                $("#bill-table-left").find("tbody  > tr").find('td').removeClass("highlight");
                App.updateTransfersField();
            });

            $('#payment_bon_date').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'YYYY-MM-DD',
            });

        },
        sendToLeft:function(data,action,object,amount){
            var findRightData = App.findRightOrder(data);
            var findLeftData = App.findLeftOrder(data);
            var cloneRight = object;
            
            if(findRightData.product_amount < amount) return;

            if(findLeftData){
                if(action === App.enumOrder.SINGLE){
                    findLeftData.product_amount += amount;
                }else{
                    findLeftData.product_amount += data.product_amount;
                }
 
                
                var rightElement = $('#bill-table-left').find("tbody  > tr");
                console.log($(rightElement).length);
                for (var i = 0; i < $(rightElement).length; i++) {
                    var child = $(rightElement)[i];
                    if($(child).attr("id") === findRightData.product_id){
                        $(child).children().eq(1).html(findLeftData.product_amount);
                    }
                     
                };
            }else{
                if(action === App.enumOrder.SINGLE){
                    data.product_amount = amount;
                } 
               
                App.leftDataOrder.push(data);
                
                var cloneRight2 = $(cloneRight).clone();
                $(cloneRight2).children().eq(1).html(data.product_amount);
                $(cloneRight2).removeClass("highlight-bill-right");
              
                $('#bill-table-left').append($(cloneRight2));
            }

            if(findRightData){
                if(action === App.enumOrder.SINGLE){
                    findRightData.product_amount -= amount;
               
                    if(findRightData.product_amount <= 0){
                        $(cloneRight).remove();
                        // AppCashier.leftDataOrder.slice(indexOf(findLeftData),1);
                        $.each(App.rightDataOrder, function(i){
                            if(App.rightDataOrder[i].product_id === findRightData.product_id) {
                                App.rightDataOrder.splice(i,1);
                                return false;
                            }
                        });
                    }else{

                        $(cloneRight).children().eq(1).html(findRightData.product_amount);
                    }
                }else{
                     $(cloneRight).remove();
                    $.each(App.rightDataOrder, function(i){
                        if(App.rightDataOrder[i].product_id === findRightData.product_id) {
                            App.rightDataOrder.splice(i,1);
                            return false;
                        }
                    });
                }
                
               
            }
        },
        sendToRight:function(data,action,object,amount){
            var findRightData = App.findRightOrder(data);
            var findLeftData = App.findLeftOrder(data);
            var cloneLeft = object;

            if(findLeftData.product_amount < amount) return;
           
            if(findRightData){
                if(action === App.enumOrder.SINGLE){
                    findRightData.product_amount += amount;    
                }else{
                    findRightData.product_amount +=data.product_amount;
                }
                
                var rightElement = $('#bill-table-right').find("tbody  > tr");
                
                for (var i = 0; i < $(rightElement).length; i++) {
                    var child = $(rightElement)[i];
                    if($(child).attr("id") === findRightData.product_id){
                        console.log($(child).children().eq(1));
                        $(child).children().eq(1).html(findRightData.product_amount);
                    }
                     
                };
            }else{
                if(action === App.enumOrder.SINGLE){
                    data.product_amount = amount;
                }
                App.rightDataOrder.push(data);
                
                var cloneLeft2 = $(cloneLeft).clone();
                
                $(cloneLeft2).children().eq(1).html(data.product_amount);
                $(cloneLeft2).removeAttr("class");
                
                $('#bill-table-right').append($(cloneLeft2));
            }

            if(findLeftData){
               console.log(findLeftData);
                if(action === App.enumOrder.SINGLE){
                    findLeftData.product_amount -= amount;
                    
                    if(findLeftData.product_amount <= 0){
                        $(cloneLeft).remove();
                        
                        $.each(App.leftDataOrder, function(i){
                            if(App.leftDataOrder[i].product_id === findLeftData.product_id) {
                                App.leftDataOrder.splice(i,1);
                                return false;
                            }
                        });
                    }else{
                        $(cloneLeft).children().eq(1).html(findLeftData.product_amount);
                    }
                }else{
                    $(cloneLeft).remove();
                     
                    $.each(App.leftDataOrder, function(i){
                        if(App.leftDataOrder[i].product_id === findLeftData.product_id) {
                            App.leftDataOrder.splice(i,1);
                            return false;
                        }
                    });
                }
               
            }
        },
        findLeftOrder:function(data){
            for (var i = 0; i < App.leftDataOrder.length; i++) {
                if(data.product_id == App.leftDataOrder[i].product_id){
                     return App.leftDataOrder[i];
                }
            }
             return false;
        },
        findRightOrder:function(data){
           for (var i = 0; i < App.rightDataOrder.length; i++) {
                if(data.product_id == App.rightDataOrder[i].product_id){
                     return App.rightDataOrder[i];
                }
            }
            return false;
        },
        extractDataOrder:function(parent){
            var data = {};
            data.product_id = $(parent).attr("id");
            for (var i = 0; i < $(parent).children().length; i++) {
                var child = $($(parent).children()[i]);
                switch (i) {
                    case 0:   data.product_name = child.text() ;
                    case 1:   data.product_amount = parseFloat(child.text());
                    case 2:   data.product_price = child.text();
                }
            };
           
            return data;
        }, 
        resetHighlight:function(){
            $("#bill-table-right").find("tbody  > tr").removeClass("highlight-bill-right");
            $("#bill-table-left").find("tbody  > tr").removeClass("highlight-bill-left");
            console.log(App.leftDataOrder);
            console.log(App.rightDataOrder);
        },
        initSetLeftOrder:function(){
            console.log("INISIALISASI DATA LEFT ORDER");
            var parent = $("#bill-table-left > tbody");
            for (var i = 0; i < $(parent).children().length; i++) {
                var child = $(parent).children()[i];
               
                var data = App.extractDataOrder(child);
                App.leftDataOrder.push(data);
            }
            console.log(App.leftDataOrder);
        },
        updateTransfersField:function(){
            console.log("UPDATE FIELD");
            $('.popup-block').hide();
            $("#transfers").val(JSON.stringify(App.rightDataOrder));
            if(App.rightDataOrder.length == 0) $("#transfers").val("");
        },
        eventOpname:function(){
            $('#tgl_opname').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'YYYY-MM-DD HH:mm' 
             });
            $('#tgl_opname').on("dp.change", function (e) {
                console.log($("#opname_date").val());
                var url = App.baseUrl+'admincms/stock/get_stock_by_date';
                var request = $.ajax({
                    type    : 'POST',
                    url     : url,
                    data    : {
                        'tgl_opname_value' : $('#opname_date').val(),
                        'inventory_id' : $('#inventory_id').val(),
                    }
                });
                request.done(function (msg) {
                    var parsedObject = JSON.parse(msg);
                    
                    var jumlah_stok = parseInt(parsedObject[0].jumlah_stok);
                    if(jumlah_stok !== "null"){
                        $("#jumlah_stok").html(jumlah_stok);
                    }else{
                         $("#jumlah_stok").html(0);
                       
                    }
                    App.updateDiffOpname();
                });
            });
            $("#quantity_opname").on("keyup",function(){
                App.updateDiffOpname();
            });
        },
        updateDiffOpname:function(){
            var jumlah_opname = 0;
            var jumlah_sistem = parseInt($("#jumlah_stok").html());
            jumlah_opname = parseInt($("#quantity_opname").val());
            var total = jumlah_sistem - jumlah_opname;
            total *= -1;
             
            $("#diff_opname").html(total);
            $("#difference").val(total);
        },
        syncDatabaseToServer:function(urlx){
            App.overlayUI.show();
            var request = $.ajax({
                type : 'POST',
                dataType:"json",
                url  :App.baseUrl + 'scheduler/'+urlx
            });
            request.done(function (msg) {
                if(msg===true){
                  App.alert("Sinkronisasi ke server berhasil dilakukan.");                  
                }else if(msg===false){
                  App.alert("Sinkronisasi ke server gagal dilakukan.");                  
                }
                App.overlayUI.hide();
            });
            request.fail(function (jqXHR, textStatus) {
                App.overlayUI.hide();
               
            });
            request.always(function () {
            });
        },
        reportReceiveStock:function(){ 
            var is_in_report_page = $("#is_in_report_page").length;
            if(!is_in_report_page) return;

             $('#start_date_picker').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'YYYY-MM-DD',
            });

             $('#end_date_picker').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'YYYY-MM-DD',
            });
            $("#start_date").on("dp.change", function (e) {

                $('#end_date_picker').datetimepicker({
                    sideBySide: true,
                    useCurrent: true,
                    format: 'YYYY-MM-DD' 
                });
                
                $('#end_date_picker').data("DateTimePicker").minDate(e.date);

            });

            
            $("#filter_submit").on('click', function (e) { 
                e.preventDefault(); 
                var table =  $('#table-report-receive').dataTable({
                    "bProcessing"    : true,
                    "bServerSide"    : true,
                    "sServerMethod"  : "POST",
                    "bFilter" :false,                   
                    "bDestroy" :true,
                    "autoWidth": false,
                   // "ordering" : false,
                    "iDisplayLength" : 10,
                    "ajax": {
                    "url": $('#dataProcessUrl').val(),
                    "type": 'POST',
                    "data": {
                      param: $('#formFilter').serialize()
                    },
                    "dataSrc": function( json ) {
                      $('#export_pdf').show();    
                        return json.data;
                    },   
                      
                 },

                 "columns"        : [
                     {data : "incoming_date"},
                     {data : "payment_no"},
                     {data : "supplier_name"},
                     {data : "inventory_name"},
                     {data : "received_quantity"},
                     {data : "price"},
                     {data : "total_per_item"} 
                 ],

                 "columnDefs"     : [
                    {
                        "targets"     : 6,
                        "orderable" : false
                    }
                ],

                "order"          : [[0, "desc"]],
               



                }); //end datatable
            
              
            });

            
            $("#export_pdf").on('click', function (e) {
                e.preventDefault();
                $.ajax({
                    url      :  "export_report_to_pdf",
                    type     : "POST",
                    dataType : "json",
                    data     : {
                        type      : 'transaction',
                        supplier_id: $('#supplier_id').val(),
                         inventory_id: $('#inventory_id').val(),
                        start_date: $('#start_date').val(),
                        end_date: $('#end_date').val()
                    },
                    success  : function (result) {
                        if (result != '') {
                            window.open(App.baseUrl + result, '_newtab')
                        }
                        else{
                            alert('Export report gagal');
                        }
                    }
                });
            });


        
        },
        topProductDashboard:function(){
          if($('#sales_by_department_chart_pie').length>0)
          {
            $('#sales_by_department_chart_pie').highcharts({
              chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
              },
              title: {
                text: 'Grafik Sales By Department'
              },
              tooltip: {
                  pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
              },
              plotOptions: {
                  pie: {
                      allowPointSelect: true,
                      cursor: 'pointer',
                      dataLabels: {
                          enabled: true,
                          format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                          style: {
                              color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                          }
                      }
                  }
              },
              series: [{
                  name: "Persentase",
                  colorByPoint: true,
                  data: sales_by_department_series_data_pie
              }]
            });
          }
          if($('#sales_by_day_chart').length>0){
            sales_by_day_chart=$('#sales_by_day_chart').highcharts({
              chart   : {
                type : 'column'
              },
              credits : {
                enabled : false
              },
              title   : {
                text : 'Grafik Sales By Day Report'
              },
              xAxis   : {
                categories:sales_by_day_categories_data,
                crosshair:true,
                labels : {
                  rotation : -45,
                  style    : {
                    fontSize   : '13px',
                    fontFamily : 'Verdana, sans-serif'
                  }
                }
              },
              tooltip:{
                valueDecimals:0,
                valuePrefix: 'Rp. '
              },
              yAxis   : {
                title : {
                  text : 'Jumlah Revenue'
                }
              },
              legend  : {
                enabled : false
              },
              series  : [{
                name       : 'Revenue',
                data       : sales_by_day_series_data
              }]
            });
          }
          if($('#customer_by_day_chart').length>0){
            customer_by_day_chart=$('#customer_by_day_chart').highcharts({
              chart   : {
                type : 'column'
              },
              credits : {
                enabled : false
              },
              title   : {
                text : 'Grafik Customer By Day Report'
              },
              xAxis   : {
                categories:customer_by_day_categories_data,
                crosshair:true,
                labels : {
                  rotation : -45,
                  style    : {
                    fontSize   : '13px',
                    fontFamily : 'Verdana, sans-serif'
                  }
                }
              },
              tooltip:{
                valueDecimals:0,
                valuePrefix: ''
              },
              yAxis   : {
                title : {
                  text : 'Jumlah Pelanggan'
                }
              },
              legend  : {
                enabled : false
              },
              series  : [{
                name       : 'Pelanggan',
                data       : customer_by_day_series_data
              }]
            });
          }
          if($('#sales_by_waiter_chart').length>0)
          {
            sales_by_waiter_chart=$('#sales_by_waiter_chart').highcharts({
              chart: {
                type: 'bar'
              },
              title   : {
                text : 'Grafik Sales By Waiter'
              },
              xAxis   : {
                categories:sales_by_waiter_categories_data,
                title:null
              },
              yAxis: {
                min: 0,
                title: {
                    text: 'Jumlah Revenue',
                    align: 'high'
                },
                labels: {
                    overflow: 'justify'
                }
              },
              tooltip: {
                valueDecimals:0,
                valuePrefix: 'Rp. '
              },
              plotOptions: {
                bar: {
                  dataLabels: {
                    enabled: true
                  }
                }
              },
              legend: {
                  layout: 'vertical',
                  align: 'right',
                  verticalAlign: 'top',
                  x: -40,
                  y: 80,
                  floating: true,
                  borderWidth: 1,
                  backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'),
                  shadow: true
              },
              credits: {
                  enabled: false
              },
              series: [{
                  name: 'Revenue',
                  data: sales_by_waiter_series_data
              }]
            });
          }
          $("#goto_sales_by_waiter,#goto_sales_by_department").click(function(){
            url=$(this).attr("url");
            $("#form_dashboard").attr("action",url);
            $("#form_dashboard").attr("target","_blank");
            $("#form_dashboard").submit();
          });
        }
    }
})
;