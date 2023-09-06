<div class="adminPanel">
<?php // confirmation to server shutdown

    // generate confirmation code
    $confirm_code = $string_utils->gen_random_sring(15);

    // check if form submited
    if (isset($_POST["submitShutdown"])) {

        // init values
        $code = $escape_utils->special_chars_strip($_POST["confirmCode"]);
        $shutdown_code = $escape_utils->special_chars_strip($_POST["shutdownCode"]);

        // check if code is valid
        if ($shutdown_code == $code) {

            // redirect to cmd executor
            $url_utils->js_redirect("?admin=executeTask&command=shutdown");

        } else {
            $alert_manager->flash_error("Incorrect confirmation code.");
        }
    } else {
        // flash warning msg
        $alert_manager->flash_warning("Warning this action will completely <strong>shutdown</strong> your server, it can only be started physically or from the administration");
    }
?>

<form class="loginForm dark-table" style="top: 28%;" action="?admin=form&form=shutdown" method="post">
    <p class="loginFormTitle">Confirmation</p>
    <p class="loginFormTitle loginFormSubTitle">please repeat the verification code</p>
    <input class="textInput" type="text" name="confirmCode" value=<?php echo $confirm_code;?> readonly><br>
    <input class="textInput" type="text" name="shutdownCode" placeholder="Confirmation code"><br>
    <div class="rightPosition"><input class="inputButton" type="submit" name="submitShutdown" value="Shutdown"></div>
</form>
<style>
@media screen and (max-width: 800px) { 
	.alert {
		top: 14% !important;
	}
    .loginForm {
        top: 42% !important;
    }
}
</style>
</div>
