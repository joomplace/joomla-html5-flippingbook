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
 
import com.fi.twentythings.Article;
import com.fi.twentythings.Locale;
import com.fi.twentythings.Page;
import com.googlecode.objectify.Objectify;
import com.googlecode.objectify.ObjectifyService;

require('../../locale/locale.php');

require_once('../includes/auth.php');

if(!isset($_REQUEST['locale'])) {
	echo 'Must set locale.';
	return;
}
	
$localeclass = java_class('com.fi.twentythings.Locale');
$articleclass = java_class('com.fi.twentythings.Article');	
$pageclass = java_class('com.fi.twentythings.Page');

ObjectifyService::register($localeclass);
ObjectifyService::register($articleclass);
ObjectifyService::register($pageclass);
	
$obj_service = new Java('com.googlecode.objectify.ObjectifyService');
$ofy = $obj_service->begin();

$row = $_REQUEST['locale'];	
 	
require('../../locale/'.LOCALES[$row]['strings']);

$locale = new Locale();
$locale->setId(LOCALES[$row]['code']);
$locale->setLOCALE_DISPLAY_NAME(LOCALE_META_TEXT['LOCALE_DISPLAY_NAME']);		
$locale->setLOCALE_META_DESCRIPTION(LOCALE_META_TEXT['LOCALE_META_DESCRIPTION']);	
$locale->setLOCALE_META_KEYWORDS(LOCALE_META_TEXT['LOCALE_META_KEYWORDS']);
$locale->setLOCALE_META_AUTHOR(LOCALE_META_TEXT['LOCALE_META_AUTHOR']);
$locale->setLOCALE_FACEBOOK_MESSAGE(LOCALE_META_TEXT['LOCALE_FACEBOOK_MESSAGE']);
$locale->setLOCALE_FACEBOOK_MESSAGE_SINGLE(LOCALE_META_TEXT['LOCALE_FACEBOOK_MESSAGE_SINGLE']);
$locale->setLOCALE_TWITTER_MESSAGE(LOCALE_META_TEXT['LOCALE_TWITTER_MESSAGE']);
$locale->setLOCALE_TWITTER_MESSAGE_SINGLE(LOCALE_META_TEXT['LOCALE_TWITTER_MESSAGE_SINGLE']);
$locale->setLOCALE_BUZZ_MESSAGE(LOCALE_META_TEXT['LOCALE_BUZZ_MESSAGE']);
$locale->setLOCALE_BUZZ_MESSAGE_SINGLE(LOCALE_META_TEXT['LOCALE_BUZZ_MESSAGE_SINGLE']);
$locale->setLOCALE_SHARING_IMAGE(LOCALE_META_TEXT['LOCALE_SHARING_IMAGE']);
$locale->setLOCALE_TITLE(LOCALE_META_TEXT['LOCALE_TITLE']);
$locale->setLOCALE_NOT_SUPPORTED(LOCALE_META_TEXT['LOCALE_NOT_SUPPORTED']);
$locale->setLOCALE_NOT_SUPPORTED_IE(LOCALE_META_TEXT['LOCALE_NOT_SUPPORTED_IE']);
$locale->setLOCALE_MENU_TOT(LOCALE_META_TEXT['LOCALE_MENU_TOT']);
$locale->setLOCALE_MENU_FORWARD(LOCALE_META_TEXT['LOCALE_MENU_FOREWORD']);
$locale->setLOCALE_MENU_CREDITS(LOCALE_META_TEXT['LOCALE_MENU_CREDITS']);
$locale->setLOCALE_SEARCH_PLACEHOLDER(LOCALE_META_TEXT['LOCALE_SEARCH_PLACEHOLDER']);
$locale->setLOCALE_SEARCH_INVALID(LOCALE_META_TEXT['LOCALE_SEARCH_INVALID']);
$locale->setLOCALE_SEARCH_RESULTS_CHAPTERS(LOCALE_META_TEXT['LOCALE_SEARCH_RESULTS_CHAPTERS']);
$locale->setLOCALE_SEARCH_RESULTS_PAGES(LOCALE_META_TEXT['LOCALE_SEARCH_RESULTS_PAGES']);
$locale->setLOCALE_FRONT_COVER_INTRO(LOCALE_META_TEXT['LOCALE_FRONT_COVER_INTRO']);
$locale->setLOCALE_FRONT_COVER_CTA(LOCALE_META_TEXT['LOCALE_FRONT_COVER_CTA']);
$locale->setLOCALE_SHARER_LABEL1(LOCALE_META_TEXT['LOCALE_SHARER_LABEL1']);
$locale->setLOCALE_SHARER_LABEL2(LOCALE_META_TEXT['LOCALE_SHARER_LABEL2']);
$locale->setLOCALE_TOC_TITLE(LOCALE_META_TEXT['LOCALE_TOC_TITLE']);
$locale->setLOCALE_TOC_BACK(LOCALE_META_TEXT['LOCALE_TOC_BACK']);
$locale->setLOCALE_OVERLAY_CLOSE(LOCALE_META_TEXT['LOCALE_OVERLAY_CLOSE']);
$locale->setLOCALE_OVERLAY_PRINT_TITLE(LOCALE_META_TEXT['LOCALE_OVERLAY_PRINT_TITLE']);
$locale->setLOCALE_OVERLAY_PRINT_DESCRIPTION(LOCALE_META_TEXT['LOCALE_OVERLAY_PRINT_DESCRIPTION']);
$locale->setLOCALE_OVERLAY_PDF_TITLE(LOCALE_META_TEXT['LOCALE_OVERLAY_PDF_TITLE']);
$locale->setLOCALE_OVERLAY_PDF_DESCRIPTION(LOCALE_META_TEXT['LOCALE_OVERLAY_PDF_DESCRIPTION']);
$locale->setLOCALE_OVERLAY_RESUME_TITLE(LOCALE_META_TEXT['LOCALE_OVERLAY_RESUME_TITLE']);
$locale->setLOCALE_OVERLAY_RESUME_DESCRIPTION(LOCALE_META_TEXT['LOCALE_OVERLAY_RESUME_DESCRIPTION']);
$locale->setLOCALE_OVERLAY_RESUME_YES(LOCALE_META_TEXT['LOCALE_OVERLAY_RESUME_YES']);
$locale->setLOCALE_OVERLAY_RESUME_NO(LOCALE_META_TEXT['LOCALE_OVERLAY_RESUME_NO']);
$locale->setLOCALE_FOOTER_CURATOR(LOCALE_META_TEXT['LOCALE_FOOTER_CURATOR']);
$locale->setLOCALE_FOOTER_PRINT(LOCALE_META_TEXT['LOCALE_FOOTER_PRINT']);
$locale->setLOCALE_FOOTER_SHARE(LOCALE_META_TEXT['LOCALE_FOOTER_SHARE']);
$locale->setLOCALE_FOOTER_LIGHTS(LOCALE_META_TEXT['LOCALE_FOOTER_LIGHTS']);
$locale->setLOCALE_FOOTER_FULLSCREEN(LOCALE_META_TEXT['LOCALE_FOOTER_FULLSCREEN']);
$locale->setLOCALE_PREVIOUS_PAGE(LOCALE_META_TEXT['LOCALE_PREVIOUS_PAGE']);
$locale->setLOCALE_NEXT_PAGE(LOCALE_META_TEXT['LOCALE_NEXT_PAGE']);
$locale->setLOCALE_SELECT_LANGUAGE(LOCALE_META_TEXT['LOCALE_SELECT_LANGUAGE']);

$ofy->put($locale);		

require('../../locale/'.LOCALES[$row]['configuration']);
 	
for ($articlerow = 0; $articlerow < 22; $articlerow++ ) { 	

	echo 'creating article and pages for '.$ALL_CHAPTERS[$articlerow]['stub'];	 		
	
	$article = new Article(LOCALES[$row]['code'].'|'.$ALL_CHAPTERS[$articlerow]['stub'], $ALL_CHAPTERS[$articlerow]['active'], LOCALES[$row]['code'], $ALL_CHAPTERS[$articlerow]['numberOfPages'], $ALL_CHAPTERS[$articlerow]['stub'], $ALL_CHAPTERS[$articlerow]['subtitle'], $ALL_CHAPTERS[$articlerow]['title'], (int)$ALL_CHAPTERS[$articlerow]['order'], ($ALL_CHAPTERS[$articlerow]['hidden']==null ? '' : $ALL_CHAPTERS[$articlerow]['hidden']));	
	
	$ofy->put($article);
	
	$pageindex = 1;
	
	for ($articlepage = 0; $articlepage < $ALL_CHAPTERS[$articlerow]['numberOfPages']; $articlepage++ ) { 	
		
		$filepath = $ALL_CHAPTERS[$articlerow]['stub'].'-'.$pageindex.'.html'; 
		
		echo $filepath;		

		// The file name of the page we are processing
		$filename = '../../locale/'.LOCALES[$row]['code'].'/pages/'.$filepath;
		
		echo 'file to open is '.$filename;
			
		// Add all of the contents of the page to concatenated content
		$contents = file_get_contents($filename);
		
		$contents = htmlspecialchars($contents, ENT_NOQUOTES, 'UTF-8');
		echo 'contents are '.$contents;		
		
		$page = new Page(LOCALES[$row]['code'].'|'.$ALL_CHAPTERS[$articlerow]['stub'].'|'.$pageindex, $ALL_CHAPTERS[$articlerow]['stub'], LOCALES[$row]['code'], $pageindex, $ALL_CHAPTERS[$articlerow]['templates'][$pageindex-1], $contents);	
		
		$ofy->put($page);	
		
		$pageindex++;
	}	
}


?>