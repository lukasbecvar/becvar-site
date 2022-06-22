<div class="contactPanel">
<?php //The private media browser component in admin
    
    //Check if user is owner 
	if (!$adminController->isUserOwner()) {
		echo"<h2 class=pageTitle>Sorry you dont have permission to this page</h2>";
	} else {

		//Page items limit
		$limitOnPage = $pageConfig->getValueByName("imagesInBrowserLimit");

		//If limit get seted make this trash part of code xD
		if (isset($_GET["limit"]) && isset($_GET["startby"])) {

			//Get show limit form url
			$showLimit = $mysqlUtils->escapeString($_GET["limit"], true, true);

			//Get start row form url
			$startByRow = $mysqlUtils->escapeString($_GET["startby"], true, true);

			//Set next limit
			$nextLimit = (int) $showLimit + $limitOnPage;

			//Set next start by for pages
			$nextStartByRow = (int) $startByRow + $limitOnPage;
			$nextLimitBack = (int) $showLimit - $limitOnPage;
			$nextStartByRowBack = (int) $startByRow - $limitOnPage;	
		}

        //Print gallery list
        echo '<center><div id="lightgallery">';

        //Draw all images to page
        $imagesUpload = mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT * FROM image_uploader ORDER BY id DESC LIMIT $startByRow, $limitOnPage");
    
        while ($row = mysqli_fetch_assoc($imagesUpload)) {
            echo '<span data-src="data:image/png;base64,'.$row["image"].'" data-sub-html="Image <a class=imgEditButton href=index.php?process=image&spec='.$row["imgSpec"].' target=blank_>'.$row["imgSpec"].'</a> | <a class=imgEditButton href=index.php?page=admin&process=dbBrowser&delete=image_uploader&id='.$row["id"].'&close=y target=blank_>Delete</a>"><img class="gallery_images" src="data:image/png;base64,'.$row["image"].'"></span>'; 
        } 
    
        //End of gallery list
        echo '</div></center><br>'; 


        if (isset($_GET["limit"]) and isset($_GET["startby"]) and !isset($_GET["action"])) {

            echo '<div class="pageButtonBox">';
        
            //Print back button if user in next page
            if ($showLimit > $limitOnPage) {
                echo '<br><a class="backPageButton" href=index.php?page=admin&process=mediaBrowser&limit='.$nextLimitBack.'&startby='.$nextStartByRowBack.'>Back</a><br>';
            }


            //Print next button if user on start page and can see next items
            if ($imagesUpload->num_rows == $limitOnPage) {
                echo '<br><a class="backPageButton" href=index.php?page=admin&process=mediaBrowser&limit='.$nextLimit.'&startby='.$nextStartByRow.'>Next</a><br>';	
            }
    
            echo '</div>';
        }

        //End of image box
        echo '<br><br></div>';	
    }
?>
<script src="assets/js/lg/lightgallery.js"></script>
<script src="assets/js/lg/lg-zoom.js"></script>
<script src="assets/js/lg/lg-autoplay.js"></script>
<script>lightGallery(document.getElementById('lightgallery'));</script>
</div>
