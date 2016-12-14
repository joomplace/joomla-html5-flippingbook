<?php defined('_JEXEC') or die('Restricted access');
/**
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

require_once(JPATH_COMPONENT_ADMINISTRATOR.'/libs/VarsHelper.php');

class HTML5FlippingBookViewHTML5FlippingBook extends JViewLegacy
{
	protected $state = null;
	protected $items = null;
	protected $item = null;
	protected $pagination = null;
	protected $user = null;
	protected $menuItemParams = null;
	protected $showListTitle = null;
	protected $listTitle = null;
	protected $viewPublicationButtonText = null;
	protected $config;
	//----------------------------------------------------------------------------------------------------
	function display($tpl = null)
	{
		if ( JFactory::getApplication()->input->get('task', '') == 'templatecss' )
		{
			$this->template_css();
		}

		$document = JFactory::getDocument();
		$document->addStyleSheet(COMPONENT_CSS_URL.'html5flippingbook.css');
		$document->addStyleSheet(COMPONENT_CSS_URL.'font-awesome.min.css');
		
		$this->state = $this->get('State');
		$this->items = array_map(function($item){
            $pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
            $tagPos = preg_match($pattern, $item->c_pub_descr);
            if ($tagPos == 0){
                $item->introtext = $item->c_pub_descr;
                $item->fulltext = '';
            }else{
                list ($item->introtext, $item->fulltext) = preg_split($pattern, $item->c_pub_descr, 2);
            }
		    return $item;
        },$this->get('Items'));
		$this->item = $this->get('Item');

		$this->pagination = $this->get('Pagination');
		$this->user = JFactory::getUser();
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.'/models/configuration.php');
		$configurationModel = JModelLegacy::getInstance('Configuration', COMPONENT_MODEL_PREFIX);
		$this->config = $configurationModel->GetConfig();
		
		// Processing menu item parameters.
		
		$this->menuItemParams = $this->state->get('parameters.menu');

		if (isset($this->menuItemParams))
		{
			$showListTitleParam = $this->menuItemParams->get('showListTitle');
			$listTitleParam = $this->menuItemParams->get('listTitle');
			$viewPublicationButtonTextParam = $this->menuItemParams->get('viewPublicationButtonText');

			$this->showListTitle = (isset($showListTitleParam) ? (bool) $showListTitleParam : null);
			$this->listTitle = (isset($listTitleParam) ? $listTitleParam : null);
			$this->viewPublicationButtonText = (isset($viewPublicationButtonTextParam) ? $viewPublicationButtonTextParam : null);
		}

		if (!isset($this->showListTitle)) $this->showListTitle = false;

		parent::display($tpl);
	}

	private function template_css()
	{
		require_once(JPATH_COMPONENT_ADMINISTRATOR.'/libs/HtmlHelper.php');

		header('Content-Type: text/css');

		$html5flippingbook = file_get_contents( JPATH_SITE . '/components/'.COMPONENT_OPTION.'/assets/css/html5flippingbook.css' );
		$html5flippingbook = str_replace('../images/', COMPONENT_IMAGES_URL, $html5flippingbook);
		echo $html5flippingbook;

		$template_id = (int)JFactory::getApplication()->input->get('template_id', '0');

		$database = JFactory::getDbo();
		$database->setQuery("SELECT * FROM #__html5fb_templates WHERE id = $template_id");
		$template = $database->loadObject();

		if ( empty($template) )
			exit;

		$resultStyles = array();
		
		if ( !empty($template->page_background_color) )
        	$resultStyles[]= 'body { background-color: '.$template->page_background_color.' !important; }';
	
		if ( !empty($template->background_color) )
			$resultStyles[]= '.flipbook-viewport .page { background-color: '.$template->background_color.' !important; }';
	
		if ( !empty($template->text_color) )
			$resultStyles[]= '.flipbook-viewport .page { color: '.$template->text_color.' !important; }';

		if ( !empty($template->fontfamily) )
		{
			$font_family = PublicationTemplateFont::FontsList( $template->fontfamily );
			if ( !empty($font_family) )
				$resultStyles[] = '.mceContentBody, .flipbook-viewport .page { font-family: '.$font_family.' !important; }';
		}

		if ( !empty($template->fontsize) )
		{
			$template->fontsize = preg_replace('/[\s]+/is', '', $template->fontsize);

			if ( !empty($template->fontsize) )
			{
				if ( !preg_match('/[px|pt|em|\%]+/', $template->fontsize) )
					$template->fontsize.= 'px';

				if ( !empty($template->fontsize) )
					$resultStyles[] = '.mceContentBody, .flipbook-viewport .page { font-size: '.$template->fontsize.' !important; }';
			}
		}

		if ( !empty($template->p_margin) )
		{
			$template->p_margin = preg_replace('/[\s]+/is', '', $template->p_margin);

			if ( !empty($template->p_margin) )
			{
				if ( !preg_match('/[px|pt|em|\%]+/', $template->p_margin) )
					$template->p_margin.= 'px';

				if ( !empty($template->p_margin) )
					$resultStyles[] = '.mceContentBody p, .flipbook-viewport .page p { margin: '.$template->p_margin.' 0 !important; }';
			}
		}

		if ( !empty($template->p_lineheight) )
		{
			$template->p_lineheight = preg_replace('/[\s]+/is', '', $template->p_lineheight);

			if ( !empty($template->p_lineheight) )
			{
				if ( !preg_match('/[px|pt|em|\%]+/', $template->p_lineheight) )
					$template->p_lineheight.= 'px';

				if ( !empty($template->p_lineheight) )
					$resultStyles[] = '.mceContentBody p, .flipbook-viewport .page p { line-height: '.$template->p_lineheight.' !important; }';
			}
		}

//		if ( !empty($template->show_shadow) && !$template->hard_cover)
//		{
//			$resultStyles[] = '.flipbook .even{ background-image:url("'.COMPONENT_IMAGES_URL.'gradient-page-left.jpg"); background-position:right top; background-repeat:repeat-y; }';
//			$resultStyles[] = '.flipbook .odd{ background-image:url("'.COMPONENT_IMAGES_URL.'gradient-page-right.jpg"); background-position:left top; background-repeat:repeat-y; }';
//		}

		if ( !empty($resultStyles) )
		{
			die( implode("\n", $resultStyles) );
		}

	}
}