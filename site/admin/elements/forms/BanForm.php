<?php // ban user forms

    // check if auto close seted
    if (isset($_GET["close"])) {
        $form_action = '?admin=visitors&action=ban&id='.$site_manager->get_query_string("id").'&limit=500&startby=0&close=yes';
    } else {
        $form_action = '?admin=visitors&action=ban&id='.$site_manager->get_query_string("id").'&limit=500&startby=0';
    }

    // check if get not empty
    if ($site_manager->get_query_string("id") != null) {
        echo '
            <form class="newTodoForm" action="'.$form_action.'" method="post">
                <textarea class="todoArea" maxlength="120" name="banReason" class="feedback-input" placeholder="Reason"></textarea>
                <input class="inputButton todoButton" type="submit" name="submitBan" value="Submit">
            </form>
        ';
    } else {
        echo"<h2 class=pageTitle>Error by id not identified</h2>";
    }
?>