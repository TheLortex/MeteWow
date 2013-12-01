<?php
include("datamanager.php");

if(isset($_GET["request"])) {
    if($_GET["request"] == "servers") {
        $datamgr = new DataManager();
        $data = $datamgr->getServers();
        echo json_encode($data);
    } else if($_GET["request"] == "sensors") {
        if(isset($_GET["from"])) {
            $datamgr = new DataManager();
            $data = $datamgr->getSensors($_GET["from"]);
            echo json_encode($data);
        } else {
            die("C'est mort.");
        }
    } else if($_GET["request"] == "data") {
        if(isset($_GET["from"]) && (isset($_GET["server"]) || isset($_GET["sensor"]))) {
            $all = !isset($_GET["sensor"]);
            $from =  $_GET["from"];
            $to = (isset($_GET["to"])) ? $_GET["to"] : date('Y-m-d H:i:s', time());
        
            $datamgr = new DataManager();
            $data = array();
            if($all) {
                $sensors = $datamgr->getSensors($_GET["server"]);
                foreach($sensors as $s) {
                    $data[$s->id] = $datamgr->getData($s->id,$from,$to);
                }
            } else {
                $data = $datamgr->getData($_GET["sensor"], $from, $to);
            }
            echo json_encode($data);
        } else {
            die("C'est mort.");
        }
    }
    
}
