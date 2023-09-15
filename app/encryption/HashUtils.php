<?php // string hash methods

	namespace becwork\utils;

	class HashUtils { 

		/*
		  * Function for blowfish hash generate
		  * Usage: genBlowFish("plaintext")
		  * Input text type string
		  * Returned final hash type string
		*/
		public function gen_main_hash($plain_text): string {
			$hash_fromat = "$2y$10$";
			$salt = "123sbrznvdzvchpj8z5p5k";
			$hash_fromat_salt = $hash_fromat.$salt;
			return crypt($plain_text, $hash_fromat_salt);
		}

		/*
		  * FUNCTION: hash_validate validate hash = string
		  * USAGE: hash_validate("password", "password_hash")
		  * Input text type string, hash type string
		  * RETURN: true or false
		*/
		public function hash_validate($plain_text, $hash): bool {

			// default state
			$state = false;

			// check if password verified
			if (password_verify($plain_text, $hash)) {
				$state = true;
			} 

			return $state;
		}

		/*
		  * FUNCTION: bcrypt hash generate
		  * USAGE: gen_bcrypt("plaintext", "cost int")
		  * Input text type string, hash cost type int
		  * RETURN: final hash type string
		*/
		public function gen_bcrypt($plain_text, $cost): string {
			$options = [
				'cost' => $cost
			];
			$hash_final = password_hash($plain_text, PASSWORD_BCRYPT, $options);
			return $hash_final;
		}

		/*
		  * FUNCTION: gen_sha1 hash generate
		  * USAGE: gen_sha1("plaintext")
		  * Input text type string
		  * RETURN: final hash type string
		*/
		public function gen_sha1($plain_text): string {
			$hash = "*" . sha1(sha1($plain_text, true));
			$hash_final = strtoupper($hash);
			return $hash_final;
		}

		/*
		  * FUNCTION: gen_md5 hash generate
		  * USAGE: gen_md5("plaintext")
		  * Input text type string
		  * RETURN: final hash type string
		*/
		public function gen_md5($plain_text): string {
			$hash_final = hash('md5', $plain_text);
			return $hash_final;
		}

		/*
		  * FUNCTION: generate sha256 hash form string
		  * USAGE: gen_sha256("string");
		  * INPUT: text in string
		  * RETURN: final sha256 hash form string
		*/
		public function gen_sha256($plain_text): string {
			$final_hash = hash('sha256', $plain_text);
			return $final_hash;
		}

		/*
		  * FUNCTION: generate custom hash form string by name
		  * USAGE: custom_hash("string", "sha1");
		  * INPUT: text in string, hash name in string
		  * RETURN: final hash form string
		*/
		public function custom_hash($plain_text, $hash): string {
			$final_hash = hash($hash, $plain_text);
			return $final_hash;
		}
	}
?>