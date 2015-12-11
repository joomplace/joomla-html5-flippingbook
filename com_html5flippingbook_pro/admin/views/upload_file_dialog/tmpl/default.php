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

    function html5fbOnFileUploadedToList(fileName, fileIsBeingReplaced)
    {
        if (!fileIsBeingReplaced)
        {
            BootstrapFormHelper.addOptionToSelectList('jform_avfile_filename', fileName, fileName, true);
        }
        else
        {
            BootstrapFormHelper.selectOptionInSelectList('jform_avfile_filename', fileName);
        }

        var uploadResultElement = document.getElementById('jform_avfile_filename_result');

        html5fbAnimateFileUploadResult(uploadResultElement);
    }

    function html5fbAnimateFileUploadResult(element)
    {
        element.style.opacity = 1;

        var intervalId = setInterval(function() { html5fbAnimateFileUploadStep(element, intervalId); }, 150);
    }

    function html5fbAnimateFileUploadStep(element, intervalId)
    {
        var opacity = parseFloat(element.style.opacity);

        if (isNaN(opacity) || opacity < 0) opacity = 0;

        opacity -= 0.05;

        if (opacity < 0) opacity = 0;

        element.style.opacity = opacity;

        if (opacity == 0) clearInterval(intervalId);
    }

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

        error = BootstrapFormValidator.checkPatterns([fileInput], new RegExp('^[\\w_ \\-\\.\\(\\)\\[\\]]+$', ''),
            '<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_FILE_UPLOAD_NOT_ALLOWED_CHARACTERS'); ?>', true);
        if (error) return;

        var extensionsStr = '<?php echo implode(',',$this->extensions); ?>';

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
        changeAjaxIndicatorVisibility(true);

        var url = componentUrl + '&task=publications.check_file_existence' +
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
        changeAjaxIndicatorVisibility(false);

        var xmlDoc = MethodsForXml.getXmlDocFromString(responseText);
        var rootNode = xmlDoc.documentElement;

        var error = MethodsForXml.getNodeValue(rootNode.childNodes[0]);

        if (error != '')
        {
            unlockControls();
            changeAjaxIndicatorVisibility(false);
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
        changeAjaxIndicatorVisibility(false);
        unlockControls();

        alert('<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_NETWORK_OPERATION_EXPIRED'); ?>');
    }

    function insertToEditor(filename)
    {
        <?php if ( $this->type == 'audio' ) { ?>
        window.parent.jInsertEditorText('<audio src="<?=$this->dirUrl;?>'+filename+'" controls><?=JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_AUDIO_NOT_SUPPORT');?></audio>', '<?=$this->e_name;?>');
        <?php } else { ?>
        window.parent.jInsertEditorText('<video src="<?=$this->dirUrl;?>'+filename+'" controls width="350"><?=JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_VIDEO_NOT_SUPPORT');?></video>', '<?=$this->e_name;?>');
        <?php } ?>
    }

</script>
<form name="adminForm" method="post" action="index.php" enctype="multipart/form-data" style="margin: 0">
    <input type="hidden" name="option" value="<?php echo COMPONENT_OPTION; ?>" />
    <input type="hidden" name="type" value="<?php echo $this->type; ?>" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="view" value="<?php echo $this->getName(); ?>" />
    <input type="hidden" name="layout" value="<?php echo $this->getLayout(); ?>" />
    <input type="hidden" name="e_name" value="<?php echo $this->e_name; ?>" />
    <input type="hidden" name="tmpl" value="component" />
    <div class="html5fb_upload_lightbox_title">
        <?php echo ($this->type == 'video' ? JText::_('COM_HTML5FLIPPINGBOOK_BE_INSERT_BUTTON_VIDEO') : JText::_('COM_HTML5FLIPPINGBOOK_BE_INSERT_BUTTON_AUDIO') )
            . ' ' . JText::_('COM_HTML5FLIPPINGBOOK_BE_FILE_UPLOAD_TITLE'); ?>
    </div>

    <div class="html5fb_upload_lightbox_row ">
        <div class="html5fb_file_upload_controls form-horizontal">
            <div class="control-group">
                <div class="control-label" style="float: left; padding-top: 5px;">
                    <label id="jform_avfile_filename-lbl" for="jform_avfile_filename" class="hasTip" title="<?=JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_VIDEOAUDIO_DESC');?>"><?=($this->type == 'video' ? JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_VIDEO_FILE') : JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_AUDIO_FILE') );?></label>
                </div>
                <div class="controls" style="margin-left: 104px;">
                    <select id="jform_avfile_filename" name="">
                        <option value="0"><? echo JText::_('COM_HTML5FLIPPINGBOOK_BE_SELECT_FILE'); ?></option>
                        <?php
                        if ( $this->filesList )
                            foreach ( $this->filesList as $filename )
                                echo '<option value="'.$filename.'">'.$filename.'</option>';
                        ?>
                    </select>
                    <input type="hidden" name="jform[publication_id]" value="1"/>
                </div>
            </div>
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
            <div class="_tip">
                <div class="_info hasTip" title="<small><?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_VA_SUPPORT_FORMATS').': '.implode(', ', $this->extensions); ?></small>"></div>
                <div class="_text">
                    <?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_VA_SUPPORT_FORMATS').': '.implode(', ', $this->extensions); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="html5fb_upload_dialog_lightbox_buttons">
        <div id="jform_avfile_filename_result" class="html5fb_upload_image_result" style="opacity:0;">
            <?=JText::_('COM_HTML5FLIPPINGBOOK_BE_FILE_UPLOAD_SUCCESS');?>
        </div>

        <button onclick="window.parent.SqueezeBox.close();" type="button" class="btn">Cancel</button>
        <button onclick="insertToEditor(jQuery('#jform_avfile_filename').val());window.parent.SqueezeBox.close();" type="button" class="btn btn-primary">Insert</button>
    </div>

</form>