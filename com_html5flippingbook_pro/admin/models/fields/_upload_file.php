<?php defined('_JEXEC') or die('Restricted Access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

JFormHelper::loadFieldClass('list');

//==================================================
// List of files and upload button.
//==================================================
class JFormField_Upload_File extends JFormFieldList
{
	protected $type = '_upload_file';
	protected $dir;
	protected $pubid;
	protected $dirUrl;
	protected $filter;
	protected $listTitle;
	protected $successMessage;
	protected $tooltipTitle;
	protected $tooltipText;
	protected $linkedElementIds;
	protected $fileExtensions;
	protected $fileExtensionsObj;
	protected $fileNames;
	//----------------------------------------------------------------------------------------------------
	public function __construct($form = null)
	{
		parent::__construct($form);
	}
	//----------------------------------------------------------------------------------------------------
	public function getLabel()
	{
		return parent::getLabel();
	}
	//----------------------------------------------------------------------------------------------------
	public function getInput()
	{
		$this->dir = JPATH_SITE.'/'.$this->element['dir'];
		$this->pubid = 0;
		$this->dirUrl = JURI::root().$this->element['dir'].'/';
		$this->listTitle = ($this->element['listTitle'] ? $this->element['listTitle'] : 'COM_HTML5FLIPPINGBOOK_BE_SELECT_FILE');
		$this->successMessage = ($this->element['successMessage'] ? $this->element['successMessage'] : 'COM_HTML5FLIPPINGBOOK_BE_FILE_UPLOAD_SUCCESS');
		$this->tooltipTitle = ($this->element['tooltipTitle'] ? $this->element['tooltipTitle'] : 'COM_HTML5FLIPPINGBOOK_BE_FILE_UPLOAD_TOOLTIP_TITLE');
		$this->tooltipText = ($this->element['tooltipText'] ? $this->element['tooltipText'] : 'COM_HTML5FLIPPINGBOOK_BE_FILE_UPLOAD_TOOLTIP_TEXT');
		$this->linkedElementIds = ($this->element['linkedElementIds'] ? $this->element['linkedElementIds'] : '');
		$this->fileExtensions = ($this->element['fileExtensions'] ? $this->element['fileExtensions'] : 'png,jpg,gif');
		
		$this->fileExtensionsObj = $this->getFileExtensionsObject($this->fileExtensions);
		
		$this->fileNames = $this->getFileNames();
		
		$html = array();
		$attr = '';
		$this->multiple = false;
		
		ob_start();
		?>
		<script type="text/javascript">
			
			function html5fbOnFileUploadedToList_<?php echo $this->id; ?>(fileName, isTargetElement, fileIsBeingReplaced)
			{
				if (isTargetElement)
				{
					if (!fileIsBeingReplaced)
					{
						BootstrapFormHelper.addOptionToSelectList('<?php echo $this->id; ?>', fileName, fileName, true);
					}
					else
					{
						BootstrapFormHelper.selectOptionInSelectList('<?php echo $this->id; ?>', fileName);
					}
					
					var uploadResultElement = document.getElementById('<?php echo $this->id; ?>_result');
					
					html5fbAnimateFileUploadResult_<?php echo $this->id; ?>(uploadResultElement);
					
					SqueezeBox.close();
				}
				else
				{
					if (!fileIsBeingReplaced)
					{
						BootstrapFormHelper.addOptionToSelectList('<?php echo $this->id; ?>', fileName, fileName, false);
					}
				}
			}
			
			function html5fbAnimateFileUploadResult_<?php echo $this->id; ?>(element)
			{
				element.style.opacity = 1;
				
				var intervalId = setInterval(function() { html5fbAnimateFileUploadStep_<?php echo $this->id; ?>(element, intervalId); }, 150);
			}
			
			function html5fbAnimateFileUploadStep_<?php echo $this->id; ?>(element, intervalId)
			{
				var opacity = parseFloat(element.style.opacity);
				
				if (isNaN(opacity) || opacity < 0) opacity = 0;
				
				opacity -= 0.05;
				
				if (opacity < 0) opacity = 0;
				
				element.style.opacity = opacity;
				
				if (opacity == 0) clearInterval(intervalId);
			}
			
		</script>
		<?php
		$addjs = ob_get_contents();
		ob_get_clean();
		
		$html[] = $addjs;
		
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		
		if ((string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true')
		{
			$attr .= ' disabled="disabled"';
		}
		
		$attr .= ($this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '');
		$attr .= ($this->multiple ? ' multiple="multiple"' : '');
		
		$options = (array) $this->getOptions();
		array_unshift($options, JHTML::_('select.option', '', JText::_($this->listTitle)));
		
		if ((string) $this->element['readonly'] == 'true')
		{
			$html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);
			$html[] = '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'"/>';
		}
		else
		{
			$html[] = '<div class="'.$this->element['class'].'_select_wrapper">';
			$html[] = 	JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
			$html[] = 	'<div id="' . $this->id . '_result" class="'.$this->element['class'].'_result" style="opacity:0;">';
			$html[] = 		'* ' . JText::_($this->successMessage);
			$html[] = 	'</div>';
			$html[] = '</div>';
			$html[] = '<div class="'.$this->element['class'].'_btn">';
			$html[] = 	'<span class="hasTip" title="' . JText::_($this->tooltipTitle) . '::<small>* ' . JText::_($this->tooltipText) . ': ' .
							JText::_($this->fileExtensionsObj->tooltip) . '</small>">';
			$html[] =		'<a class="modal"' .
								' href="' . 'index.php?option='.COMPONENT_OPTION.'&tmpl=component' .
								'&task=' . $this->element['uploadTask'] .
								'&dir=' . rawurlencode($this->element['dir']) .
								'&pubid=' . $this->element['pubid'] .
								'&extensions=' . rawurlencode($this->fileExtensionsObj->fixedStr) .
								'&elementId=' . $this->id .
								($this->linkedElementIds == '' ? '' : '&linkedElementIds=' . rawurlencode($this->linkedElementIds)) .
								'" rel="{handler: \'iframe\', size: {x: 400, y: 110}}">';
			$html[] =			'<img src="' . COMPONENT_IMAGES_URL.'upload.png' . '" width="16" height="16" />';
			$html[] = 		'</a>';
			$html[] = 	'</span>';
			$html[] = '</div>';
		}
		
		return implode('', $html);
	}
	//----------------------------------------------------------------------------------------------------
	public function setProperty($name, $value)
	{
		$this->element[$name] = $value;
	}
	//----------------------------------------------------------------------------------------------------
	protected function getOptions()
	{
		$options = array();
		
		foreach ($this->fileNames as $fileName)
		{
			$options[] = JHTML::_('select.option', $fileName, $fileName);
		}
		
		return $options;
	}
	//----------------------------------------------------------------------------------------------------
	protected function getFileNames()
	{
		$fileNames = JFolder::files($this->dir, $this->fileExtensionsObj->filter);
		
		if ($fileNames === false) $fileNames = array();
		
		return $fileNames;
	}
	//----------------------------------------------------------------------------------------------------
	protected function getFileExtensionsObject($fileExtensionsStr)
	{
		$filter = '.';
		$tooltip = '*.*';
		$fixedStr = '';
		
		if ($fileExtensionsStr != '')
		{
			$extensions = array_filter(explode(',', $fileExtensionsStr));
			array_walk($extensions, function(&$item, $key) { $item = strtolower(trim($item)); });
			$extensions = array_unique($extensions);
			$extensions = array_values($extensions);
			
			$fixedStr = implode(',', $extensions);
			
			if (count($extensions) > 0)
			{
				$filter = '\.(';
				$tooltip = '';
				
				foreach ($extensions as $key => $extension)
				{
					$extensionFilter = '';
					
					$letters = str_split($extension);
					
					foreach ($letters as $letter)
					{
						$extensionFilter .= '(' . $letter . '|' . strtoupper($letter) . ')';
						
					}
					
					$filter .= ($key == 0 ? '' : '|') . $extensionFilter;
					$tooltip .= ($key == 0 ? '' : ', ') . strtoupper($extension);
				}
				
				$filter .= ')$';
			}
		}
		
		return (object) array(
			'filter' => $filter,
			'tooltip' => $tooltip,
			'fixedStr' => $fixedStr,
			);
	}
}