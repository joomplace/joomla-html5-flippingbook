<?php defined('_JEXEC') or die('Restricted Access');
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
JHtml::_('behavior.formvalidation');

JFactory::getDocument()->addScript(COMPONENT_ASSETS_URL.'js/templatePreview.js');

require_once(JPATH_COMPONENT_ADMINISTRATOR.'/libs/VarsHelper.php');

if ( !empty($this->item->fontsize) )
	JFactory::getDocument()->addStyleDeclaration('.template_preview { font-size: '.$this->item->fontsize.'}');

if ( !empty($this->item->fontfamily) )
	JFactory::getDocument()->addStyleDeclaration('.template_preview { font-family: '.PublicationTemplateFont::FontsList($this->item->fontfamily).'}');

if ( !empty($this->item->p_margin) )
	JFactory::getDocument()->addStyleDeclaration('.template_preview p { margin: '.$this->item->p_margin.' 0}');

if ( !empty($this->item->p_lineheight) )
	JFactory::getDocument()->addStyleDeclaration('.template_preview p { line-height: '.$this->item->p_lineheight.'}');

if ( !empty($this->item->page_background_color) )
	JFactory::getDocument()->addStyleDeclaration('.template_preview { background-color: '.$this->item->page_background_color.'}');

if ( !empty($this->item->background_color) )
	JFactory::getDocument()->addStyleDeclaration('.template_preview > div { background-color: '.$this->item->background_color.'}');

if ( !empty($this->item->text_color) )
	JFactory::getDocument()->addStyleDeclaration('.template_preview > div { color: '.$this->item->text_color.'}');
?>

<script type="text/javascript">
	
	jQuery(document).ready(function ()
	{
	    jQuery('#viewTabs a:first').tab('show');
	});

	Joomla.submitbutton = function(task)
	{
		if (task == 'template.cancel')
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

			<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>

			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('COM_HTML5FLIPPINGBOOK_BE_DETAILS', true)); ?>
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
						<?php echo $this->form->getLabel('template_name'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('template_name'); ?>
					</div>
				</div>
			<?php echo JHtml::_('bootstrap.endTab'); ?>

			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'page-style', JText::_('COM_HTML5FLIPPINGBOOK_BE_TEMPLATES_PAGESTYLE', true)); ?>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('hard_cover'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('hard_cover'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('doublepages'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('doublepages'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('fontfamily'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('fontfamily'); ?>
					</div>
				</div>
				<!--<div class="control-group">
					<div class="control-label">
						<?php //echo $this->form->getLabel('fontsize'); ?>
					</div>
					<div class="controls">
						<?php //echo JHtml::_('select.genericlist', PublicationTemplateFont::FontSize(), 'jform[fontsize]', '' , 'value', 'text', $this->item->fontsize); ?>
					</div>
				</div>-->
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('p_margin'); ?>
					</div>
					<div class="controls">
						<?php echo JHtml::_('select.genericlist', PublicationTemplateFont::P_margin(), 'jform[p_margin]', '' , 'value', 'text', $this->item->p_margin); ?>
					</div>
				</div>
				<!--<div class="control-group">
					<div class="control-label">
						<?php //echo $this->form->getLabel('p_lineheight'); ?>
					</div>
					<div class="controls">
						<?php //echo JHtml::_('select.genericlist', PublicationTemplateFont::P_lineheight(), 'jform[p_lineheight]', '' , 'value', 'text', $this->item->p_lineheight); ?>
					</div>
				</div>-->
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('page_background_color'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('page_background_color'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('background_color'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('background_color'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('text_color'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('text_color'); ?>
					</div>
				</div>
			<?php echo JHtml::_('bootstrap.endTab'); ?>

			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'elements-display', JText::_('COM_HTML5FLIPPINGBOOK_BE_TEMPLATES_DISPLAY_ELEMENTS', true)); ?>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('display_slider'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('display_slider'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('slider_thumbs'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('slider_thumbs'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('display_pagebox'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('display_pagebox'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('display_title'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('display_title'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('display_topicons'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('display_topicons'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('display_nextprev'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('display_nextprev'); ?>
					</div>
				</div>
				<!--<div class="control-group">
					<div class="control-label">
						<?php //echo $this->form->getLabel('show_shadow'); ?>
					</div>
					<div class="controls">
						<?php //echo $this->form->getInput('show_shadow'); ?>
					</div>
				</div>-->

			<?php echo JHtml::_('bootstrap.endTab'); ?>

			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'preview', JText::_('COM_HTML5FLIPPINGBOOK_BE_TEMPLATES_PREVIEW', true)); ?>
					<span class="label label-info"><?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_TEMPLATES_PREVIEW_NEEDSAVE'); ?></span>

		<div class="fb_topBar" ignore="1">

			<h2 id="fb_bookname" style="display: block;">Joomla! Documentation</h2>

			<div class="tb_social" style="float: right; margin-left: 0px;">
				<i title="Table of contents" class="tbicon table-contents"></i>
				<i title="Share on facebook" class="tbicon share-facebook"></i>
				<i title="Share on Twitter" class="tbicon share-twitter"></i>
				<i title="Share on G+" class="tbicon share-plus"></i>
			</div>
		</div>

				<div class="template_preview">
					<div>
						<div class="template_preview_previous-button"></div>
						<div>
							<h1>Learn More</h1>
							<b>From Joomla! Documentation</b>
							<p>Joomla is an award-winning content management system (CMS), which enables you to build Web sites and powerful online applications. Many aspects, including its ease-of-use and extensibility, have made Joomla the most popular Web site software available. Best of all, Joomla is an open source solution that is freely available to everyone. </p>
							<h3>What's a content management system (CMS)?</h3>
							<p>A content management system is software that keeps track of every piece of content on your Web site, much like your local public library keeps track of books and stores them. Content can be simple text, photos, music, video, documents, or just about anything you can think of. A major advantage of using a CMS is that it requires almost no technical skill or knowledge to manage. The CMS manages all your content, so you don't have too. </p>
						</div>
						<div>
							<h1>I need to build a site for a client. How will Joomla! help me?</h1>
							<p>Joomla is designed to be easy to install and set up even if you're not an advanced user. Many Web hosting services offer a single-click install, getting your new site up and running in just a few minutes.</p>
							<p>Since Joomla is so easy to use, as a Web Designer or Developer, you can quickly build sites for your clients. Then, with a minimal amount of instruction, you can empower your clients to easily manage their own sites themselves.</p>
							<p>If your clients need specialized functionality, Joomla is highly extensible and thousands of extensions (most for free under the GPL license) are available in the Joomla Extensions Directory. </p>
						</div>
						<div class="template_preview_next-button"></div>
					</div>
				</div>

				<div id="page-bar" ignore="1" style="display: block;">
					<label>Go to</label>
					<input type="text" placeholder="page" autocomplete="" value="" id="goto_page_input">
					<span onclick="" id="goto_page_input_button"></span>
				</div>
				<div class="template_preview_slider"><span></span></div>

					<br clear="all"/>
					<br clear="all"/>

			<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>

	</div>
	
</form>