<?php

class DataManager {
    private $database;
    
    function __construct() {
        loadDatabase();
    }
    
    public function registerServer($mac,$secret); // return server id;
    public function registerSensor($server_id, $secret, $display_name, $display_unit, $type); // return Sensor object;
    public function addData($server_id, $secret, $sensor_id, $value, $time = now());
    
    public function getServers();  // return array of id of registered servers
    public function getSensors($server_id); // return array of Sensor object;
    public function getData($server_id, $sensor_id, $from, $to = now()) // return array of pair<time, value>
    
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
}
?>