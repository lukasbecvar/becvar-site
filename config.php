<?php //Define site config

	class PageConfig {

		public $config = [

			/* Main config */
			"appName"     => "Becvold.xyz",        	//Define app name
			"version"     => 6.0,                  	//Define app version
			"author"      => "Lukáš Bečvář",       	//Define app author
			"authorLink"  => "becvold.xyz", 	   	//Define author site
			"url"         => "localhost",        	//Define main app url
			"dev_mode"    => true,					//Define devmode value
			"encoding"    => "utf8",               	//Define default charset
			"https"       => false,				   	//If this = true (Site can run only on https://)
			
			/* Page config */
			"maintenance" => "disabled", //The maintenance config (Disable acces to public page)

			/* Site meta values */
			"googleVerifyToken" => "token",
			"siteDescription"   => "Lukáš Bečvář AKA Lordbecvold personal website",
			"siteKeywords"      => "developer, lordbecvold, php, web, website, programator, css, designer, java, coder, projects, lukas, becvar, lukasbecvar, lukas becvar, lukáš bečvář, lukáš, bečvář",

			/* Cookie values */
			"antiLogCookie" => "88d6Z97RJc6gbHn8Ch7ybZbO1Y0bVFYx",	//Anti log cookie for disable loging for browser who used this cookie
			"antiLogValue"  => "3vULvNnBG96Ocm2i9Zbw6S307JkwG1bA",	//Value of anti log cookie
			"loginCookie"   => "d1dRhG2L0lVufOgtWm02kZ1Z27NUYs85",	//The login cookie name
			"loginValue"    => "0ZQHj24pyMSzAHDh123w4Pwj9Sl27mgJ",	//Value of login cookie

			/* Rest API config */
			"apiEnable" => false, 	//Api status (?process=api or api.url.domain)
			"apiToken"  => "1234",	//Token to acces api

			/* IP info api token */
			"IPinfoToken" => "000_token", //Token to access https://ipinfo.io/ API

			/* Contact information */
			"email"     => "lukas@becvold.xyz",
			"discord"   => "https://discord.gg/XfAWKpHm6k",
			"twitter"   => "https://twitter.com/Lordbecvold",
			"github"    => "https://github.com/lordbecvold",
			"instagram" => "https://www.instagram.com/lordbecvold",
			"telegram"  => "https://t.me/lordbecvold",
			"youtube"   => "https://www.youtube.com/channel/UCcALaaQqdBlcR4-tGxavCvQ",

			/* Browsers limiters */
			"rowInTableLimit"      => 500,	//Database browser & log reader [table row limit]
			"imagesInBrowserLimit" => 50,	//Images in browser

			/* Server variabiles */
			"serviceDir" => "/services",    //Define services directory

			/* Mysql config */
			"ip" 		=> 	"localhost",	//Define mysql server ip
			"basedb" 	=> 	"becvar_site",	//Define mysql default db name
			"username"	=> 	"root",			//Define mysql user 
			"password" 	=> 	"root"			//Define Mysql password
		];
	}
?>
