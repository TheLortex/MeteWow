<?php
include "../api/datamanager.php";
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Un titre</title>

        <link type="text/css" rel="stylesheet" href="//cdn.jsdelivr.net/bootstrap/3.0.0/css/bootstrap.min.css" />
        <link type="text/css" rel="stylesheet" href="main.css" />
        <link type="text/css" rel="stylesheet" href="tile.css" />
        <link type="text/css" rel="stylesheet" href="nprogress.css" />
        
            <script type="text/javascript" src="//code.jquery.com/jquery-2.0.3.js"></script>
            <script type="text/javascript" src="//code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
            <link rel="stylesheet" type="text/css" href="//code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css">
            <script type="text/javascript" src="jquery.shapeshift.min.js"></script>
            <script type="text/javascript" src="jquery.transit.min.js"></script>
            <script type="text/javascript" src="nprogress.js"></script>
            <script src="highstock.js"></script>
            <script type="text/javascript">
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

        <div id="content">
            <nav id="sidebar">
                <h2 onclick="toggleShowMenu();">MENU</h2>
                <ul id="list_nav">
                    <li>Dashboard</li>
                    <li>Graphiques</li>
                    <li>Paramètres</li>
                </ul>
            </nav>
            <div id="main">
                <div id="cheval">
                    <h2>Phrase récapitulative.</h2>
                    <nav>
                        <select id="metewow_server" name="metewow_server">
                            <?php 
                                $dmgr = new DataManager();
                                $data = $dmgr->getServers();
                                foreach($data as $server) {
                                    echo "<option value=\"".$server["id"]."\"> MTW_".sprintf('%04d', $server["id"])." : ".$server["mac"]."</option>";
                                }
                                
                            ?>
                     </select>
                    </nav>
                    <div style="clear:both;"></div>
                </div>
                <div id="tileset" class="container">
                    
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
                  <h1 class="draggable">Il fait beau.<sub style="font-size:0.2em">(1)</sub></h1>
               </div>
            </div>
            <p style="position: absolute; bottom: 50px; margin-left: 10px;">(1) Cette vérité générale est générale. <sub style="font-size:0.6em">(2)</sub><br />
                (2) Sauf en cas de chutes d'eau, telle que la pluie.
            </p>
            <div id="databar" class="draggable">
                <div class="row hidden-xs" >
                    <span class="col-lg-4 draggable"><strong>42</strong> serveurs MétéWow connectés.</span>
                    <span class="col-lg-4 draggable">Dernière mise à jour il y a <strong>42 secondes</strong>.</span>
                    <span class="col-lg-4 draggable"><strong>Aucune</strong> alerte.</span>
                </div>
                <div class="carousel slide visible-xs">
                      <div class="carousel-inner">
                        <div class="item active draggable">
                            <p><strong>42</strong> serveurs MétéWow connectés.</p>
                        </div>
                        <div class="item draggable">
                            <p>Dernière mise à jour il y a <strong>42 secondes</strong>.</p>
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
            var container = document.getElementById("tileset");
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
            });
        });
        
        $(".container").on("ss-rearranged", function(e, selected) {
           $('.container').children().each(function() {
              localStorage.setItem($(this).index(), $(this).data("id"));
            });
        });
        </script>
        <script type="text/javascript" src="main.js"> </script>
        <script type="text/javascript" src="tile.js"> </script>
        <script type="text/javascript" src="data.js"> </script>
    </body>
</html>
