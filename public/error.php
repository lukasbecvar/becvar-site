<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/assets/css/error-page.css" rel="stylesheet">
	<link rel="icon" href="/assets/img/favicon.png" type="image/x-icon"/>
</head>
<body> 
<?php // error site handlerer 
    if (isset($_GET["code"])) {

        // get error code form url
        $code = htmlspecialchars($_GET["code"], ENT_QUOTES);

        // set error page by code
        if ($code == 400) {
            include_once("../site/errors/400.php");

        } else if ($code == 401) {
            include_once("../site/errors/401.php");

        } else if ($code == 403) {
            include_once("../site/errors/403.php");

        } else if ($code == 404) {
            include_once("../site/errors/404.php");

        } else if ($code == 429) {
            include_once("../site/errors/429.php");

        } else if ($code == 500) {
            include_once("../site/errors/500.php");

        } else if ($code == "banned") {
            include_once("../site/errors/BannedPage.php");

        } else if ($code == "maintenance") {
            include_once("../site/errors/Maintenance.php");

        } else if ($code == "unknown") {
            include_once("../site/errors/UnknownError.php");

        // not found code
        } else {
            include_once("../site/errors/UnknownError.php");
        }

    // code is empty 
    } else {
        include_once("../site/errors/404.php");
    }
?>
<!-- init fluid animation -->
<canvas></canvas>
<script src="/assets/js/fluid-animation.js"></script>
</body>
</html>