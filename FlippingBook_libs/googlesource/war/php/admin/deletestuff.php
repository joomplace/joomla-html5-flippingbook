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
import com.fi.twentythings.Version;
import com.googlecode.objectify.Objectify;
import com.googlecode.objectify.ObjectifyService;
			
global $ofy, $obj_service, $localeclass, $articleclass, $pageclass, $versionclass, $loc, $langcode;

$localeclass = java_class('com.fi.twentythings.Locale');
$articleclass = java_class('com.fi.twentythings.Article');
$pageclass = java_class('com.fi.twentythings.Page');
$versionclass = java_class('com.fi.twentythings.Version');

ObjectifyService::register($localeclass);	
ObjectifyService::register($articleclass);	
ObjectifyService::register($pageclass);
ObjectifyService::register($versionclass);		

$obj_service = new Java('com.googlecode.objectify.ObjectifyService');
$ofy = $obj_service->begin();	
//$loc = $ofy->query($localeclass)->filter('id', getBrowserLanguage())->get();


	echo 'in delete';
	$ofy->delete($localeclass, $key)->filter('id',;
	$version->setVersion(($version->getVersion())+1);	
	$ofy->put($version);


?>