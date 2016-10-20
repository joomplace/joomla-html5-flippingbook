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
	if (isset($query['view']))
	{
		if ($query['view'] == 'publication')
		{
			$segments[] = $query['view'];
			unset($query['view']);
			
			if (isset($query['id']))
			{
				$db = JFactory::getDbo();
				
				$dbQuery = "SELECT *" .
					" FROM `#__html5fb_publication`" .
					" WHERE `c_id` = " . $db->quote($query['id']);
				$db->setQuery($dbQuery);
				$row = $db->loadObject();
				
				$publicationTitle = (isset($row) ? $row->c_title : $query['id']);
				
				$titleSegment = JFilterOutput::stringURLSafe($publicationTitle);
				$titleSegment = ($titleSegment != '' ? $titleSegment : '-');
				$segments[] = $query['id'] . '-' . $titleSegment;
				
				unset($query['id']);

				if (isset($query['tmpl']))
				{
					if ( $query['tmpl'] != 'component' )
						$segments[] = $query['tmpl'];

					unset($query['tmpl']);
				}
			}

			if (isset($query['layout']))
			{
				$segments[] = 'mobile';
				unset($query['layout']);
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
			$vars['Itemid'] = '';
			$data = explode(":", $segments[1]);

			$vars['id'] = $data[0];
			if ($numSegments > 2) $vars['layout'] = $segments[2];
			if ($numSegments > 3) $vars['tmpl'] = ( empty($segments[3]) ? 'component' : ($segments[3]=='direct')?'':$segments[3]);
				else
					$vars['tmpl'] = 'component';
			if(!$vars['tmpl']) unset($vars['tmpl']);
			
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