<?php // projects table controller

    namespace becwork\controllers;

    class ProjectsController {

        // get projects list objects where status string
        public function getProjectsWhereStatus($status) {

            global $mysqlUtils;
            global $pageConfig;

            // get projects where status
            $output = mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT * FROM projects WHERE status='".$status."'");

            // return projects objct
            return $output;
        }
    }
?>