<?php

namespace App\Util;

/*
    Security util provides all security methods (escape, encrypt, hash)
*/

class SecurityUtil
{
    // replace html tags with secure chars
    public function escapeString(string $string): ?string {
        return htmlspecialchars($string, ENT_QUOTES);
    }
}
