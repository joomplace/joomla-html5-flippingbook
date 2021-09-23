<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

require_once(JPATH_COMPONENT_ADMINISTRATOR.'/libs/HtmlHelper.php');

class HTML5FlippingBookViewPages extends JViewLegacy
{
	protected $state;
	protected $items;
	protected $pagination;
	protected $numAllItems;
	protected $sidebar;
	protected $publicationOptions;
	//----------------------------------------------------------------------------------------------------
	function display($tpl = null)
	{
		$jinput = JFactory::getApplication()->input;
		
		$layout = $jinput->get('layout', 'default', 'STRING');
		
		HtmlHelper::addCss();
		HtmlHelper::getSidebarMenu($this);
		
		switch ($layout)
		{
			case 'default':
			{
				$this->state = $this->get('State');
				$this->items = $this->get('Items');
				$this->pagination = $this->get('Pagination');
				
				$pagesModel = $this->getModel();
				$this->numAllItems = $pagesModel->getAllItemsQuantity();
				
				JHtmlSidebar::setAction('index.php?option='.COMPONENT_OPTION.'&view='.$this->getName());
				
				$publicationsModel = JModelLegacy::getInstance('Publications', COMPONENT_MODEL_PREFIX);
				$this->PublicationOptions = $publicationsModel->getSelectOptions();
				
				JHtmlSidebar::addFilter(
					JText::_('COM_HTML5FLIPPINGBOOK_BE_SELECT_PUBLICATION'),
					'filter_publication_id',
					JHtml::_('select.options', $this->PublicationOptions, 'value', 'text', $this->state->get('filter.publication_id'))
					);
				
				$this->sidebar = JHtmlSidebar::render();

				break;
			}
			case 'multiupload':
			{
				$document = JFactory::getDocument();
				$document->addScript(COMPONENT_JS_URL.'BootstrapFormHelper.js');
				$document->addScript(COMPONENT_JS_URL.'BootstrapFormValidator.js');
				
				$publicationId = $jinput->get('pubId', 0, 'INT');
				
				$publicationsModel = JModelLegacy::getInstance('Publications', COMPONENT_MODEL_PREFIX);
				
				$this->PublicationOptions = $publicationsModel->getSelectOptions();
				array_unshift($this->PublicationOptions, JHTML::_('select.option', '0', JText::_('COM_HTML5FLIPPINGBOOK_BE_SELECT_PUBLICATION')));
				
				JForm::addFieldPath(JPATH_COMPONENT.'/models/fields');
				
				$this->form = JForm::getInstance('pages_multiupload', JPATH_COMPONENT.'/models/forms/'.'pages_multiupload.xml');
				
				$this->form->bind(array(
					'publication_id' => $publicationId,
					'general_pages_title' => JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_MULTIUPLOAD_GENERAL_PAGES_TITLE_VALUE'),
					));

				break;
			}
		}

		$this->addToolbar( $layout );
		parent::display($tpl);
	}

	protected function addToolbar( $layout ) {

		switch ($layout)
		{
			case 'default':
			{
				HtmlHelper::showTitle(JText::_('COM_HTML5FLIPPINGBOOK_BE_SUBMENU_PAGES'), '_pages');

				JToolBarHelper::addNew('page.add');
				JToolBarHelper::editList('page.edit');
				JToolBarHelper::deleteList(JText::_('COM_HTML5FLIPPINGBOOK_BE_GENERALCOFIRMATION'), 'pages.delete');

                $bar = JToolbar::getInstance('toolbar');
                $title = JText::_('JTOOLBAR_BATCH');
                $layout = new JLayoutFile('joomla.toolbar.batch');
                $dhtml = $layout->render(array('title' => $title));
                $bar->appendButton('Custom', $dhtml, 'batch');

				JToolbarHelper::custom('pages.show_multiupload', 'upload', 'upload', 'COM_HTML5FLIPPINGBOOK_BE_PAGES_MULTIUPLOAD', false);
				JToolbarHelper::spacer(20);
				JToolbarHelper::custom('pages.set_contents', 'list-view', 'contents', 'COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_CONTENTS', true);

				break;
			}
			case 'multiupload':
			{
				HtmlHelper::showTitle(JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_MULTIUPLOAD'), '');

				JToolbarHelper::custom('pages.multiupload', 'upload', 'upload', 'COM_HTML5FLIPPINGBOOK_BE_PAGES_MULTIUPLOAD_START', false);
				JToolBarHelper::cancel('page.cancel', 'JTOOLBAR_CANCEL');

				break;
			}
		}
	}
}