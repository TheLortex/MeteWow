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
        if(!sh.is("div"))
            sh = p.prev();
        
        p.transition({
          rotateY: '90deg'
        }, function() {
            p.hide();
            
            sh.css("-webkit-transform","rotateY(-90deg)");
            sh.show();
            
            sh.transition({
              rotateY: '0deg'
            });
        });
    });
});