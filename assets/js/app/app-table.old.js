/**
 * Created by alta falconeri on 12/15/2014.
 */

define([
    "jquery",
    "jquery-ui",
    "bootstrap"
], function ($, ui) {
    return {
        nodeUrl             : $('#node_url').val(),
        baseUrl             : $('#base_url').val(),
        socket              : false,
        isChangeTable       : false,
        isSelectTableFirst  : false,
        isSelectTableSecond : false,
        firstTableId        : '',
        secondTableId       : '',
        statusTable         : '',
        loadingOverlay      : $("#cover"),
        userId              : $("#user_id").val(),
        groupId             : $("#group_id").val(),
        groupName           : $("#group_name").val(),
        userName            : $("#user_name").val(),
        init                : function () {
            console.log("App Table inited..");
            AppTable.loadingOverlay.show();
            try {
                AppTable.initSocketIO();
            } catch (err) {
                AppTable.loadingOverlay.hide();
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

            AppTable.socket = io(AppTable.nodeUrl, {
                'reconnectionAttempts' : 2
            });
            AppTable.socket.on('reconnect_failed', function () {
                AppTable.loadingOverlay.hide();
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
                    var content = '' +
                        '<p class="table-name">' + data.status_name + '</p>' +
                        '<p class= "table-status" >' + data.table_status + '</p>' +
                        '<input type = "hidden" id = "table_id_selected" value = "' + data.table_id + '" />' +
                        '<input type = "hidden" id = "number_guest_selected" value = "' + data.number_guest + '" />' +
                        '<input type = "hidden" id = "table_status_selected" value = "' + data.table_status + '" />' +
                        '<input type = "hidden" id = "order_id_selected" value = "' + data.order_id + '" /> ';
                    tablenew.removeClass().addClass("table-list table2 status-" + data.status_name);
                    tablenew.html(content);
                });
            });
        },
        initUIEvent         : function () {
            $(document).on('click', '.table-list', function () {
                var arrDiv = $(this).children();
                var statusTable = $(arrDiv[1]).text();
                if (AppTable.isChangeTable == false) {
                    if (statusTable == 1) {
                        for (i = 1; i < 13; i++) {
                            $('.table_id_' + i).val($(arrDiv[2]).val());
                        }
                        $('#new_table_id').val($(arrDiv[2]).val());
                        $('#order_id_hidden').val($(arrDiv[5]).val());
                        $('.popup-block').show();
                    }
                    else {
                        $('#table_id_hidden').val($(arrDiv[2]).val());
                        $('#number_guest_hidden').val($(arrDiv[3]).val());
                        $('#table_status_hidden').val($(arrDiv[4]).val());
                        $('#order_id_hidden').val($(arrDiv[5]).val());
                        // console.log($(arrDiv[4]).val());
                        $(".btn-hidden").click();
                    }
                }
                else {
                    if (AppTable.isSelectTableFirst) {
                        console.log('first choose');

                        //first choose must empty
                        if (statusTable != 1) {
                            // AppTable.isSelectTableSecond = true;
                            AppTable.firstTableId = $(arrDiv[2]).val();
                            AppTable.statusTable = $(arrDiv[4]).val();
                            AppTable.isSelectTableFirst = false;

                            $(".table-container").find(".status-completed").css("background-color", "red");
                            $(".table-container").find(".status-order").css("background-color", "red");
                            $(".table-container").find(".status-waiting").css("background-color", "red");
                            $(".table-container").find(".status-empty").css("background-color", "");
                        }
                        else {
                            console.log("table empty");
                            AppTable.isSelectTableFirst = true;
                        }
                    }
                    else {
                        console.log('second choose');
                        //second choose must empty
                        if (statusTable == 1) {
                            AppTable.secondTableId = $(arrDiv[2]).val();

                            // console.log('AppTable.firstTableId '+AppTable.firstTableId);
                            // console.log('AppTable.statusTable '+AppTable.statusTable);
                            // console.log('AppTable.secondTableId '+AppTable.secondTableId);
                            AppTable.loadingOverlay.show();
                            $.ajax({
                                type : "POST",
                                url  : AppTable.baseUrl + 'table/change_table',
                                data : {
                                    first_table  : AppTable.firstTableId,
                                    second_table : AppTable.secondTableId,
                                    status_table : AppTable.statusTable
                                }
                            }).done(function (resp) {
                                if (resp != '') {
                                    var parsedObject = JSON.parse(resp);
                                    if (parsedObject.status === true) {
                                        console.log(parsedObject);
                                        // change first table
                                        AppTable.socket.emit('cm_notify_new_order', {
                                            number_guest : parsedObject.table1.number_guest,
                                            table_status : parsedObject.table1.table_status,
                                            status_name  : parsedObject.table1.status_name,
                                            table_id     : parsedObject.table1.table_id,
                                            order_id     : parsedObject.table1.order_id,
                                            room         : 'waiter'
                                        });

                                        //change second table
                                        AppTable.socket.emit('cm_notify_new_order', {
                                            number_guest : parsedObject.table2.number_guest,
                                            table_status : parsedObject.table2.table_status,
                                            status_name  : parsedObject.table2.status_name,
                                            table_id     : parsedObject.table2.table_id,
                                            order_id     : parsedObject.table2.order_id,
                                            room         : 'waiter'
                                        });

                                        AppTable.isChangeTable = false;
                                        AppTable.isSelectTableFirst = false;
                                        $('#floor_id').val(1);
                                        setTimeout(function(){window.location.reload(); }, 1000);
                                    }
                                    else {
                                        AppTable.loadingOverlay.hide();
                                        alert('Pindahkan meja gagal dilakukan');
                                        // console.log(resp);
                                    }
                                }
                            }).fail(function () {
                                alert('failed');
                                window.location.reload();
                            });
                        }
                        else {
                            AppTable.loadingOverlay.hide();
                            console.log("table not empty");
                            AppTable.isSelectTableFirst = false;
                        }
                    }
                }
            });

            $('.btn-trans').click(function () {
                $('.popup-block').hide();
            });

            $('.btn-prev').click(function (e) {
                console.log('previous');

                $('.alert-danger').hide();
                var floorId = $('#floor_id').val();
                var storeId = $('#store_id').val();
                if (floorId != '') {
                    if (floorId != 1) {
                        floorId = parseInt(floorId) - 1;
                        $.ajax({
                            type : "POST",
                            url  : AppTable.baseUrl + 'table/previous_floor',
                            data : {
                                floor_id : floorId,
                                store_id : storeId
                            }
                        }).done(function (resp) {
                            if (resp != '') {
                                var parsedObject = JSON.parse(resp);
                                $('#floor_id').val(parsedObject.floor_id);
                                $('#floor_name').text(parsedObject.floor_name);

                                var tableArr = parsedObject.data_table;

                                var htmlString = '';
                                for (i = 0; i < tableArr.length; i++) {
                                    htmlString += "<div class=\"table-list table2 status-" + tableArr[i].status_name + "\" style=\"top:" + tableArr[i].pos_y + "px;left:" + tableArr[i].pos_x + "%;\">" +
                                    "<p class=\"table-name\">" + tableArr[i].status_name + "</p>" +
                                    "<p class=\"table-status\">" + tableArr[i].table_status + "</p>" +
                                    "<input type=\"hidden\" id=\"table_id_selected\" value=\"" + tableArr[i].table_id + "\">" +
                                    "<input type=\"hidden\" id=\"number_guest_selected\" value=\"" + tableArr[i].customer_count + "\">" +
                                    "<input type=\"hidden\" id=\"table_status_selected\" value=\"" + tableArr[i].table_status + "\">" +
                                    "<input type=\"hidden\" id=\"order_id_selected\" value=\"" + tableArr[i].order_id + "\">" +
                                    "</div>"
                                }
                                $('.table-container').html(htmlString);
                            }
                        }).fail(function () {
                            alert('failed');
                        });
                    }
                    else
                        $('#floor_id').val(1);
                }
                e.preventDefault();
            });

            $('.btn-next').click(function (e) {
                console.log('next');
                $('.alert-danger').hide();
                var floorId = $('#floor_id').val();
                var storeId = $('#store_id').val();
                if (floorId != '') {
                    floorId = parseInt(floorId) + 1;

                    $.ajax({
                        type : "POST",
                        url  : AppTable.baseUrl + 'table/next_floor',
                        data : {
                            floor_id : floorId,
                            store_id : storeId
                        }
                    }).done(function (resp) {
                        console.log(resp);
                        if (resp != '') {
                            var parsedObject = JSON.parse(resp);
                            $('#floor_id').val(parsedObject.floor_id);
                            $('#floor_name').text(parsedObject.floor_name);

                            var tableArr = parsedObject.data_table;
                            console.log(tableArr);
                            var htmlString = '';
                            for (i = 0; i < tableArr.length; i++) {
                                htmlString += "<div class=\"table-list table2 status-" + tableArr[i].status_name + "\" style=\"top:" + tableArr[i].pos_y + "px;left:" + tableArr[i].pos_x + "%;\">" +
                                "<p class=\"table-name\">" + tableArr[i].status_name + "</p>" +
                                "<p class=\"table-status\">" + tableArr[i].table_status + "</p>" +
                                "<input type=\"hidden\" id=\"table_id_selected\" value=\"" + tableArr[i].table_id + "\">" +
                                "<input type=\"hidden\" id=\"number_guest_selected\" value=\"" + tableArr[i].customer_count + "\">" +
                                "<input type=\"hidden\" id=\"table_status_selected\" value=\"" + tableArr[i].table_status + "\">" +
                                "<input type=\"hidden\" id=\"order_id_selected\" value=\"" + tableArr[i].order_id + "\">" +
                                "</div>"
                            }
                            $('.table-container').html(htmlString);
                        }
                    }).fail(function () {
                        alert('failed');
                    });
                }
                e.preventDefault();
            });

            $('.btn-change-table').click(function (e) {
                if (AppTable.isChangeTable) {
                    AppTable.isChangeTable = false;
                    $(".table-container").find(".status-completed").css("background-color", "");
                    $(".table-container").find(".status-order").css("background-color", "");
                    $(".table-container").find(".status-waiting").css("background-color", "");
                    $(".table-container").find(".status-empty").css("background-color", "");
                    $(".btn-change-table").text('Pindahkan Meja');
                }
                else {
                    AppTable.isChangeTable = true;
                    AppTable.isSelectTableFirst = true;
                    console.log('change table');
                    $(".table-container").find(".status-empty").css("background-color", "red");
                    $(".btn-change-table").text('Batalkan');
                }
                e.preventDefault();
            });

            $('.new_order').on('click', function (e) {
                var url = $(this).attr('href');
                var new_order_url = $('#new_order_url').val();
                var number_guest = $(this).data("guest");
                var table_id = $('#new_table_id').val();
                var request = $.ajax({
                    type : 'POST',
                    url  : url,
                    data : {
                        'number_guest' : number_guest,
                        'table_status' : '1',
                        'table_id'     : table_id,
                        'order_id'     : '0'
                    }
                });
                request.done(function (msg) {
                    var parsedObject = JSON.parse(msg);

                    AppTable.socket.emit('cm_notify_new_order', {
                        number_guest : parsedObject.number_guest,
                        table_status : parsedObject.table_status,
                        status_name  : parsedObject.status_name,
                        table_id     : parsedObject.table_id,
                        order_id     : parsedObject.order_id,
                        room         : 'waiter'
                    });

                    window.location = new_order_url;

                });
                request.fail(function (jqXHR, textStatus) {
                });
                request.always(function () {
                });

                e.preventDefault();
            });

        }
    };
});