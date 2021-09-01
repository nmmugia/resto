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
            
            App.groupsAccessProcess();
            App.sideBarProcess();

            App.overlayUI.hide();
             $('#side-menu').metisMenu();

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

        sideBarProcess: function(){

            var tableSide = $('#dataTables-sidebar').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : {
                    "url": $('#dataProcessUrl').val(),
                    "data": function ( d ) {
                        d.parent_id = $('#ddl_parent_menu').val();
   
                  },
              },

                "iDisplayLength" : 10,
                "bFilter": true,
                "sDom":"lrtip",
                "order": [[6,'asc']],
                "columns"        : [
                {data : "name"},
                {data : "url"},
                {data : "actions_sequence"},
                {data : "groups_access"},
                {data : "actions"},
                {data : "parent_id"},
                {data : "sequence"},

                ],
                "columnDefs"     : [
                {
                    "targets"     : 4,
                    "orderable"   : false,
                    "bSearchable" : false,
                    "class"       : 'center-tr'
                },
                {
                    "targets"     : 5,
                    "visible"   : false
                    
                },
                {
                    "targets"     : 6,
                    "visible"   : false
                    
                },
                {
                    "targets"     : 2,
                    "orderable"   : false,
                    "bSearchable" : false,
                    "class"       : 'center-tr'
                },

                ],


            });

            $('#ddl_parent_menu').change( function() { 
                tableSide.api().ajax.reload(); 

            });

            $('.multiselect').multiselect({
                keepRenderingSort: true
            });


            $('.form-ajax').on("submit", function (e) {
                $('#menu_access').val($('#multiselect_to_1').val());
                console.log($('#menu_access').val())
            });

            $('#dataTables-sidebar tbody').on('click', '.update-sequence', function (e) {
                App.overlayUI.show();
                
                var index = $(this).closest('tr').index();
                var parent_id = tableSide.fnGetData(index).parent_id;
                var sequence = tableSide.fnGetData(index).sequence;
              
                var request = $.ajax({
                    type    : 'POST',
                    url     : $(this).data('url'),
                    data: {
                        parent_id : parent_id,
                        sequence : sequence
                        }
                });
                request.done(function (resp) {
                    tableSide.api().ajax.reload( function ( json ) {
                        App.overlayUI.hide();
                    } );
                });

                request.fail(function (jqXHR, textStatus) {
                    window.location.reload(true);
                });

            });


        },// end sideBarProcess

    };
});