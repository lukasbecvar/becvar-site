<div class="adminPanel">
<?php // private media browser component in admin
    
	// default values 
	$startByRow = 0;

    // check if user is owner 
	if (!$adminController->isUserOwner()) {
		echo"<h2 class=pageTitle>Sorry you dont have permission to this page</h2>";
	} else {

		// get page items limit
		$limitOnPage = $pageConfig->getValueByName("imagesInBrowserLimit");

		// if limit get seted make this trash part of code xD
		if (($siteController->getQueryString("limit") != null) && ($siteController->getQueryString("startby") != null)) {

			// get show limit form url
			$showLimit = $siteController->getQueryString("limit");

			// get start row form url
			$startByRow = $siteController->getQueryString("startby");

			// set next limit
			$nextLimit = (int) $showLimit + $limitOnPage;

			// set next start by for pages
			$nextStartByRow = (int) $startByRow + $limitOnPage;
			$nextLimitBack = (int) $showLimit - $limitOnPage;
			$nextStartByRowBack = (int) $startByRow - $limitOnPage;	
		}

        // print gallery list
        echo '<center><div id="lightgallery">';

        // draw all images to page
        $imagesUpload = mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT * FROM image_uploader ORDER BY id DESC LIMIT $startByRow, $limitOnPage");
    
        // print images (if found)
        if ($imagesUpload->num_rows != 0) {
            
            // print all images to gallery box
            while ($row = mysqli_fetch_assoc($imagesUpload)) {
                echo '<span data-src="data:image/jpg;base64,'.$row["image"].'" data-sub-html="Image <a class=imgEditButton href=?process=image&spec='.$row["imgSpec"].' target=blank_>'.$row["imgSpec"].'</a> | <a class=imgEditButton href=?admin=dbBrowser&delete=image_uploader&id='.$row["id"].'&close=y target=blank_>Delete</a>"><img class="gallery_images" src="data:image/jpg;base64,'.$row["image"].'"></span>'; 
            } 
        } else {
            echo"<h2 class=pageTitle>Image database is empty!</h2>";
        }
    
        //End of gallery list
        echo '</div></center><br>'; 


        // pager button box check
        if (($siteController->getQueryString("limit") != null) and ($siteController->getQueryString("startby") != null) and ($siteController->getQueryString("action") == null)) {

            echo '<div class="pageButtonBox">';
        
            // print back button if user in next page
            if ($showLimit > $limitOnPage) {
                echo '<br><a class="backPageButton" href=?admin=mediaBrowser&limit='.$nextLimitBack.'&startby='.$nextStartByRowBack.'>Back</a><br>';
            }

            // print next button if user on start page and can see next items
            if ($imagesUpload->num_rows == $limitOnPage) {
                echo '<br><a class="backPageButton" href=?admin=mediaBrowser&limit='.$nextLimit.'&startby='.$nextStartByRow.'>Next</a><br>';	
            }
    
            echo '</div>';
        }

        // end of image box
        echo '<br><br></div>';	
    }
?>
<!----------------------- import scripts ---------------------------->
<script src="/assets/vendor/lightgallery/js/lightgallery.js"></script>
<script src="/assets/vendor/lightgallery/js/lg-zoom.js"></script>
<script src="/assets/vendor/lightgallery/js/lg-autoplay.js"></script>
<script>lightGallery(document.getElementById('lightgallery'));</script>
<!-------------------- end of import scripts ------------------------>
</div>
 