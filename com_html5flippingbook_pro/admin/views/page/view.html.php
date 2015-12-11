<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

require_once(JPATH_COMPONENT_ADMINISTRATOR.'/libs/HtmlHelper.php');

class HTML5FlippingBookViewPage extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;
	protected $imagesSubdirRelativeName;
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

		$publicationModel = JModelLegacy::getInstance('Publication', COMPONENT_MODEL_PREFIX);
		$publication = $publicationModel->getItem($this->item->publication_id);
		
		$this->imagesSubdirRelativeName = 'media/'.COMPONENT_OPTION.'/images';
		
		if ($publication->c_imgsub == 1 && $publication->c_imgsubfolder != '') $this->imagesSubdirRelativeName .= '/'.$publication->c_imgsubfolder;

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {

		if ( !empty($this->item->page_title) )	$additionTitle = ' ( '.$this->item->page_title.' )';
		HtmlHelper::showTitle(($this->item->id == 0 ? JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_ADD') : JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_EDIT')) . @$additionTitle, '_pages');

		JToolBarHelper::apply('page.apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('page.save', 'JTOOLBAR_SAVE');
		JToolBarHelper::custom('page.saveandnew', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		JToolBarHelper::cancel('page.cancel', 'JTOOLBAR_CANCEL');
	}
}