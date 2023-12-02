<?php

namespace App\Util;

/**
 * SecurityUtil provides various security methods such as escaping, hashing, and encryption.
 */
class SecurityUtil
{
    /**
     * Escape a string for safe output in HTML.
     *
     * @param string $string The string to escape.
     *
     * @return string|null The escaped string or null on failure.
     */
    public function escapeString(string $string): ?string 
    {
        return htmlspecialchars($string, ENT_QUOTES);
    }

    /**
     * Validate a plain text against a hashed value.
     *
     * @param string $plain_text The plain text to validate.
     * @param string $hash The hashed value to compare against.
     *
     * @return bool Whether the validation is successful.
     */
    public function hashValidate(string $plain_text, string $hash): bool 
	{
		return password_verify($plain_text, $hash);
	}

    /**
     * Generate a bcrypt hash for the given plain text with the specified cost.
     *
     * @param string $plain_text The plain text to hash.
     * @param int $cost The cost parameter for bcrypt.
     *
     * @return string The generated bcrypt hash.
     */
	public function genBcryptHash(string $plain_text, int $cost): string 
	{
		return password_hash($plain_text, PASSWORD_BCRYPT, ['cost' => $cost]);
	}

    /**
     * Encrypt a string using AES-128-CBC method.
     *
     * @param string $plain_text The plain text to encrypt.
     * @param string $method The encryption method (default: AES-128-CBC).
     *
     * @return string The encrypted data in JSON format.
     */
	public static function encryptAes(string $plain_text, string $method = 'AES-128-CBC'): string {
		
        // get encryption password form app enviroment
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
		$encrypted_data = openssl_encrypt(json_encode($plain_text), $method, $key, 0, $iv);
		$data = array("ct" => base64_encode($encrypted_data), "iv" => bin2hex($iv), "s" => bin2hex($salt));
		  
		return json_encode($data);
	}

    /**
     * Decrypt an AES-128-CBC encrypted JSON string.
     *
     * @param string $json_string The JSON string containing encrypted data.
     * @param string $method The encryption method (default: AES-128-CBC).
     *
     * @return string The decrypted data in JSON format.
     */
	public static function decryptAes(string $json_string, string $method = 'AES-128-CBC'): string {
		  
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
		$data = openssl_decrypt($ct, $method, $key, 0, $iv);
		return json_decode($data, true);
	}
}
