#!/usr/bin/php
<?php // basic tesponse codes test

    // config file
    require_once("config.php");

    // response test class
    require_once("framework/utils/ResponseUtils.php");

    // init ConfigManager array
    $config = new becwork\config\PageConfig();

    // init response utils class
    $responseUtils = new becwork\utils\ResponseUtils();

    // register all testing urls
    $register = [
        $config->config["url"]
    ];
    
    // test all pages in array
    foreach ($register as $value) {

        // check if site running
        if ($responseUtils->checkOnline($value) == "Online") {
            echo "\033[32mPage: ".$value." working!\033[0m\n";
        } else {
            echo "\033[31mPage: ".$value." error: page not running!\033[0m\n";
        }
    }

    // print spacer
    echo"\033[33m================================================================================\n";
?>