<?php // projects table controller

    namespace becwork\controllers;

    class ProjectsController {

        // get projects list objects where status string
        public function getProjectsWhereStatus($status) {

            global $mysqlUtils;

            // check if status is all
            if ($status == "all") {

                // get all projects
                $output = $mysqlUtils->fetch("SELECT * FROM projects");

            } else {

                // get projects where status
                $output = $mysqlUtils->fetch("SELECT * FROM projects WHERE status='".$status."'");
            }


            // return projects objct
            return $output;
        }
    }
?>