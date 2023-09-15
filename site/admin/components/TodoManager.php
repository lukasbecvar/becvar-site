<?php // todo manager admin component

    // check if user submit new todo
    if (isset($_POST["submitNewTodo"])) {

        // get text from for and escape
        $todo_text = $escape_utils->special_chars_strip($_POST["todoText"]);

        // check if text is empty
        if (!empty($todo_text)) {

            // check if todo test have < 121 characters
            if (strlen($todo_text) < 121) {
                $todos_manager->insert_todo($todo_text);

                // instant refrash after add new todo
                $url_utils->js_redirect("?admin=todos");

            } else {
                
                // flash custom error msg for todo manager
                echo '
                    <center><div class="alert alert-danger todo-error-alert alert-dismissible fade show" role="alert">
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
    if ($site_manager->get_query_string("delete") != null) {

        // get id form url and escape
        $id = $site_manager->get_query_string("delete");

        // close todo
        $todos_manager->close_todo($id);

        // redirect to todos page
        $url_utils->js_redirect("?admin=todos"); 
    }
?>

<div class="admin-panel">
    <?php
        // new todo from
        include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/forms/NewTodoForm.php');
    ?>
    <div class="table-responsive">
        <?php // print todos to site

            // check if todos is empty
            if ($todos_manager->is_empty()) {
                echo"<h2 class=page-title>Todolist is empty</h2>";
            } else {

                // print table struct
                echo '<table class="todo-table table table-dark"><thead><tr class="line-item">
                    <th scope="col">#</th>
                    <th scope="col">Todo</th>
                    <th scope="col">X</th>
                    <th scope="col">Edit</th>
                </tr></thead><tbody>';

                // get todos from database
                $final_todos = $mysql->fetch("SELECT * from todos WHERE status = 'open'");

                // print todos to table
                foreach ($final_todos as $row) { 
                    echo "<tr class='line-item'><th scope='row'>".$row["id"]."<td>".$row["text"]."<td><a class='delete-link-todos' href='?admin=todos&delete=".$row["id"]."'>X</a></td><td><a class='text-warning delete-link-todos' href='?admin=dbBrowser&editor=todos&id=".$row["id"]."&postby=todomanager' target='_blank'>Edit</a></td></td></th></tr>";
                }

                // end of table struct
                echo '</tbody></table>';
            }        
        ?>
    </div>
</div>