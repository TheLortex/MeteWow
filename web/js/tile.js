var target=null;

function mouseX(evt) {
    if (evt.pageX) return evt.pageX;
    else if (evt.clientX)
        return evt.clientX + (document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft);
    else return null;
}

function mouseY(evt) {
    if (evt.pageY) return evt.pageY;
    else if (evt.clientY)
        return evt.clientY + (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop);
    else return null;
}

$(document).ready(function() {
    $("body").on("mouseover","#tileset article div", function() {
        $(this).find("button").show();
    });
    $("body").on("mouseout","#tileset article div", function() {
        $(this).find("button").hide();
    });
    
    $("body").on("click","#tileset article div button", function() {
        var p = $(this).parent();
        var sh = p.next();
        var viewGraph = true;
        if(!sh.is("div")) {
            sh = p.prev();
        viewGraph = false;
    }
    
    p.transition({
        rotateY: '90deg'
        }, function() {
            if(!viewGraph) {
                p.find(".quickgraph").highcharts().destroy();
            }
            p.hide();
            sh.css("-webkit-transform","rotateY(-90deg)");
            sh.show();
            if(viewGraph) {
                createGraph(sh);
            }
            sh.transition({rotateY: '0deg'}, function() {});
        });
    });
});

function createGraph(section) {
    var id = section.parent().data("sensor-id");
    var name = section.parent().data("sensor-name");
    var unit = section.parent().data("unit");
    var container=section.find(".quickgraph");
    section.find(".quickgraph").highcharts('StockChart', {
        chart : {
            renderTo: $("#params"),
            events : {
                load : function() {
                // set up the updating of the chart each second
                graphiques[id] = this;
                var series = this.series[0];
                var interval = setInterval(function() {
                    try {
                        // alert(sensors_data[id][0][0]+";"+sensors_data[id][sensors_data[id].length-1][0]);
                        var last_t = sensors_data[id][sensors_data[id].length-1][0];
                        series.xAxis.setExtremes(last_t-24*3600*1000,last_t);
                        // series.yAxis.setExtremes(sensors_data[id][0][1],sensors_data[id][sensors_data[id].length-1][1]);
                        series.setData(sensors_data[id]);
                    } catch(err) {
                        clearInterval(interval);
                    }
                }, 1000);
            }
        },
            backgroundColor: "rgba(0,0,0,0.1)"
        },
            rangeSelector: {
            enabled: false
        },
            navigator: {
            enabled: false
        },
        scrollbar: {
            enabled: false
        },
        xAxis: {
            labels: {
                style: {
                    color: "white"
                }
            },
            range: 24 * 3600 * 1000 
        },
        yAxis: {
            gridColor: "rgba(1,1,1,0.5)",
            labels: {
                style: {
                    color: "white",
                    fontSize: "1.2em"
                }
            }
        },
        title : {
            text : name,
            style: {
                color: "white",
                fontSize: "2em"
            }
        },
        exporting: {
            enabled: false
        },
        tooltip: {
            enabled: true,
            shared: true, 
            formatter: function() {
                return  '<b>' + name +'</b><br/>' +
                    Highcharts.dateFormat('%e/%m/%y %H:%M:%S',
                                          new Date(this.x))
                + '  <br/>' + this.points[0].y.toFixed(2) + ' '+unit;
            }
        },
        credits : {
            enabled: false
        },
        series : [{ 
            name : 'data',
            gapSize: 10000,
            data : (function() {
            return sensors_data[id];
            })()
        }]
    });
    
}