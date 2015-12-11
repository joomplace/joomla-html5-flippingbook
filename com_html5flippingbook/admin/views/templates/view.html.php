<?php defined('_JEXEC') or die('Restricted Access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

require_once(JPATH_COMPONENT_ADMINISTRATOR.'/libs/HtmlHelper.php');

class HTML5FlippingBookViewTemplates extends JViewLegacy
{
	protected $state;
	protected $items;
	protected $pagination;
	//----------------------------------------------------------------------------------------------------
	function display($tpl = null)
	{
		HtmlHelper::addCss();
		HtmlHelper::getSidebarMenu($this);
		
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		
		$templatesModel = $this->getModel();
		
		$this->numAllItems = $templatesModel->getAllItemsQuantity();
		
		$this->sidebar = JHtmlSidebar::render();

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {

		HtmlHelper::showTitle(JText::_('COM_HTML5FLIPPINGBOOK_BE_SUBMENU_TEMPLATES'), '_templates');

		JToolBarHelper::addNew('template.add');
		JToolBarHelper::editList('template.edit');
		JToolBarHelper::deleteList(JText::_('COM_HTML5FLIPPINGBOOK_BE_GENERALCOFIRMATION'), 'templates.delete');
	}
}