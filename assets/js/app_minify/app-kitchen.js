var b;define(["jquery","jquery-ui","datatables","bootstrap","datatables-bootstrap","bootstrap","select2","qtip"],function(a,b){return{nodeUrl:a("#node_url").val(),baseUrl:a("#base_url").val(),outletId:a("#outlet_id").val(),theme:a("#theme").val(),socket:!1,loadingOverlay:a("#cover"),diningType:a("#dining_type").val(),countKitchenProcess:a("#count_kitchen_process").val(),useRoleChecker:a("#use_role_checker").val(),useChecklistOrder:parseInt(a("#use_checking_order").val()),init:function(){console.log("App Kitchen inited.."),AppKitchen.loadingOverlay.show(),AppKitchen.initFunc(AppKitchen);try{AppKitchen.initSocketIO()}catch(b){AppKitchen.alert(a("#server-error-message p").text())}AppKitchen.inventoryEvent()},inventoryEvent:function(){a(document).on("change","#inventory_id",function(){inventory_id=a(this).val(),el=this,""!=inventory_id?(uom_id=a(this).find("option:selected").attr("uom_id"),a.ajax({url:AppKitchen.baseUrl+"admincms/inventory/get_inventory_uoms",type:"POST",dataType:"JSON",data:{inventory_id:inventory_id},success:function(b){a("#uom_id").html(b.content),a("#uom_id").val(uom_id)}})):a("#uom_id").html("<option value=''>Pilih Satuan</option>")}),a("#dataTables-inventory-history").dataTable({bProcessing:!0,bServerSide:!0,sServerMethod:"POST",ajax:a("#dataProcessUrl").val(),iDisplayLength:10,columns:[{data:"join_name"},{data:"total_used",defaultContent:0},{data:"total_spoiled",defaultContent:0},{data:"sisa_stok",defaultContent:0}],columnDefs:[{targets:[1,2,3],orderable:!1,bSearchable:!1,"class":"center-tr"}]}),a("#save-spoiled").on("click",function(){var b=a("#inventory_id").val(),c=a("#uom_id").val(),d=a("#quantity").val(),e=a("#description").val();if(0===d.length)AppKitchen.alert("Jumlah Stok harus diisi",function(){});else{var f="save_spoiled";a.ajax({url:f,type:"POST",data:{inventory_id:b,quantity:d,uom_id:c,description:e},success:function(a){var b=JSON.parse(a);AppKitchen.alert(b.message)}})}})},initSocketIO:function(){AppKitchen.socket=io(AppKitchen.nodeUrl,{reconnectionAttempts:2}),AppKitchen.socket.on("reconnect_failed",function(){AppTable.alert(a("#server-error-message p").text()),window.location.reload(!0)}),AppKitchen.socket.on("sm_notify_cooking_status_refresh",function(a){AppKitchen.loadingOverlay.show(),AppKitchen.getDataKitchenMode2(),AppKitchen.loadingOverlay.hide()}),AppKitchen.socket.on("sm_notify_change_table",function(a){console.log(a),AppKitchen.getDataKitchenMode2()}),AppKitchen.socket.on("connected",function(b){AppKitchen.socket.emit("cm_get_checker_ip_address"),console.log("Socket.IO connected"),AppKitchen.socket.on("sm_get_checker_ip_address",function(b){for(html="",x=0;x<b.checker_ip_address.length;x++)html+='<li><a href="#" class="btn-mode2-post" checker-number="'+b.checker_ip_address[x]+'">'+b.checker_ip_address[x]+"</a></li>";a(".list-checker-ip").html(html)}),AppKitchen.socket.on("sm_notify_new_order",function(b){void 0!=b.outlets&&b.outlets.indexOf(parseInt(AppKitchen.outletId))>=0?(AppKitchen.getDataKitchenMode2(),a("#bgsound_notification").get(0).play()):AppKitchen.getDataKitchenMode2()}),AppKitchen.socket.on("sm_empty_table",function(a){a.order_id}),AppKitchen.initUIEvent(),AppKitchen.loadingOverlay.hide()})},getDataKitchenMode2:function(){var b=AppKitchen.baseUrl+"kitchen/get_data_left_right";a.ajax({url:b,dataType:"JSON",success:function(b){1==b.theme?a("#default_mode_content").html(b.content):(a("#regular_content").html(b.content_left),a("#additional_content").html(b.content_right),AppKitchen.socket.emit("cm_get_checker_ip_address")),a('[title!=""]').qtip()}})},initUIEvent:function(){a(".select2").select2(),a(".slide-down").length>0&&(console.log("asdasdsadsa"),a("nav").hide(),a("#header_store").hide()),a(document).on("click",".slide-up",function(){a(this).removeClass("slide-up").addClass("slide-down"),slide="slide-down",a.ajax({url:AppKitchen.baseUrl+"kitchen/set_slide_setting",data:{slide:slide}}),a(this).find("i").removeClass("fa-arrow-up").addClass("fa-arrow-down"),a("nav").hide(),a("#header_store").hide()}),a(document).on("click",".slide-down",function(){a(this).removeClass("slide-down").addClass("slide-up"),slide="slide-up",a.ajax({url:AppKitchen.baseUrl+"kitchen/set_slide_setting",data:{slide:slide}}),a(this).find("i").removeClass("fa-arrow-down").addClass("fa-arrow-up"),a("nav").show(),a("#header_store").show()}),a(document).on("click",".print_list_menu",function(){order_id=a(this).attr("order-id"),cooking_status=a(this).attr("cooking-status"),AppKitchen.loadingOverlay.show(),url=AppKitchen.baseUrl+"kitchen/print_list_menu",order_menu_ids=new Array,1==AppKitchen.theme?a(this).parents(".title-bg-kitchen:first").next().find(".kitchen-table tbody input#menu_order_id").each(function(){order_menu_ids.push(a(this).val())}):a(this).parents("li:first").find(".kitchen-table tbody input#menu_order_id").each(function(){order_menu_ids.push(a(this).val())});var b=a.ajax({type:"POST",url:url,data:{order_id:order_id,cooking_status:cooking_status,order_menu_ids:order_menu_ids}});b.done(function(){AppKitchen.loadingOverlay.hide()}),b.fail(function(){AppKitchen.loadingOverlay.hide()})}),a(document).on("click","#pagination_left .pagination a",function(){var b=a(this).attr("href");return a.ajax({url:b,dataType:"JSON",success:function(b){a("#regular_content").html(b.content)}}),!1}),a(document).on("click",".choice_type",function(){value=a(this).val(),is_checked=a(this).is(":checked"),a.ajax({url:AppKitchen.baseUrl+"kitchen/set_choice_type",data:{is_checked:is_checked,value:value},success:function(a){window.location.reload()}})}),a(document).on("click","#pagination_right .pagination a",function(){var b=a(this).attr("href");return a.ajax({url:b,dataType:"JSON",success:function(b){a("#additional_content").html(b.content)}}),!1}),a(document).on("click",".btn-cooking",function(b){b.preventDefault(),console.log("button cooking pressed...");var c=a(this).closest("tr"),d=c.children(),e=a(this).closest("table").attr("table-id");a.ajax({type:"POST",url:AppKitchen.baseUrl+"kitchen/update_cooking_status",data:{order_menu_id:a(d[6]).val(),cooking_status:2,table_id:e}}).done(function(b){if(console.log(b),""!=b){var c=JSON.parse(b);AppKitchen.socket.emit("cm_notify_cooking_status",{order_menu_id:c[0].order_menu_id,cooking_status:c[0].cooking_status,status_name:c[0].status_name,order_id:c[0].order_id,table_id:e,arr_merge_table:c[0].arr_merge_table}),a(d[5]).html('<button class="btn btn-status btn-ready"><img src="'+AppKitchen.baseUrl+'/assets/img/ico-ready.png"></button>'),a(d[4]).html("<center>"+c[0].status_name+"</center>")}}).fail(function(){AppKitchen.alert("Gagal memperbaharui status. Silahkan coba lagi. Jika masalah masih terjadi mohon hubungi administrator.")})}),a(document).on("click",".btn-mode2-countup",function(b){AppKitchen.loadingOverlay.show(),b.preventDefault();var c=a(this).parents("table").attr("table-id"),d=a(this).parents("tr").find("#menu_order_id").val(),e=parseInt(a(this).parents("tr").find("#menu_order_id").attr("order_package_menu_id"));isNaN(e)&&(e=0);var f=this;a.ajax({type:"POST",url:AppKitchen.baseUrl+"kitchen/update_cooking_process",data:{order_menu_id:d,order_package_menu_id:e,table_id:c,process:"up"}}).done(function(b){if(""!=b){var c=JSON.parse(b);c.quantity_process==c.quantity&&a("#up-quantity"+d).attr("disabled",!0),a(f).siblings(".count-cooking").text(c.quantity_process),a(f).siblings("#down-quantity"+d).removeAttr("disabled"),a("#cooking-quantity"+d).val(c.quantity_process)}AppKitchen.loadingOverlay.hide()}).fail(function(){AppKitchen.alert("Gagal memperbaharui status. Silahkan coba lagi. Jika masalah masih terjadi mohon hubungi administrator."),AppKitchen.loadingOverlay.hide()})}),a(document).on("click",".btn-mode2-countdown",function(b){AppKitchen.loadingOverlay.show(),b.preventDefault();var c=a(this).parents("table").attr("table-id"),d=a(this).parents("tr").find("#menu_order_id").val(),e=parseInt(a(this).parents("tr").find("#menu_order_id").attr("order_package_menu_id"));isNaN(e)&&(e=0);var f=this;a.ajax({type:"POST",url:AppKitchen.baseUrl+"kitchen/update_cooking_process",data:{order_menu_id:d,order_package_menu_id:e,table_id:c,process:"down"}}).done(function(b){if(""!=b){var c=JSON.parse(b);0==c.quantity_process&&a("#down-quantity"+d).attr("disabled",!0),a(f).siblings(".count-cooking").text(c.quantity_process),a(f).siblings("#up-quantity"+d).removeAttr("disabled"),a("#cooking-quantity"+d).val(c.quantity_process)}AppKitchen.loadingOverlay.hide()}).fail(function(){AppKitchen.alert("Gagal memperbaharui status. Silahkan coba lagi. Jika masalah masih terjadi mohon hubungi administrator."),AppKitchen.loadingOverlay.hide()})}),a(document).on("click",".btn-mode2-cooking",function(b){AppKitchen.loadingOverlay.show(),a(this).hasClass("active")?cooking_status=1:cooking_status=2,b.preventDefault();var c=a(this).parents("table").attr("table-id"),d=a(this).parents("tr").find("#menu_order_id").val(),e=parseInt(a(this).parents("tr").find("#menu_order_id").attr("order_package_menu_id"));isNaN(e)&&(e=0);var f=this;a.ajax({type:"POST",url:AppKitchen.baseUrl+"kitchen/update_cooking_status",data:{order_menu_id:d,order_package_menu_id:e,cooking_status:cooking_status,table_id:c}}).done(function(b){if(""!=b){var e=JSON.parse(b);AppKitchen.socket.emit("cm_notify_cooking_status",{order_menu_id:e[0].order_menu_id,cooking_status:e[0].cooking_status,status_name:e[0].status_name,order_id:e[0].order_id,table_id:c,arr_merge_table:e[0].arr_merge_table,refresh:!1}),a(f).hasClass("active")?(a(f).removeClass("active"),a(f).siblings(".btn-mode2-unavailable").show(),a(f).siblings(".btn-mode2-checklist").removeClass("active"),a(f).siblings(".btn-mode2-checklist").hide(),1==AppKitchen.countKitchenProcess&&(a("#up-quantity"+d).attr("disabled",!0),a("#down-quantity"+d).attr("disabled",!0))):(a(f).addClass("active"),a(f).siblings(".btn-mode2-unavailable").hide(),a(f).siblings(".btn-mode2-checklist").show(),1==AppKitchen.countKitchenProcess&&(a("#order-quantity"+d).val()!=a("#cooking-quantity"+d).val()&&0==a("#cooking-quantity"+d).val()?a("#up-quantity"+d).removeAttr("disabled"):a("#down-quantity"+d).removeAttr("disabled")),0==AppKitchen.useChecklistOrder&&a(f).siblings(".btn-mode2-checklist").addClass("active")),0==AppKitchen.useChecklistOrder&&a(f).siblings(".btn-mode2-checklist").hide()}AppKitchen.loadingOverlay.hide()}).fail(function(){AppKitchen.alert("Gagal memperbaharui status. Silahkan coba lagi. Jika masalah masih terjadi mohon hubungi administrator."),AppKitchen.loadingOverlay.hide()})}),a(document).on("click",".btn-mode2-checklist",function(b){AppKitchen.loadingOverlay.show(),a(this).hasClass("active")?is_check=0:is_check=1,b.preventDefault();var c=a(this).parents("tr").find("#menu_order_id");order_menu_id=c.val(),order_package_menu_id=parseInt(c.attr("order_package_menu_id")),isNaN(order_package_menu_id)&&(order_package_menu_id=0);var d=this;a.ajax({type:"POST",url:AppKitchen.baseUrl+"kitchen/update_checklist",data:{order_package_menu_id:order_package_menu_id,order_menu_id:order_menu_id,is_check:is_check}}).done(function(b){a(d).hasClass("active")?a(d).removeClass("active"):a(d).addClass("active"),AppKitchen.loadingOverlay.hide()}).fail(function(){AppKitchen.alert("Gagal memperbaharui status. Silahkan coba lagi. Jika masalah masih terjadi mohon hubungi administrator."),AppKitchen.loadingOverlay.hide()})}),a(document).on("click",".btn-ready",function(b){b.preventDefault(),AppKitchen.loadingOverlay.show(),console.log("button ready pressed...");var c=a(this).closest("tr"),d=c.children(),e=a(this).closest("table").attr("table-id");1==AppKitchen.useRoleChecker?(process_checker=parseInt(a(this).parents("tr").attr("process_checker")),1==process_checker?cooking_status=7:cooking_status=3):cooking_status=3,a.ajax({type:"POST",url:AppKitchen.baseUrl+"kitchen/update_cooking_status",data:{order_menu_id:a(d[6]).val(),cooking_status:cooking_status,table_id:e}}).done(function(a){if(""!=a){var b=JSON.parse(a);AppKitchen.socket.emit("cm_notify_checker",{}),AppKitchen.socket.emit("cm_notify_cooking_status",{order_menu_id:b[0].order_menu_id,cooking_status:b[0].cooking_status,status_name:b[0].status_name,table_id:e,order_id:b[0].order_id,arr_merge_table:b[0].arr_merge_table,notification:b[0].notification}),1==c.parents(".kitchen-table").find("tbody tr").length&&(c.parents(".dark-theme-con").prev().remove(),c.parents(".dark-theme-con").remove()),c.remove(),AppKitchen.loadingOverlay.hide()}}).fail(function(){AppKitchen.alert("Gagal memperbaharui status. Silahkan coba lagi. Jika masalah masih terjadi mohon hubungi administrator."),AppKitchen.loadingOverlay.hide()})}),a(document).on("click",".btn-mode2-post",function(){var b=a(this).attr("checker-number");void 0==b&&(b=0);var c=(a(this).attr("url"),a(this).parents("li.tag-order:first").find(".kitchen-table").attr("table-id")),d=a(this).parents("li.tag-order:first").find(".kitchen-table").attr("oder-id"),e=a(this).parents("li.tag-order:first").find(".kitchen-table tbody tr").length,f=a(this).parents("li.tag-order:first").find(".kitchen-table tbody tr .btn-mode2-cooking.active").length,g=a(this).parents("li.tag-order:first").find(".kitchen-table tbody tr .btn-mode2-checklist.active").length;if(e!=g||e!=f)return AppKitchen.alert("Silahkan proses cooking & checklist semua terlebih dahulu untuk melakukan aksi ini!"),!1;if(1==AppKitchen.countKitchenProcess){var h=0,i=[];if(a(this).parents("li.tag-order").find(".kitchen-table tbody tr .btn-mode2-cooking.active").each(function(b,c){i=a(c).parents("tr").find("#menu_order_id").val(),a("#order-quantity"+i).val()!=a("#cooking-quantity"+i).val()&&h++}),0!=h)return AppKitchen.alert("Silahkan proses cooking semua terlebih dahulu untuk melakukan aksi ini!"),!1}var j=this;AppKitchen.loadingOverlay.show();var i=[],k=[],l=[];counter=0,a(this).parents("li.tag-order").find(".kitchen-table tbody tr .btn-mode2-cooking.active").each(function(b,c){menu_order_id=a(c).parents("tr").find("#menu_order_id"),i.push(menu_order_id.val()),temp=menu_order_id.attr("order_package_menu_id"),void 0==temp&&(temp=0),k.push(temp),process_checker=parseInt(a(c).parents("tr").attr("process_checker")),1==AppKitchen.useRoleChecker?(process_checker=parseInt(a(this).parents("tr").attr("process_checker")),1==process_checker?l.push(7):l.push(3)):l.push(3),counter++}),a.ajax({type:"POST",url:AppKitchen.baseUrl+"kitchen/posts",data:{order_menu_id:i,order_package_menu_id:k,cooking_status:l,table_id:c,order_id:d,post_to:b}}).done(function(b){if(""!=b){AppKitchen.socket.emit("cm_notify_checker",{});var d=JSON.parse(b);d.notify_cooking_status.forEach(function(a,b){AppKitchen.socket.emit("cm_notify_cooking_status",{order_menu_id:a.order_menu_id,cooking_status:a.cooking_status,status_name:a.status_name,table_id:c,order_id:a.order_id,arr_merge_table:a.arr_merge_table,notification:a.notification})}),3==AppKitchen.diningType&&AppKitchen.socket.emit("cm_notify_new_order",{number_guest:d.number_guest,table_status:d.table_status,table_name:d.table_name,table_id:d.table_id,order_id:d.order_id,status_name:d.status_name,arr_merge_table:d.arr_merge_table,arr_menu_outlet:d.arr_menu_outlet,status_class:d.status_class})}AppKitchen.loadingOverlay.hide(),a(j).parents("li.tag-order").remove()})}),a(document).on("click",".btn-delete",function(){console.log("button delete pressed...");var b=a(this).closest("tr"),c=b.children(),d=a(this).closest("table").attr("table-id");a.ajax({type:"POST",url:AppKitchen.baseUrl+"kitchen/update_cooking_status",data:{order_menu_id:a(c[6]).val(),cooking_status:6,table_id:d}}).done(function(a){if(console.log(a),""!=a){var b=JSON.parse(a);AppKitchen.socket.emit("cm_notify_cooking_status",{order_menu_id:b[0].order_menu_id,cooking_status:b[0].cooking_status,status_name:b[0].status_name,arr_merge_table:b[0].arr_merge_table,order_id:b[0].order_id,table_id:d,room:"kitchen"}),window.location=AppKitchen.baseUrl+"kitchen"}}).fail(function(){AppKitchen.alert("Gagal menghapus menu. Silahkan coba lagi. Jika masalah masih terjadi mohon hubungi administrator.")})}),a(document).on("click",".btn-mode2-unavailable",function(b){b.preventDefault(),menu_name=a(this).parents("tr").find("td:first span").attr("title");var c=a(this).parents("table").attr("table-id"),d=a(this).parents("tr").find("#menu_order_id").val();AppKitchen.confirm("Set status menu "+menu_name+" menjadi tidak tersedia ?",function(){AppKitchen.loadingOverlay.show(),a.ajax({type:"POST",url:AppKitchen.baseUrl+"kitchen/update_cooking_status",data:{order_menu_id:d,cooking_status:6,table_id:c}}).done(function(a){if(""!=a){var b=JSON.parse(a);AppKitchen.socket.emit("cm_notify_cooking_status",{order_menu_id:b[0].order_menu_id,cooking_status:b[0].cooking_status,status_name:b[0].status_name,arr_merge_table:b[0].arr_merge_table,order_id:b[0].order_id,table_id:c,room:"kitchen"})}AppKitchen.loadingOverlay.hide()}).fail(function(){AppKitchen.alert("Gagal menghapus menu. Silahkan coba lagi. Jika masalah masih terjadi mohon hubungi administrator."),AppKitchen.loadingOverlay.hide()})})}),a('[title!=""]').qtip()}}});