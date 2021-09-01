define([
    "jquery",
    "jquery-ui",
    "bootstrap",
    'datatables',
    "datatables-bootstrap",
    "metisMenu",

], function ($, ui) {
    return {
        overlayUI         : $('#cover'),
        baseUrl                : $('#root_base_url').val(),
        
        init           : function () {
            App.overlayUI.show();
            App.initFunc(App);
            $('#side-menu').metisMenu();
  
              /*$('#dataTables-reservation').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrl').val(),
                "iDisplayLength" : 10,
                "columns"        : [
                    
                    {data : "customer_name"},
                    {data : "phone"},
                    {data : "book_date"},
                    {data : "customer_count"},
                    {data : "book_note"},
                    {data : "table_name"},
                    {data : "down_payment"},
                    {data : "value"},
                    {data : "failed_note"},
                    {data : "actions"}
                    ],
                "columnDefs"     : [
                    {
                        "targets"     : 9,
                        "orderable"   : false,
                        "bSearchable" : false,
                        "class"       : 'center-tr'
                    }
                ],
                "order"          : [[0, "desc"]],
                "scrollX" : true


            });*/ 
            $('#dataTables-reservation-template-note').dataTable({
              "bProcessing"    : true,
              "bServerSide"    : true,
              "sServerMethod"  : "POST",
              "ajax"           : $('#dataProcessUrl').val(),
              "iDisplayLength" : 10,
              "columns"        : [
                  
                  {data : "id"},
                  {data : "template_name"},
                  {data : "note"},
                  {data : "actions"}
                ],
              "columnDefs"     : [
                {
                  "targets"     : [0,3],
                  "orderable"   : false,
                  "bSearchable" : false,
                  "class"       : 'center-tr'
                }
              ],
              "scrollX" : true
            });
            App.overlayUI.hide();

        }





    };
});