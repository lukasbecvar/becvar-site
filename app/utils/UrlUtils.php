<?php // url utils

	namespace becwork\utils;

	class UrlUtils { 

		/*
		  * The function for get actual url by protocol
		  * Usage like get_complete('https://')
		  * Input protocol
		  * Return actual page complete
		*/
		public function get_complete($protocol): ?string {
			$out = $protocol.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
			return $out;
		}

		/*
		  * The function for get actual url by protocol
		  * Usage like get_url('https://')
		  * Input protocol
		  * Return actual base page
		*/
		public function get_url($protocol): ?string {
			$out = $protocol.$_SERVER['HTTP_HOST'];
			return $out;
		}

		/*
		  * The function for redirect user
		  * Usage like redirect("home.php")
		  * Input page
		*/
		public function redirect($page): void {
			header("location:$page");
		}

		/*
		  * The function for refrash page
		  * Usage like refrash(1, "login.php")
		*/
		public static function refrash($time, $page): void {
			header("Refresh: $time; url=$page");
		}

		/*
		  * The function for redirect with java script
		  * Usage like  js_redirect("index.php")
		*/
		public function js_redirect($page): void {
			print '<script type="text/javascript">window.location.replace("'.$page.'");</script>';
		}
	}
?>