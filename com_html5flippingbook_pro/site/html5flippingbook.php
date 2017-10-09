<?php defined('_JEXEC') or die('Restricted access');
/**
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

$jinput = JFactory::getApplication()->input;

define('COMPONENT_OPTION', 'com_html5flippingbook');
define('COMPONENT_MODEL_PREFIX', 'HTML5FlippingBookModel');
define('COMPONENT_TABLE_PREFIX', 'HTML5FlippingBookTable');
define('COMPONENT_IMAGES_URL', JURI::root(true).'/components/'.COMPONENT_OPTION.'/assets/images/');
define('COMPONENT_IMAGES_PATH', JPATH_SITE.'/components/'.COMPONENT_OPTION.'/assets/images/');
define('COMPONENT_CSS_URL', JURI::root(true).'/components/'.COMPONENT_OPTION.'/assets/css/');
define('COMPONENT_JS_URL', JURI::root(true).'/components/'.COMPONENT_OPTION.'/assets/js/');
define('COMPONENT_ADMIN_JS_URL', JURI::root(true).'/administrator/components/'.COMPONENT_OPTION.'/assets/js/');
define('COMPONENT_MOBILE_LIB_URL', JURI::root(true).'/components/' . COMPONENT_OPTION . '/assets/mobile/');
define('COMPONENT_MOBILE_THEME_URL', COMPONENT_MOBILE_LIB_URL . 'themes/');
define('COMPONENT_LIBS_PATH', JPATH_SITE.'/components/'.COMPONENT_OPTION.'/libs/');
define('COMPONENT_MEDIA_PATH', JPATH_SITE .'/media/'.COMPONENT_OPTION);
define('COMPONENT_MEDIA_URL', '/media/'.COMPONENT_OPTION.'/');
define('COMPONENT_ITEM_ID', $jinput->get('Itemid', ''));

$lang = JFactory::getLanguage();

// Load component helper
JLoader::register('HTML5FlippingBookFrontHelper', JPATH_SITE . '/components/com_html5flippingbook/helpers/html5fbfront.php');

$lang->load(COMPONENT_OPTION, JPATH_ADMINISTRATOR, '');
$controller = JControllerLegacy::getInstance('HTML5FlippingBook');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();