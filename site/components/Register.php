<?php //Add nav menu to site
	include_once("elements/navigation/HeaderElement.php");
?>
<main class="homePage">
    <?php 
 
        //Check if register submited
        if (isset($_POST["submitRegister"])) {
            
            //Check if user table is empty
            if (!$adminController->isUserEmpty()) {
                echo "<br><h2 class=pageTitle>This feature can be used only to create an admin account</h2>";
            } else {
               
                //Get data from post and escapeit
                $username = $mysqlUtils->escapeString($_POST["username"], true, true);
                $password = $mysqlUtils->escapeString($_POST["password"], true, true);
                $repassword = $mysqlUtils->escapeString($_POST["repassword"], true, true);
                
                //Check if values not empty
                if (empty($username) or empty($password) or empty($repassword)) {
                    $alertController->flashError("You must add all values in form!");
                } else {
                    
                    //Check password minimal length
                    if (strlen($password) < 5) {
                        $alertController->flashError("Password must have more than 5 characters");
                    //Check password match
                    } elseif ($password != $repassword) {
                        $alertController->flashError("Passwords do not match");
                    } else {
                        if ($adminController->isUserEmpty()) {

                            //Init basic values
                            $role = "Owner";
                            $image_base64 = "image_code";
                          
                            //Hash password to save in database
                            $password = $hashUtils->genBlowFish($password);
                          
                            //insert user account to database
                            $mysqlUtils->insertQuery("INSERT INTO `users`(`username`, `password`, `role`, `image_base64`) VALUES ( '$username', '$password', '$role', '$image_base64')");   

                            //Redirect to login page
                            $urlUtils->redirect("index.php?page=admin");
                       
                        } else {
                            echo "<br><h2 class=pageTitle>This feature can be used only to create an admin account</h2>";
                        }
                    }
                }
            }
        }


        //Check if users table is realy empty
        if (!$adminController->isUserEmpty()) {
            echo "<br><h2 class=pageTitle>This feature can be used only to create an admin account</h2>";
        } else {
            include_once("elements/forms/RegisterForm.php");
        }
    ?>
</main>
<?php //Add footer to site
	include_once("elements/navigation/FooterElement.php");
?>
