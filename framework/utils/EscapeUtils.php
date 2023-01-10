<?php // escape security utils

    namespace becwork\utils;

    class EscapeUtils {

        /*
          * The function for replace dangerous chars in string (XSS proteection)
          * Usage like specialCharshStrip("<p>Ola</p>")
          * Input string
          * Returned secure string
        */
        public function specialCharshStrip($string) {
            return htmlspecialchars($string, ENT_QUOTES);
        }
    }
?>
