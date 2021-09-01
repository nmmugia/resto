/**
 * Created by alta falconeri on 12/19/2014.
 */

define([],
    function () {

        return {
            init           : function () {
                App.fabricJs.shape.initRect();
                App.fabricJs.shape.initCircle();
                App.fabricJs.shape.initTriangle();
            },
            initRect       : function () {
                fabric.LabeledRect = fabric.util.createClass(fabric.Rect, {

                    type : 'labeledRect',

                    initialize : function (options) {
                        options || (options = {});

                        this.callSuper('initialize', options);
                        this.set('label', options.label || '');
                        this.set('id', options.id || '');
                    },

                    toObject : function () {
                        return fabric.util.object.extend(this.callSuper('toObject'), {
                            label : this.get('label'),
                            id    : this.get('id')
                        });
                    },

                    _render : function (ctx) {
                        this.callSuper('_render', ctx);

                        var lines = App.fabricJs.shape.fragmentText(ctx, this.label, this.width / 2), // 10% padding
                            font_size = 15, // px
                            tempheight = 0;

                        ctx.font = font_size + 'px Helvetica';
                        ctx.fillStyle = '#FFF';
                        //ctx.fillText(this.label, -this.width / 2 + 20, 0);
                        ctx.textBaseline = 'alphabetic';
                        ctx.textAlign = 'center';
                        lines.forEach(function (line, i) {
                            tempheight += ((i + 1) * font_size);
                        });
                        var newHeight = ((this.height - tempheight )/2);
                        lines.forEach(function (line, i) {
                            ctx.fillText(line, 0, -newHeight + ((i + 1) * font_size)); // assume font height.
                        });
                    }
                });
                fabric.LabeledRect.fromObject = function (object) {
                    var instance = new fabric.LabeledRect(object, function () {
                        return instance && instance.canvas && instance.canvas.renderAll();
                    });
                    return instance;
                };

            },
            initCircle     : function () {
                fabric.LabeledCircle = fabric.util.createClass(fabric.Circle, {

                    type : 'labeledCircle',

                    initialize : function (options) {
                        options || (options = {});

                        this.callSuper('initialize', options);
                        this.set('label', options.label || '');
                        this.set('id', options.id || '');
                    },

                    toObject : function () {
                        return fabric.util.object.extend(this.callSuper('toObject'), {
                            label : this.get('label'),
                            id    : this.get('id')
                        });
                    },

                    _render : function (ctx) {
                        this.callSuper('_render', ctx);

                        var lines = App.fabricJs.shape.fragmentText(ctx, this.label, this.width / 2), // 10% padding
                            font_size = 15, // px
                            tempheight = 0;

                        ctx.font = font_size + 'px Helvetica';
                        ctx.fillStyle = '#FFF';
                        //ctx.fillText(this.label, -this.width / 2 + 20, 0);
                        ctx.textBaseline = 'alphabetic';
                        ctx.textAlign = 'center';
                        lines.forEach(function (line, i) {
                            tempheight += ((i + 1) * font_size);
                        });
                        var newHeight = ((this.height - tempheight )/2);
                        lines.forEach(function (line, i) {
                            ctx.fillText(line, 0, -newHeight + ((i + 1) * font_size)); // assume font height.
                        });
                    }
                });
                fabric.LabeledCircle.fromObject = function (object) {
                    var instance = new fabric.LabeledCircle(object, function () {
                        return instance && instance.canvas && instance.canvas.renderAll();
                    });
                    return instance;
                };

            },
            initTriangle   : function () {
                fabric.LabeledTriangle = fabric.util.createClass(fabric.Triangle, {

                    type : 'labeledTriangle',

                    initialize : function (options) {
                        options || (options = {});

                        this.callSuper('initialize', options);
                        this.set('label', options.label || '');
                        this.set('id', options.id || '');
                    },

                    toObject : function () {
                        return fabric.util.object.extend(this.callSuper('toObject'), {
                            label : this.get('label'),
                            id    : this.get('id')
                        });
                    },

                    _render : function (ctx) {
                        this.callSuper('_render', ctx);

                        var lines = App.fabricJs.shape.fragmentText(ctx, this.label, this.width / 3), // 10% padding
                            font_size = 15, // px
                            tempheight = 0;

                        ctx.font = font_size + 'px Helvetica';
                        ctx.fillStyle = '#FFF';
                        //ctx.fillText(this.label, -this.width / 2 + 20, 0);
                        ctx.textBaseline = 'alphabetic';
                        ctx.textAlign = 'center';

                        lines.forEach(function (line, i) {
                            ctx.fillText(line, 0, ((i + 1) * font_size)); // assume font height.
                        });
                    }
                });
                fabric.LabeledTriangle.fromObject = function (object) {
                    var instance = new fabric.LabeledTriangle(object, function () {
                        return instance && instance.canvas && instance.canvas.renderAll();
                    });
                    return instance;
                };

            },
            createRect     : function (label) {
                var coord = App.fabricJs.getRandomLeftTop();
                var newShapes = new fabric.LabeledRect({
                    left   : coord.left,
                    top    : coord.top,
                    width  : 150,
                    height : 150,
                    label  : label
                });

                return newShapes;
            },
            createCircle   : function (label) {
                var coord = App.fabricJs.getRandomLeftTop();
                var newShapes = new fabric.LabeledCircle({
                    left   : coord.left,
                    top    : coord.top,
                    radius : 75,
                    label  : label
                });

                return newShapes;
            },
            createTriangle : function (label) {
                var coord = App.fabricJs.getRandomLeftTop();
                var newShapes = new fabric.LabeledTriangle({
                    left   : coord.left,
                    top    : coord.top,
                    width  : 150,
                    height : 150,
                    label  : label
                });

                return newShapes;
            },
            fragmentText   : function (context, text, maxWidth) {
                var words = text.split(' '),
                    lines = [],
                    line = "";
                if (context.measureText(text).width < maxWidth) {
                    return [text];
                }
                while (words.length > 0) {

                    while (context.measureText(words[0]).width >= maxWidth) {
                        var tmp = words[0];
                        words[0] = tmp.slice(0, -1);
                        if (words.length > 1) {
                            words[1] = tmp.slice(-1) + words[1];
                        } else {
                            words.push(tmp.slice(-1));
                        }
                    }
                    if (context.measureText(line + words[0]).width < maxWidth) {
                        line += words.shift() + " ";
                    } else {
                        lines.push(line);
                        line = "";
                    }
                    if (words.length === 0) {
                        lines.push(line);
                    }
                }
                return lines;
            }
        }
    });