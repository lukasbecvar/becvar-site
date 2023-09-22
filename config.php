<?php // define site config

	namespace becwork\config;

	class PageConfig {

		public $config = [

			/* main config */
			"app-name"   => "Becvar.xyz",	// define app name
			"version"    => 7.7,			// define app version
			"dev-mode"   => true,			// define devmode value
			"encoding"   => "utf8",			// define default charset

			/* page config */
			"maintenance" => "disabled", // maintenance config (Disable acces to public page)

			/* site meta values */
			"google-verify-token" => "token",
			"site-description"    => "Lukáš Bečvář AKA Lordbecvold personal website",
			"site-keywords"       => "developer, lordbecvold, php, web, website, programator, css, designer, java, coder, projects, lukas, becvar, lukasbecvar, lukas becvar, lukáš bečvář, lukáš, bečvář",

			/* cookie values */
			"anti-log-cookie" => "88d6Z97RJc6gbHn8Ch7ybZbO1Y0bVFYx",	// anti log cookie for disable loging for browser who used this cookie
			"anti-log-value"  => "3vULvNnBG96Ocm2i9Zbw6S307JkwG1bA",	// balue of anti log cookie
			"login-cookie"    => "d1dRhG2L0lVufOgtWm02kZ1Z27NUYs85",	// login cookie name
			"login-value"     => "0ZQHj24pyMSzAHDh123w4Pwj9Sl27mgJ",	// value of login cookie

			/* contact information */
			"email"     => "lukas@becvar.xyz",
			"twitter"   => "https://twitter.com/Lordbecvold",
			"github"    => "https://github.com/lordbecvold",
			"instagram" => "https://www.instagram.com/lordbecvold",
			"telegram"  => "https://t.me/lordbecvold",
			"linkedin"  => "https://www.linkedin.com/in/luk%C3%A1%C5%A1-be%C4%8Dv%C3%A1%C5%99-29900a204/",

			/* browsers limiters */
			"row-in-table-limit"      => 100,	// database browser & log reader [table row limit]
			"images-in-browser-limit" => 10,	// images in browser

			/* server variabiles */
			"service-dir" => "/services",	// define services directory

			/* geolocate config */
			"geoplugin-url" => "http://www.geoplugin.net",

			/* disabled logs */
			"logs" => true,

			/* mysql config */
			"database-host"		=> 	"localhost",	// define mysql server ip
			"database-name" 	=> 	"becvar_site",	// define mysql default db name
			"database-username"	=> 	"root",			// define mysql user 
			"database-password" => 	"root"			// define Mysql password
		];
	}
