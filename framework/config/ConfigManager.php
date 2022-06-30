<?php //The main config manager functions

	class ConfigManager {


		/*
		 * Get value by name form config
		 * Input value name
		 * Return value 
		*/
		public function getValueByName($name) {

			//Init config file
			require_once("../config.php");

			//Config obj Array get
			$configOBJ = new PageConfig();

			//Get String form array
			$value = $configOBJ->config[$name];

			//Return value
			return $value;
		}
	}
?>