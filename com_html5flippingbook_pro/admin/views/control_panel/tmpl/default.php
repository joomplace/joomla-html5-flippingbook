<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
?>
<script type="text/javascript">
	
	jQuery(document).ready(function ()
	{
	    getLatestNews();
	});
	
	function onBtnCheckLatestVersionClick(sender, event)
	{
		var resultDiv = document.getElementById('html5fbLatestVersion');
        if(resultDiv) {
            resultDiv.innerHTML = '<img src="<?php echo COMPONENT_IMAGES_URL . 'ajax_loader_16x11.gif'; ?>" />';
        }
		var url = '<?php echo JURI::root().'administrator/index.php?option='.COMPONENT_OPTION.'&task=general.get_latest_component_version'; ?>';
		var xmlData = "";
		var syncObject = {};
		var timeout = 5000;
		var dataCallback = function(request, syncObject, responseText) { onGetLatestVersionData(request, syncObject, responseText); };
		var timeoutCallback = function(request, syncObject) { onGetLatestVersionTimeout(request, syncObject); };
		
		MyAjax.makeRequest(url, xmlData, syncObject, timeout, dataCallback, timeoutCallback);
	}
	
	function onGetLatestVersionData(request, syncObject, responseText)
	{
		var resultDiv = document.getElementById('html5fbLatestVersion');
		
		// Handling XML.
		
		var xmlDoc = MethodsForXml.getXmlDocFromString(responseText);
		var rootNode = xmlDoc.documentElement;
		
		var error = MethodsForXml.getNodeValue(rootNode.childNodes[0]);
		var status = MethodsForXml.getNodeValue(rootNode.childNodes[1]);
		var version = MethodsForXml.getNodeValue(rootNode.childNodes[2]);
		var changelog = MethodsForXml.getNodeValue(rootNode.childNodes[3]);
		var link = MethodsForXml.getNodeValue(rootNode.childNodes[4]);

		// Handling data.
        if(resultDiv) {
            if (error == "" && status == 200) {
                if (version == "<?php echo $this->config->component_version; ?>") {
                    resultDiv.innerHTML = '<font color="green">' + version + '</font>';
                }
                else {
                    jQuery('#body').html('<h3>Version: ' + version + '</h3>' +
                        changelog +
                        '<br/><hr/>If you want to see full list of component changes, please follow this link: ' + '<a href="' + link + '" target="_blank">Component changelog</a>');
                    jQuery('#changelogModal').modal('show');
                    resultDiv.innerHTML = '<button class="btn btn-small" onclick="onBtnCheckLatestVersionClick(this, event);"><i class="icon-health"></i>Check now</button>';
                }
            }
            else {
                resultDiv.innerHTML = '<font color="red">' + '<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_CONNECTION_FAILED'); ?>: ' + error + (error == '' ? '' : ', ') +
                    (status == -100 ? '<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_TIMEOUT'); ?>' : status) + '</font>';
            }
        }
	}
	
	function onGetLatestVersionTimeout(request, syncObject)
	{
		var resultDiv = document.getElementById('html5fbLatestVersion');
        if(resultDiv) {
            resultDiv.innerHTML = '<font color="red">' + '<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_CONNECTION_FAILED'); ?>: ' +
                '<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_TIMEOUT'); ?>' + '</font>';
        }
	}
	
	function getLatestNews()
	{
		var url = '<?php echo JURI::root().'administrator/index.php?option='.COMPONENT_OPTION.'&task=general.get_latest_news'; ?>';
		var xmlData = "";
		var syncObject = {};
		var timeout = 5000;
		var dataCallback = function(request, syncObject, responseText) { onGetLatestNewsData(request, syncObject, responseText); };
		var timeoutCallback = function(request, syncObject) { onGetLatestNewsTimeout(request, syncObject); };
		
		MyAjax.makeRequest(url, xmlData, syncObject, timeout, dataCallback, timeoutCallback);
	}
	
	function onGetLatestNewsData(request, syncObject, responseText)
	{
		var resultDiv = document.getElementById('html5fbLatestNews');
		
		// Handling XML.
		
		var xmlDoc = MethodsForXml.getXmlDocFromString(responseText);
		var rootNode = xmlDoc.documentElement;
		
		var error = MethodsForXml.getNodeValue(rootNode.childNodes[0]);
		var status = MethodsForXml.getNodeValue(rootNode.childNodes[1]);
		var content = MethodsForXml.getNodeValue(rootNode.childNodes[2]);
		
		// Handling data.
        if(resultDiv) {
            if (error == "" && status == 200) {
                resultDiv.innerHTML = content;
            }
            else {
                resultDiv.innerHTML = '<font color="red">' + '<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_CONNECTION_FAILED'); ?>: ' +
                    '<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_TIMEOUT'); ?>' + '</font>';
            }
        }
	}
	
	function onGetLatestNewsTimeout(request, syncObject)
	{
		var resultDiv = document.getElementById('html5fbLatestNews');
		if(resultDiv) {
            resultDiv.innerHTML = '<font color="red">' + '<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_CONNECTION_FAILED'); ?>: ' +
                '<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_TIMEOUT'); ?>' + '</font>';
        }
	}
	
	function onBtnShowChangelogClick(sender, event)
	{
		var link = '<?php echo 'index.php?option='.COMPONENT_OPTION.'&task=general.show_changelog&tmpl=component'; ?>';
		var width = 620;
		var height = 620;
		
		var linkElement = document.createElement('a');
		linkElement.href = link;
		
		SqueezeBox.fromElement(linkElement, { handler: 'iframe', size: { x: width, y: height }, url: link });
	}
	
</script>

<?php echo HtmlHelper::getMenuPanel(); ?>

<div id="j-sidebar-container" class="span6" style="margin-left: 0px;">
	<div class="html5fb_dashboard">
		<div class="btn" onclick="window.location = 'index.php?option=<?php echo COMPONENT_OPTION; ?>&view=categories';">
			<img src="<?php echo COMPONENT_IMAGES_URL.'icon_48_categories.png'; ?>">
			<div><?php echo $this->escape(JText::_('COM_HTML5FLIPPINGBOOK_BE_SUBMENU_CATEGORIES')); ?></div>
		</div>
		<div class="btn" onclick="window.location = 'index.php?option=<?php echo COMPONENT_OPTION; ?>&view=publications';">
			<img src="<?php echo COMPONENT_IMAGES_URL.'icon_48_publications.png'; ?>">
			<div><?php echo $this->escape(JText::_('COM_HTML5FLIPPINGBOOK_BE_SUBMENU_PUBLICATIONS')); ?></div>
		</div>
		<div class="btn" onclick="window.location = 'index.php?option=<?php echo COMPONENT_OPTION; ?>&view=pages';">
			<img src="<?php echo COMPONENT_IMAGES_URL.'icon_48_pages.png'; ?>">
			<div><?php echo $this->escape(JText::_('COM_HTML5FLIPPINGBOOK_BE_SUBMENU_PAGES')); ?></div>
		</div>
		<div class="btn" onclick="window.location = 'index.php?option=<?php echo COMPONENT_OPTION; ?>&view=templates';">
			<img src="<?php echo COMPONENT_IMAGES_URL.'icon_48_templates.png'; ?>">
			<div><?php echo $this->escape(JText::_('COM_HTML5FLIPPINGBOOK_BE_SUBMENU_TEMPLATES')); ?></div>
		</div>
		<div class="btn" onclick="window.location = 'index.php?option=<?php echo COMPONENT_OPTION; ?>&view=resolutions';">
			<img src="<?php echo COMPONENT_IMAGES_URL.'icon_48_resolutions.png'; ?>">
			<div><?php echo $this->escape(JText::_('COM_HTML5FLIPPINGBOOK_BE_SUBMENU_RESOLUTIONS')); ?></div>
		</div>
		<div class="btn" onclick="window.location = 'index.php?option=<?php echo COMPONENT_OPTION; ?>&view=configuration';">
			<img src="<?php echo COMPONENT_IMAGES_URL.'icon_48_settings.png'; ?>">
			<div><?php echo $this->escape(JText::_('COM_HTML5FLIPPINGBOOK_BE_MENU_CONFIGURATION')); ?></div>
		</div>
		<div class="btn" onclick="window.open('https://www.joomplace.com/video-tutorials-and-documentation/html5-flipping-book/index.html?description.htm', '_blank');">
			<img src="<?php echo COMPONENT_IMAGES_URL.'icon_48_help.png'; ?>">
			<div><?php echo $this->escape(JText::_('COM_HTML5FLIPPINGBOOK_BE_SUBMENU_HELP')); ?></div>
		</div>
	</div>
</div>

<div id="j-main-container" class="span6 form-horizontal html5fb_control_panel_container well" style="margin-right: 0px; padding-left: 0px;">
    <div id="pgm_collapse">
        <div class="accordion" id="accordion2">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a style="text-decoration: underline !important;" class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
                        <?php echo "About " . JText::_('COM_HTML5FLIPPINGBOOK'); ?>
                    </a>
                </div>
                <div id="collapseOne" class="accordion-body collapse in">
                    <div class="accordion-inner">
                        <table border="1" width="100%" class="about_table" >
                            <tr>
                                <th colspan="2" class="a_comptitle">
                                    <strong><?php echo JText::_('COM_HTML5FLIPPINGBOOK'); ?></strong> component for Joomla! 3.0 Developed by
                                    <a href="https://www.joomplace.com" target="_blank" rel="noopener noreferrer">JoomPlace</a>.
                                </th>
                            </tr>
                            <tr>
                                <td width="13%"  align="left">Installed version:</td>
                                <td align="left">&nbsp;<b><?php $xml = JFactory::getXML(JPATH_COMPONENT_ADMINISTRATOR .'/html5flippingbook.xml'); echo (string)$xml->version; ?></b>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" align="left">About:</td>
                                <td align="left"><?php echo JText::_('COM_HTML5FLIPPINGBOOK_ABOUT_TEXT'); ?></td>
                            </tr>
                            <tr>
                                <td align="left">Support Helpdesk:</td>
                                <td align="left"><a target="_blank" rel="noopener noreferrer" href="https://www.joomplace.com/support/helpdesk/post-purchase-questions/ticket/create.html">https://www.joomplace.com/support/helpdesk/post-purchase-questions/ticket/create.html</a></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <?php if(!empty($this->errors)){ ?>
                <div class="accordion-group">
                    <div class="accordion-heading" style="background: #DC0000;border-radius: 4px;">
                        <a style="color: #FFF;font-weight: bold;text-decoration: none!important;" class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseErrors">
                            Databse Errors (<?php echo count($this->errors); ?>)
                        </a>
                    </div>
                    <div id="collapseErrors" class="accordion-body collapse">
                        <div class="accordion-inner">
                            <div style="padding: 15px;">
                                <?php foreach ($this->errors as $line => $error) : ?>
                                    <?php $key = 'COM_INSTALLER_MSG_DATABASE_' . $error->queryType;
                                    $msgs = $error->msgElements;
                                    $file = basename($error->file);
                                    $msg0 = (isset($msgs[0])) ? $msgs[0] : ' ';
                                    $msg1 = (isset($msgs[1])) ? $msgs[1] : ' ';
                                    $msg2 = (isset($msgs[2])) ? $msgs[2] : ' ';
                                    $message = JText::sprintf($key, $file, $msg0, $msg1, $msg2); ?>
                                    <p><?php echo $message; ?></p>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a style="text-decoration: underline !important" class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo">
                        <?php echo JText::_("COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_SAY_THANKS_TITLE"); ?>
                    </a>
                </div>
                <div id="collapseTwo" class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="thank_fdiv" style="font-size:12px;margin-left: 4px;">
                            <?php echo JText::_("COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_SAY_THANKS_1"); ?>
                            <a href="https://extensions.joomla.org/extensions/extension/directory-a-documentation/portfolio/html5-flipping-book/" target="_blank" rel="noopener noreferrer">http://extensions.joomla.org/</a>
                            <?php echo JText::_("COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_SAY_THANKS_2"); ?>
                        </div>
                        <div style="float:right; margin:3px 5px 5px 5px;">
                            <a href="https://extensions.joomla.org/extensions/extension/directory-a-documentation/portfolio/html5-flipping-book/" target="_blank" rel="noopener noreferrer">
                                <img src="https://www.joomplace.com/components/com_jparea/assets/images/rate-2.png" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="admin_banners">
    <div class="admin_banner admin_banner_support"><div><?php echo JText::_("COM_HTML5FLIPPINGBOOK_ADMIN_BANNER_SUPPORT"); ?><i class="icon-remove"></i></div></div>
    <div class="admin_banner admin_banner_dev"><div><?php echo JText::_("COM_HTML5FLIPPINGBOOK_ADMIN_BANNER_DEVELOPMENT"); ?><i class="icon-remove"></i></div></div>
    <div class="admin_banner admin_banner_free"><div><?php echo JText::_("COM_HTML5FLIPPINGBOOK_ADMIN_BANNER_FREE_EXTENSION"); ?><i class="icon-remove"></i></div></div>
</div>
<script>
    jQuery(function($){
        $('.admin_banner .icon-remove').on('click', function(){
            $(this).closest('.admin_banner').remove();
        });
    });
</script>
