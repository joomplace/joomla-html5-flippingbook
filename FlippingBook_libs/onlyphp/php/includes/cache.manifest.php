<?php header('Content-type: text/cache-manifest'); ?>
CACHE MANIFEST
<?php 
	require_once('../../locale/locale.php');
	$lang = $_GET['language'];
	//echo 'in cache manifest and lang is ' . $lang;
	require_once('../../locale/' . $lang . '/configuration.php');
	$versionNumber = get_version_number();
 	echo "#version " . $versionNumber;
?>


NETWORK:
*

CACHE:
<?php 
	require_once('../../locale/locale.php');
	foreach( OFFLINE_ASSETS as $asset ) {
		echo $asset . '?v='. $versionNumber . "\n";
	}
?>

