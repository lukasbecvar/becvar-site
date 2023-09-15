<?php // flash alert msg manager

    namespace becwork\managers;

    class AlertManager { 

        public function flash_success($msg): void {
            include_once __DIR__."/../../../site/common/alerts/SuccessAlert.php";
        }

        public function flash_warning($msg): void {
            include_once __DIR__."/../../../site/common/alerts/WarninAlert.php";
        }

        public function flash_error($msg, $without_close = false): void {

            // check if alert can be closable
            if ($without_close) {
                include_once __DIR__."/../../../site/common/alerts/ErrorAlert.php"; 
            } else {
                include_once __DIR__."/../../../site/common/alerts/ErrorAlertClosable.php"; 
            }            
        }
    }
?>