<?php // projects table controller

    namespace becwork\controllers;

    class ProjectsController {

        // get projects list objects where status string
        public function getProjectsWhereStatus($status) {

            global $mysql;

            // check if status is all
            if ($status == "all") {

                // get all projects
                $output = $mysql->fetch("SELECT * FROM projects");

            } else {

                // get projects where status
                $output = $mysql->fetch("SELECT * FROM projects WHERE status='".$status."'");
            }

            // return projects objct
            return $output;
        }

        // update all github repos in database table
        public function updateProjectDatabase() {

            global $mysql;
            global $config;
            global $escapeUtils;

            // set requst options
            $opts = [
                'http' => [
                        'method' => 'GET',
                        'header' => [
                            'User-Agent: PHP'
                        ]
                ]
            ];
            
            // create request context
            $context = stream_context_create($opts);

            // get github username
            $githubLink = $config->getValue("github");

            // strip link
            $githubUser = str_replace("https://github.com/", "", $githubLink);
            $githubUser = str_replace("/", "", $githubUser);

            // get repos from api
            $repos = file_get_contents("https://api.github.com/users/$githubUser/repos", false, $context);
            
            // decode json object
            $repos = json_decode($repos);
            
            // delete all projects from table
            $mysql->insertQuery("DELETE FROM projects WHERE id=id");

            // reset auto increment
            $mysql->insertQuery("ALTER TABLE projects AUTO_INCREMENT = 1");

            // update projects
            foreach($repos as $repo) {

                // escape values
                $name = $escapeUtils->specialCharshStrip($repo->name);
                $description = $escapeUtils->specialCharshStrip($repo->description);
                $language = $escapeUtils->specialCharshStrip($repo->language);
                $html_url = $escapeUtils->specialCharshStrip($repo->html_url);

                // check if repo is profile readme
                if ($name != $githubUser) {

                    // check if repo archived
                    if ($repo->archived == true) {
                        $status = "closed";
                    } else {
                        $status = "open";
                    }

                    // insert project row
                    $mysql->insertQuery("INSERT INTO projects(name, description, technology, github_link, status) VALUES('$name', '$description', '$language', '$html_url', '$status')");
                }
            }

            // log action to mysql
            $mysql->logToMysql("Project-update", "project list updated!");
        }
    }
?>