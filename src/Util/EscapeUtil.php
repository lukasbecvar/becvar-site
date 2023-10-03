<?php

namespace App\Util;

/*
    EscapeUtil util provides string escape methods
*/

class EscapeUtil
{
    // replace html tags with secure chars
    public static function special_chars_strip(string $string): ?string {
        return htmlspecialchars($string, ENT_QUOTES);
    }
}
