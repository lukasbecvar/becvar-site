<?php //Todo manager admin component

    //Check if user submit new todo
    if (isset($_POST["submitNewTodo"])) {

        //Get text from for and escape
        $todoText = $mysqlUtils->escapeString($_POST["todoText"], true, true);

        //Check if text is empty
        if (!empty($todoText)) {

            //Check if todo test have < 121 characters
            if (strlen($todoText) < 121) {
                $todosController->addTodo($todoText);

                //Instant refrash after add new todo
                $urlUtils->jsRedirect("?admin=todos");

            } else {
                
                //Flash custom error msg for todo manager
                echo '
                    <center><div class="alert alert-danger todoErrorAlert alert-dismissible fade show" role="alert">
                        Maximum todo characters is 120
                        <a href="?admin=todos" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></a>
                    </div></center>
                ';

                //Underline on mobile devices
                if($mobileDetector->isMobile()) {
                    echo '<br><br>';
                }
            }            
        }
    }
	
    //Check if user typed id
    if (isset($_GET["delete"])) {

        //Get id form url and escape
        $id = $mysqlUtils->escapeString($_GET["delete"], true, true);

        //Close todo
        $todosController->closeTodo($id);

        //Redirect to todos page
        $urlUtils->jsRedirect("?admin=todos"); 
    }
?>

<div class="adminPanel">
    <?php
        //Include new todo from
        include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/forms/NewTodoForm.php');
    ?>
    <div class="table-responsive">
        <?php //Print todos to site

            //Check if todos is empty
            if ($todosController->isEmpty()) {
                echo"<h2 class=pageTitle>Todolist is empty</h2>";
            } else {

                //Print table struct
                echo '<table class="todoTable table table-dark"><thead><tr><th scope="col">#</th><th scope="col">Todo</th><th scope="col">X</th><th scope="col">Edit</th></tr></thead><tbody>';

                //Get todos from database
                $finalTodos = mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT * from todos WHERE status = 'open'"); 

                //Print todos to table
                while ($row = mysqli_fetch_assoc($finalTodos)) { 
                    echo "<tr class='lineItem'><th scope='row'>".$row["id"]."<td>".$row["text"]."<td><a class='deleteLinkTodos' href='?admin=todos&delete=".$row["id"]."'>X</a></td><td><a class='text-warning deleteLinkTodos' href='?admin=dbBrowser&editor=todos&id=".$row["id"]."&postby=todomanager' target='_blank'>Edit</a></td></td></th></tr>";
                }

                //End of table struct
                echo '</tbody></table>';
            }        
        ?>
    </div>
</div>
