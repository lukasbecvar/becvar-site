<?php // string hash class

	namespace becwork\utils;

	class HashUtils { 

		/*
		  * Function for blowfish hash generate
		  * Usage: genBlowFish("plaintext")
		  * Input text type string
		  * Returned final hash type string
		*/
		public function genBlowFish($plain_text): string {
			$hash_fromat = "$2y$10$";
			$salt = "123sbrznvdzvchpj8z5p5k";
			$hash_fromat_salt = $hash_fromat.$salt;
			return crypt($plain_text, $hash_fromat_salt);
		}

		/*
		  * Function for genSHA1 hash generate
		  * Usage: genSHA1("plaintext")
		  * Input text type string
		  * Returned final hash type string
		*/
		public function genSHA1($plain_text): string {
			$hash = "*" . sha1(sha1($plain_text, true));
			$hash_final = strtoupper($hash);
			return $hash_final;
		}

		/*
		  * Function for hashMD5 hash generate
		  * Usage: hashMD5("plaintext")
		  * Input text type string
		  * Returned final hash type string
		*/
		public function hashMD5($plain_text): string {
			$hash_final= hash('md5', $plain_text);
			return $hash_final;
		}

		/*
		  * Function for generate sha256 hash form string
		  * Usage: genSHA256("string");
		  * Input: text in string
		  * Returned final sha256 hash form string
		*/
		public function genSHA256($string): string {
			return hash('sha256', $string);
		}

		/*
		  * Function for generate custom hash form string by name
		  * Usage: customhash("string", "sha1");
		  * Input: text in string, hash name in string
		  * Returned final hash form string
		*/
		public function customhash($string, $hash): string {
			return hash($hash, $string);
		}
	}
?>