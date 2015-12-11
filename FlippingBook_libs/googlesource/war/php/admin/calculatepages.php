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

require_once('../includes/auth.php');

/*if(!isset($_REQUEST['locale'])) {
	echo 'Must set locale.';
	return;
}

require('../../locale/locale.php');*/
	
$localeclass = java_class('com.fi.twentythings.Locale');			
		
$articleclass = java_class('com.fi.twentythings.Article');	

$pageclass = java_class('com.fi.twentythings.Page');

ObjectifyService::register($localeclass);		
	
ObjectifyService::register($articleclass);

ObjectifyService::register($pageclass);	
	
$obj_service = new Java('com.googlecode.objectify.ObjectifyService');		
			
$ofy = $obj_service->begin();

$locales = $ofy->query($localeclass)->list();

foreach ($locales as $locale) {
	$articles = $ofy->query($articleclass)->filter('locale', $locale->getId())->list();
	foreach ($articles as $article) {
		echo 'locale is '.$locale;		
		$pagecount = $ofy->query($pageclass)->filter('locale', $locale->getId())->filter('stub', $article->getStub())->count();
		echo 'pagecount is '.$pagecount;
		$article->setNumberOfPages($pagecount);
		$ofy->put($article);	
	}
	
}

?>