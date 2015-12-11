<?php 
	
	
	global $langcode;
		
	session_start();
	
	// const LANGUAGE_SESSION_NAME = 'twentythingslocale';
	// const LANGUAGE_CHANGE_QUERY = 'language';
	// const LANGUAGE_ECHO_QUERY = 'echo';

	/**
	* defining an array of Locale arrays containing values for each Locale name, Locale code, and the paths to the files containing further Locale info.
	*/
	
	// define( 'LOCALES', array(	
	// 	array(
	// 		'name' => 'English',
	// 		'code' => 'en-US',
	// 		'pages' => 'en-US/pages/',
	// 		'strings' => 'en-US/strings.php',
	// 		'configuration' => 'en-US/configuration.php'
	// 	)
		
		
	// ) );
	
	
  // Default to the first language in the list
  $locale = array (
  	"name" => "English",
  	"code" => "en-US",
  	"pages" => "locale/en-US/pages/",
  	"strings" => "locale/en-US/strings.php",
  	"configuration" => "locale/en-US/configuration.php");
  
  // GLobal locale values
  define( 'LOCALE_NAME', 'English' );
  define( 'LOCALE_CODE', 'en-US' );
  define( 'LOCALE_PAGES', 'locale/en-US/pages/');
  define( 'LOCALE_STRINGS', 'locale/en-US/strings.php' );
  define( 'LOCALE_CONFIGURATION', 'locale/en-US/configuration.php' );
  
  include( 'locale/en-US/strings.php' );
	
	/**
	 * 
	 */
	
	
?>