<?php // image viewer component

    // check if image specified
    if ($site_manager->get_query_string("spec") != null) {

        // get image spec
        $img_spec = $site_manager->get_query_string("spec");

        // get image by specID
        $image = $mysql->fetch_value("SELECT image FROM image_uploader WHERE img_spec='".$img_spec."'", "image");
    
        // check if image found
        if ($image == null) {
            $site_manager->redirect_error(404);
        } else {

            // page View
            include($_SERVER['DOCUMENT_ROOT'].'/../site/public/elements/ImageViewPage.php');
        }

    } else {

        // handle error
        $site_manager->handle_error("[DEV-MODE]:Error: image spec is empty", 404);
    }
?>