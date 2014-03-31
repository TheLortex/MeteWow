<?php
require_once("factual-php-driver/Factual.php");

define("CATE_TEMP",0);
define("CATE_PRES",1);
define("CATE_HUMI",2);
define("CATE_VENT",3);
define("CATE_GPSLAT",4);
define("CATE_GPSLON",5);


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
			$req = $this->database->prepare("INSERT INTO mtw_servers (mac,secret,lastlat,lastlon) VALUES (?,?,43.6483994,-79.4857025)");
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
    
    public function getServers() {
        $result = $this->database->query("SELECT id,mac,lastlat,lastlon FROM mtw_servers");
        $ids = array();
        while($data = $result->fetch()) {
            $d["id"] = $data["id"];
            $d["mac"] = $data["mac"];
            
            $d["lat"] = $data["lastlat"];
            $d["lon"] = $data["lastlon"];
            $d["location"] = getLocation($d["lat"],$d["lon"]);
            
            $ids[] = $d;
            
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
    
    public function addData($mac, $secret, $sensor_id, $value, $time) {
        $sid = $this->auth_server($mac, $secret);
        if($sid == -1)
            die();
            
        $sensorlist = $this->getSensors($sid);
        $okay = false;
        foreach($sensorlist as $sensor) {
            if($sensor->id == $sensor_id) {
                $okay = true;
                $cate = $sensor->category;
            }
        }
        
        if($okay) {
            $req = $this->database->prepare("INSERT INTO mtw_data (mtw_sensor_id,value,time) VALUES (?,?,?)");
            $req->execute(array($sensor_id,$value,$time));
            
            if($cate == 4) {// lat
                $req = $this->database->prepare("UPDATE mtw_servers SET lastlat = ? WHERE mac = ?");
                $req->execute(array($value,$mac));
            }
            
            if($cate == 3) {//lon
                $req = $this->database->prepare("UPDATE mtw_servers SET lastlon = ? WHERE mac = ?");
                $req->execute(array($value,$mac));
            }
        } else {
            die("wrong server");
        }
    }
    
    
    public function getData($sensor_id, $from, $to) {
        $request =$this->database->prepare("SELECT value, time FROM mtw_data WHERE mtw_sensor_id=? AND time > ? AND time < ?");
        $request->execute(array($sensor_id, $from, $to));
        
        $values = array();
        
        while($data = $request->fetch()) {
            $d = array();
            $d[0] = strtotime($data["time"]);
            $d[1] = $data["value"];
            
            if(is_numeric($d[1]))
                $values[] = $d;
        }
        
        return $values;
    }
    
    
    private function loadDatabase() {
        try {
            ini_set('display_errors', 'On');
            error_reporting(E_ALL);
			$this->database = new PDO("sqlite:".realpath(dirname(__FILE__))."/db.sqlite"); 
			$this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
            $this->database->exec("CREATE TABLE IF NOT EXISTS mtw_servers (id INTEGER PRIMARY KEY, mac VARCHAR(255) UNIQUE, secret VARCHAR(255), lastlat DOUBLE, lastlon DOUBLE)");
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

function getLocation($lat, $lng) {
  /*  $factual = new Factual("ZS8U4Vxi0pEvgKcI3h9IvuUtH4RedoSUYrQEJASe","Y8ZJPvVWLGT3XxX10UwotLYwWxbyhGPl8khYHpdm");
 //   $point = new FactualPoint($lat,$lng);
    $res = $factual->reverseGeoCode($lat,$lng); 
    return $res["street"];*/


  $returnValue = NULL;
  $ch = curl_init();
  $url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&sensor=false";
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  $result = curl_exec($ch);
  $json = json_decode($result, TRUE);

  if (isset($json['results'])) {
    $result = $json['results'][0];

    foreach($result["address_components"] as $component) {
        if(in_array("locality",$component["types"])) {
            $returnValue = $component["short_name"];
        }
        
    }
  }
  return $returnValue;
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
