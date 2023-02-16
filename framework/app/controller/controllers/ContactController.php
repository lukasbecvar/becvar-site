<?php // contact controller (read, sent, delelete msg)

	namespace becwork\controllers;

	class ContactController {

		// get messages from database
		public function getMessages() {

			global $mysqlUtils;

			return $mysqlUtils->fetch("SELECT * from messages WHERE status = 'open' ORDER BY id DESC");
		}

		// send message to database
		public function sendMessage($name, $email, $message, $status) {

			global $mysqlUtils;
			global $mainUtils;
			global $escapeUtils;

			// get & escape values
			$name = $escapeUtils->specialCharshStrip(trim($name));
			$email = $escapeUtils->specialCharshStrip(trim($email));
			$message = $escapeUtils->specialCharshStrip(trim($message));
			$time = date('d.m.Y H:i:s');
			$remote_addr = $mainUtils->getRemoteAdress();
			$status = $escapeUtils->specialCharshStrip(trim($status));

			// insert values
			$queryInsert = $mysqlUtils->insertQuery("INSERT INTO `messages`(`name`, `email`, `message`, `time`, `remote_addr`, `status`) VALUES ('$name', '$email', '$message', '$time', '$remote_addr', '$status')");

			// return output
			if ($queryInsert) {
				return false;
			} else {

				// log to mysql
				$mysqlUtils->logToMysql("Sended message", "by sender $name");
				
				return true;
			}
		}

		// print all messages
		public function printMSGS() {

			global $visitorController;

            // get messages array
            $messages = $this->getMessages();

			// default ID
			$userID = NULL;

            // show all messages from array messages in page
            foreach ($messages as $row) {

				// get user ID
				$userID = $visitorController->getVisitorIDByIP($row["remote_addr"]);

				// ban link builder
				if ($visitorController->isVisitorBanned($row["remote_addr"])) {
					$banLink = "<a class='deleteLink text-warning' href='?admin=visitors&action=ban&id=".$userID."&limit=500&startby=0&close=y' target='blank_'>UNBAN</a>";
				} else {
					$banLink = "<a class='deleteLink text-warning' href='?admin=visitors&action=ban&id=".$userID."&limit=500&startby=0&close=y&reason=spam' target='blank_'>BAN</a>";
				}

				// check if ip found
				if ($userID == NULL) {
					echo"<div class='card text-white mb-3' style='max-width: 95%;'><div class=card-body><h5 class=leftCenter class=card-title>".$row["name"]." (".$row["email"].") <span class='text-success phoneNone'>[".$row["time"]."]</span>, <span class='text-warning phoneNone'>[".$row["remote_addr"]."]</span><a class='deleteLink' href='?admin=inbox&delete=".$row["id"]."'>X</a></h5><p class=leftCenter class=card-text>".$row["message"]."</p></div></div>";
				} else {

					// check if sender banned
					if ($visitorController->isVisitorBanned($row["remote_addr"])) {
						echo"<div class='card text-white mb-3' style='max-width: 95%;'><div class=card-body><h5 class=leftCenter class=card-title>".$row["name"]." (".$row["email"].") <span class='text-success phoneNone'>[".$row["time"]."]</span>, <span class='text-red phoneNone'>[".$row["remote_addr"]."]</span><a class='deleteLink' href='?admin=inbox&delete=".$row["id"]."'>X</a>".$banLink."</h5><p class=leftCenter class=card-text>".$row["message"]."</p></div></div>";
					} else {
						echo"<div class='card text-white mb-3' style='max-width: 95%;'><div class=card-body><h5 class=leftCenter class=card-title>".$row["name"]." (".$row["email"].") <span class='text-success phoneNone'>[".$row["time"]."]</span>, <span class='text-warning phoneNone'>[".$row["remote_addr"]."]</span><a class='deleteLink' href='?admin=inbox&delete=".$row["id"]."'>X</a>".$banLink."</h5><p class=leftCenter class=card-text>".$row["message"]."</p></div></div>";
					}
				}
            }
		}
		
		// felete message by id
		public function deleteMsgByID($id) {

			global $mysqlUtils;
			global $adminController;

			// log process to mysql database 
			$mysqlUtils->logToMysql("Messages", "User ".$adminController->getCurrentUsername()." closed message $id");

			// update message for close 
			$mysqlUtils->insertQuery("UPDATE messages SET status='closed' WHERE id='$id'");
		}
		
        // check if msgs empty
        public function isEmpty() {

            // get messages from database
            $msgs = $this->getMessages();

			// check if msgs list empty
            if (count($msgs) < 1) {
                return true;
            } else {
                return false;
            }
        }
	}
?>