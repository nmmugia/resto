define(["jquery","jquery-ui"],function(a,b){return{jobsData:null!=document.getElementById("jobs_data")?JSON.parse(a("#jobs_data").val()):0,appraisalTemplates:null!=document.getElementById("appraisal_template_data")?JSON.parse(a("#appraisal_template_data").val()):0,init:function(){App.Appraisal.initEvent()},initEvent:function(){console.log("EVENT APPRAISAL"),App.Appraisal.templateAppraisalEvent(),App.Appraisal.processAppraisalEvent(),App.Appraisal.dueAppraisalEvent()},processAppraisalEvent:function(){a("#dataTables-app-process-list").dataTable({bProcessing:!0,bServerSide:!0,sServerMethod:"POST",ajax:a("#dataProcessUrlProcessAppraisal").val(),iDisplayLength:10,columns:[{data:"name"},{data:"period"},{data:"created_at"},{data:"template_name"},{data:"description"},{data:"actions"}],columnDefs:[{targets:5,orderable:!1,bSearchable:!1,"class":"center-tr"}],order:[[0,"desc"]]});a("#template_appraisal").change(function(){var b=App.baseUrl+"admincms/hrd_appraisal/download_template_appraisal",c=a(this).val();if(0==c)a("#category_list").empty();else{var d=a.ajax({type:"POST",url:b,data:{template_id:c}});d.done(function(b){a("#category_list").html(b),a("#max_grade_appraisal").html(a("#max_grade").val()),App.Appraisal.sliderEvent()}),d.fail(function(a,b){App.alert("Maaf, Data Template Appraisal. Silahkan Hubungi Administrator")}),d.always(function(){})}})},sliderEvent:function(){function b(){var b=0,c=parseInt(a("#max_grade_appraisal").html());a(".point").each(function(){b+=parseInt(a(this).val())});var d=parseInt(b)/c*100;a("#total_grade_appraisal").html(b),a("#total_precentage_appraisal").html(d.toFixed(2)+" %")}a(".slider-range-min-app").each(function(c){a(this).slider({range:"min",value:0,min:a(this).attr("min"),max:a(this).attr("max"),slide:function(b,c){a("#"+a(this).attr("child")).val(c.value)},stop:function(a,c){b()}}),a("#"+a(this).attr("child")).val(a("#slide-"+a(this).attr("child")).slider("value"))})},addCategoryAppEvent:function(){a(".remove_category").on("click",function(){var b=a(this).attr("id");a("#category_list").find("#category-"+b).remove(),console.log(a("#category_list").find("#category-"+b))})},addDetailCategoryAppEvent:function(){a(".add_appraisal_detail_category").on("click",function(){var b=a(this).attr("category-id"),c=1;c=a("#detail-container-"+b).children().length,App.Appraisal.createHtmlAppraisalDetailCategory(c,b)}),a(".remove_detail_category").on("click",function(){var b=a(this).attr("category-id"),c=a(this).attr("id");0!=parseInt(c)&&a("#category-"+b).find("#category-detail-"+c).remove()})},createHtmlAppraisalCategory:function(b){var c='<div class="col-lg-12 " id="category-'+b+'"><div class="panel panel-default"><div class="panel-heading"> <a class="btn remove_category" id="'+b+'"   >X</a></div><div class="panel-body"><div class="col-lg-12" > <div class="form-group"><label for="floor_name" class="col-sm-2 control-label">Nama Kategori</label> <div class="col-sm-8"><input type="text" name="category[]" class="form-control no-special-char"></div> </div>   <div class="col-lg-12" id="detail-container-'+b+'"></div>  </div><div class="col-lg-12"><div class="form-group">  <div class="col-sm-4 col-sm-offset-5"><a   category-id="'+b+'"    class="btn btn-default add_appraisal_detail_category">Tambah</a></div></div>    </div>    </div>    </div>    </div>    ';a("#category_list").append(c),a(".add_appraisal_category").unbind("click"),App.Appraisal.createHtmlAppraisalDetailCategory(0,b),App.Appraisal.addCategoryAppEvent(),App.validationInput()},createHtmlAppraisalDetailCategory:function(b,c){console.log(b);var d='<div class="panel panel-default" id="category-detail-'+b+'"><div class="panel-heading"> <a class="btn remove_detail_category" id="'+b+'"  category-id = '+c+' >X</a></div><div class="panel-body"><div class="col-lg-12"><div class="form-group"> <label for="floor_name" class="col-sm-2 control-label">Nama</label> <div class="col-sm-10"><input type="text" name="detail_category['+c+"]["+b+'][name]" class="form-control no-special-char"></div>  </div>    </div> <div class="col-lg-12"> <div class="form-group"> <label for="floor_name" class="col-sm-2 control-label">Maximal Nilai</label>  <div class="col-sm-10">    <input type="text" name="detail_category['+c+"]["+b+'][point]" class="form-control qty-input">  </div></div>     </div>   </div>    </div>   ';a(".add_appraisal_detail_category").unbind("click"),a("#detail-container-"+c).append(d),App.Appraisal.addDetailCategoryAppEvent(),App.validationInput()},templateAppraisalEvent:function(){var b=(a("#dataTables-template-appraisal-list").dataTable({bProcessing:!0,bServerSide:!0,sServerMethod:"POST",ajax:a("#dataProcessUrlTemplateAppraisal").val(),iDisplayLength:10,columns:[{data:"name"},{data:"description"},{data:"actions"}],columnDefs:[{targets:2,orderable:!1,bSearchable:!1,"class":"center-tr"}],order:[[0,"desc"]]}),1);a("#add_appraisal_category").on("click",function(){App.Appraisal.createHtmlAppraisalCategory(b),b++}),App.Appraisal.addDetailCategoryAppEvent()},createDropdown:function(a,b,c,d){for(var e='<select id="'+a+'" name="receiver['+a+"]["+d+']" field-name = "'+c+'" class="form-control requiredDropdown"  autocomplete="off">',f=0;f<b.length;f++)void 0==b[f].name&&(b[f].name=b[f].jobs_name),e+='<option value="'+b[f].id+'">'+b[f].name+"</option>";return e+="</select>"},dueAppraisalEvent:function(){var b=1;a("#add_receiver_appraisal").on("click",function(c){var d=App.Appraisal.createHtmlReceiver(b);a("#container-receiver").append(d),App.Appraisal.addEventRemove(),b++})},addEventRemove:function(){a(".remove-receiver").unbind("click"),a(".remove-receiver").on("click",function(b){var c=a(this).attr("id");a("#receiver-"+c).remove()})},createHtmlReceiver:function(a){var b='<div class="panel panel-default" id="receiver-'+a+'"><div class="panel-heading"> <a class="remove-receiver btn"  id='+a+' >X</a></div><div class="panel-body"><div class="form-group"> <label for="floor_name" class="col-sm-2 control-label">Jabatan</label>  <div class="col-sm-7">'+App.Appraisal.createDropdown(a,App.Appraisal.jobsData,"Jabatan","job")+'  </div> </div>  <div class="form-group"><label for="floor_name" class="col-sm-2 control-label">Template Appraisal</label> <div class="col-sm-7">'+App.Appraisal.createDropdown(a,App.Appraisal.appraisalTemplates,"Template","template")+" </div>  </div>   </div></div>";return b}}});