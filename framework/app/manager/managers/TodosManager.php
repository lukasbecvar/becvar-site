<?php // todo list manager (admin todos)

    namespace becwork\managers;

    class TodosManager {

        // new todo to mysql
        public function addTodo($text): void {

            global $mysql, $userManager;

            // insert to mysql
            $mysql->insertQuery("INSERT INTO `todos`(`text`, `status`) VALUES ( '$text', 'open')");    
            
            // log action to mysql database 
            $mysql->logToMysql("todo-manager", "user ".$userManager->getCurrentUsername()." added new todo $text");

            // refrsh window aftre add todo
            print '<script type="text/javascript">window.location.replace("?admin=todos");</script>';
        }
    
        // close todo
        public function closeTodo($id): void {
    
            global $mysql, $userManager;
            
            // log action to mysql database 
            $mysql->logToMysql("todo-manager", "user ".$userManager->getCurrentUsername()." closed todo $id");
    
            // update todos for close 
            $mysql->insertQuery("UPDATE todos SET status='closed' WHERE id='$id'");
        }
        
        // check if todos empty
        public function isEmpty(): bool {

            global $mysql;

			// default state output
			$state = false;

            // select todos count form database
            $todos = $mysql->fetch("SELECT id FROM todos WHERE status='open'");

            // count todos
            $todos_count = count($todos);

            // check if logs is empty
            if ($todos_count == 0) {
                $state = true;
            }

            return $state;
        }
    }
?>