define(["jquery","jquery-ui","bootstrap"],function(a,b){return{inputLogin:a(".input-pin"),loadingOverlay:a("#cover"),myMessages:["info","warning","error","success"],isExpiredModule:a("#isExpiredModule").val(),init:function(){AppLogin.loadingOverlay.show(),console.log("App Login inited..");var b=/mobile|android/i.test(navigator.userAgent);b&&AppLogin.inputLogin.on("focus",function(){AppLogin.inputLogin.blur()}),AppLogin.inputLogin.on("keydown",function(b){-1!==a.inArray(b.keyCode,[46,8,9,27,13])||65==b.keyCode&&b.ctrlKey===!0||b.keyCode>=35&&b.keyCode<=39||(b.shiftKey||b.keyCode<48||b.keyCode>57)&&(b.keyCode<96||b.keyCode>105)&&b.preventDefault()}),AppLogin.inputLogin.keyup(function(b){13==b.keyCode&&a(".btn-enter").click()});var c="ontouchstart"in window||window.DocumentTouch&&document instanceof DocumentTouch,d="click";c&&(d="touchend"),a(".btn-pin").on(d,function(){var b=a(this).text();AppLogin.inputLogin.val(AppLogin.inputLogin.val()+b)}),a(".btn-backspace").on(d,function(){var a=AppLogin.inputLogin.val();AppLogin.inputLogin.val(a.substr(0,a.length-1))}),a(".btn-clear").on(d,function(){AppLogin.inputLogin.val("")}),AppLogin.inputLogin.focus(),AppLogin.loadingOverlay.hide(),AppLogin.moduleHighlightInit(),AppLogin.hideAllMessages(),AppLogin.isExpiredModule&&setTimeout(function(){AppLogin.showMessage("error")},500)},moduleHighlightInit:function(){a(".message").click(function(){a(this).animate({top:-a(this).outerHeight()},500)})},hideAllMessages:function(){var b=new Array;for(i=0;i<AppLogin.myMessages.length;i++)b[i]=a("."+AppLogin.myMessages[i]).outerHeight(),a("."+AppLogin.myMessages[i]).css("top",-b[i])},showMessage:function(b){AppLogin.hideAllMessages(),a("."+b).animate({top:"0"},500)}}});