<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

class HTML5FlippingBookTablePublications extends JTable
{
	//----------------------------------------------------------------------------------------------------
	function __construct(&$db) 
	{
		parent::__construct('#__html5fb_publication', 'c_id', $db);
		$this->_trackAssets = true;
	}
	//----------------------------------------------------------------------------------------------------
	public function store($updateNulls = false)
	{
		jimport('joomla.filesystem.folder');
		
		if (!$this->c_id) $this->c_id = 0;
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.'/libs/HtmlHelper.php');
		
		$db = $this->_db;
		
		$jinput = JFactory::getApplication()->input;
		
		$jform = $jinput->get('jform', array(), 'ARRAY');
		
		if (!$this->c_id)
		{
			$this->c_user_id = JFactory::getUser()->id;
			$this->c_created_time = gmdate("Y-m-d H:i:s");
			
			$query = "SELECT MAX(`ordering`)" .
				" FROM `#__html5fb_publication`";
			$db->setQuery($query);
			$this->ordering = $db->loadResult() + 1;
		}
		
		//==================================================
		// Handling images subdirectory changes.
		//==================================================
		
		$dir = JPATH_SITE.'/media/'.COMPONENT_OPTION.'/images/';
		
		$query = "SELECT `c_imgsub`, `c_imgsubfolder`" .
			" FROM `#__html5fb_publication`" .
			" WHERE `c_id` = ".$this->c_id;
		$db->setQuery($query);
		$row = $db->loadObject();
		
		$oldUseSubdir = (isset($row) ? ($row->c_imgsub == 1) : false);
		$oldSubdirName = (isset($row) ? $row->c_imgsubfolder : '');
		
		$newUseSubdir = ($this->c_imgsub == 1);
		$newSubdirName = $this->c_imgsubfolder;
		
		if (!$newUseSubdir)
		{
			if ($oldUseSubdir)
			{
				$this->setError(JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_IMAGES_SUBDIR_CANT_STOP_USING'));
				return false;
			}
		}
		else
		{
			// This case is checked during client-side validation, but it's too important - so rechecking.
			
			if ($newSubdirName == '')
			{
				$this->setError(JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_IMAGES_SUBDIR_EMPTY_NAME'));
				return false;
			}
			
			// Checking new subdirectory name.
			
			$existingSubdirNames = JFolder::folders($dir);
			
			if ($oldUseSubdir)
			{
				foreach ($existingSubdirNames as $key => $dirName)
				{
					if ($dirName == $oldSubdirName)
					{
						array_splice($existingSubdirNames, $key, 1);
						break;
					}
				}
			}
			
			if (in_array($newSubdirName, $existingSubdirNames))
			{
				$this->setError(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_IMAGES_SUBDIR_ALREADY_EXISTS', $newSubdirName));
				return false;
			}
			
			if ($oldUseSubdir)
			{
				if ($newSubdirName != $oldSubdirName)
				{
					// Renaming subdirectory.
					
					if (!JFolder::exists($dir.$oldSubdirName))
					{
						$this->setError(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_IMAGES_SUBDIR_CANT_RENAME', $dir.$oldSubdirName));
						return false;
					}
					
					$renamed = rename($dir.$oldSubdirName, $dir.$newSubdirName);
					
					if (!$renamed)
					{
						$this->setError(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_CANT_RENAME_DIR', $dir.$newSubdirName));
						return false;
					}
				}
			}
			else
			{
				// Creating subdirectory and moving images there.
				
				JFolder::create($dir.$newSubdirName);
				
				@chmod($dir.$newSubdirName, 0755);
				
				HtmlHelper::createIndexHtmlFile($dir.$newSubdirName);
				
				$query = "SELECT `id`, `page_image`" .
					" FROM `#__html5fb_pages`" .
					" WHERE `publication_id` = " . $this->c_id .
					" AND `c_enable_image` = 1";
				$db->setQuery($query);
				$rows = $db->loadObjectList();
				
				foreach ($rows as $row)
				{
					$currentImageFullFileName = $dir.$row->page_image;
					$newImageFullFileName = $dir.$newSubdirName.'/'.$row->page_image;

					if (JFile::exists($currentImageFullFileName)) JFile::move($currentImageFullFileName, $newImageFullFileName);
				}
			}
		}
		
		//==================================================
		// Access rules.
		//==================================================
		
		if (isset($jform['rules']))
		{
			$rulesArray = $jform['rules'];
			
			// Removing 'Inherited' permissons. Otherwise they will be converted to 'Denied'.
			
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
			
			$rules = new JAccessRules($rulesArray);
			$this->setRules($rules);
		}

		//Delete preview image
		if (file_exists(COMPONENT_MEDIA_PATH.'/thumbs/preview_' . $this->c_id . '.gif'))
		{
			@unlink(COMPONENT_MEDIA_PATH.'/thumbs/preview_' . $this->c_id . '.gif');
		}
		
		return parent::store($updateNulls);
	}
	//----------------------------------------------------------------------------------------------------
	public function delete($pk = null)
	{
        @unlink(COMPONENT_MEDIA_PATH.'/thumbs/preview_'.$pk.'.gif');

		$db = $this->_db;
		
		$query = "SELECT *" .
			" FROM `#__html5fb_publication`" .
			" WHERE `c_id` = " . $pk;
		$db->setQuery($query);
		$publication = $db->loadObject();
		
		//==================================================
		// Deleting resources (this should go before deletion of the Publication itself).
		//==================================================
		
		// Deleting pages.
		$query = "SELECT `id` FROM `#__html5fb_pages`" .
			" WHERE `publication_id` = " . $pk;
		$db->setQuery($query);
		$pageIds = $db->loadColumn(0);
		
		if (count($pageIds) > 0)
		{
			$pagesTable = JTable::getInstance('Pages', COMPONENT_TABLE_PREFIX);
			
			foreach ($pageIds as $pageId)
			{
				$pagesTable->delete($pageId);
			}
		}
		
		// Deleting Publication folder, if possible.
		
		if ($publication->c_imgsubfolder != '')
		{
			jimport('joomla.filesystem.folder');
			
			$publicationImagesSubdirFullName = COMPONENT_MEDIA_PATH.'/images/'.$publication->c_imgsubfolder;
			
			if (JFolder::exists($publicationImagesSubdirFullName))
			{
				$fileNames = JFolder::files($publicationImagesSubdirFullName, '.', false, false, array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'index.html'));
				
				if (count($fileNames) == 0)
				{
					JFolder::delete($publicationImagesSubdirFullName);
				}
			}
		}
		
		//==================================================
		// Deleting Publication.
		//==================================================
		
		$deleted = parent::delete($pk);
		
		if (!$deleted)
		{
			// Notice: Class JTable doesn't delete items without correct assets in jos_assets table: doesn't return any error and doesn't throw exception in this case.
			
			$error = $this->getError();
			JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_CANT_DELETE', $pk) . ($error == '' ? '' : '. ' . $error), 'error');
			return false;
		}
		
		return true;
	}
	//----------------------------------------------------------------------------------------------------
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		
		return COMPONENT_OPTION.'.publication.'.(int) $this->$k;
	}
	//----------------------------------------------------------------------------------------------------
	protected function _getAssetTitle()
	{
		return $this->c_title;
	}
	//----------------------------------------------------------------------------------------------------
	protected function _getAssetParentId(JTable $table = null, $id = null)
	{
		$assetsTable = JTable::getInstance('Asset', 'JTable');
		
		$assetsTable->loadByName(COMPONENT_OPTION);
		
		return $assetsTable->id;
	}
}