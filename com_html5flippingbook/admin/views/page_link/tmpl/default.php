<?php defined('_JEXEC') or die('Restricted access');
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

    function updateInEditor(pagenum)
    {
        window.parent.jReplaceSelectedContents('<a href="#page/'+pagenum+'">{$selection}</a>', '<?=$this->e_name;?>');
    }

</script>

    <div class="html5fb_upload_lightbox_title">
        <?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGELINK_BUTTON'); ?>
    </div>

    <div class="html5fb_upload_lightbox_row ">
        <div class="html5fb_file_upload_controls form-horizontal">
			<div class="control-group">
				<div class="controls">
					<?php echo JHtml::_('select.genericlist', $this->pages, 'pages', 'style="width:100%"' , 'value', 'text', ''); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="controls pull-right">
					<button onclick="window.parent.SqueezeBox.close();" type="button" class="btn">Cancel</button>
					<button onclick="updateInEditor(jQuery('#pages').val());window.parent.SqueezeBox.close();" type="button" class="btn btn-primary">Insert</button>
				</div>
			</div>

        </div>
    </div>