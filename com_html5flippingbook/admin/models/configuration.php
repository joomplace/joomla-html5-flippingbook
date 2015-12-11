<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

class HTML5FlippingBookModelConfiguration extends JModelAdmin
{
	//----------------------------------------------------------------------------------------------------
	public function __construct($config = array())
	{
		parent::__construct($config);
	}
	//----------------------------------------------------------------------------------------------------
	public function getTable($type = 'configuration', $prefix = COMPONENT_TABLE_PREFIX, $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	//----------------------------------------------------------------------------------------------------
	public function getItem($pk = null)
	{
		return $this->getConfig();
	}
	//----------------------------------------------------------------------------------------------------
	public function getForm($data = array(), $loadData = true)
	{
		$app = JFactory::getApplication();
		
		$form = $this->loadForm(COMPONENT_OPTION.'.configuration', 'configuration', array('control' => 'jform', 'load_data' => $loadData));
		
		if (empty($form)) return false;
		
		return $form;
	}
	//----------------------------------------------------------------------------------------------------
	protected function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState(COMPONENT_OPTION.'.edit.configuration.data', array());
		
		if (empty($data))
		{
			$data = $this->getItem();
		}
		
		return $data;
	}
	//----------------------------------------------------------------------------------------------------
	public function getConfig()
	{
		return $this->getTable()->getConfig();
	}
	//----------------------------------------------------------------------------------------------------
	public function saveConfig($jform)
	{
		$this->getTable()->saveConfig($jform);
	}
}