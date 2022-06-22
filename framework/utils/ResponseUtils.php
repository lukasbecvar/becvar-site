<?php //The Response utils (Status getters utils, etc.)

	class ResponseUtils { 


        /*
            * The function for get website status
            * Usage like $status = checkOnline("https://becvar.xyz");
            * Input domain url
            * Returned Online or Offline string
        */
        public function checkOnline($domain) {

            $curlInit = curl_init($domain);

            curl_setopt($curlInit,CURLOPT_CONNECTTIMEOUT,10);
            curl_setopt($curlInit,CURLOPT_HEADER,true);
            curl_setopt($curlInit,CURLOPT_NOBODY,true);
            curl_setopt($curlInit,CURLOPT_RETURNTRANSFER,true);

            $response = curl_exec($curlInit);

            curl_close($curlInit);
            
            if ($response) {
            return "Online";
            } else {
            return "Offline";
            }
        }


        /*
          * The function for send 404 error
          * Usage: just call function
        */
        public function send404Header() {
            header("HTTP/1.0 404 Not Found");
        }


        /*
          * The fake apache errro draw
          * Usage like echo drawApache404ErrorIndex()
          * Returned apache not found page
        */
        public function drawApache404ErrorIndex() {
            $string = "<!DOCTYPE HTML PUBLIC '-//IETF//DTD HTML 2.0//EN'>\n<html><head>\n<title>404 Not Found</title>\n</head><body>\n<h1>Not Found</h1>\n<p>The requested URL was not found on this server.</p>\n</body></html>";
            return $string;
        }


        /*
          * The function for get service status
          * Usage like $status = serviceOnlineCheck("127.0.0.1", 25565);
          * Input server ip and port
          * Returned On or Of string
        */
        public function serviceOnlineCheck($ip, $port) {
            $service = @fsockopen($ip, $port, $errno, $errstr, 2);

            if($service >= 1) {
                return 'Online';
            } else {
                return 'Offline';
            }
        }
	}
?>