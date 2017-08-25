<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

class HTML5FlippingBookModelPublications extends JModelList
{
	//----------------------------------------------------------------------------------------------------
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'm.ordering',
				'm.c_title',
				'm.published',
				'c.c_category',
				't.template_name',
				'r.resolution_name',
				'm.c_created_time',
				'm.c_id',
				);
		}
		
		parent::__construct($config);
	}
	//----------------------------------------------------------------------------------------------------
	protected function populateState($ordering = null, $direction = null)
	{
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '', 'string');
		$this->setState('filter.published', $published);
		
		$categoryId = $this->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id', '');
		$this->setState('filter.category_id', $categoryId);
		
		parent::populateState('m.ordering', 'asc');
	}
	//----------------------------------------------------------------------------------------------------
	protected function getListQuery()
	{
		$db = $this->_db;
		
		$query = $db->getQuery(true);
		$query->select('m.*, c.`c_category`, t.`template_name`, r.`resolution_name`, COUNT(`pages`.`id`) AS `page_count`');
		$query->from('`#__html5fb_publication` AS `m`');
		$query->join('LEFT', '`#__html5fb_category` AS `c` ON `c`.`c_id` = `m`.`c_category_id`');
		$query->join('LEFT', '`#__html5fb_templates` AS `t` ON `t`.`id` = `m`.`c_template_id`');
		$query->join('LEFT', '`#__html5fb_resolutions` AS `r` ON `r`.`id` = `m`.`c_resolution_id`');
		$query->join('LEFT', '`#__html5fb_pages` AS `pages` ON `pages`.`publication_id` = `m`.`c_id`');
		$query->group('`m`.`c_id`');
		$query->order($db->escape($this->state->get('list.ordering').' '.$this->state->get('list.direction')));
		
		// Filter by search in name.
		
		$search = $this->getState('filter.search');
		
		if (!empty($search))
		{
			$search = $db->Quote('%'.$db->escape($search, true).'%');
			$query->where('`m`.`c_title` LIKE '.$search);
		}
		
		// Filter by published state
		
		$published = $this->getState('filter.published');
		
		if (is_numeric($published))
		{
			$query->where('`m`.`published` = ' . (int) $published);
		}
		
		// Filter by category.
		
		$categoryId = $this->getState('filter.category_id');
		
		if (is_numeric($categoryId) && $categoryId != 0)
		{
			$query->where('`m`.`c_category_id` = ' . (int) $categoryId);
		}
		
		return $query;
	}
	//----------------------------------------------------------------------------------------------------
	public function getAllItemsQuantity()
	{
		$db = $this->_db;
		
		$query = "SELECT COUNT(*) FROM `#__html5fb_publication`";
		$db->setQuery($query);
		$result = $db->loadResult();
		
		return $result;
	}
	//----------------------------------------------------------------------------------------------------
	public function getSelectOptions()
	{
		$db = $this->_db;
		
		$query = $db->getQuery(true);
		$query->select('`c_id`, `c_title`');
		$query->from('`#__html5fb_publication`');
		$query->order('`c_title` ASC');
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		
		$options = array();
		
		foreach ($rows as $row)
		{
			$options[] = JHtml::_('select.option', $row->c_id, $row->c_title, 'value', 'text');
		}
		
		return $options;
	}
}