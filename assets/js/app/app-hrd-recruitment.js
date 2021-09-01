 
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
           
            App.recruitment.initEvent();   
            
        },
                 
        initEvent:function(){
            console.log("RECRUITMENT"); 
            App.recruitment.familyTableEvent();
            App.recruitment.siblingsTableEvent();
            App.recruitment.eduTableEvent();
            App.recruitment.coursesTableEvent();

            App.recruitment.exprerienceTableEvent();
            App.recruitment.orgTableEvent();

            $('#mate_birth_date').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'YYYY-MM-DD',
                widgetPositioning:{
                    "vertical":"bottom"
                } 
            });

            $('#report_attendance_start_date').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'YYYY-MM-DD' 
            });

            $('#report_attendance_end_date').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'YYYY-MM-DD' 
            });
            $('#report_payroll_start_period').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'MM-YYYY' 
            });

            $('#report_payroll_end_period').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'MM-YYYY' 
            });

             $('#report_appraisal_period').datetimepicker({
                sideBySide: true,
                useCurrent: true,
                format: 'MM-YYYY' 
            });

            var tableMemorandum =  $('#dataTables-recruitment').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "scrollX": true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrlRecruit').val(),
                "iDisplayLength" : 10,
                "columns"        : [ 
                    {data : "name"},
                    {data : "created_at"},
                    {data : "phone_no"},
                    {data : "job_apply"},
                    {data : "actions"}
                    ],
                "columnDefs"     : [
                    {
                        "targets"     : [4],
                        "orderable"   : false,
                        "bSearchable" : false,
                        "class"       : 'center-tr'
                    }
                ],
                "order"          : [[0, "asc"]] 
            });

            App.recruitment.removeHtmlEvent();
        }, 

        orgTableEvent:function(){
            var index =  $("#dataTables-org").find("tbody").children().length;
            $("#add-org").on("click",function(){  
                var html = App.recruitment.createOrgForm(index);
                $("#dataTables-org").find("tbody").append(html);

               

                App.recruitment.removeHtmlEvent();
                App.validationInput();
                index++;
            });
        },
        createOrgForm:function(index){
            var html = ' <tr id="remove-org-'+index+'"> '+
                        '<td ><input type="text" name="org['+index+'][experience_company]" class="form-control no-special-char"></td>'+ 
                         '<td ><input type="text" name="org['+index+'][experience_period]" class="form-control "></td> '+
                       '<td ><input type="text" name="org['+index+'][experience_job]" class="form-control no-special-char"></td> '+
                       '<td ><input type="text" name="org['+index+'][experience_reason]" class="form-control no-special-char"></td> '+
                        '<td ><a  class="btn remove-org" id='+index+'>X</a></td> '+
                    '</tr>';
            return html;
 
        },

        exprerienceTableEvent:function(){
            var index =  $("#dataTables-experience").find("tbody").children().length;
            $("#add-experience").on("click",function(){  
                var html = App.recruitment.createExperienceForm(index);
                $("#dataTables-experience").find("tbody").append(html);

              

                App.recruitment.removeHtmlEvent();
                App.validationInput();
                index++;
            });
        },
        createExperienceForm:function(index){
            var html = ' <tr id="remove-experience-'+index+'"> '+
                        '<td ><input type="text" name="experience['+index+'][experience_company]" class="form-control no-special-char"></td>'+ 
                         '<td ><input type="text" name="experience['+index+'][experience_period]" class="form-control "></td> '+
                       '<td ><input type="text" name="experience['+index+'][experience_job]" class="form-control no-special-char"></td> '+
                       '<td ><input type="text" name="experience['+index+'][experience_reason]" class="form-control no-special-char"></td> '+
                        '<td ><a  class="btn remove-experience" id='+index+'>X</a></td> '+
                    '</tr>';
            return html;
 
        },

        eduTableEvent:function(){
            var index =  $("#dataTables-edu").find("tbody").children().length;
            $("#add-edu").on("click",function(){  
                var html = App.recruitment.createEduForm(index);
                $("#dataTables-edu").find("tbody").append(html);

             
                App.recruitment.removeHtmlEvent();
                App.validationInput();
                index++;
            });
        },
        createEduForm:function(index){
            var html = ' <tr id="remove-edu-'+index+'"> '+
                        '<td ><input type="text" name="edu['+index+'][period]" class="form-control  qty-input"></td>'+ 
                         '<td ><input type="text" name="edu['+index+'][school_name]" class="form-control no-special-char"></td> '+
                       '<td ><input type="text" name="edu['+index+'][city]" class="form-control no-special-char"></td> '+
                       '<td ><input type="text" name="edu['+index+'][legacy]" class="form-control no-special-char"></td> '+
                        '<td ><a  class="btn remove-edu" id='+index+'>X</a></td> '+
                    '</tr>';
            return html;
 
        },

         coursesTableEvent:function(){
            var index =  $("#dataTables-courses").find("tbody").children().length;
            $("#add-courses").on("click",function(){  
                var html = App.recruitment.createCourseForm(index);
                $("#dataTables-courses").find("tbody").append(html);

                App.recruitment.removeHtmlEvent();
                App.validationInput();
                index++;
            });
        },
        createCourseForm:function(index){
            var html = ' <tr id="remove-courses-'+index+'"> '+
                        '<td ><input type="text" name="courses['+index+'][course_name]" class="form-control no-special-char" ></td>'+ 
                         '<td ><input type="text" name="courses['+index+'][course_time]" class="form-control" placeholder="2013-2014"></td> '+
                       '<td ><input type="text" name="courses['+index+'][course_place]" class="form-control no-special-char" placeholder="Bandung"></td> '+
                       '<td ><input type="text" name="courses['+index+'][course_description]" class="form-control no-special-char"></td> '+
                        '<td ><a  class="btn remove-courses" id='+index+'>X</a></td> '+
                    '</tr>';
            return html;


        },
        siblingsTableEvent:function(){
            var index =  $("#dataTables-siblings-list").find("tbody").children().length;
            $("#add-siblings").on("click",function(){ 
                var html = App.recruitment.createSiblingsForm(index);
                $("#dataTables-siblings-list").find("tbody").append(html);

                App.recruitment.removeHtmlEvent();
                App.validationInput();
                index++;
            });
        },
        createSiblingsForm:function(index){
           var html = ' <tr id="remove-siblings-'+index+'"> '+
                        '<td ><input type="text" name="siblings['+index+'][name]" class="form-control no-special-char"></td>'+
                        '<td ><input type="text" name="siblings['+index+'][status]" class="form-control no-special-char"></td> '+
                        '<td ><input type="text" name="siblings['+index+'][age]" class="form-control qty-input"></td> '+
                       '<td >'+
                            '<select name = "siblings['+index+'][education]" class="form-control">'+
                               '<option value="SD">SD</option>'+
                               '<option value="SMP">SMP</option>'+
                               '<option value="SMA">SMA</option>'+
                               '<option value="D3">D3</option>'+
                               '<option value="D4">D4</option>'+
                               '<option value="S1">S1</option>'+
                               '<option value="S2">S2</option>'+
                               '<option value="S3">S3</option>'+
                            '</select>'+
                        '</td> '+
                        '<td ><a  class="btn remove-siblings" id='+index+'>X</a></td> '+
                    '</tr>';
            return html;


        },
        familyTableEvent:function(){
            var index =  $("#dataTables-family-list").find("tbody").children().length;
            $("#add-family").on("click",function(){ 
                var html = App.recruitment.createFamilyForm(index);
                $("#dataTables-family-list").find("tbody").append(html);

                App.recruitment.removeHtmlEvent();
                App.validationInput();
                index++;
            });
        },
        createFamilyForm:function(index){
           
             var html = ' <tr id="remove-family-'+index+'"> '+
                        '<td ><input type="text" name="family['+index+'][name]" class="form-control no-special-char"></td>'+ 
                        '<td ><input type="text" name="family['+index+'][age]" class="form-control qty-input"></td> '+
                       '<td >'+
                            '<select name = "family['+index+'][education]" class="form-control">'+
                               '<option value="SD">SD</option>'+
                               '<option value="SMP">SMP</option>'+
                               '<option value="SMA">SMA</option>'+
                               '<option value="D3">D3</option>'+
                               '<option value="D4">D4</option>'+
                               '<option value="S1">S1</option>'+
                               '<option value="S2">S2</option>'+
                               '<option value="S3">S3</option>'+
                            '</select>'+
                        '</td> '+
                        '<td ><a  class="btn remove-family" id='+index+'>X</a></td> '+
                    '</tr>';
            return html;

        },
        removeHtmlEvent:function(){
            $(".remove-family").on("click",function(){
                var id = $(this).attr("id");  
                $("#remove-family-"+id).remove(); 
            });
            $(".remove-siblings").on("click",function(){
                    var id = $(this).attr("id"); 
                    $("#remove-siblings-"+id).remove(); 
                });
             $(".remove-courses").on("click",function(){
                    var id = $(this).attr("id"); 
                    $("#remove-courses-"+id).remove(); 
                });

              $(".remove-org").on("click",function(){
                    var id = $(this).attr("id"); 
                    $("#remove-org-"+id).remove(); 
                });

               $(".remove-edu").on("click",function(){
                    var id = $(this).attr("id"); 
                    $("#remove-edu-"+id).remove(); 
                });

                 $(".remove-experience").on("click",function(){
                    var id = $(this).attr("id"); 
                    $("#remove-experience-"+id).remove(); 
                });

        }
       

    }
});