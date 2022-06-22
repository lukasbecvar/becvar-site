<?php //Image uploader component

	//Add nav menu element to site
	include_once("elements/navigation/HeaderElement.php");

	//Upload function
	if (isset($_POST["submitUpload"])) { 
        
		//Extract file extension
        $ext = substr(strrchr($_FILES["userfile"]["name"], '.'), 1);      
		
		//Check if file is image
        if ($ext == "gif" or $ext == "jpg" or $ext == "jpeg" or $ext == "jfif" or $ext == "pjpeg" or $ext == "pjp" or $ext == "png" or $ext == "webp" or $ext == "bmp" or $ext == "ico") {		
			
			//Generate imgSpec value
			$imgSpec = $stringUtils->genRandomStringAll(40);
			
			//Get image file
			$imageFile = file_get_contents($_FILES['userfile']['tmp_name']);

			//Escape and encode image
			$image = $mysqlUtils->escapeString(base64_encode($imageFile), true, true);

			//Get current data
			$date = date('d.m.Y H:i:s');

			//Insert query to mysql table images
			$mysqlUtils->insertQuery("INSERT INTO `image_uploader`(`imgSpec`, `image`, `date`) VALUES ('$imgSpec', '$image', '$date')");				

			//Log to mysql
			$mysqlUtils->logToMysql("Uploader", "uploaded new image");	

			//Redirect to image view
			$urlUtils->redirect("index.php?process=image&spec=".$imgSpec);

		} else {
			die($alertController->flashError("Error file have wrong format!"));
		}
	}

	
	//Include uploader form
	include_once("elements/forms/ImageUploaderForm.php");


	//Add footer to page
	include_once("elements/navigation/FooterElement.php");
?> 