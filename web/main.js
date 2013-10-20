$(window).resize(function() {
    if($("#locker").css("top") != "0px") {
        $("#locker").css("top",50-($("#locker").height()));
    }
});

var menuShowed=true;

function toggleShowMenu() {
    if(menuShowed) {
        $("#list_nav").css("height",0);
    } else {
        $("#list_nav").css("height","250px");
    }
    menuShowed = !menuShowed;
}


if  (document.getElementById){
(function(){

    //Stop Opera selecting anything whilst dragging.
    if (window.opera){
        document.write("<input type='hidden' id='Q' value=' '>");
    }
    
    var n = 500;
    var dragok = false;
    var y,x,d,dy,dx;
    
    var lastTop=0;
    var goingUp=true;
    function move(e){
        if (!e) e = window.event;
        if (dragok){
          var top = dy + e.clientY - y;
          if(top < lastTop) {
              goingUp=true;
          } else {
              goingUp=false;
          }
          lastTop=top;
          
          if(top <= 0) {
            $("#locker").css("top",top);
          }
          else {
            $("#locker").css("top",0);
          }
          
          return false;
        }
    }
    
    function down(e){
        if (!e) e = window.event;
        
        var temp = (typeof e.target != "undefined")?e.target:e.srcElement;
        
        if (temp.tagName != "HTML"|"BODY" && (temp.className.indexOf("draggable") == -1)){
            temp = (typeof temp.parentNode != "undefined")?temp.parentNode:temp.parentElement;
        }
        
        if (temp.className.indexOf("draggable") != -1){
    
         if (window.opera){
            document.getElementById("Q").focus();
         }
         dragok = true;
         temp.style.zIndex = n++;
         d = temp;
         dx = parseInt(document.getElementById("locker").style.left+0);
         dy = parseInt(document.getElementById("locker").style.top+0);
         x = e.clientX;
         y = e.clientY;
         document.onmousemove = move;
         return false;
        }
    }
    
    function up(){
        if(dragok) {
            if(goingUp) {
                $("#locker").animate({top: 50-($("#locker").height())}, 400);
                goingUp=false;
            } else {
                $("#locker").animate({top: 0}, 400);
                goingUp=true;
            }
        }
        lastTop=0;
        dragok = false;
        document.onmousemove = null;
        
            
    }
    
    document.onmousedown = down;
    document.onmouseup = up;
    
    })();
}//End.

(function ($) {
$.fn.disableSelection = function () {
    return this.each(function () {
        if (typeof this.onselectstart != 'undefined') {
            this.onselectstart = function() { return false; };
        } else if (typeof this.style.MozUserSelect != 'undefined') {
            this.style.MozUserSelect = 'none';
        } else {
            this.onmousedown = function() { return false; };
        }
    });
};
})(jQuery);

$(document).ready(function() {
    $('*').disableSelection();
    $('.carousel').carousel({
      interval: 4200,
      pause: "none"
    })
});