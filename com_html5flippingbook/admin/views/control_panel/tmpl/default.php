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
		<div class="btn" onclick="window.open('http://www.joomplace.com/video-tutorials-and-documentation/html5-flipping-book/index.html?description.htm', '_blank');">
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
                                    <a href="http://www.JoomPlace.com" target="_blank" rel="noopener noreferrer">JoomPlace</a>.
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
                                <td align="left">Community Forum:</td>
                                <td align="left"><a target="_blank" rel="noopener noreferrer" href="https://www.joomplace.com/forum/joomla-components/html5-flipping-book.html">https://www.joomplace.com/forum/joomla-components/html5-flipping-book.html</a></td>
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
                                <img src="http://www.joomplace.com/components/com_jparea/assets/images/rate-2.png" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>

	<!-- <table class="table"> -->
		<!-- Hide current and latest version since integrate Joomla update system -->
<!--		<tr>-->
<!--			<th colspan="100%" class="html5fb_control_panel_title">-->
<!--				--><?php //echo JText::_('COM_HTML5FLIPPINGBOOK'); ?><!--&nbsp;--><?php //echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_COMPONENT_DESC') .
//					" 3.x+. " . JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_DEVELOPED_BY'); ?><!-- <a href="http://www.joomplace.com/" target="_blank">JoomPlace</a>.-->
<!--			</th>-->
<!--		</tr>-->
<!--		<tr>-->
<!--			<td width="120">--><?php //echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_INSTALLED_VERSION') . ':'; ?><!--</td>-->
<!--			<td class="html5fb_control_panel_current_version">--><?php //echo $this->config->component_version; ?><!--</td>-->
<!--		</tr>-->
<!--		<tr>-->
<!--			<td>--><?php //echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_LATEST_VERSION') . ':'; ?><!--</td>-->
<!--			<td>-->
<!--				<div id="html5fbLatestVersion">-->
<!--					<button class="btn btn-small" onclick="onBtnCheckLatestVersionClick(this, event);">-->
<!--						<i class="icon-health"></i>-->
<!--						--><?php //echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_CHECK_NOW'); ?>
<!--					</button>-->
<!--				</div>-->
<!--			</td>-->
<!--		 </tr>-->
	<!--	 <tr>
			<td><?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_ABOUT') . ':'; ?></td>
			<td>
				<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_ABOUT_DESC'); ?>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_FORUM') . ':'; ?></td>
			<td>
				<a target="_blank" href="http://www.joomplace.com/forum/joomla-components/joomlahtml5fbazine.html"
					>http://www.joomplace.com/forum/joomla-components/joomlahtml5ne.html</a>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_CHANGELOG') . ':'; ?></td>
			<td>
				<div class="button2-left"><div class="blank">
					<button class="btn btn-small" onclick="onBtnShowChangelogClick(this, event);">
						<i class="icon-file"></i>
						<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_CHANGELOG_VIEW'); ?>
					</button>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<table cellpadding="5" class="html5fb_control_panel_news_table"> -->
					<!--<tr>
						<td section="">
							<img src="<?php echo COMPONENT_IMAGES_URL.'tick.png'; ?>"><?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_SAY_THANKS_TITLE'); ?>
						</td>
					</tr>
					<tr>
						<td class="html5fb_control_panel_thanks_cell">
							<div class="html5fb_control_panel_thanks">
								<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_SAY_THANKS_1'); ?>
								<a href="http://extensions.joomla.org/extensions/directory-a-documentation/portfolio/11307" target="_blank">http://extensions.joomla.org</a>
								<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_SAY_THANKS_2'); ?>
							</div>
							<div class="html5fb_control_panel_rate_us">
								<a href="http://extensions.joomla.org/extensions/directory-a-documentation/portfolio/11307" target="_blank">
									<img src="<?php echo COMPONENT_IMAGES_URL.'rate_us.png'; ?>" />
								</a>
							</div>
						</td>
					</tr>-->
				<!--	<tr>
						<td section="">
							<img src="<?php echo COMPONENT_IMAGES_URL.'tick.png'; ?>"><?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_NEWS_TITLE'); ?>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="html5fb_control_panel_news_cell" style="background-image: linear-gradient(to bottom, #FFFFFF, #EEEEEE);">
							<div id="html5fbLatestNews" class="html5fb_control_panel_news">
								<img src="<?php echo COMPONENT_IMAGES_URL.'ajax_loader_16x11.gif'; ?>" />
							</div>
						</td>
					</tr>
				</table> -->
			</td>
		</tr>
	</table>
	
	<div class="modal hide fade" id="changelogModal">
		<div class="modal-header">
			<button type="button" role="presentation" class="close" style="z-index: 2000" data-dismiss="modal">x</button>
			<h3><?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_LATEST_CHANGES');?></h3>
		</div>
		<div class="modal-body form-horizontal" id="body"></div>
		<div class="modal-footer">
			<button class="btn" id="closeBtn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_BUTTON_CLOSE'); ?></button>
			<button class="btn btn-primary" onclick="window.open('http://www.joomplace.com/members-area.html', '_blank'); jQuery('#changelogModal').modal('hide'); return false;"><?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_BUTTON_DOWNLOAD'); ?></button>
		</div>
	</div>
</div>