<?php defined('_JEXEC') or die('Restricted access');
/**
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

jimport('joomla.application.component.model');
jimport('joomla.filter.output');

function HTML5FlippingBookBuildRoute(&$query)
{
	$segments = array();
	/* check menu item and build route */
	if (isset($query['tmpl']))
	{
		if($query['tmpl'] == 'direct'){
			unset($query['tmpl']);
		}
	}
	if (isset($query['view']))
	{
		if ($query['view'] == 'publication')
		{
			$segments[] = $query['view'];
			unset($query['view']);
			
			if (isset($query['id']))
			{
				$publicationId = $query['id'];
				
				$db = JFactory::getDbo();
				
				$dbQuery = "SELECT *" .
					" FROM `#__html5fb_publication`" .
					" WHERE `c_id` = " . $publicationId;
				$db->setQuery($dbQuery);
				$row = $db->loadObject();
				
				$publicationTitle = (isset($row) ? $row->c_title : $publicationId);
				
				$titleSegment = JFilterOutput::stringURLSafe($publicationTitle);
				$titleSegment = ($titleSegment != '' ? $titleSegment : '-');
				$segments[] = $titleSegment;
				
				$segments[] = $publicationId;
				unset($query['id']);

			}
		}
	}
	else
	{
		if ( @$query['task'] == 'templatecss' )
		{
			$segments[] = 'css';
			$segments[] = $query['template_id'].'.css';

			unset($query['template_id']);
			unset($query['task']);
		}
	}
	
	return $segments;
}

function HTML5FlippingBookParseRoute($segments)
{
	$vars = array();

	switch ($segments[0])
	{
		case 'publication':
		{
			$numSegments = count($segments);
			$vars['view'] = $segments[0];
			if ($numSegments > 2) $vars['id'] = $segments[2];
			
			if(!$vars['Itemid']){
				$lang = JFactory::getLanguage();
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select('id')
					->from('#__menu')
					->where('`link` LIKE "%option=com_html5flippingbook&view=publication%"')
					->where('`params` LIKE \'"publication_id":"'.$vars['id'].'"\'')
					->where('`language` IN ("'.$lang->getTag().'","*","")')
					->order('`language` DESC');
				$vars['Itemid'] = $db->setQuery($query)->loadResult();
			}
			
			break;
		}

		case 'css':
			$vars['view'] = 'html5flippingbook';
			$vars['tmpl'] = 'component';
			$vars['task'] = 'templatecss';
			$vars['template_id'] = str_replace('.css', '', $segments[1]);
		break;
	}
	
	return $vars;
}