
var map = L.map('mapview');
//var osmUrl='http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
var osmUrl='http://otile1.mqcdn.com/tiles/1.0.0/map/{z}/{x}/{y}.jpg';
var osmAttrib='Tiles Courtesy of <a href="http://www.mapquest.com/" target="_blank">MapQuest</a> <img src="http://developer.mapquest.com/content/osm/mq_logo.png"> — Map data © <a href="http://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors';
var osm = new L.TileLayer(osmUrl, {minZoom: 1, maxZoom: 99, attribution: osmAttrib});		
map.addLayer(osm);

map.setView([46, 4], 7);

var polyline = L.polyline([], {color: 'red'}).addTo(map);
   function height(bloc){
    var hauteur;

    if( typeof( window.innerWidth ) == 'number' )
	hauteur = window.innerHeight - 134;
    else if( document.documentElement && document.documentElement.clientHeight )
	hauteur = document.documentElement.clientHeight - 134;

    document.getElementById(bloc).style.height = hauteur+"px";
}

window.onload = function(){ height("mapview") };
window.onresize = function(){ height("mapview") };
