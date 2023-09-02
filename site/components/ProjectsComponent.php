<section id="projects" class="projects">
    <div class="container">
        <div class="section-title">
            <h2>Projects</h2>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <h3 class="projects-title">Open</h3>
                <?php // open projects
                    include(__DIR__."/../elements/projects/ProjectsOpenElement.php");
                ?>
            </div>
            <div class="col-lg-6">
                <h3 class="projects-title">Closed</h3>
                <?php // closed projects
                    include(__DIR__."/../elements/projects/ProjectsClosedElement.php");
                ?>
        </div>
        <?php // github referal link
            include(__DIR__."/../elements/projects/GitHubReferal.php");
        ?>
    </div>
</section>