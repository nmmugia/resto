/* 
* @Author: Fitria Kartika
* @Date:   2015-10-09 14:58:03
* @Last Modified by:   Fitria Kartika
* @Last Modified time: 2015-10-15 15:16:39
*/
define([
    "jquery",
    "jquery-ui",
    "bootstrap",
    'datatables',
    "datatables-bootstrap",
    "metisMenu",
    "multiselect"
], function ($, ui) {
    return {
        overlayUI         : $('#cover'),
        baseUrl                : $('#root_base_url').val(),
        
        init           : function () {
            
            App.overlayUI.show();
            App.initFunc(App);

            var printerType = $('#ddl_printer_type').val();
            //hide detail printer like font size and printer width if printer type is dot matrix (6,7,8)
            if(printerType == 6 || printerType == 7 || printerType == 8 || printerType == 9){
                $('#printer-detail').hide();
            } else {
                $('#printer-detail').show();

                //show list table if printer type is checker/service (3)
                if (printerType == 2 || printerType == 4) {
                    $('#outlet').show();
                } else {
                    $('#outlet').hide();
                }

                //show list table if printer type is checker/service (3)
                if (printerType == 3) {
                    $('#panel_store').show();
                } else {
                    $('#panel_store').hide();
                }
            }
            
            // App.groupsAccessProcess();
            App.settingPrinterProcess();

            App.overlayUI.hide();

            $(window).bind("load resize", function () {
                topOffset = 50;
                width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
                if (width < 768) {
                    $('div.navbar-collapse').addClass('collapse');
                    topOffset = 100; // 2-row-menu
                } else {
                    $('div.navbar-collapse').removeClass('collapse');
                }

                height = (this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height;
                height = height - topOffset;
                if (height < 1) height = 1;
                if (height > topOffset) {
                    $("#page-wrapper").css("min-height", (height) + "px");
                }
            });
 
        }, 
        groupsAccessProcess: function(){

            var tableGroups = $('#dataTables-groups').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrl').val(),
                "iDisplayLength" : 10,
                "columns"        : [
                {data : "id"},
                {data : "name"},
                {data : "description"},
                {data : "actions"},

                ],
                "columnDefs"     : [
                {
                    "targets"     : 3,
                    "orderable"   : false,
                    "bSearchable" : false,
                    "class"       : 'center-tr'
                },
                {
                    "targets": [ 0   ],
                    "visible": false
                },

                ],
                "order"          : [[1, "desc"]]


            });

            $('#dataTables-groups tbody').on('click', '.btn-edit', function () {
                var index = $(this).closest('tr').index();
                var id = tableGroups.fnGetData(index).id;
                var name = tableGroups.fnGetData(index).name;
                var description = tableGroups.fnGetData(index).description;
                $('#id').val(id) ;
                $('#name').val(name);
                $('#description').val(description);
            } );

            $('#dataTables-groups tbody').on('click', '.btn-feature', function () {
                var index = $(this).closest('tr').index();
                var id = tableGroups.fnGetData(index).id;
                  var url = $(this).data('url') 

                 var request = $.ajax({
                    type    : 'POST',
                    url     : url,
                });
                request.done(function (resp) {
                    $('#form-feature').attr('action', url); 
                    $('#modal-body-feature').html(resp);

                    App.groupFeatureProcess();

                });
                $('#form-feature').unbind();

            } );
            
            $('#modal-form').on('show.bs.modal', function (event) {
                  var button = $(event.relatedTarget) // Button that triggered the modal
                  var title = button.data('title') // Extract info from data-* attributes
                  var url = button.data('url') 

                  var modal = $(this)
                  modal.find('.modal-title').text(title)
                  $('form').attr('action', url); 
                  $('#modal-type').val(button.data('type')); 
              });

            $('#modal-form').on('hide.bs.modal', function (event) {
                $('.result').html('');
                $('#id').val('');
                $('#name').val('');
                $('#description').val('');
            });

            $('.result-detail').html('');

            $('#form-ajax').on("submit", function (e) {
             e.preventDefault();
             App.overlayUI.show();
                var request = $.ajax({
                    type    : 'POST',
                    url     : $('form').attr('action'),
                    data    : $('#form-ajax').serialize()
                });
                request.done(function (resp) {
                    if (resp != '') {
                        var parsedObject = JSON.parse(resp);
                        if(parsedObject.status == true){
                            tableGroups.api().ajax.reload();
                            $('#modal-form').modal('toggle');
                            App.messageSuccess(parsedObject.msg,'non');

                        }else{
                            App.messageError(parsedObject.msg,'popup');

                        }
                        
                         App.overlayUI.hide();
                    } else {
                        window.location.reload(true);
                    }
                });
            

            });


        },// end groupsAccessProcess

        groupFeatureProcess: function(){

            $('.multiselect').multiselect({
                keepRenderingSort: true
            });
                            
         $('#form-feature').on("submit", function (e) {
             e.preventDefault();

             $('#feature_access').val($('#multiselect_to_1').val());
             App.overlayUI.show();
                var request = $.ajax({
                    type    : 'POST',
                    url     : $('#form-feature').attr('action'),
                    data    : $('#form-feature').serialize()
                });
                request.done(function (resp) {
                    if (resp != '') {
                        var parsedObject = JSON.parse(resp);
                        if(parsedObject.status == true){
                            $('#modal-feature').modal('hide');
                            App.messageSuccess(parsedObject.msg,'non');

                        }else{
                            App.messageError(parsedObject.msg,'popup');

                        }
                         App.overlayUI.hide();
                    } else {
                        window.location.reload(true);
                    }
                });
            

            });
 
        },// end groupFeatureProcess

        settingPrinterProcess: function(){

            var tableSide = $('#dataTables-setting-printer').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : {
                    "url": $('#dataProcessUrl').val(),
                    "data": function ( d ) {
                        d.parent_id = $('#ddl_printer_type').val();
   
                  },
              },

                "iDisplayLength" : 10,
                "bFilter": true,
                "sDom":"lrtip",
                "columns"        : [
                {data : "name_printer"},
                {data : "alias_name"},
                {data : "type"},
                {data : "printer_width"},
                {data : "font_size"},
                {data : "actions"},

                ],
                "columnDefs"     : [
                {
                    "targets"     : 5,
                    "orderable"   : false,
                    "bSearchable" : false,
                    "class"       : 'center-tr'
                }

                ],


            });

            

            $('.multiselect').multiselect({
                keepRenderingSort: true
            });


            $('.form-ajax').on("submit", function (e) {
                $('#table_list').val($('#multiselect_to_1').val());
                console.log($('#table_list').val())
            });

            // $('#printer_type').on('change', function (e) {
            //     console.log($(this));
            // });
            $('#ddl_printer_type').on('change', function() {
                var printerType = $(this).val();
                //hide detail printer like font size and printer width if printer type is dot matrix (6,7,8)
                if(printerType == 6 || printerType == 7 || printerType == 8 || printerType == 9){
                    $('#printer-detail').hide();
                } else if (printerType == 1 || printerType == 5) {                    
                    $('#format_order').hide();
                } else {
                    $('#printer-detail').show();
                    $('#format_order').show();

                    //show list table if printer type is checker/service (3)
                    if (printerType == 2 || printerType == 4) {
                        $('#outlet').show();
                    } else {
                        $('#outlet').hide();
                    }

                    //show list table if printer type is checker/service (3)
                    if (printerType == 3) {
                        $('#panel_store').show();
                    } else {
                        $('#panel_store').hide();
                    }
                }
            });


        },// end sideBarProcess
    };
});