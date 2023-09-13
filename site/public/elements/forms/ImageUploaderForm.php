<?php // upload function
	if (isset($_POST["submitUpload"])) { 
        
		// extract file extension
        $ext = substr(strrchr($_FILES["userfile"]["name"], '.'), 1);      
		
		// check if file is image
        if ($ext == "gif" or $ext == "jpg" or $ext == "jpeg" or $ext == "jfif" or $ext == "pjpeg" or $ext == "pjp" or $ext == "png" or $ext == "webp" or $ext == "bmp" or $ext == "ico") {		
			
			// generate img_spec value
			$img_spec = $string_utils->gen_random_sring(40);
			
			// get image file
			$image_file = file_get_contents($_FILES['userfile']['tmp_name']);

			// escape and encode image
			$image = $escape_utils->special_chars_strip(base64_encode($image_file));

			// get current data
			$date = date('d.m.Y H:i:s');

			// insert query to mysql table images
			$mysql->insert("INSERT INTO `image_uploader`(`img_spec`, `image`, `date`) VALUES ('$img_spec', '$image', '$date')");				

			// log to mysql
			$mysql->log("image-uploader", "uploaded new image: ".$img_spec);	

			// redirect to image view
			$url_utils->js_redirect("?process=image&spec=".$img_spec);

		} else {
			$alert_manager->flash_error("Error file have wrong format!", true);
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