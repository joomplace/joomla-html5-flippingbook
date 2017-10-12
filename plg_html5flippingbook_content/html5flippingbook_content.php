<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5 Flipping Book content plugin
* @package HTML5 Flipping Book
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

jimport('joomla.plugin.plugin');

class plgContentHtml5flippingbook_Content extends JPlugin
{
	//----------------------------------------------------------------------------------------------------
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);
	}
	//----------------------------------------------------------------------------------------------------
	public function onContentPrepare($context, &$article, &$params, $limitstart = 0)
	{
		$artext = $returntext = $tag = $replace = '';

		$regex = '/{html5fb\s*.*?}/i';
		
		if (!isset($article->text)) return true;
		
		$artext = $article->text;
		
		if (strpos($artext, 'html5fb') === false) return true;
		
		if ($artext)
		{		
			preg_match_all($regex, $article->text, $matches);
			
			if ($matches)
			{
				JHTML::_('behavior.modal');
				
				$db = JFactory::getDbo();
				
				$option = 'com_html5flippingbook';
				
				$lang = JFactory::getLanguage();
				$lang->load('plg_content_html5flippingbook_content', JPATH_ADMINISTRATOR);
				
				$user = JFactory::getUser();
				
				foreach($matches[0] as $match)
				{
					$replace = $this->plgHtml5Fb_replacer($match, $db, $option, $user);
					$article->text = str_replace($match, $replace, $article->text);
				}
			}
		}
		
		return true;
	}
	//----------------------------------------------------------------------------------------------------
	private function plgHtml5Fb_replacer($matches,$db, $option, $user)
	{
		$regexId = '/id=\s*(\d*)/i';
		preg_match($regexId, $matches, $data);
		$id = (isset($data[1]) ? $data[1] : '');
		
		$regexLink = '/link=\s*([^}]*)/i';
		preg_match($regexLink, $matches, $data);
		$link = (isset($data[1]) ? $data[1] : '[thumbnail]');
		
		$replace = $this->getHtml5Fb($id, $link, $db, $option, $user);
		
		return $replace;
	}
	//----------------------------------------------------------------------------------------------------
	private function getHtml5Fb($publicationId, $link = '', $db, $option, $user)
	{
		$link = str_replace('&lt;', '<', $link);
		$link = str_replace('&gt;', '>', $link);
		
		$query = "SELECT p.*, r.`height`, r.`width`" .
			" FROM `#__html5fb_publication` AS p" .
			" LEFT JOIN `#__html5fb_resolutions` AS r ON r.`id` = p.`c_resolution_id`" .
			" WHERE p.`c_id` = " . $db->quote($publicationId) . " AND p.`published` = 1";
		$db->setQuery($query);
		$row = $db->loadObject();
		
		$html = '';
		
		if (!isset($row))
		{
			$html .= stripslashes('['.JText::_('PLG_HTML5FLIPPINGBOOK_CONTENT_MAGAZINE_NOT_AVAILABLE').']');
			$html .= '<br /><br />';
		}
		else
		{	
			$link = str_replace('[thumbnail]', '<img src='.JUri::root().'media/com_html5flippingbook/thumbs/'.$row->c_thumb.'>', $link);
			
			if ( empty($link) )
				$link = $row->c_title;

			if ($link != '')
			{
				$popupWidth = $row->width * 2+44;
				$popupHeight = $row->height+100;
				
				$uri = JUri::getInstance();

				$publicationRawLink = 'index.php?option='.$option.'&amp;view=publication&amp;id='.$row->c_id;
				$linkContent = '';
				
				if ($row->c_popup == 0) // Direct link.
				{
					$lnk = JRoute::_($publicationRawLink, false, $uri->isSSL());
					$linkContent = '<a class="readmore" href="' . $lnk . '">';
				}
				else if ($row->c_popup == 1) // Popup window.
				{
					JFactory::getDocument()->addScriptDeclaration('function ht5popupWindow(a, b, c, d, f) { window.open(a, b, "height=" + d + ",width=" + c + ",top=" + (screen.height - d) / 2 + ",left=" + (screen.width - c) / 2 + ",scrollbars=" + f + ",resizable").window.focus() };');
					$lnk = JRoute::_($publicationRawLink.'&tmpl=component', false, $uri->isSSL());
					$linkContent='<a class="readmore" href="javascript: ht5popupWindow(\'' . $lnk . '\', \'' . $row->c_id . '\', \'' . $popupWidth . '\', \'' . $popupHeight .
						'\', \'no\');">';
				}
				else if ($row->c_popup == 2) // Direct link without template.
				{
					$lnk = JRoute::_($publicationRawLink.'&tmpl=component', false, $uri->isSSL());
					$linkContent = '<a class="readmore" href="' . $lnk . '">';
				}
				else if ($row->c_popup == 3) // Modal window.
				{	
					JHTML::_('behavior.modal', 'a.flip-modal');
					$lnk = JRoute::_($publicationRawLink.'&tmpl=component', false, $uri->isSSL());
					$linkContent ='<a class="flip-modal readmore" rel="{handler: \'iframe\', size: {x: ' . $popupWidth . ', y:' . $popupHeight . '}}" href="' . $lnk . '">';
				}
				
				$html .= $linkContent . stripslashes($link) . '</a>';
			}
		}
		
		return $html;
	}
}