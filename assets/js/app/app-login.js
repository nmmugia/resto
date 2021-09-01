/**
 * Created by alta falconeri on 12/15/2014.
 */

define([
    "jquery",
    "jquery-ui",
    "bootstrap"
], function ($, ui) {
    return {
        inputLogin     : $(".input-pin"),
        loadingOverlay : $("#cover"),
		myMessages : ['info','warning','error','success'],
		isExpiredModule : $("#isExpiredModule").val(),
        init           : function () {
            AppLogin.loadingOverlay.show();
            console.log("App Login inited..");
			
            var is_mobile = /mobile|android/i.test(navigator.userAgent);

            if (is_mobile) {
                AppLogin.inputLogin.on('focus', function () {
                    AppLogin.inputLogin.blur();
                });
            }

            AppLogin.inputLogin.on('keydown', function (e) {
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

            AppLogin.inputLogin.keyup(function (event) {
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
                //AppLogin.inputLogin.focus();
                var textInput = $(this).text();
                AppLogin.inputLogin.val(AppLogin.inputLogin.val() + textInput);
            });
            $(".btn-backspace").on(clicked, function () {
                //AppLogin.inputLogin.focus();
                var textInput = AppLogin.inputLogin.val();
                AppLogin.inputLogin.val(textInput.substr(0,textInput.length-1));
            });
            

            $(".btn-clear").on(clicked, function () {
                AppLogin.inputLogin.val('');
                //AppLogin.inputLogin.focus();
            });

            AppLogin.inputLogin.focus();
            AppLogin.loadingOverlay.hide();
			
			// module highlight
			AppLogin.moduleHighlightInit();
			AppLogin.hideAllMessages();
			if(AppLogin.isExpiredModule){
				setTimeout(function(){ 
					AppLogin.showMessage("error"); 
				}, 500);
			}
			
        },
		moduleHighlightInit: function(){
			// When message is clicked, hide it
			$('.message').click(function(){
				$(this).animate({top: -$(this).outerHeight()}, 500);
			}); 
		},
		hideAllMessages: function(){
			var messagesHeights = new Array(); // this array will store height for each
			for (i=0; i<AppLogin.myMessages.length; i++){
				messagesHeights[i] = $('.' + AppLogin.myMessages[i]).outerHeight(); // fill array
				$('.' + AppLogin.myMessages[i]).css('top', -messagesHeights[i]); //move element outside viewport
			}
		},
		showMessage: function(type){
			AppLogin.hideAllMessages();
			$('.'+type).animate({top:"0"}, 500);
		}
    };
});