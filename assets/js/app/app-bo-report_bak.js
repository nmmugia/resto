/**
 * Created by alta falconeri on 12/15/2014.
 */

define([
    "jquery",
    "jquery-ui",
    "chained",
    "metisMenu",
    "Morris",
    'datatables',
    "bootstrap",
    "datatables-bootstrap",
    "paging"

], function ($, ui) {
    return {
        baseUrl           : $('#root_base_url').val(),
        adminUrl          : $('#admin_url').val(),
        overlayUI         : $('#cover'),
        reportType        : $('#report_type').val(),
        sidedishCount     : document.getElementById("sidedishCount") != null ? document.getElementById("sidedishCount").value : 0,
        optionsCount      : document.getElementById("optionsCount") != null ? document.getElementById("optionsCount").value : 0,
        optionsValueCount : document.getElementById("optionsValueCount") != null ? document.getElementById("optionsValueCount").value : 0,
        // fabricJs          : fabricMain,
        init              : function () {
            // App.fabricJs.init();
            App.overlayUI.hide();
            App.initFunc(App);
            $('#side-menu').metisMenu();
            $(".date-input").datepicker({"dateFormat" : 'yy-mm-dd'});
            $(".date-input").val("");
            $(".def-select").val("0");
            Array.prototype.unique = function () {
                var a = this.concat();
                for (var i = 0; i < a.length; ++i) {
                    for (var j = i + 1; j < a.length; ++j) {
                        if (a[i] === a[j])
                            a.splice(j--, 1);
                    }
                }

                return a;
            };
            if (App.reportType == "sales") {
                App.initSalesUI();
            }
            else if (App.reportType == "customer") {
                $("#table_sel").val("all");
                App.initCustomerUI();
            }
            else if (App.reportType == "store") {
                App.initStoreUI();
            }
            else if (App.reportType == "staff") {
                App.initStaffUI();
            }else if(App.reportType == "historyStock"){
                App.iniHistoryStockUI();
            }else if(App.reportType == "ingredient"){
                App.initIngredientUI();
            }else if(App.reportType == "menu"){
                App.initMenuUI();
            }else if(App.reportType == "stock_opname"){
                App.initStockOpnameUI();
            }

        },
        initSalesUI       : function () {
            $("#filter_submit").on('click', function () {
                //TODO add client validation
                $.ajax({
                    url      : App.adminUrl + "/reports/get_sales_data",
                    type     : "POST",
                    dataType : "json",
                    data     : {
                        start_date : $("#start_date").val(),
                        end_date   : $("#end_date").val(),
                        month      : $("#month_sel").val(),
                        year       : $("#year_sel").val(),
                        store      : $("#store_sel").val(),
                        outlet     : $("#outlet_sel").val(),
                        payment_method : $("#payment_method").val()
                    },
                    success  : function (result) {
                        console.log(result);
                        if (result.data != null) {
                            $(".sales-table").find("tr:gt(0)").remove();
                            var totalItemCount = 0;
                            var totalPriceCount = 0;
                            var totalHpp = 0;
                            var totalStoreArray = [];
                            var totalOutletArray = [];
                            var takeawayCount = 0;
                            var dineinCount = 0;
                            $('#table-sales-hidden').attr('border', '1');
                            $('#start_date_hidden').text($("#start_date").val());
                            $('#end_date_hidden').text($("#end_date").val());
                            $('#month_hidden').text($("#month_sel option:selected").text());
                            $('#year_hidden').text($("#year_sel option:selected").text());
                            $('#store_hidden').text($("#store_sel option:selected").text());
                            $('#outlet_hidden').text($("#outlet_sel option:selected").text());
                            // $('#payment_method').text($("#payment_method option:selected").text());

                            $('.hide_btn').show();

                            for (i = 0; i < result.data.length; i++) {
                                // if(result.data[i].order_type == "Takeaway")
                                // takeawayCount++;
                                // else
                                // dineinCount++;

                                // remove column "NOMOR NOTA" if choose month or year
                                if ($("#month_sel").val() != '0' || $("#year_sel").val() != '0') {
                                    $('.hideme').hide();
                                    $('.showme').show();

                                    $('.custom-hidden-table').html('' +
                                    '<tr><th colspan="2">Tanggal Penjualan</th>' +
                                    '<th>Jumlah Item</th>' +
                                    '<th>Total</th>' +
                                    '<th>Resto</th>' +
                                    '<th>Outlet</th></tr>');

                                    $(".sales-table").append(
                                        $("<tr></tr>")
                                            .append($("<td colspan='2'>" + result.data[i].order_date + "</td>"))
                                            .append($("<td>" + result.data[i].item_count + "</td>"))
                                            .append($("<td>" + result.data[i].total_price_str + "</td>"))
                                            .append($("<td>" + result.data[i].stores.join(',') + "</td>"))
                                            .append($("<td>" + result.data[i].outlets.join(',') + "</td>"))
                                            .append($("<td>" + result.data[i].payment_method + "</td>"))

                                    );
                                } else {
                                    $('.hideme').show();
                                    $('.showme').hide();

                                    $('.custom-hidden-table').html('' +
                                    '<tr><th>Nomor Nota</th>' +
                                    '<th>Tanggal Penjualan</th>' +
                                    '<th>Jumlah Item</th>' +
                                    '<th>Total</th>' +
                                    '<th>Resto</th>' +
                                    '<th>Outlet</th>' +

                                    '<th>Metode Pembayaran</th></tr>');

                                    $(".sales-table").append(
                                        $("<tr></tr>").append($("<td>" + result.data[i].receipt_id + "</td>"))
                                            .append($("<td>" + result.data[i].order_date + "</td>"))
                                            .append($("<td>" + result.data[i].item_count + "</td>"))
                                            .append($("<td>" + result.data[i].total_price_str + "</td>"))
                                            .append($("<td>" + result.data[i].stores.join(',') + "</td>"))
                                            .append($("<td>" + result.data[i].outlets.join(',') + "</td>"))
                                            .append($("<td>" + result.data[i].payment_method + "</td>"))
                                            .append($('<td><div class="btn-group"><a class="btn btn-default" href="'+App.adminUrl+'/reports/get_sales_detail/'+result.data[i].id+'" target="_blank"><i class="fa fa-search"></i></a></div></td>'))

                                    );
                                }
                                totalItemCount += result.data[i].item_count;
                                takeawayCount += result.data[i].takeaway_count;
                                dineinCount += result.data[i].dinein_count;
                                totalPriceCount += parseInt(result.data[i].total_price);
                                totalHpp += parseInt(result.data[i].menu_hpp) + parseInt(result.data[i].side_dish_hpp);
                                totalStoreArray = totalStoreArray.concat(result.data[i].stores).unique();
                                totalOutletArray = totalOutletArray.concat(result.data[i].outlets).unique();
                            }


                            if (result.data.length == 0) {
                                $(".sales-table").append($('<tr><th colspan="8" align="center" valign="middle" >No Data</th></tr>'));
                            }
                            else {
                                //totals
                                if ($("#month_sel").val() != '0' || $("#year_sel").val() != '0') {
                                    $(".sales-table").append(
                                        $("<tr></tr>").append($("<td colspan='2'><b>Total</b></td>"))
                                            .append($("<td><b>" + totalItemCount + " Item</b></td>"))
                                            .append($("<td><b>Rp " + App.addCommas(totalPriceCount) + "</b></td>"))
                                            .append($("<td><b>" + totalStoreArray.length + " Resto</b></td>"))
                                            .append($("<td><b>" + totalOutletArray.length + " Outlets</b></td>"))
                                            .append($("<td></td>"))

                                    );
                                }
                                else {
                                    $(".sales-table").append(
                                        $("<tr></tr>").append($("<td><b>Total</b></td>"))
                                            .append($("<td><b>" + result.sales_count + " Sales</b></td>"))
                                            .append($("<td><b>" + totalItemCount + " Item</b></td>"))
                                            .append($("<td><b>Rp " + App.addCommas(totalPriceCount) + "</b></td>"))
                                            .append($("<td><b>" + totalStoreArray.length + " Resto</b></td>"))
                                            .append($("<td><b>" + totalOutletArray.length + " Outlets</b></td>"))
                                            .append($("<td></td>"))

                                    );
                                }
                                //recaps
                                $(".sales-table").append(
                                    $("<tr></tr>").append($("<td>Gross Expenses</td>"))
                                        .append($("<td colspan=6><b>Rp " + App.addCommas(totalHpp) + "</b></td>"))
                                );
                                $(".sales-table").append(
                                    $("<tr></tr>").append($("<td>Gross Revenue</td>"))
                                        .append($("<td colspan=6><b>Rp " + App.addCommas(totalPriceCount) + "</b></td>"))
                                );
                                $(".sales-table").append(
                                    $("<tr></tr>").append($("<td>Gross Profit</td>"))
                                        .append($("<td colspan=6><b>Rp " + App.addCommas(totalPriceCount - totalHpp) + "</b></td>"))
                                );
                                $(".sales-table").append(
                                    $("<tr></tr>").append($("<td>Total Dine In</td>"))
                                        .append($("<td colspan=6><b>" + dineinCount + "</b></td>"))
                                );
                                $(".sales-table").append(
                                    $("<tr></tr>").append($("<td>Total Take Away</td>"))
                                        .append($("<td colspan=6><b>" + takeawayCount + "</b></td>"))
                                );
                            }
                        }
                        else
                            alert(result.message);
                    }
                });
            });

            $("#export_pdf").on('click', function () {
                // console.log($('.panel-hidden').html());
                $.ajax({
                    url      : App.adminUrl + "/reports/export_report_to_pdf",
                    type     : "POST",
                    dataType : "json",
                    data     : {
                        // html_string:$('#sales-table').prop('outerHTML')
                        html_string : $('.panel-hidden').html(),
                        report      : 'sales'
                    },
                    success  : function (result) {
                        console.log(App.baseUrl + result);
                        if (result != '') {
                            // window.location=App.adminUrl+"download.php?filename="+result;
                            // window.location.href = App.baseUrl + result;
                            window.open(App.baseUrl + result, '_newtab')
                        }
                        else
                            alert('Export report gagal');
                    }
                });
            });

            $("#export_xls").on('click', function () {
                $.ajax({
                    url      : App.adminUrl + "/reports/export_report_to_xls",
                    type     : "POST",
                    dataType : "json",
                    data     : {
                        html_string : $('.panel-hidden').html(),
                        report      : 'sales'
                    },
                    success  : function (result) {
                        console.log(App.baseUrl + result);
                        if (result != '') {
                            window.open(App.baseUrl + result, '_newtab')
                        }
                        else {
                            alert('Export report gagal');
                        }
                    }
                });
            });
        },

        initCustomerUI    : function () {
            $("#filter_submit").on('click', function () {
                //TODO add client validation

                $.ajax({
                    url      : App.adminUrl + "/reports/get_customer_data",
                    type     : "POST",
                    dataType : "json",
                    data     : {
                        start_date : $("#start_date").val(),
                        end_date   : $("#end_date").val(),
                        month      : $("#month_sel").val(),
                        year       : $("#year_sel").val(),
                        store      : $("#store_sel").val(),
                        outlet     : $("#outlet_sel").val(),
                        table      : $("#table_sel").val()
                    },
                    success  : function (result) {
                        if (result.data != null) {
                            $(".sales-table").find("tr:gt(0)").remove();
                            var totalItemCount = 0;
                            var totalTimeStay = 0;
                            var countHour = 0;
                            var countMinute = 0;
                            var totalGuest = 0;
                            var totalPriceCount = 0;
                            var totalStoreArray = [];
                            var totalOutletArray = [];
                            var stringHours = '';
                            var stringMinutes = '';
                            $('#table-sales-hidden').attr('border', '1');
                            $('#start_date_hidden').text($("#start_date").val());
                            $('#end_date_hidden').text($("#end_date").val());
                            $('#month_hidden').text($("#month_sel option:selected").text());
                            $('#year_hidden').text($("#year_sel option:selected").text());
                            $('#store_hidden').text($("#store_sel option:selected").text());
                            $('#outlet_hidden').text($("#outlet_sel option:selected").text());
                            $('#table_hidden').text($("#table_sel option:selected").text());
                            $('.hide_btn').show();

                            for (i = 0; i < result.data.length; i++) {
                                if (result.data[i].order_type == "Takeaway") {
                                    totalTimeStay += 0;
                                    stringHours = '';
                                    stringMinutes = '';
                                }
                                else {
                                    for (j = 0; j < result.data[i].order_start.length; j++) {
                                        var thisDate = result.data[i].order_start[j];
                                        var thisDateT = thisDate.substr(0, 10) + "T" + thisDate.substr(11, 8);
                                        var jDateOrder = new Date(thisDateT);
                                        var thisDate = result.data[i].order_ends[j];
                                        var thisDateT = thisDate.substr(0, 10) + "T" + thisDate.substr(11, 8);
                                        var jDateEnd = new Date(thisDateT);
                                        var timeStay = 0;
                                        if (isNaN(jDateEnd)) {  // d.valueOf() could also work
                                            // date is not valid
                                            timeStay = 0;
                                        }
                                        else {
                                            // date is valid
                                            timeStay = Math.abs((jDateEnd.getTime()) - (jDateOrder.getTime()));
                                        }
                                        totalTimeStay += timeStay;

                                        // var stringTime = '';
                                        timeStayDate = new Date(timeStay);
                                        var h = timeStayDate.getUTCHours();
                                        var m = timeStayDate.getUTCMinutes();
                                        countHour += h;
                                        countMinute += m;
                                    }

                                    stringHours = timeStayDate.getUTCHours();
                                    stringMinutes = timeStayDate.getUTCMinutes();
                                    if (timeStayDate.getUTCHours() == 0)
                                        stringHours = '';
                                    else
                                        stringHours = timeStayDate.getUTCHours() + ' jam ';

                                    if (timeStayDate.getUTCMinutes() == 0)
                                        stringMinutes = '';
                                    else
                                        stringMinutes = timeStayDate.getUTCMinutes() + ' menit';
                                }
                                // console.log($("#month_sel").val());
                                if ($("#month_sel").val() != '0' || $("#year_sel").val() != '0') {
                                    $('.hideme').hide();
                                    $('.showme').show();

                                    $('.custom-hidden-table').html('' +
                                    '<tr><th>Tanggal</th>' +
                                    '<th>Jumlah Tamu</th>' +
                                    '<th>Lama Duduk</th>' +
                                    '<th>Jumlah Item</th>' +
                                    '<th>Resto</th>' +
                                    '<th>Outlet</th></tr>');

                                    $(".sales-table").append(
                                        $("<tr></tr>").append($("<td>" + result.data[i].order_date + "</td>"))
                                            .append($("<td>" + result.data[i].guest_count + "</td>"))
                                            .append($("<td>" + stringHours + stringMinutes + "</td>"))
                                            .append($("<td>" + result.data[i].item_count + "</td>"))
                                            .append($("<td>" + result.data[i].stores.join(',') + "</td>"))
                                            .append($("<td>" + result.data[i].outlets.join(',') + "</td>"))
                                    );
                                }
                                else {
                                    $('.hideme').show();
                                    $('.showme').hide();

                                    $('.custom-hidden-table').html('' +
                                    '<tr><th>Nomor Nota</th>' +
                                    '<th>Tanggal</th>' +
                                    '<th>Nama Meja</th>' +
                                    '<th>Jumlah Tamu</th>' +
                                    '<th>Lama Duduk</th>' +
                                    '<th>Jumlah Item</th>' +
                                    '<th>Resto</th>' +
                                    '<th>Outlet</th></tr>');

                                    $(".sales-table").append(
                                        $("<tr></tr>").append($("<td>" + result.data[i].receipt_id + "</td>"))
                                            .append($("<td>" + result.data[i].order_date + "</td>"))
                                            .append($("<td>" + result.data[i].table_name + "</td>"))
                                            .append($("<td>" + result.data[i].guest_count + "</td>"))
                                            .append($("<td>" + stringHours + stringMinutes + "</td>"))
                                            .append($("<td>" + result.data[i].item_count + "</td>"))
                                            .append($("<td>" + result.data[i].stores.join(',') + "</td>"))
                                            .append($("<td>" + result.data[i].outlets.join(',') + "</td>"))
                                    );
                                }

                                totalItemCount += result.data[i].item_count;
                                totalPriceCount += parseInt(result.data[i].total_price);
                                totalGuest += parseInt(result.data[i].guest_count);
                                totalStoreArray = totalStoreArray.concat(result.data[i].stores).unique();
                                totalOutletArray = totalOutletArray.concat(result.data[i].outlets).unique();
                            }


                            if (result.data.length == 0) {
                                $(".sales-table").append($('<tr><th colspan="6" align="center" valign="middle" >No Data</th></tr>'));
                            }
                            else {
                                var stringTimeDiv = '';
                                var stringTimeTotal = '';
                                var countDay = 0;
                                var timeDiv = 0;

                                var totTimeStay = (countHour * 3600000) + (countMinute * 60000);
                                if (totalTimeStay != 0) {
                                    var totHours = Math.floor((totTimeStay %= 86400) / 3600);
                                    var totMinutes = Math.floor((totTimeStay %= 3600) / 60);
                                    var timeStayNew = new Date(totalTimeStay);

                                    stringTimeTotal = totHours + " Jam, " + totMinutes + " Menit";

                                    var duration = totalTimeStay / totalGuest;
                                    var milliseconds = parseInt((duration % 1000) / 100)
                                        , seconds = parseInt((duration / 1000) % 60)
                                        , minutes = parseInt((duration / (1000 * 60)) % 60)
                                        , hours = parseInt((duration / (1000 * 60 * 60)) % 24);

                                    timeDiv = hours + ' jam ' + minutes + ' Menit';
                                    stringTimeDiv = '';
                                }
                                else {
                                    stringTimeTotal = "0 Jam, 0 Menit";
                                    timeDiv = 0;
                                    stringTimeDiv = ' Menit';
                                }
                                if ($("#start_date").val() != '' && $("#end_date").val() != '') {
                                    var startDate = new Date($("#start_date").val());
                                    var endDate = new Date($("#end_date").val());
                                    if (endDate >= startDate) {
                                        if (startDate.getTime() == endDate.getTime()) {
                                            countDay = 1;
                                        }
                                        else {
                                            var oneDay = 24 * 60 * 60 * 1000;
                                            countDay = Math.abs((endDate.getTime() - startDate.getTime()) / oneDay);
                                        }
                                    }
                                }
                                else if ($("#month_sel").val() != '') {
                                    countDay = 30;
                                }
                                else if ($("#year_sel").val() != '') {
                                    countDay = 365;
                                }

                                if ($("#month_sel").val() != '0' || $("#year_sel").val() != '0') {
                                    $(".sales-table").append(
                                        $("<tr></tr>").append($("<td><b>Total</b></td>"))
                                            .append($("<td><b>" + totalGuest + " Tamu</b></td>"))
                                            .append($("<td><b>" + stringTimeTotal + "</b></td>"))
                                            .append($("<td><b>" + totalItemCount + " Item</b></td>"))
                                            .append($("<td><b>" + totalStoreArray.length + " Resto</b></td>"))
                                            .append($("<td><b>" + totalOutletArray.length + " Outlets</b></td>"))
                                    );
                                }
                                else {
                                    $(".sales-table").append(
                                        $("<tr></tr>").append($("<td><b>Total</b></td>"))
                                            .append($("<td><b>" + '' + "</b></td>"))
                                            .append($("<td><b>" + '' + "</b></td>"))
                                            .append($("<td><b>" + totalGuest + " Tamu</b></td>"))
                                            .append($("<td><b>" + stringTimeTotal + "</b></td>"))
                                            .append($("<td><b>" + totalItemCount + " Item</b></td>"))
                                            .append($("<td><b>" + totalStoreArray.length + " Resto</b></td>"))
                                            .append($("<td><b>" + totalOutletArray.length + " Outlets</b></td>"))
                                    );
                                }
                                // recaps
                                $(".sales-table").append(
                                    $("<tr></tr>").append($("<td>Pengunjung Per Jam</td>"))
                                        .append($("<td colspan=5><b>" + Math.round(((totalGuest / countDay) / 12) * 1) / 1 + " Pengunjung</b></td>"))
                                );
                                $(".sales-table").append(
                                    $("<tr></tr>").append($("<td>Item Per Pengunjung</td>"))
                                        .append($("<td colspan=5><b>" + Math.round((totalItemCount / totalGuest) * 1) / 1 + " Item</b></td>"))
                                );
                                $(".sales-table").append(
                                    $("<tr></tr>").append($("<td>Rata-rata Duduk Pengunjung</td>"))
                                        //.append($("<td colspan=5><b>"+Math.round((timeDiv/totalGuest)*1)/1+stringTimeDiv+"</b></td>"))
                                        .append($("<td colspan=5><b>" + timeDiv + stringTimeDiv + "</b></td>"))
                                );
                                $(".sales-table").append(
                                    $("<tr></tr>").append($("<td>Total Revenue Per Pengunjung</td>"))
                                        .append($("<td colspan=5><b>Rp " + App.addCommas(Math.round((totalPriceCount / totalGuest) * 1) / 1) + "</b></td>"))
                                );
                            }


                        }
                        else
                            alert(result.message);
                    }
                });
            });

            $("#export_pdf").on('click', function () {
                // console.log($('.panel-hidden').html());
                $.ajax({
                    url      : App.adminUrl + "/reports/export_report_to_pdf",
                    type     : "POST",
                    dataType : "json",
                    data     : {
                        // html_string:$('#sales-table').prop('outerHTML')
                        html_string : $('.panel-hidden').html(),
                        report      : 'customer'
                    },
                    success  : function (result) {
                        console.log(App.baseUrl + result);
                        if (result != '') {
                            // window.location=App.adminUrl+"download.php?filename="+result;
                            // window.location.href = App.baseUrl + result;
                            window.open(App.baseUrl + result, '_newtab')
                        }
                        else
                            alert('Export report gagal');
                    }
                });
            });

            $("#export_xls").on('click', function () {
                $.ajax({
                    url      : App.adminUrl + "/reports/export_report_to_xls",
                    type     : "POST",
                    dataType : "json",
                    data     : {
                        html_string : $('.panel-hidden').html(),
                        report      : 'customer'
                    },
                    success  : function (result) {
                        console.log(App.baseUrl + result);
                        if (result != '') {
                            window.open(App.baseUrl + result, '_newtab')
                        }
                        else {
                            alert('Export report gagal');
                        }
                    }
                });
            });
        },

        iniHistoryStockUI   : function(){
            $("#filter_submit").on('click', function () {
              $.ajax({
                    url      : App.adminUrl + "/inventory/get_history_data",
                    type     : "POST",
                    dataType : "json",
                    data     : {
                        start_date : $("#start_date").val(),
                        end_date   : $("#end_date").val(),
                        month      : $("#month_sel").val(),
                        year       : $("#year_sel").val(),
                        store      : $("#store_sel").val(),
                        outlet     : $("#outlet_sel").val(),
                    },
                    success  : function (result) {
                        console.log(result);
                        if (result.data != null) {
                            $(".history-stock-table").find("tr:gt(0)").remove();
                           
                            $('#table-history-stock-hidden').attr('border', '1');
                            $('#start_date_hidden').text($("#start_date").val());
                            $('#end_date_hidden').text($("#end_date").val());
                            $('#month_hidden').text($("#month_sel option:selected").text());
                            $('#year_hidden').text($("#year_sel option:selected").text());
                            $('#outlet_hidden').text($("#outlet_sel option:selected").text());

                            $('.hide_btn').show();

                            for (i = 0; i < result.data.length; i++) {
                       
                                    $('.showme').show();

                                    $('.custom-hidden-table').html('' +
                                    '<tr><th colspan="2">Tanggal Penjualan</th>' +
                                    '<th>Nama bahan</th>' +
                                    '<th>Outlet</th></tr>');

                                    $(".history-stock-table").append(
                                        $("<tr></tr>")
                                            .append($("<td colspan='1'>" + result.data[i].date + "</td>"))
                                            .append($("<td>" + result.data[i].outlet_name + "</td>"))
                                            .append($("<td>" + result.data[i].name + "</td>"))
                                            .append($("<td>" + result.data[i].total + "</td>"))
                                            .append($("<td>" + result.data[i].unit + "</td>"))

                                    );                                
                           
                            }

                            if (result.data.length == 0) {
                                $(".history-stock-table").append($('<tr><th colspan="6" align="center" valign="middle" >No Data</th></tr>'));
                            }
                            
                        }
                        else
                            alert(result.message);
                    }
                });
             });
        },
        initMenuUI       : function () {
            $("#filter_submit").on('click', function () {
                //TODO add client validation
                $.ajax({
                    url      : App.adminUrl + "/reports/get_menu_data",
                    type     : "POST",
                    dataType : "json",
                    data     : {
                        start_date : $("#start_date").val(),
                        end_date   : $("#end_date").val(),
                        month      : $("#month_sel").val(),
                        year       : $("#year_sel").val(),
                        store      : $("#store_sel").val(),
                        outlet     : $("#outlet_sel").val(),
                    },
                    success  : function (result) {
                        console.log(result.data);
                        if (result.data != null) {
                            $(".sales-table").find("tr:gt(0)").remove();
                            var totalItemCount = 0;
                            var totalPriceCount = 0;
                            var totalHpp = 0;
                            var totalMenuArray = [];
                            var totalOutletArray = [];
                            var takeawayCount = 0;
                            var dineinCount = 0;
                            $('#table-sales-hidden').attr('border', '1');
                            $('#start_date_hidden').text($("#start_date").val());
                            $('#end_date_hidden').text($("#end_date").val());
                            $('#month_hidden').text($("#month_sel option:selected").text());
                            $('#year_hidden').text($("#year_sel option:selected").text());
                            $('#store_hidden').text($("#store_sel option:selected").text());
                            $('#outlet_hidden').text($("#outlet_sel option:selected").text());
                            // $('#payment_method').text($("#payment_method option:selected").text());

                            $('.hide_btn').show();

                            for (i = 0; i < result.data.length; i++) {
                            
                                    $('.hideme').hide();
                                    $('.showme').show();

                                    $('.custom-hidden-table').html('' +
                                    '<th>Resto</th>' +
                                    '<tr>Outlet</th>' +
                                    '<th>Menu</th>' +
                                    '<th>Jumlah Terjual</th>' +
                                    '<th>Profit per item</th>' +
                                    '<th>Total Profit</th></tr>');

                                    $(".sales-table").append(
                                        $("<tr></tr>")
                                            .append($("<td>" + result.data[i].store_name + "</td>"))
                                            .append($("<td>" + result.data[i].outlet_name + "</td>"))
                                            .append($("<td>" + result.data[i].menu_name + "</td>"))
                                            .append($("<td>" + result.data[i].menu_count + "</td>"))
                                            .append($("<td>Rp " + App.addCommas(result.data[i].gross_profit_item) + "</td>"))
                                            .append($("<td>Rp " + App.addCommas(result.data[i].gross_profit) + "</td>"))

                                    );
                               
                                totalItemCount += result.data[i].menu_count;
                                totalMenuArray = totalMenuArray.concat(result.data[i].stores).unique();
                                
                            }

                            var menu_count = 0;
                            if (result.data.length == 0) {
                                $(".sales-table").append($('<tr><th colspan="6" align="center" valign="middle" >No Data</th></tr>'));
                            }
                            else {
                                //totals
                                    $(".sales-table").append(
                                        $("<tr></tr>").append($("<td><b>Total</b></td>"))
                                            .append($("<td><b>" + menu_count + " Item</b></td>"))
                                            .append($("<td><b>Rp " + totalItemCount + "</b></td>"))
                                            .append($("<td><b>" + totalMenuArray.length + " Resto</b></td>"))
                                            .append($("<td><b>" + totalMenuArray.length + " Outlets</b></td>"))
                                            .append($("<td></td>"))

                                    );
                               
                                //recaps
                                $(".sales-table").append(
                                    $("<tr></tr>").append($("<td>Gross Expenses</td>"))
                                        .append($("<td colspan=5><b>Rp " + App.addCommas(totalHpp) + "</b></td>"))
                                );
                                $(".sales-table").append(
                                    $("<tr></tr>").append($("<td>Gross Revenue</td>"))
                                        .append($("<td colspan=5><b>Rp " + App.addCommas(totalPriceCount) + "</b></td>"))
                                );
                                $(".sales-table").append(
                                    $("<tr></tr>").append($("<td>Gross Profit</td>"))
                                        .append($("<td colspan=5><b>Rp " + App.addCommas(totalPriceCount - totalHpp) + "</b></td>"))
                                );
                                $(".sales-table").append(
                                    $("<tr></tr>").append($("<td>Total Dine In</td>"))
                                        .append($("<td colspan=5><b>" + dineinCount + "</b></td>"))
                                );
                                $(".sales-table").append(
                                    $("<tr></tr>").append($("<td>Total Take Away</td>"))
                                        .append($("<td colspan=5><b>" + takeawayCount + "</b></td>"))
                                );
                            }
                        }
                        else
                            alert(result.message);
                    }
                });
            });

            $("#export_pdf").on('click', function () {
                // console.log($('.panel-hidden').html());
                $.ajax({
                    url      : App.adminUrl + "/reports/export_report_to_pdf",
                    type     : "POST",
                    dataType : "json",
                    data     : {
                        // html_string:$('#sales-table').prop('outerHTML')
                        html_string : $('.panel-hidden').html(),
                        report      : 'menu'
                    },
                    success  : function (result) {
                        console.log(App.baseUrl + result);
                        if (result != '') {
                            // window.location=App.adminUrl+"download.php?filename="+result;
                            // window.location.href = App.baseUrl + result;
                            window.open(App.baseUrl + result, '_newtab')
                        }
                        else
                            alert('Export report gagal');
                    }
                });
            });

            $("#export_xls").on('click', function () {
                $.ajax({
                    url      : App.adminUrl + "/reports/export_report_to_xls",
                    type     : "POST",
                    dataType : "json",
                    data     : {
                        html_string : $('.panel-hidden').html(),
                        report      : 'menu'
                    },
                    success  : function (result) {
                        console.log(App.baseUrl + result);
                        if (result != '') {
                            window.open(App.baseUrl + result, '_newtab')
                        }
                        else {
                            alert('Export report gagal');
                        }
                    }
                });
            });
        },
        initStockOpnameUI : function(){
            $("#filter_submit").on('click', function () {
                //TODO add client validation
                $.ajax({
                    url      : App.adminUrl + "/reports/get_stock_opname_data",
                    type     : "POST",
                    dataType : "json",
                    data     : {
                        start_date : $("#start_date").val(),
                        end_date   : $("#end_date").val(),
                        month      : $("#month_sel").val(),
                        year       : $("#year_sel").val(),
                        outlet     : $("#outlet_sel").val()
                    },
                    success  : function (result) {
                        if (result.data != null) {
                            $(".sales-table").find("tr:gt(0)").remove();
                            var totalItemCount = 0;
                            var totalGuest = 0;
                            var totalPriceCount = 0;
                            var totalHpp = 0;
                            var totalStoreArray = [];
                            $('#table-sales-hidden').attr('border', '1');
                            $('#start_date_hidden').text($("#start_date").val());
                            $('#end_date_hidden').text($("#end_date").val());
                            $('#month_hidden').text($("#month_sel option:selected").text());
                            $('#year_hidden').text($("#year_sel option:selected").text());
                            $('#outlet_hidden').text($("#outlet_sel option:selected").text());

                            $('.hide_btn').show();

                            for (i = 0; i < result.data.length; i++) {
                                var grossExpense = parseInt(result.data[i].menu_hpp) + parseInt(result.data[i].side_dish_hpp);
                                var grossRevenue = parseInt(result.data[i].total_price);
                                var grossProfit = grossRevenue - grossExpense;

                                $(".sales-table").append(
                                    $("<tr></tr>").append($("<td>" + result.data[i].order_date + "</td>"))
                                        .append($("<td>" + result.data[i].outlet_name + "</td>"))
                                        .append($("<td>" + result.data[i].inventory_name + "</td>"))
                                        .append($("<td>" + result.data[i].total_used + "</td>"))
                                        .append($("<td>" + result.data[i].total_stock + "</td>"))
                                        .append($("<td>" + result.data[i].unit + "</td>"))
                                );
                                totalItemCount += result.data[i].item_count;
                                totalGuest += parseInt(result.data[i].guest_count);
                                totalPriceCount += parseInt(result.data[i].total_price);
                                totalHpp += parseInt(result.data[i].menu_hpp) + parseInt(result.data[i].side_dish_hpp);
                                totalStoreArray = totalStoreArray.concat(result.data[i].stores).unique();
                            }

                            if (result.data.length == 0) {
                                $(".sales-table").append($('<tr><th colspan="6" align="center" valign="middle" >No Data</th></tr>'));
                            }
                            else {
                                // totals
                                // $(".sales-table").append(
                                //     $("<tr></tr>").append($("<td><b>Total</b></td>"))
                                //         .append($("<td><b>" + totalStoreArray.length + " Resto</b></td>"))
                                //         .append($("<td><b>" + totalGuest + " Tamu</b></td>"))
                                //         .append($("<td><b>" + totalItemCount + " Item</b></td>"))
                                //         .append($("<td><b>Rp " + App.addCommas(totalHpp) + "</b></td>"))
                                //         .append($("<td><b>Rp " + App.addCommas(totalPriceCount) + "</b></td>"))
                                //         .append($("<td><b>Rp " + App.addCommas(totalPriceCount - totalHpp) + "</b></td>"))
                                // );
                                // // recaps
                                // $(".sales-table").append(
                                //     $("<tr></tr>").append($("<td>Average Gross Expense Per Resto</td>"))
                                //         .append($("<td colspan=5><b>Rp " + App.addCommas(Math.round((totalHpp / totalStoreArray.length) * 1) / 1) + "</b></td>"))
                                // );
                                // $(".sales-table").append(
                                //     $("<tr></tr>").append($("<td>Average Gross Revenue Per Resto</td>"))
                                //         .append($("<td colspan=5><b>Rp " + App.addCommas(Math.round((totalPriceCount / totalStoreArray.length) * 1) / 1) + "</b></td>"))
                                // );
                                // $(".sales-table").append(
                                //     $("<tr></tr>").append($("<td>Average Gross Profit Per Resto</td>"))
                                //         .append($("<td colspan=5><b>Rp " + App.addCommas(Math.round(((totalPriceCount - totalHpp) / totalStoreArray.length) * 1) / 1) + "</b></td>"))
                                // );
                            }
                        }
                        else
                            alert(result.message);
                    }
                });
            });

            $("#export_pdf").on('click', function () {
                // console.log($('.panel-hidden').html());
                $.ajax({
                    url      : App.adminUrl + "/reports/export_report_to_pdf",
                    type     : "POST",
                    dataType : "json",
                    data     : {
                        // html_string:$('#sales-table').prop('outerHTML')
                        html_string : $('.panel-hidden').html(),
                        report      : 'store'
                    },
                    success  : function (result) {
                        console.log(App.baseUrl + result);
                        if (result != '') {
                            // window.location=App.adminUrl+"download.php?filename="+result;
                            // window.location.href = App.baseUrl + result;
                            window.open(App.baseUrl + result, '_newtab')
                        }
                        else
                            alert('Export report gagal');
                    }
                });
            });

            $("#export_xls").on('click', function () {
                $.ajax({
                    url      : App.adminUrl + "/reports/export_report_to_xls",
                    type     : "POST",
                    dataType : "json",
                    data     : {
                        html_string : $('.panel-hidden').html(),
                        report      : 'store'
                    },
                    success  : function (result) {
                        console.log(App.baseUrl + result);
                        if (result != '') {
                            window.open(App.baseUrl + result, '_newtab')
                        }
                        else {
                            alert('Export report gagal');
                        }
                    }
                });
            });

        },
        addCommas         : function (nStr) {
            nStr += '';
            x = nStr.split('.');
            x1 = x[0];
            x2 = x.length > 1 ? '.' + x[1] : '';
            var rgx = /(\d+)(\d{3})/;
            while (rgx.test(x1)) {
                x1 = x1.replace(rgx, '$1' + '.' + '$2');
            }
            return x1 + x2;
        },
        add_option        : function (e, count) {


        },
        buttonSubmitEvent : function () {

        },
        tableCanvas       : function () {


        },
        initStoreUI       : function () {
            $("#filter_submit").on('click', function () {
                //TODO add client validation
                $.ajax({
                    url      : App.adminUrl + "/reports/get_store_data",
                    type     : "POST",
                    dataType : "json",
                    data     : {
                        start_date : $("#start_date").val(),
                        end_date   : $("#end_date").val(),
                        month      : $("#month_sel").val(),
                        year       : $("#year_sel").val(),
                        store      : $("#store_sel").val(),
                        outlet     : $("#outlet_sel").val()
                    },
                    success  : function (result) {
                        if (result.data != null) {
                            $(".sales-table").find("tr:gt(0)").remove();
                            var totalItemCount = 0;
                            var totalGuest = 0;
                            var totalPriceCount = 0;
                            var totalHpp = 0;
                            var totalStoreArray = [];
                            $('#table-sales-hidden').attr('border', '1');
                            $('#start_date_hidden').text($("#start_date").val());
                            $('#end_date_hidden').text($("#end_date").val());
                            $('#month_hidden').text($("#month_sel option:selected").text());
                            $('#year_hidden').text($("#year_sel option:selected").text());
                            $('#store_hidden').text($("#store_sel option:selected").text());
                            $('#outlet_hidden').text($("#outlet_sel option:selected").text());

                            $('.hide_btn').show();

                            for (i = 0; i < result.data.length; i++) {
                                var grossExpense = parseInt(result.data[i].menu_hpp) + parseInt(result.data[i].side_dish_hpp);
                                var grossRevenue = parseInt(result.data[i].total_price);
                                var grossProfit = grossRevenue - grossExpense;

                                $(".sales-table").append(
                                    $("<tr></tr>").append($("<td>" + result.data[i].order_date + "</td>"))
                                        .append($("<td>" + result.data[i].stores.join(',') + "</td>"))
                                        .append($("<td>" + result.data[i].guest_count + "</td>"))
                                        .append($("<td>" + result.data[i].item_count + "</td>"))
                                        .append($("<td>Rp " + App.addCommas(grossExpense) + "</td>"))
                                        .append($("<td>Rp " + App.addCommas(grossRevenue) + "</td>"))
                                        .append($("<td>Rp " + App.addCommas(grossProfit) + "</td>"))
                                );
                                totalItemCount += result.data[i].item_count;
                                totalGuest += parseInt(result.data[i].guest_count);
                                totalPriceCount += parseInt(result.data[i].total_price);
                                totalHpp += parseInt(result.data[i].menu_hpp) + parseInt(result.data[i].side_dish_hpp);
                                totalStoreArray = totalStoreArray.concat(result.data[i].stores).unique();
                            }

                            if (result.data.length == 0) {
                                $(".sales-table").append($('<tr><th colspan="6" align="center" valign="middle" >No Data</th></tr>'));
                            }
                            else {
                                // totals
                                $(".sales-table").append(
                                    $("<tr></tr>").append($("<td><b>Total</b></td>"))
                                        .append($("<td><b>" + totalStoreArray.length + " Resto</b></td>"))
                                        .append($("<td><b>" + totalGuest + " Tamu</b></td>"))
                                        .append($("<td><b>" + totalItemCount + " Item</b></td>"))
                                        .append($("<td><b>Rp " + App.addCommas(totalHpp) + "</b></td>"))
                                        .append($("<td><b>Rp " + App.addCommas(totalPriceCount) + "</b></td>"))
                                        .append($("<td><b>Rp " + App.addCommas(totalPriceCount - totalHpp) + "</b></td>"))
                                );
                                // recaps
                                $(".sales-table").append(
                                    $("<tr></tr>").append($("<td>Average Gross Expense Per Resto</td>"))
                                        .append($("<td colspan=5><b>Rp " + App.addCommas(Math.round((totalHpp / totalStoreArray.length) * 1) / 1) + "</b></td>"))
                                );
                                $(".sales-table").append(
                                    $("<tr></tr>").append($("<td>Average Gross Revenue Per Resto</td>"))
                                        .append($("<td colspan=5><b>Rp " + App.addCommas(Math.round((totalPriceCount / totalStoreArray.length) * 1) / 1) + "</b></td>"))
                                );
                                $(".sales-table").append(
                                    $("<tr></tr>").append($("<td>Average Gross Profit Per Resto</td>"))
                                        .append($("<td colspan=5><b>Rp " + App.addCommas(Math.round(((totalPriceCount - totalHpp) / totalStoreArray.length) * 1) / 1) + "</b></td>"))
                                );
                            }
                        }
                        else
                            alert(result.message);
                    }
                });
            });

            $("#export_pdf").on('click', function () {
                // console.log($('.panel-hidden').html());
                $.ajax({
                    url      : App.adminUrl + "/reports/export_report_to_pdf",
                    type     : "POST",
                    dataType : "json",
                    data     : {
                        // html_string:$('#sales-table').prop('outerHTML')
                        html_string : $('.panel-hidden').html(),
                        report      : 'store'
                    },
                    success  : function (result) {
                        console.log(App.baseUrl + result);
                        if (result != '') {
                            // window.location=App.adminUrl+"download.php?filename="+result;
                            // window.location.href = App.baseUrl + result;
                            window.open(App.baseUrl + result, '_newtab')
                        }
                        else
                            alert('Export report gagal');
                    }
                });
            });

            $("#export_xls").on('click', function () {
                $.ajax({
                    url      : App.adminUrl + "/reports/export_report_to_xls",
                    type     : "POST",
                    dataType : "json",
                    data     : {
                        html_string : $('.panel-hidden').html(),
                        report      : 'store'
                    },
                    success  : function (result) {
                        console.log(App.baseUrl + result);
                        if (result != '') {
                            window.open(App.baseUrl + result, '_newtab')
                        }
                        else {
                            alert('Export report gagal');
                        }
                    }
                });
            });

        },
        initStaffUI       : function () {
            $("#filter_submit").on('click', function () {
                //TODO add client validation
                $.ajax({
                    url      : App.adminUrl + "/reports/get_staff_data",
                    type     : "POST",
                    dataType : "json",
                    data     : {
                        start_date : $("#start_date").val(),
                        end_date   : $("#end_date").val(),
                        month      : $("#month_sel").val(),
                        year       : $("#year_sel").val(),
                        store      : $("#store_sel").val(),
                        staff      : $("#staff_sel").val(),
                        role       : $("#role_sel").val()
                    },
                    success  : function (result) {
                        if (result.data != null) {
                            $(".sales-table").find("tr:gt(0)").remove();
                            var totalItemCount = 0;
                            var totalGuest = 0;
                            var totalStaff = 0;
                            var arrayStaff = [];
                            var arrayCashier = [];
                            var countWaiter = 0;
                            var countCashier = 0;
                            var arrayIdStaff = [];
                            var arrayIdWaiter = [];
                            var arrayIdCashier = [];
                            $('#table-sales-hidden').attr('border', '1');
                            $('#start_date_hidden').text($("#start_date").val());
                            $('#end_date_hidden').text($("#end_date").val());
                            $('#month_hidden').text($("#month_sel option:selected").text());
                            $('#year_hidden').text($("#year_sel option:selected").text());
                            $('#store_hidden').text($("#store_sel option:selected").text());
                            $('#outlet_hidden').text($("#outlet_sel option:selected").text());

                            $('.hide_btn').show();

                            for (i = 0; i < result.data.length; i++) {
                                if (result.data[i].waiter_id == result.data[i].cashier_id) {
                                    arrayIdStaff.push(result.data[i].cashier_id);

                                    var role = '';
                                    var name = '';
                                    if (result.data[i].waiter_data) {
                                        role = result.data[i].waiter_data.role;
                                        name = result.data[i].waiter_data.name;
                                        countWaiter += 1;
                                        arrayIdWaiter.push(result.data[i].waiter_id);
                                    }
                                    else {
                                        role = result.data[i].cashier_data.role;
                                        name = result.data[i].cashier_data.name;
                                        countCashier += 1;
                                        arrayIdCashier.push(result.data[i].cashier_id);
                                    }
                                    $(".sales-table").append(
                                        $("<tr></tr>").append($("<td>" + result.data[i].order_date + "</td>"))
                                            .append($("<td>" + name + "</td>"))
                                            .append($("<td>" + result.data[i].guest_count + "</td>"))
                                            // .append($("<td>"+0+"</td>"))
                                            .append($("<td>" + role + "</td>"))
                                    );
                                }
                                else {
                                    if (result.data[i].waiter_data) {
                                        arrayIdStaff.push(result.data[i].waiter_id);
                                        arrayIdWaiter.push(result.data[i].waiter_id);

                                        role = result.data[i].waiter_data.role;
                                        countWaiter += 1;
                                        $(".sales-table").append(
                                            $("<tr></tr>").append($("<td>" + result.data[i].order_date + "</td>"))
                                                .append($("<td>" + result.data[i].waiter_data.name + "</td>"))
                                                .append($("<td>" + result.data[i].guest_count + "</td>"))
                                                // .append($("<td>"+0+"</td>"))
                                                .append($("<td>" + result.data[i].waiter_data.role + "</td>"))
                                        );
                                    }
                                    if (result.data[i].cashier_data) {
                                        arrayIdStaff.push(result.data[i].cashier_id);
                                        arrayIdCashier.push(result.data[i].cashier_id);

                                        role = result.data[i].cashier_data.role;
                                        countCashier += 1;
                                        $(".sales-table").append(
                                            $("<tr></tr>").append($("<td>" + result.data[i].order_date + "</td>"))
                                                .append($("<td>" + result.data[i].cashier_data.name + "</td>"))
                                                .append($("<td>" + result.data[i].guest_count + "</td>"))
                                                // .append($("<td>"+0+"</td>"))
                                                .append($("<td>" + result.data[i].cashier_data.role + "</td>"))
                                        );
                                    }

                                }

                                if (arrayStaff.length != 0) {
                                    for (j = 0; j < arrayStaff.length; j++) {
                                        if (result.data[i].waiter_id != arrayStaff[j])
                                            arrayStaff.push(result.data[i].waiter_id);
                                        else
                                            break;
                                    }
                                }
                                else
                                    arrayStaff.push(result.data[i].waiter_id);

                                if (arrayCashier.length != 0) {
                                    for (k = 0; k < arrayCashier.length; k++) {
                                        if (result.data[i].cashier_id != arrayCashier[k])
                                            arrayCashier.push(result.data[i].cashier_id);
                                    }
                                }
                                else
                                    arrayCashier.push(result.data[i].cashier_id);

                                totalItemCount += 0;
                                totalGuest += parseInt(result.data[i].guest_count);
                            }

                            var stringAvWaiter = '';
                            var stringAvCashier = '';
                            if (App.uniqueArray(arrayIdWaiter).length == 0) {
                                stringAvWaiter = 0;
                            }
                            else
                                stringAvWaiter = Math.round((totalGuest / App.uniqueArray(arrayIdWaiter).length) * 1) / 1;

                            if (App.uniqueArray(arrayIdCashier).length == 0) {
                                stringAvCashier = 0;
                            }
                            else
                                stringAvCashier = Math.round((totalGuest / App.uniqueArray(arrayIdCashier).length) * 1) / 1;

                            if (result.data.length == 0) {
                                $(".sales-table").append($('<tr><th colspan="6" align="center" valign="middle" >No Data</th></tr>'));
                            }
                            else {
                                // totals
                                $(".sales-table").append(
                                    $("<tr></tr>").append($("<td><b>Total</b></td>"))
                                        .append($("<td><b>" + (App.uniqueArray(arrayIdStaff).length) + " Staff</b></td>"))
                                        .append($("<td><b>" + totalGuest + " Tamu</b></td>"))
                                        // .append($("<td><b>"+totalItemCount+" Item</b></td>"))
                                        //.append($("<td><b>" + (App.uniqueArray(arrayIdWaiter).length) + " Waiter, " + (App.uniqueArray(arrayIdCashier).length) + " Kasir, " + 0 + " Kitchen" + "</b></td>"))
                                        .append($("<td><b>" + (App.uniqueArray(arrayIdWaiter).length) + " Waiter, " + (App.uniqueArray(arrayIdCashier).length) + " Kasir, " + "</b></td>"))
                                );
                                // recaps
                                $(".sales-table").append(
                                    $("<tr></tr>").append($("<td>Average Guest Per Waiter</td>"))
                                        .append($("<td colspan=5><b>" + stringAvWaiter + "</b></td>"))
                                );
                                $(".sales-table").append(
                                    $("<tr></tr>").append($("<td>Average Guest Per Kasir</td>"))
                                        .append($("<td colspan=5><b>" + stringAvCashier + "</b></td>"))
                                );
                                /*$(".sales-table").append(
                                 $("<tr></tr>").append($("<td>Average Guest Per Chef</td>"))
                                 .append($("<td colspan=5><b>" + 0 + "</b></td>"))
                                 );*/
                            }
                        }
                        else
                            alert(result.message);
                    }
                });
            });

            $("#export_pdf").on('click', function () {
                // console.log($('.panel-hidden').html());
                $.ajax({
                    url      : App.adminUrl + "/reports/export_report_to_pdf",
                    type     : "POST",
                    dataType : "json",
                    data     : {
                        // html_string:$('#sales-table').prop('outerHTML')
                        html_string : $('.panel-hidden').html(),
                        report      : 'staff'
                    },
                    success  : function (result) {
                        console.log(App.baseUrl + result);
                        if (result != '') {
                            // window.location=App.adminUrl+"download.php?filename="+result;
                            // window.location.href = App.baseUrl + result;
                            window.open(App.baseUrl + result, '_newtab')
                        }
                        else
                            alert('Export report gagal');
                    }
                });
            });

            $("#export_xls").on('click', function () {
                $.ajax({
                    url      : App.adminUrl + "/reports/export_report_to_xls",
                    type     : "POST",
                    dataType : "json",
                    data     : {
                        html_string : $('.panel-hidden').html(),
                        report      : 'staff'
                    },
                    success  : function (result) {
                        console.log(App.baseUrl + result);
                        if (result != '') {
                            window.open(App.baseUrl + result, '_newtab')
                        }
                        else {
                            alert('Export report gagal');
                        }
                    }
                });
            });
        },
        initIngredientUI       : function () {
            $("#filter_submit").on('click', function () {
                //TODO add client validation
                $.ajax({
                    url      : App.adminUrl + "/reports/get_ingredient_data",
                    type     : "POST",
                    dataType : "json",
                    data     : {
                        start_date : $("#start_date").val(),
                        end_date   : $("#end_date").val(),
                        month      : $("#month_sel").val(),
                        year       : $("#year_sel").val(),
                        outlet     : $("#outlet_sel").val(),
                        inventory     : $("#inventory_sel").val()
                    },
                    success  : function (result) {
                        if (result.data != null) {
                            $(".sales-table").find("tr:gt(0)").remove();
                            var totalItemCount = 0;
                            var totalGuest = 0;
                            var totalPriceCount = 0;
                            var totalHpp = 0;
                            var totalStoreArray = [];
                            $('#table-sales-hidden').attr('border', '1');
                            $('#start_date_hidden').text($("#start_date").val());
                            $('#end_date_hidden').text($("#end_date").val());
                            $('#month_hidden').text($("#month_sel option:selected").text());
                            $('#year_hidden').text($("#year_sel option:selected").text());
                            $('#outlet_hidden').text($("#outlet_sel option:selected").text());
                            $('#inventory_hidden').text($("#outlet_sel option:selected").text());

                            $('.hide_btn').show();

                            $("#btn_prev").on('click', function () {
                                prevPage();
                            });
                            $("#btn_next").on('click', function () {
                             nextPage();

                         });
                            for (i = 0; i < result.data.length; i++) {
                                var grossExpense = parseInt(result.data[i].menu_hpp) + parseInt(result.data[i].side_dish_hpp);
                                var grossRevenue = parseInt(result.data[i].total_price);
                                var grossProfit = grossRevenue - grossExpense;
                                
                                $(".sales-table").append(
                                    $("<tr></tr>").append($("<td>" + result.data[i].order_date + "</td>"))
                                        .append($("<td>" + result.data[i].outlet_name + "</td>"))
                                        .append($("<td>" + result.data[i].inventory_name + "</td>"))
                                        .append($("<td>" + result.data[i].total_used + "</td>"))
                                        .append($("<td>" + result.data[i].total_stock + "</td>"))
                                        .append($("<td>" + result.data[i].total_stock_opname + "</td>"))
                                        .append($("<td>" + parseInt(result.data[i].delta_stock) + "</td>"))
                                        .append($("<td>" + result.data[i].unit + "</td>"))
                                );
                                totalItemCount += result.data[i].item_count;
                                totalGuest += parseInt(result.data[i].guest_count);
                                totalPriceCount += parseInt(result.data[i].total_price);
                                totalHpp += parseInt(result.data[i].menu_hpp) + parseInt(result.data[i].side_dish_hpp);
                                totalStoreArray = totalStoreArray.concat(result.data[i].stores).unique();
                            }


                            if (result.data.length == 0) {
                                $(".sales-table").append($('<tr><th colspan="8" align="center" valign="middle" >No Data</th></tr>'));
                                $("#paging-report").pagination('destroy');
                        }
                            else {
                                var items = $(".sales-table.table-show tbody tr");
                                var perPage = 10;
                                var numItems = Math.floor(items.length / perPage);
                                var numItems = items.length ;
                                 // only show the first 2 (or "first per_page") items initially
                                items.slice(perPage).hide();
                                 $("#paging-report").pagination({
                                    items: numItems,
                                    itemsOnPage: perPage,
                                    cssStyle: 'light-theme',
                                    onPageClick: function(pageNumber) { 
                                        var showFrom = perPage * (pageNumber - 1);
                                        var showTo = showFrom + perPage;
                                        items.hide() // first hide everything, then show for the new page
                                        .slice(showFrom, showTo).show();
                                    }

                                });
                            }
                        }
                        else
                            alert(result.message);
                    }
                });
            });


            $("#export_pdf").on('click', function () {
                // console.log($('.panel-hidden').html());
                $.ajax({
                    url      : App.adminUrl + "/reports/export_report_to_pdf",
                    type     : "POST",
                    dataType : "json",
                    data     : {
                        // html_string:$('#sales-table').prop('outerHTML')
                        html_string : $('.panel-hidden').html(),
                        report      : 'store'
                    },
                    success  : function (result) {
                        console.log(App.baseUrl + result);
                        if (result != '') {
                            // window.location=App.adminUrl+"download.php?filename="+result;
                            // window.location.href = App.baseUrl + result;
                            window.open(App.baseUrl + result, '_newtab')
                        }
                        else
                            alert('Export report gagal');
                    }
                });
            });

            $("#export_xls").on('click', function () {
                $.ajax({
                    url      : App.adminUrl + "/reports/export_report_to_xls",
                    type     : "POST",
                    dataType : "json",
                    data     : {
                        html_string : $('.panel-hidden').html(),
                        report      : 'store'
                    },
                    success  : function (result) {
                        console.log(App.baseUrl + result);
                        if (result != '') {
                            window.open(App.baseUrl + result, '_newtab')
                        }
                        else {
                            alert('Export report gagal');
                        }
                    }
                });
            });

        },
        uniqueArray       : function (data) {
            var arrayResult = [];

            var arr = data,
                len = arr.length;

            while (len--) {
                var itm = arr[len];
                if (arrayResult.indexOf(itm) === -1) {
                    arrayResult.unshift(itm);
                }
            }

            return arrayResult;
        }
    }
});