<?php
include("datamanager.php");

if(isset($_POST["add"])) {
    $request = $_POST["add"];
    if($request = "server") {
        $datamgr = new DataManager();
        echo $datamgr->registerServer("1.2.3","test");
    }
} else {
    die("ko");
}?>