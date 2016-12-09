<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

class HTML5FlippingBookControllerPages extends JControllerAdmin
{
	//----------------------------------------------------------------------------------------------------
	public function getModel($name = 'Page', $prefix = COMPONENT_MODEL_PREFIX, $config = array()) 
	{
		return parent::getModel($name, $prefix, array('ignore_request' => true));
	}
	//----------------------------------------------------------------------------------------------------
	public function save_order_ajax()
	{
		@ob_clean();
		header('Expires: Thu, 01 Jan 1970 00:00:01 GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-Type: text/plain; charset=utf-8');

		$pks = $this->input->post->get('cid', array(), 'array');
		$order = $this->input->post->get('order', array(), 'array');

		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);

		$model = $this->getModel();

		$return = $model->saveorder($pks, $order);

		echo ($return ? '1' : '0');

		jexit();
	}
	//----------------------------------------------------------------------------------------------------
	public function redirect_from_publications()
	{
		$jinput = JFactory::getApplication()->input;
		
		$publicationId = $jinput->get('pubId', 0, 'INT');
		
		JFactory::getApplication()->setUserState(COMPONENT_OPTION.'.pages.filter.publication_id', $publicationId);
		
		JFactory::getApplication()->redirect('index.php?option='.COMPONENT_OPTION.'&view=pages', '', '');
	}
	//----------------------------------------------------------------------------------------------------
	public function show_multiupload()
	{
		$jinput = JFactory::getApplication()->input;
		
		$publicationId = $jinput->get('filter_publication_id', 0, 'INT');
		
		$url = 'index.php?option='.COMPONENT_OPTION.'&view=pages&layout=multiupload'.($publicationId == 0 ? '' : '&pubId='.$publicationId);
		
		JFactory::getApplication()->redirect($url, '', '');
	}
	//----------------------------------------------------------------------------------------------------
	public function multiupload()
	{
		require_once(JPATH_COMPONENT_ADMINISTRATOR.'/libs/MethodsForStrings.php');
		
		if ((int) ini_get('memory_limit') < 128) ini_set('memory_limit', '128M');
		if ((int) ini_get('max_execution_time') < 300) ini_set('max_execution_time', '300');
		
		$jinput = JFactory::getApplication()->input;
		
		$sourceType = $jinput->get('source_type');
		$publicationId = $jinput->get('publication_id');
		$pagesTitle = $jinput->get('general_pages_title');
		
		switch ($sourceType)
		{
			case 'archive': $this->multiuploadPagesFromArchive($publicationId, $pagesTitle); break;
			case 'directory': $this->multiuploadPagesFromDirectory($publicationId, $pagesTitle); break;
		}
	}
	//----------------------------------------------------------------------------------------------------
	private function multiuploadPagesFromArchive($publicationId, $pagesTitle)
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.archive');
		
		$archiveFileObj = $_FILES['archive_file'];
		
		// Reading publication data.
		
		$db = JFactory::getDBO();
		
		$query = "SELECT * FROM `#__html5fb_publication`" .
			" WHERE `c_id` = " . $publicationId;
		$db->setQuery($query);
		$publication = $db->loadObject();
		
		// Defining target directory.
		$targetDirFullName = JPATH_SITE.'/media/'.COMPONENT_OPTION.'/images'.($publication->c_imgsubfolder == '' ? '' : '/'.$publication->c_imgsubfolder);

		// Defining target directory for big image
		$targetDirFullOriginalIMG = JPATH_SITE . '/media/' . COMPONENT_OPTION . '/images' . ($publication->c_imgsubfolder == '' ? '' : '/' . $publication->c_imgsubfolder) . '/original' ;
		
		// Creating temporary directory for archive unpacking and images resizing.
		$tempDirName = JPATH_SITE.'/tmp/'.COMPONENT_OPTION.'_tmp_'.MethodsForStrings::GenerateRandomString(16, 'lower');
		
		$folderCreated = JFolder::create($tempDirName, 0757);
		
		if (!$folderCreated)
		{
			$this->showErrorOnPagesMultiupload(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PAGES_CANNOT_CREATE_TEMP_DIR', $tempDirName), $tempDirName);
			return;
		}

		if (!JFolder::create($targetDirFullOriginalIMG, 0757))
		{
			$this->showErrorOnPagesMultiupload(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PAGES_CANNOT_CREATE_DIR_BIG_IMG', $targetDirFullOriginalIMG), null);
			return;
		}

		// Downloading and checking archive.
		if ($archiveFileObj['name'] == '' || $archiveFileObj['error'] == 1)
		{
			$this->showErrorOnPagesMultiupload(JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_CANNOT_UPLOAD_ARCHIVE_FILE'), $tempDirName);
			return;
		}
		
		if (!$archiveFileObj['size'])
		{
			$maxSize = min((int) ini_get('post_max_size'), (int) ini_get('upload_max_filesize'));
			
			$this->showErrorOnPagesMultiupload(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PAGES_TOO_LARGE_FILE', $maxSize), $tempDirName);
			return;
		}
		
		// Checking MIME type.
		$fileMimeType = $archiveFileObj['type'];
		
		$zipMimeTypes = array(
			'application/zip',
			'application/x-zip',
			'application/x-zip-compressed',
			'application/octet-stream',
			'application/x-compress',
			'application/x-compressed',
			'multipart/x-zip',
			'application/force-download',
			'application/x-download',
			);
		
		if (!in_array($fileMimeType, $zipMimeTypes))
		{
			$this->showErrorOnPagesMultiupload(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PAGES_WRONG_ARCHIVE_TYPE', $fileMimeType), $tempDirName);
			return;
		}
		
		// Moving archive to temporary directory.
		$archiveFileName = $archiveFileObj['name'];
		$movedArchiveFileFullName = $tempDirName.'/'.$archiveFileName;
		
		if (!move_uploaded_file($archiveFileObj['tmp_name'], $movedArchiveFileFullName) || !JPath::setPermissions($movedArchiveFileFullName))
		{
			$this->showErrorOnPagesMultiupload(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PAGES_CANNOT_MOVE_ARCHIVE', $archiveFileName), $tempDirName);
			return;
		}
		
		// Unpacking and deleting archive.
		$archiveUnpacked = JArchive::extract($movedArchiveFileFullName, $tempDirName);
		
		if (!$archiveUnpacked)
		{
			$this->showErrorOnPagesMultiupload(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PAGES_CANNOT_EXTRACT_ARCHIVE', $archiveFileName), $tempDirName);
			return;
		}
		
		unlink($movedArchiveFileFullName);
		
		// Selecting files with supported extensions and sorting them.

        $extensions = '\.(' .
            '(j|J)(p|P)(g|G)' .
            '|(j|J)(p|P)(e|E)(g|G)' .
            '|(p|P)(n|N)(g|G)' .
            '|(g|G)(i|I)(f|F)' .
            '|(h|H)(t|H)(m|M)' .
            '|(h|H)(t|H)(m|M)(l|L)' .
            '|(t|T)(x|X)(t|T)' .
            ')$';

        $allowedHtmlFiles = array('html', 'htm', 'txt');
		
		$fileNames = JFolder::files($tempDirName, $extensions, false);
		
		if (!$fileNames || count($fileNames) == 0)
		{
			$this->showErrorOnPagesMultiupload(JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_NO_CORRECT_IMAGES_IN_ARCHIVE'), $tempDirName);
			return;
		}
		
		uasort($fileNames, 'strnatcmp');
		$fileNames = array_values($fileNames);
		
		// Creating thumbnails.
		try
		{
			//Create thumb for first page
			if (isset($fileNames[0]))
			{
				$outputFileName = 'thumb_' . $fileNames[0] . "-" . 0 . ".jpg";
				$outputFilePath = JPATH_SITE . '/media/' . COMPONENT_OPTION . '/thumbs/' . $outputFileName;

				$image = new JImage();
				$image->loadFile($tempDirName . '/' . $fileNames[0]);
				$image->resize(240, 340, FALSE);
				$image->toFile($outputFilePath, IMAGETYPE_JPEG, array('quality' => 95));
				$image->destroy();
			}

			$this->_resize($fileNames, $tempDirName, $publicationId);
		}
		catch (Exception $ex)
		{
			$this->showErrorOnPagesMultiupload(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PAGES_CANNOT_RESIZE_IMAGES', $ex->getMessage()), $tempDirName);
			return;
		}
		
		// Copying files to target directory and deleting temporary directory. Existing files will be overridden.
		$fileNamesToCopy = JFolder::files($tempDirName, $extensions, false);
		$htmlFilesContent = array();

		foreach ($fileNamesToCopy as $fileName)
		{
            if ( in_array(JFile::getExt($fileName), $allowedHtmlFiles) )
            {
                $htmlFilesContent[ basename($fileName) ] = $this->_getStrippedHTML($tempDirName.'/'.$fileName);
            }
            else
            {
	            $fileCopied = false;
	            if ( stripos($fileName, 'thumb_')!==false )
	            {
		            $fileCopied = JFile::copy($tempDirName . '/' . $fileName, $targetDirFullName.'/'.$fileName);
	            }
	            else
	            {
		            $fileCopied = JFile::copy($tempDirName . '/' . $fileName, $targetDirFullOriginalIMG . '/' . $fileName);
	            }

                if (!$fileCopied)
                {
                    $this->showErrorOnPagesMultiupload(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PAGES_CANNOT_COPY_FILE_TO_TARGET_DIR', $fileName), $tempDirName);
                    return;
                }
            }
		}
		
		JFolder::delete($tempDirName);

		// Updating database.
		$pagesInfoCreated = $this->registerPagesInDB($fileNames, $publicationId, $pagesTitle, $htmlFilesContent, $outputFileName);
		
		if (!$pagesInfoCreated)
		{
			$this->showErrorOnPagesMultiupload(JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_CANNOT_WRITE_TO_DB'), $tempDirName);
			return;
		}
		
		// Redirecting.
		$app = JFactory::getApplication();
		$app->setUserState(COMPONENT_OPTION.'.pages.filter.publication_id', $publicationId);
		
		JFactory::getApplication()->redirect('index.php?option='.COMPONENT_OPTION.'&view=pages', JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_CREATED'));
	}
	//----------------------------------------------------------------------------------------------------
	private function multiuploadPagesFromDirectory($publicationId, $pagesTitle)
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.archive');
		
		$jinput = JFactory::getApplication()->input;
		
		$sourceDirName = $jinput->get('server_directory', '', 'STRING');
		
		// Reading publication data.
		
		$db = JFactory::getDBO();
		
		$query = "SELECT * FROM `#__html5fb_publication`" .
			" WHERE `c_id` = " . $publicationId;
		$db->setQuery($query);
		$publication = $db->loadObject();
		
		// Defining source and target directories.
		$sourceDirFullName = JPATH_SITE.'/'.$sourceDirName;
		$targetDirFullName = JPATH_SITE.'/media/'.COMPONENT_OPTION.'/images'.($publication->c_imgsubfolder == '' ? '' : '/'.$publication->c_imgsubfolder);

		// Defining target directory for big image
		$targetDirFullOriginalIMG = JPATH_SITE . '/media/' . COMPONENT_OPTION . '/images' . ($publication->c_imgsubfolder == '' ? '' : '/' . $publication->c_imgsubfolder) . '/original' ;

		if (!JFolder::create($targetDirFullOriginalIMG, 0757))
		{
			$this->showErrorOnPagesMultiupload(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PAGES_CANNOT_CREATE_DIR_BIG_IMG', $targetDirFullOriginalIMG), null);
			return;
		}
		
		// Checking source directory.
		if (!is_dir($sourceDirFullName))
		{
			$this->showErrorOnPagesMultiupload(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PAGES_NO_SERVER_DIR', $sourceDirFullName), null);
			return;
		}

		// Copying appropriate files to temporary directory.
		$extensions = '\.(' .
			'(j|J)(p|P)(g|G)' .
			'|(j|J)(p|P)(e|E)(g|G)' .
			'|(p|P)(n|N)(g|G)' .
			'|(g|G)(i|I)(f|F)' .
			'|(h|H)(t|H)(m|M)' .
			'|(h|H)(t|H)(m|M)(l|L)' .
			'|(t|T)(x|X)(t|T)' .
			')$';

        $allowedHtmlFiles = array('html', 'htm', 'txt');

		$fileNames = JFolder::files($sourceDirFullName, $extensions, false);
		
		if (!$fileNames || count($fileNames) == 0)
		{
			$this->showErrorOnPagesMultiupload(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PAGES_NO_CORRECT_IMAGES_IN_DIR', $sourceDirFullName));
			return;
		}
		
		uasort($fileNames, 'strnatcmp');
		$fileNames = array_values($fileNames);

        // Creating thumbnails.
        try
        {
	        //Create thumb for first page
	        if (isset($fileNames[0]))
	        {
		        $outputFileName = 'thumb_' . $fileNames[0] . "-" . 0 . ".jpg";
		        $outputFilePath = JPATH_SITE . '/media/' . COMPONENT_OPTION . '/thumbs/' . $outputFileName;

		        $image = new JImage();
		        $image->loadFile($sourceDirFullName . '/' . $fileNames[0]);
		        $image->resize(240, 340, FALSE);
		        $image->toFile($outputFilePath, IMAGETYPE_JPEG, array('quality' => 95));
		        $image->destroy();
	        }

            $this->_resize($fileNames, $sourceDirFullName, $publicationId);
        }
        catch (Exception $ex)
        {
            $this->showErrorOnPagesMultiupload(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PAGES_CANNOT_RESIZE_IMAGES', $ex->getMessage()));
            return;
        }

        // Copying files to target directory and deleting temporary directory. Existing files will be overridden.
        $fileNamesToCopy = JFolder::files($sourceDirFullName, $extensions, false);
        $htmlFilesContent = array();

        foreach ($fileNamesToCopy as $fileName)
        {
            if ( in_array(JFile::getExt($fileName), $allowedHtmlFiles) )
            {
                $htmlFilesContent[ basename($fileName) ] = $this->_getStrippedHTML($sourceDirFullName.'/'.$fileName);
            }
            else
            {
	            $fileCopied = false;
	            if ( stripos($fileName, 'thumb_')!==false )
	            {
		            $fileCopied = JFile::copy($sourceDirFullName.'/'.$fileName, $targetDirFullName.'/'.$fileName);
	            }
	            else
	            {
		            $fileCopied = JFile::copy($sourceDirFullName . '/' . $fileName, $targetDirFullOriginalIMG . '/' . $fileName);
	            }

	            if (!$fileCopied)
	            {
		            $this->showErrorOnPagesMultiupload(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PAGES_CANNOT_COPY_FILE_TO_TARGET_DIR', $fileName));
		            return;
	            }
            }
        }

		// Updating database.
		
		$pagesInfoCreated = $this->registerPagesInDB($fileNames, $publicationId, $pagesTitle, $htmlFilesContent, $outputFileName);
		
		if (!$pagesInfoCreated)
		{
			$this->showErrorOnPagesMultiupload(JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_CANNOT_WRITE_TO_DB'));
			return;
		}
		
		// Redirecting.
		
		$app = JFactory::getApplication();
		$app->setUserState(COMPONENT_OPTION.'.pages.filter.publication_id', $publicationId);
		
		JFactory::getApplication()->redirect('index.php?option='.COMPONENT_OPTION.'&view=pages', JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_CREATED'));
	}
	//----------------------------------------------------------------------------------------------------
	private function showErrorOnPagesMultiupload($error, $tempDirName = null)
	{
		if ($tempDirName != null) JFolder::delete($tempDirName);
		
		$jinput = JFactory::getApplication()->input;
		
		$publicationId = $jinput->get('publication_id');
		
		JFactory::getApplication()->redirect('index.php?option='.COMPONENT_OPTION.'&view=pages&layout=multiupload&pubId='.$publicationId, $error, 'error');
	}
	//----------------------------------------------------------------------------------------------------
	private function registerPagesInDB($fileNames, $publicationId, $pagesTitle, $htmlFilesContent = array(), $firstPageThumb = NULL)
	{
		$db = JFactory::getDBO();
		
		// Reading publication data.
		
		$query = "SELECT * FROM `#__html5fb_publication`" .
			" WHERE `c_id` = " . $publicationId;
		$db->setQuery($query);
		$publication = $db->loadObject();
		
		// Receiving max pages ordering index.
		
		$query = "SELECT MAX(ordering) FROM `#__html5fb_pages`" .
			" WHERE `publication_id` = " . $publicationId;
		$db->setQuery($query);
		$maxOrderingIndex = $db->loadResult();
		
		// Writing to database.
		for ($i = 0; $i < count($fileNames); $i++)
		{
			$fileName = $fileNames[$i];
			
			$pageTitle = $pagesTitle . " " . ($maxOrderingIndex + 1 + $i);
			$orderingIndex = $maxOrderingIndex + 1 + $i;


            if ( empty($htmlFilesContent[$fileName]) )
            {
                $query = "INSERT INTO `#__html5fb_pages` (`publication_id`, `page_title`, `page_image`, `ordering`, `c_enable_image`)" .
                    " VALUES (" .
                    $db->quote($publicationId) . ", " .
                    $db->quote($pageTitle) . ", " .
                    $db->quote("thumb_" . $fileName) . ", " .
                    $orderingIndex . ", ".
                    "1" . ")";

                $db->setQuery($query);
                $db->execute();
            }
            else
            {
                $query = "INSERT INTO `#__html5fb_pages` (`publication_id`, `page_title`, `ordering`, `c_enable_image`, `c_enable_text`, `c_text`)" .
                    " VALUES (" .
                    $db->quote($publicationId) . ", " .
                    $db->quote($pageTitle) . ", " .
                    $orderingIndex . ", ".
                    "0," .
                    "1, ".
                    $db->quote( $htmlFilesContent[$fileName] ) . " ".
                    ")";

                $db->setQuery($query);
                $db->execute();
            }
		}

		//Set publication thumbnail
		if (!is_null($firstPageThumb))
		{
            $query = $db->getQuery(true)
                ->select('c_thumb')
                ->from('#__html5fb_publication')
                ->where('`c_id` = ' . $publicationId);
            $db->setQuery($query);
            $thumb = $db->loadResult();
            if(!$thumb){
                $query = $db->getQuery(true)
                    ->update('`#__html5fb_publication`')
                    ->set('`c_thumb` = "' . $firstPageThumb . '"')
                    ->where('`c_id` = ' . $publicationId);
                $db->setQuery($query);
                $db->execute();
            }
		}
		return true;
	}
	//----------------------------------------------------------------------------------------------------
	private function _getStrippedHTML($file)
	{
        $search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
            '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
            '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA
        );
        return preg_replace($search, '', file_get_contents($file));
    }
	//----------------------------------------------------------------------------------------------------
	private function _resize($fileNames, $base_Dir, $pub_id, $replace = false)
	{
		$database = JFactory::getDBO();
		
		$query = "SELECT `c_resolution_id`" .
			" FROM `#__html5fb_publication`" .
			" WHERE `c_id` = '" . $pub_id . "'";
		$database->setQuery($query);
		$res_id = $database->loadResult();
		
		$query = "SELECT *" .
			" FROM `#__html5fb_resolutions`" .
			" WHERE `id` = '" . $res_id . "'";
		$database->setQuery( $query );
		$data_res = $database->loadAssoc();

		$width = $data_res['width'];
		$height = $data_res['height'];
		
		foreach ($fileNames as $fileName)
		{
			if ( preg_match('/(\.[jpg|jpeg|gif|png])/is', $fileName) )
			{
				$inputFilePath = $base_Dir.'/'.$fileName;
				$outputFilePath = $base_Dir.'/thumb_'.$fileName;

                $image = new JImage();
                $image->loadFile($inputFilePath);
                $image->resize($width, $height, FALSE);
                $image->toFile($outputFilePath, IMAGETYPE_JPEG, array('quality' => 95));
                $image->destroy();
			}
		}
	}

	public function set_contents()
	{
		$db = JFactory::getDbo();
		$cid = (int)end( JFactory::getApplication()->input->get('cid', array(), 'array') );

		$db->setQuery("SELECT publication_id FROM #__html5fb_pages WHERE id = ".$cid);
		$pubID = $db->loadResult();

		$db->setQuery("UPDATE `#__html5fb_pages` SET `is_contents` = 0 WHERE `publication_id` = ".(int) $pubID );
		$db->execute();

		$db->setQuery("UPDATE `#__html5fb_pages` SET `is_contents` = 1 WHERE `id` = ".$cid );
		$db->execute();

		JFactory::getApplication()->redirect('index.php?option='.COMPONENT_OPTION.'&view=pages');
	}
}