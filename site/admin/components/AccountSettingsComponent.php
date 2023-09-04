<div class="adminPanel">
<?php // account settings component in admin site

    // check if user submit password change
    if (isset($_POST["submitPasswordChange"])) {
        
        // init values from form and escaped
        $password = $escapeUtils->specialCharshStrip($_POST["password"]);
        $rePassword = $escapeUtils->specialCharshStrip($_POST["repassword"]);

        // check if values not empty
        if (!empty($password) or !empty($rePassword)) {

            // check if password matches
            if ($password == $rePassword) {

                // check if password have 6 characters
                if (strlen($password) >= 5) {
        
                    // get username from session
                    $username = $userManager->getCurrentUsername();
                    
                    // update password
                    $userManager->updatePassword($username, $password);

                    // show updates msg
                    $alertManager->flashSuccess("Your password is updated");
                } else {
                    $alertManager->flashError("Minimal password lenght is 6 characters");
                }
            } else {
                $alertManager->flashError("Password is not matched");
            }
        } else {
            $alertManager->flashError("Minimal password lenght is 6 characters");
        }
    }

    // check if image file uploaded
    if(isset($_POST["submitUploadImage"])) {

        // check if file not empty
        if (empty($_FILES['fileToUpload']["tmp_name"])) {        
            $alertManager->flashError("Image file is empty");
        } else {

            // get file from form
            $imageFile = file_get_contents($_FILES['fileToUpload']['tmp_name']);

            // get base64 code form image
            $base64Final = base64_encode($imageFile);
            
            // get username
            $username = $userManager->getCurrentUsername();

            // update user avatar
            $userManager->updateProfileImage($base64Final, $username);

            // flash msg
            $alertManager->flashSuccess("Image updated");
        }
    }

    // include pic changer form
    include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/forms/ProfilePicChnageForm.php');

    // include password change form
    include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/forms/PasswordChangeForm.php');
?>
</div>
