<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

require_once(JPATH_COMPONENT_ADMINISTRATOR.'/libs/HtmlHelper.php');

class HTML5FlippingBookViewControl_Panel extends JViewLegacy
{
	protected $config;
	//----------------------------------------------------------------------------------------------------
	function display($tpl = null) 
	{
		$document = JFactory::getDocument();
		$document->addScript(COMPONENT_JS_URL.'MethodsForXml.js');
		$document->addScript(COMPONENT_JS_URL.'MyAjax.js');
		
		HtmlHelper::addCss();
		
		$configurationModel = JModelLegacy::getInstance('Configuration', COMPONENT_MODEL_PREFIX);
		$this->config = $configurationModel->getConfig();

        $this->errors = $this->get('Errors');

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {

		HtmlHelper::showTitle(JText::_('COM_HTML5FLIPPINGBOOK_BE_SUBMENU_ABOUT'), '_about');
	}
}