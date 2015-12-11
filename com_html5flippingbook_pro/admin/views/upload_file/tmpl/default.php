<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

JHtml::_('behavior.tooltip');
?>

<script type="text/javascript">
	
	var componentUrl = '<?php echo 'index.php?option='.COMPONENT_OPTION; ?>';
	
	function lockControls()
	{
		changeControlsAvailability(false);
	}
	
	function unlockControls()
	{
		changeControlsAvailability(true);
	}
	
	function changeControlsAvailability(value)
	{
		var fileInput = document.getElementById('userfile');
		var btnUpload = document.getElementById('btnUpload');
		
		if (value)
		{
			fileInput.removeAttribute("disabled");
			btnUpload.removeAttribute("disabled");
		}
		else
		{
			fileInput.setAttribute("disabled", "");
			btnUpload.setAttribute("disabled", "");
		}
	}
	
	function showAjaxIndicator()
	{
		changeAjaxIndicatorVisibility(true);
	}
	
	function hideAjaxIndicator()
	{
		changeAjaxIndicatorVisibility(false);
	}
	
	function changeAjaxIndicatorVisibility(value)
	{
		var indicator = document.getElementById('indicator');
		
		indicator.style.display = (value ? 'block' : 'none');
	}
	
	function onBtnUploadClick(sender, event)
	{
		event.preventDefault();
		
		var fileInput = document.getElementById('userfile');
		
		BootstrapFormValidator.restoreControlsDefaultState([fileInput]);
		var error = false;
		
		error = BootstrapFormValidator.checkTrimmedEmptyValues([fileInput], '<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_FILE_UPLOAD_SELECT_FILE_WARNING'); ?>');
		if (error) return;
		
		// // NOTE: Removed since version 3.0.0 (build 002).
		// error = BootstrapFormValidator.checkSpaces([fileInput], '<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_FILE_UPLOAD_NO_SPACES'); ?>');
		// if (error) return;
		
		error = BootstrapFormValidator.checkPatterns([fileInput], new RegExp('^[\\w_ \\-\\.\\(\\)\\[\\]]+$', ''),
			'<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_FILE_UPLOAD_NOT_ALLOWED_CHARACTERS'); ?>', true);
		if (error) return;
		
		var extensionsStr = '<?php echo $this->extensionsStr; ?>';
		
		if (extensionsStr != '')
		{
			var fileExtension = fileInput.value.substring(fileInput.value.lastIndexOf('.') + 1).toLowerCase();
			var extensions = extensionsStr.split(',');
			
			if (extensions.indexOf(fileExtension) == -1)
			{
				var extensionsTip = '';
				
				for (var i = 0; i < extensions.length; i++)
				{
					extensionsTip += (i == 0 ? '' : ', ') + extensions[i].toUpperCase();
				}
				
				BootstrapFormValidator.setControlsErrorState([fileInput], '<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_FILE_UPLOAD_TYPE_WARNING'); ?>' + ': ' + extensionsTip);
				return;
			}
		}
		
		lockControls();
		showAjaxIndicator();
		
		var url = componentUrl + '&task=publications.check_file_existence' +
			'&dir=' + encodeURIComponent('<?php echo $this->dir; ?>') +
			'&fileName=' + encodeURIComponent(fileInput.value);
		var xmlData = '';
		var syncObject = { fileInput : fileInput, fileName : fileInput.value };
		var timeout = 5000;
		var dataCallback = function(request, syncObject, responseText) { onCheckFileExistenceData(request, syncObject, responseText); };
		var timeoutCallback = function(request, syncObject) { onCheckFileExistenceTimeout(request, syncObject); };
		
		MyAjax.makeRequest(url, xmlData, syncObject, timeout, dataCallback, timeoutCallback);
	}
	
	function onCheckFileExistenceData(request, syncObject, responseText)
	{
		hideAjaxIndicator();
		
		var xmlDoc = MethodsForXml.getXmlDocFromString(responseText);
		var rootNode = xmlDoc.documentElement;
		
		var error = MethodsForXml.getNodeValue(rootNode.childNodes[0]);
		
		if (error != '')
		{
			unlockControls();
			hideAjaxIndicator();
			alert('<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_ERROR'); ?>' + ': ' + error);
			return;
		}
		
		var fileExists = (MethodsForXml.getNodeValue(rootNode.childNodes[1]) == '1' ? true : false);
		
		if (fileExists)
		{
			var confirmed = confirm('<?php echo JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_FILE_UPLOAD_FILE_ALREADY_EXISTS', ''); ?>' + syncObject.fileName + '. ' +
				'<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_FILE_UPLOAD_FILE_CONFIRM_REPLACEMENT'); ?>');
			
			if (confirmed)
			{
				syncObject.fileInput.removeAttribute('disabled');
				document.adminForm.submit();
			}
			else
			{
				unlockControls();
			}
		}
		else
		{
			syncObject.fileInput.removeAttribute('disabled');
			document.adminForm.submit();
		}
	}
	
	function onCheckFileExistenceTimeout(request, syncObject)
	{
		hideAjaxIndicator();
		unlockControls();
		
		alert('<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_NETWORK_OPERATION_EXPIRED'); ?>');
	}
	
</script>

<form name="adminForm" method="post" action="index.php" enctype="multipart/form-data">
	<input type="hidden" name="option" value="<?php echo COMPONENT_OPTION; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="<?php echo $this->getName(); ?>" />
	<input type="hidden" name="layout" value="<?php echo $this->getLayout(); ?>" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="dir" value="<?php echo $this->dir; ?>" />
	<input type="hidden" name="pubid" value="<?php echo $this->pubid; ?>" />
	<input type="hidden" name="extensions" value="<?php echo $this->extensionsStr; ?>" />
	<input type="hidden" name="elementId" value="<?php echo $this->elementId; ?>" />
	<input type="hidden" name="linkedElementIds" value="<?php echo $this->linkedElementIdsStr; ?>" />
	
	<div class="html5fb_upload_lightbox_title">
		<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_FILE_UPLOAD_TITLE'); ?>
	</div>
	
	<div class="html5fb_upload_lightbox_row">
		<div class="html5fb_file_upload_controls">
			<div class="_input_div">
				<input type="file" name="userfile" id="userfile" />
				<button id="btnUpload" class="btn btn-primary" onclick="onBtnUploadClick(this, event);">
					<i class="icon-upload"></i>
					<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_FILE_UPLOAD_BTN_TEXT'); ?>
				</button>
				<div id="indicator" class="_indicator" style="display:none;"></div>
			</div>
			<div class="_tip">
				<div class="_info hasTip" title="<small><?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_FILE_UPLOAD_MAX_FILE_SIZE_EXPLANATION'); ?></small>"></div>
				<div class="_text">
					<?php echo JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_FILE_UPLOAD_MAX_FILE_SIZE', $this->maxSize); ?>
				</div>
			</div>
		</div>
	</div>
	
</form>