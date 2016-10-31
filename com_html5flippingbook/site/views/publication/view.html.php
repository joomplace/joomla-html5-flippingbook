<?php defined('_JEXEC') or die('Restricted access');
/**
 * HTML5FlippingBook Component
 * @package HTML5FlippingBook
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
//TODO: AJAX LOADER

require_once(JPATH_COMPONENT_ADMINISTRATOR.'/libs/VarsHelper.php');

class HTML5FlippingBookViewPublication extends JViewLegacy
{
	protected $itemId = null;
	protected $item = null;
	protected $state = null;
	protected $layout;
	protected $tmplIsComponent = false;
	protected $basePath = '';
	protected $menuItemParams = null;
	protected $publicationId = null;
	protected $showDescriptionFirst = null;
	protected $config;
	protected $user;
	protected $isSearch;

	//----------------------------------------------------------------------------------------------------
	function display($tpl = null){

		$item = $this->get('Item');
		$item->resolutions = $this->get('Resolutions');
		$doc = JFactory::getDocument();

		$this->item = $item;

		$doc->setMetaData( 'og:url', JURI::current() );
		if ($this->item->opengraph_title) {
			$doc->setMetaData( 'og:title', $this->item->opengraph_title );
		}

		if ($this->item->opengraph_image) {
			$doc->setMetaData( 'og:image', JURI::root().'media/com_html5flippingbook/thumbs/'.$this->item->opengraph_image );
		}

		if ($this->item->opengraph_description) {
			$doc->setMetaData( 'og:description', $this->item->opengraph_description );
		}

		require_once(JPATH_COMPONENT_ADMINISTRATOR.'/models/configuration.php');
		$configurationModel = JModelLegacy::getInstance('Configuration', COMPONENT_MODEL_PREFIX);
		$this->config = $configurationModel->GetConfig();

		$this->setLayout('iframe');

		parent::display($tpl);

	}

}