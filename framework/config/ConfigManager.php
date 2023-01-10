<?php //The main config manager functions

	namespace becwork\config;

	class ConfigManager {

		/*
		 * Get value by name form config
		 * Input value name
		 * Return value 
		*/
		public function getValueByName($name) {

			global $configOBJ;

			//Get String form array
			$value = $configOBJ->config[$name];

			//Return value
			return $value;
		}
	}
?>