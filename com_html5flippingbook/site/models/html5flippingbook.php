<?php defined('_JEXEC') or die('Restricted access');
/**
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

class HTML5FlippingBookModelHTML5FlippingBook extends JModelList
{
	//----------------------------------------------------------------------------------------------------
	protected function getListQuery()
	{
		$params = JFactory::getApplication()->getParams();
		
		$category_id = (int) $params->get('c_category_id');
		
		$jinput = JFactory::getApplication()->input;
		$archive = $jinput->get('archive', '', 'STRING');

		if (!$archive)
		{
			$db = $this->getDbo();
			
			$query = $db->getQuery(true);
			
			$query->select('m.*, r.width, r.height');
			$query->from('`#__html5fb_publication` AS `m`');
			$query->join('LEFT', '`#__html5fb_resolutions` AS `r` ON r.id = m.c_resolution_id');
			if (isset($category_id)) if ($category_id > 0) $query->where('m.`c_category_id`='.$category_id);
			$query->where('m.`published`=1');
			$query->order('m.ordering ASC');
		}
		else
		{
			$date = explode('-', $archive);
			$db = $this->getDbo();
			
			$query = $db->getQuery(true);
			
			$query->select('m.*, r.width, r.height');
			$query->from('`#__html5fb_publication` AS `m`');
			$query->join('LEFT','`#__html5fb_resolutions` AS `r` ON m.c_resolution_id=r.id');
			$query->where("MONTH( m.c_created_time )=".$date[1]." AND YEAR( m.c_created_time )=".$date[0]." AND m.`published` != '0' ");
			$query->order('m.ordering ASC');
		}
		
		return $query;
	}

	//----------------------------------------------------------------------------------------------------
	public function getItem($id = null)
	{
		if ( empty($this->_item) )
		{
			$this->_item = false;
			$params = JFactory::getApplication()->getParams();
			$id = (int) $params->get('c_category_id');

			if (empty($id)) return null;

			$table = JTable::getInstance('Categories', 'HTML5FlippingBookTable');

			if ($table->load($id))
			{
				$properties = $table->getProperties(1);
				$this->_item = JArrayHelper::toObject($properties, 'JObject');

				if ( !empty($this->_item->custom_metatags) )
					$this->_item->custom_metatags = unserialize( $this->_item->custom_metatags );

			}
			else if ($error = $table->getError())
			{
				$this->setError($error);

				return null;
			}
		}

		return $this->_item;
	}
}