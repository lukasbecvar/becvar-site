<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
	<title><?= $site_manager->get_page_title() ?></title>
    <meta name="google-site-verification" content="<?= $config->get_value("google-verify-token") ?>"/>
    <meta content="<?= $config->get_value("site-description") ?>" name="description">
    <meta content="<?= $config->get_value("site-keywords") ?>" name="keywords">

    <!-------------------- import assets ------------------------>
    <link href="/assets/img/favicon.png" rel="icon">
    <link href="/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="/assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="/assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="/assets/css/public.css" rel="stylesheet">
    <!-------------------- end of assets import ------------------------>
</head>
<body>
    <?php // include components & elements to site 
    
        // include main header element
        include_once("elements/MainElement.php");

        // include about component
        include_once("components/AboutComponent.php");
 
        // include projects component
        include_once("components/ProjectsComponent.php");

        // include contact component
        include_once("components/ContactComponent.php");

        // include uploader component
        include_once("components/ImageUploaderComponent.php");  
    ?>
    <!----------------------- import scripts ---------------------------->
    <script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/vendor/jquery/jquery-3.6.0.min.js"></script>
    <script src="/assets/js/public.js"></script> 
    <!-------------------- end of import scripts ------------------------>
</body>
</html>