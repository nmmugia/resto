/**
 * Created by alta falconeri on 12/15/2014.
 */

define([
    "jquery",
    "bootstrap"
], function ($, ui) {
    return {
        nodeUrl             : $('#node_url').val(),
        baseUrl             : $('#base_url').val(),
        socket              : false,
        loadingOverlay      : $("#cover"),
        // userId              : $("#user_id").val(),
        // groupId             : $("#group_id").val(),
        // groupName           : $("#group_name").val(),
        // userName            : $("#user_name").val(),
        init                : function () {
            console.log("App Stock inited..");
			AppStock.loadingOverlay.show();
            AppStock.initFunc(AppStock);
            try {
                AppStock.initSocketIO();
            } catch (err) {
                AppStock.loadingOverlay.hide();
                $('#server-error-message').dialog(
                    {
                        dialogClass   : "no-close",
                        modal         : true,
                        closeOnEscape : false,
                        buttons       : {
                            Reload : function () {
                                window.location.reload(true);
                            }
                        }
                    }
                );
            }
        },
        initSocketIO        : function () {
			AppStock.socket = io(AppStock.nodeUrl,{
                'reconnectionAttempts': 2
            });
            AppStock.socket.on('reconnect_failed', function () {
                AppStock.loadingOverlay.hide();
                $('#server-error-message').dialog(
                    {
                        dialogClass   : "no-close",
                        modal         : true,
                        closeOnEscape : false,
                        buttons       : {
                            Reload : function () {
                                window.location.reload(true);
                            }
                        }
                    }
                );
            });
            AppStock.socket.on('connected', function (data) {
                console.log('Socket.IO connected');
				AppStock.initUIEvent();
                AppStock.loadingOverlay.hide();

				AppStock.socket.on('sm_notify_menu_available_status', function (data) {
					console.log(data);
                    // var tablenew = $('#status_menu_' + data.order_menu_id);
                    // var content = data.status_name;
                    // tablenew.html(content);
                });
            });
		},
        initUIEvent         : function () {
            $(document).on('click', '.btn-cancel', function() {
                $(".popup-block").hide();
            });

			$(document).on('click', '.menu-order', function () {
                var arrDiv = $(this).children();
                var set_menu_stock = $("#stock_menu_by_inventory").val();
                if (arrDiv != '') {
                    $.ajax({
                        type : "POST",
                        url  : AppStock.baseUrl + 'stock/get_menu_by_catergory',
                        data : {
                            category_id : $(arrDiv[2]).val()
                        }
                    }).done(function (resp) {
							// console.log(resp);
                        if (resp != '') {

                            var parsedObject = JSON.parse(resp);
                            var htmlString = '';
							var statusStock = '';
                            $('.category-name').text($(arrDiv[1]).text());
                            if (parsedObject.length != 0) {
                                for (i = 0; i < parsedObject.length; i++) {
                                    var image = AppStock.baseUrl + '/assets/img/default-menus.jpg';
                                    if (parsedObject[i].icon_url != '') {
                                        image = AppStock.baseUrl + parsedObject[i].icon_url;
                                    }

									if(parsedObject[i].available==0){
										statusStock = "Set Available";
									}
									else
										statusStock = (set_menu_stock != 1) ? "Set Available" : "Set Habis";
                                    htmlString += "<div class=\"menu-stock\">" +
                                    "<img src=\"" + image + "\" alt=\"menu\" />" +
                                    "<p>" + parsedObject[i].menu_name + "</p>" +
									"<button class=\"btn btn-trans stock-btn\" id=\""+parsedObject[i].available+"\">"+statusStock+"</button>"+
									"<input id=\"menu_id\" type=\"hidden\" value=\""+ parsedObject[i].id +"\"/>"+
                                    "</div>"
                                }
                            } else
                                htmlString += '<h5 style="color:#898989">Tidak ada menu</h5>'

                            $('.container-menus').html(htmlString);
                        }
						else
							AppStock.alert('Gagal menampilkan menu. Silahkan coba lagi. Jika masalah masih terjadi mohon hubungi administrator.');
                    }).fail(function () {
                        AppStock.alert('Gagal menampilkan menu. Silahkan coba lagi. Jika masalah masih terjadi mohon hubungi administrator.');
                    });
                }
            });

			$(document).on('click', '.stock-btn', function (e) {
				console.log('button set available pressed...');
				var statusAvailable = this.id;
				var row = $(this).closest("div");
				var arrDiv = row.children();
                var set_menu_stock = $("#stock_menu_by_inventory").val();
                var menu_quantity = $(this).data('menu-quantity');
                var menu_id = $(arrDiv[3]).val();

                if (set_menu_stock != 1) {
                    e.preventDefault();
                    AppStock.setAvailableMenu(menu_id);
                } else {
                    $.ajax({
                        type : "POST",
                        url  : AppStock.baseUrl + 'stock/update_available_status',
                        data : {
                            menu_id  : menu_id,
                            available : statusAvailable,
                            quantity : menu_quantity
                        }
                    }).done(function (resp) {
                        if (resp != '') {
                            /*AppStock.socket.emit('cm_notify_menu_available_status', {
                                menu_id : $(arrDiv[3]).val(),
                                menu_name  : $(arrDiv[1]).text(),
                                available : statusAvailable
                            });*/
                            if(statusAvailable == 1)
                            {
                                $(arrDiv[2]).text('Set Available');
                                $(arrDiv[2]).attr("id",0);
                            }
                            else
                            {
                                $(arrDiv[2]).text('Set Habis');
                                $(arrDiv[2]).attr("id",1);
                            }
                        }
                    }).fail(function () {
                        AppStock.alert('Gagal mengupdate status ketersediaan. Silahkan coba lagi. Jika masalah masih terjadi mohon hubungi administrator.');
                    });

                    e.preventDefault();
                }
			});
        },

        setAvailableMenu : function (menu_id) {
            var request = $.ajax({
                type : "POST",
                url  : AppStock.baseUrl + 'stock/get_menu',
                data : {
                    menu_id  : menu_id
                }
            });

            request.done(function(resp) {
                if (resp != '') {
                    var parsedObject = JSON.parse(resp);
                    $(".popup-available-menu").show();
                    $(".title-popup").text(parsedObject.menu_name);
                    $("#menu_quantity_val").val(parsedObject.menu_quantity);
                    $("#menu_id_val").val(parsedObject.id);
                }
            });

            request.fail(function(jqXHR, textStatus) {
                AppStock.alert('Gagal mengambil data menu. Silahkan coba lagi. Jika masalah masih terjadi mohon hubungi administrator.');
            });

            request.always(function() {});

            $("#btn-ok").on('click', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                var request = $.ajax({
                    type : "POST",
                    url  : AppStock.baseUrl + 'stock/update_available_status',
                    data : {
                        menu_id  : $("#menu_id_val").val(),
                        quantity : $("#menu_quantity_val").val()
                    }
                });

                request.done(function(msg) {
                    if (msg != '') {
                        $(".popup-available-menu").hide();
                    }
                });

                request.fail(function(jqXHR, textStatus) {
                    AppStock.alert('Gagal mengupdate status ketersediaan. Silahkan coba lagi. Jika masalah masih terjadi mohon hubungi administrator.');
                });

                request.always(function() {});
            });
        }
    };
});