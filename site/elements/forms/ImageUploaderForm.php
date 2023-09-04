<?php // upload function
	if (isset($_POST["submitUpload"])) { 
        
		// extract file extension
        $ext = substr(strrchr($_FILES["userfile"]["name"], '.'), 1);      
		
		// check if file is image
        if ($ext == "gif" or $ext == "jpg" or $ext == "jpeg" or $ext == "jfif" or $ext == "pjpeg" or $ext == "pjp" or $ext == "png" or $ext == "webp" or $ext == "bmp" or $ext == "ico") {		
			
			// generate imgSpec value
			$imgSpec = $stringUtils->genRandomStringAll(40);
			
			// get image file
			$imageFile = file_get_contents($_FILES['userfile']['tmp_name']);

			// escape and encode image
			$image = $escapeUtils->specialCharshStrip(base64_encode($imageFile));

			// get current data
			$date = date('d.m.Y H:i:s');

			// insert query to mysql table images
			$mysql->insertQuery("INSERT INTO `image_uploader`(`imgSpec`, `image`, `date`) VALUES ('$imgSpec', '$image', '$date')");				

			// log to mysql
			$mysql->logToMysql("image-uploader", "uploaded new image: ".$imgSpec);	

			// redirect to image view
			$urlUtils->jsRedirect("?process=image&spec=".$imgSpec);

		} else {
			$alertManager->flashError("Error file have wrong format!", true);
		}
	}
?>

<form class="form-limiterr" action="/#uploader" method="post" enctype="multipart/form-data">
    <div class="file-upload">
        <p class="form-title">Image upload</p>
        <div class="image-upload-wrap">
            <input class="file-upload-input" type="file" name="userfile" onchange="readURL(this);" accept="image/*" />
            <div class="drag-text">
                <h3>Drag and drop a file or select add Image</h3>
            </div>
        </div>
        <div class="file-upload-content">
            <img class="file-upload-image" src="#" alt="your image" />
            <div class="image-title-wrap">
                <button type="button" onclick="removeUpload()" class="remove-image">Remove <span class="image-title">Uploaded Image</span></button>
            </div>
        </div><br>
        <input class="file-upload-btn" type="submit" value="Upload Image" name="submitUpload">
    </div>
</form>