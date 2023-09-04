<?php // url utils

	namespace becwork\utils;

	class UrlUtils { 

		/*
		  * The function for get actual url by protocol
		  * Usage like getActualURLComplete('https://')
		  * Input protocol
		  * Return actual page complete
		*/
		public function getActualURLComplete($protocol) {
			$out = $protocol.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
			return $out;
		}

		/*
		  * The function for get actual url by protocol
		  * Usage like getActualURL('https://')
		  * Input protocol
		  * Return actual base page
		*/
		public function getActualURL($protocol) {
			$out = $protocol.$_SERVER['HTTP_HOST'];
			return $out;
		}

		/*
		  * The function for redirect user
		  * Usage like redirect("home.php")
		  * Input page
		*/
		public function redirect($page) {
			header("location:$page");
		}

		/*
		  * The function for refrash page
		  * Usage like refrash(1, "login.php")
		*/
		public static function refrash($time, $page) {
			header("Refresh: $time; url=$page");
		}

		/*
		  * The function for redirect with java script
		  * Usage like  jsRedirect("index.php")
		*/
		public function jsRedirect($page) {
			print '<script type="text/javascript">window.location.replace("'.$page.'");</script>';
		}
	}
?>
