<?php // contact controller (read, sent, delelete msg)

	namespace becwork\controllers;

	class ContactController {

		// send message to database
		public function sendMessage($name, $email, $message, $status) {

			global $mysqlUtils;
			global $mainUtils;

			// get & escape values
			$name = $mysqlUtils->escapeString(trim($name), true, true);
			$email = $mysqlUtils->escapeString(trim($email), true, true);
			$message = $mysqlUtils->escapeString(trim($message), true, true);
			$time = date('d.m.Y H:i:s');
			$remote_addr = $mainUtils->getRemoteAdress();
			$status = $mysqlUtils->escapeString(trim($status), true, true);

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

			global $mysqlUtils;
			global $pageConfig;
			global $visitorController;

            // save all messages if status open to this array
            $messages = mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT * from messages WHERE status = 'open' ORDER BY id DESC"); 

			// default ID
			$userID = NULL;

            // show all messages from array messages in page
            while ($row = mysqli_fetch_assoc($messages)) {

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

            global $mysqlUtils;
			global $pageConfig;

            // select msgs count form database
            $msgsCount = mysqli_fetch_assoc(mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT COUNT(*) AS count FROM messages WHERE status='open'"));

			// check if msgs list empty
            if ($msgsCount["count"] == 0) {
                return true;
            } else {
                return false;
            }
        }
	}
?>