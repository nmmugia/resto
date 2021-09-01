
define([
    "jquery",
    "jquery-ui",
], function ($, ui) {
    return {
        nodeUrl        : $('#node_url').val(),
        baseUrl        : $('#base_url').val(),
        openCloseStatus : $('#open_close_status').val(),
        closeOverlay      : $("#cover_close"),
        init           : function () {
    
            console.log(AppOrder);
            AppOrder.closeOverlay.show();
                if(AppOrder.openCloseStatus ==1){
                AppOrder.closeOverlay.hide();
            }

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
            
            AppOrder.openCloseProcess();

        },
        
        openCloseProcess: function(){
        $('.btn-toggle-pin').on('click', function (e) {
            $('#popup-openclose').show();
        });

         var inputPin = $(".input-pin");
         var is_mobile = /mobile|android/i.test(navigator.userAgent);

            if (is_mobile) {
                inputPin.on('focus', function () {
                    inputPin.blur();
                });
            }

            inputPin.on('keydown', function (e) {
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

            inputPin.keyup(function (event) {
                if (event.keyCode == 13) {
                    $(".btn-enter").click();
                }
            });

            var isTouchDevice = (('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch);
            var clicked = 'click';
            if (isTouchDevice) {
                clicked = 'touchend';
            }
            $(".btn-pin").on(clicked, function () {
                inputPin.focus();
                var textInput = $(this).text();
                inputPin.val(inputPin.val() + textInput);
            });

            $(".btn-clear").on(clicked, function () {
                inputPin.val('');
                inputPin.focus();
            });


            $("#btn-enter-open").on(clicked, function () {
                var request = $.ajax({
                type    : "POST",
                url     : AppOrder.baseUrl + 'cashier/update_open_close',
                data    : {
                    pin   : inputPin.val(),
                    status   : AppOrder.openCloseStatus,
                }
            });
                request.done(function (msg) {
                    if (msg != '') {
                        var parsedObject = JSON.parse(msg);

                        if (parsedObject.status == true) {
                            window.location = parsedObject.url;

                        }else{
                            AppOrder.alert(parsedObject.msg);
                        }


                    } else {
                        window.location.reload(true);
                    }
                });



                request.fail(function (jqXHR, textStatus) {
                    if (textStatus == 'timeout') {
                        AppOrder.alert($('#server-timeout-message p').text());
                    }
                    window.location.reload(true);

                });


            });

            
            inputPin.focus();


        },//end open close

    };
});