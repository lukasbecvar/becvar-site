<?php // projects list manager

    namespace becwork\managers;

    class ProjectsManager {

        // get projects list objects where status string
        public function getProjectsWhereStatus($status): ?array {

            global $mysql;

            // default projects output value
            $projects = null;

            // check if status is all
            if ($status == "all") {

                // get all projects
                $projects = $mysql->fetch("SELECT * FROM projects");

            } else {

                // get projects where status
                $projects = $mysql->fetch("SELECT * FROM projects WHERE status='".$status."'");
            }

            // return projects object
            return $projects;
        }

        // update all github repos in database table
        public function updateProjectDatabase(): void {

            global $mysql, $config, $escapeUtils;

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
            $github_link = $config->getValue("github");

            // strip link
            $github_user = str_replace("https://github.com/", "", $github_link);
            $github_user = str_replace("/", "", $github_user);

            // get repos from api
            $repos = file_get_contents("https://api.github.com/users/$github_user/repos", false, $context);
            
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
                if ($name != $github_user) {

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
            $mysql->logToMysql("project-update", "project list updated!");
        }
    }
?>