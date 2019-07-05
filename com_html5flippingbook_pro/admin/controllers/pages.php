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

        echo($return ? '1' : '0');

        jexit();
    }

    //----------------------------------------------------------------------------------------------------
    public function redirect_from_publications()
    {
        $jinput = JFactory::getApplication()->input;

        $publicationId = $jinput->get('pubId', 0, 'INT');

        JFactory::getApplication()->setUserState(COMPONENT_OPTION . '.pages.filter.publication_id', $publicationId);

        JFactory::getApplication()->redirect('index.php?option=' . COMPONENT_OPTION . '&view=pages', '', '');
    }

    //----------------------------------------------------------------------------------------------------
    public function show_multiupload()
    {
        $jinput = JFactory::getApplication()->input;

        $publicationId = $jinput->get('filter_publication_id', 0, 'INT');

        $url = 'index.php?option=' . COMPONENT_OPTION . '&view=pages&layout=multiupload' . ($publicationId == 0 ? '' : '&pubId=' . $publicationId);

        JFactory::getApplication()->redirect($url, '', '');
    }

    public function show_convert()
    {
        $jinput = JFactory::getApplication()->input;

        $publicationId = $jinput->get('filter_publication_id', 0, 'INT');

        if (!class_exists("Imagick")) {
            $this->setMessage(JText::_('ImageMagick is not available on your server. Please contact your server administrator!'), 'warning');
        }

        $url = 'index.php?option=' . COMPONENT_OPTION . '&view=pages&layout=convert' . ($publicationId == 0 ? '' : '&pubId=' . $publicationId);

        $this->setRedirect($url);
    }

    //----------------------------------------------------------------------------------------------------
    public function multiupload()
    {
        require_once(JPATH_COMPONENT_ADMINISTRATOR . '/libs/MethodsForStrings.php');

        if ((int)ini_get('memory_limit') < 128) ini_set('memory_limit', '128M');
        if ((int)ini_get('max_execution_time') < 300) ini_set('max_execution_time', '300');

        $jinput = JFactory::getApplication()->input;

        $sourceType = $jinput->get('source_type');
        $publicationId = $jinput->get('publication_id');
        $pagesTitle = $jinput->get('general_pages_title');

        switch ($sourceType) {
            case 'archive':
                $this->multiuploadPagesFromArchive($publicationId, $pagesTitle);
                break;
            case 'directory':
                $this->multiuploadPagesFromDirectory($publicationId, $pagesTitle);
                break;
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
        $targetDirFullName = JPATH_SITE . '/media/' . COMPONENT_OPTION . '/images' . ($publication->c_imgsubfolder == '' ? '' : '/' . $publication->c_imgsubfolder);

        // Defining target directory for big image
        $targetDirFullOriginalIMG = JPATH_SITE . '/media/' . COMPONENT_OPTION . '/images' . ($publication->c_imgsubfolder == '' ? '' : '/' . $publication->c_imgsubfolder) . '/original';

        // Creating temporary directory for archive unpacking and images resizing.
        $tempDirName = JPATH_SITE . '/tmp/' . COMPONENT_OPTION . '_tmp_' . MethodsForStrings::GenerateRandomString(16, 'lower');

        $folderCreated = JFolder::create($tempDirName, 0757);

        if (!$folderCreated) {
            $this->showErrorOnPagesMultiupload(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PAGES_CANNOT_CREATE_TEMP_DIR', $tempDirName), $tempDirName);
            return;
        }

        if (!JFolder::create($targetDirFullOriginalIMG, 0757)) {
            $this->showErrorOnPagesMultiupload(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PAGES_CANNOT_CREATE_DIR_BIG_IMG', $targetDirFullOriginalIMG), null);
            return;
        }

        // Downloading and checking archive.
        if ($archiveFileObj['name'] == '' || $archiveFileObj['error'] == 1) {
            $this->showErrorOnPagesMultiupload(JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_CANNOT_UPLOAD_ARCHIVE_FILE'), $tempDirName);
            return;
        }

        if (!$archiveFileObj['size']) {
            $maxSize = min((int)ini_get('post_max_size'), (int)ini_get('upload_max_filesize'));

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

        if (!in_array($fileMimeType, $zipMimeTypes)) {
            $this->showErrorOnPagesMultiupload(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PAGES_WRONG_ARCHIVE_TYPE', $fileMimeType), $tempDirName);
            return;
        }

        // Moving archive to temporary directory.
        $archiveFileName = $archiveFileObj['name'];
        $movedArchiveFileFullName = $tempDirName . '/' . $archiveFileName;

        if (!move_uploaded_file($archiveFileObj['tmp_name'], $movedArchiveFileFullName) || !JPath::setPermissions($movedArchiveFileFullName)) {
            $this->showErrorOnPagesMultiupload(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PAGES_CANNOT_MOVE_ARCHIVE', $archiveFileName), $tempDirName);
            return;
        }

        // Unpacking and deleting archive.
        $archiveUnpacked = JArchive::extract($movedArchiveFileFullName, $tempDirName);

        if (!$archiveUnpacked) {
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

        if (!$fileNames || count($fileNames) == 0) {
            $this->showErrorOnPagesMultiupload(JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_NO_CORRECT_IMAGES_IN_ARCHIVE'), $tempDirName);
            return;
        }

        uasort($fileNames, 'strnatcmp');
        $fileNames = array_values($fileNames);

        // Creating thumbnails.
        try {
            //Create thumb for first page
            if (isset($fileNames[0])) {
                $outputFileName = "thumb_{$publicationId}" . $fileNames[0] . "-" . 0 . ".jpg";
                $outputFilePath = JPATH_SITE . '/media/' . COMPONENT_OPTION . '/thumbs/' . $outputFileName;

                $image = new JImage();
                $image->loadFile($tempDirName . '/' . $fileNames[0]);
                $image->resize(240, 340, FALSE);
                $image->toFile($outputFilePath, IMAGETYPE_JPEG, array('quality' => 95));
                $image->destroy();
            }

            $this->_resize($fileNames, $tempDirName, $publicationId);
        } catch (Exception $ex) {
            $this->showErrorOnPagesMultiupload(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PAGES_CANNOT_RESIZE_IMAGES', $ex->getMessage()), $tempDirName);
            return;
        }

        // Copying files to target directory and deleting temporary directory. Existing files will be overridden.
        $fileNamesToCopy = JFolder::files($tempDirName, $extensions, false);
        $htmlFilesContent = array();

        foreach ($fileNamesToCopy as $fileName) {
            if (in_array(JFile::getExt($fileName), $allowedHtmlFiles)) {
                $htmlFilesContent[basename($fileName)] = $this->_getStrippedHTML($tempDirName . '/' . $fileName);
            } else {
                $fileCopied = false;
                if (stripos($fileName, 'thumb_') !== false) {
                    $fileCopied = JFile::copy($tempDirName . '/' . $fileName, $targetDirFullName . '/' . $fileName);
                } else {
                    $fileCopied = JFile::copy($tempDirName . '/' . $fileName, $targetDirFullOriginalIMG . '/' . $publicationId . $fileName);
                }

                if (!$fileCopied) {
                    $this->showErrorOnPagesMultiupload(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PAGES_CANNOT_COPY_FILE_TO_TARGET_DIR', $fileName));
                    return;
                }
            }
        }

        @JFolder::delete($tempDirName);

        // Updating database.
        $pagesInfoCreated = $this->registerPagesInDB($fileNames, $publicationId, $pagesTitle, $htmlFilesContent, NULL, $outputFileName);

        if (!$pagesInfoCreated) {
            $this->showErrorOnPagesMultiupload(JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_CANNOT_WRITE_TO_DB'), $tempDirName);
            return;
        }

        // Redirecting.
        $app = JFactory::getApplication();
        $app->setUserState(COMPONENT_OPTION . '.pages.filter.publication_id', $publicationId);

        JFactory::getApplication()->redirect('index.php?option=' . COMPONENT_OPTION . '&view=pages', JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_CREATED'));
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
        $sourceDirFullName = JPATH_SITE . '/' . $sourceDirName;
        $targetDirFullName = JPATH_SITE . '/media/' . COMPONENT_OPTION . '/images' . ($publication->c_imgsubfolder == '' ? '' : '/' . $publication->c_imgsubfolder);

        // Defining target directory for big image
        $targetDirFullOriginalIMG = JPATH_SITE . '/media/' . COMPONENT_OPTION . '/images' . ($publication->c_imgsubfolder == '' ? '' : '/' . $publication->c_imgsubfolder) . '/original';

        if (!JFolder::create($targetDirFullOriginalIMG, 0757)) {
            $this->showErrorOnPagesMultiupload(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PAGES_CANNOT_CREATE_DIR_BIG_IMG', $targetDirFullOriginalIMG), null);
            return;
        }

        // Checking source directory.
        if (!is_dir($sourceDirFullName)) {
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

        if (!$fileNames || count($fileNames) == 0) {
            $this->showErrorOnPagesMultiupload(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PAGES_NO_CORRECT_IMAGES_IN_DIR', $sourceDirFullName));
            return;
        }

        uasort($fileNames, 'strnatcmp');
        $fileNames = array_values($fileNames);

        // Creating thumbnails.
        try {
            //Create thumb for first page
            if (isset($fileNames[0])) {
                $outputFileName = 'thumb_' . $fileNames[0] . "-" . 0 . ".jpg";
                $outputFilePath = JPATH_SITE . '/media/' . COMPONENT_OPTION . '/thumbs/' . $outputFileName;

                $image = new JImage();
                $image->loadFile($sourceDirFullName . '/' . $fileNames[0]);
                $image->resize(240, 340, FALSE);
                $image->toFile($outputFilePath, IMAGETYPE_JPEG, array('quality' => 95));
                $image->destroy();
            }

            $this->_resize($fileNames, $sourceDirFullName, $publicationId);
        } catch (Exception $ex) {
            $this->showErrorOnPagesMultiupload(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PAGES_CANNOT_RESIZE_IMAGES', $ex->getMessage()));
            return;
        }

        // Copying files to target directory and deleting temporary directory. Existing files will be overridden.
        $fileNamesToCopy = JFolder::files($sourceDirFullName, $extensions, false);
        $htmlFilesContent = array();

        foreach ($fileNamesToCopy as $fileName) {
            if (in_array(JFile::getExt($fileName), $allowedHtmlFiles)) {
                $htmlFilesContent[basename($fileName)] = $this->_getStrippedHTML($sourceDirFullName . '/' . $fileName);
            } else {
                $fileCopied = false;
                if (stripos($fileName, 'thumb_') !== false) {
                    $fileCopied = JFile::copy($sourceDirFullName . '/' . $fileName, $targetDirFullName . '/' . $fileName);
                } else {
                    $fileCopied = JFile::copy($sourceDirFullName . '/' . $fileName, $targetDirFullOriginalIMG . '/' . $fileName);
                }

                if (!$fileCopied) {
                    $this->showErrorOnPagesMultiupload(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PAGES_CANNOT_COPY_FILE_TO_TARGET_DIR', $fileName));
                    return;
                }
            }
        }

        // Updating database.

        $pagesInfoCreated = $this->registerPagesInDB($fileNames, $publicationId, $pagesTitle, $htmlFilesContent, NULL, $outputFileName);

        if (!$pagesInfoCreated) {
            $this->showErrorOnPagesMultiupload(JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_CANNOT_WRITE_TO_DB'));
            return;
        }

        // Redirecting.

        $app = JFactory::getApplication();
        $app->setUserState(COMPONENT_OPTION . '.pages.filter.publication_id', $publicationId);

        JFactory::getApplication()->redirect('index.php?option=' . COMPONENT_OPTION . '&view=pages', JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_CREATED'));
    }

    // Make a function for convenience
    protected function _getPDFPages($document)
    {
        $stream = fopen($document, "r");
        $content = fread($stream, filesize($document));

        if (!$stream || !$content)
            return 0;

        $firstValue = 0;
        $secondValue = 0;
        if (preg_match("/\/N\s+(\d+)/", $content, $matches)) {
            $firstValue = $matches[1];
        }

        if (preg_match_all("/\/Count\s+(\d+)/s", $content, $matches)) {
            $secondValue = max($matches[1]);
        }

        return (($secondValue != 0) ? $secondValue : max($firstValue, $secondValue));
    }

    public function takefile()
    {

        require_once(JPATH_COMPONENT_ADMINISTRATOR . '/libs/MethodsForStrings.php');

        $publicationId = $this->input->get('publication_id', '', 'INT');
        $pagesTitle = $this->input->get('general_pages_title', '', 'WORD');
        $quality = $this->input->get('image_quality', 100, 'INT');

        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder');

        $pdfFile = $this->input->files->get('pdf_file');

        // Reading publication data.
        $db = JFactory::getDBO();

        $query = $db->getQuery(true)
            ->select('*')
            ->from('`#__html5fb_publication`')
            ->where('`c_id` = ' . $publicationId);
        $db->setQuery($query);
        $publication = $db->loadObject();


        // Defining temporary directory
//        $tmpFolder = MethodsForStrings::GenerateRandomString(16, 'lower');
//        $tempDirName = JPATH_SITE . '/tmp/' . COMPONENT_OPTION . '_tmp_' . $tmpFolder;

        //Defining PDF directory
        $pdfDirName = JPATH_SITE . '/media/' . COMPONENT_OPTION . '/pdf';

//        if (!JFolder::create($tempDirName, 0757))
//        {
//            $this->setMessage(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PAGES_CANNOT_CREATE_TEMP_DIR', $tempDirName), 'WARNING');
//            $this->setRedirect('index.php?option='.COMPONENT_OPTION.'&view=pages&layout=convert');
//            return;
//        }

        // Downloading and checking archive.
        if ($pdfFile['name'] == '' || $pdfFile['error'] == 1) {
            $this->setMessage(JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_CANNOT_UPLOAD_PDF_FILE'), 'WARNING');
            $this->setRedirect('index.php?option=' . COMPONENT_OPTION . '&view=pages&layout=convert');
            return;
        }

        if (!$pdfFile['size']) {
            $maxSize = min((int)ini_get('post_max_size'), (int)ini_get('upload_max_filesize'));
            $this->setMessage(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PAGES_TOO_LARGE_FILE', $maxSize), 'WARNING');
            $this->setRedirect('index.php?option=' . COMPONENT_OPTION . '&view=pages&layout=convert');
            return;
        }

        // Checking MIME type.
        $fileMimeType = $pdfFile['type'];

        $allowedMimeTypes = array(
            'application/octet-binary',
            'application/pdf',
            'application/x-pdf',
            'application/acrobat',
            'applications/vnd.pdf',
            'text/pdf',
            'text/x-pdf',
            'application/force-download'
        );

        if (!in_array($fileMimeType, $allowedMimeTypes)) {
            $this->setMessage(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PAGES_WRONG_PDF_TYPE', $fileMimeType), 'WARNING');
            $this->setRedirect('index.php?option=' . COMPONENT_OPTION . '&view=pages&layout=convert');
            return;
        }

        // Moving pdf to pdf directory.
        $pdfFileName = $pdfFile['name'];
        $pdfFileName = str_replace(" ", "_", $pdfFileName);
        $PDFFileFullName = $pdfDirName . '/' . $pdfFileName;
//        $tempPDFFileFullName = $tempDirName . '/' . $pdfFileName;

        if (!JFile::move($pdfFile['tmp_name'], $PDFFileFullName) || !JPath::setPermissions($PDFFileFullName)) {
            $this->setMessage(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PAGES_CANNOT_MOVE_PDF', $pdfFileName, $pdfDirName), 'WARNING');
            $this->setRedirect('index.php?option=' . COMPONENT_OPTION . '&view=pages&layout=convert');
            return;
        }

//        if (!JFile::copy($movedPDFFileFullName, $tempPDFFileFullName) || !JPath::setPermissions($tempPDFFileFullName))
//        {
//            $this->setMessage(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PAGES_CANNOT_MOVE_PDF', $pdfFileName, $tempDirName), 'WARNING');
//            $this->setRedirect('index.php?option='.COMPONENT_OPTION.'&view=pages&layout=convert');
//            return;
//        }


        if (class_exists('Imagick')) {
            $img = new Imagick();
            $img->readImage($PDFFileFullName);
            $count = $img->getNumberImages();
        } elseif (!class_exists('Imagick') && function_exists('exec')) {
            // Determine num of pages
            $count = (int)$this->_getPDFPages($PDFFileFullName);
        }
        $imgName = MethodsForStrings::GenerateRandomString(5, 'lower');
        $dir = base64_encode($PDFFileFullName);
        $this->setRedirect('index.php?' .
            'option=' . COMPONENT_OPTION .
            '&view=pages' .
            '&layout=progressbar' .
            '&fName=' . $pdfFileName .
            '&imgName=' . $imgName .
            '&publication_id=' . $publicationId .
            '&general_pages_title=' . $pagesTitle .
            '&image_quality=' . $quality .
            '&count=' . $count
        );
    }

    public function convert()
    {

        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder');

        require_once(JPATH_COMPONENT_ADMINISTRATOR . '/libs/MethodsForStrings.php');

        $publicationId = $this->input->get('publication_id', '', 'INT');
        $islast = $this->input->get('islast', '', 'INT');
        $pagesTitle = $this->input->get('general_pages_title', '', 'WORD');
        $fName = $this->input->getString('fName');
        $imgName = $this->input->get('imgName');
        $quality = $this->input->get('image_quality', 100, 'INT');

        $pdfDirName = JPATH_SITE . '/media/' . COMPONENT_OPTION . '/pdf';
        $PDFFileFullName = $pdfDirName . '/' . $fName;

        $db = JFactory::getDBO();

        $query = $db->getQuery(true)
            ->select('*')
            ->from('`#__html5fb_publication`')
            ->where('`c_id` = ' . $publicationId);
        $db->setQuery($query);
        $publication = $db->loadObject();

        $query = $db->getQuery(true)
            ->select('*')
            ->from('`#__html5fb_resolutions`')
            ->where('`id` = ' . $publication->c_resolution_id);
        $db->setQuery($query);
        $data_res = $db->loadAssoc();

        // Defining target directory
        $targetDirFullName = JPATH_SITE . '/media/' . COMPONENT_OPTION . '/images' . ($publication->c_imgsubfolder == '' ? '' : '/' . $publication->c_imgsubfolder);

        // Defining target directory for big image
        $targetDirFullOriginalIMG = JPATH_SITE . '/media/' . COMPONENT_OPTION . '/images' . ($publication->c_imgsubfolder == '' ? '' : '/' . $publication->c_imgsubfolder) . '/original';

        if (!JFolder::create($targetDirFullOriginalIMG, 0757)) {
            $this->setMessage(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PAGES_CANNOT_CREATE_DIR_BIG_IMG', $targetDirFullOriginalIMG), 'WARNING');
            $this->setRedirect('index.php?option=' . COMPONENT_OPTION . '&view=pages&layout=convert');
            return;
        }

        if ((int)ini_get('memory_limit') < 128) ini_set('memory_limit', '128M');
        if ((int)ini_get('max_execution_time') < 300) ini_set('max_execution_time', '300');

        $app = JFactory::getApplication();
        $app->setUserState(COMPONENT_OPTION . '.pages.filter.publication_id', $publicationId);

        set_time_limit(0);

        putenv('TMPDIR=' . JPATH_SITE . '/' . trim(JFactory::getApplication()->getCfg('tmp_path', 'tmp'), '/\\') . '/');
        putenv('MAGICK_TMPDIR=' . JPATH_SITE . '/' . trim(JFactory::getApplication()->getCfg('tmp_path', 'tmp'), '/\\') . '/');
        putenv('MAGICK_TEMPORARY_PATH=' . JPATH_SITE . '/' . trim(JFactory::getApplication()->getCfg('tmp_path', 'tmp'), '/\\') . '/');

        $params = JComponentHelper::getParams('com_html5flippingbook');

        // multiplier from bytes to mbytes
        $mbytes = 1024 * 1024;

        // to reach good quality need "2" multiplier atleast
        $density = (int)$params->get('density', 300);
        $limit_area = (int)$params->get('limit_area', 30) * $mbytes;
        $limit_memory = (int)$params->get('limit_memory', 30) * $mbytes;
        $page_number = $this->input->get('pageNumb', '', 'INT') - 1;
        $outputFileName = 'thumb_' . $imgName . "-0.jpg";

        if (class_exists('Imagick')) {
            // ** setting width and height causes "Invalid IHDR data" with density(192 or 300)
            // Assume that everage user monitor is 1920x1080 so setting up max image sizes
            //Set max image width of 960 (1960/2)
//            Imagick::setResourceLimit(9, (int)$params->get('max_width', 960) * 2);
            //Set max image height of 1080
//            Imagick::setResourceLimit(10, (int)$params->get('max_height', 1080) * 2);
            if (!$params->get('reach_out_of_limits', 0)) {
                Imagick::setResourceLimit(Imagick::RESOURCETYPE_AREA, $limit_area);
                Imagick::setResourceLimit(Imagick::RESOURCETYPE_MEMORY, $limit_memory);
            }

            // commented as causes errors on some systems "Too many IDAT's found"
//            Imagick::setResourceLimit(Imagick::RESOURCETYPE_DISK, 10*$mbytes);

            // Convert PDF document page

            $img = new Imagick();
            $img->setResolution($density, $density);
            try {
                $img->readImage($PDFFileFullName . "[$page_number]");
            } catch (Exception $e) {
                die();
            }

            $cls_v = $img->getversion();
            /*
			// Example of the array "version":
               <pre>Array
                (
                    [versionNumber] => 1684
                    [versionString] => ImageMagick 6.9.4-10 Q16 x86_64 2017-11-14 http://www.imagemagick.org
                )
                </pre>
             */
            preg_match('/ImageMagick ([0-9]+\.[0-9]+\.[0-9]+)/', $cls_v['versionString'], $cls_v);
            
            if (version_compare($cls_v[1], '6.5.7', '>=')) {
                $img->setColorspace(Imagick::COLORSPACE_SRGB);
            }
            
            $output_big = $targetDirFullOriginalIMG . "/" . $imgName . "-" . $page_number . ".jpg";
            $output_thumb = $targetDirFullName . "/th_" . $imgName . "-" . $page_number . ".jpg";

            // Set iterator postion
//				$img->setIteratorIndex($i);

            // Set image format
            $img->setImageFormat('jpeg');

            //Remove opacity
            if(method_exists($img,'setImageAlpha')){
                $img->setImageAlpha(1.0);
            }elseif(method_exists($img,'setOpacity')){
                $img->setOpacity(1.0);
            }

            $img->setImageCompression(Imagick::COMPRESSION_JPEG);

            // Compress Image Quality
            $img->setImageCompressionQuality(100);

            //Remove alpha channel
            if (version_compare($cls_v[1], '6.3.8', '>=')) {
                if ($img->getImageAlphaChannel() !== 0) {
                    $alphaChannel = 11;
                    if (defined("Imagick::ALPHACHANNEL_RESET")) {
                        $alphaChannel= Imagick::ALPHACHANNEL_RESET;
                    }
                    $img->setImageAlphaChannel($alphaChannel);
                    //$img->setImageAlphaChannel(Imagick::ALPHACHANNEL_DEACTIVATE);
                }
            }

            // Prevents black background on objects with transparency
            $img->setImageBackgroundColor('white');

            /*if (version_compare($cls_v[1], '6.3.7', '>=')) {
                $img->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
            } else {
                $img->flattenImages();
            } */

            $img->scaleImage($params->get('max_width', 960),$params->get('max_height', 1200), true);

            // Write big images to the temp 'upload' folder
            $img->writeImage($output_big);

            // Resize the image
            $img->resizeimage($data_res['width'], $data_res['height'], Imagick::FILTER_LANCZOS, 1);

            // Write small images to the temp 'upload' folder
            $img->writeImage($output_thumb);

            //Create thumb for first page
            if ($page_number == 0) {
                $outputFilePath = JPATH_SITE . '/media/' . COMPONENT_OPTION . '/thumbs/' . $outputFileName;
                $image = new JImage();
                $image->loadFile($output_thumb);
                $image->resize(240, 340, FALSE);
                $image->toFile($outputFilePath, IMAGETYPE_JPEG, array('quality' => 95));
                $image->destroy();
            }

            $img->destroy();
        } elseif (!class_exists('Imagick') && function_exists('exec')) {
            // Determine num of pages
//            $num_pages = (int)$this->_getPDFPages($PDFFileFullName);

            // Convert PDF pages to images
            $size = "-resize " . $data_res['width'] . "x" . $data_res['height'] . "!";
            $quality = "-quality " . $quality;

            $input = $PDFFileFullName . "[$page_number]";
            $output = $targetDirFullOriginalIMG . "/" . $imgName . "-" . $page_number . ".jpg";
            $output_thumb = $targetDirFullName . "/th_" . $imgName . "-" . $page_number . ".jpg";

            //ImageMagic console command for convert pdf page into image file
            exec("convert -limit area $limit_area -limit memory $limit_memory -density $density -colorspace sRGB " . $input . " " . $quality . " -background white -alpha remove " . $output);
            exec("convert -limit area $limit_area -limit memory $limit_memory -density $density -colorspace sRGB " . $input . " " . $size . " " . $quality . " -background white -alpha remove " . $output_thumb, $b);

            //Create thumb for first page
            if ($page_number == 0 && $b == 0) {
                $outputFilePath = JPATH_SITE . '/media/' . COMPONENT_OPTION . '/thumbs/' . $outputFileName;
                $image = new JImage();
                $image->loadFile($output_thumb);
                $image->resize(240, 340, FALSE);
                $image->toFile($outputFilePath, IMAGETYPE_JPEG, array('quality' => 95));
                $image->destroy();
            }
        }
        $pagesInfoCreated = $this->registerPagesInDB([$imgName . "-" . $page_number . ".jpg"], $publicationId, $pagesTitle, array(), $fName, $outputFileName);

        if (!$pagesInfoCreated) {
            $this->setMessage(JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_CANNOT_WRITE_TO_DB'), 'WARNING');
            $this->setRedirect('index.php?option=' . COMPONENT_OPTION . '&view=pages&layout=convert');
            return;
        }

        if ($islast) {
            echo 1;
        }
        die();
    }

    //----------------------------------------------------------------------------------------------------
    private function showErrorOnPagesMultiupload($error, $tempDirName = null)
    {
        if ($tempDirName != null) @JFolder::delete($tempDirName);

        $jinput = JFactory::getApplication()->input;

        $publicationId = $jinput->get('publication_id');

        JFactory::getApplication()->redirect('index.php?option=' . COMPONENT_OPTION . '&view=pages&layout=multiupload&pubId=' . $publicationId, $error, 'error');
    }

    //----------------------------------------------------------------------------------------------------
    private function registerPagesInDB($fileNames, $publicationId, $pagesTitle, $htmlFilesContent = array(), $pdfFile = NULL, $firstPageThumb = NULL)
    {
        $db = JFactory::getDBO();

        // Receiving max pages ordering index.
        $query = "SELECT MAX(ordering) FROM `#__html5fb_pages`" .
            " WHERE `publication_id` = " . $publicationId;
        $db->setQuery($query);
        $maxOrderingIndex = $db->loadResult();

        // Writing to database.
        for ($i = 0; $i < count($fileNames); $i++) {
            $fileName = $fileNames[$i];

            $pageTitle = $pagesTitle . " " . ($maxOrderingIndex + 1 + $i);
            $orderingIndex = $maxOrderingIndex + 1 + $i;


            if (empty($htmlFilesContent[$fileName])) {
                $query = $db->getQuery(true)
                    ->insert('`#__html5fb_pages`')
                    ->columns(
                        array(
                            $db->quoteName('publication_id'), $db->quoteName('page_title'), $db->quoteName('page_image'),
                            $db->quoteName('ordering'), $db->quoteName('c_enable_image')
                        )
                    )
                    ->values($db->quote($publicationId) . ', ' . $db->quote($pageTitle) . ', ' . $db->quote((is_null($pdfFile) ? "thumb_{$publicationId}" . $fileName : $fileName)) . ', ' . $orderingIndex . ', 1');
                $db->setQuery($query);
                $db->execute();
            } else {
                $query = $db->getQuery(true)
                    ->insert('`#__html5fb_pages`')
                    ->columns(
                        array(
                            $db->quoteName('publication_id'), $db->quoteName('page_title'), $db->quoteName('c_text'),
                            $db->quoteName('ordering'), $db->quoteName('c_enable_text'), $db->quoteName('c_enable_image')
                        )
                    )
                    ->values($db->quote($publicationId) . ', ' . $db->quote($pageTitle) . ', ' . $db->quote($htmlFilesContent[$fileName]) . ', ' . $orderingIndex . ', 1, 0');
                $db->setQuery($query);
                $db->execute();
            }
        }
        //Assign PDF file to publication
        if (!is_null($pdfFile)) {
            $query = $db->getQuery(true)
                ->update('`#__html5fb_publication`')
                ->set('`c_enable_pdf` = 1')
                ->set('`c_background_pdf` = "' . $pdfFile . '"')
                ->where('`c_id` = ' . $publicationId);
            $db->setQuery($query);
            $db->execute();
        }
        //Set publication thumbnail
        if (!is_null($firstPageThumb)) {
            $query = $db->getQuery(true)
                ->select('c_thumb')
                ->from('#__html5fb_publication')
                ->where('`c_id` = ' . $publicationId);
            $db->setQuery($query);
            $thumb = $db->loadResult();
            if (!$thumb) {
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
        $database->setQuery($query);
        $data_res = $database->loadAssoc();

        $width = $data_res['width'];
        $height = $data_res['height'];

        foreach ($fileNames as $fileName) {
            if (preg_match('/(\.[jpg|jpeg|gif|png])/is', $fileName)) {
                $inputFilePath = $base_Dir . '/' . $fileName;
                $outputFilePath = $base_Dir . "/thumb_{$pub_id}" . $fileName;

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
        $cid = (int)end(JFactory::getApplication()->input->get('cid', array(), 'array'));

        $db->setQuery("SELECT publication_id FROM #__html5fb_pages WHERE id = " . $cid);
        $pubID = $db->loadResult();

        $db->setQuery("UPDATE `#__html5fb_pages` SET `is_contents` = 0 WHERE `publication_id` = " . (int)$pubID);
        $db->execute();

        $db->setQuery("UPDATE `#__html5fb_pages` SET `is_contents` = 1 WHERE `id` = " . $cid);
        $db->execute();

        JFactory::getApplication()->redirect('index.php?option=' . COMPONENT_OPTION . '&view=pages');
    }
}