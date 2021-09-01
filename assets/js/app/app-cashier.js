/**
 * Created by alta falconeri on 12/15/2014.
 */

define([
    "jquery",
    "jquery-ui",
    "payment",
    "bootstrap",
    'datatables',
    "datatables-bootstrap",
    "datetimepicker",
    "currency",
    "keyboard",
    "easyautocomplete",
    "select2",
    "chained",
], function ($, ui, payment) {
    return {
        nodeUrl: $('#node_url').val(),
        baseUrl: $('#base_url').val(),
        socket: false,
        isChangeTable: false,
        isSelectTableFirst: false,
        isSelectTableSecond: false,
        firstTableId: '',
        secondTableId: '',
        statusTable: '',
        loadingOverlay: $("#cover"),
        overlayUI: $("#cover"),
        userId: $("#user_id").val(),
        groupId: $("#group_id").val(),
        groupName: $("#group_name").val(),
        userName: $("#user_name").val(),
        isEdit: false,
        countMenuOld: '',
        orderIsView: $('#order_is_view').val(),
        lessPayments: $('#less_payments').val(),
        errorCardNumber: $('#error_card_number').val(),
        paymentType: 1, // 1:cash, 2:debit, 3:credit,4:voucher, 5:compliment, 6:pendingbill, 7:pending bill Employee
        isCheckout: $('#is_checkout').val(),
        inputNumber: $('.input-number'),
        timeoutVal: 15000,
        isTakeaway: $('#is_takeaway').val(),
        isDelivery: $('#is_delivery').val(),
        diningType: $('#dining_type').val(),
        useKitchen: $('#use_kitchen').val(),
        useRoleChecker: $('#use_role_checker').val(),
        customerpay: 0,
        bankAccountId: 0,
        customerCashPayment: 0,
        totalStockAvailable: 0,
        totalBill: parseInt($('#totalBill').val()),
        leftDataOrder: [],
        rightDataOrder: [],
        enumOrder: {
            SINGLE: 1,
            ALL: 2

        },
        subtotalLeft: 0,
        subtotalRight: 0,
        subtotalTaxRight: 0,
        subtotalTaxLeft: 0,
        grandTotalLeft: 0,
        grandTotalRight: 0,
        originGrandTotalRight: 0,
        totalTax: document.getElementById("total_tax") != null ? document.getElementById("total_tax").value : 0,
        discountTotal: 0,
        creditPayment: {
            code: 0,
            amount: 0,
            type: 3,
            bankId: 0,
            cardTypeId: 0,
            accountId: 0
        },
        debitPayment: {
            code: 0,
            amount: 0,
            type: 2,
            bankId: 0,
            cardTypeId: 0,
            accountId: 0
        },
        flazzPayment: {
            code: 0,
            amount: 0,
            type: 11,
            bankId: 0,
            accountId: 0
        },
        cashPayment: {
            code: 0,
            amount: 0,
            type: 1
        },
        voucherPayment: {
            code: 0,
            amount: 0,
            type: 4
        },
        complimentPayment: {
            code: 0,
            amount: 0,
            hpp: 0,
            type: 5,
            total_price: 0
        },
        pendingbill: {
            code: 0,
            amount: 0,
            type: 6,
            total_price: 0,
            code_name: "",
            is_banquet: 0,
        },
        pendingbillEmployee: {
            code: 0,
            amount: 0,
            type: 7,
            total_price: 0,
            code_name: ""
        },
        downPaymentBill: {
            code: 0,
            amount: 0,
            type: 10,
            total_price: 0,
            code_name: ""
        },
        categoryPromoDiscounts: [],
        categoryPromoCc: [],
        promoDiscountName: "",
        promoCcName: "",
        paymentBalance: 0,
        totLeftPromoDisc: 0,
        totRightPromoDisc: 0,
        totLeftPromoCc: 0,
        totRightPromoCc: 0,
        subTotalDiscountLeft: 0,
        subTotalDiscountRight: 0,
        totalHppLeft: 0,
        totalHppRight: 0,
        nearestRound: $('#nearest_round').val(),
        voucherMethod: document.getElementById("voucher_method") != null ? document.getElementById("voucher_method").value : 1,
        isRoundUp: $('#is_round_up').val(),
        discountMember: 0,
        orginTotalLeft: 0,
        orginTotalRight: 0,
        taxes: [],
        discountMemberId: 0,
        discountMemberPercentage: 0,
        kembalian: 0,
        voucherQuantity: 0,
        voucherName: "",
        taxServiceMethod: document.getElementById("tax_service_method") != null ? document.getElementById("tax_service_method").value : 1,
        init: function () {
            console.log("App Cashier inited..");
            AppCashier.discountTotal = $("#discount-total").attr("data-price");

            AppCashier.loadingOverlay.show();
            AppCashier.initFunc(AppCashier);
            AppCashier.initSetLeftOrder();
            AppCashier.reservationProcess();
            AppCashier.pettyCashProcess();
            AppCashier.initSetRightOrder();
            AppCashier.updateTaxInfo();
            AppCashier.getOnlineReservation();
            $("#ddl_bank_account_card").chained("#ddl_bank");
            $("#ddl_flazz_bank_account_card").chained("#ddl_flazz_bank");
            $(".promo-cc-right, .promo-cc-left").hide();
            $(document).ready(function () {

                $("#reload_reservation_order").click(function () {
                    $.ajax({
                        url: AppCashier.baseUrl + "reservation/reload_reservation_order",
                        dataType: "JSON",
                        data: {reservation_id: $("#reservation_id").val()},
                        success: function (response) {
                            $("#table-bill-list tbody").html(response.content);
                            AppCashier.reservationSubtotal();
                        }
                    });
                });
            });
            $(document).on("click", ".remove_reservation_menu", function (e) {
                $(this).parents("tr").remove();
                AppCashier.reservationSubtotal();
                e.preventDefault();
            });
            is_delivery = $("#is_delivery").val();
            if (isNaN(is_delivery))is_delivery = 0;
            var is_refund = $("#is_refund").length;
            if (is_delivery == 1 || is_refund == 1) {
                AppCashier.split_all_right();
            }
            try {
                AppCashier.initSocketIO();
            } catch (err) {
                /*AppCashier.loadingOverlay.hide();
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
                 );*/
                AppCashier.alert($('#server-error-message p').text());
                window.location.reload(true);
            }
            $("#total-payment-left").find(".payment-text").parent().remove();
            AppCashier.initListFilter();
        },
        initListFilter: function () {

            var options = {
                valueNames: ['name']
            };

            var userList = new List('menus', options);
        },
        initSocketIO: function () {

            AppCashier.socket = io(AppCashier.nodeUrl, {
                'reconnectionAttempts': 2
            });
            AppCashier.socket.on('reconnect_failed', function () {
                /*AppCashier.loadingOverlay.hide();
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
                 );*/
                AppCashier.alert($('#server-error-message p').text());
                window.location.reload(true);
            });

            AppCashier.socket.on('connected', function (data) {
                console.log('Socket.IO connected');
                AppCashier.socket.emit('cm_auth', {
                        nip: AppCashier.userId,
                        name: AppCashier.userName,
                        role: AppCashier.groupId,
                        role_name: AppCashier.groupName
                    }
                );
                AppCashier.socket.on('sm_auth', function (data) {
                    AppCashier.loadingOverlay.hide();
                    AppCashier.initUIEvent();
                });

                AppCashier.socket.on('sm_notify_cooking_status', function (data) {
                    // console.log(data);
                    var tablenew = $('#status_menu_' + data.order_menu_id);
                    var content = data.status_name;
                    tablenew.html(content);
                    if (data.cooking_status == 6) {
                        $("#bgsound_notification").get(0).play();
                    }
                    if (data.notification != null) {
                        AppCashier.prependNotif(data.notification);
                        AppCashier.updateOpenNotifBar();

                    }

                });

                AppCashier.socket.on('sm_notify_menu_available_status', function (data) {
                    if (AppCashier.isTakeaway == '1') {
                        if (data.available == 1) {
                            AppCashier.alert('Pesan dari kitchen : Menu ' + data.menu_name + ' habis');
                        } else {
                            AppCashier.alert('Pesan dari kitchen : Menu ' + data.menu_name + ' tersedia');
                        }
                    }
                    //window.location.reload(true);
                });

                AppCashier.socket.on('sm_notify_new_order', function (data) {
                    AppCashier.updateStockMenu(data.arr_menu_outlet);

                });


            });
        },
        initUIEvent: function () {
            $('#hidden_menu').on('click',function (e) {
              
              document.getElementById("hidden_menu_dv").className = "col-md-4";
              document.getElementById("hidden_order_dv").className = "panel-wrapper hidden-xs";
              
            });
          $('#hidden_order').on('click',function (e) {
              
              document.getElementById("hidden_menu_dv").className = "col-md-4 hidden-xs";
              document.getElementById("hidden_order_dv").className = "panel-wrapper ";
              
            });


          
            $('#refund_date').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'YYYY-MM-DD',
            });
            $(document).on("click", ".reservation_print", function () {
                reservation_id = $(this).attr("reservation_id");
                $("#popup-reservation-template-note").show();
                $("#popup-reservation-template-note select").select2();
                $("#popup-reservation-template-note").find("#print_reservation_id").val(reservation_id);
            });
            $(".btn-cancel-reservation").on("click", function () {
                $("#popup-reservation-template-note").find("#print_reservation_id").val("");
                $("#popup-reservation-template-note").hide();
            });
            $(".delete_order_all").on('click', function (e) {
                user_confirmation = $(this).attr("feature_confirmation");
                if (user_confirmation == undefined)user_confirmation = "";
                if (user_confirmation != "") {
                    AppNav.showConfirmationPIN(user_confirmation, ".delete_order_all", "click", 1);
                    return false;
                }
                order_id = $(this).attr("order_id");
                $.ajax({
                    url: AppTable.baseUrl + "table/delete_order",
                    type: "POST",
                    dataType: "JSON",
                    data: {order_id: order_id},
                    success: function (response) {
                        if (response.status == true) {
                            window.location.href = AppTable.baseUrl + "table";
                        } else {
                            AppTable.alert('Data ini terkait dengan reservasi,silahkan hapus melalui menu reservasi!');
                        }
                    }
                });
            });
            $(".post_to_ready").on('click', function (e) {
                user_confirmation = $(this).attr("feature_confirmation");
                if (user_confirmation == undefined)user_confirmation = "";
                if (user_confirmation != "") {
                    AppNav.showConfirmationPIN(user_confirmation, ".post_to_ready", "click", 1);
                    return false;
                } else {
                    window.location.href = $(this).attr("href");
                }
            });
            $('#promo_id').on('change', function (e) {


                if ($("#promo_id").val() == 0) {
                    $(".promo-discount").remove();
                    AppCashier.promoDiscountName = "";
                    AppCashier.setPromoDiscount(AppCashier.promoDiscountName, true);
                    AppCashier.categoryPromoDiscounts = [];
                    AppCashier.getSubtotal();
                    AppCashier.setSubTotal();
                    AppCashier.setGrandTotal();
                    AppCashier.setKembalian();
                    AppCashier.updateTaxInfo();
                } else {
                    AppCashier.promoDiscountName = $("#promo_id :selected").html();
                    var url = AppCashier.baseUrl + 'cashier/get_detail_promo_menu';
                    var category_id = $("#category_id").val();
                    var promo_id = $("#promo_id").val();
                    var request = $.ajax({
                        type: 'POST',
                        url: url,

                        data: {
                            'promo_id': promo_id
                        }
                    });
                    request.done(function (msg) {
                        var parsedObject = JSON.parse(msg);
                        console.log("GET DATA DETAIL PROMO MENU");

                        AppCashier.categoryPromoDiscounts = [];
                        for (var i = 0; i < parsedObject.length; i++) {
                            AppCashier.categoryPromoDiscounts.push(parsedObject[i]);
                        }
                        ;

                        AppCashier.setPromoDiscount(AppCashier.promoDiscountName, false);

                        AppCashier.complimentPayment.amount = 0;
                        AppCashier.setSubTotal();
                        AppCashier.setGrandTotal();
                        AppCashier.setKembalian();
                        AppCashier.updateTaxInfo();
                    });
                    request.fail(function (jqXHR, textStatus) {


                    });
                    request.always(function () {
                    });
                }

            });

            $('.btn-number').on('click', function (e) {
                e.preventDefault();

                var fieldName = $(this).attr('data-field');
                var type = $(this).attr('data-type');
                var input = $("input[name='" + fieldName + "']");
                var currentVal = parseInt(input.val());
                if (!isNaN(currentVal)) {
                    if (type == 'minus') {

                        if (currentVal > input.attr('min')) {
                            input.val(currentVal - 1).change();
                        }
                        if (parseInt(input.val()) == input.attr('min')) {
                            // $(this).attr('disabled', true);
                        }

                    } else if (type == 'plus') {

                        if (currentVal < input.attr('max')) {
                            input.val(currentVal + 1).change();
                        }
                        if (parseInt(input.val()) == input.attr('max')) {
                            // $(this).attr('disabled', true);
                        }

                    }
                } else {
                    input.val(1);
                }
            });

            AppCashier.inputNumber.focusin(function () {
                $(this).data('oldValue', $(this).val());
            });

            AppCashier.inputNumber.on('keydown', function (e) {
                // e.preventDefault();
                // Allow: backspace, delete, tab, escape, enter
                // if ($.inArray(e.keyCode, [46, 8, 9, 27, 13]) !== -1 ||
                //         // Allow: Ctrl+A
                //     (e.keyCode == 65 && e.ctrlKey === true) ||
                //         // Allow: home, end, left, right
                //     (e.keyCode >= 35 && e.keyCode <= 39)) {
                //     // let it happen, don't do anything
                //     return;
                // }
                // // Ensure that it is a number and stop the keypress
                // if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                //     e.preventDefault();
                // }
            });

            AppCashier.inputNumber.on('change', function (e) {
                var minValue = parseInt($(this).attr('min'));
                var maxValue = parseInt($(this).attr('max'));
                var valueCurrent = parseInt($(this).val());

                var name = $(this).attr('name');
                if (valueCurrent >= minValue) {
                    $(".btn-number[data-type='minus'][data-field='" + name + "']").removeAttr('disabled')
                } else {
                    if ($(this).data('oldValue') != '') {
                        $(this).val($(this).data('oldValue'));
                    } else {
                        $(this).val(minValue);
                    }

                }
                if (valueCurrent <= maxValue) {
                    $(".btn-number[data-type='plus'][data-field='" + name + "']").removeAttr('disabled')
                } else {
                    if ($(this).data('oldValue') != '') {
                        $(this).val($(this).data('oldValue'));
                    } else {
                        $(this).val(maxValue);
                    }
                }
            });


            $('.btn-cancel').on('click', function (e) {
                // console.log(AppCashier.paymentType);
                e.preventDefault();
                AppCashier.clearALLInput();
                AppCashier.hideAllPopup();

                AppCashier.voucherPayment.amount = 0;
                if (AppCashier.paymentType == 4) {
                    AppCashier.voucherPayment.amount = 0;
                    AppCashier.customerpay = 0;
                    $(".voucherPayment").remove();
                    AppCashier.setKembalian();
                }
                if (AppCashier.paymentType == 5) {
                    AppCashier.resetComplimentPayment();
                }

                // AppCashier.resetInputDiscount();

            });

            $('.btn-cancel-new-order').on('click', function (e) {
                e.preventDefault();
                $('#form-input-order').get(0).reset();
                $('#form-input-order').find('textarea').val('');
                $('#new-order-checkout').hide();
            });

            $('.get_menus').on('click', function (e) {
                AppCashier.loadingOverlay.show();
                var cat = $(this).children();
                var url = $(this).attr('href');
                var category_id = $(this).data("category");
                var menuType = $('#menu-view-type').val();
                var request = $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                        'category_id': category_id,
                        'menu_type': menuType
                    }
                });
                request.done(function (msg) {
                    if (msg != '') {
                        var parsedObject = JSON.parse(msg);
                        $('.container-menus').html(parsedObject.content);

                        var catName = ($('#btn-category-list').hasClass('active')) ? $(cat[0]).text() : $(cat[1]).text();
                        $('.category-name').text('Menu - Category ' + catName);

                        AppCashier.loadingOverlay.hide();
                    } else {
                        window.location.reload(true);
                    }
                });
                request.fail(function (jqXHR, textStatus) {
                    if (textStatus == 'timeout') {
                        AppCashier.alert($('#server-timeout-message p').text());
                    }
                    window.location.reload(true);

                });
                request.always(function () {
                });

                e.preventDefault();
            });

            // $(document).on('mousedown touchstart', '.add-order-menu', function (e) {
            $(document).on('click', '.add-order-menu', function (e) {
                document.activeElement.blur();
                var id = $(this).data("id");
                var name = $(this).data("name");
                var price = $(this).data("price");
                $('.menu-name').text(name);
                $('#menu_id_selected').val(id);
                $('#menu_price_selected').val(price);
                $('#is_already_process').val('0');
                $("#dinein_takeaway").removeAttr("checked");
                $('.btn-save').show();

                AppCashier.totalStockAvailable = parseInt($('.total-available-' + id).html());
                if (AppCashier.totalStockAvailable == 0 && $('#zero_stock_order').val() == '0') {
                    $(this).css('color', 'red');
                    return false;
                }

                AppCashier.isEdit = false;
                AppCashier.countMenuOld = $('.count-order').val();

                AppCashier.getMenuDetail(id, false, false, this);

                e.preventDefault();
            });

            $(document).on('click', '.btn-new-order', function (e) {
                $('#new-order-checkout').show();

                e.preventDefault();
            });

            $('.btn-save').on('click', function (e) {
                e.preventDefault();
                if ($('#is_already_process').val() == '1' && AppCashier.isCheckout != '1') {
                    return false;
                }

                var menuId = 0;
                var count = $('.count-order').val();
                if (AppCashier.isEdit) {
                    menuId = $('#menu_order_id_selected').val();
                    var total_available = parseInt($('#temp_total_ordered').val()) + AppCashier.totalStockAvailable;

                    if (parseInt(total_available) < parseInt(count) && $('#zero_stock_order').val() == '0') {
                        AppCashier.alert('Jumlah pesanan melebihi stok.');
                        return false;
                    }
                    count = count - $('#temp_total_ordered').val();
                }
                else {
                    menuId = $('#menu_id_selected').val();
                    if (AppCashier.totalStockAvailable < parseInt(count) && $('#zero_stock_order').val() == '0') {
                        AppCashier.alert('Jumlah pesanan melebihi stok.');
                        return false;
                    }
                }

                var orderId = $('#order_id').val();
                var option = '';
                var sideDish = '';
                var notes = $('.order-notes').val();
                // var discountPrice = $('#discount-total').attr('data-price');
                // var discountName = $('#discount-total').attr('data-name');

                //get side dish
                $('.chk_dish:checked').each(function () {
                    sideDish += $(this).val() + ",";
                });
                sideDish = sideDish.slice(0, -1);

                $('.options :selected').each(function (i, selected) {
                    // console.log($(selected));
                    option += $(selected).val() + ",";
                });
                option = option.slice(0, -1);

                var request = $.ajax({
                    type: "POST",
                    url: AppCashier.baseUrl + 'cashier/save_order_menu',
                    data: {
                        menu_id: menuId,
                        order_id: orderId,
                        count: count,
                        option: option,
                        side_dish: sideDish,
                        notes: notes,
                        is_edit: AppCashier.isEdit,
                        isCheckout: AppCashier.isCheckout,
                        // discount_price : discountPrice,
                        // discount_name : discountName,
                        outlet_id: $('.total-available-' + $('#menu_id_selected').val()).data("outlet")

                    }
                });
                request.done(function (msg) {
                    if (msg != '') {
                        var parsedObject = JSON.parse(msg);
                        if (parsedObject.status === true) {
                            $('.bill-table tbody').html(parsedObject.order_list);
                            $('.total-payment tbody').html(parsedObject.order_bill);

                            AppCashier.socket.emit('cm_notify_new_order', {
                                arr_menu_outlet: parsedObject.arr_menu_outlet,
                                room: 'waiter'
                            });
                            AppCashier.updateStockMenu(parsedObject.arr_menu_outlet);

                        } else {
                            if (parsedObject.msg != '') {
                                AppCashier.alert(parsedObject.msg);

                            }
                        }

                        $('.popup-block').hide();
                        AppCashier.isEdit = false;
                        AppCashier.countOld = '';
                        $('#form-input-order').get(0).reset();
                        $('#form-input-order').find('textarea').val('');
                    } else {
                        window.location.reload(true);
                    }
                });
                request.fail(function (jqXHR, textStatus) {
                    if (textStatus == 'timeout') {
                        AppCashier.alert($('#server-timeout-message p').text());
                    }
                    window.location.reload(true);

                });
                request.always(function () {
                });

            });

            $('.btn-delete-order').on('click', function (e) {
                e.preventDefault();
                var menuId = $('#menu_order_id_selected').val();
                var orderId = $('#order_id').val();
                var request = $.ajax({
                    type: "POST",
                    url: AppCashier.baseUrl + 'cashier/delete_order_menu',
                    data: {
                        menu_id: menuId,
                        order_id: orderId,
                        count: $('.count-order').val()

                    }
                });
                request.done(function (msg) {
                    if (msg != '') {
                        var parsedObject = JSON.parse(msg);
                        if (parsedObject.status === true) {
                            $('.bill-table tbody').html(parsedObject.order_list);
                            $('.total-payment tbody').html(parsedObject.order_bill);
                        }
                        $('.popup-block').hide();
                        $('#form-input-order').get(0).reset();
                        $('#form-input-order').find('textarea').val('');
                        AppCashier.socket.emit('cm_notify_new_order', {
                            arr_menu_outlet: parsedObject.arr_menu_outlet,
                            room: 'kitchen'
                        });

                        AppCashier.updateStockMenu(parsedObject.arr_menu_outlet);

                    } else {
                        window.location.reload(true);
                    }
                });
                request.fail(function (jqXHR, textStatus) {
                    if (textStatus == 'timeout') {
                        AppCashier.alert($('#server-timeout-message p').text());
                    }
                    window.location.reload(true);

                });
                request.always(function () {
                });
            });

            var timeout_id = 0, idTable = "";

            $(".bill-table").on('click', 'tbody  > tr', function () {

                idTable = $(this).parents("table:first").attr("id");

                if (idTable === "bill-table-left") {
                    $(this).parents("table:first").find("tbody  > tr.tOrder").removeClass("highlight-bill-left");

                    $(this).addClass("highlight-bill-left");
                    $(this).find('td').addClass("highlight");
                } else {
                    $(this).parents("table:first").find("tbody  > tr.tOrder").removeClass("highlight-bill-right");

                    $(this).addClass("highlight-bill-right");
                    $(this).find('td').addClass("highlight");
                }


                //   timeout_id = setTimeout(AppCashier.editOrderMenuDetail($(this)), 1000);


            })
                .bind('mouseup', function () {
                    $(this).find('td').removeClass("highlight");
                    clearTimeout(timeout_id);
                });
            $(document).on('mouseup touchend', '#table-bill-list-checkout', function (e) {
                $(this).find('td').removeClass("highlight");
                $(this).find('tr').removeClass("highlight-bill-left");
                $(this).find('tr').removeClass("highlight-bill-right");
            });
            $(document).on('mouseup touchend', '.add-order-menu', function (e) {
                // alert();
                $(this).css('color', '#333');

            })

            $('#split-single-right').on('click', function (e) {
                var data = AppCashier.extractDataOrder($('.highlight-bill-left').get(0));

                if($("#reservation_id").val() != 0){
                    AppCashier.alert("Split bill tidak dapat dilakukan karena terkait reservasi");
                    return;
                }

                if (typeof data.product_id === "undefined") {
                    AppCashier.alert("Silahkan pilih menu pesanan terlebih dahulu");
                    return;
                }

                user_confirmation = $(this).attr("feature_confirmation");
                if (user_confirmation == undefined)user_confirmation = "";
                if (user_confirmation != "") {
                    AppNav.showConfirmationPIN(user_confirmation, '#split-single-right', "click", 1);
                    return;
                }
                AppCashier.sendToRight(data, AppCashier.enumOrder.SINGLE, $('.highlight-bill-left').get(0));


                // AppCashier.resetHighlight();

                AppCashier.getSubtotal();
                AppCashier.setSubTotal();
                AppCashier.updateTaxInfo();
                AppCashier.setGrandTotal();
                $('#done-payment').prop('disabled', true);
                if (AppCashier.cashPayment.type == 9) {
                    AppCashier.bonPayment();
                }
            });

            $('#split-all-right').on('click', function (e) {
                AppCashier.split_all_right();
            });
            $('#split-single-left').on('click', function (e) {
                var data = AppCashier.extractDataOrder($('.highlight-bill-right').get(0));
                if (typeof data.product_id === "undefined") {
                    AppCashier.alert("Silahkan pilih menu pesanan terlebih dahulu");
                    return;
                }
                user_confirmation = $(this).attr("feature_confirmation");
                if (user_confirmation == undefined)user_confirmation = "";
                if (user_confirmation != "") {
                    AppNav.showConfirmationPIN(user_confirmation, '#split-single-left', "click", 1);
                    return;
                }
                AppCashier.sendToLeft(data, AppCashier.enumOrder.SINGLE, $('.highlight-bill-right').get(0));

                //AppCashier.resetHighlight();
                AppCashier.getSubtotal();
                AppCashier.setSubTotal();
                AppCashier.updateTaxInfo();
                AppCashier.setGrandTotal();
                $('#done-payment').prop('disabled', true);
                if (AppCashier.cashPayment.type == 9) {
                    AppCashier.bonPayment();
                }

            });
            $('#split-all-left').on('click', function (e) {
                var parent = $("#bill-table-right > tbody");
                for (var i = 0; i < $(parent).children().length; i++) {
                    var child = $(parent).children()[i];
                    var data = AppCashier.extractDataOrder(child);

                    AppCashier.sendToLeft(data, AppCashier.enumOrder.ALL, child);
                }

                AppCashier.removeAllRightElement();
                AppCashier.resetHighlight();
                AppCashier.getSubtotal();
                AppCashier.setSubTotal();
                AppCashier.updateTaxInfo();
                AppCashier.setGrandTotal();
                $('#input-payment').val('');
                $('#input-payment').trigger('change');
                AppCashier.resetComplimentPayment();

                AppCashier.promoDiscountName = "";
                AppCashier.setPromoDiscount(AppCashier.promoDiscountName, true);
                AppCashier.setPromoCc(AppCashier.promoCcName, true);
                AppCashier.categoryPromoDiscounts = [];
                AppCashier.categoryPromoCc = [];
                $('#done-payment').prop('disabled', true);
                if (AppCashier.cashPayment.type == 9) {
                    AppCashier.bonPayment();
                }
            });

            $('#btn-reset-takeaway').on('click', function (e) {
                var object = $(this);

                function resetTakeAway() {
                    AppCashier.loadingOverlay.show();
                    var url = object.attr('href');
                    var order_id = $('#order_id').val();
                    var request = $.ajax({
                        type: 'POST',
                        url: url,
                        data: {
                            'order_id': order_id
                        }
                    });
                    request.done(function (msg) {
                        window.location.reload(true);

                    });
                    request.fail(function (jqXHR, textStatus) {
                        if (textStatus == 'timeout') {
                            AppCashier.alert($('#server-timeout-message p').text());
                        }
                        window.location.reload(true);

                    });
                    request.always(function () {
                    });
                }

                AppCashier.confirm('Apakah anda yakin akan reset pesanan ini?', resetTakeAway);
                e.preventDefault();
            });

            $('#btn-process-takeaway').on('click', function (e) {
                var customer_name = $('#customer_name').val();
                var customer_phone = $('#customer_phone').val();
                var count_ord = $('.bill-table > tbody  > tr').length;
                if (customer_name == '') {
                    AppCashier.alert('Silahkan isi nama pelanggan terlebih dahulu');
                    return false;
                } else if (count_ord == 0) {
                    AppCashier.alert('No Orders!');
                    return false;
                }

                AppCashier.loadingOverlay.show();
                var url = $(this).attr('href');
                var order_id = $('#order_id').val();

                var request = $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                        'order_id': order_id,
                        'customer_name': customer_name,
                        'customer_phone': customer_phone,
                    }
                });
                request.done(function (msg) {
                    var parsedObject = JSON.parse(msg);
                    AppCashier.socket.emit('cm_notify_new_order', {
                        number_guest: parsedObject.number_guest,
                        order_id: order_id,
                        room: 'kasir'
                    });

                    setTimeout(function () {
                        window.location = parsedObject.url_redir;
                    }, 100);

                });
                request.fail(function (jqXHR, textStatus) {
                    if (textStatus == 'timeout') {
                        AppCashier.alert($('#server-timeout-message p').text());
                    }
                    window.location.reload(true);

                });
                request.always(function () {
                });
                e.preventDefault();
            });

            // disable button when mode view detail takeaway
            if (AppCashier.orderIsView == '1') {
                $('#btn-reset-takeaway').hide();
            } else {
                $('#btn-reset-takeaway').show();
            }

            AppCashier.resizePanel();
            $(document).ajaxStop(function () {
                AppCashier.resizePanel();
            });

            AppCashier.paymentProcess();

            $(window).on({
                orientationchange: function (e) {
                    AppCashier.resizePanel();
                }, resize: function (e) {
                    AppCashier.resizePanel();
                }
            });

            $('#btn-notif').on('click', function (e) {
                var options = {direction: 'right'};
                $('.notification-container').toggle('slide', options, 500);
                $('.notification-container').addClass('open-notif');

                AppCashier.updateOpenNotifBar();

            });

            $('.button-hide').on('click', function (e) {
                var options = {direction: 'right'};
                $('.notification-container').toggle('slide', options, 500);

                var list = $('.unseen-notif');
                var arrID = [];

                for (var i = list.length - 1; i >= 0; i--) {
                    var id = list.eq(i).attr('data-id');
                    AppCashier.updateNotifCounter('-');
                    $('#notif-' + id + ' ').removeClass('unseen-notif');
                }
                ;

            });

            $('.notification-container').removeClass('open-notif');
            AppCashier.deleteNotif();

            $("#save_reservasi").on('click', function (e) {
              /* stop form from submitting normally */
              e.preventDefault();
              App.addReservation(function(data){
                 AppCashier.socket.emit('cm_new_reservation', {
                        reservation_id: data.reservation_id,
                         detail_order: data.detail_order,
                        table_id:data.table_id,
                        table_number:data.table_number,
                        reservation_date:data.reservation_date,
                        customer_name:data.customer_name,
                        order_id:data.order_id,
                        operator_name:data.operator_name
                    }
                  );
                 window.location = AppCashier.baseUrl + 'reservation/add';
                
              });
              
            });
            $("#save_exit_reservasi").on('click', function (e) {
              /* stop form from submitting normally */
               e.preventDefault();
               App.addReservation(function(data){
                  AppCashier.socket.emit('cm_new_reservation', {
                        reservation_id: data.reservation_id,
                        detail_order: data.detail_order,
                        table_id:data.table_id,
                        table_number:data.table_number,
                        reservation_date:data.reservation_date,
                        customer_name:data.customer_name,
                        order_id:data.order_id,
                        operator_name:data.operator_name
                    }
                  );
                  window.location = AppCashier.baseUrl + 'reservation';     
               });
              
            });
            $("#edit_reservasi").on('click', function (e) {
              /* stop form from submitting normally */
              e.preventDefault();
              App.editReservation(function(data){
                AppCashier.socket.emit('cm_edit_reservation', {
                        reservation_id: data.reservation_id,
                        detail_order: data.detail_order,
                        table_id:data.table_id,
                        table_number:data.table_number,
                        reservation_date:data.reservation_date,
                        customer_name:data.customer_name,
                        order_id:data.order_id,
                        operator_name:data.operator_name
                    }
                  );
                 window.location = AppCashier.baseUrl + 'reservation/edit/'+$("#reservation_id").val();         
              });
              
            });
            $("#edit_exit_reservasi").on('click', function (e) {
              /* stop form from submitting normally */
               e.preventDefault();
               App.editReservation(function(data){
                 AppCashier.socket.emit('cm_edit_reservation', {
                        reservation_id: data.reservation_id,
                        detail_order: data.detail_order,
                        table_id:data.table_id,
                        table_number:data.table_number,
                        reservation_date:data.reservation_date,
                        customer_name:data.customer_name,
                        order_id:data.order_id,
                        operator_name:data.operator_name
                    }
                  );
                  window.location = AppCashier.baseUrl + 'reservation';
               });
              
            });

        },
        addReservation:function(callback){
            var request = $.ajax({
                        type: 'POST',
                        url: AppCashier.baseUrl + 'reservation/add_reservation',
                        data: $("#from_reservasi").serialize()
                    });
            request.success(function (result) {
                try{
                    var result = JSON.parse(result);
                    if(result.success){
                        App.alertSuccessMessageReservation(result.message);
                        callback(result);
                    }else{
                        App.alertErrorMessageReservation(result.message);    
                    }
                }catch(err){
                     App.alertErrorMessageReservation("Internal Server Error");    
                }
                

            });
        },
        editReservation:function(callback){
            var request = $.ajax({
                        type: 'POST',
                        url: AppCashier.baseUrl + 'reservation/edit_reservation',
                        data: $("#from_reservasi").serialize()
                    });
            request.success(function (result) {
                console.log(result);
                try{
                    var result = JSON.parse(result);
                    if(result.success){
                        App.alertSuccessMessageReservation(result.message);
                        callback(result);
                    }else{
                        App.alertErrorMessageReservation(result.message);    
                    }
                }catch(err){
                     App.alertErrorMessageReservation("Internal Server Error");    
               }
                

            });
        },
        alertErrorMessageReservation:function(message){
            var html = '<div class="alert alert-danger" role="alert">';
            html+=message;
            html+='</div';
            $(".result").html(html);
            $(window).scrollTop(0);
        },
        alertSuccessMessageReservation:function(message){
            var html = '<div class="alert alert-success" role="alert">';
            html+=message;
            html+='</div';
            $(".result").html(html);  
            $(window).scrollTop(0);
        },
        split_all_right: function () {
            var parent = $("#bill-table-left > tbody");
            for (var i = 0; i < $(parent).children().length; i++) {
                var child = $(parent).children()[i];
                var data = AppCashier.extractDataOrder(child);

                AppCashier.sendToRight(data, AppCashier.enumOrder.ALL, child);
            }
            $('#done-payment').prop('disabled', true);

            AppCashier.removeAllLeftElement();

            AppCashier.resetHighlight();

            AppCashier.getSubtotal();
            AppCashier.setSubTotal();
            AppCashier.updateTaxInfo();
            AppCashier.setGrandTotal();
            if (AppCashier.cashPayment.type == 9) {
                AppCashier.bonPayment();
            }
        },
        updateOpenNotifBar: function () {
            if ($('.notification-container').hasClass('open-notif')) {
                var list = $('.unseen-notif');
                for (var i = list.length - 1; i >= 0; i--) {
                    var id = list.eq(i).attr('data-id');
                    var request = $.ajax({
                        type: 'POST',
                        url: AppCashier.baseUrl + 'notification/update_notif',
                        data: {
                            'notif_id': id,
                        }
                    });
                    request.done(function () {

                    });
                }
                ;
            }

        },
        updateNotifCounter: function (type) {
            var count = parseInt($('.counter-notification').html());
            if (type == '+') {
                if (isNaN(count)) {
                    $('#btn-notif').append('<div class="counter-notification">0</div>');
                    count = 0;
                }
                $('.counter-notification').html(count + 1).change();
            } else {
                $('.counter-notification').html(count - 1).change();
                if ($('.counter-notification').html() == 0) {
                    $('.counter-notification').hide();
                }
            }
        },
        prependNotif: function (data) {
            for (var i = data.length - 1; i >= 0; i--) {
                if (data[i].to_user == AppCashier.userId) {
                    var msg = '<div class="list-notification unseen-notif" id="notif-' + data[i].notif_id + '" data-id="' + data[i].notif_id + '" >' +
                        '<p class="content-notif" >' + data[i].msg + '</p>' +
                        '<a class="button-ok-notif" href="#" data-id="' + data[i].notif_id + '"></a></div>';
                    $('#notification-container').prepend(msg);

                    AppCashier.updateNotifCounter('+');
                    $('.counter-notification').show();
                    AppCashier.deleteNotif();
                }

            }

        },
        deleteNotif: function () {
            $('.button-ok-notif').on('click', function (e) {
                AppCashier.loadingOverlay.show();
                var id = $(this).attr('data-id');

                var request = $.ajax({
                    type: 'POST',
                    url: AppCashier.baseUrl + 'notification/delete_notif',
                    data: {
                        'notif_id': id,
                    }
                });
                request.done(function () {

                    $('#notif-' + id + '').remove();
                    AppCashier.updateNotifCounter('-');
                    AppCashier.loadingOverlay.hide();
                });
            });
        },

        resizePanel: function () {
            var bilPanelHeight = $('.bill-theme-con').height();
            var subtotalheight = $('.total-payment').height();
            var billMover = $('.bill-mover').height() + 73;
            var paymentAmount = $('#paymentAmount').height();
            //$('#table-bill-list').height(bilPanelHeight - subtotalheight - 10);
            // $('#table-bill-list-checkout').height(bilPanelHeight - subtotalheight - paymentAmount - billMover);
        },
        editOrderMenuDetail: function (row) {
            var arrDiv = row.children();

            $('.menu-name').text($(arrDiv[0]).text());
            $('#menu_id_selected').val($(arrDiv[3]).text());
            $('#menu_order_id_selected').val($(arrDiv[7]).text());
            $('#is_already_process').val($(arrDiv[9]).text());
            $('#temp_total_ordered').val($(arrDiv[10]).text());
            $('#price-menu').val($(arrDiv[2]).attr('data-price'));
            AppCashier.totalStockAvailable = parseInt($('.total-available-' + $(arrDiv[4]).text()).html());

            AppCashier.getMenuDetail($(arrDiv[3]).text(), true, arrDiv);
            AppCashier.isEdit = true;
        },
        getMenuDetail: function (menuId, isEdit, dataOrder, element) {
            var menuOrderID = 0;
            if (isEdit) {
                menuOrderID = $(dataOrder[7]).text();
            }
            if (isEdit == false && element != undefined && parseInt($(element).data("option-count")) == 0 && parseInt($(element).data("side-dish-count")) == 0) {
                parsedObject = {
                    "options": "<h5 style='color:#898989'>Tidak ada opsi untuk menu ini</h5>",
                    "order_menu_data": [],
                    "side_dish": "<h5 style='color:#898989'>Tidak ada side dish untuk menu ini</h5>"
                };
                $('.side-dish').html(parsedObject.side_dish);
                $('.menu-option').html(parsedObject.options);
                $('.side-dish').parents(".dark-theme-con").css("height", "198px");
                $('.side-dish').parents("#form-input-order").find('.order-note').css("height", "225px");
                $('.side-dish').parents("#form-input-order").find('.popup-order-panel').css("height", "310px");
                if ($('#dinein_takeaway').length == 0) {
                    $('.order-notes').css("height", "162px");
                } else {
                    $('.order-notes').css("height", "126px");
                }
                $('.side-dish').parents("table:first").find("tr:eq(3)").hide();
                $('.side-dish').parents("table:first").find("tr:eq(4)").hide();
                $('#menu_cooking_status').val('1');
                $('.btn-delete-order').hide();
                $('#popup-new-order').show();
            } else {

                var request = $.ajax({
                    type: "POST",
                    url: AppCashier.baseUrl + 'table/get_menu_accessories',
                    data: {
                        menu_id: menuId,
                        menu_order_id: menuOrderID
                    }
                });

                request.done(function (msg) {
                    if (msg != '') {
                        var parsedObject = JSON.parse(msg);

                        $('.side-dish').html(parsedObject.side_dish);
                        $('.menu-option').html(parsedObject.options);
                        if ($('.side-dish tr').length == 0) {
                            $('.side-dish').parents(".dark-theme-con").css("height", "198px");
                            $('.side-dish').parents("#form-input-order").find('.order-note').css("height", "225px");
                            $('.side-dish').parents("#form-input-order").find('.popup-order-panel').css("height", "310px");
                            if ($('#dinein_takeaway').length == 0) {
                                $('.order-notes').css("height", "162px");
                            } else {
                                $('.order-notes').css("height", "126px");
                            }
                            $('.side-dish').parents("table:first").find("tr:eq(3)").hide();
                            $('.side-dish').parents("table:first").find("tr:eq(4)").hide();
                        } else {
                            $('.side-dish').parents(".dark-theme-con").css("height", "310px");
                            $('.side-dish').parents("#form-input-order").find('.order-note').css("height", "auto");
                            $('.side-dish').parents("#form-input-order").find('.popup-order-panel').css("height", "390px");
                            if ($('#dinein_takeaway').length == 0) {
                                $('.order-notes').css("height", "274px");
                            } else {
                                $('.order-notes').css("height", "238px");
                            }
                            $('.side-dish').parents("table:first").find("tr:eq(3)").show();
                            $('.side-dish').parents("table:first").find("tr:eq(4)").show();

                        }
                        if (isEdit) {
                            $('.count-order').val($(dataOrder[1]).text());
                            $('.order-notes').val($(dataOrder[4]).text());
                            $('#menu_cooking_status').val($(dataOrder[8]).text());

                            // disable button when mode view detail takeaway
                            if ($(dataOrder[9]).text() == '1' && AppCashier.isCheckout != '1') {
                                $('.btn-delete-order').hide();
                                $('.btn-save').hide();
                            } else {
                                $('.btn-delete-order').show();
                                $('.btn-save').show();
                            }
                        }
                        else {
                            $('#menu_cooking_status').val('1');
                            $('.btn-delete-order').hide();
                        }


                        $('#popup-new-order').show();
                        // $('.popup-block').show();
                    } else {
                        window.location.reload(true);
                    }
                });
                request.fail(function (jqXHR, textStatus) {
                    if (textStatus == 'timeout') {
                        AppCashier.alert($('#server-timeout-message p').text());
                    }
                    window.location.reload(true);

                });
                request.always(function () {
                });
            }
        },
        paymentProcess: function () {

            var inputPayment = $('#input-payment');
            var is_mobile = /mobile|android/i.test(navigator.userAgent);

            if (is_mobile) {
                $(document).on('focus', '#input-payment', function (e) {
                    inputPayment.blur();
                });
            }

            var isTouchDevice = (('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch);
            var clicked = 'click';
            if (isTouchDevice) {
                // clicked = 'touchend';
            }

            $(document).on(clicked, '#cash-payment', function (e) {
                //AppCashier.activateNumberButton();
                AppCashier.disablePaymentButton(false);

                /* 
                *   Change payment method: 
                *    - compare customer pay value and grand total
                *    - if the same, clear customer pay
                *    - if different, customer will pay with dual method
                */

                if (AppCashier.customerpay == AppCashier.grandTotalRight) {
                    $('.clearNumber:not(#btn-feature-confirmation-clear)').trigger('click');
                }

                if (AppCashier.rightDataOrder.length > 0)
                    AppCashier.activateNumberButton(true);

                inputPayment.trigger('change');

                inputPayment.off();
                inputPayment.payment('restrictNumeric');

                inputPayment.on('change keyup', function (e) {
                    var e = window.event || e;
                    var keyUnicode = e.charCode || e.keyCode;
                    if (e !== undefined) {
                        switch (keyUnicode) {
                            case 16:
                                break; // Shift
                            case 17:
                                break; // Ctrl
                            case 18:
                                break; // Alt
                            case 27:
                                this.value = '';
                                break; // Esc: clear entry
                            case 35:
                                break; // End
                            case 36:
                                break; // Home
                            case 37:
                                break; // cursor left
                            case 38:
                                break; // cursor up
                            case 39:
                                break; // cursor right
                            case 40:
                                break; // cursor down
                            case 78:
                                break; // N (Opera 9.63+ maps the "." from the number key section to the "N" key too!) (See: http://unixpapa.com/js/key.html search for ". Del")
                            case 110:
                                break; // . number block (Opera 9.63+ maps the "." from the number block to the "N" key (78) !!!)
                            case 190:
                                break; // .
                            default:
                                $(this).formatCurrency({
                                    symbol: 'Rp ',
                                    colorize: true,
                                    negativeFormat: '-%s%n',
                                    roundToDecimalPlace: -1,
                                    groupDigits: true,
                                    eventOnDecimalsEntered: true
                                });
                        }
                    }
                });


                AppCashier.paymentType = 1;

                AppCashier.resetInputPayment();

                AppCashier.deactivatePaymentMethod();
                $(this).addClass('active');
                $("#input-payment").attr("placeholder", "Masukan nominal");

            });

            // $(document).on(clicked, '#debit-payment', function (e) {
            //     AppCashier.disablePaymentButton(true);
            //     inputPayment.val('');
            //     inputPayment.off();
            //     inputPayment.payment('formatCardNumber');
            //     inputPayment.focus();
            //     AppCashier.paymentType = 2;

            //     AppCashier.deactivatePaymentMethod();
            //     $(this).addClass('active');
            //     $("#input-payment").attr("placeholder", "Masukan nomor kartu");
            // });

            // $(document).on(clicked, '#credit-payment', function (e) {
            //     AppCashier.disablePaymentButton(true);
            //     inputPayment.val('');
            //     inputPayment.off();
            //     inputPayment.payment('formatCardNumber');
            //     inputPayment.focus();
            //     AppCashier.paymentType = 3;

            //     AppCashier.deactivatePaymentMethod();
            //     $(this).addClass('active');
            //     $("#input-payment").attr("placeholder", "Masukan nomor kartu");

            // });

            $(document).on(clicked, '#pending-bill-print', function (e) {
                AppCashier.loadingOverlay.show();
                var orderId = $('#order_id').val();

                var request = $.ajax({
                    type: 'POST',
                    url: AppCashier.baseUrl + 'cashier/print_pending_bill',
                    data: {
                        'order_id': orderId
                    }
                });
                request.done(function (msg) {
                    AppCashier.loadingOverlay.hide();
                });
                request.fail(function (jqXHR, textStatus) {
                    if (textStatus == 'timeout') {
                        AppCashier.alert($('#server-timeout-message p').text());
                    }
                    window.location.reload(true);

                });
                request.always(function () {
                });

                AppCashier.deactivatePaymentMethod();
                $(this).addClass('active');
                $("#input-payment").attr("placeholder", "");

            });

            $(document).on(clicked, '#print-checkout-bill', function (e) {
                // AppCashier.loadingOverlay.show();
                var paymentValue = 0;
                var orderId = $('#order_id').val();
                var totalBill = $('#totalBill').val();
                var customerpay = 0;
                // console.log(AppCashier.rightDataOrder)
                //return false;
                if (paymentValue > 0) {
                    var request = $.ajax({
                        type: 'POST',
                        url: AppCashier.baseUrl + 'cashier/print_bill',
                        data: {
                            'order_id': orderId,
                            'payment_type': AppCashier.paymentType,
                            'payment_value': paymentValue,
                            'payment_desc': customerpay
                        }
                    });
                    request.done(function (msg) {
                        AppCashier.loadingOverlay.hide();
                    });
                    request.fail(function (jqXHR, textStatus) {
                        if (textStatus == 'timeout') {
                            AppCashier.alert($('#server-timeout-message p').text());
                        }
                        window.location.reload(true);

                    });
                    request.always(function () {
                    });
                }

                AppCashier.deactivatePaymentMethod();
                $(this).addClass('active');
                $("#input-payment").attr("placeholder", "");

            });


            $(document).on(clicked, '#print-bill', function (e) {
                AppCashier.loadingOverlay.show();
                var paymentValue = 0;
                var orderId = $('#order_id').val();
                var totalBill = $('#totalBill').val();
                var customerpay = 0;

                if (AppCashier.paymentType == '1') {
                    customerpay = AppCashier.customerpay;
                    if (isNaN(customerpay)) {
                        customerpay = 0;
                    }
                    if (customerpay < totalBill) {
                        AppCashier.alert(AppCashier.lessPayments);

                    } else {
                        paymentValue = customerpay;
                    }
                } else {
                    customerpay = inputPayment.val();
                    paymentValue = totalBill;
                }

                if (paymentValue > 0) {
                    var request = $.ajax({
                        type: 'POST',
                        url: AppCashier.baseUrl + 'cashier/print_bill',
                        data: {
                            'order_id': orderId,
                            'payment_type': AppCashier.paymentType,
                            'payment_value': paymentValue,
                            'payment_desc': customerpay
                        }
                    });
                    request.done(function (msg) {
                        AppCashier.loadingOverlay.hide();
                    });
                    request.fail(function (jqXHR, textStatus) {
                        if (textStatus == 'timeout') {
                            AppCashier.alert($('#server-timeout-message p').text());
                        }
                        window.location.reload(true);

                    });
                    request.always(function () {
                    });
                }

                AppCashier.deactivatePaymentMethod();
                $(this).addClass('active');
                $("#input-payment").attr("placeholder", "");

            });

            $(document).on(clicked, '#reset-payment', function (e) {
                AppCashier.loadingOverlay.show();
                inputPayment.val('');
                inputPayment.trigger('change');

                $('#done-payment').prop('disabled', true);
                $('#paymentAmount').html('');
                $('#cash-payment').trigger('click');

                AppCashier.loadingOverlay.hide();

                AppCashier.deactivatePaymentMethod();
                $('#cash-payment').addClass('active');
                $("#input-payment").attr("placeholder", "Masukan nominal");

            });

            $(document).on(clicked, '.payment-ok', function (e) {
                if (AppCashier.paymentType === 5) AppCashier.paymentType = 1;
                AppCashier.newPaymentProcess(inputPayment);
                return;
                var paymentValue = 0;
                var orderId = $('#order_id').val();
                var totalBill = $('#totalBill').val();
                var customerpay = 0;

                if (totalBill == undefined) {
                    totalBill = 0;
                }

                var inputPaymentTemp = inputPayment.val().slice(3).replace(/\,/g, '');
                AppCashier.customerpay = parseFloat(inputPaymentTemp);

                if (AppCashier.paymentType == '1') {
                    // customerpay = parseFloat(inputPayment.val());
                    customerpay = AppCashier.customerpay;
                    if (isNaN(customerpay)) {
                        customerpay = 0;
                    }
                    if (customerpay < totalBill) {
                        AppCashier.alert(AppCashier.lessPayments);

                    } else {
                        paymentValue = customerpay;
                    }

                } else {

                    if (inputPayment.val().length < 15) {
                        AppCashier.alert(AppCashier.errorCardNumber);

                    } else {
                        paymentValue = parseFloat(totalBill);
                    }

                }

                if (paymentValue > 0) {
                    $('#done-payment').prop('disabled', false);
                    $('#print-bill').prop('disabled', false);
                    var returnMoney = (paymentValue - totalBill);
                    var method = 'Cash';
                    switch (AppCashier.paymentType) {
                        case 1:
                            method = 'Cash';
                            break;
                        case 2:
                            method = 'Debit';
                            break;
                        case 3:
                            method = 'Credit';
                            break;
                        case 11:
                            method = 'Flazz';
                            break;
                    }
                    var htmlcontent = '' +
                        '<tbody>' +
                        '<tr>' +
                        '<td style="width:40%"></td>' +
                        '<td style="width:30%"><b>Pembayaran</b></td>' +
                        '<td style="width:30%" id="subtotal-price" class="tb-align-right">' +
                        '<b>Rp ' + AppCashier.formatRupiah(paymentValue) + '</b></td>' +
                        '</tr>' +
                        '<tr>' +
                        '<td style="width:40%"></td>' +
                        '<td style="width:30%"><b>Kembalian</b></td>' +
                        '<td style="width:30%" id="subtotal-price" class="tb-align-right">Rp ' + AppCashier.formatRupiah(returnMoney) + '</td>' +
                        '</tr>' +
                        '<tr>' +
                        '<td style="width:40%"></td>' +
                        '<td style="width:30%"><b>Metode</b></td>' +
                        '<td style="width:30%" id="subtotal-price" class="tb-align-right">' + method + '</td>' +
                        '</tr>' +
                        '</tbody>';

                    $('#paymentAmount').html(htmlcontent);
                }
            });
            $('#done-payment, .btn-preview-bill').on(clicked, function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                AppCashier.loadingOverlay.show();
                var type = $(this).attr('data-type');
                var url = "";
                var paymentValue = 0;
                var orderId = $('#order_id').val();
                var totalBill = AppCashier.grandTotalRight;

                payment_value = AppCashier.paymentBalance;
                var payment_option = [];
                if (type == 'done') {
                    url = AppCashier.baseUrl + 'cashier/payment_bill';
                } else {
                    if (AppCashier.subtotalRight < 0) {
                        AppCashier.alert("Silahkan pilih data pesanan terlebih dahulu!");
                        return;
                    }
                    AppCashier.loadingOverlay.show();

                    url = AppCashier.baseUrl + 'cashier/print_preview_bill';
                }
                var check_kembalian=false;
                if (AppCashier.cashPayment.amount > 0 && AppCashier.kembalian > 0 && AppCashier.cashPayment.amount > AppCashier.kembalian) {
                    AppCashier.cashPayment.amount -= AppCashier.kembalian;
                    check_kembalian=true;
                }
                down_payment = parseFloat($("#reservation_id").attr("down_payment"));
                if (isNaN(down_payment))down_payment = 0;
                if (down_payment > 0) {
                    payment_option.push(AppCashier.downPaymentBill);
                }
                if (AppCashier.debitPayment.amount > 0) payment_option.push(AppCashier.debitPayment);
                if (AppCashier.cashPayment.amount > 0) payment_option.push(AppCashier.cashPayment);
                if (AppCashier.creditPayment.amount > 0) payment_option.push(AppCashier.creditPayment);
                if (AppCashier.flazzPayment.amount > 0) payment_option.push(AppCashier.flazzPayment);
                if (AppCashier.voucherPayment.amount > 0) payment_option.push(AppCashier.voucherPayment);
                if (AppCashier.complimentPayment.amount > 0) payment_option.push(AppCashier.complimentPayment);
                if (AppCashier.pendingbill.amount != 0) payment_option.push(AppCashier.pendingbill);
                if (AppCashier.pendingbillEmployee.amount > 0) payment_option.push(AppCashier.pendingbillEmployee);
                if (payment_option.length == 0) {
                    payment_option.push({
                        code: 0,
                        amount: 0,
                        type: 1
                    });
                }
                
                if(check_kembalian==true && type!='done'){
                    AppCashier.cashPayment.amount += AppCashier.kembalian;
                }
                delivery_cost_id = $("#delivery_cost_id").val();
                delivery_cost = $("input#delivery_cost").val();
                if (isNaN(delivery_cost_id) || delivery_cost_id == undefined)delivery_cost_id = "";
                if (isNaN(delivery_cost) || delivery_cost == undefined)delivery_cost = 0;
                receipt_number = ($("#is_refund").length > 0 ? $("#is_refund").val() : "");

                if (paymentValue == 0) {
                    var request = $.ajax({
                        type: 'POST',
                        url: url,
                        data: {
                            'order_id': orderId,
                            'payment_type': AppCashier.paymentType,
                            'customer_payment': AppCashier.customerpay,
                            'payment_total': AppCashier.subtotalRight,
                            "promo_id": $("#promo_id :selected").val(),
                            "promo_cc_id": $("#promo_cc_id").val(),
                            "promo_total": AppCashier.totRightPromoDisc,
                            "promo_cc": AppCashier.totRightPromoCc,
                            "promo_name": AppCashier.promoDiscountName,
                            "promo_cc_name": AppCashier.promoCcName,
                            "compliment_code": AppCashier.complimentPayment.code,
                            "compliment_total": AppCashier.complimentPayment.hpp,
                            "delivery_cost_id": delivery_cost_id,
                            "delivery_cost": delivery_cost,
                            'taxes': JSON.stringify(AppCashier.taxes),
                            'data_bayar': JSON.stringify(AppCashier.rightDataOrder),
                            'payment_option': JSON.stringify(payment_option),
                            'sub_total_2': AppCashier.subtotalRight,
                            'round_total': AppCashier.roundRightTotal,
                            'is_round_up': AppCashier.isRoundUp,
                            'discount_member_total': AppCashier.discountMember,
                            'discount_member_id': AppCashier.discountMemberId,
                            'discount_member_percentage': AppCashier.discountMemberPercentage,
                            'origin_grand_total': AppCashier.originGrandTotalRight,
                            'down_payment': $("#reservation_id").attr("down_payment"),
                            'grand_total': AppCashier.grandTotalRight,
                            'customer_cash_payment': AppCashier.customerCashPayment,
                            "pending_bill": JSON.stringify(AppCashier.pendingbill),
                            "pending_bill_employee": JSON.stringify(AppCashier.pendingbillEmployee),
                            "voucher_quantity": AppCashier.voucherQuantity,
                            "receipt_number": receipt_number,
                            "kembalian": AppCashier.kembalian,
                            "down_payment": AppCashier.downPaymentBill.amount,
                            "user_unlock_member_bill":$("#user_unlock_member_bill").val()
                        }
                    });

                    if (type == "done") {
                        request.done(function (msg) {
                            if (AppCashier.diningType == 3) {
                                var is_refund = $("#is_refund").length;
                                var data_refund = JSON.stringify(AppCashier.leftDataOrder);
                                AppCashier.hybridProcess(is_refund, data_refund);
                            } else {
                                var parsedObject = JSON.parse(msg); 
                                if (parsedObject.status === true) {
                                    AppCashier.socket.emit('cm_empty_table', {
                                        number_guest: parsedObject.number_guest,
                                        table_status: parsedObject.table_status,
                                        status_name: parsedObject.status_name,
                                        status_class: parsedObject.status_class,
                                        table_id: parsedObject.table_id,
                                        order_id: parsedObject.order_id,
                                        url_redir: parsedObject.url_redir,
                                        arr_merge_table: parsedObject.arr_merge_table,
                                        room: 'kasir'
                                    });
                                        
                                    setTimeout(function () {
                                        window.location = parsedObject.url_redir;
                                    }, 100);
                                } else {
                                    // window.location.reload(true);
                                    AppCashier.alert("Pembayaran gagal dilakukan!");
                                }
                            }
                            AppCashier.loadingOverlay.hide();
                        });
                        request.fail(function (jqXHR, textStatus) {
                            if (textStatus == 'timeout') {
                                AppCashier.alert($('#server-timeout-message p').text());
                            }
                            // window.location.reload(true);
                            AppCashier.alert("Request Pembayaran Gagal Dilakukan!");

                        });
                        request.always(function () {
                        });


                    }// end done payment
                    else {
                        AppCashier.loadingOverlay.hide();

                    }

                }
            });

            $('#cash-payment').trigger('click');
            $('#cash-payment').trigger('touchend');


            $(document).on(clicked, '.btn-calc-direct', function (e) {
                var number = parseInt($(this).attr('data-value'));
                if (inputPayment.val() == '') {
                    inputPayment.val('0');
                }


                var inputPaymentTemp = inputPayment.val().slice(3).replace(/\,/g, '');
                var customerpay = parseFloat(inputPaymentTemp);

                if (isNaN(customerpay)) {
                    customerpay = 0;
                }

                inputPayment.val(number + customerpay);
                inputPayment.trigger('change');

            });

            // $(document).on(clicked, '.number25', function (e) {
            //     inputPayment.val('25000');
            //     inputPayment.trigger('change');
            //     inputPayment.focus();
            // });

            // $(document).on(clicked, '.number50', function (e) {
            //     inputPayment.val('50000');
            //     inputPayment.trigger('change');
            //     inputPayment.focus();
            // });

            // $(document).on(clicked, '.number75', function (e) {
            //     inputPayment.val('75000');
            //     inputPayment.trigger('change');
            //     inputPayment.focus();
            // });

            // $(document).on(clicked, '.number100', function (e) {
            //     inputPayment.val('100000');
            //     inputPayment.trigger('change');
            //     inputPayment.focus();
            // });

            $(document).on(clicked, '.number', function (e) {
                inputPayment.val($.trim(inputPayment.val()) + $.trim($(e.target).text()));
                inputPayment.trigger('change');

            });

            $(document).on(clicked, '.deleteNumber', function (e) {
                inputPayment.val(inputPayment.val().slice(0, -1));
                inputPayment.trigger('change');

            });

            $(document).on(clicked, '.clearNumber:not(#btn-feature-confirmation-clear)', function (e) {
                switch (AppCashier.paymentType) {
                    case 1:
                        AppCashier.cashPayment.amount = 0;
                        break;
                    case 2:
                        AppCashier.debitPayment.amount = 0;
                        break;
                    case 3:
                        AppCashier.creditPayment.amount = 0;
                        break;
                    case 4:
                        AppCashier.voucherPayment.amount = 0;
                        break;
                    case 5:
                        AppCashier.complimentPayment.amount = 0;
                        break;
                    case 11:
                        AppCashier.flazzPayment.amount = 0;
                        break;
                }

                AppCashier.customerpay = AppCashier.cashPayment.amount + AppCashier.debitPayment.amount + AppCashier.creditPayment.amount + AppCashier.flazzPayment.amount + AppCashier.voucherPayment.amount + AppCashier.downPaymentBill.amount;
                AppCashier.setKembalian();
                inputPayment.val('');
                inputPayment.trigger('change');

                AppCashier.setPaymentBill();
            });

            $(document).on(clicked, '.btn-exactly', function (e) {
                switch (AppCashier.paymentType) {
                    case 1:
                        temp = parseInt(AppCashier.grandTotalRight - AppCashier.complimentPayment.hpp - AppCashier.voucherPayment.amount - AppCashier.creditPayment.amount - AppCashier.debitPayment.amount - AppCashier.downPaymentBill.amount);
                        (AppCashier.downPaymentBill.amount >= AppCashier.grandTotalRight) ? AppCashier.cashPayment.amount = 0 : AppCashier.cashPayment.amount = temp;
                        inputPayment.val((AppCashier.cashPayment.amount < 0) ? 0 : AppCashier.cashPayment.amount);
                        break;
                    case 2:
                        if (AppCashier.debitPayment.code === 0) return;
                        temp = parseInt(AppCashier.grandTotalRight - AppCashier.complimentPayment.hpp - AppCashier.voucherPayment.amount - AppCashier.creditPayment.amount - AppCashier.cashPayment.amount - AppCashier.downPaymentBill.amount);
                        (AppCashier.downPaymentBill.amount >= AppCashier.grandTotalRight) ? AppCashier.debitPayment.amount = 0 : AppCashier.debitPayment.amount = temp;
                        inputPayment.val((AppCashier.debitPayment.amount < 0) ? 0 : AppCashier.debitPayment.amount);
                        break;
                    case 3:
                        if (AppCashier.creditPayment.code === 0) return;
                        temp = parseInt(AppCashier.grandTotalRight - AppCashier.complimentPayment.hpp - AppCashier.voucherPayment.amount - AppCashier.cashPayment.amount - AppCashier.debitPayment.amount - AppCashier.downPaymentBill.amount);
                        (AppCashier.downPaymentBill.amount >= AppCashier.grandTotalRight) ? AppCashier.creditPayment.amount = 0 : AppCashier.creditPayment.amount = temp;
                        inputPayment.val((AppCashier.creditPayment.amount < 0) ? 0 : AppCashier.creditPayment.amount);
                        break;
                    case 4:
                        if (AppCashier.voucherPayment.amount === 0 || AppCashier.voucherPayment.code === 0) return;
                        inputPayment.val((AppCashier.voucherPayment.amount < 0) ? 0 : AppCashier.voucherPayment.amount);
                        break;
                    case 5:
                        temp = parseInt(AppCashier.grandTotalRight - AppCashier.complimentPayment.hpp - AppCashier.cashPayment.amount - AppCashier.debitPayment.amount - AppCashier.creditPayment.amount - AppCashier.downPaymentBill.amount);
                        (AppCashier.downPaymentBill.amount >= AppCashier.grandTotalRight) ? AppCashier.complimentPayment.amount = 0 : AppCashier.complimentPayment.amount = temp;
                        inputPayment.val((AppCashier.complimentPayment.amount < 0) ? 0 : AppCashier.complimentPayment.amount);
                        break;
                    case 11:
                        AppCashier.flazzPayment.amount = parseInt(AppCashier.grandTotalRight - AppCashier.voucherPayment.amount - AppCashier.cashPayment.amount - AppCashier.debitPayment.amount);
                        inputPayment.val((AppCashier.flazzPayment.amount < 0) ? 0 : AppCashier.flazzPayment.amount);
                        break;
                }
                AppCashier.customerpay = AppCashier.cashPayment.amount + AppCashier.debitPayment.amount + AppCashier.creditPayment.amount + AppCashier.voucherPayment.amount + AppCashier.downPaymentBill.amount;
                var temp = AppCashier.customerpay;


                if (AppCashier.customerpay >= AppCashier.grandTotalRight) {
                    AppCashier.customerpay = temp;
                } else {
                    AppCashier.customerpay = AppCashier.grandTotalRight - AppCashier.customerpay;
                }

                AppCashier.setPaymentBill();
                AppCashier.setKembalian();
                inputPayment.trigger('change');
                inputPayment.focus();
                $('#done-payment').prop('disabled', true);

            });


            $(document).on('keypress', '#input-payment', function (e) {
                if (e.keyCode == $.ui.keyCode.ENTER) {
                    $('.payment-ok').trigger('click');
                }
            });

            $('#btn-category-list').on('click', function (e) {
                $('#list-category-text').show();
                $('#thumb-category-text').hide();
                $('#btn-category-thumb').removeClass('active');
                $('#btn-category-list').addClass('active');
                return false;
            });

            $('#btn-category-thumb').on('click', function (e) {
                $('#thumb-category-text').show();
                $('#list-category-text').hide();
                $('#btn-category-list').removeClass('active');
                $('#btn-category-thumb').addClass('active');
                return false;
            });

            $('#btn-menu-list').on('click', function (e) {
                $('#list-menu-text').show();
                $('#thumb-menu-text').hide();
                $('#btn-menu-thumb').removeClass('active');
                $('#btn-menu-list').addClass('active');
                $('#menu-view-type').val('list');
                return false;
            });

            $('#btn-menu-thumb').on('click', function (e) {
                $('#thumb-menu-text').show();
                $('#list-menu-text').hide();
                $('#btn-menu-list').removeClass('active');
                $('#btn-menu-thumb').addClass('active');
                $('#menu-view-type').val('thumb');
                return false;
            });

            $(document).on(clicked, '#btn-discount', function (e) {
                e.preventDefault();

                $('#is_single_discount').val('0');
                $('#discount-name').val("");
                var priceText = $('#subtotal-price').attr('data-price');
                var price = priceText.replace(/\./g, '');
                AppCashier.discount(parseInt(price));

            });

            $(document).on(clicked, '#btn-discount-single', function (e) {
                e.preventDefault();
                $('#is_single_discount').val('1');
                $('#discount-name').val("");
                var priceText = $('#price-menu').val();
                var price = priceText.replace(/\./g, '');
                AppCashier.discount(parseInt(price));

            });

            $('#input-discount-percent').on('change keyup', function (e) {
                var percent = parseInt($('#input-discount-percent').val());
                var subtotal = parseInt($('#subtotal-price-discount').attr('data-price'));
                if (!isNaN(percent)) {
                    $('#input-discount-amount').val(AppCashier.convertToAmount(subtotal, percent));
                } else {
                    $('#input-discount-amount').val(0);
                }

            });

            $('#input-discount-amount').on('change keyup', function (e) {
                var percent = parseInt($('#input-discount-amount').val());
                var subtotal = parseInt($('#subtotal-price-discount').attr('data-price'));
                if (!isNaN(percent)) {
                    $('#input-discount-percent').val(AppCashier.convertToPercent(subtotal, percent));
                } else {
                    $('#input-discount-percent').val(0);
                }

            });


            $('#ddl_discount').on('change', function (e) {

                var selected = $(this).children(':selected');
                var type = selected.attr('data-type');
                var value = selected.attr('data-discount');
                var subtotal = parseInt($('#subtotal-price-discount').attr('data-price'));

                $('#discount-name').val(selected.attr('data-name'));

                if (type == 'percent') {

                    $('#input-discount-percent').val(value).change();
                    var amount = AppCashier.convertToAmount(subtotal, value);
                    $('#input-discount-amount').val(amount).change();

                    $('#form-discount-name').hide();

                } else if (type == 'amount') {
                    $('#input-discount-amount').val(value);
                    var amount = AppCashier.convertToPercent(subtotal, value);
                    $('#input-discount-percent').val(Math.ceil(amount));

                    $('#form-discount-name').hide();

                } else {
                    // console.log(this.value);
                    $('#input-discount-amount').val('0');
                    $('#input-discount-percent').val('0');

                    if (value == 'other') {
                        $('#form-discount-name').show();
                    } else {
                        $('#form-discount-name').hide();

                    }
                }
            });


            $('.btn-save-discount').on('click', function (e) {
                $('.popup-discount').hide();
                $('.popup-block').hide();

                var amount = 0;
                amount = parseInt($('#input-discount-amount').val());

                var name = $('#discount-name').val();
                if (name == "") {
                    name = $('#input-discount-name').val();
                }

                if ($('#is_single_discount').val() == '1') {
                    var menuOrderId = $('#menu_order_id_selected').val();
                    var request = $.ajax({
                        type: "POST",
                        url: AppCashier.baseUrl + 'cashier/save_order_menu_discount',
                        data: {
                            order_id: menuOrderId,
                            discount_price: amount,
                            discount_name: name,
                            type: 'menu'
                        }
                    });
                    request.done(function (msg) {
                        if (msg != '') {
                            var parsedObject = JSON.parse(msg);
                            if (parsedObject.status === true) {
                                $('.bill-table tbody').html(parsedObject.order_list);
                                $('.total-payment tbody').html(parsedObject.order_bill);

                            } else {
                                if (parsedObject.msg != '') {
                                    AppCashier.alert(parsedObject.msg);

                                }
                            }

                            $('.popup-block').hide();
                            AppCashier.isEdit = false;
                            AppCashier.countOld = '';
                            $('#form-input-order').get(0).reset();
                            $('#form-input-order').find('textarea').val('');
                        } else {
                            window.location.reload(true);
                        }
                    });
                    request.fail(function (jqXHR, textStatus) {
                        if (textStatus == 'timeout') {
                            AppCashier.alert($('#server-timeout-message p').text());
                        }
                        window.location.reload(true);

                    });
                    request.always(function () {
                    });


                } else {
                    var orderId = $('#order_id').val();
                    var request = $.ajax({
                        type: "POST",
                        url: AppCashier.baseUrl + 'cashier/save_order_menu_discount',
                        data: {
                            order_id: orderId,
                            discount_price: amount,
                            discount_name: name,
                            type: 'order'
                        }
                    });

                    request.done(function (msg) {
                        if (msg != '') {
                            var parsedObject = JSON.parse(msg);
                            if (parsedObject.status === true) {
                                $('.bill-table tbody').html(parsedObject.order_list);
                                $('.total-payment tbody').html(parsedObject.order_bill);

                            } else {
                                if (parsedObject.msg != '') {
                                    AppCashier.alert(parsedObject.msg);

                                }
                            }

                            $('.popup-block').hide();
                            AppCashier.isEdit = false;
                            AppCashier.countOld = '';
                            $('#form-input-order').get(0).reset();
                            $('#form-input-order').find('textarea').val('');
                        } else {
                            window.location.reload(true);
                        }
                    });
                    request.fail(function (jqXHR, textStatus) {
                        if (textStatus == 'timeout') {
                            AppCashier.alert($('#server-timeout-message p').text());
                        }
                        window.location.reload(true);

                    });
                    request.always(function () {
                    });


                    // $('#discount-total').attr('data-price',amount );
                    // $('#discount-total').attr('data-name', name);
                    // $('#discount-total').html('Rp '+AppCashier.formatRupiah(amount));
                    // $('#discount-total').closest('td').prev('td').html('<b>Diskon ('+$('#input-discount-percent').val()+'%)</b>');

                }
            });
            $(document).on('click', '#btn-compliment', function (e) {
                if (AppCashier.rightDataOrder.length == 0) {
                    AppCashier.alert("Silahkan pilih data pesanan yang akan dibayar!");
                    return;
                }
                user_confirmation = $(this).attr("feature_confirmation");
                if (user_confirmation == undefined)user_confirmation = "";
                if (user_confirmation != "") {
                    AppNav.showConfirmationPIN(user_confirmation, '#btn-compliment', "click", 1);
                    return false;
                }
                var popup = $('.popup-compliment');
                $(".select2").select2();

                $("#value").removeAttr("class");
                $("#value").addClass("form-control compliment-payment-val");

                var compliment_code = $("#compliment_code :selected").val();
                $("#value").val(compliment_code);
                $("#subtitle").html('<b></b>');
                $("#select-bank").hide();
                $("#select-bank-account-card").hide();
                popup.find('.title-popup').text('Input Otorisasi');
                $('#confirm_type').val('compliment');
                $('#form-payment').attr('action', $(this).attr('data-url'));
                popup.find('#btn-ok-input').removeAttr("data-id");
                popup.find('#btn-ok-input').attr("data-id", "btn-compliment-payment");
                popup.find("#btn-ok-input-default").hide();
                popup.show();
                AppCashier.paymentType = 5;

            });

            $('#credit-payment').on('click', function (e) {
                if (AppCashier.paymentType == 1 && AppCashier.customerpay == AppCashier.grandTotalRight) {
                    AppCashier.customerpay = 0;
                    $(".cashPayment").remove();
                    AppCashier.setKembalian();                    
                }
                var popup = $('.popup-input');
                var inputPayment = $('#payment_value');

                $("#value").removeAttr("class");
                $("#value").addClass("form-control only_numeric credit-payment-val");
                $(".credit-payment-val").attr("maxlength", '16');

                $("#subtitle").html('<b>NOMOR KARTU :</b>');
                $("#select-bank").show();
                if ($("#promo_cc").find("option").length > 1) {
                    $("#select-promo-cc").show();
                } else {
                    $("#select-promo-cc").hide();
                }

                $("#select-bank-account-card").show();
                AppCashier.validationNumber($(".credit-payment-val"));
                $(".credit-payment-val").val("");
                popup.find('.title-popup').text('Input Data Kartu Kredit');
                popup.find('form').attr('href', AppCashier.baseUrl);
                $('#confirm_type').val('credit');

                if (AppCashier.subtotalRight > 0)
                    AppCashier.disableNumberButton(false);

                // action button for compliment payment option
                if (AppCashier.paymentType == 5) {
                    AppCashier.complimentActionButton();
                }

                popup.find('#btn-ok-input').removeAttr("data-id");
                popup.find('#btn-ok-input').attr("data-id", "btn-credit-payment");
                popup.find("#btn-ok-input-default").show();
                AppCashier.paymentType = 3;

                if (AppCashier.complimentPayment.amount < 0) {
                    AppCashier.resetInputPayment();
                }

                AppCashier.deactivatePaymentMethod();
                $(this).addClass('active');

                popup.show();
                $('#popup-ajax').attr('style', 'display:none');
            });

            $('#debit-payment').on('click', function (e) {
                var popup = $('.popup-input');
                var inputPayment = $('#payment_value');

                $("#value").removeAttr("class");
                $("#value").addClass("form-control only_numeric debit-payment-val");
                $(".debit-payment-val").attr("maxlength", '16');

                $("#subtitle").html('<b>NOMOR KARTU :</b>');
                $("#promo_cc").val("0");
                $("#select-promo-cc").hide();
                AppCashier.validationNumber($(".debit-payment-val"));
                $("#select-bank").show();
                $("#select-bank-account-card").show();
                $(".debit-payment-val").val("");
                popup.find('.title-popup').text('Input Data Kartu Debit');
                popup.find('form').attr('href', AppCashier.baseUrl);
                $('#confirm_type').val('debit');

                popup.find('#btn-ok-input').removeAttr("data-id");
                popup.find('#btn-ok-input').attr("data-id", "btn-debit-payment");
                popup.find("#btn-ok-input-default").show();

                if (AppCashier.subtotalRight > 0)
                    AppCashier.disableNumberButton(false);

                // action button for compliment payment option
                if (AppCashier.paymentType == 5) {
                    AppCashier.complimentActionButton();
                }

                $("#value").focus();
                AppCashier.paymentType = 2;

                AppCashier.deactivatePaymentMethod();
                $(this).addClass('active');
                popup.show();

                if (AppCashier.complimentPayment.amount < 0) {
                    AppCashier.resetInputPayment();
                }
                $('#popup-ajax').attr('style', 'display:none');
            });

            $('#flazz-payment').on('click', function (e) {
                var popup = $('.popup-input-flazz');
                var inputPayment = $('#payment_value');

                $("#value_flazz").removeAttr("class");
                $("#value_flazz").addClass("form-control only_numeric flazz-payment-val");

                $(".flazz-payment-val").val("");
                popup.find('.title-popup').text('Input Data Kartu Flazz');
                popup.find('form').attr('href', AppCashier.baseUrl);
                $('#confirm_type_flazz').val('flazz');
                $("#promo_cc").val("0");
                $("#select-promo-cc").hide();
                popup.find('#btn-ok-input-flazz').removeAttr("data-id");
                popup.find('#btn-ok-input-flazz').attr("data-id", "btn-flazz-payment");
                popup.find("#btn-ok-input-default").show();

                if (AppCashier.subtotalRight > 0)
                    AppCashier.disableNumberButton(false);

                // action button for compliment payment option
                if (AppCashier.paymentType == 5) {
                    AppCashier.complimentActionButton();
                }

                $("#valueflazz").focus();
                AppCashier.paymentType = 11;

                AppCashier.deactivatePaymentMethod();
                $(this).addClass('active');
                popup.show();

                if (AppCashier.complimentPayment.amount < 0) {
                    AppCashier.resetInputPayment();
                }
            });
            //method voucher
            $('#voucher-payment').on('click', function (e) {
                if (AppCashier.voucherPayment.amount > 0) {
                    AppCashier.alert("Voucher Sudah Di input");
                    return;
                }
                // console.log($(this).attr('data-url'));
                if (parseInt(AppCashier.voucherMethod) === 1) {
                    var popup = $('.popup-input');
                    popup.find('.title-popup').text('Input Data Voucher');

                    $("#subtitle").html('<b>NOMOR VOUCHER :</b>');
                    $("#select-bank").hide();
                    $("#select-bank-account-card").hide();
                } else {
                    var popup = $('.popup-voucher');
                    var voucher_category = $("#voucher_category :selected").val();
                    $("#value").val(voucher_category);
                }
                $("#promo_cc").val("0");
                $("#select-promo-cc").hide();
                $('#form-payment').attr('action', $(this).attr('data-url'));
                $('#confirm_type').val('voucher');

                popup.find('#btn-ok-input').removeAttr("data-id");
                popup.find('#btn-ok-input').attr("data-id", "btn-voucher-payment");
                popup.find("#btn-ok-input-default").hide();


                $("#value").removeAttr("class");

                $("#value").addClass("form-control voucher-payment-val");
                AppCashier.disableNumberButton(true);
                AppCashier.paymentType = 4;

                AppCashier.deactivatePaymentMethod();
                $(this).addClass('active');
                popup.show();
                $('#popup-ajax').attr('style', 'display:none');
            });
            $(document).on('click', '#bon-payment', function () {
                if (AppCashier.rightDataOrder.length == 0) {
                    AppCashier.alert("Silahkan pilih data pesanan yang akan dibayar!");
                    return;
                }
                user_confirmation = $(this).attr("feature_confirmation");
                if (user_confirmation == undefined)user_confirmation = "";
                if (user_confirmation != "") {
                    AppNav.showConfirmationPIN(user_confirmation, '#bon-payment', "click", 1);
                    return false;
                }
                AppCashier.bonPayment();
            });
            $(document).on('click', '#member-payment', function () {
                if (AppCashier.rightDataOrder.length == 0) {
                    AppCashier.alert("Silahkan pilih data pesanan yang akan dibayar!");
                    return;
                }
                user_confirmation = $(this).attr("feature_confirmation");
                if (user_confirmation == undefined)user_confirmation = "";
                if (user_confirmation != "") {
                    AppNav.showConfirmationPIN(user_confirmation, '#member-payment', "click", 1);
                    return false;
                }

                $(".select2").select2();
                var popup = $('.popup-member');
                popup.show();


                $('#btn-ok-member').on('click', function (e) {
                    App.setMemberPayment();
                });
                // popup.find('.title-name').text('Input ID Member');
                // $('#confirm_type').val('member');

                // // $("#value").removeAttr("class");

                // // $("#value").addClass("form-control member-payment-val");

                // $("#value").hide();

                // $("#subtitle").html('<b>ID MEMBER :</b>');
                // $("#select-bank").hide();

                // popup.find('form').attr('href', AppCashier.baseUrl);
                // popup.find('#btn-ok-input').removeAttr("data-id");
                // popup.find('#btn-ok-input').attr("data-id","btn-member-payment");
                // popup.show();
                // AppCashier.initKeyboardDefault($(".member-payment-val"));
            });

            $('#search_member').on('click', function() {
                var popup = $('.popup-data-member');
                popup.show();
            });

            $('.btn-cancel-search-member').on('click', function(e) {
                e.preventDefault();
                $('.popup-data-member').hide();
            });

            $('#datatable-data-member').dataTable({
                "bProcessing": true,
                "bServerSide": true,
                "sServerMethod": "POST",
                "ajax": $('#dataProcessUrl').val(),
                "iDisplayLength": 10,
                "columns": [
                    {data: "name"},
                    {data: "member_id"},
                    {data: "birth_date"},
                    {data: "city"},
                    {data: "email"},
                    {data: "mobile_phone"},
                    {data: "actions"}
                ],
                "columnDefs": [{
                    "targets": 6,
                    "orderable": false,
                    "bSearchable": false,
                    "class": 'center-tr'
                }],
                "order": [[0, "asc"]]
            });

            $(document).on('click', '.choose_member', function() {
                $('.popup-data-member').hide();
                
                member_id = $(this).attr('member_id');
                member_name = $(this).attr('member_name');
                member_code = $(this).attr('member_code');

                $('#member_id_val').val(member_id);
                $('#search_member').val(member_name + ' (' + member_code + ')');
            });

            $(document).on('click', '#btn-company', function (e) {
                if (AppCashier.rightDataOrder.length == 0) {
                    AppCashier.alert("Silahkan pilih data pesanan yang akan dibayar!");
                    return;
                }
                user_confirmation = $(this).attr("feature_confirmation");
                if (user_confirmation == undefined)user_confirmation = "";
                if (user_confirmation != "") {
                    AppNav.showConfirmationPIN(user_confirmation, '#btn-company', "click", 1);
                    return false;
                }
                var popup = $('.popup-order-company');
                popup.find('.title-popup').text('Pending Bill');


                popup.find('form').attr('href', AppCashier.baseUrl);
                popup.find('#btn-ok-input').removeAttr("data-id");
                popup.find('#btn-ok-input').attr("data-id", "btn-company");
                popup.find("#btn-ok-input-default").hide();
                popup.show();

            });
            $('#btn-ok-compliment, #btn-ok-voucher').on('click', function (e) {
                $("#form-payment").trigger("submit");
            });

            $('#btn-ok-input-default').on('click', function (e) {
                target = $(this).attr("target");
                value = $(this).attr("value");
                $(target).val(value);
            });
            $('.btn-ok-input').on('click', function (e) {

                $('.popup-order-company').hide();
                AppCashier.setPendingBill();


            });

            // $('.btn-preview-bill').on('click', function (e){

            //    if ($(this).attr('data-type')== 'single'){

            //         if(AppCashier.subtotalLeft === 0) return;
            //         $('#single-bill').show();

            //         $('#single-bill').find("#list-order > tbody").empty();
            //         var html = AppCashier.getLeftDataOrder();
            //         $('#single-bill').find("#list-order > tbody").append(html);

            //         $('#single-bill-print > tbody').empty();
            //         var clone = $("#total-payment-left > tbody").clone();
            //         $('#single-bill-print').append($(clone));
            //    }else{

            //         if(AppCashier.subtotalRight === 0) return;
            //         $('#custom-bill').show();


            //         $('#custom-bill-order > tbody').empty();
            //         var html = AppCashier.getRightDataOrder();
            //         $('#custom-bill-order').append(html);


            //          $('#custom-bill-print > tbody').empty();
            //         var clone = $("#total-payment-right > tbody").clone();
            //         $('#custom-bill-print').append($(clone));
            //     }

            // });


            $('#btn-ok-confirm').on('click', function (e) {
                var type = $('#confirm_type').val();
                var output = $('#text-code');

                $('#popup-confirm').show();
                if (type == "compliment") {
                    if (AppCashier.complimentType == 0) {
                        if (AppCashier.totalHppRight > 0) {
                            if (AppCashier.complimentPayment.is_cogs != 0 && AppCashier.complimentPayment.limit < AppCashier.totalHppRight && AppCashier.totalHppRight < AppCashier.subtotalRight) {
                                AppCashier.alert("Compliment tidak dapat digunakan!");
                                return;
                            }
                        } else {
                            AppCashier.alert("Compliment tidak dapat digunakan!");
                            return;
                        }
                    }
                } else if (type == "voucher") {
                    if (AppCashier.voucherMethod == 1) {
                        output.text('Kode Voucher: ' + AppCashier.voucherPayment.code)
                    } else {
                        output.text('Kode Voucher: ' + AppCashier.voucherName)
                    }
                } else if (type == "member") {

                }

                AppCashier.hideAllPopup();
                AppCashier.clearALLInput();

            });

            $('#btn-ok-input,#btn-ok-input-flazz').on('click', function (e) {
                var btnId = $(this).attr("data-id");

                // console.log(btnId);
                switch (btnId) {
                    case "btn-credit-payment":
                        AppCashier.setCreditPayment();
                        break;
                    case "btn-debit-payment":
                        AppCashier.setDebitPayment();
                        break;
                    case "btn-flazz-payment":
                        AppCashier.setFlazzPayment();
                        break;
                    case "btn-member-payment":
                        AppCashier.setMemberPayment();
                        break;
                    case "btn-compliment-payment":
                        $("#form-payment").trigger("submit");
                        break;
                    case "btn-voucher-payment":
                        if (AppCashier.grandTotalRight > 0)
                            $("#form-payment").trigger("submit");
                        else
                            AppCashier.alert("Jumlah Order Yang akan Di bayar tidak ada, silahkan pilih menu order");
                }


            });

            $('#form-payment').on("submit", function (e) {
                e.preventDefault();
                AppCashier.complimentType = 1;
                // AppCashier.overlayUI.show();
                if (AppCashier.paymentType == 4) {
                    var code = $('#value').val();
                    var data = {
                        code: code,
                        order_id: $('#order_id').val(),
                    };
                    if (AppCashier.voucherMethod == 2) {
                        var voucherCategory = $("#voucher_category").val();
                        var voucherQuantity = $("#voucher_quantity").val();
                        var data = {
                            voucher_category: voucherCategory,
                            voucher_quantity: voucherQuantity,
                            order_id: $('#order_id').val(),
                        };
                    }
                } else {
                    // var code = $('#value').val();
                    var code = $('#compliment_code option:selected').val();
                    var data = {
                        code: code,
                        order_id: $('#order_id').val(),
                    };
                }
                var request = $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: data
                });
                request.done(function (resp) {
                    if (resp != '') {
                        var parsedObject = JSON.parse(resp);
                        AppCashier.complimentPayment.is_cogs = parseInt(parsedObject.is_cogs);

                        if (parsedObject.status == true) {
                            AppCashier.complimentPayment.limit = parseInt(parsedObject.cogs_limit);

                            if (AppCashier.complimentPayment.limit == 0 && AppCashier.complimentPayment.is_cogs == 1) {
                                AppCashier.alert("Compliment Tidak Mencukupi");
                                return;
                            }

                            $('.popup-input').hide();
                            $('#popup-confirm').show();
                            $('#popup-confirm-content').html(parsedObject.msg);                            

                            if (AppCashier.paymentType === 5) {
                                AppCashier.complimentPayment.amount = 0;

                                priceText = $('#subtotal-price').attr('data-price');
                                price = priceText.replace(/\./g, '');

                                // default value for compliment payment (compliment selling price)
                                var compliment_price = $('input[name="compliment_type"]:checked').val();
                                if (compliment_price) {
                                    AppCashier.complimentType = 1;
                                    if (AppCashier.complimentPayment.is_cogs === 1) { // status for limited compliment user
                                        AppCashier.complimentPayment.code = code;                                    
                                        AppCashier.complimentPayment.amount = AppCashier.orginTotalRight;
                                        if (AppCashier.complimentPayment.limit < AppCashier.subtotalRight) {
                                            AppCashier.complimentPayment.hpp = AppCashier.complimentPayment.limit;
                                        } else {
                                            AppCashier.complimentPayment.hpp = AppCashier.subtotalRight;
                                        }           
                                    } else {
                                        AppCashier.complimentPayment.code = code;
                                        AppCashier.complimentPayment.amount = AppCashier.orginTotalRight;
                                        AppCashier.complimentPayment.hpp = AppCashier.orginTotalRight;
                                    }
                                }

                                // action for compliment type (hpp or selling price)
                                AppCashier.complimentTypeClassEvent(price, code);

                                // action for setting attribut on compliment view payment
                                AppCashier.setCompliment();
                            }

                            if (AppCashier.paymentType == 4) {
                                //check minimum order
                                if (AppCashier.grandTotalRight < parseInt(parsedObject.minimum_order)) {
                                    $('#text-code').text("");
                                    $('#popup-confirm').hide();
                                    AppCashier.alert("Voucher Tidak Bisa digunakan, minimum order: " + parsedObject.minimum_order);

                                    return;
                                }
                                if (AppCashier.voucherMethod == 1) {
                                    AppCashier.voucherPayment.code = code;
                                } else {
                                    AppCashier.voucherPayment.code = parseInt(parsedObject.voucher_category);
                                    AppCashier.voucherQuantity = parseInt(parsedObject.voucher_quantity);
                                    AppCashier.voucherName = (parsedObject.voucher_name);
                                    // console.log(AppCashier.voucherQuantity);
                                }

                                AppCashier.voucherPayment.amount = parseInt(parsedObject.nominal);
                                if (AppCashier.voucherPayment.amount > AppCashier.grandTotalRight) {
                                    AppCashier.voucherPayment.amount = AppCashier.grandTotalRight;
                                    AppCashier.cashPayment.amount = 0;
                                    AppCashier.creditPayment.amount = 0;
                                    AppCashier.debitPayment.amount = 0;
                                    $(".cashPayment").remove();
                                    $(".creditPayment").remove();
                                    $(".debitPayment").remove();
                                }

                                if (AppCashier.voucherPayment.amount > AppCashier.grandTotalRight) {
                                    AppCashier.customerpay = AppCashier.grandTotalRight;
                                } else {
                                    AppCashier.customerpay += AppCashier.voucherPayment.amount;
                                }
                            }

                            AppCashier.setPaymentBill();
                            AppCashier.setKembalian();
                            AppCashier.setGrandTotal();

                        } else {
                            // AppCashier.messageError(parsedObject.msg, 'popup');
                            AppCashier.alert(parsedObject.msg);
                        }
                        $("#value").val("");
                        // App.overlayUI.hide();
                    } else {
                        window.location.reload(true);
                    }
                });
                request.fail(function (jqXHR, textStatus) {
                });
            });
        },

        hybridProcess: function(is_refund, data_refund) {
            var orderId = $('#order_id').val();
            if (is_refund != 1 || is_refund == undefined) {
                var url = AppCashier.baseUrl + 'table/process_order';
                var data = {'order_id': orderId}
            } else {
                var url = AppCashier.baseUrl + 'table/refund_void';
                var data = {
                    'data_refund': data_refund,
                    'order_id': orderId
                }
            }
            
            var request = $.ajax({
                type: 'POST',
                url: url,
                data: data
            });
            request.done(function(msg) {
                if (msg != '' && msg != '0' && is_refund != 1) {
                    var parsedObject = JSON.parse(msg);
                    AppCashier.socket.emit('cm_notify_new_order', {
                        number_guest: parsedObject.number_guest,
                        table_status: parsedObject.table_status,
                        status_name: parsedObject.status_name,
                        status_class: parsedObject.status_class,
                        table_id: parsedObject.table_id,
                        table_name: parsedObject.table_name,
                        order_id: parsedObject.order_id,
                        arr_merge_table: parsedObject.arr_merge_table,
                        floor_id: parsedObject.floor_id,
                        room: 'waiter',
                        notification: parsedObject.notification,
                        outlets: parsedObject.outlets
                    });
                    if (AppCashier.useKitchen == 1) {
                        var temp = $('.bill-table > tbody  > tr');
                        temp.each(function() {

                            var statusMenu = $(this).find(".status_menu_order").html();
                            if (statusMenu == all_cooking_status[0]) {
                                var statusMenu = $(this).find(".status_menu_order").html(all_cooking_status[1]);
                                var arrDiv = $(this).children();
                                $(arrDiv[9]).text('1');
                                $(arrDiv[10]).text('1');
                            }

                        });
                        parsedObject.status_menu.forEach(function(e, i, a) {
                            $("#status_menu_" + e.id).html(e.cooking_status_name);
                        });
                    } else {
                        var temp = $('.bill-table > tbody  > tr');

                        temp.each(function() {

                            var statusMenu = $(this).find(".status_menu_order").html();
                            if (statusMenu == "New") {
                                var statusMenu = $(this).find(".status_menu_order").html("Ready");
                                var arrDiv = $(this).children();
                                $(arrDiv[9]).text('1');
                                $(arrDiv[10]).text('3');
                            }

                        });
                        parsedObject.status_menu.forEach(function(e, i, a) {
                            $("#status_menu_" + e.id).html(e.cooking_status_name);
                        });

                        //add by bening
                        //check if use checker process
                        if (AppCashier.useRoleChecker == 1) {
                            AppCashier.socket.emit('cm_notify_checker', {});
                        }
                    }


                    AppCashier.loadingOverlay.hide();
                    setTimeout(function() {
                        window.location = parsedObject.url_redir;
                    }, 100);
                    // window.location.href=AppTable.baseUrl + "table"

                } else {
                    window.location = AppCashier.baseUrl + "table";
                }
            });
            request.fail(function(jqXHR, textStatus) {
                if (textStatus == 'timeout') {
                    AppCashier.alert($('#server-timeout-message p').text());
                }
                window.location.reload(true);

            });
            request.always(function() {});
        },

        complimentTypeClassEvent: function (price, code) {
            $(".complimentTypeClass").click(function(){
                var compliment_type = $(this).val();
                /* 
                *   compliment_type == 0 is compliment hpp
                *   compliment_type == 1 is compliment selling price
                */
                if (compliment_type == 0) {
                    AppCashier.complimentType = 0;
                    if (AppCashier.complimentPayment.is_cogs === 1) { // status for limited compliment user
                        AppCashier.complimentPayment.code = code;                                    
                        AppCashier.complimentPayment.amount = AppCashier.totalHppRight;
                        if (AppCashier.complimentPayment.limit < AppCashier.totalHppRight) {
                            AppCashier.complimentPayment.hpp = AppCashier.complimentPayment.limit;
                        } else {
                            AppCashier.complimentPayment.hpp = AppCashier.totalHppRight;
                        }           
                    } else {
                        AppCashier.complimentPayment.code = code;
                        AppCashier.complimentPayment.amount = AppCashier.totalHppRight;
                        AppCashier.complimentPayment.hpp = AppCashier.totalHppRight;
                    }
                } else if (compliment_type == 1) {
                    AppCashier.complimentType = 1;
                    if (AppCashier.complimentPayment.is_cogs === 1) { // status for limited compliment user
                        AppCashier.complimentPayment.code = code;                                    
                        AppCashier.complimentPayment.amount = AppCashier.orginTotalRight;
                        if (AppCashier.complimentPayment.limit < AppCashier.subtotalRight) {
                            AppCashier.complimentPayment.hpp = AppCashier.complimentPayment.limit;
                        } else {
                            AppCashier.complimentPayment.hpp = AppCashier.subtotalRight;
                        }
                    } else {
                        AppCashier.complimentPayment.code = code;
                        AppCashier.complimentPayment.amount = AppCashier.orginTotalRight;
                        AppCashier.complimentPayment.hpp = AppCashier.orginTotalRight;
                    }
                }
            });
        },

        resetInputDiscount: function () {
            $('#input-discount-amount').val('');
            $('#input-discount-percent').val('');
        },
        convertToAmount: function (price, value) {
            return price * value / 100;
        },

        convertToPercent: function (price, value) {
            return value / price * 100;
        },

        discount: function (price) {
            $('#subtotal-price-discount').attr('data-price', price).change();
            $('#subtotal-price-discount').html(AppCashier.formatRupiah(price));
            $('.popup-discount').show();

        },
        disableNumberButton: function (evt) {
            $('.btn-calc:not(.btn-pin-confirmation)').prop('disabled', evt);
            $('.btn-calc-direct').prop('disabled', evt);
        },
        disableAllPaymentButton: function (evt) {
            $('.btn-calc:not(.btn-pin-confirmation)').prop('disabled', evt);
            $('.btn-calc-direct').prop('disabled', evt);
            $('.btn-metode').prop('disabled', evt);
            $("#input-payment").prop("disabled", true);
        },
        disablePaymentButton: function (evt) {
            $('.number25').prop('disabled', evt);
            $('.number50').prop('disabled', evt);
            $('.number75').prop('disabled', evt);
            $('.number100').prop('disabled', evt);
        },

        activateNumberButton: function (e) {
            $('.btn-calc').prop('disabled', false);
            $('.btn-calc-direct').prop('disabled', false);

        },

        // button behaviour for compliment payment type
        complimentActionButton: function() {
            $("#input-payment").val(AppCashier.customerpay);
            $("#input-payment").trigger('change');
            AppCashier.disableNumberButton(true);
            $('.btn-calc:not(.btn-pin-confirmation)').prop('disabled', true);
            AppCashier.disablePaymentButton(true);
            $('.payment-ok').prop('disabled', false);
            $('#done-payment').prop('disabled', true);
        },

        formatRupiah: function (num) {
            var p = num.toFixed(2).split(".");
            var chars = p[0].split("").reverse();
            var newstr = '';
            var count = 0;
            for (x in chars) {
                count++;
                if (count % 3 == 1 && count != 1) {
                    newstr = chars[x] + '.' + newstr;
                } else {
                    newstr = chars[x] + newstr;
                }
            }
            return newstr
        },

        deactivatePaymentMethod: function (e) {
            $('#cash-payment').removeClass('active');
            $('#voucher-payment').removeClass('active');
            $('#member-payment').removeClass('active');
            $('#debit-payment').removeClass('active');
            $('#flazz-payment').removeClass('active');
            $('#credit-payment').removeClass('active');
            $('#pending-bill-print').removeClass('active');
            $('#print-bill').removeClass('active');
        },

        updateStockMenu: function (arr_menu_outlet) {
            for (var index = 0; index < arr_menu_outlet.length; index++) {
                $('.total-available-' + arr_menu_outlet[index].id)
                    .html(arr_menu_outlet[index].total_available);
            }
        },
        extractDataOrder: function (parent) {
            var data = {};
            data.product_id = $(parent).attr("id");
            data.menu_id = $(parent).attr("menu_id");
            data.category_id = $(parent).attr("data-category");
            data.use_taxes = $(parent).attr("data-use_taxes");
            for (var i = 0; i < $(parent).children().length; i++) {
                var child = $($(parent).children()[i]);
                switch (i) {
                    case 0:
                        data.product_name = child.text();
                    case 1:
                        data.product_amount = parseInt(child.text());

                    case 2:
                        data.product_price = child.attr("data-price");
                        data.menu_hpp = child.attr("data-menu-hpp");
                }

            }
            ;
            data.origin_price = data.product_price;

            return data;
        },
        bonPayment: function () {
            AppCashier.resetInputPayment();
            AppCashier.resetPayment();
            AppCashier.resetPaymentUI();
            AppCashier.getSubtotal();
            AppCashier.setSubTotal();
            AppCashier.updateTaxInfo();
            AppCashier.setGrandTotal();
            // $("#split-all-right").click();
            $(".btn-exactly").click();
            $(".payment-ok").click();
            AppCashier.cashPayment.type = 9;
            $("tr.cashPayment td:eq(1)").html("<b>BON</b>");
            AppCashier.disableAllPaymentButton(true);
            $("#member-payment").prop("disabled", true);
            $("#btn-compliment").prop("disabled", true);
            $("#btn-company").prop("disabled", true);
            $("#promo_id").prop("disabled", true);
        },
        initSetLeftOrder: function () {


            var parent = $("#bill-table-left > tbody");
            for (var i = 0; i < $(parent).children().length; i++) {
                var child = $(parent).children()[i];

                var data = AppCashier.extractDataOrder(child);
                AppCashier.leftDataOrder.push(data);
            }
            // console.log(AppCashier.leftDataOrder);
            AppCashier.getSubtotal();
            AppCashier.setSubTotal();
        },
        initSetRightOrder: function () {
            $("#total-payment-right").find("#subtotal-price").html(AppCashier.moneyFormat(AppCashier.subtotalRight, "Rp "));
            $("#total-payment-right").find("#total-price").html(AppCashier.moneyFormat(AppCashier.subtotalRight, "Rp "));
            AppCashier.getSubtotal();
            AppCashier.setSubTotal();
        },

        findLeftOrder: function (data) {
            for (var i = 0; i < AppCashier.leftDataOrder.length; i++) {
                if (data.product_id == AppCashier.leftDataOrder[i].product_id) {
                    return AppCashier.leftDataOrder[i];
                }
            }
            return false;
        },
        findRightOrder: function (data) {
            for (var i = 0; i < AppCashier.rightDataOrder.length; i++) {
                if (data.product_id == AppCashier.rightDataOrder[i].product_id) {
                    return AppCashier.rightDataOrder[i];
                }
            }
            return false;
        },
        resetHighlight: function () {
            $("#bill-table-right").find("tbody  > tr.tOrder").removeClass("highlight-bill-right");
            $("#bill-table-left").find("tbody  > tr.tOrder").removeClass("highlight-bill-left");
            // console.log(AppCashier.leftDataOrder);
            // console.log(AppCashier.rightDataOrder);
        },
        sendToLeft: function (data, action, object) {
            var findRightData = AppCashier.findRightOrder(data);
            var findLeftData = AppCashier.findLeftOrder(data);
            var cloneRight = object;


            if (findLeftData) {
                if (action === AppCashier.enumOrder.SINGLE) {
                    findLeftData.product_amount += 1;
                } else {
                    findLeftData.product_amount += data.product_amount;
                }


                var rightElement = $('#bill-table-left').find("tbody  > tr.tOrder");


                for (var i = 0; i < $(rightElement).length; i++) {
                    var child = $(rightElement)[i];
                    if ($(child).attr("id") === findRightData.product_id) {
                        var update_price = findLeftData.product_amount * findLeftData.product_price;
                        $(child).children().eq(1).html(findLeftData.product_amount);
                        $(child).children().eq(2).html(AppCashier.moneyFormat(update_price, "Rp "));
                    }

                }
                ;
            } else {
                if (action === AppCashier.enumOrder.SINGLE) {
                    data.product_amount = 1;
                }

                AppCashier.leftDataOrder.push(data);

                var cloneRight2 = $(cloneRight).clone();
                $(cloneRight2).children().eq(1).html(data.product_amount);
                var update_price = data.product_amount * data.product_price;

                $(cloneRight2).children().eq(2).html(AppCashier.moneyFormat(update_price, "Rp "));

                $(cloneRight2).removeClass("highlight-bill-right");

                $('#bill-table-left').append($(cloneRight2));
            }

            if (findRightData) {
                if (action === AppCashier.enumOrder.SINGLE) {
                    findRightData.product_amount -= 1;

                    if (findRightData.product_amount === 0) {
                        $(cloneRight).remove();
                        // AppCashier.leftDataOrder.slice(indexOf(findLeftData),1);
                        $.each(AppCashier.rightDataOrder, function (i) {
                            if (AppCashier.rightDataOrder[i].product_id === findRightData.product_id) {
                                AppCashier.rightDataOrder.splice(i, 1);
                                return false;
                            }
                        });
                    } else {

                        $(cloneRight).children().eq(1).html(findRightData.product_amount);
                        var update_price = findRightData.product_amount * findRightData.product_price;
                        $(cloneRight).children().eq(2).html(AppCashier.moneyFormat(update_price, "Rp "));
                    }
                } else {
                    AppCashier.resetPayment();
                    $.each(AppCashier.rightDataOrder, function (i) {
                        if (AppCashier.rightDataOrder[i].product_id === findRightData.product_id) {
                            AppCashier.rightDataOrder.splice(i, 1);
                            return false;
                        }
                    });
                }


            }
        },
        sendToRight: function (data, action, object) {
            // console.log("SEND TO RIGHT");
            // console.log(data);
            var findRightData = AppCashier.findRightOrder(data);
            var findLeftData = AppCashier.findLeftOrder(data);
            var cloneLeft = object;


            if (findRightData) {
                if (action === AppCashier.enumOrder.SINGLE) {
                    findRightData.product_amount += 1;
                } else {
                    findRightData.product_amount += data.product_amount;
                }

                var rightElement = $('#bill-table-right').find("tbody  > tr.tOrder");

                for (var i = 0; i < $(rightElement).length; i++) {
                    var child = $(rightElement)[i];
                    if ($(child).attr("id") === findLeftData.product_id) {
                        $(child).children().eq(1).html(findRightData.product_amount);
                        var update_price = findRightData.product_amount * findRightData.product_price;
                        $(child).children().eq(2).html(AppCashier.moneyFormat(update_price, "Rp "));

                    }

                }
                ;
            } else {
                if (action === AppCashier.enumOrder.SINGLE) {
                    data.product_amount = 1;
                }
                AppCashier.rightDataOrder.push(data);

                var cloneLeft2 = $(cloneLeft).clone();
                $(cloneLeft2).children().eq(1).html(data.product_amount);

                var update_price = data.product_amount * data.product_price;
                $(cloneLeft2).children().eq(2).html(AppCashier.moneyFormat(update_price, "Rp "));
                $(cloneLeft2).removeClass("highlight-bill-left");
                $(cloneLeft2).find("td").removeClass("highlight");
                $('#bill-table-right').append($(cloneLeft2));
            }

            if (findLeftData) {
                if (action === AppCashier.enumOrder.SINGLE) {
                    findLeftData.product_amount -= 1;

                    if (findLeftData.product_amount === 0) {
                        $(cloneLeft).remove();
                        // AppCashier.leftDataOrder.slice(indexOf(findLeftData),1);
                        $.each(AppCashier.leftDataOrder, function (i) {
                            if (AppCashier.leftDataOrder[i].product_id === findLeftData.product_id) {
                                AppCashier.leftDataOrder.splice(i, 1);
                                return false;
                            }
                        });
                    } else {
                        $(cloneLeft).children().eq(1).html(findLeftData.product_amount);
                        var update_price = findLeftData.product_amount * findLeftData.product_price;
                        $(cloneLeft).children().eq(2).html(AppCashier.moneyFormat(update_price, "Rp "));
                    }
                } else {
                    // $(cloneLeft).remove();
                    $.each(AppCashier.leftDataOrder, function (i) {
                        if (AppCashier.leftDataOrder[i].product_id === findLeftData.product_id) {
                            AppCashier.leftDataOrder.splice(i, 1);
                            return false;
                        }
                    });
                }

            }
        },

        removeAllLeftElement: function () {
            $('#bill-table-left > tbody  > tr').each(function () {
                $(this).remove();

            });
        },

        removeAllRightElement: function () {
            $('#bill-table-right > tbody  > tr').each(function () {
                $(this).remove();

            });
        },

        clearALLInput: function () {
            $('#form-input-order').get(0).reset();
            $('#value').val('');
            $('#form-input-order').find('textarea').val('');
        },

        hideAllPopup: function () {
            $('#popup-confirm').hide();
            $('.popup-block').hide();
            $('.popup-discount').hide();

        },

        getLeftDataOrder: function () {
            var html = "";
            for (var i = 0; i < AppCashier.leftDataOrder.length; i++) {
                var product_amount = AppCashier.leftDataOrder[i].product_amount;
                var product_price = AppCashier.leftDataOrder[i].product_price;
                var product_name = AppCashier.leftDataOrder[i].product_name;
                var amount = product_price * product_amount;
                html += "<tr>" +
                    "<td style='width:10%;'>" + (i + 1) + "</td>" +
                    "<td style='width:60%;'>" + product_name + "</td>" +
                    "<td style='width:30%;text-align:right;'>" + AppCashier.moneyFormat(amount, "Rp") + "</td>" +
                    "</tr>";
            }
            ;
            return html;
        },

        getRightDataOrder: function () {
            var html = "";
            for (var i = 0; i < AppCashier.rightDataOrder.length; i++) {
                var product_amount = AppCashier.rightDataOrder[i].product_amount;
                var product_price = AppCashier.rightDataOrder[i].product_price;
                var product_name = AppCashier.rightDataOrder[i].product_name;
                var amount = product_price * product_amount;
                html += "<tr>" +
                    "<td style='width:10%;'>" + (i + 1) + "</td>" +
                    "<td style='width:60%;'>" + product_name + "</td>" +
                    "<td style='width:30%;text-align:right;'>" + AppCashier.moneyFormat(amount, "Rp") + "</td>" +
                    "</tr>";
            }
            ;
            return html;
        },

        moneyFormat: function (n, currency) {
            return currency + " " + n.toFixed(0).replace(/./g, function (c, i, a) {
                    return i > 0 && c !== "," && (a.length - i) % 3 === 0 ? "." + c : c;
                });

        },

        getSubtotal: function () {
            console.log("GET subtotal");
            AppCashier.subtotalLeft = 0;
            AppCashier.subtotalRight = 0;
            AppCashier.orginTotalLeft = 0;
            AppCashier.orginTotalRight = 0;
            AppCashier.totalHppLeft = 0;
            AppCashier.totalHppRight = 0;

            for (var i = 0; i < AppCashier.leftDataOrder.length; i++) {
                var product_amount = AppCashier.leftDataOrder[i].product_amount;
                var product_price = AppCashier.leftDataOrder[i].origin_price;
                var menu_hpp = AppCashier.leftDataOrder[i].menu_hpp;
                var amount = product_price * product_amount;
                AppCashier.subtotalLeft += amount;
                AppCashier.orginTotalLeft += amount;
                AppCashier.totalHppLeft += menu_hpp;
            }
            // console.log(AppCashier.rightDataOrder);
            for (var j = 0; j < AppCashier.rightDataOrder.length; j++) {
                var product_amount_right = AppCashier.rightDataOrder[j].product_amount;
                var product_price_right = AppCashier.rightDataOrder[j].origin_price;
                var menu_hpp = AppCashier.rightDataOrder[j].menu_hpp;
                var amountRight = product_price_right * product_amount_right;

                AppCashier.subtotalRight += amountRight;
                AppCashier.orginTotalRight += amountRight;
                AppCashier.totalHppRight += product_amount_right * parseInt(menu_hpp);
            }
            ("GET SUBTOTAL" + AppCashier.subtotalRight);
        },

        setCompliment: function () {
            var inputPayment = $('#input-payment');
            var paymentOk = $('.payment-ok');
            AppCashier.discountMemberId = 0;
            AppCashier.discountMemberPercentage = 0;
            AppCashier.discountMember = 0;
            AppCashier.viewCompliment = 0;
            AppCashier.setPromoDiscount("", true);
            AppCashier.setPromoCc("", true);
            $(".promo-discount-right, .promo-discount-left").remove();
            $(".promo-cc-right, .promo-cc-left").remove();
            $(".promo-compliment-right,.promo-compliment-left").remove();

            AppCashier.categoryPromoDiscounts = [];
            AppCashier.categoryPromoCc = [];

            // AppCashier.subTotalCompliment = AppCashier.orginTotalRight - AppCashier.complimentPayment.hpp;
            
            AppCashier.subTotalDiscountLeft = AppCashier.orginTotalLeft;
            AppCashier.subTotalDiscountRight = AppCashier.orginTotalRight;

            if (AppCashier.subTotalDiscountRight < 0) AppCashier.subTotalDiscountRight = 0;
            if (AppCashier.subTotalDiscountLeft < 0) AppCashier.subTotalDiscountLeft = 0;

            $("#promo_id").val(0);
            $('#promo_id').prop('disabled', true);
            $("#promo_cc").val(0);
            $('#promo_cc').prop('disabled', true);
            $(".discount-member").remove();

            AppCashier.voucherPayment.amount = 0;
            AppCashier.resetTaxInfo();
            AppCashier.getSubtotal();
            AppCashier.subtotalRight = AppCashier.subTotalDiscountRight;
            AppCashier.setGrandTotal();
            AppCashier.setKembalian();

            // default view is compliment selling price
            var compliment_price = $('input[name="compliment_type"]:checked').val();
            if (compliment_price) {
                AppCashier.complimentType = 1;
                if (AppCashier.complimentPayment.is_cogs == 1) { // status for limited compliment user
                    if (AppCashier.complimentPayment.limit < AppCashier.orginTotalRight) {
                        AppCashier.customerpay = AppCashier.orginTotalRight - AppCashier.complimentPayment.limit;

                        AppCashier.viewCompliment = AppCashier.complimentPayment.limit;
                        AppCashier.disableNumberButton(false);
                        $('.btn-metode').prop('disabled', false);
                        paymentOk.prop('disabled', false);
                    } else {
                        AppCashier.customerpay = AppCashier.orginTotalRight;

                        AppCashier.viewCompliment = AppCashier.orginTotalRight;
                        AppCashier.disableNumberButton(true);
                        $('.btn-metode').prop('disabled', true);
                    }
                } else {
                    AppCashier.resetTaxInfo();
                    AppCashier.customerpay = AppCashier.orginTotalRight;

                    AppCashier.viewCompliment = AppCashier.orginTotalRight;
                    AppCashier.disableNumberButton(true);
                    $('.btn-metode').prop('disabled', true);
                }
            }

            AppCashier.setComplimentEvent(inputPayment, paymentOk);
            
            $(".voucherPayment").remove();

            if (AppCashier.rightDataOrder.length > 0) {
                $("#total-payment-right").find("#sub-total-2").html(AppCashier.moneyFormat(AppCashier.subtotalRight, "Rp "));
                $(".promo-compliment-right").remove();
                var html = "<tr class='promo-compliment-right'><td> </td><td><b>Compliment</b></td><td class='tb-align-right'>" + AppCashier.moneyFormat(AppCashier.viewCompliment, "Rp ") + "</td></tr>";
                $('.payment-method').after(html);
                //var html2 = "<tr class='promo-compliment-right'><td> </td><td><b>Compliment</b></td><td class='tb-align-right'>" + AppCashier.moneyFormat(AppCashier.viewCompliment, "Rp ") + "</td></tr>";
                //$("#total-payment-right").find(".discount-order-list").after(html2);
            }
        },

        setComplimentEvent: function (inputPayment, paymentOk) {
            $(".complimentTypeClass").click(function(){
                var compliment_type = $(this).val();
                if (compliment_type == 0) {
                    AppCashier.complimentType = 0;
                    if (AppCashier.complimentPayment.is_cogs == 1) {
                        if (AppCashier.complimentPayment.limit < AppCashier.totalHppRight) {

                            AppCashier.customerpay = AppCashier.totalHppRight - AppCashier.complimentPayment.limit;

                            AppCashier.viewCompliment = AppCashier.complimentPayment.limit;
                            AppCashier.disableNumberButton(false);
                            $('.btn-metode').prop('disabled', false);
                            paymentOk.prop('disabled', false);
                        } else {
                            AppCashier.customerpay = AppCashier.totalHppRight;

                            AppCashier.viewCompliment = AppCashier.totalHppRight;
                            AppCashier.disableNumberButton(true);
                            $('.btn-metode').prop('disabled', true);
                        }
                    } else {
                        AppCashier.resetTaxInfo();
                        AppCashier.customerpay = AppCashier.grandTotalRight;
                        $('.btn-metode').prop('disabled', true);
                    }
                } else if (compliment_type == 1) {
                    AppCashier.complimentType = 1;
                    if (AppCashier.complimentPayment.is_cogs == 1) {
                        if (AppCashier.complimentPayment.limit < AppCashier.orginTotalRight) {
                            AppCashier.customerpay = AppCashier.orginTotalRight - AppCashier.complimentPayment.limit;

                            AppCashier.viewCompliment = AppCashier.complimentPayment.limit;
                            AppCashier.disableNumberButton(false);
                            $('.btn-metode').prop('disabled', false);
                            paymentOk.prop('disabled', false);
                        } else {
                            AppCashier.customerpay = AppCashier.orginTotalRight;

                            AppCashier.viewCompliment = AppCashier.orginTotalRight;
                            AppCashier.disableNumberButton(true);
                            $('.btn-metode').prop('disabled', true);
                        }
                    } else {
                        AppCashier.resetTaxInfo();
                        AppCashier.customerpay = AppCashier.orginTotalRight;

                        AppCashier.viewCompliment = AppCashier.orginTotalRight;
                        AppCashier.disableNumberButton(true);
                        $('.btn-metode').prop('disabled', true);
                    }
                }
            });
        },

        resetComplimentPayment: function() {
            AppCashier.paymentType = 1; // change status payment type to default (cash)
            AppCashier.complimentPayment.amount = 0;
            AppCashier.complimentPayment.hpp = 0;
            $(".promo-compliment-right").remove();
            AppCashier.customerpay = 0;
            AppCashier.activateNumberButton(true);
            AppCashier.disableAllPaymentButton(false);
            AppCashier.setPromoDiscount("", true);
            AppCashier.setPromoCc("", true);
            AppCashier.getSubtotal();
            AppCashier.setSubTotal();
            AppCashier.updateTaxInfo();
            AppCashier.setKembalian();
        },

        setSubTotal: function () {
            console.log("SET subtotal");
            //console.log(AppCashier.subtotalLeft);
            $("#total-payment-left").find("#subtotal-price").html(AppCashier.moneyFormat(AppCashier.orginTotalLeft, "Rp "));
            $("#single-bill").find("#subtotal-price").html(AppCashier.moneyFormat(AppCashier.orginTotalLeft, "Rp "));

            $("#total-payment-right").find("#subtotal-price").html(AppCashier.moneyFormat(AppCashier.orginTotalRight, "Rp "));
            $("#custom-bill").find("#subtotal-price").html(AppCashier.moneyFormat(AppCashier.orginTotalRight, "Rp "));


            AppCashier.updateDiscountMember();

            AppCashier.setPromoDiscount(AppCashier.promoDiscountName, false);
            AppCashier.setPromoCc(AppCashier.promoCcName, false);

            if (AppCashier.rightDataOrder.length > 0) {

                AppCashier.subtotalRight = AppCashier.orginTotalRight - AppCashier.totRightPromoDisc - AppCashier.totRightPromoCc - AppCashier.discountMember - AppCashier.complimentPayment.amount;

            } else {
                $("#promo_id").val(0);
                $("#promo_cc").val(0);
            }


            $("#total-payment-left").find("#sub-total-2").html(AppCashier.moneyFormat(AppCashier.subtotalLeft, "Rp "));
            $("#total-payment-right").find("#sub-total-2").html(AppCashier.moneyFormat(AppCashier.subtotalRight, "Rp "));
            
			// console.log("AppCashier.complimentPayment.amount", AppCashier.complimentPayment.amount);
            if (AppCashier.complimentPayment.amount > 0) {
				// console.log("resetTaxInfo");
                AppCashier.resetTaxInfo();
            } else {
                AppCashier.updateDiscount();
                AppCashier.updateTaxInfo();
            }            
        },

        updateTaxInfo: function () {
            AppCashier.subtotalTaxRight = 0;
            AppCashier.subtotalTaxLeft = 0;
            AppCashier.taxes = [];
            if (AppCashier.complimentPayment.amount > 0) return;
            var taxes2 = $("#total-payment-left").find("#tax-price");
            var total_non_taxes = 0;
            var total_services = 0;
            var total_non_services = 0;
            var subtotal_taxes = 0;
            for (x = 0; x < AppCashier.leftDataOrder.length; x++) {
                product_price = parseFloat(AppCashier.leftDataOrder[x].product_price);
                if (isNaN(product_price) || product_price == undefined)product_price = 0;
                product_amount = parseFloat(AppCashier.leftDataOrder[x].product_amount);
                if (isNaN(product_amount) || product_amount == undefined)product_amount = 0;
                use_taxes = parseInt(AppCashier.leftDataOrder[x].use_taxes);
                if (isNaN(use_taxes) || use_taxes == undefined)use_taxes = 1;
                if (use_taxes == 0) {
                    total_non_taxes += (product_amount * product_price);
                }
            }
            //alert(AppCashier.paymentType);

            for (var i = 0; i < taxes2.length; i++) {
                if ($(taxes2[i]).attr("service") == 1) {
                    total_services += $(taxes2[i]).attr("percentage") * AppCashier.subtotalLeft / 100;
                }
            }
            subtotal_taxes = AppCashier.subtotalLeft - total_non_taxes;
            for (var j = 0; j < taxes2.length; j++) {
                var newTax2 = 0;
                if (AppCashier.paymentType != AppCashier.pendingbillEmployee.type) {
                    if (AppCashier.taxServiceMethod == 1) {
                        newTax2 = $(taxes2[j]).attr("percentage") * AppCashier.subtotalLeft / 100;
                        newTax2 -= $(taxes2[j]).attr("percentage") * total_non_taxes / 100;
                        if (newTax2 < 0) newTax2 = 0;
                    } else {
                        var newSubtotal = 0;
                        service = $(taxes2[j]).attr("service");
                        if (service == 1) {
                            total_services = subtotal_taxes * $(taxes2[j]).attr("percentage") / 100;
                            newTax2 += total_services;
                        } else {
                            newSubtotal = subtotal_taxes + total_services;
                            newTax2 = newSubtotal * $(taxes2[j]).attr("percentage") / 100;
                        }
                        if (newTax2 < 0) newTax2 = 0;
                    }
                }
                $(taxes2[j]).html(AppCashier.moneyFormat(newTax2, "Rp "));

                AppCashier.subtotalTaxLeft += newTax2;
            }
            
            var delivery_cost = parseFloat($("#total-payment-left #delivery_cost").attr("cost"));
            if (isNaN(delivery_cost))delivery_cost = 0;
            AppCashier.subtotalTaxLeft += delivery_cost;


            var taxes = $("#total-payment-right").find("#tax-price");
            var total_non_taxes2 = 0;
            var total_services2 = 0;
            var total_non_services2 = 0;
            var subtotal_taxes2 = 0;
            for (x = 0; x < AppCashier.rightDataOrder.length; x++) {
                product_price = parseFloat(AppCashier.rightDataOrder[x].product_price);
                if (isNaN(product_price) || product_price == undefined)product_price = 0;
                product_amount = parseFloat(AppCashier.rightDataOrder[x].product_amount);
                if (isNaN(product_amount) || product_amount == undefined)product_amount = 0;
                use_taxes = parseInt(AppCashier.rightDataOrder[x].use_taxes);
                if (isNaN(use_taxes) || use_taxes == undefined)use_taxes = 1;
                if (use_taxes == 0) {
                    total_non_taxes2 += (product_amount * product_price);
                }
            }

            for (var i = 0; i < taxes.length; i++) {
                if ($(taxes[i]).attr("service") == 1) {
                    total_services2 += $(taxes[i]).attr("percentage") * AppCashier.subtotalRight / 100;
                }
            }
            subtotal_taxes2 = AppCashier.subtotalRight - total_non_taxes2;
            for (var i = 0; i < taxes.length; i++) {
                var newTax = 0;
                if (AppCashier.paymentType != AppCashier.pendingbillEmployee.type) {
                    if (AppCashier.taxServiceMethod == 1) {
                        newTax = $(taxes[i]).attr("percentage") * AppCashier.subtotalRight / 100;
                        newTax -= $(taxes[i]).attr("percentage") * total_non_taxes2 / 100;
                        if (newTax < 0) newTax = 0;
                    } else {
                        var newSubtotal = 0;
                        service = $(taxes[i]).attr("service");
                        if (service == 1) {
                            total_services2 = subtotal_taxes2 * $(taxes[i]).attr("percentage") / 100;
                            newTax += total_services2;
                        } else {
                            newSubtotal = subtotal_taxes2 + total_services2;
                            newTax = newSubtotal * $(taxes[i]).attr("percentage") / 100;
                        }
                        if (newTax < 0) newTax = 0;
                    }
                }
                var objectTax = {};
                objectTax.tax_name = $(taxes[i]).attr("tax-origin-name") + " (" + $(taxes[i]).attr("percentage") + "%)";
                objectTax.tax_percentage = $(taxes[i]).attr("percentage");
                objectTax.account_id = $(taxes[i]).attr("account-id");
                objectTax.tax_total = newTax;
                AppCashier.taxes.push(objectTax);
                $(taxes[i]).html(AppCashier.moneyFormat(newTax, "Rp "));

                AppCashier.subtotalTaxRight += newTax;
            }
            
            var delivery_cost = parseFloat($("#total-payment-right #delivery_cost").attr("cost"));
            if (isNaN(delivery_cost))delivery_cost = 0;
            AppCashier.subtotalTaxRight += delivery_cost;
            //AppCashier.setPromoDiscount(AppCashier.promoDiscountName,false);
            AppCashier.setKembalian();
        },

        setGrandTotal: function () {
            down_payment = parseFloat($("#reservation_id").attr("down_payment"));
            if (isNaN(down_payment)) {
                down_payment = 0;
            }
            AppCashier.downPaymentBill.code = $("#reservation_id").val();
            AppCashier.downPaymentBill.amount = down_payment;
            AppCashier.originGrandTotalRight = parseInt(AppCashier.subtotalTaxRight + AppCashier.subtotalRight);
            
            AppCashier.grandTotalRight = parseInt(Math.round(AppCashier.subtotalTaxRight) + Math.round(AppCashier.subtotalRight));
            AppCashier.grandTotalLeft = parseInt(Math.round(AppCashier.subtotalTaxLeft) + Math.round(AppCashier.subtotalLeft));

            if (AppCashier.grandTotalRight < 0) AppCashier.grandTotalRight = 0;
            if (AppCashier.grandTotalLeft < 0) AppCashier.grandTotalLeft = 0;

            var tempGrandTotalLeft = AppCashier.grandTotalLeft;
            var tempGrandTotalRight = AppCashier.grandTotalRight;
            if (AppCashier.isRoundUp == 1) {

                if (AppCashier.leftDataOrder.length > 0 && AppCashier.grandTotalLeft % parseInt(AppCashier.nearestRound) > 0) {
                    AppCashier.grandTotalLeft += parseInt(AppCashier.nearestRound) - (AppCashier.grandTotalLeft % AppCashier.nearestRound);
                    var pembulatan = AppCashier.grandTotalLeft - tempGrandTotalLeft;
                    if (AppCashier.nearestRound == 1000) {
                        if (pembulatan > 500) {
                            pembulatan = pembulatan - AppCashier.nearestRound;
                            AppCashier.grandTotalLeft = tempGrandTotalLeft + pembulatan;
                        }
                    }
                    if (AppCashier.complimentPayment.amount > 0) {
                        AppCashier.grandTotalRight -= AppCashier.roundRightTotal;
                        pembulatan = 0;
                    }
                    $("#total-payment-left").find("#pembulatan").html(((pembulatan < 0) ? "- " : "") + "Rp " + Math.abs(pembulatan).toFixed(0));
                    AppCashier.roundRightTotal = pembulatan.toFixed(0);
                } else {
                    $("#total-payment-left").find("#pembulatan").html("Rp 0");
                    AppCashier.roundRightTotal = 0;
                }

                if (AppCashier.rightDataOrder.length > 0 && AppCashier.grandTotalRight % parseInt(AppCashier.nearestRound) > 0) {

                    AppCashier.grandTotalRight += parseInt(AppCashier.nearestRound) - (AppCashier.grandTotalRight % AppCashier.nearestRound);
                    AppCashier.grandTotalRight -= (AppCashier.grandTotalRight % AppCashier.nearestRound).toFixed(0);
                    AppCashier.roundRightTotal = AppCashier.grandTotalRight - tempGrandTotalRight;
                    if (AppCashier.nearestRound == 1000) {
                        if (AppCashier.roundRightTotal > 500) {
                            AppCashier.roundRightTotal = AppCashier.roundRightTotal - AppCashier.nearestRound;
                            AppCashier.grandTotalRight = tempGrandTotalRight + AppCashier.roundRightTotal;
                        }
                    }
                    if (AppCashier.complimentPayment.amount > 0) {
                        AppCashier.grandTotalRight -= AppCashier.roundRightTotal;
                        AppCashier.roundRightTotal = 0;
                    }
                    $("#total-payment-right").find("#pembulatan").html(((AppCashier.roundRightTotal < 0) ? "- " : "") + "Rp " + Math.abs(AppCashier.roundRightTotal).toFixed(0));
                    AppCashier.roundRightTotal = AppCashier.roundRightTotal.toFixed(0);
                } else {
                    $("#total-payment-right").find("#pembulatan").html("Rp 0");
                    AppCashier.roundRightTotal = 0;
                }
            } else {
                if (AppCashier.leftDataOrder.length > 0 && AppCashier.grandTotalLeft % parseInt(AppCashier.nearestRound) > 0) {
                    AppCashier.grandTotalLeft -= (AppCashier.grandTotalLeft % AppCashier.nearestRound).toFixed(0);
                    var pembulatan = AppCashier.grandTotalLeft - tempGrandTotalLeft;
                    if (AppCashier.complimentPayment.amount > 0) {
                        AppCashier.grandTotalRight -= pembulatan;
                        pembulatan = 0;
                    }
                    $("#total-payment-left").find("#pembulatan").html("Rp " + pembulatan);
                    AppCashier.roundRightTotal = pembulatan;
                } else {
                    $("#total-payment-left").find("#pembulatan").html("Rp 0");
                    AppCashier.roundRightTotal = 0;
                }


                if (AppCashier.rightDataOrder.length > 0 && AppCashier.grandTotalRight % parseInt(AppCashier.nearestRound) > 0) {
                    AppCashier.grandTotalRight -= (AppCashier.grandTotalRight % AppCashier.nearestRound).toFixed(0);
                    AppCashier.roundRightTotal = AppCashier.grandTotalRight - tempGrandTotalRight;
                    if (AppCashier.complimentPayment.amount > 0) {

                        AppCashier.grandTotalRight -= AppCashier.roundRightTotal;
                        AppCashier.roundRightTotal = 0;
                    }
                    $("#total-payment-right").find("#pembulatan").html("Rp " + AppCashier.roundRightTotal.toFixed(0));
                    AppCashier.roundRightTotal = AppCashier.roundRightTotal.toFixed(0);
                } else {
                    $("#total-payment-right").find("#pembulatan").html("Rp 0");
                    AppCashier.roundRightTotal = 0;
                }
            }


            $("#total-payment-left").find("#total-price").html(AppCashier.moneyFormat(AppCashier.grandTotalLeft, "Rp "));
            $("#single-bill").find("#grand-total").html(AppCashier.moneyFormat(AppCashier.grandTotalLeft, "Rp "));
            $("#single-bill").find("#total-price").html(AppCashier.moneyFormat(AppCashier.grandTotalLeft, "Rp "));

            $("#total-payment-right").find("#total-price").html(AppCashier.moneyFormat(AppCashier.grandTotalRight, "Rp "));
            $("#custom-bill").find("#total-price").html(AppCashier.moneyFormat(AppCashier.subtotalRight, "Rp "));
            AppCashier.setDiscountPendingBill();
        },

        number_format: function (number, decimals, dec_point, thousands_sep) {
            //  discuss at: http://phpjs.org/functions/number_format/
            // original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
            // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // improved by: davook
            // improved by: Brett Zamir (http://brett-zamir.me)
            // improved by: Brett Zamir (http://brett-zamir.me)
            // improved by: Theriault
            // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // bugfixed by: Michael White (http://getsprink.com)
            // bugfixed by: Benjamin Lupton
            // bugfixed by: Allan Jensen (http://www.winternet.no)
            // bugfixed by: Howard Yeend
            // bugfixed by: Diogo Resende
            // bugfixed by: Rival
            // bugfixed by: Brett Zamir (http://brett-zamir.me)
            //  revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
            //  revised by: Luke Smith (http://lucassmith.name)
            //    input by: Kheang Hok Chin (http://www.distantia.ca/)
            //    input by: Jay Klehr
            //    input by: Amir Habibi (http://www.residence-mixte.com/)
            //    input by: Amirouche
            //   example 1: number_format(1234.56);
            //   returns 1: '1,235'
            //   example 2: number_format(1234.56, 2, ',', ' ');
            //   returns 2: '1 234,56'
            //   example 3: number_format(1234.5678, 2, '.', '');
            //   returns 3: '1234.57'
            //   example 4: number_format(67, 2, ',', '.');
            //   returns 4: '67,00'
            //   example 5: number_format(1000);
            //   returns 5: '1,000'
            //   example 6: number_format(67.311, 2);
            //   returns 6: '67.31'
            //   example 7: number_format(1000.55, 1);
            //   returns 7: '1,000.6'
            //   example 8: number_format(67000, 5, ',', '.');
            //   returns 8: '67.000,00000'
            //   example 9: number_format(0.9, 0);
            //   returns 9: '1'
            //  example 10: number_format('1.20', 2);
            //  returns 10: '1.20'
            //  example 11: number_format('1.20', 4);
            //  returns 11: '1.2000'
            //  example 12: number_format('1.2000', 3);
            //  returns 12: '1.200'
            //  example 13: number_format('1 000,50', 2, '.', ' ');
            //  returns 13: '100 050.00'
            //  example 14: number_format(1e-8, 8, '.', '');
            //  returns 14: '0.00000001'

            number = (number + '')
                .replace(/[^0-9+\-Ee.]/g, '');
            var n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                s = '',
                toFixedFix = function (n, prec) {
                    var k = Math.pow(10, prec);
                    return '' + (Math.round(n * k) / k)
                            .toFixed(prec);
                };
            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
                .split('.');
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || '')
                    .length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1)
                    .join('0');
            }
            return s.join(dec);
        },

        reservationSubtotal: function () {
            order_type = $("#order_type").val();
            tax_service_method = $("#tax_service_method").val();
            if ($("#reservation-menu-tab").length == 0)return false;
            // tax_percentages=parseFloat($("#tax_percentages").val());
            // if(isNaN(tax_percentages))tax_percentages=0;
            subtotal = 0;
            subtotal_taxes = 0;
            // taxes=0;
            total_per_taxes = [];
            total_services = 0;
            total_non_services = 0;
            total_taxes = 0;
            total_non_taxes = 0;
            service_dinein = 0;
            service_takeaway = 0;
            service_delivery = 0;
            $("input#tax-price-dinein").each(function (i2, e2) {
                service = $(this).attr('service');
                if (service == 1) {
                    service_dinein = parseInt($(this).attr('percentage'));
                }
            });
            $("input#tax-price-takeaway").each(function (i2, e2) {
                service = $(this).attr('service');
                if (service == 1) {
                    service_takeaway = parseInt($(this).attr('percentage'));
                }
            });
            $("input#tax-price-delivery").each(function (i2, e2) {
                service = $(this).attr('service');
                if (service == 1) {
                    service_delivery = parseInt($(this).attr('percentage'));
                }
            });
            $('.bill-table tbody tr').each(function () {
                total_sidedish = parseFloat($(this).attr("total_sidedish"));
                if (isNaN(total_sidedish))total_sidedish = 0;
                is_taxes = parseInt($(this).attr("is_taxes"));
                qty = parseInt($(this).find(".qty").val());
                if (isNaN(qty))qty = 0;
                price = parseFloat($(this).attr("price"));
                if (isNaN(price))price = 0;
                subtotal += (qty * price) + total_sidedish;
                // taxes+=(is_taxes==1 ? (qty*price)*tax_percentages/100 : 0);
                if (order_type == 1) {
                    $("input#tax-price-dinein").each(function (i2, e2) {
                        percentage = parseFloat($(this).attr("percentage"));
                        service = parseInt($(this).attr("service"));
                        total_per_taxes[i2] = 0;
                        if (tax_service_method == 1) {
                            total_per_taxes[i2] += total_sidedish * percentage / 100;
                            total_per_taxes[i2] += (is_taxes == 1 ? (qty * price) * percentage / 100 : 0);
                        } else {
                            if (service == 1) {
                                total_services = subtotal * service_dinein / 100;
                                total_non_services += (is_taxes != 1 ? (qty * price) * percentage / 100 : 0);
                                total_services -= total_non_services;
                                subtotal_taxes += (is_taxes == 1 ? (qty * price) : 0);
                                total_per_taxes[i2] = total_services;
                            } else {
                                new_subtotal = subtotal_taxes + total_services;
                                total_taxes = new_subtotal * percentage / 100;
                                total_per_taxes[i2] = total_taxes;
                            }
                        }
                    });
                } else if (order_type == 2) {
                    $("input#tax-price-takeaway").each(function (i2, e2) {
                        percentage = parseFloat($(this).attr("percentage"));
                        service = parseInt($(this).attr("service"));
                        total_per_taxes[i2] = 0;
                        if (tax_service_method == 1) {
                            total_per_taxes[i2] += total_sidedish * percentage / 100;
                            total_per_taxes[i2] += (is_taxes == 1 ? (qty * price) * percentage / 100 : 0);
                        } else {
                            if (service == 1) {
                                total_services = subtotal * service_takeaway / 100;
                                total_non_services += (is_taxes != 1 ? (qty * price) * percentage / 100 : 0);
                                total_services -= total_non_services;
                                subtotal_taxes += (is_taxes == 1 ? (qty * price) : 0);
                                total_per_taxes[i2] = total_services;
                            } else {
                                new_subtotal = subtotal_taxes + total_services;
                                total_taxes = new_subtotal * percentage / 100;
                                total_per_taxes[i2] = total_taxes;
                            }
                        }
                    });
                } else {
                    $("input#tax-price-delivery").each(function (i2, e2) {
                        percentage = parseFloat($(this).attr("percentage"));
                        service = parseInt($(this).attr("service"));
                        total_per_taxes[i2] = 0;
                        if (tax_service_method == 1) {
                            total_per_taxes[i2] += total_sidedish * percentage / 100;
                            total_per_taxes[i2] += (is_taxes == 1 ? (qty * price) * percentage / 100 : 0);
                        } else {
                            if (service == 1) {
                                total_services = subtotal * service_delivery / 100;
                                total_non_services += (is_taxes != 1 ? (qty * price) * percentage / 100 : 0);
                                total_services -= total_non_services;
                                subtotal_taxes += (is_taxes == 1 ? (qty * price) : 0);
                                total_per_taxes[i2] = total_services;
                            } else {
                                new_subtotal = subtotal_taxes + total_services;
                                total_taxes = new_subtotal * percentage / 100;
                                total_per_taxes[i2] = total_taxes;
                            }
                        }
                    });
                }
                    
            });
            // total=parseFloat(subtotal)+parseFloat(taxes);
            total = parseFloat(subtotal);
            html = '<tr>';
            html += '<td style="width:40%"></td>';
            html += '<td style="width:30%"><b>Subtotal</b></td>';
            html += '<td style="width:30%" class="tb-align-right">Rp ' + AppCashier.number_format(subtotal, 0) + '</td>';
            html += '</tr>';

            if (order_type == 1) {
                $("input#tax-price-dinein").each(function (i, e) {
                    percentage = $(this).attr("percentage");
                    tax_name = $(this).attr("tax-name");
                    html += '<tr>';
                    html += '<td style="width:40%"></td>';
                    html += '<td style="width:30%"><b>' + tax_name + ' (' + percentage + '%)</b></td>';
                    html += '<td style="width:30%" class="tb-align-right">Rp ' + AppCashier.number_format(total_per_taxes[i], 0) + '</td>';
                    html += '</tr>';
                    total += total_per_taxes[i];
                });
            } else if (order_type == 2) {
                $("input#tax-price-takeaway").each(function (i, e) {
                    percentage = $(this).attr("percentage");
                    tax_name = $(this).attr("tax-name");
                    html += '<tr>';
                    html += '<td style="width:40%"></td>';
                    html += '<td style="width:30%"><b>' + tax_name + ' (' + percentage + '%)</b></td>';
                    html += '<td style="width:30%" class="tb-align-right">Rp ' + AppCashier.number_format(total_per_taxes[i], 0) + '</td>';
                    html += '</tr>';
                    total += total_per_taxes[i];
                });
            } else {
                $("input#tax-price-delivery").each(function (i, e) {
                    percentage = $(this).attr("percentage");
                    tax_name = $(this).attr("tax-name");
                    html += '<tr>';
                    html += '<td style="width:40%"></td>';
                    html += '<td style="width:30%"><b>' + tax_name + ' (' + percentage + '%)</b></td>';
                    html += '<td style="width:30%" class="tb-align-right">Rp ' + AppCashier.number_format(total_per_taxes[i], 0) + '</td>';
                    html += '</tr>';
                    total += total_per_taxes[i];
                });
            }

            if ($(".order_type_reservation:checked").val() == 3) {
                reservation_delivery_cost_id = $("#reservation_delivery_cost_id").val();
                delivery_cost = parseFloat($("#reservation_delivery_cost_id option:selected").data("delivery_cost"));
                is_percentage = parseFloat($("#reservation_delivery_cost_id option:selected").data("is_percentage"));
                if (isNaN(delivery_cost)) delivery_cost = 0;
                if (is_percentage == 1) {
                	delivery_cost = parseFloat(subtotal) * (delivery_cost / 100);
                }
                html += '<tr>';
                html += '<td style="width:40%"></td>';
                html += '<td style="width:30%"><b>Ongkir</b></td>';
                html += '<td style="width:30%" class="tb-align-right">Rp ' + AppCashier.number_format(delivery_cost, 0) + '</td>';
                html += '</tr>';
                total += delivery_cost;
            }
            html += '<tr>';
            html += '<td style="width:40%"></td>';
            html += '<td style="width:30%"><b>Total</b></td>';
            html += '<td style="width:30%" class="tb-align-right">Rp ' + AppCashier.number_format(total, 0) + '</td>';
            html += '</tr>';
            $(".total-payment tbody").html(html);
        },
        pettyCashProcess: function () {
            var tablePettyCash = $('#dataTables-petty-cash').dataTable({
                "bProcessing": true,
                "bServerSide": true,
                "sServerMethod": "POST",
                "ajax": $('#dataProcessUrl').val(),
                "iDisplayLength": 10,
                "columns": [
                    {data: "name"},
                    {data: "description"},
                    {data: "amount"},
                    {data: "actions"}
                ],
                "columnDefs": [{
                    "targets": 3,
                    "orderable": false,
                    "bSearchable": false,
                    "class": 'center-tr'
                }],
                "order": [[0, "desc"]]
            });
            $(document).on("click", ".btn-petty-cash-delete", function () {
                user_confirmation = $(this).attr("feature_confirmation");
                if (user_confirmation == undefined)user_confirmation = "";
                if (user_confirmation != "") {
                    AppNav.showConfirmationPIN(user_confirmation, '#dataTables-petty-cash tbody tr:eq(' + $(this).parents("tr").index() + ') .btn-petty-cash-delete', "click", 1);
                    return false;
                }
                url = $(this).attr("href");
                AppCashier.confirm('Anda yakin ingin menghapus data ini?', function () {
                    window.location.href = url;
                });
                return false;
            });
            var tableBalance = $('#dataTables-balance').dataTable({
                "bProcessing": true,
                "bServerSide": true,
                "sServerMethod": "POST",
                "ajax": $('#dataProcessBalanceUrl').val(),
                "iDisplayLength": 10,
                "columns": [
                    {data: "name"},
                    {data: "description"},
                    {data: "amount"},
                    {data: "actions"}
                ],
                "columnDefs": [{
                    "targets": 3,
                    "orderable": false,
                    "bSearchable": false,
                    "class": 'center-tr'
                }],
                "order": [[0, "desc"]]
            });
        },
        reservationProcess: function () {

            var url = AppCashier.baseUrl + 'reservation/get_table_reservation';
            var request = $.ajax({
                type: 'POST',
                url: url,
                data: {
                    'book_date': $('#book_date').val(),
                }
            });
            request.done(function (msg) {

                if (msg != '') {
                    var parsedObject = JSON.parse(msg);
                    $('#ddl_table').html(parsedObject.msg);

                } else {
                    window.location.reload(true);
                }
            });

            is_delivery = $("#is_delivery").val();
            if (is_delivery == undefined)is_delivery = 0;
            is_takeaway = $("#is_takeaway").val();
            if (is_takeaway == undefined)is_takeaway = 0;
            url_autocomplete = AppCashier.baseUrl + "reservation/get_customer_auto_complete?is_delivery=" + is_delivery + "&is_takeaway=" + is_takeaway;
            options = {
                url: url_autocomplete,
                getValue: "customer_name",
                template: {
                    type: "description",
                    fields: {
                        description: "customer_address"
                    }
                },
                list: {
                    onSelectItemEvent: function () {
                        var selectedItemValue = $("#customer_name").getSelectedItemData();
                        $("#customer_address").val(selectedItemValue.customer_address);
                        // console.log(selectedItemValue.customer_phone);
                        $("#customer_phone,#phone").val(selectedItemValue.customer_phone);
                    },
                    match: {
                        enabled: true
                    }
                },
                theme: "plate-dark"
            };
            $("#reservation_delivery_cost_id").on("change", function () {
                AppCashier.reservationSubtotal();
            });
            $(".order_type_reservation").on('click', function () {
                value = $(this).val();
                $("#order_type").val(value);
                $("#reservation_delivery_cost_id").val("").hide();
                if (value == 1) {
                    $("#ddl_table").parents(".form-group:first").show();
                    $("#customer_count").parents(".form-group:first").show();
                } else {
                    $("#ddl_table").val("");
                    $("#ddl_table").parents(".form-group:first").hide();
                    $("#customer_count").parents(".form-group:first").hide();
                    if (value == 3) {
                        $("#reservation_delivery_cost_id").val("").show();
                    }
                }
                AppCashier.reservationSubtotal();
            });
            // $("#customer_name").easyAutocomplete(options);
            AppCashier.reservationSubtotal();
            $('#btn-save-reservation').on('click', function (e) {
                e.preventDefault();
                AppCashier.loadingOverlay.show();

                // if (AppTable.alreadyCompleted == '2' || AppTable.alreadyCompleted == '5') {
                //     return false;
                // }

                // if (AppTable.alreadyProcess == '1' && $('#is_already_process').val() == '1') {
                //     return false;
                // }
                var menuId = $('#menu_id_selected').val();
                ;
                var count = $('.count-order').val();

                // if (AppTable.isEdit)
                // {
                // menuId = $('#menu_order_id_selected').val();
                // var total_available = parseInt($('#temp_total_ordered').val())  + AppTable.totalStockAvailable ;

                // if(parseInt(total_available) < parseInt(count) && $('#zero_stock_order').val() == '0'){
                // AppTable.alert('Jumlah pesanan melebihi stok.');
                // return false;
                // }
                // count = count - $('#temp_total_ordered').val() ;
                // }
                // else{
                // menuId = $('#menu_id_selected').val();
                // if(AppTable.totalStockAvailable < parseInt(count) && $('#zero_stock_order').val() == '0'){
                // AppTable.alert('Jumlah pesanan melebihi stok.');
                // return false;
                // }
                // }

                // var orderId = $('#order_id').val();
                var option = '';
                var sideDish = '';
                var notes = $('.order-notes').val();

                //get side dish
                $('.chk_dish:checked').each(function () {
                    sideDish += $(this).val() + ",";
                });
                sideDish = sideDish.slice(0, -1);

                $('.options :selected').each(function (i, selected) {
                    option += $(selected).val() + ",";
                });
                option = option.slice(0, -1);


                var request = $.ajax({
                    type: "POST",
                    url: AppCashier.baseUrl + 'reservation/temp_save_order_menu',
                    data: {
                        menu_id: menuId,
                        count: count,
                        option: option,
                        side_dish: sideDish,
                        notes: notes,
                        outlet_id: $('.total-available-' + $('#menu_id_selected').val()).data("outlet")
                    }
                });
                request.done(function (msg) {
                    if (msg != '') {
                        var parsedObject = JSON.parse(msg);
                        if (parsedObject.status === true) {
                            $('.bill-table tbody').append(parsedObject.order_list);
                            AppCashier.reservationSubtotal();
                            // $('.total-payment tbody').html();
                            $('.popup-block').hide();
                            $('#form-input-order').get(0).reset();
                            $('#form-input-order').find('textarea').val('');
                            $(".remove_reservation_menu").unbind('click').bind('click', function () {
                                $(this).parents("tr").remove();
                                AppCashier.reservationSubtotal();
                            });
                        } else {
                            window.location.reload(true);
                        }
                    }
                    AppCashier.loadingOverlay.hide();
                });
            });
            $('.btn-cancel-dine-in, .btn-cancel').on('click', function (e) {
                e.preventDefault();
                if ($('#form-input-order').get(0) != undefined)$('#form-input-order').get(0).reset();
                $('#form-input-order').find('textarea').val('');
                $('.popup-block').hide();
            });
            var tableReservation = $('#dataTables-reservation').dataTable({
                "bProcessing": true,
                "bServerSide": true,
                "sServerMethod": "POST",
                "ajax": $('#dataProcessUrl').val(),
                "iDisplayLength": 10,
                "columns": [

                    {data: "customer_name"},
                    {data: "phone"},
                    {data: "book_date"},
                    {data: "status_reservasi"},
                    {data: "order_type"},
                    {data: "customer_count"},
                    {data: "book_note"},
                    {data: "table_name"},
                    {data: "down_payment"},
                    {data: "actions"}
                ],
                "columnDefs": [
                    {
                        "targets": 6,
                        "orderable": false,
                        "bSearchable": false,
                        "class": 'center-tr'
                    },
                    {
                        "targets": 7,
                        "orderable": false,
                        "bSearchable": false,
                        "class": 'center-tr'
                    },
                    {
                        "targets": 8,
                        "orderable": false,
                        "bSearchable": false,
                        "class": 'center-tr'
                    },
                    {
                        "targets": 9,
                        "orderable": false,
                        "bSearchable": false,
                        "class": 'center-tr'
                    }
                ],
                "order": [[2, "desc"]]


            });

            Date.prototype.addHours = function(hours) {
                this.setHours(this.getHours() + hours);
                return this;
            };

            var dateToday = new Date();

            $('#panel_calendar').datetimepicker({
                sideBySide: true,
                useCurrent: false,
                format: 'YYYY-MM-DD HH:mm',
                minDate: new Date(),
            });

            // $('#panel_calendar').data("DateTimePicker").update($('#book_date').val());


            $('#panel_calendar').on("dp.change", function (e) {

                var url = AppCashier.baseUrl + 'reservation/get_table_reservation';
                var request = $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                        'book_date': $('#book_date').val(),
                    }
                });
                request.done(function (msg) {

                    if (msg != '') {
                        var parsedObject = JSON.parse(msg);
                        $('#ddl_table').html(parsedObject.msg);

                    } else {
                        window.location.reload(true);
                    }
                });
            });


            $('#dataTables-reservation tbody').on('click', '.btn-reserv-delete', function (e) {
                user_confirmation = $(this).attr("feature_confirmation");
                if (user_confirmation == undefined)user_confirmation = "";
                if (user_confirmation != "") {
                    AppNav.showConfirmationPIN(user_confirmation, '#dataTables-reservation tbody tr:eq(' + $(this).parents("tr").index() + ') .btn-reserv-delete', "click", 1);
                    return;
                }
                var status = $(this).attr('data-status');
                var title = "";
                var reservationID = $(this).attr('data-id');

                title = 'Hapus Status Reserved';

                $('.btn-save-replace-reserv').attr('data-status', '3');

                var request = $.ajax({
                    type: 'POST',
                    url: AppCashier.baseUrl + 'table/get_reserved_note',
                    data: {}
                });
                request.done(function (msg) {
                    if (msg != '') {
                        var parsedObject = JSON.parse(msg);

                        $('#popup-reservation-note').show();
                        $('#reservation-note').html(parsedObject.msg);
                        $('#popup-reservation-note').find('.title-name').text(title);

                        $('#custom-note').on('click', function (e) {
                            e.preventDefault();
                            $('#custom').prop("checked", true);
                            $('#custom').attr('checked', 'checked');
                        });
                    } else {
                        window.location = AppCashier.baseUrl;
                    }
                });

                $('.btn-close-reserv').on('click', function (e) {
                    e.preventDefault();
                    $('.popup-block').hide();
                });


                $('.btn-save-replace-reserv').unbind('click').bind('click', function (e) {
                    e.preventDefault();

                    var request = $.ajax({
                        type: 'POST',
                        url: AppCashier.baseUrl + 'table/update_reservation_status',
                        data: {
                            reservation_id: reservationID,
                            status: 3,
                            note: $('#form-reservation-note').serialize()
                        }
                    });
                    request.done(function (msg) {
                        var parsedObject = JSON.parse(msg);
                        if (parsedObject.status == true) {
                            $('#popup-reservation-note').hide();
                            tableReservation.api().ajax.reload();
                            $(".alert-success").html("Berhasil Menghapus Data");

                        } else {
                            AppCashier.alert(parsedObject.msg);
                        }

                    });
                    request.fail(function () {
                        window.location.reload(true);

                    });


                });


            });

            // $('#customer_name').bind('keyup blur',function(){
            // var node = $(this);
            // node.val(node.val().replace(/[^a-zA-Z ]/g,'') );
            // });
            // $('#phone').bind('keypress',function(e){
            // if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            // return false;
            // }
            // });
            // $('#customer_count').bind('keypress',function(e){
            // if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            // return false;
            // }
            // });

        }, //end reservation process
        updateDiscount: function () {
            var discount = $("#total-payment-left").find("#discount-percent");
            var discountPrice = $("#total-payment-left").find("#discount-total");

            if (parseInt(AppCashier.subtotalLeft) == 0) {
                var total_discount_percent = 0;
                var total_discount_price = 0;

            } else {
                var total_discount_percent = parseInt(AppCashier.discountTotal) / AppCashier.subtotalLeft;
                var total_discount_price = AppCashier.discountTotal;
            }
            if (total_discount_percent * 100 > 100) total_discount_percent = 1;
            $(discountPrice).html(AppCashier.moneyFormat(parseInt(total_discount_price), "Rp "));
            $(discount).html(Math.ceil(total_discount_percent * 100));


            var discountRight = $("#total-payment-right").find("#discount-percent");
            var discountPriceRight = $("#total-payment-right").find("#discount-total");

            if (parseInt(AppCashier.subtotalRight) == 0) {
                var total_discount_percentR = 0;
                var total_discount_priceR = 0;

            } else {
                var total_discount_percentR = parseInt(AppCashier.discountTotal) / AppCashier.subtotalRight;
                var total_discount_priceR = AppCashier.discountTotal;
            }
            if (total_discount_percentR * 100 > 100) total_discount_percentR = 1;
            $(discountPriceRight).html(AppCashier.moneyFormat(parseInt(total_discount_priceR), "Rp "));
            $(discountRight).html(Math.ceil(total_discount_percentR * 100));


            if (AppCashier.subtotalRight > 0) {
                AppCashier.activateNumberButton(true);
            } else {

                AppCashier.disableNumberButton(true);
            }
        },
        setCreditPayment: function () {
            if ($("#ddl_bank").val() == undefined || $("#ddl_bank").val() == 0 || $("#ddl_bank").val() == "") {
                AppCashier.alert("Pilih Nama Bank terlebih dahulu");
                return;
            }
            if ($("#value").val().length <= 0) {
                AppCashier.alert("Nomor Kartu Kredit Harus Diisi");
                return;
            }
             if($("#value").val().length > 16){
             AppCashier.alert("Nomor Kartu Kredit Harus 16 digit");
             return;
             }
            var popup = $('.popup-input');

            AppCashier.creditPayment.accountId = popup.find("#ddl_bank option:selected").attr('data-account-id');
            AppCashier.creditPayment.bankId = popup.find("#ddl_bank option:selected").val();
            AppCashier.creditPayment.code = $("#value").val();
            AppCashier.creditPayment.cardTypeId = $("#ddl_bank_account_card").val();

            if ($("#promo_cc").val() == 0) {
                // $(".promo-discount").remove();
                // AppCashier.promoDiscountName = "";
                // AppCashier.setPromoDiscount(AppCashier.promoDiscountName, true);
                // AppCashier.categoryPromoDiscounts = [];
                // AppCashier.getSubtotal();
                // AppCashier.setSubTotal();
                // AppCashier.setGrandTotal();
                // AppCashier.setKembalian();
                // AppCashier.updateTaxInfo();
            } else {
                // console.log("--------------------------");
                // console.log(AppCashier.promoDiscountName);
                // console.log("--------------------------");

                $(".promo-cc-right, .promo-cc-left").show();
                AppCashier.promoCcName = $("#promo_cc :selected").html();
                var url = AppCashier.baseUrl + 'cashier/get_detail_promo_menu';
                var category_id = $("#category_id").val();
                var promo_id = $("#promo_cc").val();
                var request = $.ajax({
                    type: 'POST',
                    url: url,

                    data: {
                        'promo_id': promo_id
                    }
                });
                request.done(function (msg) {
                    var parsedObject = JSON.parse(msg);
                    console.log("GET DATA DETAIL PROMO MENU");

                    AppCashier.categoryPromoCc = [];
                    for (var i = 0; i < parsedObject.length; i++) {
                        AppCashier.categoryPromoCc.push(parsedObject[i]);
                    }
                    ;

                    $("#promo_cc_id").val(promo_id);
                    AppCashier.setPromoCc(AppCashier.promoCcName, false);

                    AppCashier.complimentPayment.amount = 0;
                    AppCashier.setSubTotal();
                    AppCashier.setGrandTotal();
                    AppCashier.setKembalian();
                    AppCashier.updateTaxInfo();
                });
                request.fail(function (jqXHR, textStatus) {


                });
                request.always(function () {
                });
            }

            popup.hide();
        },

        setDebitPayment: function () {
            if ($("#ddl_bank").val() == undefined || $("#ddl_bank").val() == 0 || $("#ddl_bank").val() == "") {
                AppCashier.alert("Pilih Nama Bank terlebih dahulu");
                return;
            }
            if ($("#value").val().length <= 0) {
                AppCashier.alert("Nomor Kartu Debit Harus Diisi");
                return;
            }
             if($("#value").val().length > 16){
             AppCashier.alert("Nomor Kartu Debit Harus 16 digit");
             return;
             }
             
            var popup = $('.popup-input');
            AppCashier.debitPayment.accountId = popup.find("#ddl_bank option:selected").attr('data-account-id');
            AppCashier.debitPayment.bankId = popup.find("#ddl_bank option:selected").val();
            AppCashier.debitPayment.code = $("#value").val();
            AppCashier.debitPayment.cardTypeId = $("#ddl_bank_account_card").val();
            popup.hide();
        },

        setFlazzPayment: function () {
            if ($("#ddl_flazz_bank").val() == undefined || $("#ddl_flazz_bank").val() == 0 || $("#ddl_flazz_bank").val() == "") {
                AppCashier.alert("Pilih Nama Bank terlebih dahulu");
                return;
            }
            if ($("#value_flazz").val().length <= 0) {
                AppCashier.alert("Nomor Kartu Flazz Harus Diisi");
                return;
            }
            if($("#value_flazz").val().length > 16){
            AppCashier.alert("Nomor Kartu Debit Harus 16 digit");
            return;
            }
            var popup = $('.popup-input-flazz');
            AppCashier.flazzPayment.accountId = popup.find("#ddl_flazz_bank option:selected").attr('data-account-id');
            AppCashier.flazzPayment.bankId = popup.find("#ddl_flazz_bank option:selected").val();
            AppCashier.flazzPayment.code = $("#value_flazz").val();
            AppCashier.flazzPayment.cardTypeId = $("#ddl_flazz_bank_account_card").val();
            popup.hide();
        },
        newPaymentProcess: function (inputTextElement) {
            down_payment = parseFloat($("#reservation_id").attr("down_payment"));
            if (isNaN(down_payment))down_payment = 0;
            if (AppCashier.grandTotalRight === 0 && down_payment == 0) {
                AppCashier.alert("Jumlah Order Yang akan Di bayar tidak ada, silahkan pilih menu order");
                AppCashier.resetInputPayment();
                return;
            }
            var inputPaymentTemp = inputTextElement.val().slice(3).replace(/\,/g, '');
            if (inputPaymentTemp.length === 0) inputPaymentTemp = 0;


            switch (AppCashier.paymentType) {
                case 1:
                    if (AppCashier.voucherPayment.amount > 0 && AppCashier.voucherPayment.amount > App.grandTotalRight) return;
                    AppCashier.cashPayment.amount = parseInt(inputPaymentTemp);
                    AppCashier.customerCashPayment = AppCashier.cashPayment.amount;
                    $(".cashPayment").remove();
                    var html = "<tr class='cashPayment'><td> </td><td><b>" + (AppCashier.cashPayment.type == 1 ? "Cash" : "BON") + "</b></td>"
                        + "<td class='tb-align-right'>" + AppCashier.moneyFormat(AppCashier.cashPayment.amount, 'Rp ') + "</td></tr>";

                    break;
                case 2:
                    if (AppCashier.debitPayment.code === 0) return;
                    //check bayaran kredit tidak boleh lebih dari grand total
                    var receivable = parseInt(inputPaymentTemp) + AppCashier.creditPayment.amount + AppCashier.flazzPayment.amount + AppCashier.complimentPayment.hpp + AppCashier.cashPayment.amount;
                    if (receivable > AppCashier.grandTotalRight) {
                        AppCashier.alert("Bayaran Kartu Tidak Boleh Lebih Dari Grand Total");
                        return;
                    }

                    AppCashier.debitPayment.amount = parseInt(inputPaymentTemp);

                    $(".debitPayment").remove();
                    var html = "<tr class='debitPayment'><td> </td><td><b>Debit</b></td>"
                        + "<td class='tb-align-right'>" + AppCashier.moneyFormat(AppCashier.debitPayment.amount, 'Rp ') + "</td></tr>";

                    break;
                case 3:
                    if (AppCashier.creditPayment.code === 0) return;
                    //check bayaran kredit tidak boleh lebih dari grand total
                    var receivable = AppCashier.debitPayment.amount + AppCashier.flazzPayment.amount + parseInt(inputPaymentTemp) + AppCashier.complimentPayment.hpp + AppCashier.cashPayment.amount;
                    if (receivable > AppCashier.grandTotalRight) {
                        AppCashier.alert("Bayaran Kartu Tidak Boleh Lebih Dari Grand Total");
                        return;
                    }

                    AppCashier.creditPayment.amount = parseInt(inputPaymentTemp);

                    $(".creditPayment").remove();
                    var html = "<tr class='creditPayment'><td> </td><td><b>Credit</b></td>"
                        + "<td class='tb-align-right'>" + AppCashier.moneyFormat(AppCashier.creditPayment.amount, 'Rp ') + "</td></tr>";

                    break;
                case 11:
                    if (AppCashier.flazzPayment.code === 0) return;
                    //check bayaran flazz tidak boleh lebih dari grand total
                    var receivable = AppCashier.debitPayment.amount + AppCashier.creditPayment.amount + parseInt(inputPaymentTemp) + AppCashier.complimentPayment.hpp + AppCashier.cashPayment.amount;
                    if (receivable > AppCashier.grandTotalRight) {
                        AppCashier.alert("Bayaran Kartu Tidak Boleh Lebih Dari Grand Total");
                        return;
                    }

                    AppCashier.flazzPayment.amount = parseInt(inputPaymentTemp);

                    $(".flazzPayment").remove();
                    var html = "<tr class='flazzPayment'><td> </td><td><b>Flazz</b></td>"
                        + "<td class='tb-align-right'>" + AppCashier.moneyFormat(AppCashier.flazzPayment.amount, 'Rp ') + "</td></tr>";

                    break;
            }

            $(".payment-method").after(html);
            // console.log(AppCashier.cashPayment, AppCashier.debitPayment, AppCashier.creditPayment, AppCashier.flazzPayment);
            AppCashier.customerpay = AppCashier.cashPayment.amount + AppCashier.debitPayment.amount + AppCashier.creditPayment.amount + AppCashier.voucherPayment.amount + AppCashier.flazzPayment.amount + AppCashier.downPaymentBill.amount;

            AppCashier.setPaymentBill();
            AppCashier.setGrandTotal();
            if (AppCashier.paymentType != 5) {
                AppCashier.setKembalian();
            }
			
			$('.btn-preview-bill').prop('disabled', true);
        },
        getDataCategoryLeft: function () {
            var arrayCategoryLeft = [];
            for (var i = 0; i < AppCashier.leftDataOrder.length; i++) {

                arrayCategoryLeft.push(AppCashier.leftDataOrder[i].category_id);

            }
            ;

            return arrayCategoryLeft;
        },
        getDataCategoryRight: function () {
            var arrayCategoryRight = [];
            for (var i = 0; i < AppCashier.rightDataOrder.length; i++) {

                arrayCategoryRight.push(AppCashier.rightDataOrder[i].category_id);

            }
            ;

            return arrayCategoryRight;
        },
        array_unique: function (arr) {
            var result = [];
            for (var i = 0; i < arr.length; i++) {
                if (result.indexOf(arr[i]) == -1) {
                    result.push(arr[i]);
                }
            }
            return result;
        },

        setPromoDiscount: function (promo_name, isReset) {
            // console.log(promo_name);
            console.log("SET PROMO discount");
            $('#promo_id').prop('disabled', false);


            $(".promo-compliment-right,.promo-compliment-left").remove();
            var totLeftPromoDisc = 0;
            var totRightPromoDisc = 0;
            var discountPercentage = 0;


            if (AppCashier.categoryPromoDiscounts.length > 0) {
                if (AppCashier.categoryPromoDiscounts[0].menu_id != undefined) {
                    //left table
                    for (var indexLeft = 0; indexLeft < AppCashier.leftDataOrder.length; indexLeft++) {
                        for (var iLeft = 0; iLeft < AppCashier.categoryPromoDiscounts.length; iLeft++) {
                            AppCashier.leftDataOrder[indexLeft].product_price = AppCashier.leftDataOrder[indexLeft].origin_price;
                            var dataOrder = AppCashier.leftDataOrder[indexLeft];
                            var dataCatPromo = AppCashier.categoryPromoDiscounts[iLeft];
                            var origin_price = AppCashier.leftDataOrder[indexLeft].origin_price;
                            var product_price = AppCashier.leftDataOrder[indexLeft].product_price;

                            if (parseInt(dataOrder.menu_id) === parseInt(dataCatPromo.menu_id)) {
                                var element = $("#bill-table-left").find("tbody  > tr#" + dataOrder.product_id + "> td.price-menu");
                                if (!isReset) {
                                    totLeftPromoDisc += ( origin_price * parseInt(dataCatPromo.discount) / 100) * dataOrder.product_amount;
                                    break;
                                } else {
                                    AppCashier.leftDataOrder[indexLeft].product_price = origin_price;

                                }
                            }
                        }
                    }
                    //right table
                    for (var indexRight = 0; indexRight < AppCashier.rightDataOrder.length; indexRight++) {
                        for (var iLeft = 0; iLeft < AppCashier.categoryPromoDiscounts.length; iLeft++) {
                            AppCashier.rightDataOrder[indexRight].product_price = AppCashier.rightDataOrder[indexRight].origin_price;
                            var dataOrder = AppCashier.rightDataOrder[indexRight];
                            var dataCatPromo = AppCashier.categoryPromoDiscounts[iLeft];
                            var origin_price = AppCashier.rightDataOrder[indexRight].origin_price;
                            var product_price = AppCashier.rightDataOrder[indexRight].product_price;
                            if (parseInt(dataOrder.menu_id) === parseInt(dataCatPromo.menu_id)) {
                                var element = $("#bill-table-right").find("tbody  > tr#" + dataOrder.product_id + "> td.price-menu");
                                if (!isReset) {

                                    totRightPromoDisc += ( origin_price * parseInt(dataCatPromo.discount) / 100) * dataOrder.product_amount;
                                    discountPercentage = dataCatPromo.discount;
                                    break;
                                } else {
                                    AppCashier.rightDataOrder[indexRight].product_price = origin_price;
                                    element.html(
                                        AppCashier.moneyFormat(AppCashier.rightDataOrder[indexRight].origin_price * dataOrder.product_amount, "Rp "));
                                }
                            }
                        }
                    }
                } else {
                    //left table
                    for (var indexLeft = 0; indexLeft < AppCashier.leftDataOrder.length; indexLeft++) {
                        for (var iLeft = 0; iLeft < AppCashier.categoryPromoDiscounts.length; iLeft++) {
                            AppCashier.leftDataOrder[indexLeft].product_price = AppCashier.leftDataOrder[indexLeft].origin_price;
                            var dataOrder = AppCashier.leftDataOrder[indexLeft];
                            var dataCatPromo = AppCashier.categoryPromoDiscounts[iLeft];
                            var origin_price = AppCashier.leftDataOrder[indexLeft].origin_price;
                            var product_price = AppCashier.leftDataOrder[indexLeft].product_price;

                            if (parseInt(dataOrder.category_id) === parseInt(dataCatPromo.category_id)) {
                                var element = $("#bill-table-left").find("tbody  > tr#" + dataOrder.product_id + "> td.price-menu");
                                if (!isReset) {
                                    totLeftPromoDisc += ( origin_price * parseInt(dataCatPromo.discount) / 100) * dataOrder.product_amount;
                                    break;
                                } else {
                                    AppCashier.leftDataOrder[indexLeft].product_price = origin_price;

                                }
                            }
                        }
                    }
                    //right table
                    for (var indexRight = 0; indexRight < AppCashier.rightDataOrder.length; indexRight++) {
                        for (var iLeft = 0; iLeft < AppCashier.categoryPromoDiscounts.length; iLeft++) {
                            AppCashier.rightDataOrder[indexRight].product_price = AppCashier.rightDataOrder[indexRight].origin_price;
                            var dataOrder = AppCashier.rightDataOrder[indexRight];
                            var dataCatPromo = AppCashier.categoryPromoDiscounts[iLeft];
                            var origin_price = AppCashier.rightDataOrder[indexRight].origin_price;
                            var product_price = AppCashier.rightDataOrder[indexRight].product_price;
                            if (parseInt(dataOrder.category_id) === parseInt(dataCatPromo.category_id)) {
                                var element = $("#bill-table-right").find("tbody  > tr#" + dataOrder.product_id + "> td.price-menu");
                                if (!isReset) {

                                    totRightPromoDisc += ( origin_price * parseInt(dataCatPromo.discount) / 100) * dataOrder.product_amount;
                                    discountPercentage = dataCatPromo.discount;
                                    break;
                                } else {
                                    AppCashier.rightDataOrder[indexRight].product_price = origin_price;
                                    element.html(
                                        AppCashier.moneyFormat(AppCashier.rightDataOrder[indexRight].origin_price * dataOrder.product_amount, "Rp "));
                                }
                            }
                        }
                    }
                }

            }
            
            if (AppCashier.rightDataOrder.length > 0) {
                if (isReset === false) {
                    if ($("#promo_id").val() == 0) {
                        $(".promo-discount-right").remove();
                        var html = "<tr class='promo-discount-right'><td> </td><td><b></b></td><td class='tb-align-right'></td></tr>";
                        $("#total-payment-right").find(".discount-order-list").after(html);
                    } else {
                        $(".promo-discount-right").remove();
                        var html = "<tr class='promo-discount-right'><td> </td><td><b>Promo " + promo_name + ' ' + discountPercentage + "%</b></td><td class='tb-align-right'>" + AppCashier.moneyFormat(totRightPromoDisc, "Rp ") + "</td></tr>";
                        $("#total-payment-right").find(".discount-order-list").after(html);
                    }                        
                }
            } else {
                $(".promo-discount-right").remove();
                var html = "<tr class='promo-discount-right'><td> </td><td><b></b></td><td class='tb-align-right'></td></tr>";
                $("#total-payment-right").find(".discount-order-list").after(html);
            }

            AppCashier.totRightPromoDisc = totRightPromoDisc;
            AppCashier.totLeftPromoDisc = totLeftPromoDisc;


        },

        setPromoCc: function (promo_name, isReset) {
            // console.log(promo_name);
            console.log("SET PROMO CC");
            $('#promo_cc').prop('disabled', false);

            $(".promo-compliment-right,.promo-compliment-left").remove();
            var totLeftPromoCc = 0;
            var totRightPromoCc = 0;
            var discountPercentage = 0;

            if (AppCashier.categoryPromoCc.length > 0) {
                priceText = $('#subtotal-price').attr('data-price');
                price = priceText.replace(/\./g, '');
                subtotal = parseInt(price);

                if (subtotal < AppCashier.categoryPromoCc[0].min_order) {
                    if (AppCashier.rightDataOrder.length > 0) {
                        AppCashier.alert("Promo tidak dapat digunakan. Minimum order tidak mencukupi.");
                        promo_name = '';
                        discountPercentage = 0;
                        totRightPromoCc = 0;
                    }                        
                } else {
                    if (AppCashier.categoryPromoCc[0].menu_id != undefined) {
                        //left table
                        for (var indexLeft = 0; indexLeft < AppCashier.leftDataOrder.length; indexLeft++) {
                            for (var iLeft = 0; iLeft < AppCashier.categoryPromoCc.length; iLeft++) {
                                AppCashier.leftDataOrder[indexLeft].product_price = AppCashier.leftDataOrder[indexLeft].origin_price;
                                var dataOrder = AppCashier.leftDataOrder[indexLeft];
                                var dataCatPromo = AppCashier.categoryPromoCc[iLeft];
                                var origin_price = AppCashier.leftDataOrder[indexLeft].origin_price;
                                var product_price = AppCashier.leftDataOrder[indexLeft].product_price;

                                if (parseInt(dataOrder.menu_id) === parseInt(dataCatPromo.menu_id)) {
                                    var element = $("#bill-table-left").find("tbody  > tr#" + dataOrder.product_id + "> td.price-menu");
                                    if (!isReset) {
                                        totLeftPromoCc += ( origin_price * parseInt(dataCatPromo.discount) / 100) * dataOrder.product_amount;
                                        break;
                                    } else {
                                        AppCashier.leftDataOrder[indexLeft].product_price = origin_price;

                                    }
                                }
                            }
                        }
                        //right table
                        for (var indexRight = 0; indexRight < AppCashier.rightDataOrder.length; indexRight++) {
                            for (var iLeft = 0; iLeft < AppCashier.categoryPromoCc.length; iLeft++) {
                                AppCashier.rightDataOrder[indexRight].product_price = AppCashier.rightDataOrder[indexRight].origin_price;
                                var dataOrder = AppCashier.rightDataOrder[indexRight];
                                var dataCatPromo = AppCashier.categoryPromoCc[iLeft];
                                var origin_price = AppCashier.rightDataOrder[indexRight].origin_price;
                                var product_price = AppCashier.rightDataOrder[indexRight].product_price;
                                if (parseInt(dataOrder.menu_id) === parseInt(dataCatPromo.menu_id)) {
                                    var element = $("#bill-table-right").find("tbody  > tr#" + dataOrder.product_id + "> td.price-menu");
                                    if (!isReset) {

                                        totRightPromoCc += ( origin_price * parseInt(dataCatPromo.discount) / 100) * dataOrder.product_amount;
                                        discountPercentage = dataCatPromo.discount;
                                        break;
                                    } else {
                                        AppCashier.rightDataOrder[indexRight].product_price = origin_price;
                                        element.html(
                                            AppCashier.moneyFormat(AppCashier.rightDataOrder[indexRight].origin_price * dataOrder.product_amount, "Rp "));
                                    }
                                }
                            }
                        }
                    } else {
                        //left table
                        for (var indexLeft = 0; indexLeft < AppCashier.leftDataOrder.length; indexLeft++) {
                            for (var iLeft = 0; iLeft < AppCashier.categoryPromoCc.length; iLeft++) {
                                AppCashier.leftDataOrder[indexLeft].product_price = AppCashier.leftDataOrder[indexLeft].origin_price;
                                var dataOrder = AppCashier.leftDataOrder[indexLeft];
                                var dataCatPromo = AppCashier.categoryPromoCc[iLeft];
                                var origin_price = AppCashier.leftDataOrder[indexLeft].origin_price;
                                var product_price = AppCashier.leftDataOrder[indexLeft].product_price;

                                if (parseInt(dataOrder.category_id) === parseInt(dataCatPromo.category_id)) {
                                    var element = $("#bill-table-left").find("tbody  > tr#" + dataOrder.product_id + "> td.price-menu");
                                    if (!isReset) {
                                        totLeftPromoCc += ( origin_price * parseInt(dataCatPromo.discount) / 100) * dataOrder.product_amount;
                                        break;
                                    } else {
                                        AppCashier.leftDataOrder[indexLeft].product_price = origin_price;
                                    }
                                }
                            }
                        }
                        //right table
                        for (var indexRight = 0; indexRight < AppCashier.rightDataOrder.length; indexRight++) {
                            for (var iLeft = 0; iLeft < AppCashier.categoryPromoCc.length; iLeft++) {
                                AppCashier.rightDataOrder[indexRight].product_price = AppCashier.rightDataOrder[indexRight].origin_price;
                                var dataOrder = AppCashier.rightDataOrder[indexRight];
                                var dataCatPromo = AppCashier.categoryPromoCc[iLeft];
                                var origin_price = AppCashier.rightDataOrder[indexRight].origin_price;
                                var product_price = AppCashier.rightDataOrder[indexRight].product_price;
                                if (parseInt(dataOrder.category_id) === parseInt(dataCatPromo.category_id)) {
                                    var element = $("#bill-table-right").find("tbody  > tr#" + dataOrder.product_id + "> td.price-menu");
                                    if (!isReset) {

                                        totRightPromoCc += ( origin_price * parseInt(dataCatPromo.discount) / 100) * dataOrder.product_amount;
                                        discountPercentage = dataCatPromo.discount;
                                        break;
                                    } else {
                                        AppCashier.rightDataOrder[indexRight].product_price = origin_price;
                                        element.html(
                                            AppCashier.moneyFormat(AppCashier.rightDataOrder[indexRight].origin_price * dataOrder.product_amount, "Rp "));
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if (AppCashier.rightDataOrder.length > 0) {
                if (isReset === false) {
                    if (totRightPromoCc <= 0) {
                        $(".promo-cc-right").remove();
                        var html = "<tr class='promo-cc-right'><td> </td><td><b></b></td><td class='tb-align-right'></td></tr>";
                        $("#total-payment-right").find(".discount-order-list").after(html);
                    } else {
                        $(".promo-cc-right").remove();
                        var html = "<tr class='promo-cc-right'><td> </td><td><b>Promo " + promo_name + ' ' + discountPercentage + "%</b></td><td class='tb-align-right'>" + AppCashier.moneyFormat(totRightPromoCc, "Rp ") + "</td></tr>";
                        $("#total-payment-right").find(".discount-order-list").after(html);
                    }                        
                }
            } else {
                $(".promo-cc-right").remove();
                var html = "<tr class='promo-cc-right'><td> </td><td><b></b></td><td class='tb-align-right'></td></tr>";
                $("#total-payment-right").find(".discount-order-list").after(html);
            }

            AppCashier.totRightPromoCc = totRightPromoCc;
            AppCashier.totLeftPromoCc = totLeftPromoCc;


        },
        resetInputPayment: function () {
            var inputPayment = $("#input-payment");
            var amount;
            switch (AppCashier.paymentType) {
                case 1:
                    amount = AppCashier.cashPayment.amount;
                    break;
                case 2:
                    amount = AppCashier.debitPayment.amount;
                    break;
                case 3:
                    amount = AppCashier.creditPayment.amount;
                    break;
                case 5:
                    amount = AppCashier.customerpay;
                    break;
                case 11:
                    amount = AppCashier.flazzPayment.amount;
                    break;
            }


            if (amount == 0) inputPayment.val('');
            else inputPayment.val(amount);

            inputPayment.trigger('change');

        },
        setKembalian: function () {
            AppCashier.setGrandTotal();
            var receivableVoucher = AppCashier.voucherPayment.amount - AppCashier.grandTotalRight;
            if (AppCashier.pendingbillEmployee.amount > 0) AppCashier.customerpay = AppCashier.grandTotalRight;

            if (AppCashier.paymentType == 5 || AppCashier.complimentPayment.hpp > 0) {
                var payment = (AppCashier.complimentPayment.hpp + AppCashier.cashPayment.amount + AppCashier.creditPayment.amount + AppCashier.debitPayment.amount + AppCashier.flazzPayment.amount);
                var kembalian = payment - AppCashier.grandTotalRight;
                if (kembalian == AppCashier.customerpay) {
                    kembalian = 0;
                    AppCashier.cashPayment.amount = 0;
                    AppCashier.creditPayment.amount = 0;
                    AppCashier.debitPayment.amount = 0;
                    AppCashier.flazzPayment.amount = 0;
                    $(".cashPayment").remove();
                    $(".debitPayment").remove();
                    $(".creditPayment").remove();
                    $(".flazzPayment").remove();
                }
                AppCashier.paymentBalance = kembalian;
                $(".payment-sisa").html(AppCashier.moneyFormat(AppCashier.paymentBalance, "Rp "));
            } else {
                var kembalian = AppCashier.customerpay - AppCashier.grandTotalRight;
                AppCashier.paymentBalance = kembalian;
            }

            if (AppCashier.downPaymentBill.amount > 0) {
                var kembalian = AppCashier.downPaymentBill.amount - AppCashier.grandTotalRight;
                AppCashier.paymentBalance = kembalian;

                if (AppCashier.customerpay >= AppCashier.grandTotalRight) {
                    var kembalian = AppCashier.customerpay - AppCashier.grandTotalRight;
                    AppCashier.paymentBalance = kembalian;
                }
            }

            if (kembalian >= 0) AppCashier.paymentBalance = 0;
            $(".payment-sisa").html(AppCashier.moneyFormat(AppCashier.paymentBalance, "Rp "));

            if (receivableVoucher > 0 && receivableVoucher > AppCashier.grandTotalRight) kembalian -= receivableVoucher;
            if (AppCashier.grandTotalRight <= 0 || kembalian <= 0) kembalian = 0;

            // if (AppCashier.complimentPayment.amount > 0) kembalian = 0;
            $("#total-payment-right").find(".payment-text").html(AppCashier.moneyFormat(kembalian, "Rp "));

            if (AppCashier.paymentBalance < 0) {
                $('#done-payment').prop('disabled', true);
            } else {
                if (AppCashier.rightDataOrder.length > 0)
                    $('#done-payment').prop('disabled', false);
            }
            AppCashier.kembalian = kembalian;
        },
        setPaymentBill: function () {
            switch (AppCashier.paymentType) {
                case 1:
                    $(".cashPayment").remove();
                    if (AppCashier.cashPayment.amount <= 0) return;
                    var html = "<tr class='cashPayment'><td> </td><td><b>Cash</b></td>"
                        + "<td class='tb-align-right'>" + AppCashier.moneyFormat(AppCashier.cashPayment.amount, 'Rp ') + "</td></tr>";

                    break;
                case 2:
                    $(".debitPayment").remove();
                    if (AppCashier.debitPayment.amount <= 0 || AppCashier.debitPayment.code === 0) return;
                    var html = "<tr class='debitPayment'><td> </td><td><b>Debit</b> (" + AppCashier.debitPayment.code + ")</td>"
                        + "<td class='tb-align-right'>" + AppCashier.moneyFormat(AppCashier.debitPayment.amount, 'Rp ') + "</td></tr>";

                    break;
                case 3:

                    $(".creditPayment").remove();
                    if (AppCashier.creditPayment.amount <= 0 || AppCashier.creditPayment.code === 0) return;
                    var html = "<tr class='creditPayment'><td> </td><td><b>Credit</b>(" + AppCashier.creditPayment.code + ")</td>"
                        + "<td class='tb-align-right'>" + AppCashier.moneyFormat(AppCashier.creditPayment.amount, 'Rp ') + "</td></tr>";

                    break;
                case 4:

                    $(".voucherPayment").remove();
                    if (AppCashier.voucherPayment.amount <= 0 || AppCashier.voucherPayment.code === 0) return;
                    var html = "<tr class='voucherPayment'><td> </td><td><b>Voucher</b>(" + AppCashier.voucherPayment.code + ")</td>"
                        + "<td class='tb-align-right'>" + AppCashier.moneyFormat(AppCashier.voucherPayment.amount, 'Rp ') + "</td></tr>";
					
                    break;	
                case 6:

                    $(".pendingbill").remove();


                    var html = "<tr class='pendingbill'><td> </td><td><b>Pending</b></td>"
                        + "<td class='tb-align-right'>" + AppCashier.moneyFormat(AppCashier.pendingbill.amount, 'Rp ') + "</td></tr>";
                    AppCashier.paymentType = 1;
                    break;
                case 7:

                    $(".pendingbillEmployee").remove();


                    var html = "<tr class='pendingbillEmployee'><td> </td><td><b>Pending Karyawan (" + AppCashier.pendingbillEmployee.code_name + ")</b></td>"
                        + "<td class='tb-align-right'>" + AppCashier.moneyFormat(AppCashier.pendingbillEmployee.amount, 'Rp ') + "</td></tr>";
                    AppCashier.paymentType = 1;
                    break;
                case 8:

                    $(".pendingbill").remove();


                    var html = "<tr class='pendingbill'><td> </td><td><b>Sisa</b></td>"
                        + "<td class='tb-align-right'>" + AppCashier.moneyFormat(-1 * AppCashier.pendingbill.amount, 'Rp ') + "</td></tr>";
                    AppCashier.paymentType = 1;
                    break;
                case 11:

                    $(".flazzPayment").remove();
                    if (AppCashier.flazzPayment.amount <= 0 || AppCashier.flazzPayment.code === 0) return;
                    var html = "<tr class='flazzPayment'><td> </td><td><b>Flazz</b>(" + AppCashier.flazzPayment.code + ")</td>"
                        + "<td class='tb-align-right'>" + AppCashier.moneyFormat(AppCashier.flazzPayment.amount, 'Rp ') + "</td></tr>";

                    break;
            }

            $("#total-payment-right").find(".payment-method").before(html);
        },
        resetPayment: function () {
            $(".cashPayment, .debitPayment, .creditPayment, .voucherPayment, .discount-member, .pendingbill").remove();
            AppCashier.cashPayment.amount = 0;
            AppCashier.debitPayment.amount = 0;
            AppCashier.creditPayment.amount = 0;
            AppCashier.flazzPayment.amount = 0;
            AppCashier.voucherPayment.amount = 0;
            AppCashier.complimentPayment.amount = 0;
            AppCashier.complimentPayment.hpp = 0;
            AppCashier.complimentPayment.limit = 0;
            AppCashier.complimentPayment.is_cogs = 0;
            AppCashier.pendingbill.amount = 0;
            AppCashier.customerpay = 0;
            AppCashier.discountMemberId = 0;
            AppCashier.discountMember = 0;
            AppCashier.updateDiscountMember();
            AppCashier.grandTotalRight = 0;
            AppCashier.subtotalTaxRight = 0;
            AppCashier.subtotalRight = 0;
            AppCashier.orginTotalRight = 0;
            AppCashier.totRightPromoDisc = 0;
            AppCashier.totRightPromoCc = 0;

            $('#text-code').text("");
        },
        resetPaymentUI: function () {
            $(".cashPayment, .debitPayment, .creditPayment, .flazzPayment, .voucherPayment, .discount-member, .pendingbill,.pendingbillEmployee").remove();
            AppCashier.pendingbillEmployee.amount = 0;
            AppCashier.pendingbill.amount = 0;
        },
        setDiscountPendingBill: function () {

            //fungsi untuk dinamis pending bill dan discount
            $(".pendingbillEmployee").remove();

            if (AppCashier.pendingbillEmployee.amount > 0) {
                AppCashier.disableNumberButton(true);
                AppCashier.pendingbillEmployee.amount = AppCashier.grandTotalRight;
                AppCashier.pendingbillEmployee.code = $("#profile option:selected").val();
                AppCashier.pendingbillEmployee.code_name = $("#profile option:selected").text();

                AppCashier.customerpay = AppCashier.grandTotalRight;
                AppCashier.paymentType = AppCashier.pendingbillEmployee.type;

                App.setPaymentBill();
            }

            if (AppCashier.pendingbill.amount > 0) {
                AppCashier.disableNumberButton(true);
                if (AppCashier.pendingbill.is_banquet == 1) {

                } else {
                    //AppCashier.pendingbill.amount = parseInt(AppCashier.grandTotalRight);
                    AppCashier.paymentType = AppCashier.pendingbill.type;

                    App.setPaymentBill();
                    AppCashier.customerpay = parseInt(AppCashier.grandTotalRight);

                }

            }

        },
        setPendingBill: function () {
            var type = $('.nav-tabs .active').attr("data-id");
            AppCashier.resetPaymentUI();
            //disabled manual input
            AppCashier.disableNumberButton(true);

            if (type == "company") {
                var url = AppCashier.baseUrl + 'cashier/get_order_company';
                var data = {
                    order_company_id: $("#order_company option:selected").val()
                }
                var request = $.ajax({
                    type: 'POST',
                    url: url,
                    data: data
                });
                request.done(function (resp) {
                    var parsedObject = JSON.parse(resp);
                    if (parsedObject.status) {

                        AppCashier.pendingbill.code = parsedObject.data.id;
                        AppCashier.pendingbill.code_name = parsedObject.data.company_name;
                        AppCashier.pendingbill.is_banquet = parseInt(parsedObject.data.is_use_banquet);

                        if (AppCashier.pendingbill.is_banquet == 1) {

                            if (AppCashier.grandTotalRight < parseInt(parsedObject.data.down_payment)) {
                                AppCashier.alert(parsedObject.data.company_name + " terdaftar untuk pemesanan banquet<br> dengan minimal order sebesar : " + AppCashier.moneyFormat(parseInt(parsedObject.data.down_payment), "Rp "));
                                return;
                            }

                            //NO TAX
                            AppCashier.grandTotalRight -= AppCashier.subtotalTaxRight;
                            AppCashier.resetTaxInfo();
                            AppCashier.customerpay = AppCashier.grandTotalRight;

                            if (parseInt(parsedObject.data.down_payment) > 0) {
                                //set Cash
                                AppCashier.cashPayment.amount = parseInt(parsedObject.data.down_payment);
                                AppCashier.customerCashPayment = AppCashier.cashPayment.amount;
                                AppCashier.paymentType = AppCashier.cashPayment.type;
                                App.setPaymentBill();
                            }


                            //set SISA Pembayaran
                            var residu = AppCashier.grandTotalRight - parsedObject.data.down_payment;
                            AppCashier.pendingbill.amount = parseInt(residu);
                            AppCashier.paymentType = AppCashier.pendingbill.type;
                            App.setPaymentBill();
                            App.setKembalian();
                        } else {
                            AppCashier.updateTaxInfo();
                            AppCashier.customerpay = AppCashier.grandTotalRight;
                            // AppCashier.pendingbill.amount = parseInt(AppCashier.grandTotalRight);
                            // AppCashier.paymentType = AppCashier.pendingbill.type;

                            // App.setPaymentBill();
                            // AppCashier.customerpay = parseInt(AppCashier.grandTotalRight);
                            // App.setKembalian();

                            var residu = AppCashier.grandTotalRight - parsedObject.data.down_payment;
                            if (parseInt(parsedObject.data.down_payment) > 0 && residu >= 0) {
                                //set Cash
                                AppCashier.cashPayment.amount = parseInt(parsedObject.data.down_payment);
                                AppCashier.customerCashPayment = AppCashier.cashPayment.amount;
                                AppCashier.paymentType = AppCashier.cashPayment.type;
                                App.setPaymentBill();
                            }


                            //set SISA Pembayaran
                            if (residu < 0) {
                                AppCashier.pendingbill.type = 8;
                            } else {
                                AppCashier.pendingbill.type = 6;
                            }
                            AppCashier.pendingbill.amount = parseInt(residu);
                            AppCashier.paymentType = AppCashier.pendingbill.type;
                            AppCashier.customerpay = parseInt(AppCashier.grandTotalRight);
                            App.setPaymentBill();
                            App.setKembalian();
                            if (residu < 0) {
                                AppCashier.pendingbill.amount = parseInt(AppCashier.grandTotalRight);
                            } else {
                                AppCashier.pendingbill.amount = parseInt(residu);
                            }
                            // alert(AppCashier.pendingbill.amount);
                        }


                    }
                });
            } else {
                AppCashier.paymentType = AppCashier.pendingbillEmployee.type;

                AppCashier.pendingbillEmployee.amount = AppCashier.grandTotalRight;
                AppCashier.pendingbillEmployee.code = $("#profile option:selected").val();
                AppCashier.pendingbillEmployee.code_name = $("#profile option:selected").text();

                AppCashier.customerpay = AppCashier.grandTotalRight;

                var member_id = AppCashier.pendingbillEmployee.code;
                var url = AppCashier.baseUrl + 'cashier/get_member_detail_by_db_id';
                var request = $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                        member_id: member_id,
                    }
                });
                request.done(function (resp) {

                    var parsedObject = JSON.parse(resp);
                    if (parsedObject.status) {
                        if (AppCashier.orginTotalRight == 0) {
                            AppCashier.alert("Data Order Tidak ada");
                            return;
                        }
                        var diskon_member = AppCashier.orginTotalRight * parseInt(parsedObject.percentage) / 100;
                        AppCashier.discountMemberId = member_id;

                        AppCashier.discountMemberPercentage = parsedObject.percentage;

                        AppCashier.updateDiscountMember();
                        AppCashier.setSubTotal();
                        AppCashier.setGrandTotal();
                        AppCashier.setKembalian();
                        AppCashier.hideAllPopup();
                        AppCashier.updateTaxInfo();
                        App.setPaymentBill();
                    } else {
                        AppCashier.alert(parsedObject.msg);
                    }
                });
            }


            //AppCashier.updateTaxInfo();
            //alert(AppCashier.paymentType);
            //App.setKembalian();

        },
        setMemberPayment: function () {
            if (AppCashier.complimentPayment.amount > 0) {
                App.alert("Sudah Menggunakan Compliment");
                AppCashier.hideAllPopup();
            } else {
                var member_id = $('#member_id_val').val();
                var url = AppCashier.baseUrl + 'cashier/get_member_detail';
                var request = $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                        member_id: member_id,
                    }
                });
                request.done(function (resp) {

                    var parsedObject = JSON.parse(resp);
                    if (parsedObject.status) {
                        if (AppCashier.orginTotalRight == 0) {
                            AppCashier.alert("Data Order Tidak ada");
                            return;
                        }
                        var diskon_member = AppCashier.orginTotalRight * parseInt(parsedObject.percentage) / 100;
                        AppCashier.discountMemberId = member_id;

                        AppCashier.discountMemberPercentage = parsedObject.percentage;


                        AppCashier.updateDiscountMember();
                        AppCashier.setSubTotal();
                        AppCashier.setGrandTotal();
                        AppCashier.setKembalian();
                        AppCashier.hideAllPopup();
                    } else {
                        AppCashier.discountMemberId=0;
                        AppCashier.discountMember=0;
                        AppCashier.discountMemberPercentage=0;
                        AppCashier.alert(parsedObject.msg);
                    }


                });
            }
        },
        resetTaxInfo: function () {
            AppCashier.subtotalTaxRight = 0;
            AppCashier.subtotalTaxLeft = 0;

            var taxesLeft = $("#total-payment-left").find("#tax-price");
            for (var j = 0; j < taxesLeft.length; j++) {
                $(taxesLeft[j]).html(AppCashier.moneyFormat(0, "Rp "));

            }
            ;

            var taxesRight = $("#total-payment-right").find("#tax-price");
            for (var i = 0; i < taxesRight.length; i++) {
                $(taxesRight[i]).html(AppCashier.moneyFormat(0, "Rp "));

            }
            ;

            var pembulatanLeft = $("#total-payment-left").find("#pembulatan");
            for (var j = 0; j < pembulatanLeft.length; j++) {
                $(pembulatanLeft[j]).html(AppCashier.moneyFormat(0, "Rp "));

            }
            ;

            var pembulatanRight = $("#total-payment-right").find("#pembulatan");
            for (var i = 0; i < pembulatanRight.length; i++) {
                $(pembulatanRight[i]).html(AppCashier.moneyFormat(0, "Rp "));

            }
            ;

            AppCashier.setKembalian();
            AppCashier.setGrandTotal();
        },
        validationNumber: function (object) {
            // console.log(object);
            object.on('keydown', function (e) {
                if (object.attr("class") == "form-control compliment-payment-val" ||
                    object.attr("class") == "form-control voucher-payment-val" ||
                    object.attr("class") == "form-control member-payment-val"
                ) {
                    return true;
                }
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
        },
        updateDiscountMember: function () {
            if (AppCashier.complimentPayment.amount > 0) return;
            if (AppCashier.discountMemberId === 0) return;
            AppCashier.discountMember = (AppCashier.discountMemberPercentage / 100) * AppCashier.orginTotalRight;
            $(".discount-member").remove();
            var html = "<tr class='discount-member'><td> </td><td><b>Diskon Member (" + AppCashier.discountMemberPercentage + "%)</b></td><td class='tb-align-right'>" + AppCashier.moneyFormat(AppCashier.discountMember, "Rp ") + "</td></tr>";
            $("#total-payment-right").find(".discount-order-list").after(html);
        },
        initKeyboardDefault: function (object) {
            var is_mobile = /mobile|android/i.test(navigator.userAgent);
            if (is_mobile) return;
            var keyboard = $('#value').keyboard().getkeyboard();
            keyboard.destroy();
            object.keyboard({
                layout: 'custom',
                customLayout: {
                    'normal': [
                        '` 1 2 3 4 5 6 7 8 9 0 - = {bksp}',
                        'q w e r t y u i o p [ ] \\',
                        'a s d f g h j k l ; \' ',
                        '{shift} z x c v b n m , . / {shift}',
                        '{accept} {space} {left} {right}'
                    ],
                    'shift': [
                        '~ ! @ # $ % ^ & * ( ) _ + {bksp}',
                        'Q W E R T Y U I O P { } |',
                        'A S D F G H J K L : " ',
                        '{shift} Z X C V B N M < > ? {shift}',
                        '{accept} {space} {left} {right}'
                    ]
                },
                restrictInput: false, // Prevent keys not in the displayed keyboard from being typed in
                preventPaste: false,  // prevent ctrl-v and right click
                autoAccept: false,
                lockInput: false, // prevent manual keyboard entry
            });

        },
        initKeyboardNumber: function (object) {
            var is_mobile = /mobile|android/i.test(navigator.userAgent);
            if (is_mobile) return;
            var keyboard = $('#value').keyboard().getkeyboard();
            keyboard.destroy();
            object.keyboard({
                layout: 'custom',
                customLayout: {
                    'default': [
                        '1 2 3',
                        '4 5 6',
                        '7 8 9',
                        '{left} 0 {right}',
                        '{bksp} {a} {c}'
                    ]

                },
                maxLength: 16,
                restrictInput: false, // Prevent keys not in the displayed keyboard from being typed in
                preventPaste: false,  // prevent ctrl-v and right click
                autoAccept: false,
                lockInput: false, // prevent manual keyboard entry
                // }).addCaret();
            });

        },
        getOnlineReservation: function () {
            $("#get_online_data").on("click", function () {
                var url = $(this).attr("href");
                App.overlayUI.show();
                $.ajax({
                    url: url,
                    dataType: "JSON",
                    success: function (response) {
                        AppCashier.alert(response.message);
                        App.overlayUI.hide();
                        if (response.status == true) {
                            window.location.reload();
                        }
                    }
                });
                return false
            });
        }
    };
});