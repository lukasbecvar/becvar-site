<?php // string utils

	namespace becwork\utils;
	
	class StringUtils { 

		/*
		  * FUNCTION: generate random lower string
		  * USAGE: echo gen_random_sring(20, "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ")
		  * INPUT string lenght, string allowed characters
		  * RETURN: random string in lowercase
		*/
		public function gen_random_sring($lenght, $permitted_chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"): ?string {
			$generated = substr(str_shuffle($permitted_chars), 0, $lenght);
			return $generated;
		}

		/*
		  * FUNCTION: generate random numbers
		  * USAGE: echo gen_random_number(20)
		  * INPUT string lenght
		  * RETURN: random numbers type string
		*/
		public static function gen_random_number($lenght): string {
			$permitted_chars = "0123456789";
			$generated = substr(str_shuffle($permitted_chars), 0, $lenght);
			return $generated;
		}
	}
?>