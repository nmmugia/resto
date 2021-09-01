/**
 * Created by alta falconeri on 12/15/2014.
 */
var b;
define([
    "jquery",
    "jquery-ui",
    'datatables',
    "bootstrap",
    "datatables-bootstrap",
    "bootstrap",
    "select2",
    "qtip"
], function ($, ui) {
    return {
        nodeUrl             : $('#node_url').val(),
        baseUrl             : $('#base_url').val(),
        outletId            : $('#outlet_id').val(),
        theme               : $('#theme').val(),
        socket              : false,
        loadingOverlay      : $("#cover"),
        diningType          : $('#dining_type').val(),
        countKitchenProcess : $('#count_kitchen_process').val(),
        useRoleChecker      : $("#use_role_checker").val(),
        useChecklistOrder   : parseInt($("#use_checking_order").val()),
        init                : function () {
            console.log("App Kitchen inited..");
            AppKitchen.loadingOverlay.show();
            AppKitchen.initFunc(AppKitchen);
            try {
                AppKitchen.initSocketIO();
            } catch (err) {
                // AppKitchen.loadingOverlay.hide();
                AppKitchen.alert($('#server-error-message p').text());
                // $('#server-error-message').dialog(
                    // {
                        // dialogClass   : "no-close",
                        // modal         : true,
                        // closeOnEscape : false,
                        // buttons       : {
                            // Reload : function () {
                                // window.location.reload(true);
                            // }
                        // }
                    // }
                // );
            }
            AppKitchen.inventoryEvent();
        },
        inventoryEvent:function(){
          $(document).on("change","#inventory_id",function(){
            inventory_id=$(this).val();
            el=this;
            if(inventory_id!=""){
              uom_id=$(this).find("option:selected").attr("uom_id");
              $.ajax({
                url:AppKitchen.baseUrl+"admincms/inventory/get_inventory_uoms",
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
           $('#dataTables-inventory-history').dataTable({
              "bProcessing"    : true,
              "bServerSide"    : true,
              "sServerMethod"  : "POST",
              "ajax"           : $('#dataProcessUrl').val(),
              "iDisplayLength" : 10,
              "columns"        : [
                {data : "join_name"},
                {
                      "data": "total_used", // can be null or undefined
                      "defaultContent": 0
                    },
                {
                      "data": "total_spoiled", // can be null or undefined
                      "defaultContent": 0
                },
                {
                      "data": "sisa_stok", // can be null or undefined
                      "defaultContent": 0
                    },
              ],
              "columnDefs"     : [
                {
                  "targets"     : [1,2,3],
                  "orderable"   : false,
                  "bSearchable" : false,
                  "class"       : 'center-tr'
                }
              ]
            });

            $("#save-spoiled").on("click",function(){
              var inventory_id = $("#inventory_id").val();
              var uom_id = $("#uom_id").val();
              var quantity = $("#quantity").val();
              var description = $("#description").val();

              if(quantity.length === 0) {


                AppKitchen.alert("Jumlah Stok harus diisi",function(){ return;}); 
              }else{
                var url = "save_spoiled";
                  $.ajax({
                    url:url, 
                      type : 'POST',
                    data:{inventory_id:inventory_id,quantity:quantity,uom_id:uom_id,description:description},
                    success:function(response){
                          var parsedObject = JSON.parse(response);
                           AppKitchen.alert(parsedObject.message);
                    }
                  }); 
              }
              
            });
        },

        initSocketIO        : function () {
		      AppKitchen.socket = io(AppKitchen.nodeUrl,{
              'reconnectionAttempts': 2
          });
          AppKitchen.socket.on('reconnect_failed', function () {
            AppTable.alert($('#server-error-message p').text());
            window.location.reload(true);
          });
		      AppKitchen.socket.on('sm_notify_cooking_status_refresh', function (data) {
            // window.location = AppKitchen.baseUrl+'kitchen'; 
            AppKitchen.loadingOverlay.show();
            AppKitchen.getDataKitchenMode2();
            AppKitchen.loadingOverlay.hide();
          });

          AppKitchen.socket.on('sm_notify_change_table', function(data) {
            console.log(data);
            AppKitchen.getDataKitchenMode2();
          });
				
          AppKitchen.socket.on('connected', function (data) {
            AppKitchen.socket.emit('cm_get_checker_ip_address');
            console.log('Socket.IO connected');
            
            AppKitchen.socket.on('sm_get_checker_ip_address', function (data) {
              html="";
              for(x=0;x<data.checker_ip_address.length;x++){
                  html+='<li><a href="#" class="btn-mode2-post" checker-number="'+data.checker_ip_address[x]+'">'+data.checker_ip_address[x]+'</a></li>';
              }
              $(".list-checker-ip").html(html);
            });

            AppKitchen.socket.on('sm_notify_new_order', function (data) {
              if(data.outlets!=undefined && data.outlets.indexOf(parseInt(AppKitchen.outletId))>=0){
                AppKitchen.getDataKitchenMode2();
                $("#bgsound_notification").get(0).play();
              } else {
                AppKitchen.getDataKitchenMode2();
              }
            });
				
    				AppKitchen.socket.on('sm_empty_table', function (data) {
    					var orderId = data.order_id;
    				});
				
            AppKitchen.initUIEvent();
            AppKitchen.loadingOverlay.hide();
          });
			
        },

        getDataKitchenMode2:function(){
          var url=AppKitchen.baseUrl+"kitchen/get_data_left_right";
          $.ajax({
            url:url,
            dataType:"JSON",
            success:function(response){
              if(response.theme==1){
                $("#default_mode_content").html(response.content);
              }else{
                $("#regular_content").html(response.content_left);
                $("#additional_content").html(response.content_right);
                AppKitchen.socket.emit('cm_get_checker_ip_address');
              }
              $('[title!=""]').qtip();
            }
          })
        },
        initUIEvent         : function () {
						$(".select2").select2()
            if($(".slide-down").length>0){
              console.log("asdasdsadsa");
              $("nav").hide();
              $("#header_store").hide();
            }
            $(document).on("click",".slide-up",function(){
              $(this).removeClass("slide-up").addClass("slide-down");
              slide="slide-down";
              $.ajax({
                url:AppKitchen.baseUrl + "kitchen/set_slide_setting",
                data:{slide:slide}
              });
              $(this).find("i").removeClass("fa-arrow-up").addClass("fa-arrow-down");
              $("nav").hide();
              $("#header_store").hide();
            });
            $(document).on("click",".slide-down",function(){
              $(this).removeClass("slide-down").addClass("slide-up");
              slide="slide-up";
              $.ajax({
                url:AppKitchen.baseUrl + "kitchen/set_slide_setting",
                data:{slide:slide}
              });
              $(this).find("i").removeClass("fa-arrow-down").addClass("fa-arrow-up");
              $("nav").show();
              $("#header_store").show();
            });
            $(document).on("click",".print_list_menu",function(){
              order_id=$(this).attr("order-id");
              cooking_status=$(this).attr("cooking-status");
              AppKitchen.loadingOverlay.show();
              url=AppKitchen.baseUrl + 'kitchen/print_list_menu';
              order_menu_ids=new Array();
              if(AppKitchen.theme==1){
                $(this).parents(".title-bg-kitchen:first").next().find(".kitchen-table tbody input#menu_order_id").each(function(){
                  order_menu_ids.push($(this).val());
                });
              }else{
                $(this).parents("li:first").find(".kitchen-table tbody input#menu_order_id").each(function(){
                  order_menu_ids.push($(this).val());
                });                
              }
              var request = $.ajax({
                type    : 'POST',
                url     : url,
                data    : {'order_id':order_id,'cooking_status':cooking_status,order_menu_ids:order_menu_ids}
              });
							request.done(function () {
								AppKitchen.loadingOverlay.hide();
							});
							request.fail(function () {
								AppKitchen.loadingOverlay.hide();
							});
            }); 
            $(document).on("click","#pagination_left .pagination a",function(){
              var url=$(this).attr("href");
              $.ajax({
                url:url,
                dataType:"JSON",
                success:function(response){
                  $("#regular_content").html(response.content);
                }
              })
              return false;
            });
            $(document).on('click',".choice_type",function(){
              value=$(this).val();
              is_checked=$(this).is(":checked");
              $.ajax({
                url:AppKitchen.baseUrl + "kitchen/set_choice_type",
                data:{is_checked:is_checked,value:value},
                success:function(response){
                  window.location.reload();
                }
              });
            });
            $(document).on("click","#pagination_right .pagination a",function(){
              var url=$(this).attr("href");
              $.ajax({
                url:url,
                dataType:"JSON",
                success:function(response){
                  $("#additional_content").html(response.content);
                }
              })
              return false;
            });
            $(document).on('click','.btn-cooking',function (e) {
                e.preventDefault();
                console.log('button cooking pressed...');
				
                var row = $(this).closest("tr");
                var arrTable = row.children();
                var tableId = $(this).closest('table').attr('table-id');				
                
                $.ajax({
                  type : "POST",
                  url  : AppKitchen.baseUrl + 'kitchen/update_cooking_status',
                  data : {
                    order_menu_id  : $(arrTable[6]).val(),
                    cooking_status : 2,
                    table_id	: tableId						
                  }
                }).done(function (resp) {
                  console.log(resp);
                  if (resp != '') {
                    var parsedObject = JSON.parse(resp);
                    AppKitchen.socket.emit('cm_notify_cooking_status', {
                      order_menu_id : parsedObject[0].order_menu_id,
                      cooking_status : parsedObject[0].cooking_status,
                      status_name  : parsedObject[0].status_name,
                      order_id : parsedObject[0].order_id,
                      table_id	: tableId,
                      arr_merge_table: parsedObject[0].arr_merge_table
                    });

                    // var buttonReady = $(arrTable[5]).find('.btn-ready');
                    // buttonReady.attr("disabled",false);
                    // var buttonCooking = $(arrTable[5]).find('.btn-cooking');
                    // buttonCooking.attr("disabled",true);
                    // $(arrTable[5]).find('.btn-delete').remove();
                    $(arrTable[5]).html('<button class="btn btn-status btn-ready"><img src="'+AppKitchen.baseUrl+'/assets/img/ico-ready.png"></button>');
                    
                    $(arrTable[4]).html("<center>"+parsedObject[0].status_name+"</center>");
                  }
                }).fail(function () {
                  AppKitchen.alert('Gagal memperbaharui status. Silahkan coba lagi. Jika masalah masih terjadi mohon hubungi administrator.');
                });
            });

            $(document).on('click', '.btn-mode2-countup', function(e) {
              AppKitchen.loadingOverlay.show();
              e.preventDefault();

              var tableId = $(this).parents('table').attr('table-id');        
              var order_menu_id = $(this).parents("tr").find("#menu_order_id").val();
              var order_package_menu_id = parseInt($(this).parents("tr").find("#menu_order_id").attr("order_package_menu_id"));
              if(isNaN(order_package_menu_id))order_package_menu_id=0;
              var el=this;

              $.ajax({
                type : "POST",
                url  : AppKitchen.baseUrl + 'kitchen/update_cooking_process',
                data : {
                  order_menu_id : order_menu_id,
                  order_package_menu_id : order_package_menu_id,
                  table_id : tableId,
                  process : 'up'
                }
              }).done(function(response) {
                if (response != '') {
                  var parsedObject = JSON.parse(response);
                  if (parsedObject.quantity_process == parsedObject.quantity) {
                    $('#up-quantity'+order_menu_id).attr('disabled', true);
                  }
                  $(el).siblings(".count-cooking").text(parsedObject.quantity_process);
                  $(el).siblings("#down-quantity"+order_menu_id).removeAttr('disabled');
                  $("#cooking-quantity"+order_menu_id).val(parsedObject.quantity_process);
                }
                AppKitchen.loadingOverlay.hide();
              }).fail(function () {
                AppKitchen.alert('Gagal memperbaharui status. Silahkan coba lagi. Jika masalah masih terjadi mohon hubungi administrator.');
                AppKitchen.loadingOverlay.hide();
              });
            });

            $(document).on('click', '.btn-mode2-countdown', function(e) {
              AppKitchen.loadingOverlay.show();
              e.preventDefault();

              var tableId = $(this).parents('table').attr('table-id');        
              var order_menu_id = $(this).parents("tr").find("#menu_order_id").val();
              var order_package_menu_id = parseInt($(this).parents("tr").find("#menu_order_id").attr("order_package_menu_id"));
              if(isNaN(order_package_menu_id))order_package_menu_id=0;
              var el=this;

              $.ajax({
                type : "POST",
                url  : AppKitchen.baseUrl + 'kitchen/update_cooking_process',
                data : {
                  order_menu_id : order_menu_id,
                  order_package_menu_id : order_package_menu_id,
                  table_id : tableId,
                  process : 'down'
                }
              }).done(function(response) {
                if (response != '') {
                  var parsedObject = JSON.parse(response);
                  if (parsedObject.quantity_process == 0) {
                    $('#down-quantity'+order_menu_id).attr('disabled', true);
                  }
                  $(el).siblings(".count-cooking").text(parsedObject.quantity_process);
                  $(el).siblings("#up-quantity"+order_menu_id).removeAttr('disabled');
                  $("#cooking-quantity"+order_menu_id).val(parsedObject.quantity_process);
                }
                AppKitchen.loadingOverlay.hide();
              }).fail(function () {
                AppKitchen.alert('Gagal memperbaharui status. Silahkan coba lagi. Jika masalah masih terjadi mohon hubungi administrator.');
                AppKitchen.loadingOverlay.hide();
              });
            });

            $(document).on('click','.btn-mode2-cooking',function (e) {
                AppKitchen.loadingOverlay.show();
                if($(this).hasClass("active")){
                  cooking_status=1;
                }else{
                  cooking_status=2;
                }
                e.preventDefault();
                var tableId = $(this).parents('table').attr('table-id');				
                var order_menu_id=$(this).parents("tr").find("#menu_order_id").val();
                var order_package_menu_id=parseInt($(this).parents("tr").find("#menu_order_id").attr("order_package_menu_id"));
								if(isNaN(order_package_menu_id))order_package_menu_id=0;
                var el=this;
                $.ajax({
                  type : "POST",
                  url  : AppKitchen.baseUrl + 'kitchen/update_cooking_status',
                  data : {
                    order_menu_id  : order_menu_id,
                    order_package_menu_id  : order_package_menu_id,
                    cooking_status : cooking_status,
                    table_id	: tableId						
                  }
                }).done(function (resp) {
                  if (resp != '') {
                    var parsedObject = JSON.parse(resp);
                    AppKitchen.socket.emit('cm_notify_cooking_status', {
                      order_menu_id : parsedObject[0].order_menu_id,
                      cooking_status : parsedObject[0].cooking_status,
                      status_name  : parsedObject[0].status_name,
                      order_id : parsedObject[0].order_id,
                      table_id	: tableId,
                      arr_merge_table: parsedObject[0].arr_merge_table,
                      refresh : false
                    });
                    if($(el).hasClass("active")){
                      $(el).removeClass("active");
                      $(el).siblings(".btn-mode2-unavailable").show();
                      $(el).siblings(".btn-mode2-checklist").removeClass("active");
                      $(el).siblings(".btn-mode2-checklist").hide();
                      if (AppKitchen.countKitchenProcess == 1) {
                        $('#up-quantity'+order_menu_id).attr('disabled', true);
                        $('#down-quantity'+order_menu_id).attr('disabled', true);
                      }
                    }else{
                      $(el).addClass("active");
                      $(el).siblings(".btn-mode2-unavailable").hide();
                      $(el).siblings(".btn-mode2-checklist").show();
                      if (AppKitchen.countKitchenProcess == 1) {
                        if ($("#order-quantity"+order_menu_id).val() != $("#cooking-quantity"+order_menu_id).val() && $("#cooking-quantity"+order_menu_id).val() == 0) {
                          $('#up-quantity'+order_menu_id).removeAttr('disabled');
                        } else {
                          $('#down-quantity'+order_menu_id).removeAttr('disabled');
                        }
                      } 
                      if(AppKitchen.useChecklistOrder==0){
                        $(el).siblings(".btn-mode2-checklist").addClass("active");
                      }
                    }
                    if(AppKitchen.useChecklistOrder==0){
                      $(el).siblings(".btn-mode2-checklist").hide();
                    }
                  }
                  AppKitchen.loadingOverlay.hide();
                }).fail(function () {
                  AppKitchen.alert('Gagal memperbaharui status. Silahkan coba lagi. Jika masalah masih terjadi mohon hubungi administrator.');
                  AppKitchen.loadingOverlay.hide();
                });
            });
            $(document).on('click','.btn-mode2-checklist',function (e) {
                AppKitchen.loadingOverlay.show();
                if($(this).hasClass("active")){
                  is_check=0;
                }else{
                  is_check=1;
                }
                e.preventDefault();
                var order_menu=$(this).parents("tr").find("#menu_order_id");
								order_menu_id=order_menu.val();
								order_package_menu_id=parseInt(order_menu.attr("order_package_menu_id"));
								if(isNaN(order_package_menu_id))order_package_menu_id=0;
                var el=this;
                $.ajax({
                  type : "POST",
                  url  : AppKitchen.baseUrl + 'kitchen/update_checklist',
                  data : {
                    order_package_menu_id  : order_package_menu_id,
                    order_menu_id  : order_menu_id,
                    is_check : is_check
                  }
                }).done(function (resp) {
                  if($(el).hasClass("active")){
                    $(el).removeClass("active");
                  }else{
                    $(el).addClass("active");
                  }
                  AppKitchen.loadingOverlay.hide();
                }).fail(function () {
                  AppKitchen.alert('Gagal memperbaharui status. Silahkan coba lagi. Jika masalah masih terjadi mohon hubungi administrator.');
                  AppKitchen.loadingOverlay.hide();
                });
            });
            $(document).on('click','.btn-ready',function (e) {
            	e.preventDefault();
            	AppKitchen.loadingOverlay.show();
                console.log('button ready pressed...');
				
                var row = $(this).closest("tr");
                var arrTable = row.children();
                var tableId = $(this).closest('table').attr('table-id');				
                if(AppKitchen.useRoleChecker==1){
                  process_checker=parseInt($(this).parents("tr").attr("process_checker"));
                  if(process_checker==1){
                    cooking_status=7;
                  }else{
                    cooking_status=3;
                  }
                }else{
                  cooking_status=3;
                }
                // if(tableId==undefined)cooking_status=3;
                $.ajax({
                  type : "POST",
                  url  : AppKitchen.baseUrl + 'kitchen/update_cooking_status',
                  data : {
                    order_menu_id  : $(arrTable[6]).val(),
                    cooking_status : cooking_status,
                    table_id	: tableId						
                  }
                }).done(function (resp) {
                  if (resp != '') {
                    var parsedObject = JSON.parse(resp);
                    AppKitchen.socket.emit('cm_notify_checker',{});
                    AppKitchen.socket.emit('cm_notify_cooking_status', {
                      order_menu_id : parsedObject[0].order_menu_id,
                      cooking_status : parsedObject[0].cooking_status,
                      status_name  : parsedObject[0].status_name,
                      table_id	: tableId,
                      order_id : parsedObject[0].order_id,
                      arr_merge_table: parsedObject[0].arr_merge_table,
                      notification : parsedObject[0].notification

                    });                 
										if(row.parents(".kitchen-table").find("tbody tr").length==1){
											row.parents(".dark-theme-con").prev().remove();
											row.parents(".dark-theme-con").remove();
										}
                    row.remove();
										AppKitchen.loadingOverlay.hide();
                  }
                }).fail(function () {
                  AppKitchen.alert('Gagal memperbaharui status. Silahkan coba lagi. Jika masalah masih terjadi mohon hubungi administrator.');
									AppKitchen.loadingOverlay.hide();
                });

            });
            $(document).on('click',".btn-mode2-post",function () {
							var post_to=$(this).attr("checker-number");
							if(post_to==undefined)post_to=0;
              var url=$(this).attr("url");
              var tableId=$(this).parents("li.tag-order:first").find(".kitchen-table").attr("table-id");
              var order_id=$(this).parents("li.tag-order:first").find(".kitchen-table").attr("oder-id");
              var table_length=$(this).parents("li.tag-order:first").find(".kitchen-table tbody tr").length;
              var length_cooking=$(this).parents("li.tag-order:first").find(".kitchen-table tbody tr .btn-mode2-cooking.active").length;
              var length=$(this).parents("li.tag-order:first").find(".kitchen-table tbody tr .btn-mode2-checklist.active").length;
              if(table_length!=length || table_length!=length_cooking){
                AppKitchen.alert("Silahkan proses cooking & checklist semua terlebih dahulu untuk melakukan aksi ini!");
                return false;
              }
              if (AppKitchen.countKitchenProcess == 1) {
                var counter_cooking = 0;
                var order_menu_id = [];
                $(this).parents("li.tag-order").find(".kitchen-table tbody tr .btn-mode2-cooking.active").each(function(i,e){
                  order_menu_id = $(e).parents("tr").find("#menu_order_id").val();
                  if ($("#order-quantity"+order_menu_id).val() != $("#cooking-quantity"+order_menu_id).val()) {
                    counter_cooking++;
                  }
                });
                if (counter_cooking != 0) {
                  AppKitchen.alert("Silahkan proses cooking semua terlebih dahulu untuk melakukan aksi ini!");
                  return false;
                }
              }
              var element=this;
              AppKitchen.loadingOverlay.show();
              var order_menu_id=[];
              var order_package_menu_id=[];
              var cooking_status=[];
							counter=0;
              $(this).parents("li.tag-order").find(".kitchen-table tbody tr .btn-mode2-cooking.active").each(function(i,e){
								menu_order_id=$(e).parents("tr").find("#menu_order_id");
                order_menu_id.push(menu_order_id.val());
								temp=menu_order_id.attr("order_package_menu_id");
								if(temp==undefined)temp=0;
                order_package_menu_id.push(temp);
                process_checker=parseInt($(e).parents("tr").attr("process_checker"));
                if(AppKitchen.useRoleChecker==1){
                  process_checker=parseInt($(this).parents("tr").attr("process_checker"));
                  if(process_checker==1){
                    cooking_status.push(7);
                  }else{
                    cooking_status.push(3);
                  }
                }else{
                  cooking_status.push(3);
                }
								counter++;
              });
              $.ajax({
                type : "POST",
                url  : AppKitchen.baseUrl + "kitchen/posts",
                data : {
                  order_menu_id  : order_menu_id,
                  order_package_menu_id  : order_package_menu_id,
                  cooking_status : cooking_status,
                  table_id	: tableId,
                  order_id  : order_id,
									post_to : post_to
                }
              }).done(function (resp) {
                if (resp != '') {
                  AppKitchen.socket.emit('cm_notify_checker',{});
                  var parsedObject = JSON.parse(resp);
                  parsedObject.notify_cooking_status.forEach(function(e,i){
                    AppKitchen.socket.emit('cm_notify_cooking_status', {
                      order_menu_id : e.order_menu_id,
                      cooking_status : e.cooking_status,
                      status_name  : e.status_name,
                      table_id	: tableId,
                      order_id : e.order_id,
                      arr_merge_table: e.arr_merge_table,
                      notification : e.notification
                    });
                  });
                  if (AppKitchen.diningType == 3) {
                    AppKitchen.socket.emit('cm_notify_new_order', {
                      number_guest : parsedObject.number_guest,
                      table_status : parsedObject.table_status,
                      table_name : parsedObject.table_name,
                      table_id  : parsedObject.table_id,
                      order_id : parsedObject.order_id,
                      status_name : parsedObject.status_name,
                      arr_merge_table: parsedObject.arr_merge_table,
                      arr_menu_outlet : parsedObject.arr_menu_outlet,
                      status_class : parsedObject.status_class
                    });
                  }
                }
                AppKitchen.loadingOverlay.hide();
                $(element).parents("li.tag-order").remove();
              });
            });
            $(document).on('click','.btn-delete',function () {
              console.log('button delete pressed...');
              var row = $(this).closest("tr");
              var arrTable = row.children();
              var tableId = $(this).closest('table').attr('table-id');				
              
              $.ajax({
                type : "POST",
                url  : AppKitchen.baseUrl + 'kitchen/update_cooking_status',
                data : {
                  order_menu_id  : $(arrTable[6]).val(),
                  cooking_status : 6,
                  table_id	: tableId
                }
              }).done(function (resp) {
                console.log(resp);
                if (resp != '') {
                  var parsedObject = JSON.parse(resp);
                  AppKitchen.socket.emit('cm_notify_cooking_status', {
                    order_menu_id : parsedObject[0].order_menu_id,
                    cooking_status : parsedObject[0].cooking_status,
                    status_name  : parsedObject[0].status_name,
                    arr_merge_table: parsedObject[0].arr_merge_table,
                    order_id : parsedObject[0].order_id,
                    table_id : tableId,
                    room 	: 'kitchen'
                  });
                  // $(arrTable).remove();
                  window.location = AppKitchen.baseUrl+'kitchen';
                }
              }).fail(function () {
                AppKitchen.alert('Gagal menghapus menu. Silahkan coba lagi. Jika masalah masih terjadi mohon hubungi administrator.');
              });
            });
            $(document).on('click','.btn-mode2-unavailable',function (e) {
              e.preventDefault();
              menu_name=$(this).parents("tr").find("td:first span").attr("title");
              var tableId = $(this).parents('table').attr('table-id');				
              var order_menu_id=$(this).parents("tr").find("#menu_order_id").val();
              var el=this;
              AppKitchen.confirm('Set status menu '+menu_name+' menjadi tidak tersedia ?', function(){
                AppKitchen.loadingOverlay.show();
                $.ajax({
                  type : "POST",
                  url  : AppKitchen.baseUrl + 'kitchen/update_cooking_status',
                  data : {
                    order_menu_id  : order_menu_id,
                    cooking_status : 6,
                    table_id	: tableId
                  }
                }).done(function (resp) {
                  if (resp != '') {
                    var parsedObject = JSON.parse(resp);
                    AppKitchen.socket.emit('cm_notify_cooking_status', {
                      order_menu_id : parsedObject[0].order_menu_id,
                      cooking_status : parsedObject[0].cooking_status,
                      status_name  : parsedObject[0].status_name,
                      arr_merge_table: parsedObject[0].arr_merge_table,
                      order_id : parsedObject[0].order_id,
                      table_id : tableId,
                      room 	: 'kitchen'
                    });
                  }
                  AppKitchen.loadingOverlay.hide();
                }).fail(function () {
                  AppKitchen.alert('Gagal menghapus menu. Silahkan coba lagi. Jika masalah masih terjadi mohon hubungi administrator.');
                  AppKitchen.loadingOverlay.hide();
                });                
              });
            });
            $('[title!=""]').qtip();
        }
    };
});