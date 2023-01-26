<?php // REST API function

    // check if API enabled
    if ($apiController->isApiEnabled()) {

        // init token for controll user add token
        $accesToken = $pageConfig->getValueByName('apiToken');

        // init token from get parameter and escaped
        $token = $mysqlUtils->escapeString($siteController->getQueryString("token"), true, true);
        
        // check if token is valid
        if ($apiController->isTokenValid($token, $accesToken) == null) {
            die("Error: api token is empty");
       
        } elseif ($apiController->isTokenValid($token, $accesToken) == "invalid") {
            die("Error: api token is invalid, please check token format or validate with admin");

        } elseif ($apiController->isTokenValid($token, $accesToken) == "valid") {
            
            // get value from url get
            $value = $mysqlUtils->escapeString($siteController->getQueryString("value"), true, true);
            
            // check if value is null
            if ($value == null) {
                $apiController->printValueNull();
            
            } else {

                /* ////////////////////////////// Main value list ////////////////////////////// */

                // print api data to json by value name
                if ($value == "list") {
                    $apiController->prntValueList();

                // get API status
                } elseif ($value == "status") {
                    $apiController->printApiStatus();
                
                // log to mysql
                } elseif ($value == "log") {
                    $apiController->saveLog();

                /* ///////////////////////////// End of value list ///////////////////////////// */

                // print error if value not found
                } else {
                    $apiController->printUnknowValue();
                }
            }
        }
    } else {
        
        // print valid error format 
        if ($siteController->isSiteDevMode()) {
            die("[DEV-MODE]:Error: api is disabled in comfig file");

        } else {
            $urlUtils->jsRedirect("ErrorHandlerer.php?code=404");
        }
    }
?>