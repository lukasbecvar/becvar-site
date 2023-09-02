<?php // flash alert msg 

    namespace becwork\controllers;

    class AlertController { 

        // flash success alert
        public function flashSuccess($msg) {
            echo '
                <center>
                    <div class="alert alert-success alert-dismissible fade show alert-dismissible fade show" role="alert">'.$msg.'
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div><center>
            ';
        }

        // flash warning alert
        public function flashWarning($msg) {

            echo '
                <center><div class="alert alert-warning" role="alert">
                
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                '.$msg.'
                </div><center>';
        }

        // flash error alert
        public function flashError($msg, $withoutClose = false) {

            // check if closeble
            if ($withoutClose) {
                echo 
                    '<center><div class="alert alert-danger alert-dismissible fade show" role="alert">
                        '.$msg.'
                    </div></center><br>' 
                ;  
            } else {
                echo 
                    '<center><div class="alert alert-danger alert-dismissible fade show" role="alert">
                        '.$msg.'
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div></center><br>' 
                ;  
            }            
        }
    }
?>