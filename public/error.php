<?php // error site handlerer
    if (isset($_GET["code"])) {

        // get error code form url
        $code = htmlspecialchars($_GET["code"], ENT_QUOTES);

        // set error page by code
        if ($code == 404) {
            include_once("../site/errors/404.php");

        } else if ($code == 403) {
            include_once("../site/errors/403.php");

        } else if ($code == 400) {
            include_once("../site/errors/400.php");

        } else if ($code == "banned") {
            include_once("../site/errors/BannedPage.php");

        // not found code
        } else {
            include_once("../site/errors/UnknownError.php");
        }

    // code is empty 
    } else {
        include_once("../site/errors/404.php");
    }
?>