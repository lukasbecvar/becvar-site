<?php // contact manager (read, send, delelete msg)

	namespace becwork\managers;

	class ContactManager {

		// get messages from database
		public function get_messages(): ?array {

			global $mysql;

			$messages = $mysql->fetch("SELECT * from messages WHERE status = 'open' ORDER BY id DESC"); 
			return $messages;
		}

		// get banned email count
		public function get_banned_email_count($email): ?string {

			global $mysql;

			// get banned emails
			$banned_emails = $mysql->fetch("SELECT * FROM banned_emails WHERE email = '$email'");
			
			// get emails count
			$banned_email_count = count($banned_emails);

			return $banned_email_count;
		}

		// send message to database
		public function send_message($name, $email, $message, $status): bool {

			global $mysql, $main_utils, $escape_utils;

			// default state value
			$state = false;

			// check if email banned
			if ($this->get_banned_email_count($email) > 0) {

				// log to mysql
				$mysql->log("message-block", "blocked email: $email");
				
			} else {
				// get & escape values
				$name = $escape_utils->special_chars_strip(trim($name));
				$email = $escape_utils->special_chars_strip(trim($email));
				$message = $escape_utils->special_chars_strip(trim($message));
				$status = $escape_utils->special_chars_strip(trim($status));

				// get current time
				$time = date('d.m.Y H:i:s');

				// get user ip
				$remote_addr = $main_utils->get_remote_adress();

				// insert message 
				$query_insert = $mysql->insert("INSERT INTO `messages`(`name`, `email`, `message`, `time`, `remote_addr`, `status`) VALUES ('$name', '$email', '$message', '$time', '$remote_addr', '$status')");

				// check if message inserted
				if (!$query_insert) {

					// log to mysql
					$mysql->log("recived-message", "by sender $name");
					
					$state = true;
				}
			}

			return $state;
		}

		// block email (call if user banned from inbox)
		public function block_email($email): void {

			global $mysql;
		
			// insert new blocked email 
			$mysql->insert("INSERT INTO `banned_emails`(`email`) VALUES ('$email')");
		}

		// close message by id
		public function close_message($id): void {

			global $mysql, $user_manager;

			// log process to mysql database 
			$mysql->log("close-message", "user ".$user_manager->get_username()." closed message id: $id");

			// update message status = close 
			$mysql->insert("UPDATE messages SET status='closed' WHERE id='$id'");
		}
		
        // check if inbox empty
        public function is_empty(): bool {

			// default state output
			$state = false;

            // get messages from database
            $msgs = $this->get_messages();

			// check if msgs list empty
            if (count($msgs) < 1) {
                $state = true;
            } 
			
			return $state;
        }

		// print all messages
		public function print_messages(): void {

			global $visitor_manager;

            // get messages array
            $messages = $this->get_messages();

			// default ID
			$user_id = null;

            // show all messages from array messages in page
            foreach ($messages as $row) {

				// get user ID
				$user_id = $visitor_manager->get_visitor_id_by_ip($row["remote_addr"]);

				// get email
				$email = $row["email"];

				// ban link builder
				if ($visitor_manager->is_visitor_banned($row["remote_addr"])) {
					$ban_state = "UNBAN";
					$ban_link = "?admin=visitors&action=ban&id=$user_id&limit=500&startby=0&close=y&email=$email";
				} else {
					$ban_state = "BAN";
					$ban_link = "?admin=visitors&action=ban&id=$user_id&limit=500&startby=0&close=y&reason=spam&email=$email";
				}

				// check if ip found
				if ($user_id == null) {

					# unknow user msg
					echo "
						<div class='card text-white mb-3' style='max-width: 95%;'>
							<div class=card-body>
								<h5 class=leftCenter class=card-title>".$row["name"]." (".$row["email"].") 
									<span class='text-success phoneNone'>[".$row["time"]."]</span>, 
									<span class='text-warning phoneNone'>[".$row["remote_addr"]."]</span>
									<a class='deleteLink' href='?admin=inbox&delete=".$row["id"]."'>X</a>
								</h5>
								<p class=leftCenter class=card-text>".$row["message"]."</p>
							</div>
						</div>
					";
				} else {

					// check if sender banned
					if ($visitor_manager->is_visitor_banned($row["remote_addr"])) {
						echo "
							<div class='card text-white mb-3' style='max-width: 95%;'>
								<div class=card-body>
									<h5 class=leftCenter class=card-title>".$row["name"]." (".$row["email"].") 
										<span class='text-success phoneNone'>[".$row["time"]."]</span>, 
										<span class='text-red phoneNone'>[".$row["remote_addr"]."]</span>
										<a class='deleteLink' href='?admin=inbox&delete=".$row["id"]."'>X</a>
										<a class='deleteLink text-warning' href=".$ban_link." target='blank_'>".$ban_state."</a>
									</h5>
									<p class=leftCenter class=card-text>".$row["message"]."</p>
								</div>
							</div>
						";

					# non banned user msg
					} else {
						echo "
							<div class='card text-white mb-3' style='max-width: 95%;'>
								<div class=card-body>
									<h5 class=leftCenter class=card-title>".$row["name"]." (".$row["email"].") 
										<span class='text-success phoneNone'>[".$row["time"]."]</span>, 
										<span class='text-warning phoneNone'>[".$row["remote_addr"]."]</span>
										<a class='deleteLink' href='?admin=inbox&delete=".$row["id"]."'>X</a>
										<a class='deleteLink text-warning' href=".$ban_link." target='blank_'>".$ban_state."</a>
									</h5>
									<p class=leftCenter class=card-text>".$row["message"]."</p>
								</div>
							</div>
						";
					}
				}
            }
		}
	}
?>
