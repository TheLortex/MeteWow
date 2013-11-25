<?php
include("datamanager.php");

if(isset($_GET["add"])&& (isset($_GET["mac"])) && (isset($_GET["secret"]))) {
    $request = $_GET["add"];
	$mac = $_GET["mac"];
	$secret = $_GET["secret"];
    
    if($request == "server") {
        $datamgr = new DataManager();
        echo $datamgr->registerServer($mac,$secret);
    } else if($request == "sensor") {
		$name = $_GET["name"];
		$unit = $_GET["unit"];
		$category = $_GET["cate"];
		
        $datamgr = new DataManager();
        echo $datamgr->registerSensor($mac,$secret,$name,$unit,$category)->id;
	} else if($erquest == "data") {
		$sid = $_GET["sensor_id"];
		$value = $_GET["value"];
		$time = $_GET["time"];
		
        $datamgr = new DataManager();
        $datamgr->addData($mac,$secret,$sid,$value,$time);
        echo "ok";
	} else {
		die("ko");
	}
} else {
    die("ko");
}?>
