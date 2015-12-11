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


import com.googlecode.objectify.ObjectifyService;
import com.googlecode.objectify.Objectify;
import com.fi.twentythings.Locale;

require_once('../includes/auth.php');


/**
 * get the locales fromn the datastore and add them to a select
 */
 
function locale_codes_dropdown() {

	$obj_service = new Java('com.googlecode.objectify.ObjectifyService');
	$ofy = $obj_service->begin();
	$localeclass = java_class('com.fi.twentythings.Locale');
	ObjectifyService::register($localeclass);	
	
	$localeField = 'locale';
	
	$locales = $ofy->query($localeclass)->list();
	
	foreach( $locales as $locale ) {
        echo '<p><a href="/cmseditlocale?locale='.$locale->getId().'">'.$locale->getId().'</a></p>'."\n";                
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

		<h1>Select Locale</h1>		
		
		<div id="wrap">
		
			<!-- Select Locale -->
			<form id="selectLocale" class="section" action="cmseditlocale">
				<br />
				<p>
				<?locale_codes_dropdown();?>
			</form>
			
			<!-- Create Locale -->

		</div>
		
	</body>

</html>

