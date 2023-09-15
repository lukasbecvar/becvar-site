<div class="admin-panel">
<?php // account settings component in admin site

    // check if user submit password change
    if (isset($_POST["submitPasswordChange"])) {
        
        // init values from form and escaped
        $password = $escape_utils->special_chars_strip($_POST["password"]);
        $re_rassword = $escape_utils->special_chars_strip($_POST["repassword"]);

        // check if values not empty
        if (!empty($password) or !empty($re_rassword)) {

            // check if password matches
            if ($password == $re_rassword) {

                // check if password have 6 characters
                if (strlen($password) >= 5) {
        
                    // get username from session
                    $username = $user_manager->get_username();
                    
                    // update password
                    $user_manager->update_password($username, $password);

                    // show updates msg
                    $alert_manager->flash_success("Your password is updated");
                } else {
                    $alert_manager->flash_error("Minimal password lenght is 6 characters");
                }
            } else {
                $alert_manager->flash_error("Password is not matched");
            }
        } else {
            $alert_manager->flash_error("Minimal password lenght is 6 characters");
        }
    }

    // check if image file uploaded
    if(isset($_POST["submitUploadImage"])) {

        // check if file not empty
        if (empty($_FILES['fileToUpload']["tmp_name"])) {        
            $alert_manager->flash_error("Image file is empty");
        } else {

            // get file from form
            $imageFile = file_get_contents($_FILES['fileToUpload']['tmp_name']);

            // get base64 code form image
            $base64Final = base64_encode($imageFile);
            
            // get username
            $username = $user_manager->get_username();

            // update user avatar
            $user_manager->update_profile_image($base64Final, $username);

            // flash msg
            $alert_manager->flash_success("Image updated");
        }
    }

    // include pic changer form
    include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/forms/ProfilePicChnageForm.php');

    // include password change form
    include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/forms/PasswordChangeForm.php');
?>
</div>