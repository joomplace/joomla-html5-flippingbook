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

JFormHelper::loadFieldClass('_upload_file');

//==================================================
// List of images, upload button and preview.
//==================================================
class JFormField_Upload_Image extends JFormField_Upload_File
{
	protected $type = '_upload_image';
	protected $noImageUrl;
	//----------------------------------------------------------------------------------------------------
	public function __construct($form = null)
	{
		parent::__construct($form);
	}
	//----------------------------------------------------------------------------------------------------
	public function getInput()
	{
		$this->dir = JPATH_SITE.'/'.$this->element['dir'];
		$this->pubid = 0;
		$this->dirUrl = JURI::root().$this->element['dir'].'/';
		$this->noImageUrl = JURI::root() . ($this->element['noImageUrl'] ? $this->element['noImageUrl'] : 'administrator/components/'.COMPONENT_OPTION.'/assets/thumbnails/no_image.png');
		$this->listTitle = ($this->element['listTitle'] ? $this->element['listTitle'] : 'COM_HTML5FLIPPINGBOOK_BE_SELECT_IMAGE');
		$this->successMessage = ($this->element['successMessage'] ? $this->element['successMessage'] : 'COM_HTML5FLIPPINGBOOK_BE_FILE_UPLOAD_SUCCESS');
		$this->tooltipTitle = ($this->element['tooltipTitle'] ? $this->element['tooltipTitle'] : 'COM_HTML5FLIPPINGBOOK_BE_FILE_UPLOAD_TOOLTIP_TITLE');
		$this->tooltipText = ($this->element['tooltipText'] ? $this->element['tooltipText'] : 'COM_HTML5FLIPPINGBOOK_BE_FILE_UPLOAD_TOOLTIP_TEXT');
		$this->linkedElementIds = ($this->element['linkedElementIds'] ? $this->element['linkedElementIds'] : '');
		$this->fileExtensions = ($this->element['fileExtensions'] ? $this->element['fileExtensions'] : 'png,jpg,jpeg,gif');
		
		$this->fileExtensionsObj = $this->getFileExtensionsObject($this->fileExtensions);
		
		$this->fileNames = $this->getFileNames();
		
		$selectedImageUrl = (isset($this->value) && JFile::exists($this->dir.'/'.$this->value) ? $this->dirUrl.$this->value : $this->noImageUrl);
		
		$html = array();
		$attr = '';
		$this->multiple = false;
		
		ob_start();
		?>
		<script type="text/javascript">
			
			var html5fbForceImageListPreviewRefresh_<?php echo $this->id; ?> = false;
			
			function html5fbRefreshImageListPreview_<?php echo $this->id; ?>(fileName)
			{
				var previewImg = document.getElementById('<?php echo $this->id; ?>_preview');
				
				if (html5fbForceImageListPreviewRefresh_<?php echo $this->id; ?>)
				{
					previewImg.src = (fileName != '' ? '<?php echo $this->dirUrl; ?>' + fileName + '?rand=' + Math.floor((Math.random() * 1000000) + 1) :
						'<?php echo $this->noImageUrl; ?>');
				}
				else
				{
					previewImg.src = (fileName != '' ? '<?php echo $this->dirUrl; ?>' + fileName : '<?php echo $this->noImageUrl; ?>');
				}
			}
			
			function html5fbOnFileUploadedToList_<?php echo $this->id; ?>(fileName, isTargetElement, fileIsBeingReplaced)
			{
				if (fileIsBeingReplaced) html5fbForceImageListPreviewRefresh_<?php echo $this->id; ?> = true;
				
				if (isTargetElement)
				{
					if (!fileIsBeingReplaced)
					{
						BootstrapFormHelper.addOptionToSelectList('<?php echo $this->id; ?>', 'thumb_' + fileName, 'thumb_' + fileName, true);
					}
					else
					{
						BootstrapFormHelper.selectOptionInSelectList('<?php echo $this->id; ?>', 'thumb_' + fileName);
					}
					
					var uploadResultElement = document.getElementById('<?php echo $this->id; ?>_result');
					
					html5fbAnimateFileUploadResult_<?php echo $this->id; ?>(uploadResultElement);
					
					SqueezeBox.close();
				}
				else
				{
					if (!fileIsBeingReplaced)
					{
						BootstrapFormHelper.addOptionToSelectList('<?php echo $this->id; ?>', 'thumb_' + fileName, 'thumb_' + fileName, false);
					}
					else
					{
						var selectedOption = BootstrapFormHelper.getSelectedListOption('<?php echo $this->id; ?>');
						
						if (selectedOption.value == 'thumb_' + fileName) BootstrapFormHelper.selectOptionInSelectList('<?php echo $this->id; ?>', 'thumb_' + fileName);
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
		$attr .= ' onChange="html5fbRefreshImageListPreview_' . $this->id . '(this.value);" ';
		
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
			$html[] = '<div class="'.$this->element['class'].'_preview">';
			$html[] = 	'<img id="' . $this->id . '_preview" class="'.$this->element['previewImgClass'].'" src="' . $selectedImageUrl . '" />';
			$html[] = '</div>';
		}
		
		return implode('', $html);
	}
}