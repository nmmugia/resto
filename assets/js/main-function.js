define(function() {
    return {
        browserReturn: false,
        myMessages: ['info', 'warning', 'error', 'success'],
        isGracePeriodModule: false,
        initFunc: function(App) {
            console.log("initFunc");
            // module highlight
            App.moduleHighlightInit();
            App.hideAllMessages();
            if (App.isGracePeriodModule) {
                setTimeout(function() {
                    App.showMessage("warning");
                }, 500);
            }

            if ($.fn.dataTable != undefined) {
                $.extend(true, $.fn.dataTable.defaults, {
                    "language": {
                        "emptyTable": "Tidak ada data",
                        "sProcessing": "Sedang memproses...",
                        "sLengthMenu": "Tampilkan _MENU_ entri",
                        "sZeroRecords": "Tidak ditemukan data yang sesuai",
                        "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                        "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                        "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                        "sInfoPostFix": "",
                        "sSearch": "Cari:",
                        "sUrl": "",
                        "oPaginate": {
                            "sFirst": "Pertama",
                            "sPrevious": "Sebelumnya",
                            "sNext": "Selanjutnya",
                            "sLast": "Terakhir"
                        }
                    }
                });
            }
            $(document).on("keypress keydown keyup blur", ".only_character", function(e) {
                ignore = [37, 38, 39, 40]
                if ($.inArray(e.which, [46, 8, 9, 27, 13]) !== -1 ||
                    // Allow: Ctrl+A
                    (e.which == 65 && e.ctrlKey === true) ||
                    // Allow: home, end, left, right
                    (e.which >= 35 && e.which <= 39) || e.which == 32) {
                    // let it happen, don't do anything
                    return;
                } else {
                    if (ignore.indexOf(e.which) < 0) {
                        var node = $(this);
                        node.val(node.val().replace(/[^a-zA-Z ]/g, ''));
                    }
                }


            });
            $(document).on("keypress", ".only_numeric", function(e, i) {
                if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                    return false;
                }
            });
            $(document).on("keypress", ".only_number", function(event, i) {
                if (event.which < 46 ||
                    event.which > 59) {
                    event.preventDefault();
                } // prevent if not number/dot

                if (event.which == 46 &&
                    $(this).val().indexOf('.') != -1) {
                    event.preventDefault();
                } // prevent if already dot
            });
            $(document).on("keypress", ".only_alpha_numeric", function(e, i) {
                if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which != 32) {
                    if ($.inArray(e.which, [46, 8, 9, 27, 13]) !== -1 ||
                        // Allow: Ctrl+A
                        (e.which == 65 && e.ctrlKey === true) ||
                        // Allow: home, end, left, right

                        (e.which >= 65 && e.which <= 90) ||
                        (e.which >= 97 && e.which <= 122)
                    ) {
                        // let it happen, don't do anything
                        return;
                    } else {
                        return false;
                    }
                }
            });
            // String.locale="jp";
            console.log("MAIN FUNCTION ALERT");
            App.initAlert();
            App.buttonSubmitEvent();


            var loc = window.location.href;
            setTimeout(function() {
                    $(".nav a[data-active='active']").addClass("active").parents(".nav:first").collapse("toggle");
                }, 500)
                // $('.nav').each(function(){
                // var temp = $(this).children().children();
                // for (var i = temp.length - 1; i >= 0; i--) {
                // if($(temp[i]).attr('data-active') == 'active'){
                // $(temp[i]).addClass('active');
                // $(this).collapse('toggle');
                // return;
                // }

            // };

            // }); 
            if ($('#side-menu').length > 0) {
                $('#side-menu').metisMenu();

                // $('.sidemenu').each(function(){
                // var exists = -1;
                // var exists = this.baseURI.search($(this).attr('pathname'));
                // var res = this.baseURI.replace(App.baseUrl, "");
                // if(exists != -1 && $(this).attr('pathname') !="" && res == $(this).attr('pathname')){
                // $(this).addClass('active');

                // $(this).parents().collapse('toggle');
                // return false;

                // }
                // });
                $('.sidemenu').each(function() {
                    var exists = -1;
                    var exists = this.baseURI.search($(this).attr('pathname'));
                    var res = this.baseURI.replace(App.baseUrl, "");
                    var links = $(this).attr("links");
                    if (links == undefined) links = "";
                    check_exists = -1;
                    relatedLinks = links.split(",");
                    if (relatedLinks.length > 0) {
                        for (x = 0; x < relatedLinks.length; x++) {
                            check = this.baseURI.search(relatedLinks[x]);
                            if (check != -1) {
                                check_exists = 1;
                            }
                        }
                    }
                    if ((exists != -1 && $(this).attr('pathname') != "" && res == $(this).attr('pathname')) || (links != "" && check_exists == 1)) {
                        $(this).addClass('active');

                        $(this).parents().collapse('toggle');
                        return false;

                    }
                });
            }
			
			$(document).on('click', '#call_waiter', function (e) {
                var baseUrl = $('#base_url').val();
				$.ajax({
					url:baseUrl + "checker/call_waiter",
					dataType:"JSON",
					success:function(response){
						if(response.waiter_online>0){
							$("#popup-ajax").html(response.content);
							$("#popup-ajax").show();              
						}else{
							App.alert("Tidak ada waiter yang sedang online, silahkan coba beberapa saat lagi!");
						}
					}
				});
			});
				  
			$(document).on('click', '#btn_call_waiter', function (e) {
				var user_id=$("#waiter_user_id").val();
                var baseUrl = $('#base_url').val();
				if(user_id==undefined)user_id="";
				if(user_id!=""){
					$.ajax({
						url:baseUrl + "checker/call_waiter",
						type:"POST",
						dataType:"JSON",
						data:{user_id:user_id},
						success:function(response){
							App.socket.emit('cm_notify_call', {
								notification : response.notification
							});
							$("#popup-ajax #waiter_user_id").val("");
							$("#popup-ajax").hide();
						}
					});
				}else{
					App.alert("Silahkan pilih waiter terlebih dahulu!");
				}
			});
			
			$(document).on('click', '.btn-cancel', function (e) {
				$("#popup-ajax #waiter_user_id").val("");
				$("#popup-ajax").hide();
			});
        },
        initAlert: function() {
            /* Init Alert*/



            var html = '<div class="modal fade" id="alert_container" tabindex="10000" style="z-index:100001;" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">' +
                '<div class="modal-dialog">' +
                '<div class="modal-content">' +
                '<div class="modal-header">' +
                '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                '<h4 class="modal-title" id="exampleModalLabel"> </h4>' +
                '</div>' +
                '<div class="modal-body"><p><b></b></p>' +

                '</div>' +
                '<div class="modal-footer">' +
                ' <button type="button" class="btn btn-default btn-cancel" data-dismiss="modal">Cancel</button>' +
                '<button type="button" class="btn btn-danger btn-ok">Ok</button>' +
                ' </div>' +
                '</div>' +
                ' </div>' +
                '</div>';


            $("body").append(html);
        },
        alert: function(text, callback) {
            $("#alert_container .modal-body p b").html(text);

            $("#alert_container .btn-danger").removeClass("hide");
            $("#alert_container .btn-cancel").addClass("hide");
            $("#alert_container .btn-refresh").addClass("hide");

            $("#alert_container").modal("show");

            $("#alert_container .btn-ok,#alert_container .close").bind("click", function() {
                if (callback != null) {
                    callback();
                }
                //$(".modal-backdrop").remove();
                $("#alert_container").modal("hide");
                $(this).unbind("click");
            });

        },
        alertRefresh: function(text, callback) {
            $("#alert_container .modal-body p b").html(text);

            $("#alert_container .btn-danger").addClass("hide");
            $("#alert_container .btn-cancel").addClass("hide");
            $("#alert_container .btn-refresh").removeClass("hide");

            $("#alert_container").modal("show");

            $("#alert_container .btn-refresh").bind("click", function() {
                if (callback != null) {
                    callback();
                }
                $(".modal-backdrop").remove();
                $(this).unbind("click");
            });

        },

        confirm: function(text, callback, argumen, nocallback) {
            $("#alert_container .modal-body p b").text(text);

            $("#alert_container .btn-danger").removeClass("hide");
            $("#alert_container .btn-cancel").removeClass("hide");
            $("#alert_container .btn-refresh").addClass("hide");

            $("#alert_container").modal("show");


            $("#alert_container .btn-ok").bind("click", function() {

                $(".modal-backdrop").remove();
                $("#alert_container").modal("hide");
                $(this).unbind("click");
                $("#alert_container .btn-cancel").unbind("click");
                callback(argumen);
            });
            $("#alert_container .btn-cancel,#alert_container .close").bind("click", function() {
                if (nocallback != null)
                    nocallback();


                $(".modal-backdrop").remove();

                $(this).unbind("click");
                $("#alert_container .btn-ok").unbind("click");



            });


        },

        buttonSubmitEvent: function() {
            var btnAct;

            /*$('.qty-input').on('keydown', function (e) {
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
            });*/
            $(document).ajaxStop(function() {
                $('.deleteNow').on('click', function(e) {
                    e.preventDefault();
                    var object = $(this);

                    function deleteNow() {
                        window.location = object.attr("href");
                    }

                    App.confirm('Anda yakin ingin menghapus ' + $(this).attr('rel') + '?', deleteNow);

                });
            });

            $("button[name=btnAction]").on('click', function(event) {
                event.preventDefault();
                btnAct = $(this).attr("value");
                $(".form-ajax").trigger("submit");
            });

            $('.form-ajax').on("submit", function(evt) {
                App.overlayUI.show();
                var myForm = $(this);

                var target = $('.result');
                target.html('');

                $('.NumericOnly').each(function() {
                    var pattern = /^([0-9]*)|([0]+)$/;
                    var checkNum = pattern.test($(this).val());
                    if (checkNum === false) {
                        target.empty().html('<div class="alert alert-danger" role="alert">' + $(this).attr('field-name') + ' hanya boleh memuat angka</div>');
                        App.overlayUI.hide();
                        evt.preventDefault();
                        return false;
                    }
                });

                $('.NumericWithZero').each(function() {
                    if ($(this).val() != '') {
                        var pattern = /^([0-9]*)$/;
                        var checkNum = pattern.test($(this).val());
                        if (checkNum === false) {
                            target.empty().html('<div class="alert alert-danger" role="alert">' + $(this).attr('field-name') + ' hanya boleh memuat angka</div>');
                            App.overlayUI.hide();
                            evt.preventDefault();
                            return false;
                        }
                    }
                });

                $('.NumericDecimal').each(function() {
                    if ($(this).val() != '') {
                        var pattern = /^([0-9][.0-9]*)|([0]+)$/;
                        var checkNum = pattern.test($(this).val());
                        if (checkNum === false) {
                            target.empty().html('<div class="alert alert-danger" role="alert">' + $(this).attr('field-name') + ' hanya boleh memuat angka</div>');
                            App.overlayUI.hide();
                            evt.preventDefault();
                            return false;
                        }
                    }
                });

                $('.requiredDropdown,.requiredDropDown').each(function() {
                    if ($(this).val() == '0' || $(this).val() == '' || $(this).val() == undefined) {
                        target.empty().html('<div class="alert alert-danger" role="alert">Anda harus memilih pilihan pada ' + $(this).attr('field-name') + '</div>');
                        App.overlayUI.hide();
                        evt.preventDefault();
                        return false;
                    }
                });

                $('.requiredTextField').each(function() {
                    if ($(this).val() == '') {
                        target.empty().html('<div class="alert alert-danger" role="alert">Bagian ' + $(this).attr('field-name') + ' dibutuhkan</div>');
                        App.overlayUI.hide();
                        evt.preventDefault();
                        return false;
                    }
                });

                $('.maxUploadSize').each(function() {
                    var max_img_size = $(this).data('maxsize');
                    if (max_img_size != '') {
                        var input = $(this)[0];
                        // check for browser support (may need to be modified)
                        if (input.files && input.files.length == 1) {
                            if (input.files[0].size > max_img_size) {
                                target.empty().html('<div class="alert alert-danger" role="alert">' + "File harus kurang dari " + Math.round(max_img_size / 1024 / 1024) + "MB" + '</div>');
                                App.overlayUI.hide();
                                evt.preventDefault();
                                return false;
                            }
                        }
                    }
                });

                $('html, body').animate({
                    scrollTop: 0
                }, 'fast');

                var tempElement = $("<input type='hidden'/>");
                tempElement
                    .attr("name", 'btnAction')
                    .val(btnAct)
                    .appendTo(myForm);
                tempElement = '';
            });
        },
        moduleHighlightInit: function() {
			this.isGracePeriodModule = $("#isGracePeriodModule").val();
			
			// When message is clicked, hide it
			$('.message').click(function() {
				$(this).animate({
					top: -$(this).outerHeight()
				}, 500);
			});
        },
        hideAllMessages: function() {
            var messagesHeights = new Array(); // this array will store height for each
            for (i = 0; i < this.myMessages.length; i++) {
                messagesHeights[i] = $('.' + this.myMessages[i]).outerHeight(); // fill array
                $('.' + this.myMessages[i]).css('top', -messagesHeights[i]); //move element outside viewport
            }
        },
        showMessage: function(type) {
            console.log("showMessage : " + type);
            this.hideAllMessages();
            $('.' + type).animate({
                top: "0"
            }, 500);
        }
    }
});