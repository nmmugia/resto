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
    "highcharts"

], function ($, ui) {
    return {
        baseUrl           : $('#root_base_url').val(),
        adminUrl          : $('#admin_url').val(),
        overlayUI         : $('#cover'),
        analyticsType     : $('#analytics_type').val(),
        sidedishCount     : document.getElementById("sidedishCount") != null ? document.getElementById("sidedishCount").value : 0,
        optionsCount      : document.getElementById("optionsCount") != null ? document.getElementById("optionsCount").value : 0,
        optionsValueCount : document.getElementById("optionsValueCount") != null ? document.getElementById("optionsValueCount").value : 0,
        top10Container    : $('#top10Container'),
        chartContainer    : $('#chartContainer'),
        init              : function () {
            App.overlayUI.hide();
            $('#side-menu').metisMenu();
            App.initFunc(App);

            // datepicker validation
            $(".date-input").val("");
            $("#start_date").datepicker({
                dateFormat     : 'yy-mm-dd',
                numberOfMonths : 1,
                onSelect       : function (selected) {
                    var dt = new Date(selected);
                    dt.setDate(dt.getDate());
                    $("#end_date").datepicker("option", "minDate", dt);
                }
            });
            $("#end_date").datepicker({
                dateFormat     : 'yy-mm-dd',
                numberOfMonths : 1,
                onSelect       : function (selected) {
                    var dt = new Date(selected);
                    dt.setDate(dt.getDate());
                    $("#start_date").datepicker("option", "maxDate", dt);
                }
            });

            $(".def-select").val("0");

            if (App.analyticsType == "sales") {
                App.initSalesUI();
            }
            else if (App.analyticsType == "table") {
                App.initTableUI();
            }
            else if (App.analyticsType == "staff") {
                App.initStaffUI();
            }else if (App.analyticsType == "menu") {
                App.initMenuUI();
            }else if(App.analyticsType == "engineering"){
                App.initEngineeringUI();
            }

        },
        initSalesUI       : function () {
            $("#outlet_sel").chained("#store_sel");
            $("#filter_submit").on('click', function () {
                App.overlayUI.show();
                var request = $.ajax({
                    url      : App.adminUrl + "/analytics/get_sales_data",
                    type     : "POST",
                    dataType : "json",
                    data     : {
                        start_date : $("#start_date").val(),
                        end_date   : $("#end_date").val(),
                        month      : $("#month_sel").val(),
                        year       : $("#year_sel").val(),
                        store      : $("#store_sel").val(),
                        outlet     : $("#outlet_sel").val()
                    }
                });

                request.done(function (result) {
                    if (result != '') {
                        if (result.status === true) {
                            App.top10Container.html(result.data);
                            App.chartContainer.html('');

                            var row = $("<div>", {
                                "id" : 'chart_1'
                            });

                            var row2 = $("<div>", {
                                "id" : 'chart_2'
                            });

                            var row3 = $("<div>", {
                                "id" : 'chart_3'
                            });

                            var row4 = $("<div>", {
                                "id" : 'chart_4'
                            });

                            var row5 = $("<div>", {
                                "id" : 'chart_5'
                            });

                            App.chartContainer.append(row);
                            App.chartContainer.append(row2);
                            App.chartContainer.append(row3);
                            App.chartContainer.append(row4);
                            App.chartContainer.append(row5);

                            var catType = '';
                            var chartData1 = [];
                            var chartData2 = [];
                            var chartData3 = [];
                            var chartData4 = [];
                            var chartData5 = [];

                            if (result.data_chart[0].get_year) {
                                catType = 'Tahun';
                                for (var i = 0; i < result.data_chart.length; i++) {
                                    chartData1.push([result.data_chart[i].get_year, parseInt(result.data_chart[i].item_count)]);
                                    chartData2.push([result.data_chart[i].get_year, parseInt(result.data_chart[i].customer_count)]);
                                    chartData3.push([result.data_chart[i].get_year, parseInt(result.data_chart[i].gross_revenue)]);
                                    chartData4.push([result.data_chart[i].get_year, parseInt(result.data_chart[i].gross_expenses)]);
                                    chartData5.push([result.data_chart[i].get_year, parseInt(result.data_chart[i].gross_profit)]);
                                }
                            }
                            else if (result.data_chart[0].get_month) {
                                catType = 'Bulan';
                                for (var i = 0; i < result.data_chart.length; i++) {
                                    chartData1.push([result.data_chart[i].get_month, parseInt(result.data_chart[i].item_count)]);
                                    chartData2.push([result.data_chart[i].get_month, parseInt(result.data_chart[i].customer_count)]);
                                    chartData3.push([result.data_chart[i].get_month, parseInt(result.data_chart[i].gross_revenue)]);
                                    chartData4.push([result.data_chart[i].get_month, parseInt(result.data_chart[i].gross_expenses)]);
                                    chartData5.push([result.data_chart[i].get_month, parseInt(result.data_chart[i].gross_profit)]);
                                }
                            }
                            else {
                                catType = 'Tanggal';
                                for (var i = 0; i < result.data_chart.length; i++) {
                                    chartData1.push([result.data_chart[i].get_date, parseInt(result.data_chart[i].item_count)]);
                                    chartData2.push([result.data_chart[i].get_date, parseInt(result.data_chart[i].customer_count)]);
                                    chartData3.push([result.data_chart[i].get_date, parseInt(result.data_chart[i].gross_revenue)]);
                                    chartData4.push([result.data_chart[i].get_date, parseInt(result.data_chart[i].gross_expenses)]);
                                    chartData5.push([result.data_chart[i].get_date, parseInt(result.data_chart[i].gross_profit)]);
                                }
                            }

                            $('#chart_1').highcharts({
                                chart   : {
                                    type : 'column'
                                },
                                credits : {
                                    enabled : false
                                },
                                title   : {
                                    text : 'Penjualan Item'
                                },
                                xAxis   : {
                                    type   : 'category',
                                    labels : {
                                        rotation : -45,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif'
                                        }
                                    }
                                },
                                yAxis   : {
                                    title : {
                                        text : 'Jumlah Item'
                                    }
                                },
                                legend  : {
                                    enabled : false
                                },
                                series  : [{
                                    name       : 'Jumlah item pada ' + catType + ' ini',
                                    data       : chartData1,
                                    dataLabels : {
                                        enabled  : true,
                                        rotation : -90,
                                        color    : '#FFFFFF',
                                        align    : 'right',
                                        x        : 4,
                                        y        : 10,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif',
                                            textShadow : '0 0 3px black'
                                        }
                                    }
                                }]
                            });

                            $('#chart_2').highcharts({
                                chart   : {
                                    type : 'column'
                                },
                                credits : {
                                    enabled : false
                                },
                                title   : {
                                    text : 'Jumlah Tamu'
                                },
                                xAxis   : {
                                    type   : 'category',
                                    labels : {
                                        rotation : -45,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif'
                                        }
                                    }
                                },
                                yAxis   : {
                                    title : {
                                        text : 'Jumlah tamu'
                                    }
                                },
                                legend  : {
                                    enabled : false
                                },
                                series  : [{
                                    name       : 'Jumlah tamu pada ' + catType + ' ini',
                                    data       : chartData2,
                                    dataLabels : {
                                        enabled  : true,
                                        rotation : -90,
                                        color    : '#FFFFFF',
                                        align    : 'right',
                                        x        : 4,
                                        y        : 10,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif',
                                            textShadow : '0 0 3px black'
                                        }
                                    }
                                }]
                            });

                            $('#chart_3').highcharts({
                                chart   : {
                                    type : 'column'
                                },
                                credits : {
                                    enabled : false
                                },
                                title   : {
                                    text : 'Gross Revenue'
                                },
                                xAxis   : {
                                    type   : 'category',
                                    labels : {
                                        rotation : -45,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif'
                                        }
                                    }
                                },
                                yAxis   : {
                                    title : {
                                        text : 'Gross Revenue'
                                    }
                                },
                                legend  : {
                                    enabled : false
                                },
                                series  : [{
                                    name       : 'Gross Revenue pada ' + catType + ' ini',
                                    data       : chartData3,
                                    dataLabels : {
                                        enabled  : true,
                                        rotation : -90,
                                        color    : '#FFFFFF',
                                        align    : 'right',
                                        x        : 4,
                                        y        : 10,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif',
                                            textShadow : '0 0 3px black'
                                        }
                                    }
                                }]
                            });

                            $('#chart_4').highcharts({
                                chart   : {
                                    type : 'column'
                                },
                                credits : {
                                    enabled : false
                                },
                                title   : {
                                    text : 'Gross Expenses'
                                },
                                xAxis   : {
                                    type   : 'category',
                                    labels : {
                                        rotation : -45,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif'
                                        }
                                    }
                                },
                                yAxis   : {
                                    title : {
                                        text : 'Gross Expenses'
                                    }
                                },
                                legend  : {
                                    enabled : false
                                },
                                series  : [{
                                    name       : 'Gross Expenses pada ' + catType + ' ini',
                                    data       : chartData4,
                                    dataLabels : {
                                        enabled  : true,
                                        rotation : -90,
                                        color    : '#FFFFFF',
                                        align    : 'right',
                                        x        : 4,
                                        y        : 10,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif',
                                            textShadow : '0 0 3px black'
                                        }
                                    }
                                }]
                            });

                            $('#chart_5').highcharts({
                                chart   : {
                                    type : 'column'
                                },
                                credits : {
                                    enabled : false
                                },
                                title   : {
                                    text : 'Gross Profit'
                                },
                                xAxis   : {
                                    type   : 'category',
                                    labels : {
                                        rotation : -45,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif'
                                        }
                                    }
                                },
                                yAxis   : {
                                    title : {
                                        text : 'Gross Profit'
                                    }
                                },
                                legend  : {
                                    enabled : false
                                },
                                series  : [{
                                    name       : 'Gross Profit pada ' + catType + ' ini',
                                    data       : chartData5,
                                    dataLabels : {
                                        enabled  : true,
                                        rotation : -90,
                                        color    : '#FFFFFF',
                                        align    : 'right',
                                        x        : 4,
                                        y        : 10,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif',
                                            textShadow : '0 0 3px black'
                                        }
                                    }
                                }]
                            });

                        } else {
                            App.top10Container.html(result.message);
                            App.chartContainer.html(result.message);
                        }

                        App.overlayUI.hide();
                    } else {
                        // window.location.reload(true);
                    }
                });
                request.fail(function (jqXHR, textStatus) {
                    // window.location.reload(true);
                });
                request.always(function () {
                });
            });

        },
        initTableUI       : function () {
            $("#filter_submit").on('click', function () {
                App.overlayUI.show();
                var request = $.ajax({
                    url      : App.adminUrl + "/analytics/get_table_data",
                    type     : "POST",
                    dataType : "json",
                    data     : {
                        start_date : $("#start_date").val(),
                        end_date   : $("#end_date").val(),
                        month      : $("#month_sel").val(),
                        year       : $("#year_sel").val(),
                        store      : $("#store_sel").val()
                    }
                });

                request.done(function (result) {
                    if (result != '') {
                        if (result.status === true) {
                            App.top10Container.html(result.data);
                            App.chartContainer.html('');

                            var row = $("<div>", {
                                "id" : 'chart_1'
                            });

                            var row2 = $("<div>", {
                                "id" : 'chart_2'
                            });

                            App.chartContainer.append(row);
                            App.chartContainer.append(row2);

                            var catType = '';
                            var chartData1 = [];
                            var chartData2 = [];

                            if (result.data_chart[0].get_year) {
                                catType = 'Tahun';
                                for (var i = 0; i < result.data_chart.length; i++) {
                                    var avgCustomerPerTable = Math.round(result.data_chart[i].customer_count / result.data_chart[i].total_table);
                                    var avgMinutesPerTable = Math.round(result.data_chart[i].get_minutes / result.data_chart[i].total_table);
                                    chartData1.push([result.data_chart[i].get_year, avgCustomerPerTable]);
                                    chartData2.push([result.data_chart[i].get_year, Math.floor(avgMinutesPerTable / 60)]);
                                }
                            }
                            else if (result.data_chart[0].get_month) {
                                catType = 'Bulan';
                                for (var i = 0; i < result.data_chart.length; i++) {
                                    var avgCustomerPerTable = Math.round(result.data_chart[i].customer_count / result.data_chart[i].total_table);
                                    var avgMinutesPerTable = Math.round(result.data_chart[i].get_minutes / result.data_chart[i].total_table);
                                    chartData1.push([result.data_chart[i].get_month, avgCustomerPerTable]);
                                    chartData2.push([result.data_chart[i].get_month, Math.floor(avgMinutesPerTable / 60)]);
                                }
                            }
                            else {
                                catType = 'Tanggal';
                                for (var i = 0; i < result.data_chart.length; i++) {
                                    var avgCustomerPerTable = Math.round(result.data_chart[i].customer_count / result.data_chart[i].total_table);
                                    var avgMinutesPerTable = Math.round(result.data_chart[i].get_minutes / result.data_chart[i].total_table);
                                    chartData1.push([result.data_chart[i].get_date, avgCustomerPerTable]);
                                    chartData2.push([result.data_chart[i].get_date, Math.floor(avgMinutesPerTable / 60)]);
                                }
                            }

                            $('#chart_1').highcharts({
                                chart   : {
                                    type : 'column'
                                },
                                credits : {
                                    enabled : false
                                },
                                title   : {
                                    text : 'Chart Tamu Per Meja'
                                },
                                xAxis   : {
                                    type   : 'category',
                                    labels : {
                                        rotation : -45,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif'
                                        }
                                    }
                                },
                                yAxis   : {
                                    title : {
                                        text : 'Jumlah Tamu Per Meja (orang)'
                                    }
                                },
                                legend  : {
                                    enabled : false
                                },
                                series  : [{
                                    name       : 'Jumlah tamu per meja pada ' + catType + ' ini',
                                    data       : chartData1,
                                    dataLabels : {
                                        enabled  : true,
                                        rotation : -90,
                                        color    : '#FFFFFF',
                                        align    : 'right',
                                        x        : 4,
                                        y        : 10,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif',
                                            textShadow : '0 0 3px black'
                                        }
                                    }
                                }]
                            });

                            $('#chart_2').highcharts({
                                chart   : {
                                    type : 'column'
                                },
                                credits : {
                                    enabled : false
                                },
                                title   : {
                                    text : 'Chart Lama Duduk Per Meja'
                                },
                                xAxis   : {
                                    type   : 'category',
                                    labels : {
                                        rotation : -45,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif'
                                        }
                                    }
                                },
                                yAxis   : {
                                    title : {
                                        text : 'Jumlah Jam Duduk Per Meja (Jam)'
                                    }
                                },
                                legend  : {
                                    enabled : false
                                },
                                series  : [{
                                    name       : 'Jumlah jam duduk per meja pada ' + catType + ' ini',
                                    data       : chartData2,
                                    dataLabels : {
                                        enabled  : true,
                                        rotation : -90,
                                        color    : '#FFFFFF',
                                        align    : 'right',
                                        x        : 4,
                                        y        : 10,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif',
                                            textShadow : '0 0 3px black'
                                        }
                                    }
                                }]
                            });

                        } else {
                            App.top10Container.html(result.message);
                            App.chartContainer.html(result.message);
                        }

                        App.overlayUI.hide();
                    } else {
                        //window.location.reload(true);
                    }
                });
                request.fail(function (jqXHR, textStatus) {
                    //window.location.reload(true);
                });
                request.always(function () {
                });
            });
        },
        initStaffUI       : function () {
            $("#filter_submit").on('click', function () {
                App.overlayUI.show();
                var request = $.ajax({
                    url      : App.adminUrl + "/analytics/get_staff_data",
                    type     : "POST",
                    dataType : "json",
                    data     : {
                        start_date : $("#start_date").val(),
                        end_date   : $("#end_date").val(),
                        month      : $("#month_sel").val(),
                        year       : $("#year_sel").val(),
                        store      : $("#store_sel").val()
                    }
                });

                request.done(function (result) {
                    if (result != '') {
                        if (result.status === true) {
                            App.top10Container.html(result.data);
                            App.chartContainer.html('');

                            var row = $("<div>", {
                                "id" : 'chart_1'
                            });

                            var row2 = $("<div>", {
                                "id" : 'chart_2'
                            });

                            App.chartContainer.append(row);
                            App.chartContainer.append(row2);

                            var catType = '';
                            var chartData1 = [];
                            var chartData2 = [];
                            var totalCusWaiter = 0;
                            var totalCusCashier = 0;
                            var i = 0;

                            if (result.data_chart[0].get_year) {
                                catType = 'Tahun';
                                for (i = 0; i < result.data_chart.length; i++) {
                                    totalCusWaiter = 0;
                                    totalCusCashier = 0;

                                    if (result.data_chart[i].total_customer[5] !== undefined) {
                                        totalCusWaiter = result.data_chart[i].total_customer[5].customer_count;
                                    }
                                    if (result.data_chart[i].total_customer[3] !== undefined) {
                                        totalCusCashier = result.data_chart[i].total_customer[3].customer_count;
                                    }

                                    chartData1.push([result.data_chart[i].get_year, parseInt(totalCusWaiter)]);
                                    chartData2.push([result.data_chart[i].get_year, parseInt(totalCusCashier)]);
                                }
                            }
                            else if (result.data_chart[0].get_month) {
                                catType = 'Bulan';
                                for (i = 0; i < result.data_chart.length; i++) {
                                    totalCusWaiter = 0;
                                    totalCusCashier = 0;

                                    if (result.data_chart[i].total_customer[5] !== undefined) {
                                        totalCusWaiter = result.data_chart[i].total_customer[5].customer_count;
                                    }
                                    if (result.data_chart[i].total_customer[3] !== undefined) {
                                        totalCusCashier = result.data_chart[i].total_customer[3].customer_count;
                                    }

                                    chartData1.push([result.data_chart[i].get_month, parseInt(totalCusWaiter)]);
                                    chartData2.push([result.data_chart[i].get_month, parseInt(totalCusCashier)]);
                                }
                            }
                            else {
                                catType = 'Tanggal';

                                for (i = 0; i < result.data_chart.length; i++) {
                                    totalCusWaiter = 0;
                                    totalCusCashier = 0;

                                    if (result.data_chart[i].total_customer[5] !== undefined) {
                                        totalCusWaiter = result.data_chart[i].total_customer[5].customer_count;
                                    }
                                    if (result.data_chart[i].total_customer[3] !== undefined) {
                                        totalCusCashier = result.data_chart[i].total_customer[3].customer_count;
                                    }

                                    chartData1.push([result.data_chart[i].get_date, parseInt(totalCusWaiter)]);
                                    chartData2.push([result.data_chart[i].get_date, parseInt(totalCusCashier)]);
                                }
                            }

                            $('#chart_1').highcharts({
                                chart   : {
                                    type : 'column'
                                },
                                credits : {
                                    enabled : false
                                },
                                title   : {
                                    text : 'Pelayanan Waiter'
                                },
                                xAxis   : {
                                    type   : 'category',
                                    labels : {
                                        rotation : -45,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif'
                                        }
                                    }
                                },
                                yAxis   : {
                                    title : {
                                        text : 'Jumlah tamu'
                                    }
                                },
                                legend  : {
                                    enabled : false
                                },
                                series  : [{
                                    name       : 'Jumlah tamu pada ' + catType + ' ini',
                                    data       : chartData1,
                                    dataLabels : {
                                        enabled  : true,
                                        rotation : -90,
                                        color    : '#FFFFFF',
                                        align    : 'right',
                                        x        : 4,
                                        y        : 10,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif',
                                            textShadow : '0 0 3px black'
                                        }
                                    }
                                }]
                            });

                            $('#chart_2').highcharts({
                                chart   : {
                                    type : 'column'
                                },
                                credits : {
                                    enabled : false
                                },
                                title   : {
                                    text : 'Pelayanan Kasir'
                                },
                                xAxis   : {
                                    type   : 'category',
                                    labels : {
                                        rotation : -45,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif'
                                        }
                                    }
                                },
                                yAxis   : {
                                    title : {
                                        text : 'Jumlah tamu'
                                    }
                                },
                                legend  : {
                                    enabled : false
                                },
                                series  : [{
                                    name       : 'Jumlah tamu pada ' + catType + ' ini',
                                    data       : chartData2,
                                    dataLabels : {
                                        enabled  : true,
                                        rotation : -90,
                                        color    : '#FFFFFF',
                                        align    : 'right',
                                        x        : 4,
                                        y        : 10,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif',
                                            textShadow : '0 0 3px black'
                                        }
                                    }
                                }]
                            });

                        } else {
                            App.top10Container.html(result.message);
                            App.chartContainer.html(result.message);
                        }

                        App.overlayUI.hide();
                    } else {
                        // window.location.reload(true);
                    }
                });
                request.fail(function (jqXHR, textStatus) {
                    // window.location.reload(true);
                });
                request.always(function () {
                });
            });
        },
         initMenuUI       : function () {
           $("#filter_submit").on('click', function () {
                App.overlayUI.show();
                var request = $.ajax({
                    url      : App.adminUrl + "/analytics/get_menu_data",
                    type     : "POST",
                    dataType : "json",
                    data     : {
                        start_date : $("#start_date").val(),
                        end_date   : $("#end_date").val(),
                        month      : $("#month_sel").val(),
                        year       : $("#year_sel").val(),
                        store      : $("#store_sel").val(),
                        outlet     : $("#outlet_sel").val()
                    }
                });

                request.done(function (result) {
                    if (result != '') {
                        if (result.status === true) {
                            App.top10Container.html(result.data);
                            App.chartContainer.html('');

                            var row = $("<div>", {
                                "id" : 'chart_1'
                            });

                            var row2 = $("<div>", {
                                "id" : 'chart_2'
                            });

                            var row3 = $("<div>", {
                                "id" : 'chart_3'
                            });

                            var row4 = $("<div>", {
                                "id" : 'chart_4'
                            });

                            var row5 = $("<div>", {
                                "id" : 'chart_5'
                            });

                            App.chartContainer.append(row);
                            App.chartContainer.append(row2);
                            App.chartContainer.append(row3);
                            App.chartContainer.append(row4);
                            App.chartContainer.append(row5);

                            var catType = '';
                            var chartData1 = [];
                            var chartData2 = [];
                            var chartData3 = [];
                            var chartData4 = [];
                            var chartData5 = [];

                            if (result.data_chart[0].get_year) {
                                catType = 'Tahun';
                                for (var i = 0; i < result.data_chart.length; i++) {
                                    chartData1.push([result.data_chart[i].get_year, parseInt(result.data_chart[i].item_count)]);
                                    chartData2.push([result.data_chart[i].get_year, parseInt(result.data_chart[i].customer_count)]);
                                    chartData3.push([result.data_chart[i].get_year, parseInt(result.data_chart[i].gross_revenue)]);
                                    chartData4.push([result.data_chart[i].get_year, parseInt(result.data_chart[i].gross_expenses)]);
                                    chartData5.push([result.data_chart[i].get_year, parseInt(result.data_chart[i].gross_profit)]);
                                }
                            }
                            else if (result.data_chart[0].get_month) {
                                catType = 'Bulan';
                                for (var i = 0; i < result.data_chart.length; i++) {
                                    chartData1.push([result.data_chart[i].get_month, parseInt(result.data_chart[i].item_count)]);
                                    chartData2.push([result.data_chart[i].get_month, parseInt(result.data_chart[i].customer_count)]);
                                    chartData3.push([result.data_chart[i].get_month, parseInt(result.data_chart[i].gross_revenue)]);
                                    chartData4.push([result.data_chart[i].get_month, parseInt(result.data_chart[i].gross_expenses)]);
                                    chartData5.push([result.data_chart[i].get_month, parseInt(result.data_chart[i].gross_profit)]);
                                }
                            }
                            else {
                                catType = 'Tanggal';
                                for (var i = 0; i < result.data_chart.length; i++) {
                                    chartData1.push([result.data_chart[i].get_date, parseInt(result.data_chart[i].item_count)]);
                                    chartData2.push([result.data_chart[i].get_date, parseInt(result.data_chart[i].customer_count)]);
                                    chartData3.push([result.data_chart[i].get_date, parseInt(result.data_chart[i].gross_revenue)]);
                                    chartData4.push([result.data_chart[i].get_date, parseInt(result.data_chart[i].gross_expenses)]);
                                    chartData5.push([result.data_chart[i].get_date, parseInt(result.data_chart[i].gross_profit)]);
                                }
                            }

                            $('#chart_1').highcharts({
                                chart   : {
                                    type : 'column'
                                },
                                credits : {
                                    enabled : false
                                },
                                title   : {
                                    text : 'Penjualan Item'
                                },
                                xAxis   : {
                                    type   : 'category',
                                    labels : {
                                        rotation : -45,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif'
                                        }
                                    }
                                },
                                yAxis   : {
                                    title : {
                                        text : 'Jumlah Item'
                                    }
                                },
                                legend  : {
                                    enabled : false
                                },
                                series  : [{
                                    name       : 'Jumlah item pada ' + catType + ' ini',
                                    data       : chartData1,
                                    dataLabels : {
                                        enabled  : true,
                                        rotation : -90,
                                        color    : '#FFFFFF',
                                        align    : 'right',
                                        x        : 4,
                                        y        : 10,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif',
                                            textShadow : '0 0 3px black'
                                        }
                                    }
                                }]
                            });

                            $('#chart_2').highcharts({
                                chart   : {
                                    type : 'column'
                                },
                                credits : {
                                    enabled : false
                                },
                                title   : {
                                    text : 'Jumlah Tamu'
                                },
                                xAxis   : {
                                    type   : 'category',
                                    labels : {
                                        rotation : -45,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif'
                                        }
                                    }
                                },
                                yAxis   : {
                                    title : {
                                        text : 'Jumlah tamu'
                                    }
                                },
                                legend  : {
                                    enabled : false
                                },
                                series  : [{
                                    name       : 'Jumlah tamu pada ' + catType + ' ini',
                                    data       : chartData2,
                                    dataLabels : {
                                        enabled  : true,
                                        rotation : -90,
                                        color    : '#FFFFFF',
                                        align    : 'right',
                                        x        : 4,
                                        y        : 10,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif',
                                            textShadow : '0 0 3px black'
                                        }
                                    }
                                }]
                            });

                            $('#chart_3').highcharts({
                                chart   : {
                                    type : 'column'
                                },
                                credits : {
                                    enabled : false
                                },
                                title   : {
                                    text : 'Gross Revenue'
                                },
                                xAxis   : {
                                    type   : 'category',
                                    labels : {
                                        rotation : -45,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif'
                                        }
                                    }
                                },
                                yAxis   : {
                                    title : {
                                        text : 'Gross Revenue'
                                    }
                                },
                                legend  : {
                                    enabled : false
                                },
                                series  : [{
                                    name       : 'Gross Revenue pada ' + catType + ' ini',
                                    data       : chartData3,
                                    dataLabels : {
                                        enabled  : true,
                                        rotation : -90,
                                        color    : '#FFFFFF',
                                        align    : 'right',
                                        x        : 4,
                                        y        : 10,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif',
                                            textShadow : '0 0 3px black'
                                        }
                                    }
                                }]
                            });

                            $('#chart_4').highcharts({
                                chart   : {
                                    type : 'column'
                                },
                                credits : {
                                    enabled : false
                                },
                                title   : {
                                    text : 'Gross Expenses'
                                },
                                xAxis   : {
                                    type   : 'category',
                                    labels : {
                                        rotation : -45,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif'
                                        }
                                    }
                                },
                                yAxis   : {
                                    title : {
                                        text : 'Gross Expenses'
                                    }
                                },
                                legend  : {
                                    enabled : false
                                },
                                series  : [{
                                    name       : 'Gross Expenses pada ' + catType + ' ini',
                                    data       : chartData4,
                                    dataLabels : {
                                        enabled  : true,
                                        rotation : -90,
                                        color    : '#FFFFFF',
                                        align    : 'right',
                                        x        : 4,
                                        y        : 10,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif',
                                            textShadow : '0 0 3px black'
                                        }
                                    }
                                }]
                            });

                            $('#chart_5').highcharts({
                                chart   : {
                                    type : 'column'
                                },
                                credits : {
                                    enabled : false
                                },
                                title   : {
                                    text : 'Gross Profit'
                                },
                                xAxis   : {
                                    type   : 'category',
                                    labels : {
                                        rotation : -45,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif'
                                        }
                                    }
                                },
                                yAxis   : {
                                    title : {
                                        text : 'Gross Profit'
                                    }
                                },
                                legend  : {
                                    enabled : false
                                },
                                series  : [{
                                    name       : 'Gross Profit pada ' + catType + ' ini',
                                    data       : chartData5,
                                    dataLabels : {
                                        enabled  : true,
                                        rotation : -90,
                                        color    : '#FFFFFF',
                                        align    : 'right',
                                        x        : 4,
                                        y        : 10,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif',
                                            textShadow : '0 0 3px black'
                                        }
                                    }
                                }]
                            });

                        } else {
                            App.top10Container.html(result.message);
                            App.chartContainer.html(result.message);
                        }

                        App.overlayUI.hide();
                    } else {
                        // window.location.reload(true);
                    }
                });
                request.fail(function (jqXHR, textStatus) {
                    // window.location.reload(true);
                });
                request.always(function () {
                });
            });

        }, initEngineeringUI       : function () {
           $("#filter_submit").on('click', function () {
                App.overlayUI.show();
                var request = $.ajax({
                    url      : App.adminUrl + "/analytics/get_engineering_data",
                    type     : "POST",
                    dataType : "json",
                    data     : {
                        start_date : $("#start_date").val(),
                        end_date   : $("#end_date").val(),
                        month      : $("#month_sel").val(),
                        year       : $("#year_sel").val(),
                        store      : $("#store_sel").val(),
                        outlet     : $("#outlet_sel").val()
                    }
                });

                request.done(function (result) {
                    if (result != '') {
                        if (result.status === true) {
                            App.top10Container.html(result.data);
                            App.chartContainer.html('');

                            var row = $("<div>", {
                                "id" : 'chart_1'
                            });

                            var row2 = $("<div>", {
                                "id" : 'chart_2'
                            });

                            var row3 = $("<div>", {
                                "id" : 'chart_3'
                            });

                            var row4 = $("<div>", {
                                "id" : 'chart_4'
                            });

                            var row5 = $("<div>", {
                                "id" : 'chart_5'
                            });

                            App.chartContainer.append(row);
                            App.chartContainer.append(row2);
                            App.chartContainer.append(row3);
                            App.chartContainer.append(row4);
                            App.chartContainer.append(row5);

                            var catType = '';
                            var chartData1 = [];
                            var chartData2 = [];
                            var chartData3 = [];
                            var chartData4 = [];
                            var chartData5 = [];

                            if (result.data_chart[0].get_year) {
                                catType = 'Tahun';
                                for (var i = 0; i < result.data_chart.length; i++) {
                                    chartData1.push([result.data_chart[i].get_year, parseInt(result.data_chart[i].item_count)]);
                                    chartData2.push([result.data_chart[i].get_year, parseInt(result.data_chart[i].customer_count)]);
                                    chartData3.push([result.data_chart[i].get_year, parseInt(result.data_chart[i].gross_revenue)]);
                                    chartData4.push([result.data_chart[i].get_year, parseInt(result.data_chart[i].gross_expenses)]);
                                    chartData5.push([result.data_chart[i].get_year, parseInt(result.data_chart[i].gross_profit)]);
                                }
                            }
                            else if (result.data_chart[0].get_month) {
                                catType = 'Bulan';
                                for (var i = 0; i < result.data_chart.length; i++) {
                                    chartData1.push([result.data_chart[i].get_month, parseInt(result.data_chart[i].item_count)]);
                                    chartData2.push([result.data_chart[i].get_month, parseInt(result.data_chart[i].customer_count)]);
                                    chartData3.push([result.data_chart[i].get_month, parseInt(result.data_chart[i].gross_revenue)]);
                                    chartData4.push([result.data_chart[i].get_month, parseInt(result.data_chart[i].gross_expenses)]);
                                    chartData5.push([result.data_chart[i].get_month, parseInt(result.data_chart[i].gross_profit)]);
                                }
                            }
                            else {
                                catType = 'Tanggal';
                                for (var i = 0; i < result.data_chart.length; i++) {
                                    chartData1.push([result.data_chart[i].get_date, parseInt(result.data_chart[i].item_count)]);
                                    chartData2.push([result.data_chart[i].get_date, parseInt(result.data_chart[i].customer_count)]);
                                    chartData3.push([result.data_chart[i].get_date, parseInt(result.data_chart[i].gross_revenue)]);
                                    chartData4.push([result.data_chart[i].get_date, parseInt(result.data_chart[i].gross_expenses)]);
                                    chartData5.push([result.data_chart[i].get_date, parseInt(result.data_chart[i].gross_profit)]);
                                }
                            }

                            $('#chart_1').highcharts({
                                chart   : {
                                    type : 'column'
                                },
                                credits : {
                                    enabled : false
                                },
                                title   : {
                                    text : 'Penjualan Item'
                                },
                                xAxis   : {
                                    type   : 'category',
                                    labels : {
                                        rotation : -45,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif'
                                        }
                                    }
                                },
                                yAxis   : {
                                    title : {
                                        text : 'Jumlah Item'
                                    }
                                },
                                legend  : {
                                    enabled : false
                                },
                                series  : [{
                                    name       : 'Jumlah item pada ' + catType + ' ini',
                                    data       : chartData1,
                                    dataLabels : {
                                        enabled  : true,
                                        rotation : -90,
                                        color    : '#FFFFFF',
                                        align    : 'right',
                                        x        : 4,
                                        y        : 10,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif',
                                            textShadow : '0 0 3px black'
                                        }
                                    }
                                }]
                            });

                            $('#chart_2').highcharts({
                                chart   : {
                                    type : 'column'
                                },
                                credits : {
                                    enabled : false
                                },
                                title   : {
                                    text : 'Jumlah Tamu'
                                },
                                xAxis   : {
                                    type   : 'category',
                                    labels : {
                                        rotation : -45,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif'
                                        }
                                    }
                                },
                                yAxis   : {
                                    title : {
                                        text : 'Jumlah tamu'
                                    }
                                },
                                legend  : {
                                    enabled : false
                                },
                                series  : [{
                                    name       : 'Jumlah tamu pada ' + catType + ' ini',
                                    data       : chartData2,
                                    dataLabels : {
                                        enabled  : true,
                                        rotation : -90,
                                        color    : '#FFFFFF',
                                        align    : 'right',
                                        x        : 4,
                                        y        : 10,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif',
                                            textShadow : '0 0 3px black'
                                        }
                                    }
                                }]
                            });

                            $('#chart_3').highcharts({
                                chart   : {
                                    type : 'column'
                                },
                                credits : {
                                    enabled : false
                                },
                                title   : {
                                    text : 'Gross Revenue'
                                },
                                xAxis   : {
                                    type   : 'category',
                                    labels : {
                                        rotation : -45,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif'
                                        }
                                    }
                                },
                                yAxis   : {
                                    title : {
                                        text : 'Gross Revenue'
                                    }
                                },
                                legend  : {
                                    enabled : false
                                },
                                series  : [{
                                    name       : 'Gross Revenue pada ' + catType + ' ini',
                                    data       : chartData3,
                                    dataLabels : {
                                        enabled  : true,
                                        rotation : -90,
                                        color    : '#FFFFFF',
                                        align    : 'right',
                                        x        : 4,
                                        y        : 10,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif',
                                            textShadow : '0 0 3px black'
                                        }
                                    }
                                }]
                            });

                            $('#chart_4').highcharts({
                                chart   : {
                                    type : 'column'
                                },
                                credits : {
                                    enabled : false
                                },
                                title   : {
                                    text : 'Gross Expenses'
                                },
                                xAxis   : {
                                    type   : 'category',
                                    labels : {
                                        rotation : -45,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif'
                                        }
                                    }
                                },
                                yAxis   : {
                                    title : {
                                        text : 'Gross Expenses'
                                    }
                                },
                                legend  : {
                                    enabled : false
                                },
                                series  : [{
                                    name       : 'Gross Expenses pada ' + catType + ' ini',
                                    data       : chartData4,
                                    dataLabels : {
                                        enabled  : true,
                                        rotation : -90,
                                        color    : '#FFFFFF',
                                        align    : 'right',
                                        x        : 4,
                                        y        : 10,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif',
                                            textShadow : '0 0 3px black'
                                        }
                                    }
                                }]
                            });

                            $('#chart_5').highcharts({
                                chart   : {
                                    type : 'column'
                                },
                                credits : {
                                    enabled : false
                                },
                                title   : {
                                    text : 'Gross Profit'
                                },
                                xAxis   : {
                                    type   : 'category',
                                    labels : {
                                        rotation : -45,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif'
                                        }
                                    }
                                },
                                yAxis   : {
                                    title : {
                                        text : 'Gross Profit'
                                    }
                                },
                                legend  : {
                                    enabled : false
                                },
                                series  : [{
                                    name       : 'Gross Profit pada ' + catType + ' ini',
                                    data       : chartData5,
                                    dataLabels : {
                                        enabled  : true,
                                        rotation : -90,
                                        color    : '#FFFFFF',
                                        align    : 'right',
                                        x        : 4,
                                        y        : 10,
                                        style    : {
                                            fontSize   : '13px',
                                            fontFamily : 'Verdana, sans-serif',
                                            textShadow : '0 0 3px black'
                                        }
                                    }
                                }]
                            });

                        } else {
                            App.top10Container.html(result.message);
                            App.chartContainer.html(result.message);
                        }

                        App.overlayUI.hide();
                    } else {
                        // window.location.reload(true);
                    }
                });
                request.fail(function (jqXHR, textStatus) {
                    // window.location.reload(true);
                });
                request.always(function () {
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