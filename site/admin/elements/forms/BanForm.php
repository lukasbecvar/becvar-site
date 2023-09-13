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
            <form class="new-todo-form" action="'.$form_action.'" method="post">
                <textarea class="todo-area" maxlength="120" name="banReason" class="feedback-input" placeholder="Reason"></textarea>
                <input class="input-button todo-button" type="submit" name="submitBan" value="Submit">
            </form>
        ';
    } else {
        echo"<h2 class=page-title>Error by id not identified</h2>";
    }
?>