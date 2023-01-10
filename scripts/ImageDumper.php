#!/usr/bin/php 
<?php // image uploader database dumper -> GalleryDump.tar.gz

    // include config
    require_once("config.php");

    // init PageConfig
	$configOBJ = new becwork\config\PageConfig();

    // clear console
    echo chr(27).chr(91).'H'.chr(27).chr(91).'J';

    // random number generator
    function genNumbrGenerator($lenght) {
        $permitted_chars = "0123456789";
        $generated = substr(str_shuffle($permitted_chars), 0, $lenght);
        return $generated;
    }

    // get mysql credentials
    $mysqlIP = $configOBJ->config["ip"];
    $mysqlUser = $configOBJ->config["username"];
    $mysqlPassword = $configOBJ->config["password"];

    // check if mysql data nor empty
    if (!empty($mysqlIP) && !empty($mysqlUser) && !empty($mysqlPassword)) {
        
        // get images from mysql
        $images = mysqli_query(mysqli_connect(trim($mysqlIP), trim($mysqlUser), trim($mysqlPassword), $configOBJ->config["basedb"]), "SELECT * FROM image_uploader");

        // save separate files to dump
        while ($row = mysqli_fetch_assoc($images)) {
        
            // value builder
            $name = $row["id"];
            $base64 = base64_decode($row["image"]);
            $galleryName = "image_uploader";
        
            // check if dump dir exist & create
            if (!file_exists('dump/')) {
                mkdir('dump/', 0777, true);
            }

            // check if dump/gallery dir exist & create
            if (!file_exists('dump/'.$galleryName."/")) {
                mkdir('dump/'.$galleryName."/", 0777, true);
            }
            
            // check if dump/gallery/file.image dir exist & generate name
            if (file_exists("dump/".$galleryName."/".$name.".jpg")) {
                $name = $name.genNumbrGenerator(10);
            }

            // print response
            echo "\033[32m".$name." : saved to dump/".$galleryName."/".$name.".jpg\033[0m\n";
            
            // create image file
            file_put_contents("dump/".$galleryName."/".$name.".jpg", $base64);
        } 

        // build gallery dump archive
        $phar = new PharData('GalleryDump.tar.gz');
        $phar->buildFromDirectory('dump/');

        // remove old directory
        system("rm -rf ".escapeshellarg("dump/"));

    } else {
        echo "\033[31mError MysqlIP or user or password is empty\033[0m\n";
    }
?>