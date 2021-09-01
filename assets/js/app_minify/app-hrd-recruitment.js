define(["jquery","jquery-ui","highcharts"],function(a,b){return{baseUrl:a("#root_base_url").val(),serverBaseUrl:a("#server_base_url").val(),storeID:a("#store_id_config").val(),overlayUI:a("#cover"),init:function(){App.recruitment.initEvent()},initEvent:function(){console.log("RECRUITMENT"),App.recruitment.familyTableEvent(),App.recruitment.siblingsTableEvent(),App.recruitment.eduTableEvent(),App.recruitment.coursesTableEvent(),App.recruitment.exprerienceTableEvent(),App.recruitment.orgTableEvent(),a("#mate_birth_date").datetimepicker({sideBySide:!0,useCurrent:!0,format:"YYYY-MM-DD",widgetPositioning:{vertical:"bottom"}}),a("#report_attendance_start_date").datetimepicker({sideBySide:!0,useCurrent:!0,format:"YYYY-MM-DD"}),a("#report_attendance_end_date").datetimepicker({sideBySide:!0,useCurrent:!0,format:"YYYY-MM-DD"}),a("#report_payroll_start_period").datetimepicker({sideBySide:!0,useCurrent:!0,format:"MM-YYYY"}),a("#report_payroll_end_period").datetimepicker({sideBySide:!0,useCurrent:!0,format:"MM-YYYY"}),a("#report_appraisal_period").datetimepicker({sideBySide:!0,useCurrent:!0,format:"MM-YYYY"});a("#dataTables-recruitment").dataTable({bProcessing:!0,bServerSide:!0,scrollX:!0,sServerMethod:"POST",ajax:a("#dataProcessUrlRecruit").val(),iDisplayLength:10,columns:[{data:"name"},{data:"created_at"},{data:"phone_no"},{data:"job_apply"},{data:"actions"}],columnDefs:[{targets:[4],orderable:!1,bSearchable:!1,"class":"center-tr"}],order:[[0,"asc"]]});App.recruitment.removeHtmlEvent()},orgTableEvent:function(){var b=a("#dataTables-org").find("tbody").children().length;a("#add-org").on("click",function(){var c=App.recruitment.createOrgForm(b);a("#dataTables-org").find("tbody").append(c),App.recruitment.removeHtmlEvent(),App.validationInput(),b++})},createOrgForm:function(a){var b=' <tr id="remove-org-'+a+'"> <td ><input type="text" name="org['+a+'][experience_company]" class="form-control no-special-char"></td><td ><input type="text" name="org['+a+'][experience_period]" class="form-control "></td> <td ><input type="text" name="org['+a+'][experience_job]" class="form-control no-special-char"></td> <td ><input type="text" name="org['+a+'][experience_reason]" class="form-control no-special-char"></td> <td ><a  class="btn remove-org" id='+a+">X</a></td> </tr>";return b},exprerienceTableEvent:function(){var b=a("#dataTables-experience").find("tbody").children().length;a("#add-experience").on("click",function(){var c=App.recruitment.createExperienceForm(b);a("#dataTables-experience").find("tbody").append(c),App.recruitment.removeHtmlEvent(),App.validationInput(),b++})},createExperienceForm:function(a){var b=' <tr id="remove-experience-'+a+'"> <td ><input type="text" name="experience['+a+'][experience_company]" class="form-control no-special-char"></td><td ><input type="text" name="experience['+a+'][experience_period]" class="form-control "></td> <td ><input type="text" name="experience['+a+'][experience_job]" class="form-control no-special-char"></td> <td ><input type="text" name="experience['+a+'][experience_reason]" class="form-control no-special-char"></td> <td ><a  class="btn remove-experience" id='+a+">X</a></td> </tr>";return b},eduTableEvent:function(){var b=a("#dataTables-edu").find("tbody").children().length;a("#add-edu").on("click",function(){var c=App.recruitment.createEduForm(b);a("#dataTables-edu").find("tbody").append(c),App.recruitment.removeHtmlEvent(),App.validationInput(),b++})},createEduForm:function(a){var b=' <tr id="remove-edu-'+a+'"> <td ><input type="text" name="edu['+a+'][period]" class="form-control  qty-input"></td><td ><input type="text" name="edu['+a+'][school_name]" class="form-control no-special-char"></td> <td ><input type="text" name="edu['+a+'][city]" class="form-control no-special-char"></td> <td ><input type="text" name="edu['+a+'][legacy]" class="form-control no-special-char"></td> <td ><a  class="btn remove-edu" id='+a+">X</a></td> </tr>";return b},coursesTableEvent:function(){var b=a("#dataTables-courses").find("tbody").children().length;a("#add-courses").on("click",function(){var c=App.recruitment.createCourseForm(b);a("#dataTables-courses").find("tbody").append(c),App.recruitment.removeHtmlEvent(),App.validationInput(),b++})},createCourseForm:function(a){var b=' <tr id="remove-courses-'+a+'"> <td ><input type="text" name="courses['+a+'][course_name]" class="form-control no-special-char" ></td><td ><input type="text" name="courses['+a+'][course_time]" class="form-control" placeholder="2013-2014"></td> <td ><input type="text" name="courses['+a+'][course_place]" class="form-control no-special-char" placeholder="Bandung"></td> <td ><input type="text" name="courses['+a+'][course_description]" class="form-control no-special-char"></td> <td ><a  class="btn remove-courses" id='+a+">X</a></td> </tr>";return b},siblingsTableEvent:function(){var b=a("#dataTables-siblings-list").find("tbody").children().length;a("#add-siblings").on("click",function(){var c=App.recruitment.createSiblingsForm(b);a("#dataTables-siblings-list").find("tbody").append(c),App.recruitment.removeHtmlEvent(),App.validationInput(),b++})},createSiblingsForm:function(a){var b=' <tr id="remove-siblings-'+a+'"> <td ><input type="text" name="siblings['+a+'][name]" class="form-control no-special-char"></td><td ><input type="text" name="siblings['+a+'][status]" class="form-control no-special-char"></td> <td ><input type="text" name="siblings['+a+'][age]" class="form-control qty-input"></td> <td ><select name = "siblings['+a+'][education]" class="form-control"><option value="SD">SD</option><option value="SMP">SMP</option><option value="SMA">SMA</option><option value="D3">D3</option><option value="D4">D4</option><option value="S1">S1</option><option value="S2">S2</option><option value="S3">S3</option></select></td> <td ><a  class="btn remove-siblings" id='+a+">X</a></td> </tr>";return b},familyTableEvent:function(){var b=a("#dataTables-family-list").find("tbody").children().length;a("#add-family").on("click",function(){var c=App.recruitment.createFamilyForm(b);a("#dataTables-family-list").find("tbody").append(c),App.recruitment.removeHtmlEvent(),App.validationInput(),b++})},createFamilyForm:function(a){var b=' <tr id="remove-family-'+a+'"> <td ><input type="text" name="family['+a+'][name]" class="form-control no-special-char"></td><td ><input type="text" name="family['+a+'][age]" class="form-control qty-input"></td> <td ><select name = "family['+a+'][education]" class="form-control"><option value="SD">SD</option><option value="SMP">SMP</option><option value="SMA">SMA</option><option value="D3">D3</option><option value="D4">D4</option><option value="S1">S1</option><option value="S2">S2</option><option value="S3">S3</option></select></td> <td ><a  class="btn remove-family" id='+a+">X</a></td> </tr>";return b},removeHtmlEvent:function(){a(".remove-family").on("click",function(){var b=a(this).attr("id");a("#remove-family-"+b).remove()}),a(".remove-siblings").on("click",function(){var b=a(this).attr("id");a("#remove-siblings-"+b).remove()}),a(".remove-courses").on("click",function(){var b=a(this).attr("id");a("#remove-courses-"+b).remove()}),a(".remove-org").on("click",function(){var b=a(this).attr("id");a("#remove-org-"+b).remove()}),a(".remove-edu").on("click",function(){var b=a(this).attr("id");a("#remove-edu-"+b).remove()}),a(".remove-experience").on("click",function(){var b=a(this).attr("id");a("#remove-experience-"+b).remove()})}}});