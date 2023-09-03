<?php // define site config

	namespace becwork\config;

	class PageConfig {

		public $config = [

			/* Main config */
			"appName"     => "Becvar.xyz",        	// define app name
			"version"     => 7.2,                  	// define app version
			"author"      => "Lukáš Bečvář",       	// define app author
			"authorLink"  => "becvar.xyz", 	   		// define author site
			"url-check"   => true,				    // check if url valid
			"url"         => "localhost",        	// define main app url
			"dev-mode"    => true,					// define devmode value
			"encoding"    => "utf8",               	// define default charset
			"https"       => false,				   	// if this = true (Site can run only on https://)
			
			/* Page config */
			"maintenance" => "disabled", // maintenance config (Disable acces to public page)

			/* Site meta values */
			"googleVerifyToken" => "token",
			"siteDescription"   => "Lukáš Bečvář AKA Lordbecvold personal website",
			"siteKeywords"      => "developer, lordbecvold, php, web, website, programator, css, designer, java, coder, projects, lukas, becvar, lukasbecvar, lukas becvar, lukáš bečvář, lukáš, bečvář",

			/* Cookie values */
			"antiLogCookie" => "88d6Z97RJc6gbHn8Ch7ybZbO1Y0bVFYx",	// anti log cookie for disable loging for browser who used this cookie
			"antiLogValue"  => "3vULvNnBG96Ocm2i9Zbw6S307JkwG1bA",	// balue of anti log cookie
			"loginCookie"   => "d1dRhG2L0lVufOgtWm02kZ1Z27NUYs85",	// login cookie name
			"loginValue"    => "0ZQHj24pyMSzAHDh123w4Pwj9Sl27mgJ",	// value of login cookie

			/* Contact information */
			"email"     => "lukas@becvar.xyz",
			"twitter"   => "https://twitter.com/Lordbecvold",
			"github"    => "https://github.com/lordbecvold",
			"instagram" => "https://www.instagram.com/lordbecvold",
			"telegram"  => "https://t.me/lordbecvold",
			"linkedin"  => "https://www.linkedin.com/in/luk%C3%A1%C5%A1-be%C4%8Dv%C3%A1%C5%99-29900a204/",

			/* Browsers limiters */
			"rowInTableLimit"      => 100,	// database browser & log reader [table row limit]
			"imagesInBrowserLimit" => 10,	// images in browser

			/* Server variabiles */
			"serviceDir" => "/services",    // define services directory

			/* Geolocate config */
			"geoplugin_url" => "http://www.geoplugin.net",

			/* Enabled logs */
			"logs" => true,

			/* Mysql config */
			"mysql-address"		=> 	"localhost",	// define mysql server ip
			"mysql-database" 	=> 	"becvar_site",	// define mysql default db name
			"mysql-username"	=> 	"root",			// define mysql user 
			"mysql-password" 	=> 	"root"			// define Mysql password
		];
	}
?>
