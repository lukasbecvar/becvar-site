<?php // config manager functions

	namespace becwork\config;

	class ConfigManager {

		/*
		 * Get value by name form config
		 * Input value name
		 * Return value 
		*/
		public function getValueByName($name) {

			global $configOBJ;

			// return config value
			return $configOBJ->config[$name];
		}
	}
?>