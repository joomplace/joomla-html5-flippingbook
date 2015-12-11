<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

class HTML5FlippingBookModelPages extends JModelList
{
	//----------------------------------------------------------------------------------------------------
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'p.ordering',
				'p.page_title',
				'm.c_title',
				'p.id',
				);
		}
		
		parent::__construct($config);
	}
	//----------------------------------------------------------------------------------------------------
	protected function populateState($ordering = null, $direction = null)
	{
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$publicationId = $this->getUserStateFromRequest($this->context.'.filter.publication_id', 'filter_publication_id', '');
		$this->setState('filter.publication_id', $publicationId);
		
		parent::populateState('p.ordering', 'asc');
	}
	//----------------------------------------------------------------------------------------------------
	protected function getListQuery()
	{
		$db = $this->_db;
		
		$query = $db->getQuery(true);
		$query->select('p.*, m.`c_title` AS publication_title');
		$query->from('`#__html5fb_pages` AS `p`');
		$query->join('LEFT','`#__html5fb_publication` AS `m` ON `m`.`c_id` = `p`.`publication_id`');
		$query->order($db->escape($this->state->get('list.ordering').' '.$this->state->get('list.direction')));
		
		// Filter by search in name.
		
		$search = $this->getState('filter.search');
		
		if (!empty($search))
		{
			$search = $db->Quote('%'.$db->escape($search, true).'%');
			$query->where('`p`.`page_title` LIKE '.$search);
		}
		
		// Filter by publication.
		
		$publicationId = $this->getState('filter.publication_id');
		
		if (is_numeric($publicationId) && $publicationId != 0)
		{
			$publicationId = (int) $publicationId;
			
			// Checking publication (otherwise after deletion of the publication state will be wrong).
			
			$db->setQuery("SELECT COUNT(*)" .
				" FROM `#__html5fb_publication`" .
				" WHERE `c_id` = " . $publicationId);
			$count = $db->loadResult();
			
			if ($count == 0)
			{
				$publicationId = '';
				
				$app = JFactory::getApplication();
				$app->setUserState(COMPONENT_OPTION.'.pages.filter.publication_id', $publicationId);
				$this->setState('filter.publication_id', $publicationId);
			}
			else
			{
				$query->where('`p`.`publication_id` = ' . (int) $publicationId);
			}
		}
		
		return $query;
	}
	//----------------------------------------------------------------------------------------------------
	public function getAllItems()
	{
		$db = $this->_db;
		
		$query = "SELECT * FROM `#__html5fb_pages` ORDER BY `ordering`";
		$db->setQuery($query);
		$result = $db->loadObjectList();
		
		return $result;
	}
	//----------------------------------------------------------------------------------------------------
	public function getAllItemsQuantity()
	{
		$db = $this->_db;

		$query = "SELECT COUNT(*) FROM `#__html5fb_pages`";
		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}
}