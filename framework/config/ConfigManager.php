<?php // config manager functions

	namespace becwork\config; 

	class ConfigManager {

		/*
		 * FUNCTION: value by name form config
		 * INPUT: value name
		 * RETURN: value 
		*/
		public function getValue($name) {

			require_once(__DIR__."./../../config.php");

			$config = new \becwork\config\PageConfig();

			return $config->config[$name];
		}
	}
?>