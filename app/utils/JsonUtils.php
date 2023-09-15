<?php // file get utils
    
    namespace becwork\utils;
    
    class JsonUtils {
 
        /*
          * FUNCTION: get json by target url
          * USAGE $json = get_json_from_url("https://www.becvar.xyz/api/becwork.json")
          * RETURN json object
        */
        public function get_json_from_url($target): ?array {

            // requst options
            $opts = [
                'http' => [
                        'method' => 'GET',
                        'header' => [
                            'User-Agent: PHP'
                        ]
                ]
            ];

            // create request context
            $context = stream_context_create($opts);

            // try get contents data
            try {

                // get data
                $data = file_get_contents($target, false, $context);
            } catch (Exception $e) {
                $data = null;
            }

            // decode data
            $data = json_decode(utf8_encode($data), true);
            return $data;
        }
    }
?>