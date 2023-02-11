<?php // code paste component paste save

	// check if user save paste
	if (isset($_POST['data'])) {

		// get data from post
		$content = $mysqlUtils->escapeString($_POST['data'], true, true);

		// get file name
		$name = $mysqlUtils->escapeString($_POST['file'], true, true);

		// select content to write (Escape [XSS Protection])
		$content = str_replace(array("<", ">"), array("&lt;", "&gt;"), $content);

		// get date
		$date = date('d.m.Y H:i:s');

		// save paste to mysql table
		if (!empty($content)) {
			$mysqlUtils->insertQuery("INSERT INTO `pastes`(`spec`, `content`, `date`) VALUES ('$name', '$content', '$date')");
		}

		// log to mysql
		$mysqlUtils->logToMysql("Paste", "added new paste: ".$name);	
	}
?>
