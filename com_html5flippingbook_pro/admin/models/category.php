<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

class HTML5FlippingBookModelCategory extends JModelAdmin
{
	protected $text_prefix = COMPONENT_OPTION;
	//----------------------------------------------------------------------------------------------------
	public function getTable($type = 'categories', $prefix = COMPONENT_TABLE_PREFIX, $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	//----------------------------------------------------------------------------------------------------
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);
		$assetName = COMPONENT_OPTION.'.category.'.$item->c_id;

		if ( $item->c_id )
		{
			if ( !JFactory::getUser()->authorise('category.edit', $assetName)
				&&
				( JFactory::getUser()->id == $item->user_id && !JFactory::getUser()->authorise('category.edit.own', $assetName) )
				)
			{
				JError::raiseWarning(404, JText::_('COM_HTML5FLIPPINGBOOK_BE_CATEGORIES_ACCESS_ALERT_EDIT'));
				JFactory::getApplication()->redirect('index.php?option='.COMPONENT_OPTION.'&view=categories');
				return false;
			}
		}
		else
		{
			if ( !JFactory::getUser()->authorise('core.create', 'com_html5flippingbook') )
			{
				JError::raiseWarning(404, JText::_('COM_HTML5FLIPPINGBOOK_BE_CATEGORIES_ACCESS_ALERT_CREATE'));
				JFactory::getApplication()->redirect('index.php?option='.COMPONENT_OPTION.'&view=categories');
				return false;
			}
		}

		if ( !empty($item->custom_metatags) )
			$item->custom_metatags = unserialize( $item->custom_metatags );

		return $item;
	}
	//----------------------------------------------------------------------------------------------------
	protected function loadFormData()
	{
		$data = $this->getItem();

		return $data;
	}
	//----------------------------------------------------------------------------------------------------
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm(COMPONENT_OPTION.'.categories', 'category', array('control' => 'jform', 'load_data' => $loadData));

		return (empty($form) ? false : $form);
	}
	//----------------------------------------------------------------------------------------------------
	public function save($data)
	{
		$custom_tags = array();
		$custom_tags_names = JFactory::getApplication()->input->get('cm_names', array(), 'array');
		$custom_tags_values = JFactory::getApplication()->input->get('cm_values', array(), 'array');

		if ( !empty($custom_tags_names) )
		{
			foreach ( $custom_tags_names as $k => $custom_name )
				$custom_tags[ $custom_name ] = $custom_tags_values[ $k ];
		}

		$data['custom_metatags'] = serialize($custom_tags);
		$data['user_id'] = JFactory::getUser()->id;

		return parent::save($data);
	}
	//----------------------------------------------------------------------------------------------------
	public function delete(&$pks)
	{
		foreach ($pks as $category_id)
		{
			$assetName = COMPONENT_OPTION.'.category.'.$category_id;

			if( !JFactory::getUser()->authorise('core.delete', 'com_html5flippingbook') ){
				JError::raiseWarning(404, JText::_('COM_HTML5FLIPPINGBOOK_BE_CATEGORIES_ACCESS_ALERT_DELETE'));
				return false;
			}
		}

		return parent::delete($pks);
	}
}