<?php defined('_JEXEC') or die('Restricted Access');
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

	Joomla.submitbutton = function(task, id)
	{
		if ( task == 'import.flashmagazine' )
		{
			if ( !jQuery('#flashmag_category_autocreate').attr('checked') && document.adminForm.flashmag_category_id.value == '0' )
			{
				jQuery('#flashmag_category_id').addClass('invalid');
				jQuery('#flashmag_category_id_lbl').addClass('invalid');
				alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
				return false;
			}
			else
			{
				if ( !id )
				{
					Joomla.submitform(task, document.adminForm);
					return;
				}
				else
				{
					document.adminForm.item_id.value = id;
					Joomla.submitform(task, document.adminForm);
					return;
				}
			}
		}
	}

	function flashmag_autocreateChecker( who )
	{
		if ( jQuery('#flashmag_category_autocreate').attr('checked') )
			jQuery('#flashmag_category_id').prop('disabled', true);
		else
			jQuery('#flashmag_category_id').prop('disabled', false);
	}


</script>

<?php echo HtmlHelper::getMenuPanel(); ?>

<form name="adminForm" id="adminForm" action="index.php" method="post" autocomplete="off">
	<input type="hidden" name="option" value="<?php echo COMPONENT_OPTION; ?>" />
	<input type="hidden" name="view" value="<?php echo $this->getName(); ?>" />
	<input type="hidden" name="item_id" value="" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>

	<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'flashmagazine')); ?>

	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'flashmagazine', JText::_('COM_FLASHPUBLICATIONDELUXE', true)); ?>

	<div id="j-main-container" class="span8 form-horizontal">

		<?php if ( $this->flashmagazine ) { ?>
		<table class="table table-striped">
			<thead>
				<tr>
					<th><?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_PUBLICATION'); ?></th>
					<th><?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_CATEGORY'); ?></th>
					<th width="100" class="center"><?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_PAGES'); ?></th>
					<th width="50" class="center"><?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_PAGES'); ?></th>
					<th width="50"></th>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach ( $this->flashmagazine as $publication ) {

						echo '<tr>'
								.'<td>'.$publication->c_title.'</td>'
								.'<td>'.$publication->category->c_category.'</td>'
								.'<td class="center">'.$publication->pages_count.'</td>'
								.'<td class="center">'.( $publication->exists ? '<span class="label label-success">'.JText::_('COM_HTML5FLIPPINGBOOK_BE_IMPORT_ALREADY_IMPORTED').'</span>' : '<span class="label label-important">'.JText::_('COM_HTML5FLIPPINGBOOK_BE_IMPORT_NOT_IMPORTED').'</span>').'</td>'
								.'<td><input type="button" class="btn-small btn btn-info" value="'.JText::_('COM_HTML5FLIPPINGBOOK_BE_IMPORT').'" onclick="Joomla.submitbutton(\'import.flashmagazine\', '.$publication->c_id.');"/></td>'
							.'</tr>';
					}
				?>
			</tbody>
		</table>
			<div class="pull-left">
				<div class="control-group">
					<div class="control-label" id="flashmag_category_id_lbl">
						<b><?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_IMPORT_TO_CATEGORY');?>:</b>
					</div>
					<div class="controls">
						<?php echo JHtml::_('select.genericlist', $this->categories, 'flashmag_category_id', '', 'value', 'text', '', 'flashmag_category_id'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<label for="flashmag_category_autocreate"><b><?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_IMPORT_AUTO_CREATE_CATEGORY');?>:</b></label>
					</div>
					<div class="controls">
						<input type="checkbox" name="flashmag_category_autocreate" value="1" id="flashmag_category_autocreate" onchange="flashmag_autocreateChecker();"/>
					</div>
				</div>

			</div>
			<div class="pull-right">
				<input type="button" value="<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_IMPORT_ALL');?>" class="btn btn-large btn-primary" onclick="Joomla.submitbutton('import.flashmagazine');"/>
			</div>

		<?php } else { echo '<h3 class="well">'.JText::_('COM_FLASHPUBLICATIONDELUXE_NOT_EXISTS').'<h3>'; } ?>

	</div>

	<?php echo JHtml::_('bootstrap.endTab'); ?>

	<?php echo JHtml::_('bootstrap.endTabSet'); ?>

</form>