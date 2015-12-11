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

require_once('../includes/auth.php');

import com.fi.twentythings.Page;
import com.googlecode.objectify.Objectify;
import com.googlecode.objectify.ObjectifyService;

require_once('../includes/objectify.php');

/** 
 * this method creates the link used for creating a new Page
 */
 
function new_page_link() {
	if($_REQUEST['new']!='1') {
		$newpagelink = '<div class="section"><h2><a href="./cmseditpage?locale='.$_REQUEST['locale'].'&article='.$_REQUEST['article'].'&new=1">Add Page +</a></h2></div>';
		echo $newpagelink;
	}
}

/** 
 * this method gets the Page param value from the query string OR as a POST value from a form submit
 */
 
function get_page_from_query_string() {
	if(isset($_POST['save'])) {
		$page = $_POST['pageinput'];
	} else {
		$page = $_REQUEST['article'];	
	}
	$htmlend = '></input>';
	echo $htmlresult;	
}

/** 
 * this method creates the link used for removing a Page from an Article
 */
 
function remove_page_link() {
		$removepagelink = '<h2><a onclick="var answer = confirm(\'Are you sure you want to delete the page?\n(This cannot be undone)\'); if(!answer){ return false;}" href="./cmseditpage?locale='.$_REQUEST['locale'].'&article='.$_REQUEST['article'].'&pagenumber='.$_REQUEST['pagenumber'].'&action=remove">Remove This Page -</a></h2>';
		echo $removepagelink;
}

/** 
 * this method gets all pages for a specified article and displays links
 * 	
 */

function get_pages() {
	
	global $ofy, $pageclass;
	
	$pagekey = $_REQUEST['article'];	
	$localekey = $_REQUEST['locale'];

	/*if($_REQUEST['new']=='1') {
		$pagekey = $_REQUEST['article'];	
		$localekey = $_REQUEST['locale'];
	} else if(isset($_POST['save'])) {
		$pagekey = $_REQUEST['article'];	
		$localekey = $_REQUEST['locale'];
	} else {		
		$pagekey = $_REQUEST['article'];	
		$localekey = $_REQUEST['locale'];
	}*/	

	
	$pages = $ofy->query($pageclass)->filter('locale', $localekey)->filter('stub', $pagekey)->list();
	
	echo '<ol>';	
	
	foreach($pages as $page) {
		echo '<li><a href="./cmseditpage?article='.$pagekey.'&locale='.$localekey.'&pagenumber='.$page->getPageNumber().'">'.$pagekey.'-'.$page->getPageNumber().'</a></li>';
	}
	
	echo '</ol>';
	
}


function get_page_properties() {	
	global $ofy, $pageclass;


	//if we detect a form submit and are saving the Page....
	if(isset($_POST['save'])) {
	
		$requestlocale = $_REQUEST['locale'];	
		$requestarticle = $_REQUEST['article'];	
		
		if($_REQUEST['pagenumber']==null) {
			$pagenumber =  $_POST['pagePageNumber'];
		} else {
			$pagenumber =  $_REQUEST['pagenumber'];
		}		
		 
		$pagekey = $requestlocale.'|'.$requestarticle.'|'.$pagenumber;	
		$page = new Page($pagekey, $requestarticle, $requestlocale, $pagenumber, $_POST['pageTemplate'], htmlspecialchars($_POST['pageContent'], ENT_NOQUOTES));	
		
		save_entity_increment_version($page);
					
		echo '<p class="success">The Page was saved successfully!</p>';
		echo '<h2>Locale:  </h2><p><input readonly="readonly" name="pageLocale" value="'.$page->getLocale().'"></p>';
		echo '<h2>Article:  </h2><p><input readonly="readonly" name="pageArticle" value="'.$page->getStub().'"></p>';		
		echo '<h2>Page Number:  </h2><p><input name="pagePageNumber" value="'.$page->getPageNumber().'"></p>';	
		echo '<h2>CSS Template:  </h2><p><input name="pageTemplate" value="'.$page->getTemplate().'"></p>';	
				
		if(stripos($page->getContent(), '&#feff;')) {
			$pagecontent = substr($page->getContent(), 8);
		} else {
			$pagecontent = $page->getContent();
		}

		echo '<h2>HTML Content:  </h2><p class="bigtext"><textarea name="pageContent">'.$pagecontent.'</textarea></p>';				
		
		return;		
				
	} else {
		
		if($_REQUEST['new']=='1') {  
			
			//is this a new page?? if so then create a blank Page for data entry.
			
			echo '<h2>Locale:  </h2><p><input readonly="readonly" name="pageLocale" value="'.$_REQUEST['locale'].'"></p>';
			echo '<h2>Article:  </h2><p><input readonly="readonly" name="pageArticle" value="'.$_REQUEST['article'].'"></p>';
			echo '<h2>Page Number:  </h2><p><input name="pagePageNumber"></p>';
			echo '<h2>CSS Template:  </h2><p><input name="pageTemplate"></p>';
			echo '<h2>HTML Content:  </h2><p class="bigtext"><textarea name="pageContent"></textarea></p>';
		} else {  
			
			//is this an existing page??  if so then write out the values from the datastore Page obejct into the input fields.
					
			$requestlocale = $_REQUEST['locale'];
			$requestarticle = $_REQUEST['article'];	
			$requestpagenumber = $_REQUEST['pagenumber'];	
			
			$pagekey = $requestlocale.'|'.$requestarticle.'|'.$requestpagenumber;
			$pages = $ofy->query($pageclass)->filter('id', $pagekey)->list();
			
			foreach($pages as $page) {	
				echo '<h2>Locale:  </h2><p><input readonly="readonly" name="pageLocale" value="'.$page->getLocale().'"></p>';
				echo '<h2>Article:  </h2><p><input readonly="readonly" name="pageArticle" value="'.$page->getStub().'"></p>';		
				echo '<h2>Page Number:  </h2><p><input name="pagePageNumber" value="'.$page->getPageNumber().'"></p>';		
				echo '<h2>CSS Template:  </h2><p><input name="pageTemplate" value="'.$page->getTemplate().'"></p>';		
						
				if(stripos($page->getContent(), '&#feff;')) {
					$pagecontent = substr($page->getContent(), 8);
				} else {
					$pagecontent = $page->getContent();
				}
		
				echo '<h2>HTML Content:  </h2><p class="bigtext"><textarea name="pageContent">'.$pagecontent.'</textarea></p>';	
				return;
			}
			
		}		
				
	}		
}

/**
 * delete the currect page and redirect to the Article view
 */
 
function remove_page() {
	global $pageclass;
	if(isset($_POST['remove'])) {
		$pagekey = $_REQUEST['locale'].'|'.$_REQUEST['article'].'|'.$_REQUEST['pagenumber'];	
		//echo $pagekey;
		delete_entity_increment_version($pagekey, $pageclass);
		$url = '/cmseditarticle?locale='.$_REQUEST['locale'].'&article='.$_REQUEST['article'];		
		redirect($url);
	}
}

function redirect($url)
{	
	header('Location: '.$url);
	exit();
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

		<h1>Edit Page</h1>
		
		<div id="wrap">
		
		
			<div class="section">
			<!-- Edit Page -->
			<form name="editpageform"" method="POST" action="<? $_SERVER ?>"> 
				
				<!--h2>Page Title</h2>-->
				
				<p>
				<?get_page_from_query_string()?>
				</p>
				
				<?get_page_properties()?>	

				<p class="submit">
					<input name="save" type="submit" value="Save Changes" />
					<?php if(!isset($_GET['new'])) : ?>
						<a id="viewLink" href="/<?php echo $_GET['locale'] . '/' . $_GET['article'] . '/' . $_GET['pagenumber']; ?>" target="_blank">View Page</a>
					<?php endif; ?>					
				</p>

			</form>	
			
			<?php if(!isset($_GET['new'])) : ?>
			<form id="delete" name="deletepageform" method="POST" action="<? $_SERVER ?>"> 
			
				<?remove_page()?>
				
				<p class="submit">
					<input onclick="var answer = confirm('Are you sure you want to delete the page?\n(This cannot be undone)'); if(!answer){ return false;}" name="remove" type="submit" value="Delete Page" />					
				</p>

			</form>
			
			<?php endif; ?>
			
			</div>			
			

			

			<div class="section">			
				<h2>Edit Article Pages</h2>
				<?get_pages()?>
			</div>		
			
			<?new_page_link()?>				
	
			
		</div>

		<script type="text/javascript">		
		</script>
		
	</body>

</html>

