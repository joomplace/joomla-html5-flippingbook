<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
?>

<div class="modal hide fade html5fb_batch_modal html5fb_batch_modal_publications" id="collapseModal">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">x</button>
		<h3><?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_BATCH');?></h3>
	</div>
	<div class="modal-body">
		<p><?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_BATCH_TIP'); ?></p>
		<div class="control-group">
			<div class="controls">
				<?php
				echo '<label id="targetCategoryId-lbl" class="hasTip" title="" for="targetCategoryId">' .
						$this->escape(JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_BATCH_SELECT_CATEGORY')) .
					'</label>';
				
				$options = array_merge(array(JHtml::_('select.option', -1, JText::_('JSELECT'), 'value', 'text')), $this->categoryOptions);
				
				echo JHtml::_('select.genericlist', $options, 'targetCategoryId', null, 'value', 'text', -1);
				
				$options = array(
					JHtml::_('select.option', 'copy', JText::_('COM_HTML5FLIPPINGBOOK_BE_COPY'), 'value', 'text'),
					JHtml::_('select.option', 'move', JText::_('COM_HTML5FLIPPINGBOOK_BE_MOVE'), 'value', 'text'),
					);
				
				echo JHtml::_('select.radiolist', $options, 'batchAction', null, 'value', 'text', 'move');
				?>
			</div>
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="modal-footer">
		<button class="btn" type="button" onclick="document.id('batch-category-id').value='';document.id('batch-client-id').value='';document.id('batch-language-id').value=''" data-dismiss="modal">
			<?php echo JText::_('JCANCEL'); ?>
		</button>
		<button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('publication.batch');">
			<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
		</button>
	</div>
</div>