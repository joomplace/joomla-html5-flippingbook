<?php defined('_JEXEC') or die('Restricted Access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

require_once(JPATH_COMPONENT_ADMINISTRATOR.'/libs/HtmlHelper.php');

class HTML5FlippingBookViewResolution extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;
	//----------------------------------------------------------------------------------------------------
	public function display($tpl = null) 
	{
		HtmlHelper::addCss();
		HtmlHelper::getSidebarMenu($this);
		$this->sidebar = JHtmlSidebar::render();

		$document = JFactory::getDocument();
		$document->addScript(COMPONENT_JS_URL.'BootstrapFormHelper.js');
		$document->addScript(COMPONENT_JS_URL.'BootstrapFormValidator.js');

		$this->state = $this->get('State');
		$this->item = $this->get('Item');
		$this->form = $this->get('Form');

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {

		if ( !empty($this->item->resolution_name) )	$additionTitle = ' ( '.$this->item->resolution_name.' )';
		HtmlHelper::showTitle(($this->item->id == 0 ? JText::_('COM_HTML5FLIPPINGBOOK_BE_RESOLUTIONS_ADD') : JText::_('COM_HTML5FLIPPINGBOOK_BE_RESOLUTIONS_EDIT')) . @$additionTitle, '_resolutions');

		JToolBarHelper::apply('resolution.apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('resolution.save', 'JTOOLBAR_SAVE');
		JToolBarHelper::custom('resolution.save2new', 'save-new', 'save-new_f2', 'JTOOLBAR_SAVE_AND_NEW', false);
		JToolBarHelper::custom('resolution.save2copy', 'save-copy', 'save-copy_f2', 'JTOOLBAR_SAVE_AS_COPY', false);
		JToolBarHelper::cancel('resolution.cancel', 'JTOOLBAR_CANCEL');
	}
}