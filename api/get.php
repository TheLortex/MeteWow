<?
include("datamanager.php");

if(isset($_POST["request"])) {
    if($_POST["request"] == "servers") {
        $datamgr = new DataManager();
        $data = $datamgr->getServers($_POST["from"]);
        echo json_encode($data);
    } else if($_POST["request"] == "sensors") {
        if(isset($_POST["from"])) {
            $datamgr = new DataManager();
            $data = $datamgr->getSensors($_POST["from"]);
            echo json_encode($data);
        } else {
            die("C'est mort.");
        }
    } else if($_POST["request"] == "data") {
        if(isset($_POST["from"]) && (isset($_POST["server"]) || isset($_POST["sensor"]))) {
            $all = !isset($_POST["sensor"]);
            $from = $_POST["from"];
            $to = (isset($_POST["to"])) ? $_POST["to"] : now();
            
            $datamgr = new DataManager();
            $data = array();
            if($all) {
                $sensors = $datamgr->getSensors($_POST["server"]);
                foreach($i in $sensors) {
                    $data[$sensors[i]] = $datamgr->getData($sensors[i],$from,$to);
                }
            } else {
                $data = $datamgr->getData($_POST["sensor"], $from, $to);
            }
            echo json_encode($data);
        } else {
            die("C'est mort.");
        }
    }
    
}
