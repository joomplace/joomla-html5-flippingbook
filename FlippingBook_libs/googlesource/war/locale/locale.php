<?php 
	
/**
 * Copyright 2011 Google Inc.
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */ 
	
	// Include the flexible cross origin policy for this request
	include_once( '../php/includes/cors.php' );
	include( '../php/includes/objectify.php' );
	
	global $langcode;
		
	session_start();
	
	const LANGUAGE_SESSION_NAME = 'twentythingslocale';
	const LANGUAGE_CHANGE_QUERY = 'language';
	const LANGUAGE_ECHO_QUERY = 'echo';

	/**
	* defining an array of Locale arrays containing values for each Locale name, Locale code, and the paths to the files containing further Locale info.
	*/
	
	define( 'LOCALES', array(	
		array(
			'name' => 'English',
			'code' => 'en-US',
			'pages' => 'en-US/pages/',
			'strings' => 'en-US/strings.php',
			'configuration' => 'en-US/configuration.php'
		)
		
		
	) );
	
	
  // Default to the first language in the list
  $locale = getLocaleByLanguageCode( $langcode );
  
  // GLobal locale values
  define( 'LOCALE_NAME', $locale['name'] );
  define( 'LOCALE_CODE', $locale['code'] );
  define( 'LOCALE_PAGES', $locale['pages'] );
  define( 'LOCALE_STRINGS', $locale['strings'] );
  define( 'LOCALE_CONFIGURATION', $locale['configuration'] );
  
  include( LOCALE_STRINGS );
	
	/**
	 * 
	 */
	function getLocaleByLanguageCode( $code ) {
		foreach( LOCALES as $key => $value ) {
			if( strtolower( $value['code'] ) === strtolower( $code ) ) {
				return $value;
			}
		}
		
		return NULL;
	}
	
?>