<?php // code paste component paste save

	// check if user save paste
	if (isset($_POST['data'])) {
 
		// get data from post
		$content_raw = $escape_utils->special_chars_strip($_POST['data']);

		// get file name
		$name = $escape_utils->special_chars_strip($_POST['file']);

		// select content to write (Escape [XSS Protection])
		$content = str_replace(array("<", ">"), array("&lt;", "&gt;"), $content_raw);

		// get date
		$date = date('d.m.Y H:i:s');
		
		// check if maximum lenght reached
		if (strlen($content_raw) > 60001) {

			// redirect error
			$site_manager->redirect_error(400);
			$site_manager->handle_error("error: this paste reached maximum characters 60000", 400);

		} else {

			// save paste to mysql table
			if (!empty($content)) {
				$mysql->insert("INSERT INTO pastes(spec, content, date) VALUES ('$name', '$content', '$date')");
			} 

			// log to mysql
			$mysql->log("paste", "saved new paste: ".$name);	
		}
	}
?>