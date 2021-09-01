
define([
    "jquery",
    "jquery-ui",
    "chained",
    "metisMenu",
    "Morris",
    'datatables',
    "bootstrap",
    "datatables-bootstrap",
    "datetimepicker",
    "highcharts",
    "html2canvas",
    "canvg",
    "rgbcolor",
    "html2canvassvg",
    "select2",

], function ($, ui) {
    return {
        baseUrl           : $('#root_base_url').val(),
        adminUrl          : $('#admin_url').val(),
        overlayUI         : $('#cover'),
        reportType        : $('#report_type').val(),
        pdfTitle: "PDF export",
        exportFileName : "DataTable export",
        captureImageData:"",
        init              : function () {
			$("#menu_id").chained("#category_id");
            $("select.select2").select2();
            App.overlayUI.hide();
            App.initFunc(App);
            $('#side-menu').metisMenu();
            $('#single_date').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'YYYY-MM-DD',
            });
            $('#month_date').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'YYYY-MM',
            });
            $(document).on('click','#capture_chart',function(){
              App.captureImage($(this).attr("target_capture"),"#image_data");
              $("#report_export_pdf").show();
            });
            setTimeout(function(){
              if($('#sales_by_day_chart').length>0){
                
                sales_by_day_chart=$('#sales_by_day_chart').highcharts({
                  chart   : {
                    type : 'column'
                  },
                  credits : {
                    enabled : false
                  },
                  title   : {
                    text : 'Grafik Sales By Day Report'
                  },
                  xAxis   : {
                    categories:sales_by_day_categories_data,
                    crosshair:true,
                    labels : {
                      rotation : -45,
                      style    : {
                        fontSize   : '13px',
                        fontFamily : 'Verdana, sans-serif'
                      }
                    }
                  },
                  tooltip:{
                    valueDecimals:0,
                    valuePrefix: 'Rp. '
                  },
                  yAxis   : {
                    title : {
                      text : 'Jumlah Revenue'
                    }
                  },
                  legend  : {
                    enabled : false
                  },
                  series  : [{
                    name       : 'Revenue',
                    data       : sales_by_day_series_data
                  }]
                });
              }
              if($('#sales_by_department_category_chart').length>0)
              {
                sales_by_department_category_chart=$('#sales_by_department_category_chart').highcharts({
                  chart   : {
                    type : 'column'
                  },
                  credits : {
                    enabled : false
                  },
                  title   : {
                    text : 'Grafik Column Sales By Department / Category'
                  },
                  xAxis   : {
                    categories:sales_by_department_category_categories_data,
                    crosshair:true,
                    labels : {
                      rotation : -45,
                      style    : {
                        fontSize   : '13px',
                        fontFamily : 'Verdana, sans-serif'
                      }
                    }
                  },
                  tooltip:{
                    valueDecimals:0,
                    valuePrefix: 'Rp. '
                  },
                  yAxis   : {
                    title : {
                      text : 'Jumlah Revenue'
                    }
                  },
                  legend  : {
                    enabled : false
                  },
                  series  : [{
                    name       : 'Revenue',
                    data       : sales_by_department_category_series_data
                  }]
                });
                $('#sales_by_department_category_chart_pie').highcharts({
                  chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                  },
                  title: {
                    text: 'Grafik Pie Sales By Department / Category'
                  },
                  tooltip: {
                      pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                  },
                  plotOptions: {
                      pie: {
                          allowPointSelect: true,
                          cursor: 'pointer',
                          dataLabels: {
                              enabled: true,
                              format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                              style: {
                                  color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                              }
                          }
                      }
                  },
                  series: [{
                      name: "Persentase",
                      colorByPoint: true,
                      data: sales_by_department_category_series_data_pie
                  }]
                });
              }
              if($('#sales_by_waiter_chart').length>0)
              {
                sales_by_waiter_chart=$('#sales_by_waiter_chart').highcharts({
                  chart   : {
                    type : 'column'
                  },
                  credits : {
                    enabled : false
                  },
                  title   : {
                    text : 'Grafik Column Sales By Waiter'
                  },
                  xAxis   : {
                    categories:sales_by_waiter_categories_data,
                    crosshair:true,
                    labels : {
                      rotation : -45,
                      style    : {
                        fontSize   : '13px',
                        fontFamily : 'Verdana, sans-serif'
                      }
                    }
                  },
                  tooltip:{
                    valueDecimals:0,
                    valuePrefix: 'Rp. '
                  },
                  yAxis   : {
                    title : {
                      text : 'Jumlah Revenue'
                    }
                  },
                  legend  : {
                    enabled : false
                  },
                  series  : [{
                    name       : 'Revenue',
                    data       : sales_by_waiter_series_data
                  }]
                });
                $('#sales_by_waiter_chart_pie').highcharts({
                  chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                  },
                  title: {
                    text: 'Grafik Pie Sales By Waiter'
                  },
                  tooltip: {
                      pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                  },
                  plotOptions: {
                      pie: {
                          allowPointSelect: true,
                          cursor: 'pointer',
                          dataLabels: {
                              enabled: true,
                              format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                              style: {
                                  color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                              }
                          }
                      }
                  },
                  series: [{
                      name: "Persentase",
                      colorByPoint: true,
                      data: sales_by_waiter_series_data_pie
                  }]
                });
              }
              if($('#inventory_used_detail_chart_pie').length>0)
              {
                $('#inventory_used_detail_chart_pie').highcharts({
                  chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                  },
                  title: {
                    text: 'Grafik Pie Inventory Used Detail'
                  },
                  tooltip: {
                      pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                  },
                  plotOptions: {
                      pie: {
                          allowPointSelect: true,
                          cursor: 'pointer',
                          dataLabels: {
                              enabled: true,
                              format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                              style: {
                                  color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                              }
                          }
                      }
                  },
                  series: [{
                      name: "Persentase",
                      colorByPoint: true,
                      data: inventory_used_detail_series_data_pie
                  }]
                });
              }
            },1000);
            $("#filter_inventory_used,#export_inventory_used").click(function(){
              $("#inventory_id").val("");
              $("#form_inventory_used").removeAttr("target");
            });
            $(".get_detail_inventory_used").click(function(){
              inventory_id=$(this).attr("inventory_id");
              $("#inventory_id").val(inventory_id);
              $("#form_inventory_used").attr("target","_blank");
              $("#form_inventory_used").submit();
            });
            $('#start_date').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'YYYY-MM-DD HH:mm',
            });

            $('#end_date').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'YYYY-MM-DD HH:mm',
            });


            $("#start_date").on("dp.change", function (e) {

                $('#end_date').datetimepicker({
                    sideBySide: true,
                    useCurrent: true,
                    format: 'YYYY-MM-DD HH:mm' 
                });
                
                $('#end_date').data("DateTimePicker").minDate(e.date);

            });
            $('#input_date').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'YYYY-MM-DD',
            });
              $('#report_end_date').datetimepicker({
                    sideBySide: true,
                    useCurrent: true,
                    format: 'YYYY-MM-DD' 
                });
                
             $("#input_date").on("dp.change", function (e) {

                $('#report_end_date').datetimepicker({
                    sideBySide: true,
                    useCurrent: true,
                    format: 'YYYY-MM-DD' 
                });
                
                $('#report_end_date').data("DateTimePicker").minDate(e.date);

            });

             $("#end_date").on("dp.change", function (e) {

                $('#end_date').datetimepicker({
                    sideBySide: true,
                    useCurrent: true,
                    format: 'YYYY-MM-DD HH:mm' 
                });
                
                $('#start_date').data("DateTimePicker").maxDate(e.date);

            });

             $('#purchase_order_date').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'YYYY-MM-DD' 
             });

            $(".date-input").val("");
            $(".def-select").val("0");
            
            if (App.reportType == "open_close") {
                App.openCloseUI();
            }else if(App.reportType == "transaction"){
                App.transactionUI();
            }
            else if(App.reportType == "transaction_detail"){
                App.transactionDetailUI();
            }else if(App.reportType == "sales_menu"){
                App.salesMenuUI();
            }else if(App.reportType == "moving_item"){
                App.salesMovingItem();
            }else if(App.reportType == "sales_menu_detail"){
                App.salesMenuDetail();
            }else if(App.reportType == "profit_loss"){
                App.profitLossUI();
            }else if(App.reportType == "sales_category"){
              App.salesCategoryUI();
            }else if(App.reportType == "sales_outlet"){
              App.salesOutletUI();
            }else if(App.reportType == "void"){
              App.voidUI();
            }else if(App.reportType == "sos"){
              App.sosUI();
            }else if(App.reportType == "inventory_stock" || App.reportType=="inventory_stock_detail"){
              App.inventoryStockReportUI();
            }else if(App.reportType=="total_sales_waiter"){
              App.totalSalesWaiterUI();
            }else if(App.reportType=="total_sales_waiter_detail"){
              App.totalSalesWaiterDetailUI();
            }else if(App.reportType=="total_quantity_order_table_waiter"){
              App.totalQtyOrderTableWaiter();
            }else if(App.reportType=="achievement_waiter"){
              App.achievementWaiter();
            }else if(App.reportType=="achievement_waiter_detail"){
              App.achievementWaiterDetail();
            }else if(App.reportType=="kitchen_duration"){
              App.kitchenDurationUI();
            }else if(App.reportType=="inventory_adjustment"){
              App.inventoryAdjustment();
            }else if(App.reportType=="refund"){
              App.refund();
            }else if(App.reportType=="petty_cash"){
              App.pettyCash();
            }else if(App.reportType=="side_dish"){
              App.sidedish();
            }else if(App.reportType=="delete_order"){
              App.deleteOrder();
            }else if(App.reportType=="summary_year"){
              App.summaryYear();
            }else if(App.reportType=="taxes"){
              App.taxesYear(); 
            }else if(App.reportType=="summary_inventory"){
              App.summaryInventory(); 
            }else if(App.reportType=="detail_inventory"){
              App.detailInventory(); 
            }else if(App.reportType=="summary_receive_order"){
              App.summaryReceiveOrder(); 
            }else if(App.reportType=="summary_retur_order"){
              App.summaryReturOrder(); 
            }else if(App.reportType=="member_transaction"){
              App.memberTransaction(); 
            }else if(App.reportType=="voucher_used"){
              App.voucherUsed(); 
            }else if(App.reportType=="promo_used"){
              App.promoUsed(); 
            }else if(App.reportType=="promo_cc"){
              App.promoCc(); 
            }else if(App.reportType=="bon_bill"){
              App.bonBill(); 
            }else if(App.reportType=="pending_bill"){
              App.pendingBill(); 
            }else if(App.reportType=="compliment"){
              App.compliment(); 
            }else if(App.reportType=="spoiled"){
              App.spoiled(); 
            }else if(App.reportType=="cost_opname"){
              App.costOpnameUI(); 
            }else if(App.reportType=="transfer_menu"){
              App.transferMenu(); 
            }else if(App.reportType=="transfer_inventory"){
              App.transferinventory(); 
            }else if(App.reportType=="aging_data"){
              App.AgingData();
            }else if(App.reportType=="price_analyst"){
              App.PriceAnalystUI();
            }else if (App.reportType=="data_inventory"){
              App.InventoryList();
            }else if (App.reportType=="kontra_bon") {
              App.kontraBon();
            }else if (App.reportType=="member_discount_detail") {
              App.memberDiscountDetail();
            }else if (App.reportType=="delivery_service") {
              App.deliveryService();
            }
        },
				spoiled:function(){
          $("#filter_submit").on('click', function (e) {
            $("#report_content").html("");
            e.preventDefault();
            var request = $.ajax({
              type    : "POST",
              url     : App.adminUrl + "/reports/get_spoiled_data",
              data    : {
                inventory_id: $('#formFilter #inventory_id').val(),
                outlet_id: $('#formFilter #outlet_id').val(),
                start_date: $('#formFilter #input_date input').val(),
                end_date: $('#formFilter #report_end_date input').val(),
              }
            });
            request.done(function (msg) {
              $("#report_content").html(msg);
              $('#export_pdf').show();
            });
          }); 
          $("#export_pdf").unbind('click').bind('click', function (e) {
            $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
          });
        },     
        sosUI       : function () {
            $("#user_id").chained("#store_id");

           $("#filter_submit").on('click', function (e) {

                e.preventDefault();

                var table =  $('#table-sos').dataTable({
                   "bProcessing"    : true,
                   //"bServerSide"    : true,
                   "sServerMethod"  : "POST",
                   "bFilter" :true,                   
                   "bDestroy" :true,
                   "autoWidth": true,
                   "scrollX": true,
                   // "ordering" : false,
                   "iDisplayLength" : 10,
                   "ajax": {
                      "url": $('#dataProcessUrl').val(),
                      "type": 'POST',
                      "data": {
                        param: $('#formFilter').serialize()
                      },
                       "dataSrc": function( json ) {
                          // $('#export_xls').show();    
                          $('#export_pdf').show();               
                          return json.data;
                      },
                    },

                 "columns"        : [
                     {data : "no_bill"},
                     {data : "qty"},
                     {data : "gross"},
                     {data : "nett"},
                     {data : "tax"},
                     {data : "net_tax"},
                     {data : "cost"}

                 ],
                }); //end datatable
            
              
            });

            $("#export_pdf").on('click', function (e) {
              $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
            });

        },   
        costOpnameUI:function(){
          $("#filter_submit").on('click', function (e) {
            $("#report_content").html("");
            e.preventDefault();
            var request = $.ajax({
              type    : "POST",
              url     : App.adminUrl + "/reports/get_cost_opname",
              data    : {
                inventory_id   : $('#formFilter #inventory_id').val(),
                start_date   : $('#formFilter #input_start_date').val(),
                end_date   : $('#formFilter #input_end_date').val()
              }
            });
            request.done(function (msg) {
              $("#report_content").html(msg);
              $('#export_pdf').show();
              // $('#export_xls').show();
            });
          }); 
          $("#export_pdf").unbind('click').bind('click', function (e) {
            $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
          });
          // $("#export_xls").on('click', function (e) {
            // $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_excel");
          // });
        },
				compliment:function(){
          $("#filter_submit").on('click', function (e) {
            $("#report_content").html("");
            e.preventDefault();
            var request = $.ajax({
              type    : "POST",
              url     : App.adminUrl + "/reports/get_compliment_data",
              data    : {
                start_date: $('#formFilter #input_date input').val(),
                end_date: $('#formFilter #report_end_date input').val(),
              }
            });
            request.done(function (msg) {
              $("#report_content").html(msg);
              $('#export_pdf').show();
            });
          }); 
          $("#export_pdf").unbind('click').bind('click', function (e) {
            $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
          });
        },
				bonBill:function(){
          $("#filter_submit").on('click', function (e) {
            $("#report_content").html("");
            e.preventDefault();
            var request = $.ajax({
              type    : "POST",
              url     : App.adminUrl + "/reports/get_bon_bill_data",
              data    : {
                start_date: $('#formFilter #input_date input').val(),
                end_date: $('#formFilter #report_end_date input').val(),
              }
            });
            request.done(function (msg) {
              $("#report_content").html(msg);
              $('#export_pdf').show();
            });
          }); 
          $("#export_pdf").unbind('click').bind('click', function (e) {
            $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
          });
        },

        /*
        *   Report Pending Bill Company and Employee
        *   Created by : M. Tri
        *   Created at : 31/08/2016
        */
        pendingBill:function(){
          var type = $("#pending_type").val();

          $("#filter_submit").on('click', function (e) {
            $("#report_content").html("");

            e.preventDefault();
            var request = $.ajax({
              type    : "POST",
              url     : App.adminUrl + "/reports/get_pending_bill_data/" + type,
              data    : {
                start_date: $('#formFilter #input_date input').val(),
                end_date: $('#formFilter #report_end_date input').val(),
              }
            });
            request.done(function (msg) {
              $("#report_content").html(msg);
              $('#export_pdf').show();
            });
          });

          $("#export_pdf").unbind('click').bind('click', function (e) {
            $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf/" + type);
          });
        },

        promoUsed:function(){
          $("#filter_submit").on('click', function (e) {
            $("#report_content").html("");
            e.preventDefault();
            var request = $.ajax({
              type    : "POST",
              url     : App.adminUrl + "/reports/get_promo_used_data",
              data    : {
                start_date: $('#formFilter #input_date input').val(),
                end_date: $('#formFilter #report_end_date input').val(),
              }
            });
            request.done(function (msg) {
              $("#report_content").html(msg);
              $('#export_pdf').show();
            });
          }); 
          $("#export_pdf").unbind('click').bind('click', function (e) {
            $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
          });
        },
        promoCc:function(){
          $("#filter_submit").on('click', function (e) {
            $("#report_content").html("");
            e.preventDefault();
            var request = $.ajax({
              type    : "POST",
              url     : App.adminUrl + "/reports/get_promo_cc_data",
              data    : {
                start_date : $('#input_start_date').val(),
                end_date : $('#input_end_date').val(),
                promo_cc_id : $('#promo_cc_name').val()
              }
            });
            request.done(function (msg) {
              $("#report_content").html(msg);
              $('#export_pdf').show();
            });
          }); 
          $("#export_pdf").unbind('click').bind('click', function (e) {
            $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
          });
        },
        voucherUsed:function(){
          $("#filter_submit").on('click', function (e) {
            $("#report_content").html("");
            e.preventDefault();
            var request = $.ajax({
              type    : "POST",
              url     : App.adminUrl + "/reports/get_voucher_used_data",
              data    : {
                start_date: $('#formFilter #input_date input').val(),
                end_date: $('#formFilter #report_end_date input').val(),
              }
            });
            request.done(function (msg) {
              $("#report_content").html(msg);
              $('#export_pdf').show();
            });
          }); 
          $("#export_pdf").unbind('click').bind('click', function (e) {
            $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
          });
        },
        memberTransaction:function(){
          $("#filter_submit").on('click', function (e) {
            $("#report_content").html("");
            e.preventDefault();
            var request = $.ajax({
              type    : "POST",
              url     : App.adminUrl + "/reports/get_member_transaction_data",
              data    : {
                start_date: $('#formFilter #input_date input').val(),
                end_date: $('#formFilter #report_end_date input').val(),
                member_id:$("#formFilter #member_id").val(),
              }
            });
            request.done(function (msg) {
              $("#report_content").html(msg);
              $('#export_pdf').show();
            });
          }); 
          $("#export_pdf").unbind('click').bind('click', function (e) {
            $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
          });
        },
        summaryReceiveOrder:function(){
          $("#filter_submit").on('click', function (e) {
            $("#report_content").html("");
            e.preventDefault();
            var request = $.ajax({
              type    : "POST",
              url     : App.adminUrl + "/reports/get_summary_receive_order_data",
              data    : {
                start_date: $('#formFilter #input_date input').val(),
                end_date: $('#formFilter #report_end_date input').val(),
                supplier_id:$("#formFilter #supplier_id").val(),
                store_id:$("#formFilter #store_id").val(),
                payment_method:$("#formFilter #payment_method").val(),
              }
            });
            request.done(function (msg) {
              $("#report_content").html(msg);
              $('#export_pdf').show();
            });
          }); 
          $("#export_pdf").unbind('click').bind('click', function (e) {
            $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
          });
        },
        summaryReturOrder:function(){
          $("#filter_submit").on('click', function (e) {
            $("#report_content").html("");
            e.preventDefault();
            var request = $.ajax({
              type    : "POST",
              url     : App.adminUrl + "/reports/get_summary_retur_order_data",
              data    : {
                start_date: $('#formFilter #input_date input').val(),
                end_date: $('#formFilter #report_end_date input').val(),
                supplier_id:$("#formFilter #supplier_id").val(),
                store_id:$("#formFilter #store_id").val(),
              }
            });
            request.done(function (msg) {
              $("#report_content").html(msg);
              $('#export_pdf').show();
            });
          }); 
          $("#export_pdf").unbind('click').bind('click', function (e) {
            $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
          });
        },
        summaryInventory:function(){
          $('#start_period').datetimepicker({

            sideBySide: true,
            useCurrent: true,
            format: 'YYYY-MM-DD',

          });

          $('#end_period').datetimepicker({
            sideBySide: true,
            useCurrent: true,
            format: 'YYYY-MM-DD',
          });

          $("#filter_submit").on('click', function (e) {
            $("#report_content").html("");
            e.preventDefault();
            var request = $.ajax({
              type    : "POST",
              url     : App.adminUrl + "/reports/get_summary_inventory_data",
              data    : {
                start_period: $('#formFilter #start_period').val(),
                end_period: $('#formFilter #end_period').val(),
                outlet_id: $("#formFilter #outlet_id").val(),
                inventory_id: $("#formFilter #inventory_id").val()
              }
            });
            request.done(function (msg) {
              $("#report_content").html(msg);
              $('#export_pdf').show();
              $('#export_xls').show();
            });
          }); 
          $("#export_pdf").unbind('click').bind('click', function (e) {
            $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
          });
		  $("#export_xls").unbind('click').bind('click', function (e) {
            $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_xls");
          });
        },
		detailInventory:function(){
          $("#filter_submit").on('click', function (e) {
            $("#report_content").html("");
            e.preventDefault();
            var request = $.ajax({
              type    : "POST",
              url     : App.adminUrl + "/reports/get_detail_inventory_data",
              data    : {
                start_date: $('#formFilter #input_date input').val(),
                end_date: $('#formFilter #report_end_date input').val(),
                outlet_id:$("#formFilter #outlet_id").val(),
                inventory_id:$("#formFilter #inventory_id").val()
              }
            });
            request.done(function (msg) {
              $("#report_content").html(msg);
              $('#export_pdf').show();
            });
          }); 
          $("#export_pdf").unbind('click').bind('click', function (e) {
            $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
          });
        },
        taxesYear:function(){
          $('#start_period').datetimepicker({

            sideBySide: true,
            useCurrent: true,
            format: 'YYYY-MM-DD',

          });

          $('#end_period').datetimepicker({
            sideBySide: true,
            useCurrent: true,
            format: 'YYYY-MM-DD',
          });

          $("#filter_submit").on('click', function (e) {
            $("#report_content").html("");
            e.preventDefault();
            var request = $.ajax({
              type    : "POST",
              url     : App.adminUrl + "/reports/get_taxes_year_data",
              data    : {
                start_date : $('#formFilter #input_start_date').val(),
                end_date : $('#formFilter #input_end_date').val(),
                tax_name : $('#formFilter #filter_taxes').val()
              }
            });
            request.done(function (msg) {
              $("#report_content").html(msg);
              $('#export_pdf').show();
            });
          }); 
          $("#export_pdf").unbind('click').bind('click', function (e) {
            $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
          });
        },
        AgingData: function(data){
          var table =  $('#table-aging-data').dataTable({
                 "bProcessing": true,
                 "bServerSide": true,
                 "sServerMethod": "POST",
                 "iDisplayLength": 10,
                 "ajax": {
                    "url": $('#dataProcessUrl').val(),
                    "type": 'POST',
                    "data": {
                      param: $('#formFilter').serialize()
                    },                       
                 },
                "columns": [   
                      {data : "name"},
                      {data : "total_payment"},
                      {data : "due_1"},
                      {data : "due_2"},
                      {data : "due_3"},
                      {data : "due_4"}
                    ],
                 "columnDefs": [
                      {
                        "targets"     : [1,2,3,4,5],
                        "orderable"   : true,
                        "bSearchable" : false
                      },
                      {
                        "targets"     : [0],
                        "orderable"   : true,
                        "bSearchable" : true
                      }
                  ],
                  "order": [[0, "asc"]],
              });
          $("#filter_submit").on('click', function (e) {
              e.preventDefault();
              var table =  $('#table-aging-data').dataTable({
                 "bProcessing"    : true,
                 "bServerSide"    : true,
                 "sServerMethod"  : "POST",
                 "bDestroy" :true,
                 "autoWidth": false,
                 "bFilter" : false, 
                 "iDisplayLength" : 10,
                 "ajax": {
                    "url": $('#dataProcessUrl').val(),
                    "type": 'POST',
                    "data": {
                      param: $('#formFilter').serialize()
                    },                       
                 },
                "columns": [        
                      {data : "name"},
                      {data : "total_payment"},
                      {data : "due_1"},
                      {data : "due_2"},
                      {data : "due_3"},
                      {data : "due_4"}
                    ],
                 "columnDefs": [
                      {
                        "targets"     : [1,2,3,4,5],
                        "orderable"   : true,
                        "bSearchable" : false
                      },
                      {
                        "targets"     : [0],
                        "orderable"   : true,
                        "bSearchable" : true
                      }
                  ],
                  "order": [[0, "asc"]],
              });
          });

          $("#export_pdf").on('click', function (e) {
            $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
          });
        },
        PriceAnalystUI: function(data){
          $("#category_menu_id").chained("#outlet_id");
          var table=$('#table-price-analyst').dataTable({
                   "bProcessing"    : true,
                   "bServerSide"    : true,
                   "sServerMethod"  : "POST",
                   "bDestroy" :true,
                   "autoWidth": false,
                   "iDisplayLength" : 10,
                   "ajax": {
                   "url": $('#dataProcessUrl').val(),
                   "type": 'POST',
                   "data": {
              param: $('#formFilter').serialize()
            },
              
           },
          "columns":[
                {data: "ctgname"},
                {data: "menu_name"},
                {data: "menu_price"},
                {data: "menu_hpp"},
                {data: "gross"},
                {data: "margin"},
                {data: "markup"}
            ],
            "columnDefs": [
                {
                    "targets": [5,6],
                    "orderable": false,
                    "bSearchable": false
                }
            ],
            "order": [[0, "asc"]]
        });

        $("#filter_submit").on('click', function (e) {

        e.preventDefault();
        var table=$('#table-price-analyst').dataTable({
                   "bProcessing"    : true,
                   "bServerSide"    : true,
                   "sServerMethod"  : "POST",
                   "bDestroy" :true,
                   "autoWidth": false,
                   "iDisplayLength" : 10,
                   "ajax": {
                   "url": $('#dataProcessUrl').val(),
                   "type": 'POST',
                   "data": {
              param: $('#formFilter').serialize()
            },
              

             "dataSrc": function( json ) {
              
              $('#export_pdf').show();                      
              return json.data;
            },   
              
           },
          "columns":[
                {data: "ctgname"},
                {data: "menu_name"},
                {data: "menu_price"},
                {data: "menu_hpp"},
                {data: "gross"},
                {data: "margin"},
                {data: "markup"}
            ],
            "columnDefs": [
                {
                    "targets": [5,6],
                    "orderable": false,
                    "bSearchable": false
                }
            ],
            "order": [[0, "asc"]]
        });
        $('#export_pdf').show();
        $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
        });
        },
        InventoryList:function(){
          $('#table-inventory-data').dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sServerMethod": "POST",
            "ajax": {
              "url": $('#dataProcessUrl').val(),
              "type": 'POST',
              "data": {
                param: $('#formFilter').serialize()
              },                       
            },
            "iDisplayLength": 10,
            "columns": [
                {data: "name"},
                {data: "on_hand"},
                {data: "total_value"},
                {data: "average_cost"},
                {data: "current_price"}
            ],
            "columnDefs": [
                {
                    "targets": 0,
                    "orderable": true,
                    "bSearchable": true
                },
                {
                    "targets": [1,2,3,4],
                    "orderable": true,
                    "bSearchable": false
                }
            ],
            "order": [[0, "asc"]]
          });
          $("#filter_submit").on('click', function (e) {
              e.preventDefault();
              var table =  $('#table-inventory-data').dataTable({
                  "bProcessing"    : true,
                  "bServerSide"    : true,
                  "sServerMethod"  : "POST",
                  "bDestroy" :true,
                  "autoWidth": false,
                  "bFilter" : false, 
                  "iDisplayLength" : 10,
                  "ajax": {
                    "url": $('#dataProcessUrl').val(),
                    "type": 'POST',
                    "data": {
                      param: $('#formFilter').serialize()
                    },                       
                  },
                  "columns": [
                      {data: "name"},
                      {data: "on_hand"},
                      {data: "total_value"},
                      {data: "average_cost"},
                      {data: "current_price"}
                  ],
                  "columnDefs": [
                      {
                          "targets": 0,
                          "orderable": true,
                          "bSearchable": true
                      },
                      {
                          "targets": [1,2,3,4],
                          "orderable": true,
                          "bSearchable": false
                      }
                  ],
                  "order": [[0, "asc"]]
              });
          });
        },
        summaryYear:function(){
          $("#filter_submit").on('click', function (e) {
            $("#report_content").html("");
            e.preventDefault();
            var request = $.ajax({
              type    : "POST",
              url     : App.adminUrl + "/reports/get_summary_year_data",
              data    : {
                year   : $('#formFilter #filter_year').val()
              }
            });
            request.done(function (msg) {
              $("#report_content").html(msg);
              $('#export_pdf').show();
            });
          }); 
          $("#export_pdf").unbind('click').bind('click', function (e) {
            $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
          });
        },
        deleteOrder:function(){
          var table =  $('#dataTables-delete-order').dataTable({
                 "bProcessing"    : true,
                 "bServerSide"    : true,
                 "sServerMethod"  : "POST",
                 "bDestroy" :true,
                 "autoWidth": false,
                 "bFilter" : false, 
                 "iDisplayLength" : 10,
                 "ajax": {
                    "url": $('#dataProcessUrl').val(),
                    "type": 'POST',
                    "data": {
                      param: $('#formFilter').serialize()
                    },                       
                 },
                "columns": [                  
                      {data : "created_at"},
                      {data : "name"},
                      {data : "actions"}
                    ],
                 "columnDefs": [
                      {
                        "targets"     : 2,
                        "orderable"   : false,
                        "bSearchable" : false
                      }
                  ],
                  "order": [[0, "desc"]],
              });

          $("#filter_submit").on('click', function (e) {
              e.preventDefault();
              var table =  $('#dataTables-delete-order').dataTable({
                 "bProcessing"    : true,
                 "bServerSide"    : true,
                 "sServerMethod"  : "POST",
                 "bDestroy" :true,
                 "autoWidth": false,
                 "bFilter" : false, 
                 "iDisplayLength" : 10,
                 "ajax": {
                    "url": $('#dataProcessUrl').val(),
                    "type": 'POST',
                    "data": {
                      param: $('#formFilter').serialize()
                    },                       
                 },
                "columns": [                  
                      {data : "created_at"},
                      {data : "name"},
                      {data : "actions"}
                    ],
                 "columnDefs": [
                      {
                        "targets"     : 2,
                        "orderable"   : false,
                        "bSearchable" : false
                      }
                  ],
                  "order": [[0, "desc"]],
              });
          });
        },
        refund:function(){

          var table =  $('#dataTables-refund').dataTable({
                 "bProcessing"    : true,
                 "bServerSide"    : true,
                 "sServerMethod"  : "POST",
                 "bDestroy" :true,
                 "autoWidth": false,
                 "bFilter" : false, 
                 "iDisplayLength" : 10,
                 "ajax": {
                    "url": $('#dataProcessUrl').val(),
                    "type": 'POST',
                    "data": {
                      param: $('#formFilter').serialize()
                    },                       
                 },
                "columns": [
                      {data : "created_at"},
                      {data : "name"},
                      {data : "cost_refund"},
                      {data : "actions"}
                    ],
                 "columnDefs": [
                      {
                        "targets"     : [2,3],
                        "orderable"   : false,
                        "bSearchable" : false
                      }
                  ],
                  "order": [[0, "desc"]],
              });

          $("#filter_submit").on('click', function (e) {
              e.preventDefault();
              var table =  $('#dataTables-refund').dataTable({
                 "bProcessing"    : true,
                 "bServerSide"    : true,
                 "sServerMethod"  : "POST",
                 "bDestroy" :true,
                 "autoWidth": false,
                 "bFilter" : false, 
                 "iDisplayLength" : 10,
                 "ajax": {
                    "url": $('#dataProcessUrl').val(),
                    "type": 'POST',
                    "data": {
                      param: $('#formFilter').serialize()
                    },                       
                 },
                "columns": [                                
                      {data : "created_at"},
                      {data : "name"},
                      {data : "cost_refund"},
                      {data : "actions"}
                    ],
                 "columnDefs": [
                      {
                        "targets"     : [2,3],
                        "orderable"   : false,
                        "bSearchable" : false
                      }
                  ],
                  "order": [[0, "desc"]],
              });
          });
        },

        pettyCash:function(){
          

           var table =  $('#table-petty-cash').dataTable({
                   "bProcessing"    : true,
                   "bServerSide"    : true,
                   "sServerMethod"  : "POST",
                   // "ajax"           : $('#dataProcessUrl').val(),
                   "bDestroy" :true,
                   "autoWidth": false,
                   "iDisplayLength" : 10,
                   "ajax": {
                    "url": $('#dataProcessUrl').val(),
                    "type": 'POST',
                    "data": {
                      param: $('#formFilter').serialize()
                    }, 
                    },

                "columns"        : [
                    
                    
                    {data : "date"},
                    {data : "name"},
                    {data : "gename"},
                    {data : "description"},
                    {data : "amount"}
                    ],
                "columnDefs"     : [
              {
                "targets"     : 3,
                "orderable"   : false,
                "bSearchable" : false,
                "class"       : 'center-tr'
              }
                ],
                "order": [[0, "desc"]],
              }); //end datatable
            
           
           $("#filter_submit").on('click', function (e) {
            // if($('#store_id').val() == 0){
            //   App.alert("Please Select Store"); 
            //   return false; 
            // }
                e.preventDefault();
                var table =  $('#table-petty-cash').dataTable({
                   "bProcessing"    : true,
                   "bServerSide"    : true,
                   "sServerMethod"  : "POST",
                   // "ajax"           : $('#dataProcessUrl').val(),
                   "bDestroy" :true,
                   "autoWidth": false,
                   "iDisplayLength" : 10,
                   "ajax": {
                    "url": $('#dataProcessUrl').val(),
                    "type": 'POST',
                    "data": {
                      param: $('#formFilter').serialize()
                    }, 
                    },

                "columns"        : [
                    
                  
                    {data : "date"},
                    {data : "name"},
                    {data : "gename"},
                    {data : "description"},
                    {data : "amount"}
                    ],
                "columnDefs"     : [
              {
                "targets"     : 3,
                "orderable"   : false,
                "bSearchable" : false,
                "class"       : 'center-tr'
              }
                ],
                "order": [[0, "desc"]],
              }); //end datatable
            
                $('#export_pdf').show();

            });

          $("#export_pdf").unbind('click').bind('click', function (e) {
            $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
              
             }); 

        },

        sidedish:function(){
          

           var table =  $('#table-side-dish').dataTable({
                   "bProcessing"    : true,
                   "bServerSide"    : true,
                   "sServerMethod"  : "POST",
                   // "ajax"           : $('#dataProcessUrl').val(),
                   "bDestroy" :true,
                   "autoWidth": false,
                   "iDisplayLength" : 10,
                   "ajax": {
                    "url": $('#dataProcessUrl').val(),
                    "type": 'POST',
                    "data": {
                      param: $('#formFilter').serialize()
                    }, 
                    },

                "columns"        : [
                    
                    
                    {data : "sdname"},
                    {data : "sdprice"},
                    {data : "bmname"},
                    {data : "blreceipt"}
             
                    ],
                "columnDefs"     : [
              {
                "targets"     : 3,
                "orderable"   : false,
                "bSearchable" : false,
                "class"       : 'center-tr'
              }
                ],
                "order": [[0, "desc"]],
              }); //end datatable
            
           
           $("#filter_submit").on('click', function (e) {
            // if($('#store_id').val() == 0){
            //   App.alert("Please Select Store"); 
            //   return false; 
            // }
                e.preventDefault();
                var table =  $('#table-side-dish').dataTable({
                   "bProcessing"    : true,
                   "bServerSide"    : true,
                   "sServerMethod"  : "POST",
                   // "ajax"           : $('#dataProcessUrl').val(),
                   "bDestroy" :true,
                   "autoWidth": false,
                   "iDisplayLength" : 10,
                   "ajax": {
                    "url": $('#dataProcessUrl').val(),
                    "type": 'POST',
                    "data": {
                      param: $('#formFilter').serialize()
                    }, 
                    },

                "columns"        : [
                    
                  
                    {data : "sdname"},
                    {data : "sdprice"},
                    {data : "bmname"},
                    {data : "blreceipt"}
                    
                    ],
                "columnDefs"     : [
              {
                "targets"     : 3,
                "orderable"   : false,
                "bSearchable" : false,
                "class"       : 'center-tr'
              }
                ],
                "order": [[0, "desc"]],
              }); //end datatable
            
                $('#export_pdf').show();

            });

          $("#export_pdf").unbind('click').bind('click', function (e) {
            $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
              
             }); 

        },


        inventoryAdjustment:function(){
          $("#filter_submit").on('click', function (e) {
            $("#report_content").html("");
            e.preventDefault();
            var request = $.ajax({
              type    : "POST",
              url     : App.adminUrl + "/reports/get_inventory_adjustment",
              data    : {
                outlet_id   : $('#formFilter #outlet_id').val(),
                store_id   : $('#formFilter #store_id').val(),
                date   : $('#formFilter #input_date input').val()
              }
            });
            request.done(function (msg) {
              $("#report_content").html(msg);
              // $('#export_pdf').show();
            });
          }); 
          $("#export_pdf").unbind('click').bind('click', function (e) {
            $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
          });
        },


 salesOutletUI: function(){

          var table =  $('#table-sales-menu').dataTable({
                   "bProcessing"    : true,
                   "bServerSide"    : true,
                   "sServerMethod"  : "POST",
                   "bDestroy" :true,
                   "autoWidth": false,
                   "bFilter" : false, 
                   "iDisplayLength" : 10,
                   "ajax": {
                    "url": $('#dataProcessUrl').val(),
                    "type": 'POST',
                    "data": {
                      param: $('#formFilter').serialize()
                    },
                     "dataSrc": function( json ) {
                     
                      
                        App.detailTransaction(json.data);
                        return json.data;
                    },   
                      
                 },

                "columns"        : [
                    
                  
                    {data : "outlet_name"},
                    {data : "total_quantity"},
                    {data : "total_price"},
                    ],
               "columnDefs"     : [
                    {
                        
                    }
                ],
                "order"          : [[0, "desc"]],
               

                });

           $("#filter_submit").on('click', function (e) {

                e.preventDefault();

                var table =  $('#table-sales-menu').dataTable({
                   "bProcessing"    : true,
                   "bServerSide"    : true,
                   "sServerMethod"  : "POST",
                   "bDestroy" :true,
                   "autoWidth": false,
                   "bFilter" : false, 
                   "iDisplayLength" : 10,
                   "ajax": {
                    "url": $('#dataProcessUrl').val(),
                    "type": 'POST',
                    "data": {
                      param: $('#formFilter').serialize()
                    },
                     "dataSrc": function( json ) {
                     
                      $('#export_pdf').show();
                        App.detailTransaction(json.data);
                        return json.data;
                    },   
                      
                 },

                "columns"        : [
                    
                    
                    {data : "outlet_name"},
                    {data : "total_quantity"},
                    {data : "total_price"},
                    ],
               "columnDefs"     : [
                    {
                        
                    }
                ],
                "order"          : [[0, "desc"]],
               

                }); //end datatable

              $("#export_pdf").unbind('click').bind('click', function (e) {
                $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");

             }); //end ajax request


            

            });
      

        },

		transferMenu:function(){
          $("#filter_submit").on('click', function (e) {
            $("#report_content").html("");
            e.preventDefault();
            var request = $.ajax({
              type    : "POST",
              url     : App.adminUrl + "/reports/get_transfer_menu",
              data    : {
                date   : $('#formFilter #input_date input').val(),
                report_end_date   : $('#formFilter #report_end_date input').val()
              }
            });
            request.done(function (msg) {
              $("#report_content").html(msg);
              $('#export_pdf').show();
            });
          }); 
          $("#export_pdf").unbind('click').bind('click', function (e) {
            $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
          });
        },

    transferinventory:function(){
          $("#filter_submit").on('click', function (e) {
            if($('#store_id_start').val() == $('#store_id_end').val()){
              App.alert("Please do not choose the same Store"); 
              return false; 
            }
            if($('#outlet_id_start').val() == $('#outlet_id_end').val()){
              App.alert("Please do not choose the same Outlet"); 
              return false; 
            }
            $("#report_content").html("");
            e.preventDefault();
            var request = $.ajax({
              type    : "POST",
              url     : App.adminUrl + "/reports/get_transfer_inventory",
              data    : {
                start_date   : $('#formFilter #start_date input').val(),
                end_date   : $('#formFilter #end_date input').val(),
                store_id_start   : $('#store_id_start ').val(),
                store_id_end   : $('#store_id_end ').val(),
                outlet_id_start   : $('#outlet_id_start ').val(),
                outlet_id_end   : $('#outlet_id_end ').val(),
                inventory_id  : $('#inventory_id ').val()
              }
            });
            request.done(function (msg) {
              $("#report_content").html(msg);
              $('#export_pdf').show();
            });
          }); 
          $("#export_pdf").unbind('click').bind('click', function (e) {
            $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
          });
        },


        inventoryStockReportUI:function(){
          $("#filter_submit").on('click', function (e) {
            $("#report_content").html("");
            e.preventDefault();
            var request = $.ajax({
              type    : "POST",
              url     : App.adminUrl + "/reports/get_inventory_stock_data",
              data    : {
                store_id   : $('#formFilter #store_id').val(),
                date   : $('#formFilter #input_date input').val(),
                report_end_date   : $('#formFilter #report_end_date input').val()
              }
            });
            request.done(function (msg) {
              $("#report_content").html(msg);
              $('#export_pdf').show();
              // $('#export_xls').show();
              // $("#export_xls").unbind('click').bind('click', function (e) {
                // $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_excel");
              // }); 
            });
          }); 
          $("#export_pdf").unbind('click').bind('click', function (e) {
            $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
          });
        },
        totalSalesWaiterUI:function(){
          $("#filter_submit").on('click', function (e) {
            $("#report_content").html("");
            e.preventDefault();
            var request = $.ajax({
              type    : "POST",
              url     : App.adminUrl + "/reports/get_total_sales_waiter",
              data    : {
                user_id   : $('#formFilter #user_id').val(),
                date   : $('#formFilter #input_date input').val()
              }
            });
            request.done(function (msg) {
              $("#report_content").html(msg);
              $('#export_pdf').show();
            });
          }); 
          $("#export_pdf").unbind('click').bind('click', function (e) {
            $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
          });
        },
        kitchenDurationUI:function(){
          $('#month_year').datetimepicker({
            sideBySide: true,
            useCurrent: true,
            format: 'M-YYYY',
          }); 
          $("#filter_submit").on('click', function (e) {
            $("#report_content").html("");
            e.preventDefault();
            var request = $.ajax({
              type    : "POST",
              url     : App.adminUrl + "/reports/get_kitchen_duration",
              data    : {
                month_year   : $('#formFilter #month_year').val()
              }
            });
            request.done(function (msg) {
              $("#report_content").html(msg);
              $('#export_pdf').show();
            });
          }); 
          $("#export_pdf").unbind('click').bind('click', function (e) {
            $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
          });
        },
        achievementWaiter:function(){
          $('#month_year').datetimepicker({
            sideBySide: true,
            useCurrent: true,
            format: 'M-YYYY',
          });
          $("#filter_submit").on('click', function (e) {
            $("#report_content").html("");
            e.preventDefault();
            var request = $.ajax({
              type    : "POST",
              url     : App.adminUrl + "/reports/get_achievement_waiter",
              data    : {
                user_id   : $('#formFilter #user_id').val(),
                month_year   : $('#formFilter #month_year').val()
              }
            });
            request.done(function (msg) {
              $("#report_content").html(msg);
              $('#export_pdf').show();
            });
          }); 
          $("#export_pdf").unbind('click').bind('click', function (e) {
            $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
          });
        },
        achievementWaiterDetail:function(){
          $("#export_pdf").unbind('click').bind('click', function (e) {
            $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
          });
        },
        totalSalesWaiterDetailUI:function(){
          $("#export_pdf").unbind('click').bind('click', function (e) {
            $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
          });
        },
        totalQtyOrderTableWaiter:function(){
          $("#filter_submit").on('click', function (e) {
            $("#report_content").html("");
            e.preventDefault();
            var request = $.ajax({
              type    : "POST",
              url     : App.adminUrl + "/reports/get_total_quantity_order_table_waiter",
              data    : {
                user_id   : $('#formFilter #user_id').val(),
                date   : $('#formFilter #input_date input').val()
              }
            });
            request.done(function (msg) {
              $("#report_content").html(msg);
              $('#export_pdf').show();
            });
          }); 
          $("#export_pdf").unbind('click').bind('click', function (e) {
            $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
          });
        },
        moneyFormat:function(n, currency){
         return currency + " " + n.toFixed(0).replace(/./g, function(c, i, a) {
                return i > 0 && c !== "," && (a.length - i) % 3 === 0 ? "." + c : c;
            });
         
        },
        voidUI: function(){

            var table =  $('#table-void').dataTable({
                   "bProcessing"    : true,
                   "bServerSide"    : true,
                   "sServerMethod"  : "POST",
                   "bDestroy" :true,
                   "autoWidth": false,
                   "bFilter" : false, 
                   "iDisplayLength" : 10,
                   "ajax": {
                    "url": $('#dataProcessUrl').val(),
                    "type": 'POST',
                    "data": {
                      param: $('#formFilter').serialize()
                    },
                    "dataSrc": function( json ) {
                     
                      $('#export_pdf').show();
                        App.detailVoid(json.data);
                        return json.data;
                    },   
                      
                 },

                "columns"        : [
                    
                    {data : "created_at"},
                    {data : "menu_name"},
                    {data : "amount"},
                    {data : "void_note"},
                    {data : "name"},
                    {data : "user_unlock_name"},
                    {data : "is_deduct_stock"},
                    {data : "cost_void"}
                    ],
             
                "order"          : [[0, "desc"]],
               

                });

           $("#filter_submit").on('click', function (e) {

                e.preventDefault();

                var table =  $('#table-void').dataTable({
                   "bProcessing"    : true,
                   "bServerSide"    : true,
                   "sServerMethod"  : "POST",
                   "bDestroy" :true,
                   "autoWidth": false,
                   "bFilter" : false, 
                   "iDisplayLength" : 10,
                   "ajax": {
                    "url": $('#dataProcessUrl').val(),
                    "type": 'POST',
                    "data": {
                      param: $('#formFilter').serialize()
                    },
                    "dataSrc": function( json ) {
                     
                      $('#export_pdf').show();
                        App.detailVoid(json.data);
                        return json.data;
                    },   
                      
                 },

                "columns"        : [
                    
                    {data : "created_at"},
                    {data : "menu_name"},
                    {data : "amount"},
                    {data : "void_note"},
                    {data : "name"},
                    {data : "user_unlock_name"},
                    {data : "is_deduct_stock"},
                    {data : "cost_void"}
                    ],
             
                "order"          : [[0, "desc"]],
               

                }); //end datatable

             //   $("#export_pdf").unbind('click').bind('click', function (e) {
             //    e.preventDefault();
             //    $.ajax({
             //      url      : App.adminUrl + "/reports/export_report_to_pdf",
             //      type     : "POST",
             //      dataType : "json",
             //      data     : {
             //        type      : 'void',
             //        start_date: $('#input_start_date').val(),
             //        end_date: $('#input_end_date').val(),

             //      },
             //      success  : function (result) {
             //        console.log(App.baseUrl + result);
             //        if (result != '') {
             //                window.open(App.baseUrl + result, '_newtab')
             //              }
             //              else
             //                alert('Export report gagal');
             //            }
             //          });
             // }); //end ajax request

            
            

            });

            $("#export_pdf").unbind('click').bind('click', function (e) {
              $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
            });
         

        },

        profitLossUI: function () {

          var request = $.ajax({
                type    : "POST",
                url     : App.adminUrl + "/reports/get_report_profit_lose",
                data    : {
                  start_date      : $('#input_start_date').val(),
                  end_date      : $('#input_end_date').val()

                }
              });

              request.done(function (msg) {
                $('#report_content').html(msg);
                

              });

          $("#filter_submit").on('click', function (e) {
            e.preventDefault();

              var request = $.ajax({
                type    : "POST",
                url     : App.adminUrl + "/reports/get_report_profit_lose",
                data    : {
                  start_date      : $('#input_start_date').val(),
                  end_date      : $('#input_end_date').val()

                }
              });

              request.done(function (msg) {
                $('#report_content').html(msg);
                

              });


              


           
            });// end button event
          $("#export_pdf").unbind('click').bind('click', function (e) {
              $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
            });
// $("#export_pdf").on('click', function (e) {
//                 e.preventDefault();
//                 $.ajax({
//                   url      : App.adminUrl + "/reports/export_report_to_pdf",
//                   type     : "POST",
//                   dataType : "json",
//                   data     : {
//                     type      : 'profit_loss',
//                     start_date: $('#input_start_date').val(),
//                     end_date: $('#input_end_date').val(),

//                   },
//                   success  : function (result) {
//                     console.log(App.baseUrl + result);
//                     if (result != '') {
//                             // window.location=App.adminUrl+"download.php?filename="+result;
//                             // window.location.href = App.baseUrl + result;
//                             window.open(App.baseUrl + result, '_newtab')
//                           }
//                           else
//                             alert('Export report gagal');
//                         }
//                       });
            // }); //end ajax request


        },
         salesMenuDetail: function(){

          var table =  $('#table-sales-menu-detail').dataTable({
           "bProcessing"    : true,
           "bServerSide"    : true,
           "sServerMethod"  : "POST",
                   // "ajax"           : $('#dataProcessUrl').val(),
                   "bDestroy" :true,
                   "bFilter" : false, 
                   "autoWidth": false,
                   "iDisplayLength" : 10,
                   "ajax": {
                    "url": $('#data_sales_menu_detail').val(),
                    "type": 'POST',
                    "data": function ( d , callback,settings) { 
                      return $('#formFilter').serialize();
                    },

                    "dataSrc": function( json ) {

                      App.detailTransaction(json.data);
                      return json.data;
                    },   

                  },

                  "columns"        : [

                  {data : "created_at"},
                  {data : "receipt_number"},
                  {data : "quantity"},
                  {data : "price"},
                  {data : "cogs"},
                  {data : "profit"},
                  // {data : "actions"}
                  ],

                  "order"          : [[0, "desc"]],

                }); //end datatable
            
        },

        salesMenuUI: function(){

            var table =  $('#table-sales-menu').dataTable({
                   "bProcessing"    : true,
                   "bServerSide"    : true,
                   "sServerMethod"  : "POST",
                   "bDestroy" :true,
                   "autoWidth": false,
                   "bFilter" : false, 
                   "iDisplayLength" : 10,
                   "ajax": {
                    "url": $('#dataProcessUrl').val(),
                    "type": 'POST',
                    "data": {
                      param: $('#formFilter').serialize()
                    },
                     "dataSrc": function( json ) {
                     
                      
                        App.detailTransaction(json.data);
                        return json.data;
                    },   
                      
                 },

                "columns"        : [
                    
                    // {data : "created_at"},
                    {data : "category_name"},
                    {data : "menu_name"},
                    {data : "total_quantity"},
                    {data : "total_price"},
                    // {data : "total_cogs"},
                    // {data : "total_profit"},
                    {data : "actions"}
                    ],
               "columnDefs"     : [
                    {
                        "targets"     : 4,
                        "orderable" : false
                    }
                ],
                "order"          : [[0, "desc"]],
               

                });

           $("#filter_submit").on('click', function (e) {

                e.preventDefault();

                var table =  $('#table-sales-menu').dataTable({
                   "bProcessing"    : true,
                   "bServerSide"    : true,
                   "sServerMethod"  : "POST",
                   "bDestroy" :true,
                   "autoWidth": false,
                   "bFilter" : false, 
                   "iDisplayLength" : 10,
                   "ajax": {
                    "url": $('#dataProcessUrl').val(),
                    "type": 'POST',
                    "data": {
                      param: $('#formFilter').serialize()
                    },
                     "dataSrc": function( json ) {
                     
                      
                        App.detailTransaction(json.data);
                        return json.data;
                    },   
                      
                 },

                "columns"        : [
                    
                    // {data : "created_at"},
                    {data : "category_name"},
                    {data : "menu_name"},
                    {data : "total_quantity"},
                    {data : "total_price"},
                    // {data : "total_cogs"},
                    // {data : "total_profit"},
                    {data : "actions"}
                    ],
               "columnDefs"     : [
                    {
                        "targets"     : 4,
                        "orderable" : false
                    }
                ],
                "order"          : [[0, "desc"]],
               

                }); //end datatable

             


            

            });
            $("#export_pdf").unbind('click').bind('click', function (e) {
              $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
            });
            
           // $("#export_pdf").unbind('click').bind('click', function (e) {
           //      e.preventDefault();
           //      $.ajax({
           //        url      : App.adminUrl + "/reports/export_report_to_pdf",
           //        type     : "POST",
           //        dataType : "json",
           //        data     : {
           //          type      : 'sales_menu',
           //          start_date: $('#input_start_date').val(),
           //          category_id: $('#category_id').val(),
           //          end_date: $('#input_end_date').val(),

           //        },
           //        success  : function (result) {
           //          console.log(App.baseUrl + result);
           //          if (result != '') {
           //                  window.open(App.baseUrl + result, '_newtab')
           //                }
           //                else
           //                  alert('Export report gagal');
           //              }
           //            });
  

        },
        salesMovingItem: function(){

          var table =  $('#table-moving-item').dataTable({
                   "bProcessing"    : true,
                   "bServerSide"    : true,
                   "sServerMethod"  : "POST",
                   "bDestroy" :true,
                   "autoWidth": false,
                   "bFilter" : true, 
                   "scrollX": true,
                   "iDisplayLength" : 10,
                   "ajax": {
                   "url": $('#dataProcessUrlMoving').val(),
                   "type": 'POST',
                    "data": {
                      param: $('#formFilter').serialize()
                    },
                     "dataSrc": function( json ) {
                     
                     
                        App.detailTransaction(json.data);
                        return json.data;
                    },   

                      
                 },

                "columns"        : [ 
                    {data : "menu_name"},
                   
                    {data : "harga_menu"},
                    {data : "harga_hpp"},
                    {
                      "data": "qty_reguler", // can be null or undefined
                      "defaultContent": 0
                    },
                    {
                      "data": "qty_compliment", // can be null or undefined
                      "defaultContent": 0
                    },
                    {data : "total_quantity"},
                    {data : "total_reguler"},
                    {
                      "data": "total_compliment", // can be null or undefined
                      "defaultContent": 0
                    },
                    // {data : "total_profit"},
                    {data : "actions"}
                    ],
               "columnDefs"     : [
                    {
                        "targets"     : 8,
                        "orderable" : false
                    },
                     {
                        "targets"     : [1,2,3,4,5,6,7,8],
                        "bSearchable" : false
                    },
                    { 
                        "render": function ( data, type, row ) { 
                            if(!data){ 
                              return 0;
                            }
                            return App.moneyFormat(parseInt(data),"Rp.");
                        },
                        "targets": [6,7]
                    },
                     {
                        "targets"     : [3,4,5], 
                        "class"       : 'center-tr'
                    }
                ],

                "order"          : [[1, "desc"]],
               

                });

           $("#filter_submit").on('click', function (e) {
            
                e.preventDefault();

                var table =  $('#table-moving-item').dataTable({
                   "bProcessing"    : true,
                   "bServerSide"    : true,
                   "sServerMethod"  : "POST",
                   "bDestroy" :true,
                   "autoWidth": false,
                   "bFilter" : true, 
                   "scrollX": true,
                   "iDisplayLength" : 10,
                   "ajax": {
                   "url": $('#dataProcessUrlMoving').val(),
                   "type": 'POST',
                    "data": {
                      param: $('#formFilter').serialize()
                    },
                     "dataSrc": function( json ) {
                     
                      
                        App.detailTransaction(json.data);
                        return json.data;
                    },   

                      
                 },

                "columns"        : [ 
                    {data : "menu_name"},
                   
                    {data : "harga_menu"},
                    {data : "harga_hpp"},
                    {
                      "data": "qty_reguler", // can be null or undefined
                      "defaultContent": 0
                    },
                    {
                      "data": "qty_compliment", // can be null or undefined
                      "defaultContent": 0
                    },
                    {data : "total_quantity"},
                    {data : "total_reguler"},
                    {
                      "data": "total_compliment", // can be null or undefined
                      "defaultContent": 0
                    },
                    // {data : "total_profit"},
                    {data : "actions"}
                    ],
               "columnDefs"     : [
                    {
                        "targets"     : 8,
                        "orderable" : false
                    },
                     {
                        "targets"     : [1,2,3,4,5,6,7,8],
                        "bSearchable" : false
                    },
                    { 
                        "render": function ( data, type, row ) { 
                            if(!data){ 
                              return 0;
                            }
                            return App.moneyFormat(parseInt(data),"Rp.");
                        },
                        "targets": [6,7]
                    },
                     {
                        "targets"     : [3,4,5], 
                        "class"       : 'center-tr'
                    }
                ],

                "order"          : [[1, "desc"]],
               

                }); //end datatable

               
            });
           $("#export_pdf").unbind('click').bind('click', function (e) {
              $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
            });

            
         

        },
        salesCategoryUI: function(){
			var table= $('#table-sales-category').dataTable({
					"bProcessing" : true,
					"bServerSide" : true,
					"sServerMethod" : "POST",
					"bDestroy" :true,
					"autoWidth": false,
					"iDisplayLength" : 10,
					"ajax": {
					"url": $('#dataProcessUrl').val(),
					"type": 'POST',
					"data": {
						param: $('#formFilter').serialize()
					},
					"dataSrc": function( json ) {
						return json.data;
					},   
				},
				"columns": [
					{data : "outlet_name"},
					{data : "category_name"},
					{data : "total_quantity"},
					{data : "total_price"},
				],
				"order": [[0, "asc"]]
			});

            $("#filter_submit").on('click', function (e) {
				e.preventDefault();
				var table=$('#table-sales-category').dataTable({
					"bProcessing" : true,
					"bServerSide" : true,
					"sServerMethod" : "POST",
					"bDestroy" :true,
					"autoWidth": false,
					"iDisplayLength" : 10,
					"ajax": {
                    "url": $('#dataProcessUrl').val(),
						"type": 'POST',
						"data": {
							param: $('#formFilter').serialize()
						},
						"dataSrc": function( json ) {
							$('#export_pdf').show();                      
							return json.data;
						},
					 },
					"columns": [
						{data : "outlet_name"},
						{data : "category_name"},
						{data : "total_quantity"},
						{data : "total_price"},
					],
					"order": [[0, "asc"]]
				});
                $('#export_pdf').show();
				$("#export_pdf").unbind('click').bind('click', function (e) {
					$("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
				});
            });
      

        },
        openCloseUI       : function () {

          var table =  $('#table-open-close').dataTable({
                   "bProcessing"    : true,
                   "bServerSide"    : true,
                   "sServerMethod"  : "POST",
                   "bFilter" : false, 
                   // "ajax"           : $('#dataProcessUrl').val(),
                   "bDestroy" :true,
                   "autoWidth": false,
                   "iDisplayLength" : 10,
                   "ajax": {
                    "url": $('#dataProcessUrl').val(),
                    "type": 'POST',
                    "data": {
                      param: $('#formFilter').serialize()
                    },
                      

                     "dataSrc": function( json ) {
                      
                      $('#export_pdf').show();                      
                        App.detailTransaction(json.data);
                        return json.data;
                    },   
                      
                 },

                "columns"        : [
                    
                   {data : "open_at"},
                    {data : "open_by_user"},
                    {data : "close_at"},
                    {data : "close_by_user"},
                    {data : "total_transaction"},
                    {data : "total_cash"},
                    {data : "actions"}
                    ],
               
                "order"          : [[0, "desc"]],

                });

           $("#filter_submit").on('click', function (e) {

                e.preventDefault();

                var table =  $('#table-open-close').dataTable({
                   "bProcessing"    : true,
                   "bServerSide"    : true,
                   "sServerMethod"  : "POST",
                   "bFilter" : false, 
                   // "ajax"           : $('#dataProcessUrl').val(),
                   "bDestroy" :true,
                   "autoWidth": false,
                   "iDisplayLength" : 10,
                   "ajax": {
                    "url": $('#dataProcessUrl').val(),
                    "type": 'POST',
                    "data": {
                      param: $('#formFilter').serialize()
                    },
                      

                     "dataSrc": function( json ) {
                      
                      $('#export_pdf').show();                      
                        App.detailTransaction(json.data);
                        return json.data;
                    },   
                      
                 },

                "columns"        : [
                    
                   {data : "open_at"},
                    {data : "open_by_user"},
                    {data : "close_at"},
                    {data : "close_by_user"},
                    {data : "total_transaction"},
                    {data : "total_cash"},
                    {data : "actions"}
                    ],
               
                "order"          : [[0, "desc"]],

                }); //end datatable
								$(document).off('click').on('click','.print_open_close_cashier',function(e){
									url=$(this).attr("href");
									$.ajax({
										url:url
									});
                                    return false;
								});
            });

          // $("#export_pdf").unbind('click').bind('click', function (e) {
          //       e.preventDefault();
          //       $.ajax({
          //         url      : App.adminUrl + "/reports/export_report_to_pdf",
          //         type     : "POST",
          //         dataType : "json",
          //         data     : {
          //           type      : 'open_close',
          //           start_date: $('#input_start_date').val(),
          //           end_date: $('#input_end_date').val(),
          //           close_by: $('#close_by').val(),
          //           open_by: $('#open_by').val(),

          //         },
          //         success  : function (result) {
          //           console.log(App.baseUrl + result);
          //           if (result != '') {
          //                   window.open(App.baseUrl + result, '_newtab')
          //                 }
          //                 else
          //                   alert('Export report gagal');
          //               }
          //             });
          //    }); //end ajax request

            $("#export_pdf").unbind('click').bind('click', function (e) {
              $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
            });

        },
				getSummaryTransaction:function(){
          $('.loading-summary').show();
					data=$("#formFilter").serialize();
					url=App.baseUrl+"admincms/reports/get_summary_transaction";
					$.ajax({
						url:url,
						type:"POST",
						dataType:"JSON",
						data:data,
						success:function(response){
							$("#summary-tab").html(response.content);
              $('.loading-summary').hide();
						}
					})
				},
        transactionUI       : function () {
          $('.loading-summary').show();

                App.getSummaryTransaction();
          var table =  $('#table-transaction').dataTable({
                   "bProcessing"    : true,
                   "bServerSide"    : true,
                   "sServerMethod"  : "POST",
                   "bFilter" :false,                   
                   "bDestroy" :true,
                   "autoWidth": false,
                   // "ordering" : false,
                   "iDisplayLength" : 10,
                   "ajax": {
                    "url": $('#dataProcessUrl').val(),
                    "type": 'POST',
                   "data": {
                      param: $('#formFilter').serialize()
                    },
                    // "dataSrc": function( json ) {
                      // $('#export_pdf').show();                      
                        // App.detailTransaction(json.data);
                        // return json.data;
                    // },   
                      
                 },

                 "columns"        : [
                     {data : "payment_date"},
                     {data : "receipt_number"},
                     {data : "order_type"},
                     {data : "total_price_rp"},
                     {data : "customer_count"},
                     {data : "order_id"},
                     {data : "actions"}

                 ],

                 "columnDefs"     : [
                    {
                        "targets"     : 6,
                        "orderable" : false
                    }
                ],

                "order"          : [[0, "desc"]],
               



                });
            $("#reprint_billing_filter_submit").on('click', function (e) {
              $("#formFilter").removeAttr("action");
              $("#formFilter").removeAttr("target");
            });

            $("#reprint_billing_export_pdf").on('click', function (e) {
              $("#formFilter").attr("action",App.baseUrl + "admincms/reprint_billings/export_to_pdf");
              $("#formFilter").attr("target","_blank");
            });

           $("#filter_submit").on('click', function (e) {
                $('.loading-summary').show();

                e.preventDefault();
								App.getSummaryTransaction();
                var table =  $('#table-transaction').dataTable({
                   "bProcessing"    : true,
                   "bServerSide"    : true,
                   "sServerMethod"  : "POST",
                   "bFilter" :false,                   
                   "bDestroy" :true,
                   "autoWidth": false,
                   // "ordering" : false,
                   "iDisplayLength" : 10,
                   "ajax": {
                    "url": $('#dataProcessUrl').val(),
                    "type": 'POST',
                   "data": {
                      param: $('#formFilter').serialize()
                    },
										// "dataSrc": function( json ) {
                      // $('#export_pdf').show();                      
                        // App.detailTransaction(json.data);
                        // return json.data;
                    // },   
                      
                 },

                 "columns"        : [
                     {data : "payment_date"},
                     {data : "receipt_number"},
                     {data : "order_type"},
                     {data : "total_price_rp"},
                     {data : "customer_count"},
                     {data : "order_id"},
                     {data : "actions"}

                 ],

                 "columnDefs"     : [
                    {
                        "targets"     : 6,
                        "orderable" : false
                    }
                ],

                "order"          : [[0, "desc"]],
               



                }); //end datatable
								// $('#export_pdf').show();      
              
            });

            
            // $("#export_pdf").on('click', function (e) {
            // e.preventDefault();
                // $.ajax({
                    // url      : App.adminUrl + "/reports/export_report_to_pdf",
                    // type     : "POST",
                    // dataType : "json",
                    // data     : {
                        // type      : 'transaction',
                        // user_id: $('#user_id').val(),
                        // start_date: $('#input_start_date').val(),
                        // end_date: $('#input_end_date').val(),

                    // },
                    // success  : function (result) {
                        // if (result != '') {
                            // window.open(App.baseUrl + result, '_newtab')
                        // }
                        // else
                            // alert('Export report gagal');
                    // }
                // });
            // });
            $("#export_pdf").unbind('click').bind('click', function (e) {
              $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
            });


        },
        transactionDetailUI: function(){

            var tableOrder =  $('#table-transaction-order').dataTable({
                   "bProcessing"    : true,
                   "bServerSide"    : true,
                   "sServerMethod"  : "POST",
                   "ajax"           : $('#data_transaction_order').val(),
                   "bDestroy" :true,
                   "autoWidth": false,
                   "iDisplayLength" : 5,
                   "bFilter" :false,                   

                 "columns"        : [
                     {data : "menu_name"},
                     {data : "quantity"},
                     {data : "cogs"},
                     {data : "price"},
                     {data : "subtotal"},
                     
                     // {data : "actions"}

                 ],               
                "order"          : [[0, "desc"]],              


                }); //end datatable trans order

            var tableOrder =  $('#table-transaction-sidedish').dataTable({
                   "bProcessing"    : true,
                   "bServerSide"    : true,
                   "sServerMethod"  : "POST",
                   "ajax"           : $('#data_transaction_sidedish').val(),
                   "bDestroy" :true,
                   "autoWidth": false,
                   "iDisplayLength" : 5,
                   "bFilter" :false,                   

                 "columns"        : [
                     {data : "sdname"},
                     {data : "sdprice"},
                     // {data : "actions"}

                 ],               
                "order"          : [[0, "desc"]],              


                }); //end datatable trans sidedish


            var tableMinus =  $('#table-transaction-minus').dataTable({
                   "bProcessing"    : true,
                   "bServerSide"    : true,
                   "sServerMethod"  : "POST",
                   "ajax"           : $('#data_transaction_minus').val(),
                   "bDestroy" :true,
                   "autoWidth": false,
                   "iDisplayLength" : 5,
                   "bFilter" :false,                   

                 "columns"        : [
                     {data : "info"},
                     {data : "amount"},
                  
                 ],               
                "order"          : [[0, "desc"]],              


                }); //end datatable trans order


             var tablePlus =  $('#table-transaction-plus').dataTable({
                   "bProcessing"    : true,
                   "bServerSide"    : true,
                   "sServerMethod"  : "POST",
                   "ajax"           : $('#data_transaction_plus').val(),
                   "bDestroy" :true,
                   "autoWidth": false,
                   "iDisplayLength" : 5,
                   "bFilter" :false,                   

                 "columns"        : [
                     {data : "info"},
                     {data : "amount"},
                  
                 ],               
                "order"          : [[0, "desc"]],              


                }); //end datatable trans order


                var tableMinus =  $('#table-transaction-minus').dataTable({
                     "bProcessing"    : true,
                     "bServerSide"    : true,
                     "sServerMethod"  : "POST",
                     "ajax"           : $('#data_transaction_minus').val(),
                     "bDestroy" :true,
                     "autoWidth": false,
                     "iDisplayLength" : 5,
                     "bFilter" :false,                   

                   "columns"        : [
                       {data : "info"},
                       {data : "amount"},
                    
                   ],               
                  "order"          : [[0, "desc"]],              


                  }); //end datatable trans order


              var tablePayment =  $('#table-transaction-payment').dataTable({
                   "bProcessing"    : true,
                   "bServerSide"    : true,
                   "sServerMethod"  : "POST",
                   "ajax"           : $('#data_transaction_payment').val(),
                   "bDestroy" :true,
                   "autoWidth": false,
                   "iDisplayLength" : 5,
                   "bFilter" :false,                   

                 "columns"        : [
                     {data : "payment_option"},
                     {data : "amount"},
                  
                 ],               
                "order"          : [[0, "desc"]],              


                }); //end datatable trans order


        },

        detailTransaction: function(data){
            var grand_total = 0;
            var total_hpp = 0;
            var total_profit = 0;
            var periode = "";
            var total_quantity = 0;
            var total_quantity_order = 0;
            var total_customer = 0;
            if(data.length > 0){
              var tmp = $(data).get(0);
              total_quantity = parseInt(tmp.sum_total_quantity);
              grand_total += parseInt(tmp.sum_total_price);
              total_hpp += (tmp.sum_total_cogs == null)? 0:  parseInt(tmp.sum_total_cogs);
              total_profit += (tmp.sum_profit == null)? 0:  parseInt(tmp.sum_profit);
              periode = tmp.periode;
              $('#quantity').text(total_quantity);
              $('#periode').text(periode);
              $('#total_price').text("Rp " + App.addCommas(grand_total));
              $('#total_cogs').text("Rp " + App.addCommas(total_hpp));
              $('#total_profit').text("Rp " + App.addCommas(total_profit));
              $('#total_quantity').text(parseInt(tmp.total_quantity_order));
              $('#total_customer').text(tmp.total_customer_count);
              $('#total_transaction').text(tmp.total_transaction);
              $('#total_petty_cash').text("Rp " + App.addCommas(tmp.total_petty_cash));
              $('#total_courier_service').text("Rp " + App.addCommas(tmp.total_courier_service));
              $('#total_discount').text("Rp " + App.addCommas(tmp.total_discount));
              $('#total_dp').text("Rp " + App.addCommas(tmp.total_dp));
              $('#dinein_count').text(App.addCommas(tmp.total_count_dinein));
              $('#dinein_total').text("Rp " + App.addCommas(tmp.total_dinein));
              $('#takeaway_count').text(App.addCommas(tmp.total_count_takeaway));
              $('#takeaway_total').text("Rp " + App.addCommas(tmp.total_takeaway));
              $('#delivery_count').text(App.addCommas(tmp.total_count_delivery));
              $('#delivery_total').text("Rp " + App.addCommas(tmp.total_delivery));
              $('#promo_count').text("Rp " + App.addCommas(tmp.total_count_promo));
              $('#promo_total').text("Rp " + App.addCommas(tmp.total_promo,0));
              $('#voucher_count').text("Rp " + App.addCommas(tmp.total_count_voucher,0));
              $('#voucher_total').text("Rp " + App.addCommas(tmp.total_voucher,0));

            }else{
              $('#periode').text("");
              $('#total_price').text("");
              $('#total_cogs').text("");
              $('#total_profit').text("");
              $('#quantity').text("");
              $('#total_quantity').text("");
              $('#total_customer').text("");
              $('#total_transaction').text("");
              $('#total_petty_cash').text("");
              $('#total_courier_service').text("");
              $('#total_discount').text("");
              $('#total_dp').text("");
              $('#dinein_count').text("Rp " + App.addCommas(""));
              $('#dinein_total').text("Rp " + App.addCommas(""));
              $('#takeaway_count').text("Rp " + App.addCommas(""));
              $('#takeaway_total').text("Rp " + App.addCommas(""));
              $('#delivery_count').text("Rp " + App.addCommas(""));
              $('#delivery_total').text("Rp " + App.addCommas(""));
              $('#promo_count').text("Rp " + App.addCommas(""));
              $('#promo_total').text("Rp " + App.addCommas(""));
              $('#voucher_count').text("Rp " + App.addCommas(""));
              $('#voucher_total').text("Rp " + App.addCommas(""));
            }            
        },

        detailVoid: function(data){
         if(data.length > 0){
            $('#total_amount').text($(data).get(0).total_amount);
            $('#periode').text($(data).get(0).periode);
          }else{
            $('#periode').text("");
            $('#total_amount').text("");

          }
            
        },

        kontraBon: function() {
          $('#filter_submit').on('click', function(e) {
            $('#report_content').html('');
            e.preventDefault();

            var request = $.ajax({
              type     : "POST",
              url      : App.adminUrl + "/reports/get_data_kontra_bon",
              data     : {
                supplier_id   : $('#formFilter #supplier_id').val(),
                start_date    : $('#formFilter #start_date input').val(),
                end_date      : $('#formFilter #end_date input').val()
              }
            });

            request.done(function (msg) {
              $('#report_content').html(msg);
              $('#export_pdf').show();
            });
          });

          $('#export_pdf').unbind('click').bind('click', function (e) {
            $('#formFilter').attr('action', App.adminUrl + "/reports/export_to_pdf");
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
        },
        captureImage:function(element,target){
            svgElements = $(element).find('svg');

            //replace all svgs with a temp canvas
            svgElements.each(function() {
              var canvas, xml;

              // canvg doesn't cope very well with em font sizes so find the calculated size in pixels and replace it in the element.
              $.each($(this).find('[style*=em]'), function(index, el) {
                $(this).css('font-size', getStyle(el, 'font-size'));
              });

              canvas = document.createElement("canvas");
              canvas.className = "screenShotTempCanvas";
              //convert SVG into a XML string
              xml = (new XMLSerializer()).serializeToString(this);

              // Removing the name space as IE throws an error
              xml = xml.replace(/xmlns=\"http:\/\/www\.w3\.org\/2000\/svg\"/, '');

              //draw the SVG onto a canvas
              canvg(canvas, xml);
              $(canvas).insertAfter(this);
              //hide the SVG element
              $(this).attr('class', 'tempHide');
              $(this).hide();
            });
            html2canvas($(element), {
              onrendered: function(canvas) {
                imgData = canvas.toDataURL('image/png');
                App.captureImageData=imgData;
                $(target).val(imgData);
                $(element).find("svg").show();
              }
            });
        },
        memberDiscountDetail:function(){
          $("#filter_submit").on('click', function (e) {
            $("#report_content").html("");
            e.preventDefault();
            var request = $.ajax({
              type    : "POST",
              url     : App.adminUrl + "/reports/get_member_discount_detail",
              data    : {
                start_date: $('#formFilter #input_date input').val(),
                end_date: $('#formFilter #report_end_date input').val(),
              }
            });
            request.done(function (msg) {
              $("#report_content").html(msg);
              $('#export_pdf').show();
            });
          }); 
          $("#export_pdf").unbind('click').bind('click', function (e) {
            $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
          });
        },
        deliveryService:function(){
          $('#start_period').datetimepicker({
            sideBySide: true,
            useCurrent: true,
            format: 'YYYY-MM-DD',
          });

          $('#end_period').datetimepicker({
            sideBySide: true,
            useCurrent: true,
            format: 'YYYY-MM-DD',
          });

          $("#filter_submit").on('click', function (e) {
            $("#report_content").html("");
            e.preventDefault();
            var request = $.ajax({
              type    : "POST",
              url     : App.adminUrl + "/reports/get_data_delivery_service",
              data    : {
                start_date : $('#formFilter #input_start_date').val(),
                end_date : $('#formFilter #input_end_date').val(),
              }
            });
            request.done(function (msg) {
              $("#report_content").html(msg);
              $('#export_pdf').show();
            });
          }); 
          $("#export_pdf").unbind('click').bind('click', function (e) {
            $("#formFilter").attr("action",App.adminUrl + "/reports/export_report_to_pdf");
          });
        }
    }
});