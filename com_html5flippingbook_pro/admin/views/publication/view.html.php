<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

jimport('joomla.filesystem.folder');

require_once(JPATH_COMPONENT_ADMINISTRATOR.'/libs/HtmlHelper.php');

class HTML5FlippingBookViewPublication extends JViewLegacy
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

		if ( !empty($this->item->c_title) )	$additionTitle = ' ( '.$this->item->c_title.' )';
		HtmlHelper::showTitle(($this->item->c_id == 0 ? JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_ADD') : JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_EDIT')) . @$additionTitle, '_publications');

		JToolBarHelper::apply('publication.apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('publication.save', 'JTOOLBAR_SAVE');
		JToolBarHelper::cancel('publication.cancel', 'JTOOLBAR_CANCEL');
	}
}