#!/usr/bin/php 
<?php //Image uploader database dumper -> GalleryDump.tar.gz

    //Include config
    require_once("config.php");

    //Init PageConfig
	$configOBJ = new PageConfig();

    //Clear console
    echo chr(27).chr(91).'H'.chr(27).chr(91).'J';

    function genNumbrGenerator($lenght) {
        $permitted_chars = "0123456789";
        $generated = substr(str_shuffle($permitted_chars), 0, $lenght);
        return $generated;
    }

    $mysqlIP = $configOBJ->config["ip"];
    $mysqlUser = $configOBJ->config["username"];
    $mysqlPassword = $configOBJ->config["password"];



    if (!empty($mysqlIP) && !empty($mysqlUser) && !empty($mysqlPassword)) {
        
        //Get images from mysql
        $images = mysqli_query(mysqli_connect(trim($mysqlIP), trim($mysqlUser), trim($mysqlPassword), $configOBJ->config["basedb"]), "SELECT * FROM image_uploader");

        //Save separate files to dump
        while ($row = mysqli_fetch_assoc($images)) {
        
            $name = $row["id"];
            $base64 = base64_decode($row["image"]);
            $galleryName = "image_uploader";
        
            if (!file_exists('dump/')) {
                mkdir('dump/', 0777, true);
            }

            if (!file_exists('dump/'.$galleryName."/")) {
                mkdir('dump/'.$galleryName."/", 0777, true);
            }
            
        
            if (file_exists("dump/".$galleryName."/".$name.".jpg")) {
                $name = $name.genNumbrGenerator(10);
            }

            echo "\033[32m".$name." : saved to dump/".$galleryName."/".$name.".jpg\033[0m\n";
            file_put_contents("dump/".$galleryName."/".$name.".jpg", $base64);
        } 

        $phar = new PharData('GalleryDump.tar.gz');
        $phar->buildFromDirectory('dump/');

        system("rm -rf ".escapeshellarg("dump/"));

    } else {
        echo "\033[31mError MysqlIP or user or password is empty\033[0m\n";
    }
?>