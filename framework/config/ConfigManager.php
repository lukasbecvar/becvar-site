<?php // config manager functions (get value, set value)

	namespace becwork\config; 

	class ConfigManager {

		// get config value by name
		public function getValue($name) {

			// link config file
			require_once(__DIR__."./../../config.php");

			// default value
			$value = null;

			// init config object
			$config = new \becwork\config\PageConfig();

			// get config value
			$value = $config->config[$name];

			return $value;
		}

		// update maintenance config value
		public function updateMaintenanceValue($value) {
		
			global $siteManager, $mysql, $userManager;

			// set enabled
			if ($value == "enabled") {

				// edit value in config file
				file_put_contents("../config.php", str_replace("disabled", "enabled", file_get_contents("../config.php")));
			
				// log to mysql
				$mysql->logToMysql("config-update", $userManager->getCurrentUsername()." activated maintenance mode");

			// set disabled
			} else {

				// edit value in config file
				file_put_contents("../config.php", str_replace("enabled", "disabled", file_get_contents("../config.php")));

				// log to mysql
				$mysql->logToMysql("config-update", $userManager->getCurrentUsername()." deactivated maintenance mode");
			}
		}

		// update dev-mode config value
		public function updateDevModeValue($value) {
		
			global $siteManager, $mysql, $userManager;

			// set true
			if ($value == true) {

				// edit value in config file
				file_put_contents("../config.php", str_replace('"dev-mode"    => false', '"dev-mode"    => true', file_get_contents("../config.php")));
			
				// log to mysql
				$mysql->logToMysql("config-update", $userManager->getCurrentUsername()." activated maintenance mode");

			// set false
			} else {

				// edit value in config file
				file_put_contents("../config.php", str_replace('"dev-mode"    => true', '"dev-mode"    => false', file_get_contents("../config.php")));

				// log to mysql
				$mysql->logToMysql("config-update", $userManager->getCurrentUsername()." deactivated maintenance mode");
			}
		}
	}
?>