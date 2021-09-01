define([
    "jquery",
    "jquery-ui",
    "bootstrap",
    'datatables',
    "datatables-bootstrap",
    "metisMenu",
    "chained",

], function ($, ui) {
    return {
        overlayUI         : $('#cover'),
        baseUrl                : $('#root_base_url').val(),
        
        init           : function () {
            App.overlayUI.show();
            App.initFunc(App);
            $('#side-menu').metisMenu();
            $("body").tooltip({ selector: '[data-tooltip=tooltip]' });
            $("#province_id_chained").chained("#country_id_chained");
            $("#city_id_chained").chained("#province_id_chained");
            $('#name').bind('keyup blur',function(){ 
              var node = $(this);
              node.val(node.val().replace(/[^a-zA-Z ]/g,'') ); }
            );
        	 $('#dataTables-member').dataTable({
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrl').val(),
                "iDisplayLength" : 10,
                "columns"        : [
                    
                    {data : "name"},
                    {data : "member_id"},
                    {data : "category_name"},
                    {data : "discount"},
                    {data : "join_date"},
                    {data : "last_transaction_date"},
                    {data : "total_spending"},
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
                "order"          : [[0, "desc"]]


            });
            var table =  $('#table-member-transaction').dataTable({
               "bProcessing"    : true,
               "bServerSide"    : true,
               "sServerMethod"  : "POST",
               "bFilter" :false,                   
               "bDestroy" :true,
               "autoWidth": false,
               "iDisplayLength" : 10,
               "ajax": {
                "url": $('#dataProcessUrl').val(),
                "type": 'POST'
              },

             "columns"        : [
                 {data : "payment_date"},
                 {data : "receipt_number"},
                 {data : "order_type"},
                 {data : "total_price_rp"},
                 {data : "quantity_order"},
                 {data : "customer_count"},
                 {data : "order_id"},
                 {data : "actions"}

             ],

             "columnDefs"     : [
                {
                    "targets"     : 6,
                    "orderable" : false
                }
            ],

            "order"          : [[0, "desc"]],
           



            }); //end datatable
           App.overlayUI.hide();

            $(".datepicker").datepicker({
            dateFormat     : 'yy-mm-dd',
            constrainInput: true,
            showOn: 'button',
            buttonImage: App.baseUrl+'assets/img/calendar_icon.png'   
        });

        }



    };
});