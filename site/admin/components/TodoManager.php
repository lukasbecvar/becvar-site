<?php // todo manager admin component

    // check if user submit new todo
    if (isset($_POST["submitNewTodo"])) {

        // get text from for and escape
        $todoText = $escapeUtils->specialCharshStrip($_POST["todoText"]);

        // check if text is empty
        if (!empty($todoText)) {

            // check if todo test have < 121 characters
            if (strlen($todoText) < 121) {
                $todosManager->addTodo($todoText);

                // instant refrash after add new todo
                $urlUtils->jsRedirect("?admin=todos");

            } else {
                
                // flash custom error msg for todo manager
                echo '
                    <center><div class="alert alert-danger todoErrorAlert alert-dismissible fade show" role="alert">
                        Maximum todo characters is 120
                        <a href="?admin=todos" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></a>
                    </div></center>
                ';

                // underline on mobile devices
                if($mobileDetector->isMobile()) {
                    echo '<br><br>';
                }
            }            
        }
    }
	
    // check if user typed id
    if ($siteManager->getQueryString("delete") != null) {

        // get id form url and escape
        $id = $siteManager->getQueryString("delete");

        // close todo
        $todosManager->closeTodo($id);

        // redirect to todos page
        $urlUtils->jsRedirect("?admin=todos"); 
    }
?>

<div class="adminPanel">
    <?php
        // new todo from
        include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/forms/NewTodoForm.php');
    ?>
    <div class="table-responsive">
        <?php // print todos to site

            // check if todos is empty
            if ($todosManager->isEmpty()) {
                echo"<h2 class=pageTitle>Todolist is empty</h2>";
            } else {

                // print table struct
                echo '<table class="todoTable table table-dark"><thead><tr class="lineItem">
                    <th scope="col">#</th>
                    <th scope="col">Todo</th>
                    <th scope="col">X</th>
                    <th scope="col">Edit</th>
                </tr></thead><tbody>';

                // get todos from database
                $finalTodos = $mysql->fetch("SELECT * from todos WHERE status = 'open'");

                // print todos to table
                foreach ($finalTodos as $row) { 
                    echo "<tr class='lineItem'><th scope='row'>".$row["id"]."<td>".$row["text"]."<td><a class='deleteLinkTodos' href='?admin=todos&delete=".$row["id"]."'>X</a></td><td><a class='text-warning deleteLinkTodos' href='?admin=dbBrowser&editor=todos&id=".$row["id"]."&postby=todomanager' target='_blank'>Edit</a></td></td></th></tr>";
                }

                // end of table struct
                echo '</tbody></table>';
            }        
        ?>
    </div>
</div>
