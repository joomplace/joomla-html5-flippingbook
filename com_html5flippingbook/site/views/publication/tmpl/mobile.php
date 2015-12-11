<?php
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

$uri = JUri::getInstance();
$doc = JFactory::getDocument();

if ( $this->item->opengraph_use )
{
	require_once(JPATH_COMPONENT_ADMINISTRATOR.'/libs/MethodsForXml.php');

	$pageLink = JRoute::_('index.php?option='.COMPONENT_OPTION.'&view=publication&id='.$this->item->c_id, false, $uri->isSSL());

	$opengraphTitle = ($this->item->opengraph_title != '' ? $this->item->opengraph_title : $this->item->c_title);
	$opengraphAuthor = ($this->item->opengraph_author != '' ? $this->item->opengraph_author : $this->item->c_author);
	$imageFileName = ($this->item->opengraph_image != '' ? $this->item->opengraph_image : $this->item->c_thumb);
	$imageFullFileName = COMPONENT_MEDIA_PATH.'/thumbs/'.$imageFileName;

	if ($imageFileName != '' && is_file($imageFullFileName))
	{
		$opengraphImage = COMPONENT_MEDIA_URL.'thumbs/'.$imageFileName;
	}
	else
	{
		$opengraphImage = '';
	}

	if ($this->item->opengraph_description != '')
	{
		$opengraphDesc = $this->item->opengraph_description;
	}
	else
	{
		$noTagsDescr = preg_replace('/<[^>]*>/', '', $this->item->c_pub_descr);
		$opengraphDesc = (strlen($noTagsDescr) <= 100 ? $noTagsDescr : substr($noTagsDescr, 0, 100).'...');
	}

	if ( !empty($this->config->social_facebook_og_app_id) )
	{
		$doc->addCustomTag('<meta property="fb:admins" content="'.$this->config->social_facebook_og_admin_id.'" />');
		$doc->addCustomTag('<meta property="fb:app_id" content="'.$this->config->social_facebook_og_app_id.'" />');
	}

	$doc->addCustomTag('<meta property="og:url" content="'.$pageLink.'" />');
	$doc->addCustomTag('<meta property="og:title" content="'.MethodsForXml::XmlEncode($opengraphTitle).'" />');
	$doc->addCustomTag('<meta property="og:image" content="'.$opengraphImage.'" />');
	$doc->addCustomTag('<meta property="og:description" content="'.MethodsForXml::XmlEncode($opengraphDesc).'" />');
	$doc->addCustomTag('<meta property="og:updated_time" content="'.MethodsForXml::XmlEncode($this->item->c_created_time).'" />');
	$doc->addCustomTag('<meta property="og:type" content="article" />');

	$doc->addCustomTag('<meta property="article:tag" content="HTML5FlippingBook" />');
	$doc->addCustomTag('<meta property="article:author" content="'.$opengraphAuthor.'" />');
	$doc->addCustomTag('<meta property="article:published_time" content="'.MethodsForXml::XmlEncode($this->item->c_created_time).'" />');

	$doc->addCustomTag('<meta property="twitter:title" content="'.MethodsForXml::XmlEncode($opengraphTitle).'" />');
	$doc->addCustomTag('<meta property="twitter:description" content="'.MethodsForXml::XmlEncode($opengraphDesc).'" />');
}

if ( !empty($this->item->custom_metatags) )
{
	foreach ( $this->item->custom_metatags as $custom_tag_name => $custom_tag_value )
	{
		$doc->addCustomTag('<meta property="'.$custom_tag_name.'" content="'.$custom_tag_value.'" />');
		$doc->addCustomTag('<meta name="'.$custom_tag_name.'" content="'.$custom_tag_value.'" />');
	}
}

JHtml::_('jquery.framework', false);

// add viewpoint tag for mobile
$doc->addCustomTag('<meta name="viewport" content="width=device-width, initial-scale=1" />');
$doc->addStyleSheet(COMPONENT_MOBILE_THEME_URL . 'def-mobile-theme.min.css');
$doc->addStyleSheet(COMPONENT_MOBILE_THEME_URL . 'jquery.mobile.icons.min.css');
$doc->addStyleSheet(COMPONENT_MOBILE_LIB_URL . 'jquery.mobile.structure-1.4.2.min.css');
$doc->addStyleSheet(COMPONENT_MOBILE_LIB_URL . 'jquery.minicolors.css');
$doc->addScript(COMPONENT_JS_URL .'jquery.cookie.min.js');
$doc->addScript(COMPONENT_MOBILE_LIB_URL .'jquery.mobile-1.4.2.min.js');
$doc->addScript(COMPONENT_MOBILE_LIB_URL .'jquery.minicolors.min.js');

if ($this->tmplIsComponent)
{
	$targetLink = 'index.php?option=com_html5flippingbook&view=html5flippingbook';
	$lang = JFactory::$document->getLanguage();
	$direction = JFactory::$document->getDirection();
	@ob_clean();
	?>

	<!DOCTYPE html>
	<!--[if lt IE 7 ]> <html lang="en" class="ie6"> <![endif]-->
	<!--[if IE 7 ]>    <html lang="en" class="ie7"> <![endif]-->
	<!--[if IE 8 ]>    <html lang="en" class="ie8"> <![endif]-->
	<!--[if IE 9 ]>    <html lang="en" class="ie9"> <![endif]-->
	<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<?php
		$hdata = $doc->getHeadData();
		foreach ($hdata['scripts'] as $url => $params)
			if ( strpos($url, 'jquery') == false)
				unset( $hdata['scripts'][$url]);

		if ( !empty($hdata['custom']) )
			foreach ($hdata['custom'] as $k => $value )
				if ( strpos($value, '<script') !== false )
					unset($hdata['custom'][$k]);

		$doc->setHeadData($hdata);

		echo JFactory::$document->loadRenderer("head")->fetchHead(JFactory::$document);
		?>
		<link rel="stylesheet" href="<?php echo COMPONENT_MOBILE_LIB_URL;?>mobile.css"/>
		<script type="text/javascript">
			var pubID = <?php echo $this->publicationId;?>;
			var realUrl = '<?php echo JUri::root();?>';
		</script>
		<script type="text/javascript" src="<?php echo COMPONENT_MOBILE_LIB_URL;?>mobile.min.js"></script>
	</head>

	<body>
		<div data-role="page" id="page-container" data-theme="a">
			<div data-role="header" id="page-header" data-theme="a" data-position="fixed" data-fullscreen="true" data-disable-page-zoom="false">
				<a class="ui-btn-left ui-btn-corner-all ui-btn ui-icon-home ui-btn-icon-notext ui-shadow" title=" <?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_BUTTON_HOME');?> " target="_self" href="<?php echo JRoute::_($targetLink, false, $uri->isSSL());?>" data-form="ui-icon" data-role="button" role="button"> <?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_BUTTON_HOME');?> </a>
				<h1 id="book-title"></h1>
				<div id="book-author"></div>
				<a class="ui-btn-right ui-btn-corner-all ui-btn ui-icon-gear ui-btn-icon-notext ui-shadow" id="book-settings" href="#settings" data-rel="dialog" data-transition="flip" title=" <?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_BUTTON_SETT');?> " data-form="ui-icon" data-role="button" role="button"> <?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_BUTTON_SETT');?> </a>
			</div>

			<div data-role="content" id="page-content" data-theme="a">
				<div id="navi" style="display: none;">
					<a class="ui-btn-left  ui-icon-carat-l ui-btn-icon-notext ui-shadow-icon ui-btn-corner-all" id="prev" title=" <?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_BUTTON_PREV');?> " data-form="ui-icon"> <?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_BUTTON_PREV');?> </a>
					<a class="ui-btn-right ui-icon-carat-r ui-btn-icon-notext ui-shadow-icon ui-btn-corner-all" id="next" title=" <?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_BUTTON_NEXT');?> " data-form="ui-icon"> <?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_BUTTON_NEXT');?> </a>
				</div>
				<div id="page"></div>
			</div>

			<div data-role="footer" id="page-footer" data-position="fixed" data-fullscreen="true" data-disable-page-zoom="false">
				<input type="range" name="page-slider" id="page-slider" value="1" min="1" max="100" data-highlight="true" data-mini="true">
			</div>
		</div>

		<div data-role="dialog" id="settings" data-corners="false">
			<div data-role="header">
				<h2><?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_PAGE_SETT_TITLE');?></h2>
			</div>
			<div data-role="content">
				<div class="ui-field-contain">
					<label for="navi-buttons"><?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_PAGE_SETT_VIEW_NAVI');?></label>
					<input data-theme="a" name="navi-buttons" type="checkbox" id="navi-buttons" data-role="flipswitch" data-mini="true">
				</div>
				<div class="ui-field-contain">
					<label for="night-mode"><?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_PAGE_SETT_NIGHT_VIEW');?></label>
					<input data-theme="a" name="night-mode" type="checkbox" id="night-mode" data-role="flipswitch" data-mini="true">
				</div>
				<div class="ui-field-contain">
					<label for="backgroundcolor"><?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_PAGE_SETT_BACGR_COLOR');?></label>
					<input type="hidden" id="backgroundcolor" name="backgroundcolor" value="#f9f9f9" />
				</div>
				<div class="ui-field-contain">
					<label for="fontcolor"><?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_PAGE_SETT_FONT_COLOR');?></label>
					<input type="hidden" id="fontcolor" name="fontcolor" value="#000" />
				</div>
				<div class="ui-field-contain">
					<label for="fontsize" style="float: none;"><?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_PAGE_SETT_FONT_SIZE');?></label>
					<input data-theme="a" type="range" name="fontsize" id="fontsize" value="12" min="8" max="32" data-highlight="true" data-mini="true">
				</div>
			</div>
		</div>
	</body>
</html>

<?php
	jexit();
}
