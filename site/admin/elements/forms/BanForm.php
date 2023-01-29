<?php 

    // check if auto close seted
    if (isset($_GET["close"])) {
        $formAction = '?admin=visitors&action=ban&id='.$siteController->getQueryString("id").'&limit=500&startby=0&close=yes';
    } else {
        $formAction = '?admin=visitors&action=ban&id='.$siteController->getQueryString("id").'&limit=500&startby=0';
    }

    // check if get not empty
    if ($siteController->getQueryString("id") != null) {
        echo '
            <form class="newTodoForm" action="'.$formAction.'" method="post">
                <textarea class="todoArea" maxlength="120" name="banReason" class="feedback-input" placeholder="Reason"></textarea>
                <input class="inputButton todoButton" type="submit" name="submitBan" value="Submit">
            </form>
        ';
    } else {
        echo"<h2 class=pageTitle>Error by id not identified</h2>";
    }
?>