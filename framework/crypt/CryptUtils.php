<?php //The main crypt utils

    namespace becwork\utils;

    class CryptUtils {

        /*
          * Base64 gen function
          * Input: String or file (img, etc.)
          * Return: Base64 code
        */
        public function genBase64($string) {
            return base64_encode($string);
        }

        /*
          * Base64 decode function
          * Input: base64 code
          * Return: string or file
        */
        public function decodeBase64($base64) {
            return base64_decode($base64);
        }

        /*
          * AES128 AES encrypt
          * Input: string or file, encrypt key
          * Return: encrypted string
        */
        public function encryptAES($plaintext, $password, $method) {
          
            $salt = openssl_random_pseudo_bytes(8);
            $salted = '';
            $dx = '';
          
            while (strlen($salted) < 48) {
                $dx = md5($dx.$password.$salt, true);
                $salted .= $dx;
            }
          
            $key = substr($salted, 0, 32);
            $iv  = substr($salted, 32,16);
            $encrypted_data = openssl_encrypt(json_encode($plaintext), $method, $key, true, $iv);
            $data = array("ct" => base64_encode($encrypted_data), "iv" => bin2hex($iv), "s" => bin2hex($salt));
          
            return json_encode($data);
        }

        /*
          * AES128 AES decrypt
          * Input: string or file, decrypt key
          * Return: decrypted string
        */
        public function decryptAES($jsonString, $password, $method) {
          
            $jsondata = json_decode($jsonString, true);
            $salt = hex2bin($jsondata["s"]);
            $ct = base64_decode($jsondata["ct"]);
            $iv  = hex2bin($jsondata["iv"]);
            $concatedPassphrase = $password.$salt;
            $md5 = array();
            $md5[0] = md5($concatedPassphrase, true);
            $result = $md5[0];
          
            for ($i = 1; $i < 3; $i++) {
                $md5[$i] = md5($md5[$i - 1].$concatedPassphrase, true);
                $result .= $md5[$i];
            }
          
            $key = substr($result, 0, 32);
            $data = openssl_decrypt($ct, $method, $key, true, $iv);
            return json_decode($data, true);
        }
    }
?>