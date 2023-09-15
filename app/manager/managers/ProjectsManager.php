<?php // projects list manager

    namespace becwork\managers;

    class ProjectsManager {

        // get projects list objects where status string
        public function get_projects_list($status): ?array {

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
        public function update_project_list(): void {

            global $mysql, $config, $escape_utils, $json_utils;

            // get github username
            $github_link = $config->get_value("github");

            // strip link
            $github_user = str_replace("https://github.com/", "", $github_link);
            $github_user = str_replace("/", "", $github_user);

            // get repos form github
            $repos = $json_utils->get_json_from_url("https://api.github.com/users/$github_user/repos");

            // delete all projects from table
            $mysql->insert("DELETE FROM projects WHERE id=id");

            // reset auto increment
            $mysql->insert("ALTER TABLE projects AUTO_INCREMENT = 1");

            // update projects
            foreach($repos as $repo) {

                // escape values
                $name = $escape_utils->special_chars_strip($repo["name"]);
                $description = $escape_utils->special_chars_strip($repo["description"]);
                $language = $escape_utils->special_chars_strip($repo["language"]);
                $html_url = $escape_utils->special_chars_strip($repo["html_url"]);

                // check if repo is profile readme
                if ($name != $github_user) {

                    // check if repo archived
                    if ($repo["archived"] == true) {
                        $status = "closed";
                    } else {
                        $status = "open";
                    }

                    // insert project row
                    $mysql->insert("INSERT INTO projects(name, description, technology, github_link, status) VALUES('$name', '$description', '$language', '$html_url', '$status')");
                }
            }

            $mysql->log("project-update", "project list updated!");
        }
    }
?>