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
?>

<script type="text/javascript">
	
	jQuery(document).ready(function () {
	    jQuery('#viewTabs a:first').tab('show');
	});
	
	Joomla.submitbutton = function(task)
	{
		if (task == 'category.cancel')
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

<?php echo HtmlHelper::getMenuPanel(); ?>

<form name="adminForm" id="adminForm" action="index.php" method="post" autocomplete="off" class="form-validate">
	<input type="hidden" name="option" value="<?php echo COMPONENT_OPTION; ?>" />
	<input type="hidden" name="view" value="<?php echo $this->getName(); ?>" />
	<input type="hidden" name="layout" value="<?php echo $this->getLayout(); ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="c_id" value="<?php echo $this->item->c_id; ?>" />
	<?php echo $this->form->getInput('asset_id'); ?>

	<?php if (!empty($this->sidebar)) { ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
	<?php } ?>

	<?php echo JHtml::_('form.token'); ?>
	
	<div id="j-main-container" class="span9 form-horizontal">

		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('COM_HTML5FLIPPINGBOOK_BE_CATEGORIES_DETAILS', true)); ?>
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
					<?php echo $this->form->getLabel('c_category'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('c_category'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('c_instruction'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('c_instruction'); ?>
				</div>
			</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'metadata', JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_METADATA_TAB', true)); ?>
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
		<?php echo JHtml::_('bootstrap.endTab'); ?>


			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'rules', JText::_('COM_HTML5FLIPPINGBOOK_BE_CATEGORIES_PERMISSIONS', true)); ?>
			<fieldset>
				<?php echo $this->form->getInput('rules'); ?>
			</fieldset>
			<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>

	</div>
	
</form>
<script>
    //Quick fix: the 'id' parameter is required in the URL for Joomla scripts to change permissions
    window.onload = function() {
        if (history.pushState) {
            var oldUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + window.location.search;
            var newUrl = oldUrl + '&id=' + getUrlParam('c_id');
            history.pushState(null, null, newUrl);
        }
    };
</script>