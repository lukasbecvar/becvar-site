<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Image: <?php echo $imgSpec; ?></title>
    </head>
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            overflow: hidden;
            background: #292e33;
        }

        body, html {
            height: 100%;
        }
                
        div {
            width:100vw;
            height:100vh;
        }
                
        img {
            max-width:100%;
            height:auto;
            max-height:100%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

    </style>
    <body>
        <center>
            <div>
                <img src="data:image/png;base64,<?php echo $image["image"]; ?>"/> 
            </div>
        <center>
    </body>
</html>