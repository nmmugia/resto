var express = require('express');
var app = express();
var server = app.listen(4312);
var io = require('socket.io').listen(server);

var users = [];
var checker_ip_address=[];
var tableUsedTemp = [];
console.log("Socket.IO server started");
io.on('connection', function (socket) {
    socket.emit('connected', {});

    socket.on('cm_auth', function (data) {
        var userObject = {
            nip       : data.nip,
            name      : data.name,
            role      : data.role,
            role_name : data.role_name,
            socket_id : socket.id
        };
        users.push(userObject);
        if(data.ip_address!=undefined){
            if(checker_ip_address.indexOf(data.ip_address)<0){
                checker_ip_address.push(data.ip_address);
            }
        }
        socket.join(data.role_name);
        socket.emit('sm_auth',{});
    });
    socket.on('cm_get_checker_ip_address', function () {
        socket.emit('sm_get_checker_ip_address', {
            checker_ip_address : checker_ip_address
        });
    });
    socket.on('cm_notify_new_order', function (data) {
        console.log('new order');
        socket.broadcast.emit('sm_notify_new_order', {
            number_guest : data.number_guest,
            table_status : data.table_status,
            status_name  : data.status_name,
            status_class : data.status_class,
            table_id     : data.table_id,
            table_name     : data.table_name,
            order_id     : data.order_id,
            arr_merge_table : data.arr_merge_table,
            warning_badge : data.warning_badge,
            arr_menu_outlet : data.arr_menu_outlet,
            floor_id      : data.floor_id,
            notification      : data.notification,
            outlets : data.outlets
        });
    });

    socket.on('cm_notify_new_order_to_all', function (data) {
        console.log('new order notify to all');
        io.sockets.emit('sm_notify_new_order', {
            number_guest : data.number_guest,
            table_status : data.table_status,
            status_name  : data.status_name,
            status_class : data.status_class,
            table_id     : data.table_id,
            order_id     : data.order_id,
            reservation_id     : data.reservation_id
        });
    });

    socket.on('cm_notify_cooking_status', function (data) {
      console.log('notify cooking status');
      socket.broadcast.emit('sm_notify_cooking_status', {
          order_menu_id  : data.order_menu_id,
          cooking_status : data.cooking_status,
          status_name    : data.status_name,
          table_id       : data.table_id,
          order_id       : data.order_id,
          arr_merge_table: data.arr_merge_table,
          notification : data.notification

      });
      if(data.refresh==undefined)data.refresh=true;
      if(data.refresh==true){
        io.sockets.emit('sm_notify_cooking_status_refresh', {});
      }
    });
    socket.on('cm_notify_checker', function (data) {
      console.log('notify to checker');
      socket.broadcast.emit('sm_notify_checker',{});
    });

    socket.on('cm_notify_menu_available_status', function (data) {
        console.log('notify menu available');
        socket.broadcast.emit('sm_notify_menu_available_status', {
            menu_id   : data.menu_id,
            menu_name : data.menu_name,
            available : data.available
        });
    });

    socket.on('cm_empty_table', function (data) {
        console.log('empty table');
        socket.broadcast.emit('sm_empty_table', {
            number_guest : data.number_guest,
            table_status : data.table_status,
            status_name  : data.status_name,
            status_class : data.status_class,
            table_id     : data.table_id,
            order_id     : data.order_id,
            url_redir    : data.url_redir,
            arr_merge_table : data.arr_merge_table,
            room        : data.room

        });
    });

    socket.on('cm_cancel_merge', function (data){
        console.log('cancel_merge_server');
        console.log(data);
        socket.broadcast.emit('sm_cancel_merge', {
            ids: data.ids
        });
    });

    socket.on('cm_notify_merge_table', function (data) {
        console.log('merge table');
        socket.broadcast.emit('sm_notify_merge_table', {
            parent_id    : data.parent_id,
            parent_name   : data.parent_name,
            order_id     : data.order_id,
            status_class   : data.status_class,
            status_name   : data.status_name,
            arr_merge_table : data.arr_merge_table
        });
    });
    socket.on('cm_notify_call', function (data) {
        console.log('panggil waiter');
        socket.broadcast.emit('sm_notify_call', {
          notification : data.notification
        });
    });
    socket.on('cm_notify_open_close_cashier', function (data) {
        console.log('cashier status :'+data.status);
        socket.broadcast.emit('sm_notify_open_close_cashier', {
          status : data.status
        });
    });


    socket.on('cm_post_reservation', function (data) {
        console.log('post reservation');
        socket.broadcast.emit('sm_post_reservation', {
            table_status : data.table_status,
            order_id     : data.order_id,
            table_id     : data.table_id,
            status_name  : data.status_name,
            reservation_id : data.reservation_id,
            status_class : data.status_class

        });
    });

    socket.on('cm_set_table_used', function (data) {
        var tableId = data.table_id;
        var isUsed = true;

        if (tableUsedTemp.indexOf(tableId) < 0) {
            tableUsedTemp.push(tableId);
            isUsed = false;
        }

        socket.emit('sm_set_table_used', {
            table_used  : tableUsedTemp,
            is_used     : isUsed,
            table_id    : tableId
        });
    });

    socket.on('cm_remove_table_used', function (data) {
        var tableId = data.table_id;
        var index = tableUsedTemp.indexOf(tableId);

        if (index > -1) {
            tableUsedTemp.splice(index, 1);
        }

        socket.emit('sm_remove_table_used', {
            table_used  : tableUsedTemp,
            table_id    : tableId
        });
    });

    socket.on('cm_new_reservation', function (data) {
        console.log('new reservation');
        
        socket.broadcast.emit('sm_new_reservation', {
            reservation_id: data.reservation_id,
            detail_order: data.detail_order,
            table_id:data.table_id,
            table_number:data.table_number,
            reservation_date:data.reservation_date,
            customer_name:data.customer_name,
            order_id:data.order_id,
            operator_name:data.operator_name

        });
    });

    socket.on('cm_edit_reservation', function (data) {
        console.log('edit reservation');
        socket.broadcast.emit('sm_edit_reservation', {
            reservation_id: data.reservation_id,
            detail_order: data.detail_order,
            table_id:data.table_id,
            table_number:data.table_number,
            reservation_date:data.reservation_date,
            customer_name:data.customer_name,
            order_id:data.order_id,
            operator_name:data.operator_name

        });
    });

    socket.on('cm_notify_change_table', function (data) {
        console.log('change table');
        socket.broadcast.emit('sm_notify_change_table', {
            number_guest:data.number_guest,
            table_name:data.table_name,
            table_id:data.table_id,
            order_id:data.order_id
        });
    });

});