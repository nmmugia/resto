 
define([
    "jquery",
    "jquery-ui", 
    "highcharts"
], function ($, ui) {
    return {
        baseUrl                 : $('#root_base_url').val(),
        serverBaseUrl           : $('#server_base_url').val(),
        storeID                 : $('#store_id_config').val(),
        overlayUI               : $('#cover'),  
        init                   : function () {
           
            App.hrdReport.initEvent();   

            $('#i_date').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'MM-YYYY',
            });
            $('#e_date').datetimepicker({
              sideBySide: true,
              useCurrent: true,
              format: 'MM-YYYY' 
            });

            $('#i2_date').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'YYYY-MM-DD',
            });
            $('#e2_date').datetimepicker({
              sideBySide: true,
              useCurrent: true,
              format: 'YYYY-MM-DD' 
            });

            $('#limit_start_date').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'YYYY-MM-DD',

            });
            $('#limit_end_date').datetimepicker({
              sideBySide: true,
              useCurrent: true,
              format: 'YYYY-MM-DD' 
            });
            
        },
                 
        initEvent:function(){
            console.log("HRD REPORT"); 

            
            App.hrdReport.attendance();
            App.hrdReport.payroll();
            App.hrdReport.appraisal();
            App.hrdReport.employeeSchedule();
        },
       
        attendance:function(){

            var table =  $('#report-attendance').dataTable({
                   "bProcessing"    : true,
                   "bServerSide"    : true,
                   "sServerMethod"  : "POST",
                   "bDestroy" :true,
                   "autoWidth": false,
                   "bFilter" : false, 
                   "iDisplayLength" : 10,
                   "ajax": {
                    "url": $('#dataProcessUrlReportAttendance').val(),
                    "type": 'POST',
                    "data": {
                      param: $('#formFilter').serialize()
                    },
                    "dataSrc": function( json ) {
                     
                      $('#export_pdf').show();
                        
                        return json.data;
                    },   
                      
                 },

                "columns"        : [   
                    {data : "name"}, 
                    {data : "spent_hour"},
                    {data : "late_total"},
                    {data : "overtime_total"},
                    {data : "hadir"},
                    {data : "cuti"},
                    {data : "sakit"},
                    {data : "ijin"}
                ],
             
                "order"          : [[0, "desc"]],
               

                });

            $("#filter_submit_attendance").on('click', function (e) {  
                e.preventDefault();

                var table =  $('#report-attendance').dataTable({
                   "bProcessing"    : true,
                   "bServerSide"    : true,
                   "sServerMethod"  : "POST",
                   "bDestroy" :true,
                   "autoWidth": false,
                   "bFilter" : false, 
                   "iDisplayLength" : 10,
                   "ajax": {
                    "url": $('#dataProcessUrlReportAttendance').val(),
                    "type": 'POST',
                    "data": {
                      param: $('#formFilter').serialize()
                    },
                    "dataSrc": function( json ) {
                     
                      $('#export_pdf').show();
                        
                        return json.data;
                    },   
                      
                 },

                "columns"        : [ 
                    {data : "name"}, 
                    {data : "spent_hour"},
                    {data : "late_total"},
                    {data : "overtime_total"},
                    {data : "hadir"},
                    {data : "cuti"},
                    {data : "sakit"},
                    {data : "ijin"}
                ],
             
                "order"          : [[0, "desc"]],
               

                }); //end datatable
							 
               
             }); //end ajax request
            $("#export_pdf").unbind('click').bind('click', function (e) {
            $("#formFilter").attr("action",App.baseUrl + "admincms/hrd_report/export_report_to_pdf");
          });

              var table =  $('#report-attendance-detail').dataTable({
                   "bProcessing"    : true,
                   "bServerSide"    : true,
                   "sServerMethod"  : "POST",
                   "bDestroy" :true,
                   "autoWidth": false,
                   "bFilter" : false, 
                   "iDisplayLength" : 25,
                   "ajax": {
                    "url": $('#dataProcessUrlReportAttendance').val(),
                    "type": 'POST',
                    "data": {
                      param: $('#formFilter').serialize()
                    },
                    "dataSrc": function( json ) {
                     
                     
                        
                        return json.data;
                    },   
                      
                   },

                  "columns"        : [ 
                      {data : "name"}, 
                      {data : "created_at"},
                      {data : "start_time"},
                      {data : "end_time"},
                      {data : "checkin"},
                      {data : "checkout"},
                      {data : "status"},
                      {data : "note"},
                  ],
                });

						 $("#filter_submit_attendance_detail").on('click', function (e) {  
                e.preventDefault();

                var table =  $('#report-attendance-detail').dataTable({
                   "bProcessing"    : true,
                   "bServerSide"    : true,
                   "sServerMethod"  : "POST",
                   "bDestroy" :true,
                   "autoWidth": false,
                   "bFilter" : false, 
                   "iDisplayLength" : 25,
                   "ajax": {
                    "url": $('#dataProcessUrlReportAttendance').val(),
                    "type": 'POST',
                    "data": {
                      param: $('#formFilter').serialize()
                    },
                    "dataSrc": function( json ) {
                     
                     
                        
                        return json.data;
                    },   
                      
									 },

									"columns"        : [ 
											{data : "name"}, 
                      {data : "created_at"},
                      {data : "start_time"},
                      {data : "end_time"},
                      {data : "checkin"},
                      {data : "checkout"},
                      {data : "status"},
                      {data : "note"},
									],
                }); //end datatable
							 
            });
             $("#export_pdf").unbind('click').bind('click', function (e) {
            $("#formFilter").attr("action",App.baseUrl + "admincms/hrd_report/export_report_to_pdf");
          });
            // $("#export_pdf").unbind('click').bind('click', function (e) {
            //     e.preventDefault();
            //     $.ajax({
            //       url      : "export_report_to_pdf",
            //       type     : "POST",
            //       dataType : "json",
            //       data     : {
            //         type      : 'attendance_detail',
            //         start_date: $('#input_start_date').val(),
            //         end_date: $('#input_end_date').val(),
            //         user_id: $('#user_id').val(),

            //       },
            //       success  : function (result) {
            //         console.log(App.baseUrl + result);
            //         if (result != '') {
            //                 window.open(App.baseUrl + result, '_newtab')
            //               }
            //               else
            //                 alert('Export report gagal');
            //             }
            //           });
            //  }); //end ajax request

						$("#filter_submit_attendance_periode").on('click', function (e) {
							$("#report_content").html("");
							e.preventDefault();
							var request = $.ajax({
								type    : "POST",
								url     : App.baseUrl + "admincms/hrd_report/get_report_attendance_periode",
								data    : {
									start_date   : $('#formFilter #input_start_date').val(),
									end_date   : $('#formFilter #input_end_date').val(),
								}
							});
							request.done(function (msg) {
								$("#report_content").html(msg);
								
							});
						}); 
						$("#export_schedule_pdf").unbind('click').bind('click', function (e) {
							$("#formFilter").attr("action",App.baseUrl + "admincms/hrd_report/export_report_to_pdf");
						});


            $("#filter_submit_attendance_overdue").on('click', function (e) {
              $("#report_content").html("");
              e.preventDefault();
              var request = $.ajax({
                type    : "POST",
                url     : App.baseUrl + "admincms/hrd_report/get_report_attendance_overdue",
                data    : {
                  start_date   : $('#formFilter #input_start_date').val(),
                  end_date   : $('#formFilter #input_end_date').val(),
                }
              });
              request.done(function (msg) {
                $("#report_content").html(msg);
                $('#export_overdue_pdf').show();
              });
            }); 
            $("#export_overdue_pdf").unbind('click').bind('click', function (e) {
              $("#formFilter").attr("action",App.baseUrl + "admincms/hrd_report/export_report_to_pdf");
            });
        },
        payroll:function(){
          console.log("aaaaaa");
              console.log($('#dataProcessUrlReportPayroll').val());
              console.log("aaaaaa")
            var table =  $('#report-payroll').dataTable({
                   "bProcessing"    : true,
                   "bServerSide"    : true,
                   "sServerMethod"  : "POST",
                   "bDestroy" :true,
                   "autoWidth": false,
                   "bFilter" : false, 
                   "iDisplayLength" : 10,
                   "ajax": {
                    "url": $('#dataProcessUrlReportPayroll').val(),
                    "type": 'POST',
                    "data": {
                      param: $('#formFilter').serialize()
                    },
                    "dataSrc": function( json ) {
                     
                      $('#export_pdf').show();
                        
                        return json.data;
                    },   
                }, 
                "columns"        : [ 
                    {data : "pname"},
                    {data : "jobs_name"},
                    
                    {data : "months"},
                    {data : "years"},
                    {data : "total_penerimaan"},  
                    {data : "total_potongan"},
                    {data : "total"}
                ],
             
                "order"          : [[0, "desc"]],
               

                });
            $("#filter_submit_payroll").on('click', function (e) {  
              console.log($('#dataProcessUrlReportPayroll').val());
                e.preventDefault(); 
                var table =  $('#report-payroll').dataTable({
                   "bProcessing"    : true,
                   "bServerSide"    : true,
                   "sServerMethod"  : "POST",
                   "bDestroy" :true,
                   "autoWidth": false,
                   "bFilter" : false, 
                   "iDisplayLength" : 10,
                   "ajax": {
                    "url": $('#dataProcessUrlReportPayroll').val(),
                    "type": 'POST',
                    "data": {
                      param: $('#formFilter').serialize()
                    },
                    "dataSrc": function( json ) {
                     
                     
                        
                        return json.data;
                    },   
                }, 
                "columns"        : [ 
                    {data : "pname"},
                    {data : "jobs_name"},
                    
                    {data : "months"},
                    {data : "years"},
                    {data : "total_penerimaan"},  
                    {data : "total_potongan"},
                    {data : "total"}
                ],
             
                "order"          : [[0, "desc"]],
               

                }); //end datatable

               

            

            });
            $("#export_overdue_pdf").unbind('click').bind('click', function (e) {
              $("#formFilter").attr("action",App.baseUrl + "admincms/hrd_report/export_report_to_pdf");
            });
            // $("#export_pdf").unbind('click').bind('click', function (e) {
            //     e.preventDefault();
            //     $.ajax({
            //       url      : "export_report_to_pdf",
            //       type     : "POST",
            //       dataType : "json",
            //       data     : {
            //         type      : 'payroll',
            //         start_period: $('#input_start_period').val(),
            //         end_period: $('#input_end_period').val(),
            //         user_id: $('#user_id').val(),

            //       },
            //       success  : function (result) {
            //         console.log(App.baseUrl + result);
            //         if (result != '') {
            //                 window.open(App.baseUrl + result, '_newtab')
            //               }
            //               else
            //                 alert('Export report gagal');
            //             }
            //           });
            //  }); //end ajax request




        },
        appraisal:function(){
                var table =  $('#report-appraisal').dataTable({
                   "bProcessing"    : true,
                   "bServerSide"    : true,
                   "sServerMethod"  : "POST",
                   "bDestroy" :true,

                "scrollX": true,
                   "autoWidth": false,
                   "bFilter" : false, 
                   "iDisplayLength" : 10,
                   "ajax": {
                        "url": $('#dataProcessUrlReportAppraisal').val(),
                        "type": 'POST',
                        "data": { param: $('#formFilter').serialize() },
                        "dataSrc": function( json ) {
                            
                            return json.data;
                        },   
                    }, 
                    "columns"        : [ 
                     {data : "name"}, 
                     {data : "period"},
                     {data : "created_at"},
                     {data : "total_nilai"},

                     {data : "max_nilai"}
                    ],
                    "order"          : [[0, "desc"]], 
                });

            $("#filter_submit_appraisal").on('click', function (e) {  
                e.preventDefault(); 
                var table =  $('#report-appraisal').dataTable({
                   "bProcessing"    : true,
                   "bServerSide"    : true,
                   "sServerMethod"  : "POST",
                   "bDestroy" :true,

                "scrollX": true,
                   "autoWidth": false,
                   "bFilter" : false, 
                   "iDisplayLength" : 10,
                   "ajax": {
                        "url": $('#dataProcessUrlReportAppraisal').val(),
                        "type": 'POST',
                        "data": { param: $('#formFilter').serialize() },
                        "dataSrc": function( json ) {
                            
                            return json.data;
                        },   
                    }, 
                    "columns"        : [ 
                     {data : "name"}, 
                     {data : "period"},
                     {data : "created_at"},
                     {data : "total_nilai"},

                     {data : "max_nilai"}
                    ],
                    "order"          : [[0, "desc"]], 
                }); //end datatable

                
             }); //end ajax request
              $("#export_pdf").unbind('click').bind('click', function (e) {
              $("#formFilter").attr("action",App.baseUrl + "admincms/hrd_report/export_report_to_pdf");
            });
            // $("#export_pdf").unbind('click').bind('click', function (e) {
            //         e.preventDefault();
            //         $.ajax({
            //           url      : "export_report_to_pdf",
            //           type     : "POST",
            //           dataType : "json",
            //           data     : {
            //             type      : 'appraisal',
            //             start_period: $('#start_period').val(), 
            //             user_id: $('#user_id').val(),

            //           },
            //           success  : function (result) {
            //                 console.log(App.baseUrl + result);
            //                 if (result != '') {
            //                     window.open(App.baseUrl + result, '_newtab')
            //                   }
            //                   else{
            //                     alert('Export report gagal');
            //                 }
            //           }
            //         });
            //     }); //end ajax request
        },
        employeeSchedule:function(){
          
          $("#filter_submit").on('click', function (e) {
            $("#report_content").html("");
            e.preventDefault();
            var request = $.ajax({
              type    : "POST",
              url     : App.baseUrl + "admincms/hrd_report/get_report_schedule",
              data    : {
                status   : $('#formFilter #status').val(),
                start_date   : $('#formFilter #input_start_date').val(),
                end_date   : $('#formFilter #input_end_date').val(),
                user_id   : $('#formFilter #user_id').val(),
              }
            });
            request.done(function (msg) {
              $("#report_content").html(msg);
              $('#export_pdf').show();
            });
          }); 
          $("#export_pdf").unbind('click').bind('click', function (e) {
            $("#formFilter").attr("action",App.baseUrl + "admincms/hrd_report/export_report_to_pdf");
          });
        }
    }
});