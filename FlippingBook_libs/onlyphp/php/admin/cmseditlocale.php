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

import com.fi.twentythings.Locale;
import com.fi.twentythings.Article;
import com.googlecode.objectify.Objectify;
import com.googlecode.objectify.ObjectifyService;

require_once('../includes/auth.php');
require_once('../includes/objectify.php');

/**
 * this method retrieves all Articles for the specified Locale
 */

function get_articles() {
	global $ofy, $articleclass;

	if(isset($_POST['save'])) {
		$localekey = $_POST['localeinput'];
	} else {
		$localekey = $_REQUEST['locale'];	
	}	
	
	$articles = $ofy->query($articleclass)->filter('locale',$localekey)->list();
	
	if($articles->size()>0) {
	
		echo '<h2>Edit Locale Articles</h2>';
	
		echo '<ol>';	
	
		foreach($articles as $article) {
			$stubvalue = $article->getStub();
			echo '<li><a href="./cmseditarticle?locale='.$localekey.'&article='.$stubvalue.'">'.$stubvalue.'</a></li>';
		}
	
		echo '</ol>';
	}
	
}

/**
 * this method retrieves the Locale abbreviation from the query string
 */

function get_locale_from_query_string() {
	
	if(isset($_POST['save'])) {
		$locale = $_POST['localeinput'];
	} else {
		$locale = $_REQUEST['locale'];	
	}
	
	$htmlend = '></input>';
	$htmlresult = '<input readonly="readonly" name="localeinput" type="text" value="'.$locale.'"'.$htmlend;	
	echo $htmlresult;
}

/**
 * this method saves the entered Locale data from the UI in the Locale object if it detects a form submit.  
 * if it does not detect a form submit then it performs a datastore get on the query string Locale and writes the 
 * fields out with the data from the retrieved Locale object.   
 */
 
function get_locale_meta_data() {
	global $ofy, $localeclass;
	
	if(isset($_POST['save'])) {
		$locale = new Locale();
		$locale->setId($_POST['localeinput']);		
		$locale->setLOCALE_META_DESCRIPTION($_POST['LOCALE_META_DESCRIPTION']);
		$locale->setLOCALE_DISPLAY_NAME($_POST['LOCALE_DISPLAY_NAME']);		
		$locale->setLOCALE_META_KEYWORDS($_POST['LOCALE_META_KEYWORDS']);
		$locale->setLOCALE_META_AUTHOR($_POST['LOCALE_META_AUTHOR']);
		$locale->setLOCALE_FACEBOOK_MESSAGE($_POST['LOCALE_FACEBOOK_MESSAGE']);
		$locale->setLOCALE_FACEBOOK_MESSAGE_SINGLE($_POST['LOCALE_FACEBOOK_MESSAGE_SINGLE']);
		$locale->setLOCALE_TWITTER_MESSAGE($_POST['LOCALE_TWITTER_MESSAGE']);
		$locale->setLOCALE_TWITTER_MESSAGE_SINGLE($_POST['LOCALE_TWITTER_MESSAGE_SINGLE']);
		$locale->setLOCALE_BUZZ_MESSAGE($_POST['LOCALE_BUZZ_MESSAGE']);
		$locale->setLOCALE_BUZZ_MESSAGE_SINGLE($_POST['LOCALE_BUZZ_MESSAGE_SINGLE']);
		$locale->setLOCALE_SHARING_IMAGE($_POST['LOCALE_SHARING_IMAGE']);
		$locale->setLOCALE_TITLE($_POST['LOCALE_TITLE']);
		$locale->setLOCALE_NOT_SUPPORTED($_POST['LOCALE_NOT_SUPPORTED']);
		$locale->setLOCALE_NOT_SUPPORTED_IE($_POST['LOCALE_NOT_SUPPORTED_IE']);
		$locale->setLOCALE_MENU_TOT($_POST['LOCALE_MENU_TOT']);
		$locale->setLOCALE_MENU_FORWARD($_POST['LOCALE_MENU_FORWARD']);
		$locale->setLOCALE_MENU_CREDITS($_POST['LOCALE_MENU_CREDITS']);
		$locale->setLOCALE_SEARCH_PLACEHOLDER($_POST['LOCALE_SEARCH_PLACEHOLDER']);
		$locale->setLOCALE_SEARCH_INVALID($_POST['LOCALE_SEARCH_INVALID']);
		$locale->setLOCALE_SEARCH_RESULTS_CHAPTERS($_POST['LOCALE_SEARCH_RESULTS_CHAPTERS']);
		$locale->setLOCALE_SEARCH_RESULTS_PAGES($_POST['LOCALE_SEARCH_RESULTS_PAGES']);
		$locale->setLOCALE_FRONT_COVER_INTRO($_POST['LOCALE_FRONT_COVER_INTRO']);
		$locale->setLOCALE_FRONT_COVER_CTA($_POST['LOCALE_FRONT_COVER_CTA']);
		$locale->setLOCALE_SHARER_LABEL1($_POST['LOCALE_SHARER_LABEL1']);
		$locale->setLOCALE_SHARER_LABEL2($_POST['LOCALE_SHARER_LABEL2']);
		$locale->setLOCALE_TOC_TITLE($_POST['LOCALE_TOC_TITLE']);
		$locale->setLOCALE_TOC_BACK($_POST['LOCALE_TOC_BACK']);
		$locale->setLOCALE_OVERLAY_CLOSE($_POST['LOCALE_OVERLAY_CLOSE']);
		$locale->setLOCALE_OVERLAY_PRINT_TITLE($_POST['LOCALE_OVERLAY_PRINT_TITLE']);
		$locale->setLOCALE_OVERLAY_PRINT_DESCRIPTION($_POST['LOCALE_OVERLAY_PRINT_DESCRIPTION']);
		$locale->setLOCALE_OVERLAY_PDF_TITLE($_POST['LOCALE_OVERLAY_PDF_TITLE']);
		$locale->setLOCALE_OVERLAY_PDF_DESCRIPTION($_POST['LOCALE_OVERLAY_PDF_DESCRIPTION']);
		$locale->setLOCALE_OVERLAY_RESUME_TITLE($_POST['LOCALE_OVERLAY_RESUME_TITLE']);
		$locale->setLOCALE_OVERLAY_RESUME_DESCRIPTION($_POST['LOCALE_OVERLAY_RESUME_DESCRIPTION']);
		$locale->setLOCALE_OVERLAY_RESUME_YES($_POST['LOCALE_OVERLAY_RESUME_YES']);
		$locale->setLOCALE_OVERLAY_RESUME_NO($_POST['LOCALE_OVERLAY_RESUME_NO']);
		$locale->setLOCALE_FOOTER_CURATOR($_POST['LOCALE_FOOTER_CURATOR']);
		$locale->setLOCALE_FOOTER_PRINT($_POST['LOCALE_FOOTER_PRINT']);
		$locale->setLOCALE_FOOTER_SHARE($_POST['LOCALE_FOOTER_SHARE']);
		$locale->setLOCALE_FOOTER_LIGHTS($_POST['LOCALE_FOOTER_LIGHTS']);
		$locale->setLOCALE_FOOTER_FULLSCREEN($_POST['LOCALE_FOOTER_FULLSCREEN']);
		$locale->setLOCALE_PREVIOUS_PAGE($_POST['LOCALE_PREVIOUS_PAGE']);
		$locale->setLOCALE_NEXT_PAGE($_POST['LOCALE_NEXT_PAGE']);
		$locale->setLOCALE_SELECT_LANGUAGE($_POST['LOCALE_SELECT_LANGUAGE']);
		
		$locale->setLOCALE_TWENTY_THINGS_LABEL($_POST['LOCALE_TWENTY_THINGS_LABEL']);
		$locale->setLOCALE_ILLUSTRATION_LABEL($_POST['LOCALE_ILLUSTRATION_LABEL']);
		$locale->setLOCALE_WRITERS_EDITORS_LABEL($_POST['LOCALE_WRITERS_EDITORS_LABEL']);
		$locale->setLOCALE_PROJECT_CURATOR_LABEL($_POST['LOCALE_PROJECT_CURATOR_LABEL']);
		$locale->setLOCALE_DESIGN_LABEL($_POST['LOCALE_DESIGN_LABEL']);
		$locale->setLOCALE_DEVELOPMENT_LABEL($_POST['LOCALE_DEVELOPMENT_LABEL']);
		$locale->setLOCALE_SPECIAL_THANKS_LABEL($_POST['LOCALE_SPECIAL_THANKS_LABEL']);
		$locale->setLOCALE_BUILT_IN_HTML5_LABEL($_POST['LOCALE_BUILT_IN_HTML5_LABEL']);
		$locale->setLOCALE_SHARE_THIS_BOOK_LABEL($_POST['LOCALE_SHARE_THIS_BOOK_LABEL']);
		$locale->setLOCALE_SHARE_ON_LABEL($_POST['LOCALE_SHARE_ON_LABEL']);
		$locale->setLOCALE_PRINT_THING_LABEL($_POST['LOCALE_PRINT_THING_LABEL']);
		$locale->setLOCALE_PAGE_LABEL($_POST['LOCALE_PAGE_LABEL']);
		$locale->setLOCALE_PAGES_LABEL($_POST['LOCALE_PAGES_LABEL']);
		$locale->setLOCALE_ORDER($_POST['LOCALE_ORDER']);		
		
		save_entity_increment_version($locale);
		
	} 

	$requestlocale = $_REQUEST['locale'];	
	$locales = $ofy->query($localeclass)->filter('id',$requestlocale)->list();	
	
	foreach($locales as $locale) {	
		echo '<h2>Locale Meta Description:  </h2><p><textarea name="LOCALE_META_DESCRIPTION">'.$locale->getLOCALE_META_DESCRIPTION().'</textarea></p>';
		echo '<h2>Locale Meta Keywords:  </h2><p><textarea name="LOCALE_META_KEYWORDS">'.$locale->getLOCALE_META_KEYWORDS().'</textarea></p>';
		echo '<h2>Locale Display Name:  </h2><p><input type="text" name="LOCALE_DISPLAY_NAME" value="'.$locale->getLOCALE_DISPLAY_NAME().'"></p>';
		echo '<h2>Locale Meta Author:  </h2><p><input type="text" name="LOCALE_META_AUTHOR" value="'.$locale->getLOCALE_META_AUTHOR().'"></p>';
		echo '<h2>Locale Facebook Message:  </h2><p><textarea name="LOCALE_FACEBOOK_MESSAGE">'.$locale->getLOCALE_FACEBOOK_MESSAGE().'</textarea></p>';
		echo '<h2>Locale Facebook Message Single:  </h2><p><textarea name="LOCALE_FACEBOOK_MESSAGE_SINGLE">'.$locale->getLOCALE_FACEBOOK_MESSAGE_SINGLE().'</textarea></p>';
		echo '<h2>Locale Twitter Message:  </h2><p><textarea name="LOCALE_TWITTER_MESSAGE">'.$locale->getLOCALE_TWITTER_MESSAGE().'</textarea></p>';
		echo '<h2>Locale Twitter Message Single:  </h2><p><textarea name="LOCALE_TWITTER_MESSAGE_SINGLE">'.$locale->getLOCALE_TWITTER_MESSAGE_SINGLE().'</textarea></p>';
		echo '<h2>Locale Buzz Message:  </h2><p><textarea name="LOCALE_BUZZ_MESSAGE">'.$locale->getLOCALE_BUZZ_MESSAGE().'</textarea></p>';
		echo '<h2>Locale Buzz Message Single:  </h2><p><textarea name="LOCALE_BUZZ_MESSAGE_SINGLE">'.$locale->getLOCALE_BUZZ_MESSAGE_SINGLE().'</textarea></p>';
		echo '<h2>Locale Sharing Image:  </h2><p><textarea name="LOCALE_SHARING_IMAGE">'.$locale->getLOCALE_SHARING_IMAGE().'</textarea></p>';
		echo '<h2>Locale Title:  </h2><p><textarea name="LOCALE_TITLE">'.$locale->getLOCALE_TITLE().'</textarea></p>';
		echo '<h2>Locale Not Supported:  </h2><p><textarea name="LOCALE_NOT_SUPPORTED">'.$locale->getLOCALE_NOT_SUPPORTED().'</textarea></p>';
		echo '<h2>Locale Not Supported IE:  </h2><p><textarea name="LOCALE_NOT_SUPPORTED_IE">'.$locale->getLOCALE_NOT_SUPPORTED_IE().'</textarea></p>';
		echo '<h2>Locale Menu Tot:  </h2><p><input type="text" name="LOCALE_MENU_TOT" value="'.$locale->getLOCALE_MENU_TOT().'"></p>';
		echo '<h2>Locale Menu Forward:  </h2><p><input type="text" name="LOCALE_MENU_FORWARD" value="'.$locale->getLOCALE_MENU_FORWARD().'"></p>';
		echo '<h2>Locale Menu Credits:  </h2><p><input type="text" name="LOCALE_MENU_CREDITS" value="'.$locale->getLOCALE_MENU_CREDITS().'"></p>';
		echo '<h2>Locale Search Placeholder:  </h2><p><input type="text" name="LOCALE_SEARCH_PLACEHOLDER" value="'.$locale->getLOCALE_SEARCH_PLACEHOLDER().'"></p>';
		echo '<h2>Locale Search Invalid:  </h2><p><input type="text" name="LOCALE_SEARCH_INVALID" value="'.$locale->getLOCALE_SEARCH_INVALID().'"></p>';
		echo '<h2>Locale Search Results Chapters:  </h2><p><input type="text" name="LOCALE_SEARCH_RESULTS_CHAPTERS" value="'.$locale->getLOCALE_SEARCH_RESULTS_CHAPTERS().'"></p>';
		echo '<h2>Locale Search Results Pages:  </h2><p><input type="text" name="LOCALE_SEARCH_RESULTS_PAGES" value="'.$locale->getLOCALE_SEARCH_RESULTS_PAGES().'"></p>';
		echo '<h2>Locale Front Cover Intro:  </h2><p><textarea name="LOCALE_FRONT_COVER_INTRO">'.$locale->getLOCALE_FRONT_COVER_INTRO().'</textarea></p>';
		echo '<h2>Locale Front Cover CTA:  </h2><p><input type="text" name="LOCALE_FRONT_COVER_CTA" value="'.$locale->getLOCALE_FRONT_COVER_CTA().'"></p>';
		echo '<h2>Locale Sharer Label 1:  </h2><p><input type="text" name="LOCALE_SHARER_LABEL1" value="'.$locale->getLOCALE_SHARER_LABEL1().'"></p>';
		echo '<h2>Locale Sharer Label 2:  </h2><p><input type="text" name="LOCALE_SHARER_LABEL2" value="'.$locale->getLOCALE_SHARER_LABEL2().'"></p>';
		echo '<h2>Locale TOC Title:  </h2><p><input type="text" name="LOCALE_TOC_TITLE" value="'.$locale->getLOCALE_TOC_TITLE().'"></p>';
		echo '<h2>Locale TOC Back:  </h2><p><input type="text" name="LOCALE_TOC_BACK" value="'.$locale->getLOCALE_TOC_BACK().'"></p>';
		echo '<h2>Locale Overlay Close:  </h2><p><input type="text" name="LOCALE_OVERLAY_CLOSE" value="'.$locale->getLOCALE_OVERLAY_CLOSE().'"></p>';
		echo '<h2>Locale Overlay Print Title:  </h2><p><input type="text" name="LOCALE_OVERLAY_PRINT_TITLE" value="'.$locale->getLOCALE_OVERLAY_PRINT_TITLE().'"></p>';
		echo '<h2>Locale Overlay Print Description:  </h2><p><input type="text" name="LOCALE_OVERLAY_PRINT_DESCRIPTION" value="'.$locale->getLOCALE_OVERLAY_PRINT_DESCRIPTION().'"></p>';
		echo '<h2>Locale Overlay PDF Title:  </h2><p><input type="text" name="LOCALE_OVERLAY_PDF_TITLE" value="'.$locale->getLOCALE_OVERLAY_PDF_TITLE().'"></p>';
		echo '<h2>Locale Overlay PDF Description:  </h2><p><input type="text" name="LOCALE_OVERLAY_PDF_DESCRIPTION" value="'.$locale->getLOCALE_OVERLAY_PDF_DESCRIPTION().'"></p>';
		echo '<h2>Locale Overlay Resume Title:  </h2><p><input type="text" name="LOCALE_OVERLAY_RESUME_TITLE" value="'.$locale->getLOCALE_OVERLAY_RESUME_TITLE().'"></p>';
		echo '<h2>Locale Overlay Resume Desciption:  </h2><p><input type="text" name="LOCALE_OVERLAY_RESUME_DESCRIPTION" value="'.$locale->getLOCALE_OVERLAY_RESUME_DESCRIPTION().'"></p>';
		echo '<h2>Locale Overlay Resume Yes:  </h2><p><input type="text" name="LOCALE_OVERLAY_RESUME_YES" value="'.$locale->getLOCALE_OVERLAY_RESUME_YES().'"></p>';
		echo '<h2>Locale Overlay Resume No:  </h2><p><input type="text" name="LOCALE_OVERLAY_RESUME_NO" value="'.$locale->getLOCALE_OVERLAY_RESUME_NO().'"></p>';
		echo '<h2>Locale Footer Curator:  </h2><p><textarea name="LOCALE_FOOTER_CURATOR">'.$locale->getLOCALE_FOOTER_CURATOR().'</textarea></p>';
		echo '<h2>Locale Footer Print:  </h2><p><input type="text" name="LOCALE_FOOTER_PRINT" value="'.$locale->getLOCALE_FOOTER_PRINT().'"></p>';
		echo '<h2>Locale Footer Share:  </h2><p><input type="text" name="LOCALE_FOOTER_SHARE" value="'.$locale->getLOCALE_FOOTER_SHARE().'"></p>';
		echo '<h2>Locale Footer Lights:  </h2><p><input type="text" name="LOCALE_FOOTER_LIGHTS" value="'.$locale->getLOCALE_FOOTER_LIGHTS().'"></p>';
		echo '<h2>Locale Footer Fullscreen:  </h2><p><input type="text" name="LOCALE_FOOTER_FULLSCREEN" value="'.$locale->getLOCALE_FOOTER_FULLSCREEN().'"></p>';
		echo '<h2>Locale Previous Page:  </h2><p><input type="text" name="LOCALE_PREVIOUS_PAGE" value="'.$locale->getLOCALE_PREVIOUS_PAGE().'"></p>';	
		echo '<h2>Locale Next Page:  </h2><p><input type="text" name="LOCALE_NEXT_PAGE" value="'.$locale->getLOCALE_NEXT_PAGE().'"></p>';
		echo '<h2>Locale Select Language:  </h2><p><textarea name="LOCALE_SELECT_LANGUAGE">'.$locale->getLOCALE_SELECT_LANGUAGE().'</textarea></p>';

		echo '<h2>Locale Twenty Things Label:  </h2><p><textarea name="LOCALE_TWENTY_THINGS_LABEL">'.$locale->getLOCALE_TWENTY_THINGS_LABEL().'</textarea></p>';
		echo '<h2>Locale Illustration Label:  </h2><p><textarea name="LOCALE_ILLUSTRATION_LABEL">'.$locale->getLOCALE_ILLUSTRATION_LABEL().'</textarea></p>';
		echo '<h2>Locale Writers/Editors Label:  </h2><p><textarea name="LOCALE_WRITERS_EDITORS_LABEL">'.$locale->getLOCALE_WRITERS_EDITORS_LABEL().'</textarea></p>';
		echo '<h2>Locale Project Curator Label:  </h2><p><textarea name="LOCALE_PROJECT_CURATOR_LABEL">'.$locale->getLOCALE_PROJECT_CURATOR_LABEL().'</textarea></p>';
		echo '<h2>Locale Design Label:  </h2><p><textarea name="LOCALE_DESIGN_LABEL">'.$locale->getLOCALE_DESIGN_LABEL().'</textarea></p>';
		echo '<h2>Locale Development Label:  </h2><p><textarea name="LOCALE_DEVELOPMENT_LABEL">'.$locale->getLOCALE_DEVELOPMENT_LABEL().'</textarea></p>';
		echo '<h2>Locale Special Thanks Label:  </h2><p><textarea name="LOCALE_SPECIAL_THANKS_LABEL">'.$locale->getLOCALE_SPECIAL_THANKS_LABEL().'</textarea></p>';
		echo '<h2>Locale Built in HTML5 Label:  </h2><p><textarea name="LOCALE_BUILT_IN_HTML5_LABEL">'.$locale->getLOCALE_BUILT_IN_HTML5_LABEL().'</textarea></p>';
		echo '<h2>Locale Share This Book Label:  </h2><p><textarea name="LOCALE_SHARE_THIS_BOOK_LABEL">'.$locale->getLOCALE_SHARE_THIS_BOOK_LABEL().'</textarea></p>';
		echo '<h2>Locale Share On Label:  </h2><p><textarea name="LOCALE_SHARE_ON_LABEL">'.$locale->getLOCALE_SHARE_ON_LABEL().'</textarea></p>';
		echo '<h2>Locale Print Thing Label:  </h2><p><textarea name="LOCALE_PRINT_THING_LABEL">'.$locale->getLOCALE_PRINT_THING_LABEL().'</textarea></p>';
		echo '<h2>Locale Page Label:  </h2><p><textarea name="LOCALE_PAGE_LABEL">'.$locale->getLOCALE_PAGE_LABEL().'</textarea></p>';
		echo '<h2>Locale Pages Label:  </h2><p><textarea name="LOCALE_PAGES_LABEL">'.$locale->getLOCALE_PAGES_LABEL().'</textarea></p>';
		echo '<h2>Locale Order:  </h2><p><textarea name="LOCALE_ORDER">'.$locale->getLOCALE_ORDER().'</textarea></p>';
			
		return;
	}	
	
	echo '<h2>Locale Meta Description:  </h2><p><textarea name="LOCALE_META_DESCRIPTION"></textarea></p>';	
	echo '<h2>Locale Meta Keywords:  </h2><p><textarea name="LOCALE_META_KEYWORDS"></textarea></p>';
	echo '<h2>Locale Display Name:  </h2><p><textarea name="LOCALE_DISPLAY_NAME"></textarea></p>';
	echo '<h2>Locale Meta Author:  </h2><p><textarea name="LOCALE_META_AUTHOR"></textarea></p>';
	echo '<h2>Locale Facebook Message:  </h2><p><textarea name="LOCALE_FACEBOOK_MESSAGE"></textarea></p>';
	echo '<h2>Locale Facebook Message Single:  </h2><p><textarea name="LOCALE_FACEBOOK_MESSAGE_SINGLE"></textarea></p>';
	echo '<h2>Locale Twitter Message:  </h2><p><textarea name="LOCALE_TWITTER_MESSAGE"></textarea></p>';
	echo '<h2>Locale Twitter Message Single:  </h2><p><textarea name="LOCALE_TWITTER_MESSAGE_SINGLE"></textarea></p>';
	echo '<h2>Locale Buzz Message:  </h2><p><textarea name="LOCALE_BUZZ_MESSAGE"></textarea></p>';
	echo '<h2>Locale Buzz Message Single:  </h2><p><textarea name="LOCALE_BUZZ_MESSAGE_SINGLE"></textarea></p>';
	echo '<h2>Locale Sharing Image:  </h2><p><textarea name="LOCALE_SHARING_IMAGE"></textarea></p>';
	echo '<h2>Locale Title:  </h2><p><textarea name="LOCALE_TITLE"></textarea></p>';
	echo '<h2>Locale Not Supported:  </h2><p><textarea name="LOCALE_NOT_SUPPORTED"></textarea></p>';
	echo '<h2>Locale Not Supported IE:  </h2><p><textarea name="LOCALE_NOT_SUPPORTED_IE"></textarea></p>';
	echo '<h2>Locale Menu Tot:  </h2><p><textarea name="LOCALE_MENU_TOT"></textarea></p>';
	echo '<h2>Locale Menu Forward:  </h2><p><textarea name="LOCALE_MENU_FORWARD"></textarea></p>';
	echo '<h2>Locale Menu Credits:  </h2><p><textarea name="LOCALE_MENU_CREDITS"></textarea></p>';
	echo '<h2>Locale Search Placeholder:  </h2><p><textarea name="LOCALE_SEARCH_PLACEHOLDER"></textarea></p>';
	echo '<h2>Locale Search Invalid:  </h2><p><textarea name="LOCALE_SEARCH_INVALID"></textarea></p>';
	echo '<h2>Locale Search Results Chapters:  </h2><p><textarea name="LOCALE_SEARCH_RESULTS_CHAPTERS"></textarea></p>';
	echo '<h2>Locale Search Results Pages:  </h2><p><textarea name="LOCALE_SEARCH_RESULTS_PAGES"></textarea></p>';
	echo '<h2>Locale Front Cover Intro:  </h2><p><textarea name="LOCALE_FRONT_COVER_INTRO"></textarea></p>';
	echo '<h2>Locale Front Cover CTA:  </h2><p><textarea name="LOCALE_FRONT_COVER_CTA"></textarea></p>';
	echo '<h2>Locale Sharer Label 1:  </h2><p><textarea name="LOCALE_SHARER_LABEL1"></textarea></p>';
	echo '<h2>Locale Sharer Label 2:  </h2><p><textarea name="LOCALE_SHARER_LABEL2"></textarea></p>';
	echo '<h2>Locale TOC Title:  </h2><p><textarea name="LOCALE_TOC_TITLE"></textarea></p>';
	echo '<h2>Locale TOC Back:  </h2><p><textarea name="LOCALE_TOC_BACK"></textarea></p>';
	echo '<h2>Locale Overlay Close:  </h2><p><textarea name="LOCALE_OVERLAY_CLOSE"></textarea></p>';
	echo '<h2>Locale Overlay Print Title:  </h2><p><textarea name="LOCALE_OVERLAY_PRINT_TITLE"></textarea></p>';
	echo '<h2>Locale Overlay Print Description:  </h2><p><textarea name="LOCALE_OVERLAY_PRINT_DESCRIPTION"></textarea></p>';
	echo '<h2>Locale Overlay PDF Title:  </h2><p><textarea name="LOCALE_OVERLAY_PDF_TITLE"></textarea></p>';
	echo '<h2>Locale Overlay PDF Description:  </h2><p><textarea name="LOCALE_OVERLAY_PDF_DESCRIPTION"></textarea></p>';
	echo '<h2>Locale Overlay Resume Title:  </h2><p><textarea name="LOCALE_OVERLAY_RESUME_TITLE"></textarea></p>';
	echo '<h2>Locale Overlay Resume Desciption:  </h2><p><textarea name="LOCALE_OVERLAY_RESUME_DESCRIPTION"></textarea></p>';
	echo '<h2>Locale Overlay Resume Yes:  </h2><p><textarea name="LOCALE_OVERLAY_RESUME_YES"></textarea></p>';
	echo '<h2>Locale Overlay Resume No:  </h2><p><textarea name="LOCALE_OVERLAY_RESUME_NO"></textarea></p>';
	echo '<h2>Locale Footer Curator:  </h2><p><textarea name="LOCALE_FOOTER_CURATOR"></textarea></p>';
	echo '<h2>Locale Footer Print:  </h2><p><textarea name="LOCALE_FOOTER_PRINT"></textarea></p>';
	echo '<h2>Locale Footer Share:  </h2><p><textarea name="LOCALE_FOOTER_SHARE"></textarea></p>';
	echo '<h2>Locale Footer Lights:  </h2><p><textarea name="LOCALE_FOOTER_LIGHTS"></textarea></p>';
	echo '<h2>Locale Footer Fullscreen:  </h2><p><textarea name="LOCALE_FOOTER_FULLSCREEN"></textarea></p>';
	echo '<h2>Locale Previous Page:  </h2><p><textarea name="LOCALE_PREVIOUS_PAGE"></textarea></p>';	
	echo '<h2>Locale Next Page:  </h2><p><textarea name="LOCALE_NEXT_PAGE"></textarea></p>';
	echo '<h2>Locale Select Language:  </h2><p><textarea name="LOCALE_SELECT_LANGUAGE"></textarea></p>';
	
	echo '<h2>Locale Twenty Things Label:  </h2><p><textarea name="LOCALE_TWENTY_THINGS_LABEL"></textarea></p>';
	echo '<h2>Locale Illustration Label:  </h2><p><textarea name="LOCALE_ILLUSTRATION_LABEL"</textarea></p>';
	echo '<h2>Locale Writers/Editors Label:  </h2><p><textarea name="LOCALE_WRITERS_EDITORS_LABEL"></textarea></p>';
	echo '<h2>Locale Project Curator Label:  </h2><p><textarea name="LOCALE_PROJECT_CURATOR_LABEL"></textarea></p>';
	echo '<h2>Locale Design Label:  </h2><p><textarea name="LOCALE_DESIGN_LABEL"></textarea></p>';
	echo '<h2>Locale Development Label:  </h2><p><textarea name="LOCALE_DEVELOPMENT_LABEL"></textarea></p>';
	echo '<h2>Locale Special Thanks Label:  </h2><p><textarea name="LOCALE_SPECIAL_THANKS_LABEL"></textarea></p>';
	echo '<h2>Locale Built in HTML5 Label:  </h2><p><textarea name="LOCALE_BUILT_IN_HTML5_LABEL"></textarea></p>';
	echo '<h2>Locale Share This Book Label:  </h2><p><textarea name="LOCALE_SHARE_THIS_BOOK_LABEL"></textarea></p>';
	echo '<h2>Locale Share On Label:  </h2><p><textarea name="LOCALE_SHARE_ON_LABEL"></textarea></p>';
	echo '<h2>Locale Print Thing Label:  </h2><p><textarea name="LOCALE_PRINT_THING_LABEL"></textarea></p>';
	echo '<h2>Locale Page Label:  </h2><p><textarea name="LOCALE_PAGE_LABEL"></textarea></p>';
	echo '<h2>Locale Pages Label:  </h2><p><textarea name="LOCALE_PAGES_LABEL"></textarea></p>';
	echo '<h2>Locale Order:  </h2><p><textarea name="LOCALE_ORDER"></textarea></p>';
			
}

/** 
 * this method creates the link used for creating a new Article for the currently displayed Locale
 */
 
function new_article_link() {
	if($_REQUEST['LOCALE_META_KEYWORDS']!=null && $_REQUEST['LOCALE_META_KEYWORDS']!='') {
		$newarticlelink = '<h2><a href="./cmseditarticle?locale='.$_REQUEST['locale'].'&new=1">Add Article +</a></h2>';
		echo $newarticlelink;
	}
}


?>

<!doctype html>
<html lang="en">

	<head>
		<meta charset="utf-8"> 
		<title>20 Things CMS</title>
		<link type="text/css" rel="stylesheet" href="../../css/cms.css" />
	</head>

	<body> 

		<?php require_once('./cmsnav.php'); ?>

		<h1>Edit Locale</h1>
		
		<div id="wrap">
		
		
			<!-- Edit Locale -->
			<form name="editlocaleform" class="section" method="POST" action="<? $_SERVER ?>"> <!-- onsubmit="return onsubmitform();-->
				
				<?php if(isset($_POST['save'])) {
					echo '<p class="success">The Locale was saved successfully!</p>';
				} ?>
				
				<h2>Locale Code</h2>
				
				<p>
				<?get_locale_from_query_string()?>
				</p>
				
				<?get_locale_meta_data()?>
				
				<!--<h2>Meta Keywords</h2>
				<p><textarea>internet, computing, brwosers, speed, chrome</textarea></p>
				
				<h2>Etc...</h2>-->

				<p class="submit">
					<input name="save" type="submit" value="Save Changes" />
				</p>

			</form>

			
			<div class="section">					
				<?get_articles()?>
			</div>
			
		</div>

		<script type="text/javascript">
					
		</script>
		
	</body>

</html>

