<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

require_once(JPATH_COMPONENT_ADMINISTRATOR.'/libs/HtmlHelper.php');

class HTML5FlippingBookViewCategory extends JViewLegacy
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
		$document->addScript(COMPONENT_JS_URL.'GeneralHelper.js');
		
		$this->state = $this->get('State');
		$this->item = $this->get('Item');
		$this->form = $this->get('Form');


		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {

		if ( !empty($this->item->c_category) )	$additionTitle = ' ( '.$this->item->c_category.' )';
		HtmlHelper::showTitle( ($this->item->c_id == 0 ? JText::_('COM_HTML5FLIPPINGBOOK_BE_CATEGORIES_ADD') : JText::_('COM_HTML5FLIPPINGBOOK_BE_CATEGORIES_EDIT')) . @$additionTitle, '_categories');

		JToolBarHelper::apply('category.apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('category.save', 'JTOOLBAR_SAVE');
		JToolBarHelper::custom('category.save2new', 'save-new', 'save-new_f2', 'JTOOLBAR_SAVE_AND_NEW', false);
		JToolBarHelper::custom('category.save2copy', 'save-copy', 'save-copy_f2', 'JTOOLBAR_SAVE_AS_COPY', false);
		JToolBarHelper::cancel('category.cancel', 'JTOOLBAR_CANCEL');
	}
}