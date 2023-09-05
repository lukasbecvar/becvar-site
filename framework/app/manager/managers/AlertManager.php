<?php // flash alert msg manager

    namespace becwork\managers;

    class AlertManager { 

        // flash success alert
        public function flashSuccess($msg): void {
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
        public function flashWarning($msg): void {

            echo '
                <center><div class="alert alert-warning" role="alert">
                
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                '.$msg.'
                </div><center>';
        }

        // flash error alert
        public function flashError($msg, $without_close = false): void {

            // check if closeble
            if ($without_close) {
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