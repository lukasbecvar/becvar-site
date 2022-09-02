#!/usr/bin/php
<?php //Basic test for crypt utils

    //Import util
    require_once("framework/crypt/HashUtils.php");

    //Init hash funnctions
    $hashUtils = new HashUtils();

    //test Blowfish (AKA Bcrypt idk)
    if ($hashUtils->genBlowFish("test") == "$2y$10$123sbrznvdzvchpj8z5p5eVspVSYEKrDeIU2Rz907rTjDhWl3bCH2") {
        echo"\033[32mBlowFish hash 'test' -> $2y$10$123sbrznvdzvchpj8z5p5eVspVSYEKrDeIU2Rz907rTjDhWl3bCH2 success\n";
    } else {
        echo"\033[31mBlowFish Encode 'test' -> $2y$10$123sbrznvdzvchpj8z5p5eVspVSYEKrDeIU2Rz907rTjDhWl3bCH2 Failed\n";
    }

    //test SHA1 hash
    if ($hashUtils->genSHA1("test") == "*94BDCEBE19083CE2A1F959FD02F964C7AF4CFC29") {
        echo"\033[32mSHA1 hash 'test' -> *94BDCEBE19083CE2A1F959FD02F964C7AF4CFC29 success\n";
    } else {
        echo"\033[31mSHA1 Encode 'test' -> *94BDCEBE19083CE2A1F959FD02F964C7AF4CFC29 Failed\n";
    }

    //test MD5 hash
    if ($hashUtils->hashMD5("test") == "098f6bcd4621d373cade4e832627b4f6") {
        echo"\033[32mMD5 hash 'test' -> 098f6bcd4621d373cade4e832627b4f6 success\n";
    } else {
        echo"\033[31mMD5 Encode 'test' -> 098f6bcd4621d373cade4e832627b4f6 Failed\n";
    }

    //test SHA256 hash
    if ($hashUtils->genSHA256("test") == "9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08") {
        echo"\033[32mSHA256 hash 'test' -> 9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08 success\n";
    } else {
        echo"\033[31mSHA256 Encode 'test' -> 9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08 Failed\n";
    }

    //Print spacer
    echo"\033[33m================================================================================\n";
?>