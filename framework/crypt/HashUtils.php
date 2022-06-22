<?php //The main hash class

    class HashUtils { 

        /*
          * Function for blowfish hash generate
          * Usage: genBlowFish("plaintext")
          * Input text type string
          * Returned final hash type string
        */
        public function genBlowFish($plainText) {
            $hashFromat = "$2y$10$"; 
            $salt = "123sbrznvdzvchpj8z5p5k";
            $hashFromat_salt = $hashFromat.$salt;
            return crypt($plainText, $hashFromat_salt);
        }


        /*
          * Function for genSHA1 hash generate
          * Usage: genSHA1("plaintext")
          * Input text type string
          * Returned final hash type string
        */
        public function genSHA1($plainText) {
            $hash = "*" . sha1(sha1($plainText, true));
            $hashFinal = strtoupper($hash);
            return $hashFinal;
        }

  
        /*
          * Function for hashMD5 hash generate
          * Usage: hashMD5("plaintext")
          * Input text type string
          * Returned final hash type string
        */
        public function hashMD5($plainText) {
            $hashFinal= hash('md5', $plainText);
            return $hashFinal;
        }


        /*
          * Function for generate sha256 hash form string
          * Usage: genSHA256("string");
          * Input: text in string
          * Returned final sha256 hash form string
        */
        public function genSHA256($string) {
            return hash('sha256', $string);
        }


        /*
          * Function for generate custom hash form string by name
          * Usage: customhash("string", "sha1");
          * Input: text in string, hash name in string
          * Returned final hash form string
        */
        public function customhash($string, $hash) {
            return hash($hash, $string);
        }
    }
?>