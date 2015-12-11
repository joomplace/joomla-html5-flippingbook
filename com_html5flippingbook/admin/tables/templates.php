<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

class HTML5FlippingBookTableTemplates extends JTable
{
	//----------------------------------------------------------------------------------------------------
	function __construct(&$db) 
	{
		parent::__construct('#__html5fb_templates', 'id', $db);
	}
	//----------------------------------------------------------------------------------------------------
	public function delete($pk = null)
	{
		$db = $this->_db;
		
		$query = "SELECT COUNT(`c_id`)" .
			" FROM `#__html5fb_publication`" .
			" WHERE `c_template_id` = " . $this->id;
		$db->setQuery($query);
		$count = $db->loadResult();
		
		if ($count > 0)
		{
			JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_TEMPLATES_DELETION_FORBIDDEN', $this->template_name, $count), 'error');
			return false;
		}
		
		return parent::delete($pk);
	}
}