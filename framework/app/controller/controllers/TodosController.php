<?php 

    class TodosController {

        //Add new todo to mysql
        public function addTodo($text) {

            global $mysqlUtils;

            //Insert to mysql
            $mysqlUtils->insertQuery("INSERT INTO `todos`(`text`, `status`) VALUES ( '$text', 'open')");    
            
            //Log action to mysql dsatabase 
            $mysqlUtils->logToMysql("Todos", "User ".$_SESSION["username"]." added new todo $text");

            //Refrsh window aftre add todo
            print '<script type="text/javascript">window.location.replace("index.php?page=admin&process=todos");</script>';
        }
    


        //Close todo
        public function closeTodo($id) {
    
            global $mysqlUtils;
            
            //Log action to mysql dsatabase 
            $mysqlUtils->logToMysql("Todos", "User ".$_SESSION["username"]." closed todo $id");
    
            //Update todos for close 
            $mysqlUtils->insertQuery("UPDATE todos SET status='closed' WHERE id='$id'");
        }


        
        //Check if todos empty
        public function isEmpty() {

            global $mysqlUtils;
            global $pageConfig;

            //Select todos count form database
            $todosCount = mysqli_fetch_assoc(mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT COUNT(*) AS count FROM todos WHERE status='open'"));

            if ($todosCount["count"] == 0) {
                return true;
            } else {
                return false;
            }
        }
    }
?>