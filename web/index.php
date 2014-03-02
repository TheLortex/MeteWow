<?php
include "../api/datamanager.php";
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>MétéWow</title>

        <link type="text/css" rel="stylesheet" href="//cdn.jsdelivr.net/bootstrap/3.0.0/css/bootstrap.min.css" />
        <link type="text/css" rel="stylesheet" href="css/main.css" />
        <link type="text/css" rel="stylesheet" href="css/tile.css" />
        <link type="text/css" rel="stylesheet" href="css/nprogress.css" />
        
            <script type="text/javascript" src="//code.jquery.com/jquery-2.0.3.js"></script>
            <script type="text/javascript" src="//code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
            <link rel="stylesheet" type="text/css" href="//code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css">
            <script type="text/javascript" src="js/lib/jquery.shapeshift.min.js"></script>
            <script type="text/javascript" src="js/lib/jquery.transit.min.js"></script>
            <script type="text/javascript" src="js/lib/nprogress.js"></script>
            <script type="text/javascript" src="js/lib/jquery.fittext.js"></script>
            <script type="text/javascript" src="//momentjs.com/downloads/moment-with-langs.js"></script>
            
            <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.5.1/leaflet.css" />
 		    <script src="http://cdn.leafletjs.com/leaflet-0.5.1/leaflet.js"></script>
 		
            <script src="js/lib/highstock.js"></script>
            
            
		
            <script type="text/javascript">
                window.setTimeout("showStuff()",2500);
                function showStuff() {
                    $("#indicator").fadeIn();
                }
                
                var currentPage = 1 ;
                
                
                function changerPage(npage) {
                    if(npage == currentPage)
                        return;
                        
                        
                    if(npage == 1) {
                        $("#content").css("top","0vh");
                        $("#listnav1").attr("class","selected");
                        $("#listnav2").attr("class","ns");
                        $("#listnav3").attr("class","ns");
                        $("#listnav4").attr("class","ns");
                    } else if(npage == 2) {
                        $("#content").css("top","-100vh");
                        $("#listnav1").attr("class","ns");
                        $("#listnav2").attr("class","selected");
                        $("#listnav3").attr("class","ns");
                        $("#listnav4").attr("class","ns");
                    } else if(npage == 3) {
                        $("#content").css("top","-200vh");
                        $("#listnav1").attr("class","ns");
                        $("#listnav2").attr("class","ns");
                        $("#listnav3").attr("class","selected");
                        $("#listnav4").attr("class","ns");
                    } else if(npage == 4) {
                        $("#content").css("top","-300vh");
                        $("#listnav1").attr("class","ns");
                        $("#listnav2").attr("class","ns");
                        $("#listnav3").attr("class","ns");
                        $("#listnav4").attr("class","selected");
                    }
                   
                   
                    
                    currentPage = npage;
                }
            </script>
            <style type="text/css">
            .container {
                position: relative;
                width: 95%;
                padding-top: 10px;
            }
            
            .container > article {
                position: absolute;
            }
            
            .container .ss-placeholder-child {
                background: rgba(0,0,0,0.03);
                border: 1px dashed rgba(0,0,0,0.15);
            }</style>
    </head>
    <body>

        <!--  L'UI. -->

        <div id="content" style="top: 0vh">
            <nav id="sidebar">
                <h2 onclick="toggleShowMenu();">MENU</h2>
                <ul id="list_nav">
                    <li id="listnav1" class="selected" onclick="changerPage(1)">Tableau de bord</li>
                    <li id="listnav2" onclick="changerPage(2)" >Graphiques</li>
                    <li id="listnav3" onclick="changerPage(3)" >Carte</li>
                    <li id="listnav4" onclick="changerPage(4)" >Paramètres</li>
                </ul>
            </nav>
            <div id="main">
                <div class="cheval">
                    <h2>Phrase récapitulative.</h2>
                    <nav>
                        <select id="metewow_server" name="metewow_server">
                            <?php 
                                $dmgr = new DataManager();
                                $serverlist = $dmgr->getServers();
                                $nservers = count($serverlist);
                                
                                $s = $nservers > 1 ? "s" : "";
                                
                                foreach($serverlist as $server) {
                                    echo "<option value=\"".$server["id"]."\"> MTW_".sprintf('%04d', $server["id"])." : ".$server["mac"]." (".$server["location"].")</option>";
                                }
                                
                            ?>
                     </select>
                    </nav>
                    <div style="clear:both;"></div>
                </div>
                <div id="dashboard">
                    <div id="tileset" class="container">
                        
                    </div>
                </div>
            </div>
            
            <!-- UI GRAPH -->
            <div id="graphs">
                <div class="cheval">
                <h2>Graphiques </h2>
                    <nav>
                        <select id="metewow_sensor" name="metewow_sensor">
                            
                        </select>
                        <select id="metewow_server_graph" name="metewow_server_graph">
                            <?php 
                                foreach($serverlist as $server) {
                                    echo "<option value=\"".$server["id"]."\"> MTW_".sprintf('%04d', $server["id"])." : ".$server["mac"]."</option>";
                                }
                                
                            ?>
                        </select>
                    </nav>
                    <div style="clear:both;"></div>
                </div>
                <div id="graphboard" style="height:75%">
                
                </div>
            </div>
            
            
            
            <!-- UI CARTE -->
            <div id="map">
                <div class="cheval">
                    <h2> Carte des Stations MétéWow</h2>
                    <div style="clear:both;"></div>
                </div>
                <div id="mapview" style="height: 100%">
            
                </div>
            </div>
            
            <!-- UI PARAMETRES --> 
            <div id="params">
                <div class="cheval">
                    <h2>Paramètres</h2>
                    <div style="clear:both;"></div>
                </div>
                <div id="paramboard">
                </div>
                
            </div>
        
            <div style="clear:both;"></div>
        </div>
        
        
        
        <!-- Menu contextuel pour les tiles -->
        
        <div class="hide" id="rmenu">
            <ul>
                <li onclick="setDimensions(1,1); return false;">1x1</li>
                <li onclick="setDimensions(2,1); return false;">2x1</li>
                <li onclick="setDimensions(1,2); return false;">1x2</li>
                <li onclick="setDimensions(2,2); return false;">2x2</li>
            </ul>
        </div>


        <!--  Le lock screen. -->

        <div id="locker" style="display:block">
            <div class="cLock draggable">
               <div class="cLock_2 draggable">
                <!-- <h1 class="draggable">Il fait beau.<sub style="font-size:0.2em">(1)</sub></h1>-->
                    <h1 class="draggable">Bienvenue sur MeteWow</h1>
                    <img class="draggable" src="img/MeteWowToutCourtAlias.png" />
                    <h2 class="draggable" id="indicator" style="display:none">Cliquez pour commencer<h2>
                </div>
            </div>
           <!-- <p style="position: absolute; bottom: 50px; margin-left: 10px;">(1) Cette vérité générale est générale. <sub style="font-size:0.6em">(2)</sub><br />
                (2) Sauf en cas de chutes d'eau, telle que la pluie.
            </p>-->
            <div id="databar" class="draggable">
                <div class="row hidden-xs" >
                    <span class="col-lg-4 draggable"><strong><?php echo $nservers; ?></strong> serveur<?php echo $s; ?> MétéWow connecté<?php echo $s;?>.</span>
                    <span class="col-lg-4 draggable">Dernière mise à jour il y a <strong class="last_update_delta">trop longtemps</strong>.</span>
                    <span class="col-lg-4 draggable"><strong>Aucune</strong> alerte.</span>
                </div>
                <div class="carousel slide visible-xs">
                      <div class="carousel-inner">
                        <div class="item active draggable">
                            <p><strong><?php echo $nservers; ?></strong> serveur<?php echo $s; ?> MétéWow connecté<?php echo $s;?>.</p>
                        </div>
                        <div class="item draggable">
                            <p>Dernière mise à jour il y a <strong class="last_update_delta">trop longtemps</strong>.</p>
                        </div>
                        <div class="item draggable">
                            <p><strong>Aucune</strong> alerte.</p>
                        </div>
                      </div>
                </div>
            </div>
        </div>

        <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap/3.0.0/js/bootstrap.min.js"></script>
        
        <script type="text/javascript">
        
         $(window).load(function(){
          /*  var container = document.getElementById("tileset");
            var nodes = container.getElementsByTagName('article');
            var sortList = [];
            var l = nodes.length;
            for(var i=0;i<l;i++) {
                $(nodes[0]).attr("data-ss-colspan",parseInt(localStorage.getItem("col-"+i) || "1",10));
                $(nodes[0]).attr("data-ss-rowspan",parseInt(localStorage.getItem("row-"+i) || "1",10));
                
                sortList.push(nodes[0]); 
                container.removeChild(nodes[0]);
            }
            
            for(var i=0;i<l;i++) {
                var itemAtIndex = parseInt(localStorage.getItem(i) || (i+""),10);
                container.appendChild(sortList[itemAtIndex]); 
            }
             
            $(".container").shapeshift({
                gutterX: 0, // Compensate for div border
                gutterY: 0, // Compensate for div border
                paddingX: 10,
                paddingY: 10
            });*/
        });
        
        $(".container").on("ss-rearranged", function(e, selected) {
           $('.container').children().each(function() {
              localStorage.setItem($(this).index(), $(this).data("id"));
            });
        });
        </script>
        <script type="text/javascript" src="js/main.js"> </script>
        <script type="text/javascript" src="js/tile.js"> </script>
        <script type="text/javascript" src="js/data.js"> </script>
        
        
        <script type="text/javascript" src="js/maps.js"> </script>
        <script type="text/javascript">
        <?php 
            foreach($serverlist as $server) {
                echo "L.marker([".$server["lat"].", ".$server["lon"]."]).addTo(map);";
            }
        ?>
        
        </script>
        
    </body>
</html>
