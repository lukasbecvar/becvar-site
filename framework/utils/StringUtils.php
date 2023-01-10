<?php //The String utils class

    namespace becwork\utils;

    class StringUtils { 

        /*
          * The function for generate random lower string
          * Usage like echo genRandomStringLower(20)
          * Input string lenght
          * Returned random string in lowercase
        */
        public function genRandomStringLower($lenght) {
            $permitted_chars = 'abcdefghijklmnopqrstuvwxyz';
            $generated = substr(str_shuffle($permitted_chars), 0, $lenght);
            return $generated;
        }

        /*
          * The function for generate random uper string
          * Usage like echo genRandomStringUper(20)
          * Input string lenght
          * Returned random string in upercase
        */
        public function genRandomStringUper($lenght) {
            $permitted_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $generated = substr(str_shuffle($permitted_chars), 0, $lenght);
            return $generated;
        }

        /*
          * The function for generate random string
          * Usage like echo genRandomStringAll(20)
          * Input string lenght
          * Returned random string
        */
        public function genRandomStringAll($lenght) {
            $permitted_chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $generated = substr(str_shuffle($permitted_chars), 0, $lenght);
            return $generated;
        }

        /*
          * The function for generate random special chars
          * Usage like echo genSpecialChars(20)
          * Input string lenght
          * Returned random special chars type string
        */
        public function genSpecialChars($lenght) {
            $permitted_chars = "!#$%&()*+,-./:;<=>?@[\]^_`{|}~";
            $generated = substr(str_shuffle($permitted_chars), 0, $lenght);
            return $generated;
        }

        /*
          * The function for generate random characters
          * Usage like echo genCombinated(20)
          * Input string lenght
          * Returned random chars type string
        */
        public static function genCombinated($lenght) {
            $permitted_chars = "0123456789!#$%&()*+,-./:;<=>?@[\]^_`{|}~0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
            $generated = substr(str_shuffle($permitted_chars), 0, $lenght);
            return $generated;
        }

        /*
          * The function for generate random numbers
          * Usage like echo genNumbrGenerator(20)
          * Input string lenght
          * Returned random numbers type string
        */
        public static function genNumbrGenerator($lenght) {
            $permitted_chars = "0123456789";
            $generated = substr(str_shuffle($permitted_chars), 0, $lenght);
            return $generated;
        }
    }
?>
