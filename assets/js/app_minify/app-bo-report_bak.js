define(["jquery","jquery-ui","chained","metisMenu","Morris","datatables","bootstrap","datatables-bootstrap","paging"],function(a,b){return{baseUrl:a("#root_base_url").val(),adminUrl:a("#admin_url").val(),overlayUI:a("#cover"),reportType:a("#report_type").val(),sidedishCount:null!=document.getElementById("sidedishCount")?document.getElementById("sidedishCount").value:0,optionsCount:null!=document.getElementById("optionsCount")?document.getElementById("optionsCount").value:0,optionsValueCount:null!=document.getElementById("optionsValueCount")?document.getElementById("optionsValueCount").value:0,init:function(){App.overlayUI.hide(),App.initFunc(App),a("#side-menu").metisMenu(),a(".date-input").datepicker({dateFormat:"yy-mm-dd"}),a(".date-input").val(""),a(".def-select").val("0"),Array.prototype.unique=function(){for(var a=this.concat(),b=0;b<a.length;++b)for(var c=b+1;c<a.length;++c)a[b]===a[c]&&a.splice(c--,1);return a},"sales"==App.reportType?App.initSalesUI():"customer"==App.reportType?(a("#table_sel").val("all"),App.initCustomerUI()):"store"==App.reportType?App.initStoreUI():"staff"==App.reportType?App.initStaffUI():"historyStock"==App.reportType?App.iniHistoryStockUI():"ingredient"==App.reportType?App.initIngredientUI():"menu"==App.reportType?App.initMenuUI():"stock_opname"==App.reportType&&App.initStockOpnameUI()},initSalesUI:function(){a("#filter_submit").on("click",function(){a.ajax({url:App.adminUrl+"/reports/get_sales_data",type:"POST",dataType:"json",data:{start_date:a("#start_date").val(),end_date:a("#end_date").val(),month:a("#month_sel").val(),year:a("#year_sel").val(),store:a("#store_sel").val(),outlet:a("#outlet_sel").val(),payment_method:a("#payment_method").val()},success:function(b){if(console.log(b),null!=b.data){a(".sales-table").find("tr:gt(0)").remove();var c=0,d=0,e=0,f=[],g=[],h=0,j=0;for(a("#table-sales-hidden").attr("border","1"),a("#start_date_hidden").text(a("#start_date").val()),a("#end_date_hidden").text(a("#end_date").val()),a("#month_hidden").text(a("#month_sel option:selected").text()),a("#year_hidden").text(a("#year_sel option:selected").text()),a("#store_hidden").text(a("#store_sel option:selected").text()),a("#outlet_hidden").text(a("#outlet_sel option:selected").text()),a(".hide_btn").show(),i=0;i<b.data.length;i++)"0"!=a("#month_sel").val()||"0"!=a("#year_sel").val()?(a(".hideme").hide(),a(".showme").show(),a(".custom-hidden-table").html('<tr><th colspan="2">Tanggal Penjualan</th><th>Jumlah Item</th><th>Total</th><th>Resto</th><th>Outlet</th></tr>'),a(".sales-table").append(a("<tr></tr>").append(a("<td colspan='2'>"+b.data[i].order_date+"</td>")).append(a("<td>"+b.data[i].item_count+"</td>")).append(a("<td>"+b.data[i].total_price_str+"</td>")).append(a("<td>"+b.data[i].stores.join(",")+"</td>")).append(a("<td>"+b.data[i].outlets.join(",")+"</td>")).append(a("<td>"+b.data[i].payment_method+"</td>")))):(a(".hideme").show(),a(".showme").hide(),a(".custom-hidden-table").html("<tr><th>Nomor Nota</th><th>Tanggal Penjualan</th><th>Jumlah Item</th><th>Total</th><th>Resto</th><th>Outlet</th><th>Metode Pembayaran</th></tr>"),a(".sales-table").append(a("<tr></tr>").append(a("<td>"+b.data[i].receipt_id+"</td>")).append(a("<td>"+b.data[i].order_date+"</td>")).append(a("<td>"+b.data[i].item_count+"</td>")).append(a("<td>"+b.data[i].total_price_str+"</td>")).append(a("<td>"+b.data[i].stores.join(",")+"</td>")).append(a("<td>"+b.data[i].outlets.join(",")+"</td>")).append(a("<td>"+b.data[i].payment_method+"</td>")).append(a('<td><div class="btn-group"><a class="btn btn-default" href="'+App.adminUrl+"/reports/get_sales_detail/"+b.data[i].id+'" target="_blank"><i class="fa fa-search"></i></a></div></td>')))),c+=b.data[i].item_count,h+=b.data[i].takeaway_count,j+=b.data[i].dinein_count,d+=parseInt(b.data[i].total_price),e+=parseInt(b.data[i].menu_hpp)+parseInt(b.data[i].side_dish_hpp),f=f.concat(b.data[i].stores).unique(),g=g.concat(b.data[i].outlets).unique();0==b.data.length?a(".sales-table").append(a('<tr><th colspan="8" align="center" valign="middle" >No Data</th></tr>')):("0"!=a("#month_sel").val()||"0"!=a("#year_sel").val()?a(".sales-table").append(a("<tr></tr>").append(a("<td colspan='2'><b>Total</b></td>")).append(a("<td><b>"+c+" Item</b></td>")).append(a("<td><b>Rp "+App.addCommas(d)+"</b></td>")).append(a("<td><b>"+f.length+" Resto</b></td>")).append(a("<td><b>"+g.length+" Outlets</b></td>")).append(a("<td></td>"))):a(".sales-table").append(a("<tr></tr>").append(a("<td><b>Total</b></td>")).append(a("<td><b>"+b.sales_count+" Sales</b></td>")).append(a("<td><b>"+c+" Item</b></td>")).append(a("<td><b>Rp "+App.addCommas(d)+"</b></td>")).append(a("<td><b>"+f.length+" Resto</b></td>")).append(a("<td><b>"+g.length+" Outlets</b></td>")).append(a("<td></td>"))),a(".sales-table").append(a("<tr></tr>").append(a("<td>Gross Expenses</td>")).append(a("<td colspan=6><b>Rp "+App.addCommas(e)+"</b></td>"))),a(".sales-table").append(a("<tr></tr>").append(a("<td>Gross Revenue</td>")).append(a("<td colspan=6><b>Rp "+App.addCommas(d)+"</b></td>"))),a(".sales-table").append(a("<tr></tr>").append(a("<td>Gross Profit</td>")).append(a("<td colspan=6><b>Rp "+App.addCommas(d-e)+"</b></td>"))),a(".sales-table").append(a("<tr></tr>").append(a("<td>Total Dine In</td>")).append(a("<td colspan=6><b>"+j+"</b></td>"))),a(".sales-table").append(a("<tr></tr>").append(a("<td>Total Take Away</td>")).append(a("<td colspan=6><b>"+h+"</b></td>"))))}else alert(b.message)}})}),a("#export_pdf").on("click",function(){a.ajax({url:App.adminUrl+"/reports/export_report_to_pdf",type:"POST",dataType:"json",data:{html_string:a(".panel-hidden").html(),report:"sales"},success:function(a){console.log(App.baseUrl+a),""!=a?window.open(App.baseUrl+a,"_newtab"):alert("Export report gagal")}})}),a("#export_xls").on("click",function(){a.ajax({url:App.adminUrl+"/reports/export_report_to_xls",type:"POST",dataType:"json",data:{html_string:a(".panel-hidden").html(),report:"sales"},success:function(a){console.log(App.baseUrl+a),""!=a?window.open(App.baseUrl+a,"_newtab"):alert("Export report gagal")}})})},initCustomerUI:function(){a("#filter_submit").on("click",function(){a.ajax({url:App.adminUrl+"/reports/get_customer_data",type:"POST",dataType:"json",data:{start_date:a("#start_date").val(),end_date:a("#end_date").val(),month:a("#month_sel").val(),year:a("#year_sel").val(),store:a("#store_sel").val(),outlet:a("#outlet_sel").val(),table:a("#table_sel").val()},success:function(b){if(null!=b.data){a(".sales-table").find("tr:gt(0)").remove();var c=0,d=0,e=0,f=0,g=0,h=0,k=[],l=[],m="",n="";for(a("#table-sales-hidden").attr("border","1"),a("#start_date_hidden").text(a("#start_date").val()),a("#end_date_hidden").text(a("#end_date").val()),a("#month_hidden").text(a("#month_sel option:selected").text()),a("#year_hidden").text(a("#year_sel option:selected").text()),a("#store_hidden").text(a("#store_sel option:selected").text()),a("#outlet_hidden").text(a("#outlet_sel option:selected").text()),a("#table_hidden").text(a("#table_sel option:selected").text()),a(".hide_btn").show(),i=0;i<b.data.length;i++){if("Takeaway"==b.data[i].order_type)d+=0,m="",n="";else{for(j=0;j<b.data[i].order_start.length;j++){var o=b.data[i].order_start[j],p=o.substr(0,10)+"T"+o.substr(11,8),q=new Date(p),o=b.data[i].order_ends[j],p=o.substr(0,10)+"T"+o.substr(11,8),r=new Date(p),s=0;s=isNaN(r)?0:Math.abs(r.getTime()-q.getTime()),d+=s,timeStayDate=new Date(s);var t=timeStayDate.getUTCHours(),u=timeStayDate.getUTCMinutes();e+=t,f+=u}m=timeStayDate.getUTCHours(),n=timeStayDate.getUTCMinutes(),m=0==timeStayDate.getUTCHours()?"":timeStayDate.getUTCHours()+" jam ",n=0==timeStayDate.getUTCMinutes()?"":timeStayDate.getUTCMinutes()+" menit"}"0"!=a("#month_sel").val()||"0"!=a("#year_sel").val()?(a(".hideme").hide(),a(".showme").show(),a(".custom-hidden-table").html("<tr><th>Tanggal</th><th>Jumlah Tamu</th><th>Lama Duduk</th><th>Jumlah Item</th><th>Resto</th><th>Outlet</th></tr>"),a(".sales-table").append(a("<tr></tr>").append(a("<td>"+b.data[i].order_date+"</td>")).append(a("<td>"+b.data[i].guest_count+"</td>")).append(a("<td>"+m+n+"</td>")).append(a("<td>"+b.data[i].item_count+"</td>")).append(a("<td>"+b.data[i].stores.join(",")+"</td>")).append(a("<td>"+b.data[i].outlets.join(",")+"</td>")))):(a(".hideme").show(),a(".showme").hide(),a(".custom-hidden-table").html("<tr><th>Nomor Nota</th><th>Tanggal</th><th>Nama Meja</th><th>Jumlah Tamu</th><th>Lama Duduk</th><th>Jumlah Item</th><th>Resto</th><th>Outlet</th></tr>"),a(".sales-table").append(a("<tr></tr>").append(a("<td>"+b.data[i].receipt_id+"</td>")).append(a("<td>"+b.data[i].order_date+"</td>")).append(a("<td>"+b.data[i].table_name+"</td>")).append(a("<td>"+b.data[i].guest_count+"</td>")).append(a("<td>"+m+n+"</td>")).append(a("<td>"+b.data[i].item_count+"</td>")).append(a("<td>"+b.data[i].stores.join(",")+"</td>")).append(a("<td>"+b.data[i].outlets.join(",")+"</td>")))),c+=b.data[i].item_count,h+=parseInt(b.data[i].total_price),g+=parseInt(b.data[i].guest_count),k=k.concat(b.data[i].stores).unique(),l=l.concat(b.data[i].outlets).unique()}if(0==b.data.length)a(".sales-table").append(a('<tr><th colspan="6" align="center" valign="middle" >No Data</th></tr>'));else{var v="",w="",x=0,y=0,z=36e5*e+6e4*f;if(0!=d){var A=Math.floor((z%=86400)/3600),B=Math.floor((z%=3600)/60);new Date(d);w=A+" Jam, "+B+" Menit";var C=d/g,D=(parseInt(C%1e3/100),parseInt(C/1e3%60),parseInt(C/6e4%60)),E=parseInt(C/36e5%24);y=E+" jam "+D+" Menit",v=""}else w="0 Jam, 0 Menit",y=0,v=" Menit";if(""!=a("#start_date").val()&&""!=a("#end_date").val()){var F=new Date(a("#start_date").val()),G=new Date(a("#end_date").val());if(G>=F)if(F.getTime()==G.getTime())x=1;else{var H=864e5;x=Math.abs((G.getTime()-F.getTime())/H)}}else""!=a("#month_sel").val()?x=30:""!=a("#year_sel").val()&&(x=365);"0"!=a("#month_sel").val()||"0"!=a("#year_sel").val()?a(".sales-table").append(a("<tr></tr>").append(a("<td><b>Total</b></td>")).append(a("<td><b>"+g+" Tamu</b></td>")).append(a("<td><b>"+w+"</b></td>")).append(a("<td><b>"+c+" Item</b></td>")).append(a("<td><b>"+k.length+" Resto</b></td>")).append(a("<td><b>"+l.length+" Outlets</b></td>"))):a(".sales-table").append(a("<tr></tr>").append(a("<td><b>Total</b></td>")).append(a("<td><b></b></td>")).append(a("<td><b></b></td>")).append(a("<td><b>"+g+" Tamu</b></td>")).append(a("<td><b>"+w+"</b></td>")).append(a("<td><b>"+c+" Item</b></td>")).append(a("<td><b>"+k.length+" Resto</b></td>")).append(a("<td><b>"+l.length+" Outlets</b></td>"))),a(".sales-table").append(a("<tr></tr>").append(a("<td>Pengunjung Per Jam</td>")).append(a("<td colspan=5><b>"+Math.round(g/x/12*1)/1+" Pengunjung</b></td>"))),a(".sales-table").append(a("<tr></tr>").append(a("<td>Item Per Pengunjung</td>")).append(a("<td colspan=5><b>"+Math.round(c/g*1)/1+" Item</b></td>"))),a(".sales-table").append(a("<tr></tr>").append(a("<td>Rata-rata Duduk Pengunjung</td>")).append(a("<td colspan=5><b>"+y+v+"</b></td>"))),a(".sales-table").append(a("<tr></tr>").append(a("<td>Total Revenue Per Pengunjung</td>")).append(a("<td colspan=5><b>Rp "+App.addCommas(Math.round(h/g*1)/1)+"</b></td>")))}}else alert(b.message)}})}),a("#export_pdf").on("click",function(){a.ajax({url:App.adminUrl+"/reports/export_report_to_pdf",type:"POST",dataType:"json",data:{html_string:a(".panel-hidden").html(),report:"customer"},success:function(a){console.log(App.baseUrl+a),""!=a?window.open(App.baseUrl+a,"_newtab"):alert("Export report gagal")}})}),a("#export_xls").on("click",function(){a.ajax({url:App.adminUrl+"/reports/export_report_to_xls",type:"POST",dataType:"json",data:{html_string:a(".panel-hidden").html(),report:"customer"},success:function(a){console.log(App.baseUrl+a),""!=a?window.open(App.baseUrl+a,"_newtab"):alert("Export report gagal")}})})},iniHistoryStockUI:function(){a("#filter_submit").on("click",function(){a.ajax({url:App.adminUrl+"/inventory/get_history_data",type:"POST",dataType:"json",data:{start_date:a("#start_date").val(),end_date:a("#end_date").val(),month:a("#month_sel").val(),year:a("#year_sel").val(),store:a("#store_sel").val(),outlet:a("#outlet_sel").val()},success:function(b){if(console.log(b),null!=b.data){for(a(".history-stock-table").find("tr:gt(0)").remove(),a("#table-history-stock-hidden").attr("border","1"),a("#start_date_hidden").text(a("#start_date").val()),a("#end_date_hidden").text(a("#end_date").val()),a("#month_hidden").text(a("#month_sel option:selected").text()),a("#year_hidden").text(a("#year_sel option:selected").text()),a("#outlet_hidden").text(a("#outlet_sel option:selected").text()),a(".hide_btn").show(),i=0;i<b.data.length;i++)a(".showme").show(),a(".custom-hidden-table").html('<tr><th colspan="2">Tanggal Penjualan</th><th>Nama bahan</th><th>Outlet</th></tr>'),a(".history-stock-table").append(a("<tr></tr>").append(a("<td colspan='1'>"+b.data[i].date+"</td>")).append(a("<td>"+b.data[i].outlet_name+"</td>")).append(a("<td>"+b.data[i].name+"</td>")).append(a("<td>"+b.data[i].total+"</td>")).append(a("<td>"+b.data[i].unit+"</td>")));0==b.data.length&&a(".history-stock-table").append(a('<tr><th colspan="6" align="center" valign="middle" >No Data</th></tr>'))}else alert(b.message)}})})},initMenuUI:function(){a("#filter_submit").on("click",function(){a.ajax({url:App.adminUrl+"/reports/get_menu_data",type:"POST",dataType:"json",data:{start_date:a("#start_date").val(),end_date:a("#end_date").val(),month:a("#month_sel").val(),year:a("#year_sel").val(),store:a("#store_sel").val(),outlet:a("#outlet_sel").val()},success:function(b){if(console.log(b.data),null!=b.data){a(".sales-table").find("tr:gt(0)").remove();var c=0,d=0,e=0,f=[],g=0,h=0;for(a("#table-sales-hidden").attr("border","1"),a("#start_date_hidden").text(a("#start_date").val()),a("#end_date_hidden").text(a("#end_date").val()),a("#month_hidden").text(a("#month_sel option:selected").text()),a("#year_hidden").text(a("#year_sel option:selected").text()),a("#store_hidden").text(a("#store_sel option:selected").text()),a("#outlet_hidden").text(a("#outlet_sel option:selected").text()),a(".hide_btn").show(),i=0;i<b.data.length;i++)a(".hideme").hide(),a(".showme").show(),a(".custom-hidden-table").html("<th>Resto</th><tr>Outlet</th><th>Menu</th><th>Jumlah Terjual</th><th>Profit per item</th><th>Total Profit</th></tr>"),a(".sales-table").append(a("<tr></tr>").append(a("<td>"+b.data[i].store_name+"</td>")).append(a("<td>"+b.data[i].outlet_name+"</td>")).append(a("<td>"+b.data[i].menu_name+"</td>")).append(a("<td>"+b.data[i].menu_count+"</td>")).append(a("<td>Rp "+App.addCommas(b.data[i].gross_profit_item)+"</td>")).append(a("<td>Rp "+App.addCommas(b.data[i].gross_profit)+"</td>"))),c+=b.data[i].menu_count,f=f.concat(b.data[i].stores).unique();var j=0;0==b.data.length?a(".sales-table").append(a('<tr><th colspan="6" align="center" valign="middle" >No Data</th></tr>')):(a(".sales-table").append(a("<tr></tr>").append(a("<td><b>Total</b></td>")).append(a("<td><b>"+j+" Item</b></td>")).append(a("<td><b>Rp "+c+"</b></td>")).append(a("<td><b>"+f.length+" Resto</b></td>")).append(a("<td><b>"+f.length+" Outlets</b></td>")).append(a("<td></td>"))),a(".sales-table").append(a("<tr></tr>").append(a("<td>Gross Expenses</td>")).append(a("<td colspan=5><b>Rp "+App.addCommas(e)+"</b></td>"))),a(".sales-table").append(a("<tr></tr>").append(a("<td>Gross Revenue</td>")).append(a("<td colspan=5><b>Rp "+App.addCommas(d)+"</b></td>"))),a(".sales-table").append(a("<tr></tr>").append(a("<td>Gross Profit</td>")).append(a("<td colspan=5><b>Rp "+App.addCommas(d-e)+"</b></td>"))),a(".sales-table").append(a("<tr></tr>").append(a("<td>Total Dine In</td>")).append(a("<td colspan=5><b>"+h+"</b></td>"))),a(".sales-table").append(a("<tr></tr>").append(a("<td>Total Take Away</td>")).append(a("<td colspan=5><b>"+g+"</b></td>"))))}else alert(b.message)}})}),a("#export_pdf").on("click",function(){a.ajax({url:App.adminUrl+"/reports/export_report_to_pdf",type:"POST",dataType:"json",data:{html_string:a(".panel-hidden").html(),report:"menu"},success:function(a){console.log(App.baseUrl+a),""!=a?window.open(App.baseUrl+a,"_newtab"):alert("Export report gagal")}})}),a("#export_xls").on("click",function(){a.ajax({url:App.adminUrl+"/reports/export_report_to_xls",type:"POST",dataType:"json",data:{html_string:a(".panel-hidden").html(),report:"menu"},success:function(a){console.log(App.baseUrl+a),""!=a?window.open(App.baseUrl+a,"_newtab"):alert("Export report gagal")}})})},initStockOpnameUI:function(){a("#filter_submit").on("click",function(){a.ajax({url:App.adminUrl+"/reports/get_stock_opname_data",type:"POST",dataType:"json",data:{start_date:a("#start_date").val(),end_date:a("#end_date").val(),month:a("#month_sel").val(),year:a("#year_sel").val(),outlet:a("#outlet_sel").val()},success:function(b){if(null!=b.data){a(".sales-table").find("tr:gt(0)").remove();var c=0,d=0,e=0,f=0,g=[];for(a("#table-sales-hidden").attr("border","1"),a("#start_date_hidden").text(a("#start_date").val()),a("#end_date_hidden").text(a("#end_date").val()),a("#month_hidden").text(a("#month_sel option:selected").text()),a("#year_hidden").text(a("#year_sel option:selected").text()),a("#outlet_hidden").text(a("#outlet_sel option:selected").text()),a(".hide_btn").show(),i=0;i<b.data.length;i++){parseInt(b.data[i].menu_hpp)+parseInt(b.data[i].side_dish_hpp),parseInt(b.data[i].total_price);a(".sales-table").append(a("<tr></tr>").append(a("<td>"+b.data[i].order_date+"</td>")).append(a("<td>"+b.data[i].outlet_name+"</td>")).append(a("<td>"+b.data[i].inventory_name+"</td>")).append(a("<td>"+b.data[i].total_used+"</td>")).append(a("<td>"+b.data[i].total_stock+"</td>")).append(a("<td>"+b.data[i].unit+"</td>"))),c+=b.data[i].item_count,d+=parseInt(b.data[i].guest_count),e+=parseInt(b.data[i].total_price),f+=parseInt(b.data[i].menu_hpp)+parseInt(b.data[i].side_dish_hpp),g=g.concat(b.data[i].stores).unique()}0==b.data.length&&a(".sales-table").append(a('<tr><th colspan="6" align="center" valign="middle" >No Data</th></tr>'))}else alert(b.message)}})}),a("#export_pdf").on("click",function(){a.ajax({url:App.adminUrl+"/reports/export_report_to_pdf",type:"POST",dataType:"json",data:{html_string:a(".panel-hidden").html(),report:"store"},success:function(a){console.log(App.baseUrl+a),""!=a?window.open(App.baseUrl+a,"_newtab"):alert("Export report gagal")}})}),a("#export_xls").on("click",function(){a.ajax({url:App.adminUrl+"/reports/export_report_to_xls",type:"POST",dataType:"json",data:{html_string:a(".panel-hidden").html(),report:"store"},success:function(a){console.log(App.baseUrl+a),""!=a?window.open(App.baseUrl+a,"_newtab"):alert("Export report gagal")}})})},addCommas:function(a){a+="",x=a.split("."),x1=x[0],x2=x.length>1?"."+x[1]:"";for(var b=/(\d+)(\d{3})/;b.test(x1);)x1=x1.replace(b,"$1.$2");return x1+x2},add_option:function(a,b){},buttonSubmitEvent:function(){},tableCanvas:function(){},initStoreUI:function(){a("#filter_submit").on("click",function(){a.ajax({url:App.adminUrl+"/reports/get_store_data",type:"POST",dataType:"json",data:{start_date:a("#start_date").val(),end_date:a("#end_date").val(),month:a("#month_sel").val(),year:a("#year_sel").val(),store:a("#store_sel").val(),outlet:a("#outlet_sel").val()},success:function(b){if(null!=b.data){a(".sales-table").find("tr:gt(0)").remove();var c=0,d=0,e=0,f=0,g=[];for(a("#table-sales-hidden").attr("border","1"),a("#start_date_hidden").text(a("#start_date").val()),a("#end_date_hidden").text(a("#end_date").val()),a("#month_hidden").text(a("#month_sel option:selected").text()),a("#year_hidden").text(a("#year_sel option:selected").text()),a("#store_hidden").text(a("#store_sel option:selected").text()),a("#outlet_hidden").text(a("#outlet_sel option:selected").text()),a(".hide_btn").show(),i=0;i<b.data.length;i++){var h=parseInt(b.data[i].menu_hpp)+parseInt(b.data[i].side_dish_hpp),j=parseInt(b.data[i].total_price),k=j-h;a(".sales-table").append(a("<tr></tr>").append(a("<td>"+b.data[i].order_date+"</td>")).append(a("<td>"+b.data[i].stores.join(",")+"</td>")).append(a("<td>"+b.data[i].guest_count+"</td>")).append(a("<td>"+b.data[i].item_count+"</td>")).append(a("<td>Rp "+App.addCommas(h)+"</td>")).append(a("<td>Rp "+App.addCommas(j)+"</td>")).append(a("<td>Rp "+App.addCommas(k)+"</td>"))),c+=b.data[i].item_count,d+=parseInt(b.data[i].guest_count),e+=parseInt(b.data[i].total_price),f+=parseInt(b.data[i].menu_hpp)+parseInt(b.data[i].side_dish_hpp),g=g.concat(b.data[i].stores).unique()}0==b.data.length?a(".sales-table").append(a('<tr><th colspan="6" align="center" valign="middle" >No Data</th></tr>')):(a(".sales-table").append(a("<tr></tr>").append(a("<td><b>Total</b></td>")).append(a("<td><b>"+g.length+" Resto</b></td>")).append(a("<td><b>"+d+" Tamu</b></td>")).append(a("<td><b>"+c+" Item</b></td>")).append(a("<td><b>Rp "+App.addCommas(f)+"</b></td>")).append(a("<td><b>Rp "+App.addCommas(e)+"</b></td>")).append(a("<td><b>Rp "+App.addCommas(e-f)+"</b></td>"))),a(".sales-table").append(a("<tr></tr>").append(a("<td>Average Gross Expense Per Resto</td>")).append(a("<td colspan=5><b>Rp "+App.addCommas(Math.round(f/g.length*1)/1)+"</b></td>"))),a(".sales-table").append(a("<tr></tr>").append(a("<td>Average Gross Revenue Per Resto</td>")).append(a("<td colspan=5><b>Rp "+App.addCommas(Math.round(e/g.length*1)/1)+"</b></td>"))),a(".sales-table").append(a("<tr></tr>").append(a("<td>Average Gross Profit Per Resto</td>")).append(a("<td colspan=5><b>Rp "+App.addCommas(Math.round((e-f)/g.length*1)/1)+"</b></td>"))))}else alert(b.message)}})}),a("#export_pdf").on("click",function(){a.ajax({url:App.adminUrl+"/reports/export_report_to_pdf",type:"POST",dataType:"json",data:{html_string:a(".panel-hidden").html(),report:"store"},success:function(a){console.log(App.baseUrl+a),""!=a?window.open(App.baseUrl+a,"_newtab"):alert("Export report gagal")}})}),a("#export_xls").on("click",function(){a.ajax({url:App.adminUrl+"/reports/export_report_to_xls",type:"POST",dataType:"json",data:{html_string:a(".panel-hidden").html(),report:"store"},success:function(a){console.log(App.baseUrl+a),""!=a?window.open(App.baseUrl+a,"_newtab"):alert("Export report gagal")}})})},initStaffUI:function(){a("#filter_submit").on("click",function(){a.ajax({url:App.adminUrl+"/reports/get_staff_data",type:"POST",dataType:"json",data:{start_date:a("#start_date").val(),end_date:a("#end_date").val(),month:a("#month_sel").val(),year:a("#year_sel").val(),store:a("#store_sel").val(),staff:a("#staff_sel").val(),role:a("#role_sel").val()},success:function(b){if(null!=b.data){a(".sales-table").find("tr:gt(0)").remove();var c=0,d=0,e=[],f=[],g=0,h=0,l=[],m=[],n=[];for(a("#table-sales-hidden").attr("border","1"),a("#start_date_hidden").text(a("#start_date").val()),a("#end_date_hidden").text(a("#end_date").val()),a("#month_hidden").text(a("#month_sel option:selected").text()),a("#year_hidden").text(a("#year_sel option:selected").text()),a("#store_hidden").text(a("#store_sel option:selected").text()),a("#outlet_hidden").text(a("#outlet_sel option:selected").text()),a(".hide_btn").show(),i=0;i<b.data.length;i++){if(b.data[i].waiter_id==b.data[i].cashier_id){l.push(b.data[i].cashier_id);var o="",p="";b.data[i].waiter_data?(o=b.data[i].waiter_data.role,p=b.data[i].waiter_data.name,g+=1,m.push(b.data[i].waiter_id)):(o=b.data[i].cashier_data.role,p=b.data[i].cashier_data.name,h+=1,n.push(b.data[i].cashier_id)),a(".sales-table").append(a("<tr></tr>").append(a("<td>"+b.data[i].order_date+"</td>")).append(a("<td>"+p+"</td>")).append(a("<td>"+b.data[i].guest_count+"</td>")).append(a("<td>"+o+"</td>")))}else b.data[i].waiter_data&&(l.push(b.data[i].waiter_id),m.push(b.data[i].waiter_id),o=b.data[i].waiter_data.role,g+=1,a(".sales-table").append(a("<tr></tr>").append(a("<td>"+b.data[i].order_date+"</td>")).append(a("<td>"+b.data[i].waiter_data.name+"</td>")).append(a("<td>"+b.data[i].guest_count+"</td>")).append(a("<td>"+b.data[i].waiter_data.role+"</td>")))),b.data[i].cashier_data&&(l.push(b.data[i].cashier_id),n.push(b.data[i].cashier_id),o=b.data[i].cashier_data.role,h+=1,a(".sales-table").append(a("<tr></tr>").append(a("<td>"+b.data[i].order_date+"</td>")).append(a("<td>"+b.data[i].cashier_data.name+"</td>")).append(a("<td>"+b.data[i].guest_count+"</td>")).append(a("<td>"+b.data[i].cashier_data.role+"</td>"))));if(0!=e.length)for(j=0;j<e.length&&b.data[i].waiter_id!=e[j];j++)e.push(b.data[i].waiter_id);else e.push(b.data[i].waiter_id);if(0!=f.length)for(k=0;k<f.length;k++)b.data[i].cashier_id!=f[k]&&f.push(b.data[i].cashier_id);else f.push(b.data[i].cashier_id);c+=0,d+=parseInt(b.data[i].guest_count)}var q="",r="";q=0==App.uniqueArray(m).length?0:Math.round(d/App.uniqueArray(m).length*1)/1,r=0==App.uniqueArray(n).length?0:Math.round(d/App.uniqueArray(n).length*1)/1,0==b.data.length?a(".sales-table").append(a('<tr><th colspan="6" align="center" valign="middle" >No Data</th></tr>')):(a(".sales-table").append(a("<tr></tr>").append(a("<td><b>Total</b></td>")).append(a("<td><b>"+App.uniqueArray(l).length+" Staff</b></td>")).append(a("<td><b>"+d+" Tamu</b></td>")).append(a("<td><b>"+App.uniqueArray(m).length+" Waiter, "+App.uniqueArray(n).length+" Kasir, </b></td>"))),a(".sales-table").append(a("<tr></tr>").append(a("<td>Average Guest Per Waiter</td>")).append(a("<td colspan=5><b>"+q+"</b></td>"))),a(".sales-table").append(a("<tr></tr>").append(a("<td>Average Guest Per Kasir</td>")).append(a("<td colspan=5><b>"+r+"</b></td>"))))}else alert(b.message)}})}),a("#export_pdf").on("click",function(){a.ajax({url:App.adminUrl+"/reports/export_report_to_pdf",type:"POST",dataType:"json",data:{html_string:a(".panel-hidden").html(),report:"staff"},success:function(a){console.log(App.baseUrl+a),""!=a?window.open(App.baseUrl+a,"_newtab"):alert("Export report gagal")}})}),a("#export_xls").on("click",function(){a.ajax({url:App.adminUrl+"/reports/export_report_to_xls",type:"POST",dataType:"json",data:{html_string:a(".panel-hidden").html(),report:"staff"},success:function(a){console.log(App.baseUrl+a),""!=a?window.open(App.baseUrl+a,"_newtab"):alert("Export report gagal")}})})},initIngredientUI:function(){a("#filter_submit").on("click",function(){a.ajax({url:App.adminUrl+"/reports/get_ingredient_data",type:"POST",dataType:"json",data:{start_date:a("#start_date").val(),end_date:a("#end_date").val(),month:a("#month_sel").val(),year:a("#year_sel").val(),outlet:a("#outlet_sel").val(),inventory:a("#inventory_sel").val()},success:function(b){if(null!=b.data){a(".sales-table").find("tr:gt(0)").remove();var c=0,d=0,e=0,f=0,g=[];for(a("#table-sales-hidden").attr("border","1"),a("#start_date_hidden").text(a("#start_date").val()),a("#end_date_hidden").text(a("#end_date").val()),a("#month_hidden").text(a("#month_sel option:selected").text()),a("#year_hidden").text(a("#year_sel option:selected").text()),a("#outlet_hidden").text(a("#outlet_sel option:selected").text()),a("#inventory_hidden").text(a("#outlet_sel option:selected").text()),a(".hide_btn").show(),a("#btn_prev").on("click",function(){prevPage()}),a("#btn_next").on("click",function(){nextPage()}),i=0;i<b.data.length;i++){parseInt(b.data[i].menu_hpp)+parseInt(b.data[i].side_dish_hpp),parseInt(b.data[i].total_price);a(".sales-table").append(a("<tr></tr>").append(a("<td>"+b.data[i].order_date+"</td>")).append(a("<td>"+b.data[i].outlet_name+"</td>")).append(a("<td>"+b.data[i].inventory_name+"</td>")).append(a("<td>"+b.data[i].total_used+"</td>")).append(a("<td>"+b.data[i].total_stock+"</td>")).append(a("<td>"+b.data[i].total_stock_opname+"</td>")).append(a("<td>"+parseInt(b.data[i].delta_stock)+"</td>")).append(a("<td>"+b.data[i].unit+"</td>"))),c+=b.data[i].item_count,d+=parseInt(b.data[i].guest_count),e+=parseInt(b.data[i].total_price),f+=parseInt(b.data[i].menu_hpp)+parseInt(b.data[i].side_dish_hpp),g=g.concat(b.data[i].stores).unique()}if(0==b.data.length)a(".sales-table").append(a('<tr><th colspan="8" align="center" valign="middle" >No Data</th></tr>')),a("#paging-report").pagination("destroy");else{var h=a(".sales-table.table-show tbody tr"),j=10,k=Math.floor(h.length/j),k=h.length;h.slice(j).hide(),a("#paging-report").pagination({items:k,itemsOnPage:j,cssStyle:"light-theme",onPageClick:function(a){var b=j*(a-1),c=b+j;h.hide().slice(b,c).show()}})}}else alert(b.message)}})}),a("#export_pdf").on("click",function(){a.ajax({url:App.adminUrl+"/reports/export_report_to_pdf",type:"POST",dataType:"json",data:{html_string:a(".panel-hidden").html(),report:"store"},success:function(a){console.log(App.baseUrl+a),""!=a?window.open(App.baseUrl+a,"_newtab"):alert("Export report gagal")}})}),a("#export_xls").on("click",function(){a.ajax({url:App.adminUrl+"/reports/export_report_to_xls",type:"POST",dataType:"json",data:{html_string:a(".panel-hidden").html(),report:"store"},success:function(a){console.log(App.baseUrl+a),""!=a?window.open(App.baseUrl+a,"_newtab"):alert("Export report gagal")}})})},uniqueArray:function(a){for(var b=[],c=a,d=c.length;d--;){var e=c[d];-1===b.indexOf(e)&&b.unshift(e)}return b}}});