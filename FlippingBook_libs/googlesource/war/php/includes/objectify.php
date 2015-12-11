<?php

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

if (!isset ($_GET['locale']))
	$langcode = manageLanguage();

/**
 * Deletes the passed in 'entity' from the datastore and increments the datastore Version number
*/

function delete_entity_increment_version($key, $class) {

	global $ofy, $versionclass, $articleclass;
	$version;

	if ($ofy->query($versionclass)->filter('id', '1')-> list ()->size() == 0) {
		$version = new Version('1', '0');
	} else {
		$version = $ofy->query($versionclass)->filter('id', '1')->get();
	}

	$ofy->delete($class, $key);	

	if($class == 'class com.fi.twentythings.Page') {
		$articlekey = substr($key, 0, strrpos($key, "|"));
		$article = $ofy->query($articleclass)->filter('id', $articlekey)->get();
		$article->setNumberOfPages($article->getNumberOfPages() - 1);
		$ofy->put($article);
	}
	
	$version->setVersion($version->getVersion() + 1);
	$ofy->put($version);
}

/**
 * Deletes the passed in 'entity' from the datastore and increments the datastore Version number
 */

function save_entity_increment_version($entity) {

	global $ofy, $versionclass, $articleclass;
	$version;

	if ($ofy->query($versionclass)->filter('id', '1')-> list ()->size() == 0) {
		$version = new Version('1', '0');
	} else {
		$version = $ofy->query($versionclass)->filter('id', '1')->get();
	}

	$ofy->put($entity);
	
	if($entity->getClass() == 'class com.fi.twentythings.Page') {
		$articlekey = substr($entity->getId(), 0, strrpos($entity->getId(), "|"));
		$article = $ofy->query($articleclass)->filter('id', $articlekey)->get();
		$article->setNumberOfPages($article->getNumberOfPages() + 1);
		$ofy->put($article);
	}
	
	$version->setVersion($version->getVersion() + 1);
	$ofy->put($version);
}

/**
 * gets the current version number from the datastore 
 */

function get_version_number() {
	global $ofy, $versionclass;
	$version = $ofy->query($versionclass)->filter('id', '1')->get();
	if ($version == null) {
		$version = new Version('1', '0');
		$ofy->put($version);
	}
	return $version->getVersion();
}

function get_version_no_echo() {
	global $ofy, $versionclass;
	$version = $ofy->query($versionclass)->filter('id', '1')->get();
	if ($version == null) {
		$version = new Version('1', '0');
		$ofy->put($version);
	}
	return $version->getVersion();
}

/**
 * determines the current locale based on cookie, query string or default values 
 */

function manageLanguage() {
	global $ofy, $loc, $localeclass, $langcode;

	// Valid language codes.
	$langPattern = '/^(en-US|en-GB|fr-FR|pt-BR|zh-CN|in-ID|pl-PL|cs-CZ|de-DE|es-419|es-ES|fil-PH|it-IT|ru-RU|ja-JP|zh-TW|nl-NL)$/i';

	if (isset ($_GET['language']) && preg_match($langPattern, $_GET['language'])) {
		// If requesting a language in URL, set cookie and continue to output that language.
		setcookie('language', $_GET['language'], pow(2, 31) - 1, '/');
		$loc = $ofy->query($localeclass)->filter('id', $_GET['language'])->get();
		$lang = $_GET['language'];
	} else {
		// No language requested in URL.
		if (isset ($_COOKIE['language'])) {
			// If cookie is set, redirect to that language.
			header('Location: http://'.$_SERVER['HTTP_HOST'].'/'.$_COOKIE['language'].$_SERVER['SCRIPT_URL']);
			$loc = $ofy->query($localeclass)->filter('id', $_COOKIE['language'])->get();
			$lang = $_COOKIE['language'];
		} else {
			// If no cookie is set, detect language, and redirect to resulting language.
			header('Location: http://'.$_SERVER['HTTP_HOST'].'/'.getBrowserLanguage().$_SERVER['SCRIPT_URL']);
			$loc = $ofy->query($localeclass)->filter('id', getBrowserLanguage())->get();
			$lang = getBrowserLanguage();
		}
	}
	return $lang;
}

/**
 * gets an array of the supported locales 
 */

function get_locales() {
	global $ofy, $localeclass;
	$locales = $ofy->query($localeclass)-> list ();
	$returnlocales = '';
	$index = 1;
	foreach ($locales as $locale) {
		$delim = $index < $locales->size() ? '|' : '';
		$returnlocales .= $locale->getId().$delim;
		$index++;
	}

	return explode('|', $returnlocales);
}

function get_display_locales() {
	global $ofy, $localeclass;
	$locales = $ofy->query($localeclass)-> list ();
	$returnlocales = '';
	$index = 1;
	foreach ($locales as $locale) {
		$delim = $index < $locales->size() ? ',' : '';
		$returnlocales .= $locale->getLOCALE_DISPLAY_NAME().'|'.$locale->getId().$delim;
		$index++;
	}

	return explode(',', $returnlocales);

}

function getBrowserLanguage() {

	// List of available translations. Populate this from datastore.
	// If multiple translations exist for same language (ie. en-US and en-GB),
	// then the first to occur in this array will take priority.
	$googleLangs = get_locales(); //array(		'fr',		'de',		'es',		'en-US',		'en-GB'	);
	// Array of user languages, in preference order.
	$userLangs = explode(",", $_SERVER['HTTP_ACCEPT_LANGUAGE']);
	// Strip out unused strings from $userLangs array.
	foreach ($userLangs as $i => $lang) {
		if (strpos($lang, ';')) {
			$userLangs[$i] = strtolower(substr($lang, 0, strpos($lang, ';')));
		}
	};

	// Find matches in order of userLang priority.
	foreach ($userLangs as $userLang) {
		$userLangFragment = strpos($userLang, '-') ? substr($userLang, 0, strpos($userLang, '-')) : $userLang;
		// First look for exact match (language-country).
		foreach ($googleLangs as $googleLang) {
			if (preg_match('/^'.$userLang.'$/i', $googleLang)) {
				$bestMatch = $googleLang;
				break;
			}
		}
		if ($bestMatch) {
			break;
		} else {
			// If no exact match, look for language-only match.
			foreach ($googleLangs as $googleLang) {
				$googleLangFragment = strpos($googleLang, '-') ? substr($googleLang, 0, strpos($googleLang, '-')) : $googleLang;
				if (preg_match('/^'.$userLangFragment.'$/i', $googleLangFragment)) {
					$bestMatch = $googleLang;
					break;
				}
			}
			if ($bestMatch)
				break;
		}
	};

	$returnlocale = $bestMatch ? $bestMatch : 'en-US';
	// Set default if no match.
	return $bestMatch ? $bestMatch : 'en-US';
}


?>