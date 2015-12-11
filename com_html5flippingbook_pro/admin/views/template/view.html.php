<?php defined('_JEXEC') or die('Restricted Access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

require_once(JPATH_COMPONENT_ADMINISTRATOR.'/libs/HtmlHelper.php');

class HTML5FlippingBookViewTemplate extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;
	protected $image;
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
		$this->image = $this->get('Image');

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {

		if ( !empty($this->item->template_name) )	$additionTitle = ' ( '.$this->item->template_name.' )';
		HtmlHelper::showTitle(($this->item->id == 0 ? JText::_('COM_HTML5FLIPPINGBOOK_BE_TEMPLATES_ADD') : JText::_('COM_HTML5FLIPPINGBOOK_BE_TEMPLATES_EDIT')) . @$additionTitle, '_templates');

		JToolBarHelper::apply('template.apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('template.save', 'JTOOLBAR_SAVE');
		JToolBarHelper::custom('template.save2new', 'save-new', 'save-new_f2', 'JTOOLBAR_SAVE_AND_NEW', false);
		JToolBarHelper::custom('template.save2copy', 'save-copy', 'save-copy_f2', 'JTOOLBAR_SAVE_AS_COPY', false);
		JToolBarHelper::cancel('template.cancel', 'JTOOLBAR_CANCEL');
	}
}