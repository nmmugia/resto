
define([
    "jquery",
    "jquery-ui",
    "bootstrap",
    "bootstrapToggle",
], function ($, ui) {
    return {
        nodeUrl        : $('#node_url').val(),
        baseUrl        : $('#base_url').val(),
        openCloseStatus : $('#open_close_status').val(),
        closeOverlay      : $("#cover_close"),
        pageWrapper      : $("#inner_container"),
        popupOpenClose      : $("#popup-open-close"),
        inputPin:$("#pin_input"),
        init           : function () {
			$('[data-tooltip="tooltip"]').tooltip();
			$('#openCloseToggle').bootstrapToggle();
			AppNav.baseUrl = $('#base_url').val();
            AppNav.closeOverlay.show();
                if(AppNav.openCloseStatus ==1){
                }else{
                    AppNav.pageWrapper.css("visibility","hidden"); 
                    AppNav.popupOpenClose.show();                   
                }
                AppNav.closeOverlay.hide();

            var loc = window.location.href;
            $('.icon-bar').each(function(){

                var temp = $(this).children();

                for (var i = temp.length - 1; i >= 0; i--) {
                   if($(temp[i]).attr('data-active') == 'active'){
                    $(temp[i]).addClass('active');
                    return;
                }

            };
            
            });
            $('.btn-pin').prop('disabled', false);
            $('.btn-open').prop('disabled', false);
            $("#popup-feature-confirmation button[data-value]").on("click",function(){
              inputPinConfirmation=$("#popup-feature-confirmation .input-pin");
              textInput = $(this).text();
              inputPinConfirmation.val(inputPinConfirmation.val() + textInput);
            });
            $("#btn-feature-confirmation-clear").on("click",function(){
              inputPinConfirmation=$("#popup-feature-confirmation .input-pin");
              inputPinConfirmation.val("");
            });
            $("#btn-feature-confirmation-ok").on("click",function(){
              inputPinConfirmation=$("#popup-feature-confirmation .input-pin").val();
              feature_confirmation=$("#popup-feature-confirmation").attr("feature_confirmation")
              element=$("#popup-feature-confirmation").attr("element")
              event_name=$("#popup-feature-confirmation").attr("event_name")
              not_firing=$("#popup-feature-confirmation").attr("not_firing")
              $.ajax({
                url:AppNav.baseUrl + "login/check_feature_confirmation",
                type:"POST",
                dataType:"JSON",
                data:{pin:inputPinConfirmation,users_unlock:feature_confirmation},
                success:function(response){
                  if(response.status==true){
                    $(element).attr("feature_confirmation","");
                    $("#popup-feature-confirmation .input-pin").val("");
                    $("#popup-feature-confirmation").attr("feature_confirmation","")
                    $("#popup-feature-confirmation").attr("element","")
                    $("#popup-feature-confirmation").attr("event_name","")
                    $("#popup-feature-confirmation").attr("not_firing","")
                    $("#popup-feature-confirmation").hide();
                    if(element=="#split-single-right"){
                      $("#split-single-left").attr("feature_confirmation","");
                    }else if(element=="#split-single-left"){
                      $("#split-single-right").attr("feature_confirmation","");
                    }else if(element==".btn-void-order"){
                      if($("#user_unlock_void").length==0){
                        $("body").prepend("<input type='hidden' id='user_unlock_void'>");
                      }
                      $("#user_unlock_void").val(response.data.id);
                    }else if(element == "#member-payment"){
                      if($("#user_unlock_member_bill").length==0){
                        $("body").prepend("<input type='hidden' id='user_unlock_member_bill'>");
                      }
                      $("#user_unlock_member_bill").val(response.data.id);
                    }
                    
                    // console.log(element);
                    if(not_firing==1){
						if(!$(element).hasClass("delete_order_all")){
							setTimeout(function () { $(element).attr("feature_confirmation", feature_confirmation); }, 100);
						} else {
							$(element).attr("skip_confirm", true); 
						}
						$(element).trigger(event_name);
                      // $(element).attr("feature_confirmation",feature_confirmation);
                    }
                  }else{
                    AppNav.alert("PIN salah!");
                  }
                }
              });
            });
            $('#btn-feature-confirmation-cancel').on('click', function (e) {
                $('#popup-feature-confirmation').hide();
            });
            AppNav.openCloseProcess();
            AppNav.initFeatureConfirmation();

        },
		initFeatureConfirmation:function(){
            $('.menu_petty_cash').on('click', function (e) {
                user_confirmation = $(this).attr("feature_confirmation");
                if (user_confirmation == undefined)user_confirmation = "";
                if (user_confirmation != "") {
                    e.preventDefault();
                    AppNav.showConfirmationPIN(user_confirmation, '.menu_petty_cash', "click", 1);
                } else {
                    window.location.href = $(this).attr("href");
                }
            });
            $('.menu_refund').on('click', function (e) {
                user_confirmation = $(this).attr("feature_confirmation");
                if (user_confirmation == undefined)user_confirmation = "";
                if (user_confirmation != "") {
                    AppNav.showConfirmationPIN(user_confirmation, '.menu_refund', "click", 1);
                    return false;
                } else {
                    window.location.href = $(this).attr("href");
                }
            });
            $('.menu_report').on('click', function (e) {
                e.preventDefault();
                user_confirmation = $(this).attr("feature_confirmation");
                if (user_confirmation == undefined)user_confirmation = "";
                if (user_confirmation != "") {
                    AppNav.showConfirmationPIN(user_confirmation, '.menu_report', "click", 1);
                    return false;
                }
                var popup = $(".popup-sales-report");
                var request = $.ajax({
                    type : "POST",
                    url  : AppNav.baseUrl + 'cashier/get_sales_today'
                });

                request.done(function(response) {
                    if (response != '') {
                        var parsedObject = JSON.parse(response);
                        var summary = parsedObject.summary;
                        var cash = parsedObject.total_cash;
                        var debit = parsedObject.total_debit;
                        var credit = parsedObject.total_credit;
                        popup.find("#sum_total_price").html(AppNav.moneyFormat((summary.total_sell != null) ? parseInt(summary.total_sell) : 0, "Rp"));
                        popup.find("#total_cash").html(AppNav.moneyFormat((cash.sum_total_price != null) ? parseInt(cash.sum_total_price) : 0, "Rp"));
                        popup.find("#total_transaction").html(parseInt(summary.total_transaction));
                        popup.find("#total_debit").html(AppNav.moneyFormat((debit.sum_total_price != null) ? parseInt(debit.sum_total_price) : 0, "Rp"));
                        popup.find("#total_customer_count").html(parseInt(summary.total_customer_count));
                        popup.find("#total_credit").html(AppNav.moneyFormat((credit.sum_total_price != null) ? parseInt(credit.sum_total_price) : 0, "Rp"));
                        popup.find("#total_quantity_order").html(parseInt(summary.total_quantity_order));
                        popup.find("#total_discount").html(AppNav.moneyFormat((summary.total_discount != null) ? parseInt(summary.total_discount) : 0, "Rp"));
                        popup.find("#total_count_voucher").html(parseInt(summary.total_count_voucher));
                        popup.find("#total_tax").html(AppNav.moneyFormat((summary.total_tax != null) ? parseInt(summary.total_tax) : 0, "Rp"));
                        popup.show();
                    }
                });

                request.fail(function (jqXHR, textStatus) {
                    if (textStatus == 'timeout') {
                        AppNav.alert($('#server-timeout-message p').text());
                    }
                    window.location.reload(true);
                });
            });
		},
        showConfirmationPIN:function(user_confirmation,element,event_name,not_firing){
          if(not_firing==undefined)not_firing=0;
          $("#popup-feature-confirmation").attr("feature_confirmation",user_confirmation)
          $("#popup-feature-confirmation").attr("element",element)
          $("#popup-feature-confirmation").attr("event_name",event_name)
          $("#popup-feature-confirmation").attr("not_firing",not_firing)
          $("#popup-feature-confirmation").show();
        },
        openCloseProcess: function(){ 
            $('.btn-cancel').on('click', function (e) {
                $('#popup-openclose').hide();
            });

            $('.btn-toggle-pin, .btn-toggle-openclose').on('click', function (e) {
                $('#popup-openclose').show();
                $("#openclose_begin_balance").val($("#begin_balance_value").val());
            }); 
            $("#openclose_begin_balance").on("focus",function(){
              
              // AppNav.inputPin = $("#openclose_begin_balance") ;
            });

             $("#pin_input").on("focus",function(){ 
              AppNav.inputPin =  $("#pin_input");  
            }); 
            var is_mobile = /mobile|android/i.test(navigator.userAgent);

            if (is_mobile) {
                AppNav.inputPin.on('focus', function () {
                    AppNav.inputPin.blur();
                });
            }

            AppNav.inputPin.on('keydown', function (e) {
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
            });

            AppNav.inputPin.keyup(function (event) {
                if (event.keyCode == 13) {
                    $(".btn-enter").click();
                }
            });

            var isTouchDevice = (('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch);
            var clicked = 'click';
            // if (isTouchDevice) {
                // clicked = 'touchend';
            // }
            $(".btn-pin").on(clicked, function () { 
                AppNav.inputPin.focus();
                var textInput = $(this).text();
                AppNav.inputPin.val(AppNav.inputPin.val() + textInput);
            });
            $(".btn-clear").on(clicked, function () {
                AppNav.inputPin.val('');
                AppNav.inputPin.focus();
            });


            $("#btn-enter-open").on(clicked, function () {
                begin_balance=parseInt($("#openclose_begin_balance").val());
                cash_on_hand=parseFloat($("#openclose_cash_on_hand").val());
                if(isNaN(begin_balance))begin_balance=0;
                if(isNaN(cash_on_hand))cash_on_hand=0;
                if(AppNav.openCloseStatus!=1){
                  if(begin_balance<0){
                    AppNav.alert("Saldo Awal harus >=0");
                    return;
                  }                  
                }
                var request = $.ajax({
                    type    : "POST",
                    url     : AppNav.baseUrl + 'cashier/update_open_close',
                    data    : {
                        cash_on_hand   : cash_on_hand,
                        begin_balance   : begin_balance,
                        pin   : AppNav.inputPin.val(),
                        status   : AppNav.openCloseStatus,
                    }
                });
                request.done(function (msg) {
                    if (msg != '') {
                        var parsedObject = JSON.parse(msg);

                        if (parsedObject.status == true) {
                          try{
                            AppTable.socket.emit('cm_notify_open_close_cashier', {
                              status : parsedObject.status
                            });
                          }catch(e){
                            AppCashier.socket.emit('cm_notify_open_close_cashier', {
                              status : parsedObject.status
                            });
                          }
                          setTimeout(function () { window.location = parsedObject.url; }, 100);
                        }else{
                          AppNav.alert(parsedObject.msg);
                        }
                    } else {
                        window.location.reload(true);
                    }
                });



                request.fail(function (jqXHR, textStatus) {
                    if (textStatus == 'timeout') {
                        AppNav.alert($('#server-timeout-message p').text());
                    }
                    window.location.reload(true);

                });


            });

            
            AppNav.inputPin.focus();


        },//end open close

        moneyFormat: function (n, currency) {
            return currency + " " + n.toFixed(0).replace(/./g, function (c, i, a) {
                return i > 0 && c !== "," && (a.length - i) % 3 === 0 ? "." + c : c;
            });
        },

    };
});