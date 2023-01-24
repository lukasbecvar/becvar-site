<?php 

    // check if get not empty
    if (!empty($_GET["id"])) {
        echo '
            <form class="newTodoForm bg-dark" action="?admin=visitors&action=ban&id='.$_GET["id"].'&limit=500&startby=0" method="post">
                <textarea class="todoArea bg-dark" maxlength="120" name="banReason" class="feedback-input" placeholder="Reason"></textarea>
                <input class="inputButton bg-dark todoButton" type="submit" name="submitBan" value="Submit">
            </form>
        ';
    } else {
        echo"<h2 class=pageTitle>Error by id not identified</h2>";
    }
?>