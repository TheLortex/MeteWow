var current_server=0;

$("#metewow_server").change(function () {
    var id = $(this).val();
    setServer(id);
}).trigger('change');


function setServer(i) {
    current_server=i;
    $.ajax({
      type: 'GET',
      url: 'http://lortex.org/metewow/api/get.php',
      data: { request: 'sensors', from: i },
      beforeSend:function(){
        $("#tileset").empty();
      },
      success:function(data){
        var sensors = JSON.parse(data);
        for (var i = 0; i < sensors.length; ++i) {
            var s = sensors[i];
            $("#tileset").append("<article data-id="+i+"><div><h3>"+s.display_name+"</h3><p data-sensor-id="+s.id+" data-unit=\""+s.display_unit+"\"></p><button class=\"glyphicon glyphicon-signal btn-lg\"></button></div><div style=\"display: none;\"><div class=\"quickgraph\"></div><button class=\"glyphicon glyphicon-ok btn-lg\"></button></div></article>");
        }
        if(sensors.length == 0) {
            $("#tileset").append("<article data-id=0><div><h3>Pas de capteurs sur cette station.</h3></article>");
        }
        
        $(".container").shapeshift({
            gutterX: 0, // Compensate for div border
            gutterY: 0, // Compensate for div border
            paddingX: 10,
            paddingY: 10
        });
      },
      error: function(xhr,textStatus,err)
{
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
    
    $.ajax({
      type: 'GET',
      url: 'http://lortex.org/metewow/api/get.php',
      data: { request: 'data', from: '2012-01-01 00:00:00',server: current_server},
      beforeSend:function(){},
      success:function(data){
        values = JSON.parse(data);
        if(current_server == rq) {
            var children = $('#tileset').children();
            for(var c=0;c<children.length;c++){
                var i = $(children[c]).find("p").data("sensor-id");
                var v = values[i];
                if(v.length > 0)
                    $(children[c]).find("p").html(v.pop()[1] + " " + $(children[c]).find("p").data("unit"));
            };
        }
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