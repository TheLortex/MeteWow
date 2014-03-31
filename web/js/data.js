var current_server=0;
var current_sensor=-1;

var last_update = 0;
var last_update_delta = 10000000000;
var sensors_data = [];
var sensors_meta = [];

var already_empty = false;

var base_api ="http://mtw.lortex.org/api/";
var graphiques = [];

var displayed_graph = null;

var majDataTimeout;

var gridster;
$(document).ready(function(){ 
    gridster = $(".gridster > ul").gridster({
          widget_margins: [5, 5],
          widget_base_dimensions: [200, 200],
          widget_class: "article",
           resize: {
            enabled: true,
            max_size: [4, 4],
            min_size: [1, 1],
            start: function(e, ui, widget) {
                var sensid = widget.data("sensor-id");
                if(typeof graphiques[sensid] != 'undefined') {
                    (graphiques[sensid]).setSize(widget.width()-20,widget.height()-20,false);
                }
                
            },

            resize: function(e, ui, widget) {
                var sensid = widget.data("sensor-id");
                jQuery("#tileset article > div").fitText(0.6, {maxFontSize: '90px' });
                if(typeof graphiques[sensid] != 'undefined') {
                    (graphiques[sensid]).setSize(widget.width()-20,widget.height()-20,false);
                }
            },

            stop: function(e, ui, widget) {
                var sensid = widget.data("sensor-id");
                jQuery("#tileset article > div").fitText(0.6, {maxFontSize: '90px' });
                
                if(typeof graphiques[sensid] != 'undefined') {
                    (graphiques[sensid]).setSize(widget.width()-20,widget.height()-20,false);
                }
                    
                localStorage.setItem(current_server+"-"+sensid+"-w", (widget.width()+20)/210);
                localStorage.setItem(current_server+"-"+sensid+"-h", (widget.height()+20)/210);
            }
          },
        draggable: {
            stop: function(event, ui) {
                var gridarray = gridster.serialize();
                for(var i=0;i<gridarray.length;i++) {
                    var sensid = ($("#tileset ul").children()[i]).dataset.sensorId;
                    
                    localStorage.setItem(current_server+"-"+sensid+"-x", gridarray[i].col);
                    localStorage.setItem(current_server+"-"+sensid+"-y", gridarray[i].row);
                }
            }
        }
          
      }).data('gridster');
});


$("#metewow_server").change(function () {
    var id = $(this).val();
    $("#metewow_server_graph").val(id);
    setServer(id);
}).trigger('change');

$("#metewow_server_graph").change(function () {
    var id = $(this).val();
    $("#metewow_server").val(id);
    setServer(id);
});

$("#metewow_sensor").change(function () {
    var id = $(this).val();
    setSensor(id);
});

function setSensor(i,section) {
    if(i == -1) {
        $("#graphboard").children().fadeOut(400);
        displayed_graph = null;
        return;
    }
    current_sensor=i;
    
    var name = sensors_meta[current_sensor][0];
    var unit = sensors_meta[current_sensor][1];
    
    $("#graphboard").highcharts('StockChart', {
        chart : {
            style: {
                color: "#fff"
            },
            events : {
                load : function() {
                    displayed_graph = this;
                  /*  var series = this.series[0];
                    var interval = setInterval(function() {
                        try {
                            // alert(sensors_data[id][0][0]+";"+sensors_data[id][sensors_data[id].length-1][0]);
                            series.xAxis.setExtremes(sensors_data[current_sensor][0][0],sensors_data[current_sensor][sensors_data[current_sensor].length-1][0]);
                            // series.yAxis.setExtremes(sensors_data[id][0][1],sensors_data[id][sensors_data[id].length-1][1]);
                            series.setData(sensors_data[current_sensor]);
                        } catch(err) {
                            clearInterval(interval);
                        }
                    }, 1000);*/
                }
            },
            backgroundColor: "rgba(0,0,0,0.1)"
        },tooltip: {
            enabled: true,
            shared: true, 
            formatter: function() {
                if(this.points[0].series.name == "average")
                    return;
                
                return  '<b>' + name +'</b><br/>' +
                    Highcharts.dateFormat('%e/%m/%y %H:%M:%S',
                                          new Date(this.x))
                + '  <br/>' + this.points[0].y + ' '+unit;
            }
        }, labels: {
            style: {
            	color: '#FFFFFF'
            }
        }, series : [{
                name : 'data',
                data : (function() {
                    return sensors_data[current_sensor];
                })(),
                gapSize : 10000,
                dataGrouping: {
                    enabled: false
                }
            },{
            name: 'average',
            data: (function() {
                    return sensors_data[current_sensor];
            })(),
            gapSize : 10000,
            color: "#ee1212",
            dashStyle: 'dash',
            dataGrouping: {
                groupPixelWidth: 25,
                approximation: 'average',
                smoothed: true
            }

        }],xAxis: {
            ordinal: true,
            type:"linear",
            labels: {
                style: {
                color: '#FFF'
            }
        }},yAxis: {labels: {
         style: {
            color: '#FFF'
         }
      }}, rangeSelector: {
        inputBoxWidth: 180,
        buttons: [{
        	type: 'day',
        	count: 1,
        	text: '1d'
        }, {
        	type: 'week',
        	count: 1,
        	text: '1w'
        }, {
        	type: 'month',
        	count: 1,
        	text: '1m'
        }, {
        	type: 'month',
        	count: 3,
        	text: '3m'
        }, {
        	type: 'month',
        	count: 6,
        	text: '6m'
        }, {
        	type: 'year',
        	count: 1,
        	text: '1y'
        }, {
        	type: 'all',
        	text: 'All'
        }]
      }     
      });
}

function setServer(i) {
    if(i == current_server)
        return;
    
    current_server=i;
    current_sensor=-1;
    setSensor(-1);
    graphiques = [];
    $.ajax({
      type: 'GET',
      url: base_api+'get.php',
      data: { request: 'sensors', from: current_server},
      beforeSend:function(){
        already_empty = false;
        $("#metewow_sensor").children().fadeOut(400, function() {
            if(already_empty == false)
                $("#metewow_sensor").empty();
        });
        sensors_data.lenght = 0;
        NProgress.start();
        
        if(gridster != undefined)
            gridster.remove_all_widgets();
      },
      success:function(data){
        NProgress.done();
        var sensors = JSON.parse(data);
        
        if($("#metewow_sensor").is(":empty")) {} else {
            already_empty = true;
            $("#metewow_sensor").empty();
        }
        
        $("#metewow_sensor").append("<option value=\"-1\" >Sélectionnez le capteur</option>");
        for (var i = 0; i < sensors.length; ++i) {
            var s = sensors[i];
            sensors_meta[s.id] = [s.display_name,s.display_unit,s.category];
            
            var widgetToAdd = "<article data-sensor-id="+s.id+" data-unit=\""+s.display_unit+"\" data-sensor-name=\""+s.display_name+"\" data-id="+i+" style=\"display:none\"><div><h3>"+s.display_name+"</h3><div><p></p><div class='loadBarWidget'></div></div><button class=\"glyphicon glyphicon-signal btn-lg\"></button></div><div style=\"display: none;\"><div class=\"quickgraph\"></div><button class=\"glyphicon glyphicon-ok btn-lg\"></button></div></article>";
            var width = localStorage.getItem(current_server+"-"+s.id+"-w");
            if( width == 0)
                width = 1;
            var height = localStorage.getItem(current_server+"-"+s.id+"-h");
            if( height == 0)
                height = 1;
                
            var x = localStorage.getItem(current_server+"-"+s.id+"-x");
            var y = localStorage.getItem(current_server+"-"+s.id+"-y");
            
            
            gridster.add_widget(widgetToAdd,width,height,x,y);
            $("#metewow_sensor").append("<option value=\""+s.id+"\" > "+s.display_name+"</option>");
            
        }
        
        
        
        if(sensors.length == 0) {
            gridster.add_widget("<article data-id=0 style=\"display:none\"><div><h3>Pas de capteurs sur cette station.</h3></article>", 1, 1);
        }
         $("#tileset").children().fadeIn(400);
        last_update = 0;
        sensors_data.lenght = 0;
                jQuery("#tileset article > div").fitText(0.6, {maxFontSize: '90px' });
        majData();
      },
      error: function(xhr,textStatus,err)
{
    NProgress.done();
    alert("readyState: " + xhr.readyState);
    alert("responseText: "+ xhr.responseText);
    alert("status: " + xhr.status);
    alert("text status: " + textStatus);
    alert("error: " + err);
}
    });
}

var children;
function majData() {
    var rq=current_server;
    var from_age = last_update;
    
    clearTimeout(majDataTimeout);
    
    $.ajax({
      type: 'GET',
      url: base_api+'get.php',
      data: { request: 'data', from: from_age+"",server: current_server},
      beforeSend:function(){
      },
      success:function(data){
        values = JSON.parse(data);
        children = $('#tileset ul').children();
        var i = $(children[0]).data("sensor-id");
        var v = values[i];
        var len = children.length-1;
        handleData(0,0,Math.min(batchSize,len),len,v.length);
        
     
      },
      error:function(){}
    });
}

var batchSize = 256;

function handleData(cursensor, curvalue_i, curvalue_e, maxsensor, maxvalue) {
    var i = $(children[cursensor]).data("sensor-id");
    
    
    var i_plus_1 =0;
    if(cursensor != maxsensor);
        i_plus_1 = $(children[cursensor+1]).data("sensor-id");
    var v = values[i];
    
    var width = ((maxvalue-curvalue_e)/maxvalue)*100;
    var left = (100-width)/2;
    
    $(children[cursensor]).find(".loadBarWidget").css("width",width+"%");
    $(children[cursensor]).find(".loadBarWidget").css("left",left+"%");
                
    if(typeof v != 'undefined') {
        for(var index=curvalue_i; index <= curvalue_e; index++) {
            if(v.length > index) {
                if(typeof sensors_data[i] == 'undefined')
                    sensors_data[i] = [];
                    
               // var t = moment(v[index][0]).unix()*1000;
                var t = v[index][0];
                if(last_update < v[index][0])
                    last_update = v[index][0];
                
                var crvalue = parseFloat(v[index][1]);
                var already=false;
                for(var azer=0;azer<sensors_data[i].length && !already;azer++) 
                    if(sensors_data[i][azer][0] == t)
                        already=true;
                
                if(!already && !isNaN(crvalue))
                    sensors_data[i].push([t,crvalue]);
            
            }
        }
    }
    
    if(curvalue_e == maxvalue) {
        if(typeof v != 'undefined') {
            if(v.length > 0)
                $(children[cursensor]).find("p").html(v.pop()[1] + " " + $(children[cursensor]).data("unit"));
            
        }
        if(typeof sensors_data[i] != 'undefined') {
            var newDelta = new Date().getTime() - sensors_data[i][sensors_data[i].length-1][0];
            if(newDelta < last_update_delta)
                last_update_delta = newDelta;
        }
        if(cursensor == maxsensor) {
            handleUpdateDelta();
            majDataTimeout = setTimeout("majData()",5000);
          //  console.log("Ending");
        } else {
            maxvalue = values[i_plus_1].length;
            var max = Math.min(batchSize, maxvalue);
            
        //    console.log("Calling handleData("+(cursensor+1)+",0,"+max+","+maxsensor+","+maxvalue+")");
            setTimeout("handleData("+(cursensor+1)+",0,"+max+","+maxsensor+","+maxvalue+")",1);
        }
    } else {
        
        var min = curvalue_e+1;
        var max = Math.min(min+batchSize, maxvalue);
     //   console.log("Calling handleData("+cursensor+","+min+","+max+","+maxsensor+","+maxvalue+")");
        setTimeout("handleData("+cursensor+","+min+","+max+","+maxsensor+","+maxvalue+")",1);
    }
    
    
}


function handleUpdateDelta() {
    if(last_update_delta==10000000000)
    $(".last_update_delta").html("trop longtemps");
    else {
        var secs = Math.round(last_update_delta/1000);
        var mins = Math.floor(secs/60);
        secs = secs - mins*60;
        var hours = Math.floor(mins/60);
        mins = mins - hours*60;
        var days = Math.floor(hours/24);
        hours = hours - days*24 - 1; // TECHNIQUE DE GROS PORC
        
        if(days != 0) {
            $(".last_update_delta").html(days + " jour" + (days > 1 ? "s" : ""));
        } else if(hours != 0) {
            $(".last_update_delta").html(hours + " heure" + (hours > 1 ? "s" : ""));
        } else if(mins != 0) {
            $(".last_update_delta").html(mins + " minute" + (mins > 1 ? "s" : ""));
        } else if(secs != 0) {
            $(".last_update_delta").html(secs + " seconde" + (secs > 1 ? "s" : ""));
        }
    }
    last_update_delta=10000000000;
}


majData();
/*
<article data-id=0>
    <div>
        <h3>Une donnée.</h3>
        <p>Lorem ipsum</p>
        
        <button class="glyphicon glyphicon-signal btn-lg"></button>
    </div>
    <div style="display: none;">
        <div class="quickgraph"></div>
        <button class="glyphicon glyphicon-ok btn-lg"></button>
    </div>
</article>
*/



