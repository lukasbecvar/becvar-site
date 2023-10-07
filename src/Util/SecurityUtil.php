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

	public static function encrypt_aes(string $plain_text, string $method = 'AES-128-CBC'): string {
		
        // get encryption password
        $password = $_ENV['APP_SECRET'];

		$salt = openssl_random_pseudo_bytes(8);
		$salted = '';
		$dx = '';
		  
		while (strlen($salted) < 48) {
			$dx = md5($dx.$password.$salt, true);
			$salted .= $dx;
		}
		  
		$key = substr($salted, 0, 32);
		$iv  = substr($salted, 32,16);
		$encrypted_data = openssl_encrypt(json_encode($plain_text), $method, $key, true, $iv);
		$data = array("ct" => base64_encode($encrypted_data), "iv" => bin2hex($iv), "s" => bin2hex($salt));
		  
		return json_encode($data);
	}

	public static function decrypt_aes(string $json_string, string $method = 'AES-128-CBC'): string {
		  
        // get encryption password
        $password = $_ENV['APP_SECRET'];

		$json_data = json_decode($json_string, true);
		$salt = hex2bin($json_data["s"]);
		$ct = base64_decode($json_data["ct"]);
		$iv  = hex2bin($json_data["iv"]);
		$concated_passphrase = $password.$salt;
		$md5 = array();
		$md5[0] = md5($concated_passphrase, true);
		$result = $md5[0];
		  
		for ($i = 1; $i < 3; $i++) {
			$md5[$i] = md5($md5[$i - 1].$concated_passphrase, true);
			$result .= $md5[$i];
		}
		  
		$key = substr($result, 0, 32);
		$data = openssl_decrypt($ct, $method, $key, true, $iv);
		$decoded_string = json_decode($data, true);
		return $decoded_string;
	}
}
