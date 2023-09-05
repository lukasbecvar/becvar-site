<?php // contact manager (read, send, delelete msg)

	namespace becwork\managers;

	class ContactManager {

		// get messages from database
		public function getMessages(): ?array {

			global $mysql;

			$messages = $mysql->fetch("SELECT * from messages WHERE status = 'open' ORDER BY id DESC"); 
			return $messages;
		}

		// get banned email count
		public function getBannedEmailCount($email): ?string {

			global $mysql;

			// get banned emails
			$banned_emails = $mysql->fetch("SELECT * FROM banned_emails WHERE email = '$email'");
			
			// get emails count
			$banned_email_count = count($banned_emails);

			return $banned_email_count;
		}

		// send message to database
		public function sendMessage($name, $email, $message, $status): bool {

			global $mysql, $mainUtils, $escapeUtils;

			// default state value
			$state = false;

			// check if email banned
			if ($this->getBannedEmailCount($email) > 0) {

				// log to mysql
				$mysql->logToMysql("message-block", "blocked email: $email");
				
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
				$query_insert = $mysql->insertQuery("INSERT INTO `messages`(`name`, `email`, `message`, `time`, `remote_addr`, `status`) VALUES ('$name', '$email', '$message', '$time', '$remote_addr', '$status')");

				// check if message inserted
				if (!$query_insert) {

					// log to mysql
					$mysql->logToMysql("recived-message", "by sender $name");
					
					$state = true;
				}
			}

			return $state;
		}

		// print all messages
		public function printMSGS(): void {

			global $visitorManager;

            // get messages array
            $messages = $this->getMessages();

			// default ID
			$user_ID = null;

            // show all messages from array messages in page
            foreach ($messages as $row) {

				// get user ID
				$user_ID = $visitorManager->getVisitorIDByIP($row["remote_addr"]);

				// ban link builder
				if ($visitorManager->isVisitorBanned($row["remote_addr"])) {
					$ban_link = "<a class='deleteLink text-warning' href='?admin=visitors&action=ban&id=".$user_ID."&limit=500&startby=0&close=y' target='blank_'>UNBAN</a>";
				} else {
					$ban_link = "<a class='deleteLink text-warning' href='?admin=visitors&action=ban&id=".$user_ID."&limit=500&startby=0&close=y&reason=spam' target='blank_'>BAN</a>";
				}

				// check if ip found
				if ($user_ID == null) {
					echo"<div class='card text-white mb-3' style='max-width: 95%;'><div class=card-body><h5 class=leftCenter class=card-title>".$row["name"]." (".$row["email"].") <span class='text-success phoneNone'>[".$row["time"]."]</span>, <span class='text-warning phoneNone'>[".$row["remote_addr"]."]</span><a class='deleteLink' href='?admin=inbox&delete=".$row["id"]."'>X</a></h5><p class=leftCenter class=card-text>".$row["message"]."</p></div></div>";
				} else {

					// check if sender banned
					if ($visitorManager->isVisitorBanned($row["remote_addr"])) {
						echo"<div class='card text-white mb-3' style='max-width: 95%;'><div class=card-body><h5 class=leftCenter class=card-title>".$row["name"]." (".$row["email"].") <span class='text-success phoneNone'>[".$row["time"]."]</span>, <span class='text-red phoneNone'>[".$row["remote_addr"]."]</span><a class='deleteLink' href='?admin=inbox&delete=".$row["id"]."'>X</a>".$ban_link."</h5><p class=leftCenter class=card-text>".$row["message"]."</p></div></div>";
					} else {
						echo"<div class='card text-white mb-3' style='max-width: 95%;'><div class=card-body><h5 class=leftCenter class=card-title>".$row["name"]." (".$row["email"].") <span class='text-success phoneNone'>[".$row["time"]."]</span>, <span class='text-warning phoneNone'>[".$row["remote_addr"]."]</span><a class='deleteLink' href='?admin=inbox&delete=".$row["id"]."'>X</a>".$ban_link."</h5><p class=leftCenter class=card-text>".$row["message"]."</p></div></div>";
					}
				}
            }
		}
		
		// felete message by id
		public function deleteMsgByID($id): void {

			global $mysql, $userManager;

			// log process to mysql database 
			$mysql->logToMysql("close-message", "user ".$userManager->getCurrentUsername()." closed message id: $id");

			// update message status = close 
			$mysql->insertQuery("UPDATE messages SET status='closed' WHERE id='$id'");
		}
		
        // check if inbox empty
        public function isEmpty(): bool {

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