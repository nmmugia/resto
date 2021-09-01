/**
 * Created by alta falconeri on 12/15/2014.
 */

define([
    "jquery",
    "jquery-ui", 
    "app/app-hrd-appraisal",
    "app/app-hrd-recruitment",
    "app/app-hrd-report",
    "chained",
    "metisMenu",
    "Morris",
    'datatables',
    "bootstrap",
    "datatables-bootstrap",
    "timepicker",
    "datepair",
    "datetimepicker",
    "highcharts",
    "libs/bootstrap3-typeahead",
    "select2",
    "multiselect"
], function ($, ui,Appraisal,recruitment,hrdReport) {
    return {
        baseUrl                 : $('#root_base_url').val(),
        serverBaseUrl           : $('#server_base_url').val(),
        storeID                 : $('#store_id_config').val(),
        overlayUI               : $('#cover'), 
        Appraisal            : Appraisal,
        recruitment            : recruitment,
        hrdReport            : hrdReport,
        enhancerCount           : document.getElementById("count_enhancer") != null ? $("#count_enhancer").val(): 0, 
        standardSchedRepeatStat           : document.getElementById("repeat-status") != null ? $("#repeat-status").val(): 0,
        subtrahendCount         :  document.getElementById("count_subtrahend") != null ? $("#count_subtrahend").val(): 0,
        dataEnhancerSalaryComponent    : document.getElementById("data_enhancer_salary_component") != null ? JSON.parse($("#data_enhancer_salary_component").val()) : 0,
        dataSubstrahendSalaryComponent    : document.getElementById("data_substrahend_salary_component") != null ? JSON.parse($("#data_substrahend_salary_component").val()) : 0,
        init                   : function () {
            App.overlayUI.hide();
            
            App.initFunc(App);

            App.initEvent();   
            App.Appraisal.init();
            App.recruitment.init();
            App.hrdReport.init();

            $(App.overlayUI).on("click",function(){
                App.overlayUI.hide();
            });
             $(".modal").on("click",function(){
                App.overlayUI.hide();
            });
        },
                 
        initEvent:function(){
            console.log("EVENT");
            $('.multiselect').multiselect({
                keepRenderingSort: true
            });
            $("select.select2").select2();
            $('#side-menu').metisMenu();
            $('#period-generate-date').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'MM-YYYY' 
            });

            App.fingerprint();

            App.validationInput();

            App.employeeAffairEvent();
            App.memoRandumEvent();
            App.salaryComponentEvent();
            App.officeHourseEvent();
            App.jobsEvent(); 
            App.setSalaryComponentEVent();
            App.employeeEvent();

            App.jobsHistoryEvent();
            App.payrollHistoryEvent();

            App.scheduleStaffList();

            App.holidaysHistory();
            App.scheduleStandardEvent();
            App.setComplimentUserEvent();

            App.reimburseEvent();

            App.attendanceEvent();
            App.shiftEvent();
            App.moveshiftEvent();
            App.performanceEvent();
            App.loanEvent();
            App.overtimeEvent();
            App.kelolajadwal();

            App.employeesHoliday();
            App.templateAuditEvent(); 

            App.processAuditEvent(); 
            App.settingEvent();

            App.syncToMachine();
            App.initListScheduleEmployee();
            App.rollingShift();
            App.setHolidays();
            App.exchangeShift();
            App.settingRollingOfficeHour();


            $('#start_time').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'H:mm'
                
            });
            $('#end_time').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'H:mm'
            });

            $('#start_date').datetimepicker({
                sideBySide: true,
                useCurrent: false,
                format: 'YYYY-MM-DD'
                
            });
            $('#end_date').datetimepicker({
                sideBySide: true,
                useCurrent: false,
                format: 'YYYY-MM-DD'
            });

            $("#start_date").on("dp.change", function (e) {
            $('#end_date').data("DateTimePicker").minDate(e.date);
            });

            $("#end_date").on("dp.change", function (e) { 
            $('#start_date').data("DateTimePicker").minDate(e.date);
            });

            $('#start_date1').datetimepicker({
                sideBySide: true,
                useCurrent: false,
                format: 'YYYY-MM-DD'
                
            });
            $('#end_date1').datetimepicker({
                sideBySide: true,
                useCurrent: false,
                format: 'YYYY-MM-DD'
            });

            $("#start_date1").on("dp.change", function (e) {
            $('#end_date1').data("DateTimePicker").minDate(e.date);
            });

            $("#end_date1").on("dp.change", function (e) { 
            $('#start_date1').data("DateTimePicker").minDate(e.date);
            });

        },
        initListScheduleEmployee:function(){
          var options = {
            valueNames: [ 'name' ],
            searchClass:'search'
          };
          new List('employees', options);
        },
        settingEvent:function(){
            $('#cron_job_time').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'HH:mm',
                  widgetPositioning:{
                    "vertical":"bottom"
                }  
            }); 

            $('#dataTables-hrd-sync').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrlSync').val(),
                "iDisplayLength" : 10,
                "columns"        : [ 
                    {data : "controller"},
                    {data : "start_time"},
                    {data : "end_time"},
                    {data : "interval"},
                    {data : "actions"}


                ],
                "columnDefs"     : [
                    {
                        "targets"     : 2,
                        "orderable"   : false,
                        "bSearchable" : false,
                        "class"       : 'center-tr'
                    }
                ],
                "order"          : [[0, "desc"]]

            });
        },
        downloadLogData:function(urlx){
            App.overlayUI.show();
            var request = $.ajax({
                type : 'POST', 
                url  :App.baseUrl + 'index.php/hrd_scheduler/'+urlx
            });
            request.done(function (msg) {
                App.alert("Download Data Fingerprint Berhasil!",function(){

                                App.overlayUI.hide();

                });
            });
            request.fail(function (jqXHR, textStatus) {
                App.alert("Gagal Mendownload Data Fingerprint!",function(){

                                App.overlayUI.hide();

                });          
            });
            request.always(function () {
            });
        },
        syncToMachine:function(){
            
            $("#upload-to-machine").on("click",function(){
                 App.overlayUI.show();
                var url = App.baseUrl+"admincms/hrd_staff/upload_staff_to_machine";
                var request = $.ajax({
                    type    : 'POST',
                    url     : url
                });
                request.done(function (msg) {  
                     App.alert("Data Sudah Tersinkronisasi",function(){
                        App.overlayUI.hide();
                     });
                });
                request.fail(function (jqXHR, textStatus) { 
                    App.alert("Maaf, Status History Gagal Di Hapus. Silahkan Hubungi Administrator");
                });
                request.always(function () {
                }); 
            });


             $("#download-from-machine").on("click",function(){
                 App.overlayUI.show();
                var url = App.baseUrl+"admincms/hrd_staff/download_finger_template";
                var request = $.ajax({
                    type    : 'POST',
                    url     : url
                });
                request.done(function (msg) {  
                     App.alert("Download Data Fingerprint berhasil",function(){
                        App.overlayUI.hide();
                     });
                });
                request.fail(function (jqXHR, textStatus) { 
                    App.alert("Maaf, Status History Gagal Di Hapus. Silahkan Hubungi Administrator");
                });
                request.always(function () {
                }); 
            });
        },
        employeeAffairEvent:function(){
            var tableEmployeeAffair =  $('#dataTables-employee-affair').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrl').val(),
                "iDisplayLength" : 10,
                "columns"        : [ 
                    {data : "name"},
                    {data : "during"},
                    ],
                "order"          : [[0, "asc"]] 
            });

            tableEmployeeAffair.on( 'draw.dt', function () {
                $(".delete-employee-affair").on("click",function(){
                    var employeeAffairId = $(this).attr('employee-affair-id');
                    App.confirm("Anda yakin ingin menghapus Status Kepegawaian? ",function(employeeAffairId){  
                        var data = {
                            emp_affair_id : employeeAffairId
                        } 
                        var url = "delete_employee_affair";
                        var request = $.ajax({
                            type    : 'POST',
                            url     : url, 
                            data    : data
                        });
                        request.done(function (msg) {  
                            var parsedObject = JSON.parse(msg);
                            if(parsedObject.status){
                                  tableEmployeeAffair.api().ajax.reload();    
                            }else{
                                App.alert(parsedObject.message);
                              
                            }
                            
                        });
                        request.fail(function (jqXHR, textStatus) { 
                            App.alert("Maaf, Status Kepegawaian Gagal Di Hapus. Silahkan Hubungi Administrator");
                        });
                        request.always(function () {
                        }); 
                    },employeeAffairId);
                });


                $(".edit-employee-affair").on("click",function(){  
                    var employeeAffairId = $(this).attr('employee-affair-id');  
                    $("#save-employee-affair").attr("data-action","edit");  
                    var request = $.ajax({
                        type    : 'POST',
                        url     : 'get_one_employee_affair', 
                        data    : { 
                            emp_affair_id : employeeAffairId
                        }
                    });
                    request.done(function (msg) { 
                         var parsedObject = JSON.parse(msg);
                         if(parsedObject.status){
                            $("#employee-affair-name").val(parsedObject.data.name);
                            $("#during").val(parsedObject.data.during); 
                            $("#next_job").html(App.addOptionElement(parsedObject.next_job,parsedObject.data.next_job));

                            $("#employee-affair-id").val(parsedObject.data.id);

                            $(".modal-title").html("Ubah Status Kepegawaian"); 
                            $("#save-employee-affair").attr("data-action","edit"); 
                            $('#employee-affair-modal').modal('show');
                         }
                    });
                    request.fail(function (jqXHR, textStatus) { 
                        App.alert("Maaf, Gagal Mengambil data status Kepegawaian. Silahkan Hubungi Administrator");
                    });
                    request.always(function () {
                    }); 
                });
            }); 

            $("#save-employee-affair").on("click",function(){ 
                var data = {
                    emp_affair_name : $("#employee-affair-name").val(),
                    during : $("#during").val(),
                    next_job :  $("#next_job").val()
                } 
                var url = "save_employee_affair"; 
                 var employeeAffairName,during = "";
                 var errorList = "";


                if($(this).attr("data-action") == "edit"){
                    data = {
                            emp_affair_name : $("#employee-affair-name").val(),
                            emp_affair_id :  $("#employee-affair-id").val(),
                            next_job :  $("#next_job").val(),
                            during : $("#during").val()
                        };
                    url = "update_employee_affair";
                }

                 employeeAffairName = $("#employee-affair-name");
                 during = $("#during"); 
                 if(employeeAffairName.val().length === 0){
                    employeeAffairName.parent().addClass("has-error");  
                    errorList += "Nama Status Kepegawaian Harus Diisi <br>";
                 }
                 else if(during.val().length === 0){
                    during.parent().addClass("has-error");  
                    errorList += "Lama Kerja Harus Diisi <br>";
                 }
                 else{ 
                    var request = $.ajax({
                        type    : 'POST',
                        url     : url, 
                        data    : data
                    });
                    request.done(function (msg) { 
                        console.log(msg);
                         var parsedObject = JSON.parse(msg);
                         if(parsedObject.status){ 
                            //reset form
                            $("#employee-affair-name").val("");
                            $("#employee-affair-name").parent().removeClass("has-error"); 
                            $("#during").val("");
                            $("#during").parent().removeClass("has-error"); 

                            errorList = "";
                            $(".error-message").html(errorList);

                            tableEmployeeAffair.api().ajax.reload();

                            $('#employee-affair-modal').modal('hide');
                         }else{
                            App.alert(parsedObject.message);
                         } 
                    });
                    request.fail(function (jqXHR, textStatus) { 
                        App.alert("Maaf, Status Kepegawaian Gagal Di simpan. Silahkan Hubungi Administrator");
                    });
                    request.always(function () {
                    }); 
                 } 
                 $(".error-message").html(errorList);
            });  

            $('#employee-affair-modal').on('hidden.bs.modal', function (e) {
                $("#employee-affair-name").val("");
                $("#employee-affair-id").val("");
                $("#employee-affair-name").parent().removeClass("has-error");
                $("#save-employee-affair").attr("data-action",""); 
                $(".modal-title").html("Tambah Status Kepegawaian"); 
                $(".error-message").html(" ");
                $("#next_job").html("");
            });

            $('#employee-affair-modal').on('shown.bs.modal', function (e) {
                var emp_affair_id =  $("#employee-affair-id").val(); 
                if(emp_affair_id.length == 0){
                    var request = $.ajax({
                        type    : 'POST',
                        url     : 'get_all_employee_affair'
                    });
                    request.done(function (msg) { 
                         var parsedObject = JSON.parse(msg);
                         if(parsedObject.status){ 
                            $("#next_job").html(App.addOptionElement(parsedObject.next_job,false)); 
                         }
                    });
                    request.fail(function (jqXHR, textStatus) { 
                        App.alert("Maaf, Gagal Mengambil data status Kepegawaian. Silahkan Hubungi Administrator");
                    });
                    request.always(function () {
                    });     
                }
                
            });
        },
        addOptionElement:function(data,current_selected){
            var html = "";
                console.log(data);
            for (var i = 0; i < data.length; i++) {
                if(current_selected == data[i].id){
                    html += "<option value="+data[i].id+" selected>"+data[i].name+"</option>";    
                }else{
                    html += "<option value="+data[i].id+">"+data[i].name+"</option>";
                }
                
            } 
             if(current_selected && current_selected == 0){
             html += "<option value='0' selected>Selamanya</option>";
            }else{
                html += "<option value='0' >Selamanya</option>";
            }
            return html;
        },
        memoRandumEvent:function(){
            var tableMemorandum =  $('#dataTables-memorandum').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrl').val(),
                "iDisplayLength" : 10,
                "columns"        : [ 
                    {data : "name"},
                    {data : "period"},
                    {data : "actions"}
                    ],
                "columnDefs"     : [
                    {
                        "targets"     : [1,2],
                        "orderable"   : false,
                        "bSearchable" : false,
                        "class"       : 'center-tr'
                    }
                ],
                "order"          : [[0, "asc"]] 
            });

            tableMemorandum.on( 'draw.dt', function () {
                $(".delete-memorandum").on("click",function(){
                    var memorandumId = $(this).attr('memorandum-id');
                    App.confirm("Anda yakin ingin menghapus Surat Peringatan? ",function(memorandumId){  
                        var data = {
                            memorandum_id : memorandumId
                        } 
                        var url = "delete_memorandum";
                        var request = $.ajax({
                            type    : 'POST',
                            url     : url, 
                            data    : data
                        });
                        request.done(function (msg) {  
                            tableMemorandum.api().ajax.reload();
                        });
                        request.fail(function (jqXHR, textStatus) { 
                            App.alert("Maaf, Surat Peringatan Gagal Di Hapus. Silahkan Hubungi Administrator");
                        });
                        request.always(function () {
                        }); 
                    },memorandumId);
                });


                $(".edit-memorandum").on("click",function(){ 
                    var memorandumId = $(this).attr('memorandum-id');  
                    $("#save-memorandum").attr("data-action","edit");  
                    var request = $.ajax({
                        type    : 'POST',
                        url     : 'get_one_memorandum', 
                        data    : { 
                            memorandum_id : memorandumId
                        }
                    });
                    request.done(function (msg) { 
                         var parsedObject = JSON.parse(msg);
                         if(parsedObject.status){
                            $("#memorandum-name").val(parsedObject.data.name);
                            $("#memorandum-id").val(parsedObject.data.id);
                            $("#memorandum-period").val(parsedObject.data.period);

                            $(".modal-title").html("Ubah Surat Peringatan"); 
                            $("#save-memorandum").attr("data-action","edit"); 
                            $('#memorandum-modal').modal('show');
                         }
                    });
                    request.fail(function (jqXHR, textStatus) { 
                        App.alert("Maaf, Gagal Mengambil data Surat Peringatan. Silahkan Hubungi Administrator");
                    });
                    request.always(function () {
                    }); 
                });
            }); 

            $("#save-memorandum").on("click",function(){ 
                var data = {
                    memorandum_name : $("#memorandum-name").val(),
                    memorandum_period : $("#memorandum-period").val()
                } 
                var url = "save_memorandum";


                 var memorandumPeriod,memorandumName = "";
                 var errorList = "";


                if($(this).attr("data-action") == "edit"){
                    data = {
                            memorandum_name : $("#memorandum-name").val(),
                            memorandum_period : $("#memorandum-period").val(),
                            memorandum_id :  $("#memorandum-id").val()
                        };
                        console.log(data);
                    url = "update_memorandum";
                }

                 memorandumName = $("#memorandum-name"); 
                 memorandumPeriod = $("#memorandum-period");

                 if(memorandumName.val().length === 0){
                    memorandumName.parent().addClass("has-error");  
                    errorList += "Nama Surat Peringatan Tidak Boleh Kosong <br>";
                 }else{
                    memorandumName.parent().removeClass("has-error");
                 }

                 if (memorandumPeriod.val().length === 0){
                    memorandumPeriod.parent().addClass("has-error");  
                    errorList += "Period Surat Peringatan Tidak Boleh Kosong <br>";
                 }else if(!App.isInteger(memorandumPeriod.val())){
                    memorandumPeriod.parent().addClass("has-error");  
                    errorList += "Period Surat Peringatan Harus Diisi  Angka<br>";
                 }else{
                    memorandumPeriod.parent().removeClass("has-error");
                 }

                 if(errorList.length == 0){ 
                    var request = $.ajax({
                        type    : 'POST',
                        url     : url, 
                        data    : data
                    });
                    request.done(function (msg) { 
                        console.log(msg);
                         var parsedObject = JSON.parse(msg);
                         if(parsedObject.status){ 
                            //reset form
                            $("#memorandum-name").val("");
                            $("#memorandum-period").val("");
                            $("#memorandum-name").parent().removeClass("has-error"); 
                            $("#memorandum-period").parent().removeClass("has-error"); 
                            errorList = "";
                            $(".error-message").html(errorList);

                            tableMemorandum.api().ajax.reload();

                            $('#memorandum-modal').modal('hide');
                         }else{
                            App.alert(parsedObject.message);
                         } 
                    });
                    request.fail(function (jqXHR, textStatus) { 
                        App.alert("Maaf, Status Kepegawaian Gagal Di simpan. Silahkan Hubungi Administrator");
                    });
                    request.always(function () {
                    }); 
                 } 
                 $(".error-message").html(errorList);
            }); 
        },
        salaryComponentEvent:function(){  
            config={
              "bProcessing"    : true,
              "bServerSide"    : true,
              "sServerMethod"  : "POST",
              "responsive"     :true,
              "autoWidth"     :false,
              "ajax"           : $('#dataProcessUrl').val(),
              "iDisplayLength" : 10,
              "columns"        : [ 
                {data : "name"},
                {data : "is_enhancer"},
                {data : "key"},
                {data : "formula_default"}, 
              ],
              "order"          : [[0, "asc"]] 
            };
            var tableSalaryComponent =  $('#dataTables-salary-component').dataTable(config);
            $(window).on({
              resize         : function (e) {
                if(tableSalaryComponent.fnClearTable() && tableSalaryComponent.fnDestroy()){
                  tableSalaryComponent = $('#dataTables-salary-component').dataTable(config);
                }
              }
            });
            tableSalaryComponent.on( 'draw.dt', function () {
                $(".delete-salary-component").on("click",function(){
                    var salaryCompId = $(this).attr('salary-component-id');
                    App.confirm("Anda yakin ingin menghapus Surat Peringatan? ",function(salaryCompId){  
                        var data = {
                            salary_component_id : salaryCompId
                        } 
                        var url = "delete_salary_component";
                        var request = $.ajax({
                            type    : 'POST',
                            url     : url, 
                            data    : data
                        });
                        request.done(function (msg) {  
                            tableSalaryComponent.api().ajax.reload();
                        });
                        request.fail(function (jqXHR, textStatus) { 
                            App.alert("Maaf, Komponen Gaji Gagal Di Hapus. Silahkan Hubungi Administrator");
                        });
                        request.always(function () {
                        }); 
                    },salaryCompId);
                });
            });
        },
        officeHourseEvent:function(){  
            var dataTableID =  $('#dataTables-office-hours').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrl').val(),
                "iDisplayLength" : 10,
                "columns"        : [ 
                    {data : "name"},
                    {data : "checkin_time"},
                    {data : "checkout_time"}, 
                    {data : "actions"}
                    ],
                "columnDefs"     : [
                    {
                        "targets"     : 3,
                        "orderable"   : false,
                        "bSearchable" : false,
                        "class"       : 'center-tr'
                    }
                ],
                "order"          : [[0, "asc"]] 
            });


            dataTableID.on( 'draw.dt', function () {
                $(".delete-office-hours").on("click",function(){
                    var id = $(this).attr('office-hours-id');
                    App.confirm("Anda yakin ingin menghapus Template Jam Kerja? ",function(id){  
                        var data = {
                            id : id
                        } 
                        var url = "delete_office_hours";
                        var request = $.ajax({
                            type    : 'POST',
                            url     : url, 
                            data    : data
                        });
                        request.done(function (msg) { 
                            var parsedObject = JSON.parse(msg);
                            if(parsedObject.status){
                             dataTableID.api().ajax.reload();
                            }else{
                                App.alert(parsedObject.message);
                            }
                        });
                        request.fail(function (jqXHR, textStatus) { 
                            App.alert("Maaf, Template Jam Kerja Gagal Di Hapus. Silahkan Hubungi Administrator");
                        });
                        request.always(function () {
                        }); 
                    },id);
                });
            });
        },
        jobsEvent:function(){   
            $('#checkin_time').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'HH:mm',
            });

            $('#checkout_time').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'HH:mm'
            });

            var dataTableID =  $('#dataTables-jobs-list').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrl').val(),
                "iDisplayLength" : 10,
                "columns"        : [ 
                    {data : "jobs_name"},
                    {data : "note"},
                    {data : "salary_component"}, 
                    ],
                "columnDefs"     : [
                    {
                        "targets"     : [2],
                        "orderable"   : false,
                        "bSearchable" : false,
                        "class"       : 'center-tr'
                    }
                ],
                "order"          : [[1, "asc"]] 
            });


            dataTableID.on( 'draw.dt', function () {
                $(".delete-jobs").on("click",function(){
                    var id = $(this).attr('jobs-id');
                    App.confirm("Anda yakin ingin menghapus Master Jabatan? ",function(id){  
                        var data = {
                            id : id
                        } 
                        var url = "delete_jobs";
                        var request = $.ajax({
                            type    : 'POST',
                            url     : url, 
                            data    : data
                        });
                        request.done(function (msg) { 
                            var parsedObject = JSON.parse(msg);
                            if(parsedObject.status){
                                dataTableID.api().ajax.reload();
                            }else{
                                App.alert(parsedObject.message);
                            }
                        });
                        request.fail(function (jqXHR, textStatus) { 
                            App.alert("Maaf, Master Jabatan Gagal Di Hapus. Silahkan Hubungi Administrator");
                        });
                        request.always(function () {
                        }); 
                    },id);
                });
            }); 
        },
        isString:function(o){
            return typeof o == "string" || (typeof o == "object" && o.constructor === String);
        },
        isInteger:function(o){
            var regInteger = /^\d+$/;
            return regInteger.test( o );
        },
        setSalaryComponentEVent:function(){
            $('#add_enhancer').on('click', function (e) {
                e.preventDefault(); 
                App.enhancerCount++;
                App.add_enhancer(App.enhancerCount); 
            });


            $('.remove_enhancer').on('click', function (e) {
                var str = $(this).attr('id').split('_');

                function removeIngredient() { 
                    $('#enhancer-' + str[2]).remove();
                }

                App.confirm('Anda yakin ingin menghapus?', removeIngredient);
            });




            $('#add_subtrahend').on('click', function (e) {
                e.preventDefault(); 
                App.subtrahendCount++;
                App.add_subtrahend(App.subtrahendCount); 
            });


            $('.remove_subtrahend').on('click', function (e) {
                var str = $(this).attr('id').split('_');

                function removeIngredient() { 
                    $('#subtrahend-' + str[2]).remove();
                }

                App.confirm('Anda yakin ingin menghapus?', removeIngredient);
            });
        },
        add_enhancer: function (count) { 
            var appedendVal = '' +
                    '<tr id="enhancer-' + count + '" class="countIngredient"><td>' +
                    '<div class="row">' + 
                    '<div class="col-md-5">' + App.createIngredientOption(count,App.dataEnhancerSalaryComponent,'enhancer') + '</div>' +
                    '<div class="col-md-3"><input type="text" class="form-control NumericDecimal" field-name = "jumlah" placeholder="Jumlah" name="enhancer[' + count + '][quantity]" value="0"/></div>' +
                    '<div class="col-md-1"><button id="remove_enhancer_' + count + '" type="button" class="remove_enhancer btn btn-mini btn-danger pull-right"><i class="fa fa-trash-o"></i></button></div>' +
                    '</div>' +
                    '</td></tr>'; 

            $('#enhancer_container').append(appedendVal); 

            $('.remove_enhancer').on('click', function (e) {
                var str = $(this).attr('id').split('_');

                function removeIngredient() { 
                    $('#enhancer-' + str[2]).remove();
                }

                App.confirm('Anda yakin ingin menghapus?', removeIngredient);
            });
             App.validationInput();
        }, 
        createIngredientOption:function(id,data,field){
            var appendOpt = '' +
                '<select id="ingredient_id_chained_' + id + '" name="'+field+'[' + id + '][component_id]" field-name = "Bahan" class="form-control requiredDropdown ingredient_id_chained" autocomplete="off">';
        
            for (var i = 0; i < data.length; i++) { 
               appendOpt += '<option value="' + data[i].id + '">' + data[i].name + '</option>' ;
            };
            appendOpt += '</select>';

           
            return appendOpt;
        }, 
        add_subtrahend: function (count) { 
            var appedendVal = '' +
                    '<tr id="subtrahend-' + count + '" class="countIngredient"><td>' +
                    '<div class="row">' + 
                    '<div class="col-md-5">' + App.createIngredientOption(count,App.dataSubstrahendSalaryComponent,'subtrahend') + '</div>' +
                    '<div class="col-md-3"><input type="text" class="form-control NumericDecimal" field-name = "jumlah" placeholder="Jumlah" name="subtrahend[' + count + '][quantity]"  value="0"/></div>' +
                    '<div class="col-md-1"><button id="remove_subtrahend_' + count + '" type="button" class="remove_subtrahend btn btn-mini btn-danger pull-right"><i class="fa fa-trash-o"></i></button></div>' +
                    '</div>' +
                    '</td></tr>'; 

            $('#subtrahend_container').append(appedendVal); 

            $('.remove_subtrahend').on('click', function (e) {
                var str = $(this).attr('id').split('_');

                function removeIngredient() { 
                    $('#subtrahend-' + str[2]).remove();
                }

                App.confirm('Anda yakin ingin menghapus?', removeIngredient);
            });
             App.validationInput();
        },  
        validationInput:function(){
            $('.qty-input').on('keydown', function (e) {
                if(window.event){ // IE                 
                    e.keyCode = e.keyCode;
                }else{
                    if(e.which){ // Netscape/Firefox/Opera                  
                        e.keyCode = e.which;
                     }
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

            $('.char-only').keypress(function (e) {
                var regex = new RegExp("^[a-zA-Z- ]+$");
                var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
                if (regex.test(str) || e.keyCode == 8 || e.keyCode == 37 || e.keyCode == 39 || e.keyCode == 46 ) {
                    return true;
                }

                e.preventDefault();
                return false;
            }); 

            $('.no-special-char').keypress(function (e) {
                var regex = new RegExp("^[a-zA-Z0-9 ]+$");
                 
                var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
                if (regex.test(str) || e.keyCode == 8 || e.keyCode == 37 || e.keyCode == 39 || e.keyCode == 46 ) {
                    return true;
                }

                e.preventDefault();
                return false;
            });
             
            $('.NumericDecimal').keypress(function (e) { 
                var regex = new RegExp("^[0-9. ]+$");
                var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
                if (regex.test(str) || e.keyCode == 8 || e.keyCode == 37 || e.keyCode == 39 || e.keyCode == 46 ) {
                    return true;
                }

                e.preventDefault();
                return false;
            });
        },
        employeeEvent:function(){

            var url = document.location.toString();
            if (url.match('#')) {
             
                $('.nav-tabs a[href=#'+url.split('#')[1]+']').tab('show') ;
            } 

            // With HTML5 history API, we can easily prevent scrolling!
            $('.nav-tabs a').on('shown.bs.tab', function (e) {
                if(history.pushState) {
                    history.pushState(null, null, e.target.hash); 
                } else {
                    window.location.hash = e.target.hash; //Polyfill for old browsers
                }
            });
            var tableEmployeeAffair =  $('#dataTables-staff').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrl').val(),
                "iDisplayLength" : 10,
                "columns"        : [ 
                    {data : "username"},
                     {data : "name"},
                     {data : "email"},
                     {data : "phone"},
                     {data : "gender"},
                    {data : "detail"},
                    // {data : "actions"}
                    ],
                "columnDefs"     : [
                    {
                        "targets"     : [5],
                        "orderable"   : false,
                        "bSearchable" : false,
                        "class"       : 'center-tr'
                    }
                ],
                "order"          : [[1, "asc"]] 
            }); 

             var datatable =  $('#dataTables-finger-template').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "scrollX":true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrlFingertemplates').val(),
                "iDisplayLength" : 10,
                "columns"        : [ 
                    {data : "finger_id"}, 
                    {data : "size"}
                ], 
                "order"          : [[0, "desc"]] 
            });  
        },

        jobsHistoryEvent:function(){
            $('#start-date').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'YYYY-MM-DD', 
                widgetPositioning:{
                    "vertical":"bottom"
                }  
            });
            $('#end-date').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'YYYY-MM-DD'
            });
            $("#start-date").on("dp.change", function (e) {
              if($('#end-date').length>0){
                $('#end-date').datetimepicker({
                    sideBySide: true,
                    useCurrent: true,
                    format: 'YYYY-MM-DD',
                    minDate:new Date($("#start-date input").val())
                });
                start_date=new Date($("#start-date input").val())
                start_date.setDate(start_date.getDate());
                $('#end-date').data("DateTimePicker").minDate(new Date(start_date.toLocaleDateString()));
                $('#end-date').data("DateTimePicker").minDate(new Date(start_date.toLocaleDateString()));
//                $('#end-date').data("DateTimePicker").minDate(new Date($("#start-date input").val()));
              }

            });


            var tableEmployeeStatus =  $('#dataTables-jobs-history').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrlJobs').val(),
                "iDisplayLength" : 10,
                "columns"        : [ 
                    {data : "status_name"},
                     {data : "start_date"},
                     {data : "end_date"},
                     {data : "store_name"},
                     {data : "jobs_name"},
                    {data : "reimburse"},
                    {data : "vacation"},
                    {data : "actions"}
                    ],
                "columnDefs"     : [
                    {
                        "targets"     : 7,
                        "orderable"   : false,
                        "bSearchable" : false,
                        "class"       : 'center-tr'
                    }
                ],
                "order"          : [[2, "asc"]] 
            });

            tableEmployeeStatus.on( 'draw.dt', function () {
                $(".delete-jobs-history").on("click",function(){
                    var id = $(this).attr('jobs-history-id');
                    App.confirm("Anda yakin ingin menghapus Surat Peringatan? ",function(id){  
                        var data = {
                            id : id
                        } 
                        var url = App.baseUrl+"admincms/hrd_staff/delete_jobs_history";
                        var request = $.ajax({
                            type    : 'POST',
                            url     : url, 
                            data    : data
                        });
                        request.done(function (msg) {  
                            tableEmployeeStatus.api().ajax.reload();
                        });
                        request.fail(function (jqXHR, textStatus) { 
                            App.alert("Maaf, Status History Gagal Di Hapus. Silahkan Hubungi Administrator");
                        });
                        request.always(function () {
                        }); 
                    },id);
                });


                $(".edit-jobs-history").on("click",function(){ 
                    var id = $(this).attr('jobs-history-id');  
                    $("#save-jobs-history").attr("data-action","edit");  
                    var request = $.ajax({
                        type    : 'POST',
                        url     : App.baseUrl+'admincms/hrd_staff/get_one_jobs_history', 
                        data    : { 
                            id : id
                        }
                    });
                    request.done(function (msg) { 
                         var parsedObject = JSON.parse(msg);
                         console.log(parsedObject);
                         if(parsedObject.status){
                           

                            $(".modal-title").html("Ubah status Kepegawaian "); 
                            $("#save-jobs-history").attr("data-action","edit"); 
                            $('#jobs-history-modal').modal('show'); 
                            $("#start-date-value").val(parsedObject.data.start_date);
                            $("#store_id").val(parsedObject.data.store_id);
                            $("#end-date-value").val(parsedObject.data.end_date); 
                            $("#reimburse").val(parsedObject.data.reimburse);
                            $("#vacation").val(parsedObject.data.vacation);
                            $("#jobs-history-id").val(parsedObject.data.id);
                         }
                    });
                    request.fail(function (jqXHR, textStatus) { 
                        App.alert("Maaf, Gagal Mengambil data Status History. Silahkan Hubungi Administrator");
                    });
                    request.always(function () {
                    }); 
                });
            }); 

            $("#save-jobs-history").on("click",function(){  
                var data = {
                    employee_id : $("#employee_id").val(),
                    emp_affair_id : $("#emp_affair_id").val(),
                    start_date : $("#start-date-value").val(),
                     jobs_id : $("#jobs_id").val(),
                    end_date : $("#end-date-value").val(),
                    store_id : $("#store_id").val(),
                    reimburse : $("#reimburse").val(),
                    vacation : $("#vacation").val()
                } 
                var url = App.baseUrl+"admincms/hrd_staff/save_jobs_history";


                 var start_date,end_date = "";
                 var errorList = "";


                if($(this).attr("data-action") == "edit"){
                    data = {
                            emp_affair_id : $("#emp_affair_id").val(),
                            start_date : $("#start-date-value").val(),
                            jobs_id : $("#jobs_id").val(),
                            // end_date : $("#end-date-value").val(),
                            store_id : $("#store_id").val(),
                            reimburse : $("#reimburse").val(),
                            vacation : $("#vacation").val(),
                            id : $("#jobs-history-id").val()
                        };
                        console.log(data);
                    url = App.baseUrl+"admincms/hrd_staff/update_jobs_history";
                } 
                 start_date = $("#start-date-value"); 
                 // end_date = $("#end-date-value");

                 if(start_date.val().length === 0){
                    start_date.parent().addClass("has-error");  
                    errorList += "Tanggal Mulai Tidak Boleh Kosong <br>";
                 }else{
                    start_date.parent().removeClass("has-error");
                 }

                 // if (end_date.val().length === 0){
                    // end_date.parent().addClass("has-error");  
                    // errorList += "Tanggal Akhir Tidak Boleh Kosong <br>"; 
                 // }else{
                    // end_date.parent().removeClass("has-error");
                 // }

                 if(errorList.length == 0){ 
                    var request = $.ajax({
                        type    : 'POST',
                        url     : url, 
                        data    : data
                    });
                    request.done(function (msg) { 
                        console.log(msg);
                         var parsedObject = JSON.parse(msg);
                         if(parsedObject.status){ 
                            //reset form 
                            $("#start-date-value").val("");
                            $("#end-date-value").val(""); 
                            $("#reimburse").val("");
                            $("#vacation").val("");
                            errorList = "";
                            $(".error-message").html(errorList);

                            tableEmployeeStatus.api().ajax.reload();

                            $('#jobs-history-modal').modal('hide');
                         }else{
                            App.alert(parsedObject.message);
                         } 
                    });
                    request.fail(function (jqXHR, textStatus) { 
                        App.alert("Maaf, Status Kepegawaian Gagal Di simpan. Silahkan Hubungi Administrator");
                    });
                    request.always(function () {
                    }); 
                 } 
                 $(".error-message").html(errorList);
            }); 


            $('#jobs-history-modal').on('hidden.bs.modal', function (e) {
                $("#start-date-value").val("");
                $("#end-date-value").val(""); 
                $("#reimburse").val("");
                $("#vacation").val("");

                $("#start-date-value").parent().removeClass("has-error");
                $("#end-date-value").parent().removeClass("has-error"); 
                $("#reimburse").parent().removeClass("has-error");
                $("#vacation").parent().removeClass("has-error");
                 
                $("#save-jobs-history").attr("data-action",""); 
                 $(".modal-title").html("Tambah Status Kepegawaian"); 
                  $(".error-message").html(" ");
            });
        },

        payrollHistoryEvent:function(){
            $('#period-date').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'MM-YYYY' 
            });
            $("#period-date").on("dp.change", function (e) { 
              if($("#payroll_history_content").length>0){
                employee_id=$("#employee_id").val();
                job_id=$("#jobs_id").val();
                job_history_id=$("#job_history_id").val();
                periode=$(this).find("input").val();
                $.ajax({
                  url:App.baseUrl+"admincms/hrd_payroll/generate_single_slip",
                  type:"POST",
                  dataType:"JSON",
                  data:{periode:periode,employee_id:employee_id,job_id:job_id,job_history_id:job_history_id},
                  success:function(response){
                    $("#payroll_history_content").html(response.content);
                    App.setSalaryComponentEVent();
                  }
                })
              }
            });

            var tablePayrollHistory =  $('#dataTables-payroll-history').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrlPayroll').val(),
                "iDisplayLength" : 10,
                "columns"        : [ 
                    {data : "name"},
                     {data : "store_name"},
                     {data : "jobs_name"},
                     {data : "period"},
                     {data : "payroll_total"}, 
                      {data : "view"}, 
                    {data : "actions"}
                    ],
                "columnDefs"     : [
                    {
                        "targets"     : [3,4,5,6],
                        "orderable"   : false,
                        "bSearchable" : false,
                        "class"       : 'center-tr'
                    }
                ],
                "order"          : [[1, "asc"]] 
            }); 


            var tablePayrollSlip =  $('#dataTables-payroll-slip').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrlSlip').val(),
                "iDisplayLength" : 10,
                "columns"        : [ 
                    {data : "name"},
                     {data : "store_name"},
                     {data : "jobs_name"},
                     {data : "period"},
                     {data : "payroll_total"}, 
                      {data : "view"}
                    // {data : "actions"}
                    ],
                "columnDefs"     : [
                    {
                        "targets"     : [3,4,5],
                        "orderable"   : false,
                        "bSearchable" : false,
                        "class"       : 'center-tr'
                    }
                ],
                "order"          : [[1, "asc"]] 
            }); 
            $("#generate-jobs,#generate-status").on("change",function(){
              periode=$("#period-date input").val();
              job_id=$("#generate-jobs").val();
              status_id=$("#generate-status").val();
              var id=$(this).attr("id");
              $.ajax({
                url:App.baseUrl + "admincms/hrd_payroll/get_employees_for_payroll",
                type:"POST",
                dataType:"JSON",
                data:{periode:periode,job_id:job_id,status_id:status_id},
                success:function(response){
                  $("#employee_list").html(response.content);
                }
              })
            });
            $("#save-generate-slip").on("click",function(){   
              url = App.baseUrl+"admincms/hrd_payroll/generate_slip";
              $("#form_payroll").attr("action",url);
              $("#form_payroll").submit();
            });
            $("#save-preview-slip").on("click",function(){   
              url = App.baseUrl+"admincms/hrd_payroll/preview_slip";
              $("#form_payroll").attr("action",url);
              $("#form_payroll").submit();
            });
            $("#save-print-slip").on("click",function(){   
              url = App.baseUrl+"admincms/hrd_payroll/print_slip";
              $("#form_payroll").attr("action",url);
              $("#form_payroll").submit();
            });
            $("#save-download-slip").on("click",function(){   
              url = App.baseUrl+"admincms/hrd_payroll/download_slip";
              $("#form_payroll").attr("action",url);
              $("#form_payroll").submit();
            });
            // App.generateSlip();
            // App.downloadSlip();
            // App.printSlip();
        },
        scheduleStaffList:function(){ 
            var datatable =  $('#dataTables-schedule-staff').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrl').val(),
                "iDisplayLength" : 10,
                "columns"        : [ 
                    {data : "nip"},
                     {data : "name"},
                     // {data : "standard_schedule"},
                     // {data : "special_schedule"},
                     {data : "history_holiday"}, 
                      {data : "set_change_schedule"}
                    ],
                 "columnDefs"     : [
                    {
                        "targets"     : [2,3],
                        "orderable"   : false,
                        "bSearchable" : false,
                        "class"       : 'center-tr'
                    }
                ],
                "order"          : [[0, "desc"]] 
            }); 
        },

        holidaysHistory:function(){
            var datatable_holiday_history =  $('#dataTables-holidays-history').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrl').val(),
                "iDisplayLength" : 10,
                "columns"        : [ 
                    {data : "nip"},
                    {data : "name"},
                    {data : "start_date"},
                    {data : "end_date"},
                    {data : "days"},
                    {data : "actions"}
                ],
                "columnDefs"     : [
                    {
                        "targets"     : 5,
                        "orderable"   : false,
                        "bSearchable" : false,
                        "class"       : 'center-tr'
                    }
                ],
                "order"          : [[1, "desc"]]
            });

            $("#trigger_filter_hrd_holidays").on("click",function(){
                var from_date = $('input[name="start_date"]:text').val();
                var end_date = $('input[name="end_date"]:text').val();
                column = 2;
                datatable_holiday_history.columns(column).search(date).draw();
            });
        },

        scheduleStandardEvent:function(){ 
            if(App.standardSchedRepeatStat == 1){
                 $("#container-end-date").hide();
                 $("#container-end-date").find('.no-special-char').removeClass("requiredTextField"); 
            }else{
                $("#container-end-date").show();
            }

            $('#start-time').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'HH:mm:ss'
            });

            $('#end-time').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'HH:mm:ss' 
            });

            $("#office_hour").on('change', function() {
                var request = $.ajax({
                    type : 'POST',
                    url  : App.baseUrl + 'admincms/hrd_schedule/get_data_office_hours',
                    data : {
                        template_id : $(this).val()
                    }
                });
                request.done(function(data) {
                    var parsedObject = JSON.parse(data);
                    if (parsedObject.status == true) {
                        data = parsedObject.data;
                        checkin_time = data.checkin_time;
                        checkout_time = data.checkout_time;
                        $("#checkin_time").val(checkin_time);
                        $("#checkout_time").val(checkout_time);
                    } else {
                        App.alert(parsedObject.data);
                    }
                });
            });


            $("#validate_standard_schedule").on('click',function(evt){  
                var target = $('.result');
                target.html('');                

                var start_time = $('#start-time').val();
                var end_time = $('#end-time').val();
                var office_hour = $('#office_hour').val();

                $('.requiredDropdown,.requiredDropDown').each(function () {
                    if ($(this).val() == '0' || $(this).val()=='' || $(this).val()==undefined) {
                        /*target.empty().html('<div class="alert alert-danger" role="alert">Anda harus memilih pilihan pada ' + $(this).attr('field-name') + '</div>');
                        evt.preventDefault();*/                        
                    }
                });

                if (office_hour == '0' || office_hour == '') {
                    if (start_time == '') {
                        target.empty().html('<div class="alert alert-danger" role="alert">Bagian ' + $('#start-time').attr('field-name') + ' dibutuhkan</div>');
                    }
                    if (end_time == '') {
                        target.empty().html('<div class="alert alert-danger" role="alert">Bagian ' + $('#end-time').attr('field-name') + ' dibutuhkan</div>');
                    }
                }                    

                $('.requiredTextField').each(function () {
                    if ($(this).val() == '') {
                        target.empty().html('<div class="alert alert-danger" role="alert">Bagian ' + $(this).attr('field-name') + ' dibutuhkan</div>');
                        evt.preventDefault();
                    }
                });

                $('html, body').animate({scrollTop:0}, 'fast'); 
                return false;
            });
            
            $('input[type=radio][name=repeat]').change(function() { 
                if (this.value == 0) {
                    $("#container-end-date").show();
                    $("#container-end-date").find('.no-special-char').addClass("requiredTextField");
                }
                else if (this.value == 1) {
                     $("#container-end-date").hide();
                     $("#container-end-date").find('.no-special-char').removeClass("requiredTextField"); 
                }
            });

            $('#template_id').change(function() { 
                if($(this).val() != 0){
                    var url = App.baseUrl+"admincms/hrd_schedule/get_data_office_hours";
                    var request = $.ajax({
                        type    : 'POST',
                        url     : url, 
                        data    : {
                            template_id : $(this).val()
                        }
                    });
                    request.done(function (msg) {   
                        var parsedObject = JSON.parse(msg);
                         if(parsedObject.status){  
                             $('#start-time-value').val(parsedObject.data.checkin_time);
                             $('#end-time-value').val(parsedObject.data.checkout_time);
                         }
                    });
                    request.fail(function (jqXHR, textStatus) { 
                        App.alert("Maaf,Ada Kesalahan Server. Silahkan Hubungi Administrator");
                    });
                    request.always(function () {
                    }); 
                }
               
            });
        },
        setComplimentUserEvent:function(){
            if(!$('.y_compliment').is(':checked'))
                $("#container-pin").hide();

            $('input[type=radio][name=is_compliment]').change(function() {
               
                if (this.value == 0) {
                    $("#container-pin").hide();
                }
                else if (this.value == 1) {
                     $("#container-pin").show();
                }
            });
        },
        reimburseEvent:function(){ 
            config={
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "responsive"     :true,
                "autoWidth"     :false,
                "ajax"           : $('#dataProcessUrl').val(),
                "iDisplayLength" : 10,
                "columns"        : [ 
                    {data : "store_name"},
                     {data : "name"},
                     {data : "jobs_name"},
                     {data : "created_at"},
                     {data : "total"},
                     {data : "note"},
                  //   {data : "attachment"},
                     {data : "actions"}
                    ],
                 "columnDefs"     : [
                    {
                        "targets"     : 6,
                        "orderable"   : false,
                        "bSearchable" : false,
                        "class"       : 'center-tr'
                    }
                ],
                "order"          : [[0, "desc"]] 
            };
            var tableReimburse =  $('#dataTables-reimburse').dataTable(config);  
            $(window).on({
              resize         : function (e) {
                if(tableReimburse.fnClearTable() && tableReimburse.fnDestroy()){
                  tableReimburse = $('#dataTables-reimburse').dataTable(config);
                }
              }
            });
            var $input = $('.typeahead');
                $input.typeahead({source:[{id: "someId1", name: "Display name 1"}, 
                            {id: "someId2", name: "Display name 2"}], 
                            autoSelect: true}); 
                $input.change(function() {
                    var current = $input.typeahead("getActive");
                    if (current) {
                        // Some item from your model is active!
                        if (current.name == $input.val()) {
                            // This means the exact match is found. Use toLowerCase() if you want case insensitive match.
                        } else {
                            // This means it is only a partial match, you can either add a new item 
                            // or take the active if you don't want new items
                        }
                    } else {
                        // Nothing is active so it is a new value (or maybe empty value)
                    }
                });
        },
        attendanceEvent:function(){
            var datatable_attendance =  $('#dataTables-attendance').DataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "iDisplayLength" : 25,
                "columns"        : [ 
                
                                     {data : "name"}, 
                                     {data : "curdate"}, 
                                     {data : "schedule_time"},
                                     {data : "over_checkin_time"},
                                     {data : "over_checkout_time"},
                                     {data : "checkin_time"},
                                     {data : "checkout_time"},
                                     {data : "enum_status_attendance"},
                                     {data : "attachment"},
                                     {data : "performance"},
                                     // {data : "actions"}
                                ],
                                "ajax": {
                  "url": $('#dataProcessUrl').val(),
                  "type": 'POST',
                  "data": function(d){
                    d.office_hour_id= $('#office_hour_id').val()
                  }
                },
                 "columnDefs"     : [
                                    {
                                        "targets"     : [9,1],
                                        "orderable"   : false,
                                        "bSearchable" : false,
                                        "class"       : 'center-tr'
                                    }
                ],
                "order"          : [[0, "desc"]] 
            }); 

            $("#search_attendance").on("click",function(){
              var date=$("#attendance_date").val();
              column=1;
              datatable_attendance.columns(column).search(date).draw();
            });

             var datatable =  $('#dataTables-attendance-history').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrlAttendance').val(),
                "iDisplayLength" : 10,
                "columns"        : [ 
                
                     {data : "name"}, 
                     {data : "curdate"}, 
                     {data : "schedule_time"},
                    {data : "checkin_time"},
                     {data : "checkout_time"},
                     {data : "enum_status_attendance"},
                     {data : "attachment"}
                    ],
                 "columnDefs"     : [
                    {
                        "targets"     :[1,5,6],
                        "orderable"   : false,
                        "bSearchable" : false,
                        "class"       : 'center-tr'
                    }
                ],
                "order"          : [[0, "desc"]] 
            });  
     
        },
        shiftEvent:function(){
             var table =  $('#table-shift').dataTable({
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

                "columns"        : [
                    
                    {data : "name"},
                    {data : "start_date"},
                    {data : "end_date"},
                    {data : "start_time"},
                    {data : "end_time"},
                    {data : "ofname"}

                    ],
                
                "order": [[0, "desc"]],
              }); //end datatable
            
            $('input[type=radio][name=repeat_exchange]').change(function() { 
                if (this.value == 0) {
                    $("#end_date_view").show();
                }
                else if (this.value == 1) {
                     $("#end_date_view").hide(); 
                }
            });
           
            $("#tukar_shift").on("click",function(){
                var status = true;
                if($('#user_1').val() == $('#user_2').val()){
                    App.alert("Tidak Bisa memilih pegawai yang sama !"); 
                    status = false;   
                }
                if($('#user_1').val() == ""){
                    App.alert("Pegawai Tidak Di Pilih"); 
                    status = false;
                }
                if($('#user_2').val() == ""){
                    App.alert("Pegawai Tidak Di Pilih");  
                    status = false;
                }
                if(status){
                    return true;
                }else{
                    return false;
                }

                 
            });
       },
        moveshiftEvent:function(){ 
            var movetable =  $('#table-move-shift').dataTable({
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
                "columns"        : [ 
                    {data : "name"},
                    {data : "start_date"},
                    {data : "end_date"},
                    {data : "start_time"},
                    {data : "end_time"},
                    {data : "ofname"} 
                    ], 
                "order": [[1, "desc"]],
            }); //end datatable
            
            $('input[type=radio][name=repeat_exchange]').change(function() { 
                if (this.value == 0) {
                    $("#end_date_view2").show();
                }
                else if (this.value == 1) {
                     $("#end_date_view2").hide(); 
                }
            });

            $("#from_office_hour_1").on('change',function(e){
                var office_hour_id=$(this).val();
                var element=this;
                $.ajax({
                  url:App.baseUrl+"admincms/hrd_shift/get_data_employee_by_office_hour_1",
                  dataType:"JSON",
                  type:"POST",
                  data:{office_hour_id:office_hour_id},
                  success:function(response){
                    
                      $("#multiselect_from_1").html(response.content);
                    
                  }
                });
                return false;
            });

            $("#to_office_hour_1").on('change',function(e){
                var office_hour_id=$(this).val();
                var element=this;
                $.ajax({
                  url:App.baseUrl+"admincms/hrd_shift/get_data_employee_by_office_hour_1",
                  dataType:"JSON",
                  type:"POST",
                  data:{office_hour_id:office_hour_id},
                  success:function(response){
                   
                      $("#multiselect_to_1").html(response.content);
                    
                  }
                });
                return false;
            });
           
            $("#move_shift").on("click",function(){
                var status = true;
                if($('#from_office_hour_1').val() == ""){
                    App.alert("Asal Jam Kerja Tidak Di Pilih !");  
                    status = false; 
                }
                if($('#to_office_hour_1').val() == ""){
                    App.alert("Tujuan Jam Kerja Tidak Di Pilih !");  
                    status = false; 
                }
                if($('#from_office_hour_1').val() == $('#to_office_hour_1').val()){
                    App.alert("Tidak Bisa Memilih Jam Kerja Yang Sama !");  
                    status = false; 
                } 

                if(status){
                    return true;
                }else{
                    return false;
                }
            }); 
        },
        kelolajadwal:function(){

            var table =  $('#table-kelola-jadwal').dataTable({
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

                "columns"        : [
                    
                    {data : "nm"}, 
                    {data : "sd"},
                    {data : "ed"},
                    {data : "st"},
                    {data : "et"}

                    ],

                "order": [[0, "desc"]],
              }); //end datatable
        },
        employeesHoliday:function(){

            var table =  $('#table-holidays').dataTable({
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

                "columns"        : [
                    
                    {data : "name"}, 
                    {data : "day"},
                    {data : "created_at"}

                    ],

                "order": [[0, "desc"]],
              }); //end datatable
        },
        fingerprint:function(){
            var table =  $('#table-fingerprint').dataTable({
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
                "columns"        : [ 
                    {data : "name"},
                    {data : "curdate"},
                    {data : "time"} 
                ],
                "columnDefs"     : [
                    {
                        "targets"     : 2,
                        "orderable"   : false,
                        "bSearchable" : false,
                        "class"       : 'center-tr'
                    }
                ],
                "order": [[0, "desc"]],
              }); 
            
           
            $("#filter_submit").on('click', function (e) {
                e.preventDefault();
                var table =  $('#table-fingerprint').dataTable({
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

                "columns"        : [
                    
                    {data : "name"},
                    {data : "curdate"},
                    {data : "time"}

                    ],
                "columnDefs"     : [
              {
                "targets"     : 2,
                "orderable"   : false,
                "bSearchable" : false,
                "class"       : 'center-tr'
              }
                ],
                "order": [[0, "desc"]],
              }); //end datatable

            }); 

            $("#download_fingerprint").on("click",function(){
                App.overlayUI.show();
                var data = {
                    start_date : $("#input_start_date").val(),
                    end_date : $("#input_end_date").val()
                } 
                var url = App.baseUrl + 'index.php/hrd_scheduler/save_all_fingerprint';
                var request = $.ajax({
                    type    : 'POST',
                    url     : url, 
                    data    : data 
                });
                request.done(function (msg) {
                    App.alert("Download Data Fingerprint Berhasil!",function(){
                        App.overlayUI.hide(); 
                    });
                });
                request.fail(function (jqXHR, textStatus) {
                    App.alert("Gagal Mendownload Data Fingerprint!",function(){
                        App.overlayUI.hide();
                    });          
                });
                request.always(function () {
                });
                return false
            });
        },
        downloadLogData:function(urlx){
            App.overlayUI.show();
            var request = $.ajax({
                type : 'POST', 
                url  :App.baseUrl + 'index.php/hrd_scheduler/'+urlx
            });
            request.done(function (msg) {
                App.alert("Download Data Fingerprint Berhasil!",function(){

                                App.overlayUI.hide();

                });
            });
            request.fail(function (jqXHR, textStatus) {
                App.alert("Gagal Mendownload Data Fingerprint!",function(){

                                App.overlayUI.hide();

                });          
            });
            request.always(function () {
            });
        },
        performanceEvent:function(){ 
            $('#start-date-statistic').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'YYYY-MM-DD' 
            });

            $("#start-date-statistic").on("dp.change", function (e) { 
                $('#end-date-statistic').datetimepicker({
                    sideBySide: true,
                    useCurrent: true,
                    format: 'YYYY-MM-DD'
                });

                $('#end-date-statistic').data("DateTimePicker").minDate(e.date);
                 $("#end-date-statistic").on("dp.change", function (e) { 
                    alert("RELOAD TABLE");
                 });
            });


            var x =  $('#dataTables-perfomance-statistic').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "autoWidth":false,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrlAttendanceSta').val(),
                "iDisplayLength" : 10,
                "columns"        : [  
                     {data : "name"}, 
                     {data : "total_days"}
                    ],
                 "columnDefs"     : [
                    {
                        "targets"     : 0,
                        "orderable"   : false,
                        "bSearchable" : false,
                        "class"       : 'center-tr'
                    }
                ],
                "order"          : [[1, "desc"]] 
            });  


            App.initGraphAttendance();
        },
        initGraphAttendance:function(){
            if( $('#graph-attendance').length <= 0) return;
            var data = {
                user_id : $("#user_id").val()
            } 
            var url = App.baseUrl+"admincms/hrd_attendance/get_data_graphic_attendance";
            var request = $.ajax({
                type    : 'POST',
                url     : url, 
                data    : data
            });
            request.done(function (msg) {  
                var parsedObject = JSON.parse(msg);
                $('#graph-attendance').highcharts({
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
                        backgroundColor: '#FFFFFF',
                        align: 'left',
                        verticalAlign: 'top',
                        x: 100,
                        y: 70,
                        floating: true,
                        shadow: true
                    },
                     series:parsedObject
                }); 
            });
            request.fail(function (jqXHR, textStatus) { 
                App.alert("Data Kosong"); 
            });
            request.always(function () {
            }); 
           
        },
        loanEvent:function(){
            var loanDatatable =  $('#dataTables-loan').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "autoWidth":false,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrlLoan').val(),
                "iDisplayLength" : 10,
                "columns"        : [  
                     {data : "store_name"}, 
                     {data : "name"},
                     {data : "jobs_name"},
                     {data : "loan_date"},
                     {data : "loan_total"},
                     {data : "instalment"},
                     {data : "repayment_total"}, 
                     {data : "outstanding_total"},
                     {data : "view"},
                     {data : "actions"}
                    ],
                 "columnDefs"     : [
                    {
                        "targets"     : [0,6,7,8,9],
                        "orderable"   : false,
                        "bSearchable" : false,
                        "class"       : 'center-tr'
                    }
                ],
                "order"          : [[0, "desc"]] 
            });  


            var repaymentDatatable =  $('#dataTables-repayments').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "scrollX":true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrlRepayments').val(),
                "iDisplayLength" : 10,
                "columns"        : [  
                     {data : "repayment_date"}, 
                     {data : "repayment_total"}, 
                     {data : "repayment_method"},
                     {data : "actions"}
                    
                    ],
                 "columnDefs"     : [
                    {
                        "targets"     : 3,
                        "orderable"   : false,
                        "bSearchable" : false,
                        "class"       : 'center-tr'
                    }
                ],
                "order"          : [[0, "desc"]] 
            });  


            $('#repayment-date').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'YYYY-MM-DD' 
            });
            $("#save-repayment").on("click",function(){ 
                var data = {
                    repayment_total : $("#repayment_total").val(),
                    repayment_date : $("#repayment_date").val(),
                    loan_id : $("#loan_id").val()

                } 
                var url = App.baseUrl+"admincms/hrd_loan/save_repayment";
                var request = $.ajax({
                    type    : 'POST',
                    url     : url, 
                    data    : data
                });
                request.done(function (msg) { 
                    console.log(msg);
                     var parsedObject = JSON.parse(msg);
                     if(parsedObject.status){  
                        repaymentDatatable.api().ajax.reload(); 
                         $('#repayment-modal').modal('hide');
                     }else{
                        App.alert(parsedObject.message);
                     } 
                });
                request.fail(function (jqXHR, textStatus) { 
                    App.alert("Maaf, Data Pembayaran Gagal Di simpan. Silahkan Hubungi Administrator");
                });
                request.always(function () {
                }); 
            });


            $('#payment_option').change(function() { 
                var id=$(this).val(); 

                if(id == 0){
                    $("#instalment").removeAttr("disabled");
                }else if(id == 1){ // cash bon
                    $("#instalment").val(1);
                    $("#instalment").attr("disabled","disabled");
                }else if(id == 2){ //cicilan
                     $("#instalment").removeAttr("disabled");
                }else if(id == 3){ //pinjaman
                    $("#instalment").attr("disabled","disabled"); 
                     $("#instalment").val(1);
                } 
            });

            $('#loan_user_id').change(function() { 
                if($(this).val() != 0){
                    $("#take_home_pay").css({
                        "display":"block"
                    }); 


                    var url = App.baseUrl+"admincms/hrd_loan/get_last_payroll";
                    var request = $.ajax({
                        type    : 'POST',
                        url     : url,
                        data    : {
                            "user_id" : $(this).val()
                        }
                    });
                    request.done(function (msg) {   
                        var parsedObject = JSON.parse(msg);
                        if(parsedObject.status){ 
                             $("#take_home_pay_value").html("Rp. 0");    
                            if(!parsedObject.data.total_take_home_pay){
                                parsedObject.data.total_take_home_pay = 0;
                            }

                            $("#total_take_home").val(parsedObject.data.total_take_home_pay);    

                            var total_take_home_pay = parseInt(parsedObject.data.total_take_home_pay);
                            var format_total = App.moneyFormat(total_take_home_pay,"Rp.");

                            if(total_take_home_pay){
                                $("#take_home_pay_value").html(format_total);    
                            } 
                            

                        }
                    });
                    request.fail(function (jqXHR, textStatus) { 
                        App.alert("Maaf, Ambil data gagal. Silahkan Hubungi Administrator");
                    });
                    request.always(function () {
                    }); 
                }else{
                      $("#take_home_pay").css({
                        "display":"block"
                    });
                }     
            });
        },



        overtimeEvent:function(){
            var overtimeDatatable =  $('#dataTables-overtime').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "autoWidth":false,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrlovertime').val(),
                "iDisplayLength" : 5,
                "columns"        : [  
                     {data : "uname"}, 
                     {data : "created_at"},
                     {data : "start_time"},
                     {data : "end_time"},
                     {data : "note"},
                     {data : "actions"}
                   
                    ],
                 "columnDefs"     : [
                    {
                        "targets"     : [2,3,5],
                        "orderable"   : false,
                        "bSearchable" : false,
                        "class"       : 'center-tr'
                    }
                ],
                "order"          : [[2, "desc"]] 
            });   

        },


        moneyFormat:function(n, currency){
         return currency + " " + n.toFixed(0).replace(/./g, function(c, i, a) {
                return i > 0 && c !== "," && (a.length - i) % 3 === 0 ? "." + c : c;
            });
         
        },
        templateAuditEvent:function(){
            var repaymentDatatable =  $('#dataTables-template-list').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true, 
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrlTemplate').val(),
                "iDisplayLength" : 10,
                "columns"        : [  
                     {data : "name"}, 
                     {data : "description"},
                     {data : "actions"}
                    
                    ],
                 "columnDefs"     : [
                    {
                        "targets"     : 2,
                        "orderable"   : false,
                        "bSearchable" : false,
                        "class"       : 'center-tr'
                    }
                ],
                "order"          : [[0, "desc"]] 
            });  


            var indexCategory = 1; 
            indexCategory = $("#child-list").children().length;
            $("#add_audit_category").on("click",function(){
                App.createHtmlAuditCategory(parseInt(indexCategory));
                indexCategory++;
            });
            App.addDetailCategoryEvent();
           
        },
        createHtmlAuditCategory:function(index){
            var html = 
            '<div class="col-lg-6 col-sm-offset-3" id="category-'+index+'">'+ 
              '<div class="panel panel-default">'+
                '<div class="panel-heading"> '+
                    '<a class="btn remove_category" id="'+index+'"   >X</a>'+
                '</div>'+
                '<div class="panel-body">'+
                        '<div class="col-lg-12" > '+
                            '<div class="form-group">'+
                                '<label for="floor_name" class="col-sm-4 control-label">Nama Kategori</label> '+
                                '<div class="col-sm-8">'+
                                    '<input type="text" name="category[]" class="form-control no-special-char">'+
                                '</div>'+
                           ' </div>   '+
                            '<div class="col-lg-12" id="detail-container-'+index+'">'+
                                '<div class="panel panel-default" id="category-detail-0">'+
                                    '<div class="panel-body">'+
                                        '<div class="col-lg-12">'+
                                             '<div class="form-group">'+
                                               ' <label for="floor_name" class="col-sm-2 control-label">Nama</label> '+
                                                '<div class="col-sm-10">'+
                                                   '<input type="text" name="detail_category['+index+'][0][name]" class="form-control no-special-char">'+
                                                '</div>'+
                                            '</div>    '+
                                       ' </div>'+
                                         '<div class="col-lg-12">'+
                                            ' <div class="form-group">'+
                                               '<label for="floor_name" class="col-sm-2 control-label">Point</label> '+
                                              '  <div class="col-sm-10">'+
                                               '      <input type="text" name="detail_category['+index+'][0][point]" class="form-control qty-input">'+
                                              '  </div>'+
                                          '  </div>    '+
                                      '  </div> '+
                                    '</div>  '+ 
                              '  </div>'+   
                            '</div> '+  
                       ' </div>'+
                       '<div class="col-lg-12">'+
                            '<div class="form-group"> '+
                               ' <div class="col-sm-4 col-sm-offset-5">'+
                                     '<a   category-id="'+index+'"    class="btn btn-default add_audit_detail_category">Tambah</a>'+
                                '</div>'+
                            '</div>    '+
                       '</div>    '+
                     '</div>    '+
                 '</div>    '+
             '</div>    ';

            $("#category_list").append(html);
            $(".add_audit_detail_category").unbind("click");
            App.addCategoryEvent();
           App.addDetailCategoryEvent();
           App.validationInput();
        },
        createHtmlAuditDetailCategory:function(index,category_id){  
            console.log(index);
            var html = 
            '<div class="panel panel-default" id="category-detail-'+index+'">'+
                '<div class="panel-heading"> '+
                    '<a class="btn remove_detail_category" id="'+index+'"  category-id = '+category_id+' >X</a>'+
                '</div>'+
                '<div class="panel-body">'+
                    '<div class="col-lg-12">'+
                         '<div class="form-group">'+
                           ' <label for="floor_name" class="col-sm-2 control-label">Nama</label> '+
                            '<div class="col-sm-10">'+
                               '<input type="text" name="detail_category['+category_id+']['+index+'][name]" class="form-control no-special-char">'+
                            '</div>'+
                      '  </div> '+   
                 '   </div>'+
                    ' <div class="col-lg-12">'+
                        ' <div class="form-group">'+
                           ' <label for="floor_name" class="col-sm-2 control-label">Point</label> '+
                           ' <div class="col-sm-10">'+
                             '    <input type="text" name="detail_category['+category_id+']['+index+'][point]" class="form-control qty-input">'+
                          '  </div>'+
                        '</div>    '+
                   ' </div> '+
              '  </div>   '+
           ' </div>   ';
            $(".add_audit_detail_category").unbind("click");
           $("#detail-container-"+category_id).append(html);
           App.addDetailCategoryEvent();
           App.validationInput();
        },
        addCategoryEvent:function(){ 
            $(".remove_category").on("click",function(){
                var id = $(this).attr("id"); 
                
                $("#category_list").find("#category-"+id).remove();
                console.log($("#category_list").find("#category-"+id));
            });
        },
        addDetailCategoryEvent:function(){ 
            $(".add_audit_detail_category").on("click",function(){  
                var category_id = $(this).attr("category-id");
                var indexDetailCategory = 1;
                indexDetailCategory = $("#detail-container-"+category_id).children().length;
                App.createHtmlAuditDetailCategory(indexDetailCategory,category_id);
                 
            }); 

            $(".remove_detail_category").on("click",function(){    
                 var category_id = $(this).attr("category-id");
                 var id = $(this).attr("id");
                 $("#category-"+category_id).find("#category-detail-"+id).remove();
            }); 
        },   
        processAuditEvent:function(){
            var repaymentDatatable =  $('#dataTables-process-list').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true, 
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrlProcessAudit').val(),
                "iDisplayLength" : 10,
                "columns"        : [  
                     {data : "store_name"}, 
                     {data : "period"},
                     {data : "created_at"},
                     {data : "template_name"},
                     {data : "description"},
                     {data : "actions"}
                    
                    ],
                 "columnDefs"     : [
                    {
                        "targets"     : 5,
                        "orderable"   : false,
                        "bSearchable" : false,
                        "class"       : 'center-tr'
                    }
                ],
                "order"          : [[0, "desc"]] 
            });   


            $('#template_audit').change(function() { 
                var url = App.baseUrl+"admincms/hrd_audit/download_template";
                var template_id = $(this).val();
                if(template_id == 0){
                    $("#category_list").empty();
                }else{
                    var request = $.ajax({
                        type    : 'POST',
                        url     : url, 
                        data    : { 
                            template_id : template_id
                        }
                    });
                    request.done(function (msg) {  
                        $("#category_list").html(msg);
                        $("#max_grade_audit").html($("#max_grade").val());
                        App.sliderEvent();        
                    });
                    request.fail(function (jqXHR, textStatus) { 
                        App.alert("Maaf, Status Kepegawaian Gagal Di Hapus. Silahkan Hubungi Administrator");
                    });
                    request.always(function () {
                    }); 
                } 
            });  
            
        },
        sliderEvent:function(){ 
            $(".slider-range-min").each(function(total) { 

                $(this).slider({
                      range: "min",
                      value: 0,
                      min: $(this).attr("min"),
                      max: $(this).attr("max"),
                      slide: function( event, ui ) { 
                        $( "#"+$(this).attr("child") ).val(ui.value); 
                       
                      },
                      stop:function(event, ui ){  
                         update_total();
                      }
                });
                $( "#"+$(this).attr("child") ).val($( "#slide-"+$(this).attr("child")).slider( "value" ) );
                
               
            });
           
            function update_total(){
                var total = 0;
                var max_grade_audit = parseInt($("#max_grade_audit").html());
                $(".point").each(function() { 
                    total += parseInt($(this).val());
                }); 
                

                var percentage = parseInt(total)/max_grade_audit * 100;

                $("#total_grade_audit").html(total);   
                $("#total_precentage_audit").html(percentage.toFixed(2)+" %");
            }    
        }, 
        rollingShift:function(){
            $('input[type=radio][name=repeat-rolling]').change(function() { 
                if (this.value == 0) {
                    $("#conta-end-date").show();
                    $("#conta-end-date").find('.no-special-char').addClass("requiredTextField");
                }
                else if (this.value == 1) {
                     $("#conta-end-date").hide();
                     $("#conta-end-date").find('.no-special-char').removeClass("requiredTextField"); 
                }
            });

            $('#start-date-rolling').datetimepicker({
                useCurrent: true,
                format: 'YYYY-MM-DD' 
            });

            $('#end-date-rolling').datetimepicker({
                useCurrent: true,
                format: 'YYYY-MM-DD' 
            });
        },

        setHolidays:function(){
            var target = $('.result');
            target.html('');
          $('#holiday-date').datetimepicker({
                useCurrent: true,
                format: 'YYYY-MM-DD' 
            });

            var date = $('#holiday-date').val();
            /*if (date == '') {
                target.empty().html('<div class="alert alert-danger" role="alert">Anda harus memilih pilihan pada ' + $(this).attr('field-name') + '</div>');
            }*/
        },

        exchangeShift:function(){
            $('input[type=radio][name=repeat-exchange]').change(function() {
                if (this.value == 0) {
                    $("#container-end-date-exchange").show();
                    $("#container-end-date-exchange").find('.no-special-char').addClass("requiredTextField");
                }
                else if (this.value == 1) {
                     $("#container-end-date-exchange").hide();
                     $("#container-end-date-exchange").find('.no-special-char').removeClass("requiredTextField"); 
                }
            });

          $("#from_office_hour,#to_office_hour").on('change',function(e){
            var office_hour_id=$(this).val();
            var element=this;
            $.ajax({
              url:App.baseUrl+"admincms/hrd_schedule/get_data_employee_by_office_hour",
              dataType:"JSON",
              type:"POST",
              data:{office_hour_id:office_hour_id},
              success:function(response){
                if($(element).attr("id")=="from_office_hour"){
                  $("#multiselect_from_1").html(response.content);
                }else{
                  $("#multiselect_to_1").html(response.content);
                }
              }
            });
            return false;
          });
        },

        settingRollingOfficeHour:function(){
            $("#from_office_hour_id").on('change',function(e){
            var office_hour_id=$(this).val();
            var element=this;
            $.ajax({
              url:App.baseUrl+"admincms/hrd/get_data_office_hour_rolling",
              dataType:"JSON",
              type:"POST",
              data:{office_hour_id:office_hour_id},
              success:function(response){
                $("#multiselect_from_1").html(response.content_from);
                $("#multiselect_to_1").html(response.content_to);
              }
            });
            return false;
          });
        }
    }
});