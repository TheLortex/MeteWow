var current_server=0;
var current_sensor=-1;

var last_update = "2999-01-01 00:00:00";
var last_update_delta = 10000000000;
var sensors_data = [];
var sensors_meta = [];

var already_empty = false;

var base_api ="http://mtw.lortex.org/api/";

$("#metewow_server").change(function () {
    var id = $(this).val();
    setServer(id);
}).trigger('change');

$("#metewow_sensor").change(function () {
    var id = $(this).val();
    setSensor(id);
});

function setSensor(i,section) {
    if(i == -1)
        return;
    current_sensor=i;
    
    var name = sensors_meta[current_sensor][0];
    var unit = sensors_meta[current_sensor][1];
    
    /*$("#graphboard").children().fadeOut(400, function() {
        $("#graphboard").empty();*/
        $("#graphboard").highcharts('StockChart', {
            chart : {
                style: {
                    color: "#fff"
                },
                events : {
                    load : function() {
                        // set up the updating of the chart each second
                     /*   var series = this.series[0];
                        var interval = setInterval(function() {
                            try {
                           //     alert(sensors_data[id][0][0]+";"+sensors_data[id][sensors_data[id].length-1][0]);
                                series.xAxis.setExtremes(sensors_data[current_sensor][0][0],sensors_data[current_sensor][sensors_data[current_sensor].length-1][0]);
                              //  series.yAxis.setExtremes(sensors_data[id][0][1],sensors_data[id][sensors_data[id].length-1][1]);
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
            headerFormat: '<b>'+name+'</b><br>',
            pointFormat : '{point.y}',
            valueDecimals: 2,
            valueSuffix: ''+unit
            }, labels: {
                style: {
                	color: '#FFFFFF'
                }
            }, series : [{
                name : 'Random data',
                data : (function() {
                    
                    return sensors_data[current_sensor];
                })()
            }],xAxis: {labels: {
             style: {
                color: '#FFF'
             }
          }},yAxis: {labels: {
             style: {
                color: '#FFF'
             }
          }}
        });
    /*});*/
}

function setServer(i) {
    current_server=i;
    current_sensor=-1;
    $.ajax({
      type: 'GET',
      url: base_api+'get.php',
      data: { request: 'sensors', from: current_server},
      beforeSend:function(){
        already_empty = false;
        $("#tileset").children().fadeOut(400, function() {
            if(already_empty == false)
                $("#tileset").empty();
        });
        sensors_data.lenght = 0;
        NProgress.start();
      },
      success:function(data){
        NProgress.done();
        var sensors = JSON.parse(data);
        
        if($("#tileset").is(":empty")) {} else {
            already_empty = true;
            $("#tileset").empty();
        }
        
        for (var i = 0; i < sensors.length; ++i) {
            var s = sensors[i];
            sensors_meta[s.id] = [s.display_name,s.display_unit];
            $("#tileset").append("<article data-sensor-id="+s.id+" data-unit=\""+s.display_unit+"\" data-sensor-name=\""+s.display_name+"\" data-id="+i+" style=\"display:none\"><div><h3>"+s.display_name+"</h3><p></p><button class=\"glyphicon glyphicon-signal btn-lg\"></button></div><div style=\"display: none;\"><div class=\"quickgraph\"></div><button class=\"glyphicon glyphicon-ok btn-lg\"></button></div></article>");
        }
        if(sensors.length == 0) {
            $("#tileset").append("<article data-id=0 style=\"display:none\"><div><h3>Pas de capteurs sur cette station.</h3></article>");
        }
         $("#tileset").children().fadeIn(400);
        $(".container").shapeshift({
                column: 5,
                minColumn: 5,
                gutterX: 0, // Compensate for div border
                gutterY: 0, // Compensate for div border
                paddingX: 10,
                paddingY: 10
            });
        last_update = "2012-01-01 00:00:00";
        sensors_data.lenght = 0;
        
        
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

function majData() {
    var rq=current_server;
    var from_age = last_update;
    
    $.ajax({
      type: 'GET',
      url: base_api+'get.php',
      data: { request: 'data', from: from_age+"",server: current_server},
      beforeSend:function(){
      },
      success:function(data){
        values = JSON.parse(data);
        if(current_server == rq) {
            var children = $('#tileset').children();
            for(var c=0;c<children.length;c++){
               // var i = $(children[c]).find("p").data("sensor-id");
                var i = $(children[c]).data("sensor-id");
                var v = values[i];
                if(typeof v != 'undefined') {
                    if(v.length > 0) {
                        for(var curV = 0; curV < v.length;curV++) {
                            if(typeof sensors_data[i] == 'undefined')
                                sensors_data[i] = [];
                                
                            var t = moment(v[curV][0]).unix()*1000;
                            
                            last_update = v[curV][0];
                            
                            var crvalue = parseFloat(v[curV][1]);
                            var already=false;
                            for(var azer=0;azer<sensors_data[i].length && !already;azer++) 
                                if(sensors_data[i][azer][0] == t)
                                    already=true;
                            
                            if(!already)
                                sensors_data[i].push([t,crvalue]);
                        }
                        $(children[c]).find("p").html(v.pop()[1] + " " + $(children[c]).data("unit"));
                        
                    }
                    if(typeof sensors_data[i] != 'undefined') {
                        var newDelta = new Date().getTime() - sensors_data[i][sensors_data[i].length-1][0];
                        if(newDelta < last_update_delta)
                            last_update_delta = newDelta;
                    }
                }
            };
        }
        
        if(last_update_delta==10000000000)
            $(".last_update_delta").html("trop longtemps");
        else {
            var secs = Math.round(last_update_delta/1000);
            var mins = Math.floor(secs/60);
            secs = secs - mins*60;
            var hours = Math.floor(mins/60);
            mins = mins - hours*60;
            var days = Math.floor(hours/24);
            hours = hours - days*24;
            
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
        setTimeout("majData()",1000);
      },
      error:function(){}
    });
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






function DateTime() {
    function getDaySuffix(a) {
        var b = "" + a,
            c = b.length,
            d = parseInt(b.substring(c-2, c-1)),
            e = parseInt(b.substring(c-1));
        if (c == 2 && d == 1) return "th";
        switch(e) {
            case 1:
                return "st";
                break;
            case 2:
                return "nd";
                break;
            case 3:
                return "rd";
                break;
            default:
                return "th";
                break;
        };
    };

    this.getDoY = function(a) {
        var b = new Date(a.getFullYear(),0,1);
    return Math.ceil((a - b) / 86400000);
    }

    this.date = arguments.length == 0 ? new Date() : new Date(arguments);

    this.weekdays = new Array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
    this.months = new Array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
    this.daySuf = new Array( "st", "nd", "rd", "th" );

    this.day = {
        index: {
            week: "0" + this.date.getDay(),
            month: (this.date.getDate() < 10) ? "0" + this.date.getDate() : this.date.getDate()
        },
        name: this.weekdays[this.date.getDay()],
        of: {
            week: ((this.date.getDay() < 10) ? "0" + this.date.getDay() : this.date.getDay()) + getDaySuffix(this.date.getDay()),
            month: ((this.date.getDate() < 10) ? "0" + this.date.getDate() : this.date.getDate()) + getDaySuffix(this.date.getDate())
        }
    }

    this.month = {
        index: (this.date.getMonth() + 1) < 10 ? "0" + (this.date.getMonth() + 1) : this.date.getMonth() + 1,
        name: this.months[this.date.getMonth()]
    };

    this.year = this.date.getFullYear();

    this.time = {
        hour: {
            meridiem: (this.date.getHours() > 12) ? (this.date.getHours() - 12) < 10 ? "0" + (this.date.getHours() - 12) : this.date.getHours() - 12 : (this.date.getHours() < 10) ? "0" + this.date.getHours() : this.date.getHours(),
            military: (this.date.getHours() < 10) ? "0" + this.date.getHours() : this.date.getHours(),
            noLeadZero: {
                meridiem: (this.date.getHours() > 12) ? this.date.getHours() - 12 : this.date.getHours(),
                military: this.date.getHours()
            }
        },
        minute: (this.date.getMinutes() < 10) ? "0" + this.date.getMinutes() : this.date.getMinutes(),
        seconds: (this.date.getSeconds() < 10) ? "0" + this.date.getSeconds() : this.date.getSeconds(),
        milliseconds: (this.date.getMilliseconds() < 100) ? (this.date.getMilliseconds() < 10) ? "00" + this.date.getMilliseconds() : "0" + this.date.getMilliseconds() : this.date.getMilliseconds(),
        meridiem: (this.date.getHours() > 12) ? "PM" : "AM"
    };

    this.sym = {
        d: {
            d: this.date.getDate(),
            dd: (this.date.getDate() < 10) ? "0" + this.date.getDate() : this.date.getDate(),
            ddd: this.weekdays[this.date.getDay()].substring(0, 3),
            dddd: this.weekdays[this.date.getDay()],
            ddddd: ((this.date.getDate() < 10) ? "0" + this.date.getDate() : this.date.getDate()) + getDaySuffix(this.date.getDate()),
            m: this.date.getMonth() + 1,
            mm: (this.date.getMonth() + 1) < 10 ? "0" + (this.date.getMonth() + 1) : this.date.getMonth() + 1,
            mmm: this.months[this.date.getMonth()].substring(0, 3),
            mmmm: this.months[this.date.getMonth()],
            yy: (""+this.date.getFullYear()).substr(2, 2),
            yyyy: this.date.getFullYear()
        },
        t: {
            h: (this.date.getHours() > 12) ? this.date.getHours() - 12  : this.date.getHours() ,
            hh: (this.date.getHours() > 12) ? (this.date.getHours() - 12) < 10 ? "0" + (this.date.getHours() - 12) : this.date.getHours() - 12 : (this.date.getHours() < 10) ? "0" + this.date.getHours() : this.date.getHours(),
            hhh: this.date.getHours(),
            m: this.date.getMinutes(),
            mm: (this.date.getMinutes() < 10) ? "0" + this.date.getMinutes() : this.date.getMinutes(),
            s: this.date.getSeconds(),
            ss: (this.date.getSeconds() < 10) ? "0" + this.date.getSeconds() : this.date.getSeconds(),
            ms: this.date.getMilliseconds(),
            mss: Math.round(this.date.getMilliseconds()/10) < 10 ? "0" + Math.round(this.date.getMilliseconds()/10) : Math.round(this.date.getMilliseconds()/10),
            msss: (this.date.getMilliseconds() < 100) ? (this.date.getMilliseconds() < 10) ? "00" + this.date.getMilliseconds() : "0" + this.date.getMilliseconds() : this.date.getMilliseconds()
        }
    };

    this.formats = {
        compound: {
            commonLogFormat: this.sym.d.dd + "/" + this.sym.d.mmm + "/" + this.sym.d.yyyy + ":" + this.sym.t.hhh + ":" + this.sym.t.mm + ":" + this.sym.t.ss,
            exif: this.sym.d.yyyy + ":" + this.sym.d.mm + ":" + this.sym.d.dd + " " + this.sym.t.hhh + ":" + this.sym.t.mm + ":" + this.sym.t.ss,
            /*iso1: "",
            iso2: "",*/
            mySQL: this.sym.d.yyyy + "-" + this.sym.d.mm + "-" + this.sym.d.dd + " " + this.sym.t.hhh + ":" + this.sym.t.mm + ":" + this.sym.t.ss,
            postgreSQL1: this.sym.d.yyyy + "." + this.getDoY(this.date),
            postgreSQL2: this.sym.d.yyyy + "" + this.getDoY(this.date),
            soap: this.sym.d.yyyy + "-" + this.sym.d.mm + "-" + this.sym.d.dd + "T" + this.sym.t.hhh + ":" + this.sym.t.mm + ":" + this.sym.t.ss + "." + this.sym.t.mss,
            //unix: "",
            xmlrpc: this.sym.d.yyyy + "" + this.sym.d.mm + "" + this.sym.d.dd + "T" + this.sym.t.hhh + ":" + this.sym.t.mm + ":" + this.sym.t.ss,
            xmlrpcCompact: this.sym.d.yyyy + "" + this.sym.d.mm + "" + this.sym.d.dd + "T" + this.sym.t.hhh + "" + this.sym.t.mm + "" + this.sym.t.ss,
            wddx: this.sym.d.yyyy + "-" + this.sym.d.m + "-" + this.sym.d.d + "T" + this.sym.t.h + ":" + this.sym.t.m + ":" + this.sym.t.s
        },
        constants: {
            atom: this.sym.d.yyyy + "-" + this.sym.d.mm + "-" + this.sym.d.dd + "T" + this.sym.t.hhh + ":" + this.sym.t.mm + ":" + this.sym.t.ss,
            cookie: this.sym.d.dddd + ", " + this.sym.d.dd + "-" + this.sym.d.mmm + "-" + this.sym.d.yy + " " + this.sym.t.hhh + ":" + this.sym.t.mm + ":" + this.sym.t.ss,
            iso8601: this.sym.d.yyyy + "-" + this.sym.d.mm + "-" + this.sym.d.dd + "T" + this.sym.t.hhh + ":" + this.sym.t.mm + ":" + this.sym.t.ss,
            rfc822: this.sym.d.ddd + ", " + this.sym.d.dd + " " + this.sym.d.mmm + " " + this.sym.d.yy + " " + this.sym.t.hhh + ":" + this.sym.t.mm + ":" + this.sym.t.ss,
            rfc850: this.sym.d.dddd + ", " + this.sym.d.dd + "-" + this.sym.d.mmm + "-" + this.sym.d.yy + " " + this.sym.t.hhh + ":" + this.sym.t.mm + ":" + this.sym.t.ss,
            rfc1036: this.sym.d.ddd + ", " + this.sym.d.dd + " " + this.sym.d.mmm + " " + this.sym.d.yy + " " + this.sym.t.hhh + ":" + this.sym.t.mm + ":" + this.sym.t.ss,
            rfc1123: this.sym.d.ddd + ", " + this.sym.d.dd + " " + this.sym.d.mmm + " " + this.sym.d.yyyy + " " + this.sym.t.hhh + ":" + this.sym.t.mm + ":" + this.sym.t.ss,
            rfc2822: this.sym.d.ddd + ", " + this.sym.d.dd + " " + this.sym.d.mmm + " " + this.sym.d.yyyy + " " + this.sym.t.hhh + ":" + this.sym.t.mm + ":" + this.sym.t.ss,
            rfc3339: this.sym.d.yyyy + "-" + this.sym.d.mm + "-" + this.sym.d.dd + "T" + this.sym.t.hhh + ":" + this.sym.t.mm + ":" + this.sym.t.ss,
            rss: this.sym.d.ddd + ", " + this.sym.d.dd + " " + this.sym.d.mmm + " " + this.sym.d.yy + " " + this.sym.t.hhh + ":" + this.sym.t.mm + ":" + this.sym.t.ss,
            w3c: this.sym.d.yyyy + "-" + this.sym.d.mm + "-" + this.sym.d.dd + "T" + this.sym.t.hhh + ":" + this.sym.t.mm + ":" + this.sym.t.ss
        },
        pretty: {
            a: this.sym.t.hh + ":" + this.sym.t.mm + "." + this.sym.t.ss + this.time.meridiem + " " + this.sym.d.dddd + " " + this.sym.d.ddddd + " of " + this.sym.d.mmmm + ", " + this.sym.d.yyyy,
            b: this.sym.t.hh + ":" + this.sym.t.mm + " " + this.sym.d.dddd + " " + this.sym.d.ddddd + " of " + this.sym.d.mmmm + ", " + this.sym.d.yyyy
        }
    };
};