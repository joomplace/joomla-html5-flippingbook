<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

define('COMPONENT_OPTION', 'com_html5flippingbook');
define('COMPONENT_MODEL_PREFIX', 'HTML5FlippingBookModel');
define('COMPONENT_TABLE_PREFIX', 'HTML5FlippingBookTable');
define('COMPONENT_ASSETS_URL', JURI::root().'administrator/components/'.COMPONENT_OPTION.'/assets/');
define('COMPONENT_ASSETS_URL_FRONT', JURI::root().'components/'.COMPONENT_OPTION.'/assets/');
define('COMPONENT_IMAGES_URL', JURI::root().'administrator/components/'.COMPONENT_OPTION.'/assets/images/');
define('COMPONENT_CSS_URL', JURI::root().'administrator/components/'.COMPONENT_OPTION.'/assets/css/');
define('COMPONENT_JS_URL', JURI::root().'administrator/components/'.COMPONENT_OPTION.'/assets/js/');
define('COMPONENT_MEDIA_PATH', JPATH_SITE.'/media/'.COMPONENT_OPTION);
define('COMPONENT_MEDIA_URL', JURI::root().'/media/'.COMPONENT_OPTION.'/images/');
define('COMPONENT_FORMS_PATH', JPATH_COMPONENT_ADMINISTRATOR.'/models/forms');
define('COMPONENT_FIELDS_PATH', JPATH_COMPONENT_ADMINISTRATOR.'/models/fields');

$lang = JFactory::getLanguage();
$lang->load(COMPONENT_OPTION, JPATH_ADMINISTRATOR);

$controller = JControllerLegacy::getInstance('HTML5FlippingBook');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();