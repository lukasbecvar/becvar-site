<div class="admin-panel">
<?php // private media browser component in admin
    
	// default values 
	$start_by_row = 0;

    // check if user is owner 
	if (!$user_manager->is_user_Owner()) {
		echo"<h2 class=page-title>Sorry you dont have permission to this page</h2>";
	} else {

		// get page items limit
		$limit_on_page = $config->get_value("images-in-browser-limit");

		// if limit get seted make this trash part of code xD
		if (($site_manager->get_query_string("limit") != null) && ($site_manager->get_query_string("startby") != null)) {

			// get show limit form url
			$show_limit = $site_manager->get_query_string("limit");

			// get start row form url
			$start_by_row = $site_manager->get_query_string("startby");

			// set next limit
			$next_limit = (int) $show_limit + $limit_on_page;

			// set next start by for pages
			$next_start_by_row = (int) $start_by_row + $limit_on_page;
			$next_limit_back = (int) $show_limit - $limit_on_page;
			$next_start_by_row_back = (int) $start_by_row - $limit_on_page;	
		}

        // print gallery list
        echo '<center><div id="lightgallery">';

        // draw all images to page
        $images_upload = $mysql->fetch("SELECT * FROM image_uploader ORDER BY id DESC LIMIT $start_by_row, $limit_on_page");
    
        // print images (if found)
        if (count($images_upload) != 0) {
            
            // print all images to gallery box
            foreach ($images_upload as $row) {
                echo '<span data-src="data:image/jpg;base64,'.$row["image"].'" data-sub-html="Image <a class=img-edit-button href=?process=image&spec='.$row["img_spec"].' target=blank_>'.$row["img_spec"].'</a> | <a class=img-edit-button href=?admin=dbBrowser&delete=image_uploader&id='.$row["id"].'&close=y target=blank_>Delete</a>"><img class="gallery-images" src="data:image/jpg;base64,'.$row["image"].'"></span>'; 
            } 
        } else {
            echo"<h2 class=page-title>Image database is empty!</h2>";
        }
    
        //End of gallery list
        echo '</div></center><br>'; 


        // pager button box check
        if (($site_manager->get_query_string("limit") != null) and ($site_manager->get_query_string("startby") != null) and ($site_manager->get_query_string("action") == null)) {

            echo '<div class="page-button-box">';
        
            // print back button if user in next page
            if ($show_limit > $limit_on_page) {
                echo '<br><a class="back-page-button" href=?admin=mediaBrowser&limit='.$next_limit_back.'&startby='.$next_start_by_row_back.'>Back</a><br>';
            }

            // print next button if user on start page and can see next items
            if (count($images_upload) == $limit_on_page) {
                echo '<br><a class="back-page-button" href=?admin=mediaBrowser&limit='.$next_limit.'&startby='.$next_start_by_row.'>Next</a><br>';	
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