define([
  "jquery",
  "jquery-ui",
  "bootstrap",
  "keyboard",
  "datetimepicker",
  "timepicker",
  "qtip"
], function ($, ui) {
  return {
    nodeUrl             : $('#node_url').val(),
    baseUrl             : $('#base_url').val(),
    socket              : false,
    loadingOverlay      : $("#cover"),
    timeoutVal          : 10000,
    numberOfData        : $("#number_of_data").val(),
    perPage             : $("#perPage").val(),
    selectedtableId     : "",
    selectedorder_id    : "",
    selectedreservation_id:"",
    order_menu_id :[],
    init                : function () {
      AppMonitoring.loadingOverlay.show();
      AppMonitoring.initFunc(AppMonitoring);
      try {
        AppMonitoring.initSocketIO();
      } catch (err) {
        AppMonitoring.alert($('#server-error-message p').text());
      }
      
    },
    initSocketIO        : function () {
      AppMonitoring.socket = io(AppMonitoring.nodeUrl, {
        'reconnectionAttempts' : 2
      });
      AppMonitoring.socket.on('reconnect_failed', function () {
        AppMonitoring.alert($('#server-error-message p').text());
        window.location.reload(true);
      });
      AppMonitoring.socket.on('connected', function (data) {
        console.log('Socket.IO connected');
        AppMonitoring.socket.emit('cm_auth', {
          nip       : AppMonitoring.userId,
          name      : AppMonitoring.userName,
          role      : AppMonitoring.groupId,
          role_name : AppMonitoring.groupName
        });
        AppMonitoring.socket.on('sm_auth', function (data) {
          AppMonitoring.loadingOverlay.hide();
          AppMonitoring.initUIEvent();
        }); 
        AppMonitoring.socket.on('sm_new_reservation', function (data) {
          var current_active = parseInt($('ul.pagination').find('li.active').text());
          
          if(current_active == 1 || isNaN(current_active)){
            if(data.order_id < 1){
              console.log("order id");
              AppMonitoring.addNewReservation(data);
            }else{
               AppMonitoring.addNewReservationOrder(data);
            }  
          }
          console.log(data);
        }); 
        AppMonitoring.socket.on('sm_edit_reservation', function (data) {
          console.log(data);
          if(data.order_id < 1){
            AppMonitoring.editReservation(data);
          }else{
            AppMonitoring.editReservationOrder(data);
          }
          
        }); 

        // AppTable.socket.on('sm_post_reservation', function (data) {
        //     console.log(data);
        //     var orderId = $('#order_id').val();
        //     if (AppTable.isDineIn == '1' && orderId == data.order_id) {
        //         window.location = data.url_redir;
        //     }
        //     var tablenew = $('#tab_layout_' + data.table_id);
        //     if(data.status_class!=""){
        //       tablenew.removeClass().addClass(data.status_class);
        //     }
        //     tablenew.data("table-status", data.table_status);
        //     tablenew.data("order-id", data.order_id);
        //     tablenew.data("customer-count", data.number_guest);

        //     tablenew = $('#list_layout_' + data.table_id);
        //     tablenew.removeClass();
        //     tablenew.addClass("table-list-text label-rect-"+data.status_name);
        //     tablenew.data("table-status", data.table_status);
        //     tablenew.data("order-id", data.order_id);
        //     tablenew.data("customer-count", data.number_guest);

        //     if(data.table_status == 1){
        //         AppTable.removeWarning(data.table_id); 
        //         $('#tab_layout_'+data.table_id+' .badge-table').remove();
        //         $('#list_layout_'+data.table_id+' .badge-table-small').remove();
        //     }


        //     merge_table = data.arr_merge_table;

        //         if(merge_table != null)
        //         {
        //             for (var index = 0; index < merge_table.length; index++ ){

        //               var tableTemp = $('#tab_layout_' +merge_table[index].id);
        //               tableTemp.removeClass().addClass(merge_table[index].status_class);
        //               tableTemp.data("table-status", data.table_status);
        //               tableTemp.data("order-id", data.order_id);
        //               tableTemp.data("customer-count", data.number_guest);
        //               tableTemp.data("parent-id","0");

        //               tableTemp = $('#list_layout_' +merge_table[index].id);
        //               tableTemp.removeClass();
        //               tableTemp.addClass("table-list-text label-rect-"+data.status_name);
        //               tableTemp.data("table-status", data.table_status);
        //               tableTemp.data("order-id", data.order_id);
        //               tableTemp.data("customer-count", data.number_guest);
        //               tableTemp.data("parent-id","0");

        //               if(data.table_status == 1){
                    
        //                 AppTable.removeWarning(merge_table[index].id);         
        //                 $('#tab_layout_'+merge_table[index].id+' .badge-table').remove();
        //                 $('#list_layout_'+merge_table[index].id+' .badge-table-small').remove();
        //             }    

        //           }
        //       }

                             
           
        // });
      });
    },
    initUIEvent         : function () {
      $('[title!=""]').qtip();
      $('#start_date').datetimepicker({
        sideBySide: true,
        useCurrent: true,
        format: 'YYYY-MM-DD', 
        widgetPositioning:{
          "vertical":"bottom"
        }  
      });

      $("#start_date").on("dp.change", function (e) { 
        $('#to_date').datetimepicker({
          sideBySide: true,
          useCurrent: true,
          format: 'YYYY-MM-DD'
        });
        $('#to_date').data("DateTimePicker").minDate(e.date);
      });
      $('#to_date').datetimepicker({
        sideBySide: true,
        useCurrent: true,
        format: 'YYYY-MM-DD' 
      });
      $(document).on("click","#search_reservation",function(){
        start_date=$("#start_date input").val();
        end_date=$("#to_date input").val();
        $.ajax({
          url:AppMonitoring.baseUrl + "monitoring/search",
          data:{start_date:start_date,end_date:end_date},
          success:function(response){
            $("#content_monitoring").html(response);
          }
        })
      });
      $(document).on("click","#today_reservation,#tommorow_reservation",function(){
        start_date=$(this).attr("date");
        end_date=start_date;
        $("#start_date input").val(start_date).change();
        $("#to_date input").val(end_date).change();
        $.ajax({
          url:AppMonitoring.baseUrl + "monitoring/search",
          data:{start_date:start_date,end_date:end_date},
          success:function(response){
            $("#content_monitoring").html(response);
          }
        })
      });
      $(document).on("click","#all_reservation",function(){
        $("#start_date input").val("").change();
        $("#to_date input").val("").change();
        $.ajax({
          url:AppMonitoring.baseUrl + "monitoring/search",
          data:{start_date:"",end_date:""},
          success:function(response){
            $("#content_monitoring").html(response);
          }
        })
      });
      $(document).on("click","#pagination .pagination a",function(){
        var url=$(this).attr("href");
        start_date=$("#start_date input").val();
        end_date=$("#to_date input").val();
        $.ajax({
          url:url,
          data:{start_date:start_date,end_date:end_date},
          success:function(response){
            $("#content_monitoring").html(response);
          }
        })
        return false;
      });
      $(document).on("click",".print_list_menu",function(){
        order_id=$(this).attr("order-id");
        AppMonitoring.loadingOverlay.show();
        url=AppMonitoring.baseUrl + 'monitoring/print_list_menu';
        var request = $.ajax({
          type    : 'POST',
          url     : url,
          timeout : AppMonitoring.timeoutVal,
          data    : {'order_id':order_id}
        });
        AppMonitoring.loadingOverlay.hide();
      });
      $(document).off('click',".btn-mode2-post");
      $(document).on('click',".btn-mode2-post",function (e) {
        e.preventDefault();
        var url=$(this).attr("url");
        AppMonitoring.selectedtableId =$(this).parents("li:first").find(".kitchen-table").attr("table-id");
        AppMonitoring.selectedorder_id=$(this).parents("li:first").find(".kitchen-table").attr("order-id");
        AppMonitoring.selectedreservation_id=$(this).parents("li:first").find(".kitchen-table").attr("reservation-id");
        if(AppMonitoring.selectedreservation_id==undefined)AppMonitoring.selectedreservation_id=0;
        var el=this;
       
        AppMonitoring.order_menu_id=[];
        $(this).parents("li:first").find(".kitchen-table tbody tr").each(function(i,e){
          AppMonitoring.order_menu_id.push($(e).find("#menu_order_id").val());                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               
        });
        $("#confirm_post").show();
        
        
      });
      $(document).off('click',"#btn-ok-post");
      $(document).on('click',"#btn-ok-post",function (e) {
          AppMonitoring.postReservation();
      });
      $(document).off('click',"#btn-cancel-post");
      $(document).on('click',"#btn-cancel-post",function (e) {
          $("#confirm_post").hide();

      });
    },
    postReservation:function(){
      $("#confirm_post").hide();
      AppMonitoring.loadingOverlay.show();
      $.ajax({
          type : "POST",
          url  : AppMonitoring.baseUrl + "monitoring/posts",
          data : {
            order_menu_id  : AppMonitoring.order_menu_id,
            table_id  : AppMonitoring.selectedtableId,
            order_id  : AppMonitoring.selectedorder_id,    
            reservation_id : AppMonitoring.selectedreservation_id            
          }
        }).done(function (resp) {
          AppMonitoring.loadingOverlay.hide();
          if (resp != '') {
            var parsedObject = JSON.parse(resp);
            if(parsedObject.status==true){
                console.log(parsedObject);
                if(parsedObject.table_id!=undefined){
                  tableId = parsedObject.table_id;
                  console.log(tableId);
                }
                parsedObject.data.forEach(function(e,i){
                    AppMonitoring.socket.emit('sm_notify_new_order', {
                        order_menu_id : e.order_menu_id,
                        cooking_status : e.cooking_status,
                        status_name  : e.status_name,
                        table_id  : tableId,
                        order_id : e.order_id,
                        arr_merge_table: e.arr_merge_table,
                        notification : e.notification
                    });
                });
                if(parsedObject.outlets.length>0){
                    AppMonitoring.socket.emit('cm_notify_new_order', {
                        order_id : AppMonitoring.selectedtableId,
                        outlets:parsedObject.outlets
                    });
                }
                
                AppMonitoring.socket.emit('cm_post_reservation', {
                    table_status : 4,
                    order_id : AppMonitoring.selectedorder_id,
                    table_id : AppMonitoring.selectedtableId,
                    status_name : "waiting",
                    reservation_id : AppMonitoring.selectedreservation_id,
                    status_class : "dine-in-order label-rect-waiting"
                });
                $("#reservation_"+AppMonitoring.selectedreservation_id).hide();
            }else{
                AppMonitoring.alert(parsedObject.message);
            }
          }
         
        });
    },
    startTouchTimer : function (time) {
      AppMonitoring.touchTimer += time;
    },
    addNewReservation: function(data){
      console.log(data);
      var number_data = parseInt($("#number_of_data").val());
      var per_page =  parseInt($("#perPage").val());
      var current_active = parseInt($('ul.pagination').find('li.active').text());
      var html = "";
      var operator_name = "";
      var customer_name = "";
     
      operator_name= data.operator_name;
       if(operator_name.length > 15){
         operator_name = operator_name.substring(0,15)+"...";
       }
      customer_name= data.customer_name;
       if(customer_name.length > 10){
         customer_name = customer_name.substring(0,15)+"...";
      }
       
      html = '<li class="col-md-2" style="padding: 0px;margin-right:3px;width:16.4%" id="reservation_'+data.reservation_id+'">';
      html += '<div class="title-bg title-bg-kitchen" style="padding:0px;">';
      html += '<div class="col-md-12" style="padding:0px 0px 0px 2px;">';
      html += '<div class="left" style="color: #881817;text-transform: uppercase;font-weight: bold;font-size: 11px;" title="'+data.operator_name+'">'+operator_name+'</div>';
      html += '<div class="right" style="color: #881817;text-transform: uppercase;font-weight: bold;font-size: 11px;">'+data.reservation_date+'</div>';
      html +=  '<div style="clear:both"></div>';
      html += '<div style="float:left;font-weight:bold;font-size: 29px;color:green;margin-right: 2px;line-height: 1;" title="'+data.table_number+'">'+data.table_number+'</div>';
      html += '<div class="left">';
      html += '<h4 class="title-name" style="color:#881817;margin: 0px;font-size: 12px;padding: 0px;text-align:left;" title="'+data.customer_name+'">'+data.customer_name+'</h4>';
      html += '</div>';
      html += '<button class="btn btn-option-list pull-right btn-mode2-post" style="margin-top: 0px;margin-left: 0px;font-size: 15px;font-weight: bold;line-height: 1;padding: 0px 5px;height: 29px;border-radius: 0px !important;">POST</button>';
      html += '</div>';
      html += '</div>';
      html += '<div class="dark-theme-con" style="overflow:auto;overflow-x:hidden;height:220px;padding-bottom:15px;">';
      html += '<table class="kitchen-table" table-id="'+data.table_id+'" order-id="'+data.order_id+'" reservation-id="'+data.reservation_id+'">';
      html += '<thead>';
      html += '<tr>';
      html += '<th style="width:90%;" colspan="2">MENU</th>';
      html += '<th style="width:10%;">JML</th>';
      html += '</tr>';
      html += '</thead>';
      html += '<tbody>';
      html += '</tbody>';
      html += '</table>';
      html += '</div>';
      html += '</li>'; 
      if(number_data == 0){
         $(".list-order-kitchen").html(html);
      }else{
         $(".list-order-kitchen").append(html);
      }
     
      var pagination_html = "";
      for(var i=1;i<=number_data;i++){
        if(i == current_active){
          pagination_html = '<li class="active"><a href="javascript:void()"'+i+'>'+i+'</a></li>';
        }else{
          pagination_html = '<li><a href="'+AppMonitoring.baseUrl+'bosresto/monitoring/get_data/'+i+'">'+i+'</a></li>';

        }
      }  
      $("#pagination").html(pagination_html);
      $("#number_of_data").val(number_data+1);    
      AppMonitoring.initUIEvent();
        
    },
    addNewReservationOrder:function(data){
      var number_data = parseInt($("#number_of_data").val());
      var per_page =  parseInt($("#perPage").val());
      var current_active = parseInt($('ul.pagination').find('li.active').text());
      var html = "";
      var operator_name = "";
      var customer_name = "";
      var menu_order = data.detail_order[0];
      operator_name = menu_order.waiter_name;
      if(operator_name.length > 15){
         operator_name = operator_name.substring(0,15)+"...";
      }

      customer_name = menu_order.customer_name;
      if(customer_name.length > 10){
         customer_name = customer_name.substring(0,10)+"...";
      }

      var  html = '<li class="col-md-2" style="padding: 0px;margin-right:3px;width:16.4%" id="reservation_'+data.reservation_id+'">';
      html += '<div class="title-bg title-bg-kitchen" style="padding:0px;">';
      html += '<div class="col-md-12" style="padding:0px 0px 0px 2px;">';
      html += '<div class="left" style="color: #881817;text-transform: uppercase;font-weight: bold;font-size: 11px;" title="'+menu_order.waiter_name+'">'+operator_name+'</div>';
      html += '<div class="right" style="color: #881817;text-transform: uppercase;font-weight: bold;font-size: 11px;">'+data.reservation_date+'</div>';
      html += '<div style="clear:both"></div>';
      html += '<div style="float:left;font-weight:bold;font-size: 29px;color:green;margin-right: 2px;line-height: 1;" title="'+menu_order.table_name+'">'+menu_order.table_name+'</div>';
      html += '<div class="left">';
      html += '<h4 class="title-name" style="color:#881817;margin: 0px;font-size: 12px;padding: 0px;text-align:left;" title="'+menu_order.customer_name+'">'+customer_name+'</h4>';
      html += '<div style="color: #881817;text-transform: uppercase;font-weight: bold;font-size: 11px;" title="ORDER ID : "'+menu_order.order_id+'>'+menu_order.order_id+'</div>';
      html += '</div>';
      html += '<button title="Print List Menu" class="btn btn-option-list pull-right print_list_menu" style="margin-top: 0px;margin-left:1px;font-size: 13px;font-weight: bold;line-height: 1;padding: 0px 5px;height: 29px;border-radius: 0px !important;" order-id="'+menu_order.order_id+'"><i class="fa fa-print"></i></button>';
      html += '<button class="btn btn-option-list pull-right btn-mode2-post" style="margin-top: 0px;margin-left: 0px;font-size: 15px;font-weight: bold;line-height: 1;padding: 0px 5px;height: 29px;border-radius: 0px !important;">POST</button>';
      html += '</div>';
      html += '</div>';
      html += '<div class="dark-theme-con" style="overflow:auto;overflow-x:hidden;height:220px;padding-bottom:15px;">';
      html += '<table class="kitchen-table" table-id="'+menu_order.table_id+'" order-id="'+menu_order.order_id+'" reservation-id="'+menu_order.reservation_id+'">';
      html += '<thead>';
      html += '<tr>';
      html += '<th style="width:90%;" colspan="2">MENU</th>';
      html += '<th style="width:10%;">JML</th>';
      html += '</tr>';
      html += '</thead>';
      html += '<tbody>';
      var notes = '';
      var order = data.detail_order;
      var max_char =17;
      var menu_name;
      for(var i=0;i<order.length;i++){

        html += '<tr class="kitchen-order" process_checker="'+order[i].process_checker+'">';
        html += '<td style="color:'+order[i].color+'; ?>;background-color:'+order[i].background_color+'">';
        notes = "";
        if (order[i].note) {
            notes+=order[i].note + '<br> ';
        }
        var option_list = order[i].option_list;
        for(var j=0;j<option_list.length;j++) {
            notes+='- '+option_list[j].option_value_name+'<br>';
        }
        var side_dish = order[i].side_dish_list;
        for(var j=0;j<option_list.length;j++) {
            notes+='- '+option_list[j].name+'<br>';
        }
        if(operator_name.length > max_char){
           customer_name = order[i].menu_name.substring(0,max_char)+"...";
        }
        html += '<span title="'+order[i].menu_name+'">'+order[i].menu_name+'</span>';
        html += '</td>';
        html += '<td>';
        if(notes.length > 0){
          html += '<div class="blink">';
          html += '<img src="'+AppMonitoring.baseUrl+'assets/img/notif.png" style="width: 9px;"/>';
          html += '<div class="popup-notes">'+notes+'</div>';
          html += '</div>'
        }
        html += '</td>';
        html += '<td class="border-side-white">';
        html += '<center>'+order[i].quantity+'</center>';
        html += '</td>';
        html += '<input id="menu_order_id" type="hidden" value="'+order[i].id+'"/>';
        html += '</tr>';
      }
      html +='</tbody>';      
      html += '</table>';
      html += '</div>';
      html += '</li>';       
      if(number_data == 0){
         $(".list-order-kitchen").html(html);
      }else{
         $(".list-order-kitchen").append(html);
      } 
      
      var pagination_html = "";
      for(var i=1;i<=number_data;i++){
        if(i == current_active){
          pagination_html = '<li class="active"><a href="javascript:void()"'+i+'>'+i+'</a></li>';
        }else{
          pagination_html = '<li><a href="'+AppMonitoring.baseUrl+'bosresto/monitoring/get_data/'+i+'">'+i+'</a></li>';

        }
      }  
      $("#pagination").html(pagination_html);   
      $("#number_of_data").val(number_data+1); 
      AppMonitoring.initUIEvent();
    },
    editReservation: function(data){
      console.log(data);
      var number_data = parseInt($("#number_of_data").val());
      var per_page =  parseInt($("#perPage").val());
      var current_active = parseInt($('ul.pagination').find('li.active').text());
      var html = "";
      var operator_name = "";
      var customer_name = "";
     
      operator_name= data.operator_name;
       if(operator_name.length > 15){
         operator_name = operator_name.substring(0,15)+"...";
       }
      customer_name= data.customer_name;
       if(customer_name.length > 10){
         customer_name = customer_name.substring(0,15)+"...";
      }
       
    
      html += '<div class="title-bg title-bg-kitchen" style="padding:0px;">';
      html += '<div class="col-md-12" style="padding:0px 0px 0px 2px;">';
      html += '<div class="left" style="color: #881817;text-transform: uppercase;font-weight: bold;font-size: 11px;" title="'+data.operator_name+'">'+operator_name+'</div>';
      html += '<div class="right" style="color: #881817;text-transform: uppercase;font-weight: bold;font-size: 11px;">'+data.reservation_date+'</div>';
      html +=  '<div style="clear:both"></div>';
      html += '<div style="float:left;font-weight:bold;font-size: 29px;color:green;margin-right: 2px;line-height: 1;" title="'+data.table_number+'">'+data.table_number+'</div>';
      html += '<div class="left">';
      html += '<h4 class="title-name" style="color:#881817;margin: 0px;font-size: 12px;padding: 0px;text-align:left;" title="'+data.customer_name+'">'+data.customer_name+'</h4>';
      html += '</div>';
      html += '<button class="btn btn-option-list pull-right btn-mode2-post" style="margin-top: 0px;margin-left: 0px;font-size: 15px;font-weight: bold;line-height: 1;padding: 0px 5px;height: 29px;border-radius: 0px !important;">POST</button>';
      html += '</div>';
      html += '</div>';
      html += '<div class="dark-theme-con" style="overflow:auto;overflow-x:hidden;height:220px;padding-bottom:15px;">';
      html += '<table class="kitchen-table" table-id="'+data.table_id+'" order-id="'+data.order_id+'" reservation-id="'+data.reservation_id+'">';
      html += '<thead>';
      html += '<tr>';
      html += '<th style="width:90%;" colspan="2">MENU</th>';
      html += '<th style="width:10%;">JML</th>';
      html += '</tr>';
      html += '</thead>';
      html += '<tbody>';
      html += '</tbody>';
      html += '</table>';
      html += '</div>';
  
      
      $('#reservation_'+data.reservation_id).html(html);
      
     
      var pagination_html = "";
      for(var i=1;i<=number_data;i++){
        if(i == current_active){
          pagination_html = '<li class="active"><a href="javascript:void()"'+i+'>'+i+'</a></li>';
        }else{
          pagination_html = '<li><a href="'+AppMonitoring.baseUrl+'bosresto/monitoring/get_data/'+i+'">'+i+'</a></li>';

        }
      }  
      $("#pagination").html(pagination_html);
      $("#number_of_data").val(number_data+1);    
      AppMonitoring.initUIEvent();
        
    },
    editReservationOrder:function(data){
      var number_data = parseInt($("#number_of_data").val());
      var per_page =  parseInt($("#perPage").val());
      var current_active = parseInt($('ul.pagination').find('li.active').text());
      var html = "";
      var operator_name = "";
      var customer_name = "";
      var menu_order = data.detail_order[0];
      operator_name = menu_order.waiter_name;
      if(operator_name.length > 15){
         operator_name = operator_name.substring(0,15)+"...";
      }

      customer_name = menu_order.customer_name;
      if(customer_name.length > 10){
         customer_name = customer_name.substring(0,10)+"...";
      }

      html += '<div class="title-bg title-bg-kitchen" style="padding:0px;">';
      html += '<div class="col-md-12" style="padding:0px 0px 0px 2px;">';
      html += '<div class="left" style="color: #881817;text-transform: uppercase;font-weight: bold;font-size: 11px;" title="'+menu_order.waiter_name+'">'+operator_name+'</div>';
      html += '<div class="right" style="color: #881817;text-transform: uppercase;font-weight: bold;font-size: 11px;">'+data.reservation_date+'</div>';
      html += '<div style="clear:both"></div>';
      html += '<div style="float:left;font-weight:bold;font-size: 29px;color:green;margin-right: 2px;line-height: 1;" title="'+menu_order.table_name+'">'+menu_order.table_name+'</div>';
      html += '<div class="left">';
      html += '<h4 class="title-name" style="color:#881817;margin: 0px;font-size: 12px;padding: 0px;text-align:left;" title="'+menu_order.customer_name+'">'+customer_name+'</h4>';
      html += '<div style="color: #881817;text-transform: uppercase;font-weight: bold;font-size: 11px;" title="ORDER ID : "'+menu_order.order_id+'>'+menu_order.order_id+'</div>';
      html += '</div>';
      html += '<button title="Print List Menu" class="btn btn-option-list pull-right print_list_menu" style="margin-top: 0px;margin-left:1px;font-size: 13px;font-weight: bold;line-height: 1;padding: 0px 5px;height: 29px;border-radius: 0px !important;" order-id="'+menu_order.order_id+'"><i class="fa fa-print"></i></button>';
      html += '<button class="btn btn-option-list pull-right btn-mode2-post" style="margin-top: 0px;margin-left: 0px;font-size: 15px;font-weight: bold;line-height: 1;padding: 0px 5px;height: 29px;border-radius: 0px !important;">POST</button>';
      html += '</div>';
      html += '</div>';
      html += '<div class="dark-theme-con" style="overflow:auto;overflow-x:hidden;height:220px;padding-bottom:15px;">';
      html += '<table class="kitchen-table" table-id="'+menu_order.table_id+'" order-id="'+menu_order.order_id+'" reservation-id="'+menu_order.reservation_id+'">';
      html += '<thead>';
      html += '<tr>';
      html += '<th style="width:90%;" colspan="2">MENU</th>';
      html += '<th style="width:10%;">JML</th>';
      html += '</tr>';
      html += '</thead>';
      html += '<tbody>';
      var notes = '';
      var order = data.detail_order;
      var max_char =17;
      var menu_name;
      for(var i=0;i<order.length;i++){

        html += '<tr class="kitchen-order" process_checker="'+order[i].process_checker+'">';
        html += '<td style="color:'+order[i].color+'; ?>;background-color:'+order[i].background_color+'">';
        notes = "";
        if (order[i].note) {
            notes+=order[i].note + '<br> ';
        }
        var option_list = order[i].option_list;
        for(var j=0;j<option_list.length;j++) {
            notes+='- '+option_list[j].option_value_name+'<br>';
        }
        var side_dish = order[i].side_dish_list;
        for(var j=0;j<option_list.length;j++) {
            notes+='- '+option_list[j].name+'<br>';
        }
        if(operator_name.length > max_char){
           customer_name = order[i].menu_name.substring(0,max_char)+"...";
        }
        html += '<span title="'+order[i].menu_name+'">'+order[i].menu_name+'</span>';
        html += '</td>';
        html += '<td>';
        if(notes.length > 0){
          html += '<div class="blink">';
          html += '<img src="'+AppMonitoring.baseUrl+'assets/img/notif.png" style="width: 9px;"/>';
          html += '<div class="popup-notes">'+notes+'</div>';
          html += '</div>'
        }
        html += '</td>';
        html += '<td class="border-side-white">';
        html += '<center>'+order[i].quantity+'</center>';
        html += '</td>';
        html += '<input id="menu_order_id" type="hidden" value="'+order[i].id+'"/>';
        html += '</tr>';
      }
      html +='</tbody>';      
      html += '</table>';
      html += '</div>';
      
      $('#reservation_'+data.reservation_id).html(html);
      
      var pagination_html = "";
      for(var i=1;i<=number_data;i++){
        if(i == current_active){
          pagination_html = '<li class="active"><a href="javascript:void()"'+i+'>'+i+'</a></li>';
        }else{
          pagination_html = '<li><a href="'+AppMonitoring.baseUrl+'bosresto/monitoring/get_data/'+i+'">'+i+'</a></li>';

        }
      }  
      $("#pagination").html(pagination_html);   
      $("#number_of_data").val(number_data+1); 
      AppMonitoring.initUIEvent();
    }
  }
});