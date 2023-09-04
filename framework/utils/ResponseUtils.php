<?php // response utils

	namespace becwork\utils;

	class ResponseUtils { 

		/*
		  * The function for get website status
		  * Usage like $status = checkOnline("https://becvar.xyz");
		  * Input domain url
		  * Returned Online or Offline string
		*/
		public function checkOnline($domain) {

			// default response output
			$responseOutput = "Offline";

			// curl init
			$curlInit = curl_init($domain);

			// set options
			curl_setopt($curlInit,CURLOPT_CONNECTTIMEOUT,10);
			curl_setopt($curlInit,CURLOPT_HEADER,true);
			curl_setopt($curlInit,CURLOPT_NOBODY,true);
			curl_setopt($curlInit,CURLOPT_RETURNTRANSFER,true);

			// execute curl
			$response = curl_exec($curlInit);

			// close curl
			curl_close($curlInit);
			
			// get response 
			if ($response) {
				$responseOutput = "Online";
			} 

			return $responseOutput;
		}

        /*
          * The function for send 404 error
        */
        public function send404Header() {
			http_response_code(404);
            header("HTTP/1.0 404 Not Found");
        }

        /*
          * The function for get service status
          * Usage like $status = serviceOnlineCheck("127.0.0.1", 25565);
          * Input server ip and port
          * Returned On or Of string
        */
        public function serviceOnlineCheck($ip, $port) {

			// default response output
			$responseOutput = "Offline";

			// open service socket
            $service = @fsockopen($ip, $port);

			// check is service online
            if($service >= 1) {
                $responseOutput = 'Online';
            }

			return $responseOutput;
        }
	}
?>