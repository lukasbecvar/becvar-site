<?php // todo list manager (admin todos)

    namespace becwork\managers;

    class TodosManager {

        // new todo to mysql
        public function insert_todo($text): void {

            global $mysql, $user_manager;

            // insert to mysql
            $mysql->insert("INSERT INTO `todos`(`text`, `status`) VALUES ( '$text', 'open')");    
            
            // log action to mysql database 
            $mysql->log("todo-manager", "user ".$user_manager->get_username()." added new todo $text");

            // refrsh window aftre add todo
            print '<script type="text/javascript">window.location.replace("?admin=todos");</script>';
        }
    
        // close todo
        public function close_todo($id): void {
    
            global $mysql, $user_manager;
            
            // log action to mysql database 
            $mysql->log("todo-manager", "user ".$user_manager->get_username()." closed todo $id");
    
            // update todos for close 
            $mysql->insert("UPDATE todos SET status='closed' WHERE id='$id'");
        }
        
        // check if todos empty
        public function is_empty(): bool {

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