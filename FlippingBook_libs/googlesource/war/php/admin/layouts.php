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
 
session_start();
require_once('../libraries/browser.php');

/**
 * Java imports
 * 
 */
import com.google.appengine.api.datastore.Query;
import com.google.appengine.api.datastore.DatastoreServiceFactory;

$pages = array(); $activePages = array();
function load_articles()
{
	global $pages, $activePages;
	$service = DatastoreServiceFactory::getDatastoreService();
	$query = new Query('article')->addSort('order');
	$prepared = $service->prepare($query)->asIterable();

	foreach ($prepared as $article) {
		$stub = $article->getProperty('stub');
		$title = $article->getProperty('title');
		$subtitle = $article->getProperty('subtitle');
		$numberofpages = $article->getProperty('numberOfPages');
		$active = $article->getProperty('active');
		$pages[$stub] = array(
			'numberOfPages' => $numberofpages,
			'title' => $title,
			'subtitle' => $subtitle,
			'active' => $active
		);
		if($article->getProperty('active')) {
			$activePages[$stub] = array(
				'numberOfPages' => $numberofpages,
				'title' => $title,
				'subtitle' => $subtitle,
				'active' => $active
			);
		}
	}	
}
load_articles();
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
		<link type="text/css" href="/css/reset.css" rel="stylesheet" media="screen" />
		<link type="text/css" href="/css/main.css" rel="stylesheet" media="screen" />
		<link type="text/css" href="/css/print.css" rel="stylesheet" media="print" />
		<link type="text/css" href="/css/layouts.css" rel="stylesheet" media="screen" />
		<link type="text/css" href="/css/illustrations.css" rel="stylesheet" media="screen" />
		<style type="text/css">
			html, body {
				background-color: #333;
			}
			
			#pages {
				width: 100%;
			}
			
			#pages section {
				margin: 0 5px 5px 5px;
				position: relative;
				display: inline-block;
			}
			div.spacer {
				margin: 100px 0;
			}
		</style>
	</head>
	<body>
		<?php
		echo '<div id="pages">';
		foreach($pages as $name => $value) {
			for($i=1; $i<=$value['numberOfPages']; $i++) {
				require_once('../../pages/'.$name.'-'.$i.'.html');
			}
			echo '<div class="spacer"></div>';
		}
		echo '</div>';
		?>
		<script type="text/javascript" src="js/thirdparty/jquery.1.4.2.min.js"></script>
		<script type="text/javascript">
			$("div.page").find('img').each(function(){
				$(this).attr( 'src', $(this).attr('data-src') );
			});
		</script>
	</body>
</html>