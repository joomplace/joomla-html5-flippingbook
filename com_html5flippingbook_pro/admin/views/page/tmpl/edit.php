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

	var form = null;

    function isBrowserIE(){
        return navigator.appName=="Microsoft Internet Explorer";
    }

    function IeCursorFix(){
        if (isBrowserIE()){
            tinyMCE.execCommand('mceInsertContent', false, '');
            global_ie_bookmark = tinyMCE.activeEditor.selection.getBookmark(false);
        }
        return true;
    }

	function getFormControls(){
        return { pageImageSelect : document.getElementById('jform_page_image') };
    }

    function checkEditorInvalidElements(){
        var editorInvalidElements = ['iframe'],
            editor = tinyMCE.get('jform_c_text'),
            isInvalid = false;
        if(typeof editor.settings.invalid_elements !='undefined') {
            editorInvalidElements.forEach(function(value){
                if (editor.settings.invalid_elements.indexOf(value) >= 0) {
                    isInvalid = true;
                    alert('<?php echo  JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_EXCLUDE_FROM_PROHIBITED'); ?>' + value);
                }
            });
        }
        if(isInvalid){
            return false;
        }
        return true;
    }

    function jInsertEditorText( text, editor ){
        if(isBrowserIE()){
            if (window.parent.tinyMCE) {
                window.parent.tinyMCE.selectedInstance.selection.moveToBookmark(window.parent.global_ie_bookmark);
            }
        }
        tinyMCE.get(editor).execCommand('mceInsertContent',false,text);
        return false;
    }

    jQuery(function($) {
	    $('#viewTabs a:first').tab('show');
		form = getFormControls();
		refreshPageTypeControls();

		//set custom settings in tinyMCE (fontsize select)
        setTimeout(function(){
            var editor = tinyMCE.activeEditor,
                defaultInit = editor.settings,
                customInit = $.extend({}, defaultInit);
            customInit.selector = 'jform_c_text';
            customInit.toolbar1 = customInit.toolbar1.replace(/fontsizeselect/, 'fontsizeselectcustom');
            customInit.setup = function (editor) {
                editor.addButton('fontsizeselectcustom', function() {
                    var items = [],
                        fontsize_formats = '0.125em 0.25em 0.5em 0.625em 0.75em 0.875em 1em ' +
                            '1.125em 1.25em 1.5em 1.625em 1.75em 1.875em 2em ' +
                            '2.25em 2.5em 2.75em 3em 3.5em 4em';
                    fontsize_formats.split(' ').each(function(item) {
                        items.push({text: item, value: item});
                    });
                    return {
                        type: 'listbox',
                        text: 'Font Sizes',
                        tooltip: 'Font Sizes',
                        values: items,
                        fixedWidth: true,
                        onclick: function(e) {
                            if (e.control.settings.value) {
                                editor.execCommand('FontSize', false, e.control.settings.value);
                            }
                        }
                    };
                });
                editor.addButton('button-0Image', function() {
                    var link = '<?php echo JUri::base(); ?>' + 'index.php?option=com_media&view=images&tmpl=component&e_name=jform_c_text&asset=com_html5flippingbook';
                    return {
                        type: 'button',
                        text: 'Image',
                        tooltip: 'Image',
                        icon: 'none icon-pictures',
                        onclick: function() {
                            var modalOptions={
                                title: 'Image',
                                url: link,
                                buttons: [{text: "Close",onclick:"close"}],
                                width: 800,
                                height: 500,
                            };
                            editor.windowManager.open(modalOptions);
                        }
                    };
                });
            };
            editor.remove();
            tinyMCE.init(customInit);
            tinyMCE.execCommand('mceToggleEditor', false, 'jform_c_text');
        }, 1000);

    });

	function onShowAdditionalLinkClick(sender, event){
		refreshAdditionalLinkControls();
	}

	function onPageTypeClick(sender, event){
		refreshPageTypeControls();
	}
	
	function refreshPageTypeControls(){
		var pageType = BootstrapFormHelper.getRadioGroupValue('jform_page_type');
		var imageTypeControls = document.getElementById('image_type_controls');
		var textTypeControls = document.getElementById('text_type_controls');
		var allTypesControls = [imageTypeControls, textTypeControls];
		for (var i = 0; i < allTypesControls.length; i++){
			allTypesControls[i].style.display = 'none';
		}
		switch(pageType){
			case 'image': imageTypeControls.style.display = 'block'; break;
			case 'text': textTypeControls.style.display = 'block'; break;
		}
	}
	
	Joomla.submitbutton = function(task){
		if (task == 'page.cancel'){
			Joomla.submitform(task, document.adminForm);
			return;
		}
		Joomla.removeMessages();
		BootstrapFormValidator.restoreControlsDefaultState([form.pageImageSelect]);
		if (!document.formvalidator.isValid(document.adminForm)){
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			return;
		}
		var error = false;
		var pageType = BootstrapFormHelper.getRadioGroupValue('jform_page_type');
		switch (pageType){
			case 'image': {
				error = BootstrapFormValidator.checkSelectControlsEmptyValues([form.pageImageSelect], '<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_UNDEFINED_VALUE'); ?>');
				if (error) return;
				break;
			}
		}
		Joomla.submitform(task, document.adminForm);
	}

	function YoutubeInsert(){
        if(!checkEditorInvalidElements()){
            return false;
        }
		IeCursorFix();
		youtubeLink = prompt("<?php echo str_replace('[n]', '\n', JText::_('COM_HTML5FLIPPINGBOOK_BE_INSERT_BUTTON_YOUTUBE_PROMT') ); ?>", '');
		if(youtubeLink){
			youtubeheight = prompt("<?php echo str_replace('[n]', '\n', JText::_('COM_HTML5FLIPPINGBOOK_BE_INSERT_BUTTON_YOUTUBE_HEIGHT') ); ?>", '300');
			if (!youtubeheight){
				youtubeheight = 300;
			}
			patt1= /\?v=([^\&]+)/gi;
			link = new String( youtubeLink.match(patt1) );
			jInsertEditorText('<iframe width="100%" height="'+youtubeheight+'" src="//www.youtube.com/embed/'+link.replace('?v=', '')+'" frameborder="0" allowfullscreen webkitallowfullscreen mozallowfullscreen></iframe>', 'jform_c_text');
		}
	}

	function VimeoInsert(){
        if(!checkEditorInvalidElements()){
            return false;
        }
		IeCursorFix();
		vimeoLink = prompt("<?php echo str_replace('[n]', '\n', JText::_('COM_HTML5FLIPPINGBOOK_BE_INSERT_BUTTON_VIMEO_PROMT') ); ?>", '');
		if (vimeoLink){
			videmoheight = prompt("<?php echo str_replace('[n]', '\n', JText::_('COM_HTML5FLIPPINGBOOK_BE_INSERT_BUTTON_YOUTUBE_HEIGHT') ); ?>", '300');
			if (!videmoheight){
				videmoheight = 300;
			}
			patt1= /\.com\/([\d]+)/gi;
			link = new String( vimeoLink.match(patt1) );
			jInsertEditorText('<iframe width="100%" height="'+videmoheight+'" src="//player.vimeo.com/video/'+link.replace('.com/', '')+'" frameborder="0" allowfullscreen webkitallowfullscreen mozallowfullscreen></iframe>', 'jform_c_text');
		}
	}

	//button "Link to page"
    function jReplaceSelectedContents(text, editor) {
        tinyMCE.execCommand('mceReplaceContent', false, text);
        return false;
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
                            <div style="width:<?php echo (int)$this->item->page_resolution->width; ?>px;">
                                <?php echo $this->form->getInput('c_text'); ?>
                            </div>
                            <div id="editor-xtd-buttons" class="btn-toolbar pull-left">
                                <div class="btn-toolbar">
									<a rel="{handler: 'iframe', size: {x: 370, y: 125}}" id="additonInsertLink" href="<?='index.php?option='.COMPONENT_OPTION.'&tmpl=component&view=page_link&e_name=jform_c_text&publication_id='.$this->item->publication_id;?>" class="btn modal btn-success"><i class="icon-flag-2"></i> <?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGELINK_BUTTON'); ?></a>
                                    <a rel="{handler: 'iframe', size: {x: 370, y: 215}}" id="additonInsertMusic" href="<?='index.php?option='.COMPONENT_OPTION.'&tmpl=component&view=upload_file_dialog&type=audio&e_name=jform_c_text';?>" class="btn modal"><i class="icon-music"></i> <?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_INSERT_BUTTON_AUDIO'); ?></a>
                                    <a rel="{handler: 'iframe', size: {x: 370, y: 215}}" id="additonInsertVideo" href="<?='index.php?option='.COMPONENT_OPTION.'&tmpl=component&view=upload_file_dialog&type=video&e_name=jform_c_text';?>" class="btn modal btn-inverse"><i class="icon-play"></i> <?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_INSERT_BUTTON_VIDEO'); ?></a>
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