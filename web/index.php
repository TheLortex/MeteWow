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
        <link type="text/css" rel="stylesheet" href="css/jquery.gridster.css" />
        
            <!--<script type="text/javascript" src="//code.jquery.com/jquery-2.0.3.js"></script>-->
            <script type="text/javascript" src="js/lib/jquery.js"></script>
            <script src="http://crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/sha1.js"></script>

            <!--<script type="text/javascript" src="//code.jquery.com/ui/1.9.2/jquery-ui.js"></script>-->
            <link rel="stylesheet" type="text/css" href="//code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css">
            <script type="text/javascript" src="js/lib/jquery.gridster.js"></script>
            <script type="text/javascript" src="js/lib/jquery.transit.min.js"></script>
            <script type="text/javascript" src="js/lib/nprogress.js"></script>
            <script type="text/javascript" src="js/lib/jquery.fittext.js"></script>
          <!--  <script type="text/javascript" src="//momentjs.com/downloads/moment-with-langs.js"></script>-->
            
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
                
                    if(currentPage == 5) {
                        $("#listnav5").attr("class","ns");
                        $("#listnav5").fadeOut();
                    }
                
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
                    } else if(npage == 5) {
                        $("#listnav5").fadeIn();
                        
                        $("#content").css("top","-400vh");
                        $("#listnav1").attr("class","ns");
                        $("#listnav2").attr("class","ns");
                        $("#listnav3").attr("class","ns");
                        $("#listnav4").attr("class","ns");
                        $("#listnav5").attr("class","selected");
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
                    <li id="listnav5" onclick="changerPage(5)" style="display: none">Admin</li>
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
                    <div id="tileset" class="gridster">
                        <ul></ul>
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
                    <h2> Carte des stations MétéWow</h2>
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
                    <fieldset>
                        <h3> ↪ Tableau de bord </h3>
                        </br>
                        <div>
                            <span> Intervalle de temps pour les graphiques : </span>
                            <select>
                                <option value="3h">3 heures</option>
                                <option value="6h">6 heures</option>
                                <option value="12h">12 heures</option>
                                <option value="24h">24 heures</option>
                                <option value="3d">3 jours</option>
                                <option value="1w">1 semaine</option>
                            </select>
                            </br>
                            </br>
                            <button onclick="resetDisposition()">Réinitialiser la disposition des informations </button>
                        </div>
                    </fieldset>
                    <fieldset id="admin_fieldset">
                        <h3> ↪ Interface administrateur</h3>
                        </br>
                        <div>
                            <span> Mot de passe: </span> <input type="password" id="admin_passwd"/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <button onclick="authAdmin()">Accéder à l'interface administrateur</button>
                        </div>
                    </fieldset>
                </div>
                
            </div>
            
            <!-- UI ADMIN -->
            <div id="admin">
                <div class="cheval">
                    <h2> Interface admin</h2>
                    <div style="clear:both;"></div>
                </div>
            </div>
        
            <div style="clear:both;"></div>
        </div>
        
        

        <!--  Le lock screen. -->

        <div id="locker" style="display:block">
            <div class="cLock draggable">
               <div class="cLock_2 draggable">
                <!-- <h1 class="draggable">Il fait beau.<sub style="font-size:0.2em">(1)</sub></h1>-->
                    <h1 class="draggable">Bienvenue sur MétéWow</h1>
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
        
        <script type="text/javascript" src="js/main.js"> </script>
        <script type="text/javascript" src="js/tile.js"> </script>
        <script type="text/javascript" src="js/data.js"> </script>
        
        
        <script type="text/javascript" src="js/maps.js"> </script>
        <script type="text/javascript" src="js/admin.js"> </script>
        <script type="text/javascript">
        <?php 
            foreach($serverlist as $server) {
                if(is_numeric($server["lat"]) && is_numeric($server["lon"])) {
                    echo "L.marker([".$server["lat"].", ".$server["lon"]."]).addTo(map).on(\"click\",";
                ?>
                function() {
                    var id = <?php echo $server["id"];?>;
                    setServer(id);
                    changerPage(1);
                    $("#metewow_server_graph").val(id);
                    $("#metewow_server").val(id);
                });
                <?php
                }
            }
        ?>
        
        
        
        
        
        </script>
        
    </body>
</html>
