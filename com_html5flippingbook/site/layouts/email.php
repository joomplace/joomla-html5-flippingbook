<?php
/**
 * HTML5FlippingBook Component
 * @package HTML5FlippingBook
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

$user = JFactory::getUser();
?>
<script type="text/javascript">
	function sendEmail(task) {
		if (task == 'sendEmail') {
			var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
			var reqEl = jQuery('#emailModal input[aria-required="true"]');

			for (var i = 0; i < reqEl.length; i++) {
				if (jQuery(reqEl[i]).val() == '' || ((reqEl[i].id == 'recipient' || reqEl[i].id == 'from') && !filter.test(jQuery(reqEl[i]).val()))) {
					alert(Joomla.JText._('COM_HTML5FLIPPINGBOOK_FE_MAILTO_ERROR'));
					jQuery(reqEl[i]).focus();
					return false;
				}
			}

			document.adminForm.submit();
		}
		return true;
	}
</script>
<div class="html5fb-overlay"></div>
<div class="html5fb modal hide fade" id="emailModal" style="display: none">
	<div class="modal-header">
		<button type="button" role="presentation" class="close" style="z-index: 2000" data-dismiss="modal">x</button>
		<h3><?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_MAILTO_MODAL_HEADER');?></h3>
	</div>
	<div class="modal-body form-horizontal">
		<form action="<?php echo JRoute::_('index.php?option=com_html5flippingbook&task=sendEmail');?>" id="adminForm" name="adminForm" method="post">
			<div class="control-group">
				<div class="control-label">
					<label id="recipient-lbl" for="recipient" class="hasTooltip required"><?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_MAILTO_EMAIL_TO'); ?><span class="star">&nbsp;*</span></label>
				</div>
				<div class="controls">
					<input type="text" name="recipient" id="recipient" value="" class="inputbox" size="30" required="" aria-required="true">
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<label id="sender-lbl" for="sender" class="hasTooltip required"><?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_MAILTO_SENDER'); ?><span class="star">&nbsp;*</span></label>
				</div>
				<div class="controls">
					<input type="text" name="sender" id="sender" value="<?php echo ($user->get('id') ? $user->get('name') : '');?>" class="inputbox" size="30" required="" aria-required="true">
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<label id="from-lbl" for="from" class="hasTooltip required"><?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_MAILTO_YOUR_EMAIL'); ?><span class="star">&nbsp;*</span></label>
				</div>
				<div class="controls">
					<input type="text" name="from" id="from" value="<?php echo ($user->get('id') ? $user->get('email') : '');?>" class="inputbox" size="30" required="" aria-required="true">
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<label id="subject-lbl" for="from" class="hasTooltip required"><?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_MAILTO_SUBJECT'); ?><span class="star">&nbsp;*</span></label>
				</div>
				<div class="controls">
					<input type="text" name="subject" id="subject" value="" class="inputbox" size="30" required="" aria-required="true">
				</div>
			</div>
			<input type="hidden" name="option" value="com_html5flippingbook" />
			<input type="hidden" name="task" value="sendEmail" />
			<input type="hidden" name="publID" value="" />
			<?php echo JHtml::_('form.token'); ?>
		</form>
	</div>
	<div class="modal-footer">
		<button class="btn" id="closeBtn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_MAILTO_CANCEL'); ?></button>
		<button class="btn btn-primary" onclick="sendEmail('sendEmail'); return false;"><?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_MAILTO_SEND'); ?></button>
	</div>
</div>