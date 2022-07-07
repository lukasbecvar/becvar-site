<?php //The main mysql utils class

    class MysqlUtils {

        
        /*
          * The database connection function
          * Usage like $con = mysqlConnect("dbName")
          * Input only database name (Server ip, username, password from config.php)
          * Returned mysql con usable in function, etc
        */
        public function mysqlConnect($mysqlDbName) {
            require_once("../config.php");

            $configOBJ = new PageConfig();

            //Try connect to database
            try {
                $connection = mysqli_connect($configOBJ->config["ip"], $configOBJ->config["username"], $configOBJ->config["password"], $mysqlDbName);
            
            } catch(Exception $e) { 
                
                //Print error
                if ($configOBJ->config["dev_mode"] == false) {
                    if ($e->getMessage() == "Connection refused") {
                        die(include_once($_SERVER['DOCUMENT_ROOT']."/../site/errors/Maintenance.php"));
                    } else {
                        die(include_once($_SERVER['DOCUMENT_ROOT']."/../site/errors/UnknownError.php"));
                    }
                }

            }

            //Set mysql utf/8 charset
            mysqli_set_charset($connection, $configOBJ->config["encoding"]);

            return $connection;
        }


        /*
          * The database insert sql query function (Use basedb name from config.php)
          * Usage like insertQuery("INSERT INTO `users`(`firstName`, `secondName`, `password`) VALUES ('$firstName', '$secondName', '$password')"))
          * Input sql command like string
          * Returned true or false if insers, array if select, etc
        */
        public function insertQuery($query) {
            require_once("../config.php");

            $configOBJ = new PageConfig();

            $useInsertQuery = mysqli_query($this->mysqlConnect($configOBJ->config["basedb"]), $query);
            if (!$useInsertQuery) {
                if ($configOBJ->config["dev_mode"] == true) {
                    if ($_SERVER['HTTP_HOST'] == "localhost") {
                        http_response_code(503);
                        die('[DEV-MODE]:Database error: the database server query could not be completed');		  
                    }
                } else {
                    die('<script type="text/javascript">window.location.replace("ErrorHandlerer.php?code=520");</script>');
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

            require_once("../config.php");

            $configOBJ = new PageConfig();

            global $mainUtils;

            if (empty($_COOKIE[$configOBJ->config["antiLogCookie"]])) {

                //Escape values
                $name = $this->escapeString($name, true, true);
                $value = $this->escapeString($value, true, true);

                $date = date('d.m.Y H:i:s');
                $remote_addr = $mainUtils->getRemoteAdress();
                $status = "unreader";

                $this->insertQuery("INSERT INTO `logs`(`name`, `value`, `date`, `remote_addr`, `status`) VALUES ('$name', '$value', '$date', '$remote_addr', '$status')");
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
        public function escapeString($string, $stripTags = false, $specialChasr = false) {
            require_once("../config.php");

            $configOBJ = new PageConfig();

            $out = mysqli_real_escape_string($this->mysqlConnect($configOBJ->config["basedb"]), $string);
            if ($stripTags) {
                $out = strip_tags($out);
            }
            if ($specialChasr) {
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
            require_once("../config.php");

            $configOBJ = new PageConfig();

            mysqli_set_charset($this->mysqlConnect($configOBJ->config["basedb"]), $charset);
        }


        /*
          * The read specific value from mysql base db by query
          * Usage like $vaue = readFromMysql("SELECT name FROM users WHERE username = 'lukas'", "name");
          * Input query select string and select value
          * Return value type string or number
        */
        public function readFromMysql($query, $specifis) {
            require_once("../config.php");

            $configOBJ = new PageConfig();

            $sql=mysqli_fetch_assoc(mysqli_query($this->mysqlConnect($configOBJ->config["basedb"]), $query));
            return $sql[$specifis];
        }


        /*
          * Check if mysql is offline
          * Usage like: $status = isOffline();
          * Return: true or false
        */
        public function isOffline() {
            require_once("../config.php");

            $configOBJ = new PageConfig();

            if($this->mysqlConnect($configOBJ->config["basedb"])->connect_error) {
                return true;
            } else {
                return false;
            }
        }
    }
?>
