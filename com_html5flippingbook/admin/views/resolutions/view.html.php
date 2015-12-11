<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

require_once(JPATH_COMPONENT_ADMINISTRATOR.'/libs/HtmlHelper.php');

class HTML5FlippingBookViewResolutions extends JViewLegacy
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
		
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		
		$resolutionsModel = $this->getModel();
		
		$this->numAllItems = $resolutionsModel->getAllItemsQuantity();
		
		$this->sidebar = JHtmlSidebar::render();

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {

		HtmlHelper::showTitle(JText::_('COM_HTML5FLIPPINGBOOK_BE_SUBMENU_RESOLUTIONS'), '_resolutions');

		JToolBarHelper::addNew('resolution.add');
		JToolBarHelper::editList('resolution.edit');
		JToolBarHelper::deleteList(JText::_('COM_HTML5FLIPPINGBOOK_BE_GENERALCOFIRMATION'), 'resolutions.delete');
	}
}