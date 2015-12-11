<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

class HTML5FlippingBookViewUpload_File_Dialog extends JViewLegacy
{
	protected $maxSize;

    public $mediaDir = 'media/com_html5flippingbook/';
    protected $e_name;
    protected $type;
    protected $dir;
    protected $dirUrl;

    protected $filesList;

    protected $extensions;
    protected $videoExtensions = array('mp4', 'ogg', 'ogv', 'webm');
    protected $audioExtensions = array('mp3', 'ogg', 'wav', 'm4a');

	//----------------------------------------------------------------------------------------------------
	public function display($tpl = null)
	{
		$jinput = JFactory::getApplication()->input;

        $this->type = $jinput->get('type');
        $this->e_name = $jinput->get('e_name');

        $this->dir = JPATH_SITE.'/'.$this->mediaDir.$this->type;
        $this->dirUrl = JURI::root().$this->mediaDir.$this->type.'/';

		$this->extensions = ( $this->type == 'video' ? $this->videoExtensions : $this->audioExtensions);
		$this->maxSize = min((int) ini_get('post_max_size'), (int) ini_get('upload_max_filesize'));
        $this->filesList = $this->getExistsFiles();

		if (isset($_FILES['userfile']))
		{
			$this->upload();
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
    protected function getExistsFiles()
    {
        jimport('joomla.filesystem.folder');
        $fileNames = JFolder::files($this->dir, $this->getFileExtensionsFilter());
        return ($fileNames === false ? array() : $fileNames);
    }

    //----------------------------------------------------------------------------------------------------
    protected function getFileExtensionsFilter()
    {
        $filter = '\.(';

        foreach ($this->extensions as $key => $extension)
        {
            $extensionFilter = '';
            $letters = str_split($extension);

            foreach ($letters as $letter)
                $extensionFilter .= '(' . $letter . '|' . strtoupper($letter) . ')';

            $filter .= ($key == 0 ? '' : '|') . $extensionFilter;
        }

        $filter .= ')$';

        return $filter;
    }

    //----------------------------------------------------------------------------------------------------
    private function showJSResult($js)
    {
        echo '<script type="text/javascript">jQuery(document).ready(function (){'."\n"
                .$js
            ."\n }); </script>";
    }

	//----------------------------------------------------------------------------------------------------
	private function upload()
	{
		jimport('joomla.filesystem.file');
		
		$userFileName = (isset($_FILES['userfile']['name']) ? $_FILES['userfile']['name'] : '');
		
		// Checking not specified file.
		if (!isset($_FILES['userfile']) || empty($userFileName))
		{
			$this->showJSResult('alert("' . JText::_('COM_HTML5FLIPPINGBOOK_BE_FILE_UPLOAD_SELECT_FILE_WARNING') . '")');
			return;
		}

		// Replace not allowed characters.
        $userFileName = str_replace(str_split(preg_replace('/([[:alnum:]_\.-]*)/','-',$userFileName)),'-',$userFileName);

		// Checking file extension.
        $userFileExtension = strtolower(JFile::getExt($userFileName));

        if (!in_array($userFileExtension, $this->extensions))
        {
            $this->showJSResult('alert("' . JText::_('COM_HTML5FLIPPINGBOOK_BE_FILE_UPLOAD_TYPE_WARNING') . ': ' . implode(', ', $this->extensions) . '")');
            return;
        }

        // Check exists dir
		@mkdir($this->dir, 0755);

		// Detecting file replacement.
		$fileIsBeingReplaced = file_exists($this->dir.'/'.$userFileName);
		
		// Moving uploaded file.
		if ( !move_uploaded_file($_FILES['userfile']['tmp_name'], $this->dir.'/'.$_FILES['userfile']['name']) || !JPath::setPermissions($this->dir.'/'.$_FILES['userfile']['name']) )
		{
			$this->showJSResult('alert("' . JText::_('COM_HTML5FLIPPINGBOOK_BE_FILE_UPLOAD_FAILED') . ': ' . $userFileName . '")');
			return;
		}
		
		// Handling successfull upload.
        $this->showJSResult('html5fbOnFileUploadedToList("' . $userFileName . '", ' . ($fileIsBeingReplaced ? 'true' : 'false') . ');');
	}
}