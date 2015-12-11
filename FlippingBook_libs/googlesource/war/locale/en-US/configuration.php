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
	
	/**
	 * Include the basic configuration values which are shared
	 * between all locales.
	 */
	include_once( '../locale-base-configuration.php' );
	
	/**
	 * In JavaScript, a solid colored block is drawn behind the
	 * book to ensure the edge is anti-aliased, this is mainly
	 * visible when dragging a hard cover. This is the color that
	 * will be used for that solid shape, so it needs to match
	 * the color of the book texture.
	 */
	define( 'SOLID_BOOK_COLOR', DEFAULT_SOLID_BOOK_COLOR );
	
	/** 
	 * Defines the content structure of the book using:
	 * 
	 * stub: 			The unique identifier of a chapter. This is used for,
	 * 					amongs other things, the URL structure of the book.
	 * numberOfPages:	Total number of pages that this chapter holds.
	 * title:			Main title of the chapter.
	 * subtitle:		Secondary title of the chapter.
	 * hidden:			Flags if an item should be hidden from the navigational
	 * 					elements in the UI.
	 * active:			Flags if this chapter is enabled in the application,
	 * 					disabled chapters are visible in the UI but can not
	 * 					be accessed (for a rolling release schedule).
	 * order:			Determines the order in which chapters appear.
	 */
	 
	$ALL_CHAPTERS = array(
		array(
			'stub' => 'foreword',
			'numberOfPages' => 3,
			'title' => 'Foreword to 20 Things',
			'subtitle' => '',
			'templates' => array(
				'template-start-7',
				'template-inner-6',
				'template-inner-7'
			),
			'active' => '1',	
			'hidden' => '1',						
			'order' => 0
		),
		array(
			'stub' => 'what-is-the-internet',
			'numberOfPages' => 3,
			'title' => 'What is the Internet?',
			'subtitle' => 'or, "You Say Tomato, I Say TCP/IP"',
			'active' => '1',
			'templates' => array(
				'template-start-7',
				'template-inner-5',
				'template-inner-2'
			),
			'order' => 1
		),
		array(
			'stub' => 'cloud-computing',
			'numberOfPages' => 2,
			'title' => 'Cloud Computing',
			'subtitle' => 'or, why it\'s ok for a truck to crush your laptop',
			'active' => '1',
			'templates' => array(
				'template-start-5',
				'template-inner-5'
			),
			'order' => 2
		),
		array(
			'stub' => 'web-apps',
			'numberOfPages' => 3,
			'title' => 'Web Apps',
			'subtitle' => 'or, "Life, Liberty and the Pursuit of Appiness"',
			'active' => '1',
			'templates' => array(
				'template-start-5',
				'template-inner-2',
				'template-inner-2'
			),
			'order' => 3
		),
		array(
			'stub' => 'html',
			'numberOfPages' => 3,
			'title' => 'HTML, JavaScript, CSS and more',
			'subtitle' => 'or, this is not your mom\'s AJAX',
			'active' => '1',
			'templates' => array(
				'template-start-2',
				'template-inner-4',
				'template-inner-4'
			),
			'order' => 4
		),
		array(
			'stub' => 'html5',
			'numberOfPages' => 2,
			'title' => 'HTML5',
			'subtitle' => 'or, in the beginning there was no &lt;video&gt;',
			'active' => '1',
			'templates' => array(
				'template-start-2',
				'template-inner-2'
			),
			'order' => 5
		),
		array(
			'stub' => 'threed',
			'numberOfPages' => 3,
			'title' => '3D in the Browser',
			'subtitle' => 'or, browsing with more depth',
			'active' => '1',
			'templates' => array(
				'template-start-7',
				'template-inner-6',
				'template-inner-7'
			),
			'order' => 6
		),
		array(
			'stub' => 'old-vs-new-browsers',
			'numberOfPages' => 4,
			'title' => 'A Browser Madrigal',
			'subtitle' => 'or, old vs. modern browsers',
			'active' => '1',
			'templates' => array(
				'template-start-4',
				'template-inner-6',
				'template-inner-3',
				'template-inner-2'
			),
			'order' => 7
		),
		array(
			'stub' => 'plugins',
			'numberOfPages' => 2,
			'title' => 'Plug-ins',
			'subtitle' => 'or, pepperoni for your cheese pizza',
			'active' => '1',
			'templates' => array(
				'template-start-7',
				'template-inner-2'
			),
			'order' => 8
		),
		array(
			'stub' => 'browser-extensions',
			'numberOfPages' => 3,
			'title' => 'Browser Extensions ',
			'subtitle' => 'or, superpowers for your browser',
			'active' => '1',
			'templates' => array(
				'template-start-5',
				'template-inner-5',
				'template-inner-2'
			),
			'order' => 9
		),
		array(
			'stub' => 'sync',
			'numberOfPages' => 2,
			'title' => 'Synchronizing the Browser',
			'subtitle' => 'or, why it\'s ok for a truck to crush your laptop, part II',
			'active' => '1',
			'templates' => array(
				'template-start-7',
				'template-inner-2'
			),
			'order' => 10
		),
		array(
			'stub' => 'browser-cookies',
			'numberOfPages' => 2,
			'title' => 'Browser Cookies',
			'subtitle' => 'or, thanks for the memories',
			'active' => '1',
			'templates' => array(
				'template-start-5',
				'template-inner-2'
			),
			'order' => 11
		),
		array(
			'stub' => 'browser-privacy',
			'numberOfPages' => 4,
			'title' => 'Browsers and Privacy',
			'subtitle' => 'or, giving you choices to protect your privacy in the browser',
			'active' => '1',
			'templates' => array(
				'template-start-7',
				'template-inner-6',
				'template-inner-2',
				'template-inner-7'
			),
			'order' => 12
		),
		array(
			'stub' => 'malware',
			'numberOfPages' => 3,
			'title' => 'Malware, Phishing, and Security Risks',
			'subtitle' => 'or, if it quacks like a duck but isn\'t a duck',
			'active' => '1',
			'templates' => array(
				'template-start-2',
				'template-inner-5',
				'template-inner-2'
			),
			'order' => 13
		),
		array(
			'stub' => 'browser-protection',
			'numberOfPages' => 3,
			'title' => 'How Modern Browsers Help Protect You From Malware and Phishing',
			'subtitle' => 'or, beware the ne\'er-do-wells!',
			'active' => '1',
			'templates' => array(
				'template-start-2',
				'template-inner-6',
				'template-inner-2'
			),
			'order' => 14
		),
		array(
			'stub' => 'url',
			'numberOfPages' => 5,
			'title' => 'Using Web Addresses to Stay Safe',
			'subtitle' => 'or, \'my name is URL\'',
			'active' => '1',
			'templates' => array(
				'template-start-7',
				'template-inner-2',
				'template-inner-2',
				'template-inner-6',
				'template-inner-2'
			),
			'order' => 15
		),
		array(
			'stub' => 'dns',
			'numberOfPages' => 3,
			'title' => 'IP Addresses and DNS',
			'subtitle' => 'or, the phantom phone booth',
			'active' => '1',
			'templates' => array(
				'template-start-7',
				'template-inner-5',
				'template-inner-2'
			),
			'order' => 16
		),
		array(
			'stub' => 'identity',
			'numberOfPages' => 3,
			'title' => 'Validating Identities Online',
			'subtitle' => 'or, "Dr. Livingstone, I presume?"',
			'active' => '1',
			'templates' => array(
				'template-start-5',
				'template-inner-5',
				'template-inner-7'
			),
			'order' => 17
		),
		array(
			'stub' => 'page-load',
			'numberOfPages' => 4,
			'title' => 'Evolving to a Faster Web',
			'subtitle' => 'or, speeding up images, video, and JavaScript on the web',
			'active' => '1',
			'templates' => array(
				'template-start-7',
				'template-inner-6',
				'template-inner-2',
				'template-inner-2'
			),
			'order' => 18
		),
		array(
			'stub' => 'open-source',
			'numberOfPages' => 2,
			'title' => 'Open Source and Browsers',
			'subtitle' => 'or, standing on the shoulders of giants',
			'active' => '1',
			'templates' => array(
				'template-start-7',
				'template-inner-2'
			),
			'order' => 19
		),
		array(
			'stub' => 'conclusion',
			'numberOfPages' => 2,
			'title' => '19 Things Later...',
			'subtitle' => 'or, a day in the clouds',
			'active' => '1',
			'templates' => array(
				'template-start-5',
				'template-inner-2'
			),
			'order' => 20
		),
		array(
			'stub' => 'theend',
			'numberOfPages' => 1,
			'title' => '',
			'subtitle' => '',
			'active' => '1',
			'hidden' => '1',
			'templates' => array(
				'template-3',
			),
			'order' => 21
		)
	);
	
	/**
	 * A list of static image assets used throughout the site
	 * that may need to be localized.
	 * 
	 * This extends the DEFAULT_IMAGE_ASSETS and everything in
	 * this array will also be added to the OFFLINE_ASSETS
	 * array.
	 */
	define( 'IMAGE_ASSETS', array_merge( DEFAULT_IMAGE_ASSETS, array( /* Override Assets Here */ ) ) );
	
	/**
	 * A listing of all assets (static files and URL's) that
	 * will be cached for offline use.
	 */
	define( 'OFFLINE_ASSETS', array_merge( array(
		'/', 
		'/home', 
		'/all',
		
		'js/twentythings.min.js',
		
		'css/twentythings.min.css',
		'css/hideOnLoad.css',
		'css/basic.css',
		'css/print.css',
		
		'css/fonts/droidsans-webfont.woff',
		'css/fonts/droidsans-bold-webfont.woff',
		'css/fonts/droidserif-webfont.woff',
		'css/fonts/droidserif-bold-webfont.woff',
		'css/fonts/droidserif-italic-webfont.woff',
		'css/fonts/droidserif-bolditalic-webfont.woff',
		
		'css/images/sprites.png',
		'css/images/overlay-pattern.png',
		'css/images/grey-mask.png',
		'css/images/illustrations.png',
		'css/images/right-page-paper.jpg',
		'css/images/repeat-x.png',
		
		'media/illustrations/3d01.png',
		'media/illustrations/3d01_clouds.png',
		'media/illustrations/browserextensions.png',
		'media/illustrations/browserprivacy01.png',
		'media/illustrations/browserprivacy02.png',
		'media/illustrations/browserprotection01.png',
		'media/illustrations/browserprotection02.png',
		'media/illustrations/cloud01.png',
		'media/illustrations/cloud02.png',
		'media/illustrations/cloud03.png',
		'media/illustrations/conclusion01.png',
		'media/illustrations/cookies01.png',
		'media/illustrations/dns01.png',
		'media/illustrations/html01.png',
		'media/illustrations/html02.png',
		'media/illustrations/html03.png',
		'media/illustrations/html04.png',
		'media/illustrations/identity01.png',
		'media/illustrations/identity02.png',
		'media/illustrations/internet01.png',
		'media/illustrations/internet01-part1.png',
		'media/illustrations/internet01-part2.png',
		'media/illustrations/malware01.png',
		'media/illustrations/oldvsnewbrowsers01.png',
		'media/illustrations/oldvsnewbrowsers02.png',
		'media/illustrations/oldvsnewbrowsers03.png',
		'media/illustrations/opensource01.png',
		'media/illustrations/opensource01-part1.png',
		'media/illustrations/opensource01-part2.png',
		'media/illustrations/pageload01.png',
		'media/illustrations/plugins.png',
		'media/illustrations/webapps01.png',
		'media/illustrations/webapps02.png',
		'media/illustrations/url_b1.png',
		'media/illustrations/url_b2.png',
		'media/illustrations/url_b3.png',
		'media/illustrations/url_b4.png',
		'media/illustrations/url_b5.png',
		'media/illustrations/url01.png',
		'media/illustrations/url02.png'
	), IMAGE_ASSETS ) );
?>