<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

class HTML5FlippingBookModelPublication extends JModelAdmin
{
	protected $text_prefix = COMPONENT_OPTION;
	//----------------------------------------------------------------------------------------------------
	public function getTable($type = 'publications', $prefix = COMPONENT_TABLE_PREFIX, $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	//----------------------------------------------------------------------------------------------------
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);

		if ( !empty($item->custom_metatags) )
			$item->custom_metatags = unserialize( $item->custom_metatags );

		if (!is_null($item->convert_formats) && !empty($item->convert_formats))
			$item->convert_formats = explode(",", $item->convert_formats);

		if (!is_null($item->cloudconvert_formats) && !empty($item->cloudconvert_formats))
			$item->cloudconvert_formats = explode(",", $item->cloudconvert_formats);

		return $item;
	}
	//----------------------------------------------------------------------------------------------------
	protected function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState(COMPONENT_OPTION.'.edit.publication.data', array());
		
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
		
		$form = $this->loadForm(COMPONENT_OPTION.'.publications', 'publication', array('control' => 'jform', 'load_data' => $loadData));
		
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

		return parent::save($data);
	}
	//----------------------------------------------------------------------------------------------------
	public function delete(&$pks)
	{
		return parent::delete($pks);
	}
	//----------------------------------------------------------------------------------------------------
	public function move($ids, $targetCategoryId)
	{
		$db = $this->_db;
		
		$query = "SELECT `c_id`, `c_category_id` FROM `#__html5fb_publication`" .
			" WHERE `c_id` IN (" . implode(',', $ids) . ")";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		
		$idsToMove = array();
		
		foreach ($rows as $row)
		{
			if ($row->c_category_id != $targetCategoryId) $idsToMove[] = $row->c_id;
		}
		
		if (count($idsToMove) > 0)
		{
			$query = "UPDATE `#__html5fb_publication`" .
				" SET `c_category_id` = " . $db->quote($targetCategoryId) .
				" WHERE `c_id` IN (" . implode(',', $idsToMove) . ")";
			$db->setQuery($query);
			$db->execute();
		}
	}
	//----------------------------------------------------------------------------------------------------
	public function copy($ids, $targetCategoryId)
	{
		$db = $this->_db;
		
		// Preparing some info.
		
		$query = "SELECT `id` FROM `#__assets`" .
			" WHERE `name` = " . $db->quote(COMPONENT_OPTION);
		$db->setQuery($query);
		$componentRootAssetId = $db->loadResult();
		
		// Getting publications columns names.
		
		$query = "SHOW COLUMNS FROM `#__html5fb_publication`";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		
		$publicationColNames = array();
		
		foreach ($rows as $row)
		{
			if ($row->Field != 'c_id') $publicationColNames[] = $row->Field;
		}
		
		// Getting pages columns names.
		
		$query = "SHOW COLUMNS FROM `#__html5fb_pages`";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		
		$pageColNames = array();
		
		foreach ($rows as $row)
		{
			if ($row->Field != 'id') $pageColNames[] = $row->Field;
		}
		
		// Copying publications.
		
		$query = "SELECT * FROM `#__html5fb_publication`" .
			" WHERE `c_id` IN (" . implode(',', $ids) . ")";
		$db->setQuery($query);
		$publicationRows = $db->loadObjectList();
		
		foreach ($publicationRows as $publicationRow)
		{
			if ($publicationRow->c_category_id != $targetCategoryId)
			{
				// Reading asset.
				
				$query = "SELECT * FROM `#__assets`" .
					" WHERE `name` = " . $db->quote(COMPONENT_OPTION.'.publication.'.$publicationRow->c_id);
				$db->setQuery($query);
				$assetRow = $db->loadObject();
				
				if (!isset($assetRow)) throw new Exception(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_NO_ASSET', $publicationRow->c_title, $publicationRow->c_id));
				
				// Copying publication.
				
				$query = "INSERT INTO `#__html5fb_publication` (";
				
				for ($i = 0; $i < count($publicationColNames); $i++)
				{
					$columnName = $publicationColNames[$i];
					
					$query .= ($i == 0 ? "" : ", ") . "`" . $columnName . "`";
				}
				
				$query .= ") VALUES (";
				
				for ($i = 0; $i < count($publicationColNames); $i++)
				{
					$columnName = $publicationColNames[$i];
					
					switch ($columnName)
					{
						case 'c_category_id':
						{
							$value = $targetCategoryId;
							break;
						}
						default:
						{
							$value = $publicationRow->{$columnName};
						}
					}
					
					$query .= ($i == 0 ? "" : ", ") . $db->quote($value);
				}
				
				$query .= ")";
				
				$db->setQuery($query);
				$db->execute();
				
				// Adding asset.
				
				$newPublicationId =  $db->insertid();
				
				$asset = JTable::getInstance('Asset', 'JTable');
				$asset->name = COMPONENT_OPTION.'.publication.'.$newPublicationId;
				$asset->title = $publicationRow->c_title;
				$asset->rules = $assetRow->rules;
				$asset->setLocation($componentRootAssetId, 'last-child');
				$asset->store();
				
				// Copying pages.
				
				$query = "SELECT * FROM `#__html5fb_pages`" .
					" WHERE `publication_id` = " . $publicationRow->c_id;
				$db->setQuery($query);
				$pageRows = $db->loadObjectList();
				
				foreach ($pageRows as $pageRow)
				{
					$query = "INSERT INTO `#__html5fb_pages` (";
					
					for ($i = 0; $i < count($pageColNames); $i++)
					{
						$columnName = $pageColNames[$i];
						
						$query .= ($i == 0 ? "" : ", ") . "`" . $columnName . "`";
					}
					
					$query .= ") VALUES (";
					
					for ($i = 0; $i < count($pageColNames); $i++)
					{
						$columnName = $pageColNames[$i];
						
						switch ($columnName)
						{
							case 'publication_id':
							{
								$value = $newPublicationId;
								break;
							}
							default:
							{
								$value = $pageRow->{$columnName};
							}
						}
						
						$query .= ($i == 0 ? "" : ", ") . $db->quote($value);
					}
					
					$query .= ")";
					
					$db->setQuery($query);
					$db->execute();
				}
			}
		}
	}
}