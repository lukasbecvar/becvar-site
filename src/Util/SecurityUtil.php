<?php

namespace App\Util;

/*
    Security util provides all security methods (escape, encrypt, hash)
*/

class SecurityUtil
{
    // replace html tags with secure chars
    public function escapeString(string $string): ?string 
    {
        return htmlspecialchars($string, ENT_QUOTES);
    }

    public function hash_validate(string $plain_text, string $hash): bool {
        
		// check if password verified
		if (password_verify($plain_text, $hash)) {
			return true;
		} else {
            return false;
        }
	}

	public function gen_bcrypt(string $plain_text, int $cost): string {
		$options = [
			'cost' => $cost
		];
		return password_hash($plain_text, PASSWORD_BCRYPT, $options);
	}
}
