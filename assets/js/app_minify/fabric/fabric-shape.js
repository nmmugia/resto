define([],function(){return{init:function(){App.fabricJs.shape.initRect(),App.fabricJs.shape.initCircle(),App.fabricJs.shape.initTriangle()},initRect:function(){fabric.LabeledRect=fabric.util.createClass(fabric.Rect,{type:"labeledRect",initialize:function(a){a||(a={}),this.callSuper("initialize",a),this.set("label",a.label||""),this.set("id",a.id||"")},toObject:function(){return fabric.util.object.extend(this.callSuper("toObject"),{label:this.get("label"),id:this.get("id")})},_render:function(a){this.callSuper("_render",a);var b=App.fabricJs.shape.fragmentText(a,this.label,this.width/2),c=15,d=0;a.font=c+"px Helvetica",a.fillStyle="#FFF",a.textBaseline="alphabetic",a.textAlign="center",b.forEach(function(a,b){d+=(b+1)*c});var e=(this.height-d)/2;b.forEach(function(b,d){a.fillText(b,0,-e+(d+1)*c)})}}),fabric.LabeledRect.fromObject=function(a){var b=new fabric.LabeledRect(a,function(){return b&&b.canvas&&b.canvas.renderAll()});return b}},initCircle:function(){fabric.LabeledCircle=fabric.util.createClass(fabric.Circle,{type:"labeledCircle",initialize:function(a){a||(a={}),this.callSuper("initialize",a),this.set("label",a.label||""),this.set("id",a.id||"")},toObject:function(){return fabric.util.object.extend(this.callSuper("toObject"),{label:this.get("label"),id:this.get("id")})},_render:function(a){this.callSuper("_render",a);var b=App.fabricJs.shape.fragmentText(a,this.label,this.width/2),c=15,d=0;a.font=c+"px Helvetica",a.fillStyle="#FFF",a.textBaseline="alphabetic",a.textAlign="center",b.forEach(function(a,b){d+=(b+1)*c});var e=(this.height-d)/2;b.forEach(function(b,d){a.fillText(b,0,-e+(d+1)*c)})}}),fabric.LabeledCircle.fromObject=function(a){var b=new fabric.LabeledCircle(a,function(){return b&&b.canvas&&b.canvas.renderAll()});return b}},initTriangle:function(){fabric.LabeledTriangle=fabric.util.createClass(fabric.Triangle,{type:"labeledTriangle",initialize:function(a){a||(a={}),this.callSuper("initialize",a),this.set("label",a.label||""),this.set("id",a.id||"")},toObject:function(){return fabric.util.object.extend(this.callSuper("toObject"),{label:this.get("label"),id:this.get("id")})},_render:function(a){this.callSuper("_render",a);var b=App.fabricJs.shape.fragmentText(a,this.label,this.width/3),c=15;a.font=c+"px Helvetica",a.fillStyle="#FFF",a.textBaseline="alphabetic",a.textAlign="center",b.forEach(function(b,d){a.fillText(b,0,(d+1)*c)})}}),fabric.LabeledTriangle.fromObject=function(a){var b=new fabric.LabeledTriangle(a,function(){return b&&b.canvas&&b.canvas.renderAll()});return b}},createRect:function(a){var b=App.fabricJs.getRandomLeftTop(),c=new fabric.LabeledRect({left:b.left,top:b.top,width:150,height:150,label:a});return c},createCircle:function(a){var b=App.fabricJs.getRandomLeftTop(),c=new fabric.LabeledCircle({left:b.left,top:b.top,radius:75,label:a});return c},createTriangle:function(a){var b=App.fabricJs.getRandomLeftTop(),c=new fabric.LabeledTriangle({left:b.left,top:b.top,width:150,height:150,label:a});return c},fragmentText:function(a,b,c){var d=b.split(" "),e=[],f="";if(a.measureText(b).width<c)return[b];for(;d.length>0;){for(;a.measureText(d[0]).width>=c;){var g=d[0];d[0]=g.slice(0,-1),d.length>1?d[1]=g.slice(-1)+d[1]:d.push(g.slice(-1))}a.measureText(f+d[0]).width<c?f+=d.shift()+" ":(e.push(f),f=""),0===d.length&&e.push(f)}return e}}});