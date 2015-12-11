<?php 

	$versionnumber=0;
	define( 'SITE_VERSION', $versionnumber);
	define( 'SITE_VERSION_SUFFIX', '?v=' . SITE_VERSION );
	
	/**
	 * An expression that matches all development hosts.
	 * When the application runs on a development host,
	 * it will use unminified JavaScript and CSS.
	 */
	define( 'DEVELOPMENT_HOSTS_EXPRESSION', "/war/is" );
	
	/**
	 * A list of static image assets used throughout the site,
	 * that may require replacements depending on locale.
	 * 
	 * The items in this array will be used as a foundation for
	 * the IMAGE_ASSETS constant which is defined in the
	 * individual locale configuration files.
	 */
	define('logo-style','');
	define('front-cover','/css/images/front-cover.jpg');
	define('back-cover','/css/images/back-cover.jpg');
	define('back-cover-flipped','/css/images/back-cover-flipped.jpg');
	define('left-page','/css/images/left-page.jpg');
	define('left-page-flipped','/css/images/left-page-flipped.jpg');
	define('right-page','/css/images/right-page.jpg');
	
	/**
	 * In JavaScript, a solid colored block is drawn behind the
	 * book to ensure the edge is anti-aliased, this is mainly
	 * visible when dragging a hard cover. This is the color 
	 * that will be used.
	 */
	define( 'DEFAULT_SOLID_BOOK_COLOR', '#5873a0' );
	
?>