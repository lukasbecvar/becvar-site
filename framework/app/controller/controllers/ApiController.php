<?php // main api actions class

    namespace becwork\controllers;

    class ApiController {

        // get true or false api enabled check
        public function isApiEnabled() {

            global $pageConfig;

            // check if api enabled
            if ($pageConfig->getValueByName('apiEnable') == true) {
                return true;
            } else {
                return false;
            }
        }

        // get token from get parameter
        public function getToken() {

            global $mysqlUtils;

            // check if token set
            if (isset($_GET["token"])) {
                return $mysqlUtils->escapeString($_GET["token"], true, true);
            } else {
                return null;
            }         
        }

        // get value from query string
        public function getValue() {

            global $mysqlUtils;

            // check if value set
            if (isset($_GET["value"])) {
                return $mysqlUtils->escapeString($_GET["value"], true, true);
            } else {
                return null;
            }
        }  

        // check if api token is valid
        public function isTokenValid($token, $controlToken) {
            
            // check if token si null
            if ($token == null) {
                return null;
            } else {

                // check if token is same like controll
                if ($token == $controlToken) {
                    return "valid";
                } else {
                    return "invalid";
                }
            }
        }

        // send API headers
        public function sendAPIHeaders() {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST');
            header("Access-Control-Allow-Headers: X-Requested-With"); 
            header('Content-Type: application/json; charset=utf-8');
        }
        
        // print null value json
        public function printValueNull() {

            $this->sendAPIHeaders();

            // array builder
            $arr = [
                "status" => "ok",
                "errors" => 0,
                "values" => 0,
            ];

            // print final json
            echo json_encode($arr);           
        }

        // api status
        public function printApiStatus() {

            $this->sendAPIHeaders();

            // array builder
            $arr = [
                "status" => "ok",
                "errors" => 0,
                "values" => "status",
            ];

            // print final json
            echo json_encode($arr);            
        }

        // unknow value
        public function printUnknowValue() {

            $this->sendAPIHeaders();

            // array builder
            $arr = [
                "status" => "ko",
                "errors" => 1,
                "values" => $this->getValue(),
                "error" => "unkonw get value"
            ];

            // print final json
            echo json_encode($arr);              
        }

        // value list
        public function prntValueList() {

            $this->sendAPIHeaders();

            // array builder
            $arr = [
                "list",
                "status",
                "log"
            ];

            // print final json
            echo "Value list: " . json_encode($arr);              
        }

        // save log to mysql
        public function saveLog() {
            
            global $mysqlUtils;

            // get log name if is set
            if (empty($_GET["name"])) {
                $name = null;
            } else {
                $name = $mysqlUtils->escapeString($_GET["name"], true, true);
            }

             // get log log if is set
             if (empty($_GET["log"])) {
                $log = null;
            } else {
                $log = $mysqlUtils->escapeString($_GET["log"], true, true);
            }  
            
            // check if inputs is null
            if ($name == null) {

                $this->sendAPIHeaders();

                // array builder
                $arr = [
                    "status" => "ko",
                    "errors" => 1,
                    "values" => $this->getValue(),
                    "error" => "name get value is null"
                ];
    
                // print final json
                echo json_encode($arr);  

            } 
            
            // check if log is null
            else if ($log == null) {

                $this->sendAPIHeaders();

                // array builder
                $arr = [
                    "status" => "ko",
                    "errors" => 1,
                    "values" => $this->getValue(),
                    "error" => "log get value is null"
                ];
    
                echo json_encode($arr);  

            } else {

                //Log to mysql
                $mysqlUtils->logToMysql($name, $log);

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