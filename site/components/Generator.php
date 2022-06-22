<?php //Hash & Password generator components 

	//Add nav menu to site
	include_once("elements/navigation/HeaderElement.php");
?>
<main class="generator">
<?php

	//Check if post request sent
	if (isset($_POST["submitBase64Encode"])) {
		$encryptController->base64Encode($_POST["stringToEncode"]);
	} 
	
	elseif (isset($_POST["submitBase64Decode"])) {
		$encryptController->base64Decode($_POST["stringToDecode"]);
	}

	elseif (isset($_POST["submitBase64ImageEncode"])) {
		$encryptController->base64ImageEncode($_FILES["fileToUpload"]);
	}

	elseif (isset($_POST["submitBase64ImageDecode"])) {
		$encryptController->base64ImageDecode($_POST["stringToImageDecode"]);
	}

	elseif (isset($_POST["submitAESEncrypt"])) {		
		$encryptController->aesEncrypt($_POST["stringToEncryptAES"], $_POST["AESEncryptKey"], $_POST["AESEncryptMethod"], $_POST["AESEncryptBits"]);	
	}

	elseif (isset($_POST["submitAESDecrypt"])) {		
		$encryptController->aesDecrypt($_POST["stringToDecryptAES"], $_POST["AESDecryptKey"], $_POST["AESDecryptMethod"], $_POST["AESDecryptBits"]);	
	}
	/////////////////////////////////////////////////////////////////////////////////////


	//Password & hash
	echo '<p class="pageTitle borderer-bot">Password & hash</p>';

	//Include hash generator
	include_once("elements/forms/generatorForms/HashGeneratorForm.php");

	//Include Password generator
	include_once("elements/forms/generatorForms/PasswordGeneratorForm.php");	



	//Base64
	echo '<br class="removeFloat"><br class="removeFloat"><br class="removeFloat"><p class="removeFloat pageTitle borderer-bot">Base64</p>';

	//Include Base64 decoder
	include_once("elements/forms/generatorForms/base64/Base64DecoderForm.php");	

	//Include Base64 encoder
	include_once("elements/forms/generatorForms/base64/Base64EncoderForm.php");	

	//Include Base64 image decoder
	include_once("elements/forms/generatorForms/base64/Base64ImageDecoderForm.php");	

	//Include Base64 image encoder
	include_once("elements/forms/generatorForms/base64/Base64ImageEncoderForm.php");
	
	

	//AES
	echo '<br class="removeFloat"><br class="removeFloat"><br class="removeFloat"><p class="removeFloat pageTitle borderer-bot">AES</p>';

	//Include AES encryption form
	include_once("elements/forms/generatorForms/aes/AESDecryptionForm.php");

	//Include AES encryption form
	include_once("elements/forms/generatorForms/aes/AESEncryptionForm.php");	
?>
<br class="removeFloat">
</main>
<?php //Add footer to site
	include_once("elements/navigation/FooterElement.php");
?>