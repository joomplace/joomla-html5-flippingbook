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
		
		resultDiv.innerHTML = '<img src="<?php echo COMPONENT_IMAGES_URL.'ajax_loader_16x11.gif'; ?>" />';
		
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
		var actual_version = "<?php echo $this->config->component_version; ?>";
		var is_actual = true;

		version_arr = version.split('.');
		version_arr.pop();

		actual_version_arr = actual_version.split('.');
		actual_version_arr.pop();

		for (var i in version_arr) {
			if (parseInt(version_arr[i]) > parseInt(actual_version_arr[i])) {
				break;
			}
			else if (parseInt(version_arr[i]) < parseInt(actual_version_arr[i])) {
				is_actual = false;
				break;
			}
		}
		
		// Handling data.
		
		if (error == "" && status == 200)
		{
			if (is_actual)
			{
				resultDiv.innerHTML = '<font color="green">' + version + '</font>';
			}
			else
			{
				jQuery('#body').html('<h3>Version: ' + version + '</h3>' +
					changelog +
					'<br/><hr/>If you want to see full list of component changes, please follow this link: ' + '<a href="' + link + '" target="_blank">Component changelog</a>');
				jQuery('#changelogModal').modal('show');

				resultDiv.innerHTML = '<button class="btn btn-small" onclick="onBtnCheckLatestVersionClick(this, event);"><i class="icon-health"></i>Check now</button>';
			}
		}
		else
		{
			resultDiv.innerHTML = '<font color="red">' + '<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_CONNECTION_FAILED'); ?>: ' + error + (error == '' ? '' : ', ') +
				(status == -100 ? '<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_TIMEOUT'); ?>' : status) + '</font>';
		}
	}
	
	function onGetLatestVersionTimeout(request, syncObject)
	{
		var resultDiv = document.getElementById('html5fbLatestVersion');
		
		resultDiv.innerHTML = '<font color="red">' + '<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_CONNECTION_FAILED'); ?>: ' +
			'<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_TIMEOUT'); ?>' + '</font>';
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
		
		if (error == "" && status == 200)
		{
			resultDiv.innerHTML = content;
		}
		else
		{
			resultDiv.innerHTML = '<font color="red">' + '<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_CONNECTION_FAILED'); ?>: ' +
				'<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_TIMEOUT'); ?>' + '</font>';
		}
	}
	
	function onGetLatestNewsTimeout(request, syncObject)
	{
		var resultDiv = document.getElementById('html5fbLatestNews');
		
		resultDiv.innerHTML = '<font color="red">' + '<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_CONNECTION_FAILED'); ?>: ' +
			'<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_TIMEOUT'); ?>' + '</font>';
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

<div id="j-main-container" class="span6 form-horizontal html5fb_control_panel_container well" style="margin-right: 0px;">
	
	<table class="table">
		<tr>
			<th colspan="100%" class="html5fb_control_panel_title">
				<?php echo JText::_('COM_HTML5FLIPPINGBOOK'); ?>&nbsp;<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_COMPONENT_DESC') .
					" 3.x+. " . JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_DEVELOPED_BY'); ?> <a href="http://www.joomplace.com/" target="_blank">JoomPlace</a>.
			</th>
		</tr>
		<tr>
			<td width="120"><?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_INSTALLED_VERSION') . ':'; ?></td>
			<td class="html5fb_control_panel_current_version"><?php echo $this->config->component_version; ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_LATEST_VERSION') . ':'; ?></td>
			<td>
				<div id="html5fbLatestVersion">
					<button class="btn btn-small" onclick="onBtnCheckLatestVersionClick(this, event);">
						<i class="icon-health"></i>
						<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_CONTROL_PANEL_CHECK_NOW'); ?>
					</button>
				</div>
			</td>
		 </tr>
		 <tr>
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
				<table cellpadding="5" class="html5fb_control_panel_news_table">
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
					<tr>
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
				</table>
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