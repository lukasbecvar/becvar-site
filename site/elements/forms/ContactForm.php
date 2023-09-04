<form action="/#contact" method="post" role="form" class="msg-from mt-4">
    <div class="row">
        <div class="col-md-6 form-group">
            <input type="text" name="name" class="form-control" id="name" placeholder="Your Name" required>
        </div>
        <div class="col-md-6 form-group mt-3 mt-md-0">
            <input type="email" class="form-control" name="email" id="email" placeholder="Your Email" required>
        </div>
        <div class="col-md-6 form-group mt-3 mt-md-0 websiteIN">
            <input class="websiteIN" name="nickname" type="text" class="feedback-input" placeholder="Website"/>
        </div>
    </div>
    <div class="form-group mt-3">
        <textarea class="form-control resize-disable" name="message" rows="5" placeholder="Message" required></textarea>
    </div>
    <div class="my-3">
        <?php 
            // check if request is post
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                // check if post have value email (contact form verification)
                if (isset($_POST["nickname"])) {

                    // init values from form and escape
                    $name = $escapeUtils->specialCharshStrip($_POST["name"]);
                    $email = $escapeUtils->specialCharshStrip($_POST["email"]);
                    $message = $escapeUtils->specialCharshStrip($_POST["message"]);

                    // honeypot check
                    if (empty($_POST["website"])) {

                        // check if inputs is not empty
                        if (empty($name) or empty($email) or empty($message)) {
                            echo '<div class="error-message">You must enter all inputs!</div>';
                        } else {

                            // save msg to database
                            $sendMSG = $contactManager->sendMessage($name, $email, $message, "open");
                    
                            // flash msg with status
                            if ($sendMSG) {
                                echo '<div class="sent-message">Your message has been sent. Thank you!</div>';
                            } else {
                                echo '<div class="error-message">System error please contact page administrator!</div>';
                            }
                        }

                    // flash error msg
                    } else {
                        echo '<div class="error-message">System error please contact page administrator!</div>';
                    }
                }
            }
        ?>
    </div>
    <div class="text-center"><button type="submit">Send Message</button></div>
</form>