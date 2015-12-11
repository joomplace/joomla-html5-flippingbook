<?php defined('_JEXEC') or die('Restricted Access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>

<script type="text/javascript">
	
	var form = null;
	
	jQuery(document).ready(function ()
	{
	    jQuery('#viewTabs a:first').tab('show');
		
		form = getFormControls();
	});
	
	function getFormControls()
	{
		return {
			widthInput : document.getElementById('jform_width'),
			heightInput : document.getElementById('jform_height')
			};
	}
	
	Joomla.submitbutton = function(task)
	{
		if (task == 'resolution.cancel')
		{
			Joomla.submitform(task, document.adminForm);
			return;
		}
		
		Joomla.removeMessages();
		
		if (!document.formvalidator.isValid(document.adminForm))
		{
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			return;
		}
		
		var error = false;
		
		error = BootstrapFormValidator.checkSpaces([form.widthInput, form.heightInput], '<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_NO_SPACES'); ?>');
		if (error) return;
		
		error = BootstrapFormValidator.checkNumbersValidityAndLimits([form.widthInput, form.heightInput], 320, 3000,
			'<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_NOT_NUMERICAL_VALUE'); ?>', '<?php echo JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_NUMERICAL_VALUE_LIMITS', 320, 3000); ?>',
			'int');
		if (error) return;

		Joomla.submitform(task, document.adminForm);
	}
	
</script>

<?php echo HtmlHelper::getMenuPanel(); ?>

<form name="adminForm" id="adminForm" action="index.php" method="post" autocomplete="off" class="form-validate">
	<input type="hidden" name="option" value="<?php echo COMPONENT_OPTION; ?>" />
	<input type="hidden" name="view" value="<?php echo $this->getName(); ?>" />
	<input type="hidden" name="layout" value="<?php echo $this->getLayout(); ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
	<?php echo JHtml::_('form.token'); ?>

	<?php if (!empty($this->sidebar)) { ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
	<?php } ?>

	<div id="j-main-container" class="span9 form-horizontal">
		
		<ul class="nav nav-tabs" id="viewTabs">
			<li><a href="#tab_details" data-toggle="tab"><?php echo  JText::_('COM_HTML5FLIPPINGBOOK_BE_DETAILS');?></a></li>
		</ul>
		
		<div class="tab-content">
			
			<div class="tab-pane" id="tab_details">
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('id'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('id'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('resolution_name'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('resolution_name'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('width'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('width'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('height'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('height'); ?>
					</div>
				</div>
				<div class="html5fb_tip">
					<?php echo '* ' . $this->escape(JText::_('COM_HTML5FLIPPINGBOOK_BE_RESOLUTIONS_TIP')); ?>
				</div>
			</div>
			
		</div>
		
	</div>
	
</form>