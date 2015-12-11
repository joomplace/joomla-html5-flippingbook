<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

require_once(JPATH_COMPONENT_ADMINISTRATOR.'/libs/HtmlHelper.php'); 

class HTML5FlippingBookViewPublications extends JViewLegacy
{
	protected $state;
	protected $items;
	protected $pagination;
	protected $numAllItems;
	protected $sidebar;
	protected $categoryOptions;
	//----------------------------------------------------------------------------------------------------
	function display($tpl = null)
	{
		HtmlHelper::addCss();
		HtmlHelper::getSidebarMenu($this);
		
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		
		$publicationsModel = $this->getModel();
		$this->numAllItems = $publicationsModel->getAllItemsQuantity();
		
		JHtmlSidebar::setAction('index.php?option='.COMPONENT_OPTION.'&view='.$this->getName());
		
		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_published',
			JHtml::_('select.options', HtmlHelper::getPublishedOptions(), 'value', 'text', $this->state->get('filter.published'), true)
			);
		
		$categoriesModel = JModelLegacy::getInstance('Categories', COMPONENT_MODEL_PREFIX);
		$this->categoryOptions = $categoriesModel->getSelectOptions();
		
		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_CATEGORY'),
			'filter_category_id',
			JHtml::_('select.options', $this->categoryOptions, 'value', 'text', $this->state->get('filter.category_id'))
			);
		
		$this->sidebar = JHtmlSidebar::render();

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {

		HtmlHelper::showTitle(JText::_('COM_HTML5FLIPPINGBOOK_BE_SUBMENU_PUBLICATIONS'), '_publications');

		JToolBarHelper::addNew('publication.add');
		JToolBarHelper::editList('publication.edit');
		JToolBarHelper::deleteList(JText::_('COM_HTML5FLIPPINGBOOK_BE_GENERALCOFIRMATION'), 'publications.delete');
		JToolbarHelper::publish('publications.publish', 'JTOOLBAR_PUBLISH', true);
		JToolbarHelper::unpublish('publications.unpublish', 'JTOOLBAR_UNPUBLISH', true);

		JHtml::_('bootstrap.modal', 'collapseModal');
		$title = JText::_('JTOOLBAR_BATCH');
		$dhtml = "<button data-toggle=\"modal\" data-target=\"#collapseModal\" class=\"btn btn-small\">
			<i class=\"icon-checkbox-partial\" title=\"$title\"></i>
			$title</button>";
		JToolBar::getInstance('toolbar')->appendButton('Custom', $dhtml, 'batch');
	}
}