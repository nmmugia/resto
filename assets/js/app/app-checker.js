define([
  "jquery",
  "jquery-ui",
  "bootstrap",
  "qtip",
	"keyboard",
], function ($, ui) {
  return {
    nodeUrl             : $('#node_url').val(),
    baseUrl             : $('#base_url').val(),
    theme               : $('#theme').val(),
    socket              : false,
    loadingOverlay      : $("#cover"),
    timeoutVal          : 10000,
    ip_address          : $("#ip_address").val(),
    init                : function () {
      AppChecker.loadingOverlay.show();
      AppChecker.initFunc(AppChecker);
      try {
        AppChecker.initSocketIO();
      } catch (err) {
        AppChecker.alert($('#server-error-message p').text());
      }
      
    },
    initSocketIO        : function () {
      AppChecker.socket = io(AppChecker.nodeUrl, {
        'reconnectionAttempts' : 2
      });
      AppChecker.socket.on('reconnect_failed', function () {
        AppChecker.alert($('#server-error-message p').text());
        window.location.reload(true);
      });
      AppChecker.socket.on('connected', function (data) {
        console.log('Socket.IO connected');
        AppChecker.socket.emit('cm_auth', {
          nip       : AppChecker.userId,
          name      : AppChecker.userName,
          role      : AppChecker.groupId,
          role_name : AppChecker.groupName,
          ip_address: AppChecker.ip_address
        });
        AppChecker.socket.on('sm_auth', function (data) {
          AppChecker.loadingOverlay.hide();
          AppChecker.initUIEvent();
        });
        AppChecker.socket.on('sm_notify_new_order', function (data) {
          if(data.notification!=undefined && data.notification != null){
            AppChecker.prependNotif(data.notification);
            AppChecker.updateOpenNotifBar();  
          }
          // AppChecker.getDetailView();
        });
        AppChecker.socket.on('sm_notify_cooking_status', function (data) {
          if(data.notification != null){
            AppChecker.prependNotif(data.notification);
            AppChecker.updateOpenNotifBar();  
          }
          // AppChecker.getDetailView();
        });
        AppChecker.socket.on('sm_notify_cooking_status_refresh', function (data) {
          // AppChecker.getDetailView();
        });
        AppChecker.socket.on('sm_notify_new_order', function (data) {
          // setTimeout(function(){
            // AppChecker.getDetailView();
          // },2000);
        });
        AppChecker.socket.on('sm_notify_checker', function (data) {
          // setTimeout(function(){
            AppChecker.getDetailView();
          // },2000);
        });
      });
    },
    initUIEvent         : function () {
			if($("#string_number").length>0){
				AppChecker.initKeyboardNumber($("#string_number"));
			}
			$("#print_number").on('click',function(){
				string_number=$("#string_number").val();
				if(string_number!=""){
					$.ajax({
						url:AppChecker.baseUrl + "checker/print_number",
						data:{string_number:string_number}
					});
				}
			});
      if($(".slide-down").length>0){
        $("nav").hide();
        $("#header_store").hide();
      }
      $(document).on("click",".slide-up",function(){
        $(this).removeClass("slide-up").addClass("slide-down");
        slide="slide-down";
        $.ajax({
          url:AppChecker.baseUrl + "checker/set_slide_setting",
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
          url:AppChecker.baseUrl + "checker/set_slide_setting",
          data:{slide:slide}
        });
        $(this).find("i").removeClass("fa-arrow-down").addClass("fa-arrow-up");
        $("nav").show();
        $("#header_store").show();
      });
      // $(document).on("click","#pagination .pagination a",function(){
        // var url=$(this).attr("href");
        // $.ajax({
          // url:url,
          // success:function(response){
            // $("#content_checker").html(response);
          // }
        // })
        // return false;
      // });
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
      $(document).on('click',".choice_type",function(){
        value=$(this).val();
        is_checked=$(this).is(":checked");
        $.ajax({
          url:AppChecker.baseUrl + "checker/set_choice_type",
          data:{is_checked:is_checked,value:value},
          success:function(response){
            window.location.reload();
          }
        });
      });
      $(document).on('click', '.btn-ready', function () {
        if($(this).hasClass("active"))
        {
          $(this).removeClass("active");
        }else{
          $(this).addClass("active");
        }
        $(this).blur();
      });
      
      $(document).on('click', '#btn-notif', function (e) {
        var options = { direction: 'right' };
        $('.notification-container').toggle('slide',options , 500);
        $('.notification-container').addClass('open-notif');
        AppChecker.updateOpenNotifBar();              
      });
      $(document).on('click', '.button-hide', function (e) {
        var options = { direction: 'right' };
        $('.notification-container').toggle('slide',options , 500);
        var list = $('.unseen-notif');
        var arrID = [];
        for (var i = list.length - 1; i >= 0; i--) {
          var id = list.eq(i).attr('data-id');
          AppChecker.updateNotifCounter('-');
          $('#notif-'+id+' ' ).removeClass('unseen-notif');
        };
      });
      $('.notification-container').removeClass('open-notif');
      $(document).on("click",".print_list_menu",function(){
        order_id=$(this).attr("order-id");
        cooking_status=$(this).attr("cooking-status");
        AppChecker.loadingOverlay.show();
        url=AppChecker.baseUrl + 'checker/print_list_menu';
        order_menu_ids=new Array();
        $(this).parents("li:first").find(".kitchen-table tbody input#menu_order_id").each(function(){
          order_menu_ids.push($(this).val());
        });
        var request = $.ajax({
          type    : 'POST',
          url     : url,
          data    : {'order_id':order_id,'cooking_status':cooking_status,order_menu_ids:order_menu_ids}
        });
				request.done(function () {
					AppChecker.loadingOverlay.hide();
				});
				request.fail(function () {
					AppChecker.loadingOverlay.hide();
				});
      });
      $(document).on('click','.btn-mode2-checklist',function (e) {
        AppChecker.loadingOverlay.show();
        if($(this).hasClass("active")){
          is_check=0;
        }else{
          is_check=1;
        }
        e.preventDefault();
        var order_menu=$(this).parents("tr").find("#menu_order_id");
        var order_menu_id=order_menu.val();
				order_package_menu_id=parseInt(order_menu.attr("order_package_menu_id"));
				if(isNaN(order_package_menu_id))order_package_menu_id=0;
        var el=this;
        $.ajax({
          type : "POST",
          url  : AppChecker.baseUrl + 'checker/update_checklist',
          data : {
            order_menu_id  : order_menu_id,
            order_package_menu_id  : order_package_menu_id,
            is_check : is_check
          }
        }).done(function (resp) {
          if($(el).hasClass("active")){
            $(el).removeClass("active");
          }else{
            $(el).addClass("active");
          }
          AppChecker.loadingOverlay.hide();
        }).fail(function () {
          AppChecker.alert('Gagal memperbaharui status. Silahkan coba lagi. Jika masalah masih terjadi mohon hubungi administrator.');
          AppChecker.loadingOverlay.hide();
        });
      });
      $(document).on('click',".btn-mode2-post",function () {
        var url=$(this).attr("url");
        var tableId=$(this).parents("li:first").find(".kitchen-table").attr("table-id");
        var order_id=$(this).parents("li:first").find(".kitchen-table").attr("order-id");
        var table_length=$(this).parents("li:first").find(".kitchen-table tbody tr").length;
        var length=$(this).parents("li:first").find(".kitchen-table tbody tr .btn-mode2-checklist.active").length;
        var element=this;
        if(table_length!=length){
          AppChecker.alert("Silahkan checklist semua terlebih dahulu untuk melakukan aksi ini!");
          return false;
        }
        AppChecker.loadingOverlay.show();
        var order_menu_id=[];
				var order_package_menu_id=[];
        var cooking_status=3;
        $(this).parents("li:first").find(".kitchen-table tbody tr").each(function(i,e){
          order_menu_id.push($(e).find("#menu_order_id").val());
					temp=$(e).find("#menu_order_id").attr("order_package_menu_id");
					if(temp==undefined)temp=0;
					order_package_menu_id.push(temp);
        });
        $.ajax({
          type : "POST",
          url  : AppChecker.baseUrl + "checker/posts",
          data : {
            order_menu_id  : order_menu_id,
            order_package_menu_id  : order_package_menu_id,
            cooking_status : cooking_status,
            table_id	: tableId,
            order_id  : order_id            
          }
        }).done(function (resp) {
          if (resp != '') {
            var parsedObject = JSON.parse(resp);
            parsedObject.forEach(function(e,i){
              AppChecker.socket.emit('cm_notify_cooking_status', {
                order_menu_id : e.order_menu_id,
                cooking_status : e.cooking_status,
                status_name  : e.status_name,
                table_id	: tableId,
                order_id : e.order_id,
                arr_merge_table: e.arr_merge_table,
                notification : e.notification
              });
            })
          }
          $(element).parents("li").remove();
          AppChecker.loadingOverlay.hide();
        });
      });
      $(document).on('click',".btn-post-order",function () {
        AppChecker.loadingOverlay.show();
        var url=$(this).attr("url");
        var tableId=$(this).parents(".list-order-checker").attr("table-id");
        var order_id=$(this).parents(".list-order-checker").attr("order-id");
        var length=$(this).parents(".list-order-checker").find(".kitchen-table tbody tr .btn-ready.active").length;
        var order_menu_id=[];
        $(this).parents(".list-order-checker").find(".kitchen-table tbody tr .btn-ready.active").each(function(i,e){
          order_menu_id.push($(e).parents("tr").find("#menu_order_id").val());
        });
        $.ajax({
          type : "POST",
          url  : url,
          data : {
            order_menu_id  : order_menu_id,
            cooking_status : 3,
            table_id	: tableId,
            order_id  : order_id            
          }
        }).done(function (resp) {
          if (resp != '') {
            var parsedObject = JSON.parse(resp);
            AppChecker.socket.emit('cm_notify_cooking_status', {
              order_menu_id : parsedObject[0].order_menu_id,
              cooking_status : parsedObject[0].cooking_status,
              status_name  : parsedObject[0].status_name,
              table_id	: tableId,
              order_id : parsedObject[0].order_id,
              arr_merge_table: parsedObject[0].arr_merge_table,
              notification : parsedObject[0].notification
            });
            // if((i+1)==length){
              AppChecker.getDetailView();
            // }
          }
        });
        AppChecker.loadingOverlay.hide();
      });
      $('[title!=""]').qtip();
      AppChecker.deleteNotif();
    },
    getDetailView:function(){
      // AppChecker.loadingOverlay.show();
      var request = $.ajax({
        type    : 'POST',
        url     : AppChecker.baseUrl + 'checker/get_data'
      });
      request.done(function (response) {
        // AppChecker.loadingOverlay.hide();
        if(response!=""){
          $("#content_checker").html(response);
          $('[title!=""]').qtip();
        }
      });
      request.fail(function (jqXHR, textStatus) {
        // AppChecker.loadingOverlay.hide();
        if (textStatus == 'timeout') {
          AppChecker.alert($('#server-timeout-message p').text());
        }
        window.location.reload(true);
      });
    },
    updateOpenNotifBar : function(){
      if($('.notification-container').hasClass('open-notif')){
        var list = $('.unseen-notif');
        for (var i = 0 ; i < list.length ; i++) {
          var id = list.eq(i).attr('data-id');
          var request = $.ajax({
            type    : 'POST',
            url     : AppChecker.baseUrl + 'notification/update_notif',
            data    : {
              'notif_id' : id,
            }
          });
          request.done(function () {}); 
        };
      }
    },
    updateNotifCounter : function(type){
      var count = parseInt($('.counter-notification').html());
      if(type == '+'){
        if(isNaN(count)){
          $('#btn-notif').append('<div class="counter-notification">0</div>');
          count = 0;
        }
        $('.counter-notification').html(count + 1).change();
      }else{
        if(count - 1<0)count=1;
        $('.counter-notification').html(count - 1).change();
        if($('.counter-notification').html() == 0){
          $('.counter-notification').hide();
        }  
      }
    },
    prependNotif : function(data){
      for (var i = data.length - 1; i >= 0; i--) {
        if(data[i].to_user == AppChecker.userId){
          $('.empty-notif').remove()
          var msg = '<div class="list-notification unseen-notif" id="notif-'+data[i].notif_id+'" data-id="'+data[i].notif_id+'" >'+
          '<p class="content-notif" >'+data[i].msg+'</p>'+
          '<a class="button-ok-notif" href="#" data-id="'+data[i].notif_id+'"></a></div>';
          $('#notification-container-list').prepend(msg);

          AppChecker.updateNotifCounter('+');               
          $('.counter-notification').show();
        }
      }           
      AppChecker.deleteNotif();
    },
    deleteNotif : function (){
      $('.button-ok-notif').on('click', function (e){
        AppChecker.loadingOverlay.show();
        var id = $(this).attr('data-id');

        var request = $.ajax({
            type    : 'POST',
            url     : AppChecker.baseUrl + 'notification/delete_notif',
            data    : {
                'notif_id' : id,
            }
        });
        request.done(function () {

            $('#notif-'+id+'').remove();
            AppChecker.updateNotifCounter('-');                
            AppChecker.loadingOverlay.hide();
        });     
      }); 
    },
    startTouchTimer : function (time) {
      AppChecker.touchTimer += time;
    },
		initKeyboardNumber:function(object){
				var is_mobile = /mobile|android/i.test(navigator.userAgent);
				if(is_mobile) return;
				var keyboard=object.keyboard().getkeyboard();
				keyboard.destroy();
				object.keyboard({
						layout: 'custom',
						customLayout: {
							'default' : [
								'1 2 3',
								'4 5 6',
								'7 8 9',
								'{left} 0 {right}',
								'{bksp} {a} {c}'
						]
						 
						},
						maxLength:5,
						restrictInput : false, // Prevent keys not in the displayed keyboard from being typed in
						preventPaste : false,  // prevent ctrl-v and right click
						autoAccept : false,
						lockInput: false, // prevent manual keyboard entry
				// }).addCaret();
				});
				
		},
  }
});