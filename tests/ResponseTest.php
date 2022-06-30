#!/usr/bin/php
<?php //The basic tesponse codes test

    //Add config file
    require_once("config.php");

    //Add response test class
    require_once("framework/utils/ResponseUtils.php");

    //Init ConfigManager array
    $pageConfig = new PageConfig();

    //Init response utils class
    $responseUtils = new ResponseUtils();


    //Register all testing urls
    $register = [
        $pageConfig->config["url"]
    ];

    
    //Test all pages in array
    foreach ($register as $value) {

        //Check if site running
        if ($responseUtils->checkOnline($value) == "Online") {
            echo "\033[32mPage: ".$value." working!\033[0m\n";
        } else {
            echo "\033[31mPage: ".$value." error: page not running!\033[0m\n";
        }
    }

    echo"\033[33m================================================================================\n";
?>