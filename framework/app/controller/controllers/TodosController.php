<?php // todo list manager

    namespace becwork\controllers;

    class TodosController {

        // new todo to mysql
        public function addTodo($text) {

            global $mysqlUtils;
            global $adminController;

            // insert to mysql
            $mysqlUtils->insertQuery("INSERT INTO `todos`(`text`, `status`) VALUES ( '$text', 'open')");    
            
            // log action to mysql database 
            $mysqlUtils->logToMysql("Todos", "User ".$adminController->getCurrentUsername()." added new todo $text");

            // refrsh window aftre add todo
            print '<script type="text/javascript">window.location.replace("?admin=todos");</script>';
        }
    
        // close todo
        public function closeTodo($id) {
    
            global $mysqlUtils;
            global $adminController;
            
            // log action to mysql database 
            $mysqlUtils->logToMysql("Todos", "User ".$adminController->getCurrentUsername()." closed todo $id");
    
            // update todos for close 
            $mysqlUtils->insertQuery("UPDATE todos SET status='closed' WHERE id='$id'");
        }
        
        // check if todos empty
        public function isEmpty() {

            global $mysqlUtils;
            global $pageConfig;

            // select todos count form database
            $todosCount = mysqli_fetch_assoc(mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT COUNT(*) AS count FROM todos WHERE status='open'"));

            // check if logs is empy
            if ($todosCount["count"] == 0) {
                return true;
            } else {
                return false;
            }
        }
    }
?>