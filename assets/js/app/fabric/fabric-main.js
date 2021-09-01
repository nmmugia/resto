/**
 * Created by alta falconeri on 12/19/2014.
 */

define([
        "jquery",
        "app/fabric/fabric-shape"
    ],
    function ($, shape) {

        return {
            shape          : shape,
            myCanvas       : null,
            canvasWidth    : $('#defaultTableWidth').val(),
            canvasHeight   : $('#defaultTableHeight').val(),
            loadDataTable  : $('#loadDataTable').val(),
            saveDataTable  : $('#saveDataTable').val(),
            getUniqueID    : $('#getUniqueID').val(),
            deleteDataID   : $('#deleteDataID').val(),
            clearDataTable : $('#clearDataTable').val(),
            init           : function () {
                console.log('Init Fabric..');
                App.fabricJs.initCanvas();
                App.fabricJs.initObject();
                App.initFunc(App);
                //shape
                App.fabricJs.shape.init();
            },

            initCanvas : function () {
                console.log('Init Fabric Canvas..');

                var newCanvas = $('<canvas/>', {
                    id : 'canvas_table'
                });
                $('#canvasWrapper').html(newCanvas);

                App.fabricJs.myCanvas = new fabric.Canvas('canvas_table');
                App.fabricJs.myCanvas.setHeight(App.fabricJs.canvasHeight);
                App.fabricJs.myCanvas.setWidth(App.fabricJs.canvasWidth);
                App.fabricJs.myCanvas.backgroundColor = "#FFF";
                App.fabricJs.myCanvas.selectionColor = 'rgba(0,255,0,0.3)';
                App.fabricJs.myCanvas.selectionBorderColor = 'red';
                App.fabricJs.myCanvas.selectionLineWidth = 5;
                App.fabricJs.myCanvas.selectionDashArray = [3, 2];
                App.fabricJs.myCanvas.renderAll();
                App.fabricJs.myCanvas.clear();

                // keep object inside canvas
                App.fabricJs.myCanvas.on('object:modified', function (e) {
                    var obj = e.target;
                    var rect = obj.getBoundingRect();

                    if (rect.left < 0
                        || rect.top < 0
                        || rect.left + rect.width > App.fabricJs.myCanvas.getWidth()
                        || rect.top + rect.height > App.fabricJs.myCanvas.getHeight()) {
                        if (obj.getAngle() != obj.originalState.angle) {
                            obj.setAngle(obj.originalState.angle);
                        }
                        else {
                            obj.setTop(obj.originalState.top);
                            obj.setLeft(obj.originalState.left);
                            obj.setScaleX(obj.originalState.scaleX);
                            obj.setScaleY(obj.originalState.scaleY);
                        }
                        obj.setCoords();
                    }
                }).on('object:selected', function (e) {
                    $('#delete_table').prop('disabled', false);
                }).on('selection:cleared', function () {
                    $('#delete_table').prop('disabled', true);
                });

                // disabled button
                $('#delete_table').prop('disabled', true);
                App.fabricJs.disabledBtn(true);

            },

            initObject : function () {
                $('#table_floor_canvas').on('change', function (e) {
                    App.overlayUI.show();
                    App.fabricJs.initCanvas();
                    if ($(this).val() > 0) {
                        App.fabricJs.disabledBtn(false);

                        var request = $.ajax({
                            type : 'POST',
                            url  : App.fabricJs.loadDataTable,
                            data : {'floor_id' : $(this).val()}
                        });
                        request.done(function (msg) {
                            var parsedObject = JSON.parse(msg);
                            if (parsedObject.status === true) {
                                App.fabricJs.myCanvas.clear();
                                App.fabricJs.myCanvas.loadFromJSON(parsedObject.items);
                                App.fabricJs.myCanvas.renderAll();
                                // optional
                                App.fabricJs.myCanvas.calcOffset();

                                App.fabricJs.myCanvas.forEachObject(function (obj) {
                                    obj.toObject = (function (toObject) {
                                        return function () {
                                            return fabric.util.object.extend(toObject.call(this), {
                                                id : this.id
                                            });
                                        };
                                    })(obj.toObject);

                                });
                            }
                            App.overlayUI.hide();
                        });
                        request.fail(function (jqXHR, textStatus) {
                            App.overlayUI.hide();
                        });
                        request.always(function () {
                        });
                    } else {
                        App.fabricJs.disabledBtn(true);
                        App.overlayUI.hide();
                    }

                });

                var btnRect = $('#addRect');
                var btnTriangle = $('#addTriangle');
                var btnCircle = $('#addCircle');
                var btnDeleteTable = $('#delete_table');
                var btnClearTable = $('#clear_table');
                var btnSaveTable = $('#save_table');

                btnRect.on('click', function (e) {
                    var floor_val = $('#table_floor_canvas').val();
                    var tablename = $('#table_name').val();

                    if (tablename == '') {
                        App.alert('Table name cannot empty!');
                        return false;
                    } else if (tablename.length > 25) {
                        App.alert('Max 25 Characters!');
                        return false;
                    }
                    if (floor_val > 0) {
                        var coord = App.fabricJs.getRandomLeftTop();

                        var newShapes = App.fabricJs.shape.createRect(tablename);

                        var request = $.ajax({
                            type : 'POST',
                            url  : App.fabricJs.getUniqueID
                        });
                        request.done(function (msg) {
                            var parsedObject = JSON.parse(msg);
                            newShapes.id = parsedObject.random_id;

                            App.fabricJs.myCanvas.add(newShapes);
                            App.fabricJs.myCanvas.setActiveObject(newShapes);
                        });
                        request.fail(function (jqXHR, textStatus) {
                            App.overlayUI.hide();
                        });
                        request.always(function () {
                        });
                        $('#table_name').val('');
                    } else {
                        App.alert('please, choose floor first');
                    }
                });

                btnTriangle.on('click', function (e) {
                    var floor_val = $('#table_floor_canvas').val();
                    var tablename = $('#table_name').val();

                    if (tablename == '') {
                        App.alert('Table name cannot empty!');
                        return false;
                    } else if (tablename.length > 25) {
                        App.App.alert('Max 25 Characters!');
                        return false;
                    }
                    if (floor_val > 0) {
                        var coord = App.fabricJs.getRandomLeftTop();

                        var newShapes = App.fabricJs.shape.createTriangle(tablename);

                        var request = $.ajax({
                            type : 'POST',
                            url  : App.fabricJs.getUniqueID
                        });
                        request.done(function (msg) {
                            var parsedObject = JSON.parse(msg);
                            newShapes.id = parsedObject.random_id;

                            App.fabricJs.myCanvas.add(newShapes);
                            App.fabricJs.myCanvas.setActiveObject(newShapes);
                        });
                        request.fail(function (jqXHR, textStatus) {
                            App.overlayUI.hide();
                        });
                        request.always(function () {
                        });
                        $('#table_name').val('');
                    } else {
                        App.alert('please,choose floor first');
                    }
                });

                btnCircle.on('click', function (e) {
                    var floor_val = $('#table_floor_canvas').val();
                    var tablename = $('#table_name').val();

                    if (tablename == '') {
                        App.alert('Table name cannot empty!');
                        return false;
                    } else if (tablename.length > 25) {
                        App.alert('Max 25 Characters!');
                        return false;
                    }
                    if (floor_val > 0) {
                        var coord = App.fabricJs.getRandomLeftTop();

                        var newShapes = App.fabricJs.shape.createCircle(tablename);

                        var request = $.ajax({
                            type : 'POST',
                            url  : App.fabricJs.getUniqueID
                        });
                        request.done(function (msg) {
                            var parsedObject = JSON.parse(msg);
                            newShapes.id = parsedObject.random_id;

                            App.fabricJs.myCanvas.add(newShapes);
                            App.fabricJs.myCanvas.setActiveObject(newShapes);
                        });
                        request.fail(function (jqXHR, textStatus) {
                            App.overlayUI.hide();
                        });
                        request.always(function () {
                        });
                        $('#table_name').val('');
                    } else {
                        App.alert('choose floor first');
                    }
                });

                btnDeleteTable.on('click', function (e) {

                    function deleteTable(){

                        var activeObject = App.fabricJs.myCanvas.getActiveObject(),
                            activeGroup = App.fabricJs.myCanvas.getActiveGroup();

                        if (activeGroup) {
                            var objectsInGroup = activeGroup.getObjects();
                            App.fabricJs.myCanvas.discardActiveGroup();
                            objectsInGroup.forEach(function (object) {
                                // App.fabricJs.myCanvas.remove(object);
                                App.fabricJs.deleteObjOnServer(object.id, object);
                            });
                        }
                        else if (activeObject) {
                            // App.fabricJs.myCanvas.remove(activeObject);
                            App.fabricJs.deleteObjOnServer(activeObject.id, activeObject);

                        }else{
                            return false;
                        }
                    }
                    App.confirm('Apakah anda yakin akan hapus meja ini?',deleteTable);
                });

                btnClearTable.on('click', function (e) {
                    App.confirm('Apakah anda yakin akan hapus semua meja di lantai ini?', function(){ 
                        App.fabricJs.deleteAllObjOnServer() 
                    });
                });

                btnSaveTable.on('click', function (e) {
                    App.fabricJs.sendObjToserver();
                });
            },

            capitalize : function (string) {
                return string.charAt(0).toUpperCase() + string.slice(1);
            },

            pad : function (str, length) {
                while (str.length < length) {
                    str = '0' + str;
                }
                return str;
            },

            getRandomNum : function (max) {
                return Math.floor((Math.random() * max) + 1);
            },

            getRandomLeftTop     : function () {
                var offset = 50;
                return {
                    left : fabric.util.getRandomInt(0 + offset, 700 - offset),
                    top  : fabric.util.getRandomInt(0 + offset, 500 - offset)
                };
            },
            disabledBtn          : function (e) {
                //disable button by default
                $('#save_table').prop('disabled', e);
                $('#clear_table').prop('disabled', e);
                $('#addRect').prop('disabled', e);
                $('#addTriangle').prop('disabled', e);
                $('#addCircle').prop('disabled', e);
            },
            sendObjToserver      : function () {
                App.overlayUI.show();
                var json = JSON.stringify(App.fabricJs.myCanvas);
                var request = $.ajax({
                    type : 'POST',
                    url  : App.fabricJs.saveDataTable,
                    data : {'floor_id' : $('#table_floor_canvas').val(), 'data' : json}
                });
                request.done(function (msg) {
                    App.overlayUI.hide();
                });
                request.fail(function (jqXHR, textStatus) {
                    App.overlayUI.hide();
                });
                request.always(function () {
                });
            },
            deleteObjOnServer    : function (id, object) {
                var request = $.ajax({
                    type : 'POST',
                    url  : App.fabricJs.deleteDataID,
                    data : {'id' : id}
                });
                request.done(function (msg) {
                    if (msg != '' && msg == 1) {
                        App.fabricJs.myCanvas.remove(object);
                    }
                    else
                        App.alert('Gagal menghapus semua tabel. satu atau lebih tabel tidak kosong');
                });
                request.fail(function (jqXHR, textStatus) {
                });
                request.always(function () {
                });
            },
            deleteAllObjOnServer : function (id) {
                var request = $.ajax({
                    type : 'POST',
                    url  : App.fabricJs.clearDataTable,
                    data : {'id' : $('#table_floor_canvas').val()}
                });
                request.done(function (msg) {
                    if (msg != '' && msg == 1) {
                        App.fabricJs.myCanvas.clear();
                    }
                    else
                        App.alert('Gagal menghapus semua tabel. Satu atau lebih tabel tidak kosong! Pastikan tidak ada yang dine in pada tabel yag ingin dihapus!');
                });
                request.fail(function (jqXHR, textStatus) {
                });
                request.always(function () {
                });
            }
        }
    });