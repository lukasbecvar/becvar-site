<?php // mysql utils (PDO functions: connect, read, insert, etc...)
    namespace becwork\utils;

    class MysqlUtils {

        private $db_host;
        private $db_name;
        private $db_username;
        private $db_password;

        // init constructor
        public function __construct($db_ip, $database_name, $username, $password) {
            $this->db_host = $db_ip;
            $this->db_name = $database_name;
            $this->db_username = $username;
            $this->db_password = $password;
        }

        /* 
          * FUNCTION: database connection (use PDO)
          * RETURN: database connection
        */
        public function connect() {
            
            global $config, $siteManager;

            // get mysql connection data form app config
            $address  = $this->db_host;
            $database = $this->db_name;
            $username = $this->db_username;
            $password = $this->db_password;

            // get default database charset
            $encoding = $config->getValue("encoding");
            
            // try connect to database
            try {

                // build connction string
                $conn = new \PDO("mysql:host=$address;dbname=$database;charset=$encoding", $username, $password);
               
                // set the PDO error mode to exception
                $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                   
            // catch connection error
            } catch(\PDOException $e) {
                
                // handle error
                $siteManager->handleError('Database connection error: '.$e->getMessage(), 400);
            }

            // return database connection object
            return $conn;
        }

        /*
          * FUNCTION:  database insert sql query function (Use database name from config.php)
          * USAGE: like insertQuery("INSERT INTO logs(name, value, date, remote_addr) VALUES('log name', 'log value', 'log date', 'log remote_addr')")
          * INPUT: sql command like string
        */
        public function insertQuery($query) {

            global $config, $siteManager;

            // get PDO connection
            $connection = $this->connect();

            // use prepare statement for query
            $statement = $connection->prepare($query);

            try {
                
                // execute prepered query
                $statement->execute();

            // catch insert error
            } catch(\PDOException $e) {

                // handle error
                $siteManager->handleError('SQL query insert error: '.$e->getMessage(), 400);
            }
        }

        /*
         * FUNCTION: mysql log function (Muste instaled logs table form sql)
         * INPUT: log name and value
        */
        public function logToMysql($name, $value) {

            global $config, $escapeUtils, $mainUtils, $visitorManager;

            // check if logs enable
            if ($config->getValue("logs") == true) {

                // check if antilog cookie set
                if (empty($_COOKIE[$config->getValue("anti-log-cookie")])) {

                    // check if name is null
                    if (empty($name)) {
                        $name = null;
                    }

                    // check if value is null
                    if (empty($value)) {
                        $value = null;
                    }

                    // get data & escape
                    $name = $escapeUtils->specialCharshStrip($name);
                    $value = $escapeUtils->specialCharshStrip($value);

                    // get values
                    $date = date('d.m.Y H:i:s');
                    $remote_addr = $mainUtils->getRemoteAdress();
                    $status = "unreaded";
                    $browser = $visitorManager->getBrowser();

                    // insert log to mysql
                    $this->insertQuery("INSERT INTO logs(name, value, date, remote_addr, browser, status) VALUES('$name', '$value', '$date', '$remote_addr', '$browser', '$status')");
                }
            }
        }

        /*
          * FUNCTION: mysql data query fetch
          * INPUT: query like "SELECT * FROM logs"
          * RETURN: database output
        */
        public function fetch($query) {

            global $config;

            // get database connection
            $connection = $this->connect();

            try {
                
                // use prepare statement for query
                $statement = $connection->prepare($query);

                // execute query
                $statement->execute();
                
                // fetch data
                $data = $statement->fetchAll();

                // return data
                return $data;

            // catch fetch error
            } catch(\PDOException $e) {

                // handle error
                $siteManager->handleError('SQL fetch error: '.$e->getMessage(), 400);
            }
        }

        /*
          * FUNCTION: fetch single value form database
          * INPUT: sql query & specific value
          * RETURN: selected value
        */
        public function fetchValue($query, $value) {

            global $config, $siteManager;

            // get database connection
            $connection = $this->connect();

            try {
                
                // use prepare statement for query
                $statement = $connection->prepare($query);

                // execute query
                $statement->execute();

                // fetch data query
                $fetch = $statement->fetchAll();

                // check if select exist
                if (array_key_exists(0, $fetch)) {
                    
                    // check if selected value exist in array
                    if (array_key_exists($value, $fetch[0])) {

                        // get value from retrun
                        $valueOutput = $fetch[0][$value];
                    
                    } else {
                    
                        // handle error
                        $siteManager->handleError("Database select error: '$value' not exist in selected data", 404);
                    }

                } else {

                    // handle error
                    $siteManager->handleError("Database select error: please check if query valid, query:'$query'", 404);
                }

                // return value
                return $valueOutput;

            // catch fetch error
            } catch(\PDOException $e) {

                // handle error
                $siteManager->handleError('SQL fetch error: '.$e->getMessage(), 400);
            }
        }

        /*
          * FUNCTION: get version function
          * USAGE: $ver = getMySQLVersion();
          * RETURN: mysql version in system
        */
        public function getMySQLVersion() {
            $output = shell_exec('mysql -V');
            preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $output, $version);
            return $version[0];
        }
    }
?>
