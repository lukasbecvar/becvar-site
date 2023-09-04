<?php // todo list manager

    namespace becwork\managers;

    class TodosManager {

        // new todo to mysql
        public function addTodo($text) {

            global $mysql;
            global $userManager;

            // insert to mysql
            $mysql->insertQuery("INSERT INTO `todos`(`text`, `status`) VALUES ( '$text', 'open')");    
            
            // log action to mysql database 
            $mysql->logToMysql("Todos", "User ".$userManager->getCurrentUsername()." added new todo $text");

            // refrsh window aftre add todo
            print '<script type="text/javascript">window.location.replace("?admin=todos");</script>';
        }
    
        // close todo
        public function closeTodo($id) {
    
            global $mysql;
            global $userManager;
            
            // log action to mysql database 
            $mysql->logToMysql("Todos", "User ".$userManager->getCurrentUsername()." closed todo $id");
    
            // update todos for close 
            $mysql->insertQuery("UPDATE todos SET status='closed' WHERE id='$id'");
        }
        
        // check if todos empty
        public function isEmpty() {

            global $mysql;

            // select todos count form database
            $todos = $mysql->fetch("SELECT id FROM todos WHERE status='open'");

            // check if logs is empy
            if (count($todos) == 0) {
                return true;
            } else {
                return false;
            }
        }
    }
?>