<?php

class LastFMLibrary {
	private static $auth;





	
	
	private function __construct() {
		require 'lastfmapi' . DS . 'lastfmapi.php';
		
		// Pass the apiKey to the auth class to get a none fullAuth auth class

	}

	public static function init() {
		if (self::$auth == null) {
			self::$auth = new LastFMLibrary();
		}
		$authVars = array(
			'apiKey' => '6f3bc9859f27f75a895bb3037499dfe0',
			'secret' => '9a02a1035c20b6f147234213ca590db2',
			'user' => 'thicolares'
		);
		
		self::$auth = new lastfmApiAuth('gettoken', $authVars);
		return self::$auth;
	}
}

?>