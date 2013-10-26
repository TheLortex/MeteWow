<?php

class DataManager {
    private $database;
    
    public function registerServer($mac); // return server id;
    public function registerSensor($server_id, $display_name, $display_unit, $type); // return Sensor object;
    public function addData($server_id, $sensor_id, $value, $time = now());
    
    public function getServers();  // return array of id of registered servers
    public function getSensors($server_id); // return array of Sensor object;
    public function getData($server_id, $sensor_id, $from, $to = now()) // return array of pair<time, value>

}
?>