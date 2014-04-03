<?php

$action = "";
if(isset($_GET["action"]))
    $action = $_GET["action"];



if($action == "auth") {
    $pwd = isset($_GET["pwd"]) ? $_GET["pwd"] : "";
    
    if(sha1("42".$pwd."42") == "9b27cf540247e294354d290693725ded0e781739") {
        die("ok");
    }
    
    die("ko");
}

die("ko");
?>