/**
 * Created by alta falconeri on 12/15/2014.
 */

define([
    "jquery",
    "jquery-ui",
    "bootstrap",
    "keyboard"
], function ($, ui) {
    return {
        nodeUrl             : $('#node_url').val(),
        baseUrl             : $('#base_url').val(),
        socket              : false,
        isChangeTable       : false,
        isSelectTableFirst  : false,
        isSelectTableSecond : false,
        isMergeTable        : false,
        firstTableId        : '',
        firstOrderId        : '',
        secondTableId       : '',
        statusTable         : '',
        loadingOverlay      : $("#cover"),
        userId              : $("#user_id").val(),
        groupId             : $("#group_id").val(),
        groupName           : $("#group_name").val(),
        userName            : $("#user_name").val(),
        tableParent         : $("#table-parent"),
        alreadyProcess      : $("#already_process").val(),
        alreadyCompleted    : $("#already_completed").val(),
        isEdit              : false,
        inputNumber         : $('.input-number'),
        orderIsView         : $('#order_is_view').val(),
        isDineIn            : $('#is_dine_in').val(),
        timeoutVal          : 10000,
        arrMergeTable       : [],
        totalStockAvailable      : 0,
		touchTimer 			: 0,
        init                : function () {
            console.log("App Table inited..");
                console.log(AppTable.nodeUrl)
            
            AppTable.loadingOverlay.show();

            AppTable.initFunc(AppTable);
            AppTable.initTakeaway();
            try {
                AppTable.initSocketIO();
            } catch (err) {
                AppTable.alert($('#server-error-message p').text());
                // window.location.reload(true);
            }
            AppTable.initKeyboard();
        },
        initSocketIO        : function () {

            AppTable.socket = io(AppTable.nodeUrl, {
                'reconnectionAttempts' : 2
            });
            AppTable.socket.on('reconnect_failed', function () {
                /*AppTable.loadingOverlay.hide();
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
                AppTable.alert($('#server-error-message p').text());
                window.location.reload(true);
            });

            AppTable.socket.on('connected', function (data) {
                console.log('Socket.IO connected');
                AppTable.socket.emit('cm_auth', {
                        nip       : AppTable.userId,
                        name      : AppTable.userName,
                        role      : AppTable.groupId,
                        role_name : AppTable.groupName
                    }
                );
                AppTable.socket.on('sm_auth', function (data) {
                    AppTable.loadingOverlay.hide();
                    AppTable.initUIEvent();
                });

                AppTable.socket.on('sm_notify_new_order', function (data) {
                    var tablenew = $('#tab_layout_' + data.table_id);
                    tablenew.removeClass().addClass(data.status_class);
                    tablenew.data("table-status", data.table_status);
                    tablenew.data("order-id", data.order_id);
                    tablenew.data("customer-count", data.number_guest);
                    tablenew.attr("data-reservation-id", data.reservation_id);

                    tablenew = $('#list_layout_' + data.table_id);
                    tablenew.removeClass();
                    tablenew.addClass("table-list-text label-rect-"+data.status_name);
                    tablenew.data("table-status", data.table_status);
                    tablenew.data("order-id", data.order_id);
                    tablenew.data("customer-count", data.number_guest);
                    tablenew.attr("data-reservation-id", data.reservation_id);

                    AppTable.updateStockMenu(data.arr_menu_outlet);                            
                    
                    if(data.warning_badge === true)
                    {
                        AppTable.prependWarning(data.table_id);
                        // AppTable.prependArrayWarning(merge_table);
                    }else
                    {
                        AppTable.removeWarning(data.table_id);
                    }

                    if(data.arr_merge_table != null && data.arr_merge_table.length > 0)
                    {

                      AppTable.prependBadge(data.table_id, data.table_name);
                        for (var index = 0; index < data.arr_merge_table.length; index++ ){

                          var tableTemp = $('#tab_layout_' +data.arr_merge_table[index].id);
                          tableTemp.removeClass().addClass(data.arr_merge_table[index].status_class);
                          tableTemp.data("table-status", data.table_status);
                          tableTemp.data("order-id", data.order_id);
                          tableTemp.data("customer-count", data.number_guest);
                          tableTemp.data("parent-id", data.table_id);

                          tableTemp = $('#list_layout_' +data.arr_merge_table[index].id);
                          tableTemp.removeClass();
                          tableTemp.addClass("table-list-text label-rect-"+data.status_name);
                          tableTemp.data("table-status", data.table_status);
                          tableTemp.data("order-id", data.order_id);
                          tableTemp.data("customer-count", data.number_guest);
                          tableTemp.data("parent-id", data.table_id);

                          AppTable.prependBadge(data.arr_merge_table[index].id, data.table_name);


                      }
                   }
                   
                });

                AppTable.socket.on('sm_empty_table', function (data) {
                    console.log(data);
                    var orderId = $('#order_id').val();
                    if (AppTable.isDineIn == '1' && orderId == data.order_id) {
                        window.location = data.url_redir;
                    }
                    var tablenew = $('#tab_layout_' + data.table_id);
                    tablenew.removeClass().addClass(data.status_class);
                    tablenew.data("table-status", data.table_status);
                    tablenew.data("order-id", data.order_id);
                    tablenew.data("customer-count", data.number_guest);

                    tablenew = $('#list_layout_' + data.table_id);
                    tablenew.removeClass();
                    tablenew.addClass("table-list-text label-rect-"+data.status_name);
                    tablenew.data("table-status", data.table_status);
                    tablenew.data("order-id", data.order_id);
                    tablenew.data("customer-count", data.number_guest);

                    if(data.table_status == 1){
                        AppTable.removeWarning(data.table_id); 
                        $('#tab_layout_'+data.table_id+' .badge-table').remove();
                        $('#list_layout_'+data.table_id+' .badge-table-small').remove();
                    }


                    merge_table = data.arr_merge_table;

                        if(merge_table != null)
                        {
                            for (var index = 0; index < merge_table.length; index++ ){

                              var tableTemp = $('#tab_layout_' +merge_table[index].id);
                              tableTemp.removeClass().addClass(merge_table[index].status_class);
                              tableTemp.data("table-status", data.table_status);
                              tableTemp.data("order-id", data.order_id);
                              tableTemp.data("customer-count", data.number_guest);
                              tableTemp.data("parent-id","0");

                              tableTemp = $('#list_layout_' +merge_table[index].id);
                              tableTemp.removeClass();
                              tableTemp.addClass("table-list-text label-rect-"+data.status_name);
                              tableTemp.data("table-status", data.table_status);
                              tableTemp.data("order-id", data.order_id);
                              tableTemp.data("customer-count", data.number_guest);
                              tableTemp.data("parent-id","0");

                              if(data.table_status == 1){
                            
                                AppTable.removeWarning(merge_table[index].id);         
                                $('#tab_layout_'+merge_table[index].id+' .badge-table').remove();
                                $('#list_layout_'+merge_table[index].id+' .badge-table-small').remove();
                            }    

                          }
                      }

                                     
                   
                });

                AppTable.socket.on('sm_cancel_merge', function (data){
                    ids = data.ids;
                    for(i = 0; i < ids.length; i++) AppTable.removeBadge(ids[i]);
                    console.log('Cancel');
                    window.location.reload(true);
                });

                AppTable.socket.on('sm_notify_menu_available_status', function (data) {
                    if (AppTable.isDineIn == '1') {
                        if (data.available == 1) {
                            AppTable.alert('Pesan dari kitchen : Menu ' + data.menu_name + ' habis');
                        } else {
                            AppTable.alert('Pesan dari kitchen : Menu ' + data.menu_name + ' tersedia');
                        }
                    }
                    window.location.reload(true);
                });

                 AppTable.socket.on('sm_notify_cooking_status', function (data) {
                    console.log(data);
                    var tablenew = $('#status_menu_' + data.order_menu_id);
                    var tablenew = $('#status_menu_' + data.order_menu_id);
                    var content = data.status_name;
                    tablenew.html(content);

                    if(data.notification != null){
                        AppTable.prependNotif(data.notification);
                        AppTable.updateOpenNotifBar();  
                    }
                    
                    merge_table = data.arr_merge_table;
                    if(data.cooking_status ==6)
                    {
                        AppTable.prependWarning(data.table_id);
                    }                                           
                    
                });

                 //belum digunakan lagi
                 AppTable.socket.on('sm_notify_merge_table',  function (data){                   
                    AppTable.prependBadge(data.parent_id, data.parent_name, data.status_class,data);
                    AppTable.prependArrayBadge(data);

                });


            });
        },
        initUIEvent         : function () {
            $(document).ready(function () {
                AppTable.resizePanel();
                $('#floor_id').val($('#floor_default_id').val());

                if (AppTable.alreadyProcess == '1') {
                    $('#btn-reset-order').hide();
                } else {
                    $('#btn-reset-order').show();
                }

                if (AppTable.alreadyCompleted == '2') {
                    $('#btn-reset-order').show  ();
                    $('#btn-process-order').show();
                } else {
                    $('#btn-reset-order').show();
                    $('#btn-process-order').show();
                }

                if (AppTable.alreadyCompleted == '5') {
                    $('#btn-reset-order').show();
                    $('#btn-process-order').hide();
                    $('#btn-checkout-order').hide();
                }

            });

            $(document).ajaxStop(function () {
                AppTable.resizePanel();
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

            AppTable.inputNumber.focusin(function () {
                $(this).data('oldValue', $(this).val());
            });

            AppTable.inputNumber.on('keydown', function (e) {
                return false;
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

            AppTable.inputNumber.on('change', function (e) {
                var minValue = parseInt($(this).attr('min'));
                var maxValue = parseInt($(this).attr('max'));
                var valueCurrent = parseInt($(this).val());

                var name = $(this).attr('name');
                if (valueCurrent >= minValue) {
                    $(".btn-number[data-type='minus'][data-field='" + name + "']").removeAttr('disabled')
                } else {
                    if ($(this).data('oldValue') != '') {
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

            $(document).on('click', '.dine-in-order, .table-list-text ', function () {
                var tableID = $(this).data('table-id');
                var tabelStatus = $(this).data('table-status');
                var orderID = $(this).data('order-id');
                var customerCount = $(this).data('customer-count');
                var new_order_url = $('#new_order_url').val();
                var table = $('#tab_layout_'+tableID);

                var parentID = $(this).data('parent-id');
                if(parentID != '0')
                {
                    var tableParent = $('#tab_layout_'+parentID);
                    tableID=parentID;
                    tabelStatus = tableParent.data('table-status');
                    orderID = tableParent.data('order-id');
                }

                var firstTable  = $('#tab_layout_'+AppTable.firstTableId);
                
                if (AppTable.isMergeTable) {
                    AppTable.mergeTableProcess(tableID);

                } else if(AppTable.isChangeTable) {
                    if (AppTable.isSelectTableFirst) {
                        //first choose must not empty
                        if (tabelStatus != 1) {
                            AppTable.firstTableId = tableID;
                            AppTable.firstOrderId = orderID;
                            AppTable.isSelectTableFirst = false;

                            AppTable.disableEmptyTable(false);
                        }
                        else {
                            AppTable.isSelectTableFirst = true;
                        }
                    } else {
                        //second choose must empty
                        if (tabelStatus == 1) {
                            AppTable.secondTableId = tableID;
                            AppTable.loadingOverlay.show();

                            var request = $.ajax({
                                type    : "POST",
                                url     : AppTable.baseUrl + 'table/change_table',
                                timeout : AppTable.timeoutVal,
                                data    : {
                                    first_table  : AppTable.firstTableId,
                                    first_order  : AppTable.firstOrderId,
                                    second_table : AppTable.secondTableId,
                                    reservation_id: $('#tab_layout_'+AppTable.firstTableId).attr('data-reservation-id')
                                }
                            });

                            request.done(function (msg) {
                                if (msg != '') {
                                    var parsedObject = JSON.parse(msg);
                                    if (parsedObject.status === true) {
                                        // change first table

                                        console.log(parsedObject)
                                        AppTable.socket.emit('cm_notify_new_order_to_all', {
                                            number_guest : parsedObject.table1.number_guest,
                                            table_status : parsedObject.table1.table_status,
                                            status_name  : parsedObject.table1.status_name,
                                            status_class : parsedObject.table1.status_class,
                                            table_id     : parsedObject.table1.table_id,
                                            order_id     : parsedObject.table1.order_id,
                                            reservation_id     : parsedObject.table1.reservation_id,
                                            room         : 'waiter'
                                        });

                                        //change second table
                                        AppTable.socket.emit('cm_notify_new_order_to_all', {
                                            number_guest : parsedObject.table2.number_guest,
                                            table_status : parsedObject.table2.table_status,
                                            status_name  : parsedObject.table2.status_name,
                                            status_class : parsedObject.table2.status_class,
                                            table_id     : parsedObject.table2.table_id,
                                            order_id     : parsedObject.table2.order_id,
                                            reservation_id     : parsedObject.table2.reservation_id,
                                            room         : 'waiter'
                                        });

                                        AppTable.isChangeTable = false;
                                        AppTable.isSelectTableFirst = false;
                                        AppTable.tableParent.find(".dine-in-order").css("opacity", "1");
                                        AppTable.tableParent.find(".dine-in-order").css("cursor", "pointer");
                                        $(".btn-change-table").text('Pindahkan Meja');
                                        $(".btn-merge-table").show();
                                        AppTable.loadingOverlay.hide();
                                    }
                                    else {
                                        AppTable.alert('Gagal memindahkan meja. Silahkan coba lagi. Jika masih gagal mohon hubungi administrator.');
                                        console.log(parsedObject.msg);
                                        window.location.reload(true);
                                    }
                                }
                            });
                            request.fail(function (jqXHR, textStatus) {
                                if (textStatus == 'timeout') {
                                    AppTable.alert($('#server-timeout-message p').text());
                                }
                                window.location.reload(true);

                            });
                            request.always(function () {
                            });
                        }
                        else {
                            AppTable.isSelectTableFirst = false;
                        }
                    }
                }else{
					if (tabelStatus == '1') {
                        $('#new_table_id').val(tableID);
                        $('#popup-customer-count').show();
                        // $('.popup-block').show();

                    } else if (tabelStatus == '6') {

                        var url = AppTable.baseUrl+'table/get_reservation_table';
                        $('#new_table_id').val(tableID);

                        var request = $.ajax({
                            type    : 'POST',
                            url     : url,
                            timeout : AppTable.timeoutVal,
                            data    : {
                                'table_id'     : tableID
                            }
                        });
                        request.done(function (msg) {
                            if (msg != '') {
                                var parsedObject = JSON.parse(msg);
                                AppTable.socket.emit('cm_notify_new_order', {
                                    table_status : parsedObject.table_status,
                                    status_name  : parsedObject.status_name,
                                    status_class : parsedObject.status_class,
                                    table_name   : parsedObject.table_name,
                                    table_id     : parsedObject.table_id,
                                    order_id     : parsedObject.order_id,
                                    arr_merge_table : parsedObject.arr_merge_table,
                                    room         : 'waiter'
                                });

                                $('#popup-block-reservation-detail').show();
                                $('#reservation_id').val(parsedObject.reservation_id);
                                $('#reservation-detail').html(parsedObject.msg);

                            } else {
                                window.location = AppTable.baseUrl;
                            }

                            AppTable.reservationProcess();

                        });



                    } else {
                        window.location = new_order_url + '/' + orderID;
                    }
				}
            });

            $('.btn-cancel-new-order, .btn-close-reserv').on('click', function (e) {
                e.preventDefault();
                $('.popup-block').hide();
                $('#reservation_id').val('');
                $('.input-pin').val('');

            });

            $('.new_order').bind('click').bind('click', function (e) {
                var url = $(this).attr('href');
                var new_order_url = $('#new_order_url').val();
                var number_guest = $(this).data("guest");
                var table_id = $('#new_table_id').val();
                var reservationID = $('#reservation_id').val();
                var reservation_status = $('#reservation_status').val();

                var request = $.ajax({
                    type    : 'POST',
                    url     : url,
                    timeout : AppTable.timeoutVal,
                    data    : {
                        'number_guest' : number_guest,
                        'table_id'     : table_id,
                        'reservation_id' : reservationID,
                        'reservation_status' : reservation_status
                    }
                });
                request.done(function (msg) {
                    if (msg != '') {
                        var parsedObject = JSON.parse(msg);
                        AppTable.socket.emit('cm_notify_new_order', {
                            table_status : parsedObject.table_status,
                            table_name   : parsedObject.table_name,
                            status_name  : parsedObject.status_name,
                            status_class : parsedObject.status_class,
                            table_id     : parsedObject.table_id,
                            order_id     : parsedObject.order_id,
                            arr_merge_table : parsedObject.arr_merge_table,
                            room         : 'waiter'
                        });

                        setTimeout(function () { window.location = new_order_url; }, 100);
                    } else {
                        window.location = new_order_url;
                    }

                });
                request.fail(function (jqXHR, textStatus) {
                    if (textStatus == 'timeout') {
                        AppTable.alert($('#server-timeout-message p').text());
                    }
                    window.location.reload(true);

                });
                request.always(function () {
                });

                e.preventDefault();
            });

            $(document).on('click', '.btn-change-floor', function (e) {
                $('.alert-danger').hide();
                var floor = $('#floor_id');
                var floorName = $('#floor_name');
                var floorId = floor.val();
                var url = $(this).attr('href');
                var destination = $(this).data('id');
                if (floorId != '') {
                    var request = $.ajax({
                        type    : "POST",
                        url     : url,
                        timeout : AppTable.timeoutVal,
                        data    : {
                            floor_id    : floorId,
                            destination : destination
                        }
                    });
                    request.done(function (msg) {
                        if (msg != '') {
                            var parsedObject = JSON.parse(msg);
                            if (parsedObject.status === true) {
                                floor.val(parsedObject.floor_id);
                                floorName.text(parsedObject.floor_name);
                                AppTable.tableParent.html(parsedObject.data_table);
                                $('#table-list').html(parsedObject.data_table_list);
                                // for change table position
                                if (AppTable.isChangeTable) {
                                    if (AppTable.isSelectTableFirst === true) {
                                        AppTable.disableEmptyTable(true);
                                    } else {
                                        AppTable.disableEmptyTable(false);
                                    }

                                }
                            } else {
                                AppTable.alert(parsedObject.message);
                            }
                        } else {
                            window.location.reload(true);
                        }

                    });
                    request.fail(function (jqXHR, textStatus) {
                        if (textStatus == 'timeout') {
                            AppTable.alert($('#server-timeout-message p').text());
                        }
                        window.location.reload(true);

                    });
                    request.always(function () {
                    });

                }
                e.preventDefault();
            });

            $('.btn-change-table').on('click', function (e) {

                if (AppTable.isChangeTable) {
                    AppTable.isChangeTable = false;
                    AppTable.tableParent.find(".dine-in-order").css("opacity", "1");
                    AppTable.tableParent.find(".dine-in-order").css("cursor", "pointer");
                    $(".btn-change-table").text('Pindahkan Meja');
                    $(".btn-merge-table").show();
                }
                else {
                    if ($('#all_table_empty').val() == 0) {
                        AppTable.isChangeTable = true;
                        AppTable.isSelectTableFirst = true;
                        AppTable.disableEmptyTable(true);
                        $(".btn-change-table").text('Batalkan');
                        $(".btn-merge-table").hide();
                    }
                    else
                        AppTable.alert('Tidak ada meja yang dapat dipindahkan');
                }

                e.preventDefault();
            });

            $('.btn-ok-merge').on('click', function (e) {
                AppTable.loadingOverlay.show();
                if (AppTable.arrMergeTable.length > 0) {
                    var arrOrderID = [];
                     for (var i = 0; i < AppTable.arrMergeTable.length; i++) {
                        var table = $('#tab_layout_'+AppTable.arrMergeTable[i]);
                        arrOrderID.push($(table).data('order-id'));
                     };
                    var request = $.ajax({
                        type    : "POST",
                        url     : AppTable.baseUrl + 'table/merge_table',
                        timeout : AppTable.timeoutVal,
                        data    : {
                            parent_id    : AppTable.firstTableId,
                            array_table : AppTable.arrMergeTable,
                            floor_id : $('#floor_id').val(),
                            parent_order_id: $('#tab_layout_'+AppTable.firstTableId).data('order-id'),
                            array_order_id: arrOrderID
                        }
                    });
                    request.done(function (msg) {
                        if (msg != '') {
                            var parsedObject = JSON.parse(msg);
                            if (parsedObject.status === true) {

                                 AppTable.socket.emit('cm_notify_new_order', {
                                    table_status : parsedObject.table_status,
                                    table_name   : parsedObject.table_name,
                                    status_name  : parsedObject.status_name,
                                    status_class : parsedObject.status_class,
                                    table_id     : parsedObject.table_id,
                                    order_id     : parsedObject.order_id,
                                    arr_merge_table : parsedObject.arr_merge_table
                                });
                                
                            window.location.reload(true);

                            } else {
                                AppTable.alert(parsedObject.message);
                            }
                        } else {
                            window.location.reload(true);
                        }
                    

                    });
                    request.fail(function (jqXHR, textStatus) {
                        if (textStatus == 'timeout') {
                            AppTable.alert($('#server-timeout-message p').text());
                        }
                        window.location.reload(true);

                    });
                    request.always(function () {
                    });

                }else
                {
                    AppTable.loadingOverlay.hide();
                    alert('Minimal 2 tabel yang digabungkan');
                }
            });
            $('.btn-merge-table').on('click', function (e) {
                // var countEmptyTable = 0;
                // $('.dine-in-order').each(function(){
                //     var statusTable = $(this).data('table-status');
                //     if(statusTable == 1)
                //         countEmptyTable += 1;
                // });
                if (AppTable.isMergeTable) {
                    AppTable.isMergeTable = false;
                    AppTable.tableParent.find(".dine-in-order").css("opacity", "1");
                    AppTable.tableParent.find(".dine-in-order").css("cursor", "pointer");
                    $(".btn-merge-table").text('Gabungkan Meja');
                    $(".btn-change-table").show();
                    $(".btn-ok-merge").hide();
                    $(".btn-change-floor").show();
                    $(".btn-option-list").show();
                    AppTable.hideReservationTable(false);

                    $('.dine-in-order').each(function(){
                        var tableID = $(this).data('table-id');
                        console.log(tableID)
                        for (var i = 0; i < AppTable.arrMergeTable.length; i++) {
                            if(AppTable.arrMergeTable[i] == tableID)
                                $('#tab_layout_'+tableID+' .badge-table').remove();
                        };
                        $('#tab_layout_'+AppTable.firstTableId+' .badge-table').remove();
                       
                    });

                }
                else {
                    // if (countEmptyTable > 1) {
                        AppTable.isMergeTable = true;
                        AppTable.isSelectTableFirst = true;
                        AppTable.hideReservationTable(true);
                        // AppTable.disableEmptyTable(false);
                        $(".btn-merge-table").text('Batalkan');
                        $(".btn-change-table").hide();
                        $(".btn-ok-merge").show();
                        $(".btn-change-floor").hide();
                        $(".btn-option-list").hide();
                        // AppTable.hideTableWithBadge(true);

                    // }
                    // else
                    //     AppTable.alert('Minimal terdapat 2 meja kosong untuk digabungkan');
                }
                e.preventDefault();
            });

            $('#btn-notif').on('click', function (e) {
              var options = { direction: 'right' };
              $('.notification-container').toggle('slide',options , 500);
              $('.notification-container').addClass('open-notif');

              AppTable.updateOpenNotifBar();              

            });

             $('.button-hide').on('click', function (e) {
              var options = { direction: 'right' };
              $('.notification-container').toggle('slide',options , 500);

              var list = $('.unseen-notif');
              var arrID = [];

              for (var i = list.length - 1; i >= 0; i--) {
                var id = list.eq(i).attr('data-id');
                AppTable.updateNotifCounter('-');
                $('#notif-'+id+' ' ).removeClass('unseen-notif');
              };

            });

            $('.notification-container').removeClass('open-notif');            
            AppTable.deleteNotif();
            AppTable.dineInProcess();

        },

        initTakeaway: function(){
            $('#btn-process-takeaway').on('click', function (e) {
                var customer_name = $('#customer_name').val();
                var count_ord = $('.bill-table > tbody  > tr').length;
                if (customer_name == '') {
                    AppTable.alert('Silahkan isi nama pelanggan terlebih dahulu');
                    return false;
                } else if (count_ord == 0) {
                    AppTable.alert('No Orders!');
                    return false;
                }

                AppTable.loadingOverlay.show();
                var url = $(this).attr('href');
                var order_id = $('#order_id').val();

                var request = $.ajax({
                    type    : 'POST',
                    url     : url,
                    timeout : AppTable.timeoutVal,
                    data    : {
                        'order_id'      : order_id,
                        'customer_name' : customer_name
                    }
                });
                request.done(function (msg) {
                    var parsedObject = JSON.parse(msg);
                    AppTable.socket.emit('cm_notify_new_order', {
                        number_guest : parsedObject.number_guest,
                        order_id     : order_id,
                        room         : 'kasir'
                    });

                    setTimeout(function () { window.location = parsedObject.url_redir; }, 100);

                });
                request.fail(function (jqXHR, textStatus) {
                    if (textStatus == 'timeout') {
                        AppTable.alert($('#server-timeout-message p').text());
                    }
                    window.location.reload(true);

                });
                request.always(function () {
                });
                e.preventDefault();
            });
            

            $('#btn-reset-takeaway').on('click', function (e) {
                var object = $(this);
                function resetTakeAway(){
                    AppTable.loadingOverlay.show();
                    var url = object.attr('href');
                    var order_id = $('#order_id').val();
                    var request = $.ajax({
                        type    : 'POST',
                        url     : url,
                        timeout : AppTable.timeoutVal,
                        data    : {
                            'order_id' : order_id
                        }
                    });
                    request.done(function (msg) {
                        var parsedObject = JSON.parse(msg);
                       if (parsedObject.status === false) {
                                AppTable.alert('Pesanan tidak dapat dihapus karena pesanan sudah diproses. Silahkan checkout pesanan terlebih dahulu!');
                                AppTable.loadingOverlay.hide();
                        }else{
                             window.location = parsedObject.url_redir;
                        }
                    });
                    request.fail(function (jqXHR, textStatus) {
                        if (textStatus == 'timeout') {
                            AppTable.alert($('#server-timeout-message p').text());
                        }
                        window.location.reload(true);

                    });
                    request.always(function () {
                    });
                }
                AppTable.confirm('Are you sure you want to reset order?', resetTakeAway);
                e.preventDefault();
            });

            // disable button when mode view detail takeaway
            // if (AppTable.orderIsView == '1') {
            //     $('#btn-reset-takeaway').hide();
            // } else {
            //     $('#btn-reset-takeaway').show();
            // }

        },

        updateOpenNotifBar : function(){
            if($('.notification-container').hasClass('open-notif')){
                var list = $('.unseen-notif');

                for (var i = 0 ; i < list.length ; i++) {
                    var id = list.eq(i).attr('data-id');
                    var request = $.ajax({
                        type    : 'POST',
                        url     : AppTable.baseUrl + 'notification/update_notif',
                        timeout : AppTable.timeoutVal,
                        data    : {
                            'notif_id' : id,
                        }
                    });
                    request.done(function () {

                    }); 
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
                $('.counter-notification').html(count - 1).change();
                if($('.counter-notification').html() == 0){
                    $('.counter-notification').hide();
                }  
            }
        },
        prependNotif : function(data){
            for (var i = data.length - 1; i >= 0; i--) {
                if(data[i].to_user == AppTable.userId){
                    $('.empty-notif').remove()
                    var msg = '<div class="list-notification unseen-notif" id="notif-'+data[i].notif_id+'" data-id="'+data[i].notif_id+'" >'+
                    '<p class="content-notif" >'+data[i].msg+'</p>'+
                    '<a class="button-ok-notif" href="#" data-id="'+data[i].notif_id+'"></a></div>';
                    $('#notification-container-list').prepend(msg);

                    AppTable.updateNotifCounter('+');               
                    $('.counter-notification').show();
                }
                
            }           
            AppTable.deleteNotif();
            
        },
        deleteNotif : function (){
           $('.button-ok-notif').on('click', function (e){
            AppTable.loadingOverlay.show();
            var id = $(this).attr('data-id');

            var request = $.ajax({
                type    : 'POST',
                url     : AppTable.baseUrl + 'notification/delete_notif',
                timeout : AppTable.timeoutVal,
                data    : {
                    'notif_id' : id,
                }
            });
            request.done(function () {

                $('#notif-'+id+'').remove();
                AppTable.updateNotifCounter('-');                
                AppTable.loadingOverlay.hide();
            });     
        }); 
       },
        hideReservationTable  : function(e)
        {
            if(e)
            {
                $('.dine-in-order').each(function(index, value){
                 var table =  $('#'+value.id+'');
                 var reserv = $(this).data("reservation-id");
                    if(reserv !=0)
                        table.css("opacity", "0");
                });
            }else{
                AppTable.tableParent.find(".dine-in-order").css("opacity", "1");
                AppTable.tableParent.find(".dine-in-order").css("cursor", "pointer");
            }
            
        },
        disableEmptyTable   : function (e) {
            if (e === true) {
                AppTable.tableParent.find(".dine-in-order").css("opacity", "1");
                AppTable.tableParent.find(".dine-in-order").css("cursor", "pointer");
                AppTable.tableParent.find(".label-triangle-empty").css("opacity", "0");
                AppTable.tableParent.find(".label-triangle-empty").css("cursor", "default");
                AppTable.tableParent.find(".label-rect-empty").css("opacity", "0");
                AppTable.tableParent.find(".label-rect-empty").css("cursor", "default");
                AppTable.tableParent.find(".label-circle-empty").css("opacity", "0");
                AppTable.tableParent.find(".label-circle-empty").css("cursor", "default");

                AppTable.tableParent.find(".dine-in-order").children(".badge-table").parent().css("opacity", "0");
                AppTable.tableParent.find(".dine-in-order").children(".badge-table").parent().css("cursor", "default");

            } else {
                AppTable.tableParent.find(".dine-in-order").css("opacity", "0");
                AppTable.tableParent.find(".dine-in-order").css("cursor", "default");
                AppTable.tableParent.find(".label-triangle-empty").css("opacity", "1");
                AppTable.tableParent.find(".label-triangle-empty").css("cursor", "pointer");
                AppTable.tableParent.find(".label-rect-empty").css("opacity", "1");
                AppTable.tableParent.find(".label-rect-empty").css("cursor", "pointer");
                AppTable.tableParent.find(".label-circle-empty").css("opacity", "1");
                AppTable.tableParent.find(".label-circle-empty").css("cursor", "pointer");

                AppTable.tableParent.find(".dine-in-order").children(".badge-table").parent().css("opacity", "0");
                AppTable.tableParent.find(".dine-in-order").children(".badge-table").parent().css("cursor", "default");
            }
        },

        transferProcess: function(data){
            $('.btn-transfer-order').show(); 

            $('.btn-transfer-order').unbind('click').bind('click', function (e) {
                $('#input_void_note').val('');
                e.preventDefault();

                var cookingStatus = $(data[9]).text();
                var orderMenuID = $(data[8]).text();
                var orderId = $('#order_id').val();
                var count = $(data[11]).text();

                $(".input_void_count").val('1');                
                $(".input_void_count").attr('max', count);                
                $('#popup-transfer').show();

                $('.btn-save-transfer').unbind('click').bind('click', function (e) {
                    e.preventDefault();
                    AppTable.loadingOverlay.show();
                    var order_menu_id;
                    var quantity = $('.input-quantity-transfer').val();
                    var request = $.ajax({
                        type    : 'POST',
                        url     : AppTable.baseUrl + 'table/transfer_order_menu',
                        timeout : AppTable.timeoutVal,
                        data    : {
                            quantity : quantity,
                            from_table_id : $('#table_id').val(),
                            to_table_id :  $('.to_table_id').val(),
                            order_menu_id : $('#menu_order_id_selected').val() ,
                            note : $('.input-note').val() 
                        }
                    });

                    request.done(function (msg) {
                        var parsedObject = JSON.parse(msg);
                        if (parsedObject.status == true) {
                              $('.bill-table tbody').html(parsedObject.order_list);
                              $('.total-payment tbody').html(parsedObject.order_bill);
                              $('.popup-block').hide();
                              $('.input-note').val('') ;
                              AppTable.socket.emit('cm_notify_new_order', {                               
                                table_status : parsedObject.table_status,
                                status_name  : parsedObject.status_name,
                                status_class : parsedObject.status_class,
                                table_name   : parsedObject.table_name,
                                table_id     : parsedObject.table_id,
                                order_id     : parsedObject.order_id,
                                arr_merge_table : parsedObject.arr_merge_table,
                                room         : 'waiter'
                            });

                        }else{
                            AppTable.alert(parsedObject.msg);
                        }
                    AppTable.loadingOverlay.hide();
                        
                });

                });
    
                $('.btn-cancel-transfer').on('click', function (e) {
                    $('#popup-transfer').hide();
                    $('.input-note').val('') ;
                });
               

            });

        },

        voidProcess: function(data){
            $('.btn-void-order').show(); 

            $('.btn-void-order').unbind('click').bind('click', function (e) {
                $('#input_void_note').val('');
                e.preventDefault();

                var cookingStatus = $(data[9]).text();
                var orderMenuID = $(data[8]).text();
                var orderId = $('#order_id').val();
                var count = $(data[11]).text();

                if(cookingStatus == 0 || cookingStatus == 6){
                    $('#is_decrease_stock').prop("checked", false);
                }else{
                    $('#is_decrease_stock').prop("checked",true);
                }

                $("#input_void_count").val('1');                
                $("#input_void_count").attr('max', count);                
                $('#popup-void').show();

                $('.btn-save-void').unbind('click').bind('click', function (e) {
                    $('#input_pin').val('');

                    //commented 
                    // if ($('#void_manager_confirmation').val() == '1'){

                    //     var popup = $('.popup-input');
                    //     popup.find('.title-name').text('Input Otorisasi');
                    //     popup.show();
                    //     $('.btn-void-confirm').unbind('click').bind('click', function(){
                    //         var request = $.ajax({
                    //         type    : "POST",
                    //         url     : AppTable.baseUrl + 'table/check_general_setting',
                    //         timeout : AppTable.timeoutVal,
                    //         data    : {
                    //             name  : 'void_manager_confirmation',
                    //             pin : $('#input_pin').val(),
                                
                    //         }
                    //         });
                    //          request.done(function (msg) {
                    //             if (msg != '') {
                    //                 var parsedObject = JSON.parse(msg);
                    //                 if (parsedObject.status == true) {

                    //                     AppTable.startRequestVoid(orderId, orderMenuID);                                      
                    //                     popup.hide();
                    //                 }else{
                    //                     AppTable.alert(parsedObject.msg);
                    //                 }

                    //             } else {
                    //             window.location.reload(true);
                    //          }
                    //         });

                    //         request.fail(function (jqXHR, textStatus) {
                    //             if (textStatus == 'timeout') {
                    //                 AppTable.alert($('#server-timeout-message p').text());
                    //             }
                    //             window.location.reload(true);

                    //         });

                    //     });


                    // }// end void confirmation
                    // else{
                        AppTable.startRequestVoid(orderId, orderMenuID);                                      
                    // }

                   

                });
    
                $('.btn-cancel-void').on('click', function (e) {
                    $('#popup-void').hide();
                    $('#input_pin').val('');
                });
               

            });

        },		

		startTouchTimer : function (time) {
			AppTable.touchTimer += time;
		},
			
        dineInProcess       : function () {
            // disable button when mode view detail takeaway

            $('.btn-cancel-dine-in, .btn-cancel').on('click', function (e) {
                e.preventDefault();
                $('#form-input-order').get(0).reset();
                $('#form-input-order').find('textarea').val('');
                $('#popup-customer-count').hide();
                $('.popup-block').hide();
                $('.btn-void-order').hide();
                $('#input_void_note').val('');
                $('#input_pin').val('');
              
            });

            $('.get_menus').on('click', function (e) {
                AppTable.loadingOverlay.show();
                var cat = $(this).children();
                var url = $(this).attr('href');
                var category_id = $(this).data("category");
                var menuType = $('#menu-view-type').val();
                var request = $.ajax({
                    type    : 'POST',
                    url     : url,
                    timeout : AppTable.timeoutVal,
                    data    : {
                        'category_id' : category_id,
                        'menu_type' : menuType
                    }
                });
                request.done(function (msg) {
                    if (msg != '') {
                        var parsedObject = JSON.parse(msg);
                        $('.container-menus').html(parsedObject.content);

                        var catName = ($('#btn-category-list').hasClass('active')) ? $(cat[0]).text() : $(cat[1]).text(); 
                        $('.category-name').text('' + catName);

                        AppTable.loadingOverlay.hide();
                    } else {
                        window.location.reload(true);
                    }
                });
                request.fail(function (jqXHR, textStatus) {

                    if (textStatus == 'timeout') {
                        AppTable.alert($('#server-timeout-message p').text());
                    }
                    //window.location.reload(true);

                });
                request.always(function () {
                });

                e.preventDefault();
            });

            var timeout_id = 0;
            $(document).on('mousedown', '.bill-table > tbody  > tr.tOrder', function () {
                timeout_id = setTimeout(AppTable.editOrderMenuDetail($(this)), 1000);
            }).bind('mouseup mouseleave', function () {
                clearTimeout(timeout_id);
            });
			
            var touchTimerID = 0;
            $(document).on('mouseup touchend', '.add-order-menu', function (e) {
                $(this).css('color','#333');
                clearTimeout(touchTimerID);
				if(AppTable.touchTimer < 100){
					if (AppTable.alreadyCompleted == '2' || AppTable.alreadyCompleted == '5') {
						// return false;
					}

					var id = $(this).data("id");
					var name = $(this).data("name");
					AppTable.totalStockAvailable =parseInt($('.total-available-'+id).html());
					if(AppTable.totalStockAvailable == 0 && $('#zero_stock_order').val() == '0') 
					{
						$(this).css('color','red');
						return false;
					}                

					$('.menu-name').text(name);
					$('#menu_id_selected').val(id);
					$('#is_already_process').val('0');
					$('.btn-save').show();

					AppTable.isEdit = false;
					AppTable.getMenuDetail(id, false, false);
					e.preventDefault();
				}else{
					//alert(AppTable.touchTimer);
				}
				AppTable.touchTimer = 0;
            })

            $(document).on('mousedown touchstart ', '.add-order-menu', function (e) {
                touchTimerID = setTimeout(AppTable.startTouchTimer(50), 50);
            });

            $('.btn-save').on('click', function (e) {
                e.preventDefault();
                AppTable.loadingOverlay.show();

                // if (AppTable.alreadyCompleted == '2' || AppTable.alreadyCompleted == '5') {
                //     return false;
                // }

                // if (AppTable.alreadyProcess == '1' && $('#is_already_process').val() == '1') {
                //     return false;
                // }
                var menuId = 0;
                var count = $('.count-order').val();

                if (AppTable.isEdit)
                {
                    menuId = $('#menu_order_id_selected').val();
                    var total_available = parseInt($('#temp_total_ordered').val())  + AppTable.totalStockAvailable ;
                    
                    if(parseInt(total_available) < parseInt(count) && $('#zero_stock_order').val() == '0'){
                        AppTable.alert('Jumlah pesanan melebihi stok.');
                        return false;
                    }
                    count = count - $('#temp_total_ordered').val() ;
                }
                else{
                    menuId = $('#menu_id_selected').val();
                    if(AppTable.totalStockAvailable < parseInt(count) && $('#zero_stock_order').val() == '0'){
                        AppTable.alert('Jumlah pesanan melebihi stok.');
                        return false;
                    }
                }

                var orderId = $('#order_id').val();                
                var option = '';
                var sideDish = '';
                var notes = $('.order-notes').val();

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
                    type    : "POST",
                    url     : AppTable.baseUrl + 'table/save_order_menu',
                    timeout : AppTable.timeoutVal,
                    data    : {
                        menu_id   : menuId,
                        order_id  : orderId,
                        count     : count,
                        option    : option,
                        side_dish : sideDish,
                        notes     : notes,
                        is_edit   : AppTable.isEdit,
                        is_take_away   : $('#is_take_away').val(),
                        outlet_id : $('.total-available-'+ $('#menu_id_selected').val()).data("outlet")
                    }
                });
                request.done(function (msg) {
                    if (msg != '') {
                        var parsedObject = JSON.parse(msg);

                        if (parsedObject.status === true) {
                            $('.bill-table tbody').html(parsedObject.order_list);
                            $('.total-payment tbody').html(parsedObject.order_bill);

                            AppTable.socket.emit('cm_notify_new_order', {
                                number_guest : parsedObject.number_guest,
                                table_status : parsedObject.table_status,
                                status_name  : parsedObject.status_name,
                                status_class : parsedObject.status_class,
                                table_id     : parsedObject.table_id,
                                table_name   : parsedObject.table_name,
                                order_id     : parsedObject.order_id,
                                arr_merge_table : parsedObject.arr_merge_table,
                                arr_menu_outlet : parsedObject.arr_menu_outlet,
                                room         : 'waiter'
                            });

                            AppTable.updateStockMenu(parsedObject.arr_menu_outlet);                            

                            var totalAvailable = 0;
                            if(AppTable.totalStockAvailable != 0) {
                               totalAvailable =  AppTable.totalStockAvailable - count;
                            }

                            $('.total-available-'+menuId).html(totalAvailable);


                        }else{ 
                            if(parsedObject.msg != ''){
                                AppTable.alert(parsedObject.msg);

                            }            
                        }
                        $('#popup-customer-count').hide();
                        $('.popup-block').hide();
                        AppTable.isEdit = false;
                        AppTable.countOld = '';
                        $('#form-input-order').get(0).reset();
                        $('#form-input-order').find('textarea').val('');
                    } else {

                        // window.location.reload(true);
                    }
                });
                request.fail(function (jqXHR, textStatus) {
                    if (textStatus == 'timeout') {
                        AppTable.alert($('#server-timeout-message p').text());
                    }
                    // window.location.reload(true);

                });
                request.always(function () {
                    AppTable.loadingOverlay.hide();

                });


            });
            
            $('#btn-close-legend').on('click', function() {
                 $('#legend').slideToggle('fast');
             });

            $('.btn-delete-order').on('click', function (e) {
                e.preventDefault();


                var menuId = $('#menu_order_id_selected').val();
                var orderId = $('#order_id').val();
                var count = $('.count-order').val();

                $("input[name='quantity-void']").attr('max', count);

                // $('#popup-void').show();

                var request = $.ajax({
                    type    : "POST",
                    url     : AppTable.baseUrl + 'table/delete_order_menu',
                    timeout : AppTable.timeoutVal,
                    data    : {
                        menu_id  : menuId,
                        order_id : orderId,
                        count   : $('.count-order').val()
                    }
                });

                request.done(function (msg) {
                    if (msg != '') {
                        var parsedObject = JSON.parse(msg);
                        if (parsedObject.status === true) {
                            $('.bill-table tbody').html(parsedObject.order_list);
                            $('.total-payment tbody').html(parsedObject.order_bill);
                        }
                        $('#popup-customer-count').hide();
                        $('.popup-block').hide();
                        $('#form-input-order').get(0).reset();
                        $('#form-input-order').find('textarea').val('');
                         
                         AppTable.socket.emit('cm_notify_new_order', {                               
                                arr_menu_outlet : parsedObject.arr_menu_outlet,
                                room         : 'waiter'
                            });

                            AppTable.updateStockMenu(parsedObject.arr_menu_outlet);        

                    } else {
                        // window.location.reload(true);
                    }
                });
                request.fail(function (jqXHR, textStatus) {
                    if (textStatus == 'timeout') {
                        AppTable.alert($('#server-timeout-message p').text());
                    }
                    window.location.reload(true);

                });
                request.always(function () {
                });
            });
            $('.btn-cancel-order').on('click', function (e) {
                e.preventDefault();
                var menuId = $('#menu_order_id_selected').val();
                var orderId = $('#order_id').val();
                var request = $.ajax({
                    type    : "POST",
                    url     : AppTable.baseUrl + 'table/update_cooking_status',
                    timeout : AppTable.timeoutVal,
                    data    : {
                        menu_id  : menuId,
                        order_id : orderId,
                        cooking_status : 4
                    }
                });

                request.done(function (msg) {
                    if (msg != '') {
                        var parsedObject = JSON.parse(msg);
                        if (parsedObject.status === true) {
                            $('.bill-table tbody').html(parsedObject.order_list);
                            $('.total-payment tbody').html(parsedObject.order_bill);
                        }

                        AppTable.socket.emit('cm_notify_new_order', {
                            number_guest : parsedObject.number_guest,
                            table_status : parsedObject.table_status,
                            status_name  : parsedObject.status_name,
                            status_class : parsedObject.status_class,
                            table_id     : parsedObject.table_id,
                            table_name   : parsedObject.table_name,
                            order_id     : parsedObject.order_id,
                            arr_merge_table : parsedObject.arr_merge_table,
                            arr_menu_outlet : parsedObject.arr_menu_outlet,
                            room         : 'waiter'
                        });
                        AppTable.updateStockMenu(parsedObject.arr_menu_outlet);                            

                        $('.popup-block').hide();
                        $('#form-input-order').get(0).reset();
                        $('#form-input-order').find('textarea').val('');

                    } else {
                        window.location.reload(true);
                    }
                });
                request.fail(function (jqXHR, textStatus) {
                    if (textStatus == 'timeout') {
                        AppTable.alert($('#server-timeout-message p').text());
                    }
                    window.location.reload(true);

                });
                request.always(function () {
                });
            });

            $(document).on('click', '#btn-process-order', function (e) {
                e.preventDefault();
                var count_ord = $('.bill-table > tbody  > tr').length;
                if (count_ord == 0) {
                    AppTable.alert('Belum ada pesanan!');
                    return false;
                }

                AppTable.loadingOverlay.show();
                var orderId = $('#order_id').val();
                var url = $(this).attr('href');
                var request = $.ajax({
                    type    : 'POST',
                    url     : url,
                    timeout : AppTable.timeoutVal,
                    data    : {
                        'order_id' : orderId
                    }
                });
                request.done(function (msg) {
                    if (msg != '' && msg != '0') {
                        var parsedObject = JSON.parse(msg);

                        AppTable.socket.emit('cm_notify_new_order', {
                            number_guest : parsedObject.number_guest,
                            table_status : parsedObject.table_status,
                            status_name  : parsedObject.status_name,
                            status_class : parsedObject.status_class,
                            table_id     : parsedObject.table_id,
                            table_name   : parsedObject.table_name,
                            order_id     : parsedObject.order_id,
                            arr_merge_table : parsedObject.arr_merge_table,
                            room         : 'waiter'
                        });

                        var temp = $('.bill-table > tbody  > tr');

                        temp.each(function(){

                            var statusMenu = $(this).find(".status_menu_order").html();
                            if(statusMenu == "New")
                            {
                                var statusMenu = $(this).find(".status_menu_order").html("Queue");
                                var arrDiv = $(this).children();
                                $(arrDiv[9]).text('1');
                                $(arrDiv[10]).text('1');
                            }                            

                        });

                        AppTable.loadingOverlay.hide();


                    } else {
                        window.location.reload(true);
                    }
                });
                request.fail(function (jqXHR, textStatus) {
                    if (textStatus == 'timeout') {
                        AppTable.alert($('#server-timeout-message p').text());
                    }
                    window.location.reload(true);

                });
                request.always(function () {
                });
            });

            $(document).on('click', '#btn-checkout-order', function (e) {
                e.preventDefault();
                var count_ord = $('.bill-table > tbody  > tr').length;
                if (count_ord == 0) {
                    AppTable.alert('Belum ada pesanan ! Silahkan pesan terlebih dahulu');
                    return false;
                }
                var temp = $('.bill-table > tbody  > tr');

                var countValidStatus=0;
                temp.each(function(){

                var statusMenu = $(this).find(".status_menu_order").html();
                    if(statusMenu == "Queue" || statusMenu == "Ready" || statusMenu == "Cooking")
                    {
                        countValidStatus += 1;
                        return;
                    }

                });
                
                if(countValidStatus == 0)
                {
                    AppTable.alert('Chekout gagal, silahkan periksa kembali status pesanan anda.');
                    return false;
                }

                AppTable.loadingOverlay.show();
                var orderId = $('#order_id').val();
                var url = $(this).attr('href');
                var request = $.ajax({
                    type    : 'POST',
                    url     : url,
                    timeout : AppTable.timeoutVal,
                    data    : {
                        'order_id' : orderId,
                        'floor_id' : $('#floor_id').val()
                    }
                });
                request.done(function (msg) {
                    if (msg != '' && msg != '0') {
                        var parsedObject = JSON.parse(msg);

                        if (parsedObject.status === false) {
                            AppTable.alert(parsedObject.message);
                            AppTable.loadingOverlay.hide();
                        } else {
                            AppTable.socket.emit('cm_notify_new_order', {
                                number_guest : parsedObject.number_guest,
                                table_status : parsedObject.table_status,
                                table_name   : parsedObject.table_name,
                                status_name  : parsedObject.status_name,
                                status_class : parsedObject.status_class,
                                table_id     : parsedObject.table_id,
                                order_id     : parsedObject.order_id,
                                arr_merge_table : parsedObject.arr_merge_table,
                                room         : 'waiter',
                                warning_badge : parsedObject.warning_badge
                                
                            });

                            setTimeout(function () { window.location = parsedObject.url_redir; }, 100);
                        }
                    } else {
                        window.location.reload(true);
                    }
                });
                request.fail(function (jqXHR, textStatus) {
                    if (textStatus == 'timeout') {
                        AppTable.alert($('#server-timeout-message p').text());
                    }
                    window.location.reload(true);

                });
                request.always(function () {
                });
            });

            $(document).on('click', '#btn-pending-bill', function (e) {
                e.preventDefault();
                var count_ord = $('.bill-table > tbody  > tr').length;
                if (count_ord == 0) {
                    AppTable.alert('Belum ada pesanan! Silahkan pesan terlebih dahulu');
                    return false;
                }
                AppTable.loadingOverlay.show();
                var orderId = $('#order_id').val();
                var url = $(this).attr('href');
                var request = $.ajax({
                    type    : 'POST',
                    url     : url,
                    timeout : AppTable.timeoutVal,
                    data    : {
                        'order_id' : orderId
                    }
                });
                request.done(function (msg) {
                    AppTable.loadingOverlay.hide();
                });
                request.fail(function (jqXHR, textStatus) {
                    if (textStatus == 'timeout') {
                        AppTable.alert($('#server-timeout-message p').text());
                    }
                    window.location.reload(true);

                });
                request.always(function () {
                });
            });
    
            $(document).on('click', '#btn-back-order', function (e) {
                e.preventDefault();               
                AppTable.loadingOverlay.show();
                var orderId = $('#order_id').val();
                var url = $(this).attr('href');
                var request = $.ajax({
                    type    : 'POST',
                    url     : url,
                    timeout : AppTable.timeoutVal,
                    data    : {
                        'order_id' : orderId
                    }
                });
                request.done(function (msg) {
                    var parsedObject = JSON.parse(msg);

                     AppTable.socket.emit('cm_notify_new_order', {
                                table_status : parsedObject.table_status,
                                status_name  : parsedObject.status_name,
                                status_class : parsedObject.status_class,
                                table_id     : parsedObject.table_id,
                                table_name   : parsedObject.table_name,
                                order_id     : parsedObject.order_id,
                                arr_merge_table : parsedObject.arr_merge_table,
                                room         : 'waiter',
                                warning_badge : parsedObject.warning_badge
                            });
                     
                    setTimeout(function () { window.location = parsedObject.url_redir; }, 100);                  
                });
                request.fail(function (jqXHR, textStatus) {
                    if (textStatus == 'timeout') {
                        AppTable.alert($('#server-timeout-message p').text());
                    }
                    window.location.reload(true);
                });
                request.always(function () {
                });
            });

            $(document).on('click', '#btn-reset-order', function (e) {
                  e.preventDefault();
                  var object = $(this);
                function resetOrder(){
                    AppTable.loadingOverlay.show();
                    var url = object.attr('href');
                    var order_id = $('#order_id').val();
                    var request = $.ajax({
                        type    : 'POST',
                        url     : url,
                        timeout : AppTable.timeoutVal,
                        data    : {
                            'order_id' : order_id
                        }
                    });
                    request.done(function (msg) {
                        if (msg != '') {
                            var parsedObject = JSON.parse(msg);
                            //console.log(parsedObject.status);

                            if (parsedObject.status === false) {
                                //alert('Cannot reset, order already process to kitchen');
                                AppTable.alert('Pesanan tidak dapat dihapus karena pesanan sudah diproses. Silahkan checkout pesanan terlebih dahulu!');
                                AppTable.loadingOverlay.hide();
                            } else {
                                AppTable.socket.emit('cm_empty_table', {
                                    number_guest : parsedObject.number_guest,
                                    table_status : parsedObject.table_status,
                                    status_name  : parsedObject.status_name,
                                    status_class : parsedObject.status_class,
                                    table_id     : parsedObject.table_id,
                                    order_id     : parsedObject.order_id,
                                    url_redir    : parsedObject.url_redir,
                                    arr_merge_table : parsedObject.arr_merge_table,
                                    room         : 'waiter'
                                });

                                setTimeout(function () { window.location = parsedObject.url_redir; }, 100);
                            }
                        } else {
                            window.location.reload(true);
                        }

                    });
                    request.fail(function (jqXHR, textStatus) {
                        if (textStatus == 'timeout') {
                            AppTable.alert($('#server-timeout-message p').text());
                        }
                        window.location.reload(true);

                    });
                    request.always(function () {
                    });

                }
                AppTable.confirm('Anda yakin ingin reset order?', resetOrder);
                
              
            });

            $('#btn-cancel-merge').on('click', function(e){
                e.preventDefault();
                object = $(this);
                function cancelMerge(){
                    url = object.attr('href');
                    console.log(url);
                    var request = $.ajax({
                        type    : 'GET',
                        url     : url,
                        timeout : AppTable.timeoutVal
                    });
                    request.done(function (msg){
                        if (msg != '') {
                            var parsedObject = JSON.parse(msg);
                            AppTable.socket.emit('cm_cancel_merge', {
                                ids : parsedObject.table_id
                            });

                             AppTable.socket.emit('cm_empty_table', {
                                    number_guest : 0,
                                    table_status : parsedObject.table_status,
                                    status_name  : parsedObject.status_name,
                                    table_id     : parsedObject.table_id,
                                    order_id     : 0,
                                    arr_merge_table : parsedObject.arr_merge_table,
                                    room         : 'waiter'
                                });


                            setTimeout(function () { window.location = parsedObject.url_redir; }, 100);
                        }
                    });
                }
                AppTable.confirm('Anda yakin ingin membatalkan penggabungan meja?', cancelMerge);
                console.log("ini loh");

            });

            $('#btn-menu-list').on('click', function (e){
               $('#list-menu-text').show();
               $('#thumb-menu-text').hide();
               //$('#table-menu-header').show();	
               $('#menu-container').css({
					"height":"100%",
					"overflow-x":"hidden",
					"overflow-y":"auto"
			   });		
               $('#btn-menu-thumb').removeClass('active');
               $('#btn-menu-list').addClass('active');               
               $('#menu-view-type').val('list');
                
            });

            $('#btn-menu-thumb').on('click', function (e){
               $('#thumb-menu-text').show();
               $('#list-menu-text').hide();
               //$('#table-menu-header').hide();	
               $('#menu-container').css({
					"height":"100%",
					"overflow-x":"hidden",
					"overflow-y":"auto"
			   });			   
               $('#btn-menu-list').removeClass('active');
               $('#btn-menu-thumb').addClass('active');
               $('#menu-view-type').val('thumb');

            });

            $('#btn-category-list').on('click', function (e){
             $('#list-category-text').show();
             $('#thumb-category-text').hide();
             $('#btn-category-thumb').removeClass('active');
             $('#btn-category-list').addClass('active');               

            });

            $('#btn-category-thumb').on('click', function (e){
               $('#thumb-category-text').show();
               $('#list-category-text').hide();
               $('#btn-category-list').removeClass('active');
               $('#btn-category-thumb').addClass('active');

            });

             $('#btn-split-order').on('click', function (e){
                e.preventDefault();
                var count_ord = $('.bill-table > tbody  > tr').length;
                if (count_ord < 2) {
                    AppTable.alert('Membagi tagihan harus memesan menu lebih dari satu.');
                    return false;
                }
                $('.split-block').show();             
                AppTable.splitBillProcess();
                // AppTable.arrMergeTable.push(tableID);

            });

            $('#btn-combine-order').on('click', function (e){
                e.preventDefault();
                
                $('.combine-block').show();             
                var orderId =  $('#order_id').val();
                var request = $.ajax({
                        type    : 'POST',
                        url     : AppTable.baseUrl + 'table/get_order_combine',
                        timeout : AppTable.timeoutVal,
                        data    : {
                            'order_id' : orderId
                        }
                    });
                    request.done(function (msg) {
                        if (msg != '') {
                            var parsedObject = JSON.parse(msg);
                            $('.combine-order-table > tbody').html(parsedObject.data);
                        } else {
                            window.location.reload(true);
                        }

                    });
                    request.fail(function (jqXHR, textStatus) {
                        if (textStatus == 'timeout') {
                            AppTable.alert($('#server-timeout-message p').text());
                        }
                        window.location.reload(true);

                    });
                    request.always(function () {
                    });

                $('.btn-save-combine').on('click', function (e){
                    e.preventDefault();
                    var destinationId = $('input[name="combine-order"]:checked').val() ; 
                    request = $.ajax({
                        type    : 'POST',
                        url     : AppTable.baseUrl + 'table/save_order_combine',
                        timeout : AppTable.timeoutVal,
                        data    : {
                            'order_id' : orderId,
                            'destination_id': destinationId
                        }
                    });
                    request.done(function (msg) {
                        if (msg != '') {
                            var parsedObject = JSON.parse(msg);
                            console.log(parsedObject);

                            if(parsedObject.number_guest ==0){
                                AppTable.socket.emit('cm_empty_table', {
                                    number_guest : parsedObject.number_guest,
                                    table_status : parsedObject.table_status,
                                    status_name  : parsedObject.status_name,
                                    status_class : parsedObject.status_class,
                                    table_id     : parsedObject.table_id,
                                    order_id     : parsedObject.order_id,
                                    url_redir    : parsedObject.url_redir,
                                    arr_merge_table : parsedObject.arr_merge_table,
                                    room         : 'waiter'
                                });
                            }
                            

                            setTimeout(function () { window.location = parsedObject.url_redir; }, 100);
                        } else {
                            window.location.reload(true);
                        }

                    });
                    request.fail(function (jqXHR, textStatus) {
                        if (textStatus == 'timeout') {
                            AppTable.alert($('#server-timeout-message p').text());
                        }
                        window.location.reload(true);

                    });
                    request.always(function () {
                    });
                });


            });


            $('#btn-table-list').on('click', function (e){
              
                var request = $.ajax({
                        type    : 'POST',
                        url     : AppTable.baseUrl + 'table/update_table_view',
                        timeout : AppTable.timeoutVal,
                        data    : {
                            'view_type' : '1' //1 for list
                        }
                    });
                    request.done(function (msg) {
                        if (msg != '') {
                            var parsedObject = JSON.parse(msg);                           
                        } else {
                            window.location.reload(true);
                        }
                        $('#table-view-list').show();
                        $('#table-parent').hide();
                        $('#btn-table-thumb').removeClass('active');
                        $('#btn-table-list').addClass('active'); 
                        $('.navigation-floor').hide();

                    });
                    request.fail(function (jqXHR, textStatus) {
                        if (textStatus == 'timeout') {
                            AppTable.alert($('#server-timeout-message p').text());
                        }
                        window.location.reload(true);

                    });
                    request.always(function () {
                    });                
            });
            $('#btn-table-thumb').on('click', function (e){                            

               var request = $.ajax({
                        type    : 'POST',
                        url     : AppTable.baseUrl + 'table/update_table_view',
                        timeout : AppTable.timeoutVal,
                        data    : {
                            'view_type' : '2' //2 for thumb
                        }
                    });
                    request.done(function (msg) {
                        console.log(msg);
                        if (msg != '') {
                            var parsedObject = JSON.parse(msg);                           
                        } else {
                            window.location.reload(true);
                        }

                        $('#table-parent').show();
                        $('#table-view-list').hide();
                        $('#btn-table-list').removeClass('active');
                        $('#btn-table-thumb').addClass('active');
                        $('.navigation-floor').show(); 

                    });
                    request.fail(function (jqXHR, textStatus) {
                        if (textStatus == 'timeout') {
                            AppTable.alert($('#server-timeout-message p').text());
                        }
                        window.location.reload(true);

                    });
                    request.always(function () {
                    });    
            });

            $('.btn-cancel-dine-in').on('click', function (e) {
                e.preventDefault();
                $('#form-input-order').get(0).reset();
                $('#form-input-order').find('textarea').val('');
                $('.split-block').hide();
                $('.combine-block').hide();
                $('#input_void_note').val('');
            });


            },
        resizePanel         : function () {
            var bilPanelHeight = $('.bill-theme-con').height();
            var subtotalheight = $('.total-payment').height();
            var paymentAmount = $('#paymentAmount').height();
            //$('#table-bill-list').height(bilPanelHeight - subtotalheight - 10);
            $('#table-bill-list-checkout').height(bilPanelHeight - subtotalheight - paymentAmount);
        },
        editOrderMenuDetail : function (row) {
            var arrDiv = row.children();

            $('.menu-name').text($(arrDiv[1]).text());
            $('#menu_id_selected').val($(arrDiv[4]).text());
            $('#menu_order_id_selected').val($(arrDiv[8]).text());
            $('#is_already_process').val($(arrDiv[10]).text());
            $('#temp_total_ordered').val($(arrDiv[11]).text());
            AppTable.totalStockAvailable =parseInt ($('.total-available-'+$(arrDiv[4]).text()).html());

            AppTable.getMenuDetail($(arrDiv[4]).text(), true, arrDiv);
            AppTable.voidProcess(arrDiv);
            AppTable.transferProcess(arrDiv);
            AppTable.isEdit = true;
        },
        removeBadge : function(id){
            $('#tab_layout_'+id+' .badge-table').remove();
            $('#list_layout_'+id+' .badge-table').remove();

            $('#tab_layout_'+id+' .badge-table').data('parent-id', 0);
            $('#list_layout_'+id+' .badge-table').data('parent-id', 0);

        }, 
        removeChildBadge: function(childObject){
            $(childObject).each(function(){ 
                AppTable.removeBadge($(this).data('table-id'))
            }) 

        },
        prependChildBadge: function(childObject, name){
         $(childObject).each(function(){ 
                AppTable.prependBadge($(this).data('table-id'), name)
            }) 
        }, 
        prependBadge        : function(id, name, status_class, data)
        {
            
            AppTable.removeBadge(id);

            var table = $('#tab_layout_'+id);
            var badge = '<div class="badge-table">'+name+'</div>';
            table.prepend(badge);

            var tableList = $('#list_layout_'+id);
            var badgeList = '<div class="badge-table-small">'+name+'</div>';
            tableList.prepend(badgeList);


            if(status_class){
                table.removeClass().addClass(status_class);
                tableList.removeClass().addClass("table-list-text label-rect-"+data.status_name); 

                table.attr("data-order-id", data.order_id);
                tableList.attr("data-order-id", data.order_id);
                
                table.attr("data-table-status", data.table_status);
                tableList.attr("data-table-status", data.table_status);
            }


        },

        prependArrayBadge      : function(data)
        {
           var name =  data.parent_name;
           var parent_id = data.parent_id;
           var status_class =data.status_class;
           var order_id = data.order_id;
           var merge_table = data.arr_merge_table;
            
            if(merge_table != null)
            {
             for (var index = 0; index < merge_table.length; index++ ){

                AppTable.prependBadge(merge_table[index].id, name, merge_table[index].status_class,data);
                $('#tab_layout_'+merge_table[index].id).attr("data-parent-id", parent_id);
                $('#list_layout_'+merge_table[index].id).attr("parent-data-id", parent_id);
            } 

         }
        },

        prependWarning      : function(id)
        {
             $('#tab_layout_'+id).prepend('<div class="warning-table"></div>');
             $('#list_layout_'+id).prepend('<div class="warning-table-small"></div>');
        },

        removeWarning      : function(id)
        {
            $('#tab_layout_'+id+' .warning-table').remove();
            $('#list_layout_'+id+' .warning-table-small').remove();
            
        },

        prependArrayWarning      : function(data)
        {
            if(data != null)
            {
             for (var index = 0; index < data.length; index++ ){
                AppTable.prependWarning(data[index].id);                            
            }

         }
        },

        removeArrayWarning      : function(data, name)
             {
                if(data != null)
                {
                   for (var index = 0; index < data.length; index++ ){
                    AppTable.removeWarning(data[index].id);                            
                }

            }
        },

        getTableMergeChild : function (id) {
           return $("#table-parent").find('[data-parent-id="'+id+'"]')
        },

        updateStockMenu : function(arr_menu_outlet){
            if(typeof arr_menu_outlet !== "undefined"){
                for (var index = 0; index < arr_menu_outlet.length; index++ ){
                    $('.total-available-' +arr_menu_outlet[index].id)
                    .html(arr_menu_outlet[index].total_available);                             
                }
            }
            
        },

        getMenuDetail       : function (menuId, isEdit, dataOrder) {
            var menuOrderID = 0;
            if (isEdit) {
                menuOrderID = $(dataOrder[8]).text();
            }
            var request = $.ajax({
                type    : "POST",
                url     : AppTable.baseUrl + 'table/get_menu_accessories',
                timeout : AppTable.timeoutVal,
                data    : {
                    menu_id       : menuId,
                    menu_order_id : menuOrderID
                }
            });

            request.done(function (msg) {
                if (msg != '') {
                    var parsedObject = JSON.parse(msg);
                    $('.side-dish').html(parsedObject.side_dish);
                    $('.menu-option').html(parsedObject.options);
                    if (isEdit) {
                        $('.count-order').val($(dataOrder[2]).text());
                        $('.order-notes').val($(dataOrder[5]).text());
                        $('#menu_cooking_status').val($(dataOrder[9]).text());
                    
                        // disable button when mode view detail takeaway
                        if ($(dataOrder[10]).text() == '1' || AppTable.alreadyCompleted == '2' || AppTable.alreadyCompleted == '5') {
                            $('.btn-delete-order').hide();
                            $('.btn-save').hide();
                            //enable button batalkan for cooking status queue and unvailable                            
                            if($(dataOrder[9]).text() == '0' ||$(dataOrder[9]).text() == '6' || $(dataOrder[9]).text() == '4')
                            {
                                $('.btn-delete-order').show();
                                $('.btn-save').show();
                                $('.btn-void-order').hide();                
                            }
                            else{
                                $('.btn-cancel-order').hide();
                            }
                        } else {
                            $('.btn-delete-order').show();
                            $('.btn-save').show();
                            $('.btn-cancel-order').hide();

                              if($(dataOrder[9]).text() == '0' ||$(dataOrder[9]).text() == '6' || $(dataOrder[9]).text() == '4')
                            {
                                $('.btn-void-order').hide();                

                            }

                            if($(dataOrder[9]).text() == '4')
                            {
                                $('.btn-save').hide();                

                            }
                        }
                    }
                    else {
                        $('#menu_cooking_status').val('1');
                        $('.btn-delete-order').hide();
                        $('.btn-cancel-order').hide();
                        $('.btn-transfer-order').hide();                

                    }

                    //$('#popup-customer-count').show();
                    $('#popup-new-order').show();
                } else {
                    window.location.reload(true);
                }
            });
            request.fail(function (jqXHR, textStatus) {
                if (textStatus == 'timeout') {
                    AppTable.alert($('#server-timeout-message p').text());
                }
                window.location.reload(true);

            });
            request.always(function () {
            });
        },

        splitBillProcess       : function () 
        {
            
            var request = $.ajax({
                type    : "POST",
                url     : AppTable.baseUrl + 'table/get_list_order',
                timeout : AppTable.timeoutVal,
                data    : {
                    order_id       : $('#order_id').val(),
                }
            });

            request.done(function (msg) {

                var parsedObject = JSON.parse(msg);
                console.log(parsedObject);
                $('.split-order-table tbody').html(parsedObject.order_list);

               
                $('.btn-number').on('click', function (e) {
                    e.preventDefault();

                    var fieldName = $(this).attr('data-field');
                    var type = $(this).attr('data-type');
                    var input = $("input[name='" + fieldName + "']");
                    var currentVal = parseInt(input.val());
                    var id = fieldName.split("-");
                    id = id[1];
                     opositeType = (type == 'minus') ? 'plus' : 'minus';
                    var oppositeInput = $("input[name='"+opositeType+"-"+id+"']");
                    var oppositeVal = parseInt(oppositeInput.val());
                    if (!isNaN(currentVal)) {
                        // if (type == 'minus') {

                        //     if (currentVal > input.attr('min')) {
                        //         input.val(currentVal + 1).change();
                        //         oppositeInput.val(oppositeVal - 1).change();
                        //     }                            

                        // } else if (type == 'plus') {

                        if (currentVal < input.attr('max')) {
                            input.val(currentVal + 1).change();
                            oppositeInput.val(oppositeVal - 1).change();

                        }
                    // }
                } else {
                    input.val(1);
                }
            });


            });




        },


        reservationProcess: function(){

         $('.btn-reserv-come').on('click', function (e) {
            var status = $(this).attr('data-status');
            $('#reservation_status').val(status);
            $('#popup-block-reservation-detail').hide();
            $('#popup-customer-count').show();                

            var dataCount = $('.new_order');
            var customerCount =  $('#reserved_customer_count').val() ;

            $('.new_order').each(function(){               

                if (parseInt($(this).attr('data-guest')) == customerCount){
                    $(this).addClass('reservation-active');
                    return;
                }

            });           
       
          });  


          $('.btn-save-replace-reserv').unbind('click').bind('click', function (e) {
            e.preventDefault();
            
            var status = $(this).attr('data-status');
            $('#reservation_status').val(status);

               var request = $.ajax({
                        type    : 'POST',
                        url     : AppTable.baseUrl + 'table/update_reservation_status',
                        timeout : AppTable.timeoutVal,
                        data    : {
                            reservation_id : $('#reservation_id').val(),
                            status : status,
                            note: $('#form-reservation-note').serialize()
                        }
                    });
                request.done(function (msg) {
                    var parsedObject = JSON.parse(msg);
                    if(parsedObject.status == true){

                        $('#popup-block-reservation-detail').hide();
                        $('#popup-reservation-note').hide();
                        
                        if(status=='4'){
                            $('#popup-customer-count').show();               
                        }else{

                             AppTable.socket.emit('cm_empty_table', {
                                    table_status : parsedObject.table_status,
                                    status_name  : parsedObject.status_name,
                                    status_class : parsedObject.status_class,
                                    table_id     : parsedObject.table_id,
                                    room         : 'waiter'
                                });

                             var tablenew = $('#tab_layout_' + parsedObject.table_id);

                             tablenew.removeClass().addClass(parsedObject.status_class);
                             tablenew.data("table-status", parsedObject.table_status);

                             tablenew = $('#list_layout_' + parsedObject.table_id);
                             tablenew.removeClass();
                             tablenew.addClass("table-list-text label-rect-"+parsedObject.status_name);
                             tablenew.data("table-status", parsedObject.table_status);

                        }


                    }else{
                        AppTable.alert(parsedObject.msg);
                    }
                                              
                });
                request.fail(function () {
                    window.location.reload(true);
                    
                });

       
          });  


         $('#btn-reserv-replace, #btn-reserv-delete').on('click', function (e) {
            var status = $(this).attr('data-status');
            var title = "";
            $('#reservation_status').val(status);
            if(status == '3'){
                title = 'Hapus Status Reserved';

            }else{
                title = 'Isi Dengan Tamu Lain';

            }

            $('.btn-save-replace-reserv').attr('data-status', status);    

            var request = $.ajax({
                type    : 'POST',
                url     : AppTable.baseUrl+'table/get_reserved_note',
                timeout : AppTable.timeoutVal,
                data    : {
                }
            });
            request.done(function (msg) {
                if (msg != '') {
                    var parsedObject = JSON.parse(msg);

                    $('#popup-reservation-note').show();
                    $('#reservation-note').html(parsedObject.msg);
                    $('#popup-reservation-note').find('.title-name').text(title);                    

                } else {
                    window.location = AppTable.baseUrl;
                }

                AppTable.reservationProcess();

            });


          });  


        }, //end reservationProcess func

        startRequestVoid: function(orderId, orderMenuID){
            AppTable.loadingOverlay.show();
            var request = $.ajax({
                type    : "POST",
                url     : AppTable.baseUrl + 'table/void_order_menu',
                data    : {
                    order_menu_id  : orderMenuID,
                    table_id  : $('#table_id').val(),
                    order_id : orderId,
                    count   : $('#input_void_count').val(),
                    is_decrease_stock: $('#is_decrease_stock').is( ":checked" ),
                    input_void_note: $('#input_void_note').val()
                }
            });
            request.done(function (msg) {
                if (msg != '') {
                    var parsedObject = JSON.parse(msg);

                    if (parsedObject.status == true) {
                        $('.bill-table tbody').html(parsedObject.order_list);
                        $('.total-payment tbody').html(parsedObject.order_bill);

                        $('#popup-customer-count').hide();
                        $('.popup-block').hide();

                        AppTable.socket.emit('cm_notify_new_order', {                               
                            arr_menu_outlet : parsedObject.arr_menu_outlet,
                            table_status : parsedObject.table_status,
                            status_name  : parsedObject.status_name,
                            status_class : parsedObject.status_class,
                            table_name   : parsedObject.table_name,
                            table_id     : parsedObject.table_id,
                            order_id     : parsedObject.order_id,
                            arr_merge_table : parsedObject.arr_merge_table,
                            room         : 'waiter'
                        });

                        AppTable.updateStockMenu(parsedObject.arr_menu_outlet);  
                        $('.btn-void-order').hide();

                    }else{
                        AppTable.alert(parsedObject.msg);
                    }

                    AppTable.loadingOverlay.hide();

                } else {
                    window.location.reload(true);
                }
            });

        

            request.fail(function (jqXHR, textStatus) {
                if (textStatus == 'timeout') {
                    AppTable.alert($('#server-timeout-message p').text());
                }
                // window.location.reload(true);

            });

            // request.abort();

        },//end request void


        initKeyboard:function(){
            var is_mobile = /mobile|android/i.test(navigator.userAgent);
            if(is_mobile) return;
              $(".form-keyboard").keyboard({
     
                layout: 'custom',
                customLayout: {
                'normal': [
                    '` 1 2 3 4 5 6 7 8 9 0 - = {bksp}',
                    '{tab} q w e r t y u i o p [ ] \\',
                    'a s d f g h j k l ; \' {enter}',
                    '{shift} z x c v b n m , . / {shift}',
                    '{accept} {space} {left} {right}'
                ],
                'shift': [
                        '~ ! @ # $ % ^ & * ( ) _ + {bksp}',
                        '{tab} Q W E R T Y U I O P { } |',
                        'A S D F G H J K L : " {enter}',
                        '{shift} Z X C V B N M < > ? {shift}',
                        '{accept} {space} {left} {right}'
                    ]
                },
                restrictInput : false, // Prevent keys not in the displayed keyboard from being typed in
                preventPaste : true,  // prevent ctrl-v and right click
                autoAccept : false,
                lockInput: false, // prevent manual keyboard entry
            }).addCaret();
        },

        mergeTableProcess:function(tableID){

                    var child = AppTable.getTableMergeChild(tableID);
                    var firstTable  = $('#tab_layout_'+AppTable.firstTableId);

                    //pertama memilih parent table
                   if (AppTable.isSelectTableFirst) {
                            AppTable.firstTableId = tableID;
                            AppTable.isSelectTableFirst = false;
                            firstTable = $('#tab_layout_'+tableID);
                            AppTable.prependBadge(AppTable.firstTableId, firstTable.data('table-name'));

                            if(child.length > 0){
                                // AppTable.removeChildBadge(child);
                                $(child).each(function(){ 
                                    AppTable.prependBadge($(this).data('table-id'), firstTable.data('table-name'));
                                    AppTable.arrMergeTable.push($(this).data('table-id'));
                                }) 
                            }    

                    } 
                        //jika memilih sudah memilih parent table, lalu memilih child
                    else {
                        var index = AppTable.arrMergeTable.indexOf(tableID);

                        //menambah table sebagai child
                        if( index == -1 && AppTable.firstTableId!= tableID)
                        {
                            console.log('1')
                            AppTable.arrMergeTable.push(tableID);
                            AppTable.removeBadge(tableID);  

                            if(child.length > 0){
                                // AppTable.removeChildBadge(child);
                                $(child).each(function(){ 
                                    AppTable.prependBadge($(this).data('table-id'), firstTable.data('table-name'));
                                    AppTable.arrMergeTable.push($(this).data('table-id'));
                                }) 
                            }                             

                            AppTable.prependBadge(tableID, firstTable.data('table-name'));
                            console.log( AppTable.arrMergeTable);
                        }

                        //table child sudah terdaftar namun dipilih kembali maka hapus badge
                        else if(tableID != AppTable.firstTableId){
                            console.log('2')
                           $('#tab_layout_'+tableID+' .badge-table').remove();
                           
                                $(child).each(function(){ 
                                    var indexChild = AppTable.arrMergeTable.indexOf($(this).data('table-id'));
                                    AppTable.removeBadge($(this).data('table-id'))
                                    AppTable.arrMergeTable.splice(indexChild, 1);
                                }) 

                            AppTable.arrMergeTable.splice(index, 1);
                        }

                        //jika yg dipilih merupakan parent dan tidak memiliki child
                        else if(AppTable.arrMergeTable.length == 0){
                            console.log('3')
                           $('#tab_layout_'+AppTable.firstTableId+' .badge-table').remove();
                            AppTable.isSelectTableFirst = true;
                        }
                        else{
                            console.log('4')

                        }
                    }

        } //end mergetable proccess


    };
});