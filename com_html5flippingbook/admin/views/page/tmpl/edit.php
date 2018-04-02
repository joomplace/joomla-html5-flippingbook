<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.modal');

$doc = JFactory::getDocument();
$doc->addScript('/administrator/components/com_html5flippingbook/assets/js/html2canvas.min.js');
?>

<script type="text/javascript">
	
	var form = null;

	<?php echo HtmlHelper::tinyMCE_js(
		$this->item->page_resolution->width,
		($this->item->page_resolution->height+90),
		 COMPONENT_ASSETS_URL_FRONT .'css/tinymce_default.css, '.JRoute::_(JUri::root().'index.php?option='.COMPONENT_OPTION.'&task=templatecss&template_id=1'),
		 'jform_c_text'
		);
	?>
	
	jQuery(document).ready(function() {
	    jQuery('#viewTabs a:first').tab('show');
		form = getFormControls();
		refreshPageTypeControls();
	});


	function onShowAdditionalLinkClick(sender, event)
	{
		refreshAdditionalLinkControls();
	}

	function onPageTypeClick(sender, event)
	{
		refreshPageTypeControls();
	}
	
	function refreshPageTypeControls()
	{
		var pageType = BootstrapFormHelper.getRadioGroupValue('jform_page_type');
		
		var imageTypeControls = document.getElementById('image_type_controls');
		var textTypeControls = document.getElementById('text_type_controls');
		
		var allTypesControls = [imageTypeControls, textTypeControls];
		
		for (var i = 0; i < allTypesControls.length; i++) {
			allTypesControls[i].style.display = 'none';
		}
		
		switch (pageType)
		{
			case 'image': imageTypeControls.style.display = 'block'; break;
			case 'text': textTypeControls.style.display = 'block'; break;
		}
	}

	Joomla.submitbutton = function(task)
	{
		if (task == 'page.cancel') {
			Joomla.submitform(task, document.adminForm);
			return;
		}
		
		Joomla.removeMessages();
		BootstrapFormValidator.restoreControlsDefaultState([form.pageImageSelect]);
		
		if (!document.formvalidator.isValid(document.adminForm)) {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			return;
		}
		
		var error = false;
		var pageType = BootstrapFormHelper.getRadioGroupValue('jform_page_type');
		
		switch (pageType) {
			case 'image': {
				error = BootstrapFormValidator.checkSelectControlsEmptyValues([form.pageImageSelect], '<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_UNDEFINED_VALUE'); ?>');
				if (error) return;
				break;
			}
		}

		// Sending data to the server to create a preview of the HTML page
        if ( (task == 'page.apply' || task == 'page.save' || task == 'page.saveandnew')
                && document.getElementById('jform_page_type1').checked  //type == html
                    && !!document.createElement('canvas').getContext
        ){
            htmlPageToCanvas(task);
        }
        else {
            Joomla.submitform(task, document.adminForm);
        }

	}

    // Sending data to the server to create a preview of the HTML page
	function htmlPageToCanvas(task){
        html2canvas(document.getElementById('jform_c_text_ifr')).then(function (canvas) {
            //Take 80% of the editor's height, so that in the preview there was no empty space on bottom.
            var ifr = document.getElementById('jform_c_text_ifr'),
                ifrSizes = ifr.getBoundingClientRect(),
                ifrStyles = getComputedStyle(ifr.contentWindow.document.body),
                cropper = document.createElement('canvas').getContext('2d');
            cropper.canvas.width = parseInt(ifrSizes.width);
            cropper.canvas.height = parseInt(ifrSizes.height) * 0.8;
            cropper.drawImage(canvas,
                -(parseInt(ifrStyles.marginLeft) + parseInt(ifrStyles.paddingLeft)),
                -(parseInt(ifrStyles.marginTop) + parseInt(ifrStyles.paddingTop)) );
            //document.body.appendChild(cropper.canvas);

            var inputCanvas = document.createElement('input');
            inputCanvas.type = 'hidden';
            inputCanvas.name = 'jform[canvas]';
            inputCanvas.value = cropper.canvas.toDataURL('image/jpeg');
            document.forms.adminForm.appendChild(inputCanvas);
        }).then(function(){
            Joomla.submitform(task, document.adminForm);
        });
    }

	function YoutubeInsert()
	{
		IeCursorFix();
		youtubeLink = prompt("<?php echo str_replace('[n]', '\n', JText::_('COM_HTML5FLIPPINGBOOK_BE_INSERT_BUTTON_YOUTUBE_PROMT') ); ?>", '');
		if ( youtubeLink )
		{
			youtubeheight = prompt("<?php echo str_replace('[n]', '\n', JText::_('COM_HTML5FLIPPINGBOOK_BE_INSERT_BUTTON_YOUTUBE_HEIGHT') ); ?>", '300');
			if ( !youtubeheight )
			{
				youtubeheight = 300;
			}
			patt1= /\?v=([^\&]+)/gi;
			link = new String( youtubeLink.match(patt1) );
			jInsertEditorText('<iframe width="100%" height="'+youtubeheight+'" src="//www.youtube.com/embed/'+link.replace('?v=', '')+'" frameborder="0" allowfullscreen webkitallowfullscreen mozallowfullscreen></iframe>', 'jform_c_text');
		}
	}

	function VimeoInsert()
	{
		IeCursorFix();
		vimeoLink = prompt("<?php echo str_replace('[n]', '\n', JText::_('COM_HTML5FLIPPINGBOOK_BE_INSERT_BUTTON_VIMEO_PROMT') ); ?>", '');
		if ( vimeoLink )
		{
			videmoheight = prompt("<?php echo str_replace('[n]', '\n', JText::_('COM_HTML5FLIPPINGBOOK_BE_INSERT_BUTTON_YOUTUBE_HEIGHT') ); ?>", '300');
			if ( !videmoheight )
			{
				videmoheight = 300;
			}
			patt1= /\.com\/([\d]+)/gi;
			link = new String( vimeoLink.match(patt1) );
			jInsertEditorText('<iframe width="100%" height="'+videmoheight+'" src="//player.vimeo.com/video/'+link.replace('.com/', '')+'" frameborder="0" allowfullscreen webkitallowfullscreen mozallowfullscreen></iframe>', 'jform_c_text');
		}
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
			<li style="margin-right: 100px;"><a href="#tab_details" data-toggle="tab"><?php echo  JText::_('COM_HTML5FLIPPINGBOOK_BE_DETAILS');?></a></li>
			<li style="margin-left: 10px; <?php if ( empty($this->item->prev_page) ) echo "visibility:hidden;display:none;"; ?>">
				<div class="btn btn-info" onclick="location.href='index.php?option=<?php echo COMPONENT_OPTION; ?>&view=page&layout=edit&id=<?php echo $this->item->prev_page; ?>';">
					<span class="icon-chevron-left"></span> <?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_PREVPAGE'); ?></div>
			</li>
			<li style="margin-left: 10px; <?php if ( empty($this->item->next_page) ) echo "visibility:hidden;display:none;"; ?>">
				<div class="btn btn-info" onclick="location.href='index.php?option=<?php echo COMPONENT_OPTION; ?>&view=page&layout=edit&id=<?php echo $this->item->next_page; ?>';">
					<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_NEXTPAGE'); ?> <span class="icon-chevron-right"></span></div>
			</li>
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
					<?php
					$input = $this->form->getField('publication_id');

					if (empty($this->item->id))
					{
						$input->setValue($this->item->publication_id);
					}
					?>
					<div class="control-label">
						<?php echo $input->getLabel(); ?>
					</div>
					<div class="controls">
						<?php echo $input->getInput(); ?><?php if (!empty($this->item->publication_id)) { ?> <span class="btn btn-small btn-warning" onclick="location.href='index.php?option=<?php echo COMPONENT_OPTION;?>&view=publication&layout=edit&c_id=<?php echo $this->item->publication_id; ?>';" style="margin-left: 4px; position: absolute; margin-top: 1px;"> <?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_EDIT');?> <span class="icon-chevron-right"></span> </span><?php } ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('page_title'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('page_title'); ?>
					</div>
				</div>

                <?php echo $this->form->getLabel('page_hard'); ?>
                <?php echo $this->form->getInput('page_hard'); ?>

				<div class="control-group">
					<?php
					$input = $this->form->getField('page_type');
					?>
					<div class="control-label">
						<?php echo $input->getLabel(); ?>
					</div>
					<div class="controls">
						<?php echo $input->getInput(); ?>
					</div>
				</div>
                <?php echo $this->form->renderField('enable_svg'); ?>
				<div id="image_type_controls" style="display:none;">
					<div class="control-group">
						<?php
						$input = $this->form->getField('page_image');
						$input->setProperty('dir', $this->imagesSubdirRelativeName);
						$input->setProperty('pubid', $this->item->publication_id);
						?>
						<div class="control-label">
							<?php echo $input->getLabel(); ?>
						</div>
						<div class="controls">
							<?php echo $input->getInput(); ?>
						</div>
					</div>
				</div>

				<div id="text_type_controls" style="display:none;">
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('c_text'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('c_text'); ?>
                            <div id="editor-xtd-buttons" class="btn-toolbar pull-left">
                                <div class="btn-toolbar">
									<a rel="{handler: 'iframe', size: {x: 370, y: 125}}" id="additonInsertLink" onclick="IeCursorFix(); return false;" href="<?='index.php?option='.COMPONENT_OPTION.'&tmpl=component&view=page_link&e_name=jform_c_text&publication_id='.$this->item->publication_id;?>" class="btn modal btn-success"><i class="icon-flag-2"></i> <?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGELINK_BUTTON'); ?></a>
                                    <a rel="{handler: 'iframe', size: {x: 370, y: 215}}" id="additonInsertMusic" onclick="IeCursorFix(); return false;" href="<?='index.php?option='.COMPONENT_OPTION.'&tmpl=component&view=upload_file_dialog&type=audio&e_name=jform_c_text';?>" class="btn modal"><i class="icon-music"></i> <?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_INSERT_BUTTON_AUDIO'); ?></a>
                                    <a rel="{handler: 'iframe', size: {x: 370, y: 215}}" id="additonInsertVideo" onclick="IeCursorFix(); return false;" href="<?='index.php?option='.COMPONENT_OPTION.'&tmpl=component&view=upload_file_dialog&type=video&e_name=jform_c_text';?>" class="btn modal btn-inverse"><i class="icon-play"></i> <?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_INSERT_BUTTON_VIDEO'); ?></a>
									<span onclick="return YoutubeInsert();" href="" title="YouTube Video" class="btn modal btn-danger"><i class="icon-play"></i> YouTube</span>
                                    <span onclick="return VimeoInsert();" href="" title="vimeo Video" class="btn modal btn-info"><i class="icon-play"></i> VIMEO</span>
                                </div>
                            </div>
						</div>
					</div>
				</div>
			</div>

		</div>

	</div>
	
</form>
