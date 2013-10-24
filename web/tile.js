var target=null;

$(document).ready(function() {

    if ($("#test").addEventListener) {
        $("#test").addEventListener('contextmenu', function(e) {
            alert("You've tried to open context menu"); //here you draw your own menu
            e.preventDefault();
        }, false);
    } else {
        $('body').on('contextmenu', 'article', function() {
            document.getElementById("rmenu").className = "show";  
            $("#rmenu").css("top",mouseY(window.event));
            $("#rmenu").css("left",mouseX(window.event));
            target = $(this);
            window.event.returnValue = false;
        });
    }
    
    $(document).bind("click", function(event) {
        document.getElementById("rmenu").className = "hide";
    });
});

function mouseX(evt) {
    if (evt.pageX) return evt.pageX;
    else if (evt.clientX)
       return evt.clientX + (document.documentElement.scrollLeft ?
       document.documentElement.scrollLeft :
       document.body.scrollLeft);
    else return null;
}

function mouseY(evt) {
    if (evt.pageY) return evt.pageY;
    else if (evt.clientY)
       return evt.clientY + (document.documentElement.scrollTop ?
       document.documentElement.scrollTop :
       document.body.scrollTop);
    else return null;
}

function setDimensions(x,y) {
    if(target !== null) {
        target.attr("data-ss-colspan",x);
        target.attr("data-ss-rowspan",y);
        $("#tileset").trigger("ss-rearrange");
        
        
        localStorage.setItem("row-"+target.data("id"), target.data("ss-rowspan"));
        localStorage.setItem("col-"+target.data("id"), target.data("ss-colspan"));
    } 
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
            p.hide();
            
            sh.css("-webkit-transform","rotateY(-90deg)");
            sh.show();
            
            sh.transition({
              rotateY: '0deg'
            }, function() {
                sh.first().highcharts('StockChart', {
                		chart : {
                			events : {
                				load : function() {
                					// set up the updating of the chart each second
                					var series = this.series[0];
                					setInterval(function() {
                						var x = (new Date()).getTime(), // current time
                						y = Math.round(Math.random() * 100);
                						series.addPoint([x, y], true, true);
                						
                						series.xAxis.setExtremes(x-60*1000,x);
                					}, 1000);
                				}
                			}
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
                		
                		yAxis: {
                		    min:0,
                		    max: 100
                		},
                		
                		title : {
                			text : 'Du random en live'
                		},
                		
                		exporting: {
                			enabled: false
                		},
                		
                		credits : {
                		    enabled: false  
                		},
                		
                		series : [{
                			name : 'Random data',
                			data : (function() {
                				// generate an array of random data
                				var data = [], time = (new Date()).getTime(), i;
                
                				for( i = -999; i <= 0; i++) {
                					data.push([
                						time + i * 1000,
                						Math.round(Math.random() * 100)
                					]);
                				}
                				return data;
                			})()
                		}]
                	});
            });
        });
    });
});