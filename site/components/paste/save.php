<?php //Code paste component paste save

	//Check if user save paste
	if (isset($_POST['data'])) {

		//Get data from post
		$data = $mysqlUtils->escapeString($_POST['data']);

		//Get file name
		$name = $mysqlUtils->escapeString($_POST['file'], true, true);

		//Select data to write
		$data = str_replace(array("<", ">"), array("&lt;", "&gt;"), $data);

		//Get link on paste
		$link = $name;

		$date = date('d.m.Y H:i:s');

		//Save paste to mysql table
		if (!empty($data)) {
			$mysqlUtils->insertQuery("INSERT INTO `pastes`(`link`, `spec`, `content`, `date`) VALUES ('$link', '$name', '$data', '$date')");
		}

		//Log to mysql
		$mysqlUtils->logToMysql("Paste", "added new paste: ".$link);	
	}
?>
