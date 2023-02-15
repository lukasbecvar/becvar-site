<?php // mysql/database utils

    namespace becwork\utils;

    class MysqlUtils {

        /*
          * The database connection function
          * Usage like $con = mysqlConnect("dbName")
          * Input only database name (Server ip, username, password from config.php)
          * Returned mysql con usable in function, etc
        */
        public function mysqlConnect($mysqlDbName) {
            
            global $configOBJ;
            global $siteController;

            // build connection 
            $connection = mysqli_connect($configOBJ->config["ip"], $configOBJ->config["username"], $configOBJ->config["password"], $mysqlDbName);
        
            // check if connection failed
            if ($connection == false) {
                if ($configOBJ->config["dev-mode"] == false) {
                    $siteController->redirectError(400);
                }
            }

            // set mysql utf/8 charset
            mysqli_set_charset($connection, $configOBJ->config["encoding"]);

            // return connection function
            return $connection;
        }

        /*
          * The database insert sql query function (Use basedb name from config.php)
          * Usage like insertQuery("INSERT INTO `users`(`firstName`, `secondName`, `password`) VALUES ('$firstName', '$secondName', '$password')"))
          * Input sql command like string
          * Returned true or false if insers, array if select, etc
        */
        public function insertQuery($query) {
            
            global $configOBJ;
            global $siteController;

            // insert query
            $useInsertQuery = mysqli_query($this->mysqlConnect($configOBJ->config["basedb"]), $query);
            
            // check if insert
            if (!$useInsertQuery) {
                
                // print developer error
                if ($configOBJ->config["dev-mode"] == true) {
                    http_response_code(503);
                    die('[DEV-MODE]:Database error: the database server query could not be completed');		
                } 
                
                // non developer redirect error page
                else {
                    $siteController->redirectError(520);
                }
            }
        }

        /*
          * The mysql get version function
          * Usage like $ver = getMySQLVersion();
          * Returned mysql version in system
        */
        public function getMySQLVersion() {
            $output = shell_exec('mysql -V');
            preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $output, $version);
            return $version[0];
        }

        /*
         * The mysql log function (Muste instaled logs table form sql)
         * Input log name and value
       */
        public function logToMysql($name, $value) {

            global $configOBJ;
            global $mainUtils;
            global $visitorController;

            // check if logs enable
            if ($configOBJ->config["logs"] == true) {

                // check if antilog cookie set
                if (empty($_COOKIE[$configOBJ->config["antiLogCookie"]])) {

                    //Escape values
                    $name = $this->escapeString($name, true, true);
                    $value = $this->escapeString($value, true, true);

                    // get values
                    $date = date('d.m.Y H:i:s');
                    $remote_addr = $mainUtils->getRemoteAdress();
                    $status = "unreaded";
                    $browser = $visitorController->getBrowser();

                    // insert log to mysql
                    $this->insertQuery("INSERT INTO `logs`(`name`, `value`, `date`, `remote_addr`, `browser`, `status`) VALUES ('$name', '$value', '$date', '$remote_addr', '$browser', '$status')");
                }
            }
        }

        /*
         * The escape string function
         * Usage standard like $str = escapeString("string")
         * Usage protected html tasg like $str = escapeString("string", true)
         * Usage protected html special chars like $str = escapeString("string", false, true)
         * Usage complete protect string like $str = escapeString("string", true, true)
         * Returned escaped string
       */
        public function escapeString($string, $stripTags = false, $specialChars = false) {
            
            global $configOBJ;

            // escape mysql special chars
            $out = mysqli_real_escape_string($this->mysqlConnect($configOBJ->config["basedb"]), $string);
            
            // strip html tags
            if ($stripTags = true) {
                $out = strip_tags($out);
            }

            // encode html chars
            if ($specialChars = true) {
                $out = htmlspecialchars($out, ENT_QUOTES);
            }
            return $out;
        }

        /*
          * The set mysql charset to basedb from config
          * Usage like setCharset("utf8")
          * Input charset type
        */
        public function setCharset($charset) {

            global $configOBJ;

            // set charset
            mysqli_set_charset($this->mysqlConnect($configOBJ->config["basedb"]), $charset);
        }

        /*
          * The read specific value from mysql base db by query
          * Usage like $vaue = readFromMysql("SELECT name FROM users WHERE username = 'lukas'", "name");
          * Input query select string and select value
          * Return value type string or number
        */
        public function readFromMysql($query, $specifis) {
            
            global $configOBJ;

            // read query builder
            $sql = mysqli_fetch_assoc(mysqli_query($this->mysqlConnect($configOBJ->config["basedb"]), $query));
            
            // return specific value
            return $sql[$specifis];
        }

        /*
          * Check if mysql is offline
          * Usage like: $status = isOffline();
          * Return: true or false
        */
        public function isOffline() {

            global $configOBJ;

            // check if mysql is offline
            if($this->mysqlConnect($configOBJ->config["basedb"])->connect_error) {
                return true;
            } else {
                return false;
            }
        }
    }
?>
