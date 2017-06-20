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

$maxSize = min((int) ini_get('post_max_size'), (int) ini_get('upload_max_filesize'));
?>
<script type="text/javascript">
	var form = null;

	jQuery(document).ready(function ()
	{
		form = getFormControls();
	});

	function getFormControls()
	{
		return {
			publicationSelect : document.getElementById('publication_id'),
			generalPagesTitleInput : document.getElementById('general_pages_title'),
			pdfFile : document.getElementById('pdf_file')
		};
	}

	Joomla.submitbutton = function(task)
	{
		if (task == 'page.cancel')
		{
			Joomla.submitform(task, document.adminForm);
			return;
		}

		if (task == 'pages.takefile')
		{
			Joomla.removeMessages();
			BootstrapFormValidator.restoreControlsDefaultState([form.publicationSelect, form.generalPagesTitleInput, form.pdfFile]);

			var error = false;

			error = BootstrapFormValidator.checkSelectControlsEmptyValues([form.publicationSelect],
				'<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_UNDEFINED_VALUE'); ?>');
			if (error) return;

			error = BootstrapFormValidator.checkTrimmedEmptyValues([form.generalPagesTitleInput],
				'<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_UNDEFINED_VALUE'); ?>');
			if (error) return;

			error = BootstrapFormValidator.checkTrimmedEmptyValues([form.pdfFile],
				'<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_UNDEFINED_VALUE'); ?>');
			if (error) return;

			Joomla.submitform(task, document.adminForm);
		}
	}
</script>
<?php echo HtmlHelper::getMenuPanel(); ?>

<form name="adminForm" id="adminForm" action="index.php" method="post" enctype="multipart/form-data" autocomplete="off">
	<input type="hidden" name="option" value="<?php echo COMPONENT_OPTION; ?>" />
	<input type="hidden" name="view" value="<?php echo $this->getName(); ?>" />
	<input type="hidden" name="layout" value="<?php echo $this->getLayout(); ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>

	<div id="j-main-container" class="span7 form-horizontal html5fb_pages_convert">
		<?php if (!$this->is_imagemg):?>
		<div class="disabled"></div>
		<?php endif;?>
		<div class="control-group">
			<?php
			$input = $this->form->getField('publication_id');
			$input->addOptions($this->PublicationOptions);
			?>
			<div class="control-label">
				<?php echo $input->getLabel(); ?>
			</div>
			<div class="controls">
				<?php echo $input->getInput(); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo $this->form->getLabel('general_pages_title'); ?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('general_pages_title'); ?>
			</div>
		</div>
		<div id="archive_controls">
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('pdf_file'); ?>
				</div>
				<div class="controls">
					<div class="html5fb_file_upload_controls">
						<div class="_input_div">
							<input type="file" name="pdf_file" id="pdf_file" />
						</div>
						<div class="_tip">
							<div class="_info hasTip" title="<small><?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_FILE_UPLOAD_MAX_FILE_SIZE_EXPLANATION'); ?></small>">
							</div>
							<div class="_text">
								<?php echo JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_FILE_UPLOAD_MAX_FILE_SIZE', $maxSize); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo $this->form->getLabel('image_quality'); ?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('image_quality'); ?>
			</div>
		</div>
	</div>
</form>