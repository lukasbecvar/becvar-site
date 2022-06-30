<?php //Controller for flash alert msg 

    class AlertController { 

        //Flash alert
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

        //Flash warning alert
        public function flashWarning($msg) {
            echo '
                <center><div class="alert alert-warning" role="alert">
                
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                '.$msg.'
                </div><center>';
        }

        //Flash error alert
        public function flashError($msg, $withoutClose = false) {

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