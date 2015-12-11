<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

require_once(JPATH_COMPONENT_ADMINISTRATOR.'/libs/HtmlHelper.php');

class HTML5FlippingBookViewCategories extends JViewLegacy
{
	protected $state;
	protected $items;
	protected $pagination;
	protected $numAllItems;
	//----------------------------------------------------------------------------------------------------
	public function display($tpl = null) 
	{
		HtmlHelper::addCss();
		HtmlHelper::getSidebarMenu($this);
		$this->sidebar = JHtmlSidebar::render();

		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		
		$categoriesModel = $this->getModel();
		
		$this->numAllItems = $categoriesModel->getAllItemsQuantity();
		
		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {

		HtmlHelper::showTitle(JText::_('COM_HTML5FLIPPINGBOOK_BE_SUBMENU_CATEGORIES'), '_categories');

		JToolBarHelper::addNew('category.add');
		JToolBarHelper::editList('category.edit');
		JToolBarHelper::deleteList(JText::_('COM_HTML5FLIPPINGBOOK_BE_GENERALCOFIRMATION'), 'categories.delete');
	}
}