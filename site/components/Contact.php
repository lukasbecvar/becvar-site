<?php //Contact form component 

	//Add nav menu to pag
	include_once("elements/navigation/HeaderElement.php");

	//Page layout
	echo '<main class="homePage">';

	//Check if user submit send message button
	if (isset($_POST["submit"])) {

		//honeypot check
		if (!empty($_POST["website"])) {
			
			$urlUtils->jsRedirect("ErrorHandlerer.php?code=400");

		} else {
			//Check if inputs is not empty
			if (empty($_POST["name"]) or empty($_POST["email"]) or empty($_POST["message"])) {
				
				//Flash error mesage if inputs empty
				$alertController->flashError("You must enter all inputs!");
			} else {

				//Send msg with controller
				$sendMSG = $contactController->sendMessage($_POST["name"], $_POST["email"], $_POST["message"], "open");

				if ($sendMSG) {
					$urlUtils->redirect("index.php?page=contact&status=success");
				} else {
					$urlUtils->redirect("index.php?page=contact&status=error");
				}
			}
		}
	}


	//Print send status
	if(!empty($_GET["status"])) {

		if ($_GET["status"] == "success") {
			$alertController->flashSuccess("Your message has been sended.");
		} elseif ($_GET["status"] == "error") {
			$alertController->flashError("Database error please contact page administrator!");
		}
	}


	//include contact form
	include_once("elements/forms/ContactForm.php");


	//include contact links bar
	include_once("elements/public/ContactBarElement.php");

	//End of page div
	echo '</div>';

	//Add footer to site
	include_once("elements/navigation/FooterElement.php");
?>
