/**
 * Created by alta falconeri on 12/15/2014.
 */

var App;
if(!window.console) {
    var console = {
        log : function(){},
        warn : function(){},
        error : function(){},
        time : function(){},
        timeEnd : function(){}
    }
}
require.config({
    paths : {
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
        "highchart"            : "libs/highcharts"
    },
    shim  : {
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
        "chained"              : {
            deps    : ["jquery"],
            exports : "chained",
            init    : function () {
                console.log('chained inited..');
            }
        },
        "payment"              : {
            deps    : ["jquery"],
            exports : "payment",
            init    : function () {
                console.log('payment inited..');
            }
        },
        "highcharts"              : {
            deps    : ["jquery"],
            exports : "highcharts",
            init    : function () {
                console.log('highcharts inited..');
            }
        }
    }
});
require([ "./main-function","app_minify/app-bo-analytics"], function (func,application) {
    App = $.extend(application,func);
    App.init();
});
 