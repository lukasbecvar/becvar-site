<?php // config manager functions (get value, set value)

	namespace becwork\config; 

	class ConfigUtils {

		public function get_value($name): string {

			global $site_manager;

			// link config file
			require_once(__DIR__."./../../config.php");

			// init config instance
			$config_OBJ = new \becwork\config\PageConfig();
			
			// get config value
			$value = $config_OBJ->config[$name];

			// check if value return valid
			if ($value === null) {
				$site_manager->handle_error("error to get config value: ".$name." please check config file", 520);
			} else {
				return $value;
			}
		}	

		public function update_maintenance($value): void {
		
			global $site_manager, $mysql, $user_manager;

			// set enabled
			if ($value == "enabled") {

				// edit value in config file
				file_put_contents("../config.php", str_replace("disabled", "enabled", file_get_contents("../config.php")));
			
				$mysql->log("config-update", $user_manager->get_username()." activated maintenance mode");

			// set disabled
			} else {

				// edit value in config file
				file_put_contents("../config.php", str_replace("enabled", "disabled", file_get_contents("../config.php")));

				$mysql->log("config-update", $user_manager->get_username()." deactivated maintenance mode");
			}
		}

		public function update_dev_mode($value): void {
		
			global $site_manager, $mysql, $user_manager;

			// set true
			if ($value == true) {

				// edit value in config file
				file_put_contents("../config.php", str_replace('"dev-mode"    => false', '"dev-mode"    => true', file_get_contents("../config.php")));
			
				$mysql->log("config-update", $user_manager->get_username()." activated maintenance mode");

			// set false
			} else {

				// edit value in config file
				file_put_contents("../config.php", str_replace('"dev-mode"    => true', '"dev-mode"    => false', file_get_contents("../config.php")));

				$mysql->log("config-update", $user_manager->get_username()." deactivated maintenance mode");
			}
		}
	}
?>