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

        if(!$item){
            throw new \Exception(\JText::_('COM_HTML5FLIPPINGBOOK_FE_PUBLICATION_NOT_FOUND'), 404);
        }

        $app        = JFactory::getApplication();
        $this->user = JFactory::getUser();

        $viewAccessGranted = $this->user->authorise('core.view', COMPONENT_OPTION . '.publication.' . $item->c_id);
        if(!$viewAccessGranted){
            $app->enqueueMessage(JText::_('JERROR_LAYOUT_YOU_HAVE_NO_ACCESS_TO_THIS_PAGE'), 'error');
            $app->setHeader('status', 403, true);
            return;
        }

		$item->resolutions = $this->get('Resolutions');
		$doc = JFactory::getDocument();

		$this->item = $item;


        $Itemid = $app->input->getInt('Itemid', 0);
        $menuitem = $app->getMenu()->getItem($Itemid);
        $menuparams = $menuitem->params;
        if(!empty($menuparams)) {
            $menu_meta_description = $menuparams->get('menu-meta_description');
            $menu_meta_keywords = $menuparams->get('menu-meta_keywords');
        }

        if(!empty($menu_meta_description)) {
            $doc->setDescription($menu_meta_description);
        } else {
            if (!empty($this->item->c_metadesc)) {
                $doc->setDescription($this->item->c_metadesc);
            }
        }

        if(!empty($menu_meta_keywords)) {
            $doc->setMetaData('keywords', $menu_meta_keywords);
        } else {
            if (!empty($this->item->c_metakey)) {
                $doc->setMetaData('keywords', $this->item->c_metakey);
            }
        }

		$doc->setMetaData('og:url', JURI::current());

		if (!empty($this->item->opengraph_title)) {
			$doc->setMetaData( 'og:title', $this->item->opengraph_title );
		}

		if (!empty($this->item->opengraph_image)) {
			$doc->setMetaData( 'og:image', JURI::root().'media/com_html5flippingbook/thumbs/'.$this->item->opengraph_image );
		}

        if (!empty($this->item->opengraph_author)) {
            $doc->setMetaData( 'og:author', $this->item->opengraph_author);
        }

		if (!empty($this->item->opengraph_description)) {
			$doc->setMetaData( 'og:description', $this->item->opengraph_description );
		}

        if (!empty($this->item->custom_metatags)) {
            foreach ( $this->item->custom_metatags as $custom_tag_name => $custom_tag_value ) {
                $doc->setMetaData($custom_tag_name, $custom_tag_value);
            }
        }

		require_once(JPATH_COMPONENT_ADMINISTRATOR.'/models/configuration.php');
		$configurationModel = JModelLegacy::getInstance('Configuration', COMPONENT_MODEL_PREFIX);
		$this->config = $configurationModel->GetConfig();

		$this->setLayout('iframe');

		parent::display($tpl);

	}

}