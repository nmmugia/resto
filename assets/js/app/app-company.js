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
            $('#company_name,#pic_name,#beneficary').bind('keyup blur',function(){ 
              var node = $(this);
              node.val(node.val().replace(/[^a-zA-Z ]/g,'') ); }
            );
            $('#down_payment,#land_phone,#mobile_phone').bind('keyup blur',function(){ 
              var node = $(this);
              node.val(node.val().replace(/[^0-9]/g,'') ); }
            );
             $('#dataTables-member').dataTable({
                "scrollX": true,
                "bProcessing"    : true,
                "bServerSide"    : true,
                "sServerMethod"  : "POST",
                "ajax"           : $('#dataProcessUrl').val(),
                "iDisplayLength" : 10,
                "columns"        : [
                    
                    {data : "store_name"},
                    {data : "company_name"},
                    {data : "pic_name"},
                    {data : "address"},
                    {data : "email"},
                    {data : "land_phone"},
                    {data : "mobile_phone"},
                    {data : "down_payment"},
                    {data : "no_rec"},
                    {data : "beneficary"},
                    {data : "is_use_banquet"},
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