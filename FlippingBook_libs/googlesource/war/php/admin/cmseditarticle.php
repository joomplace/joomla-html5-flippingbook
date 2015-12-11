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
import com.fi.twentythings.Article;
import com.googlecode.objectify.Objectify;
import com.googlecode.objectify.ObjectifyService;

require_once('../includes/objectify.php');

/** 
 * this method creates the link used for creating a new Page for the currently displayed Article
 */
 
function new_page_link() {
	if($_REQUEST['new']!='1') {
		$newpagelink = '<h2><a href="./cmseditpage?locale='.$_REQUEST['locale'].'&article='.$_REQUEST['article'].'&new=1">Add Page +</a></h2>';
		echo $newpagelink;
	}
}

/** 
 * this method gets the Pages for the specified Article
 */

function get_pages() {
	global $ofy, $pageclass;
	
	if($_REQUEST['new']=='1') {
		return;
	} else if(isset($_POST['save'])) {
		$pagekey = $_REQUEST['article'];	
		$localekey = $_REQUEST['locale'];
	} else {		
		$pagekey = $_REQUEST['article'];	
		$localekey = $_REQUEST['locale'];
	}	
	
	$pages = $ofy->query($pageclass)->filter('locale', $localekey)->filter('stub', $pagekey)->list();
	
	echo '<ol>';	
	
	foreach($pages as $page) {
		echo '<li><a href="./cmseditpage?article='.$pagekey.'&locale='.$localekey.'&pagenumber='.$page->getPageNumber().'">'.$pagekey.'-'.$page->getPageNumber().'</a></li>';
	}
	
	echo '</ol>';
	
}

function save_new_article() {
	
	global $ofy;
	
	if($_POST['articleStub']=='theend' || $_POST['articleStub']=='foreword') {
		$hidden = '1';
	} else {
		$hidden = '0';
	}
	$newarticle = new Article($_POST['articleLocale'].'|'.$_POST['articleStub'], $_POST['articleActive'], $_POST['articleLocale'], $_POST['articleNumberOfPages'], $_POST['articleStub'], $_POST['articleTitle'], $_POST['articleSubtitle'], $_POST['articleOrder'], $hidden);	
	save_entity_increment_version($newarticle);
}

function get_article_from_query_string() {
	
	global $ofy, $articleclass;	
	
	if($_REQUEST['new']=='1' && !isset($_POST['save'])) {
		$htmlresult = '<input name="articleinput"></p>';  // type="text"
		echo $htmlresult;	
		return;
	} else if($_REQUEST['new']=='1' && isset($_POST['save'])) {
		save_new_article();		
		$htmlresult = '<input name="articleinput" type="text" value="'.$_POST['articleinput'].'">';
		echo $htmlresult;	
		echo 'The Article was saved successfully!';			
		return;
	} else if(isset($_POST['save'])) {
		
		//getting the article from the datastore and storing the new UI entered values and saving back to the datastore
		
		$articlekey = $_POST['articleLocale'].'|'.$_POST['articleStub'];
		$article = $ofy->get($articleclass, $articlekey);
		$article->setLocale($_POST['articleLocale']);		
		$article->setActive($_POST['articleActive']);
		//$article->setHidden($_POST['articleHidden']);	
		$article->setOrder($_POST['articleOrder']);				
		$article->setNumberOfPages($_POST['articleNumberOfPages']);
		$article->setStub($_POST['articleStub']);
		$article->setTitle(htmlspecialchars($_POST['articleTitle'], ENT_QUOTES));
		$article->setSubtitle(htmlspecialchars($_POST['articleSubtitle'], ENT_QUOTES));
		save_entity_increment_version($article);			
		$article = $_POST['articleinput'];
	} else {		 
		$article = $_REQUEST['article'];		
	}
	
	$htmlend = '></input>';
	echo $htmlresult;
	
	if(isset($_POST['save'])) {
		echo '<p class="success">The Article was saved successfully!</p>';
	} 				
		
}

function get_article_properties() {	

	global $ofy, $articleclass;
	
	if($_REQUEST['new']=='1' && !isset($_POST['save'])) {
		echo '<h2>Locale:  </h2><p><input name="articleLocale" value="'.$_REQUEST['locale'].'"></p>';
		echo '<h2>Number of pages in article:  </h2><p><input name="articleNumberOfPages"></p>';
		echo '<h2>Stub:  </h2><p><input name="articleStub" value="'.$_REQUEST['article'].'"></p>';
		echo '<h2>Title:  </h2><p><input name="articleTitle"></p>';		
		echo '<h2>Order:  </h2><p><input name="articleOrder"></p>';		
		echo '<h2>Active:  </h2><p><input name="articleActive"></p>';
		echo '<h2>Subtitle:  </h2><p><input name="articleSubtitle"></p>';
		return;
	} else if ($_REQUEST['new']=='1' && isset($_POST['save'])) { 	
		
		if(isset($_POST['save'])) {
			$articlekey = $_POST['articleLocale'].'|'.$_POST['articleStub'];
			$article = $ofy->get($articleclass, $articlekey);
			$article->setLocale($_POST['articleLocale']);		
			$article->setActive($_POST['articleActive']);
			$article->setOrder($_POST['articleOrder']);			
			$article->setNumberOfPages($_POST['articleNumberOfPages']);
			$article->setStub($_POST['articleStub']);
			$article->setTitle($_POST['articleTitle']);
			$article->setSubtitle($_POST['articleSubtitle']);	
			save_entity_increment_version($article);
			
		} 
	
		echo '<h2>Locale:  </h2><p><input readonly="readonly" name="articleLocale" value="'.$article->getLocale().'"></p>';
		//echo '<h2>Active:  </h2><p><input name="articleActive" value="'.$article->getActive().'"></p>';
		echo '<h2>Number of pages in article:  </h2><p><input name="articleNumberOfPages" value="'.$article->getNumberOfPages().'"></p>';
		//echo '<h2>Hidden:  </h2><p><input name="articleHidden" value="'.$article->getHidden().'"></p>';
		//echo '<h2>Order:  </h2><p><input name="articleOrder" value="'.$article->getOrder().'"></p>';		
		echo '<h2>Stub:  </h2><p><input readonly="readonly" name="articleStub" value="'.$article->getStub().'"></p>';
		echo '<h2>Title:  </h2><p><input name="articleTitle" value="'.$article->getTitle().'"></p>';
		echo '<h2>Subtitle:  </h2><p><input name="articleSubtitle" value="'.$article->getSubtitle().'"></p>';
		
	} else if ($_REQUEST['new']==null) { 
	
		$requestlocale = $_REQUEST['locale'];	
		$requestarticle = $_REQUEST['article'];	
		$article = $ofy->query($articleclass)->filter('locale', $requestlocale)->filter('stub',$requestarticle)->get();
	
		echo '<h2>Locale:  </h2><p><input readonly="readonly" name="articleLocale" value="'.$article->getLocale().'"></p>';
		echo '<h2>Active:  </h2><p><input readonly="readonly" name="articleActive" value="'.$article->getActive().'"></p>';
		//echo '<h2>Hidden:  </h2><p><input name="articleHidden" value="'.$article->getHidden().'"></p>';		
		echo '<h2>Order:  </h2><p><input readonly="readonly" name="articleOrder" value="'.$article->getOrder().'"></p>';		
		echo '<h2>Number of pages in article:  </h2><p><input name="articleNumberOfPages" value="'.$article->getNumberOfPages().'"></p>';
		echo '<h2>Stub:  </h2><p><input readonly="readonly" name="articleStub" value="'.$article->getStub().'"></p>';
		echo '<h2>Title:  </h2><p><input name="articleTitle" value="'.$article->getTitle().'"></p>';
		echo '<h2>Subtitle:  </h2><p><input name="articleSubtitle" value="'.$article->getSubtitle().'"></p>';
	}
}

function remove_article() {
	global $articleclass;
	if(isset($_POST['remove'])) {
		$articlekey = $_REQUEST['locale'].'|'.$_REQUEST['article'];	
		echo $articlekey;
		delete_entity_increment_version($articlekey, $articleclass);
		$url = '/cmseditlocale?locale='.$_REQUEST['locale'];		
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
	
		<h1>Edit Article</h1>
		
		<div id="wrap">		
		
			<!-- Edit Article -->
			<div class="section">
			<form name="editarticleform" method="POST" action="<? $_SERVER ?>"> 
				
				<p>
				<?get_article_from_query_string()?>
				</p>
				
				<?get_article_properties()?>	

				<p class="submit">
					<input name="save" type="submit" value="Save Changes" />
					<a id="viewLink" href="/<?php echo $_GET['locale'] . '/' . $_GET['article'] . '/1'; ?>" target="_blank">View Article</a>		
				</p>

			</form>

			<?php if(!isset($_GET['new'])) : ?>
			
			<!-- Delete Article -->
			<form id="delete" name="deletearticleform" method="POST" action="<? $_SERVER ?>"> 
			
				<?remove_article()?>
				
				<p class="submit">
					<input onclick="var answer = confirm('Are you sure you want to delete the article?\n(This cannot be undone)'); if(!answer){ return false;}"  name="remove" type="submit" value="Delete Article" />
				</p>

			</form>		
			<?php endif; ?>
			
			</div>
			
			
			<div class="section">			
				<h2>Edit Article Pages</h2>
				<?get_pages()?>
			</div>
			
			<div class="section">
				<?new_page_link()?>			
			</div>				
				
			
		</div>

		<script type="text/javascript">		
		</script>
		
	</body>

</html>

