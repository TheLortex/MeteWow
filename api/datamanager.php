<?php
define("CATE_TEMP",0);
define("CATE_PRES",1);
define("CATE_HUMI",2);
define("CATE_VENT",3);

class DataManager {
    private $database = null;
    
    function __construct() {
        $this->loadDatabase();
    }
    
    public function registerServer($mac,$secret) {
		$sid = $this->auth_server($mac,$secret);
		if($sid!= -1) {
			return $sid;
		} else {
			$req = $this->database->prepare("INSERT INTO mtw_servers (mac,secret) VALUES (?,?)");
			$req->execute(array($mac,$secret));
			
			return $this->database->lastInsertId();
		}
    }
    
    public function registerSensor($mac, $secret, $display_name, $display_unit, $type) {
		$sid = $this->auth_server($mac,$secret);
        if($sid == -1)
            die("Auth error");
            
        $req = $this->database->prepare("INSERT INTO mtw_sensors (mtw_server_id,name,unit,category) VALUES (?,?,?,?)");
        $req->execute(array($sid,$display_name,$display_unit,$type));
        $sensor = new Sensor($sid,$display_name,$display_unit,$type,$this->database->lastInsertId());
        return $sensor;
    }
    public function addData($mac, $secret, $sensor_id, $value, $time) {
        if($this->auth_server($mac, $secret) == -1)
            die();
            
        $req = $this->database->prepare("INSERT INTO mtw_data (mtw_sensor_id,value,time) VALUES (?,?,?)");
        $req->execute(array($sensor_id,$value,$time));
    }
    
    public function getServers() {
        $result = $this->database->query("SELECT id FROM mtw_servers");
        $ids = array();
        while($data = $result->fetch()) {
            $ids[] = $data["id"];
        }
        return $ids;
    }
    public function getSensors($server_id)  {
        $request = $this->database->prepare("SELECT * FROM mtw_sensors WHERE mtw_server_id=?");
        $request->execute(array($server_id));
        
        $sensors = array();
        while($data = $request->fetch()) {
            $s = new Sensor($data["mtw_server_id"],$data["name"],$data["unit"],$data["category"],$data["id"]);
            $sensors[] = $s;
        }
        return $sensors;
    }
    
    public function getData($sensor_id, $from, $to) {
        $request =$this->database->prepare("SELECT * FROM mtw_data WHERE mtw_sensor_id=? AND time >= ? AND time <= ?");
        $request->execute(array($sensor_id, $from, $to));
        
        $values = array();
        
        while($data = $request->fetch()) {
            $values[$data["time"]] = $data["value"];
        }
        
        return $values;
    }
    
    
    private function loadDatabase() {
        try {
            ini_set('display_errors', 'On');
            error_reporting(E_ALL);

			$this->database = new PDO("sqlite:db.sqlite");
			$this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
            $this->database->exec("CREATE TABLE IF NOT EXISTS mtw_servers (id INTEGER PRIMARY KEY, mac VARCHAR(255) UNIQUE, secret VARCHAR(255))");
            $this->database->exec("CREATE TABLE IF NOT EXISTS mtw_sensors (id INTEGER PRIMARY KEY, mtw_server_id INTEGER, name VARCHAR(255), unit VARCHAR(255), category VARCHAR(4))");
            $this->database->exec("CREATE TABLE IF NOT EXISTS mtw_data    (id INTEGER PRIMARY KEY, mtw_sensor_id INTEGER, value DOUBLE, time TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
        } catch (Exception $e) {
           die('Erreur : '.$e->getMessage());
        }
    }
    
    private function auth_server($mac, $secret) {
        $req = $this->database->prepare("SELECT * FROM mtw_servers WHERE mac=? AND secret=?");
        $req->execute(array($mac,$secret));
        if($data=$req->fetch())
			return $data["id"];
		return -1;
    }
}

class Sensor {
    public $mtw_server = 0;
    public $display_name = "";
    public $display_unit = "";
    public $category = "";
    public $id = 0;
    
    public function __construct($mtw_server_, $display_name_, $display_unit_, $category_, $id_) {
        $this->mtw_server = $mtw_server_;
        $this->display_name = $display_name_;
        $this->display_unit = $display_unit_;
        $this->category = $category_;
        $this->id = $id_;
    }
}
?>
