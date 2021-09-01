 define([
    "jquery",
    "jquery-ui", 
], function ($, ui) {
    return { 
        jobsData    : document.getElementById("jobs_data") != null ? JSON.parse($("#jobs_data").val()) : 0,
        appraisalTemplates    : document.getElementById("appraisal_template_data") != null ? JSON.parse($("#appraisal_template_data").val()) : 0,
        init                   : function () { 
            App.Appraisal.initEvent();   
        },
                 
        initEvent:function(){
            console.log("EVENT APPRAISAL"); 
            App.Appraisal.templateAppraisalEvent(); 
            App.Appraisal.processAppraisalEvent();
            App.Appraisal.dueAppraisalEvent();
        },
        processAppraisalEvent:function(){
            var repaymentDatatable =  $('#dataTables-app-process-list').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true, 
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrlProcessAppraisal').val(),
                "iDisplayLength" : 10,
                "columns"        : [  
                     {data : "name"}, 
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

            $('#template_appraisal').change(function() { 
                var url = App.baseUrl+"admincms/hrd_appraisal/download_template_appraisal";
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
                        $("#max_grade_appraisal").html($("#max_grade").val());
                        App.Appraisal.sliderEvent();        
                    });
                    request.fail(function (jqXHR, textStatus) { 
                        App.alert("Maaf, Data Template Appraisal. Silahkan Hubungi Administrator");
                    });
                    request.always(function () {
                    }); 
                } 
            });   
        },
        sliderEvent:function(){ 
            $(".slider-range-min-app").each(function(total) { 

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
                var max_grade_audit = parseInt($("#max_grade_appraisal").html());
                $(".point").each(function() { 
                    total += parseInt($(this).val());
                }); 
                

                var percentage = parseInt(total)/max_grade_audit * 100;

                $("#total_grade_appraisal").html(total);   
                $("#total_precentage_appraisal").html(percentage.toFixed(2)+" %");
            }    
        },   
        addCategoryAppEvent:function(){ 
            $(".remove_category").on("click",function(){
                var id = $(this).attr("id"); 
                
                $("#category_list").find("#category-"+id).remove();
                console.log($("#category_list").find("#category-"+id));
            });
        },
        addDetailCategoryAppEvent:function(){ 
            $(".add_appraisal_detail_category").on("click",function(){  
                var category_id = $(this).attr("category-id");
                var indexDetailCategory = 1;
                indexDetailCategory = $("#detail-container-"+category_id).children().length;
                App.Appraisal.createHtmlAppraisalDetailCategory(indexDetailCategory,category_id);
                 
            }); 

            $(".remove_detail_category").on("click",function(){   
                 var category_id = $(this).attr("category-id");
                 var id = $(this).attr("id");
                 if(parseInt(id) == 0) return;
                 $("#category-"+category_id).find("#category-detail-"+id).remove();
            }); 
        }, 
        createHtmlAppraisalCategory:function(index){
            var html = 
            '<div class="col-lg-12 " id="category-'+index+'">'+ 
              '<div class="panel panel-default">'+
                '<div class="panel-heading"> '+
                    '<a class="btn remove_category" id="'+index+'"   >X</a>'+
                '</div>'+
                '<div class="panel-body">'+
                        '<div class="col-lg-12" > '+
                            '<div class="form-group">'+
                                '<label for="floor_name" class="col-sm-2 control-label">Nama Kategori</label> '+
                                '<div class="col-sm-8">'+
                                    '<input type="text" name="category[]" class="form-control no-special-char">'+
                                '</div>'+
                           ' </div>   '+
                            '<div class="col-lg-12" id="detail-container-'+index+'">'+
                                
                            '</div> '+  
                       ' </div>'+
                       '<div class="col-lg-12">'+
                            '<div class="form-group"> '+
                               ' <div class="col-sm-4 col-sm-offset-5">'+
                                     '<a   category-id="'+index+'"    class="btn btn-default add_appraisal_detail_category">Tambah</a>'+
                                '</div>'+
                            '</div>    '+
                       '</div>    '+
                     '</div>    '+
                 '</div>    '+
             '</div>    ';

            $("#category_list").append(html);
            $(".add_appraisal_category").unbind("click"); 

            App.Appraisal.createHtmlAppraisalDetailCategory(0,index); 
            App.Appraisal.addCategoryAppEvent();  
            App.validationInput();
        },
        createHtmlAppraisalDetailCategory:function(index,category_id){  
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
                           ' <label for="floor_name" class="col-sm-2 control-label">Maximal Nilai</label> '+
                           ' <div class="col-sm-10">'+
                             '    <input type="text" name="detail_category['+category_id+']['+index+'][point]" class="form-control qty-input">'+
                          '  </div>'+
                        '</div>    '+ 
                   ' </div> '+
              '  </div>   '+
           ' </div>   ';
            $(".add_appraisal_detail_category").unbind("click");
           $("#detail-container-"+category_id).append(html);
           App.Appraisal.addDetailCategoryAppEvent();

            App.validationInput();
        },
  
        templateAppraisalEvent:function(){
            var repaymentDatatable =  $('#dataTables-template-appraisal-list').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true, 
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrlTemplateAppraisal').val(),
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
            $("#add_appraisal_category").on("click",function(){
                App.Appraisal.createHtmlAppraisalCategory(indexCategory);
                indexCategory++;
            });
            App.Appraisal.addDetailCategoryAppEvent();
           
        },
        createDropdown:function(id,data,fieldName,fieldVariable){
            var appendOpt = '<select id="' + id + '" name="receiver['+id+']['+fieldVariable+']" field-name = "'+fieldName+'" class="form-control requiredDropdown"  autocomplete="off">';
        
            for (var i = 0; i < data.length; i++) { 
                if(data[i].name == undefined) data[i].name = data[i].jobs_name;
                appendOpt += '<option value="' + data[i].id + '">' + data[i].name + '</option>' ;
            };
            appendOpt += '</select>';

           
            return appendOpt;
        }, 
        dueAppraisalEvent:function(){
            var index = 1;
            $('#add_receiver_appraisal').on('click', function (e) {
                 var html = App.Appraisal.createHtmlReceiver(index);

                 $("#container-receiver").append(html);
                  App.Appraisal.addEventRemove();
                 index++;
            });

           
        },
        addEventRemove:function(){
            $(".remove-receiver").unbind("click"); 
            $('.remove-receiver').on('click', function (e) {
                  var id = $(this).attr('id'); 
                  $("#receiver-"+id).remove();
            });
        },
        createHtmlReceiver:function(index){
            var html = '<div class="panel panel-default" id="receiver-'+index+'">'+
                '<div class="panel-heading"> '+
                   '<a class="remove-receiver btn"  id='+index+' >X</a>'+
                '</div>'+
                '<div class="panel-body">'+
                    '<div class="form-group">'+
                       ' <label for="floor_name" class="col-sm-2 control-label">Jabatan</label> '+
                       ' <div class="col-sm-7">'
                        + App.Appraisal.createDropdown(index,App.Appraisal.jobsData,"Jabatan","job")+ 
                      '  </div>'+
                   ' </div>  '+
                    '<div class="form-group">'+
                        '<label for="floor_name" class="col-sm-2 control-label">Template Appraisal</label> '+
                       '<div class="col-sm-7">' 
                            + App.Appraisal.createDropdown(index,App.Appraisal.appraisalTemplates,"Template","template")+ 
                       ' </div>'+
                  '  </div>  '+
               ' </div>'+
            '</div>';

            return html;
        }

    }
});