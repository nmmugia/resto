 
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
           
            App.hrdDashboard.initEvent();   
            
        },
                 
        initEvent:function(){
            console.log("DASHBOARD"); 

            App.hrdDashboard.loadStatisticAttendance();
        },
        loadStatisticAttendance:function(){
            if( $('#attendance-statistic').length <= 0) return;
            
            var url = App.baseUrl+"admincms/hrd_attendance/get_attendance_statistic_bymonth";
            var request = $.ajax({
                type    : 'GET',
                url     : url
            });
            request.done(function (msg) {  
                var parsedObject = JSON.parse(msg);
                console.log(parsedObject);
                $('#attendance-statistic').highcharts({
                    chart   : {
                        type : 'column'
                    },
                    credits : {
                        enabled : false
                    },
                    title   : {
                        text : 'Statistik Absensi'
                    },
                    xAxis   : {
                        type   : 'category',
                        labels : {
                            rotation : -45,
                            style    : {
                                fontSize   : '13px',
                                fontFamily : 'Verdana, sans-serif'
                            }
                        },
                        categories: [
                            'Jan', 
                            'Feb', 
                            'Mar', 
                            'Apr', 
                            'May', 
                            'Jun', 
                            'Jul', 
                            'Aug', 
                            'Sep', 
                            'Oct', 
                            'Nov', 
                            'Dec'
                        ]
                    },
                    yAxis   : {
                        title : {
                            text : 'Jumlah'
                        }
                    },
                   legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle',
                        borderWidth: 0
                    },
                     series:parsedObject
                }); 
            });
            request.fail(function (jqXHR, textStatus) { 
                App.alert("Data Kosong"); 
            });
            request.always(function () {
            }); 
        }
       

    }
});