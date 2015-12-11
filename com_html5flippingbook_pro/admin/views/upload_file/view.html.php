<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class HTML5FlippingBookViewUpload_File extends JViewLegacy
{
	protected $dir;
	protected $pubid;
	protected $extensionsStr;
	protected $elementId;
	protected $linkedElementIdsStr;
	protected $maxSize;
	//----------------------------------------------------------------------------------------------------
	public function display($tpl = null)
	{
		$jinput = JFactory::getApplication()->input;
		
		$this->dir                  = $jinput->getString('dir', '');
		$this->pubid                = $jinput->getInt('pubid');
		$this->elementId            = $jinput->getString('elementId', '');
		$this->extensionsStr        = $jinput->getString('extensions', '');
		$this->linkedElementIdsStr  = $jinput->getString('linkedElementIds', '');
		
		$linkedElementIds = array_filter(explode(',', $this->linkedElementIdsStr));
		array_walk($linkedElementIds, function(&$item, $key) { $item = trim($item); });
		
		$this->maxSize = min((int) ini_get('post_max_size'), (int) ini_get('upload_max_filesize'));
		
		if (isset($_FILES['userfile']))
		{
			$this->upload($this->dir, $this->extensionsStr, $this->elementId, $linkedElementIds, $this->pubid);
		}
		
		$document = JFactory::getDocument();
		$document->addStyleSheet(COMPONENT_CSS_URL.'html5flippingbook.css');
		$document->addScript(COMPONENT_JS_URL.'BootstrapFormHelper.js');
		$document->addScript(COMPONENT_JS_URL.'BootstrapFormValidator.js');
		$document->addScript(COMPONENT_JS_URL.'MethodsForXml.js');
		$document->addScript(COMPONENT_JS_URL.'MyAjax.js');
		
		parent::display($tpl);
	}
	//----------------------------------------------------------------------------------------------------
	private function upload($dir, $extensionsStr, $elementId, $linkedElementIds, $publicationId)
	{
		jimport('joomla.filesystem.file');
		
		$userFileName = (isset($_FILES['userfile']['name']) ? $_FILES['userfile']['name'] : '');
		
		// Checking not specified file.
		if (!isset($_FILES['userfile']) || empty($userFileName))
		{
			echo '<script type="text/javascript">alert("' . JText::_('COM_HTML5FLIPPINGBOOK_BE_FILE_UPLOAD_SELECT_FILE_WARNING') . '")</script>';
			return;
		}
		
		// // NOTE: Removed since version 3.0.0 (build 002).
		// // Checking spaces.
		
		// if (strpos($userFileName, ' ') !== false)
		// {
			// echo '<script type="text/javascript">alert("' . JText::_('COM_HTML5FLIPPINGBOOK_BE_FILE_UPLOAD_NO_SPACES') . '")</script>';
			// return;
		// }
		
		// Checking not allowed characters.
		if (!preg_match('/^[\w_ \-\.\(\)\[\[\]]+$/', $userFileName))
		{
			echo '<script type="text/javascript">alert("' . JText::_('COM_HTML5FLIPPINGBOOK_BE_FILE_UPLOAD_NOT_ALLOWED_CHARACTERS') . '")</script>';
			return;
		}
		
		// Checking file extension.
		$isImage = FALSE;
		if ($extensionsStr != '')
		{
			$userFileExtension = strtolower(JFile::getExt($userFileName));
			
			$extensions = explode(',', $extensionsStr);
			if (in_array($userFileExtension, array('png', 'jpg', 'gif')))
			{
				$isImage = TRUE;
			}

			if (!in_array($userFileExtension, $extensions))
			{
				$extensionsTip = ''; 
				
				foreach ($extensions as $key => $extension)
				{
					$extensionsTip .= ($key == 0 ? '' : ', ') . strtoupper($extension);
				}
				
				echo '<script type="text/javascript">alert("' . JText::_('COM_HTML5FLIPPINGBOOK_BE_FILE_UPLOAD_TYPE_WARNING') . ': ' . $extensionsTip . '")</script>';
				return;
			}
		}
		
		// Creating base directory if required.
		
		$baseDir = JPATH_SITE.'/'.$dir;
		
		if (!file_exists($baseDir)) @mkdir($baseDir, 0755);
		
		// // NOTE: Removed since version 3.0.0 (build 002).
		// // Checking file existence.
		
		// if (file_exists($baseDir.'/'.$userFileName))
		// {
			// //echo '<script type="text/javascript">alert("' . JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_FILE_UPLOAD_FILE_ALREADY_EXISTS', $userFileName) . '")</script>';
			// //return;
		// }
		
		// Detecting file replacement.
		$fileIsBeingReplaced = file_exists($baseDir.'/'.$userFileName);

		// Moving uploaded file.
		if ($isImage && $elementId == 'jform_page_image')
		{
			jimport('joomls.filesystem.folder');

			// Defining target directory for big image
			$targetDirFullOriginalIMG = $baseDir . '/original' ;
			if (!JFolder::exists($targetDirFullOriginalIMG))
			{
				if (!JFolder::create($targetDirFullOriginalIMG, 0757))
				{
					echo '<script type="text/javascript">alert("' . JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PAGES_CANNOT_CREATE_DIR_BIG_IMG', $targetDirFullOriginalIMG) . '")</script>';
					return;
				}
			}

			try
			{
				$db = JFactory::$database;
				$query = $db->getQuery(true)
					->select('`r`.`width`, `r`.`height`')
					->from('`#__html5fb_resolutions` AS `r`')
					->innerJoin('`#__html5fb_publication` AS `p` ON `p`.`c_resolution_id` = `r`.`id`')
					->where('`p`.`c_id` = ' . $publicationId);
				$db->setQuery($query);
				$resolution = $db->loadObject();

				//Resize image
				$image = new JImage();
				$image->loadFile($_FILES['userfile']['tmp_name']);
				$image->resize($resolution->width, $resolution->height, FALSE);
				$image->toFile($baseDir . '/thumb_' . $userFileName, IMAGETYPE_JPEG, array('quality' => 95));
				$image->destroy();
			}
			catch (Exception $ex)
			{
				echo '<script type="text/javascript">alert("' . JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PAGES_CANNOT_RESIZE_IMAGES', addslashes($ex->getMessage())) . '")</script>';
				return;
			}

			if (!move_uploaded_file($_FILES['userfile']['tmp_name'], $targetDirFullOriginalIMG . '/' . $userFileName) || !JPath::setPermissions($targetDirFullOriginalIMG . '/' . $userFileName))
			{
				echo '<script type="text/javascript">alert("' . JText::_('COM_HTML5FLIPPINGBOOK_BE_FILE_UPLOAD_FAILED') . ': ' . $userFileName . '")</script>';
				return;
			}
		}
		else
		{
			if (!move_uploaded_file($_FILES['userfile']['tmp_name'], $baseDir . '/' . $_FILES['userfile']['name']) || !JPath::setPermissions($baseDir . '/' . $_FILES['userfile']['name']))
			{
				echo '<script type="text/javascript">alert("' . JText::_('COM_HTML5FLIPPINGBOOK_BE_FILE_UPLOAD_FAILED') . ': ' . $userFileName . '")</script>';
				return;
			}
		}
		
		// Handling successfull upload.
		
		$html = array();
		
		$html[] = '<script type="text/javascript">';
		
		foreach ($linkedElementIds as $linkedElementId)
		{
			$html[] = 'window.parent.html5fbOnFileUploadedToList_' . $linkedElementId . '("' . $userFileName . '", false, ' . ($fileIsBeingReplaced ? 'true' : 'false') . ');';
		}
		
		$html[] = 'window.parent.html5fbOnFileUploadedToList_' . $elementId . '("' . $userFileName . '", true, ' . ($fileIsBeingReplaced ? 'true' : 'false') . ');';
		
		$html[] = '</script>';
		
		echo implode('', $html);
	}
}