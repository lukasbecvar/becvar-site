<?php // response utils

	namespace becwork\utils;

	class ResponseUtils { 

		/*
		  * The function for get website status
		  * Usage like $status = is_url_online("https://becvar.xyz");
		  * Input domain url
		  * Returned Online or Offline string
		*/
		public function is_url_online($domain): string {

			// default response output
			$response_output = "Offline";

			// curl init
			$curl_init = curl_init($domain);

			// set options
			curl_setopt($curl_init, CURLOPT_CONNECTTIMEOUT,10);
			curl_setopt($curl_init, CURLOPT_HEADER,true);
			curl_setopt($curl_init, CURLOPT_NOBODY,true);
			curl_setopt($curl_init, CURLOPT_RETURNTRANSFER,true);

			// execute curl
			$response = curl_exec($curl_init);

			// close curl
			curl_close($curl_init);
			
			// get response 
			if ($response) {
				$response_output = "Online";
			} 

			return $response_output;
		}

        /*
          * The function for get service status
          * Usage like $status = is_service_online("127.0.0.1", 25565);
          * Input server ip and port
          * Returned On or Of string
        */
        public function is_service_online($ip, $port): string {

			// default response output
			$response_output = "Offline";

			// open service socket
            $service = @fsockopen($ip, $port);

			// check is service online
            if($service >= 1) {
                $response_output = 'Online';
            }

			return $response_output;
        }
	}
?>