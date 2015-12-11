<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

jimport('joomla.access.rules');

class HTML5FlippingBookModelPermissions extends JModelLegacy
{
	//----------------------------------------------------------------------------------------------------
	public static function SaveComponentAsset($rules)
	{
		self::GetComponentRootAssetId(); // Contains error check.
		
		self::UpdateExistingAsset(COMPONENT_OPTION, $rules);
	}
	//----------------------------------------------------------------------------------------------------
	public static function SavePublicationAsset($publicationId, $rules)
	{
		$publicationAssetName = COMPONENT_OPTION.'.publication.'.$publicationId;
		
		self::UpdateExistingAsset($publicationAssetName, $rules);
	}
	//----------------------------------------------------------------------------------------------------
	public static function ResetAllAssets()
	{
		$db = JFactory::getDBO();
		
		$componentRootAssetId = self::GetComponentRootAssetId();
		
		// Adding missing assets.
		
		$query = "SELECT * FROM `#__html5fb_publication`" .
			" ORDER BY `c_id`";
		$db->setQuery($query);
		$publications = $db->loadObjectList();
		
		$query = "SELECT * FROM `#__assets`" .
			" WHERE `name` REGEXP '" . COMPONENT_OPTION.".publication." . "'";
			" ORDER BY `c_id`";
		$db->setQuery($query);
		$assets = $db->loadObjectList();
		
		$publicationsWithoutAsset = array();
		
		foreach ($publications as $publication)
		{
			$assetExists = false;
			
			foreach ($assets as $asset)
			{
				if ($asset->id == $publication->asset_id)
				{
					$assetExists = true;
					break;
				}
			}
			
			if (!$assetExists) $publicationsWithoutAsset[] = $publication;
		}
		
		if (count($publicationsWithoutAsset) > 0)
		{
			foreach ($publicationsWithoutAsset as $publication)
			{
				$publicationAssetName = COMPONENT_OPTION.'.publication.'.$publication->c_id;
				$title = $publication->c_title;
				$rules = "";
				
				$asset = JTable::getInstance('Asset', 'JTable');
				$asset->name = $publicationAssetName;
				$asset->title = $title;
				$asset->rules = $rules;
				$asset->setLocation($componentRootAssetId, 'last-child');
				$asset->store();
			}
		}
		
		// Updating component's assets.
		
		$componentRule = array(
			'core.view' => array('1' => 1),
			'core.preview' => array('1' => 1),
			);
		
		$rule = $rules = self::CleanRules($componentRule);
		
		self::UpdateExistingAsset(COMPONENT_OPTION, $rule);
		
		// Updating all publications assets.
		
		$publicationRule = array(
			'core.view' => array(),
			'core.preview' => array(),
			);
		
		$rule = $rules = self::CleanRules($publicationRule);
		$ruleJson = self::JsonEncodeRules($publicationRule);
		
		$query = "UPDATE `#__assets` SET" .
			" `rules` = " . $db->quote($ruleJson) .
			" WHERE `name` REGEXP '".COMPONENT_OPTION.".publication.'";
		$db->setQuery($query);
		$db->execute();
	}
	//----------------------------------------------------------------------------------------------------
	private static function UpdateExistingAsset($assetName, $rules)
	{
		$rules = self::CleanRules($rules);
		$json = self::JsonEncodeRules($rules);
		
		$db = JFactory::getDBO();
		
		$query = "UPDATE `#__assets` SET" .
			" `rules` = " . $db->quote($json) .
			" WHERE `name` = " . $db->quote($assetName);
		$db->setQuery($query);
		$db->execute();
	}
	//----------------------------------------------------------------------------------------------------
	private static function GetComponentRootAssetId()
	{
		$db = JFactory::getDBO();
		
		$query = "SELECT `id` FROM `#__assets`" .
			" WHERE `name` = " . $db->quote(COMPONENT_OPTION);
		$db->setQuery($query);
		$componentRootAssetId = $db->loadResult();
		
		if (!isset($componentRootAssetId)) throw new Exception(JText::_('COM_HTML5FLIPPINGBOOK_BE_NO_COMPONENT_ASSET'));
		
		return $componentRootAssetId;
	}
	//----------------------------------------------------------------------------------------------------
	private static function CleanRules($rulesArray)
	{
		// Removing 'Inherited' permissons.
		
		foreach ($rulesArray as $actionName => $permissions)
		{
			foreach ($permissions as $userGroupId => $permisson)
			{
				if ($permisson == '')
				{
					unset($rulesArray[$actionName][$userGroupId]);
				}
			}
		}
		
		return $rulesArray;
	}
	//----------------------------------------------------------------------------------------------------
	private static function JsonEncodeRules($rules)
	{
		$joomlaAccessRules = new JAccessRules($rules);
		
		return $joomlaAccessRules->__toString();
	}
}