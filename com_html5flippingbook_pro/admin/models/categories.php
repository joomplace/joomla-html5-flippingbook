<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

class HTML5FlippingBookModelCategories extends JModelList
{
	//----------------------------------------------------------------------------------------------------
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'c_category',
				'c_id',
				);
		}
		
		parent::__construct($config);
	}
	//----------------------------------------------------------------------------------------------------
	protected function populateState($ordering = null, $direction = null)
	{
		parent::populateState('c_category', 'asc');
	}
	//----------------------------------------------------------------------------------------------------
	protected function getListQuery()
	{
		$db = $this->_db;
		
		$query = $db->getQuery(true);
		$query->select('c.*');
		$query->from('`#__html5fb_category` AS c');
		$query->order($db->escape($this->state->get('list.ordering').' '.$this->state->get('list.direction')));
		
		return $query;
	}
	//----------------------------------------------------------------------------------------------------
	public function getAllItemsQuantity()
	{
		$db = $this->_db;
		
		$query = "SELECT COUNT(*) FROM `#__html5fb_category`";
		$db->setQuery($query);
		$result = $db->loadResult();
		
		return $result;
	}
	//----------------------------------------------------------------------------------------------------
	public function getSelectOptions()
	{
		$db = $this->_db;
		
		$query = $db->getQuery(true);
		$query->select('`c_id`, `c_category`');
		$query->from('`#__html5fb_category`');
		$query->order('`c_category` ASC');
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		
		$options = array();
		
		foreach ($rows as $row)
		{
			$options[] = JHtml::_('select.option', $row->c_id, $row->c_category, 'value', 'text');
		}
		
		return $options;
	}
}