<?php // contact manager (read, send, delelete msg)

	namespace becwork\managers;

	class ContactManager {

		// get messages from database
		public function getMessages() {

			global $mysql;

			$messages = $mysql->fetch("SELECT * from messages WHERE status = 'open' ORDER BY id DESC"); 
			return $messages;
		}

		// get banned email count
		public function getBannedEmailCount($email) {

			global $mysql;

			// get banned emails
			$bannedEmails = $mysql->fetch("SELECT * FROM banned_emails WHERE email = '$email'");
			
			// get emails count
			$bannedEmailCount = count($bannedEmails);

			return $bannedEmailCount;
		}

		// send message to database
		public function sendMessage($name, $email, $message, $status) {

			global $mysql, $mainUtils, $escapeUtils;

			// default state value
			$state = false;

			// check if email banned
			if ($this->getBannedEmailCount($email) > 0) {

				// log to mysql
				$mysql->logToMysql("message-block", "blocked email: $email");

				// return banned
				return "banned";
				
			} else {
				// get & escape values
				$name = $escapeUtils->specialCharshStrip(trim($name));
				$email = $escapeUtils->specialCharshStrip(trim($email));
				$message = $escapeUtils->specialCharshStrip(trim($message));
				$status = $escapeUtils->specialCharshStrip(trim($status));

				// get current time
				$time = date('d.m.Y H:i:s');

				// get user ip
				$remote_addr = $mainUtils->getRemoteAdress();

				// insert message 
				$queryInsert = $mysql->insertQuery("INSERT INTO `messages`(`name`, `email`, `message`, `time`, `remote_addr`, `status`) VALUES ('$name', '$email', '$message', '$time', '$remote_addr', '$status')");

				// check if message inserted
				if (!$queryInsert) {

					// log to mysql
					$mysql->logToMysql("recived-message", "by sender $name");
					
					$state = true;
				}
			}

			return $state;
		}

		// print all messages
		public function printMSGS() {

			global $visitorManager;

            // get messages array
            $messages = $this->getMessages();

			// default ID
			$userID = null;

            // show all messages from array messages in page
            foreach ($messages as $row) {

				// get user ID
				$userID = $visitorManager->getVisitorIDByIP($row["remote_addr"]);

				// ban link builder
				if ($visitorManager->isVisitorBanned($row["remote_addr"])) {
					$banLink = "<a class='deleteLink text-warning' href='?admin=visitors&action=ban&id=".$userID."&limit=500&startby=0&close=y' target='blank_'>UNBAN</a>";
				} else {
					$banLink = "<a class='deleteLink text-warning' href='?admin=visitors&action=ban&id=".$userID."&limit=500&startby=0&close=y&reason=spam' target='blank_'>BAN</a>";
				}

				// check if ip found
				if ($userID == null) {
					echo"<div class='card text-white mb-3' style='max-width: 95%;'><div class=card-body><h5 class=leftCenter class=card-title>".$row["name"]." (".$row["email"].") <span class='text-success phoneNone'>[".$row["time"]."]</span>, <span class='text-warning phoneNone'>[".$row["remote_addr"]."]</span><a class='deleteLink' href='?admin=inbox&delete=".$row["id"]."'>X</a></h5><p class=leftCenter class=card-text>".$row["message"]."</p></div></div>";
				} else {

					// check if sender banned
					if ($visitorManager->isVisitorBanned($row["remote_addr"])) {
						echo"<div class='card text-white mb-3' style='max-width: 95%;'><div class=card-body><h5 class=leftCenter class=card-title>".$row["name"]." (".$row["email"].") <span class='text-success phoneNone'>[".$row["time"]."]</span>, <span class='text-red phoneNone'>[".$row["remote_addr"]."]</span><a class='deleteLink' href='?admin=inbox&delete=".$row["id"]."'>X</a>".$banLink."</h5><p class=leftCenter class=card-text>".$row["message"]."</p></div></div>";
					} else {
						echo"<div class='card text-white mb-3' style='max-width: 95%;'><div class=card-body><h5 class=leftCenter class=card-title>".$row["name"]." (".$row["email"].") <span class='text-success phoneNone'>[".$row["time"]."]</span>, <span class='text-warning phoneNone'>[".$row["remote_addr"]."]</span><a class='deleteLink' href='?admin=inbox&delete=".$row["id"]."'>X</a>".$banLink."</h5><p class=leftCenter class=card-text>".$row["message"]."</p></div></div>";
					}
				}
            }
		}
		
		// felete message by id
		public function deleteMsgByID($id) {

			global $mysql, $userManager;

			// log process to mysql database 
			$mysql->logToMysql("close-message", "user ".$userManager->getCurrentUsername()." closed message id: $id");

			// update message status = close 
			$mysql->insertQuery("UPDATE messages SET status='closed' WHERE id='$id'");
		}
		
        // check if inbox empty
        public function isEmpty() {

			// default state output
			$state = false;

            // get messages from database
            $msgs = $this->getMessages();

			// check if msgs list empty
            if (count($msgs) < 1) {
                $state = true;
            } 
			
			return $state;
        }
	}
?>