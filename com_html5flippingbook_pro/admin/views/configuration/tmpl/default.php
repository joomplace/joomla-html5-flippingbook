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
JHtml::_('behavior.modal');
JHtml::_('formbehavior.chosen', 'select');
?>

<script type="text/javascript">
	
	var form = null;
	
	jQuery(document).ready(function ()
	{
	    jQuery('#viewTabs a:first').tab('show');
	    jQuery('#socialTabs a:first').tab('show');
		
		updateGooglePlusPreview();
		updateTwitterPreview();
		updateLinkedinPreview();
		updateFacebookPreview();
	});

	
	function updateGooglePlusPreview()
	{
		var size = BootstrapFormHelper.getRadioGroupValue('jform_social_google_plus_size');
		var annotation = BootstrapFormHelper.getRadioGroupValue('jform_social_google_plus_annotation');
		
		var previewImg = document.getElementById('social_google_plus_preview');
		
		previewImg.setAttribute('src', '<?php echo COMPONENT_IMAGES_URL.'social/'; ?>' + 'googleplus-' + size + '-' + annotation + '.png');
	}
	
	function updateTwitterPreview()
	{
		var size = BootstrapFormHelper.getRadioGroupValue('jform_social_twitter_size');
		var annotation = BootstrapFormHelper.getRadioGroupValue('jform_social_twitter_annotation');
		
		var previewImg = document.getElementById('social_twitter_preview');
		
		previewImg.setAttribute('src', '<?php echo COMPONENT_IMAGES_URL.'social/'; ?>' + 'twitter-' + size + '-' + annotation + '.png');
		
		// Showing notice.
		
		var noticeDiv = document.getElementById('social_twitter_preview_notice');
		
		if (size == 'large' && annotation == 'vertical')
		{
			noticeDiv.innerHTML = '<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONFIG_TWITTER_PREVIEW_NOTICE'); ?>';
		}
		else
		{
			noticeDiv.innerHTML = '';
		}
	}
	
	function updateLinkedinPreview()
	{
		var annotation = BootstrapFormHelper.getRadioGroupValue('jform_social_linkedin_annotation');
		
		var previewImg = document.getElementById('social_linkedin_preview');
		
		previewImg.setAttribute('src', '<?php echo COMPONENT_IMAGES_URL.'social/'; ?>' + 'linkedin-' + annotation + '.png');
	}
	
	function updateFacebookPreview()
	{
		var verb = BootstrapFormHelper.getRadioGroupValue('jform_social_facebook_verb');
		var layout = BootstrapFormHelper.getRadioGroupValue('jform_social_facebook_layout');
		
		var previewImg = document.getElementById('social_facebook_preview');
		
		previewImg.setAttribute('src', '<?php echo COMPONENT_IMAGES_URL.'social/'; ?>' + 'facebook-' + verb + '-' + layout + '.png');
	}

	function onRadioGooglePlusSizeClick(sender, event)
	{
		updateGooglePlusPreview();
	}
	
	function onRadioGooglePlusAnnotationClick(sender, event)
	{
		updateGooglePlusPreview();
	}
	
	function onRadioTwitterSizeClick(sender, event)
	{
		updateTwitterPreview();
	}
	
	function onRadioTwitterAnnotationClick(sender, event)
	{
		updateTwitterPreview();
	}
	
	function onRadioLinkedinAnnotationClick(sender, event)
	{
		updateLinkedinPreview();
	}
	
	function onRadioFacebookVerbClick(sender, event)
	{
		updateFacebookPreview();
	}
	
	function onRadioFacebookLayoutClick(sender, event)
	{
		updateFacebookPreview();
	}

	function onResetPermissoionsLinkClick(sender, event)
	{
		if (confirm('<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONFIG_RESET_ALL_PERMISSIONS_CONFIRM'); ?>'))
		{
			var link = '<?php echo JURI::root().'administrator/index.php?option='.COMPONENT_OPTION.'&task=configuration.reset_permissions&tmpl=component'; ?>';
			var width = 350;
			var height = 120;
			
			var linkElement = document.createElement('a');
			linkElement.href = link;
			
			SqueezeBox.fromElement(linkElement, { handler: 'iframe', size: { x: width, y: height }, url: link });
		}
	}
	
	Joomla.submitbutton = function(task)
	{
		if (task == 'configuration.cancel')
		{
			Joomla.submitform(task, document.adminForm);
			return;
		}
		
		if (task == 'configuration.apply')
		{
			Joomla.removeMessages();

			if (!document.formvalidator.isValid(document.adminForm))
			{
				alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
				return;
			}
			
			Joomla.submitform(task, document.adminForm);
		}
	}
	
</script>

<?php echo HtmlHelper::getMenuPanel(); ?>

<form name="adminForm" id="adminForm" action="index.php" method="post" autocomplete="off" class="form-validate">
	<input type="hidden" name="option" value="<?php echo COMPONENT_OPTION; ?>" />
	<input type="hidden" name="view" value="<?php echo $this->getName(); ?>" />
	<input type="hidden" name="layout" value="<?php echo $this->getLayout(); ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
	
	<div id="j-main-container" class="span12 form-horizontal">
		
		<ul class="nav nav-tabs" id="viewTabs">
			<li><a href="#tab_social" data-toggle="tab"><?php echo  JText::_("COM_HTML5FLIPPINGBOOK_BE_CONFIG_SOCIAL_TAB");?></a></li>
			<li><a href="#tab_global_permissions" data-toggle="tab"><?php echo  JText::_("COM_HTML5FLIPPINGBOOK_BE_CONFIG_PERMISSIONS_TAB");?></a></li>
		</ul>
		
		<div class="tab-content">
			
			<div class="tab-pane" id="tab_social">
				
				<ul class="nav nav-tabs" id="socialTabs">
					<li><a href="#tab_social_google" data-toggle="tab"><?php echo  JText::_("COM_HTML5FLIPPINGBOOK_BE_CONFIG_GOOGLEPLUS_SUBPANEL");?></a></li>
					<li><a href="#tab_social_twitter" data-toggle="tab"><?php echo  JText::_("COM_HTML5FLIPPINGBOOK_BE_CONFIG_TWITTER_SUBPANEL");?></a></li>
					<li><a href="#tab_social_linkedin" data-toggle="tab"><?php echo  JText::_("COM_HTML5FLIPPINGBOOK_BE_CONFIG_LINKEDIN_SUBPANEL");?></a></li>
					<li><a href="#tab_social_facebook" data-toggle="tab"><?php echo  JText::_("COM_HTML5FLIPPINGBOOK_BE_CONFIG_FACEBOOK_SUBPANEL");?></a></li>
					<li><a href="#tab_social_jomsocial" data-toggle="tab"><?php echo  JText::_("COM_HTML5FLIPPINGBOOK_BE_CONFIG_JOMSOCIAL_SUBPANEL");?></a></li>
					<li><a href="#tab_social_email" data-toggle="tab"><?php echo  JText::_("COM_HTML5FLIPPINGBOOK_BE_CONFIG_EMAIL_SUBPANEL");?></a></li>
				</ul>
				
				<div class="tab-content">
					
					<?php
					//==================================================
					// Google+.
					//==================================================
					?>
					
					<div class="tab-pane" id="tab_social_google">
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('social_google_plus_use'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('social_google_plus_use'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('social_google_plus_size'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('social_google_plus_size'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('social_google_plus_annotation'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('social_google_plus_annotation'); ?>
							</div>
						</div>
						<div class="control-group">
							<?php
							$input = $this->form->getField('social_google_plus_language');
							$input->addOptions($this->googlePlusLanguageOptions);
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
								<?php
								echo JHTML::_("tooltip", JText::_("COM_HTML5FLIPPINGBOOK_BE_CONFIG_GOOGLEPLUS_PREVIEW_DESC") . '<br/><br/>' .
									'<span>' . "* " . JText::_("COM_HTML5FLIPPINGBOOK_BE_CONFIG_GOOGLEPLUS_PREVIEW_NOLANG") . '</span>',
									JText::_("COM_HTML5FLIPPINGBOOK_BE_CONFIG_GOOGLEPLUS_PREVIEW"), null,
									'<label>' . JText::_("COM_HTML5FLIPPINGBOOK_BE_CONFIG_GOOGLEPLUS_PREVIEW") . '</label>', null);
								?>
							</div>
							<div class="controls">
								<img id="social_google_plus_preview" class="html5fb_google_plus_preview" />
							</div>
						</div>
					</div>
					
					<?php
					//==================================================
					// Twitter.
					//==================================================
					?>
					
					<div class="tab-pane" id="tab_social_twitter">
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('social_twitter_use'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('social_twitter_use'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('social_twitter_size'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('social_twitter_size'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('social_twitter_annotation'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('social_twitter_annotation'); ?>
							</div>
						</div>
						<div class="control-group">
							<?php
							$input = $this->form->getField('social_twitter_language');
							$input->addOptions($this->twitterLanguageOptions);
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
								<?php
								echo JHTML::_("tooltip", JText::_("COM_HTML5FLIPPINGBOOK_BE_CONFIG_TWITTER_PREVIEW_DESC") . '<br/><br/>' .
									'<span>' . "* " . JText::_("COM_HTML5FLIPPINGBOOK_BE_CONFIG_TWITTER_PREVIEW_NOLANG") . '</span>',
									JText::_("COM_HTML5FLIPPINGBOOK_BE_CONFIG_TWITTER_PREVIEW"), null,
									'<label>' . JText::_("COM_HTML5FLIPPINGBOOK_BE_CONFIG_TWITTER_PREVIEW") . '</label>', null);
								?>
							</div>
							<div class="controls">
								<img id="social_twitter_preview" class="html5fb_twitter_preview" />
								<div id="social_twitter_preview_notice" class="html5fb_twitter_preview_notice"></div>
							</div>
						</div>
					</div>
					
					<?php
					//==================================================
					// LinkedIn.
					//==================================================
					?>
					
					<div class="tab-pane" id="tab_social_linkedin">
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('social_linkedin_use'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('social_linkedin_use'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('social_linkedin_annotation'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('social_linkedin_annotation'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php
								echo JHTML::_("tooltip", JText::_("COM_HTML5FLIPPINGBOOK_BE_CONFIG_LINKEDIN_PREVIEW_DESC"),
									JText::_("COM_HTML5FLIPPINGBOOK_BE_CONFIG_LINKEDIN_PREVIEW"), null,
									'<label>' . JText::_("COM_HTML5FLIPPINGBOOK_BE_CONFIG_LINKEDIN_PREVIEW") . '</label>', null);
								?>
							</div>
							<div class="controls">
								<img id="social_linkedin_preview" class="html5fb_linkedin_preview" />
							</div>
						</div>
					</div>
					
					<?php
					//==================================================
					// Facebook.
					//==================================================
					?>
					
					<div class="tab-pane" id="tab_social_facebook">
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('social_facebook_use'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('social_facebook_use'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('social_facebook_verb'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('social_facebook_verb'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('social_facebook_layout'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('social_facebook_layout'); ?>
							</div>
						</div>
						<div class="control-group">
							<?php
							$input = $this->form->getField('social_facebook_font');
							$input->addOptions($this->facebookFontOptions);
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
								<?php
								echo JHTML::_("tooltip", JText::_("COM_HTML5FLIPPINGBOOK_BE_CONFIG_FACEBOOK_PREVIEW_DESC") . '<br/><br/>' .
									'<span>' . "* " . JText::_("COM_HTML5FLIPPINGBOOK_BE_CONFIG_FACEBOOK_PREVIEW_NOFONT") . '</span>',
									JText::_("COM_HTML5FLIPPINGBOOK_BE_CONFIG_FACEBOOK_PREVIEW"), null,
									'<label>' . JText::_("COM_HTML5FLIPPINGBOOK_BE_CONFIG_FACEBOOK_PREVIEW") . '</label>', null);
								?>
							</div>
							<div class="controls">
								<img id="social_facebook_preview" class="html5fb_facebook_preview" />
							</div>
						</div>
					</div>

					<?php
					//==================================================
					// JomSocial.
					//==================================================
					?>

					<div class="tab-pane" id="tab_social_jomsocial">
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('social_jomsocial_use'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('social_jomsocial_use'); ?>
							</div>
						</div>
					</div>

					<?php
					//==================================================
					// Email.
					//==================================================
					?>

					<div class="tab-pane" id="tab_social_email">
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('social_email_use'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('social_email_use'); ?>
							</div>
						</div>
					</div>
				</div>
				
			</div>
			
			<div class="tab-pane" id="tab_global_permissions">
				<div class="controls html5fb_global_permissions">
					<?php echo $this->form->getInput('rules'); ?>
					<button class="btn btn-small" onclick="onResetPermissoionsLinkClick(this, event);">
						<i class="icon-key"></i>
						<?php echo JText::_("COM_HTML5FLIPPINGBOOK_BE_CONFIG_RESET_ALL_PERMISSIONS"); ?>
					</button>
				</div>
				
			</div>
			
		</div>
		
	</div>
	
</form>