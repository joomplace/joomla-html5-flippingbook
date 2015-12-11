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
	protected $form;
	protected $is_imagemg;
	protected $PublicationOptions;
	//----------------------------------------------------------------------------------------------------
	function display($tpl = null)
	{
		$jinput = JFactory::getApplication()->input;
		
		$layout = $jinput->get('layout', 'default', 'STRING');
		$app = JFactory::getApplication();

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
			case 'multiupload': case 'convert':
			{
				$document = JFactory::getDocument();
				$document->addScript(COMPONENT_JS_URL.'BootstrapFormHelper.js');
				$document->addScript(COMPONENT_JS_URL.'BootstrapFormValidator.js');
				
				$publicationId = $jinput->get('pubId', 0, 'INT');
				
				$publicationsModel = JModelLegacy::getInstance('Publications', COMPONENT_MODEL_PREFIX);
				
				$this->PublicationOptions = $publicationsModel->getSelectOptions();
				array_unshift($this->PublicationOptions, JHTML::_('select.option', '0', JText::_('COM_HTML5FLIPPINGBOOK_BE_SELECT_PUBLICATION')));
				
				JForm::addFieldPath(JPATH_COMPONENT.'/models/fields');
				
				$this->form = JForm::getInstance('pages_' . $layout, JPATH_COMPONENT.'/models/forms/'.'pages_' . $layout . '.xml');
				
				$this->form->bind(array(
					'publication_id' => $publicationId,
					'general_pages_title' => JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_MULTIUPLOAD_GENERAL_PAGES_TITLE_VALUE'),
					));

				if ($layout == 'convert')
				{
					$this->is_imagemg = FALSE;
					if (class_exists("Imagick"))
					{
						$this->is_imagemg = TRUE;
						$ext_v = phpversion('imagick');
						$imagick = new Imagick();
						$cls_v = $imagick->getVersion();
						preg_match('/ImageMagick ([0-9]+\.[0-9]+\.[0-9]+)/', $cls_v['versionString'], $cls_v);

						if (version_compare($cls_v[1], '6.5.4') == -1)
						{
							$this->is_imagemg = FALSE;
							$app->enqueueMessage(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PAGES_CONVERT_IMAGICK_CLASS_V_ERROR', $cls_v[1]), 'ERROR');
						}

						if (version_compare($ext_v, '3.1.2') == -1)
						{
							$this->is_imagemg = FALSE;
							$app->enqueueMessage(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PAGES_CONVERT_IMAGICK_PHPEXT_V_ERROR', $ext_v), 'ERROR');
						}
					}
					else
					{
						$app->enqueueMessage(JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_CONVERT_IMAGICK_INSTALL_ERROR'), 'WARNING');
					}
				}
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

				JToolbarHelper::spacer(20);

				JToolbarHelper::custom('pages.set_contents', 'list-view', 'contents', 'COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_CONTENTS', true);
				JHtml::_('bootstrap.modal', 'collapseModal');
				$title = JText::_('JTOOLBAR_BATCH');
				$dhtml = "<button data-toggle=\"modal\" data-target=\"#collapseModal\" class=\"btn btn-small\">
					<i class=\"icon-checkbox-partial\" title=\"$title\"></i>
					$title</button>";
				JToolBar::getInstance('toolbar')->appendButton('Custom', $dhtml, 'batch');

				JToolbarHelper::spacer(20);

				JToolbarHelper::custom('pages.show_multiupload', 'upload', 'upload', 'COM_HTML5FLIPPINGBOOK_BE_PAGES_MULTIUPLOAD', false);
				JToolbarHelper::custom('pages.show_convert', 'images', 'images', 'COM_HTML5FLIPPINGBOOK_BE_PAGES_CONVERT', false);
				break;
			}
			case 'multiupload':
			{
				HtmlHelper::showTitle(JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_MULTIUPLOAD'), '');

				JToolbarHelper::custom('pages.multiupload', 'upload', 'upload', 'COM_HTML5FLIPPINGBOOK_BE_PAGES_MULTIUPLOAD_START', false);
				JToolBarHelper::cancel('page.cancel', 'JTOOLBAR_CANCEL');

				break;
			}
			case 'convert':
			{
				HtmlHelper::showTitle(JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_CONVERT'), '');

				JToolbarHelper::custom('pages.convert', 'images', 'images', 'COM_HTML5FLIPPINGBOOK_BE_PAGES_CONVERT_PDF_START', false);
				JToolBarHelper::cancel('page.cancel', 'JTOOLBAR_CANCEL');

				break;
			}
		}
	}
}