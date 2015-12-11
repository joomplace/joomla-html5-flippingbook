<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

class HTML5FlippingBookModelTemplate extends JModelAdmin
{
	protected $text_prefix = COMPONENT_OPTION;
	//----------------------------------------------------------------------------------------------------
	public function getTable($type = 'templates', $prefix = COMPONENT_TABLE_PREFIX, $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	//----------------------------------------------------------------------------------------------------
	public function getItem($pk = null)
	{
		return parent::getItem($pk);
	}
	//----------------------------------------------------------------------------------------------------
	protected function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState(COMPONENT_OPTION.'.edit.template.data', array());
		
		if (empty($data))
		{
			$data = $this->getItem();
		}
		
		return $data;
	}
	//----------------------------------------------------------------------------------------------------
	public function getForm($data = array(), $loadData = true)
	{
		$app = JFactory::getApplication();
		
		$form = $this->loadForm(COMPONENT_OPTION.'.templates', 'template', array('control' => 'jform', 'load_data' => $loadData));
		
		if (empty($form))
		{
			return false;
		}
		
		return $form;
	}

	//----------------------------------------------------------------------------------------------------
	public function delete(&$pks)
	{
		return parent::delete($pks);
	}
}