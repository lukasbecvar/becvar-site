<?php //About component 

    //Add nav menu to site
    include_once("elements/navigation/HeaderElement.php");
?>
<main class="aboutContainer">
   
    <div style="color: white;">
        <h2 class="aboutTitle">About me</h2>
    </div>

    <?php

        //Include main description
        include_once("elements/public/AboutDescElement.php");

        //Include timeline element
        include_once("elements/public/ProjectTimelineElement.php");

        //Include skillbar element
        include_once("elements/public/SkillBarElement.php");
    ?>
</main>

<?php 

    //Add footer to site
   include_once("elements/navigation/FooterElement.php");
?>