<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="/assets/css/image-viewer.css" rel="stylesheet">
        <title>Image: <?= $img_spec ?></title>
    </head>
    <body>
        <center>
            <div>
                <img src="data:image/png;base64,<?= $image ?>"/> 
            </div>
            <?php 
                // log image view to database
                $mysql->log("image-load", "visitor loaded image: $img_spec");
            ?>
        <center>
    </body>
</html>