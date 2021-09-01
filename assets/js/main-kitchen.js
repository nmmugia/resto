var AppKitchen;
if (!window.console) {
    var console = {
        log     : function () {},
        warn    : function () {},
        error   : function () {},
        time    : function () {},
        timeEnd : function () {}
    }
}
require.config({
    paths   : {
        "jquery"               : "libs/jquery",
        "jquery-ui"            : "plugins/jquery-ui/jquery-ui.min",
        "bootstrap"            : "../bootstrap/js/bootstrap.min",
        "metisMenu"            : "plugins/metisMenu/metisMenu.min",
        "raphael"              : "plugins/morris/raphael.min",
        "Morris"               : "plugins/morris/morris.min",
        "blockUI"              : "libs/jquery.blockUI.min",
        "datatables"           : "plugins/dataTables/js/jquery.dataTables.min",
        "datatables-bootstrap" : "plugins/dataTables/js/dataTables.bootstrap",
        "chained"              : "libs/jquery.chained",
        "fabric"               : "libs/fabric.require",
        "payment"              : "libs/jquery.payment",
        "timepicker"           : "plugins/timepicker/jquery.timepicker.min",
        "datepair"             : "plugins/timepicker/lib/Datepair",
        "currency"             : "libs/jquery.formatCurrency-1.4.0",
        "paging"                : "plugins/jquery.simplePagination",
        "moment"                : "plugins/moment.min",
        "datetimepicker"       : "plugins/bootstrap-datetimepicker",
        "keyboard"                : "plugins/virtual-keyboard/jquery.keyboard",
        "multiselect"           : "plugins/multiselect.min",
        "easyautocomplete"       : "plugins/easyautocomplete/jquery.easy-autocomplete",
        "list"                  : "plugins/list.min",
        "highcharts"                  : "libs/highcharts",
        "html2canvas"           :"plugins/html2canvas",
        "rgbcolor"           :"plugins/rgbcolor",
        "canvg"           :"plugins/canvg",
        "html2canvassvg"           :"plugins/html2canvas.svg",
        "select2"             : "plugins/select2/select2.min",
        "qtip"             : "plugins/qtip2/jquery.qtip.min",
        "bootstrapToggle" : "plugins/bootstrap-toggle/bootstrap-toggle.min",
    }, 
    shim    : {
        "jquery-ui"            : {
            deps    : ["jquery"],
            exports : "jquery-ui",
            init    : function () {
                console.log('jquery-ui inited..');
            }
        },
        "bootstrap"            : {
            deps    : ["jquery"],
            exports : "bootstrap",
            init    : function () {
                console.log('bootstrap inited..');
            }
        },
        "metisMenu"            : {
            deps    : ["jquery"],
            exports : "metisMenu",
            init    : function () {
                console.log('metisMenu inited..');
            }
        },
        "Morris"               : {
            deps    : ["jquery", "raphael"],
            exports : "Morris",
            init    : function () {
                console.log('morris inited..');
            }
        },
        "datatables-bootstrap" : {
            deps    : ["jquery", "datatables"],
            exports : "datatables-bootstrap",
            init    : function () {
                console.log('datatables-bootstrap inited..');
            }
        },
        "datatables-tableTools" : {
            deps    : [""],
            exports : "datatables-tableTools",
            init    : function () {
                console.log('datatables-tableTools inited..');
            }
        },

        "chained"              : {
            deps    : ["jquery"],
            exports : "chained",
            init    : function () {
                console.log('chained asdinited..');
            }
        },
        "payment"              : {
            deps    : ["jquery"],
            exports : "payment",
            init    : function () {
                console.log('payment inited..');
            }
        },
        "timepicker"           : {
            deps    : ["jquery"],
            exports : "timepicker",
            init    : function () {
                console.log('timepicker inited..');
            }
        },
        "datepair"             : {
            deps    : ["jquery", "timepicker"],
            exports : "datepair",
            init    : function () {
                console.log('datepair inited..');
            }
        },
        "currency"              : {
            deps    : ["jquery"],
            exports : "currency",
            init    : function () {
                console.log('currency inited..');
            }
        },

        "paging"              : {
            deps    : ["jquery"],
            exports : "paging",
            init    : function () {
                console.log('paging inited..');
            }
        },

        "datetimepicker"              : {
            deps    : ["jquery", ''],
            exports : "datetimepicker",
            init    : function () {
                console.log('datetimepicker inited..');
            }
        },
         "keyboard"              : {
            deps    : ["jquery"],
            exports : "keyboard",
            init    : function () {
                console.log('keyboard inited..');
            }
        },
        "multiselect"              : {
            deps    : ["jquery"],
            exports : "multiselect",
            init    : function () {
                console.log('multiselect inited..');
            }
        },
        "easyautocomplete"              : {
            deps    : ["jquery"],
            exports : "easyautocomplete",
            init    : function () {
                console.log('aasyautocomplete inited..');
            }
        },
        "list"              : {
            deps    : ["jquery"],
            exports : "list",
            init    : function () {
                console.log('list inited..');
            }
        },
        "highcharts"              : {
            deps    : ["jquery"],
            exports : "highcharts",
            init    : function () {
                console.log('highcharts inited..');
            }
        },
        "html2canvas"              : {
            deps    : ["jquery"],
            exports : "html2canvas",
            init    : function () {
                console.log('html2canvas inited..');
            }
        },
        "rgbcolor"              : {
            deps    : ["jquery"],
            exports : "rgbcolor",
            init    : function () {
                console.log('rgbcolor inited..');
            }
        },
        "canvg"              : {
            deps    : ["rgbcolor"],
            exports : "canvg",
            init    : function () {
                console.log('canvg inited..');
            }
        },
        "html2canvassvg"              : {
            deps    : ["html2canvas"],
            exports : "html2canvassvg",
            init    : function () {
                console.log('html2canvassvg inited..');
            }
        },
        "select2"              : {
            deps    : ["jquery"],
            exports : "select2",
            init    : function () {
                console.log('select2 inited..');
            }
        },
        "qtip"              : {
            deps    : ["jquery"],
            exports : "qtip",
            init    : function () {
                console.log('qtip inited..');
            }
        },
        "bootstrapToggle" : {
            deps    : ["bootstrap"],
            exports : "bootstrapToggle",
            init    : function () {
                console.log('bootstrapToggle inited..');
            }
        },
    }
});
// require(["./common"], function (common) {
    require(["./main-function", "app_minify/app-kitchen", "app_minify/app-navigation"], function (func, appKitchen, nav) {
		AppNav = $.extend(nav,func);
		AppNav.init();
        AppKitchen = $.extend(appKitchen,func);
        AppKitchen.init();
    });
// });

requirejs.onError = function (err) {
    window.location.reload(true);
};
