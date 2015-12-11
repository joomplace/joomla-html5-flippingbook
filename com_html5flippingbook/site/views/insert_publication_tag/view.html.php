<?php defined('_JEXEC') or die('Restricted access');
/**
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

require_once(JPATH_COMPONENT_ADMINISTRATOR.'/libs/HtmlHelper.php');

class HTML5FlippingBookViewInsert_Publication_Tag extends JViewLegacy
{
	//----------------------------------------------------------------------------------------------------
	function display($tpl = null)
	{
		$document = JFactory::getDocument();
		$document->addScript(COMPONENT_ADMIN_JS_URL.'BootstrapFormHelper.js');
		$document->addScript(COMPONENT_ADMIN_JS_URL.'BootstrapFormValidator.js');
		
		HtmlHelper::addCss();
		
		JForm::addFieldPath(JPATH_COMPONENT_ADMINISTRATOR.'/models/fields');
		
		$this->form = JForm::getInstance('select_publication_modal', JPATH_COMPONENT_ADMINISTRATOR.'/models/forms/'.'select_publication_modal.xml');
		
		parent::display($tpl);
	}
}