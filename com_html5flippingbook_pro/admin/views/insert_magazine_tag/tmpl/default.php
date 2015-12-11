<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');
?>

<style type="text/css">
	
	body {
		height: auto;
		margin: 0;
		padding: 0;
	}
	
</style>

<script type="text/javascript">
	
	var form = null;
	
	jQuery(document).ready(function ()
	{
		form = getFormControls();
		
		refreshInsertionTypeControls();
	});
	
	function getFormControls()
	{
		return {
			publicationSelect : document.getElementById('publication_id'),
			linkTextInput : document.getElementById('link_text')
			};
	}
	
	function onInsertionTypeClick(sender, event)
	{
		refreshInsertionTypeControls();
	}
	
	function refreshInsertionTypeControls()
	{
		var insertionType = BootstrapFormHelper.getRadioGroupValue('insertion_type');
		
		var insertionTypeControls = document.getElementById('insertion_type_controls');
		
		switch (insertionType)
		{
			case 'direct': insertionTypeControls.style.display = 'none'; break;
			case 'link': insertionTypeControls.style.display = 'block'; break;
		}
	}
	
	function onBtnInsertClick(sender, event)
	{
		event.preventDefault();
		
		BootstrapFormValidator.restoreControlsDefaultState([form.publicationSelect, form.linkTextInput]);
		
		var error = false;
		
		error = BootstrapFormValidator.checkSelectControlsEmptyValues([form.publicationSelect],
			'<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_UNDEFINED_VALUE'); ?>');
		if (error) return;
		
		var insertionType = BootstrapFormHelper.getRadioGroupValue('insertion_type');
		
		switch (insertionType)
		{
			case 'direct':
			{
				form.linkTextInput.value = '';
				break;
			}
			case 'link':
			{
				error = BootstrapFormValidator.checkTrimmedEmptyValues([form.linkTextInput], '<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_UNDEFINED_VALUE'); ?>');
				if (error) return;
				
				break;
			}
		}
		
		window.parent.html5fbInsertPublicationTag(form.publicationSelect.value, form.linkTextInput.value);
	}
	
	function onBtnCancelClick(sender, event)
	{
		window.parent.SqueezeBox.close();
	}
	
</script>

<form name="adminForm" action="index.php" method="post" autocomplete="off">
	<input type="hidden" name="option" value="<?php echo COMPONENT_OPTION; ?>" />
	<input type="hidden" name="view" value="<?php echo $this->getName(); ?>" />
	<input type="hidden" name="view" value="<?php echo $this->getLayout(); ?>" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
	
	<div id="j-main-container" class="span7 form-horizontal html5fb_insert_publication_tag">
		
		<div class="_title">
			<?php echo $this->escape(JText::_('COM_HTML5FLIPPINGBOOK_BE_INSERT_PUBLICATION_TAG_TITLE')); ?>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo $this->form->getLabel('publication_id'); ?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('publication_id'); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo $this->form->getLabel('insertion_type'); ?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('insertion_type'); ?>
			</div>
		</div>
		<div id="insertion_type_controls" style="display:none;">
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('link_text'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('link_text'); ?>
				</div>
			</div>
		</div>
		<div class="control-group _buttons_group">
			<button class="btn btn-primary" onclick="onBtnInsertClick(this, event);">
				<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_INSERT_PUBLICATION_TAG_BTN_INSERT'); ?>
			</button>
			<button class="btn" onclick="onBtnCancelClick(this, event);">
				<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_INSERT_PUBLICATION_TAG_BTN_CANCEL'); ?>
			</button>
		</div>
		
	</div>
	
</form>