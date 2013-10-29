<?php

abstract class DataManager {
    private $database;
    
    function __construct() {
        loadDatabase();
    }
    
    public function registerServer($mac,$secret) {
        $req = $database->prepare("INSERT INTO mtw_servers (mac,secret) VALUES (?,?)");
        $req->execute(array($mac,$secret));
        
        return $database->lastInsertId();
    }
    
    public function registerSensor($server_id, $secret, $display_name, $display_unit, $type) {
        if(!auth_server($server_id, $secret))
            die();
            
        $req = $database->prepare("INSERT INTO mtw_sensors (mtw_server_id,name,unit,category) VALUES (?,?,?,?)");
        $req->execute(array($server_id,$display_name,$display_unit,$type));
        
        //TODO: Create sensor object
    }
    public function addData($server_id, $secret, $sensor_id, $value, $time) {
        if(!auth_server($server_id, $secret))
            die();
            
        $req = $database->prepare("INSERT INTO mtw_data (mtw_sensor_id,value,time) VALUES (?,?,?)");
        $req->execute(array($sensor_id,$value,$time));
    }
    
    public abstract function getServers();  // return array of id of registered servers
    public abstract function getSensors($server_id); // return array of Sensor object;
    public abstract function getData($server_id, $sensor_id, $from, $to); // return array of pair<time, value>
    
    
    private function loadDatabase() {
        try {
            $database = new PDO("sqlite:db.sqlite");
            $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
            $database->exec("CREATE TABLE IF NOT EXISTS mtw_servers (id INTEGER PRIMARY KEY, mac VARCHAR(255) UNIQUE, secret VARCHAR(255))");
            $database->exec("CREATE TABLE IF NOT EXISTS mtw_sensors (id INTEGER PRIMARY KEY, mtw_server_id INTEGER, name VARCHAR(255), unit VARCHAR(255), category VARCHAR(4))");
            $database->exec("CREATE TABLE IF NOT EXISTS mtw_data    (id INTEGER PRIMARY KEY, mtw_sensor_id INTEGER, value DOUBLE, time TIMESTAMP DEFAULT CURRENT_TIMESTAMP())");
        } catch (Exception $e) {
           die('Erreur : '.$e->getMessage());
        }
    }
    
    private function auth_server($server_id, $secret) {
        return true;
    }
}
?>