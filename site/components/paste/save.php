<?php //Code paste component paste save

	//Check if user save paste
	if (isset($_POST['data'])) {

		//Get data from post
		$conent = $mysqlUtils->escapeString($_POST['data']);

		//Get file name
		$name = $mysqlUtils->escapeString($_POST['file'], true, true);

		//Select conent to write (Escape [XSS Protection])
		$conent = str_replace(array("<", ">"), array("&lt;", "&gt;"), $conent);

		//Get link on paste
		$link = $name;

		//Get date
		$date = date('d.m.Y H:i:s');

		//Save paste to mysql table
		if (!empty($conent)) {
			$mysqlUtils->insertQuery("INSERT INTO `pastes`(`link`, `spec`, `content`, `date`) VALUES ('$link', '$name', '$conent', '$date')");
		}

		//Log to mysql
		$mysqlUtils->logToMysql("Paste", "added new paste: ".$link);	
	}
?>
