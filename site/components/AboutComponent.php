<section id="about" class="about">
    <div class="about-me container">
        <div class="section-title">
            <h2>About</h2>
        </div>
        <?php 
            //Include main info
            include(__DIR__."/../elements/about/AboutInfoElement.php");

            //Include counter element
            include(__DIR__."/../elements/about/AboutCounterElement.php");

            //Include skills element
            include(__DIR__."/../elements/about/AboutSkillsElement.php");
            
            //Include skills element
            include(__DIR__."/../elements/about/AboutGithubGraph.php");
        ?>
    </div>
</section>
<script src="assets/vendor/purecounter/purecounter.js"></script>
<script src="assets/vendor/waypoints/noframework.waypoints.js"></script>