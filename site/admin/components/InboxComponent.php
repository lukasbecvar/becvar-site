<div class="admin-panel">
    <center>
        <?php // admin inbox component (for show msgs from contact site)
        
            // check if user typed id
            if ($site_manager->get_query_string("delete") != null) {

                // get and escape string id form url and save to id
                $id = $site_manager->get_query_string("delete");

                // delete msg by id
                $contact_manager->close_message($id);

                // redirect to messages page
                $url_utils->js_redirect("?admin=inbox");
            } 
         
            // print msgs is not empty
            if ($contact_manager->is_empty()) {
                echo"<h2 class=page-title>Inbox is empty</h2>";
            } else {
                $contact_manager->print_messages();
            }
        ?><br>
    </center>
</div>