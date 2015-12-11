<?php
/**
 * HTML5FlippingBook Component
 * @package HTML5FlippingBook
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined("_JEXEC") or die();
$friends = $displayData['friends'];
?>
<div class="html5fb modal hide fade" id="jomshareModal" style="display: none">
	<div class="modal-header">
		<button type="button" role="presentation" class="close" style="z-index: 2000" data-dismiss="modal">x</button>
		<h3><?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_ACTION_JOMSOCIAL_SHARE');?></h3>
	</div>
	<div class="modal-body center">
		<div class="button-block">
			<button class="btn btn-large btn-success" type="button" onclick="return shareAjaxAction('post');"><i class="fa fa-comment-o"></i> <?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_JSSHARE_BUTTON_POST');?></button>
			<button class="btn btn-large btn-info" type="button" onclick="return showPrivate();"><i class="fa fa-envelope-o"></i> <?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_JSSHARE_BUTTON_PR_MESS');?></button>
		</div>
		<div class="form-horizontal" style="display: none">
			<div class="control-group">
				<div class="control-label">
					<label id="friends-lbl" for="friends"><?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_JSSHARE_FIELD_FRIEND'); ?><span class="star">&nbsp;*</span></label>
				</div>
				<div class="controls">
					<select name="friends" id="friends">
						<option value="-1"><?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_JSSHARE_FIELD_FRIEND_OPT_0');?></option>
						<?php foreach ($friends as $friend):?>
							<option value="<?php echo $friend->id;?>"><?php echo $friend->name;?></option>
						<?php endforeach;?>
					</select>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<label id="subject-lbl" for="subject"><?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_JSSHARE_FIELD_SUBJECT'); ?></label>
				</div>
				<div class="controls">
					<input type="text" name="subject" id="subject" value="" class="inputbox" size="30">
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<label id="body-lbl" for="body"><?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_JSSHARE_FIELD_MESS'); ?></label>
				</div>
				<div class="controls">
					<textarea name="body" id="body" rows="3"></textarea>
				</div>
			</div>
			<div class="form-actions">
				<button type="submit" class="btn btn-primary" onclick="return shareAjaxAction('sendprivate');"><?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_JSSHARE_BUTTON_SEND');?></button>
				<button type="button" onclick="return jQuery('#jomshareModal').modal('hide');" class="btn"><?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_JSSHARE_BUTTON_CANCEL');?></button>
			</div>
		</div>
	</div>
	<div>
		<input type="hidden" name="publID" value="" />
	</div>
</div>