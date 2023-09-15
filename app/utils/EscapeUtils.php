<?php // escape security utils

    namespace becwork\utils;

    class EscapeUtils {

        /*
          * The function for replace dangerous chars in string (XSS proteection)
          * Usage like special_chars_strip("<p>Ola</p>")
          * Input string
          * Returned secure string
        */
        public function special_chars_strip($string): ?string {
            $string = htmlspecialchars($string, ENT_QUOTES);
            return $string;
        }
    }
?>