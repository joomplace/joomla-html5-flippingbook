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
	
	include( '../php/includes/objectify.php' );
	
	/**
	 * The current version number of the content and cache manifest,
	 * must be updated every time the content of the book is
	 * changed.
	 */
	define( 'SITE_VERSION', $versionnumber);
	define( 'SITE_VERSION_SUFFIX', '?v=' . SITE_VERSION );
	
	/**
	 * An expression that matches all development hosts.
	 * When the application runs on a development host,
	 * it will use unminified JavaScript and CSS.
	 */
	define( 'DEVELOPMENT_HOSTS_EXPRESSION', "/localhost/is" );
	
	/**
	 * A list of static image assets used throughout the site,
	 * that may require replacements depending on locale.
	 * 
	 * The items in this array will be used as a foundation for
	 * the IMAGE_ASSETS constant which is defined in the
	 * individual locale configuration files.
	 */
	define( 'DEFAULT_IMAGE_ASSETS', array(
    'logo-style' => '',
		'front-cover' => '/css/images/front-cover.jpg',
		'back-cover' => '/css/images/back-cover.jpg',
		'back-cover-flipped' => '/css/images/back-cover-flipped.jpg',
		'left-page' => '/css/images/left-page.jpg',
		'left-page-flipped' => '/css/images/left-page-flipped.jpg',
		'right-page' => '/css/images/right-page.jpg'
	) );
	
	/**
	 * In JavaScript, a solid colored block is drawn behind the
	 * book to ensure the edge is anti-aliased, this is mainly
	 * visible when dragging a hard cover. This is the color 
	 * that will be used.
	 */
	define( 'DEFAULT_SOLID_BOOK_COLOR', '#5873a0' );
	
?>