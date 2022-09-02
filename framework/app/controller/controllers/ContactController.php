<?php /* Contact controller (read, sent, delelete msg) */

	class ContactController {

		//Send message to database
		public function sendMessage($name, $email, $message, $status) {

			//Init mysql utils
			global $mysqlUtils;

			//Escape values
			$name = $mysqlUtils->escapeString(trim($name), true, true);
			$email = $mysqlUtils->escapeString(trim($email), true, true);
			$message = $mysqlUtils->escapeString(trim($message), true, true);
			$time = date('d.m.Y H:i:s');
			$status = $mysqlUtils->escapeString(trim($status), true, true);

			//Insert values
			$queryInsert = $mysqlUtils->insertQuery("INSERT INTO `messages`(`name`, `email`, `message`, `time`, `status`) VALUES ('$name', '$email', '$message', '$time', '$status')");

			//Return output
			if ($queryInsert) {
				return false;
			} else {

				//Log to mysql
				$mysqlUtils->logToMysql("Sended message", "by sender $name");
				
				return true;
			}
		}

		//Print all messages
		public function printMSGS() {

			//Init mysql utils 
			global $mysqlUtils;
			global $pageConfig;

            /*Save all messages if status open to this array*/
            $messages = mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT * from messages WHERE status = 'open' ORDER BY id DESC"); 

            //Show all messages from array messages in page
            while ($row = mysqli_fetch_assoc($messages)) {
                echo"<div class='card text-white bg-dark mb-3' style='max-width: 95%;'><div class=card-body><h5 class=leftCenter class=card-title>".$row["name"]." (".$row["email"].") [".$row["time"]."]<a class='deleteLink' href='?admin=inbox&delete=".$row["id"]."'>X</a></h5><p class=leftCenter class=card-text>".$row["message"]."</p></div></div>";
            }
		}
		
		//Delete message by id
		public function deleteMsgByID($id) {

			global $mysqlUtils;
			global $adminController;

			//Log process to mysql database 
			$mysqlUtils->logToMysql("Messages", "User ".$adminController->getCurrentUsername()." closed message $id");

			//Update message for close 
			$mysqlUtils->insertQuery("UPDATE messages SET status='closed' WHERE id='$id'");
		}
		
        //Check if msgs empty
        public function isEmpty() {

            global $mysqlUtils;
			global $pageConfig;

            //Select msgs count form database
            $msgsCount = mysqli_fetch_assoc(mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT COUNT(*) AS count FROM messages WHERE status='open'"));

            if ($msgsCount["count"] == 0) {
                return true;
            } else {
                return false;
            }
        }
	}
?>