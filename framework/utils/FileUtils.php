<?php //The File utils class

    class FileUtils {
 
        /*
          * The function for get json
          * Usage like $json = getJsonFromUrl("https://www.becvar.xyz/api/becwork.json")
          * Returned json object
        */
        public function getJsonFromUrl($target) {
            $json = file_get_contents($target);
            return $json_a = json_decode(utf8_encode($json), true);
        }
    }
?>