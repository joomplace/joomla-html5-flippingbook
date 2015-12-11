<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.modal');

// Checking images subdirectory.

if ($this->item->c_imgsub == 1 && $this->item->c_imgsubfolder != '')
{
	$fullImagesSubdirName = JPATH_SITE.'/media/'.COMPONENT_OPTION.'/images/'.$this->item->c_imgsubfolder;
	
	if (!JFolder::exists($fullImagesSubdirName))
	{
		JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_IMAGES_SUBDIR_DOESNT_EXIST', $fullImagesSubdirName), 'error');
	}
}
?>

<script type="text/javascript">
	
	var form = null;

	<?php echo HtmlHelper::tinyMCE_js(
		'640',
		'300',
		 '',
		 'jform_c_description,jform_c_author_description'
		);
	?>

	jQuery(document).ready(function ()
	{
	    jQuery('#viewTabs a:first').tab('show');
		
		form = getFormControls();
		
		refreshSubfolderControls(false);

		jQuery('label[for="jform_convert0"]').on('click', function() {
			if (jQuery(this).hasClass('active') && jQuery('label[for="jform_cloudconvert0"]').hasClass('active')) {
				jQuery('label[for="jform_cloudconvert0"]').removeClass('active btn-success');
				jQuery('input[id="jform_cloudconvert0"]').attr('checked', false);

				jQuery('label[for="jform_cloudconvert1"]').addClass('active btn-danger');
				jQuery('input[id="jform_cloudconvert1"]').attr('checked', true);
			}
		});

		jQuery('label[for="jform_cloudconvert0"]').on('click', function() {
			if (jQuery(this).hasClass('active') && jQuery('label[for="jform_convert0"]').hasClass('active')) {
				jQuery('label[for="jform_convert0"]').removeClass('active btn-success');
				jQuery('input[id="jform_convert0"]').attr('checked', false);

				jQuery('label[for="jform_convert1"]').addClass('active btn-danger');
				jQuery('input[id="jform_convert1"]').attr('checked', true);
			}
		});
	});
	
	function getFormControls()
	{
		return {
			categorySelect : document.getElementById('jform_c_category_id'),
			resolutionSelect : document.getElementById('jform_c_resolution_id'),
			templateSelect : document.getElementById('jform_c_template_id'),
			imagesSubdirInput : document.getElementById('jform_c_imgsubfolder'),
			pdfSelect : document.getElementById('jform_c_background_pdf'),
			convertFormats : document.getElementById('jform_convert_formats'),
			cloudConvertApi : document.getElementById('jform_cloudconvert_api'),
			cloudConvertFormats : document.getElementById('jform_cloudconvert_formats')
			};
	}

	function onUseSubfolderClick(sender, event)
	{
		refreshSubfolderControls(true);
	}
	
	function refreshSubfolderControls(isRefreshDuringEditing)
	{
		var useImagesSubdir = (BootstrapFormHelper.getRadioGroupValue('jform_c_imgsub') == 1);
		
		if (useImagesSubdir)
		{
			form.imagesSubdirInput.disabled = false;
			
			if (isRefreshDuringEditing)
			{
				form.imagesSubdirInput.focus();
			}
		}
		else
		{
			form.imagesSubdirInput.disabled = true;
		}
	}
	
	Joomla.submitbutton = function(task)
	{
		if (task == 'publication.cancel')
		{
			Joomla.submitform(task, document.adminForm);
			return;
		}
		
		Joomla.removeMessages();
		BootstrapFormValidator.restoreControlsDefaultState([form.categorySelect, form.resolutionSelect, form.templateSelect, form.pdfSelect]);
		
		if (!document.formvalidator.isValid(document.adminForm))
		{
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			return;
		}
		
		var error = false;
		
		error = BootstrapFormValidator.checkSelectControlsEmptyValues([form.categorySelect, form.resolutionSelect, form.templateSelect],
			'<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_UNDEFINED_VALUE'); ?>');
		if (error) return;
		
		var useImagesSubdir = (BootstrapFormHelper.getRadioGroupValue('jform_c_imgsub') == 1);
		
		if (useImagesSubdir)
		{
			error = BootstrapFormValidator.checkTrimmedEmptyValues([form.imagesSubdirInput], '<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_UNDEFINED_VALUE'); ?>');
			if (error) return;
			
			error = BootstrapFormValidator.checkSpaces([form.imagesSubdirInput], '<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_NO_SPACES'); ?>');
			if (error) return;
			
			error = BootstrapFormValidator.checkPatterns([form.imagesSubdirInput], new RegExp('^[\\w\\-]+$', ''),
				'<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_DIRECTORY_NAME_PATTERN_NOTICE'); ?>');
			if (error) return;
		}
		
		error = BootstrapFormValidator.checkNumbersValidityAndLimits([form.staticShadowsDepthInput, form.dynamicShadowsDepthInput], 0, 10,
			'<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_NOT_NUMERICAL_VALUE'); ?>', '<?php echo JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_NUMERICAL_VALUE_LIMITS', 0, 10); ?>',
			'int');
		if (error) return;
		
		var enablePdf = (BootstrapFormHelper.getRadioGroupValue('jform_c_enable_pdf') == 1);
		
		if (enablePdf)
		{
			error = BootstrapFormValidator.checkSelectControlsEmptyValues([form.pdfSelect], '<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_UNDEFINED_VALUE'); ?>');
			if (error) return;
		}

		var enableConvert = (BootstrapFormHelper.getRadioGroupValue('jform_convert') == 1);

		if (enableConvert)
		{
			error = BootstrapFormValidator.checkSelectControlsEmptyValues([form.convertFormats], '<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_UNDEFINED_VALUE_CONVERT_FORMATS'); ?>');
			if (error) return;
		}

		var enableCloudConvert = (BootstrapFormHelper.getRadioGroupValue('jform_cloudconvert') == 1);

		if (enableCloudConvert)
		{
			error = BootstrapFormValidator.checkEmptyValues([form.cloudConvertApi], '<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_UNDEFINED_VALUE_CLOUDCONVERT_APIKEY'); ?>');
			if (error) return;

			error = BootstrapFormValidator.checkSelectControlsEmptyValues([form.cloudConvertFormats], '<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_UNDEFINED_VALUE_CONVERT_FORMATS'); ?>');
			if (error) return;
		}
		
		form.imagesSubdirInput.disabled = false;
		
		Joomla.submitform(task, document.adminForm);
	}

	function cmtRemove(element)
	{
		var oldNodesCount = jQuery('.custom_metatags > table > tbody').children().length;
		element.parentNode.parentNode.parentNode.removeChild(element.parentNode.parentNode);
		if ( oldNodesCount == 1 )
			jQuery('.custom_metatags > table > tbody').append(
				'<tr id="ct_notags"><td colspan="3"><?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_METADATA_CUSTOM_TAGS_NOTAGS'); ?></td>'
			);
	}

	function cmtAdd()
	{
		document.getElementById('jcustom_name').value = document.getElementById('jcustom_name').value.replace(/\"/g, '&quote;');
		document.getElementById('jcustom_value').value = document.getElementById('jcustom_value').value.replace(/\"/g, '&quote;');

		if ( document.getElementById('jcustom_name').value != '' && document.getElementById('jcustom_value').value != '')
		{
			if ( document.getElementById('ct_notags') )
				document.getElementById('ct_notags').parentNode.removeChild( document.getElementById('ct_notags') );

			jQuery('.custom_metatags > table > tbody').append(
				'<tr><td>'+document.getElementById('jcustom_name').value+'</td>'
					+'<td>'+document.getElementById('jcustom_value').value+'</td>'
					+'<td><span class="btn-small btn btn-danger" onclick="cmtRemove(this);"> <span class="icon-delete"> </span> </span>'
					+'<input type="hidden" name="cm_names[]" value="'+document.getElementById('jcustom_name').value+'" />'
					+'<input type="hidden" name="cm_values[]" value="'+document.getElementById('jcustom_value').value+'" />'
					+'</td>'
				+'</tr>'
			);

			document.getElementById('jcustom_name').value = '';
			document.getElementById('jcustom_value').value = '';
		}
	}
	
</script>
<style type="text/css">
	#tab_convert .controls {
		margin-left: 300px;
	}
</style>
<?php echo HtmlHelper::getMenuPanel(); ?>

<form name="adminForm" id="adminForm" action="index.php" method="post" autocomplete="off" class="form-validate">
	<input type="hidden" name="option" value="<?php echo COMPONENT_OPTION; ?>" />
	<input type="hidden" name="view" value="<?php echo $this->getName(); ?>" />
	<input type="hidden" name="layout" value="<?php echo $this->getLayout(); ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="c_id" value="<?php echo $this->item->c_id; ?>" />
	<?php echo $this->form->getInput('asset_id'); ?>
	<?php echo JHtml::_('form.token'); ?>

	<?php if (!empty($this->sidebar)) { ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
	<?php } ?>


	<div id="j-main-container" class="span9 form-horizontal">
		
		<ul class="nav nav-tabs" id="viewTabs">
			<li><a href="#tab_general" data-toggle="tab"><?php echo  JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_GENERAL'); ?></a></li>
			<li><a href="#tab_pdf_version" data-toggle="tab"><?php echo  JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_PDF_TAB'); ?></a></li>
			<li><a href="#tab_convert" data-toggle="tab"><?php echo  JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_CONVERT_TAB'); ?></a></li>
			<li><a href="#tab_metadata" data-toggle="tab"><?php echo  JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_METADATA_TAB'); ?></a></li>
			<li><a href="#tab_permissions" data-toggle="tab"><?php echo  JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_PERMISSIONS_TAB'); ?></a></li>
		</ul>
		
		<div class="tab-content">
			
			<div class="tab-pane" id="tab_general">
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('c_id'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('c_id'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('c_title'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('c_title'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('c_author'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('c_author'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('published'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('published'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('c_category_id'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('c_category_id'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('c_imgsub'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('c_imgsub'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('c_imgsubfolder'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('c_imgsubfolder'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('right_to_left'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('right_to_left'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('navi_settings'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('navi_settings'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('c_resolution_id'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('c_resolution_id'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('c_template_id'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('c_template_id'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('c_popup'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('c_popup'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('c_created_time'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('c_created_time'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('c_thumb'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('c_thumb'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('c_pub_descr'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('c_pub_descr'); ?>
					</div>
				</div>
			</div>

			<div class="tab-pane" id="tab_pdf_version">
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('c_enable_pdf'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('c_enable_pdf'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('c_background_pdf'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('c_background_pdf'); ?>
					</div>
				</div>
			</div>

			<div class="tab-pane" id="tab_convert">
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('convert'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('convert'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('convert_formats'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('convert_formats'); ?>
					</div>
				</div>
				<hr/>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('cloudconvert'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('cloudconvert'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('cloudconvert_api'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('cloudconvert_api'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('cloudconvert_formats'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('cloudconvert_formats'); ?>
					</div>
				</div>
			</div>
			
			<div class="tab-pane" id="tab_metadata">
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('c_metadesc'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('c_metadesc'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('c_metakey'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('c_metakey'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('opengraph_use'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('opengraph_use'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('opengraph_title'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('opengraph_title'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('opengraph_author'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('opengraph_author'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('opengraph_image'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('opengraph_image'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('opengraph_description'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('opengraph_description'); ?>
					</div>
				</div>

				<!-- custom_metatags -->
					<h3 class="page-header"><?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_METADATA_CUSTOM_TAGS_TITLE'); ?></h3>
					<div class="custom_metatags">
						<table border="0" width="100%" class="table table-striped">
							<thead>
							<tr>
								<th width="200"><?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_METADATA_CUSTOM_TAGS_NAME'); ?></th>
								<th><?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_METADATA_CUSTOM_TAGS_CONTENT'); ?></th>
								<th width="20"></th>
							</tr>
							</thead>
							<tbody>
								<?php if ( empty($this->item->custom_metatags) )
									echo '<tr id="ct_notags"><td colspan="3">'.JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_METADATA_CUSTOM_TAGS_NOTAGS').'</td>';
								else
								{
									foreach ( $this->item->custom_metatags as $ctag_name => $ctag_value )
										echo '<tr>'
											.'<td>'.$ctag_name.'</td>'
											.'<td>'.$ctag_value.'</td>'
											.'<td><span class="btn-small btn btn-danger" onclick="cmtRemove(this);"> <span class="icon-delete"> </span> </span>'
											.'<input type="hidden" name="cm_names[]" value="'.$ctag_name.'" />'
											.'<input type="hidden" name="cm_values[]" value="'.$ctag_value.'" />'
											.'</td>'
											.'</tr>';
								} ?>
							</tbody>
						</table>
						<div class="well">
							<table border="0" width="100%">
							<tr><td width="210">
									<input type="text" size="20" class="inputbox" value="" id="jcustom_name" placeholder="<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_METADATA_CUSTOM_TAGS_NAME'); ?>">
								</td>
								<td style="padding-right: 25px;">
									<input type="text" size="60" class="inputbox" value="" id="jcustom_value" placeholder="<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_METADATA_CUSTOM_TAGS_CONTENT'); ?>" style="width: 100%">
								</td>
								<td width="20">
									<span class="btn btn-success" onclick="cmtAdd();"> <span class="icon-plus"> </span> </span>
								</td>
							</tr>
							</table>
						</div>
						<br />
						<br />
					</div>

			</div>
			
			<div class="tab-pane" id="tab_permissions">
				<div class="control-group">
					<?php echo $this->form->getInput('rules'); ?>
				</div>
			</div>
			
		</div>
		
	</div>
	
</form>