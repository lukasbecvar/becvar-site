<?php //Projects table controller

    class ProjectsController {

        //function for get projects list objects where status string
        public function getProjectsWhereStatus($status) {

            global $mysqlUtils;
            global $pageConfig;

            $output = mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT * FROM projects WHERE status='".$status."'");

            return $output;
        }
    }
?>