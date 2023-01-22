<?php // define site config

	namespace becwork\config;

	class PageConfig {

		public $config = [

			/* Main config */
			"appName"     => "Becvold.xyz",        	// define app name
			"version"     => 6.0,                  	// define app version
			"author"      => "Lukáš Bečvář",       	// define app author
			"authorLink"  => "becvold.xyz", 	   	// define author site
			"url"         => "localhost",        	// define main app url
			"dev_mode"    => true,					// define devmode value
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

			/* Rest API config */
			"apiEnable" => true, 	// API status (?process=api or api.url.domain)
			"apiToken"  => "1234",	// token to acces api

			/* IP info api token */
			"IPinfoToken" => "000_token", // token to access https://ipinfo.io/ API

			/* Contact information */
			"email"     => "lukas@becvold.xyz",
			"discord"   => "https://discord.gg/XfAWKpHm6k",
			"twitter"   => "https://twitter.com/Lordbecvold",
			"github"    => "https://github.com/lordbecvold",
			"instagram" => "https://www.instagram.com/lordbecvold",
			"telegram"  => "https://t.me/lordbecvold",
			"youtube"   => "https://www.youtube.com/channel/UCcALaaQqdBlcR4-tGxavCvQ",

			/* Browsers limiters */
			"rowInTableLimit"      => 500,	// database browser & log reader [table row limit]
			"imagesInBrowserLimit" => 50,	// images in browser

			/* Server variabiles */
			"serviceDir" => "/services",    // define services directory

			/* Banned country */
			"bannedRussia" => true,

			/* Enabled logs */
			"logs" => true,

			/* Mysql config */
			"ip" 		=> 	"localhost",	// define mysql server ip
			"basedb" 	=> 	"becvar_site",	// define mysql default db name
			"username"	=> 	"root",			// define mysql user 
			"password" 	=> 	"root"			// define Mysql password
		];
	}
?>
