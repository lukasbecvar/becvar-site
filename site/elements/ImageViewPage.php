<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="/assets/css/imageViewer.css" rel="stylesheet">
        <title>Image: <?php echo $imgSpec; ?></title>
    </head>
    <body>
        <center>
            <div>
                <img src="data:image/png;base64,<?php echo $image["image"]; ?>"/> 
            </div>
            <?php 
                // log image view to database
                $mysqlUtils->logToMysql("Image-load", "visitor loaded image: $imgSpec");
            ?>
        <center>
    </body>
</html>