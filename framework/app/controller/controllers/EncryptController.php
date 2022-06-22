<?php //Functions for Generator / Cryptor components

    class EncryptController {

        //Encode string -> base64 (For generator component)
        public function base64Encode($string) {

            global $mysqlUtils;
            global $cryptUtils;
            global $alertController;

            //Get String from form
            $stringToEncode = $mysqlUtils->escapeString($string, true, true);

            if (empty($stringToEncode)) {
                
                $alertController->flashError("Error encode string is empty!");

            } else {

                //encode
                $encoded = $cryptUtils->genBase64($stringToEncode);

                //Print alert with decoded string
                $alertController->falshEncryptorAlert("Base64 encoded string", '<textarea class="cryptMSGArea">'.$encoded.'</textarea>');
            
                //Save to crypted table
                $queryInsert = $mysqlUtils->insertQuery("INSERT INTO `crypted`(`algorithm`, `key`, `method`, `input`, `output`) VALUES ('base64', 'none', 'encode', '$stringToEncode', 'Base64')");

			    //Log process to mysql database 
			    $mysqlUtils->logToMysql("Encryptor", "User encoded string: $stringToEncode to base64");
            }
        }




        //Decode base64 -> string (For generator component)
        public function base64Decode($string) {

            global $mysqlUtils;
            global $cryptUtils;
            global $alertController;

            //Get String from form
            $stringToDecode = $mysqlUtils->escapeString($string, true, true);

            if (empty($stringToDecode)) {

                $alertController->flashError("Error decode string is empty!");

            } else {

                //Decode
                $decoded = $cryptUtils->decodeBase64($stringToDecode);

                //Print alert with decoded string
                $alertController->falshEncryptorAlert("Base64 decoded string", '<textarea class="cryptMSGArea">'.$decoded.'</textarea>');
            
                //Save to crypted table
                if (mb_check_encoding($decoded, 'UTF-8')) {
                    $queryInsert = $mysqlUtils->insertQuery("INSERT INTO `crypted`(`algorithm`, `key`, `method`, `input`, `output`) VALUES ('base64', 'none', 'decode', 'Base64', '$decoded')");
                }

                //Log process to mysql database 
                if ($stringToDecode < 150) {
                    $mysqlUtils->logToMysql("Encryptor", "User decoded base64 string: $stringToDecode");
                } else {
                    $mysqlUtils->logToMysql("Encryptor", "User decoded base64 string: [String is too long for log value save]");
                }
            }			
        }



        //Encode image -> base64 (For generator component)
        public function base64ImageEncode($image) {

            global $cryptUtils;
            global $stringUtils;
            global $mysqlUtils;
            global $pageConfig;
            global $alertController;

            //Check if file is not empty
            if (!empty($image['tmp_name'])) {

                //Get file extension
                $ext = substr(strrchr($image["name"], '.'), 1);

                //Get image file
                $imageFile = file_get_contents($image["tmp_name"]);

                //Check if file is image
                if ($ext == "gif" or $ext == "jpg" or $ext == "jpeg" or $ext == "jfif" or $ext == "pjpeg" or $ext == "pjp" or $ext == "png" or $ext == "webp" or $ext == "bmp" or $ext == "ico") {
                
                    //Encode image file to base64
                    $encodeImage = $cryptUtils->genBase64($imageFile);

                    //Escape image file
                    $encodeImageEscaped = $mysqlUtils->escapeString($encodeImage, true, true);

                    //Get image count for check if exist
                    $count = mysqli_fetch_assoc(mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT COUNT(*) AS count FROM image_uploader WHERE image='".$encodeImageEscaped."'"))["count"];

                    if ($count == "0") {

                        //Generate imgSpec value
                        $imgSpec = $stringUtils->genRandomStringAll(40);

                        //Get current data
                        $date = date('d.m.Y H:i:s');

                        //Insert query to mysql table images
                        $mysqlUtils->insertQuery("INSERT INTO `image_uploader`(`imgSpec`, `image`, `date`) VALUES ('$imgSpec', '$encodeImageEscaped', '$date')");				

                        //Save to crypted table
                        $queryInsert = $mysqlUtils->insertQuery("INSERT INTO `crypted`(`algorithm`, `key`, `method`, `input`, `output`) VALUES ('base64', 'none', 'Image encode', '$imgSpec', 'Base64')");

                        //Log to mysql
                        $mysqlUtils->logToMysql("Encryptor", "User encoded image to base64, image $imgSpec saved to image_uploader");	
                    
                    } else {

                        //Log to mysql
                        $mysqlUtils->logToMysql("Encryptor", "User encoded image to base64");	
                    }
                    
                    //Show alert with encoded image string
                    $alertController->falshEncryptorAlert("Base64 string of image", '<textarea class="cryptMSGArea">'.$encodeImage.'</textarea>');

                } else {
                    $alertController->flashError("The file must be type image");
                }
            } else {
                $alertController->flashError("Error image file is empty!");
            }
        }



        //Encode base64 -> image (For generator component)
        public function base64ImageDecode($string) {

            global $mysqlUtils;
            global $pageConfig;
            global $stringUtils;
            global $alertController;

            if (!empty($string)) {

                //Show alert with image
                $alertController->falshEncryptorAlert("Base64 decoded image", '<div id="lightgallery"><span data-src="data:image/png;base64,'.$string.'" data-sub-html="Decoded image"><img class="gallery_images" src="data:image/png;base64,'.$string.'"></span></div>');
   
                //Log to mysql
                $mysqlUtils->logToMysql("Encryptor", "User decoded image from base64");

            } else {
                $alertController->flashError("Error string is empty!");
            }
        }



        //Encrypt string -> aes (For generator component)
        public function aesEncrypt($stringToEncrypt, $encryptKey, $encryptMethod, $encryptBits) {

            global $mysqlUtils;
            global $alertController;
            global $cryptUtils;

            //Escape inputs
            $stringToEncrypt = $mysqlUtils->escapeString($stringToEncrypt, true, true);
            $encryptKey = $mysqlUtils->escapeString($encryptKey, true, true);
            $encryptMethod = $mysqlUtils->escapeString($encryptMethod, true, true);
            $encryptBits = $mysqlUtils->escapeString($encryptBits, true, true);

            //Check if inputs not empty
            if (empty($stringToEncrypt) or empty($encryptKey) or empty($encryptMethod) or empty($encryptBits)) {
                $alertController->flashError("Error fields is empty!");
            } else {
                
                //Check if encrypt method is valid
                if ($encryptMethod != "CBC" && $encryptMethod != "CTR" && $encryptMethod != "CFB" && $encryptMethod != "OFB") {
                    $alertController->flashError("Error encrypt method not found!");
                } else {

                    //Check if encrypt bits is valud
                    if ($encryptBits != "128" && $encryptBits != "192" && $encryptBits != "256") {
                        $alertController->flashError("Error encrypt bits not found!");
                    } else {

                        //Get encrypt method
                        $method = "AES-$encryptBits-$encryptMethod";
                    
                        //Encrypt
                        $encrypted = $cryptUtils->encryptAES($stringToEncrypt, $encryptKey, $method);

                        //Flash encrypted string in alert
                        $alertController->falshEncryptorAlert("$method encrypted string", '<textarea class="cryptMSGArea">'.$encrypted.'</textarea>');
                    
                        //Save to crypted table
                        $queryInsert = $mysqlUtils->insertQuery("INSERT INTO `crypted`(`algorithm`, `key`, `method`, `input`, `output`) VALUES ('AES', '$encryptKey', '$method encrypt', '$stringToEncrypt', 'AES')");

                        //Set key for log format
                        if (strlen($encryptKey) > 60) {
                            $encryptKey = "[too long key]";
                        }

                        //Log to mysql
                        if (strlen($stringToEncrypt) < 60) {
                            $mysqlUtils->logToMysql("Encryptor", "User encrypted: $stringToEncrypt to $method and key: $encryptKey");
                        } else {
                            $mysqlUtils->logToMysql("Encryptor", "User encrypted: [too long string] to $method and key: $encryptKey");
                        }
                    }
                }
            }
        }



        //Encrypt aes -> string (For generator component)
        public function aesDecrypt($stringToDecrypt, $decryptKey, $decryptMethod, $decryptBits) {

            global $mysqlUtils;
            global $alertController;
            global $cryptUtils;

            //Escape inputs
            $decryptKey = $mysqlUtils->escapeString($decryptKey, true, true);
            $decryptMethod = $mysqlUtils->escapeString($decryptMethod, true, true);
            $decryptBits = $mysqlUtils->escapeString($decryptBits, true, true);

            //Check if inputs not empty
            if (empty($stringToDecrypt) or empty($decryptKey) or empty($decryptMethod) or empty($decryptBits)) {
                $alertController->flashError("Error fields is empty!");
            } else {

                //Check if decrypt method is valid
                if ($decryptMethod != "CBC" && $decryptMethod != "CTR" && $decryptMethod != "CFB" && $decryptMethod != "OFB") {
                    $alertController->flashError("Error encrypt method not found!");
                } else {

                    //Check if decrypt bits is valud
                    if ($decryptBits != "128" && $decryptBits != "192" && $decryptBits != "256") {
                        $alertController->flashError("Error encrypt bits not found!");
                    } else {

                        //Get decrypt method
                        $method = "AES-$decryptBits-$decryptMethod";
                        
                        //Decrypt
                        $decrypted = $cryptUtils->decryptAES($stringToDecrypt, $decryptKey, $method);

                        //Flash decrypted string in alert
                        $alertController->falshEncryptorAlert("Decrypted $method string", '<textarea class="cryptMSGArea">'.$decrypted.'</textarea>');
                    
                        //Save to crypted table
                        $queryInsert = $mysqlUtils->insertQuery("INSERT INTO `crypted`(`algorithm`, `key`, `method`, `input`, `output`) VALUES ('AES', '$decryptKey', '$method decrypt', 'AES', '$decrypted')");

                        //Log to mysql
                        if ($stringToDecrypt < 150) {
                            $mysqlUtils->logToMysql("Encryptor", "User decrypted: $stringToDecrypt with $method");
                        } else {
                            $mysqlUtils->logToMysql("Encryptor", "User decrypted: too long string with $method");
                        }
                    }
                }
            }
        }




    }
?>
