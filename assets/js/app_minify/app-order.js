define(["jquery","jquery-ui"],function(a,b){return{nodeUrl:a("#node_url").val(),baseUrl:a("#base_url").val(),openCloseStatus:a("#open_close_status").val(),closeOverlay:a("#cover_close"),init:function(){console.log(AppOrder),AppOrder.closeOverlay.show(),1==AppOrder.openCloseStatus&&AppOrder.closeOverlay.hide();window.location.href;a(".icon-bar").each(function(){for(var b=a(this).children(),c=b.length-1;c>=0;c--)if("active"==a(b[c]).attr("data-active"))return void a(b[c]).addClass("active")}),AppOrder.openCloseProcess()},openCloseProcess:function(){a(".btn-toggle-pin").on("click",function(b){a("#popup-openclose").show()});var b=a(".input-pin"),c=/mobile|android/i.test(navigator.userAgent);c&&b.on("focus",function(){b.blur()}),b.on("keydown",function(b){-1!==a.inArray(b.keyCode,[46,8,9,27,13])||65==b.keyCode&&b.ctrlKey===!0||b.keyCode>=35&&b.keyCode<=39||(b.shiftKey||b.keyCode<48||b.keyCode>57)&&(b.keyCode<96||b.keyCode>105)&&b.preventDefault()}),b.keyup(function(b){13==b.keyCode&&a(".btn-enter").click()});var d="ontouchstart"in window||window.DocumentTouch&&document instanceof DocumentTouch,e="click";d&&(e="touchend"),a(".btn-pin").on(e,function(){b.focus();var c=a(this).text();b.val(b.val()+c)}),a(".btn-clear").on(e,function(){b.val(""),b.focus()}),a("#btn-enter-open").on(e,function(){var c=a.ajax({type:"POST",url:AppOrder.baseUrl+"cashier/update_open_close",data:{pin:b.val(),status:AppOrder.openCloseStatus}});c.done(function(a){if(""!=a){var b=JSON.parse(a);1==b.status?window.location=b.url:AppOrder.alert(b.msg)}else window.location.reload(!0)}),c.fail(function(b,c){"timeout"==c&&AppOrder.alert(a("#server-timeout-message p").text()),window.location.reload(!0)})}),b.focus()}}});