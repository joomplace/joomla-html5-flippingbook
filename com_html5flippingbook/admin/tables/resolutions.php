<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

class HTML5FlippingBookTableResolutions extends JTable
{
	//----------------------------------------------------------------------------------------------------
	function __construct(&$db) 
	{
		parent::__construct('#__html5fb_resolutions', 'id', $db);
	}
	//----------------------------------------------------------------------------------------------------
	public function delete($pk = null)
	{
		$db = $this->_db;
		
		$query = "SELECT COUNT(`c_id`)" .
			" FROM `#__html5fb_publication`" .
			" WHERE `c_resolution_id` = " . $this->id;
		$db->setQuery($query);
		$count = $db->loadResult();
		
		if ($count > 0)
		{
			JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_RESOLUTIONS_DELETION_FORBIDDEN', $this->resolution_name, $count), 'error');
			return false;
		}
		
		return parent::delete($pk);
	}
}