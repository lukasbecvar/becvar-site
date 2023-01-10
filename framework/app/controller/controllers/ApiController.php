<?php //The main api actions class

    namespace becwork\controllers;

    class ApiController {

        //Function for get true or false api enabled check
        public function isApiEnabled() {

            require_once("../framework/config/ConfigManager.php");
            $pageConfig = new ConfigManager();

            if ($pageConfig->getValueByName('apiEnable') == true) {
                return true;
            } else {
                return false;
            }
        }

        //Function for get token from get parameter
        public function getToken() {

            global $mysqlUtils;

            if (isset($_GET["token"])) {
                return $mysqlUtils->escapeString($_GET["token"], true, true);
            } else {
                return null;
            }         
        }

        //Function for get values from query string
        public function getValue() {

            global $mysqlUtils;

            if (isset($_GET["value"])) {
                return $mysqlUtils->escapeString($_GET["value"], true, true);
            } else {
                return null;
            }
        }  

        //Function for check if api token is valid
        public function isTokenValid($token, $controlToken) {
            if ($token == null) {
                return null;
            } else {
                if ($token == $controlToken) {
                    return "valid";
                } else {
                    return "invalid";
                }
            }
        }

        //Send API headers
        public function sendAPIHeaders() {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST');
            header("Access-Control-Allow-Headers: X-Requested-With"); 
            header('Content-Type: application/json; charset=utf-8');
        }
        
        //Print null value json
        public function printValueNull() {

            $this->sendAPIHeaders();

            $arr = [
                "status" => "ok",
                "errors" => 0,
                "values" => 0,
            ];

            echo json_encode($arr);           
        }

        //Print api status
        public function printApiStatus() {

            $this->sendAPIHeaders();

            $arr = [
                "status" => "ok",
                "errors" => 0,
                "values" => "status",
            ];

            echo json_encode($arr);            
        }

        //Print unknow value
        public function printUnknowValue() {

            $this->sendAPIHeaders();

            $arr = [
                "status" => "ko",
                "errors" => 1,
                "values" => $this->getValue(),
                "error" => "unkonw get value"
            ];

            echo json_encode($arr);              
        }

        //Print value list
        public function prntValueList() {

            $this->sendAPIHeaders();

            $arr = [
                "list",
                "status",
                "log"
            ];

            echo "Value list: " . json_encode($arr);              
        }

        //Save log to mysql
        public function saveLog() {
            
            global $mysqlUtils;

            //Get log name if is set
            if (empty($_GET["name"])) {
                $name = null;
            } else {
                $name = $mysqlUtils->escapeString($_GET["name"], true, true);
            }

             //Get log log if is set
             if (empty($_GET["log"])) {
                $log = null;
            } else {
                $log = $mysqlUtils->escapeString($_GET["log"], true, true);
            }  
            
            //Check if inputs is null
            if ($name == null) {

                $this->sendAPIHeaders();

                $arr = [
                    "status" => "ko",
                    "errors" => 1,
                    "values" => $this->getValue(),
                    "error" => "name get value is null"
                ];
    
                echo json_encode($arr);  

            } else if ($log == null) {

                $this->sendAPIHeaders();

                $arr = [
                    "status" => "ko",
                    "errors" => 1,
                    "values" => $this->getValue(),
                    "error" => "log get value is null"
                ];
    
                echo json_encode($arr);  

            } else {

                //Log to mysql
                $logFunc = $mysqlUtils->logToMysql($name, $log);

                // set api headers
                $this->sendAPIHeaders();

                // build response
                $arr = [
                    "status" => "ok",
                    "errors" => 1,
                    "values" => $this->getValue(),
                    "error" => "Log inserted"
                ];

                // print response
                echo json_encode($arr); 
            }
        }
    }
?>