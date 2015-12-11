<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

class HTML5FlippingBookModelResolutions extends JModelList
{
	//----------------------------------------------------------------------------------------------------
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'resolution_name',
				'width',
				'height',
				'id',
				);
		}
		
		parent::__construct($config);
	}
	//----------------------------------------------------------------------------------------------------
	protected function populateState($ordering = null, $direction = null)
	{
		parent::populateState('resolution_name', 'asc');
	}
	//----------------------------------------------------------------------------------------------------
	protected function getListQuery()
	{
		$db = $this->_db;
		
		$query = $db->getQuery(true);
		$query->select('r.*');
		$query->from('`#__html5fb_resolutions` AS `r`');
		$query->order($db->escape($this->state->get('list.ordering').' '.$this->state->get('list.direction')));
		
		return $query;
	}
	//----------------------------------------------------------------------------------------------------
	public function getAllItemsQuantity()
	{
		$db = $this->_db;
		
		$query = "SELECT COUNT(*) FROM `#__html5fb_resolutions`";
		$db->setQuery($query);
		$result = $db->loadResult();
		
		return $result;
	}
}