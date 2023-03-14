<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/cv-page.css">
    <link href="/assets/img/favicon.png" rel="icon">
    <link href="/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <title>Lukáš Bečvář</title>
</head>
<body>
    <div class="container">
        <header>
            <p class="name">Lukáš Bečvář</p>
            <p class="role">Full stack web & server solutions developer. </p>
            <div class="social-links">
                <a class="social-link" target="_blank" href=<?php echo $pageConfig->getValueByName('github') ?>><i class="bi bi-github"></i> Github</a>
                <a class="social-link" target="_blank" href=<?php echo $pageConfig->getValueByName('telegram') ?>><i class="bi bi-telegram"></i> Telegram</a>
                <a class="social-link" target="_blank" href=<?php echo $pageConfig->getValueByName('linkedin') ?>><i class="bi bi-linkedin"></i> Linkedin</a>
                <a class="social-link" target="_blank" href=mailto:<?php echo $pageConfig->getValueByName('email') ?>><i class="bi bi-envelope"></i> Email</a>
            </div>
        </header>
        
        <main>
        <div class="card">
                <p class="card-title">Personal information</p>
                <p class="personal-info-card">
                    Nationality: Czech <br>
                    Birthday: 28 May 1999 (Age: <?php echo $siteController->getAge("05/28/1999"); ?>) <br>
                    First programming language: was Pascal I started learning it in 2012. <br>
                    Main programming languages: are "currently" Java and PHP. <br>
                </p>
            </div>

            <div class="card">
                <p class="card-title">Skills</p>

                <div class="skill-card">
                    <p>HTML / CSS</p>
                    <p class="skill-level">Level: Intermediate</p>
                </div>

                <div class="skill-card">
                    <p>JS / REACT</p>
                    <p class="skill-level">Level: Basic</p>
                </div>

                <div class="skill-card">
                    <p>PHP</p>
                    <p class="skill-level">Level: Intermediate</p>
                </div>

                <div class="skill-card">
                    <p>LINUX</p>
                    <p class="skill-level">Level: Expert</p>
                </div>

                <div class="skill-card">
                    <p>JAVA</p>
                    <p class="skill-level">Level: Intermediate</p>
                </div>

                <div class="skill-card">
                    <p>PYTHON</p>
                    <p class="skill-level">Level: Beginner</p>
                </div>
            </div>
            
            <div class="card">
                <p class="card-title">Projects</p>
                <?php 
                    // get all projects
                    $projects = $projectsController->getProjectsWhereStatus("all"); 
                
                    // print all project to element
                    foreach ($projects as $data) {
                        
                        // get information form array
                        $name = $data["name"];
                        $description = $data["description"];
                        $technology = $data["technology"];
                        $github_link = $data["github_link"];
                        $started_developed_year = $data["started_developed_year"];
                        $ended_developed_year = $data["ended_developed_year"];

                        // print project card
                        echo '
                            <div class="project-card">
                                <p class="project-name">'.$name.'</p>
                                <p class="project-desc">'.$description.'</p>
                                <p class="project-link-text">project link: <a class="poject-link" href="'.$github_link.'">here</a></p>
                            </div>                        
                        ';
                    }
                ?>
            </div>   
        </main>

        <footer>
            <?php
                echo '<p>'.date("Y").'  © Lukáš Bečvář</p>';
            ?>
        </footer>
    </div>
</body>
</html>