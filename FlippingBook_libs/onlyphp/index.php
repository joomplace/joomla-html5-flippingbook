<?php


header("Cache-Control: no-cache, must-revalidate");
header("Content-Type: text/html;charset=UTF-8");


require_once('php/libraries/browser.php');
require_once('locale/locale.php');
require_once('locale/en-US/configuration.php');


$browser = new Browser();
define( 'BROWSER_NAME', $browser->getBrowser() );
define( 'BROWSER_VERSION', $browser->getVersion() );


/**
 * Route to basic or advanced
 */
 
function is_basic() {
	if(	
		( BROWSER_NAME == Browser::BROWSER_CHROME && BROWSER_VERSION >= 5 ) ||
		( BROWSER_NAME == Browser::BROWSER_IE && BROWSER_VERSION >= 9 ) ||
		( BROWSER_NAME == Browser::BROWSER_FIREFOX && BROWSER_VERSION >= 3.6 ) ||
		( BROWSER_NAME == Browser::BROWSER_SAFARI && BROWSER_VERSION >= 4 ) ||
		( BROWSER_NAME == Browser::BROWSER_OPERA && BROWSER_VERSION >= 10 ) ||
		is_touchdevice() ||
		is_chromeframe()
		) {
		return false;
	} else {
		return true;
	}
}

/**
 * 
 */

function is_facebook(){
	return preg_match( "/facebook/is", $_SERVER["HTTP_USER_AGENT"] );
}

/**
 * 
 */
function is_chromeframe(){
	return preg_match( '/chromeframe/is', $_SERVER['HTTP_USER_AGENT'] );
}

/**
 * 
 */
function is_touchdevice(){
	return preg_match( '/iPad|iPhone|iPod|Android/', BROWSER_NAME );
}

/**
 * Determines if we are running on the live server (or locally)
 */
function is_live() {
	return preg_match( DEVELOPMENT_HOSTS_EXPRESSION, $_SERVER['SERVER_NAME']) ? false : true;
}

/**
 * Assembles body class
 * 
 */
function body_class() {
    global $langcode;
	$bodyClass = '';
	
	if( is_basic() ) {
		$bodyClass .= 'basic ';
	}
	
	if( isset($_GET['view']) && ($_GET['view'] == 'home' || $_GET['view'] == 'credits' || $_GET['view'] == 'tot' || $_GET['view'] == '404' ) ) {
		$bodyClass .= $_GET['view'] . ' ';
	}
	
	if( is_touchdevice() ) {
		$bodyClass .= 'touch-device ';
	}
	
	$bodyClass .= $langcode . ' ';
	
	return $bodyClass;
}


global $pages, $activePages, $articlesarray, $activearticlesarray, $pagesarray;
$pages = array(); 
$activePages = array();
$parsedPages = array();
$articlesarray = array();
$pagesarray = array();




function load_articles()
{

	global $contents, $templates, $pages, $activePages;

$con = mysql_connect("localhost","root","");
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  } 

mysql_select_db("war", $con);
$result = mysql_query("SELECT * FROM `Article`") or die(mysql_error());

while ($row = mysql_fetch_assoc($result)) {
    $title = $row["title"];
	$subtitle = $row["subtitle"];
	$numberofpages = $row["numberOfPages"];
	$active = $row["active"];
	$hidden = $row["hidden"];
	$order = $row["order"];
	$stub = $row["stub"];
	
	mysql_select_db("war", $con);
	$result_content = mysql_query("SELECT `content` FROM `Page` WHERE `stub`='$stub'") or die(mysql_error());
	$contents = mysql_fetch_array($result_content);

	mysql_select_db("war", $con);
 	$result_templates = mysql_query("SELECT `template` FROM `Page` WHERE `stub`='$stub'") or die(mysql_error());
 	$templates = mysql_fetch_array($result_templates);

 	$pages[$stub] = array(
		'numberOfPages' => $numberofpages,
		'title' => $title,
		'subtitle' => $subtitle,
		'active' => $active,
		'hidden' => $hidden,
		'order' => $order,
		'templates' => $templates,
		'contents' => $contents,
		'stub' => $stub);

}

mysql_free_result($result); mysql_free_result($result_content); mysql_free_result($result_templates);

mysql_select_db("war", $con);
$result = mysql_query("SELECT * FROM `Article` WHERE `active`=1") or die(mysql_error());

while ($row = mysql_fetch_assoc($result)) {
	$title = $row["title"];
	$subtitle = $row["subtitle"];
	$numberofpages = $row["numberOfPages"];
	$active = $row["active"];
	$hidden = $row["hidden"];
	$order = $row["order"];
	$stub = $row["stub"];

	mysql_select_db("war", $con);
	$result_content = mysql_query("SELECT `content` FROM `Page` WHERE `stub`='$stub'") or die(mysql_error());
	$contents = mysql_fetch_array($result_content);

	mysql_select_db("war", $con);
 	$result_templates = mysql_query("SELECT `template` FROM `Page` WHERE `stub`='$stub'") or die(mysql_error());
 	$templates = mysql_fetch_array($result_templates);

 	$activePages[$stub] = array(
		'numberOfPages' => $numberofpages,
		'title' => $title,
		'subtitle' => $subtitle,
		'active' => $active,
		'hidden' => $hidden,
		'order' => $order,
		'templates' => $templates,
		'contents' => $contents,
		'stub' => $stub);

}

mysql_free_result($result); mysql_free_result($result_content); mysql_free_result($result_templates);
mysql_close($con);
}

load_articles();

/**
 * Set up page counters
 * 
 */
$totalNumberOfPages = 1;
foreach ($pages as $key => $value) {
	$pages[$key]['globalStartPage'] = $totalNumberOfPages;
	$totalNumberOfPages += $pages[$key]['numberOfPages'];
	$pages[$key]['globalEndPage'] = $totalNumberOfPages - 1;
}


/**
 * Get page query params
 * 
 */

$currentView = isset($_GET['view']) ? $_GET['view'] : 'home';
$currentArticle = isset($_GET['article']) ? $_GET['article'] : null;
$currentArticlePage = isset($_GET['page']) ? $_GET['page'] : '1';	

//print_r($currentView);die();
/**
 * Return stub of next/previous article
 * 
 */
function nextPrevArticleName($order) {
	global $activePages, $currentArticle;  //
	$keys = array_keys($activePages); 
	$position = array_search($currentArticle, $keys); 
	if (isset($keys[$position + 1]) || isset($keys[$position - 1])) { 
		$nextPrevArticleName = $order == 'next' ? $keys[$position + 1] : $keys[$position - 1];
	}
	return $nextPrevArticleName;
}


/**
 * Return url of next page
 * 
 */
function nextPage() {
	global $activePages, $currentView, $currentArticle, $currentArticlePage;
  $root = '/en-US/';
	
	// If we're on the home page, the next button should point to the first article
	if( $currentView == 'home' ) {
		return $root . getArrayFirstIndex( $activePages );
	}
	// If we're on the credits page, the next button should be inactive
	else if( $currentView == 'credits' ) {
		return false;
	}
	// If we're on the last page, send to credits
	else if( $currentArticle == 'theend' ) {
		return $root . 'credits';
	}
	// If we're on a regular article page get the next one
	if($activePages[$currentArticle]['numberOfPages'] == $currentArticlePage) {
		if( !$activePages[nextPrevArticleName('next')]['active'] ) return $root . 'theend';
		return $root . nextPrevArticleName('next');
	} elseif($activePages[$currentArticle]['numberOfPages'] > $currentArticlePage) {
		return $root . $currentArticle . '/' . (string)($currentArticlePage+1);
	}
}

//var_dump($currentView); die();
/**
 * Return url of next page
 * 
 */
function prevPage() {
	global $activePages, $currentView, $currentArticle, $currentArticlePage;
	
	$root = '/en-US/';
	
	// If we're on the home page, the prev button should be inactive
	if( $currentView == 'home' ) {
		return false;
	}
	// If we're on the credits page, the prev button should point to the last article
	else if( $currentView == 'credits' ) {
		return $root . getArrayLastIndex( $activePages );
	}
	$nextPrevArticleName = nextPrevArticleName('prev');
	if( empty($nextPrevArticleName) && $currentArticlePage == '1' ){
		return $root;
	} elseif( $currentArticlePage == '1' ) {
		return $root . nextPrevArticleName('prev') . '/' . $activePages[nextPrevArticleName('prev')]['numberOfPages'];
	} elseif($activePages[$currentArticle]['numberOfPages'] >= $currentArticlePage) {
		return $root . $currentArticle . '/' . (string)($currentArticlePage-1);
	} else {
		return $root . $currentArticle;		
	}
}

/**
 * 404 Handling
 * 
 */
if( isset($_GET['article']) && !$activePages[$_GET['article']] ) {
	header('Location: /');
}
if( $currentView == '404' ) {
	header("HTTP/1.0 404 Not Found");
}

/**
 * Checks if it's a 'stub'
 * 
 */
function isStub() {
	return isset($_GET['mode']) && ($_GET['mode'] == 'stub' || $_GET['mode'] == 'all');

}

/**
 * Checks if it's a print page
 * 
 */
function isPrintPage() {
	return isset($_GET['mode']) && ($_GET['mode'] == 'print' || $_GET['mode'] == 'printAll') ;
}

/**
 * Include header file
 * 
 */
if(isPrintPage()) {
	require_once('php/includes/header_print.php');
} elseif(!isStub()) {
	require_once('php/includes/header.php');
} 

/**
 * Spit our the right type of content depending on what
 * parameters were set in the request.
 */

// Are we printing a specific page of an article?
if(isset($_GET['article']) && !isset($_GET['page']) && isPrintPage()) {
	foreach($pages as $name => $value) {
		if( $name == $_GET['article'] ) {		
			echo isPrintPage() ? '<div id="pages">' : '<article id="'.$name.'">';
			
				for($i=1; $i<=$value['numberOfPages']; $i++) {
					$templates = $value['templates'];
					
					if ($i == 1) {
						echo '<div class="page-title">';
						echo '<h2>' . $value['title'] . '</h2>';
						if(strlen($value['subtitle'])) echo '<h3>' . $value['subtitle'] . '</h3>';
							echo '</div>';
					}
					
					echo '<section class="' . $templates[$i-1] . ' title-' . $value['stub'] . ' page-' . $i . '"><div class="page">';
					$txt = $value['contents'];
				    echo html_entity_decode($txt[$i-1]);				    
				    echo '</div></section>';
				}
				
			echo isPrintPage() ? '</div>' : '</article>';
			
		}
	}
} 

// If the request is for an article, the markup returned should
// be the first page of that article
elseif(isset($_GET['article'])) {
	$thisArticle = $pages[$currentArticle];
	echo '<section class="' . $thisArticle['templates'][$currentArticlePage-1] . ' title-' . $thisArticle['stub'] . ' page-' . $currentArticlePage . '"><div class="page">';
	if ($currentArticlePage == 1) {
		echo '<div class="page-title">';
		echo '<h2>' . $thisArticle['title'] . '</h2>';
		if(strlen($value['subtitle'])) echo '<h3>' . $thisArticle['subtitle'] . '</h3>';
		echo '</div>';
	}
	echo html_entity_decode($thisArticle['contents'][$currentArticlePage-1]);
	echo '</div></section>';	
} 


// If the requested mode is set to all, we return a concatenated
// version of all articles and pages
elseif(isset($_GET['mode']) && $_GET['mode'] == 'all') {
	 // Include the flexible cross origin policy for this request
	// include_once( 'php/includes/cors.php' );
	
	foreach($pages as $name => $value) {
		if( $value['active'] ) {
			echo '<article id="'.$name.'">';
				foreach($value['contents'] as $key => $content) {
					echo '<section class="' . $value['templates'][$key] . ' title-' . $value['stub'] . ' page-' . ($key+1) . '"><div class="page">';
					if ($key == 0) {
						echo '<div class="page-title">';
						echo '<h2>' . $value['title'] . '</h2>';
						if(strlen($value['subtitle'])) echo '<h3>' . $value['subtitle'] . '</h3>';
						echo '</div>';
					}
					echo html_entity_decode($content);
					echo '</div></section>';
				}
			echo '</article>';
		}
	}
} 

// This means we're printing the whole book
elseif(isPrintPage()) {
foreach($pages as $name => $value) {
			echo isPrintPage() ? '<div id="pages">' : '<article id="'.$name.'">';
			
				for($i=1; $i<=$value['numberOfPages']; $i++) {
					$templates = $value['templates'];
					
					if ($i == 1) {
						echo '<div class="page-title">';
						echo '<h2>' . $value['title'] . '</h2>';
						if(strlen($value['subtitle'])) echo '<h3>' . $value['subtitle'] . '</h3>';
							echo '</div>';
					}
					
					echo '<section class="' . $templates[$i-1] . ' title-' . $value['stub'] . ' page-' . $i . '"><div class="page">';
					$txt = $value['contents'];
				    echo html_entity_decode($txt[$i-1]);				    
				    echo '</div></section>';
				}
				
			echo isPrintPage() ? '</div>' : '</article>';
			
	}
}

/**
 * Include footer file
 * 
 */
if(isPrintPage()) {	
	require_once('php/includes/footer_print.php');
	
} elseif(!isStub()) {

	require_once('php/includes/content.php');
	
	if(isset($_GET['article'])) echo '<div id="articleId">' . $_GET['article'] . '</div>';
	if(isset($_GET['page'])) echo '<div id="pageNumber">' . $_GET['page'] . '</div>';
	
	$prevClass = !prevPage() ? 'inactive' : '';
	$nextClass = !nextPage() ? 'inactive' : '';
	
	?>
	<div id="pagination-prev" class="<?php echo $prevClass; ?>"><a href="<?php echo prevPage(); ?>"><div class="arrow"><?php echo LOCALE_PREVIOUS_PAGE; ?></div></a></div>
	<div id="pagination-next" class="<?php echo $nextClass; ?>"><a href="<?php echo nextPage(); ?>"><div class="arrow"><?php echo LOCALE_NEXT_PAGE; ?></div></a></div>
	<?php
	
	require_once('php/includes/table-of-things.php');
	require_once('php/includes/footer.php');
}


/**
 * Returns the id of the first item in an 
 * associative array.
 */
function getArrayFirstIndex($arr) {
	foreach ($arr as $key => $value)
	return $key;
}


/**
 * Returns the id of the last item in an 
 * associative array.
 */
function getArrayLastIndex($arr) {
	$result = '';
	foreach ($arr as $key => $value) {
		$result = $key;
	}
	return $result;
}


?>