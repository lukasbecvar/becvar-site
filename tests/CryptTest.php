#!/usr/bin/php
<?php //Basic test for crypt utils

    //Import util
    require_once("framework/crypt/CryptUtils.php");

    //Init crypter utils
    $cryptUtils = new CryptUtils();
    
    //Test base64 encoder
    if ($cryptUtils->genBase64("test") == "dGVzdA==") {
        echo"\033[32mBase64 Encode test -> dGVzdA== success\n";
    } else {
        echo"\033[31mBase64 Encode test -> dGVzdA== Failed\n";
    }

    //Test base64 decoder
    if ($cryptUtils->decodeBase64("dGVzdA==") == "test") {
        echo"\033[32mBase64 Decode dGVzdA== -> test success\n";
    } else {
        echo"\033[31mBase64 Decode dGVzdA== -> test Failed\n";
    }

    echo"\033[33m================================================================================\n";

?>