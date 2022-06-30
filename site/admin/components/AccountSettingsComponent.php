<div class="dashboardBox">
<?php //The account settings component in admin site

    //Check if user submit password change
    if (isset($_POST["submitPasswordChange"])) {
        
        //init values from form and escaped
        $password = $mysqlUtils->escapeString($_POST["password"], true, true);
        $rePassword = $mysqlUtils->escapeString($_POST["repassword"], true, true);

        //Check if values not empty
        if (!empty($password) or !empty($rePassword)) {

            //Check if password matches
            if ($password == $rePassword) {

                //Check if password have 6 characters
                if (strlen($password) >= 5) {
        
                    //Get username from session
                    $username = $adminController->getCurrentUsername();
                    
                    //Update password
                    $adminController->updatePassword($username, $password);

                    //Show updates msg
                    $alertController->flashSuccess("Your password is updated");
                } else {
                    $alertController->flashError("Minimal password lenght is 6 characters");
                }
            } else {
                $alertController->flashError("Password is not matched");
            }
        } else {
            $alertController->flashError("Minimal password lenght is 6 characters");
        }
    }

    // Check if image file uploaded
    if(isset($_POST["submitUploadImage"])) {

        //Check if file not empty
        if (empty($_FILES['fileToUpload']["tmp_name"])) {        
            $alertController->flashError("Image file is empty");
        } else {

            //Get file from form
            $imageFile = file_get_contents($_FILES['fileToUpload']['tmp_name']);

            //Get base64 code form image
            $base64Final = base64_encode($imageFile);
            
            //Get username
            $username = $adminController->getCurrentUsername();

            //Update user avatar
            $adminController->updateProfileImage($base64Final, $username);

            //Flash msg
            $alertController->flashSuccess("Image updated");
        }
    }

    //Include pic changer form
    include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/forms/ProfilePicChnageForm.php');

    //Include password change form
    include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/forms/PasswordChangeForm.php');
?>
</div>
