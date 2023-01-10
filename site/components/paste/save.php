<?php // code paste component paste save

	// check if user save paste
	if (isset($_POST['data'])) {

		// get data from post
		$conent = $mysqlUtils->escapeString($_POST['data']);

		// get file name
		$name = $mysqlUtils->escapeString($_POST['file'], true, true);

		// select conent to write (Escape [XSS Protection])
		$conent = str_replace(array("<", ">"), array("&lt;", "&gt;"), $conent);

		// get link on paste
		$link = $name;

		// get date
		$date = date('d.m.Y H:i:s');

		// save paste to mysql table
		if (!empty($conent)) {
			$mysqlUtils->insertQuery("INSERT INTO `pastes`(`link`, `spec`, `content`, `date`) VALUES ('$link', '$name', '$conent', '$date')");
		}

		// log to mysql
		$mysqlUtils->logToMysql("Paste", "added new paste: ".$link);	
	}
?>
